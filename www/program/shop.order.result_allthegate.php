<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
?>
<script language=javascript src="//www.allthegate.com/plugin/AGSWallet_utf8.js"></script>
<!-- ※ UTF8 언어 형식으로 페이지 제작시 아래 경로의 js 파일을 사용할 것!! -->
<!-- script language=javascript src="http://www.allthegate.com/plugin/AGSWallet_utf8.js"></script -->
<!-- ※ SSL 보안을 이용할 경우 아래 경로의 js 파일을 사용할 것!! -->
<!-- script language=javascript src="https://www.allthegate.com/plugin/AGSWallet_ssl.js"></script -->
<script language=javascript>
<!--
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 올더게이트 플러그인 설치를 확인합니다.
//////////////////////////////////////////////////////////////////////////////////////////////////////////////

StartSmartUpdate();  

function Pay(form){
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// MakePayMessage() 가 호출되면 올더게이트 플러그인이 화면에 나타나며 Hidden 필드
	// 에 리턴값들이 채워지게 됩니다.
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	if(form.Flag.value == "enable"){
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 입력된 데이타의 유효성을 검사합니다.
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		if(Check_Common(form) == true){
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// 올더게이트 플러그인 설치가 올바르게 되었는지 확인합니다.
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			if(document.AGSPay == null || document.AGSPay.object == null){
				alert("플러그인 설치 후 다시 시도 하십시오.");
			}else{
				//////////////////////////////////////////////////////////////////////////////////////////////////////////////
				// 올더게이트 플러그인 설정값을 동적으로 적용하기 JavaScript 코드를 사용하고 있습니다.
				// 상점설정에 맞게 JavaScript 코드를 수정하여 사용하십시오.
				//
				// [1] 일반/무이자 결제여부
				// [2] 일반결제시 할부개월수
				// [3] 무이자결제시 할부개월수 설정
				// [4] 인증여부
				//////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
				//////////////////////////////////////////////////////////////////////////////////////////////////////////////
				// [1] 일반/무이자 결제여부를 설정합니다.
				//
				// 할부판매의 경우 구매자가 이자수수료를 부담하는 것이 기본입니다. 그러나,
				// 상점과 올더게이트간의 별도 계약을 통해서 할부이자를 상점측에서 부담할 수 있습니다.
				// 이경우 구매자는 무이자 할부거래가 가능합니다.
				//
				// 예제)
				// 	(1) 일반결제로 사용할 경우
				// 	form.DeviId.value = "9000400001";
				//
				// 	(2) 무이자결제로 사용할 경우
				// 	form.DeviId.value = "9000400002";
				//
				// 	(3) 만약 결제 금액이 100,000원 미만일 경우 일반할부로 100,000원 이상일 경우 무이자할부로 사용할 경우
				// 	if(parseInt(form.Amt.value) < 100000)
				//		form.DeviId.value = "9000400001";
				// 	else
				//		form.DeviId.value = "9000400002";
				//////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
				form.DeviId.value = "9000400001";
				
				//////////////////////////////////////////////////////////////////////////////////////////////////////////////
				// [2] 일반 할부기간을 설정합니다.
				// 
				// 일반 할부기간은 2 ~ 12개월까지 가능합니다.
				// 0:일시불, 2:2개월, 3:3개월, ... , 12:12개월
				// 
				// 예제)
				// 	(1) 할부기간을 일시불만 가능하도록 사용할 경우
				// 	form.QuotaInf.value = "0";
				//
				// 	(2) 할부기간을 일시불 ~ 12개월까지 사용할 경우
				//		form.QuotaInf.value = "0:3:4:5:6:7:8:9:10:11:12";
				//
				// 	(3) 결제금액이 일정범위안에 있을 경우에만 할부가 가능하게 할 경우
				// 	if((parseInt(form.Amt.value) >= 100000) || (parseInt(form.Amt.value) <= 200000))
				// 		form.QuotaInf.value = "0:2:3:4:5:6:7:8:9:10:11:12";
				// 	else
				// 		form.QuotaInf.value = "0";
				//////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
				//결제금액이 5만원 미만건을 할부결제로 요청할경우 결제실패
				if(parseInt(form.Amt.value) < 50000)
					form.QuotaInf.value = "0";
				else
					form.QuotaInf.value = "0:2:3:4:5:6:7:8:9:10:11:12";
				
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				// [3] 무이자 할부기간을 설정합니다.
				// (일반결제인 경우에는 본 설정은 적용되지 않습니다.)
				// 
				// 무이자 할부기간은 2 ~ 12개월까지 가능하며, 
				// 올더게이트에서 제한한 할부 개월수까지만 설정해야 합니다.
				// 
				// 100:BC
				// 200:국민
				// 201:NH 
				// 300:외환
				// 310:하나SK
				// 400:삼성
				// 500:신한
				// 800:현대
				// 900:롯데
				// 
				// 예제)
				// 	(1) 모든 할부거래를 무이자로 하고 싶을때에는 ALL로 설정
				// 	form.NointInf.value = "ALL";
				//
				// 	(2) 국민카드 특정개월수만 무이자를 하고 싶을경우 샘플(2:3:4:5:6개월)
				// 	form.NointInf.value = "200-2:3:4:5:6";
				//
				// 	(3) 외환카드 특정개월수만 무이자를 하고 싶을경우 샘플(2:3:4:5:6개월)
				// 	form.NointInf.value = "300-2:3:4:5:6";
				//
				// 	(4) 국민,외환카드 특정개월수만 무이자를 하고 싶을경우 샘플(2:3:4:5:6개월)
				// 	form.NointInf.value = "200-2:3:4:5:6,300-2:3:4:5:6";
				//	
				//	(5) 무이자 할부기간 설정을 하지 않을 경우에는 NONE로 설정
				//	form.NointInf.value = "NONE";
				//
				//	(6) 전카드사 특정개월수만 무이자를 하고 싶은경우(2:3:6개월)
				//	form.NointInf.value = "100-2:3:6,200-2:3:6,201-2:3:6,300-2:3:6,310-2:3:6,400-2:3:6,500-2:3:6,600-2:3:6,
				//	800-2:3:6,900-2:3:6";
				//
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
				if(form.DeviId.value == "9000400002")
					form.NointInf.value = "ALL";
				   
				if(MakePayMessage(form) == true){					
					
					Disable_Flag(form);
					
					//var openwin = window.open("testAGS_progress.html","popup","width=300,height=160"); //"지불처리중"이라는 팝업창연결 부분
					
					form.submit();
				}else{
					alert("지불에 실패하였습니다.");// 취소시 이동페이지 설정부분
				}
			}
		}
	}
}

