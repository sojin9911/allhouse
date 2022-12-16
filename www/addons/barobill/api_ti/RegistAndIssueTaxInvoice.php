<?php include '../../_include/top.php'; ?>
<?php include '../BaroService_TI.php'; ?>
<p>RegistAndIssueTaxInvoice - 일반(수정)세금계산서 "등록" 과 "발행" 을 한번에 처리</p>
<div class="result">
	<?php
	$CERTKEY = '';							//인증키

	$IssueDirection = 1;					//1-정발행, 2-역발행(위수탁 세금계산서는 정발행만 허용)
	$TaxInvoiceType = 1;					//1-세금계산서, 2-계산서, 4-위수탁세금계산서, 5-위수탁계산서

	//-------------------------------------------
	//과세형태
	//-------------------------------------------
	//TaxInvoiceType 이 1,4 일 때 : 1-과세, 2-영세
	//TaxInvoiceType 이 2,5 일 때 : 3-면세
	//-------------------------------------------
	$TaxType = 1;
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
	$SerialNum = '';						//별지서식 11호 상의 [일련번호] 항목

	//-------------------------------------------
	//공급가액 총액
	//-------------------------------------------
	$AmountTotal = '';

	//-------------------------------------------
	//세액합계
	//-------------------------------------------
	//$TaxType 이 2 또는 3 으로 셋팅된 경우 0으로 입력
	//-------------------------------------------
	$TaxTotal = '';

	//-------------------------------------------
	//합계금액
	//-------------------------------------------
	//공급가액 총액 + 세액합계 와 일치해야 합니다.
	//-------------------------------------------
	$TotalAmount = '';

	$Cash = '';								//현금
	$ChkBill = '';							//수표
	$Note = '';								//어음
	$Credit = '';							//외상미수금

	$Remark1 = '';
	$Remark2 = '';
	$Remark3 = '';

	$WriteDate = '';						//작성일자 (YYYYMMDD), 공백입력 시 Today로 작성됨.

	//-------------------------------------------
	//공급자 정보 - 정발행시 세금계산서 작성자
	//------------------------------------------
	$InvoicerParty = array(
		'MgtNum' 		=> '',				//필수입력 - 연동사부여 문서키
		'CorpNum' 		=> '',				//필수입력 - 연계사업자 사업자번호 ('-' 제외, 10자리)
		'TaxRegID' 		=> '',
		'CorpName' 		=> '',				//필수입력
		'CEOName' 		=> '',				//필수입력
		'Addr' 			=> '',
		'BizType' 		=> '',
		'BizClass' 		=> '',
		'ContactID' 	=> '',				//필수입력 - 담당자 바로빌 아이디
		'ContactName' 	=> '',				//필수입력
		'TEL' 			=> '',
		'HP' 			=> '',
		'Email' 		=> ''				//필수입력
	);

	//-------------------------------------------
	//공급받는자 정보 - 역발행시 세금계산서 작성자
	//------------------------------------------
	$InvoiceeParty = array(
		'MgtNum' 		=> '',
		'CorpNum' 		=> '',				//필수입력
		'TaxRegID' 		=> '',
		'CorpName' 		=> '',				//필수입력
		'CEOName' 		=> '',				//필수입력
		'Addr' 			=> '',
		'BizType' 		=> '',
		'BizClass' 		=> '',
		'ContactID' 	=> '',
		'ContactName' 	=> '',				//필수입력
		'TEL' 			=> '',
		'HP' 			=> '',
		'Email' 		=> ''
	);

	//-------------------------------------------
	//수탁자 정보 - 입력하지 않음
	//------------------------------------------
	$BrokerParty = array(
		'MgtNum' 		=> '',
		'CorpNum' 		=> '',
		'TaxRegID' 		=> '',
		'CorpName' 		=> '',
		'CEOName' 		=> '',
		'Addr' 			=> '',
		'BizType' 		=> '',
		'BizClass' 		=> '',
		'ContactID' 	=> '',
		'ContactName' 	=> '',
		'TEL' 			=> '',
		'HP' 			=> '',
		'Email' 		=> ''
	);

	//-------------------------------------------
	//품목
	//-------------------------------------------
	$TaxInvoiceTradeLineItems = array(
		'TaxInvoiceTradeLineItem'	=> array(
			array(
				'PurchaseExpiry'=> '',			//YYYYMMDD
				'Name'			=> '',
				'Information'	=> '',
				'ChargeableUnit'=> '',
				'UnitPrice'		=> '',
				'Amount'		=> '',
				'Tax'			=> '',
				'Description'	=> ''
			),
			array(
				'PurchaseExpiry'=> '',			//YYYYMMDD
				'Name'			=> '',
				'Information'	=> '',
				'ChargeableUnit'=> '',
				'UnitPrice'		=> '',
				'Amount'		=> '',
				'Tax'			=> '',
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

	//-------------------------------------------

	$SendSMS = false;							//문자 발송여부 (공급받는자 정보의 HP 항목이 입력된 경우에만 발송됨)

	$ForceIssue = false;						//가산세가 예상되는 세금계산서 발행 여부
	
	$MailTitle = '';							//전송되는 이메일의 제목 설정 (공백 시 바로빌 기본 제목으로 전송됨)

	//-------------------------------------------

	//정발행
	$Result = $BaroService_TI->RegistAndIssueTaxInvoice(array(
		'CERTKEY'	=> $CERTKEY,
		'CorpNum'	=> $TaxInvoice['InvoicerParty']['CorpNum'],
		'Invoice'	=> $TaxInvoice,
		'SendSMS'	=> $SendSMS,
		'ForceIssue'=> $ForceIssue,
		'MailTitle'	=> $MailTitle,
	))->RegistAndIssueTaxInvoiceResult;

	echo $Result;
	?>
</div>
<?php include '../../_include/bottom.php'; ?>
