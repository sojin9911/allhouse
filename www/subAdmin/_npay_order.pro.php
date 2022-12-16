<?php
// 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19
@ini_set("precision", "20");
@ini_set('memory_limit', '1000M');

include_once('inc.php');


# 네이버페이 결제 데이터 엑셀다운로드
if(in_array($_mode, array('get_excel', 'get_search_excel'))) { // Excel 다운로드 Start


	# 재귀 && 입점업체 조건을 위한 어드민 구분 판별
	$AdminPathData = parse_url($_SERVER['REQUEST_URI']);
	$AdminPathData = explode('/', $AdminPathData['path']);
	$AdminPath = $AdminPathData[1]; unset($AdminPathData); // 'totalAdmin' or 'subAdmin'

	$toDay = date('YmdHis', time());
	$fileName = 'npay_order_list';
	if(!$test) {

		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$fileName-$toDay.xls");
	}

	# 모드별 쿼리 조건
	if($_mode == 'get_excel') $s_query = " where (1) and `o`.`npay_order` = 'Y' and `op`.`op_uid` in ('".implode("', '", $op_uid)."') ";
	else $s_query = enc('d', $_search_que);


	# 쿼리
	$que = "
		select
			`op`.*,
			`o`.*,
			`o_rtel` as `ordertel`,
			`o_rhp` as `orderhtel`,
			`p_name`
		from
			`smart_order_product` as `op` left join
			`smart_order` as `o` on(`o`.`o_ordernum` = `op`.`op_oordernum`) left join
			`smart_product` as `p` on(`op`.`op_pcode` = `p`.`p_code`)
			{$s_query}
		order by `o_rdate` desc
	";
	$res = _MQ_assoc($que);

	# 공금업체 리스트 추출
	$arr_customer = arr_company();

	# 테이블 스타일
	$THStyle = ' style="color: #333;padding: 10px; border-bottom: 1px solid #c6c6c6; background: #d6d6d6; font-size: 11px;"';
	$TDStyle = ' style="padding: 5px; border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; vertical-align: middle; text-align: center; mso-number-format:\'\@\';"';
	$TDStyle2 = ' style="padding: 5px; border-left: 1px solid #c6c6c6; border-bottom: 1px solid #c6c6c6; vertical-align: middle; text-align: center;"';
	$br = '<br style="mso-data-placement:same-cell;">';
?>
	<table>
		<thead>
			<tr>
				<th<?php echo $THStyle; ?>>SerialNum</th>
				<th<?php echo $THStyle; ?>>주문일</th>
				<th<?php echo $THStyle; ?>>주문번호</th>
				<th<?php echo $THStyle; ?>>NPAY CODE</th>
				<th<?php echo $THStyle; ?>>상품정보</th>
				<th<?php echo $THStyle; ?>>수량</th>
				<th<?php echo $THStyle; ?>>주문가격</th>

				<?php if($AdminPath == 'totalAdmin' && $SubAdminMode === true) { ?>
				<th<?php echo $THStyle; ?>>N 포인트 사용(전체 주문기준)</th>
				<th<?php echo $THStyle; ?>>N 적립금 사용(전체 주문기준)</th>
				<?php } ?>

				<th<?php echo $THStyle; ?>>배송비용</th>
				<th<?php echo $THStyle; ?>>택배업체</th>
				<th<?php echo $THStyle; ?>>송장번호</th>
				<th<?php echo $THStyle; ?>>배송일</th>

				<th<?php echo $THStyle; ?>>주문자</th>
				<th<?php echo $THStyle; ?>>주문자 이메일</th>
				<th<?php echo $THStyle; ?>>주문자 전화번호</th>
				<th<?php echo $THStyle; ?>>주문자 휴대전화번호</th>

				<th<?php echo $THStyle; ?>>받는분</th>
				<th<?php echo $THStyle; ?>>받는분 전화번호</th>
				<th<?php echo $THStyle; ?>>받는분 휴대전화번호</th>

				<th<?php echo $THStyle; ?>>우편번호</th>
				<th<?php echo $THStyle; ?>>주소</th>
				<th<?php echo $THStyle; ?>>배송메시지</th>
				<th<?php echo $THStyle; ?>>주문/결제일시</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($res as $k=>$v) {

				// 상태아이콘
				$StatusIcon = '';
				if($v['npay_status'] == 'PAYED') $StatusIcon = '결제완료';
				if($v['npay_status'] == 'PLACE') $StatusIcon = '발주처리';
				if($v['npay_status'] == 'DISPATCHED') $StatusIcon = '배송처리';
				if($v['npay_status'] == 'CANCELED') $StatusIcon = '취소';

				// 구매디바이스 아이콘
				if($v['mobile'] == 'Y') $device_icon = 'MOBLIE주문';
				else $device_icon = 'PC주문';
			?>
			<tr>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['op_uid']; ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo date('Y-m-d H:i', strtotime($v['o_rdate'])); ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['op_oordernum']; ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['npay_order_code']; ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['p_name']; ?>
					<?php echo ($v['op_option1']?$br.$v['op_option1']:null); ?>
					<?php echo ($v['op_option2']?$br.$v['op_option2']:null); ?>
					<?php echo ($v['op_option3']?$br.$v['op_option3']:null); ?>
				</td>
				<td<?php echo $TDStyle2; ?>>
					<?php echo number_format($v['op_cnt']); ?>
				</td>
				<td<?php echo $TDStyle2; ?>>
					<?php echo number_format($v['op_price'] * $v['op_cnt']); ?>원
				</td>

				<?php if($AdminPath == 'totalAdmin' && $SubAdminMode === true) { ?>
				<td<?php echo $TDStyle2; ?>>
					<?php echo number_format($v['npay_point']); ?>
				</td>
				<td<?php echo $TDStyle2; ?>>
					<?php echo number_format($v['npay_point2']); ?>
				</td>
				<?php } ?>

				<td<?php echo $TDStyle2; ?>>
					<?php echo number_format($v['op_delivery_price'] + $v['op_add_delivery_price']); ?>원
				</td>

				<td<?php echo $TDStyle; ?>>
					<?php echo $v['op_sendcompany']; ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo ($v['op_sendcompany'] == $v['op_sendnum']?'-':$v['op_sendnum']); ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['op_senddate']; ?>
				</td>

				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_oname']; ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_oemail']; ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_otel']; ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_ohp'] ?>
				</td>

				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_rname'] ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_rtel'] ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_rhp'] ?>
				</td>

				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_rpost']; ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_raddr1'].' '.$v['o_raddr2']; ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo str_replace(array('<br>', '<br/>', '<br />'), $br, $v['o_content']); ?>
				</td>
				<td<?php echo $TDStyle; ?>>
					<?php echo $v['o_rdate']; ?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
<?php
} // excel 다운로드 End