<?php
/*
================================================================================================
바로빌 현금영수증 연동서비스
version : 1.2 (2015-10)

바로빌 연동개발지원 사이트
http://dev.barobill.co.kr/

-- 용어 ----
 연계사 - 서비스이용회사
 연동사 - 원데이넷
'================================================================================================
*/


//------------------------------------------------------------------------------------------------
//바로빌 연동서비스 웹서비스 참조(WebService Reference) URL
if($siteInfo['TAX_MODE'] == "test"){
	$BaroService_URL = 'http://testws.baroservice.com/CASHBILL.asmx?WSDL';	//테스트베드용
}else{
	$BaroService_URL = 'http://ws.baroservice.com/CASHBILL.asmx?WSDL';	//실서비스용
}
//------------------------------------------------------------------------------------------------


//------------------------------------------------------------------------------------------------
//바로빌 연동서비스 공통변수 설정
$CERTKEY = $siteInfo['TAX_CERTKEY'];			//인증키
$CorpNum = rm_str($siteInfo['s_company_num']);			//연계사업자 사업자번호 ('-' 제외, 10자리)
$ID = $UserID = $siteInfo['TAX_BAROBILL_ID'];				//연계사업자 아이디
$PWD = $siteInfo['TAX_BAROBILL_PW'];				//연계사업자 비밀번호
$SMSSendYN = false;		//발행(취소) 알림문자 전송여부 (발행비용과 별도로 과금됨)
$FranchiseCorpName = $siteInfo["s_adshop"];		//가맹점 회사명
$FranchiseCEOName = $siteInfo["s_ceo_name"];		//가맹점 대표자명
$FranchiseAddr = $siteInfo["s_company_addr"];		//가맹점 주소
$FranchiseTel = $siteInfo["s_glbtel"];		//가맹점 전화번호
//------------------------------------------------------------------------------------------------


$BaroService_CASHBILL = new SoapClient($BaroService_URL, array(
	'trace' => 'true',
	'encoding' => 'UTF-8' //소스를 ANSI로 사용할 경우 euc-kr로 수정
));

function getErrStr($CERTKEY, $ErrCode){
	global $BaroService_CASHBILL;

	$ErrStr = $BaroService_CASHBILL->GetErrString(array(
		'CERTKEY' => $CERTKEY,
		'ErrCode' => $ErrCode
	))->GetErrStringResult;

	return $ErrStr;
}
?>
