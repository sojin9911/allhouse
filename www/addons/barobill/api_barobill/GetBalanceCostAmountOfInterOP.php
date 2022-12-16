<? include '../include/top.php'; ?>
<p>GetBalanceCostAmountOfInterOP - 연동사포인트 확인</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키

	$Result = $BaroService_TI->GetBalanceCostAmountOfInterOP(array(
				'CERTKEY'		=> $CERTKEY
				))->GetBalanceCostAmountOfInterOPResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//잔여포인트
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
