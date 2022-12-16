<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

$r= $row; // 주문정보 동기화

$siteDomain = $system['url']; //가맹점 도메인 입력

// -- 배너 정보
	$banner_info = info_banner("mailing_logo",1,"data");

	// -- 환경설정 절대 디렉토리
	$configPath 				= PG_DIR."/lgpay/lgdacom"; 						//LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.

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
	$CST_PLATFORM               = $siteInfo['s_pg_mode'];                        //LG유플러스 결제 서비스 선택(test:테스트, service:서비스)
	$CST_MID                    = $siteInfo['s_pg_code'];                             //상점아이디(LG유플러스으로 부터 발급받으신 상점아이디를 입력하세요)
	                                                                           //테스트 아이디는 't'를 반드시 제외하고 입력하세요.
	$LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;   //상점아이디(자동생성)
	$LGD_OID                    = $ordernum;                             //주문번호(상점정의 유니크한 주문번호를 입력하세요)
	$LGD_AMOUNT                 = $r['o_price_real'];                          //결제금액("," 를 제외한 결제금액을 입력하세요)
	$LGD_BUYER                  = $r[o_oname];                           //구매자명
	$LGD_PRODUCTINFO            = $app_product_name;                     //상품명
	$LGD_BUYEREMAIL             = $r[o_oemail];                      //구매자 이메일
	$LGD_CUSTOM_LOGO			= $system['url']."/upfiles/banner/".$banner_info[0][b_img];
	$LGD_TIMESTAMP              = date(YmdHis);                                  //타임스탬프
	$LGD_OSTYPE_CHECK           = "P";                                           //값 P: XPay 실행(PC 결제 모듈): PC용과 모바일용 모듈은 파라미터 및 프로세스가 다르므로 PC용은 PC 웹브라우저에서 실행 필요.
																		 //"P", "M" 외의 문자(Null, "" 포함)는 모바일 또는 PC 여부를 체크하지 않음
	//$LGD_ACTIVEXYN			= "N";											 //계좌이체 결제시 사용, ActiveX 사용 여부로 "N" 이외의 값: ActiveX 환경에서 계좌이체 결제 진행(IE)

	$LGD_CUSTOM_SKIN            = "red";                                         //상점정의 결제창 스킨
	$LGD_CUSTOM_USABLEPAY       = $paymethod[$r['o_paymethod']];        	     //디폴트 결제수단 (해당 필드를 보내지 않으면 결제수단 선택 UI 가 노출됩니다.)
	$LGD_WINDOW_VER		        = "2.5";										 //결제창 버젼정보
	$LGD_WINDOW_TYPE            = 'iframe';					 //결제창 호출방식 (수정불가)
	$LGD_CUSTOM_SWITCHINGTYPE   = 'IFRAME';            //신용카드 카드사 인증 페이지 연동 방식 (수정불가)
	$LGD_CUSTOM_PROCESSTYPE     = "TWOTR";                                       //수정불가

	/*
	* 가상계좌(무통장) 결제 연동을 하시는 경우 아래 LGD_CASNOTEURL 을 설정하여 주시기 바랍니다.
	*/
	$LGD_CASNOTEURL				= $siteDomain.OD_PROGRAM_DIR."/shop.order.result_lgpay_new_casnoteurl.php";

	$LGD_ESCROW_USEYN			= 'Y'; // 에스크로 사용 여부

	/*
	* LGD_RETURNURL 을 설정하여 주시기 바랍니다. 반드시 현재 페이지와 동일한 프로트콜 및  호스트이어야 합니다. 아래 부분을 반드시 수정하십시요.
	* 2016-12-15 LCY :: return url 로 마지막 처리 페이지와는다르다. 마지막 처리결과페이지는 PRO  => pages/shop.order.result_lgpay_new.pro.php
	*/
	$LGD_RETURNURL				= $siteDomain.OD_PROGRAM_DIR."/shop.order.result_lgpay_new_returnurl.php";   // return url


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
	$LGD_MERTKEY				= $siteInfo['s_pg_key'];    //상점MertKey(mertkey는 상점관리자 -> 계약정보 -> 상점정보관리에서 확인하실수 있습니다)
	$LGD_BUYERID                = $r[o_mid];       //구매자 아이디
	$LGD_BUYERIP                = $_SERVER["REMOTE_ADDR"];       //구매자IP

	$LGD_ENCODING                = "UTF-8";       //UTF-8
	$LGD_ENCODING_RETURNURL                = "UTF-8";       //UTF-8

	$xpay = &new XPayClient($configPath, $CST_PLATFORM);  // -- 파일은 shop.order.result.php 파일 상단에서 인클루드 된다.
	$xpay->Init_TX($LGD_MID);
	$LGD_HASHDATA = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_TIMESTAMP.$xpay->config[$LGD_MID]);

	/*
	*************************************************
	* 2. MD5 해쉬암호화 (수정하지 마세요) - END
	*************************************************
	*/

	$payReqMap['CST_PLATFORM']           = $CST_PLATFORM;				// 테스트, 서비스 구분
	$payReqMap['LGD_WINDOW_TYPE']        = $LGD_WINDOW_TYPE;			// 수정불가
	$payReqMap['CST_MID']                = $CST_MID;					// 상점아이디
	$payReqMap['LGD_MID']                = $LGD_MID;					// 상점아이디
	$payReqMap['LGD_OID']                = $LGD_OID;					// 주문번호
	$payReqMap['LGD_BUYER']              = $LGD_BUYER;					// 구매자
	$payReqMap['LGD_PRODUCTINFO']        = $LGD_PRODUCTINFO;			// 상품정보
	$payReqMap['LGD_AMOUNT']             = $LGD_AMOUNT;					// 결제금액


	$payReqMap['LGD_BUYEREMAIL']         = $LGD_BUYEREMAIL;				// 구매자 이메일
	$payReqMap['LGD_CUSTOM_SKIN']        = $LGD_CUSTOM_SKIN;			// 결제창 SKIN
	$payReqMap['LGD_CUSTOM_PROCESSTYPE'] = $LGD_CUSTOM_PROCESSTYPE;		// 트랜잭션 처리방식
	$payReqMap['LGD_TIMESTAMP']          = $LGD_TIMESTAMP;				// 타임스탬프
	$payReqMap['LGD_HASHDATA']           = $LGD_HASHDATA;				// MD5 해쉬암호값
	$payReqMap['LGD_RETURNURL']   		 = $LGD_RETURNURL;				// 응답수신페이지
	$payReqMap['LGD_VERSION']         	 = "PHP_Non-ActiveX_Standard";	// 버전정보 (삭제하지 마세요)
	$payReqMap['LGD_CUSTOM_USABLEPAY']  	= $LGD_CUSTOM_USABLEPAY;	// 디폴트 결제수단
	$payReqMap['LGD_CUSTOM_SWITCHINGTYPE']  = $LGD_CUSTOM_SWITCHINGTYPE;// 신용카드 카드사 인증 페이지 연동 방식
	$payReqMap['LGD_OSTYPE_CHECK']          = $LGD_OSTYPE_CHECK;        // 값 P: XPay 실행(PC용 결제 모듈), PC, 모바일 에서 선택적으로 결제가능
	//$payReqMap['LGD_ACTIVEXYN']			= $LGD_ACTIVEXYN;			// 계좌이체 결제시 사용,ActiveX 사용 여부
	$payReqMap['LGD_WINDOW_VER'] 			= $LGD_WINDOW_VER;
	$payReqMap['LGD_ESCROW_USEYN'] 			= $LGD_ESCROW_USEYN;  // 에스크로 여부

	$payReqMap['LGD_BUYERID'] 			= $LGD_BUYERID;  // 구매자 아이디 (상품권사용시)
	$payReqMap['LGD_BUYERIP'] 			= $LGD_BUYERIP;  // 구매자 아이피 (상품권사용시필요)
	$payReqMap['LGD_ENCODING'] 			= $LGD_ENCODING;  // 요청창 언어셋
	$payReqMap['LGD_ENCODING_RETURNURL'] 			= $LGD_ENCODING_RETURNURL;  // 결과창 언어셋


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

