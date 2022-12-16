<?php
include_once('inc.php');

if(!$uid || !$op_complain)  error_alt('잘못된 접근입니다.');

$r = _MQ("
	select * from
		smart_order_product as op inner join
		smart_order as o on (o.o_ordernum = op.op_oordernum) left join
		smart_individual as m on (o.o_mid = m.in_id)
	where op_uid = '{$uid}' and op_partnerCode = '{$com_id}'
");

if(!$r)  error_alt('잘못된 접근입니다.');

if($r['op_cancel'] != 'N' && !in_array($op_complain, array('완료/부분취소요청(PG연동)', '완료/부분취소요청(포인트환불)'))) {
	error_alt('이미 부분취소 요청된 주문상품의 상태는 변경할 수 없습니다.');
}

if($r['op_cancel'] == 'Y' && in_array($op_complain, array('완료/부분취소요청(PG연동)', '완료/부분취소요청(포인트환불)'))) {
	error_alt("이미 부분취소 처리된 주문상품의 상태는 변경할 수 없습니다.");
}

if($r['op_complain'] <> $op_complain) {
	_MQ_noreturn(" update smart_order_product set op_complain	= '{$op_complain}' where op_uid='{$uid}' ");
}
error_frame_reload("적용하였습니다.");