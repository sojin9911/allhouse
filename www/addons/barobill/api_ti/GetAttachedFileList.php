<? include '../include/top.php'; ?>
<p>GetAttachedFileList - 첨부파일 목록</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtKey = '';			//자체문서관리번호

	$Result = $BaroService_TI->GetAttachedFileList(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtKey
				))->GetAttachedFileListResult->AttachedFile;

	if (is_null($Result)){
		echo '첨부파일 목록을 불러오지 못했습니다.';
	}else if (sizeof($Result) == 1){
		echo "$Result->FileIndex,
			$Result->FileName,
			$Result->DisplayFileName";
	}else{
		foreach ($Result as $af){
			echo "$af->FileIndex,
				$af->FileName,
				$af->DisplayFileName<br>";
		}
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
