<?php
	include_once("inc.php");

	if($_mode == 'modify'){
		// -- 메뉴 정보
		$row = _MQ("select *from smart_display_type_set where dts_uid = '".$_uid."' ");

		$printParent = '';
		if($row['dts_depth'] > 1){
			$arrParent = explode(",",$row['dts_parent']);
			$rowDepth1 = _MQ("select dts_name from smart_display_type_set where dts_depth = '1' and dts_uid = '".$arrParent[0]."'   ");
			$printParent =" <span class='fr_tx'>".$rowDepth1['dts_name']."</span> ";
		}

		if($row['dts_depth'] > 2){
			$rowDepth2 = _MQ("select dts_name from smart_display_type_set where dts_depth = '2' and dts_uid = '".$arrParent[1]."'   ");
			$printParent =" <span class='fr_tx'>".$rowDepth2['dts_name']."</span> ";
		}

		$_depth = $row['dts_depth'];

	}else{

		$printParent = '';
		if($_depth > 1){
			$rowDepth1 = _MQ("select dts_name from smart_display_type_set where dts_depth = '1' and dts_uid = '".$locUid1."'   ");
			$printParent =" <span class='fr_tx'>".$rowDepth1['dts_name']."</span> ";
		}

		if($_depth > 2){
			$rowDepth2 = _MQ("select dts_name from smart_display_type_set where dts_depth = '2' and dts_uid = '".$locUid2."'   ");
			$printParent =" <span class='fr_tx'>".$rowDepth2['dts_name']."</span> ";
		}
	}


	// -- 스킨별 기본 리스트를 따른다.
	$tempSkinInfo = SkinInfo('category');  // 기존규칙은 상품의 정렬을 따른다.
	$arrProductDisplay['arrList'] = explode(",",$tempSkinInfo['pc_list_depth']); 	// 진열리스트 :: PC
	$arrProductDisplay['listDefault'] = $tempSkinInfo['pc_list_depth_default'];
	$arrProductDisplay['arrListMo'] = explode(",",$tempSkinInfo['mo_list_depth']);	// 진열리스트 :: 모바일
	$arrProductDisplay['listDefaultMo'] = $tempSkinInfo['mo_list_depth_default'];


	// -- 타입별 :: 일반상품리스트
	$_list_product_display_value =  $row['dts_list_product_display'] > 0  ?  $row['dts_list_product_display'] : $arrProductDisplay['listDefault'];
	$_list_product_mobile_display_value = $row['dts_list_product_mobile_display'] > 0  ?  $row['dts_list_product_mobile_display'] : $arrProductDisplay['listDefaultMo'];


	// -- 타입 url
	$typeInUrl = '/?pn=product.list&_event=type&typeuid='.$row['dts_uid'];
	$typeFullUrl = $system['url'].$typeInUrl;

