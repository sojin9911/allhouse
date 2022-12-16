<?php
include_once("inc.php");
AutoHTTPSMove('admin');

	// 서브운영자 권한 확인
	AdminMenuCheck();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<title><?=$siteInfo['s_adshop']?> 관리자</title>

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /><!-- 문서모드고정 (문서모드7이되서 깨지는경우가있음) -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width">
	<meta name="format-detection" content="telephone=no" /><!-- 자동으로 전화 링크되는것 방지 -->
	<meta name="robots" content="noindex"><!-- 관리자 모드 네이버 검색 등록 차단 -->

	<!-- 디자인css 순서지킬것 -->
	<link href="<?php echo OD_ADMIN_URL; ?>/css/default_setting.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo OD_ADMIN_URL; ?>/css/totalAdmin.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo OD_ADMIN_URL; ?>/css/design.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo OD_ADMIN_URL; ?>/css/customize.css" rel="stylesheet" type="text/css" />
	<link href="/include/js/jquery/jqueryui/jquery-ui.min.css" rel="stylesheet" type="text/css">
	<link href="/include/js/tagEditor/jquery.tag-editor.css" rel="stylesheet" type="text/css">
	<link href="/include/js/tagEditor/admin.css" rel="stylesheet" type="text/css">
	<link href="<?php echo OD_ADMIN_URL; ?>/js/colorpicker/evol-colorpicker.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo OD_ADMIN_URL; ?>/js/colorpicker/evol-colorpicker.custom.css" rel="stylesheet" type="text/css">
	<link href="/include/js/air-datepicker/css/datepicker.min.css" rel="stylesheet" type="text/css">

	<script src="/include/js/jquery-1.11.2.min.js"></script>
	<script src="/include/js/jquery.placeholder.js"></script>
	<script src="/include/js/jquery/jquery.lightbox_me.js"></script>
	<script src="/include/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="/include/js/jquery/jquery.validate.js"></script>
	<script src="/include/js/jquery/jquery.easing.1.3.js"></script>
	<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
	<script src="/include/js/tagEditor/jquery.tag-editor.js"></script>
	<script src="/include/js/tagEditor/jquery.tag-editor.caret.min.js"></script>
	<script src="/include/js/clipboard/clipboard.min.js"></script>
	<script src="<?php echo OD_ADMIN_URL; ?>/js/colorpicker/evol-colorpicker.custom.js"></script>
	<script src="/include/js/air-datepicker/js/datepicker.js"></script>
	<script src="/include/js/air-datepicker/js/i18n/datepicker.ko.js"></script>
	<script src="<?php echo OD_ADMIN_URL; ?>/js/common.js?v=<?=time()?>"></script>

	<!--  2017-07-12 ::: 네이버 스마트에디터2 추가 ::: SSJ { -->
	<script type="text/javascript" src="/include/smarteditor2/js/service/HuskyEZCreator.js" charset="utf-8"></script>
	<script type="text/javascript" src="/include/smarteditor2/smarteditor2.js" charset="utf-8"></script>
	<!-- } 2017-07-12 ::: 네이버 스마트에디터2 추가 ::: SSJ -->

	<script type="text/javascript">
	$(document).ready(function(){

		var $root = $('html, body');
		$(document).delegate('.scrollto','click',function() {
			var target = $(this).data('scrollto');
			$root.animate({

				scrollTop: $('[data-name="' + target + '"]').offset().top - 10
			}, 500, 'easeInOutCubic');
			return false;
		});

		// --- LCY :: 2017-12-21 :: 클립보드 기능(브라우저버전별 지원 => Chrome 42+ Edge 12+, Firefox 41+, IE9 +, oPERA 29+, Safari 10+ ) ---
		var clipboard = new Clipboard('.js-clipboard');
		clipboard.on('success', function(e) {
			alert("복사되었습니다.");
			e.clearSelection();
		});
		clipboard.on('error', function(e) {
			alert("복사기능이지원되지 않습니다.\n직접 선택복사해주세요");
		});
		// --- LCY :: 2017-12-21 :: 클립보드 기능(브라우저버전별 지원 => Chrome 42+ Edge 12+, Firefox 41+, IE9 +, oPERA 29+, Safari 10+ ) ---

	});
	</script>
</head>
<body<?php echo (isset($app_mode) && $app_mode == 'popup' ? ' style="min-width: auto;" ' : null); ?>>
<div class="wrap"<?php echo (isset($app_mode) && $app_mode == 'popup' ? ' style="padding-bottom: 0;" ' : null); ?>>