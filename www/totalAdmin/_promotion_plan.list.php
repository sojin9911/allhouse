<?php

	include_once('wrap.header.php');

	// 넘길 변수 설정하기
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {if(is_array($val)) {foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }}else {$_PVS .= "&$key=$val";}}
	$_PVSC = enc('e' , $_PVS);
	// 넘길 변수 설정하기



	######## 검색 체크

	$pass_sort = $pass_sort ? $pass_sort : 'rdate_desc';
	$pass_limit = $pass_limit ? $pass_limit : 20;

	$s_query = " from smart_promotion_plan where 1 ";

	if( $pass_title !="" ) { $s_query .= " and pp_title like '%${pass_title}%' "; }
	if( $pass_view !="" ) { $s_query .= " and pp_view='${pass_view}' "; }

	if( $pass_sdate && $pass_edate ) { $s_query .= " AND left(pp_rdate,10) between '". $pass_sdate ."' and '". $pass_edate ."' "; }// - 진행기간
	else if( $pass_sdate ) { $s_query .= " AND left(pp_rdate,10) >= '". $pass_sdate ."' "; }
	else if( $pass_edate ) { $s_query .= " AND left(pp_rdate,10) <= '". $pass_edate ."' "; }

	if(!$listmaxcount) $listmaxcount = 20;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'p_rdate';
	if(!$so) $so = 'desc';
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스


	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);


	$s_orderby = "ORDER BY pp_uid DESC";
	switch($pass_sort){
		case "title_asc": $s_orderby = "ORDER BY pp_title ASC, pp_uid DESC"; break;//기획전명순↑
		case "title_desc": $s_orderby = "ORDER BY pp_title DESC, pp_uid DESC"; break;//기획전명순↓
		case "rdate_asc": $s_orderby = "ORDER BY pp_rdate ASC"; break;//등록일순↑
		case "rdate_desc": $s_orderby = "ORDER BY pp_rdate DESC"; break;//등록일순↓
	}

	$res = _MQ_assoc(" SELECT * $s_query $s_orderby  LIMIT $count , $listmaxcount ");

?>


	<!-- ● 단락타이틀 -->
	<div class="group_title">
		<strong>기획전검색</strong>
		<!-- 해당페이지의 등록/업로드 버튼 있을 경우 -->
		<div class="btn_box">
			<a href="_promotion_plan.form.php?_mode=add" class="c_btn h46 red" accesskey="a">기획전등록</a>
		</div>

	</div>





	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<div class="data_form if_search">


