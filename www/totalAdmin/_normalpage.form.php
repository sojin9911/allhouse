<?php
	$app_current_link = '_normalpage.list.php';
	include_once("wrap.header.php");

	if( $_mode == "modify" ) {
		$que = "  select * from smart_normal_page where np_uid='". $_uid ."' ";
		$r = _MQ($que);
	}
	else {
		// 순위 지정
		$r = _MQ("  select ifnull(max(np_idx),0) as max_idx from smart_normal_page ");
		$r['np_idx'] = $r['max_idx'] + 10;
	}


?>



<form name="frm" method="post" action="_normalpage.pro.php" enctype="multipart/form-data" >
<input type="hidden" name="_mode" value='<?php echo $_mode; ?>'>
<input type="hidden" name="_uid" value='<?php echo $_uid; ?>'>
<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">

	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>기본 설정</strong></div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<?php if($_mode == "modify") { ?>
					<tr>
						<th>페이지 아이디</th>
						<td>
							<input type="hidden" name="_mail_checking" id="_mail_checking" value="Y">
							<input type="hidden" name="_id" value="<?php echo $r['np_id']; ?>" />
							<strong><?php echo $r['np_id']; ?></strong>
						</td>
						<th>페이지 URL</th>
						<td>
							<a href="<?php echo "/?pn=pages.view&type=pages&data={$r['np_id']}"; ?>" target="_blank">
								<?php echo $app_HTTP_URL."/?pn=pages.view&type=pages&data={$r['np_id']}"; ?>
							</a>
						</td>
					</tr>
				<?php } else { ?>
					<tr>
						<th>페이지 아이디</th>
						<td colspan="3">
							<input type="hidden" name="_mail_checking" id="_mail_checking" value="">
							<input type="text" name="_id" class="design" value="<?php echo $r['np_id']; ?>" onchange="id_change(); return false;" placeholder="" style="width:150px">
							<a href="#none" onclick="id_chk(); return false;" class="c_btn h27 black" title="페이지 아이디 확인" >페이지 아이디 확인</a>

							<div class="c_tip" id="__idchk_onedaynet">페이지에 지정되는 고유한 아이디를 입력하시기 바랍니다. 페이지아이디는 영숫자만 허용합니다.</div>
						</td>
					</tr>
				<?php } ?>
				<tr>
					<th>노출여부</th>
					<td>
						<?php echo _InputRadio( '_view', array('Y','N'), ($r['np_view']?$r['np_view']:'Y'), '', array('노출','숨김'), ''); ?>
					</td>
					<!-- // 2019-11-29 SSJ :: 노출 메뉴 추가 -->
					<th>노출메뉴</th>
					<td>
						<?php echo _InputRadio( '_menu', array('default','agree','only'), ($r['np_menu']?$r['np_menu']:'default'), '', array('회사소개','이용안내','단독메뉴')); ?>
						<!-- np_menu 칼럼 DB 추가여부 :: N이면 pro파일에서 DB 추가후 저장한다 -->
						<input type="hidden" name="is_colmn_menu" value="<?php echo ($r['np_menu']=="" ? "N" : "Y"); ?>">
					</td>
					<!-- // 2019-11-29 SSJ :: 노출 메뉴 추가 -->
				</tr>
				<tr>
					<th>페이지명</th>
					<td>
						<input type="text" name="_title" class="design" value="<?php echo $r['np_title']; ?>" placeholder="" style="width:500px">
					</td>
					<th>노출순위</th>
					<td>
						<input type="text" name="_idx" class="design number_style" style="width:40px" value="<?=$r[np_idx]?>" placeholder="노출순위"/>
						<?php echo _DescStr('낮은 순위가 먼저 나오며, 순위가 같을 경우 먼저 최근 등록한 순으로 나옵니다.'); ?>
					</td>
				</tr>
				<?php /*
				<tr style="display:none;">
					<td class="article">페이지 상단 이미지</td>
					<td class="conts"><?=_PhotoForm( "../../upfiles/normal" , "_header_img"  , $r['np_header_img'] )?></td>
				</tr>
				<tr style="display:none;">
					<td class="article">페이지 하단 이미지</td>
					<td class="conts"><?=_PhotoForm( "../../upfiles/normal" , "_footer_img"  , $r['np_footer_img'] )?></td>
				</tr>
				*/?>
			</tbody>
		</table>
	</div>


	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>페이지 설정</strong></div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">

		<!-- ● 내부탭 -->
		<div class="c_tab">
			<ul>
				<li class="hit"><a href="#none" class="btn tab_menu" data-idx="pc" data-trigger="N"><strong>상세내용(PC)</strong></a></li>
				<li><a href="#none" class="btn tab_menu" data-idx="mobile" data-trigger="Y"><strong>상세내용(MOBILE)</strong></a></li>
			</ul>
			<label class="design"><input type="checkbox" name="_use_content" value="Y" <?php echo ($r['np_use_content'] == 'Y' ? ' checked ' : null); ?>>PC/MOBILE 상세내용 함께 사용</label>
		</div>

		<table class="table_form">
			<tbody>
				<tr>
					<td>
						<div class="tab_conts" data-idx="pc">
							<textarea name="_content" class="input_text SEditor" style="width:100%;height:300px;" hname='상품설명'><?php echo stripslashes($r['np_content']); ?></textarea>
						</div>
						<div class="tab_conts" data-idx="mobile" style="display:none">
							<textarea name="_content_m" class="input_text SEditor" style="width:100%;height:300px;" hname='상품설명'><?php echo stripslashes($r['np_content_m']); ?></textarea>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<?php echo _submitBTN('_normalpage.list.php'); ?>

