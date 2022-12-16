<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
$ordernum = $_SESSION["session_ordernum"];//주문번호





// --> 비회원 구매를 위한 쿠키 적용여부 파악
cookie_chk();


/*
 * [최종결제요청 페이지(STEP2-2)]
 *
 * 매뉴얼 "5.1. XPay 결제 요청 페이지 개발"의 "단계 5. 최종 결제 요청 및 요청 결과 처리" 참조
 *
 * LG유플러스으로 부터 내려받은 LGD_PAYKEY(인증Key)를 가지고 최종 결제요청.(파라미터 전달시 POST를 사용하세요)
 */
	$configPath = PG_DIR . "/lgpay/lgdacom";

/*
 *************************************************
 * 1.최종결제 요청 - BEGIN
 *  (단, 최종 금액체크를 원하시는 경우 금액체크 부분 주석을 제거 하시면 됩니다.)
 *************************************************
 */
$CST_PLATFORM               = $_POST["CST_PLATFORM"];
$CST_MID                    = $_POST["CST_MID"];
$LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
$LGD_PAYKEY                 = $_POST["LGD_PAYKEY"];


require_once(PG_DIR."/lgpay/lgdacom/XPayClient.php");
// (1) XpayClient의 사용을 위한 xpay 객체 생성
// (2) Init: XPayClient 초기화(환경설정 파일 로드)
// configPath: 설정파일
// CST_PLATFORM: - test, service 값에 따라 lgdacom.conf의 test_url(test) 또는 url(srvice) 사용
//				- test, service 값에 따라 테스트용 또는 서비스용 아이디 생성
$xpay = &new XPayClient($configPath, $CST_PLATFORM);

// (3) Init_TX: 메모리에 mall.conf, lgdacom.conf 할당 및 트랜잭션의 고유한 키 TXID 생성
$xpay->Init_TX($LGD_MID);
$xpay->Set("LGD_TXNAME", "PaymentByKey");
$xpay->Set("LGD_PAYKEY", $LGD_PAYKEY);

//금액을 체크하시기 원하는 경우 아래 주석을 풀어서 이용하십시요.
//$DB_AMOUNT = "DB나 세션에서 가져온 금액"; //반드시 위변조가 불가능한 곳(DB나 세션)에서 금액을 가져오십시요.
//$xpay->Set("LGD_AMOUNTCHECKYN", "Y");
//$xpay->Set("LGD_AMOUNT", $DB_AMOUNT);

/*
 *************************************************
 * 1.최종결제 요청(수정하지 마세요) - END
 *************************************************
 */

/*
 * 2. 최종결제 요청 결과처리
 *
 * 최종 결제요청 결과 리턴 파라미터는 연동메뉴얼을 참고하시기 바랍니다.
 */
	// (4) TX: lgdacom.conf에 설정된 URL로 소켓 통신하여 최종 인증요청, 결과값으로 true, false 리턴
