<?PHP
	include_once(dirname(__FILE__).'/inc.php');
	actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
	session_start();
	$ordernum = $_ordernum = $_SESSION["session_ordernum"] != '' ? $_SESSION["session_ordernum"] : $_REQUEST["reserveOrderNo"];//주문번호

	//--------------------------------------------------------------------------------
	// PAYCO 주문 완료시 호출되는 RETURN 페이지 샘플 ( PHP EASYPAY / PAY2 )
	// - PAYCO 결제창에서 비밀번호 입력후, 호출 하여 처리
	// payco_return.php
	// 2016-12-02	PAYCO기술지원 <dl_payco_ts@nhnent.com>
	//--------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------
	// 이 문서는 text/html 형태의 데이터를 반환합니다.
	//-----------------------------------------------------------------------------
	header('Content-type: text/html; charset:UTF-8');
	require(PG_DIR."/payco/payco_config.php");

	// --> 비회원 구매를 위한 쿠키 적용여부 파악
		cookie_chk();
    // -- 2016-12-14 LCY :: 데이터 처리
	// 회원정보 추출
	if(is_login()) $indr = $row_member;

	// 주문정보 추출
	$r = $order  = _MQ("select * from smart_order where ordernum='". $ordernum ."' ");


	//-----------------------------------------------------------------------------
	// 오류가 발생했는지 기억할 변수와 결과를 담을 변수를 선언합니다.
	//-----------------------------------------------------------------------------
	$doApproval = true;																// 기본적으로 주문예약 및 결제인증 받은것으로 설정

	$reserveOrderNo					= $_REQUEST["reserveOrderNo"];					// 주문 예약 번호
	$sellerOrderReferenceKey		= $_REQUEST["sellerOrderReferenceKey"];			// 외부가맹점에서 관리하는 주문연동Key
	$paymentCertifyToken			= $_REQUEST["paymentCertifyToken"];				// 결제인증토큰(결제승인시 필요)
	$totalPaymentAmt				= $_REQUEST["totalPaymentAmt"];					// 총결제금액
	$discountAmt					= $_REQUEST["discountAmt"];						// 쿠폰할인금액(PAYCO포인트 미포함 )
	$totalRemoteAreaDeliveryFeeAmt	= $_REQUEST["totalRemoteAreaDeliveryFeeAmt"];	// 총 도서산간비( 추가배송비 )
	$pointAmt						= $_REQUEST["pointAmt"];						// PAYCO 포인트 사용금액

	$cartNo							= $_REQUEST["cartNo"];							// returnUrlParam 에서 던진 값을 수신( 장바구니 번호 )
	$totalTaxableAmt				= $_REQUEST["tmpTotalTaxableAmt"];				// returnUrlParam 에서 던진 값을 수신( 과세 )
	$totalVatAmt					= $_REQUEST["tmpTotalVatAmt"];					// returnUrlParam 에서 던진 값을 수신( 부과세 )
	$totalTaxfreeAmt 				= $_REQUEST["tmpTotalTaxfreeAmt"];				// returnUrlParam 에서 던진 값을 수신( 면세 )

	//-----------------------------------------------------------------------------

	$code							= $_REQUEST["code"];							// 결과코드
	$message						= $_REQUEST["message"];							// 결과코드
	$mainPgCode						= $_REQUEST['mainPgCode'];						// 메인 PG 코드

	//-----------------------------------------------------------------------------
	// Read_code 값이 0 또는 2222, 또는 실패시 오류코드 값 중 하나가 옵니다.
	//	 0 - 결제 인증 성공
	// 2222 - 사용자에 의한 결제 취소
	// 내역을 표시하고 창을 닫습니다.
	//-----------------------------------------------------------------------------


	//-----------------------------------------------------------------------------
	// (로그) 호출 시점과 호출값을 파일에 기록합니다.
	//-----------------------------------------------------------------------------
	Write_Log("payco_return.php is Called - reserveOrderNo : $reserveOrderNo , sellerOrderReferenceKey : $sellerOrderReferenceKey , paymentCertifyToken : $paymentCertifyToken , totalPaymentAmt : $totalPaymentAmt , discountAmt : $discountAmt ,  totalRemoteAreaDeliveryFeeAmt : $totalRemoteAreaDeliveryFeeAmt , pointAmt : $pointAmt , code : $code");


	//-----------------------------------------------------------------------------
	// response 값이 없으면 에러(ERROR)를 돌려주고 로그를 기록한 뒤
	// 오류페이지를 보여주거나 주문되지 않았음을 고객에게 통보하는 페이지로 이동합니다.
	//-----------------------------------------------------------------------------
	// ** {{{페이코보완추가}}} ** 2019-01-16 LCY
	if($code != '0'){ // 사용자 취소

		 Write_Log("payco_return.php ERROR - reserveOrderNo : $reserveOrderNo , sellerOrderReferenceKey : $sellerOrderReferenceKey , paymentCertifyToken : $paymentCertifyToken , totalPaymentAmt : $totalPaymentAmt , code : $code");

		 if($code == 2222){
			echo "<script>alert('결제요청을 취소하셧습니다. [ERROR CODE ".$code."]'); opener.location.href='/?pn=shop.order.result'; window.close();  </script>";
			exit;
		 }
			echo "<script>alert('결제에 실패하였습니다. [ERROR CODE  ".$code."]'); opener.location.href='/?pn=shop.order.result'; window.close();  </script>";
			exit;
	}

	//-----------------------------------------------------------------------------
	// 이곳에 위의 변수들을 이용해 가맹점에서 필요한 데이터를 처리합니다.
	// 예) 재고 체크, 매출금액 확인, 주문서 생성 , 총결제 금액 비교($totalPaymentAmt) , 외부가맹점 번호일치($sellerOrderReferenceKey) 등등
	//-----------------------------------------------------------------------------

	$ItemTotalOrderAmt = $r['o_price_real'];                // DB에서 주문시 주문했던 총 금액(PAYCO 에 주문예약할때 던졌던 값.)을 가져옵니다.(주문값)
	                                            // 연동 실패를 테스트 하시려면 값을 주문값을 totalPaymentAmt 값과 틀리게 설정하세요.
	//-----------------------------------------------------------------------------
	// 수신 데이터 사용 예제2 ( 결제금액 위변조 확인 )
	//-----------------------------------------------------------------------------
	if( $totalPaymentAmt != $ItemTotalOrderAmt ){    // 위에서 파라메터로 받은 totalPaymentAmt 값과 주문값이 같은지 비교합니다.

		Write_Log("payco_return.php 결제금액 위변조 확인 > DB 총 주문금액 : ".$ItemTotalOrderAmt." 원 과 PAYCO 결제금액 : ".$totalPaymentAmt." 원 이 같지 않습니다.");

		$doApproval = false;   						// DB 총 주문금액과 PAYCO결제금액이 다르다면 오류로 설정

		//-----------------------------------------------------------------------------
		//오류일 경우 오류페이지를 표시하거나 결제되지 않았음을 고객에게 통보합니다.
		//-----------------------------------------------------------------------------

		echo "<script>alert('결제요청 금액과 결제승인된 금액이 일치하지 않습니다.'); opener.location.href='/?pn=shop.order.result'; window.close();  </script>";
		exit;

	//★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★

	}

	// @ -- 결제인증 내역을 페이코로그 db에 저장한다. :: 인증시에는 pl_ono 와  pl_certifykey 는 존재하지 않는다.
	$_serialize = serialize($_REQUEST);
	_MQ_noreturn("insert smart_payco_log set pl_code = '".$code."', pl_ono = '', pl_certifykey = '', pl_oordernum = '".$ordernum."', pl_rordernum = '".$reserveOrderNo."', pl_type = 'request', pl_serialize = '".$_serialize."', pl_rdate = now();  ");



	if ( $doApproval == true ){

		//-----------------------------------------------------------------------------
		// 확인결과(재고확인,결제금액 위변조 확인)가 정상이면 PAYCO 에 결제 승인을 요청
		//-----------------------------------------------------------------------------
		//-----------------------------------------------------------------------------
		// 결제 승인 요청에 담을 JSON OBJECT를 선언합니다.
		//-----------------------------------------------------------------------------

		$approvalOrder["sellerKey"]					= $sellerKey;					// 가맹점 코드. payco_config.php 에 설정
		$approvalOrder["reserveOrderNo"]			= $reserveOrderNo;				// 예약주문번호.
		$approvalOrder["paymentCertifyToken"]		= $paymentCertifyToken;			// 결제인증토큰.
		$approvalOrder["sellerOrderReferenceKey"]	= $sellerOrderReferenceKey;		// 외부가맹점에서 관리하는 주문연동Key
		$approvalOrder["totalPaymentAmt"]			= $totalPaymentAmt;				// 주문 총 금액.

		// ** {{{페이코보완추가}}} ** 2019-01-16 LCY
		try
		{
			// 배열을 JSON 형식으로 변환 및 백슬래시 제거 후에, 결제 승인 요청함.
			$Result = payco_approval(stripslashes(json_encode($approvalOrder)));  		// 배열을 JSON 형식으로 변환 및 백슬래시 제거 후에, 결제 승인 요청함.
			if( !is_array( $Result ) ) Write_Log("payco_return.php  payco_approval -  Result : $Result");

			// JSON 결과값을 배열로 변환한다.
			$Read_Data = json_decode($Result, true);                           			// JSON 형식의 전달받은 값을, 배열로 변환.
		}
		catch ( Exception $e )
		{
			$Error_Return                = array();
			$Error_Return["result"]        = "APPROVAL_ERROR";
			$Error_Return["message"]    = $e->getMessage();
			$Error_Return["code"]        = $e->getCode();
			Write_Log( "payco_return.php payco_approval Result Error : Code - ".$e->getCode().", Description - ".$e->getMessage() );
		}


		// @ -- 결제승인요청 로그를 이어 붙인다.
		$Result_Total = $Read_Data["result"];

		// @ -- 빈값일 시 초기화 처리
		if( count( $Result_Total) < 1 || $Result_Total == '' ) { $Result_Total = array();  }

		// @ -- 복합결제이기 때문에 메인 pg 를 참조
       // $paymethod_code = $Read_Data['result']['paymentDetails'][0]['paymentMethodCode']; // 결제코드
		// $paymethod_name = $Read_Data['result']['paymentDetails'][0]['paymentMethodName']; // 결제명
		// arr_payco_paymethod

		$paymethod_code = $mainPgCode;  // 메인 pg 만 가져온다.
		$paymethod_name = $arr_payco_paymethod[$mainPgCode];

		$app_oc_content =  "paymentMethodCode||" .addslashes($paymethod_code). "§§";
		$app_oc_content .=  "paymentMethodName||" .addslashes($paymethod_name). "§§";


		// @ -- 페이코 내에서 결제한 수단을 업데이트 한다.
		_MQ_noreturn("update smart_order set payco_paymethod_code = '".$paymethod_code."', payco_paymethod_name = '".$paymethod_name."' where ordernum = '".$ordernum."' ");


		// @ -- 상세로그 처리를 위한 부분 ====> 실질적인 데이터 처리는 하지 않는다.
		foreach ($Result_Total as $key => $value){

			$app_oc_content .= $key . "||" .addslashes($value). "§§" ;

			switch ($key){
				case "deliveryPlace": // 배송지 정보
					$deliveryPlace = $Result_Total["deliveryPlace"];

					foreach ($deliveryPlace as $key => $value){
						Write_Log("deliveryPlace[$key] : ".$value);

					}
				break;

				case "orderProducts": // 주문상품 리스트
					$orderProducts = $Result_Total["orderProducts"];
					foreach ($orderProducts as $key => $value){
						Write_Log("orderProducts[$key]");
						$orderProduct = $orderProducts[$key];

						foreach ($orderProduct as $key => $value){
							Write_Log("    $key : ".$value);
						}
					}
				break;

				case "paymentDetails": // 취소/결제내역 리스트
					$paymentDetails = $Result_Total["paymentDetails"];
					/*
					무통장입금 02
					실시간 계좌이체 04
					신용카드 31
					포인트 98
					페이코쿠폰 75
					카드쿠폰 76
					가맹점쿠폰 77
					*/
					foreach ($paymentDetails as $key => $value){
						Write_Log("paymentDetails[$key] : ");
						$paymentDetail = $paymentDetails[$key];

						foreach ($paymentDetail as $key => $value){
							switch ($paymentDetail["paymentMethodCode"]){
								case "02": // 무통장입금
									Write_Log(" withoutBankbook :");
									//nonBankbookSettleInfo
									$nonBankbookSettleInfo = $paymentDetail["nonBankbookSettleInfo"]; // 무통장 정보를 가져온다.
									foreach($nonBankbookSettleInfo  as $key => $value) {
										Write_Log("    $key : ".$value);
									}
								break;

								case "31": // 신용카드
									if ($key=="cardSettleInfo"){
										Write_Log("    cardSettleInfo :");
										$cardSettleInfo = $paymentDetail["cardSettleInfo"];

										foreach ($cardSettleInfo as $key => $value){
											Write_Log("        $key : ".$value);
										}
									} else {
										Write_Log("    $key : ".$value);
									}
								break;

								// 페이코 쿠폰
								case "75":
								case "76":
								case "77":
									if ($key=="couponSettleInfo"){
										Write_Log("    couponSettleInfo : ");
										$couponSettleInfo = $paymentDetail["couponSettleInfo"];

										foreach ($couponSettleInfo as $key => $value){
											Write_Log("        $key : ".$value);
										}
									} else {
										Write_Log("    $key : ".$value);
									}
								break;

								case "98":
									Write_Log("    $key : ".$value);
								break;

								default:
								break;
							}
						}
					}

					break;

					default:
						Write_Log("$key : ".$value);
					break;
				}
			}


		// - 주문결제기록 저장 ---
		$que = "
			insert smart_order_cardlog set
				 oc_oordernum = '".$ordernum."'
				,oc_tid = '".$Read_Data["result"]["reserveOrderNo"]."'
				,oc_content = '". $app_oc_content ."'
				,oc_rdate = now();
		";

		_MQ_noreturn($que);
		// - 주문결제기록 저장 ---
		// - 결제 성공 기록정보 저장 ---




		//-----------------------------------------------------------------------------
		// 결제 승인 수신 데이터 사용
		//-----------------------------------------------------------------------------

		$sellerOrderReferenceKey			= $Read_Data["result"]["sellerOrderReferenceKey"];			// 가맹점에서 발급했던 주문 연동 Key
		$reserveOrderNo						= $Read_Data["result"]["reserveOrderNo"];					// PAYCO에서 발급한 주문예약번호
		$orderNo							= $Read_Data["result"]["orderNo"];							// PAYCO에서 발급한 주문번호 // 주문취소 시 필요
		$memberName							= $Read_Data["result"]["memberName"];						// 주문자명
		$totalOrderAmt						= $Read_Data["result"]["totalOrderAmt"];					// 총 주문 금액
		$totalDeliveryFeeAmt				= $Read_Data["result"]["totalDeliveryFeeAmt"];				// 총 배송비 금액
		$totalRemoteAreaDeliveryFeeAmt		= $Read_Data["result"]["totalRemoteAreaDeliveryFeeAmt"];	// 총 추가배송비 금액
		$totalPaymentAmt					= $Read_Data["result"]["totalPaymentAmt"];					// 총 결제 금액

		$sellerOrderProductReferenceKey     = $Read_Data["result"]["orderProducts"][0]["sellerOrderProductReferenceKey"];	// 가맹점에서 관리하는 	주문상품 연동key // 주문취소 시 필요 op_uid
		$orderCertifyKey 					= $Read_Data["result"]["orderCertifyKey"];					// 주문인증키 ::  주문취소 시 필요


		// @ -- 결제승인 내역을 페이코로그 db에 저장한다.
		$_serialize = serialize($Read_Data); // $Read_Data["code"]
		_MQ_noreturn("insert smart_payco_log set pl_code = '".$Read_Data["code"]."', pl_ono = '".$orderNo."', pl_certifykey = '".$orderCertifyKey."', pl_oordernum = '".$ordernum."', pl_rordernum = '".$reserveOrderNo."', pl_type = 'auth', pl_serialize = '".$_serialize."', pl_rdate = now();  ");


		// @ -- 결제 최종 저장
		// ** {{{페이코보완추가}}} ** 2019-01-16 LCY
		if($Read_Data["code"] == '0'){	// 결제 성공
			// $extraDataArray['includePaymentMethodCodes'] = array('01','35','04','31','98','75','76','77');
			// @ -- 결제 수단별 처리
			switch($paymethod_code){
				case "02": // 무통장 입금

					$bankName =  $Read_Data['result']['paymentDetails'][0]['nonBankbookSettleInfo']['bankName']; // 은행명
					$bankCode =  $Read_Data['result']['paymentDetails'][0]['nonBankbookSettleInfo']['bankCode']; // 은행코드
					$accountNo = $Read_Data['result']['paymentDetails'][0]['nonBankbookSettleInfo']['accountNo']; // 계좌번호
					$paymentExpirationYmd = $Read_Data['result']['paymentDetails'][0]['nonBankbookSettleInfo']['paymentExpirationYmd']; // 입금만료일


					_MQ_noreturn("
						insert into smart_order_onlinelog (
						ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_escrow_code, ool_deposit_tel, ool_bank_owner
						) values (
						'$ordernum', '$order[o_mid]', now(), '$orderNo', 'R', '".date('YmdHis')."', '$totalPaymentAmt', '".$r['o_price_real']."', '$accountNo', '', '".$order[o_oname]."', '".$bankName."', '$bankCode', 'N', '', '', ''
						)
					");

					// 장바구니 정보 삭제
					_MQ_noreturn(" delete from smart_cart where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' and c_direct='Y'  ");

					// 가상계좌 결제 이메일 및 SMS 발송
					include_once OD_PROGRAM_ROOT."/shop.order.mail.send.virtual.php";
					echo "<script>opener.location.href='/?pn=shop.order.complete'; window.close();  </script>";
					exit;

				break;

				case "35": // 실시간 계좌이체
				case "04": // 실시간 계좌이체

					// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
					include OD_PROGRAM_ROOT."/shop.order.result.pro.php";
					echo "<script>opener.location.href='/?pn=shop.order.complete'; window.close();  </script>";
					exit;

				break;

				case "31": // 신용카드
				case "01": // 신용카드

					// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
					include OD_PROGRAM_ROOT."/shop.order.result.pro.php";
					echo "<script>opener.location.href='/?pn=shop.order.complete'; window.close();  </script>";
					exit;

				break;

				case "98": // 포인트

					// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
					include OD_PROGRAM_ROOT."/shop.order.result.pro.php";
					echo "<script>opener.location.href='/?pn=shop.order.complete'; window.close();  </script>";
					exit;

				break;

				case "75": // 페이코 쿠폰

					// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
					include OD_PROGRAM_ROOT."/shop.order.result.pro.php";
					echo "<script>opener.location.href='/?pn=shop.order.complete'; window.close();  </script>";
					exit;

				break;

				case "76": // 카드쿠폰

					// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
					include OD_PROGRAM_ROOT."/shop.order.result.pro.php";
					echo "<script>opener.location.href='/?pn=shop.order.complete'; window.close();  </script>";
					exit;

				break;

				case "77": // 가맹점 쿠폰

					// 주문완료시 처리 부분 - 주문서수정,포인트,수량,문자발송,메일발송
					include OD_PROGRAM_ROOT."/shop.order.result.pro.php";
					echo "<script>opener.location.href='/?pn=shop.order.complete'; window.close();  </script>";
					exit;

				break;
			}

			// @ -- 결제완료 처리 전

			echo "<script>opener.location.href='/?pn=shop.order.complete'; window.close();  </script>";
			exit;

		}else if($Read_Data["code"] == 4005){ // 중복결제

			echo "<script>alert('중복결제가 확인 되어 결제 처리가 되지 않았습니다.'); opener.location.href='/?pn=shop.order.result'; window.close();  </script>";
			_MQ_noreturn("update smart_order set o_status='결제실패' where o_ordernum='". $ordernum ."' ");
			exit;
		}else{ // 그밖에 에러....

			$ErrBoolean = true;              //  결제 승인 코드값이 0 (성공) 이 아니면 오류로 설정

			$Err_code    = $Read_Data["code"];
			$Err_message = $Read_Data["message"];

			Write_Log("payco_return.php 결제 승인 실패 - code : $Err_code , message : $Err_message ");
			_MQ_noreturn("update smart_order set o_status='결제실패' where o_ordernum='". $ordernum ."' ");
			echo "<script>alert('결제승인에 실패하였습니다.[".$Err_message."]'); opener.location.href='/?pn=shop.order.result'; window.close();  </script>";
			exit;

		}	// 결제 승인 실패



		//-----------------------------------------------------------------------------
		// ...
		// 결제 승인 수신 데이터를 이용하여 결제 승인후 처리할 부분을 이곳에 작성합니다.
		// ...
		//-----------------------------------------------------------------------------

	}else{

		//-----------------------------------------------------------------------------
		//
		// 오류일 경우 오류페이지를 표시하거나 결제되지 않았음을 고객에게 통보합니다.
		// 팝업창 닫기 또는 구매 실패 페이지 작성 ( 팝업창 닫을때 Opener 페이지 이동 등 )
		//
		//-----------------------------------------------------------------------------
		//결제 인증 후 내부 오류가 있어 승인은 받지 않았습니다. 오류내역을 여기에 표시하세요. 예) 재고 수량이 부족합니다.

			// - 주문결제기록 저장 ---
			// - 결제 성공 기록정보 저장 ---


		//최종결제요청 결과 실패 DB처리
		//echo "최종결제요청 결과 실패 DB처리하시기 바랍니다.<br>";
		_MQ_noreturn("update smart_order set o_status='결제실패' where o_ordernum='". $ordernum ."' ");

		if($code != 0){
			 Write_Log("payco_return.php ERROR - reserveOrderNo : $reserveOrderNo , sellerOrderReferenceKey : $sellerOrderReferenceKey , paymentCertifyToken : $paymentCertifyToken , totalPaymentAmt : $totalPaymentAmt , code : $code");

			echo "<script>alert('결제승인요청에 실패하였습니다. 에러코드[".$code."]'); opener.location.href='/?pn=shop.order.result'; window.close();  </script>";
			exit;
		}
	}

?>

<?php actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행 ?>