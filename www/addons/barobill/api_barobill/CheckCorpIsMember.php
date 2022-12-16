<? include '../include/top.php'; ?>
<p>CheckCorpIsMember - 회원사 여부 확인</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$CheckCorpNum = '';		//확인할 사업자번호 ('-' 제외, 10자리)

	$Result = $BaroService_TI->CheckCorpIsMember(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'CheckCorpNum'	=> $CheckCorpNum
				))->CheckCorpIsMemberResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//0:바로빌 회원사가 아님, 1:바로빌 회원, -1:휴/폐업, -2:탈퇴함
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
