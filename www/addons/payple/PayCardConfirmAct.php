<?php

	/*
		* 결제요청 재컨펌(PCD_PAY_WORK : CERT)
			http:// 도메인/addons/payple/PayCardConfirmAct.php
	*/

	ini_set('memory_limit','-1');

	//header("Expires: Mon 26 Jul 1997 05:00:00 GMT");
	//header("Last-Modified: " . gmdate("D, d, M Y H:i:s") . " GMT");
	//header("Cache-Control: no-store, no-cache, must-revalidate");
	//header("Cache-Control: post-check=0; pre-check=0", false);
	//header("Pragma: no-cache");
	//header("Content-type: application/json; charset=utf-8");

	include_once( dirname(__FILE__) ."/inc.php" );


	// URL 불러오기
	$url = $app_payple_domain . '/php/PayCardConfirmAct.php?ACT_=PAYM';


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


	// 발송 데이터 정보
	$post_data = array();
	$post_data["PCD_CST_ID"] = $siteInfo['s_payple_cst_id'];// 가맹점 ID(cst_id)
	$post_data["PCD_CUST_KEY"] = $siteInfo['s_payple_custKey'];// 가맹점 운영 Key(custKey)
	$post_data["PCD_AUTH_KEY"] = $_AUTH_KEY ;// 결제요청 후 리턴받은 PCD_AUTH_KEY
	$post_data["PCD_PAY_REQKEY"] = $_PAY_REQKEY ;// 결제요청 후 리턴받은 PCD_PAY_REQKEY
	$post_data["PCD_PAYER_ID"] = $_PAYER_ID ;// 결제요청 후 리턴받은 PCD_PAYER_ID

	// API CURL 연동
	$res = ApiCurl( $url , $post_data , $header_data);
	//	if($res['json']->PCD_PAY_RST == 'success') {
	//		echo "success";
	//	}
	//	else {
	//		echo "error";
	//	}


