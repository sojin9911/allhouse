<?php
function UserDevice() {
	$Return = $ReturnText = array();
	include_once($_SERVER['DOCUMENT_ROOT'].'/addons/user_device/useragent.class.php');
	$useragent = UserAgentFactory::analyze($_SERVER['HTTP_USER_AGENT']);
	$Return['agent'] = $useragent->useragent;
	$Return['platform'] = $useragent->platform['title'];
	$Return['os'] = $useragent->os['title'];
	$Return['browser'] = $useragent->browser['title'];
	$Return['ip'] = $_SERVER['REMOTE_ADDR'];
	foreach($Return as $k=>$v) {

		$ReturnText[] = "[$k] {$v}";
	}
	return implode(PHP_EOL, $ReturnText);
}