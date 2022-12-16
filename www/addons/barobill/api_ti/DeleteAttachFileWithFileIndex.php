<? include '../include/top.php'; ?>
<p>DeleteAttachFileWithFileIndex - 첨부파일 삭제</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtKey = '';			//자체문서관리번호
	$FileIndex = 1;			//삭제할 첨부파일의 인덱스 (GetAttachedFileList 로 확인된 인덱스)

	$Result = $BaroService_TI->DeleteAttachFileWithFileIndex(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtKey,
				'FileIndex'		=> $FileIndex
				))->DeleteAttachFileWithFileIndexResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//1-성공, 0-해당 첨부파일이 없음
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
