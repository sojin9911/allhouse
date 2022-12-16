<?php

	// ---------- JJC : 부분취소 개선 : 2021-02-10  ----------
		if(!$_SERVER['DOCUMENT_ROOT']) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../'); // dirname(__FILE__) 다음 경로 주의
		include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
		actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


		// 부분취소 전용함수 파일 포함
		include_once($_SERVER['DOCUMENT_ROOT'].'/program/mypage.order.view.func.php');

		$__result = 'OK'; $__result_text = ''; $__result_array = array();

		// 필수정보 없을 경우 오류발생
		if( !$op_uid || !$mode) { $__result = 'FAIL'; $__result_text = '잘못된 접근입니다.'; return_json(); }

		// 회원로그인 or 관리자가 아닌 경우 오류발생
		//if( !(is_login() || is_master()) ){ $__result = 'FAIL'; $__result_text = '잘못된 접근입니다.'; $__result_array = $_COOKIE; return_json(); }

		switch($mode) {




			// --- 부분취소 전 주문상품 정보확인 ---
			case 'product': // 상품정보
				//		- 부분취소 확인 함수 -
				$__result_array = partcancel_before($op_uid);
				if($__result_array['status'] == "fail"){ $__result = 'FAIL'; $__result_text = $__result_array['msg']; }//실패처리
				return_json();
			break;
			// --- 부분취소 전 주문상품 정보확인 ---





			// --- 취소요청한 주문상품정보 ---
			case 'view': // 상품정보

				// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 - op_cancel_discount_price -  ::: JJC --------------------
				$res = _MQ("
					select
						p.p_code, p.p_img_list_square, p.p_name, op.op_price, ( if(op_free_delivery_event_use = 'Y',0,op_delivery_price) ) as op_delivery_price, op.op_add_delivery_price, op.op_cnt,
						concat(op.op_option1,' ',op.op_option2,' ',op.op_option3) as option_name, op.op_pouid,
						op.op_cancel_rdate, op.op_cancel_msg, op.op_cancel_bank, op.op_cancel_bank_account, op.op_cancel_bank_name, op.op_cancel_type ,
						op.op_cancel_discount_price , op.op_cancel_usepoint , op.op_cancel_price
					from smart_order_product as op
					left join smart_product as p on (op.op_pcode = p.p_code)
					left join smart_order as o on (o.o_ordernum = op.op_oordernum)
					where op_oordernum = '".$ordernum."' and op_uid = '".$op_uid."'
				");

				// 취소에 사용된 상품쿠폰
				$cl_res = _MQ(" SELECT cl_price FROM smart_order_coupon_log WHERE cl_type = 'product' AND cl_oordernum = '". $ordernum ."' AND cl_pcode = '". $res['p_code'] ."' AND cl_cancel_opuid = '". $op_uid ."' ");
				$cl_price = $cl_res['cl_price'];

				$p_thumb	= get_img_src('thumbs_s_'.$res['p_img_list_square']); // 상품 이미지
				if($p_thumb=='') $p_thumb = $SkinData['skin_url']. '/images/skin/thumb.gif';

				$__result_array = array(
					'pcode' => $res['p_code'],
					'image' => $p_thumb,
					'name' => $res['p_name'],
					'option' => trim($res['option_name']),
					'addoption' => trim($_addoption_name),
					'price' => number_format($res['op_price'] * $res['op_cnt'] + $_addoption_price),
					'delivery' => number_format($res['op_delivery_price'] + $res['op_add_delivery_price']), // 2016-05-24 추가
					'discount' => number_format($res['op_cancel_discount_price']), // 할인비용 - // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC
					'return' => number_format($res['op_price'] * $res['op_cnt'] + $res['op_delivery_price'] + $res['op_add_delivery_price'] + $_addoption_price - $res['op_cancel_discount_price'] - $cl_price ), // 2016-05-24 추가  // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC
					'cnt' => number_format($res['op_cnt'] + $_addoption_cnt),
					'msg' => $res['op_cancel_msg'],
					'date' => date('Y년 m월 d일',strtotime($res['op_cancel_rdate'])),
					'bank' => $ksnet_bank[$res['op_cancel_bank']],
					'bank_account' => $res['op_cancel_bank_account'],
					'bank_name' => $res['op_cancel_bank_name'],
					'cancel_type' => $res['op_cancel_type'],

					'return_price' => number_format($res['op_cancel_price']),
					'return_point' => number_format($res['op_cancel_usepoint']),
					'return_discount' => number_format($res['op_cancel_discount_price'])

					);
				return_json();
			break;
			// --- 취소요청한 주문상품정보 ---





			// --- 취소요청 ---
			case 'cancel':

				$chk = _MQ("
				  select op.*, o.* , ( if(op_free_delivery_event_use = 'Y',0,op_delivery_price) ) as op_delivery_price  from smart_order_product as op
				  left join smart_order as o on (o.o_ordernum = op.op_oordernum)
				  where op_oordernum = '".$ordernum."' and op_uid = '".$op_uid."'
				");

				$return = partcancel_request($op_uid , $cancel_type , $cancel_bank , $cancel_bank_account , $cancel_bank_name , $save_myinfo , $cancel_msg);
				if($return != ""){ $__result = 'FAIL'; $__result_text = $return; }//실패처리
				else{
					// -- 2019-04-09 SSJ :: 부분취소 요청시 문자연동 문자 연동 ----
					// 문자 발송
					$sms_to = $chk['o_ohp'] ? $chk['o_ohp'] : $chk['o_otel'];
					$sms_pname = trim($chk['op_pname']) . implode(" " , array_filter(array(' '.$chk['op_option1'],$chk['op_option2'],$chk['op_option3'])));
					$arr_sms_replace = array('{주문번호}'=>$ordernum, '{주문상품명}'=>$sms_pname);
					shop_send_sms($sms_to,'cancel_part_request',$arr_sms_replace);
					// -- 2019-04-09 SSJ :: 부분취소 요청시 문자연동 문자 연동 ----
				}

				return_json();
			break;
			// --- 취소요청 ---





			// --- 취소요청삭제 : 원복 ---
			case 'restore': // 상품정보
				//		- 부분취소 원복 함수 -
				$return = partcancel_restore($op_uid);
				if($return != ""){ $__result = 'FAIL'; $__result_text = $return; }//실패처리
				return_json();
			break;
			// --- 취소요청삭제 : 원복 ---




		}


		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
	// ---------- JJC : 부분취소 개선 : 2021-02-10  ----------