<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
if($NotInclude !== true) actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



if(!$pcode) {
	if($code) $pcode = $code;
	else error_msg("잘못된 접근입니다.");
}

// 스킨정보
$SkinInfo = SkinInfo('all', 'auto', 'cookie');

// - 임시 옵션 삭제 ---
if($_COOKIE["AuthShopCOOKIEID"]) _MQ_noreturn("delete from smart_product_tmpoption where pto_mid='". $_COOKIE["AuthShopCOOKIEID"] ."'");



// 상품정보 추출
$p_info = get_product_info($pcode);
if(!$p_info['p_name']) error_msg("잘못된 상품정보입니다.");

// - 텍스트 정보 추출 ---
$p_info = array_merge($p_info , _text_info_extraction( "smart_product" , $pcode ));

// - JJC : 입점업체 정보 추출 - 2019-01-02 : //
$p_com_info = _company_info( $p_info['p_cpid'] );
$product_company_name = $p_com_info['cp_name']; // 입점업체명
$product_company_tel = $p_com_info['cp_tel']; // 입점 전화
$product_company_fax = $p_com_info['cp_fax']; // 입점 팩스
$product_company_homepage = $p_com_info['cp_homepage']; // 입점 홈페이지
$product_company_addr = $p_com_info['cp_address']; // 입점 주소
$product_company_charge = $p_com_info['cp_charge']; // 입점 담당자명
$product_company_tel2 = $p_com_info['cp_tel2']; // 입점 담당자 핸드폰
$product_company_email = $p_com_info['cp_email']; // 입점 담당자이메일

$product_company_delivery_company = $p_com_info['cp_delivery_company']; // 입점 지정택배사
$product_company_delivery_date = $p_com_info['cp_delivery_date']; // 입점 평균배송기간
$product_company_delivery_return_addr = $p_com_info['cp_delivery_return_addr']; // 입점 반송주소
// - JJC : 입점업체 정보 추출 - 2019-01-02 : //


// 숨김 상품 체크
if($p_info['p_view'] == "N") error_msg("판매종료된 상품입니다.");

// 모바일 상품상세 설명
if(is_mobile() && $p_info['p_use_content'] <> 'Y') $p_info['p_content'] = $p_info['p_content_m'];


// 큰이미지
$main_img = get_img_src($p_info['p_img_b1']);
$pro_img = array();
for($i=1; $i<=10; $i++){
	$pro_img[] = $p_info['p_img_b'.$i];
}
$pro_img = array_values(array_filter($pro_img));



// 노출항목 설정 추출
$ex_display_pc = array_filter(array_unique(explode('|', $siteInfo['s_display_pinfo_pc'])));
if($siteInfo['s_display_pinfo_mo_use_pc'] == 'Y'){
	$ex_display_mo = $ex_display_pc;
}else{
	$ex_display_mo = array_filter(array_unique(explode('|', $siteInfo['s_display_pinfo_mo'])));
}
// 추가 노출항목 설정 추출
$ex_display_add = array_filter(array_unique(explode('|', $siteInfo['s_display_pinfo_add'])));



// -- 기본 정보 -----
$pro_name = stripslashes($p_info['p_name']); // 대표 상품명
// 부가상품명 설정에 따라 노출여부 결정
if(in_array('subname', $ex_display_add)) $pro_subname = htmlspecialchars(stripslashes($p_info['p_subname'])); // 부가 상품명
else $pro_subname = ''; // 부가 상품명
// 옵션재고 노출여부
if(in_array('optionStock', $ex_display_add)) $isOptionStock = true;
else $isOptionStock = false;
$pro_price = number_format($p_info['p_price']); // 판매가
$pro_screenprice = number_format($p_info['p_screenPrice']); // 정상가
$pro_point = number_format($p_info['p_price']*$p_info['p_point_per']/100); // 적립율
$pro_orgin = $p_info['p_orgin']; // 원산지
$pro_maker = $p_info['p_maker']; // 제조사
// -- 기본 정보 -----



