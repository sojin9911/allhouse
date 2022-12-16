<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
$ordernum = $_SESSION["session_ordernum"];//주문번호




/*
 * [상점 결제결과처리(DB) 페이지]
 *
 * 1) 위변조 방지를 위한 hashdata값 검증은 반드시 적용하셔야 합니다.
 *
 */
$LGD_RESPCODE            = $HTTP_POST_VARS["LGD_RESPCODE"];             // 응답코드: 0000(성공) 그외 실패
$LGD_RESPMSG             = iconv('euc-kr','utf-8',$HTTP_POST_VARS["LGD_RESPMSG"]);              // 응답메세지
$LGD_MID                 = $HTTP_POST_VARS["LGD_MID"];                  // 상점아이디
$LGD_OID                 = $HTTP_POST_VARS["LGD_OID"];                  // 주문번호
$LGD_AMOUNT              = $HTTP_POST_VARS["LGD_AMOUNT"];               // 거래금액
$LGD_TID                 = $HTTP_POST_VARS["LGD_TID"];                  // LG유플러스에서 부여한 거래번호
$LGD_PAYTYPE             = $HTTP_POST_VARS["LGD_PAYTYPE"];              // 결제수단코드
$LGD_PAYDATE             = $HTTP_POST_VARS["LGD_PAYDATE"];              // 거래일시(승인일시/이체일시)
$LGD_HASHDATA            = $HTTP_POST_VARS["LGD_HASHDATA"];             // 해쉬값
$LGD_FINANCECODE         = $HTTP_POST_VARS["LGD_FINANCECODE"];          // 결제기관코드(은행코드)
$LGD_FINANCENAME         = iconv('euc-kr','utf-8',$HTTP_POST_VARS["LGD_FINANCENAME"]);          // 결제기관이름(은행이름)
$LGD_TIMESTAMP           = $HTTP_POST_VARS["LGD_TIMESTAMP"];            // 타임스탬프
$LGD_ACCOUNTNUM          = $HTTP_POST_VARS["LGD_ACCOUNTNUM"];           // 계좌번호(무통장입금)
//$LGD_CASTAMOUNT          = $HTTP_POST_VARS["LGD_CASTAMOUNT"];           // 입금총액(무통장입금)
//$LGD_CASCAMOUNT          = $HTTP_POST_VARS["LGD_CASCAMOUNT"];           // 현입금액(무통장입금)
$LGD_AMOUNT              = $HTTP_POST_VARS["LGD_AMOUNT"];           // 입금액(무통장입금)
$LGD_CASFLAG             = $HTTP_POST_VARS["LGD_CASFLAG"];              // 무통장입금 플래그(무통장입금) - 'R':계좌할당, 'I':입금, 'C':입금취소
$LGD_CASSEQNO            = $HTTP_POST_VARS["LGD_CASSEQNO"];             // 입금순서(무통장입금)
$LGD_CASHRECEIPTNUM      = $HTTP_POST_VARS["LGD_CASHRECEIPTNUM"];       // 현금영수증 승인번호
$LGD_CASHRECEIPTSELFYN   = $HTTP_POST_VARS["LGD_CASHRECEIPTSELFYN"];    // 현금영수증자진발급제유무 Y: 자진발급제 적용, 그외 : 미적용
$LGD_CASHRECEIPTKIND     = $HTTP_POST_VARS["LGD_CASHRECEIPTKIND"];      // 현금영수증 종류 0: 소득공제용 , 1: 지출증빙용
$LGD_PAYER     			 = iconv('euc-kr','utf-8',$HTTP_POST_VARS["LGD_PAYER"]);      			// 입금자명
$LGD_TELNO               = $HTTP_POST_VARS["LGD_TELNO"];                // 입금자 휴대폰번호
$LGD_ACCOUNTOWNER        = $HTTP_POST_VARS["LGD_ACCOUNTOWNER"];                // 예금주명


$LGD_ESCROWYN            = $HTTP_POST_VARS["LGD_ESCROWYN"];             // 에스크로 적용여부


/*
 * 구매정보
 */
$LGD_BUYER               = iconv('euc-kr','utf-8',$HTTP_POST_VARS["LGD_BUYER"]);                // 구매자
$LGD_PRODUCTINFO         = iconv('euc-kr','utf-8',$HTTP_POST_VARS["LGD_PRODUCTINFO"]);          // 상품명
$LGD_BUYERID             = $HTTP_POST_VARS["LGD_BUYERID"];              // 구매자 ID
$LGD_BUYERADDRESS        = iconv('euc-kr','utf-8',$HTTP_POST_VARS["LGD_BUYERADDRESS"]);         // 구매자 주소
$LGD_BUYERPHONE          = $HTTP_POST_VARS["LGD_BUYERPHONE"];           // 구매자 전화번호
$LGD_BUYEREMAIL          = $HTTP_POST_VARS["LGD_BUYEREMAIL"];           // 구매자 이메일
$LGD_BUYERSSN            = $HTTP_POST_VARS["LGD_BUYERSSN"];             // 구매자 주민번호
$LGD_PRODUCTCODE         = $HTTP_POST_VARS["LGD_PRODUCTCODE"];          // 상품코드
$LGD_RECEIVER            = iconv('euc-kr','utf-8',$HTTP_POST_VARS["LGD_RECEIVER"]);             // 수취인
$LGD_RECEIVERPHONE       = $HTTP_POST_VARS["LGD_RECEIVERPHONE"];        // 수취인 전화번호
$LGD_DELIVERYINFO        = iconv('euc-kr','utf-8',$HTTP_POST_VARS["LGD_DELIVERYINFO"]);         // 배송지

