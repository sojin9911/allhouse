<?php
// ★ product.list.php와는 별도 로직
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
$query_alias = ''; // score alias
$score_alias = array(); // 검색 스코어를 위한 배열 변수 준비


// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---


//  검색어에서 html 제거
if(isset($search_word) && $search_word != '') $search_word = strip_tags($search_word); // 일반검색어
if(isset($search_hashtag) && $search_hashtag != '') $search_hashtag = strip_tags($search_hashtag); // 해시검색어
if(isset($detail_search) && $detail_search != '') $detail_search = stripslashes(strip_tags($detail_search)); // 상세검색(결과 내 재검색)

// 전체 브랜드
$AllBrand = brand_info();


// 가격검색 리스트
/*
	- 스킨마다 필요한 것만 추출하여 사용
	- 만약 없다면 여기에 추가 후 사용자에서 사용
*/
$arr_price = array(
	'1만원 이하'=>'10000_lower',
	'2만원 이하'=>'20000_lower',
	'3만원 이하'=>'30000_lower',
	'4만원 이하'=>'40000_lower',
	'5만원 이하'=>'50000_lower',
	'6만원 이하'=>'60000_lower',
	'7만원 이하'=>'70000_lower',
	'8만원 이하'=>'80000_lower',
	'9만원 이하'=>'90000_lower',
	'10만원 이하'=>'100000_lower',
	'1만원 이상'=>'10000_upper',
	'2만원 이상'=>'20000_upper',
	'3만원 이상'=>'30000_upper',
	'4만원 이상'=>'40000_upper',
	'5만원 이상'=>'50000_upper',
	'6만원 이상'=>'60000_upper',
	'7만원 이상'=>'70000_upper',
	'8만원 이상'=>'80000_upper',
	'9만원 이상'=>'90000_upper',
	'10만원 이상'=>'100000_upper'
);


// 검색 설정
$s_query = " and p_view = 'Y' and p_option_valid_chk = 'Y' "; // 메인 검색 조건(검색어)(노출만 표기)
$sub_query = ''; // 서브 검색조건(결과 내 재검색, 카테고리, 브랜드, 해시태그, 가격대, 혜택구분)


// 검색 내 해시태그 추가할 경우
if($search_word != str_replace('#', '', $search_word)) {
	$TmpHash = explode('#', $search_word);
	$TmpHashArr = array();
	if(count($TmpHash) > 0) $TmpHash = array_values(array_filter($TmpHash));
	if(count($TmpHash) <= 0) $TmpHash = array();
	foreach($TmpHash as $hk=>$hv) {
		$TmpHashArr[] = $hv;
	}
	if(count($TmpHashArr) > 0){
		// -- 2019-07-23 SSJ :: 검색어로 해시테그 검색 시 선택한 해시테그가 초기화 되는 현상 수정 ----
		$ex_hash = explode(',', $search_hashtag);
		$TmpHashArr = array_unique(array_filter(array_merge($ex_hash, $TmpHashArr)));
		// -- 2019-07-23 SSJ :: 검색어로 해시테그 검색 시 선택한 해시테그가 초기화 되는 현상 수정 ----
		$search_hashtag = implode(',', $TmpHashArr);
	}
}

if(isset($search_hashtag) && $search_hashtag != '') {
	$search_hashtag_arr = $ex = array_filter(explode(',', $search_hashtag));
	$arr_sub_s_query = "";
	foreach($ex as $k=>$v){
		if(trim($v)) {
			$arr_sub_s_query[] = " find_in_set('" . trim($v) . "', p.p_hashtag) > 0 ";
		}
	}
	if(sizeof($arr_sub_s_query) > 0) $sub_query .= " and (". implode(" or " , $arr_sub_s_query) .") ";
}

