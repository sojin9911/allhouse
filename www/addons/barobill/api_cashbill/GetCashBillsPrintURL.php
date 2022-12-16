<?php include '../../_include/top.php'; ?>
<?php include '../BaroService_CASHBILL.php'; ?>
<p>GetCashBillsPrintURL - 대량인쇄 팝업 URL</p>
<div class="result">
	<?php
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$ID = '';				//연계사업자 아이디
	$PWD = '';				//연계사업자 비밀번호
	$MgtKeyList = array(	//연동사부여 문서키 배열
		'',
		''
	);

	$Result = $BaroService_CASHBILL->GetCashBillsPrintURL(array(
		'CERTKEY'		=> $CERTKEY,
		'CorpNum'		=> $CorpNum,
		'UserID'		=> $ID,
		'PWD'			=> $PWD,
		'MgtKeyList'	=> $MgtKeyList
	))->GetCashBillsPrintURLResult;

	echo $Result;
	?>
</div>
<?php include '../../_include/bottom.php'; ?>
