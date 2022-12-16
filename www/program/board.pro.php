<?php
# 게시글 처리 프로세스
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


// -- 고유번호가 있다면 add, 또는 modify
if(!empty($_uid)) {
	$postInfo = _MQ("select * from smart_bbs where b_uid = '".$_uid."' ");
	$_menu = $postInfo['b_menu'];
}

$boardInfo = get_board_info($_menu);
$_menu = nullchk($_menu , "게시판 코드가 입력되지 않았습니다" , "" , "ALT");

// 전체권한 확인
$boardAuthChk = boardAuthChkAll($_menu); // 전체권한

// -- 관리자를 위한 처리
if( is_admin() === true && is_login() !== true){
	$mem_info = shopAdminInfo(); // 쇼핑몰 관리자 계정정보 호출 :: smart_individual and level is 9
}

// -- 입력데이터 공통처리
if( in_array( $_mode , array("add" , "modify" , "reply") ) ){

	// -- 사용/노출/기본 설정값을 변수로 미리 처리
	$boardFormData = array();
	$boardFormData['optionDateUse'] = $boardInfo['bi_option_date_use'] == 'Y' ? true : false; // 기간옵션 사용여부 , 이벤트에도 사용이 된다.
	$boardFormData['replyMode'] = in_array($boardInfo['bi_list_type'], array('qna')) == true ? true : false; // 답변 노출 여부 (qna 게시판만 가능)
	$boardFormData['imagesUploadUse'] = $boardInfo['bi_images_upload_use'] == 'Y' ? true : false; // 파일업로드 사용여부 판단
	$boardFormData['fileUploadUse'] = $boardInfo['bi_file_upload_use'] == 'Y' ? true : false; // 파일업로드 사용여부 판단
	$boardFormData['commentUse'] = $boardInfo['bi_comment_use'] == 'Y' ? true : false; // 댓글 사용여부
	$boardFormData['editorUse'] = $boardAuthChk['editor'] === true ? true : false; // 에디터 사용여부
	$boardFormData['secretUse'] =  $boardInfo['bi_secret_use']== 'Y' ? true : false; // 비밀글 사용여부
	$boardFormData['noticeUse'] =  is_admin() === true  ? true : false; // 공지사항 사용여부 (관리자 일시에만 적용가능)
	$boardFormData['passwdUse'] =  is_admin() !== true && is_login() !== true   ? true : false; // 비밀번호 입력 사용유무 (회원이 아니거나 관리자가 아닐 시 적용)

	// 리캡챠 사용여부
	if( $boardInfo['bi_recaptcha_use'] === 'Y' && ($siteInfo['recaptcha_api'] != '' && $siteInfo['recaptcha_secret'] != '') ){
		// -- 게시판에 따른 사용권한 판단
		$boardFormData['recaptchaUse'] = $boardInfo['bi_recaptcha_set'] === 'all' ? true : ( $boardInfo['bi_recaptcha_set'] == 'nonemember' && is_login() !== true ? true : false ) ;
		if( is_admin() === true) {  $boardFormData['recaptchaUse'] = false; }
	}else{
		$boardFormData['recaptchaUse'] = false;
	}

	// --사전 체크 --- {{{
	if( $boardFormData['recaptchaUse'] === true ){
		$secret=$siteInfo['recaptcha_secret'];
		$response=$_POST["g-recaptcha-response"];
		$verify=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
		$_action_result=json_decode($verify); # -- 스팸체크 결과
		if ($_action_result->success==false) {
			error_alt( "스팸방지를 확인인해 주세요.");
		}
	}

	$_menu = nullchk($_menu , "게시판을 선택해 주세요." , "" , "ALT" );
	$_title = nullchk($_title , "제목을 입력해 주세요.", "" , "ALT" );
	$_writer = nullchk($_writer , "작성자를 입력해 주세요.", "" , "ALT" );
	$_content = nullchk($_content , "내용을 입력해 주세요.", "" , "ALT" );

	// 2020-07-09 SSJ :: 웹 취약점 보완 패치
	$_content = RemoveXSS($_content);

	// 2019-03-05 SSJ :: 네이버 에디터 동영상 사이즈 제어를 위해 iframe 태그가 있으면 div.iframe_wrap 으로 감싸기
	$_content = wrap_tag_iframe($_content);


	if($boardFormData['passwdUse'] === true){
		$_passwd = nullchk($_passwd , "비밀번호를 입력해주시기 바랍니다." , "" , "ALT");
	}

	if( $boardFormData['optionDateUse'] === true){
		$_sdate = nullchk($_sdate , "기간(시작일)을 입력해 주세요.", "" , "ALT" );
		$_edate = nullchk($_edate , "기간(종료일)을 입력해 주세요.", "" , "ALT" );
	}
	// --사전 체크 --- }}}

	$_notice = $boardFormData['noticeUse'] === true && $_notice == 'Y' ? 'Y':'';
	$_secret = $boardFormData['secretUse'] === true && $_secret == 'Y' ? 'Y':'N';


	// -- 파일첨부 확인
	$sque  = "	b_menu = '".$_menu."' ";
	$sque .= " ,b_writer = '".$_writer."' ";
	//$sque .= " ,b_pcode = '".$_pcode."' ";
	$sque .= " ,b_title = '".addslashes(htmlspecialchars($_title))."' ";
	$sque .= " ,b_content = '".addslashes($_content)."' ";
	$sque .= " ,b_notice = '".$_notice."' ";
	$sque .= " ,b_secret = '".$_secret."' ";


	$sque .= " , b_reginfo_ip = '".$_SERVER['REMOTE_ADDR']."' ";
	$sque .= " , b_editor_use = '".($boardFormData['editorUse'] === true ? 'Y' : 'N' )."' ";

	// -- 시작일 종료일 있을경우에만 저장
	if( $_sdate != '' && $_edate != ''){
		$sque .= " ,b_sdate = '". $_sdate ."' ";
		$sque .= " ,b_edate = '". $_edate ."' ";
	}

	// KAY :: 게시판 카테고리설정 -- 카테고리 값 설정
	$sque .= " ,b_category = '".$_category."' ";

}

