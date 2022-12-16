<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



if( in_array($_mode , array("add","delete" )) ){
	member_chk();// 로그인 체크는 등록 / 삭제시에만 적용됨
}

// 모드별 처리
switch( $_mode ){

	// - 상품 토크 등록 ---
	case "add":

		$que = "
			insert smart_product_talk set
				pt_type			= '".$arr_p_talk_type[$talk_type]."'
				,pt_pcode		= '". $pcode ."'
				,pt_inid		= '".get_userid()."'
				,pt_writer		= '".$mem_info[in_name]."'
				,pt_title		= '". $_title ."'
				,pt_content		= '".$_content."'
				,pt_depth		= 1
				,pt_relation	= 0
				,pt_rdate		= now()
		";
		_MQ_noreturn($que);

        // -- 2019-04-09 SSJ :: 상품문의 등록 시 문자 연동 ----
        $p_name = _MQ_result(" select p_name from smart_product where p_code = '{$pcode}' ");// 상품명추출
        $sms_to = $mem_info['in_tel2'];
        $stringsAdd = array('{회원명}'=>$mem_info['in_name'], '{후기(문의)상품명}'=>$p_name, '{후기(문의)타이틀}'=>$_title);
        shop_send_sms($sms_to,"product_talk",$stringsAdd);
        // -- 2019-04-09 SSJ :: 상품문의 등록 시 문자 연동 ----

		echo "<script>parent.qna_view();parent.iframe_init_qna(false);</script>";

	break;
	// - 상품 댓글 등록 ---


	// - 상품 댓글 삭제 ---
	case "delete":
		$uid = nullchk($uid , "잘못된 접근입니다." , "" , "ALT");

		// 등록 상품 댓글 확인
		$r = _MQ(" select count(*) as cnt from smart_product_talk where pt_uid = '".$uid."' and pt_inid = '".get_userid()."' ");
		if( $r[cnt] == 0 ) {
			echo "no data";//error_alt("등록하신 글이 아닙니다.");
			exit;
		}

		// 댓글있는 상품 댓글인지 확인
		$r = _MQ(" select count(*) as cnt from smart_product_talk where pt_relation = '".$uid."' ");
		if( $r[cnt] > 0 ) {
			echo "is reply";//error_alt("댓글이 있으므로 삭제가 불가합니다.");
			exit;
		}

		$que = " delete from smart_product_talk where pt_uid = '".$uid."' and pt_inid='".get_userid()."' ";
		_MQ_noreturn($que);
	break;
	// - 상품 댓글 삭제 ---



	// - 댓글 갯수 추출 ---
	case "getcnt":

		echo "(".get_talk_total($pcode,"qna","normal").")";

	break;


	// - 상품 댓글 보기 ---
	case "view":

		$s_query = "from smart_product_talk as pt where pt_depth=1 and pt_type='".$arr_p_talk_type[$talk_type]."' and pt_pcode = '" . $pcode . "' ";

		// 페이징을 위한 작업
		$listmaxcount = is_mobile() ? 5 : 8; // $view_cnt
		$listpg = $listpg ? $listpg : 1; // $page_num
		$count = ($listpg-1) * $listmaxcount; // $limit_start_num
		$res = _MQ("select count(*) as cnt ".$s_query);
		$TotalCount = $res[cnt];
		$Page = $TotalCount ? ceil($TotalCount / $listmaxcount) : 1;
		$page_num = $TotalCount-$count;

		// - 상품 댓글 목록 ---
		$que = "
			select
				pt.*
			".$s_query."
			order by pt_rdate desc limit  $count , $listmaxcount
		";
		$res = _MQ_assoc($que);

		include_once($SkinData['skin_root'].'/product.qna.view.php'); // 스킨폴더에서 해당 파일 호출
	break;
}




actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행