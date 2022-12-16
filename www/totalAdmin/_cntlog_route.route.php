<?php

	// - 넘길 변수 설정하기 ---
	$_PVS = ""; $ARR_PVS = array(); // 링크 넘김 변수		
	foreach(array_filter($_GET) as $key => $val) { $ARR_PVS[$key] = $val; } // GET먼저 중복걸러내기
	foreach(array_filter($_POST) as $key => $val) { $ARR_PVS[$key] = $val; } // POST나중 중복걸러내기
	foreach( $ARR_PVS as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// - 넘길 변수 설정하기 ---



	// 일자계산 
	$pass_sdate = $pass_sdate ? $pass_sdate : date('Y-m-d' , strtotime("-1 week"));
	$pass_edate = $pass_edate ? $pass_edate : date('Y-m-d');


	$arr_sum = array();

	$s_query = " where 1 ";
	$s_query .= " and scr_date between '". $pass_sdate ."' and '". $pass_edate ."' ";


	// ---- 총방문수, 접속기기 요약 ----
	$que = "
		SELECT 
			'----------- 접속기기 -----------',
			SUM( scr_cnt_mo ) as sum_mobileY_cnt,
			SUM( scr_cnt_pc ) as sum_mobileN_cnt,
			'----------- 총방문수 -----------',
			SUM( scr_cnt_pc + scr_cnt_mo ) as sum_cnt
		FROM smart_cntlog_route 
			" . $s_query . "
	";
	$res = _MQ($que);
	foreach( $res as $k=>$v ){
		$arr_sum[$k] = $v;
	}
	// ---- 총방문수, 접속기기 요약 ----


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
											<input type="text" name="pass_sdate" class='design js_pic_day_max_today' value="<?=$pass_sdate?>" readonly style="width:90px;">
											<span class="fr_tx">~</span>
											<input type="text" name="pass_edate" class='design js_pic_day_max_today' value="<?=$pass_edate?>" readonly style="width:90px;">
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
				<div class="group_title"><strong><?=date("Y년 m월 d일" , strtotime($pass_sdate))?> ~ <?=date("Y년 m월 d일" , strtotime($pass_edate))?> 방문자 경로분석 요약</strong></div>
				<div class="data_list">
					<table class="table_list">
						<colgroup>
							<col width="33%"/><col width="33%"/><col width="33%"/>
						</colgroup>
						<thead>
							<tr>
								<th scope="col" class="colorset" rowspan="2">총 방문수</th>
								<th scope="col" class="colorset" colspan="2">접속기기 구분</th>
							</tr>
							<tr>
								<th scope="col" class="colorset" >PC</th>
								<th scope="col" class="colorset" >모바일</th>
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

				<table class="table_list if_counter_table">
					<colgroup>
						<col width="60"/>
						<col width="100"/><col width="120"/><col width="120"/><col width="*"/>
						<col width="100"/>
					</colgroup>
					<thead>
						<tr>
							<th scope="col" class="colorset" >순위</th>
							<th scope="col" class="colorset" >방문수</th>
							<th scope="col" class="colorset" >PC방문수</th>
							<th scope="col" class="colorset" >MOBILE방문수</th>
							<th scope="col" class="colorset" >접속경로</th>
							<th scope="col" class="colorset" >비율</th>
						</tr>
					</thead>
					<tbody> 

						<?php

							$listmaxcount = 50 ;
							if( !$listpg ) {$listpg = 1 ;}
							$count = $listpg * $listmaxcount - $listmaxcount;

							// ------- 순위별 목록 -------
							$que = "
								SELECT 
									COUNT(DISTINCT(scr_route)) as cnt
								FROM smart_cntlog_route 
									" . $s_query . "
							";
							$res = _MQ($que);

							$TotalCount = $res['cnt'];
							$Page = ceil($TotalCount / $listmaxcount);


							//------------------ sort 기능 ------------------
							$order_field = $order_field ? $order_field : 'sum_cnt';
							$order_sort = $order_sort ? $order_sort : '1';
							switch($order_field){
								case "sum_cnt": case "sum_ratio": default: $que_order = 'sum_cnt '. ($order_sort == '2' ? 'ASC' : 'DESC') . ' , scr_route ASC'; break;
								case "pc_sum_cnt": $que_order = 'sum_mobileN_cnt '. ($order_sort == '2' ? 'ASC' : 'DESC') .', sum_cnt DESC' ; break;
								case "mo_sum_cnt": $que_order = 'sum_mobileY_cnt '. ($order_sort == '2' ? 'ASC' : 'DESC') .', sum_cnt DESC' ; break;
								case "route": $que_order = 'scr_route '. ($order_sort == '2' ? 'DESC' : 'ASC') .', sum_cnt DESC' ; break;
							}
							//------------------ sort 기능 ------------------


							// ------- 순위별 목록 -------
							$que = "
								SELECT 
									scr_route , 
									'----------- 접속기기 -----------',
									SUM( scr_cnt_mo ) as sum_mobileY_cnt,
									SUM( scr_cnt_pc ) as sum_mobileN_cnt,
									'----------- 총방문수 -----------',
									SUM( scr_cnt_pc + scr_cnt_mo ) as sum_cnt

								FROM smart_cntlog_route 
									" . $s_query . "
								GROUP BY scr_route
								ORDER BY ". $que_order ."
								limit " . $count . " , " . $listmaxcount . "
							";
							$res = _MQ_assoc($que);
							foreach( $res as $datek => $datev ){

								$_num = $count + $datek + 1;// 순위

								$sum_cnt = $arr_sum['sum_cnt'] > 0 ? $arr_sum['sum_cnt'] : 1;
								$_ratio =  number_format( 100 * $datev['sum_cnt'] / $sum_cnt , 2); // 비율

								echo '
									<tr>
										<td>'. $_num .'</td><!-- 순위 -->
										<!-- 총 방문수 -->
										<td ><span class="bold">' . number_format($datev['sum_cnt']) . '</span></td>
										<!-- 접속기기 구분 -->
										<td class="">' . number_format($datev['sum_mobileN_cnt']) . ' <span class="grid_sky">(' . number_format($datev['sum_mobileN_cnt'] * 100 /($datev['sum_cnt'] > 0 ? $datev['sum_cnt'] : 1),2) . '%)</span></td>
										<td class="">' . number_format($datev['sum_mobileY_cnt']) . ' <span class="grid_sky">(' . number_format($datev['sum_mobileY_cnt'] * 100 /($datev['sum_cnt'] > 0 ? $datev['sum_cnt'] : 1),2) . '%)</span></td>
										<td class="t_left">' . ($datev['scr_route'] ? $datev['scr_route'] : '<span class="grid_sky">즐겨찾기 OR URL 직접입력을 통한 접속</span>') . '</td><!-- 접속경로 -->
										<td ><span class="bold">' . $_ratio . '%</span></td><!-- 비율 -->
									</tr>
								';
							}
							// ------- 순위별 목록 -------
						?>

					</tbody> 
				</table>


				<div class="data_summery" style="border-top:0px">
					<?=_DescStr("<strong>즐겨찾기 OR URL 직접입력을 통한 접속</strong>은 다른 사이트를 통하지 않고 직접 접속한 경우를 의미합니다.")?>
				</div>


				<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
				<div class="paginate">
					<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
				</div>


			</div>






<form name="frmSearch" method="post" action="_cntlog.pro.php" >
	<input type="hidden" name="_mode" value="">	
	<input type="hidden" name="pass_sdate" value="<?php echo $pass_sdate; ?>">
	<input type="hidden" name="pass_edate" value="<?php echo $pass_edate; ?>">
</form>



<script>
	// --- 검색 엑셀 ---
	function searchExcel() {
		$('form[name="frmSearch"]').children('input[name="_mode"]').val('cntlog_route_search');
		$('form[name="frmSearch"]').attr('action', '_cntlog.pro.php');
		$('form[name="frmSearch"]')[0].submit();
	}
	// --- 검색 엑셀 ---
</script>