switch( $_mode ){

	// 등록
	case "add":

		if( $boardAuthChk['write'] !== true){ error_msg("본 게시판에 대한 권한이 없습니다."); }

		$_writer_type = $boardFormData['writeType'] =  is_admin() === true ? 'admin' : (is_login() === true ? 'member':'guest'); // 등록회원 타입
		$_img1_name = _PhotoPro(IMG_DIR_BOARD_ROOT , "_img1" );
		$_img2_name = _PhotoPro(IMG_DIR_BOARD_ROOT , "_img2" );


		if( $_img1_name != ''){ $sque .= " ,b_img1 = '".$_img1_name."' "; }
		if( $_img2_name != ''){ $sque .= " ,b_img2 = '".$_img2_name."' "; }

		$sque .= " , b_inid = '".$mem_info['in_id']."' ";
		$sque .= " , b_writer_type = '".$_writer_type."'  ";
		if($_passwd) {
			$sque .= " , b_passwd = password('".$_passwd."') ";
		}
		$sque .= " , b_rdate = now() ";

		$que = " insert smart_bbs set ".$sque;
		_MQ_noreturn($que);
		$_uid = mysql_insert_id();

		odtFileUpload('addFile','smart_bbs',$_uid); // 파일첨부

		// 게시물 갯수 업데이트
		update_board_post_cnt($_menu); // 게시글 개수 업데이트

		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_frame_loc_msg("/?pn=board.list&_menu=".$_menu."&" . enc('d' , $_PVSC), "정상적으로 등록하였습니다.") ;
		break;


	// 수정
	case "modify":

		// -- 수정 시 필요한 권한 체크
		if( is_admin() !== true){
			if( $postInfo['b_writer_type'] == 'member'){ // 회원이 남긴 글이라면
				if( $postInfo['b_inid'] != get_userid()){ error_msg("게시글 수정에 대한 권한이 없습니다.");  }
			}else if( $postInfo['b_writer_type'] == 'guest' ) { // 비회원이 남긴 글이라면
				$chk_passwd = _MQ_result("select password('".$_passwd."') ");
				if( $postInfo['b_passwd'] !=  $chk_passwd){ error_msg("비밀번호가 일치하지 않습니다."); }
			}
		}

		$_img1_name = _PhotoPro(IMG_DIR_BOARD_ROOT , "_img1" );
		$_img2_name = _PhotoPro(IMG_DIR_BOARD_ROOT , "_img2" );


		if( $_img1_name != ''){ $sque .= " ,b_img1 = '".$_img1_name."' "; }
		if( $_img2_name != ''){ $sque .= " ,b_img2 = '".$_img2_name."' "; }


		_MQ_noreturn(" update smart_bbs set ".$sque." where b_uid = '".$_uid."'  ");

		odtFileUpload('addFile','smart_bbs',$_uid); // 파일첨부 :: 추가피일에 대한 처리
		odtFileUpload('modifyFile','smart_bbs',$_uid); // 파일첨부 :: 수정파일에 대한 처리

		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_frame_loc_msg("/?pn=board.list&_menu=".$_menu."&" . enc('d' , $_PVSC), "정상적으로 수정하였습니다.") ;
		break;

    // 답글
	case "reply":

		// -- 게시판 권한 다시 체크
		if( $boardAuthChk['reply'] !== true){ error_msg("본 게시판에 대한 권한이 없습니다."); }

		$_writer_type = $boardFormData['writeType'] =  is_admin() === true ? 'admin' : (is_login() === true ? 'member':'guest'); // 등록회원 타입

		$sque .= " , b_depth = '2'  ";
		$sque .= " , b_relation = '".$postInfo['b_uid']."'  ";
		$sque .= " , b_inid = '".$mem_info['in_id']."' ";
		$sque .= " , b_writer_type = '".$_writer_type."'  ";
		$sque .= " , b_passwd = password('".$_passwd."') ";
		$sque .= " , b_rdate = now() ";
		_MQ_noreturn(" insert smart_bbs set ".$sque);
		$_uid = mysql_insert_id();

		odtFileUpload('addFile','smart_bbs',$_uid); // 파일첨부

		// 게시물 갯수 업데이트
		update_board_post_cnt($bbs_menu);

		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_frame_loc_msg("/?pn=board.list&_menu=".$_menu."&" . enc('d' , $_PVSC), "정상적으로 등록하였습니다.") ;
		break;

	// 삭제
	case "delete":

		// -- 삭제 시 본인글인지 체크 :: 기본 관리자가 아닐 시
		if( is_admin() !== true){
			if( $postInfo['b_writer_type'] == 'member'){ // 회원이 남긴 글이라면
				if( $postInfo['b_inid'] != get_userid()){ error_msg("게시글에 대한 삭제 권한이 없습니다.");  }
			}else if( $postInfo['b_writer_type'] == 'guest' ) { // 비회원이 남긴 글이라면
				$authCode = onedaynet_encode($siteInfo['s_license'].$_uid);
				if( $_SESSION['authPostItem'][$_uid] != $authCode ){ error_msg("게시글에 대한 삭제 권한이 없습니다."); }
			}
		}

		$que = " delete from smart_bbs where b_uid='{$_uid}' ";
		_MQ_noreturn($que);

		// 게시물 갯수 업데이트
		update_board_post_cnt($_menu);

        //이미지파일삭제
        _PhotoDel(IMG_DIR_BOARD_ROOT , $postInfo['b_img1']);
        _PhotoDel(IMG_DIR_BOARD_ROOT , $postInfo['b_img2']);

		// -- 파일삭제(데이터,첨부파일)
		$resBoardFiles = _MQ_assoc("select f_realname from smart_files where f_table_uid = '".$_uid."' and f_table = 'smart_bbs'   ");
		foreach($resBoardFiles as $k=>$v){
			deleteFiles($v['f_realname']);
		}

		_MQ_noreturn("delete  from smart_files where  f_table_uid = '".$_uid."'  and f_table = 'smart_bbs'  "); //파일, 과부하가 발생할 수 있으니 한번에 삭제

		unset($_SESSION['authPostItem'][$_uid]); // 데이터가 삭제되었기때문에 세션파기

        actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_frame_loc_msg("/?pn=board.list&_menu=".$_menu."&" . enc('d' , $_PVSC), "정상적으로 삭제하였습니다.") ;
		break;

}