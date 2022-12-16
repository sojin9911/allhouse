<?PHP
	include "./inc.php";


	// - 모드별 처리 ---
	switch( $_mode ){

		// 설정저장
		case "config":
			$que = "
				update smart_setup set
					s_online_notice_use = '". $_online_notice_use ."'
					,s_online_notice_auto = '". $_online_notice_auto ."'
					,s_online_notice_privacy = '". $_online_notice_privacy ."'
					,s_online_notice_bank = '". $_online_notice_bank ."'
					,s_online_notice_view = '". $_online_notice_view ."'
			";
			_MQ_noreturn($que);

			error_loc_msg('_online_notice.list.php' . ($_PVSC ? '?'.enc('d', $_PVSC) : null) , '설정이 저장되었습니다.');
			break;



		case "add":
			// -- 사전체크 -----
			$_date = nullchk($_date , '입금일자를 입력해주시기 바랍니다.');
			$_name = nullchk($_name , '입금자를 입력해주시기 바랍니다.');
			if($siteInfo['s_online_notice_bank']=='Y'){
				$_bank = nullchk($_bank , '입금은행을 등록해주시기 바랍니다.');
			}
			$_price = nullchk(rm_str($_price) , '입금액을 입력해주시기 바랍니다.');
			if($_price<1) error_msg('입금액을 입력해주시기 바랍니다.');
			// -- 사전체크 -----

			$que = "
				insert into smart_online_notice set 
					on_view = '". $_view ."'
					,on_name = '". $_name ."'
					,on_price = '". $_price ."'
					,on_bank = '". $_bank ."'
					,on_date = '". $_date ."'
					,on_rdate = now()
			";
			_MQ_noreturn($que);
			error_loc_msg('_online_notice.list.php' . ($_PVSC ? '?'.enc('d', $_PVSC) : null) , '정상적으로 추가되었습니다.');

			exit;
			break;



		case "delete":

			if($_uid){
				_MQ_noreturn(" delete from smart_online_notice where on_uid = '". $_uid ."' ");
			}else{
				error_msg('잘못된 접근입니다.');
			}
			

			error_loc_msg('_online_notice.list.php' . ($_PVSC ? '?'.enc('d', $_PVSC) : null), '정상적으로 삭제되었습니다.');
			break;



		// 일괄삭제
		case "mass_delete":

			if(sizeof($chk_uid)>0){
				_MQ_noreturn(" delete from smart_online_notice where on_uid in ('".implode("','" , array_values($chk_uid))."') ");
			}else{
				error_msg('잘못된 접근입니다.');
			}

			error_loc_msg('_online_notice.list.php' . ($_PVSC ? '?'.enc('d', $_PVSC) : null), '정상적으로 삭제되었습니다.');
			break;


		// 노출설정 변경
		case "ajax_modify_view":
			if($_uid && $_val){
				$r = _MQ_noreturn(" update smart_online_notice set on_view = '". ($_val=='Y'?'Y':'N') ."' where on_uid = '". $_uid ."' ");
				if($r==1){
					echo 'success';
				}else{
					echo 'fail';
				}
			}else{
				echo 'fail';
			}
			break;


		// 엑셀다운로드 
		case "select_excel":
		case "search_excel":

			// 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19
			@ini_set("precision", "20");
			@ini_set('memory_limit', '1000M'); // 메모리 가용폭을 1기가 까지 올림

			$fileName = 'online-notice';
			$toDay = date('Ymd', time());

			header( "Content-type: application/vnd.ms-excel; charset=utf-8" );
			header( "Content-Disposition: attachment; filename=$fileName-$toDay.xls" );
			print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");

			if($_mode == 'select_excel'){
				$que = " select * from smart_online_notice where on_uid in ('".implode("','" , array_values($chk_uid))."') ORDER BY on_date desc , on_uid desc ";
			}else{
				$s_query = enc('d', $_search);
				$que = " select * from smart_online_notice {$s_query} ORDER BY on_date desc , on_uid desc  ";
			}

			$res = _MQ_assoc($que);
			?>
			<table class="table_list">
				<thead>
					<tr>
						<th scope="col">NO</th>
						<th scope="col">입금일자</th>
						<th scope="col">입금자</th>
						<th scope="col">입금은행</th>
						<th scope="col">입금액</th>
						<th scope="col">노출설정</th>
					</tr>
				</thead> 
				<tbody>
				<?php

				foreach($res as $k=>$v) {
					$_num = $k+1 ;
				?>
				<tr>
					<td><?php echo $_num; ?></td>
					<td><?php echo date('Y-m-d', strtotime($v['on_date'])); ?></td>
					<td><?php echo $v['on_name']; ?></td>
					<td><?php echo $v['on_bank']; ?></td>
					<td><?php echo $v['on_price']; ?></td>
					<td>
						<?php echo ($v['on_view']=='Y' ? '노출' : '숨김'); ?>
					</td>
				</tr>
				<?php
			}
			?>
				</tbody>
			</table>
			<?php

			break;

		// 무통장 입금확인 자동입력
		case "auto_insert":

			// -- 은행명 추출 ---
			$arr_bank = array();
			$ex = _MQ_assoc("select * from smart_bank_set order by bs_idx asc");
			foreach( $ex as $k=>$v ){ 
				$arr_bank[rm_str($v['bs_bank_num'])] = '['. $v['bs_bank_name'] .'] ' . $v['bs_bank_num'] . ' ' . $v['bs_user_name'];
			}

			$que = " 
				select * 
				from smart_orderbank_log as ob 
				left join smart_online_notice as oln on (ob.ob_uid = oln.on_obuid)
				where 1 
					and ob.ob_deleted = 'N' 
					and ob.ob_status ='N' 
					and oln.on_uid is null
				ORDER BY ob.ob_paydate asc 
			";
			$res = _MQ_assoc($que);
		
			$totalCnt = sizeof($res);
			if($totalCnt>0){
				foreach($res as $k=>$v){
					$_query = "
						insert into smart_online_notice set 
							on_view = 'Y'
							,on_name = '". $v['ob_ordername'] ."'
							,on_price = '". $v['ob_orderprice'] ."'
							,on_bank = '".  ($arr_bank[$r["ob_account"]] ? $arr_bank[$r["ob_account"]] : $v['ob_account']) ."'
							,on_date = '". date('Y-m-d' , strtotime($v['ob_paydate'])) ."'
							,on_obuid = '". $v['ob_uid'] ."'
							,on_rdate = now()
					";
					_MQ_noreturn($_query);
				}
				error_loc_msg('_online_notice.list.php' . ($_PVSC ? '?'.enc('d', $_PVSC) : null), '총 '. number_format($totalCnt) .'건의 입금내역이 추가되었습니다.');
			}else{
				error_loc_msg('_online_notice.list.php' . ($_PVSC ? '?'.enc('d', $_PVSC) : null), '추가할 내역이 없습니다.');
			}


			break;

	}
	// - 모드별 처리 ---

	exit;
?>