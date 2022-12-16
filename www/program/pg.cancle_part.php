<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



//$_ordernum  - 주문번호
//$_paymethod - 결제타입
// $_apply_point - 연동여부
// $_applytype - 실행타입 : admin , member ::: member 일 경우 반드시 get_userid() 있어야 함
$_trigger = "Y"; // 처리형태

$ordr = _MQ("
	SELECT * FROM smart_order_product as op
	inner join smart_order as o on (o.o_ordernum = op.op_oordernum)
	WHERE o.o_ordernum='" . $_ordernum . "' and op_uid = '".$_uid."'
");
$r = _MQ("select * from smart_order_cardlog where oc_oordernum='".$_ordernum."' AND oc_tid !='' order by oc_uid desc limit 1");

// SSJ : 주문검색 실패 시 오류 처리 : 2021-02-18
if($ordr['o_ordernum'] == ''){
	$_trigger = "N"; // 처리형태
}else{
	// - 카드결제/계좌이체 취소 ---
	if( in_array($ordr['o_paymethod'] , $arr_cancel_part_payment_type ) && $ordr['o_paystatus']=='Y') {
		if($_force_cancel || $_total_amount < 1 || $ordr['op_cancel_type'] == 'point') {
			$is_pg_status = true;
		}
		else {
			// 결제 취소를 위한 거래 정보 호출
			switch($siteInfo['s_pg_type']) {
				case "lgpay" :
					//require(OD_PROGRAM_ROOT."/pg.cancle_part_lgpay.php");
					// SSJ : 토스페이먼츠 PG 모듈 교체 : 2021-02-22
					require(OD_PROGRAM_ROOT."/pg.cancle_part_toss.php");
					break;
				case "kcp" :
					require(OD_PROGRAM_ROOT."/pg.cancle_part_kcp.php");
					break;
				case "inicis" :
					require(OD_PROGRAM_ROOT."/pg.cancle_part_inicis.php");
					break;
				case "allthegate" :
					require(OD_PROGRAM_ROOT."/pg.cancle_part_allthegate.php");
					break;
				case "billgate" :
					$_paymethod = $ordr['o_paymethod'];
					if($_paymethod=='iche') {
						require(OD_PROGRAM_ROOT."/pg.cancle_part_billgate.account.php");
					} else {
						require(OD_PROGRAM_ROOT."/pg.cancle_part_billgate.php");
					}
				break;
				case "daupay" :
					require(OD_PROGRAM_ROOT."/pg.cancle_part_daupay.php");
				break;
			}
		}

		if ($is_pg_status) {	// pg모듈 호출 상태

			// 2018-11-19 SSJ :: 단일 상품 재고 증가 및 판매량 차감 :: $_ordernum , $_uid
			include(OD_PROGRAM_ROOT."/shop.order.salecntdel_part.php");

			_MQ_noreturn(" update smart_order_product set
				op_cancel = 'Y',
				op_cancel_returnmsg = '".$_result_msg."',
				op_cancel_tid = '".$_result_tid."',
				op_cancel_cdate = now()
				where op_oordernum = '".$_ordernum."' and op_uid = '".$_uid."'
			");

			// 추가옵션 취소처리
			$add_res = _MQ(" select count(*) as cnt from smart_order_product where op_is_addoption = 'Y' and op_addoption_parent = '".$ordr['op_pouid']."' and op_oordernum = '".$_ordernum."' ");
			if( $add_res['cnt'] > 0 ) {
				_MQ_noreturn(" update smart_order_product set
					op_cancel = 'Y',
					op_cancel_returnmsg = '".$_result_msg."',
					op_cancel_tid = '".$_result_tid."',
					op_cancel_cdate = now()
					where op_is_addoption = 'Y' and op_addoption_parent = '".$ordr['op_pouid']."' and op_oordernum = '".$_ordernum."'
				");
			}

			// 마지막 부분취소인지 체크
			$tmp = _MQ(" select count(*) as cnt from smart_order_product where op_cancel!='Y' and op_oordernum = '".$_ordernum."' ");

			// 문자 발송
			$sms_to = $ordr['o_ohp'] ? $ordr['o_ohp'] : $ordr['o_otel'];
			if($tmp['cnt']==0) { // 마지막 부분취소일 경우 주문 전체 취소
				$smskbn = "order_cancel";   // 문자 발송 유형
			}else{
				$smskbn = "order_cancel_part";  // 문자 발송 유형
			}
			$sms_pname = trim($ordr['op_pname']) . implode(" " , array_filter(array(' '.$ordr['op_option1'],$ordr['op_option2'],$ordr['op_option3'])));
			$arr_sms_replace = array('{주문번호}'=>$_ordernum, '{주문상품명}'=>$sms_pname);
			shop_send_sms($sms_to,$smskbn,$arr_sms_replace);

			// 마지막 부분취소일 경우 주문 전체 취소
			if($tmp['cnt']==0) {
				// 제공변수 : $_ordernum
				include(OD_PROGRAM_ROOT."/shop.order.pointdel_pro.php");
				// - 적용된 포인트, 쿠폰적용 취소 ---
				_MQ_noreturn("update smart_order set o_canceled='Y' , o_canceldate = now() , o_cancel_mem_type = 'admin' where o_ordernum='{$_ordernum}' ");
			}
			else{
				// SSJ : 정산 할인금액 패치 : 2021-05-14 -- 상품쿠폰 금액 다른 동일 상품으로 이전
				if($ordr['op_use_product_coupon'] > 0){
					$npr = _MQ(" SELECT op_uid FROM smart_order_product WHERE op_oordernum='" . $_ordernum . "' and op_pcode = '".$ordr['op_pcode']."' and op_uid != '".$ordr['op_uid']."' and op_cancel = 'N' order by op_uid asc limit 1 ");
					if($npr['op_uid'] > 0){
						_MQ_noreturn(" update smart_order_product set op_use_product_coupon = 0 where op_uid = '". $ordr['op_uid'] ."' "); // 취소주문 상품쿠폰 사용액 0 처리
						_MQ_noreturn(" update smart_order_product set op_use_product_coupon = '". $ordr['op_use_product_coupon'] ."' where op_uid = '". $npr['op_uid'] ."' "); // 다른 주문상품으로 이동
					}
				}
			}

			// 주문발송 상태 변경
			order_status_update($_ordernum);

			$_trigger = "Y"; // 처리형태
		}
		else {
			$_trigger = "N"; // 처리형태
		}
	}
	// - 카드결제/계좌이체 취소 ---

	// - 무통장입금 취소 ---
	else {

		$ool_bank_name_array = $ksnet_bank;

		// SSJ : 토스페이먼츠 PG 모듈 교체 : 2021-02-22
		//if($ordr['o_paymethod']=='virtual') { if(!isset($v_cnt)) { $v_cnt = 0; }
		//	// LGU+ 가상계좌 반납처리
		//	switch($siteInfo['s_pg_type']) {
		//		case "lgpay" :
		//			$LGD_TID		= $r['oc_tid'];
		//			$CST_PLATFORM	= $siteInfo['s_pg_mode'];
		//			$CST_MID		= $siteInfo['s_pg_code'];
		//			$LGD_MID		= (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
		//			$configPath		= PG_DIR . "/lgpay/lgdacom";
		//			require_once(PG_DIR. "/lgpay/lgdacom/XPayClient.php");
		//			$xpay = &new XPayClient($configPath, $CST_PLATFORM); $xpay->Init_TX($LGD_MID);
		//			$xpay->Set("LGD_TXNAME", "Settlement");
		//			$xpay->Set("LGD_TID", $LGD_TID);
		//			$is_pg_status = $xpay->TX();
		//			break;
		//	}
		//}

		// 2018-11-19 SSJ :: 단일 상품 재고 증가 및 판매량 차감 :: $_ordernum , $_uid
		include(OD_PROGRAM_ROOT."/shop.order.salecntdel_part.php");

		_MQ_noreturn(" update smart_order_product set
			op_cancel = 'Y',
			op_cancel_cdate = now()
			where op_oordernum = '".$_ordernum."' and op_uid = '".$_uid."'
		");

		// 추가옵션 취소처리
		$add_res = _MQ(" select count(*) as cnt from smart_order_product where op_is_addoption = 'Y' and op_addoption_parent = '".$ordr['op_pouid']."' and op_oordernum = '".$_ordernum."' ");
		if( $add_res['cnt'] > 0 ) {
			_MQ_noreturn(" update smart_order_product set
				op_cancel = 'Y',
				op_cancel_cdate = now()
				where op_is_addoption = 'Y' and op_addoption_parent = '".$ordr['op_pouid']."' and op_oordernum = '".$_ordernum."'
			");
		}

		// 마지막 부분취소인지 체크
		$tmp = _MQ(" select count(*) as cnt from smart_order_product where op_cancel!='Y' and op_oordernum = '".$_ordernum."' ");

		// 문자 발송
		$sms_to = $ordr['o_ohp'] ? $ordr['o_ohp'] : $ordr['o_otel'];
		if($tmp['cnt']==0) { // 마지막 부분취소일 경우 주문 전체 취소
			$smskbn = "order_cancel";   // 문자 발송 유형
		}else{
			$smskbn = "order_cancel_part";  // 문자 발송 유형
		}
		$sms_pname = trim($ordr['op_pname']) . implode(" " , array_filter(array(' '.$ordr['op_option1'],$ordr['op_option2'],$ordr['op_option3'])));
		$arr_sms_replace = array('{주문번호}'=>$_ordernum, '{주문상품명}'=>$sms_pname);
		shop_send_sms($sms_to,$smskbn,$arr_sms_replace);

		// 마지막 부분취소일 경우 주문 전체 취소
		if($tmp['cnt']==0) {
			// 제공변수 : $_ordernum
			include(OD_PROGRAM_ROOT."/shop.order.pointdel_pro.php");

			// {{{회원쿠폰}}} --무통장의 경우 결제전일경우에 쿠폰처리
			if( $ordr[o_paystatus] == "N" && $ordr[o_canceled] == "N" ){
				if($ordr['o_coupon_individual_uid']){
					_MQ_noreturn("update smart_individual_coupon set coup_use ='N', coup_usedate = NULL where  find_in_set(coup_uid, '".$ordr[o_coupon_individual_uid]."') > 0 and coup_use = 'W'  ");

				}
			}
			// {{{회원쿠폰}}}

			// - 적용된 포인트, 쿠폰적용 취소 ---
			_MQ_noreturn("update smart_order set o_canceled='Y' , o_canceldate = now() , o_cancel_mem_type = 'admin' where o_ordernum='{$_ordernum}' ");

			if($ordr[o_get_tax]=='Y') { // 현금영수증 취소
				$method = 'CANCEL';
				$paymethod = $ordr['o_paymethod'];
				$ordernum = $_ordernum;
				$tid = $r['oc_tid'];
				$amount = $ordr['o_price_real'];
				include(OD_PROGRAM_ROOT."/totalCashReceipt.php");
			}
		}
		else{
			// SSJ : 정산 할인금액 패치 : 2021-05-14 -- 상품쿠폰 금액 다른 동일 상품으로 이전
			if($ordr['op_use_product_coupon'] > 0){
				$npr = _MQ(" SELECT op_uid FROM smart_order_product WHERE op_oordernum='" . $_ordernum . "' and op_pcode = '".$ordr['op_pcode']."' and op_uid != '".$ordr['op_uid']."' and op_cancel = 'N' order by op_uid asc limit 1 ");
				if($npr['op_uid'] > 0){
					_MQ_noreturn(" update smart_order_product set op_use_product_coupon = 0 where op_uid = '". $ordr['op_uid'] ."' "); // 취소주문 상품쿠폰 사용액 0 처리
					_MQ_noreturn(" update smart_order_product set op_use_product_coupon = '". $ordr['op_use_product_coupon'] ."' where op_uid = '". $npr['op_uid'] ."' "); // 다른 주문상품으로 이동
				}
			}
		}

		// 주문발송 상태 변경
		order_status_update($_ordernum);

		$_trigger = "Y"; // 처리형태
	}
}




actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행