// 검색어
if(isset($search_word) && $search_word != '') {
	$score_alias = array(); // 검색 스코어를 위한 배열 변수 준비
	$search_word = trim(htmlspecialchars($search_word)); // 검색키워드
	$search_keyword = $search_word;
	if(preg_match('/^#/',$search_keyword)) $search_type = 'hash'; // 해시태그(키워드로 들어왔다면)
	else $search_type = 'word'; // 일반 검색으로 들어왔다면

	// 일반 검색
	if($search_type == 'word') {
		$s_query .= " and ( ";
		$search_tmp = explode(' ',$search_keyword); $s_query_array = array();
		foreach($search_tmp as $skk=>$skv) {
			$skv = strtolower($skv);

			// 검색조건
			$s_query_array[] = " replace(lower(p_name),' ','') like '%{$skv}%' "; // 대표 상품명 검색
			$s_query_array[] = " replace(lower(p_subname),' ','') like '%{$skv}%' "; // 부가 상품명 검색

			// 스코어
			$score_alias[] = " if(lower(p_name) = replace(lower(p_name), '{$skv}', ''), 0, -1) "; // 대표 상품명 스코어(중요도에 따라 스코어 포인트를 조절하세요.)
			$score_alias[] = " if(lower(p_subname) = replace(lower(p_subname), '{$skv}', ''), 0, -1) "; // 부가 상품명 스코어(중요도에 따라 스코어 포인트를 조절하세요.)
		}
		$s_query .= implode(' or ',$s_query_array);
		$s_query .= " or p_code = '".$search_keyword."' ";
		$s_query .= " ) ";
	}
	else if($search_type == 'hash') { // 가능한 search_hashtag를 통하여 검색(이방법은 1건의 해시태그만 검색 됩니다.)
		$search_word_str = preg_replace('/^#/', '', $search_word);
		$sub_query .=  " and find_in_set('{$search_word_str}', p_hashtag) ";
	}
}


// 결과 내 재검색(상세검색)
if($detail_search != 'Y') $search_word_detail = ''; // 디테일 검색 모드가 아니면 디테일 검색어 초기화
if(isset($search_word_detail) && $search_word_detail != '') {
	$search_word_detail = trim(htmlspecialchars($search_word_detail)); // 검색키워드
	$s_query .= " and ( ";
	$dsearch_tmp = explode(' ',$search_word_detail); $ds_query_array = array();
	foreach($dsearch_tmp as $dskk=>$dskv) {
		$skv = strtolower($skv);

		// 검색조건
		$ds_query_array[] = " replace(lower(p_name), ' ', '') like '%{$dskv}%' ";
		$ds_query_array[] = " replace(lower(p_subname), ' ', '') like '%{$dskv}%' "; // 부가 상품명 검색

		// 스코어
		$score_alias[] = " if(lower(p_name) = replace(lower(p_name), '{$dskv}', ''), 0, -1) "; // 대표 상품명 스코어(중요도에 따라 스코어 포인트를 조절하세요.)
		$score_alias[] = " if(lower(p_subname) = replace(lower(p_subname), '{$dskv}', ''), 0, -1) "; // 부가 상품명 스코어(중요도에 따라 스코어 포인트를 조절하세요.)
	}
	$s_query .= implode(' or ',$ds_query_array);
	$s_query .= " or p_code = '".$search_word_detail."' ";
	$s_query .= " ) ";
}
if(count($score_alias) > 0) $query_alias .= '('.implode(' + ', $score_alias).') as score, p.* '; // 스코어가 있다면 alias에 추가


// 카테고리 검색
$ActiveCate = array(); // 검색된 카테고리의 정보
if(isset($cuid) && $cuid != '') {
	$sub_query .=  " and (select count(*) from smart_product_category as pct where pct.pct_pcode=p_code and find_in_set(pct_cuid,'".implode(",",get_descendant_cate($cuid))."')) > 0 ";

	// 검색된 카테고리 정보를 찾는다.
	$ct_info = info_category($cuid);
	$ActiveCate['cuid'] = array(
		$ct_info['depth1_cuid'],
		$ct_info['depth2_cuid'],
		$ct_info['depth3_cuid']
	);
	$ActiveCate['cname'] = array(
		$ct_info['depth1_cname'],
		$ct_info['depth2_cname'],
		$ct_info['depth3_cname']
	);
	if(count($ActiveCate) > 0 && count($ActiveCate['cuid']) > 0) $ActiveCate['cuid'] = array_filter($ActiveCate['cuid']); // 빈값제거
	if(count($ActiveCate) > 0 && count($ActiveCate['cname']) > 0) $ActiveCate['cname'] = array_filter($ActiveCate['cname']); // 빈값제거
}


