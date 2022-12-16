<?PHP
	include_once("wrap.header.php");

	// KAY :: 2021-04-02 :: 상품쿠폰 배열화
	//		p_coupon : 상품의 쿠폰 DB 항목
	if(function_exists('product_ex_coupon') !== true){
		function product_ex_coupon($p_coupon) {

			$arr = array();
			$ex_coupon = explode("|" , $p_coupon);

			if(sizeof($ex_coupon) > 2) {// 2개 초과 신규 데이터
				$arr['title'] = $ex_coupon[0];// 상품쿠폰명
				$arr['type'] = $ex_coupon[1];// 상품쿠폰 타입
				$arr['price'] = $ex_coupon[2];// 상품쿠폰 할인액
				$arr['per'] = $ex_coupon[3];// 상품쿠폰 할인율
				$arr['max'] = $ex_coupon[4];// 상품쿠폰 최대 할인액 - 제외함.
			}
			else { // 2개 - 이전 데이터
				$arr['title'] = $ex_coupon[0];// 상품쿠폰명
				$arr['price'] = $ex_coupon[1];// 상품쿠폰 할인액
			}
			return $arr;
		}
	}

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


		// 상품 일괄 관리 --- 검색폼 불러오기
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
<input type=hidden name='_submode' value='mass_point'>
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
							<col width="45px"/><col width="50px"/><col width="50px"/>
							<?if($SubAdminMode === true) { echo '<col width="120px"/>'; }// 입점업체 검색기능 2016-05-26 LDD?>
							<col width="60px"/><col width="*"/>
							<col width="90px"/><col width="180px"/><col width="190px"/><col width="155px"/>
						</colgroup>
						<thead>
							<tr>
								<th scope="col" ><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
								<th scope="col">NO</th>
								<th scope="col">노출</th>
								<?if($SubAdminMode === true) { echo '<th scope="col">입점업체</th>'; }// 입점업체 검색기능 2016-05-26 LDD?>
								<th scope="col">이미지</th>
								<th scope="col">상품코드/상품명</th>
								<th scope="col">적립율</th>
								<th scope="col">상품쿠폰명</th>
								<th scope="col">상품쿠폰 할인</th>
								<th scope="col">관리</th>
							</tr>
						</thead>
						<tbody>



							<!-- // 일괄지정 열 -->
							<tr>
								<?php
									$app_str = _DescStr("상품을 부분 또는 전체 선택 하신 후 <strong>버튼 클릭</strong>하시면 변경이 가능합니다." , "orange");
									$app_str .= _DescStr("버튼 클릭 후 <strong>팝업창의 일괄변경을 클릭하시면 바로 저장</strong>됩니다." , "orange");
									$app_str .= _DescStr("<strong>팝업창의 일괄 비우기 클릭 시 바로 적용되니 주의하시기 바랍니다.</strong>" , "orange");
									echo ($SubAdminMode === true ? '<td colspan="6" ><div class="tip_box">'. $app_str .'</div></td>' : '<td colspan="5"><div class="tip_box">'. $app_str .'</div></td>');// 입점업체 검색기능 2016-05-26 LDD
								?>

								<?php // KAY :: 2021-04-02 :: 상품쿠폰일괄지정 입력값 (상품쿠폰할인-할인액,할인율 텍스트 2개로 나눠놓음 )?>
								<td><span class="option_btn"><a href="#none" onclick="return false;" class="c_btn h22 gray mass_point_per">적립율 변경</span></a></td>
								<td><span class="option_btn"><a href="#none" onclick="return false;" class="c_btn h22 gray mass_coupon_title">상품쿠폰명 변경</span></a></td>
								<td><span class="option_btn"><a href="#none" onclick="return false;" class="c_btn h22 gray mass_coupon_price">상품쿠폰할인 변경</span></a></td>
								<td></td>
							</tr>
							<!-- 일괄지정 열 -->

