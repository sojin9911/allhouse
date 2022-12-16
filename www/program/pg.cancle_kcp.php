<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행





// 거래번호
$tno = $r[oc_tid]; // PG사 거래 번호

#############################################################################################
## KCP 결제 취소 START
#############################################################################################
require_once PG_DIR."/kcp/cfg/site_conf_inc.php";       // 환경설정 파일 include
require_once PG_DIR."/kcp/files/pp_ax_hub_lib.php";     // library [수정불가]

$c_PayPlus = new C_PP_CLI;
$c_PayPlus->mf_clear();

$tno     = trim($tno);
$tran_cd = "00200000";
$cust_ip = getenv("REMOTE_ADDR"); // 요청 IP

$c_PayPlus->mf_set_modx_data( "tno",      $tno                         );  // KCP 원거래 거래번호
$c_PayPlus->mf_set_modx_data( "mod_type", "STSC"                       );  // 원거래 변경 요청 종류
$c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip                     );  // 변경 요청자 IP
$c_PayPlus->mf_set_modx_data( "mod_desc", "cancel" );  // 변경 사유

$c_PayPlus->mf_do_tx( $tno,  $g_conf_home_dir, $g_conf_site_cd, $g_conf_site_key ,  $tran_cd,    "", $g_conf_gw_url,  $g_conf_gw_port,  "payplus_cli_slib", $ordernum, $cust_ip, "3", 0, 0, $g_conf_key_dir, $g_conf_log_dir);

$res_cd  = $c_PayPlus->m_res_cd;
$res_msg = iconv("euckr","utf8",$c_PayPlus->m_res_msg);	// 결과 메세지

// 취소 성공 여부
$is_pg_status = $res_cd == "0000" ? true : false;

// 발행된 현금영수증이 있으면 취소기록
if($is_pg_status){
	_MQ_noreturn(" update smart_baro_cashbill set BarobillState='6000', bc_iscancel='Y' where bc_ordernum='". $_ordernum ."' and bc_type='pg' and bc_isdelete='N' and bc_iscancel='N' ");
}

// 취소결과 로그 기록
card_cancle_log_write($tno,$res_msg);	// 카드거래번호 , 결과 메세지







actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행