<?php

	// ---------- JJC : 부분취소 개선 : 2021-02-10  ----------


	if(in_array($_POST['_mode'], array('get_excel','get_search_excel'))){
		@ini_set("precision", "20");
		@ini_set('memory_limit', '1000M');
	}
	include_once('inc.php');


	switch($_mode) {




		// ---------- JJC : 부분취소 개선 : 2021-02-10  ----------
		// --- 부분취소 요청 삭제 - 2016-07-01 추가 ---
		case "req_cancel":

			$data = "mode=restore&op_uid=" . $op_uid;
			$res = json_decode(CurlPostExec($system['url'] . "/program/mypage.order.view.ajax.php" , $data));
			if($res->result != "OK") {error_msg($res->result_text);}
			else {error_loc_msg("_cancel.list.php?_PVSC=${_PVSC}" , "부분취소 요청을 삭제하였습니다.");}			

			break;
		// --- 부분취소 요청 삭제 - 2016-07-01 추가 --- 
		// ---------- JJC : 부분취소 개선 : 2021-02-10  ----------





		// ---------- JJC : 부분취소 개선 : 2021-02-10  ----------
		// 부분취소 - 실행
		case "cancel":

			$r = _MQ("
				SELECT 
					*, ( if(op_free_delivery_event_use = 'Y',0,op_delivery_price) ) AS op_delivery_price  
				FROM smart_order_product AS op
				LEFT JOIN smart_order AS o on (o.o_ordernum = op.op_oordernum)
				WHERE 
					o_ordernum='" . $ordernum . "' AND 
					op_uid = '".$op_uid."'
			");
			// SSJ : 주문검색 실패 시 오류 처리 : 2021-02-18
			if($r['o_ordernum'] == ''){ error_loc_msg("_cancel.list.php?_PVSC=${_PVSC}" , "주문 검색에 실패 하였습니다."); }
			if($r['op_cancel']=='Y') { error_loc_msg("_cancel.list.php?_PVSC=${_PVSC}" , "이미 취소된 주문입니다."); }


			$_ordernum = $ordernum; 
			$_uid = $op_uid; 
			$_applytype = "admin";
			unset($__trigger);


			// 적립금 환불일 경우 환불금액을 적립금으로 추가함.
			$r['op_usepoint'] += ($r['op_cancel_type'] == "point" ? $r['op_cancel_price'] : 0);

			// 적립금 환불 있을 경우 처리
			if( $r['op_usepoint'] > 0 ) {
				shop_pointlog_insert( $r[o_mid] , "주문취소에 따른 사용 적립금반환 (주문번호 : ".$_ordernum.")" , $r['op_usepoint'] , "N" , 0);
				$_trigger = 'Y';
				if($_trigger=='Y') { $__trigger++; }
			}

			// PG 환불일 경우 환불금액 적용
			$_total_amount = ($r['op_cancel_type'] == "pg" ? $r['op_cancel_price'] : 0);
			include(OD_PROGRAM_ROOT . "/pg.cancle_part.php");
			if($_trigger=='Y') { $__trigger++; }


			// pg.cancle_part.php에서 발송완료 함.
			//if(sizeof($arr_send) > 0 ){ onedaynet_sms_multisend($arr_send); }

			if($__trigger > 0){ error_loc_msg("_cancel.list.php?_PVSC=${_PVSC}" , "주문을 취소하였습니다."); }
			else { error_loc_msg("_cancel.list.php?_PVSC=${_PVSC}" , "결제 취소요청이 실패하였습니다."); }

		break;




		case "mass":
			unset($__trigger);
			foreach($OpUid as $k=>$v) {

				$r = _MQ("
					SELECT 
						*, ( if(op_free_delivery_event_use = 'Y',0,op_delivery_price) ) AS op_delivery_price  
					FROM smart_order_product AS op
					LEFT JOIN smart_order AS o on (o.o_ordernum = op.op_oordernum)
					WHERE 
						op_uid = '".$v."'
				");
				$ordernum = $r['op_oordernum']; 
				$op_uid = $v;

				if($r['op_cancel']=='Y') { continue; /*error_loc_msg("_cancel.list.php?_PVSC=${_PVSC}" , "이미 취소된 주문입니다.");*/ }


				$_ordernum = $ordernum; 
				$_uid = $op_uid; 
				$_applytype = "admin";
				unset($__trigger);


				// 적립금 환불일 경우 환불금액을 적립금으로 추가함.
				$r['op_usepoint'] += ($r['op_cancel_type'] == "point" ? $r['op_cancel_price'] : 0);

				// 적립금 환불 있을 경우 처리
				if( $r['op_usepoint'] > 0 ) {
					shop_pointlog_insert( $r[o_mid] , "주문취소에 따른 사용 적립금반환 (주문번호 : ".$_ordernum.")" , $r['op_usepoint'] , "N" , 0);
					$_trigger = 'Y';
					if($_trigger=='Y') { $__trigger++; }
				}

				// PG 환불일 경우 환불금액 적용
				$_total_amount = ($r['op_cancel_type'] == "pg" ? $r['op_cancel_price'] : 0);
				include(OD_PROGRAM_ROOT . "/pg.cancle_part.php");
				if($_trigger=='Y') { $__trigger++; }

			}

			if($__trigger > 0){ error_loc_msg("_cancel.list.php?_PVSC=${_PVSC}" , "주문을 취소하였습니다."); }
			else { error_loc_msg("_cancel.list.php?_PVSC=${_PVSC}" , "결제 취소요청이 실패하였습니다."); }

		break;
		// ---------- JJC : 부분취소 개선 : 2021-02-10  ----------

		case "modify":
			// 직접 환불일때만 체크 kms 2019-05-22
			if ( $cancel_type == "pg" ) {
				$cancel_bank = nullchk($cancel_bank , "환불 은행을 선택하시기 바랍니다.");
				$cancel_bank_account = nullchk($cancel_bank_account , "환불 계좌번호를 입력하시기 바랍니다.");
				$cancel_bank_name = nullchk($cancel_bank_name , "환불 예금주명을 입력하시기 바랍니다.");
			}
			_MQ_noreturn("
				update smart_order_product set
					op_cancel_bank = '".$cancel_bank."',
					op_cancel_bank_account = '".$cancel_bank_account."',
					op_cancel_bank_name = '".$cancel_bank_name."',
					op_cancel_msg = '".$cancel_msg."'
				where op_oordernum = '".$ordernum."' and op_uid = '".$op_uid."'
			");

			error_frame_loc_msg("_cancel.form.php?_mode=modify&_ordernum=".$ordernum."&uid=".$op_uid."&_PVSC=".$_PVSC , "정보를 수정 하였습니다.");
		break;

		// - 엑셀다운로드 ---
	    case "get_excel": // 선택
	    case "get_search_excel": // 검색

			// 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19


			if(count($OpUid) <= 0 && $_mode != 'get_search_excel') error_msg('항목을 선택하시기 바랍니다.');
			$toDay = date('YmdHis');
			$fileName = iconv('utf-8', 'euc-kr', '부분취소내역');


			# 모드별 쿼리 조건
			if($_mode == 'get_excel') $s_query = " and op_uid in ('".implode("', '", $OpUid)."') ";
			else $s_query = enc('d', $_search_que);
			if(!$st) $st = 'op.op_cancel_rdate';
			if(!$so) $so = 'desc';
			$res = _MQ_assoc("
				select
					* ,
					o.o_otel as ordertel,
					o.o_ohp as orderhtel
				from
					smart_order_product as op left join
					smart_order as o on (o.o_ordernum = op.op_oordernum)
				where (1)
					{$s_query}
				order by {$st} {$so}
			");

			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=$fileName-$toDay.xls");


			# 테이블 스타일
			$THStyle = ' style="color: #333;padding: 10px; border-bottom: 1px solid #c6c6c6; background: #d6d6d6; font-size: 11px;"';
			$TDStyle = ' style="padding: 5px; border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; vertical-align: middle; text-align: center; mso-number-format:\'\@\';"';
			$TDStyle2 = ' style="padding: 5px; border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; vertical-align: middle; text-align: center;"';
			$br = '<br style="mso-data-placement:same-cell;">';
?>
<table>
	<thead>
		<tr>
			<th<?php echo $THStyle; ?>>주문번호</th>
			<th<?php echo $THStyle; ?>>구매상품정보</th>
			<th<?php echo $THStyle; ?>>주문자</th>
			<th<?php echo $THStyle; ?>>E-mail</th>
			<th<?php echo $THStyle; ?>>핸드폰번호</th>
			<th<?php echo $THStyle; ?>>주문일시</th>
			<th<?php echo $THStyle; ?>>취소요청일시</th>
			<th<?php echo $THStyle; ?>>취소처리일시</th>
			<th<?php echo $THStyle; ?>>취소상태</th>
			<th<?php echo $THStyle; ?>>환불금액</th>
			<th<?php echo $THStyle; ?>>고객 요청내용</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($res as $k=>$v) {
			$tmp_content = '';
			$itemName = $v['op_pname'];
			if($v['op_option1']) {   // 해당상품에 대한 옵션내역이 있으면
				$itemName .= " (" . trim($v['op_option1']." ".$v['op_option2']." ".$v['op_option3']).")";
			}
			$itemName .= " " . $v['op_cnt']."개";
			$tmp_content .= $itemName;

			$cancel_status = $v[op_cancel]=='Y' ? '취소완료' : '취소요청중';
			$cancel_total = ( $v[op_price] * $v[op_cnt] ) + $v[op_delivery_price] + $v[op_add_delivery_price] - $v['op_cancel_discount_price'] ;// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC
			$cancel_bank = $ksnet_bank[$v[op_cancel_bank]];
			$cancel_bank_account = $v[op_cancel_bank_account];
			$cancel_bank_name = $v[op_cancel_bank_name];
			$cancel_msg = str_replace(array('<br>', '<br/>', '<br />'), $br, $v['op_cancel_msg']);
			$cancel_rdate = date('Y-m-d H:i:s',strtotime($v[op_cancel_rdate]));
			$cancel_cdate = ( rm_str($v[op_cancel_cdate])>0 ? date('Y-m-d H:i:s',strtotime($v[op_cancel_cdate])) : "");
		?>
			<tr>
				<td<?php echo $TDStyle; ?>><?php echo $v['o_ordernum']; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $tmp_content; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $v['o_oname']; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $v['o_oemail']; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $v['o_ohp']; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $v['o_rdate']; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $cancel_rdate; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $cancel_cdate; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $cancel_status; ?></td>
				<td<?php echo $TDStyle2; ?>><?php echo $cancel_total; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $cancel_msg; ?></td>
			</tr>
		<?php } ?>
	</tbody>
</table>
<?php

		break;
	}


	// ---------- JJC : 부분취소 개선 : 2021-02-10  ----------