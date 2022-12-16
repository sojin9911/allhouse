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
	$s_query = " from smart_product as p where 1 ";

	echo '<div class="group_title"><strong>상품검색</strong></div>';
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


	// - 입점업체 ---
	$arr_customer = arr_company();
	$arr_customer2 = arr_company2();
?>


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
						<!--<a href="#none" onclick="selectMassModify(); return false;" class="c_btn h27 gray">선택상품정보수정</a>-->
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
							<col width="200px"/><col width="100px"/><col width="160px"/>
						</colgroup>
						<thead>
							<tr>
								<th scope="col" ><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
								<th scope="col">NO</th>
								<th scope="col">노출</th>
								<?if($SubAdminMode === true) { echo '<th scope="col">입점업체</th>'; }// 입점업체 검색기능 2016-05-26 LDD?>
								<th scope="col">이미지</th>
								<th scope="col">상품코드/상품명</th>
								<th scope="col">노출</th>
								<th scope="col">재고</th>
								<th scope="col">관리</th>
							</tr>
						</thead>
						<tbody>



							<?// 일괄지정 열 ?>
							<tr>
								<?php
									$app_str = _DescStr("상품을 부분 또는 전체 선택 하신 후 <strong>버튼 클릭</strong>하시면 변경이 가능합니다." , "orange");
									$app_str .= _DescStr("버튼 클릭 후 <strong>팝업창의 일괄변경을 클릭하시면 바로 저장</strong>됩니다." , "orange");
									$app_str .= _DescStr("<strong>팝업창의 일괄 비우기 클릭 시 바로 적용되니 주의하시기 바랍니다.</strong>" , "orange");
									echo ($SubAdminMode === true ? '<td colspan="6" ><div class="tip_box">'. $app_str .'</div></td>' : '<td colspan="5"><div class="tip_box">'. $app_str .'</div></td>');// 입점업체 검색기능 2016-05-26 LDD
								?>
								
								<!--<td>
									<div class='lineup-center'>
										<?=_InputRadio( "_view[all]" , array('Y','N', '미지정'), "미지정" , "" , array('노출','숨김' , '미지정'))?>
									</div>
								</td>-->
								<td >	<span class="option_btn"><a href="#none" onclick="return false;" class="c_btn h22 gray mass_view">노출여부 변경</span></a></td>
								<!--<td style='text-align:right; margin-right:5px;'><input type='text' name="_stock[all]" value='' class='design' style='width:50px;'><span class="fr_tx term">개</span></td>-->
								<td >	<span class="option_btn"><a href="#none" onclick="return false;" class="c_btn h22 gray mass_stock">재고 변경</span></a></td>
								<td>
									<div class='lineup-center'>
										<!--<a href="#none" onclick="selectMassMatching()" class="c_btn h22 gray">일괄지정</a>-->
										<!--<a href="#none" onclick="selectMassClear()" class="c_btn h22 gray">일괄비우기</a>-->
									</div>
								</td>
							</tr>
							<?// 일괄지정 열 ?>



