<?php
header('Content-Type: text/html; charset=utf8');
session_start();
include_once(dirname(__FILE__).'/inc.php');
include_once(dirname(__FILE__).'/totalCashReceipt.php');
echo $return;