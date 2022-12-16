<?php
	include_once "./inc.php";

	if( $_uid != ''){ $r = _MQ(" select * from smart_bbs_faq where bf_uid='{$_uid}' ");}

	// -- 공통처리
	if( in_array($_mode,array('add','modify')) == true){
		// --사전 체크 ---
		$_title = nullchk($_title , "제목을 입력해 주세요." );
		$_type = nullchk($_type , "분류를 선택해 주세요." );
		$_content = nullchk($_content , "내용을 입력해 주세요." );

		// 2019-03-05 SSJ :: 네이버 에디터 동영상 사이즈 제어를 위해 iframe 태그가 있으면 div.iframe_wrap 으로 감싸기
		$_content = wrap_tag_iframe($_content);

		$_best = $_best != 'Y' ? '' : 'Y'; // 공지사항체크 유무

		// -- 파일첨부 확인
		$sque = " bf_title = '".$_title."' ";
		$sque .= " ,bf_type = '".$_type."' ";
		$sque .= " ,bf_content = '".addslashes($_content)."' ";
		$sque .= " ,bf_best = '".$_best."' ";
	}

	switch($_mode){

		case "add": // 추가
			$sque .= " , bf_rdate = now() ";
			_MQ_noreturn(" insert smart_bbs_faq set ".$sque);
			$_uid = mysql_insert_id();

			// KAY :: 에디터 이미지 관리 :: 에디터에 이미지 등록함수  :: 2021-06-02 -------------
			editor_img_ex($_content , 'board_faq' , $_uid);
			error_loc("_bbs.post_faq.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
			break;

		case "modify": // 수정
			_MQ_noreturn(" update smart_bbs_faq set ".$sque." where bf_uid = '".$_uid."'  ");

			// KAY :: 에디터 이미지 관리 :: 에디터에 이미지 등록함수 :: 2021-06-02 -------------
			editor_img_ex($_content , 'board_faq' , $_uid);
			error_loc("_bbs.post_faq.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
			break;

		case "selectDelete": // 선택삭제
		case "delete": // 삭제
			if( $_mode == 'delete'){ $chkVar[] = $_uid; }
			if( count($chkVar)  < 1 ){ error_msg("삭제할 게시글이 존재하지 않습니다."); }

			// KAY :: 에디터 이미지 관리 :: 에디터 이미지 사용관리 DB삭제, 파일관리 사용개수 업데이트 :: 2021-07-07
			editor_img_del($chkVar,'board_faq');

			_MQ_noreturn("delete  from smart_bbs_faq where  find_in_set(bf_uid, '".implode(",",$chkVar)."' ) > 0 "); // 게시글
			error_loc("_bbs.post_faq.list.php?".enc('d' , $_PVSC ));
			break;

	}
?>