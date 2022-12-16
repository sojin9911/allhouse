<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

$ordernum = $_SESSION["session_ordernum"]; // 주문번호
if(is_login()) $indr = $mem_info;

// --> 비회원 구매를 위한 쿠키 적용여부 파악
cookie_chk();

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
        '26'=>'신한',
        '48'=>'신협',
        '05'=>'외환',
        '20'=>'우리',
        '71'=>'우체국',
        '37'=>'전북',
        '35'=>'제주',
        '81'=>'하나',
        '27'=>'한국씨티',
        '53'=>'씨티',
        '23'=>'SC은행',
        '09'=>'동양증권',
        '78'=>'신한금융투자증권',
        '40'=>'삼성증권',
        '30'=>'미래에셋증권',
        '43'=>'한국투자증권',
        '69'=>'한화증권'
    );


// kcp 처리 ///////////////////////////////////////////////////////////////////////////////
include_once PG_M_DIR."/kcp/cfg/site_conf_inc.php";
include_once PG_M_DIR."/kcp/common/pp_ax_hub_lib.php";

//-------------------------------------------------------------------------
// 01. 지불 요청 정보 설정
//-------------------------------------------------------------------------
$req_tx         = $_POST[ "req_tx"         ]; // 요청 종류
$tran_cd        = $_POST[ "tran_cd"        ]; // 처리 종류

$cust_ip        = getenv( "REMOTE_ADDR"    ); // 요청 IP
$ordr_idxx      = trim($_POST[ "ordr_idxx"      ]); // 쇼핑몰 주문번호
$good_name      = $_POST[ "good_name"      ]; // 상품명
$good_mny       = $_POST[ "good_mny"       ]; // 결제 총금액

//echo("good_mny :".$good_mny."<br>");exit;
//echo("good_name :".$good_name."<br>");

$res_cd         = "";                         // 응답코드
$res_msg        = "";                         // 응답메시지
$tno            = $_POST[ "tno"            ]; // KCP 거래 고유 번호

$buyr_name      = $_POST[ "buyr_name"      ]; // 주문자명
$buyr_tel1      = $_POST[ "buyr_tel1"      ]; // 주문자 전화번호
$buyr_tel2      = $_POST[ "buyr_tel2"      ]; // 주문자 핸드폰 번호
$buyr_mail      = $_POST[ "buyr_mail"      ]; // 주문자 E-mail 주소

$mod_type       = $_POST[ "mod_type"       ]; // 변경TYPE VALUE 승인취소시 필요
$mod_desc       = $_POST[ "mod_desc"       ]; // 변경사유

$use_pay_method = $_POST[ "use_pay_method" ]; // 결제 방법
$bSucc          = "";                         // 업체 DB 처리 성공 여부

$app_time       = "";                         // 승인시간 (모든 결제 수단 공통)
$amount         = "";                         // KCP 실제 거래 금액
$total_amount   = 0;                          // 복합결제시 총 거래금액

$card_cd        = "";                         // 신용카드 코드
$card_name      = "";                         // 신용카드 명
$app_no         = "";                         // 신용카드 승인번호
$noinf          = "";                         // 신용카드 무이자 여부
$quota          = "";                         // 신용카드 할부개월

$bank_name      = "";                         // 은행명
$bank_code      = "";                         // 은행코드

$bankname       = "";                         // 입금할 은행명
$depositor      = "";                         // 입금할 계좌 예금주 성명
$account        = "";                         // 입금할 계좌 번호
$va_date        = "";                         // 가상계좌 입금마감시간

$pnt_issue      = "";                         // 결제 포인트사 코드
$pt_idno        = "";                         // 결제 및 인증 아이디
$pnt_amount     = "";                         // 적립금액 or 사용금액
$pnt_app_time   = "";                         // 승인시간
$pnt_app_no     = "";                         // 승인번호
$add_pnt        = "";                         // 발생 포인트
$use_pnt        = "";                         // 사용가능 포인트
$rsv_pnt        = "";                         // 총 누적 포인트

$commid         = "";                         // 통신사 코드
$mobile_no      = "";                         // 휴대폰 번호

$tk_shop_id     = $_POST[ "tk_shop_id"     ]; // 가맹점 고객 아이디
$tk_van_code    = "";                         // 발급사 코드
$tk_app_no      = "";                         // 상품권 승인 번호

$cash_yn        = $_POST[ "cash_yn"        ]; // 현금영수증 등록 여부
$cash_authno    = '';                         // 현금 영수증 승인 번호
$cash_tr_code   = $_POST[ "cash_tr_code"   ]; // 현금 영수증 발행 구분
$cash_id_info   = $_POST[ "cash_id_info"   ]; // 현금 영수증 등록 번호