<?PHP

	//if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";
	foreach($res as $k=>$v){

		$_num = $TotalCount - $count - $k ;

		$_p_img = get_img_src('thumbs_s_'.$v[p_img_list_square]);
		if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

		//KAY :: 2021-04-20 // 상품쿠폰 개별 변경 추가
		$_mod = "
			<a href='#none' onclick='return false;' class='c_btn h22 blue product_change' data-pcode='".$v['p_code']."'>변경</a>
			<a href='_product.form.php?_mode=modify&_code=" . $v[p_code] . "' class='c_btn h22 ' target='_blank'>수정</a>
		";
		$preview = "<a href='#none' onclick=\"window.open('/?pn=product.view&pcode=".$v[p_code]."')\" class='c_btn h22 '>미리보기</a>";


		// 입점업체 검색기능 2016-05-26 LDD
		$app_subadmin_string = ($SubAdminMode === true ? "<td >" . $arr_customer2[$v['p_cpid']] ."</td>" : "");


		// KAY :: 2021-04-09 :: 상품쿠폰액 텍스트값 변경
		$ex_coupon = product_ex_coupon($v['p_coupon']);

		$ex_coupon['per']= number_format(floor($ex_coupon['per']*10)/10,1); // 퍼센트 첫째짜리까지

		// KAY :: 2021-04-20 :: 상품쿠폰 출력
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

								<td style='text-align:right; margin-right:5px;'><input type='text' name=_point_per[".$v['p_code']."] value='". $v['p_point_per'] ."' class='design _point_per' style='width:40px;'><span class='fr_tx term'>%</span></td>
								<td><input type='text' name=_coupon_title[".$v['p_code']."] value='". $ex_coupon['title'] ."' class='design _coupon_title' ></td>
								<td style='text-align:right; margin-right:5px;'>
									<input type='text' name= _coupon_price[".$v['p_code']."]  value='". number_format($ex_coupon['price']) ."' 	class='design _coupon_price' style='width:70px; display:". ($ex_coupon['type'] == 'price' ? '' : "none;") . "' placeholder='할인액'>
									<input type='text' name= _coupon_per[".$v['p_code']."]  value='". $ex_coupon['per'] ."' class='design _coupon_per' style='width:70px; display: ". ($ex_coupon['type'] == 'per' ? '' : "none;") . "' placeholder='할인율'>
									" . _Inputselect( "_coupon_type[".$v['p_code']."]" , array('price','per'),$ex_coupon['type'],"class='_coupon_type' data-pcode='".$v['p_code']."' ", array('할인액(원)','할인율(%)')) . "
									<input type='text' name= _coupon_max[".$v['p_code']."]  value='". $ex_coupon['max'] ."' class='design _coupon_max' style='width:164px; margin-top:5px; display: ". ($ex_coupon['type'] == 'per' ? '' : "none;") . "' placeholder='최대할인액'>
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


			<!-- KAY :: 2021-04-19 :: 적립율 팝업 -->
			<div class="popup _point_per_pop" id="" style="display:none;width:640px;background:#fff;">
				<!--  레이어팝업 공통타이틀 영역 -->
				<div class="pop_title">적립율 변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
				<!-- 하얀색박스공간 -->
				<div class="data_form">
					<form name="">
						<table class="table_form">
							<colgroup>
								<col width="130"><col width="*">
							</colgroup>
							<tbody>
								<tr>
									<th><span class="tit">적립율</span></th>
									<td><input type='text' name="_point_per[all]" value='' class='design' style='width:80px;' placeholder="적립율"><span class="fr_tx term">%</span></td>
								</tr>
							</tbody>
						</table>
						<div class="tip_box">
							<?php echo _DescStr ('적립율을 입력하신 후 일괄변경을 클릭하시면 변경됩니다.'); ?>
						</div>
						<!-- 레이어팝업 버튼공간 -->
						<div class="c_btnbox">
							<ul>
								<li><a href="#none" onclick="return false;" class="c_btn h34 black line close selectMass_point_per"> 일괄변경</a></li>
								<li><a href="#none" onclick="selectMassClear('_point_per');" class="c_btn h34 black line close">일괄비우기</a></li>
								<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
							</ul>
						</div>
					</form>
				</div>
			</div>

			<!-- KAY :: 2021-04-19 :: 상품쿠폰명 팝업 -->
			<div class="popup _coupon_title_pop" id="" style="display:none;width:640px;background:#fff;">
				<!--  레이어팝업 공통타이틀 영역 -->
				<div class="pop_title">상품쿠폰명 변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
				<!-- 하얀색박스공간 -->
				<div class="data_form">
					<form name="">
						<table class="table_form">
							<colgroup>
								<col width="130"><col width="*">
							</colgroup>
							<tbody>
								<tr>
									<th><span class="tit">상품쿠폰명</span></th>
									<td ><input type='text' name="_coupon_title[all]" value='' placeholder="상품쿠폰명" class='design' ></td>
								</tr>
							</tbody>
						</table>
						<div class="tip_box">
							<?php echo _DescStr ('상품쿠폰명을 입력하신 후 일괄변경을 클릭하시면 변경됩니다.'); ?>
						</div>
						<!-- 레이어팝업 버튼공간 -->
						<div class="c_btnbox">
							<ul>
								<li><a href="#none" onclick="return false;" class="c_btn h34 black line close selectMass_coupon_title"> 일괄변경</a></li>
								<li><a href="#none" onclick="selectMassClear('_coupon_title');" class="c_btn h34 black line close">일괄비우기</a></li>
								<li><a href="#none" onclick="return false;" class="c_btn h34 black line close"> 닫기</a></li>
							</ul>
						</div>
					</form>
				</div>
			</div>

			<!-- KAY :: 2021-04-19 :: 상품쿠폰할인 팝업 -->
			<div class="popup _coupon_price_pop" id="" style="display:none;width:640px;background:#fff;">
				<!--  레이어팝업 공통타이틀 영역 -->
				<div class="pop_title">상품쿠폰할인 변경<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>
				<!-- 하얀색박스공간 -->
				<div class="data_form">
					<form name="">
						<table class="table_form">
							<colgroup>
								<col width="130"><col width="*">
							</colgroup>
							<tbody>
								<tr>
									<th><span class="tit">상품쿠폰할인</span></th>
										<td style='text-align:right; margin-right:5px; '>
											<input type='text' name="_coupon_price[all]" value='' class='design _coupon_price_all' style='width:150px; ' placeholder="할인액">
											<input type='text' name="_coupon_per[all]" value='' class='design _coupon_price_all' style='width:80px; display:none;' placeholder="할인율">
												<?php echo _Inputselect( '_coupon_type[all]' , array('price','per') , 'price','', array('원','%') ); ?>
											<div class="dash_line coupon_price_max" style="display:none"><!-- 점선라인 --></div>
											<input type='text' name="_coupon_max[all]" value='' class='design coupon_price_max' style='width:150px; display:none;margin-top:5px' placeholder="최대할인액"><span class="fr_tx term coupon_price_max"style="display:none; margin-top:5px">원</span>
										<script>
											// KAY :: 2021-04-02 :: 상품쿠폰 선택(전체)
											$(document).on("change" , "select[name='_coupon_type[all]']", function(){
												var _type_all = $(this).val();
												// 전체숨김
												$("._coupon_price_all").hide(); $(".coupon_price_max").hide();
												// 개별열기
												if(_type_all == "price"){ $("input[name='_coupon_price[all]']").show();}
												if(_type_all == "per"){$("input[name='_coupon_per[all]']").show();$("input[name='_coupon_max[all]']").show();	$(".coupon_price_max").show(); }
											});
										</script>
										</td>
								</tr>
							</tbody>
						</table>
						<div class="tip_box">
							<?php echo _DescStr ('할인액,할인율을 선택하신 후 값을 입력해 주세요.'); ?>
							<?php echo _DescStr ('할인율 선택 후 최대할인액은 미정 또는 0원 설정 시 할인액의 제한이 없습니다.'); ?>
							<?php echo _DescStr ('쿠폰 할인율은 소수점 1 자리까지 허용합니다.'); ?>
							<?php echo _DescStr ('쿠폰 할인율 반영 후 실제 할인 적용 시 소수점은 버림처리합니다.'); ?>
						</div>

						<!-- 레이어팝업 버튼공간 -->
						<div class="c_btnbox">
							<ul>
								<li><a href="#none" onclick="return false;" class="c_btn h34 black line close selectMass_coupon_price"> 일괄변경</a></li>
								<li><a href="#none" onclick="selectMassClear('_coupon_price_all');" class="c_btn h34 black line close">일괄비우기</a></li>
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

	//KAY :: 2021-04-19 :: -------------- 선택상품 일괄비우기 --------------
	function selectMassClear(class_point) {
		if(confirm("일괄비우기 하시겠습니까?") ){
			if($('.js_ck').is(":checked")){
				$('.js_ck:checked').each(function(){
					var _pcode = $(this).data("pcode");

					if(class_point=='_point_per'){		$("input[name='_point_per["+_pcode+"]']").val(0);	}
					if(class_point=='_coupon_title'){	$("input[name='_coupon_title["+_pcode+"]']").val("");	}
					if(class_point=='_coupon_price_all'){
						$("input[name='_coupon_price["+_pcode+"]']").val("");
						$("input[name='_coupon_per["+_pcode+"]']").val("");
						$("input[name='_coupon_type["+_pcode+"]']").val("price");
						$("input[name='_coupon_max["+_pcode+"]']").val("");
					}
				});
				$("input[name=_mode]").val('mass_point');
				frm.submit();
			}
			else {
				alert('1개 이상 선택해 주시기 바랍니다.');
			}
		}
	 }

	// KAY :: 2021-04-09 :: 개별수정(상품쿠폰 일괄관리 파란 변경버튼)
	$(document).ready(function(){
		$('.product_change').on('click',function(){

			var pcode = $(this).data("pcode");// 상품코드 추출
			var _point_per = $("input[name='_point_per[" + pcode +"]']").val();	//적립율
			var _coupon_title = $("input[name='_coupon_title[" + pcode +"]']").val();	//상품쿠폰명
			var _coupon_type = $("select[name='_coupon_type[" + pcode +"]']").val();	//상품쿠폰 할인원,할인액 타입
			var _coupon_price = $("input[name='_coupon_price[" + pcode +"]']").val();		// 상품쿠폰할인금액
			var _coupon_per = $("input[name='_coupon_per[" + pcode +"]']").val();		//상품쿠폰 할인율(퍼센트)
			var _coupon_max = $("input[name='_coupon_max[" + pcode +"]']").val();	//상품쿠폰 할인율일경우 최댓값

			// encodeURIComponent
			$.ajax({
				data: {'_mode': 'point_direct_change' , 'pcode': pcode, '_point_per': _point_per,'_coupon_title': _coupon_title,'_coupon_type': _coupon_type,'_coupon_price': _coupon_price,'_coupon_per': _coupon_per,'_coupon_max': _coupon_max},
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

	// KAY :: 2021-04-19 :: 적립율 변경 팝업창 띄우기 + 일괄지정
	$('.mass_point_per').on('click',function(){
		if($('.js_ck').is(":checked")){
			$("._point_per_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_point_per').on('click',function(){
		if( confirm("적립율을 일괄변경하시겠습니까?") ){
			$('.js_ck:checked').each(function(){
			var _pcode = $(this).data('pcode');//선택 상품코드
			var _point_per = $.trim($("input[name='_point_per[all]']").val()); // 지정 적립율

			if( _point_per != ''){	$("input[name='_point_per[" + _pcode +"]']").val(_point_per);	}	// 적립율 적용
		});
			$("input[name=_mode]").val('mass_point');
			frm.submit();
		}
		else {		return false;	}
	});


	// KAY :: 2021-04-19 :: 상품쿠폰명 변경 팝업창 띄우기 + 일괄지정
	$('.mass_coupon_title').on('click',function(){
		if($('.js_ck').is(":checked")){
			$("._coupon_title_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {	},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_coupon_title').on('click',function(){
		if( confirm("상품쿠폰명을 일괄변경하시겠습니까?") ){
			$('.js_ck:checked').each(function(){
			var _pcode = $(this).data('pcode');//선택 상품코드
			var _coupon_title = $.trim($("input[name='_coupon_title[all]']").val()); // 지정 상품쿠폰명

			if( _coupon_title != ''){	$("input[name='_coupon_title[" + _pcode +"]']").val(_coupon_title);	}	// 상품쿠폰명 적용
		});
			$("input[name=_mode]").val('mass_point');
			frm.submit();
		}
		else {		return false;	}
	});



	// KAY :: 2021-04-02 :: 개별 상품쿠폰 할인액,할인율 선택
	$(document).on("change" , "._coupon_type", function(){
		var _type = $(this).val();
		var _pcode = $(this).data("pcode");
		// 전체숨김
		$("input[name='_coupon_price["+_pcode+"]']").hide();
		$("input[name='_coupon_per["+_pcode+"]']").hide();
		$("input[name='_coupon_max["+_pcode+"]']").hide();
		// 개별열기
		if(_type == "price"){ $("input[name='_coupon_price["+_pcode+"]']").show();}
		if(_type == "per"){$("input[name='_coupon_per["+_pcode+"]']").show();$("input[name='_coupon_max["+_pcode+"]']").show();}
	});



	// KAY :: 2021-04-19 :: 상품쿠폰할인 변경 팝업창 띄우기 + 일괄지정
	$('.mass_coupon_price').on('click',function(){
		if($('.js_ck').is(":checked")){
			$("._coupon_price_pop").lightbox_me({centered: true, closeEsc: false,onLoad: function() {
				$("select[name='common_type']").change(function(){
					var _type = $(this).val();
					var _pcode = $(this).data("pcode");

					// 전체숨김
					$("input[name='_coupon_price["+_pcode+"]']").hide();
					$("input[name='_coupon_per["+_pcode+"]']").hide();
					$("input[name='_coupon_max["+_pcode+"]']").hide();
					// 개별열기
					if(_type == "price"){ $("input[name='_coupon_price["+_pcode+"]']").show();}
					if(_type == "per"){$("input[name='_coupon_per["+_pcode+"]']").show();$("input[name='_coupon_max["+_pcode+"]']").show();}
				});
			},onClose: function(){}});
		}else{
			alert('1개 이상 선택해 주시기 바랍니다.');
		}
	});

	$('.selectMass_coupon_price').on('click',function(){
		if( confirm("상품쿠폰할인을 일괄변경하시겠습니까?") ){
			$('.js_ck:checked').each(function(){
			var _pcode = $(this).data('pcode');//선택 상품코드
			var _coupon_price = $.trim($("input[name='_coupon_price[all]']").val()); // 지정 상품쿠폰액
			var _coupon_type = $("select[name='_coupon_type[all]']").val();
			var _coupon_per = $.trim($("input[name='_coupon_per[all]']").val());
			var _coupon_max = $.trim($("input[name='_coupon_max[all]']").val());

			if(_coupon_type=='per'){
				$("._coupon_per").show(); $("._coupon_max").show(); $("._coupon_price").hide();
				if(_coupon_per >= 100){ alert("100미만값을 입력해주세요");return false;}
				if(_coupon_per <= 0){ alert("0 이상값을 입력해주세요"); return false;}
			}
			if(_coupon_type=='price'){
				$("._coupon_per").hide();$("._coupon_max").hide(); $("._coupon_price").show();
				if(_coupon_price <= 0){ alert("0 이상값을 입력해주세요");  return false;}
			}

			if( _coupon_type != ''){	$("select[name='_coupon_type[" + _pcode +"]']").val(_coupon_type);	}// 상품쿠폰타입 적용
			if( _coupon_price !=''){	$("input[name='_coupon_price[" + _pcode +"]']").val(_coupon_price);	}// 상품쿠폰할인금액 적용
			if(_coupon_per != ''){$("input[name='_coupon_per[" + _pcode +"]']").val(_coupon_per);}// 상품쿠폰할인율(%) 적용
			if(_coupon_max != ''){$("input[name='_coupon_max[" + _pcode +"]']").val(_coupon_max);}// 상품쿠폰할인율(%) 적용
		});

		$("input[name=_mode]").val('mass_point');
		frm.submit();
		}
		else {		return false;	}
	});


</SCRIPT>
<?PHP
	include_once("wrap.footer.php");
?>