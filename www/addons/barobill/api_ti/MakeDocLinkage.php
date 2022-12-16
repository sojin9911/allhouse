<? include '../include/top.php'; ?>
<p>MakeDocLinkage - 문서 연결</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$FromDocType = 1;		//원본 문서의 종류 : 1-세금계산서, 2-계산서
	$FromMgtKey = '';		//원본 자체문서관리번호
	$ToDocType = 1;			//대상 문서의 종류 : 1-세금계산서, 2-계산서, 3-전자문서
	$ToMgtKey = '';			//대상 자체문서관리번호

	$Result = $BaroService_TI->MakeDocLinkage(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'FromDocType'	=> $FromDocType,
				'FromMgtKey'	=> $FromMgtKey,
				'ToDocType'		=> $ToDocType,
				'ToMgtKey'		=> $ToMgtKey
				))->MakeDocLinkageResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//1-성공, 0-이미 연결되어 있습니다.
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
