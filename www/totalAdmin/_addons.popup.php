<?php
$app_mode = 'popup';
include_once('inc.header.php');
?>
<div class="popup">
	<?php if( $_mode == '080info'){ ?>
	<div class="pop_title"><strong>080 수신거부 서비스 소개</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="140">
				<col width="*-">
			</colgroup>
			<tbody>
				<tr>
					<td colspan="2">
						<img src="<?php echo OD_ADDONS_URL; ?>/080deny/images/info1.jpg" alt="080 수신거부 서비스소개">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="c_btnbox">
		<ul>
			<li><a href="#none" onclick="window.close(); return false;" class="c_btn h34 black line normal">닫기</a></li>
		</ul>
	</div>
	<?php }else if( $_mode == '080help'){ ?>
	<div class="pop_title"><strong>080 수신거부 서비스 이용절차</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="140">
				<col width="*-">
			</colgroup>
			<tbody>
				<tr>
					<td colspan="2">
						<img src="<?php echo OD_ADDONS_URL; ?>/080deny/images/info2.jpg" alt="080 수신거부 이용절차" usemap="#imgmap">
						<map id="imgmap" name="imgmap">
							<area shape="rect" alt="신청서 작성 예시" title="신청서 작성 예시" coords="541,398,642,417" href="http://biz080.com/request_write_sample.php" target="_blank" />
							<area shape="rect" alt="080 가입신청서" title="080 가입신청서" coords="469,420,644,468" href="http://biz080.com/download/080_sms_request_form.zip" target="_blank" />
						</map>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="c_btnbox">
		<ul>
			<li><a href="#none" onclick="window.close(); return false;" class="c_btn h34 black line normal">닫기</a></li>
		</ul>
	</div>
	<?php }else if( $_mode == '2yearSend'){ // 발송하기 :: _2year_opt.form.php 에서 실행 ?>
	<div class="pop_title"><strong>수신동의 발송하기</strong></div>
	<form name='frm2yearOptSend' id="frm2yearOptSend" method='post'>
		<?php
			$mr_cnt = _MQ(" select count(*) as cnt from smart_2year_opt_log where ol_status='N'  "); // 수신동의 2년 지난 -  회원
		?>
		<input type="hidden" value="<?php echo $mr_cnt['cnt'];  ?>" name="sendCnt">
		<div class="data_form">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th class="ess">발송 대상회원 수</th>
						<td class="only_text">
							<?php echo number_format($mr_cnt['cnt']).'명';?>
						</td>
					</tr>
					<tr>
						<th class="ess">발송타입</th>
						<td>
							<?php ?>
							<?php echo _InputRadio("_type", array('email','sms' , 'both'), "email" , '', array('이메일발송' , '문자발송' , '이메일 + 문자발송'), '') ?>
						</td>
					</tr>

					<tr>
						<td colspan="2">
							<div class="tip_box">
								<?=_DescStr("수신동의 2년이 지난 회원중 이메일, 휴대폰에 대한 수신동의를 하신 회원에게 발송이 됩니다.","black")?>
								<?=_DescStr("<strong>이메일발송 :</strong> 이메일만 발송 됩니다.")?>
								<?=_DescStr("<strong>문자발송 :</strong> SMS문자만 발송 됩니다.")?>
								<?=_DescStr("<strong>이메일 + 문자발송 :</strong> 이메일 과 SMS문자가 같이 발송됩니다.")?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="c_btnbox">
			<ul>
				<li><a id="mailsend_submit" href="#none" onclick="return false;" class="c_btn h34 black">발송하기</a></li>
				<li><a href="#none" onclick="window.close(); return false;" class="c_btn h34 black line normal">닫기</a></li>
			</ul>
		</div>
	</form>

	<?// --------------------- progress bar 적용 --------------------- //?>
	<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script> -->
	<style>
		.ui-progressbar {position: relative;}
		.progress-label {position: absolute;left: 50%;top: 4px;font-weight: bold;text-shadow: 1px 1px 0 #fff;}
	</style>
	<script>
		$(function() {

			var progressbar = $( "#progressbar" ), progressLabel = $( ".progress-label" );

			progressbar.progressbar({
				value: false,
				change: function() { progressLabel.text( progressbar.progressbar( "value" ) + "%" ); },
				complete: function() {progressLabel.text( "Complete!" );}
			});

			function progress() {
				progressbar = $( "#progressbar" ).show();
				var max_var = <?=$mr_cnt['cnt']?> * 1;
				max_var = max_var ? max_var : 1;
				$.ajax({
					data: {'_mode':'send' , '_type' : $("input[name='_type']").filter(function() {if (this.checked) return this;}).val()},
					type: 'POST', cache: false,
					url: '/addons/2yearOpt/_2year_opt.pro.php',
					success: function(data) {
						var app_data = data * 1;
						//console.log( max_var +' '+ app_data );
						progressbar.progressbar( "value",  Math.round(( (max_var - app_data) * 100  / max_var  )) + 1 );
						if ( app_data > 1 ) { setTimeout( progress, 300 ); }
						if( app_data == 0 ) { alert("발송을 완료하였습니다."); location.href=("_addons.php?pass_menu=2yearOpt/_2year_opt.form"); }
					}
				});
			}

			$("#mailsend_submit").click(function(){
				if(confirm("발송형태에 따라 시간이 걸리 수 있습니다.\n\n정말 발송하시겠습니까?")){
					var chkCnt = $('form#frm2yearOptSend').find('input[name="sendCnt"]').val()*1;
					if( chkCnt < 1){ alert("수신동의 2년이 지난 회원이 존재하지 않습니다."); return false;  }
					$("form[name='frm02']")[0].submit();
					progress();
				}
			});
		});
	</script>

	<div id="progressbar" style="display:none; margin:20px;"><div class="progress-label">Loading...</div></div>

	<?php }else if( $_mode == '2yearMail'){ // 발송 메일 설정 :: _2year_opt.form.php 에서 실행  ?>
	<div class="pop_title"><strong>수신동의 발송메일 설정하기</strong></div>
	<form name='frm2yearOptMail' id="frm2yearOptMail" method='post' action="/addons/2yearOpt/_2year_opt.pro.php">
		<input type="hidden" name="_mode" value="mailsetup">
		<?php if($_sub_mode == 'view') { // 보기 모드 일시 ?>
		<div class="email_preview">
		<?php
			include_once(OD_MAIL_ROOT."/mail.contents.2yearOpt.php"); // 메일 내용 불러오기 ($mailing_content)
			// --- 타이틀 ---
			$_title = "[".$siteInfo['s_adshop']."]" . stripslashes($siteInfo['s_2year_opt_title']);
			$_content = get_mail_content($mailling_content);
			echo $_content;
		?>
		</div>

		<?php }else{ ?>
		<div class="data_list">
			<table class="table_form">
				<colgroup>
					<col width="140">
					<col width="*-">
				</colgroup>
				<tbody>
					<tr>
						<th>제목</th>
						<td>
							<input type="text" class="design" value="[사이트명]" disabled style="width:78px;">
							<input type="text" name="_2year_opt_title" class="design" value="<?=stripslashes($siteInfo['s_2year_opt_title'])?>" style="width:400px">
							<?php echo _DescStr("[사이트명]의 경우 자동으로 메일내용에 포함되어 발송이 됩니다."); ?>
						</td>
					</tr>
					<tr>
						<th>상단 내용</th>
						<td class="edit_td">
							<textarea name="_2year_opt_content_top" class="input_text SEditor" style="width:100%;height:400px;" hname='상단 내용'><?php echo stripslashes($siteInfo['s_2year_opt_content_top']); ?></textarea>
						</td>
					</tr>
					<tr>
						<th>치환자 안내</th>
						<td class="edit_td">
							<div class="tip_box">
								<?=_DescStr("치환자 : <strong>{회원명} - 회원명 , {회원아이디} - 회원아이디</strong>")?>
								<?=_DescStr("치환자를 이용하여 내용에 회원명이나 회원아이디를 넣을 수 있습니다.")?>
							</div>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>



		<div class="c_btnbox">
			<ul>
				<?php if( $_sub_mode == 'view'){  ?>
				<li><a href="_addons.popup.php?_mode=2yearMail" class="c_btn h34 red line normal">돌아가기</a></li>
				<?php }else{ ?>
				<li><a href="_addons.popup.php?_mode=2yearMail&_sub_mode=view" class="c_btn h34 red line normal">메일미리보기</a></li>
				<?php } ?>
				<li><span class="c_btn h34 black"><input type="submit" value="확인" class=""></span></li>
				<li><a href="#none" onclick="window.close(); return false;" class="c_btn h34 black line normal">닫기</a></li>
			</ul>
		</div>

	</form>

	<?php } ?>

</div>




<script type="text/javascript">
	$(document).ready(function(){
		$('.js_html_viewer').on('keypress cut paste click', function(e) {
			e.preventDefault();
			if(e.type == 'click') document.execCommand('selectAll',false,null);
		});
	});
</script>
<style type="text/css">
	.js_html_viewer {
		float: left;
		background: #fff;
		box-sizing: border-box;
		border: 1px solid #d9dee3;
		padding: 0 5px;
		margin-right: 5px;
		overflow: hidden;
		padding: 4px 10px 5px 9px;
		width:100%;
	}
	strong { font-weight:600; }
	span { display: inline; }
</style>
<?php
include_once('inc.footer.php');
?>