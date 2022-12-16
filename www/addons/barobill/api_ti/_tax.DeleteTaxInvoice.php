<?php

	//$CERTKEY = '';			//인증키
	$CorpNum = rm_str($siteInfo['s_company_num']);			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtNum = $taxInfo['MgtKey'];			//자체문서관리번호

	if($CorpNum && $MgtNum) {
		$Result = $BaroService_TI->DeleteTaxInvoice(array(
					'CERTKEY'		=> $CERTKEY,
					'CorpNum'		=> $CorpNum,
					'MgtKey'		=> $MgtNum
					))->DeleteTaxInvoiceResult;

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