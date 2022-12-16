<?php
/*
	accesskey {
		s: 저장
		l: 리스트
	}
*/
$app_current_link = '_popup.list.php';
include_once('wrap.header.php');


// 변수 설정
if($_mode == 'modify'){
	$r = _MQ(" select * from smart_popup where p_uid = '{$_uid}' ");
}else{
	$_mode = 'add';
}

?>
<form name="frm" action="_popup.pro.php" method="post" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>팝업타입</th>
					<td>
						<?php echo _InputRadio('p_mode', array('I', 'E'), ($r['p_mode']?$r['p_mode']:'I'), ' class="js_mode"', array('이미지', '에디터'), ''); ?>
					</td>
				</tr>
				<tr>
					<th>팝업노출</th>
					<td>
						<?php echo _InputRadio('p_view', array('Y', 'N'), ($r['p_view']?$r['p_view']:'N'), '', array('노출', '비노출'), ''); ?>
					</td>
				</tr>
				<tr>
					<th>기한설정</th>
					<td>
						<?php echo _InputRadio('p_none_limit', array('Y', 'N'), ($r['p_none_limit']?$r['p_none_limit']:'N'), ' class="js_banner_trim_type"', array('무기한', '기간지정'), ''); ?>
						<div class="js_change_trim">
							<div class="dash_line"><!-- 점선라인 --></div>
							<input type="text" name="p_sdate" class="design js_datepic js_banner_trim_use" value="<?php echo ($r['p_none_limit'] <> 'Y'?$r['p_sdate']:''); ?>" style="width:85px" <?php echo  ($r['p_none_limit'] == 'Y'?' disabled ':null); ?>>
							<span class="fr_tx">-</span>
							<input type="text" name="p_edate" class="design js_datepic js_banner_trim_use" value="<?php echo ($r['p_none_limit'] <> 'Y'?$r['p_edate']:''); ?>" style="width:85px" <?php echo  ($r['p_none_limit'] == 'Y'?' disabled ':null); ?>>
						</div>
						<script type="text/javascript">
							$(document).on('change', '.js_banner_trim_type', BannerTrim);
							$(document).ready(BannerTrim);
							function BannerTrim() {
								var _type = $('.js_banner_trim_type:checked').val();
								if(_type == 'N') $('.js_banner_trim_use').removeAttr('disabled');
								else $('.js_banner_trim_use').attr('disabled',true);
							}
						</script>
					</td>
				</tr>
				<tr class="js_images_mode">
					<th>팝업이미지</th>
					<td>
						<?php echo _PhotoForm('..'.IMG_DIR_POPUP, 'p_img', $r['p_img'], 'style="width:250px"'); ?>
					</td>
				</tr>
				<tr>
					<th>타이틀</th>
					<td>
						<input type="text" name="p_title" class="design" value="<?php echo $r['p_title']; ?>" style="width:400px">
					</td>
				</tr>
				<tr class="js_edit_mode">
					<th>팝업내용</th>
					<td>
						<textarea name="p_content" class="design SEditor" style="width:100%;height:300px;" cols="30" rows="10"><?php echo $r['p_content']; ?></textarea>
					</td>
				</tr>
				<tr class="js_edit_mode">
					<th>팝업 배경색</th>
					<td>
						<input type="text" name="p_bgcolor" class="design js_colorpic" value="<?php echo strtoupper($r['p_bgcolor']?$r['p_bgcolor']:'#ffffff'); ?>" style="width:70px">
					</td>
				</tr>
				<tr class="js_edit_mode">
					<th>팝업크기</th>
					<td>
						<span class="fr_tx">가로</span><input type="text" name="p_width" class="input_text design number_style" style="width:50px" value="<?php echo ((int)$r['p_width'] >= 350?$r['p_width']:350); ?>">
						<div class="bar"></div>
						<span class="fr_tx">세로</span><input type="text" name="p_height" class="input_text design number_style" style="width:50px" value="<?php echo ((int)$r['p_height'] >= 250?$r['p_height']:250); ?>">
						<?php echo _DescStr('에디터형 팝업의 기본 크기는 350px x 250px입니다.'); ?>
					</td>
				</tr>
				<tr class="js_images_mode">
					<th>링크타켓</th>
					<td>
						<?php echo _InputRadio('p_target', array('_self', '_blank'), ($r['p_target']?$r['p_target']:'_self'), '', array('같은창', '새창'), ''); ?>
					</td>
				</tr>
				<tr class="js_images_mode">
					<th>링크주소</th>
					<td>
						<input type="text" name="p_link" class="design js_app_link" value="<?php echo $r['p_link']; ?>" style="width:400px;">
						<a href="#none" onclick="productWin(); return false;" class="c_btn h27 line bold">상품연결</a>
					</td>
				</tr>
				<tr>
					<th>노출순위</th>
					<td>
						<input type="text" name="p_idx" class="design t_center" value="<?php echo ($r['p_idx']?$r['p_idx']:0); ?>" style="width:50px">
						<?php echo _DescStr('노출순위가 높을수록 우선 노출됩니다.'); ?>
					</td>
				</tr>
				<tr>
					<th>노출위치</th>
					<td>
						<?php echo _InputRadio('p_type', array('A', 'P', 'M'), ($r['p_type']?$r['p_type']:'A'), ' class="p_type" ', array('전체노출', 'PC 노출', 'Mobile 노출'), ''); ?>
					</td>
				</tr>
				<tr class="p_type_view p_type_view_P">
					<th>PC 노출위치</th>
					<td>
						<span class="fr_tx">위쪽으로부터</span><input type="text" name="p_top" class="input_text design number_style" style="width:50px" value="<?php echo $r['p_top']; ?>"><span class="fr_tx">px, </span>
						<span class="fr_tx">왼쪽으로부터</span><input type="text" name="p_left" class="input_text design number_style" style="width:50px" value="<?php echo $r['p_left']; ?>"><span class="fr_tx">px</span>
					</td>
				</tr>
				<tr class="p_type_view p_type_view_M">
					<th>MOBILE 노출위치</th>
					<td>
						<span class="fr_tx">위쪽으로부터</span><input type="text" name="p_mtop" class="input_text design number_style" style="width:50px" value="<?php echo $r['p_mtop']; ?>"><span class="fr_tx">px</span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<?php echo _submitBTN($app_current_link); ?>
