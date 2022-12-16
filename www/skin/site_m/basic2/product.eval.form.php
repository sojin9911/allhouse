
	<!-- 1-2) 상품후기 폼 -->
	<div class="c_view_open if_view" id="js_eval_form" style="display:none; height:auto;">
		<div class="wrapping" style="height:auto;">

			<form method="post" name="eval_frm" id="eval_frm" enctype="multipart/form-data" target="common_frame_eval" action="<?php echo OD_PROGRAM_URL; ?>/product.eval.pro.php">
			<input type=hidden name='talk_type' value='eval'>
			<input type="hidden" name="_mode" value="add"/>
			<input type="hidden" name="pcode" value="<?=$pcode?>"/>
			<input type="hidden" name="_eval_point" id="eval_point" value="100"/>

				<div class="inner">
					<div class="box">
						<div class="tit_box">
							<div class="tit">상품후기 작성하기</div>
							<a href="#none" class="btn_close close" title="닫기"></a>
						</div>
						<div class="form_box">
							<ul>
								<li><div class="add">게시판 성격과 다른 내용의 글을 등록할 경우 임의로 삭제될 수 있습니다.</div></li>
								<li>
									<div class="mark_box if_score5 toggleScore"><!-- 클릭할때마다 if_score1~5 까지 클래스변경, 기존 모바일 별후기 참조 -->
										<div class="tit">평가점수</div>
										<ul>
											<li><a href="#none" onclick="return false;" data-score="20" class="click"></a></li>
											<li><a href="#none" onclick="return false;" data-score="40" class="click"></a></li>
											<li><a href="#none" onclick="return false;" data-score="60" class="click"></a></li>
											<li><a href="#none" onclick="return false;" data-score="80" class="click"></a></li>
											<li><a href="#none" onclick="return false;" data-score="100" class="click"></a></li>
										</ul>
										<script>
										$(document).ready(function(){
											$('.toggleScore a.click').on('click',function(){
												$('.toggleScore').attr('class','mark_box toggleScore');
												$('.toggleScore').addClass('if_score'+($(this).data('score')/20));
												$('#eval_point').val($(this).data('score'));
											});
										});
										</script>
										<span class="mark"><span class="star"></span></span>
										<div class="tip">별을 클릭하면 원하는 점수를 선택하실 수 있습니다.</div>
									</div>
								</li>
								<li>
									<?php if(is_login()){ ?>
                                        <?php if($trigger_eval_msg <> ''){ // SSJ : 상품후기 작성제한 기능강화 : 2020-08-13 : 상품후기 작성전 관리자 설정에 따른 안내문구 노출 ?>
                                            <input type="text" name="" onclick="alert('<?php echo $trigger_eval_msg; ?>'); return false;" class="design" value="<?php echo $trigger_eval_msg; ?>" placeholder="<?php echo $trigger_eval_msg; ?>" readonly>
                                        <?php }else{ ?>
                                            <input type="text" name="_title" id="eval_title" class="design" placeholder="제목을 입력해주세요.">
                                        <?php } ?>
									<?php }else{ ?>
										<input type="text" name="" onclick="login_alert('<?php echo urlencode($_rurl); ?>'); return false;" class="design" value="로그인 후 입력가능 합니다." placeholder="로그인 후 입력가능 합니다." readonly>
									<?php } ?>
								</li>
								<li class="js_image_preview_box">
									<!-- 기존 모바일 이미지 첨부 참조바람 -->
									<label class="btn_photo_box">
										<?php if(is_login()){ ?>
                                            <?php if($trigger_eval_msg <> ''){ // SSJ : 상품후기 작성제한 기능강화 : 2020-08-13 : 상품후기 작성전 관리자 설정에 따른 안내문구 노출 ?>
                                                <input type="file" name="" onclick="alert('<?php echo $trigger_eval_msg; ?>'); return false;" accept="image/*"  class="input_photo js_image_preview_input_file" readonly />
                                                <span class="upper_txt fakeFileTxt"><?php echo $trigger_eval_msg; ?></span>
                                            <?php }else{ ?>
                                                <input type="file" name="_img" accept="image/*"  class="input_photo js_image_preview_input_file"  />
                                                <span class="upper_txt fakeFileTxt">사진을 첨부하세요.</span>
                                            <?php } ?>
										<?php }else{ ?>
                                            <input type="file" name="_img" accept="image/*"  class="input_photo js_image_preview_input_file"  onclick="login_alert('<?php echo urlencode($_rurl); ?>'); return false;" />
                                            <span class="upper_txt fakeFileTxt">로그인 후 첨부 가능합니다.</span>
										<?php } ?>
									</label>
									<div class="tip">사진업로드 시, 카메라로 직접촬영은 기능이 제한될 수 있습니다. 사진을 찍은 후에 사진보관함을 통해 업로드를 해주십시오. 또한 모바일웹의 특성 상, 직접 촬영한 이미지는 방향이 자동으로 변환되어 업로드 될수도 있습니다.</div>
									<!-- 업로드 이미지 미리보기 -->
									<div class="photo_box js_image_preview" style="display:none;"><img id="img_preview" class="img_preview"></div>
									<script type="text/javascript">
										// 첨부이미지 미리보기
										$(document).on('change', '.js_image_preview_input_file', function(e) {

											var su = $(this);
											var fval = su.val();
											var ext = su.val().split('.').pop().toLowerCase();
											if($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
												su.val('');
												alert('이미지만 첨부가능 합니다. (gif, png, jpg, jpeg)');
											}
											else {
												var file = su.prop('files')[0];
												var blobURL = window.URL.createObjectURL(file);
												su.closest('.js_image_preview_box').find('.fakeFileTxt').text(fval);
												su.closest('.js_image_preview_box').find('.js_image_preview').find('img').attr('src', blobURL);
												su.closest('.js_image_preview_box').find('.js_image_preview').slideDown();
											}
										});
									</script>
								</li>
								<li>
									<?php if(is_login()){ ?>
                                        <?php if($trigger_eval_msg <> ''){ // SSJ : 상품후기 작성제한 기능강화 : 2020-08-13 : 상품후기 작성전 관리자 설정에 따른 안내문구 노출 ?>
                                            <textarea name="" id="eval_content" onclick="alert('<?php echo $trigger_eval_msg; ?>'); return false;" cols="" rows="" class="design" placeholder="<?php echo $trigger_eval_msg; ?>"><?php echo $trigger_eval_msg; ?></textarea>
                                        <?php }else{ ?>
                                            <textarea name="_content" id="eval_content" cols="" rows="" class="design" placeholder="내용을 입력해주세요."></textarea>
                                        <?php } ?>
									<?php }else{ ?>
										<textarea name="" onclick="login_alert('<?php echo urlencode($_rurl); ?>'); return false;"  cols="" rows="" class="design" placeholder="로그인 후 입력가능합니다." readonly>로그인 후 입력가능합니다.</textarea>
									<?php } ?>
								</li>
							</ul>
						</div>
						<div class="c_btnbox">
							<ul>
								<li><a href="#none" onclick="eval_add(); return false;" class="c_btn h35 light">상품후기 등록</a></li>
							</ul>
						</div>
					</div>
				</div>

			</form>

		</div>
	</div>
	<!-- / 보기/폼 열기 -->



	<!-- 1-1) 상품후기 보기 -->
	<div class="c_view_open if_view" id="js_eval_view" style="display:none; height:auto;">
		<div class="wrapping">
			<div class="inner">
				<div class="box">
					<div class="tit_box">
						<div class="tit">상품후기 상세보기</div>
						<a href="#none" class="btn_close close" title="닫기"></a>
					</div>
					<div class="content_area"></div>
				</div>
			</div>
		</div>
	</div>




	<!-- 리스트 -->
	<div class="c_view_list"  id="ID_eval_list">
		<?php
			// 내용추출
			//$pcode= $pcode;
			$_mode = 'view';
			$talk_type = 'eval';
			$listpg = 1;
			include OD_PROGRAM_ROOT.'/product.eval.pro.php';
		?>
	</div>





