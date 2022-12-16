<?php

	include_once( dirname(__FILE__)."/../include/BaroService_TI.php");
	include_once( dirname(__FILE__)."/../include/var.php");

	$CERTKEY = $siteInfo['TAX_CERTKEY'];			//인증키
	$CorpNum = rm_str($siteInfo['s_company_num']); //연계사업자 사업자번호 ('-' 제외, 10자리)

	$MgtNum = $app_tax_mgtnum;			//자체문서관리번호

	$ID = $siteInfo['TAX_BAROBILL_ID'];				//연계사업자 아이디
	$PWD = $siteInfo['TAX_BAROBILL_PW'];				//연계사업자 비밀번호

	if($MgtNum && $siteInfo['TAX_CERTKEY'] && $CorpNum && $ID && $PWD) {

		$Result = $BaroService_TI->GetTaxInvoicePrintURL(array(
					'CERTKEY'		=> $CERTKEY,
					'CorpNum'		=> $CorpNum,
					'MgtKey'		=> $MgtNum,
					'ID'			=> $ID,
					'PWD'			=> $PWD
					))->GetTaxInvoicePrintURLResult;

	}
