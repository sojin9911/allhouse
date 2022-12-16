<?php
# ------------------------------> DEV 변경 하세요. <---------------------- :: 내부 이미지등...
# 회원가입 & 정보수정
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// - 모드별 처리 ---
switch( $_mode ){

	// 회원가입
	case "join":

		if($siteInfo['join_spam'] == 'Y' && $siteInfo['recaptcha_api'] && $siteInfo['recaptcha_secret']) { // 2020-05-14 SSJ :: 회원가입 정책 스팸방지 설정 적용

            // 스팸방지
            $secret = $siteInfo['recaptcha_secret'];
            $response = $_POST["g-recaptcha-response"];
            $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
            $_action_result = json_decode($verify); # -- 스팸체크 결과
            if($_action_result->success==false) error_alt( "스팸방지를 확인인해 주세요.");
        }

        $is_join_auth = false; // SSJ : 본인인증여부 체크 수정 : 2021-11-22
        // 2018-10-04 SSJ :: 본인인증 사용 시
        if($siteInfo['s_join_auth_use'] == 'Y') {
            $_ordr_idxx = nullchk($_ordr_idxx , "본인 인증후 회원가입이 가능합니다." , "" , "ALT");
            $res_auth = _MQ(" select * from smart_individual_auth_log where inl_ordr_idxx = '". $_ordr_idxx ."' ");
            if($res_auth['inl_enc_cert_data']){
                // -- SSJ : KCP 본인확인 암호화 데이터 추가 패치 : 2021-11-01 ----
				$app_enc_key = $siteInfo['s_join_auth_kcb_enckey'] ? $siteInfo['s_join_auth_kcb_enckey'] : '';
				if($app_enc_key <> ''){
					$home_dir      = $_SERVER['DOCUMENT_ROOT'] . "/auth/kcp_v2"; // ct_cll 절대경로 ( bin 전까지 )
				}else{
					$home_dir      = $_SERVER['DOCUMENT_ROOT'] . "/auth/kcp"; // ct_cll 절대경로 ( bin 전까지 )
				}
                /* ============================================================================== */
                /* =   라이브러리 파일 Include                                                  = */
                /* = -------------------------------------------------------------------------- = */
				require $home_dir . "/lib/ct_cli_lib.php";
                $ct_cert = new C_CT_CLI;
                $ct_cert->mf_clear();
                // 인증데이터 복호화 함수
                // 해당 함수는 암호화된 enc_cert_data 를
                // site_cd 와 cert_no 를 가지고 복화화 하는 함수 입니다.
                // 정상적으로 복호화 된경우에만 인증데이터를 가져올수 있습니다.
                $opt = "1" ; // 복호화 인코딩 옵션 ( UTF - 8 사용시 "1" )
				if($app_enc_key <> ''){
					$ct_cert->decrypt_enc_cert( $home_dir , $app_enc_key , $res_auth['inl_site_cd'] , $res_auth['inl_cert_no'] , $res_auth['inl_enc_cert_data'] , $opt );
				}else{
					$ct_cert->decrypt_enc_cert( $home_dir , $res_auth['inl_site_cd'] , $res_auth['inl_cert_no'] , $res_auth['inl_enc_cert_data'] , $opt );
				}
				// -- SSJ : KCP 본인확인 암호화 데이터 추가 패치 : 2021-11-01 ----

                $phone_no = $ct_cert->mf_get_key_value("phone_no"); // 전화번호
                $phone_no = tel_format($phone_no); // 전화번호 포멧 변경
                $user_name = $ct_cert->mf_get_key_value("user_name"); // 이름
                $birth_day = $ct_cert->mf_get_key_value("birth_day"); // 생년월일
                $birth_day = date('Y-m-d', strtotime($birth_day)); // 생년월일 포멧 변경
                $sex_code = $ct_cert->mf_get_key_value("sex_code"); // 성별코드
                $sex_code = $sex_code == '01' ? 'M' : 'F'; // 성별코드 포멧 변경

                if($join_tel2 <> $phone_no) error_alt('본인 인증 정보가 변조되었습니다.');
                if($join_name <> $user_name) error_alt('본인 인증 정보가 변조되었습니다.');
                if($siteInfo['join_birth'] == 'Y' && $_birth <> $birth_day) error_alt('본인 인증 정보가 변조되었습니다.');
                if($siteInfo['join_sex'] == 'Y' && $_sex <> $sex_code) error_alt('본인 인증 정보가 변조되었습니다.');

                $ct_cert->mf_clear();

				$is_join_auth = true; // SSJ : 본인인증여부 체크 수정 : 2021-11-22

            }else{
                error_alt('본인 인증후 회원가입이 가능합니다.');
            }
        }

		// --사전 체크 ---
		$join_id = nullchk($join_id , "아이디를 입력해주시기 바랍니다." , "" , "ALT");
		$join_pw = nullchk($join_pw , "패스워드를 입력해주시기 바랍니다." , "" , "ALT");
		$join_repw = nullchk($join_repw , "패스워드를 입력해주시기 바랍니다." , "" , "ALT");
		if( $join_pw <> $join_repw ) {
			error_alt("패스워드가 일치하지 않습니다.");
		}
		$join_email = nullchk($join_email , "이메일을 입력해주시기 바랍니다." , "" , "ALT");
		$join_tel2 = nullchk($join_tel2 , "휴대폰 번호를 입력해주시기 바랍니다." , "" , "ALT");
		$join_name = nullchk($join_name , "이름을 입력해주시기 바랍니다." , "" , "ALT");
		if($siteInfo['join_tel'] == 'Y' && $siteInfo['join_tel_required'] == 'Y') $join_tel = nullchk($join_tel , "전화번호를 입력해주시기 바랍니다." , "" , "ALT");
		if($siteInfo['join_addr'] == 'Y' && $siteInfo['join_addr_required'] == 'Y') {

			//$join_zip1 = nullchk($join_zip1 , "우편번호 앞자리를 입력해주시기 바랍니다." , "" , "ALT");
			//$join_zip2 = nullchk($join_zip2 , "우편번호 뒷자리를 입력해주시기 바랍니다." , "" , "ALT");
			$join_address1 = nullchk($join_address1 , "기본주소를 입력해주시기 바랍니다." , "" , "ALT");
			$join_address2 = nullchk($join_address2 , "상세주소를 입력해주시기 바랍니다." , "" , "ALT");
			$join_address_doro = nullchk($join_address_doro , "도로명주소를 입력해주시기 바랍니다." , "" , "ALT");
			$join_zonecode = nullchk($join_zonecode , "새 우편번호를 입력해주시기 바랍니다." , "" , "ALT");
		}
		if($siteInfo['join_sex'] == 'Y' && $siteInfo['join_sex_required'] == 'Y') $_sex = nullchk($_sex, '성별을 선택해주세요', '', 'ALT');
		if($siteInfo['join_birth'] == 'Y' && $siteInfo['join_birth_required'] == 'Y') $_birth = nullchk($_birth, '생년월일을 입력해주세요', '', 'ALT');
		if($join_pw && $join_repw && $join_pw == $join_repw) {
			$pw_minlength = (isset($siteInfo['join_pw_limit_min']) && $siteInfo['join_pw_limit_min'] >= 4?(int)$siteInfo['join_pw_limit_min']:4); // 최소 글자 수 구함
			if(strlen($join_pw) < $pw_minlength) error_alt('비밀번호는 '.$pw_minlength.'자 이상 입력해주세요'); // 최소 글자 수 체크
			if($siteInfo['join_pw_limit_max'] > 4 && strlen($join_pw) > $siteInfo['join_pw_limit_max']) error_alt('비밀번호는 최대 '.$siteInfo['join_pw_limit_max'].'자 까지만 입력가능합니다'); // 최대 글자 수 체크

			// 대문자 포함 옵션 사용시
			if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) {
				$pw_up_pattern = '/[A-Z]/';
				preg_match_all($pw_up_pattern, $join_pw, $pw_up_pattern_result);
				if(count($pw_up_pattern_result) < $siteInfo['join_pw_up_length']) error_alt('비밀번호에는 대문자가 '.$siteInfo['join_pw_up_length'].'개 이상 포함되어야합니다');
			}

			// 특수문자 포함 옵션 사용시
			if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_sp_length'] > 0) {
				$pw_sp_pattern = '/[~!@#$%^&*()_+|<>?:{}]/';
				preg_match_all($pw_sp_pattern, $join_pw, $pw_sp_pattern_result);
				if(count($pw_sp_pattern_result) < $siteInfo['join_pw_sp_length']) error_alt('비밀번호에는 특수문자(~!@#$%^&*()_+|<>?:{})가 '.$siteInfo['join_pw_sp_length'].'개 이상 포함되어야합니다');
			}
		}
		// --사전 체크 ---

		// 가입제한 아이디 검증
		if($siteInfo['join_ban_id'] != '') {
			$ban_id = explode(',', $siteInfo['join_ban_id']);
			if(in_array($join_id, $ban_id)) error_msg('가입이 제한된 아이디입니다.');
		}

		$join_tel = tel_format($join_tel);
		$join_tel2 = ($join_tel2 ? tel_format($join_tel2) : "");

		// === 본인인증 중복 체크 추가 통합 kms 2019-06-21 ====
		if ( memberDuplicateTelChk($join_tel2)) {
			error_frame_loc_msg( '/?pn=member.find.form' , '이미 등록된 휴대폰 번호입니다.\n\n로그인을 이용해 주시기 바랍니다.\n\n로그인 정보를 모르신다면 아이디/비밀번호 찾기를 이용해 주시기 바랍니다.');
		}
		// === 본인인증 중복 체크 추가 통합 kms 2019-06-21 ====

		// --query 사전 준비 ---
		$sque = "
			  in_pw				= password('". $join_pw ."')
			, in_email			= '". $join_email ."'
			, in_emailsend		= '". $join_emailsend ."'
			, in_smssend		= '". $join_smssend ."'
			, in_name			= '". $join_name ."'
			, in_birth			= '". $_birth ."'
			, in_sex			= '". $_sex ."'
			, in_tel			= '". $join_tel ."'
			, in_tel2			= '". $join_tel2 ."'
			, in_zip1			= '". $join_zip1 ."'
			, in_zip2			= '". $join_zip2 ."'
			, in_address1		= '". $join_address1 ."'
			, in_address2		= '". $join_address2 ."'
			, in_address_doro	= '". $join_address_doro ."'
			, in_ip				= '".$_SERVER["REMOTE_ADDR"]."'
			, in_zonecode		= '". $join_zonecode ."'
			, m_opt_date		= now()
		";
		if($siteInfo['join_approve'] == 'N') $sque .= " , in_auth = 'N' "; // 승인후 로그인 처리
		// --query 사전 준비 ---

		// 본인인증여부 기록 LDD
		//if($siteInfo['s_join_auth_use'] == 'Y' && $auth == 'B000') $sque .= ", auth_use = 'Y', auth_date = now() ";
		if($siteInfo['s_join_auth_use'] == 'Y' && $is_join_auth == true) $sque .= ", auth_use = 'Y', auth_date = now() "; // SSJ : 본인인증여부 체크 수정 : 2021-11-22


		// in_join_ua 추가
		$this_device = is_mobile() === true ? 'MOBILE' : 'PC';
		$sque .= " , in_join_ua = '". $this_device ."' ";


		// -- 아이디 중복 체크 ---
		$r = _MQ("select count(*) as cnt from smart_individual where in_id='${join_id}' ");
		if( $r[cnt] > 0 ) {
			error_msg("아이디가 중복 됩니다.");
		}
		// -- 아이디 중복 체크 ---

		// -- 이메일 중복체크 ---
		$r = _MQ("select count(*) as cnt from smart_individual where in_email='${join_email}' ");
		if( $r[cnt] > 0 ) {
			error_msg("이메일이 중복 됩니다.");
		}
		// -- 이메일 중복체크 ---


		$que = " insert smart_individual set $sque , in_id='{$join_id}' , in_rdate = now() , in_mdate=now(), in_ldate = now(), in_pw_rdate = now()  ";
		_MQ_noreturn($que);

		// 로그인 쿠키 적용 - 로그인 처리
		if($siteInfo['join_approve'] == 'Y') { // 승인후 로그인 처리
			_MQ_noreturn("update smart_cart set c_cookie='". $join_id ."' where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' ");
			samesiteCookie("AuthIndividualMember", $join_id , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
			samesiteCookie("AuthShopCOOKIEID", $join_id , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		}


		// SNS 계정연동 로그인 LDD -> 중간페이지를 거치는 소셜로그인은 아래 처럼 추가 처리 하여야 함
		/*
			if($AuthSNSEncID) {
				$sns_info = unserialize(onedaynet_decode($AuthSNSEncID));
				if($sns_info['type'] == 'facebook') _MQ_noreturn(" update smart_individual set sns_join = 'Y', fb_join = 'Y', fb_encid = '{$sns_info['id']}' where in_id='{$join_id}' ");
				else if($sns_info['type'] == 'kakao') _MQ_noreturn(" update smart_individual set sns_join = 'Y', ko_join = 'Y', ko_encid = '{$sns_info['id']}' where in_id='{$join_id}' ");
				else if($sns_info['type'] == 'naver') _MQ_noreturn(" update smart_individual set sns_join = 'Y', nv_join = 'Y', nv_encid = '{$sns_info['id']}' where in_id='{$join_id}' ");
				if($_COOKIE['AuthSNSEncID']) SetCookie('AuthSNSEncID', '', time()-3600 , '/', '.'.str_replace('www.', '', $system['host'])); // SNS 고유정보 제거
			}
		*/

		// 로그인 체크
		loginchk_insert($join_id , "individual");

        // - 메일발송 ---
        if( mailCheck($join_email) ){


            /*
                # 2016-08-29 스팸방지 추가
                # $id 변수를 받아서 처리
            # $_mailling / $_sms 정보 있어야 함.
            */
            $_mailling = $join_emailsend; // 이메일
            $_sms = $join_smssend; // 문자
            $id = $join_id;

            // $join_id ==> 적용
            $mem_info = _MQ(" select * from smart_individual where in_id = '". $id ."' and in_userlevel != '9' ");
            include_once(OD_MAIL_ROOT."/member.join.mail.php"); // 메일 내용 불러오기 ($mailing_content)
            $_title = "[".$siteInfo[s_adshop]."] 회원가입을 환영합니다.";

            $_content = get_mail_content($mailling_content);
            mailer( $join_email , $_title , $_content );
        }
        // - 메일발송 ---

		// 회원가입 축하 적립금
		shop_pointlog_insert( $join_id , "회원가입" , $siteInfo[s_joinpoint] , "N" , $siteInfo[s_joinpointprodate]);

		// 회원가입 쿠폰 발급
		couponIssuedAutoType4($join_id);

		// 문자 발송
		$sms_to = $join_tel2 ? $join_tel2 : $join_tel;
		shop_send_sms($sms_to,"join" , $join_id);

		if($siteInfo['join_approve'] == 'N') { // 승인후 로그인 처리
			error_frame_loc_msg("/", '회원가입을 환영합니다.\\n로그인은 관리자 승인 후 가능합니다.');
		}
		UserLogin($join_id); // 세션로그인

		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_frame_loc('/?pn=member.join.complete','parent');
		break;




	// 정보수정
    case "modify":

        $is_join_auth = false; // SSJ : 본인인증여부 체크 수정 : 2021-11-22
        // 2018-10-04 SSJ :: 본인인증 사용 시
        if(
            $siteInfo['s_join_auth_use'] == 'Y'
            &&
            (
                $mem_info['in_tel2'] <> $join_tel2
                ||
                $mem_info['in_name'] <> $join_name
                ||
                $siteInfo['join_birth'] == 'Y' && $mem_info['in_birth'] <> $_birth
                ||
                $siteInfo['join_sex'] == 'Y' && $mem_info['in_sex'] <> $_sex
            )
        ) {
            $res_auth = _MQ(" select * from smart_individual_auth_log where inl_ordr_idxx = '". $_ordr_idxx ."' ");
            if($res_auth['inl_enc_cert_data']){
                // -- SSJ : KCP 본인확인 암호화 데이터 추가 패치 : 2021-11-01 ----
				$app_enc_key = $siteInfo['s_join_auth_kcb_enckey'] ? $siteInfo['s_join_auth_kcb_enckey'] : '';
				if($app_enc_key <> ''){
					$home_dir      = $_SERVER['DOCUMENT_ROOT'] . "/auth/kcp_v2"; // ct_cll 절대경로 ( bin 전까지 )
				}else{
					$home_dir      = $_SERVER['DOCUMENT_ROOT'] . "/auth/kcp"; // ct_cll 절대경로 ( bin 전까지 )
				}
                /* ============================================================================== */
                /* =   라이브러리 파일 Include                                                  = */
                /* = -------------------------------------------------------------------------- = */
				require $home_dir . "/lib/ct_cli_lib.php";
                $ct_cert = new C_CT_CLI;
                $ct_cert->mf_clear();
                // 인증데이터 복호화 함수
                // 해당 함수는 암호화된 enc_cert_data 를
                // site_cd 와 cert_no 를 가지고 복화화 하는 함수 입니다.
                // 정상적으로 복호화 된경우에만 인증데이터를 가져올수 있습니다.
                $opt = "1" ; // 복호화 인코딩 옵션 ( UTF - 8 사용시 "1" )
				if($app_enc_key <> ''){
					$ct_cert->decrypt_enc_cert( $home_dir , $app_enc_key , $res_auth['inl_site_cd'] , $res_auth['inl_cert_no'] , $res_auth['inl_enc_cert_data'] , $opt );
				}else{
					$ct_cert->decrypt_enc_cert( $home_dir , $res_auth['inl_site_cd'] , $res_auth['inl_cert_no'] , $res_auth['inl_enc_cert_data'] , $opt );
				}
				// -- SSJ : KCP 본인확인 암호화 데이터 추가 패치 : 2021-11-01 ----

                $phone_no = $ct_cert->mf_get_key_value("phone_no"); // 전화번호
                $phone_no = tel_format($phone_no); // 전화번호 포멧 변경
                $user_name = $ct_cert->mf_get_key_value("user_name"); // 이름
                $birth_day = $ct_cert->mf_get_key_value("birth_day"); // 생년월일
                $birth_day = date('Y-m-d', strtotime($birth_day)); // 생년월일 포멧 변경
                $sex_code = $ct_cert->mf_get_key_value("sex_code"); // 성별코드
                $sex_code = $sex_code == '01' ? 'M' : 'F'; // 성별코드 포멧 변경

                if($join_tel2 <> $phone_no) error_alt('본인 인증 정보가 변조되었습니다.');
                if($join_name <> $user_name) error_alt('본인 인증 정보가 변조되었습니다.');
                if($siteInfo['join_birth'] == 'Y' && $_birth <> $birth_day) error_alt('본인 인증 정보가 변조되었습니다.');
                if($siteInfo['join_sex'] == 'Y' && $_sex <> $sex_code) error_alt('본인 인증 정보가 변조되었습니다.');

                $ct_cert->mf_clear();

				$is_join_auth = true; // SSJ : 본인인증여부 체크 수정 : 2021-11-22

            }else{
                error_alt('본인 인증후 정보수정이 가능합니다.');
            }
        }

		// -- 사전 체크 ---
		$join_name = nullchk($join_name , '이름을 입력해주세요', '', 'ALT');
		$join_email = nullchk($join_email , '이메일 아이디를 입력해주세요', '', 'ALT');
		if($mem_info['in_email'] != $join_email) {
			$join_email_check = nullchk($join_email_check , '이메일 중복검사를 해주세요', '', 'ALT');

			// 이메일 중복검사
			$EmailOverlap = _MQ(" select count(*) as cnt from `smart_individual` where `in_email` = '{$join_email}' and `in_id` != '{$mem_info['in_id']}' ");
			if($EmailOverlap['cnt'] > 0) error_alt('사용이 불가능한 이메일입니다.');
		}
		if( $join_pw && $join_repw && ($join_pw <> $join_repw)){
			error_alt("비밀번호가 서로 다릅니다.\\n\\n다시 한번 확인해주세요");
		}
		if($siteInfo['join_tel'] == 'Y' && $siteInfo['join_tel_required'] == 'Y') $join_tel = nullchk($join_tel , '전화번호를 입력해주세요', '', 'ALT');
		if($siteInfo['join_addr'] == 'Y' && $siteInfo['join_addr_required'] == 'Y') {

			//$join_zip1 = nullchk($join_zip1 , '우편번호 앞자리를 입력해주시기 바랍니다', '', 'ALT'); // 구우편번호가 없는 경우가 발생 하기 때문에 필수 조건 제외
			//$join_zip2 = nullchk($join_zip2 , '우편번호 뒷자리를 입력해주시기 바랍니다', '', 'ALT'); // 구우편번호가 없는 경우가 발생 하기 때문에 필수 조건 제외
			$join_address1 = nullchk($join_address1, '주소검색을 통하여 기본주소를 입력해주세요', '', 'ALT');
			$join_address2 = nullchk($join_address2, '나머지주소를 입력해주세요', '', 'ALT');
			$join_address_doro = nullchk($join_address_doro, '주소검색을 통하여 도로명주소를 입력해주세요', '', 'ALT');
			$join_zonecode = nullchk($join_zonecode, '주소검색을 통하여 새 우편번호를 입력해주세요', '', 'ALT');
		}
		if($siteInfo['join_sex'] == 'Y' && $siteInfo['join_sex_required'] == 'Y') $_sex = nullchk($_sex, '성별을 선택해주세요', '', 'ALT');
		if($siteInfo['join_birth'] == 'Y' && $siteInfo['join_birth_required'] == 'Y') $_birth = nullchk($_birth, '생년월일을 입력해주세요', '', 'ALT');
		if($join_pw && $join_repw && $join_pw == $join_repw) {
			$pw_minlength = (isset($siteInfo['join_pw_limit_min']) && $siteInfo['join_pw_limit_min'] >= 4?(int)$siteInfo['join_pw_limit_min']:4); // 최소 글자 수 구함
			if(strlen($join_pw) < $pw_minlength) error_alt('비밀번호는 '.$pw_minlength.'자 이상 입력해주세요'); // 최소 글자 수 체크
			if($siteInfo['join_pw_limit_max'] > 4 && strlen($join_pw) > $siteInfo['join_pw_limit_max']) error_alt('비밀번호는 최대 '.$siteInfo['join_pw_limit_max'].'자 까지만 입력가능합니다'); // 최대 글자 수 체크

			// 대문자 포함 옵션 사용시
			if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) {
				$pw_up_pattern = '/[A-Z]/';
				preg_match_all($pw_up_pattern, $join_pw, $pw_up_pattern_result);
				if(count($pw_up_pattern_result) < $siteInfo['join_pw_up_length']) error_alt('비밀번호에는 대문자가 '.$siteInfo['join_pw_up_length'].'개 이상 포함되어야합니다');
			}

			// 특수문자 포함 옵션 사용시
			if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_sp_length'] > 0) {
				$pw_sp_pattern = '/[~!@#$%^&*()_+|<>?:{}]/';
				preg_match_all($pw_sp_pattern, $join_pw, $pw_sp_pattern_result);
				if(count($pw_sp_pattern_result) < $siteInfo['join_pw_sp_length']) error_alt('비밀번호에는 특수문자(~!@#$%^&*()_+|<>?:{})가 '.$siteInfo['join_pw_sp_length'].'개 이상 포함되어야합니다');
			}
		}
		// -- 사전 체크 ---

		$tel_array = array(' ','.','-');
		$join_tel = str_replace($tel_array,'',$join_tel); $join_tel2 = str_replace($tel_array,'',$join_tel2);
		$join_tel = tel_format($join_tel);
		$join_tel2 = ($join_tel2?tel_format($join_tel2):"");

        // 정보수정 시 광고성 정보 수신동의 상태 - 정보 추가 - changeAlert
        // 자체로 메일발송함.
        // $id 변수를 이용하여 회원정보 추출
        // $_mailling / $_sms 정보 있어야 함.
        $_mailling = $join_emailsend; // 이메일
        $_sms = $join_smssend; // 문자
        $id = get_userid(); // 아이디 정보를 넘겨준다. :: 자체적으로 메일발송

        // ---------------------------- 수신동의 상태에 따른 메일 발송처리 ---------------------------------
        $mem_info = _MQ(" select * from smart_individual where in_id = '". $id ."' and in_userlevel != '9' ");
        if( mailCheck($join_email) && ( $mem_info['in_emailsend'] <> $_mailling || $mem_info['in_smssend'] <> $_sms ) && ( $_mailling || $_sms ) && count($mem_info) > 0 ){
			$_title = "[".$siteInfo[s_adshop]."] 정보수정으로 수신동의 상태가 변경되었습니다.";
			 include_once(OD_MAIL_ROOT."/changeAlert.mail.contents.modify.php"); // 메일 내용 불러오기 ($mailing_content)
			$_content = get_mail_content($mailling_content);
			mailer( $join_email , $_title , $_content );
		}
		// ---------------------------- 수신동의 상태에 따른 메일 발송처리 끝 ---------------------------------


		// === 본인인증 중복 체크 추가 통합 kms 2019-06-21 ====
		$tel_chk_bool = ($mem_info['in_tel2'] != $join_tel2);
		if ( $tel_chk_bool && memberDuplicateTelChk($join_tel2)) {
			error_frame_loc_msg('/?pn=mypage.modify.form' , '이미 등록된 휴대폰 번호입니다.');
		}
		// === 본인인증 중복 체크 추가 통합 kms 2019-06-21 ====

        // -- query 사전 준비 ---
        $sque = "
            in_name             = '". $join_name ."'
            ,in_sex             = '". $_sex ."'
            ,in_birth           = '". $_birth ."'
            ,in_email           = '". $join_email ."'
            ,in_emailsend       = '". $join_emailsend ."'
            ,in_smssend         = '". $join_smssend ."'
            ,in_tel             = '". $join_tel ."'
            ,in_tel2            = '". $join_tel2 ."'
            ,in_zip1            = '". $join_zip1 ."'
            ,in_zip2            = '". $join_zip2 ."'
            ,in_address1        = '". $join_address1 ."'
            ,in_address2        = '". $join_address2 ."'
            ,in_address_doro    = '". $join_address_doro ."'
            ,in_zonecode        = '". $join_zonecode ."'
            ,m_opt_date          = now()
        ";
		if( $join_pw && $join_repw && ( $join_pw == $join_repw )){
			$sque .= " , in_pw = password('". $join_pw ."'), in_pw_rdate = now() ";// --- modify source ---
		}
		// -- query 사전 준비 ---

		// SSJ : 본인인증여부 체크 수정 : 2021-11-22
		if($siteInfo['s_join_auth_use'] == 'Y' && $is_join_auth == true) $sque .= ", auth_use = 'Y', auth_date = now() ";

		$que = " update smart_individual set $sque where in_id='". get_userid() ."' ";
		_MQ_noreturn($que);


		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행

		error_frame_loc_msg("/?pn=mypage.modify.form" , "정보를 수정하였습니다.");
		break;




	// 회원탈퇴
	case "delete":
		// 비밀번호 체크
		$r = _MQ("select count(*) as cnt from smart_individual where in_id='". get_userid() ."' and in_pw = password('".$leave_pw."') ");

		if($r[cnt] < 1) error_frame_reload("비밀번호가 일치하지 않습니다.");

		// --query 사전 준비 ---
		$sque = "
			in_name			= '탈퇴회원'
			,in_email		= ''
			,in_emailsend	= 'N'
			,in_smssend		= 'N'
			,in_tel			= ''
			,in_odate		= now()
			,in_pw			= '탈퇴회원'
			,in_out			= 'Y'
			,in_point		= '0'
			, sns_join = 'N'
			, fb_join = 'N'
			, fb_encid = ''
			, ko_join = 'N'
			, ko_encid = ''
			, nv_join = 'N'
			, nv_encid = ''
		";
		// --query 사전 준비 ---
