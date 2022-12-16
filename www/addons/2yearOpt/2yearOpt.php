<?PHP
// 매 2년마다 수신동의 설정 연동 -- 메일을 통한 수신동의 수정
// 넘어온 변수 : p
include_once('inc.php');


// 받은 변수 복호화 적용
$pass_var = (function_exists(onedaynet_decode)?onedaynet_decode($p):enc('d',$p));


$ex1 = explode("§" , $pass_var);
$arr_var = array();
foreach($ex1 as $k=>$v){
	$ex2 = explode("|" , $v);
	$arr_var[trim($ex2[0])]= $ex2[1];
}
//ViewArr($arr_var);
//	[id] => admin
//	[mode] => mail
//	[pass] => Y


// 회원정보 추출
$mr = _MQ(" select * from smart_individual where in_id = '". $arr_var['id'] ."' ");
// 2년이 넘었거나 오늘일 경우 적용
if( 
	date("Ymd") == date("Ymd" , strtotime($mr['m_opt_date'])) 
	||
	date("Ymd" , strtotime("-2 year")) >= date("Ymd" , strtotime($mr['m_opt_date'])) 
){
	_MQ_noreturn(" update smart_individual set ". ($arr_var['mode'] == "mail" ? "in_emailsend" : "in_smssend") ." = '". $arr_var['pass'] ."' , m_opt_date = now() where in_id = '". $arr_var['id'] ."' ");

	// 파일명 : changeAlert.mail.contents.2yearopt.php
	// 매 2년 수신동의 재설정 시 광고성 정보 수신동의 상태 - 정보 추가
	// $arr_var 정보가 있어야 함.
	//	arr_var = array( id => 아이디 , mode => (mail , sms) , pass => (Y , N) )

	$id = $arr_var['id'] ;
	if( $id ) {
		// 회원정보 추출
		$mem_info = _MQ(" select * from smart_individual where in_id = '". $id ."' and in_userlevel != '9' ");
		$email = $mem_info['in_email'];

		// -- 메일발송
		if( mailCheck($email) ){
			include_once(OD_MAIL_ROOT."/changeAlert.mail.contents.2yearopt.php"); // 메일 내용 불러오기 ($mailing_content)
			$_title = "[".$siteInfo['s_adshop']."] 수신동의 상태가 변경되었습니다.";
			$_content = get_mail_content($mailling_content);
			mailer( $email , $_title , $_content );
		}
	}



	error_loc_msg( "/" , ($arr_var['mode'] == "mail" ? "메일수신" : "문자수신") ."설정을 ".($arr_var['pass'] == "Y" ? "동의" : "거부")."로 저장하였습니다.");
}
// 위 조건이 안될 경우 미적용
else {
	error_loc_msg( "/" , "메일을 통해 수정할 수 없습니다.");
}

exit;

?>