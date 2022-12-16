<?php
	include_once("inc.php");

	if($_mode == 'modify'){
		// -- 메뉴 정보
		$row = _MQ("select *from smart_display_main_set where dms_uid = '".$_uid."' ");

		$printParent = '';
		if($row['dms_depth'] > 1){
			$arrParent = explode(",",$row['dms_parent']);
			$rowDepth1 = _MQ("select dms_name from smart_display_main_set where dms_depth = '1' and dms_uid = '".$arrParent[0]."'   ");
			$printParent =" <span class='fr_tx'>".$rowDepth1['dms_name']."</span> ";
		}

		if($row['dms_depth'] > 2){
			$rowDepth2 = _MQ("select dms_name from smart_display_main_set where dms_depth = '2' and dms_uid = '".$arrParent[1]."'   ");
			$printParent =" <span class='fr_tx'>".$rowDepth2['dms_name']."</span> ";
		}

		$_depth = $row['dms_depth'];

	}else{

		$printParent = '';
		if($_depth > 1){
			$rowDepth1 = _MQ("select dms_name from smart_display_main_set where dms_depth = '1' and dms_uid = '".$locUid1."'   ");
			$printParent =" <span class='fr_tx'>".$rowDepth1['dms_name']."</span> ";
		}

		if($_depth > 2){
			$rowDepth2 = _MQ("select dms_name from smart_display_main_set where dms_depth = '2' and dms_uid = '".$locUid2."'   ");
			$printParent =" <span class='fr_tx'>".$rowDepth2['dms_name']."</span> ";
		}
	}

	$arrProductDisplyImage = array(
		'pc'=>array(6=>'type_6x1.gif', 5=>'type_5x1.gif', 4=> 'type_4x1.gif', 3=> 'type_3x1.gif', 2=> 'type_list2x.gif', 1=> 'type_list1x.gif'),
		'mobile'=>array(3=> 'type_3x1.gif', 2=> 'type_2x1.gif', 1=> 'type_m1x1.gif')
	);

	// -- 스킨별 기본 리스트를 따른다.
	$tempSkinInfo = SkinInfo('category');  // 기존규칙은 상품의 정렬을 따른다.
	$arrProductDisplay['arrList'] = explode(",",$tempSkinInfo['pc_best_depth']); 	// 진열리스트 :: PC  5, 4, 3
	$arrProductDisplay['listDefault'] = $tempSkinInfo['pc_best_depth_default'];		// 기본
	$arrProductDisplay['arrListMo'] = explode(",",$tempSkinInfo['mo_best_depth']);	// 진열리스트 :: 모바일 MOBILE 3,2
	$arrProductDisplay['listDefaultMo'] = $tempSkinInfo['mo_best_depth_default'];	// 기본


	// -- 상품 진열 설정 판별
	$_list_product_display_value =  $row['dms_list_product_display'] > 0  ?  $row['dms_list_product_display'] : $arrProductDisplay['listDefault'];
	$_list_product_mobile_display_value = $row['dms_list_product_mobile_display'] > 0  ?  $row['dms_list_product_mobile_display'] : $arrProductDisplay['listDefaultMo'];


