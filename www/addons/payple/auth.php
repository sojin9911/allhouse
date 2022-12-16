<?php

	/*
		* 간편결제(비밀번호, 일회성), 앱카드결제 가맹점 인증 요청
			https://도메인/addons/payple/auth.php

			비밀번호 간편결제 요청 시 - payple_auth_file 항목에 들어가는 파일
				payple_auth_file = '/addons/payple/auth.php'
	*/

	ini_set('memory_limit','-1');

	//	header("Expires: Mon 26 Jul 1997 05:00:00 GMT");
	//	header("Last-Modified: " . gmdate("D, d, M Y H:i:s") . " GMT");
	//	header("Cache-Control: no-store, no-cache, must-revalidate");
	//	header("Cache-Control: post-check=0; pre-check=0", false);
	//	header("Pragma: no-cache");
	//	header("Content-type: application/json; charset=utf-8");

	include_once( dirname(__FILE__) ."/inc.php" );


	// URL 불러오기
	$url = $app_payple_domain . '/php/auth.php';
	//ViewArr($url);


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


	// API CURL 연동
	$res = ApiCurl( $url , $post_data , $header_data);
	if($res['json']->result == 'success') {
		echo $res['none'];
	}
	else {
		echo $res['json']->result_msg;
	}
	exit;




//{"server_name":"testcpay.payple.kr","result":"success","result_msg":"사용자 인증 완료!!","cst_id":"Z0huNVVVYmpXL3RHN2NFWXhmbStQQT09","custKey":"VE9lWUhsUnZwaXYvL01oakVOMW9Ydz09","AuthKey":"K0VnWlZ5TWZSaGNla1Vpay96YnNQQTFnYXcyVWxlSzJGTHdtNHpNTndIUmJIZ2IrUFI1VExnZzhvOGNqS2MwR0RXL2ZVVjNXbUNBSG43ajdJNXJlelZuKzBXenZNa2RQSGMwdzJlNndBS3dwMTF4Y29OMkdEaFI4RjZSQVpidVpPYmFKSUlqSDhvVDFyc1JCS2JDQkc3amJOMEgrcmw5TitQdzJtOWdSS1YwQ2lyd0prSmJNQWoySTB5elFsK0ZCdnZTQ3NZSC8xMHJZT3dHVnBoSVZRcXBzWWVDcUlIeXNrcHowRWxiSTd5Z2p5NFlRTDQ4azJxRGNjbVluZjRJZWVLL25VcFhIK2tobnc2MmxSZzUzdmc9PQ==","PCD_PAY_HOST":"https:\/\/testcpay.payple.kr","PCD_PAY_URL":"\/index.php?ACT_=PAYM&CPAYVER=202104051618","return_url":"https:\/\/testcpay.payple.kr\/index.php?ACT_=PAYM&CPAYVER=202104051618"}


	// ViewArr($res);
	//		stdClass Object
	//		(
	//			[server_name] => testcpay.payple.kr
	//			[result] => success
	//			[result_msg] => 사용자 인증 완료!!
	//			[cst_id] => bVN2eFd3MHJLd0NCTWhhbTRmMW1idz09
	//			[custKey] => T2wvNEdkS3BiQVU1RjdjS21xSWoxdz09
	//			[AuthKey] => K0VnWlZ5TWZSaGNla1Vpay96YnNQQTFnYXcyVWxlSzJGTHdtNHpNTndIUmJIZ2IrUFI1VExnZzhvOGNqS2MwR0RXL2ZVVjNXbUNBSG43ajdJNXJlelZuKzBXenZNa2RQSGMwdzJlNndBS3dwMTF4Y29OMkdEaFI4RjZSQVpidVowcDZFQXpFa0R4enNpWUFKSVRHYkF3WWRNUVRhaG1nODVWVklCS09VY1c1cVpJaVFKc3JPMUdoeSt4bGg3b3dkUUliU3c5Mm9zeDdFM3hXUTNuWWZuRnRpcFJaOWNvdnlVdVBsWE1LMjBLbWNyTEpFRzh4Z295QTJ0OU9uSGpUdjVVWHZhVnB1cytFbTM0bHRFRG55d0E9PQ==
	//			[PCD_PAY_HOST] => https://testcpay.payple.kr
	//			[PCD_PAY_URL] => /index.php?ACT_=PAYM&CPAYVER=202103311942
	//			[return_url] => https://testcpay.payple.kr/index.php?ACT_=PAYM&CPAYVER=202103311942
	//		)