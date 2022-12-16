<?php
/*
	accesskey {
		s: 검색
		l: 전체리스트(검색결과 페이지에서 작동)
	}
*/
# 매출분석 - 일별


	// 일자계산 - 시작일자 정의
	$pass_date = ($pass_date?$pass_date:date('Y-m-d', strtotime("-1 week")));
	$Select_Year = date('Y', strtotime($pass_date));
	$Select_Month = date('m', strtotime($pass_date));
	$Select_Day = date('d', strtotime($pass_date));

	// 일자계산 - 종료일자 정의
	$pass_edate = ($pass_edate?$pass_edate:date('Y-m-d', time()));
	$Select_eYear = date('Y', strtotime($pass_edate));
	$Select_eMonth = date('m', strtotime($pass_edate));
	$Select_eDay = date('d', strtotime($pass_edate));


	// JJC : 주문 취소항목 추출 : 2018-01-04
	$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));


	// ---- 요약 ----
	$arr_sum = array();
	$que = "

			SELECT

				SUM( IF( mobile = 'Y' , o_price_real , 0 ) ) as sum_mobileY_total_price,
				SUM( IF( mobile != 'Y' , o_price_real , 0 ) ) as sum_mobileN_total_price,

				SUM( IF( o_memtype = 'N' , o_price_real , 0 ) ) as sum_memtypeN_total_price,
				SUM( IF( o_memtype != 'N' , o_price_real , 0  )) as sum_memtypeY_total_price,

				SUM( IF( mobile = 'Y' , IF( o_canceled =  'Y', o_price_real, ". $add_que_cancel ." ) , 0 ) ) as sum_mobileY_cancel_price,
				SUM( IF( mobile != 'Y' , IF( o_canceled =  'Y', o_price_real, ". $add_que_cancel ." ) , 0 ) ) as sum_mobileN_cancel_price,

				SUM( IF( o_memtype = 'N' , IF( o_canceled =  'Y', o_price_real, ". $add_que_cancel ." ) , 0 ) ) as sum_memtypeN_cancel_price,
				SUM( IF( o_memtype != 'N' , IF( o_canceled =  'Y', o_price_real, ". $add_que_cancel ." ) , 0  )) as sum_memtypeY_cancel_price

			FROM smart_order

			WHERE
				o_paystatus = 'Y' AND
				DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'

	";
	$res = _MQ($que);
	foreach( $res as $k=>$v ){
		$arr_sum[$k] = $v;
	}
	// ---- 요약 ----



	// ------- 매출 - 요일별 목록 -------
	$arr_data = $arr_res = $arr_max = $arr_tot_cumul = array();

	// JJC : 주문 할인항목 추출 : 2018-01-04
	$add_que_discount = implode(" + " , array_keys($arr_order_discount_field));

	// JJC : 주문 취소항목 추출 : 2018-01-04
	$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

	$que = "

			SELECT

				DATE_FORMAT(o_rdate, '%w') as rdate ,

				'------------ PC 부분 ------------ ' as dummy_pc ,
				SUM( IF( mobile != 'Y' , IF( npay_order = 'Y' ,  ( o_price_real - o_price_delivery ) , o_price_total) , 0 ) ) as mobileN_sum_price_total,
				SUM( IF( mobile != 'Y' , o_price_delivery , 0 ) ) as mobileN_sum_price_delivery,
				SUM( IF( mobile != 'Y' , ". $add_que_discount ." , 0 ) ) as mobileN_sum_discount,
				SUM( IF( mobile != 'Y' , o_price_real , 0 ) ) as mobileN_sum_price_real,
				SUM( IF( mobile != 'Y' , IF( o_canceled =  'Y', o_price_real, ". $add_que_cancel ." ) , 0 ) ) as mobileN_sum_price_refund,
				SUM( IF( mobile != 'Y' , o_price_supplypoint , 0 ) ) as mobileN_sum_price_supplypoint,

				'------------ MOBILE 부분 ------------ ' as dummy_mobile ,
				SUM( IF( mobile = 'Y' , IF( npay_order = 'Y' ,  ( o_price_real - o_price_delivery ) , o_price_total) , 0 ) ) as mobileY_sum_price_total,
				SUM( IF( mobile = 'Y' , o_price_delivery , 0 ) ) as mobileY_sum_price_delivery,
				SUM( IF( mobile = 'Y' , ". $add_que_discount ." , 0 ) ) as mobileY_sum_discount,
				SUM( IF( mobile = 'Y' , o_price_real , 0 ) ) as mobileY_sum_price_real,
				SUM( IF( mobile = 'Y' , IF( o_canceled =  'Y', o_price_real, ". $add_que_cancel ." ) , 0 ) ) as mobileY_sum_price_refund,
				SUM( IF( mobile = 'Y' , o_price_supplypoint , 0 ) ) as mobileY_sum_price_supplypoint


			FROM smart_order

			WHERE
				o_paystatus = 'Y' AND
				DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'

		GROUP BY rdate
		ORDER BY rdate ASC
	";
	$res = _MQ_assoc($que);
	foreach( $res as $k=>$v ){
		foreach( $v as $sk=>$sv ){
			$arr_res[$v['rdate']][$sk] = $sv;
		}

		$arr_data['mobileY'][$v['rdate']] = $v['mobileY_sum_price_real'];
		$app_max['mobileY'] = ($app_max['mobileY'] < $v['mobileY_sum_price_real'] ? $v['mobileY_sum_price_real'] : $app_max['mobileY']);

		$arr_data['mobileN'][$v['rdate']] = $v['mobileN_sum_price_real'];
		$app_max['mobileN'] = ($app_max['mobileN'] < $v['mobileN_sum_price_real'] ? $v['mobileN_sum_price_real'] : $app_max['mobileN']);

		$arr_tot_cumul[$v['rdate']] = array(
			'idx' => $arr_tot_cumul[$v['rdate']]['idx'] + 1 , // 횟수
			'cnt' => $arr_tot_cumul[$v['rdate']]['cnt'] + $v['mobileY_sum_price_real'] + $v['mobileN_sum_price_real'] // 누적수
		);

	}
	// ------- 매출 - 요일별 목록 -------

	// ------- 기간내 매출 -------
	$arr_tot_data = array();
	$app_tot_max = 0; // 기간내 매출 평균 최대치
	$arr_tot_maxmin = array(); // 기간내 매출 평균 최대 최소 방문정보 배열 기록
	foreach($arr_tot_cumul as $k=>$v){
		$v['idx'] = $v['idx'] > 0 ? $v['idx'] : 1; // 횟수
		$avg_cnt = round($v['cnt'] / $v['idx']);
		$arr_tot_data[$k] = $avg_cnt;
		$app_tot_max = ($app_tot_max < $avg_cnt ? $avg_cnt : $app_tot_max);
	}
	// ------- 기간내 가입 매출 -------


	# Chart 그래프 적용
	$arr_date_num = array(); // 요일별 매출
	$arr_date_date = array(); // 요일
	$arr_date_color = array(); // 그래프 색
	$arr_date_border = array(); // 그래프 border 색

	$arr_tot_date_num = array(); // 기간내 전체 매출
	$arr_tot_date_date = array(); // 기간내 전체 매출 날짜
	$arr_tot_date_color = array(); // 기간내 전체 매출 그래프 색
	$arr_tot_date_border = array(); // 기간내 전체 매출 그래프 border 색

	$arr_avg = $arr_max_avg = array();
	for($i=0 ; $i<=6 ; $i++){

		// ------------------------ 가입기기별 ------------------------
		$arr_date_date[$i] = week_name( $i , '요일');// 요일

		$arr_date_num['mobileN'][$i] = $arr_data['mobileN'][$i] * 1;// 선택일자 PC 매출
		$arr_date_num['mobileY'][$i] = $arr_data['mobileY'][$i] * 1;// 선택일자 모바일 매출

		// PC - 최대값일 경우
		if( $app_max['mobileN'] == $arr_date_num['mobileN'][$i] && $app_max['mobileN'] > 0) {
			$arr_date_color['mobileN'][$i] = "rgba(255, 99, 132, 0.2)"; // 그래프 색
			$arr_date_border['mobileN'][$i] = "rgba(255,99,132,1)"; // 그래프 border 색
		}
		// PC - 일반 데이터 일 경우
		else {
			$arr_date_color['mobileN'][$i] = "rgba(54, 162, 235, 0.2)"; // 그래프 색
			$arr_date_border['mobileN'][$i] = "rgba(54, 162, 235, 1)"; // 그래프 border 색
		}

		// MOBILE - 최대값일 경우
		if( $app_max['mobileY'] == $arr_date_num['mobileY'][$i] && $app_max['mobileY'] > 0) {
			$arr_date_color['mobileY'][$i] = "rgba(0, 128, 0, 0.2)"; // 그래프 색
			$arr_date_border['mobileY'][$i] = "rgba(0, 128, 0,1)"; // 그래프 border 색
		}
		// MOBILE - 일반 데이터 일 경우
		else {
			$arr_date_color['mobileY'][$i] = "rgba(128, 0, 255, 0.2)"; // 그래프 색
			$arr_date_border['mobileY'][$i] = "rgba(128, 0, 255, 1)"; // 그래프 border 색
		}


		// PC - 최대값 체크
		$arr_maxmin['mobileN']['max'] = ( $arr_maxmin['mobileN']['max']['cnt'] < $arr_date_num['mobileN'][$i] ? array('cnt'=>$arr_date_num['mobileN'][$i] , 'date'=> $i ) : $arr_maxmin['mobileN']['max']);

		// MOBILE - 최대값 체크
		$arr_maxmin['mobileY']['max'] = ( $arr_maxmin['mobileY']['max']['cnt'] < $arr_date_num['mobileY'][$i] ? array('cnt'=>$arr_date_num['mobileY'][$i] , 'date'=> $i ) : $arr_maxmin['mobileY']['max']);



		// PC - 등록되어 있지 않은 경우 무조건 등록
		if(!isset($arr_maxmin['mobileN']['min']['cnt'])) {
			$arr_maxmin['mobileN']['min'] = array('cnt'=>$arr_date_num['mobileN'][$i] , 'date'=> $i );
			$arr_avg['mobileN']['idx'] ++; // 계산할 횟수
			$arr_avg['mobileN']['cnt'] += $arr_date_num['mobileN'][$i]; // 계산할 접속수
		}
		// PC - 최소 정보일 경우 현요일 제외
		else if(date("Ymd") > $i){
			$arr_maxmin['mobileN']['min'] = ( $arr_maxmin['mobileN']['min']['cnt'] > $arr_date_num['mobileN'][$i] ? array('cnt'=>$arr_date_num['mobileN'][$i] , 'date'=> $i ) : $arr_maxmin['mobileN']['min']);
			$arr_avg['mobileN']['idx'] ++; // 계산할 횟수
			$arr_avg['mobileN']['cnt'] += $arr_date_num['mobileN'][$i]; // 계산할 접속수
		}

		// MOBILE - 등록되어 있지 않은 경우 무조건 등록
		if(!isset($arr_maxmin['mobileY']['min']['cnt'])) {
			$arr_maxmin['mobileY']['min'] = array('cnt'=>$arr_date_num['mobileY'][$i] , 'date'=> $i );
			$arr_avg['mobileY']['idx'] ++; // 계산할 횟수
			$arr_avg['mobileY']['cnt'] += $arr_date_num['mobileY'][$i]; // 계산할 접속수
		}
		// MOBILE - 최소 정보일 경우 현요일 제외
		else if(date("Ymd") > $i){
			$arr_maxmin['mobileY']['min'] = ( $arr_maxmin['mobileY']['min']['cnt'] > $arr_date_num['mobileY'][$i] ? array('cnt'=>$arr_date_num['mobileY'][$i] , 'date'=> $i ) : $arr_maxmin['mobileY']['min']);
			$arr_avg['mobileY']['idx'] ++; // 계산할 횟수
			$arr_avg['mobileY']['cnt'] += $arr_date_num['mobileY'][$i]; // 계산할 접속수
		}
		// ------------------------ 가입기기별 ------------------------


		// ------------------------ 기간내 매출 평균 ------------------------
		$arr_tot_date_num[$i] = $arr_tot_data[$i] * 1;// 선택일자 회원수
		$arr_tot_date_date[$i] = week_name( $i , '요일');// 요일

		//최대값일 경우
		if( $app_tot_max == $arr_tot_date_num[$i] && $app_tot_max > 0) {
			$arr_tot_date_color[$i] = "rgba(255, 99, 132, 0.2)"; // 그래프 색
			$arr_tot_date_border[$i] = "rgba(255,99,132,1)"; // 그래프 border 색
		}
		// 일반 데이터 일 경우
		else {
			$arr_tot_date_color[$i] = "rgba(54, 162, 235, 0.2)"; // 그래프 색
			$arr_tot_date_border[$i] = "rgba(54, 162, 235, 1)"; // 그래프 border 색
		}

		// 최대값 체크
		$arr_tot_maxmin['max'] = ( $arr_tot_maxmin['max']['cnt'] < $arr_tot_date_num[$i] ? array('cnt'=>$arr_tot_date_num[$i] , 'date'=> $i ) : $arr_tot_maxmin['max']);

		// 등록되어 있지 않은 경우 무조건 등록
		if(!isset($arr_tot_maxmin['min']['cnt'])) {
			$arr_tot_maxmin['min'] = array('cnt'=>$arr_tot_date_num[$i] , 'date'=> $i );
			$arr_max_avg['idx'] ++; // 계산할 횟수
			$arr_max_avg['cnt'] += $arr_tot_date_num[$i]; // 계산할 접속수
		}
		else {
			$arr_tot_maxmin['min'] = ( $arr_tot_maxmin['min']['cnt'] > $arr_tot_date_num[$i] ? array('cnt'=>$arr_tot_date_num[$i] , 'date'=> $i ) : $arr_tot_maxmin['min']);
			$arr_max_avg['idx'] ++; // 계산할 횟수
			$arr_max_avg['cnt'] += $arr_tot_date_num[$i]; // 계산할 접속수
		}
		// ------------------------ 기간내 매출 평균 ------------------------


	}

