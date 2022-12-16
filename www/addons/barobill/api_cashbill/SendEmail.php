<?php include '../../_include/top.php'; ?>
<?php include '../BaroService_CASHBILL.php'; ?>
<p>SendEmail - 이메일 전송</p>
<div class="result">
	<?php
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$ID = '';				//연계사업자 아이디
	$MgtKey = '';			//연동사부여 문서키
	$ToEmailAddress = '';	//수신자 메일 주소

	$Result = $BaroService_CASHBILL->SendEmail(array(
		'CERTKEY'			=> $CERTKEY,
		'CorpNum'			=> $CorpNum,
		'UserID'			=> $ID,
		'MgtKey'			=> $MgtKey,
		'ToEmailAddress'	=> $ToEmailAddress
	))->SendEmailResult;

	echo $Result;
	?>
</div>
<?php include '../../_include/bottom.php'; ?>
