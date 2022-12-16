<?PHP

	include_once("wrap.header.php");

	// 넘길 변수 설정하기
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {
		if(is_array($val)) {
			foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }
		}
		else {
			$_PVS .= "&$key=$val";
		}
	}
	$_PVSC = enc('e' , $_PVS);
	// 넘길 변수 설정하기

	// - 입점업체 ---
	$arr_customer = arr_company();
	$arr_customer2 = arr_company2();
?>

	<!-- ● 단락타이틀 -->
	<div class="group_title">
		<strong>상품검색</strong>
	</div>


	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<?php


		// 상품 관리 --- 검색폼 불러오기
		//			반드시 - s_query가 적용되어야 함.
		$s_query = " from smart_product as p where 1 ";

		include_once("_product.inc_search.php");
		//	==> s_query 리턴됨.

		if(!$listmaxcount) $listmaxcount = 50;
		if(!$listpg) $listpg = 1;
		if(!$st) $st = 'p_idx';
		if(!$so) $so = 'asc';
		$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스


		$res = _MQ(" select count(*) as cnt  $s_query ");
		$TotalCount = $res['cnt'];
		$Page = ceil($TotalCount / $listmaxcount);

		$res = _MQ_assoc(" select p.* $s_query order by {$st} {$so} limit $count , $listmaxcount ");


	?>


	<!-- ● 데이터 리스트 -->
	<div class="data_list">


<form name=frm method=post action='_product_mass.pro.php' >
<input type=hidden name='_mode' value=''>
<input type=hidden name='_submode' value='mass_price'>
<input type=hidden name='_select_category_cnt' value='0'>
<input type=hidden name="_PVSC" value=<?=$_PVSC?>>

				<!-- ●리스트 컨트롤영역 -->
				<div class="list_ctrl">
					<div class="left_box">
						<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
						<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
					</div>
					<div class="right_box">
						<select class="h27" onchange="location.href=this.value;">
							<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
							<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
							<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
						</select>
					</div>
				</div>
				<!-- / 리스트 컨트롤영역 -->


				<table class="table_list">
					<colgroup>
						<col width="45px"/><col width="50px"/><col width="50px"/>
						<?if($SubAdminMode === true) { echo '<col width="120px"/>'; }// 입점업체 검색기능 2016-05-26 LDD?>
						<col width="60px"/><col width="*"/>
						<col width="220px"/><col width="116px"/><col width="90px"/><col width="116x"/><col width="116px"/><col width="160"/>
					</colgroup>
					<thead>
						<tr>
							<th scope="col" ><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
							<th scope="col" >NO</th>
							<th scope="col" >노출</th>
							<?if($SubAdminMode === true) { echo '<th scope="col" >입점업체</th>'; }// 입점업체 검색기능 2016-05-26 LDD?>
							<th scope="col" >이미지</th>
							<th scope="col" >상품코드/상품명</th>
							<th scope="col" >정산방식</th>
							<th scope="col" >공급가</th>
							<th scope="col" >수수료</th>
							<th scope="col" >기존가</th>
							<th scope="col" >판매가</th>
							<th scope="col" >관리</th>
						</tr>
					</thead>
					<tbody>

							<?// 일괄지정 열 ?>
							<tr>
								<?// 정산방식,공급가,수수로,기존가,판매가 팝업 추가 ?>
								<?php
									$app_str = _DescStr("상품을 부분 또는 전체 선택 하신 후 <strong>버튼 클릭</strong>하시면 변경이 가능합니다." , "orange");
									$app_str .= _DescStr("버튼 클릭 후 <strong>팝업창의 일괄변경을 클릭하시면 바로 저장</strong>됩니다." , "orange");
									$app_str .= _DescStr("<strong>팝업창의 일괄 비우기 클릭 시 바로 적용되니 주의하시기 바랍니다.</strong>" , "orange");
									echo ($SubAdminMode === true ? '<td colspan="6" ><div class="tip_box">'. $app_str .'</div></td>' : '<td colspan="5"><div class="tip_box">'. $app_str .'</div></td>');// 입점업체 검색기능 2016-05-26 LDD
								?>
						
								<td><span class="option_btn"><a href="#none" onclick="return false;" class="c_btn h22 gray mass_commission_type">정산방식 변경</span></a></td>
								<td><span class="option_btn"><a href="#none" onclick="return false;" class="c_btn h22 gray mass_change_sPrice">공급가 변경</span></a></td>
								<td><span class="option_btn"><a href="#none" onclick="return false;" class="c_btn h22 gray mass_change_sPersent">수수료 변경</span></a></td>
								<td><span class="option_btn"><a href="#none" onclick="return false;" class="c_btn h22 gray mass_change_screenPrice">기존가 변경</span></a></td>
								<td><span class="option_btn"><a href="#none" onclick="return false;" class="c_btn h22 gray mass_change_price">판매가 변경</span></a></td>
								<td></td>
							</tr>
							<?// 일괄지정 열 ?>



