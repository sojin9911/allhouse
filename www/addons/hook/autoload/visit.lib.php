<?php

	// JJC : 방문통계 : 2018-03-05


	// ----- JJC :  방문통계 함수 설정 영역 : 2017-12-21 ------------------------------------------------------------
		// URL에서 키워드를 추출
		if(!function_exists('GetUrlKeyword')) {
			function GetUrlKeyword($url) {
				$KeyParam = array('query', 'q'); // query: 네이버, q: 다음/네이트
				$arr_url = parse_url($url);
				if($arr_url['query']) {
					foreach(explode('&', $arr_url['query']) as $k=>$v) {
						$Ep = explode('=', $v);
						if(!in_array($Ep[0], $KeyParam)) continue;
						return rawurldecode($Ep[1]);
					}
				}
			}
		}

		// 로봇 , 크롤러 체크
		if(!function_exists('_bot_detected')) {
			function _bot_detected() {
				return (
					isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT'])
				);
			}
		}
	// ----- JJC :  방문통계 함수 설정 영역 : 2017-12-21 ------------------------------------------------------------



	# ------------- (B)모든 inc.php가 호출 되는 위치의 함수 -------------
	function hook_log_insert() {
		global $_SERVER , $_COOKIE , $mem_info;

		// ----- 접속 쿠키 적용 ------------------------------------------------------------
		//			- 방문통계, 구매자분석, 구매전화에 사용함.
		$_system_http_host = (  $_SERVER['HTTPS'] == 'on' ? ($_SERVER['SSL_TLS_SNI']?$_SERVER['SSL_TLS_SNI']:reset(explode(':', $_SERVER['HTTP_HOST']))) : $_SERVER['HTTP_HOST']) ;
		if(!$_COOKIE["AuthCounterCOOKIEID"]) {
			$appCounterCOOKIEID = md5(serialize($_SERVER) . mt_rand(0,9999999));
			samesiteCookie("AuthCounterCOOKIEID", $appCounterCOOKIEID , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $_system_http_host)))); // 로그인에도 변경되지 않음.
		}
		$appCounterCOOKIEID = $_COOKIE["AuthCounterCOOKIEID"] ? $_COOKIE["AuthCounterCOOKIEID"] : $appCounterCOOKIEID;
		// ----- 접속 쿠키 적용 ------------------------------------------------------------


		// ----- 카운터 설정 영역 ------------------------------------------------------------
			// 로봇체크
			$app_counter_use = _bot_detected() ? 'N' : 'Y';

			if($app_counter_use == 'Y'){
				// 카운터 사용여부 체크
				$cntlog_config = _MQ("select * from smart_cntlog_config WHERE clc_uid='1'");
				$app_counter_use = $cntlog_config['clc_counter_use']; // 방문통계 사용여부 - Y:사용함, N:사용하지않음
				$app_cookie_use = $cntlog_config['clc_cookie_use']; // 중복 접속설정 - A:접속하는대로 카운터 증가, T:지정된시간대로 카운터 증가, O:하루에 한번만 카운터 증가
				$app_cookie_term = $cntlog_config['clc_cookie_term']; // 중복접속시간설정 - 초단위 -- 접속설정 - T:지정된시간대로 카운터 증가일때 사용
				$app_admin_check_use = $cntlog_config['clc_admin_check_use']; // 관리자 통계포함, Y:포함함, N:포함하지않음
				$app_admin_ip = $cntlog_config['clc_admin_ip']; // 관리자접속 아이피(IP) -- 관리자 통계포함-Y일때 사용

				$app_url = $_SERVER['REQUEST_URI'] ;
				$app_referer = $_SERVER['HTTP_REFERER'];
				$app_keyword = trim(GetUrlKeyword($_SERVER['HTTP_REFERER'])) ;
				$app_ip = $_SERVER['REMOTE_ADDR'];
			}

			// 관리자 아이피 차단
			$app_counter_use = ( $app_admin_check_use == 'N' && $app_admin_ip == $app_ip ? "N" : $app_counter_use);

		// ----- 카운터 설정 영역 ------------------------------------------------------------



		// 방문통계 사용 체크
		if($app_counter_use == 'Y'){




			// 중복설정에 따른 처리
			$app_trigger = 'N'; // N:저장하지 않음. Y:저장함.
			switch($app_cookie_use){

				//접속하는대로 카운터 증가 - 쿠키가 있으면 추가하지 않음.
				case "A":
					if( !$_COOKIE['AuthCookieTerm'] ) {
						$app_trigger = 'Y';
						samesiteCookie("AuthCookieTerm" , time() , 0 ,"/" , "." . str_replace("www." , "" , reset(explode(':', $_system_http_host))));
					}
					break;

				//지정된시간대로 카운터 증가
				case "T":
				//하루에 한번만 카운터 증가
				case "O":
					// --- 쿠키 확인 - 설정시간을 넘겼으면 :: 쿠키 갱신 , 카운트 추가 ----
					$app_diff_time = time() - $_COOKIE['AuthCookieTerm'];
					$cntlog_config['clc_cookie_term'] = ( $app_cookie_use == 'O' ? 3600*24 : $cntlog_config['clc_cookie_term']); // O;하루, T:지정시간(초단위)
					if( $app_diff_time > $cntlog_config['clc_cookie_term'] ) {
						// 더블체크 - smart_cntlog_ip - 접속 - IP 기록 - 카운터증가_로봇검출 기능 관리에 저장된 정보가 있다면 카운터 추가하지 않음.
						//			--> 3일 넘긴 smart_cntlog_ip 데이터 삭제 - inc.daily.update.php
						$chk_ip = _MQ("
							SELECT
								COUNT(*) as cnt
							FROM smart_cntlog_ip
							where
								sci_ip = '". $app_ip ."' and
								DATE_ADD( sci_rdate , INTERVAL + ". ( $app_cookie_use == 'O' ? " 1 DAY " : rm_str($cntlog_config['clc_cookie_term']) ." SECOND ") ."  ) >= NOW()
						");
						if( $chk_ip['cnt'] == 0 ){
							$app_trigger = 'Y'; // 카운트 추가
							samesiteCookie("AuthCookieTerm" , time() , time() + $cntlog_config['clc_cookie_term'] ,"/" , "." . str_replace("www." , "" , reset(explode(':', $_system_http_host))));
							_MQ_noreturn(" INSERT INTO smart_cntlog_ip (sci_ip, sci_rdate) VALUES ('". $app_ip ."', NOW() ) ON DUPLICATE KEY UPDATE sci_rdate = NOW() ");// 아이피 정보 갱신
						}
					}
					break;

			}



			// 카운트 적용
			if($app_trigger == 'Y') {

				// 디바이스 정보 추출
				$arr_device = Get_device_info('array');

				// smart_cntlog_list - 접속자통계-기본 테이블 추가
				$queCntlogList = "
					INSERT smart_cntlog_list SET
						sc_mobile = '". (is_mobile() === true?'Y':'N') ."'
						,sc_memtype = '". (is_login() === true?'Y':'N') ."'
						,sc_date = NOW()
						,sc_mid = '". (is_login() === true ? $mem_info['in_id'] : NULL) ."'
						,sc_cookie = '". $appCounterCOOKIEID ."'
				";
				_MQ_noreturn($queCntlogList);
				$app_uid = mysql_insert_id();


				// smart_cntlog_detail - 접속자통계-상세 테이블 추가
				$queCntlogDetail = "
					INSERT smart_cntlog_detail SET
						sc_uid = '". $app_uid ."'
						,sc_url = '". $app_url ."'
						,sc_referer = '".$app_referer."'
						,sc_keyword = '". $app_keyword ."'
						,sc_ip = '". $app_ip ."'
						,sc_device = '".$arr_device['agent']."'
						,sc_os = '".$arr_device['os']."'
						,sc_browser = '".$arr_device['browser']."'
				";
				_MQ_noreturn($queCntlogDetail);


				// smart_cntlog_browser - 접속 - 브라우저 통계 적용
				_MQ_noreturn(" INSERT INTO smart_cntlog_browser (scb_date, scb_browser , scb_cnt_pc , scb_cnt_mo) VALUES (CURDATE(), '".$arr_device['browser']."' , '". (is_mobile() === true?'0':'1') ."' , '". (is_mobile() === true?'1':'0') ."') ON DUPLICATE KEY UPDATE ". (is_mobile() === true?'scb_cnt_mo=scb_cnt_mo+1':'scb_cnt_pc=scb_cnt_pc+1') );

				//smart_cntlog_device - 접속 - Device 통계
				_MQ_noreturn(" INSERT INTO smart_cntlog_device (scd_date, scd_device , scd_cnt) VALUES (CURDATE(), '". (is_mobile() === true?'MOBILE':'PC') ."' , 1) ON DUPLICATE KEY UPDATE scd_cnt = scd_cnt + 1 ");

				//smart_cntlog_keyword - 접속 - 키워드 통계
				_MQ_noreturn(" INSERT INTO smart_cntlog_keyword (sck_date, sck_keyword , sck_cnt_pc , sck_cnt_mo) VALUES (CURDATE(), '".$app_keyword."' , '". (is_mobile() === true?'0':'1') ."' , '". (is_mobile() === true?'1':'0') ."') ON DUPLICATE KEY UPDATE ". (is_mobile() === true?'sck_cnt_mo=sck_cnt_mo+1':'sck_cnt_pc=sck_cnt_pc+1') );

				//smart_cntlog_os - 접속 - OS 통계
				_MQ_noreturn(" INSERT INTO smart_cntlog_os (sco_date, sco_os , sco_cnt_pc , sco_cnt_mo) VALUES (CURDATE(), '".$arr_device['os']."' , '". (is_mobile() === true?'0':'1') ."' , '". (is_mobile() === true?'1':'0') ."') ON DUPLICATE KEY UPDATE ". (is_mobile() === true?'sco_cnt_mo=sco_cnt_mo+1':'sco_cnt_pc=sco_cnt_pc+1') );

				//smart_cntlog_route - 접속 - 경로 통계
				$arr_referer = parse_url($_SERVER['HTTP_REFERER']);
				$arr_referer['host'] = ($arr_referer['host'] == $_SERVER['HTTP_HOST'] ? '' : $arr_referer['host']);
				_MQ_noreturn(" INSERT INTO smart_cntlog_route (scr_date, scr_route , scr_cnt_pc , scr_cnt_mo) VALUES (CURDATE(), '". $arr_referer['host'] ."' , '". (is_mobile() === true?'0':'1') ."' , '". (is_mobile() === true?'1':'0') ."') ON DUPLICATE KEY UPDATE ". (is_mobile() === true?'scr_cnt_mo=scr_cnt_mo+1':'scr_cnt_pc=scr_cnt_pc+1') );

				// 회원정보 있을 경우 적용함.
				//			최초 접근시 회원로그인 되지 않을 것으로 예상됨..
				if(is_login() === true) {
					//smart_cntlog_sex - 접속 - 회원성별 통계
					_MQ_noreturn(" INSERT INTO smart_cntlog_sex (scs_date, scs_sex , scs_cnt_pc, scs_cnt_mo) VALUES (CURDATE(), '". $mem_info['in_sex'] ."' , '". (is_mobile() === true?'0':'1') ."' , '". (is_mobile() === true?'1':'0') ."') ON DUPLICATE KEY UPDATE ". (is_mobile() === true?'scs_cnt_mo=scs_cnt_mo+1':'scs_cnt_pc=scs_cnt_pc+1') );

					//smart_cntlog_age - 접속 - 회원연령 통계
					$app_age = DATE("Y") - substr($mem_info['in_birth'],0,4);
					_MQ_noreturn(" INSERT INTO smart_cntlog_age (sca_date, sca_age , sca_cnt_pc , sca_cnt_mo) VALUES (CURDATE(), '". $app_age ."' , '". (is_mobile() === true?'0':'1') ."' , '". (is_mobile() === true?'1':'0') ."') ON DUPLICATE KEY UPDATE ". (is_mobile() === true?'sca_cnt_mo=sca_cnt_mo+1':'sca_cnt_pc=sca_cnt_pc+1') );
				}

				// 전체 통계 추가
				_MQ_noreturn(" UPDATE smart_cntlog_config SET clc_total_num = clc_total_num + 1 WHERE clc_uid = '1' ");

			}

		}

	}
	# ------------- (B)모든 inc.php가 호출 되는 위치의 함수 -------------











	// 로그인시 로그 업데이트 처리
	//			- type : login , join , sns(sns 로그인)
	/*
		이미 저장된 로그 정보에 회원정보 업데이트 적용
	*/
	function hook_log_id_update($type , $login_id , $login_pw) {

		global $_SERVER , $_COOKIE;

		// 아이디와 쿠키가 같지 않아야 하며, 관리자가 아니어야 함.
		if( !is_admin()) {

			// 일반 로그인/ 회원가입일 경우---// 회원정보 추출
			if( IN_ARRAY($type , array('login' , 'join')) && $login_id && $login_pw){
				$mem_info = _MQ(" select * from smart_individual where in_id = '" . addslashes($login_id) . "' and in_pw = password('" . addslashes($login_pw) . "')  ");
			}
			// SNS 로그인/ 회원가입일 경우---// 회원정보 추출
			elseif($type == 'sns' && $login_id ){
				$mem_info = _MQ(" select * from smart_individual where in_id = '" . addslashes($login_id) . "'  ");
			}

			// --------------- 방문통계 영역 적용 ---------------
			if($mem_info['in_id']) {

				// smart_cntlog_list - 접속자통계-기본 테이블 - 3일 이내 $_cookie 정보 UPDATE
				$queCntlogList = " UPDATE smart_cntlog_list SET sc_memtype = 'Y', sc_mid = '". $mem_info['in_id'] ."' WHERE sc_cookie = '". addslashes($_COOKIE["AuthCounterCOOKIEID"]) ."' ";
				_MQ_noreturn($queCntlogList);
				$app_cnt = mysql_affected_rows();//최근 MySQL 작업으로 변경된 행 개수를 얻음

				if($app_cnt > 0) {
					//smart_cntlog_sex - 접속 - 회원성별 통계
					_MQ_noreturn(" INSERT INTO smart_cntlog_sex (scs_date, scs_sex , scs_cnt_pc, scs_cnt_mo) VALUES (CURDATE(), '". $mem_info['in_sex'] ."' , '". (is_mobile() === true?'0': $app_cnt ) ."' , '". (is_mobile() === true? $app_cnt :'0') ."') ON DUPLICATE KEY UPDATE ". (is_mobile() === true?'scs_cnt_mo=scs_cnt_mo+' . $app_cnt :'scs_cnt_pc=scs_cnt_pc+' . $app_cnt) );
					//smart_cntlog_age - 접속 - 회원연령 통계
					if( $mem_info['in_birth'] > 0 ) {
						$app_age = DATE("Y") - substr($mem_info['in_birth'],0,4);
						_MQ_noreturn(" INSERT INTO smart_cntlog_age (sca_date, sca_age , sca_cnt_pc , sca_cnt_mo) VALUES (CURDATE(), '". $app_age ."' , '". (is_mobile() === true?'0': $app_cnt ) ."' , '". (is_mobile() === true? $app_cnt :'0') ."') ON DUPLICATE KEY UPDATE ". (is_mobile() === true?'sca_cnt_mo=sca_cnt_mo+' . $app_cnt :'sca_cnt_pc=sca_cnt_pc+' . $app_cnt) );
					}
				}

			}
			// --------------- 방문통계 영역 적용 ---------------

		}

	}
	# ------------- (B) 로그인시 로그 업데이트 처리 -------------