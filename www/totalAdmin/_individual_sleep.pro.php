<?php // -- LCY -- 회원관리 AJAX 처리
	@ini_set("precision", "20");
	@ini_set('memory_limit', '1000M'); // 메모리 가용폭을 1기가 까지 올림
	include_once('inc.php');

	switch ($_mode) {

		// -- 선택회원,검색회원 휴면처리 
		case "getSleepReturn":
			// -- 회원 키
			$arrKey = array('in_id'=>'아이디');

			if($ctrlMode == 'select'){
				$ctrlModeName = '선택';
				if( count($arrID) < 1 ){ error_msg("회원을 1명이상 선택해 주세요."); }
				$sque = " from smart_individual_sleep where 1 and in_sleep_type = 'Y' AND in_out = 'N' and find_in_set(in_id, '".implode(",",$arrID)."' ) > 0   ";
			}else if( $ctrlMode == 'search'){
				$sque = enc('d', $searchQue);
			}else{
				$ctrlModeName = '검색';
				error_msg('실행이 올바르지 않습니다.');
			}

			if( $orderby == ''){ $orderby = ' order by ins_rdate desc '; }

			$res = _MQ_assoc("select ".implode(",",array_keys($arrKey))."   ".$sque. ' '.$orderby);
			if( count($res) < 1){ error_msg('회원검색에 실패하였습니다.'); }

			$successCnt = 0; // 성공카운트
			foreach($res as $k=>$v){
				$_id = $v['in_id'];
				$result =  member_sleep_return($_id);
				if($result == 'Y')  $successCnt ++; // 성공기록 
			}

			if( $successCnt < 1){
				error_loc_msg("_individual_sleep.list.php?".enc('d' , $_PVSC ),$ctrlModeName.' 회원 휴면해제에 실패하였습니다.');
			}else{
				error_loc_msg("_individual_sleep.list.php?".enc('d' , $_PVSC ),$ctrlModeName.' 회원이 휴면해제 되었습니다.');
			}

		break;

		// -- 선택/검색 회원 휴면메일 발송
		case "getSleepReturnMail":
			// -- 회원 키
			$arrKey = array('*'=>'전체');

			if($ctrlMode == 'select'){
				$ctrlModeName = '선택';
				if( count($arrID) < 1 ){ error_msg("회원을 1명이상 선택해 주세요."); }
				$sque = " from smart_individual_sleep where 1 and in_sleep_type = 'Y' AND in_out = 'N' and find_in_set(in_id, '".implode(",",$arrID)."' ) > 0   ";
			}else if( $ctrlMode == 'search'){
				$sque = enc('d', $searchQue);
			}else{
				$ctrlModeName = '검색';
				error_msg('실행이 올바르지 않습니다.');
			}

			if( $orderby == ''){ $orderby = ' order by ins_rdate desc '; }
			$res = _MQ_assoc("select ".implode(",",array_keys($arrKey))."   ".$sque. '  '.$orderby);
			if( count($res) < 1){ error_msg('회원검색에 실패하였습니다.'); }

			$successCnt = 0; // 성공카운트
			$arrSuccessId = array();
			foreach($res as $k=>$v){
				$_id = $v['in_id'];
				$_name = $v['in_name'];
				// -- 메일링 작업 완료 시 메일 발송 기능 추가 --
				$_title = "[".$siteInfo[s_adshop]."] 휴면계정 인증을 위한 메일을 발송해드립니다.";
				include(OD_MAIL_ROOT."/member.sleep_backup.mail.php"); // 메일 내용 불러오기 ($mailling_content)
				$_content = get_mail_content($mailling_content);
				mailer( $v['in_email'], $_title, $_content);


				$successCnt ++;
				$arrSuccessId[] = $_id;
				_MQ_noreturn("update smart_individual_sleep set ins_mailing = 'Y' where in_id = '".$_id."'  ");
			}

			if( $successCnt < 1){
				error_loc_msg("_individual_sleep.list.php?".enc('d' , $_PVSC ),$ctrlModeName.' 회원휴면메일 발송에 실패하였습니다.');
			}else{

				error_loc_msg("_individual_sleep.list.php?".enc('d' , $_PVSC ),$ctrlModeName.' 회원에게 휴면메일이 발송되었습니다.');
			}

		break;
		
		// -- 엑셀다운로드
		case "getExcelDownload":

			// -- 회원 키
			$arrKey = array('ins_rdate'=>'휴면전환일','ins_mailing'=>'휴면메일발송여부','in_id'=>'아이디', 'in_name'=>'이름', 'in_sex'=>'성별', 'in_birth'=>'생년월일','in_email'=>'이메일','in_emailsend'=>'이메일수신여부' , 'in_tel'=>'전화번호','in_tel2'=>'휴대폰', 'in_smssend'=>'휴대폰수신여부', 'in_zonecode'=>'새우편번호','in_zip1'=>'구우편번호1','in_zip2'=>'구우편번호2', 'in_address1'=>'구주소', 'in_address_doro'=>'도로명주소',  'in_address2'=>'상세주소', 'in_rdate'=>'가입일');

			if( $ctrlMode  == 'select'){
				if( count($arrID) < 1 ){ error_msg("회원을 1명이상 선택해 주세요."); }
				$sque = " from smart_individual_sleep where 1 and in_sleep_type = 'Y' AND in_out = 'N' and find_in_set(in_id, '".implode(",",$arrID)."' ) > 0   ";
			}else if( $ctrlMode == 'search'){
				$sque = enc('d', $searchQue);
			}else{
				error_msg('실행이 올바르지 않습니다.');
			}

			if( $orderby == ''){ $orderby = ' order by ins_rdate desc '; }
			$res = _MQ_assoc("select ".implode(",",array_keys($arrKey))."   ".$sque. '  '.$orderby);
			if( count($res) < 1){ error_msg('회원검색에 실패하였습니다.'); }
			$toDay = date('YmdHis');
			$varExcelStyle['th'] = 'background:#e6e9eb;height:48px;';
			## Exel 파일로 변환 #############################################			
			header( "Content-type: application/vnd.ms-excel; charset=utf-8" );
			header("Content-Disposition: attachment; filename=휴면회원리스트_$toDay.xls");			
			print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");			
			
			// -- SQL 에서 처리못할 부분이 있을 수 있으니, 될 수 있으면 데이터 뽑아낼떄는 FOREACH 에서 ..
			$arrData = '';
			foreach($res as $k=>$v){
				$arrData[] = "<tr>";
				foreach($v as $key=>$val){ 
					if( $key == 'in_tel' || $val == 'in_tel2' ){  $val = tel_format($val); }
					if( $key == 'in_rdate'){ $val = date('Y-m-d',strtotime($val)); }

					if( $key == 'ins_rdate'){ $val = date('Y-m-d',strtotime($val)); } // 휴면전환일
					if( $key == 'ins_mailing'){ $val = $val == 'Y' ? '발송':'미발송' ; } // 휴면메일발송여부

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

	}

?>