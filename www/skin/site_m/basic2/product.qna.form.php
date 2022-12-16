

	<!-- 2-2) 상품문의 폼 -->
	<div class="c_view_open if_view" id="js_qna_form" style="display:none; height:auto;">
		<div class="wrapping">

			<form method="post" name="qna_frm" id="qna_frm" enctype="multipart/form-data" target="common_frame_qna" action="<?php echo OD_PROGRAM_URL; ?>/product.qna.pro.php">
			<input type=hidden name="talk_type" value="qna">
			<input type="hidden" name="_mode" value="add"/>
			<input type="hidden" name="pcode" value="<?=$pcode?>"/>

				<div class="inner">
					<div class="box">
						<div class="tit_box">
							<div class="tit">상품문의 작성하기</div>
							<a href="#none" class="btn_close close" title="닫기"></a>
						</div>
						<div class="form_box">
							<ul>
								<?php /*
								<li>
									<div class="password">
										<input type="password" name="" value="" class="design" placeholder="비밀번호(4자 이상)">
										<label><input type="checkbox" name="">비밀글 등록</label>
									</div>
								</li>
								*/?>
								<li><div class="add">게시판 성격과 다른 내용의 글을 등록할 경우 임의로 삭제될 수 있습니다.</div></li>
								<li>
									<?php if(is_login()){ ?>
										<input type="text" name="_title" id="qna_title" class="design" placeholder="문의제목을 입력하세요.">
									<?php }else{ ?>
										<input type="text" name="" onclick="login_alert('<?php echo urlencode($_rurl); ?>'); return false;" class="design" value="로그인 후 입력가능 합니다." placeholder="로그인 후 입력가능 합니다." readonly>
									<?php } ?>
								</li>
								<li>
									<?php if(is_login()){ ?>
										<textarea name="_content" id="qna_content" cols="" rows="" class="design" placeholder="문의내용을 입력하세요."></textarea>
									<?php }else{ ?>
										<textarea name="" onclick="login_alert('<?php echo urlencode($_rurl); ?>'); return false;"  cols="" rows="" class="design" placeholder="로그인 후 입력가능합니다." readonly>로그인 후 입력가능합니다.</textarea>
									<?php } ?>
								</li>
							</ul>
						</div>
						<div class="c_btnbox">
							<ul>
								<li><a href="#none" onclick="qna_add(); return false;" class="c_btn h35 light">상품문의 등록</a></li>
							</ul>
						</div>
					</div>
				</div>

			</form>

		</div>
	</div>
	<!-- / 보기/폼 열기 -->




	<!-- 2-1) 상품문의 보기 -->
	<div class="c_view_open if_view" id="js_qna_view" style="display:none; height:auto;">
		<div class="wrapping">
			<div class="inner">
				<div class="box">
					<div class="tit_box">
						<div class="tit">상품문의 상세보기</div>
						<a href="#none" class="btn_close close" title="닫기"></a>
					</div>
					<div class="content_area"></div>
				</div>
			</div>
		</div>
	</div>


	
	<!-- 리스트 -->
	<div class="c_view_list if_qna" id="ID_qna_list">
		<?php
			// 내용추출
			//$pcode= $pcode;
			$_mode = 'view';
			$talk_type = 'qna';
			$listpg = 1;
			include OD_PROGRAM_ROOT.'/product.qna.pro.php';
		?>
	</div>




<script>

function iframe_init_qna(_type)
{
	if(_type == true){
		$('body').after('<iframe name="common_frame_qna" id="common_frame_qna" width="150" height="150" frameborder="0" style="display:none;"></iframe>')
	}else{
		$('#common_frame_qna').remove();
	}
}

// 상품평 쓰기 폼 노출
function qna_write_form_view() {
	$('#js_qna_form').lightbox_me({
		centered: true, closeEsc: false, overlaySpeed: 0, lightboxSpeed: 0,
		overlayCSS:{background:'#000', opacity: 0.7},
		onLoad: function() {},
		onClose: function(){}
	});
}



// 상품평 쓰기
function qna_add() {
<?PHP
	if( !is_login() ) echo 'login_alert("'. urlencode($_rurl) .'"); return false;';
?>
	

	if(!confirm('상품문의를 등록하시겠습니까?')) return false;

	if($('#qna_frm').valid()){
		$('#qna_frm').submit();
	}

}


$(document).ready(function(){ 
	$("#qna_frm").validate({
		rules: {
			_title: { required: true },
			_content: { required : true }
		},
		messages: {
			_title: { required: '제목을 입력하세요.' },
			_content: { required: '내용을 입력하세요.' }
		},
		submitHandler : function(form) {
			iframe_init_qna(true);
			form.submit(); setTimeout(function() { form.reset(); },100);
		}
	});
});


// 갯수 추출
function qna_get_cnt() {
	$.ajax({
		url: "<?php echo OD_PROGRAM_URL; ?>/product.qna.pro.php",
		cache: false,
		type: "POST",
		data: "_mode=getcnt&pcode=<?=$pcode?>",
		success: function(data){
			$(".qna_cnt").html(data);
		}
	});
}

// 리뷰 삭제
function qna_del(uid) {
<?PHP
	if( !is_login() ) {
	echo 'alert("먼저 로그인 하세요"); location.href = "/?pn=member.login.form&_rurl='.urlencode("/?".$_SERVER[QUERY_STRING]).'"; ';
	}
	else {
?>
	if(confirm("정말 삭제하시겠습니까?")) {
		$.ajax({
			url: "<?php echo OD_PROGRAM_URL; ?>/product.qna.pro.php",
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
					qna_view();
				}
			}
		});
	}
<?PHP
	}
?>
}
// 리뷰 보기
function qna_view(listpg) {
	$('#js_qna_form').trigger('close');
	$('#js_qna_view').trigger('close');

	if(listpg == undefined) listpg = 1;
	$.ajax({
		url: "<?php echo OD_PROGRAM_URL; ?>/product.qna.pro.php",
		cache: false,
		type: "POST",
		data: "_mode=view&talk_type=qna&pcode=<?=$pcode?>&listpg="+listpg,
		success: function(data){
			$("#ID_qna_list").html(data);
		}
	});
	qna_get_cnt();
}

var old_qna_id;
function qna_show(id) {
	if($("#"+id).length < 1) return false; 
	// hit cnt 처리를 위한 변수 
	var _uid = id.replace('view_','');


	// 상품후기 불러오기
	var _html = $("#"+id).find('.popup_html').html();
	$('#js_qna_view .content_area').html(_html)
	$('#js_qna_view').lightbox_me({
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