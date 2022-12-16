<? include '../include/top.php'; ?>
<p>GetTaxInvoicesPrintURL - 대량인쇄 팝업 URL (자체문서관리번호)</p>
<div class="result">
	<?
	$CERTKEY = '';			//인증키
	$CorpNum = '';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$ID = '';				//연계사업자 아이디
	$PWD = '';				//연계사업자 비밀번호
	$MgtKeyList = array(	//자체문서관리번호 배열
					string => array(
						'',
						'',
						'',
						'',
						''
					)
				);

	$Result = $BaroService_TI->GetTaxInvoicesPrintURL(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKeyList'	=> $MgtKeyList,
				'ID'			=> $ID,
				'PWD'			=> $PWD
				))->GetTaxInvoicesPrintURLResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo "<a href=\"$Result\" target=\"_blank\">$Result</a>";
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
