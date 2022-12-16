<?php
# NPay
if(!$_SERVER['DOCUMENT_ROOT']) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../../'); // dirname(__FILE__) 다음 경로 주의
$_path_str = $_SERVER['DOCUMENT_ROOT'];
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/addons/npay/Nsync.class.php');
unset($NSync);
$NSync = new Sync($Npay, $siteInfo['npay_lisense'], $siteInfo['npay_secret'], 'test');

$GetChangedProductOrderList = $NSync->GetChangedProductOrderList('', '', 'PAYED'); // 결제 된 주문조회
$GetProductOrderInfoList = $NSync->GetProductOrderInfoList($GetChangedProductOrderList[0]['ProductOrderID']); // 최근 1건의 상세 정보

if($mode == 'phpinfo') {

	phpinfo();
	exit;
}
else if($mode == 'GetProductOrderInfoList') { // 최근 이력 2012-01-01T00:00:00+09:00 ~ 2012-01-02T00:00:00+09:00

	# 이력조회
	ViewArr($GetProductOrderInfoList);
	exit;
}
else if($mode == 'GetChangedProductOrderList') { // 최근 1건의 상세 정보

	# 상세 조회
	ViewArr($GetChangedProductOrderList);
	exit;
}
else if($mode == 'CancelSale') { // 취소처리

	# 취소처리
	$Cancel = $NSync->CancelSale($GetChangedProductOrderList[0]['ProductOrderID']);
	ViewArr($Cancel);

	# 주문 상세 다시 조회
	echo '변경조회<hr>';
	$GetProductOrderInfoList = $NSync->GetProductOrderInfoList($GetChangedProductOrderList[0]['ProductOrderID']); // 최근 1건의 상세 정보
	ViewArr($GetProductOrderInfoList);
	exit;
}
else if($mode == 'ShipProductOrder') { // 배송처리

	# 배송처리
	echo '배송처리<hr>';
	$Ship = $NSync->ShipProductOrder($GetChangedProductOrderList[0]['ProductOrderID'], '기타 택배', '123-456-7890');
	ViewArr($Ship);

	# 주문 상세 다시 조회
	echo '변경조회<hr>';
	$GetProductOrderInfoList = $NSync->GetProductOrderInfoList($GetChangedProductOrderList[0]['ProductOrderID']);
	ViewArr($GetProductOrderInfoList);
	exit;
}