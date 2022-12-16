<? include '../include/top.php'; ?>
<p>CheckMgtKeyIsExists - 관리번호 사용여부 확인</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtKey = '';			//자체문서관리번호

	$Result = $BaroService_TI->CheckMgtNumIsExists(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtKey,
				))->CheckMgtNumIsExistsResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//1-사용중인 관리번호, 2-사용가능한 관리번호
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