// 브랜드정보 추출
if($p_info['p_brand']){
	$brand_info = _MQ(" select * from smart_brand where c_uid = '". $p_info['p_brand'] ."' ");
	$pro_brand_name = $brand_info['c_name'];
	$pro_brand_uid = $brand_info['c_uid'];
}



// 배송비 정책 추출
$pro_delivery_info = get_delivery_info($p_info['p_code']);
// 2019-01-04 SSJ :: 배송비가 0원일경우 무료배송처리
if($pro_delivery_info['price'] <= 0) $p_info['p_shoppingPay_use'] = 'F';



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

// 기획전 상품에 기획전 아이콘
$que_promotion_pcode = "
	SELECT
		count(*) as cnt
	FROM  smart_promotion_plan as pp
	INNER JOIN smart_promotion_plan_product_setup AS ppps ON ( ppps.ppps_ppuid = pp.pp_uid )
	INNER JOIN smart_product AS p ON ( p.p_code = ppps.ppps_pcode AND p.p_code = '". $pcode ."' )
	WHERE
		pp.pp_view = 'Y' AND
		CURDATE() BETWEEN pp.pp_sdate AND pp.pp_edate
";
$res_promotion_pcode = _MQ_result($que_promotion_pcode);
if($res_promotion_pcode > 0  && $promotion_icon_src) {
	$tmpicon .= '<img src="'.$promotion_icon_src .'" alt="'. $promotion_icon['pi_title'] .'">';
}


// 무료배송 - 아이콘
if($pro_delivery_info['status'] == '1' && $freedelivery_icon_src) $tmpicon .= '<img src="'.$freedelivery_icon_src .'" alt="'. $freedelivery_icon['pi_title'] .'">';	// 무료배송 아이콘



// 배송정보
$pro_del_info = implode(' / ', array_filter(array($pro_delivery_info['del_company'] , $p_info['p_delivery_info'])));



// KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22
$ex_coupon = explode('|' , $p_info['p_coupon']);
if(($ex_coupon[1] == 'price' || $ex_coupon[1] == 'per') || $ex_coupon[0] <> ''){
	if($coupon_icon_src) $tmpicon .= '<img src="'.$coupon_icon_src .'" alt="'. $coupon_icon['pi_title'] .'">';  // 상품쿠폰 아이콘
	$ex_coupon['name'] = stripslashes($ex_coupon[0]);
	$ex_coupon['price'] = rm_comma($ex_coupon[2]);
	$ex_coupon['per'] = rm_comma($ex_coupon[3]);
	$ex_coupon['max'] = rm_comma($ex_coupon[4]);
}


// 아이콘 설정
$p_icon_array = explode(",",$p_info['p_icon']);
if(count($product_icon) > 0) {
	foreach($product_icon as $k0 => $v0) {
		if(array_search($v0['pi_uid'],$p_icon_array) !== false){
			$_tmp_arr = array('pc'=>get_img_src($v0['pi_img'],IMG_DIR_ICON), 'mo'=>get_img_src($v0['pi_img_m'],IMG_DIR_ICON));
			$_tmp_src = is_mobile() ? ($_tmp_arr['mo'] ? $_tmp_arr['mo'] : $_tmp_arr['pc']) : ($_tmp_arr['pc'] ? $_tmp_arr['pc'] : $_tmp_arr['mo']);
			if($_tmp_src) $tmpicon .= '<img src="'.$_tmp_src.'" alt="'.$v0['pi_title'].'">';
		}
	}
}
$app_pro_icon = ($tmpicon ? $tmpicon : '');



// 상품평 갯수
$eval_cnt = number_format(get_talk_total($p_info['p_code'],"eval","normal"));
// 상품문의 갯수
$qna_cnt = number_format(get_talk_total($p_info['p_code'],"qna","normal"));
// 상품평점
$star_persent = get_eval_average($p_info['p_code']);



