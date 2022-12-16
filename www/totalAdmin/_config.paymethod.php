<?php
include_once('wrap.header.php');
$r = _MQ(" select * from smart_setup where s_uid = 1 ");
?>
<form action="_config.paymethod.pro.php" method="post">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>무통장입금</th>
					<td>
						<?php echo _InputRadio('s_pg_paymethod_B', array('Y', 'N'), ($r['s_pg_paymethod_B']?$r['s_pg_paymethod_B']:'N'), '', array('사용', '미사용'), ''); ?>
					</td>
					<th>신용카드</th>
					<td>
						<?php echo _InputRadio('s_pg_paymethod_C', array('Y', 'N'), ($r['s_pg_paymethod_C']?$r['s_pg_paymethod_C']:'N'), '', array('사용', '미사용'), ''); ?>
					</td>
				</tr>
				<tr>
					<th>실시간 계좌이체</th>
					<td>
						<?php echo _InputRadio('s_pg_paymethod_L', array('Y', 'N'), ($r['s_pg_paymethod_L']?$r['s_pg_paymethod_L']:'N'), '', array('사용', '미사용'), ''); ?>
						<?php echo _DescStr('모바일에서는 제공되지 않습니다.'); ?>
					</td>
					<th>가상계좌</th>
					<td>
						<?php echo _InputRadio('s_pg_paymethod_V', array('Y', 'N'), ($r['s_pg_paymethod_V']?$r['s_pg_paymethod_V']:'N'), '', array('사용', '미사용'), ''); ?>
					</td>
				</tr>
				<tr>
					<th>휴대폰 결제</th>
					<td colspan="3">
						<?php echo _InputRadio('s_pg_paymethod_H', array('Y', 'N'), ($r['s_pg_paymethod_H']?$r['s_pg_paymethod_H']:'N'), '', array('사용', '미사용'), ''); ?>
						<?php echo _DescStr('<a href="_config.pg_mobile.form.php"><u>휴대폰 결제 서비스 설정</u></a>을 설정 후 이용 가능합니다.'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<!-- 저장 -->
	<div class="c_btnbox">
		<ul>
			<li><span class="c_btn h46 red"><input type="submit" value="확인" /></span></li>
		</ul>
	</div>
	<!-- 저장 -->
</form>
<?php include_once('wrap.footer.php'); ?>