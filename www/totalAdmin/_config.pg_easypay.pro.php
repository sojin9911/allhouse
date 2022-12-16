<?php 
	include "./inc.php";

/*
	payco_use = 페이코 사용여부 
	payco_mode	= 페이코 활성화 여부
	payco_sellerkey = 페이코 가맹점 코드
	payco_cpid = 페이코 상점 ID
	payco_productid = 페이코 상품 ID
	payco_app_scheme = 페이코 app 스키마
	payco_paymethod = 페이코 결제수단 콤마로 구분(,) var.php 에 코드 정의
*/


	// -- 페이코의 활성화 모드가 테스트라면 테스트 키를 자동으로 삽입하도록 결제 작업시 진행해야한다.
	// $payco_sellerKey = $arrPaycoInfo['test']['sellerKey'];
	// $payco_cpid = $arrPaycoInfo['test']['cpId'];
	// $payco_productid = $arrPaycoInfo['test']['productId'];

	// -- 결제 수단의 경우 FROM 에서 체킹하지만 오류로 인해 안될 수 있으니 데이터 체크하여 가공
	$payco_paymethod = is_array($payco_paymethod) == true && count($payco_paymethod) > 0 ? implode(",",$payco_paymethod) : null;


	$sque = "
		payco_use = '".$payco_use."'
		, payco_mode = '".$payco_mode."'
		, payco_sellerkey = '".trim($payco_sellerkey)."'
		, payco_cpid = '".trim($payco_cpid)."'
		, payco_productid = '".trim($payco_productid)."'
		, payco_paymethod	= '".trim($payco_paymethod)."' 
		, payco_app_scheme = '".trim($payco_app_scheme)."'

		/* -- JJC : 간편결제 - 페이플 : 2021-06-05 -- */
		, s_payple_use = '".$payple_use."'
		, s_payple_mode = '".$payple_mode."'
		, s_payple_cst_id = '".trim($payple_cst_id)."'
		, s_payple_custKey = '".trim($payple_custKey)."'
		, s_payple_cancelKey = '".trim($payple_cancelKey)."'

	";

	_MQ_noreturn("update smart_setup set ".$sque." where s_uid = '1' ");

	error_loc("_config.pg_easypay.form.php");

?>