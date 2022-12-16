<?php
	include_once("inc.php");

	if($_mode == 'modify'){
		// -- 메뉴 정보
		$rowFavMenu = _MQ(" select *from smart_favmenu where fm_uid = '".$_uid."' ");

		$printParent = '';
		if($rowFavMenu['fm_depth'] > 1){
			$arrParent = explode(",",$rowFavMenu['fm_parent']);
			$rowFavMenuDepth1 = _MQ(" select fm_menuName from smart_favmenu where fm_depth = '1' and fm_uid = '".$arrParent[0]."'   ");
			$printParent =" <span class='fr_tx'>".$rowFavMenuDepth1['fm_menuName']."</span> ";
		}

		if($rowFavMenu['fm_depth'] > 2){
			$rowFavMenuDepth2 = _MQ(" select fm_menuName from smart_favmenu where fm_depth = '2' and fm_uid = '".$arrParent[1]."'   ");
			$printParent =" <span class='fr_tx'>".$rowFavMenuDepth2['fm_menuName']."</span> ";
		}

		$_depth = $rowFavMenu['fm_depth'];

	}else{

		$printParent = '';
		if($_depth > 1){
			$rowFavMenuDepth1 = _MQ(" select fm_menuName from smart_favmenu where fm_depth = '1' and fm_uid = '".$locUid1."'   ");
			$printParent =" <span class='fr_tx'>".$rowFavMenuDepth1['fm_menuName']."</span> ";
		}

		if($_depth > 2){
			$rowFavMenuDepth2 = _MQ(" select fm_menuName from smart_favmenu where fm_depth = '2' and fm_uid = '".$locUid2."'   ");
			$printParent =" <span class='fr_tx'>".$rowFavMenuDepth2['fm_menuName']."</span> ";
		}
	}

?>

		<!-- ● 단락타이틀 -->
		<div class="group_title"><strong>선택 메뉴 <?=$_mode == 'add' ? '추가':'수정' ?></strong></div>

		<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form">
		<form name="formFavMenu" onsubmit="saveFavMenu(); return false;">
		<input type="hidden" name="_uid" value="<?=$_uid?>">
		<input type="hidden" name="_mode" value="<?=$_mode?>">
		<input type="hidden" name="_depth" value="<?=$_depth?>">
		<input type="hidden" name="locUid1" value="<?=$locUid1?>">
		<input type="hidden" name="locUid2" value="<?=$locUid2?>">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th>메뉴명</th>
						<td>
							<?php if($_depth > 1) {?>
							<!-- 상위카테고리가 있을 경우 -->
							<div class="category_upper"><span class="fr_bullet">메뉴분류</span><?=$printParent?></div>
							<?php } ?>
							<input type="text" name="_name" class="design bold t_black" placeholder="메뉴명" value="<?=$rowFavMenu['fm_menuName']?>" style="width:240px" />
							<!-- <div class="c_tip black">메뉴명을 입력해 주세요.</div> -->
						</td>
						<th>노출여부</th>
						<td>
							<label class="design"><input type="radio" value="Y" name="_view" <?=!$rowFavMenu['fm_view'] || $rowFavMenu['fm_view'] == 'Y' ? 'checked':''?>>노출</label>
							<label class="design"><input type="radio" value="N" name="_view" <?=$rowFavMenu['fm_view'] == 'N' ? 'checked':''?>>숨김</label>
						</td>
					</tr>
					<?php if($_depth > 1) {?>
					<tr>
						<th>메뉴선택</th>
						<td colspan="3">
							<?php include dirname(__file__).'/_config.favmenu.admin_menu.php'; ?>
						</td>
					</tr>
					<?php } ?>

				</tbody>
			</table>
		</form>
		</div>

		<div class="c_btnbox">
			<ul>
				<li><a href="#none" onclick="saveFavMenu(); return false;" class="c_btn h46 red">저장</a></li>
				<?php if($_mode != 'add') { ?>
				<li><a href="#none" onclick="deleteFavMenu('<?=$rowFavMenu['fm_uid']?>');" class="c_btn h46 black line">삭제</a></li>
				<?php } ?>
			</ul>
		</div>