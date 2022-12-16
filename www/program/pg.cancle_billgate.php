<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행




$today=mktime();
$today_time = date('YmdHis', $today);

// 거래번호
$TID = ($ocs[oc_tid] ? $ocs[oc_tid] : $r[oc_tid]); // PG사 거래 번호

/* [결제취소 요청 사전 정리] *************/
/*
 *
 * LG유플러스으로 부터 내려받은 거래번호(LGD_TID)를 가지고 취소 요청을 합니다.(파라미터 전달시 POST를 사용하세요)
 * (승인시 LG유플러스으로 부터 내려받은 PAYKEY와 혼동하지 마세요.)
 */
$CST_PLATFORM               = $siteInfo[s_pg_mode];       //LG유플러스 결제 서비스 선택(test:테스트, service:서비스)
$MID                    = $siteInfo[s_pg_code];            //상점아이디(LG유플러스으로 부터 발급받으신 상점아이디를 입력하세요)
																	 //테스트 아이디는 't'를 반드시 제외하고 입력하세요.
/* [결제취소 요청 사전 정리] *************/

if($_paymethod=='virtual') { } else {

	//---------------------------------------
	// API 클래스 Include
	//---------------------------------------
	include_once PG_DIR."/billgate/config.php";
	include_once PG_DIR."/billgate/class/Message.php";
	include_once PG_DIR."/billgate/class/MessageTag.php";
	include_once PG_DIR."/billgate/class/ServiceCode.php";
	include_once PG_DIR."/billgate/class/Command.php";
	include_once PG_DIR."/billgate/class/ServiceBroker.php";

	//---------------------------------------
	// 값 초기화
	//---------------------------------------
	$SERVICE_ID = $MID;
	$ORDER_ID = ($ordernum?$ordernum:$_ordernum);	// 새로운 주문번호
	$ORDER_DATE = $today_time;		// 새로운 주문시간
	$TRANSACTION_ID = $TID;	// 취소건의 거래번호
	$DEAL_AMOUNT = $r[o_price_real];	// 금액


	//---------------------------------------
	// API 인스턴스 생성
	//---------------------------------------
	$reqMsg = new Message(); //요청 메시지
	$resMsg = new Message(); //응답 메시지
	$tag = new MessageTag(); //태그
	$svcCode = new ServiceCode(); //서비스 코드
	$cmd = new Command(); //Command
	$broker = new ServiceBroker($COMMAND, $CONFIG_FILE); //통신 모듈

	//---------------------------------------
	//Header 설정
	//---------------------------------------
	$reqMsg->setVersion("0100"); //버전 (0100)
	$reqMsg->setMerchantId($SERVICE_ID); //가맹점 아이디
	$reqMsg->setServiceCode($svcCode->CREDIT_CARD); //서비스코드
	$reqMsg->setCommand($cmd->CANCEL_SMS_REQUEST); // 취소_매입취소 요청 Command
	$reqMsg->setOrderId($ORDER_ID); //주문번호
	$reqMsg->setOrderDate($ORDER_DATE); //주문일시(YYYYMMDDHHMMSS)

	//---------------------------------------
	//Body 설정
	//---------------------------------------
	if($TRANSACTION_ID != NULL) //승인 거래번호
		$reqMsg->put($tag->TRANSACTION_ID, $TRANSACTION_ID);
	if($DEAL_AMOUNT != NULL)	// 금액
		$reqMsg->put($tag->DEAL_AMOUNT, $DEAL_AMOUNT);

	//---------------------------------------
	// 요청 전송
	//---------------------------------------
	$broker->setReqMsg($reqMsg); //요청 메시지 설정
	$broker->invoke($svcCode->CREDIT_CARD); //응답 요청
	$resMsg = $broker->getResMsg(); //응답 메시지 확인

	//---------------------------------------
	//요청 결과 Alert 처리
	//---------------------------------------
	$msg = iconv("euc-kr" , "utf-8" , $resMsg->get($tag->RESPONSE_MESSAGE)); //응답 메시지
	$return_tid = $resMsg->get($tag->TRANSACTION_ID); //응답TID


	// 취소 성공 여부
	$is_pg_status = $msg == "성공" ? true : false;	// pg 모듈 호출상태

	// 발행된 현금영수증이 있으면 취소기록
	if($is_pg_status){
		_MQ_noreturn(" update smart_baro_cashbill set BarobillState='6000', bc_iscancel='Y' where bc_ordernum='". $_ordernum ."' and bc_type='pg' and bc_isdelete='N' and bc_iscancel='N' ");
	}

	// 취소결과 로그 기록
	card_cancle_log_write($return_tid,$msg);	// 카드거래번호 , 결과 메세지
}





actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행