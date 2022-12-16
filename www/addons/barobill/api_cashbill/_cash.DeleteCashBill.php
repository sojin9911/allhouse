<?php
//<!-- <p>DeleteCashBill - 삭제</p> -->
	$Result = $BaroService_CASHBILL->DeleteCashBill(array(
		'CERTKEY'	=> $CERTKEY,
		'CorpNum'	=> $CorpNum,
		'UserID'	=> $ID,
		'MgtKey'	=> $MgtKey
	))->DeleteCashBillResult;

//	echo $Result;
?>