<? include '../include/top.php'; ?>
<p>GetCorpMemberContacts - 회원사 담당자 목록</p>
<div class="result">
	<?

	include_once( dirname(__FILE__)."/../include/BaroService_TI.php");
	include_once( dirname(__FILE__)."/../include/var.php");

	$CERTKEY = '8FE6E4DB-E408-4899-8A5E-57DB781245FA';			//인증키
	$CorpNum = '6102539436';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$CheckCorpNum = '6102539436';		//확인할 사업자번호 ('-' 제외, 10자리)

	$Result = $BaroService_TI->GetCorpMemberContacts(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'CheckCorpNum'	=> $CheckCorpNum
				))->GetCorpMemberContactsResult->Contact;

	if (is_null($Result)){
		echo '사용자 목록을 불러오지 못했습니다.';
	}else if (sizeof($Result) == 1){
		echo "$Result->ID,
			$Result->ContactName,
			$Result->Grade,
			$Result->TEL,
			$Result->HP,
			$Result->Email";
	}else{
		foreach ($Result as $ct){
			echo "$ct->ID,
				$ct->ContactName,
				$ct->Grade,
				$ct->TEL,
				$ct->HP,
				$ct->Email<br>";
		}
	}

	echo getErrStr($CERTKEY, $Result);
	?>
</div>
<? include '../include/bottom.html'; ?>
