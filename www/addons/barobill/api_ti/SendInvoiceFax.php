<? include '../include/top.php'; ?>
<p>SendInvoiceFax - 팩스 전송 (문서이력에 기록됨)</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtKey = '';			//자체문서관리번호
	$SenderID = '';			//연계사업자 아이디
	$FromFaxNumber = '';	//발신자 팩스 번호
	$ToFaxNumber = '';		//수신자 팩스 번호

	$Result = $BaroService_TI->SendInvoiceFax(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtKey,
				'SenderID'		=> $SenderID,
				'FromFaxNumber'	=> $FromFaxNumber,
				'ToFaxNumber'	=> $ToFaxNumber
				))->SendInvoiceFaxResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//1-성공
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
