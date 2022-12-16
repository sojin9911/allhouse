<?php
/*
	accesskey {
		s: 저장
		l: 리스트
	}
*/
$app_current_link = '_banner.list.php';
include_once('wrap.header.php');

// 사이트 스킨 전체 배너리스트를 얻음
$AllBannerLoc = $arr_banner_loc_common;
$merge_no = 'NO';
foreach($_skin_list as $k=>$v) {
	if(file_exists(OD_SKIN_ROOT.'/site/'.$k.'/_var.php')) {
		include(OD_SKIN_ROOT.'/site/'.$k.'/_var.php');
		if(count($skin_banner_loc) > 0) $AllBannerLoc = array_merge($AllBannerLoc, $skin_banner_loc);
	}
	if(file_exists(OD_SKIN_ROOT.'/site_m/'.$k.'/_var.php')) {
		include(OD_SKIN_ROOT.'/site_m/'.$k.'/_var.php');
		if(count($skin_banner_loc) > 0) $AllBannerLoc = array_merge($AllBannerLoc, $skin_banner_loc);
	}
}


// 변수 설정
$b_site_skin = $s_skin;
if(!$b_site_skin) $b_site_skin = $siteInfo['s_skin']; // 기본 스킨은 사용중인 스킨으로 고정
if($_mode == 'modify') $r = _MQ(" select * from smart_banner where b_uid = '{$_uid}' ");
else $r['b_loc'] = $s_loc;
if($r['b_site_skin']) $b_site_skin = $r['b_site_skin'];
?>
<form action="_banner.pro.php" method="post" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="s_skin" value="<?php echo $s_skin; ?>">
	<input type="hidden" name="s_loc" value="<?php echo $s_loc; ?>">
	<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>스킨</th>
					<td>
						<?php echo _InputSelect('b_site_skin', array_keys($_skin_list), $b_site_skin, ' class="js_site_skin"', array_values($_skin_list), ''); ?>
					</td>
					<th>배너구분</th>
					<td>
						<?php echo _InputSelect('b_loc', array_keys($AllBannerLoc), $r['b_loc'], ' class="js_skin_banner_loc"', array_values($AllBannerLoc), ''); ?>
						<script type="text/javascript">
							$(document).on('change', '.js_site_skin', SkinBannerLoc);
							$(document).ready(SkinBannerLoc);
							function SkinBannerLoc() {
								var _skin = $('.js_site_skin').find('option:selected').val();
								$.ajax({
									data: {
										_mode: 'skin_banner_loc',
										s_skin: _skin
									},
									type: 'POST',
									cache: false,
									url: '_banner.pro.php',
									success: function(data) {
										if(data == '') data = null;
										var result = $.parseJSON(data);
										var _option;
										_option = '<option value="">-선택-</option>';
										if(result) {
											$.each(result, function(k, v) {
												_option += '<option value="'+k+'"'+(k == '<?php echo $r['b_loc']; ?>'?' selected':'')+'>'+v+'</option>';
											});
										}
										$('.js_skin_banner_loc').html(_option);
										return result;
									}
								});
							}
						</script>
					</td>
				</tr>
				<tr class="js_set_view">
					<th>배너노출</th>
					<td>
						<?php echo _InputRadio('b_view', array('Y', 'N'), ($r['b_view']?$r['b_view']:'Y'), '', array('노출', '숨김'), ''); ?>
					</td>
					<th>노출순위</th>
					<td>
						<input type="text" name="b_idx" class="design t_center" value="<?php echo ($r['b_idx']?$r['b_idx']:0); ?>" style="width:50px">
					</td>
				</tr>
				<tr class="js_set_term">
					<th>기한설정</th>
					<td colspan="3">
						<?php echo _InputRadio('b_none_limit', array('Y', 'N'), ($r['b_none_limit']?$r['b_none_limit']:'Y'), ' class="js_banner_trim_type"', array('무기한', '기간지정'), ''); ?>
						<div class="js_change_trim">
							<div class="dash_line"><!-- 점선라인 --></div>
							<input type="text" name="b_sdate" class="design js_datepic js_banner_trim_use" value="<?php echo ($r['b_none_limit'] <> 'Y'?$r['b_sdate']:''); ?>" style="width:85px" <?php echo  ($r['b_none_limit'] == 'Y'?' disabled ':null); ?>>
							<span class="fr_tx">-</span>
							<input type="text" name="b_edate" class="design js_datepic js_banner_trim_use" value="<?php echo ($r['b_none_limit'] <> 'Y'?$r['b_edate']:''); ?>" style="width:85px" <?php echo  ($r['b_none_limit'] == 'Y'?' disabled ':null); ?>>
						</div>
						<script type="text/javascript">
							$(document).on('change', '.js_banner_trim_type', BannerTrim);
							$(document).ready(BannerTrim);
							function BannerTrim() {
								var _type = $('.js_banner_trim_type:checked').val();
								if(_type == 'N') $('.js_banner_trim_use').removeAttr('disabled');
								else $('.js_banner_trim_use').attr('disabled',true);
							}
						</script>
					</td>
				</tr>
				<tr class="option_area option_area_set_color js_set_color">
					<th>배경색</th>
					<td colspan="3">
						<input type="text" name="b_color" class="design js_colorpic" value="<?php echo $r['b_color']; ?>" style="width:70px">
					</td>
				</tr>
				<tr class="js_set_image">
					<th>배너이미지</th>
					<td colspan="3">
						<?php echo _PhotoForm('..'.IMG_DIR_BANNER, 'b_img', $r['b_img'], 'style="width:250px"'); ?>
						<?php echo _DescStr('사이트 스킨에 따라 크기는 자동 조절될 수 있습니다.'); ?>
					</td>
				</tr>
				<tr class="js_set_title">
					<th>타이틀</th>
					<td colspan="3">
						<input type="text" name="b_title" class="design" value="<?php echo $r['b_title']; ?>" style="width:400px">
					</td>
				</tr>
				<tr class="option_area option_area_set_detail_info js_set_detail">
					<th>배너설명</th>
					<td colspan="3">
						<span class="fr_tx">메인설명 :</span>
						<input type="text" name="b_info" class="design" value="<?php echo $r['b_info']; ?>" style="width:400px">
						<div class="clear_both"></div>
						<span class="fr_tx">보조설명 :</span>
						<input type="text" name="b_sub_info" class="design" value="<?php echo $r['b_sub_info']; ?>" style="width:400px">
					</td>
				</tr>
				<tr class="js_set_link_target">
					<th>링크타켓</th>
					<td colspan="3">
						<?php echo _InputRadio('b_target', array('_none', '_self', '_blank'), ($r['b_target']?$r['b_target']:'_none'), ' class="js_target"', array('링크없음', '같은창', '새창'), ''); ?>
						<script type="text/javascript">
							$(document).on('change', '.js_target', BannerLink);
							$(document).ready(BannerLink);
							function BannerLink() {
								var _type = $('.js_target:checked').val();
								if(_type == '_none') $('.js_banner_link').hide();
								else $('.js_banner_link').show();
							}
						</script>
					</td>
				</tr>
				<tr class="js_banner_link js_set_banner_link">
					<th>링크주소</th>
					<td colspan="3">
						<input type="text" name="b_link" class="design js_app_link" value="<?php echo $r['b_link']; ?>" style="width:400px;">
						<a href="#none" onclick="productWin(); return false;" class="c_btn h27 line bold">상품연결</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<?php
		$pass_variable_string_url = enc('e', 'pass_loc='.$s_loc);
		echo _submitBTN($app_current_link);
	?>