$LGD_MERTKEY = $siteInfo[s_pg_key];  //LG유플러스에서 발급한 상점키로 변경해 주시기 바랍니다.

$LGD_HASHDATA2 = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_RESPCODE.$LGD_TIMESTAMP.$LGD_MERTKEY);

/*
 * 상점 처리결과 리턴메세지
 *
 * OK  : 상점 처리결과 성공
 * 그외 : 상점 처리결과 실패
 *
 * ※ 주의사항 : 성공시 'OK' 문자이외의 다른문자열이 포함되면 실패처리 되오니 주의하시기 바랍니다.
 */
$resultMSG = "결제결과 상점 DB처리(LGD_CASNOTEURL) 결과값을 입력해 주시기 바랍니다.";

$order_no =  $LGD_OID;

if ( $LGD_HASHDATA2 == $LGD_HASHDATA ) { //해쉬값 검증이 성공이면
    if ( "0000" == $LGD_RESPCODE ){ //결제가 성공이면
    	if( "R" == $LGD_CASFLAG ) {
            /*
             * 무통장 할당 성공 결과 상점 처리(DB) 부분
             * 상점 결과 처리가 정상이면 "OK"
             */

            //_MQ_noreturn("update smart_order set o_status='결제대기' where o_ordernum='$LGD_OID'");


            //if( 무통장 할당 성공 상점처리결과 성공 )
            $resultMSG = "OK";
    	}else if( "I" == $LGD_CASFLAG ) {
	            /*
	         * 무통장 입금 성공 결과 상점 처리(DB) 부분
    	     * 상점 결과 처리가 정상이면 "OK"
        	 */
	            _MQ_noreturn("
				insert into smart_order_onlinelog (
					ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_deposit_tel, ool_bank_owner
				) values (
					'$LGD_OID', '$LGD_BUYERID', now(), '$LGD_TID', '$LGD_CASFLAG', '$LGD_PAYDATE', '$LGD_AMOUNT', '$LGD_AMOUNT', '$LGD_ACCOUNTNUM', '$LGD_CASSEQNO', '$LGD_PAYER', '$LGD_FINANCENAME', '$LGD_FINANCECODE', '$LGD_ESCROWYN', '$LGD_TELNO', '$LGD_ACCOUNTOWNER'
				)
            ");

            $r = _MQ("select * from smart_order_onlinelog as ol inner join smart_order as o on (o.o_ordernum=ol.ool_ordernum) where ol.ool_ordernum='$LGD_OID' order by ol.ool_uid desc limit 1");

			// - 2016-09-05 ::: JJC ::: 주문정보 추출 ::: 가상계좌 - 이미 결제가 되었다면 추가 적용을 하지 않게 처리함. ---
			$iosr = get_order_info($order_no);

            if($r[ool_amount_total] == $r[ool_amount_current] && $iosr['o_paystatus'] <> "Y"  ) { // 전액 입금되었다면 진행

                if($LGD_CASHRECEIPTNUM&&$r[o_get_tax]=='Y') { // 현금영수증을 신청했고, 승인번호가 발급되었을 경우 DB에 등록
                    _MQ_noreturn("
                        insert into smart_order_cashlog (
                            ocs_ordernum, ocs_member, ocs_date, ocs_tid, ocs_cashnum, ocs_respdate, ocs_msg, ocs_method, ocs_cardnum, ocs_amount, ocs_type, ocs_seqno
                        ) values (
                            '$LGD_OID', '$LGD_BUYERID', now(), '$LGD_TID', '$LGD_CASHRECEIPTNUM', '$LGD_PAYDATE', '', 'AUTH', '', '$LGD_AMOUNT', '$ocs_type[$LGD_PAYTYPE]', ''
                        )
                    ");

					$op_name = _MQ("
						select p.p_name, count(*) as cnt
						from smart_order_product as op
						inner join smart_product as p on (p.p_code=op.op_pcode)
						where op_oordernum='{$order_no}'
						group by op_oordernum
					");
					// 현금영수증용 상품명 생성
					$cash_product_name = ($op_name['cnt']>0)?$op_name['p_name'].'외 '.($op_name['cnt']-1).'개':$op_name['p_name'];
					_MQ_noreturn("insert into smart_baro_cashbill (bc_type, bc_ordernum,TradeUsage,IdentityNum,Amount,TradeDate,RegistDT,IssueDT,BarobillState,ItemName,NTSConfirmNum) values
					('pg','$order_no','1','','".$LGD_AMOUNT."',curdate(),now(),now(),'3000','".addslashes($cash_product_name)."','$LGD_CASHRECEIPTNUM')");
                }

            	// ----- SSJ : 2020-07-01 : 결제완료/결제취소 일괄처리 -----
				// 공통결제
				//		넘길변수
				//			-> 주문번호 : $ordernum
				$ordernum = $LGD_OID;
				include(OD_PROGRAM_ROOT."/shop.order.result.pro.php"); // ----- SSJ : 2020-07-01 : 결제완료/결제취소 일괄처리 -----
				//if($pay_status == 'N') {echo "FAIL";}// 실패처리
				// ----- SSJ : 2020-07-01 : 결제완료/결제취소 일괄처리 -----
            }
        	//if( 무통장 입금 성공 상점처리결과 성공 )
        	$resultMSG = ($pay_status == 'N' ? "FAIL" : "OK");// ----- SSJ : 2020-07-01 : 결제완료/결제취소 일괄처리 -----
    	}else if( "C" == $LGD_CASFLAG ) {
	            /*
	         * 무통장 입금취소 성공 결과 상점 처리(DB) 부분
    	     * 상점 결과 처리가 정상이면 "OK"
        	 */
            $CST_PLATFORM = $siteInfo[s_pg_mode];
            $CST_MID = $siteInfo[s_pg_code];
            $LGD_MID = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
            $configPath = PG_DIR . "/lgpay/lgdacom";
            require_once(PG_DIR."/lgpay/lgdacom/XPayClient.php");
            $xpay = &new XPayClient($configPath, $CST_PLATFORM);
            $xpay->Init_TX($LGD_MID);
            $xpay->Set("LGD_TXNAME", "CashReceipt");
            $xpay->Set("LGD_METHOD", 'CANCEL');
            $xpay->Set("LGD_PAYTYPE", $LGD_PAYTYPE);
            $xpay->Set("LGD_ENCODING", 'UTF-8');
            $xpay->Set("LGD_ENCODING_NOTEURL", 'UTF-8');
            $xpay->Set("LGD_ENCODING_RETURNURL", 'UTF-8');

            if ($xpay->TX()) {

            $ocs_cashnum = $xpay->Response("LGD_CASHRECEIPTNUM",0);
            $ocs_respdate = $xpay->Response("LGD_RESPDATE",0);
            $ocs_seqno = $xpay->Response("LGD_SEQNO",0);
            $ocs_msg = $xpay->Response_Msg();

            }

            $cash = _MQ("select * from smart_order_cashlog where ocs_ordernum='$LGD_OID' order by ocs_uid desc limit 1");

            _MQ_noreturn("
                insert into smart_order_cashlog (
                    ocs_ordernum, ocs_member, ocs_date, ocs_tid, ocs_cashnum, ocs_respdate, ocs_msg, ocs_method, ocs_cardnum, ocs_amount, ocs_type, ocs_seqno
                ) values (
                    '$LGD_OID', '$LGD_BUYERID', now(), '$LGD_TID', '$ocs_cashnum', '$ocs_respdate', '$ocs_msg', 'CANCEL', '$cash[ocs_cardnum]', '$cash[ocs_amount]', '$LGD_PAYTYPE', '$ocs_seqno'
                )
            ");

            _MQ_noreturn("
                insert into smart_order_onlinelog (
                    ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_deposit_tel, ool_bank_owner
                ) values (
                    '$LGD_OID', '$LGD_BUYERID', now(), '$LGD_TID', '$LGD_CASFLAG', '$LGD_PAYDATE', '$LGD_CASCAMOUNT', '$LGD_CASTAMOUNT', '$LGD_ACCOUNTNUM', '$LGD_CASSEQNO', '$LGD_PAYER', '$LGD_FINANCENAME', '$LGD_FINANCECODE', '$LGD_ESCROWYN', '$LGD_TELNO', '$LGD_ACCOUNTOWNER'
                )
            ");

        	//if( 무통장 입금취소 성공 상점처리결과 성공 )
        	$resultMSG = "OK";
    	}
    } else { //결제가 실패이면
        /*
         * 거래실패 결과 상점 처리(DB) 부분
         * 상점결과 처리가 정상이면 "OK"
         */
        //if( 결제실패 상점처리결과 성공 )
        $resultMSG = "OK";
    }
} else { //해쉬값이 검증이 실패이면

     // hashdata검증 실패 로그를 처리하시기 바랍니다.

    $resultMSG = "결제결과 상점 DB처리(LGD_CASNOTEURL) 해쉬값 검증이 실패하였습니다.";
}



echo $resultMSG;






actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행