<?PHP

	//if(sizeof($res) < 1) echo "<tr><td colspan=12 height='40'>내용이 없습니다.</td></tr>";

	foreach($res as $k=>$v){

		$_num = $TotalCount - $count - $k ;

		$_p_img = get_img_src('thumbs_s_'.$v[p_img_list_square]);
		if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

		// KAY :: 2021-04-15 :: 일괄 가격 개별수정 버튼 변경 추가
		$_mod = "
			<a href='#none' onclick='return false;' class='c_btn h22 blue product_price_change' data-pcode='".$v['p_code']."'>변경</a> 
			<a href='_product.form.php?_mode=modify&_code=" . $v[p_code] . "' class='c_btn h22 ' target='_blank'>수정</a>
			";
		$preview = "<a href='#none' onclick=\"window.open('/?pn=product.view&pcode=".$v[p_code]."')\" class='c_btn h22 '>미리보기</a>";

		// 입점업체 검색기능 2016-05-26 LDD
		$app_subadmin_string = ($SubAdminMode === true ? "<td >" . $arr_customer2[$v['p_cpid']] ."</td>" : "");

		echo "
							<tr>
								<td>
									<label class='design'><input type='checkbox' name='chk_pcode[".$v['p_code']."]' class='js_ck' value='Y' data-pcode='".$v['p_code']."'></label>
								</td>
								<td>" . $_num . "</td>
								<td>" . $arr_adm_button[($v['p_view'] == 'Y' ? '노출' : '숨김')] . "</td>
								". $app_subadmin_string ."
								<td>".( $v['p_img_list_square'] ? "<img src='".$_p_img."' style='width:50px !important;'>" : "&nbsp;" )."</td>
								<td style='text-align:left; '>
									<div style='both:clear; margin:5px;'>[ " . $v['p_code'] ." ]</div>
									<div style='both:clear; margin:5px; font-weight:bold;'>". strip_tags($v['p_name']) . "</div>
								</td>
								<td >
									<div class='lineup-center'>
									<label class='design'><input type='radio' name='_commission_type[".$v['p_code']."]' value='공급가' id='_commission_type_".$v['p_code']."_공급가' class='_commission_type' ". ($v['p_commission_type'] == "공급가" ? "checked" : "") .">공급가</label>
									<label class='design'><input type='radio' name='_commission_type[".$v['p_code']."]' value='수수료' id='_commission_type_".$v['p_code']."_수수료' class='_commission_type' ". ($v['p_commission_type'] == "수수료" ? "checked" : "") .">수수료</label>
									</div>
								</td>
								<td class='t_right'><input type='text' name='_sPrice[".$v['p_code']."]' class='design _sPrice' value='". $v['p_sPrice'] ."' style='width:70px' ><span class='fr_tx term'>원</span></td>
								<td class='t_right'><input type='text' name='_sPersent[".$v['p_code']."]' class='design _sPersent' value='". $v['p_sPersent'] ."' style='width:45px' ><span class='fr_tx term'>%</span></td>
								<td class='t_right'>
									<input type='text' name='_screenPrice[".$v['p_code']."]' class='design _screenPrice' value='". $v['p_screenPrice'] ."' style='width:70px' >
									<span class='fr_tx term'>원</span>	
								</td>
								<td class='t_right'><input type='text' name='_price[".$v['p_code']."]' class='design _price' value='". $v['p_price'] ."' style='width:70px' ><span class='fr_tx term'>원</span></td>

								<td>
									<div class='lineup-vertical'>
										". $_mod ."
										". $preview ."
									</div>
									". (
										in_array($v['p_option_type_chk'] , array('1depth','2depth','3depth')) ?
											"
												<div class='lineup-vertical'>
													<a href='#none' onclick=\"option_popup('". $v['p_code'] ."' , '". $v['p_option_type_chk'] ."')\" class='c_btn h22 gray' >옵션</a>
													<a href='#none' onclick=\"addoption_popup('". $v['p_code'] ."')\" class='c_btn h22 gray' >추가옵션</a>
												</div>
											" :
											""
									) ."
								</td>
							</tr>
		";
	}
