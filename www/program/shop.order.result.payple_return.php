<?php

	include_once(dirname(__FILE__).'/inc.php');
	actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


	// 접속 허용 아이피 체크
	//if( !IN_ARRAY($_SERVER['REMOTE_ADDR'] , $arr_arrow_ip) ) {exit;}
	//header("Content-Type:application/json");


	$data = @file_get_contents('php://input');
	parse_str($data , $app_data);


	//	ViewArr($data);
	//	ViewArr($app_data);
	//	echo $app_data['PCD_PAY_RST'];
	//	exit;


	// 변수 설정
	$ordernum = $app_data['PCD_PAY_OID']; // 주문번호
	$tid = $app_data['PCD_PAY_CARDAUTHNO']; // 승인번호
	$_AUTH_KEY = $app_data['PCD_AUTH_KEY']; // CERT 결제요청 후 리턴받은 토큰키
	$_PAY_REQKEY = $app_data['PCD_PAY_REQKEY']; // CERT 결제요청 후 최종 승인요청용 키
	$_PAYER_ID = $app_data['PCD_PAYER_ID']; // CERT 결제요청 후 리턴받은 빌링키(PCD_CARD_VER:01 일 때 필수)


	// - 주문결제기록 1차 수집 ---
	$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
	foreach($app_data as $k => $v) { $app_oc_content .= $k . "||" .$v . "§§" ; } // 데이터 저장


	// 주문정보 추출
	$r = _MQ("select * from smart_order where o_ordernum='". $ordernum ."' ");
	//ViewArr("select * from smart_order where o_ordernum='". $ordernum ."' ");


	// 결제성공
	$trigger = "N";
	if($app_data['PCD_PAY_RST'] == "success") {

		// ----- 결제요청 재컨펌(PCD_PAY_WORK : CERT) -----
		if($app_data['PCD_PAY_WORK'] == "CERT") {

			include OD_ADDONS_ROOT."/payple/PayCardConfirmAct.php";
			if($res['json']->PCD_PAY_RST == 'success') { $trigger = "Y"; }

			// - 주문결제기록 2차 수집 ---
			$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
			foreach($res['json'] as $k => $v) { $app_oc_content .= $k . "||" .$v . "§§" ; } // 데이터 저장
			//ViewArr($app_oc_content);

			$tid = $res['json']->PCD_PAY_CARDAUTHNO; // 승인번호

		}
		else { $trigger = "Y"; }
		// ----- 결제요청 재컨펌(PCD_PAY_WORK : CERT) -----


		// 페이플 간편결제 연동정보 저장
		//		PCD_PAYER_ID 정보 저장
		$ipi_que = " INSERT INTO smart_individual_payple_info (ipi_inid  , ipi_payer_id , ipi_rdate ) VALUES  ('". $r['o_mid'] ."'  , '". $_PAYER_ID ."' , NOW() ) ON DUPLICATE KEY UPDATE  ipi_rdate = NOW() ";
		_MQ_noreturn($ipi_que);
		//ViewArr($ipi_que);

	}


	// - 주문결제기록 저장 ---
	$que = " insert smart_order_cardlog set oc_oordernum = '".$ordernum."' ,oc_tid = '". $tid ."' ,oc_content = '". $app_oc_content ."' ,oc_rdate = now(); ";
	//ViewArr($que); 
	if(!preg_match('/중복/i' , $app_oc_content)) { _MQ_noreturn($que); }
	$insert_oc_uid = mysql_insert_id();


	// 최종 결제성공 처리
	if($trigger == "Y") {
		// 주문완료시 처리 부분 - shop.order.result.pro.php주문서수정,포인트,수량,문자발송,메일발송
		include OD_PROGRAM_ROOT."/shop.order.result.pro.php";

		// 결제완료페이지 이동
		error_loc("/?pn=shop.order.complete&ordernum={$ordernum}");
	}

	// 결제실패 처리
	else {

		// 2017-01-04 ::: 결제성공 이후 동일한 정보가 다시 오는 경우 결제실패처리 하지 않음. ::: JJC
		$oc_res_cnt = _MQ(" select count(*) as cnt from smart_order_cardlog where oc_oordernum = '".$ordernum."' and oc_tid = '". $tid ."' and oc_content like '%PCD_PAY_RST||success§§%' ");
		if($oc_res_cnt['cnt'] == 1 ) {

			// 결제 실패기록 삭제
			_MQ_noreturn("delete from smart_order_cardlog where oc_uid='". $insert_oc_uid ."' ");

			// 결제완료페이지 이동
			error_loc("/?pn=shop.order.complete&ordernum={$ordernum}",'top');

		}

		// 결제실패 처리
		else {

			_MQ_noreturn("update smart_order set o_status='결제실패' where o_ordernum='". $ordernum ."' ");
			error_loc_msg("/?pn=shop.order.result" , $resultMap["resultMsg"]." - 결제에 실패하였습니다. 다시한번 확인 바랍니다.");

		}
		// 2017-01-04 ::: 결제성공 이후 동일한 정보가 다시 오는 경우 결제실패처리 하지 않음. ::: JJC
	}
	// 결제실패 처리







	/*
		응답변수	값	설명
		PCD_PAY_RST	success	결제요청 결과(success|error)
		PCD_PAY_MSG	결제완료	결제요청 결과 메시지
		PCD_PAY_WORK	CERT	결제요청방식
		PCD_AUTH_KEY	a688ccb355...	결제 후 리턴 받은 토큰키
		PCD_PAY_REQKEY	abcd..	CERT - 결제생성 후 승인을 위한 키
		PCD_PAY_COFURL	http://test..	CERT - 결제승인 후 리턴 URL
		PCD_PAY_OID	test099942200156938	주문번호
		PCD_PAY_TYPE	card	‘card’ – 고정 값
		PCD_PAYER_ID	d0to...	카드등록 후 리턴받은 빌링키
		PCD_PAYER_NO	1234	가맹점에서 사용하는 회원번호
		PCD_PAY_GOODS	상품1	상품명
		PCD_PAY_TOTAL	100	카드결제 완료금액
		PCD_PAY_TAXTOTAL	10	복합과세(과세+면세) 주문건에 필요한 금액이며 가맹점에서 전송한 값을 부가세로 설정합니다.과세 또는 비과세의 경우 사용하지 않습니다.
		PCD_PAY_ISTAX	Y	과세설정 (Default: Y 이며, 과세:Y, 복합과세:Y, 비과세: N)
		PCD_PAYER_EMAIL	dev@payple.kr	결제자 이메일
		PCD_PAY_YEAR	-	결제 구분 년도
		PCD_PAY_MONTH	-	결제 구분 월
		PCD_PAY_CARDNAME	BC 카드	카드사명
		PCD_PAY_CARDNUM	1111 * * * * * * * * 4444	카드번호(중간 8자리 * 처리)
		PCD_PAY_CARDTRADENUM	2020031413203326920	거래 키
		PCD_PAY_CARDAUTHNO	98123445	승인번호
		PCD_PAY_CARDRECEIPT	https://www.danal..	매출전표 출력 링크
		PCD_PAY_TIME	20200301140130	결제시간
		PCD_REGULER_FLAG	N	월 중복결제 방지 (사용: Y, 그 외: N)
		PCD_RST_URL	/result/..	결제(요청)결과 RETURN URL
		PCD_USER_DEFINE1	가맹점 입력 값 1	가맹점 사용 필드 1
		PCD_USER_DEFINE2	가맹점 입력 값 2	가맹점 사용 필드 2
		PCD_PAYER_NAME	홍길동	결제고객 이름
	*/