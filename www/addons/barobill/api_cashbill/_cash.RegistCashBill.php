<?php
//<!-- <p>RegistCashBill - 등록</p> -->
	$CashBill = array(
		'MgtKey'							=> $MgtKey,		//연동사부여 문서키	
		'TradeDate'						=> '',		//거래일자 (YYYYMMDD), 공백입력 시 Today로 작성됨.
		'FranchiseCorpNum'			=> $CorpNum,		//가맹점 사업자번호
		'FranchiseMemberID'		=> $UserID,		//가맹점 바로빌 회원 아이디
		'FranchiseCorpName'		=> $FranchiseCorpName,		//가맹점 회사명
		'FranchiseCEOName'			=> $FranchiseCEOName,		//가맹점 대표자명
		'FranchiseAddr'					=> $FranchiseAddr,		//가맹점 주소
		'FranchiseTel'					=> $FranchiseTel,		//가맹점 전화번호
		'IdentityNum'					=> $IdentityNum,		//소비자 신분확인번호 ("-" 를 제외한 주민등록번호/사업자번호/휴대폰번호/카드번호 중 택1)
		'HP'									=> $HP,		//소비자 휴대폰번호 (문자 전송시 활용)
		'Fax'									=> $Fax,		//소비자 팩스번호 (팩스 전송시 활용)
		'Email'								=> $Email,		//소비자 이메일 (이메일 전송시 활용)
		'TradeType'						=> 'N',		//거래구분 : N-승인거래, D-취소거래
		'TradeUsage'						=> $TradeUsage,		//거래용도 : 1-소득공제용, 2-지출증빙용 (신분확인번호가 사업자번호인 경우 지출증빙용으로)
		'TradeMethod'					=> $TradeMethod,		//거래방법 : 1-카드, 2-수기입력 (신분확인번호가 카드번호가 아닌 경우 수기입력으로)
		'ItemName'						=> $ItemName,		//품목명
		'Amount'							=> $Amount,		//공급가액
		'Tax'									=> $Tax,		//부가세
		'ServiceCharge'					=> $ServiceCharge,		//봉사료
		'CancelType'						=> '',		//취소사유 : 1-거래취소, 2-오류발행, 3-기타 (거래구분이 취소거래일 경우에만 작성)
		'CancelNTSConfirmNum'	=> '',		//취소할 원본 현금영수증의 국세청 승인번호
		'CancelNTSConfirmDate'	=> '',		//취소할 원본 현금영수증의 국세청 승인일자 (YYYYMMDD)
	);

	$Result = $BaroService_CASHBILL->RegistCashBill(array(
		'CERTKEY'	=> $CERTKEY,
		'CorpNum'	=> $CashBill['FranchiseCorpNum'],
		'UserID'	=> $CashBill['FranchiseMemberID'],
		'Invoice'	=> $CashBill
	))->RegistCashBillResult;

	//echo $Result;
?>