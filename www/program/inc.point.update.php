<?php
# ------------------------------> DEV 변경 하세요. <---------------------- :: 날짜를 DB화 처리
# 포인트 & 휴면 & 자동정산 처리
@include_once($_SERVER['DOCUMENT_ROOT']."/upfiles/normal/point.update.date.php");
// 하루 한번 실행한다.
if($last_update_date != date("Y-m-d")) {

	// 오늘 날짜를 입력한다.
	$fp = fopen($_SERVER['DOCUMENT_ROOT']."/upfiles/normal/point.update.date.php", "w");
	fputs($fp,"<?PHP\n\t\$last_update_date = '".date("Y-m-d")."';?>");
	fclose($fp);

	point_update();
	coupon_update();
	member_sleep_backup();  // 휴면계정 처리

	include_once(OD_PROGRAM_ROOT.'/inc.settle.update.php'); // 자동정산처리

	// -- 보안서버 상태정보 체크 ---
	/*$arr = ssl_condition_info();
	$ssl_condition = CurlExecHeader( $arr['ssl_domain'].'/program/_ping.php' ); // 200 이 아니면 비정상
	if($ssl_condition <> 200 ){
		// 접속비정상일 경우 비적용함..
		_MQ_noreturn(" update smart_setup set s_ssl_check = 'N', s_ssl_admin_loc = 'N', s_ssl_pc_loc = 'N', s_ssl_m_loc = 'N' where s_uid = 1 ");
	}*/
	// -- 보안서버 상태정보 체크 ---
}