?>

		<form name="formDisplayType">
		<input type="hidden" name="_uid" value="<?=$_uid?>">
		<input type="hidden" name="_mode" value="<?=$_mode?>">
		<input type="hidden" name="_depth" value="<?=$_depth?>">
		<input type="hidden" name="locUid1" value="<?=$locUid1?>">
		<input type="hidden" name="locUid2" value="<?=$locUid2?>">


		<!-- ● 단락타이틀 -->
		<div class="group_title"><strong>선택 타입 <?=$_mode == 'add' ? '추가':'수정' ?></strong><!-- 메뉴얼로 링크 --><?=openMenualLink('선택타입설정')?></div>

		<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th>타입등록 안내</th>
						<td colspan="3">
							<div class="tip_box">
								<div class="c_tip">타입추가의 경우 개수 제한이 없으나 사용자 페이지 내에서는 실제 노출되는 개수 제한이 있습니다.</div>
							</div>
						</td>
					</tr>
					<tr>
						<th>분류명</th>
						<td>
							<?php if($_depth > 1) {?>
							<!-- 상위카테고리가 있을 경우 -->
							<div class="category_upper"><span class="fr_bullet">상위 분류</span><?=$printParent?></div>
							<?php } ?>
							<input type="text" name="_name" class="design bold t_black" placeholder="분류명" value="<?=$row['dts_name']?>" style="width:240px" />
							<div class="c_tip black">분류명을 입력해 주세요.</div>
						</td>
						<th>노출여부</th>
						<td>
							<label class="design"><input type="radio" value="Y" name="_view" <?=!$row['dts_view'] || $row['dts_view'] == 'Y' ? 'checked':''?>>노출</label>
							<label class="design"><input type="radio" value="N" name="_view" <?=$row['dts_view'] == 'N' ? 'checked':''?>>숨김</label>
						</td>
					</tr>
					<tr>
						<th>페이지 주소(외부)</th>
						<td>
						<?php if($row['dts_uid'] != ''){ ?>
							<a href="<?=$typeFullUrl?>" class="fr_url h22" target="_blank" title="미리보기" id="typeFullUrl" ><?=$typeFullUrl?></a>
							<a href="#none"  data-clipboard-target="#typeFullUrl" class="c_btn h22 js-clipboard" onclick="return false;">복사</a>
						<?php }else{ ?>
						<?php echo _DescStr("타입 추가 후 확인가능합니다."); ?>
						<?php }?>
						</td>
						<th>페이지 주소(내부)</th>
						<td>
						<?php if($row['dts_uid'] != ''){ ?>
							<a href="<?=$typeInUrl?>" class="fr_url h22" target="_blank" title="미리보기" id="typeInUrl"><?=$typeInUrl?></a>
							<a href="#none" data-clipboard-target="#typeInUrl" class="c_btn h22 js-clipboard" onclick="return false;">복사</a>
						<?php }else{ ?>
						<?php echo _DescStr("타입 추가 후 확인가능합니다."); ?>
						<?php } ?>
						</td>
					</tr>

					<?php if( $tempSkinInfo['pc_image_width'] !='off'){ ?>
					<tr>
						<th>(PC) 상단 배너 사용</th>
						<td colspan="3">
							<?php echo _InputRadio( "_img_top_banner_use" , array('Y' , 'N'), (!$row['dts_img_top_banner_use'] ? "N" : $row['dts_img_top_banner_use']) , "" , array('사용','사용안함') , ""); ?>
							<div class="dash_line"><!-- 점선라인 --></div>
							<?php echo _PhotoForm('../upfiles/category', '_img_top_banner', $row['dts_img_top_banner'], 'style="width:280px"'); ?>
							<?php echo _DescStr('이미지 사이즈 : '.($tempSkinInfo['pc_image_width']).' × Free (pixel)'); ?>
							<div class="dash_line"><!-- 점선라인 --></div>
							<span class="fr_tx bold">배너 링크</span>

							<?php echo _InputRadio( "_img_top_banner_target" , array('_none', '_self' , '_blank'), (!$row['dts_img_top_banner_target'] ? "_self" : $row['dts_img_top_banner_target']) , "" , array('링크없음','같은창','새창') , ""); ?>

							<input type="text" name="_img_top_banner_link" class="design" placeholder="링크 주소를 입력해주세요." value="<?=$row['dts_img_top_banner_link']?>" style="width:425px" />
						</td>
					</tr>
					<?php } ?>

					<?php if( $tempSkinInfo['mo_image_width'] !='off'){ ?>
					<tr>
						<th>(모바일) 상단 배너 사용</th>
						<td colspan="3">
							<?php echo _InputRadio( "_img_top_mobile_banner_use" , array('Y' , 'N'), (!$row['dts_img_top_mobile_banner_use'] ? "N" : $row['dts_img_top_mobile_banner_use']) , "" , array('사용','사용안함') , ""); ?>
							<div class="dash_line"><!-- 점선라인 --></div>
							<?php echo _PhotoForm('../upfiles/category', '_img_top_mobile_banner', $row['dts_img_top_mobile_banner'], 'style="width:280px"'); ?>
							<?php echo _DescStr('이미지 사이즈 : '.($tempSkinInfo['mo_image_width']).' × Free (pixel)'); ?>
							<div class="dash_line"><!-- 점선라인 --></div>
							<span class="fr_tx bold">배너 링크</span>

							<?php echo _InputRadio( "_img_top_mobile_banner_target" , array('_none', '_self' , '_blank'), (!$row['dts_img_top_mobile_banner_target'] ? "_self" : $row['dts_img_top_mobile_banner_target']) , "" , array('링크없음','같은창','새창') , ""); ?>

							<input type="text" name="_img_top_mobile_banner_link" class="design" placeholder="링크 주소를 입력해주세요." value="<?=$row['dts_img_top_mobile_banner_link']?>" style="width:425px" />
						</td>
					</tr>
					<?php } ?>

				</tbody>
			</table>
		</div>


		<!-- ● 단락타이틀 -->
		<div class="group_title"><strong>타입별 상품 진열 설정</strong><!-- 메뉴얼로 링크 --><?=openMenualLink('타입별상품진열설정')?></div>

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
							<?php echo _InputRadio( "_list_product_view" , array('Y' , 'N'), (!$row['dts_list_product_view'] ? "N" : $row['dts_list_product_view']) , "" , array('노출','숨김') , ""); ?>

						</td>
						<th>(모바일)상품 노출</th>
						<td>
							<?php echo _InputRadio( "_list_product_mobile_view" , array('Y' , 'N'), (!$row['dts_list_product_mobile_view'] ? "N" : $row['dts_list_product_mobile_view']) , "" , array('노출','숨김') , ""); ?>

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
		<div class="group_title" data-name="view-type"><strong>선택된 상품</strong><!-- 메뉴얼로 링크 --></div>
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
				<tbody class="select-type-product-list">
				</tbody>
			</table>

			<div class="common_none select-type-product-none" style="display:none;"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>


			<!-- 테이블 하단버튼 -->
			<div class="table_total_btn">
				<div class="overflow">
					<div class="fr_left">
						<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27 ">전체선택</a>
						<a href="#none" onclick="selectAll('N'); return false;"  class="c_btn h27 ">전체해제</a>
						<a href="#none" onclick="return false;" class="c_btn h27 gray select-type-product-delete">선택삭제</a>
					</div>
					<div class="fr_right"><a href="#none" onclick="selectTypeProductAddpop(); return false;" class="c_btn h27 black line b">상품선택</a></div>
				</div>
			</div>

			<div class="paginate view-paginate">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, "?{$_PVS}&listpg=", 'Y'); ?>
			</div>

			<div class="ajax-data-box" data-ahref=""></div>

		</div>


		</form>

		<div class="c_btnbox">
			<ul>
				<li><a href="#none" onclick="configDisplayType.saveItem(); return false;" class="c_btn h46 red">저장</a></li>
				<li><a href="#none" onclick="configDisplayType.deleteItem('<?=$row['dts_uid']?>');" class="c_btn h46 black line">삭제</a></li>
			</ul>
		</div>

		<div class="fixed_save js_fixed_save" style="display:none;">
			<div class="wrapping" style="margin:0;">
				<!-- 가운데정렬버튼 -->
				<div class="c_btnbox" style="margin:0 !important;">
				<ul>
					<li><a href="#none" onclick="configDisplayType.saveItem(); return false;" class="c_btn h34 red">저장</a></li>
					<li><a href="#none" onclick="configDisplayType.deleteItem('<?=$row['dts_uid']?>');" class="c_btn h34 black line">삭제</a></li>
				</ul>
				</div>
			</div>
		</div>


	<script>

		var categoryForm = {};

		// -- 초기화
		categoryForm.init = function()
		{
		// -- 배너링크에 따른 처리
			var chk = $('[name="_img_top_banner_target"]:checked').val();
			$('[name="_img_top_banner_link"]').hide();
			if( chk != '_none'){$('[name="_img_top_banner_link"]').show(); }
		}


		$(document).ready(function(){
		categoryForm.init();
		});

		// -- 배너링크에 따른 처리
		$(document).on('click','[name="_img_top_banner_target"]',categoryForm.init);


	</script>