<? include '../include/top.php'; ?>
<p>GetNTSSendOption - 국세청 전송설정 확인</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)

	$Result = $BaroService_TI->GetNTSSendOption(array(
				'CERTKEY'			=> $CERTKEY,
				'CorpNum'			=> $CorpNum
				))->GetNTSSendOptionResult;

	//----------------------------------------------------
	if ($Result->TaxationOption < 0){
		echo '오류코드 : '.$Result->TaxationOption.'<br><br>'.getErrStr($CERTKEY, $Result->TaxationOption);
	}else{
		echo '과세,영세 국세청전송옵션 : ';
		switch($Result->TaxationOption){
			case 1:
				echo '발행 익일 자동전송';
				break;
			case 2:
				echo '발행 즉시 전송';
				break;
		}
		echo '<br>';

		echo '과세,영세 가산세허용여부 : ';
		switch($Result->TaxationAddTaxAllowYN){
			case 1:
				echo '허용';
				break;
			case 0:
				echo '차단';
				break;
		}
		echo '<br>';

		echo '면세 국세청전송옵션 : ';
		switch($Result->TaxExemptionOption){
			case 1:
				echo '발행 익일 자동전송';
				break;
			case 2:
				echo '발행 즉시 전송';
				break;
			case 3:
				echo '수동 전송';
				break;
		}
		echo '<br>';

		echo '면세 가산세허용여부 : ';
		switch($Result->TaxExemptionAddTaxAllowYN){
			case 1:
				echo '허용';
				break;
			case 0:
				echo '차단';
				break;
		}
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
