<?php
//<!-- <p>GetCashBillPrintURL - μΈμ νμ URL</p> -->
	$Result = $BaroService_CASHBILL->GetCashBillPrintURL(array(
		'CERTKEY'		=> $CERTKEY,
		'CorpNum'		=> $CorpNum,
		'UserID'		=> $ID,
		'PWD'			=> $PWD,
		'MgtKey'		=> $MgtKey
	))->GetCashBillPrintURLResult;

//	echo $Result;
?>