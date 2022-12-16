<?PHP
	include_once("wrap.header.php");
?>


<form name="frm" method="post" action="_config.tax.pro.php"  target="common_frame">

<!-- ● 단락타이틀 -->
<div class="group_title"><strong>바로빌 설정</strong></div>

<!-- 검색영역 -->
<div class="data_form">

	<table class="table_form" summary="검색항목">
			<colgroup>
				<col width="180"><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>가입안내</th>
					<td>

						<strong class="bold">1. 바로빌 가입</strong><br>
							&nbsp;&nbsp;&nbsp;&nbsp;<B>테스트 회원가입</B> : <A HREF="http://testbed.barobill.co.kr" target='_blank'><u>http://testbed.barobill.co.kr</u></A><br>
							&nbsp;&nbsp;&nbsp;&nbsp;<B>실연동 회원가입</B> : <A HREF="http://www.barobill.co.kr" target='_blank'><u>http://www.barobill.co.kr</u></A><br>
							&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight:bold; color:green">회원가입시 연동회원으로 가입하시고, 연동코드에 <span style="color:red">"GOBEYOND"</span> 을 입력하시기 바랍니다.</span><br>
							&nbsp;&nbsp;&nbsp;&nbsp;링크를 클릭하여 회원가입 하시기 바랍니다.<br><br>

						<strong class="bold">2. 바로빌 공인인증서 등록</strong><br>
							&nbsp;&nbsp;&nbsp;&nbsp;전자문서 &gt; 환경설정 &gt; 공인인증서관리<br>
							&nbsp;&nbsp;&nbsp;&nbsp;위 메뉴를 통해 세금계산서에 연동할 업체 공인인증서을 등록하시기 바랍니다.<br><br>

						<strong class="bold">3. 바로빌 충전</strong><br>
							&nbsp;&nbsp;&nbsp;&nbsp;마이페이지&gt; 포인트관리 &gt; 충전하기<br>
							&nbsp;&nbsp;&nbsp;&nbsp;위 메뉴를 통해 포인트를 충전하시기 바랍니다. <br>
							&nbsp;&nbsp;&nbsp;&nbsp;(단, 테스트의 경우 일정 포인트를 제공해드리고 있습니다.)<br>

					</td>
				</tr>

				<tr >
					<th>사용여부<br></th>
					<td>
						<?php echo _InputRadio( 'TAX_CHK' , array('Y','N') ,  (!$siteInfo['TAX_CHK'] ? 'N' : $siteInfo['TAX_CHK'] ) , '' , array('사용','미사용') , ''); ?>
						<?php echo _DescStr('세금계산서와 현금영수증 발행 시 공통으로 적용됩니다.'); ?>
					</td>
				</tr>


				<tr class="auth_view">
					<th>서비스여부</th>
					<td>
						<?php echo _InputRadio( 'TAX_MODE' , array('service','test') , (!$siteInfo['TAX_MODE'] ? 'test' : $siteInfo['TAX_MODE'] ) , '' , array('서비스모드','테스트모드') , ''); ?>
						<?php echo _DescStr('테스트 모드 경우 실제 연동이 이루어지지 않습니다.'); ?>
					</td>
				</tr>

				<tr class="auth_view">
					<th>가입정보<span class="ic_ess" title="필수"></span></th>
					<td>
						<span class="fr_tx" style="width:105px">바로빌 가입자명</span><span class="bar"></span><input type="text" name="TAX_BAROBILL_NAME" class="design" style="width:200px" value="<?php echo $siteInfo['TAX_BAROBILL_NAME']; ?>" />
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx" style="width:105px">바로빌 가입아이디</span><span class="bar"></span><input type="text" name="TAX_BAROBILL_ID" class="design" style="width:200px" value="<?php echo  $siteInfo['TAX_BAROBILL_ID']; ?>" />
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx" style="width:105px">바로빌 가입비번</span><span class="bar"></span><input type="password" name="TAX_BAROBILL_PW" class="design" style="width:200px" value="<?php echo $siteInfo['TAX_BAROBILL_PW']; ?>" />
						<div class="dash_line"><!-- 점선라인 --></div>
						<?PHP
							// 상태값 추출
							if($siteInfo[TAX_BAROBILL_ID] && $siteInfo['TAX_CERTKEY']) {
								// 세금계산서 잔여포인트 추출 - return_balance
								//include_once( dirname(__FILE__)."/../addons/barobill/api_ti/_tax.GetBalanceCostAmount.php");
								echo '<script>
											(function(){
												// 바로빌 잔여 포인트 추출
												$.get("/totalAdmin/ajax.simple.php?_mode=getBalanceCostAmount", function( data ) {
													if(data.indexOf("오류") > -1){
														$(".js_return_balance").html("<font style=\"color:red;font-size:15px;\">" + data + "</font>");
													}else{
														$(".js_return_balance").html("<font style=\"color:red;font-size:15px;\">" + data + "</font>P");

														// 2018-08-27 SSJ :: 바로빌-현금영수증 문서키 중복 체크를 통해 아이디 유효성 체크
														$.get("/totalAdmin/ajax.simple.php?_mode=check_key", function( data ) {
															console.log(data);
															if(data != ""){
																$(".js_return_error").html("<font style=\"color:red;font-size:12px;\">※ " + data + "</font>").show();
															}
														}, "text");
													}
												}, "text");
											})();
										</script>';
								echo '<span class="fr_tx" style="width:95px">바로빌 잔여포인트</span><span class="bar"></span><span class="fr_tx js_return_balance">조회중입니다.</span><a href="/addons/barobill/api_barobill/GetCashChargeURL.php" target="_blank" class="c_btn h27 black left10">바로빌포인트충전</a>';
								//echo '<div class="dash_line"><!-- 점선라인 --></div>';
								echo '<div class="tip_box">';
								echo _DescStr('세금계산서 발행 시 포인트가 소모되며, 바로빌 포인트가 없으면 세금계산서 발행이 되지 않습니다.');
								echo _DescStr('현금영수증 발행에는 포인트가 소모되지 않습니다.');
								echo '</div>';

								// 2018-08-27 SSJ :: 바로빌-현금영수증 문서키 중복 체크를 통해 아이디 유효성 체크
								echo '<span class="fr_tx js_return_error" style="display:none;"></span>';
							}
						?>
					</td>
				</tr>

				<input type="hidden" name="TAX_CERTKEY" value="<?php echo $tax_barobill_certkery; ?>" />

			</tbody>
		</table>

