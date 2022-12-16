<?php
	/**** SSJ : 정산대기관리 메뉴 개선 패치 : 2021-10-01 ****/

	// --------- JJC : 정산기능분화 : 2021-01-19 ---------

	// 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19
	@ini_set("precision", "20");
	@ini_set('memory_limit', '1000M');
	include_once('inc.php');

	$toDay = date('YmdHis');
	$fileName = "order3_excel";

	if($_mode == 'select_excel'){
		if(count($chk_id) <= 0) error_alt('항목을 선택하시기 바랍니다.');
		$s_query = "
			and op.op_settlementstatus = 'ready' and o.o_paystatus = 'Y' and o.o_canceled = 'N' and o.npay_order = 'N'
			and op.op_partnerCode in ('". implode("', '" , array_keys($chk_id)) ."')
		";
	}else if($_mode == 'search_excel'){
		$s_query = enc('d', $_s_query);
	}else{
		exit;
	}

	// 데이터 요약 조회
	$res = _MQ_assoc("
		select
			cp.cp_charge , cp.cp_tel , cp.cp_tel2 ,
			op_partnerCode ,
			sum( op.op_price * op.op_cnt ) as sum_price,
			IF(
				op.op_comSaleType = '공급가',
				sum(op.op_supply_price * op.op_cnt + op.op_delivery_price + op.op_add_delivery_price) ,
				sum(op.op_price * op.op_cnt + op.op_delivery_price + op.op_add_delivery_price - op.op_price * op.op_cnt * op.op_commission / 100)
			) as comPrice,
			sum(op.op_delivery_price + op.op_add_delivery_price) as delivery_price,
			sum(op.op_usepoint+op.op_use_discount_price+op.op_use_product_coupon) as use_point,
			sum(op.op_cnt) as total_cnt
		FROM smart_order_product AS op
		LEFT JOIN smart_order AS o ON (o.o_ordernum=op.op_oordernum)
		LEFT JOIN smart_company as cp ON (cp.cp_id = op.op_partnerCode)
		WHERE (1)
			{$s_query}
		GROUP BY op.op_partnerCode
		ORDER BY op.op_uid desc
	");
	//ViewArr($res);


	## Exel 파일로 변환 #############################################
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=$fileName-$toDay.xls");
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';


	# 테이블 스타일
	$THStyle = ' style="color: #333;padding: 10px; background: #d6d6d6; font-size: 11px;"';
	$TDStyle = ' style="padding: 5px; vertical-align: middle; text-align: center; mso-number-format:\'\@\';"';
	$TDStyleLeft = ' style="padding: 5px; vertical-align: middle; mso-number-format:\'\@\';"';
	$TDStyle2 = ' style="padding: 5px; vertical-align: middle; text-align: right;"';
	$br = '<br style="mso-data-placement:same-cell;">';

	// 업체정보
	//$arr_customer = arr_company();
	$arr_customer2 = arr_company2();
?>

<table>
	<tbody>

		<!-- 내용 -->
		<tr>
			<td>

				<table border="1">
					<thead>
						<tr>
							<th<?php echo $THStyle; ?>>업체명</th>
							<th<?php echo $THStyle; ?>>업체아이디</th>
							<th<?php echo $THStyle; ?>>담당자</th>
							<th<?php echo $THStyle; ?>>대표번호</th>
							<th<?php echo $THStyle; ?>>휴대폰번호</th>
							<th<?php echo $THStyle; ?>>판매수</th>
							<th<?php echo $THStyle; ?>>총합계</th>
							<th<?php echo $THStyle; ?>>판매액</th>
							<th<?php echo $THStyle; ?>>배송비</th>
							<th<?php echo $THStyle; ?>>판매수수료</th>
							<th<?php echo $THStyle; ?>>할인금액</th>
							<th<?php echo $THStyle; ?>>정산금액</th>
						</tr>
					</thead>
					<tbody>
						<?php
							// 총합계
							$total_sale_cnt = 0;
							$total_sum_price = 0;
							$total_delivery_price = 0;
							$total_com_price = 0;
							$total_use_point = 0;
							$total_fee_price = 0;
							foreach($res as $k=>$v) {

								$sale_cnt = $v['total_cnt'];
								$sum_price = $v['sum_price'];
								$delivery_price = $v['delivery_price'];
								$com_price = $v['comPrice'];
								$use_point = $v['use_point'];
								$fee_price = $sum_price + $delivery_price - $com_price - $use_point;

								// 총합계
								$total_sale_cnt += $sale_cnt;
								$total_sum_price += $sum_price;
								$total_delivery_price += $delivery_price;
								$total_com_price += $com_price;
								$total_use_point += $use_point;
								$total_fee_price += $fee_price;
						?>
								<tr>
									<td<?php echo $TDStyle; ?>><?php echo $arr_customer2[$v['op_partnerCode']]; ?></td>
									<td<?php echo $TDStyle; ?>><?php echo $v['op_partnerCode']; ?></td>
									<td<?php echo $TDStyle; ?>><?php echo $v['cp_charge']; ?></td>
									<td<?php echo $TDStyle; ?>><?php echo $v['cp_tel']; ?></td>
									<td<?php echo $TDStyle; ?>><?php echo $v['cp_tel2']; ?></td>
									<td<?php echo $TDStyle2; ?>><?php echo $sale_cnt; ?></td>
									<td<?php echo $TDStyle2; ?>><?php echo ($sum_price + $delivery_price); ?></td>
									<td<?php echo $TDStyle2; ?>><?php echo $sum_price; ?></td>
									<td<?php echo $TDStyle2; ?>><?php echo $delivery_price; ?></td>
									<td<?php echo $TDStyle2; ?>><?php echo $fee_price; ?></td>
									<td<?php echo $TDStyle2; ?>><?php echo $use_point; ?></td>
									<td<?php echo $TDStyle2; ?>><?php echo $com_price; ?></td>
								</tr>
						<?php } ?>

					</tbody>
				</table>

			</td>
		</tr>

	</tbody>
</table>
