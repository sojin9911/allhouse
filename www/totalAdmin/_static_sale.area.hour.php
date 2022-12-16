<?php
/*
	accesskey {
		s: 검색
		l: 전체리스트(검색결과 페이지에서 작동)
	}
*/
# 매출분석 - 지역별 - 일별


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


	// 색상 배열
	$arr_color = array(
		"rgba(0, 0, 255, 0.5)",
		"rgba(255, 0, 0, 0.5)",
		"rgba(0, 128, 0, 0.5)",
		"rgba(128, 0, 255, 0.5)",
		"rgba(255, 0, 128, 0.5)",
		"rgba(0, 128, 128, 0.5)",
		"rgba(128, 0, 0, 0.5)",
		"rgba(255, 128, 0, 0.5)",
		"rgba(0, 128, 255, 0.5)",
		"rgba(255, 128, 255, 0.5)",
		"rgba(0, 0, 255, 0.5)",
		"rgba(255, 0, 0, 0.5)",
		"rgba(0, 128, 0, 0.5)",
		"rgba(128, 0, 255, 0.5)",
		"rgba(255, 0, 128, 0.5)",
		"rgba(0, 128, 128, 0.5)",
		"rgba(128, 0, 0, 0.5)",
		"rgba(255, 128, 0, 0.5)",
		"rgba(0, 128, 255, 0.5)",
		"rgba(255, 128, 255, 0.5)"
	);

	// 지역별 색상 지정
	foreach($arr_order_area_basic as $k=>$v){
		$arr_order_area_color[$v] = $arr_color[$k];
	}

	// ---- 요약 ----
	$arr_sum = $arr_sum2 = $arr_app_color = $arr_label = array();
	$que = "
			SELECT
				o.o_area,
				IF(o.mobile = 'Y' , 'Y' , 'N') as mobile,
				SUM( o.o_price_real - (". $add_que_cancel .") ) as sum_real_price
			FROM smart_order as o
			WHERE
				o.o_paystatus = 'Y' AND
				o.o_canceled = 'N' AND
				DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'
			GROUP BY o_area , mobile
	";
	$res = _MQ_assoc($que);
	foreach( $res as $k=>$v ){
		$arr_sum[$v['mobile']][$v['o_area']] = $v['sum_real_price'];
		$arr_sum2[$v['o_area']] += $v['sum_real_price'];
		$arr_app_color[$v['o_area']] = $arr_order_area_color[$v['o_area']];
		$arr_label[$v['o_area']] = $v['o_area'];
	}
	// ---- 요약 ----


	// ------- 매출 - 지역별 - 시간별 목록 -------

	$arr_res = array();
	$que = "
			SELECT

				HOUR(o_rdate) as rdate ,
				o.o_area,
				IF(mobile = 'Y' , 'Y' , 'N') as mobile,
				SUM( o_price_real - (". $add_que_cancel .") ) as sum_real_price

			FROM smart_order as o

			WHERE
				o_paystatus = 'Y' AND
				o_canceled = 'N' AND
				DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'

		GROUP BY rdate , o_area , mobile
		ORDER BY rdate ASC
	";
	$res = _MQ_assoc($que);
	foreach( $res as $k=>$v ){
		$arr_res[$v['rdate']][$v['mobile']][$v['o_area']] = $v['sum_real_price'];
		$arr_res[$v['rdate']][$v['mobile']]['sum'] += $v['sum_real_price'];
	}
	// ------- 매출 - 지역별 - 시간별 목록 -------

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
				<div class="group_title"><strong><?php echo $Select_Year; ?>년 <?php echo $Select_Month; ?>월 지역 - 시간별 매출 요약</strong></div>
				<div class="data_list" style="margin-bottom:30px; ">
					<table class="table_list if_counter_table">
						<colgroup>
							<col width="8.3%"><col width="8.3%"><col width="8.3%"><col width="8.3%"><col width="8.3%"><col width="8.3%">
							<col width="8.3%"><col width="8.3%"><col width="8.3%"><col width="8.3%"><col width="8.3%"><col width="8.3%">
						</colgroup>
						<tbody>
							<tr>
								<th colspan="2" >실결제액</th>
								<?php
									foreach($arr_order_area_basic as $k=>$v){
										if($k > 4 ) {continue;}
										echo '<th colspan="2">'. $v .'</th>';
									}
								?>
							</tr>


							<!-- 총계 -->
							<?
								$arr_sum['N'] = IS_ARRAY($arr_sum['N']) ? $arr_sum['N'] : array();
								$arr_sum['Y'] = IS_ARRAY($arr_sum['Y']) ? $arr_sum['Y'] : array();
								$app_total_sum = array_sum($arr_sum['N']) + array_sum($arr_sum['Y']);
							?>
							<tr>
								<td class="disabled"><span class="bold">총계</span></td>
								<td><span class="no_left"><span class="t_orange bold"><?php echo number_format($app_total_sum); ?></span>원</span></td>
								<?php
									$total_sum = $app_total_sum > 0 ? $app_total_sum : 1;
									foreach($arr_order_area_basic as $k=>$v){
										if($k > 4 ) {continue;}
										echo '
											<td class="disabled"><span class="bold">총계</span></td>
											<td >
												<span class="no_left">
													<span class="t_orange bold">' . number_format($arr_sum['N'][$v] + $arr_sum['Y'][$v]) . '</span>원<br>
													<span class="t_sky">(' . number_format( ($arr_sum['N'][$v] + $arr_sum['Y'][$v]) * 100 / $total_sum , 2) . '%)</span>
												</span>
											</td>
										';
									}
								?>
							</tr>

							<!-- PC -->
							<tr>
								<td class="disabled">PC</td>
								<td><span class="no_left"><span class="t_orange "><?php echo number_format(array_sum($arr_sum['N'])); ?></span>원</span></td>
								<?php
									$total_sum_N = array_sum($arr_sum['N']) > 0 ? array_sum($arr_sum['N']) : 1;
									foreach($arr_order_area_basic as $k=>$v){
										if($k > 4 ) {continue;}
										echo '
											<td class="disabled">PC</td>
											<td >
												<span class="no_left">
													<span class="t_orange ">' . number_format($arr_sum['N'][$v]) . '</span>원<br>
													<span class="t_sky b">(' . number_format($arr_sum['N'][$v] * 100 / $total_sum_N , 2) . '%)</span>
												</span>
											</td>
										';
									}
								?>
							</tr>

							<!-- 모바일 -->
							<tr>
								<td class="disabled">모바일</td>
								<td><span class="no_left"><span class="t_orange "><?php echo number_format(array_sum($arr_sum['Y'])); ?></span>원</span></td>
								<?php
									$total_sum_Y = array_sum($arr_sum['Y']) > 0 ? array_sum($arr_sum['Y']) : 1;
									foreach($arr_order_area_basic as $k=>$v){
										if($k > 4 ) {continue;}
										echo '
											<td class="disabled">모바일</td>
											<td >
												<span class="no_left">
													<span class="t_orange ">' . number_format($arr_sum['Y'][$v]) . '</span>원<br>
													<span class="t_sky">(' . number_format($arr_sum['Y'][$v] * 100 / $total_sum_Y , 2) . '%)</span>
												</span>
											</td>
										';
									}
								?>
							</tr>




							<!-- 2줄 -->
							<tr>
								<?php
									foreach($arr_order_area_basic as $k=>$v){
										if($k > 4 && $k<=10) {
											echo '<th colspan="2">'. $v .'</th>';
										}
									}
								?>
							</tr>

							<!-- 총계 -->
							<tr>
								<?php
									$total_sum = $app_total_sum > 0 ? $app_total_sum : 1;
									foreach($arr_order_area_basic as $k=>$v){
										if($k > 4 && $k<=10) {
											echo '
												<td class="disabled"><span class="bold">총계</span></td>
												<td >
													<span class="no_left">
														<span class="t_orange bold">' . number_format($arr_sum['N'][$v] + $arr_sum['Y'][$v]) . '</span>원<br>
														<span class="t_sky">(' . number_format( ($arr_sum['N'][$v] + $arr_sum['Y'][$v]) * 100 / $total_sum , 2) . '%)</span>
													</span>
												</td>
											';
										}
									}
								?>
							</tr>

							<!-- PC -->
							<tr>
								<?php
									foreach($arr_order_area_basic as $k=>$v){
										if($k > 4 && $k<=10) {
											echo '
												<td class="disabled">PC</td>
												<td >
													<span class="no_left">
														<span class="t_orange ">' . number_format($arr_sum['N'][$v]) . '</span>원<br>
														<span class="t_sky b">(' . number_format($arr_sum['N'][$v] * 100 / $total_sum_N , 2) . '%)</span>
													</span>
												</td>
											';
										}
									}
								?>
							</tr>

							<!-- 모바일 -->
							<tr>
								<?php
									foreach($arr_order_area_basic as $k=>$v){
										if($k > 4 && $k<=10) {
											echo '
												<td class="disabled">모바일</td>
												<td >
													<span class="no_left">
														<span class="t_orange ">' . number_format($arr_sum['Y'][$v]) . '</span>원<br>
														<span class="t_sky">(' . number_format($arr_sum['Y'][$v] * 100 / $total_sum_Y , 2) . '%)</span>
													</span>
												</td>
											';
										}
									}
								?>
							</tr>
							<!-- 2줄 -->


							<!-- 3줄 -->
							<tr>
								<?php
									foreach($arr_order_area_basic as $k=>$v){
										if($k >= 11 ) {
											echo '<th colspan="2">'. $v .'</th>';
										}
									}
								?>
							</tr>

							<!-- 총계 -->
							<tr>
								<?php
									$total_sum = $app_total_sum > 0 ? $app_total_sum : 1;
									foreach($arr_order_area_basic as $k=>$v){
										if($k >= 11 ) {
											echo '
												<td class="disabled"><span class="bold">총계</span></td>
												<td >
													<span class="no_left">
														<span class="t_orange bold">' . number_format($arr_sum['N'][$v] + $arr_sum['Y'][$v]) . '</span>원<br>
														<span class="t_sky">(' . number_format( ($arr_sum['N'][$v] + $arr_sum['Y'][$v]) * 100 / $total_sum , 2) . '%)</span>
													</span>
												</td>
											';
										}
									}
								?>
							</tr>

							<!-- PC -->
							<tr>
								<?php
									foreach($arr_order_area_basic as $k=>$v){
										if($k >= 11 ) {
											echo '
												<td class="disabled">PC</td>
												<td >
													<span class="no_left">
														<span class="t_orange ">' . number_format($arr_sum['N'][$v]) . '</span>원<br>
														<span class="t_sky b">(' . number_format($arr_sum['N'][$v] * 100 / $total_sum_N , 2) . '%)</span>
													</span>
												</td>
											';
										}
									}
								?>
							</tr>

							<!-- 모바일 -->
							<tr>
								<?php
									foreach($arr_order_area_basic as $k=>$v){
										if($k >= 11 ) {
											echo '
												<td class="disabled">모바일</td>
												<td >
													<span class="no_left">
														<span class="t_orange ">' . number_format($arr_sum['Y'][$v]) . '</span>원<br>
														<span class="t_sky">(' . number_format($arr_sum['Y'][$v] * 100 / $total_sum_Y , 2) . '%)</span>
													</span>
												</td>
											';
										}
									}
								?>
							</tr>
							<!-- 3줄 -->


						</tbody>
					</table>
					<div class="data_summery" style="border-top:0px">
						<div class="tip_box">
							<?=_DescStr("취소되지 않고 결제가 된 <strong>정상적인 주문 건을 기준으로 요약 정보</strong>를 추출합니다.");?>
							<?=_DescStr("실결제액은 실매출액(최초 주문시 결제된 금액으로 배송비, 할인액 포함)에서 취소/환불(부분취소 및 주문취소의 합계액)을 제외한 합계입니다. <strong>실결제액 = 실매출액 - 취소/환불</strong>");?>
						</div>
					</div>
				</div>

				<? // ------- 매출 Summary ------- ?>
			</td>
		</tr>


		<?if(sizeof($arr_res) > 0 ) {?>
		<tr>
			<td style="text-align:center; ">
				<div style='width:100%; max-width:800px; display:inline-block; margin-bottom:30px; '><canvas id="js_couter_chart1" ></canvas></div>
			</td>
		</tr>
		<?}?>


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
		$('form[name="frmSearch"]').children('input[name="_mode"]').val('area_hour_search');
		$('form[name="frmSearch"]').attr('action', '_static_sale.pro.php');
		$('form[name="frmSearch"]')[0].submit();
	}
	// --- 검색 엑셀 ---
