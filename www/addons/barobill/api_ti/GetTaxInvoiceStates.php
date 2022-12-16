<? include '../include/top.php'; ?>
<p>GetTaxInvoiceStates - 문서 상태(대량, 100건 까지) (자체문서관리번호)</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtKeyList = array(	//자체문서관리번호 배열
					string => array(
						'',
						'',
						'',
						'',
						''
					)
				);

	$Result = $BaroService_TI->GetTaxInvoiceStates(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKeyList'	=> $MgtKeyList
				))->GetTaxInvoiceStatesResult->TaxInvoiceState;

	if (is_null($Result)){
		echo '문서 상태를 불러오지 못했습니다.';
	}else if (sizeof($Result) == 1){
		echo "$Result->MgtKey,
			$Result->InvoiceKey,
			$Result->BarobillState,
			$Result->IsOpened,
			$Result->Remark1,
			$Result->Remark2,
			$Result->NTSSendState,
			$Result->NTSSendKey,
			$Result->NTSSendResult,
			$Result->NTSSendDT,
			$Result->NTSResultDT";
	}else{
		foreach ($Result as $es){
			echo "$es->MgtKey,
				$es->InvoiceKey,
				$es->BarobillState,
				$es->IsOpened,
				$es->Remark1,
				$es->Remark2,
				$es->NTSSendState,
				$es->NTSSendKey,
				$es->NTSSendResult,
				$es->NTSSendDT,
				$es->NTSResultDT<br>";
		}
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