function Enable_Flag(form){
        form.Flag.value = "enable"
}

function Disable_Flag(form){
        form.Flag.value = "disable"
}

function Check_Common(form){
	if(form.StoreId.value == ""){
		alert("상점아이디를 입력하십시오.");
		return false;
	}
	else if(form.StoreNm.value == ""){
		alert("상점명을 입력하십시오.");
		return false;
	}
	else if(form.OrdNo.value == ""){
		alert("주문번호를 입력하십시오.");
		return false;
	}
	else if(form.ProdNm.value == ""){
		alert("상품명을 입력하십시오.");
		return false;
	}
	else if(form.Amt.value == ""){
		alert("금액을 입력하십시오.");
		return false;
	}
	else if(form.MallUrl.value == ""){
		alert("상점URL을 입력하십시오.");
		return false;
	}
	return true;
}

$(document).ready(function() {

 Enable_Flag(frmAGS_pay);
 
});

-->
</script>

<form name=frmAGS_pay method=post action='<?php echo OD_PROGRAM_URL; ?>/shop.order.result_allthegate.pro.php' target="common_frame">
<!-- 스크립트 및 플러그인에서 값을 설정하는 Hidden 필드  !!수정을 하시거나 삭제하지 마십시오-->

<!-- 각 결제 공통 사용 변수 -->
<input type=hidden name=Flag								value="">		<!-- 스크립트결제사용구분플래그 -->
<input type=hidden name=AuthTy							value="">		<!-- 결제형태 -->
<input type=hidden name=SubTy								value="">		<!-- 서브결제형태 -->

