<?PHP
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
    $CST_PLATFORM               = "test";// LG유플러스 결제 서비스 선택(test:테스트, service:서비스)
    $CST_MID                    = "ssnumer";// 상점아이디(LG유플러스으로 부터 발급받으신 상점아이디를 입력하세요)
                                                                        //테스트 아이디는 't'를 반드시 제외하고 입력하세요.
    $LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;  //상점아이디(자동생성)
    $LGD_OID                    = $_POST["LGD_OID"];           //주문번호(상점정의 유니크한 주문번호를 입력하세요)
    $LGD_AMOUNT                 = $_POST["LGD_AMOUNT"];        //결제금액("," 를 제외한 결제금액을 입력하세요)
    $LGD_BUYER                  = $_POST["LGD_BUYER"];         //구매자명
    $LGD_PRODUCTINFO            = $_POST["LGD_PRODUCTINFO"];   //상품명
    $LGD_BUYEREMAIL             = $_POST["LGD_BUYEREMAIL"];    //구매자 이메일
    $LGD_TIMESTAMP              = date(YmdHms);                         //타임스탬프
    $LGD_CUSTOM_SKIN            = "blue";                               //상점정의 결제창 스킨 (red, blue, cyan, green, yellow)
	$LGD_CUSTOM_USABLEPAY = "SC0010,SC0030,SC0040";		// 신용카드, 계좌이체만 적용
	/*
	신용카드 - SC0010
	계좌이체 -SC0030
	가상계좌-SC0040
	핸드폰-SC0060
	*/
    $LGD_MERTKEY				= "0dfe9db32f088a1ab7c773dfc949a1e1";    //상점MertKey(mertkey는 상점관리자 -> 계약정보 -> 상점정보관리에서 확인하실수 있습니다)
	$configPath 				= $_SERVER["DOCUMENT_ROOT"] . "/shop/lgpay/lgdacom"; 						//LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.
    $LGD_BUYERID                = $_POST["LGD_BUYERID"];       //구매자 아이디
    $LGD_BUYERIP                = $_POST["LGD_BUYERIP"];       //구매자IP

    /*
     * 가상계좌(무통장) 결제 연동을 하시는 경우 아래 LGD_CASNOTEURL 을 설정하여 주시기 바랍니다. 
     */    
    $LGD_CASNOTEURL				= "http://".$_SERVER[HTTP_HOST]."/shop/lgpay/cas_noteurl.php";    
		
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
    require_once("./lgdacom/XPayClient.php");
    $xpay = &new XPayClient($configPath, $LGD_PLATFORM);
   	$xpay->Init_TX($LGD_MID);
    $LGD_HASHDATA = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_TIMESTAMP.$xpay->config[$LGD_MID]);
    $LGD_CUSTOM_PROCESSTYPE = "TWOTR";
    /*
     *************************************************
     * 2. MD5 해쉬암호화 (수정하지 마세요) - END
     *************************************************
     */
?>


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
<meta http-equiv="Pragma" content="no-cache"/>
<title>LG U+ eCredit서비스 결제테스트</title>
<script language="javascript" src="//xpay.uplus.co.kr/xpay/js/xpay_install.js" type="text/javascript"></script>
<script type="text/javascript">
<!--
/*
 * 상점결제 인증요청후 PAYKEY를 받아서 최종결제 요청.
 */
function doPay_ActiveX(){
    ret = xpay_check(document.getElementById('LGD_PAYINFO'), '<?= $CST_PLATFORM ?>');

    if (ret=="00"){     //ActiveX 로딩 성공
        var LGD_RESPCODE        = dpop.getData('LGD_RESPCODE');       //결과코드
        var LGD_RESPMSG         = dpop.getData('LGD_RESPMSG');        //결과메세지

        if( "0000" == LGD_RESPCODE ) { //인증성공
            var LGD_PAYKEY      = dpop.getData('LGD_PAYKEY');         //LG유플러스 인증KEY
            var msg = "인증결과 : " + LGD_RESPMSG + "\n";
            msg += "LGD_PAYKEY : " + LGD_PAYKEY +"\n\n";
            document.getElementById('LGD_PAYKEY').value = LGD_PAYKEY;
            alert(msg);
            document.getElementById('LGD_PAYINFO').submit();
        } else { //인증실패
            alert("인증이 실패하였습니다. " + LGD_RESPMSG);
            /*
             * 인증실패 화면 처리
             */
        }
    } else {
        alert("LG U+ 전자결제를 위한 플러그인 모듈이 설치되지 않았습니다.");
        /*
         * 인증실패 화면 처리
         */
    }
}

function isPluginOK(){
	if(hasXpayObject()) {
		//alert('LG U+ 전자결제를 위한 XPayClient (Plugin) 이 설치되었습니다. ');
	}else {
		//xpayShowInstall();
	}
}
//-->
</script>
</head>

