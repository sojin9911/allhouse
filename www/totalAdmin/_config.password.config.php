<?php
include_once('wrap.header.php');
$r = _MQ("select * from smart_setup where s_uid = 1 ");
?>
<form action="_config.password.pro.php" method="post">
	<!-- 사이트 기본설정 -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">변경 안내주기</th>
					<td colspan="3">
						<input type="text" name="member_cpw_period" class="design t_center" value="<?php echo $r['member_cpw_period']; ?>" style="width:50px" required><span class="fr_tx">개월</span>
						<div class="tip_box">
							<?php echo _DescStr('개월 단위로 지정할 수 있으며, 회원이 비밀번호를 변경한 날이 지정한 개월 수를 넘을 경우 작동됩니다.'); ?>
							<?php echo _DescStr('3개월로 처리할 시 3개월 동안 한 번도 비밀번호를 변경하지 않는 회원은 3개월 후 로그인 시 비밀번호 변경 안내페이지가 노출됩니다.'); ?>
						</div>
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