<?php
	include_once("inc.php");
	/*
		$viewDepth = 노출될 페이지의 depth
		$viewUid = 보여질 페이지

	*/

	// reload 일경우
	if( $_mode == 'reload'){
		$rowAdminMenu = _MQ("select *from smart_brand where c_uid = '".$_uid."'  ");
		$viewDepth = $rowAdminMenu['c_depth'];
	}

	switch($viewDepth)
	{
		case "1":
			$resAdminMenu = _MQ_assoc("select *from smart_brand where c_depth = 1 order by c_name");
		break;

		case "2":
			if($locUid1 != ''){ $viewUid = $locUid1; }
			$resAdminMenu = _MQ_assoc("select *from smart_brand where c_depth = 2 and c_parent = '".$viewUid."' order by c_name");
		break;

		case "3":
			if($locUid2 != ''){ $viewUid = $locUid2; }
			$resAdminMenu = _MQ_assoc("select *from smart_brand where c_depth = 3 and find_in_set('".$viewUid."',c_parent) order by c_name");
		break;
	}

?>
	<!-- 카테고리 목록박스 -->



<form name="brandlist" method="post" action="_brand.ajax_pro.php">
<input type='hidden' name="_mode" value="mass_modify">


		<table class="category_list">
			<!-- <colgroup>
				<col width="50"/><col width="*"/><col width="130"/><col width="60"/>
			</colgroup> -->
			<tbody>
				<!-- 추가하면 나오는 폼 -->
				<tr>
					<td colspan="2" class="this_form">

						<table class="brand_list">
							<colgroup>
								<col width="50"/><col width="*"/><col width="130"/><col width="80"/>
							</colgroup>
							<tbody>

								<tr>
									<td colspan="4">
										<div class='lineup-center'>
											<a href="#none" onclick="changeView('show'); return false;" class="c_btn h23 line" >전체노출</a>
											<a href="#none" onclick="changeView('hide'); return false;" class="c_btn h23 line" >전체숨김</a>
											<span class="c_btn h23 black"><input type="submit" name="" value="일괄수정" /></span>
										</div>
									</td>
								</tr>

								<tr>
									<td>추가</td>
									<td><input type="text" name="ADD_name" value="" class="design" placeholder="브랜드 이름을 입력해주세요."  style="width:200px"/></td>
									<td>
										<div class='lineup-center'>
											<label for='ADD_view_Y' class='design'><input type='radio' id='ADD_view_Y' name='ADD_view' value='Y' class='_view' checked> 노출</label>
											<label for='ADD_view_N' class='design'><input type='radio' id='ADD_view_N' name='ADD_view' value='N' class='_view' > 숨김</label>
										</div>
									</td>
									<td>
										<div class='lineup-center'>
											<a href="#none" onclick="saveAdminMenu(0); return false;" class="c_btn h23 black" >추가</a>
										</div>
									</td>
								</tr>

							</tbody>
						</table>

					</td>
				</tr>
				<!-- 이미 등록한 브랜드목록 -->

				<?php if(count($resAdminMenu) > 0) { ?>


				<tr>

					<?php

						foreach($resAdminMenu as $k=>$v){

							$amViewClass = $v['c_view'] == 'Y' ? "blue line" : "gray"; // 노출여부에 따른 클래스명
							$amViewName = $v['c_view'] == 'Y' ? "노출" : "숨김"; // 노출여부에 따른 클래스명

							// 줄바꿈
							echo ( $k%2 == 0 && $k<> 0 ? "</tr><tr>" : "" );

					?>
					<td>
						<table class="brand_list">
							<colgroup>
								<col width="50"/><col width="*"/><col width="130"/><col width="70"/>
							</colgroup>
							<tbody>
								<tr>
									<td><span class="c_tag <?=$amViewClass?> h22 t2"><?=$amViewName?></span></td>
									<td><input type="text" name="_name[<?=$v['c_uid']?>]" value="<?=$v['c_name']?>" class="design" placeholder="브랜드 이름을 입력해주세요."  style="width:200px"/></td>
									<td>
										<div class='lineup-center'>
											<label for='_view_<?=$v['c_uid']?>_Y' class='design'><input type='radio' id='_view_<?=$v['c_uid']?>_Y' name='_view[<?=$v['c_uid']?>]' value='Y' class='_view' <?=($v['c_view'] == "Y" ? "checked" : "")?>> 노출</label>
											<label for='_view_<?=$v['c_uid']?>_N' class='design'><input type='radio' id='_view_<?=$v['c_uid']?>_N' name='_view[<?=$v['c_uid']?>]' value='N' class='_view' <?=($v['c_view'] == "N" ? "checked" : "")?>> 숨김</label>
										</div>
									</td>
									<td>
										<div class='lineup-center'>
											<!-- <a href="#none" onclick="saveAdminMenu('<?=$v['c_uid']?>'); return false;" class="c_btn h23 line" >수정</a> -->
											<a href="#none" onclick="deleteAdminMenu('<?=$v['c_uid']?>'); return false;" class="c_btn h23 gray scrollto" >삭제</a>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
					<?php
						}
						$k++;
						if($k%2 <> 0 ) {
							echo '<td></td>';
						}
					?>

				</tr>


				<?php }else{ ?>

				<tr>
					<td colspan="2" class="this_form">

						<div class="category_before">등록된 브랜드가 없습니다.</div>

					</td>
				</tr>


				<?php } ?>


			</tbody>
		</table>

		<div class="tip_box">
			<?php echo _DescStr('브랜드는 사용자페이지에서 <strong>가나다순</strong> 및 <strong>ABC순</strong>으로 자동정렬됩니다.'); ?>
		</div>

</form>