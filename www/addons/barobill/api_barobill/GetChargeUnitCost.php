<? include '../include/top.php'; ?>
<p>GetChargeUnitCost - 요금 단가 확인</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$ChargeCode = 1;		//1-세금계산서, 2-계산서, 3-거래명세서, 4-입금표, 5-청구서, 6-견적서, 7-영수증, 8-발주서, 9-현금영수증, 11-SMS전송, 12-FAX전송

	$Result = $BaroService_TI->GetChargeUnitCost(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'ChargeCode'	=> $ChargeCode
				))->GetChargeUnitCostResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//단가
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
