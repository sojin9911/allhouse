<? include '../include/top.php'; ?>
<p>SendInvoiceSMS - 문자 전송 (문서이력에 기록됨)</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$SenderID = '';			//연계사업자 아이디
	$MgtKey = '';			//자체문서관리번호
	$FromNumber = '';		//발신자 휴대폰 번호
	$ToNumber = '';			//수신자 휴대폰 번호
	$Contents = '';			//문자메세지 내용

	$Result = $BaroService_TI->SendInvoiceSMS(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'SenderID'		=> $SenderID,
				'MgtKey'		=> $MgtKey,
				'FromNumber'	=> $FromNumber,
				'ToNumber'		=> $ToNumber,
				'Contents'		=> $Contents
				))->SendInvoiceSMSResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//1-성공
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
