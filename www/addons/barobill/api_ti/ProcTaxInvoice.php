<? include '../include/top.php'; ?>
<p>ProcTaxInvoice - 프로세스 처리</p>
<div class="result">
	<?
	//$CERTKEY = '';			//인증키
	$CorpNum = '6102539436';			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtKey = "20150909175754";			//자체문서관리번호
	$ProcType = 'ISSUE_CANCEL'; //프로세스 타입
							//CANCEL : 승인(발행)요청 취소
							//ACCEPT : 승인
							//REFUSE : 거부
							//ISSUE_CANCEL : 발행완료된 매출 세금계산서의 발행을 취소
	$Memo = '';				//프로세스 처리시 거래처에 전달할 메모.

	$Result = $BaroService_TI->ProcTaxInvoice(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $CorpNum,
				'MgtKey'		=> $MgtKey,
				'ProcType'		=> $ProcType,
				'Memo'			=> $Memo
				))->ProcTaxInvoiceResult;

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//1-성공
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
