<?php
    /**************************************************************************
	 * ���ϸ� : hs_cnfrm_popup2.php
	 *
	 * ����Ȯ�μ��� ���� ���� �Է� ȭ��
	 *    (�� �������� KCB�˾�â���� �Է¿�)
     *
     * ������
     * 	���� ��ÿ��� 
     * 	response.write�� ����Ͽ� ȭ�鿡 �������� �����͸� 
     * 	�����Ͽ� �ֽñ� �ٶ��ϴ�. �湮�ڿ��� ����Ʈ�����Ͱ� ����� �� �ֽ��ϴ�.
     **************************************************************************/

	// ���񽺰ŷ���ȣ�� �����Ѵ�.
	function generateSvcTxSeqno() {   
		$numbers  = "0123456789";   
		$svcTxSeqno = date("YmdHis");   
		$nmr_loops = 6;   
		while ($nmr_loops--) {   
			$svcTxSeqno .= $numbers[mt_rand(0, strlen($numbers))];   
		}   
		return $svcTxSeqno;   
	}   

	/**************************************************************************
	 * okname ����Ȯ�μ��� �Ķ����
	 **************************************************************************/
	$inTpBit = $_POST["in_tp_bit"];	// �Է±����ڵ�(0:����, 1:�⺻����, 2:���ܱ���, 4:�޴�������)
	$name = "x";										// ����
	$birthday = "x";									// ������� 
	$gender = "x";										// ����
	$nation="x";										// ���ܱ��α��� 
	$telComCd="x";										// �̵���Ż��ڵ� 
	$telNo="x";											// �޴�����ȣ 

	$inTpBitVal = intval($inTpBit, 0);
	if (($inTpBitVal & 1) == 1) {
		$name = $_POST["name"];							// ����
	}
	
	if (($inTpBitVal & 2) == 2) {
		$birthday = $_POST["birthday"];					// �������
	}
	
	if (($inTpBitVal & 4) == 4) {
		$gender = $_POST["gender"];						// ����
		$nation = $_POST["nation"];			// ���ܱ��α���
	}
	
	if (($inTpBitVal & 8) == 8) {
		$telComCd = $_POST["tel_com_cd"];			// ��Ż��ڵ�
		$telNo = $_POST["tel_no"];					// �޴�����ȣ
	}

	$svcTxSeqno = generateSvcTxSeqno();					// �ŷ���ȣ. ���Ϲ��ڿ��� �ι� ����� �� ����. (�ִ� 30�ڸ��� ���ڿ�. 0-9,A-Z,a-z ���)
	
	// ########################################################################
	// # ���ȯ�� Ȯ�� �ʿ�
	// ########################################################################
	$memId = "P00000000000";							// ȸ�����ڵ�

	$clientIp = "x";									// ��⼳ġ ������ ������ IP
	$clientDomain = "ok-name.co.kr";					// ȸ���� ������. (�޴���������ȣ �߼۽� ���޻�� ����)
	
	$rsv1 = "0";										// ���� �׸�
	$rsv2 = "0";										// ���� �׸�
	$rsv3 = "0";										// ���� �׸�
	
	$hsCertMsrCd = "10";								// ���������ڵ� 2byte  (10:�ڵ���)
	$hsCertRqstCausCd = "00";							// ������û�����ڵ� 2byte  (00:ȸ������, 01:��������, 02:ȸ����������, 03:��й�ȣã��, 04:��ǰ����, 99:��Ÿ)
	
	$returnMsg = "x";									// ���ϸ޽��� (������ 'x') 
	
	// ########################################################################
	// # ���� URL ����
	// ########################################################################
	// opener(hs_cnfrm_popup1.php)�� �����ϰ� ��ġ�ϵ��� �����ؾ� ��. 
	// (http://www.test.co.kr�� http://test.co.kr�� �ٸ� ���������� �ν��ϸ�, http �� https�� ��ġ�ؾ� ��)
	$returnUrl = "http://localhost:8008/test/hs_cnfrm_popup3.php";// �������� �Ϸ��� ���ϵ� URL (������ ���� full path)
	
	// ########################################################################
	// # ���ȯ�� ���� �ʿ�
	// ########################################################################
	$endPointURL = "http://tsafe.ok-name.co.kr:29080/KcbWebService/OkNameService";	// �׽�Ʈ ����
	//$endPointURL = "http://safe.ok-name.co.kr/KcbWebService/OkNameService"; // � ���� 
	
    //okname ���� ����
	// ########################################################################
	// # ��� ��� ���� �� ���� �ο� (������)
	// ########################################################################
	$exe = "c:\\okname\\win32\\okname.exe";				// okname ���� ����

	// ########################################################################
	// # �α� ��� ���� �� ���� �ο� (������)
	// ########################################################################
	$logPath = "c:\\okname\\";

	// ########################################################################
	// # �ɼǰ��� 'L'�� �߰��ϴ� ��쿡�� �α�(logPath������ ������)�� ������.
	// ########################################################################
	$options = "QL";		// Q:������û������ ��ȣȭ
	
	$cmd = "$exe $svcTxSeqno \"$name\" $birthday $gender $nation $telComCd $telNo $rsv1 $rsv2 $rsv3 \"$returnMsg\" $returnUrl $inTpBit $hsCertMsrCd $hsCertRqstCausCd $memId $clientIp $clientDomain $endPointURL $logPath $options";
	
