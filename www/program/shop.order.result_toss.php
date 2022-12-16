<?php
// SSJ : 토스페이먼츠 PG 모듈 추가 : 2021-02-22
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

$r= $row; // 주문정보 동기화

$siteDomain = $system['url']; //가맹점 도메인 입력

// -- 결제 수단
$tos_paymethod = array(
	'card'=>'카드',
	'virtual'=>'가상계좌',
	'hpp'=>'휴대폰',
);

$clientKey = $siteInfo['s_pg_code'];
$paymethod = $tos_paymethod[$r['o_paymethod']];
$amount = $r['o_price_real']*1; //결제금액("," 를 제외한 결제금액을 입력하세요)
$orderId = $r['o_ordernum']; //주문번호
$orderName = $app_product_name; //주문명
$successUrl = '/program/shop.order.result_toss.pro.php'; //성공처리
$failUrl = '/program/shop.order.result_toss.error.php'; //실패처리
$virtualAccountCallbackUrl = '/program/shop.order.result_toss.casnoteurl.php'; //가상계좌 콜백 URL

$maxCardInstallmentPlan = ($siteInfo['s_pg_installment'] == 'Y' ? $siteInfo['s_pg_installment_peroid'] : 1); //최대 할부 개월 수(1:일시불, 12:최대12개월)
$customerName = trim($r['o_oname']); //주문자명
$customerEmail = trim($r['o_oemail']); //주문자 이메일
$customerMobilePhone = rm_str(trim($r['o_ohp'])); //주문자 휴대폰

// 2017-06-16 ::: 부가세율설정 ::: JJC
if($app_vat_N > 0 ) {
	// 결제금액(amount) 중 면세금액
	//		과세상품과 면세상품을 같이 취급하는 고객사의 경우 신용카드 매출전표에 면세적용을 하기 위해서는 LG 유플러스와 별도 부분면세 계약이 되어야 합니다. (계약필수)
	//		면세처리를 하고자 하는 건은 taxFreeAmount 파라미터를 설정하시면 됩니다.
	//
	//			1) 구매하고자 하는 물품의 금액이 15,000 원이고 면세 받고자 하는 금액이 15,000 원일 경우
	//				taxFreeAmount 의 값을 15000 으로 설정
	//
	//			2) 단일주문번호로 여러 물품을 구매한 금액이 45,000 원이고 일부가 면세품목(면세총액 35,000 원)일 경우
	//				taxFreeAmount 의 값을 35000 으로 설정

	$taxFreeAmount = $app_vat_N; // 면세금액
}
// 2017-06-16 ::: 부가세율설정 ::: JJC

?>

<script src="https://js.tosspayments.com/v1"></script>
<script type="text/javascript">

	// TossPayments 객체 초기화하기
	var clientKey = '<?php echo $clientKey; ?>';
	var tossPayments = TossPayments(clientKey);

	// 결제창 열기
	function requestPayment(){

		tossPayments.requestPayment('<?php echo $paymethod; ?>', {
			amount: <?php echo $amount; ?>,
			orderId: '<?php echo $orderId; ?>',
			orderName: '<?php echo $orderName; ?>',
			successUrl: window.location.origin + '<?php echo $successUrl; ?>',
			failUrl: window.location.origin + '<?php echo $failUrl; ?>'
			<?php if($customerName){ echo ",customerName: '".$customerName."'"; } ?>
			<?php if($customerEmail){ echo ",customerEmail: '".$customerEmail."'"; } ?>
			<?php if($customerMobilePhone){ echo ",customerMobilePhone: '".$customerMobilePhone."'"; } ?>
			<?php if($taxFreeAmount){ echo ",taxFreeAmount: '".$taxFreeAmount."'"; } ?>
			<?php if($virtualAccountCallbackUrl){ echo ",virtualAccountCallbackUrl: window.location.origin + '".$virtualAccountCallbackUrl."'"; } ?>
		});

	}

</script>

<?php actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행 ?>