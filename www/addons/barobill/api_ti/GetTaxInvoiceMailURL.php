<? include '../include/top.php'; ?>
<p>GetTaxInvoiceMailURL - 이메일의 보기버튼 URL</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtKey = '';			//자체문서관리번호

	$Result = $BaroService_TI->GetTaxinvoiceMailURL(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtKey
				))->GetTaxinvoiceMailURLResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo "<a href=\"$Result\" target=\"_blank\">$Result</a>";
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
