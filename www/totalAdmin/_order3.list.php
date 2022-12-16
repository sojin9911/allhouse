<?php
/**** SSJ : 정산대기관리 메뉴 개선 패치 : 2021-10-01 ****/

include_once('wrap.header.php');

// 넘길 변수 설정하기
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {
	if(is_array($val)) foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }
	else $_PVS .= "&$key=$val";
}
$_PVSC = enc('e' , $_PVS);
// 넘길 변수 설정하기



// 검색 조건
$s_query = " and op.op_settlementstatus = 'ready' and o.o_paystatus = 'Y' and o.o_canceled = 'N' and o.npay_order = 'N' "; // 기본조건(취소 되지 않고 결제 상태인것) / 네이버페이 제외

$date_type = 'op_rdate';
if($pass_date_type == 'odate') $date_type = 'op_rdate'; // 주문일
else if($pass_date_type == 'pdate') $date_type = 'op_paydate'; // 결제일
else if($pass_date_type == 'ddate') $date_type = 'op_senddate'; // 배송완료일
else if($pass_date_type == 'rdate') $date_type = 'op_settlement_reday'; // 정산대기 전환일
if($pass_sdate && $pass_edate)  $s_query .= " AND ({$date_type} between '{$pass_sdate}' and '". date('Y-m-d', strtotime('+1day', strtotime($pass_edate))) ."') ";// - 검색기간
else if($pass_sdate) $s_query .= " AND {$date_type} >= '{$pass_sdate}' ";
else if($pass_edate) $s_query .= " AND {$date_type} < '". date('Y-m-d', strtotime('+1day', strtotime($pass_edate))) ."' ";
$DateTypeArr = array(
	'op_rdate'=>'주문일',
	'op_paydate'=>'결제일',
	'op_senddate'=>'배송완료일',
	'op_settlement_reday'=>'정산대기 전환일',
);

if($pass_company) $s_query .= " AND op.op_partnerCode = '{$pass_company}' "; // 공급업체
if($pass_pname) $s_query .= " AND op.op_pname like '%{$pass_pname}%' "; //상품명
if($pass_paymethod) $s_query .= " and o.o_paymethod = '{$pass_paymethod}' ";  // 결제수단

// 페이징 작업
if(!$listmaxcount) $listmaxcount = 10;
if(!$listpg) $listpg = 1;
$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스


