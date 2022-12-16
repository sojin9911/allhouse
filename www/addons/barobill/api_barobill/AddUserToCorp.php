<? include '../include/top.php'; ?>
<p>AddUserToCorp - 사용자 추가</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MemberName = '';		//담당자 성명
	$JuminNum = '';			//주민등록번호 ('-' 제외, 13자리)
	$ID = '';				//연계사업자 아이디
	$PWD = '';				//연계사업자 비밀번호 (6~20자만 가능)
	$Grade = '';			//직급
	$TEL = '';				//전화번호
	$HP = '';				//휴대폰
	$Email = '';			//이메일

	$Result = $BaroService_TI->AddUserToCorp(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MemberName'	=> $MemberName,
				'JuminNum'		=> $JuminNum,
				'ID'			=> $ID,
				'PWD'			=> $PWD,
				'Grade'			=> $Grade,
				'TEL'			=> $TEL,
				'HP'			=> $HP,
				'Email'			=> $Email
				))->AddUserToCorpResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//1-성공
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
