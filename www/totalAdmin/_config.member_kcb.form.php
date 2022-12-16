<?php
include_once('wrap.header.php');
$r = _MQ("select * from smart_setup where s_uid = 1 ");
?>
<form action="_config.member.pro.php" method="post">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>본인확인 서비스 사용</th>
					<td>
						<?php echo _InputRadio('_join_auth_use', array('Y', 'N'), ($r['s_join_auth_use'] == 'Y'?'Y':'N'), '', array('사용', '미사용'), ''); ?>
					</td>
				</tr>
				<tr>
					<th>KCB 회원사 코드</th>
					<td>
						<input type="text" name="_join_auth_kcb_code" class="design" value="<?php echo $r['s_join_auth_kcb_code']; ?>" style="width:185px">
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