// 브랜드 검색
if(isset($search_brand) && $search_brand != '') {
	$brand_flip = array_flip($AllBrand); // 전체 브랜드 변수의 index와 value를 flip한다.
	$search_brand_arr = array_filter(explode(',', $search_brand));
	$brand_arr_list = array();
	foreach($search_brand_arr as $bk=>$bv) {
		if(empty($brand_flip[$bv]) || !$brand_flip[$bv]) continue;
		$brand_arr_list[] = $brand_flip[$bv];
	}
	if(count($brand_arr_list) > 0) {
		$sub_query .= " and p_brand in ('".implode("', '", $brand_arr_list)."') ";
	}
}


// 혜택구분 검색
if(isset($search_boon) && $search_boon != '') {
	$search_boon_arr = array_filter(explode(',', $search_boon));
	if(count($search_boon_arr) <= 0) $search_boon_arr = array();
	$FreeDeliQuery = array();
	foreach($search_boon_arr as $bk=>$bv) {
		if(in_array($bv, array('무료배송', '배송비 무료', '무료 배송비'))) {

			// 무료배송에 해당되는 업체 추출
			$DeliveryFreeCompany = array();
			$SiteFree = array();
			$ComFree = array();
			if($siteInfo['s_delprice'] <= 0) $SiteFree = _MQ_assoc(" select cp_id from smart_company where cp_delivery_use = 'N' "); // 사이트설정 무료배송 업체
			$ComFree = _MQ_assoc(" select cp_id from smart_company where cp_delivery_use = 'Y' and cp_delivery_price <= 0 "); // 입점업체 중 무료 배송 업체
			if(count($SiteFree) > 0) $ComFree = array_merge($SiteFree, $ComFree); // 사이트설정 무료배송 업체 + 무료배송업체
			if(count($ComFree) <= 0) $ComFree = array();
			foreach($ComFree as $cfk=>$cfv) {
				$DeliveryFreeCompany[] = $cfv['cp_id'];
			}

			// 무료배송 쿼리문 추가
			$FreeDeliQuery[] = "
				if(
					(
						(p_shoppingPay_use = 'Y' && p_shoppingPay <= 0) || /* 개별배송비를 설정하고 배송비가 0원인 경우 */
						(p_shoppingPay_use = 'N' and p_cpid in('".implode("', '", array_values($DeliveryFreeCompany))."')) || /* 입점배송비를 설정하고 무료 배송 입점리스트에 상품입점업체 아이디가 포함된 경우 */
						p_shoppingPay_use = 'F' /* 상품배송비가 무료배송인 경우 */
					),
					1, 0
				) > 0
			";
		}
		else if(in_array($bv, array('조건부 무료배송'))) {

			// 조건부 무료배송에 해당되는 업체 추출
			$DeliveryFreeCompany = array();
			$SiteFree = array();
			$ComFree = array();
			if($siteInfo['s_delprice'] > 0 && $siteInfo['s_delprice_free'] > 0) $SiteFree = _MQ_assoc(" select cp_id from smart_company where cp_delivery_use = 'N' "); // 사이트설정 조건부 무료배송 업체
			$ComFree = _MQ_assoc(" select cp_id from smart_company where cp_delivery_use = 'Y' and cp_delivery_price > 0 and cp_delivery_freeprice > 0 "); // 입점업체 중 무료 배송 업체
			if(count($SiteFree) > 0) $ComFree = array_merge($SiteFree, $ComFree); // 사이트설정 조건부 무료배송 업체 + 조건부 무료배송 업체
			if(count($ComFree) <= 0) $ComFree = array();
			foreach($ComFree as $cfk=>$cfv) {
				$DeliveryFreeCompany[] = $cfv['cp_id'];
			}

			// 부분 무료배송 쿼리문 추가
			$FreeDeliQuery[] = "
				if(
					(
						p_shoppingPay_use = 'N' and p_cpid in('".implode("', '", array_values($DeliveryFreeCompany))."') /* 조건부 무료배송 업체에 해당되는 상품  */
					),
					1, 0
				) > 0
			";
		}
		else if(in_array($bv, array('할인', '할인상품', '할인 상품'))) { // 정상가보다 판매가가 낮은 것
			$sub_query .= " and (p_screenPrice > 0 and p_screenPrice > p_price) ";
		}
		else if(in_array($bv, array('쿠폰', '쿠폰상품', '쿠폰 상품'))) { // 쿠폰이 등록된 상품중 실제 쿠폰금액이 있는경우
			/*
				1. |를 구분으로 금액 부분만 추출
				2. 금액 단위 콤마(,)가 있다면 제거
				3. 처리된 값을 숫자형으로 변형
				4. 상품금액이 0보다 큰것으로 조건 추가
			*/
			$sub_query .= " and cast(replace(substring_index(p_coupon, '|', -1), ',', '') as char(1)) > 0 ";
		}
		else if(in_array($bv, array('적립금', '적립금 지급'))) { // 적립비율이 0보다 큰것
			$sub_query .= " and p_point_per > 0 ";
		}
	}

	// 혜택구분 - (무료배송+ 조건부 무료배송) 조건 쿼리 추가
	if(count($FreeDeliQuery) > 0) $sub_query .= " and (".implode(' or ', $FreeDeliQuery).") ";
}


