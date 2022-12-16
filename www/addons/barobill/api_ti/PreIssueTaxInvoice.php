<? include '../include/top.php'; ?>
<p>PreIssueTaxInvoice - 발행예정</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtKey = '';			//자체문서관리번호
	$SendSMS = false;		//발행 알림문자 전송여부 (발행비용과 별도로 과금됨)
	$MailTitle = '';		//발행 알림메일의 제목 (공백이나 Null의 경우 바로빌 기본값으로 전송됨.)

	$Result = $BaroService_TI->PreIssueTaxInvoice(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtKey,
				'SendSMS'		=> $SendSMS,
				'MailTitle'		=> $MailTitle
				))->PreIssueTaxInvoiceResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//1-성공
						//2-성공(포인트 부족으로 SMS 전송실패)
						//3-성공(이메일 전송실패, ReSendEmail 함수로 재전송 하십시오.)
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
