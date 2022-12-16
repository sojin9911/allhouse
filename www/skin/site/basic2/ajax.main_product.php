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
<div class="main_item_list rolling_box">
	<!-- ◆ 상품리스트 : 기본 6단 / 5단 if_col5  -->
	<div class="item_list<?php echo (isset($item_list_class) && $item_list_class != ''?' '.$item_list_class:null); ?> ">
		<ul class="hide js_main_product_<?php echo $dmsuid; ?>_slide_tmp">
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
				for($i=0; $i<$itemMain['dms_list_product_display']+1; $i++) {
			?>
				<li><div class="item_box"><div class="thumb"><div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="" /></div></div></div></li>
			<?php }} ?>
		</ul>
		<div class="js_main_product_<?php echo $dmsuid; ?>_slide">
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
</div>