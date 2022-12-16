<?php


	ini_set('memory_limit', '-1');
	include_once('inc.php');



	switch($_mode){





	// -------------- 회원 가입형태 분석  ----------------------------

		// -------------- 회원 가입형태 분석 - 일자별 --------------
		case "method_day_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_member_method_day_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m', time()));


			echo '
				<table border=1>
					<tr>
						<td rowspan=2>날짜</td>
						<td rowspan=2>가입<br>회원수</td>
						<td colspan=4>회원구분</td>
						<td colspan=2>가입기기</td>
						<td colspan=4>수신여부</td>
					</tr>
					<tr>
						<td>일반</td>
						<td>페이스북</td>
						<td>카카오톡</td>
						<td>네이버</td>
						<td>PC</td>
						<td>MOBILE</td>
						<td>이메일허용</td>
						<td>이메일거부</td>
						<td>문자허용</td>
						<td>문자거부</td>
					</tr>
			';
			$mem_date = _MQ_assoc("
				SELECT

					DATE(in_rdate) as rdate ,

					count(*) as total_plus,

					SUM(IF(sns_join = 'N' , 1 , 0)) as sns_join_plus,
					SUM(IF(fb_join = 'Y' , 1 , 0)) as fb_join_plus,
					SUM(IF(ko_join = 'Y' , 1 , 0)) as ko_join_plus,
					SUM(IF(nv_join = 'Y' , 1 , 0)) as nv_join_plus,

					SUM(IF(in_join_ua = 'PC' , 1 , 0)) as in_join_ua_PC_plus,
					SUM(IF(in_join_ua = 'MOBILE' , 1 , 0)) as in_join_ua_MOBILE_plus,

					SUM(IF(in_emailsend = 'Y' , 1 , 0)) as in_emailsendY_plus,
					SUM(IF(in_emailsend != 'Y' , 1 , 0)) as in_emailsendN_plus,
					SUM(IF(in_smssend = 'Y' , 1 , 0)) as in_smssendY_plus,
					SUM(IF(in_smssend != 'Y' , 1 , 0)) as in_smssendN_plus

				FROM smart_individual
				WHERE
					LEFT(in_rdate,7) = '". $pass_date ."'
				GROUP BY rdate
				ORDER BY rdate ASC
			");
			foreach( $mem_date as $mem_datek => $mem_datev ){
				echo '
					<tr>
						<td>'. DATE("Y년 m월 d일" , strtotime($mem_datev['rdate'])) .'</td><!-- 날짜 -->
						<td>'. number_format($mem_datev['total_plus']) .'</td><!-- 가입 회원수 -->
						<!-- 회원구분 -->
						<td>'. number_format($mem_datev['sns_join_plus']) .'</td><!-- 일반 -->
						<td>'. number_format($mem_datev['fb_join_plus']) .'</td><!-- 페이스북 -->
						<td>'. number_format($mem_datev['ko_join_plus']) .'</td><!-- 카카오톡 -->
						<td>'. number_format($mem_datev['nv_join_plus']) .'</td><!-- 네이버 -->
						<!-- 가입기기 -->
						<td>'. number_format($mem_datev['in_join_ua_PC_plus']) .'</td><!-- PC -->
						<td>'. number_format($mem_datev['in_join_ua_MOBILE_plus']) .'</td><!-- MOBILE -->
						<!-- 수신여부 -->
						<td>'. number_format($mem_datev['in_emailsendY_plus']) .'</td><!-- 이메일허용 -->
						<td>'. number_format($mem_datev['in_emailsendN_plus']) .'</td><!-- 이메일거부 -->
						<td>'. number_format($mem_datev['in_smssendY_plus']) .'</td><!-- 문자허용 -->
						<td>'. number_format($mem_datev['in_smssendN_plus']) .'</td><!-- 문자거부 -->
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 회원 가입형태 분석 - 일자별 --------------



		// -------------- 회원 가입형태 분석 - 월별 --------------
		case "method_month_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_member_method_month_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y', time()));

			echo '
				<table border=1>
					<tr>
						<td rowspan=2>년월</td>
						<td rowspan=2>가입<br>회원수</td>
						<td colspan=4>회원구분</td>
						<td colspan=2>가입기기</td>
						<td colspan=4>수신여부</td>
					</tr>
					<tr>
						<td>일반</td>
						<td>페이스북</td>
						<td>카카오톡</td>
						<td>네이버</td>
						<td>PC</td>
						<td>MOBILE</td>
						<td>이메일허용</td>
						<td>이메일거부</td>
						<td>문자허용</td>
						<td>문자거부</td>
					</tr>
			';
			$mem_date = _MQ_assoc("
				SELECT

					LEFT(in_rdate,7) as rdate ,

					count(*) as total_plus,

					SUM(IF(sns_join = 'N' , 1 , 0)) as sns_join_plus,
					SUM(IF(fb_join = 'Y' , 1 , 0)) as fb_join_plus,
					SUM(IF(ko_join = 'Y' , 1 , 0)) as ko_join_plus,
					SUM(IF(nv_join = 'Y' , 1 , 0)) as nv_join_plus,

					SUM(IF(in_join_ua = 'PC' , 1 , 0)) as in_join_ua_PC_plus,
					SUM(IF(in_join_ua = 'MOBILE' , 1 , 0)) as in_join_ua_MOBILE_plus,

					SUM(IF(in_emailsend = 'Y' , 1 , 0)) as in_emailsendY_plus,
					SUM(IF(in_emailsend != 'Y' , 1 , 0)) as in_emailsendN_plus,
					SUM(IF(in_smssend = 'Y' , 1 , 0)) as in_smssendY_plus,
					SUM(IF(in_smssend != 'Y' , 1 , 0)) as in_smssendN_plus

				FROM smart_individual
				WHERE
					LEFT(in_rdate,4) = '". $pass_date ."'
				GROUP BY rdate
				ORDER BY rdate ASC
			");
			foreach( $mem_date as $mem_datek => $mem_datev ){
				$ex = explode("-" , $mem_datev['rdate']);
				echo '
					<tr>
						<td>'. $ex[0].'년 ' . $ex[1] . '월</td><!-- 년월 -->
						<td>'. number_format($mem_datev['total_plus']) .'</td><!-- 가입 회원수 -->
						<!-- 회원구분 -->
						<td>'. number_format($mem_datev['sns_join_plus']) .'</td><!-- 일반 -->
						<td>'. number_format($mem_datev['fb_join_plus']) .'</td><!-- 페이스북 -->
						<td>'. number_format($mem_datev['ko_join_plus']) .'</td><!-- 카카오톡 -->
						<td>'. number_format($mem_datev['nv_join_plus']) .'</td><!-- 네이버 -->
						<!-- 가입기기 -->
						<td>'. number_format($mem_datev['in_join_ua_PC_plus']) .'</td><!-- PC -->
						<td>'. number_format($mem_datev['in_join_ua_MOBILE_plus']) .'</td><!-- MOBILE -->
						<!-- 수신여부 -->
						<td>'. number_format($mem_datev['in_emailsendY_plus']) .'</td><!-- 이메일허용 -->
						<td>'. number_format($mem_datev['in_emailsendN_plus']) .'</td><!-- 이메일거부 -->
						<td>'. number_format($mem_datev['in_smssendY_plus']) .'</td><!-- 문자허용 -->
						<td>'. number_format($mem_datev['in_smssendN_plus']) .'</td><!-- 문자거부 -->
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 회원 가입형태 분석 - 월별 --------------



		// -------------- 회원 가입형태 분석 - 년별 --------------
		case "method_year_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_member_method_year_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			echo '
				<table border=1>
					<tr>
						<td rowspan=2>년월</td>
						<td rowspan=2>가입<br>회원수</td>
						<td colspan=4>회원구분</td>
						<td colspan=2>가입기기</td>
						<td colspan=4>수신여부</td>
					</tr>
					<tr>
						<td>일반</td>
						<td>페이스북</td>
						<td>카카오톡</td>
						<td>네이버</td>
						<td>PC</td>
						<td>MOBILE</td>
						<td>이메일허용</td>
						<td>이메일거부</td>
						<td>문자허용</td>
						<td>문자거부</td>
					</tr>
			';
			$mem_date = _MQ_assoc("
				SELECT

					LEFT(in_rdate,4) as rdate ,

					count(*) as total_plus,

					SUM(IF(sns_join = 'N' , 1 , 0)) as sns_join_plus,
					SUM(IF(fb_join = 'Y' , 1 , 0)) as fb_join_plus,
					SUM(IF(ko_join = 'Y' , 1 , 0)) as ko_join_plus,
					SUM(IF(nv_join = 'Y' , 1 , 0)) as nv_join_plus,

					SUM(IF(in_join_ua = 'PC' , 1 , 0)) as in_join_ua_PC_plus,
					SUM(IF(in_join_ua = 'MOBILE' , 1 , 0)) as in_join_ua_MOBILE_plus,

					SUM(IF(in_emailsend = 'Y' , 1 , 0)) as in_emailsendY_plus,
					SUM(IF(in_emailsend != 'Y' , 1 , 0)) as in_emailsendN_plus,
					SUM(IF(in_smssend = 'Y' , 1 , 0)) as in_smssendY_plus,
					SUM(IF(in_smssend != 'Y' , 1 , 0)) as in_smssendN_plus

				FROM smart_individual
				GROUP BY rdate
				ORDER BY rdate ASC
			");
			foreach( $mem_date as $mem_datek => $mem_datev ){
				echo '
					<tr>
						<td>'. $mem_datev['rdate'] . '년</td><!-- 년 -->
						<td>'. number_format($mem_datev['total_plus']) .'</td><!-- 가입 회원수 -->
						<!-- 회원구분 -->
						<td>'. number_format($mem_datev['sns_join_plus']) .'</td><!-- 일반 -->
						<td>'. number_format($mem_datev['fb_join_plus']) .'</td><!-- 페이스북 -->
						<td>'. number_format($mem_datev['ko_join_plus']) .'</td><!-- 카카오톡 -->
						<td>'. number_format($mem_datev['nv_join_plus']) .'</td><!-- 네이버 -->
						<!-- 가입기기 -->
						<td>'. number_format($mem_datev['in_join_ua_PC_plus']) .'</td><!-- PC -->
						<td>'. number_format($mem_datev['in_join_ua_MOBILE_plus']) .'</td><!-- MOBILE -->
						<!-- 수신여부 -->
						<td>'. number_format($mem_datev['in_emailsendY_plus']) .'</td><!-- 이메일허용 -->
						<td>'. number_format($mem_datev['in_emailsendN_plus']) .'</td><!-- 이메일거부 -->
						<td>'. number_format($mem_datev['in_smssendY_plus']) .'</td><!-- 문자허용 -->
						<td>'. number_format($mem_datev['in_smssendN_plus']) .'</td><!-- 문자거부 -->
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 회원 가입형태 분석 - 년별 --------------





		// -------------- 회원 가입형태 분석 - 요일별 --------------
		case "method_week_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_member_method_week_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));

			echo '
				<table border=1>
					<tr>
						<td rowspan=2>요일</td>
						<td rowspan=2>가입<br>회원수</td>
						<td colspan=4>회원구분</td>
						<td colspan=2>가입기기</td>
						<td colspan=4>수신여부</td>
					</tr>
					<tr>
						<td>일반</td>
						<td>페이스북</td>
						<td>카카오톡</td>
						<td>네이버</td>
						<td>PC</td>
						<td>MOBILE</td>
						<td>이메일허용</td>
						<td>이메일거부</td>
						<td>문자허용</td>
						<td>문자거부</td>
					</tr>
			';
			$mem_date = _MQ_assoc("
				SELECT

					DATE_FORMAT(in_rdate, '%w') as rdate ,

					count(*) as total_plus,

					SUM(IF(sns_join = 'N' , 1 , 0)) as sns_join_plus,
					SUM(IF(fb_join = 'Y' , 1 , 0)) as fb_join_plus,
					SUM(IF(ko_join = 'Y' , 1 , 0)) as ko_join_plus,
					SUM(IF(nv_join = 'Y' , 1 , 0)) as nv_join_plus,

					SUM(IF(in_join_ua = 'PC' , 1 , 0)) as in_join_ua_PC_plus,
					SUM(IF(in_join_ua = 'MOBILE' , 1 , 0)) as in_join_ua_MOBILE_plus,

					SUM(IF(in_emailsend = 'Y' , 1 , 0)) as in_emailsendY_plus,
					SUM(IF(in_emailsend != 'Y' , 1 , 0)) as in_emailsendN_plus,
					SUM(IF(in_smssend = 'Y' , 1 , 0)) as in_smssendY_plus,
					SUM(IF(in_smssend != 'Y' , 1 , 0)) as in_smssendN_plus

				FROM smart_individual
				WHERE
					DATE(in_rdate) between '". $pass_date ."' and '". $pass_edate ."'
				GROUP BY rdate
				ORDER BY rdate ASC
			");
			foreach( $mem_date as $mem_datek => $mem_datev ){
				echo '
					<tr>
						<td>'. week_name( $mem_datev['rdate'] , '요일') .'</td><!-- 요일 -->
						<td>'. number_format($mem_datev['total_plus']) .'</td><!-- 가입 회원수 -->
						<!-- 회원구분 -->
						<td>'. number_format($mem_datev['sns_join_plus']) .'</td><!-- 일반 -->
						<td>'. number_format($mem_datev['fb_join_plus']) .'</td><!-- 페이스북 -->
						<td>'. number_format($mem_datev['ko_join_plus']) .'</td><!-- 카카오톡 -->
						<td>'. number_format($mem_datev['nv_join_plus']) .'</td><!-- 네이버 -->
						<!-- 가입기기 -->
						<td>'. number_format($mem_datev['in_join_ua_PC_plus']) .'</td><!-- PC -->
						<td>'. number_format($mem_datev['in_join_ua_MOBILE_plus']) .'</td><!-- MOBILE -->
						<!-- 수신여부 -->
						<td>'. number_format($mem_datev['in_emailsendY_plus']) .'</td><!-- 이메일허용 -->
						<td>'. number_format($mem_datev['in_emailsendN_plus']) .'</td><!-- 이메일거부 -->
						<td>'. number_format($mem_datev['in_smssendY_plus']) .'</td><!-- 문자허용 -->
						<td>'. number_format($mem_datev['in_smssendN_plus']) .'</td><!-- 문자거부 -->
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 회원 가입형태 분석 - 요일별 --------------





		// -------------- 회원 가입형태 분석 - 시간별 --------------
		case "method_hour_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_member_hour_week_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));

			echo '
				<table border=1>
					<tr>
						<td rowspan=2>시간</td>
						<td rowspan=2>가입<br>회원수</td>
						<td colspan=4>회원구분</td>
						<td colspan=2>가입기기</td>
						<td colspan=4>수신여부</td>
					</tr>
					<tr>
						<td>일반</td>
						<td>페이스북</td>
						<td>카카오톡</td>
						<td>네이버</td>
						<td>PC</td>
						<td>MOBILE</td>
						<td>이메일허용</td>
						<td>이메일거부</td>
						<td>문자허용</td>
						<td>문자거부</td>
					</tr>
			';
			$mem_date = _MQ_assoc("
				SELECT

					HOUR(in_rdate) as rdate ,

					count(*) as total_plus,

					SUM(IF(sns_join = 'N' , 1 , 0)) as sns_join_plus,
					SUM(IF(fb_join = 'Y' , 1 , 0)) as fb_join_plus,
					SUM(IF(ko_join = 'Y' , 1 , 0)) as ko_join_plus,
					SUM(IF(nv_join = 'Y' , 1 , 0)) as nv_join_plus,

					SUM(IF(in_join_ua = 'PC' , 1 , 0)) as in_join_ua_PC_plus,
					SUM(IF(in_join_ua = 'MOBILE' , 1 , 0)) as in_join_ua_MOBILE_plus,

					SUM(IF(in_emailsend = 'Y' , 1 , 0)) as in_emailsendY_plus,
					SUM(IF(in_emailsend != 'Y' , 1 , 0)) as in_emailsendN_plus,
					SUM(IF(in_smssend = 'Y' , 1 , 0)) as in_smssendY_plus,
					SUM(IF(in_smssend != 'Y' , 1 , 0)) as in_smssendN_plus

				FROM smart_individual
				WHERE
					DATE(in_rdate) between '". $pass_date ."' and '". $pass_edate ."'
				GROUP BY rdate
				ORDER BY rdate ASC
			");
			foreach( $mem_date as $mem_datek => $mem_datev ){
				echo '
					<tr>
						<td>'. $mem_datev['rdate'] . '시</td><!-- 시간 -->
						<td>'. number_format($mem_datev['total_plus']) .'</td><!-- 가입 회원수 -->
						<!-- 회원구분 -->
						<td>'. number_format($mem_datev['sns_join_plus']) .'</td><!-- 일반 -->
						<td>'. number_format($mem_datev['fb_join_plus']) .'</td><!-- 페이스북 -->
						<td>'. number_format($mem_datev['ko_join_plus']) .'</td><!-- 카카오톡 -->
						<td>'. number_format($mem_datev['nv_join_plus']) .'</td><!-- 네이버 -->
						<!-- 가입기기 -->
						<td>'. number_format($mem_datev['in_join_ua_PC_plus']) .'</td><!-- PC -->
						<td>'. number_format($mem_datev['in_join_ua_MOBILE_plus']) .'</td><!-- MOBILE -->
						<!-- 수신여부 -->
						<td>'. number_format($mem_datev['in_emailsendY_plus']) .'</td><!-- 이메일허용 -->
						<td>'. number_format($mem_datev['in_emailsendN_plus']) .'</td><!-- 이메일거부 -->
						<td>'. number_format($mem_datev['in_smssendY_plus']) .'</td><!-- 문자허용 -->
						<td>'. number_format($mem_datev['in_smssendN_plus']) .'</td><!-- 문자거부 -->
					</tr>
				';
			}
			echo '</table>';
			exit;
			break;
		// -------------- 회원 가입형태 분석 - 시간별 --------------
	// -------------- 회원 가입형태 분석  ----------------------------








	// -------------- 회원 상태 분석  ----------------------------


		// -------------- 회원 상태 분석 - 일자별 --------------
		case "type_day_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_member_type_day_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m', time()));


			echo '
				<table border=1>
					<tr>
						<td>날짜</td>
						<td>가입 회원수</td>
						<td>승인 회원수</td>
						<td>휴면 회원수</td>
						<td>탈퇴 회원수</td>
					</tr>
			';


			// ------- 회원 상태 분석 -기간 내 승인 회원수 - 날짜별 목록 -------
			$mem_date = _MQ_assoc(" SELECT  DATE(in_auth_date) as rdate , count(*) as total_plus FROM smart_individual  WHERE  LEFT(in_auth_date,7) = '". $pass_date ."' and in_auth = 'Y' GROUP BY rdate ORDER BY rdate ASC");
			foreach($mem_date as $k=>$v){
				$arr_data['auth'][DATE("Ymd" , strtotime($v['rdate']))] = $v['total_plus'];
			}

			// ------- 회원 상태 분석 -기간 내 탈퇴 회원수 - 날짜별 목록 -------
			$mem_date = _MQ_assoc(" SELECT  DATE(in_odate) as rdate , count(*) as total_plus FROM smart_individual  WHERE  LEFT(in_odate,7) = '". $pass_date ."' and in_out = 'Y' GROUP BY rdate ORDER BY rdate ASC");
			foreach($mem_date as $k=>$v){
				$arr_data['out'][DATE("Ymd" , strtotime($v['rdate']))] = $v['total_plus'];
			}

			// ------- 회원 상태 분석 -기간 내 휴면 회원수 - 날짜별 목록 -------
			$mem_date = _MQ_assoc("
				SELECT
					DATE(ins.ins_rdate) as rdate , count(*) as total_plus
				FROM smart_individual_sleep as ins
				INNER JOIN smart_individual as ind ON (ind.in_id = ins.in_id and ind.in_sleep_type = 'Y')
				WHERE
					LEFT(ins.ins_rdate,7) = '". $pass_date ."'
				GROUP BY rdate
				ORDER BY rdate ASC
			");
			foreach($mem_date as $k=>$v){
				$arr_data['sleep'][DATE("Ymd" , strtotime($v['rdate']))] = $v['total_plus'];
			}


			// ------- 기간내 가입 회원 수 -------
			$mem_date = _MQ_assoc(" SELECT  DATE(in_rdate) as rdate , count(*) as total_plus FROM smart_individual  WHERE  LEFT(in_rdate,7) = '". $pass_date ."' GROUP BY rdate ORDER BY rdate ASC");
			foreach($mem_date as $k=>$v){
				$arr_data['join'][DATE("Ymd" , strtotime($v['rdate']))] = $v['total_plus'];
			}

			for($i=1 ; $i<=date("t" , strtotime(date("{$Select_Year}-{$Select_Month}-01"))) ; $i++){

				$app_date = $Select_Year ."-". $Select_Month ."-". sprintf("%02d" , $i);

				echo '
					<tr>
						<td>'. $app_date .'</td><!-- 날짜 -->
						<td>'. number_format($arr_data['join'][$Select_Year . $Select_Month . sprintf("%02d" , $i)] * 1) .'</td><!-- 가입 회원수 -->
						<td>'. number_format($arr_data['auth'][$Select_Year . $Select_Month . sprintf("%02d" , $i)] * 1) .'</td><!-- 승인 회원수 -->
						<td>'. number_format($arr_data['sleep'][$Select_Year . $Select_Month . sprintf("%02d" , $i)] * 1) .'</td><!-- 휴면 회원수 -->
						<td>'. number_format($arr_data['out'][$Select_Year . $Select_Month . sprintf("%02d" , $i)] * 1) .'</td><!-- 탈퇴 회원수 -->
					</tr>
				';

			}
			echo '</table>';
			exit;
			break;
		// -------------- 회원 상태 분석 - 일자별 --------------


		// -------------- 회원 상태 분석 - 월별 --------------
		case "type_month_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_member_type_month_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m', time()));


			echo '
				<table border=1>
					<tr>
						<td>년월</td>
						<td>가입 회원수</td>
						<td>승인 회원수</td>
						<td>휴면 회원수</td>
						<td>탈퇴 회원수</td>
					</tr>
			';


			// ------- 회원 상태 분석 -기간 내 승인 회원수 - 날짜별 목록 -------
			$mem_date = _MQ_assoc(" SELECT  LEFT(in_auth_date,7) as rdate , count(*) as total_plus FROM smart_individual  WHERE  LEFT(in_auth_date,4) = '". $pass_date ."' and in_auth = 'Y' GROUP BY rdate ORDER BY rdate ASC");
			foreach($mem_date as $k=>$v){
				$arr_data['auth'][str_replace("-" , "" , $v['rdate'])] = $v['total_plus'];
			}

			// ------- 회원 상태 분석 -기간 내 탈퇴 회원수 - 날짜별 목록 -------
			$mem_date = _MQ_assoc(" SELECT  LEFT(in_odate,7) as rdate , count(*) as total_plus FROM smart_individual  WHERE  LEFT(in_odate,4) = '". $pass_date ."' and in_out = 'Y' GROUP BY rdate ORDER BY rdate ASC");
			foreach($mem_date as $k=>$v){
				$arr_data['out'][str_replace("-" , "" , $v['rdate'])] = $v['total_plus'];
			}

			// ------- 회원 상태 분석 -기간 내 휴면 회원수 - 날짜별 목록 -------
			$mem_date = _MQ_assoc("
				SELECT
					LEFT(ins.ins_rdate,7) as rdate , count(*) as total_plus
				FROM smart_individual_sleep as ins
				INNER JOIN smart_individual as ind ON (ind.in_id = ins.in_id and ind.in_sleep_type = 'Y')
				WHERE
					LEFT(ins.ins_rdate,4) = '". $pass_date ."'
				GROUP BY rdate
				ORDER BY rdate ASC
			");
			foreach($mem_date as $k=>$v){
				$arr_data['sleep'][str_replace("-" , "" , $v['rdate'])] = $v['total_plus'];
			}


			// ------- 기간내 가입 회원 수 -------
			$mem_date = _MQ_assoc(" SELECT  LEFT(in_rdate,7) as rdate , count(*) as total_plus FROM smart_individual  WHERE  LEFT(in_rdate,4) = '". $pass_date ."' GROUP BY rdate ORDER BY rdate ASC");
			foreach($mem_date as $k=>$v){
				$arr_data['join'][str_replace("-" , "" , $v['rdate'])] = $v['total_plus'];
			}

			for($i=1 ; $i<=12 ; $i++){

				$app_date = $Select_Year ."년 ". sprintf("%02d" , $i) . "일";

				echo '
					<tr>
						<td>'. $app_date .'</td><!-- 날짜 -->
						<td>'. number_format($arr_data['join'][$Select_Year . sprintf("%02d" , $i)] * 1) .'</td><!-- 가입 회원수 -->
						<td>'. number_format($arr_data['auth'][$Select_Year . sprintf("%02d" , $i)] * 1) .'</td><!-- 승인 회원수 -->
						<td>'. number_format($arr_data['sleep'][$Select_Year . sprintf("%02d" , $i)] * 1) .'</td><!-- 휴면 회원수 -->
						<td>'. number_format($arr_data['out'][$Select_Year . sprintf("%02d" , $i)] * 1) .'</td><!-- 탈퇴 회원수 -->
					</tr>
				';

			}
			echo '</table>';
			exit;
			break;
		// -------------- 회원 상태 분석 - 월별 --------------


		// -------------- 회원 상태 분석 - 년별 --------------
		case "type_year_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_member_type_year_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			echo '
				<table border=1>
					<tr>
						<td>년도</td>
						<td>가입 회원수</td>
						<td>승인 회원수</td>
						<td>휴면 회원수</td>
						<td>탈퇴 회원수</td>
					</tr>
			';


			// ------- 회원 상태 분석 -기간 내 승인 회원수 - 날짜별 목록 -------
			$mem_date = _MQ_assoc(" SELECT  LEFT(in_auth_date,4) as rdate , count(*) as total_plus FROM smart_individual WHERE in_auth = 'Y' GROUP BY rdate ORDER BY rdate ASC");
			foreach($mem_date as $k=>$v){
				if($v['rdate'] > 0 ) {
					$arr_data['auth'][$v['rdate']] = $v['total_plus'];
				}
			}

			// ------- 회원 상태 분석 -기간 내 탈퇴 회원수 - 날짜별 목록 -------
			$mem_date = _MQ_assoc(" SELECT  LEFT(in_odate,4) as rdate , count(*) as total_plus FROM smart_individual WHERE in_out = 'Y' GROUP BY rdate ORDER BY rdate ASC");
			foreach($mem_date as $k=>$v){
				if($v['rdate'] > 0 ) {
					$arr_data['out'][$v['rdate']] = $v['total_plus'];
				}
			}

			// ------- 회원 상태 분석 -기간 내 휴면 회원수 - 날짜별 목록 -------
			$mem_date = _MQ_assoc("
				SELECT
					LEFT(ins.ins_rdate,4) as rdate , count(*) as total_plus
				FROM smart_individual_sleep as ins
				INNER JOIN smart_individual as ind ON (ind.in_id = ins.in_id and ind.in_sleep_type = 'Y')
				GROUP BY rdate
				ORDER BY rdate ASC
			");
			foreach($mem_date as $k=>$v){
				if($v['rdate'] > 0 ) {
					$arr_data['sleep'][$v['rdate']] = $v['total_plus'];
				}
			}


			// ------- 기간내 가입 회원 수 -------
			$mem_date = _MQ_assoc(" SELECT  LEFT(in_rdate,4) as rdate , count(*) as total_plus FROM smart_individual GROUP BY rdate ORDER BY rdate ASC");
			foreach($mem_date as $k=>$v){
				if($v['rdate'] > 0 ) {
					$arr_data['join'][$v['rdate']] = $v['total_plus'];
				}
			}

			$year_min = IS_ARRAY($arr_data['join']) ? min(array_keys($arr_data['join'])) : DATE("Y");
			$year_max = IS_ARRAY($arr_data['join']) ? max(array_keys($arr_data['join'])) : DATE("Y");

			for($i=$year_min ; $i<=$year_max ; $i++){

				$app_date = sprintf("%04d" , $i);

				echo '
					<tr>
						<td>'. $app_date .'</td><!-- 년 -->
						<td>'. number_format($arr_data['join'][sprintf("%04d" , $i)] * 1) .'</td><!-- 가입 회원수 -->
						<td>'. number_format($arr_data['auth'][sprintf("%04d" , $i)] * 1) .'</td><!-- 승인 회원수 -->
						<td>'. number_format($arr_data['sleep'][sprintf("%04d" , $i)] * 1) .'</td><!-- 휴면 회원수 -->
						<td>'. number_format($arr_data['out'][sprintf("%04d" , $i)] * 1) .'</td><!-- 탈퇴 회원수 -->
					</tr>
				';

			}
			echo '</table>';
			exit;
			break;
		// -------------- 회원 상태 분석 - 년별 --------------


		// -------------- 회원 상태 분석 - 시간별 --------------
		case "type_hour_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_member_type_hour_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));

			echo '
				<table border=1>
					<tr>
						<td>시간</td>
						<td>가입 회원수</td>
						<td>승인 회원수</td>
						<td>휴면 회원수</td>
						<td>탈퇴 회원수</td>
					</tr>
			';


			// ------- 회원 상태 분석 -기간 내 승인 회원수 - 날짜별 목록 -------
			$mem_date = _MQ_assoc(" SELECT  HOUR(in_auth_date) as rdate , count(*) as total_plus FROM smart_individual  WHERE  DATE(in_auth_date) between '". $pass_date ."' and '". $pass_edate ."' and in_auth = 'Y' GROUP BY rdate ORDER BY rdate ASC");
			foreach($mem_date as $k=>$v){
				$arr_data['auth'][$v['rdate']] = $v['total_plus'];
			}

			// ------- 회원 상태 분석 -기간 내 탈퇴 회원수 - 날짜별 목록 -------
			$mem_date = _MQ_assoc(" SELECT  HOUR(in_odate) as rdate , count(*) as total_plus FROM smart_individual  WHERE  DATE(in_odate) between '". $pass_date ."' and '". $pass_edate ."' and in_out = 'Y' GROUP BY rdate ORDER BY rdate ASC");
			foreach($mem_date as $k=>$v){
				$arr_data['out'][$v['rdate']] = $v['total_plus'];
			}

			// ------- 회원 상태 분석 -기간 내 휴면 회원수 - 날짜별 목록 -------
			$mem_date = _MQ_assoc("
				SELECT
					HOUR(ins.ins_rdate) as rdate , count(*) as total_plus
				FROM smart_individual_sleep as ins
				INNER JOIN smart_individual as ind ON (ind.in_id = ins.in_id and ind.in_sleep_type = 'Y')
				WHERE
					DATE(ins.ins_rdate) between '". $pass_date ."' and '". $pass_edate ."'
				GROUP BY rdate
				ORDER BY rdate ASC
			");
			foreach($mem_date as $k=>$v){
				$arr_data['sleep'][$v['rdate']] = $v['total_plus'];
			}


			// ------- 기간내 가입 회원 수 -------
			$mem_date = _MQ_assoc(" SELECT  HOUR(in_rdate) as rdate , count(*) as total_plus FROM smart_individual  WHERE  DATE(in_rdate) between '". $pass_date ."' and '". $pass_edate ."' GROUP BY rdate ORDER BY rdate ASC");
			foreach($mem_date as $k=>$v){
				$arr_data['join'][$v['rdate']] = $v['total_plus'];
			}

			// ------- 회원 상태 분석 - 날짜별 목록 -------
			for($i=0 ; $i<=23 ; $i++){

				echo '
					<tr>
						<td>'. $i .'시</td><!-- 시간 -->
						<td>'. number_format($arr_data['join'][$i] * 1) .'</td><!-- 가입 회원수 -->
						<td>'. number_format($arr_data['auth'][$i] * 1) .'</td><!-- 승인 회원수 -->
						<td>'. number_format($arr_data['sleep'][$i] * 1) .'</td><!-- 휴면 회원수 -->
						<td>'. number_format($arr_data['out'][$i] * 1) .'</td><!-- 탈퇴 회원수 -->
					</tr>
				';
			}

				// ------- 회원 상태 분석 - 날짜별 목록 -------
			echo '</table>';
			exit;
			break;
		// -------------- 회원 상태 분석 - 시간별 --------------



		// -------------- 회원 상태 분석 - 요일별 --------------
		case "type_week_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_member_type_hour_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));

			echo '
				<table border=1>
					<tr>
						<td>요일</td>
						<td>가입 회원수</td>
						<td>승인 회원수</td>
						<td>휴면 회원수</td>
						<td>탈퇴 회원수</td>
					</tr>
			';


			// ------- 회원 상태 분석 -기간 내 승인 회원수 - 날짜별 목록 -------
			$mem_date = _MQ_assoc(" SELECT  DATE_FORMAT(in_auth_date, '%w') as rdate , count(*) as total_plus FROM smart_individual  WHERE  DATE(in_auth_date) between '". $pass_date ."' and '". $pass_edate ."' and in_auth = 'Y' GROUP BY rdate ORDER BY rdate ASC");
			foreach($mem_date as $k=>$v){
				$arr_data['auth'][$v['rdate']] = $v['total_plus'];
			}

			// ------- 회원 상태 분석 -기간 내 탈퇴 회원수 - 날짜별 목록 -------
			$mem_date = _MQ_assoc(" SELECT  DATE_FORMAT(in_odate, '%w') as rdate , count(*) as total_plus FROM smart_individual  WHERE  DATE(in_odate) between '". $pass_date ."' and '". $pass_edate ."' and in_out = 'Y' GROUP BY rdate ORDER BY rdate ASC");
			foreach($mem_date as $k=>$v){
				$arr_data['out'][$v['rdate']] = $v['total_plus'];
			}

			// ------- 회원 상태 분석 -기간 내 휴면 회원수 - 날짜별 목록 -------
			$mem_date = _MQ_assoc("
				SELECT
					DATE_FORMAT(ins.ins_rdate, '%w') as rdate , count(*) as total_plus
				FROM smart_individual_sleep as ins
				INNER JOIN smart_individual as ind ON (ind.in_id = ins.in_id and ind.in_sleep_type = 'Y')
				WHERE
					DATE(ins.ins_rdate) between '". $pass_date ."' and '". $pass_edate ."'
				GROUP BY rdate
				ORDER BY rdate ASC
			");
			foreach($mem_date as $k=>$v){
				$arr_data['sleep'][$v['rdate']] = $v['total_plus'];
			}


			// ------- 기간내 가입 회원 수 -------
			$mem_date = _MQ_assoc(" SELECT  DATE_FORMAT(in_rdate, '%w') as rdate , count(*) as total_plus FROM smart_individual  WHERE  DATE(in_rdate) between '". $pass_date ."' and '". $pass_edate ."' GROUP BY rdate ORDER BY rdate ASC");
			foreach($mem_date as $k=>$v){
				$arr_data['join'][$v['rdate']] = $v['total_plus'];
			}

			// ------- 회원 상태 분석 - 날짜별 목록 -------
			for($i=0 ; $i<=6 ; $i++){

				echo '
					<tr>
						<td>'. week_name($i , '요일') . '</td><!-- 요일 -->
						<td>'. number_format($arr_data['join'][$i] * 1) .'</td><!-- 가입 회원수 -->
						<td>'. number_format($arr_data['auth'][$i] * 1) .'</td><!-- 승인 회원수 -->
						<td>'. number_format($arr_data['sleep'][$i] * 1) .'</td><!-- 휴면 회원수 -->
						<td>'. number_format($arr_data['out'][$i] * 1) .'</td><!-- 탈퇴 회원수 -->
					</tr>
				';
			}

				// ------- 회원 상태 분석 - 날짜별 목록 -------
			echo '</table>';
			exit;
			break;
		// -------------- 회원 상태 분석 - 요일별 --------------

	// -------------- 회원 상태 분석  ----------------------------







	// -------------- 회원 포인트 분석  ----------------------------

		// -------------- 회원 포인트 분석 - 일별 --------------
		case "point_day_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_member_point_day_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m', time()));

			echo '
				<table border=1>
					<tr>
						<td>날짜</td>
						<td>기간 내 지급(예정)/사용 적립금 합계</td>
						<td>적립금 지급예정액</td>
						<td>적립금 지급액</td>
						<td>적립금 사용액</td>
					</tr>
			';

			$arr_data = array();
			$mres1 = _MQ_assoc("
				SELECT
					DATE(pl_rdate) as rdate ,
					SUM(IFNULL(pl_point,0)) as total_sum,
					SUM( IFNULL( IF( pl_status = 'N' and pl_point >= 0 , pl_point , 0 ) , 0 ) ) as pay_ing
				FROM smart_point_log
				WHERE
					LEFT(pl_rdate,7) = '". $pass_date ."'
				GROUP BY rdate
				ORDER BY rdate ASC
			");
			$mres2 = _MQ_assoc("
				SELECT
					pl_appdate as rdate ,
					SUM( IFNULL( IF( pl_status = 'Y' and pl_point >= 0 , pl_point , 0 ) , 0 ) ) as pay_end,
					SUM( IFNULL( IF( pl_status = 'Y' and pl_point < 0 , pl_point , 0 ) , 0 ) ) as use_end
				FROM smart_point_log
				WHERE
					LEFT(pl_appdate,7) = '". $pass_date ."'
				GROUP BY rdate
				ORDER BY rdate ASC
			");
			$mem_date = array_merge($mres1 , $mres2);
			foreach($mem_date as $k=>$v){

				$arr_data['pay_ing'][DATE("Ymd" , strtotime($v['rdate']))] = $v['pay_ing'];
				$arr_data['pay_end'][DATE("Ymd" , strtotime($v['rdate']))] = $v['pay_end'];
				$arr_data['use_end'][DATE("Ymd" , strtotime($v['rdate']))] = $v['use_end'];
				// 총액
				$arr_tot_cumul[DATE("d" , strtotime($v['rdate']))] = array(
					'cnt' => $arr_tot_cumul[DATE("d" , strtotime($v['rdate']))]['cnt'] + $v['total_sum'] // 총액
				);

			}

			foreach( $arr_tot_cumul as $mem_datek => $mem_datev ){

				$app_date = $Select_Year . $Select_Month . sprintf("%02d" , $mem_datek);

				echo '
					<tr>
						<td>'. date("Y-m-d" , strtotime($app_date)) .'</td><!-- 날짜 -->
						<td>'. number_format($mem_datev['cnt'] * 1) .'</td>
						<td>'. number_format($arr_data['pay_ing'][$app_date] * 1) .'</td>
						<td>'. number_format($arr_data['pay_end'][$app_date] * 1) .'</td>
						<td>'. number_format($arr_data['use_end'][$app_date] * -1) .'</td>
					</tr>
				';

			}
			echo '</table>';
			exit;
			break;
		// -------------- 회원 포인트 분석 - 일별 --------------

		// -------------- 회원 포인트 분석 - 월별 --------------
		case "point_month_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_member_point_month_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y', time()));

			echo '
				<table border=1>
					<tr>
						<td>년월</td>
						<td>기간 내 지급(예정)/사용 적립금 합계</td>
						<td>적립금 지급예정액</td>
						<td>적립금 지급액</td>
						<td>적립금 사용액</td>
					</tr>
			';

			$arr_data = array();
			$mres1 = _MQ_assoc("
				SELECT
					LEFT(pl_rdate,7) as rdate ,
					SUM(IFNULL(pl_point,0)) as total_sum,
					SUM( IFNULL( IF( pl_status = 'N' and pl_point >= 0 , pl_point , 0 ) , 0 ) ) as pay_ing
				FROM smart_point_log
				WHERE
					LEFT(pl_rdate,4) = '". $pass_date ."'
				GROUP BY rdate
				ORDER BY rdate ASC
			");
			$mres2 = _MQ_assoc("
				SELECT
					LEFT(pl_appdate,7) as rdate ,
					SUM( IFNULL( IF( pl_status = 'Y' and pl_point >= 0 , pl_point , 0 ) , 0 ) ) as pay_end,
					SUM( IFNULL( IF( pl_status = 'Y' and pl_point < 0 , pl_point , 0 ) , 0 ) ) as use_end
				FROM smart_point_log
				WHERE
					LEFT(pl_appdate,4) = '". $pass_date ."'
				GROUP BY rdate
				ORDER BY rdate ASC
			");
			$mem_date = array_merge($mres1 , $mres2);
			foreach($mem_date as $k=>$v){

				$arr_data['pay_ing'][$v['rdate']] = $v['pay_ing'];
				$arr_data['pay_end'][$v['rdate']] = $v['pay_end'];
				$arr_data['use_end'][$v['rdate']] = $v['use_end'];
				// 총액
				$arr_tot_cumul[$v['rdate']] = array(
					'cnt' => $arr_tot_cumul[$v['rdate']]['cnt'] + $v['total_sum'] // 총액
				);

			}

			foreach( $arr_tot_cumul as $mem_datek => $mem_datev ){
				$ex = explode("-" , $mem_datek);
				echo '
					<tr>
						<td>'. $ex[0].'년 ' . $ex[1] . '월</td><!-- 년월 -->
						<td>'. number_format($mem_datev['cnt'] * 1) .'</td>
						<td>'. number_format($arr_data['pay_ing'][$mem_datek] * 1) .'</td>
						<td>'. number_format($arr_data['pay_end'][$mem_datek] * 1) .'</td>
						<td>'. number_format($arr_data['use_end'][$mem_datek] * -1) .'</td>
					</tr>
				';

			}
			echo '</table>';
			exit;
			break;
		// -------------- 회원 포인트 분석 - 월별 --------------

		// -------------- 회원 포인트 분석 - 년별 --------------
		case "point_year_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_member_point_year_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			echo '
				<table border=1>
					<tr>
						<td>년</td>
						<td>기간 내 지급(예정)/사용 적립금 합계</td>
						<td>적립금 지급예정액</td>
						<td>적립금 지급액</td>
						<td>적립금 사용액</td>
					</tr>
			';

			$arr_data = array();
			$mres1 = _MQ_assoc("
				SELECT
					LEFT(pl_rdate,4) as rdate ,
					SUM(IFNULL(pl_point,0)) as total_sum,
					SUM( IFNULL( IF( pl_status = 'N' and pl_point >= 0 , pl_point , 0 ) , 0 ) ) as pay_ing
				FROM smart_point_log
				GROUP BY rdate
				ORDER BY rdate ASC
			");
			$mres2 = _MQ_assoc("
				SELECT
					LEFT(pl_appdate,4) as rdate ,
					SUM( IFNULL( IF( pl_status = 'Y' and pl_point >= 0 , pl_point , 0 ) , 0 ) ) as pay_end,
					SUM( IFNULL( IF( pl_status = 'Y' and pl_point < 0 , pl_point , 0 ) , 0 ) ) as use_end
				FROM smart_point_log
				GROUP BY rdate
				ORDER BY rdate ASC
			");
			$mem_date = array_merge($mres1 , $mres2);
			foreach($mem_date as $k=>$v){

				$arr_data['pay_ing'][$v['rdate']] = $v['pay_ing'];
				$arr_data['pay_end'][$v['rdate']] = $v['pay_end'];
				$arr_data['use_end'][$v['rdate']] = $v['use_end'];
				// 총액
				$arr_tot_cumul[$v['rdate']] = array(
					'cnt' => $arr_tot_cumul[$v['rdate']]['cnt'] + $v['total_sum'] // 총액
				);

			}

			foreach( $arr_tot_cumul as $mem_datek => $mem_datev ){
				echo '
					<tr>
						<td>'. $mem_datek . '년</td><!-- 년 -->
						<td>'. number_format($mem_datev['cnt'] * 1) .'</td>
						<td>'. number_format($arr_data['pay_ing'][$mem_datek] * 1) .'</td>
						<td>'. number_format($arr_data['pay_end'][$mem_datek] * 1) .'</td>
						<td>'. number_format($arr_data['use_end'][$mem_datek] * -1) .'</td>
					</tr>
				';

			}
			echo '</table>';
			exit;
			break;
		// -------------- 회원 포인트 분석 - 월별 --------------

		// -------------- 회원 포인트 분석 - 요일별 --------------
		case "point_week_search":

			$toDay = date('YmdHis');
			## Exel 파일로 변환 #############################################
			header("Content-Type: application/vnd.ms-excel;");
			header("Content-Disposition: attachment; filename=static_member_point_day_". $toDay .".xls");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

			$pass_date = $Select_Year ."-". $Select_Month ."-". $Select_Day;
			$pass_date = (rm_str($pass_date) > 0 ? $pass_date : date('Y-m-d', time()));

			$pass_edate = $Select_eYear ."-". $Select_eMonth ."-". $Select_eDay;
			$pass_edate = (rm_str($pass_edate) > 0 ? $pass_edate : date('Y-m-d', time()));

			echo '
				<table border=1>
					<tr>
						<td>요일</td>
						<td>기간 내 지급(예정)/사용 적립금 합계</td>
						<td>적립금 지급예정액</td>
						<td>적립금 지급액</td>
						<td>적립금 사용액</td>
					</tr>
			';

			$arr_data = array();
			$mres1 = _MQ_assoc("
				SELECT
					DATE_FORMAT(pl_rdate, '%w') as rdate ,
					SUM(IFNULL(pl_point,0)) as total_sum,
					SUM( IFNULL( IF( pl_status = 'N' and pl_point >= 0 , pl_point , 0 ) , 0 ) ) as pay_ing
				FROM smart_point_log
				WHERE
					DATE(pl_rdate) between '". $pass_date ."' and '". $pass_edate ."'
				GROUP BY rdate
				ORDER BY rdate ASC
			");
			$mres2 = _MQ_assoc("
				SELECT
					DATE_FORMAT(pl_appdate, '%w') as rdate ,
					SUM( IFNULL( IF( pl_status = 'Y' and pl_point >= 0 , pl_point , 0 ) , 0 ) ) as pay_end,
					SUM( IFNULL( IF( pl_status = 'Y' and pl_point < 0 , pl_point , 0 ) , 0 ) ) as use_end
				FROM smart_point_log
				WHERE
					pl_appdate between '". $pass_date ."' and '". $pass_edate ."'
				GROUP BY rdate
				ORDER BY rdate ASC
			");
			$mem_date = array_merge($mres1 , $mres2);
			foreach($mem_date as $k=>$v){

				$arr_data['pay_ing'][$v['rdate']] = $v['pay_ing'];
				$arr_data['pay_end'][$v['rdate']] = $v['pay_end'];
				$arr_data['use_end'][$v['rdate']] = $v['use_end'];
				// 총액
				$arr_tot_cumul[$v['rdate']] = array(
					'cnt' => $arr_tot_cumul[$v['rdate']]['cnt'] + $v['total_sum'] // 총액
				);

			}

			foreach( $arr_tot_cumul as $mem_datek => $mem_datev ){

				echo '
					<tr>
						<td>'. week_name( $mem_datek , '요일') .'</td><!-- 요일 -->
						<td>'. number_format($mem_datev['cnt'] * 1) .'</td>
						<td>'. number_format($arr_data['pay_ing'][$mem_datek] * 1) .'</td>
						<td>'. number_format($arr_data['pay_end'][$mem_datek] * 1) .'</td>
						<td>'. number_format($arr_data['use_end'][$mem_datek] * -1) .'</td>
					</tr>
				';

			}
			echo '</table>';
			exit;
			break;
		// -------------- 회원 포인트 분석 - 요일별 --------------

	// -------------- 회원 포인트 분석  ----------------------------





	}
exit;