<?PHP

	//if(sizeof($res) < 1) echo "<tr><td colspan=12 height='40'>내용이 없습니다.</td></tr>";

	foreach($res as $k=>$v){

		$_num = $TotalCount - $count - $k ;

		$_p_img = get_img_src('thumbs_s_'.$v[p_img_list_square]);
		if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

		$_mod = "
			<a href='#none' onclick='return false;' class='c_btn h22 blue product_view_change' data-pcode='".$v['p_code']."'>변경</a>
			<a href='_product.form.php?_mode=modify&_code=" . $v[p_code] . "' class='c_btn h22 ' target='_blank'>수정</a>
		";
		$preview = "<a href='#none' onclick=\"window.open('/?pn=product.view&pcode=".$v[p_code]."')\" class='c_btn h22 '>미리보기</a>";

		// 입점업체 검색기능 2016-05-26 LDD
		$app_subadmin_string = ($SubAdminMode === true ? "<td >" . $arr_customer2[$v['p_cpid']] ."</td>" : "");

		echo "
					<tr>
						<td><input type=checkbox name='chk_pcode[".$v['p_code']."]' value='Y' class='js_ck' data-pcode='".$v['p_code']."'></td>
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
								<label for='_view_".$v['p_code']."_Y' class='design'><input type='radio' id='_view_".$v['p_code']."_Y' name='_view[".$v['p_code']."]' value='Y' class='_view' ". ($v['p_view'] == "Y" ? "checked" : "") ."> 노출</label>
								<label for='_view_".$v['p_code']."_N' class='design'><input type='radio' id='_view_".$v['p_code']."_N' name='_view[".$v['p_code']."]' value='N' class='_view' ". ($v['p_view'] == "N" ? "checked" : "") ."> 숨김</label>
							</div>
						</td>
						<td style='text-align:right; margin-right:5px;'><input type='text' name=_stock[".$v['p_code']."] value='". $v['p_stock'] ."' class='design _stock' style='width:50px;'><span class='fr_tx term'>개</span></td>
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


			</div>
			
			<!-- KAY :: 2021-04-19 ::  노출 팝업 -->
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
									<th><span class="tit">노출</span></th>
									<td>
										<?=_InputRadio( "_view[all]" , array('Y','N'),'','', array('노출','숨김'))?>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="tip_box">
							<?php echo _DescStr ('노출을 선택하시면 해당 상품이 노출됩니다.'); ?>
							<?php echo _DescStr ('숨김을 선택하시면 해당 상품이 숨김됩니다.'); ?>
						</div>
						<!-- 레이어팝업 버튼공간 -->
						<div class="c_btnbox">
							<ul>
								<li><a href="#none" onclick="return false;" class="c_btn h34 black line close selectMass_view "> 일괄변경</a></li>
								<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
							</ul>
						</div>
					</form>
				</div>
			</div>


			<!-- KAY :: 2021-04-19 :: 재고 팝업 -->
			<div class="popup _stock_pop" id="" style="display:none;width:640px;background:#fff;">
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
									<td style='text-align:right; margin-right:5px;'><input type='text' name="_stock[all]" value='' class='design' style='width:50px;'><span class="fr_tx term">개</span></td>
								</tr>
							</tbody>
						</table>
						<div class="tip_box">
							<?php echo _DescStr ('재고를 입력하신 후 일괄변경을 클릭하시면 변경됩니다.'); ?>
						</div>
						<!-- 레이어팝업 버튼공간 -->
						<div class="c_btnbox">
							<ul>
								<li><a href="#none" onclick="return false;" class="c_btn h34 black line close selectMass_stock "> 일괄변경</a></li>
								<li><a href="#none" onclick="selectMassClear();" class="c_btn h34 black line close">일괄비우기</a></li>
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
	/* function selectMassModify() {
		 if($('.js_ck').is(":checked")){
				$("form[name=frm]").attr("action" , "_product_mass.pro.php");
				$("input[name=_mode]").val('mass_view');
				document.frm.submit();
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }*/


	 // -------------- 선택상품 일괄비우기 --------------
	 /*function selectMassClear() {
		 if($('.js_ck').is(":checked")){
			 $('.js_ck:checked').each(function(){
				$("._stock").val(0) ;
			 });
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }*/

	 // -------------- 선택상품 일괄비우기 --------------

	 // -------------- 선택상품 일괄지정 --------------
	 /*function selectMassMatching() {
		 if($('.js_ck').is(":checked")){
			 var _view = $("input[name='_view[all]']").filter(function() {if (this.checked) return this;}).val(); // 지정 노출
			 var _bestview = $("input[name='_bestview[all]']").filter(function() {if (this.checked) return this;}).val(); // 지정 베스트
			 var _newview = $("input[name='_newview[all]']").filter(function() {if (this.checked) return this;}).val(); // 지정 신규
			 var _saleview = $("input[name='_saleview[all]']").filter(function() {if (this.checked) return this;}).val(); // 지정 Today's Pick
			 var _stock = $.trim($("input[name='_stock[all]']").val()); // 지정 재고

			 $('.js_ck:checked').each(function(){
				var _pcode = $(this).data('pcode');//선택 상품코드
				// 노출 적용
				if( _view != '미지정'){	$("#_view_" + _pcode +"_" + _view ).prop('checked', true);	}
				// 베스트 적용
				if( _bestview != '미지정'){	$("#_bestview_" + _pcode +"_" + _bestview ).prop('checked', true);	}
				// 신규 적용
				if( _newview != '미지정'){	$("#_newview_" + _pcode +"_" + _newview ).prop('checked', true);	}
				// Today's Pick 적용
				if( _saleview != '미지정'){	$("#_saleview_" + _pcode +"_" + _saleview ).prop('checked', true);	}
				// 재고 적용
				if( _stock != ''){	$("._stock[name='_stock[" + _pcode +"]']").val(_stock);	}

			 });
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }*/
	 // -------------- 선택상품 일괄지정 --------------



	// KAY :: 2021-04-19 :: 노출여부 변경 팝업창 띄우기 + 일괄지정
	$('.mass_view').on('click',function(){
		if($('.js_ck').is(":checked")){
			$("._view_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{	
			alert('1개 이상 선택해 주시기 바랍니다.');	
		}
	});

	$('.selectMass_view').on('click',function(){
		if( confirm("노출여부를 일괄변경하시겠습니까?") ){
			$('.js_ck:checked').each(function(){
			var _pcode = $(this).data('pcode');//선택 상품코드
			var _view = $("input[name='_view[all]']").filter(function() {if (this.checked) return this;}).val(); // 지정 노출

			if( _view != ''){	$("#_view_" + _pcode +"_" + _view ).prop('checked', true);	}
		});
				
		$("input[name=_mode]").val('mass_view');
		frm.submit();
		}
		else {		return false;	}
	});


	// KAY :: 2021-04-19 :: 재고변경 팝업창 띄우기 + 일괄지정
	$('.mass_stock').on('click',function(){
		if($('.js_ck').is(":checked")){
			$("._stock_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{	
			alert('1개 이상 선택해 주시기 바랍니다.');	
		}
	});

	$('.selectMass_stock').on('click',function(){
		if( confirm("재고를 일괄변경하시겠습니까?") ){
			$('.js_ck:checked').each(function(){
				var _pcode = $(this).data('pcode');//선택 상품코드
				var _stock = $.trim($("input[name='_stock[all]']").val()); // 지정 재고
		
				if( _stock != ''){	$("._stock[name='_stock[" + _pcode +"]']").val(_stock);	}
			});
				
			$("input[name=_mode]").val('mass_view');
			frm.submit();
			}
			else{
				return false;
			}
	});

	//KAY :: 2021-04-19 :: 선택상품 일괄비우기  ----------
	function selectMassClear() {
		if( confirm("일괄비우기 하시겠습니까?") ){
			if($('.js_ck').is(":checked")){
				$('.js_ck:checked').each(function(){
					var _pcode = $(this).data("pcode");
					$("input[name='_stock["+_pcode+"]']").val(0);
				});
			}
			$("input[name=_mode]").val('mass_view');
			frm.submit();
		}else{
			return false;
		}
	}

	// KAY :: 2021-04-15 :: 개별수정( 개별변경 파란 변경버튼) 
	$('.product_view_change').on('click',function(){
		var pcode = $(this).data("pcode");// 상품코드 추출
		var _view = $("input[name='_view["+pcode+"]']").filter(function() {if (this.checked) return this;}).val(); // 지정 노출
		var _stock = $("input[name='_stock[" + pcode +"]']").val(); // 재고
		
		// encodeURIComponent
		$.ajax({
			data: {'_mode': 'view_direct_change' , 'pcode': pcode, '_view': _view,'_stock': _stock},
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

