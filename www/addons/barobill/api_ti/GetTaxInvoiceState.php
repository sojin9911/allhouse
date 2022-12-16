<? include '../include/top.php'; ?>
<p>GetTaxInvoiceState - 문서 상태</p>
<div class="result">
	<?
	//$CERTKEY = '';			//인증키
	$CorpNum = '6102539436';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtKey = "20150909175754";			//자체문서관리번호

	$Result = $BaroService_TI->GetTaxInvoiceState(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtKey
				))->GetTaxInvoiceStateResult;

	if (is_null($Result)){
		echo '문서 상태를 불러오지 못했습니다.';
	}else if ($Result->BarobillState < 0){
		echo "오류코드 : $Result->BarobillState<br><br>".getErrStr($CERTKEY, $Result->BarobillState);
	}else{
		echo "자체문서관리번호 : $Result->MgtKey<br>
			바로빌문서관리번호 : $Result->InvoiceKey<br>
			바로빌상태코드 : $Result->BarobillState : ". $arr_inner_state_table[$Result->BarobillState] ."<br>
			개봉여부 : $Result->IsOpened<br>
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
