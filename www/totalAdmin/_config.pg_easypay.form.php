<?php

	// --------- JJC : 간편결제 - 페이플 : 2021-06-05 ---------
	//		페이지 전체 수정

	include_once('wrap.header.php');


	// -- 페이코 결제 수단을 가공
	$payco_paymethod = $siteInfo['payco_paymethod'] != '' ? explode(",",$siteInfo['payco_paymethod']) : array_keys($arrPaycoInfo['paymethod']);


?>
<form name="frmPayco" id = "frmPayco" action="_config.pg_easypay.pro.php" method="post">


	<?php // --------- JJC : 간편결제 - 페이플 : 2021-06-05 --------- ?>
	<div class="group_title js_easypay_payco"><strong>페이플 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>

				<tr>
					<th>페이플 간편결제 사용여부</th>
					<td>
						<?php echo _InputRadio('payple_use', array('N' , 'Y'), ($siteInfo['s_payple_use']?$siteInfo['s_payple_use']:'N'), '', array('미사용' , '사용',), ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr('페이플 결제 서비스 신청 후 서비스를 이용해주시기 바랍니다.'); ?>
						<a href="https://www.payple.kr/payple/admin/?ADMIN_=MCT&ACT_=LOGIN" class="c_btn h22 if_with_tip" target="_blank">페이플 파트너관리자 바로가기</a>						
					</td>
				</tr>

				<tr>
					<th>활성화 모드</th>
					<td>
						<?php echo _InputRadio('payple_mode', array('test' , 'service'), ($siteInfo['s_payple_mode']?$siteInfo['s_payple_mode']:'test'), '', array('테스트 모드' , '실결제 모드' ), ''); ?>
							<?php echo _DescStr('페이플 결제 서비스 신청 후 서비스를 이용해주시기 바랍니다.'); ?>
					</td>
				</tr>

				<tr>
					<th>가맹점 ID(cst_id)</th>
					<td>
						<input type="text" name="payple_cst_id" class="design" value="<?php echo $siteInfo['s_payple_cst_id']; ?>">
						<div class="tip_box">
							<?php echo _DescStr('페이플 계약 이후 발급 받으신 가맹점 ID를 입력해 주세요.'); ?>
							<?php echo _DescStr('페이플 테스트 가맹점 ID는 <u>test</u> 입니다.'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>가맹점 운영 Key(custKey)</th>
					<td>
						<input type="text" name="payple_custKey" class="design" value="<?php echo $siteInfo['s_payple_custKey']; ?>" style="width:450px;">
						<div class="tip_box">
							<?php echo _DescStr('페이플에서 발급 받으신 가맹점 운영 Key를 입력해주세요.'); ?>
							<?php echo _DescStr('페이플 테스트 가맹점 운영 Key는 <u>abcd1234567890</u> 입니다.'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>취소(환불) key</th>
					<td>
						<input type="text" name="payple_cancelKey" class="design" value="<?php echo $siteInfo['s_payple_cancelKey']; ?>" style="width:450px;">
						<div class="tip_box">
							<?php echo _DescStr('페이플에서 발급 받으신 취소(환불) key를 입력해주세요. 취소 시 필요합니다.'); ?>
							<?php echo _DescStr('페이플 테스트 취소(환불) key는 <u>a41ce010ede9fcbfb3be86b24858806596a9db68b79d138b147c3e563e1829a0</u> 입니다.'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="tip_box">
							<?php 
								echo _DescStr('페이플 간편결제는 최초 카드정보 등록과 함께 6자리 비밀번호 인증 후, 추가 결제 시 간편하게 이용하는 결제방식입니다. 단, 로그인하신 회원에게 적용됩니다.', 'black'); 
								echo _DescStr("면세 또는 복합과세의 경우 반드시 페이플과 먼저 복합과세 계약을 신청하셔야합니다.","black");
								echo _DescStr("<u>페이플 간편결제 주문의 경우 부분취소가 불가합니다.</u>","black");
								echo _DescStr("페이플 계약 시 등록하신 도메인과 운영 도메인이 다를 경우 실결제 모드 변경 시 결제가 되지 않을 수 있습니다. 이 경우 페이플 측에 문의하여 확인하시기 바랍니다.","black");
							?>
						</div>	
					</td>
				</tr>																		
			</tbody>
		</table>
	</div>
	<?php // --------- JJC : 간편결제 - 페이플 : 2021-06-05 --------- ?>




	<div class="group_title js_easypay_payco"><strong>페이코 설정</strong></div>
	<div class="data_form js_easypay_payco">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>

						
				<tr>
					<th>페이코 사용여부</th>
					<td>
						<?php echo _InputRadio('payco_use', array('N' , 'Y'), ($siteInfo['payco_use']?$siteInfo['payco_use']:'N'), '', array( '미사용' , '사용'), ''); ?>
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