</form>



<script type="text/javascript">
	function productWin() {
		var _pcode = $('input[name=p_link]').val();
		_pcode = _pcode.split('/?pn=product.view&pcode=');
		_pcode = (_pcode[1]?_pcode[1]:_pcode[0]);
		window.open('_banner.product_link.php?relation_prop_code='+_pcode, 'relation', 'width=1120, height=800, scrollbars=yes'); // 배너의 상품연결과 동일한 파일 사용
	}

	$(document).ready(function() {
		// -  validate ---
		$('form[name=frm]').validate({
			ignore: 'input[type=text]:hidden,input[type=button]',
			rules: {
				p_idx: { required: true}	//페이지 아이디 체크
				,p_sdate: { required: function(){ if( $('input[name=p_none_limit]:checked').val()=='N' ){ return true; }else{ return false; } } }	//노출여부
				,p_edate: { required: function(){ if( $('input[name=p_none_limit]:checked').val()=='N' ){ return true; }else{ return false; } } }	//노출순위
			},
			messages: {
				p_idx: { required: '순위를 선택해주시기 바랍니다.'}	//페이지 아이디 체크
				,p_sdate: { required: '시작일을 입력해주시기 바랍니다.'}	//노출여부
				,p_edate: { required: '종료일을 입력해주시기 바랍니다.'}	//노출순위
			},
			submitHandler : function(form) {
				// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
				form.submit();
			}
		});
		// - validate ---
	});


	function PopupView() {
		var _mode = $('.p_type:checked').val();
		$('.p_type_view').hide();
		if(_mode == 'A') $('.p_type_view').show();
		else $('.p_type_view_'+_mode).show();
	}
	$(document).ready(PopupView);
	$(document).on('click', '.p_type', function() { PopupView(); });

	function PopupMode() {
		var _mode = $('.js_mode:checked').val();
		$('.js_images_mode').hide();
		$('.js_edit_mode').hide();
		if(_mode == 'I') {
			$('.js_images_mode').show();
		}
		else {
			$('.js_edit_mode').show();

			// 에디터 사이즈 조정
			if(oEditors.length > 0) {
				var id = $('.SEditor').attr('id');
				oEditors.getById[id].exec('RESIZE_EDITING_AREA_BY',[true]);
			}
		}
	}
	$(document).ready(PopupMode);
	$(document).on('click', '.js_mode', PopupMode);
</script>
<?php include_once('wrap.footer.php'); ?>