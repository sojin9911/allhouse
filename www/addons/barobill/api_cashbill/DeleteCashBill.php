<?php include '../../_include/top.php'; ?>
<?php include '../BaroService_CASHBILL.php'; ?>
<p>DeleteCashBill - 삭제</p>
<div class="result">
	<?php
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$ID = '';				//연계사업자 아이디
	$MgtKey = '';			//연동사부여 문서키

	$Result = $BaroService_CASHBILL->DeleteCashBill(array(
		'CERTKEY'	=> $CERTKEY,
		'CorpNum'	=> $CorpNum,
		'UserID'	=> $ID,
		'MgtKey'	=> $MgtKey
	))->DeleteCashBillResult;

	echo $Result;
	?>
</div>
<?php include '../../_include/bottom.php'; ?>
