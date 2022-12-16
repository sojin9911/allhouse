<? include '../include/top.php'; ?>
<p>GetEmailPublicKeys - ASP업체 Email 목록확인</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키

	$Result = $BaroService_TI->GetEmailPublicKeys(array(
				'CERTKEY'		=> $CERTKEY
				))->GetEmailPublicKeysResult->EMAILPUBLICKEY;

	if (is_null($Result)){
		echo 'ASP업체 Email 목록을 불러오지 못했습니다.';
	}else if (sizeof($Result) == 1){
		echo "$Result->Email,
			$Result->NTSCertNum,
			$Result->PK";
	}else{
		foreach ($Result as $ep){
			echo "$ep->Email,
			$ep->NTSCertNum,
			$ep->PK<br>";
		}
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
