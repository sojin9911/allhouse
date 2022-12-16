<?php
	// SSJ : 2017-12-20 상품 상세페이지 노출 설정
	include_once('wrap.header.php');

	// 노출항목 설정 추출
	$ex_display_pc = array_filter(array_unique(explode('|', $siteInfo['s_display_pinfo_pc'])));
	$ex_display_mo = array_filter(array_unique(explode('|', $siteInfo['s_display_pinfo_mo'])));
	// 전체항목에 노출될 항목 추출
	$arr_display_pc = array_diff(array_keys($arrDisplayPinfo), $ex_display_pc);
	$arr_display_mo = array_diff(array_keys($arrDisplayPinfo), $ex_display_mo);

	// 추가 노출항목 설정 추출
	$ex_display_add = array_filter(array_unique(explode('|', $siteInfo['s_display_pinfo_add'])));
	$ex_display_add = (count($arrDisplayPinfoAdd) > 1 ? $ex_display_add : $siteInfo['s_display_pinfo_add']);


	// 스킨정보 추출
	$SkinInfo = SkinInfo();

?>
<style>
.category .after .set_before {display:none;}
.category .after .set_after {display:inline-block;}
.category .before .set_before {display:inline-block;}
.category .before .set_after {display:none;}
</style>


<form name="frm" method="post" action="_config.display.pinfo.pro.php" enctype="multipart/form-data" >
<input type="hidden" name="_mode" value='<?php echo $_mode; ?>'>
<input type="hidden" name="_uid" value='<?php echo $_uid; ?>'>
<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">

	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>노출 항목 설정</strong></div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">


		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>노출 항목</th>
					<td>
						<!-- ● 내부탭 -->
						<div class="c_tab">
							<ul>
								<li class="hit"><a href="#none" class="btn tab_menu" data-idx="pc"><strong>노출항목(PC)</strong></a></li>
								<li><a href="#none" class="btn tab_menu" data-idx="mobile"><strong>노출항목(MOBILE)</strong></a></li>
							</ul>
							<label class="design"><input type="checkbox" name="s_display_pinfo_mo_use_pc" value="Y" <?php echo ($siteInfo['s_display_pinfo_mo_use_pc'] == 'Y' ? 'checked' : null); ?>>PC/MOBILE 노출항목 동일 적용</label>
						</div>

						<div class="tab_conts" data-idx="pc">
							<div class="category" style="margin:0;">
								<ul class="table">
									<li class="td">
										<div class="depth_tt"><span class="lineup"><strong>전체항목</strong><a href="#none" style="" class="c_btn h27 icon icon_plus_b js_diplay_pinfo_add_all">전체추가</a></div>
										<div class="inner_box">

											<table class="category_list before">
												<colgroup>
													<col width="*"><col width="70">
												</colgroup>
												<tbody>
													<?php foreach($arr_display_pc as $k=>$v){ ?>
														<tr>
															 <td class="t_left ctg_name" style="">
																<span class="fr_tx"><?php echo $arrDisplayPinfo[$v]; ?></span>
																<input type="hidden" name="s_display_pinfo_pc[]" value="<?php echo $v; ?>" disabled>
															</td>
															<td>
																<a href="#none" class="c_btn h22 icon_up js_diplay_pinfo_up set_after" title="위로"></a>
																<a href="#none" class="c_btn h22 icon_down js_diplay_pinfo_down set_after" title="아래로"></a>
																<a href="#none" class="c_btn h22 t2 js_diplay_pinfo_del set_after">삭제</a>
																<a href="#none" class="c_btn h22 t3 js_diplay_pinfo_add set_before">+ 추가</a>
															</td>
														</tr>
													<?php } ?>
												</tbody>
											</table>

											<div class="common_none" style="margin-top:110px;<?php echo (count($arr_display_pc)>0?'display:none':null); ?>"><div class="no_icon"></div><div class="gtxt">모든 항목이 노출항목에 추가되었습니다.</div></div>

										</div>
									</li>
									<li class="td">
										<div class="depth_tt"><span class="lineup"><strong>노출항목</strong><a href="#none" style="" class="c_btn h27 icon icon_minus_b js_diplay_pinfo_del_all">전체삭제</a></span></div>
										<div class="inner_box">

											<table class="category_list after">
												<colgroup>
													<col width="*"><col width="105">
												</colgroup>
												<tbody>
													<?php foreach($ex_display_pc as $k=>$v){ ?>
														<tr>
															 <td class="t_left ctg_name" style="">
																<span class="fr_tx"><?php echo $arrDisplayPinfo[$v]; ?></span>
																<input type="hidden" name="s_display_pinfo_pc[]" value="<?php echo $v; ?>">
															</td>
															<td>
																<a href="#none" class="c_btn h22 icon_up js_diplay_pinfo_up set_after" title="위로"></a>
																<a href="#none" class="c_btn h22 icon_down js_diplay_pinfo_down set_after" title="아래로"></a>
																<a href="#none" class="c_btn h22 t2 js_diplay_pinfo_del set_after">삭제</a>
																<a href="#none" class="c_btn h22 t3 js_diplay_pinfo_add set_before">+ 추가</a>
															</td>
														</tr>
													<?php } ?>
												</tbody>
											</table>

											<div class="common_none" style="margin-top:110px;<?php echo (count($arr_display_pc)<>count($arrDisplayPinfo)?'display:none':null); ?>"><div class="no_icon"></div><div class="gtxt">노출항목에 추가된 항목이 없습니다.</div></div>

										</div>
									</li>
								</ul>
							</div>
						</div>

						<div class="tab_conts" data-idx="mobile" style="display:none">
							<div class="category" style="margin:0;">
								<ul class="table">
									<li class="td">
										<div class="depth_tt"><span class="lineup"><strong>전체항목</strong><a href="#none" style="" class="c_btn h27 icon icon_plus_b js_diplay_pinfo_add_all">전체추가</a></div>
										<div class="inner_box">

											<table class="category_list before">
												<colgroup>
													<col width="*"><col width="70">
												</colgroup>
												<tbody>
													<?php foreach($arr_display_mo as $k=>$v){ ?>
														<tr>
															 <td class="t_left ctg_name" style="">
																<span class="fr_tx"><?php echo $arrDisplayPinfo[$v]; ?></span>
																<input type="hidden" name="s_display_pinfo_mo[]" value="<?php echo $v; ?>" disabled>
															</td>
															<td>
																<a href="#none" class="c_btn h22 icon_up js_diplay_pinfo_up set_after" title="위로"></a>
																<a href="#none" class="c_btn h22 icon_down js_diplay_pinfo_down set_after" title="아래로"></a>
																<a href="#none" class="c_btn h22 t2 js_diplay_pinfo_del set_after">삭제</a>
																<a href="#none" class="c_btn h22 t3 js_diplay_pinfo_add set_before">+ 추가</a>
															</td>
														</tr>
													<?php } ?>
												</tbody>
											</table>

											<div class="common_none" style="margin-top:110px;<?php echo (count($arr_display_mo)>0?'display:none':null); ?>"><div class="no_icon"></div><div class="gtxt">모든 항목이 노출항목에 추가되었습니다.</div></div>

										</div>
									</li>
									<li class="td">
										<div class="depth_tt"><span class="lineup"><strong>노출항목</strong><a href="#none" style="" class="c_btn h27 icon icon_minus_b js_diplay_pinfo_del_all">전체삭제</a></span></div>
										<div class="inner_box">

											<table class="category_list after">
												<colgroup>
													<col width="*"><col width="105">
												</colgroup>
												<tbody>
													<?php foreach($ex_display_mo as $k=>$v){ ?>
														<tr>
															 <td class="t_left ctg_name" style="">
																<span class="fr_tx"><?php echo $arrDisplayPinfo[$v]; ?></span>
																<input type="hidden" name="s_display_pinfo_mo[]" value="<?php echo $v; ?>">
															</td>
															<td>
																<a href="#none" class="c_btn h22 icon_up js_diplay_pinfo_up set_after" title="위로"></a>
																<a href="#none" class="c_btn h22 icon_down js_diplay_pinfo_down set_after" title="아래로"></a>
																<a href="#none" class="c_btn h22 t2 js_diplay_pinfo_del set_after">삭제</a>
																<a href="#none" class="c_btn h22 t3 js_diplay_pinfo_add set_before">+ 추가</a>
															</td>
														</tr>
													<?php } ?>
												</tbody>
											</table>

											<div class="common_none" style="margin-top:110px;<?php echo (count($arr_display_mo)<>count($arrDisplayPinfo)?'display:none':null); ?>"><div class="no_icon"></div><div class="gtxt">노출항목에 추가된 항목이 없습니다.</div></div>

										</div>
									</li>
								</ul>
							</div>
						</div>

					</td>
				</tr>
				<tr>
					<th>노출항목 추가 설정</th>
					<td>
						<?php echo _InputCheckBox('s_display_pinfo_add', array_keys($arrDisplayPinfoAdd), $ex_display_add, '', array_values($arrDisplayPinfoAdd)); ?>
					</td>
				</tr>
			</tbody>
		</table>

	</div>



	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>상세페이지 내 관련상품 진열 설정</strong></div>

	<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>(PC) 상품 노출</th>
					<td>
						<?php echo _InputRadio('s_display_relation_pc_use', array('Y', 'N'), $siteInfo['s_display_relation_pc_use'], '', array('노출', '숨김')); ?>
						<?php echo _DescStr('슬라이드 롤링 방식으로 노출됩니다.'); ?>
					</td>
					<th>(모바일) 상품 노출</th>
					<td>
						<?php echo _InputRadio('s_display_relation_mo_use', array('Y', 'N'), $siteInfo['s_display_relation_mo_use'], '', array('노출', '숨김')); ?>
						<?php echo _DescStr('슬라이드 롤링 방식으로 노출됩니다.'); ?>
					</td>
				</tr>
				<tr>
					<th>(PC)상품 진열 설정<!-- <br><span class="normal">- 슬라이드 방식 -</span> --></th>
					<td>
						<label class="type if_setting">
							<span class="img"><img src="images/type_<?php echo $SkinInfo['product']['config_relative_cnt']['pc'][0]; ?>x1.gif" alt=""></span>
							<span class="tx">
								<input type="radio" name="s_display_relation_pc_col" value="0" <?php echo ($siteInfo['s_display_relation_pc_col']=='0' ? 'checked' : null); ?> class="js_relation_type">
								<span class="fr_tx"><?php echo $SkinInfo['product']['config_relative_cnt']['pc'][0]; ?> x </span>
								<select name="s_display_relation_pc_row" <?php echo ($siteInfo['s_display_relation_pc_col']<>'0' ? 'class="disabled" disabled' : null); ?>>
									<?php for($i=1; $i<=10; $i++){ ?>
										<option value="<?php echo $i; ?>" <?php echo ($siteInfo['s_display_relation_pc_col']=='0' && $siteInfo['s_display_relation_pc_row'] == $i ? 'selected' : null); ?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</span>
						</label>
						<label class="type if_setting">
							<span class="img"><img src="images/type_<?php echo $SkinInfo['product']['config_relative_cnt']['pc'][1]; ?>x1.gif" alt=""></span>
							<span class="tx">
								<input type="radio" name="s_display_relation_pc_col" value="1" <?php echo ($siteInfo['s_display_relation_pc_col']=='1' ? 'checked' : null); ?> class="js_relation_type">
								<span class="fr_tx"><?php echo $SkinInfo['product']['config_relative_cnt']['pc'][1]; ?> x </span>
								<select name="s_display_relation_pc_row" <?php echo ($siteInfo['s_display_relation_pc_col']<>'1' ? 'class="disabled" disabled' : null); ?>>
									<?php for($i=1; $i<=10; $i++){ ?>
										<option value="<?php echo $i; ?>" <?php echo ($siteInfo['s_display_relation_pc_col']=='1' && $siteInfo['s_display_relation_pc_row'] == $i ? 'selected' : null); ?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</span>
						</label>
					</td>
					<th>(모바일)상품 진열 설정<!-- <br><span class="normal">- 슬라이드 방식 -</span> --></th>
					<td>
						<label class="type if_setting">
							<span class="img"><img src="images/type_<?php echo $SkinInfo['product']['config_relative_cnt']['mo'][0]; ?>x1.gif" alt=""></span>
							<span class="tx">
								<input type="radio" name="s_display_relation_mo_col" value="0" <?php echo ($siteInfo['s_display_relation_mo_col']=='0' ? 'checked' : null); ?> class="js_relation_type">
								<span class="fr_tx"><?php echo $SkinInfo['product']['config_relative_cnt']['mo'][0]; ?> x </span>
								<select name="s_display_relation_mo_row" <?php echo ($siteInfo['s_display_relation_mo_col']<>'0' ? 'class="disabled" disabled' : null); ?>>
									<?php for($i=1; $i<=10; $i++){ ?>
										<option value="<?php echo $i; ?>" <?php echo ($siteInfo['s_display_relation_mo_col']=='0' && $siteInfo['s_display_relation_mo_row'] == $i ? 'selected' : null); ?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</span>
						</label>
						<label class="type if_setting">
							<span class="img"><img src="images/type_<?php echo $SkinInfo['product']['config_relative_cnt']['mo'][1]; ?>x1.gif" alt=""></span>
							<span class="tx">
								<input type="radio" name="s_display_relation_mo_col" value="1" <?php echo ($siteInfo['s_display_relation_mo_col']=='1' ? 'checked' : null); ?> class="js_relation_type">
								<span class="fr_tx"><?php echo $SkinInfo['product']['config_relative_cnt']['mo'][1]; ?> x </span>
								<select name="s_display_relation_mo_row" <?php echo ($siteInfo['s_display_relation_mo_col']<>'1' ? 'class="disabled" disabled' : null); ?>>
									<?php for($i=1; $i<=10; $i++){ ?>
										<option value="<?php echo $i; ?>" <?php echo ($siteInfo['s_display_relation_mo_col']=='1' && $siteInfo['s_display_relation_mo_row'] == $i ? 'selected' : null); ?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</span>
						</label>
					</td>
				</tr>
			</tbody>
		</table>
	</div>



	<?php echo _submitBTNsub(); ?>

</form>

<script>
	// 텝메뉴
	$(document).on('click', '.tab_menu', function() {
		$parent = $(this).closest('.data_form');
		var idx = $(this).data('idx');
		// 탭변경
		$parent.find('.tab_menu').closest('li').removeClass('hit');
		$parent.find('.tab_menu[data-idx='+ idx +']').closest('li').addClass('hit');
		// 입력항목변경
		$parent.find('.tab_conts').hide();
		$parent.find('.tab_conts[data-idx='+ idx +']').show();

		// 부모창이 display:none; 일때 높이 오류 수정
		var trigger_cont_editor = $(this).data('trigger')=='Y' ? true : false;
		if(trigger_cont_editor){
			$('.tab_conts[data-idx='+ idx +'] .SEditor').each(function(){
				var id = $(this).attr('id');
				if(oEditors.length > 0){
					oEditors.getById[id].exec('RESIZE_EDITING_AREA_BY',[true]);
				}
			});
			$(this).data('trigger','N');
		}
	});

	// 위로 이동 버튼
	$(document).on('click', '.js_diplay_pinfo_up', function(){
		$tr = $(this).closest('tr');
		$prev = $tr.prev();
		if($prev.length > 0){
			$prev.before($tr);
			$tr.find('td').animate({backgroundColor: '#e6e9eb'},100).animate({backgroundColor: '#fff'},500);
		}
	});
	// 아래로 이동 버튼
	$(document).on('click', '.js_diplay_pinfo_down', function(){
		$tr = $(this).closest('tr');
		$next = $tr.next();
		if($next.length > 0){
			$next.after($tr);
			$tr.find('td').animate({backgroundColor: '#e6e9eb'},100).animate({backgroundColor: '#fff'},500);
		}
	});
	// 삭제 버튼
	$(document).on('click', '.js_diplay_pinfo_del', function(){
		$tr = $(this).closest('tr');
		$wrap = $(this).closest('.category');
		$before = $wrap.find('.before');
		if($before.length > 0){
			$before.find('tbody').prepend($tr);
			$tr.find('td').animate({backgroundColor: '#e6e9eb'},100).animate({backgroundColor: '#fff'},500);
			$tr.find('input').attr({'disabled':'disabled'});
		}

		display_content_none(this);
	});
	// 추가 버튼
	$(document).on('click', '.js_diplay_pinfo_add', function(){
		$tr = $(this).closest('tr');
		$wrap = $(this).closest('.category');
		$after = $wrap.find('.after');
		if($after.length > 0){
			$after.find('tbody').prepend($tr);
			$tr.find('td').animate({backgroundColor: '#e6e9eb'},100).animate({backgroundColor: '#fff'},500);
			$tr.find('input').removeAttr('disabled');
		}

		display_content_none(this);
	});
	// 전체삭제 버튼
	$(document).on('click', '.js_diplay_pinfo_del_all', function(){
		$wrap = $(this).closest('.category');
		$after = $wrap.find('.after');
		$before = $wrap.find('.before');
		$tr = $after.find('tr');
		if($after.length > 0 && $before.length > 0){
			$before.find('tbody').prepend($tr);
			$tr.find('td').animate({backgroundColor: '#e6e9eb'},100).animate({backgroundColor: '#fff'},500);
			$tr.find('input').attr({'disabled':'disabled'});
		}

		display_content_none(this);
	});
	// 전체추가 버튼
	$(document).on('click', '.js_diplay_pinfo_add_all', function(){
		$wrap = $(this).closest('.category');
		$after = $wrap.find('.after');
		$before = $wrap.find('.before');
		$tr = $before.find('tr');
		if($after.length > 0 && $before.length > 0){
			$after.find('tbody').prepend($tr);
			$tr.find('td').animate({backgroundColor: '#e6e9eb'},100).animate({backgroundColor: '#fff'},500);
			$tr.find('input').removeAttr('disabled');
		}

		display_content_none(this);
	});
	// 항목 추가 삭제 시 내용없음 표시
	function display_content_none(obj){
		$wrap = $(obj).closest('.category');
		$after = $wrap.find('.after');
		$before = $wrap.find('.before');

		if($after.find('tr').length > 0) $after.parent().find('.common_none').hide();
		else $after.parent().find('.common_none').show();

		if($before.find('tr').length > 0) $before.parent().find('.common_none').hide();
		else $before.parent().find('.common_none').show();

	}



	// 진열설정 변경
	$(document).on('click','.js_relation_type', function(){
		$wrap = $(this).closest('td');
		$wrap.find('select').attr({'disabled':'disabled'}).addClass('disabled');
		$(this).parent().find('select').removeAttr('disabled').removeClass('disabled');
	});
</script>

<?php include_once('wrap.footer.php'); ?>