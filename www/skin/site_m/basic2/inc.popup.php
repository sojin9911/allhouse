<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
foreach($res as $pk=>$pv) {
	if($_COOKIE['AuthPopupClose_'.$pv['p_uid']] == 'Y') continue; // 오늘 하루 보이지 않음으로 체크된 팝업은 제외
?>
	<!-- ◆ 팝업창 (이미지형/에디터형 선택가능하게) -->
	<div class="c_popup js_popup_<?php echo $pv['p_uid']; ?>" style="top:<?php echo (int)$pv['p_mtop']; ?>px;">
		<div class="inner">
			<?php
			if($pv['p_mode'] == 'I') { // 이미지 모드
				$_img = IMG_DIR_POPUP_URL.$pv['p_img'];
			?>
				<!-- 이미지형 (다른설정값 없음) -->
				<div class="img_box">
					<?php if($pv['p_link']) { ?><a href="<?php echo $pv['p_link']; ?>" target="<?php echo $pv['p_target']; ?>"><?php } ?>
						<img src="<?php echo $_img; ?>" alt="<?php echo addslashes(htmlspecialchars($pv['p_title'])); ?>" />
					<?php if($pv['p_link']) { ?></a><?php } ?>
				</div>
			<?php } else { ?>
				<!-- 에디터형 : 배경색상,팝업창 크기 관리자에서 지정; 기본값 350 * 250 으로 지정해서 0이 되지 않도록 -->
				<div class="editor_box" style="background:<?php echo '#'.str_replace('#', '', ($pv['p_bgcolor']?$pv['p_bgcolor']:'#FFFFFF')); ?>;">
					<div class="editor">
						<?php echo $pv['p_content']; ?>
					</div>
				</div>
			<?php } ?>

			<div class="close_box">
				<ul>
					<li><a href="#none" data-uid="<?php echo $pv['p_uid']; ?>" class="btn js_popup_today_close">오늘하루 창닫기</a></li>
					<li><a href="#none" data-uid="<?php echo $pv['p_uid']; ?>" class="btn js_popup_close">창닫기</a></li>
				</ul>
			</div>
		</div>
	</div>
	<!-- /◆ 팝업창 -->
	<script type="text/javascript">
		$(document).on('click', '.js_popup_today_close', function(e) {
			e.preventDefault();
			var _uid = $(this).data('uid');
			$.ajax({
				data: {
					_mode: 'popup_close',
					uid: _uid
				},
				type: 'POST',
				cache: false,
				url: '<?php echo OD_PROGRAM_URL; ?>/_pro.php',
				success: function(data) {
					//if(data == 'error') return alert('알수없는 이유로 팝업을 닫지 못했습니다.');
					$('.js_popup_'+_uid).remove();
				}
			});
		});
		$(document).on('click', '.js_popup_close', function(e) {
			e.preventDefault();
			var _uid = $(this).data('uid');
			$('.js_popup_'+_uid).remove();
		});
	</script>
<?php } ?>