?>

<!-- 기간검색 -->
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
	<input type="hidden" name="_type" value="<?php echo $_type; ?>">
	<input type="hidden" name="_mode" value="search">
	<div class="data_form if_search">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>기간선택</th>
					<td>
						<input type="text" name="pass_date" class="design js_pic_day_max_today" value="<?php echo $pass_date; ?>" style="width:90px" readonly>
						<span class="fr_tx">~</span>
						<input type="text" name="pass_edate" class="design js_pic_day_max_today" value="<?php echo $pass_edate; ?>" style="width:90px" readonly>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"></span></li>
				<?php if($_mode == 'search'){ ?>
					<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?_type=<?=$_type?>" class="c_btn h34 black line normal" accesskey="l">검색풀기</a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</form>
<!-- / 기간검색 -->



<!-- 그래프&추이 -->
<div class="data_list">
	<table style="width:100%; box-sizing:border-box;">
	<tbody>
		<tr>
			<td >

				<? // ------- 매출 Summary ------- ?>
				<div class="group_title"><strong><?=DATE("Y년 m월 d일" , strtotime($pass_date) )?> ~ <?=DATE("Y년 m월 d일" , strtotime($pass_edate) )?> 요일별 매출 요약</strong></div>
				<div class="data_list" style="margin-bottom:30px; ">
					<table class="table_list if_counter_table">
						<colgroup>
							<col width="10%"><col width="10%"><col width="10%"><col width="10%"><col width="10%">
							<col width="10%"><col width="10%"><col width="10%"><col width="10%"><col width="10%">
						</colgroup>
						<tbody>
							<tr>
								<th colspan="2">총 매출</th>
								<th colspan="2">PC 매출</th>
								<th colspan="2">모바일 매출</th>
								<th colspan="2">회원 매출</th>
								<th colspan="2">비회원 매출</th>
							</tr>
							<tr>
								<td class="disabled">총매출액</td>
								<td><span class="fr_tx no_left"><span class=" bold"><?php echo number_format($arr_sum['sum_mobileY_total_price'] + $arr_sum['sum_mobileN_total_price']); ?></span>원</span></td>
								<td class="disabled">총매출액</td>
								<td><span class="fr_tx no_left"><span class=" bold"><?php echo number_format($arr_sum['sum_mobileN_total_price']); ?></span>원</span></td>
								<td class="disabled">총매출액</td>
								<td><span class="fr_tx no_left"><span class=" bold"><?php echo number_format($arr_sum['sum_mobileY_total_price']); ?></span>원</span></td>
								<td class="disabled">총매출액</td>
								<td><span class="fr_tx no_left"><span class=" bold"><?php echo number_format($arr_sum['sum_memtypeY_total_price']); ?></span>원</span></td>
								<td class="disabled">총매출액</td>
								<td><span class="fr_tx no_left"><span class=" bold"><?php echo number_format($arr_sum['sum_memtypeN_total_price']); ?></span>원</span></td>
							</tr>

							<tr>
								<td class="disabled">취소/환불</td>
								<td><span class="fr_tx no_left"><span class="t_orange "><?php echo number_format($arr_sum['sum_mobileY_cancel_price'] + $arr_sum['sum_mobileN_cancel_price']); ?></span>원</span></td>
								<td class="disabled">취소/환불</td>
								<td><span class="fr_tx no_left"><span class="t_orange "><?php echo number_format($arr_sum['sum_mobileN_cancel_price']); ?></span>원</span></td>
								<td class="disabled">취소/환불</td>
								<td><span class="fr_tx no_left"><span class="t_orange "><?php echo number_format($arr_sum['sum_mobileY_cancel_price']); ?></span>원</span></td>
								<td class="disabled">취소/환불</td>
								<td><span class="fr_tx no_left"><span class="t_orange "><?php echo number_format($arr_sum['sum_memtypeY_cancel_price']); ?></span>원</span></td>
								<td class="disabled">취소/환불</td>
								<td><span class="fr_tx no_left"><span class="t_orange "><?php echo number_format($arr_sum['sum_memtypeN_cancel_price']); ?></span>원</span></td>
							</tr>
							<tr>
								<td class="disabled"><span class="bold">실매출액</span></td>
								<td><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_mobileY_total_price'] + $arr_sum['sum_mobileN_total_price'] - $arr_sum['sum_mobileY_cancel_price'] - $arr_sum['sum_mobileN_cancel_price']); ?></span>원</span></td>
								<td class="disabled"><span class="bold">실매출액</span></td>
								<td><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_mobileN_total_price'] - $arr_sum['sum_mobileN_cancel_price']); ?></span>원</span></td>
								<td class="disabled"><span class="bold">실매출액</span></td>
								<td><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_mobileY_total_price'] - $arr_sum['sum_mobileY_cancel_price']); ?></span>원</span></td>
								<td class="disabled"><span class="bold">실매출액</span></td>
								<td><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_memtypeY_total_price'] - $arr_sum['sum_memtypeY_cancel_price']); ?></span>원</span></td>
								<td class="disabled"><span class="bold">실매출액</span></td>
								<td><span class="fr_tx no_left"><span class="t_orange bold"><?php echo number_format($arr_sum['sum_memtypeN_total_price'] - $arr_sum['sum_memtypeN_cancel_price']); ?></span>원</span></td>
							</tr>
							<tr>
								<td colspan="10">
									<div class="tip_box">
										<?=_DescStr("결제가 된 <strong>주문 건(취소 주문 포함)을 기준으로 매출 정보</strong>를 추출합니다.");?>
										<?=_DescStr("총매출액은 최초 주문시 결제된 금액을 합계 입니다. <strong>총매출액 = 구매비용 + 배송비 - 할인액</strong>");?>
										<?=_DescStr("취소/환불은 부분취소 및 주문취소의 합계액입니다.");?>
										<?=_DescStr("실매출액은 총매출액에서 취소/환불을 제외한 합계입니다. <strong>실매출액 = 총매출액 - 취소/환불</strong>");?>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<? // ------- 매출 Summary ------- ?>
			</td>
		</tr>
		<tr>
			<td style="text-align:center; ">
				<div style='width:100%; display:inline-block; margin-bottom:30px; '><canvas id="js_couter_chart1" ></canvas></div><!-- 접속기기 -->
			</td>
		</tr>
	</tbody>
</table>
</div>
<!-- / 그래프&추이 -->


<div class="group_title"><!-- 공백을 위한 추가 --></div>


<div class="data_list">
	<div class="list_ctrl">
		<div class="right_box">
			<a href="#none" onclick="searchExcel(); return false;" class="c_btn icon icon_excel">엑셀다운로드</a>
		</div>
	</div>


	<?// ----- 표 테이블  -----?>
	<div ID="grid_table"></div>

	<div class="data_summery" style="border-top:0px">
		<div class="tip_box">
			<?=_DescStr("<strong>구매총액 :</strong> 주문상품의 구매비용의 총액");?>
			<?=_DescStr("<strong>할인액 :</strong> 적립금 사용액 , 보너스쿠폰사용액 , 상품쿠폰사용액 , 프로모션코드 할인액 등의 할인 총액");?>
			<?=_DescStr("<strong>실결제액 :</strong> 구매총액 + 배송비 - 할인액 ");?>
			<?=_DescStr("<strong>실매출액 :</strong> 실결제액 - 취소/환불 ");?>
		</div>
	</div>

</div>
<!-- / 도표 -->



<form name="frmSearch" method="post" action="_static_sale.pro.php" >
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="Select_Year" value="<?php echo $Select_Year; ?>">
	<input type="hidden" name="Select_Month" value="<?php echo $Select_Month; ?>">
	<input type="hidden" name="Select_Day" value="<?php echo $Select_Day; ?>">
	<input type="hidden" name="Select_eYear" value="<?php echo $Select_eYear; ?>">
	<input type="hidden" name="Select_eMonth" value="<?php echo $Select_eMonth; ?>">
	<input type="hidden" name="Select_eDay" value="<?php echo $Select_eDay; ?>">
</form>


<script>
	// --- 검색 엑셀 ---
	function searchExcel() {
		$('form[name="frmSearch"]').children('input[name="_mode"]').val('all_week_search');
		$('form[name="frmSearch"]').attr('action', '_static_sale.pro.php');
		$('form[name="frmSearch"]')[0].submit();
	}
	// --- 검색 엑셀 ---
</script>


<script src="./js/chart.js/Chart.bundle.min.js"></script>
<script>
	// ---------- 기본 line-bar 그래프 ----------
	var background = 'rgba(255,99,132,1)';
	var chartData = {
		labels: ["<?=implode('", "' , array_values($arr_tot_date_date))?>"],
		datasets: [
		// PC 매출
		{
			type: 'bar',
			label: '요일별 PC 매출',
			data: [<?=implode(' , ' , array_values($arr_date_num['mobileN']))?>],
			backgroundColor: ["<?=implode('", "' , array_values($arr_date_color['mobileN']))?>"],
			borderColor: ["<?=implode('", "' , array_values($arr_date_border['mobileN']))?>"],
			borderWidth: 1
		},
		// 모바일 매출
		{
			type: 'bar',
			label: '요일별 모바일 매출',
			data: [<?=implode(' , ' , array_values($arr_date_num['mobileY']))?>],
			backgroundColor: ["<?=implode('", "' , array_values($arr_date_color['mobileY']))?>"],
			borderColor: ["<?=implode('", "' , array_values($arr_date_border['mobileY']))?>"],
			borderWidth: 1
		},
		// 전체매출
		{
			type: 'line',
			label: '요일별 매출',
			data: [<?=implode(' , ' , array_values($arr_tot_date_num))?>],
            borderColor : background,
            pointBorderColor : ["<?=implode('", "' , array_values($arr_tot_date_border))?>"],
            pointBackgroundColor : ["<?=implode('", "' , array_values($arr_tot_date_color))?>"],
            pointBorderWidth : 1,
			borderWidth: 1,
			fill:false
		}]
	};
	// ---------- 기본 line-bar 그래프 ----------


	window.onload = function() {

		var ctx = document.getElementById("js_couter_chart1").getContext("2d");
		var myChart = new Chart(ctx, {type: 'bar',
			data: chartData,
			options: {scales: {yAxes: [{ticks: {beginAtZero:false}}]}}
		});
	};
</script>













<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>
<?php
	// ------- 표 - 테이블 데이터 -------

	// grid cell에 클래스 적용
	$arr_class_data = array();
	$arr_class_data['rdate'] = array('grid'); // 날짜 영역 grid_no 클래스 적용

	$arr_table_data = array();

	for($i=0 ; $i<=6 ; $i++){

		$app_date = week_name( $i , '요일');
		$app_date_key = $i;

		// ----- 소계 -----
		$arr_table_data[] = array(
				'_extraData' => array(
					'rowSpan' =>array('rdate' => 3),// 날짜 열별합 - 3개(rowspan=3)
					'className' =>array(
						'row' => array('grid')// 행에 디자인 클래스를 적용
					)
				),
				'rdate' => $app_date,
				'device' => '소계',

				'price_total' => number_format($arr_res[$app_date_key]['mobileN_sum_price_total'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_total'] * 1),//구매총액
				'price_delivery' => number_format($arr_res[$app_date_key]['mobileN_sum_price_delivery'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_delivery'] * 1),//배송비
				'discount' => number_format($arr_res[$app_date_key]['mobileN_sum_discount'] * 1 + $arr_res[$app_date_key]['mobileY_sum_discount'] * 1),//할인액
				'price_real' => number_format($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_real'] * 1),//실결제액
				'price_refund' => number_format($arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1),//취소/환불
				'sale_real' => number_format(($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_real'] * 1) - ($arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1)),//실매출액
				'price_supplypoint' => number_format($arr_res[$app_date_key]['mobileN_sum_price_supplypoint'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_supplypoint'] * 1)//포인트적립
		);
		// ----- 소계 -----


		// ----- PC -----
		$arr_table_data[] = array(
				'device' => 'PC',

				'price_total' => number_format($arr_res[$app_date_key]['mobileN_sum_price_total'] * 1),//구매총액
				'price_delivery' => number_format($arr_res[$app_date_key]['mobileN_sum_price_delivery'] * 1),//배송비
				'discount' => number_format($arr_res[$app_date_key]['mobileN_sum_discount'] * 1),//할인액
				'price_real' => number_format($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1),//실결제액
				'price_refund' => number_format($arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1),//취소/환불
				'sale_real' => number_format($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1 - $arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1),//실매출액
				'price_supplypoint' => number_format($arr_res[$app_date_key]['mobileN_sum_price_supplypoint'] * 1)//포인트적립
		);
		// ----- PC -----


		// ----- 모바일 -----
		$arr_table_data[] = array(
				'device' => '모바일',

				'price_total' => number_format($arr_res[$app_date_key]['mobileY_sum_price_total'] * 1),//구매총액
				'price_delivery' => number_format($arr_res[$app_date_key]['mobileY_sum_price_delivery'] * 1),//배송비
				'discount' => number_format($arr_res[$app_date_key]['mobileY_sum_discount'] * 1),//할인액
				'price_real' => number_format($arr_res[$app_date_key]['mobileY_sum_price_real'] * 1),//실결제액
				'price_refund' => number_format($arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1),//취소/환불
				'sale_real' => number_format($arr_res[$app_date_key]['mobileY_sum_price_real'] * 1 - $arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1),//실매출액
				'price_supplypoint' => number_format($arr_res[$app_date_key]['mobileY_sum_price_supplypoint'] * 1)//포인트적립
		);
		// ----- 모바일 -----

	}
	// ------- 표 - 테이블 데이터 -------

?>

<link rel="stylesheet" type="text/css" href="./js/tui.grid/tui-grid.min.css">
<script type="text/javascript" src="/include/js/underscore.min.js"></script>
<script type="text/javascript" src="/include/js/backbone-min.js"></script>
<script type="text/javascript" src="./js/tui.grid/tui-code-snippet.min.js"></script>
<script src="./js/tui.grid/grid.min.js"></script>
<script type="text/javascript" class="code-js">

    var grid = new tui.Grid({
        el: $('#grid_table'),

        columnFixCount: 2,
        headerHeight: 40,
		rowHeight  : 35,
        displayRowCount: 12,
        minimumColumnwidth : 50,
        autoNumbering: false,

        columnModelList: [
            {"title" : "<b>요일</b>", "columnName" : "rdate", "align" : "center", "width" : 90 },
			{"title" : "<b>접속기기</b>", "columnName" : "device", "align" : "center", "width" : 90 },
			{"title" : "구매총액", "columnName" : "price_total", "align" : "right", "width" : 120 },
			{"title" : "배송비", "columnName" : "price_delivery", "align" : "right", "width" : 120 },
			{"title" : "할인액", "columnName" : "discount", "align" : "right", "width" : 120 },
			{"title" : "실결제액", "columnName" : "price_real", "align" : "right", "width" : 120 },
			{"title" : "취소/환불", "columnName" : "price_refund", "align" : "right", "width" : 120 },
			{"title" : "실매출액", "columnName" : "sale_real", "align" : "right", "width" : 120 },
			{"title" : "적립금", "columnName" : "price_supplypoint", "align" : "right", "width" : 90 }
        ]
    });

	var table_data = <?=json_encode($arr_table_data)?>;
	grid.setRowList(table_data);

</script>
<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>