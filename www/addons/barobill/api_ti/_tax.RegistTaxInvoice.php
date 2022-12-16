<?php

	//$CERTKEY = '';							//인증키 - BaroService_TI.php 적용됨

	$IssueDirection = 1;					//1-정발행, 2-역발행(위수탁 세금계산서는 정발행만 허용)
	$TaxInvoiceType = $taxInfo['TaxInvoiceType'];					//1-세금계산서, 2-계산서, 4-위수탁세금계산서, 5-위수탁계산서

	//-------------------------------------------
	//과세형태
	//-------------------------------------------
	//TaxInvoiceType 이 1,4 일 때 : 1-과세, 2-영세
	//TaxInvoiceType 이 2,5 일 때 : 3-면세
	//-------------------------------------------
	if($taxInfo['TaxInvoiceType']==1) $TaxType = 1; // 과세
	else $TaxType = 3; // 면세

	$TaxCalcType = 1;						//세율계산방법 : 1-절상, 2-절사, 3-반올림
	$PurposeType = 2;						//1-영수, 2-청구

	//-------------------------------------------
	//수정사유코드
	//-------------------------------------------
	//공백-일반세금계산서, 1-기재사항의 착오 정정, 2-공급가액의 변동, 3-재화의 환입, 4-계약의 해제, 5-내국신용장 사후개설, 6-착오에 의한 이중발행
	//-------------------------------------------
	$ModifyCode = '';

	$Kwon = '';								//별지서식 11호 상의 [권] 항목
	$Ho = '';								//별지서식 11호 상의 [호] 항목
	$SerialNum = $taxInfo['bt_uid'];						//별지서식 11호 상의 [일련번호] 항목

	//-------------------------------------------
	//공급가액 총액
	//-------------------------------------------
	$AmountTotal = $taxInfo['Amount']; // 역발행 패치 // 2016-10-13 총액의 10%가 아니라 공급가의 10%적용으로 변경 SSJ

	//-------------------------------------------
	//세액합계
	//-------------------------------------------
	//$TaxType 이 2 또는 3 으로 셋팅된 경우 0으로 입력
	//-------------------------------------------
	$TaxTotal =  $taxInfo['Tax']; // 역발행 패치 // 2016-10-13 총액의 10%가 아니라 공급가의 10%적용으로 변경 SSJ

	//-------------------------------------------
	//합계금액
	//-------------------------------------------
	//공급가액 총액 + 세액합계 와 일치해야 합니다.
	//-------------------------------------------
	$TotalAmount = $taxInfo['bt_total_price']*1; // 역발행 패치

	$Cash = '';								//현금
	$ChkBill = '';							//수표
	$Note = '';								//어음
	$Credit = '';							//외상미수금

	$Remark1 = '';
	$Remark2 = '';
	$Remark3 = '';

	$WriteDate = '';						//작성일자 (YYYYMMDD), 공백입력 시 Today로 작성됨.


	// 문서키 발급
	if($taxInfo['MgtKey']){
		$MgtNum = $taxInfo['MgtKey'];
	}else{
		$MgtNum = app_mgtnum_create();
		_MQ_noreturn(" update smart_baro_tax set MgtKey ='". $MgtNum ."' where bt_uid = '". $taxInfo['bt_uid'] ."' ");// MgtNum 정보 입력
	}



	//-------------------------------------------
	//공급자 정보 - 정발행시 세금계산서 작성자
	//------------------------------------------
	$InvoicerParty = getInvoicerParty($MgtNum);


	//-------------------------------------------
	//공급받는자 정보 - 역발행시 세금계산서 작성자
	//------------------------------------------
	$InvoiceeParty = array(
		'MgtNum' 		=> '',						//역발행시 필수입력 - 자체문서관리번호
		'CorpNum' 		=> rm_str($taxInfo['CorpNum']),			//필수입력
		'TaxRegID' 		=> "2222",
		'CorpName' 		=> $taxInfo['CorpName'],	//필수입력
		'CEOName' 		=> $taxInfo['CEOName'],				//필수입력
		'Addr' 			=> $taxInfo['Addr'],
		'BizType' 		=> $taxInfo['BizType'],
		'BizClass' 		=> $taxInfo['BizClass'],
		'ContactID' 	=> '',						//역발행시 필수입력 - 담당자 바로빌 아이디
		'ContactName' 	=> $taxInfo['ContactName'],				//필수입력
		'TEL' 			=> tel_format($taxInfo['TEL']),
		'HP' 			=> tel_format($taxInfo['HP']),
		'Email' 		=> $taxInfo['Email']			//역발행시 필수입력
	);


	//-------------------------------------------
	//수탁자 정보 - 위수탁 발행시 세금계산서 작성자
	//------------------------------------------
	$BrokerParty = array(
		'MgtNum' 		=> '',						//위수탁발행시 필수입력 - 자체문서관리번호
		'CorpNum' 		=> '',						//위수탁발행시 필수입력 - 연계사업자 사업자번호 ('-' 제외, 10자리)
		'TaxRegID' 		=> '',
		'CorpName' 		=> '',						//위수탁발행시 필수입력
		'CEOName' 		=> '',						//위수탁발행시 필수입력
		'Addr' 			=> '',
		'BizType' 		=> '',
		'BizClass' 		=> '',
		'ContactID' 	=> '',						//위수탁발행시 필수입력 - 담당자 바로빌 아이디
		'ContactName' 	=> '',						//위수탁발행시 필수입력
		'TEL' 			=> '',
		'HP' 			=> '',
		'Email' 		=> ''						//위수탁발행시 필수입력
	);

	//-------------------------------------------
	//품목
	//-------------------------------------------
	$TaxInvoiceTradeLineItems = array(
		'TaxInvoiceTradeLineItem'	=> array(
			array(
				'PurchaseExpiry'=> '',			//YYYYMMDD
				'Name'			=> $taxInfo['Name'] ,
				'Information'	=> 'EA',
				'ChargeableUnit'=> '1',
				'UnitPrice'		=> $taxInfo['UnitPrice'] ,
				'Amount'		=> $taxInfo['Amount'] ,
				'Tax'			=> $taxInfo['Tax'],
				'Description'	=> ''
			)
		)
	);

	//-------------------------------------------
	//전자세금계산서
	//-------------------------------------------
	$TaxInvoice = array(
		'InvoiceKey'				=> '',
		'InvoiceeASPEmail'			=> '',
		'IssueDirection'			=> $IssueDirection,
		'TaxInvoiceType'			=> $TaxInvoiceType,
		'TaxType'					=> $TaxType,
		'TaxCalcType'				=> $TaxCalcType,
		'PurposeType'				=> $PurposeType,
		'ModifyCode'				=> $ModifyCode,
		'Kwon'						=> $Kwon,
		'Ho'						=> $Ho,
		'SerialNum'					=> $SerialNum,
		'Cash'						=> $Cash,
		'ChkBill'					=> $ChkBill,
		'Note'						=> $Note,
		'Credit'					=> $Credit,
		'WriteDate'					=> $WriteDate,
		'AmountTotal'				=> $AmountTotal,
		'TaxTotal'					=> $TaxTotal,
		'TotalAmount'				=> $TotalAmount,
		'Remark1'					=> $Remark1,
		'Remark2'					=> $Remark2,
		'Remark3'					=> $Remark3,
		'InvoicerParty'				=> $InvoicerParty,
		'InvoiceeParty'				=> $InvoiceeParty,
		'BrokerParty'				=> $BrokerParty,
		'TaxInvoiceTradeLineItems'	=> $TaxInvoiceTradeLineItems
	);

	//정발행
	//echo "<xmp>".print_R($TaxInvoice , true)."</xmp>";
	$Result = $BaroService_TI->RegistTaxInvoice(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $TaxInvoice['InvoicerParty']['CorpNum'],
				'Invoice'		=> $TaxInvoice
				))->RegistTaxInvoiceResult;
	/*
	//역발행
	$Result = $BaroService_TI->RegistTaxInvoiceReverse(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $TaxInvoice['InvoiceeParty']['CorpNum'],
				'Invoice'		=> $TaxInvoice
				))->Result;

	//위수탁
	$Result = $BaroService_TI->RegistBrokerTaxInvoice(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $TaxInvoice['BrokerParty']['CorpNum'],
				'Invoice'		=> $TaxInvoice
				))->Result;
	*/



//ViewArr($Result);
	if ($Result < 0) {
		//echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
		tax_log_insert($taxInfo['bt_uid'] , $mode , $Result , getErrStr($CERTKEY, $Result));
	}
	else{
		//echo $Result; //1-성공
		tax_log_insert($taxInfo['bt_uid'] , $mode , $Result , "성공");
	}

?>