<? include '../include/top.php'; ?>
<p>ChangeCorpManager - 회원사 관리자 변경</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$newManagerID = '';		//연계사업자 새 관리자 아이디

	$Result = $BaroService_TI->ChangeCorpManager(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'newManagerID'	=> $newManagerID
				))->ChangeCorpManagerResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//1-성공
	}
	?>
</div>
<? include '../include/bottom.html'; ?>

