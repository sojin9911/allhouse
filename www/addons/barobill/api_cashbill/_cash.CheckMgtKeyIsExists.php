<?php
//<!-- <p>CheckMgtKeyIsExists - 연동사부여 문서키 사용여부 확인</p> -->
	$Result = $BaroService_CASHBILL->CheckMgtKeyIsExists(array(
		'CERTKEY'	=> $CERTKEY,
		'CorpNum'	=> $CorpNum,
		'UserID'	=> $ID,
		'MgtKey'	=> $MgtKey,
	))->CheckMgtKeyIsExistsResult;

	//echo $Result;
?>
