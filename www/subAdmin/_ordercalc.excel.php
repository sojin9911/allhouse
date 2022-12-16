<?php
// 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19
@ini_set("precision", "20");
@ini_set('memory_limit', '1000M');
include_once('inc.php');



// 기본 날짜 지정 (7일)
if(!$pass_sdate) $pass_sdate = date('Y-m-d', strtotime('-7 day'));
$pass_edate = ($pass_edate?$pass_edate:date('Y-m-d'));


// 저장 파일명 지정
$toDay = date("YmdHis");
$fileName = "ordercalcexcel";


// 검색 조건
//$s_query = " and o.o_paystatus='Y' and o.o_canceled='N' and npay_order = 'N' and ocp.ocp_cpid = '{$com_id}' "; // 기본조건(취소 되지 않고 결제 상태인것) / 네이버페이 제외
$s_query = " and o.o_paystatus='Y' and o.o_canceled='N' and npay_order = 'N' and op.op_partnerCode = '{$com_id}' and op.op_cancel='N' "; // SSJ : 정산 할인금액 패치 : 2021-05-14 -- 부분취소 제외
if($pass_sdate) $s_query .= " and o_rdate >= '{$pass_sdate} 00:00:00' ";
if($pass_edate) $s_query .= " and o_rdate <= '{$pass_edate} 23:59:59' ";


// 합계 변수 초기화
$sum_app_cnt = $sum_tPrice = $sum_payPrice = $sum_dPrice = 0;
// SSJ : 정산 할인금액 패치 : 2021-05-14 -- smart_order_company 제거
$que = "
	select
		sum(op_price*op_cnt) as tPrice,
		sum(if(
			op.op_comSaleType='공급가' ,
			op.op_supply_price * op.op_cnt + op.op_delivery_price + op.op_add_delivery_price  ,
			op.op_price * op.op_cnt - op.op_price * op.op_cnt * op.op_commission/ 100 + op.op_delivery_price + op.op_add_delivery_price
		)) as comPrice,
		sum(op.op_cnt) as tCnt,
		sum(op.op_delivery_price + op.op_add_delivery_price) as dPrice,
		date(o.o_rdate) as sub_orderdate,
		count(*) as cnt,
		(select concat(cp_id, '(', cp_name, ')') from smart_company where cp_id = op.op_partnerCode) as cp_id_name
	from smart_order_product as op
	left join smart_order as o on (o.o_ordernum = op.op_oordernum)
	where (1)
		{$s_query}
	group by sub_orderdate, op.op_partnerCode
	order by sub_orderdate, op.op_partnerCode
";
$res = _MQ_assoc($que);


## Exel 파일로 변환 #############################################
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$fileName-$toDay.xls");
print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");

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
			<th<?php echo $THStyle; ?>>구매금액</th>
			<th<?php echo $THStyle; ?>>판매량</th>
			<th<?php echo $THStyle; ?>>배송비</th>
			<th<?php echo $THStyle; ?>>정산금액</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($res as $sk=>$sv) {

			$sum_app_cnt += $sv['tCnt'];
			$sum_tPrice += $sv['tPrice'];
			$sum_dPrice += $sv['dPrice'];
			$sum_payPrice += $sv['comPrice'];
		?>
			<tr>
				<td<?php echo $TDStyle; ?>><?php echo $sv['sub_orderdate']; ?></td>
				<td<?php echo $TDStyle2; ?>><?php echo number_format($sv['tPrice']); ?></td>
				<td<?php echo $TDStyle2; ?>><?php echo number_format($sv['tCnt']); ?></td>
				<td<?php echo $TDStyle2; ?>><?php echo number_format($sv['dPrice']); ?></td>
				<td<?php echo $TDStyle2; ?>><?php echo number_format($sv['comPrice']); ?></td>
			</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td<?php echo $THStyle; ?>><strong>합계</strong></td>
			<td<?php echo $THStyle; ?>><strong><?php echo number_format($sum_tPrice); ?></strong></td>
			<td<?php echo $THStyle; ?>><strong><?php echo number_format($sum_app_cnt); ?></strong></td>
			<td<?php echo $THStyle; ?>><strong><?php echo number_format($sum_dPrice); ?></strong></td>
			<td<?php echo $THStyle; ?>><strong><?php echo number_format($sum_payPrice); ?></strong></td>
		</tr>
	</tfoot>
</table>