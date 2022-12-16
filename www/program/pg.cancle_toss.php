<?php
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



// 거래번호
$ocl = _MQ("select oc_tid from smart_order_cardlog where oc_oordernum = '".$_ordernum."' order by oc_uid desc limit 1");
$paymentKey = $ocl['oc_tid']; // PG사 거래 번호

// 결제취소 API
$url = "https://api.tosspayments.com/v1/payments/{$paymentKey}/cancel";
$data = array('cancelReason' => '고객요청', 'cancelAmount' => ''); // cancelAmount가 없으면 전액 취소
$secretKey = base64_encode($siteInfo['s_pg_key'].':'); // 시크릿 키에 콜론(:)을 합쳐 Base64로 인코딩, Basic 포함해야함
$header = array(
	'Authorization: Basic '.$secretKey
	,'Content-Type: application/json'
);

$rst = json_decode(tosAPICurl($url, json_encode($data), $header), true);


if($rst['code'] == '' && $rst['status'] == 'CANCELED'){
	// 취소 성공 여부
	$is_pg_status = true;
}else{
	// 취소 성공 여부
	$is_pg_status = false;
}





// 발행된 현금영수증이 있으면 취소기록
if($is_pg_status){
	_MQ_noreturn(" update smart_baro_cashbill set BarobillState='6000', bc_iscancel='Y' where bc_ordernum='". $_ordernum ."' and bc_type='pg' and bc_isdelete='N' and bc_iscancel='N' ");
}

// 취소결과 로그 기록
$res_msg = $rst['message'] ? $rst['message'] : '취소실패';
card_cancle_log_write($paymentKey,$res_msg);	// 카드거래번호 , 결과 메세지




actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행