<?PHP
	include_once("inc.php");

	// 연동사부여문서키 추출
	if($app_tax_mgtnum){
		$mode = "popup";
		include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");

		if($Result < 0){
			error_msgPopup_s($arr_error_code[$Result]);
		}else{
			error_loc($Result);
		}

	}else{
		error_msgPopup_s('잘못된 접근입니다.');
	}


?>