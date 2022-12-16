<?php
/**********************************************************************************************
*
* 프로젝트 : AGSMobile V1.0
* (※ 본 프로젝트는 아이폰 및 안드로이드에서 이용하실 수 있으며 일반 웹페이지에서는 결제가 불가합니다.)
*
* 파일명 : AGS_pay_result.php
* 작성일자 : 2010/10/6
*
* 소켓결제결과를 처리합니다.
*
* Copyright AEGIS ENTERPRISE.Co.,Ltd. All rights reserved.
*
**********************************************************************************************/

//공통사용
$AuthTy 		= trim( $_POST["AuthTy"] );				//결제형태
$SubTy 			= trim( $_POST["SubTy"] );				//서브결제형태
$rStoreId 		= trim( $_POST["rStoreId"] );			//업체ID
$rAmt 			= trim( $_POST["rAmt"] );				//거래금액
$rOrdNo 		= trim( $_POST["rOrdNo"] );				//주문번호
$rProdNm 		= trim( $_POST["rProdNm"] );			//상품명
$rOrdNm			= trim( $_POST["rOrdNm"] );				//주문자명

//소켓통신결제(신용카드,핸드폰,일반가상계좌)시 사용
$rSuccYn 		= trim( $_POST["rSuccYn"] );			//성공여부
$rResMsg 		= trim( $_POST["rResMsg"] );			//실패사유
$rApprTm 		= trim( $_POST["rApprTm"] );			//승인시각

//신용카드공통
$rBusiCd 		= trim( $_POST["rBusiCd"] );			//전문코드
$rApprNo 		= trim( $_POST["rApprNo"] );			//승인번호
$rCardCd 		= trim( $_POST["rCardCd"] );			//카드사코드
$rDealNo 		= trim( $_POST["rDealNo"] );			//거래고유번호

//신용카드(안심,일반)
$rCardNm 		= trim( $_POST["rCardNm"] );			//카드사명
$rMembNo 		= trim( $_POST["rMembNo"] );			//가맹점번호
$rAquiCd 		= trim( $_POST["rAquiCd"] );			//매입사코드
$rAquiNm 		= trim( $_POST["rAquiNm"] );			//매입사명

//핸드폰
$rHP_TID 		= trim( $_POST["rHP_TID"] );			//핸드폰결제TID
$rHP_DATE 		= trim( $_POST["rHP_DATE"] );			//핸드폰결제날짜
$rHP_HANDPHONE 	= trim( $_POST["rHP_HANDPHONE"] );		//핸드폰결제핸드폰번호
$rHP_COMPANY 	= trim( $_POST["rHP_COMPANY"] );		//핸드폰결제통신사명(SKT,KTF,LGT)

//가상계좌
$rVirNo 		= trim( $_POST["rVirNo"] );				//가상계좌번호 가상계좌추가
$VIRTUAL_CENTERCD = trim( $_POST["VIRTUAL_CENTERCD"] );	//가상계좌 입금은행코드

//이지스에스크로
$ES_SENDNO	= trim( $_POST["ES_SENDNO"] );				//이지스에스크로(전문번호)
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<META content="user-scalable=no, initial-scale = 1.0, maximum-scale=1.0, minimum-scale=1.0" name=viewport>
<META content=telephone=no name=format-detection>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-KR">
<title>올더게이트</title>
<style type="text/css">
body { font-family:"돋움"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; }
td { font-family:"돋움"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; }
.clsright { padding-right:10px; text-align:right; }
.clsleft { padding-left:10px; text-align:left; }
</style>
<script language=javascript> // "지불처리중" 팝업창 닫기
var _ua = window.navigator.userAgent.toLowerCase();

