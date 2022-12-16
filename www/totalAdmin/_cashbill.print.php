<?PHP
	include_once("inc.php");

	// 연동사부여문서키 추출

	if($app_tax_mgtnum){
		$mode = "print";
		include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");
 
		if($Result < 0){
			error_msgPopup_s($arr_error_code[$Result]);
		}else{
			error_loc($Result);
		}

	}else{
		$arr_tax_mgtnum = $_mgtnum;
		$mode = "mass_print";
		include( OD_ADDONS_ROOT."/barobill/_cashbill.pro.php");
 
		if($Result < 0){
			error_msgPopup_s($arr_error_code[$Result]);
		}else{
			error_loc($Result);
		}
	}

?>