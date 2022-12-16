<?
	// 관리자 메인 페이지 적용용
	// ------------- 쇼핑몰 주요 현황 -------------
?>
	<!-- 메인타이틀 -->
	<div class="main_tt">
		<span class="tit">쇼핑몰 주요 현황</span>
		<!-- 주문관리 페이지로 이동 -->
		<!-- <a href="" class="more_btn" title="더보기"></a> -->
	</div>
	<!-- 탭메뉴 -->
	<div class="tab_menu">
		<ul>
			<!-- 활성화시 li에 hit클래스 추가 -->
			<li class="hit"><a href="#none" class="btn log_btn" data-key="sale">매출</a></li>
			<li><a href="#none" class="btn log_btn" data-key="order">주문</a></li>
			<!-- <li><a href="#none" class="btn log_btn" data-key="product">상품</a></li> -->
			<li><a href="#none" class="btn log_btn" data-key="member">회원</a></li>
			<li><a href="#none" class="btn log_btn" data-key="cntlog">방문자</a></li>
		</ul>
	</div>


	<!-- 선택 탭 - content 노출 영역 -->
	<div ID="main_static_log_content">
		<?php
			// 내용부분
			include_once('_main.static_log.ajax.php');
		?>
	</div>

<script type="text/javascript">

	// 탭메뉴 클릭 - 이벤트 처리
	$(document).on("click" , ".tab_menu .log_btn" , function(){

		$(".tab_menu li").removeClass("hit");// 전체 hit 제거
		$(this).closest("li").addClass("hit");// 선택 hit 적용

		// content  정보 가져오기
		var type = $(this).data("key");
		$.ajax({
			data: {type: type,},
			type: 'post',
			cache: false,
			url: '_main.static_log.ajax.php',
			success: function(data) {
				$('#main_static_log_content').html(data);

				// --- 그래프 실행 ---
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
									if(MaxLength <= 2) scaleInstance.width = MaxLength*16;
								}
							}]
						}
					}
				});
				// --- 그래프 실행 ---

			}
		});

	});

</script>

<?
	// ------------- 쇼핑몰 주요 현황 -------------
?>