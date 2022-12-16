<?php

	include_once('inc.php');



	if($_mode == 'config') {

		$sque = "
			UPDATE smart_cntlog_config SET 
				clc_cookie_use='$_cookie_use',
				clc_cookie_term='$_cookie_term',
				clc_counter_use='$_counter_use',
				clc_total_num='$_total_num',
				clc_admin_check_use='$_admin_check_use',
				clc_admin_ip='$_admin_ip' 
			WHERE clc_uid='1'
		";
		_MQ_noreturn($sque);

	}
	else if($_mode == 'all') {

		_MQ_noreturn("truncate table smart_cntlog_age");
		_MQ_noreturn("truncate table smart_cntlog_browser");
		_MQ_noreturn("truncate table smart_cntlog_detail");
		_MQ_noreturn("truncate table smart_cntlog_device");
		_MQ_noreturn("truncate table smart_cntlog_ip");
		_MQ_noreturn("truncate table smart_cntlog_keyword");
		_MQ_noreturn("truncate table smart_cntlog_list");
		_MQ_noreturn("truncate table smart_cntlog_os");
		_MQ_noreturn("truncate table smart_cntlog_route");
		_MQ_noreturn("truncate table smart_cntlog_sex");

		_MQ_noreturn("UPDATE smart_cntlog_config SET clc_total_num = 0 WHERE clc_uid = '1'");

	}


	// 설정페이지 이동
	error_loc('_config.cntlog.form.php');