<body onload='javascript:isPluginOK();'>
<form method="post" id="LGD_PAYINFO" action="payres.php">
<table>
    <tr>
        <td>구매자 이름 </td>
        <td><?= $LGD_BUYER ?></td>
    </tr>
    <tr>
        <td>구매자 IP </td>
        <td><?= $LGD_BUYERIP ?></td>
    </tr>
    <tr>
        <td>구매자 ID </td>
        <td><?= $LGD_BUYERID ?></td>
    </tr>
    <tr>
        <td>상품정보 </td>
        <td><?= $LGD_PRODUCTINFO ?></td>
    </tr>
    <tr>
        <td>결제금액 </td>
        <td><?= $LGD_AMOUNT ?></td>
    </tr>
    <tr>
        <td>구매자 이메일 </td>
        <td><?= $LGD_BUYEREMAIL ?></td>
    </tr>
    <tr>
        <td>주문번호 </td>
        <td><?= $LGD_OID ?></td>
    </tr>
    <tr>
        <td colspan="2">* 추가 상세 결제요청 파라미터는 메뉴얼을 참조하시기 바랍니다.</td>
    </tr>
    <tr>
        <td colspan="2"></td>
    </tr>    
    <tr>
        <td colspan="2">
		<input type="button" value="인증요청" onclick="doPay_ActiveX();"/>         
        </td>
    </tr>    
</table>
<br>

<br>
<input type="hidden" name="CST_PLATFORM"                value="<?= $CST_PLATFORM ?>">                   <!-- 테스트, 서비스 구분 -->
<input type="hidden" name="CST_MID"                     value="<?= $CST_MID ?>">                        <!-- 상점아이디 -->
<input type="hidden" name="LGD_MID"                     value="<?= $LGD_MID ?>">                        <!-- 상점아이디 -->
<input type="hidden" name="LGD_OID"                     value="<?= $LGD_OID ?>">                        <!-- 주문번호 -->
<input type="hidden" name="LGD_BUYER"                   value="<?= $LGD_BUYER ?>">           			<!-- 구매자 -->
<input type="hidden" name="LGD_PRODUCTINFO"             value="<?= $LGD_PRODUCTINFO ?>">     			<!-- 상품정보 -->
<input type="hidden" name="LGD_AMOUNT"                  value="<?= $LGD_AMOUNT ?>">                     <!-- 결제금액 -->
<input type="hidden" name="LGD_BUYEREMAIL"              value="<?= $LGD_BUYEREMAIL ?>">                 <!-- 구매자 이메일 -->
<input type="hidden" name="LGD_CUSTOM_SKIN"             value="<?= $LGD_CUSTOM_SKIN ?>">                <!-- 결제창 SKIN -->
<input type="hidden" name="LGD_CUSTOM_USABLEPAY"        value="<?= $LGD_CUSTOM_USABLEPAY ?>"> <!-- 신용카드, 계좌이체만 사용 -->
<input type="hidden" name="LGD_CUSTOM_PROCESSTYPE"      value="<?= $LGD_CUSTOM_PROCESSTYPE ?>">         <!-- 트랜잭션 처리방식 -->
<input type="hidden" name="LGD_TIMESTAMP"               value="<?= $LGD_TIMESTAMP ?>">                  <!-- 타임스탬프 -->
<input type="hidden" name="LGD_HASHDATA"                value="<?= $LGD_HASHDATA ?>">                   <!-- MD5 해쉬암호값 -->
<input type="hidden" name="LGD_PAYKEY"                  id="LGD_PAYKEY">                                <!-- LG유플러스 PAYKEY(인증후 자동셋팅)-->
<input type="hidden" name="LGD_VERSION"         		value="PHP_XPay_1.0">							<!-- 버전정보 (삭제하지 마세요) -->
<input type="hidden" name="LGD_BUYERIP"                 value="<?= $LGD_BUYERIP ?>">           			<!-- 구매자IP -->
<input type="hidden" name="LGD_BUYERID"                 value="<?= $LGD_BUYERID ?>">           			<!-- 구매자ID -->
<input type="hidden" name="LGD_ENCODING"                value="UTF-8"> 
<input type="hidden" name="LGD_ENCODING_NOTEURL"        value="UTF-8"> 
<input type="hidden" name="LGD_ENCODING_RETURNURL"      value="UTF-8"> 
<!-- 가상계좌(무통장) 결제연동을 하시는 경우  할당/입금 결과를 통보받기 위해 반드시 LGD_CASNOTEURL 정보를 LG 유플러스에 전송해야 합니다 . -->
<!-- input type="hidden" name="LGD_CASNOTEURL"          	value="<?= $LGD_CASNOTEURL ?>"-->					<!-- 가상계좌 NOTEURL -->  

</form>
</body>
<!--  xpay.js는 반드시 body 밑에 두시기 바랍니다. -->
<!--  UTF-8 인코딩 사용 시는 xpay_ub.js 대신 xpay_ub_utf-8.js 을  호출하시기 바랍니다.-->
<script language="javascript" src="<?= $_SERVER['SERVER_PORT']!=443?"http":"https" ?>://xpay.uplus.co.kr<?=($CST_PLATFORM == "test")?($_SERVER['SERVER_PORT']!=443?":7080":":7443"):""?>/xpay/js/xpay_ub_utf-8.js" type="text/javascript" charset="utf-8"></script>
</html>