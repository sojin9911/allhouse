<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// -- 실행모드가 없을경우 무조건 추가
$_mode = in_array($_mode,array('add','reply','modify')) == false ? 'add':$_mode;

// 게시판 정보 추출
$boardInfo = get_board_info($_menu);
$boardInfo_tmp['_board_skin'] = ($_device_mode == 'pc'?$boardInfo['bi_skin']:$boardInfo['bi_skin_m']); // 스킨명
$boardInfo_tmp['_board_dir'] = ($_device_mode == 'pc'?OD_BOARD_SKIN_DIR:OD_BOARD_MSKIN_DIR).'/'.$boardInfo_tmp['_board_skin'];
$boardInfo_tmp['_board_root'] = $_SERVER['DOCUMENT_ROOT'].$boardInfo_tmp['_board_dir'];
$boardInfo_tmp['_board_url'] = $system['url'].$boardInfo_tmp['_board_dir'];
$boardInfo_tmp = array_merge($boardInfo_tmp, $boardInfo); // 배열 순서를 위하여 임시정보와 merge
$boardInfo = $boardInfo_tmp;
unset($boardInfo_tmp);

// 게시판 아이디
$_menu = $boardInfo['bi_uid'];

// 노출여부 확인
if($boardInfo['bi_view'] == "N" && is_admin() !== true ) error_msg("현재 비공개로 전환된 게시판입니다.");

// 권한체크
$boardAuthChk = boardAuthChkAll($_menu); // 전체권한

// -- 노출/사용설정
$boardFormData['optionDateUse'] = $boardInfo['bi_option_date_use'] == 'Y' ? true : false; // 기간옵션 사용여부 , 이벤트에도 사용이 된다.
$boardFormData['replyMode'] = in_array($boardInfo['bi_list_type'], array('qna')) == true ? true : false; // 답변 노출 여부 (qna 게시판만 가능)
$boardFormData['imagesUploadUse'] = $boardInfo['bi_images_upload_use'] == 'Y' ? true : false; // 파일업로드 사용여부 판단
$boardFormData['fileUploadUse'] = $boardInfo['bi_file_upload_use'] == 'Y' ? true : false; // 파일업로드 사용여부 판단
$boardFormData['commentUse'] = $boardInfo['bi_comment_use'] == 'Y' ? true : false; // 댓글 사용여부
$boardFormData['editorUse'] = $boardAuthChk['editor'] === true ? true : false; // 에디터 사용여부
$boardFormData['secretUse'] =  $boardInfo['bi_secret_use']== 'Y' ? true : false; // 비밀글 사용여부
$boardFormData['noticeUse'] =  is_admin() === true  ? true : false; // 공지사항 사용여부 (관리자 일시에만 적용가능)
$boardFormData['passwdUse'] =  is_admin() !== true && is_login() !== true   ? true : false; // 비밀번호 입력 사용유무 (회원이 아니거나 관리자가 아닐 시 적용)
// KAY :: 게시판 카테고리설정
$boardFormData['categoryUse'] = $boardInfo['bi_category_use'] == 'Y' ? true : false; // 카테고리 사용유무
$boardFormData['category']=array_filter(explode(",",$boardInfo['bi_category'])); // 게시판 카테고리

// 리캡챠 사용여부
if( $boardInfo['bi_recaptcha_use'] === 'Y' && ($siteInfo['recaptcha_api'] != '' && $siteInfo['recaptcha_secret'] != '') ){
	// -- 게시판에 따른 사용권한 판단
	$boardFormData['recaptchaUse'] = $boardInfo['bi_recaptcha_set'] === 'all' ? true : ( $boardInfo['bi_recaptcha_set'] == 'nonemember' && is_login() !== true ? true : false ) ;
	$boardFormData['recaptchaApi'] = $siteInfo['recaptcha_api']; // 구글 api 키값 (공개키)
	$boardFormData['recaptchaSecret'] = $siteInfo['recaptcha_secret']; // 구글 secret 키값 (비밀키)
	if( is_admin() === true) {  $boardFormData['recaptchaUse'] = false; }
}else{
	$boardFormData['recaptchaUse'] = false;
}

// -- 관리자를 위한 처리
if( is_admin() === true && is_login() !== true){
	$mem_info = shopAdminInfo(); // 쇼핑몰 관리자 계정정보 호출 :: smart_individual and level is 9
}

