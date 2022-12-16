<?php
// SSJ : 토스페이먼츠 PG 모듈 추가 : 2021-02-22
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// Post 방식의 curl 발송 위한 함수
function tosAPICurl( $url , $data, $header, $RequestTime=100) {
	$cu = curl_init();
	curl_setopt($cu, CURLOPT_URL,  $url ); // 데이타를 보낼 URL 설정
	curl_setopt($cu, CURLOPT_POST,1); // 데이타를 get/post 로 보낼지 설정
	curl_setopt($cu, CURLOPT_RETURNTRANSFER,1); // REQUEST 에 대한 결과값을 받을건지 체크 #Resource ID 형태로 넘어옴 :: 내장 함수 curl_errno 로 체크
	$arr_url = parse_url($url);
	if( $arr_url[scheme] == "https") {curl_setopt($cu, CURLOPT_SSL_VERIFYPEER, 0);}
	curl_setopt($cu, CURLOPT_HEADER, false);//헤더 정보를 보내도록 함(*필수)
	curl_setopt($cu, CURLOPT_HTTPHEADER, $header); //header 지정하기
	curl_setopt($cu, CURLOPT_TIMEOUT, $RequestTime); // REQUEST 에 대한 결과값을 받는 시간타임 설정
	curl_setopt($cu, CURLOPT_POSTFIELDS, $data);
	$str = curl_exec($cu); // 실행
	curl_close($cu);
	return $str;
}

// --> 비회원 구매를 위한 쿠키 적용여부 파악
cookie_chk();


if($orderId <> '' && $paymentKey <> '' && $amount > 0){

	$ordernum = $orderId;//주문번호
	// 주문정보 추출
	$r = _MQ("select * from smart_order where o_ordernum='". $ordernum ."' ");

	// 금액이 같을 때만 결제 승인
	if($r['o_price_real'] == $amount){

		// 결제승인 API
		$url = "https://api.tosspayments.com/v1/payments/{$paymentKey}";
		$data = array('orderId' => $ordernum, 'amount' => $amount);
		$secretKey = base64_encode($siteInfo['s_pg_key'].':'); // 시크릿 키에 콜론(:)을 합쳐 Base64로 인코딩, Basic 포함해야함
		$header = array(
			'Authorization: Basic '.$secretKey
			,'Content-Type: application/json'
		);

		$rst = json_decode(tosAPICurl($url, json_encode($data), $header), true);

		if($rst['paymentKey'] <> '' && $rst['code'] == ''){

			$keys = array('paymentKey','orderId','method','totalAmount','secret');
			$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
			foreach($keys as $name) {
				$app_oc_content .= $name . "||" .$rst[$name] . "§§" ;
			}
			// 영수증 URL
			$app_oc_content .= "receiptUrl||" .$rst['card']['receiptUrl'] . "§§" ;
			$app_oc_content .= "cashReceipt||" .$rst['cashReceipt']['receiptUrl'] . "§§" ;

			// - 결제 성공 기록정보 저장 ---
			$que = "
				insert smart_order_cardlog set
					 oc_oordernum = '".$ordernum."'
					,oc_tid = '". $rst['paymentKey'] ."'
					,oc_content = '". $app_oc_content ."'
					,oc_rdate = now();
			";
			_MQ_noreturn($que);
			$insert_oc_uid = mysql_insert_id();// 결제기록 고유번호 저장
			// - 결제 성공 기록정보 저장 ---

			// 결제 성공 처리
    		if($rst['method']=='카드' || $rst['method']=='휴대폰') {

				// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
				include OD_PROGRAM_ROOT."/shop.order.result.pro.php";
				error_loc("/?pn=shop.order.complete","top");

			}else if($rst['method']=='가상계좌') {
				$ool_type = 'R';
				$tno = $rst['secret'];
				$app_time = date('Y-m-d H:i:s', strtotime($rst['requestedAt']));
				$amount = $rst['totalAmount'];
				$account = $rst['virtualAccount']['accountNumber'];
				$bankname = $rst['virtualAccount']['bank'];
				$bankcode = '';
				$escw_yn = $rst['useEscrow'] ? 'Y' : 'N';
				$buyr_tel2 = $r['o_hp'];
				$depositor = '';
				$payer = $rst['virtualAccount']['customerName'];
				_MQ_noreturn("
					insert into smart_order_onlinelog (
					ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_escrow_code, ool_deposit_tel, ool_bank_owner
					) values (
					'". $ordernum ."', '". $r['o_mid'] ."', now(), '". $tno ."', '". $ool_type ."', '". $app_time ."', '". $amount ."', '". $amount ."', '". $account ."', '', '". $payer ."', '". $bankname ."', '". $bankcode ."', '". $escw_yn ."', '', '". $buyr_tel2 ."', '". $depositor ."'
					)
				");

				// 현금영수증을 신청하였다면
				if($rst['useCashReceipt']){
					_MQ_noreturn(" update smart_order set o_get_tax = 'Y' where o_ordernum='".$ordernum."' ");
				}

				// 장바구니 정보 삭제
				_MQ_noreturn(" delete from smart_cart where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' and c_direct='Y'  ");

				// 가상계좌 결제 이메일 및 SMS 발송
				include_once OD_PROGRAM_ROOT."/shop.order.mail.send.virtual.php";
				error_loc("/?pn=shop.order.complete","top");

			}else{

				// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
				include OD_PROGRAM_ROOT."/shop.order.result.pro.php";
				error_loc("/?pn=shop.order.complete","top");

			}

		}else{

			// SSJ : 결제성공 이후 동일한 정보가 다시 오는 경우 결제실패처리 하지 않음 : 2021-12-30
			$oc_res_cnt = _MQ(" select count(*) as cnt from smart_order_cardlog where oc_oordernum = '".$ordernum."' and oc_tid = '". $paymentKey."' ");
			if($oc_res_cnt['cnt'] == 1 ) {

				// 결제완료페이지 이동
				error_loc("/?pn=shop.order.complete",'top');

			}else{

				// 결제 실패 처리
				_MQ_noreturn("update smart_order set o_status='결제실패' where o_ordernum='". $ordernum ."' ");
				$msg = $rst['message'] ? $rst['message'] : "결제에 실패하였습니다. 다시한번 확인 바랍니다.";
				error_loc_msg("/?pn=shop.order.result" , $msg);

			}

		}

	}else{

		// 결제금액 변조
		$msg = '결제 금액이 변조되었습니다.';
		error_loc_msg("/?pn=shop.order.result" , $msg);

	}


}

//$orderId
//$paymentKey
//$amount