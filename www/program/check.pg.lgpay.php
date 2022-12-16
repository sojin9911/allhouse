<?php
include_once(dirname(__FILE__).'/inc.php');
    /*
     * [결제조회 요청 페이지]
     *
     * LG유플러스으로 부터 내려받은 거래번호(LGD_TID) 또는 주문번호(LGD_OID)와 결제수단으로 조회(LGD_PAYTYPE)(파라미터 전달시 POST를 사용하세요)
     * 둘 다 있는 경우는 LGD_TID로 조회를 합니다.
     */

	/*
	신용카드 - SC0010
	계좌이체 -SC0030
	가상계좌-SC0040
	핸드폰-SC0060
	*/
	// 카드 결제 또는 계좌이체, 결제 상태가 N, 날짜가 하루전인 주문
	$card_fail_order = _MQ_assoc(" select o_ordernum, o_paymethod from smart_order where o_paymethod in('card', 'iche') and o_paystatus='N' and o_canceled = 'N' and npay_order = 'N' and o_rdate >= '". date("Y-m-d", strtotime("-1 days"))." 00:00:00' ");
	foreach( $card_fail_order as $k => $v){

	$LGD_OID = $v['o_ordernum']; //주문번호

	if($v['o_paymethod'] == 'card'){
		$LGD_PAYTYPE = 'SC0010';   //결제 방법 신용카드
	}else{
		$LGD_PAYTYPE = 'SC0030';   //결제 방법 계좌이체
	}


	$LGD_MERTKEY				= $siteInfo['s_pg_key'];
	$CST_PLATFORM               = $siteInfo['s_pg_mode'];     				//LG유플러스 결제 서비스 선택(test:테스트, service:서비스)
  	$CST_MID                    = $siteInfo['s_pg_code'];           					//상점아이디(LG유플러스으로 부터 발급받으신 상점아이디를 입력하세요)
																				//테스트 아이디는 't'를 반드시 제외하고 입력하세요.
    $LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;  //상점아이디(자동생성)

	$configPath 				=   PG_DIR."/lgpay/lgdacom";							//LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.

    require_once(PG_DIR."/lgpay/lgdacom/XPayClient.php");
    $xpay = &new XPayClient($configPath, $CST_PLATFORM);
    $xpay->Init_TX($LGD_MID);

    $xpay->Set("LGD_TXNAME", "Search");
    $xpay->Set("LGD_STEP", "STEP2");
    $xpay->Set("LGD_TID", $LGD_TID);
	$xpay->Set("LGD_OID", $LGD_OID);
    $xpay->Set("LGD_PAYTYPE", $LGD_PAYTYPE);

    /*
     * 결제조회 요청 결과처리
     *
     * 조회결과 리턴 파라미터는 연동메뉴얼을 참고하시기 바랍니다.
     */

	//결제조회결과 화면처리(성공,실패 결과 처리를 하시기 바랍니다)
    if ($xpay->TX()) {

		// SSJ : 결제 승인 여부 체크 추가 : 2021-10-21
		if( "0000" == $xpay->Response_Code() && $xpay->Response("LGD_STATUSCODE",0) == '1000000' && $xpay->Response("LGD_STATUSSTR",0) == 'status[승인성공]') {
			// - 결제 성공 기록정보 저장 ---
			$keys = $xpay->Response_Names();
			$app_oc_content = ""; // 주문결제기록 정보 이어 붙이기
			foreach($keys as $name) {
				$app_oc_content .= $name . "||" .$xpay->Response($name, 0) . "§§" ;
			}

			$ordernum = $v['o_ordernum'];
			$que = "
				insert smart_order_cardlog set
					 oc_oordernum = '".$ordernum."'
					,oc_tid = '". $xpay->Response("LGD_TID",0) ."'
					,oc_content = '". $app_oc_content ."'
					,oc_rdate = now();
			";
			_MQ_noreturn($que);
			// 상태 업데이트
			include OD_PROGRAM_ROOT."/shop.order.result.pro.php";
		}


		/*
        echo "결제조회 요청이 완료되었습니다. <br>";
        echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
        echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";

        // 아래는 결제조회 결과 파라미터를 모두 찍어 줍니다.
		$keys = $xpay->Response_Names();
        foreach($keys as $name) {
        	echo $name . " = " . $xpay->Response($name, 0) . "<br>";
		}
        echo "<p>";
		*/

    }else {  //API 요청 실패 화면처리
		/*
        echo "결제조회 요청이 실패하였습니다. <br>";
        echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
        echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";
		*/
    }
}
?>
