<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// 취소 서버 아이피
$SERVER_IP = $siteInfo[s_pg_mode] == "test" ? "27.102.213.205" : "27.102.213.207";

define( "ENCKEY"	,$siteInfo[s_pg_enc_key] );	// 암호화 키값
define( "SERVER_IP"	,$SERVER_IP ); 				// 다우페이 결제취소 서버
define( "CARD_PORT"	,64001 );					// 신용카드 포트
define( "BANK_PORT"	,46001 );					// 계좌이체 포트

//  휴대폰소액결제 추가 ---
define( "MOBILE_PORT"	,43001 );				// 모바일 포트 추가
//  휴대폰소액결제 추가 ---

define( "TIMEOUT"	,10 );

// 카드정보
$ocl = _MQ("select oc_tid from smart_order_cardlog where oc_oordernum = '".$_ordernum."' order by oc_uid desc limit 1");

// 주문정보
$r = get_order_info($_ordernum);

if($r[o_paymethod] == "virtual") {		// 가상계좌는 다우페이 취소연동이 되지 않는다. 직접 환불처리해야한다.

	$is_pg_status = true;

} else if($r[o_paymethod] == "card") {	// 카드결제

	require_once(PG_DIR. "/daupay/library/Card_library.php");

	$CPID				= $siteInfo[s_pg_code];
	$DAOUTRX			= $ocl[oc_tid];
	$AMOUNT				= $_total_amount;
	$IPADDRESS			= $r[SERVER_ADDR];
	$CANCELMEMO			= "관리자모드 취소";

	CardCancel(  SERVER_IP, CARD_PORT, $CPID, ENCKEY, TIMEOUT );

	// 카드거래번호 , 결과 메세지
	card_cancle_log_write($DAOUTRX,"resultcode:".$res_resultcode."|".iconv("euckr","utf8",$res_errormessage));

	// 결과코드 0000 : 성공	그외 : 실패
	$is_pg_status = $res_resultcode == "0000" ? true : false;

} else if($r[o_paymethod] == "inche") {	// 계좌이체

	require_once(PG_DIR. "/daupay/library/Bank_library.php");

	$CPID				= $siteInfo[s_pg_code];
	$DAOUTRX			= $ocl[oc_tid];
	$AMOUNT				= $_total_amount;
	$CANCELMEMO			= "관리자모드 취소";

	BankCancel(  SERVER_IP, BANK_PORT, $CPID, ENCKEY, TIMEOUT );

	// 카드거래번호 , 결과 메세지
	card_cancle_log_write($DAOUTRX,"resultcode:".$res_resultcode."|".iconv("euckr","utf8",$res_errormessage));

	// 결과코드 0000 : 성공	그외 : 실패
	$is_pg_status = $res_resultcode == "0000" ? true : false;

}else if($r[o_paymethod] == "hpp") {	// 휴대포

	require_once(PG_DIR. "/daupay/library/Mobile_library.php");

	$opcode		=	"304";		
	$CPID				= $siteInfo[s_pg_code];
	$DAOUTRX			= $ocl[oc_tid];
	$AMOUNT				= $_total_amount;
	$IPADDRESS			= $r[SERVER_ADDR];
	$CANCELMEMO			= "관리자모드 취소";

	// -- 취소요청
	MobileCancel(  SERVER_IP, MOBILE_PORT, $cpid, ENCKEY, TIMEOUT );

	// 카드거래번호 , 결과 메세지
	card_cancle_log_write($DAOUTRX,"resultcode:".$res_resultcode."|".iconv("euckr","utf8",$res_errormessage));

	// 결과코드 0000 : 성공	그외 : 실패
	$is_pg_status = $res_resultcode == "0000" ? true : false;

} 

actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행