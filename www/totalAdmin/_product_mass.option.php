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

?>

	<!-- ● 단락타이틀 -->
	<div class="group_title">
		<strong>상품검색</strong>
	</div>


	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<?php


		// 상품 일괄 관리 --- 검색폼 불러오기
		//			반드시 - s_query가 적용되어야 함.
		$s_query = " from smart_product as p where p_option_type_chk IN ('1depth','2depth','3depth') ";

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
<input type=hidden name='_submode' value='mass_option'>
<input type=hidden name='_select_category_cnt' value='0'>
<input type=hidden name=_PVSC value=<?=$_PVSC?>>

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
						<col width="50px"/><col width="*"/>
					</colgroup>
					<thead>
						<tr>
							<th scope="col" >NO</th>
							<th scope="col" >상품정보</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>&nbsp;</td>
							<td style='text-align:left; padding-top:0; padding-bottom:0'>

									<table class="table_list if_mass">
										<colgroup>
											<col width='*'/>
											<col width='120px'/><col width='120px'/><col width='100px'/><col width='200'/><col width='100px'/>
										</colgroup>
										<tbody>

										<?// 일괄지정 열 ?>
										<?// KAY :: 2021-04-21 ::  팝업 클릭 버튼 ?>
										<tr>
											<td >
												<div class='tip_box'>
													<?php echo _DescStr("상품을 부분 또는 전체 선택 하신 후 <strong>버튼 클릭</strong>하시면 변경이 가능합니다." , "orange")?>
													<?php echo _DescStr("버튼 클릭 후 <strong>팝업창의 일괄변경을 클릭하시면 바로 저장</strong>됩니다." , "orange")?>
													<?php echo _DescStr("<strong>팝업창의 일괄 비우기 클릭 시 바로 적용되니 주의하시기 바랍니다.</strong>" , "orange")?>
												</div>
											</td>
											<td><span class="option_btn"><a href="#none" onclick="return false;" class="c_btn h22 gray mass_poption_supplyprice">공급가 변경</span></a></td>
											<td><span class="option_btn"><a href="#none" onclick="return false;" class="c_btn h22 gray mass_poptionprice">판매가 변경</span></a></td>
											<td><span class="option_btn"><a href="#none" onclick="return false;" class="c_btn h22 gray mass_cnt">재고 변경</span></a></td>
											<td><span class="option_btn"><a href="#none" onclick="return false;" class="c_btn h22 gray mass_view">노출여부 변경</span></a></td>
											<td></td>
										</tr>
										<?// 일괄지정 열 ?>
										</tbody>
									</table>
								</div>
							</td>
						</tr>

