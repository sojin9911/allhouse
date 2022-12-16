<?PHP
include_once(dirname(__FILE__).'/inc.php');

/**************************************************************************
	파일명 : hs_cnfrm_popup3.php
	
	본인확인서비스 결과 화면(return url)
**************************************************************************/



	// --------------- 환경설정 : 2013-07-31 정준철 -------------
	$onedaynet_kcb_service_type = "service"; // test , service - 실 적용시 service 적용
	// --------------- 환경설정 : 2013-07-31 정준철 -------------



	/* 공통 리턴 항목 */
	$idcfMbrComCd			=	$_POST["idcf_mbr_com_cd"];		// 고객사코드
	$hsCertSvcTxSeqno		=	$_POST["hs_cert_svc_tx_seqno"];	// 거래번호
	$rqstSiteNm				=	$_POST["rqst_site_nm"];			// 접속도메인	
	$hsCertRqstCausCd		=	$_POST["hs_cert_rqst_caus_cd"];	// 인증요청사유코드 2byte  (00:회원가입, 01:성인인증, 02:회원정보수정, 03:비밀번호찾기, 04:상품구매, 99:기타)

	$resultCd				=	$_POST["result_cd"];			// 결과코드
	$resultMsg				=	$_POST["result_msg"];			// 결과메세지
	$certDtTm				=	$_POST["cert_dt_tm"];			// 인증일시

	/**************************************************************************
	 * 모듈 호출	; 본인확인서비스 결과 데이터를 복호화한다.
	 **************************************************************************/
	$encInfo = $_POST["encInfo"];

	//KCB서버 공개키
	$WEBPUBKEY = trim($_POST["WEBPUBKEY"]);
	//KCB서버 서명값
	$WEBSIGNATURE = trim($_POST["WEBSIGNATURE"]);

	// ########################################################################
	// # 운영전환시 변경 필요
	// ########################################################################
	if($onedaynet_kcb_service_type == "test") {
		$endPointUrl = "http://tsafe.ok-name.co.kr:29080/KcbWebService/OkNameService";//EndPointURL, 테스트 서버
	}
	else {
		$endPointUrl = "http://safe.ok-name.co.kr/KcbWebService/OkNameService";// 운영 서버
	}

	//okname 실행 정보
	// ########################################################################
	// # 모듈 경로 지정 및 권한 부여 (hs_cnfrm_popup2.php에서 설정된 값과 동일하게 설정)
	// ########################################################################
	$exe = AUTH_DIR. "/kcb/okname";

	// ########################################################################
	// # 암호화키 파일 설정 (절대경로) - 파일은 주어진 파일명으로 자동 생성되며, 매월마다 갱신됨 
	// # 만일 키파일이 갱신되지 않으면 복화화데이터가 깨지는 현상이 발생됨.
	// ########################################################################
	$keyPath = AUTH_DIR. "/kcb/key/safecert_$idcfMbrComCd.key";

	// ########################################################################
	// # 로그 경로 지정 및 권한 부여 (hs_cnfrm_popup2.asp에서 설정된 값과 동일하게 설정)
	// ########################################################################
	$logPath = AUTH_DIR. "/kcb/log/";

	// ########################################################################
	// # 옵션값에 'L'을 추가하는 경우에만 로그가 생성됨.
	// ########################################################################
	$options = "SLU";	// S:인증결과복호화 , L - 로그기록 , U - utf8

	// 명령어
	$cmd = "$exe $keyPath $idcfMbrComCd $endPointUrl $WEBPUBKEY $WEBSIGNATURE $encInfo $logPath $options";
	//echo $cmd;
	// 실행
	exec($cmd, $out, $ret);
    
	if($ret == 0) {
		//echo "복호화 요청 호출 성공.<br/>";		 
		// 결과라인에서 값을 추출
		foreach($out as $a => $b) {
			if($a < 17) {
				$field[$a] = $b;
			}
		}
	}
	else {
		echo "복호화 요청 호출 에러. 리턴값 : ".$ret."<br/>";		 
	}

//*/
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<title>KCB 본인확인서비스</title>
    <script language="javascript" type="text/javascript" >
	function fncOpenerSubmit() {

		opener.document.kcbResultForm.idcf_mbr_com_cd.value = "<?=$idcfMbrComCd?>";
		opener.document.kcbResultForm.hs_cert_rqst_caus_cd.value = "<?=$hsCertRqstCausCd?>";
		opener.document.kcbResultForm.result_cd.value = "<?=$field[0]?>";
		opener.document.kcbResultForm.result_msg.value = "<?=$field[1]?>";
		opener.document.kcbResultForm.hs_cert_svc_tx_seqno.value = "<?=$field[2]?>";
		opener.document.kcbResultForm.cert_dt_tm.value = "<?=$field[3]?>";
		opener.document.kcbResultForm.di.value = "<?=$field[4]?>";
		opener.document.kcbResultForm.ci.value = "<?=$field[5]?>";
		opener.document.kcbResultForm.name.value = "<?=$field[7]?>";
		opener.document.kcbResultForm.birthday.value = "<?=$field[8]?>";
		opener.document.kcbResultForm.gender.value = "<?=$field[9]?>";
		opener.document.kcbResultForm.nation.value = "<?=$field[10]?>";
		opener.document.kcbResultForm.tel_com_cd.value = "<?=$field[11]?>";
		opener.document.kcbResultForm.tel_no.value = "<?=$field[12]?>";
		opener.document.kcbResultForm.return_msg.value = "<?=$field[16]?>";
		opener.document.kcbResultForm.submit();

		self.close();
	}	
	</script>
</head>
<body>
</body>
<?php
	if($ret == 0) {
		//인증결과 복호화 성공
		echo ("<script>fncOpenerSubmit();</script>");
	} else {
		//인증결과 복호화 실패
		echo ("<script>alert(\"인증결과복호화 실패 : $ret.\"); </script>");
	}
?>
</html>