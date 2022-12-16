<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

$r= $row; // 주문정보 동기화

$siteDomain = $system['url']; //가맹점 도메인 입력		

# 카드결제에 필요한 셋팅
/**************************
* 1. 라이브러리 인클루드 *
**************************/
require_once(PG_DIR."/inicis/libs/INIStdPayUtil.php");
require_once(PG_DIR."/inicis/libs/sha256.inc.php");    

$siteInfo[s_pg_code_escrow] = $siteInfo[s_pg_code_escrow]?$siteInfo[s_pg_code_escrow]:$siteInfo[s_pg_code];

$_pg_mid = $r[o_paymethod]=='virtual'?trim($siteInfo[s_pg_code_escrow]):trim($siteInfo[s_pg_code]);

$siteInfo['s_pg_escrow_skey'] = trim($siteInfo['s_pg_escrow_skey']) ? trim($siteInfo['s_pg_escrow_skey']) : trim($siteInfo['s_pg_skey']);
$_pg_skey = $r[o_paymethod]=='virtual' ? $siteInfo['s_pg_escrow_skey'] : $siteInfo[s_pg_skey]; // 사인키

/***************************************
* 2. INIpay50 클래스의 인스턴스 생성  *
***************************************/
$inipay = new INIStdPayUtil();

$timestamp = $inipay->getTimestamp();   // util에 의해서 자동생성
$ordernum = $ordernum; // 가맹점 주문번호(가맹점에서 직접 설정)		
$price = $r[o_price_real];        // 상품가격(특수기호 제외, 가맹점에서 직접 설정)

$mKey = hash("sha256", $_pg_skey);

$params = array(
    "oid" => $ordernum,
    "price" => $price,
    "timestamp" => $timestamp
);

$sign = $inipay->makeSignature($params);

if($r[o_paymethod] == "card") $gopaymethod = "Card";
if($r[o_paymethod] == "iche") $gopaymethod = "DirectBank";
if($r[o_paymethod] == "virtual") $gopaymethod = "VBank";
if($r[o_paymethod] == "hpp") $gopaymethod = "HPP"; // 휴대폰 결제

// LCY : 2021-07-04 : 신용카드 간편결제 추가
if($r['o_easypay_paymethod_type'] == 'easypay_kakaopay'){
    $gopaymethod = 'onlykakaopay';
}else if($r['o_easypay_paymethod_type'] == 'easypay_naverpay'){
    $gopaymethod = 'onlynaverpay';
}

// 일반 할부기간
$arr_installment_peroid = array();
$ex_installment_peroid = $siteInfo['s_pg_installment_peroid'] == '' ? array() : explode(",",$siteInfo['s_pg_installment_peroid']);
if($siteInfo['s_pg_installment'] == 'N' || count($ex_installment_peroid) < 1){ // 일시불
	$siteInfo['s_pg_installment_peroid'] = '0';
}else{
	foreach($ex_installment_peroid as $k=>$v){ $arr_installment_peroid[] = $v < 10 ? $v : $v; }
	$siteInfo['s_pg_installment_peroid'] = implode(":",$arr_installment_peroid);
}

// 이니시스 결제창 스크립트 호출
$pgScriptUrl = $_pg_mid == "INIpayTest" ? "https://stgstdpay.inicis.com/stdjs/INIStdPay.js":"https://stdpay.inicis.com/stdjs/INIStdPay.js";


