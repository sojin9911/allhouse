<?php 

	// 관리자 - 메인 > 쇼핑몰 주요 현황 > 매출 부분

	define('_OD_DIRECT_', true); // 개별 실행방지


?>
		<div class="inner_box table_box">
			<div class="inner">
				<a href="_static_sale.all.php" class="more_btn">전체 매출현황 보기</a>
				<table class="all_view">
					<thead>
						<tr>
							<th scope="col">날짜</th>
							<th scope="col">구매총액</th>
							<th scope="col">실결제액</th>
							<th scope="col">취소/환불</th>
						</tr>
					</thead>
					<tbody>
					<?php
						// ------- 매출 - 날짜별 목록 -------
						$arr_data = $arr_cumul = array();

						// JJC : 주문 취소항목 추출 : 2018-01-04
						$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

						$que = "
								SELECT 
									DATE(o_rdate) as rdate,
									SUM( IF( npay_order = 'Y' ,  ( o_price_real - o_price_delivery ) , o_price_total) ) as sum_price_total,
									SUM( o_price_real ) as sum_price_real,
									SUM( IF( o_canceled =  'Y', o_price_real, ". $add_que_cancel ." ) ) as sum_price_refund
								FROM smart_order
								WHERE
									o_paystatus = 'Y' AND
									DATE_ADD(DATE(o_rdate) , INTERVAL + 7 DAY) > CURDATE()
							GROUP BY rdate 
							ORDER BY rdate ASC
						";
						$res = _MQ_assoc($que);
						foreach( $res as $k=>$v ){
							foreach( $v as $sk=>$sv ){
								$arr_data[$v['rdate']][$sk] = $sv;
							}
							$arr_cumul['sum_price_total'] += $v['sum_price_total'];//구매총액
							$arr_cumul['sum_price_real'] += $v['sum_price_real'];//실결제액
							$arr_cumul['sum_price_refund'] += $v['sum_price_refund'];//취소/환불
						}


						// 그래프 적용 데이터
						$arr_tot_date_num = array(); // 전체 매출
						$arr_tot_date_date = array(); // 매출 날짜
						$arr_tot_date_color = array(); // 매출 그래프 색
						$arr_tot_date_border = array(); // 그래프 border 색

						// 7일 지정
						for($i=0 ; $i<7 ; $i++){
							$rdate = DATE("Y-m-d" , strtotime(" - ". (6 - $i) ." DAY")); // 날짜 지정
							$v = $arr_data[$rdate]; // 날짜별 배열 지정
							echo '
								<tr class="'. ( $rdate == DATE("Y-m-d") ? 'today' : '') .'">
									<td>'. DATE("m-d" , strtotime($rdate)) .'</td>
									<td>'. number_format($v['sum_price_total']) .'</td>
									<td>'. number_format($v['sum_price_real']) .'</td>
									<td>'. number_format($v['sum_price_refund']) .'</td>
								</tr>
							';

							// ------------------------ 그래프 적용 데이터 ------------------------
							$arr_tot_date_num[$i] = $v['sum_price_real']*1;// 실결제액
							$arr_tot_date_date[$i] = DATE("m/d" , strtotime($rdate));// 주문일자
							$arr_tot_date_color[$i] = "rgba(54, 162, 235, 0.2)"; // 그래프 색
							$arr_tot_date_border[$i] = "rgba(54, 162, 235, 1)"; // 그래프 border 색
							// ------------------------ 그래프 적용 데이터 ------------------------

						}

						echo '
							<tr class="total">
								<td>1주일 합계</td>
								<td>' . number_format($arr_cumul['sum_price_total']) . '</td>
								<td>' . number_format($arr_cumul['sum_price_real']) . '</td>
								<td>' . number_format($arr_cumul['sum_price_refund']) . '</td>
							</tr>
						';


						// 1개월 합계
						$que = "
								SELECT 
									SUM( IF( npay_order = 'Y' ,  ( o_price_real - o_price_delivery ) , o_price_total) ) as sum_price_total,
									SUM( o_price_real ) as sum_price_real,
									SUM( IF( o_canceled =  'Y', o_price_real, ". $add_que_cancel ." ) ) as sum_price_refund
								FROM smart_order
								WHERE
									o_paystatus = 'Y' AND
									DATE_ADD(DATE(o_rdate) , INTERVAL + 1 MONTH) >= CURDATE()
						";
						$row_month = _MQ($que);
						echo '
							<tr class="total">
								<td>1개월 합계</td>
								<td>' . number_format($row_month['sum_price_total']) . '</td>
								<td>' . number_format($row_month['sum_price_real']) . '</td>
								<td>' . number_format($row_month['sum_price_refund']) . '</td>
							</tr>
						';

					?>
					</tbody>
				</table>
			</div>
		</div>