<form name="searchfrm" method="get" action="<?php echo $_SERVER["PHP_SELF"]?>">
<input type="hidden" name="mode" value="search">
<input type="hidden" name="st" value="<?php echo $st; ?>">
<input type="hidden" name="so" value="<?php echo $so; ?>">
<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
<!-- <input type="hidden" name="pass_sort" value="<?=$pass_sort?>">
<input type="hidden" name="pass_limit" value="<?=$pass_limit?>"> -->

			<!-- 폼테이블 2단 -->
			<table class="table_form">
				<colgroup>
					<col width="140"/><col width="*"/><col width="140"/><col width="*"/><col width="140"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>

						<th>기획전명</th>
						<td><input type="text" name="pass_title" class="design" style="" value="<?php echo $pass_title; ?>"></td>

						<th>진행기간</th>
						<td >
							<input type="text" name="pass_sdate" value="<?=$pass_sdate?>" class="design" style="width:85px" readonly>
							<span class="fr_tx">-</span>
							<input type="text" name="pass_edate" value="<?=$pass_sdate?>" class="design" style="width:85px" readonly>
						</td>

						<th>노출여부</th>
						<td><?php echo _InputRadio( "pass_view" , array('', 'Y','N'), $pass_view , "" , array('전체', '노출','숨김') ); ?></td>

					</tr>
				</tbody>
			</table>
			<!-- 폼테이블 2단 -->


			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
					<span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"></span>
					<?php if($mode == 'search'){ ?>
						<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>

</form>


	</div>



	<!-- ● 데이터 리스트 -->
	<div class="data_list">


		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
				<a href="#none" onclick="selectDelete(); return false;" class="c_btn h27 gray">선택삭제</a>
			</div>
			<div class="right_box">

				<?php

					// SSJ : 2017-11-23 아래방식으로 통일
					//$arr_sort = array('title_asc' => '기획전명순↑' , 'title_desc' => '기획전명순↓', 'rdate_asc' => '등록일순↑' , 'rdate_desc' => '등록일순↓');
					//echo _InputSelect( "select_sort" , array_keys($arr_sort) , $pass_sort , "  class='h27' onchange='search_sort_limit();' " , array_values($arr_sort) , "-선택-");
					//
					//$arr_limit = array('20' => '20개씩' , '50' => '50개씩', '100' => '100개씩');
					//echo _InputSelect( "select_limit" , array_keys($arr_limit) , $pass_limit , "  class='h27' onchange='search_sort_limit();' " , array_values($arr_limit) , "-선택-");

				?>

				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pp_title', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'pp_title' && $so == 'asc'?' selected':null); ?>>기획전명 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pp_title', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'pp_title' && $so == 'desc'?' selected':null); ?>>기획전명 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pp_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'pp_rdate' && $so == 'asc'?' selected':null); ?>>등록일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pp_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'pp_rdate' && $so == 'desc'?' selected':null); ?>>등록일 ▼</option>
				</select>
				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
				</select>

			</div>
		</div>
		<!-- / 리스트 컨트롤영역 -->



		<form name="frm" method="post" action="_promotion_plan.mass_form.php" >
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
		<input type="hidden" name="_search" value="<?php echo enc('e', $s_query); ?>">

			<table class="table_list">
				<colgroup>
					<col width="40"><col width="70"><col width="80"><col width="80"><col width="80"><col width="*"><col width="200"><col width="100"><col width="160">
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th scope="col">NO</th>
						<th scope="col">노출여부</th>
						<th scope="col">진행상태</th>
						<th scope="col">이미지</th>
						<th scope="col">기획전명</th>
						<th scope="col">기획전기간</th>
						<th scope="col">등록일</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
				<?php
					if(sizeof($res) > 0){
						foreach($res as $k=>$v){

							$_mod = "<a href='_promotion_plan.form.php?_mode=modify&uid=" . $v['pp_uid'] . "&_PVSC=" . $_PVSC . "' class='c_btn h22 '>수정</a>";
							$_del = "<a href='#none' onclick='del(\"_promotion_plan.pro.php?_mode=delete&uid=" . $v['pp_uid'] . "&_PVSC=" . $_PVSC . "\");' class='c_btn h22 gray'>삭제</a>";
							$_preview = "<a href='/?pn=product.promotion_view&uid=".$v['pp_uid']."' target='_blank' class='c_btn h22 '>미리보기</a>";

							$_num = $TotalCount - $count - $k ;

							// 목록 이미지
							$_img = IMG_DIR_BANNER.$v['pp_img'];
							if(file_exists($_SERVER['DOCUMENT_ROOT'].$_img)) $app_img = '<img src="'.$_img.'" class="js_thumb_img" data-img="'.$_img.'" alt="'.addslashes($_title).'">';
							else $app_img = '';

							// 진행상태
							$app_status = '';
							//종료후
							if($v['pp_edate']<DATE('Y-m-d')) {
								$app_status = '<span class="c_tag gray h22 t4">진행종료</span>';// 종료문구
							}
							//시작전
							else if($v['pp_sdate']>DATE('Y-m-d')) {
								$app_status = '<span class="c_tag sky h22 t4">D-'. fn_date_diff($v['pp_sdate'],DATE("Y-m-d")) .'</span>';
							}
							//진행중
							else {
								$app_status = '<span class="c_tag green h22 t4">진행중</span>';
							}

					?>
							<tr>
								<td>
									<label class="design"><input type="checkbox" name="chk_pcode[<?php echo $v['pp_uid']; ?>]" class="js_ck" value="Y"></label>
								</td>
								<td><?php echo $_num; ?></td>
								<td>
									<div class="lineup-center">
										<?php echo $arr_adm_button[($v['pp_view'] == 'Y' ? '노출' : '숨김')]; ?>
									</div>
								</td>
								<td>
									<div class="lineup-center">
										<?php echo $app_status; ?>
									</div>
								</td>
								<td class="img80"><?php echo $app_img; ?></td>
								<td class="t_left t_black"><?php echo stripslashes(strip_tags($v['pp_title'])); ?></td>
								<td><?php echo date('Y.m.d' , strtotime($v['pp_sdate'])); ?> ~ <?php echo date('Y.m.d' , strtotime($v['pp_edate'])); ?></td>
								<td><?php echo date('Y.m.d' , strtotime($v['pp_rdate'])); ?></td>
								<td>
									<div class="lineup-vertical">
										<?php echo $_mod; ?>
										<?php echo $_del; ?>
										<?php echo $_preview; ?>
									</div>
									<div class="lineup-vertical">
										<!-- KAY :: 2021-06-10 :: 에디터이미지관리 버튼생성-->
										<a href="#none" onclick="edit_img_pop('<?php echo $v['pp_uid'] ?>')" class="c_btn h22 green">이미지 관리</a>
									</div>
								</td>
							</tr>
					<?php
						}
					}
					?>
				</tbody>
			</table>

		</form>

		<?php if(sizeof($res) < 1){ ?>
			<!-- 내용없을경우 -->
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<?php } ?>

	</div>
	<!-- / 데이터 리스트 -->

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>
	</div>



	<script>

		 // 선택삭제
		 function selectDelete() {
			 if($('.js_ck').is(":checked")){
				 if(confirm("정말 삭제하시겠습니까?")){
					$("form[name=frm]").children("input[name=_mode]").val("mass_delete");
					$("form[name=frm]").attr("action" , "_promotion_plan.pro.php");
					document.frm.submit();
				 }
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
			$("form[name=frm]").attr("action" , "_promotion_plan.download.php");
			$("form[name=frm]").attr("target" , "_self");
			document.frm.submit();
			return true;
		}
		// 검색엑셀 다운로드

		// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기
		function edit_img_pop(_uid, table='promotion'){
			window.open('_config.editor_img.pop.php?_uid='+_uid+'&tn='+table+'','editimg','width=1120,height=600,scrollbars=yes');
		}
		// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기

		// SSJ : 2017-11-23 방식 변경
		//// sort / limit 변경 처리 함수
		//function search_sort_limit(){
		//	$("form[name='searchfrm']").children("input[name='pass_sort']").val( $("select[name='select_sort']").val() );
		//	$("form[name='searchfrm']").children("input[name='pass_limit']").val( $("select[name='select_limit']").val() );
		//	document.searchfrm.submit();
		//}

	</script>

	<link rel='stylesheet' href='/include/js/jquery/jqueryui/jquery-ui.min.css' type=text/css>
	<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
	<script src="/include/js/jquery/jqueryui/jquery.ui.datepicker-ko.js"></script>
	<script>
		$(function() {
			$("input[name='pass_sdate']").datepicker({changeMonth: true, changeYear: true });
			$("input[name='pass_sdate']").datepicker( "option", "dateFormat", "yy-mm-dd" );
			$("input[name='pass_sdate']").datepicker( "option",$.datepicker.regional["ko"] );

			$("input[name='pass_edate']").datepicker({changeMonth: true, changeYear: true });
			$("input[name='pass_edate']").datepicker( "option", "dateFormat", "yy-mm-dd" );
			$("input[name='pass_edate']").datepicker( "option",$.datepicker.regional["ko"] );
		});
	</script>

<?php
	include_once('wrap.footer.php');
?>
