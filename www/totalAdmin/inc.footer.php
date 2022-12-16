		<div class="js_dialog" style="display: none"><!-- 다이얼로그 기본폼:: 삭제금지 --></div>
		<div class="js_preview_image_popup" style="display: none;"><!-- 라이트박스미 기본폼:: 삭제금지 -->
			<div class="popup" style="max-width:1100px; background-color:#fff;">
				<div class="pop_title"><strong>이미지 미리보기</strong></div>
				<div class="data_list"></div>
				<div class="c_btnbox">
					<ul>
						<li><a href="#none" class="c_btn h34 black line normal close">닫기</a></li>
					</ul>
				</div>
			</div>
		</div>

	</div><!-- /wrap -->

	<!-- 공통 frame -->
	<iframe name="common_frame" width="0" height="0" frameborder="0" style="display:none;"></iframe>
	<script src="/include/js/jquery.validate.setDefault.js" type="text/javascript"></script>
	<script src="<?php echo OD_ADMIN_URL; ?>/js/common.footer.js"></script>
	<?php //DeveModeFooter('#880015', '#ffffff', 'top:187px; font-weight:bold; left:250px'); ?>
	<?php DeveModeFooterDetail(); ?>
</body>
</html>