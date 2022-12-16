<?php

$order = _MQ("select * from smart_order as o left join smart_order_cardlog as oc on (o.o_ordernum = oc.oc_oordernum) where o.o_ordernum = '$ordernum'");




// 2017-06-16 ::: 부가세율설정 - 배송비 과세 / 면세 비용 계산 ::: JJC
//$ordernum = $ordernum; // --> 주문번호 
$order_row = $order; // --> 주문배열정보

include(dirname(__FILE__)."/shop.order.result.vat_calc.php");
$amount = $order_row['o_price_real'];
// 2017-06-16 ::: 부가세율설정 - 배송비 과세 / 면세 비용 계산 ::: JJC



if($order[o_get_tax]=='Y') { // 현금영수증 신청이 된 상태면 PG사 연동

	/******* LG U+ ***************/
	if($siteInfo[s_pg_type]=='lgpay') {
		
		$CST_PLATFORM               = $siteInfo[s_pg_mode];
		$CST_MID                    = $siteInfo[s_pg_code];
		$LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
		$configPath 				= PG_DIR . "/lgpay/lgdacom";
		require_once(PG_DIR."/lgpay/lgdacom/XPayClient.php");

		$paytype_array = array(
			'virtual'	=>	'SC0040',
			'online'	=>	'SC0100',
			'iche'		=>	'SC0030'
		);
		$paytype_array_flip = array_flip($paytype_array);

		$LGD_METHOD = $method;
		$LGD_PAYTYPE = $paytype_array[$paymethod];
		$LGD_OID = $ordernum;
		$LGD_TID = $tid;
		$LGD_AMOUNT = $amount;
		$LGD_CASHCARDNUM = $num;
		$LGD_CASHRECEIPTUSE = $use;
		$LGD_PRODUCTINFO = $product;
		$LGD_CUSTOM_BUSINESSNUM = $store;
		
		$xpay = &new XPayClient($configPath, $CST_PLATFORM);
		$xpay->Init_TX($LGD_MID);
		$xpay->Set("LGD_TXNAME", "CashReceipt");
		$xpay->Set("LGD_METHOD", $LGD_METHOD);
		$xpay->Set("LGD_PAYTYPE", $LGD_PAYTYPE);
		$xpay->Set("LGD_ENCODING", $LGD_ENCODING);
		$xpay->Set("LGD_ENCODING_NOTEURL", $LGD_ENCODING_NOTEURL);
		$xpay->Set("LGD_ENCODING_RETURNURL", $LGD_ENCODING_RETURNURL);

		if ($LGD_METHOD == "AUTH"){ // 현금영수증 발행 요청이면 - 요청 = AUTH, 취소 = CANCEL
			$xpay->Set("LGD_OID", $LGD_OID);
			$xpay->Set("LGD_AMOUNT", $LGD_AMOUNT);
			$xpay->Set("LGD_CASHCARDNUM", $LGD_CASHCARDNUM);
			$xpay->Set("LGD_CUSTOM_MERTNAME", $LGD_CUSTOM_MERTNAME);
			$xpay->Set("LGD_CUSTOM_BUSINESSNUM", $LGD_CUSTOM_BUSINESSNUM);
			$xpay->Set("LGD_CUSTOM_MERTPHONE", $LGD_CUSTOM_MERTPHONE);
			$xpay->Set("LGD_CASHRECEIPTUSE", $LGD_CASHRECEIPTUSE);

			if ($LGD_PAYTYPE == "SC0030"){ $xpay->Set("LGD_TID", $LGD_TID); } //기결제된 계좌이체건 현금영수증 발행요청시 필수 
			else if ($LGD_PAYTYPE == "SC0040"){ $xpay->Set("LGD_TID", $LGD_TID); $xpay->Set("LGD_SEQNO", "001"); } //기결제된 가상계좌건 현금영수증 발행요청시 필수 
			else { $xpay->Set("LGD_PRODUCTINFO", $LGD_PRODUCTINFO); $xpay->Set("LGD_SEQNO", "001"); } //무통장입금 단독건 발행요청
		} else { 
			$xpay->Set("LGD_TID", $LGD_TID); // 현금영수증 취소 요청 
			if ($LGD_PAYTYPE == "SC0040" || $LGD_PAYTYPE == "SC0100"){ $xpay->Set("LGD_SEQNO", "001"); } //가상계좌, 무통장건 현금영수증 발행취소시 필수
		}
		
		if ($xpay->TX()) {

			$ocs_ordernum = $xpay->Response("LGD_OID",0);
			$ocs_tid = $xpay->Response("LGD_TID",0);
			$ocs_cashnum = $xpay->Response("LGD_CASHRECEIPTNUM",0);
			$ocs_respdate = $xpay->Response("LGD_RESPDATE",0);
			$ocs_seqno = $xpay->Response("LGD_SEQNO",0);
			$ocs_msg = $xpay->Response_Msg();
			$ocs_member = $member;
			$ocs_type = $paytype_array_flip[$LGD_PAYTYPE];

			if($xpay->Response_Code()=='0000') {
				_MQ_noreturn("
					insert into smart_order_cashlog (
						ocs_ordernum, ocs_member, ocs_date, ocs_tid, ocs_cashnum, ocs_respdate, ocs_msg, ocs_method, ocs_cardnum, ocs_amount, ocs_type, ocs_seqno
					) values (
						'$ocs_ordernum', '$ocs_member', now(), '$ocs_tid', '$ocs_cashnum', '$ocs_respdate', '$ocs_msg', '$LGD_METHOD', '$LGD_CASHCARDNUM', '$LGD_AMOUNT', '$ocs_type', '$ocs_seqno'
					)
				");

				/*$log = 
					"<p style='padding-top: 5px; display: block; margin-top: 5px; border-top: 1px solid #ddd;'><strong>"
					.(($LGD_METHOD=='AUTH')?"<span style='color:green;'>O</span> 발행":"<span style='color:red;'>X</span> 취소")."일</strong>: ".date('Y-m-d h:i',strtotime($ocs_respdate))
					." / <strong>결제수단</strong>: ".$ocs_type[$LGD_PAYTYPE]
					." / <strong>주문번호</strong>: ".$ocs_ordernum
					." / <strong>거래번호</strong>: ".$ocs_tid
					." / <strong>현금영수증 승인번호</strong>: ".$ocs_cashnum
					//." / <strong>소비자번호</strong>: ".$LGD_CASHCARDNUM
					." / <strong>금액</strong>: ".number_format($LGD_AMOUNT)
					."</p>";
				$cash_print = "<a style=\"padding: 5px; color: #fff; background: #666;\" href=\"javascript:showCashReceipts('".$LGD_MID."','".$ocs_ordernum."','','".$LGD_PAYTYPE."','".$CST_PLATFORM."')\">현금영수증 출력</a>";*/
				
				if($LGD_METHOD=='AUTH') { 
					//$return = array('현금영수증이 발행되었습니다.','issue',$log,$ocs_tid,$cash_print); 
					$return = 'AUTH';
				}
				else { 
					//$return = array('현금영수증 발행이 취소되었습니다.','cancel',$log,$ocs_tid,$cash_print); 
					$return = 'CANCEL';
				}
				//echo json_encode($return);
			} else {
				$return = 'FAIL';
			}

			// - 주문결제기록 저장 ---
			$app_oc_content = "LGD_RESPMSG||" . $xpay->Response_Msg() . "§§"; // 주문결제기록 정보 이어 붙이기

			_MQ_noreturn("
				insert smart_order_cardlog set
				oc_oordernum = '". $ordernum ."'
				,oc_tid = '". $ocs_tid ."'
				,oc_content = '". mysql_real_escape_string($app_oc_content) ."'
				,oc_rdate = now();
			");

		} else { $return = 'FAIL'; }

	}

	/******* 이니시스 ***************/
	if($siteInfo[s_pg_type]=='inicis') {
		if($paymethod == "card") $gopaymethod = "Card";
		if($paymethod == "iche") $gopaymethod = "DirectBank";
		if($paymethod == "virtual") $gopaymethod = "VBank";

		require_once(PG_DIR."/inicis/libs/INILib.php");
		$siteInfo[s_pg_code_escrow] = $siteInfo[s_pg_code_escrow]?$siteInfo[s_pg_code_escrow]:$siteInfo[s_pg_code];
		$_pg_mid = $r[o_paymethod]=='virtual'?$siteInfo[s_pg_code_escrow]:$siteInfo[s_pg_code];
		$inipay = new INIpay50;
		$inipay->SetField("inipayhome"			,PG_DIR."/inicis");							// 이니페이 홈디렉터리
		$inipay->SetField("debug"				,"true");									// 로그모드("true"로 설정하면 상세로그가 생성됨.)
		$inipay->SetField("admin"				,"1111");									// 키패스워드(상점아이디에 따라 변경)
		$inipay->SetField("mid"					,$_pg_mid);						// 상점아이디
		$inipay->SetField("paymethod"			,$gopaymethod);								// 지불방법 (절대 수정 불가)
		$inipay->SetField("pgid"				,"INIphp".$pgid); 							// 고정

		if($method=='AUTH') { // 현금영수증 발행 요청
			$inipay->SetField("type"			,"receipt"); 								// 고정
			$inipay->SetField("paymethod"		,"CASH");									// 고정 (요청분류)
			$inipay->SetField("currency"		,$currency);								// 화폐단위 (고정)
			$inipay->SetField("goodname"		,iconv('utf-8','euc-kr',$goodname));		// 상품명
			$inipay->SetField("cr_price"		,$amount);									// 총 현금결제 금액
			$inipay->SetField("sup_price"		,$app_vat_Y_tot);									// 공급가액 -- 2016-09-09 수정
			$inipay->SetField("tax"				,$app_vat_Y_vat);						  				// 부가세 -- 2016-09-09 수정
			$inipay->SetField("srvc_price"		,0);										// 봉사료
			$inipay->SetField("buyername"		,$order[o_oname]);							// 구매자 성명
			$inipay->SetField("buyeremail"		,$order[o_oemail]);							// 구매자 이메일 주소
			$inipay->SetField("buyertel"		,$order[o_ohp]);							// 구매자 전화번호
			$inipay->SetField("reg_num"			,$num);										// 현금결제자 주민등록번호
			$inipay->SetField("useopt"			,$use);										// 현금영수증 발행용도 ("0" - 소비자 소득공제용, "1" - 사업자 지출증빙용)
			$inipay->SetField("companynumber"	,$store);
		} 
		else if($method=='CANCEL') { // 현금영수증 취소 요청
			$inipay->SetField("type"			,"cancel");									// 고정 (절대 수정 불가)		
			$inipay->SetField("tid"				,$tid);										// 취소할 거래의 거래아이디
			$inipay->SetField("cancelmsg"		,$msg);										// 취소사유
			if($code != ""){ $inipay->SetField("cancelcode", $code); }						//취소사유코드
		}
		else { $return = 'FAIL'; }

		$inipay->startAction(); // 실행

/***************************************************************
* [발급 결과]
*
* 결과코드 : $inipay->GetResult('ResultCode') ("00" 이면 발행 성공)
* 승인번호 : $inipay->GetResult('ApplNum') (현금영수증 발행 승인번호)
* 승인날짜 : $inipay->GetResult('ApplDate') (YYYYMMDD)
* 승인시각 : $inipay->GetResult('ApplTime') (HHMMSS)
* 거래번호 : $inipay->GetResult('TID')
* 총현금결제 금액 : $inipay->GetResult('CSHR_ApplPrice')
* 공급가액 : $inipay->GetResult('CSHR_SupplyPrice')
* 부가세 : $inipay->GetResult('CSHR_Tax')
* 봉사료 : $inipay->GetResult('CSHR_ServicePrice')
* 사용구분 : $inipay->GetResult('CSHR_Type')
*
* [취소 결과]
*
* 결과코드 : $inipay->getResult('ResultCode') ("00"이면 취소 성공)
* 결과내용 : $inipay->getResult('ResultMsg') (취소결과에 대한 설명)
* 취소날짜 : $inipay->getResult('CancelDate') (YYYYMMDD)
* 취소시각 : $inipay->getResult('CancelTime') (HHMMSS)
* 현금영수증 취소 승인번호 : $inipay->getResult('CSHR_CancelNum')
* (현금영수증 발급 취소시에만 리턴됨)
****************************************************************/

		if($inipay->GetResult('ResultCode')=='00') { // 성공이면
			if($method=='AUTH') {
				$cash_no = $inipay->GetResult('TID');
				$receipt_no = $inipay->GetResult('ApplNum');
				$app_time = $inipay->GetResult('ApplDate');
				$res_desc = '';
				_MQ_noreturn("
					insert into smart_order_cashlog (
						ocs_ordernum, ocs_member, ocs_date, ocs_tid, ocs_cashnum, ocs_respdate, ocs_msg, ocs_method, ocs_cardnum, ocs_amount, ocs_type, ocs_seqno
					) values (
						'$ordernum', '$order[o_mid]', now(), '$cash_no', '$receipt_no', '$app_time', '$res_desc', '$method', '', '$amount', '$order[o_paymethod]', ''
					)
				");
				$return = $method;
			}
			else if($method=='CANCEL') {
				$cash_no = $inipay->GetResult('TID');
				$receipt_no = $inipay->GetResult('CSHR_CancelNum');
				$app_time = $inipay->GetResult('CancelDate');
				$res_desc = $inipay->GetResult('ResultMsg');
				_MQ_noreturn("
					insert into smart_order_cashlog (
						ocs_ordernum, ocs_member, ocs_date, ocs_tid, ocs_cashnum, ocs_respdate, ocs_msg, ocs_method, ocs_cardnum, ocs_amount, ocs_type, ocs_seqno
					) values (
						'$ordernum', '$order[o_mid]', now(), '$cash_no', '$receipt_no', '$app_time', '$res_desc', '$method', '', '$amount', '$order[o_paymethod]', ''
					)
				");
			}
		} else { $return = 'FAIL'; }

		// - 주문결제기록 저장 ---
		$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
		$keys = array('tid','ResultCode','ResultMsg','MOID','ApplDate','ApplTime','ApplNum','PayMethod','TotPrice','EventCode','CARD_Num','CARD_Interest','CARD_Quota','CARD_Code','CARD_BankCode');
		foreach($keys as $name) { $app_oc_content .= $name . "||" .iconv("euc-kr","utf-8",$inipay->GetResult($name)) . "§§" ; }
		_MQ_noreturn("
			insert smart_order_cardlog set
			 oc_oordernum = '".$ordernum."'
			,oc_tid = '". $inipay->GetResult('TID') ."'
			,oc_content = '". mysql_real_escape_string($app_oc_content) ."'
			,oc_rdate = now();
		");
	}

	/******* KCP ***************/
	if($siteInfo[s_pg_type]=='kcp') {

		include_once PG_DIR."/kcp/cfg/site_conf_inc.php";
		include_once PG_DIR."/kcp/files/pp_cli_hub_lib.php";

		$c_PayPlus = new C_PAYPLUS_CLI;
		$c_PayPlus->mf_clear();
		$cust_ip    = getenv( "REMOTE_ADDR" ); // 요청 IP

		$type_array = array(
			'AUTH'=>'',
			'CANCEL'=>'STSC'
		);
		$paytype_array = array(
			'virtual'	=>	'PAVC',
			'online'	=>	'PAXX',
			'iche'		=>	'PABK'
		);

		if ( $method == "AUTH" ) {
			$tx_cd = "07010000"; // 현금영수증 발행 요청
			//$current = _MQ("select * from smart_order_onlinelog where ool_ordernum = '$ordernum' order by ool_uid desc limit 1");


			// 현금영수증 정보
			$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "user_type",      'PGNW' ); // V5 = PGNW , V6 = MT31T08661 // 테스트아이디: V5 = T0000 , V6 = MT31T00875
			$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "trad_time",      date('YmdHis',strtotime($order[o_rdate]))        ); // 원거래시각
			$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "tr_code",        $use          ); // 0 = 소득공제용, 1 = 지출증빙용
			$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "id_info",        $num          ); // 핸드폰 / 사업자번호
			$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "amt_tot",        $amount          ); // 공급가액 + 봉사료 + 부가가치세
			$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "amt_sup",        $app_vat_Y_tot          ); // 공급가액 -- 2016-09-09 수정
			$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "amt_svc",        '0'          ); // 봉사료
			$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "amt_tax",        $app_vat_Y_vat          ); // 부가가치세 -- 2016-09-09 수정
			$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "pay_type",       $paytype_array[$paymethod]            ); // 선 결제 서비스 구분(PABK - 계좌이체, PAVC - 가상계좌, PAXX - 기타)
			$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "pay_trade_no",   $tid ); // 결제 거래번호(PABK, PAVC일 경우 필수)
			$rcpt_data_set .= $c_PayPlus->mf_set_data_us( "pay_tx_id",      $order[oc_tid]   ); // 가상계좌 입금통보 TX_ID(PAVC일 경우 필수)

			// 주문 정보
			$c_PayPlus->mf_set_ordr_data( "ordr_idxx",  $ordernum );
			$c_PayPlus->mf_set_ordr_data( "good_name",  $product );
			$c_PayPlus->mf_set_ordr_data( "buyr_name",  $order[o_oname] );
			$c_PayPlus->mf_set_ordr_data( "buyr_tel1",  $order[o_ohp] );
			$c_PayPlus->mf_set_ordr_data( "buyr_mail",  '' );
			$c_PayPlus->mf_set_ordr_data( "comment",    ''   );
			$corp_data_set .= $c_PayPlus->mf_set_data_us( "corp_type",'0');

			$c_PayPlus->mf_set_ordr_data( "rcpt_data", $rcpt_data_set );
			$c_PayPlus->mf_set_ordr_data( "corp_data", $corp_data_set );

		} else {
			$current = _MQ("select * from smart_order_cashlog where ocs_ordernum = '$ordernum' and ocs_method = 'AUTH' order by ocs_uid desc limit 1");
			$tx_cd = "07020000"; // 취소 요청
			$c_PayPlus->mf_set_modx_data( "mod_type",   $type_array['CANCEL']   );      // 원거래 변경 요청 종류
			$c_PayPlus->mf_set_modx_data( "mod_value",  $tid  );
			$c_PayPlus->mf_set_modx_data( "mod_gubn",   'MG04'   ); // MG01 = 현금영수증 거래번호, MG02 = 현금영수증 승인번호, MG03 - 신분확인ID, MG04 = KCP 결제 거래번호
			$c_PayPlus->mf_set_modx_data( "trad_time",  $current[ocs_respdate] );
		}

		if ( strlen($tx_cd) > 0 ) {
			$c_PayPlus->mf_do_tx("",$g_conf_home_dir,$g_conf_site_id,"",$tx_cd,"",$g_conf_pa_url,$g_conf_pa_port,"payplus_cli_slib",$ordernum,$cust_ip,$g_conf_log_level,"",$g_conf_tx_mode);
			if($c_PayPlus->m_res_cd == "0000") {
				$cash_no    = $c_PayPlus->mf_get_res_data( "cash_no"    );       // 현금영수증 거래번호
				$receipt_no = $c_PayPlus->mf_get_res_data( "receipt_no" );       // 현금영수증 승인번호
				$app_time   = $c_PayPlus->mf_get_res_data( "app_time"   );       // 승인시간(YYYYMMDDhhmmss)
				$reg_stat   = $c_PayPlus->mf_get_res_data( "reg_stat"   );       // 등록 상태 코드
				$reg_desc   = $c_PayPlus->mf_get_res_data( "reg_desc"   );       // 등록 상태 설명
				
				_MQ_noreturn("
					insert into smart_order_cashlog (
						ocs_ordernum, ocs_member, ocs_date, ocs_tid, ocs_cashnum, ocs_respdate, ocs_msg, ocs_method, ocs_cardnum, ocs_amount, ocs_type, ocs_seqno
					) values (
						'$ordernum', '$order[o_mid]', now(), '$cash_no', '$receipt_no', '$app_time', '$res_desc', '$method', '', '$amount', '$order[o_paymethod]', ''
					)
				");
				$return = $method;
			} else { $return = 'FAIL'; }
		} else { 
			$c_PayPlus->m_res_cd  = "9562"; $c_PayPlus->m_res_msg = "연동 오류"; $return = 'FAIL';
		}

		// - 주문결제기록 저장 ---
		$keys = array('amount','pnt_issue','card_cd','card_name','app_time','app_no',);
		$app_oc_content = "결과코드||".$c_PayPlus->m_res_cd . "§§" ; // 주문결제기록 정보 이어 붙이기
		$app_oc_content .= "결과메시지||".iconv("euckr","utf8",$c_PayPlus->m_res_msg) . "§§" ; // 주문결제기록 정보 이어 붙이기
		foreach($keys as $name) { $app_oc_content .= $name . "||" .iconv("euckr","utf8",$c_PayPlus->mf_get_res_data($name)) . "§§" ; }
		_MQ_noreturn("
			insert smart_order_cardlog set
				 oc_oordernum = '".$ordernum."'
				,oc_tid = '". $c_PayPlus->mf_get_res_data( "cash_no"       ) ."'
				,oc_content = '". mysql_real_escape_string($app_oc_content) ."'
				,oc_rdate = now();
		");

	}

	/******* 올더게이트 ***************/
	if($siteInfo[s_pg_type]=='allthegate') {
	// 현금영수증 발행 기능 없음
	}
	/******* 다우페이 ***************/
	if($siteInfo[s_pg_type]=='daupay') {
	// 현금영수증 발행 기능 없음
	}

} else { // 현금영수증이 신청되지 않았다면 DB 업데이트

	_MQ_noreturn("update smart_order set o_get_tax='Y' where o_ordernum='$ordernum'");
	$return = 'OK';

}