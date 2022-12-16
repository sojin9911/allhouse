<?PHP

	include "./inc.php";

	if( in_array( $_mode , array("add" , "modify") ) ){

		// --사전 체크 ---
		$_uid = nullchk($_uid , "게시판 아이디를 입력 해주시기 바랍니다." );
		$_name = nullchk($_name , "게시판 이름을 입력해주시기 바랍니다." );
		$_listmaxcnt = nullchk($_listmaxcnt , "페이지당 게시물 수 를 입력해 주세요." );
		$_board_skin = nullchk($_board_skin , "게시판 스킨을 선택해 주세요." );
		$_writer_view_use = nullchk($_writer_view_use , "작성자노출 설정을 선택해 주세요." );
		if( $_write_day_use == 'Y'){ $_write_day_cnt = nullchk($_write_day_cnt , "글쓰기 제한 개수를 입력해 주세요." ); }
		// --사전 체크 ---

		// -- 리스트권한설정 :: 회원의 정보, 지정이 회원이든 아니든 저장된정보를 저장
		if( count($_auth_group) > 0){
			foreach($_auth_group as $k=>$v){
				// -- 체크리스트가 있다면
				if( count($_auth_group[$k]) > 0){
					${_auth_.$k._group} = implode(",",$_auth_group[$k]);
				}
			}
		}

		// -- 게시판 스킨옵션에 따른 설정 :: 스킨자체에서 사용하지 못하는 기능은 N 으로 처리
		$_skinName = $_board_skin; $agent = 'pc';
		$skinInfo = getBoardSkinInfo($_skinName,$agent);
		$skinOption = $skinInfo[$_skinName]['skin']; // 변수를 짧게 줄인다
		if( $skinOption['file'] != 'true'){ $_file_upload_use = 'N'; }
		if( $skinOption['images'] != 'true'){ $_images_upload_use = 'N'; }
		if( $skinOption['date'] != 'true'){ $_option_date_use = 'N'; }

		// -- 게시판 스킨에 따라 필 수선택
		if( in_array($skinOption['type'],array('event')) == true) { $_option_date_use = 'Y';  } // 기간옵션 사용여부 필수 고정

		// -- 게시판 타입에 따라 불가능한 옵션 설정
		if( in_array($skinOption['type'],array('qna')) == true) { $_comment_use = 'N';  } // 댓글


		// -- bi_list_type :: skin 정보에서 가져온다.
		$sque = "
				bi_name				= '".$_name ."'
				,bi_view			= '".$_view ."'
				,bi_auth_list		= '".$_auth_list."'
				,bi_auth_view		= '".$_auth_view ."'
				,bi_auth_write		= '".$_auth_write ."'
				,bi_auth_reply		= '".$_auth_reply ."'
				,bi_auth_comment	= '".$_auth_comment ."'
				,bi_listmaxcnt		= '".$_listmaxcnt."'
				,bi_newicon_view	= '".$_newicon_view."'
				,bi_comment_use		= '".$_comment_use."'
				,bi_secret_use		= '".$_secret_use."'
				,bi_list_type		= '".$_list_type ."'

				,bi_file_upload_use	= '".$_file_upload_use."'
				,bi_file_size_limit	= '".$_file_size_limit."'
				,bi_html_header		= '".$_html_header."'
				,bi_html_footer		= '".$_html_footer."'
		";

		// -- 추가쿼리문 --
		$sque .= " ,	bi_view_type = '".$_view_type."' " ; // 노출타입
		$sque .= " ,	bi_skin			= '".$_board_skin."' " ; // 스킨
		$sque .= " ,	bi_skin_m			= '".$_board_skin."' " ; // 스킨 모바일 :: PC에 상속


		$sque .= " ,	bi_auth_list_group	= '".$_auth_list_group."'";  // 리스트권한 :: 회원일 시 그룹
		$sque .= " ,	bi_auth_view_group	= '".$_auth_view_group."'"; // 보기권한 :: 회원일 시 그룹
		$sque .= " ,	bi_auth_write_group	= '".$_auth_write_group."'"; // 쓰기권한 :: 회원일 시 그룹
		$sque .= " ,	bi_auth_reply_group	= '".$_auth_reply_group."'"; // 답글권한 :: 회원일 시 그룹
		$sque .= " ,	bi_auth_comment_group	= '".$_auth_comment_group."'"; // 댓글권한 :: 회원일 시 그룹

		$sque .= " ,	bi_auth_editor	= '".$_auth_editor."'"; // 에디터권한 (Y,N)
		$sque .= " ,	bi_auth_editor_group	= '".$_auth_editor_group."'"; // 에디터권한 :: 회원일 시 그룹

		$sque .= " ,	bi_reply_use	= '".$_reply_use."'"; // 댓글권한 :: 회원일 시 그룹
		$sque .= " ,	bi_writer_view_use = '$_writer_view_use' " ; // 글쓴이 노출조건여부 Y 일시 전체, P 일시 일부, N일시 숨김
		$sque .= " ,	bi_write_day_use = '$_write_day_use' " ; // 당일 기준 작성가능 게시물 제한여부
		$sque .= " ,	bi_write_day_cnt = '$_write_day_cnt' " ; // 당일기준 작성가능 게시물 제한일경우 개수

		$sque .= " ,	bi_recaptcha_use = '$_recaptcha_use' " ; // 리캡챠 사용여부
		$sque .= " ,	bi_recaptcha_set = '$_recaptcha_set' " ; // 리캡챠 적용대상

		$sque .= " ,	bi_images_upload_use = '$_images_upload_use' " ; // 이미지 업로드 사용여부
		$sque .= " ,	bi_option_date_use = '$_option_date_use' " ; // 기간 이벤트 사용여부

		$sque .= " ,	bi_btuid = '$_btuid' " ; // 게시글 양식

		// KAY :: 게시판 카테고리설정
		$sque .= " , bi_category_use = '".$_category_use."' " ;
		$sque .= " , bi_category = '".$_category."' " ;

	}


	// 정보 추출 (modify / delete)
	if( $_uid ){
		$r = _MQ(" select * from smart_bbs_info where bi_uid='{$_uid}' ");
	}


	// - 모드별 처리 ---
	switch( $_mode ){

		// -- 등록 ---
		case "add":

			// -- 게시물 순서를 맨뒤로 보내기 위한 처리
			$chkIdx = _MQ("select max(bi_view_idx) as idx from smart_bbs_info where bi_view_type = '$_view_type' ");
			if( count($chkIdx) > 0 && $chkIdx['idx'] > 0) { $_view_idx =  $chkIdx['idx']+1; }
			else{ $_view_idx = 1;  }

			$que = " insert smart_bbs_info set {$sque} , bi_uid='".$_uid."' , bi_view_idx = '".$_view_idx."', bi_rdate = now() ";
			_MQ_noreturn($que);

			error_loc("_bbs.board.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
			break;
		// -- 등록 ---


		// -- 수정 ---
		case "modify":


			$que = " update smart_bbs_info set {$sque} where bi_uid='{$_uid}' ";
			_MQ_noreturn($que);

			// -- 노출구분이 변경되었는지 체크하여 변경되었을경우 해당 게시판은 뒤로
			if($r['bi_view_type'] != $_view_type){
				$chkIdx = _MQ("select max(bi_view_idx) as idx from smart_bbs_info where bi_view_type = '$_view_type' ");
				if( count($chkIdx) > 0 && $chkIdx['idx'] > 0) { $_view_idx =  $chkIdx['idx']+1; }
				else{ $_view_idx = 1;  }

				$que = " update smart_bbs_info set  bi_view_idx = '".$_view_idx."' where bi_uid='{$_uid}' ";
				_MQ_noreturn($que);
			}


			error_loc("_bbs.board.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
			break;
		// -- 수정 ---

		// -- 삭제 ---
		case "delete":

			if(preg_match("/^notice$|^event$/",$_uid)) { error_msg($r[bi_name]." 게시판은 삭제 할수 없는 게시판입니다."); }
			if( count($r) < 1){  error_msg('게시판 정보가 존재 하지 않습니다.'); }

			// -- 등록된 게시물이 있는지 체크
			$rowChk = _MQ("select count(*) as cnt from smart_bbs where b_menu = '".$r['bi_uid']."' ");
			if( $rowChk['cnt'] > 0){ error_msg("본 게시판에 등록된 게시글을 전부 삭제하셔야 처리 가능합니다."); }


			_MQ_noreturn("delete from smart_bbs_info where bi_uid='{$_uid}' ");
			error_loc("_bbs.board.list.php?".enc('d' , $_PVSC ));
			break;
		// -- 삭제 ---


		// -- 노출순서 변경 : 리스트에서 넘어옴
		case "change_idx":
			if($_idx && $_uid){
				_MQ_noreturn(" update smart_bbs_info set bi_view_idx = '". ($_idx >1 ? $_idx : 1) ."' where bi_uid='{$_uid}' ");
				error_loc("_bbs.board.list.php?".enc('d' , $_PVSC ));
			}else{
				error_msg("필수 정보가 누락되었습니다.");
			}

			break;


		// -- 게시판 정렬
		case "sort":
			if( count($r) < 1){  error_msg('게시판 정보가 존재 하지 않습니다.'); }

			if($_sort == 'up'){ // 위로
				// --  한단계 높은 쉬위를 가진 아이템 호출
				$sortOrg = _MQ("select bi_uid,bi_view_idx from smart_bbs_info where bi_view_type = '".$r['bi_view_type']."' and bi_view_idx < '".$r['bi_view_idx']."' order by bi_view_idx desc limit 0,1  ");

				if( count($sortOrg) < 1){ error_msg("맨 처음입니다."); }

				_MQ_noreturn("update smart_bbs_info set bi_view_idx = '".$sortOrg['bi_view_idx']."' where bi_uid = '".$r['bi_uid']."'   ");
				_MQ_noreturn("update smart_bbs_info set bi_view_idx = '".$r['bi_view_idx']."' where bi_uid= '".$sortOrg['bi_uid']."'   ");

			}
			else if($_sort == 'down'){ // 아래로
				// -- 한단계 낮은 순위를 가진 아이템 호출
				$sortOrg = _MQ("select bi_uid,bi_view_idx from smart_bbs_info where bi_view_type = '".$r['bi_view_type']."' and bi_view_idx > '".$r['bi_view_idx']."' order by bi_view_idx asc limit 0,1  ");
				if( count($sortOrg) < 1){ error_msg("맨 마지막입니다."); }

				_MQ_noreturn("update smart_bbs_info set bi_view_idx = '".$sortOrg['bi_view_idx']."' where bi_uid = '".$r['bi_uid']."'   ");
				_MQ_noreturn("update smart_bbs_info set bi_view_idx = '".$r['bi_view_idx']."' where bi_uid= '".$sortOrg['bi_uid']."'   ");


			}else if( $_sort == 'first'){

				// -- 가장 높은 순위를 가진 아이템 호출
				$sortOrg = _MQ("select  bi_uid,bi_view_idx from smart_bbs_info where bi_view_type = '".$r['bi_view_type']."' and bi_view_idx < '".$r['bi_view_idx']."' order by bi_view_idx asc limit 0,1 ");
				if( count($sortOrg) < 1){ error_msg("맨 처음입니다."); }

				// -- 기존 idx보다 높은값들을 + 1로 증가
				_MQ_noreturn("update smart_bbs_info set bi_view_idx = (bi_view_idx + 1)  where bi_view_type = '".$r['bi_view_type']."' and bi_view_idx < '".$r['bi_view_idx']."'   ");

				// -- 해당 아이템을 1순위로 변경
				_MQ_noreturn("update smart_bbs_info set bi_view_idx = '".$sortOrg['bi_view_idx']."' where bi_uid = '".$r['bi_uid']."'   ");
			}else if( $_sort == 'last'){

				// -- 가장 낮은 순위를 가진 아이템 호출
				$sortOrg = _MQ("select bi_uid,bi_view_idx from smart_bbs_info where bi_view_type = '".$r['bi_view_type']."' and bi_view_idx > '".$r['bi_view_idx']."' order by bi_view_idx desc limit 0,1  ");
				if( count($sortOrg) < 1){ error_msg("맨 마지막입니다."); }

				// -- 기존 idx보다 낮은값들을 - 1로 감소
				_MQ_noreturn("update smart_bbs_info set bi_view_idx = (bi_view_idx - 1)  where bi_view_type = '".$r['bi_view_type']."' and bi_view_idx > '".$r['bi_view_idx']."'   ");

				// -- 해당 아이템을 마지막 순위로 변경
				_MQ_noreturn("update smart_bbs_info set bi_view_idx = '".$sortOrg['bi_view_idx']."' where bi_uid = '".$r['bi_uid']."'   ");
			}

			error_loc("_bbs.board.list.php?".enc('d' , $_PVSC ));

		break;

	}
	// - 모드별 처리 ---
	exit;
?>