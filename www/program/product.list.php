<?php
/*
	$_order: 기존 정의 된 정렬 방식
	$order_field: 사용자 정의 정렬 필드
		└> $order_sort: 사용자 정의 정렬 방식

	$cuid: 카테고리 코드
	$search_word: 검색어
	$search_hashtag: 해시태그 전용 검색어
	$query_from: 상품 테이블에 join시 사용
	$query_alias: alias가 있는경우 사용(기본값: *)
	$_event: 설정된 조건의 상품이 출력

	$Page: 페이지
	$listmaxcount: 페이지당 출력 개수

	$non_list_view: 리스트 출력여부 <평문 true(기본값), false> - 프로세스 결과 배열만 필요 한경우 false로 처리 후 $res변수 사용
	$_list_type: 사용자에 출력될 상품리스트 형태, 스킨 폴더에서 호출함 (기본: 스킨/product.list.php)
*/
//defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---


// 카테고리 정보(cuid가 있을 경우만 카테고리 정보 호출)
if(isset($cuid) && $cuid != '') $category_info = info_category($cuid);


//  검색어에서 html 제거
if(isset($search_word) && $search_word != '') $search_word = strip_tags($search_word); // 일반검색어
if(isset($search_hashtag) && $search_hashtag != '') $search_hashtag = strip_tags($search_hashtag); // 해시검색어


// 이벤트 리스트(이미 지정된 조건의 상품 리스트 출력)
if(isset($_event) && $_event != '') {

	$s_query = " and p_view = 'Y' and p_option_valid_chk = 'Y' "; // 쿼리 기본값(노출만 표기)
	if($_event == 'main_category_best') { // 메인 카테고리 베스트
		$query_from = " left join smart_product_category_best as pctb on(p.p_code = pctb.pctb_pcode) ";
		$s_query .= " and pctb_cuid = '{$cuid}' ";
		$order_field = 'pctb.pctb_idx ';
		$order_sort = 'asc';
	}
	else if($_event == 'main_md') { // 메인 MD
		$query_from = " left join smart_display_main_product as dmp on(p.p_code = dmp.dmp_pcode) ";
		$s_query .= " and dmp_dmsuid = '{$dmsuid}'  ";
		$order_field = 'dmp.dmp_idx ';
		$order_sort = 'asc';
		$listmaxcount = '9999';
	}
	else if($_event == 'main_product') { // 메인 기본 MD와 동일하나 리스트 페이지가 다르다
		$query_from = " left join smart_display_main_product as dmp on(p.p_code = dmp.dmp_pcode) ";
		$s_query .= " and dmp_dmsuid = '{$dmsuid}'  ";
		$order_field = 'dmp.dmp_idx ';
		$order_sort = 'asc';
		$listmaxcount = '9999';
	}
	else if($_event == 'type') { // 타입이벤트
		/*
			// -- LCY :: 타입 url 적용하는 방법 --- 참고
			$typeInUrl = '/?pn=product.list&_event=type&typeuid='.$row['dts_uid'];
			$typeFullUrl = $system['url'].$typeInUrl;
		*/
		$query_from = " left join smart_display_type_product as dtp on(p.p_code = dtp.dtp_pcode) ";
		$s_query .= " and dtp_dtsuid = '{$typeuid}'  ";
		$order_field = 'dtp.dtp_idx ';
		$order_sort = 'asc';
		$listmaxcount = '9999';

		// 탑네비 설정
		$TopEventInfo = _MQ(" select * from `smart_display_type_set` where (1) and dts_uid = '{$typeuid}' ");
		$ActiveCate['cuid'][0] = '&_event='.$_event.'&typeuid='.$typeuid;
		$ActiveCate['cname'][0] = $TopEventInfo['dts_name'];
	}

}
else {

	// 검색 설정
	$s_query = " and p_view = 'Y' and p_option_valid_chk = 'Y' "; // 쿼리 기본값(노출만 표기)
	if(isset($search_hashtag) && $search_hashtag != '') {// 검색 내 해시태그 추가할 경우
		$search_hashtag_arr = $ex = array_filter(explode("," , $search_hashtag));
		$arr_sub_s_query = "";
		foreach($ex as $k=>$v){
			if(trim($v)) {
				$arr_sub_s_query[] = " FIND_IN_SET( '" . trim($v) . "', p.p_hashtag ) > 0 ";
			}
		}
		if(sizeof($arr_sub_s_query) > 0 ) {
			$s_query .= " and ( ". implode(" or " , $arr_sub_s_query) ." ) ";
		}
	}
	if(isset($search_word) && $search_word != '') {
		$search_word = trim(stripslashes(htmlspecialchars($search_word))); // 검색키워드
		$search_keyword = $search_word;
		if(preg_match('/^#/',$search_keyword)) $search_type = 'hash'; // 해시태그(키워드로 들어왔다면)
		else $search_type = 'word'; // 일반 검색으로 들어왔다면

		// 일반 검색
		if($search_type == 'word') {
			$s_query .= " and ( ";
			$search_tmp = explode(' ',addslashes($search_keyword)); $s_query_array = array();
			foreach($search_tmp as $skk=>$skv) {
				$s_query_array[] = " replace(p_name,' ','') like '%".$skv."%' ";
			}
			$s_query .= implode(' or ',$s_query_array);
			$s_query .= " or p_code = '".addslashes($search_keyword)."' ";
			$s_query .= " ) ";
		}
		else if($search_type == 'hash') { // 가능한 search_hashtag를 통하여 검색(이방법은 1건의 해시태그만 검색 됩니다.)
			$search_word_str = preg_replace('/^#/','',$search_word);
			$s_query .=  " and  find_in_set('".$search_word_str."',p_hashtag) ";
		}
	}
	if(isset($cuid) && $cuid != '') { // 카테고리 검색
		$s_query .=  " and (select count(*) from smart_product_category as pct where pct.pct_pcode=p_code and find_in_set(pct_cuid,'".implode(",",get_descendant_cate($cuid))."')) > 0 ";
	}
}


