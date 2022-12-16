<?php
if(count($res) <= 0) {
	echo '
		<div class="rolling_box">
			<div class="c_none"><div class="gtxt">등록된 상품이 없습니다.</div></div>
		</div>
	';
	return;
}

// 임시보기 출력개수 보정처리
if($_COOKIE['temp_skin']) {
	$SkinInfoColArr = explode(',', $SkinInfo['category']['pc_best_depth']);
	if(!in_array($siteInfo['s_category_display'], $SkinInfoColArr)) $siteInfo['s_category_display'] = $SkinInfo['category']['pc_best_depth_default'];
}

$item_list_class = '';
if(in_array($siteInfo['s_category_display'], array(5))) $item_list_class = 'if_col'.$siteInfo['s_category_display']; // 기본6단 이외 5단일 경우 클래스 변경

?>
<div class="rolling_box">
	<!-- ◆ 상품리스트 : 기본 6단 / 5단 if_col5  -->
	<div class="item_list<?php echo (isset($item_list_class) && $item_list_class != ''?' '.$item_list_class:null); ?>">
		<?php if(count($res) > 0) { ?>
			<ul class="js_main_best_product_slide_tmp">
				<?php
				foreach($res as $k=>$v) {
					if($k>=$siteInfo['s_category_display'] *2) continue;
				?>
					<li<?php echo ($k >= ($siteInfo['s_category_display']*2)?' style="display:none;"':null); ?>>
						<?php 
							$incType =''; // 타입은 기본 type1, 있을 경우 별도 설정
							$locationFile = basename(__FILE__); // 파일설정
							include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
						?>
					</li>
				<?php } ?>
			<?php

			if(($siteInfo['s_category_display']*2) > count($res)) {
				for($i=0; $i<($siteInfo['s_category_display']*2)-count($res); $i++) {
			?>
				<li><div class="item_box"><div class="thumb"><div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="" /></div></div></div></li>
			<?php }} ?>
			</ul>
		<?php } ?>
		<div class="js_main_best_product_slide" style="display: none">
			<ul>
				<?php
				$res_num = 1;
				foreach($res as $k=>$v) {
					$res_num++;
					if($k > 0 && $k%($siteInfo['s_category_display']*2) === 0) {
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
				if(($siteInfo['s_category_display']*2) > $res_num) {
					for($i=0; $i<($siteInfo['s_category_display']*2)-$res_num; $i++) {
				?>
					<li><div class="item_box"><div class="thumb"><div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="" /></div></div></div></li>
				<?php }} ?>
			</ul>
		</div>
	</div>
	<!-- / ◆ 상품리스트 -->
</div>


<?php if(count($res) > ($siteInfo['s_category_display']*2)) { ?>
<!-- 롤링아이콘 (롤링이 1개일때는 숨김) (해당 롤링일때 active 추가) -->
	<div class="rolling_icon">
		<span class="lineup js_main_best_product_slide_pager">
			<?php for($i=0; $i<count($res)/($siteInfo['s_category_display']*2); $i++) { ?>
				<a href="#none" data-slide-index="<?php echo $i; ?>" class="icon<?php echo ($i<=0?' active':null); ?>"></a>
			<?php } ?>
		</span>
	</div>
	<script type="text/javascript">
		var MainBestCategorySlide = $('.js_main_best_product_slide');
		function MainBestCategorySlideOption() {
			$('.js_main_best_product_slide_tmp').show();
			$('.js_main_best_product_slide').hide();

			var MainBestCategorySlideMargin = $('.js_main_best_product_slide_tmp').find('.item_box').css('margin-left').replace('px', '')*1;
			var MainBestCategorySlideWidth = $('.js_main_best_product_slide_tmp').outerWidth();
			$('.js_main_best_product_slide').css('width', MainBestCategorySlideWidth+MainBestCategorySlideMargin);

			$('.js_main_best_product_slide_tmp').hide();
			$('.js_main_best_product_slide').show();
			return {
				auto: false,
				autoHover: false,
				controls: false,
				useCSS: false,
				minSlides: 1,
				moveSlides: 1,
				pagerCustom: '.js_main_best_product_slide_pager',
				slideMargin: MainBestCategorySlideMargin,
				slideWidth: MainBestCategorySlideWidth,
				holdWidth: MainBestCategorySlideWidth, // LDD: 2018-01-09 새롭게 추가된 옵션(자동 크기 변경을 차단하고 지정값으로 강제로 맞춘다)
				onSliderLoad: function() {
                    // 2020-07-08 SSJ :: 메인 Category Best 롤링 잔상 제거
                    $('.js_main_cate_box .bx-wrapper').css({'width':$('.js_main_cate_box .bx-wrapper').css('max-width') , 'max-width':''});
                },
				onSlideBefore: function() { MainBestCategorySlide.stopAuto(); },
				onSlideAfter: function() { MainBestCategorySlide.startAuto(); }
			};
		}

		$(function() {
			MainBestCategorySlide.bxSlider(MainBestCategorySlideOption());
		});

		$(document).on('click', '.js_main_md_product_slide_prev', function(e) {
			e.preventDefault();
			if(typeof MainBestCategorySlide == 'object') MainBestCategorySlide.goToPrevSlide();
		});
		$(document).on('click', '.js_main_md_product_slide_next', function(e) {
			e.preventDefault();
			if(typeof MainBestCategorySlide == 'object') MainBestCategorySlide.goToNextSlide();
		});

		$(window).resize(function() {
			if(typeof MainBestCategorySlide == 'object') {
				MainBestCategorySlide.destroySlider();
				MainBestCategorySlide.bxSlider(MainBestCategorySlideOption());
				MainBestCategorySlide.startAuto();
			}
		});
	</script>
<?php } ?>