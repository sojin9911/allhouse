<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



# 데이터 조회
if(!$_uid) error_msg("잘못된 접근입니다."); // _mode가 없을 경우 추가를 기본으로 함
$postInfo = get_post_info($_uid); // 게시물 정보 추출
$boardInfo = get_board_info($postInfo['b_menu']); // 게시판정보 추출
$_menu = $boardInfo['bi_uid']; // 게시판 아이디
$_buid = $_uid; // 게시물 아이디


# 경로
$boardInfo_tmp['_board_skin'] = ($_device_mode == 'pc'?$boardInfo['bi_skin']:$boardInfo['bi_skin_m']); // 스킨명
$boardInfo_tmp['_board_dir'] = ($_device_mode == 'pc'?OD_BOARD_SKIN_DIR:OD_BOARD_MSKIN_DIR).'/'.$boardInfo_tmp['_board_skin'];
$boardInfo_tmp['_board_root'] = $_SERVER['DOCUMENT_ROOT'].$boardInfo_tmp['_board_dir'];
$boardInfo_tmp['_board_url'] = $system['url'].$boardInfo_tmp['_board_dir'];
$boardInfo_tmp = array_merge($boardInfo_tmp, $boardInfo); // 배열 순서를 위하여 임시정보와 merge
$boardInfo = $boardInfo_tmp;
unset($boardInfo_tmp);

// -- 스킨정보 호출
$boardSkinInfo = getBoardSkinInfo($boardInfo['_board_skin']);


# 데이터 호출
// 노출여부 확인
if($boardInfo['bi_view'] == "N" && is_admin() !== true ) error_msg("현재 비공개로 전환된 게시판입니다.");

$_secretChk = postSecretChk($_uid);
if( $_secretChk !== true){ error_msg("비밀글에 대한 권한이 없습니다."); }

// 권한체크
$boardAuthChk = boardAuthChkAll($_menu); // 전체권한
if( $boardAuthChk['view'] !== true){
	switch($boardAuthChk['view']['code']) {
		case ""  : 	error_msg("본 게시판에 대한 권한이 없습니다."); // 아무것도 없을 시
		case "9995" :	error_loc_msg("/?pn=member.login.form&_rurl=".urlencode("/?".$_SERVER['QUERY_STRING']), $boardAuthChk['view']['msg']);
		default  :	error_msg($boardAuthChk['view']['msg']);
	}
}

// 삭제 버튼
if($is_auth) $delete_event = "bbs_delete(".$postInfo[b_uid].")";
else $delete_event = "delete_auth(".$postInfo[b_uid].")";

// 수정버튼
if($is_auth) $modify_event = "bbs_modify(".$postInfo[b_uid].")";
else $modify_event = "modify_auth(".$postInfo[b_uid].")";

// 조회수 증가
_MQ_noreturn(" update smart_bbs set b_hit = b_hit + 1 where b_uid = '{$_uid}' ");



// -- 가공데이터 배열화
$boardViewData = array();
$boardViewData['uid'] = $_buid;
$boardViewData['writer'] = $boardInfo['bi_writer_view_use'] == 'Y' ? $postInfo['b_writer'] : LastCut($postInfo['b_writer'],1); // 글쓴이
if( $postInfo['b_writer_type'] == 'admin') { $boardViewData['writer'] = $postInfo['b_writer']; }
$boardViewData['rdate'] = $postInfo['b_rdate']; // 글등록일
$boardViewData['hit'] = number_format($postInfo['b_hit']); // 조회수
$boardViewData['title'] = htmlspecialchars(stripslashes(($postInfo['b_title']))); // 글제목
$boardViewData['content'] = (stripslashes($postInfo['b_content']));

// -- 노출설정
$boardViewData['writerView'] = in_array($boardInfo['bi_list_type'], array('notice')) == false ? true:false; // 작성자 노출 :: 공지형일경우 안보임
$boardViewData['optionDateUse'] = $boardInfo['bi_option_date_use'] == 'Y' ? true : false; // 기간옵션 사용여부 , 이벤트에도 사용이 된다.
$boardViewData['replyMode'] = in_array($boardInfo['bi_list_type'], array('qna')) == true ? true : false; // 답변 노출 여부 (qna 게시판만 가능)
$boardViewData['imagesUploadUse'] = $boardInfo['bi_images_upload_use'] == 'Y' ? true : false; // 파일업로드 사용여부 판단
$boardViewData['fileUploadUse'] = $boardInfo['bi_file_upload_use'] == 'Y' ? true : false; // 파일업로드 사용여부 판단
$boardViewData['commentUse'] = $boardInfo['bi_comment_use'] == 'Y' ? true : false; // 댓글 사용여부
// KAY :: 게시판 카테고리설정
$boardViewData['categoryUse'] = $boardInfo['bi_category_use'] == 'Y' ? true : false; //카테고리 사용여부
$boardViewData['category']=$postInfo['b_category']; // 게시판 카테고리

