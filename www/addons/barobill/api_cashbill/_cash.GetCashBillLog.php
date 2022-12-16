<?php
//<!-- <p>GetCashBillLog - 문서 이력</p> -->
	$Result = $BaroService_CASHBILL->GetCashBillLog(array(
		'CERTKEY'	=> $CERTKEY,
		'CorpNum'	=> $CorpNum,
		'UserID'	=> $ID,
		'MgtKey'	=> $MgtKey
	))->GetCashBillLogResult->CashBillLog;

//	if (!is_array($Result) && $Result->Seq < 0){ //실패
//		echo $Result->Seq;
//	}else{ //성공
//		echo '<pre>';
//		print_r($Result);
//		echo '</pre>';
//	}
?>
