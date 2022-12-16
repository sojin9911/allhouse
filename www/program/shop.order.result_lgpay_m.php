<?php

	include_once(dirname(__FILE__).'/inc.php');
	actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
	$r= $row; // 주문정보 동기화

	$siteDomain = $system['url']; //가맹점 도메인 입력

	// -- 배너정보
	$banner_info = info_banner("mailing_logo",1,"data");

	// -- pg 사 경로
	$configPath 				= PG_M_DIR . "/lgpay/lgdacom"; 						//LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.

	// -- 결제 수단
	$paymethod = array(
		'iche'=>'SC0030',
		'card'=>'SC0010',
		'virtual'=>'SC0040',
		'online'=>'SC0100',
		'hpp'=>'SC0060', // 휴대폰
	);


  /*
   * [결제 인증요청 페이지(STEP2-1)]
   *
   * 샘플페이지에서는 기본 파라미터만 예시되어 있으며, 별도로 필요하신 파라미터는 연동메뉴얼을 참고하시어 추가 하시기 바랍니다.
   */

  /*
   * 1. 기본결제 인증요청 정보 변경
   *
   * 기본정보를 변경하여 주시기 바랍니다.(파라미터 전달시 POST를 사용하세요)
   */
  $CST_PLATFORM               = $siteInfo[s_pg_mode];				//LG유플러스 결제 서비스 선택(test:테스트, service:서비스)
  $CST_MID                    = $siteInfo[s_pg_code];					//상점아이디(LG유플러스으로 부터 발급받으신 상점아이디를 입력하세요)
                                                                      //테스트 아이디는 't'를 반드시 제외하고 입력하세요.
  $LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;  //상점아이디(자동생성)
  $LGD_OID                    = $ordernum;					//주문번호(상점정의 유니크한 주문번호를 입력하세요)
  $LGD_AMOUNT                 = $r[o_price_real];					//결제금액("," 를 제외한 결제금액을 입력하세요)
  $LGD_BUYER                  = $r[o_oname];					//구매자명
  $LGD_PRODUCTINFO            = $app_product_name;			//상품명
  $LGD_BUYEREMAIL             = $r[o_oemail];				//구매자 이메일
  $LGD_CUSTOM_FIRSTPAY        = $paymethod[$r[o_paymethod]];		//상점정의 초기결제수단
  $LGD_TIMESTAMP              = date(YmdHis);                         //타임스탬프

  //$LGD_PCVIEWYN				= $_POST["LGD_PCVIEWYN"];				//휴대폰번호 입력 화면 사용 여부(유심칩이 없는 단말기에서 입력-->유심칩이 있는 휴대폰에서 실제 결제)
	$LGD_CUSTOM_SKIN            = "SMART_XPAY2";                        //상점정의 결제창 스킨

	$LGD_MERTKEY						= $siteInfo['s_pg_key'];			//상점MertKey(mertkey는 상점관리자 -> 계약정보 -> 상점정보관리에서 확인하실수 있습니다)
	$LGD_BUYERID						= $r['o_mid'];				//구매자 아이디
	$LGD_BUYERIP						= $_SERVER["REMOTE_ADDR"];		//구매자IP

	$LGD_ENCODING						= "UTF-8";       //UTF-8
	$LGD_ENCODING_RETURNURL	= "UTF-8";       //UTF-8

	// 에스크로 설정
	$LGD_ESCROW_USEYN			= 'Y'; // 에스크로 사용 여부


  /*
   * 가상계좌(무통장) 결제 연동을 하시는 경우 아래 LGD_CASNOTEURL 을 설정하여 주시기 바랍니다.
   */
  $LGD_CASNOTEURL				= $siteDomain.OD_PROGRAM_DIR."/shop.order.result_lgpay_m_casnoteurl.php";

  /*
   * LGD_RETURNURL 을 설정하여 주시기 바랍니다. 반드시 현재 페이지와 동일한 프로트콜 및  호스트이어야 합니다. 아래 부분을 반드시 수정하십시요.
   */
  $LGD_RETURNURL				= $siteDomain.OD_PROGRAM_DIR."/shop.order.result_lgpay_m_returnurl.php";

	/*
	* ISP 카드결제 연동을 위한 파라미터(필수) , 비동기 방식일 경우
	*/
	$LGD_KVPMISPWAPURL		= "";
	$LGD_KVPMISPCANCELURL   = "";
	$LGD_MPILOTTEAPPCARDWAPURL = ""; //iOS 연동시 필수 //

	/*
	* 계좌이체 연동을 위한 파라미터(필수)
	*/
	$LGD_MTRANSFERWAPURL 		= "";
	$LGD_MTRANSFERCANCELURL 	= "";


  /*
   *************************************************
   * 2. MD5 해쉬암호화 (수정하지 마세요) - BEGIN
   *
   * MD5 해쉬암호화는 거래 위변조를 막기위한 방법입니다.
   *************************************************
   *
   * 해쉬 암호화 적용( LGD_MID + LGD_OID + LGD_AMOUNT + LGD_TIMESTAMP + LGD_MERTKEY )
   * LGD_MID          : 상점아이디
   * LGD_OID          : 주문번호
   * LGD_AMOUNT       : 금액
   * LGD_TIMESTAMP    : 타임스탬프
   * LGD_MERTKEY      : 상점MertKey (mertkey는 상점관리자 -> 계약정보 -> 상점정보관리에서 확인하실수 있습니다)
   *
   * MD5 해쉬데이터 암호화 검증을 위해
   * LG유플러스에서 발급한 상점키(MertKey)를 환경설정 파일(lgdacom/conf/mall.conf)에 반드시 입력하여 주시기 바랍니다.
   */
  $xpay = &new XPayClient($configPath, $LGD_PLATFORM);
 	$xpay->Init_TX($LGD_MID);
  $LGD_HASHDATA = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_TIMESTAMP.$xpay->config[$LGD_MID]);
  $LGD_CUSTOM_PROCESSTYPE = "TWOTR";
  /*
   *************************************************
   * 2. MD5 해쉬암호화 (수정하지 마세요) - END
   *************************************************
   */
  $CST_WINDOW_TYPE = "submit";										// 수정불가
  $payReqMap['CST_PLATFORM']           = $CST_PLATFORM;				// 테스트, 서비스 구분
  $payReqMap['CST_WINDOW_TYPE']        = $CST_WINDOW_TYPE;			// 수정불가
  $payReqMap['CST_MID']                = $CST_MID;					// 상점아이디
  $payReqMap['LGD_MID']                = $LGD_MID;					// 상점아이디
  $payReqMap['LGD_OID']                = $LGD_OID;					// 주문번호
  $payReqMap['LGD_BUYER']              = $LGD_BUYER;            		// 구매자
  $payReqMap['LGD_PRODUCTINFO']        = $LGD_PRODUCTINFO;     		// 상품정보
  $payReqMap['LGD_AMOUNT']             = $LGD_AMOUNT;					// 결제금액
  $payReqMap['LGD_BUYEREMAIL']         = $LGD_BUYEREMAIL;				// 구매자 이메일
  $payReqMap['LGD_CUSTOM_SKIN']        = $LGD_CUSTOM_SKIN;			// 결제창 SKIN
  $payReqMap['LGD_CUSTOM_PROCESSTYPE'] = $LGD_CUSTOM_PROCESSTYPE;		// 트랜잭션 처리방식
  $payReqMap['LGD_TIMESTAMP']          = $LGD_TIMESTAMP;				// 타임스탬프
  $payReqMap['LGD_HASHDATA']           = $LGD_HASHDATA;				// MD5 해쉬암호값
  $payReqMap['LGD_RETURNURL']   		 = $LGD_RETURNURL;      		// 응답수신페이지
  $payReqMap['LGD_VERSION']         	 = "PHP_Non-ActiveX_SmartXPay";	// 버전정보 (삭제하지 마세요)
  $payReqMap['LGD_CUSTOM_FIRSTPAY']  	 = $LGD_CUSTOM_FIRSTPAY;		// 디폴트 결제수단
	$payReqMap['LGD_PCVIEWYN']			 = $LGD_PCVIEWYN;				// 휴대폰번호 입력 화면 사용 여부(유심칩이 없는 단말기에서 입력-->유심칩이 있는 휴대폰에서 실제 결제)
	$payReqMap['LGD_CUSTOM_SWITCHINGTYPE']  = "SUBMIT";					// 신용카드 카드사 인증 페이지 연동 방식

	$payReqMap['LGD_BUYERID'] 			= $LGD_BUYERID;  // 구매자 아이디 (상품권사용시)
	$payReqMap['LGD_BUYERIP'] 			= $LGD_BUYERIP;  // 구매자 아이피 (상품권사용시필요)
	$payReqMap['LGD_ENCODING'] 			= $LGD_ENCODING;  // 요청창 언어셋
	$payReqMap['LGD_ENCODING_RETURNURL'] 			= $LGD_ENCODING_RETURNURL;  // 결과창 언어셋
	$payReqMap['LGD_ESCROW_USEYN'] 			= $LGD_ESCROW_USEYN;  // 가상계좌


	//iOS 연동시 필수
	$payReqMap['LGD_MPILOTTEAPPCARDWAPURL'] = $LGD_MPILOTTEAPPCARDWAPURL;

	/*
	****************************************************
	* 신용카드 ISP(국민/BC)결제에만 적용 - BEGIN
	****************************************************
	*/
	$payReqMap['LGD_KVPMISPWAPURL']		 	= $LGD_KVPMISPWAPURL;
	$payReqMap['LGD_KVPMISPCANCELURL']  	= $LGD_KVPMISPCANCELURL;

	/*
	****************************************************
	* 신용카드 ISP(국민/BC)결제에만 적용  - END
	****************************************************
	*/

	/*
	****************************************************
	* 계좌이체 결제에만 적용 - BEGIN
	****************************************************
	*/
	$payReqMap['LGD_MTRANSFERWAPURL']		= $LGD_MTRANSFERWAPURL;
	$payReqMap['LGD_MTRANSFERCANCELURL']  	= $LGD_MTRANSFERCANCELURL;

	/*
	****************************************************
	* 계좌이체 결제에만 적용  - END
	****************************************************
	*/


	/*
	****************************************************
	* 모바일 OS별 ISP(국민/비씨), 계좌이체 결제 구분 값
	****************************************************
	- 안드로이드: A (디폴트)
	- iOS: N
	- iOS일 경우, 반드시 N으로 값을 수정
	*/
	$payReqMap['LGD_KVPMISPAUTOAPPYN']	= "A";		// 신용카드 결제
	$payReqMap['LGD_MTRANSFERAUTOAPPYN']= "A";		// 계좌이체 결제

    // 가상계좌(무통장) 결제연동을 하시는 경우  할당/입금 결과를 통보받기 위해 반드시 LGD_CASNOTEURL 정보를 LG 유플러스에 전송해야 합니다 .
    $payReqMap['LGD_CASNOTEURL'] = $LGD_CASNOTEURL;               // 가상계좌 NOTEURL

    //Return URL에서 인증 결과 수신 시 셋팅될 파라미터 입니다.*/
    $payReqMap['LGD_RESPCODE']           = "";
    $payReqMap['LGD_RESPMSG']            = "";
    $payReqMap['LGD_PAYKEY']             = "";

	// 2017-06-16 ::: 부가세율설정 ::: JJC
	if($app_vat_N > 0 ) {
		// 결제금액(LGD_AMOUNT) 중 면세금액
		//		과세상품과 면세상품을 같이 취급하는 고객사의 경우 신용카드 매출전표에 면세적용을 하기 위해서는 LG 유플러스와 별도 부분면세 계약이 되어야 합니다. (계약필수)
		//		면세처리를 하고자 하는 건은 LGD_TAXFREEAMOUNT 파라미터를 설정하시면 됩니다.
		//
		//			1) 구매하고자 하는 물품의 금액이 15,000 원이고 면세 받고자 하는 금액이 15,000 원일 경우
		//				LGD_TAXFREEAMOUNT 의 값을 15000 으로 설정
		//
		//			2) 단일주문번호로 여러 물품을 구매한 금액이 45,000 원이고 일부가 면세품목(면세총액 35,000 원)일 경우
		//				LGD_TAXFREEAMOUNT 의 값을 35000 으로 설정

		$payReqMap['LGD_TAXFREEAMOUNT']         = $app_vat_N;				// 구매자 이메일
	}
	// 2017-06-16 ::: 부가세율설정 ::: JJC

	// 일반 할부기간
	$arr_installment_peroid = array();
	$ex_installment_peroid = $siteInfo['s_pg_installment_peroid'] == '' ? array() : explode(",",$siteInfo['s_pg_installment_peroid']);
	if($siteInfo['s_pg_installment'] == 'N' || count($ex_installment_peroid) < 1){ // 일시불
		// 값 자체를 보내지 않는다.
	}else{
		$arr_installment_peroid[] = 0;
		foreach($ex_installment_peroid as $k=>$v){ $arr_installment_peroid[] = $v < 10 ? $v : $v; }
		$siteInfo['s_pg_installment_peroid'] = implode(":",$arr_installment_peroid);
		$payReqMap['LGD_INSTALLRANGE'] 			= $siteInfo['s_pg_installment_peroid'];  // 할부개월 수 있을경우 처리
	}


    $_SESSION['PAYREQ_MAP'] = $payReqMap;
?>
<script language="javascript" src="//xpay.uplus.co.kr/xpay/js/xpay_crossplatform.js" type="text/javascript"></script>
<script type="text/javascript">


	var LGD_window_type = '<?= $CST_WINDOW_TYPE ?>';
/*
* 수정불가
*/
function launchCrossPlatform(){
      lgdwin = open_paymentwindow(document.getElementById('LGD_PAYINFO'), '<?= $CST_PLATFORM ?>', LGD_window_type);
}
/*
* FORM 명만  수정 가능
*/
function getFormObject() {
        return document.getElementById("LGD_PAYINFO");
}

</script>

<form name=LGD_PAYINFO method=post id="LGD_PAYINFO" action="">
<?php
  foreach ($payReqMap as $key => $value) {
    echo "<input type='hidden' name='$key' id='$key' value='$value'>";
  }
?>
</form>


