<?PHP

	//----------------------------------------------------------------------------------------------------------------------
	// PAYCO 주문 예약 페이지 ( PHP )
	// 2016-08-26	PAYCO기술지원 <dl_payco_ts@nhnent.com>
	// payco_util.php 는 PAYCO와 통신하기 위한 필수 함수들을 모아놓은 파일입니다. 
	// 반드시 include 하시기 바랍니다.
	//----------------------------------------------------------------------------------------------------------------------
	include(dirname(__FILE__)."/payco_util.php");

	//----------------------------------------------------------------------------------------------------------------------
	// 캐릭터셋 지정
	//-----------------------------------------------------------------------------------------------------------------------
	// header("charset=utf8"); 

	//-----------------------------------------------------------------------------------------------------------------------
	//
	// 환경변수 선언
	//
	//------------------------------------------------------------------------------------------------------------------------
	// 가맹점 코드 선언 ( 가맹점 수정 부분 )
	//------------------------------------------------------------------------------------------------------------------------

	/*
		// 테스트 정보 
		S0FSJE
	*/

	//-------------------------------------------------------------------------------------------------------------------------
	// 가맹점 API 가 호출 당할 경우 도메인 또는 아이피 셋팅하기 위한 변수 ( 도메인이 있을 경우 도메인을 셋팅하시면 됩니다. )
	// 용도 : serviceUrl 및 returnUrl, nonBankbookDepositInformUrl 용.
	// API 호출시 http:// 부터 경로를 전체적으로 써줘야 HttpRequest 통신시 오류발생 안함.
	//--------------------------------------------------------------------------------------------------------------------------
	
	$AppWebPath =$system['url'].OD_PROGRAM_DIR;	

	//--------------------------------------------------------------------------------------------------------------------------
	// 운영/개발 설정
	// Log 사용 여부 설정
	//---------------------------------------------------------------------------------------------------------------------------
	$appMode	= $siteInfo['payco_mode'];		// REAL - 실서버 운영, TEST - 개발(테스트) -- S0FSJE
	$LogUse		= true;			// Log 사용 여부 ( True = 사용, False = 미사용 )
	//---------------------------------------------------------------------------------------------------------------------------
	// API 주소 설정 ( 상단 appMode 에 따라 테스트와 실서버로 분기됩니다. )
	//--------------------------------------------------------------------------------------------------------------------------
	if($appMode == "test"){
		$sellerKey				= $arrPaycoInfo['test']['sellerKey'];		//(필수) 가맹점 코드 - 파트너센터에서 알려주는 값으로, 초기 연동 시 PAYCO에서 쇼핑몰에 값을 전달한다.
		$cpId					= $arrPaycoInfo['test']['cpId'];			//(필수) 상점ID, 30자 이내
		$productId				= $arrPaycoInfo['test']['productId'];				//(필수) 상품ID, 50자 이내
		//$deliveryId				= "DELIVERY_PROD"	;		//(필수) 배송비상품ID, 50자 이내
		//$deliveryReferenceKey	= "DV0001"			;		//(필수) 가맹점에서 관리하는 배송비상품 연동 키, 100자 이내, 고정

		$URL_reserve			= "https://alpha-api-bill.payco.com/outseller/order/reserve";
		$URL_approval			= "https://alpha-api-bill.payco.com/outseller/payment/approval";
		$URL_cancel_check		= "https://alpha-api-bill.payco.com/outseller/order/cancel/checkAvailability";
		$URL_cancel				= "https://alpha-api-bill.payco.com/outseller/order/cancel";
		$URL_upstatus			= "https://alpha-api-bill.payco.com/outseller/order/updateOrderProductStatus";
		$URL_cancelMileage		= "https://alpha-api-bill.payco.com/outseller/order/cancel/partMileage";
		$URL_checkUsability		= "https://alpha-api-bill.payco.com/outseller/code/checkUsability";
		$URL_detailForVerify	= "https://alpha-api-bill.payco.com/outseller/payment/approval/getDetailForVerify"; // alpha(개발) 결제상세 조회(검증용)API URL
	}else{

		$sellerKey				= $siteInfo['payco_sellerkey']			;		//(필수) 가맹점 코드 - 파트너센터에서 알려주는 값으로, 초기 연동 시 PAYCO에서 쇼핑몰에 값을 전달한다.
		$cpId					= $siteInfo['payco_cpid']		;			//(필수) 상점ID, 30자 이내
		$productId				= $siteInfo['payco_productid']		;				//(필수) 상품ID, 50자 이내
		//$deliveryId				= "DELIVERY_PROD"	;		//(필수) 배송비상품ID, 50자 이내
		//$deliveryReferenceKey	= "DV0001"			;		//(필수) 가맹점에서 관리하는 배송비상품 연동 키, 100자 이내, 고정

		$URL_reserve			= "https://api-bill.payco.com/outseller/order/reserve";
		$URL_approval			= "https://api-bill.payco.com/outseller/payment/approval";
		$URL_cancel_check		= "https://api-bill.payco.com/outseller/order/cancel/checkAvailability";
		$URL_cancel				= "https://api-bill.payco.com/outseller/order/cancel";
		$URL_upstatus			= "https://api-bill.payco.com/outseller/order/updateOrderProductStatus";
		$URL_cancelMileage		= "https://api-bill.payco.com/outseller/order/cancel/partMileage";
		$URL_checkUsability		= "https://api-bill.payco.com/outseller/code/checkUsability";
		$URL_detailForVerify	= "https://api-bill.payco.com/outseller/payment/approval/getDetailForVerify";      // (운영)결제상세 조회(검증용)API URL
	}
	
	//--------------------------------------------------------------------------------------------------------------------------
	// 로그 파일 선언
	//--------------------------------------------------------------------------------------------------------------------------
	$todate			= str_replace("-","",date("Y-m-d"));
	$Write_LogFile = dirname(__FILE__)."/log".DIRECTORY_SEPARATOR."Payco_Log_".$todate."_php.txt";
	
	//--------------------------------------------------------------------------------------------------------------------------
	// 접속 브라우저 확인
	//--------------------------------------------------------------------------------------------------------------------------	
	
	if(preg_match('/(iPhone|iPod|iPad|Android|Windows CE|BlackBerry|Symbian|Windows Phone|webOS|Opera Mni|Opera Mobi|POLARIS|IEMobile|lgtelecom|nokia|SonyEricsson|LG|SAMSUNG|Samsung)/i',$_SERVER['HTTP_USER_AGENT'])){
		$isMobile = 0;
		// echo "모바일 웹 브라우저 입니다.";
	}else{
		// echo "웹 브라우저 입니다.";
		$isMobile = 1;
	}		

?>