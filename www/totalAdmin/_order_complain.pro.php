<?php
include_once('inc.php');

if(!$uid || !$op_complain)  error_alt('잘못된 접근입니다.');

// LCY : 2021-02-05 : DB조회시 비회원 검색 패치(inner join -> left join)
$r = _MQ("
	select *
	from smart_order_product as op
	inner join smart_order as o on (o.o_ordernum = op.op_oordernum)
	left join smart_individual as m on (o.o_mid = m.in_id)
	where op_uid = '{$uid}'
");
$r = array_merge($r, _text_info_extraction("smart_order_product",$r['op_uid']));

if($r['op_cancel'] != 'N' && !in_array($op_complain, array('완료/부분취소요청(PG연동)', '완료/부분취소요청(포인트환불)'))) {
	error_alt('이미 부분취소 요청된 주문상품의 상태는 변경할 수 없습니다.');
}

if($r['op_cancel'] == 'Y' && in_array($op_complain, array('완료/부분취소요청(PG연동)', '완료/부분취소요청(포인트환불)'))) {
	error_alt("이미 부분취소 처리된 주문상품의 상태는 변경할 수 없습니다.");
}

if($r['op_complain'] <> $op_complain) {
	_MQ_noreturn(" update smart_order_product set op_complain = '". ($op_complain == 'reset' ? '' : $op_complain) ."' where op_uid='{$uid}' ");

	// SSJ : 교환반품 메뉴 취소요청 개선 패치 : 2021-11-24 -- 부분취소 전용함수 파일 포함
	include_once($_SERVER['DOCUMENT_ROOT'].'/program/mypage.order.view.func.php');

	if( $op_complain == "완료/부분취소요청(PG연동)" ) {
		//_MQ_noreturn(" update smart_order_product set
		//	op_cancel				= 'R',
		//	op_cancel_type			= 'pg',
		//	op_cancel_rdate			= now(),
		//	op_cancel_bank = '{$r['in_cancel_bank']}',
		//	op_cancel_bank_name = '{$r['in_cancel_bank_name']}',
		//	op_cancel_bank_account = '{$r['in_cancel_bank_account']}',
		//	op_cancel_msg = '{$r['op_complain_comment']}'
		//	where op_uid = '{$uid}'
		//");
		// SSJ : 교환반품 메뉴 취소요청 개선 패치 : 2021-11-24
		$return = partcancel_request($uid , 'pg' , ($r['in_cancel_bank']?$r['in_cancel_bank']:'-') , ($r['in_cancel_bank_account']?$r['in_cancel_bank_account']:'-') , ($r['in_cancel_bank_name']?$r['in_cancel_bank_name']:'-') , 'N' , $r['op_complain_comment']);
		//실패처리
		if($return != ""){
			_MQ_noreturn(" update smart_order_product set op_complain = '". $r['op_complain'] ."' where op_uid='{$uid}' ");
			error_frame_reload("부분취소요청(PG연동) 처리 시 오류가 발생하였습니다. (".$return.")");
		}
	}
	if( $op_complain == "완료/부분취소요청(적립금 환불)" ) {
		//_MQ_noreturn(" update smart_order_product set
		//	op_cancel		= 'R',
		//	op_cancel_type	= 'point',
		//	op_cancel_rdate	= now(),
		//	op_cancel_msg = '{$r['op_complain_comment']}'
		//	where op_uid = '{$uid}'
		//");
		// SSJ : 부분취소 개선 패치 적용 : 2021-11-24
		$return = partcancel_request($uid , 'point' , '' , '' , '' , 'N' , $r['op_complain_comment']);
		//실패처리
		if($return != ""){
			_MQ_noreturn(" update smart_order_product set op_complain = '". $r['op_complain'] ."' where op_uid='{$uid}' ");
			error_frame_reload("부분취소요청(포인트 환불) 처리 시 오류가 발생하였습니다. (".$return.")");
		}
	}
	if( $op_complain == "reset" ) {
		_MQ_noreturn(" update smart_order_product set
			op_cancel		= 'N',
			op_cancel_type	= '',
			op_cancel_msg	= '',
			op_cancel_rdate	= '0000-00-00 00:00:00'
			where op_uid = '{$uid}'
		");
		error_frame_reload("환불요청을 취소하였습니다.");
	}
}
error_frame_reload("적용하였습니다.");