//	echo $cmd."<br>";
	
	/**************************************************************************
	okname ����
	**************************************************************************/
	
	//cmd ����
	exec($cmd, $out, $ret);
//	echo "ret=".$ret."<br>";
	
	/**************************************************************************
	okname ���� ����
	**************************************************************************/
	$retcode = "";										// ����ڵ�
	$retmsg = "";										// ����޽���
	$e_rqstData = "";									// ��ȣȭ�ȿ�û������
	
	if ($ret == 0) {//������ ��� ������ ������� ����
		$retcode = $out[0];
		$retmsg  = $out[1];
		$e_rqstData = $out[2];
	}
	else {
		if($ret <=200)
			$retcode=sprintf("B%03d", $ret);
		else
			$retcode=sprintf("S%03d", $ret);
	}
	
	/**************************************************************************
	 * hs_cnfrm_popup3.php ���� ����
	 **************************************************************************/
	$targetId = "";		// Ÿ��ID (����� ������ �˾��� ���� ���� ��� �ش� �˾���(window.name ������)�� ����. �Ϲ������� ""���� ����)

	// ########################################################################
	// # ���ȯ�� ���� �ʿ�
	// ########################################################################
    $commonSvlUrl = "https://tsafe.ok-name.co.kr:2443/CommonSvl";	// �׽�Ʈ URL
    //$commonSvlUrl = "https://safe.ok-name.co.kr/CommonSvl";	// � URL
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<title>KCB ����Ȯ�μ��� ����</title>
	<script>
		function request(){
		window.name = "<?=$targetId?>";

		document.form1.action = "<?=$commonSvlUrl?>";
		document.form1.method = "post";

		document.form1.submit();
	}
	</script>
</head>

 <body>
	<form name="form1">
	<!-- ���� ��û ���� -->
	<!--// �ʼ� �׸� -->
	<input type="hidden" name="tc" value="kcb.oknm.online.safehscert.popup.cmd.P901_CertChoiceCmd">				<!-- ����Ұ�-->
	<input type="hidden" name="rqst_data"				value="<?=$e_rqstData?>">		<!-- ��û������ -->
	<input type="hidden" name="target_id"				value="<?=$targetId?>">				<!-- Ÿ��ID --> 
	<!-- �ʼ� �׸� //-->	
	</form>
<?php
 	if ($retcode == "B000") {
		//������û
		echo ("<script>request();</script>");
	} else {
		//��û ���� �������� ����
		echo ("<script>alert(\"$retcode\"); self.close();</script>");
	}
?>
 </body>
</html>
