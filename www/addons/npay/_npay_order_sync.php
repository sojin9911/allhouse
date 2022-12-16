<?php
/*
// 1시간마다 실행 되도록 처리
	# 크론 사용시
		0 * * * * curl -s -o /dev/null -w "%{http_code}\n"  http://홈페이지 주소/네이버페이 에드온/_npay_order_sync.php

	# 크론 미사용시 include

	# Nsync.class.php 추가 항목
	- GetProductOrderIDList($OrderID = '', $service = 'MallService41')
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
if($siteInfo['npay_use'] != 'Y' || $siteInfo['npay_mode'] != 'real') die('네이버페이가 사용 불가능한 상태입니다.');

// 기본설정 --------------------------------------------------------
$NPCartTable = 'smart_npay'; // 네이버페이 카트 테이블
$OTable = 'smart_order'; // 주문 테이블
$Oodernum = 'o_ordernum'; // 주문 테이블 - 주문번호
$OPTable = 'smart_order_product'; // 주문상품 테이블
$OPodernum = 'op_oordernum'; // 주문상품 테이블 - 주문번호
$OPpcode = 'op_pcode'; // 주문상품 테이블 - 상품코드
$OPPouid = 'op_pouid'; // 주문상품 테이블 - 옵션 고유코드
$SettingTable = 'smart_setup'; // 환경설정 테이블
$startDate = date('Y-m-d');
//$startDate = '2019-01-18'; // TEST::날짜고정
// 기본설정 --------------------------------------------------------

if(file_exists($_SERVER['DOCUMENT_ROOT'].'/include/addons/npay/Nsync.class.php')) include_once($_SERVER['DOCUMENT_ROOT'].'/include/addons/npay/Nsync.class.php');
else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/addons/npay/Nsync.class.php')) include_once($_SERVER['DOCUMENT_ROOT'].'/addons/npay/Nsync.class.php');


/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */

	// * PAY_WAITING 는 건너띄세요
	// -=@ 네이버 조회 조건 설정 @=-
		/*
			★: 패치 전 제한 적으로 사용 하전 상태값
			# 전달 가능한 $type 값
				PAY_WAITING => 입금 대기
				PAYED => 결제 완료 ★
				DISPATCHED => 발송 처리 ★
				CANCEL_REQUESTED => 취소 요청
				RETURN_REQUESTED => 반품 요청
				EXCHANGE_REQUESTED => 교환 요청
				EXCHANGE_REDELIVERY_READY => 교환 재배송 준비
				HOLDBACK_REQUESTED => 구매 확정 보류 요청
				CANCELED => 취소 ★
				RETURNED => 반품
				EXCHANGED => 교환
				PURCHASE_DECIDED => 구매 확정
		*/
		$StatusArray = array(
			  'PAYED'
			, 'DISPATCHED'
			, 'CANCEL_REQUESTED'
			, 'RETURN_REQUESTED'
			, 'EXCHANGE_REQUESTED'
			, 'EXCHANGE_REDELIVERY_READY'
			, 'HOLDBACK_REQUESTED'
			, 'CANCELED'
			, 'RETURNED'
			, 'EXCHANGED'
			, 'PURCHASE_DECIDED'
		); // array_search('EXCHANGE_REDELIVERY_READY', $StatusArray) -> 5

		// 파라미터로 TYPE이 들어오면 해당 타입만 확인한다.
		if(isset($TYPE)) $TYPE = strtoupper($TYPE);
		if(isset($TYPE) && in_array($TYPE, $StatusArray)) {
			if(!in_array($TYPE, $StatusArray)) die('지원하지 않는 타입');
			$FindType = array_search($TYPE, $StatusArray);
			$StatusArray = array($StatusArray[$FindType]);

			$FindSyncTime = _MQ(" select npay_callback from {$SettingTable} ");
			$LastCallbackTime = strtotime($FindSyncTime['npay_callback']);
			$CallbackDiff = time()-$LastCallbackTime;
			if($CallbackDiff < 10) {
				$trigger = false;
			}
			else {
				$trigger = true;
				_MQ_noreturn(" update {$SettingTable} set npay_callback = now() ");
			}
		}
		else {
			// 마지막 수집을 체크 하여 1시간 이내라면 실행 하지 않도록 차단 추가
			$FindSyncTime = _MQ(" select npay_sync_date from {$SettingTable} ");
			if($FindSyncTime['npay_sync_date'] == '0000-00-00 00:00:00') $FindSyncTime['npay_sync_date'] = strtotime('-24 hours'); // 최초실행 시 값보정
			if(strtotime($FindSyncTime['npay_sync_date'].'+1 hours') > time()) die('No update!! '.$FindSyncTime['npay_sync_date']);
			_MQ_noreturn(" update {$SettingTable} set npay_sync_date = now() ");
			$trigger = true;
		}
		if($trigger === false) die('실행중');
	// -=@ 네이버 조회 조건 설정 @=-

