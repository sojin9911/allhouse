<?php

	// ----- SSJ : 2020-07-01 : 접수완료/결제취소 일괄처리 -----

	// 공통취소파일 - include 사용
	//			넘길변수
	//					-> 주문번호 : $ordernum
	//			return 정보
	//					-> 성공여부 : pay_status = Y/N
	//					-> 메시지 : pay_msg
	$pay_status = $pay_msg = "";

	actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행





	if($pay_status <> "N") {

		$__sque = "update smart_order set o_paystatus='Y' , o_status='접수완료', o_paydate=now() where o_ordernum='". $ordernum ."' ";
		_MQ_noreturn($__sque);

		$__sque = "update smart_order_product set op_paydate=now() where op_oordernum='". $ordernum ."' ";
		_MQ_noreturn($__sque);


		// 상품 재고 차감 및 판매량 증가
		$_ordernum = $ordernum;
		include(OD_PROGRAM_ROOT."/shop.order.salecntadd_pro.php");


		// 결제가 확인되었을 경우 - 포인트 쿠폰 - 적용
		// 제공변수 : $_ordernum
		$_ordernum = $ordernum;
		include(OD_PROGRAM_ROOT."/shop.order.pointadd_pro.php");


		// 주문정보 보정
		if(!$r) $r = $order_info;
		if(!$r) $r = get_order_info($_ordernum);


		// 문자 발송
		$sms_to = $r['o_ohp'] ? $r['o_ohp'] : $r['o_otel'];
		shop_send_sms($sms_to,"order_pay",$_ordernum);


		// - 메일발송 ---
		$_oemail = $r['o_oemail'];
		if( mailCheck($_oemail) ){
			$_ordernum = $ordernum;
			$_type = "card"; // 결제확인처리
			include_once(OD_PROGRAM_ROOT."/shop.order.mail.php"); // 메일 내용 불러오기 ($mailing_content)
			$_title = "[".$siteInfo['s_adshop']."]주문하신 상품의 결제가 성공적으로 완료되었습니다!";
			$_content = $mailing_app_content;
			$_content = get_mail_content($_content);
			mailer( $_oemail , $_title , $_content );
		}
		// - 메일발송 ---


		// 2018-12-12 SSJ :: 주문상태 업데이트 추가
		order_status_update($_ordernum);


		// 장바구니 정보 삭제 - 무통장결제는 shop.order.pro.php에서처리 SSJ : 2018-03-22
		if($_COOKIE["AuthShopCOOKIEID"]) {
			_MQ_noreturn(" delete from smart_cart where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' and c_direct='Y'  ");
		}

		$pay_status = "Y"; // 성공처리
		$pay_msg = "결제를 완료하였습니다.";
	}


	actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행