</form>


<script>
	// 아이디변경 체크
	function id_change() {

		$('#__idchk_onedaynet').addClass('black').addClass('blink').html('아이디 변경을 원하시면 아이디 확인 버튼을 눌러 인증하시기 바랍니다.');
		$('#_mail_checking').val('');
	}

	// 아이디 중복 체크
	function id_chk() {

		if(!$('input[name=_id]').val()) {

			alert($('input[name=_id]').val() + '페이지 아이디를 입력하세요');
			$('#_mail_checking').val('');

			return;
		}

		$.ajax({
			url: '_normalpage.pro.php',
			cache: false,
			type: 'POST',
			data: '_mode=idchk&_id=' + $('input[name=_id]').val() ,
			success: function(data){
				if(data == 'no') {

					$('#__idchk_onedaynet').addClass('black').addClass('blink').html('중복되는 아이디로 적용이 불가합니다.');
					$('#_mail_checking').val('');
				}
				else if(data == 'en') {
					$('#__idchk_onedaynet').addClass('black').addClass('blink').html('페이지 아이디 아이디는 영문 및 숫자만 입력 가능합니다');
					$('#_mail_checking').val('');
				}
				else if(data == 'yes') {

					$('#__idchk_onedaynet').removeClass('black').removeClass('blink').html('중복되지 않는 아이디로 적용이 가능합니다.');
					$('#_mail_checking').val('Y');
				}
			}
		});
	}

	// 텝메뉴
	$(document).on('click', '.tab_menu', function() {
		$parent = $(this).closest('.data_form');
		var idx = $(this).data('idx');
		// 탭변경
		$parent.find('.tab_menu').closest('li').removeClass('hit');
		$parent.find('.tab_menu[data-idx='+ idx +']').closest('li').addClass('hit');
		// 입력항목변경
		$parent.find('.tab_conts').hide();
		$parent.find('.tab_conts[data-idx='+ idx +']').show();

		// 부모창이 display:none; 일때 높이 오류 수정
		var trigger_cont_editor = $(this).data('trigger')=='Y' ? true : false;
		if(trigger_cont_editor){
			$('.tab_conts[data-idx='+ idx +'] .SEditor').each(function(){
				var id = $(this).attr('id');
				if(oEditors.length > 0){
					oEditors.getById[id].exec('RESIZE_EDITING_AREA_BY',[true]);
				}
			});
			$(this).data('trigger','N');
		}
	});

	$(document).ready(function() {
		// -  validate ---
		$('form[name=frm]').validate({
			ignore: 'input[type=text]:hidden,input[type=button]',
			rules: {
				_id: { required: true, alphanumeric:true},//페이지 아이디
				_mail_checking: { required: true},//페이지 아이디 체크
				_view: { required: true},//노출여부
				_idx: { required: true},//노출순위
				_title: { required: true},//페이지명
				_content: { required: true}//페이지 내용
			},
			messages: {
				_id: { required: '페이지 아이디를 입력하시기 바랍니다.', alphanumeric : '페이지 아이디는 영숫자만 가능합니다.'},//페이지 아이디
				_mail_checking: { required: '페이지 아이디를 확인하시기 바랍니다.'},//페이지 아이디 체크
				_view: { required: '노출여부을 선택하시기 바랍니다.'},//노출여부
				_idx: { required: '노출순위를 입력하시기 바랍니다.'},//노출순위
				_title: { required: '페이지명을 입력하시기 바랍니다.'},//페이지명
				_content: { required: '페이지 내용을 입력하시기 바랍니다.'}//페이지 내용
			},
			submitHandler : function(form) {
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
				form.submit();
			}
		});
		// - validate ---
	});



function blink_text(){
    $('.blink').fadeOut().fadeIn();
}

setInterval(blink_text, 1000);
</script>

<?PHP
	include_once("wrap.footer.php");  //o_price_real
?>