// 상품 해시테그 추출
$pro_hashtag = array_filter(array_unique(explode(',', $p_info['p_hashtag'])));


if($SkinData['device']=='pc'){
	// [PC]공통 : 상품상세 중간 배너 (1050 x free)
	$ProductMiddle = info_banner($_skin.',site_product_middle', 999999, 'data');
}else{
	// [MOBILE]공통 : 상품상세 중간 배너 (940 x free, 1개)
	$ProductMiddle = info_banner($_skin.',mobile_product_middle', 999999, 'data');
}



// 관련상품 추출 - 상세페이지 노출설정에따라
$relation = array();
if($SkinData['device']=='pc' && $siteInfo['s_display_relation_pc_use'] == 'Y') {
	$relation_total = $SkinInfo['product']['config_relative_cnt']['pc'][$siteInfo['s_display_relation_pc_col']] * $siteInfo['s_display_relation_pc_row'] *1;
	$relation = ProductRelation($p_info, $relation_total);
}
else if($SkinData['device']=='m' && $siteInfo['s_display_relation_mo_use'] == 'Y') {
	$relation_total = $SkinInfo['product']['config_relative_cnt']['mo'][$siteInfo['s_display_relation_mo_col']] * $siteInfo['s_display_relation_mo_row'] *1;
	$relation = ProductRelation($p_info, $relation_total);
}



// 2018-07-27 SSJ :: 재고 체크
$isSoldOut = false;
if($p_info['p_stock'] < 1){ $isSoldOut = true; }
else if($p_info['p_soldout_chk'] == 'Y'){ $isSoldOut = true; }



// 옵션정보 불러오기
$options = array(); $add_options = array();
if($p_info['p_option_type_chk'] <> 'nooption'){
	// 필수옵션
	$option_que = " select po_uid , po_poptionname, po_cnt,po_poptionprice , po_color_type , po_color_name  from smart_product_option where po_view = 'Y' and po_pcode='" . $pcode . "' and po_depth='1' and po_poptionname != '' ORDER BY po_sort , po_uid ASC ";
	$options = _MQ_assoc($option_que);
	// 추가옵션
	$add_option_que = "select * from smart_product_addoption where pao_pcode='". $pcode ."' and pao_depth='1' and pao_view = 'Y' and pao_poptionname != '' order by pao_sort asc, pao_uid asc ";
	$add_options = _MQ_assoc($add_option_que);
    foreach($add_options as $k=>$v){
        //$add_sub_options = _MQ_assoc("select * from smart_product_addoption where pao_pcode='".$pcode."' and pao_depth='2' and pao_parent='".$v['pao_uid']."' and pao_poptionname != '' order by pao_sort asc, pao_uid asc  ");
        // 2019-04-01 SSJ :: 추가옵션의 숨김옵션 제외 처리
        $add_sub_options = _MQ_assoc("select * from smart_product_addoption where pao_pcode='".$pcode."' and pao_depth='2' and pao_parent='".$v['pao_uid']."' and pao_view = 'Y' and pao_poptionname != '' order by pao_sort asc, pao_uid asc  ");
        if(count($add_sub_options)>0) $add_options[$k]['add_sub_options'] = $add_sub_options;
        else unset($add_options[$k]); // 2차 옵션이 없으면 비노출
    }
}



// 정보제공고시 추출
$notify_info = _MQ_assoc("select * from smart_product_req_info where pri_value != '' and pri_pcode='".$pcode."' order by pri_uid asc");