var browser = {
	model: _ua.match(/(samsung-sch-m490|sonyericssonx1i|ipod|iphone)/) ? _ua.match(/(samsung-sch-m490|sonyericssonx1i|ipod|iphone)/)[0] : "",
	skt : /msie/.test( _ua ) && /nate/.test( _ua ),
	lgt : /msie/.test( _ua ) && /([010|011|016|017|018|019]{3}\d{3,4}\d{4}$)/.test( _ua ),
	opera : (/opera/.test( _ua ) && /(ppc|skt)/.test(_ua)) || /opera mobi/.test( _ua ),
	ipod : /webkit/.test( _ua ) && /\(ipod/.test( _ua ) ,
	iphone : /webkit/.test( _ua ) && /\(iphone/.test( _ua ),
	lgtwv : /wv/.test( _ua ) && /lgtelecom/.test( _ua )
};

if(browser.opera) {
	document.write("<meta name=\"viewport\" content=\"user-scalable=no, initial-scale=0.75, maximum-scale=0.75, minimum-scale=0.75\" \/>");
} else if (browser.ipod || browser.iphone) {
	setTimeout(function() { if(window.pageYOffset == 0){ window.scrollTo(0, 1);} }, 100);
}
</script>
<script language=javascript>
/***********************************************************************************
* ◈ 영수증 출력을 위한 자바스크립트
*		
*	영수증 출력은 [카드결제]시에만 사용하실 수 있습니다.
*  
*   ※당일 결제건에 한해서 영수증 출력이 가능합니다.
*     당일 이후에는 아래의 주소를 팝업(630X510)으로 띄워 내역 조회 후 출력하시기 바랍니다.
*	  ▷ 팝업용 결제내역조회 패이지 주소 : 
*	     	 http://www.allthegate.com/support/card_search.html
*		→ (반드시 스크롤바를 'yes' 상태로 하여 팝업을 띄우시기 바랍니다.) ←
*
***********************************************************************************/
function show_receipt()
{
	if("<?=$rSuccYn?>"== "y" && "<?=$AuthTy?>"=="card")
	{
		var send_dt = appr_tm.value;
		
		url="http://www.allthegate.com/customer/receiptLast3.jsp"
		url=url+"?sRetailer_id="+sRetailer_id.value;
		url=url+"&approve="+approve.value;
		url=url+"&send_no="+send_no.value;
		url=url+"&send_dt="+send_dt.substring(0,8);
		
		location.href = url;
	}
	else
	{
		alert("해당하는 결제내역이 없습니다");
	}
}
</script>
</head>
<? //include "../../complete.php"; ?>
<body topmargin=0 leftmargin=0 rightmargin=0 bottommargin=0>
<table border=0 width=100% height=100% cellpadding=0 cellspacing=0>
	<tr>
		<td align=center>
		<table width=320 border=0 cellpadding=0 cellspacing=0>
			<tr>
				<td><hr></td>
			</tr>
			<tr>
				<td class=clsleft>지불 결과</td>
			</tr>
			<tr>
				<td><hr></td>
			</tr>
			<tr>
				<td>
				<table width=320 border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td class=clsright width=110>결제형태 : </td>
						<td class=clsleft width=220>
							<?php

							if($AuthTy == "card")
							{
								if($SubTy == "isp")
								{
									echo "신용카드결제-안전결제(ISP)";
								}	
								else if($SubTy == "visa3d")
								{
									echo "신용카드결제-안심클릭";
								}
								else if($SubTy == "normal")
								{
									echo "신용카드결제-일반결제";
								}
								
							}
							else if($AuthTy == "hp")
							{
								echo "핸드폰결제";
							}
							else if($AuthTy == "virtual")
							{
								echo "가상계좌결제";
							}
							?>
						</td>
					</tr>
					<tr>
						<td class=clsright>상점아이디 : </td>
						<td class=clsleft><?=$rStoreId?></td>
					</tr>
					<tr>
						<td class=clsright>주문번호 : </td>
						<td class=clsleft><?=$rOrdNo?></td>
					</tr>
					<tr>
						<td class=clsright>주문자명 : </td>
						<td class=clsleft><?=$rOrdNm?></td>
					</tr>
					<tr>
						<td class=clsright>상품명 : </td>
						<td class=clsleft><?=$rProdNm?></td>
					</tr>
					<tr>
						<td class=clsright>결제금액 : </td>
						<td class=clsleft><?=$rAmt?></td>
					</tr>
					<tr>
						<td class=clsright>성공여부 : </td>
						<td class=clsleft><?=$rSuccYn?></td>
					</tr>
					<tr>
						<td class=clsright>처리메세지 : </td>
						<td class=clsleft><?=$rResMsg?></td>
					</tr>
<?				if($AuthTy == "card" || $AuthTy == "virtual") { ?>
					<tr>
						<td class=clsright>승인시각 : </td>
						<td class=clsleft><?=$rApprTm?></td>
					</tr>
<?				}
				if($AuthTy == "card" && $rSuccYn == "y") {?>
					<tr>
						<td class=clsright>전문코드 : </td>
						<td class=clsleft><?=$rBusiCd?></td>
					</tr>
					<tr>
						<td class=clsright>승인번호 : </td>
						<td class=clsleft><?=$rApprNo?></td>
					</tr>
					<tr>
						<td class=clsright>카드사코드 : </td>
						<td class=clsleft><?=$rCardCd?></td>
					</tr>
					<tr>
						<td class=clsright>거래번호 : </td>
						<td class=clsleft><?=$rDealNo?></td>
					</tr>
<?				}
				if($AuthTy == "card" && ($SubTy == "visa3d" || $SubTy == "normal") && $rSuccYn == "y") {?>
					<tr>
						<td class=clsright>카드사명 : </td>
						<td class=clsleft><?=$rCardNm?></td>
					</tr>
					<tr>
						<td class=clsright>매입사코드 : </td>
						<td class=clsleft><?=$rAquiCd?></td>
					</tr>
					<tr>
						<td class=clsright>매입사명 : </td>
						<td class=clsleft><?=$rAquiNm?></td>
					</tr>
					<tr>
						<td class=clsright>가맹점번호 : </td>
						<td class=clsleft><?=$rMembNo?></td>
					</tr>					
<?				}
				if($AuthTy == "hp" ) {?>
					<tr>
						<td class=clsright>핸드폰결제TID : </td>
						<td class=clsleft><?=$rHP_TID?></td>
					</tr>
					<tr>
						<td class=clsright>핸드폰결제날짜 : </td>
						<td class=clsleft><?=$rHP_DATE?></td>
					</tr>
					<tr>
						<td class=clsright>핸드폰결제핸드폰번호 : </td>
						<td class=clsleft><?=$rHP_HANDPHONE?></td>
					</tr>
					<tr>
						<td class=clsright>핸드폰결제통신사명 : </td>
						<td class=clsleft><?=$rHP_COMPANY?></td>
					</tr>
<?				}
				if($AuthTy == "virtual" ) {?>
					<tr>
						<td class=clsright>입금계좌번호 : </td>
						<td class=clsleft><?=$rVirNo?></td>
					</tr>
                    <tr><!-- 은행코드(20) : 우리은행 -->
						<td class=clsright>입금은행 : </td>
						<td class=clsleft><?=getCenter_cd($VIRTUAL_CENTERCD)?></td>
					</tr>
                    <tr>
						<td class=clsright>예금주명 : </td>
						<td class=clsleft>(주)이지스효성</td>
					</tr>
					<tr>
						<td class=clsright>이지스에스크로(SEND_NO) : </td>
						<td class=clsleft><?=$ES_SENDNO?></td>
					</tr>
<?				}
				if($AuthTy == "card" ) {?>
					<tr>
						<td class=clsright>영수증 :</td>
						<!--영수증출력을위해서보내주는값-------------------->
						<input type=hidden name=sRetailer_id value="<?=$rStoreId?>"><!--상점아이디-->
						<input type=hidden name=approve value="<?=$rApprNo?>"><!---승인번호-->
						<input type=hidden name=send_no value="<?=$rDealNo?>"><!--거래고유번호-->
						<input type=hidden name=appr_tm value="<?=$rApprTm?>"><!--승인시각-->
						<!--영수증출력을위해서보내주는값-------------------->
						<td class=clsleft><input type="button" value="영수증" onclick="javascript:show_receipt();"></td>
					</tr>
					<tr>
						<td colspan=2>&nbsp;</td>
					</tr>
					<tr>
						<td align=center colspan=2>카드 이용명세서에 구입처가 <font color=red>이지스 엔터프라이즈(주)</font>로 표기됩니다.</td>
					</tr>
<?				}	?>
					
				</table>
				</td>
			</tr>
			<tr>
				<td><hr></td>
			</tr>
			<tr>
				<td class=clsleft>Copyright AEGIS ENTERPRISE.Co.,Ltd. All rights reserved.</td> 
			</tr>
		</table>
		</td>
	</tr>
</table>
</body>
</html>
<?
	function getCenter_cd($VIRTUAL_CENTERCD){
		if($VIRTUAL_CENTERCD == "39"){
			echo "경남은행";
		}else if($VIRTUAL_CENTERCD == "34"){
			echo "광주은행";
		}else if($VIRTUAL_CENTERCD == "04"){
			echo "국민은행";
		}else if($VIRTUAL_CENTERCD == "11"){
			echo "농협중앙회";
		}else if($VIRTUAL_CENTERCD == "31"){
			echo "대구은행";
		}else if($VIRTUAL_CENTERCD == "32"){
			echo "부산은행";
		}else if($VIRTUAL_CENTERCD == "02"){
			echo "산업은행";
		}else if($VIRTUAL_CENTERCD == "45"){
			echo "새마을금고";
		}else if($VIRTUAL_CENTERCD == "07"){
			echo "수협중앙회";
		}else if($VIRTUAL_CENTERCD == "48"){
			echo "신용협동조합";
		}else if($VIRTUAL_CENTERCD == "26"){
			echo "(구)신한은행";
		}else if($VIRTUAL_CENTERCD == "05"){
			echo "외환은행";
		}else if($VIRTUAL_CENTERCD == "20"){
			echo "우리은행";
		}else if($VIRTUAL_CENTERCD == "71"){
			echo "우체국";
		}else if($VIRTUAL_CENTERCD == "37"){
			echo "전북은행";
		}else if($VIRTUAL_CENTERCD == "23"){
			echo "제일은행";
		}else if($VIRTUAL_CENTERCD == "35"){
			echo "제주은행";
		}else if($VIRTUAL_CENTERCD == "21"){
			echo "(구)조흥은행";
		}else if($VIRTUAL_CENTERCD == "03"){
			echo "중소기업은행";
		}else if($VIRTUAL_CENTERCD == "81"){
			echo "하나은행";
		}else if($VIRTUAL_CENTERCD == "88"){
			echo "신한은행";
		}else if($VIRTUAL_CENTERCD == "27"){
			echo "한미은행";
		}
				}
?>
