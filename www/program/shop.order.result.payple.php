<?php

	// 과세여부
	//		과세설정 (Default: Y 이며, 과세:Y, 복합과세:Y, 비과세: N)
	//		- 과세부가세가 있을 경우 과세 표시 -
	$payple_is_vat = ($app_vat_Y == 0 && $app_vat_N > 0  ? "N" : "Y");

	// 복합과세액
	//		복합과세(과세+면세) 주문건에 필요한 금액이며 가맹점에서 전송한 값을 부가세로 설정합니다.과세 또는 비과세의 경우 사용하지 않습니다.
	$payple_vat_price = ($app_vat_Y > 0 && $app_vat_N > 0  ? $app_vat_Y_vat : "0");


	// 페이플 간편결제 연동정보 불러오기
	$ipi_que = " SELECT * FROM smart_individual_payple_info WHERE ipi_inid = '". $row['o_mid'] ."'  ";
	$ipi_row = _MQ($ipi_que);

?>

	<?php //  payple js 호출. 테스트-운영 선택 ?>
	<?php if($siteInfo['s_payple_mode']== "service") {?>
		<script src="https://cpay.payple.kr/js/cpay.payple.1.0.1.js"></script> 
	<?php } ?>
	<?php if($siteInfo['s_payple_mode']== "test") {?> 
		<script src="https://testcpay.payple.kr/js/cpay.payple.1.0.1.js"></script> <!-- 테스트 --> 
	<?php } ?>


    <script>	
		$(document).ready( function () {        
			$('#payAction').on('click', function (event) {

				var obj = new Object();
				obj.PCD_CPAY_VER = "1.0.1";
				obj.PCD_PAY_TYPE = "card";           
				obj.PCD_PAY_WORK = "CERT";

				/* 01 : 빌링키결제 */
				obj.PCD_CARD_VER = "01"

				/* 비밀번호 간편결제 구분 */
				obj.PCD_SIMPLE_FLAG = "Y";
				/* 비밀번호 결제 인증방식 pwd */
				obj.PCD_PAYER_AUTHTYPE = "pwd";
				
				/* PCD_PAYER_ID 는 소스상에 표시하지 마시고 반드시 Server Side Script 를 이용하여 불러오시기 바랍니다. */
				/* 첫 결제 완료 후, 재결제 시 주석을 풀고 리턴받은 데이터 중 PCD_PAYER_ID 를 넣어주세요.  */
				<?php if($ipi_row['ipi_payer_id']) { ?>
					obj.PCD_PAYER_ID = "<?php echo $ipi_row['ipi_payer_id'];?>";
				<?php } ?>

				/* 가맹점 인증요청 */
				obj.payple_auth_file = "/addons/payple/auth.php";

				obj.PCD_PAYER_NO = "";
				obj.PCD_PAY_OID = "<?php echo $ordernum?>";// 주문자명
				obj.PCD_PAYER_NAME = "<?php echo $row['o_oname']?>";// 주문자명
				obj.PCD_PAYER_HP = "<?php echo $row['o_ohp']?>";// 주문자 핸드폰
				obj.PCD_PAYER_EMAIL = "<?php echo $row['o_oemail'];?>"; // 주문자 이메일
				obj.PCD_PAY_GOODS = "<?php echo $app_product_name;?>"; // 상품명
				obj.PCD_PAY_TOTAL = <?php echo $row['o_price_real'];?>; // 결제금액
				obj.PCD_PAY_ISTAX = "<?php echo $payple_is_vat;?>";
				obj.PCD_PAY_TAXTOTAL = <?php echo $payple_vat_price;?>;

				/* 결과를 콜백 함수로 받고자 하는 경우 함수 설정 추가 */
				//obj.callbackFunction = getResult;  // getResult : 콜백 함수명 
					
				obj.PCD_RST_URL = "<?php echo $system['url'];?>/program/shop.order.result.payple_return.php";
			
				PaypleCpayAuthCheck(obj);
				
				event.preventDefault();
			
			});   
		});
    </script>