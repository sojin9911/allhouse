<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
?>
<!DOCTYPE html>
<html>
	<head>
		<!-- 화면축소/확대방지 -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0, target-densitydpi=medium-dpi">
		<!-- 모바일에서 숫자 전화자동링크방지 -->
		<meta name="format-detection" content="telephone=no">
		<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
		<script src="/include/js/jquery-1.11.2.min.js" type="text/javascript"></script>
		<script>
		function fnSubmit() {
			var fileName,fileName_mobile;
			var paymethod = $("input[name=PAYMETHOD]").val();
			var subdomain = $("input[name=ISTEST]").val() == "1" ? "ssltest" : "ssl";
			var ismobile = $("input[name=RESERVEDINDEX1]").val() == "1" ? 1 : 0;
			var isescrow = $("input[name=ISESCROW]").val() == "1" ? 1 : 0;

			if(paymethod == "card") {
				fileName 			= "https://"+subdomain+".daoupay.com/card/DaouCardMng.jsp";    
				fileName_mobile 	= "https://"+subdomain+".daoupay.com/m/card/DaouCardMng.jsp";    
			} else if(paymethod == "iche") {

				if(!isescrow) {
//					fileName 			= "http://"+subdomain+".daoupay.com/bank/DaouBankMng.jsp";    
//					fileName_mobile 	= "http://"+subdomain+".daoupay.com/m/bank/DaouBankMng.jsp";    

					// 15.9.23 계좌이체 Non-ActiveX 적용
					fileName 			= "http://"+subdomain+".daoupay.com/bank2/DaouBankMng.jsp";    
					fileName_mobile 	= "http://"+subdomain+".daoupay.com/m/bank2/DaouBankMng.jsp";    
				} else {
					fileName 			= "http://"+subdomain+".daoupay.com/bank/DaouBankEscrowMng.jsp";
					fileName_mobile 	= "http://"+subdomain+".daoupay.com/m/bank/DaouBankEscrowMng.jsp";
				}

			} else if(paymethod == "virtual") {
				
				if(!isescrow) {
					fileName 			= "http://"+subdomain+".daoupay.com/vaccount/DaouVaccountMng.jsp";    
					fileName_mobile 	= "http://"+subdomain+".daoupay.com/m/vaccount/DaouVaccountMng.jsp";    
				} else {
					fileName 			= "http://"+subdomain+".daoupay.com/vaccount/DaouVaccountEscrowMng.jsp";    
					fileName_mobile 	= "http://"+subdomain+".daoupay.com/m/vaccount/DaouVaccountEscrowMng.jsp";    
				}

			} else if(paymethod == "hpp") { // 휴대폰결제
				
					fileName 			= "https://"+subdomain+".kiwoompay.co.kr/2.0/mobile/DaouMobileMng.jsp";    
					fileName_mobile 	= "https://"+subdomain+".kiwoompay.co.kr/m/mobile/DaouMobileMng.jsp";    
		
			}

			pf = document.order_info;

			if (ismobile){
				pf.action = fileName_mobile;
			}else{
				pf.action = fileName;
			}

			pf.submit();
		}


		$(document).ready(function() {

			document.charset = "euc-kr"; // 익스 한글깨짐 해결

			// 한글이 깨지는 문제가 있어서, jquery 로 opener에서 가져온다.
			$("input[name=CPID]").val($(opener.document).find("input[name=CPID]").val());
			$("input[name=ORDERNO]").val($(opener.document).find("input[name=ORDERNO]").val());
			$("input[name=AMOUNT]").val($(opener.document).find("input[name=AMOUNT]").val());
//			$("input[name=AMOUNT]").val(1000); // 테스트 금액.
			$("input[name=PRODUCTNAME]").val($(opener.document).find("input[name=PRODUCTNAME]").val());
			$("input[name=PRODUCTTYPE]").val($(opener.document).find("input[name=PRODUCTTYPE]").val());
			$("input[name=BILLTYPE]").val($(opener.document).find("input[name=BILLTYPE]").val());
			$("input[name=EMAIL]").val($(opener.document).find("input[name=EMAIL]").val());
			$("input[name=USERNAME]").val($(opener.document).find("input[name=USERNAME]").val());
			$("input[name=RETURNURL]").val($(opener.document).find("input[name=RETURNURL]").val());
			$("input[name=HOMEURL]").val($(opener.document).find("input[name=HOMEURL]").val());
			$("input[name=TAXFREECD]").val($(opener.document).find("input[name=TAXFREECD]").val());
			$("input[name=CASHRECEIPTFLAG]").val($(opener.document).find("input[name=CASHRECEIPTFLAG]").val());
			$("input[name=PAYMETHOD]").val($(opener.document).find("input[name=PAYMETHOD]").val());
			$("input[name=ISTEST]").val($(opener.document).find("input[name=ISTEST]").val());
			$("input[name=ISESCROW]").val($(opener.document).find("input[name=ISESCROW]").val());
			$("input[name=RESERVEDINDEX1]").val($(opener.document).find("input[name=RESERVEDINDEX1]").val());
			$("input[name=TELNO2]").val($(opener.document).find("input[name=TELNO2]").val());
			$("input[name=CPQUOTA]").val($(opener.document).find("input[name=CPQUOTA]").val());

			// 폼을 실행시킨다.
			fnSubmit();
		});

		</script>
	</head>
	<body>

	<form name="order_info" action="" method="post" charset="euc-kr" accept-charset="EUC-KR"> 
		<input type="hidden" name="CPID"  >
		<input type="hidden" name="ORDERNO" >
		<input type="hidden" name="AMOUNT" >
		<input type="hidden" name="PRODUCTNAME" >
		<input type="hidden" name="PRODUCTTYPE" >
		<input type="hidden" name="BILLTYPE" >
		<input type="hidden" name="EMAIL" >
		<input type="hidden" name="USERNAME" >
		<input type="hidden" name="RETURNURL" >
		<input type="hidden" name="HOMEURL" >
		<input type="hidden" name="TAXFREECD" >
		<input type="hidden" name="CASHRECEIPTFLAG" >
		<input type="hidden" name="PAYMETHOD" >
		<input type="hidden" name="ISTEST" >
		<input type="hidden" name="ISESCROW" >
		<input type="hidden" name="RESERVEDINDEX1" >
		<input type="hidden" name="TELNO2" >
		<input type="hidden" name="CPQUOTA" >
	</form>

	</body>
</html>
<?php actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행 ?>