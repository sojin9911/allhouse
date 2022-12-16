<?php

	// 2017-06-16 ::: 부가세율설정 - 배송비 과세 / 면세 비용 계산 ::: JJC
	//		- 적용파일
	//				shop.order.result.php
	//		- 넘겨받은 변수
	//				$ordernum = $ordernum; // --> 주문번호
	//				$order_row = $v; // --> 주문배열정보



	// 2017-06-16 ::: 부가세율설정 - 배송비 과세/면세 비용 저장 - 입점업체 미적용시 - cp_id = master 으로 적용 ::: JJC
	$arr_deli_vattype_price = array();

	// 2017-06-16 ::: 부가세율설정 - 업체별 배송비 부가세 설정 여부 정보 추출  ::: JJC
	$arr_com_deli_condition = array();
	$arr_com_deli_condition['master'] = array('delivery_use' => 'Y', 'vat_delivery' => $siteInfo['s_vat_delivery']); // 기본 master (전체관리자) 적용
	$cprow = _MQ_assoc("select cp_id , cp_delivery_use , cp_vat_delivery from smart_company ");
	foreach($cprow as $k=>$v){
		$arr_com_deli_condition[$v['cp_id']] = array('delivery_use' => $v['cp_delivery_use'], 'vat_delivery' => $v['cp_vat_delivery']);
	}


	// 2017-06-16 ::: 부가세율설정 - 과세 / 면세 비용저장 ::: JJC
	// 예) $arr_vattype_price['Y'] = 20000;  //과세
	// 예) $arr_vattype_price['N'] = 10000; // 면세
	$arr_vattype_price = array();



	$opres = _MQ_assoc("
		select *
		from smart_order_product as op
		inner join smart_product as p on ( p.p_code=op.op_pcode )
		where op_oordernum='{$ordernum}'
	");
	foreach($opres as $sk=>$sv) {

		// 2017-06-16 ::: 부가세율설정 - 과세 / 면세 비용저장 ::: JJC
		$arr_vattype_price[$sv['op_vat']] += $sv['op_price'] * $sv['op_cnt'];
		// 2017-06-16 ::: 부가세율설정 - 과세 / 면세 비용저장 ::: JJC

		// 2017-06-16 ::: 부가세율설정 - 배송비 과세 / 면세 비용저장 ::: JJC
		// 배송비 정책 사용할 경우 - 입점업체의 배송비 부가세 적용여부를 따져사 Y or N 배열에 배송비 합계액 저장
		if($siteInfo['s_vat_delivery'] == 'C') {
			$arr_deli_vattype_price[$arr_com_deli_condition[$sv['op_partnerCode']]['vat_delivery']] += $sv['op_delivery_price'] + $sv['op_add_delivery_price'];
		}
		// 배송비 정책 사용할 경우 - 전체 배송비 부가세 적용여부를 따져사 Y or N 배열에 배송비 합계액 저장
		else {
			$arr_deli_vattype_price[$arr_com_deli_condition['master']['vat_delivery']] += $sv['op_delivery_price'] + $sv['op_add_delivery_price'];
		}
		// 2017-06-16 ::: 부가세율설정 - 배송비 과세 / 면세 비용저장 ::: JJC

	}
	// 옵션처리끝

	// 상품 과세비용 : $arr_vattype_price['Y']
	// 상품 면세비용 : $arr_vattype_price['N']
	// 배송 과세비용 : $arr_deli_vattype_price['Y']
	// 배송 면세비용 : $arr_deli_vattype_price['N']

	// 과세비용
		$app_vat_Y = $arr_vattype_price['Y'] + $arr_deli_vattype_price['Y'];
	// 면세비용
		$app_vat_N = $arr_vattype_price['N'] + $arr_deli_vattype_price['N'];
	// 할인비용
		$app_discount = $order_row['o_price_total'] + $order_row['o_price_delivery'] - $order_row['o_price_real'];
	// 결제비용 : $order_row['o_price_real']
		$app_pay = $order_row['o_price_real'];

	// 할인액 과세설정 따른 구분  - 할인액이 있어야 함..
	if($app_discount > 0 ){
		switch($siteInfo['s_vat_discount']){


			// 과세부터 차감
			case "Y":
				if($app_vat_Y < $app_discount ) {
					$app_vat_Y = 0 ;
					$app_vat_N -= ($app_discount - $app_vat_Y);
				}
				else {
					$app_vat_Y -= $app_discount;
				}
				break; // 과세부터 차감


			// 면세부터 차감
			case "N":
				if($app_vat_N < $app_discount ) {
					$app_vat_N = 0 ;
					$app_vat_Y -= ($app_discount - $app_vat_N);
				}
				else {
					$app_vat_N -= $app_discount;
				}
				break; // 면세부터 차감


			// 비율별로 차감
			case "D":
				$tmp_vat_Y = round($app_discount * $app_vat_Y / ($app_vat_Y + $app_vat_N));//할인액 - 과세비율 비용
				$app_vat_Y -= $tmp_vat_Y;
				$app_vat_N -= ($app_discount - $tmp_vat_Y);
				break; // 비율별로 차감

		}
	}// 할인액 과세설정 따른 구분  - 할인액이 있어야 함..





	// ### return ################
	// 총과세 : $app_vat_Y
	// 총면세 : $app_vat_N
	// 과세공급가 : $app_vat_Y_tot
	// 과세부가세 : $app_vat_Y_vat
	$app_vat_Y_vat = round($app_vat_Y / 11); // 과세부가세
	$app_vat_Y_tot = $app_vat_Y - $app_vat_Y_vat; // 과세공급가