<?php include_once('wrap.header.php'); ?>
<form action="_config.today_view.pro.php" method="post">
<input type="hidden" name="_mode" value="modify">

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">	
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>시간 설정</th>
					<td>
						<input type="text" name="_today_view_time" class="design number_style" value="<?php echo number_format($siteInfo['s_today_view_time']); ?>" placeholder="" style="width:100px">
						<span class="fr_tx">시간</span>
					</td>
				</tr>
				<tr>
					<th>최대 수량</th>
					<td>
						<input type="text" name="_today_view_max" class="design number_style" value="<?php echo number_format($siteInfo['s_today_view_max']); ?>" placeholder="" style="width:100px">
						<span class="fr_tx">개</span>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="tip_box">
							<?php echo _DescStr("최근 본 상품의 유지시간과 최대 노출 수량을 설정합니다."); ?>
							<?php echo _DescStr("최대 수량은 최대 50개까지 설정할 수 있습니다."); ?>
						</div>
					</td>
				</tr>
			</tbody> 
		</table>
	</div>
 
	<?php echo _submitBTNsub(); ?>

</form>

<?php include_once('wrap.footer.php'); ?>