/*
	- PG 전달 파라미터 정의 {{{{{
*/

	$arrPgInput = array();
	$arrPgInput['gopaymethod'] = $gopaymethod; // 이니시스 결제수단 코드
	$arrPgInput['paymethod'] = $gopaymethod; // 이니시스 결제수단 코드
	$arrPgInput['goodname'] = $app_product_name; // 상품명
	$arrPgInput['buyername'] = $r['o_oname'];  // 구매자명
	$arrPgInput['buyeremail'] = $r['o_oemail']; // 구매자 이메일
	$arrPgInput['buyertel'] = $r['o_otel'] ? $r['o_otel'] : $r['o_ohp']; // 구매자 전화번호

	$arrPgInput['price'] = $price; // 결제금액
	$arrPgInput['mid'] = $_pg_mid; // PG사 아이디 
	$arrPgInput['timestamp'] = $timestamp; // 타임스탬프 - 이니시스 제공 라이브러리로 생성

	$arrPgInput['mKey'] = $mKey; // signkey 에 대한 해시값
	$arrPgInput['oid'] = $ordernum; // 상점 고유 주문번호 
	$arrPgInput['signature'] = $sign; // 위변조 방지 SHA256 Hash 값

	$arrPgInput['returnUrl'] = $siteDomain.OD_PROGRAM_DIR."/shop.order.result_inicis_std.pro.php"; // 결제창을 통해 인증완료된 결과를 수신받고 승인요청을 해서 결과를 표시할 페이지 URL
	$arrPgInput['popupUrl'] = $siteDomain.OD_PROGRAM_DIR."/shop.order.result_inicis_std_popup.php"; // 팝업처리Url
	$arrPgInput['closeUrl'] = $siteDomain.OD_PROGRAM_DIR."/shop.order.result_inicis_std_close.php"; // 결제창 닫기처리Url
	$arrPgInput['quotabase'] = $siteInfo['s_pg_installment_peroid']; // 일반 할부개월수, 2:3:4:5
	$arrPgInput['version'] = '1.0'; //  결제창 버전
	$arrPgInput['currency'] = 'WON'; // 환율설정
	$arrPgInput['useescrow'] = $siteInfo['s_pg_code_escrow'] <> '' && $r['o_paymethod'] == "virtual"  ? 'useescrow':''; // 에스크로 사용여부

	// 가맹점데이터 a=A&b=B 이런식으로 생성가능
	$addOptionParm = array(); // 추가 파라미터값 설정 
	if( count($addOptionParm) > 0 ) {  $arrPgInput['merchantData'] = implode("&",$addOptionParm) ; } 

	//  결제수단별 추가 옵션
	if($r['o_paymethod'] == "virtual") { // 가상계좌라면
		 $_virtual_due_date = date('Ymd', time() + ($siteInfo['s_pg_virtual_date'] * 86400)); // 입금기한
		 $arrPgInput['acceptmethod'] = "HPP(2):OCB:va_receipt:vbank(".$_virtual_due_date."):useescrow";
	}else{
		$arrPgInput['acceptmethod'] = "HPP(2):Card(0):OCB:VBank:DirectBank:receipt:cardpoint".($gopaymethod=='DirectBank'?':useescrow':'');
	}

    // LCY : 2021-07-04 : 신용카드 간편결제 추가
    if($r['o_easypay_paymethod_type'] == 'easypay_kakaopay'){
        $arrPgInput['acceptmethod'] = 'cardonly';
    }else if($r['o_easypay_paymethod_type'] == 'easypay_naverpay'){
        $arrPgInput['acceptmethod'] = 'cardonly';
    }


	// 계좌 이체시 현금영수증처리에따른 방법 추가처리 
	if($siteInfo['s_cash_receipt_use'] != 'Y' && $r['o_get_tax'] != 'Y') { $arrPgInput['acceptmethod'] .=":no_receipt"; }


	// 2017-06-19 ::: 부가세율설정 ::: JJC
	// 부가세
	//		숫자만 입력
	//		대상: ‘부가세업체정함’ 설정업체에 한함
	//		주의: 전체금액의 10%이하로 설정
	//		가맹점에서 등록시 VAT가 총 상품가격의 10% 초과할 경우는 거절됨
	if($app_vat_Y_vat > 0 ) { $arrPgInput['tax'] = $app_vat_Y_vat; }

	// 비과세
	//		숫자만 입력
	//		대상: ‘부가세업체정함’ 설정업체에 한함
	//		과세되지 않는 금액
	if($app_vat_N > 0 ) { $arrPgInput['taxfree'] = $app_vat_N; } 
	// 2017-06-19 ::: 부가세율설정 ::: JJC

/*
	- PG 전달 파라미터 정의 }}}}}}}}}}}}}
*/

/*	// ### return ################
	// 총과세 : $app_vat_Y 
	// 총면세 : $app_vat_N 
	// 과세공급가 : $app_vat_Y_tot
	// 과세부가세 : $app_vat_Y_vat

	echo $app_vat_Y.'<br/>';
	echo $app_vat_N.'<br/>';
	echo $app_vat_Y_tot.'<br/>';
	echo $app_vat_Y_vat.'<br/>';*/
?>

<form name=ini id="ini_form" method="post" target="common_frame"> 
<?php foreach($arrPgInput as $key=>$val){ echo "<input type='hidden' name='".$key."' value='".$val."'   />"; } ?>
</form>
<script language="javascript" type="text/javascript" src="<?php echo $pgScriptUrl ?>" charset="UTF-8"></script>
<script language="JavaScript" type="text/JavaScript">

	function ini_submit()
	{
		// MakePayMessage()를 호출함으로써 플러그인이 화면에 나타나며, Hidden Field
		// 에 값들이 채워지게 됩니다. 일반적인 경우, 플러그인은 결제처리를 직접하는 것이
		// 아니라, 중요한 정보를 암호화 하여 Hidden Field의 값들을 채우고 종료하며,
		// 다음 페이지인 INIsecureresult.php로 데이터가 포스트 되어 결제 처리됨을 유의하시기 바랍니다.
		if(document.ini.goodname.value == "")  // 필수항목 체크 (상품명, 상품가격, 구매자명, 구매자 이메일주소, 구매자 전화번호)
		{
			alert("상품명이 빠졌습니다. 필수항목입니다.");
			return false;
		}
		else if(document.ini.buyername.value == "")
		{
			alert("구매자명이 빠졌습니다. 필수항목입니다.");
			return false;
		} 
		else if(document.ini.buyeremail.value == "")
		{
			alert("구매자 이메일주소가 빠졌습니다. 필수항목입니다.");
			return false;
		}
		else if(document.ini.buyertel.value == "")
		{
			alert("구매자 전화번호가 빠졌습니다. 필수항목입니다.");
			return false;
		}
		else
		{
				INIStdPay.pay('ini_form');
		}
	}
//-->
</script>

<?php actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행 ?>