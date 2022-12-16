<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// 취소 서버 아이피
$SERVER_IP = $siteInfo[s_pg_mode] == "test" ? "27.102.213.205" : "27.102.213.207";

define( "ENCKEY"	,$siteInfo[s_pg_enc_key] );	// 암호화 키값
define( "SERVER_IP"	,$SERVER_IP ); 				// 다우페이 결제취소 서버
define( "CARD_PORT"	,64001 );					// 신용카드 포트
define( "BANK_PORT"	,46001 );					// 계좌이체 포트
define( "TIMEOUT"	,10 );

// 휴대폰소액결제 추가 ---
define( "MOBILE_PORT"	,43001 );				// 모바일 포트 추가
// 휴대폰소액결제 추가 ---


if($r[o_paymethod] == "virtual") {		// 가상계좌는 다우페이 취소연동이 되지 않는다. 직접 환불처리해야한다.

	$is_pg_status = true;

} else if($r[o_paymethod] == "card") {	// 카드결제

	require_once(PG_DIR. "/daupay/library/Card_library.php");

	$CPID				= $siteInfo[s_pg_code];
	$DAOUTRX			= $r[oc_tid];
	$AMOUNT				= $r[o_price_real];
	$IPADDRESS			= $r[SERVER_ADDR];
	$CANCELMEMO			= "관리자모드 취소";

	CardCancel(  SERVER_IP, CARD_PORT, $CPID, ENCKEY, TIMEOUT );

	// 카드거래번호 , 결과 메세지
	card_cancle_log_write($DAOUTRX,"resultcode:".$res_resultcode."|".iconv("euckr","utf8",$res_errormessage));

	// 결과코드 0000 : 성공	그외 : 실패
	$is_pg_status = $res_resultcode == "0000" ? true : false;

} else if($r[o_paymethod] == "iche") {	// 계좌이체

	require_once(PG_DIR. "/daupay/library/Bank_library.php");

	$CPID				= $siteInfo[s_pg_code];
	$DAOUTRX			= $r[oc_tid];
	$AMOUNT				= $r[o_price_real];
	$CANCELMEMO			= "관리자모드 취소";

	BankCancel(  SERVER_IP, BANK_PORT, $CPID, ENCKEY, TIMEOUT );

	// 카드거래번호 , 결과 메세지
	card_cancle_log_write($DAOUTRX,"resultcode:".$res_resultcode."|".iconv("euckr","utf8",$res_errormessage));

	// 결과코드 0000 : 성공	그외 : 실패
	$is_pg_status = $res_resultcode == "0000" ? true : false;

}
// 휴대폰소액결제 추가 ---
else if($r[o_paymethod] == "hpp") {	// 휴대폰

	require_once(PG_DIR. "/daupay/library/Mobile_library.php");

	$opcode		=	"304";
	$CPID				= $siteInfo[s_pg_code];
	$DAOUTRX			= $r[oc_tid];
	$AMOUNT				= $r[o_price_real];
	$CANCELMEMO			= "관리자모드 취소";

	// -- 취소요청
	MobileCancel(  SERVER_IP, MOBILE_PORT, $CPID, ENCKEY, TIMEOUT );

	// 카드거래번호 , 결과 메세지
	card_cancle_log_write($DAOUTRX,"resultcode:".$res_resultcode."|".iconv("euckr","utf8",$res_errormessage));

	// 결과코드 0000 : 성공	그외 : 실패
	$is_pg_status = $res_resultcode == "0000" ? true : false;

	// 발행된 현금영수증이 있으면 취소기록
	if($is_pg_status){
		_MQ_noreturn(" update smart_baro_cashbill set BarobillState='6000', bc_iscancel='Y' where bc_ordernum='". $_ordernum ."' and bc_type='pg' and bc_isdelete='N' and bc_iscancel='N' ");
	}

}






actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행