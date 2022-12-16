<?php

	/*
		* 결제 승인 취소
			http://도메인/addons/payple/cancel.php
	*/

	ini_set('memory_limit','-1');



	include_once( dirname(__FILE__) ."/inc.php" );

	
	$PCD_result_trigger = "N"; // 취소 성공여부


	// ----- 결제 취소를 위한 인증 -----
		// URL 불러오기
		$url = $app_payple_domain . '/php/auth.php';

		// 헤더정보
		$header_data = array();
		// 가맹점 인증 API를 요청하는 서버와 결제창을 띄우는 서버가 다른 경우 또는 AWS 이용 가맹점인 경우 REFERER에 도메인을 넣어주세요.
		$header_data[] = "Expires: Mon 26 Jul 1997 05:00:00 GMT";
		$header_data[] = "Last-Modified: " . gmdate("D, d, M Y H:i:s") . " GMT";
		$header_data[] = "Cache-Control: no-store, no-cache, must-revalidate";
		$header_data[] = "Cache-Control: post-check=0; pre-check=0";
		$header_data[] = "Pragma: no-cache";
		$header_data[] = "cache-control: no-cache";
		$header_data[] = "content-type: application/json; charset=UTF-8";
		$header_data[] = "referer: ". ($siteInfo['s_payple_mode'] == 'test' ? "http://localhost:80" : $system['url']) ; // 테스트일 경우 로컬, 실운영일 경우 페이플에 등록한 도메인


		// 발송 데이터 정보
		$post_data = array();
		$post_data["cst_id"] = $siteInfo['s_payple_cst_id'];// 가맹점 ID(cst_id)
		$post_data["custKey"] = $siteInfo['s_payple_custKey'];// 가맹점 운영 Key(custKey)
		$post_data["PCD_PAYCANCEL_FLAG"] = "Y"; // 승인취소 API 사용할 때 필수

		// API CURL 연동
		$res = ApiCurl( $url , $post_data , $header_data);

		// 취소시에는 $res['json']으로 리턴받음
		$PCD_result = $res['json']->result ;
		$PCD_CST_ID = $res['json']->cst_id ;
		$PCD_CUST_KEY = $res['json']->custKey ;
		$PCD_AUTH_KEY = $res['json']->AuthKey ;
		$PCD_PAY_HOST = $res['json']->PCD_PAY_HOST ;
		$PCD_PAY_URL = $res['json']->PCD_PAY_URL ;
	// ----- 결제 취소를 위한 인증 -----


	// ----- 결제 취소실행 -----
	if($PCD_result == "success") {

		// URL 불러오기
		$url = $PCD_PAY_HOST . $PCD_PAY_URL;

		$header_data = array();
		$header_data[] = "cache-control: no-cache";
		$header_data[] = "content-type: application/json; charset=UTF-8";

		// 발송 데이터 정보
		$post_data = array();
		$post_data["PCD_CST_ID"] = $PCD_CST_ID; // 가맹점 인증 후 리턴받은 cst_id
		$post_data["PCD_CUST_KEY"] = $PCD_CUST_KEY; // 가맹점 인증 후 리턴받은 custKey
		$post_data["PCD_AUTH_KEY"] = $PCD_AUTH_KEY; // 가맹점 인증 후 리턴받은 AuthKey
		$post_data["PCD_REFUND_KEY"] = $siteInfo['s_payple_cancelKey']; // 취소 연동키
		$post_data["PCD_PAYCANCEL_FLAG"] = "Y";
		$post_data["PCD_PAY_OID"] = $_ordernum; // 주문번호
		$post_data["PCD_PAY_DATE"] = $PCD_PAY_DATE; // 취소할 원거래일자
		$post_data["PCD_REFUND_TOTAL"] = $PCD_REFUND_TOTAL; // 승인취소 요청금액

		// API CURL 연동
		$res = ApiCurl( $url , $post_data , $header_data);

		if($res['json']->PCD_PAY_RST == "success"){
			$PCD_result_trigger = "Y"; // 취소 성공여부
		}
		// 취소 메시지
		$res_msg = $res['json']->PCD_PAY_CODE . " : " . $res['json']->PCD_PAY_MSG ;

		/*
			stdClass Object
			(
				[PCD_PAY_RST] => success
				[PCD_PAY_CODE] => PAYC0000
				[PCD_PAY_MSG] => 환불성공
				[PCD_PAY_OID] => 92150-14320-60337
				[PCD_PAY_TYPE] => card
				[PCD_PAYER_NO] => 0
				[PCD_PAYER_ID] => cFNLN3dRZ3UybnoyZnhMT25PWmkwQT09
				[PCD_PAY_YEAR] => 
				[PCD_PAY_MONTH] => 
				[PCD_PAY_GOODS] => [복사] 테스트 상품
				[PCD_REGULER_FLAG] => N
				[PCD_REFUND_TOTAL] => 1000
				[PCD_REFUND_TAXTOTAL] => 0
				[PCD_PAY_CARDTRADENUM] => 202104061122379668844400
				[PCD_PAY_CARDRECEIPT] => https://www.danalpay.com/receipt/creditcard/view.aspx?dataType=receipt&cpid=9810030929&data=AT%2Foz6aPH0%2BUwyzYmzSvQSSyMN1o2aEFOq0cnbFPUUafiXSmJ32VxjO9QFDc2KP3
			)
		*/
	}