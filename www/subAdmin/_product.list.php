<?php
include_once('wrap.header.php');

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
		<!-- 해당페이지의 등록/업로드 버튼 있을 경우 -->
		<div class="btn_box">
			<a href="_product.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">상품등록</a>
		</div>
	</div>





	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<?php
		// 상품 관리 --- 검색폼 불러오기
		//			반드시 - s_query가 적용되어야 함.
		$s_query = " from smart_product as p where 1 and p.p_cpid = '{$com_id}' ";

		include_once(OD_ADMIN_ROOT.'/_product.inc_search.php');
		//	==> s_query 리턴됨.

		if(!$listmaxcount) $listmaxcount = 20;
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

		<form name="frm" method="post" action="" >
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
		<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
		<input type="hidden" name="_search" value="<?php echo enc('e', $s_query); ?>">

			<!-- ●리스트 컨트롤영역 -->
			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
					<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
					<a href="#none" onclick="selectDelete(); return false;" class="c_btn h27 gray">선택삭제</a>
				</div>
				<div class="right_box">

					<a href="#none" onclick="downloadExcel('select'); return false;" class="c_btn icon icon_excel">선택 엑셀다운로드</a>
					<a href="#none" onclick="downloadExcel('search'); return false;" class="c_btn icon icon_excel">검색 엑셀다운로드(<?php echo number_format($TotalCount); ?>)</a>

					<select class="h27" onchange="location.href=this.value;">
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_idx', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_idx' && $so == 'asc'?' selected':null); ?>>순위순 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_idx', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_idx' && $so == 'desc'?' selected':null); ?>>순위순 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_rdate' && $so == 'asc'?' selected':null); ?>>등록일 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_rdate' && $so == 'desc'?' selected':null); ?>>등록일 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_name', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_name' && $so == 'asc'?' selected':null); ?>>상품명 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_name', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_name' && $so == 'desc'?' selected':null); ?>>상품명 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_price', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_price' && $so == 'asc'?' selected':null); ?>>판매가 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_price', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_price' && $so == 'desc'?' selected':null); ?>>판매가 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_stock', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_stock' && $so == 'asc'?' selected':null); ?>>재고량 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_stock', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_stock' && $so == 'desc'?' selected':null); ?>>재고량 ▼</option>
					</select>

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
					<col width="40"><col width="70"><col width="70"><col width="140"><col width="90"><col width="*"><col width="100"><col width="80"><col width="90"><col width="160">
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th scope="col">NO</th>
						<th scope="col">노출여부</th>
						<th scope="col">상품코드</th>
						<th scope="col">이미지</th>
						<th scope="col">상품명</th>
						<th scope="col">판매가</th>
						<th scope="col">재고량</th>
						<th scope="col">등록일</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
					<?php
					 if(sizeof($res) > 0){
						foreach($res as $k=>$v){

							$_mod = '<a href="_product.form.php?_mode=modify&_code=' . $v['p_code'] . '&_PVSC=' . $_PVSC . '" class="c_btn h22 ">수정</a>';
							$_del = '<a href="#none" onclick="del(\'_product.pro.php?_mode=delete&_code=' . $v['p_code'] . '&_PVSC=' . $_PVSC . '\');" class="c_btn h22 gray">삭제</a>';
							$preview = '<a href="#none" onclick="window.open(\'/?pn=product.view&pcode='.$v['p_code'].'\')" class="c_btn h22 ">미리보기</a>';

							$_num = $TotalCount - $count - $k ;

							// 이미지 체크
							$_p_img = get_img_src('thumbs_s_'.$v['p_img_list_square']);
							if($_p_img == '') $_p_img = OD_ADMIN_DIR.'/images/thumb_no.jpg';
					?>
							<tr>
								<td>
									<label class="design"><input type="checkbox" name="chk_pcode[<?php echo $v['p_code']; ?>]" class="js_ck" value="Y"></label>
								</td>
								<td><?php echo $_num; ?></td>
								<td>
									<?php echo $arr_adm_button[($v['p_view'] == 'Y' ? '노출' : '숨김')]; ?>
								</td>
								<td><?php echo $v['p_code']; ?></td>
								<td class="img80">
									<img src="<?php echo $_p_img; ?>" alt="<?php echo addslashes(strip_tags($v['p_name'])); ?>" >
								</td>
								<td class="t_left t_black">
									<?php
										// JJC ::: 브랜드관리 ::: 2017-11-03
										echo ($arr_brand[$v['p_brand']] ? "<span style='color:#008aff;'>Brand : ".$arr_brand[$v['p_brand']] . "</span><br>" : "") ;
									?>
									<?php echo strip_tags($v['p_name']); ?>
								</td>
								<td class="t_black"><?php echo number_format($v['p_price']); ?>원</td>
								<td><?php echo number_format($v['p_stock']); ?></td>
								<td><?php echo date('Y.m.d' , strtotime($v['p_rdate'])); ?></td>
								<td>
									<div class="lineup-vertical">
										<?php echo $_mod; ?>
										<?php echo $_del; ?>
										<?php echo $preview; ?>
									</div>
								</td>
							</tr>
					<?php
						}
					}
					?>
				</tbody>
			</table>

			<?php if(sizeof($res) < 1){ ?>
				<!-- 내용없을경우 -->
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
			<?php } ?>

		</form>

	</div>
	<!-- / 데이터 리스트 -->

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
	</div>

	<script>
		// 상품 일괄업로드 폼 열기/닫기
		$(document).delegate('.js_open_excel_box', 'click', function(){
			$('.js_excel_box').toggle(); return false;
		});
		 // 선택삭제
		 function selectDelete() {
			 if($('.js_ck').is(":checked")){
				 if(confirm("정말 삭제하시겠습니까?")){
					$("form[name=frm]").children("input[name=_mode]").val("mass_delete");
					$("form[name=frm]").attr("action" , "_product.pro.php");
					document.frm.submit();
				 }
			 }
			 else {
				 alert('1개 이상 선택해 주시기 바랍니다.');
			 }
		 }
		// 순위조정 up-down-top-bottom
		 function sort_up(pcode,mode,query) {
			<?php if($st  == 'p_idx' && $so == 'asc' ){ ?>
				common_frame.location.href='_product.sort.php?pcode='+pcode+'&_mode='+mode+'&query='+query;
			<?php }else{ ?>
				alert('순위조정은 정렬상태가 "노출순위 ▲"인 상태에서만 조정할 수 있습니다,');
			<?php } ?>
		}
		// 순위그룹 수정
		function sort_group(pcode){
			var group = $('.sort_group_'+ pcode).val()*1;
			if(group <= 0){
				alert('상품 순위를 입력해 주시기 바랍니다.');
				$('.sort_group_'+ pcode).focus();
				return false;
			}
			common_frame.location.href='_product.sort.php?pcode='+pcode+'&_mode=modify_group&_group='+group;
		}
		// 선택순위그룹 수정
		 function selectSortModify() {
			 if($('.js_ck').is(':checked')){
					$('form[name=frm]').attr({'action':'_product.sort.php' , 'target':'common_frame'});
					$('input[name=_mode]').val('mass_sort');
					document.frm.submit();
			 }
			 else {
				 alert('1개 이상 선택해 주시기 바랍니다.');
			 }
		 }
		// 선택엑셀 다운로드
		function downloadExcel(_mode){
			if(_mode == 'select' && $('.js_ck').is(":checked") === false){
				alert('1개 이상 선택해 주시기 바랍니다.');
				return false;
			}

			$("form[name=frm]").children("input[name=_mode]").val(_mode);
			$("form[name=frm]").attr("action" , "_product.download.php");
			$("form[name=frm]").attr("target" , "_self");
			document.frm.submit();
			return true;
		}
		// 검색엑셀 다운로드
	</script>


<?php include_once('wrap.footer.php'); ?>