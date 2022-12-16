<?php

	// ----- [하이센스3.0 결제취소파일 일원화 패치] -----

	// 공통취소파일 - include 사용
	//			넘길변수
	//					-> 취소위치 : _loc (관리자일 경우 - admin / 사용자일 경우 - user)
	//					-> 주문번호 : _ordernum
	//					-> 주문정보 : $osr
	//			return 정보
	//					-> 성공여부 : cancel_status = Y/N
	//					-> 메시지 : cancel_msg

	$cancel_status = ""; // 없음 처리
	$add_mid_que = ($_loc == "admin" ? "" : (is_login() ? " and o_mid='".get_userid()."' and o_memtype = 'Y' " : " and o_memtype = 'N' "));// ----- JJC : 비회원 주문취소 추가 : 2020-07-09 -----

	if(sizeof($osr) == 0 ) {
		$osr = _MQ("
			select
				o.* ,
				oc.oc_tid, oc.oc_uid,
				( select ool_tid from smart_order_onlinelog where ool_ordernum=o.o_ordernum order by ool_uid desc limit 1 ) as ool_tid
			from smart_order as o
			left join smart_order_cardlog as oc on (oc.oc_oordernum=o.o_ordernum and oc.oc_tid !='')
			where o.o_ordernum='". addslashes($_ordernum) ."'  and o.o_canceled ='N' ". $add_mid_que ."
		");
	}


	// 주문 있을 경우 취소
	if( sizeof($osr) > 0 ) {

		// SSJ : 주문/결제 통합 패치 : 2021-02-24
		if( in_array($osr['o_paymethod'] , $arr_cancel_payment_type) & $osr['o_paystatus'] == 'Y') {//[하이센스3.0 결제취소파일 일원화 패치] : 결제완료 시 연동 - 결제대기는 연동필요 없음

            if($force_cancel) { // 강제 취소
                $is_pg_status = true;
            }
			else if( $osr['o_paymethod'] == 'payco'){ // {{{페이코주문취소}}}
				if( in_array($osr['payco_paymethod_code'] ,array('01','04','31','35')) == false){
					$cancel_status = "N"; // 실패처리
					$cancel_msg = "(페이코) 취소가 불가능한 결제수단입니다. 고객센터에 문의해주세요.";
				}
				if(!$cancel_status){
					require(OD_PROGRAM_ROOT."/pg.cancle_payco.php");
				}
			}

            // JJC : 간편결제 - 페이플 : 2021-06-05
            else if( $osr['o_paymethod'] == 'payple'){ // 페이플 간편결제 주문취소
                require(OD_PROGRAM_ROOT."/pg.cancle_payple.php");
            }
            // JJC : 간편결제 - 페이플 : 2021-06-05

			else{
				// 결제 취소를 위한 거래 정보 호출
				switch($siteInfo[s_pg_type]) {
					case "lgpay" :
						//require(OD_PROGRAM_ROOT."/pg.cancle_lgpay.php");
						// SSJ : 토스페이먼츠 PG 모듈 교체 : 2021-02-22
						require(OD_PROGRAM_ROOT."/pg.cancle_toss.php");
					break;
					case "kcp" :
						require(OD_PROGRAM_ROOT."/pg.cancle_kcp.php");
					break;
					case "inicis" :
						require(OD_PROGRAM_ROOT."/pg.cancle_inicis.php");
					break;
					case "allthegate" :
						require(OD_PROGRAM_ROOT."/pg.cancle_allthegate.php");
					break;
					case "billgate" :
						$_paymethod = $osr['o_paymethod'];
						if($_paymethod=='iche') {
							require(OD_PROGRAM_ROOT."/pg.cancle_billgate.account.php");
						} else {
							require(OD_PROGRAM_ROOT."/pg.cancle_billgate.php");
						}
					break;
					case "daupay" :
						require(OD_PROGRAM_ROOT."/pg.cancle_daupay.php");
					break;

				}
			}


			if ($is_pg_status) {	// pg모듈 호출 상태

				// 상품 재고 증가 및 판매량 차감
				if( $osr['o_canceled'] <> "Y" ){
					// 제공변수 : $_ordernum
					include(OD_PROGRAM_ROOT."/shop.order.salecntdel_pro.php");

					// 제공변수 : $_ordernum
					include(OD_PROGRAM_ROOT."/shop.order.pointdel_pro.php");
					// - 적용된 포인트, 쿠폰적용 취소 ---

					// 문자 발송
					$sms_to = $osr['o_ohp'] ? $osr['o_ohp'] : $osr['o_otel'];
                    if($isMultiSms == 'Y'){ $arr_send[] = array('to'=>$sms_to, 'type'=>'order_cancel', 'ordernum'=>$_ordernum); } // 2020-04-07 SSJ :: 문자 일괄 발송
                    else{ shop_send_sms($sms_to,"order_cancel",$_ordernum); }
				}

				_MQ_noreturn("update smart_order set o_canceled='Y' , o_canceldate = now() , o_cancel_mem_type = 'member' where o_ordernum='{$_ordernum}' ". $add_mid_que ." ");

				if($osr['o_get_tax']=='Y') { // 현금영수증 취소
					$method = 'CANCEL';
					$paymethod = $osr['o_paymethod'];
					$ordernum = $_ordernum;
					$tid = $osr['oc_tid'];
					$amount = $osr['o_price_real'];

					include(OD_PROGRAM_ROOT.'/totalCashReceipt.php');
				}

				// 주문서 상태 업데이트
				order_status_update($_ordernum);

				$cancel_status = "Y"; // 성공처리
				$cancel_msg = "주문을 취소하였습니다.";

			}
			else {

				$cancel_status = "N"; // 실패처리
                $cancel_msg = $rResMsg ? iconv('euc-kr','utf-8',$rResMsg) : '결제취소 시 오류가 발생하였습니다.';

			}
		}
		// - 카드결제/계좌이체 취소 ---

		// - 무통장입금/가상계좌 취소 && 결제대기 된 카드 등 처리 ---
		else {

			// SSJ : 토스페이먼츠 PG 모듈 교체 : 2021-02-22
			//// LGU+ 가상계좌 반납처리
			//if($osr['o_paymethod']=='virtual') {
			//	switch($siteInfo[s_pg_type]) {
			//		case "lgpay" :
			//			if($osr['ool_tid']) { $LGD_TID = $osr['ool_tid']; } else { $LGD_TID = $osr['oc_tid']; }
			//			$CST_PLATFORM               = $siteInfo['s_pg_mode'];
			//			$CST_MID                    = $siteInfo['s_pg_code'];
			//			$LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
			//			$configPath 				= PG_DIR . "/lgpay/lgdacom";
			//			require(PG_DIR. "/lgpay/lgdacom/XPayClient.php");
			//			$xpay = &new XPayClient($configPath, $CST_PLATFORM); $xpay->Init_TX($LGD_MID);
			//			$xpay->Set("LGD_TXNAME", "Settlement");
			//			$xpay->Set("LGD_TID", $LGD_TID);
			//			$is_pg_status = $xpay->TX();
			//			break;
			//	}
			//}


			// 상품 재고 증가 및 판매량 차감
			if( $osr['o_canceled'] <> "Y" ){
				if($osr['o_apply_point'] == "Y") {

					// 상품 재고 증가 및 판매량 차감
					$_ordernum = $_ordernum;
					include(OD_PROGRAM_ROOT."/shop.order.salecntdel_pro.php");

					// 문자 발송
					$sms_to = $osr['o_ohp'] ? $osr['o_ohp'] : $osr['o_otel'];
                    if($isMultiSms == 'Y'){ $arr_send[] = array('to'=>$sms_to, 'type'=>'order_cancel', 'ordernum'=>$_ordernum); } // 2020-04-07 SSJ :: 문자 일괄 발송
                    else{ shop_send_sms($sms_to,"order_cancel",$_ordernum); }

				}

				// - 적용된 포인트, 쿠폰적용 취소 ---
				// 제공변수 : $_ordernum
				include(OD_PROGRAM_ROOT."/shop.order.pointdel_pro.php");
				// - 적용된 포인트, 쿠폰적용 취소 ---

				// {{{회원쿠폰}}} --무통장의 경우 결제전일경우에 쿠폰처리
				if( $osr['o_paystatus'] == "N" && $osr['o_canceled'] <> "Y" ){
					if($osr['o_coupon_individual_uid']){
						_MQ_noreturn("update smart_individual_coupon set coup_use ='N', coup_usedate = NULL where  find_in_set(coup_uid, '".$osr['o_coupon_individual_uid']."') > 0 and coup_use = 'W'  ");
					}
				}
				// {{{회원쿠폰}}}
			}

			_MQ_noreturn("update smart_order set o_canceled='Y' , o_canceldate = now() , o_cancel_mem_type = 'member' where o_ordernum='{$_ordernum}' ". $add_mid_que ." ");

			if($osr['o_get_tax']=='Y') { // 현금영수증 취소
				$method = 'CANCEL';
				$paymethod = $osr['o_paymethod'];
				$ordernum = $_ordernum;
				$tid = $osr['oc_tid'];
				$amount = $osr['o_price_real'];

				include(OD_PROGRAM_ROOT.'/totalCashReceipt.php');
			}

			// 주문서 상태 업데이트
			order_status_update($_ordernum);

			$cancel_status = "Y"; // 성공처리
			$cancel_msg = "주문을 취소하였습니다.";

		}
		// - 무통장입금/가상계좌 취소 ---

	}
	else {

		$cancel_status = "N"; // 실패처리
		$cancel_msg = "주문정보가 없습니다.";

	}