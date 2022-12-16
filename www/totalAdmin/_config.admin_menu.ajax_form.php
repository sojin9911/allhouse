<?php 
	include_once("inc.php");

	if($_mode == 'modify'){
		// -- 메뉴 정보 
		$rowAdminMenu = _MQ("select *from smart_admin_menu where am_uid = '".$_uid."' ");
		
		$printParent = '';
		if($rowAdminMenu['am_depth'] > 1){
			$arrParent = explode(",",$rowAdminMenu['am_parent']);
			$rowAdminMenuDepth1 = _MQ("select am_name from smart_admin_menu where am_depth = '1' and am_uid = '".$arrParent[0]."'   ");
			$printParent =" <span class='fr_tx'>".$rowAdminMenuDepth1['am_name']."</span> ";
		}

		if($rowAdminMenu['am_depth'] > 2){
			$rowAdminMenuDepth2 = _MQ("select am_name from smart_admin_menu where am_depth = '2' and am_uid = '".$arrParent[1]."'   ");
			$printParent =" <span class='fr_tx'>".$rowAdminMenuDepth2['am_name']."</span> ";	
		}

		$_depth = $rowAdminMenu['am_depth'];

	}else{

		$printParent = '';
		if($_depth > 1){
			$rowAdminMenuDepth1 = _MQ("select am_name from smart_admin_menu where am_depth = '1' and am_uid = '".$locUid1."'   ");
			$printParent =" <span class='fr_tx'>".$rowAdminMenuDepth1['am_name']."</span> ";
		}

		if($_depth > 2){
			$rowAdminMenuDepth2 = _MQ("select am_name from smart_admin_menu where am_depth = '2' and am_uid = '".$locUid2."'   ");
			$printParent =" <span class='fr_tx'>".$rowAdminMenuDepth2['am_name']."</span> ";	
		}		
	}

?>	

		<!-- ● 단락타이틀 -->
		<div class="group_title"><strong>선택 메뉴 <?=$_mode == 'add' ? '추가':'수정' ?></strong><!-- 메뉴얼로 링크 --><?=openMenualLink('선택메뉴설정')?></div>
		
		<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form">
		<form name="formAdminMenu">
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
							<div class="category_upper"><span class="fr_bullet">상위 메뉴</span><?=$printParent?></div>
							<?php } ?>
							<input type="text" name="_name" class="design bold t_black" placeholder="메뉴명" value="<?=$rowAdminMenu['am_name']?>" style="width:240px" />
							<div class="c_tip black">메뉴명을 입력해 주세요.</div>
						</td>
						<th>노출여부</th>
						<td>
							<label class="design"><input type="radio" value="Y" name="_view" <?=!$rowAdminMenu['am_view'] || $rowAdminMenu['am_view'] == 'Y' ? 'checked':''?>>노출</label>
							<label class="design"><input type="radio" value="N" name="_view" <?=$rowAdminMenu['am_view'] == 'N' ? 'checked':''?>>숨김</label>
						</td>
					</tr>
					<?php if($_depth > 2) {?> 
					<tr>
						<th>파일명</th>
						<td colspan="3">
							<input type="text" name="_link" class="design bold t_black" placeholder="파일명" value="<?=$rowAdminMenu['am_link']?>" style="width:340px" />

							<div class="tip_box">
								<div class="c_tip black">파일명을 입력해 주세요.</div>
								<div class="c_tip">파일명의 경우 FTP 상 /totalAdmin/ 경로에 있는 실제 파일을 입력하셔야합니다.</div>
								<div class="c_tip">ex) _config.admin_menu.list.php</div>
							</div>
						</td>
					</tr>
					<?php } ?>

				</tbody> 
			</table>
		</form>
		</div>

		<div class="c_btnbox">
			<ul>
				<li><a href="#none" onclick="saveAdminMenu(); return false;" class="c_btn h46 red">저장</a></li>
				<?php if($_mode != 'add') { ?>
				<li><a href="#none" onclick="deleteAdminMenu('<?=$rowAdminMenu['am_uid']?>');" class="c_btn h46 black line">삭제</a></li>
				<?php } ?>
			</ul>
		</div>