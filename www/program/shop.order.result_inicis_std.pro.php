<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
$ordernum = $_SESSION["session_ordernum"];//주문번호



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

// --> 비회원 구매를 위한 쿠키 적용여부 파악
cookie_chk();


# -- 라이브러리 로드
require(PG_DIR."/inicis/libs/INIStdPayUtil.php");
require(PG_DIR."/inicis/libs/HttpClient.php");
require(PG_DIR."/inicis/libs/sha256.inc.php");
require(PG_DIR."/inicis/libs/json_lib.php");


# -- 이니시스 객체생성
$inipay = new INIStdPayUtil();

	try {

 	if (strcmp("0000", $_REQUEST["resultCode"]) == 0) { //인증이 성공일경우


    $mid 				= $_REQUEST["mid"];     						// 가맹점 ID 수신 받은 데이터로 설정

    $signKey 			=  $siteInfo[s_pg_skey]; 			// 가맹점에 제공된 키(이니라이트키) (가맹점 수정후 고정) !!!절대!! 전문 데이터로 설정금지

    $timestamp 			= $inipay->getTimestamp();   						// util에 의해서 자동생성

    $charset 			= "UTF-8";        								// 리턴형식[UTF-8,EUC-KR](가맹점 수정후 고정)

    $format 			= "JSON";        								// 리턴형식[XML,JSON,NVP](가맹점 수정후 고정)

    $authToken 			= $_REQUEST["authToken"];   					// 취소 요청 tid에 따라서 유동적(가맹점 수정후 고정)

    $authUrl 			= $_REQUEST["authUrl"];    						// 승인요청 API url(수신 받은 값으로 설정, 임의 세팅 금지)

    $netCancel 			= $_REQUEST["netCancelUrl"];   					// 망취소 API url(수신 받은f값으로 설정, 임의 세팅 금지)

    $mKey 				= hash("sha256", $signKey);						// 가맹점 확인을 위한 signKey를 해시값으로 변경 (SHA-256방식 사용)


  //#####################
  // 2.signature 생성
  //#####################
  $signParam["authToken"] = $authToken;  		// 필수
  $signParam["timestamp"] = $timestamp;  		// 필수
  // signature 데이터 생성 (모듈에서 자동으로 signParam을 알파벳 순으로 정렬후 NVP 방식으로 나열해 hash)
  $signature = $inipay->makeSignature($signParam);

    //#####################
    // 3.API 요청 전문 생성
    //#####################
    $authMap["mid"] 		= $mid;   			// 필수
    $authMap["authToken"] 	= $authToken; 		// 필수
    $authMap["signature"] 	= $signature; 		// 필수
    $authMap["timestamp"] 	= $timestamp; 		// 필수
    $authMap["charset"] 	= $charset;  		// default=UTF-8
    $authMap["format"] 		= $format;  		// default=XML

    try {

    	$httpUtil = new HttpClient();

    //#####################
    // 4.API 통신 시작
    //#####################

    $authResultString = "";
    if ($httpUtil->processHTTP($authUrl, $authMap)) {
        $authResultString = $httpUtil->body;
    } else {
        echo $httpUtil->errormsg;
        throw new Exception("Http Connect Error"); // 예외처리 => 통신실패
    }


      //############################################################
      //5.API 통신결과 처리(***가맹점 개발수정***)
      //############################################################
      $resultMap = json_decode($authResultString, true);  // 기존결제모듈에서 ->GetResult() 와 같은 역활

      /*************************  결제보안 추가 2016-05-18 START ****************************/
      $secureMap["mid"]		= $mid;							//mid
      $secureMap["tstamp"]	= $timestamp;					//timestemp
      $secureMap["MOID"]		= $ordernum = $resultMap["MOID"];			//MOID => 주문번호
      $secureMap["TotPrice"]	= $resultMap["TotPrice"];		//TotPrice => 총주문금액

      // signature 데이터 생성
      $secureSignature = $inipay->makeSignatureAuth($secureMap);

      // 결제성공여부 체크 변수
      $access_result = false; // 초기화

			$keys = array('tid',
										'resultCode',
										'resultMsg',
										'MOID',
										'applDate',
										'applTime',
										'applNum',
										'payMethod',
										'TotPrice',
										'EventCode',
										'CARD_Num',
										'CARD_Interest',
										'CARD_Quota',
										'CARD_Code',
										'CARD_BankCode');

			$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
			foreach($keys as $name) {
				$app_oc_content .= $name . "||" .$resultMap[$name] . "§§" ; // 데이터 저장
			}

			// 보인키와 받은키 저장
			$app_oc_content .=  "send_val||" . $secureSignature . "§§" ; // 데이터 저장
			$app_oc_content .= "recive_val||" . $resultMap['authSignature'] . "§§" ; // 데이터 저장


			if(is_login()) $indr = $mem_info;

			// 주문정보 추출
			$r = _MQ("select * from smart_order where o_ordernum='". $ordernum ."' ");

			// - 주문결제기록 저장 ---
			$que = "
				insert smart_order_cardlog set
					 oc_oordernum = '".$ordernum."'
					,oc_tid = '". $resultMap['tid']."'
					,oc_content = '". $app_oc_content ."'
					,oc_rdate = now();
			";
			if(!preg_match('/중복/i' , $app_oc_content))  _MQ_noreturn($que);
			$insert_oc_uid = mysql_insert_id();

      if ((strcmp("0000", $resultMap["resultCode"]) == 0) && (strcmp($secureSignature, $resultMap["authSignature"]) == 0) ){	//결제보안 추가 2016-05-18
				//
				$access_result = true;

      }else{ // 결제 실패일경우

      	if (strcmp($secureSignature, $resultMap["authSignature"]) != 0) { // 결제 보안키 오류
      		throw new Exception("결제실패 - 데이터 위변조");
      	}else{
  			throw new Exception("결제실패 - msg [".(@(in_array($resultMap["resultMsg"] , $resultMap) ? $resultMap["resultMsg"] : "null" ))."]");
      	}

      }

      if($access_result <> true){ // 보안코드값 없이 왔다면
      	throw new Exception("결제실패 - 데이터 위변조");
      }


			$order = _MQ("select * from smart_order as o left join smart_order_cardlog as oc on (o.o_ordernum = oc.oc_oordernum) where o.o_ordernum = '$ordernum'");

     	/*
     		# -- 현금 영수증
     	*/
     	if( $resultMap['CSHR_ResultCode'] ) { // 현금영수정 발급결과
     		$cash_no = $resultMap['CSHR_ResultCode'];
     		_MQ_noreturn("update smart_order set o_get_tax='Y' where o_ordernum='".$ordernum."'");
			// 현금영수증 정보 저장
			_MQ_noreturn("insert into smart_order_cashlog (ocs_ordernum,ocs_member,ocs_date,ocs_tid,ocs_cashnum,ocs_respdate,ocs_amount,ocs_method,ocs_type) values
			('$ordernum','$order[o_mid]',now(),'','$no_cshr_appl','$tm_cshr','$resultMap[CSHR_ApplPrice]','AUTH','$order[o_paymethod]')");

			_MQ_noreturn("insert into smart_baro_cashbill (bc_type, bc_ordernum,TradeUsage,IdentityNum,Amount,TradeDate,RegistDT,IssueDT,BarobillState,ItemName,NTSConfirmNum) values
			('pg','$ordernum','". ($resultMap['CSHR_Type']==0?1:2) ."','','".$resultMap['CSHR_ApplPrice']."',curdate(),now(),now(),'3000','".addslashes($resultMap['goodsName'])."','')");

     	}


     	/*
				# -- 거래유형별 처리
     	*/

     	if (isset($resultMap["payMethod"]) && strcmp("VBank", $resultMap["payMethod"]) == 0) { //가상계좌 => 무통장

				$ool_type = 'R'; // 발급
				$tno = @(in_array($resultMap["tid"] , $resultMap) ? $resultMap["tid"] : "null" ); // 고유번호
				$app_time = @(in_array($resultMap["applDate"] , $resultMap) ? $resultMap["applDate"] : "null" );
				$amount = @(in_array($resultMap["TotPrice"] , $resultMap) ? $resultMap["TotPrice"] : "null" ) ;
				$account = (in_array($resultMap["VACT_Num"] , $resultMap) ? $resultMap["VACT_Num"] : "null" ) ;
				$depositor = $order[o_oname]?$order[o_oname]:@(in_array($resultMap["VACT_InputName"] , $resultMap) ? $resultMap["VACT_InputName"] : "null" );
				$bankcode = @(in_array($resultMap["VACT_BankCode"] , $resultMap) ? $resultMap["VACT_BankCode"] : "null" );
				$bank_owner = @(in_array($resultMap["VACT_Name"] , $resultMap) ? $resultMap["VACT_Name"] : "null" );

				_MQ_noreturn("
					insert into smart_order_onlinelog (
					ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_escrow_code, ool_deposit_tel, ool_bank_owner
					) values (
					'$ordernum', '$order[o_mid]', now(), '$tno', '$ool_type', '$app_time', '$amount', '$amount', '$account', '', '$depositor', '$ool_bank_name_array[$bankcode]', '$bankcode', '$escw_yn', '', '$buyr_tel2', '$bank_owner'
					)
				");

				// 장바구니 정보 삭제
				_MQ_noreturn(" delete from smart_cart where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' and c_direct='Y'  ");

				// 가상계좌 결제 이메일 및 SMS 발송
				include_once OD_PROGRAM_ROOT."/shop.order.mail.send.virtual.php";

				// 결제완료페이지 이동
				error_loc("/?pn=shop.order.complete");

     	}else{ // 카드 // 실시간계좌이체 / 휴대폰

				// 주문완료시 처리 부분 - shop.order.result.pro.php주문서수정,포인트,수량,문자발송,메일발송
				include OD_PROGRAM_ROOT."/shop.order.result.pro.php";

				// 결제완료페이지 이동
				error_loc("/?pn=shop.order.complete");

     	}


    }catch (Exception $e) { // 결제 실패처리 [결제가 완료되었으나 실패했을 시 ]

                //####################################
                // 실패시 처리(***가맹점 개발수정***)
                //####################################
                //---- db 저장 실패시 등 예외처리----//

				// 2017-01-04 ::: 결제성공 이후 동일한 정보가 다시 오는 경우 결제실패처리 하지 않음. ::: JJC
				$oc_res_cnt = _MQ(" select count(*) as cnt from smart_order_cardlog where oc_oordernum = '".$ordernum."' and oc_tid = '". $resultMap['tid']."' and (oc_content like '%resultCode||0000§§%' or oc_content like '%m_resultCode||00§§%') ");
				if($oc_res_cnt['cnt'] == 1 ) {

					// 결제 실패기록 삭제
					_MQ_noreturn("delete from smart_order_cardlog where oc_uid='". $insert_oc_uid ."' ");

					// 결제완료페이지 이동
					error_loc("/?pn=shop.order.complete",'top');

				}

				// 결제실패 처리
				else {

					_MQ_noreturn("update smart_order set o_status='결제실패' where o_ordernum='". $ordernum ."' ");
					error_loc_msg("/?pn=shop.order.result" , $resultMap["resultMsg"]." - 결제에 실패하였습니다. 다시한번 확인 바랍니다.");

				}
				// 2017-01-04 ::: 결제성공 이후 동일한 정보가 다시 오는 경우 결제실패처리 하지 않음. ::: JJC

                //#####################
                // 망취소 API
                //#####################
                $netcancelResultString = ""; // 망취소 요청 API url(고정, 임의 세팅 금지)
                if ($httpUtil->processHTTP($netCancel, $authMap)) {

                    $netcancelResultString = $httpUtil->body;
                     $s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
					 error_loc_msg("/?pn=shop.order.result" , $s );

                } else {
                		// httpUtil->errormsg
                    throw new Exception("Http Connect Error");
                }
    }

 	}else{ // 결제요청 인증 실패
 		 error_loc_msg("/?pn=shop.order.result" , '결제요청에 실패하였습니다. ['.$_REQUEST["resultCode"].']');
 	}
}catch (Exception $e) {
        $s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
        error_loc_msg("/?pn=shop.order.result" , $s );
}



actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행