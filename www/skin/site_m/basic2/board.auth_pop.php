<?php  	
	if( in_array($_mode,array('view','modify','delete')) == true) { 
		ob_start();
?>
<!-- 팝업 / 팝업 사이즈는 컨텐츠 마다 별도 -->
<form name="bbsAuth" action="<?php echo OD_PROGRAM_URL.'/board.auth.php'; ?>" target="common_frame">
	<input type="hidden" name="_mode" value="<?php echo $_mode ?>">
	<input type="hidden" name="_uid" value="<?php echo $_uid?>">
	
	<div class="wrapping" style="">
		<div class="inner">
			<div class="box">
				<div class="tit_box">
					<div class="tit">
						<?php if( $_mode == 'view') { // 비밀글 보기일경우   ?>
						비밀글 열람하기
						<?php }else if($_mode == 'modify'){ // 비회원 글 수정일경우 ?>
						게시물 수정
						<?php }else if($_mode == 'delete') { // 게시물 삭제하기 ?>
						게시물 삭제
						<?php } ?>						
					</div>
					<a href="#none" onclick="return false;" class="btn_close close" title="닫기"></a>
				</div>
				<div class="conts_box c_order">

					<div class="c_group_tit">
						<?php if( $_mode == 'view') { // 비밀글 보기일경우   ?>
						<span class="tit">본 게시물을 열람하기 위해서는 비밀번호가 필요합니다.</span>
						<?php }else if($_mode == 'modify'){ // 비회원 글 수정일경우 ?>
						<span class="tit">본 게시물을 수정하기 위해서는 비밀번호가 필요합니다.</span>
						<?php }else if($_mode == 'delete') { // 게시물 삭제하기 ?>
						<span class="tit">본 게시물을 삭제하기 위해서는 비밀번호가 필요합니다.</span>
						<?php } ?>								
					</div>
					<div class="c_form">
						<table>
							<tbody>
								<tr>
									<th class="ess"><span class="tit ">비밀번호</span></th>
									<td>
										<div class="textarea_box"><input type="password" name="passwd" class="input_design" placeholder="비밀번호" autocomplete="new-password"></div>
									</td>
								</tr>
							</tbody> 
						</table>
					</div>
					

				</div>
				<div class="c_btnbox">
					<ul>
						<li><a href="#none" class="c_btn h40 dark secret_submit">확인</a></li>
						<li><a href="#none" onclick="return false;" class="c_btn h40 line close">닫기</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>


</form>

<?php
		$html = ob_get_contents();
		ob_end_clean(); 
		echo json_encode(array('rst'=>'success','html'=>$html)); exit;

	}else{ // 뷰일 시
?>

<?php // ajax 처리 {{{  ?>
<div class="c_pop js_secret_pop" style="display: none; width:80%;"></div>
<?php // ajax 처리 }}}  ?>

<script>
// -- 권항없는  클릭시
$(document).on('click', '.js_auth_fail', function(e) {
	alert("본 게시글에 대한 권한이 없습니다.");
	return false;
});
// -- 권한요청 ::  클릭시
$(document).on('click', '.js_open_auth_pop', function(e) {
	var _uid = $(this).attr('data-uid');
	var _mode = $(this).attr('data-mode');
	if( _uid == undefined || _uid == '' || _mode == undefined || _mode == ''){ alert("접근 권한이 없습니다."); return false; }

	$('.js_secret_pop').lightbox_me({
		centered: true, closeEsc: true,
		onLoad: function() { 
			var url = '<?php echo OD_PROGRAM_URL.'/board.auth_pop.php'; ?>';
			$.ajax({
				url: url, cache: false,dataType : 'json', type: "get", data: { _uid  : _uid , _mode : _mode }, success: function(data){
					
					if(data.rst == 'success'){
						$('.js_secret_pop').html(data.html);
					}else{
						$('.js_secret_pop').trigger('close');
				  	}
				},error:function(request,status,error){ console.log(request.responseText);}
			});
		},
		onClose: function(){
			$('form[name="bbsAuth"]').find('[name="_uid"]').val('');
		}
	});
	return false;
});

//  입력 시
$(document).on('click', '.secret_submit', function(e) {
	var chk = $('form[name="bbsAuth"]').find('[name="passwd"]').val();
	if( chk == undefined){ chk = ''; }
	if( chk.replace(/\s/gi,'') == ''){
		alert('비밀번호를 입력해 주세요.'); 
		$('form[name="bbsAuth"]').find('[name="passwd"]').focus();
		return false;
	}
	$('form[name="bbsAuth"]').submit();
});
</script>
<!-- <style>
	.js_secret_pop{ position: static; width: 460px; height: 400px;}
	.js_secret_pop .bg{ display:none;;}
	.js_secret_pop .pop_wrap{ position: static;}
</style>
 -->


<?php } ?>