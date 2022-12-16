<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
?>
<form name="form_auth" class="js_join_auth" autocomplete="off">
	<input type="hidden" name="in_tp_bit" value="8">
	<!-- 주문번호 -->
	<input type="hidden" name="ordr_idxx" class="frminput" value="" size="40" readonly="readonly" maxlength="40"/>
	<!-- 요청종류 -->
	<input type="hidden" name="req_tx" value="cert"/>
	<!-- 요청구분 -->
	<input type="hidden" name="cert_method" value="01"/>
	<!-- 웹사이트아이디 -->
	<input type="hidden" name="web_siteid" value=""/>
	<!-- 노출 통신사 default 처리시 아래의 주석을 해제하고 사용하십시요
	SKT : SKT , KT : KTF , LGU+ : LGT
	<input type="hidden" name="fix_commid" value="KTF"/>
	-->
	<!-- 사이트코드 -->
	<input type="hidden" name="site_cd" value="<?php echo $siteInfo['s_join_auth_kcb_code']; ?>" />
	<!-- Ret_URL : 인증결과 리턴 페이지 ( 가맹점 URL 로 설정해 주셔야 합니다. ) -->
	<input type="hidden" name="Ret_URL" value="<?php echo $system['url'] . OD_PROGRAM_DIR; ?>/member.join.auth.step3.php" />
	<!-- cert_otp_use 필수 ( 메뉴얼 참고)
	Y : 실명 확인 + OTP 점유 확인 , N : 실명 확인 only
	-->
	<input type="hidden" name="cert_otp_use" value="Y"/>
	<!-- cert_enc_use 필수 (고정값 : 메뉴얼 참고) -->
	<input type="hidden" name="cert_enc_use" value="Y"/>

	<input type="hidden" name="res_cd" value=""/>
	<input type="hidden" name="res_msg" value=""/>

	<!-- up_hash 검증 을 위한 필드 -->
	<input type="hidden" name="veri_up_hash" value=""/>

	<!-- 본인확인 input 비활성화 -->
	<input type="hidden" name="cert_able_yn" value="N"/>

	<!-- web_siteid 을 위한 필드 -->
	<input type="hidden" name="web_siteid_hashYN" value="N"/>

	<!-- 가맹점 사용 필드 (인증완료시 리턴)-->
	<input type="hidden" name="param_opt_1" value=""/>
	<input type="hidden" name="param_opt_2" value=""/>
	<input type="hidden" name="param_opt_3" value=""/>

	<!-- 결과 저장 -->
	<input type="hidden" name="phone_no" value=""/>
	<input type="hidden" name="user_name" value=""/>
	<input type="hidden" name="birth_day" value=""/>
	<input type="hidden" name="sex_code" value=""/>
	<input type="hidden" name="ci_url" value=""/>
	<input type="hidden" name="di_url" value=""/>

	<?php if($siteInfo['s_join_auth_kcb_enckey'] <> ''){ ?>
	<!-- SSJ : KCP 본인인증 모듈 가맹점인증키 추가 패치 : 2021-03-12 -->
	<!-- 내/외국인구분 -->
	<input type="hidden" name="local_code" value=""/>
	<!-- 리턴 암호화 고도화 -->
	<input type="hidden" name="cert_enc_use_ext" value="Y"/>
	<?php } ?>
</form>

