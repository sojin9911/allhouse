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
		<strong>상품검색</strong><!-- 메뉴얼로 링크 -->
	</div>


	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<?php


		// 상품 일괄 관리 --- 검색폼 불러오기
		//			반드시 - s_query가 적용되어야 함.
		$s_query = " from smart_product as p where p_option_type_chk IN ('1depth','2depth','3depth') and p.p_cpid = '{$com_id}' ";

		include_once(OD_ADMIN_ROOT."/_product.inc_search.php");
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
						<a href="#none" onclick="selectMassModify(); return false;" class="c_btn h27 gray">선택상품정보수정</a>
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
							<td style='text-align:left; '>

									<table class="table_list">
										<colgroup>
											<col width='*'/>
											<col width='120px'/><col width='120px'/><col width='100px'/><col width='150px'/>
										</colgroup>
										<tbody>

										<?// 일괄지정 열 ?>
										<tr>
											<td >
												<div style='float:left; margin-left:10px; width:480px; text-align:left; '>
													<?=_DescStr("선택 또는 입력한 항목을 <strong>일괄지정을 클릭</strong>한 경우, <strong>선택 데이터의 입력 항목에 일괄로 지정</strong>됩니다." , "orange")?>
													<?=_DescStr("일괄지정 후 <strong>'선택 상품옵션 정보수정'을 클릭하여야 저장</strong>됩니다." , "orange")?>
												</div>
												<div style='float:right; margin-top:12px; margin-right:8px; width:200px; '>
													<a href="#none" onclick="selectMassMatching()" class="c_btn h27 gray">일괄지정</a>
													<a href="#none" onclick="selectMassClear()" class="c_btn h27 gray">일괄비우기</a>
												</div>

											</td>

											<td style='text-align:right; margin-right:5px;'><input type='text' name="_poption_supplyprice[all]" value='' class='design' style='width:70px;'><span class="fr_tx term">원</span></td>
											<td style='text-align:right; margin-right:5px;'><input type='text' name="_poptionprice[all]" value='' class='design' style='width:70px;'><span class="fr_tx term">원</span></td>
											<td style='text-align:right; margin-right:5px;'><input type='text' name="_cnt[all]" value='' class='design' style='width:50px;'><span class="fr_tx term">개</span></td>
											<td ><?=_InputRadio( "_view[all]" , array('Y','N' , '미지정'), "미지정" , "" , array('노출','숨김' , '미지정') , '')?></td>

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

		$_mod = "<span class='shop_btn_pack'><a href='_product.form.php?_mode=modify&_code=" . $v[p_code] . "' class='small gray' title='수정' target='_blank'>수정</a></span>";
		$preview = "<span class='shop_btn_pack'><a class='small white' href='#none' onclick=\"window.open('/?pn=product.view&pcode=".$v[p_code]."')\">미리보기</a></span>";


		echo "
							<tr>
								<td>" . $_num . "</td>
								<td style='text-align:left; '>
									<div style='both:clear; margin:5px; '><strong>". strip_tags($v['p_name']) ."</strong> ( " . $v['p_code'] .")</div>
									<div style='both:clear; margin:10px 5px 5px 5px; '>

										<table class='table_list'>
											<colgroup>
												<col width='45px'/><col width='*'/>
												<col width='120px'/><col width='120px'/><col width='100px'/><col width='150px'/>
											</colgroup>
											<thead>
												<tr>
													<th scope='col' ><label class='design'><input type='checkbox' class='js_option_AllCK' value='Y' data-pcode='".$v['p_code']."'></label></th>
													<th scope='col' >옵션명</th>
													<th scope='col' >공급가</th>
													<th scope='col' >판매가</th>
													<th scope='col' >재고</th>
													<th scope='col' >노출</th>
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
				echo "
					<tr>
						<td><input type=checkbox name='chk_pcode[".$po_v['po_uid']."]' value='Y' class='js_ck js_ck_".$v['p_code']."' data-pcode='".$po_v['po_uid']."'></td>
						<td style='text-align:left; '>". strip_tags($po_v['app_poptionname']) . "</td>
						<td style='text-align:right; margin-right:5px;'><input type='text' name=_poption_supplyprice[".$po_v['po_uid']."] value='". $po_v['po_poption_supplyprice'] ."' class='design _poption_supplyprice' style='width:70px;'><span class='fr_tx term'>원</span></td>
						<td style='text-align:right; margin-right:5px;'><input type='text' name=_poptionprice[".$po_v['po_uid']."] value='". $po_v['po_poptionprice'] ."' class='design _poptionprice' style='width:70px;'><span class='fr_tx term'>원</span></td>
						<td style='text-align:right; margin-right:5px;'><input type='text' name=_cnt[".$po_v['po_uid']."] value='". $po_v['po_cnt'] ."' class='design _cnt' style='width:50px;'><span class='fr_tx term'>개</span></td>
						<td >
							<input type='radio' id='_view_" . $po_v['po_uid']."_Y' name='_view[".$po_v['po_uid']."]' value='Y' class='_view' ". ($po_v['po_view'] == "Y" ? "checked" : "") ."><label for='_view".$po_v['po_uid']."_Y'> 노출</label>
							<input type='radio' id='_view_" . $po_v['po_uid']."_N' name='_view[".$po_v['po_uid']."]' value='N' class='_view' ". ($po_v['po_view'] == "N" ? "checked" : "") ."><label for='_view".$po_v['po_uid']."_N'> 숨김</label>
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
	 function selectMassClear() {
		 if($('.js_ck').is(":checked")){
			 $('.js_ck:checked').each(function(){
				$("._poption_supplyprice").val(0) ;
				$("._poptionprice").val(0) ;
				$("._cnt").val(0) ;
			 });
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }
	 // -------------- 선택상품 일괄비우기 --------------

	 // -------------- 선택상품 일괄지정 --------------
	 function selectMassMatching() {
		 if($('.js_ck').is(":checked")){

			 var _poption_supplyprice = $.trim($("input[name='_poption_supplyprice[all]']").val()); // 지정 공급가
			 var _poptionprice = $.trim($("input[name='_poptionprice[all]']").val()); // 지정 판매가
			 var _cnt = $.trim($("input[name='_cnt[all]']").val()); // 지정 재고
			 var _view = $("input[name='_view[all]']").filter(function() {if (this.checked) return this;}).val(); // 지정 노출여부

			 $('.js_ck:checked').each(function(){

				var _pcode = $(this).data('pcode');//선택 상품코드

				// 공급가 적용				
				if( _poption_supplyprice != ''){
					$("._poption_supplyprice[name='_poption_supplyprice[" + _pcode +"]']").val(_poption_supplyprice) ;
				}

				// 판매가 적용				
				if( _poptionprice != ''){
					$("._poptionprice[name='_poptionprice[" + _pcode +"]']").val(_poptionprice) ;
				}

				// 판매가 적용				
				if( _cnt != ''){
					$("._cnt[name='_cnt[" + _pcode +"]']").val(_cnt) ;
				}

				// 노출여부 적용
				if( _view != '미지정'){
					$("#_view_" + _pcode +"_" + _view ).prop('checked', true) ;
				}

			 });
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }
	 // -------------- 선택상품 일괄지정 --------------




</SCRIPT>
<?PHP
	include_once("wrap.footer.php");
?>