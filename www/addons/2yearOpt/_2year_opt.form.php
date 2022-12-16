<?php

	// /pages/inc.daily.update.php 기능 테스트
	//------------------------- 있을 경우 관리자 메인페이지 노출하여야 함.....................

	// JJC : 수정 : 2021-05-17
	//$mr_cnt = _MQ(" select count(*) as cnt from smart_2year_opt_log where ol_status='N'  "); // 수신동의 2년 지난 -  회원
	$mr_cnt = _MQ(" select count(*) as cnt from smart_2year_opt_log INNER JOIN smart_individual on (in_id = ol_mid and in_sleep_type = 'N' AND in_out = 'N' and in_userlevel != '9') where ol_status='N'  ");
?>
<form name='frm2yearOpt01' id="frm2yearOpt01" method='post' action="/addons/2yearOpt/_2year_opt.pro.php">
	<input type='hidden' name='_mode' value='setup'>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">매2년마다 수신동의 발송적용여부</th>
					<td>
						<?php echo _InputRadio("_2year_opt_use", array('Y','N'), $siteInfo['s_2year_opt_use'] ? $siteInfo['s_2year_opt_use'] : "N", '', array('발송','미발송'), '') ?>
					</td>
				</tr>

				<tr class="tr-2year-opt-use" style="display: none;">
					<th class="ess">수신동의 발송</th>
					<td>
						<a href="#none" class="c_btn h27 js_pg_popup" data-mode="2yearSend" data-width="800" data-height="400" data-page="_addons.popup.php">발송하기</a>
						<span class="fr_tx t_orange">(현재 수신동의 대상 회원 수 : <strong><?php echo (number_format($mr_cnt['cnt'])); ?></strong>명)</span>
						<div class="dash_line"></div>
						<?php echo _DescStr('발송하기을 클릭하여 수신동의에 해당되는 회원들에게 발송될 메일 양식 설정 및 메일을 발송을 할 수 있습니다.'); ?>
					</td>
				</tr>

				<tr class="tr-2year-opt-use" style="display: none;">
					<th class="ess">수신동의 발송메일 설정</th>
					<td>
						<a href="#none" class="c_btn h27 js_pg_popup" data-mode="2yearMail" data-width="1200" data-height="800" data-page="_addons.popup.php">발송메일 설정</a>
						<div class="tip_box">
							<?php echo _DescStr('발송메일 설정을 클릭하여 메일의 상단내용을 설정할 수 있습니다.'); ?>
							<?php echo _DescStr('수신동의 발송문자 설정의 경우 환경설정 > <em>SMS 정보설정</em> 에서 설정 가능합니다.'); ?>
						</div>
					</td>
				</tr>

				<tr>
					<td colspan="2">
						<div class="tip_box">
							<?=_DescStr("정보통신망법 제50조제8항 및 동법 시행령 제62조의3은 최초 동의한 날로부터 매2년마다 하도록 규정하고 있습니다. 이에 따라 수신동의 받은 날부터 매 2년 마다 수신동의 여부를 재확인 해야 합니다.")?>
							<?=_DescStr("개정법 시행 이전(2014년 11월 29일 이전)에 수신동의를 받은 자는 2016년 11월 28일까지 수신 동의 여부를 확인하여야 합니다.")?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php  echo _submitBTNsub(); ?>
</form>
<script>
	$(document).ready(_2yearOptInit);
	$(document).on('click','form#frm2yearOpt01 input[name="_2year_opt_use"]',_2yearOptInit);

	function _2yearOptInit(){
		var chk = $('form#frm2yearOpt01').find('input[name="_2year_opt_use"]:checked').val();
		if( chk == 'Y'){
			$('.tr-2year-opt-use').show();
		}else{
			$('.tr-2year-opt-use').hide();
		}
	}
	$(document).on('click', '.js_pg_popup', function(e) {
		e.preventDefault();
		var _mode = $(this).data('mode');
		var _page = $(this).data('page');
		var defaultPopWidth = 1120; // 팝업 기본 넓이값
		var defaultPopHeight = 540; // 팝업 기본 높이값
		var popWidth = $(this).data('width')*1;
		var popHeight = $(this).data('height')*1;

		if( popWidth == '' || popWidth == undefined){ $popWidth = defaultPopWidth; }
		if( popHeight == '' || popHeight == undefined){ $popHeight = defaultPopHeight; }

		window.open(_page+'?_mode='+_mode, _mode, 'width='+popWidth+',height='+popHeight+',top=100,scrollbars=yes');
	});
</script>


