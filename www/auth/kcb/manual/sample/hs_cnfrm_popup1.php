<?php
//**************************************************************************
// ���ϸ� : hs_cnfrm_popup1.php
//
// ����Ȯ�μ��� ��û ���� �Է� ȭ��
//
//**************************************************************************
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html" charset="euc-kr">
<title>KCB ����Ȯ�μ��� ����</title>
<script>
<!--
	function jsSubmit(){	
		var form1 = document.form1;
		var isChecked = false;
		var inTpBit = "";

		for(i=0; i<form1.in_tp_bit.length; i++){
			if(form1.in_tp_bit[i].checked){
				inTpBit = form1.in_tp_bit[i].value;
				isChecked = true;
				break;
			}
		}
		
		if(!(isChecked)){
			alert("�Է������� �������ּ���");
			return;
		}

		if (inTpBit & 1) {
			if (form1.name.value == "") {
				alert("������ �Է����ּ���");
				return;
			}
		}
		if (inTpBit & 2) {
			if (form1.birthday.value == "") {
				alert("��������� �Է����ּ���");
				return;
			}
		}
		if (inTpBit & 8) {
			if (form1.tel_com_cd.value == "") {
				alert("��Ż��ڵ带 �Է����ּ���");
				return;
			}
			if (form1.tel_no.value == "") {
				alert("�޴�����ȣ�� �Է����ּ���");
				return;
			}
		}

		window.open("", "auth_popup", "width=430,height=590,scrollbar=yes");

		var form1 = document.form1;
		form1.target = "auth_popup";
		form1.submit();
	}
//-->
</script>
</head>
 <body>
	<form name="form1" action="hs_cnfrm_popup2.php" method="post">
		<table>
			<tr>
				<td colspan="2"><strong> - KCB �������� �Է¿�</strong></td>
			</tr>
			<tr>
				<td>�Է�����</td>
				<td>
					<?php
					// �Է������� ������ ������ �����ϴ�.
					//  1 : 0001 - ����
					//  2 : 0010 - �������
					//  3 : 0011 - ������� + ���� 
					//  4 : 0100 - ����,���ܱ��α���
					//  5 : 0101 - ����,���ܱ��α��� + ����
					//  6 : 0110 - ����,���ܱ��α��� + �������
					//  7 : 0111 - ����,���ܱ��α��� + ������� + ����
					//  8 : 1000 - ��Ż�,�޴�����ȣ
					//  9 : 1001 - ��Ż�,�޴�����ȣ + ����
					// 10 : 1010 - ��Ż�,�޴�����ȣ + �������
					// 11 : 1011 - ��Ż�,�޴�����ȣ + ������� + ����
					// 12 : 1100 - ��Ż�,�޴�����ȣ + ����,���ܱ��α���
					// 13 : 1101 - ��Ż�,�޴�����ȣ + ����,���ܱ��α��� + ����
					// 14 : 1110 - ��Ż�,�޴�����ȣ + ����,���ܱ��α��� + �������
					// 15 : 1111 - ��Ż�,�޴�����ȣ + ����,���ܱ��α��� + ������� + ����
					?>
					<input type="radio" name="in_tp_bit" value="0">���� (�˾����� ��� ������ �Է�)<br/>
					<input type="radio" name="in_tp_bit" value="7">����+�������+����,���ܱ��α���<br/>
					<input type="radio" name="in_tp_bit" value="8">��Ż�,�޴�����ȣ<br/>
					<input type="radio" name="in_tp_bit" value="15" checked>����+�������+����,���ܱ��α���+��Ż�,�޴�����ȣ<br/>
				</td>
			</tr>
			<tr>
				<td>����</td>
				<td>
					<input type="text" name="name" maxlength="20" size="20" value="">
				</td>
			</tr>
			<tr>
				<td>�������</td>
				<td>
					<input type="text" name="birthday" maxlength="8" size="10" value="">
				</td>
			</tr>
			<tr>
				<td>����</td>
				<td>
					<input type="radio" name="gender" value="1" checked>��
					<input type="radio" name="gender" value="0">��
			</tr>
			<tr>
				<td>���ܱ��α���</td>
				<td>
					<input type="radio" name="nation" value="1" checked>������
					<input type="radio" name="nation" value="2">�ܱ���
			</tr>
			<tr>
				<td>�޴���</td>
				<td>
					<input type="radio" name="tel_com_cd" value="01" checked>SKT
					<input type="radio" name="tel_com_cd" value="02">KT
					<input type="radio" name="tel_com_cd" value="03">LGU<br/>
					<input type="text" name="tel_no" maxlength="11" size="15" value="">
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="button" value="����Ȯ�μ���" onClick="jsSubmit();"></td>
			</tr>
		</table>
	</form>

	<!-- ����Ȯ�� ó����� ���� -->
	<form name="kcbResultForm" method="post" >
        <input type="hidden" name="idcf_mbr_com_cd" 		value="" 	/>
        <input type="hidden" name="hs_cert_svc_tx_seqno" 	value=""	/>
        <input type="hidden" name="hs_cert_rqst_caus_cd" 	value="" 	/>
        <input type="hidden" name="result_cd" 				value="" 	/>
        <input type="hidden" name="result_msg" 				value="" 	/>
        <input type="hidden" name="cert_dt_tm" 				value="" 	/>
        <input type="hidden" name="di" 						value="" 	/>
        <input type="hidden" name="ci" 						value="" 	/>
        <input type="hidden" name="name" 					value="" 	/>
        <input type="hidden" name="birthday" 				value="" 	/>
        <input type="hidden" name="gender" 					value="" 	/>
        <input type="hidden" name="nation" 					value="" 	/>
        <input type="hidden" name="tel_com_cd" 				value="" 	/>
        <input type="hidden" name="tel_no" 					value="" 	/>
        <input type="hidden" name="return_msg" 				value="" 	/>
	</form>  
 </body>
</html>
