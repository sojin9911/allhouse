<?php
//<!-- <p>CancelCashBill - 발행취소</p> -->
	$CancelType = '1';		//취소사유 : 1-거래취소, 2-오류발행, 3-기타
	$MailTitle = '';		//취소 알림메일의 제목 (공백이나 Null의 경우 바로빌 기본값으로 전송됨.)

	$Result = $BaroService_CASHBILL->CancelCashBillBeforeNTSSend(array(
		'CERTKEY'		=> $CERTKEY,
		'CorpNum'		=> $CorpNum,
		'UserID'		=> $ID,
		'MgtKey'		=> $MgtKey
	))->CancelCashBillBeforeNTSSendResult;

//	echo $Result;
?>