</script>



<?if(sizeof($arr_res) > 0 ) {?>
<script src="./js/chart.js/Chart.bundle.min.js"></script>
<script>
	// ---------- 파이 - 그래프 ----------
	<?php if(sizeof($arr_sum2) > 0 ) { ?>
    var config1 = {
        type: 'pie',
        data: {
            datasets: [{
				data: [<?=implode(' , ' , array_values($arr_sum2))?>],
				backgroundColor: ["<?=implode('", "' , array_values($arr_app_color))?>"],
            }],
            labels: ["<?=implode('" , "' , array_values($arr_label))?>"]
        },
		options: {
			responsive: true,
			legend: {position: 'top',},
			animation: {animateRotate: false,animateScale: true}
		}
    };

	var ctx1 = document.getElementById("js_couter_chart1").getContext("2d");
	window.myPie = new Chart(ctx1, config1);
	<?php } ?>
	// ---------- 파이 - 그래프 ----------
</script>
<?}?>









<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>
<?php
	// ------- 표 - 테이블 데이터 -------

	// grid cell에 클래스 적용
	$arr_class_data = array();
	$arr_class_data['rdate'] = array('grid_no'); // 날짜 영역 grid_no 클래스 적용
	// 지역 비율별 클래스 적용
	foreach($arr_order_area_basic as $sk=>$sv){
		$arr_class_data['area_' . $sk . '_ratio'] =  array('grid_sky');
	}

	$arr_table_data = array();

	for($i=0 ; $i<=23 ; $i++){

		$app_date = $i . "시";
		$app_date_key = $i;

		$app_total_sum = $arr_res[$app_date_key]['N']['sum'] * 1 + $arr_res[$app_date_key]['Y']['sum'] * 1 ;



		// ----- 소계 -----
		$arr_table_data_tmp1 = array(
				'_extraData' => array(
					'rowSpan' =>array('rdate' => 3),// 날짜 열별합 - 3개(rowspan=3)
					'className' =>array(
						'row' => array('grid'),// 행에 디자인 클래스를 적용
						'column' => $arr_class_data
					)
				),
				'rdate' => $app_date,
				'device' => '소계',
				'price_real' => number_format($app_total_sum)
		);

		$arr_table_data_tmp2 = array();
		$total_sum = $app_total_sum > 0 ? $app_total_sum : 1;
		foreach($arr_order_area_basic as $sk=>$sv){
			$arr_table_data_tmp2 = array_merge($arr_table_data_tmp2 , array(
				'area_' . $sk . '_price' => number_format($arr_res[$app_date_key]['N'][$sv] * 1 + $arr_res[$app_date_key]['Y'][$sv] * 1),
				'area_' . $sk . '_ratio' => number_format(($arr_res[$app_date_key]['N'][$sv] * 1 + $arr_res[$app_date_key]['Y'][$sv] * 1) * 100 / $total_sum , 2) . '%'
			));
		}

		$arr_table_data[] = array_merge($arr_table_data_tmp1 , $arr_table_data_tmp2);
		// ----- 소계 -----



		// ----- PC -----
		$arr_table_data_tmp1 = array(
				'device' => 'PC',
				'price_real' => number_format($arr_res[$app_date_key]['N']['sum'] * 1)
		);

		$arr_table_data_tmp2 = array();
		$total_sum_N = $arr_res[$app_date_key]['N']['sum'] > 0 ? $arr_res[$app_date_key]['N']['sum'] : 1;
		foreach($arr_order_area_basic as $sk=>$sv){
			$arr_table_data_tmp2 = array_merge($arr_table_data_tmp2 , array(
				'area_' . $sk . '_price' => number_format($arr_res[$app_date_key]['N'][$sv] * 1),
				'area_' . $sk . '_ratio' => number_format($arr_res[$app_date_key]['N'][$sv] * 100 / $total_sum_N , 2) . '%'
			));
		}

		$arr_table_data[] = array_merge($arr_table_data_tmp1 , $arr_table_data_tmp2);
		// ----- PC -----



		// ----- 모바일 -----
		$arr_table_data_tmp1 = array(
				'device' => '모바일',
				'price_real' => number_format($arr_res[$app_date_key]['Y']['sum'] * 1)
		);

		$arr_table_data_tmp2 = array();
		$total_sum_Y = $arr_res[$app_date_key]['Y']['sum'] > 0 ? $arr_res[$app_date_key]['Y']['sum'] : 1;
		foreach($arr_order_area_basic as $sk=>$sv){
			$arr_table_data_tmp2 = array_merge($arr_table_data_tmp2 , array(
				'area_' . $sk . '_price' => number_format($arr_res[$app_date_key]['Y'][$sv] * 1),
				'area_' . $sk . '_ratio' => number_format($arr_res[$app_date_key]['Y'][$sv] * 100 / $total_sum_Y , 2) . '%'
			));
		}

		$arr_table_data[] = array_merge($arr_table_data_tmp1 , $arr_table_data_tmp2);
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

        columnFixCount: 3,
        headerHeight: 80,
		rowHeight  : 35,
        displayRowCount: 12,
        minimumColumnwidth : 50,
        autoNumbering: false,

        columnMerge : [
			<?foreach($arr_order_area_basic as $sk=>$sv){?>
				<?echo ( $sk <> 0 ? ',' : '' )?>
				{"title" : "<b><?=$sv?></b>", "columnName" : "area_<?=$sk?>", "columnNameList" : ["area_<?=$sk?>_price", "area_<?=$sk?>_ratio"] }
			<?}?>
        ],

        columnModelList: [
            {"title" : "<b>시간</b>", "columnName" : "rdate", "align" : "center", "width" : 90 },
			{"title" : "<b>접속기기</b>", "columnName" : "device", "align" : "center", "width" : 80 },
			{"title" : "<b>실결제액</b>", "columnName" : "price_real", "align" : "center", "width" : 150 },

			<?foreach($arr_order_area_basic as $sk=>$sv){?>
				<?echo ( $sk <> 0 ? ',' : '' )?>
				{"title" : "금액", "columnName" : "area_<?=$sk?>_price", "align" : "center", "width" : 120 }, {"title" : "비율", "columnName" : "area_<?=$sk?>_ratio", "align" : "center", "width" : 70 }
			<?}?>
        ]
    });

	var table_data = <?=json_encode($arr_table_data)?>;
	grid.setRowList(table_data);

</script>
<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>