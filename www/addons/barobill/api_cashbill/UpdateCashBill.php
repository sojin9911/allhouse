<?php include '../../_include/top.php'; ?>
<?php include '../BaroService_CASHBILL.php'; ?>
<p>UpdateCashBill - 수정</p>
<div class="result">
	<?php
	$CERTKEY = '';			//인증키

	$CashBill = array(
		'MgtKey'				=> '',		//연동사부여 문서키	
		'TradeDate'				=> '',		//거래일자 (YYYYMMDD), 공백입력 시 Today로 작성됨.
		'FranchiseCorpNum'		=> '',		//가맹점 사업자번호
		'FranchiseMemberID'		=> '',		//가맹점 바로빌 회원 아이디
		'FranchiseCorpName'		=> '',		//가맹점 회사명
		'FranchiseCEOName'		=> '',		//가맹점 대표자명
		'FranchiseAddr'			=> '',		//가맹점 주소
		'FranchiseTel'			=> '',		//가맹점 전화번호
		'IdentityNum'			=> '',		//소비자 신분확인번호 ("-" 를 제외한 주민등록번호/사업자번호/휴대폰번호/카드번호 중 택1)
		'HP'					=> '',		//소비자 휴대폰번호 (문자 전송시 활용)
		'Fax'					=> '',		//소비자 팩스번호 (팩스 전송시 활용)
		'Email'					=> '',		//소비자 이메일 (이메일 전송시 활용)
		'TradeType'				=> 'N',		//거래구분 : N-승인거래, D-취소거래
		'TradeUsage'			=> '1',		//거래용도 : 1-소득공제용, 2-지출증빙용 (신분확인번호가 사업자번호인 경우 지출증빙용으로)
		'TradeMethod'			=> '1',		//거래방법 : 1-카드, 3-주민등록번호, 4-사업자번호, 5-휴대폰번호 (신분확인번호 종류에 따라 선택)
		'ItemName'				=> '',		//품목명
		'Amount'				=> '',		//공급가액
		'Tax'					=> '',		//부가세
		'ServiceCharge'			=> '',		//봉사료
		'CancelType'			=> '',		//취소사유 : 1-거래취소, 2-오류발행, 3-기타 (거래구분이 취소거래일 경우에만 작성)
		'CancelNTSConfirmNum'	=> '',		//취소할 원본 현금영수증의 국세청 승인번호
		'CancelNTSConfirmDate'	=> '',		//취소할 원본 현금영수증의 국세청 승인일자 (YYYYMMDD)
	);

	$Result = $BaroService_CASHBILL->UpdateCashBill(array(
		'CERTKEY'	=> $CERTKEY,
		'CorpNum'	=> $CashBill['FranchiseCorpNum'],
		'UserID'	=> $CashBill['FranchiseMemberID'],
		'Invoice'	=> $CashBill
	))->UpdateCashBillResult;

	echo $Result;
	?>
</div>
<?php include '../../_include/bottom.php'; ?>
