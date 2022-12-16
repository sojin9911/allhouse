<?php
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
$s_query = " and op.op_settlementstatus = 'ready' and o.o_paystatus = 'Y' and o.o_canceled = 'N' and o.npay_order = 'N' and op.op_partnerCode = '{$com_id}' "; // 기본조건(취소 되지 않고 결제 상태인것) / 네이버페이 제외

$date_type = 'op_rdate';
if($pass_date_type == 'odate') $date_type = 'op_rdate'; // 주문일
else if($pass_date_type == 'pdate') $date_type = 'op_paydate'; // 결제일
else if($pass_date_type == 'ddate') $date_type = 'op_senddate'; // 배송완료일
else if($pass_date_type == 'rdate') $date_type = 'op_settlement_reday'; // 정산대기 전환일
if($pass_sdate && $pass_edate)  $s_query .= " AND ({$date_type} between '{$pass_sdate}' and '{$pass_edate}') ";// - 검색기간
else if($pass_sdate) $s_query .= " AND {$date_type} >= '{$pass_sdate}' ";
else if($pass_edate) $s_query .= " AND {$date_type} <= '{$pass_edate}' ";
$DateTypeArr = array(
	'op_rdate'=>'주문일',
	'op_paydate'=>'결제일',
	'op_senddate'=>'배송완료일',
	'op_settlement_reday'=>'정산대기 전환일',
);
if($pass_pname) $s_query .= " and op.op_pname like '%{$pass_pname}%' "; //상품명
if($pass_paymethod) $s_query .= " and o.o_paymethod = '{$pass_paymethod}' ";  // 결제수단