if ($xpay->TX()) {

	$ordernum = $xpay->Response("LGD_OID",0);

	// -- 2016-12-15 LCY :: 결제요청성공이더라도 결제가 실패할 시 ordernum 이 없을 수 있으므로 따로 처리
		$ordernum = trim($ordernum) == '' ? $_SESSION["session_ordernum"]:$ordernum;

		// - 결제 성공 기록정보 저장 ---
		$keys = $xpay->Response_Names();
		$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
		foreach($keys as $name) {
			$app_oc_content .= $name . "||" .$xpay->Response($name, 0) . "§§" ;
		}

		// 회원정보 추출
		if(is_login()) $indr = $mem_info;

		// 주문정보 추출
		$r = _MQ("select * from smart_order where o_ordernum='". $ordernum ."' ");


		// - 주문결제기록 저장 ---
		$que = "
			insert smart_order_cardlog set
				 oc_oordernum = '".$ordernum."'
				,oc_tid = '". $xpay->Response("LGD_TID",0) ."'
				,oc_content = '". $app_oc_content ."'
				,oc_rdate = now();
		";
		_MQ_noreturn($que);
		// - 주문결제기록 저장 ---
		$insert_oc_uid = mysql_insert_id();// 2017-01-04 ::: 결제기록 고유번호 저장. ::: JJC
		// - 결제 성공 기록정보 저장 ---

  if( "0000" == $xpay->Response_Code() ) {
    // -- 최종결제요청 결과 성공 DB처리 ---
			if($xpay->Response("LGD_CASHRECEIPTCODE",0)=='0000') { // 현금영수증을 신청했으면 DB 업데이트
                _MQ_noreturn("update smart_order set o_get_tax = 'Y' where o_ordernum = '$ordernum'");

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
					('pg','$ordernum','1','','".$xpay->Response("LGD_AMOUNT",0)."',curdate(),now(),now(),'3000','".addslashes($cash_product_name)."','".$xpay->Response("LGD_CASHRECEIPTNUM",0)."')");
            }

    		if($xpay->Response("LGD_PAYTYPE",0)=='SC0010') {
				// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
				include OD_PROGRAM_ROOT."/shop.order.result.pro.php";
				error_loc("/?pn=shop.order.complete","top");
			} else if($xpay->Response("LGD_PAYTYPE",0)=='SC0040') { // 가상계좌 일때
				$ool_type = 'R';
				$tno = $xpay->Response("LGD_TID",0);
				$app_time = $xpay->Response("LGD_PAYDATE",0);
				$amount = $xpay->Response("LGD_CASTAMOUNT",0);
				$account = $xpay->Response("LGD_ACCOUNTNUM",0);
				$bankname = $xpay->Response("LGD_FINANCENAME",0);
				$bankcode = $xpay->Response("LGD_FINANCECODE",0);
				$escw_yn = 'Y';
				$buyr_tel2 = $xpay->Response("LGD_BUYERPHONE",0);
				$depositor = $xpay->Response("LGD_BUYER",0);
				$payer = $xpay->Response("LGD_PAYER",0);
				_MQ_noreturn("
					insert into smart_order_onlinelog (
					ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_escrow_code, ool_deposit_tel, ool_bank_owner
					) values (
					'$ordernum', '$indr[in_id]', now(), '$tno', '$ool_type', '$app_time', '$amount', '$amount', '$account', '', '$payer', '$bankname', '$bankcode', '$escw_yn', '', '$buyr_tel2', '$depositor'
					)
				");

				// 장바구니 정보 삭제
				_MQ_noreturn(" delete from smart_cart where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' and c_direct='Y'  ");

				// 가상계좌 결제 이메일 및 SMS 발송
				include_once OD_PROGRAM_ROOT."/shop.order.mail.send.virtual.php";
				error_loc("/?pn=shop.order.complete","top");

			}else if($xpay->Response("LGD_PAYTYPE",0)=='SC0030') { // 실시간 계좌이체 일때

			      $_authum = $xpay->Response("LGD_FINANCEAUTHNUM",0);

				// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
				include OD_PROGRAM_ROOT."/shop.order.result.pro.php";
				error_loc("/?pn=shop.order.complete","top");

			}else if($xpay->Response("LGD_PAYTYPE",0)=='SC0060') { // 휴대폰결제일시

				// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
				include OD_PROGRAM_ROOT."/shop.order.result.pro.php";
				error_loc("/?pn=shop.order.complete","top");

			}else {
				// 결제완료페이지 이동
				error_loc("/?pn=shop.order.complete","top");
			}
  }else{

		//최종결제요청 결과 실패 DB처리
		//echo "최종결제요청 결과 실패 DB처리하시기 바랍니다.<br>";
		// 2017-01-04 ::: 결제성공 이후 동일한 정보가 다시 오는 경우 결제실패처리 하지 않음. ::: JJC
		$oc_res_cnt = _MQ(" select count(*) as cnt from smart_order_cardlog where oc_oordernum = '".$ordernum."' and oc_tid = '". $xpay->Response("LGD_TID",0) ."' and oc_content like '%LGD_RESPCODE||0000%' ");
		if($oc_res_cnt['cnt'] == 1 ) {

			// 결제 실패기록 삭제
			_MQ_noreturn("delete from smart_order_cardlog where oc_uid='". $insert_oc_uid ."' ");

			// 결제완료페이지 이동
			error_loc("/?pn=shop.order.complete",'top');

		}

		// 결제실패 처리
		else {

			_MQ_noreturn("update smart_order set o_status='결제실패' where o_ordernum='". $ordernum ."' ");
			error_loc_msg("/?pn=shop.order.result" , "결제에 실패하였습니다. 다시한번 확인 바랍니다.");

		}
		// 2017-01-04 ::: 결제성공 이후 동일한 정보가 다시 오는 경우 결제실패처리 하지 않음. ::: JJC

  }
}else{ // -- 잘못된요청 최종처리
		//2)API 요청실패 화면처리
		//echo "결제요청이 실패하였습니다.  <br>";
		//echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
		//echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";

		// - 주문결제기록 저장 ---
		$app_oc_content = "LGD_RESPMSG||" . $xpay->Response_Msg() . "§§"; // 주문결제기록 정보 이어 붙이기

		$que = "
			insert smart_order_cardlog set
				 oc_oordernum = '". $ordernum ."'
				,oc_tid = ''
				,oc_content = '". $app_oc_content ."'
				,oc_rdate = now();
		";
		_MQ_noreturn($que);
		// - 주문결제기록 저장 ---
		// - 결제 성공 기록정보 저장 ---


		//최종결제요청 결과 실패 DB처리
		//echo "최종결제요청 결과 실패 DB처리하시기 바랍니다.<br>";
		//_MQ_noreturn("update smart_order set o_status='결제실패' where o_ordernum='". $ordernum ."' ");
		//error_loc_msg("/?pn=shop.order.result" , "결제에 실패하였습니다. 다시한번 확인 바랍니다.");
}






actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행