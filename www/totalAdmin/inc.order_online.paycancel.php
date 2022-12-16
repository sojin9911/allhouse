<?php

	/**
	* 무통장입금 입금완료시 입금완료처리 :: 결제완료 => 결제대기
	*/
	$_ordernum = $_ordernum ? $_ordernum : $ordernum;

	// 필수변수 체크
	if($_ordernum == '') return false;

	// 주문내역 추출
	if($row['o_ordernum'] == $_ordernum) $osr = $row;
	if($_ordernum && $osr['o_ordernum'] <> $_ordernum) $osr = get_order_info($_ordernum);

	// 주문내역 체크
	if($osr['o_ordernum'] <> $_ordernum) return false;

	// 주문상태 체크
	if($osr['o_canceled'] == 'Y') return false; // 취소된주문은 실행하지 않음
	if($osr['o_apply_point'] == 'N') return false; // 포인트, 쿠폰등이 적용되지 않은 주문은 실행하지 않음

	// 주문완료시 처리 부분 - 주문서수정,포인트,수량
	$__sque = "update smart_order set o_paystatus='N' , o_status='결제대기', o_paydate='0000-00-00 00:00:00' where o_ordernum='". $_ordernum ."' ";
	_MQ_noreturn($__sque);

	$__sque = "update smart_order_product set op_paydate='0000-00-00 00:00:00' where op_oordernum='". $_ordernum ."' ";
	_MQ_noreturn($__sque);

	// 제공변수 : $_ordernum
	include(OD_PROGRAM_ROOT.'/shop.order.pointdel_pro.php');

	// 상품 재고 증가 및 판매량 차감 : $_ordernum
	include(OD_PROGRAM_ROOT.'/shop.order.salecntdel_pro.php');


	// 주문발송 상태 변경
	order_status_update($_ordernum);
