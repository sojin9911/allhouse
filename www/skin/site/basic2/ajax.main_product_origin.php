<?php

// -- 해당 메인 상품의 설정을 가져온다.
$itemMain = _MQ("select *from `smart_display_main_set` where `dms_type` = 'main' and `dms_depth` = '2' and `dms_view` = 'Y' and `dms_uid` = '".$dmsuid."'
and `dms_list_product_view` = 'Y'");

// -- 검색된 값이 없거나 상품이 노출이 아닐 시
if(count($res) <= 0 || count($itemMain) < 1 ) {
	echo '
		<div class="item_list">
			<div class="c_none"><div class="gtxt">등록된 상품이 없습니다.</div></div>
		</div>
	';
	return;
}


// 임시보기 출력개수 보정처리
if($_COOKIE['temp_skin']) {
	$SkinInfoColArr = explode(',', $SkinInfo['category']['pc_list_depth']);
	if(!in_array($itemMain['dms_list_product_display'], $SkinInfoColArr)) $itemMain['dms_list_product_display'] = $SkinInfo['category']['pc_list_depth_default'];
}


$item_list_class = '';
if(in_array($itemMain['dms_list_product_display'], array(5))) $item_list_class = 'if_col'.$itemMain['dms_list_product_display'];
?>
<div class="rolling_box">
	<!-- ◆ 상품리스트 : 기본 6단 / 5단 if_col5  -->
	<div class="item_list<?php echo (isset($item_list_class) && $item_list_class != ''?' '.$item_list_class:null); ?>">
		<ul class="js_main_product_<?php echo $dmsuid; ?>_slide_tmp">
			<?php
			foreach($res as $k=>$v) {
				if($k>=$itemMain['dms_list_product_display']) continue;

			?>
				<li<?php echo ($k >= $itemMain['dms_list_product_display']?' style="display:none;"':null); ?>>
					<?php 
						$incType =''; // 타입은 기본 type1, 있을 경우 별도 설정
						$locationFile = basename(__FILE__); // 파일설정
						include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
					?>
				</li>
			<?php } ?>
			<?php
			if($itemMain['dms_list_product_display'] > count($res)) {
				for($i=0; $i<$itemMain['dms_list_product_display']-count($res); $i++) {
			?>
				<li><div class="item_box"><div class="thumb"><div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="" /></div></div></div></li>
			<?php }} ?>
		</ul>
		<div class="js_main_product_<?php echo $dmsuid; ?>_slide" style="display: none">
			<ul>
				<?php
				$res_num = 1;
				foreach($res as $k=>$v) {
					$res_num++;
					if($k > 0 && $k%$itemMain['dms_list_product_display'] === 0) {
						echo '</ul><ul>';
						$res_num = 1;
					}

				?>
					<li>
					<?php 
						$incType =''; // 타입은 기본 type1, 있을 경우 별도 설정
						$locationFile = basename(__FILE__); // 파일설정
						include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
					?>
					</li>
				<?php } ?>
				<?php
				if($itemMain['dms_list_product_display'] > $res_num) {
					for($i=0; $i<$itemMain['dms_list_product_display']-$res_num; $i++) {
				?>
					<li><div class="item_box"><div class="thumb"><div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="" /></div></div></div></li>
				<?php }} ?>
			</ul>
		</div>
	</div>
	<!-- / ◆ 상품리스트 -->

	<?php if(count($res) > $itemMain['dms_list_product_display']) { ?>
		<!-- 이전다음 버튼 (롤링 없으면 숨김) -->
		<span class="prevnext prev"><a href="#none" class="js_main_product_<?php echo $dmsuid; ?>_slide_prev" title="이전"><span class="icon off"></span><span class="icon over"></span></a></span>
		<span class="prevnext next"><a href="#none" class="js_main_product_<?php echo $dmsuid; ?>_slide_next" title="다음"><span class="icon off"></span><span class="icon over"></span></a></span>

		<script type="text/javascript">
			var MainMainSlide_<?php echo $dmsuid; ?> = $('.js_main_product_<?php echo $dmsuid; ?>_slide');
			function MainMainSlideOption_<?php echo $dmsuid; ?>() {
				$('.js_main_product_<?php echo $dmsuid; ?>_slide_tmp').show();
				$('.js_main_product_<?php echo $dmsuid; ?>_slide').hide();

				var MainMainSlide_<?php echo $dmsuid; ?>Margin = $('.js_main_product_<?php echo $dmsuid; ?>_slide_tmp').find('.item_box').css('margin-left').replace('px', '')*1;
				var MainMainSlide_<?php echo $dmsuid; ?>Width = $('.js_main_product_<?php echo $dmsuid; ?>_slide_tmp').outerWidth();
				$('.js_main_product_<?php echo $dmsuid; ?>_slide').css('width', MainMainSlide_<?php echo $dmsuid; ?>Width+MainMainSlide_<?php echo $dmsuid; ?>Margin);

				$('.js_main_product_<?php echo $dmsuid; ?>_slide_tmp').hide();
				$('.js_main_product_<?php echo $dmsuid; ?>_slide').show();
				return {
					auto: true,
					autoHover: false,
					controls: false,
					useCSS: true,
					minSlides: 1,
					moveSlides: 1,
					slideMargin: MainMainSlide_<?php echo $dmsuid; ?>Margin,
					slideWidth: MainMainSlide_<?php echo $dmsuid; ?>Width,
					holdWidth: MainMainSlide_<?php echo $dmsuid; ?>Width, // LDD: 2018-01-09 새롭게 추가된 옵션(자동 크기 변경을 차단하고 지정값으로 강제로 맞춘다)
					onSliderLoad: function() {
						// 2020-07-08 SSJ :: 메인 New Arrivals 롤링 잔상 제거
						$('.main_item .bx-wrapper').css({'width':$('.main_item .bx-wrapper').css('max-width') , 'max-width':''});
					},
					onSlideBefore: function() { MainMainSlide_<?php echo $dmsuid; ?>.stopAuto(); },
					onSlideAfter: function() { MainMainSlide_<?php echo $dmsuid; ?>.startAuto(); }
				};
			}
			$(function() {
				MainMainSlide_<?php echo $dmsuid; ?>.bxSlider(MainMainSlideOption_<?php echo $dmsuid; ?>());
			});

			$(document).on('click', '.js_main_product_<?php echo $dmsuid; ?>_slide_prev', function(e) {
				e.preventDefault();
				if(typeof MainMainSlide_<?php echo $dmsuid; ?> == 'object') MainMainSlide_<?php echo $dmsuid; ?>.goToPrevSlide();
			});
			$(document).on('click', '.js_main_product_<?php echo $dmsuid; ?>_slide_next', function(e) {
				e.preventDefault();
				if(typeof MainMainSlide_<?php echo $dmsuid; ?> == 'object') MainMainSlide_<?php echo $dmsuid; ?>.goToNextSlide();
			});

			$(window).resize(function() {
				if(typeof MainMainSlide_<?php echo $dmsuid; ?> == 'object') {
					MainMainSlide_<?php echo $dmsuid; ?>.destroySlider();
					MainMainSlide_<?php echo $dmsuid; ?>.bxSlider(MainMainSlideOption_<?php echo $dmsuid; ?>());
					MainMainSlide_<?php echo $dmsuid; ?>.startAuto();
				}
			});
		</script>
	<?php } ?>
</div>