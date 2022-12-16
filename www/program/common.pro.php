<?php
include_once(dirname(__FILE__).'/inc.php');
if(!$_mode) exit;
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// 공통 프로세스 파일
if($_mode == 'popup_close') { // 팝업닫기
	if(!$uid) exit;

	$app_div_name = "event_popup_div_" . $uid;

	// 쿠키 적용
	samesiteCookie('AuthPopupClose_'. $uid, 'Y', time() +3600 * 24, '/', '.'.str_replace('www.', '', reset(explode(':', $system['host']))));
	die("<script>parent.document.getElementById('{$app_div_name}').style.display = 'none';</script>");
}
else if($_mode == 'latest_del') { // 최근본상품 삭제
	if(!$uid) exit;
	_MQ_noreturn(" delete from smart_product_latest where pl_uid='{$uid}' ");
	die("<script>parent.latest_view();</script>");
}

actionHook(basename(__FILE__).'.end'); // 해당 파일 시작에 대한 후킹액션 실행