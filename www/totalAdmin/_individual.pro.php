<?php // -- LCY -- 회원관리 AJAX 처리
	@ini_set("precision", "20");
	@ini_set('memory_limit', '1000M'); // 메모리 가용폭을 1기가 까지 올림
	include_once('inc.php');

	if( in_array($_mode, array('add','modify') ) == true){

		// === 본인인증 체크 설정 추가 통합 kms 2019-06-21 ====
		$_tel2=tel_format($_tel2);
		// === 본인인증 체크 설정 추가 통합 kms 2019-06-21 ====

		// -- 회원 등록/수정 공통 처리
		$sque = "
			in_name									= '". $_name ."'
			,in_zip1								= '". $_zip1 ."'
			,in_zip2								= '". $_zip2 ."'
			,in_address1						= '". $_address1 ."'
			,in_address2						= '". $_address2 ."'
			,in_address_doro				= '". $_address_doro ."'
			,in_email								='" . $_email . "'
			,in_emailsend						= '". $_emailsend ."'
			,in_smssend							= '". $_smssend ."'
			,in_tel									= '". $_tel ."'
			,in_tel2								= '". $_tel2 ."'
			,in_zonecode						= '". $_zonecode ."'
			,in_sex									= '". $_sex ."'
			,in_birth								= '". $_birth ."'
			,in_cancel_bank					= '".$_cancel_bank."'
			,in_cancel_bank_account	= '".$_cancel_bank_account."'
			,in_cancel_bank_name		= '".$_cancel_bank_name."'
			,in_auth								= '".$_auth."'
			,in_mgsuid							= '".$_mgsuid."'

		";

		// echo $sque;
		// exit;

		// -- 패스워드 입력이 있을경우 처리
		if( $_pw && $_rpw && ( $_pw == $_rpw )){
			$sque .= " , in_pw = password('". $_pw ."')";
		}
	}


	switch ($_mode) {

		// -- 회원등록
		case "add":
			// -- 이메일 중복 체크 ---
			$r = _MQ("select count(*) as cnt from smart_individual where in_id='${_id}' ");
			if( $r[cnt] > 0 ) {
				error_msg("아이디가 중복 됩니다.");
			}

			$que = " insert smart_individual set ".$sque." , in_id='".$_id."' , in_join_ua = 'PC',  in_rdate = now() ";
			_MQ_noreturn($que);
			error_loc("_individual.form.php?_mode=modify&_id=".$_id."&_PVSC=".$_PVSC."");
		break;


		// --  회원수정
		case "modify":

		$que = " update smart_individual set ".$sque." where in_id='".$_id."' ";
		_MQ_noreturn($que);
		error_loc("_individual.list.php?".enc('d' , $_PVSC ));

		break;

		// -- 회원삭제
		case "delete":
			$shopAdminInfo = shopAdminInfo();
			if($shopAdminInfo['in_id'] == $_id) error_msg("쇼핑몰 관리자는 삭제할수 없습니다.");
			_MQ_noreturn("delete from smart_individual where in_id='{$_id}' ");
			error_loc("_individual.list.php?".enc('d' , $_PVSC ));
		break;

		// -- 엑셀다운로드
		case "getExcelDownload":

			// -- 회원 키
			//$arrKey = array('in_id'=>'아이디', 'in_name'=>'이름', 'in_sex'=>'성별', 'in_birth'=>'생년월일','in_email'=>'이메일','in_emailsend'=>'이메일수신여부' , 'in_tel'=>'전화번호','in_tel2'=>'휴대폰', 'in_smssend'=>'휴대폰수신여부', 'in_zonecode'=>'새우편번호','in_zip1'=>'구우편번호1','in_zip2'=>'구우편번호2', 'in_address1'=>'구주소', 'in_address_doro'=>'도로명주소',  'in_address2'=>'상세주소', 'in_rdate'=>'가입일');
			// ----- SSJ : 관리자 지번주소 내부패치 : 2020-04-27 -----
			$arrKey = array('in_id'=>'아이디', 'in_name'=>'이름', 'in_sex'=>'성별', 'in_birth'=>'생년월일','in_email'=>'이메일','in_emailsend'=>'이메일수신여부' , 'in_tel'=>'전화번호','in_tel2'=>'휴대폰', 'in_smssend'=>'휴대폰수신여부', 'in_zonecode'=>'우편번호', 'in_address_doro'=>'도로명주소',  'in_address2'=>'상세주소', 'in_address1'=>'지번주소', 'in_rdate'=>'가입일');
			// ----- SSJ : 관리자 지번주소 내부패치 : 2020-04-27 -----

			if( $ctrlMode  == 'select'){
				if( count($arrID) < 1 ){ error_msg("회원을 1명이상 선택해 주세요."); }
				$sque = " from smart_individual where 1 and in_sleep_type = 'N' AND in_out = 'N' and find_in_set(in_id, '".implode(",",$arrID)."' ) > 0   ";
			}else if( $ctrlMode == 'search'){
				$sque = enc('d', $searchQue);
			}else{
				error_msg('실행이 올바르지 않습니다.');
			}

			// 	echo "<table><tr><td>".implode("</td><td>",$arrNkey)."</tr></table>";
			if($orderby == '' ){ $orderby = 'order by in_rdate desc'; }
			$res = _MQ_assoc("select ".implode(",",array_keys($arrKey))."   ".$sque. '  '.$orderby);
			if( count($res) < 1){ error_msg('회원검색에 실패하였습니다.'); }
			$toDay = date('YmdHis');
			$varExcelStyle['th'] = 'background:#e6e9eb;height:48px;';
			## Exel 파일로 변환 #############################################
			header( "Content-type: application/vnd.ms-excel; charset=utf-8" );
			header("Content-Disposition: attachment; filename=회원리스트_$toDay.xls");
			print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");

			// -- SQL 에서 처리못할 부분이 있을 수 있으니, 될 수 있으면 데이터 뽑아낼떄는 FOREACH 에서 ..
			$arrData = '';
			foreach($res as $k=>$v){
				$arrData[] = "<tr>";
				foreach($v as $key=>$val){
					if( $key == 'in_tel' || $key == 'in_tel2' ){  $val = tel_format($val); }
					if( $key == 'in_rdate'){ $val = date('Y-m-d',strtotime($val)); }
					if( trim($val) == '') { $val = '-'; }
					$arrData[] = "<td>".$val."</td>";
				}
				$arrData[] ="</tr>";
			}


			echo '
			<table border="1">
				<thead>
					<th style="'.$varExcelStyle['th'].'">'.implode("</th><th style='".$varExcelStyle['th']."'>",array_values($arrKey)).'</th></tr>
				</thead>
				<tbody>
					'.implode("",$arrData).'
				</tbody>
			</table>
			';
		break;

		// -- 선택회원 승인/미승인 처리
		case "selectAuth":
			if(count($arrID) < 1){ error_msg('회원을 한명 이상 선택해 주세요.'); }
			if( in_array($ctrlMode,array('Y','N')) == false){ error_msg('선택회원에 대한 처리가 올바르지 않습니다.'); }
			_MQ_noreturn("update smart_individual set in_auth = '".$ctrlMode."' where find_in_set(in_id, '".implode(",",$arrID)."' ) > 0 and in_auth != '".$ctrlMode."' ");
			error_loc("_individual.list.php?".enc('d' , $_PVSC ));

		break;

	}

?>