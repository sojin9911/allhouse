<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
$ordernum = $_SESSION["session_ordernum"];//주문번호




// --> 비회원 구매를 위한 쿠키 적용여부 파악
cookie_chk();

$_POST["StoreNm"]  = iconv("utf-8", "euc-kr", $_POST["StoreNm"]);
$_POST["ProdNm"]   = iconv("utf-8", "euc-kr", $_POST["ProdNm"]);
$_POST["OrdNm"]    = iconv("utf-8", "euc-kr", $_POST["OrdNm"]);
$_POST["RcpNm"]    = iconv("utf-8", "euc-kr", $_POST["RcpNm"]);
$_POST["DlvAddr"]  = iconv("utf-8", "euc-kr", $_POST["DlvAddr"]);
$_POST["Remark"]   = iconv("utf-8", "euc-kr", $_POST["Remark"]);

$_POST["KVP_CURRENCY"]   = iconv("utf-8", "euc-kr", $_POST["KVP_CURRENCY"]);
$_POST["KVP_CARDCODE"]   = iconv("utf-8", "euc-kr", $_POST["KVP_CARDCODE"]);
$_POST["KVP_SESSIONKEY"] = iconv("utf-8", "euc-kr", $_POST["KVP_SESSIONKEY"]);
$_POST["KVP_ENCDATA"]    = iconv("utf-8", "euc-kr", $_POST["KVP_ENCDATA"]);
$_POST["KVP_CONAME"]     = iconv("utf-8", "euc-kr", $_POST["KVP_CONAME"]);
$_POST["KVP_NOINT"]      = iconv("utf-8", "euc-kr", $_POST["KVP_NOINT"]);
$_POST["KVP_QUOTA"]      = iconv("utf-8", "euc-kr", $_POST["KVP_QUOTA"]);

$_POST["KVP_CONAME"]      = iconv("utf-8", "euc-kr", $_POST["KVP_CONAME"]);
$_POST["KVP_CARDCODE"]      = iconv("utf-8", "euc-kr", $_POST["KVP_CARDCODE"]);

$_POST["ICHE_OUTBANKNAME"]   = iconv("utf-8", "euc-kr", $_POST["ICHE_OUTBANKNAME"]);
$_POST["ICHE_OUTBANKMASTER"] = iconv("utf-8", "euc-kr", $_POST["ICHE_OUTBANKMASTER"]);
$_POST["ES_SENDNO"]          = iconv("utf-8", "euc-kr", $_POST["ES_SENDNO"]);

/****************************************************************************
*
* [1] 라이브러리(AGSLib.php)를 인클루드 합니다.
*
****************************************************************************/
require(PG_DIR."/Ags/lib/AGSLib.php");

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
$agspay->SetValue("AgsPayHome",PG_DIR."/Ags");								      						//올더게이트 결제설치 디렉토리 (상점에 맞게 수정)
$agspay->SetValue("StoreId",trim($_POST["StoreId"]));										//상점아이디
$agspay->SetValue("log","true");									//true : 로그기록, false : 로그기록안함.
$agspay->SetValue("logLevel","INFO");							//로그레벨 : DEBUG, INFO, WARN, ERROR, FATAL (해당 레벨이상의 로그만 기록됨)
$agspay->SetValue("UseNetCancel","true");					//true : 망취소 사용. false: 망취소 미사용
$agspay->SetValue("Type", "Pay");									//고정값(수정불가)
$agspay->SetValue("RecvLen", 7);									//수신 데이터(길이) 체크 에러시 6 또는 7 설정. 

$agspay->SetValue("AuthTy",trim($_POST["AuthTy"]));					//결제형태
$agspay->SetValue("SubTy",trim($_POST["SubTy"]));						//서브결제형태
$agspay->SetValue("OrdNo",trim($_POST["OrdNo"]));						//주문번호
$agspay->SetValue("Amt",trim($_POST["Amt"]));								//금액
$agspay->SetValue("UserEmail",trim($_POST["UserEmail"]));		//주문자이메일
$agspay->SetValue("ProdNm",trim($_POST["ProdNm"]));					//상품명

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

/*계좌이체사용*/
$agspay->SetValue("ICHE_OUTBANKNAME",trim($_POST["ICHE_OUTBANKNAME"]));		//이체은행명
$agspay->SetValue("ICHE_OUTACCTNO",trim($_POST["ICHE_OUTACCTNO"]));			//이체계좌번호
$agspay->SetValue("ICHE_OUTBANKMASTER",trim($_POST["ICHE_OUTBANKMASTER"]));	//이체계좌소유주
$agspay->SetValue("ICHE_AMOUNT",trim($_POST["ICHE_AMOUNT"]));				//이체금액