//    if (!isset($PayResult->PCD_PAY_RST)) {
//
//        /////////////////////////////////////////////////////////////////////////////////////
//		////////////////// throw new Exception("결제승인 결과수신 실패"); //////////////////
//		/////////////////////////////////////////////////////////////////////////////////////
//
//    }
//
//    if (isset($PayResult->PCD_PAY_RST) && $PayResult->PCD_PAY_RST != '') {
//        
//        
//    	$pay_rst = $PayResult->PCD_PAY_RST;             	// success | error
//    	$pay_msg = $PayResult->PCD_PAY_MSG;             	// 출금이체완료 | 가맹점 건당 한도 초과.., 가맹점 월 한도 초과.., 등록된 계좌정보를 찾을 수 없습니다..., 최초 결제자 입니다. 본인인증 후 이용하세요...
//    	$pay_reqkey = $PayResult->PCD_PAY_REQKEY;       	// 결제요청 고유KEY
//    	$pay_oid = $PayResult->PCD_PAY_OID;
//    	$pay_type = $PayResult->PCD_PAY_TYPE;           	// 결제방법 (transfer)
//    	$payer_no = $PayResult->PCD_PAYER_NO;           	// 결제자 고유번호 (가맹점 회원 회원번호)
//    	$pay_year = $PayResult->PCD_PAY_YEAR;           	// (정기결제) 과금 년도
//    	$pay_month = $PayResult->PCD_PAY_MONTH;         	// (정기결제) 과금 월
//    	$pay_year = $PayResult->PCD_PAY_YEAR;           	// 결제구분 년
//    	$pay_month = $PayResult->PCD_PAY_MONTH;         	// 결제구분 월
//    	$pay_goods = $PayResult->PCD_PAY_GOODS;         	// 결제 상품
//    	$pay_amount = $PayResult->PCD_PAY_AMOUNT;			// 결제요청금액
//    	$pay_discount = $PayResult->PCD_PAY_DISCOUNT;		// 할인금액
//    	$pay_amount_real = $PayResult->PCD_PAY_AMOUNT_REAL; // 결제완료금액
//
//    	$pay_total = $PayResult->PCD_PAY_TOTAL;         	// 결제 금액
//
//
//    	if ($pay_type == 'transfer') {
//	    	$pay_bank = $PayResult->PCD_PAY_BANK;			// 은행코드
//	    	$pay_bankName = $PayResult->PCD_PAY_BANKNAME;	// 은행명
//	    	$pay_bankNum = $PayResult->PCD_PAY_BANKNUM;		// 계좌번호
//    	} 
//
//		else if ($pay_type == 'card') {
//    		$pay_taxtotal = (isset($PayResult->PCD_PAY_TAXTOTAL)) ? $PayResult->PCD_PAY_TAXTOTAL : "";					// 부가세(복합과세적용)
//    		$pay_isTax = (isset($PayResult->PCD_PAY_ISTAX)) ? $PayResult->PCD_PAY_ISTAX : "";							// 과세여부
//    		$pay_cardname = (isset($PayResult->PCD_PAY_CARDNAME)) ? $PayResult->PCD_PAY_CARDNAME : "";					// 카드사명
//    		$pay_cardnum = (isset($PayResult->PCD_PAY_CARDNUM)) ? $PayResult->PCD_PAY_CARDNUM : "";						// 카드번호
//    		$pay_cardtradenum = (isset($PayResult->PCD_PAY_CARDTRADENUM)) ? $PayResult->PCD_PAY_CARDTRADENUM : "";		// 카드결제 거래번호
//    		$pay_cardauthno = (isset($PayResult->PCD_PAY_CARDAUTHNO)) ? $PayResult->PCD_PAY_CARDAUTHNO : "";			// 카드결제 승인번호
//    		$pay_cardreceipt = (isset($PayResult->PCD_PAY_CARDRECEIPT)) ? $PayResult->PCD_PAY_CARDRECEIPT : "";			// 카드전표 URL
//    	}
//
//    	$pay_time = $PayResult->PCD_PAY_TIME;           	// 결제완료 시간
//    	$taxsave_rst = (isset($PayResult->PCD_TAXSAVE_RST)) ? $PayResult->PCD_TAXSAVE_RST : "";     	// 현금영수증 발행결과 (Y|N)
//    	$reguler_flag = $PayResult->PCD_REGULER_FLAG;   	// 정기결제 요청여부 (Y|N)
//
//
//        // 결제요청 결과 수신 - 성공처리
//        if ($pay_rst == 'success') {
//
//						// 출금성공 결과 처리...
//						
//						// DB PROCESS
//						/*
//						 INSERT INTO paylist
//						 (PListNo, pay_oid, pay_year, pay_month, pay_goods, pay_type, pay_total, taxsave_flag)
//						 VALUES
//						 ('$No', '$pay_oid', '$pay_year', '$pay_month', '$pay_goods', '$pay_type', $pay_total, '$taxsaave_flag')
//						 */
//
//
//        }
//
//        // 결제요청 결과 수신 - 실패처리
//		else {
//		}
//
//
//
//		//	$DATA = array(
//		//		"PCD_PAY_RST" => "$pay_rst",
//		//		"PCD_PAY_MSG" => "$pay_msg",
//		//		"PCD_PAY_REQKEY" => "$pay_reqkey",
//		//		"PCD_PAY_OID" => "$pay_oid",
//		//		"PCD_PAY_TYPE" => "$pay_type",
//		//		"PCD_PAYER_NO" => "$payer_no",
//		//		"PCD_PAY_YEAR" => "$pay_year",
//		//		"PCD_PAY_MONTH" => "$pay_month",
//		//		"PCD_PAY_GOODS" => "$pay_goods",
//		//		"PCD_PAY_AMOUNT" => "$pay_amount",
//		//		"PCD_PAY_DISCOUNT" => "$pay_discount",
//		//		"PCD_PAY_AMOUNT_REAL" => "$pay_amount_real",
//		//		"PCD_PAY_TOTAL" => "$pay_total",
//		//		"PCD_PAY_TIME" => "$pay_time",
//		//		"PCD_TAXSAVE_RST" => "$taxsave_rst"
//		//	);
//		//	
//		//	if ($pay_type == 'transfer') {
//		//		$DATA['PCD_PAY_BANK'] = $pay_bank;
//		//		$DATA['PCD_PAY_BANKNAME'] = $pay_bankName;
//		//		$DATA['PCD_PAY_BANKNUM'] = $pay_bankNum;
//		//	}
//		//	
//		//	if ($pay_type == 'card') {
//		//		$DATA['PCD_PAY_TAXTOTAL'] = $pay_taxtotal;
//		//		$DATA['PCD_PAY_ISTAX'] = $pay_isTax;
//		//		$DATA['PCD_PAY_CARDNAME'] = $pay_cardname;
//		//		$DATA['PCD_PAY_CARDNUM'] = $pay_cardnum;
//		//		$DATA['PCD_PAY_CARDTRADENUM'] = $pay_cardtradenum;
//		//		$DATA['PCD_PAY_CARDAUTHNO'] = $pay_cardauthno;
//		//		$DATA['PCD_PAY_CARDRECEIPT'] = $pay_cardreceipt;
//		//	}
//      