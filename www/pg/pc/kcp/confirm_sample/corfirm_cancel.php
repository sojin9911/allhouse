<?
    /* ============================================================================== */
    /* =   PAGE : ����ũ�� ���� ���� PAGE                                           = */
    /* = -------------------------------------------------------------------------- = */
    /* =   �Ʒ��� �� ���� �� �κ��� �� �����Ͻþ� ������ �����Ͻñ� �ٶ��ϴ�.       = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ������ ������ �߻��ϴ� ��� �Ʒ��� �ּҷ� �����ϼż� Ȯ���Ͻñ� �ٶ��ϴ�.= */
    /* =   ���� �ּ� : http://kcp.co.kr/technique.requestcode.do                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2013   KCP Inc.   All Rights Reserverd.                   = */
    /* ============================================================================== */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title>*** KCP [AX-HUB Version] ***</title>
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
    <link href="../sample/css/style.css" rel="stylesheet" type="text/css"/>

    <script language="javascript">
    function  jsf__go_mod( form )
    {
        if(form.mod_method.value=="mod_method_not_sel")
        {
          alert( "�ŷ������� �����Ͻʽÿ�.");
          return false;
        }
        if(form.mod_type.value=="mod_type_not_sel")
        {
          alert( "��� ������ �����Ͻʽÿ�.");
          return false;
        }
        else if ( form.tno.value.length < 14 )
        {
            alert( "KCP �ŷ� ��ȣ�� �Է��ϼ���." );
            form.tno.focus();
            form.tno.select();
            return false;
        }
        /* �Էµ����� ��ȿ�� ���� Start */
        else if (form.mod_method.value == "VCNT" && form.mod_account.value=="")
        {
            alert( "ȯ�� ���� ���¹�ȣ�� �Է��ϼ���." );
            form.mod_account.focus();
            form.mod_account.select();
            return false;
        }
        else if (form.mod_method.value == "VCNT" && form.mod_depositor.value=="")
        {
            alert( "ȯ�� ���� �����ָ��� �Է��ϼ���." );
            form.mod_depositor.focus();
            form.mod_depositor.select();
            return false;
        }
        else if (form.mod_method.value == "VCNT" && form.mod_bankcode.value=="mod_bankcode_not_sel")
        {
            alert( "ȯ�� ���� �����ڵ带 ������ �ּ���." );
            return false;
        }
        /* �Էµ����� ��ȿ�� ���� End */
        return true;
    }

    function type_chk( form )
    {
        /* mod_type �ʱ�ȭ */
        document.getElementById("type_R").style.display = "none";

        /* �ſ�ī�� : ��� */
        /* ������ü : ��� */
        if (form.mod_method.value == "CARD" || form.mod_method.value == "ACNT")
        {
            form.sub_mod_type.value = "STSC";
            form.mod_sub_type.value = "MDSC03";

            document.getElementById("type_R").style.display = "none";
        }

        /* ������� : ȯ�� , �κ�ȯ�� */
        if (form.mod_method.value == "VCNT")
        {
            form.sub_mod_type.value = "STHD";
            form.mod_sub_type.value = "MDSC00";

            document.getElementById("type_R").style.display = "block";
        }
    }

    </script>
</head>

