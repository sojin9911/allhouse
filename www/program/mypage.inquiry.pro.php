<?PHP
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

if($_mode == 'delete') { // 삭제
	$uid = nullchk($uid , '잘못된 접근입니다.', '', 'ALT');
	$r = _MQ(" select * from smart_request where (1) and r_inid= '". get_userid() ."' and r_uid = '{$uid}' ");
	if(!$r['r_uid']) die('no data');
	_MQ_noreturn(" delete from smart_request where (1) and r_inid= '". get_userid() ."' and r_uid = '{$uid}' ");

	// LCY : 2022-08-30 : 파일업로드 보완 패치 --
	// -- 파일삭제(데이터,첨부파일)
	$resBoardFiles = _MQ_assoc("select f_realname , f_uid from smart_files where f_table_uid = '".$uid."_user' and f_table = 'smart_request'   ");
	foreach($resBoardFiles as $k=>$v){
		deleteFiles($v['f_realname'] , $v['f_uid']);
	}

}
else { // 추가
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


	$request_title = nullchk(trim($_title) , "제목을 입력해주시기 바랍니다." , "" , "ALT");
	$request_content = nullchk(trim($_content) , "내용을 입력해주시기 바랍니다." , "" , "ALT"); // nullchk - alert 형식으로 return

	$que = "
		insert smart_request set
			 r_title = '". $request_title ."'
			,r_content = '". $request_content ."'
			,r_inid = '" . get_userid() . "'
			,r_comname = '".$mem_info['in_name']."'
			,r_status = '답변대기'
			,r_rdate = now()
			,r_menu = '".$_menu."'
	";
	_MQ_noreturn($que);

	$_uid = mysql_insert_id();
	odtFileUpload('addFile', 'smart_request', $_uid.'_user'); // 파일첨부


	// 문자 발송
	$sms_to = $mem_info[in_tel2] ? $mem_info[in_tel2] : $mem_info[in_tel];
	shop_send_sms($sms_to,"request");

	actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
	error_frame_loc_msg("/?pn=mypage.inquiry.list", "정상적으로 등록하였습니다.\\n\\n빠른 답변드리도록 노력하겠습니다.\\n\\n감사합니다.");
}