?>
						</tbody>
					</table>
					
					<?php if(sizeof($res) < 1){ ?>
						<!-- 내용없을경우 -->
						<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
					<?php } ?>

					<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
					<div class="paginate">
						<?php echo pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>
					</div>

					<?// KAY :: 2021-04-15 ::  정산방식 팝업 ?>
					<div class="popup _commission_type_pop" id="" style="display:none;width:640px;background:#fff;">
						<!--  레이어팝업 공통타이틀 영역 -->
						<div class="pop_title">정산방식 변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
						<!-- 하얀색박스공간 -->
						<div class="data_form">
							<form name="change_sPrice">
								<table class="table_form">
									<colgroup>
										<col width="130"><col width="*">
									</colgroup>
									<tbody>
										<tr>
											<th><span class="tit">정산방식</span></th>
											<td>
												<label class='design'><input type='radio' id='_commission_type_all_공급가' name='_commission_type[all]' value='공급가' >공급가</label>
												<label class='design'><input type='radio' id='_commission_type_all_수수료' name='_commission_type[all]' value='수수료' >수수료</label>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="tip_box">
									<?php echo _DescStr ('정산방식을 선택하신 후 일괄변경을 클릭하시면 변경됩니다.'); ?>
								</div>
								<!-- 레이어팝업 버튼공간 -->
								<div class="c_btnbox">
									<ul>
										<li><a href="#none" onclick="return false;" class="c_btn h34 black line close selectMass_commission_type "> 일괄변경</a></li>
										<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
									</ul>
								</div>
							</form>
						</div>
					</div>

					<?// KAY :: 2021-04-15 ::  공급가 팝업 ?>
					<div class="popup _sPrice_pop" id="" style="display:none;width:640px;background:#fff;">
						<!--  레이어팝업 공통타이틀 영역 -->
						<div class="pop_title">공급가 변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
						<!-- 하얀색박스공간 -->
						<div class="data_form">
							<form name="change_sPrice">
								<table class="table_form">
									<colgroup>
										<col width="130"><col width="*">
									</colgroup>
									<tbody>
										<tr>
											<th><span class="tit">공급가</span></th>
											<td>
												<input type="text" name="common_price" style="width:150px;" class="design _sPrice_cal" placeholder="공급가">
												<?php echo _InputSelect( 'common_type' , array('price','per'),'price',' data-class="_sPrice_pop" ', array('원','%')); ?>
												<?php echo _InputSelect( 'common_ud' , array('no','up','down'),'down',' data-class="_sPrice_pop" ' , array('금액반영','인상','인하') ); ?>
											</td>
										</tr>

										<tr class="common_perdel">
											<th ><span class="tit ">절사 단위</span></th>
											<td >
												<?php echo _InputRadio( 'common_perdel' , array('per_no','per_te','per_h','per_th'),'per_h','data-class="_sPrice_pop"' , array('선택안함','10원','100원','1,000원') ); ?>
												<div class="tip_box">
													<?php echo _DescStr ('값을 입력한 후 단위를 선택해야 합니다.'); ?>
													<?php echo _DescStr ('절사안함을 선택시 1원단위로 절사됩니다.'); ?>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="tip_box">
									<?php echo _DescStr ('금액반영을 선택하시면 입력한 금액이 지정됩니다.'); ?>
									<?php echo _DescStr ('인상을 선택하시면 입력한 값과 원래 지정된 값을 계산하여 값을 올립니다.'); ?>
									<?php echo _DescStr ('인하를 선택하시면 입력한 값과 원래 지정된 값을 계산하여 값을 내립니다.'); ?>
									<?php echo _DescStr ('인상/인하/금액반영을 선택하지 않을 시 일괄지정이 되지않습니다.'); ?>
									<?php echo _DescStr ('율(%) 선택시 절사기능 설정 후 인상/인하가 적용됩니다.'); ?>
								</div>
								<!-- 레이어팝업 버튼공간 -->
								<div class="c_btnbox">
									<ul>
										<li><a href="#none" onclick="return false;" class="c_btn h34 black line close selectMass_sPrice "> 일괄변경</a></li>
										<li><a href="#none" onclick="selectMassClear('_sPrice')" class="c_btn h34 black line close"> 일괄비우기</a></li>
										<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
									</ul>
								</div>
							</form>
						</div>
					</div>

					<?// KAY :: 2021-04-15 ::  수수료 팝업 ?>
					<div class="popup _sPersent_pop" id="" style="display:none;width:640px;background:#fff;">
						<!--  레이어팝업 공통타이틀 영역 -->
						<div class="pop_title">수수료 변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
						<!-- 하얀색박스공간 -->
						<div class="data_form">
							<form name="change_sPersent">
								<table class="table_form">
									<colgroup>
										<col width="130"><col width="*">
									</colgroup>
									<tbody>
										<tr>
											<th ><span class="tit">수수료</span></th>
											<td>
												<input type="text" name="_sPersent[all]" style="width:150px;" class="design" placeholder="수수료">
												<span class='fr_tx term'>%</span>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="tip_box">
									<?php echo _DescStr ('수수료를 입력하신 후 일괄변경을 클릭하시면 변경됩니다.'); ?>
								</div>
								<div class="c_btnbox">
									<ul>
										<li><a href="#none" onclick="return false;" class="c_btn h34 black line close selectMass_sPersent"> 일괄변경</a></li>
										<li><a href="#none" onclick="selectMassClear('_sPersent')" class="c_btn h34 black line close"> 일괄비우기</a></li>
										<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
									</ul>
								</div>
							</form>
						</div>
					</div>

					<?// KAY :: 2021-04-15 ::  기존가 팝업 ?>
					<div class="popup _screenPrice_pop" id="" style="display:none;width:640px;background:#fff;">
						<!--  레이어팝업 공통타이틀 영역 -->
						<div class="pop_title">기존가 변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
						<!-- 하얀색박스공간 -->
						<div class="data_form">
							<form name="change_screenPrice">
								<table class="table_form">
									<colgroup>
										<col width="130"><col width="*">
									</colgroup>
									<tbody>
										<tr>
											<th><span class="tit">기존가</span></th>
											<td>
												<input type="text" name="common_price" style="width:150px;" class="design _screenPrice_cal" placeholder="기존가">
												<?php echo _InputSelect( 'common_type' , array('price','per'),'price',' data-class="_screenPrice_pop" ', array('원','%')); ?>
												<?php echo _InputSelect( 'common_ud' , array('no','up','down'),'down',' data-class="_screenPrice_pop" ' , array('금액반영','인상','인하') ); ?>
											</td>
										</tr>

										<tr class="common_perdel">
											<th ><span class="tit ">절사 단위</span></th>
											<td >
												<?php echo _InputRadio( 'common_perdel' , array('per_no','per_te','per_h','per_th'),'per_h','data-class=_screenPrice_pop' , array('선택안함','10원','100원','1,000원') ); ?>
												<div class="tip_box">
													<?php echo _DescStr ('값을 입력한 후 단위를 선택해야 합니다.'); ?>
													<?php echo _DescStr ('절사안함을 선택시 1원단위로 절사됩니다.'); ?>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="tip_box">
									<?php echo _DescStr ('금액반영을 선택하시면 입력한 금액이 지정됩니다.'); ?>
									<?php echo _DescStr ('인상을 선택하시면 입력한 값과 원래 지정된 값을 계산하여 값을 올립니다.'); ?>
									<?php echo _DescStr ('인하를 선택하시면 입력한 값과 원래 지정된 값을 계산하여 값을 내립니다.'); ?>
									<?php echo _DescStr ('인상/인하를 선택하지 않을 시 입력한 값으로 변경되어 일괄지정됩니다.'); ?>
									<?php echo _DescStr ('율(%) 선택시 절사기능 설정 후 인상/인하가 적용됩니다.'); ?>
								</div>
								<div class="c_btnbox">
									<ul>
										<li><a href="#none" onclick="return false;" class="c_btn h34 black line close selectMass_screenPrice"> 일괄변경</a></li>
										<li><a href="#none" onclick="selectMassClear('_screenPrice')" class="c_btn h34 black line close "> 일괄비우기</a></li>
										<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
									</ul>
								</div>
							</form>
						</div>
					</div>

					<?// KAY :: 2021-04-15 ::  판매가 팝업 ?>
					<div class="popup _price_pop" id="" style="display:none;width:640px;background:#fff;">
						<!--  레이어팝업 공통타이틀 영역 -->
						<div class="pop_title">판매가 변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
						<!-- 하얀색박스공간 -->
						<div class="data_form">
							<form name="change_price">
								<table class="table_form">
									<colgroup>
										<col width="130"><col width="*">
									</colgroup>
									<tbody>
										<tr>
											<th ><span class="tit">판매가</span></th>
											<td>
												<input type="text" name="common_price" style="width:150px;" class="design _price_cal" placeholder="판매가">
												<?php echo _InputSelect( 'common_type' , array('price','per'),'price',' data-class="_price_pop" ', array('원','%')); ?>
												<?php echo _InputSelect( 'common_ud' , array('no','up','down'),'down',' data-class="_price_pop" ' , array('금액반영','인상','인하') ); ?>
											</td>
										</tr>

										<tr class="common_perdel">
											<th ><span class="tit ">절사 단위</span></th>
											<td >
												<?php echo _InputRadio( 'common_perdel' , array('per_no','per_te','per_h','per_th'),'per_h','data-class="_price_pop"' , array('선택안함','10원','100원','1,000원') ); ?>
												<div class="tip_box">
													<?php echo _DescStr ('값을 입력한 후 단위를 선택해야 합니다.'); ?>
													<?php echo _DescStr ('절사안함을 선택시 1원단위로 절사됩니다.'); ?>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="tip_box">
									<?php echo _DescStr ('금액반영을 선택하시면 입력한 금액이 지정됩니다.'); ?>
									<?php echo _DescStr ('인상을 선택하시면 입력한 값과 원래 지정된 값을 계산하여 값을 올립니다.'); ?>
									<?php echo _DescStr ('인하를 선택하시면 입력한 값과 원래 지정된 값을 계산하여 값을 내립니다.'); ?>
									<?php echo _DescStr ('인상/인하를 선택하지 않을 시 입력한 값으로 변경되어 일괄지정됩니다.'); ?>
									<?php echo _DescStr ('율(%) 선택시 절사기능 설정 후 인상/인하가 적용됩니다.'); ?>
								</div>
								<div class="c_btnbox">
									<ul>
										<li><a href="#none" onclick="return false;" class="c_btn h34 black line close selectMass_Price"> 일괄변경</a></li>
										<li><a href="#none" onclick="selectMassClear('_price')" class="c_btn h34 black line close"> 일괄비우기</a></li>
										<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
									</ul>
								</div>
							</form>
						</div>
					</div>

