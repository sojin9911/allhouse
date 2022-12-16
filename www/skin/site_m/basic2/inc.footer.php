	</div>
	<div class="js_footer_position"></div>
	<!-- container -->
	<iframe name="common_frame" width="150" height="150" frameborder="0" style="display:none;"></iframe>
	<!-- validate setting-->
	<script src="/include/js/jquery.validate.setDefault.js" type="text/javascript"></script>
	<script src="<?php echo $system['__url']; ?>/include/js/common.footer.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() {

			// 카트에 담긴 상품을 페이지 전체적으로 일괄 적용한다. class명은 glb_cart_cnt or js_cart_cnt 이다.
			// 인자 : 카트상품갯수, 상품갯수가 0일때 보이게 할것인지(show), 안보이게 할것인지(hide - 기본값)
			glb_cart_cnt_update('<?php echo $cart_cnt; ?>/<?php echo $cart_manual_cnt; ?>', 'hide'); // /include/js/shop.js

			if($('.js_wish_cnt').length > 0) $('.js_wish_cnt').text('<?php echo (get_wish_cnt()?get_wish_cnt():'0'); ?>'); // 찜한상품 개수 반영
		});

		// -- 로그인공통 로그인 필요할경우 qnxdlsek.
		$(document).on('click','.js_login',function(){
			if( confirm("로그인 후 이용가능합니다.\n로그인 하시겠습니까?") == false){ return false; }
			location.href="/?pn=member.login.form&_rurl=<?php echo urlencode("/?".$_SERVER['QUERY_STRING']); ?>";
			return false;
		});

		// 이미지 onerror 처리
		$('img').error(function() {
			$(this).unbind('error');
			$(this).attr('src', '<?php echo $SkinData['skin_url']; ?>/images/skin/blank.gif');
		});
		$(document).ajaxComplete(function() {
			$('img').error(function() {
				$(this).unbind('error');
				$(this).attr('src', '<?php echo $SkinData['skin_url']; ?>/images/skin/blank.gif');
			});
		});

		$(document).on('click', '.js_wish', function(e) {
			e.preventDefault();
			var _pcode = $(this).data('pcode');
			var su = $(this);
			<?php if(is_login()) { ?>
				$.ajax({
					data: {
						mode: 'add',
						code: _pcode
					},
					type: 'POST',
					cache: false,
					url: '<?php echo OD_PROGRAM_URL; ?>/ajax.product.wish.php',
					success: function(data) {
						data = data*1;
						if(data > 0) {
							su.addClass('hit');
							su.prop('title', '찜삭제');
						}
						else {
							su.removeClass('hit');
							su.prop('title', '찜하기');
						}
					}
				});
			<?php } else { ?>
				if( confirm("로그인 후 이용가능합니다.\n로그인 하시겠습니까?") == false){ return false; }
				location.href = '/?pn=member.login.form&_rurl=<?php echo urlencode('/?'.$_SERVER['QUERY_STRING']); ?>';
				return;
			<?php } ?>
		});

	</script>

	<?php DeveModeFooter(); ?>
	<?php actionHook('footer_insert'); // 푸터에 스크립트등 삽입에 사용(로그 스크립트 등) ?>
</body>
</html>