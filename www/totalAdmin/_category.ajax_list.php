<?php 
	include_once("inc.php");
	/*
		$viewDepth = 노출될 페이지의 depth
		$viewUid = 보여질 페이지 
	*/

	// reload 일경우
	if( $_mode == 'reload'){
		$rowCategory = _MQ("select *from smart_category where c_uid = '".$_uid."'  ");
		$viewDepth = $rowCategory['c_depth'];
	}

	switch($viewDepth)
	{
		case "1":
			$resCategory = _MQ_assoc("select *from smart_category where c_depth = 1 order by c_idx asc");
		break;

		case "2":
			if($locUid1 != ''){ $viewUid = $locUid1; }
			$resCategory = _MQ_assoc("select *from smart_category where c_depth = 2 and c_parent = '".$viewUid."' order by c_idx asc");
		break;

		case "3":
			if($locUid2 != ''){ $viewUid = $locUid2; }
			$resCategory = _MQ_assoc("select *from smart_category where c_depth = 3 and find_in_set('".$viewUid."',c_parent) order by c_idx asc");
		break;
	}

?>
	<!-- 카테고리 목록박스 -->

	<?php if(count($resCategory) > 0) { ?>
		<table class="category_list">
			<colgroup>
				<col width="50"/><col width="*"/><col width="105"/>
			</colgroup>
			<tbody>
			<?php 
				foreach($resCategory as $k=>$v){ 
					$cViewClass = $v['c_view'] == 'Y' ? "blue line" : "gray"; // 노출여부에 따른 클래스명
					$cViewName = $v['c_view'] == 'Y' ? "노출" : "숨김"; // 노출여부에 따른 클래스명
					$cOnclickEvt = $v['c_depth'] != 3 ? " onclick=\"viewCategoryList('".$v['c_depth']."','".$v['c_uid']."');\" style='cursor:pointer;' ":"";
			?>
				<!-- 클릭하면 아래 폼 나오고 hit : tr 온클릭 작업시 a링크 삭제가능 -->
				<tr class="category-list-tr <?=in_array($v['c_uid'],array($locUid1,$locUid2,$_uid)) == true ? 'hit':''?>"  data-depth= "<?=$v['c_depth']?>" data-uid="<?=$v['c_uid']?>"  >
					<td><span class="c_tag <?=$cViewClass?> h22 t2"><?=$cViewName?></span></td>
					 <td class="t_left ctg_name" <?=$cOnclickEvt?>><?=$v['c_name']?></td>
					<td>
						<a href="#none" onclick="idxCategory('up','<?=$v['c_uid']?>','<?=$v['c_depth']?>'); return false;" class="c_btn h22 icon_up" title="위로"></a>
						<a href="#none" onclick="idxCategory('down','<?=$v['c_uid']?>','<?=$v['c_depth']?>'); return false;" class="c_btn h22 icon_down" title="아래로"></a>
						<a href="#none" onclick="viewCategoryForm('modify','<?=$v['c_depth']?>','<?=$v['c_uid']?>'); return false;" class="c_btn h22 t2 scrollto" data-scrollto="view-form">수정</a>
					</td>
				</tr>
			<?php } ?>
			</tbody> 
		</table>
		<?php }else{ ?>

		<?php if($viewDepth != '1' && $viewUid == ''){ ?> 
		<div class="category_before">상위 카테고리를 먼저 선택해주세요.</div>
		<?php }else{ ?>
		<div class="category_before">등록된 카테고리가 없습니다.</div>
		<?php } ?>


		<?php } ?>
