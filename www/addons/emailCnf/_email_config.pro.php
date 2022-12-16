<?PHP

	// 080 수신거부 설정 적용
	include_once("inc.php");

	$que = "
		update smart_setup set 
			s_set_email_txt = '". $_set_email_txt ."'
		where 
			s_uid = 1
	"; 
	_MQ_noreturn($que);

	error_loc("/totalAdmin/_addons.php?_menuType=smsEmail&pass_menu=emailCnf/_email_config.form");

?>