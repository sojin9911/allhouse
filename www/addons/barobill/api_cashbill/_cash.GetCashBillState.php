<?php
//<!-- <p>GetCashBillState - 문서 상태</p> -->
	$Result = $BaroService_CASHBILL->GetCashBillState(array(
		'CERTKEY'		=> $CERTKEY,
		'CorpNum'		=> $CorpNum,
		'UserID'		=> $ID,
		'MgtKey'		=> $MgtKey
	))->GetCashBillStateResult;

//	if ($Result->BarobillState < 0){ //실패
//		echo $Result->BarobillState;
//	}else{ //성공
//		echo '<pre>';
//		print_r($Result);
//		echo '</pre>';
//	}
?>