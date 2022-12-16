<?php
/********************************************************************************
*
* 프로젝트 : AGSMobile V1.0
* (※ 본 프로젝트는 아이폰 및 안드로이드에서 이용하실 수 있으며 일반 웹페이지에서는 결제가 불가합니다.)
*
* 파일명 : AGS_pay_ing.php
* 최종수정일자 : 2010/10/6
*
* 올더게이트 결제창에서 리턴된 데이터를 받아서 소켓결제요청을 합니다.
*
* Copyright AEGIS ENTERPRISE.Co.,Ltd. All rights reserved.
*
*
*  ※ 유의사항 ※
*  1.  "|"(파이프) 값은 결제처리 중 구분자로 사용하는 문자이므로 결제 데이터에 "|"이 있을경우
*   결제가 정상적으로 처리되지 않습니다.(수신 데이터 길이 에러 등의 사유)
********************************************************************************/
	
	
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
	$agspay->SetValue("AgsPayHome",$_SERVER[DOCUMENT_ROOT]."/m/pages/order/pgscript/A");			//올더게이트 결제설치 디렉토리 (상점에 맞게 수정)
	$agspay->SetValue("StoreId",trim($_POST["StoreId"]));		//상점아이디
	$agspay->SetValue("log","true");							//true : 로그기록, false : 로그기록안함.
	$agspay->SetValue("logLevel","INFO");						//로그레벨 : DEBUG, INFO, WARN, ERROR, FATAL (해당 레벨이상의 로그만 기록됨)
	$agspay->SetValue("UseNetCancel","true");					//true : 망취소 사용. false: 망취소 미사용
	$agspay->SetValue("Type", "Pay");							//고정값(수정불가)
	$agspay->SetValue("RecvLen", 7);							//수신 데이터(길이) 체크 에러시 6 또는 7 설정. 
	
	$agspay->SetValue("AuthTy",trim($_POST["AuthTy"]));			//결제형태
	$agspay->SetValue("SubTy",trim($_POST["SubTy"]));			//서브결제형태
	$agspay->SetValue("OrdNo",trim($_POST["OrdNo"]));			//주문번호
	$agspay->SetValue("Amt",trim($_POST["Amt"]));				//금액
	$agspay->SetValue("UserEmail",trim($_POST["UserEmail"]));	//주문자이메일
	$agspay->SetValue("ProdNm",trim($_POST["ProdNm"]));			//상품명

	/*신용카드&가상계좌사용*/
	$agspay->SetValue("MallUrl",trim($_POST["MallUrl"]));		//MallUrl(무통장입금) - 상점 도메인 가상계좌추가
	$agspay->SetValue("UserId",trim($_POST["UserId"]));			//회원아이디


	/*신용카드사용*/
	$agspay->SetValue("OrdNm",trim($_POST["OrdNm"]));			//주문자명
	$agspay->SetValue("OrdPhone",trim($_POST["OrdPhone"]));		//주문자연락처
	$agspay->SetValue("OrdAddr",trim($_POST["OrdAddr"]));		//주문자주소 가상계좌추가
	$agspay->SetValue("RcpNm",trim($_POST["RcpNm"]));			//수신자명
	$agspay->SetValue("RcpPhone",trim($_POST["RcpPhone"]));		//수신자연락처
	$agspay->SetValue("DlvAddr",trim($_POST["DlvAddr"]));		//배송지주소
	$agspay->SetValue("Remark",trim($_POST["Remark"]));			//비고
	$agspay->SetValue("DeviId",trim($_POST["DeviId"]));			//단말기아이디
	$agspay->SetValue("AuthYn",trim($_POST["AuthYn"]));			//인증여부
	$agspay->SetValue("Instmt",trim($_POST["Instmt"]));			//할부개월수
	$agspay->SetValue("UserIp",$_SERVER["REMOTE_ADDR"]);		//회원 IP

	/*신용카드(ISP)*/
	$agspay->SetValue("partial_mm",trim($_POST["partial_mm"]));		//일반할부기간
	$agspay->SetValue("noIntMonth",trim($_POST["noIntMonth"]));		//무이자할부기간
	$agspay->SetValue("KVP_CURRENCY",trim($_POST["KVP_CURRENCY"]));	//KVP_통화코드
	$agspay->SetValue("KVP_CARDCODE",trim($_POST["KVP_CARDCODE"]));	//KVP_카드사코드
	$agspay->SetValue("KVP_SESSIONKEY",$_POST["KVP_SESSIONKEY"]);	//KVP_SESSIONKEY
	$agspay->SetValue("KVP_ENCDATA",$_POST["KVP_ENCDATA"]);			//KVP_ENCDATA
	$agspay->SetValue("KVP_CONAME",trim($_POST["KVP_CONAME"]));		//KVP_카드명
	$agspay->SetValue("KVP_NOINT",trim($_POST["KVP_NOINT"]));		//KVP_무이자=1 일반=0
	$agspay->SetValue("KVP_QUOTA",trim($_POST["KVP_QUOTA"]));		//KVP_할부개월

	/*신용카드(안심)*/
	$agspay->SetValue("CardNo",trim($_POST["CardNo"]));			//카드번호
	$agspay->SetValue("MPI_CAVV",$_POST["MPI_CAVV"]);			//MPI_CAVV
	$agspay->SetValue("MPI_ECI",$_POST["MPI_ECI"]);				//MPI_ECI
	$agspay->SetValue("MPI_MD64",$_POST["MPI_MD64"]);			//MPI_MD64

	/*신용카드(일반)*/
	$agspay->SetValue("ExpMon",trim($_POST["ExpMon"]));				//유효기간(월)
	$agspay->SetValue("ExpYear",trim($_POST["ExpYear"]));			//유효기간(년)
	$agspay->SetValue("Passwd",trim($_POST["Passwd"]));				//비밀번호
	$agspay->SetValue("SocId",trim($_POST["SocId"]));				//주민등록번호/사업자등록번호

	/*핸드폰사용*/
	$agspay->SetValue("HP_SERVERINFO",trim($_POST["HP_SERVERINFO"]));	//SERVER_INFO(핸드폰결제)
	$agspay->SetValue("HP_HANDPHONE",trim($_POST["HP_HANDPHONE"]));		//HANDPHONE(핸드폰결제)
	$agspay->SetValue("HP_COMPANY",trim($_POST["HP_COMPANY"]));			//COMPANY(핸드폰결제)
	$agspay->SetValue("HP_ID",trim($_POST["HP_ID"]));					//HP_ID(핸드폰결제)
	$agspay->SetValue("HP_SUBID",trim($_POST["HP_SUBID"]));				//HP_SUBID(핸드폰결제)
	$agspay->SetValue("HP_UNITType",trim($_POST["HP_UNITType"]));		//HP_UNITType(핸드폰결제)
	$agspay->SetValue("HP_IDEN",trim($_POST["HP_IDEN"]));				//HP_IDEN(핸드폰결제)
	$agspay->SetValue("HP_IPADDR",trim($_POST["HP_IPADDR"]));			//HP_IPADDR(핸드폰결제)

	/*가상계좌사용*/
	$agspay->SetValue("VIRTUAL_CENTERCD",trim($_POST["VIRTUAL_CENTERCD"]));	//은행코드(가상계좌)
	$agspay->SetValue("VIRTUAL_DEPODT",trim($_POST["VIRTUAL_DEPODT"]));		//입금예정일(가상계좌)
	$agspay->SetValue("ZuminCode",trim($_POST["ZuminCode"]));				//주민번호(가상계좌)
	$agspay->SetValue("MallPage",trim($_POST["MallPage"]));					//상점 입/출금 통보 페이지(가상계좌)
	$agspay->SetValue("VIRTUAL_NO",trim($_POST["VIRTUAL_NO"]));				//가상계좌번호(가상계좌)

	/*에스크로사용*/
	$agspay->SetValue("ES_SENDNO",trim($_POST["ES_SENDNO"]));				//에스크로전문번호

	/*추가사용필드*/
	$agspay->SetValue("Column1", trim($_POST["Column1"]));						//추가사용필드1   
	$agspay->SetValue("Column2", trim($_POST["Column2"]));						//추가사용필드2
	$agspay->SetValue("Column3", trim($_POST["Column3"]));						//추가사용필드3
	
	/****************************************************************************
	*
	* [4] 올더게이트 결제서버로 결제를 요청합니다.
	*
	****************************************************************************/
	$agspay->startPay();

	
	/****************************************************************************
	*
	* [5] 결제결과에 따른 상점DB 저장 및 기타 필요한 처리작업을 수행하는 부분입니다.
	*
	*	아래의 결과값들을 통하여 각 결제수단별 결제결과값을 사용하실 수 있습니다.
	*	
	*	-- 공통사용 --
	*	업체ID : $agspay->GetResult("rStoreId")
	*	주문번호 : $agspay->GetResult("rOrdNo")
	*	상품명 : $agspay->GetResult("rProdNm")
	*	거래금액 : $agspay->GetResult("rAmt")
	*	성공여부 : $agspay->GetResult("rSuccYn") (성공:y 실패:n)
	*	결과메시지 : $agspay->GetResult("rResMsg")
	*
	*	1. 신용카드
	*	
	*	전문코드 : $agspay->GetResult("rBusiCd")
	*	거래번호 : $agspay->GetResult("rDealNo")
	*	승인번호 : $agspay->GetResult("rApprNo")
	*	할부개월 : $agspay->GetResult("rInstmt")
	*	승인시각 : $agspay->GetResult("rApprTm")
	*	카드사코드 : $agspay->GetResult("rCardCd")
	*
	*
	*	2.가상계좌
	*	가상계좌의 결제성공은 가상계좌발급의 성공만을 의미하며 입금대기상태로 실제 고객이 입금을 완료한 것은 아닙니다.
	*	따라서 가상계좌 결제완료시 결제완료로 처리하여 상품을 배송하시면 안됩니다.
	*	결제후 고객이 발급받은 계좌로 입금이 완료되면 MallPage(상점 입금통보 페이지(가상계좌))로 입금결과가 전송되며
	*	이때 비로소 결제가 완료되게 되므로 결제완료에 대한 처리(배송요청 등)은  MallPage에 작업해주셔야 합니다.
	*	결제종류 : $agspay->GetResult("rAuthTy") (가상계좌 일반 : vir_n 유클릭 : vir_u 에스크로 : vir_s)
	*	승인일자 : $agspay->GetResult("rApprTm")
	*	가상계좌번호 : $agspay->GetResult("rVirNo")
	*
	*	3.핸드폰결제
	*	핸드폰결제일 : $agspay->GetResult("rHP_DATE")
	*	핸드폰결제 TID : $agspay->GetResult("rHP_TID")
	*
	****************************************************************************/
    include $_SERVER["DOCUMENT_ROOT"]."/m/common/common.php";

	if($agspay->GetResult("rSuccYn") == "y")
	{ 
		if($agspay->GetResult("AuthTy") == "virtual"){
			//가상계좌결제의 경우 입금이 완료되지 않은 입금대기상태(가상계좌 발급성공)이므로 상품을 배송하시면 안됩니다. 

		}else{
			// 결제성공에 따른 상점처리부분
			//echo ("결제가 성공처리되었습니다. [" . $agspay->GetResult("rSuccYn")."]". $agspay->GetResult("rResMsg").". " );
            //성공시해야할일은 complete 에서 정의되어있으므로 compleete 에 필요한 파라미터를 정의해주고 complete를 호출한다.
            //complete에서 사용할 변수를 이곳에서 미리 정의한후에 넘겨준다.

            ##주문정보의 결제정보를 넘겨준다.
            $authum			= $agspay->GetResult('rApprNo');    //승인번호
            $ordernum       = $agspay->GetResult('rOrdNo');           //주문번호
            $tPriceResult	= $agspay->GetResult('rAmt');   //결제금액
            $apprTm         = $agspay->GetResult('rApprTm');    //결제시간
            $dealNo         = $agspay->GetResult('rDealNo');    //신용카드공통거래번호
            $subTy          = $agspay->GetResult("SubTy");      //서브결제형태

            # 결재완료 (최종데이터의 업데이트는 complete.php가수행함)
			$comp_qry = "update ".$pub_slntype."Order set orderstep='ing', 
                        paydate = now(), 
                        paystatus = 'Y',
                        authum = '".$authum."', 
                        apprTm = '".$apprTm."', 
                        dealNo = '".$dealNo."', 
                        subTy = '".$subTy."' 
                        where ordernum ='".$ordernum."'";
            $my->exec($comp_qry);   //주문정보업데이트
?>

            <form name=frmAGS_pay_ing method=post action="/m/pages/order/complete.php">

            <!-- 각 결제 공통 사용 변수 -->
            <input type=hidden name=AuthTy value="<?=$agspay->GetResult("AuthTy")?>">		<!-- 결제형태 -->
            <input type=hidden name=SubTy value="<?=$agspay->GetResult("SubTy")?>">			<!-- 서브결제형태 -->
            <input type=hidden name=rStoreId value="<?=$agspay->GetResult("rStoreId")?>">		<!-- 상점아이디 -->
            <input type=hidden name=rOrdNo value="<?=$agspay->GetResult("rOrdNo")?>">		<!-- 주문번호 -->
            <input type=hidden name=rProdNm value="<?=$agspay->GetResult("ProdNm")?>">		<!-- 상품명 -->
            <input type=hidden name=rAmt value="<?=$agspay->GetResult("rAmt")?>">				<!-- 결제금액 -->
            <input type=hidden name=rOrdNm value="<?=$agspay->GetResult("OrdNm")?>">		<!-- 주문자명 -->

            <input type=hidden name=rSuccYn value="<?=$agspay->GetResult("rSuccYn")?>">	<!-- 성공여부 -->
            <input type=hidden name=rResMsg value="<?=$agspay->GetResult("rResMsg")?>">	<!-- 결과메시지 -->
            <input type=hidden name=rApprTm value="<?=$agspay->GetResult("rApprTm")?>">	<!-- 결제시간 -->

            <!-- 신용카드 결제 사용 변수 -->
            <input type=hidden name=rBusiCd value="<?=$agspay->GetResult("rBusiCd")?>">		<!-- (신용카드공통)전문코드 -->
            <input type=hidden name=rApprNo value="<?=$agspay->GetResult("rApprNo")?>">		<!-- (신용카드공통)승인번호 -->
            <input type=hidden name=rCardCd value="<?=$agspay->GetResult("rCardCd")?>">	<!-- (신용카드공통)카드사코드 -->
            <input type=hidden name=rDealNo value="<?=$agspay->GetResult("rDealNo")?>">			<!-- (신용카드공통)거래번호 -->

            <input type=hidden name=rCardNm value="<?=$agspay->GetResult("rCardNm")?>">	<!-- (안심클릭,일반사용)카드사명 -->
            <input type=hidden name=rMembNo value="<?=$agspay->GetResult("rMembNo")?>">	<!-- (안심클릭,일반사용)가맹점번호 -->
            <input type=hidden name=rAquiCd value="<?=$agspay->GetResult("rAquiCd")?>">		<!-- (안심클릭,일반사용)매입사코드 -->
            <input type=hidden name=rAquiNm value="<?=$agspay->GetResult("rAquiNm")?>">	<!-- (안심클릭,일반사용)매입사명 -->

            <!-- 핸드폰 결제 사용 변수 -->
            <input type=hidden name=rHP_HANDPHONE value="<?=$agspay->GetResult("HP_HANDPHONE")?>">		<!-- 핸드폰번호 -->
            <input type=hidden name=rHP_COMPANY value="<?=$agspay->GetResult("HP_COMPANY")?>">			<!-- 통신사명(SKT,KTF,LGT) -->
            <input type=hidden name=rHP_TID value="<?=$agspay->GetResult("rHP_TID")?>">					<!-- 결제TID -->
            <input type=hidden name=rHP_DATE value="<?=$agspay->GetResult("rHP_DATE")?>">				<!-- 결제일자 -->

            <!-- 가상계좌 결제 사용 변수 -->
            <input type=hidden name=rVirNo value="<?=$agspay->GetResult("rVirNo")?>">					<!-- 가상계좌번호 -->
            <input type=hidden name=VIRTUAL_CENTERCD value="<?=$agspay->GetResult("VIRTUAL_CENTERCD")?>">	<!--입금가상계좌은행코드(우리은행:20) -->

            <!-- 이지스에스크로 결제 사용 변수 -->
            <input type=hidden name=ES_SENDNO value="<?=$agspay->GetResult("ES_SENDNO")?>">				<!-- 이지스에스크로(전문번호) -->

            <!-- complete용 주문변수 -->
            <input type=hidden name=ordernum value="<?=$agspay->GetResult("rOrdNo")?>">		<!-- 주문번호 -->
            </form>
            <script>
                frmAGS_pay_ing.submit();
            </script>
<?
		}
	}
	else
	{
		// 결제실패에 따른 상점처리부분
        $OrdNo = $agspay->GetResult("rOrdNo");      //주문번호
        $errmsg = iconv("euc-kr","utf-8",$agspay->GetResult("rResMsg"));     //실패사유


        $canceltime = time();
        if($OrdNo) {
            $cancel_qry = "update ".$pub_slntype."Order set canceled = 'Y' ,
                           orderstep='fail' , 
                           canceldate = ".$canceltime." , 
                           ordersau = '".$errmsg."' 
                           where ordernum='".$OrdNo."'";
            $my->exec($cancel_qry);
        }
        $my->msgbox("결제가 실패처리되었습니다. [ 사유 : " . $errmsg.". ");
        $my->go($path_home);
		//echo ("결제가 실패처리되었습니다. [" . $agspay->GetResult("rSuccYn")."]". $agspay->GetResult("rResMsg").". " );
	}
	

	/*******************************************************************
	* [6] 결제가 정상처리되지 못했을 경우 $agspay->GetResult("NetCancID") 값을 이용하여                                     
	* 결제결과에 대한 재확인요청을 할 수 있습니다.
	* 
	* 추가 데이터송수신이 발생하므로 결제가 정상처리되지 않았을 경우에만 사용하시기 바랍니다. 
	*
	* 사용방법 :
	* $agspay->checkPayResult($agspay->GetResult("NetCancID"));
	*                           
	*******************************************************************/
	
	/*
	$agspay->SetValue("Type", "Pay"); // 고정
	$agspay->checkPayResult($agspay->GetResult("NetCancID"));
	*/
	
	/*******************************************************************
	* [7] 상점DB 저장 및 기타 처리작업 수행실패시 강제취소                                      
	*   
	* $cancelReq : "true" 강제취소실행, "false" 강제취소실행안함.
	*
	* 결제결과에 따른 상점처리부분 수행 중 실패하는 경우    
	* 아래의 코드를 참조하여 거래를 취소할 수 있습니다.
	*	취소성공여부 : $agspay->GetResult("rCancelSuccYn") (성공:y 실패:n)
	*	취소결과메시지 : $agspay->GetResult("rCancelResMsg")
	*
	* 유의사항 :
	* 가상계좌(virtual)는 강제취소 기능이 지원되지 않습니다.
	*******************************************************************/
	
	// 상점처리부분 수행실패시 $cancelReq를 "true"로 변경하여 
	// 결제취소를 수행되도록 할 수 있습니다.
	// $cancelReq의 "true"값으로 변경조건은 상점에서 판단하셔야 합니다.
	
	/*
	$cancelReq = "false";

	if($cancelReq == "true")
	{
		$agspay->SetValue("Type", "Cancel"); // 고정
		$agspay->SetValue("CancelMsg", "DB FAIL"); // 취소사유
		$agspay->startPay();
	}
	*/
	

?>