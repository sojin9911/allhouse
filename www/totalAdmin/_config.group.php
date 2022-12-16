<?php
include_once('wrap.header.php');
$r = _MQ(" select * from smart_setup where s_uid = 1 ");
?>

<form action="_config.group.pro.php" method="post" name="frm">
	<input type="hidden" name="_mode" value="modify">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>회원등급평가 방법</th>
					<td>
						<?php echo _InputRadio('groupset_autouse', array('Y', 'N'), ($r['groupset_autouse']?$r['groupset_autouse']:'N'), '', array('자동평가', '수동평가'), ''); ?>
						<div class="tip_box">
							<?php echo _DescStr('회원 등급평가를 자동으로 설정할 시 자동평가 기간설정에서 설정하신 기간 마다 등급평가가 진행됩니다.'); ?>
							<?php echo _DescStr('회원 등급평가를 수동으로 설정할 시 <a href="#none" onclick="return false;" class="groupset-auto-apply" data-apply="true" style="color:#000;">[회원등급 수동평가]</a> 를 클릭하여 회원등급을 평가할 수 있습니다.</a>'); ?>
							<?php echo _DescStr( rm_str($r['groupset_apply_rdate']) < 1 ? '최근 등급평가된 기록이 없습니다.':'최근등급평가일은 <em>'.$r['groupset_apply_rdate'].'</em> 입니다.' ); ?>							
						</div>
					</td>
				</tr>
				<tr>
					<th>자동평가 기간설정</th>
					<td>
						<?php echo _InputRadio('groupset_auto_daily', array('day', 'week', 'month'), ($r['groupset_auto_daily']?$r['groupset_auto_daily']:'month'), '', array('매일', '매주','매달'), ''); ?>
						<div class="tip_box">
							<?php echo _DescStr('회원등급평가가 자동일 시 자동평가 기간을 설정할 수 있는 항목입니다. '); ?>
						</div>
					</td>
				</tr>

				<tr>
					<th>특정기간 설정</th>
					<td>
						<?php 
							echo _InputSelect( "groupset_check_term" , array_keys($arrGroupsetCheckTerm['print']) , $r['groupset_check_term'] , "" , array_values($arrGroupsetCheckTerm['print']) , "-특정기간-");
						?>
						<div class="tip_box">
							<?php echo _DescStr('회원등급 평가 시 특정기간을 설정할 수 있으며 설정된 기간에 따라 회원등급별 평가기준 조건에 따른 평가를 하게됩니다.'); ?>
							<?php echo _DescStr('등급별 평가기준은 <em>회원관리 > 회원등급관리</em> 에서 각 등급별 평가기준을 설정할 수 있습니다.'); ?>
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
$(document).on('click','.groupset-auto-apply',groupsetAutoApply)

// -- 수동평가 클릭 시 
function groupsetAutoApply()
{
	var apply = $('.groupset-auto-apply').attr('data-apply'); // 연속클릭방지
	if( apply != 'true'){ alert("[잠시만 기다려주세요.]\n현재 수동등급평가가 진행중입니다."); return false; }
	if( confirm("회원 등급 평가를 수동으로 진행하시겠습니까?") == false){ return false; }
	$('.groupset-auto-apply').attr('data-apply','false');
	$('[name="_mode"]').val('groupset_apply');
	$('form[name="frm"]').submit();
}

// -- 서브밋 검증 
function funcValidate()
{
	$("form[name=frm]").validate({
			ignore: ".ignore",
			rules: {
					groupset_check_term: { required: true }
					// ,_content_m: { required: function(){ return (  );} }
			},
			invalidHandler: function(event, validator) {
				// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.

			},
			messages: {
					groupset_check_term: { required: "특정기간을 설정해 주세요." }
			},
			submitHandler : function(form) {
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
				form.submit();
			}
	});	
}


</script>


<?php include_once('wrap.footer.php'); ?>