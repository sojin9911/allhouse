<?php

	//$CERTKEY = '';			//인증키
	$CorpNum = rm_str($oscr['cp_number']);			//연계사업자 사업자번호 ('-' 제외, 10자리)
	$MgtNum = $oscr['s_tax_mgtnum'];			//자체문서관리번호

	$SendSMS = false;		//발행 알림문자 전송여부 (발행비용과 별도로 과금됨)
	$NTSSendOption = 1;		//현재 사용되지 않는 항목으로 1을 입력하면 된다.
	$ForceIssue = false;	//가산세 부과 여부에 상관없이 발행할지 여부
	$MailTitle = '';		//발행 알림메일의 제목 (공백이나 Null의 경우 바로빌 기본값으로 전송됨.)

	if($CorpNum && $MgtNum) {
		$Result = $BaroService_TI->IssueTaxInvoice(array(
					'CERTKEY'		=> $CERTKEY,
					'CorpNum'		=> $CorpNum,
					'MgtKey'		=> $MgtNum,
					'SendSMS'		=> $SendSMS,
					'NTSSendOption'	=> $NTSSendOption,
					'ForceIssue'	=> $ForceIssue,
					'MailTitle'		=> $MailTitle
					))->IssueTaxInvoiceResult;

	//ViewArr($Result);
		if ($Result < 0){
			//echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
			tax_log_insert($oscr['s_uid'] , $mode , $Result , getErrStr($CERTKEY, $Result));
		}else{
			//echo $Result;	//1-성공
							//2-성공(포인트 부족으로 SMS 전송실패)
							//3-성공(이메일 전송실패, ReSendEmail 함수로 재전송 하십시오.)
			tax_log_insert($oscr['s_uid'] , $mode , $Result , "성공");
		}
	}
?>