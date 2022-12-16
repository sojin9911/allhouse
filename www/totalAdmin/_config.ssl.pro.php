<?PHP

	// 2017-07-11 ::: 보안서버 ::: JJC

	include "./inc.php";


	$pass_menu = $_REQUEST['pass_menu'] ? $_REQUEST['pass_menu'] : "_config.ssl.default_form";


	switch($pass_menu){

		// 보안서버 관리
		case "_config.ssl.default_form":

			// 2019-11-25 SSJ :: 대표도메인 필수 체크
			if($_ssl_check == 'Y' && $siteInfo['s_ssl_domain'] == "") error_msg("대표도메인이 설정되지 않았습니다.\\n\\n대표도메인 설정 후 다시 시도해 주시기 바랍니다.");

			$sque = "
				update smart_setup set
					s_ssl_check = '".$_ssl_check."',
					s_ssl_status = '".$_ssl_status."',
					s_ssl_sdate = '".$_ssl_sdate."',
					s_ssl_edate = '".$_ssl_edate."',
					s_ssl_port = '".$_ssl_port."',
					s_ssl_pc_img = '".$_ssl_pc_img."',
					s_ssl_pc_img_etc = '".$_ssl_pc_img_etc."',
					s_ssl_pc_sealnum = '".$_ssl_pc_sealnum."',
					s_ssl_pc_sealid = '".$_ssl_pc_sealid."'
				where
					s_uid = 1
			";
			_MQ_noreturn($sque);

			# 보안서버 상태정보 체크
			$siteInfo = get_site_info();
			/*$arr = ssl_condition_info();
			if(str_replace(array('https://', 'http://'), '', $arr['ssl_domain']) <> '') $ssl_condition = CurlExecHeader($arr['ssl_domain'].'/program/_ping.php'); // 200 이 아니면 비정상 // # 2019-04-16 SSJ :: 도메인이 입력 체크 추가
			if($ssl_condition != 200 ) {
				_MQ_noreturn(" update smart_setup set s_ssl_check = 'N' where s_uid = 1 ");// 접속비정상일 경우 비적용함..
				if($_ssl_check == 'Y') error_loc_msg( $pass_menu . ".php" , "보안서버를 사용할 수 없는 상태입니다.");
			}*/

			break;




		/* 2019-11-25 SSJ :: 보안서버 설정항목 간소화
			// 관리자 보안서버 관리
			case "_config.ssl.admin_form":

				// 보안서버 추가 적용페이지
				$arr_page = array();
				if(sizeof($page_value) > 0 ) {
					foreach($page_value as $k=>$v){
						if( trim($v)) {
							$arr_page[] = trim($v);
						}
					}
				}
				$app_page = implode("§" , array_values($arr_page));

				// 보안서버 추가 적용페이지
				$sque = "
					update smart_setup set
						s_ssl_admin_loc = '".$_ssl_admin_loc."',
						s_ssl_admin_page = '".$app_page."'
					where
						s_uid = 1
				";
				_MQ_noreturn($sque);

				break;





			// PC 사용자 보안서버 관리
			case "_config.ssl.pc_form":

				// 보안서버 추가 적용페이지
				$arr_page = array();
				if(sizeof($page_value) > 0 ) {
					foreach($page_value as $k=>$v){
						if( trim($v)) {
							$arr_page[] = trim($v);
						}
					}
				}
				$app_page = implode("§" , array_values($arr_page));

				// 보안서버 추가 적용페이지
				$sque = "
					update smart_setup set
						s_ssl_pc_loc = '".$_ssl_pc_loc."',
						s_ssl_pc_page = '".$app_page."',
						s_ssl_pc_img = '".$_ssl_pc_img."',
						s_ssl_pc_img_etc = '".$_ssl_pc_img_etc."',
						s_ssl_pc_sealnum = '".$_ssl_pc_sealnum."',
						s_ssl_pc_sealid = '".$_ssl_pc_sealid."'
					where
						s_uid = 1
				";
				_MQ_noreturn($sque);

				break;





			// 모바일 보안서버 관리
			case "_config.ssl.m_form":

				// 보안서버 추가 적용페이지
				$arr_page = array();
				if(sizeof($page_value) > 0 ) {
					foreach($page_value as $k=>$v){
						if( trim($v)) {
							$arr_page[] = trim($v);
						}
					}
				}
				$app_page = implode("§" , array_values($arr_page));

				// 보안서버 추가 적용페이지
				$sque = "
					update smart_setup set
						s_ssl_m_loc = '".$_ssl_m_loc."',
						s_ssl_m_page = '".$app_page."'
					where
						s_uid = 1
				";
				_MQ_noreturn($sque);

				break;

		*/
	}


//	// -- 보안서버 상태정보 체크 ---
//	$siteInfo = _MQ("select * from smart_setup where s_uid = 1");
//	if(HTTPS_Check()){
//		// SSL 사용안함 강제처리
//		ssl_forced_reset()
//	}
//	// -- 보안서버 상태정보 체크 ---



	error_loc_msg( $pass_menu . ".php" , "설정 내용을 적용하였습니다.");

?>