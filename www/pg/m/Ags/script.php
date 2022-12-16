<script language=javascript>
function PayStart(){
    
    frmAGS_pay.action = "http://www.allthegate.com/payment/mobile/pay_start.jsp";
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
		
		frmAGS_pay.DeviId.value = "9000400001";
		
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
		//		form.QuotaInf.value = "0:2:3:4:5:6:7:8:9:10:11:12";
		//
		// 	(3) 결제금액이 일정범위안에 있을 경우에만 할부가 가능하게 할 경우
		// 	if((parseInt(form.Amt.value) >= 100000) || (parseInt(form.Amt.value) <= 200000))
		// 		form.QuotaInf.value = "0:2:3:4:5:6:7:8:9:10:11:12";
		// 	else
		// 		form.QuotaInf.value = "0";
    		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		//결제금액이 5만원 미만건을 할부결제로 요청할경우 결제실패
		if(parseInt(frmAGS_pay.Amt.value) < 50000)
			frmAGS_pay.QuotaInf.value = "0";
		else
			frmAGS_pay.QuotaInf.value = "0:2:3:4:5:6:7:8:9:10:11:12";
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// [3] 무이자 할부기간을 설정합니다.
		// (일반결제인 경우에는 본 설정은 적용되지 않습니다.)
		// 
		// 무이자 할부기간은 2 ~ 12개월까지 가능하며, 
		// 올더게이트에서 제한한 할부 개월수까지만 설정해야 합니다.
		// 
		// 100:BC
		// 200:국민
		// 300:외환
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
		//	form.NointInf.value = "100-2:3:6,200-2:3:6,300-2:3:6,400-2:3:6,500-2:3:6,600-2:3:6,800-2:3:6,900-2:3:6";
		//
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		if(frmAGS_pay.DeviId.value == "9000400002")
			frmAGS_pay.NointInf.value = "100-2:3:6,200-2:3:6,300-2:3:6,400-2:3:6,500-2:3:6,600-2:3:6,800-2:3:6,900-2:3:6";


        document.charset="euc-kr";
		frmAGS_pay.submit();
}
</script>
<?
    //데이터정의
    if($paymethod == "C")       $ags_paymethod ="card";
    elseif($paymethod == "H")   $ags_paymethod ="hp";       //휴대폰(미지원)    
    elseif($paymethod == "V")   $ags_paymethod ="virtual";  //가상계좌(미지원)
    else                        $ags_paymethod ="";
    //main_pname
?>
<form name="frmAGS_pay" method="post" action="http://www.allthegate.com/payment/mobile/pay_start.jsp" accept-charset="euc-kr" style="display:none;">
<input type="hidden" name="Job" style="width:150px" value="<?=$ags_paymethod?>"> <!--결제방법-->
<input type="hidden" style="width:100px" name="StoreId" maxlength=20 value="<?=$pub_pgid?>"> <!--상점id-->
<input type="hidden" style=width:100px name=OrdNo maxlength=40 value="<?=$ordernum?>"> <!--주문번호-->
<input type="hidden" style=width:100px name=Amt maxlength=12 value="<?=$param_last_amt?>"><!--결제금액-->
<input type="hidden" style=width:180px name=StoreNm value="<?=$pub_company['name']?>"><!--회사명-->
<input type="hidden" style=width:180px name=ProdNm maxlength=300 value="<?=$main_pname?>"><!--상품명-->
<input type="hidden" style=width:180px name=MallUrl value="<?="http://".$_SERVER["HTTP_HOST"]?>">
<input type="hidden" style=width:180px name=UserEmail maxlength=50 value="<?=$orderemail?>">
<input type="hidden" style=width:180px name=UserId maxlength=20 value="<?=$orderid?>">
<input type="hidden" style=width:100px name=OrdNm maxlength=40 value="<?=$ordername?>">
<input type="hidden" style=width:100px name=OrdPhone maxlength=21 value="<?=$orderhtel?>">
<input type="hidden" style=width:180px name=OrdAddr maxlength=100 value="<?=$orderaddress.$orderaddress1?>">.
<input type="hidden" style=width:100px name=RcpNm maxlength=40 value="<?=$recname?>">
<input type="hidden" style=width:100px name=RcpPhone maxlength=21 value="<?=$rechtel?>">
<input type="hidden" style=width:180px name=DlvAddr maxlength=100 value="<?=$recaddress.$recaddress1?>">
<input type="hidden" style=width:180px name=Remark maxlength=350 value="<?=$comment?>">

<!-- 결제창에 특정카드만 표기기능입니다.       사용방법 예)  BC, 국민을 사용하고자 하는 경우 ☞ 100:200
모두 사용하고자 할 때에는 아무 값도 입력하지 않습니다.  카드사별 코드는 매뉴얼에서 확인해 주시기 바랍니다. 
코드번호	카드사명
100	BC
200	국민
300	외환
400	삼성
500	신한
800	현대
900	롯데
-->
<input type="hidden" style=width:300px name=CardSelect value="">

<input type="hidden" style=width:180px name=RtnUrl value="<?=$path_home?>/pages/order/pgscript/A/AGS_pay_ing.php">
<input type="hidden" style=width:180px name=CancelUrl value="<?=$path_home?>/pages/order/pgscript/A/AGS_pay_cancel.php?area=<?=$main_area?>&OrdNo=<?=$ordernum?>">
<input type="hidden" style=width:180px name=Column1 maxlength=200 value="">
<input type="hidden" style=width:180px name=Column2 maxlength=200 value="">
<input type="hidden" style=width:180px name=Column3 maxlength=200 value="">

<!-- 가상계좌 결제에서 입/출금 통보를 위한 필수 입력 사항 입니다. -->
<!-- 페이지주소는 도메인주소를 제외한 '/'이후 주소를 적어주시면 됩니다. -->
<input type="hidden" style=width:180px name=MallPage maxlength=100 value="./AGS_VirAcctResult.php"> <!--통보페이지 -->
<input type="hidden" style=width:180px name=VIRTUAL_DEPODT maxlength=8 value="20100530"><!--입금예정일 -->


<!-- 핸드폰결제용변수 -->

<!-- CP아이디를 핸드폰 결제 실거래 전환후에는 발급받으신 CPID로 변경하여 주시기 바랍니다. -->
<input type="hidden" style=width:100px name=HP_ID maxlength=10 value="">
<!-- CP비밀번호를 핸드폰 결제 실거래 전환후에는 발급받으신 비밀번호로 변경하여 주시기 바랍니다. -->
<input type="hidden" style=width:100px name=HP_PWD maxlength=10 value="">
<!-- SUB-CPID는 핸드폰 결제 실거래 전환후에 발급받으신 상점만 입력하여 주시기 바랍니다. -->
<input type="hidden" style=width:100px name=HP_SUBID maxlength=10 value="">
<!-- 상품코드를 핸드폰 결제 실거래 전환후에는 발급받으신 상품코드로 변경하여 주시기 바랍니다. -->
<input type="hidden" style=width:100px name=ProdCode maxlength=10 value="">
<!-- 상품종류를 핸드폰 결제 실거래 전환후에는 발급받으신 상품종류로 변경하여 주시기 바랍니다. -->
<!-- 판매하는 상품이 디지털(컨텐츠)일 경우 = 1, 실물(상품)일 경우 = 2 -->
<input type="hidden" name=HP_UNITType value="1">


<input type=hidden name=DeviId value="">			<!-- 단말기아이디 -->
<input type=hidden name=QuotaInf value="0">			<!-- 할부개월설정변수 -->
<input type=hidden name=NointInf value="NONE">		<!-- 무이자할부개월설정변수 -->

</form>