$res = _MQ("
	select count(*) as cnt
	from (
		select
			op_partnerCode
		FROM smart_order_product AS op
		LEFT JOIN smart_order AS o ON (o.o_ordernum=op.op_oordernum)
		LEFT JOIN smart_company as cp ON (cp.cp_id = op.op_partnerCode)
		WHERE (1)
			{$s_query}
		GROUP BY op.op_partnerCode
	) as t
");
$TotalCount= $res['cnt'];
$Page = ceil($TotalCount / $listmaxcount);

// 데이터 조회
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
	limit $count , $listmaxcount
");






// -- 전체 데이터 조회 ----
$total_res = _MQ("
	select
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
	ORDER BY NULL
");
// -- 전체 데이터 조회 ----






// 전체 입점업체 정보 호출
$arr_customer = arr_company();
$arr_customer2 = arr_company2();
?>
<form name="searchfrm" action="<?php ECHO $_SERVER['PHP_SELF']; ?>" method="get">
	<input type="hidden" name="mode" value="search">
	<!-- ●폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<div class="group_title"><strong>정산대기 검색</strong></div>
	<div class="data_form if_search if_nobottom">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>상품명</th>
					<td><input type="text" name="pass_pname" class="design" value="<?php echo $pass_pname; ?>" /></td>
					<th>결제수단</th>
					<td>
						<?php echo _InputSelect("pass_paymethod" , array_keys($arr_payment_type), $pass_paymethod , "" , array_values($arr_payment_type) , '전체'); ?>
					</td>
				</tr>
				<tr>
					<th>공급업체</th>
					<td>
						<?php if(sizeof($arr_customer) > 20){ ?>
							<link href="/include/js/select2/css/select2.css" type="text/css" rel="stylesheet">
							<script src="/include/js/select2/js/select2.min.js"></script>
							<script>$(document).ready(function() { $('.select2').select2(); });</script>
						<?php } ?>
						<?php echo _InputSelect('pass_company', array_keys($arr_customer), $pass_company, ' class="select2"', array_values($arr_customer), '-공급업체선택-'); ?>
					</td>
					<th>검색기간</th>
					<td>
						<select name="pass_date_type">
							<option value="odate"<?php echo ($pass_date_type == 'odate'?' selected':null); ?>>주문일</option>
							<option value="pdate"<?php echo ($pass_date_type == 'pdate'?' selected':null); ?>>결제일</option>
							<option value="ddate"<?php echo ($pass_date_type == 'ddate'?' selected':null); ?>>배송완료일</option>
							<option value="rdate"<?php echo ($pass_date_type == 'rdate'?' selected':null); ?>>정산대기 전환일</option>
						</select>
						<input type="text" name="pass_sdate" class="design js_pic_day_max_today" style="width:85px;" value="<?php echo $pass_sdate; ?>" readonly />
						<span class="fr_tx">-</span>
						<input type="text" name="pass_edate" class="design js_pic_day_max_today" style="width:85px;" value="<?php echo $pass_edate; ?>" readonly />
					</td>
				</tr>
				<tr>
					<th>자동 정산대기 안내</th>
					<td colspan="3">

						<?php echo ($siteInfo['s_product_auto_on'] == 'Y' ? $arr_adm_button['사용'] : $arr_adm_button['미사용']); ?>
						<div class="tip_box">
							<?php echo _DescStr("자동 정산대기 기능 사용 시 배송완료 후 설정된 기간이 지나면 자동으로 정산대기로 넘어갑니다."); ?>
							<?php echo _DescStr("배송주문상품관리 메뉴에서 수동으로 정산대기 처리를 할 수도 있습니다.", 'black'); ?>
							<?php echo _DescStr('자동 정산대기 처리 설정은 [환경설정 > 상품/배송 설정 > 상품/배송 기본 정보] 메뉴에서 수정 가능합니다. <a href="_config.delivery.form.php" class="t_black">[바로가기]</a>'); ?>
						</div>

						<div class="dash_line"></div>
						<a href="#none" onclick="return false;" class="c_btn h27 js_product_auto">자동 정산대기 처리 기간 안내</a>

						<!-- ● 데이터 통계박스 -->
						<table class="table_list fix js_product_auto_wrap" style="display:none;margin-top:30px;">
							<thead>
								<tr>
									<?php foreach($arr_paymethod_name as $k=>$v) { ?>
										<th scope="col"><?php echo $v; ?></th>
									<?php } ?>
								</tr>
							</thead>
							<tbody>
								<tr>
									<?php foreach($arr_paymethod_name as $k=>$v) { ?>
										<td><?php echo number_format($siteInfo['s_product_auto_'.$k]); ?>일</td>
									<?php } ?>
								</tr>
							</tbody>
						</table>

						<script>
							$(document).on('click', '.js_product_auto', function(){
								var trigger = $('.js_product_auto_wrap').is(":visible");
								if(trigger){ $('.js_product_auto_wrap').hide(); }
								else{ $('.js_product_auto_wrap').show(); }
							});
						</script>

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


<?php
	// 총합계
	$total_sale_cnt = $total_res['total_cnt']*1;
	$total_sum_price = $total_res['sum_price']*1;
	$total_delivery_price = $total_res['delivery_price']*1;
	$total_com_price = $total_res['comPrice']*1;
	$total_use_point = $total_res['use_point']*1;
	$total_fee_price = $total_sum_price + $total_delivery_price - $total_com_price - $total_use_point;;
?>
<!-- ● 데이터 통계박스 -->
<div class="group_title"><strong>정산대기 검색 총계</strong></div>
<div class="data_summery">
	<table class="table_list fix">
		<colgroup>
			<col width="1%"/>
			<col width="1%"/>
			<col width="1%"/>
			<col width="1%"/>
			<col width="1%"/>
			<col width="1%"/>
			<col width="1%"/>
		</colgroup>
		<thead>
			<tr>
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
			<tr>
				<td class="t_right "><span class="bold"><?php echo number_format($total_sale_cnt); ?></span>개</td>
				<td class="t_right"><span class="bold t_red"><?php echo number_format($total_sum_price + $total_delivery_price); ?></span>원</td>
				<td class="t_right"><span class="bold t_red"><?php echo number_format($total_sum_price); ?></span>원</td>
				<td class="t_right"><span class="bold"><?php echo number_format($total_delivery_price); ?></span>원</td>
				<td class="t_right"><span class="bold t_sky"><?php echo number_format($total_fee_price); ?></span>원</td>
				<td class="t_right"><span class="bold"><?php echo number_format($total_use_point); ?></span>원</td>
				<td class="t_right"><span class="bold t_green"><?php echo number_format($total_com_price); ?></span>원</td>
			</tr>
		</tbody>
	</table>

	<div class="tip_box">
		<?php echo _DescStr("검색된 모든 업체에 대한 정산대기 총합계 입니다."); ?>
	</div>

</div>


<!-- ● 데이터 리스트 -->
<form class="form_list" action="_order_product.pro.php" method="post" target="common_frame">
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="_search_cnt" value="<?php echo $TotalCount; ?>">
	<input type="hidden" name="_s_query" value="<?php echo enc('e', $s_query); ?>">
	<div class="data_list if_entershop">
		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="AllChecked('active');" class="c_btn h27">전체선택</a>
				<a href="#none" onclick="AllChecked('inactive');" class="c_btn h27">선택해제</a>
				<a href="#none" onclick="settlement_status(); return false;" class="c_btn h27 gray">선택 정산완료 처리</a>
			</div>
			<div class="right_box">
				<a href="#none" onclick="saveExcel('_order3.excel.php'); return false;" class="c_btn icon icon_excel">선택 엑셀다운로드</a>
				<a href="#none" onclick="searchExcel('_order3.excel.php'); return false;" class="c_btn icon icon_excel">검색 엑셀다운로드 (<?php echo number_format($TotalCount); ?>)</a>
			</div>
		</div>
		<!-- / 리스트 컨트롤영역 -->

		<?php if(count($res) > 0) { ?>
			<table class="table_list">
				<colgroup>
					<col width="50"/>
					<col width="70"/>
					<col width="280"/>
					<col width="*"/>
					<col width="*"/>
					<col width="*"/>
					<col width="*"/>
					<col width="*"/>
					<col width="*"/>
					<col width="*"/>
					<col width="80"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th scope="col">NO</th>
						<th scope="col">업체명</th>
						<th scope="col">판매수</th>
						<th scope="col">총합계</th>
						<th scope="col">판매액</th>
						<th scope="col">배송비</th>
						<th scope="col">판매수수료</th>
						<th scope="col">할인금액</th>
						<th scope="col">정산금액</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach($res as $k=>$v) {
							$_num = $TotalCount - $count - $k ;

							$sale_cnt = $v['total_cnt'];
							$sum_price = $v['sum_price'];
							$delivery_price = $v['delivery_price'];
							$com_price = $v['comPrice'];
							$use_point = $v['use_point'];
							$fee_price = $sum_price + $delivery_price - $com_price - $use_point;
					?>
							<tr>
								<td>
									<label class="design"><input type="checkbox" name="chk_id[<?php echo $v['op_partnerCode']; ?>]" class="js_ck" value="Y"></label>
								</td>
								<td><?php echo $_num; ?></td>
								<td>
									<a href="_entershop.form.php?_mode=modify&_id=<?php echo $v['op_partnerCode']; ?>" target="_blank"><?php echo $arr_customer2[$v['op_partnerCode']]; ?> <span class="t_light">(<?php echo $v['op_partnerCode']; ?>)</span></a>
									<?php
										$arr_tel = array_filter(array($v['cp_charge'] , $v['cp_tel'] , $v['cp_tel2']));
										echo (sizeof($arr_tel) > 0 ? "<div class='clear_both'></div><span class='t_light'>(" . implode(" / " , $arr_tel) . ")</span>" : "");
									?>
								</td>
								<td class="t_right "><span class="bold"><?php echo number_format($sale_cnt); ?></span>개</td>
								<td class="t_right"><span class="bold t_red"><?php echo number_format($sum_price + $delivery_price); ?></span>원</td>
								<td class="t_right"><span class="bold t_red"><?php echo number_format($sum_price); ?></span>원</td>
								<td class="t_right"><span class="bold"><?php echo number_format($delivery_price); ?></span>원</td>
								<td class="t_right"><span class="bold t_sky"><?php echo number_format($fee_price); ?></span>원</td>
								<td class="t_right"><span class="bold"><?php echo number_format($use_point); ?></span>원</td>
								<td class="t_right"><span class="bold t_green"><?php echo number_format($com_price); ?></span>원</td>
								<td>
									<div class="lineup-center">
										<a href="_order3.view.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', '_id'=>$v['op_partnerCode'], '_s_query'=>enc('e', $s_query), '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 line" target="">상세보기</a>
									</div>
									<div class="lineup-center">
										<a href="_order3.view.excel.php<?php echo URI_Rebuild('?', array('_mode'=>'listexcel', '_id'=>$v['op_partnerCode'], '_s_query'=>enc('e', $s_query), '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 line" target="">엑셀다운</a>
									</div>
								</td>
							</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php }else{ ?>
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">정산내역이 없습니다.</div></div>
		<?php } ?>
	</div>

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
	</div>


</form>




<script type="text/javascript">

	// 선택정산완료
	function settlement_status() {
		if($('input:checkbox[name^=chk_id]:checked').length <= 0) {
			alert('처리할 항목을 1건 이상 선택 바랍니다.');
			return false;
		}
		$('.form_list').prop('action', '_order_product.pro.php');
		$('.form_list').find('input[name=_mode]').val('settlementstatus_complete');
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
	}

	// 엑셀저장
	function saveExcel(fileTemp) {
		if($('input:checkbox[name^=chk_id]:checked').length <= 0) {
			alert('처리할 항목을 1건 이상 선택 바랍니다.');
			return false;
		}
		$('.form_list').find('input[name=_mode]').val('select_excel');
		$('.form_list').prop('action', fileTemp);
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
		$('.form_list').prop('action', '_order_product.pro.php');
	}

	// 엑셀저장
	function searchExcel(fileTemp) {
		var search_cnt = $('input[name=_search_cnt]').val()*1;
		if(search_cnt <= 0) {
			alert('검색된 내역이 없습니다.');
			return false;
		}
		$('.form_list').find('input[name=_mode]').val('search_excel');
		$('.form_list').prop('action', fileTemp);
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
		$('.form_list').prop('action', '_order_product.pro.php');
	}

	// 전체선택/해제
	function AllChecked(_mode) {
		$('.js_ck').prop('checked', false);
		$('.js_AllCK').prop('checked', false);
		if(_mode == 'active'){
			$('.js_AllCK').prop('checked', true);
			$('.js_ck').prop('checked', true);
		}
	}

</script>
<?php include_once('wrap.footer.php'); ?>