<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



$ordernum = $_SESSION["session_ordernum"];//주문번호

@extract($_REQUEST);

$ool_bank_name_array = array(
    '039'=>'경남',
    '034'=>'광주',
    '004'=>'국민',
    '003'=>'기업',
    '011'=>'농협',
    '031'=>'대구',
    '032'=>'부산',
    '002'=>'산업',
    '045'=>'새마을금고',
    '007'=>'수협',
    '088'=>'신한',
    '026'=>'신한',
    '048'=>'신협',
    '005'=>'외환',
    '020'=>'우리',
    '071'=>'우체국',
    '037'=>'전북',
    '035'=>'제주',
    '081'=>'하나',
    '027'=>'한국씨티',
    '053'=>'씨티',
    '023'=>'SC은행',
    '009'=>'동양증권',
    '078'=>'신한금융투자증권',
    '040'=>'삼성증권',
    '030'=>'미래에셋증권',
    '043'=>'한국투자증권',
    '069'=>'한화증권'
);



//cookie_chk();

//---------------------------------------
// Include Class(don't modify)
//---------------------------------------
include PG_DIR."/billgate/config.php";
include PG_DIR."/billgate/class/Message.php";
include PG_DIR."/billgate/class/MessageTag.php";
include PG_DIR."/billgate/class/ServiceCode.php";
include PG_DIR."/billgate/class/Command.php";
include PG_DIR."/billgate/class/ServiceBroker.php";

//---------------------------------------
// Create Instance
//---------------------------------------
$reqMsg = new Message(); // Request Message
$resMsg = new Message(); //Response Message
$tag = new MessageTag();
$svcCode = new ServiceCode(); //Service Code
$cmd = new Command(); //Command
//---------------------------------------
// Create Service Broker
//---------------------------------------
$broker = new ServiceBroker($ENCRYPT_COMMAND, $CONFIG_FILE); //communication module
//---------------------------------------
//Set Header
//---------------------------------------
$reqMsg->setVersion("0100"); //version (0100)
$reqMsg->setMerchantId($SERVICE_ID);
$reqMsg->setServiceCode($svcCode->CREDIT_CARD); //Service Code
$reqMsg->setCommand($cmd->ID_AUTH_REQUEST); //authentication request Command
$reqMsg->setOrderId($ORDER_ID);
$reqMsg->setOrderDate($ORDER_DATE); //(YYYYMMDDHHMMSS)

