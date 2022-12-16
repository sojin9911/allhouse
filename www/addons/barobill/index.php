<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<!--
	바로빌 전자세금계산서 연동서비스
	version : 3.6 (2013-12)

	바로빌 연동개발지원 사이트
	http://dev.barobill.co.kr/

	Copyright (c) 2009 BaroBill
	http://www.barobill.co.kr/


	연동사업자란?
	바로빌이 제공한 WebService를 이용하여 솔루션에 전자세금계산서와 관련된 기능을 개발하는 사업자

	연계사업자란?
	연동사업자가 공급한 솔루션을 사용하는 연동사의 고객



	/include/BaroService_TI.php 에서 기본정보를 설정하세요.
	-->
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

		<style type="text/css">
		body { color: #000000; background-color: white; font-family: Verdana; margin: 0px; }
		p { color: #000000; margin-top: 0px; margin-bottom: 12px; font-family: Verdana; }
		fieldset { margin:0 auto; padding:0; border:1px solid #D5DFE5; }
		ul { margin:0 0 0 20px; padding:10px; }
		li { margin:0; padding:0 0 7px 0; font-size: 12px; color: #333333; }
		#content { font-size: 12px; padding: 0 15px 0 15px; }
		.heading1 { color: #ffffff; font-family: Tahoma; font-size: 24px; background-color: #910000; margin: 0 -15px 0 -15px; padding: 10px 15px 7px 15px; width: 100%; }
		.fieldset1 { padding:0 10px 0 10px; }
		.fieldset2 { margin-top:10px; margin-bottom:10px; }
		.fieldset1 legend{ font-weight:bold; }
		.fieldset2 legend{ margin-left:10px; font-weight:normal; }
		.old { color: #888888; font-size:12px; }
		.arr { color: #FF1100; font-size:12px; font-family:dotum; margin-left: 5px; margin-right: 3px; }
		.new { color: #FF1100; font-size:12px; }
		a:link { color: #336699; font-weight: bold; font-size:12px; text-decoration: underline; }
		a:visited { color: #6699cc; font-weight: bold; font-size:12px; text-decoration: underline; }
		a:active { color: #336699; font-weight: bold; font-size:12px; text-decoration: underline; }
		a:hover { color: #cc3300; font-weight: bold; font-size:12px; text-decoration: underline; }
		</style>

		<title>BaroService_TI(전자세금계산서) 웹 서비스</title>
	</head>

	<body>

		<div id="content">

			<p class="heading1">BaroService_TI(전자세금계산서) PHP5 Sample</p><br />

			<p class="intro">바로빌 전자세금계산서 연동서비스</p>

			<p class="intro">다음 작업이 지원됩니다. 형식 정의를 보려면 <a href="https://testws.baroservice.com:8010/edoc.asmx?WSDL">서비스 설명</a>을 참조하십시오. </p>

			<br />

			<fieldset class="fieldset1">
				<legend>전자세금계산서 관련 API</legend>

				<fieldset class="fieldset2">
					<legend>문서 프로세스</legend>
					<ul>
						<li><a href="api_ti/RegistTaxInvoice.php">RegistTaxInvoice</a> - 일반세금계산서 등록</li>
						<li><a href="api_ti/RegistTaxInvoiceEX.php">RegistTaxInvoiceEX</a> - 일반세금계산서 등록 (승인 시 자동발행 옵션 추가)</li>
						<li><a href="api_ti/RegistModifyTaxInvoice.php">RegistModifyTaxInvoice</a> - 수정세금계산서 등록</li>
						<li><a href="api_ti/RegistModifyTaxInvoiceEX.php">RegistModifyTaxInvoiceEX</a> - 수정세금계산서 등록 (승인 시 자동발행 옵션 추가)</li>
						<li><a href="api_ti/UpdateTaxInvoice.php">UpdateTaxInvoice</a> - 수정</li>
						<li><a href="api_ti/UpdateTaxInvoiceEX.php">UpdateTaxInvoiceEX</a> - 수정 (승인 시 자동발행 옵션 추가)</li>
						<li><a href="api_ti/PreIssueTaxInvoice.php">PreIssueTaxInvoice</a> - 발행예정</li>
						<li><a href="api_ti/IssueTaxInvoice.php">IssueTaxInvoice</a> - 발행</li>
						<li><a href="api_ti/ProcTaxInvoice.php">ProcTaxInvoice</a> - 프로세스 처리</li>
						<li><a href="api_ti/DeleteTaxInvoice.php">DeleteTaxInvoice</a> - 삭제</li>
					</ul>
				</fieldset>

				<fieldset class="fieldset2">
					<legend>첨부파일</legend>
					<ul>
						<li><a href="api_ti/GetAttachedFileList.php">GetAttachedFileList</a> - 첨부파일 목록</li>
						<li><a href="api_ti/AttachFileByFTP.php">AttachFileByFTP</a> - 파일 첨부</li>
						<li><a href="api_ti/DeleteAttachFileWithFileIndex.php">DeleteAttachFileWithFileIndex</a> - 첨부파일 삭제</li>
						<li><a href="api_ti/DeleteAttachFile.php">DeleteAttachFile</a> - 첨부파일 전체삭제</li>
					</ul>
				</fieldset>

				<fieldset class="fieldset2">
					<legend>문서 연결</legend>
					<ul>
						<li><a href="api_ti/GetLinkedDocs.php">GetLinkedDocs</a> - 연결된 문서 목록</li>
						<li><a href="api_ti/MakeDocLinkage.php">MakeDocLinkage</a> - 문서 연결</li>
						<li><a href="api_ti/RemoveDocLinkage.php">RemoveDocLinkage</a> - 문서 연결 해제</li>
					</ul>
				</fieldset>

				<fieldset class="fieldset2">
					<legend>문서 정보</legend>
					<ul>
						<li><a href="api_ti/GetTaxInvoice.php">GetTaxInvoice</a> - 문서 정보</li>
						<li><a href="api_ti/GetTaxInvoiceLog.php">GetTaxInvoiceLog</a> - 문서 이력</li>
						<li><a href="api_ti/GetTaxInvoiceState.php">GetTaxInvoiceState</a> - 문서 상태</li>
						<li><a href="api_ti/GetTaxInvoiceStates.php">GetTaxInvoiceStates</a> - 문서 상태 (대량, 100건 까지)</li>
						<li><a href="api_ti/GetTaxInvoiceStateEX.php">GetTaxInvoiceStateEX</a> - 문서 상태 (수신확인, 등록일시, 작성일자, 발행예정일시, 발행일시 추가)</li>
						<li><a href="api_ti/GetTaxInvoiceStatesEX.php">GetTaxInvoiceStatesEX</a> - 문서 상태 (수신확인, 등록일시, 작성일자, 발행예정일시, 발행일시 추가) (대량, 100건 까지)</li>
					</ul>
				</fieldset>

				<fieldset class="fieldset2">
					<legend>부가서비스</legend>
					<ul>
						<li><a href="api_ti/ReSendEmail.php">ReSendEmail</a> - 이메일 재전송</li>
						<li><a href="api_ti/ReSendSMS.php">ReSendSMS</a> - 문자 재전송</li>
						<li><a href="api_ti/SendInvoiceSMS.php">SendInvoiceSMS</a> - 문자 전송 (문서이력에 기록됨)</li>
						<li><a href="api_ti/SendInvoiceFax.php">SendInvoiceFax</a> - 팩스 전송 (문서이력에 기록됨)</li>
					</ul>
				</fieldset>
				
				<fieldset class="fieldset2">
					<legend>국세청 관련</legend>
					<ul>
						<li><a href="api_ti/SendToNTS.php">SendToNTS</a> - 세금계산서 국세청 즉시 전송</li>		
						<li><a href="api_ti/GetNTSSendOption.php">GetNTSSendOption</a> - 국세청 전송설정 확인</li>
						<li><a href="api_ti/ChangeNTSSendOption.php">ChangeNTSSendOption</a> - 국세청 전송설정 변경</li>
					</ul>
				</fieldset>

				<fieldset class="fieldset2">
					<legend>기타</legend>
					<ul>
						<li><a href="api_ti/CheckMgtNumIsExists.php">CheckMgtNumIsExists</a> - 관리번호 사용여부 확인</li>
						<li><a href="api_ti/GetTaxInvoicePopUpURL.php">GetTaxInvoicePopUpURL</a> - 문서 내용보기 팝업 URL</li>
						<li><a href="api_ti/GetTaxInvoicePrintURL.php">GetTaxInvoicePrintURL</a> - 인쇄 팝업 URL</li>
						<li><a href="api_ti/GetTaxInvoicesPrintURL.php">GetTaxInvoicesPrintURL</a> - 대량인쇄 팝업 URL</li>
						<li><a href="api_ti/GetTaxInvoiceMailURL.php">GetTaxInvoiceMailURL</a> - 이메일의 보기버튼 URL</li>
						<li><a href="api_ti/GetEmailPublicKeys.php">GetEmailPublicKeys</a> - ASP업체 Email 목록확인</li>
					</ul>
				</fieldset>

			</fieldset>

			<br />
			<br />

			<fieldset class="fieldset1">
				<legend>바로빌 기본 API</legend>

				<fieldset class="fieldset2">
					<legend>회원사 정보</legend>
					<ul>
						<li><a href="api_barobill/CheckCorpIsMember.php">CheckCorpIsMember</a> - 회원사 여부 확인</li>
						<li><a href="api_barobill/RegistCorp.php">RegistCorp</a> - 회원사 추가</li>
						<li><a href="api_barobill/UpdateCorpInfo.php">UpdateCorpInfo</a> - 회원사 정보 수정</li>
						<li><a href="api_barobill/GetCorpMemberContacts.php">GetCorpMemberContacts</a> - 회원사 담당자 목록</li>
						<li><a href="api_barobill/ChangeCorpManager.php">ChangeCorpManager</a> - 회원사 관리자 변경</li>
					</ul>
				</fieldset>

				<fieldset class="fieldset2">
					<legend>사용자 정보</legend>
					<ul>
						<li><a href="api_barobill/AddUserToCorp.php">AddUserToCorp</a> - 사용자 추가</li>
						<li><a href="api_barobill/UpdateUserInfo.php">UpdateUserInfo</a> - 사용자 정보 수정</li>
						<li><a href="api_barobill/UpdateUserPWD.php">UpdateUserPWD</a> - 사용자 비밀번호 수정</li>
					</ul>
				</fieldset>

				<fieldset class="fieldset2">
					<legend>포인트 관련</legend>
					<ul>
						<li><a href="api_barobill/GetBalanceCostAmount.php">GetBalanceCostAmount</a> - 잔여포인트 확인</li>
						<li><a href="api_barobill/GetBalanceCostAmountOfInterOP.php">GetBalanceCostAmountOfInterOP</a> - 연동사포인트 확인</li>
						<li><a href="api_barobill/GetChargeUnitCost.php">GetChargeUnitCost</a> - 요금 단가 확인</li>
						<li><a href="api_barobill/CheckChargeable.php">CheckChargeable</a> - 과금 가능여부 확인</li>
					</ul>
				</fieldset>

				<fieldset class="fieldset2">
					<legend>공인인증서 관련</legend>
					<ul>
						<li><a href="api_barobill/GetCertificateExpireDate.php">GetCertificateExpireDate</a> - 등록한 공인인증서 만료일 확인</li>
					</ul>
				</fieldset>

				<fieldset class="fieldset2">
					<legend>기타</legend>
					<ul>
						<li><a href="api_barobill/GetBaroBillURL.php">GetBaroBillURL</a> - 바로빌 URL</li>
					</ul>
				</fieldset>

			</fieldset>

		 </div>

	</body>

</html>
