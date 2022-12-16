<?php


	ini_set('memory_limit', '-1');
	include_once('inc.php');



	switch($_mode){



	// -------------- 전체 주문통계  ----------------------------

		// -------------- 주문통계 - 일자별 --------------
		case "all_day_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_order_all_day_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m', time()));


			echo '
				<table border=1>
					<tr>
						<td rowspan="3">날짜</td>
						<td colspan="9">구매건수</td>
						<td colspan="9">구매수량</td>
						<td colspan="9">구매금액</td>
					</tr>
					<tr>
						<td rowspan="2">총건수</td>
						<td colspan="2">회원</td>
						<td colspan="2">비회원</td>
						<td colspan="2">PC</td>
						<td colspan="2">MOBILE</td>

						<td rowspan="2">총수량</td>
						<td colspan="2">회원</td>
						<td colspan="2">비회원</td>
						<td colspan="2">PC</td>
						<td colspan="2">MOBILE</td>

						<td rowspan="2">총금액</td>
						<td colspan="2">회원</td>
						<td colspan="2">비회원</td>
						<td colspan="2">PC</td>
						<td colspan="2">MOBILE</td>
					</tr>
					<tr>
						<td >건수</td><td >비율</td>
						<td >건수</td><td >비율</td>
						<td >건수</td><td >비율</td>
						<td >건수</td><td >비율</td>

						<td >수량</td><td >비율</td>
						<td >수량</td><td >비율</td>
						<td >수량</td><td >비율</td>
						<td >수량</td><td >비율</td>

						<td >금액</td><td >비율</td>
						<td >금액</td><td >비율</td>
						<td >금액</td><td >비율</td>
						<td >금액</td><td >비율</td>
					</tr>
			';

			$s_query = "
				where
					o.o_canceled!='Y'
					AND o.o_paystatus = 'Y'
					AND op.op_cancel = 'N'
					AND LEFT(o.o_rdate,7) = '". $pass_date ."'
			";

			$que = "
				select

					rdate,

					COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
					COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

					COUNT(DISTINCT(subsum_memtypeN_order_cnt)) as sum_memtypeN_order_cnt,
					COUNT(DISTINCT(subsum_memtypeY_order_cnt)) as sum_memtypeY_order_cnt,

					COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


					SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
					SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

					SUM(sub_sum_memtypeN_buy_cnt) as sum_memtypeN_buy_cnt,
					SUM(sub_sum_memtypeY_buy_cnt) as sum_memtypeY_buy_cnt,

					SUM(sub_sum_buy_cnt) as sum_buy_cnt,

					SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
					SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

					SUM(sub_sum_memtypeN_buy_price) as sum_memtypeN_buy_price,
					SUM(sub_sum_memtypeY_buy_price) as sum_memtypeY_buy_price,

					SUM(sub_sum_buy_price) as sum_buy_price

				from
				(
					SELECT

						DATE(o.o_rdate) as rdate ,
						op.op_oordernum ,

						IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
						IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,

						IF( o.o_memtype = 'N' , op.op_oordernum , NULL ) as subsum_memtypeN_order_cnt,
						IF( o.o_memtype != 'N' , op.op_oordernum , NULL ) as subsum_memtypeY_order_cnt,


						SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
						SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

						SUM(IF( o.o_memtype = 'N' , op.op_cnt , 0 )) as sub_sum_memtypeN_buy_cnt,
						SUM(IF( o.o_memtype != 'N' , op.op_cnt , 0 )) as sub_sum_memtypeY_buy_cnt,

						SUM( op.op_cnt ) as sub_sum_buy_cnt,

						SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
						SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,

						SUM(IF( o.o_memtype = 'N' , op_price * op_cnt , 0 )) as sub_sum_memtypeN_buy_price,
						SUM(IF( o.o_memtype != 'N' ,  op_price * op_cnt , 0 )) as sub_sum_memtypeY_buy_price,

						SUM( op_price * op_cnt ) as sub_sum_buy_price

					FROM smart_order_product as op
					INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )

					" . $s_query . "

					group by op.op_oordernum

				) as tbl_view

				GROUP BY rdate
				ORDER BY
					rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){

				// 구매건수
				$arr_res[$v['rdate']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
				$arr_res[$v['rdate']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
				$arr_res[$v['rdate']]['order_cnt']['memtypeN'] = $v['sum_memtypeN_order_cnt'];
				$arr_res[$v['rdate']]['order_cnt']['memtypeY'] = $v['sum_memtypeY_order_cnt'];
				$arr_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

				// 구매수량
				$arr_res[$v['rdate']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
				$arr_res[$v['rdate']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
				$arr_res[$v['rdate']]['buy_cnt']['memtypeN'] = $v['sum_memtypeN_buy_cnt'];
				$arr_res[$v['rdate']]['buy_cnt']['memtypeY'] = $v['sum_memtypeY_buy_cnt'];
				$arr_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

				// 구매금액
				$arr_res[$v['rdate']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
				$arr_res[$v['rdate']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
				$arr_res[$v['rdate']]['buy_price']['memtypeN'] = $v['sum_memtypeN_buy_price'];
				$arr_res[$v['rdate']]['buy_price']['memtypeY'] = $v['sum_memtypeY_buy_price'];
				$arr_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

			}
			for($i=1 ; $i<=date("t" , strtotime(date("{$Select_Year}-{$Select_Month}-01"))) ; $i++){

				$app_date = $Select_Year ."-". $Select_Month ."-". sprintf("%02d" , $i);
				$app_date_key = $app_date;

				$app_sum_order_cnt = $arr_res[$app_date_key]['order_cnt']['sum'] > 0 ? $arr_res[$app_date_key]['order_cnt']['sum'] : 1;
				$app_sum_buy_cnt = $arr_res[$app_date_key]['buy_cnt']['sum'] > 0 ? $arr_res[$app_date_key]['buy_cnt']['sum'] : 1;
				$app_sum_buy_price = $arr_res[$app_date_key]['buy_price']['sum'] > 0 ? $arr_res[$app_date_key]['buy_price']['sum'] : 1;

				echo '
					<tr>
						<td>'. $app_date .'</td><!-- 날짜 -->

						<!-- 구매건수 -->
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['memtypeY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['memtypeY'] * 100 / $app_sum_order_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['memtypeN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['memtypeN'] * 100 / $app_sum_order_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['mobileN'] * 100 / $app_sum_order_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['mobileY'] * 100 / $app_sum_order_cnt , 2) . '%</td>

						<!-- 구매수량 -->
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['memtypeY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['memtypeY'] * 100 / $app_sum_buy_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['memtypeN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['memtypeN'] * 100 / $app_sum_buy_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['mobileN'] * 100 / $app_sum_buy_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['mobileY'] * 100 / $app_sum_buy_cnt , 2) . '%</td>

						<!-- 구매금액 -->
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['memtypeY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['memtypeY'] * 100 / $app_sum_buy_price , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['memtypeN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['memtypeN'] * 100 / $app_sum_buy_price , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['mobileN'] * 100 / $app_sum_buy_price , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['mobileY'] * 100 / $app_sum_buy_price , 2) . '%</td>
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 주문통계 - 일자별 --------------

		// -------------- 주문통계 - 월별 --------------
		case "all_month_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_order_all_month_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y', time()));


			echo '
				<table border=1>
					<tr>
						<td rowspan="3">년월</td>
						<td colspan="9">구매건수</td>
						<td colspan="9">구매수량</td>
						<td colspan="9">구매금액</td>
					</tr>
					<tr>
						<td rowspan="2">총건수</td>
						<td colspan="2">회원</td>
						<td colspan="2">비회원</td>
						<td colspan="2">PC</td>
						<td colspan="2">MOBILE</td>

						<td rowspan="2">총수량</td>
						<td colspan="2">회원</td>
						<td colspan="2">비회원</td>
						<td colspan="2">PC</td>
						<td colspan="2">MOBILE</td>

						<td rowspan="2">총금액</td>
						<td colspan="2">회원</td>
						<td colspan="2">비회원</td>
						<td colspan="2">PC</td>
						<td colspan="2">MOBILE</td>
					</tr>
					<tr>
						<td >건수</td><td >비율</td>
						<td >건수</td><td >비율</td>
						<td >건수</td><td >비율</td>
						<td >건수</td><td >비율</td>

						<td >수량</td><td >비율</td>
						<td >수량</td><td >비율</td>
						<td >수량</td><td >비율</td>
						<td >수량</td><td >비율</td>

						<td >금액</td><td >비율</td>
						<td >금액</td><td >비율</td>
						<td >금액</td><td >비율</td>
						<td >금액</td><td >비율</td>
					</tr>
			';

			$s_query = "
				where
					o.o_canceled!='Y'
					AND o.o_paystatus = 'Y'
					AND op.op_cancel = 'N'
					AND LEFT(o.o_rdate,4) = '". $pass_date ."'
			";

			$que = "
				select

					rdate,

					COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
					COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

					COUNT(DISTINCT(subsum_memtypeN_order_cnt)) as sum_memtypeN_order_cnt,
					COUNT(DISTINCT(subsum_memtypeY_order_cnt)) as sum_memtypeY_order_cnt,

					COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


					SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
					SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

					SUM(sub_sum_memtypeN_buy_cnt) as sum_memtypeN_buy_cnt,
					SUM(sub_sum_memtypeY_buy_cnt) as sum_memtypeY_buy_cnt,

					SUM(sub_sum_buy_cnt) as sum_buy_cnt,

					SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
					SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

					SUM(sub_sum_memtypeN_buy_price) as sum_memtypeN_buy_price,
					SUM(sub_sum_memtypeY_buy_price) as sum_memtypeY_buy_price,

					SUM(sub_sum_buy_price) as sum_buy_price

				from
				(
					SELECT

						LEFT(o.o_rdate , 7) as rdate ,
						op.op_oordernum ,

						IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
						IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,

						IF( o.o_memtype = 'N' , op.op_oordernum , NULL ) as subsum_memtypeN_order_cnt,
						IF( o.o_memtype != 'N' , op.op_oordernum , NULL ) as subsum_memtypeY_order_cnt,


						SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
						SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

						SUM(IF( o.o_memtype = 'N' , op.op_cnt , 0 )) as sub_sum_memtypeN_buy_cnt,
						SUM(IF( o.o_memtype != 'N' , op.op_cnt , 0 )) as sub_sum_memtypeY_buy_cnt,

						SUM( op.op_cnt ) as sub_sum_buy_cnt,

						SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
						SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,

						SUM(IF( o.o_memtype = 'N' , op_price * op_cnt , 0 )) as sub_sum_memtypeN_buy_price,
						SUM(IF( o.o_memtype != 'N' ,  op_price * op_cnt , 0 )) as sub_sum_memtypeY_buy_price,

						SUM( op_price * op_cnt ) as sub_sum_buy_price

					FROM smart_order_product as op
					INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )

					" . $s_query . "

					group by op.op_oordernum

				) as tbl_view

				GROUP BY rdate
				ORDER BY
					rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){

				// 구매건수
				$arr_res[$v['rdate']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
				$arr_res[$v['rdate']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
				$arr_res[$v['rdate']]['order_cnt']['memtypeN'] = $v['sum_memtypeN_order_cnt'];
				$arr_res[$v['rdate']]['order_cnt']['memtypeY'] = $v['sum_memtypeY_order_cnt'];
				$arr_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

				// 구매수량
				$arr_res[$v['rdate']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
				$arr_res[$v['rdate']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
				$arr_res[$v['rdate']]['buy_cnt']['memtypeN'] = $v['sum_memtypeN_buy_cnt'];
				$arr_res[$v['rdate']]['buy_cnt']['memtypeY'] = $v['sum_memtypeY_buy_cnt'];
				$arr_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

				// 구매금액
				$arr_res[$v['rdate']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
				$arr_res[$v['rdate']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
				$arr_res[$v['rdate']]['buy_price']['memtypeN'] = $v['sum_memtypeN_buy_price'];
				$arr_res[$v['rdate']]['buy_price']['memtypeY'] = $v['sum_memtypeY_buy_price'];
				$arr_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

			}
			for($i=1 ; $i<=12 ; $i++){

				$app_date = $Select_Year ."년 ". sprintf("%02d" , $i)."월";
				$app_date_key = $Select_Year ."-". sprintf("%02d" , $i);

				$app_sum_order_cnt = $arr_res[$app_date_key]['order_cnt']['sum'] > 0 ? $arr_res[$app_date_key]['order_cnt']['sum'] : 1;
				$app_sum_buy_cnt = $arr_res[$app_date_key]['buy_cnt']['sum'] > 0 ? $arr_res[$app_date_key]['buy_cnt']['sum'] : 1;
				$app_sum_buy_price = $arr_res[$app_date_key]['buy_price']['sum'] > 0 ? $arr_res[$app_date_key]['buy_price']['sum'] : 1;

				echo '
					<tr>
						<td>'. $app_date .'</td><!-- 년월 -->

						<!-- 구매건수 -->
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['memtypeY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['memtypeY'] * 100 / $app_sum_order_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['memtypeN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['memtypeN'] * 100 / $app_sum_order_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['mobileN'] * 100 / $app_sum_order_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['mobileY'] * 100 / $app_sum_order_cnt , 2) . '%</td>

						<!-- 구매수량 -->
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['memtypeY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['memtypeY'] * 100 / $app_sum_buy_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['memtypeN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['memtypeN'] * 100 / $app_sum_buy_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['mobileN'] * 100 / $app_sum_buy_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['mobileY'] * 100 / $app_sum_buy_cnt , 2) . '%</td>

						<!-- 구매금액 -->
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['memtypeY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['memtypeY'] * 100 / $app_sum_buy_price , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['memtypeN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['memtypeN'] * 100 / $app_sum_buy_price , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['mobileN'] * 100 / $app_sum_buy_price , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['mobileY'] * 100 / $app_sum_buy_price , 2) . '%</td>
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 주문통계 - 월별 --------------

		// -------------- 주문통계 - 시간별 --------------
		case "all_hour_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_order_all_hour_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));

			echo '
				<table border=1>
					<tr>
						<td rowspan="3">시간</td>
						<td colspan="9">구매건수</td>
						<td colspan="9">구매수량</td>
						<td colspan="9">구매금액</td>
					</tr>
					<tr>
						<td rowspan="2">총건수</td>
						<td colspan="2">회원</td>
						<td colspan="2">비회원</td>
						<td colspan="2">PC</td>
						<td colspan="2">MOBILE</td>

						<td rowspan="2">총수량</td>
						<td colspan="2">회원</td>
						<td colspan="2">비회원</td>
						<td colspan="2">PC</td>
						<td colspan="2">MOBILE</td>

						<td rowspan="2">총금액</td>
						<td colspan="2">회원</td>
						<td colspan="2">비회원</td>
						<td colspan="2">PC</td>
						<td colspan="2">MOBILE</td>
					</tr>
					<tr>
						<td >건수</td><td >비율</td>
						<td >건수</td><td >비율</td>
						<td >건수</td><td >비율</td>
						<td >건수</td><td >비율</td>

						<td >수량</td><td >비율</td>
						<td >수량</td><td >비율</td>
						<td >수량</td><td >비율</td>
						<td >수량</td><td >비율</td>

						<td >금액</td><td >비율</td>
						<td >금액</td><td >비율</td>
						<td >금액</td><td >비율</td>
						<td >금액</td><td >비율</td>
					</tr>
			';

			$s_query = "
				where
					o.o_canceled!='Y'
					AND o.o_paystatus = 'Y'
					AND op.op_cancel = 'N'
					AND DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'
			";

			$que = "
				select

					rdate,

					COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
					COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

					COUNT(DISTINCT(subsum_memtypeN_order_cnt)) as sum_memtypeN_order_cnt,
					COUNT(DISTINCT(subsum_memtypeY_order_cnt)) as sum_memtypeY_order_cnt,

					COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


					SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
					SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

					SUM(sub_sum_memtypeN_buy_cnt) as sum_memtypeN_buy_cnt,
					SUM(sub_sum_memtypeY_buy_cnt) as sum_memtypeY_buy_cnt,

					SUM(sub_sum_buy_cnt) as sum_buy_cnt,

					SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
					SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

					SUM(sub_sum_memtypeN_buy_price) as sum_memtypeN_buy_price,
					SUM(sub_sum_memtypeY_buy_price) as sum_memtypeY_buy_price,

					SUM(sub_sum_buy_price) as sum_buy_price

				from
				(
					SELECT

						HOUR(o_rdate) as rdate,
						op.op_oordernum ,

						IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
						IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,

						IF( o.o_memtype = 'N' , op.op_oordernum , NULL ) as subsum_memtypeN_order_cnt,
						IF( o.o_memtype != 'N' , op.op_oordernum , NULL ) as subsum_memtypeY_order_cnt,


						SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
						SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

						SUM(IF( o.o_memtype = 'N' , op.op_cnt , 0 )) as sub_sum_memtypeN_buy_cnt,
						SUM(IF( o.o_memtype != 'N' , op.op_cnt , 0 )) as sub_sum_memtypeY_buy_cnt,

						SUM( op.op_cnt ) as sub_sum_buy_cnt,

						SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
						SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,

						SUM(IF( o.o_memtype = 'N' , op_price * op_cnt , 0 )) as sub_sum_memtypeN_buy_price,
						SUM(IF( o.o_memtype != 'N' ,  op_price * op_cnt , 0 )) as sub_sum_memtypeY_buy_price,

						SUM( op_price * op_cnt ) as sub_sum_buy_price

					FROM smart_order_product as op
					INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )

					" . $s_query . "

					group by op.op_oordernum

				) as tbl_view

				GROUP BY rdate
				ORDER BY
					rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){

				// 구매건수
				$arr_res[$v['rdate']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
				$arr_res[$v['rdate']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
				$arr_res[$v['rdate']]['order_cnt']['memtypeN'] = $v['sum_memtypeN_order_cnt'];
				$arr_res[$v['rdate']]['order_cnt']['memtypeY'] = $v['sum_memtypeY_order_cnt'];
				$arr_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

				// 구매수량
				$arr_res[$v['rdate']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
				$arr_res[$v['rdate']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
				$arr_res[$v['rdate']]['buy_cnt']['memtypeN'] = $v['sum_memtypeN_buy_cnt'];
				$arr_res[$v['rdate']]['buy_cnt']['memtypeY'] = $v['sum_memtypeY_buy_cnt'];
				$arr_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

				// 구매금액
				$arr_res[$v['rdate']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
				$arr_res[$v['rdate']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
				$arr_res[$v['rdate']]['buy_price']['memtypeN'] = $v['sum_memtypeN_buy_price'];
				$arr_res[$v['rdate']]['buy_price']['memtypeY'] = $v['sum_memtypeY_buy_price'];
				$arr_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

			}
			for($i=0 ; $i<=23 ; $i++){

				$app_date = $i . "시";
				$app_date_key = $i;

				$app_sum_order_cnt = $arr_res[$app_date_key]['order_cnt']['sum'] > 0 ? $arr_res[$app_date_key]['order_cnt']['sum'] : 1;
				$app_sum_buy_cnt = $arr_res[$app_date_key]['buy_cnt']['sum'] > 0 ? $arr_res[$app_date_key]['buy_cnt']['sum'] : 1;
				$app_sum_buy_price = $arr_res[$app_date_key]['buy_price']['sum'] > 0 ? $arr_res[$app_date_key]['buy_price']['sum'] : 1;

				echo '
					<tr>
						<td>'. $app_date .'</td><!-- 시간 -->

						<!-- 구매건수 -->
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['memtypeY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['memtypeY'] * 100 / $app_sum_order_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['memtypeN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['memtypeN'] * 100 / $app_sum_order_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['mobileN'] * 100 / $app_sum_order_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['mobileY'] * 100 / $app_sum_order_cnt , 2) . '%</td>

						<!-- 구매수량 -->
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['memtypeY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['memtypeY'] * 100 / $app_sum_buy_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['memtypeN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['memtypeN'] * 100 / $app_sum_buy_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['mobileN'] * 100 / $app_sum_buy_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['mobileY'] * 100 / $app_sum_buy_cnt , 2) . '%</td>

						<!-- 구매금액 -->
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['memtypeY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['memtypeY'] * 100 / $app_sum_buy_price , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['memtypeN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['memtypeN'] * 100 / $app_sum_buy_price , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['mobileN'] * 100 / $app_sum_buy_price , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['mobileY'] * 100 / $app_sum_buy_price , 2) . '%</td>
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 주문통계 - 시간별 --------------

		// -------------- 주문통계 - 요일별 --------------
		case "all_week_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_order_all_week_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));

			echo '
				<table border=1>
					<tr>
						<td rowspan="3">시간</td>
						<td colspan="9">구매건수</td>
						<td colspan="9">구매수량</td>
						<td colspan="9">구매금액</td>
					</tr>
					<tr>
						<td rowspan="2">총건수</td>
						<td colspan="2">회원</td>
						<td colspan="2">비회원</td>
						<td colspan="2">PC</td>
						<td colspan="2">MOBILE</td>

						<td rowspan="2">총수량</td>
						<td colspan="2">회원</td>
						<td colspan="2">비회원</td>
						<td colspan="2">PC</td>
						<td colspan="2">MOBILE</td>

						<td rowspan="2">총금액</td>
						<td colspan="2">회원</td>
						<td colspan="2">비회원</td>
						<td colspan="2">PC</td>
						<td colspan="2">MOBILE</td>
					</tr>
					<tr>
						<td >건수</td><td >비율</td>
						<td >건수</td><td >비율</td>
						<td >건수</td><td >비율</td>
						<td >건수</td><td >비율</td>

						<td >수량</td><td >비율</td>
						<td >수량</td><td >비율</td>
						<td >수량</td><td >비율</td>
						<td >수량</td><td >비율</td>

						<td >금액</td><td >비율</td>
						<td >금액</td><td >비율</td>
						<td >금액</td><td >비율</td>
						<td >금액</td><td >비율</td>
					</tr>
			';

			$s_query = "
				where
					o.o_canceled!='Y'
					AND o.o_paystatus = 'Y'
					AND op.op_cancel = 'N'
					AND DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'
			";

			$que = "
				select

					rdate,

					COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
					COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

					COUNT(DISTINCT(subsum_memtypeN_order_cnt)) as sum_memtypeN_order_cnt,
					COUNT(DISTINCT(subsum_memtypeY_order_cnt)) as sum_memtypeY_order_cnt,

					COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


					SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
					SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

					SUM(sub_sum_memtypeN_buy_cnt) as sum_memtypeN_buy_cnt,
					SUM(sub_sum_memtypeY_buy_cnt) as sum_memtypeY_buy_cnt,

					SUM(sub_sum_buy_cnt) as sum_buy_cnt,

					SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
					SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

					SUM(sub_sum_memtypeN_buy_price) as sum_memtypeN_buy_price,
					SUM(sub_sum_memtypeY_buy_price) as sum_memtypeY_buy_price,

					SUM(sub_sum_buy_price) as sum_buy_price

				from
				(
					SELECT

						DATE_FORMAT(o_rdate, '%w') as rdate ,
						op.op_oordernum ,

						IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
						IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,

						IF( o.o_memtype = 'N' , op.op_oordernum , NULL ) as subsum_memtypeN_order_cnt,
						IF( o.o_memtype != 'N' , op.op_oordernum , NULL ) as subsum_memtypeY_order_cnt,


						SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
						SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

						SUM(IF( o.o_memtype = 'N' , op.op_cnt , 0 )) as sub_sum_memtypeN_buy_cnt,
						SUM(IF( o.o_memtype != 'N' , op.op_cnt , 0 )) as sub_sum_memtypeY_buy_cnt,

						SUM( op.op_cnt ) as sub_sum_buy_cnt,

						SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
						SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,

						SUM(IF( o.o_memtype = 'N' , op_price * op_cnt , 0 )) as sub_sum_memtypeN_buy_price,
						SUM(IF( o.o_memtype != 'N' ,  op_price * op_cnt , 0 )) as sub_sum_memtypeY_buy_price,

						SUM( op_price * op_cnt ) as sub_sum_buy_price

					FROM smart_order_product as op
					INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )

					" . $s_query . "

					group by op.op_oordernum

				) as tbl_view

				GROUP BY rdate
				ORDER BY
					rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){

				// 구매건수
				$arr_res[$v['rdate']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
				$arr_res[$v['rdate']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
				$arr_res[$v['rdate']]['order_cnt']['memtypeN'] = $v['sum_memtypeN_order_cnt'];
				$arr_res[$v['rdate']]['order_cnt']['memtypeY'] = $v['sum_memtypeY_order_cnt'];
				$arr_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

				// 구매수량
				$arr_res[$v['rdate']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
				$arr_res[$v['rdate']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
				$arr_res[$v['rdate']]['buy_cnt']['memtypeN'] = $v['sum_memtypeN_buy_cnt'];
				$arr_res[$v['rdate']]['buy_cnt']['memtypeY'] = $v['sum_memtypeY_buy_cnt'];
				$arr_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

				// 구매금액
				$arr_res[$v['rdate']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
				$arr_res[$v['rdate']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
				$arr_res[$v['rdate']]['buy_price']['memtypeN'] = $v['sum_memtypeN_buy_price'];
				$arr_res[$v['rdate']]['buy_price']['memtypeY'] = $v['sum_memtypeY_buy_price'];
				$arr_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

			}
			for($i=0 ; $i<=6 ; $i++){

				$app_date = week_name( $i , '요일');
				$app_date_key = $i;

				$app_sum_order_cnt = $arr_res[$app_date_key]['order_cnt']['sum'] > 0 ? $arr_res[$app_date_key]['order_cnt']['sum'] : 1;
				$app_sum_buy_cnt = $arr_res[$app_date_key]['buy_cnt']['sum'] > 0 ? $arr_res[$app_date_key]['buy_cnt']['sum'] : 1;
				$app_sum_buy_price = $arr_res[$app_date_key]['buy_price']['sum'] > 0 ? $arr_res[$app_date_key]['buy_price']['sum'] : 1;

				echo '
					<tr>
						<td>'. $app_date .'</td><!-- 시간 -->

						<!-- 구매건수 -->
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['memtypeY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['memtypeY'] * 100 / $app_sum_order_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['memtypeN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['memtypeN'] * 100 / $app_sum_order_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['mobileN'] * 100 / $app_sum_order_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['order_cnt']['mobileY'] * 100 / $app_sum_order_cnt , 2) . '%</td>

						<!-- 구매수량 -->
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['memtypeY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['memtypeY'] * 100 / $app_sum_buy_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['memtypeN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['memtypeN'] * 100 / $app_sum_buy_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['mobileN'] * 100 / $app_sum_buy_cnt , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_cnt']['mobileY'] * 100 / $app_sum_buy_cnt , 2) . '%</td>

						<!-- 구매금액 -->
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['memtypeY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['memtypeY'] * 100 / $app_sum_buy_price , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['memtypeN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['memtypeN'] * 100 / $app_sum_buy_price , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['mobileN'] * 100 / $app_sum_buy_price , 2) . '%</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['buy_price']['mobileY'] * 100 / $app_sum_buy_price , 2) . '%</td>
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 주문통계 - 요일별 --------------

// -------------- 전체 주문통계  ----------------------------





// -------------- 연령별 주문통계  ----------------------------

		// -------------- 연령별 주문통계 - 일자별 --------------
		case "age_day_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_order_age_day_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m', time()));


			echo '
				<table border=1>
					<tr>
						<td rowspan="2">날짜</td>
						<td rowspan="2">접속기기</td>
						<td colspan="3">전체</td>
			';
			foreach($arr_order_age as $sk=>$sv) {
				echo '<td colspan="3">'. $sv .'</td>';
			}
			echo '
					</tr>
					<tr>
						<td>구매건수</td>
						<td>구매수량</td>
						<td>구매금액</td>
			';
			foreach($arr_order_age as $sk=>$sv) {
				echo '
					<td>구매건수</td>
					<td>구매수량</td>
					<td>구매금액</td>
				';
			}
			echo '
					</tr>
			';

			$s_query = "
				where
					o.o_canceled!='Y'
					AND o.o_paystatus = 'Y'
					AND op.op_cancel = 'N'
					AND LEFT(o.o_rdate,7) = '". $pass_date ."'
			";

			$que = "
				select

					rdate, age,

					COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
					COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

					COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


					SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
					SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

					SUM(sub_sum_buy_cnt) as sum_buy_cnt,

					SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
					SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

					SUM(sub_sum_buy_price) as sum_buy_price

				from
				(
					SELECT

						DATE(o.o_rdate) as rdate ,
						TRUNCATE( (YEAR( CURDATE( ) ) - YEAR( ind.in_birth ) ) /10, 0 ) *10 AS age,
						op.op_oordernum ,

						IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
						IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,


						SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
						SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

						SUM( op.op_cnt ) as sub_sum_buy_cnt,

						SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
						SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,


						SUM( op_price * op_cnt ) as sub_sum_buy_price

					FROM smart_order_product as op
					INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )
					INNER JOIN smart_individual as ind ON (ind.in_id = o.o_mid AND IFNULL( ind.in_birth , '0000-00-00' ) != '0000-00-00' )

					" . $s_query . "

					group by op.op_oordernum

				) as tbl_view

				GROUP BY rdate, age
				ORDER BY
					rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){

				// 연령대 정리 ::: 10대미만, 70대 초과일 경우 기타
				$v['age'] = ($v['age'] > 70 || $v['age'] < 10 ) ? 'etc' : $v['age'];

				// 구매건수
				$arr_res[$v['rdate']][$v['age']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
				$arr_res[$v['rdate']][$v['age']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
				$arr_res[$v['rdate']][$v['age']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

				$arr_all_res[$v['rdate']]['order_cnt']['mobileY'] += $v['sum_mobileY_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['mobileN'] += $v['sum_mobileN_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;


				// 구매수량
				$arr_res[$v['rdate']][$v['age']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
				$arr_res[$v['rdate']][$v['age']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
				$arr_res[$v['rdate']][$v['age']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

				$arr_all_res[$v['rdate']]['buy_cnt']['mobileY'] += $v['sum_mobileY_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['mobileN'] += $v['sum_mobileN_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;


				// 구매금액
				$arr_res[$v['rdate']][$v['age']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
				$arr_res[$v['rdate']][$v['age']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
				$arr_res[$v['rdate']][$v['age']]['buy_price']['sum'] += $v['sum_buy_price'] ;

				$arr_all_res[$v['rdate']]['buy_price']['mobileY'] += $v['sum_mobileY_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['mobileN'] += $v['sum_mobileN_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

			}
			for($i=1 ; $i<=date("t" , strtotime(date("{$Select_Year}-{$Select_Month}-01"))) ; $i++){

				$app_date = $Select_Year ."-". $Select_Month ."-". sprintf("%02d" , $i);
				$app_date_key = $app_date;

				echo '
					<tr>
						<td rowspan="3">'. $app_date .'</td><!-- 날짜 -->
						<td>소계</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['sum']) .'</td>
				';
				foreach($arr_order_age as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['sum']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileN']) .'</td>
				';
				foreach($arr_order_age as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileN']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileY']) .'</td>
				';
				foreach($arr_order_age as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileY']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----

			}
			echo '</table>';
			exit;
			break;
		// -------------- 연령별 주문통계 - 일자별 --------------

		// -------------- 연령별 주문통계 - 월별 --------------
		case "age_month_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_order_age_month_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y', time()));


			echo '
				<table border=1>
					<tr>
						<td rowspan="2">년월</td>
						<td rowspan="2">접속기기</td>
						<td colspan="3">전체</td>
			';
			foreach($arr_order_age as $sk=>$sv) {
				echo '<td colspan="3">'. $sv .'</td>';
			}
			echo '
					</tr>
					<tr>
						<td>구매건수</td>
						<td>구매수량</td>
						<td>구매금액</td>
			';
			foreach($arr_order_age as $sk=>$sv) {
				echo '
					<td>구매건수</td>
					<td>구매수량</td>
					<td>구매금액</td>
				';
			}
			echo '
					</tr>
			';

			$s_query = "
				where
					o.o_canceled!='Y'
					AND o.o_paystatus = 'Y'
					AND op.op_cancel = 'N'
					AND LEFT(o.o_rdate,4) = '". $pass_date ."'
			";

			$que = "
				select

					rdate, age,

					COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
					COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

					COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


					SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
					SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

					SUM(sub_sum_buy_cnt) as sum_buy_cnt,

					SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
					SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

					SUM(sub_sum_buy_price) as sum_buy_price

				from
				(
					SELECT

						LEFT(o_rdate,7) as rdate,
						TRUNCATE( (YEAR( CURDATE( ) ) - YEAR( ind.in_birth ) ) /10, 0 ) *10 AS age,
						op.op_oordernum ,

						IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
						IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,


						SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
						SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

						SUM( op.op_cnt ) as sub_sum_buy_cnt,

						SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
						SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,


						SUM( op_price * op_cnt ) as sub_sum_buy_price

					FROM smart_order_product as op
					INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )
					INNER JOIN smart_individual as ind ON (ind.in_id = o.o_mid AND IFNULL( ind.in_birth , '0000-00-00' ) != '0000-00-00' )

					" . $s_query . "

					group by op.op_oordernum

				) as tbl_view

				GROUP BY rdate, age
				ORDER BY
					rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){

				// 연령대 정리 ::: 10대미만, 70대 초과일 경우 기타
				$v['age'] = ($v['age'] > 70 || $v['age'] < 10 ) ? 'etc' : $v['age'];

				// 구매건수
				$arr_res[$v['rdate']][$v['age']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
				$arr_res[$v['rdate']][$v['age']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
				$arr_res[$v['rdate']][$v['age']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

				$arr_all_res[$v['rdate']]['order_cnt']['mobileY'] += $v['sum_mobileY_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['mobileN'] += $v['sum_mobileN_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;


				// 구매수량
				$arr_res[$v['rdate']][$v['age']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
				$arr_res[$v['rdate']][$v['age']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
				$arr_res[$v['rdate']][$v['age']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

				$arr_all_res[$v['rdate']]['buy_cnt']['mobileY'] += $v['sum_mobileY_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['mobileN'] += $v['sum_mobileN_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;


				// 구매금액
				$arr_res[$v['rdate']][$v['age']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
				$arr_res[$v['rdate']][$v['age']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
				$arr_res[$v['rdate']][$v['age']]['buy_price']['sum'] += $v['sum_buy_price'] ;

				$arr_all_res[$v['rdate']]['buy_price']['mobileY'] += $v['sum_mobileY_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['mobileN'] += $v['sum_mobileN_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

			}
			for($i=1 ; $i<=12 ; $i++){

				$app_date = $Select_Year ."년 ". sprintf("%02d" , $i)."월";
				$app_date_key = $Select_Year ."-". sprintf("%02d" , $i);

				echo '
					<tr>
						<td rowspan="3">'. $app_date .'</td><!-- 년월 -->
						<td>소계</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['sum']) .'</td>
				';
				foreach($arr_order_age as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['sum']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileN']) .'</td>
				';
				foreach($arr_order_age as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileN']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileY']) .'</td>
				';
				foreach($arr_order_age as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileY']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----

			}
			echo '</table>';
			exit;
			break;
		// -------------- 연령별 주문통계 - 월별 --------------

		// -------------- 연령별 주문통계 - 시간별 --------------
		case "age_hour_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_order_age_hour_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));


			echo '
				<table border=1>
					<tr>
						<td rowspan="2">시간</td>
						<td rowspan="2">접속기기</td>
						<td colspan="3">전체</td>
			';
			foreach($arr_order_age as $sk=>$sv) {
				echo '<td colspan="3">'. $sv .'</td>';
			}
			echo '
					</tr>
					<tr>
						<td>구매건수</td>
						<td>구매수량</td>
						<td>구매금액</td>
			';
			foreach($arr_order_age as $sk=>$sv) {
				echo '
					<td>구매건수</td>
					<td>구매수량</td>
					<td>구매금액</td>
				';
			}
			echo '
					</tr>
			';

			$s_query = "
				where
					o.o_canceled!='Y'
					AND o.o_paystatus = 'Y'
					AND op.op_cancel = 'N'
					AND DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'
			";

			$que = "
				select

					rdate, age,

					COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
					COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

					COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


					SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
					SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

					SUM(sub_sum_buy_cnt) as sum_buy_cnt,

					SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
					SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

					SUM(sub_sum_buy_price) as sum_buy_price

				from
				(
					SELECT

						HOUR(o_rdate) as rdate ,
						TRUNCATE( (YEAR( CURDATE( ) ) - YEAR( ind.in_birth ) ) /10, 0 ) *10 AS age,
						op.op_oordernum ,

						IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
						IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,


						SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
						SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

						SUM( op.op_cnt ) as sub_sum_buy_cnt,

						SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
						SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,


						SUM( op_price * op_cnt ) as sub_sum_buy_price

					FROM smart_order_product as op
					INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )
					INNER JOIN smart_individual as ind ON (ind.in_id = o.o_mid AND IFNULL( ind.in_birth , '0000-00-00' ) != '0000-00-00' )

					" . $s_query . "

					group by op.op_oordernum

				) as tbl_view

				GROUP BY rdate, age
				ORDER BY
					rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){

				// 연령대 정리 ::: 10대미만, 70대 초과일 경우 기타
				$v['age'] = ($v['age'] > 70 || $v['age'] < 10 ) ? 'etc' : $v['age'];

				// 구매건수
				$arr_res[$v['rdate']][$v['age']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
				$arr_res[$v['rdate']][$v['age']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
				$arr_res[$v['rdate']][$v['age']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

				$arr_all_res[$v['rdate']]['order_cnt']['mobileY'] += $v['sum_mobileY_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['mobileN'] += $v['sum_mobileN_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;


				// 구매수량
				$arr_res[$v['rdate']][$v['age']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
				$arr_res[$v['rdate']][$v['age']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
				$arr_res[$v['rdate']][$v['age']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

				$arr_all_res[$v['rdate']]['buy_cnt']['mobileY'] += $v['sum_mobileY_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['mobileN'] += $v['sum_mobileN_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;


				// 구매금액
				$arr_res[$v['rdate']][$v['age']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
				$arr_res[$v['rdate']][$v['age']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
				$arr_res[$v['rdate']][$v['age']]['buy_price']['sum'] += $v['sum_buy_price'] ;

				$arr_all_res[$v['rdate']]['buy_price']['mobileY'] += $v['sum_mobileY_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['mobileN'] += $v['sum_mobileN_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

			}
			for($i=0 ; $i<=23 ; $i++){

				$app_date_key = $i;

				echo '
					<tr>
						<td rowspan="3">'. $i .'시</td><!-- 시간 -->
						<td>소계</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['sum']) .'</td>
				';
				foreach($arr_order_age as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['sum']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileN']) .'</td>
				';
				foreach($arr_order_age as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileN']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileY']) .'</td>
				';
				foreach($arr_order_age as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileY']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----

			}
			echo '</table>';
			exit;
			break;
		// -------------- 연령별 주문통계 - 시간별 --------------

		// -------------- 연령별 주문통계 - 요일별 --------------
		case "age_week_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_order_age_week_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));


			echo '
				<table border=1>
					<tr>
						<td rowspan="2">요일</td>
						<td rowspan="2">접속기기</td>
						<td colspan="3">전체</td>
			';
			foreach($arr_order_age as $sk=>$sv) {
				echo '<td colspan="3">'. $sv .'</td>';
			}
			echo '
					</tr>
					<tr>
						<td>구매건수</td>
						<td>구매수량</td>
						<td>구매금액</td>
			';
			foreach($arr_order_age as $sk=>$sv) {
				echo '
					<td>구매건수</td>
					<td>구매수량</td>
					<td>구매금액</td>
				';
			}
			echo '
					</tr>
			';

			$s_query = "
				where
					o.o_canceled!='Y'
					AND o.o_paystatus = 'Y'
					AND op.op_cancel = 'N'
					AND DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'
			";

			$que = "
				select

					rdate, age,

					COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
					COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

					COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


					SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
					SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

					SUM(sub_sum_buy_cnt) as sum_buy_cnt,

					SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
					SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

					SUM(sub_sum_buy_price) as sum_buy_price

				from
				(
					SELECT

						DATE_FORMAT(o_rdate, '%w') as rdate ,
						TRUNCATE( (YEAR( CURDATE( ) ) - YEAR( ind.in_birth ) ) /10, 0 ) *10 AS age,
						op.op_oordernum ,

						IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
						IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,


						SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
						SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

						SUM( op.op_cnt ) as sub_sum_buy_cnt,

						SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
						SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,


						SUM( op_price * op_cnt ) as sub_sum_buy_price

					FROM smart_order_product as op
					INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )
					INNER JOIN smart_individual as ind ON (ind.in_id = o.o_mid AND IFNULL( ind.in_birth , '0000-00-00' ) != '0000-00-00' )

					" . $s_query . "

					group by op.op_oordernum

				) as tbl_view

				GROUP BY rdate, age
				ORDER BY
					rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){

				// 연령대 정리 ::: 10대미만, 70대 초과일 경우 기타
				$v['age'] = ($v['age'] > 70 || $v['age'] < 10 ) ? 'etc' : $v['age'];

				// 구매건수
				$arr_res[$v['rdate']][$v['age']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
				$arr_res[$v['rdate']][$v['age']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
				$arr_res[$v['rdate']][$v['age']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

				$arr_all_res[$v['rdate']]['order_cnt']['mobileY'] += $v['sum_mobileY_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['mobileN'] += $v['sum_mobileN_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;


				// 구매수량
				$arr_res[$v['rdate']][$v['age']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
				$arr_res[$v['rdate']][$v['age']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
				$arr_res[$v['rdate']][$v['age']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

				$arr_all_res[$v['rdate']]['buy_cnt']['mobileY'] += $v['sum_mobileY_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['mobileN'] += $v['sum_mobileN_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;


				// 구매금액
				$arr_res[$v['rdate']][$v['age']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
				$arr_res[$v['rdate']][$v['age']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
				$arr_res[$v['rdate']][$v['age']]['buy_price']['sum'] += $v['sum_buy_price'] ;

				$arr_all_res[$v['rdate']]['buy_price']['mobileY'] += $v['sum_mobileY_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['mobileN'] += $v['sum_mobileN_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

			}
			for($i=0 ; $i<=6 ; $i++){

				$app_date = week_name( $i , '요일');
				$app_date_key = $i;

				echo '
					<tr>
						<td rowspan="3">'. $app_date .'</td><!-- 요일 -->
						<td>소계</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['sum']) .'</td>
				';
				foreach($arr_order_age as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['sum']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileN']) .'</td>
				';
				foreach($arr_order_age as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileN']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileY']) .'</td>
				';
				foreach($arr_order_age as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileY']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----

			}
			echo '</table>';
			exit;
			break;
		// -------------- 연령별 주문통계 - 요일별 --------------

// -------------- 연령별 주문통계  ----------------------------




// -------------- 지역별 주문통계  ----------------------------

		// -------------- 지역별 주문통계 - 일자별 --------------
		case "area_day_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_order_area_day_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m', time()));


			echo '
				<table border=1>
					<tr>
						<td rowspan="2">날짜</td>
						<td rowspan="2">접속기기</td>
						<td colspan="3">전체</td>
			';
			foreach($arr_order_area_basic as $sk=>$sv) {
				echo '<td colspan="3">'. $sv .'</td>';
			}
			echo '
					</tr>
					<tr>
						<td>구매건수</td>
						<td>구매수량</td>
						<td>구매금액</td>
			';
			foreach($arr_order_area_basic as $sk=>$sv) {
				echo '
					<td>구매건수</td>
					<td>구매수량</td>
					<td>구매금액</td>
				';
			}
			echo '
					</tr>
			';

			$s_query = "
				where
					o.o_canceled!='Y'
					AND o.o_paystatus = 'Y'
					AND op.op_cancel = 'N'
					AND LEFT(o.o_rdate,7) = '". $pass_date ."'
			";

			$que = "
				select

					rdate, area,

					COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
					COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

					COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


					SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
					SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

					SUM(sub_sum_buy_cnt) as sum_buy_cnt,

					SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
					SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

					SUM(sub_sum_buy_price) as sum_buy_price

				from
				(
					SELECT

						DATE(o.o_rdate) as rdate ,
						o.o_area as area,
						op.op_oordernum ,

						IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
						IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,


						SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
						SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

						SUM( op.op_cnt ) as sub_sum_buy_cnt,

						SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
						SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,


						SUM( op_price * op_cnt ) as sub_sum_buy_price

					FROM smart_order_product as op
					INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )

					" . $s_query . "

					group by op.op_oordernum

				) as tbl_view

				GROUP BY rdate, area
				ORDER BY
					rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){

				// 구매건수
				$arr_res[$v['rdate']][$v['area']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
				$arr_res[$v['rdate']][$v['area']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
				$arr_res[$v['rdate']][$v['area']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

				$arr_all_res[$v['rdate']]['order_cnt']['mobileY'] += $v['sum_mobileY_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['mobileN'] += $v['sum_mobileN_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;


				// 구매수량
				$arr_res[$v['rdate']][$v['area']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
				$arr_res[$v['rdate']][$v['area']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
				$arr_res[$v['rdate']][$v['area']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

				$arr_all_res[$v['rdate']]['buy_cnt']['mobileY'] += $v['sum_mobileY_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['mobileN'] += $v['sum_mobileN_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;


				// 구매금액
				$arr_res[$v['rdate']][$v['area']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
				$arr_res[$v['rdate']][$v['area']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
				$arr_res[$v['rdate']][$v['area']]['buy_price']['sum'] += $v['sum_buy_price'] ;

				$arr_all_res[$v['rdate']]['buy_price']['mobileY'] += $v['sum_mobileY_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['mobileN'] += $v['sum_mobileN_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

			}
			for($i=1 ; $i<=date("t" , strtotime(date("{$Select_Year}-{$Select_Month}-01"))) ; $i++){

				$app_date = $Select_Year ."-". $Select_Month ."-". sprintf("%02d" , $i);
				$app_date_key = $app_date;

				echo '
					<tr>
						<td rowspan="3">'. $app_date .'</td><!-- 날짜 -->
						<td>소계</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['sum']) .'</td>
				';
				foreach($arr_order_area_basic as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sv]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_price']['sum']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileN']) .'</td>
				';
				foreach($arr_order_area_basic as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sv]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_price']['mobileN']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileY']) .'</td>
				';
				foreach($arr_order_area_basic as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sv]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_price']['mobileY']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----

			}
			echo '</table>';
			exit;
			break;
		// -------------- 지역별 주문통계 - 일자별 --------------

		// -------------- 지역별 주문통계 - 월별 --------------
		case "area_month_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_order_area_month_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y', time()));


			echo '
				<table border=1>
					<tr>
						<td rowspan="2">년월</td>
						<td rowspan="2">접속기기</td>
						<td colspan="3">전체</td>
			';
			foreach($arr_order_area_basic as $sk=>$sv) {
				echo '<td colspan="3">'. $sv .'</td>';
			}
			echo '
					</tr>
					<tr>
						<td>구매건수</td>
						<td>구매수량</td>
						<td>구매금액</td>
			';
			foreach($arr_order_area_basic as $sk=>$sv) {
				echo '
					<td>구매건수</td>
					<td>구매수량</td>
					<td>구매금액</td>
				';
			}
			echo '
					</tr>
			';

			$s_query = "
				where
					o.o_canceled!='Y'
					AND o.o_paystatus = 'Y'
					AND op.op_cancel = 'N'
					AND LEFT(o.o_rdate,4) = '". $pass_date ."'
			";

			$que = "
				select

					rdate, area,

					COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
					COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

					COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


					SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
					SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

					SUM(sub_sum_buy_cnt) as sum_buy_cnt,

					SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
					SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

					SUM(sub_sum_buy_price) as sum_buy_price

				from
				(
					SELECT

						LEFT(o.o_rdate,7) as rdate ,
						o.o_area as area,
						op.op_oordernum ,

						IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
						IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,


						SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
						SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

						SUM( op.op_cnt ) as sub_sum_buy_cnt,

						SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
						SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,


						SUM( op_price * op_cnt ) as sub_sum_buy_price

					FROM smart_order_product as op
					INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )

					" . $s_query . "

					group by op.op_oordernum

				) as tbl_view

				GROUP BY rdate, area
				ORDER BY
					rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){

				// 구매건수
				$arr_res[$v['rdate']][$v['area']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
				$arr_res[$v['rdate']][$v['area']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
				$arr_res[$v['rdate']][$v['area']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

				$arr_all_res[$v['rdate']]['order_cnt']['mobileY'] += $v['sum_mobileY_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['mobileN'] += $v['sum_mobileN_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;


				// 구매수량
				$arr_res[$v['rdate']][$v['area']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
				$arr_res[$v['rdate']][$v['area']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
				$arr_res[$v['rdate']][$v['area']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

				$arr_all_res[$v['rdate']]['buy_cnt']['mobileY'] += $v['sum_mobileY_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['mobileN'] += $v['sum_mobileN_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;


				// 구매금액
				$arr_res[$v['rdate']][$v['area']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
				$arr_res[$v['rdate']][$v['area']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
				$arr_res[$v['rdate']][$v['area']]['buy_price']['sum'] += $v['sum_buy_price'] ;

				$arr_all_res[$v['rdate']]['buy_price']['mobileY'] += $v['sum_mobileY_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['mobileN'] += $v['sum_mobileN_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

			}
			for($i=1 ; $i<=12 ; $i++){

				$app_date = $Select_Year ."년 ". sprintf("%02d" , $i)."월";
				$app_date_key = $Select_Year ."-". sprintf("%02d" , $i);

				echo '
					<tr>
						<td rowspan="3">'. $app_date .'</td><!-- 년월 -->
						<td>소계</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['sum']) .'</td>
				';
				foreach($arr_order_area_basic as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sv]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_price']['sum']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileN']) .'</td>
				';
				foreach($arr_order_area_basic as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sv]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_price']['mobileN']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileY']) .'</td>
				';
				foreach($arr_order_area_basic as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sv]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_price']['mobileY']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----

			}
			echo '</table>';
			exit;
			break;
		// -------------- 지역별 주문통계 - 월별 --------------

		// -------------- 지역별 주문통계 - 시간별 --------------
		case "area_hour_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_order_area_hour_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));

			echo '
				<table border=1>
					<tr>
						<td rowspan="2">시간</td>
						<td rowspan="2">접속기기</td>
						<td colspan="3">전체</td>
			';
			foreach($arr_order_area_basic as $sk=>$sv) {
				echo '<td colspan="3">'. $sv .'</td>';
			}
			echo '
					</tr>
					<tr>
						<td>구매건수</td>
						<td>구매수량</td>
						<td>구매금액</td>
			';
			foreach($arr_order_area_basic as $sk=>$sv) {
				echo '
					<td>구매건수</td>
					<td>구매수량</td>
					<td>구매금액</td>
				';
			}
			echo '
					</tr>
			';

			$s_query = "
				where
					o.o_canceled!='Y'
					AND o.o_paystatus = 'Y'
					AND op.op_cancel = 'N'
					AND DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'
			";

			$que = "
				select

					rdate, area,

					COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
					COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

					COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


					SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
					SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

					SUM(sub_sum_buy_cnt) as sum_buy_cnt,

					SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
					SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

					SUM(sub_sum_buy_price) as sum_buy_price

				from
				(
					SELECT

						HOUR(o.o_rdate) as rdate ,
						o.o_area as area,
						op.op_oordernum ,

						IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
						IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,


						SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
						SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

						SUM( op.op_cnt ) as sub_sum_buy_cnt,

						SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
						SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,


						SUM( op_price * op_cnt ) as sub_sum_buy_price

					FROM smart_order_product as op
					INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )

					" . $s_query . "

					group by op.op_oordernum

				) as tbl_view

				GROUP BY rdate, area
				ORDER BY
					rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){

				// 구매건수
				$arr_res[$v['rdate']][$v['area']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
				$arr_res[$v['rdate']][$v['area']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
				$arr_res[$v['rdate']][$v['area']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

				$arr_all_res[$v['rdate']]['order_cnt']['mobileY'] += $v['sum_mobileY_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['mobileN'] += $v['sum_mobileN_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;


				// 구매수량
				$arr_res[$v['rdate']][$v['area']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
				$arr_res[$v['rdate']][$v['area']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
				$arr_res[$v['rdate']][$v['area']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

				$arr_all_res[$v['rdate']]['buy_cnt']['mobileY'] += $v['sum_mobileY_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['mobileN'] += $v['sum_mobileN_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;


				// 구매금액
				$arr_res[$v['rdate']][$v['area']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
				$arr_res[$v['rdate']][$v['area']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
				$arr_res[$v['rdate']][$v['area']]['buy_price']['sum'] += $v['sum_buy_price'] ;

				$arr_all_res[$v['rdate']]['buy_price']['mobileY'] += $v['sum_mobileY_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['mobileN'] += $v['sum_mobileN_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

			}
			for($i=0 ; $i<=23 ; $i++){

				$app_date_key = $i;

				echo '
					<tr>
						<td rowspan="3">'. $i .'시</td><!-- 시간 -->
						<td>소계</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['sum']) .'</td>
				';
				foreach($arr_order_area_basic as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sv]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_price']['sum']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileN']) .'</td>
				';
				foreach($arr_order_area_basic as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sv]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_price']['mobileN']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileY']) .'</td>
				';
				foreach($arr_order_area_basic as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sv]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_price']['mobileY']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----

			}
			echo '</table>';
			exit;
			break;
		// -------------- 지역별 주문통계 - 시간별 --------------

		// -------------- 지역별 주문통계 - 요일별 --------------
		case "area_week_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_order_area_week_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));

			echo '
				<table border=1>
					<tr>
						<td rowspan="2">요일</td>
						<td rowspan="2">접속기기</td>
						<td colspan="3">전체</td>
			';
			foreach($arr_order_area_basic as $sk=>$sv) {
				echo '<td colspan="3">'. $sv .'</td>';
			}
			echo '
					</tr>
					<tr>
						<td>구매건수</td>
						<td>구매수량</td>
						<td>구매금액</td>
			';
			foreach($arr_order_area_basic as $sk=>$sv) {
				echo '
					<td>구매건수</td>
					<td>구매수량</td>
					<td>구매금액</td>
				';
			}
			echo '
					</tr>
			';

			$s_query = "
				where
					o.o_canceled!='Y'
					AND o.o_paystatus = 'Y'
					AND op.op_cancel = 'N'
					AND DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'
			";

			$que = "
				select

					rdate, area,

					COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
					COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

					COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


					SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
					SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

					SUM(sub_sum_buy_cnt) as sum_buy_cnt,

					SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
					SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

					SUM(sub_sum_buy_price) as sum_buy_price

				from
				(
					SELECT

						DATE_FORMAT(o.o_rdate, '%w') as rdate ,
						o.o_area as area,
						op.op_oordernum ,

						IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
						IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,


						SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
						SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

						SUM( op.op_cnt ) as sub_sum_buy_cnt,

						SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
						SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,


						SUM( op_price * op_cnt ) as sub_sum_buy_price

					FROM smart_order_product as op
					INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )

					" . $s_query . "

					group by op.op_oordernum

				) as tbl_view

				GROUP BY rdate, area
				ORDER BY
					rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){

				// 구매건수
				$arr_res[$v['rdate']][$v['area']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
				$arr_res[$v['rdate']][$v['area']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
				$arr_res[$v['rdate']][$v['area']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

				$arr_all_res[$v['rdate']]['order_cnt']['mobileY'] += $v['sum_mobileY_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['mobileN'] += $v['sum_mobileN_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;


				// 구매수량
				$arr_res[$v['rdate']][$v['area']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
				$arr_res[$v['rdate']][$v['area']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
				$arr_res[$v['rdate']][$v['area']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

				$arr_all_res[$v['rdate']]['buy_cnt']['mobileY'] += $v['sum_mobileY_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['mobileN'] += $v['sum_mobileN_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;


				// 구매금액
				$arr_res[$v['rdate']][$v['area']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
				$arr_res[$v['rdate']][$v['area']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
				$arr_res[$v['rdate']][$v['area']]['buy_price']['sum'] += $v['sum_buy_price'] ;

				$arr_all_res[$v['rdate']]['buy_price']['mobileY'] += $v['sum_mobileY_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['mobileN'] += $v['sum_mobileN_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

			}
			for($i=0 ; $i<=6 ; $i++){

				$app_date_key = $i;

				echo '
					<tr>
						<td rowspan="3">'. week_name( $i , '요일') .'</td><!-- 요일 -->
						<td>소계</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['sum']) .'</td>
				';
				foreach($arr_order_area_basic as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sv]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_price']['sum']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileN']) .'</td>
				';
				foreach($arr_order_area_basic as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sv]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_price']['mobileN']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileY']) .'</td>
				';
				foreach($arr_order_area_basic as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sv]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sv]['buy_price']['mobileY']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----

			}
			echo '</table>';
			exit;
			break;
		// -------------- 지역별 주문통계 - 요일별 --------------

// -------------- 지역별 주문통계  ----------------------------





// -------------- 성별 주문통계  ----------------------------

		// -------------- 성별 주문통계 - 일자별 --------------
		case "sex_day_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_order_sex_day_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m', time()));


			echo '
				<table border=1>
					<tr>
						<td rowspan="2">날짜</td>
						<td rowspan="2">접속기기</td>
						<td colspan="3">전체</td>
			';
			foreach($arr_order_sex as $sk=>$sv) {
				echo '<td colspan="3">'. $sv .'</td>';
			}
			echo '
					</tr>
					<tr>
						<td>구매건수</td>
						<td>구매수량</td>
						<td>구매금액</td>
			';
			foreach($arr_order_sex as $sk=>$sv) {
				echo '
					<td>구매건수</td>
					<td>구매수량</td>
					<td>구매금액</td>
				';
			}
			echo '
					</tr>
			';

			$s_query = "
				where
					o.o_canceled!='Y'
					AND o.o_paystatus = 'Y'
					AND op.op_cancel = 'N'
					AND LEFT(o.o_rdate,7) = '". $pass_date ."'
			";

			$que = "
				select

					rdate, sex,

					COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
					COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

					COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


					SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
					SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

					SUM(sub_sum_buy_cnt) as sum_buy_cnt,

					SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
					SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

					SUM(sub_sum_buy_price) as sum_buy_price

				from
				(
					SELECT

						DATE(o.o_rdate) as rdate ,
						ind.in_sex as sex,
						op.op_oordernum ,

						IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
						IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,


						SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
						SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

						SUM( op.op_cnt ) as sub_sum_buy_cnt,

						SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
						SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,


						SUM( op_price * op_cnt ) as sub_sum_buy_price

					FROM smart_order_product as op
					INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )
					INNER JOIN smart_individual as ind ON (ind.in_id = o.o_mid AND IFNULL( ind.in_birth , '0000-00-00' ) != '0000-00-00' )

					" . $s_query . "

					group by op.op_oordernum

				) as tbl_view

				GROUP BY rdate, sex
				ORDER BY
					rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){

				// 성별 정리 ::: M,F가 아닐 경우 미선택
				$v['sex'] = ( IN_ARRAY($v['sex'] , array('M' , 'F'))) ? $v['sex'] : 'etc';

				// 구매건수
				$arr_res[$v['rdate']][$v['sex']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
				$arr_res[$v['rdate']][$v['sex']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
				$arr_res[$v['rdate']][$v['sex']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

				$arr_all_res[$v['rdate']]['order_cnt']['mobileY'] += $v['sum_mobileY_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['mobileN'] += $v['sum_mobileN_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;


				// 구매수량
				$arr_res[$v['rdate']][$v['sex']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
				$arr_res[$v['rdate']][$v['sex']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
				$arr_res[$v['rdate']][$v['sex']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

				$arr_all_res[$v['rdate']]['buy_cnt']['mobileY'] += $v['sum_mobileY_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['mobileN'] += $v['sum_mobileN_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;


				// 구매금액
				$arr_res[$v['rdate']][$v['sex']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
				$arr_res[$v['rdate']][$v['sex']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
				$arr_res[$v['rdate']][$v['sex']]['buy_price']['sum'] += $v['sum_buy_price'] ;

				$arr_all_res[$v['rdate']]['buy_price']['mobileY'] += $v['sum_mobileY_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['mobileN'] += $v['sum_mobileN_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

			}
			for($i=1 ; $i<=date("t" , strtotime(date("{$Select_Year}-{$Select_Month}-01"))) ; $i++){

				$app_date = $Select_Year ."-". $Select_Month ."-". sprintf("%02d" , $i);
				$app_date_key = $app_date;

				echo '
					<tr>
						<td rowspan="3">'. $app_date .'</td><!-- 날짜 -->
						<td>소계</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['sum']) .'</td>
				';
				foreach($arr_order_sex as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['sum']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileN']) .'</td>
				';
				foreach($arr_order_sex as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileN']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileY']) .'</td>
				';
				foreach($arr_order_sex as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileY']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----

			}
			echo '</table>';
			exit;
			break;
		// -------------- 연령별 주문통계 - 일자별 --------------

		// -------------- 성별 주문통계 - 월별 --------------
		case "sex_month_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_order_sex_month_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y', time()));


			echo '
				<table border=1>
					<tr>
						<td rowspan="2">년월</td>
						<td rowspan="2">접속기기</td>
						<td colspan="3">전체</td>
			';
			foreach($arr_order_sex as $sk=>$sv) {
				echo '<td colspan="3">'. $sv .'</td>';
			}
			echo '
					</tr>
					<tr>
						<td>구매건수</td>
						<td>구매수량</td>
						<td>구매금액</td>
			';
			foreach($arr_order_sex as $sk=>$sv) {
				echo '
					<td>구매건수</td>
					<td>구매수량</td>
					<td>구매금액</td>
				';
			}
			echo '
					</tr>
			';

			$s_query = "
				where
					o.o_canceled!='Y'
					AND o.o_paystatus = 'Y'
					AND op.op_cancel = 'N'
					AND LEFT(o.o_rdate,4) = '". $pass_date ."'
			";

			$que = "
				select

					rdate, sex,

					COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
					COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

					COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


					SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
					SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

					SUM(sub_sum_buy_cnt) as sum_buy_cnt,

					SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
					SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

					SUM(sub_sum_buy_price) as sum_buy_price

				from
				(
					SELECT

						LEFT(o.o_rdate,7) as rdate ,
						ind.in_sex as sex,
						op.op_oordernum ,

						IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
						IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,


						SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
						SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

						SUM( op.op_cnt ) as sub_sum_buy_cnt,

						SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
						SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,


						SUM( op_price * op_cnt ) as sub_sum_buy_price

					FROM smart_order_product as op
					INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )
					INNER JOIN smart_individual as ind ON (ind.in_id = o.o_mid AND IFNULL( ind.in_birth , '0000-00-00' ) != '0000-00-00' )

					" . $s_query . "

					group by op.op_oordernum

				) as tbl_view

				GROUP BY rdate, sex
				ORDER BY
					rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){

				// 성별 정리 ::: M,F가 아닐 경우 미선택
				$v['sex'] = ( IN_ARRAY($v['sex'] , array('M' , 'F'))) ? $v['sex'] : 'etc';

				// 구매건수
				$arr_res[$v['rdate']][$v['sex']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
				$arr_res[$v['rdate']][$v['sex']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
				$arr_res[$v['rdate']][$v['sex']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

				$arr_all_res[$v['rdate']]['order_cnt']['mobileY'] += $v['sum_mobileY_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['mobileN'] += $v['sum_mobileN_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;


				// 구매수량
				$arr_res[$v['rdate']][$v['sex']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
				$arr_res[$v['rdate']][$v['sex']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
				$arr_res[$v['rdate']][$v['sex']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

				$arr_all_res[$v['rdate']]['buy_cnt']['mobileY'] += $v['sum_mobileY_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['mobileN'] += $v['sum_mobileN_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;


				// 구매금액
				$arr_res[$v['rdate']][$v['sex']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
				$arr_res[$v['rdate']][$v['sex']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
				$arr_res[$v['rdate']][$v['sex']]['buy_price']['sum'] += $v['sum_buy_price'] ;

				$arr_all_res[$v['rdate']]['buy_price']['mobileY'] += $v['sum_mobileY_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['mobileN'] += $v['sum_mobileN_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

			}
			for($i=1 ; $i<=12 ; $i++){

				$app_date = $Select_Year ."년 ". sprintf("%02d" , $i)."월";
				$app_date_key = $Select_Year ."-". sprintf("%02d" , $i);

				echo '
					<tr>
						<td rowspan="3">'. $app_date .'</td><!-- 년월 -->
						<td>소계</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['sum']) .'</td>
				';
				foreach($arr_order_sex as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['sum']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileN']) .'</td>
				';
				foreach($arr_order_sex as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileN']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileY']) .'</td>
				';
				foreach($arr_order_sex as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileY']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----

			}
			echo '</table>';
			exit;
			break;
		// -------------- 연령별 주문통계 - 월별 --------------

		// -------------- 성별 주문통계 - 시간별 --------------
		case "sex_hour_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_order_sex_hour_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';


			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));


			echo '
				<table border=1>
					<tr>
						<td rowspan="2">시간</td>
						<td rowspan="2">접속기기</td>
						<td colspan="3">전체</td>
			';
			foreach($arr_order_sex as $sk=>$sv) {
				echo '<td colspan="3">'. $sv .'</td>';
			}
			echo '
					</tr>
					<tr>
						<td>구매건수</td>
						<td>구매수량</td>
						<td>구매금액</td>
			';
			foreach($arr_order_sex as $sk=>$sv) {
				echo '
					<td>구매건수</td>
					<td>구매수량</td>
					<td>구매금액</td>
				';
			}
			echo '
					</tr>
			';

			$s_query = "
				where
					o.o_canceled!='Y'
					AND o.o_paystatus = 'Y'
					AND op.op_cancel = 'N'
					AND DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'
			";

			$que = "
				select

					rdate, sex,

					COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
					COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

					COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


					SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
					SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

					SUM(sub_sum_buy_cnt) as sum_buy_cnt,

					SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
					SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

					SUM(sub_sum_buy_price) as sum_buy_price

				from
				(
					SELECT

						HOUR(o.o_rdate) as rdate ,
						ind.in_sex as sex,
						op.op_oordernum ,

						IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
						IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,


						SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
						SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

						SUM( op.op_cnt ) as sub_sum_buy_cnt,

						SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
						SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,


						SUM( op_price * op_cnt ) as sub_sum_buy_price

					FROM smart_order_product as op
					INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )
					INNER JOIN smart_individual as ind ON (ind.in_id = o.o_mid AND IFNULL( ind.in_birth , '0000-00-00' ) != '0000-00-00' )

					" . $s_query . "

					group by op.op_oordernum

				) as tbl_view

				GROUP BY rdate, sex
				ORDER BY
					rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){

				// 성별 정리 ::: M,F가 아닐 경우 미선택
				$v['sex'] = ( IN_ARRAY($v['sex'] , array('M' , 'F'))) ? $v['sex'] : 'etc';

				// 구매건수
				$arr_res[$v['rdate']][$v['sex']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
				$arr_res[$v['rdate']][$v['sex']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
				$arr_res[$v['rdate']][$v['sex']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

				$arr_all_res[$v['rdate']]['order_cnt']['mobileY'] += $v['sum_mobileY_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['mobileN'] += $v['sum_mobileN_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;


				// 구매수량
				$arr_res[$v['rdate']][$v['sex']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
				$arr_res[$v['rdate']][$v['sex']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
				$arr_res[$v['rdate']][$v['sex']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

				$arr_all_res[$v['rdate']]['buy_cnt']['mobileY'] += $v['sum_mobileY_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['mobileN'] += $v['sum_mobileN_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;


				// 구매금액
				$arr_res[$v['rdate']][$v['sex']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
				$arr_res[$v['rdate']][$v['sex']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
				$arr_res[$v['rdate']][$v['sex']]['buy_price']['sum'] += $v['sum_buy_price'] ;

				$arr_all_res[$v['rdate']]['buy_price']['mobileY'] += $v['sum_mobileY_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['mobileN'] += $v['sum_mobileN_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

			}
			for($i=0 ; $i<=23 ; $i++){

				$app_date = $i . "시";
				$app_date_key = $i;

				echo '
					<tr>
						<td rowspan="3">'. $app_date .'</td><!-- 시간 -->
						<td>소계</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['sum']) .'</td>
				';
				foreach($arr_order_sex as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['sum']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileN']) .'</td>
				';
				foreach($arr_order_sex as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileN']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileY']) .'</td>
				';
				foreach($arr_order_sex as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileY']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----

			}
			echo '</table>';
			exit;
			break;
		// -------------- 연령별 주문통계 - 시간별 --------------

		// -------------- 성별 주문통계 - 요일별 --------------
		case "sex_week_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_order_sex_week_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';


			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));


			echo '
				<table border=1>
					<tr>
						<td rowspan="2">요일</td>
						<td rowspan="2">접속기기</td>
						<td colspan="3">전체</td>
			';
			foreach($arr_order_sex as $sk=>$sv) {
				echo '<td colspan="3">'. $sv .'</td>';
			}
			echo '
					</tr>
					<tr>
						<td>구매건수</td>
						<td>구매수량</td>
						<td>구매금액</td>
			';
			foreach($arr_order_sex as $sk=>$sv) {
				echo '
					<td>구매건수</td>
					<td>구매수량</td>
					<td>구매금액</td>
				';
			}
			echo '
					</tr>
			';

			$s_query = "
				where
					o.o_canceled!='Y'
					AND o.o_paystatus = 'Y'
					AND op.op_cancel = 'N'
					AND DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'
			";

			$que = "
				select

					rdate, sex,

					COUNT(DISTINCT(subsum_mobileY_order_cnt)) as sum_mobileY_order_cnt,
					COUNT(DISTINCT(subsum_mobileN_order_cnt)) as sum_mobileN_order_cnt,

					COUNT(DISTINCT(op_oordernum)) as sum_order_cnt ,


					SUM(sub_sum_mobileY_buy_cnt) as sum_mobileY_buy_cnt,
					SUM(sub_sum_mobileN_buy_cnt) as sum_mobileN_buy_cnt,

					SUM(sub_sum_buy_cnt) as sum_buy_cnt,

					SUM(sub_sum_mobileY_buy_price) as sum_mobileY_buy_price,
					SUM(sub_sum_mobileN_buy_price) as sum_mobileN_buy_price,

					SUM(sub_sum_buy_price) as sum_buy_price

				from
				(
					SELECT

						DATE_FORMAT(o.o_rdate, '%w') as rdate ,
						ind.in_sex as sex,
						op.op_oordernum ,

						IF( o.mobile = 'Y' , op.op_oordernum , NULL ) as subsum_mobileY_order_cnt,
						IF( o.mobile != 'Y' , op.op_oordernum , NULL ) as subsum_mobileN_order_cnt,


						SUM(IF( o.mobile = 'Y' , op.op_cnt , 0 )) as sub_sum_mobileY_buy_cnt,
						SUM(IF( o.mobile != 'Y' , op.op_cnt , 0 )) as sub_sum_mobileN_buy_cnt,

						SUM( op.op_cnt ) as sub_sum_buy_cnt,

						SUM(IF( o.mobile = 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileY_buy_price,
						SUM(IF( o.mobile != 'Y' , op_price * op_cnt , 0 )) as sub_sum_mobileN_buy_price,


						SUM( op_price * op_cnt ) as sub_sum_buy_price

					FROM smart_order_product as op
					INNER JOIN smart_order AS o ON ( o.o_ordernum = op.op_oordernum )
					INNER JOIN smart_individual as ind ON (ind.in_id = o.o_mid AND IFNULL( ind.in_birth , '0000-00-00' ) != '0000-00-00' )

					" . $s_query . "

					group by op.op_oordernum

				) as tbl_view

				GROUP BY rdate, sex
				ORDER BY
					rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){

				// 성별 정리 ::: M,F가 아닐 경우 미선택
				$v['sex'] = ( IN_ARRAY($v['sex'] , array('M' , 'F'))) ? $v['sex'] : 'etc';

				// 구매건수
				$arr_res[$v['rdate']][$v['sex']]['order_cnt']['mobileY'] = $v['sum_mobileY_order_cnt'];
				$arr_res[$v['rdate']][$v['sex']]['order_cnt']['mobileN'] = $v['sum_mobileN_order_cnt'];
				$arr_res[$v['rdate']][$v['sex']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;

				$arr_all_res[$v['rdate']]['order_cnt']['mobileY'] += $v['sum_mobileY_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['mobileN'] += $v['sum_mobileN_order_cnt'] ;
				$arr_all_res[$v['rdate']]['order_cnt']['sum'] += $v['sum_order_cnt'] ;


				// 구매수량
				$arr_res[$v['rdate']][$v['sex']]['buy_cnt']['mobileY'] = $v['sum_mobileY_buy_cnt'];
				$arr_res[$v['rdate']][$v['sex']]['buy_cnt']['mobileN'] = $v['sum_mobileN_buy_cnt'];
				$arr_res[$v['rdate']][$v['sex']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;

				$arr_all_res[$v['rdate']]['buy_cnt']['mobileY'] += $v['sum_mobileY_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['mobileN'] += $v['sum_mobileN_buy_cnt'] ;
				$arr_all_res[$v['rdate']]['buy_cnt']['sum'] += $v['sum_buy_cnt'] ;


				// 구매금액
				$arr_res[$v['rdate']][$v['sex']]['buy_price']['mobileY'] = $v['sum_mobileY_buy_price'];
				$arr_res[$v['rdate']][$v['sex']]['buy_price']['mobileN'] = $v['sum_mobileN_buy_price'];
				$arr_res[$v['rdate']][$v['sex']]['buy_price']['sum'] += $v['sum_buy_price'] ;

				$arr_all_res[$v['rdate']]['buy_price']['mobileY'] += $v['sum_mobileY_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['mobileN'] += $v['sum_mobileN_buy_price'];
				$arr_all_res[$v['rdate']]['buy_price']['sum'] += $v['sum_buy_price'] ;

			}
			for($i=0 ; $i<=6 ; $i++){

				$app_date = week_name( $i , '요일');
				$app_date_key = $i;

				echo '
					<tr>
						<td rowspan="3">'. $app_date .'</td><!-- 요일 -->
						<td>소계</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['sum']) .'</td>
				';
				foreach($arr_order_sex as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['sum']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['sum']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileN']) .'</td>
				';
				foreach($arr_order_sex as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileN']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileN']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->

						<td>'. number_format($arr_all_res[$app_date_key]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_all_res[$app_date_key]['buy_price']['mobileY']) .'</td>
				';
				foreach($arr_order_sex as $sk=>$sv) {
					echo '
						<td>'. number_format($arr_res[$app_date_key][$sk]['order_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_cnt']['mobileY']) .'</td>
						<td>'. number_format($arr_res[$app_date_key][$sk]['buy_price']['mobileY']) .'</td>
					';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----

			}
			echo '</table>';
			exit;
			break;
		// -------------- 연령별 주문통계 - 요일별 --------------

// -------------- 성별 주문통계  ----------------------------

	}
exit;