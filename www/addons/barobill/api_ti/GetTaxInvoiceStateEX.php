<? include '../include/top.php'; ?>
<p>GetTaxInvoiceStateEX - 문서 상태 (수신확인, 등록일시, 작성일자, 발행예정일시, 발행일시 추가)</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtKey = '';			//자체문서관리번호

	$Result = $BaroService_TI->GetTaxInvoiceStateEX(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtKey
				))->GetTaxInvoiceStateEXResult;

	if (is_null($Result)){
		echo '문서 상태를 불러오지 못했습니다.';
	}else if ($Result->BarobillState < 0){
		echo '오류코드 : $Result->BarobillState<br><br>'.getErrStr($CERTKEY, $Result->BarobillState);
	}else{
		echo "자체문서관리번호 : $Result->MgtKey<br>
			바로빌문서관리번호 : $Result->InvoiceKey<br>
			바로빌상태코드 : $Result->BarobillState<br>
			개봉여부 : $Result->IsOpened<br>
			수신확인여부 : $Result->IsConfirmed<br>
			등록일시 : $Result->RegistDT<br>
			작성일자 : $Result->WriteDate<br>
			발행예정일시 : $Result->PreIssueDT<br>
			발행일시 : $Result->IssueDT<br>
			메모1 : $Result->Remark1<br>
			메모2 : $Result->Remark2<br>
			국세청전송상태 : $Result->NTSSendState<br>
			국세청승인번호 : $Result->NTSSendKey<br>
			국세청전송결과 : $Result->NTSSendResult<br>
			국세청전송일시 : $Result->NTSSendDT<br>
			전송결과수신일시 : $Result->NTSResultDT";
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
