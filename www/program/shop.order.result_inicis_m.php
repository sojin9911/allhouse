<?php // {{{이니시스모바일}}}
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
$r = $row;
$siteInfo[s_pg_code_escrow] = $siteInfo[s_pg_code_escrow]?$siteInfo[s_pg_code_escrow]:$siteInfo[s_pg_code];
$_pg_mid = $r[o_paymethod]=='virtual'?$siteInfo[s_pg_code_escrow]:$siteInfo[s_pg_code];

// 일반 할부기간
$arr_installment_peroid = array();
$ex_installment_peroid = $siteInfo['s_pg_installment_peroid'] == '' ? array() : explode(",",$siteInfo['s_pg_installment_peroid']);
if($siteInfo['s_pg_installment'] == 'N' || count($ex_installment_peroid) < 1){ // 일시불
	$siteInfo['s_pg_installment_peroid'] = '01';
}else{
	foreach($ex_installment_peroid as $k=>$v){ $arr_installment_peroid[] = $v; }
	$siteInfo['s_pg_installment_peroid'] = implode(":",$arr_installment_peroid);
}

$arrP_RESERVED = array(); // 추가 파라미터값 설정
if($r['o_paymethod'] == 'card' ){ $arrP_RESERVED[] = 'twotrs_isp=Y';  $arrP_RESERVED[] = 'block_isp=Y'; $arrP_RESERVED[] = 'twotrs_isp_noti=N';  }

// LCY : 2021-07-04 : 신용카드 간편결제 추가
if($r['o_easypay_paymethod_type'] == 'easypay_kakaopay'){
    $arrP_RESERVED = array('d_kakaopay=Y'); // 추가 파라미터값 설정
}else if($r['o_easypay_paymethod_type'] == 'easypay_naverpay'){
    $arrP_RESERVED = array('d_npay=Y'); // 추가 파라미터값 설정
}

if($r['o_price_real']>299999){ $arrP_RESERVED[] = 'ismart_use_sign=Y'; } //  ios 앱일경우 mall_app_name=가맹점스키마 를 보내주어야함
if($siteInfo['s_pg_app_scheme'] != ''){ $arrP_RESERVED[] = 'app_scheme='.$siteInfo['s_pg_app_scheme'].'://';  }  // 앱 스키마가 있을경우

// 현금영수증 처리에 따른 방식
if($siteInfo['s_cash_receipt_use'] == 'Y'){ $arrP_RESERVED[] = 'vbank_receipt=Y';  } // 현금영수증 사용일경우 가상계좌는 Y로 해주어야 DISPLAY 된다.
else{  $arrP_RESERVED[] = 'bank_receipt=N'; } // 현금영수증 사용이 N이라면 계좌이체에서 DISPLAY 되지 않도록 처리해 주어야 한다.


$siteDomain = $system['url'];

?>
<!-- 스크립트-->
<form id="form1" name="ini" method="post" action="" encoding="euc-kr" accept-charset="EUC-KR" >
<input type="hidden" name="P_OID" id="textfield2" value="<?=$ordernum?>"/> <?php // 상점 고유 주문번호 ?>
<input type="hidden" name="P_GOODS" id="textfield3"  value="<?=$app_product_name?>" /> <?php // 대표 상품명 ?>
<input type="hidden" name="P_AMT" value="<?=$r[o_price_real]?>" id="textfield4" title="가격"/> <?php // 결제금액 ?>
<input type="hidden" name="P_UNAME" value="<?=$r[o_oname]?>" id="textfield5" title="구매자명"/> <?php // 주문자명 ?>
<input type="hidden" name="P_MNAME" value="<?=$siteInfo[s_adshop]?>" id="textfield6" title="상점명"/> <?php // 상점명 ?>
<input type="hidden" name="P_MOBILE" id="textfield7" value="<?=$r[o_otel] ? $r[o_otel] : $r[o_ohp] ?>" title="구매자명"/>
<input type="hidden" name="P_EMAIL"  id="textfield8" value="<?=$r[o_oemail]?>" title="주문자 이메일" />
<input type="hidden" name="P_VBANK_DT" id="textfield9" value="<?=date('Ymd', time() + ($siteInfo[s_pg_virtual_date] * 86400))?>"/>
<input type="hidden" name="P_RESERVED" value="<?php echo count($arrP_RESERVED) > 0 ? implode("&",$arrP_RESERVED) : null ?>"> <?php // 복합 추가 필드 ?>
<input type="hidden" name="P_NOTI" value=""> <?php // 기타주문정보 ?>
<input type="hidden" name="P_MID" value="<?=$_pg_mid?>">  <?php // 계약된 당사발급 아이디  ?>
<input type="hidden" name="P_NEXT_URL" value="<?php echo $siteDomain ?><?php echo OD_PROGRAM_DIR; ?>/shop.order.result_inicis_m.pro.php">  <?php  // 인증결과수신 Url  ?>
<input type="hidden" name="P_NOTI_URL" value="<?php echo $siteDomain ?><?php echo OD_PROGRAM_DIR; ?>/shop.order.result_inicis_m_paying.php"><?php // 승인결과통보 Url :: 한글도메인 사용불가  ?>
<input type="hidden" name="P_HPP_METHOD" value="2"> <?php // 실물여부 구분; ?>
<input type="hidden" name="P_RETURN_URL" value="<?php echo $siteDomain ?><?php echo OD_PROGRAM_DIR; ?>/shop.order.result_inicis_m_isp.php?ordernum=<?=$ordernum?>" title="결과화면url">
<?php // 결제완료 URL ?>
<input type="hidden" name="P_QUOTABASE" value="<?=$siteInfo['s_pg_installment_peroid']?>"> <?php /* 신용카드 할부기간 지정 :: 50,000원 이상 결제 시, 할부기간 지정 (36개월 MAX) Ex. 01:02:03:04.. 01은 일시불, 02는 2개월 등등 */ ?>
<?php
	// 2017-06-19 ::: 부가세율설정 ::: JJC

	// 부가세
	//		숫자만 입력
	//		대상: ‘부가세업체정함’ 설정업체에 한함
	//		주의: 전체금액의 10%이하로 설정
	//		가맹점에서 등록시 VAT가 총 상품가격의 10% 초과할 경우는 거절됨
	if($app_vat_Y_vat > 0 ) {
		echo '<input type="hidden" name="P_TAX" value="'. $app_vat_Y_vat .'">';
	}

	// 비과세
	//		숫자만 입력
	//		대상: ‘부가세업체정함’ 설정업체에 한함
	//		과세되지 않는 금액
	if($app_vat_N > 0 ) {
		echo '<input type="hidden" name="P_TAXFREE" value="'. $app_vat_N .'">';
	}

	// 2017-06-19 ::: 부가세율설정 ::: JJC
?>
 </form>

<script language="javascript">
function call_pay_form()
{
    var order_form = document.ini;
	var paymethod = "";
	//var wallet = window.open("", "BTPG_WALLET");

	switch("<?=$r[o_paymethod]?>") {
	case "card":   //신용카드
	    paymethod="wcard";
	break;
	case "virtual":   //가상계좌
		paymethod="vbank";
	break;

	case "hpp":   //휴대폰
	paymethod="mobile";
	break;

	}

	//order_form.target = "BTPG_WALLET";  //새창을띄운다.
	document.charset="euc-kr";
	order_form.action = "https://mobile.inicis.com/smart/" + paymethod + "/";
	order_form.submit();
}
</script>

<?php actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행 ?>