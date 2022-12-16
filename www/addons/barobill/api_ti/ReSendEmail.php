<? include '../include/top.php'; ?>
<p>ReSendEmail - 이메일 재전송</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtKey = '';			//자체문서관리번호
	$ToEmailAddress = '';	//수신자 메일 주소

	$Result = $BaroService_TI->ReSendEmail(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtKey,
				'ToEmailAddress'=> $ToEmailAddress
				))->ReSendEmailResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//1-성공
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
