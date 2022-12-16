<div class="item_box<?php echo ($v['p_stock'] < 1 ? ' item_soldout ' : ''); ?>">
	<!-- 상품이미지 344 * 400 -->
	<div class="thumb">
		<?php if($_img) { ?>
			<a href="/?pn=product.view&cuid=<?php echo $cuid; ?>&pcode=<?php echo $v['p_code']; ?>" title="<?php echo addslashes(htmlspecialchars($v['p_name'])); ?>"  >
			<div class="real_img"><img src="<?php echo $_img; ?>" alt="<?php echo addslashes(htmlspecialchars($v['p_name'])); ?>" /></div></a>
		<?php } ?>
		<div class="fake_img"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/thumb.gif" alt="<?php echo addslashes(htmlspecialchars($v['p_name'])); ?>" /></div>
		<?php if($v['p_stock'] <= 0) { ?>
			<!-- 솔드아웃일 경우 item_quick 삭제 -->
			<div class="soldout"><span class="inner">SOLD OUT</span></div>
		<?php } else { ?>
			<!-- 상품 퀵메뉴 ㅣ 찜하기는 찜되면 hit 취소하면 삭제 title값을 "찜삭제"로 변경 -->
			<div class="item_quick">
				<a href="<?php echo $cart_link; ?>" class="btn cart" title="장바구니 담기"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/iquick_cart.png" alt="" /></a>
				<a href="#none" class="btn wish js_wish<?php echo (is_wish($v['p_code'])?' hit':null); ?>" data-pcode="<?php echo $v['p_code']; ?>" title="<?php echo (is_wish($v['p_code'])?'찜삭제':'찜하기'); ?>"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/iquick_wish.png" alt="" class="off"/><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/iquick_wish_on.png" alt="" class="on" /></a>
				<a href="#none" class="btn view js_quick_view" data-pcode="<?php echo $v['p_code']; ?>" title="간단보기"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/iquick_view.png" alt="" /></a>
				<a href="/?pn=product.view&cuid=<?php echo $cuid; ?>&pcode=<?php echo $v['p_code']; ?>" class="btn blank" title="새창보기" target="_blank"><img src="<?php echo $SkinData['skin_url']; ?>/images/c_img/iquick_blank.png" alt="" /></a>
			</div>
		<?php } ?>
	</div>
	<!-- 상품정보 -->
	<div class="info">
		<div class="item_name ellipsis"><?php echo stripslashes($v['p_brand']); ?></div>
		<div class="item_name ellipsis"><?php echo stripslashes($v['p_name']); ?></div>
		<div class="price">
			<?php if($v['p_screenPrice'] > 0) { ?>
				<!-- 이전가격없으면 div삭제 display:none 하지말고 아예 안나오도록 , 순서변경/소스금지 -->
			<?php } ?>
			<div class="after real_price"><span class="won"><?php echo number_format($v['p_price']); ?></span> <span class="unit"> 원</span></div>
		</div>
	</div>
	<?php echo $pro_icon; ?>
</div>