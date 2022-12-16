<?php


	// filename : inc.2year_opt.php
	// [x] inc.daily.update.php에 include 되어 1일 1회 실행됨.
	// [o] /program/_auto_load.php 에서 1일 1회 실행


	// 2016-05-18 ::: 매 2년마다 수신동의 설정 ----- 수신동의한지 2년이 넘은 회원 체크하여 - smart_2year_opt_log 에 데이터 등록
	include_once(dirname(__FILE__).'/inc.php');
	if($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']) return; // 동일 서버안에서만 동작 하도록 => curl_async을 통해서만 실행됨

	$row_setup = $siteInfo;

	// --- 매2년마다 수신동의 발송적용일 경우에만 적용 ---
	if($row_setup['s_2year_opt_use'] == "Y" && $row_setup['s_daily'] <> date('Y-m-d')) {

		// JJC : 수정 : 2021-05-17

		// 3년 지난 경우 삭제
		_MQ_noreturn(" DELETE FROM smart_2year_opt_log WHERE DATE_ADD(ol_rdate , interval + 3 year) <= CURDATE() ");

		// 중복 삭제 - 최대값 제외 삭제 - 기존 패치전 업체 적용 ( 1회 반영 )
		$que = " SELECT ol_mid , COUNT(*) AS cnt , MAX(ol_uid) AS ol_uid FROM smart_2year_opt_log GROUP BY ol_mid HAVING cnt > 1 ";
		$res = _MQ_assoc($que);
		foreach( $res as $k=>$v) { _MQ_noreturn(" DELETE FROM smart_2year_opt_log WHERE ol_mid = '". $v['ol_mid'] ."' AND ol_uid != '". $v['ol_uid'] ."' "); }

		//		$mr_sms_row = _MQ_assoc("
		//			select m.in_id , m.in_smssend , m.in_emailsend from smart_individual as m
		//			left join smart_2year_opt_log as ol on (ol.ol_mid = m.in_id)
		//			where 
		//				(ol.ol_uid is null or (ol.ol_uid is not null and DATE_ADD(ol_rdate , interval + 2 year) <= CURDATE())) and
		//				m.in_userlevel != '9' and 
		//				(in_smssend = 'Y' or in_emailsend = 'Y' ) and 
		//				DATE_ADD(m_opt_date , interval + 2 year) <= CURDATE()
		//		"); // 수신동의 2년 지난 - 회원 추출
		$mr_sms_row = _MQ_assoc("
			select 
				m.in_id , m.in_smssend , m.in_emailsend
			from smart_individual as m
			left join smart_2year_opt_log as ol on (ol.ol_mid = m.in_id AND DATE_ADD(ol_rdate , interval + 2 year) >= CURDATE() )
			where 
				ol.ol_uid is null and
				m.in_sleep_type = 'N' AND m.in_out = 'N' AND m.in_userlevel != '9' AND 
				(in_smssend = 'Y' or in_emailsend = 'Y' ) and 
				DATE_ADD(m_opt_date , interval + 2 year) <= CURDATE()
		"); // 수신동의 2년 지난 - 회원 추출
		// JJC : 수정 : 2021-05-17

		foreach($mr_sms_row as $mr_sms_k => $mr_sms_v){
			if( $mr_sms_v['in_emailsend'] == "Y" && $mr_sms_v['in_smssend'] == "Y" ) {$_type = "both";}
			else if( $mr_sms_v['in_emailsend'] == "Y" ) {$_type = "email";}
			else if( $mr_sms_v['in_smssend'] == "Y" ) {$_type = "sms";}
			$sque = " insert smart_2year_opt_log set  ol_mid = '". $mr_sms_v['in_id'] ."', ol_type = '". $_type ."', ol_rdate = now() ";
			_MQ_noreturn($sque);
		}


		// 오늘날짜 업데이트
		_MQ_noreturn("UPDATE smart_setup SET s_daily = '".date('Y-m-d')."' WHERE s_uid = 1 ");

	}
	// --- 매2년마다 수신동의 발송적용일 경우에만 적용 ---

?>