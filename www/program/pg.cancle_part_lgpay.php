<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


// 거래번호
$ocl = _MQ("select oc_tid from smart_order_cardlog where oc_oordernum = '".$_ordernum."' order by oc_uid desc limit 1");
$LGD_TID = $ocl[oc_tid]; // PG사 거래 번호

/* [결제취소 요청 사전 정리] *************/
/*
 *
 * LG유플러스으로 부터 내려받은 거래번호(LGD_TID)를 가지고 취소 요청을 합니다.(파라미터 전달시 POST를 사용하세요)
 * (승인시 LG유플러스으로 부터 내려받은 PAYKEY와 혼동하지 마세요.)
 */
$CST_PLATFORM	= $siteInfo[s_pg_mode];							//LG유플러스 결제 서비스 선택(test:테스트, service:서비스)
$CST_MID		= $siteInfo[s_pg_code];								//상점아이디(LG유플러스으로 부터 발급받으신 상점아이디를 입력하세요)
																//테스트 아이디는 't'를 반드시 제외하고 입력하세요.
$LGD_MID		= (("test" == $CST_PLATFORM)?"t":"").$CST_MID;	//상점아이디(자동생성)

$configPath		= PG_DIR . "/lgpay/lgdacom";					//LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.

require_once(PG_DIR. "/lgpay/lgdacom/XPayClient.php");
/* [결제취소 요청 사전 정리] *************/

$xpay = &new XPayClient($configPath, $CST_PLATFORM);
$xpay->Init_TX($LGD_MID);
$xpay->Set("LGD_TXNAME", "PartialCancel");
$xpay->Set("LGD_TID", $LGD_TID);// 거래번호 지정
$xpay->Set("LGD_CANCELAMOUNT", $_total_amount); // 부분 취소할 금액

if( $ordr[o_paymethod] == 'virtual' ) {
	$xpay->Set("LGD_RFBANKCODE", $cancel_bank);
	$xpay->Set("LGD_RFACCOUNTNUM", $cancel_bank_account);
}

// 취소 성공 여부
//$is_pg_status = $xpay->TX();	// pg 모듈 호출상태
/*
 # 신용카드 - 0000, AV11
 : 신용카드 승인취소(매입전취소) 실패의 경우, LG유플러스에서 자동으로 취소처리합니다. 두개의 결과코드들에 대해 반드시 취소성공 처리를 해야 합니다. (단, 매입전 승인취소 실패 건에 대해 자동으로 취소처리를 원치 않을 경우, LG유플러스에 별도 설정변경 요청을 하여야 함)
 # 계좌이체 - 0000, RF00, RF10, RF09, RF15, RF19, RF23, RF25
 : 계좌이체 환불진행중 응답건의 경우, LG유플러스에서 자동환불 처리합니다. 환불진행중 응답코드에 대해서는 환불결과코드.xls 파일을 참고하시기 바랍니다. 환불진행중응답건의 경우도 반드시 환불성공 처리를 해야 합니다.
*/
// 취소 처리해야할 응답코드
$arr_lg_cancel_code = array('0000', 'AV11', 'RF00', 'RF10', 'RF09', 'RF15', 'RF19', 'RF23', 'RF25');

// 결제취소요청 결과 처리
if ($xpay->TX()) {
	$LGD_RESPCODE = $xpay->Response_Code();
	if( in_array($LGD_RESPCODE , $arr_lg_cancel_code) ){
		// 취소 성공 여부
		$is_pg_status = true;
	}else{
		// 취소 성공 여부
		$is_pg_status = false;
	}
}else {
	// 취소 성공 여부
	$is_pg_status = false;
}

// 취소결과 로그 기록
card_cancle_log_write($LGD_TID,$xpay->Response("LGD_RESPMSG", 0));	// 카드거래번호 , 결과 메세지



actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행