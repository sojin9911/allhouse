<?php
$app_current_link = '_order4.list.php';
include_once('wrap.header.php');

// 정산정보
$r = _MQ(" select * from `smart_order_settle_complete` where `s_uid` = '{$suid}' ");
if(!$r['s_uid']) error_msg('잘못된 접근입니다.');
$r = array_merge($r , _text_info_extraction( "smart_order_settle_complete" , $r['s_uid'] ));
$op_code = explode(',', $r['s_opuid']);
if(sizeof($op_code) <= 0) error_msg('잘못된 접근입니다.');

// 입점업체정보
$partner = _MQ(" select * from `smart_company` where `cp_id` = '{$r['s_partnerCode']}' ");

// 주문정보 호출
$pr = _MQ_assoc("
	select
		op.*, p.* , o.* ,
		IF(
			op.op_comSaleType='공급가' ,
			(op.op_supply_price * op.op_cnt + op.op_delivery_price + op.op_add_delivery_price) ,
			(op.op_price * op.op_cnt - op.op_price * op.op_cnt * op.op_commission/ 100 + op.op_delivery_price + op.op_add_delivery_price)
		) as comPrice
	from `smart_order_product` as op
	left join `smart_product` as p on (p.p_code=op.op_pcode)
	left join `smart_order` as o on (op.op_oordernum = o.o_ordernum )
	where
		op.op_uid in ('". implode("' , '" , $op_code) ."') and
		op_partnerCode = '" . $r['s_partnerCode'] . "'
");


// 2017-06-22 ::: 부가세율설정 ::: JJC
$arr_sum = array();
foreach($pr as $sk=>$sv) {
	// 2017-06-22 ::: 부가세율설정 ::: JJC
	$partner['cp_vat_delivery'] = ($siteInfo['s_vat_delivery'] == 'C' ? $partner['cp_vat_delivery'] : $siteInfo['s_vat_delivery']);
	$arr_sum['delivery_price'][$partner['cp_vat_delivery']] += $sv['op_delivery_price'] + $sv['op_add_delivery_price'];
	$arr_sum['product_cnt'][$sv['op_vat']] += $sv['op_cnt'];
	$arr_sum['product_price'][$sv['op_vat']] += $sv['op_price'] * $sv['op_cnt'];
	//$arr_sum['product_usepoint'][$sv['op_vat']] += $sv['op_usepoint'];
	// SSJ : 정산 할인금액 패치 : 2021-05-14 -- 상품쿠폰 사용액
	$arr_sum['product_usepoint'][$sv['op_vat']] += $sv['op_usepoint'] + $sv['op_use_discount_price'] + $sv['op_use_product_coupon'];
	$arr_sum['comPrice'][$sv['op_vat']] += $sv['comPrice'];
	// 2017-06-22 ::: 부가세율설정 ::: JJC
}
$arr_sum['total']['Y'] = $arr_sum['product_price']['Y'] + $arr_sum['delivery_price']['Y']; // 과세 합계
$arr_sum['total']['N'] = $arr_sum['product_price']['N'] + $arr_sum['delivery_price']['N']; // 면세 합계



// 2017-06-26 ::: 형태 변경 - 부가세율설정 ::: JJC
if($arr_sum['total']['Y'] != 0 && $arr_sum['total']['N'] != 0) include_once('_order4.view.vat_double.php'); // 2가지 형태 추출 ( 과세 / 면세 )
else include_once('_order4.view.vat_one.php');// 한가지 형태 추출



include_once('wrap.footer.php');