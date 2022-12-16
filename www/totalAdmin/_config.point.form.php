<?php
include_once('wrap.header.php');
?>
<form action="_config.point.pro.php" method="post">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>적립금 설정</th>
					<td>
						<span class="fr_tx">적립금은</span>
						<input type="text" name="_pointusevalue" value="<?php echo $siteInfo['s_pointusevalue']; ?>" class="design number_style" style="width:100px">
						<span class="fr_tx">원부터 주문시 현금처럼 사용가능합니다.</span>

						<div class="dash_line"><!-- 점선라인 --></div>

						<span class="fr_tx">한 주문당</span>
						<input type="text" name="_pointuselimit" value="<?php echo $siteInfo['s_pointuselimit']; ?>" class="design number_style" style="width:100px">
						<span class="fr_tx">원까지 사용할수 있습니다. (0은 사용제한 없음)</span>
					</td>
				</tr>
				<tr>
					<th>적립금 지급 설정</th>
					<td>
						<span class="fr_tx">회원가입시</span>
						<input type="text" name="_joinpoint" value="<?php echo $siteInfo['s_joinpoint']; ?>" class="design number_style" style="width:100px">
						<span class="fr_tx">원을</span>
						<input type="text" name="_joinpointprodate" value="<?php echo $siteInfo['s_joinpointprodate']; ?>" class="design number_style" style="width:50px">
						<span class="fr_tx">일 후 적립 (0은 즉시 적립)</span>

						<div class="dash_line"><!-- 점선라인 --></div>

						<span class="fr_tx">상품구매시 상품에 지정된 적립률(%)만큼의 적립금을</span>
						<input type="text" name="_orderpointprodate" value="<?php echo $siteInfo['s_orderpointprodate']; ?>" class="design number_style" style="width:50px">
						<span class="fr_tx">일 후 적립 (0은 즉시 적립)</span>

						<div class="dash_line"><!-- 점선라인 --></div>

						<span class="fr_tx">포토후기 등록시 </span>
						<input type="text" name="_productevalpoint" value="<?php echo $siteInfo['s_productevalpoint']; ?>" class="design number_style" style="width:100px">
						<span class="fr_tx">원을</span>
						<input type="text" name="_productevalprodate" value="<?php echo $siteInfo['s_productevalprodate']; ?>" class="design number_style" style="width:50px">
						<span class="fr_tx">일 후 적립 (0은 즉시 적립)</span>

						<div class="dash_line"><!-- 점선라인 --></div>

						<label class="design">
							<span class="fr_tx">상품후기 작성 제한 : </span><!-- SSJ : 상품후기 작성제한 기능강화 : 2020-08-13 : 포토후기 문구를 상품후기로 변경 -->
							<?php
								echo _InputRadio('_producteval_limit', array('N', 'Y', 'B'), ($siteInfo['s_producteval_limit']?$siteInfo['s_producteval_limit']:'N'), '', array('작성제한 없음', '상품을 구매한 회원만', '상품을 구매한 횟수만큼'));
							?>
						</label>
						<div class="tip_box">
							<?php echo _DescStr('<em>작성제한 없음</em>, <em>상품을 구매한 회원만</em>일 경우 "포토후기 작성 포인트"는 하나의 상품에 한번만 지급됩니다.'); ?>
							<?php echo _DescStr('<em>상품을 구매한 횟수만큼</em>일 경우 상품을 구매한 횟수만큼 상품후기의 작성이 가능하며 포토후기 작성시마다 "포토후기 작성 포인트"가 지급됩니다.'); ?><!-- SSJ : 상품후기 작성제한 기능강화 : 2020-08-13 : 포토후기 문구를 상품후기로 변경 -->
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php echo _submitBTNsub(); ?>
</form>
<?php include_once('wrap.footer.php'); ?>