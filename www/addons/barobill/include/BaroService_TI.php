<?php
	/*
	================================================================================================
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
	'================================================================================================
	*/


	//------------------------------------------------------------------------------------------------
	//바로빌 연동서비스 웹서비스 참조(WebService Reference) URL
	$BAROSERVICE_URL = ($siteInfo['TAX_MODE'] == "test" ? 'http://testws.baroservice.com/TI.asmx?WSDL' : 'http://ws.baroservice.com/TI.asmx?WSDL'); //테스트베드용 , 실서비스용 체크
	$CERTKEY = $siteInfo['TAX_CERTKEY'];
	//------------------------------------------------------------------------------------------------


	$BaroService_TI = new SoapClient($BAROSERVICE_URL, array(

								'trace'		=> 'true',

								'encoding'	=> 'UTF-8' //소스를 ANSI로 사용할 경우 euc-kr로 수정

							));


	function getErrStr($CERTKEY, $ErrCode){

			global $BaroService_TI;



			$ErrStr = $BaroService_TI->GetErrString(array(

				'CERTKEY'		=> $CERTKEY,

				'ErrCode'		=> $ErrCode

			))->GetErrStringResult;


			return $ErrStr;

		}


	// - 바로빌 연동사 부여 문서키 생성 ---
	function app_mgtnum_create(){
		// --> 문서키 - 숫자조합으로 15글자 적용, 예)
		// --> 생성원리 1. 5개씩 3단락
		//	--> 생성예. 12345-23456-34567

		$ex = explode(' ', microtime());
		$tmp1 = 'T'.sprintf("%04d" , rand(0,99999));
		$tmp2 = sprintf("%u" , crc32( microtime(). rand(1,99999) ));
		$tmp2 = str_pad( $tmp2 , 10 , '0', STR_PAD_RIGHT);
		$order_a = sprintf("%05d" , substr($tmp2 , 0 , 5));
		$order_b = substr($tmp2 , -5);
		$_code = $tmp1 ."-" . $order_a ."-" . $order_b;

		// - 과거 같은 문서키 여부 확인 ---
		$orderchk = _MQ("select count(*) as cnt from smart_baro_tax where 	MgtKey = '".  strtoupper($_code) ."'");
		if( $orderchk[cnt] > 0 ){
			$_code = app_mgtnum_create();
		}

		return $_code ;
	}
	// - 바로빌 연동사 부여 문서키 생성 ---

	//-------------------------------------------
	//공급자 정보 - 정발행시 세금계산서 작성자
	//------------------------------------------
	function getInvoicerParty($MgtNum=''){
		global $siteInfo;
		$InvoicerParty = array(
			'MgtNum' 		=> $MgtNum ,	 //정발행시 필수입력 - 자체문서관리번호 - 24자리이내 고유키
			'CorpNum' 		=> rm_str($siteInfo['s_company_num']),	//필수입력 - 연계사업자 사업자번호 ('-' 제외, 10자리)
			'TaxRegID' 		=> '1111',
			'CorpName' 		=> $siteInfo['s_company_name'],		//필수입력
			'CEOName' 		=> $siteInfo['s_ceo_name'],				//필수입력
			'Addr' 			=> $siteInfo['s_company_addr'],
			'BizType' 		=> $siteInfo['s_item1'],
			'BizClass' 		=> $siteInfo['s_item2'],
			'ContactID' 	=> $siteInfo['TAX_BAROBILL_ID'],						//필수입력 - 담당자 바로빌 아이디
			'ContactName' 	=> $siteInfo['TAX_BAROBILL_NAME'],				//필수입력
			'TEL' 			=> $siteInfo['s_glbtel'],
			'HP' 			=> $siteInfo['s_glbmanagerhp'],
			'Email' 		=> $siteInfo['s_ademail']			//필수입력
		);
		return $InvoicerParty;
	}

	//-------------------------------------------
	//공급받는자 정보 - 역발행시 세금계산서 작성자
	//------------------------------------------
	function getInvoiceeParty($taxInfo){
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
		return $InvoiceeParty;
	}