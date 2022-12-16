<?php
# NPay
if(!$_SERVER['DOCUMENT_ROOT']) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../'); // dirname(__FILE__) 다음 경로 주의
$_path_str = $_SERVER['DOCUMENT_ROOT'];
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/addons/npay/Nsync.class.php');

// LDD: 2019-01-18 네이버페이 패치
curl_async('http://'.$system['host'].OD_ADDONS_DIR.'/npay/_npay_order_sync.php?TYPE='.$TYPE);
die('RESULT=TRUE');
// LDD: 2019-01-18 네이버페이 패치