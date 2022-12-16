<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

$partnerData = array();
$partnerData['recaptchaUse'] = ($siteInfo['recaptcha_api'] != '' && $siteInfo['recaptcha_secret'] != '') ? true : false;
$partnerAgree = arr_policy('Y','partner_agree');
$partnerData['partnerAgree'] = strip_tags(stripslashes($partnerAgree['partner_agree']['po_content']));

include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행