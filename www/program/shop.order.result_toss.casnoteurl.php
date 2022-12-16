<?php
// SSJ : 토스페이먼츠 PG 모듈 추가 : 2021-02-22
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



// -- 해더 전송 ---- For 4.3.0 <= PHP <= 5.4.0
if (!function_exists('http_response_code'))
{
    function http_response_code($newcode = NULL)
    {
        static $code = 200;
        if($newcode !== NULL)
        {
            header('X-PHP-Response-Code: '.$newcode, true, $newcode);
            if(!headers_sent())
                $code = $newcode;
        }
        return $code;
    }
}
// -- 해더 전송 ----



// 정보 받기
$rawData = file_get_contents("php://input");
$rawData = json_decode($rawData , true);


// 주문번호
$order_no =  $orderId = $rawData['orderId'];
// 처리상태
$status = $rawData['status'];
// 유효성검사를 위한 시크릿키
$secret = $rawData['secret'];


if($order_no <> ''){
	$r = _MQ("select * from smart_order_onlinelog as ol inner join smart_order as o on (o.o_ordernum=ol.ool_ordernum) where ol.ool_ordernum='". $order_no ."' order by ol.ool_uid desc limit 1");
}

if($status == "DONE"){

	// 시크릿키 비교
	if($secret == '' || $r['ool_tid'] <> $secret){
		http_response_code(400);
		exit;
	}

	 /*
	 * 무통장 입금 성공 결과 상점 처리(DB) 부분
	 * 상점 결과 처리가 정상이면 "OK"
	 */
	_MQ_noreturn("
		insert into smart_order_onlinelog (
			ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_deposit_tel, ool_bank_owner
		) values (
			'". $order_no ."', '". $r['o_mid'] ."', now(), '". $secret ."', 'I', now(), '". $r['ool_amount_current'] ."', '". $r['ool_amount_total'] ."', '". $r['ool_account_num'] ."', '". $r['ool_account_code'] ."', '". $r['ool_deposit_name'] ."', '". $r['ool_bank_name'] ."', '". $r['ool_bank_code'] ."', '". $r['ool_escrow'] ."', '". $r['o_hp'] ."', '". $r['ool_bank_owner'] ."'
		)
	");

	// - 2016-09-05 ::: JJC ::: 주문정보 추출 ::: 가상계좌 - 이미 결제가 되었다면 추가 적용을 하지 않게 처리함. ---
	$iosr = get_order_info($order_no);

	if($iosr['o_paystatus'] <> "Y"  ) { // 전액 입금되었다면 진행

		if($r['o_get_tax']=='Y') { // 현금영수증을 신청했고, 승인번호가 발급되었을 경우 DB에 등록
			_MQ_noreturn("
				insert into smart_order_cashlog (
					ocs_ordernum, ocs_member, ocs_date, ocs_tid, ocs_cashnum, ocs_respdate, ocs_msg, ocs_method, ocs_cardnum, ocs_amount, ocs_type, ocs_seqno
				) values (
					'". $order_no ."', '". $r['o_mid'] ."', now(), '". $secret ."', '', now(), '', 'AUTH', '', '". $r['ool_amount_total'] ."', 'virtual', ''
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
			('pg','$order_no','1','','".$r['ool_amount_total']."',curdate(),now(),now(),'3000','".addslashes($cash_product_name)."','')");
		}

		// ----- SSJ : 2020-07-01 : 결제완료/결제취소 일괄처리 -----
		// 공통결제
		//		넘길변수
		//			-> 주문번호 : $ordernum
		$ordernum = $order_no;
		include(OD_PROGRAM_ROOT."/shop.order.result.pro.php"); // ----- SSJ : 2020-07-01 : 결제완료/결제취소 일괄처리 -----
		//if($pay_status == 'N') {echo "FAIL";}// 실패처리
		// ----- SSJ : 2020-07-01 : 결제완료/결제취소 일괄처리 -----
	}

	// 정상처리
	http_response_code();
	exit;

}

else if($status == "CANCELED"){

	 /*
	 * 무통장 입금 성공 결과 상점 처리(DB) 부분
	 * 상점 결과 처리가 정상이면 "OK"
	 */
	_MQ_noreturn("
		insert into smart_order_onlinelog (
			ool_ordernum, ool_member, ool_date, ool_tid, ool_type, ool_respdate, ool_amount_current, ool_amount_total, ool_account_num, ool_account_code, ool_deposit_name, ool_bank_name, ool_bank_code, ool_escrow, ool_deposit_tel, ool_bank_owner
		) values (
			'". $order_no ."', '". $r['o_mid'] ."', now(), '". $secret ."', 'C', now(), '". $r['ool_amount_current'] ."', '". $r['ool_amount_total'] ."', '". $r['ool_account_num'] ."', '". $r['ool_account_code'] ."', '". $r['ool_deposit_name'] ."', '". $r['ool_bank_name'] ."', '". $r['ool_bank_code'] ."', '". $r['ool_escrow'] ."', '". $r['o_hp'] ."', '". $r['ool_bank_owner'] ."'
		)
	");


	// 취소건에대한 처리는 하지 않는다
	//if($r['o_canceled'] == "N") {
	//
	//	// 주문이 결제완료되기 전이라면 강제취소 처리 한다
	//	$osr = _MQ("
	//		select o.* , oc.oc_tid, oc.oc_uid, ( select ool_tid from smart_order_onlinelog where ool_ordernum=o.o_ordernum order by ool_uid desc limit 1 ) as ool_tid
	//		from smart_order as o
	//		left join smart_order_cardlog as oc on (oc.oc_oordernum=o.o_ordernum AND oc.oc_tid !='')
	//		where o.o_ordernum='".$order_no."'
	//	");
	//
	//	// 공통취소
	//	//		넘길변수
	//	//			-> 취소위치 : _loc (관리자일 경우 - admin / 사용자일 경우 - user)
	//	//			-> 주문번호 : _ordernum
	//	//			-> 주문정보 : $osr
	//	//		return 정보
	//	//			-> 성공여부 : cancel_status = Y/N
	//	//			-> 메시지 : cancel_msg
	//	$force_cancel = true;
	//	$_loc = "admin";
	//	$_ordernum = $order_no;
	//	include_once(OD_PROGRAM_ROOT."/pg.cancel.inc.php");
	//
	//}


	// 정상처리
	http_response_code();
	exit;

}else{
	// 에러처리
	http_response_code(400);
	exit;
}



actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행