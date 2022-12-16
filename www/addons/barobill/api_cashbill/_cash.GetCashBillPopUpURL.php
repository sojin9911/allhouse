<?php
//<!-- <p>GetCashBillPopUpURL - 문서 내용보기 팝업 URL</p> -->
	$Result = $BaroService_CASHBILL->GetCashBillPopUpURL(array(
		'CERTKEY'		=> $CERTKEY,
		'CorpNum'		=> $CorpNum,
		'UserID'		=> $ID,
		'PWD'			=> $PWD,
		'MgtKey'		=> $MgtKey
	))->GetCashBillPopUpURLResult;
	
	//echo $Result;
?>
