<?php include_once('wrap.header.php');?>

<form action="_config.display.review.pro.php" method="POST">
	<div class="group_title">
		<strong>메인 리뷰관리 설정</strong>
	</div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>노출설정</th>
					<td>
						<?php echo _InputRadio('s_main_review', array('Y', 'N'), (isset($siteInfo['s_main_review'])?$siteInfo['s_main_review']:'A'), ' class="js_main_review"', array('노출', '비노출'), ''); ?>
						<div class="tip_box">
							<?php echo _DescStr('베스트리뷰가 있는 스킨에서 베스트리뷰 노출 여부를 제어합니다.'); ?>
							<?php echo _DescStr('노출로 설정하더라도 노출 조건에 맞지 않는 경우 노출되지 않습니다.'); ?>
						</div>
					</td>
				</tr>
				<tr class="js_detail_setting">
					<th>노출조건 설정</th>
					<td>
						<div class="fr_tx">리뷰 노출 기준을 </div>
						<?php echo _InputSelect('s_main_review_porder', array('S', 'I', 'R'), $siteInfo['s_main_review_porder'], '', array('상품 판매순', '상품 정렬순', '종합리뷰 높은순')); ?>
						<div class="fr_tx">으로 리뷰평점</div>
						<?php echo _InputSelect('s_main_review_score', array(5, 4, 3, 2, 1), $siteInfo['s_main_review_score'], '', array(5, 4, 3, 2, 1)); ?>
						<div class="fr_tx">점 이상의 </div>
						<?php echo _InputSelect('s_main_review_view', array('A', 'P'), $siteInfo['s_main_review_view'], '', array('모든리뷰', '포토리뷰')); ?>
						<div class="fr_tx">를 상품 기준 최대</div>
						<?php echo _InputSelect('s_main_review_limit', array(3,6,9,12), ($siteInfo['s_main_review_limit']?$siteInfo['s_main_review_limit']:3) , '' , '', ''); ?>
						<div class="fr_tx">개를 노출합니다.</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php echo _submitBTNsub(); ?>
</form>


<script type="text/javascript">
	function manual_setting() {
		var _mode = $('.js_main_review:checked').val();
		if(_mode == 'Y') $('.js_detail_setting').show();
		else $('.js_detail_setting').hide();
	}
	$(document).ready(manual_setting);
	$(document).on('click', '.js_main_review', function() { manual_setting(); });
</script>
<?php include_once('wrap.footer.php');?>