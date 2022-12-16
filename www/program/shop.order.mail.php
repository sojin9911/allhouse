<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행




// 적용메일
// _ordernum ==> 주문번호
// _type ==> card (주문내역 : 카드결제)
// _type ==> online (주문내역 : 무통장입금)
// _type ==> payconfirm (주문내역 : 결제확인)
// _type ==> delivery (주문내역 : 상품발송)
// 각 _type에 따른 mailing_app_content 불러오기



// - 주문내역 ---
$oque = " select * from smart_order where o_ordernum='" . $_ordernum . "' ";
$or = _MQ($oque);
// - 주문내역 ---


// - 주문상품내역 ---
$opque = "
	select op.* , p.p_name, p.p_img_list_square , p.p_code
	from smart_order_product as op
	inner join smart_product as p on ( p.p_code=op.op_pcode )
	where op_oordernum='" . $_ordernum . "' and op.op_cancel = 'N'
";
$opr = _MQ_assoc($opque);
// - 주문상품내역 ---

// 입금계좌 추출
if( $or['o_paymethod'] == "virtual" ) {
	$vinfo = _MQ(" select * from smart_order_onlinelog where ool_ordernum = '".$or['o_ordernum']."' and ool_type = 'R' order by ool_uid desc limit 1 ");
	$or['o_bank'] = "[".$vinfo['ool_bank_name']."] ".$vinfo['ool_account_num'].", ".$vinfo['ool_deposit_name'];
}

switch( $_type ){
	case "card": include(OD_MAIL_ROOT."/shop.order.mail_card.php"); break;
	case "online": case "virtual": include(OD_MAIL_ROOT."/shop.order.mail_online.php"); break; // 무통장/가상계좌 주문시
	case "payconfirm": include(OD_MAIL_ROOT."/shop.order.mail_payconfirm.php"); break; // 무통장/가상계좌 입금확인 시
	case "delivery": include(OD_MAIL_ROOT."/shop.order.mail_delivery.php"); break; // 배송
}



actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행