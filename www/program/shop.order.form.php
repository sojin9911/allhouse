<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
if(substr(phpversion(),0,3) < 5.4) { session_register("order_start"); }
$_SESSION["order_start"] = $_COOKIE["AuthShopCOOKIEID"];
clean_cart(); // 장바구니 판매불가 상품 삭제


// -- 카트정보 추가 -- LCY --
include(OD_PROGRAM_ROOT."/shop.cart.inc.php");  // 카트 정보 추가

// 주문 타입 both : 둘다 , product : 배송상품, coupon : 쿠폰 상품
if($order_type_product == "Y" && $order_type_coupon == "Y") { $order_type = "both"; }
if($order_type_product == "Y" && $order_type_coupon != "Y") { $order_type = "product"; }
if($order_type_product != "Y" && $order_type_coupon == "Y") { $order_type = "coupon"; }


// === 비회원 구매가 미적용이고, 로그인 하지 않았을때  통합 kms 2019-06-24 ====
// 넘길 변수 설정하기
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_POST,$_GET))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// 넘길 변수 설정하기

if( $none_member_buy === true  ) {
	// error_loc_msg('/?pn=member.login.form','로그인 후 이용하실 수 있습니다.');
	error_loc("/?pn=member.login.form&_rurl=".$_PVSC);
}
// === 비회원 구매가 미적용이고, 로그인 하지 않았을때  통합 kms 2019-06-24 ====



// JJC003 묶음배송
$arr_product_sum = $arr_product = array();
if( count($arr_cart)==0 ) { // 장바구니 상품 없을때
	error_loc_msg('/?pn=shop.cart.list','주문할 상품이 없습니다. 장바구니로 이동합니다.');
}

// {{{회원쿠폰}}} -- 주문작성시 적용된 쿠폰 데이터 삭제 ,
couponFormInit();
// {{{회원쿠폰}}}


// 사용가능한 적립금 추출
$able_point = $mem_info['in_point']; // 회원 적립금

//  -- LCY160410 2016-04-10 --  주문에 사용되었지만, 아직 적립금이 적용되지 않은 적림금들의 합을 구한다.
$psers = array();
if(is_login()){ // 2019-10-28 SSJ :: 회원일경우에만 적립금 체크
	$psers = _MQ("select sum(o_price_usepoint) as use_point_sum from smart_order where o_apply_point = 'N' and o_price_usepoint > 0 and  o_canceled = 'N' and o_paystatus = 'N' and  o_mid = '".get_userid()."' and o_paymethod in ('online','virtual') ");
}
if($able_point > 0){
	$use_point_sum = $psers['use_point_sum'] > 0 ? $psers['use_point_sum'] : 0;
	$able_point = $mem_info[in_point] - $use_point_sum;
	$able_point = $able_point <= 0 ? 0: $able_point;
}
//  -- LCY160410 2016-04-10 --  주문에 사용되었지만, 아직 적립금이 적용되지 않은 적림금들의 합을 구한다.


// 비회원 주문 시 이용약관 , 개인정보 수집동의 항목
$arr_policy = is_login() ? array() : arr_policy('all');


// 프로모션코드 적용 LMH005
$_promotion_cnt = _MQ_result(" select count(*) from smart_promotion_code where pr_use = 'Y' and pr_expire_date >= CURDATE() ");


// 이전주소 추출
$arr_old_order = array();
if(is_login()){ // 2019-10-28 SSJ :: 회원일경우에만 이전주소 추출
	$arr_old_order = _MQ_assoc("select o_rzonecode, o_oemail, o_ordernum , o_rname , o_rtel,o_rhp, o_rpost, o_raddr1, o_raddr2, o_raddr_doro , left(o_rdate,10) as ordate from smart_order where o_mid='".get_userid()."' and o_paystatus='Y' group by `o_rzonecode` order by ordate desc limit 0, 5 ");
}

// 이전주소 사용가능한지 체크
$old_use_val = 'N';
if(count($arr_old_order) > 0) { $old_use_val = 'Y'; }


// 무통장입금시 입금은행 배열 추출
$arr_bank = array();
$ex = _MQ_assoc("select * from smart_bank_set order by bs_idx asc");
foreach( $ex as $k=>$v ){ $app_str = "[$v[bs_bank_name]] $v[bs_bank_num], $v[bs_user_name]"; $arr_bank[$k] = $app_str; }

// {{{LCY무료배송이벤트}}} -- 무료배송 이벤트 조건에 속할경우 true, 그렇지 않을경우 false
$freeEventChk = PromotionEventDeliveryChk();

// SSJ : 토스페이먼츠 PG 모듈 교체 : 2021-02-22 -- 토스는 실시간계좌이체 미지원
if($siteInfo['s_pg_type'] == 'lgpay'){ $siteInfo['s_pg_paymethod_L'] = 'N'; }

include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출

// 주소찾기 - 우편번호찾기 박스
if(!is_mobile()) include_once(OD_ADDONS_ROOT.'/newpost/newpost.search.php');
else include_once(OD_ADDONS_ROOT.'/newpost/newpost.search_m.php');
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행