/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */

	// -=@ 네이버 측에서 콜백 상태별로 데이터 조회 @=-
		$NPOrderTmp = array(); // 임시 저장
		$NPList = array(); // 주문상품 번호의 주문 데이터에 포함되는 모든 주문번호를 찾는다.
		$NPOrder = array(); // 검색된 네이버 주문번호
		$NPOrder['update'] = array(); // 업데이트 .. 1
		$NPOrder['half_fix'] = array(); // 불안정 보정 추가 .. 2
		$NPOrder['insert'] = array(); // 신규 추가 항목 .. 3
		$NPOrder['half'] = array(); // 불안정 신규 추가 .. 3
		foreach($StatusArray as $Nstatus) {
			// 어제 데이터
			$order_ago = $NSync->GetChangedProductOrderList(date('Y-m-d', strtotime($startDate.' -1 day')), date('Y-m-d', strtotime($startDate.' -1 day')), $Nstatus);
			if(count($order_ago) <= 0) $order_ago = array();
			if(count($order_ago) <= 1 && empty($order_ago[0]['ProductOrderID'])) $order_ago = array();
			foreach($order_ago as $NVData) {
				if(count($NPOrderTmp[$NVData['OrderID']]) <= 0) $NPOrderTmp[$NVData['OrderID']] = array();
				$NPOrderTmp[$NVData['OrderID']][$NVData['ProductOrderID']] = $Nstatus;
			}

			// 오늘 데이터
			$order = $NSync->GetChangedProductOrderList(date('Y-m-d', strtotime($startDate)), '', $Nstatus);
			if(count($order) <= 0) $order = array();
			if(count($order) <= 1 && empty($order[0]['ProductOrderID'])) $order = array();
			foreach($order as $NVData) {
				if(count($NPOrderTmp[$NVData['OrderID']]) <= 0) $NPOrderTmp[$NVData['OrderID']] = array();
				$NPOrderTmp[$NVData['OrderID']][$NVData['ProductOrderID']] = $Nstatus;
			}
		}

		//$NPOrderTmp = array(2019011862128900=>$NPOrderTmp['2019011862128900']); // TEST:: 테스트 할 네이버 페이 주문코드를 작성하세요

		if(count($NPOrderTmp) > 0) {
			foreach($NPOrderTmp as $k=>$v) {
				foreach($v as $kk=>$vv) {

					$FindOP = _MQ(" select npay_status, npay_sync from {$OPTable} where npay_order_code = '{$kk}' "); // TEST::  and npay_uniq != '3f916c5d971476de0a1c85113bf965cd' 불필요 제거
					if(count($NPList[$k]) <= 0) $NPList[$k] = $NSync->GetProductOrderIDList($k); // 해당 주문(주문상품X)에 포함된 주문번호 수집

					if($FindOP['npay_sync'] == 'R') { // 후보정 상품 정보 도착 - half_fix
						if(count($NPOrder['half_fix'][$k]) <= 0) $NPOrder['half_fix'][$k] = array();
						$NPOrder['half_fix'][$k][$kk] = $vv;
					}
					else if(isset($FindOP['npay_status'])) { // 이미 수집되었다면 상태만 바꿀 수 있도록 배열에 추가
						if($vv != $FindOP['npay_status']) $NPOrder['update'][$kk] = $vv; // 상태가 다른것만 추가
					}
					else {
						if(count($NPOrder['insert'][$k]) <= 0) $NPOrder['insert'][$k] = array();
						$NPOrder['insert'][$k][$kk] = $vv;
					}
				}
			}
		}
	// -=@ 네이버 측에서 콜백 상태별로 데이터 조회 @=-

/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */

	// -=@ 주문이 완성형이 아닌 경우 처리(상품정보 누락) @=-
		/*
			insert 항목 중 일부가 이미 옮겨졌다면 -> 프로세스측에서 모두 주문화 후 빠진 항목만 op.npay_sync = R 처리
		*/
		if(count($NPOrder['insert']) > 0) {
			foreach($NPOrder['insert'] as $Nkey=>$Nval) {
				$NfirstNOP = reset($NPList[$Nkey]);
				$FindData = array();
				$FindData[$NfirstNOP] = $NSync->GetProductOrderInfoList($NfirstNOP);
				$Nuniq = $FindData[$NfirstNOP]['ProductOrder']['MallManageCode']; // 솔루션과 일치하는 고유값을 찾음
				$FindCart = _MQ(" select count(*) as cnt from {$NPCartTable} where c_uniq = '{$Nuniq}' ");
				if(count($FindCart) != count($Nval)) { // 불안정 데이터
					$NPOrder['half'][$Nkey] = $Nval; // 상품정보 일부 수신인 경우 insert 를 half 항목으로 변경
					unset($NPOrder['insert'][$Nkey]);
				}
			}
		}
	// -=@ 주문이 완성형이 아닌 경우 처리(상품정보 누락) @=-

