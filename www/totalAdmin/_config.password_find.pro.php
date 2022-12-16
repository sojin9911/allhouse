<?php
include_once('inc.php');

$s_find_pw_email = ($_find_pw_email?$_find_pw_email:'N');
$s_find_pw_sms = ($_find_pw_sms?$_find_pw_sms:'N');
if($s_find_pw_email == 'N' && $s_find_pw_sms == 'N') error_loc_msg('_config.password_find.config.php', '비밀번호 찾기 설정 시 이메일 또는 휴대전화 중 1건 이상 사용처리를 하여야 합니다.');


// 설정값 업데이트
$que = "
	update smart_setup set
		  s_find_pw_email = '{$s_find_pw_email}'
		, s_find_pw_sms = '{$s_find_pw_sms}'
	where s_uid = '1'
";
_MQ_noreturn($que);


// 설정페이지 이동
error_loc('_config.password_find.config.php');