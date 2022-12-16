<?php
	include_once('wrap.header.php');

	/*
		# DB :: smart_setup
		- s_category_display
	*/

	// -- 스킨별 기본 리스트를 따른다.
	$tempSkinInfo = SkinInfo('category');  // 기존규칙은 상품의 정렬을 따른다.
	$arrProductDisplay['arrList'] = explode(",",$tempSkinInfo['pc_best_depth']); 	// 진열리스트 :: PC
	$arrProductDisplay['listDefault'] = $tempSkinInfo['pc_best_depth_default'];
	$arrProductDisplay['arrListMo'] = explode(",",$tempSkinInfo['mo_best_depth']);	// 진열리스트 :: 모바일
	$arrProductDisplay['listDefaultMo'] = $tempSkinInfo['mo_list_depth_default'];

	$categoryDisplayValue = $siteInfo['s_category_display'] > 0 ? $siteInfo['s_category_display'] : $arrProductDisplay['listDefault']; // 없을 시 기본 상품리스트 단수
	$categoryDisplayValueMO = $siteInfo['s_category_display_mobile'] > 0 ? $siteInfo['s_category_display_mobile'] : $arrProductDisplay['listDefaultMo']; // 없을 시 기본 상품리스트 단수

?>

<form name="formDisplayCategory" action="_config.display.category.pro.php" method="POST">
	<div class="group_title">
		<strong>메인 베스트관리 설정</strong>
	</div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>(PC)메인 베스트 상품 진열 설정</th>
					<td>
						<?php 
							foreach($arrProductDisplay['arrList'] as $k=>$v){
								if(count($arrProductDisplay['arrList']) > 3 && ($k % 2) == 0 && $k!= 0 ) {	echo '<div class="clear_both"></div>'; }
						?>
						<label class="type"><span class="img"><img src="images/<?php echo $arrProductDisplyImage['pc'][$v]; ?>" alt="" /></span><span class="tx"><input type="radio" name="s_category_display" value="<?php echo $v;?>" <?php echo $categoryDisplayValue == $v ? 'checked':''?> /><?php echo $v;?> x *</span></label>
						<?php } ?>
					</td>
					<th>(모바일)메인 베스트 상품 진열 설정</th>
					<td>
						<?php 
							foreach($arrProductDisplay['arrListMo'] as $k=>$v){ 
								if(count($arrProductDisplay['arrListMo']) > 3 && ($k % 2) == 0 && $k!= 0 ){ echo '<div class="clear_both"></div>';}
						?>
						<label class="type"><span class="img"><img src="images/<?php echo $arrProductDisplyImage['mobile'][$v]; ?>" alt="" /></span><span class="tx"><input type="radio" name="s_category_display_mobile" value="<?php echo $v;?>" <?php echo $categoryDisplayValueMO == $v ? 'checked':''?> /><?php echo $v;?> x *</span></label>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php echo _DescStr('메인에 노출되는 베스트 상품은 카테고리 관리의 각 카테고리별로 베스트 상품을 설정하여 주시기바랍니다. '); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php echo _submitBTNsub(); ?>
</form>


<script type="text/javascript">

</script>
<?php include_once('wrap.footer.php');?>