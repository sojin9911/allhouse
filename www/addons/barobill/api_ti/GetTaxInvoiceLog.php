<? include '../include/top.php'; ?>
<p>GetTaxInvoiceLog - 문서 이력 (자체문서관리번호)</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtKey = '';			//자체문서관리번호

	$Result = $BaroService_TI->GetTaxInvoiceLog(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtKey
				))->GetTaxInvoiceLogResult->InvoiceLog;

	if (is_null($Result)){
		echo '문서 이력을 불러오지 못했습니다.';
	}else if (sizeof($Result) == 1){
		echo "$Result->Seq,
			$Result->LogType,
			$Result->ProcCorpName,
			$Result->ProcContactName,
			$Result->LogDateTime,
			$Result->Memo";
	}else{
		foreach ($Result as $il){
			echo "$il->Seq,
				$il->LogType,
				$il->ProcCorpName,
				$il->ProcContactName,
				$il->LogDateTime,
				$il->Memo<br>";
		}
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
