<?

include_once $_SERVER["DOCUMENT_ROOT"]."/m/common/COrder.php";
include_once $dir_home."/common/COrder.php";
include_once $dir_pg."/A/header.php";

##비로그인 상태일경우 로그인화면으로 이동
if(!$my->islogin()) {
	$my->msgbox("잘못된 접근입니다.");
	exit;
}

##파라미터
$ordernum = $_POST['ordernum'];		//주문번호
$orderid = $_POST['orderid'];		//주문자id
$mobile = $_POST['mobile'];		//모바일여부 Y/N

##주문번호의 주문장조회
$Rorder = $my->getRow("select tPrice, authum, apprTm, dealNo, subTy, paystatus, orderstep, paymethod, delivstatus from ".$pub_slntype."Order where ordernum='".$ordernum."' and paystatus = 'Y'");
$COR = new COrderRow($Rorder);
if(!$Rorder) {
	$my->msgbox("잘못된 접근입니다.");
	exit;
}

##취소용데이터확인
$tPrice = $Rorder['tPrice']; //배송비
$authum = $Rorder['authum']; //쿠폰할인금
$apprTm = $Rorder['apprTm'];   //포인트할인금
$dealNo = $Rorder['dealNo']; //최종할인금(쿠폰+포인트)
$subTy = $Rorder['subTy']; //최종결제금
$paymethod = $Rorder['paymethod']; //받을포인트


if(!$authum || !$apprTm || !$dealNo || !$subTy) {
	$my->msgbox("취소에 필요한 데이터가 충분치 않습니다.");
	exit;
}



    /****************************************************************************
	*
	* [1] 라이브러리(AGSLib.php)를 인클루드 합니다.
	*
	****************************************************************************/
	require ("./lib/AGSLib.php");
	
	/****************************************************************************
	*
	* [2]. agspay4.0 클래스의 인스턴스를 생성합니다.
	*
	****************************************************************************/
	$agspay = new agspay40;


	/****************************************************************************
	*
	* [3] AGS_pay.html 로 부터 넘겨받을 데이타
	*
	****************************************************************************/

	/*공통사용*/
	//$agspay->SetValue("AgsPayHome","C:/htdocs/agspay");			//올더게이트 결제설치 디렉토리 (상점에 맞게 수정)
    $agspay->SetValue("AgsPayHome",$_SERVER[DOCUMENT_ROOT].$path_home."/pages/order/pgscript/A");			//올더게이트 결제설치 디렉토리 (상점에 맞게 수정)

	$agspay->SetValue("log","true");							//true : 로그기록, false : 로그기록안함.
	$agspay->SetValue("logLevel","ERROR");						//로그레벨 : DEBUG, INFO, WARN, ERROR, FATAL (해당 레벨이상의 로그만 기록됨)
	$agspay->SetValue("Type", "Cancel");						//고정값(수정불가)
	$agspay->SetValue("RecvLen", 7);							//수신 데이터(길이) 체크 에러시 6 또는 7 설정. 

if($mobile == "Y")  $agspay->SetValue("StoreId",trim($pub_pgid));		        //상점아이디
else                $agspay->SetValue("StoreId",trim($pub_setup['P_ID']));		        //상점아이디
	$agspay->SetValue("AuthTy","card");			                //결제형태(card..에고...authty를 저장안해놨다.. 그래서 쓸수없음 카드로고정함)
	$agspay->SetValue("SubTy",trim($subTy));			        //서브결제형태
	$agspay->SetValue("rApprNo",trim($authum));		            //승인번호
	$agspay->SetValue("rApprTm",trim($apprTm));		            //승인일자
	$agspay->SetValue("rDealNo",trim($dealNo));		            //거래번호
	

	/****************************************************************************
	*
	* [4] 올더게이트 결제서버로 결제를 요청합니다.
	*
	****************************************************************************/
	echo ($agspay->startPay());

	/****************************************************************************
	*
	* [5] 취소요청결과에 따른 상점DB 저장 및 기타 필요한 처리작업을 수행하는 부분입니다.
	*
	* 신용카드결제 취소결과가 정상적으로 수신되었으므로 DB 작업을 할 경우 
	* 결과페이지로 데이터를 전송하기 전 이부분에서 하면된다.
	*
	* 여기서 DB 작업을 해 주세요.
	* 취소성공여부 : $agspay->GetResult("rCancelSuccYn") (성공:y 실패:n)
	* 취소결과메시지 : $agspay->GetResult("rCancelResMsg")
	*
	****************************************************************************/		
		
	if($agspay->GetResult("rCancelSuccYn") == "y")
	{ 
		// 결제취소에 따른 처리부분
		//echo ("신용카드 승인취소가 성공처리되었습니다. [" . $agspay->GetResult("rCancelSuccYn")."]". $agspay->GetResult("rCancelResMsg").". " );

?>
            <!--취소처리폼에 데이터전송-->
            <form name=frmAGS_pay_ing method=post action="<?=$path_home2?>/pages/order/cancel_complete.php">
                <!-- complete용 주문변수 -->
                <input type=hidden name=ordernum value="<?=$ordernum?>">		<!-- 주문번호 -->
                <input type=hidden name=orderid value="<?=$orderid?>">		<!-- 주문자id -->  
                <input type=hidden name=mobile value="<?=$mobile?>">		<!-- 모바일여부 -->  
            </form>
            <script>
                frmAGS_pay_ing.submit();
            </script>
<?

	}
	else
	{
		// 결제실패에 따른 상점처리부분
        $errmsg = iconv("euc-kr","utf-8",$agspay->GetResult("rCancelResMsg"));     //실패사유
		$my->msgbox("취소실패!!! ".(trim(reset(explode("-",$pub_company[tel]))) ? $pub_company[tel] : substr($pub_company[tel],1,10))." 로 전화주시거나 고객문의에 아래의 오류내용을 첨부하여 결제취소요청을 해주시면 처리해드리겠습니다. 오류내용:".$errmsg);
        $my->parent_reload();   //부모창갱신
	}



?>