<?php
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행




// *** 결제확인 시 --> 포인트 / 쿠폰 등 적용 ***

// - 주문정보 추출 ---
$osr = get_order_info($_ordernum);

if( $osr['o_memtype']=="Y" && $osr['o_apply_point'] == "N") {

	// 무통장/적립금결제 주문시, 회원 포인트와 쿠폰이 유효한지 검증한다.
	if($osr['o_paymethod'] == "online" || $osr['o_paymethod'] == "point") {
		// 포인트
		$ind_info = _MQ("select in_point from smart_individual where in_id='".$osr[o_mid]."'");
		if($ind_info['in_point'] < $osr['o_price_usepoint']) error_msg("보유 적립금이 충분하지 않습니다.");
	}

	// 사용한 적립금 차감
	shop_pointlog_insert( $osr['o_mid'] , "주문 시 적립금 사용 (주문번호 : {$_ordernum})" , $osr['o_price_usepoint'] * -1 , "N" , 0);

	// 구매상품 적립금 지급
	shop_pointlog_insert( $osr['o_mid'] , "구매 적립금 적용 (주문번호 : {$_ordernum})" , $osr['o_price_supplypoint'] , "N" , $siteInfo['s_orderpointprodate']);

	//{{{회원쿠폰}}} - 쿠폰 사용처리
	if($osr['o_coupon_individual_uid']) _MQ_noreturn("update smart_individual_coupon set coup_use ='Y', coup_usedate = now() where find_in_set(coup_uid, '".$osr[o_coupon_individual_uid]."') > 0   ");
	//{{{회원쿠폰}}}
}
// -- 포인트 사용량에 따른  ---



/***
* 주문정보로 현금영수증 발행
* > 무통장입금이고 현금영수증발행요청이 들어온 주문일경우
**/
if($osr['o_paymethod'] == 'online' && $osr['o_get_tax'] == 'Y'){

	// 2020-03-23 SSJ :: 현금영수증 면세, 복합과세 패치 ---- 주문금액 과세/면세 구분
	// 입점업체정보 배열 추출
	$partner = array();
	$cp_row = _MQ_assoc("
		select
			op.op_partnerCode,
			cp.cp_vat_delivery
		from
			smart_company as cp left join
			smart_order_product as op on (op.op_partnerCode = cp.cp_id)
		where
			op.op_oordernum='{$_ordernum}' and op.op_cancel = 'N'
	");
	foreach($cp_row as $sk=>$sv) {
		if($siteInfo['s_vat_delivery'] <> 'C' || $SubAdminMode === false) $sv['cp_vat_delivery'] = $siteInfo['s_vat_delivery'];
		$partner[$sv['op_partnerCode']] = $sv['cp_vat_delivery'];
	}
	// 입점업체정보 배열 추출

	// 주문정보 호출
	$pr = _MQ_assoc("
		select
			op.*
		from
			smart_order_product as op
		where (1) and
			op.op_oordernum='{$_ordernum}' and op.op_cancel = 'N'
	");
	$data2 = array();
	foreach($pr as $sk=>$sv) {

		// 과세
		if($sv['op_vat'] == 'Y') {
			$data2['vatY'] += $sv['op_price'] * $sv['op_cnt'] - $sv['op_usepoint'];
		}
		// 면세
		else if($sv['op_vat'] == 'N') {
			$data2['vatN'] += $sv['op_price'] * $sv['op_cnt'] - $sv['op_usepoint'];
		}

		// 배송비 과세
		if($partner[$sv['op_partnerCode']] == 'Y') {
			$data2['vatY'] += $sv['op_delivery_price'] + $sv['op_add_delivery_price'];
		}
		// 배송비 면세
		else {
			$data2['vatN'] += $sv['op_delivery_price'] + $sv['op_add_delivery_price'];
		}
	}
	// 2020-03-23 SSJ :: 현금영수증 면세, 복합과세 패치 ---- 주문금액 과세/면세 구분

	// JJC : 주문 취소항목 추출 : 2018-01-04
	$add_que = implode(", " , array_keys($arr_order_cancel_field));

	$query = "
			select
				o.o_ordernum, o.o_ohp, o.o_price_real, o.o_oemail, ". $add_que ."
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
			left join smart_order_product as op on (op.op_oordernum = o.o_ordernum and op.op_cancel != 'Y')
			left join smart_baro_cashbill as bc on (bc.bc_ordernum = o.o_ordernum and bc.bc_isdelete = 'N' and bc.bc_iscancel = 'N' and bc.BarobillState in ('2000','3000'))
			where 1
				and o.o_paymethod = 'online'
				and o.o_paystatus = 'Y'
				and o.o_canceled = 'N'
				and o.o_get_tax = 'Y'
				and op.op_cancel = 'N'
				and op.op_is_cashbill = 'N'
				and o.o_ordernum = '". $_ordernum ."'
	";
	$order_info = _MQ($query);

	// -- 발행정보 ----------------
	$app_mode = "issue";
	$_uid = ""; // 내부에서 _uid변수가 설정되므로 반드시 초기화
	$_ordernum = $order_info['o_ordernum'];
	$TradeUsage = $order_info['o_tax_TradeUsage'] ? $order_info['o_tax_TradeUsage'] : 1; // 1:소득공제
	$TradeMethod = $order_info['o_tax_TradeMethod'] ? $order_info['o_tax_TradeMethod'] : 4; // 4:휴대폰번호
	$IdentityNum = rm_str(($order_info['o_tax_IdentityNum'] ? onedaynet_decode($order_info['o_tax_IdentityNum']) : $order_info['o_ohp']));

	// JJC : 주문 취소항목 추가 : 2018-01-04
	$add_cancel_price = 0;
	foreach($arr_order_cancel_field as $cfk=>$cfv){ $add_cancel_price += $order_info[$cfk]; }
	$totalPrice = $order_info["o_price_real"] - $add_cancel_price; // 판매금액
	// 2020-03-23 SSJ :: 현금영수증 면세, 복합과세 패치 ---- 과세 부분만 부가세 계산
	$Tax = $data2['vatY'] - ceil($data2['vatY']/1.1);
	$Amount = $totalPrice - $Tax;  // 공급가액


	$ServiceCharge = 0;
	$ItemName = $order_info['op_pname'] . ($order_info['op_cnt'] > 1 ? " 외 ". ($order_info['op_cnt'] - 1) ."개" : null);
	$Email = $order_info['o_oemail'];
	$Amount = ($Amount > 0 ? $Amount : 0);
	$Tax = ($Tax > 0 ? $Tax : 0);
	$ServiceCharge = ($ServiceCharge > 0 ? $ServiceCharge : 0);
	// -- 발행정보 ----------------

	$no_msg = true; // 경고창 및 페이지이동방지
	$_trigger_cashbill = false; // 결과저장변수
	$_result_text = "";
	include OD_ADMIN_ROOT.'/_cashbill.pro.php';
}




// 결제 확인에 따른 포인트, 쿠폰등의 적용 처리
_MQ_noreturn("update smart_order set o_apply_point='Y' where o_ordernum='".$_ordernum."' ");


//LCY::COUPON 최종 결제확인에 따른 쿠폰발급 처리
couponIssuedAutoType1($_ordernum); // 첫구매/결제 완료
couponIssuedAutoType2($_ordernum); // 구매/결제 완료



actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행