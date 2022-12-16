<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


$ocl = _MQ("select oc_tid from smart_order_cardlog where oc_oordernum = '".$_ordernum."' order by oc_uid desc limit 1");
$tid = $ocl[oc_tid]; // PG사 거래 번호
$siteInfo[s_pg_code_escrow] = $siteInfo[s_pg_code_escrow]?$siteInfo[s_pg_code_escrow]:$siteInfo[s_pg_code];
$_pg_mid = $ordr[paymethod]=='virtual'?$siteInfo[s_pg_code_escrow]:$siteInfo[s_pg_code];

require_once(PG_DIR."/inicis/libs/INILib.php");

// --------- JJC : 부분취소 개선 : 2021-02-10 ---------
//// 취소할 금액
//$tmp = _MQ(" select sum( op_price * op_cnt + op_delivery_price + op_add_delivery_price) as sum from smart_order_product where IF(op_cancel_type = 'pg' , op_cancel != 'Y' , 1 ) and op_oordernum = '".$_ordernum."' ");
//$tmp2 = _MQ(" select sum(op_usepoint) as sum from smart_order_product where op_oordernum = '".$_ordernum."' ");
//$_cancel_price = trim($_total_amount);
//$_confirm_price = ($tmp[sum] - $tmp2[sum] - $_cancel_price) > 0 ? ($tmp[sum] - $tmp2[sum] - $_cancel_price ) : 0;
// 취소금액
$_cancel_price = trim($_total_amount); 

// 승인금액
$tmp = _MQ("
	SELECT 
		IFNULL( (SUM( op_price * op_cnt + op_delivery_price + op_add_delivery_price - op_usepoint - op_use_discount_price) - SUM( cl_price )) ,0) AS sum
	FROM smart_order_product 
	LEFT JOIN (SELECT cl_price ,cl_oordernum , cl_pcode FROM smart_order_coupon_log WHERE cl_type = 'product' ) AS tbl ON (cl_oordernum = op_oordernum AND cl_pcode = op_pcode)
	WHERE 
		IF(op_cancel_type = 'pg' , op_cancel != 'Y' , 1 ) and 
		op_oordernum = '".$_ordernum."'
");
$_confirm_price = ($tmp['sum']- $_cancel_price) > 0 ? ($tmp['sum']- $_cancel_price) : 0;
// --------- JJC : 부분취소 개선 : 2021-02-10 ---------

$inipay = new INIpay50;

$inipay->SetField("inipayhome", PG_DIR."/inicis"); // 이니페이 홈디렉터리(상점수정 필요)
$inipay->SetField("type", "repay");      // 고정 (절대 수정 불가)
$inipay->SetField("pgid", "INIphpRPAY");      // 고정 (절대 수정 불가)
$inipay->SetField("subpgip","203.238.3.10"); 				// 고정
$inipay->SetField("debug", false);        // 로그모드("true"로 설정하면 상세로그가 생성됨.)
$inipay->SetField("mid", $_pg_mid);                         // 상점아이디
$inipay->SetField("admin", "1111");         //비대칭 사용키 키패스워드
$inipay->SetField("oldtid", $tid);            // 취소할 거래의 거래아이디
$inipay->SetField("currency", 'WON');     // 화폐단위
$inipay->SetField("price", $_cancel_price);      //취소금액
$inipay->SetField("confirm_price", $_confirm_price);      //승인요청금액

$inipay->startAction();

// 취소 성공 여부
$is_pg_status = $inipay->getResult('ResultCode') == "00" ? true : false;

// 취소결과 로그 기록
card_cancle_log_write($tid,iconv("euckr","utf8",$inipay->getResult('ResultMsg')));	// 카드거래번호 , 결과 메세지


actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행