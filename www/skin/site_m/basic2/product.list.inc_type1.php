<div class="item_box<?php echo ($v['p_stock'] < 1 ? ' item_soldout ' : ''); ?>">
	<a href="/?pn=product.view&cuid=<?php echo $cuid; ?>&pcode=<?php echo $v['p_code']; ?>" class="upper_link" title="<?php echo addslashes(htmlspecialchars($v['p_name'])); ?>"></a>
	<!-- 상품이미지 344 * 400 -->
	<div class="thumb">
		<?php if($_img) { ?>
			<div class="real_img"><img src="<?php echo $_img; ?>" alt="<?php echo addslashes(htmlspecialchars($v['p_name'])); ?>" /></div>
		<?php } ?>
		<div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="<?php echo addslashes(htmlspecialchars($v['p_name'])); ?>" /></div>
		<?php if($v['p_stock'] <= 0) { ?>
			<!-- 솔드아웃일 경우 item_quick 삭제 -->
			<div class="soldout"><span class="inner">SOLD OUT</span></div>
		<?php } ?>
	</div>
	<!-- 상품정보 -->
	<div class="info">
		<div class="item_brand"><?php echo stripslashes($v['p_brand']); ?></div>
		<div class="item_name"><?php echo htmlspecialchars($v['p_name']); ?></div>
		<div class="price">
			<?php if($v['p_screenPrice'] > 0) { ?>
				<!-- 이전가격없으면 div삭제 display:none 하지말고 아예 안나오도록 , 순서변경/소스금지 -->
				<div class="before hide"><span class="won"><?php echo number_format($v['p_screenPrice']); ?></span><span class="unit">원</span></div>
			<?php } ?>
			<div class="after"><span class="won"><?php echo number_format($v['p_price']); ?></span><span class="unit">원</span></div>
		</div>
		<!-- 찜버튼 (클릭하면 hit/한번더 클릭하면 취소) -->
		<a href="#none" class="btn_wish js_wish<?php echo (is_wish($v['p_code'])?' hit':null); ?>" data-pcode="<?php echo $v['p_code']; ?>"></a>
	</div>
	<!-- 상품아이콘 / 없을때 div숨김 -->
	<?php echo $pro_icon; ?>
</div>