<?php

	ini_set('memory_limit', '-1');
	include_once('inc.php');



	switch($_mode){



	// -------------- 방문자분석   ----------------------------

		// -------------- 일자별 --------------
		case "cntlog_day_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=CntlogDay". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';


			$pass_sdate = $pass_sdate ? $pass_sdate : date('Y-m');

			$s_query = " where 1 ";
			$s_query .= " and LEFT(sc_date , 7) = '". $pass_sdate ."' ";

			$res_date = _MQ_assoc("
				SELECT
					DATE(sc_date) as rdate ,
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

			echo '
				<table border=1>
					<tr>
						<td rowspan="3">날짜</td>
						<td rowspan="3">총 방문수</td>
						<td colspan="4">접속기기 구분</td>
						<td colspan="4">회원구분</td>
					</tr>
					<tr>
						<td colspan="2">PC</td>
						<td colspan="2">MOBILE</td>
						<td colspan="2">회원</td>
						<td colspan="2">비회원</td>
					</tr>
					<tr>
						<td >건수</td>
						<td >비율</td>
						<td >건수</td>
						<td >비율</td>
						<td >건수</td>
						<td >비율</td>
						<td >건수</td>
						<td >비율</td>
					</tr>
			';
			foreach( $res_date as $datek => $datev ){

				$app_device_cnt_all = ($datev['sum_mobileN_cnt'] + $datev['sum_mobileY_cnt']) > 0 ? ($datev['sum_mobileN_cnt'] + $datev['sum_mobileY_cnt']) : 1;
				$app_mem_cnt_all = ($datev['sum_memtypeY_cnt'] + $datev['sum_memtypeN_cnt']) > 0 ? ($datev['sum_memtypeY_cnt'] + $datev['sum_memtypeN_cnt']) : 1;

				echo '
					<tr>
						<td>'. $datev['rdate'] .'</td><!-- 날짜 -->
						<!-- 총 방문수 -->
						<td>' . number_format($datev['sum_cnt']) . '</td>
						<!-- 접속기기 구분 -->
						<td>' . number_format($datev['sum_mobileN_cnt']) . '</td>
						<td>' . number_format($datev['sum_mobileN_cnt'] * 100 / $app_device_cnt_all , 2) . '%</td>
						<td>' . number_format($datev['sum_mobileY_cnt']) . '</td>
						<td>' . number_format($datev['sum_mobileY_cnt'] * 100 / $app_device_cnt_all , 2) . '%</td>
						<!-- 회원구분 -->
						<td>' . number_format($datev['sum_memtypeY_cnt']) . '</td>
						<td>' . number_format($datev['sum_memtypeY_cnt'] * 100 / $app_mem_cnt_all , 2) . '%</td>
						<td>' . number_format($datev['sum_memtypeN_cnt']) . '</td>
						<td>' . number_format($datev['sum_memtypeN_cnt'] * 100 / $app_mem_cnt_all , 2) . '%</td>
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 일자별 --------------

		// -------------- 시간별 --------------
		case "cntlog_hour_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=CntlogHour". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';


			$pass_sdate = $pass_sdate ? $pass_sdate : date('Y-m-d' , strtotime("-1 week"));
			$pass_edate = $pass_edate ? $pass_edate : date('Y-m-d');

			$s_query = " where 1 ";
			$s_query .= " and DATE(sc_date) between '". $pass_sdate ."' and '". $pass_edate ."' ";

			$res_date = _MQ_assoc("
				SELECT
					HOUR(sc_date) as rdate ,
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

			echo '
				<table border=1>
					<tr>
						<td rowspan="3">시간</td>
						<td rowspan="3">총 방문수</td>
						<td colspan="4">접속기기 구분</td>
						<td colspan="4">회원구분</td>
					</tr>
					<tr>
						<td colspan="2">PC</td>
						<td colspan="2">MOBILE</td>
						<td colspan="2">회원</td>
						<td colspan="2">비회원</td>
					</tr>
					<tr>
						<td >건수</td>
						<td >비율</td>
						<td >건수</td>
						<td >비율</td>
						<td >건수</td>
						<td >비율</td>
						<td >건수</td>
						<td >비율</td>
					</tr>
			';
			foreach( $res_date as $datek => $datev ){

				$app_device_cnt_all = ($datev['sum_mobileN_cnt'] + $datev['sum_mobileY_cnt']) > 0 ? ($datev['sum_mobileN_cnt'] + $datev['sum_mobileY_cnt']) : 1;
				$app_mem_cnt_all = ($datev['sum_memtypeY_cnt'] + $datev['sum_memtypeN_cnt']) > 0 ? ($datev['sum_memtypeY_cnt'] + $datev['sum_memtypeN_cnt']) : 1;

				echo '
					<tr>
						<td>'. $datev['rdate'] .'시</td><!-- 시간 -->
						<!-- 총 방문수 -->
						<td>' . number_format($datev['sum_cnt']) . '</td>
						<!-- 접속기기 구분 -->
						<td>' . number_format($datev['sum_mobileN_cnt']) . '</td>
						<td>' . number_format($datev['sum_mobileN_cnt'] * 100 / $app_device_cnt_all , 2) . '%</td>
						<td>' . number_format($datev['sum_mobileY_cnt']) . '</td>
						<td>' . number_format($datev['sum_mobileY_cnt'] * 100 / $app_device_cnt_all , 2) . '%</td>
						<!-- 회원구분 -->
						<td>' . number_format($datev['sum_memtypeY_cnt']) . '</td>
						<td>' . number_format($datev['sum_memtypeY_cnt'] * 100 / $app_mem_cnt_all , 2) . '%</td>
						<td>' . number_format($datev['sum_memtypeN_cnt']) . '</td>
						<td>' . number_format($datev['sum_memtypeN_cnt'] * 100 / $app_mem_cnt_all , 2) . '%</td>
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 시간별 --------------

		// -------------- 요일별 --------------
		case "cntlog_week_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=CntlogWeek". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';


			$pass_sdate = $pass_sdate ? $pass_sdate : date('Y-m-d' , strtotime("-1 week"));
			$pass_edate = $pass_edate ? $pass_edate : date('Y-m-d');

			$s_query = " where 1 ";
			$s_query .= " and DATE(sc_date) between '". $pass_sdate ."' and '". $pass_edate ."' ";

			$res_date = _MQ_assoc("
				SELECT
					DATE_FORMAT(sc_date, '%w') as rdate ,
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

			echo '
				<table border=1>
					<tr>
						<td rowspan="3">요일</td>
						<td rowspan="3">총 방문수</td>
						<td colspan="4">접속기기 구분</td>
						<td colspan="4">회원구분</td>
					</tr>
					<tr>
						<td colspan="2">PC</td>
						<td colspan="2">MOBILE</td>
						<td colspan="2">회원</td>
						<td colspan="2">비회원</td>
					</tr>
					<tr>
						<td >건수</td>
						<td >비율</td>
						<td >건수</td>
						<td >비율</td>
						<td >건수</td>
						<td >비율</td>
						<td >건수</td>
						<td >비율</td>
					</tr>
			';
			foreach( $res_date as $datek => $datev ){

				$app_device_cnt_all = ($datev['sum_mobileN_cnt'] + $datev['sum_mobileY_cnt']) > 0 ? ($datev['sum_mobileN_cnt'] + $datev['sum_mobileY_cnt']) : 1;
				$app_mem_cnt_all = ($datev['sum_memtypeY_cnt'] + $datev['sum_memtypeN_cnt']) > 0 ? ($datev['sum_memtypeY_cnt'] + $datev['sum_memtypeN_cnt']) : 1;

				echo '
					<tr>
						<td>'. week_name($datev['rdate'] , '요일') .'</td><!-- 요일 -->
						<!-- 총 방문수 -->
						<td>' . number_format($datev['sum_cnt']) . '</td>
						<!-- 접속기기 구분 -->
						<td>' . number_format($datev['sum_mobileN_cnt']) . '</td>
						<td>' . number_format($datev['sum_mobileN_cnt'] * 100 / $app_device_cnt_all , 2) . '%</td>
						<td>' . number_format($datev['sum_mobileY_cnt']) . '</td>
						<td>' . number_format($datev['sum_mobileY_cnt'] * 100 / $app_device_cnt_all , 2) . '%</td>
						<!-- 회원구분 -->
						<td>' . number_format($datev['sum_memtypeY_cnt']) . '</td>
						<td>' . number_format($datev['sum_memtypeY_cnt'] * 100 / $app_mem_cnt_all , 2) . '%</td>
						<td>' . number_format($datev['sum_memtypeN_cnt']) . '</td>
						<td>' . number_format($datev['sum_memtypeN_cnt'] * 100 / $app_mem_cnt_all , 2) . '%</td>
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 요일별 --------------

		// -------------- 월별 --------------
		case "cntlog_month_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=CntlogMonth". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';


			$pass_sdate = $pass_sdate ? $pass_sdate : date('Y');

			$s_query = " where 1 ";
			$s_query .= " and LEFT(sc_date , 4) = '". $pass_sdate ."' ";

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
			echo '
				<table border=1>
					<tr>
						<td rowspan="3">년월</td>
						<td rowspan="3">총 방문수</td>
						<td colspan="4">접속기기 구분</td>
						<td colspan="4">회원구분</td>
					</tr>
					<tr>
						<td colspan="2">PC</td>
						<td colspan="2">MOBILE</td>
						<td colspan="2">회원</td>
						<td colspan="2">비회원</td>
					</tr>
					<tr>
						<td >건수</td>
						<td >비율</td>
						<td >건수</td>
						<td >비율</td>
						<td >건수</td>
						<td >비율</td>
						<td >건수</td>
						<td >비율</td>
					</tr>
			';
			foreach( $res_date as $datek => $datev ){

				$ex = explode('-' , $datev['rdate']);

				$app_device_cnt_all = ($datev['sum_mobileN_cnt'] + $datev['sum_mobileY_cnt']) > 0 ? ($datev['sum_mobileN_cnt'] + $datev['sum_mobileY_cnt']) : 1;
				$app_mem_cnt_all = ($datev['sum_memtypeY_cnt'] + $datev['sum_memtypeN_cnt']) > 0 ? ($datev['sum_memtypeY_cnt'] + $datev['sum_memtypeN_cnt']) : 1;

				echo '
					<tr>
						<td>'. $ex[0] .'년 '. $ex[1] .'월</td><!-- 년월 -->
						<!-- 총 방문수 -->
						<td>' . number_format($datev['sum_cnt']) . '</td>
						<!-- 접속기기 구분 -->
						<td>' . number_format($datev['sum_mobileN_cnt']) . '</td>
						<td>' . number_format($datev['sum_mobileN_cnt'] * 100 / $app_device_cnt_all , 2) . '%</td>
						<td>' . number_format($datev['sum_mobileY_cnt']) . '</td>
						<td>' . number_format($datev['sum_mobileY_cnt'] * 100 / $app_device_cnt_all , 2) . '%</td>
						<!-- 회원구분 -->
						<td>' . number_format($datev['sum_memtypeY_cnt']) . '</td>
						<td>' . number_format($datev['sum_memtypeY_cnt'] * 100 / $app_mem_cnt_all , 2) . '%</td>
						<td>' . number_format($datev['sum_memtypeN_cnt']) . '</td>
						<td>' . number_format($datev['sum_memtypeN_cnt'] * 100 / $app_mem_cnt_all , 2) . '%</td>
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 월별 --------------

		// -------------- 접속경로별 --------------
		case "cntlog_route_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=CntlogRoute". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';


			$pass_sdate = $pass_sdate ? $pass_sdate : date('Y-m-d' , strtotime("-1 week"));
			$pass_edate = $pass_edate ? $pass_edate : date('Y-m-d');

			$s_query = " where 1 ";
			$s_query .= " and scr_date between '". $pass_sdate ."' and '". $pass_edate ."' ";

			$que = "
				SELECT
					SUM( scr_cnt_pc + scr_cnt_mo ) as sum_cnt
				FROM smart_cntlog_route
					" . $s_query . "
			";
			$res = _MQ($que);
			$arr_sum['sum_cnt'] = $res['sum_cnt'];

			$res_date = _MQ_assoc("
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
				ORDER BY sum_cnt DESC , scr_route ASC
			");
			echo '
				<table border=1>
					<tr>
						<td>순위</td>
						<td>방문수</td>
						<td>PC방문수</td>
						<td>MOBILE방문수</td>
						<td>접속경로</td>
						<td>비율</td>
					</tr>
			';
			foreach( $res_date as $datek => $datev ){
				$_num = $datek + 1;// 순위

				$sum_cnt = $arr_sum['sum_cnt'] > 0 ? $arr_sum['sum_cnt'] : 1;
				$_ratio =  number_format( 100 * $datev['sum_cnt'] / $sum_cnt , 2); // 비율

				echo '
					<tr>
						<td>'. $_num .'</td><!-- 순위 -->
						<!-- 총 방문수 -->
						<td >' . number_format($datev['sum_cnt']) . '</td>
						<!-- 접속기기 구분 -->
						<td >' . number_format($datev['sum_mobileN_cnt']) . '</td>
						<td >' . number_format($datev['sum_mobileY_cnt']) . '</td>
						<td >' . ($datev['scr_route'] ? $datev['scr_route'] : '즐겨찾기 OR URL 직접입력을 통한 접속') . '</td><!-- 접속경로 -->
						<td >' . $_ratio . '%</td><!-- 비율 -->
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 접속경로별 --------------


		// -------------- 키워드별 --------------
		case "cntlog_keyword_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=CntlogKeyword". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';


			$pass_sdate = $pass_sdate ? $pass_sdate : date('Y-m-d' , strtotime("-1 week"));
			$pass_edate = $pass_edate ? $pass_edate : date('Y-m-d');

			$s_query = " where 1 ";
			$s_query .= " and sck_date between '". $pass_sdate ."' and '". $pass_edate ."' ";

			$que = "
				SELECT
					SUM( sck_cnt_pc + sck_cnt_mo ) as sum_cnt
				FROM smart_cntlog_keyword
					" . $s_query . "
			";
			$res = _MQ($que);
			$arr_sum['sum_cnt'] = $res['sum_cnt'];

			$res_date = _MQ_assoc("
				SELECT
					sck_keyword ,
					'----------- 접속기기 -----------',
					SUM( sck_cnt_mo ) as sum_mobileY_cnt,
					SUM( sck_cnt_pc ) as sum_mobileN_cnt,
					'----------- 총방문수 -----------',
					SUM( sck_cnt_pc + sck_cnt_mo ) as sum_cnt

				FROM smart_cntlog_keyword
					" . $s_query . "
				GROUP BY sck_keyword
				ORDER BY sum_cnt DESC , sck_keyword ASC
			");
			echo '
				<table border=1>
					<tr>
						<td>순위</td>
						<td>방문수</td>
						<td>PC방문수</td>
						<td>MOBILE방문수</td>
						<td>키워드</td>
						<td>비율</td>
					</tr>
			';
			foreach( $res_date as $datek => $datev ){
				$_num = $datek + 1;// 순위

				$sum_cnt = $arr_sum['sum_cnt'] > 0 ? $arr_sum['sum_cnt'] : 1;
				$_ratio =  number_format( 100 * $datev['sum_cnt'] / $sum_cnt , 2); // 비율

				echo '
					<tr>
						<td>'. $_num .'</td><!-- 순위 -->
						<!-- 총 방문수 -->
						<td >' . number_format($datev['sum_cnt']) . '</td>
						<!-- 접속기기 구분 -->
						<td >' . number_format($datev['sum_mobileN_cnt']) . '</td>
						<td >' . number_format($datev['sum_mobileY_cnt']) . '</td>
						<td >' . ($datev['sck_keyword'] ? $datev['sck_keyword'] : '키워드 없음') . '</td><!-- 접속경로 -->
						<td >' . $_ratio . '%</td><!-- 비율 -->
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 키워드별 --------------


		// -------------- 상세접속 --------------
		case "cntlog_detail_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=CntlogDetail". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';


			// 일자계산
			$pass_sdate = $pass_sdate ? $pass_sdate : date('Y-m-d' , strtotime("-1 week"));
			$pass_edate = $pass_edate ? $pass_edate : date('Y-m-d');

			$s_query = "
				FROM smart_cntlog_list as sc
				INNER JOIN smart_cntlog_detail as scd ON (sc.sc_uid = scd.sc_uid)
				where 1
			";
			$s_query .= " and DATE(sc.sc_date) between '". $pass_sdate ."' and '". $pass_edate ."' ";

			if($pass_mobile)	$s_query .= " and sc.sc_mobile = '". $pass_mobile ."' ";
			if($pass_keyword)	$s_query .= " and scd.sc_keyword like '%". $pass_keyword ."%' ";
			if($pass_browser)	$s_query .= " and scd.sc_browser like '%". $pass_browser ."%' ";
			if($pass_ip)	$s_query .= " and scd.sc_ip like '%". $pass_ip ."%' ";


			$que = "
				SELECT
					sc.*, scd.*
					" . $s_query . "
				ORDER BY sc.sc_uid DESC
			";
			$res = _MQ_assoc($que);

			echo '
				<table border=1>
					<tr>
						<td >NO.</td>
						<td >유입일</td>
						<td >IP</td>
						<td >DEVICE</td>
						<td >검색어</td>
						<td >유입경로</td>
						<td >BROWSER</td>
					</tr>
			';
			foreach( $res as $datek => $datev ){
				$_num = sizeof($res) - $datek ;
				echo '
					<tr>
						<td>'. $_num .'</td>
						<td>'. $datev['sc_date'] .'</td>
						<td>'. $datev['sc_ip'] .'</td>
						<td>'. ($datev['sc_mobile'] == 'N' ? 'PC' : 'MOBILE' ) .'</td>
						<td>'. $datev['sc_keyword'] .'</td>
						<td>' . $datev['sc_referer'] . '</td>
						<td>'. $datev['sc_browser'] .'</td>
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 상세접속 --------------

	// -------------- 방문자분석   ----------------------------

	}
exit;