// 정렬방식
switch($_order) {
	case 'price_asc': $s_order = ' order by p_price asc, p_idx asc '; break; // 가격 낮은순
	case 'price_desc': $s_order = ' order by p_price desc, p_idx asc '; break; // 가격 높은순
	case 'pname': $s_order = ' order by p_name asc, p_idx asc '; break; // 상품이름순(abc..)
	case 'date': $s_order = ' order by p_rdate desc, p_idx asc '; break; // 등록일 순
	case 'sale': $s_order = ' order by p_salecnt desc, p_idx asc '; break; // 판매순(인기순)
	default: $s_order = ' order by p_idx asc '; break; // 기본 설정 정렬 순
}
if( $_order == '' && isset($order_field) && $order_field != '') { // 사용자 지정 정렬 방식이 있다면
	if(empty($order_sort) || $order_sort == '') $order_sort = 'asc'; // 정령 방식이 없다면 기본값 asc
	$s_order = " order by {$order_field} {$order_sort}  ";
}

// 데이터 조회 및 페이지 계산
$listmaxcount = (isset($listmaxcount)?$listmaxcount:20); // 페이지당 갯수
if(is_mobile() && $_event == 'type') { // 모바일 타입이벤트 설정 적용 (3개씩 출력 모드에서 20개를 출력 하면 1개가 모자르다. 이를 보정하자 결과: 20->21)
	$curEventInfo = _MQ(" select * from `smart_display_type_set` where dts_uid = '". $typeuid ."' ");
	$listmaxcountFill = ( $curEventInfo['dts_list_product_mobile_display'] > 0  ? ($curEventInfo['dts_list_product_mobile_display']-($listmaxcount%$curEventInfo['dts_list_product_mobile_display'])) : 0 );
	if($listmaxcountFill > 0) $listmaxcount += $listmaxcountFill;
}else if(is_mobile() && !in_array($pn, array('', 'main'))) { // 모바일 상품 리스트 출력 개수 보정 (3개씩 출력 모드에서 20개를 출력 하면 1개가 모자르다. 이를 보정하자 결과: 20->21)
	$listmaxcountFill = ( $category_info['c_list_product_mobile_display'] > 0  ? ($category_info['c_list_product_mobile_display']-($listmaxcount%$category_info['c_list_product_mobile_display'])) : 0 );
	if($listmaxcountFill > 0) $listmaxcount += $listmaxcountFill;
}
if(empty($listpg) || $listpg == '') $listpg = 1;
$count = $listpg*$listmaxcount-$listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_product as p {$query_from} where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
/* SSJ : 상품 품절 체크 - p_soldout_chk 정보 업데이트 : 2019-02-11 */
$res = _MQ_assoc("
    select
        ".(isset($query_alias) && $query_alias != ''?$query_alias:'*')."
        ,if(p_soldout_chk='N',p_stock,0) as p_stock
    from
        smart_product as p {$query_from}
    where (1)
        {$s_query}
    {$s_order}
    ".(empty($_event) || $_event == ''?" limit {$count}, {$listmaxcount} ":null)."
"); // 이벤트 페이지라면 리미트 제거



// 카테고리 설정 숨김인경우
if(!is_mobile() && $category_info['c_list_product_view'] == 'N') {
	$res = array();
	$TotalCount = 0;
	$Page = 0;
}
if(is_mobile() && $category_info['c_list_product_mobile_view'] == 'N') {
	$res = array();
	$TotalCount = 0;
	$Page = 0;
}


// 해시태그 추출
$arr_hashtag = array();
if($siteInfo['s_product_list_hashtag_view'] == 'Y') { // 해시태그 설정이 노출인 경우만
	$hashtag_row = _MQ_assoc("
		select
			p.p_hashtag
		from
			smart_product as p {$query_from}
		where (1)
			{$s_query}
		group by p.p_hashtag
		{$s_order}
	");
	if(count($hashtag_row) <= 0) $hashtag_row = array();
	foreach($hashtag_row as $k=>$v){
		$ex = array_filter(explode("," , $v['p_hashtag']));
		foreach($ex as $sk=>$sv) {
			if(trim($sv) != '') $arr_hashtag[trim($sv)]++;
		}
	}
	if($TotalCount <= 0) $arr_hashtag = array();

	// 해시태그 랜덤 설정
	if($siteInfo['s_product_list_hashtag_shuffle'] == 'Y' && count($arr_hashtag) > 0) $arr_hashtag = shuffle_assoc($arr_hashtag);

	// 노출수 제한 설정
	if($siteInfo['s_product_list_hashtag_shuffle'] == 'Y' && $siteInfo['s_product_list_hashtag_cnt'] > 0) $arr_hashtag = array_slice($arr_hashtag, 0, $siteInfo['s_product_list_hashtag_cnt']);
}


// 상품 아이콘정보.
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




// 스킨정보
$SkinInfo = SkinInfo('all', 'auto', 'cookie');

// 사용자 출력 템플릿
if(empty($non_list_view) || $non_list_view == '') $non_list_view = 'true';
if($non_list_view == 'true') { // 리스트 출력 모드에서만 출력
	if(isset($_list_type)) include($SkinData['skin_root'].'/'.str_replace('.php', '', $_list_type).'.php'); // 스킨폴더에서 해당 파일 호출
	else include($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
}


actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행