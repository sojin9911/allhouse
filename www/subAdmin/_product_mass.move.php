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




	// 전체 카테고리 정보 추출
	$arr_category_name = array();
	$c_que = "select c_uid , c_name from smart_category ";
	$c_res = _MQ_assoc($c_que);
	foreach( $c_res as $k=>$v ){
		$arr_category_name[$v['c_uid']] = $v['c_name'];
	}
	// 전체 카테고리 정보 추출
?>
		<!-- ● 단락타이틀 -->
		<div class="group_title">
			<strong>상품검색</strong>
		</div>


		<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
		<?php


			// 상품 일괄 관리 --- 검색폼 불러오기
			//			반드시 - s_query가 적용되어야 함.
			$s_query = " from smart_product as p where 1 and p.p_cpid = '{$com_id}' ";

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
		<div class="data_form if_search" style="margin-bottom:40px;">

			<!-- 폼테이블 2단 -->
			<table class="table_form">
				<colgroup>
					<col width="180px"/><!-- 마지막값은수정안함 --><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th>선택 카테고리 관리</th>
						<td>

							<a href="#none" onclick="selectCategoryAdd();" class="c_btn h27 gray">선택 상품 카테고리 추가</a>
							<a href="#none" onclick="selectCategorySelDel();" class="c_btn h27 gray">선택 상품 카테고리 삭제</a>

							<div class="dash_line"><!-- 점선라인 --></div>

							<?php
								include_once("_product_mass.inc_category.form.php");
							?>

							<div class="dash_line"><!-- 점선라인 --></div>

							<div class="tip_box">
								<?=_DescStr("반드시 원하는 카테고리를 먼저 선택한 다음 추가/삭제를 실행하시기 바랍니다.")?>
							</div>

						</td>
					</tr>
					<tr>
						<th>선택 상품 관리</th>
						<td>
								<a href='#none' onclick="selectDelete();" class='c_btn h27 gray'>선택 상품 삭제</a>
								<a href='#none' onclick="selectCategoryDelete();" class='c_btn h27 gray'>선택 상품 전체 카테고리 삭제</a>

						</td>
					</tr>
				</tbody>
			</table>

		</div>
		<script type="text/javascript">
			$(document).delegate('.detail_sc', 'click', function(e) {
				e.preventDefault();
				var mode2 = $('.sc_detail_mode').val();
				if(mode2 == 1) {
					$('.sc_detail_mode').val(2);
					$('.sc_detail').show();
					$(this).attr('title' , '상품 이동/복사/삭제 닫기').html('상품 이동/복사/삭제 닫기');
				}
				else {
					$('.sc_detail_mode').val(1);
					$('.sc_detail').hide();
					$(this).attr('title' , '상품 이동/복사/삭제 열기').html('상품 이동/복사/삭제 열기');
				}
			});
		</script>
		<!-- // 검색영역 -->







	<!-- ● 데이터 리스트 -->
	<div class="data_list" >


<form name=frm method=post action='_product_mass.pro.php' >
<input type=hidden name='_mode' value=''>
<input type=hidden name='_submode' value='mass_move'>
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
						<col width="60px"/><col width="*"/>
						<col width="*"/><col width="150px"/>
					</colgroup>
					<thead>
						<tr>
							<th scope="col" ><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
							<th scope="col">NO</th>
							<th scope="col">노출</th>
							<th scope="col">이미지</th>
							<th scope="col">상품코드/상품명</th>
							<th scope="col">카테고리</th>
							<th scope="col">관리</th>
						</tr>
					</thead>
					<tbody>


