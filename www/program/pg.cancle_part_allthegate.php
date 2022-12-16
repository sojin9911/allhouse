<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



// 거래번호
$rApprNo = $r[oc_tid]; // PG사 거래 번호

// 취소에 필요한 카드결제 정보 추출
$card_log_tmp = _MQ("select oc_content from smart_order_cardlog where oc_tid = '".$rApprNo."' and oc_uid = '".$r[oc_uid]."' limit 1");
$card_log_tmp = explode("§§",$card_log_tmp[oc_content]);
foreach($card_log_tmp as $tmp_val) {
	list($key,$val) = explode("||",$tmp_val);
	if($key) $card_log_value[$key] = $val;
}


# 거래번호가 없다면 취소 중단 2016-09-27 {
if($rApprNo == '' || $card_log_value["rDealNo"] == '') {

    $is_pg_status = false;
    return;
}
# 거래번호가 없다면 취소 중단 2016-09-27 }

#############################################################################################
## 올더게이트 결제 취소 START
#############################################################################################
require_once(PG_DIR."/Ags/lib/AGSLib.php");

$agspay = new agspay40;

$agspay->SetValue("AgsPayHome",PG_DIR."/Ags");     
$agspay->SetValue("log","true");																//true : 로그기록, false : 로그기록안함.
$agspay->SetValue("logLevel","DEBUG");															//로그레벨 : DEBUG, INFO, WARN, ERROR, FATAL (해당 레벨이상의 로그만 기록됨)
$agspay->SetValue("Type", "Cancel");															//고정값(수정불가)
$agspay->SetValue("RecvLen", 7);																//수신 데이터(길이) 체크 에러시 6 또는 7 설정. 

$agspay->SetValue("StoreId", $row_setup[P_ID]);													//상점아이디
$agspay->SetValue("AuthTy",  "card");															//결제형태
$agspay->SetValue("SubTy",   trim($card_log_value["SubTy"]));									//서브결제형태
$agspay->SetValue("rApprNo", trim($rApprNo));													//승인번호
$agspay->SetValue("rApprTm", trim( date('Ymd',strtotime($card_log_value["rApprTm"])) ));			//승인일자
$agspay->SetValue("rDealNo", trim($card_log_value["rDealNo"]));									//거래번호
$agspay->SetValue("PartCancelAmt",trim($_total_amount));										//부분취소금액

$agspay->startPay();

// 취소 성공 여부
$is_pg_status = $agspay->GetResult("rCancelSuccYn") == "y" ? true : false;

$_result_tid = $agspay->GetResult("rApprNo");
$_result_msg = $agspay->GetResult("rCancelResMsg");

// 취소결과 로그 기록
card_cancle_log_write($rApprNo,iconv("EUC-KR","UTF-8",$agspay->GetResult('rCancelResMsg')));	// 카드거래번호 , 결과 메세지



actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행