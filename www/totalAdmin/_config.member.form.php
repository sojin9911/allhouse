<?php
include_once('wrap.header.php');
?>
<form action="_config.member.pro.php" method="post">
<input type="hidden" name="_mode" value="modify">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>본인확인 서비스 사용</th>
					<td>
						<?php echo _InputRadio('_join_auth_use', array('Y', 'N'), ($siteInfo['s_join_auth_use'] == 'Y'?'Y':'N'), '', array('사용', '미사용'), ''); ?>
					</td>
				</tr>
				<!-- SSJ : KCP 본인인증 모듈 가맹점인증키 추가 패치 : 2021-03-12 -->
				<tr>
					<th>KCP 회원사 코드</th>
					<td>
						<input type="text" name="_join_auth_kcb_code" class="design" value="<?php echo $siteInfo['s_join_auth_kcb_code']; ?>" style="width:185px">
					</td>
				</tr>
				<tr>
					<th>KCP 가맹점 인증키</th>
					<td>
						<input type="text" name="_join_auth_kcb_enckey" class="design" value="<?php echo $siteInfo['s_join_auth_kcb_enckey']; ?>" style="width:500px">
						<div class="tip_box">
						<?php echo _DescStr('<em>가맹점인증키</em>를 발급받지 않은 경우 공란으로 비워두시기 바랍니다.'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>참고사항</th>
					<td>
						<div class="tip_box">
							<?php echo _DescStr('<a href="/totalAdmin/_content.php?cont=member_hp" target="_blank"><em>휴대폰본인확인 서비스</em></a>메뉴 에서 "본인확인서비스 신청절차" 및 "본인확인서비스 사용 혜택"을 확인하실 수 있습니다.'); ?>
							<?php
								// SSJ : KCP안내문구 추가 - 일반가맹점 모듈번전 업데이트 공지에따른 안내문구 : 2020-12-28
								echo _DescStr('위 "휴대폰본인확인 서비스" 메뉴의 신청하기 버튼을 통하지 않고, KCP와 직접 계약하여 진행할 경우 본인확인 서비스가 제대로 작동되지 않을 수 있습니다.<br>반드시 위 "휴대폰본인확인 서비스" 메뉴에서 신청하기 버튼을 통해 진행해 주시기 바랍니다. ', 'black');
							?>
							<?php echo _DescStr('
								[KCP 본인확인 테스트 코드 사용방법]<br>
								1. 테스트 회원사 코드는 <em>S6186</em>입니다. 회원사 코드에 테스트 회원사 코드를 입력해 주시기 바랍니다.<br>
								2. 테스트 가맹점 인증키는 <em>E66DCEB95BFBD45DF9DFAEEBCB092B5DC2EB3BF0</em>입니다. 가맹점 인증키에 테스트 가맹점 인증키를 입력해 주시기 바랍니다.<br>
								3. 테스트 회원사 코드 입력 시 <em>KT</em>로만 인증 가능합니다. <br>
								4. 그외 인증정보(성명, 생년월일, 성별, 휴대폰번호)는 임의로 입력가능 합니다.<br>
								5. 인증 문자가 발송되는 대신 <em>OTP_NO = XXXXXXX</em>와같은 형식으로 알림창에 인증번호가 노출됩니다. <br>
								6. 알림창에 노출된 인증번호를 입력하면 인증이 완료됩니다.<br>
								7. 인증번호가 노출되지 않을 경우 임의의 6자리 숫자를 입력하시면 인증이 완료됩니다. <br>
							'); ?>
						</div>
					</td>
				</tr>
				<!-- // SSJ : KCP 본인인증 모듈 가맹점인증키 추가 패치 : 2021-03-12 -->
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