//---------------------------------------
//Check RESPONSE_CODE
//---------------------------------------
$isSuccess = false;
if(!strcmp($RESPONSE_CODE, "0000")) { // If authentication is successful, the payment request

	if(is_mobile() == false){
		//Check Sum
		$temp = $SERVICE_ID.$ORDER_ID.$ORDER_DATE;
		$cmd = sprintf("%s \"%s\" \"%s\" \"%s\"", $COM_CHECK_SUM, "DIFF", $CHECK_SUM, $temp);
		$checkSum = exec($cmd) or die("ERROR:899900");
	}

	if($checkSum == 'SUC' || is_mobile() == true ){
		//---------------------------------------
		// Request
		//---------------------------------------
		$broker->invokeMessage($svcCode->CREDIT_CARD, $MESSAGE); //authentication request
		$resMsg = $broker->getResMsg(); //Get response request

		//---------------------------------------
		// Response
		//---------------------------------------
		$RESPONSE_CODE = $resMsg->get($tag->RESPONSE_CODE);
		$RESPONSE_MESSAGE = $resMsg->get($tag->RESPONSE_MESSAGE);
		$DETAIL_RESPONSE_CODE = $resMsg->get($tag->DETAIL_RESPONSE_CODE);
		$DETAIL_RESPONSE_MESSAGE = $resMsg->get($tag->DETAIL_RESPONSE_MESSAGE);

		if(!strcmp($resMsg->get($tag->RESPONSE_CODE), "0000")) {
			$AUTH_AMOUNT = $resMsg->get($tag->AUTH_AMOUNT);
			$TRANSACTION_ID = $resMsg->get($tag->TRANSACTION_ID);
			$AUTH_DATE = $resMsg->get($tag->AUTH_DATE);

			$isSuccess = true;
		}


	}else{
?>
	<script type="text/javascript">
		alert("에러 코드 : <?php echo $checkSum ?>\n\n에러 메시지 : 결제정보오류(return)! 관리자에게 문의 하세요!");
		window.close();
	</script>
<?php
	exit();
	}
}else{
	$AUTH_AMOUNT 		= "";
	$AUTH_DATE 			= "";
}


	$ordernum = $ORDER_ID;

	// - 결제 성공 기록정보 저장 ---
	$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
	if($RESPONSE_CODE) { $app_oc_content = $RESPONSE_CODE."||".$RESPONSE_MESSAGE. "§§"; }
	if($DETAIL_RESPONSE_CODE) { $app_oc_content .= $DETAIL_RESPONSE_CODE."||".$DETAIL_RESPONSE_MESSAGE. "§§"; }

	// 회원정보 추출
	if(is_login()) $indr = $mem_info;

	// 주문정보 추출
	$r = _MQ("select * from smart_order where o_ordernum='". $ordernum ."' ");

	// - 주문결제기록 저장 ---
	$que = "
		insert smart_order_cardlog set
			 oc_oordernum = '".$ordernum."'
			,oc_tid = '". $TRANSACTION_ID ."'
			,oc_content = '". $app_oc_content ."'
			,oc_rdate = now();
	";
	_MQ_noreturn($que);
	// - 주문결제기록 저장 ---
	// - 결제 성공 기록정보 저장 ---

	// 현금영수증을 신청했으면 주문정보 업데이트
	if(isset($AUTH_DATEIDENTIFIER)) {
		_MQ_noreturn("update smart_order set o_get_tax = 'Y' where o_ordernum = '$ordernum'");

		$op_name = _MQ("
			select p.p_name, count(*) as cnt
			from smart_order_product as op
			inner join smart_product as p on (p.p_code=op.op_pcode)
			where op_oordernum='{$ordernum}'
			group by op_oordernum
		");
		// 현금영수증용 상품명 생성
		$cash_product_name = ($op_name['cnt']>0)?$op_name['p_name'].'외 '.($op_name['cnt']-1).'개':$op_name['p_name'];
		_MQ_noreturn("insert into smart_baro_cashbill (bc_type, bc_ordernum,TradeUsage,IdentityNum,Amount,TradeDate,RegistDT,IssueDT,BarobillState,ItemName,NTSConfirmNum) values
		('pg','$ordernum','1','','".$AUTH_AMOUNT."',curdate(),now(),now(),'3000','".addslashes($cash_product_name)."','$AUTH_DATEIDENTIFIER')");
	}


if($isSuccess == true){
//---------------------------------------
// 가맹점 수정 - 성공 시 가맹점 결과 처리
// 1. 가맹점 주문번호 및 갤럭시아 거래번호로 성공 결과 DB 저장
// 2. 결제 성공 페이지 호출
//---------------------------------------

/*

	$SERVICE_ID 				:	가맹점 아이디
	$ORDER_ID					:	주문번호
	$ORDER_DATE					:	주문일시
	$TRANSACTION_ID				:	거래번호
	$AUTH_AMOUNT				:	승인금액
	$AUTH_DATE					:	승인날짜
	$RESPONSE_CODE 				:	응답코드
	$RESPONSE_MESSAGE 			:	응답메세지
	$DETAIL_RESPONSE_CODE		:	상세응답코드
	$DETAIL_RESPONSE_MESSAGE	:	상세응답메세지

	// 계좌이체일때 현금영수증 리턴값
	$

	// 가상계좌일때 리턴값
	$BANK_CODE					:	은행코드
	$ACCOUNT_NUMBER				:	입금계좌번호
	$AMOUNT 					:	상품금액

*/

include OD_PROGRAM_ROOT."/shop.order.result.pro.php";
if( is_mobile() == true){
	echo "<script language='javascript'>location.href=('/?pn=shop.order.complete');</script>";
}else{
	echo "<script language='javascript'>opener.location.href=('/?pn=shop.order.complete');window.close();</script>";
}

}else {
//---------------------------------------
// 가맹점 수정 - 실패 시 가맹점 결과 처리
// 1. 가맹점 주문번호 및 갤럭시아 거래번호로 실패 결과 DB 저장
// 2. 결제 실패 페이지 호출
//---------------------------------------
	if( is_mobile() == true){
		_MQ_noreturn("update smart_order set o_status='결제실패' where o_ordernum='". $ordernum ."' ");
		echo "<script language='javascript'>alert('결제에 실패하였습니다. 다시 한번 확인 바랍니다.');location.href=('/?pn=shop.order.result');</script>";

	}else{
		_MQ_noreturn("update smart_order set o_status='결제실패' where o_ordernum='". $ordernum ."' ");
		echo "<script language='javascript'>alert('".iconv('euc-kr','utf-8',$RESPONSE_MESSAGE)." 결제에 실패하였습니다. 다시 한번 확인 바랍니다.');opener.location.href=('/?pn=shop.order.result');window.close();</script>";

	}
}







actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행