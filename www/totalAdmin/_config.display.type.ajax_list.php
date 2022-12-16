<?php 
	include_once("inc.php");
	/*
		$viewDepth = 노출될 페이지의 depth
		$viewUid = 보여질 페이지 
	*/
	// reload 일경우
	if( $_mode == 'reload'){
		$row = _MQ("select *from smart_display_type_set where dts_uid = '".$_uid."'  ");
		$viewDepth = $row['dts_depth'];
	}

	switch($viewDepth)
	{
		case "1":
			$res = _MQ_assoc("select *from smart_display_type_set where dts_depth = 1 order by dts_idx asc");
		break;
		case "2":
			if($locUid1 != ''){ $viewUid = $locUid1; }
			$res = _MQ_assoc("select *from smart_display_type_set where dts_depth = 2 and dts_parent = '".$viewUid."' order by dts_idx asc");
		break;
	}
?>
	<!-- 카테고리 목록박스 -->

	<?php if(count($res) > 0) { ?>
		<table class="category_list">
			<colgroup>
				<col width="50"/><col width="*"/><col width="105"/>
			</colgroup>
			<tbody>
			<?php 
				foreach($res as $k=>$v){ 
					$printViewClass = $v['dts_view'] == 'Y' ? "blue line" : "gray"; // 노출여부에 따른 클래스명
					$printViewName = $v['dts_view'] == 'Y' ? "노출" : "숨김"; // 노출여부에 따른 클래스명
					$printOnclickEvt = $v['dts_depth'] != 3 ? " onclick=\"configDisplayType.viewList('".$v['dts_depth']."','".$v['dts_uid']."');\" style='cursor:pointer;' ":"";
			?>
				<!-- 클릭하면 아래 폼 나오고 hit : tr 온클릭 작업시 a링크 삭제가능 -->
				<tr class="item-list-tr <?=in_array($v['dts_uid'],array($locUid1,$locUid2)) == true ? 'hit':''?>"  data-depth= "<?=$v['dts_depth']?>" data-uid="<?=$v['dts_uid']?>"  >
					<td><span class="c_tag <?=$printViewClass?> h22 t2"><?=$printViewName?></span></td>
					 <td class="t_left ctg_name" <?=$printOnclickEvt?>><?=$v['dts_name']?></td>
					<td>
						<a href="#none" onclick="configDisplayType.idx('up','<?=$v['dts_uid']?>','<?=$v['dts_depth']?>'); return false;" class="c_btn h22 icon_up" title="위로"></a>
						<a href="#none" onclick="configDisplayType.idx('down','<?=$v['dts_uid']?>','<?=$v['dts_depth']?>'); return false;" class="c_btn h22 icon_down" title="아래로"></a>
						<a href="#none" onclick="configDisplayType.viewItemForm('modify','<?=$v['dts_depth']?>','<?=$v['dts_uid']?>'); return false;" class="c_btn h22 t2 scrollto" data-scrollto="view-form">수정</a>
					</td>
				</tr>
			<?php } ?>
			</tbody> 
		</table>
		<?php }else{ ?>

		<?php if($viewDepth != '1' && $viewUid == ''){ ?> 
		<div class="category_before">상위 타입을 먼저 선택해주세요.</div>
		<?php }else{ ?>
		<div class="category_before">등록된 타입이 없습니다.</div>
		<?php } ?>


		<?php } ?>