<script>
function iframe_init(_type)
{
	if(_type == true){
		if( $('#common_frame_eval').length < 1) {
			$('body').after('<iframe name="common_frame_eval" id="common_frame_eval" width="150" height="150" frameborder="0" style="display:none;"></iframe>')
		}
	}else{
		$('#common_frame_eval').remove();
	}
}

// 상품후기 등록폼 열기
function eval_write_form_view(){
	$('#js_eval_form').lightbox_me({
		centered: true, closeEsc: false, overlaySpeed: 0, lightboxSpeed: 0,
		overlayCSS:{background:'#000', opacity: 0.7},
		onLoad: function() {},
		onClose: function(){}
	});
}




// 상품평 쓰기
function eval_add() {
<?PHP
	if( !is_login() ) echo 'login_alert("'. urlencode($_rurl) .'"); return false;';

    // SSJ : 상품후기 작성제한 기능강화 : 2020-08-13 : 상품후기 작성전 관리자 설정에 따른 안내문구 노출
    if( $trigger_eval_msg <> '' ) echo 'alert("'. $trigger_eval_msg .'"); return false;';
?>

	if(!confirm("상품후기를 등록하시겠습니까?")) return false;

	if($('#eval_frm').valid()){
		$('#eval_frm').submit();
	}

}