<!-- 신용카드 결제 사용 변수 -->
<input type=hidden name=DeviId							value="">				<!-- (신용카드공통)		단말기아이디 -->
<input type=hidden name=QuotaInf						value="0">			<!-- (신용카드공통)		일반할부개월설정변수 -->
<input type=hidden name=NointInf						value="NONE">		<!-- (신용카드공통)		무이자할부개월설정변수 -->
<input type=hidden name=AuthYn							value="">		<!-- (신용카드공통)		인증여부 -->
<input type=hidden name=Instmt							value="">		<!-- (신용카드공통)		할부개월수 -->
<input type=hidden name=partial_mm					value="">		<!-- (ISP사용)			일반할부기간 -->
<input type=hidden name=noIntMonth					value="">		<!-- (ISP사용)			무이자할부기간 -->
<input type=hidden name=KVP_RESERVED1				value="">		<!-- (ISP사용)			RESERVED1 -->
<input type=hidden name=KVP_RESERVED2				value="">		<!-- (ISP사용)			RESERVED2 -->
<input type=hidden name=KVP_RESERVED3				value="">		<!-- (ISP사용)			RESERVED3 -->
<input type=hidden name=KVP_CURRENCY				value="">		<!-- (ISP사용)			통화코드 -->
<input type=hidden name=KVP_CARDCODE				value="">		<!-- (ISP사용)			카드사코드 -->
<input type=hidden name=KVP_SESSIONKEY			value="">		<!-- (ISP사용)			암호화코드 -->
<input type=hidden name=KVP_ENCDATA					value="">		<!-- (ISP사용)			암호화코드 -->
<input type=hidden name=KVP_CONAME					value="">		<!-- (ISP사용)			카드명 -->
<input type=hidden name=KVP_NOINT						value="">		<!-- (ISP사용)			무이자/일반여부(무이자=1, 일반=0) -->
<input type=hidden name=KVP_QUOTA						value="">		<!-- (ISP사용)			할부개월 -->
<input type=hidden name=CardNo							value="">		<!-- (안심클릭,일반사용)	카드번호 -->
<input type=hidden name=MPI_CAVV						value="">		<!-- (안심클릭,일반사용)	암호화코드 -->
<input type=hidden name=MPI_ECI							value="">		<!-- (안심클릭,일반사용)	암호화코드 -->
<input type=hidden name=MPI_MD64						value="">		<!-- (안심클릭,일반사용)	암호화코드 -->
<input type=hidden name=ExpMon							value="">		<!-- (일반사용)			유효기간(월) -->
<input type=hidden name=ExpYear							value="">		<!-- (일반사용)			유효기간(년) -->
<input type=hidden name=Passwd							value="">		<!-- (일반사용)			비밀번호 -->
<input type=hidden name=SocId								value="">		<!-- (일반사용)			주민등록번호/사업자등록번호 -->

<!-- 계좌이체 결제 사용 변수 -->
<input type=hidden name=ICHE_OUTBANKNAME		value="">		<!-- 이체계좌은행명 -->
<input type=hidden name=ICHE_OUTACCTNO			value="">		<!-- 이체계좌예금주주민번호 -->
<input type=hidden name=ICHE_OUTBANKMASTER	value="">		<!-- 이체계좌예금주 -->
<input type=hidden name=ICHE_AMOUNT					value="">		<!-- 이체금액 -->

<!-- 핸드폰 결제 사용 변수 -->
<input type=hidden name=HP_SERVERINFO				value="">		<!-- 서버정보 -->
<input type=hidden name=HP_HANDPHONE				value="">		<!-- 핸드폰번호 -->
<input type=hidden name=HP_COMPANY					value="">		<!-- 통신사명(SKT,KTF,LGT) -->
<input type=hidden name=HP_IDEN							value="">		<!-- 인증시사용 -->
<input type=hidden name=HP_IPADDR						value="">		<!-- 아이피정보 -->

<!-- ARS 결제 사용 변수 -->
<input type=hidden name=ARS_PHONE						value="">		<!-- ARS번호 -->
<input type=hidden name=ARS_NAME						value="">		<!-- 전화가입자명 -->

<!-- 가상계좌 결제 사용 변수 -->
<input type=hidden name=ZuminCode						value="">		<!-- 가상계좌입금자주민번호 -->
<input type=hidden name=VIRTUAL_CENTERCD		value="">		<!-- 가상계좌은행코드 -->
<input type=hidden name=VIRTUAL_NO					value="">		<!-- 가상계좌번호 -->

<!-- 에스크로 결제 사용 변수 -->
<input type=hidden name=ES_SENDNO						value="">		<!-- 에스크로전문번호 -->

<!-- 계좌이체(소켓) 결제 사용 변수 -->
<input type=hidden name=ICHE_SOCKETYN				value="">		<!-- 계좌이체(소켓) 사용 여부 -->
<input type=hidden name=ICHE_POSMTID				value="">		<!-- 계좌이체(소켓) 이용기관주문번호 -->
<input type=hidden name=ICHE_FNBCMTID				value="">		<!-- 계좌이체(소켓) FNBC거래번호 -->
<input type=hidden name=ICHE_APTRTS					value="">		<!-- 계좌이체(소켓) 이체 시각 -->
<input type=hidden name=ICHE_REMARK1				value="">		<!-- 계좌이체(소켓) 기타사항1 -->
<input type=hidden name=ICHE_REMARK2				value="">		<!-- 계좌이체(소켓) 기타사항2 -->
<input type=hidden name=ICHE_ECWYN					value="">		<!-- 계좌이체(소켓) 에스크로여부 -->
<input type=hidden name=ICHE_ECWID					value="">		<!-- 계좌이체(소켓) 에스크로ID -->
<input type=hidden name=ICHE_ECWAMT1				value="">		<!-- 계좌이체(소켓) 에스크로결제금액1 -->
<input type=hidden name=ICHE_ECWAMT2				value="">		<!-- 계좌이체(소켓) 에스크로결제금액2 -->
<input type=hidden name=ICHE_CASHYN					value="">		<!-- 계좌이체(소켓) 현금영수증발행여부 -->
<input type=hidden name=ICHE_CASHGUBUN_CD		value="">		<!-- 계좌이체(소켓) 현금영수증구분 -->
<input type=hidden name=ICHE_CASHID_NO			value="">		<!-- 계좌이체(소켓) 현금영수증신분확인번호 -->

