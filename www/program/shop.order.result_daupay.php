<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

$r= $row; // 주문정보 동기화
$siteDomain = $system['url']; //가맹점 도메인 입력

$CPID			= $siteInfo[s_pg_code];						// 상점ID
$ORDERNO		= $ordernum;								// 주문번호
$PRODUCTTYPE	= "2";										// 상품구분 (1:디지털, 2:실물)
$AMOUNT			= $r[o_price_real];							// 결제금액
$PRODUCTNAME	= $app_product_name; 						// 상품명
$EMAIL			= $r[o_oemail];								// 구매자 이메일
$USERNAME		= $r[o_oname];								// 구매자 명
$ISTEST			= $siteInfo[s_pg_mode] == "test" ? 1 : 0;	// 테스트결제 여부
$PAYMETHOD		= $r[o_paymethod];							// 결제방식
$CASHRECEIPTFLAG= $r[o_get_tax] == "Y" ? 1 : 0;				// 현금영수증 발형여부
$ISESCROW		= $siteInfo[s_view_escrow_join_info] == "Y" ? 1 : 0;	// 에스크로 사용여부 (계좌이체, 가상계좌만 적용) (1:사용 , 0:미사용)
$HOMEURL		= $siteDomain.OD_PROGRAM_DIR."/shop.order.result_daupay_return.php?ordernum=".$ordernum;	// 리턴URL
$RETURNURL; // 리턴 URL은 opener창으로 값을 던지지 않고 새창으로 처리하므로, HOMEURL에서 직접 컨트롤 한다. 32.05.07 오찬식
$ISMOBILE		= is_mobile() ? 1 : 0;

// 결제방식 마다 팝업창의 사이즈가 다르다.
$popup_size		= array(
							"card" => "width=579,height=527",
							"hpp" => "width=579,height=527", // 휴대폰결제
							"iche" => "width=480,height=480",
							"virtual" => "width=468,height=538",
						);

// 일반 할부기간
$arr_installment_peroid = array();
$ex_installment_peroid = $siteInfo['s_pg_installment_peroid'] == '' ? array() : explode(",",$siteInfo['s_pg_installment_peroid']);
if($siteInfo['s_pg_installment'] == 'N' || count($ex_installment_peroid) < 1){ // 일시불
	$siteInfo['s_pg_installment_peroid'] = '0';
}else{
	foreach($ex_installment_peroid as $k=>$v){ $arr_installment_peroid[] = $v < 10 ? $v : $v; }
	$siteInfo['s_pg_installment_peroid'] = '0:'.implode(":",$arr_installment_peroid);
}

?>

<script type="text/javascript">
// 다우페이에서 utf8을 지원하지 않고, 또, 무슨이유에선지 인코딩을 해도 한글이 깨지는 문제로 인해, 팝업창을 직접 띄워서 결제페이지로 넘긴다.
function fnSubmit() {
  if (<?=$ISMOBILE?>){
	window.open("<?php echo $siteDomain.OD_PROGRAM_DIR; ?>/shop.order.result_daupay_popup.php", "DAOUPAY", "fullscreen");
  }else{
	window.open("<?php echo $siteDomain.OD_PROGRAM_DIR; ?>/shop.order.result_daupay_popup.php", "DAOUPAY", "<?=$popup_size[$PAYMETHOD]?>");
  }
}
</script>

<form name="order_info">
	<input type="hidden" name="CPID"  value="<?=$CPID?>">
	<input type="hidden" name="ORDERNO" value="<?=$ORDERNO?>">
	<input type="hidden" name="AMOUNT" value="<?=$AMOUNT?>"><!-- 총 비용 -->

<?php
	$TAXFREECD = '00';
	// 2017-06-19 ::: 부가세율설정 ::: JJC
		// s_vat_product : N - 면세, C - 복합과세
		if(in_array($siteInfo['s_vat_product'] , array('N' , 'C'))) {
			//echo '<input type="hidden" name="TAXFREECD" value="02">';//<!-- TAXFREECD	2	과세 비과세 여부 - (00: 과세, 01: 비과세 , 02:복합과세) -->
			//<!-- TAXFREECD	2	과세 비과세 여부 - (00: 과세, 01: 비과세 , 02:복합과세) -->
			if($siteInfo['s_vat_product'] == 'N') $TAXFREECD = '01';
			else if($siteInfo['s_vat_product'] == 'C') $TAXFREECD = '02';
			echo '<input type="hidden" name="TELNO2" value="' . $app_vat_N . '">';//<!-- 비과세 -->
		}
	// 2017-06-19 ::: 부가세율설정 ::: JJC
?>

	<input type="hidden" name="PRODUCTNAME" value="<?=$PRODUCTNAME?>">
	<input type="hidden" name="PRODUCTTYPE" value="<?=$PRODUCTTYPE?>">
	<input type="hidden" name="BILLTYPE" value="1"> <!-- 과금유형 1로 고정 -->
	<input type="hidden" name="EMAIL" value="<?=$EMAIL?>">
	<input type="hidden" name="USERNAME" value="<?=$USERNAME?>">
	<input type="hidden" name="RETURNURL" value="<?=$RETURNURL?>">
	<input type="hidden" name="HOMEURL" value="<?=$HOMEURL?>">
	<input type="hidden" name="TAXFREECD" value="<?php echo $TAXFREECD; ?>"> <!-- 과세 00, 비과세 01 -->
	<input type="hidden" name="CASHRECEIPTFLAG" value="<?=$CASHRECEIPTFLAG?>"> <!-- 현금영수증 발행여부 -->
	<input type="hidden" name="PAYMETHOD" value="<?=$PAYMETHOD?>"> <!-- 결제수단 -->
	<input type="hidden" name="ISTEST" value="<?=$ISTEST?>"> <!-- 테스트 여부 -->
	<input type="hidden" name="ISESCROW" value="<?=$ISESCROW?>"> <!-- 테스트 여부 -->
	<input type="hidden" name="RESERVEDINDEX1" value="<?=$ISMOBILE?>"> <!-- 모바일여부 -->
	<input type="hidden" name="CPQUOTA" value="<?=$siteInfo['s_pg_installment_peroid']?>"> <!-- 모바일여부 -->
</form>
<?php actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행 ?>