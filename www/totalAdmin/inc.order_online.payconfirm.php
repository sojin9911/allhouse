<?php

	/**
	* 무통장입금 입금완료시 입금완료처리 :: 결제대기 => 결제완료
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
	if($osr['o_apply_point'] == 'Y') return false; // 포인트, 쿠폰등이 적용된 주문은 실행하지 않음


	// ----- SSJ : 2020-07-01 : 결제완료/결제취소 일괄처리 -----
	// 공통결제
	//		넘길변수
	//			-> 주문번호 : $ordernum
	$ordernum = $_ordernum;
	include(OD_PROGRAM_ROOT."/shop.order.result.pro.php"); // ----- SSJ : 2020-07-01 : 결제완료/결제취소 일괄처리 -----
	if($pay_status == 'N') {return false;}
	// ----- SSJ : 2020-07-01 : 결제완료/결제취소 일괄처리 -----