// 배송정보 추출
$com_info = _company_info($p_info['p_cpid']);
if($com_info['cp_delivery_use'] == "Y") {
	$del_company = $com_info['cp_delivery_company'];
	$del_date = $com_info['cp_delivery_date'];
	$del_complain_price = $com_info['cp_delivery_complain_price'];
	$del_return_addr = $com_info['cp_delivery_return_addr'];
	$complain_ok = htmlspecialchars_decode($com_info['cp_complain_ok']);
	$complain_fail = htmlspecialchars_decode($com_info['cp_complain_fail']);
} else {
	$del_company = $siteInfo['s_del_company'];
	$del_date = $siteInfo['s_del_date'];
	$del_complain_price = $siteInfo['s_del_complain_price'];
	$del_return_addr = $siteInfo['s_del_return_addr'];
	$complain_ok = htmlspecialchars_decode($siteInfo['s_complain_ok']);
	$complain_fail = htmlspecialchars_decode($siteInfo['s_complain_fail']);
}

// {{{회원등급혜택}}} or getGroupSetPer 검색
/*
	* 회원할인 이나 적립은 추가로 발생된다.
	* 상품 할인전 금액 기준으로 적립금 추가 적용
	* 할인 또는 적립금액을 가져오며 합산된 급액은 가져오지 않는다.
	- 금액 할인율 getGroupSetPer($p_info['p_price'],'price',$pcode) 형태로 사용
	- 추가 적립률 getGroupSetPer($p_info['p_price'],'point',$pcode) 형태로 사용

*/
$groupSetUse = false;
if(is_login() == true && $p_info['p_groupset_use'] == 'Y'  ){ // 로그인 중이고 등급할인혜택 적용이 Y 라면
	if($groupSetInfo['mgs_sale_price_per'] > 0 || $groupSetInfo['mgs_give_point_per'] > 0){
		$groupSetUse = true;
	}
}
// {{{회원등급혜택}}}


// {{{LCY무료배송이벤트}}} -- 무료배송 이벤트 조건에 속할경우 true, 그렇지 않을경우 false
$freeEventChk = PromotionEventDeliveryChk('view');
$freeEventInfo = getPromotionEventDelivery();
// {{{LCY무료배송이벤트}}}

// JJC : 2019-05-15 : 판매자 정보
//		입점기능일 경우
if($SubAdminMode === true){
	$app_adshop = $com_info['cp_name'];//상호명
	$app_glbtel = $com_info['cp_tel'];//대표전화
	$app_ceo_name = $com_info['cp_ceoname'];//대표자
	$app_fax = $com_info['cp_fax'];//팩스전화
	$app_company_num = $com_info['cp_number'];//사업자등록번호
	$app_ademail = $com_info['cp_email'];//대표 이메일
	$app_company_snum = $com_info['cp_snumber'];//통신판매업번호----------------------
	$app_company_addr = $com_info['cp_address'];//사업장소재지
}
//		입점기능이 아닐 경우
else {
	$app_adshop = $siteInfo['s_company_name'];//상호명
	$app_glbtel = $siteInfo['s_glbtel'];//대표전화
	$app_ceo_name = $siteInfo['s_ceo_name'];//대표자
	$app_fax = $siteInfo['s_fax'];//팩스전화
	$app_company_num = $siteInfo['s_company_num'];//사업자등록번호
	$app_ademail = $siteInfo['s_ademail'];//대표 이메일
	$app_company_snum = $siteInfo['s_company_snum'];//통신판매업번호
	$app_company_addr = $siteInfo['s_company_addr'];//사업장소재지
}


// 2020-03-11 SSJ :: 이용안내 기본값 설정
if(count($arrProGuideType)>0){
	foreach($arrProGuideType as $_guide_key=>$_guide_title){
		if($p_info['p_guide_type_'.$_guide_key] == ''){
			$p_info['p_guide_type_'.$_guide_key] = 'list';
			$guide_r = _MQ(" select g_uid from smart_product_guide where (1) and g_type = '". $_guide_key ."' and g_user in ('_MASTER_', '". $p_info['p_cpid'] ."') order by g_default asc limit 1  ");
			$p_info['p_guide_uid_'.$_guide_key] =$guide_r['g_uid'];
		}
	}
}


if($NotInclude !== true) {
	include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
	actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
}

