<?PHP
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// --사전 체크 --- {{{
if( $siteInfo['recaptcha_api'] != '' && $siteInfo['recaptcha_secret'] != ''){
	$secret=$siteInfo['recaptcha_secret'];
	$response=$_POST["g-recaptcha-response"];
	$verify=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
	$_action_result=json_decode($verify); # -- 스팸체크 결과
	if ($_action_result->success==false) {
		echo '<script>parent.grecaptcha.reset()</script>';
		error_alt( "스팸방지를 확인인해 주세요.");
	}
}



$r_menu = nullchk(trim($_menu) , "잘못된 코드입니다. 다시 시도하시기 바랍니다." , "" , "ALT");
$r_comname = nullchk(trim($_comname) , "상호명이나 이름을 입력해주시기 바랍니다." , "" , "ALT");
$r_tel = nullchk(trim($_tel) , "연락처를 입력해주시기 바랍니다." , "" , "ALT");
$r_title = nullchk(trim($_title) , "제목을 입력해주시기 바랍니다." , "" , "ALT");
$r_content = nullchk(trim($_content) , "내용을 입력해주시기 바랍니다." , "" , "ALT"); // nullchk - alert 형식으로 return
$r_email = $join_email;

// --사전 체크 ---

// -- 등록한 첨부파일명 ---
//$_file_name = _FilePro($_SERVER['DOCUMENT_ROOT']."/upfiles/normal" , "_file" ) ;
// -- 등록한 첨부파일명 ---



$que = "
	insert smart_request set
		 r_comname = '". $r_comname ."'
		,r_tel = '". $r_tel ."'
		,r_hp = '". $r_tel ."'
		,r_email = '". $r_email ."'
		,r_title = '". $r_title ."'
		,r_content = '". $r_content ."'
		,r_inid = '" . get_userid() . "'
		,r_status = '답변대기'
		,r_rdate = now()
		,r_menu = '".$r_menu."'
";
_MQ_noreturn($que);
$_uid = mysql_insert_id();
odtFileUpload('addFile','smart_request',$_uid); // 파일첨부

// 문자 발송
$sms_to = $r_tel;
shop_send_sms($sms_to,"request");


actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
// 내부패치 68번줄 kms 2019-11-05
error_frame_reload("정상적으로 등록하였습니다.\\n\\n등록해주신 이메일로 관리자의 답변메일이 전송됩니다.\\n\\n빠른 답변드리도록 노력하겠습니다.\\n\\n감사합니다.") ;
/*
if(is_login()) // 회원이면 나의 주문내역 페이지로 이동
	error_frame_loc_msg("/?pn=mypage.inquiry.list","정상적으로 등록하였습니다.\\n\\n등록해주신 이메일로 관리자의 답변메일이 전송됩니다.\\n\\n빠른 답변드리도록 노력하겠습니다.\\n\\n감사합니다.") ;
else
	error_frame_reload("정상적으로 등록하였습니다.\\n\\n등록해주신 이메일로 관리자의 답변메일이 전송됩니다.\\n\\n빠른 답변드리도록 노력하겠습니다.\\n\\n감사합니다.") ;
*/
