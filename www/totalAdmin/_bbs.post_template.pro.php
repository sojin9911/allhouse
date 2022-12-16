<?php
	include_once("./inc.php");

	// -- 공통처리 (수정, 등록)
	if( in_array($_mode,array('modify','add')) == true){

		// --사전 체크 ---
		$_type = nullchk($_type , "분류를 선택해 주세요." );
//		$_title = nullchk($_title , "제목을 입력해주시기 바랍니다." );
		$_content = nullchk($_content , "내용을 입력해주시기 바랍니다." );


		$sque = "	bt_type = '".$_type."' ";
		$sque .=" , bt_title = '".$_title."'  ";
		$sque .=" , bt_content = '".$_content."'  ";

	}

	switch($_mode){

		case "add": // 등록
			$sque .= " , bt_rdate = now() ";
			_MQ_noreturn("insert smart_bbs_template set ".$sque);
			$_uid = mysql_insert_id();

			// KAY :: 에디터 이미지 관리 :: 에디터 이미지 등록함수 :: 2021-06-02 ---------
			editor_img_ex($_content , 'board_template' , $_uid );

			error_loc("_bbs.post_template.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
			break;

		case "modify": // 수정
			$sque .=" where bt_uid = '".$_uid."'  ";
			_MQ_noreturn("update smart_bbs_template set ".$sque);

			// KAY :: 에디터 이미지 관리 :: 에디터 이미지 등록함수 :: 2021-06-02 --------
			editor_img_ex($_content , 'board_template' , $_uid);

			error_loc("_bbs.post_template.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
			break;

		case "delete" : // 삭제

			// KAY :: 에디터 이미지 관리 :: 에디터 이미지 사용관리 DB삭제, 파일관리 사용개수 업데이트 :: 2021-07-07
			editor_img_del($_uid,'board_template');

			_MQ_noreturn("delete from smart_bbs_template  where bt_uid = '".$_uid."' ");
			error_loc("_bbs.post_template.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null));
			break;

	}

?>