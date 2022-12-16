<?php
/**
*
*/

	# SSJ - 현금영수증 연동처리
	//include_once( dirname(__FILE__)."/../../../inc.php");
	include_once( dirname(__FILE__)."/include/var.php"); // 바로빌 공통 변수
	include_once( dirname(__FILE__)."/include/BaroService_CASHBILL.php");



	$MgtKey = $app_tax_mgtnum;			// 연동사부여 문서키 (자체문서관리번호)
	$MgtKeyList = $arr_tax_mgtnum;			// 연동사부여 문서키 배열 (배열형태로 넘어옴)


	//------------------------------------------------------------------------------------------------
//	 // 2017-01-13 $TradeMethod 변경으로 사용하지않음 SSJ
//	if($siteInfo['TAX_MODE'] == "test"){
//		$TradeMethod = 1;
//	}else{
//		$TradeMethod = 2;
//	}
	//------------------------------------------------------------------------------------------------

	// 2019-04-02 SSJ :: 경고문구 형태 구분
	if($siteInfo['TAX_CHK'] <> "Y") {
		$msg = '현금영수증을 사용하는 상태가 아닙니다.';
		if($trigger_msg === true){ echo $msg; exit;}
		else{ if($no_msg !== true) error_msg($msg); }
	}
	if(!$mode) {
		$msg = '잘못된 접근입니다.';
		if($trigger_msg === true){ echo $msg; exit; }
		else{ if($no_msg !== true) error_msg($msg); }
	}


	// 세금계산서 상태에 따른 모듈 적용
	switch( $mode ){

		// 현금영수증 임시저장
		case "check_key" :
			include( dirname(__FILE__)."/api_cashbill/_cash.CheckMgtKeyIsExists.php");
		break;

		// 현금영수증 임시저장
		case "save" :
			include( dirname(__FILE__)."/api_cashbill/_cash.RegistCashBill.php");
		break;

		// 현금영수증 정보확인
		case "chk_cashbill" :
			include( dirname(__FILE__)."/api_cashbill/_cash.GetCashBill.php");
		break;

		// 현금영수증 상태정보
		case "check_state" :
			include( dirname(__FILE__)."/api_cashbill/_cash.GetCashBillState.php");
		break;

		// 현금영수증 상태정보
		case "mass_state" :
			include( dirname(__FILE__)."/api_cashbill/_cash.GetCashBillStates.php");
		break;

		// 현금영수증의 정보를 양식 형태로출력하는 URL 반환
		case "popup" :
			include( dirname(__FILE__)."/api_cashbill/_cash.GetCashBillPopUpURL.php");
		break;

		// 현금영수증을 인쇄할 수 있는 URL
		case "print" :
			include( dirname(__FILE__)."/api_cashbill/_cash.GetCashBillPrintURL.php");
		break;

		// 현금영수증을 인쇄할 수 있는 URL
		case "mass_print" :
			include( dirname(__FILE__)."/api_cashbill/_cash.GetCashBillsPrintURL.php");
		break;

		// 현금영수증을 내용 수정
		case "update" :
			include( dirname(__FILE__)."/api_cashbill/_cash.UpdateCashBill.php");
		break;

		// 현금영수증을 발행
		case "issue" :
			include( dirname(__FILE__)."/api_cashbill/_cash.IssueCashBill.php");
		break;

		// 현금영수증 이력정보 확인
		case "info" :
			include( dirname(__FILE__)."/api_cashbill/_cash.GetCashBillLog.php");
		break;

		// 현금영수증 삭제 - 임시저장, 취소상태일때
		case "delete" :
			include( dirname(__FILE__)."/api_cashbill/_cash.DeleteCashBill.php");
		break;

		// 현금영수증 삭제 국세청으로 전송후 취소
		case "cancel" :
			include( dirname(__FILE__)."/api_cashbill/_cash.CancelCashBill.php");
		break;

		// 현금영수증 삭제 국세청으로 전송전 취소
		case "cancelbeforesend" :
			include( dirname(__FILE__)."/api_cashbill/_cash.CancelCashBillBeforeNTSSend.php");
		break;
	}

?>