<?php

	actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


	$tmp_ordernum = $_ordernum; // 주문번호 저장

	// 회원일경우 사용한 포인트 반환처리 하고, 연동 취소 처리한다.
	$osr = get_order_info($_ordernum);

	if( $osr['o_memtype']=="Y" && $osr['o_apply_point'] == "Y" ) { // 연동처리 된 상태라면..

		// ---------- JJC : 부분취소 개선 : 2021-02-10  ----------
		// 지급된 적립금 회수
		$total_buy_point=_MQ("select pl_point from smart_point_log where pl_status = 'Y' AND pl_inid='".$osr['o_mid']."' and  pl_title='구매 적립금 적용 (주문번호 : {$_ordernum})'"); // 구매시 적립된 적립금
		$cancel_buy_point = _MQ("select sum(pl_point) as sum_point from smart_point_log where pl_status = 'Y' AND pl_inid='".$osr['o_mid']."' and pl_title='[취소]구매 적립금 적용 (주문번호 : {$_ordernum})'"); // 취소된 적립금 금액의 합
		$left_use_point =  abs($cancel_buy_point['sum_point']) - $total_buy_point['pl_point'];// 반환 해줘야할 남은 적립금 음수로 나오기 위해 취소된 금액에서 총 금액 빼기
		if( $left_use_point < 0 ){
			shop_pointlog_insert( $osr['o_mid'] , "[취소]구매 적립금 적용 (주문번호 : {$_ordernum})" , $left_use_point , "N" , 0); // 남은 구매시 적립된 적립금 취소
		}

		// 사용 적립금 반환
		$total_use_point=_MQ("select pl_point from smart_point_log where pl_status = 'Y' AND pl_inid='".$osr['o_mid']."' and  pl_title='주문 시 적립금 사용 (주문번호 : {$_ordernum})'"); // 주문시 사용한 적립금
		$cancel_use_point = _MQ("select sum(pl_point) as sum_point from smart_point_log where pl_status = 'Y' AND pl_inid='".$osr['o_mid']."' and pl_title='주문취소에 따른 사용 적립금반환 (주문번호 : {$_ordernum})'"); // 취소된 적립금 금액의 합
		$left_point = abs($total_use_point['pl_point']) - $cancel_use_point['sum_point'];// 반환 해줘야할 남은 적립금 음수로 나오기 위해 취소된 금액에서 총 금액 빼기
		if( $left_point > 0 ){
			shop_pointlog_insert( $osr['o_mid'] , "주문취소에 따른 사용 적립금반환 (주문번호 : {$_ordernum})" , $left_point , "N" , 0); // 남은 사용 적립금 반환
		}

		//switch ($osr['o_paycancel_method']) {
		//	case 'D': // 환불 방식이 분배 일때 구매시 사용적립금 계산
		//		$total_use_point=_MQ("select pl_point from smart_point_log where pl_status = 'Y' AND pl_inid='".$osr['o_mid']."' and  pl_title='주문 시 적립금 사용 (주문번호 : {$_ordernum})'"); // 주문시 사용한 적립금
		//		$cancel_use_point = _MQ("select sum(pl_point) as sum_point from smart_point_log where pl_status = 'Y' AND pl_inid='".$osr['o_mid']."' and pl_title='주문취소에 따른 사용 적립금반환 (주문번호 : {$_ordernum})'"); // 취소된 적립금 금액의 합
		//		$left_point = abs($total_use_point['pl_point']) - $cancel_use_point['sum_point'];// 반환 해줘야할 남은 적립금 음수로 나오기 위해 취소된 금액에서 총 금액 빼기
		//		if( $left_point > 0 ){
		//			shop_pointlog_insert( $osr['o_mid'] , "주문취소에 따른 사용 적립금반환 (주문번호 : {$_ordernum})" , $left_point , "N" , 0); // 남은 사용 적립금 반환
		//		}
		//	break;
		//	case 'B':
		//		$part_cancel_chk = _MQ_result(" select count(*) from smart_order_product where op_oordernum = '".$_ordernum."' and op_cancel != 'N' ");
		//		if( $part_cancel_chk == 0 ) {
		//			shop_pointlog_insert( $osr['o_mid'] , "주문취소에 따른 사용 적립금반환 (주문번호 : {$_ordernum})" , $osr['o_price_usepoint'] , "N" , 0);// 사용한 적립금 반환
		//		}
		//	break;
		//}

		//{{{회원쿠폰}}} - 쿠폰 사용취소
		if($osr['o_coupon_individual_uid']){
			if( $siteInfo['s_coupon_ordercancel_return'] == 'Y'){ // 쿠폰설정중 주문취소에 따른 쿠폰이 복원일경우
				_MQ_noreturn("update smart_individual_coupon set coup_use ='N', coup_usedate = NULL where  find_in_set(coup_uid, '".$osr[o_coupon_individual_uid]."') > 0 ");
			}
		}
		//{{{회원쿠폰}}}

		// 지급대기 포인트 회수 --> 상단에서 선처리
		//			- 지급대기 상태일 경우 지급취소로 상태값만 변경
		$chk =_MQ("SELECT COUNT(*) AS cnt FROM smart_point_log WHERE pl_status = 'N' AND pl_inid='".$osr['o_mid']."' and  pl_title='구매 적립금 적용 (주문번호 : {$_ordernum})'"); // 구매시 적립된 적립금
		if($chk['cnt'] > 0 ) {
			shop_pointlog_delete( $osr['o_mid'] , "구매 적립금 적용 (주문번호 : {$_ordernum})");
		}
		// ---------- JJC : 부분취소 개선 : 2021-02-10  ----------

	}


	/***
	* 주문정보로 현금영수증 발행
	* > 현금영수증이 발행된 주문만
	**/
	if($osr['o_paymethod'] == 'online' && $osr['o_get_tax'] == 'Y'){

		$query = "
				select
					o.o_ordernum, o.o_ohp, o.o_price_real, o.o_oemail, o.o_price_refund, o.o_price_usepoint_refund
					,o.o_tax_TradeUsage,o.o_tax_TradeMethod,o.o_tax_IdentityNum
					,(select count(*) from smart_order_product as op2 where op2.op_oordernum=o.o_ordernum and op2.op_cancel = 'N') as op_cnt
					,(
						select
							p2.p_name
						from smart_order_product as op3
						left join smart_product  as p2 on ( p2.p_code=op3.op_pcode )
						where op3.op_oordernum=o.o_ordernum and op3.op_cancel = 'N' order by op3.op_uid asc limit 1
					) as op_pname
					, bc.MgtKey, bc.BarobillState
				from smart_order as o
				left join smart_order_product as op on (op.op_oordernum = o.o_ordernum)
				left join smart_baro_cashbill as bc on (bc.bc_ordernum = o.o_ordernum and bc.bc_isdelete = 'N' and bc.bc_iscancel = 'N' and bc.BarobillState in ('2000','3000'))
				where 1
					and o.o_paymethod = 'online'
					and (o.o_status != '배송완료' or o.o_canceled = 'Y')
					and op.op_is_cashbill = 'Y'
					and bc.BarobillState in ('2000','3000')
					and o.o_ordernum = '". $_ordernum ."'
		";
		$order_info = _MQ($query);

		// -- 발행정보 ----------------
		if($order_info['BarobillState']=='2000'){
			$app_mode = "cancelbeforesend";
		}else{
			$app_mode = "cancel";
		}
		$_key = $order_info['MgtKey'];
		// -- 발행정보 ----------------
		$_ordernum = $order_info['o_ordernum'];
		$no_msg = true; // 경고창 및 페이지이동방지
		$_trigger_cashbill = false; // 결과저장변수
		$_result_text = "";
		include OD_ADMIN_ROOT.'/_cashbill.pro.php';
	}

	$_ordernum = $tmp_ordernum; // 주문번호 원복

	// 연동 취소 처리
	_MQ_noreturn("update smart_order set o_apply_point='N' where o_ordernum='".$_ordernum."' ");


	actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행