/*핸드폰사용*/
$agspay->SetValue("HP_SERVERINFO",trim($_POST["HP_SERVERINFO"]));	//SERVER_INFO(핸드폰결제)
$agspay->SetValue("HP_HANDPHONE",trim($_POST["HP_HANDPHONE"]));		//HANDPHONE(핸드폰결제)
$agspay->SetValue("HP_COMPANY",trim($_POST["HP_COMPANY"]));			//COMPANY(핸드폰결제)
$agspay->SetValue("HP_ID",trim($_POST["HP_ID"]));					//HP_ID(핸드폰결제)
$agspay->SetValue("HP_SUBID",trim($_POST["HP_SUBID"]));				//HP_SUBID(핸드폰결제)
$agspay->SetValue("HP_UNITType",trim($_POST["HP_UNITType"]));		//HP_UNITType(핸드폰결제)
$agspay->SetValue("HP_IDEN",trim($_POST["HP_IDEN"]));				//HP_IDEN(핸드폰결제)
$agspay->SetValue("HP_IPADDR",trim($_POST["HP_IPADDR"]));			//HP_IPADDR(핸드폰결제)

/*ARS사용*/
$agspay->SetValue("ARS_NAME",trim($_POST["ARS_NAME"]));				//ARS_NAME(ARS결제)
$agspay->SetValue("ARS_PHONE",trim($_POST["ARS_PHONE"]));			//ARS_PHONE(ARS결제)

/*가상계좌사용*/
$agspay->SetValue("VIRTUAL_CENTERCD",trim($_POST["VIRTUAL_CENTERCD"]));	//은행코드(가상계좌)
$agspay->SetValue("VIRTUAL_DEPODT",trim($_POST["VIRTUAL_DEPODT"]));		//입금예정일(가상계좌)
$agspay->SetValue("ZuminCode",trim($_POST["ZuminCode"]));				//주민번호(가상계좌)
$agspay->SetValue("MallPage",trim($_POST["MallPage"]));					//상점 입/출금 통보 페이지(가상계좌)
$agspay->SetValue("VIRTUAL_NO",trim($_POST["VIRTUAL_NO"]));				//가상계좌번호(가상계좌)

/*에스크로사용*/
$agspay->SetValue("ES_SENDNO",trim($_POST["ES_SENDNO"]));				//에스크로전문번호

/*계좌이체(소켓) 결제 사용 변수*/
$agspay->SetValue("ICHE_SOCKETYN",trim($_POST["ICHE_SOCKETYN"]));			//계좌이체(소켓) 사용 여부
$agspay->SetValue("ICHE_POSMTID",trim($_POST["ICHE_POSMTID"]));				//계좌이체(소켓) 이용기관주문번호
$agspay->SetValue("ICHE_FNBCMTID",trim($_POST["ICHE_FNBCMTID"]));			//계좌이체(소켓) FNBC거래번호
$agspay->SetValue("ICHE_APTRTS",trim($_POST["ICHE_APTRTS"]));				//계좌이체(소켓) 이체 시각
$agspay->SetValue("ICHE_REMARK1",trim($_POST["ICHE_REMARK1"]));				//계좌이체(소켓) 기타사항1
$agspay->SetValue("ICHE_REMARK2",trim($_POST["ICHE_REMARK2"]));				//계좌이체(소켓) 기타사항2
$agspay->SetValue("ICHE_ECWYN",trim($_POST["ICHE_ECWYN"]));					//계좌이체(소켓) 에스크로여부
$agspay->SetValue("ICHE_ECWID",trim($_POST["ICHE_ECWID"]));					//계좌이체(소켓) 에스크로ID
$agspay->SetValue("ICHE_ECWAMT1",trim($_POST["ICHE_ECWAMT1"]));				//계좌이체(소켓) 에스크로결제금액1
$agspay->SetValue("ICHE_ECWAMT2",trim($_POST["ICHE_ECWAMT2"]));				//계좌이체(소켓) 에스크로결제금액2
$agspay->SetValue("ICHE_CASHYN",trim($_POST["ICHE_CASHYN"]));				//계좌이체(소켓) 현금영수증발행여부
$agspay->SetValue("ICHE_CASHGUBUN_CD",trim($_POST["ICHE_CASHGUBUN_CD"]));	//계좌이체(소켓) 현금영수증구분
$agspay->SetValue("ICHE_CASHID_NO",trim($_POST["ICHE_CASHID_NO"]));			//계좌이체(소켓) 현금영수증신분확인번호

