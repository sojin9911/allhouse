<?php

	include_once('inc.php');


	// 환불금액 재계산
	$__cancelTotal = array('pg'=>0,'point'=>0); // 부분취소할 최종 금액
	$__refundToBe = $totalPrice + $totadlPrice + $totalAprice - $totalDiscount; // 고객이 받아야할 총 금액// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC


	// -- 2016-08-22 ::: 포인트 있을 경우 오류 수정 --- JJC   // op_delivery_price
	// op_cancel_discount_price 추가 (부분취소 kms 2019-03-14)
	$__tPrice_cancel = _MQ_result(" select sum( op_price * op_cnt + ( if(op_free_delivery_event_use = 'Y',0,op_delivery_price) ) + op_add_delivery_price - op_cancel_discount_price ) from smart_order_product where op_oordernum='" . $ordernum . "' and op_cancel = 'Y' "); // 부분취소할 수 있는 실결제 잔액
	$__tPrice = $opr['o_price_real'] > 0 && $opr['o_price_real'] <> $__tPrice_cancel ? $opr['o_price_real'] - $__tPrice_cancel : $__tPrice;
	$__tPrice = $__tPrice < 0 ? 0 : $__tPrice;

	// -- 2016-08-22 ::: 포인트 있을 경우 오류 수정 --- JJC

	// PG 환불
	if( $opr['op_cancel_type']=='pg' ) {

		$__tPriceDiff = $__tPrice - $__refundToBe; // 실결제잔액과 고객이 받아야할 총 금액 차이
		$__availablePoint = $opr['o_price_usepoint'] - $opr['o_price_usepoint_refund']; // 환불시 사용 가능한 적립금 사용 금액
		$__availablePoint = ($opr['op_cancel']!='N' ? $opr['o_price_usepoint'] : $__availablePoint);

		// 실결제금액이 받아야할 총 금액보다 클 경우
		if( $__tPriceDiff >= 0 ) {
			$__cancelTotal = array('pg'=>$__refundToBe,'point'=>0); $__console = "type1";
		}
		else { // 받아야할 총 금액이 실결제금액보다 클 경우
			// 부분취소에 사용 가능한 포인트가 있을 경우
			if( $__availablePoint > 0 ) {
				// 부분취소해줄 포인트 잔액이 고객이 받아야할 금액보다 클 경우
				if( $__availablePoint - $__refundToBe > 0 ) {
					$__cancelTotal = array('pg'=>$__tPrice,'point'=>abs($__tPriceDiff)); $__console = "type3";// PG환불 요청 시
				}
				// 고객이 받아야할 금액이 부분취소해줄 포인트 잔액보다 클 경우
				else {
					$__cancelTotal = array('pg'=>$__tPrice,'point'=>$__availablePoint>$__refundToBe-$__tPrice?$__refundToBe-$__tPrice:$__availablePoint); $__console = "type5";// PG환불 요청 시
				}
			}
			// 부분취소에 사용 가능한 포인트가 없을 경우
			else {
				$__cancelTotal = array('pg'=>$__tPrice,'point'=>0); $__console = "type7"; // PG환불 요청 시
			}
		}
	} // // PG 환불


	// 포인트 환불
	else {

		$__tPriceDiff = $__tPrice + $opr['o_price_usepoint'] - $__refundToBe; // 실결제잔액과 고객이 받아야할 총 금액 차이
		$__availablePoint = $opr['tPrice'] + $opr['o_price_usepoint'] - $opr['o_price_usepoint_refund']; // 환불시 사용 가능한 적립금 사용 금액

		if( $__tPriceDiff >= 0 ) { // 실결제금액이 받아야할 총 금액보다 클 경우
			$__cancelTotal = array('pg'=>0,'point'=>$__refundToBe); $__console = "type2";
		}
		else { // 받아야할 총 금액이 실결제금액보다 클 경우

			// 부분취소에 사용 가능한 포인트가 있을 경우
			if( $__availablePoint > 0 ) {
				// 부분취소해줄 포인트 잔액이 고객이 받아야할 금액보다 클 경우
				if( $__availablePoint - $__refundToBe > 0 ) {
					$__cancelTotal = array('pg'=>0,'point'=>$__refundToBe); $__console = "type4" ;// 적립금환불 요청 시
				}
				// 고객이 받아야할 금액이 부분취소해줄 포인트 잔액보다 클 경우
				else {
					$__cancelTotal = array('pg'=>0,'point'=>$__availablePoint); $__console = "type6"; // 적립금환불 요청 시
				}
			}
			// 부분취소에 사용 가능한 포인트가 없을 경우
			else {
				$__cancelTotal = array('pg'=>0,'point'=>0); $__console = "type8"; // 적립금환불 요청 시
			}

		}

	}// 포인트 환불
