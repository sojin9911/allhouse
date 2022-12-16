<?
@extract($_REQUEST);

$today=mktime(); 
$today_time = date('YmdHis', $today);

$ordernum = $_ordernum?$_ordernum:$ordernum;


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

if($_paymethod=='virtual') {

	

} else {

	//---------------------------------------
	// API 클래스 Include
	//---------------------------------------
	include_once PG_DIR."/billgate/config.php";
	include_once PG_DIR."/billgate/class/Message.php";
	include_once PG_DIR."/billgate/class/MessageTag.php";
	include_once PG_DIR."/billgate/class/ServiceCode.php";
	include_once PG_DIR."/billgate/class/Command.php";
	include_once PG_DIR."/billgate/class/ServiceBroker.php";

	// 마지막 주문취소인지 체크 
	$total_chk = _MQ(" select count(*) as cnt from smart_order_product where op_cancel!='Y' and op_oordernum = '".$ordernum."' ");

	//취소 요청 파라메터
	$serviceId	= $MID;					//부분취소 가능 테스트아이디 : M1100408
	$orderDate 	= $today_time; 			 	//취소 요청일시
	$orderId 		= $ordernum;	//취소 요청번호
	$preTransactionId = $TID;	//원결제 거래번호
	$cancelAmount = $_total_amount;	//취소금액
	if($total_chk['cnt'] == 1){
		$usingType = '1000'; // 취소구분 (0000:부분취소, 1000:나머지금액전체취소)
	}else{
		$usingType = '0000'; // 취소구분 (0000:부분취소, 1000:나머지금액전체취소)
	}

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
	$reqMsg->setMerchantId($serviceId); //가맹점 아이디
	$reqMsg->setServiceCode($svcCode->ACCOUNT_TRANSFER); //서비스코드
	$reqMsg->setCommand($cmd->BUY_CANCEL_REQUEST); //부분 취소 요청 Command(9300)
	$reqMsg->setOrderId($orderId); //주문번호
	$reqMsg->setOrderDate($orderDate); //주문일시(YYYYMMDDHHMMSS)

	//---------------------------------------
	//Body 설정
	//---------------------------------------
	if($preTransactionId != NULL) //원거래 거래번호
		$reqMsg->put($tag->PRE_TRANSACTION_ID, $preTransactionId);                              
	if($cancelAmount != NULL) //취소금액
		$reqMsg->put($tag->CANCEL_AMOUNT, $cancelAmount);    
	if($usingType != NULL) //취소구분
		$reqMsg->put($tag->USING_TYPE, $usingType);    
	//---------------------------------------
	// 요청 전송
	//---------------------------------------
	$broker->setReqMsg($reqMsg); //요청 메시지 설정
	$broker->invoke($svcCode->ACCOUNT_TRANSFER); //응답 요청
	$resMsg = $broker->getResMsg(); //응답 메시지 확인

	//---------------------------------------
	//요청 결과
	//---------------------------------------
	$msg = iconv("euc-kr" , "utf-8" , $resMsg->get($tag->RESPONSE_MESSAGE)); //응답 메시지
	$PRE_TRANSACTION_ID = iconv("euc-kr" , "utf-8" , $resMsg->get($tag->PRE_TRANSACTION_ID));
	$RESPONSE_CODE = iconv("euc-kr" , "utf-8" , $resMsg->get($tag->RESPONSE_CODE));
	$RESPONSE_MESSAGE = iconv("euc-kr" , "utf-8" , $resMsg->get($tag->RESPONSE_MESSAGE));
	$DETAIL_RESPONSE_CODE = iconv("euc-kr" , "utf-8" , $resMsg->get($tag->DETAIL_RESPONSE_CODE));
	$DETAIL_RESPONSE_MESSAGE = iconv("euc-kr" , "utf-8" , $resMsg->get($tag->DETAIL_RESPONSE_MESSAGE));
	$CANCEL_DATE = iconv("euc-kr" , "utf-8" , $resMsg->get($tag->CANCEL_DATE));
	$CANCEL_AMOUNT = iconv("euc-kr" , "utf-8" , $resMsg->get($tag->CANCEL_AMOUNT));
	$PROTOCOL_NUMBER = iconv("euc-kr" , "utf-8" , $resMsg->get($tag->PROTOCOL_NUMBER));


	// 취소 성공 여부
	$is_pg_status = $RESPONSE_CODE == "0000" ? true : false;	// pg 모듈 호출상태

	// 취소결과 로그 기록
	card_cancle_log_write($TID,$msg.'('.$DETAIL_RESPONSE_MESSAGE.')');	// 카드거래번호 , 결과 메세지

}
