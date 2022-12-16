<? include '../include/top.php'; ?>
<p>UpdateUserInfo - 사용자 정보 수정</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$ID = '';				//연계사업자 아이디
	$MemberName = '';		//담당자 성명
	$JuminNum = '';			//주민등록번호 ('-' 제외, 13자리)
	$TEL = '';				//전화번호
	$HP = '';				//휴대폰
	$Email = '';			//이메일
	$Grade = '';			//직급

	$Result = $BaroService_TI->UpdateUserInfo(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'ID'			=> $ID,
				'MemberName'	=> $MemberName,
				'JuminNum'		=> $JuminNum,
				'TEL'			=> $TEL,
				'HP'			=> $HP,
				'Email'			=> $Email,
				'Grade'			=> $Grade
				))->UpdateUserInfoResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//1-성공
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
