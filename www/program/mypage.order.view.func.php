<?php
	
	// ---------- JJC : 부분취소 개선 : 2021-02-10  ----------
	// mypage.order.view.ajax.php 전용 함수 파일

	// ---------- JJC : 부분취소 개선 : 2021-02-10 : 함수부분  ----------

		// --- json 표준 형태 지정 함수 ---
		function return_json() {
			GLOBAL $__result, $__result_text, $__result_array;
			echo json_encode(array("result"=>$__result,"result_text"=>$__result_text,"data"=>$__result_array)); exit;
		}
		// --- json 표준 형태 지정 함수 ---

		// ------ 부분취소 전용 주문정보 추출 함수 -------
		function partcancel_orderinfo($op_uid) {
			return _MQ("
				select
					p.p_img_list_square, 
					IF(op_free_delivery_event_use = 'Y', 0 , op_delivery_price) AS op_delivery_price , 
					CONCAT(op.op_option1,' ',op.op_option2,' ',op.op_option3) AS option_name,
					o.* , op.*
				FROM smart_order_product AS op
				LEFT JOIN smart_product AS p ON (op.op_pcode = p.p_code)
				LEFT JOIN smart_order AS o ON (o.o_ordernum = op.op_oordernum)
				WHERE 
					op_uid = '". $op_uid ."'
					". (is_login() ? " AND o_mid = '".  get_userid() ."'" : "" ) ."
			");
		}
		// ------ 부분취소 전용 주문정보 추출 함수 -------

		// ------ 부분취소를 위한 주문 -가격- 정보 추출 ------ 
		//			order_row - 주문정보 배열
		function get_order_partcancel_info($order_row) {

			// 환불불가 할인액 추출
			global $arr_order_discount_field;
			$arr_app_discount = array_diff($arr_order_discount_field , array('o_price_coupon_product' => '상품쿠폰','o_price_usepoint' => '적립금 사용'));
			$order_discount = 0;
			if(sizeof($arr_app_discount) > 0 ) {
				foreach( $arr_app_discount as $appdk=>$appdv) { $order_discount += $order_row[$appdk]; }
			}

			return array(

				// -- 주문가 --
					'real_price' => $order_row['o_price_real'] , // 실 결제가 (모든 할인 제외)
					'total_price' => $order_row['o_price_total'] , // 구매총액 (상품가 * 개수)
					'delivery_price' => $order_row['o_price_delivery'] , // 배송총액 (추가배송비 포함)
					'supplypoint_price' => $order_row['o_price_supplypoint'] , // 포인트 제공액(적립액)

				// -- 할인가 --
					// - 할인(O) , 환불(O) , 개별(X) -
					'usepoint_price' => $order_row['o_price_usepoint'] , // 포인트 사용액
					// - 할인(O) , 환불(X) , 개별(X) -
					'order_discount_price' => $order_discount , // 환불불가 할인(보너스쿠폰 + 프로모션코드 + 앱주문 할인)
					// - 할인(O) , 환불(X) , 개별(O) -
					'product_discount_price' => $order_row['o_price_coupon_product'] , // 환불불가 개발할인(상품쿠폰)

				// -- 환불가 --
					'refund_price' => $order_row['o_price_refund'] , // 부분취소시 환불/취소한 금액 - 카드/현금 환불
					'usepoint_refund_price' => $order_row['o_price_usepoint_refund'] , // 부분취소시 환불한 적립금
					'order_discount_refund_price' => $order_row['o_price_discount_refund'] , // 부분취소된 할인액(환불불가, 보너스쿠폰 + 프로모션코드 + 앱주문 할인)
					'product_discount_refund_price' => $order_row['o_price_product_refund'] , // 부분취소된 개별할인액(상품쿠폰)
			);
		}
		// ------ 부분취소를 위한 주문 -가격- 정보 추출 ------ 

		// ------ 부분취소 전 정보확인 함수 -------
		//		- parameter
		//					op_uid : 주문상품 고유번호
		//					partcancel_orderinfo : 주문정보
		function partcancel_before($op_uid , $partcancel_orderinfo = array()) {

			global $siteInfo;

			// 주문 및 주문상품정보 추출
			$r = (sizeof($partcancel_orderinfo) > 0 ? $partcancel_orderinfo : partcancel_orderinfo($op_uid));
			$ordernum = $r['o_ordernum'];
			if( !$ordernum ) { return array('status' => 'fail', 'msg' => '주문정보가 없습니다.'); }

			// 부분취소를 위한 주문 -가격- 정보 추출
			$arr_order = get_order_partcancel_info($r);


			// --- 주문상품정보 ---
				// 환불요청 : 상품쿠폰 가격
					$sum_ids = 0;
					if($arr_order['product_discount_price'] > 0 ) {
						$subres = _MQ(" SELECT COUNT(*) AS cnt FROM smart_order_product WHERE op_oordernum = '". addslashes($ordernum) ."' AND op_cancel = 'N' AND op_pcode='". $r['op_pcode'] ."' ");
						if($subres['cnt'] == 1 ) {
							$sum_ids = _MQ_result(" SELECT cl_price FROM smart_order_coupon_log WHERE cl_type = 'product' AND cl_oordernum = '". addslashes($ordernum) ."' AND cl_cancel_status = 'N' AND cl_pcode = '". $r['op_pcode'] ."' ");
						}
					}

				// 환불요청 : 배송가격
					// 무료인 경우 넘김
					$app_delivery = 0; $subres = array();
					if($r['op_delivery_type'] == '개별') {
						// 개별방식인 경우 배송비 있을 경우 적용 
						$app_delivery = $r['op_delivery_price'] + $r['op_add_delivery_price'];// 배송비 적용
					}
					else if($r['op_delivery_type'] == '입점' && ($r['op_delivery_price'] + $r['op_add_delivery_price']) > 0 ) {
						// 입점방식인 경우 배송비를 넘길 수 있는 취소되지 않은 동일 입점 주문상품이 있는지 확인
						$subres = _MQ(" SELECT COUNT(*) AS cnt FROM smart_order_product  WHERE op_oordernum = '". addslashes($ordernum) ."' AND op_uid != '". addslashes($op_uid) ."' AND op_cancel = 'N' AND op_delivery_type='입점' AND op_partnerCode='". $r['op_partnerCode'] ."' ");
					}
					else if($r['op_delivery_type'] ==='상품별' && ($r['op_delivery_price'] + $r['op_add_delivery_price']) > 0 ) {
						// 상품별방식인 경우 배송비를 넘길 수 있는 취소되지 않은 동일 상품코드 주문상품이 있는지 확인
						$subres = _MQ(" SELECT COUNT(*) AS cnt FROM smart_order_product  WHERE op_oordernum = '". addslashes($ordernum) ."' AND op_uid != '". addslashes($op_uid) ."' AND op_cancel = 'N' AND op_delivery_type='상품별' AND op_pcode='". $r['op_pcode'] ."' ");
					}
					if( $subres['cnt'] == 0 ) { $app_delivery = $r['op_delivery_price'] + $r['op_add_delivery_price']; } // 배송비 적용


				// 환불요청 : 주문상품 가격
				$sum_pg = ($r['op_price'] * $r['op_cnt']) + $app_delivery - $sum_ids ;

				// 환불요청 : 사용 포인트 가격
				$sum_po = $r['op_usepoint'];

				// 환불요청 : 사용 할인 가격
				$sum_ds = $r['op_use_discount_price'];
			// --- 주문상품정보 ---


			// --- 환불정보 ---
				$pg_price = ($arr_order['real_price'] - $arr_order['refund_price']); // 환불가능액
				$po_price = ($arr_order['usepoint_price'] - $arr_order['usepoint_refund_price']); // 환불가능 사용포인트
				$ds_price = ($arr_order['order_discount_price'] - $arr_order['order_discount_refund_price']); // 사용할인액(취소가능한도)

				// --- 환불방식에 따른 할인처리 (포인트사용 , 개별쿠폰, 프로모션코드, 앱할인)  ---
				//			최종환불방식 - o_paycancel_method:B
				if($r['o_paycancel_method'] == "B") {

					// PG(실결제) 처리
					$pass_pg = $pg_price - $sum_pg; // 남은 PG - 요청 PG
					$app_pg = ( $pass_pg > 0 ? $pass_pg : 0); // 실제 남은 PG . 0 이하일 경우 0으로 적용
					$diff_pg = ($pass_pg < 0 ? $pass_pg * -1 : 0); // PG가 마이너스일 경우  PO로 넘김

					// PO(포인트) 처리
					$pass_po = $po_price - $diff_pg ;
					$app_po = ($pass_po > 0 ? $pass_po : 0);
					$diff_po = ($pass_po < 0 ? $pass_po * -1 : 0); // PO가 마이너스일 경우  DS로 넘김

					// DS(할인액) 처리 (PO에서 처리하지 못한 값 처리)
					$pass_ds = $ds_price - $diff_po;
					$app_ds = ($pass_ds > 0 ? $ds_price : $diff_po);
					$diff_ds = ($pass_ds < 0 ? $pass_ds * -1 : 0);

					$__result_array1 = array(

						// 요청
							'req_pg' => $sum_pg , // 요청 PG
							'req_po' => $sum_po , // 요청 PO
							'req_ds' => $sum_ds , // 요청 DS
							'req_ids' => $sum_ids , // 요청 IDS

						// 진행
							'ing_pg_pay' => ($pg_price > 0 ? ($sum_pg - $diff_pg) : 0) , // 지급 PG
							'ing_pg_remain' => $app_pg , // 남은 PG
							'ing_po_pay' => ($pass_po > 0 ? $diff_pg : $po_price) , // 지급 PO
							'ing_po_remain' => $app_po , // 남은 PO
							'ing_ds_pay' => ($pass_ds > 0 ? $diff_po : $ds_price) , // 지급 DS
							'ing_ds_remain' => $app_ds , // 남은 DS

						// 결과
							'res_pg' => ($arr_order['refund_price'] + ($pg_price > 0 ? ($sum_pg - $diff_pg) : 0)) , // 결과 PG
							'res_po' => ($arr_order['usepoint_refund_price'] + ($pass_po > 0 ? $diff_pg : $po_price)) , // 결과 PO
							'res_ds' => ($arr_order['order_discount_refund_price'] + ($pass_ds > 0 ? $diff_po : $ds_price)) , // 결과 DS
							'res_ids' => ($arr_order['product_discount_refund_price'] + ($pass_ids > 0 ? $diff_ds : $ids_price)) , // 결과 IDS
					);
				}
				//			분배환불방식 - o_paycancel_method:D
				else {
					$__result_array1 = array(
						// 요청
							'req_pg' => $sum_pg , // 요청 PG
							'req_po' => $sum_po , // 요청 PO
							'req_ds' => $sum_ds , // 요청 DS
							'req_ids' => $sum_ids , // 요청 IDS

						// 진행
							'ing_pg_pay' => $sum_pg , // 지급 PG
							'ing_pg_remain' => ($pg_price - $sum_pg) , // 남은 PG
							'ing_po_pay' => $sum_po , // 지급 PO
							'ing_po_remain' => ($po_price - $sum_po) , // 남은 PO
							'ing_ds_pay' => $sum_ds , // 지급 DS
							'ing_ds_remain' => ($ds_price - $sum_ds) , // 남은 DS

						// 결과(환불된 가격)
							'res_pg' => ($arr_order['refund_price'] + $sum_pg ) , // 결과 PG
							'res_po' => ($arr_order['usepoint_refund_price'] + $sum_po ) , // 결과 PO
							'res_ds' => ($arr_order['order_discount_refund_price'] + $sum_ds ) , // 결과 DS
							'res_ids' => ($arr_order['product_discount_refund_price'] + $sum_ids ) , // 결과 IDS
					);
				}
				// --- 환불방식에 따른 할인처리 (포인트사용 , 개별쿠폰, 프로모션코드, 앱할인)  ---

				//ViewArr("
				//		---------------------------------------------------------------
				//		요청
				//			- 요청 PG : ". $sum_pg ."
				//			- 요청 PO : ". $sum_po ."
				//			- 요청 DS : ". $sum_ds ."
				//			- 요청 IDS : ". $sum_ids ."
				//		---------------------------------------------------------------
				//		진행
				//			- 지급 PG : ". ($pg_price > 0 ? ($sum_pg - $diff_pg) : 0) ."
				//			- 남은 PG : {$pass_pg} ". ($pass_pg > 0 ? "" : " -> " . $app_pg) ."
				//			---------------------
				//			- 지급 PO : ". ($pass_po > 0 ? $diff_pg : $po_price) ."
				//			- 남은 PO : {$pass_po} ". ($pass_po > 0 ? "" : " -> " . $app_po) ."
				//			---------------------
				//			- 지급 DS : ". ($pass_ds > 0 ? $diff_ds : $ds_price) ."
				//			- 남은 DS : {$pass_ds} ". ($pass_ds > 0 ? "" : " -> " . $app_ds) ."
				//		---------------------------------------------------------------
				//		결과
				//			- 환불된 PG : " . ($arr_order['refund_price'] + ($pg_price > 0 ? ($sum_pg - $diff_pg) : 0)) . "
				//			- 환불된 PO : " . ($arr_order['usepoint_refund_price'] + ($pass_po > 0 ? $diff_pg : $po_price)) . "
				//			- 환불된 DS : " . ($arr_order['order_discount_refund_price'] + ($pass_ds > 0 ? $diff_ds : $ds_price)) . "
				//			- 환불된 IDS : " . ($arr_order['product_discount_refund_price'] + ($pass_ids > 0 ? $diff_ids : $ids_price)) . "
				//		---------------------------------------------------------------
				//");



				// 상품 이미지
				$p_thumb	= get_img_src('thumbs_s_'. $r['p_img_list_square']); 
				if($p_thumb=='') $p_thumb = $SkinData['skin_url']. '/images/skin/thumb.gif';

				// 이니시스일 경우 한 주문당 최대 9회 부분취소 가능
				$av_check = true;
				if( $siteInfo['s_pg_type']=='inicis' ) { 
					$av_cnt = _MQ(" select count(*) as cnt from smart_order_product where op_cancel != 'N' and op_cancel_type = 'pg' and op_oordernum = '".$ordernum."' ");
					if( $av_cnt['cnt'] > 8 ) { $av_check = false; }
				}
				if( $r['o_paymethod'] == 'point' ) { $av_check = false; }

				$app_point = $__result_array1['ing_po_pay'];
				$app_discount = $__result_array1['ing_ds_pay'] + $__result_array1['req_ids'];
				$app_product_coupon = $__result_array1['req_ids'];

				$__result_array2 = array(
					// - 요청 상품정보 -
						'pcode' => $r['op_pcode'], //상품코드
						'image' => $p_thumb, // 상품이미지
						'name' => $r['op_pname'], // 주문 상품명
						'option' => trim($r['option_name']), // 주문 옵션명
						'addoption' => trim($_addoption_name), // 추가 옵션명
						'price' => number_format($r['op_price'] * $r['op_cnt'] + $_addoption_price), // 주문 옵션가격 ( 합계 )
						'cnt' => number_format($r['op_cnt'] + $_addoption_cnt), // 주문 옵션 개수
						'delivery' => number_format($app_delivery), // 배송비
						'discount' => number_format($app_discount) , // 할인비용(사용할인액 + 상품쿠폰액) // 사용포인트 제외
						'pg_check' => ($av_check===true?'Y':'N') , // 환불방식

					// - 요청 상품의 환불정보 -
						'return' => number_format($r['op_price'] * $r['op_cnt'] + $_addoption_price + $app_delivery - $app_discount), // 환불가능액 + 환불포인트
						'return_price' => number_format($r['op_price'] * $r['op_cnt'] + $_addoption_price + $app_delivery - $app_point - $app_discount), // 환불가능액
						'return_point' => number_format($app_point * 1) , // 환불 포인트 --> 적립급으로 제공하여야 함. 
						'return_discount' => number_format($app_discount * 1) , // return 할인액 - 환불되지 않음
						'return_product_coupon' => number_format($app_product_coupon * 1) , // return 할인액 - 상품쿠폰
				);

				$__result_array = array_merge($__result_array1 , $__result_array2);

				return $__result_array;

			// --- 환불정보 ---
		}
		// ------ 부분취소 전 정보확인 함수 -------



		// ----- 부분취소 요첟 함수 -----
		//		- return
		//				return 내용이 없을 경우 정상처리 , 문구가 있을 경우 오류( 문구 그대로 노출 )
		//		- parameter
		//				op_uid : 주문상품고유정보
		//				cancel_type - pg, point :환불방식
		//				cancel_bank - 환불은행
		//				cancel_bank_name - 환불예금주
		//				cancel_bank_account - 환불계좌
		//				save_myinfo - 나의 정보에 함께 저장하기
		//				cancel_msg - 전달내용
		function partcancel_request($op_uid , $cancel_type , $cancel_bank , $cancel_bank_account , $cancel_bank_name , $save_myinfo , $cancel_msg) {

			global $siteInfo , $arr_refund_payment_type;

			// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC --------------------
			// 주문정보 추출
			$res = partcancel_orderinfo($op_uid);
			$ordernum = $res['o_ordernum'];
			if( !$ordernum ) { return '주문정보가 없습니다.'; }

			if($res['op_cancel']!='N') { return "취소요청할 수 없는 주문입니다."; }
			if( in_array($res['o_paymethod'],$arr_refund_payment_type) && (!$cancel_bank || !$cancel_bank_name || !$cancel_bank_account) && $cancel_type=='pg' ) { return "환불계좌정보를 입력하세요."; }

            // JJC : 간편결제 - 페이플 : 2021-06-05 - 부분취소불가
            if($res['o_paymethod'] == "payple") {echo "페이플 간편결제 주문의 경우 부분 취소할 수 없습니다."; exit; }


			//		- 부분취소 확인 함수 -
			$__result_array = partcancel_before($op_uid , $res);

			// 공통 Query
			$sque = " WHERE op_oordernum = '". $ordernum ."' AND op_uid != '". $op_uid ."' AND op_cancel = 'N' ";


			// --- 상품쿠폰 업데이트 처리 ---
			if(rm_str($__result_array['return_product_coupon']) > 0 ) {
				$subres = _MQ(" SELECT COUNT(*) AS cnt FROM smart_order_product  {$sque} AND op_pcode='". $res['op_pcode'] ."' ");
				if($subres['cnt'] == 0 ) {
					_MQ_noreturn(" 
						UPDATE smart_order_coupon_log SET 
							cl_cancel_status = 'Y' , 
							cl_cancel_opuid = '". addslashes($op_uid) ."' 
						WHERE
							cl_type = 'product' AND
							cl_oordernum = '". $ordernum ."' AND 
							cl_cancel_status = 'N' AND 
							cl_pcode = '". $res['op_pcode'] ."'
					");
				}
			}
			// --- 상품쿠폰 업데이트 처리 ---


			// --- 배송비 처리 ---
				// 무료인 경우 넘김
				$add_que = ""; // 추가 query$subres = array();
				if($res['op_delivery_type'] == '입점' && ($res['op_delivery_price'] + $res['op_add_delivery_price']) > 0 ) {
					// 입점방식인 경우 배송비를 넘길 수 있는 취소되지 않은 동일 입점 주문상품이 있는지 확인
					$subres = _MQ(" SELECT op_uid FROM smart_order_product {$sque} AND op_delivery_type='입점' AND op_partnerCode='". $res['op_partnerCode'] ."' ORDER BY op_uid LIMIT 0, 1 ");
				}
				else if($res['op_delivery_type'] ==='상품별' && ($res['op_delivery_price'] + $res['op_add_delivery_price']) > 0 ) {
					// 상품별방식인 경우 배송비를 넘길 수 있는 취소되지 않은 동일 상품코드 주문상품이 있는지 확인
					$subres = _MQ(" SELECT op_uid FROM smart_order_product  {$sque} AND op_delivery_type='상품별' AND op_pcode='". $res['op_pcode'] ."' ORDER BY op_uid LIMIT 0, 1 ");
				}
				if($subres['op_uid']) { 
					$add_que = " op_delivery_price = 0 , op_add_delivery_price = 0 , "; // 추가 query
					_MQ_noreturn(" 
						UPDATE smart_order_product SET 
							op_delivery_price = ".$res['op_delivery_price']." , 
							op_add_delivery_price = ".$res['op_add_delivery_price']." 
						WHERE 
							op_uid ='". $subres['op_uid'] ."' 
					");
				}
			// --- 배송비 처리 ---


			// --- 할인비용 Query ---
			//			취소 사용포인트
			$add_que .= " op_cancel_price = '". rm_str($__result_array['return_price']) ."' , "; // 추가 query
			//			포인트 분할액 , 취소 포인트액 변경
			$add_que .= " op_usepoint = '". rm_str($__result_array['return_point']) ."' , op_cancel_usepoint = '". rm_str($__result_array['return_point']) ."' , "; // 추가 query
			//			할인액 분할액 , 취소 할인액 변경
			$add_que .= " op_use_discount_price = '". rm_str($__result_array['ing_ds_pay']) ."' , op_cancel_discount_price = '". rm_str($__result_array['ing_ds_pay'])  ."' ,  "; // 추가 query


			// --- 주문상품 처리 ---
			_MQ_noreturn("
				UPDATE smart_order_product set
					" . $add_que . "
					op_cancel = 'R',
					op_cancel_msg = '".$cancel_msg."',
					op_cancel_bank = '".$cancel_bank."',
					op_cancel_bank_name = '".$cancel_bank_name."',
					op_cancel_bank_account = '".$cancel_bank_account."',
					op_cancel_rdate = now(),
					op_cancel_type = '".$cancel_type."',
					op_cancel_mem_type = '".$cancel_mem_type."'
				WHERE
					op_oordernum = '".$ordernum."' AND
					op_uid = '".$op_uid."'
			");


			// --- 주문 처리 ---
			_MQ_noreturn("
				UPDATE smart_order set
					o_price_refund = o_price_refund + '". rm_str($__result_array['return_price']) ."' ,
					o_price_to_usepoint_refund = o_price_to_usepoint_refund + '". ($cancel_type == "point" ? rm_str($__result_array['return_price']) : 0) ."' ,
					o_price_usepoint_refund = o_price_usepoint_refund + '". rm_str($__result_array['return_point']) ."' ,
					o_price_discount_refund = o_price_discount_refund + '". rm_str($__result_array['ing_ds_pay']) ."' ,
					o_price_product_refund = o_price_product_refund + '". rm_str($__result_array['return_product_coupon']) ."'
				WHERE
					o_ordernum = '".$ordernum."'
			");


			// --- 최종환불방식 - o_paycancel_method:B 일 경우 분할 처리---
			//			반드시 주문상품 및 주문처리 후 처리
			partcancel_subdivision($op_uid );
			// --- 최종환불방식 - o_paycancel_method:B 일 경우 분할 처리---


			// --- 나의 정보에 함께 저장하기 시 ---
			if($cancel_type == 'pg' && $save_myinfo=='Y') {
				_MQ_noreturn(" 
					UPDATE smart_individual SET
						in_cancel_bank = '".$cancel_bank."',
						in_cancel_bank_name = '".$cancel_bank_name."',
						in_cancel_bank_account = '".$cancel_bank_account."'
					WHERE 
						in_id = '" . $res['o_mid'] . "'
				");
			}

			return ;

		}
		// ----- 부분취소 요청 함수 -----



		// ----- 부분취소 원복 함수 -----
		function partcancel_restore($op_uid) {

			// 주문정보 추출
			$res = partcancel_orderinfo($op_uid);
			$ordernum = $res['o_ordernum'];
			if( !$ordernum ) { return '주문정보가 없습니다.'; }


			if( $res['op_cancel'] <> 'R') { return '부분취소 요청에 대한 삭제를 실행할 수 없는 상태입니다.'; }


			// --- 상품쿠폰 처리 ---
			if($res['o_price_coupon_product'] > 0 ) {

				// 공통 Query 
				$cl_sque = "
					WHERE 
						cl_type = 'product' AND 
						cl_cancel_status = 'Y' AND 
						cl_oordernum = '". $ordernum ."' AND 
						cl_pcode = '". $res['op_pcode'] ."' AND 
						cl_cancel_opuid = '". $res['op_uid'] ."'
				";

				// 복원된 쿠폰합계액 추출
				$sum_ids = _MQ_result(" SELECT  cl_price  FROM smart_order_coupon_log {$cl_sque}");

				// 상품쿠폰 복원 처리
				_MQ_noreturn("  UPDATE smart_order_coupon_log SET  cl_cancel_status = 'N' ,  cl_cancel_opuid = '0' {$cl_sque} ");
			}


			// --- 배송비 처리 ---
			//		!!! 취소요청 주문상품 중 배송비가 있는 경우 별도 처리히지 않음. 그대로 둠 !!!
			// --- 배송비 처리 ---


			// --- 주문 처리 ---
			//		반드시 주문상품 처리 전 처리
			_MQ_noreturn("
				UPDATE smart_order set
					o_price_refund = o_price_refund - '". $res['op_cancel_price'] ."' ,
					o_price_to_usepoint_refund = o_price_to_usepoint_refund - '". ($res['op_cancel_type'] == "point" ? $res['op_cancel_price'] : 0) ."' ,
					o_price_usepoint_refund = o_price_usepoint_refund - '". $res['op_cancel_usepoint'] ."' ,
					o_price_discount_refund = o_price_discount_refund - '". $res['op_cancel_discount_price'] ."' ,
					o_price_product_refund = o_price_product_refund - '". $sum_ids ."'
				WHERE
					o_ordernum = '".$ordernum."'
			");


			// --- 주문상품 처리 ---
			_MQ_noreturn("
				UPDATE smart_order_product SET
					op_cancel = 'N',
					op_cancel_msg = '',
					op_cancel_bank = '',
					op_cancel_bank_name = '',
					op_cancel_bank_account = '',
					op_cancel_rdate = '0000-00-00 00:00:00',
					op_cancel_type = '',
					op_cancel_discount_price = 0 ,
					op_cancel_price = 0,
					op_cancel_usepoint = 0
				WHERE 
					op_oordernum = '".$ordernum."' AND 
					op_uid = '".$op_uid."'
			");


			// --- 최종환불방식 - o_paycancel_method:B 일 경우 분할 처리---
			//			반드시 주문상품 및 주문처리 후 처리
			partcancel_subdivision($op_uid );
			// --- 최종환불방식 - o_paycancel_method:B 일 경우 분할 처리---

			return;

		}
		//		- 부분취소 원복 함수 -




		// --- 부분취소 시 재분할 처리( 사용할인액, 사용포인트 ) ---
		//			반드시 주문상품 및 주문처리 후 처리
		function partcancel_subdivision($op_uid , $res = array()) {

			// 주문정보 추출
			if(sizeof($res) == 0 ) { $res = partcancel_orderinfo($op_uid); }
			$ordernum = $res['o_ordernum'];
			if( !$ordernum ) { return '주문정보가 없습니다.'; }

			// - 최종환불방식 - o_paycancel_method:B 일 경우 분할 처리
			if($res['o_paycancel_method'] == "B") {


				// 부분취소를 위한 주문 -가격- 정보 추출
				$arr_order = get_order_partcancel_info($res);


				// 공통 Query
				$sque = " WHERE op_oordernum = '". $ordernum ."' AND op_cancel = 'N' ";


				// --- 할인액 분할 처리 ---
				$app_sum_pg = $arr_order['real_price'] - $arr_order['refund_price']; // 총 비용
				$app_sum_po = $arr_order['usepoint_price'] - $arr_order['usepoint_refund_price'] ; // 총 사용포인트
				$app_sum_ds = $arr_order['order_discount_price'] - $arr_order['order_discount_refund_price']; // 총 사용할인액
				$app_total = $arr_order['total_price'] + $arr_order['delivery_price'];

				//	// --- 상품쿠폰 추출 및 배열화 ---
				//	$arr_clprice = array(); $app_clpsum= 0;
				//	$clres = _MQ_assoc(" SELECT cl_price , cl_pcode FROM smart_order_coupon_log WHERE cl_type = 'product' AND cl_oordernum = '". $ordernum ."' AND cl_cancel_status = 'N' ");
				//	foreach( $clres as $clk=>$clv) {
				//		$arr_clprice[$clv['cl_pcode']] = $clv['cl_price'];
				//		$app_clpsum += $clv['cl_price'];
				//	}
				//	// --- 상품쿠폰 추출 및 배열화 ---


				// - 사용할인액 / 사용포인트 개별분배 - 
				//		사용할인액이 사용포인트 계산에 앞서 처리되어야 함.
				$sum_ds= 0; $sum_pg= 0; // 합계
				$opres = _MQ_assoc("SELECT * , ( (op_price * op_cnt) + op_delivery_price + op_add_delivery_price) AS app_sum  FROM smart_order_product {$sque} ");
				foreach($opres as $sk=>$sv){
					// -- 사용할인액 --
						// 평균 사용할인액 추출
						//$app_pg_price = ($sv['app_sum'] - $arr_clprice[$sv['op_pcode']]); // 할인쿠폰 제외
						$app_pg_price = $sv['app_sum']; 
						$app_ds = round( $app_pg_price * $app_sum_ds / $app_total);
						$app_ds = ($app_pg_price >= $app_ds ? $app_ds : $app_pg_price);

					// -- 사용포인트 --
						// 평균 사용포인트 추출
						$app_po = round( $app_pg_price * $app_sum_po / $app_total);
						$app_po = ($app_pg_price >= $app_po ? $app_po : $app_pg_price);

					$ssque = "UPDATE smart_order_product SET op_use_discount_price = '". $app_ds ."' , op_usepoint = '". $app_po ."' WHERE op_uid='". $sv['op_uid'] ."'";
					_MQ_noreturn($ssque);

					$sum_pg += $app_po; // 사용포인트 누적
					$sum_ds += $app_ds; // 사용할인액 누적

				}
				// - 사용할인액 / 사용포인트 개별분배 - 

				// 사용할인액 차액 있을 경우 처리
				if( $app_sum_ds <> $sum_ds ) {
					$opres = _MQ("SELECT op_uid FROM smart_order_product {$sque} ORDER BY op_use_discount_price DESC LIMIT 0, 1 ");
					if(sizeof($opres) > 0 ) {
						_MQ_noreturn(" UPDATE smart_order_product SET op_use_discount_price = op_use_discount_price + " . ($app_sum_ds - $sum_ds)  ." WHERE op_uid='". $opres['op_uid'] ."' ");
					}
				}

				// 사용포인트 차액 있을 경우 처리
				if( $app_sum_po <> $sum_pg ) {
					$opres = _MQ("SELECT op_uid FROM smart_order_product {$sque} ORDER BY op_usepoint DESC LIMIT 0, 1 ");
					if(sizeof($opres) > 0 ) {
						_MQ_noreturn(" UPDATE smart_order_product SET op_usepoint = op_usepoint + " . ($app_sum_po - $sum_pg) ." WHERE op_uid='". $opres['op_uid'] ."' ");
					}
				}

			}
		}
		// --- 부분취소 시 재분할 처리( 사용할인액, 사용포인트 ) ---

	// ---------- JJC : 부분취소 개선 : 2021-02-10 : 함수부분  ----------