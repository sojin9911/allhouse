<?php
	include_once('wrap.header.php');

	$_PVS = ''; // 링크 넘김 변수
	foreach(array_filter(array_unique(array_merge($_GET , $_POST))) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);

	// 상품아이콘에 자동적용아이콘 추가
	$arr_product_icon_type2 = array_merge(array('product_coupon_small_icon'=>'자동적용 아이콘 - 상품쿠폰 ( 40 x 20 )', 'product_freedelivery_small_icon'=>'자동적용 아이콘 - 무료배송 ( 40 x 20 )', 'product_promotion_small_icon'=>'자동적용 아이콘 - 기획전 ( 40 x 20 )'), $arr_product_icon_type);

	// 검색 체크
	$s_query = " where 1 ";
	if( $pass_type !="" ) { $s_query .= " and pi_type='${pass_type}' "; }
	if( $pass_title !="" ) { $s_query .= " and pi_title like '%${pass_title}%' "; }

	if(!$listmaxcount) $listmaxcount = 20;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'pi_idx';
	if(!$so) $so = 'asc';
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from smart_product_icon $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * from smart_product_icon {$s_query} order by if(pi_type='product_name_small_icon',1,2) asc,  {$st} {$so} , pi_uid desc limit $count , $listmaxcount ");
?>



	<!-- ● 단락타이틀 -->
	<div class="group_title">
		<strong>상품 아이콘 검색</strong>
		<div class="btn_box"><a href="_product_icon.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">상품 아이콘 등록</a></div>
	</div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<div class="data_form if_search">

		<form name="searchfrm" method="get" action="<?php echo $PHP_SELF; ?>" autocomplete="off">
		<input type="hidden" name="mode" value="search">
		<input type="hidden" name="st" value="<?php echo $st; ?>">
		<input type="hidden" name="so" value="<?php echo $so; ?>">
		<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">

			<!-- 폼테이블 2단 -->
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*"><col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>아이콘 구분</th>
						<td><?php echo _InputSelect( 'pass_type' , array_keys($arr_product_icon_type2) , $pass_type , "" , array_values($arr_product_icon_type2) , '-선택-'); ?></td>
						<th>아이콘 타이틀</th>
						<td><input type="text" name="pass_title" class="design" style="" value="<?php echo $pass_title; ?>"></td>
					</tr>
				</tbody>
			</table>
			<!-- 폼테이블 2단 -->


			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
					<li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"/></span><!-- <a href="" class="c_btn h34 black ">검색</a> --></li>
					<?php if($mode == 'search'){ ?>
						<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>

		</form>

	</div>



	<!-- ● 데이터 리스트 -->
	<div class="data_list">

		<form name="frm" method="post" action="_product_icon.pro.php" >
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">

			<!-- ●리스트 컨트롤영역 -->
			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
					<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
					<a href="#none" onclick="selectDelete(); return false;" class="c_btn h27 gray">선택삭제</a>
				</div>
			</div>
			<!-- / 리스트 컨트롤영역 -->

			<table class="table_list">
				<colgroup>
					<col width="40"><col width="70"><col width="120"><col width="250"><col width="*"><col width="160"><col width="160"><col width="90"><col width="160">
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th scope="col">NO</th>
						<th scope="col">순위</th>
						<th scope="col">구분</th>
						<th scope="col">아이콘 타이틀</th>
						<th scope="col">아이콘 이미지(PC)</th>
						<th scope="col">아이콘 이미지(MOBILE)</th>
						<th scope="col">등록일</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
					<?PHP
					if(count($res) >  0) {
						foreach($res as $k=>$v) {

							$_mod = '<a href="#none" onclick="location.href=(\'_product_icon.form.php' . URI_Rebuild('?', array('_mode'=>'modify', '_uid'=>$v['pi_uid'], '_PVSC'=>$_PVSC)) .'\');" class="c_btn h22 ">수정</a>';
							$_del = '<a href="#none" onclick="del(\'_product_icon.pro.php'. URI_Rebuild('?', array('_mode'=>'delete', '_uid'=>$v['pi_uid'], '_PVSC'=>$_PVSC)) .'\');" class="c_btn h22 gray">삭제</a>';
							if(!in_array($v['pi_type'],array_keys($arr_product_icon_type))){
								$_del = '';
							}

							$_num = $TotalCount - $count - $k ;

							// 이미지 검사
							if($v['pi_img'] && file_exists('..'.IMG_DIR_ICON . $v['pi_img'])){
								$app_icon = '<img src="'. get_img_src($v['pi_img'], IMG_DIR_ICON) .'" alt="'. addslashes(strip_tags($v['pi_title'])) .'" style="max-width:80px;">';
							}else{
								$app_icon = '<div class="lineup-center">'. _DescStr('미등록') .'</div>';
							}

							// 이미지 검사
							if($v['pi_img_m'] && file_exists('..'.IMG_DIR_ICON . $v['pi_img_m'])){
								$app_icon_m = '<img src="'. get_img_src($v['pi_img_m'], IMG_DIR_ICON) .'" alt="'. addslashes(strip_tags($v['pi_title'])) .'" style="max-width:80px;">';
							}else{
								$app_icon_m = '<div class="lineup-center">'. _DescStr('미등록') .'</div>';
							}

					?>
							<tr>
								<td><label class="design"><input type="checkbox" name="chk_uid[]" <?php echo (!in_array($v['pi_type'],array_keys($arr_product_icon_type)) ? ' disabled ' : ' class="js_ck" ')?> value="<?php echo $v['pi_uid']; ?>"></label></td>
								<td><?php echo $_num; ?></td>
								<td>
									<?php if(in_array($v['pi_type'],array_keys($arr_product_icon_type))){ ?>
										<div class="lineup-center" style="">
											<input type="text" name="" value="<?php echo $v['pi_idx']; ?>" class="design number_style js_sort_uid_<?php echo $v['pi_uid']; ?>" placeholder="" style="width:45px;margin-right:0;">
											<a href="#none" onclick="sort_index('<?php echo $v['pi_uid']; ?>'); return false;" class="c_btn h27 " style="width:45px;">수정</a>
										</div>
									<?php }else{ echo '자동적용'; } ?>
								</td>
								<td><?php echo $arr_product_icon_type2[$v['pi_type']]; ?></td>
								<td class="t_left t_black"><?php echo stripslashes($v['pi_title']); ?></td>
								<td><?php echo $app_icon; ?></td>
								<td><?php echo $app_icon_m; ?></td>
								<td><?php echo date('Y.m.d' , strtotime($v['pi_rdate'])); ?></td>
								<td>
									<div class="lineup-vertical">
										<?php echo $_mod; ?>
										<?php echo $_del; ?>
									</div>
								</td>
							</tr>
					<?php
						}
					}
					?>
				</tbody>
			</table>


			<?php if(count($res) < 1) {  ?>
				<!-- 내용없을경우 -->
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
			<?php } ?>

		</form>

	</div>

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
	</div>

	<script>
		// 선택삭제
		function selectDelete() {
			if($('.js_ck').is(":checked")){
				if(confirm('정말 삭제하시겠습니까?')){
					$('form[name=frm]').children('input[name=_mode]').val('mass_delete');
					$('form[name=frm]').attr('action' , '_product_icon.pro.php');
					document.frm.submit();
				}
			}
			else {
				alert('1개 이상 선택해 주시기 바랍니다.');
			}
		}

		// 순위수정
		function sort_index(_uid){
			var _idx = $('.js_sort_uid_' + _uid).val();
			if(_uid && _idx){
				document.location.href = '_product_icon.pro.php?_mode=sort&_uid=' + _uid +'&_idx=' + _idx;
			}
		}
	</script>


<?php include_once('wrap.footer.php'); ?>