</form>



<script type="text/javascript">
	function productWin() {
		var _pcode = $('input[name=b_link]').val();
		_pcode = _pcode.split('/?pn=product.view&pcode=');
		_pcode = (_pcode[1]?_pcode[1]:_pcode[0]);
		window.open('_banner.product_link.php?relation_prop_code='+_pcode, 'relation', 'width=1120, height=800, scrollbars=yes');
	}
	var loc_select_check = function() {
		setTimeout(function() {
			var bannerArr = $('.js_skin_banner_loc option:selected').val().split(',');
			$('[class^=js_set_]').show();
			$('.option_area').hide();
			if($.inArray('set_color', bannerArr) > 0) $('.option_area_set_color').show();
			if($.inArray('set_detail_info', bannerArr) > 0) $('.option_area_set_detail_info').show();

			// 숨김 전용 설정
			if($.inArray('not_set_view', bannerArr) > 0) $('.js_set_view').hide();
			if($.inArray('not_set_term', bannerArr) > 0) $('.js_set_term').hide();
			if($.inArray('not_set_color', bannerArr) > 0) $('.js_set_color').hide();
			if($.inArray('not_set_image', bannerArr) > 0) $('.js_set_image').hide();
			if($.inArray('not_set_title', bannerArr) > 0) $('.js_set_title').hide();
			if($.inArray('not_set_detail', bannerArr) > 0) $('.js_set_detail').hide();
			if($.inArray('not_set_link_target', bannerArr) > 0) $('.js_set_link_target').hide();
			if($.inArray('not_set_banner_link', bannerArr) > 0) $('.js_set_banner_link').hide();
		}, 100);
	}
	$(document).on('change', '.js_skin_banner_loc', loc_select_check);
	$(document).ready(loc_select_check);
</script>
<?php include_once('wrap.footer.php'); ?>