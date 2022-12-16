<?PHP

	// 080 수신거부 연동

	// 로컬 테스트 URL : http://127.0.0.1/oldjob/normal/test.php
	//	Request Method		- HTTP POST
	//	Parameter	
	//		refusal_num			- 080 수신거부 번호	예) 0801231234
	//		refusal_time			- 수신거부 요청 시간	예) 20140411120055
	//		hp						- 수신거부 요청 전화번호	예) 01012345678

	include_once("inc.php");
	$row_setup = $siteInfo;

	// 저장된 수신거부 번호와  080 수신거부 번호가 맞는지 확인
	if(rm_str($row_setup['s_deny_tel']) == rm_str($refusal_num) ) {

		// 수신거부를 할 회원 체크
		$mr_cnt = _MQ("select count(*) as cnt from smart_individual where REPLACE(in_tel2 , '-' , '') = '". rm_str($hp) ."' ");

		if( $mr_cnt['cnt'] == 1 ) {
			_MQ_noreturn(" update smart_individual set in_smssend = 'N' , m_opt_date = now() where REPLACE(in_tel2 , '-' , '')  = '". rm_str($hp) ."' ");
			$_trigger = "OK";// sms 수신거부처리

			// 080수신거부시 광고성 정보 수신거부 처리 - changeAlert 
			// 자체로 메일발송함.
			// $hp 변수를 이용하여 회원정보 추출
			// $_trigger = "OK" 여야 진행가능
			$_change_alert_file_name = $_SERVER["DOCUMENT_ROOT"] . "/addons/changeAlert/changeAlert.mail.contents.080deny.php";
			if(@file_exists($_change_alert_file_name)) { include_once($_change_alert_file_name); }

		}
		else if( $mr_cnt['cnt'] > 1 ) {
			$_trigger = "MULTI";// 다수검색
		}
		else{
			$_trigger = "NO";// 검색안됨
		}

	}	
	else {
		$_trigger = "FALSE";// 저장된 수신거부 번호와  080 수신거부 번호가 맞지 않을 경우
	}


	// 수신거부 사용시 로그기록함.
	if( $row_setup['s_deny_use'] == "Y" ) {
		$que = "
			insert smart_member_080_deny set 
				md_refusal_num = '". $refusal_num ."',
				md_refusal_time = '". $refusal_time ."',
				md_hp = '". $hp ."',
				md_status = '". $_trigger ."',
				md_rdate = now()
		";
		_MQ_noreturn($que);
		//echo $que;
	}

	exit;


?>