/*계좌이체-텔래뱅킹(소켓) 결제 사용 변수*/
$agspay->SetValue("ICHEARS_SOCKETYN", trim($_POST["ICHEARS_SOCKETYN"]));	//텔레뱅킹계좌이체(소켓) 사용 여부
$agspay->SetValue("ICHEARS_ADMNO", trim($_POST["ICHEARS_ADMNO"]));			//텔레뱅킹계좌이체 승인번호       
$agspay->SetValue("ICHEARS_POSMTID", trim($_POST["ICHEARS_POSMTID"]));		//텔레뱅킹계좌이체 이용기관주문번호
$agspay->SetValue("ICHEARS_CENTERCD", trim($_POST["ICHEARS_CENTERCD"]));	//텔레뱅킹계좌이체 은행코드      
$agspay->SetValue("ICHEARS_HPNO", trim($_POST["ICHEARS_HPNO"]));			//텔레뱅킹계좌이체 휴대폰번호   

/****************************************************************************
*
* [4] 올더게이트 결제서버로 결제를 요청합니다.
*
****************************************************************************/
$agspay->startPay();


// 주문번호 오류 수정
$ordernum = trim($ordernum) ? $ordernum : $agspay->GetResult('rOrdNo');


// - 결제 성공 기록정보 저장 ---
$keys = array('rResMsg','AuthTy','rApprTm','rDealNo','SubTy');
$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
foreach($keys as $name) {
	$app_oc_content .= $name . "||" .iconv("euc-kr","utf-8",$agspay->GetResult($name)) . "§§" ;
}


// 회원정보 추출
if(is_login()) $indr = $mem_info;

// 주문정보 추출
$r = _MQ("select * from smart_order where o_ordernum='". $ordernum ."' ");


// - 주문결제기록 저장 ---
$que = "
	insert smart_order_cardlog set
		 oc_oordernum = '".$ordernum."'
		,oc_tid = '". $agspay->GetResult('rApprNo') ."'
		,oc_content = '". $app_oc_content ."'
		,oc_rdate = now();
";
_MQ_noreturn($que);
// - 주문결제기록 저장 ---
// - 결제 성공 기록정보 저장 ---

//// 가상계좌 에스크로 이용 결제시 수수료 계산
//if($agspay->GetResult('rAmt') < 30000) { $ool_amount_total_final = $agspay->GetResult('rAmt') + 500; }
//if($agspay->GetResult('rAmt') >= 30000 && $agspay->GetResult('rAmt') < 200000) { $ool_amount_total_final = $agspay->GetResult('rAmt') + 800; }
//if($agspay->GetResult('rAmt') >= 200000 && $agspay->GetResult('rAmt') < 500000) { $ool_amount_total_final = $agspay->GetResult('rAmt') + 1400; }
//if($agspay->GetResult('rAmt') >= 500000 && $agspay->GetResult('rAmt') < 1000000) { $ool_amount_total_final = $agspay->GetResult('rAmt') + 2500; }
//if($agspay->GetResult('rAmt') >= 1000000) { $ool_amount_total_final = $agspay->GetResult('rAmt') + 4900; }


