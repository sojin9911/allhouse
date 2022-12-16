<?php
// 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19
@ini_set("precision", "20");
@ini_set('memory_limit', '1000M');
include_once('inc.php');


if(count($OpUid) <= 0)error_msg('항목을 선택하시기 바랍니다.');
$toDay = date('YmdHis');
$fileName = "order3excel";


# 모드별 쿼리 조건
$res = _MQ_assoc("
	select
		op.*, o.*, m.cp_name,
		IF(
			op.op_comSaleType = '공급가',
			(op.op_supply_price * op.op_cnt + op.op_delivery_price + op.op_add_delivery_price),
			(op.op_price * op.op_cnt - op.op_price * op.op_cnt * op.op_commission / 100 + op.op_delivery_price + op.op_add_delivery_price)
		) as comPrice
	from
		smart_order_product as op left join
		smart_order as o on (o.o_ordernum=op.op_oordernum) left join
		smart_company as m on (m.cp_id = op.op_partnerCode)
	where (1) and
		op.op_uid in ('". implode("', '" , $OpUid) ."')
	order by op_partnerCode asc, op_uid desc
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
			<th<?php echo $THStyle; ?>>주문일</th>
			<th<?php echo $THStyle; ?>>결제일</th>
			<th<?php echo $THStyle; ?>>배송완료일</th>
			<th<?php echo $THStyle; ?>>정산대기 전환일</th>
			<th<?php echo $THStyle; ?>>상품명</th>
			<th<?php echo $THStyle; ?>>주문번호</th>
			<th<?php echo $THStyle; ?>>구매합계(상품가*판매량)</th>
			<th<?php echo $THStyle; ?>>배송비</th>
			<th<?php echo $THStyle; ?>>업체수수료</th>
			<th<?php echo $THStyle; ?>>할인액</th>
			<th<?php echo $THStyle; ?>>수수료</th>
			<?php if($siteInfo['s_vat_product'] == 'C') { ?>
				<th<?php echo $THStyle; ?>>과세여부</th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($res as $sk=>$sv) {
			$sum_price = $sv['op_price']*$sv['op_cnt'];
		?>
			<tr>
				<td<?php echo $TDStyle; ?>><?php echo ($sv['op_rdate'] == '0000-00-00 00:00:00'?'-':date('Y.m.d', strtotime($sv['op_rdate']))); ?></td>
				<td<?php echo $TDStyle; ?>><?php echo ($sv['op_paydate'] == '0000-00-00 00:00:00'?'-':date('Y.m.d', strtotime($sv['op_paydate']))); ?></td>
				<td<?php echo $TDStyle; ?>><?php echo ($sv['op_senddate'] == '0000-00-00'?'-':date('Y.m.d', strtotime($sv['op_senddate']))); ?></td>
				<td<?php echo $TDStyle; ?>><?php echo ($sv['op_settlement_reday'] == '0000-00-00 00:00:00'?'-':date('Y.m.d', strtotime($sv['op_settlement_reday']))); ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $sv['op_pname']; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $sv['op_oordernum']; ?></td>
				<td<?php echo $TDStyle2; ?>><?php echo number_format( $sum_price); ?></td>
				<td<?php echo $TDStyle2; ?>><?php echo number_format($sv['op_delivery_price']+$sv['op_add_delivery_price']); ?></td>
				<td<?php echo $TDStyle2; ?>><?php echo number_format($sv['comPrice']); ?></td>
				<!-- SSJ : 정산 할인금액 패치 : 2021-05-14 -- 상품쿠폰 사용액 -->
				<td<?php echo $TDStyle2; ?>><?php echo number_format($sv['op_usepoint']+$sv['op_use_discount_price']+$sv['op_use_product_coupon']); ?></td>
				<!-- SSJ : 정산 할인금액 패치 : 2021-05-14 -- 상품쿠폰 사용액 -->
				<td<?php echo $TDStyle2; ?>><?php echo number_format($sum_price + $sv['op_delivery_price'] + $sv['op_add_delivery_price'] - $sv['comPrice'] - $sv['op_usepoint'] - $sv['op_use_discount_price'] - $sv['op_use_product_coupon']); ?></td>
				<?php if($siteInfo['s_vat_product'] == 'C') { ?>
					<td<?php echo $TDStyle; ?>>
						<?php echo ($sv['op_vat'] == 'N'?'면세':'과세'); ?>
					</td>
				<?php } ?>
			</tr>
		<?php } ?>
	</tbody>
</table>