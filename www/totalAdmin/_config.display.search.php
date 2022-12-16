<?php
	include_once('wrap.header.php');

	/*
		# DB :: smart_setup
		- s_search_option 	검색조건 var.php 참고
		- s_search_display
		- s_search_mobile_display
		- s_search_diff_orderby
		- s_search_diff_maxcnt
		- s_search_diff_option
	*/

	// -- 스킨별 기본 리스트를 따른다.
	$tempSkinInfo = SkinInfo('category');  // 기존규칙은 상품의 정렬을 따른다.
	$arrProductDisplay['arrList'] = explode(",",$tempSkinInfo['pc_list_depth']); 	// 진열리스트 :: PC
	$arrProductDisplay['listDefault'] = $tempSkinInfo['pc_list_depth_default'];
	$arrProductDisplay['arrListMo'] = explode(",",$tempSkinInfo['mo_list_depth']);	// 진열리스트 :: 모바일
	$arrProductDisplay['listDefaultMo'] = $tempSkinInfo['mo_list_depth_default'];

	// -- 상품 진열 설정 판별
	$searchOption = $siteInfo['s_search_option'] == '' ? array() : explode(",",$siteInfo['s_search_option']);
	$searchDisplayValue = $siteInfo['s_search_display'] > 0 ? $siteInfo['s_search_display'] : $arrProductDisplay['listDefault']; // 없을 시 기본 상품리스트 단수
	$searchMobileDisplayValue = $siteInfo['s_search_mobile_display'] > 0 ? $siteInfo['s_search_mobile_display'] : $arrProductDisplay['listDefaultMo']; //  기본 3,2

	$searchDiffOrderbyValue = $siteInfo['s_search_diff_orderby'] == '' ? 'salecnt':$siteInfo['s_search_diff_orderby']; // 정렬방식
	$searchDiffMaxcntValue = $siteInfo['s_search_diff_maxcnt'] == 0 ? 20:$siteInfo['s_search_diff_maxcnt']; // 최대출력개수
	$searchDiffOptionValue = $siteInfo['s_search_diff_option'] == '' ? 'rand':$siteInfo['s_search_diff_option']; // 뽑는옵션

?>

<form name="formDisplaySearch" action="_config.display.search.pro.php" method="POST">
	<div class="group_title">
		<strong>검색 기본 설정</strong>
	</div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>검색조건 설정</th>
					<td colspan="3">
						<?php echo _InputCheckbox('s_search_option', array_keys($arrSearchOption), $searchOption, ' class="s_search_option"', array_values($arrSearchOption), ''); ?>

						<div class="tip_box">
							<?php echo _DescStr('체크한 항목에 따라 통합검색 페이지 검색조건에 노출됩니다.'); ?>
						</div>
					</td>
				</tr>

				<tr>
					<th>(PC)검색 상품 진열 설정</th>
					<td>


						<?php foreach($arrProductDisplay['arrList'] as $k=>$v){ if(count($arrProductDisplay['arrList']) > 3 && ($k % 2) == 0 && $k!= 0 ) echo '<div class="clear_both"></div>';?>
						<label class="type"><span class="img"><img src="images/<?php echo $arrProductDisplyImage['pc'][$v]; ?>" alt="" /></span><span class="tx"><input type="radio" name="s_search_display" value="<?php echo $v;?>" <?php echo $searchDisplayValue == $v ? 'checked':''?> /><?php echo $v;?> x *</span></label>
						<?php } ?>

					</td>
					<th>(모바일)검색 상품 진열 설정</th>
					<td>
						<?php foreach($arrProductDisplay['arrListMo'] as $k=>$v){ if(count($arrProductDisplay['arrListMo']) > 3 && ($k % 2) == 0 && $k!= 0 ) echo '<div class="clear_both"></div>';?>
						<label class="type"><span class="img"><img src="images/<?php echo $arrProductDisplyImage['mobile'][$v]; ?>" alt="" /></span><span class="tx"><input type="radio" name="s_search_mobile_display" value="<?php echo $v;?>" <?php echo $searchMobileDisplayValue == $v ? 'checked':''?> /><?php echo $v;?> x *</span></label>
						<?php } ?>

					</td>
				</tr>

				<tr>
					<th>다른 고객이 많이 찾은 상품 설정</th>
					<td colspan="3">
						<?php echo _InputSelect('s_search_diff_orderby', array('salecnt', 'review'), $searchDiffOrderbyValue, '', array('상품판매 높은순', '리뷰등록 많은순')); ?>
						<div class="fr_tx">으로 정렬하여 최대</div>
						<input type="text" name="s_search_diff_maxcnt" class="design bold t_black number_style" placeholder="" value="<?php echo $searchDiffMaxcntValue; ?>" style="width:50px;">
						<div class="fr_tx">까지</div>
						<?php echo _InputSelect('s_search_diff_option', array('rand', 'normal'), $searchDiffOptionValue, '', array('랜덤하게', '순서대로')); ?>
						<div class="fr_tx">노출합니다.</div>
						<div class="tip_box">
							<?php echo _DescStr('통합검색 시 검색된 결과가 한개도 없을 시 노출되는 상품에 대한 설정을  할 수 있습니다.'); ?>
							<?php echo _DescStr('최대 노출개수의 경우 <em>100</em> 이하로 입력해 주세요. ','black'); ?>
						</div>
					</td>
				</tr>


			</tbody>
		</table>
	</div>
	<?php echo _submitBTNsub(); ?>
</form>





<script type="text/javascript">
	var form = $(form);

	$(document).on('submit',form,function(){

		var chk_search_diff_orderby = $(this).find('[name="s_search_diff_orderby"]').val();
		var chk_search_diff_maxcnt = $(this).find('[name="s_search_diff_maxcnt"]').val();
		var chk_search_diff_option = $(this).find('[name="s_search_diff_option"]').val();


		if( chk_search_diff_orderby == undefined || chk_search_diff_orderby == ''){
			alert('다른 고객이 많이 찾은 상품 설정을 확인해 주세요.');
			$(this).find('[name="s_search_diff_orderby"]:checked').focus();
			return false;
		}

		if( chk_search_diff_maxcnt == undefined || chk_search_diff_maxcnt == '' || chk_search_diff_maxcnt > 100 ){
			alert("다른 고객이 많이 찾은 상품 설정 중 최대 노출개수는 100 이하로 입력해 주세요.");
			$(this).find('[name="s_search_diff_maxcnt"]:checked').focus();
			return false;
		}

		if( chk_search_diff_option == undefined || chk_search_diff_option == ''){
			alert('다른 고객이 많이 찾은 상품 설정을 확인해 주세요.');
			$(this).find('[name="s_search_diff_option"]:checked').focus();
			return false;
		}

		return true;
	});
</script>
<?php include_once('wrap.footer.php');?>