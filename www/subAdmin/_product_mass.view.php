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

	// 상품 일괄 관리 --- 검색폼 불러오기
	//			반드시 - s_query가 적용되어야 함.
	$s_query = " from smart_product as p where 1 and p.p_cpid = '{$com_id}' ";

	echo '<div class="group_title"><strong>상품검색</strong></div>';
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

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->

	<!-- ● 데이터 리스트 -->
	<div class="data_list">


<form name=frm method=post action='_product_mass.pro.php' >
<input type=hidden name='_mode' value=''>
<input type=hidden name='_submode' value='mass_view'>
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
							<col width="45px"/><col width="50px"/><col width="50px"/>
							<col width="60px"/><col width="*"/>
							<col width="100px"/><col width="150px"/>
						</colgroup>
						<thead>
							<tr>
								<th scope="col" ><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
								<th scope="col">NO</th>
								<th scope="col">노출</th>
								<th scope="col">이미지</th>
								<th scope="col">상품코드/상품명</th>
								<th scope="col">재고</th>
								<th scope="col">관리</th>
							</tr>
						</thead>
						<tbody>



							<?// 일괄지정 열 ?>
							<tr>
								<td colspan="5">
									<div class="tip_box">
										<?php echo _DescStr("선택 또는 입력한 항목을 <strong>일괄지정을 클릭</strong>한 경우, <strong>선택 데이터의 입력 항목에 일괄로 지정</strong>됩니다."); ?>
										<?php echo _DescStr("일괄지정 후 <strong>선택상품정보수정을 클릭하여야 저장</strong>됩니다."); ?>
									</div>
								</td>
								<td style='text-align:right; margin-right:5px;'><input type='text' name="_stock[all]" value='' class='design' style='width:50px;'><span class="fr_tx term">개</span></td>

								<td>
									<div class='lineup-vertical'>
										<a href="#none" onclick="selectMassMatching()" class="c_btn h22 gray">일괄지정</a>
										<a href="#none" onclick="selectMassClear()" class="c_btn h22 gray">일괄비우기</a>
									</div>
								</td>
							</tr>
							<?// 일괄지정 열 ?>



<?PHP

	//if(sizeof($res) < 1) echo "<tr><td colspan=12 height='40'>내용이 없습니다.</td></tr>";

	foreach($res as $k=>$v){

		$_num = $TotalCount - $count - $k ;

		$_p_img = get_img_src('thumbs_s_'.$v[p_img_list_square]);
		if($_p_img == '') $_p_img = OD_ADMIN_DIR.'/images/thumb_no.jpg';

		$_mod = "<a href='_product.form.php?_mode=modify&_code=" . $v[p_code] . "' class='c_btn h22 ' target='_blank'>수정</a>";
		$preview = "<a href='#none' onclick=\"window.open('/?pn=product.view&pcode=".$v[p_code]."')\" class='c_btn h22 '>미리보기</a>";

		echo "
							<tr>
								<td><input type=checkbox name='chk_pcode[".$v['p_code']."]' value='Y' class='js_ck' data-pcode='".$v['p_code']."'></td>
								<td>" . $_num . "</td>
								<td>" . $arr_adm_button[($v['p_view'] == 'Y' ? '노출' : '숨김')] . "</td>
								<td>".( $v['p_img_list_square'] ? "<img src='".$_p_img."' style='width:50px !important;'>" : "&nbsp;" )."</td>
								<td style='text-align:left; '>
									<div style='both:clear; margin:5px;'>[ " . $v['p_code'] ." ]</div>
									<div style='both:clear; margin:5px; font-weight:bold;'>". strip_tags($v['p_name']) . "</div>
								</td>
								<td style='text-align:right; margin-right:5px;'><input type='text' name=_stock[".$v['p_code']."] value='". $v['p_stock'] ."' class='design _stock' style='width:50px;'><span class='fr_tx term'>개</span></td>
								<td>
									<div class='lineup-vertical'>
										". $_mod ."
											<span class='shop_btn_pack'><span class='blank_3'></span></span>
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
				$("input[name=_mode]").val('mass_view');
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
				$("._stock").val(0) ;
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

			 var _view = $("input[name='_view[all]']").filter(function() {if (this.checked) return this;}).val(); // 지정 노출
			 var _bestview = $("input[name='_bestview[all]']").filter(function() {if (this.checked) return this;}).val(); // 지정 베스트
			 var _newview = $("input[name='_newview[all]']").filter(function() {if (this.checked) return this;}).val(); // 지정 신규
			 var _saleview = $("input[name='_saleview[all]']").filter(function() {if (this.checked) return this;}).val(); // 지정 Today's Pick
			 var _stock = $.trim($("input[name='_stock[all]']").val()); // 지정 재고

			 $('.js_ck:checked').each(function(){

				var _pcode = $(this).data('pcode');//선택 상품코드

				// 노출 적용
				if( _view != '미지정'){
					$("#_view_" + _pcode +"_" + _view ).prop('checked', true) ;
				}

				// 베스트 적용
				if( _bestview != '미지정'){
					$("#_bestview_" + _pcode +"_" + _bestview ).prop('checked', true) ;
				}

				// 신규 적용
				if( _newview != '미지정'){
					$("#_newview_" + _pcode +"_" + _newview ).prop('checked', true) ;
				}

				// Today's Pick 적용
				if( _saleview != '미지정'){
					$("#_saleview_" + _pcode +"_" + _saleview ).prop('checked', true) ;
				}

				// 재고 적용
				if( _stock != ''){
					$("._stock[name='_stock[" + _pcode +"]']").val(_stock) ;
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