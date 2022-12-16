<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

$r= $row; // 주문정보 동기화
$siteDomain = $system['url']; //가맹점 도메인 입력		

$PG_MODE = $siteInfo[s_pg_mode];

$today=mktime(); 
$today_time = date('YmdHis', $today);

//parameter
$serviceId = $siteInfo[s_pg_code] ;   //테스트서버 : glx_api
$orderDate = $today_time ; //(YYYYMMDDHHMMSS)
$orderId = $ordernum ;
$userId =  $r[o_mid]; 
$userName = $r[o_oname];
$itemName = $app_product_name;
$itemCode = "ITEM_CODE";
$amount = $r[o_price_real];
$userIp = $_SERVER["REMOTE_ADDR"];
$returnUrl = $siteDomain.OD_PROGRAM_DIR."/shop.order.result_billgate.pro.php";

if($r[o_paymethod]=='card') { $_method = 'credit'; } 
if($r[o_paymethod]=='iche') { $_method = 'account'; } 
if($r[o_paymethod]=='virtual') { $_method = 'vaccount'; } 

if($r[o_paymethod]!='card') {
	$returnUrl = $siteDomain.OD_PROGRAM_DIR."/shop.order.result_billgate.pro.".$_method.".php";
}

if($r[o_paymethod]!='virtual') {
	$temp = $serviceId.$orderId.$amount;
	$cmd = sprintf("%s \"%s\" \"%s\"", $COM_CHECK_SUM, "GEN", $temp);
	$checkSum = exec($cmd) or die("ERROR:899900 - JDK 미설치 (서버 관리자에게 문의하세요)");
}

// 일반 할부기간
$arr_installment_peroid = array();
$ex_installment_peroid = $siteInfo['s_pg_installment_peroid'] == '' ? array() : explode(",",$siteInfo['s_pg_installment_peroid']);
if($siteInfo['s_pg_installment'] == 'N' || count($ex_installment_peroid) < 1){ // 일시불
	$siteInfo['s_pg_installment_peroid'] = '0';
}else{
	$arr_installment_peroid[] = 0;
	foreach($ex_installment_peroid as $k=>$v){ $arr_installment_peroid[] = $v < 10 ? $v : $v; }
	$siteInfo['s_pg_installment_peroid'] = implode(":",$arr_installment_peroid);
}

if ($checkSum == '8001'||$checkSum == '8003'||$checkSum == '8009'){
	error_alt($checkSum." Error Message : make checksum error! Please contact your system administrator!");
} else {

?>


<script language="JavaScript" charset="euc-kr">
function checkSubmit(){
	var HForm = document.payment;
	HForm.target = "payment";
	document.charset = "euc-kr";
	
	//테스트 URL 
	HForm.action = "<?=($PG_MODE=='test')?'http://tpay.billgate.net/'.$_method.'/certify.jsp':'https://pay.billgate.net/'.$_method.'/certify.jsp'?>";
	//상용 URL 
	//HForm.action = "https://pay.billgate.net/credit/certify.jsp";

	var option ="width=500,height=477,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,left=150,top=150";
	var objPopup = window.open("", "payment", option);

	if(objPopup == null){	//팝업 차단여부 확인
		alert("팝업이 차단되어 있습니다.\n팝업차단을 해제하신 뒤 다시 시도하여 주십시오.");
	}

	HForm.submit();
}
</script>

<form name="payment" method="post" charset="euc-kr" accept-charset="EUC-KR" >
<input type="hidden" name="SERVICE_ID" value="<?=trim($serviceId)?>">								<!-- 서비스아이디 -->
<input type="hidden" name="AMOUNT" value="<?=$amount?>">									<!-- 결제 금액 -->
<input type="hidden" name="ORDER_ID" class="input" value="<?=$orderId?>">					<!-- 주문번호 -->
<input type="hidden" name="ORDER_DATE" size=20 class="input" value="<?=$orderDate?>">		<!-- 주문일시 -->
<input type="hidden" name="USER_IP" size=20 class="input" value="<?=$userIp?>">				<!-- 고객 IP -->
<input type="hidden" name="ITEM_NAME" size=20 class="input" value="<? echo cutstr($itemName,15); ?>">			<!-- 상품명 -->
<input type="hidden" name="ITEM_CODE" size=20 class="input" value="<?=$itemCode?>">			<!-- 상품코드 -->
<input type="hidden" name="USER_ID" size=20 class="input" value="<?=$userId?>">				<!-- 고객 아이디 -->
<input type="hidden" name="USER_NAME" size=20 class="input" value="<?=$userName?>">			<!-- 고객명 -->
<? if($r[o_paymethod]=='card') {?>
<input type="hidden" name="INSTALLMENT_PERIOD" size=30 class="input" value="<?php echo $siteInfo['s_pg_installment_peroid']; ?>">	<!-- 할부개월수 -->
<? } ?>
<input type="hidden" name="_paymethod" value="<?=$r[o_paymethod]?>">
<input type="hidden" name="RETURN_URL" size=50 class="input" value="<?=$returnUrl?>">		<!-- Return Url -->
<input type="hidden" name="CHECK_SUM" class="input" value="<?=$checkSum?>">					<!-- Check Sum -->
</form>


<?
} // checksum end
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
?>