<?php

	defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
	actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



	// - 넘길 변수 설정하기 ---
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { IF($key <> "listpg"){$_PVS .= "&$key=$val";} }
	$_PVSC = enc('e' , $_PVS);
	// - 넘길 변수 설정하기 ---




	// 전체 브랜드 초성별 분류
	$arr_brand_prefix = array();
	$app_brand_name = '';
	$res = _MQ_assoc("select * from smart_brand where c_view = 'Y' order by c_name asc ");
	foreach( $res as $k=>$v ){
		// 한글과 영문이 아닌 경우 - 무조건 기타로 보냄
		$str_asi = @ord($v['c_prefix_str']);
		$v['c_prefix_str'] =  ($str_asi == '227' || ($str_asi >= 65 &&$str_asi <= 90)) ? $v['c_prefix_str'] : '기타';
		$arr_brand_prefix[$v['c_prefix_str']][$v['c_uid']] = $v['c_name'];

		if( $uid == $v['c_uid'] ) {
			$app_brand_name = $v['c_name'];
			$brand_prefix = $v['c_prefix_str'];
		}
	}
	$app_brand_name ? $app_brand_name : "전체";






	// 브랜드 - 상품정보 추출
	if( $uid ) {

		// ---- 모바일일 경우 ----
		IF(is_mobile())  {
			$listmaxcount = (isset($listmaxcount)?$listmaxcount:18); // 페이지당 갯수
		}
		// ---- 모바일일 경우 ----
		// ---- PC일 경우 ----
		else {

			$listmaxcount = (isset($listmaxcount)?$listmaxcount:20); // 페이지당 갯수
		}
		// ---- PC일 경우 ----

		$s_query = " where  p_brand = '". addslashes(htmlspecialchars($uid)) ."' and p_view = 'Y' ";


		if(empty($listpg) || $listpg == '') $listpg = 1;
		$count = $listpg*$listmaxcount-$listmaxcount;

		$res = _MQ(" select count(*) as cnt from smart_product as p " . $s_query ." ");
		$TotalCount = $res['cnt'];
		$Page = ceil($TotalCount/$listmaxcount);

		// 정렬방식
		switch($_order) {
			case 'price_asc': $s_order = ' order by p.p_price asc, p.p_idx asc '; break; // 가격 낮은순
			case 'price_desc': $s_order = ' order by p.p_price desc, p.p_idx asc '; break; // 가격 높은순
			case 'pname': $s_order = ' order by p.p_name asc, p.p_idx asc '; break; // 상품이름순(abc..)
			case 'date': $s_order = ' order by p.p_rdate desc, p.p_idx asc '; break; // 등록일 순
			case 'sale': $s_order = ' order by p.p_salecnt desc, p.p_idx asc '; break; // 판매순(인기순)
			default: $s_order = ' order by p.p_idx asc '; break; // 기본 설정 정렬 순
		}
		if(isset($order_field) && $order_field != '') { // 사용자 지정 정렬 방식이 있다면
			if(empty($order_sort) || $order_sort == '') $order_sort = 'asc'; // 정령 방식이 없다면 기본값 asc
			$s_order = " order by {$order_field} {$order_sort}  ";
		}

		$p_que = "
			select p.*
			from smart_product as p
			". $s_query ."
			". $s_order ."
			limit " . $count . ", " . $listmaxcount . "
		";
		$p_res = _MQ_assoc($p_que);


		// 상품 기본 아이콘
		$product_icon = get_product_icon_info('product_name_small_icon');

		// 자동적용아이콘 - 상품쿠폰
		$coupon_icon = get_product_icon_info('product_coupon_small_icon');
		$_tmp_arr = array('pc'=>get_img_src($coupon_icon[0]['pi_img'],IMG_DIR_ICON), 'mo'=>get_img_src($coupon_icon[0]['pi_img_m'],IMG_DIR_ICON));
		$coupon_icon_src = is_mobile() ? ($_tmp_arr['mo'] ? $_tmp_arr['mo'] : $_tmp_arr['pc']) : ($_tmp_arr['pc'] ? $_tmp_arr['pc'] : $_tmp_arr['mo']);

		// 자동적용아이콘 - 무료쿠폰
		$freedelivery_icon = get_product_icon_info('product_freedelivery_small_icon');
		$_tmp_arr = array('pc'=>get_img_src($freedelivery_icon[0]['pi_img'],IMG_DIR_ICON), 'mo'=>get_img_src($freedelivery_icon[0]['pi_img_m'],IMG_DIR_ICON));
		$freedelivery_icon_src = is_mobile() ? ($_tmp_arr['mo'] ? $_tmp_arr['mo'] : $_tmp_arr['pc']) : ($_tmp_arr['pc'] ? $_tmp_arr['pc'] : $_tmp_arr['mo']);

		// 자동적용아이콘 - 기획전
		$promotion_icon = get_product_icon_info('product_promotion_small_icon');
		$_tmp_arr = array('pc'=>get_img_src($promotion_icon[0]['pi_img'],IMG_DIR_ICON), 'mo'=>get_img_src($promotion_icon[0]['pi_img_m'],IMG_DIR_ICON));
		$promotion_icon_src = is_mobile() ? ($_tmp_arr['mo'] ? $_tmp_arr['mo'] : $_tmp_arr['pc']) : ($_tmp_arr['pc'] ? $_tmp_arr['pc'] : $_tmp_arr['mo']);

		// 기획전 상품에 기획전 아이콘 적용을 위한 배열 추출
		$que_promotion_pcode = "
			SELECT
				ppps.ppps_pcode
			FROM  smart_promotion_plan as pp
			INNER JOIN smart_promotion_plan_product_setup AS ppps ON ( ppps.ppps_ppuid = pp.pp_uid )
			WHERE
				pp.pp_view = 'Y' AND
				CURDATE() BETWEEN pp.pp_sdate AND pp.pp_edate
		";
		$res_promotion_pcode = _MQ_assoc($que_promotion_pcode);
		foreach($res_promotion_pcode as $ppk => $ppv){
			$arr_promotion_pcode[$ppv['ppps_pcode']]++;
		}

	}


	include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 스킨 호출
	actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행