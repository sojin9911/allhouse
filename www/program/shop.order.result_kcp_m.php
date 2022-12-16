<?
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
include PG_M_DIR."/kcp/cfg/site_conf_inc.php";       // 환경설정 파일 include

$r = $row; // 주문정보 데이터  변수 연동

$tablet_size      = "1.0"; // 화면 사이즈 조정 - 기기화면에 맞게 수정(갤럭시탭,아이패드 - 1.85, 스마트폰 - 1.0)

// 사이트 도메인에 대한 전체 URL
$siteDomain = $system['url'];

// 일반 할부기간
$arr_installment_peroid = array();
$siteInfo['s_pg_installment_peroid']; // 할부개월수

    //데이터정의
    if($r[o_paymethod] == "card")     { $ActionResult = "card";  $ags_paymethod ="CARD";  $use_pay_method = "100000000000"; }
    else if($r[o_paymethod]=='iche')  { $ActionResult = "acnt";  $ags_paymethod ="BANK";  $use_pay_method = "010000000000"; }
    else if($r[o_paymethod]=='virtual') { $ActionResult = "vcnt";  $ags_paymethod ="VCNT";  $use_pay_method = "0010000000000"; }
    else if($r[o_paymethod]=='hpp') { $ActionResult = "mobx";  $ags_paymethod ="MOBX";  $use_pay_method = "000010000000"; } // 휴대폰 결제
    else { $ActionResult = "";      $ags_paymethod ="";      $use_pay_method = ""; }
    //main_pname
?>

<!-- 거래등록 하는 kcp 서버와 통신을 위한 스크립트-->
<script type="text/javascript" src="<?php echo $siteDomain ?><?php echo OD_PROGRAM_DIR; ?>/shop.order.result_kcp_approval.js"></script>

<form name="sm_form" method="POST" style="display:none;">
<input type="hidden" name="encoding_trans" value="UTF-8" />
<input type='hidden' name='PayUrl' >
<input type="text" name='good_name' maxlength="100" value='<?=$app_product_name?>'> <!--good_name(상품명)-->
<input type="text" name='good_mny' size="9" maxlength="9" value='<?=$r[o_price_real]?>' ><!--good_mny(상품금액)-->
<input type="text" name='buyr_name' size="20" maxlength="20" value="<?=$r[o_oname]?>"><!--buyr_name(주문자이름)-->
<input type="text" name='buyr_tel1' size="20" maxlength="20" value='<?=$r[o_otel]?>'><!--buyr_tel1(주문자 연락처)-->
<input type="text" name='buyr_tel2' size="20" maxlength="20" value='<?=$r[o_ohp]?>'><!--buyr_tel2(주문자 핸드폰 번호)-->
<input type="text" name='buyr_mail' size="20" maxlength="30" value='<?=$r[o_oemail]?>'><!--buyr_mail(주문자 E-mail)-->

<input type="text" name="ipgm_date" value="<?=date('Ymd', time() + ($siteInfo[s_pg_virtual_date] * 86400))?>"/>
<input type="hidden" name="used_bank" value="BK05:BK03:BK04:BK07:BK11:BK23:BK26:BK32:BK34:BK81:BK71"/>

<!-- 필수 사항 -->
<!-- 요청 구분 -->
<input type='hidden' name='req_tx'       value='pay'>
<!-- 사이트 코드 -->
<input type="hidden" name='site_cd'      value="<?=$g_conf_site_cd?>">
<!-- 사이트 키 -->
<input type='hidden' name='site_key'     value='<?=$g_conf_site_key?>'>
 <!-- 사이트 이름 --> 
<input type="hidden" name='shop_name'    value="<?=$g_conf_site_name?>">
<!-- 결제수단-->
<input type="hidden" name='pay_method'   value="<?=$ags_paymethod?>">
<!-- 주문번호 -->
<input type="hidden"   name='ordr_idxx'    value="<?=$ordernum?>">
<!-- 최대 할부개월수 -->
<input type="hidden" name='quotaopt'     value="<?=$siteInfo['s_pg_installment_peroid']?>">
<!-- 통화 코드 -->
<input type="hidden" name='currency'     value="410">
<!-- 결제등록 키 -->
<input type="hidden" name='approval_key' id="approval">
<!-- 리턴 URL (kcp와 통신후 결제를 요청할 수 있는 암호화 데이터를 전송 받을 가맹점의 주문페이지 URL) -->
<!-- 반드시 가맹점 주문페이지의 URL을 입력 해주시기 바랍니다. -->
<input type="hidden" name='Ret_URL'      value="<?php echo $siteDomain ?>/?pn=shop.order.result">

