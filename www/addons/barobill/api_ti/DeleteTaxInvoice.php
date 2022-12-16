<? include '../include/top.php'; ?>
<p>DeleteTaxInvoice - 삭제 (자체문서관리번호)</p>
<div class="result">
	<?
	//$CERTKEY = '';			//인증키
	$CorpNum = '6102539436';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtKey = "20150909175754";			//자체문서관리번호

	$Result = $BaroService_TI->DeleteTaxInvoice(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtKey
				))->DeleteTaxInvoiceResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//1-임시저장건 삭제성공,  2-발행된건 삭제성공(삭제보관함으로 이동됨)
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
