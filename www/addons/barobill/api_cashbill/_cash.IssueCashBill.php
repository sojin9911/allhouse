<?php
//<!-- <p>IssueCashBill - 발행</p> -->
	$MailTitle = '';		//발행 알림메일의 제목 (공백이나 Null의 경우 바로빌 기본값으로 전송됨.)

	$Result = $BaroService_CASHBILL->IssueCashBill(array(
		'CERTKEY'		=> $CERTKEY,
		'CorpNum'		=> $CorpNum,
		'UserID'		=> $ID,
		'MgtKey'		=> $MgtKey,
		'SMSSendYN'		=> $SMSSendYN,
		'MailTitle'		=> $MailTitle
	))->IssueCashBillResult;

	//echo $Result;
?>