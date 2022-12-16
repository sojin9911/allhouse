<? include '../include/top.php'; ?>
<p>GetTaxInvoicePrintURL - 인쇄 팝업 URL (자체문서관리번호)</p>
<div class="result">
	<?
	//$CERTKEY = '';			//인증키
	$CorpNum = '6102539436';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtKey = "20150909175754";			//자체문서관리번호
	$ID = 'ssnumer';				//연계사업자 아이디
	$PWD = 'ssnumer0501';				//연계사업자 비밀번호

	$Result = $BaroService_TI->GetTaxInvoicePrintURL(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtKey,
				'ID'			=> $ID,
				'PWD'			=> $PWD
				))->GetTaxInvoicePrintURLResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo "<a href=\"$Result\" target=\"_blank\">$Result</a>";
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