</div>
<!-- // 검색영역 -->



<!-- ● 단락타이틀 -->
<div class="group_title"><strong>세금계산서 설정</strong></div>


<!-- 검색영역 -->
<div class="data_form">

	<table class="table_form" summary="검색항목">
			<colgroup>
				<col width="180"><col width="*"/>
			</colgroup>
			<tbody>

				<tr class="auth_view">
					<th>사업자(세금계산서)정보</th>
					<td>
						<span class="fr_tx" style="width:95px">상호명(법인명)</span><span class="bar"></span><input type="text" name="name" class="design" style="width:200px" value="<?php echo $siteInfo['s_company_name']; ?>" />
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx" style="width:95px">대표자명</span><span class="bar"></span><input type="text" name="ceoname" class="design" style="width:100px" value="<?php echo $siteInfo['s_ceo_name']; ?>" />
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx" style="width:95px">사업자등록번호</span><span class="bar"></span><input type="text" name="number1" class="design" style="width:200px" value="<?php echo $siteInfo['s_company_num']; ?>" />
						<?php echo _DescStr('사업자등록번호는 현금영수증 발급 기능에 필수 항목입니다. 반드시 입력하세요.'); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx" style="width:95px">사업장소재지</span><span class="bar"></span><input type="text" name="taxaddress" class="design" style="width:500px" value="<?php echo $siteInfo['s_company_addr']; ?>" />
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx" style="width:95px">업태</span><span class="bar"></span><input type="text" name="taxstatus" class="design" style="width:200px" value="<?php echo $siteInfo['s_item1']; ?>" />
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx" style="width:95px">종목</span><span class="bar"></span><input type="text" name="taxitem" class="design" style="width:200px" value="<?php echo $siteInfo['s_item2']; ?>" />

						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="tip_box">
							<?php echo _DescStr('세금계산서 설정 항목에 입력된 내용은 [환경설정 > 기본설정 > 쇼핑몰 기본정보]에서 설정하신 내용과 동일하며 변경 시 함께 변경됩니다.'); ?>
						</div>
					</td>
				</tr>

				<tr class="auth_view">
					<th>담당자 정보</th>
					<td>
						<span class="fr_tx" style="width:95px">담당자 휴대폰</span><span class="bar"></span><input type="text" name="htel" class="design" value="<?php echo $siteInfo['s_glbmanagerhp']; ?>" />
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx" style="width:95px">담당자 E-mail</span><span class="bar"></span><input type="text" name="email" class="design" value="<?php echo $siteInfo['s_ademail']; ?>" />
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx" style="width:95px">담당자 전화번호</span><span class="bar"></span><input type="text" name="tel" class="design" value="<?php echo $siteInfo['s_glbtel']; ?>" />
					</td>
				</tr>

			</tbody>
		</table>