//		$que = " update smart_individual set $sque where in_id='". get_userid() ."' ";
//		_MQ_noreturn($que);

		memberGetOut(get_userid() , 'member'); // 회원 탈퇴 처리

		// 로그인 쿠키 적용 - 로그아웃
		samesiteCookie("AuthIndividualMember", "" , time() - 3600 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		samesiteCookie("AuthShopCOOKIEID", md5(serialize($_SERVER) . mt_rand(0,9999999)) , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
		UserLogout(); // 세션로그아웃

		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_frame_loc_msg("/" , "정상적으로 탈퇴처리하였습니다.\\n\\n그동안 이용해 주셔서 감사합니다.");
		break;


	// 이메일 중복체크
	case 'email_check':
		$checkEmail = checkInputValue($_email, 'email');
		if(empty($_email) || $_email == '') die(json_encode(array('result'=>'error', 'msg'=>'이메일 주소를 입력해주세요.')));
		if($checkEmail === false) die(json_encode(array('result'=>'error', 'msg'=>'이메일 주소가 올바르지 않습니다.')));
		if(is_login() === true && $_email == $mem_info['in_email']) die(json_encode(array('result'=>'success', 'msg'=>'이메일 주소 변경시에만 중복체크하시면됩니다.')));

		$EmailOverlap = _MQ(" select count(*) as cnt from `smart_individual` where `in_email` = '{$_email}' ");
		if($EmailOverlap['cnt'] > 0) die(json_encode(array('result'=>'error', 'msg'=>'사용이 불가능한 이메일입니다.')));
		else die(json_encode(array('result'=>'success', 'msg'=>'사용이 가능한 이메일입니다.')));

		die(json_encode(array('result'=>'error', 'msg'=>'알 수 없는 에러가 발생하였습니다.\\n새로 고침 후 다시 시도 부탁합니다.'))); // 혹시 모를 조건 탈출 차단
	break;

	case 'id_check':

		// 아이디 입력여부 검증
		$_id = trim($_id);
		if(empty($_id) || $_id == '') die(json_encode(array('result'=>'error', 'msg'=>'아이디를 입력해주세요.'))); // 아이디 입력 체크

		// 정보 변수화
		$id_length = mb_strlen($_id, 'UTF-8'); // 입력된 아이디의 글자수
		$id_min_length = ((int)$siteInfo['join_id_limit_min'] >= 4?(int)$siteInfo['join_id_limit_min']:4); // 최소 글자수
		$id_max_length = ((int)$siteInfo['join_id_limit_max'] > (int)$siteInfo['join_id_limit_min']?(int)$siteInfo['join_id_limit_max']:0); // 최대 글자수(최소 글자 수보다 크면 제한 작동)
		$checkID = checkInputValue($_id, 'enum'); // 영+숫자 아이디인지 검증

		// 최대 글자 수에 따른 안내 메시지 변경
		$length_text = '아이디는 영문, 숫자로 '.$id_min_length.'자 이상 입력해주세요.';
		if($id_max_length > 0) $length_text = '아이디는 영문, 숫자로 '.$id_min_length.'자~'.$id_max_length.'자 이내로 입력해주세요.';

		// 검증
		if($checkID === false) die(json_encode(array('result'=>'error', 'msg'=>$length_text))); // 영문과 숫자로만 이루어져있는지 체크
		if($id_min_length > $id_length) die(json_encode(array('result'=>'error', 'msg'=>$length_text))); // 최소 글자수 체크
		if($id_max_length > 0 && $id_max_length < $id_length) die(json_encode(array('result'=>'error', 'msg'=>$length_text))); // 최대 글자수 체크

		// 가입제한 아이디 검증
		if($siteInfo['join_ban_id'] != '') {
			$ban_id = explode(',', $siteInfo['join_ban_id']);
			if(in_array($_id, $ban_id)) die(json_encode(array('result'=>'error', 'msg'=>'가입이 제한된 아이디입니다.')));
		}

		// 중복 처리 및 가능처리
		$r = _MQ(" select count(*) as cnt from smart_individual where in_id='{$_id}' ");
		if($r['cnt'] > 0) die(json_encode(array('result'=>'error', 'msg'=>'이미 사용중인 아이디입니다.'))); // 중복
		else die(json_encode(array('result'=>'success', 'msg'=>'사용이 가능한 아이디입니다.'))); // 사용가능
	break;

	// LCY 관리자가 설정한 일 수 마다 비밀번호 변경
	case 'password_change' :

		$_id = get_userid();

		$_pw = nullchk($_pw , '현재 비밀번호를 입력해주시기 바랍니다.', '', 'ALT');
		$_cpw = nullchk($_cpw , '새 비밀번호를 입력해주시기 바랍니다.', '', 'ALT');
		$_rcpw = nullchk($_rcpw , '새 비밀번호 확인을 입력해주시기 바랍니다.', '', 'ALT');
		if($_site_access <> sha1($_id) || $_cpw == '') error_alt('유효하지 않는 요청입니다.'); // 2016-09-27 유효아이디 비교 수정 SSJ
		if(db_password($_pw) != $mem_info['in_pw']) error_alt('현재 비밀번호가 일치하지않습니다.');
		if($_cpw && $_rcpw && $_cpw == $_rcpw) {
			$pw_minlength = (isset($siteInfo['join_pw_limit_min']) && $siteInfo['join_pw_limit_min'] >= 4?(int)$siteInfo['join_pw_limit_min']:4); // 최소 글자 수 구함
			if(strlen($_cpw) < $pw_minlength) error_alt('비밀번호는 '.$pw_minlength.'자 이상 입력해주세요'); // 최소 글자 수 체크
			if($siteInfo['join_id_limit_max'] > 4 && strlen($_cpw) > $siteInfo['join_id_limit_max']) error_alt('비밀번호는 최대 '.$siteInfo['join_id_limit_max'].'자 까지만 입력가능합니다'); // 최대 글자 수 체크

			// 대문자 포함 옵션 사용시
			if($siteInfo['join_pw_up_use'] == 'Y' && $siteInfo['join_pw_up_length'] > 0) {
				$pw_up_pattern = '/[A-Z]/';
				preg_match_all($pw_up_pattern, $_cpw, $pw_up_pattern_result);
				if(count($pw_up_pattern_result) < $siteInfo['join_pw_up_length']) error_alt('비밀번호에는 대문자가 '.$siteInfo['join_pw_up_length'].'개 이상 포함되어야합니다');
			}

			// 특수문자 포함 옵션 사용시
			if($siteInfo['join_pw_sp_use'] == 'Y' && $siteInfo['join_pw_sp_length'] > 0) {
				$pw_sp_pattern = '/[~!@#$%^&*()_+|<>?:{}]/';
				preg_match_all($pw_sp_pattern, $_cpw, $pw_sp_pattern_result);
				if(count($pw_sp_pattern_result) < $siteInfo['join_pw_sp_length']) error_alt('비밀번호에는 특수문자(~!@#$%^&*()_+|<>?:{})가 '.$siteInfo['join_pw_sp_length'].'개 이상 포함되어야합니다');
			}
		}


		_MQ_noreturn("update smart_individual set in_pw_rdate = now(), in_pw = password('".$_cpw."')  where in_id = '".$_id."' ");

		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		error_frame_loc_msg('/','비밀번호가 변경되었습니다.');

	break;

}
// - 모드별 처리 ---
exit;