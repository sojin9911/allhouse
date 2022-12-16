<?php
include_once('inc.php');

// --이미지 처리 ---
$s_favicon_file = _PhotoPro('../upfiles/banner', 's_favicon');
$s_home_icon_file = _PhotoPro('../upfiles/banner', 's_home_icon');
// --이미지 처리 ---

// -- URL 공유 파비콘 추가 -- 2019-05-23 LCY
$s_share_favicon_file = _PhotoPro('../upfiles/banner', 's_share_favicon');
// -- URL 공유 파비콘 추가 -- 2019-05-23 LCY

$sque = "
	update smart_setup set
		  s_device_mode = '".($s_device_mode?$s_device_mode:'A')."'
		, s_favicon = '{$s_favicon_file}'
		, s_home_icon = '{$s_home_icon_file}'

		, s_share_favicon = '{$s_share_favicon_file}' /*  // -- URL 공유 파비콘 추가 -- 2019-05-23 LCY */

	where
		s_uid = 1
";
_MQ_noreturn($sque);

// 설정페이지 이동
error_loc('_config.device.form.php');