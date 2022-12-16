<?php
include_once('inc.php');


// update
$sque = "
	update smart_setup set 
		  s_pg_paymethod_B = '{$s_pg_paymethod_B}'
		, s_pg_paymethod_C = '{$s_pg_paymethod_C}'
		, s_pg_paymethod_L = '{$s_pg_paymethod_L}'
		, s_pg_paymethod_V = '{$s_pg_paymethod_V}'
		, s_pg_paymethod_H = '{$s_pg_paymethod_H}'
	where
		s_uid = 1
	";
_MQ_noreturn($sque);


// 설정페이지 이동
error_loc('_config.paymethod.php');