<?php
	include_once "./inc.php";


	if( $_uid != ''){ $r = _MQ(" select * from smart_bbs where b_uid='{$_uid}' ");}
	if( $_menu == ''){ $_menu = $r['b_menu'];  }
	$boardInfo = get_board_info($_menu); // 게시판 정보 호출
	$shopAdminInfo = shopAdminInfo(); // 쇼핑몰 관리자 계정정보 호출 :: smart_individual and level is 9

	// -- 공통처리
	if( in_array($_mode, array('modify','add','reply')) == true){

			// -- 게시글 권한
			$getBoardAuth = boardAuthChkAll($_menu);

			// --사전 체크 ---
			$_menu = nullchk($_menu , "게시판을 선택해 주세요." );
			$_title = nullchk($_title , "제목을 입력해 주세요." );
			$_writer = nullchk($_writer , "작성자를 입력해 주세요." );
			$_content = nullchk($_content , "내용을 입력해 주세요." );

			// 2019-03-05 SSJ :: 네이버 에디터 동영상 사이즈 제어를 위해 iframe 태그가 있으면 div.iframe_wrap 으로 감싸기
			$_content = wrap_tag_iframe($_content);
			// 2019-12-04 SSJ :: 이미지 alt 속성 자동추가
			$_content = set_img_alter($_content, $_title);

			$_title = mysql_real_escape_string($_title);
			$_content = mysql_real_escape_string($_content);

			$_notice = $_notice != 'Y' ? '' : 'Y'; // 공지사항체크 유무
			$_secret = $_secret != 'Y' ? 'N': 'Y'; // 비밀글체크 유무

			$_img1_name = _PhotoPro($_SERVER['DOCUMENT_ROOT'].IMG_DIR_BOARD , "_img1" );
			$_img2_name = _PhotoPro($_SERVER['DOCUMENT_ROOT'].IMG_DIR_BOARD , "_img2" );

			// -- 파일첨부 확인
			$sque  = "	b_menu = '".$_menu."' ";
			$sque .= " ,b_writer = '".$_writer."' ";
			//$sque .= " ,b_pcode = '".$_pcode."' ";
			$sque .= " ,b_title = '".$_title."' ";
			$sque .= " ,b_content = '".$_content."' ";
			$sque .= " ,b_notice = '".$_notice."' ";
			$sque .= " ,b_secret = '".$_secret."' ";

			if( $_img1_name != ''){ $sque .= " ,b_img1 = '".$_img1_name."' "; }
			if( $_img2_name != ''){ $sque .= " ,b_img2 = '".$_img2_name."' "; }

			$sque .= " , b_reginfo_ip = '".$_SERVER['REMOTE_ADDR']."' ";
			$sque .= " , b_editor_use = '".($getBoardAuth['editor'] === true ? 'Y' : 'N' )."' ";

			// -- 시작일 종료일 있을경우에만 저장
			if( $_sdate != '' && $_edate != ''){
				$sque .= " ,b_sdate = '". $_sdate ."' ";
				$sque .= " ,b_edate = '". $_edate ."' ";
			}

			// KAY :: 게시판 카테고리설정
			$sque .= " ,b_category = '".$_category."' ";
	}


	switch($_mode){
		case "add": // 추가


			$sque .= " , b_inid = '".$shopAdminInfo['in_id']."' ";
			$sque .= " , b_writer_type = 'admin'  ";
			$sque .= " , b_rdate = now() ";

			_MQ_noreturn(" insert smart_bbs set ".$sque);
			$_uid = mysql_insert_id();

			odtFileUpload('addFile','smart_bbs',$_uid); // 파일첨부

			update_board_post_cnt($_menu); // 게시글 개수 업데이트
			// KAY :: 에디터 이미지 관리 :: 에디터 이미지 등록함수 :: 2021-06-02 ------------
			editor_img_ex($_content , 'board' , $_uid);
			error_loc("_bbs.post_mng.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
		break;

		case "reply": // 답글

			// -- qna 와 같이 답글이 무조건 한개인 글에서는 수정일 수도 있으니 체크한다.
			$rowReply = _MQ(" select *from smart_bbs as b left join smart_bbs_info as bi on(bi.bi_uid = b.b_menu)  where b_relation = '".$_relation."' and b_depth = 2 and bi_list_type = 'qna'  ");
			if( count($rowReply) > 0 ){ // 있다면 답글 내용 업데이트
				_MQ_noreturn(" update smart_bbs set ".$sque." where b_uid = '".$rowReply['b_uid']."'  ");

				odtFileUpload('addFile','smart_bbs',$rowReply['b_uid']); // 파일첨부
				odtFileUpload('modifyFile','smart_bbs',$rowReply['b_uid']); // 파일첨부

			}else{ // 없다면 추가
				$sque .= " , b_depth = '2'  ";
				$sque .= " , b_relation = '".$_relation."'  ";
				$sque .= " , b_inid = '".$shopAdminInfo['in_id']."' ";
				$sque .= " , b_writer_type = 'admin'  ";
				$sque .= " , b_rdate = now() ";
				_MQ_noreturn(" insert smart_bbs set ".$sque);
				$_uid = mysql_insert_id();
				odtFileUpload('addFile','smart_bbs',$_uid); // 파일첨부
			}

			update_board_post_cnt($_menu); // 게시글 개수 업데이트

			// -- 게시글 뷰로 이동
			error_loc("_bbs.post_mng.view.php?_uid=${_uid}&_PVSC=".$_PVSC);
		break;

		case "modify": // 수정

			_MQ_noreturn(" update smart_bbs set ".$sque." where b_uid = '".$_uid."'  ");

			odtFileUpload('addFile','smart_bbs',$_uid); // 파일첨부
			odtFileUpload('modifyFile','smart_bbs',$_uid); // 파일첨부
			// KAY :: 에디터 이미지 관리 :: 에디터 이미지 등록함수 :: 2021-06-02 ------------
			editor_img_ex($_content , 'board' , $_uid);

			error_loc("_bbs.post_mng.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");

		break;

		// -- 선택삭제와 일반삭제를 같이 처리한다.
		case "selectDelete": // 선택삭제
		case "delete": // 삭제

			if( $_mode == 'delete'){ $chkVar[] = $_uid; }
			if( count($chkVar)  < 1 ){ error_msg("삭제할 게시글이 존재하지 않습니다."); }
			$res = _MQ_assoc("select b_uid from smart_bbs where find_in_set(b_uid, '".implode(",",$chkVar)."' )  order by b_uid desc ");

			// -- 답글합산, 답글이 있는지 체킹하여 저장 :: 삭제 시 1뎁스에 속한 데이터도 삭제하기 위함
			foreach($res as $k=>$v){
				if( empty($_menu) == true ){ $_menu = $v['b_menu']; } // 게시글 업데이트를 위한처리
				if( $v['b_depth'] > 1 ){ continue; }

				$resBoardReply = _MQ_assoc("select b_uid from smart_bbs where b_depth > 1 and b_relation = '".$v['b_uid']."' ");
				if(  count($resBoardReply) > 0){ foreach($resBoardReply as $sk=>$sv){ $chkVar[] = $sv['b_uid']; } }
			}

			// -- 게시글 첨부이미지 삭제
			$resBoard = _MQ_assoc("select b_img1, b_img2 from smart_bbs where find_in_set(b_uid, '".implode(",",$chkVar)."' )  order by b_uid desc ");
			foreach($resBoard as $k=>$v){
				_PhotoDel($_SERVER['DOCUMENT_ROOT']."/upfiles/board" , $v[b_img1]);
				_PhotoDel($_SERVER['DOCUMENT_ROOT']."/upfiles/board" , $v[b_img2]);
			}

			// -- 파일삭제(데이터,첨부파일)
			$resBoardFiles = _MQ_assoc("select f_realname from smart_files where find_in_set(f_table_uid, '".implode(",",$chkVar)."' ) > 0 and f_table = 'smart_bbs'   ");
			foreach($resBoardFiles as $k=>$v){
				deleteFiles($v['f_realname']);
			}

			// KAY :: 에디터 이미지 관리 :: 에디터 이미지 사용관리 DB삭제, 파일관리 사용개수 업데이트 :: 2021-07-07
			editor_img_del($chkVar,'board');

			// -- 게시글,댓글,파일 db 데이터 삭제
			_MQ_noreturn("delete  from smart_bbs where  find_in_set(b_uid, '".implode(",",$chkVar)."' ) > 0 "); // 게시글
			_MQ_noreturn("delete  from smart_bbs_comment where find_in_set(bt_buid, '".implode(",",$chkVar)."' ) > 0 "); // 댓글
			_MQ_noreturn("delete  from smart_files where  find_in_set(f_table_uid, '".implode(",",$chkVar)."' ) > 0 and f_table = 'smart_bbs'  "); //파일, 과부하가 발생할 수 있으니 한번에 삭제


			update_board_post_cnt($_menu); // 게시글 개수 업데이트
			error_loc("_bbs.post_mng.list.php?".enc('d' , $_PVSC ));

		break;
	}






?>