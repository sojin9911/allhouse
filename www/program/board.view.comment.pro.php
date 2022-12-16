<?php
	# 게시판 댓글등록
	include_once(dirname(__FILE__).'/inc.php');
	actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

	if( in_array($ajaxMode,array('add','delete')) == true){
		parse_str($formData);
		if( $_buid == ''){ echo json_encode(array('rst'=>'fail','msg'=>'잘못된 접근입니다.')); exit; }
		if( $_menu == ''){ $postInfo = get_post_info($_buid); $_menu = $postInfo['b_menu']; }
	}

	switch($ajaxMode)
	{
		case "add": // 추가

			if( $_content == ''){ echo json_encode(array('rst'=>'fail','msg'=>'댓글을 입력해 주세요.')); exit; }
			if( mb_strlen($_content,'utf8') > $varCommentWriteLen ){ echo json_encode(array('rst'=>'fail','msg'=>'댓글은 500자 이내로 입력해 주세요.'.mb_strlen($_content))); exit; } 

			// -- 권한체크
			$writeAuth = boardAuthChk($_menu,'comment'); // 댓글에 대한 쓰기권한을 가져온다. 
			if($writeAuth !== true){ echo json_encode(array('rst'=>'fail','msg'=>'댓글등록에 실패하였습니다.(권한이 없습니다.)')); exit; }

			$_content = addslashes(htmlspecialchars($_content));

			// -- 관리자로 로그인 중이고, 현재 사이트에서는 로그인이 안되었을 시 사이트 관리자 계정정보 호출
			if( is_admin() === true && is_login() !== true){ 
				$mem_info = shopAdminInfo(); // 쇼핑몰 관리자 계정정보 호출 :: smart_individual and level is 9
			 }

			$que = "
				insert smart_bbs_comment set
					 bt_buid = '". $_buid ."'
					,bt_inid = '".$mem_info['in_id']."'
					,bt_writer = '".$mem_info[in_name]."'
					,bt_content = '".$_content."'
					,bt_reginfo_ip = '".$_SERVER['REMOTE_ADDR']."'
					,bt_rdate = now()					
			";
			_MQ_noreturn($que);

			update_board_comment_cnt ( $_buid ); // 게시판 덧글 갯수 업데이트
			echo json_encode(array('rst'=>'success')); exit;		

		break;

		case "delete": // 삭제
			$row = _MQ("select *from smart_bbs_comment where bt_uid = '".$_uid."' ");
			if( count($row)< 1){ echo json_encode(array('rst'=>'fail','msg'=>'삭제할 댓글이 존재하지 않습니다.')); exit; }
			if( $row['bt_inid'] != get_userid() && is_admin() !== true ){ echo json_encode(array('rst'=>'fail','msg'=>'삭제하실 댓글이 본인의 댓글이 아닙니다.')); exit; }
			_MQ_noreturn("delete from smart_bbs_comment where bt_uid = '".$_uid."' ");
			update_board_comment_cnt ( $_buid ); // 게시판 덧글 갯수 업데이트
			echo json_encode(array('rst'=>'success')); exit;		
		break;
	}


?>


<?php

// 받은 변수
//		- _mode ==> add
//		- _buid
//		- bbs_talk_content

return; 
// 게시판 덧글 갯수 업데이트
function bbs_cnt ( $buid ){
	$r = _MQ("select count(*) as cnt from smart_bbs_comment where bt_buid='" . $buid . "' ");
	_MQ_noreturn(" update smart_bbs set b_talkcnt = '".$r[cnt]."' where b_uid='". $buid ."' ");
}



if( in_array($_mode , array("add","delete")) ){
	member_chk();// 로그인 체크는 등록 / 삭제시에만 적용됨
}

