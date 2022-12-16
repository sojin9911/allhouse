<?PHP
	include_once(dirname(__FILE__).'/inc.php');

   /* ============================================================================== */
    /* =   인증데이터 수신 및 복호화 페이지                                         = */
    /* = -------------------------------------------------------------------------- = */
    /* =   해당 페이지는 반드시 가맹점 서버에 업로드 되어야 하며                    = */
    /* =   가급적 수정없이 사용하시기 바랍니다.                                     = */
    /* ============================================================================== */

    /* ============================================================================== */
    /* =   라이브러리 파일 Include                                                  = */
    /* = -------------------------------------------------------------------------- = */

	// SSJ : KCP 본인인증 모듈 가맹점인증키 추가 패치 : 2021-03-12
	$app_enc_key = $siteInfo['s_join_auth_kcb_enckey'] ? $siteInfo['s_join_auth_kcb_enckey'] : '';
	if($app_enc_key <> ''){
		$home_dir      = $_SERVER['DOCUMENT_ROOT'] . "/auth/kcp_v2"; // ct_cll 절대경로 ( bin 전까지 )
	}else{
		$home_dir      = $_SERVER['DOCUMENT_ROOT'] . "/auth/kcp"; // ct_cll 절대경로 ( bin 전까지 )
	}
	require $home_dir . "/lib/ct_cli_lib.php";

    /* = -------------------------------------------------------------------------- = */
    /* =   라이브러리 파일 Include END                                               = */
    /* ============================================================================== */

    /* ============================================================================== */
    /* =   null 값을 처리하는 메소드                                                = */
    /* = -------------------------------------------------------------------------- = */
    function f_get_parm_str( $val )
    {
        if ( $val == null ) $val = "";
        if ( $val == ""   ) $val = "";
        return  $val;
    }
    /* ============================================================================== */

	// SSJ : KCP 본인인증 모듈 가맹점인증키 추가 패치 : 2021-03-12
	// $home_dir      = $_SERVER['DOCUMENT_ROOT'] . "/auth/kcp"; // ct_cll 절대경로 ( bin 전까지 )

    $site_cd       = "";
    $ordr_idxx     = "";

    $cert_no       = "";
    $cert_enc_use  = "";
    $enc_info      = "";
    $enc_data      = "";
    $req_tx        = "";

    $enc_cert_data = "";
    $cert_info     = "";

    $tran_cd       = "";
    $res_cd        = "";
    $res_msg       = "";

    $dn_hash       = "";
	/*------------------------------------------------------------------------*/
    /*  :: 전체 파라미터 남기기                                               */
    /*------------------------------------------------------------------------*/

	// request 로 넘어온 값 utf-8로 변환
    foreach($_POST as $nmParam => $valParam){
		$_POST[$nmParam] = iconv('euc-kr', 'utf-8', $valParam);

	}

    // request 로 넘어온 값 처리
    foreach($_POST as $nmParam => $valParam)
    {

        if ( $nmParam == "site_cd" )
        {
            $site_cd = f_get_parm_str ( $valParam );
        }

        if ( $nmParam == "ordr_idxx" )
        {
            $ordr_idxx = f_get_parm_str ( $valParam );
        }

        if ( $nmParam == "res_cd" )
        {
            $res_cd = f_get_parm_str ( $valParam );
        }

        if ( $nmParam == "cert_enc_use" )
        {
            $cert_enc_use = f_get_parm_str ( $valParam );
        }

        if ( $nmParam == "req_tx" )
        {
            $req_tx = f_get_parm_str ( $valParam );
        }

        if ( $nmParam == "cert_no" )
        {
            $cert_no = f_get_parm_str ( $valParam );
        }

        if ( $nmParam == "enc_cert_data" )
        {
            $enc_cert_data = f_get_parm_str ( $valParam );
        }

		// SSJ : KCP 본인인증 모듈 가맹점인증키 추가 패치 : 2021-03-12
		if ( $nmParam == "enc_cert_data2" )
        {
            $enc_cert_data2 = f_get_parm_str ( $valParam );
        }

        if ( $nmParam == "dn_hash" )
        {
            $dn_hash = f_get_parm_str ( $valParam );
       }

        // 부모창으로 넘기는 form 데이터 생성 필드
        //$sbParam .= "<input type='hidden' name='" . $nmParam . "' value='" . f_get_parm_str( $valParam ) . "'/>";
    }

    $ct_cert = new C_CT_CLI;
    $ct_cert->mf_clear();

	// SSJ : KCP 본인인증 모듈 가맹점인증키 추가 패치 : 2021-03-12
	if($app_enc_key <> ''){ $cert_enc_use = "Y"; } // -- 미사용 항목으로 항상 Y로 설정

    // 결과 처리

    if( $cert_enc_use == "Y" )
    {
        if( $res_cd == "0000" )
        {
            // dn_hash 검증
            // KCP 가 리턴해 드리는 dn_hash 와 사이트 코드, 주문번호 , 인증번호를 검증하여
            // 해당 데이터의 위변조를 방지합니다
             $veri_str = $site_cd.$ordr_idxx.$cert_no; // 사이트 코드 + 주문번호 + 인증거래번호

			// SSJ : KCP 본인인증 모듈 가맹점인증키 추가 패치 : 2021-03-12
			if($app_enc_key <> ''){
				$chk_hash = $ct_cert->check_valid_hash ( $home_dir , $app_enc_key , $dn_hash , $veri_str ) != "1";
			}else{
				$chk_hash = $ct_cert->check_valid_hash ( $home_dir , $dn_hash , $veri_str ) != "1";
			}
            if ( $chk_hash )
            {
                // 검증 실패시 처리 영역

                echo "dn_hash 변조 위험있음";
                // 오류 처리 ( dn_hash 변조 위험있음)
            }

            // 가맹점 DB 처리 페이지 영역

            //echo "========================= 리턴 데이터 ======================="       ."<br>";
            //echo "사이트 코드            :" . $site_cd                                 ."<br>";
            //echo "인증 번호              :" . $cert_no                                 ."<br>";
            //echo "암호된 인증정보        :" . $enc_cert_data                           ."<br>";

            // 인증데이터 복호화 함수
            // 해당 함수는 암호화된 enc_cert_data 를
            // site_cd 와 cert_no 를 가지고 복화화 하는 함수 입니다.
            // 정상적으로 복호화 된경우에만 인증데이터를 가져올수 있습니다.
            $opt = "1" ; // 복호화 인코딩 옵션 ( UTF - 8 사용시 "1" )
			// SSJ : KCP 본인인증 모듈 가맹점인증키 추가 패치 : 2021-03-12
			if($app_enc_key <> ''){
				$ct_cert->decrypt_enc_cert( $home_dir , $app_enc_key , $site_cd , $cert_no , $enc_cert_data2 , $opt );
			}else{
				$ct_cert->decrypt_enc_cert( $home_dir , $site_cd , $cert_no , $enc_cert_data , $opt );
			}

            //echo "========================= 복호화 데이터 ====================="       ."<br>";
            //echo "복호화 이동통신사 코드 :" . $ct_cert->mf_get_key_value("comm_id"    )."<br>"; // 이동통신사 코드
            //echo "복호화 전화번호        :" . $ct_cert->mf_get_key_value("phone_no"   )."<br>"; // 전화번호
            //echo "복호화 이름            :" . $ct_cert->mf_get_key_value("user_name"  )."<br>"; // 이름
            //echo "복호화 생년월일        :" . $ct_cert->mf_get_key_value("birth_day"  )."<br>"; // 생년월일
            //echo "복호화 성별코드        :" . $ct_cert->mf_get_key_value("sex_code"   )."<br>"; // 성별코드
            //echo "복호화 내/외국인 정보  :" . $ct_cert->mf_get_key_value("local_code" )."<br>"; // 내/외국인 정보
            //echo "복호화 CI              :" . $ct_cert->mf_get_key_value("ci_url"         )."<br>"; // CI
            //echo "복호화 DI              :" . $ct_cert->mf_get_key_value("di_url"         )."<br>"; // DI 중복가입 확인값
            //echo "복호화 WEB_SITEID      :" . $ct_cert->mf_get_key_value("web_siteid" )."<br>"; // WEB_SITEID
            //echo "복호화 결과코드        :" . $ct_cert->mf_get_key_value("res_cd"     )."<br>"; // 암호화된 결과코드
            //echo "복호화 결과메시지      :" . $ct_cert->mf_get_key_value("res_msg"    )."<br>"; // 암호화된 결과메시지

            $comm_id = $ct_cert->mf_get_key_value("comm_id"); // 이동통신사 코드
            $phone_no = $ct_cert->mf_get_key_value("phone_no"); // 전화번호
            $phone_no = tel_format($phone_no); // 전화번호 포멧 변경
            $user_name = $ct_cert->mf_get_key_value("user_name"); // 이름
            $birth_day = $ct_cert->mf_get_key_value("birth_day"); // 생년월일
			$birth_day = date('Y-m-d', strtotime($birth_day)); // 생년월일 포멧 변경
            $sex_code = $ct_cert->mf_get_key_value("sex_code"); // 성별코드
			$sex_code = $sex_code == '01' ? 'M' : 'F'; // 성별코드 포멧 변경
            $local_code = $ct_cert->mf_get_key_value("local_code"); // 내/외국인 정보
            $ci_url = $ct_cert->mf_get_key_value("ci_url"); // CI
            $di_url = $ct_cert->mf_get_key_value("di_url"); // DI 중복가입 확인값
            $web_siteid = $ct_cert->mf_get_key_value("web_siteid" ); // WEB_SITEID
            $res_cd = $ct_cert->mf_get_key_value("res_cd"); // 암호화된 결과코드
            $res_msg = $ct_cert->mf_get_key_value("res_msg"); // 암호화된 결과메시지

			// 부모창으로 넘기는 form 데이터 생성 필드
			$sbParam .= "<input type='hidden' name='res_cd' value='" . $res_cd . "'/>";
			$sbParam .= "<input type='hidden' name='res_msg' value='" . $res_msg . "'/>";
			$sbParam .= "<input type='hidden' name='phone_no' value='" . $phone_no . "'/>";
			$sbParam .= "<input type='hidden' name='user_name' value='" . $user_name . "'/>";
			$sbParam .= "<input type='hidden' name='birth_day' value='" . $birth_day . "'/>";
			$sbParam .= "<input type='hidden' name='sex_code' value='" . $sex_code . "'/>";
			$sbParam .= "<input type='hidden' name='ci_url' value='" . $ci_url . "'/>";
			$sbParam .= "<input type='hidden' name='di_url' value='" . $di_url . "'/>";
			$sbParam .= "<input type='hidden' name='up_hash' value='" . f_get_parm_str( $_POST['up_hash'] ) . "'/>";

            // === 회원 휴대폰 번호 중복 체크 추가 통합 kms 2019-06-21 ====
            $return_string = "이미 등록된 휴대폰 번호입니다.";
            if ( memberDuplicateTelChk($phone_no) && is_login() ) {
                if ( is_mobile() === true ) {
                    error_frame_loc_msg('/?pn=member.modify.form' , $return_string);
                }else{
                    error_loc_msgPopup('/?pn=mypage.modify.form' , $return_string );
                }
            }else if (memberDuplicateTelChk($phone_no)) {
                $return_string = "이미 등록된 휴대폰 번호입니다.\\n\\n로그인을 이용해 주시기 바랍니다.\\n\\n로그인 정보를 모르신다면 \\n\\n아이디/비밀번호 찾기를 이용해 주시기 바랍니다.";
                if ( is_mobile() === true ) {
                    error_frame_loc_msg('/?pn=member.find.form' , $return_string);
                }else{
                    error_loc_msgPopup('/?pn=member.find.form' , $return_string);
                }
            }
            // === 회원 휴대폰 번호 중복 체크 추가 통합 kms 2019-06-21 ====

			// 위변조 체크를 위한 로그 저장 - 성공시에만 저장
			$que = "
				insert into smart_individual_auth_log set
					inl_ordr_idxx = '". $ordr_idxx ."'
					, inl_site_cd = '". $site_cd ."'
					, inl_cert_no = '". $cert_no ."'
					, inl_enc_cert_data = '". ($app_enc_key ? $enc_cert_data2 : $enc_cert_data) /* SSJ : KCP 본인확인 암호화 데이터 추가 패치 : 2021-11-01 */ ."'
					, inl_rdate = now()
				on duplicate key update
					inl_site_cd = '". $site_cd ."'
					, inl_cert_no = '". $cert_no ."'
					, inl_enc_cert_data = '". ($app_enc_key ? $enc_cert_data2 : $enc_cert_data) /* SSJ : KCP 본인확인 암호화 데이터 추가 패치 : 2021-11-01 */ ."'
					, inl_rdate = now()
			";
			_MQ_noreturn($que);

            // 위변조 체크용 변수 셋팅
            if ( is_mobile() === true ) {
                echo "
                    <script>
                        parent.document.join_form._ordr_idxx.value = '". $ordr_idxx ."';
                        parent.window.scAuthR = '". $res_cd ."';
                        parent.window.scAuthN = '". $user_name ."';
                        parent.window.scAuthB = '". $birth_day ."';
                        parent.window.scAuthS = '". $sex_code ."';
                        parent.window.scAuthH = '". $phone_no ."';
                        parent.window.scAuthM = '". $res_msg ."';
                    </script>
                ";
            }else{
                echo "
                    <script>
                        window.opener.document.join_form._ordr_idxx.value = '". $ordr_idxx ."';
                        window.opener.window.scAuthR = '". $res_cd ."';
                        window.opener.window.scAuthN = '". $user_name ."';
                        window.opener.window.scAuthB = '". $birth_day ."';
                        window.opener.window.scAuthS = '". $sex_code ."';
                        window.opener.window.scAuthH = '". $phone_no ."';
                        window.opener.window.scAuthM = '". $res_msg ."';
                    </script>
                ";
            }

        }
        else/*if( res_cd.equals( "0000" ) != true )*/
        {
           // 인증실패
		   $sbParam .= "<input type='hidden' name='up_hash' value='" . f_get_parm_str( $_POST['up_hash'] ) . "'/>";

           // 위변조 체크용 변수 셋팅
            if ( is_mobile() === true ) {

                echo "
                    <script>
                        parent.document.join_form._ordr_idxx.value = '';
                        parent.window.scAuthR = '9999';
                        parent.window.scAuthN = '';
                        parent.window.scAuthB = '';
                        parent.window.scAuthS = '';
                        parent.window.scAuthH = '';
                        parent.window.scAuthM = '';
                    </script>
                ";
            }else{
                echo "
                    <script>
                        window.opener.document.join_form._ordr_idxx.value = '';
                        window.opener.window.scAuthR = '9999';
                        window.opener.window.scAuthN = '';
                        window.opener.window.scAuthB = '';
                        window.opener.window.scAuthS = '';
                        window.opener.window.scAuthH = '';
                        window.opener.window.scAuthM = '';
                    </script>
                ";
            }
        }
    }
    else/*if( cert_enc_use.equals( "Y" ) != true )*/
    {
		if(urldecode($_POST['res_msg']) && urldecode($_POST['res_msg']) <> 'cancel') $res_msg = urldecode($_POST['res_msg']);
		else $res_msg = '본인인증을 취소하였습니다.';

        if ( is_mobile() === true ) {
            // 암호화 인증 안함 => 인증취소 시
            echo "
                <script>
                    parent.document.getElementById( 'cert_info' ).style.display = '';
                    parent.document.getElementById( 'kcp_cert'  ).style.display = 'none';
                    alert('".$res_msg."');
                </script>
            ";
            exit;
        }else{
            // 암호화 인증 안함 => 인증취소 시
            error_msgPopup_s($res_msg);
        }
    }
    $ct_cert->mf_clear();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>*** KCP Online Payment System [PHP Version] ***</title>
        <script type="text/javascript">
            window.onload=function()
            {
                try
                {

                    if( navigator.userAgent.indexOf("Android") > - 1 || navigator.userAgent.indexOf("iPhone") > - 1 ){
                        parent.auth_data( document.form_auth ); // 부모창으로 값 전달

                        parent.document.getElementById( "cert_info" ).style.display = "";
                        parent.document.getElementById( "kcp_cert"  ).style.display = "none";

                    }else{
                        window.opener.auth_data( document.form_auth ); // 부모창으로 값 전달

                        window.close();// 팝업 닫기
                    }

                }
                catch(e)
                {
                    alert(e); // 정상적인 부모창의 iframe 를 못찾은 경우임
                }
            }
        </script>
    </head>
    <body oncontextmenu="return false;" ondragstart="return false;" onselectstart="return false;">
        <form name="form_auth" method="post">
            <?php echo $sbParam; ?>
        </form>
    </body>
</html>