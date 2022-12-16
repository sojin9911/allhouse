<?php
define('_OD_DIRECT_', true); // 개별 실행허용
if(!$_SERVER['DOCUMENT_ROOT']) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../'); // dirname(__FILE__) 다음 경로 주의
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');