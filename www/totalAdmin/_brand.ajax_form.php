<?php 
	include_once("inc.php");

	if($_mode == 'modify'){
		// -- 브랜드 정보 
		$rowAdminMenu = _MQ("select *from smart_brand where c_uid = '".$_uid."' ");
		
		$printParent = '';
		if($rowAdminMenu['c_depth'] > 1){
			$arrParent = explode(",",$rowAdminMenu['c_parent']);
			$rowAdminMenuDepth1 = _MQ("select c_name from smart_brand where c_depth = '1' and c_uid = '".$arrParent[0]."'   ");
			$printParent =" <span class='fr_tx'>".$rowAdminMenuDepth1['c_name']."</span> ";
		}

		if($rowAdminMenu['c_depth'] > 2){
			$rowAdminMenuDepth2 = _MQ("select c_name from smart_brand where c_depth = '2' and c_uid = '".$arrParent[1]."'   ");
			$printParent =" <span class='fr_tx'>".$rowAdminMenuDepth2['c_name']."</span> ";	
		}

		$_depth = $rowAdminMenu['c_depth'];

	}else{

		$printParent = '';
		if($_depth > 1){
			$rowAdminMenuDepth1 = _MQ("select c_name from smart_brand where c_depth = '1' and c_uid = '".$locUid1."'   ");
			$printParent =" <span class='fr_tx'>".$rowAdminMenuDepth1['c_name']."</span> ";
		}

		if($_depth > 2){
			$rowAdminMenuDepth2 = _MQ("select c_name from smart_brand where c_depth = '2' and c_uid = '".$locUid2."'   ");
			$printParent =" <span class='fr_tx'>".$rowAdminMenuDepth2['c_name']."</span> ";	
		}		
	}

?>	

		<!-- ● 단락타이틀 -->
		<div class="group_title"><strong>선택 브랜드 <?=$_mode == 'add' ? '추가':'수정' ?></strong><!-- 메뉴얼로 링크 --><?=openMenualLink('선택브랜드설정')?></div>
		
		<!-- ●폼 영역 (검색/폼 공통으로 사용) -->

		<div class="data_form">
		<form name="formAdminMenu" onsubmit="return false;">
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
						<th>브랜드명</th>
						<td>
							<?php if($_depth > 1) {?> 
							<!-- 상위카테고리가 있을 경우 -->
							<div class="category_upper"><span class="fr_bullet">상위 브랜드</span><?=$printParent?></div>
							<?php } ?>
							<input type="text" name="_name" class="design bold t_black" placeholder="브랜드명" value="<?=$rowAdminMenu['c_name']?>" style="width:240px" />
							<div class="c_tip black">브랜드명을 입력해 주세요.</div>
						</td>
						<th>노출여부</th>
						<td>
							<label class="design"><input type="radio" value="Y" name="_view" <?=!$rowAdminMenu['c_view'] || $rowAdminMenu['c_view'] == 'Y' ? 'checked':''?>>노출</label>
							<label class="design"><input type="radio" value="N" name="_view" <?=$rowAdminMenu['c_view'] == 'N' ? 'checked':''?>>숨김</label>
						</td>
					</tr>

				</tbody> 
			</table>
		</form>
		</div>

		<div class="c_btnbox">
			<ul>
				<li><a href="#none" onclick="saveAdminMenu(); return false;" class="c_btn h46 red">저장</a></li>
				<?php if($_mode != 'add') { ?>
				<li><a href="#none" onclick="deleteAdminMenu('<?=$rowAdminMenu['c_uid']?>');" class="c_btn h46 black line">삭제</a></li>
				<?php } ?>
			</ul>
		</div>