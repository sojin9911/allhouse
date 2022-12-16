<?php include '../../_include/top.php'; ?>
<?php include '../BaroService_CASHBILL.php'; ?>
<p>CancelCashBillPartial - 부분취소 (국세청전송 후)</p>
<div class="result">
	<?php
	$CERTKEY = '';					//인증키
	$CorpNum = '';					//연계사업자 사업자번호 ('-' 제외, 10자리)
	$ID = '';						//연계사업자 아이디
	$MgtKey = '';					//연동사부여 문서키
	$CancelType = '1';				//취소사유 : 1-거래취소, 2-오류발행, 3-기타
	$CancelAmount = '';				//취소할 공급가액
	$CancelTax = '';				//취소할 부가세
	$CancelServiceCharge = '';		//취소할 봉사료
	$SMSSendYN = false;				//취소 알림문자 전송여부 (발행비용과 별도로 과금됨)
	$MailTitle = '';				//취소 알림메일의 제목 (공백이나 Null의 경우 바로빌 기본값으로 전송됨.)

	$Result = $BaroService_CASHBILL->CancelCashBillPartial(array(
		'CERTKEY'				=> $CERTKEY,
		'CorpNum'				=> $CorpNum,
		'UserID'				=> $ID,
		'MgtKey'				=> $MgtKey,
		'CancelType'			=> $CancelType,
		'CancelAmount'			=> $CancelAmount,
		'CancelTax'				=> $CancelTax,
		'CancelServiceCharge'	=> $CancelServiceCharge,
		'SMSSendYN'				=> $SMSSendYN,
		'MailTitle'				=> $MailTitle,
	))->CancelCashBillPartialResult;

	echo $Result;
	?>
</div>
<?php include '../../_include/bottom.php'; ?>