// 카드결제로 넘어온값 처리.
if($agspay->GetResult("rSuccYn") == "y")
{ 

	if($agspay->GetResult("AuthTy") == "virtual"){ // 가상계좌 결제
		$ool_ordernum = iconv('euc-kr','utf-8',$agspay->GetResult('rOrdNo'));
		$ool_member = $indr[in_id];
		$ool_tid = $agspay->GetResult('rDealNo');
		$ool_type = 'R';
		$ool_respdate = iconv('euc-kr','utf-8',$agspay->GetResult('rApprTm'));
		$ool_amount_current = trim($_POST['Amt']);
		//$ool_amount_total = $ool_amount_total_final;
		$ool_account_num = iconv('euc-kr','utf-8',$agspay->GetResult('rVirNo'));
		$ool_account_code = '';
		$ool_deposit_name = $indr[in_name];
		$ool_bank_name_array = array(
			'39'=>'경남',
			'34'=>'광주',
			'04'=>'국민',
			'03'=>'기업',
			'11'=>'농협',
			'31'=>'대구',
			'32'=>'부산',
			'02'=>'산업',
			'45'=>'새마을금고',
			'07'=>'수협',
			'88'=>'신한',
			'48'=>'신협',
			'05'=>'외환',
			'20'=>'우리',
			'71'=>'우체국',
			'37'=>'전북',
			'35'=>'제주',
			'81'=>'하나',
			'27'=>'한국씨티',
			'23'=>'SC은행',
			'09'=>'동양증권',
			'78'=>'신한금융투자증권',
			'40'=>'삼성증권',
			'30'=>'미래에셋증권',
			'43'=>'한국투자증권',
			'69'=>'한화증권'
		);
		$ool_bank_name = $ool_bank_name_array[$agspay->GetResult('VIRTUAL_CENTERCD')];
        $ool_bank_code = iconv('euc-kr','utf-8',$agspay->GetResult('VIRTUAL_CENTERCD'));

        // -- 2016-11-17 에스크로 적용여부에따른 에스크로수수료 적용 수정 SSJ ----
        //$ool_escrow = iconv('euc-kr','utf-8',$agspay->GetResult('ES_SENDNO'));
        //$ool_escrow_code = substr($agspay->GetResult('rResMsg'),-6);
        $ex_escrow = explode(':', $agspay->GetResult('rResMsg'));
        $ool_escrow_code = $ex_escrow[1];
        //$ool_escrow = 'Y';
        $ool_escrow = ($ool_escrow_code ? 'Y' : 'N');
        $ool_amount_total_final = $agspay->GetResult('rAmt');
        $ool_escrow_fee = 0;
        if($ool_escrow == "Y"){
            // 가상계좌 에스크로 이용 결제시 수수료 계산
            if($agspay->GetResult('rAmt') < 30000) { $ool_escrow_fee = 500; }
            if($agspay->GetResult('rAmt') >= 30000 && $agspay->GetResult('rAmt') < 200000) { $ool_escrow_fee = 800; }
            if($agspay->GetResult('rAmt') >= 200000 && $agspay->GetResult('rAmt') < 500000) { $ool_escrow_fee = 1400; }
            if($agspay->GetResult('rAmt') >= 500000 && $agspay->GetResult('rAmt') < 1000000) { $ool_escrow_fee = 2500; }
            if($agspay->GetResult('rAmt') >= 1000000) { $ool_escrow_fee = 4900; }
        }
        $ool_amount_total = $ool_amount_total_final + $ool_escrow_fee;
        // -- 2016-11-17 에스크로 적용여부에따른 에스크로수수료 적용 수정 SSJ ----

        $ool_deposit_tel = $indr[in_tel];
		$ool_bank_owner = $indr[in_name];
        _MQ_noreturn("
            insert into smart_order_onlinelog (
                ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_escrow_code, ool_deposit_tel, ool_bank_owner, ool_escrow_fee
            ) values (
                '$ool_ordernum', '$ool_member', now(), '$ool_tid', '$ool_type', '$ool_respdate', '$ool_amount_current', '$ool_amount_total', '$ool_account_num', '$ool_account_code', '$ool_deposit_name', '$ool_bank_name', '$ool_bank_code', '$ool_escrow', '$ool_escrow_code', '$ool_deposit_tel', '$ool_bank_owner' , '$ool_escrow_fee'
            )
        ");

		// 장바구니 정보 삭제
		_MQ_noreturn(" delete from smart_cart where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' and c_direct='Y'  ");

		// 가상계좌 결제 이메일 및 SMS 발송
		$ordernum = $ool_ordernum;
		include_once OD_PROGRAM_ROOT."/shop.order.mail.send.virtual.php";

		error_loc("/?pn=shop.order.complete","top");
	} else {
		// 성공

		// -- 최종결제요청 결과 성공 DB처리 ---
			//echo "최종결제요청 결과 성공 DB처리하시기 바랍니다.<br>";

		// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
		include OD_PROGRAM_ROOT."/shop.order.result.pro.php";

		// 결제완료페이지 이동
		error_loc("/?pn=shop.order.complete","top");
 	}
}
else
{

	//최종결제요청 결과 실패 DB처리
	//echo "최종결제요청 결과 실패 DB처리하시기 바랍니다.<br>";
	_MQ_noreturn("update smart_order set o_status='결제실패' where o_ordernum='". $ordernum ."' ");
	error_loc_msg("/?pn=shop.order.result" , "결제에 실패하였습니다. 다시한번 확인 바랍니다.","top");
}







actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행