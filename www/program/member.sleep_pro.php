<?php
# ------------------------------> DEV 변경 하세요. <---------------------- :: 내부 이미지등...
# 로그인 & 로그아웃 & 팝업닫기
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


# 프로세스처리
if( !$_mode ) error_msg("잘못된 접근입니다.");
switch($_mode){

	// ---- 메일을 통한 휴면 회원 인증 처리 ----
	case "auth":

		//금일§아이디§이메일
		$_app_auth = onedaynet_decode( $auth );
		$ex = explode("§" , $_app_auth);
		$_id = $ex[1];
		$email = $ex[2];

		if( date("Y-m-d") <> $ex[0] ) { error_loc_msg("/" , "재 인증 받으시기 바랍니다."); }

		$r = _MQ("SELECT * FROM smart_individual_sleep where in_id='".addslashes(trim($_id))."' and in_email = '".addslashes(trim($email))."'  ");

		if( sizeof($r) == 0 ) {error_loc_msg("/" , "일치하는 회원정보가 없습니다.\\n\\n다시 한번 확인해 주세요.");}

		member_sleep_return( $_id );

		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_loc_msg("/?pn=member.login.form" , "인증을 완료하였습니다.\\n\\n새로 로그인 하시면 정상적으로 서비스를 이용하실 수 있습니다.");
	break;
	// ---- 메일을 통한 휴면 회원 인증 처리 ----

	case 'send': // 휴면계정 인증메일 발송

		if( !$_id ) { error_msg("잘못된 접근입니다."); }

		$r = _MQ("SELECT * FROM smart_individual_sleep where in_id='{$_id}'  ");
		if( sizeof($r) == 0 ) {error_alt("일치하는 회원정보가 없습니다.\\n\\n다시 한번 확인해 주세요.");}

		// - 메일발송 ---
		$email = $r['in_email'];
		if( mailCheck($email) ){

			$_app_auth = date("Y-m-d") . "§" . $_id . "§" . $email;
			$_AUTH_URL = OD_PROGRAM_URL . "/member.sleep_pro.php?_mode=auth&auth=" . onedaynet_encode( $_app_auth ) ;
			$_title = "[".$siteInfo[s_adshop]."] 휴면계정 인증을 위한 메일을 발송해드립니다.";
			include_once(OD_MAIL_ROOT."/member.sleep.mail.php"); // 메일 내용 불러오기 ($mailling_content)
			$_content = get_mail_content($mailling_content);
			mailer( $r['in_email'], $_title, $_content);
		}
		// - 메일발송 ---

		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_alt("[$r[in_name]]님의 메일(" . $email . ")로 인증메일을 전송해드렸습니다. \\n\\n발송된 이메일을 통해 인증을 진행해주시기 바랍니다.");
	break;
	// --- 휴면 회원 인증을 위한 메일 발송 ----

	// ---- 로그인 없이 휴면계정 처리 ----
	case "request":

		$r = _MQ("SELECT * FROM smart_individual_sleep where in_id='{$_id}'  ");
		if( sizeof($r) == 0 ) {error_alt("일치하는 회원정보가 없습니다.\\n\\n다시 한번 확인해 주세요.");}

		if( $siteInfo['member_return_type'] != 'login') error_alt("요청이 올바르지 않습니다.");

		// -- 휴면회원 로그인 없이 진행가능하도록 업데이트 
		_MQ_noreturn("update smart_individual set in_sleep_request = 'Y' where in_id = '".$_id."'  ");

		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_frame_loc_msg("/?pn=member.login.form" , "인증을 완료하였습니다.\\n\\n새로 로그인 하시면 정상적으로 서비스를 이용하실 수 있습니다.");
	break;
	// ---- 로그인 없이 휴면계정 처리 ----


}