<?php
include_once('inc.php');

_MQ_noreturn("
	update smart_setup set
		  s_join_auth_use = '".($_join_auth_use?$_join_auth_use:'N')."'
		, s_join_auth_kcb_code = '{$_join_auth_kcb_code}'
	where s_uid = '1'
");


// 설정페이지 이동
error_loc('_config.member.form.php');