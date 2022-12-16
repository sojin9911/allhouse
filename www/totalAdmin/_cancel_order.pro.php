<?php
// 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19
@ini_set("precision", "20");
@ini_set('memory_limit', '1000M');
include_once('inc.php');


if($_mode == 'complete') { // 환불완료처리
	$ordernum = nullchk($ordernum, '잘못된 접근입니다.');
	_MQ_noreturn(" update smart_order set o_moneyback_status = 'complete', o_moneyback = '환불완료', o_moneyback_comdate = now() where o_ordernum = '{$ordernum}' ");
	error_loc_msg("_cancel_order.list.php?_PVSC=${_PVSC}", '환불완료 처리하였습니다.');
}
else if($_mode == 'request') { // 환불요청전환
	$ordernum = nullchk($ordernum, '잘못된 접근입니다.');
	_MQ_noreturn(" update smart_order set o_moneyback_status = 'request', o_moneyback = '환불요청', o_moneyback_comdate = '0000-00-00 00:00:00' where o_ordernum = '{$ordernum}' ");
	error_loc_msg("_cancel_order.list.php?_PVSC=${_PVSC}", '환불요청으로 전환하였습니다.');
}
else if($_mode == 'reset') { // 환불요청취소
	$ordernum = nullchk($ordernum, '잘못된 접근입니다.');
	_MQ_noreturn(" update smart_order set o_canceled = 'N', o_moneyback_status = 'none', o_moneyback = '', o_moneyback_comdate = '0000-00-00 00:00:00' where o_ordernum = '{$ordernum}' ");
	order_status_update($ordernum);
	error_loc_msg("_cancel_order.list.php?_PVSC=${_PVSC}", '환불요청을 취소 하였습니다.');
}
else if($_mode == 'mass') { // 선택 환불완료 처리
	if(count(array_filter($chk_ordernum)) <= 0) error_msg('잘못된 접근입니다.');
	_MQ_noreturn(" update smart_order set o_moneyback_status = 'complete' , o_moneyback = '환불완료', o_moneyback_comdate = now()  where o_ordernum in ('". implode("' , '", array_filter(array_keys($chk_ordernum))) ."') ");
	error_loc_msg("_cancel_order.list.php?_PVSC=${_PVSC}", '환불완료 처리하였습니다.');
}
else if(in_array($_mode, array('select_excel', 'search_excel'))) { // 엑셀다운로드

	$toDay = date('YmdHis');
	$fileName = iconv('utf-8', 'euc-kr', '환불요청내역');

	if($_mode == "select_excel") {
		$app_order_num = implode("','" , array_filter(array_keys($chk_ordernum)));
		$s_query = " and o_ordernum in ('" . $app_order_num . "') ";
	}
	else if($_mode == "search_excel") {
		$s_query = enc('d',$_search_que);
	}
	$r = _MQ_assoc("
		select o.* ,
			(select count(*) from smart_order_product as op where op.op_oordernum=o.o_ordernum) as op_cnt,
			(
				select p.p_name
				from smart_order_product as op
				inner join smart_product  as p on ( p.p_code=op.op_pcode )
				where op.op_oordernum=o.o_ordernum order by op.op_uid asc limit 1
			) as p_info
		from smart_order as o
		where (1)
			{$s_query}
		ORDER BY o_moneyback_date desc
	");


	## Exel 파일로 변환 #############################################
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=$fileName-$toDay.xls");


	# 테이블 스타일
	$THStyle = ' style="color: #333;padding: 10px; border-bottom: 1px solid #c6c6c6; background: #d6d6d6; font-size: 11px;"';
	$TDStyle = ' style="padding: 5px; border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; vertical-align: middle; text-align: center; mso-number-format:\'\@\';"';
	$TDStyle2 = ' style="padding: 5px; border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; vertical-align: middle; text-align: center;"';
	$br = '<br style="mso-data-placement:same-cell;">';
?>
	<table>
		<thead>
			<tr>
				<th<?php echo $THStyle; ?>>NO</th>
				<th<?php echo $THStyle; ?>>환불요청일</th>
				<th<?php echo $THStyle; ?>>환불처리일</th>
				<th<?php echo $THStyle; ?>>환불상태</th>
				<th<?php echo $THStyle; ?>>주문번호</th>
				<th<?php echo $THStyle; ?>>주문자</th>
				<th<?php echo $THStyle; ?>>상품정보</th>
				<th<?php echo $THStyle; ?>>연락처</th>
				<th<?php echo $THStyle; ?>>환불계좌</th>
				<th<?php echo $THStyle; ?>>환불금액</th>
				<th<?php echo $THStyle; ?>>결제방법</th>
				<th<?php echo $THStyle; ?>>결제상황</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($r as $k=>$v) {
				$_num = $k+1;
			?>
				<tr>
					<td<?php echo $TDStyle; ?>><?php echo $_num; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo date('Y-m-d H:i', strtotime($v['o_moneyback_date'])); ?></td>
					<td<?php echo $TDStyle; ?>><?php echo (rm_str($v['o_moneyback_comdate']) > 0? date('Y-m-d H:i', strtotime($v['o_moneyback_comdate'])):'-'); ?></td>
					<td<?php echo $TDStyle; ?>><?php echo ($v['o_moneyback_status'] == 'request'?'환불요청중':($v['o_moneyback_status'] == 'complete'?'환불완료':'')); ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_ordernum']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_oname']; ?> (<?php echo $v['o_mid']; ?>)</td>
					<td<?php echo $TDStyle; ?>><?php echo $v['p_info']; ?> <?php echo ($v['op_cnt'] > 1?' 외 '.($v['op_cnt']-1).'개':''); ?></td>
					<td<?php echo $TDStyle; ?>><?php echo implode(' , ', array_filter(array(trim($v['o_otel']), trim($v['o_ohp'])))); ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_moneyback_comment']; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo ($v['o_price_real'] > 0?number_format($v['o_price_real']).'원':'전액적립금'); ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $arr_payment_type[$v['o_paymethod']]; ?></td>
					<td<?php echo $TDStyle; ?>><?php echo $v['o_status']; ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
<?php
}