// 가격 검색
if(isset($search_price) && $search_price != '') {
	/*
		5이상 5~
		5이하 ~5
		5초과 6~
		5미만 ~4
	*/
	$search_price_arr = explode('_', $search_price);
	$sub_query .= " and p_price ".($search_price_arr[1] == 'lower'?' <= ':' >= ').$search_price_arr[0];
}


// 정렬방식
switch($_order) {
	case 'price_asc': $s_order = ' order by p_price asc, p_idx asc '; break; // 가격 낮은순
	case 'price_desc': $s_order = ' order by p_price desc, p_idx asc '; break; // 가격 높은순
	case 'pname': $s_order = ' order by p_name asc, p_idx asc '; break; // 상품이름순(abc..)
	case 'date': $s_order = ' order by p_rdate desc, p_idx asc '; break; // 등록일 순
	case 'sale': $s_order = ' order by p_salecnt desc, p_idx asc '; break; // 판매순(인기순)
	default: $s_order = ' order by score asc, p_idx asc '; break; // 기본 설정 정렬 순
}
if(isset($order_field) && $order_field != '') { // 사용자 지정 정렬 방식이 있다면
	if(empty($order_sort) || $order_sort == '') $order_sort = 'asc'; // 정령 방식이 없다면 기본값 asc
	$s_order = " order by {$order_field} {$order_sort}  ";
}
if(empty($query_alias) || $query_alias == '') $query_alias = " 0 as score, p.* ";


