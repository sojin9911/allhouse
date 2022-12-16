<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

$tid = $r[oc_tid]; // PG사 거래 번호

// @ 2017-02-22 LCY :: 에스크로 주문건이라면
if( preg_match("/(".$siteInfo[s_pg_code_escrow].")/",$tid ) === true && $siteInfo[s_pg_code_escrow] != ''){
	$siteInfo[s_pg_code] = $siteInfo[s_pg_code_escrow];
}

include_once(PG_DIR."/inicis/libs/INILib.php");

$inipay = new INIpay50;
$inipay->SetField("inipayhome", PG_DIR."/inicis"); // 이니페이 홈디렉터리(상점수정 필요)
$inipay->SetField("type", "cancel");                            // 고정 (절대 수정 불가)
$inipay->SetField("debug", false);                             // 로그모드("true"로 설정하면 상세로그가 생성됨.)
$inipay->SetField("mid", $siteInfo[s_pg_code]);                         // 상점아이디
$inipay->SetField("admin", "1111");                             // 비대칭 사용키 키패스워드
$inipay->SetField("tid", $tid);                     // 취소할 거래의 거래아이디
$inipay->SetField("cancelmsg", "normal");                             // 취소사유

$inipay->startAction();

// 취소 성공 여부
$is_pg_status = $inipay->getResult('ResultCode') == "00" ? true : false;

// 발행된 현금영수증이 있으면 취소기록
if($is_pg_status){
	_MQ_noreturn(" update smart_baro_cashbill set BarobillState='6000', bc_iscancel='Y' where bc_ordernum='". $_ordernum ."' and bc_type='pg' and bc_isdelete='N' and bc_iscancel='N' ");
}

// 취소결과 로그 기록
card_cancle_log_write($tid,iconv("euckr","utf8",$inipay->getResult('ResultMsg')));	// 카드거래번호 , 결과 메세지

actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행