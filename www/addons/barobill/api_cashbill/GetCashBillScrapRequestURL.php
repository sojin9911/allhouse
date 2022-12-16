<?php include '../../_include/top.php'; ?>
<?php include '../BaroService_CASHBILL.php'; ?>
<p>GetCashBillScrapRequestURL - 국세청 세금계산서 조회 서비스 신청 URL</p>
<div class="result">
	<?php
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$UserID = '';			//연계사업자 아이디
	$PWD = '';				//연계사업자 비밀번호
	
	$Result = $BaroService_CASHBILL->GetCashBillScrapRequestURL(array(
		'CERTKEY'	=> $CERTKEY,
		'CorpNum'	=> $CorpNum,
		'UserID'	=> $UserID,
		'PWD'		=> $PWD
	))->GetCashBillScrapRequestURLResult;

	echo $Result;
	?>
</div>
<?php include '../../_include/bottom.php'; ?>
