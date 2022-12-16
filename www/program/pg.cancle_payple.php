<?php

	/*
		http://도메인/program/pg.cancle_payple.php
	*/

	include_once(dirname(__FILE__).'/inc.php');
	actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


	// 취소할 원거래일자 추출
	$oc_row = _MQ("SELECT * FROM smart_order_cardlog WHERE oc_oordernum =  '". addslashes($_ordernum) ."' AND oc_content LIKE 'PCD_PAY_RST||success§§%' LIMIT 1 ");
	$ex = explode("§§" , $str); $arr_occontent = array(); 
	foreach( $ex as $sk=>$sv ){  $ex2 = explode("||" , $sv);  $arr_occontent[$ex2[0]] = $ex2[1];  }
	$PCD_PAY_DATE = ($arr_occontent['PCD_PAY_TIME'] ? substr($arr_occontent['PCD_PAY_TIME'] , 0, 8) : DATE("Ymd" , strtotime($osr['o_paydate']))); // 8자리

	// 승인취소 요청금액 (기존 결제금액보다 적은 금액 입력 시 부분취소로 진행)
	$PCD_REFUND_TOTAL = $osr['o_price_real']; 

	// 결제 취소 모드 적용
	//			// 취소시에는 $res['json']으로 리턴받음
	//			$PCD_CST_ID = $res['json']->cst_id ;
	//			$PCD_CUST_KEY = $res['json']->custKey ;
	//			$PCD_CUST_KEY = $res['json']->AuthKey ;
	$app_mode = "cancel";
	include OD_ADDONS_ROOT."/payple/cPayCAct.php";


	// 취소 성공 여부
	$is_pg_status = $PCD_result_trigger == "Y" ? true : false;


	// 발행된 현금영수증이 있으면 취소기록
	if($is_pg_status){ _MQ_noreturn(" update smart_baro_cashbill set BarobillState='6000', bc_iscancel='Y' where bc_ordernum='". $_ordernum ."' and bc_type='pg' and bc_isdelete='N' and bc_iscancel='N' "); }

	// 취소결과 로그 기록
	_MQ_noreturn("update smart_order_cardlog set oc_cancle_content = '".$res_msg."' where oc_uid = '".$oc_row['oc_uid']."' ".($_ordernum ? "  and oc_oordernum = '". $_ordernum ."' " : ""));

	actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행