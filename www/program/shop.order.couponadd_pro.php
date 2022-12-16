<?php
//{{{회원쿠폰}}}
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// *** 결제확인 시 --> 포인트 / 쿠폰 등 적용 ***
// - 주문정보 추출 ---
$osr = get_order_info($_ordernum);

if( $osr[o_memtype]=="Y" && $osr[o_apply_point] == "N") {

	//{{{회원쿠폰}}} -- 쿠폰 사용대기 처리
	if($osr[o_coupon_individual_uid]) _MQ_noreturn("update smart_individual_coupon set coup_use ='W' where find_in_set(coup_uid, '".$osr[o_coupon_individual_uid]."') > 0 ");
	//{{{회원쿠폰}}}
}
// -- 포인트 사용량에 따른  ---

actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
//{{{회원쿠폰}}}