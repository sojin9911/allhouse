<?php
# 로그인 & 로그아웃 & 팝업닫기
include_once(dirname(__FILE__).'/inc.php');
if( !$_mode ) error_msg("잘못된 접근입니다.");
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


switch($_mode){

	// - 로그인 ---
	case "login":
		// --사전 체크 ---
		$login_id = trim(nullchk($login_id , "아이디를 입력해주세요." , "" , "ALT")); // nullchk - alert 형식으로 return
        if($_mode2<>"master_login"){
            $login_password = trim(nullchk($login_password , "패스워드를 입력해주세요." , "" , "ALT"));// nullchk - alert 형식으로 return
        }
		// --사전 체크 ---

		// 아이디 , 비밀번호를 통한 회원 확인
		$r = _MQ("SELECT * FROM smart_individual where in_id='{$login_id}'  ");
		if( sizeof($r) == 0 ) {
			error_alt("회원정보가 없습니다.\\n\\n다시 한번 확인해 주세요.");
		}

		if( $r[in_out] == 'Y' ) {
			error_alt("탈퇴한 회원입니다.\\n\\n로그인을 원하신다면 관리자에게 문의주시기 바랍니다.");
		}

		// LCY 휴면계정 체크
		if($r['in_sleep_type'] == 'Y'){
			// LCY 2018-03-07 -- 휴면회원 이메일 인증없이 처리가능한경우
			if( $r['in_sleep_request'] == 'Y'){

				$r = _MQ("SELECT * FROM smart_individual_sleep where in_id='{$login_id}'  ");
				$tmpr = _MQ("select password('". $login_password ."') as pw ");
				$app_login_password = $tmpr[pw];
				if( !($r[in_pw] == $app_login_password && $app_login_password)) {// --- modify source ---

					// LCY - 로그인 틀린 횟수 기록
					$ad_cnt = access_deny_cnt('get');
					if($ad_cnt >= $siteInfo['member_login_cnt'] &&  $siteInfo['member_login_cnt'] > 0 ){
						loginchk_insert($login_id, "deny",true);
					}
					error_alt("비밀번호가 맞지 않습니다.\\n\\n다시 한번 확인해 주세요.");

				}else{
					member_sleep_return( $r['in_id'] );
					_MQ_noreturn("update smart_individual set in_sleep_request = 'N' where in_id = '". $r['in_id']."'  ");
				}

			}else{
				error_frame_loc("/?pn=member.sleep_form&_id=" . $r['in_id']);
			}
		}


		// --- add source ---
		// 관리자 로그인 처리
		if( $_mode2 == "master_login" && $_COOKIE["AuthAdmin"] == $siteAdmin['a_uid'] ){

			// -- 세션체크
			if( AdminLoginCheck() !== true){ error_msg("잘못된 접근입니다."); }

			// 비밀번호 추출 통한 회원 확인
			$admtmpr = _MQ("SELECT in_pw FROM smart_individual where in_id='{$login_id}'  ");
			$app_login_password = $admtmpr[in_pw];
		}
		else {

			// -- 승인 미승인 추가
			if( $r['in_auth'] != 'Y'){
				error_alt("가입에 대한 승인처리가 되지 않았습니다.\\n\\n로그인을 원하신다면 관리자에게 문의주시기 바랍니다.");
			}

			$tmpr = _MQ("select password('". $login_password ."') as pw ");
			$app_login_password = $tmpr[pw];
		}
		// --- add source ---
		if( !($r[in_pw] == $app_login_password && $app_login_password)) {// --- modify source ---
			// LCY - 로그인 틀린 횟수 기록
			$ad_cnt = access_deny_cnt('get');

			if($ad_cnt >= $siteInfo['member_login_cnt'] &&  $siteInfo['member_login_cnt'] > 0 ){
				loginchk_insert($login_id, "deny",true);
			}

			error_alt("비밀번호가 맞지 않습니다.\\n\\n다시 한번 확인해 주세요.");
		}

		// SNS 계정연동 로그인
		if($AuthSNSEncID) {
			$sns_info = unserialize(onedaynet_decode($AuthSNSEncID));
			if($sns_info['type'] == 'facebook') _MQ_noreturn(" update smart_individual set sns_join = 'Y', fb_join = 'Y', fb_encid = '{$sns_info['id']}' where in_id='{$login_id}' ");
			else if($sns_info['type'] == 'kakao') _MQ_noreturn(" update smart_individual set sns_join = 'Y', ko_join = 'Y', ko_encid = '{$sns_info['id']}' where in_id='{$login_id}' ");
			else if($sns_info['type'] == 'naver') _MQ_noreturn(" update smart_individual set sns_join = 'Y', nv_join = 'Y', nv_encid = '{$sns_info['id']}' where in_id='{$login_id}' ");
			samesiteCookie('AuthSNSEncID', '', time()-3600 , '/', '.'.str_replace('www.', '', reset(explode(':', $system['host'])))); // SNS 고유정보 제거
		}


		// 회원정보 업데이트
		_MQ_noreturn("update smart_individual set in_ldate=now() where in_id='{$login_id}'");

		// 로그인 쿠키 적용
		samesiteCookie("AuthIndividualMember", $login_id , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));

		//LCY 로그인 틀린 횟수 기록 - 로그인성궁 시 초기화
		access_deny_cnt('del');

		// 이메일 저장 체크시 쿠키 적용
		if( $login_id_chk == "Y" ) {
			samesiteCookie("AuthSDIndividualIDChk", $login_id , time()+3600*24*30 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		}
		else {
			samesiteCookie("AuthSDIndividualIDChk", "" , time() -3600 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		}

		// 로그인 체크
		$login_trigger = loginchk_insert($login_id , "individual");

		// LCY 로그인 시 장바구니를 아이디로 변경하기 -- >
		if($_mode2 <> "master_login") _MQ_noreturn("update smart_cart set c_cookie='". $login_id ."' where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' "); // SSJ : 관리자 자동 로그인 시 장바구니 연동 막기 : 2021-05-27
		samesiteCookie("AuthShopCOOKIEID", $login_id , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));

		// LCY 장바구니 중복 체크하여 수량 증가
		$r = _MQ_assoc(" select c_cookie, c_pcode, c_pouid, sum(c_cnt) as sum, IFNULL(sum(c_point),0) as sum_point, count(*) as cnt from smart_cart where c_cookie = '".$login_id."' group by c_cookie, c_pcode, c_pouid having cnt > 1 ");
		foreach( $r as $k=>$v ){
			$p = _MQ(" select p_stock from smart_product where p_code = '".$v[c_pcode]."' ");
			 $v[sum] = $v[sum] > $p[p_stock] ? $p[p_stock] : $v[sum];
			 $sr = _MQ(" select c_uid from smart_cart where c_cookie = '". $v['c_cookie'] ."' and c_pcode = '". $v['c_pcode'] ."' and c_pouid = '". $v['c_pouid'] ."' order by c_uid desc limit 1 ");
			_MQ_noreturn(" update smart_cart set c_cnt = '".$v[sum]."' , c_point = '".$v[sum_point]."' where c_uid = '".$sr['c_uid']."' and c_cookie = '".$v[c_cookie]."' ");
			_MQ_noreturn(" delete from smart_cart where c_cookie = '".$v[c_cookie]."' and c_pcode  = '".$v[c_pcode]."' and c_pouid = '".$v[c_pouid]."' and c_uid != '".$sr['c_uid']."' ");
		}

		// 페이지 이동 --> 단 로그인/회원가입페이지일 경우 메인으로 돌림 -->  모두 팝업이므로 의미없음
		if(!$_rurl) $_rurl = "/";
		if($_rurl == 'index.php') $_rurl = "/";

		// LCY 관리자가 설정할 일 수 마다비밀번호 변경 체크
		$cpw_que = _MQ("select in_pw_rdate from smart_individual where in_id = '".$login_id."' and  in_pw_rdate  < '". date('Y-m-d H:i:s',strtotime("- ". $siteInfo['member_cpw_period'] ." month"))."'");
		if(count($cpw_que) > 0){
			$_rurl = "/?pn=member.password_form&_ckval=".sha1(date('H'));
		}
		UserLogin($login_id); // 세션로그인

		// === 비회원 구매 설정 추가 통합 kms 2019-06-20 ====
		if ( preg_match ( "/(?=pn)/", enc("d",$_rurl) )) {
			error_frame_loc(str_replace("?&","?","/?".enc("d",$_rurl))); // 2020-03-17 SSJ :: 로그인 경로설정 수정
		}
		// === 비회원 구매 설정 추가 통합 kms 2019-06-20 ====

		// 페이지 이동 --> 단 로그인/회원가입페이지일 경우 메인으로 돌림 -->  모두 팝업이므로 의미없음

		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_frame_loc($_rurl);

		break;

	// - 로그아웃 ---
	case "logout":
		// 쿠키 적용
		samesiteCookie("AuthIndividualMember", "" , time() -3600 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		// LCY 장바구니 원복
		samesiteCookie("AuthShopCOOKIEID", md5(serialize($_SERVER) . mt_rand(0,9999999)) , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		UserLogout(); // 세션로그아웃

		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_frame_loc("/");
		break;
	// - 로그아웃 ---
	case "mobile_logout":
		// 쿠키 적용
		samesiteCookie("AuthIndividualMember", "" , time() -3600 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		// LCY 장바구니 원복
		samesiteCookie("AuthShopCOOKIEID", md5(serialize($_SERVER) . mt_rand(0,9999999)) , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		UserLogout(); // 세션로그아웃

		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_loc("/m");
		break;
}