/*
* 수정불가.
*/
	var LGD_window_type = '<?= $LGD_WINDOW_TYPE ?>';

/*
* 수정불가
*/
function launchCrossPlatform(){
	lgdwin = openXpay(document.getElementById('LGD_PAYINFO'), '<?= $CST_PLATFORM ?>', LGD_window_type, null, "", "");
}
/*
* FORM 명만  수정 가능
*/
function getFormObject() {
        return document.getElementById("LGD_PAYINFO");
}

/*
 * 인증결과 처리
 */
function payment_return() {
	var fDoc;

		fDoc = lgdwin.contentWindow || lgdwin.contentDocument;


	if (fDoc.document.getElementById('LGD_RESPCODE').value == "0000") {

			document.getElementById("LGD_PAYKEY").value = fDoc.document.getElementById('LGD_PAYKEY').value;
			document.getElementById("LGD_PAYINFO").target = "common_frame";
			document.getElementById("LGD_PAYINFO").action = "<?php echo $siteDomain.OD_PROGRAM_DIR; ?>/shop.order.result_lgpay_new.pro.php";
			document.getElementById("LGD_PAYINFO").submit();
	} else {
		alert("LGD_RESPCODE (결과코드) : " + fDoc.document.getElementById('LGD_RESPCODE').value + "\n" + "LGD_RESPMSG (결과메시지): " + fDoc.document.getElementById('LGD_RESPMSG').value);
		closeIframe();
	}
}

</script>

<form method="post" name="LGD_PAYINFO" id="LGD_PAYINFO" action="<?php echo $siteDomain.OD_PROGRAM_DIR; ?>/shop.order.result_lgpay_new.pro.php">
<?php

foreach ($payReqMap as $key => $value) {
	echo "<input type='hidden' name='".$key."' id='".$key."' value='".$value."'>";
}
?>
</form>
<?php actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행 ?>