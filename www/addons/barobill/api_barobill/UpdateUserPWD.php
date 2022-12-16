<? include '../include/top.php'; ?>
<p>UpdateUserPWD - 사용자 비밀번호 수정</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$ID = '';				//연계사업자 아이디
	$newPWD = '';			//새 비밀번호 (6~20자만 가능)

	$Result = $BaroService_TI->UpdateUserPWD(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'ID'			=> $ID,
				'newPWD'		=> $newPWD
				))->UpdateUserPWDResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//1-성공
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