// -- 본문에 답변이 노출이라면
if( $boardViewData['replyMode'] === true){
	$rowReply = _MQ("select * from smart_bbs where b_relation = '".$postInfo['b_uid']."' and b_depth > 1 ");
	// 관리자님의 답변 <strong>(2017-10-25)</strong>
	if( count($rowReply) > 0){
		$boardViewData['replyTitle'] = ''.$rowReply['b_writer'].'님의 답변 <strong>('.date('Y-m-d',strtotime($rowReply['b_rdate'])).')</strong>';
		$boardViewData['replyContent'] = (stripslashes($rowReply['b_content']));

		// -- qna의 답글 권한체크 :: 답글작성자가 본인이 아니고, 관리자가 아닐 시
		if( $rowReply['b_inid'] != get_userid() && is_admin() !== true){
			$boardAuthChk['reply'] = false;
		}else{
			$boardViewData['replyType'] = '답변수정';
		}
	}else{
		$boardViewData['replyTitle'] = '답변 대기중 입니다.';
		$boardViewData['replyContent'] = '답변글이 없습니다.';
		$boardViewData['replyType'] = '답변쓰기';
	}

	// -- 답글은 관리자만 작성가능하며 관리자 페이지에서 사용가능하도록 처리
	$boardAuthChk['reply'] = false;
}

// -- 기간옵션을 사용한다면 이벤트에 따른 표시
if( $boardViewData['optionDateUse'] === true && $postInfo['b_edate'] >= date('Y-m-d')){
	$boardViewData['eventing'] = true;
}

// -- 이벤트 기간표시
$boardViewData['eventSdate'] = $postInfo['b_sdate']; // 시작일
$boardViewData['eventEdate'] = $postInfo['b_edate']; // 종료일

// -- 이미지첨부가 사용가능, 노출이 view, 이미지가 있을경우
if( $boardViewData['imagesUploadUse'] === true && $boardSkinInfo['images_view'] == 'view' && is_file(IMG_DIR_BOARD_ROOT.$postInfo['b_img1']) == true ){
	$boardViewData['viewImagesUrl'] = IMG_DIR_BOARD_URL.$postInfo['b_img1'];
}else{
	$boardViewData['viewImagesUrl']='';
}

// -- 첨부파일이 있는지 확인
if( $boardViewData['fileUploadUse'] === true){
	$resFiles = getFilesRes('smart_bbs',$postInfo['b_uid']);
	if( count($resFiles) > 0){
		$arrFiles = array();
		foreach( $resFiles as $k=>$v){
			// (File Size : 5,840byte) ==> 파일 크기를 표시해줄때,,,,
			$arrFiles[] = '<a href="'.OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$v['f_uid'].'" class="link"  title="'.$v['f_oldname'].'">'.$v['f_oldname'].'</a>';
		}
		$boardViewData['filesLink'] = implode($arrFiles);
	}
}

// -- 권한별 버튼 처리
$boardViewData['writeLink'] = '/?pn=board.form&_menu='.$_menu.'&_uid='.$_uid.'&_mode=add&_PVSC='.$_PVSC;
$boardViewData['modifyLink'] = '/?pn=board.form&_menu='.$_menu.'&_uid='.$_uid.'&_mode=modify&_PVSC='.$_PVSC;
$boardViewData['replyLink'] = '/?pn=board.form&_menu='.$_menu.'&_uid='.$_uid.'&_mode=reply&_PVSC='.$_PVSC;
$boardViewData['deleteLink'] = OD_PROGRAM_URL.'/board.pro.php?_mode=delete&_uid='.$_uid.'&_menu='.$_menu.'&_PVSC='.$_PVSC;
$boardViewData['replyType'] = $boardViewData['replyType'] == '' ? '답글쓰기' :$boardViewData['replyType'];
if( is_admin() === true){
	$boardViewData['authModify'] = true;
	$boardViewData['authDelete'] = true;
}else{
	if( $postInfo['b_writer_type'] == 'member'){
		if( $postInfo['b_inid'] == get_userid()){
			$boardViewData['authModify'] = true;
			$boardViewData['authDelete'] = true;
		}
	}else if( $postInfo['b_writer_type'] == 'guest' ) {
		$boardViewData['authModify'] = true;
		$boardViewData['authDelete'] = true;
		$boardViewData['authClass'] = ' js_open_auth_pop';
	}
}

