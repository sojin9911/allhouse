<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// 임시비밀번호 발송 수단
$PasswordFindType = array();
if($siteInfo['s_find_pw_email'] == 'Y') $PasswordFindType[] = 'email';
if($siteInfo['s_find_pw_sms'] == 'Y') {

	// SMS정보 호출
	$SMSUser = onedaynet_sms_user();

	// SMS설정이 정상적이고 발송가능한 금액이 있다면 인증수단 추가
	if($SMSUser['code'] == 'U00' && $SMSUser['data'] > 10) {
		$smsInfo = _MQ(" select * from smart_sms_set where ss_uid = 'temp_password' limit 1 ");
		if($smsInfo['ss_status'] == 'Y') $PasswordFindType[] = 'sms';
	}
}

include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행