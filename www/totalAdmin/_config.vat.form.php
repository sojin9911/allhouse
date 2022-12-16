<?php
include_once('wrap.header.php');
$r = _MQ(" select * from smart_setup where s_uid = 1 ");
?>
<form method="post" action="_config.vat.pro.php" >


	<!-- 상품 과세설정 -->
	<div class="group_title"><strong>상품 과세설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>과세 여부</th>
					<td>
						<?php
							if($siteInfo['s_pg_type'] == 'billgate') {
								echo '<div class="tip_box">' . _DescStr("<strong>PG사가 빌게이트일 경우 과세만 적용됩니다.</strong>") . '</div>';
								echo "<input type='hidden' name='s_vat_product' value='Y'>";
							}
							else {
								echo _InputRadio( "s_vat_product" , array('Y','N','C') , $siteInfo['s_vat_product'] ? $siteInfo['s_vat_product'] : "Y" , "" , array('과세','면세','복합과세') , "");
								echo '
									<div class="dash_line"><!-- 점선라인 --></div>
									<div class="tip_box">
										' . _DescStr("과세로 선택할 경우, 전체 상품은 별도 선택없이, 과세로 자동 적용됩니다.") . '
										' . _DescStr("면세로 선택할 경우, 전체 상품은 별도 선택없이, 면세로 자동 적용됩니다.") . '
										' . _DescStr("복합과세로 선택할 경우, 상품 등록시 과세여부를 선택하게 됩니다.") . '
										' . _DescStr("<strong>면세나 복합과세로 결제할 경우, PG사에 면세, 복합과세로의 사용 요청을 하셔야 합니다.</strong>") . '
										' . _DescStr("과세일 경우 세금계산서, 면세일 경우 계산서가 발급되며, 정산시 과세와 면세로 분리하여 확인할 수 있습니다.") . '
									</div>
								';
							}
						?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- 상품 과세설정 -->



	<!-- 배송비 과세설정 -->
	<div class="group_title"><strong>배송비 과세설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>과세 여부</th>
					<td>
						<?php
							if($siteInfo['s_pg_type'] == 'billgate') {
								echo '<div class="tip_box">' . _DescStr("<strong>PG사가 빌게이트일 경우 과세만 적용됩니다.</strong>") . '</div>';
								echo "<input type='hidden' name='s_vat_product' value='Y'>";
							}
							else {
								if($SubAdminMode){
									echo _InputRadio( "s_vat_delivery" , array('Y','N','C') , $siteInfo['s_vat_delivery'] ? $siteInfo['s_vat_delivery'] : "Y" , "" , array('과세','면세','복합과세') , "");
								}
								else {
									echo _InputRadio( "s_vat_delivery" , array('Y','N') , $siteInfo['s_vat_delivery'] ? $siteInfo['s_vat_delivery'] : "Y" , "" , array('과세','면세') , "");
								}
								echo '
									<div class="dash_line"><!-- 점선라인 --></div>
									<div class="tip_box">
										' . _DescStr("과세로 선택할 경우, 전체 배송비는 별도 선택없이, 과세로 자동 적용됩니다.") . '
										' . _DescStr("면세로 선택할 경우, 전체 배송비는 별도 선택없이, 면세로 자동 적용됩니다.") . '
										' . ($SubAdminMode ? _DescStr("복합과세로 선택할 경우, 입점업체별로 과세여부를 선택하게 됩니다.") : '') . '
									</div>
								';
							}
						?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- 배송비 과세설정 -->





	<!-- 할인액 과세설정 -->
	<div class="group_title"><strong>할인액 과세설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>적용방식</th>
					<td>
						<?php
							if($siteInfo['s_pg_type'] == 'billgate') {
								echo '<div class="tip_box">' . _DescStr("<strong>PG사가 빌게이트일 경우 과세부터 차감으로 적용됩니다.</strong>") . '</div>';
								echo "<input type='hidden' name='s_vat_discount' value='Y'>";
							}
							else {
								echo _InputRadio( "s_vat_discount" , array('Y','N' ,'D') , $siteInfo['s_vat_discount'] ? $siteInfo['s_vat_discount'] : "Y" , "" , array('과세부터 차감','면세부터 차감' , '비율로 차감') , "");

								echo '
									<div class="dash_line"><!-- 점선라인 --></div>
									<div class="tip_box">
										' . _DescStr("PG사에 의한 결제(카드, 실시계좌이체 등) 시 복합과세로 결제가 될 경우, 할인될 비용에 대한 처리를 설정합니다.") . '
										' . _DescStr("할인액은 적립금, 예치금, 쿠폰, 프로모션 코드 등의 사용에 의해 결제시 할인된 비용을 의미합니다.") . '
										' . _DescStr("할인액 과세설정을 변경할 경우 PG적용 비용과 정산내역이 달라질 수 있으니 주의하시기 바랍니다.") . '<br><br>
										' . _DescStr("
											예시설정) 과세 : 50,000원, 면세 : 50,000원, 할인액 : 10,000원의 복합 과세로 90,000원이 결제될 경우 다음과 같습니다.<br>

											<table>
											<colgroup>
												<col width=''/>
											</colgroup>
											<thead>
												<tr>
													<th scope='col' rowspan='2'>설정</th>
													<th scope='col' colspan='2'>과세</th>
													<th scope='col' colspan='2'>면세</th>
												</tr>
												<tr>
													<th scope='col' >공급가액</th>
													<th scope='col' >부가세액</th>
													<th scope='col' >공급가액</th>
													<th scope='col' >부가세액</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<th>과세부터 차감될 경우</th>
													<td class='t_right'>36,364 원</td>
													<td class='t_right'>3,636 원</td>
													<td class='t_right'>50,000 원</td>
													<td class='t_right'>0 원</td>
												</tr>
												<tr>
													<th>면세부터 차감될 경우</th>
													<td class='t_right'>45,455 원</td>
													<td class='t_right'>4,545 원</td>
													<td class='t_right'>40,000 원</td>
													<td class='t_right'>0 원</td>
												</tr>
												<tr>
													<th>비율로 차감될 경우</th>
													<td class='t_right'>40,910 원</td>
													<td class='t_right'>4,090 원</td>
													<td class='t_right'>45,000 원</td>
													<td class='t_right'>0 원</td>
												</tr>
											</tbody>
										</table>
										") . '
									</div>
								';

							}
						?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- 할인액 과세설정 -->


	<?php echo _submitBTNsub(); ?>

</form>



<?php include_once('wrap.footer.php'); ?>