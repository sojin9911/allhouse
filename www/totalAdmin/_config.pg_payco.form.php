<?php
include_once('wrap.header.php');
$siteInfo = _MQ(" select * from smart_setup where s_uid = 1 ");

// -- 페이코 결제 수단을 가공
$payco_paymethod = $siteInfo['payco_paymethod'] != '' ? explode(",",$siteInfo['payco_paymethod']) : array_keys($arrPaycoInfo['paymethod']);


/*
	payco_use = 페이코 사용여부 
	payco_mode	= 페이코 활성화 여부
	payco_sellerkey = 페이코 가맹점 코드
	payco_cpid = 페이코 상점 ID
	payco_productid = 페이코 상품 ID
	payco_paymethod = 페이코 결제수단 콤마로 구분(,) var.php 에 코드 정의
	payco_app_scheme = 페이코 app 스키마
*/

?>
<form name="frmPayco" id = "frmPayco" action="_config.pg_payco.pro.php" method="post">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>

						
				<tr>
					<th>페이코 사용여부</th>
					<td>
						<?php echo _InputRadio('payco_use', array('Y', 'N'), ($siteInfo['payco_use']?$siteInfo['payco_use']:'N'), '', array('사용', '미사용'), ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr('PAYCO 결제 서비스 신청 후 서비스를 이용해주시기 바랍니다.'); ?>
						<a href="https://partner.payco.com/intro/online.nhn" class="c_btn h22 if_with_tip" target="_blank">PAYCO 가맹점 바로가기</a>						
					</td>
				</tr>

				<tr>
					<th>활성화 모드</th>
					<td>
						<?php echo _InputRadio('payco_mode', array('service', 'test'), ($siteInfo['payco_mode']?$siteInfo['payco_mode']:'test'), '', array('실결제 모드', '테스트 모드'), ''); ?>
							<?php echo _DescStr('PAYCO 결제 서비스 신청 후 서비스를 이용해주시기 바랍니다.'); ?>
					</td>
				</tr>

				<tr class="tr_payco_key">
					<th>가맹점 코드(sellerKey)</th>
					<td>
						<input type="text" name="payco_sellerkey" class="design" value="<?php echo $siteInfo['payco_sellerkey']; ?>">
							<?php echo _DescStr('PAYCO 파트너 센터에서 발급 받으신 가맹점 코드를 입력해 주세요.'); ?>
					</td>
				</tr>
				<tr class="tr_payco_key">
					<th>상점 ID(cpId)</th>
					<td>
						<input type="text" name="payco_cpid" class="design" value="<?php echo $siteInfo['payco_cpid']; ?>">
							<?php echo _DescStr('PAYCO 에서 발급 받으신 상점 ID를 입력해주세요.'); ?>
					</td>
				</tr>		
				<tr class="tr_payco_key">
					<th>상품 ID(productID)</th>
					<td>
						<input type="text" name="payco_productid" class="design" value="<?php echo $siteInfo['payco_productid']; ?>">
							<?php echo _DescStr('PAYCO 에서 발급 받으신 상품 ID를 입력해주세요.'); ?>
					</td>
				</tr>		

				<tr class="">
					<th>결제 수단 설정</th>
					<td>
						<?php echo _InputCheckbox('payco_paymethod', array_keys($arrPaycoInfo['paymethod']), $payco_paymethod, '', array_values($arrPaycoInfo['paymethod']), ''); ?>
						<div class="clear_both"></div>
						<?php echo _DescStr("결제 수단은 최소 1개이상 선택 하셔야합니다."); ?>			
					</td>
				</tr>
				<tr>
					<th>앱 스키마</th>
					<td>
						<input type="text" name="payco_app_scheme" class="design" value="<?php echo $siteInfo['payco_app_scheme']; ?>" style="width:340px;" />
						<div class="tip_box">	
							<?=_DescStr("별도의 APP(앱) 가 있을경우 앱 스키마를 입력해주세요.")?>
							<?=_DescStr("앱 스키마 값을 설정하시면 IOS 기기에서 ISP 결제를 할 경우 결제 완료처리 후 정상적으로 설정된 앱 스키마 값을 통해 쇼핑몰 앱으로 돌아갈 수 있습니다. ")?>
							<?=_DescStr("별도의 앱을 사용하지 않으실 경우 빈값으로 설정해 주세요.")?>
							<?=_DescStr("PG사에 따라 앱 스키마 옵션 지원이 안될 수 있습니다.",'black')?>
						</div>
					</td>			
				</tr>								
				<tr>
					<th>주문연동 방식</th>
					<td class="only_text">간편결제형</td>
				</tr>			
				<tr>
					<th>결제모드 방식</th>
					<td class="only_text">PAY2</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="tip_box">
							<?php 
								echo _DescStr('PAYCO 가맹점 신청절차 : 1.가맹점신청 → 2.전화상담 → 3.파트너센터에서 구비서류 업로드 → 4.코드발급 및 결제시스템 연동 → 5.PAYCO 검수 및 신용카드 심사 → 6.완료(PAYCO 결제오픈)', 'black'); 

								echo _DescStr("복합과세의 경우 반드시 PG와 먼저 복합과세 계약을 신청하셔야합니다.","black");
								echo _DescStr("가상계좌(무통장 입금)의 부분취소는 PG사와 취소연동이 되지 않으며, 주문취소 후 고객에게 직접 환불 해야 합니다.","black");
							?>
						</div>	
					</td>
				</tr>																		
			</tbody>
		</table>
	</div>


	<?php  echo _submitBTNsub(); ?>
</form>

<script type="text/javascript">


	// -- 초기화 함수
	$(document).ready(payco_init);

	// -- submit 함수 
	$(document).on('submit','#frmPayco',function(){
		
		// -- 결제 수단 체크된 항목의 개수를 가져와서 체킹 {{{
		var chkLen = $(this).find('input[name="payco_paymethod[]"]:checked').length * 1;
		if(chkLen < 1){
			alert("결제 수단은 최소 1개이상 선택해 주세요.");
			return false;
		}
		// -- 결제 수단 체크된 항목의 개수를 가져와서 체킹 }}} 
		return true;
	});

	// -- 페이코 활성화 모드를 클릭 시
	$(document).on('click','#frmPayco input[name="payco_mode"]',payco_init);
	function payco_init()
	{
		var paycoMode = $('#frmPayco input[name="payco_mode"]:checked').val();
		if(paycoMode == 'test'){ $('.tr_payco_key').hide(); }
		else{ $('.tr_payco_key').show(); }
	}

	$(document).on('click', '.js_payco_popup', function(e) {
		e.preventDefault();
		var _url = $(this).data('url');
		var _width = $(this).data('width');
		var _height = $(this).data('height');
		window.open(_url, 'payco', 'width='+_width+',height='+_height+',top=100,scrollbars=yes');
	});
</script>
<?php include_once('wrap.footer.php'); ?>