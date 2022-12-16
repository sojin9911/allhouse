<? include '../include/top.php'; ?>
<p>GetBalanceCostAmount - 잔여포인트 확인</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)

	$Result = $BaroService_TI->GetBalanceCostAmount(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum
				))->GetBalanceCostAmountResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//잔여포인트
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