/*if ("1" == $row_setup[P_SKBN])
{
		$escw_used      = $_POST[  "escw_used"     ]; // 에스크로 사용 여부
		$pay_mod        = $_POST[  "pay_mod"       ]; // 에스크로 결제처리 모드
		$deli_term      = $_POST[  "deli_term"     ]; // 배송 소요일
		$bask_cntx      = $_POST[  "bask_cntx"     ]; // 장바구니 상품 개수
		$good_info      = $_POST[  "good_info"     ]; // 장바구니 상품 상세 정보
		$rcvr_name      = $_POST[  "rcvr_name"     ]; // 수취인 이름
		$rcvr_tel1      = $_POST[  "rcvr_tel1"     ]; // 수취인 전화번호
		$rcvr_tel2      = $_POST[  "rcvr_tel2"     ]; // 수취인 휴대폰번호
		$rcvr_mail      = $_POST[  "rcvr_mail"     ]; // 수취인 E-Mail
		$rcvr_zipx      = $_POST[  "rcvr_zipx"     ]; // 수취인 우편번호
		$rcvr_add1      = $_POST[  "rcvr_add1"     ]; // 수취인 주소
		$rcvr_add2      = $_POST[  "rcvr_add2"     ]; // 수취인 상세주소
		$escw_yn        = "";                         // 에스크로 여부
}*/

	$escw_used      = $_POST[  "escw_used"     ]; // 에스크로 사용 여부
		$pay_mod        = $_POST[  "pay_mod"       ]; // 에스크로 결제처리 모드
		$deli_term      = $_POST[  "deli_term"     ]; // 배송 소요일
		$bask_cntx      = $_POST[  "bask_cntx"     ]; // 장바구니 상품 개수
		$good_info      = $_POST[  "good_info"     ]; // 장바구니 상품 상세 정보
		$rcvr_name      = $_POST[  "rcvr_name"     ]; // 수취인 이름
		$rcvr_tel1      = $_POST[  "rcvr_tel1"     ]; // 수취인 전화번호
		$rcvr_tel2      = $_POST[  "rcvr_tel2"     ]; // 수취인 휴대폰번호
		$rcvr_mail      = $_POST[  "rcvr_mail"     ]; // 수취인 E-Mail
		$rcvr_zipx      = $_POST[  "rcvr_zipx"     ]; // 수취인 우편번호
		$rcvr_add1      = $_POST[  "rcvr_add1"     ]; // 수취인 주소
		$rcvr_add2      = $_POST[  "rcvr_add2"     ]; // 수취인 상세주소
		$escw_yn        = "";                         // 에스크로 여부

//-------------------------------------------------------------------------
// 02. 인스턴스 생성 및 초기화
//-------------------------------------------------------------------------
$c_PayPlus = new C_PP_CLI;

$c_PayPlus->mf_clear();

//-------------------------------------------------------------------------
// 03-1. 승인 요청
//-------------------------------------------------------------------------

//echo("req_tx : ".$req_tx."<br>");exit;
//echo("good_mny : ".$good_mny."<br>");
//echo("use_pay_method : ".$use_pay_method."<br>");

if ( $req_tx == "pay" )
{
		$c_PayPlus->mf_set_encx_data( $_POST[ "enc_data" ], $_POST[ "enc_info" ] );
}

//-------------------------------------------------------------------------
// 04. 실행
//-------------------------------------------------------------------------