// -- 추가라면 쓰기 권한을 체크
if( $_mode == 'add' ) {
	if( $boardAuthChk['write'] !== true){
		switch($boardAuthChk['view']['code']) {
			case ""  : 	error_msg("본 게시판에 대한 권한이 없습니다."); // 아무것도 없을 시
			case "9995" :	error_loc_msg("/?pn=member.login.form&_rurl=".urlencode("/?".$_SERVER['QUERY_STRING']), $boardAuthChk['view']['msg']);
			default  :	error_msg($boardAuthChk['view']['msg']);
		}
	}

	$boardFormData['modeType'] = '작성';

	$boardFormData['writer'] = $mem_info['in_name'] != '' ? $mem_info['in_name'] : null;

	// -- 게시글 양식 이 있다면, 추가일 시에만 적용
	if( $boardInfo['bi_btuid'] != ''){
		$rowTemplate = _MQ("select *from smart_bbs_template where bt_uid = '".$boardInfo['bi_btuid']."' ");
//		$boardFormData['title']  = htmlspecialchars(stripslashes($rowTemplate['bt_title']));
		$boardFormData['content'] = stripslashes($rowTemplate['bt_content']);
	}else{
		$boardFormData['title']  = htmlspecialchars(stripslashes($postInfo['b_title']));
		$boardFormData['content'] = stripslashes($postInfo['b_content']);
	}


	$boardFormData['pwTxt'] = '글을 수정/삭제할 때 사용합니다.';

}else if($_mode == 'reply'){

	if( $boardFormData['replyMode'] === true){ // 답변모드가 있다면
		// -- 답글은 관리자만 작성가능하며 관리자 페이지에서 사용가능하도록 처리
		error_msg("본 게시판에 대한 권한이 없습니다.");
	}

	// -- 일반 답글 권한 체크
	if( $boardAuthChk['reply'] !== true){
		switch($boardAuthChk['view']['code']) {
			case ""  : 	error_msg("본 게시판에 대한 권한이 없습니다."); // 아무것도 없을 시
			case "9995" :	error_loc_msg("/?pn=member.login.form&_rurl=".urlencode("/?".$_SERVER['QUERY_STRING']), $boardAuthChk['view']['msg']);
			default  :	error_msg($boardAuthChk['view']['msg']);
		}
	}

	$postInfo = _MQ("select * from smart_bbs where b_uid = '".$_uid."'");
	if( count($postInfo) < 1){ error_msg("게시글 정보가 없습니다.");   }

	$boardFormData['modeType'] = '답글';
	$boardFormData['writer'] = $mem_info['in_name'] != '' ? $mem_info['in_name'] : null;
	$boardFormData['title']  = "RE : ".htmlspecialchars(stripslashes($postInfo['b_title']));
	$boardFormData['content'] = "-----------------------------------------------------<br>☞".($postInfo['b_writer'])."님의 글입니다.<br>-----------------------------------------------------<br>".stripslashes($postInfo['b_content'])."<br>-----------------------------------------------------";
}else{ // 수정
	$postInfo = _MQ("select * from smart_bbs where b_uid = '".$_uid."'");
	if( count($postInfo) < 1){ error_msg("게시글 정보가 없습니다.");   }

	if( is_admin() !== true){
		if( $postInfo['b_writer_type'] == 'member'){ // 회원이 남긴 글이라면
			if( $postInfo['b_inid'] != get_userid()){ error_msg("게시글 수정에 대한 권한이 없습니다.");  }
		}else if( $postInfo['b_writer_type'] == 'guest' ) { // 비회원이 남긴 글이라면
			$authCode = onedaynet_encode($siteInfo['s_license'].$_uid);
			if( $_SESSION['authPostItem'][$_uid] != $authCode ){ error_msg("게시글 수정에 대한 권한이 없습니다."); }
		}
	}

	$boardFormData['modeType'] = '수정';
	$boardFormData['writer'] = $postInfo['b_writer'];
	$boardFormData['title']  = htmlspecialchars(stripslashes($postInfo['b_title']));
	$boardFormData['content'] = stripslashes($postInfo['b_content']);

	$boardFormData['pwTxt'] = '글을 등록시 입력 하셨던 비밀번호를 입력해 주세요';
}

// -- 첨부파일이 있는지 확인
$boardFormData['resFile'] = array();
$boardFormData['addFileCnt'] = 0;
if( $boardFormData['fileUploadUse'] === true){
	$boardFormData['resFile'] = getFilesRes('smart_bbs',$postInfo['b_uid']); // 첨부파일이 있을경우 가져온다.

	$boardFormData['addFileCnt'] = $arrUpfileConfig['cnt'] - count($boardFormData['resFile']);
	$boardFormData['addFileUse'] = $boardFormData['addFileCnt'] > 0 ? true : false;
}

// -- 이미지 파일이 있는지 체크
if( $boardFormData['imagesUploadUse'] === true && is_file(IMG_DIR_BOARD_ROOT.$postInfo['b_img1']) === true){
	$boardFormData['listImage'] = IMG_DIR_BOARD_URL.$postInfo['b_img1'];
}

// -- 체크유무 판별
$boardFormData['noticeChk'] = $postInfo['b_notice'] == 'Y' ? true : false;
$boardFormData['secretChk'] = $postInfo['b_secret'] == 'Y' ? true : false;

if( $boardFormData['optionDateUse'] === true){
	$boardFormData['sdate'] = $postInfo['b_sdate'];
	$boardFormData['edate'] = $postInfo['b_edate'];
}


// -- 고객센터 /커뮤니티를 위한 변수정의 ///?pn=service.main
$boardHeaderData['viewTypeName'] = $arrBoardViewType[$boardInfo['bi_view_type']];
if( $boardInfo['bi_view_type'] == 'community'){
	$boardHeaderData['viewTypeLink'] = '/?pn=service.eval.list';
}else{
	$boardHeaderData['viewTypeLink'] = '/?pn=service.main';
}


ob_start();
# 스킨호출
include_once($boardInfo['_board_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
$BoardSkinData = ob_get_contents();
ob_end_clean();



include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행

