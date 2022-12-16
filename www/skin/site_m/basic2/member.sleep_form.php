<!-- ◆공통페이지 섹션 -->
<div class="c_section ">
	<form action="<?php echo OD_PROGRAM_URL; ?>/member.sleep_pro.php" method="post" target="common_frame">
		<input type="hidden" name="_mode" value="<?php echo $sleepData['mode'];?>">
		<input type="hidden" name="_id" value="<?php echo $_id; ?>">
		<!-- ◆휴면회원인증 -->
		<div class="c_complete my_sleep">
			<div class="complete_box">
				<div class="tit">휴면 회원 인증</div>
				<div class="sub_txt">
					회원님은 현재 장기 미사용 계정으로 휴면전환된 상태입니다.<br/>
					<?php if( $sleepData['type'] == 'auth'){ ?> 
					휴면 상태를 풀기 위해서는 <strong>이메일 인증절차</strong>를 거쳐야 합니다.<br/>
					<?php } ?>
					아래 버튼을 클릭하여 인증을 진행하시기 바랍니다.
				</div>
			</div>
			<div class="c_btnbox ">
				<ul>
					<li><a href="#none" onclick="$(this).closest('form').submit();" class="c_btn h55 color">휴면회원 인증 진행</a></li>
				</ul>
			</div>
		</div>
		<!-- /휴면회원인증 -->
	</form>
</div>
<!-- /공통페이지 섹션 -->