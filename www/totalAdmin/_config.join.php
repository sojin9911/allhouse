<?php
include_once('wrap.header.php');
$r = _MQ(" select * from smart_setup where s_uid = 1 ");

$r['join_email_list'] = preg_replace('/[@]/','',$r['join_email_list']);
?>
<form action="_config.join.pro.php" method="post" name="frmConfigJoin">
	<div class="group_title"><strong>회원가입 정책</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>가입승인</th>
					<td>
						<?php echo _InputRadio('join_approve', array('Y', 'N'), ($r['join_approve']?$r['join_approve']:'N'), '', array('자동승인', '승인후 가입'), ''); ?>
						<div class="tip_box">
							<?php echo _DescStr('자동승인으로 설정할 경우 회원가입 후 별도 절차 없이 자동 승인되어 바로 로그인 가능합니다.'); ?>
							<?php echo _DescStr('승인후 가입으로 설정할 경우 회원가입 후 관리자 승인 이후에 로그인 이 가능합니다.'); ?>
							<?php echo _DescStr('회원의 승인 설정은 회원관리 페이지에서 확인 가능합니다.'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>가입제한 아이디</th>
					<td>
						<input type="text" name="join_ban_id"  class="design js_tag" style="width:100%;" value="<?php echo $r['join_ban_id']; ?>">
						<div class="tip_box">
							<?php echo _DescStr('가입제한 아이디를 공백없이 입력해 주세요.(Enter 혹은 Tab으로 구분)'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<div class="group_title"><strong>회원가입 기본항목</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180">
				<col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>아이디</th>
					<td>
						<span class="fr_tx">최소 :</span>
						<input type="text" name="join_id_limit_min" class="design" value="<?php echo $r['join_id_limit_min']; ?>" style="width:40px;">
						<div class="bar"></div>

						<span class="fr_tx">최대 :</span>
						<input type="text" name="join_id_limit_max" class="design" value="<?php echo $r['join_id_limit_max']; ?>" style="width:40px;">
						<div class="tip_box">
							<?php echo _DescStr('아이디 최소, 최대 길이값을 제한합니다.'); ?>
							<?php echo _DescStr('최소 입력 기본값은 4자리 입니다.'); ?>
							<?php echo _DescStr('최대 입력값을 제한하지 않을 경우 0을 입력하세요.'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>비밀번호</th>
					<td>
						<span class="fr_tx">최소 :</span>
						<input type="text" name="join_pw_limit_min" class="design" value="<?php echo $r['join_pw_limit_min']; ?>" style="width:40px;">
						<div class="bar"></div>

						<span class="fr_tx">최대 :</span>
						<input type="text" name="join_pw_limit_max" class="design" value="<?php echo $r['join_pw_limit_max']; ?>" style="width:40px;">
						<div class="tip_box">
							<?php echo _DescStr('비밀번호 최소, 최대 길이값을 제한합니다.'); ?>
							<?php echo _DescStr('최소 입력 기본값은 4자리 입니다.'); ?>
							<?php echo _DescStr('최대 입력값을 제한하지 않을 경우 0을 입력하세요.'); ?>
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>

						<span class="fr_tx">특수문자 혼용 필수 :</span>
						<?php echo _InputRadio('join_pw_sp_use', array('Y', 'N'), ($r['join_pw_sp_use']?$r['join_pw_sp_use']:'N'), '', array('사용', '미사용'), ''); ?>

						<input type="text" name="join_pw_sp_length" class="design js_join_pw_sp_use" value="<?php echo $r['join_pw_sp_length']; ?>" style="width:40px;">
						<span class="fr_tx">개 이상 포함</span>
						<div class="tip_box">
							<?php echo _DescStr('비밀번호를 입력시 특수문자가 꼭 들어가도록 설정합니다.'); ?>
						</div>
						<div class="dash_line"><!-- 점선라인 --></div>


						<span class="fr_tx">대문자 혼용 필수 :</span>
						<?php echo _InputRadio('join_pw_up_use', array('Y', 'N'), ($r['join_pw_up_use']?$r['join_pw_up_use']:'N'), '', array('사용', '미사용'), ''); ?>
						<input type="text" name="join_pw_up_length" class="design js_pw_up_use" value="<?php echo $r['join_pw_up_length']; ?>" style="width:40px;">
						<span class="fr_tx">개 이상 포함</span>
						<div class="tip_box">
							<?php echo _DescStr('비밀번호를 입력시 알파벳 대문자가 꼭 들어가도록 설정합니다.'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>이름</th>
					<td>
						<?php echo _DescStr('이름 항목은 필수값으로 한글만 사용 가능합니다.', 'black'); ?>
					</td>
				</tr>
				<tr>
					<th>이메일</th>
					<td>
						<input type="text" class="design js_tag" name="join_email_list" value="<?php echo $r['join_email_list']; ?>" style="width:100%;">
						<div class="tip_box">
							<?php echo _DescStr('회원가입시 이메일 선택 박스의 항목을 지정합니다.(Enter 혹은 Tab으로 구분)'); ?>
							<?php echo _DescStr('공백없이 입력해주세요.'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>휴대폰</th>
					<td>
						<?php echo _DescStr('휴대폰은 필수값으로 전화번호 유형만 사용 가능합니다.', 'black'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<div class="group_title"><strong>회원가입 추가항목</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180">
				<col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>추가항목 개별설정</th>
					<td>
						<table class="table_list">
							<colgroup>
								<col width="180">
								<col width="*">
								<col width="*">
							</colgroup>
							<thead>
								<tr>
									<th>항목명</th>
									<th>사용여부</th>
									<th>필수여부</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th>전화번호</th>
									<td>
										<?php echo _InputRadio('join_tel', array('Y', 'N'), ($r['join_tel']?$r['join_tel']:'N'), '', array('사용', '미사용'), ''); ?>
									</td>
									<td>
										<?php echo _InputRadio('join_tel_required', array('Y', 'N'), ($r['join_tel_required']?$r['join_tel_required']:'N'), '', array('필수', '비필수'), ''); ?>
									</td>
								</tr>
								<tr>
									<th>주소</th>
									<td>
										<?php echo _InputRadio('join_addr', array('Y', 'N'), ($r['join_addr']?$r['join_addr']:'N'), '', array('사용', '미사용'), ''); ?>
									</td>
									<td>
										<?php echo _InputRadio('join_addr_required', array('Y', 'N'), ($r['join_addr_required']?$r['join_addr_required']:'N'), '', array('필수', '비필수'), ''); ?>
									</td>
								</tr>
								<tr>
									<th>생일</th>
									<td>
										<?php echo _InputRadio('join_birth', array('Y', 'N'), ($r['join_birth']?$r['join_birth']:'N'), '', array('사용', '미사용'), ''); ?>
									</td>
									<td>
										<?php echo _InputRadio('join_birth_required', array('Y', 'N'), ($r['join_birth_required']?$r['join_birth_required']:'N'), '', array('필수', '비필수'), ''); ?>
									</td>
								</tr>
								<tr>
									<th>성별</th>
									<td>
										<?php echo _InputRadio('join_sex', array('Y', 'N'), ($r['join_sex']?$r['join_sex']:'N'), '', array('사용', '미사용'), ''); ?>
									</td>
									<td>
										<?php echo _InputRadio('join_sex_required', array('Y', 'N'), ($r['join_sex_required']?$r['join_sex_required']:'N'), '', array('필수', '비필수'), ''); ?>
									</td>
								</tr>
								<tr>
									<th>스팸방지</th>
									<td>
										<?php echo _InputRadio('join_spam', array('Y', 'N'), ($r['join_spam']?$r['join_spam']:'N'), '', array('사용', '미사용'), ''); ?>
									</td>
									<td>
										<?php echo _InputRadio('join_spam_required', array('Y', 'N'), 'Y', 'disabled', array('필수', '비필수'), ''); ?>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="tip_box">
							<?php echo _DescStr('스팸방지는 구글 reCAPTCHA를 사용하며 <a href="_config.sns.form.php"><u>환경설정 - SNS 로그인/API 설정  - 스팸방지 구글 API</u></a>를 설정하여야 사용가능합니다.'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php echo _submitBTNsub(); ?>
</form>


<script type="text/javascript">
	$(document).ready(function(){
		// -  validate ---
		$('form[name=frmConfigJoin]').validate({
			ignore: '.ignore',
			rules: {
				join_id_limit_min: { required: true , min: 4  }
				, join_pw_limit_min: { required: true , min : 4 }
			},
			messages: {
				join_id_limit_min: { required: '(아이디) 최소 길이 값을 입력해 주세요,' , min: '(아이디) 최소 길이는 4 이상 입력하셔야합니다.'  }
				, join_pw_limit_min: { required: '(비밀번호) 최소 길이 값을 입력해 주세요,' , min: '(비밀번호) 최소 길이는 4 이상 입력하셔야합니다.'  }
			},
			submitHandler : function(form) {

				form.submit();
			}
		});
		// - validate ---
	});
</script>

<?php include_once('wrap.footer.php'); ?>