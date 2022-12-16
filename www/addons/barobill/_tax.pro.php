<?php


	# JJC001 - 세금계산서 연동처리
	include_once( dirname(__FILE__)."/../../include/inc.php");

	$_uid = $_uid ? $_uid : $uid;


	if($trigger_nomsg <>'Y'){ //=> 간편발행은 프로세스로 자동발행시에만 사용
		if($siteInfo['TAX_CHK'] <> "Y") {
			error_alt("세금계산서를 사용하는 상태가 아닙니다.");
		}
		if(!$mode) { error_alt("잘못된 접근입니다."); }
	}else{
		if($siteInfo['TAX_CHK'] <> "Y") { return; }
		if(!$mode) { return; }
	}



	include_once( dirname(__FILE__)."/include/BaroService_TI.php");
	include_once( dirname(__FILE__)."/include/var.php");


	// 세금계산서 발행정보 추출
	if(in_array($mode, array('regist', 'issue', 'quick', 'cancel', 'delete', 'info'))){

		// 유효성검사
		$taxInfo = _MQ(" select * from smart_baro_tax where bt_uid = '{$_uid}' ");
		if($trigger_nomsg <>'Y'){ //=> 간편발행은 프로세스로 자동발행시에만 사용
			if(!$taxInfo['bt_uid']) error_msg('잘못된 접근입니다.');
			if($taxInfo['bt_is_delete']=='Y') error_msg('이미 삭제된 세금계산서 입니다.');
		}else{
			if(!$taxInfo['bt_uid']) return;
			if($taxInfo['bt_is_delete']=='Y') return;
		}

	}



	// 세금계산서 상태에 따른 모듈 적용
	switch( $mode ){
		case "regist" :include_once( dirname(__FILE__)."/api_ti/_tax.RegistTaxInvoice.php");break;//세금계산서 임시저장 => 간편발행으로 통합
		case "issue" :include_once( dirname(__FILE__)."/api_ti/_tax.IssueTaxInvoice.php");break;//세금계산서 발행 => 간편발행으로 통합
		case "quick" :include( dirname(__FILE__)."/api_ti/_tax.RegistAndIssueTaxInvoice.php");break;//세금계산서 간편발행(임시저장+발행)
		case "cancel" :include( dirname(__FILE__)."/api_ti/_tax.ProcTaxInvoice.php");break;//세금계산서 발행취소
		case "delete" :include( dirname(__FILE__)."/api_ti/_tax.DeleteTaxInvoice.php");break;//세금계산서 삭제
		case "info" :include( dirname(__FILE__)."/api_ti/_tax.GetTaxInvoicePopUpURL.php");break;//세금계산서 삭제
		case "mass_print" :include( dirname(__FILE__)."/api_ti/_tax.GetTaxInvoicesPrintURL.php");break;//세금계산서 삭제
		case "print" :include( dirname(__FILE__)."/api_ti/_tax.GetTaxInvoicePrintURL.php");break;//세금계산서 삭제
	}


	// 상태값 추출
	if($MgtNum) {
		// 세금계산서 상태값 업데이트
		include_once( dirname(__FILE__)."/api_ti/_tax.GetTaxInvoiceState.php");
	}