if ( $tran_cd != "" )
{
		$c_PayPlus->mf_do_tx( $trace_no, $g_conf_home_dir, $g_conf_site_cd, $g_conf_site_key, $tran_cd, "", $g_conf_gw_url, $g_conf_gw_port, "payplus_cli_slib", $ordr_idxx, $cust_ip, "3" , 0, 0, $g_conf_key_dir, $g_conf_log_dir); // 응답 전문 처리

		$res_cd  = $c_PayPlus->m_res_cd;  // 결과 코드
		$res_msg = iconv("euckr","utf8",$c_PayPlus->m_res_msg); // 결과 메시지

		//echo("req_tx : ".$req_tx."<BR>");
		//echo("res_cd : ".$res_cd."<BR>");
		//echo("res_msg : ".$res_msg."<BR>");

}
else
{
		$c_PayPlus->m_res_cd  = "9562";
		$c_PayPlus->m_res_msg = "연동 오류|Payplus Plugin이 설치되지 않았거나 tran_cd값이 설정되지 않았습니다.";
}


	$ordernum = $ordr_idxx;

	// - 결제 성공 기록정보 저장 ---
	$keys  = array();
	$keys[] = 'amount';
	$keys[] = 'pnt_issue';
	$keys[] = 'card_cd';
	$keys[] = 'card_name';
	$keys[] = 'app_time';
	$keys[] = 'app_no';

	// 휴대폰 결제일경우 추가 파라미터
	if ( $use_pay_method == "000010000000" ){
		$keys[] = 'van_cd';
		$keys[] = 'van_id';
		$keys[] = 'commid';
		$keys[] = 'mobile_no';
	}

	$app_oc_content = "결과코드||".$c_PayPlus->m_res_cd . "§§" ; // 주문결제기록 정보 이어 붙이기
	$app_oc_content .= "결과메시지||".iconv("euckr","utf8",$c_PayPlus->m_res_msg) . "§§" ; // 주문결제기록 정보 이어 붙이기

	foreach($keys as $name) {
		$app_oc_content .= $name . "||" .iconv("euckr","utf8",$c_PayPlus->mf_get_res_data($name)) . "§§" ;
	}


	// 회원정보 추출
	if(is_login()) $indr = $mem_info;

	// 주문정보 추출
	$r = _MQ("select * from smart_order where o_ordernum='". $ordernum ."' ");


	// - 주문결제기록 저장 ---
	$que = "
		insert smart_order_cardlog set
			 oc_oordernum = '".$ordernum."'
			,oc_tid = '". $c_PayPlus->mf_get_res_data( "tno"       ) ."'
			,oc_content = '". $app_oc_content ."'
			,oc_rdate = now();
	";
	_MQ_noreturn($que);
	// 2017-01-04 ::: 결제성공 이후 동일한 정보가 다시 오는 경우 결제실패처리 하지 않음 - 결제기록 삭제 ::: JJC => 2017-01-06 LDD
	$insert_oc_uid = mysql_insert_id();
	// - 주문결제기록 저장 ---



	// - 결제 성공 기록정보 저장 ---


//-------------------------------------------------------------------------
// 05. 승인 결과 값 추출
//-------------------------------------------------------------------------
if ( $req_tx == "pay" )
{
		if( $res_cd == "0000" )
		{
				$tno       = $c_PayPlus->mf_get_res_data( "tno"       ); // KCP 거래 고유 번호
				$amount    = $c_PayPlus->mf_get_res_data( "amount"    ); // KCP 실제 거래 금액
				$pnt_issue = $c_PayPlus->mf_get_res_data( "pnt_issue" ); // 결제 포인트사 코드

				// 05-1. 신용카드 승인 결과 처리 //////////////////////////////////
				if ( $use_pay_method == "100000000000" )
				{
						$card_cd   = $c_PayPlus->mf_get_res_data( "card_cd"   ); // 카드사 코드
						$card_name = $c_PayPlus->mf_get_res_data( "card_name" ); // 카드 종류
						$app_time  = $c_PayPlus->mf_get_res_data( "app_time"  ); // 승인 시간
						$app_no    = $c_PayPlus->mf_get_res_data( "app_no"    ); // 승인 번호
						$noinf     = $c_PayPlus->mf_get_res_data( "noinf"     ); // 무이자 여부 ( 'Y' : 무이자 )
						$quota     = $c_PayPlus->mf_get_res_data( "quota"     ); // 할부 개월 수
				}

				// 05-2. 계좌이체 승인 결과 처리 //////////////////////////////////
				if ( $use_pay_method == "010000000000" )
				{
						$app_time  = $c_PayPlus->mf_get_res_data( "app_time"   );  // 승인 시간
						$bank_name = $c_PayPlus->mf_get_res_data( "bank_name"  );  // 은행명
						$bank_code = $c_PayPlus->mf_get_res_data( "bank_code"  );  // 은행코드
				}

				// 05-3. 가상계좌 승인 결과 처리 //////////////////////////////////
				if ( $use_pay_method == "001000000000" )
				{
						$bankname  = $c_PayPlus->mf_get_res_data( "bankname"  ); // 입금할 은행 이름
						$bankcode  = $c_PayPlus->mf_get_res_data( "bankcode"  ); // 입금할 은행 코드
						$depositor = $c_PayPlus->mf_get_res_data( "depositor" ); // 입금할 계좌 예금주
						$account   = $c_PayPlus->mf_get_res_data( "account"   ); // 입금할 계좌 번호
						$va_date   = $c_PayPlus->mf_get_res_data( "va_date"   ); // 가상계좌 입금마감시간

						$bankcode = preg_replace( '/[^0-9]/', '', $bankcode );
				}


				// 05-4. 휴대폰 승인 결과 처리 //////////////////////////////////
				if ( $use_pay_method == "000010000000" )
				{
						$van_cd = $c_PayPlus->mf_get_res_data( "van_cd" ); // 결제 건의 결제사 코드가 리턴
						$van_id  = $c_PayPlus->mf_get_res_data( "van_id"  ); // 결제 건의 실물 컨텐츠
						$commid    = $c_PayPlus->mf_get_res_data( "commid"    ); // 결제건의 통신사 코드
						$mobile_no     = $c_PayPlus->mf_get_res_data( "mobile_no"     ); //  결제 건의 휴대폰 번호
				}



				// 05-7. 현금영수증 결과 처리 /////////////////////////////////////
				/*$cash_authno  = $c_PayPlus->mf_get_res_data( "cash_authno"  ); // 현금 영수증 승인 번호
				$cash_yn  = $c_PayPlus->mf_get_res_data( "cash_yn"  ); // 현금 영수증 등록여부
				$cash_id_info  = $c_PayPlus->mf_get_res_data( "cash_id_info"  ); // 현금 영수증 등록번호*/
		}

		/*if ("1" == $row_setup[P_SKBN])  // 에스크로 사용일때만
		{
				$escw_yn = $c_PayPlus->mf_get_res_data( "escw_yn"  ); // 에스크로 여부
		}*/
		$escw_yn = $c_PayPlus->mf_get_res_data( "escw_yn"  ); // 에스크로 여부
}

