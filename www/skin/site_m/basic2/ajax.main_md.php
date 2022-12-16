<?php

// -- 해당 메인 상품의 설정을 가져온다.
$itemMd = _MQ("select *from `smart_display_main_set` where `dms_type` = 'md' and `dms_depth` = '2' and `dms_view` = 'Y' and `dms_uid` = '".$dmsuid."'
and `dms_list_product_mobile_view` = 'Y'");

// -- 검색된 값이 없거나 상품이 노출이 아닐 시
if(count($res) <= 0 || count($itemMd) < 1 ) {
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
if(in_array($itemMd['dms_list_product_mobile_display'], array(1))) $item_list_class = 'if_col'.$itemMd['dms_list_product_mobile_display'];
?>
<div class="rolling_box">
	<!-- ◆ 상품리스트 : 기본 2단 / 1단 if_col1  -->
	<div class="item_list js_md_<?php echo $dmsuid; ?>_item">
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
	var SwiperMainMDOption_<?php echo $dmsuid; ?> = {hit_class:'.hit', speed:0, position:'center'};
	var SwiperMainMD_<?php echo $dmsuid; ?>;
	$(window).load(function() {
		if(typeof SwiperMainMD_<?php echo $dmsuid; ?> == 'object') {
			SwiperMainMD_<?php echo $dmsuid; ?>.destroy();
			SwiperMainMD_<?php echo $dmsuid; ?> = null;
		}
		SwiperMainMD_<?php echo $dmsuid; ?> = new SwiperJSMenu('.js_md_<?php echo $dmsuid; ?>_item', SwiperMainMDOption_<?php echo $dmsuid; ?>);
		SwiperMainMD_<?php echo $dmsuid; ?>.action();
	});

	$(window).resize(function() {
		if(typeof SwiperMainMD_<?php echo $dmsuid; ?> == 'object') {
			SwiperMainMD_<?php echo $dmsuid; ?>.destroy();
			SwiperMainMD_<?php echo $dmsuid; ?> = null;
		}
		SwiperMainMD_<?php echo $dmsuid; ?> = new SwiperJSMenu('.js_md_<?php echo $dmsuid; ?>_item', SwiperMainMDOption_<?php echo $dmsuid; ?>);
		SwiperMainMD_<?php echo $dmsuid; ?>.action();
	});

	$(window).on('orientationchange', function() {
		if(typeof SwiperMainMD_<?php echo $dmsuid; ?> == 'object') {
			SwiperMainMD_<?php echo $dmsuid; ?>.destroy();
			SwiperMainMD_<?php echo $dmsuid; ?> = null;
		}
		SwiperMainMD_<?php echo $dmsuid; ?> = new SwiperJSMenu('.js_md_<?php echo $dmsuid; ?>_item', SwiperMainMDOption_<?php echo $dmsuid; ?>);
		SwiperMainMD_<?php echo $dmsuid; ?>.action();
	});
</script>