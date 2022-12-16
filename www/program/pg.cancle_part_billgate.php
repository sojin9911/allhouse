<?
@extract($_REQUEST);

$today=mktime(); 
$today_time = date('YmdHis', $today);
				
				// 주문번호
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

	//---------------------------------------
	// 값 초기화
	//---------------------------------------
	$SERVICE_ID = $MID;
	$ORDER_ID = $ordernum;	// 새로운 주문번호
	$ORDER_DATE = $today_time;		// 새로운 주문시간
	$TRANSACTION_ID = $TID;	// 취소건의 거래번호
	$DEAL_AMOUNT = $_total_amount;	// 금액
	if($total_chk['cnt'] == 1){
		$REQUIRE_TYPE = '1000'; // 취소구분 (0000:부분취소, 1000:나머지금액전체취소)
	}else{
		$REQUIRE_TYPE = '0000'; // 취소구분 (0000:부분취소, 1000:나머지금액전체취소)
	}


	//---------------------------------------
	//Create Instance
	//---------------------------------------
	$reqMsg = new Message(); 
	$resMsg = new Message(); 
	$tag = new MessageTag();
	$svcCode = new ServiceCode(); 
	$cmd = new Command(); 
	$broker = new ServiceBroker($COMMAND, $CONFIG_FILE);

	//---------------------------------------
	//Header 
	//---------------------------------------
	$reqMsg->setVersion("0100"); 
	$reqMsg->setMerchantId($SERVICE_ID); 
	$reqMsg->setServiceCode($svcCode->CREDIT_CARD); 
	$reqMsg->setCommand($cmd->CANCEL_ADMIN_REQUEST); 
	$reqMsg->setOrderId($ORDER_ID); 
	$reqMsg->setOrderDate($ORDER_DATE);

	//---------------------------------------
	//Body 
	//---------------------------------------
	if($TRANSACTION_ID != NULL) 
		$reqMsg->put($tag->TRANSACTION_ID, $TRANSACTION_ID);                              
	if($REQUIRE_TYPE != NULL)
		$reqMsg->put($tag->REQUIRE_TYPE, $REQUIRE_TYPE);	
	if($DEAL_AMOUNT != NULL) 
		$reqMsg->put($tag->DEAL_AMOUNT, $DEAL_AMOUNT);   

	//---------------------------------------
	//Request
	//---------------------------------------
	$broker->setReqMsg($reqMsg); 
	$broker->invoke($svcCode->CREDIT_CARD); 
	$resMsg = $broker->getResMsg();

	//---------------------------------------
	//Response 
	//---------------------------------------
	//$msg = $resMsg->get($tag->RESPONSE_MESSAGE); 
	$msg = iconv("euc-kr" , "utf-8" , $resMsg->get($tag->RESPONSE_MESSAGE)); //응답 메시지
	$return_tid = $resMsg->get($tag->TRANSACTION_ID); //응답TID 


	$RESPONSE_CODE = iconv("euc-kr" , "utf-8" , $resMsg->get($tag->RESPONSE_CODE));
	$RESPONSE_MESSAGE = iconv("euc-kr" , "utf-8" , $resMsg->get($tag->RESPONSE_MESSAGE));
	$DETAIL_RESPONSE_CODE = iconv("euc-kr" , "utf-8" , $resMsg->get($tag->DETAIL_RESPONSE_CODE));
	$DETAIL_RESPONSE_MESSAGE = iconv("euc-kr" , "utf-8" , $resMsg->get($tag->DETAIL_RESPONSE_MESSAGE));



	// 취소 성공 여부
	$is_pg_status = $RESPONSE_CODE == "0000" ? true : false;	// pg 모듈 호출상태

	// 취소결과 로그 기록
	card_cancle_log_write($return_tid,$msg.'('.$DETAIL_RESPONSE_MESSAGE.')');	// 카드거래번호 , 결과 메세지




}