</form>


<SCRIPT>
	// - 옵션열기 ---
	function option_popup(pass_code , pass_mode) {
		window.open("_product_option.form.php?pass_mode="+pass_mode+"&pass_code=" + pass_code ,"","width=1064,height=500,scrollbars=yes");
	}

	// - 추가옵션열기 ---
	function addoption_popup(code) {
		window.open("_product_addoption.popup.php?pass_code=" + code,"addoption","width=1064,height=500,scrollbars=yes");
	}

	// -------------- 선택상품 일괄비우기 --------------
	// KAY :: 2021-04-20 :: class_val (공급가,수수료,기존가,판매가)에 대한 저장값
	function selectMassClear(class_val) {
		if(confirm("일괄비우기 하시겠습니까?") ){
			if($('.js_ck').is(":checked")){
				$('.js_ck:checked').each(function(){
					var _pcode = $(this).data("pcode");
					$("input[name='"+class_val+"["+_pcode+"]']").val(0);
				});
			}
			$("input[name=_mode]").val('mass_price');
			frm.submit();
		}else{
			return false;
		}
	}


	//---------- KAY :: 2021-04-16 각 팝업 화면에 노출을 위한 공통 처리 ---------
	// 팝업띄우기 - 초기화
	function lightbox_me_reset(wrap_class){
		$("."+wrap_class+" input[name='common_price']").val(""); // 금액 reset
		$("."+wrap_class+" .common_perdel").hide(); // 절삭닫기
	}

	 // 절삭함수	_perdel_type : 절삭타입 (per_te , per_h , per_th)
	 function price_cut(_perdel_type , price){
		 var _screenPer_m = price;
		 switch(_perdel_type){
			case "per_te": _screenPer_m = Math.floor(price/10)*10; break;
			case "per_h": _screenPer_m = Math.floor(price/100)*100; break;
			case "per_th": _screenPer_m = Math.floor(price/1000)*1000; break;
		 }
		return _screenPer_m;
	 }

	// KAY :: 2021-04-20 :: 팝업 계산함수	
	//cal_class : 입력값(텍스트), cal_class_val(입력받은값-저장된것),cla_type(인상,인하,할인적용,금액 data-class값)
	function lightbox_me_Calculation(cal_class,cal_class_val,cal_type){
		$('.js_ck:checked').each(function(){
			var _pcode = $(this).data('pcode');//선택 상품코드
			var _common_price = $.trim($("."+cal_class).val()); // 지정 

			var common_ud = $("select[name='common_ud'][data-class='" + cal_type + "']").val(); // 인상인하 타입선택
			var common_type = $("select[name='common_type'][data-class='" + cal_type  + "']").val(); // 할인적용금액,할인적용율(%) 타입선택

			// KAY :: 2021-04-13 :: 계산(절삭단위,인상인하)
			// 변수정의				
			var common_perdel = $("input[name='common_perdel'][data-class='"	+	cal_type	+	"']:checked").val(); // 절사단위 변수
			var _p_Per =parseFloat(_common_price)/100; // 퍼센트 입력시 퍼센트 값
		
			var _price_cal = $("input[name='"+cal_class_val+"["+_pcode+"]']");
			var _price_plus = parseInt(_price_cal.val())+parseInt(_common_price); //이전 가격 + 입력 가격
			var _price_minus = parseInt(_price_cal.val())-parseInt(_common_price); //이전 가격 - 입력 가격
			var _per_plus= parseFloat(_price_cal.val())*(1+(_p_Per));// 원래가격 + 퍼센트 계산후 가격
			var _per_minus = parseFloat(_price_cal.val())*(1-(_p_Per));// 원래가격 - 퍼센트 계산후 가격

			// 사전체크
			if(_common_price<=0){alert ("값을 입력해주세요");return false;}
			if(common_type == 'per' && _common_price>=100){	alert ("100보다 작은값을 입력해주세요.");return false;}
			if(common_ud =='down' && common_type == 'price' && _price_minus<=0 ){alert("0 이하 값으로 인해 인하할 수 없는 상품이 있습니다.");return false;}
			if(common_ud =='down' && common_type == 'per' && _per_minus<=0 ){alert("0 이하 값으로 인해 인하할 수 없는 상품이 있습니다.");return false;}

			// 금액반영
			if(common_ud =='no'&&common_type == 'price' ){	_price_cal.val(_common_price);	 }

			// 인상
			if(common_ud =='up'){
				if(common_type == 'price' ){	_price_cal.val(_price_plus);	}
				if(common_type == 'per' ){	_price_cal.val(price_cut(common_perdel , _per_plus));	} // 절삭적용
			}

			// 인하
			if(common_ud =='down'){
				if(common_type == 'price' ){	_price_cal.val(_price_minus);	}
				if(common_type == 'per' ){	_price_cal.val(price_cut(common_perdel , _per_minus));	} // 절삭적용
			}
		});

		$("input[name=_mode]").val('mass_price');
		frm.submit();
	}


	// KAY :: 2021-04-20 :: common_type 선택 시 변경사항
	$("select[name='common_type']").change(function(){
		var _type = $(this).val();
		var _class = $(this).data("class");
		var _type_ud = $("select[name='common_ud'][data-class='" + _class + "']").val();
		if(_type == "price" ){	$("."+_class+" .common_perdel").hide();}
		if(_type == "per" ){	$("."+_class+" .common_perdel").show();}
		if(_type == "per" &&_type_ud=="no" ){ alert("%일 경우 금액반영은 이용하실 수 없습니다."); $("select[name='common_ud'][data-class='" + _class + "']").val('');return false;	}
	});

	$("select[name='common_ud']").change(function(){
		var _type_ud = $(this).val();
		var _class = $(this).data("class");
		var _type = $("select[name='common_type'][data-class='" + _class + "']").val();
		if(_type == "price" &&_type_ud=="no" ){$("."+_class+" .common_perdel").hide();}
		if(_type == "per" &&_type_ud=="no" ){	alert("%일 경우 금액반영은 이용하실 수 없습니다."); $("select[name='common_ud'][data-class='" + _class + "']").val('');return false;	}
	});



	//---------- KAY :: 2021-04-16 각 팝업 화면에 노출--------------------------------------
	// KAY :: 2021-04-15 :: 지정 정산방식 팝업 띄우기 + 일괄적용
	$('.mass_commission_type').on('click',function(){
		if($('.js_ck').is(":checked")){
			$("._commission_type_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{	
			alert('1개 이상 선택해 주시기 바랍니다.');	
		}
	});

	$('.selectMass_commission_type').on('click',function(){
		if( confirm("정산방식을 일괄변경하시겠습니까?") ){
			$('.js_ck:checked').each(function(){
				var _pcode = $(this).data('pcode');//선택 상품코드
				var _commission_type = $("input[name='_commission_type[all]']").filter(function() {if (this.checked) return this;}).val(); // 지정 정산방식
				if( _commission_type != ''){	$("#_commission_type_" + _pcode +"_" + _commission_type ).prop('checked', true) ;	}// 정산방식 적용
			});
			$("input[name=_mode]").val('mass_price');
			frm.submit();
		}
		else{	return false;	}
	});



	// KAY :: 2021-04-15 :: 지정 공급가 팝업 띄우기 + 일괄적용
	$('.mass_change_sPrice').on('click',function(){
		if($('.js_ck').is(":checked")){
			lightbox_me_reset("_sPrice_pop");
			$("._sPrice_pop").lightbox_me({centered: true, closeEsc: false, onClose: function(){} ,onLoad: function() {}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_sPrice').on('click',function(){
		if( confirm("공급가를 일괄변경하시겠습니까?") ){
			lightbox_me_Calculation("_sPrice_cal","_sPrice","_sPrice_pop");
		}
		else{
			return false;
		}
	});


	// KAY :: 2021-04-15 :: 지정 수수료 팝업 띄우기	+ 일괄적용
	$('.mass_change_sPersent').on('click',function(){
		$("._perdel_sPersent").hide();
		if($('.js_ck').is(":checked")){
			$("._sPersent_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_sPersent').on('click',function(){
		if( confirm("수수료를 일괄변경하시겠습니까?") ){
			$('.js_ck:checked').each(function(){
				var _pcode = $(this).data('pcode');//선택 상품코드
				var _sPersent = $.trim($("input[name='_sPersent[all]']").val()); // 지정 수수료
				if( _sPersent != ''){	$("._sPersent[name='_sPersent[" + _pcode +"]']").val(_sPersent);	}// 수수료 적용
			});
			$("input[name=_mode]").val('mass_price');
			frm.submit();
		}
		else{
			return false;
		}
	});


	// KAY :: 2021-04-15 :: 지정 기존가 팝업 띄우기	+ 일괄적용
	$('.mass_change_screenPrice').on('click',function(){
		$(".common_perdel").hide();
		if($('.js_ck').is(":checked")){
			lightbox_me_reset("_screenPrice_pop");
			$("._screenPrice_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});


	$('.selectMass_screenPrice').on('click',function(){
		if( confirm("기존가를 일괄변경하시겠습니까?") ){
			lightbox_me_Calculation("_screenPrice_cal","_screenPrice","_screenPrice_pop");
		}
		else {
			return false;
		}
	});


	// KAY :: 2021-04-15 :: 지정 판매가 팝업 띄우기	+ 일괄적용
	$('.mass_change_price').on('click',function(){
		$(".common_perdel").hide();
		if($('.js_ck').is(":checked")){
			lightbox_me_reset("_price_pop");
			$("._price_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_Price').on('click',function(){
		if( confirm("판매가를 일괄변경하시겠습니까?") ){
			lightbox_me_Calculation("_price_cal","_price","_price_pop");
		}
		else{
			return false;
		}
	});


	// KAY :: 2021-04-15 :: 개별수정(기존가격 일괄관리 파란 변경버튼) 
	// 정산방식, 공급가, 수수료,판매가,기존가 개별변경
	$('.product_price_change').on('click',function(){
		var pcode = $(this).data("pcode");// 상품코드 추출
		var _commission_type = $("input[name='_commission_type[" + pcode +"]']:checked").val(); //정산방식
		var _sPrice = $("input[name='_sPrice[" + pcode +"]']").val(); //공급가
		var _sPersent = $("input[name='_sPersent[" + pcode +"]']").val(); //수수료
		var _screenPrice = $("input[name='_screenPrice[" + pcode +"]']").val(); //기존가
		var _price = $("input[name='_price[" + pcode +"]']").val(); //판매가
		// encodeURIComponent
		$.ajax({
			data: {'_mode': 'price_direct_change' , 'pcode': pcode, '_commission_type': _commission_type,'_sPrice': _sPrice,'_sPersent': _sPersent,'_screenPrice': _screenPrice,'_price':_price},
			type: 'POST', cache: false, dataType: 'JSON',
			url: '_product_mass.pro.php',			
			success: function(data) {
				if(data.res == 'success') { alert("변경하였습니다.");}
				else {alert("변경에 실패하였습니다.");}
			}
		}).fail(function(e){console.log(e.responseText);});
	});

					

</SCRIPT>
<?PHP
	include_once("wrap.footer.php");
?>
