<?php
// 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19
@ini_set("precision", "20");
@ini_set('memory_limit', '1000M');
include_once('inc.php');


if(count($OpUid) <= 0 && $_mode != 'get_search_excel') error_msg('항목을 선택하시기 바랍니다.');
$toDay = date('YmdHis');
$fileName = iconv('utf-8', 'euc-kr', '부분취소내역');


# 모드별 쿼리 조건
if($_mode == 'get_excel') $s_query = " and op_uid in ('".implode("', '", $OpUid)."') ";
else $s_query = enc('d', $_search_que);
if(!$st) $st = 'op.op_cancel_rdate';
if(!$so) $so = 'desc';
$res = _MQ_assoc("
	select
		* ,
		o.o_otel as ordertel,
		o.o_ohp as orderhtel
	from
		smart_order_product as op left join
		smart_order as o on (o.o_ordernum = op.op_oordernum)
	where (1)
		{$s_query}
	order by {$st} {$so}
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
			<th<?php echo $THStyle; ?>>주문번호</th>
			<th<?php echo $THStyle; ?>>구매상품정보</th>
			<th<?php echo $THStyle; ?>>주문자</th>
			<th<?php echo $THStyle; ?>>E-mail</th>
			<th<?php echo $THStyle; ?>>핸드폰번호</th>
			<th<?php echo $THStyle; ?>>주문일시</th>
			<th<?php echo $THStyle; ?>>취소요청일시</th>
			<th<?php echo $THStyle; ?>>취소처리일시</th>
			<th<?php echo $THStyle; ?>>취소상태</th>
			<th<?php echo $THStyle; ?>>환불금액</th>
			<th<?php echo $THStyle; ?>>고객 요청내용</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($res as $k=>$v) {
			$tmp_content = '';
			$itemName = $v['op_pname'];
			if($v['op_option1']) {   // 해당상품에 대한 옵션내역이 있으면
				$itemName .= " (" . trim($v['op_option1']." ".$v['op_option2']." ".$v['op_option3']).")";
			}
			$itemName .= " " . $v['op_cnt']."개";
			$tmp_content .= $itemName;

			$cancel_status = $v[op_cancel]=='Y' ? '취소완료' : '취소요청중';
			$cancel_total = ( $v[op_price] * $v[op_cnt] ) + $v[op_delivery_price] + $v[op_add_delivery_price] - $v['op_cancel_discount_price'] ;// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC
			$cancel_bank = $ksnet_bank[$v[op_cancel_bank]];
			$cancel_bank_account = $v[op_cancel_bank_account];
			$cancel_bank_name = $v[op_cancel_bank_name];
			$cancel_msg = str_replace(array('<br>', '<br/>', '<br />'), $br, $v['op_cancel_msg']);
			$cancel_rdate = date('Y-m-d H:i:s',strtotime($v[op_cancel_rdate]));
			$cancel_cdate = ( rm_str($v[op_cancel_cdate])>0 ? date('Y-m-d H:i:s',strtotime($v[op_cancel_cdate])) : "");
		?>
			<tr>
				<td<?php echo $TDStyle; ?>><?php echo $v['o_ordernum']; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $tmp_content; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $v['o_oname']; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $v['o_oemail']; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $v['o_ohp']; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $v['o_rdate']; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $cancel_rdate; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $cancel_cdate; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $cancel_status; ?></td>
				<td<?php echo $TDStyle2; ?>><?php echo $cancel_total; ?></td>
				<td<?php echo $TDStyle; ?>><?php echo $cancel_msg; ?></td>
			</tr>
		<?php } ?>
	</tbody>
</table>