<!-- 인증시 필요한 파라미터(변경불가)-->
<input type='hidden' name='ActionResult' value='<?=$ActionResult?>'> 
<!-- 인증시 필요한 파라미터(변경불가)-->
<input type="hidden" name='escw_used'    value="N">
<!-- 기타 파라메터 추가 부분 - Start - -->
<input type="hidden" name='param_opt_1'	 value="<?=$param_opt_1?>"/>
<input type="hidden" name='param_opt_2'	 value="<?=$param_opt_2?>"/>
<input type="hidden" name='param_opt_3'	 value="<?=$param_opt_3?>"/>
<!-- 기타 파라메터 추가 부분 - End - -->
<!-- 화면 크기조정 부분 - Start - -->
<input type="text" name='tablet_size'	 value="<?=$tablet_size?>"/>
<!-- 화면 크기조정 부분 - End - -->

<?php // 현금영수증 ?>
<input type="hidden" name="disp_tax_yn"     value="<?php echo $siteInfo['s_cash_receipt_use']; ?>"/>

<?php 
	// 2017-06-16 ::: 부가세율설정 ::: JJC
	// 면세가
	if($app_vat_N > 0 ) {
		echo '
			<input type="hidden" name="tax_flag"          value="TG03">     <!-- 변경불가    -->
			<input type="hidden" name="comm_tax_mny"      value="'. $app_vat_Y_tot .'">         <!-- 과세금액 = (과세전체 - 부과세)    -->
			<input type="hidden" name="comm_vat_mny"      value="'. $app_vat_Y_vat .'">         <!-- 부가세      -->
			<input type="hidden" name="comm_free_mny"     value="'. $app_vat_N .'">         <!-- 비과세 금액 -->
		';
	}
?>

<!--
	사용 카드 설정
	<input type="hidden" name='used_card'    value="CClg:ccDI">
    /*  무이자 옵션
            ※ 설정할부    (가맹점 관리자 페이지에 설정 된 무이자 설정을 따른다)                             - "" 로 설정
            ※ 일반할부    (KCP 이벤트 이외에 설정 된 모든 무이자 설정을 무시한다)                           - "N" 로 설정
            ※ 무이자 할부 (가맹점 관리자 페이지에 설정 된 무이자 이벤트 중 원하는 무이자 설정을 세팅한다)   - "Y" 로 설정
    <input type="hidden" name="kcp_noint"       value=""/> */

    /*  무이자 설정
            ※ 주의 1 : 할부는 결제금액이 50,000 원 이상일 경우에만 가능
            ※ 주의 2 : 무이자 설정값은 무이자 옵션이 Y일 경우에만 결제 창에 적용
            예) 전 카드 2,3,6개월 무이자(국민,비씨,엘지,삼성,신한,현대,롯데,외환) : ALL-02:03:04
            BC 2,3,6개월, 국민 3,6개월, 삼성 6,9개월 무이자 : CCBC-02:03:06,CCKM-03:06,CCSS-03:06:04
    <input type="hidden" name="kcp_noint_quota" value="CCBC-02:03:06,CCKM-03:06,CCSS-03:06:09"/> */
-->
</form>



<script language="javascript">	
/* kcp web 결제창 호출 (변경불가)*/
function chk_pay()
{
  self.name = "tar_opener";
}
function call_pay_form()
{

		var v_frm = document.sm_form;
    v_frm.target = "";
    v_frm.action = PayUrl;

    if(v_frm.encoding_trans == undefined)
    {
        v_frm.action = PayUrl;
    }
    else
    {
        if(v_frm.encoding_trans.value == "UTF-8")
        {
            v_frm.action = PayUrl.substring(0,PayUrl.lastIndexOf("/")) + "/jsp/encodingFilter/encodingFilter.jsp";
            v_frm.PayUrl.value = PayUrl;
        }
        else
        {
            v_frm.action = PayUrl;
        }
    }

    if(v_frm.Ret_URL.value == "")
    {
        /* Ret_URL값은 현 페이지의 URL 입니다. */
        alert("연동시 Ret_URL을 반드시 설정하셔야 됩니다.");
        return false;
    }
    else
    {
        v_frm.submit();
    }

    v_frm.submit();
}
</script>

<?php actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행 ?>