// 데이터 조회
$resP = _MQ_assoc("
	select
		op.*, o.*,
		IF(
			op.op_comSaleType = '공급가',
			(op.op_supply_price * op.op_cnt + op.op_delivery_price + op.op_add_delivery_price),
			(op.op_price * op.op_cnt - op.op_price * op.op_cnt * op.op_commission / 100 + op.op_delivery_price + op.op_add_delivery_price)
		) as comPrice
	from
		smart_order_product as op left join
		smart_order as o on (o.o_ordernum=op.op_oordernum)
	where (1)
		{$s_query}
	order by op.op_uid desc
");
$arrData = array();
foreach($resP as $k=>$v){
	$arrData[$v['op_partnerCode']][] = $v;
}
?>
<form action="<?php ECHO $_SERVER['PHP_SELF']; ?>" method="get">
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
					<th>검색기간</th>
					<td colspan="3">
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


<?php if($siteInfo['s_product_auto_on'] == 'Y') { ?>
	<!-- ● 데이터 통계박스 -->
	<div class="group_title"><strong>자동 정산대기 처리 기간 안내</strong></div>
	<div class="data_summery">
		<table class="table_list fix">
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

		<div class="tip_box">
			<div class="c_tip">배송완료를 기준으로 한 기간입니다.</div>
		</div>
	</div>
<?php } ?>




<!-- ● 데이터 리스트 -->
<form class="form_list" action="_order_product.pro.php" method="post" target="common_frame">
	<input type="hidden" name="_mode" value="">
	<div class="data_list if_entershop">
		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="AllChecked('active');" class="c_btn h27">전체선택</a>
				<a href="#none" onclick="AllChecked('inactive');" class="c_btn h27">선택해제</a>
			</div>
			<div class="right_box">
				<a href="#none" onclick="saveExcel('_order3.excel.php'); return false;" class="c_btn icon icon_excel">선택 엑셀다운로드</a>
			</div>
		</div>
		<!-- / 리스트 컨트롤영역 -->

		<table class="table_list">
			<colgroup>
				<col width="50"/><col width="*"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col">NO</th>
					<th scope="col">정산정보</th>
				</tr>
			</thead>
			<tbody>
				<?php if(count($arrData) > 0) { ?>
					<?php
					// 총합계
					$TotalSumPrice = 0; // 총 구매합계
					$TotalDeliveryPrice = 0; // 총 배송비
					$TotalComPrice = 0; // 총 업체수수료
					$TotalUsePoint = 0; // 총 할인액
					$TotalDiscount = 0; // 총 수수료
					$TotalCount = 0; // 개수
					$num = 0;
					foreach($arrData as $k=>$res) {

						// 서브합계
						$SubSumPrice = 0; // 총 구매합계
						$SubDeliveryPrice = 0; // 총 배송비
						$SubComPrice = 0; // 총 업체수수료
						$SubUsePoint = 0; // 총 할인액
						$SubDiscount = 0; // 총 수수료
						$SubCount = 0;

						// -- 순번 ---
						$_num = count($arrData)-$num;
					?>
						<tr>
							<td class="bold"><?php echo $_num; ?></td>
							<td>

								<!-- 공급업체이름 -->
								<div class="entershop_name">
									<!-- 클릭하면 열리고/닫힘 -->
									<a href="#none" class="btn_ctrl js_detail_view now_open"><span class="tx">정산정보 간편보기</span></a>
								</div>

								<!-- 하나의 공급업체 묶음 / 닫으면 if_closed -->
								<!-- 이 영역안에서 마우스 스크롤 할때는 전체 스크롤에 영향없도록 -->
								<div class="entershop js_detail_box">
									<table class="table_entershop">
										<colgroup>
											<col width="40"/><col width="80"/>
											<?php if($siteInfo['s_vat_product'] == 'C') { // 복합과세 설정시에만 나옴 ?>
												<col width="80"/>
											<?php } ?>
											<col width="*"/><col width="150"/><col width="130"/><col width="130"/><col width="110"/><col width="110"/><col width="70"/>
										</colgroup>
										<thead>
											<tr>
												<th scope="col">
													<label class="design">
														<input type="checkbox" class="com_checked class_uid" data-com="com_<?php echo $k; ?>" />
													</label>
												</th>
												<th scope="col"><?php echo str_replace(' ', '<br>', $DateTypeArr[$date_type]); ?></th>
												<?php if($siteInfo['s_vat_product'] == 'C') { // 복합과세 설정시에만 나옴 ?>
													<th scope="col">과세여부</th>
												<?php } ?>
												<th scope="col">상품 정보</th>
												<th scope="col">구매합계<br/><span class="s11 normal">(상품가 × 판매수)</span></th>
												<th scope="col">배송비</th>
												<th scope="col">업체수수료</th>
												<th scope="col">할인금액</th>
												<th scope="col">수수료</th>
												<th scope="col">관리</th>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach($res as $sk=>$sv) {

												$sum_price = $sv['op_price'] * $sv['op_cnt'];

												// 총합계 적용
												$TotalSumPrice += $sum_price; // 총 구매합계
												$TotalDeliveryPrice += $sv['op_delivery_price'] + $sv['op_add_delivery_price']; // 총 배송비
												$TotalComPrice += $sv['comPrice']; // 총 업체수수료
												//$TotalUsePoint += $sv['op_usepoint']; // 총 할인액
												//$TotalDiscount += $sum_price + $sv['op_delivery_price'] + $sv['op_add_delivery_price'] - $sv['comPrice'] - $sv['op_usepoint']; // 총 수수료
												// SSJ : 정산 할인금액 패치 : 2021-05-14 -- 상품쿠폰 사용액
												$TotalUsePoint += ($sv['op_usepoint'] + $sv['op_use_discount_price'] + $sv['op_use_product_coupon']); // 총 할인액
												$TotalDiscount += $sum_price + $sv['op_delivery_price'] + $sv['op_add_delivery_price'] - $sv['comPrice'] - $sv['op_usepoint'] - $sv['op_use_discount_price'] - $sv['op_use_product_coupon']; // 총 수수료
												$TotalCount += 1; // 총 개수

												// 서브합계 적용
												$SubSumPrice += $sum_price; // 총 구매합계
												$SubDeliveryPrice += $sv['op_delivery_price'] + $sv['op_add_delivery_price']; // 총 배송비
												$SubComPrice += $sv['comPrice']; // 총 업체수수료
												//$SubUsePoint += $sv['op_usepoint']; // 총 할인액
												//$SubDiscount += $sum_price + $sv['op_delivery_price'] + $sv['op_add_delivery_price'] - $sv['comPrice'] - $sv['op_usepoint']; // 총 수수료
												// SSJ : 정산 할인금액 패치 : 2021-05-14 -- 상품쿠폰 사용액
												$SubUsePoint += ($sv['op_usepoint'] + $sv['op_use_discount_price'] + $sv['op_use_product_coupon']); // 총 할인액
												$SubDiscount += $sum_price + $sv['op_delivery_price'] + $sv['op_add_delivery_price'] - $sv['comPrice'] - $sv['op_usepoint'] - $sv['op_use_discount_price'] - $sv['op_use_product_coupon']; // 총 수수료
												$SubCount += 1; // 총 개수
											?>
												<tr>
													<td>
														<label class="design">
															<input type="checkbox" name="OpUid[]" data-com="com_<?php echo $k; ?>" value="<?php echo $sv['op_uid']; ?>" class="class_uid">
														</label>
													</td>
													<td>
														<?php
														if($date_type == 'op_senddate') $sv[$date_type] = $sv[$date_type].' 00:00:00'; // 배송완료일은 date포맷이 다르기 때문에 맞춰준다
														echo ($sv[$date_type] == '0000-00-00 00:00:00'?'-':date('Y.m.d', strtotime($sv[$date_type])));
														?>
													</td>
													<?php if($siteInfo['s_vat_product'] == 'C') { // 복합과세 설정시에만 나옴 ?>
														<td>
															<div class="lineup-vertical">
																<?php if($sv['op_vat'] == 'N') { ?>
																	<span class="c_tag h18 red">면세</span>
																<?php } else { ?>
																	<span class="c_tag h18 light">과세</span>
																<?php } ?>
															</div>
														</td>
													<?php } ?>
													<td class="t_left">
														<?php echo htmlspecialchars($sv['op_pname']).$app_vat_str; ?>
														<a href="_order.form.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', '_ordernum'=>$sv['op_oordernum'], '_PVSC'=>$_PVSC)); ?>" class="bold t_black" target="_blank">(<?php echo $sv['op_oordernum']; ?>)</a>
													</td>
													<td class="t_right"><span class="lineup"><strong class="t_red"><?php echo number_format($sum_price); ?></strong><em>원</em></span></td>
													<td class="t_right"><span class="lineup"><strong><?php echo number_format($sv['op_delivery_price']+$sv['op_add_delivery_price']); ?></strong><em>원</em></span></td>
													<td class="t_right"><span class="lineup"><strong class="t_green"><?php echo number_format($sv['comPrice']); ?></strong><em>원</em></span></td>
													<!-- SSJ : 정산 할인금액 패치 : 2021-05-14 -- 상품쿠폰 사용액 -->
													<td class="t_right"><span class="lineup"><strong><?php echo number_format($sv['op_usepoint']+$sv['op_use_discount_price']+$sv['op_use_product_coupon']); ?></strong><em>원</em></span></td>
													<!-- SSJ : 정산 할인금액 패치 : 2021-05-14 -- 상품쿠폰 사용액 -->
													<td class="t_right"><span class="lineup"><strong class="t_sky"><?php echo number_format($sum_price + $sv['op_delivery_price'] + $sv['op_add_delivery_price'] - $sv['comPrice'] - $sv['op_usepoint'] - $sv['op_use_discount_price'] - $sv['op_use_product_coupon']); ?></strong><em>원</em></span></td>
													<td><div class="lineup-center"><a href="_order.form.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', '_ordernum'=>$sv['op_oordernum'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 line" target="_blank">상세보기</a></div></td>
												</tr>
											<?php } ?>
										</tbody>
										<tfoot>
											<tr>
												<td colspan="2" class="bold t_black">합계</td>
												<td<?php echo ($siteInfo['s_vat_product'] == 'C'?' colspan="2"':null) // 복합과세 설정시에만 나옴 ?>></td>
												<td class="t_right"><span class="lineup"><strong><?php echo number_format($SubSumPrice); ?></strong><em>원</em></span></td>
												<td class="t_right"><span class="lineup"><strong><?php echo number_format($SubDeliveryPrice); ?></strong><em>원</em></span></td>
												<td class="t_right"><span class="lineup"><strong><?php echo number_format($SubComPrice); ?></strong><em>원</em></span></td>
												<td class="t_right"><span class="lineup"><strong><?php echo number_format($SubUsePoint); ?></strong><em>원</em></span></td>
												<td class="t_right"><span class="lineup"><strong><?php echo number_format($SubDiscount); ?></strong><em>원</em></span></td>
												<td class="bold t_black"><?php echo $SubCount; ?>개</td>
											</tr>
										</tfoot>
									</table>

								</div>
								<!-- / 하나의 공급업체 묶음 -->
							</td>
						</tr>
					<?php
						$num++;
					}
					?>
				<?php } ?>
			</tbody>
		</table>
		<?php if(sizeof($resP) <= 0) { ?>
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">정산내역이 없습니다.</div></div>
		<?php } ?>
	</div>
</form>




<!-- ● 정산 총합계 -->
<div class="entershop_summ">
	<table class="table_summ">
		<colgroup>
			<col width="200"/><col width="*"/><col width="*"/><col width="*"/><col width="*"/>
		</colgroup>
		<tbody>
			<tr>
				<th rowspan="2" class="title">정산 총 합계</th>
				<th>구매합계</th>
				<th>배송비</th>
				<th>업체수수료</th>
				<th>할인금액</th>
				<th>수수료</th>
			</tr>
			<tr>
				<td><span class="lineup"><strong class="t_red"><?php echo number_format($TotalSumPrice); ?></strong><em>원</em></span></td>
				<td><span class="lineup"><strong class="t_black"><?php echo number_format($TotalDeliveryPrice); ?></strong><em>원</em></span></td>
				<td><span class="lineup"><strong class="t_green"><?php echo number_format($TotalComPrice); ?></strong><em>원</em></span></td>
				<td><span class="lineup"><strong class="t_black"><?php echo number_format($TotalUsePoint); ?></strong><em>원</em></span></td>
				<td><span class="lineup"><strong class="t_sky"><?php echo number_format($TotalDiscount); ?></strong><em>원</em></span></td>
			</tr>
		</tbody>
	</table>
</div>

<script type="text/javascript">
	// 정산대기 상세보기 토글
	$('.js_detail_view').on('click', function(e) {
		e.preventDefault();
		var su = $(this);
		var status = (su.hasClass('now_open')?'open':'close');
		su.removeClass('now_open');
		su.removeClass('now_close');
		if(status == 'open') { // 열려있다면 닫는다.
			su.addClass('now_close');
			su.find('.tx').text('정산정보 펼쳐보기');
			su.closest('tr').find('.js_detail_box').addClass('if_closed');
		}
		else { // 닫혀있다면 연다,.
			su.addClass('now_open');
			su.find('.tx').text('정산정보 간편보기');
			su.closest('tr').find('.js_detail_box').removeClass('if_closed');
		}
	});

	// 입점내부 전체선택
	$('.com_checked').on('click', function(e) {
		var com = $(this).data('com');
		var ck = $(this).is(':checked');
		$('.class_uid[data-com='+com+']').attr('checked', ck);
	});

	// 전체선택/해제
	function AllChecked(_mode) {
		$('.class_uid').prop('checked', false);
		if(_mode == 'active') $('.class_uid').prop('checked', true);
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