<!-- 텔래뱅킹-계좌이체(소켓) 결제 사용 변수 -->
<input type=hidden name=ICHEARS_SOCKETYN		value="">		<!-- 텔레뱅킹계좌이체(소켓) 사용 여부 -->
<input type=hidden name=ICHEARS_ADMNO				value="">		<!-- 텔레뱅킹계좌이체 승인번호 -->
<input type=hidden name=ICHEARS_POSMTID			value="">		<!-- 텔레뱅킹계좌이체 이용기관주문번호 -->
<input type=hidden name=ICHEARS_CENTERCD		value="">		<!-- 텔레뱅킹계좌이체 은행코드 -->
<input type=hidden name=ICHEARS_HPNO				value="">		<!-- 텔레뱅킹계좌이체 휴대폰번호 -->

<?
if ($r[o_paymethod] == "card") { $job = "onlycard";	}
else if($r[o_paymethod] == "iche")  { $job = "onlyiche"; }
else if($r[o_paymethod] == "virtual")  { $job = "onlyvirtualselfescrow"; }
?>


<input type=hidden name=paymethod			value="<?=$r[o_paymethod]?>">	<!-- 결제방법 -->
<input type=hidden name=Job						value="<?=$job?>">				<!-- 지불방법 -->
<input type=hidden name=StoreId				value="<?=$siteInfo[s_pg_code]?>"> <!-- 상점아이디 -->
<input type=hidden name=OrdNo					value="<?=$ordernum?>">		<!-- 주문번호 -->


<?php
	// 2017-06-16 ::: 부가세율설정 ::: JJC

		//	과세상품만 결제 시 (총 금액 30,000원 중 면세금액 0원 경우)
		//		Amt = 30000
		//	면세상품만 결제 시 (총 금액 30,000원 중 면세금액 30,000원 경우)
		//		Amt = 30000;30000;
		//	과세상품과 면세상품 혼합 결제 시 (총 금액 30,000원 중 면세금액 5,000원 경우)
		//		Amt = 30000;5000;

		echo "<input type=hidden name=Amt value='" . $r['o_price_real'] . ($app_vat_N > 0 ? ";". $app_vat_N . ";"  : "" ) . "'>";//<!-- 금액 -->

	// 2017-06-16 ::: 부가세율설정 ::: JJC
?>


<input type=hidden name=StoreNm				value="<?=$siteInfo[s_adshop]?>"><!-- 상점명 -->
<input type=hidden name=ProdNm				value="<?=$app_product_name?>">		<!-- 상품명 -->
<input type=hidden name=MallUrl				value="http://<?=$_SERVER[HTTP_HOST]?>">		<!-- 상점url -->
<input type=hidden name=MallPage			value="<?php echo OD_PROGRAM_URL; ?>/shop.order.result_allthegate_VirAcctResult.php">
<input type=hidden name=UserId				value="<?=$r[o_mid]?>">		<!-- 아이디 -->
<input type=hidden name=UserEmail			value="<?=$r[o_oemail]?>">	<!-- 주문자 메일 -->
<input type=hidden name=OrdNm  				value="<?=$r[o_oname]?>">	<!-- 주문자 -->
<input type=hidden name=OrdPhone			value="<?=$r[o_ohp]?>">		<!-- 주문자 연락처 -->
<input type=hidden name=RcpNm  				value="<?=$r[o_rname]?>">	<!-- 수취인 -->
<input type=hidden name=RcpPhone  		value="<?=$r[o_rhp]?>">	<!-- 수취인 연락처 -->
<input type=hidden name=DlvAddr  				value="<?=$r[o_raddr1]." ".$r[o_raddr2]?>">	<!-- 배송지주소 -->
<input type=hidden name=Remark  				value="<?=$r[o_content]?>">	<!-- 요구사항 -->
<input type="hidden" name="VIRTUAL_DEPODT" value="<?=date('Ymd', time() + ($siteInfo[s_pg_virtual_date] * 86400))?>">

</form>


<?php actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행 ?>