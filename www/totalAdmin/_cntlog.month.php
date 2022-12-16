<?php

	// 일자계산 
	$pass_sdate = $pass_sdate ? $pass_sdate : date('Y');


	$arr_sum = array();

	$s_query = " where 1 ";
	$s_query .= " and LEFT(sc_date , 4) = '". $pass_sdate ."' ";


	// ---- 총방문수, 접속기기, 회원구분 요약 ----
	$que = "
		SELECT 
			'----------- 접속기기 -----------',
			SUM(IF( sc_mobile  = 'Y' , 1 , 0 )) as sum_mobileY_cnt,
			SUM(IF( sc_mobile  = 'N' , 1 , 0 )) as sum_mobileN_cnt,
			'----------- 회원구분 -----------',
			SUM(IF( sc_memtype  = 'N' , 1 , 0 )) as sum_memtypeN_cnt,
			SUM(IF( sc_memtype  = 'Y' , 1 , 0 )) as sum_memtypeY_cnt,
			'----------- 총방문수 -----------',
			COUNT(*) as sum_cnt

		FROM smart_cntlog_list 
			" . $s_query . "
	";
	$res = _MQ_assoc($que);
	foreach( $res as $k=>$v ){
		foreach( $v as $sk=>$sv ){
			$arr_sum[$sk] = $sv;
		}
	}
	// ---- 총방문수, 접속기기, 회원구분 요약 ----



	// ---- 총방문수, 접속기기, 회원구분 - 날짜별 목록  ----
	$arr_data = array();
	$arr_tot_cumul = array(); // 누적정보 저장
	$res_date = _MQ_assoc("
		SELECT 
			LEFT(sc_date,7) as rdate ,
			'----------- 접속기기 -----------',
			SUM(IF( sc_mobile  = 'Y' , 1 , 0 )) as sum_mobileY_cnt,
			SUM(IF( sc_mobile  = 'N' , 1 , 0 )) as sum_mobileN_cnt,
			'----------- 회원구분 -----------',
			SUM(IF( sc_memtype  = 'N' , 1 , 0 )) as sum_memtypeN_cnt,
			SUM(IF( sc_memtype  = 'Y' , 1 , 0 )) as sum_memtypeY_cnt,
			'----------- 총방문수 -----------',
			COUNT(*) as sum_cnt
		FROM smart_cntlog_list
			" . $s_query . "
		GROUP BY rdate
		ORDER BY rdate ASC
	");
	foreach($res_date as $k=>$v){
		// 접속기기
		$arr_data['sum_mobileY_cnt'][$v['rdate']] = $v['sum_mobileY_cnt'];
		$arr_data['sum_mobileN_cnt'][$v['rdate']] = $v['sum_mobileN_cnt'];
		// 회원구분
		$arr_data['sum_memtypeN_cnt'][$v['rdate']] = $v['sum_memtypeN_cnt'];
		$arr_data['sum_memtypeY_cnt'][$v['rdate']] = $v['sum_memtypeY_cnt'];
		// 총합 부분
		$arr_data['sum_cnt'][$v['rdate']] = $v['sum_cnt'];

	}
	// ---- 총방문수, 접속기기, 회원구분 - 날짜별 목록  ----


	# Chart 그래프 적용
	$arr_date_num = array(); // 방문수
	$arr_date_date = array(); // 접속자일
	$arr_date_color = array(); // 그래프 색
	$arr_date_border = array(); // 그래프 border 색

	$arr_tot_date_num = array(); // 기간내 방문수
	$arr_tot_date_date = array(); // 기간내 날짜
	$arr_tot_date_color = array(); // 기간내 그래프 색
	$arr_tot_date_border = array(); // 기간내 그래프 border 색

	for($i=1 ; $i<=12 ; $i++){

		$arr_date_date[$i] = $i . "월";// 접속자월
		$app_key = $pass_sdate ."-". sprintf("%02d" , $i);


		// 미래 데이터가 보이지 않게 함.
		if($app_key > date("Y-m")){ continue; }


		// --- 접속기기 - 수치배열 ---
		$arr_date_num['sum_mobileY_cnt'][$i] = $arr_data['sum_mobileY_cnt'][$app_key] * 1;// 선택일자 모바일 건수
		$arr_date_num['sum_mobileN_cnt'][$i] = $arr_data['sum_mobileN_cnt'][$app_key] * 1;// 선택일자 PC 건수
		// --- 회원구분 - 수치배열 ---
		$arr_date_num['sum_memtypeN_cnt'][$i] = $arr_data['sum_memtypeN_cnt'][$app_key] * 1;// 선택일자 비회원 건수
		$arr_date_num['sum_memtypeY_cnt'][$i] = $arr_data['sum_memtypeY_cnt'][$app_key] * 1;// 선택일자 회원 건수

		// --- 접속기기 - 그래프 ---
		$arr_date_color['sum_mobileY_cnt'][$i] = "rgba(0, 128, 0, 0.2)"; $arr_date_border['sum_mobileY_cnt'][$i] = "rgba(0, 128, 0, 1)"; // 선택일자 모바일 건수 - 그래프 색 , 그래프 border 색
		$arr_date_color['sum_mobileN_cnt'][$i] = "rgba(0, 128, 255, 0.2)"; $arr_date_border['sum_mobileN_cnt'][$i] = "rgba(0, 128, 255, 1)"; // 선택일자 PC 건수 - 그래프 색 , 그래프 border 색
		// --- 회원구분 - 그래프 ---
		$arr_date_color['sum_memtypeN_cnt'][$i] = "rgba(0, 128, 0, 0.2)";  $arr_date_border['sum_memtypeN_cnt'][$i] = "rgba(0, 128, 0, 1)"; // 선택일자 비회원 건수 - 그래프 색 , 그래프 border 색
		$arr_date_color['sum_memtypeY_cnt'][$i] = "rgba(0, 128, 255, 0.2)"; $arr_date_border['sum_memtypeY_cnt'][$i] = "rgba(0, 128, 255, 1)"; // 선택일자 회원 건수 - 그래프 색 , 그래프 border 색

		// ------------------------ 기간내 총 건수/금액 ------------------------
		$arr_tot_date_date[$i] = $i . "월";// 접속자일
		$arr_tot_date_color[$i] = "rgba(54, 162, 235, 0.2)";  $arr_tot_date_border[$i] = "rgba(54, 162, 235, 1)"; // 그래프 색 , 그래프 border 색
		$arr_tot_cnt_date_num[$i] = $arr_data['sum_cnt'][$app_key] * 1;// 선택일자 총방문수
		// ------------------------ 기간내 총 건수/금액 ------------------------

	}

?>


				<!-- 기간검색 -->
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
				<input type="hidden" name="pass_menu" value="<?php echo $pass_menu; ?>">
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
										<span style="float:left;">
											<input type="text" name="pass_sdate" class='design js_pic_year_max_today' value="<?=$pass_sdate?>" readonly style="width:60px;">
										</span>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="c_btnbox">
							<ul>
								<li><span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"></span></li>
								<?php if($_mode == 'search'){ ?>
									<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?pass_menu=<?=$pass_menu?>" class="c_btn h34 black line normal" accesskey="l">초기화</a></li>
								<?php } ?>
							</ul>
						</div>
					</div>

				</form>
				<!-- / 기간검색 -->



				<!-- 리스트영역 -->
				<div class="group_title"><strong><?=date("Y" , strtotime($pass_sdate))?>년 방문자분석 요약</strong></div>
				<div class="data_list">
					<table class="table_list">
						<colgroup>
							<col width="20%"/><col width="20%"/><col width="20%"/><col width="20%"/><col width="20%"/>
						</colgroup>
						<thead>
							<tr>
								<th scope="col" class="colorset" rowspan="2">총 방문수</th>
								<th scope="col" class="colorset" colspan="2">접속기기 구분</th>
								<th scope="col" class="colorset" colspan="2">회원구분</th>
							</tr>
							<tr>
								<th scope="col" class="colorset" >PC</th>
								<th scope="col" class="colorset" >모바일</th>
								<th scope="col" class="colorset" >회원</th>
								<th scope="col" class="colorset" >비회원</th>
							</tr>
						</thead> 
						<tbody> 
							<tr>

								<!-- 총 주문 합계 -->
								<td ><span class="t_orange bold"><?=number_format($arr_sum['sum_cnt'])?></span>건</td>

								<!-- 접속기기 구분 -->
								<td >
									<span class="t_orange"><?=number_format($arr_sum['sum_mobileN_cnt'])?></span>건
									<span class="grid_sky">(<?=number_format($arr_sum['sum_mobileN_cnt'] * 100 /($arr_sum['sum_cnt'] > 0 ? $arr_sum['sum_cnt'] : 1),2)?>%)</span>
								</td>
								<td >
									<span class="t_orange"><?=number_format($arr_sum['sum_mobileY_cnt'])?></span>건
									<span class="grid_sky">(<?=number_format($arr_sum['sum_mobileY_cnt'] * 100 /($arr_sum['sum_cnt'] > 0 ? $arr_sum['sum_cnt'] : 1),2)?>%)</span>
								</td>

								<!-- 회원구분 -->
								<td >
									<span class="t_orange"><?=number_format($arr_sum['sum_memtypeY_cnt'])?></span>건
									<span class="grid_sky">(<?=number_format($arr_sum['sum_memtypeY_cnt'] * 100 /($arr_sum['sum_cnt'] > 0 ? $arr_sum['sum_cnt'] : 1),2)?>%)</span>
								</td>
								<td >
									<span class="t_orange"><?=number_format($arr_sum['sum_memtypeN_cnt'])?></span>건
									<span class="grid_sky">(<?=number_format($arr_sum['sum_memtypeN_cnt'] * 100 /($arr_sum['sum_cnt'] > 0 ? $arr_sum['sum_cnt'] : 1),2)?>%)</span>
								</td>

							</tr>

						</tbody> 
					</table>



					<table class="table_list" style="margin-top:50px;">
						<colgroup>
							<col width="50%"/><col width="50%"/>
						</colgroup>
						<thead>
							<tr>
								<th scope="col" class="colorset">접속기기별 방문수 그래프</th>
								<th scope="col" class="colorset">회원구분별 방문수 그래프</th>
							</tr>
						</thead> 
						<tbody> 
							<tr>
								<td ><canvas id="js_couter_chart1"></canvas><!-- 접속기기별 --></td>
								<td ><canvas id="js_couter_chart2"></canvas><!-- 회원구분별 --></td>
							</tr>
						</tbody> 
					</table>


			</div>




			<!-- 리스트영역 -->
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






<form name="frmSearch" method="post" action="_cntlog.pro.php" >
	<input type="hidden" name="_mode" value="">	
	<input type="hidden" name="pass_sdate" value="<?php echo $pass_sdate; ?>">
</form>



<script src="./js/chart.js/Chart.bundle.min.js"></script>
<script>
	// --- 검색 엑셀 ---
	function searchExcel() {
		$('form[name="frmSearch"]').children('input[name="_mode"]').val('cntlog_month_search');
		$('form[name="frmSearch"]').attr('action', '_cntlog.pro.php');
		$('form[name="frmSearch"]')[0].submit();
	}
	// --- 검색 엑셀 ---



	// ---------- 기본 line-bar 그래프 (접속기기별 방문수) ----------
	var background = 'rgba(255,99,132,1)';
	var chartData1 = {
		labels: ["<?=implode('", "' , array_values($arr_tot_date_date))?>"],
		datasets: [
		// 총 방문수
		{
			type: 'line',
			label: '총방문수',
			data: [<?=implode(' , ' , array_values($arr_tot_cnt_date_num))?>],
            borderColor : background,
            pointBorderColor : ["<?=implode('", "' , array_values($arr_tot_date_border))?>"],
            pointBackgroundColor : ["<?=implode('", "' , array_values($arr_tot_date_color))?>"],
            pointBorderWidth : 1,
			borderWidth: 1,
			fill:false
		},
		// 접속기기  - PC 수
		{
			type: 'bar',
			label: 'PC',
			data: [<?=implode(' , ' , array_values($arr_date_num['sum_mobileN_cnt']))?>],
            borderColor : ["<?=implode('", "' , array_values($arr_date_border['sum_mobileN_cnt']))?>"],
			backgroundColor: ["<?=implode('", "' , array_values($arr_date_color['sum_mobileN_cnt']))?>"],
			borderWidth: 1,
			fill:false
		},
		// 접속기기  - MOBILE 수
		{
			type: 'bar',
			label: 'MOBILE',
			data: [<?=implode(' , ' , array_values($arr_date_num['sum_mobileY_cnt']))?>],
            borderColor : ["<?=implode('", "' , array_values($arr_date_border['sum_mobileY_cnt']))?>"],
			backgroundColor: ["<?=implode('", "' , array_values($arr_date_color['sum_mobileY_cnt']))?>"],
			borderWidth: 1,
			fill:false
		}]
	};
	// ---------- 기본 line-bar 그래프 (접속기기별 방문수) ----------

	// ---------- 기본 line-bar 그래프 (회원구분별 방문수) ----------
	var background = 'rgba(255,99,132,1)';
	var chartData2 = {
		labels: ["<?=implode('", "' , array_values($arr_tot_date_date))?>"],
		datasets: [
		// 총 방문수
		{
			type: 'line',
			label: '총방문수',
			data: [<?=implode(' , ' , array_values($arr_tot_cnt_date_num))?>],
            borderColor : background,
            pointBorderColor : ["<?=implode('", "' , array_values($arr_tot_date_border))?>"],
            pointBackgroundColor : ["<?=implode('", "' , array_values($arr_tot_date_color))?>"],
            pointBorderWidth : 1,
			borderWidth: 1,
			fill:false
		},
		// 회원구분  - 회원 방문수
		{
			type: 'bar',
			label: '회원',
			data: [<?=implode(' , ' , array_values($arr_date_num['sum_memtypeY_cnt']))?>],
			borderColor : ["<?=implode('", "' , array_values($arr_date_border['sum_memtypeY_cnt']))?>"],
			backgroundColor: ["<?=implode('", "' , array_values($arr_date_color['sum_memtypeY_cnt']))?>"],
			borderWidth: 1,
			fill:false
		},
		// 회원구분  - 비회원 방문수
		{
			type: 'bar',
			label: '비회원',
			data: [<?=implode(' , ' , array_values($arr_date_num['sum_memtypeN_cnt']))?>],
			borderColor : ["<?=implode('", "' , array_values($arr_date_border['sum_memtypeN_cnt']))?>"],
			backgroundColor: ["<?=implode('", "' , array_values($arr_date_color['sum_memtypeN_cnt']))?>"],
			borderWidth: 1,
			fill:false
		}]
	};
	// ---------- 기본 line-bar 그래프 (회원구분별 방문수) ----------

	window.onload = function() {

		// ---------- 기본 line-bar 그래프 (접속기기별 방문수) ----------
		var ctx1 = document.getElementById("js_couter_chart1").getContext("2d");
		var myChart1 = new Chart(ctx1, { type: 'bar', data: chartData1, options: {scales: {yAxes: [{ticks: {beginAtZero:false}}]}} });

		// ---------- 기본 line-bar 그래프 (회원구분별 방문수) ----------
		var ctx2 = document.getElementById("js_couter_chart2").getContext("2d");
		var myChart2 = new Chart(ctx2, { type: 'bar', data: chartData2, options: {scales: {yAxes: [{ticks: {beginAtZero:false}}]}} });

	};


</script>












<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>
<?php
	// ------- 표 - 테이블 데이터 -------

	// grid cell에 클래스 적용
	$arr_class_data = array();
	$arr_class_data['rdate'] = array('grid'); // 날짜 영역 grid_no 클래스 적용 
	// 각 항목에 따른 비율 클래스 적용
	$arr_class_data['device_ratio_pc'] =  array('grid_sky');
	$arr_class_data['device_ratio_mo'] =  array('grid_sky');
	$arr_class_data['mem_ratio_Y'] =  array('grid_sky');
	$arr_class_data['mem_ratio_N'] =  array('grid_sky');

	$arr_table_data = array();

	foreach( $res_date as $datek => $datev ){

		$app_device_cnt_all = ($datev['sum_mobileN_cnt'] + $datev['sum_mobileY_cnt']) > 0 ? ($datev['sum_mobileN_cnt'] + $datev['sum_mobileY_cnt']) : 1;
		$app_mem_cnt_all = ($datev['sum_memtypeY_cnt'] + $datev['sum_memtypeN_cnt']) > 0 ? ($datev['sum_memtypeY_cnt'] + $datev['sum_memtypeN_cnt']) : 1;

		$ex = explode('-' , $datev['rdate']);

		// ----- 소계 -----
		$arr_table_data[] = array(
				'_extraData' => array(
					'className' =>array(
						'column' => $arr_class_data
					)
				),

				'rdate' => $ex[0] .'년 '. $ex[1] .'월' ,


				// 총 방문수
				'total_cnt' => number_format($datev['sum_cnt']),

				// 접속기기 구분
					// 총건수
				'device_cnt_all' => number_format($datev['sum_mobileN_cnt'] + $datev['sum_mobileY_cnt']),
					// PC
				'device_cnt_pc' => number_format($datev['sum_mobileN_cnt']),
				'device_ratio_pc' => number_format($datev['sum_mobileN_cnt'] * 100 / $app_device_cnt_all , 2) . '%',
					// MO
				'device_cnt_mo' => number_format($datev['sum_mobileY_cnt']),
				'device_ratio_mo' => number_format($datev['sum_mobileY_cnt'] * 100 / $app_device_cnt_all , 2) . '%',

					// 총건수
				'mem_cnt_all' => number_format($datev['sum_memtypeY_cnt'] + $datev['sum_memtypeN_cnt']),
					// 회원
				'mem_cnt_Y' => number_format($datev['sum_memtypeY_cnt']),
				'mem_ratio_Y' => number_format($datev['sum_memtypeY_cnt'] * 100 / $app_mem_cnt_all , 2) . '%',
					// 비회원
				'mem_cnt_N' => number_format($datev['sum_memtypeN_cnt']),
				'mem_ratio_N' => number_format($datev['sum_memtypeN_cnt'] * 100 / $app_mem_cnt_all , 2) . '%'

		);

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

        columnFixCount: 1,
        headerHeight: 99,
		rowHeight  : 35,
        displayRowCount: 12,
        minimumColumnwidth : 50,
        autoNumbering: false,

        columnMerge : [

			// 접속기기 구분
			{"title" : "PC", "columnName" : "device_pc", "columnNameList" : ["device_cnt_pc", "device_ratio_pc"] },
			{"title" : "MOBILE", "columnName" : "device_mo", "columnNameList" : ["device_cnt_mo", "device_ratio_mo"] },

			// 회원구분
			{"title" : "회원", "columnName" : "mem_Y", "columnNameList" : ["mem_cnt_Y", "mem_ratio_Y"] },
			{"title" : "비회원", "columnName" : "mem_N", "columnNameList" : ["mem_cnt_N", "mem_ratio_N"] },

			// header
			{"title" : "<b>접속기기 구분</b>", "columnName" : "all_order_cnt_cols", "columnNameList" : ["device_pc", "device_mo"] },
			{"title" : "<b>회원구분</b>", "columnName" : "all_buy_cnt_cols", "columnNameList" : ["mem_Y", "mem_N"] }

        ],

        columnModelList: [
            {"title" : "<b>년월</b>", "columnName" : "rdate", "align" : "center", "width" : 90 },
			{"title" : "<b>총 방문수</b>", "columnName" : "total_cnt", "align" : "center", "width" : 70 },

			// 접속기기 구분
				// PC
				{"title" : "건수", "columnName" : "device_cnt_pc", "align" : "right", "width" : 70 },
				{"title" : "비율", "columnName" : "device_ratio_pc", "align" : "right", "width" : 70 },
				// 모바일
				{"title" : "건수", "columnName" : "device_cnt_mo", "align" : "right", "width" : 70 },
				{"title" : "비율", "columnName" : "device_ratio_mo", "align" : "right", "width" : 70 },

			// 회원구분
				// 회원
				{"title" : "건수", "columnName" : "mem_cnt_Y", "align" : "right", "width" : 70 },
				{"title" : "비율", "columnName" : "mem_ratio_Y", "align" : "right", "width" : 70 },
				// 비회원
				{"title" : "건수", "columnName" : "mem_cnt_N", "align" : "right", "width" : 70 },
				{"title" : "비율", "columnName" : "mem_ratio_N", "align" : "right", "width" : 70 }

        ]
    });

	var table_data = <?=json_encode($arr_table_data)?>;
	grid.setRowList(table_data);

</script>
<?// ---------------------------------------- 표 테이블  ---------------------------------------- ?>