// 모드별 처리
switch( $_mode ){

	// - 덧글 등록 ---
	case "add":
		$_buid = nullchk($_buid , "잘못된 접근입니다." , "" , "ALT");
		$bbs_talk_content = nullchk($bbs_talk_content , "댓글글을 등록해주시기 바랍니다." , "" , "ALT");
		$que = "
			insert smart_bbs_comment set
				 bt_buid = '". $_buid ."'
				,bt_rdate = now()
				,bt_inid = '".get_userid()."'
				,bt_writer = '".$mem_info[in_name]."'
				,bt_content = '".$bbs_talk_content."'
		";
		_MQ_noreturn($que);

		// 게시판 덧글 갯수 업데이트
		bbs_cnt ( $_buid );

		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
	break;
	// - 덧글 등록 ---

	// - 덧글 삭제 ---
	case "delete":
		$uid = nullchk($uid , "잘못된 접근입니다." , "" , "ALT");

		// 등록 덧글 확인
		$r = _MQ(" select bt_buid , bt_inid from smart_bbs_comment where bt_uid = '".$uid."' ");
		if( $r[bt_inid] <> get_userid() ) {
			error_alt("등록하신 덧글이 아닙니다.");
		}

		$que = " delete from smart_bbs_comment where bt_uid = '".$uid."' and bt_inid='".get_userid()."' ";
		_MQ_noreturn($que);

		// 게시판 덧글 갯수 업데이트
		bbs_cnt ( $r[bt_buid] );
		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
	break;
	// - 덧글 삭제 ---


	// - 덧글 보기(싱글공감) ---
	case "view":
		$_buid = nullchk($_buid , "잘못된 접근입니다." , "" , "ALT");

		// 덧글 - cnt 추출
		$sr = _MQ(" select count(*) as cnt_bt from smart_bbs_comment where bt_buid='".$_buid."' ");
		$bbs_talk_cnt = $sr[cnt_bt]; // 덧글 총수

		// - 덧글 목록 ---
		echo "<div class='comment_list'><ul>";

		$btr = _MQ_assoc("  select * from smart_bbs_comment  where bt_buid='{$_buid}' order by bt_uid desc ");
		foreach( $btr as $k=>$v ){
			if($v[bt_inid] == get_userid() && is_login()) $del_button = "<a href='javascript:bbs_talk_del(".$_buid." , ".$v[bt_uid].")' class='btn_delete' title='삭제' ></a>"; else  $del_button="";
			echo "
					<li>
						<span class='name'>".$v[bt_writer]."</span>
						<span class='id'>".$v[bt_inid]."</span>
						<span class='bar'></span>
						<span class='date'>(".date('y.m.d H:i',strtotime($v[bt_rdate])).")</span>
						".$del_button."
						<div class='conts'>".nl2br(stripslashes(strip_tags($v[bt_content])))."</div>
					</li>
			";
		}
		echo "</ul></div>";
		// - 덧글 목록 ---
		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
	break;
	// - 덧글 보기(싱글공감) ---







	// - 추가사항:덧글 보기(후기게시판) ---
	case "view_post":
		$_buid = nullchk($_buid , "잘못된 접근입니다." , "" , "ALT");

		// 덧글 - cnt 추출
		$sr = _MQ(" select count(*) as cnt_bt from smart_bbs_comment where bt_buid='".$_buid."' ");
		$bbs_talk_cnt = $sr[cnt_bt]; // 덧글 총수

		// - 덧글 목록 ---



		$btr = _MQ_assoc(" select * from smart_bbs_comment  where bt_buid='{$_buid}'  order by bt_uid desc ");
		if($btr){
			echo "<td class='conts_reply' colspan='4'>";
		}
		foreach( $btr as $k=>$v ){//service_contents_area .board_comment_list
			if($v[bt_inid] == get_userid() && is_login()) {
				$del_button = "<span class='board_comment_list' style='border:0px solid;'>
									<a href='javascript:bbs_talk_del(".$_buid." , ".$v[bt_uid].")' class='btn_delete' title='삭제' style='position:relative;top:3px'></a>
								<span>";
			}else {
				$del_button="";
			}
				echo "
						<div class='admin'><span class='icon'><span class='ic_A'></span></span>".$v[bt_writer]." (답변일 : ".date('Y.m.d',strtotime($v[bt_rdate])).")".$del_button."</div>
						".nl2br(stripslashes(strip_tags($v[bt_content])))."
					";
			}
		if($btr){
			echo "</td>";
		}
		// - 덧글 목록 ---
		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
	break;
	// - 추가사항:덧글 보기(후기게시판) ---


	// - 추가사항: 개통후기게시판 관리자승인,비승인시 비밀글 해제 처리 ---
	case "postchk":

		$_postchk = nullchk($_postchk , "잘못된 접근입니다." , "" , "ALT");

		_MQ_noreturn(" update smart_bbs set b_secret = '".$_postchk."' where b_uid='".$_buid."' ");
		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
	break;
	// - 추가사항: 개통후기게시판 관리자승인,비승인시 비밀글 해제 처리 ---
}