$(document).ready(function(){
	$("#eval_frm").validate({
		rules: {
			_eval_point: { required: true },
			_title: { required: true },
			_content: { required : true }
		},
		messages: {
			_eval_point: { required: "별점을 선택하세요." },
			_title: { required: "제목을 입력하세요." },
			_content: { required: "내용을 입력하세요." }
		},
		submitHandler : function(form) {
			iframe_init(true);
			form.submit();
		}
	});
});

// 갯수 추출
function eval_get_cnt() {
	$.ajax({
		url: "<?php echo OD_PROGRAM_URL; ?>/product.eval.pro.php",
		cache: false,
		type: "POST",
		data: "_mode=getcnt&pcode=<?=$pcode?>",
		success: function(data){
			$(".eval_cnt").html(data);
		}
	});
}

// 리뷰 삭제
function eval_del(uid) {
<?PHP
	if( !is_login() ) {
	echo 'alert("먼저 로그인 하세요"); location.href = "/?pn=member.login.form&_rurl='.urlencode("/?".$_SERVER[QUERY_STRING]).'"; ';
	}
	else {
?>
	if(confirm("정말 삭제하시겠습니까?")) {
		$.ajax({
			url: "<?php echo OD_PROGRAM_URL; ?>/product.eval.pro.php",
			cache: false,
			type: "POST",
			data: "_mode=delete&uid=" + uid ,
			success: function(data){
				if( data == "no data" ) {
					alert('등록하신 글이 아닙니다.');
				}
				else if( data == "is reply" ) {
					alert('댓글이 있으므로 삭제가 불가합니다.');
				}
				else {
					alert('정상적으로 삭제하였습니다.');
					eval_view();
				}
			}
		});
	}
<?PHP
	}
?>
}
// 리뷰 보기
function eval_view(listpg) {
	$('#js_eval_form').trigger('close');
	$('#js_eval_view').trigger('close');

	if(listpg == undefined) listpg = 1;
	$.ajax({
		url: "<?php echo OD_PROGRAM_URL; ?>/product.eval.pro.php",
		cache: false,
		type: "POST",
		data: "_mode=view&talk_type=eval&pcode=<?=$pcode?>&listpg="+listpg,
		success: function(data){
			$("#ID_eval_list").html(data);
		}
	});
	eval_get_cnt();
}

var old_eval_id;
function eval_show(id) {
	if($("#"+id).length < 1) return false;
	// hit cnt 처리를 위한 변수
	var _uid = id.replace('view_','');


	// 상품후기 불러오기
	var _html = $("#"+id).find('.popup_html').html();
	$('#js_eval_view .content_area').html(_html)
	$('#js_eval_view').lightbox_me({
		centered: true, closeEsc: false, overlaySpeed: 0, lightboxSpeed: 0,
		overlayCSS:{background:'#000', opacity: 0.7},
		onLoad: function() {},
		onClose: function(){}
	});


	// hit cnt 증가
	var _smode = ($('#'+id).attr('data-hit') == 'false'?'update':'nocount');
	if(_smode == 'update') {
		$.ajax({
			data: {
				_mode: 'eval_hit',
				_smode: _smode,
				_uid: _uid
			},
			type: 'POST',
			cache: false,
			url: '<?php echo OD_PROGRAM_URL; ?>/_pro.php',
			success: function(data) {
				// 중복 hit차단
				$('#'+id).attr('data-hit', 'true');
			}
		});
	}
}

</script>