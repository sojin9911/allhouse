<?php
//<!-- <p>GetCashBillStates - 문서 상태(대량, 100건 까지)</p> -->
	$Result = $BaroService_CASHBILL->GetCashBillStates(array(
		'CERTKEY'		=> $CERTKEY,
		'CorpNum'		=> $CorpNum,
		'UserID'		=> $ID,
		'MgtKeyList'	=> $MgtKeyList
	))->GetCashBillStatesResult->CashBillState;

//	if (!is_array($Result) && $Result->BarobillState < 0){ //실패
//		echo $Result->BarobillState;
//	}else{ //성공
//		echo '<pre>';
//		print_r($Result);
//		echo '</pre>';
//	}
?>