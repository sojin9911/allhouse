<?php
include_once('inc.php');
if($_login_trigger != 'N' && AdminLoginCheck('value') === false) header('location: _intro.php');
?>
<!DOCTYPE HTML>
<head>
	<title>모바일 관리자페이지</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<!-- 화면축소/확대방지 -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0, target-densitydpi=medium-dpi" />

	<!-- 모바일에서 숫자 전화자동링크방지 -->
	<meta name="format-detection" content="telephone=no" />


	<link href="<?php echo PATH_MOBILE_TOTALADMIN; ?>/css/m.default_setting.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo PATH_MOBILE_TOTALADMIN; ?>/css/m.totalAdmin.css" rel="stylesheet" type="text/css" />

	<!-- 공통css -->
	<link href="<?php echo PATH_MOBILE_TOTALADMIN; ?>/css/cm_font.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo PATH_MOBILE_TOTALADMIN; ?>/css/cm_design.css" rel="stylesheet" type="text/css" />


	<!-- 자바스크립트 -->
	<script src="/include/js/jquery-1.11.2.min.js" type="text/javascript"></script>
	<script src="/include/js/jquery.placeholder.js" type="text/javascript"></script>

	<!-- jQuery performance boost -->
	<script src="/include/js/jquery/jquery.easing.1.3.js" type="text/javascript"></script>
	<script src="/include/js/TweenMax.min.js"></script>
	<script src="/include/js/jquery.gsap.min.js"></script>

	<!-- validate -->
	<script src="/include/js/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
	<script src="/include/js/jquery/jquery.validate.js"></script>


	<link href="/include/js/jquery/jqueryui/jquery-ui.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo OD_ADMIN_URL; ?>/js/colorpicker/evol-colorpicker.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo OD_ADMIN_URL; ?>/js/colorpicker/evol-colorpicker.custom.css" rel="stylesheet" type="text/css">
	<link href="/include/js/air-datepicker/css/datepicker.min.css" rel="stylesheet" type="text/css">
	<link href="/include/js/tagEditor/jquery.tag-editor.css" rel="stylesheet" type="text/css">
	
	<script src="/include/js/jquery/jqueryui/jquery-ui.min.js"></script>
	<script src="/include/js/tagEditor/jquery.tag-editor.js"></script>
	<script src="/include/js/tagEditor/jquery.tag-editor.caret.min.js"></script>
	<script src="<?php echo OD_ADMIN_URL; ?>/js/colorpicker/evol-colorpicker.custom.js"></script>
	<script src="/include/js/air-datepicker/js/datepicker.js"></script>
	<script src="/include/js/air-datepicker/js/i18n/datepicker.ko.js"></script>

	<!-- default js -->
	<script src="./js/default.js" type="text/javascript"></script>
</head>