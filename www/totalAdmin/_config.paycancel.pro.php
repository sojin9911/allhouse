<?php
include_once('inc.php');


// update
$sque = "
	update smart_setup set 
		  s_paycancel_method = '{$s_paycancel_method}'
	where
		s_uid = 1
	";
_MQ_noreturn($sque);


// 설정페이지 이동
error_loc('_config.paycancel.php');