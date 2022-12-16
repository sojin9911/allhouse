<?php 
	include_once("inc.php");
	/*
		$viewDepth = 노출될 페이지의 depth
		$viewUid = 보여질 페이지 

	*/

	// reload 일경우
	if( $_mode == 'reload'){
		$rowAdminMenu = _MQ("select *from smart_admin_menu where am_uid = '".$_uid."'  ");
		$viewDepth = $rowAdminMenu['am_depth'];
	}

	switch($viewDepth)
	{
		case "1":
			$resAdminMenu = _MQ_assoc("select *from smart_admin_menu where am_depth = 1 order by am_idx asc");
		break;

		case "2":
			if($locUid1 != ''){ $viewUid = $locUid1; }
			$resAdminMenu = _MQ_assoc("select *from smart_admin_menu where am_depth = 2 and am_parent = '".$viewUid."' order by am_idx asc");
		break;

		case "3":
			if($locUid2 != ''){ $viewUid = $locUid2; }
			$resAdminMenu = _MQ_assoc("select *from smart_admin_menu where am_depth = 3 and find_in_set('".$viewUid."',am_parent) order by am_idx asc");
		break;
	}

?>
	<!-- 카테고리 목록박스 -->

	<?php if(count($resAdminMenu) > 0) { ?>
		<table class="category_list">
			<colgroup>
				<col width="50"/><col width="*"/><col width="105"/>
			</colgroup>
			<tbody>
			<?php 
				foreach($resAdminMenu as $k=>$v){ 
					$amViewClass = $v['am_view'] == 'Y' ? "blue line" : "gray"; // 노출여부에 따른 클래스명
					$amViewName = $v['am_view'] == 'Y' ? "노출" : "숨김"; // 노출여부에 따른 클래스명
					$amOnclickEvt = $v['am_depth'] != 3 ? " onclick=\"viewAdminMenuList('".$v['am_depth']."','".$v['am_uid']."');\" style='cursor:pointer;' ":"";
			?>
				<!-- 클릭하면 아래 폼 나오고 hit : tr 온클릭 작업시 a링크 삭제가능 -->
				<tr class="admin-menu-list-tr <?=in_array($v['am_uid'],array($locUid1,$locUid2)) == true ? 'hit':''?>"  data-depth= "<?=$v['am_depth']?>" data-uid="<?=$v['am_uid']?>"  >
					<td><span class="c_tag <?=$amViewClass?> h22 t2"><?=$amViewName?></span></td>
					 <td class="t_left ctg_name" <?=$amOnclickEvt?>><?=$v['am_name']?></td>
					<td>
						<a href="#none" onclick="idxAdminMenu('up','<?=$v['am_uid']?>','<?=$v['am_depth']?>'); return false;" class="c_btn h22 icon_up" title="위로"></a>
						<a href="#none" onclick="idxAdminMenu('down','<?=$v['am_uid']?>','<?=$v['am_depth']?>'); return false;" class="c_btn h22 icon_down" title="아래로"></a>
						<a href="#none" onclick="viewAdminMenuForm('modify','<?=$v['am_depth']?>','<?=$v['am_uid']?>'); return false;" class="c_btn h22 t2 scrollto" data-scrollto="view-form">수정</a>
					</td>
				</tr>
			<?php } ?>
			</tbody> 
		</table>
		<?php }else{ ?>

		<?php if($viewDepth != '1' && $viewUid == ''){ ?> 
		<div class="category_before">상위 메뉴를 먼저 선택해주세요.</div>
		<?php }else{ ?>
		<div class="category_before">등록된 메뉴가 없습니다.</div>
		<?php } ?>


		<?php } ?>