/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */

		// -=@ update 동작 처리 @=-
			/*
				$NPOrder['update'] = array(
					'네이버 주문상품코드'=>'현재상태',
					...
				)
					- 주문상품의 네이버페이 상태만 바꾸면 됨
			*/
			if(count($NPOrder['update']) > 0) {
				foreach($NPOrder['update'] as $ok=>$ov) { // 주문단위
					$OPCode = $ok; // 네이버 :: 주문상품코드
					$OPStatus = $ov; // 네이버 :: 주문상품 상태

					// ------------------------------------------------------------------------------------------------------------------------- */
					// 솔루션마다 다르게 적용 :: 손으로 작성 하세요.
					// ------------------------------------------------------------------------------------------------------------------------- */
						if($OPStatus == 'CANCELED') { // 취소 처리 프로세스

							$ChangeProduct = _MQ(" select * from {$OPTable} where npay_order_code = '{$OPCode}' ");
							$_ordernum = $ChangeProduct['op_oordernum'];
							$_uid = $ChangeProduct['op_uid'];
							$_result_msg = '네이버페이 취소'; // 취소 사유

							// [LCY] 2020-03-04 -- 주문취소 --
							include(OD_PROGRAM_ROOT.'/shop.order.salecntdel_part.php');
							_MQ_noreturn(" update `smart_order_product` set
								`op_cancel` = 'Y',
								`op_cancel_returnmsg` = '{$_result_msg}',
								`op_cancel_tid` = '',
								`op_cancel_cdate` = now(),
								`npay_status` = 'CANCELED'
								where `op_oordernum` = '{$_ordernum}' and `op_uid` = '{$_uid}'
							");

							// 추가옵션 취소처리
							$add_res = _MQ_assoc(" select * from `smart_order_product` where `op_is_addoption` = 'Y' and `op_addoption_parent` = '{$ChangeProduct['op_pouid']}' and `op_oordernum` = '{$_ordernum}' ");
							if(count($add_res) > 0) {
								foreach($add_res as $adk=>$adv) {
									_MQ_noreturn(" update `smart_order_product` set
										`op_cancel` = 'Y',
										`op_cancel_returnmsg` = '{$_result_msg}',
										`op_cancel_tid` = '',
										`op_cancel_cdate` = now(),
										`npay_status` = 'CANCELED'
										where `op_oordernum` = '{$adv['op_oordernum']}' and `op_uid` = '{$adv['op_uid']}'
									");
								}
							}

							// 마지막 부분취소일 경우 주문 전체 취소
							$tmp = _MQ(" select count(*) as `cnt` from `smart_order_product` where `op_cancel` != 'Y' and `op_oordernum` = '{$_ordernum}' ");
							if($tmp['cnt'] == 0) {
								include(OD_PROGRAM_ROOT.'/shop.order.pointdel_pro.php');
								_MQ_noreturn(" update `smart_order` set `o_canceled` = 'Y' where `o_ordernum` = '{$_ordernum}' ");
							}

							// 주문상태 업데이트
							order_status_update($_ordernum);
						}
						else if($OPStatus == 'DISPATCHED') { // 배송처리

							$OrderDetail = $NSync->GetProductOrderInfoList($OPCode);
							$ChangeProduct = _MQ(" select * from {$OPTable} where npay_order_code = '{$OPCode}' ");
							$_ordernum = $ChangeProduct['op_oordernum'];
							$_uid = $ChangeProduct['op_uid'];

							// 택배사 보정
							if($OrderDetail['Delivery']['DeliveryMethod'] == 'VISIT_RECEIPT') {
								$OrderDetail['Delivery']['DeliveryCompany'] = '방문수령';
								$OrderDetail['Delivery']['TrackingNumber'] = '방문수령';
							}
							if($OrderDetail['Delivery']['DeliveryMethod'] == 'DIRECT_DELIVERY') {
								$OrderDetail['Delivery']['DeliveryCompany'] = '직접배송';
								$OrderDetail['Delivery']['TrackingNumber'] = '직접배송';
							}
							if($OrderDetail['Delivery']['DeliveryMethod'] == 'QUICK_SVC') {
								$OrderDetail['Delivery']['DeliveryCompany'] = '퀵서비스';
								$OrderDetail['Delivery']['TrackingNumber'] = '퀵서비스';
							}
							$DeliveryCompany = $NSync->Courier($OrderDetail['Delivery']['DeliveryCompany']); // 택배명->솔루션 텍배
							$TrackingNumber = $OrderDetail['Delivery']['TrackingNumber']; // 운송장번호
							if(trim($DeliveryCompany) == '') continue;
							if(trim($TrackingNumber) == '') continue;

							// 배송처리
							_MQ_noreturn("
								update
									`smart_order_product`
								set
									`op_sendcompany` = '{$DeliveryCompany}',
									`op_sendnum` = '{$TrackingNumber}',
									`op_senddate` = now(),
									`op_sendstatus` = '배송완료',
									`npay_status` = 'DISPATCHED'
								where
									`op_uid` = '{$_uid}'
							");

							// 주문상태 업데이트
							order_status_update($_ordernum);
						}
                        else if($OPStatus == 'PAYED'){
                            // 2019-03-28 SSJ :: 발주상태에서 결제완료 상태로 되돌아가는것 방지
                        }
                       else { // 기타 상태
                            _MQ_noreturn(" update {$OPTable} set npay_status = '{$OPStatus}' where npay_order_code = '{$OPCode}' and npay_order_group != '' ");
                        }
					// ------------------------------------------------------------------------------------------------------------------------- */
					// 취소처리 솔루션마다 다르게 적용 :: 손으로 작성 하세요.
					// ------------------------------------------------------------------------------------------------------------------------- */
				}
			}
		// -=@ update 동작 처리 @=-

	/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

		// -=@ half_fix 동작 처리 @=-
			/*
				$NPOrder['half_fix'] = array(
					'네이버 주문코드'=>array('네이버 주문상품코드'=>'현재상태', ...),
					...
				)
					- 일단 엔카트(네이버장바구니) 데이터를 모두 정상 주문으로 만들고 수집이 되지 않은 상품만 새로운 필드(불안정 주문상품 여부)에 체크
					- 추후 해당 주문상품이 들어오면 네이버 페이 주문상세 데이터의 ProductID(자사 주문상품코드), MallManageCode(유일값), OptionCode(자사 상품 코드)로 op를 검색 후 ProductOrderID를 기입하고 새로운 필드(불안정 주문상품 여부)를 정상으로 변경
			*/
			if(count($NPOrder['half_fix']) > 0) {
				foreach($NPOrder['half_fix'] as $ok=>$ov) { // 주문단위
					foreach($ov as $pk=>$pv) { // 주문상품단위
						$Ocode = $ok; // 네이버 :: 주문코드
						$OPCode = $pk; // 네이버 :: 주문상품코드
						$OPStatus = $pv; // 네이버 :: 주문상품 상태
						$FindData = array();
						$FindData = $NSync->GetProductOrderInfoList($OPCode);
						$ProductInfo = array();
						$ProductInfo = $FindData['ProductOrder'];
						if(count($ProductInfo) <= 0) continue;
						if(empty($ProductInfo['OptionCode'])) $ProductInfo['OptionCode'] = 0;

						// 패치전 항목은 업데이트 안함
						_MQ_noreturn("
							update
								{$OPTable}
							set
								npay_order_group = '{$Ocode}',
								npay_order_code = '{$OPCode}',
								npay_status = '{$OPStatus}',
								npay_sync = 'A'
							where
								npay_uniq = '{$ProductInfo['MallManageCode']}' and
								npay_order_group != '' and
								{$OPpcode} = '{$ProductInfo['ProductID']}' and
								{$OPPouid} = '{$ProductInfo['OptionCode']}'
						");
					}
				}
			}
		// -=@ half_fix 동작 처리 @=-

	/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */



	/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

		// -=@ half or insert 동작 처리 @=-
			/*
				$NPOrder['half or insert'] = array(
					'네이버 주문코드'=>array('네이버 주문상품코드'=>'현재상태', ...),
					...
				)
			*/
			/*
				// -=@ half 동작 처리 @=-
					- 일단 엔카트(네이버장바구니) 데이터를 모두 정상 주문으로 만들고 수집이 되지 않은 상품만 새로운 필드(불안정 주문상품 여부)에 체크
					- 추후 해당 주문상품이 들어오면 네이버 페이 주문상세 데이터의 ProductID(자사 주문상품코드), MallManageCode(유일값), OptionCode(자사 상품 코드)로 op를 검색 후 ProductOrderID를 기입하고 새로운 필드(불안정 주문상품 여부)를 정상으로 변경

				// -=@ insert 동작 처리 @=-
					- 정상적인 신규 주문으로 자사 네이버페이 장바구니와 연계하여 신규 주문으로 접수 하면됩
			*/
			$NewInsert = array();
			$_paymethodArr = array(
				"신용카드" => "card",
				"신용카드 간편결제" => "card",
				"실시간계좌이체" => "iche",
				"계좌 간편결제" => "iche",
				"무통장입금" => "online",
				"포인트결제" => "point",
				"가상계좌" => "virtual",
				"휴대폰" => "hpp",
				"휴대폰 간편결제" => "hpp",
			);
			if(count($NPOrder['half']) > 0) { foreach($NPOrder['half'] as $hk=>$hv) { $NewInsert[$hk.'_half'] = $hv; } }
			if(count($NPOrder['insert']) > 0) { foreach($NPOrder['insert'] as $ik=>$iv) { $NewInsert[$ik.'_insert'] = $iv; } }
			if(count($NewInsert) > 0) {
				foreach($NewInsert as $ok=>$ov) {
					$NOPArr = $ov; // 네이버페이로 들어온 모든 네이버 주문상품 배열(is_array 비교용)
					$ExpKey = explode('_', $ok);
					$Nkey = $ExpKey[0]; // 주문코드
					$NMode = $ExpKey[1]; // 추가 모드
					$NfirstNOP = reset($NPList[$Nkey]); // 가장 첫 주문상품 정보 조회 :: 주문번호를 직접 조회 할 수 없다.
					$FindData = array();
					$FindData[$NfirstNOP] = $NSync->GetProductOrderInfoList($NfirstNOP);
					$Odata = $FindData[$NfirstNOP]['Order'];
					$OPdata = $FindData[$NfirstNOP]['ProductOrder'];
					$MallManageCode = $OPdata['MallManageCode'];

					// LCY : 2022-04-05 -- 추가 중복 방지 추가 스택 유니크 테이블을 활용 {
					_MQ_noreturn("insert into smart_stack_never(sn_unique_type,sn_unique_value,sn_update_dt,sn_reg_dt) values('npay','".$MallManageCode."',now(),now()) on duplicate key update sn_cnt = sn_cnt +1, sn_update_dt = now() ");
					$stack_chk = 0;
					if( function_exists('mysql_affected_rows') ){
						$stack_chk = mysql_affected_rows();
					}else if( function_exists('mysqli_affected_rows') ) {
						$stack_chk = mysqli_affected_rows();
					}
					if( $stack_chk == 2){ continue; }
					$stack_chk = 0;
					// LCY : 2022-04-05 -- 추가 중복 방지 추가 스택 유니크 테이블을 활용 }

					/*
						ChargeAmountPaymentAmount : 충전금 결제 금액
						CheckoutAccumulationPaymentAmount: 적립금 결제 금액
						GeneralPaymentAmount: 일반 결제수단 최종 결제 금액
						NaverMileagePaymentAmount: 네이버페이 포인트 최종 결제 금액
					*/
					$OrderPrice = ($Odata['ChargeAmountPaymentAmount']?$Odata['ChargeAmountPaymentAmount']:0)+($Odata['CheckoutAccumulationPaymentAmount']?$Odata['CheckoutAccumulationPaymentAmount']:0)+($Odata['GeneralPaymentAmount']?$Odata['GeneralPaymentAmount']:0)+($Odata['NaverMileagePaymentAmount']?$Odata['NaverMileagePaymentAmount']:0)+($Odata['PayLaterPaymentAmount']?$Odata['PayLaterPaymentAmount']:0); // SSJ : 후불결제 PayLaterPaymentAmount 추가 : 2021-08-03
					if($OrderPrice <= 0) { // 결제금액이 0원이면 미결제로 판단 기록 X
						if(isset($NewInsert[$ok])) unset($NewInsert[$ok]);
						if(isset($NPOrder['half'][$Nkey])) unset($NPOrder['half'][$Nkey]);
						if(isset($NPOrder['insert'][$Nkey])) unset($NPOrder['insert'][$Nkey]);
						if(isset($NPList[$Nkey])) unset($NPOrder['insert'][$Nkey]);
						continue;
					}

					// ------------------------------------------------------------------------------------------------------------------------- */
					// 솔루션마다 다르게 적용 :: 손으로 작성 하세요.
					// ------------------------------------------------------------------------------------------------------------------------- */

						// 주문등록 ------------------------------
							$que = "
								select
									c.*, p.*, po.*, pao.*, m.*,
									case c_is_addoption WHEN 'Y' THEN c_addoption_parent else c_pouid end as app_pouid
								from
									smart_npay as c
									left join smart_product as p on (p.p_code = c.c_pcode)
									left join smart_product_option as po on (po.po_uid = c.c_pouid)
									left join smart_product_addoption as pao on (pao.pao_uid = c.c_pouid)
									left join smart_company as m on (m.cp_id = p.p_cpid)
								where
									c.c_uniq = '{$MallManageCode}'
								order by c_rdate asc, c_is_addoption desc
							";
							$ODatas = _MQ_assoc($que);

							// --- 배송비 계산 -------------------------------------------------------------
								$arr_cart = $arr_customer = $arr_delivery = $arr_product_info = array();

								// ----- JJC : 상품별 배송비 : 2018-08-16 -----
									$arr_per_product = array();
									foreach($ODatas as $k=>$v ){
										$arr_per_product[$v['c_pcode']]['sum'] += $v['c_cnt'] * ($v['c_price'] + $v['c_optionprice']);
									}

									foreach($ODatas as $k=>$v ){

										# 장바구니 정보 저장
										foreach($v as $sk=>$sv ){
											$arr_cart[$v['p_cpid']][$v['c_pcode']][$v['c_pouid']][$sk] = $sv;
											$arr_product_info[$v['c_pcode']][$sk] = $sv;
										}

										// 쇼핑몰  배송비 정책을 사용한다.
										if($v['cp_delivery_use'] == "N" || $SubAdminMode === false ) {
											$v['cp_delivery_price'] = $siteInfo['s_delprice'];
											$v['cp_delivery_freeprice'] = $siteInfo['s_delprice_free'];
										}
										$arr_customer[$v['p_cpid']] = array('com_delprice'=>$v['cp_delivery_price'] , 'com_delprice_free'=>$v['cp_delivery_freeprice']);

										// 배송비용 계산을 위한 입점업체별 주문금액합산 - 개별배송 , 무료배송일 경우 가격 포함 하지 않음.
										if( $v['p_shoppingPay_use'] == 'N'){
											$arr_delivery[$v['p_cpid']] += $v['c_cnt'] * ($v['c_price'] + $v['c_optionprice']);
										}
										else if($v['p_shoppingPay_use'] == 'Y'){

											$arr_customer[$v['p_cpid']]['app_delivery_price'] += $v['p_shoppingPay'] * $v['c_cnt'] ;
										}
										 // ----- JJC : 상품별 배송비 : 2018-08-16 -----
										else if($v['p_shoppingPay_use'] == 'P') {
											// ![LCY] 상품별 배송비 오류 패치 p_shoppingPayPfPrice 는 0보다 커야함
											$arr_customer[$v['p_cpid']]['app_delivery_price']  = ($v['p_shoppingPayPfPrice'] == 0 || $v['p_shoppingPayPfPrice']> $arr_per_product[$v['c_pcode']]['sum']?$v['p_shoppingPayPdPrice']:0); // 상품별 배송비 설정 따름.
										}

										# 상품 형태 - 둘다 Y 인경우 both
										$order_type_product = $order_type_coupon = 'N';
										$order_type_product = "Y";
									}
								// ----- JJC : 상품별 배송비 : 2018-08-16 -----

								// --- 업체별 배송비 처리 ---
									if(sizeof(array_filter($arr_delivery)) > 0 ) {
										foreach( array_filter($arr_delivery) as $k=>$v ){
											if($arr_customer[$k]['com_delprice_free'] > 0) {
												$arr_customer[$k]['app_delivery_price'] += ($arr_customer[$k]['com_delprice_free'] > $v ? $arr_customer[$k]['com_delprice'] : 0 ); // 배송비적용
											}
											else {
												$arr_customer[$k]['app_delivery_price'] += $arr_customer[$k]['com_delprice'];//배송비적용
											}
										}
									}
								// --- 업체별 배송비 처리 ---

								$order_type = "product";
								$arr_product_sum = $arr_product = array();
								foreach($arr_cart as $crk=>$crv) {
									unset($del_chk_customer);
									foreach($crv as $k=>$v) {
										unset($option_html , $sum_price);
										foreach($v as $sk => $sv) {
											$option_tmp_name		= !$sv['c_option1'] ? "옵션없음" : $sv['c_option1']." ".$sv['c_option2']." ".$sv['c_option3'];
											$option_tmp_price		= $sv['c_price'] + $sv['c_optionprice'];
											$option_tmp_cnt			= $sv['c_cnt'];
											$option_tmp_sum_price	= $sv['c_cnt'] * ($sv['c_price'] + $sv['c_optionprice']);
											$app_point				= $sv['c_point'];

											# 상품수 , 포인트 , 상품금액
											$arr_product["cnt"] += $option_tmp_cnt;//상품수
											$sum_product_cnt += $option_tmp_cnt ;// |개별배송패치| - 상품갯수를 가져온다 : 해당 코드가 없을 시 추가
											$arr_product["point"] += $app_point ;//포인트
											$arr_product["sum"] += $option_tmp_sum_price;//상품금액
											$sum_price += $option_tmp_sum_price;//상품금액

											$delivery_price = 0;
											if($del_chk_customer <> $crk) {
												$arr_product["delivery"]+=$arr_customer[$crk]['app_delivery_price'];
												$delivery_price = $arr_customer[$crk]['app_delivery_price'];
												$del_chk_customer = $crk;
											}

											$c_cookie = $sv['c_cookie'];
											$npay_uniq = $sv['c_uniq'];

											//$product_delivery_price[$OrderDetail['ProductOrder']['MallManageCode']][$sv['c_pcode']] = $delivery_price; // 2019-04-08 SSJ :: 미사용 변수 숨김처리
											//$product_add_delivery_price[$OrderDetail['ProductOrder']['MallManageCode']][$sv['c_pcode']] = 0; // 2019-04-08 SSJ :: 미사용 변수 숨김처리
										}
									}
								}
								$arr_product_sum = $arr_product;
								$price_total = $arr_product_sum['sum'] + $arr_product_sum['delivery']+$arr_product_sum['add_delivery']; // 실제결제해야할 금액
								$price_delivery = $arr_product_sum['delivery']+$arr_product_sum['add_delivery'];
							// --- 배송비 계산 -------------------------------------------------------------


							// --- 주문정보 생성 -------------------------------------------------------------
								$_ordernum = shop_ordernum_create();

								// 사용자 정보
								$_oname = $_uname = $_rname = $Odata['OrdererName'];
								$_oemail = '';
								$_ohtel = $Odata['OrdererTel1'];
								$_paymethod = $_paymethodArr[$Odata['PaymentMeans']];
								$_ohp = tel_format($_ohtel); $_ohp = explode('-',$_ohp);
								$_ohtel1 = $_uhtel1 = $_rhtel1 = $_ohp[0];
								$_ohtel2 = $_uhtel2 = $_rhtel2 = $_ohp[1];
								$_ohtel3 = $_uhtel3 = $_rhtel3 = $_ohp[2];
								$paydate = substr($Odata['ProductOrder']['ShippingDueDate'],0,10);
								if(trim($OPdata['ShippingAddress']['BaseAddress']) != '') {

									$_rname = $OPdata['ShippingAddress']['Name'];
									if(trim($OPdata['ShippingAddress']['Tel1']) != '') {
										$_rhtel = tel_format($OPdata['ShippingAddress']['Tel1']); $_rhtel = explode('-', $_rhtel);
										$_rhtel1 = $_rhtel[0];
										$_rhtel2 = $_rhtel[1];
										$_rhtel3 = $_rhtel[2];
									}
									$exp_zip = explode('-', $OPdata['ShippingAddress']['ZipCode']);
									$_rzip1 = $exp_zip[0];
									$_rzip2 = $exp_zip[1];
									$_rzonecode = $OPdata['ShippingAddress']['ZipCode'];
									$_raddress = $OPdata['ShippingAddress']['BaseAddress'];
									$_raddress1 = $OPdata['ShippingAddress']['DetailedAddress'];
									$_raddress_doro = $OPdata['ShippingAddress']['BaseAddress'].$Odata['ProductOrder']['ShippingAddress']['DetailedAddress'];
								}

								// 주문자 정보
								$_mid					= $ODatas[0]['c_cookie']; // 주문자
								$_price_real			= $_price_total = $OrderPrice;
								$_price_delivery		= $price_delivery; //배송비
								$_price_supplypoint		= 0;//제공해야할 포인트
								$_price_usepoint		= 0;//포인트사용액
								$_price_coupon_member	= 0;//보너스쿠폰사용액
								$_price_coupon_product	= 0;//상품쿠폰사용액
								$_price_promotion		= 0;//프로모션코드 할인금액 LMH005
								$_price_sale_total		= 0;
								$_price_sale_total		= 0;//프로모션코드 할인금액 추가 LMH005
								$_paystatus				= 'Y';//결제상태
								$_canceled				= 'N';//결제취소상태
								$_status				= '결제확인';//주문상태 -> 결제확인 부터 시작
								$_get_tax				= 'N';	// 현금영수증
								$_paydate				= explode('-', $paydate); // 입금예정일
								$_paybankname			= $_bank; // 입금은행정보
								$_order_type			= $order_type;
								$mobile_order			= ($Odata['PayLocationType']=='MOBILE'?'Y':'N');
								$_row_member				= _MQ(" SELECT * FROM smart_individual WHERE in_id = '{$_mid}' ");
								$_content				=  $OPdata['ShippingMemo'];
								$npay_order				= 'Y'; // 네이버페이로 구매

								// 수취인 휴대폰 번호 보정
								$_rhtel = $OPdata['ShippingAddress']['Tel2'];
								$_rhtel = tel_format($_rhtel);
								$_rhtel = explode('-', $_rhtel);
								$_rhp1 = $_rhtel[0];
								$_rhp2 = $_rhtel[1];
								$_rhp3 = $_rhtel[2];

								$sque = "
									insert smart_order set
										o_ordernum					= '{$_ordernum}',
										o_memtype					= '". ($_row_member['in_id'] ? "Y" : "N") ."',
										o_mid						= '{$_mid}',
										o_oname						= '".addslashes($_oname)."',
										o_otel						= '". tel_format($_uhtel1.'-'.$_uhtel2.'-'.$_uhtel3) ."',
										o_ohp						= '". tel_format($_ohtel1.'-'.$_ohtel2.'-'.$_ohtel3) ."',
										o_oemail					= '".addslashes($_oemail)."',
										o_rname						= '".addslashes($_rname)."',
										o_rtel						= '". tel_format($_rhtel1.'-'.$_rhtel2.'-'.$_rhtel3) ."',
										o_rhp						= '". tel_format($_rhp1.'-'.$_rhp2.'-'.$_rhp3) ."',
										o_rpost						= '{$_rzip1}-{$_rzip2}',
										o_rzonecode					= '{$_rzonecode}',
										o_raddr1					= '".addslashes($_raddress)."',
										o_raddr2					= '".addslashes($_raddress1)."',
										o_raddr_doro				= '".addslashes($_raddress_doro)."',
										o_content					= '".addslashes($_content)."',
										o_price_real				= '". ($_price_real-$_price_sale_total) ."',
										o_price_total				= '". ($_price_real-$_price_delivery) ."',
										o_price_delivery			= '{$_price_delivery}',
										o_price_supplypoint			= '0',
										o_price_usepoint			= '{$_price_usepoint}',
										o_apply_point				= 'N',
										o_price_coupon_individual	= '0',
										o_price_coupon_product		= '0',
										o_paymethod					= '{$_paymethod}',
										o_paystatus					= 'Y',
										o_canceled					= 'N',
										o_status					= '결제완료',
										o_bank						= '{$_paybankname}',
										o_rdate						= '". date('Y-m-d H:i:s', strtotime($Odata['PaymentDate'])) ."',
										o_sendstatus				= '배송대기',
										o_web_mode					= '{$mobile_order}',
										mobile					= '{$mobile_order}',

										npay_order					= '{$npay_order}',
										npay_uniq					= '{$MallManageCode}',
										npay_order_group			= '{$Nkey}'
								";
								_MQ_noreturn($sque);
							// --- 주문정보 생성 -------------------------------------------------------------


							// --- 주문상품정보 생성 -------------------------------------------------------------
								/*
									:: $diff_product 구조 ::
										$diff_product = array(
											'주문번호' => array(
												'op_uid'=>'네이버페이 주문상품코드',
												...
											),
											...
										);
										[50708-30126-89770] => Array(1)
											[F9736-A4360-O5534] => Array(2)
												[6374] => 2019011891958880
												[6373] => 2019011891958890
								*/
								$diff_product = array();
								$diff_product[$_ordernum] = array();
								foreach($NPList[$Nkey] as $npk=>$npv) {
									if($npv == $NfirstNOP) {
										$show_npay = $FindData[$NfirstNOP];
									}
									else {
										$FindData[$npv] = $NSync->GetProductOrderInfoList($npv);
										$show_npay = $FindData[$npv];
									}
									$opkey = 0;
									if(isset($show_npay['ProductOrder']['OptionCode'])) $opkey = $show_npay['ProductOrder']['OptionCode'];
									$diff_product[$_ordernum][$show_npay['ProductOrder']['ProductID']][$opkey] = $npv;
								}
								$arr_product_per_apply = $arr_product_apply = array();
								foreach($ODatas as $k=>$v) {
									// --- 배송비 타입 설정 ---
									$_delivery_type = "입점";
									switch($v['p_shoppingPay_use']){
										case "Y": $_delivery_type ="개별"; $product_delivery_price = $v['p_shoppingPay'] * $v['c_cnt']; break;
										case "N":
											$_delivery_type ="입점";
											$product_delivery_price = ($arr_customer_apply[$v['p_cpid']] > 0 ? 0 : $arr_customer[$v['p_cpid']]['app_delivery_price']); // 입점 기본배송정책 따름.
											$arr_customer_apply[$v['p_cpid']] ++;// 배송비는 1회 적용
										break; // 일괄 추가
										case "F": $_delivery_type ="무료"; $product_delivery_price = 0; break;
										// ----- JJC : 상품별 배송비 : 2018-08-16 -----
										case "P":
										$_delivery_type ="상품별";
										// ![LCY] 상품별 배송비 오류 패치 p_shoppingPayPfPrice 는 0보다 커야함
										$product_delivery_price = ($arr_product_per_apply[$v['c_pcode']] > 0 ? 0 : ( $v['p_shoppingPayPfPrice'] == 0 || $v['p_shoppingPayPfPrice'] >  $arr_per_product[$v['c_pcode']]['sum'] ? $v['p_shoppingPayPdPrice'] : 0 )); // 상품별 배송비 설정 따름.
										$arr_product_per_apply[$v['c_pcode']] ++;// 배송비는 1회 적용
										break;
									// ----- JJC : 상품별 배송비 : 2018-08-16 -----
									}
									// 추가배송비
									$product_add_delivery_price = ($arr_product_apply[$v['c_pcode']] > 0 ? 0 : $op_add_delivery_price[$v['c_pcode']]);
									$arr_product_apply[$v['c_pcode']] ++;

									$NPKey = $diff_product[$_ordernum][$v['c_pcode']][$v['c_pouid']]; // 네이버페이 주문정보
									$NPData = $FindData[$NPKey];

									// 값 보정
									if(empty($NPData['Order']['NaverMileagePaymentAmount'])) $NPData['Order']['NaverMileagePaymentAmount'] = 0;
									if(empty($NPData['Order']['CheckoutAccumulationPaymentAmount'])) $NPData['Order']['CheckoutAccumulationPaymentAmount'] = 0;
									$ssque = "
										insert smart_order_product set
											  op_oordernum			= '{$_ordernum}'
											, op_pcode				= '{$v['c_pcode']}'
											, op_pouid				= '{$v['c_pouid']}'
											, op_option1			= '". mysql_real_escape_string($v['c_option1']) ."'
											, op_option2			= '". mysql_real_escape_string($v['c_option2']) ."'
											, op_option3			= '". mysql_real_escape_string($v['c_option3']) ."'
											, op_supply_price		= '{$v['c_supply_price']}'
											, op_price				= '{$v['c_price']}'
											, op_point				= '{$v['c_point']}'
											, op_cnt				= '{$v['c_cnt']}'
											, op_sendstatus			= '배송대기'
											, op_rdate				= now()
											, op_partnerCode		= '{$v['p_cpid']}'
											, op_comSaleType		= '{$v['p_commission_type']}'
											, op_commission			= '{$v['p_sPersent']}'
											, op_pname				= '". mysql_real_escape_string($v['p_name'])."'
											, op_is_addoption		= '{$v['c_is_addoption']}'
											, op_addoption_parent	= '{$v['c_addoption_parent']}'
											, op_delivery_type		= '{$_delivery_type}'
											, npay_uniq				= '{$v['c_uniq']}'
											, npay_order_code		= '{$NPKey}'
											, npay_order_group		= '{$Nkey}'
											, npay_status			= 'PAYED'
											, npay_point			= '{$NPData['Order']['NaverMileagePaymentAmount']}'
											, npay_point2			= '{$NPData['Order']['CheckoutAccumulationPaymentAmount']}'
											, op_add_delivery_price	= '{$product_add_delivery_price}'
											, op_delivery_price		= '{$product_delivery_price}'
									";

									// 2017-06-16 ::: 부가세율설정 ::: JJC
									$v['p_vat'] = $siteInfo['s_vat_product'] == 'C' ? $v['p_vat'] : $siteInfo['s_vat_product']; // SSJ : 2018-02-10 전체설정이 복합과세일때 상품의 과세설정을 그외는 전체설정을 따른다
									$ssque .= ", op_vat = '". $v['p_vat'] ."'";
									// 2017-06-16 ::: 부가세율설정 ::: JJC

									_MQ_noreturn($ssque);
								}
							// --- 주문상품정보 생성 -------------------------------------------------------------


							// --- 기타설정 -------------------------------------------------------------
								_MQ_noreturn(" delete from smart_npay where c_uniq = '{$MallManageCode}' ");
								include(OD_PROGRAM_ROOT."/shop.order.couponadd_pro.php");


								include(OD_PROGRAM_ROOT."/shop.order.salecntadd_pro.php");


								# 주문상태 업데이트
								order_status_update($_ordernum);
							// --- 기타설정 -------------------------------------------------------------
						// 주문등록 ------------------------------

					// ------------------------------------------------------------------------------------------------------------------------- */
				}
			}
		// -=@ half or insert 동작 처리 @=-

	/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

/* -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */