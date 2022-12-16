<?php


	ini_set('memory_limit', '-1');
	include_once('inc.php');



	switch($_mode){



	// -------------- 매출통계  ----------------------------

		// -------------- 매출통계 - 일자별 --------------
		case "all_day_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_sale_all_day_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m', time()));


			echo '
				<table border=1>
					<tr>
						<td>날짜</td>
						<td>접속기기</td>
						<td>구매총액</td>
						<td>배송비</td>
						<td>할인액</td>
						<td>실결제액</td>
						<td>취소/환불</td>
						<td>실매출액</td>
						<td>적립금</td>
					</tr>
			';

			// JJC : 주문 할인항목 추출 : 2018-01-04
			$add_que_discount = implode(" + " , array_keys($arr_order_discount_field));

			// JJC : 주문 취소항목 추출 : 2018-01-04
			$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

			$que = "
				SELECT

					DATE(o_rdate) as rdate,


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
					LEFT(o_rdate,7) = '". $pass_date ."'

				GROUP BY rdate
				ORDER BY rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){
				foreach( $v as $sk=>$sv ){
					$arr_res[DATE("Ymd" , strtotime($v['rdate']))][$sk] = $sv;
				}
			}
			for($i=1 ; $i<=date("t" , strtotime(date("{$Select_Year}-{$Select_Month}-01"))) ; $i++){

				$app_date = $Select_Year ."-". $Select_Month ."-". sprintf("%02d" , $i);
				$app_date_key = $Select_Year . $Select_Month . sprintf("%02d" , $i);

				echo '
					<tr>
						<td rowspan="3">'. $app_date .'</td><!-- 날짜 -->
						<td>소계</td><!-- 접속기기 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_total'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_total'] * 1) .'</td><!-- 구매총액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_delivery'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_delivery'] * 1) .'</td><!-- 배송비 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_discount'] * 1 + $arr_res[$app_date_key]['mobileY_sum_discount'] * 1) .'</td><!-- 할인액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_real'] * 1) .'</td><!-- 실결제액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1) .'</td><!-- 취소/환불 -->
						<td>'. number_format(($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_real'] * 1) - ($arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1)) .'</td><!-- 실매출액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_supplypoint'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_supplypoint'] * 1) .'</td><!-- 포인트적립 -->
					</tr>

					<tr>
						<td>PC</td><!-- 접속기기 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_total'] * 1) .'</td><!-- 구매총액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_delivery'] * 1) .'</td><!-- 배송비 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_discount'] * 1) .'</td><!-- 할인액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1) .'</td><!-- 실결제액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1) .'</td><!-- 취소/환불 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1 - $arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1) .'</td><!-- 실매출액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_supplypoint'] * 1) .'</td><!-- 포인트적립 -->
					</tr>

					<tr>
						<td>모바일</td><!-- 접속기기 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_total'] * 1) .'</td><!-- 구매총액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_delivery'] * 1) .'</td><!-- 배송비 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_discount'] * 1) .'</td><!-- 할인액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_real'] * 1) .'</td><!-- 실결제액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1) .'</td><!-- 취소/환불 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_real'] * 1 - $arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1) .'</td><!-- 실매출액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_supplypoint'] * 1) .'</td><!-- 포인트적립 -->
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 매출통계 - 일자별 --------------



		// -------------- 매출통계 - 월별 --------------
		case "all_month_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_sale_all_month_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y', time()));


			echo '
				<table border=1>
					<tr>
						<td>년월</td>
						<td>접속기기</td>
						<td>구매총액</td>
						<td>배송비</td>
						<td>할인액</td>
						<td>실결제액</td>
						<td>취소/환불</td>
						<td>실매출액</td>
						<td>적립금</td>
					</tr>
			';

			// JJC : 주문 할인항목 추출 : 2018-01-04
			$add_que_discount = implode(" + " , array_keys($arr_order_discount_field));

			// JJC : 주문 취소항목 추출 : 2018-01-04
			$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

			$que = "
				SELECT

					LEFT(o_rdate,7) as rdate,


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
					LEFT(o_rdate,4) = '". $pass_date ."'

				GROUP BY rdate
				ORDER BY rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){
				foreach( $v as $sk=>$sv ){
					$arr_res[str_replace("-" , "" , $v['rdate'])][$sk] = $sv;
				}
			}
			for($i=1 ; $i<=12 ; $i++){

				$app_date = $Select_Year ."년 ". sprintf("%02d" , $i)."월";
				$app_date_key = $Select_Year . sprintf("%02d" , $i);

				echo '
					<tr>
						<td rowspan="3">'. $app_date .'</td><!-- 년월 -->
						<td>소계</td><!-- 접속기기 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_total'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_total'] * 1) .'</td><!-- 구매총액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_delivery'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_delivery'] * 1) .'</td><!-- 배송비 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_discount'] * 1 + $arr_res[$app_date_key]['mobileY_sum_discount'] * 1) .'</td><!-- 할인액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_real'] * 1) .'</td><!-- 실결제액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1) .'</td><!-- 취소/환불 -->
						<td>'. number_format(($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_real'] * 1) - ($arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1)) .'</td><!-- 실매출액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_supplypoint'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_supplypoint'] * 1) .'</td><!-- 포인트적립 -->
					</tr>

					<tr>
						<td>PC</td><!-- 접속기기 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_total'] * 1) .'</td><!-- 구매총액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_delivery'] * 1) .'</td><!-- 배송비 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_discount'] * 1) .'</td><!-- 할인액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1) .'</td><!-- 실결제액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1) .'</td><!-- 취소/환불 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1 - $arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1) .'</td><!-- 실매출액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_supplypoint'] * 1) .'</td><!-- 포인트적립 -->
					</tr>

					<tr>
						<td>모바일</td><!-- 접속기기 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_total'] * 1) .'</td><!-- 구매총액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_delivery'] * 1) .'</td><!-- 배송비 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_discount'] * 1) .'</td><!-- 할인액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_real'] * 1) .'</td><!-- 실결제액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1) .'</td><!-- 취소/환불 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_real'] * 1 - $arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1) .'</td><!-- 실매출액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_supplypoint'] * 1) .'</td><!-- 포인트적립 -->
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 매출통계 - 월별 --------------



		// -------------- 매출통계 - 시간대별 --------------
		case "all_hour_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_sale_all_hour_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));

			echo '
				<table border=1>
					<tr>
						<td>시간</td>
						<td>접속기기</td>
						<td>구매총액</td>
						<td>배송비</td>
						<td>할인액</td>
						<td>실결제액</td>
						<td>취소/환불</td>
						<td>실매출액</td>
						<td>적립금</td>
					</tr>
			';

			// JJC : 주문 할인항목 추출 : 2018-01-04
			$add_que_discount = implode(" + " , array_keys($arr_order_discount_field));

			// JJC : 주문 취소항목 추출 : 2018-01-04
			$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

			$que = "
				SELECT

					HOUR(o_rdate) as rdate,


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
			}
			for($i=0 ; $i<=23 ; $i++){
				$app_date_key = $i;

				echo '
					<tr>
						<td rowspan="3">'. $i .'시</td><!-- 시간 -->
						<td>소계</td><!-- 접속기기 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_total'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_total'] * 1) .'</td><!-- 구매총액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_delivery'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_delivery'] * 1) .'</td><!-- 배송비 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_discount'] * 1 + $arr_res[$app_date_key]['mobileY_sum_discount'] * 1) .'</td><!-- 할인액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_real'] * 1) .'</td><!-- 실결제액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1) .'</td><!-- 취소/환불 -->
						<td>'. number_format(($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_real'] * 1) - ($arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1)) .'</td><!-- 실매출액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_supplypoint'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_supplypoint'] * 1) .'</td><!-- 포인트적립 -->
					</tr>

					<tr>
						<td>PC</td><!-- 접속기기 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_total'] * 1) .'</td><!-- 구매총액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_delivery'] * 1) .'</td><!-- 배송비 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_discount'] * 1) .'</td><!-- 할인액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1) .'</td><!-- 실결제액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1) .'</td><!-- 취소/환불 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1 - $arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1) .'</td><!-- 실매출액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_supplypoint'] * 1) .'</td><!-- 포인트적립 -->
					</tr>

					<tr>
						<td>모바일</td><!-- 접속기기 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_total'] * 1) .'</td><!-- 구매총액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_delivery'] * 1) .'</td><!-- 배송비 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_discount'] * 1) .'</td><!-- 할인액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_real'] * 1) .'</td><!-- 실결제액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1) .'</td><!-- 취소/환불 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_real'] * 1 - $arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1) .'</td><!-- 실매출액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_supplypoint'] * 1) .'</td><!-- 포인트적립 -->
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 매출통계 - 시간대별 --------------



		// -------------- 매출통계 - 요일별 --------------
		case "all_week_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_sale_all_week_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));

			echo '
				<table border=1>
					<tr>
						<td>요일</td>
						<td>접속기기</td>
						<td>구매총액</td>
						<td>배송비</td>
						<td>할인액</td>
						<td>실결제액</td>
						<td>취소/환불</td>
						<td>실매출액</td>
						<td>포인트적립</td>
					</tr>
			';

			// JJC : 주문 할인항목 추출 : 2018-01-04
			$add_que_discount = implode(" + " , array_keys($arr_order_discount_field));

			// JJC : 주문 취소항목 추출 : 2018-01-04
			$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

			$que = "
				SELECT

					DATE_FORMAT(o_rdate, '%w') as rdate,


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
			}
			for($i=0 ; $i<=6 ; $i++){
				$app_date_key = $i;

				echo '
					<tr>
						<td rowspan="3">'. week_name( $i , '요일') .'</td><!-- 요일 -->
						<td>소계</td><!-- 접속기기 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_total'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_total'] * 1) .'</td><!-- 구매총액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_delivery'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_delivery'] * 1) .'</td><!-- 배송비 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_discount'] * 1 + $arr_res[$app_date_key]['mobileY_sum_discount'] * 1) .'</td><!-- 할인액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_real'] * 1) .'</td><!-- 실결제액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1) .'</td><!-- 취소/환불 -->
						<td>'. number_format(($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_real'] * 1) - ($arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1)) .'</td><!-- 실매출액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_supplypoint'] * 1 + $arr_res[$app_date_key]['mobileY_sum_price_supplypoint'] * 1) .'</td><!-- 포인트적립 -->
					</tr>

					<tr>
						<td>PC</td><!-- 접속기기 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_total'] * 1) .'</td><!-- 구매총액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_delivery'] * 1) .'</td><!-- 배송비 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_discount'] * 1) .'</td><!-- 할인액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1) .'</td><!-- 실결제액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1) .'</td><!-- 취소/환불 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_real'] * 1 - $arr_res[$app_date_key]['mobileN_sum_price_refund'] * 1) .'</td><!-- 실매출액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileN_sum_price_supplypoint'] * 1) .'</td><!-- 포인트적립 -->
					</tr>

					<tr>
						<td>모바일</td><!-- 접속기기 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_total'] * 1) .'</td><!-- 구매총액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_delivery'] * 1) .'</td><!-- 배송비 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_discount'] * 1) .'</td><!-- 할인액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_real'] * 1) .'</td><!-- 실결제액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1) .'</td><!-- 취소/환불 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_real'] * 1 - $arr_res[$app_date_key]['mobileY_sum_price_refund'] * 1) .'</td><!-- 실매출액 -->
						<td>'. number_format($arr_res[$app_date_key]['mobileY_sum_price_supplypoint'] * 1) .'</td><!-- 포인트적립 -->
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 매출통계 - 요일별 --------------

	// -------------- 매출통계  ----------------------------





	// -------------- 결제수단별 매출통계  ----------------------------

		// -------------- 결제수단별 매출통계 - 일자별 --------------
		case "method_day_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_sale_method_day_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m', time()));


			echo '
				<table border=1>
					<tr>
						<td rowspan="2">날짜</td>
						<td rowspan="2">접속기기</td>
						<td rowspan="2">실결제액</td>
			';
			foreach($arr_payment_type as $k=>$v){
				echo '<td colspan="2">'. $v .'</td>';
			}
			echo '
					</tr>
					<tr>
			';
			foreach($arr_payment_type as $k=>$v){
				echo '
					<td>금액</td>
					<td>비율</td>
				';
			}
			echo '
					</tr>
			';

			// JJC : 주문 취소항목 추출 : 2018-01-04
			$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

			$arr_res = array();
			$que = "
					SELECT

						DATE(o_rdate) as rdate,
						o_paymethod,
						IF(mobile = 'Y' , 'Y' , 'N') as mobile,
						SUM( o_price_real - (". $add_que_cancel .") ) as sum_real_price

					FROM smart_order

					WHERE
						o_paystatus = 'Y' AND
						o_canceled = 'N' AND
						LEFT(o_rdate,7) = '". $pass_date ."'

				GROUP BY rdate , o_paymethod , mobile
				ORDER BY rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){
				$arr_res[$v['rdate']][$v['mobile']][$v['o_paymethod']] = $v['sum_real_price'];
				$arr_res[$v['rdate']][$v['mobile']]['sum'] += $v['sum_real_price'];
			}
			// ------- 매출 - 결제수단 - 날짜별 목록 -------

			// ------- 매출 - 날짜별 목록 -------
			for($i=1 ; $i<=date("t" , strtotime(date("{$Select_Year}-{$Select_Month}-01"))) ; $i++){

				$app_date = $Select_Year ."-". $Select_Month ."-". sprintf("%02d" , $i);
				$app_date_key = $app_date;

				$app_total_sum = $arr_res[$app_date_key]['N']['sum'] * 1 + $arr_res[$app_date_key]['Y']['sum'] * 1 ;

				// ----- 소계 -----
				echo '
					<tr >
						<td rowspan="3">'. $app_date .'</td><!-- 날짜 -->
						<td>소계</td><!-- 접속기기 -->
						<td>' . number_format($app_total_sum) . '</td><!-- 실결제액 -->
				';
				$total_sum = $app_total_sum > 0 ? $app_total_sum : 1;
				foreach($arr_payment_type as $sk=>$sv){
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk] * 1 + $arr_res[$app_date_key]['Y'][$sk] * 1) .'</td>';
					echo '<td>'. number_format(($arr_res[$app_date_key]['N'][$sk] * 1 + $arr_res[$app_date_key]['Y'][$sk] * 1) * 100 / $total_sum , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->
						<td>' . number_format($arr_res[$app_date_key]['N']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_N = $arr_res[$app_date_key]['N']['sum'] > 0 ? $arr_res[$app_date_key]['N']['sum'] : 1;
				foreach($arr_payment_type as $sk=>$sv){
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk] * 1) .'</td>';
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk] * 100 / $total_sum_N , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->
						<td>' . number_format($arr_res[$app_date_key]['Y']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_Y = $arr_res[$app_date_key]['Y']['sum'] > 0 ? $arr_res[$app_date_key]['Y']['sum'] : 1;
				foreach($arr_payment_type as $sk=>$sv){
					echo '<td>'. number_format($arr_res[$app_date_key]['Y'][$sk] * 1) .'</td>';
					echo '<td>'. number_format($arr_res[$app_date_key]['Y'][$sk] * 100 / $total_sum_Y , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----
			}
			echo '</table>';
			exit;
			break;
		// -------------- 결제수단별 매출통계 - 일자별 --------------

		// -------------- 결제수단별 매출통계 - 월별 --------------
		case "method_month_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_sale_method_month_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y', time()));

			echo '
				<table border=1>
					<tr>
						<td rowspan="2">년월</td>
						<td rowspan="2">접속기기</td>
						<td rowspan="2">실결제액</td>
			';
			foreach($arr_payment_type as $k=>$v){
				echo '<td colspan="2">'. $v .'</td>';
			}
			echo '
					</tr>
					<tr>
			';
			foreach($arr_payment_type as $k=>$v){
				echo '
					<td>금액</td>
					<td>비율</td>
				';
			}
			echo '
					</tr>
			';

			// JJC : 주문 취소항목 추출 : 2018-01-04
			$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

			$arr_res = array();
			$que = "
					SELECT

						LEFT(o_rdate,7) as rdate,
						o_paymethod,
						IF(mobile = 'Y' , 'Y' , 'N') as mobile,
						SUM( o_price_real - (". $add_que_cancel .") ) as sum_real_price

					FROM smart_order

					WHERE
						o_paystatus = 'Y' AND
						o_canceled = 'N' AND
						LEFT(o_rdate,4) = '". $pass_date ."'

				GROUP BY rdate , o_paymethod , mobile
				ORDER BY rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){
				$arr_res[$v['rdate']][$v['mobile']][$v['o_paymethod']] = $v['sum_real_price'];
				$arr_res[$v['rdate']][$v['mobile']]['sum'] += $v['sum_real_price'];
			}
			// ------- 매출 - 결제수단 - 날짜별 목록 -------

			// ------- 매출 - 날짜별 목록 -------
			for($i=1 ; $i<=12 ; $i++){

					$app_date = $Select_Year ."년 ". sprintf("%02d" , $i)."월";
					$app_date_key = $Select_Year ."-". sprintf("%02d" , $i);

				$app_total_sum = $arr_res[$app_date_key]['N']['sum'] * 1 + $arr_res[$app_date_key]['Y']['sum'] * 1 ;

				// ----- 소계 -----
				echo '
					<tr >
						<td rowspan="3">'. $app_date .'</td><!-- 년월 -->
						<td>소계</td><!-- 접속기기 -->
						<td>' . number_format($app_total_sum) . '</td><!-- 실결제액 -->
				';
				$total_sum = $app_total_sum > 0 ? $app_total_sum : 1;
				foreach($arr_payment_type as $sk=>$sv){
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk] * 1 + $arr_res[$app_date_key]['Y'][$sk] * 1) .'</td>';
					echo '<td>'. number_format(($arr_res[$app_date_key]['N'][$sk] * 1 + $arr_res[$app_date_key]['Y'][$sk] * 1) * 100 / $total_sum , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->
						<td>' . number_format($arr_res[$app_date_key]['N']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_N = $arr_res[$app_date_key]['N']['sum'] > 0 ? $arr_res[$app_date_key]['N']['sum'] : 1;
				foreach($arr_payment_type as $sk=>$sv){
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk] * 1) .'</td>';
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk]  * 100 / $total_sum_N , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->
						<td>' . number_format($arr_res[$app_date_key]['Y']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_Y = $arr_res[$app_date_key]['Y']['sum'] > 0 ? $arr_res[$app_date_key]['Y']['sum'] : 1;
				foreach($arr_payment_type as $sk=>$sv){
					echo '<td>'. number_format($arr_res[$app_date_key]['Y'][$sk] * 1) .'</td>';
					echo '<td>'. number_format($arr_res[$app_date_key]['Y'][$sk]  * 100 / $total_sum_Y , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----
			}
			echo '</table>';
			exit;
			break;
		// -------------- 결제수단별 매출통계 - 월별 --------------

		// -------------- 결제수단별 매출통계 - 시간별 --------------
		case "method_hour_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_sale_method_hour_". $toDay .".xls");
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
						<td rowspan="2">실결제액</td>
			';
			foreach($arr_payment_type as $k=>$v){
				echo '<td colspan="2">'. $v .'</td>';
			}
			echo '
					</tr>
					<tr>
			';
			foreach($arr_payment_type as $k=>$v){
				echo '
					<td>금액</td>
					<td>비율</td>
				';
			}
			echo '
					</tr>
			';

			// JJC : 주문 취소항목 추출 : 2018-01-04
			$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

			$arr_res = array();
			$que = "
				SELECT

					HOUR(o_rdate) as rdate ,
					o_paymethod,
					IF(mobile = 'Y' , 'Y' , 'N') as mobile,
					SUM( o_price_real - (". $add_que_cancel .") ) as sum_real_price

				FROM smart_order

				WHERE
					o_paystatus = 'Y' AND
					o_canceled = 'N' AND
					DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'

				GROUP BY rdate , o_paymethod , mobile
				ORDER BY rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){
				$arr_res[$v['rdate']][$v['mobile']][$v['o_paymethod']] = $v['sum_real_price'];
				$arr_res[$v['rdate']][$v['mobile']]['sum'] += $v['sum_real_price'];
			}
			// ------- 매출 - 결제수단 - 날짜별 목록 -------

			// ------- 매출 - 날짜별 목록 -------
			for($i=0 ; $i<=23 ; $i++){

				$app_date_key = $i;

				$app_total_sum = $arr_res[$app_date_key]['N']['sum'] * 1 + $arr_res[$app_date_key]['Y']['sum'] * 1 ;
				// ----- 소계 -----
				echo '
					<tr>
						<td rowspan="3">'. $i .'시</td><!-- 시간 -->
						<td>소계</td><!-- 접속기기 -->
						<td>' . number_format($app_total_sum) . '</td><!-- 실결제액 -->
				';
				$total_sum = $app_total_sum > 0 ? $app_total_sum : 1;
				foreach($arr_payment_type as $sk=>$sv){
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk] * 1 + $arr_res[$app_date_key]['Y'][$sk] * 1) .'</td>';
					echo '<td>'. number_format(($arr_res[$app_date_key]['N'][$sk] * 1 + $arr_res[$app_date_key]['Y'][$sk] * 1) * 100 / $total_sum , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->
						<td>' . number_format($arr_res[$app_date_key]['N']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_N = $arr_res[$app_date_key]['N']['sum'] > 0 ? $arr_res[$app_date_key]['N']['sum'] : 1;
				foreach($arr_payment_type as $sk=>$sv){
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk] * 1) .'</td>';
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk]  * 100 / $total_sum_N , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->
						<td>' . number_format($arr_res[$app_date_key]['Y']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_Y = $arr_res[$app_date_key]['Y']['sum'] > 0 ? $arr_res[$app_date_key]['Y']['sum'] : 1;
				foreach($arr_payment_type as $sk=>$sv){
					echo '<td>'. number_format($arr_res[$app_date_key]['Y'][$sk] * 1) .'</td>';
					echo '<td>'. number_format($arr_res[$app_date_key]['Y'][$sk]  * 100 / $total_sum_Y , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----

			}

			echo '</table>';
			exit;
			break;
		// -------------- 결제수단별 매출통계 - 시간별 --------------

		// -------------- 결제수단별 매출통계 - 요일별 --------------
		case "method_week_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_sale_method_week_". $toDay .".xls");
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
						<td rowspan="2">실결제액</td>
			';
			foreach($arr_payment_type as $k=>$v){
				echo '<td colspan="2">'. $v .'</td>';
			}
			echo '
					</tr>
					<tr>
			';
			foreach($arr_payment_type as $k=>$v){
				echo '
					<td>금액</td>
					<td>비율</td>
				';
			}
			echo '
					</tr>
			';

			// JJC : 주문 취소항목 추출 : 2018-01-04
			$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

			$arr_res = array();
			$que = "
					SELECT

						DATE_FORMAT(o_rdate, '%w') as rdate ,
						o_paymethod,
						IF(mobile = 'Y' , 'Y' , 'N') as mobile,
						SUM( o_price_real - (". $add_que_cancel .") ) as sum_real_price

					FROM smart_order

					WHERE
						o_paystatus = 'Y' AND
						o_canceled = 'N' AND
						DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'

				GROUP BY rdate , o_paymethod , mobile
				ORDER BY rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){
				$arr_res[$v['rdate']][$v['mobile']][$v['o_paymethod']] = $v['sum_real_price'];
				$arr_res[$v['rdate']][$v['mobile']]['sum'] += $v['sum_real_price'];
			}
			// ------- 매출 - 결제수단 - 날짜별 목록 -------

			// ------- 매출 - 날짜별 목록 -------
			for($i=0 ; $i<=6 ; $i++){

				$app_date_key = $i;

				$app_total_sum = $arr_res[$app_date_key]['N']['sum'] * 1 + $arr_res[$app_date_key]['Y']['sum'] * 1 ;

				echo '
					<tr>
						<td rowspan="3">'. week_name( $i , '요일') .'</td><!-- 요일 -->
						<td>소계</td><!-- 접속기기 -->
						<td>' . number_format($app_total_sum) . '</td><!-- 실결제액 -->
				';
				$total_sum = $app_total_sum > 0 ? $app_total_sum : 1;
				foreach($arr_payment_type as $sk=>$sv){
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk] * 1 + $arr_res[$app_date_key]['Y'][$sk] * 1) .'</td>';
					echo '<td>'. number_format(($arr_res[$app_date_key]['N'][$sk] * 1 + $arr_res[$app_date_key]['Y'][$sk] * 1) * 100 / $total_sum , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->
						<td>' . number_format($arr_res[$app_date_key]['N']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_N = $arr_res[$app_date_key]['N']['sum'] > 0 ? $arr_res[$app_date_key]['N']['sum'] : 1;
				foreach($arr_payment_type as $sk=>$sv){
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk] * 1) .'</td>';
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk]  * 100 / $total_sum_N , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->
						<td>' . number_format($arr_res[$app_date_key]['Y']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_Y = $arr_res[$app_date_key]['Y']['sum'] > 0 ? $arr_res[$app_date_key]['Y']['sum'] : 1;
				foreach($arr_payment_type as $sk=>$sv){
					echo '<td>'. number_format($arr_res[$app_date_key]['Y'][$sk] * 1) .'</td>';
					echo '<td>'. number_format($arr_res[$app_date_key]['Y'][$sk]  * 100 / $total_sum_Y , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----
			}

			echo '</table>';
			exit;
			break;
		// -------------- 결제수단별 매출통계 - 요일별 --------------

// -------------- 결제수단별 매출통계  ----------------------------






// -------------- 연령별 매출통계  ----------------------------

		// -------------- 연력별 매출통계 - 일자별 --------------
		case "age_day_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_sale_age_day_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m', time()));


			echo '
				<table border=1>
					<tr>
						<td rowspan="2">날짜</td>
						<td rowspan="2">접속기기</td>
						<td rowspan="2">실결제액</td>
			';
			foreach($arr_order_age as $sk=>$sv) {
				echo '<td colspan="2">'. $sv .'</td>';
			}
			echo '
					</tr>
					<tr>
			';
			foreach($arr_order_age as $sk=>$sv) {
				echo '
					<td>금액</td>
					<td>비율</td>
				';
			}
			echo '
					</tr>
			';

			// JJC : 주문 취소항목 추출 : 2018-01-04
			$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

			$arr_res = array();
			$que = "
					SELECT

						DATE(o_rdate) as rdate,
						TRUNCATE( (YEAR( CURDATE( ) ) - YEAR( ind.in_birth ) ) /10, 0 ) *10 AS age,
						IF(mobile = 'Y' , 'Y' , 'N') as mobile,
						SUM( o_price_real - (". $add_que_cancel .") ) as sum_real_price

					FROM smart_order as o
					INNER JOIN smart_individual as ind ON (ind.in_id = o.o_mid AND ind.in_sleep_type = 'N' AND ind.in_out = 'N' AND IFNULL( ind.in_birth , '0000-00-00' ) != '0000-00-00' )

					WHERE
						o_memtype = 'Y' AND
						o_paystatus = 'Y' AND
						o_canceled = 'N' AND
						LEFT(o_rdate,7) = '". $pass_date ."'

				GROUP BY rdate , age , mobile
				ORDER BY rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){
				// 연령대 정리 ::: 10대미만, 70대 초과일 경우 기타
				$v['age'] = ($v['age'] > 70 || $v['age'] < 10 ) ? 'etc' : $v['age'];

				$arr_res[$v['rdate']][$v['mobile']][$v['age']] = $v['sum_real_price'];
				$arr_res[$v['rdate']][$v['mobile']]['sum'] += $v['sum_real_price'];
			}
			// ------- 매출 - 연령뵬 - 날짜별 목록 -------

			for($i=1 ; $i<=date("t" , strtotime(date("{$Select_Year}-{$Select_Month}-01"))) ; $i++){

				$app_date = $Select_Year ."-". $Select_Month ."-". sprintf("%02d" , $i);
				$app_date_key = $app_date;

				$app_total_sum = $arr_res[$app_date_key]['N']['sum'] * 1 + $arr_res[$app_date_key]['Y']['sum'] * 1 ;

				// ----- 소계 -----
				echo '
					<tr >
						<td rowspan="3">'. $app_date .'</td><!-- 날짜 -->
						<td>소계</td><!-- 접속기기 -->
						<td>' . number_format($app_total_sum) . '</td><!-- 실결제액 -->
				';
				$total_sum = $app_total_sum > 0 ? $app_total_sum : 1;
				foreach($arr_order_age as $sk=>$sv) {
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk] * 1 + $arr_res[$app_date_key]['Y'][$sk] * 1) .'</td>';
					echo '<td>'. number_format(($arr_res[$app_date_key]['N'][$sk] * 1 + $arr_res[$app_date_key]['Y'][$sk] * 1) * 100 / $total_sum , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->
						<td>' . number_format($arr_res[$app_date_key]['N']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_N = $arr_res[$app_date_key]['N']['sum'] > 0 ? $arr_res[$app_date_key]['N']['sum'] : 1;
				foreach($arr_order_age as $sk=>$sv) {
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk] * 1) .'</td>';
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk]  * 100 / $total_sum_N , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->
						<td>' . number_format($arr_res[$app_date_key]['Y']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_Y = $arr_res[$app_date_key]['Y']['sum'] > 0 ? $arr_res[$app_date_key]['Y']['sum'] : 1;
				foreach($arr_order_age as $sk=>$sv) {
					echo '<td>'. number_format($arr_res[$app_date_key]['Y'][$sk] * 1) .'</td>';
					echo '<td>'. number_format($arr_res[$app_date_key]['Y'][$sk]  * 100 / $total_sum_Y , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----

			}
			echo '</table>';
			exit;
			break;
		// -------------- 연력별 매출통계 - 일자별 --------------

		// -------------- 연력별 매출통계 - 월별 --------------
		case "age_month_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_sale_age_month_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y', time()));


			echo '
				<table border=1>
					<tr>
						<td rowspan="2">년월</td>
						<td rowspan="2">접속기기</td>
						<td rowspan="2">실결제액</td>
			';
			foreach($arr_order_age as $sk=>$sv) {
				echo '<td colspan="2">'. $sv .'</td>';
			}
			echo '
					</tr>
					<tr>
			';
			foreach($arr_order_age as $sk=>$sv) {
				echo '
					<td>금액</td>
					<td>비율</td>
				';
			}
			echo '
					</tr>
			';

			// JJC : 주문 취소항목 추출 : 2018-01-04
			$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

			$arr_res = array();
			$que = "
					SELECT

						LEFT(o_rdate,7) as rdate,
						TRUNCATE( (YEAR( CURDATE( ) ) - YEAR( ind.in_birth ) ) /10, 0 ) *10 AS age,
						IF(mobile = 'Y' , 'Y' , 'N') as mobile,
						SUM( o_price_real - (". $add_que_cancel .") ) as sum_real_price

					FROM smart_order as o
					INNER JOIN smart_individual as ind ON (ind.in_id = o.o_mid AND ind.in_sleep_type = 'N' AND ind.in_out = 'N' AND IFNULL( ind.in_birth , '0000-00-00' ) != '0000-00-00' )

					WHERE
						o_memtype = 'Y' AND
						o_paystatus = 'Y' AND
						o_canceled = 'N' AND
						LEFT(o_rdate,4) = '". $pass_date ."'

				GROUP BY rdate , age , mobile
				ORDER BY rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){
				// 연령대 정리 ::: 10대미만, 70대 초과일 경우 기타
				$v['age'] = ($v['age'] > 70 || $v['age'] < 10 ) ? 'etc' : $v['age'];

				$arr_res[$v['rdate']][$v['mobile']][$v['age']] = $v['sum_real_price'];
				$arr_res[$v['rdate']][$v['mobile']]['sum'] += $v['sum_real_price'];
			}
			// ------- 매출 - 연령뵬 - 날짜별 목록 -------

			for($i=1 ; $i<=12 ; $i++){

				$app_date = $Select_Year ."년 ". sprintf("%02d" , $i)."월";
				$app_date_key = $Select_Year ."-". sprintf("%02d" , $i);

				$app_total_sum = $arr_res[$app_date_key]['N']['sum'] * 1 + $arr_res[$app_date_key]['Y']['sum'] * 1 ;

				// ----- 소계 -----
				echo '
					<tr >
						<td rowspan="3">'. $app_date .'</td><!-- 년월 -->
						<td>소계</td><!-- 접속기기 -->
						<td>' . number_format($app_total_sum) . '</td><!-- 실결제액 -->
				';
				$total_sum = $app_total_sum > 0 ? $app_total_sum : 1;
				foreach($arr_order_age as $sk=>$sv) {
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk] * 1 + $arr_res[$app_date_key]['Y'][$sk] * 1) .'</td>';
					echo '<td>'. number_format(($arr_res[$app_date_key]['N'][$sk] * 1 + $arr_res[$app_date_key]['Y'][$sk] * 1) * 100 / $total_sum , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->
						<td>' . number_format($arr_res[$app_date_key]['N']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_N = $arr_res[$app_date_key]['N']['sum'] > 0 ? $arr_res[$app_date_key]['N']['sum'] : 1;
				foreach($arr_order_age as $sk=>$sv) {
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk] * 1) .'</td>';
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk]  * 100 / $total_sum_N  , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->
						<td>' . number_format($arr_res[$app_date_key]['Y']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_Y = $arr_res[$app_date_key]['Y']['sum'] > 0 ? $arr_res[$app_date_key]['Y']['sum'] : 1;
				foreach($arr_order_age as $sk=>$sv) {
					echo '<td>'. number_format($arr_res[$app_date_key]['Y'][$sk] * 1) .'</td>';
					echo '<td>'. number_format($arr_res[$app_date_key]['Y'][$sk]  * 100 / $total_sum_Y , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----

			}
			echo '</table>';
			exit;
			break;
		// -------------- 연력별 매출통계 - 월별 --------------

		// -------------- 연력별 매출통계 - 시간별 --------------
		case "age_hour_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_sale_age_hour_". $toDay .".xls");
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
						<td rowspan="2">실결제액</td>
			';
			foreach($arr_order_age as $sk=>$sv) {
				echo '<td colspan="2">'. $sv .'</td>';
			}
			echo '
					</tr>
					<tr>
			';
			foreach($arr_order_age as $sk=>$sv) {
				echo '
					<td>금액</td>
					<td>비율</td>
				';
			}
			echo '
					</tr>
			';

			// JJC : 주문 취소항목 추출 : 2018-01-04
			$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

			$arr_res = array();
			$que = "
					SELECT

						HOUR(o_rdate) as rdate ,
						TRUNCATE( (YEAR( CURDATE( ) ) - YEAR( ind.in_birth ) ) /10, 0 ) *10 AS age,
						IF(mobile = 'Y' , 'Y' , 'N') as mobile,
						SUM( o_price_real - (". $add_que_cancel .") ) as sum_real_price

					FROM smart_order as o
					INNER JOIN smart_individual as ind ON (ind.in_id = o.o_mid AND IFNULL( ind.in_birth , '0000-00-00' ) != '0000-00-00' )

					WHERE
						o_memtype = 'Y' AND
						o_paystatus = 'Y' AND
						o_canceled = 'N' AND
						DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'

				GROUP BY rdate , age , mobile
				ORDER BY rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){
				// 연령대 정리 ::: 10대미만, 70대 초과일 경우 기타
				$v['age'] = ($v['age'] > 70 || $v['age'] < 10 ) ? 'etc' : $v['age'];

				$arr_res[$v['rdate']][$v['mobile']][$v['age']] = $v['sum_real_price'];
				$arr_res[$v['rdate']][$v['mobile']]['sum'] += $v['sum_real_price'];
			}
			// ------- 매출 - 연령뵬 - 날짜별 목록 -------

			// ------- 매출 - 날짜별 목록 -------
			for($i=0 ; $i<=23 ; $i++){

				$app_date_key = $i;

				$app_total_sum = $arr_res[$app_date_key]['N']['sum'] * 1 + $arr_res[$app_date_key]['Y']['sum'] * 1 ;

				// ----- 소계 -----
				echo '
					<tr >
						<td rowspan="3">'. $i .'시</td><!-- 시간 -->
						<td>소계</td><!-- 접속기기 -->
						<td>' . number_format($app_total_sum) . '</td><!-- 실결제액 -->
				';
				$total_sum = $app_total_sum > 0 ? $app_total_sum : 1;
				foreach($arr_order_age as $sk=>$sv) {
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk] * 1 + $arr_res[$app_date_key]['Y'][$sk] * 1) .'</td>';
					echo '<td>'. number_format(($arr_res[$app_date_key]['N'][$sk] * 1 + $arr_res[$app_date_key]['Y'][$sk] * 1) * 100 / $total_sum , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->
						<td>' . number_format($arr_res[$app_date_key]['N']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_N = $arr_res[$app_date_key]['N']['sum'] > 0 ? $arr_res[$app_date_key]['N']['sum'] : 1;
				foreach($arr_order_age as $sk=>$sv) {
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk] * 1) .'</td>';
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk]  * 100 / $total_sum_N , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->
						<td>' . number_format($arr_res[$app_date_key]['Y']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_Y = $arr_res[$app_date_key]['Y']['sum'] > 0 ? $arr_res[$app_date_key]['Y']['sum'] : 1;
				foreach($arr_order_age as $sk=>$sv) {
					echo '<td>'. number_format($arr_res[$app_date_key]['Y'][$sk] * 1) .'</td>';
					echo '<td>'. number_format($arr_res[$app_date_key]['Y'][$sk]  * 100 / $total_sum_Y , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----

			}
			echo '</table>';
			exit;
			break;
		// -------------- 연력별 매출통계 - 시간별 --------------

		// -------------- 연력별 매출통계 - 요일별 --------------
		case "age_week_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_sale_age_week_". $toDay .".xls");
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
						<td rowspan="2">실결제액</td>
			';
			foreach($arr_order_age as $sk=>$sv) {
				echo '<td colspan="2">'. $sv .'</td>';
			}
			echo '
					</tr>
					<tr>
			';
			foreach($arr_order_age as $sk=>$sv) {
				echo '
					<td>금액</td>
					<td>비율</td>
				';
			}
			echo '
					</tr>
			';

			// JJC : 주문 취소항목 추출 : 2018-01-04
			$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

			$arr_res = array();
			$que = "
					SELECT

						DATE_FORMAT(o_rdate, '%w') as rdate ,
						TRUNCATE( (YEAR( CURDATE( ) ) - YEAR( ind.in_birth ) ) /10, 0 ) *10 AS age,
						IF(mobile = 'Y' , 'Y' , 'N') as mobile,
						SUM( o_price_real - (". $add_que_cancel .") ) as sum_real_price

					FROM smart_order as o
					INNER JOIN smart_individual as ind ON (ind.in_id = o.o_mid AND IFNULL( ind.in_birth , '0000-00-00' ) != '0000-00-00' )

					WHERE
						o_memtype = 'Y' AND
						o_paystatus = 'Y' AND
						o_canceled = 'N' AND
						DATE(o_rdate) between '". $pass_date ."' and '". $pass_edate ."'

				GROUP BY rdate , age , mobile
				ORDER BY rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){
				// 연령대 정리 ::: 10대미만, 70대 초과일 경우 기타
				$v['age'] = ($v['age'] > 70 || $v['age'] < 10 ) ? 'etc' : $v['age'];

				$arr_res[$v['rdate']][$v['mobile']][$v['age']] = $v['sum_real_price'];
				$arr_res[$v['rdate']][$v['mobile']]['sum'] += $v['sum_real_price'];
			}
			// ------- 매출 - 연령뵬 - 날짜별 목록 -------

			// ------- 매출 - 날짜별 목록 -------
			for($i=0 ; $i<=6 ; $i++){

				$app_date_key = $i;

				$app_total_sum = $arr_res[$app_date_key]['N']['sum'] * 1 + $arr_res[$app_date_key]['Y']['sum'] * 1 ;

				// ----- 소계 -----
				echo '
					<tr >
						<td rowspan="3">'. week_name( $i , '요일') .'</td><!-- 요일 -->
						<td>소계</td><!-- 접속기기 -->
						<td>' . number_format($app_total_sum) . '</td><!-- 실결제액 -->
				';
				$total_sum = $app_total_sum > 0 ? $app_total_sum : 1;
				foreach($arr_order_age as $sk=>$sv) {
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk] * 1 + $arr_res[$app_date_key]['Y'][$sk] * 1) .'</td>';
					echo '<td>'. number_format(($arr_res[$app_date_key]['N'][$sk] * 1 + $arr_res[$app_date_key]['Y'][$sk] * 1) * 100 / $total_sum , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- 소계 -----

				// ----- PC -----
				echo '
					<tr>
						<td>PC</td><!-- 접속기기 -->
						<td>' . number_format($arr_res[$app_date_key]['N']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_N = $arr_res[$app_date_key]['N']['sum'] > 0 ? $arr_res[$app_date_key]['N']['sum'] : 1;
				foreach($arr_order_age as $sk=>$sv) {
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk] * 1) .'</td>';
					echo '<td>'. number_format($arr_res[$app_date_key]['N'][$sk]  * 100 / $total_sum_N , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- PC -----

				// ----- 모바일 -----
				echo '
					<tr>
						<td>모바일</td><!-- 접속기기 -->
						<td>' . number_format($arr_res[$app_date_key]['Y']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_Y = $arr_res[$app_date_key]['Y']['sum'] > 0 ? $arr_res[$app_date_key]['Y']['sum'] : 1;
				foreach($arr_order_age as $sk=>$sv) {
					echo '<td>'. number_format($arr_res[$app_date_key]['Y'][$sk] * 1) .'</td>';
					echo '<td>'. number_format($arr_res[$app_date_key]['Y'][$sk]  * 100 / $total_sum_Y , 2) . '%</td>';
				}
				echo '
					</tr>
				';
				// ----- 모바일 -----

			}
			echo '</table>';
			exit;
			break;
		// -------------- 연령별 매출통계 - 요일별 --------------

// -------------- 연령별 매출통계  ----------------------------






// -------------- 지역별 매출통계  ----------------------------

		// -------------- 지역별 매출통계 - 일자별 --------------
		case "area_day_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_sale_area_day_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m', time()));


			echo '
				<table border=1>
					<tr>
						<td rowspan="2">날짜</td>
						<td rowspan="2">접속기기</td>
						<td rowspan="2">실결제액</td>
			';
			foreach($arr_order_area_basic as $k=>$v){
				echo '<td colspan="2">'. $v .'</td>';
			}
			echo '
					</tr>
					<tr>
			';
			foreach($arr_order_area_basic as $k=>$v){
				echo '
					<td>금액</td>
					<td>비율</td>
				';
			}
			echo '
					</tr>
			';
			// JJC : 주문 취소항목 추출 : 2018-01-04
			$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

			$arr_res = array();
			$que = "
					SELECT

						DATE(o_rdate) as rdate,
						o.o_area,
						IF(mobile = 'Y' , 'Y' , 'N') as mobile,
						SUM( o_price_real - (". $add_que_cancel .") ) as sum_real_price

					FROM smart_order as o

					WHERE
						o_paystatus = 'Y' AND
						o_canceled = 'N' AND
						LEFT(o_rdate,7) = '". $pass_date ."'

				GROUP BY rdate , o_area , mobile
				ORDER BY rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){
				$arr_res[$v['rdate']][$v['mobile']][$v['o_area']] = $v['sum_real_price'];
				$arr_res[$v['rdate']][$v['mobile']]['sum'] += $v['sum_real_price'];
			}
			// ------- 매출 - 결제수단 - 날짜별 목록 -------

			// ------- 매출 - 날짜별 목록 -------
			for($i=1 ; $i<=date("t" , strtotime(date("{$Select_Year}-{$Select_Month}-01"))) ; $i++){

				$app_date = $Select_Year ."-". $Select_Month ."-". sprintf("%02d" , $i);
				$app_date_key = $app_date;

				$app_total_sum = $arr_res[$app_date_key]['N']['sum'] * 1 + $arr_res[$app_date_key]['Y']['sum'] * 1 ;

				// ----- 소계 -----
				echo '
					<tr >
						<td rowspan="3">'. $app_date .'</td><!-- 날짜 -->
						<td>소계</td><!-- 접속기기 -->
						<td>' . number_format($app_total_sum) . '</td><!-- 실결제액 -->
				';
				$total_sum = $app_total_sum > 0 ? $app_total_sum : 1;
				foreach($arr_order_area_basic as $sk=>$sv){
					echo '
						<td>'. number_format($arr_res[$app_date_key]['N'][$sv] * 1 + $arr_res[$app_date_key]['Y'][$sv] * 1) .'</td>
						<td>'. number_format(($arr_res[$app_date_key]['N'][$sv] * 1 + $arr_res[$app_date_key]['Y'][$sv] * 1) * 100 / $total_sum , 2) . '%</td>
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
						<td>' . number_format($arr_res[$app_date_key]['N']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_N = $arr_res[$app_date_key]['N']['sum'] > 0 ? $arr_res[$app_date_key]['N']['sum'] : 1;
				foreach($arr_order_area_basic as $sk=>$sv){
					echo '
						<td>'. number_format($arr_res[$app_date_key]['N'][$sv] * 1) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['N'][$sv] * 100 / $total_sum_N , 2) . '%</td>
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
						<td>' . number_format($arr_res[$app_date_key]['Y']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_Y = $arr_res[$app_date_key]['Y']['sum'] > 0 ? $arr_res[$app_date_key]['Y']['sum'] : 1;
				foreach($arr_order_area_basic as $sk=>$sv){
					echo '
						<td>'. number_format($arr_res[$app_date_key]['Y'][$sv] * 1) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['Y'][$sv] * 100 / $total_sum_Y , 2) . '%</td>
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
		// -------------- 지역별 매출통계 - 일자별 --------------

		// -------------- 지역별 매출통계 - 월별 --------------
		case "area_month_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_sale_area_month_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y', time()));

			echo '
				<table border=1>
					<tr>
						<td rowspan="2">년월</td>
						<td rowspan="2">접속기기</td>
						<td rowspan="2">실결제액</td>
			';
			foreach($arr_order_area_basic as $k=>$v){
				echo '<td colspan="2">'. $v .'</td>';
			}
			echo '
					</tr>
					<tr>
			';
			foreach($arr_order_area_basic as $k=>$v){
				echo '
					<td>금액</td>
					<td>비율</td>
				';
			}
			echo '
					</tr>
			';

			// JJC : 주문 취소항목 추출 : 2018-01-04
			$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

			$arr_res = array();
			$que = "
					SELECT

						LEFT(o_rdate,7) as rdate,
						o.o_area,
						IF(mobile = 'Y' , 'Y' , 'N') as mobile,
						SUM( o_price_real - (". $add_que_cancel .") ) as sum_real_price

					FROM smart_order as o

					WHERE
						o_paystatus = 'Y' AND
						o_canceled = 'N' AND
						LEFT(o_rdate,4) = '". $pass_date ."'

				GROUP BY rdate , o_area , mobile
				ORDER BY rdate ASC
			";
			$res = _MQ_assoc($que);
			foreach( $res as $k=>$v ){
				$arr_res[$v['rdate']][$v['mobile']][$v['o_area']] = $v['sum_real_price'];
				$arr_res[$v['rdate']][$v['mobile']]['sum'] += $v['sum_real_price'];
			}
			// ------- 매출 - 결제수단 - 월별 목록 -------

			// ------- 매출 - 월별 목록 -------
			for($i=1 ; $i<=12 ; $i++){

				$app_date = $Select_Year ."년 ". sprintf("%02d" , $i)."월";
				$app_date_key = $Select_Year ."-". sprintf("%02d" , $i);

				$app_total_sum = $arr_res[$app_date_key]['N']['sum'] * 1 + $arr_res[$app_date_key]['Y']['sum'] * 1 ;

				// ----- 소계 -----
				echo '
					<tr >
						<td rowspan="3">'. $app_date .'</td><!-- 년월 -->
						<td>소계</td><!-- 접속기기 -->
						<td>' . number_format($app_total_sum) . '</td><!-- 실결제액 -->
				';
				$total_sum = $app_total_sum > 0 ? $app_total_sum : 1;
				foreach($arr_order_area_basic as $sk=>$sv){
					echo '
						<td>'. number_format($arr_res[$app_date_key]['N'][$sv] * 1 + $arr_res[$app_date_key]['Y'][$sv] * 1) .'</td>
						<td>'. number_format(($arr_res[$app_date_key]['N'][$sv] * 1 + $arr_res[$app_date_key]['Y'][$sv] * 1) * 100 / $total_sum , 2) . '%</td>
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
						<td>' . number_format($arr_res[$app_date_key]['N']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_N = $arr_res[$app_date_key]['N']['sum'] > 0 ? $arr_res[$app_date_key]['N']['sum'] : 1;
				foreach($arr_order_area_basic as $sk=>$sv){
					echo '
						<td>'. number_format($arr_res[$app_date_key]['N'][$sv] * 1) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['N'][$sv] * 100 / $total_sum_N , 2) . '%</td>
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
						<td>' . number_format($arr_res[$app_date_key]['Y']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_Y = $arr_res[$app_date_key]['Y']['sum'] > 0 ? $arr_res[$app_date_key]['Y']['sum'] : 1;
				foreach($arr_order_area_basic as $sk=>$sv){
					echo '
						<td>'. number_format($arr_res[$app_date_key]['Y'][$sv] * 1) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['Y'][$sv] * 100 / $total_sum_Y , 2) . '%</td>
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
		// -------------- 지역별 매출통계 - 월별 --------------

		// -------------- 지역별 매출통계 - 시간별 --------------
		case "area_hour_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_sale_area_hour_". $toDay .".xls");
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
						<td rowspan="2">실결제액</td>
			';
			foreach($arr_order_area_basic as $k=>$v){
				echo '<td colspan="2">'. $v .'</td>';
			}
			echo '
					</tr>
					<tr>
			';
			foreach($arr_order_area_basic as $k=>$v){
				echo '
					<td>금액</td>
					<td>비율</td>
				';
			}
			echo '
					</tr>
			';

			// JJC : 주문 취소항목 추출 : 2018-01-04
			$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

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
			// ------- 매출 - 결제수단 - 시간별 목록 -------

			// ------- 매출 - 시간별 목록 -------
			for($i=0 ; $i<=23 ; $i++){

				$app_date_key = $i;

				$app_total_sum = $arr_res[$app_date_key]['N']['sum'] * 1 + $arr_res[$app_date_key]['Y']['sum'] * 1 ;

				// ----- 소계 -----
				echo '
					<tr >
						<td rowspan="3">'. $i .'시</td><!-- 시간 -->
						<td>소계</td><!-- 접속기기 -->
						<td>' . number_format($app_total_sum) . '</td><!-- 실결제액 -->
				';
				$total_sum = $app_total_sum > 0 ? $app_total_sum : 1;
				foreach($arr_order_area_basic as $sk=>$sv){
					echo '
						<td>'. number_format($arr_res[$app_date_key]['N'][$sv] * 1 + $arr_res[$app_date_key]['Y'][$sv] * 1) .'</td>
						<td>'. number_format(($arr_res[$app_date_key]['N'][$sv] * 1 + $arr_res[$app_date_key]['Y'][$sv] * 1) * 100 / $total_sum , 2) . '%</td>
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
						<td>' . number_format($arr_res[$app_date_key]['N']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_N = $arr_res[$app_date_key]['N']['sum'] > 0 ? $arr_res[$app_date_key]['N']['sum'] : 1;
				foreach($arr_order_area_basic as $sk=>$sv){
					echo '
						<td>'. number_format($arr_res[$app_date_key]['N'][$sv] * 1) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['N'][$sv] * 100 / $total_sum_N , 2) . '%</td>
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
						<td>' . number_format($arr_res[$app_date_key]['Y']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_Y = $arr_res[$app_date_key]['Y']['sum'] > 0 ? $arr_res[$app_date_key]['Y']['sum'] : 1;
				foreach($arr_order_area_basic as $sk=>$sv){
					echo '
						<td>'. number_format($arr_res[$app_date_key]['Y'][$sv] * 1) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['Y'][$sv] * 100 / $total_sum_Y , 2) . '%</td>
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
		// -------------- 지역별 매출통계 - 시간별 --------------

		// -------------- 지역별 매출통계 - 요일별 --------------
		case "area_week_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_sale_area_week_". $toDay .".xls");
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
						<td rowspan="2">실결제액</td>
			';
			foreach($arr_order_area_basic as $k=>$v){
				echo '<td colspan="2">'. $v .'</td>';
			}
			echo '
					</tr>
					<tr>
			';
			foreach($arr_order_area_basic as $k=>$v){
				echo '
					<td>금액</td>
					<td>비율</td>
				';
			}
			echo '
					</tr>
			';

			// JJC : 주문 취소항목 추출 : 2018-01-04
			$add_que_cancel = implode(" + " , array_keys($arr_order_cancel_field));

			$arr_res = array();
			$que = "
					SELECT

						DATE_FORMAT(o_rdate, '%w') as rdate ,
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
			// ------- 매출 - 요일별 목록 -------

			// ------- 매출 - 요일별 목록 -------
			for($i=0 ; $i<=6 ; $i++){

				$app_date_key = $i;

				$app_total_sum = $arr_res[$app_date_key]['N']['sum'] * 1 + $arr_res[$app_date_key]['Y']['sum'] * 1 ;

				// ----- 소계 -----
				echo '
					<tr >
						<td rowspan="3">'. week_name( $i , '요일') .'</td><!-- 요일 -->
						<td>소계</td><!-- 접속기기 -->
						<td>' . number_format($app_total_sum) . '</td><!-- 실결제액 -->
				';
				$total_sum = $app_total_sum > 0 ? $app_total_sum : 1;
				foreach($arr_order_area_basic as $sk=>$sv){
					echo '
						<td>'. number_format($arr_res[$app_date_key]['N'][$sv] * 1 + $arr_res[$app_date_key]['Y'][$sv] * 1) .'</td>
						<td>'. number_format(($arr_res[$app_date_key]['N'][$sv] * 1 + $arr_res[$app_date_key]['Y'][$sv] * 1) * 100 / $total_sum , 2) . '%</td>
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
						<td>' . number_format($arr_res[$app_date_key]['N']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_N = $arr_res[$app_date_key]['N']['sum'] > 0 ? $arr_res[$app_date_key]['N']['sum'] : 1;
				foreach($arr_order_area_basic as $sk=>$sv){
					echo '
						<td>'. number_format($arr_res[$app_date_key]['N'][$sv] * 1) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['N'][$sv] * 100 / $total_sum_N , 2) . '%</td>
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
						<td>' . number_format($arr_res[$app_date_key]['Y']['sum'] * 1) . '</td><!-- 실결제액 -->
				';
				$total_sum_Y = $arr_res[$app_date_key]['Y']['sum'] > 0 ? $arr_res[$app_date_key]['Y']['sum'] : 1;
				foreach($arr_order_area_basic as $sk=>$sv){
					echo '
						<td>'. number_format($arr_res[$app_date_key]['Y'][$sv] * 1) .'</td>
						<td>'. number_format($arr_res[$app_date_key]['Y'][$sv] * 100 / $total_sum_Y , 2) . '%</td>
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
		// -------------- 지역별 매출통계 - 시간별 --------------

// -------------- 지역별 매출통계  ----------------------------



	}
exit;