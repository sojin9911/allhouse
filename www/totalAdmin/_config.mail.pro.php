<?php
include_once('inc.php');

if($_mode == 'onedaynet_check') { // 원데이넷 아이디 확인

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/nusoap.php');
	$client = new soapclientW('http://www.onedaynet.co.kr/mall/nusoap/member.chk.php');
	$err = $client->getError();
	if($err) die('Constructor error: '.$err);
	$result = $client->call('member_chk', array('_id_onedaynet'=>$_id, '_pw_onedaynet'=>$_pw));
	die($result);
}
else if($_mode == 'modify') { // 프리미엄 메일 설정 업데이트

	if($_mail_checking != 1) error_alt('원데이넷 아이디 확인을 통해 인증하시기 바랍니다.');
	_MQ_noreturn("
		update
			smart_setup
		set 
			s_mailid = '{$_mailid}',
			s_mailpw = '{$_mailpw}',
			s_mailuse = '{$_mailuse}'
		where
			s_uid = 1
	");

	// 설정페이지 이동
	error_frame_reload('수정되었습니다');
}