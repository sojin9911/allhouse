<? include '../include/top.php'; ?>
<p>GetCertificateExpireDate - 등록한 공인인증서 만료일 확인</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)

	$Result = $BaroService_TI->GetCertificateExpireDate(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum
				))->GetCertificateExpireDateResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//만료일
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
