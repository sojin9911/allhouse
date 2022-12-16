<?php
// bx슬라이더 슬라이드
if(count($res) <= 0) {
	echo '
		<div class="rolling_box">
			<div class="c_none"><div class="gtxt">등록된 상품이 없습니다.</div></div>
		</div>
	';
	return;
}

// 스킨 2는 출력처리를 2개로 고정
$itemMd['dms_list_product_mobile_display'] = 2;

$item_list_class = '';
if(in_array($siteInfo['s_category_display_mobile'], array(2))) $item_list_class = 'if_col'.$siteInfo['s_category_display_mobile']; // 기본3단 이외 2단일 경우 클래스 변경

?>
<div class="rolling_box">
	<!-- ◆ 상품리스트 : 기본 2단 / 1단 if_col1  -->
	<div class="item_list js_mct_<?php echo $cuid; ?>_item" >
		<ul>
			<?php
			foreach($res as $k=>$v) {

			?>
				<li>
					<?php 
						$incType =''; // 타입은 기본 type1, 있을 경우 별도 설정
						$locationFile = basename(__FILE__); // 파일설정
						include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
					?>
				</li>
			<?php } ?>
		</ul>
	</div>
	<!-- / ◆ 상품리스트 -->
</div>

<script type="text/javascript">
	var SwiperMainCateMenuOption_<?php echo $cuid; ?> = {hit_class:'.hit', speed:0, position:'center'};
	var SwiperMainCateMenu_<?php echo $cuid; ?>;
	$(window).load(function() {
		if(typeof SwiperMainCateMenu_<?php echo $cuid; ?> == 'object') {
			SwiperMainCateMenu_<?php echo $cuid; ?>.destroy();
			SwiperMainCateMenu_<?php echo $cuid; ?> = null;
		}
		SwiperMainCateMenu_<?php echo $cuid; ?> = new SwiperJSMenu('.js_mct_<?php echo $cuid; ?>_item', SwiperMainCateMenuOption_<?php echo $cuid; ?>);
		SwiperMainCateMenu_<?php echo $cuid; ?>.action();
	});

	$(window).resize(function() {
		if(typeof SwiperMainCateMenu_<?php echo $cuid; ?> == 'object') {
			SwiperMainCateMenu_<?php echo $cuid; ?>.destroy();
			SwiperMainCateMenu_<?php echo $cuid; ?> = null;
		}
		SwiperMainCateMenu_<?php echo $cuid; ?> = new SwiperJSMenu('.js_mct_<?php echo $cuid; ?>_item', SwiperMainCateMenuOption_<?php echo $cuid; ?>);
		SwiperMainCateMenu_<?php echo $cuid; ?>.action();
	});

	$(window).on('orientationchange', function() {
		if(typeof SwiperMainCateMenu_<?php echo $cuid; ?> == 'object') {
			SwiperMainCateMenu_<?php echo $cuid; ?>.destroy();
			SwiperMainCateMenu_<?php echo $cuid; ?> = null;
		}
		SwiperMainCateMenu_<?php echo $cuid; ?> = new SwiperJSMenu('.js_mct_<?php echo $cuid; ?>_item', SwiperMainCateMenuOption_<?php echo $cuid; ?>);
		SwiperMainCateMenu_<?php echo $cuid; ?>.action();
	});
</script>