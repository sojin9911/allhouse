<? include '../include/top.php'; ?>
<p>UpdateTaxInvoiceEX - 수정 (승인 시 자동발행 옵션 추가)</p>
<div class="result">
	<?
	$CERTKEY = '';							//인증키

	$IssueDirection = 1;					//1-정발행, 2-역발행(위수탁 세금계산서는 정발행만 허용)
	$TaxInvoiceType = 1;					//1-세금계산서, 2-계산서, 4-위수탁세금계산서, 5-위수탁계산서

	//-------------------------------------------
	//과세형태
	//-------------------------------------------
	//TaxInvoiceType 이 1,4 일 때 : 1-과세, 2-영세
	//TaxInvoiceType 이 2,5 일 때 : 3-면세
	//-------------------------------------------
	$TaxType = 2;

	$TaxCalcType = 1;						//세율계산방법 : 1-절상, 2-절사, 3-반올림
	$PurposeType = 1;						//1-영수, 2-청구

	//-------------------------------------------
	//수정사유코드
	//-------------------------------------------
	//수정세금계산서를 작성하는 경우에 사용
	//1-기재사항의 착오 정정, 2-공급가액의 변동, 3-재화의 환입, 4-계약의 해제, 5-내국신용장 사후개설, 6-착오에 의한 이중발행
	//-------------------------------------------
	//$ModifyCode = 1;

	$Kwon = '';								//별지서식 11호 상의 [권] 항목
	$Ho = '';								//별지서식 11호 상의 [호] 항목
	$SerialNum = '일련번호2';				//별지서식 11호 상의 [일련번호] 항목

	//-------------------------------------------
	//공급가액 총액
	//-------------------------------------------
	$AmountTotal = '4000';

	//-------------------------------------------
	//세액합계
	//-------------------------------------------
	//$TaxType 이 2 또는 3 으로 셋팅된 경우 0으로 입력
	//-------------------------------------------
	$TaxTotal = '0';

	//-------------------------------------------
	//합계금액
	//-------------------------------------------
	//공급가액 총액 + 세액합계 와 일치해야 합니다.
	//-------------------------------------------
	$TotalAmount = '4000';

	$Cash = '';								//현금
	$ChkBill = '';							//수표
	$Note = '';								//어음
	$Credit = '';							//외상미수금

	$Remark1 = '비고2-1';
	$Remark2 = '비고2-2';
	$Remark3 = '비고2-3';

	$WriteDate = '';						//작성일자 (YYYYMMDD), 공백입력 시 Today로 작성됨.

	//-------------------------------------------
	//공급자 정보 - 정발행시 세금계산서 작성자
	//------------------------------------------
	$InvoicerParty = array(
		'MgtNum' 		=> '',						//정발행시 필수입력 - 자체문서관리번호
		'CorpNum' 		=> '',						//필수입력 - 연계사업자 사업자번호 ('-' 제외, 10자리)
		'TaxRegID' 		=> '3333',
		'CorpName' 		=> '공급자 회사명3',		//필수입력
		'CEOName' 		=> '대표자명3',				//필수입력
		'Addr' 			=> '주소3',
		'BizType' 		=> '업태3',
		'BizClass' 		=> '종목3',
		'ContactID' 	=> '',						//필수입력 - 담당자 바로빌 아이디
		'ContactName' 	=> '담당자3',				//필수입력
		'TEL' 			=> '02-1111-0003',
		'HP' 			=> '010-2222-0003',
		'Email' 		=> 'test3@test.com'			//필수입력
	);

	//-------------------------------------------
	//공급받는자 정보 - 역발행시 세금계산서 작성자
	//------------------------------------------
	$InvoiceeParty = array(
		'MgtNum' 		=> '',						//역발행시 필수입력 - 자체문서관리번호
		'CorpNum' 		=> '0000000000',			//필수입력
		'TaxRegID' 		=> '4444',
		'CorpName' 		=> '공급받는자 회사명4',	//필수입력
		'CEOName' 		=> '대표자명4',				//필수입력
		'Addr' 			=> '주소4',
		'BizType' 		=> '업태4',
		'BizClass' 		=> '종목4',
		'ContactID' 	=> '',						//역발행시 필수입력 - 담당자 바로빌 아이디
		'ContactName' 	=> '담당자4',				//필수입력
		'TEL' 			=> '02-1111-0004',
		'HP' 			=> '010-2222-0004',
		'Email' 		=> 'test4@test.com'			//역발행시 필수입력
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
										'Name'			=> '품목명2-1',
										'Information'	=> 'EA2',
										'ChargeableUnit'=> '40',
										'UnitPrice'		=> '50',
										'Amount'		=> '2000',
										'Tax'			=> '0',
										'Description'	=> '품목비고2-1'
									),
									array(
										'PurchaseExpiry'=> '',			//YYYYMMDD
										'Name'			=> '품목명2-2',
										'Information'	=> 'EA2',
										'ChargeableUnit'=> '40',
										'UnitPrice'		=> '50',
										'Amount'		=> '2000',
										'Tax'			=> '0',
										'Description'	=> '품목비고2-2'
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

	//-------------------------------------------

	$IssueTiming = 2;		//발행시점 : 1-공급자 직접발행, 2-공급받는자 승인시 자동발행
							//발행예정 기능을 사용한 경우에만 적용됨.

	//-------------------------------------------

	//정발행
	$Result = $BaroService_TI->UpdateTaxInvoiceEX(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $TaxInvoice['InvoicerParty']['CorpNum'],
				'Invoice'		=> $TaxInvoice,
				'IssueTiming'	=> $IssueTiming
				))->UpdateTaxInvoiceEXResult;
	/*
	//역발행
	$Result = $BaroService_TI->UpdateTaxInvoiceEX(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $TaxInvoice['InvoiceeParty']['CorpNum'],
				'Invoice'		=> $TaxInvoice,
				'IssueTiming'	=> 1
				))->UpdateTaxInvoiceEXResult;

	//위수탁
	$Result = $BaroService_TI->UpdateBrokerTaxInvoiceEX(array(
				'CERTKEY'		=> $CERTKEY,
				'CorpNum'		=> $TaxInvoice['BrokerParty']['CorpNum'],
				'Invoice'		=> $TaxInvoice,
				'IssueTiming'	=> $IssueTiming
				))->UpdateBrokerTaxInvoiceEXResult;
	*/

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//1-성공
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
