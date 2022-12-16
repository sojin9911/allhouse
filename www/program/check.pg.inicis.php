<?php
include_once(dirname(__FILE__).'/inc.php');
/* INIinquiry.php
 *
 * 이미 승인된 지불을 확인한다.
 *
 * Date : 2012/04
 * Author : mi@inicis.com
 * Project : INIpay V5.0 for PHP
 *
 * http://www.inicis.com
 * Copyright (C) 2007 Inicis, Co. All rights reserved.
 */

/* * ************************
 * 1. 라이브러리 인클루드 *
 * ************************ */
require_once(PG_DIR ."/inicis/libs/INILib.php");

$_pg_mid =trim($siteInfo[s_pg_code]);
$card_fail_order = _MQ_assoc(" select o_ordernum, o_paymethod from smart_order where o_paymethod in('card', 'iche') and o_paystatus='N' and o_canceled = 'N' and npay_order = 'N' and o_rdate >= '". date("Y-m-d", strtotime("-1 days"))." 00:00:00' ");
foreach( $card_fail_order as $k => $v){

	$card_log = _MQ(" select oc_tid from smart_order_cardlog where oc_oordernum ='".$v['o_ordernum']."' ");

	$tid = $card_log['oc_tid'];
	if($tid == '' ){ continue; }

	$oid = $v['o_ordernum'];


/* * *************************************
 * 2. INIpay41 클래스의 인스턴스 생성 *
 * ************************************* */
$inipay = new INIpay50;

/* * *******************
 * 3. 조회 정보 설정 *
 * ******************* */
//$inipay->SetField("inipayhome", "/home/www/INIpay50");          // 이니페이 홈디렉터리(상점수정 필요)
$inipay->SetField("inipayhome", PG_DIR."/inicis/");          // 이니페이 홈디렉터리(상점수정 필요)
$inipay->SetField("type", "inquiry");                            // 고정 (절대 수정 불가)
$inipay->SetField("debug", false);                             // 로그모드("true"로 설정하면 상세로그가 생성됨.)
$inipay->SetField("mid", $_pg_mid);                                 // 상점아이디
/* * ************************************************************************************************
 * admin 은 키패스워드 변수명입니다. 수정하시면 안됩니다. 1111의 부분만 수정해서 사용하시기 바랍니다.
 * 키패스워드는 상점관리자 페이지(https://iniweb.inicis.com)의 비밀번호가 아닙니다. 주의해 주시기 바랍니다.
 * 키패스워드는 숫자 4자리로만 구성됩니다. 이 값은 키파일 발급시 결정됩니다.
 * 키패스워드 값을 확인하시려면 상점측에 발급된 키파일 안의 readme.txt 파일을 참조해 주십시오.
 * ************************************************************************************************ */
$inipay->SetField("admin", "1111");
$inipay->SetField("tid", $tid);                                 // 확인할 거래의 거래아이디
$inipay->SetField("oid", $oid);                                 // 확인할 거래의 주문번호

/* * **************
 * 4. 조회 요청 *
 * ************** */
$inipay->startAction();

/* * **************************************************************
 * 5. 조회 결과
 * 결과코드 : $inipay->getResult('ResultCode') ("00"이면 조회 성공)
 * 결과내용 : $inipay->getResult('ResultMsg')
 * 거래번호 : $inipay->getResult('INQR_TID')
 * 거래금액 : $inipay->getResult('INQR_Price')
 * 거래상태 : $inipay->getResult('INQR_Status')
 *
 * 지불수단 : $inipay->getResult('INQR_Paymethod')
 * 주문번호 : $inipay->getResult('INQR_OID')
 * 승인번호 : $inipay->getResult('INQR_Applnum')
 * 승인일자 : $inipay->getResult('INQR_ApplDate')
 * 승인시각 : $inipay->getResult('INQR_ApplTime')
 * 원화승인금액 : $inipay->getResult('INQR_PriceExchange')
 * 환율 : $inipay->getResult('INQR_RtExchange')
 * ************************************************************** */

// LCY : 결제 승인 여부 체크 추가 : 2021-10-21
if( $inipay->getResult('ResultCode') == '00' && $inipay->getResult('INQR_Status') == '0'  ){
    $arr_oc_content = array(
		"tid" => "INQR_TID",
		"resultCode" => "ResultCode",
		"resultMsg" => "ResultMsg",
		"MOID" => "INQR_OID",
		"applDate" => "INQR_ApplDate",
		"applTime" => "INQR_ApplTime",
		"applNum" => "INQR_Applnum",
		"payMethod" => "INQR_Paymethod",
		"TotPrice" => "INQR_Price",
		"EventCode" => "EventCode",

	);

	$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
	foreach($arr_oc_content as $name => $value) {
		$app_oc_content .= $name . "||" . iconv("euc-kr","utf-8",$inipay->getResult($value)) . "§§" ; // 데이터 저장
	}

	$ordernum = $oid;
	// - 주문결제기록 저장 ---
	$que = "
		insert smart_order_cardlog set
			 oc_oordernum = '".$ordernum."'
			,oc_tid = '". $tid."'
			,oc_content = '". $app_oc_content ."'
			,oc_rdate = now();
	";

	if(!preg_match('/중복/i' , $app_oc_content))  _MQ_noreturn($que);

	include OD_PROGRAM_ROOT."/shop.order.result.pro.php";

}

}

?>