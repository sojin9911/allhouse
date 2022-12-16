<?php
/********************************************************************************
*
* 프로젝트 : AGSMobile V1.0
* (※ 본 프로젝트는 아이폰 및 안드로이드에서 이용하실 수 있으며 일반 웹페이지에서는 결제가 불가합니다.)
*
* 파일명 : AGS_pay_cancel.php
* 최종수정일자 : 2010/10/6
*
* 올더게이트 결제창에서 리턴된 데이터를 받아서 소켓결제요청을 합니다.
*
* Copyright AEGIS ENTERPRISE.Co.,Ltd. All rights reserved.
*
* 필요하신 경우 AGS_pay.html 페이지의 소스 지정에서 뒤에 GET 방식으로 파라미터를 붙여서 넘기시면 처리가 가능 합니다.
* 예시) http://www.allthegate.com/testmall/AGS_pay_cancel.php?param=content1&param=content2
*
*******************************************************************************/

$tag="order_cancel";
include "../../../top.php";  //상단(로고및 메뉴) 

##파라미터
$OrdNo = $_REQUEST['OrdNo'];	
$area = $_REQUEST['area'];	

##비로그인 상태일경우 로그인화면으로 이동
if(!$my->islogin()) {
    $my->msgbox("비정상적인 접속입니다.");
    $my->go("/m/?area=".$main_area);
    exit;
}

##해당주문의 상태를 canceled로 설정하고 ordersau 에는 사용자취소로 입력 canceled='Y'로 설정

$canceltime = time();

if($OrdNo && $my->islogin()) {
    $cancel_qry = "update ".$pub_slntype."Order set canceled = 'Y' ,  orderstep='fail' , canceldate = ".$canceltime." , ordersau = '사용자취소' where ordernum='".$OrdNo."' and orderstep = 'before' and canceled='N' and paystatus = 'N'";
    $my->exec($cancel_qry);
}

?>
<meta http-equiv='refresh' content="3;url=<?=$path_home?>/?area=<?=$area?>">
<div style="height:200px;text-align:center;padding-top:6em;">
<b><font color="red">결제 도중 취소 하셨습니다.</font></b><br><br>잠시후 메인페이지로 이동합니다.<br><br>페이지 이동이 되지않을경우 <a href="<?=$path_home?>/?area=<?=$area?>">[이곳]</a>을 클릭하세요.
</div>

<? include "../../../bottom.php";  //상단(로고및 메뉴)  ?>