<?PHP

	//if(sizeof($res) < 1) echo "<tr><td colspan=10 height='40'>내용이 없습니다.</td></tr>";

	foreach($res as $k=>$v){

		$_num = $TotalCount - $count - $k ;

		$_p_img = get_img_src('thumbs_s_'.$v[p_img_list_square]);
		if($_p_img == '') $_p_img = OD_ADMIN_DIR.'/images/thumb_no.jpg';

		$_mod = "<a href='_product.form.php?_mode=modify&_code=" . $v[p_code] . "' class='c_btn h22 ' target='_blank'>수정</a>";
		$preview = "<a href='#none' onclick=\"window.open('/?pn=product.view&pcode=".$v[p_code]."')\" class='c_btn h22 '>미리보기</a>";



		$ex_coupon = explode("|" , $v['p_coupon']);



		// 상품 카테고리 정보 추출
		$arr_product_category_string = array();
		$app_product_category_string = "";
		$pct_que = "
			SELECT c.*
			FROM smart_product_category as pct
			INNER JOIN smart_category as c ON ( c.c_uid = pct.pct_cuid )
			WHERE
				pct.pct_pcode = '". $v['p_code'] ."'
		";
		$pct_res = _MQ_assoc($pct_que);
		foreach($pct_res as $pct_sk=>$pct_sv){
			$arr_tmp_string = array();
			if( $pct_sv['c_parent'] > 0 ){
				$ex = explode("," , $pct_sv['c_parent']);
				if($ex[0] > 0 ){$arr_tmp_string[] = $arr_category_name[$ex[0]];}
				if($ex[1] > 0 ){$arr_tmp_string[] = $arr_category_name[$ex[1]];}
			}
			$arr_tmp_string[] = $arr_category_name[$pct_sv['c_uid']];
			$arr_product_category_string[] = implode(" &gt; " , $arr_tmp_string);
		}
		if(sizeof($arr_product_category_string) > 0 ){
			$app_product_category_string = "<div style='both:clear; margin:5px;'>". implode("</div><div style='both:clear; margin:5px;'>" , $arr_product_category_string) ."</div>";
		}

		// 상품 카테고리 정보 추출


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
								<td style='text-align:left; margin-left:5px;'>". $app_product_category_string ."</td>
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
</form>





<script src="/include/js/jquery/jquery.ui.datepicker-ko.js"></script>
<SCRIPT>

	// - 옵션열기 ---
	function option_popup(pass_code , pass_mode) {
		window.open("_product_option.form.php?pass_mode="+pass_mode+"&pass_code=" + pass_code ,"","width=1064,height=500,scrollbars=yes");
	}


	// - 추가옵션열기 ---
	function addoption_popup(code) {
		window.open("_product_addoption.popup.php?pass_code=" + code,"addoption","width=1064,height=500,scrollbars=yes");
	}


	// 선택상품 삭제
	 function selectDelete() {
		 if($('.js_ck').is(":checked")){
			 if(confirm("정말 선택한 상품을 삭제 하시겠습니까?")){
				$("form[name=frm]").children("input[name=_mode]").val("mass_delete");
				// 상품관리의 상품처리 파일 이용하여 처리함.
				$("form[name=frm]").attr("action" , "_product.pro.php");
				document.frm.submit();
			 }
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }


	// 선택 상품의 전체 카테고리 제외
	 function selectCategoryDelete() {
		 if($('.js_ck').is(":checked")){
			 if(confirm("정말 선택한 상품의 전체 카테고리를 삭제 하시겠습니까?")){
				$("form[name=frm]").children("input[name=_mode]").val("mass_modify_category_delete");
				$("form[name=frm]").attr("action" , "_product_mass.pro.php");
				document.frm.submit();
			 }
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }


	// 선택 카테고리 갯수 체크
	 function selectCategoryCnt() {
		return $.ajax({
			url: "_product_mass.inc_category.pro.php",
			type: "POST",
			data: "_mode=cnt&_code=<?=$_tmpcode?>",
			async: false
		}).responseText;
	 }


	// 선택 상품에 선택 카테고리 추가
	 function selectCategoryAdd() {
		 if($('.js_ck').is(":checked")){
			var category_cnt = selectCategoryCnt(); // 갯수추출
			if(category_cnt > 0 )	{
				 if(confirm("정말로 선택 상품 카테고리 추가를 실행 하시겠습니까?")){
					$("form[name='frm']").children("input[name=_mode]").val("mass_modify_category_add");
					$("form[name='frm']").attr("action" , "_product_mass.pro.php");
					document.frm.submit();
				 }
			}
			else {
				alert('카테고리를 1개 이상 선택해 주시기 바랍니다.');
			}
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }


	// 선택 상품에 선택 카테고리 삭제
	 function selectCategorySelDel() {
		 if($('.js_ck').is(":checked")){
			var category_cnt = selectCategoryCnt(); // 갯수추출
			if(category_cnt > 0 )	{
				 if(confirm("정말로 선택 상품 카테고리 삭제를 실행 하시겠습니까?")){
					$("form[name=frm]").children("input[name=_mode]").val("mass_modify_category_seldel");
					$("form[name=frm]").attr("action" , "_product_mass.pro.php");
					document.frm.submit();
				 }
			}
			else {
				alert('카테고리를 1개 이상 선택해 주시기 바랍니다.');
			}
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }
</SCRIPT>
<?PHP
	include_once("wrap.footer.php");
?>