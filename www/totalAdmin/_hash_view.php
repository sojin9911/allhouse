<?php include_once('wrap.header.php'); ?>

<form action="_hash_view.pro.php" method="post">
	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="group_title"><strong>상품리스트 해시태그 노출 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>노출여부</th>
					<td>
						<?php echo _InputRadio('_product_list_hashtag_view', array('Y', 'N'), ($siteInfo['s_product_list_hashtag_view']?$siteInfo['s_product_list_hashtag_view'] : 'Y'), '', array('노출', '비노출')); ?>
					</td>
				</tr>
				<tr>
					<th>노출수</th>
					<td>
						<input type="text" name="_product_list_hashtag_cnt" class="design number_style" value="<?php echo $siteInfo['s_product_list_hashtag_cnt']; ?>" placeholder="" style="width:50px"><span class="fr_tx">개</span>
						<?php echo _DescStr('제한하지 않을 경우 0을 입력하세요.'); ?>
					</td>
				</tr>
				<tr>
					<th>무작위 노출</th>
					<td>
						<?php echo _InputRadio('_product_list_hashtag_shuffle', array('Y', 'N'), ($siteInfo['s_product_list_hashtag_shuffle']?$siteInfo['s_product_list_hashtag_shuffle']:'Y'), '', array('무작위 노출', '순서대로 노출')); ?>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php echo _DescStr('상품 리스트 상단의 해시태그 노출을 설정합니다.'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<?php echo _submitBTNsub(); ?>
</form>

<?php include_once('wrap.footer.php'); ?>