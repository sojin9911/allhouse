<?php

	// 공통 파일

	include_once( dirname(__FILE__) ."/../../include/inc.php" );



	// 간편결제(페이플) 미사용 막기
	if($siteInfo['s_payple_use'] <> "Y") {exit;}

	// 간편결제(페이플) 활성화 모드에 따른 도메인 지정
	$app_payple_domain = ($siteInfo['s_payple_mode']== "service" ? "https://cpay.payple.kr" : "https://testcpay.payple.kr");


	// API 를 위한 Post 방식의 curl 발송 위한 함수 (header 정보 전송)
	function ApiCurl( $url , $data , $header_data , $RequestTime=100) {

		$cu = curl_init();
		curl_setopt($cu, CURLOPT_URL,  $url ); // 데이타를 보낼 URL 설정
		curl_setopt($cu, CURLOPT_POST,1); // 데이타를 get/post 로 보낼지 설정
		curl_setopt($cu, CURLOPT_RETURNTRANSFER,1); // REQUEST 에 대한 결과값을 받을건지 체크 #Resource ID 형태로 넘어옴 :: 내장 함수 curl_errno 로 체크

		curl_setopt($cu, CURLOPT_HEADER, false);//헤더 정보를 보내도록 함(*필수)
		curl_setopt($cu, CURLOPT_HTTPHEADER, $header_data); //header 지정하기

		curl_setopt($cu, CURLOPT_POSTFIELDS, json_encode($data));  //POST로 보낼 데이터 지정하기

		$arr_url = parse_url($url);
		if( $arr_url[scheme] == "https") {curl_setopt($cu, CURLOPT_SSL_VERIFYPEER, 0);}

		curl_setopt($cu, CURLOPT_TIMEOUT, $RequestTime); // REQUEST 에 대한 결과값을 받는 시간타임 설정

		$str = curl_exec($cu); // 실행
		curl_close($cu);

		return array('json' => json_decode($str) , 'none' =>$str);

	}