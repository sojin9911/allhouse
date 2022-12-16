<? include '../include/top.php'; ?>
<p>GetLinkedDocs - 연결된 문서 목록</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$DocType = 1;			//원본 문서의 종류 : 1-세금계산서, 2-계산서
	$MgtKey = '';			//자체문서관리번호

	$Result = $BaroService_TI->GetLinkedDocs(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'DocType'		=> $DocType,
				'MgtKey'		=> $MgtKey
				))->GetLinkedDocsResult->LinkedDoc;

	if (is_null($Result)){
		echo '연결문서 목록을 불러오지 못했습니다.';
	}else if (sizeof($Result) == 1){
		echo "$Result->DocType,
			$Result->MgtKey,
			$Result->InvoiceKey";
	}else{
		foreach ($Result as $ld){
			echo "$ld->DocType,
				$ld->MgtKey,
				$ld->InvoiceKey<br>";
		}
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