// -- 게시판 기본권한 처리
$boardViewData['authWrite'] = $boardAuthChk['write'];
$boardViewData['authReply'] = $boardAuthChk['reply'];

// -- qna 게시판 처리
// --

// -- 고객센터 /커뮤니티를 위한 변수정의 ///?pn=service.main
$boardHeaderData['viewTypeName'] = $arrBoardViewType[$boardInfo['bi_view_type']];
if( $boardInfo['bi_view_type'] == 'community'){
	$boardHeaderData['viewTypeLink'] = '/?pn=service.eval.list';
}else{
	$boardHeaderData['viewTypeLink'] = '/?pn=service.main';
}

// ------ 질문 답변형 게시판일 경우 처리 ------ 2019-02-20 LCY
if($boardInfo['bi_list_type'] == 'qna'){ $add_np_que = "  and b_depth != '2'  "; }


// -- 이전글, 다음글에 대한 출력을 해준다.
// 이전글
$prevr = _MQ(" select b_title , b_uid , b_rdate, b_secret, b_inid from smart_bbs where b_menu='".$_menu."' and b_notice!='Y' and b_uid<'".$_uid."' ".$add_np_que." ORDER BY b_uid desc limit 0 , 1");
$boardViewData['prevIs'] = count($prevr)>0 ? true : false;
$boardViewData['prevRdate'] = count($prevr)>0 ? date('Y-m-d',strtotime($prevr['b_rdate'])) : false;
$boardViewData['prevTitle'] = count($prevr)>0 ? htmlspecialchars(stripslashes($prevr['b_title'])) : "이전글이 없습니다.";
$boardViewData['prevLink'] = count($prevr)>0 ? "/?pn=board.view&_menu=".$_menu."&_uid=".$prevr['b_uid']."&_PVSC=".$_PVSC : "#none";
$boardViewData['prevUid'] = $prevr['b_uid'];
// -- 비밀글 판별
if($prevr['b_secret'] == 'Y'){ $boardViewData['prevSecretIcon'] = false; }
$boardViewData['prevSecretChk'] = postSecretChk($prevr['b_uid']);
$boardViewData['prevSecretEvtClass'] = '';
if( $boardViewData['prevSecretChk'] !== true){
	if(  $prevr['b_inid'] != ''){
		$boardViewData['prevSecretEvtClass'] = ' js_auth_fail'; // 무조건 실패 (권한이 없고 회원등록글이라면)
	}else{
		$boardViewData['prevSecretEvtClass'] = ' js_open_auth_pop'; // 비회원글이라면 비밀번호 팝업 이벤트
	}
}
//다음글
$nextr = _MQ(" select b_title , b_uid , b_rdate, b_secret , b_inid from smart_bbs where b_menu='".$_menu."' and b_notice!='Y' and b_uid>'{$_uid}' ".$add_np_que." ORDER BY b_uid asc limit 0 , 1");
$boardViewData['nextIs'] = count($nextr)>0 ? true : false;
$boardViewData['nextRdate'] = count($nextr)>0 ? date('Y-m-d',strtotime($nextr['b_rdate'])) : false;
$boardViewData['nextTitle'] = count($nextr)>0 ? htmlspecialchars(stripslashes($nextr['b_title'])) : "다음글이 없습니다.";
$boardViewData['nextLink'] = count($nextr)>0 ? "/?pn=board.view&_menu=".$_menu."&_uid=".$nextr['b_uid']."&_PVSC=".$_PVSC : "#none";
$boardViewData['nextUid'] = $nextr['b_uid'];
// -- 비밀글 판별
if($nextr['b_secret'] == 'Y'){ $boardViewData['nextSecretIcon'] = false; }
$boardViewData['nextSecretChk'] = postSecretChk($nextr['b_uid']);
$boardViewData['nextSecretEvtClass'] = '';
if( $boardViewData['nextSecretChk'] !== true){
	if(  $nextr['b_inid'] != ''){
		$boardViewData['nextSecretEvtClass'] = ' js_auth_fail'; // 무조건 실패 (권한이 없고 회원등록글이라면)
	}else{
		$boardViewData['nextSecretEvtClass'] = ' js_open_auth_pop'; // 비회원글이라면 비밀번호 팝업 이벤트
	}
}




ob_start();
# 스킨호출
include_once($boardInfo['_board_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
$BoardSkinData = ob_get_contents();
ob_end_clean();



include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행