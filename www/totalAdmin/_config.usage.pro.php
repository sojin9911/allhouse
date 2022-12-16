<?php
include_once('inc.php');

// 설정값 업데이트
$que = "
	update smart_setup set 
		s_information_use_pc = '".$s_information_use_pc."',
		s_information_use_mobile = '".$s_information_use_mobile."',
		s_leave_guidance = '".$s_leave_guidance."'
	where 
		s_uid = 1
";
_MQ_noreturn($que);

// 설정페이지 이동
error_loc('_config.usage.php');