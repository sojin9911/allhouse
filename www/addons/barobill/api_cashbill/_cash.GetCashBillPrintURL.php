<?php
//<!-- <p>GetCashBillPrintURL - 인쇄 팝업 URL</p> -->
	$Result = $BaroService_CASHBILL->GetCashBillPrintURL(array(
		'CERTKEY'		=> $CERTKEY,
		'CorpNum'		=> $CorpNum,
		'UserID'		=> $ID,
		'PWD'			=> $PWD,
		'MgtKey'		=> $MgtKey
	))->GetCashBillPrintURLResult;

//	echo $Result;
?>