<?PHP
	//-----------------------------------------------------------------------------
	// PAYCO 주문 예약 페이지 샘플  ( PHP EASYPAY / PAY2 )
	// payco_reserve.php
	// 2016-08-26	PAYCO기술지원 <dl_payco_ts@nhnent.com>
	//-----------------------------------------------------------------------------	
	header('Content-Type: text/html; charset=utf-8'); 
	include_once(dirname(__FILE__).'/inc.php');
	actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

	if( is_mobile() == true){  // 모바일에 따른 처리
	require(PG_M_DIR."/payco/payco_config.php");
	}else{
	require(PG_DIR."/payco/payco_config.php");
	}

	

	//-----------------------------------------------------------------------------
	// 이 문서는 json 형태의 데이터를 반환합니다.
	//-----------------------------------------------------------------------------
	// header("Content-Type:application/json"); 

	//---------------------------------------------------------------------------------
	// 이전 페이지에서 전달받은 고객 주문번호 설정, 장바구니 번호 설정
	//---------------------------------------------------------------------------------
	$customerOrderNumber =$ordernum = $_REQUEST["customerOrderNumber"];		// 주문번호
	$cartNo              = $_REQUEST["cartNo"]; // 장바구니 번호
	
	//-----------------------------------------------------------------------------
	// (로그) 호출 시점과 호출값을 파일에 기록합니다.
	//-----------------------------------------------------------------------------
	Write_Log("payco_reserve.php is Called - customerOrderNumber : $customerOrderNumber , cartNo : $cartNo");
	

	//---------------------------------------------------------------------------------
	// 상품정보 변수 선언 및 초기화
	//---------------------------------------------------------------------------------
	Global $cpId, $productId ;

	// 주문정보 추출
	$row = _MQ("select * from smart_order where o_ordernum='". $ordernum ."' ");
	if($row['o_ordernum'] == ''){ echo json_encode(array('code'=>'fail')); exit; }


	// 2017-06-16 ::: 부가세율설정 - 배송비 과세 / 면세 비용 계산 ::: JJC
	//$ordernum = $ordernum; // --> 주문번호 
	$order_row = $row; // --> 주문배열정보
	include(OD_PROGRAM_ROOT."/shop.order.result.vat_calc.php");
	// 2017-06-16 ::: 부가세율설정 - 배송비 과세 / 면세 비용 계산 ::: JJC

	// echo json_encode(array('code'=>'fail','data'=>$sellerKey)); exit;


	//---------------------------------------------------------------------------------
	// 변수 초기화
	//---------------------------------------------------------------------------------
	$TotalProductPaymentAmt = 0;		//주문 상품이 여러개일 경우 상품들의 총 금액을 저장할 변수
	$OrderNumber			= 0;		//주문 상품이 여러개일 경우 순번을 매길 변수

	//---------------------------------------------------------------------------------
	// 구매 상품을 변수에 셋팅 ( JSON 문자열을 생성 )
	//---------------------------------------------------------------------------------
	$ProductRows = array();				// (필수) 주문서에 담길 상품 목록 생성

	$tmpTotalTaxfreeAmt = 0;			// 면세상품합
	$tmpTotalTaxableAmt = 0;			// 과세상품합
	$tmpTotalVatAmt		= 0;			// 부가세합


	// 주문상품정보 추출
	$sres = _MQ_assoc("
		select 
			op.*,o.*, p.p_name,p.p_cpid, p.p_img_list_square , p.p_code, p.p_coupon,p.p_stock, p.p_shoppingPay, p_shoppingPay_use
		from smart_order as o
		inner join smart_order_product as op on (op.op_oordernum = o.o_ordernum )
		inner join smart_product as p on ( p.p_code=op.op_pcode )
		where op_oordernum='{$ordernum}'
		group by op_pcode
		order by op_uid
	");

	unset($op_price_delivery,$op_price_sum_total, $_num);
	foreach($sres as $k=>$v){
		// No. 설정
		$_num++;

		$res = _MQ_assoc("
			select *
			from smart_order_product as op
			inner join smart_product as p on ( p.p_code=op.op_pcode )
			where op_pcode = '".$v['op_pcode']."' AND op_oordernum='{$ordernum}'
			order by op_uid asc
		");
		unset($op_option_print,$option_name,$option_cnt,$op_total_price, $add_delivery_print,$op_total_point,$op_status_class,$op_delivery_price, $op_add_delivery_price);
		foreach($res as $sk => $sv) {

			/*------- 상품명 (결제시 상품명으로 사용됨) ------*/
			if(!$app_product_name)  {
				$app_product_name_tmp = $sv['op_pname'];
				$app_product_name = $sv['op_pname'];
			} else {
				$app_product_cnt++;
				$app_product_name = $app_product_name_tmp ." 외 ".$app_product_cnt."건";
			}
			/*------- // 상품명 (결제시 상품명으로 사용됨) ------*/


			$option_name = !$sv['op_option1'] ? '옵션없음' : trim(($sv['op_is_addoption']=='Y' ? '<span class="icon add">추가</span>' : '<span class="icon">필수</span>') . $sv['op_option1'].' '.$sv['op_option2'].' '.$sv['op_option3']);
			$option_name = cutstr($option_name,100);			// 보조상품명



			//---------------------------------------------------------------------------------
			// 상품정보 값 입력
			//---------------------------------------------------------------------------------
			$OrderNumber					= $OrderNumber + 1;									// 상품에 순번을 정하기 위해 값을 증가합니다.

			$orderQuantity					= $sv['op_cnt'];												// (필수) 주문수량 (1로 세팅)
			$productUnitPrice				= $sv['op_price'];											// (필수) 상품 단가      ( 테스트용으로써 100,000원으로 설정. )
			$productUnitPaymentPrice		= $sv['op_price']+$sv['op_delivery_price']+$sv['op_add_delivery_price'];										// (필수) 상품 결제 단가 ( 테스트용으로써 100,000원으로 설정. 배송비 설정시 상품가격에 포함시킴 ex)2,500 )

			 
			
			
			//상품단가(productAmt)는 원 상품단가이고 상품결제단가(productPaymentAmt)는 상품단가에서 할인등을 받은 금액입니다. 실제 결제에는 상품결제단가가 사용됩니다.
			$productAmt						= $productUnitPrice * $orderQuantity;				// (필수) 상품 결제금액(상품단가 * 수량)
			$productPaymentAmt				= $productUnitPaymentPrice * $orderQuantity;		// (필수) 상품 결제금액(상품결제단가 * 수량)
			$TotalProductPaymentAmt			= $TotalProductPaymentAmt + $productPaymentAmt;		// 주문정보를 구성하기 위한 상품들 누적 결제 금액(상품 결제 금액)
			
			// @ -- 금액보정 추가
			if( $sk == (count($res)-1) && $TotalProductPaymentAmt != $row['o_price_real'] ) 
			{
				$exp_price = $row['o_price_real'] - $TotalProductPaymentAmt;
				$productAmt =$productAmt + $exp_price;
				$productPaymentAmt = $productPaymentAmt + $exp_price;
				$TotalProductPaymentAmt =  $row['o_price_real'];
			}
				
			$iOption						= $option_name;									  		// 옵션 ( 최대 100 자리 )
			$sortOrdering					= $OrderNumber;										// (필수) 상품노출순서, 10자 이내
			$productName					= $sv['op_pname'];								// (필수) 상품명, 4000자 이내
			$orderConfirmUrl				= $system['url']."/?pn=product.view&pcode=".$sv['op_pcode'];												// 주문완료 후 주문상품을 확인할 수 있는 url, 4000자 이내
			$orderConfirmMobileUrl			= $orderConfirmUrl;		

			# 상품의 썸네일
			$p_thumb	= get_img_src('thumbs_s_'.$v['p_img_list_square']); // 상품 이미지 
			if($p_thumb=='') $p_thumb = $SkinData['skin_url']. '/images/skin/thumb.gif';
													// 주문완료 후 주문상품을 확인할 수 있는 모바일 url, 1000자 이내
			$productImageUrl				= $p_thumb;	// 상품 이미지;		// 이미지URL (배송비 상품이 아닌 경우는 필수), 4000자 이내, productImageUrl에 적힌 이미지를 썸네일해서 PAYCO 주문창에 보여줍니다.
			$sellerOrderProductReferenceKey = $sv['op_uid']	;									// 외부가맹점에서 관리하는 주문상품 연동 키(sellerOrderProductReferenceKey)는 주문 별로 고유한 key이어야 한다.
			//$taxationType					= "TAXATION";										// 과세타입(기본값 : 과세),	DUTYFREE :면세,	SMALL : 영세,	TAXATION : 과세
			//$taxationType					= "DUTYFREE";										// 과세타입(기본값 : 과세),	DUTYFREE :면세,	SMALL : 영세,	TAXATION : 과세
			$taxationType = '';


			//---------------------------------------------------------------------------------
			// 상품값으로 읽은 변수들로 Json String 을 작성합니다.
			//---------------------------------------------------------------------------------
			try {
				$ProductsList = array();
				$ProductsList["cpId"]					= $cpId;
				$ProductsList["productId"]				= $productId;
				$ProductsList["productAmt"]				= $productAmt;
				$ProductsList["productPaymentAmt"]		= $productPaymentAmt;
				$ProductsList["orderQuantity"]			= $orderQuantity;
				$ProductsList["option"]					= urlencode($iOption);
				$ProductsList["sortOrdering"]			= $sortOrdering;
				$ProductsList["productName"]			= urlencode($productName);

				if ( $orderConfirmUrl					!= "") {		$ProductsList["orderConfirmUrl"]				= $orderConfirmUrl; 				};
				if ( $orderConfirmMobileUrl				!= "") {		$ProductsList["orderConfirmMobileUrl"]			= $orderConfirmMobileUrl;			};
				if ( $productImageUrl					!= "") {		$ProductsList["productImageUrl"]				= $productImageUrl;					};
				if ( $sellerOrderProductReferenceKey	!= "") {		$ProductsList["sellerOrderProductReferenceKey"] = $sellerOrderProductReferenceKey;	};			
				array_push($ProductRows, $ProductsList);

			} catch ( Exception $e ) {
				$Error_Return = array();
				$Error_Return["result"]		= "DB_RECORDSET_ERROR";
				$Error_Return["message"]	= $e->getMassage();
				$Error_Return["code"]		= $e->getLine();
				return json_encode($Error_Return);
			}

		} // end foreach :: sv


		//-----------------------------------------------------------------------------------------------------------------------------------------------------------
		// $tmpTotalTaxfreeAmt(면세상품 총액) / $tmpTotalTaxableAmt(과세상품 총액) / $tmpTotalVatAmt(부가세 총액) -> 일부 필요한 가맹점을위한 예제임 (필요시 사용)
		//------------------------------------------------------------------------------------------------------------------------------------------------------------
		
	
	} // end foreach :: sres


/*

	// ### return ################
	// 총과세 : $app_vat_Y 
	// 총면세 : $app_vat_N 
	// 과세공급가 : $app_vat_Y_tot
	// 과세부가세 : $app_vat_Y_vat
	$app_vat_Y_vat = round($app_vat_Y / 11); // 과세부가세
	$app_vat_Y_tot = $app_vat_Y - $app_vat_Y_vat; // 과세공급가
*/
	if( $siteInfo['s_vat_product'] == 'N'){ // 면세
		$tmpTotalTaxfreeAmt =  $app_vat_N;
	}else if($siteInfo['s_vat_product'] == 'C'){ // 복합과세
		$tmpTotalTaxfreeAmt = $app_vat_N;
		$tmpTotalTaxableAmt = $app_vat_Y;
		$tmpTotalVatAmt		= $app_vat_Y_vat;		
	}else{ // Y  과세
		$tmpTotalTaxableAmt = $app_vat_Y; // 과세 상품총액
		$tmpTotalVatAmt = $app_vat_Y_vat;  // 부가세 총액
	}




	//---------------------------------------------------------------------------------------------------------------------------------
	// 주문서에 담길 부가 정보를 JSON 으로 작성 (필요시 사용) 
	// payExpiryYmdt			: 해당 주문예약건 만료 처리 일시 
	// virtualAccountExpiryYmd  : 가상계좌만료일시
	//
	// cancelMobileUrl			: 모바일 결제페이지에서 취소 버튼 클릭시 이동할 URL (결제창 이전 URL 등). 미입력시 메인 URL로 이동
	/// 모바일 결제페이지에서 취소 버튼 클릭시 이동할 URL (결제창 이전 URL 등)
	/// 1순위 : (앱결제인 경우) 주문예약 > customUrlSchemeUseYn 의 값이 Y인 경우 => "nebilres://orderCancel" 으로 이동
	/// 2순위 : 주문예약 > extraData > cancelMobileUrl 값이 있을시 => cancelMobileUrl 이동
	/// 3순위 : 주문예약시 전달받은 returnUrl 이동 + 실패코드(오류코드:2222)
	/// 4순위 : 가맹점 URL로 이동(가맹점등록시 받은 사이트URL)
	/// 5순위 : 이전 페이지로 이동 => history.Back();
	//
	// viewOptions			    : 화면UI옵션(showMobileTopGnbYn : 모바일 상단 GNB 노출여부 , iframeYn : Iframe 호출현재 iframeYN의 용도는 없으며, 차후 iframe 이슈 대응을 위한 필드로 iframe 사용인 경우는 Y로사용 )
	//---------------------------------------------------------------------------------------------------------------------------------
	 
	//$payExpiryYmdt			             	= "20171231180000";	             // 미적용시, 자동으로 만료시간 지정됨.
	//$virtualAccountExpiryYmd					= "20171231180000";
	

	if(is_mobile() == true){ 
		$appUrl = $siteInfo['s_pg_app_scheme'] != '' ? $siteInfo['s_pg_app_scheme']."://" : null;														 // IOS 인앱 결제시 ISP 모바일 등의 앱에서 결제를 처리한 뒤 복귀할 앱 URL
		$cancelMobileUrl 							= $AppWebPath."/?pn=shop.order.result";       //모바일 PAYCO 결제창 [취소] 버튼 선택
	}

	$viewOptionsArry 							= array();                      
	$viewOptionsArry["showMobileTopGnbYn"]		= "N";
	$viewOptionsArry["iframeYn"]				= "N";
	//$viewOptions = json_encode($viewOptionsArry);                             // 배열 형태를 JSON 으로 Encode 금지. 주문예약 요청 JSON 형식에 맞지않는 역슬래시가 자동 추가됨.
		
	$extraDataArray								= array();
	//$extraDataArray["payExpiryYmdt"] 			    = $payExpiryYmdt;
	//$extraDataArray["virtualAccountExpiryYmd"] 	= $virtualAccountExpiryYmd;
	
	if(is_mobile() == true){ 	
		$extraDataArray["appUrl"] 					= $appUrl;
		$extraDataArray["cancelMobileUrl"] 			= $cancelMobileUrl;
	}

	$extraDataArray["viewOptions"] 				= $viewOptionsArry; 	
	$extraDataArray['includePaymentMethodCodes'] = $siteInfo['payco_paymethod '] != '' ? $siteInfo['payco_paymethod'] : array();//ex) array('35','04','31','98','75','76','77'); // 결제수단 선택
	
	$extraData = addslashes(json_encode($extraDataArray));
		
	Write_Log("payco_reserve.php is Called >>>> extraData : $extraData"); // 로그기록
	
	
	//---------------------------------------------------------------------------------
	// 주문정보 변수 선언
	//---------------------------------------------------------------------------------
	Global $sellerKey,$AppWebPath;
	
	
	//---------------------------------------------------------------------------------
	// 주문정보 값 입력 ( 가맹점 수정 가능 부분 )
	//---------------------------------------------------------------------------------
	$sellerOrderReferenceKey		= $customerOrderNumber;														// (필수) 외부가맹점의 주문번호
	$sellerOrderReferenceKeyType	= "UNIQUE_KEY";																//  외부가맹점의 주문번호 타입 UNIQUE_KEY 유니크 키 - 기본값, DUPLICATE_KEY 중복 가능한 키( 외부가맹점의 주문번호가 중복 가능한 경우 사용)

	//$sellerOptions = "{\\\"clientIp\\\":\\\"210.206.104.164\\\",\\\"memberId\\\":\\\"userid\\\"}"; // 게임결제용_판매자부가정보
	
	$iCurrency						= "KRW";																	// 통화(default=KRW)
	//$totalOrderAmt				= $TotalProductPaymentAmt;													// (필수) 총 주문금액.  // 않쓰임
	if( $TotalProductPaymentAmt != $row['o_price_real']){ $TotalProductPaymentAmt = $row['o_price_real']; }
	$totalPaymentAmt				= $TotalProductPaymentAmt;													// (필수) 총 결재 할 금액.

	$orderTitle						= $app_product_name;										// 주문 타이틀	
	
	$returnUrl						= $AppWebPath.OD_PROGRAM_DIR.'/shop.order.result.payco_return.php';											// 주문완료 후 Redirect 되는 Url
	//---------------------------------------------------------------------------------------------------------------------------------
	//$returnUrlParam 담길 값를 JSON 으로 작성 (필요시 사용)
	//---------------------------------------------------------------------------------------------------------------------------------
	$returnUrlParamArray = array();
	$returnUrlParamArray["cartNo"] = $cartNo;                      // 장바구니 번호
	
	// 면세상품일 경우
	if( $taxationType == "DUTYFREE"){
		$returnUrlParamArray["tmpTotalTaxfreeAmt"] = $tmpTotalTaxfreeAmt;      // 면세금액 ( 총 결제 할 금액 적용 )
	
	// 과세상품일 경우
	} elseif( $taxationType == "TAXATION") {
		$returnUrlParamArray["tmpTotalTaxableAmt"] = $tmpTotalTaxableAmt;      // 과세금액
		$returnUrlParamArray["tmpTotalVatAmt"]     = $tmpTotalVatAmt;          // 부과세금액
		
	// 복합상품일 경우
	}else{
		$returnUrlParamArray["tmpTotalTaxfreeAmt"] = 0;
		$returnUrlParamArray["tmpTotalTaxableAmt"] = $tmpTotalTaxableAmt;      // 과세금액
		$returnUrlParamArray["tmpTotalVatAmt"]     = $tmpTotalVatAmt;          // 부과세금액
	}	
	
	$returnUrlParamArrayJSON = addslashes(json_encode($returnUrlParamArray));   // {\"cartNo\":\"CartNo_12345\"}
	
	//주문완료 시 PAYCO에서 가맹점의 Service API 호출할때 같이 전달할 파라미터(payco_reserve.php 에서 payco_return.php 로 전달할 값을 JSON 형태의 문자열로 전달)
	$returnUrlParam                = $returnUrlParamArrayJSON;
	Write_Log("payco_reserve.php is Called - returnUrlParam : $returnUrlParam");
	
	
	$nonBankbookDepositInformUrl	= $AppWebPath.'/shop.order.result.payco_without_bankbook.php';								    //무통장입금완료통보 URL
	$orderMethod					= "EASYPAY";																// (필수) 주문유형(=결재유형) - 체크아웃형 : CHECKOUT - 간편결제형+가맹점 id 로그인 : EASYPAY_F , 간편결제형+가맹점 id 비로그인(PAYCO 회원구매) : EASYPAY
	$orderChannel					= is_mobile() == true ? 'MOBILE':'PC';																		// 주문채널 ( default : PC / MOBILE )
	$inAppYn						= "N";																		// 인앱결제 여부( Y/N ) ( default = N )
	$individualCustomNoInputYn		= "N"	;																	// 개인통관고유번호 입력 여부 ( Y/N ) ( default = N )
	$orderSheetUiType				= "GRAY";																	// 주문서 UI 타입 선택 ( 선택 가능값 : RED / GRAY )
	$payMode						= "PAY2";																	// 결제모드 ( PAY1 - 결제인증, 승인통합 / PAY2 - 결제인증, 승인분리 )
	
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------
	// $tmpTotalTaxfreeAmt(면세상품 총액) / $tmpTotalTaxableAmt(과세상품 총액) / $tmpTotalVatAmt(부가세 총액) -> 일부 필요한 가맹점을위한 예제임 (필요시 사용)
	//------------------------------------------------------------------------------------------------------------------------------------------------------------

	$totalTaxfreeAmt				= $tmpTotalTaxfreeAmt;														// 면세금액(면세상품의 공급가액 합)					
	$totalTaxableAmt				= $tmpTotalTaxableAmt;														// 과세금액(과세상품의 공급가액 합)
	$totalVatAmt					= $tmpTotalVatAmt;															// 부가세(과세상품의 부가세 합)
	


	//---------------------------------------------------------------------------------
	// 설정한 주문정보들을 Json String 을 작성합니다.
	//---------------------------------------------------------------------------------		

	$json = array();
	try {

		// echo json_encode(array('code'=>'fail','data'=>$sellerKey)); exit;
		$strJson = array();
		$strJson["sellerKey"]					= $sellerKey;
		$strJson["sellerOrderReferenceKey"]		= $sellerOrderReferenceKey;
		$strJson["sellerOrderReferenceKeyType"] = $sellerOrderReferenceKeyType;
		
		// $strJson["sellerOptions"] = $sellerOptions; // 게임결제용_판매자부가정보
		
		$strJson["totalPaymentAmt"]			= $totalPaymentAmt;
		$strJson["orderTitle"]				= urlencode($orderTitle);
		$strJson["orderMethod"]				= $orderMethod;		
		if ( $iCurrency						!= "") {		$strJson["currency"]					= $iCurrency;					};
		if ( $returnUrl						!= "") {		$strJson["returnUrl"]					= $returnUrl;					};
		if ( $returnUrlParam				!= "") {		$strJson["returnUrlParam"]				= $returnUrlParam;				};
		if ( $nonBankbookDepositInformUrl	!= "") {		$strJson["nonBankbookDepositInformUrl"] = $nonBankbookDepositInformUrl;	};		
		if ( $orderChannel					!= "") {		$strJson["orderChannel"]				= $orderChannel;				};
		if ( $inAppYn						!= "") {		$strJson["inAppYn"]						= $inAppYn;						};
		if ( $individualCustomNoInputYn		!= "") {		$strJson["individualCustomNoInputYn"]	= $individualCustomNoInputYn;	};
		if ( $orderSheetUiType				!= "") {		$strJson["orderSheetUiType"]			= $orderSheetUiType;			};
		if ( $payMode						!= "") {		$strJson["payMode"] = $payMode;											};
		
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------
	// $tmpTotalTaxfreeAmt(면세상품 총액) / $tmpTotalTaxableAmt(과세상품 총액) / $tmpTotalVatAmt(부가세 총액) -> 일부 필요한 가맹점을위한 예제임 (필요시 사용)
	//------------------------------------------------------------------------------------------------------------------------------------------------------------
		$strJson["totalTaxfreeAmt"]			= $totalTaxfreeAmt;
		$strJson["totalTaxableAmt"]			= $totalTaxableAmt;
		$strJson["totalVatAmt"]				= $totalVatAmt;

		$strJson["extraData"]				= $extraData;
		$strJson["orderProducts"]			= $ProductRows;

		$res =  payco_reserve(urldecode(stripslashes(json_encode($strJson))));  //주문예약 API 호출 함수



		echo $res;
	} catch ( Exception $e ) {
		$Error_Return				= array();
		$Error_Return["result"]		= "RESERVE_ERROR";
		$Error_Return["message"]	= $e->getMassage();
		$Error_Return["code"]		= $e->getCode();
		Write_Log("payco_reserve.php Logical Error : Code - ".$e->getCode().", Description - ".$e->getMessage());
		return json_encode($Error_Return);
	}
?>

<?php actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행 ?>