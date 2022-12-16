<?php
	if(!$siteInfo){
		include_once( dirname(__FILE__)."/../../../include/inc.php");
	}
	include_once( dirname(__FILE__)."/../include/BaroService_TI.php");
	include_once( dirname(__FILE__)."/../include/var.php");

	$CERTKEY = $siteInfo['TAX_CERTKEY'];			//인증키
	$CorpNum = preg_replace("/[^0-9]/i", "", $siteInfo['s_company_num']); //연계사업자 사업자번호 ('-' 제외, 10자리)
	$ID = $siteInfo['TAX_BAROBILL_ID'];				//연계사업자 아이디
	$PWD = $siteInfo['TAX_BAROBILL_PW'];				//연계사업자 비밀번호


	$Result = $BaroService_TI->GetCashChargeURL(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'ID'		=> $ID,
				'PWD'		=> $PWD,
				))->GetCashChargeURLResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
		$return_balance = "오류발생(". $Result .")";
		echo "<script>alert('".$return_balance."');</script>";
	}else{
		error_loc($Result);
	}

?>