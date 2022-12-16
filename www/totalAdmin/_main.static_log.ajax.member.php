<?php 

	// 관리자 - 메인 > 쇼핑몰 주요 현황 > 회원 부분

	define('_OD_DIRECT_', true); // 개별 실행방지


?>
		<div class="inner_box table_box">
			<div class="inner">
				<a href="_static_mem.type.php" class="more_btn">전체 회원분석 보기</a>
				<table class="all_view">
					<thead>
						<tr>
							<th scope="col">날짜</th>
							<th scope="col">가입 회원수</th>
							<th scope="col">휴면 회원수</th>
							<th scope="col">탈퇴 회원수</th>
						</tr>
					</thead>
					<tbody>
					<?php
						// ------- 회원 - 날짜별 목록 -------
						$arr_data = $arr_cumul = array();

						//  --- 가입회원수 ---
						$que = "
							SELECT 
								DATE(in_rdate) as rdate,
								COUNT(*) as total_mem
							FROM smart_individual 
							WHERE
								DATE_ADD(DATE(in_rdate) , INTERVAL + 7 DAY) > CURDATE()
							GROUP BY rdate 
							ORDER BY rdate ASC
						";
						$res = _MQ_assoc($que);
						foreach( $res as $k=>$v ){
							foreach( $v as $sk=>$sv ){
								$arr_data[$v['rdate']][$sk] = $sv;
							}
							$arr_cumul['total_mem'] += $v['total_mem'];//가입회원수
						}

						//  --- 휴면회원수 ---
						$que = "
							SELECT 
								DATE(ins.ins_rdate) as rdate,
								COUNT(*) as total_sleep
							FROM smart_individual_sleep as ins  
							INNER JOIN smart_individual as ind ON (ind.in_id = ins.in_id and ind.in_sleep_type = 'Y')
							WHERE
								DATE_ADD(DATE(ins.ins_rdate) , INTERVAL + 7 DAY) > CURDATE()
							GROUP BY rdate 
							ORDER BY rdate ASC
						";
						$res = _MQ_assoc($que);
						foreach( $res as $k=>$v ){
							foreach( $v as $sk=>$sv ){
								$arr_data[$v['rdate']][$sk] = $sv;
							}
							$arr_cumul['total_sleep'] += $v['total_sleep'];//휴면회원수
						}

						//  --- 탈퇴회원수 ---
						$que = "
							SELECT 
								DATE(in_odate) as rdate,
								COUNT(*) as total_out
							FROM smart_individual 
							WHERE
								in_out = 'Y' and 
								DATE_ADD(DATE(in_odate) , INTERVAL + 7 DAY) > CURDATE()
							GROUP BY rdate 
							ORDER BY rdate ASC
						";
						$res = _MQ_assoc($que);
						foreach( $res as $k=>$v ){
							foreach( $v as $sk=>$sv ){
								$arr_data[$v['rdate']][$sk] = $sv;
							}
							$arr_cumul['total_out'] += $v['total_out'];//탈퇴회원수
						}

						// 그래프 적용 데이터
						$arr_tot_date_num = array(); // 전체
						$arr_tot_date_date = array(); // 날짜
						$arr_tot_date_color = array(); // 그래프 색
						$arr_tot_date_border = array(); // 그래프 border 색

						// 7일 지정
						for($i=0 ; $i<7 ; $i++){
							$rdate = DATE("Y-m-d" , strtotime(" - ". (6 - $i) ." DAY")); // 날짜 지정
							$v = $arr_data[$rdate]; // 날짜별 배열 지정
							echo '
								<tr class="'. ( $rdate == DATE("Y-m-d") ? 'today' : '') .'">
									<td>'. DATE("m-d" , strtotime($rdate)) .'</td>
									<td>'. number_format($v['total_mem']) .'</td>
									<td>'. number_format($v['total_sleep']) .'</td>
									<td>'. number_format($v['total_out']) .'</td>
								</tr>
							';

							// ------------------------ 그래프 적용 데이터 ------------------------
							$arr_tot_date_num[$i] = $v['total_mem']*1;// 가입회원수
							$arr_tot_date_date[$i] = DATE("m/d" , strtotime($rdate));// 주문일자
							$arr_tot_date_color[$i] = "rgba(54, 162, 235, 0.2)"; // 그래프 색
							$arr_tot_date_border[$i] = "rgba(54, 162, 235, 1)"; // 그래프 border 색
							// ------------------------ 그래프 적용 데이터 ------------------------

						}

						echo '
							<tr class="total">
								<td>1주일 합계</td>
								<td>' . number_format($arr_cumul['total_mem']) . '</td>
								<td>' . number_format($arr_cumul['total_sleep']) . '</td>
								<td>' . number_format($arr_cumul['total_out']) . '</td>
							</tr>
						';


						// 
						$que = "
								SELECT 
									COUNT(*) as sum_sale_cnt,
									SUM( op.op_cnt ) as sum_buy_cnt,
									SUM( op.op_price * op.op_cnt ) as sum_buy_price
								FROM smart_order_product as op 
								INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum ) 
								WHERE
									o.o_paystatus = 'Y' AND
									o.o_canceled!='Y' AND 
									op.op_cancel = 'N'  AND 
									DATE_ADD(DATE(o_rdate) , INTERVAL + 1 MONTH) >= CURDATE()
						";
						$row_month = _MQ($que);


						//  --- 1개월 합계 : 가입회원수 ---
						$que = "
							SELECT 
								COUNT(*) as total_mem
							FROM smart_individual 
							WHERE
								DATE_ADD(DATE(in_rdate) , INTERVAL + 1 MONTH) >= CURDATE()
						";
						$row_month_mem = _MQ($que);

						//  --- 휴면회원수 ---
						$que = "
							SELECT 
								COUNT(*) as total_sleep
							FROM smart_individual_sleep as ins  
							INNER JOIN smart_individual as ind ON (ind.in_id = ins.in_id and ind.in_sleep_type = 'Y')
							WHERE
								DATE_ADD(DATE(ins.ins_rdate) , INTERVAL + 1 MONTH) >= CURDATE()
						";
						$row_month_sleep = _MQ($que);

						//  --- 탈퇴회원수 ---
						$que = "
							SELECT 
								COUNT(*) as total_out
							FROM smart_individual 
							WHERE
								in_out = 'Y' and 
								DATE_ADD(DATE(in_odate) , INTERVAL + 1 MONTH) >= CURDATE()
						";
						$row_month_out = _MQ($que);
						
						$row_month = array_merge($row_month_mem , $row_month_sleep , $row_month_out);

						echo '
							<tr class="total">
								<td>1개월 합계</td>
								<td>' . number_format($row_month['total_mem']) . '</td>
								<td>' . number_format($row_month['total_sleep']) . '</td>
								<td>' . number_format($row_month['total_out']) . '</td>
							</tr>
						';

					?>
					</tbody>
				</table>
			</div>
		</div>