<?PHP

	//if(sizeof($res) < 1) echo "<tr><td colspan=5 height='40'>내용이 없습니다.</td></tr>";

	foreach($res as $k=>$v){

		$_num = $TotalCount - $count - $k ;
		$_p_img = get_img_src('thumbs_s_'.$v[p_img_list_square]);
		if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

		// 입점업체 검색기능 2016-05-26 LDD
		$app_subadmin_string = ($SubAdminMode === true ? " , 입점업체 : " . $arr_customer2[$v['p_cpid']] : "");
		echo "
							<tr>
								<td>" . $_num . "</td>
								<td style='text-align:left; '>
									<div style='both:clear; margin:5px; '><strong>". strip_tags($v['p_name']) ."</strong> ( " . $v['p_code'] .  $app_subadmin_string .")</div>
									<div style='both:clear; '>

										<table class='table_list'>
											<colgroup>
												<col width='45px'/><col width='*'/>
												<col width='120px'/><col width='120px'/><col width='100px'/><col width='200'/><col width='100px'/>
											</colgroup>
											<thead>
												<tr>
													<th scope='col' ><label class='design'><input type='checkbox' class='js_option_AllCK' value='Y' data-pcode='".$v['p_code']."'></label></th>
													<th scope='col' >옵션명</th>
													<th scope='col' >공급가</th>
													<th scope='col' >판매가</th>
													<th scope='col' >재고</th>
													<th scope='col' >노출</th>
													<th scope='col' >관리</th>
												</tr>
											</thead>
											<tbody>
		";

		switch($v['p_option_type_chk']){
			case "1depth":
				$po_que = "
					select
						po.* , po.po_poptionname as app_poptionname
					from smart_product_option as po
					where
						po.po_pcode='".$v['p_code']."' and
						po.po_depth='1'
						order by po_sort asc , po_uid asc
				";
				break;
			case "2depth":
				$po_que = "
					select
						po2.* , CONCAT(po1.po_poptionname , ' &gt; ' , po2.po_poptionname) as app_poptionname
					from smart_product_option as po2
					INNER JOIN smart_product_option as po1 ON (po1.po_uid = po2.po_parent and po1.po_depth='1' )
					where
						po2.po_pcode='".$v['p_code']."' and
						po2.po_depth='2'
						order by po2.po_sort asc , po2.po_uid asc
				";
				break;
			case "3depth":
				$po_que = "
					select
						po3.* , CONCAT(po1.po_poptionname , ' &gt; ' , po2.po_poptionname , ' &gt; ' , po3.po_poptionname) as app_poptionname
					from smart_product_option as po3
					INNER JOIN smart_product_option as po1 ON (po1.po_uid = SUBSTRING_INDEX(po3.po_parent, ',', 1) and po1.po_depth='1')
					INNER JOIN smart_product_option as po2 ON (po2.po_uid = SUBSTRING_INDEX(po3.po_parent, ',', -1) and po2.po_depth='2')
					where
						po3.po_pcode='".$v['p_code']."' and
						po3.po_depth='3'
						order by po3.po_sort asc , po3.po_uid asc
				";
				break;
		}
		$po_res = _MQ_assoc($po_que);
		if(sizeof($po_res) > 0){
			foreach($po_res as $po_k=>$po_v) {
				// KAY :: 2021-04-21 :: 개별변경 추가
				$_mod = "<a href='#none' onclick='return false;' class='c_btn h22 blue option_change' data-pouid='".$po_v['po_uid']."'>변경</a>";
				echo "
					<tr>
						<td><input type=checkbox name='chk_pcode[".$po_v['po_uid']."]' value='Y' class='js_ck js_ck_".$v['p_code']."' data-pcode='".$po_v['po_uid']."'></td>
						<td style='text-align:left; '>". strip_tags($po_v['app_poptionname']) . "</td>
						<td style='text-align:right; margin-right:5px;'><input type='text' name=_poption_supplyprice[".$po_v['po_uid']."] value='". $po_v['po_poption_supplyprice'] ."' class='design _poption_supplyprice' style='width:70px;'><span class='fr_tx term'>원</span></td>
						<td style='text-align:right; margin-right:5px;'><input type='text' name=_poptionprice[".$po_v['po_uid']."] value='". $po_v['po_poptionprice'] ."' class='design _poptionprice' style='width:70px;'><span class='fr_tx term'>원</span></td>
						<td style='text-align:right; margin-right:5px;'><input type='text' name=_cnt[".$po_v['po_uid']."] value='". $po_v['po_cnt'] ."' class='design _cnt' style='width:50px;'><span class='fr_tx term'>개</span></td>
						<td >
							<div class='lineup-center'>
								<label for='_view".$po_v['po_uid']."_Y' class='design'><input type='radio' id='_view_" . $po_v['po_uid']."_Y' name='_view[".$po_v['po_uid']."]' value='Y' class='_view' ". ($po_v['po_view'] == "Y" ? "checked" : "") ."> 노출</label>
								<label for='_view".$po_v['po_uid']."_N' class='design'><input type='radio' id='_view_" . $po_v['po_uid']."_N' name='_view[".$po_v['po_uid']."]' value='N' class='_view' ". ($po_v['po_view'] == "N" ? "checked" : "") ."> 숨김</label>
							</div>
						</td>
						<td>
								<div class='lineup-vertical'>
										". $_mod ."
								</div>							
						</td>
					</tr>
				";
			}
		}
		echo "
											</tbody>
										</table>
									</div>
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
			</div>



			<!-- KAY :: 2021-04-20 :: 상품옵션일괄관리 공급가 팝업 -->
			<div class="popup _poption_supplyprice_pop" id="" style="display:none;width:640px;background:#fff;">
				<!--  레이어팝업 공통타이틀 영역 -->
				<div class="pop_title">공급가 변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
				<!-- 하얀색박스공간 -->
				<div class="data_form">
					<form name="">
						<table class="table_form">
							<colgroup>
								<col width="130"><col width="*">
							</colgroup>
							<tbody>
								<tr>
									<th><span class="tit">공급가</span></th>
									<td>
										<input type='text' name="common_price" value='' class='design _poption_supplyprice_cal'placeholder="공급가" style='width:150px;'>
										<?php echo _InputSelect( 'common_type' , array('price','per'),'price',' data-class="_poption_supplyprice_pop" ', array('원','%')); ?>
										<?php echo _InputSelect( 'common_ud' , array('no','up','down'),'down',' data-class="_poption_supplyprice_pop" ' , array('금액반영','인상','인하') ); ?>
									</td>
								</tr>
								<tr class="common_perdel">
									<th ><span class="tit ">절사 단위</span></th>
									<td >
										<?php echo _InputRadio( 'common_perdel' , array('per_no','per_te','per_h','per_th'),'per_h','data-class="_poption_supplyprice_pop"' , array('선택안함','10원','100원','1,000원') ); ?>
										<div class="tip_box">
											<?php echo _DescStr ('값을 입력하신 후 단위를 선택해주세요.'); ?>
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

						<!-- 레이어팝업 버튼공간 -->
						<div class="c_btnbox">
							<ul>
								<li><a href="#none" onclick="return false;" class="c_btn h34 black line close selectMass_poption_supplyprice"> 일괄변경</a></li>
								<li><a href="#none" onclick="selectMassClear('_poption_supplyprice');" class="c_btn h34 black line close">일괄비우기</a></li>
								<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
							</ul>
						</div>
					</form>
				</div>
			</div>

			<!-- KAY :: 2021-04-20 :: 상품옵션일괄관리 판매가 팝업 -->
			<div class="popup _poptionprice_pop" id="" style="display:none;width:640px;background:#fff;">
				<!--  레이어팝업 공통타이틀 영역 -->
				<div class="pop_title">판매가 변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
				<!-- 하얀색박스공간 -->
				<div class="data_form">
					<form name="">
						<table class="table_form">
							<colgroup>
								<col width="130"><col width="*">
							</colgroup>
							<tbody>
								<tr>
									<th><span class="tit">판매가</span></th>
									<td>
										<input type='text' name="common_price" value='' class='design _poptionprice_cal' placeholder='판매가'style='width:150px;'>
										<?php echo _InputSelect( 'common_type' , array('price','per'),'price',' data-class="_poptionprice_pop" ', array('원','%')); ?>
										<?php echo _InputSelect( 'common_ud' , array('no','up','down'),'down',' data-class="_poptionprice_pop" ' , array('금액반영','인상','인하') ); ?>
									</td>
								</tr>
								<tr class="common_perdel">
									<th ><span class="tit ">절사 단위</span></th>
									<td >
										<?php echo _InputRadio( 'common_perdel' , array('per_no','per_te','per_h','per_th'),'per_h','data-class="_poptionprice_pop"' , array('선택안함','10원','100원','1,000원') ); ?>
										<div class="tip_box">
											<?php echo _DescStr ('값을 입력하신 후 단위를 선택해주세요.'); ?>
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

						<!-- 레이어팝업 버튼공간 -->
						<div class="c_btnbox">
							<ul>
								<li><a href="#none" onclick="return false;" class="c_btn h34 black line close selectMass_poptionprice"> 일괄변경</a></li>
								<li><a href="#none" onclick="selectMassClear('_poptionprice');" class="c_btn h34 black line close">일괄비우기</a></li>
								<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
							</ul>
						</div>
					</form>
				</div>
			</div>

			<!-- KAY :: 2021-04-20 :: 상품옵션일괄관리 재고변경 팝업 -->
			<div class="popup _cnt_pop" id="" style="display:none;width:640px;background:#fff;">
				<!--  레이어팝업 공통타이틀 영역 -->
				<div class="pop_title">재고 변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
				<!-- 하얀색박스공간 -->
				<div class="data_form">
					<form name="">
						<table class="table_form">
							<colgroup>
								<col width="130"><col width="*">
							</colgroup>
							<tbody>
								<tr>
									<th><span class="tit">재고</span></th>
									<td><input type='text' name="_cnt[all]" value='' class='design' style='width:80px;'><span class="fr_tx term">개</span></td>
								</tr>
							</tbody>
						</table>
						<div class="tip_box">
							<?php echo _DescStr ('재고를 입력하신 후 일괄변경을 클릭하시면 변경됩니다.'); ?>
						</div>
						<!-- 레이어팝업 버튼공간 -->
						<div class="c_btnbox">
							<ul>
								<li><a href="#none" onclick="return false;" class="c_btn h34 black line close selectMass_cnt"> 일괄변경</a></li>
								<li><a href="#none" onclick="selectMassClear('_cnt');" class="c_btn h34 black line close">일괄비우기</a></li>
								<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
							</ul>
						</div>
					</form>
				</div>
			</div>

			<!-- KAY :: 2021-04-20 :: 상품옵션일괄관리 노출여부 팝업 -->
			<div class="popup _view_pop" id="" style="display:none;width:640px;background:#fff;">
				<!--  레이어팝업 공통타이틀 영역 -->
				<div class="pop_title">노출여부 변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
				<!-- 하얀색박스공간 -->
				<div class="data_form">
					<form name="">
						<table class="table_form">
							<colgroup>
								<col width="130"><col width="*">
							</colgroup>
							<tbody>
								<tr>
									<th><span class="tit">노출여부</span></th>
									<td><?=_InputRadio( "_view[all]" , array('Y','N'), "미지정" , "" , array('노출','숨김') , '')?></td>
								</tr>
							</tbody>
						</table>
						<div class="tip_box">
							<?php echo _DescStr ('노출여부를 선택하신 후 일괄변경을 클릭하시면 변경됩니다.'); ?>
						</div>
						<!-- 레이어팝업 버튼공간 -->
						<div class="c_btnbox">
							<ul>
								<li><a href="#none" onclick="return false;" class="c_btn h34 black line close selectMass_view"> 일괄변경</a></li>
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

	 // 선택상품 일괄수정
	 function selectMassModify() {
		 if($('.js_ck').is(":checked")){
				$("form[name=frm]").attr("action" , "_product_mass.pro.php");
				$("input[name=_mode]").val('mass_option');
				document.frm.submit();
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }
	
	// -------------- 선택상품 일괄비우기 --------------
	//	KAY :: 2021-04-20 ::option_val 옵션(공급가,판매가,재고,노출여부)에 대한 저장값
	function selectMassClear(option_val) {
		if(confirm("일괄비우기 하시겠습니까?")){
			if($('.js_ck').is(":checked")){
				$('.js_ck:checked').each(function(){
					var pcode = $(this).data("pcode");
					$("."+option_val+"[name='"+option_val+"["+pcode+"]']").val(0);
				});
			}
			$("input[name=_mode]").val('mass_option');
			frm.submit();
		}else{
			return false;
		}
	}

	//	KAY :: 2021-04-20 :: 절삭함수	_perdel_type : 절삭타입 (per_te , per_h , per_th)
	function price_cut(_perdel_type , price){
		var _screenPer_m = price;
		switch(_perdel_type){
			case "per_te": _screenPer_m = Math.floor(price/10)*10; break;
			case "per_h": _screenPer_m = Math.floor(price/100)*100; break;
			case "per_th": _screenPer_m = Math.floor(price/1000)*1000; break;
		}
		return _screenPer_m;
	}
	
	// KAY :: 2021-04-20 ::		팝업띄우기 - 초기화
	function lightbox_me_reset(wrap_class){
		$("."+wrap_class+" input[name='common_price']").val(""); // 금액 reset
		$("."+wrap_class+" .common_perdel").hide(); // 절삭닫기
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

	// KAY :: 2021-04-20 :: 절삭,인상,인하 계산
	function lightbox_me_Calculation(cal_class,cal_class_val,cal_type){
		var _common_price = $("."+cal_class).val(); 
		var common_ud = $("select[name='common_ud'][data-class='" + cal_type + "']").val(); // 인상인하 타입선택
		var common_type = $("select[name='common_type'][data-class='" + cal_type + "']").val(); // 할인적용금액,할인적용율(%) 타입선택
		var exit = false;
		$('.js_ck:checked').each(function(){
			var _pcode = $(this).data('pcode');//선택 상품코드
			// KAY :: 2021-04-13 :: 판매가 계산(절삭단위,인상인하)
			// 변수정의				
			var common_perdel = $("input[name='common_perdel'][data-class='" + cal_type + "']:checked").val(); // 절사단위 변수
			var _p_Per =parseFloat(_common_price)/100; //판매가 퍼센트 입력시 퍼센트 값
			var _price_cal = $("."+cal_class_val+"[name='"+cal_class_val+"[" + _pcode +"]']");// 이전가격 변수
			var _price_plus = parseInt(_price_cal.val())+parseInt(_common_price); //이전 가격 + 입력 가격
			var _price_minus = parseInt(_price_cal.val())-parseInt(_common_price); //이전 가격 - 입력 가격
			var _per_plus= parseFloat(_price_cal.val())*(1+(_p_Per));// 원래가격 + 퍼센트 계산후 가격
			var _per_minus = parseFloat(_price_cal.val())*(1-(_p_Per));// 원래가격 - 퍼센트 계산후 가격
				
			// 사전체크
			if(_common_price<=0){alert ("값을 입력해주세요"); return false;}
			if(common_type == 'per' && _common_price>=100){	alert ("100보다 작은값을 입력해주세요.");return false;}
			if(common_ud =='down' && common_type == 'price' && _price_minus < 0 ){ exit = true; return exit;  }
			if(common_ud =='down' && common_type == 'per' && _per_minus < 0 ){exit = true; return exit; }

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
				if(common_type == 'per' ){_price_cal.val(price_cut(common_perdel , _per_minus));	}//절삭적용
			}
		});
		if (exit == true ){alert("인하할 수 없는 상품이 있으며, 그 상품을 제외하고 인하적용 되었습니다.");}
	}




	// KAY :: 2021-04-20 :: 상품옵션관리 공급가 변경 팝업창 띄우기 + 일괄지정
	$('.mass_poption_supplyprice').on('click',function(){
		if($('.js_ck').is(":checked")){
			lightbox_me_reset("_poption_supplyprice_pop");
			$("._poption_supplyprice_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{	
			alert('1개 이상 선택해 주시기 바랍니다.');	
		}
	});

	$('.selectMass_poption_supplyprice').on('click',function(){
		if( confirm("옵션의 공급가를 일괄변경하시겠습니까?") ){
			lightbox_me_Calculation("_poption_supplyprice_cal","_poption_supplyprice","_poption_supplyprice_pop");
				$("input[name=_mode]").val('mass_option');
				frm.submit();
		}
		else {		return false;	}
	});



	// KAY :: 2021-04-20 :: 상품옵션관리 판매가 변경 팝업창 띄우기 + 일괄지정
	$('.mass_poptionprice').on('click',function(){
		if($('.js_ck').is(":checked")){
			lightbox_me_reset("_poptionprice_pop");
			$("._poptionprice_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{	
			alert('1개 이상 선택해 주시기 바랍니다.');	
		}
	});

	$('.selectMass_poptionprice').on('click',function(){
		if( confirm("옵션의 판매가를 일괄변경하시겠습니까?") ){
			lightbox_me_Calculation("_poptionprice_cal","_poptionprice","_poptionprice_pop");
			$("input[name=_mode]").val('mass_option');
			frm.submit();
		}
		else {		return false;	}
	});



	// KAY :: 2021-04-20 :: 상품옵션관리 재고 변경 팝업창 띄우기 + 일괄지정
	$('.mass_cnt').on('click',function(){
		if($('.js_ck').is(":checked")){
			$("._cnt_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{	
			alert('1개 이상 선택해 주시기 바랍니다.');	
		}
	});

	$('.selectMass_cnt').on('click',function(){
		if( confirm("옵션의 재고를 일괄변경하시겠습니까?") ){
			$('.js_ck:checked').each(function(){
				var _pcode = $(this).data('pcode');//선택 상품코드
				var _cnt = $.trim($("input[name='_cnt[all]']").val()); // 지정 재고
				if( _cnt != ''){	$("._cnt[name='_cnt[" + _pcode +"]']").val(_cnt);	}// 재고 적용
			});
				$("input[name=_mode]").val('mass_option');
				frm.submit();
		}
		else {		return false;	}
	});


	// KAY :: 2021-04-20 :: 상품옵션관리 노출여부 변경 팝업창 띄우기 + 일괄지정
	$('.mass_view').on('click',function(){
		if($('.js_ck').is(":checked")){
			$("._view_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{	
			alert('1개 이상 선택해 주시기 바랍니다.');	
		}
	});

	$('.selectMass_view').on('click',function(){
		if( confirm("옵션의 노출여부를 일괄변경하시겠습니까?") ){
			$('.js_ck:checked').each(function(){
				var _pcode = $(this).data('pcode');//선택 상품코드
				var _view = $("input[name='_view[all]']").filter(function() {if (this.checked) return this;}).val(); // 지정 노출여부
				if( _view != ''){	$("#_view_" + _pcode +"_" + _view ).prop('checked', true);	}	// 노출여부 적용
			});
				$("input[name=_mode]").val('mass_option');
				frm.submit();
		}
		else {		return false;	}
	});


	// KAY :: 2021-04-20 :: 개별수정(상품옵션 일괄관리 파란 변경버튼) 
	$(document).ready(function(){
		$('.option_change').on('click',function(){
			var _po_uid= $(this).data('pouid');// 상품코드 추출
			var _poption_supplyprice = $("._poption_supplyprice[name='_poption_supplyprice[" + _po_uid +"]']").val(); //상품옵션 공급가
			var _poptionprice = $("._poptionprice[name='_poptionprice[" + _po_uid +"]']").val(); //상품옵션 판매가
			var _cnt = $("._cnt[name='_cnt[" + _po_uid +"]']").val();	//상품옵션 재고
			var _view = $("._view[name='_view["+_po_uid +"]']").filter(function() {if (this.checked) return this;}).val(); // 상품옵션 노출
			// encodeURIComponent
			console.log( $(this).data());
			$.ajax({
				data: {'_mode': 'option_direct_change' , '_po_uid': _po_uid, '_poption_supplyprice': _poption_supplyprice, '_poptionprice': _poptionprice, '_cnt': _cnt, '_view': _view},
				type: 'POST', cache: false, dataType: 'JSON',
				url: '_product_mass.pro.php',			
				success: function(data) {
					console.log(data);
					if(data.res == 'success') { alert("변경하였습니다.");}
					else {alert("변경에 실패하였습니다.");}
				}
			}).fail(function(e){console.log(e.responseText);});
		});
	});
</SCRIPT>
<?PHP
	include_once("wrap.footer.php");
?>