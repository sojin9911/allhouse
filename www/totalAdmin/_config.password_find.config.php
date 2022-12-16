<?php
include_once('wrap.header.php');
$r = _MQ("select * from smart_setup where s_uid = 1 ");
$SMSUser = onedaynet_sms_user();
$SMSCnt = 0;
if($SMSUser['code'] == 'U00') $SMSCnt = $SMSUser['data'];
else $SMSCnt = '<a href="_config.sms.out_list.php" class="t_orange">발송불가</a>';
?>
<form action="_config.password_find.pro.php" method="post" onsubmit="return validate_check();">
	<!-- 사이트 기본설정 -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">이메일</th>
					<td colspan="3">
						<?php echo _InputRadio('_find_pw_email', array('Y', 'N'), ($r['s_find_pw_email'] == 'Y'?'Y':'N'), ' class="js_find_pw_email"', array('사용', '미사용'), ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="tip_box">
							<?php echo _DescStr('등록된 이메일 주소로 임시 비밀번호를 발행합니다.'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th class="ess">휴대폰</th>
					<td>
						<?php echo _InputRadio('_find_pw_sms', array('Y', 'N'), ($r['s_find_pw_sms'] == 'Y'?'Y':'N'), ' class="js_find_pw_sms"', array('사용', '미사용'), ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="tip_box">
							<?php echo _DescStr('등록된 휴대폰으로 임시 비밀번호를 발행합니다.'); ?>
							<?php echo _DescStr('휴대폰이 등록되지 않은 경우, 임시 비밀번호를 발행하지 않습니다.'); ?>
							<?php echo _DescStr('문자 발송을 위하여 SMS 잔여건수가 남아있어야합니다. '); ?>
						</div>
					<th>SMS정보</th>
					<td>
						<table class="table_form if_insum">
							<tbody>
								<tr>
									<th>현재 잔여건수</th>
									<td><span class="t_red bold"><?php echo (is_numeric($SMSCnt)?number_format($SMSCnt , 1).'건':$SMSCnt); ?></span></td>
								</tr>
								<tr>
									<th>SMS충전관리</th>
									<td><div class="lineup-center"><a href="_config.sms.out_list.php" class="c_btn h27 line">SMS충전관리 바로가기</a></div></td>
								</tr>
								<tr>
									<th>SMS발송내역</th>
									<td><div class="lineup-center"><a href="_config.sms.out_list.php" class="c_btn h27 gray">SMS발송내역 바로가기</a></div></td>
								</tr>
							</tbody>
						</table>
					</td>
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

<script type="text/javascript">
	function validate_check() {
		var find_pw_email = $('.js_find_pw_email:checked').val();
		var find_pw_sms = $('.js_find_pw_sms:checked').val();
		if(find_pw_email == 'N' && find_pw_sms == 'N') {
			alert('비밀번호 찾기 설정 시 이메일 또는 휴대전화 중 1건 이상 사용처리를 하여야 합니다.');
			return false;
		}
	}
</script>
<?php include_once('wrap.footer.php'); ?>