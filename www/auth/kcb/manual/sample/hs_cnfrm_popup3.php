<?php
/**************************************************************************
	���ϸ� : hs_cnfrm_popup3.php
	
	����Ȯ�μ��� ��� ȭ��(return url)
**************************************************************************/
	
	/* ���� ���� �׸� */
	$idcfMbrComCd			=	$_POST["idcf_mbr_com_cd"];		// �����ڵ�
	$hsCertSvcTxSeqno		=	$_POST["hs_cert_svc_tx_seqno"];	// �ŷ���ȣ
	$rqstSiteNm				=	$_POST["rqst_site_nm"];			// ���ӵ�����	
	$hsCertRqstCausCd		=	$_POST["hs_cert_rqst_caus_cd"];	// ������û�����ڵ� 2byte  (00:ȸ������, 01:��������, 02:ȸ����������, 03:��й�ȣã��, 04:��ǰ����, 99:��Ÿ)

	$resultCd				=	$_POST["result_cd"];			// ����ڵ�
	$resultMsg				=	$_POST["result_msg"];			// ����޼���
	$certDtTm				=	$_POST["cert_dt_tm"];			// �����Ͻ�

	/**************************************************************************
	 * ��� ȣ��	; ����Ȯ�μ��� ��� �����͸� ��ȣȭ�Ѵ�.
	 **************************************************************************/
	$encInfo = $_POST["encInfo"];

	//KCB���� ����Ű
	$WEBPUBKEY = trim($_POST["WEBPUBKEY"]);
	//KCB���� ����
	$WEBSIGNATURE = trim($_POST["WEBSIGNATURE"]);

	// ########################################################################
	// # ���ȯ�� ���� �ʿ�
	// ########################################################################
	$endPointUrl = "http://tsafe.ok-name.co.kr:29080/KcbWebService/OkNameService";//EndPointURL, �׽�Ʈ ����
	//$endPointUrl = "http://safe.ok-name.co.kr/KcbWebService/OkNameService";// � ����
		  
	//okname ���� ����
	// ########################################################################
	// # ��� ��� ���� �� ���� �ο� (hs_cnfrm_popup2.php���� ������ ���� �����ϰ� ����)
	// ########################################################################
	$exe = "C:\\okname\\win32\\okname.exe";

	// ########################################################################
	// # ��ȣȭŰ ���� ���� (������) - ������ �־��� ���ϸ����� �ڵ� �����Ǹ�, �ſ����� ���ŵ� 
	// # ���� Ű������ ���ŵ��� ������ ��ȭȭ�����Ͱ� ������ ������ �߻���.
	// ########################################################################
	$keyPath = "C:\\okname\\safecert_$idcfMbrComCd.key";

	// ########################################################################
	// # �α� ��� ���� �� ���� �ο� (hs_cnfrm_popup2.asp���� ������ ���� �����ϰ� ����)
	// ########################################################################
	$logPath = "C:\\okname\\";

	// ########################################################################
	// # �ɼǰ��� 'L'�� �߰��ϴ� ��쿡�� �αװ� ������.
	// ########################################################################
	$options = "SL";	// S:���������ȣȭ
		
	// ��ɾ�
	$cmd = "$exe $keyPath $idcfMbrComCd $endPointUrl $WEBPUBKEY $WEBSIGNATURE $encInfo $logPath $options";
	echo "$cmd<br>";
	
	// ����
	exec($cmd, $out, $ret);
    echo "ret=$ret<br/>";
    
	if($ret == 0) {
		echo "��ȣȭ ��û ȣ�� ����.<br/>";		 
		// ������ο��� ���� ����
		foreach($out as $a => $b) {
			if($a < 17) {
				$field[$a] = $b;
			}
		}
	}
	else {
		echo "��ȣȭ ��û ȣ�� ����. ���ϰ� : ".$ret."<br/>";		 
	}

    echo "��ȣȭó������ڵ�:$ret	<br/>";		 
    echo "ó������ڵ�		:$field[0]	<br/>";		 
    echo "ó������޽���	:$field[1]	<br/>";		 
    echo "�ŷ��Ϸù�ȣ		:$field[2]	<br/>";		 
    echo "�����Ͻ�			:$field[3]	<br/>";		 
    echo "DI				:$field[4]	<br/>";		 
    echo "CI				:$field[5]	<br/>";		 
    echo "����				:$field[7]	<br/>";		 
    echo "�������			:$field[8]	<br/>";		 
    echo "����				:$field[9]	<br/>";		 
    echo "���ܱ��α���		:$field[10]	<br/>";	 
    echo "��Ż��ڵ�		:$field[11]	<br/>";	 
    echo "�޴�����ȣ		:$field[12]	<br/>";	 
    echo "���ϸ޽���		:$field[16]	<br/>";	 
    
//*/
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
	<title>KCB ����Ȯ�μ��� ����</title>
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
		opener.document.kcbResultForm.action = "hs_cnfrm_popup4.php";

		opener.document.kcbResultForm.submit();
		self.close();
	}	
	</script>
</head>
<body>
	<b>����Ȯ�ΰ��</b>
 	����ڵ�		: <?=$resultCd?><br />
 	����޼���		: <?=$resultMsg?><br />
	�ŷ���ȣ		: <?=$hsCertSvcTxSeqno?><br />
 	�����Ͻ�		: <?=$certDtTm?><br />
	�����ڵ�		: <?=$idcfMbrComCd?><br />
	���ӵ�����		: <?=$rqstSiteNm?><br />
	������û�����ڵ�: <?=$hsCertRqstCausCd?><br />
</body>
<?php
	if($ret == 0) {
		//������� ��ȣȭ ����
		echo ("<script>fncOpenerSubmit();</script>");
	} else {
		//������� ��ȣȭ ����
		echo ("<script>alert(\"���������ȣȭ ���� : $ret.\"); self.close(); </script>");
	}
?>
</html>