//-------------------------------------------------------------------------
// 05. 승인 결과 처리 END
//-------------------------------------------------------------------------
if ( $req_tx == "pay" )
{
		if( $res_cd == "0000" )
		{

			if($cash_yn=='Y') {
	            _MQ_noreturn("update smart_order set o_get_tax='Y' where o_ordernum='".$ordernum."'");
	            if($cash_authno) {
            		_MQ_noreturn("insert into smart_order_cashlog (ocs_ordernum,ocs_member,ocs_date,ocs_cashnum,ocs_tid,ocs_amount,ocs_method,ocs_type) values ('$ordernum','$indr[in_id]',now(),'$cash_authno','$amount','AUTH','iche')");

					$op_name = _MQ("
						select p.p_name, count(*) as cnt
						from smart_order_product as op
						inner join smart_product as p on (p.p_code=op.op_pcode)
						where op_oordernum='{$ordernum}'
						group by op_oordernum
					");
					// 현금영수증용 상품명 생성
					$cash_product_name = ($op_name['cnt']>0)?$op_name['p_name'].'외 '.($op_name['cnt']-1).'개':$op_name['p_name'];
					_MQ_noreturn("insert into smart_baro_cashbill (bc_type, bc_ordernum,TradeUsage,IdentityNum,Amount,TradeDate,RegistDT,IssueDT,BarobillState,ItemName,NTSConfirmNum) values
					('pg','$ordernum','1','','".$amount."',curdate(),now(),now(),'3000','".addslashes($cash_product_name)."','$cash_authno')");
        		}
	        }

			if($use_pay_method == '001000000000') { // 가상계좌
				$ool_type = 'R';
				$bank_code_purify = rm_str($bankcode);
				_MQ_noreturn("
					insert into smart_order_onlinelog (
					ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_escrow_code, ool_deposit_tel, ool_bank_owner
					) values (
					'$ordernum', '$r[orderid]', now(), '$tno', '$ool_type', '$app_time', '$amount', '$amount', '$account', '', '$r[ordername]', '$ool_bank_name_array[$bank_code_purify]', '$bankcode', '$escw_yn', '', '$buyr_tel2', '$depositor'
					)
				");

				// 장바구니 정보 삭제
				_MQ_noreturn(" delete from smart_cart where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' and c_direct='Y'  ");

				// 가상계좌 결제 이메일 및 SMS 발송
				include_once OD_PROGRAM_ROOT."/shop.order.mail.send.virtual.php";
			}
			else { // 이외 카드, 계좌이체, 휴대폰 일 시
				// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
				include OD_PROGRAM_ROOT."/shop.order.result.pro.php";
			}
				// 결제완료페이지 이동
			error_loc("/?pn=shop.order.complete");

		}
}

// 2017-01-04 ::: 결제성공 이후 동일한 정보가 다시 오는 경우 결제실패처리 하지 않음. ::: JJC => 2017-01-06 LDD
$oc_res_cnt = _MQ(" select count(*) as cnt from smart_order_cardlog where oc_oordernum = '".$ordernum."' and oc_tid != '' and oc_content like '%결과코드||0000%' ");
if($oc_res_cnt['cnt'] == 1 ) {

	// 결제 실패기록 삭제
	_MQ_noreturn("delete from smart_order_cardlog where oc_uid='". $insert_oc_uid ."' ");
	error_loc("/?pn=shop.order.complete");

}else{
	//최종결제요청 결과 실패 DB처리
	//echo "최종결제요청 결과 실패 DB처리하시기 바랍니다.<br>";
	_MQ_noreturn("update smart_order set o_status='결제실패' where o_ordernum='". $ordernum ."' ");
	error_loc_msg("/?pn=shop.order.result" , "결제에 실패하였습니다. 다시한번 확인 바랍니다.");

}




actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행