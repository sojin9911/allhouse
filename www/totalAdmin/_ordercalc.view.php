<?php
include_once('wrap.header.php');



// 기본 날짜 지정 (7일)
if(!$pass_sdate) $pass_sdate = date('Y-m-d', strtotime('-7 day'));
$pass_edate = ($pass_edate?$pass_edate:date('Y-m-d'));



// 검색 조건
//$s_query = " and o.o_paystatus='Y' and o.o_canceled='N' and npay_order = 'N' "; // 기본조건(취소 되지 않고 결제 상태인것) / 네이버페이 제외
$s_query = " and o.o_paystatus='Y' and o.o_canceled='N' and npay_order = 'N' and op.op_cancel='N' "; // SSJ : 정산 할인금액 패치 : 2021-05-14 -- 부분취소 제외
if($pass_sdate && $pass_edate)  $s_query .= " and (o_rdate between '{$pass_sdate}' and '". date('Y-m-d', strtotime('+1day', strtotime($pass_edate))) ."') ";// - 검색기간
else if($pass_sdate) $s_query .= " and o_rdate >= '{$pass_sdate}' ";
else if($pass_edate) $s_query .= " and o_rdate < '". date('Y-m-d', strtotime('+1day', strtotime($pass_edate))) ."' ";
if($pass_company) $s_query .= " and op.op_partnerCode = '{$pass_company}' "; // SSJ : 정산 할인금액 패치 : 2021-05-14



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
		op_partnerCode
	from smart_order_product as op
	left join smart_order as o on (o.o_ordernum = op.op_oordernum)
	where (1)
		{$s_query}
	group by sub_orderdate, op.op_partnerCode
	order by sub_orderdate, op.op_partnerCode
";
$res = _MQ_assoc($que);



// 전체 입점업체 정보 호출
$arr_customer = arr_company();
$arr_customer2 = arr_company2();
?>
<form action="<?php ECHO $_SERVER['PHP_SELF']; ?>" method="get">
	<input type="hidden" name="mode" value="search">
	<!-- ●폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<div class="group_title"><strong>정산현황 검색</strong></div>
	<div class="data_form if_search if_nobottom">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>검색기간</th>
					<td>
						<input type="text" name="pass_sdate" class="design js_pic_day_max_today" style="width:85px;" value="<?php echo $pass_sdate; ?>" readonly />
						<span class="fr_tx">-</span>
						<input type="text" name="pass_edate" class="design js_pic_day_max_today" style="width:85px;" value="<?php echo $pass_edate; ?>" readonly />
					</td>
					<th>공급업체</th>
					<td>
						<?php if(sizeof($arr_customer) > 20){ ?>
						<link href="/include/js/select2/css/select2.css" type="text/css" rel="stylesheet">
						<script src="/include/js/select2/js/select2.min.js"></script>
						<script>$(document).ready(function() { $('.select2').select2(); });</script>
						<?php } ?>
						<?php echo _InputSelect('pass_company', array_keys($arr_customer), $pass_company, ' class="select2"', array_values($arr_customer), '공급업체 전체'); ?>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<div class="tip_box">
							<?php echo _DescStr('검색기간은 <u>주문일 기준</u>으로 합니다.', 'black'); ?>
							<?php echo _DescStr('정산금액은 <u>배송비 제외</u>한 금액입니다.', 'black'); ?>
							<?php echo _DescStr('매출금액뿐 아니라 발생된 업체 정산 금액 등을 업체별/기간별로 볼수있는 기능입니다.'); ?>
							<?php echo _DescStr('결제확인된 주문에 대한 현황이며 배송비는 입점업체 총결제비에 포함되지 않으므로 참고하시기 바랍니다.'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>

		<!-- 가운데정렬버튼 -->
		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"/></span></li>
				<?php
				if($mode == 'search') {
				?>
					<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal">전체목록</a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</form>


<div class="data_list">
	<div class="list_ctrl">
		<div class="right_box">
			<a href="#none" onclick="search_excel_send(); return false;" class="c_btn icon icon_excel">엑셀다운로드(<?php echo count($res); ?>)</a>
		</div>
	</div>
	<table class="table_list">
		<colgroup>
			<col width="90">
			<col width="*">
			<col width="200">
			<col width="110">
			<col width="110">
			<col width="200">
		</colgroup>
		<thead>
			<tr>
				<th scope="col">주문일</th>
				<th scope="col">입점업체</th>
				<th scope="col">구매금액</th>
				<th scope="col">판매량</th>
				<th scope="col">배송비</th>
				<th scope="col">정산금액</th>
			</tr>
		</thead>
		<?php if(count($res) > 0) { ?>
			<tbody>
				<?php
				foreach($res as $sk=>$sv) {

					$sum_app_cnt += $sv['tCnt'];
					$sum_tPrice += $sv['tPrice'];
					$sum_dPrice += $sv['dPrice'];
					$sum_payPrice += $sv['comPrice'];
				?>
					<tr>
						<td><?php echo $sv['sub_orderdate']; ?></td>
						<td class="t_left">
							<!-- SSJ : 정산 할인금액 패치 : 2021-05-14 -- smart_order_company 제거 -->
							<?php echo showCompanyInfo($sv['op_partnerCode']); ?>
						</td>
						<td class="t_right">
							<span class="lineup"><strong class="t_red"><?php echo number_format($sv['tPrice']); ?></strong><em>원</em></span>
						</td>
						<td class="t_right">
							<span class="lineup"><strong><?php echo number_format($sv['tCnt']); ?></strong><em>개</em></span>
						</td>
						<td class="t_right">
							<span class="lineup"><strong class="t_green"><?php echo number_format($sv['dPrice']); ?></strong><em>원</em></span>
						</td>
						<td class="t_right">
							<span class="lineup"><strong class="t_sky"><?php echo number_format($sv['comPrice']); ?></strong><em>원</em></span>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		<?php } ?>
	</table>

	<?php if(count($res) <= 0) { ?>
		<!-- 내용없을경우 -->
		<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
	<?php } ?>
</div>


<!-- ● 정산 총합계 -->
<div class="entershop_summ">
	<table class="table_summ">
		<colgroup>
			<col width="90">
			<col width="*">
			<col width="200">
			<col width="110">
			<col width="110">
			<col width="200">
		</colgroup>
		<tbody>
			<tr>
				<th rowspan="2" colspan="2" class="title">정산 총 합계</th>
				<th>구매합계</th>
				<th>판매량</th>
				<th>배송비</th>
				<th>정산금액</th>
			</tr>
			<tr>
				<td><span class="lineup"><strong class="t_red"><?php echo number_format($sum_tPrice); ?></strong><em>원</em></span></td>
				<td><span class="lineup"><strong class="t_black"><?php echo number_format($sum_app_cnt); ?></strong><em>개</em></span></td>
				<td><span class="lineup"><strong class="t_green"><?php echo number_format($sum_dPrice); ?></strong><em>원</em></span></td>
				<td><span class="lineup"><strong class="t_sky"><?php echo number_format($sum_payPrice); ?></strong><em>원</em></span></td>
			</tr>
		</tbody>
	</table>
</div>

<script type="text/javascript">
	function search_excel_send() {
		common_frame.location.href= '_ordercalc.excel.php?pass_sdate=<?php echo $pass_sdate; ?>&pass_edate=<?php echo $pass_edate; ?>&pass_company=<?php echo $pass_company; ?>';
	}
</script>
<?php include_once('wrap.footer.php'); ?>