<body>

    <div id="sample_wrap">

    <form name="mod_escrow_form" method="post" action="pp_cli_hub.php">

                 <!-- Ÿ��Ʋ Start-->
                    <h1>[��ҿ�û] <span>�� �������� ����ũ�� ����Ȯ���� ���(ȯ��)�ϴ� ����(����) �������Դϴ�.</span></h1>
                 <!-- Ÿ��Ʋ End -->

                    <!-- ��� ���̺� Start -->
                    <div class="sample">
                    <p>
                    �ҽ� ������ �ҽ� �ȿ� <span>�� ���� ��</span>ǥ�ð� ���Ե� ������ �������� ��Ȳ�� �°� ������ ����<br/>
                    �����Ͻñ� �ٶ��ϴ�.<br/>
                    <span>�� �������� ����ũ�� ����Ȯ���� ���(ȯ��)�� ��û�ϴ� ������ �Դϴ�.</span><br/>
                    ������ ���εǸ� ��������� KCP �ŷ���ȣ(tno)���� ���� �� �ֽ��ϴ�.<br/>
                    ������������ �� KCP �ŷ���ȣ(tno)������ ����ũ�� ���º��� ��û �� �� �ֽ��ϴ�.
                    </p>
                    <!-- ��� ���̺� End -->

                <!-- ��� ��û ���� �Է� ���̺� Start -->
                    <h2>&sdot; ����ũ�� ����Ȯ�� �� ��� ����</h2>
                    <table class="tbl" cellpadding="0" cellspacing="0">

                    <!-- ���(ȯ��) ���� ���� -->
                    <tr>
                        <th>���(ȯ��) ���� ����</th>
                        <td>
                          <select name="mod_method" onChange="javascript:type_chk(this.form);">
                            <option value="mod_method_not_sel" selected>�����Ͻʽÿ�</option>
                            <option value="CARD">�ſ�ī�� ���</option>
                            <option value="ACNT">������ü ���</option>
                            <option value="VCNT">������� ȯ��</option>
                          </select>
                        </td>
                    </tr>
                    <!-- Input : ������ ���� �ŷ���ȣ(14 byte) �Է� -->
                    <tr>
                        <th>KCP �ŷ���ȣ</th>
                        <td><input type="text" name="tno" value=""  class="w200" maxlength="14"/></td>
                    </tr>
                     <!-- Input : ���� ����(mod_desc) �Է� -->
                    <tr>
                        <th>���� ����</th>
                        <td><input type="text" name="mod_desc" value="" class="w200" maxlength="50"/></td>
                    </tr>
                    </table>

                <div id="type_R" style="display:none">
                    <table class="tbl" cellpadding="0" cellspacing="0">
                     <!-- Input : ȯ�� �����ָ� �Է� -->
                    <tr>
                        <th>ȯ�� �����ָ�</th>
                        <td>
                            <input type='text' name='mod_depositor' class='w200' value='' size='20' maxlength='20'>
                        </td>
                    </tr>
                     <!-- Input : ȯ�� ���¹�ȣ �Է� -->
                    <tr>
                        <th>ȯ�� ���� ��ȣ</th>
                        <td>
                            <input type='text' name='mod_account' class='w200' value='' size='20' maxlength='20'>
                        </td>
                    </tr>
                     <!-- Input : ȯ�� �����ڵ� �Է� -->
                    <tr>
                        <th>ȯ�������ڵ�</th>
                        <td>
                            <select name="mod_bankcode">
                                <option value="mod_bankcode_not_sel" selected>����</option>
                                <option value="BK39">�泲����</option>
                                <option value="BK03">�������</option>
                                <option value="BK32">�λ�����</option>
                                <option value="BK07">�����߾�ȸ</option>
                                <option value="BK48">����</option>
                                <option value="BK71">��ü��</option>
                                <option value="BK23">��������</option>
                                <option value="BK06">��������</option>
                                <option value="BK81">�ϳ�����</option>
                                <option value="BK34">��������</option>
                                <option value="BK11">�����߾�ȸ</option>
                                <option value="BK02">�������</option>
                                <option value="BK53">��Ƽ����</option>
                                <option value="BK05">��ȯ����</option>
                                <option value="BK09">���ſ�</option>
                                <option value="BK35">��������</option>
                                <option value="BK16">�����߾�ȸ</option>
                                <option value="BK27">�ѹ�����</option>
                                <option value="BK04">��������</option>
                                <option value="BK31">�뱸����</option>
                                <option value="BK25">��������</option>
                                <option value="BK26">��������</option>
                                <option value="BK20">�츮����</option>
                                <option value="BK37">��������</option>
                                <option value="BK21">��������</option>
                                <option value="BK83">��ȭ����</option>
                            </select>
                        </td>
                    </tr>
                </table>
                </div>

                <!-- ����ũ�� ���º��� ��û/ó������ -->
                    <!-- ���� ��ư ���̺� Start -->
                    <div class="btnset">
                    <input name="" type="submit" class="submit" value="�����û" onclick="return jsf__go_mod(this.form);" alt="����ũ�� ����Ȯ���� ��û�մϴ�"/>
					<a href="../index.html" class="home">ó������</a>
                    </div>
                    <!-- ���� ��ư ���̺� End -->
                </div>
            <div class="footer">
                Copyright (c) KCP INC. All Rights reserved.
            </div>
        </table>
<?
    /* ============================================================================== */
    /* =   1-1. ��� ��û �ʼ� ���� ����                                            = */
    /* = -------------------------------------------------------------------------- = */
    /* =   �� �ʼ� - �ݵ�� �ʿ��� �����Դϴ�.                                      = */
    /* = ---------------------------------------------------------------------------= */
?>
        <input type="hidden" name="req_tx"          value="mod_escrow" />
        <input type="hidden" name="mod_type"        value="STE9" />
        <input type="hidden" name="mod_sub_type"    value="" />
        <input type="hidden" name="sub_mod_type"    value="" />
<?
    /* = -------------------------------------------------------------------------- = */
    /* =   1. ��� ��û ���� END                                                    = */
    /* ============================================================================== */
?>
    </form>
</div>
</body>
</html>

