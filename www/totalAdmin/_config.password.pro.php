<?php
include_once('inc.php');

// 설정값 업데이트
$que = "
	update smart_setup set
		member_cpw_period = '{$member_cpw_period}'
	where s_uid = '1'
";
_MQ_noreturn($que);


// 설정페이지 이동
error_loc('_config.password.config.php');