?>

		<form name="formDisplayMain">
		<input type="hidden" name="_uid" value="<?=$_uid?>">
		<input type="hidden" name="_mode" value="<?=$_mode?>">
		<input type="hidden" name="_depth" value="<?=$_depth?>">
		<input type="hidden" name="locUid1" value="<?=$locUid1?>">
		<input type="hidden" name="locUid2" value="<?=$locUid2?>">

		<!-- ● 단락타이틀 -->
		<div class="group_title"><strong>선택 분류 <?=$_mode == 'add' ? '추가':'수정' ?></strong><!-- 메뉴얼로 링크 --><?=openMenualLink('선택메뉴설정')?></div>

		<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th>분류명</th>
						<td>
							<?php if($_depth > 1) {?>
							<!-- 상위카테고리가 있을 경우 -->
							<div class="category_upper"><span class="fr_bullet">상위 분류</span><?=$printParent?></div>
							<?php } ?>
							<input type="text" name="_name" class="design bold t_black" placeholder="분류명" value="<?=$row['dms_name']?>" style="width:240px" />
							<div class="c_tip black">분류명을 입력해 주세요.</div>
						</td>
						<th>노출여부</th>
						<td>
							<label class="design"><input type="radio" value="Y" name="_view" <?=!$row['dms_view'] || $row['dms_view'] == 'Y' ? 'checked':''?>>노출</label>
							<label class="design"><input type="radio" value="N" name="_view" <?=$row['dms_view'] == 'N' ? 'checked':''?>>숨김</label>
						</td>
					</tr>
				</tbody>
			</table>
		</div>


		<?php if($_depth > 1) {  ?>
		<!-- ● 단락타이틀 -->
		<div class="group_title"><strong>상품 진열 설정</strong><!-- 메뉴얼로 링크 --><?=openMenualLink('상품진열설정')?></div>

		<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th>(PC)상품 노출</th>
						<td>
							<?php echo _InputRadio( "_list_product_view" , array('Y' , 'N'), (!$row['dms_list_product_view'] ? "N" : $row['dms_list_product_view']) , "" , array('노출','숨김') , ""); ?>

						</td>
						<th>(모바일)상품 노출</th>
						<td>
							<?php echo _InputRadio( "_list_product_mobile_view" , array('Y' , 'N'), (!$row['dms_list_product_mobile_view'] ? "N" : $row['dms_list_product_mobile_view']) , "" , array('노출','숨김') , ""); ?>

						</td>
					</tr>

					<tr>
						<th>(PC)상품 진열 설정</th>
						<td>


						<?php foreach($arrProductDisplay['arrList'] as $k=>$v){ if(count($arrProductDisplay['arrList']) > 3 && ($k % 2) == 0 && $k!= 0 ) echo '<div class="clear_both"></div>';?>
						<label class="type"><span class="img"><img src="images/<?php echo $arrProductDisplyImage['pc'][$v]; ?>" alt="" /></span><span class="tx"><input type="radio" name="_list_product_display" value="<?php echo $v;?>" <?php echo $_list_product_display_value == $v ? 'checked':''?> /><?php echo $v;?> x *</span></label>
						<?php } ?>

						</td>
						<th>(모바일)상품 진열 설정</th>
						<td>
							<?php foreach($arrProductDisplay['arrListMo'] as $k=>$v){ if(count($arrProductDisplay['arrListMo']) > 3 && ($k % 2) == 0 && $k!= 0 ) echo '<div class="clear_both"></div>';?>
							<label class="type"><span class="img"><img src="images/<?php echo $arrProductDisplyImage['mobile'][$v]; ?>" alt="" /></span><span class="tx"><input type="radio" name="_list_product_mobile_display" value="<?php echo $v;?>" <?php echo $_list_product_mobile_display_value == $v ? 'checked':''?> /><?php echo $v;?> x *</span></label>
							<?php } ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>


		<!-- ● 단락타이틀 -->
		<div class="group_title" data-name="view-main"><strong>선택된 상품</strong><!-- 메뉴얼로 링크 --></div>
		<!-- ● 데이터 리스트 -->
		<div class="data_list">
			<table class="table_list">
				<colgroup>
					<col width="40"/><col width="70"/><col width="120"/><col width="52"/><col width="*"/><col width="110"/><col width="90"/><col width="110"/><col width="120"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y" ></label></th>
						<th scope="col">NO</th>
						<th scope="col">순서</th><!-- KAY :: 2021-11-12 :: 순서추가-->
						<th scope="col">이미지</th>
						<th scope="col">상품명</th>
						<th scope="col">상품가격</th>
						<th scope="col">품절상태</th>
						<th scope="col">공급업체</th>
						<th scope="col">노출여부</th>
					</tr>
				</thead>
				<tbody class="select-main-product-list">
				</tbody>
			</table>

			<div class="common_none select-main-product-none" style="display:none;"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>


			<!-- 테이블 하단버튼 -->
			<div class="table_total_btn">
				<div class="overflow">
					<div class="fr_left">
						<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 ">전체선택</a>
						<a href="#none" onclick="selectAll('N'); return false;"  class="c_btn h27 ">전체해제</a>
						<a href="#none" onclick="return false;" class="c_btn h27 gray select-main-product-delete">선택삭제</a>
					</div>
					<div class="fr_right"><a href="#none" onclick="selectMainProductAddpop(); return false;" class="c_btn h27 black line b">상품선택</a></div>
				</div>
			</div>

			<div class="paginate view-paginate">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, "?{$_PVS}&listpg=", 'Y'); ?>
			</div>

			<div class="ajax-data-box" data-ahref=""></div>

		</div>



		<?php } ?>

		</form>

		<div class="c_btnbox">
			<ul>
				<li><a href="#none" onclick="configDisplayMain.saveItem(); return false;" class="c_btn h46 red" accesskey="s">저장</a></li>
				<?php if($_mode != 'add' && $_depth != 1 && ( $row['dms_parent'] != '1' || $varMdAuth === true) ) { ?>
				<li><a href="#none" onclick="configDisplayMain.deleteItem('<?=$row['dms_uid']?>');" class="c_btn h46 black line">삭제</a></li>
				<?php } ?>
			</ul>
		</div>


		<div class="fixed_save js_fixed_save" style="display:none;">
			<div class="wrapping" style="margin:0;">
				<!-- 가운데정렬버튼 -->
				<div class="c_btnbox" style="margin:0 !important;">
				<ul>
					<li><a href="#none" onclick="configDisplayMain.saveItem(); return false;" class="c_btn h34 red" accesskey="s">저장</a></li>
					<?php if($_mode != 'add' && $_depth != 1 && ( $row['dms_parent'] != '1' || $varMdAuth === true) ) { ?>
					<li><a href="#none" onclick="configDisplayMain.deleteItem('<?=$row['dms_uid']?>');" class="c_btn h34 black line">삭제</a></li>
					<?php } ?>
				</ul>
				</div>
			</div>
		</div>