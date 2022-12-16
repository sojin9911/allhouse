<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

$app_path = dirname(__FILE__)."/..".IMG_DIR_PRODUCT;

if( in_array($_mode , array("add","delete" )) ){
	member_chk('Y');// 로그인 체크는 등록 / 삭제시에만 적용됨

	if( $_FILES['_img']['name'] ) {
		$_img_name = _PhotoPro( $app_path , '_img' , 'alt');
		// -- SSJ : 상품후기 이미지 리사이즈 및 회전방지 : 2021-05-24 ----
		curl_async('http://'.$system['host'].OD_PROGRAM_DIR.'/app.resize_img.php?_img='.$_img_name.'&_path='.$app_path);
	}
}

// 모드별 처리
switch( $_mode ){

	// - 상품 토크 등록 ---
	case "add":

		// 2019-02-18 SSJ :: 관리자 설정에 따라 상품을 구매한 회원만 후기 작성 가능
		$trigger_point = false; // 포인트 지급 체크
		if( true ) { // SSJ : 상품후기 작성제한 기능강화 : 2020-08-13 : 모든 상품후기 설정 적용
			// 포토후기 작성횟수 추출
			$talk_type = 'eval'; // 상품후기
			$que = "
				select count(*) as cnt
				from smart_product_talk
				where 1
					and pt_type = '".$arr_p_talk_type[$talk_type]."'
					and pt_pcode = '". $pcode ."'
					and pt_inid = '".get_userid()."'
					and pt_depth = 1
			"; // SSJ : 상품후기 작성제한 기능강화 : 2020-08-13 : 모든 상품후기 설정 적용
			$er = _MQ($que);

			if($siteInfo['s_producteval_limit']<>'N'){
				// 구매내역 추출
				$que = "
					select count(*) as cnt
					from smart_order as o
					left join smart_order_product as op on (o.o_ordernum = op.op_oordernum)
					where 1
						and o.o_memtype = 'Y'
						and o.o_mid = '". get_userid() ."'
						and o.o_paystatus = 'Y'
						and o.o_canceled = 'N'
						and op.op_pcode = '". $pcode ."'
						and op.op_cancel = 'N'
				";
				$or = _MQ($que);

				if($or['cnt'] < 1){
					error_alt('상품후기는 상품을 구매한 회원만 작성 가능합니다.'); // SSJ : 상품후기 작성제한 기능강화 : 2020-08-13 : 포토후기 문구를 상품후기로 변경
				}
				else if($siteInfo['s_producteval_limit']=='B'){
					if($or['cnt'] <= $er['cnt']){
						error_alt('상품후기는 상품을 구매한 횟수 만큼만 등록할 수 있습니다.'); // SSJ : 상품후기 작성제한 기능강화 : 2020-08-13 : 포토후기 문구를 상품후기로 변경
					}
				}
			}

            if($_img_name){ // SSJ : 상품후기 작성제한 기능강화 : 2020-08-13 : 포토후기일 경우에만 포인트 지급
			    // 포인트 지급 체크
                if($siteInfo['s_producteval_limit']=='B') $trigger_point = true; // 구매한 횟수만큼 등록가능 시 항상 포인트 지급
                else if($er['cnt'] == 0) $trigger_point = true; // 그외 최초 등록시 포인트 지급
            }

		}

		$que = "
			insert smart_product_talk set
				pt_type			= '".$arr_p_talk_type[$talk_type]."'
				,pt_pcode		= '". $pcode ."'
				,pt_inid		= '".get_userid()."'
				,pt_writer		= '".$mem_info[in_name]."'
				,pt_title		= '". $_title ."'
				,pt_content		= '".$_content."'
				,pt_eval_point	= '".$_eval_point."'
				,pt_depth		= 1
				,pt_relation	= 0
				,pt_rdate		= now()
				,pt_img			= '".$_img_name."'
		";
		_MQ_noreturn($que);

		if( $trigger_point ) {
			shop_pointlog_insert( get_userid() , "포토후기 등록 (상품코드: {$pcode})" , $siteInfo[s_productevalpoint] , "N" , $siteInfo[s_productevalprodate]);
		}

        // -- 2019-04-09 SSJ :: 상품후기 등록 시 문자 연동 ----
        $p_name = _MQ_result(" select p_name from smart_product where p_code = '{$pcode}' ");// 상품명추출
        $sms_to = $mem_info['in_tel2'];
        $stringsAdd = array('{회원명}'=>$mem_info['in_name'], '{후기(문의)상품명}'=>$p_name, '{후기(문의)타이틀}'=>$_title);
        shop_send_sms($sms_to,"product_review",$stringsAdd);
        // -- 2019-04-09 SSJ :: 상품후기 등록 시 문자 연동 ----

		echo "<script>parent.eval_frm.reset();parent.eval_view();parent.iframe_init(false);</script>";

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

		$tmp_r = _MQ(" select pt_img, pt_pcode, pt_rdate from smart_product_talk where pt_uid = '".$uid."' and pt_inid = '".get_userid()."' ");
		if( $tmp_r[pt_img] ) {
			_PhotoDel( $app_path , $tmp_r[pt_img] );

			// 2019-02-18 SSJ :: 관리자 설정에 따라 상품을 구매한 회원만 후기 작성 가능
			// 포토 후기 등록 개수
			$talk_type = 'eval'; // 상품후기
			$que = "
				select count(*) as cnt
				from smart_product_talk
				where 1
					and pt_type = '".$arr_p_talk_type[$talk_type]."'
					and pt_pcode = '". $tmp_r['pt_pcode'] ."'
					and pt_inid = '".get_userid()."'
					and pt_depth = 1
					and pt_img != ''
				order by pt_uid asc
			";
			$er = _MQ($que);
			// 적립일 체크 , true - 지급완료
			$point_date = date('Y-m-d', strtotime('+'. $siteInfo['s_productevalprodate'] .' days', strtotime($tmp_r['pt_rdate'])));
			$trigger_date = date('Y-m-d') >= $point_date;
			$trigger_point = false;
			$point_days = (strtotime(date('Y-m-d')) - strtotime($point_date)) / (60*60*24);
			if($siteInfo['s_producteval_limit']<>'B'){
				if($er['cnt'] == 1) $trigger_point = true;
			}else{
				$trigger_point = true;
			}
			if($trigger_point){
				if($trigger_date) shop_pointlog_insert( get_userid() , "포토후기 삭제 (상품코드: ".$tmp_r['pt_pcode'].")" , $siteInfo['s_productevalpoint']*-1 , "N" , 0);
				else shop_pointlog_insert( get_userid() , "포토후기 삭제 (상품코드: ".$tmp_r['pt_pcode'].")" , $siteInfo['s_productevalpoint']*-1 , "N" , $point_days);
			}
		}

		$que = " delete from smart_product_talk where pt_uid = '".$uid."' and pt_inid='".get_userid()."' ";
		_MQ_noreturn($que);
		break;
	// - 상품 댓글 삭제 ---


	// - 댓글 갯수 추출 ---
	case "getcnt":

		echo "(".get_talk_total($pcode,"eval","normal").")";

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

		include_once($SkinData['skin_root'].'/product.eval.view.php'); // 스킨폴더에서 해당 파일 호출
	break;
}

actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행