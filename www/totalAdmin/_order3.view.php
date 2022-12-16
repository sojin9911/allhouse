<?php
/**** SSJ : 정산대기관리 메뉴 개선 패치 : 2021-10-01 ****/

// --------- JJC : 정산기능분화 : 2021-01-19 ---------

@ini_set('memory_limit', '-1');
$app_current_link = '_order3.list.php';
include_once('wrap.header.php');

if($_s_query == '' || $_id == ''){
	error_msg('잘못된 접근입니다.');
}
$s_query = enc('d', $_s_query);
$s_query .= " and o.o_canceled = 'N' "; // 취소 주문 제외
// 데이터 조회
$res = _MQ_assoc("
	select
		(op.op_price * op.op_cnt) as sum_price,
		IF(
			op.op_comSaleType = '공급가',
			(op.op_supply_price * op.op_cnt) + op.op_delivery_price + op.op_add_delivery_price ,
			(op.op_price * op.op_cnt - op.op_price * op.op_cnt * op.op_commission / 100) + op.op_delivery_price + op.op_add_delivery_price
		) as comPrice,
		(op.op_delivery_price + op.op_add_delivery_price) as delivery_price,
		op.op_usepoint+op.op_use_discount_price+op.op_use_product_coupon as use_point,
		op.op_cnt as total_cnt,
		o_rdate , op_uid, op_pname , op_option1, op_option2, op_option3 , op_oordernum
	FROM smart_order_product AS op
	LEFT JOIN smart_order AS o ON (o.o_ordernum=op.op_oordernum)
	WHERE (1)
		{$s_query}
		and op.op_partnerCode = '". addslashes($_id) ."'
	order by op.op_uid desc
");

// 전체 입점업체 정보 호출
$arr_customer = arr_company();
$arr_customer2 = arr_company2();

$cprow = _MQ("SELECT * FROM smart_company WHERE cp_id = '". addslashes($_id) ."'  ");

?>

<!-- ●폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
<div class="group_title"><strong>공급업체 정보</strong></div>
<div class="data_form if_search if_nobottom">


	<table class="table_form">
		<colgroup>
			<col width="180"/><col width="*"/><col width="180"/><col width="*"/><col width="180"/><col width="*"/>
		</colgroup>
		<tbody>

			<tr>
				<th>업체명</th>
				<td>
					<a href="_entershop.form.php?_mode=modify&_id=<?php echo $cprow['cp_id']; ?>" target="_blank"><?php echo $cprow['cp_name']; ?> <span class="t_light">(<?php echo $cprow['cp_id']; ?>)</span></a>
				</td>
				<th>대표자</th>
				<td>
					<?php echo trim($cprow['cp_ceoname']); ?>
				</td>
				<th>대표전화</th>
				<td>
					<?php echo tel_format($cprow['cp_tel']); ?>
				</td>
			</tr>
			<tr>
				<th>담당자</th>
				<td>
					<?php echo trim($cprow['cp_charge']); ?>
				</td>
				<th>담당자휴대폰</th>
				<td>
					<?php echo tel_format($cprow['cp_tel2']); ?>
				</td>
				<th>담당자이메일</th>
				<td>
					<?php echo ($cprow['cp_email']); ?>
				</td>
			</tr>
		</tbody>
	</table>

	<!-- 가운데정렬버튼 -->
	<div class="c_btnbox"></div>
</div>


<!-- ● 데이터 리스트 -->
<form class="form_list" action="_order_product.pro.php" method="post" target="common_frame">
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="_id" value="<?php echo $_id; ?>">
	<input type="hidden" name="_s_query" value="<?php echo $_s_query; ?>">
	<div class="data_list">
		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="AllChecked('active');" class="c_btn h27">전체선택</a>
				<a href="#none" onclick="AllChecked('inactive');" class="c_btn h27">선택해제</a>
				<a href="#none" onclick="settlement_status(); return false;" class="c_btn h27 gray">선택 정산완료 처리</a>
			</div>
			<div class="right_box">
				<a href="#none" onclick="saveExcel('_order3.view.excel.php'); return false;" class="c_btn icon icon_excel">선택 엑셀다운로드</a>
			</div>
		</div>
		<!-- / 리스트 컨트롤영역 -->

		<?php if(count($res) > 0) { ?>
			<table class="table_list">
				<colgroup>
					<col width="50"/>
					<col width="180"/>
					<col width="*"/>
					<col width="100"/>
					<col width="100"/>
					<col width="100"/>
					<col width="100"/>
					<col width="100"/>
					<col width="100"/>
					<col width="100"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th scope="col">주문일</th>
						<th scope="col">상품정보</th>
						<th scope="col">판매수</th>
						<th scope="col">총합계</th>
						<th scope="col">판매액</th>
						<th scope="col">배송비</th>
						<th scope="col">판매수수료</th>
						<th scope="col">할인금액</th>
						<th scope="col">정산금액</th>
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
							$fee_price = $sum_price  + $delivery_price - $com_price - $use_point;

							// 총합계
							$total_sale_cnt += $sale_cnt;
							$total_sum_price += $sum_price;
							$total_delivery_price += $delivery_price;
							$total_com_price += $com_price;
							$total_use_point += $use_point;
							$total_fee_price += $fee_price;
					?>
							<tr>
								<td>
									<label class="design"><input type="checkbox" name="OpUid[]" class="js_ck" value="<?php echo $v['op_uid']; ?>"></label>
								</td>
								<td>
									<?php echo $v['o_rdate']; ?>
								</td>
								<td class="t_left">
									<span class="bold"><?php echo $v['op_pname']; ?></span>

									<?php echo implode(" ", array_filter(array($v['op_option1'],$v['op_option2'],$v['op_option3']))); ?>
									(<?php echo $v['op_oordernum']; ?>)
								</td>
								<td class="t_right"><span class="bold"><?php echo number_format($sale_cnt); ?></span>개</td>
								<td class="t_right"><span class="bold t_red"><?php echo number_format($sum_price + $delivery_price); ?></span>원</td>
								<td class="t_right"><span class="bold t_red"><?php echo number_format($sum_price); ?></span>원</td>
								<td class="t_right"><span class="bold"><?php echo number_format($delivery_price); ?></span>원</td>
								<td class="t_right"><span class="bold t_sky"><?php echo number_format($fee_price); ?></span>원</td>
								<td class="t_right"><span class="bold"><?php echo number_format($use_point); ?></span>원</td>
								<td class="t_right"><span class="bold t_green"><?php echo number_format($com_price); ?></span>원</td>
							</tr>
					<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="3" class="title">합계</th>
						<th class="t_right "><span class="bold"><?php echo number_format($total_sale_cnt); ?></span>개</th>
						<th class="t_right"><span class="bold t_red"><?php echo number_format($total_sum_price + $total_delivery_price); ?></span>원</th>
						<th class="t_right"><span class="bold t_red"><?php echo number_format($total_sum_price); ?></span>원</th>
						<th class="t_right"><span class="bold"><?php echo number_format($total_delivery_price); ?></span>원</th>
						<th class="t_right"><span class="bold t_sky"><?php echo number_format($total_fee_price); ?></span>원</th>
						<th class="t_right"><span class="bold"><?php echo number_format($total_use_point); ?></span>원</th>
						<th class="t_right"><span class="bold t_green"><?php echo number_format($total_com_price); ?></span>원</th>
					</tr>
				</tfoot>
			</table>
		<?php }else{ ?>
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">정산내역이 없습니다.</div></div>
		<?php } ?>
	</div>
</form>




<div class="c_btnbox">
	<ul>
		<li><a href="_order3.list.php?<?php echo enc('d', $_PVSC); ?>" class="c_btn h46 black line" accesskey="l">목록</a></li>
	</ul>
</div>

<div class="fixed_save js_fixed_save" style="display: block;">
	<div class="wrapping">
		<!-- 가운데정렬버튼 -->
		<div class="c_btnbox">
			<ul>
				<li><a href="_order3.list.php?<?php echo enc('d', $_PVSC); ?>" class="c_btn h34 black line" accesskey="l">목록</a></li>
			</ul>
		</div>
	</div>
</div>



<script type="text/javascript">

	// 전체선택/해제
	function AllChecked(_mode) {
		$('.js_ck').prop('checked', false);
		$('.js_AllCK').prop('checked', false);
		if(_mode == 'active'){
			$('.js_AllCK').prop('checked', true);
			$('.js_ck').prop('checked', true);
		}
	}

	// 선택정산완료
	function settlement_status() {
		if($('input:checkbox[name^=OpUid]:checked').length <= 0) {
			alert('처리할 항목을 1건 이상 선택 바랍니다.');
			return false;
		}
		$('.form_list').prop('action', '_order_product.pro.php');
		$('.form_list').find('input[name=_mode]').val('settlementstatus_complete');
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
		$('.form_list').prop('action', '_order_product.pro.php');
	}

	// 엑셀저장
	function saveExcel(fileTemp) {
		if($('input:checkbox[name^=OpUid]:checked').length <= 0) {
			alert('처리할 항목을 1건 이상 선택 바랍니다.');
			return false;
		}
		$('.form_list').prop('action', fileTemp);
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
		$('.form_list').prop('action', '_order_product.pro.php');
	}

</script>
<?php include_once('wrap.footer.php'); ?>