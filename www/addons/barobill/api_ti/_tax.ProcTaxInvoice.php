<?php

	//$CERTKEY = '';			//인증키
	$CorpNum = rm_str($siteInfo['s_company_num']);			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtNum = $taxInfo['MgtKey'];			//자체문서관리번호

	$ProcType = 'ISSUE_CANCEL'; //프로세스 타입
							//CANCEL : 승인(발행)요청 취소
							//ACCEPT : 승인
							//REFUSE : 거부
							//ISSUE_CANCEL : 발행완료된 매출 세금계산서의 발행을 취소
	$Memo = '';				//프로세스 처리시 거래처에 전달할 메모.

	if($CorpNum && $MgtNum) {
		$Result = $BaroService_TI->ProcTaxInvoice(array(
					'CERTKEY'		=> $CERTKEY,
					'CorpNum'		=> $CorpNum,
					'MgtKey'		=> $MgtNum,
					'ProcType'		=> $ProcType,
					'Memo'			=> $Memo
					))->ProcTaxInvoiceResult;

	//ViewArr($Result);
		if ($Result < 0){
			//echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
			tax_log_insert($taxInfo['bt_uid'] , $mode , $Result , getErrStr($CERTKEY, $Result));
		}else{
			//echo $Result;	//1-성공
							//2-성공(포인트 부족으로 SMS 전송실패)
							//3-성공(이메일 전송실패, ReSendEmail 함수로 재전송 하십시오.)
			tax_log_insert($taxInfo['bt_uid'] , $mode , $Result , "성공");
		}
	}
?>