<?php

// --- JJC : 부분취소 개선 : 2021-02-10 ---

include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


$osr = get_order_info($_ordernum);// JJC : 부분취소 개선 : 2021-02-10


// -- 주문상품의 op_usepoint 적용 : 할인액이 있을 경우에만 적용 ---
if($osr['o_price_usepoint']) {// JJC : 부분취소 개선 : 2021-02-10

	// 총구매상품가격
	$opres = _MQ("select IFNULL(sum(op_price * op_cnt + op_delivery_price + op_add_delivery_price),0) as op_sum from smart_order_product where op_oordernum = '". $_ordernum ."' ");
	$app_opsum = $opres['op_sum'];

	// --- 사용포인트적용 - 개별적용 ---
	$sum_usepoint = 0;
	$opres = _MQ_assoc("SELECT op_uid , (op_price * op_cnt + op_delivery_price + op_add_delivery_price) AS app_opsum FROM smart_order_product WHERE op_oordernum = '". $_ordernum ."' "); // JJC : 부분취소 개선 : 2021-02-10
	foreach($opres as $sk=>$sv){
		// 상품가격 * 갯수 * 사용총포인트 / 총구매상품가격
		$app_usepoint = round( $sv['app_opsum'] * $osr['o_price_usepoint'] / $app_opsum);
		_MQ_noreturn(" update smart_order_product set op_usepoint = '". $app_usepoint ."' where op_uid='". $sv['op_uid'] ."'");
		$sum_usepoint += $app_usepoint;
	}
	// --- 사용포인트적용 - 개별적용 ---

	// 총할인액 - 적용한 사용포인트 차액 있을 경우 처리
	if( $osr['o_price_usepoint']  <> $sum_usepoint ) {
		$opres = _MQ("select op_uid from smart_order_product where op_oordernum = '". $_ordernum ."' order by op_usepoint desc limit 1  ");
		if(sizeof($opres) > 0 ) {
			_MQ_noreturn(" update smart_order_product set op_usepoint = op_usepoint + " . ($osr['o_price_usepoint'] - $sum_usepoint)  ." where op_uid='". $opres['op_uid'] ."' ");
		}
	}
}
// -- 주문상품의 op_usepoint 적용 ---





// ----- JJC : 부분취소 개선 : 2021-02-10 -----
// 환불불가 할인액 추출
$arr_app_discount = array_diff($arr_order_discount_field , array('o_price_coupon_product' => '상품쿠폰','o_price_usepoint' => '적립금 사용')); 
$order_discount = 0;
if(sizeof($arr_app_discount) > 0 ) {foreach( $arr_app_discount as $appdk=>$appdv) { $order_discount += $osr[$appdk];}}
if( $order_discount > 0 ) {

	// 주문단위 할인총액 (사용포인트 , 상품개별포인트 제외) -> 부분취소 시 참조항목
	$_use_discount_price = $order_discount; // 환불불가 할인액 
	$sum_use_discount_price= 0; // 합계

	// 총구매상품가격
	if( !$app_opsum ) {
		$opres = _MQ("select IFNULL(sum(op_price * op_cnt  + op_delivery_price + op_add_delivery_price),0) as op_sum from smart_order_product where op_oordernum = '". $_ordernum ."' ");
		$app_opsum = $opres['op_sum'];
	}

	// --- 사용할인액 적용 - 개별적용 ---
	$opres = _MQ_assoc("SELECT op_uid , (op_price * op_cnt + op_delivery_price + op_add_delivery_price) AS app_opsum FROM smart_order_product WHERE op_oordernum = '". $_ordernum ."' "); // JJC : 부분취소 개선 : 2021-02-10
	foreach($opres as $sk=>$sv){
		// 상품가격 * 갯수 * 주문단위 할인총액 / 총구매상품가격
		$app_use_discount_price = round( $sv['app_opsum'] * $_use_discount_price / $app_opsum);
		_MQ_noreturn(" update smart_order_product set op_use_discount_price = '". $app_use_discount_price ."' where op_uid='". $sv['op_uid'] ."'");
		$sum_use_discount_price += $app_use_discount_price;
	}
	// --- 사용할인액 적용 - 개별적용 ---

	// 총할인액 - 사용할인액 차액 있을 경우 처리
	if( $_use_discount_price  <> $sum_use_discount_price ) {
		$opres = _MQ("select op_uid from smart_order_product where op_oordernum = '". $_ordernum ."' order by op_use_discount_price desc limit 1  ");
		if(sizeof($opres) > 0 ) {
			_MQ_noreturn(" update smart_order_product set op_use_discount_price = op_use_discount_price + " . ($_use_discount_price - $sum_use_discount_price)  ." where op_uid='". $opres['op_uid'] ."' "); // 마이너스여도 작동됨
		}
	}
}
// ----- JJC : 부분취소 개선 : 2021-02-10 -----


actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행