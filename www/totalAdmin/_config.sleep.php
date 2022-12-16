<?php
include_once('wrap.header.php');
$r = _MQ(" select * from smart_setup where s_uid = 1 ");
// member_return_type
?>

<form action="_config.sleep.pro.php" method="post" name="frm">
	<input type="hidden" name="_mode" value="modify">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>  
					<th class="ess">휴면해제 방법</th>
					<td>
						<?php echo _InputRadio('member_return_type', array('login','auth'), ($r['member_return_type']?$r['member_return_type']:'auth'), '', array('로그인 시 인증없이 휴면해제','로그인 시 이메일 인증 후 휴면해제')); ?>
						<div class="tip_box">
							<?php echo _DescStr('설정된 방법에 따라 로그인 시 휴면해제에 대한 설정이 적용됩니다.'); ?>
						</div>
					</td>
				</tr>				
				<tr>
					<th class="ess">휴면회원전환 개월 수</th>
					<td>
						<input type="text" name="member_sleep_period" class="design t_center" value="<?php echo $r['member_sleep_period']; ?>" style="width:50px" required><span class="fr_tx">개월</span>
						<div class="tip_box">
							<?php echo _DescStr('개월 단위로 지정할 수 있으며 회원의 최근 로그인 날짜가 설정한 휴면계정전환 개월 수를 넘으면 1일 1회 체크하여 휴면 전환됩니다.'); ?>
							<?php echo _DescStr('정보통신망법에 따라 12개월(1년) 이상 일시에만 설정이 가능합니다.'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>휴면해제 시 회원등급초기화 설정</th>
					<td>
						<?php echo _InputRadio('member_return_groupinit', array('Y', 'N'), ($r['member_return_groupinit']?$r['member_return_groupinit']:'N'), '', array('사용', '사용안함'), ''); ?>
						<div class="tip_box">
							<?php echo _DescStr('<em>사용</em>으로 설정 할경우 휴면해제시 기존등급과 상관없이 기본순위 등급으로 변경됩니다.'); ?>
							<?php echo _DescStr('<em>사용안함</em>으로 설정 할 경우 휴면해제 시 기존 등급이 유지됩니다.'); ?>
						</div>
					</td>
				</tr>

			</tbody>
		</table>
	</div>


		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h46 red"><input type="submit" name="" value="확인"></span></li>
			</ul>
		</div>

</form>


<script> 


$(document).ready(funcValidate); // validate 검사

// -- 서브밋 검증 
function funcValidate()
{
	$("form[name=frm]").validate({
			ignore: ".ignore",
			rules: {
					member_sleep_period: { required: true , min : 12 }
			},
			invalidHandler: function(event, validator) {
				// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.
			},
			messages: {
					member_sleep_period: { required: "휴면회원전환 개월 수를 입력해 주세요." , min : "휴면회원 전환 개월 수는 최소 12개월 이상 입력해 주세요." }
			},
			submitHandler : function(form) {
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
				form.submit();
			}
	});	
}


</script>


<?php include_once('wrap.footer.php'); ?>