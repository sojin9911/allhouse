<?php include '../../_include/top.php'; ?>
<?php include '../BaroService_CASHBILL.php'; ?>
<p>GetCashBillPrintURL - 인쇄 팝업 URL</p>
<div class="result">
	<?php
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리) 			
	$ID = '';				//연계사업자 아이디
	$PWD = '';				//연계사업자 비밀번호
	$MgtKey = '';			//연동사부여 문서키

	$Result = $BaroService_CASHBILL->GetCashBillPrintURL(array(
		'CERTKEY'		=> $CERTKEY,
		'CorpNum'		=> $CorpNum,
		'UserID'		=> $ID,
		'PWD'			=> $PWD,
		'MgtKey'		=> $MgtKey
	))->GetCashBillPrintURLResult;

	echo $Result;
	?>
</div>
<?php include '../../_include/bottom.php'; ?>