// 데이터 조회 및 페이지 계산
$listmaxcount = (isset($listmaxcount)?$listmaxcount:20); // 페이지당 갯수
if(empty($listpg) || $listpg == '') $listpg = 1;
$count = $listpg*$listmaxcount-$listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_product as p {$query_from} where (1) {$s_query} "); $TotalAllCount = $res['cnt']; // 메인 조건만 포함하는 검색 개수 // 사용목적: 전체 X개중 Y개 식 표현, 전체 상품 없읍과 서브 상품(상세) 없음 구분
$res = _MQ(" select count(*) as cnt from smart_product as p {$query_from} where (1) {$s_query} {$sub_query} "); $TotalCount = $res['cnt']; // 메인+서브 조건을 모두 포함한 검색 개수
$Page = ceil($TotalCount/$listmaxcount);
/* SSJ : 상품 품절 체크 - p_soldout_chk 정보 업데이트 : 2019-02-11 */
$res = _MQ_assoc("
    select
        ".(isset($query_alias) && $query_alias != ''?$query_alias:' 0 as score, p.* ')."
        ,if(p_soldout_chk='N',p_stock,0) as p_stock
    from
        smart_product as p {$query_from}
    where (1)
        {$s_query}
        {$sub_query}
    {$s_order}
    limit {$count}, {$listmaxcount}
");


// 카테고리 추출(메인 검색 조건만 기준으로 함 - 검색마다 결과가 바뀌지 않도록.)
/*
	$AllCate -> 전체 카테고리 정보 => (/program/wrap.header.php)
	$dp1cate -> 전체 1차 카테고리 정보 => (/program/wrap.header.php)
	$dp2cate -> 전체 2차 카테고리 정보 => (/program/wrap.header.php)
	$dp3cate -> 전체 3차 카테고리 정보 => (/program/wrap.header.php)

	------------------------------------------------------------------
	$dp1cateName -> 1차 카테고리 이름정보 => (/program/wrap.header.php)
		출력 예>
		$dp1cateName['1차카테코드'] = '1차 카테네임';

	$dp2cateName -> 2차 카테고리 이름정보 => (/program/wrap.header.php)
		출력 예>
		$dp2cateName['1차카테코드']['2차카테코드'] = '2차 카테네임';

	$dp3cateName -> 3차 카테고리 이름정보 => (/program/wrap.header.php)
		출력 예>
		$dp3cateName['1차카테코드']['2차카테코드']['3차카테코드'] = '3차 카테네임';
	------------------------------------------------------------------
*/
// 검색처리
$arr_category = array();
$category_row = _MQ_assoc("
	select
		pct.pct_cuid,
		ct.*
	from
		smart_product as p {$query_from} left join
		smart_product_category as pct on(pct.pct_pcode = p.p_code) left join
		smart_category as ct on(ct.c_uid = pct.pct_cuid)
	where (1)
		{$s_query}
		and ct.c_uid is not null
		and ct.c_view = 'Y'
	group by pct.pct_cuid
");
if(count($category_row) <= 0) $category_row = array();
foreach($category_row as $k=>$v) {
	if($v['c_depth'] == 1) {
		if(is_array($arr_category[$v['c_uid']]) || $v['c_name'] == '') continue; // 이미 있다면 pass
		$arr_category[$v['c_uid']] = array();
		$arr_category[$v['c_uid']]['item'] = array();
		$arr_category[$v['c_uid']]['name'] = $v['c_name'];
	}
	else if($v['c_depth'] == 2) {
		$parent = $v['c_parent']; // 부모 c_uid
		if(!is_array($arr_category[$parent]) && $dp1cateName[$parent] != '') {
			$arr_category[$parent] = array();
			$arr_category[$parent]['item'] = array();
			$arr_category[$parent]['name'] = $dp1cateName[$parent];
		}
		if(!is_array($arr_category[$parent]['item'][$v['c_uid']]) && $dp1cateName[$parent] != '' && $v['c_name'] != '') {
			$arr_category[$parent]['item'][$v['c_uid']] = array();
			$arr_category[$parent]['item'][$v['c_uid']]['name'] = $v['c_name'];
		}
	}
	else if($v['c_depth'] == 3) {
		$parent = explode(',', $v['c_parent']); // 부모 c_uid
		if(!is_array($arr_category[$parent[0]]) && $dp1cateName[$parent[0]] != '') {
			$arr_category[$parent[0]] = array();
			$arr_category[$parent[0]]['item'] = array();
			$arr_category[$parent[0]]['name'] = $dp1cateName[$parent[0]];
		}
		if(!is_array($arr_category[$parent[0]]['item'][$parent[1]]) && $dp2cateName[$parent[0]][$parent[1]] != '' && $dp1cateName[$parent[0]] != '') {
			$arr_category[$parent[0]]['item'][$parent[1]] = array();
			$arr_category[$parent[0]]['item'][$parent[1]]['item'] = array();
			$arr_category[$parent[0]]['item'][$parent[1]]['name'] = $dp2cateName[$parent[0]][$parent[1]];
		}
		if(!is_array($arr_category[$parent[0]]['item'][$parent[1]]['item'][$v['c_uid']]) && $dp2cateName[$parent[0]][$parent[1]] != '' && $dp1cateName[$parent[0]] != '' && $v['c_name'] != '') {
			$arr_category[$parent[0]]['item'][$parent[1]]['item'][$v['c_uid']] = array();
			$arr_category[$parent[0]]['item'][$parent[1]]['item'][$v['c_uid']]['name'] = $v['c_name'];
		}
	}
}


// 브랜드 추출(메인 검색 조건만 기준으로 함 - 검색마다 결과가 바뀌지 않도록.)
$arr_brand = array();
$brand_row = _MQ_assoc("
	select
		p.p_brand,
		brd.c_name
	from
		smart_product as p {$query_from} left join
		smart_brand as brd on(p.p_brand = brd.c_uid)
	where (1)
		{$s_query}
		and c_uid is not null
		and c_view = 'Y'
	group by p.p_brand
	order by c_idx asc
");
if(count($brand_row) <= 0) $brand_row = array();
foreach($brand_row as $k=>$v){
	$ex = array_filter(explode("," , $v['p_brand']));
	foreach($ex as $sk=>$sv) {
		if(trim($sv)!='') $arr_brand[trim($v['c_name'])]++;
	}
}
if(count($arr_brand) > 0) ksort($arr_brand);


// 해시태그 추출 (메인 검색 조건만 기준으로 함 - 검색마다 결과가 바뀌지 않도록.)
$arr_hashtag = array();
$hashtag_row = _MQ_assoc("
	select
		p.p_hashtag
	from
		smart_product as p {$query_from}
	where (1)
		{$s_query}
	group by p.p_hashtag
");
//search_word=%23
if(count($hashtag_row) <= 0) $hashtag_row = array();
foreach($hashtag_row as $k=>$v){
	$ex = array_filter(explode("," , $v['p_hashtag']));
	foreach($ex as $sk=>$sv) {
		if(trim($sv)!='') $arr_hashtag[trim($sv)]++;
	}
}
if(count($arr_hashtag) > 0) ksort($arr_hashtag); // 전체 해시를 키정렬로 재배열


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


// 다른고객이 많이 찾는상품(검색 결과가 없을 경우만 노출)
$HitProduct = array();
if($TotalCount <= 0) {
	$siteInfo['s_search_diff_maxcnt'] = ($siteInfo['s_search_diff_maxcnt'] > 100?100:$siteInfo['s_search_diff_maxcnt']); // 최대 수량 100개
	$HitProductCount = $siteInfo['s_search_diff_maxcnt']*($siteInfo['s_search_diff_option'] == 'normal'?1:2); // 랜덤 정렬인경우 지정수량 *2배를 하여 셔플 후 원래 개수만큼만 출력 하도록 처리
	$HitProduct = _MQ_assoc(" select *, (select count(*) as cnt from smart_product_talk where pt_pcode=p_code and pt_type='상품평가' and pt_intype='normal') as review_cnt from smart_product where p_stock > 0 and p_soldout_chk = 'N' and p_view = 'Y' and p_option_valid_chk = 'Y' order by ".($siteInfo['s_search_diff_orderby'] == 'salecnt'?' p_salecnt desc':'review_cnt desc')." limit 0, {$HitProductCount} ");
	if($siteInfo['s_search_diff_maxcnt'] < $HitProductCount && count($HitProduct) > 0) {
		shuffle($HitProduct);
		$HitProduct = array_slice($HitProduct, 0, $siteInfo['s_search_diff_maxcnt']);
	}
}


// 추가 검색 조건
$search_option = array_filter(explode(',', $siteInfo['s_search_option'])); // category,brand,hashtag,price,boon


// 사용자 출력 템플릿
if(empty($non_list_view) || $non_list_view == '') $non_list_view = 'true';
if($non_list_view == 'true') { // 리스트 출력 모드에서만 출력
	if(isset($_list_type)) include($SkinData['skin_root'].'/'.str_replace('.php', '', $_list_type).'.php'); // 스킨폴더에서 해당 파일 호출
	else include($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
}


actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행