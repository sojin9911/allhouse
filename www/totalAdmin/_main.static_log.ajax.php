<?php 

	define('_OD_DIRECT_', true); // 개별 실행방지

	// 선택 탭 - content - ajax 적용
	include_once('inc.php'); 

?>


		<div class="inner_box graph_box">
			<div class="inner">
				<!-- 그래프 들어감 / 100% * 302px / 그래프 넣고 이미지는 삭제해 주세요. -->
				<canvas id="js_couter_chart1" style="width:425px; height:302px;"></canvas>
			</div>
		</div>


<?php 

	// type 기본값 지정
	//			sale : 매출
	//			order : 주문
	//			product : 상품 -- 제외
	//			member : 회원
	//			cntlog : 방문자
	$type = $type ? $type : 'sale';

	// type별 데이터 추출 및 테이블 정보 적용
	//				- 그래프에 해당하는 정보 return 해야 함.
	//				- return할 배열 뱐수 : $arr_tot_date_date , $arr_tot_date_num , $arr_tot_date_border , $arr_tot_date_color
	$file_name = '_main.static_log.ajax.'. $type  .'.php';
	if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/totalAdmin/' . $file_name)) {
		include_once($file_name); 
	}

?>



<script src="./js/chart.js/Chart.bundle.min.js"></script>
<script>
	// ---------- 기본 line-bar 그래프 ----------
	var background = 'rgba(54, 162, 235,1)';
	var chartData = {
		labels: ["<?=implode('", "' , array_values($arr_tot_date_date))?>"],
		datasets: [
		// 전체매출
		{
			type: 'line',
			label: '통계',
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
			options: {
				maintainAspectRatio: false,
				legend: {display: false},
				scales: {
					yAxes: [{
						ticks: {beginAtZero:false},
						afterFit: function(scaleInstance) {
							//scaleInstance.width = 70;
							var MaxLength = 0;
							$.each(scaleInstance.ticks, function(k, v) {
								if(MaxLength < v.length) MaxLength = v.length;
							});
							scaleInstance.width = MaxLength*10;
						}
					}]
				}
			}
		});
	};
</script>