<?php
//<!-- <p>GetCashBillsPrintURL - 대량인쇄 팝업 URL</p> -->
	$Result = $BaroService_CASHBILL->GetCashBillsPrintURL(array(
		'CERTKEY'		=> $CERTKEY,
		'CorpNum'		=> $CorpNum,
		'UserID'		=> $ID,
		'PWD'			=> $PWD,
		'MgtKeyList'	=> $MgtKeyList
	))->GetCashBillsPrintURLResult;

//	echo $Result;
?>
