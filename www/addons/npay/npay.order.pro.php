<?php
# NPay
if(!$_SERVER['DOCUMENT_ROOT']) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../'); // dirname(__FILE__) 다음 경로 주의
$_path_str = $_SERVER['DOCUMENT_ROOT'];
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/addons/npay/Nsync.class.php');

# 발주처리의 경우
if($_mode == 'PlaceProductOrder') {

	$Result = $NSync->OrderInfoChange('place', array('code'=>$code));
	error_loc_msg('/'.$path.'/_npay_order.form.php?_mode=modify&_uid='.$_uid.'&_PVSC='.$_PVSC, "네이버페이로 발주처리 요청이 완료 되었습니다.","top");
	exit;
}

/*
# 전달 가능한 $type 값
express => 발송 처리 ★
cancel => 취소 ★

# 동작구분
$expressnum or $n_num (네이버 테스트용 _GET 변수) 값이 있는경우 express 아닌경우 cancel
*/
$NPayOrderActionType = ($expressname != '' || trim($n_num) != ''?'express':'cancel');
$NPaySendData = array();
if($NPayOrderActionType == 'express') { // 배송처리

	if(!is_array($op_uid)) { $op_uid = array($op_uid); }
	if(!is_array($expressname)) { $expressname = array($expressname); }
	if(!is_array($expressnum)) { $expressnum = array($expressnum); }
	$NPaySendData['op_uid'] = $op_uid;
	$NPaySendData['expressname'] = $expressname;
	$NPaySendData['expressnum'] = $expressnum;
	$NPaySendData['n_name'] = $n_name;
	$NPaySendData['n_num'] = $n_num;
	$NPaySendData['npay_code'] = $npay_code;

	// 실행
	$Result = $NSync->OrderInfoChange($NPayOrderActionType, $NPaySendData);

	if($Result['Envelope']['Body']['ShipProductOrderResponse']['ResponseType'] == 'ERROR') { // 에러발생

		error_loc_msg('/'.$path.'/_npay_order.form.php?_mode=modify&_uid='.$NPaySendData['op_uid'][0].'&_PVSC='.$_PVSC, "[{$Result['Envelope']['Body']['ShipProductOrderResponse']['Error']['Code']}] ".$Result['Envelope']['Body']['ShipProductOrderResponse']['Error']['Message'],"top");
	} else {

		error_loc_msg('/'.$path.'/_npay_order.form.php?_mode=modify&_uid='.$NPaySendData['op_uid'][0].'&_PVSC='.$_PVSC, "네이버페이로 발송처리 요청이 완료 되었습니다.","top");
	}
}
else { // 취소처리

	if(trim($npay_code) != '') $ordr = _MQ(" select * from `smart_order_product` where `npay_order_code` = '{$npay_code}' ");
	else $ordr = _MQ(" select * from `smart_order_product` where `op_uid` = '{$_uid}' ");
	$NPaySendData['ordr'] = $ordr;

	// 실행
	$NSync->OrderInfoChange($NPayOrderActionType, $NPaySendData);
	error_loc_msg('/'.$path.'/_npay_order.form.php?_mode=modify&_uid='.$_uid.'&_PVSC='.$_PVSC, "네이버페이로 취소처리 요청이 완료 되었습니다.","top");
}