<?php
include_once('inc.php');

$que = "
	update smart_setup set
		  npay_use 			= '{$npay_use}'
		, npay_mode			= '{$npay_mode}'
		, npay_id			= '{$npay_id}'
		, npay_all_key		= '{$npay_all_key}'
		, npay_key			= '{$npay_key}'
		, npay_bt_key		= '{$npay_bt_key}'
		, npay_sync_mode	= '{$npay_sync_mode}'
		, npay_lisense		= '{$npay_lisense}'
		, npay_secret		= '{$npay_secret}'
	where s_uid = '1'
";
_MQ_noreturn($que);


// 설정페이지 이동
error_loc('_config.pg_naver.form.php');