<?php // 본인확인 kms 2019-09-16 ?>
<iframe id="kcp_cert" name="kcp_cert" width="100%" height="850" frameborder="0" scrolling="no" style="display:none"></iframe>
<script type="text/javascript">
	// data
	var scAuthR = ''; var scAuthN = ''; var scAuthB = ''; var scAuthS = ''; var scAuthH = '';

	// 결제창 종료후 인증데이터 리턴 함수
	function auth_data( frm )
	{
		var auth_form     = document.form_auth;
		var nField        = frm.elements.length;
		var response_data = "";

		// up_hash 검증
		if( frm.up_hash.value != auth_form.veri_up_hash.value )
		{
			alert("up_hash 변조 위험있음");

		}else{
			$(frm).find('input').each(function(){
				var _name = $(this).attr('name');
				var _value = $(this).val();
				//$(auth_form).find('input[name='+_name+']').val(_value);

				switch(_name){
					case 'user_name':
						$('.auth_name').val(_value)
						break;
					case 'birth_day':
						$('.auth_birth').val(_value)
						break;
					case 'sex_code':
						$('.auth_sex[value=' + _value + ']').attr({'checked':'checked'});
						break;
					case 'phone_no':
						$('.auth_phone').val(_value)
						break;
				}
			});

			//auth_form = frm
		}
	}

    // 인증창 호출 함수
    var txt_auth_msg = "휴대폰 본인인증 모듈을 불러오는 중입니다.\n\n잠시후 다시 시도해 주시기 바랍니다.";
    function auth_type_check()
    {
        // 본인인증 후 취소 시 새창이 뜨는것을 방지하기 위해 새로 iframe 을 생성
        document.getElementById( "kcp_cert" ).remove();
        var ifrm = document.createElement("iframe");
        ifrm.setAttribute('id','kcp_cert');
        ifrm.setAttribute('name','kcp_cert');
        ifrm.setAttribute('width','100%');
        ifrm.setAttribute('height','850');
        ifrm.setAttribute('frameborder','0');
        ifrm.setAttribute('scrolling','no');
        ifrm.setAttribute('style','display:none;min-height:100vh;');
        document.body.appendChild(ifrm);

        document.documentElement.scrollTop =0;

        init_orderid(); // 주문번호 재생성
        var auth_form = document.form_auth;
        if( auth_form.ordr_idxx.value == "" )
        {
            alert( txt_auth_msg );

            return false;
        }
        else
        {
            if( navigator.userAgent.indexOf("Android") > - 1 || navigator.userAgent.indexOf("iPhone") > - 1 )
            {
                auth_form.target = "kcp_cert";

                document.getElementById( "cert_info" ).style.display = "none";
                document.getElementById( "kcp_cert"  ).style.display = "";
            }
            else
            {
                var return_gubun;
                var width  = 410;
                var height = 500;

                var leftpos = screen.width  / 2 - ( width  / 2 );
                var toppos  = screen.height / 2 - ( height / 2 );

                var winopts  = "width=" + width   + ", height=" + height + ", toolbar=no,status=no,statusbar=no,menubar=no,scrollbars=no,resizable=no";
                var position = ",left=" + leftpos + ", top="    + toppos;
                var AUTH_POP = window.open('','auth_popup', winopts + position);

                auth_form.target = "auth_popup"; // !!주의 고정값 ( 리턴받을때 사용되는 타겟명입니다.)
            }

            auth_form.method = "post";
            auth_form.action = "<?php echo OD_PROGRAM_URL; ?>/member.join.auth.step2.php"; // 인증창 호출 및 결과값 리턴 페이지 주소
            auth_form.submit();

            return true;
        }
    }


	// 주문번호 생성 예제 ( up_hash 생성시 필요 )
	window.onload=init_orderid;
	function init_orderid()
	{
		var today = new Date();
		var year  = today.getFullYear();
		var month = today.getMonth()+ 1;
		var date  = today.getDate();
		var time  = today.getTime();
		var uniq = Math.floor(Math.random() * 10000) + 1;
		uniq = pad(uniq, 4);

		if(parseInt(month) < 10)
		{
			month = "0" + month;
		}

		var vOrderID = year + "" + month + "" + date + "" + time + "" + uniq;

		document.form_auth.ordr_idxx.value = vOrderID;
		txt_auth_msg = "주문번호는 필수 입니다.";
	}

	function pad(n, width) {
		n = n + '';
		return n.length >= width ? n : new Array(width - n.length + 1).join('0') + n;
	}



	function kcp_submit() {
		var code = scAuthR == '0000';
		var name = ($('.auth_name').length == 1 ? ($('.auth_name').val() == scAuthN) : true);
		var birth = ($('.auth_birth').length == 1 ? ($('.auth_birth').val() == scAuthB) : true);
		var sex = ($('.auth_sex:checked').length == 1 ? ($('.auth_sex:checked').val() == scAuthS) : true);
		var phone = ($('.auth_phone').length == 1 ? ($('.auth_phone').val() == scAuthH) : true);

		if( !scAuthR ) {
			alert('본인 인증후 <?php echo ($_ATUH_TYPE_ == "modify" ? "정보수정이" : "회원가입이"); ?> 가능합니다.');
			return false;
		}
		else if( !code ) {
			alert('본인 인증에 실패하였습니다.\n사유: ' + scAuthM);
			return false;
		}else if( !(name && birth && sex && phone) ) {
			alert('본인 인증 정보가 변조되었습니다. \n\n본인인증을 다시 시도해 주시기 바랍니다.');
			return false;
		}
		return true;
	}

	// 본인인증 전 알림 메세지
	$(document).ready(function(){
		$('.js_auth_before').on('click', function(){
			// === 본인인증 창 띄우기 통합 kms 2019-06-21 ====
			auth_type_check();
			// === 본인인증 창 띄우기 통합 kms 2019-06-21 ====
			return false;
		});
	});

</script>