</div>
<!-- // 검색영역 -->

<!-- SSJ : 현금영수증 필수발행 패치 : 2021-02-01 -->
<!-- ● 단락타이틀 -->
<div class="group_title"><strong>현금영수증 설정</strong></div>


<!-- 검색영역 -->
<div class="data_form">

	<table class="table_form" summary="검색항목">
			<colgroup>
				<col width="180"><col width="*"/>
			</colgroup>
			<tbody>

				<tr class="auth_cashbill">
					<th>현금영수증 필수 발행</th>
					<td>
						<?php echo _InputRadio( 'force_cashbill_use' , array('Y','N') ,  (!$siteInfo['s_force_cashbill_use'] ? 'N' : $siteInfo['s_force_cashbill_use'] ) , '' , array('사용','미사용') , ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_tx">무통장 주문 시 현금 결제액이 </span><input type="text" name="force_cashbill_price" class="design number_style" style="width:100px" value="<?php echo $siteInfo['s_force_cashbill_price']; ?>" /><span class="fr_tx">원 이상일 경우 현금영수증 발행을 신청하여야 주문이 가능합니다.</span>
						<div class="tip_box">
							<?php echo _DescStr('현금 결제액이란 주문금액 중 적립금 사용액, 쿠폰 사용액을 제외한 실 결제 금액을 의미 합니다. '); ?>
							<?php echo _DescStr('배송비는 현금 결제액에 포함되며 현금영수증 발행 금액에도 포함됩니다. '); ?>
						</div>
					</td>
				</tr>

			</tbody>
		</table>

</div>
<!-- // 검색영역 -->
<!-- // SSJ : 현금영수증 필수발행 패치 : 2021-02-01 -->


<?php echo _submitBTNsub(); ?>

</form>

<script>
	/*  메인스타일 ---------- */
	var onoff = function() {
		if($("input[name='TAX_CHK']").filter(function() {if (this.checked) return this;}).val() == "Y") {
			$(".auth_view").find("input").removeAttr("readonly").removeAttr("onclick").removeClass("disabled");
		}
		else {
			$(".auth_view").find("input").attr("readonly","readonly").attr("onclick","return false").addClass("disabled");
		}
	}
	onoff();
	$("input[name='TAX_CHK']").click(function() {onoff();});
	/*  // 메인스타일 ---------- */
</script>

<?PHP
	include_once("wrap.footer.php");
?>