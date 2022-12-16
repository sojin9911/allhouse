<? include '../include/top.php'; ?>
<p>RegistTaxInvoice - 일반세금계산서 등록</p>
<div class="result">
	<?
	//$CERTKEY = '';							//인증키 - BaroService_TI.php 적용됨

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

	$Kwon = '';								//별지서식 11호 상의 [권] 항목
	$Ho = '';								//별지서식 11호 상의 [호] 항목
	$SerialNum = '일련번호1';				//별지서식 11호 상의 [일련번호] 항목

	//-------------------------------------------
	//공급가액 총액
	//-------------------------------------------
	$AmountTotal = '2000';

	//-------------------------------------------
	//세액합계
	//-------------------------------------------
	//$TaxType 이 2 또는 3 으로 셋팅된 경우 0으로 입력
	//-------------------------------------------
	$TaxTotal = '200';

	//-------------------------------------------
	//합계금액
	//-------------------------------------------
	//공급가액 총액 + 세액합계 와 일치해야 합니다.
	//-------------------------------------------
	$TotalAmount = '2200';

	$Cash = '';								//현금
	$ChkBill = '';							//수표
	$Note = '';								//어음
	$Credit = '';							//외상미수금

	$Remark1 = '비고1-1';
	$Remark2 = '비고1-2';
	$Remark3 = '비고1-3';

	$WriteDate = '';						//작성일자 (YYYYMMDD), 공백입력 시 Today로 작성됨.

	//-------------------------------------------
	//공급자 정보 - 정발행시 세금계산서 작성자
	//------------------------------------------
	$InvoicerParty = array(
		'MgtNum' 		=> "20150909175754",						//정발행시 필수입력 - 자체문서관리번호 - 24자리이내 고유키
		'CorpNum' 		=> '6102539436',						//필수입력 - 연계사업자 사업자번호 ('-' 제외, 10자리)
		'TaxRegID' 		=> '1111',
		'CorpName' 		=> '상상너머',		//필수입력
		'CEOName' 		=> '심현숙',				//필수입력
		'Addr' 			=> '광주광역시 동구 수기동 106-4번지 2층',
		'BizType' 		=> '서비스',
		'BizClass' 		=> '솔루션',
		'ContactID' 	=> 'ssnumer',						//필수입력 - 담당자 바로빌 아이디
		'ContactName' 	=> '정준철',				//필수입력
		'TEL' 			=> '02-1544-6937',
		'HP' 			=> '010-2640-6194',
		'Email' 		=> 'tech@onedaynet.co.kr'			//필수입력
	);

	//-------------------------------------------
	//공급받는자 정보 - 역발행시 세금계산서 작성자
	//------------------------------------------
	$InvoiceeParty = array(
		'MgtNum' 		=> '',						//역발행시 필수입력 - 자체문서관리번호
		'CorpNum' 		=> '0000000000',			//필수입력
		'TaxRegID' 		=> '2222',
		'CorpName' 		=> '공급받는자 회사명2',	//필수입력
		'CEOName' 		=> '대표자명2',				//필수입력
		'Addr' 			=> '주소2',
		'BizType' 		=> '업태2',
		'BizClass' 		=> '종목2',
		'ContactID' 	=> '',						//역발행시 필수입력 - 담당자 바로빌 아이디
		'ContactName' 	=> '담당자2',				//필수입력
		'TEL' 			=> '02-1111-0002',
		'HP' 			=> '010-2222-0002',
		'Email' 		=> 'test2@test.com'			//역발행시 필수입력
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
										'Name'			=> '품목명1-1',
										'Information'	=> 'EA1',
										'ChargeableUnit'=> '25',
										'UnitPrice'		=> '40',
										'Amount'		=> '1000',
										'Tax'			=> '100',
										'Description'	=> '품목비고1-1'
									),
									array(
										'PurchaseExpiry'=> '',			//YYYYMMDD
										'Name'			=> '품목명1-2',
										'Information'	=> 'EA1',
										'ChargeableUnit'=> '25',
										'UnitPrice'		=> '40',
										'Amount'		=> '1000',
										'Tax'			=> '100',
										'Description'	=> '품목비고1-2'
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

	if ($Result < 0){
		echo "오류코드 : $Result<br><br>".getErrStr($CERTKEY, $Result);
	}else{
		echo $Result;	//1-성공
	}
	?>
</div>
<? include '../include/bottom.html'; ?>
