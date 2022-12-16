
		<!-- 등록하기 버튼 클릭시 if_open 클래스 추가 -->
		<div class="c_view_board if_open">

			<!-- 후기/문의 공통 -->
			<div class="board_title">
				<span class="guide_txt">게시판 성격과 다른내용의 글을 등록하실 경우 임의로 삭제처리 될 수 있습니다.</span>
				<div class="c_btnbox">
					<ul>
						<li><a href="#none" onclick="qna_write_form_view(); return false;" class="c_btn h30 black">등록하기</a></li>
						<li><a href="/?pn=service.qna.list" class="c_btn h30 dark line">전체보기</a></li>
					</ul>
				</div>
			</div>

			<!-- 등록폼 / 등록하기 버튼 클릭시 노출 -->
			<div class="c_view_form" id="ID_qna_form" style="display:none;">
				<form method="post" name="qna_frm" id="qna_frm" enctype="multipart/form-data" target="common_frame_qna" action="<?php echo OD_PROGRAM_URL; ?>/product.qna.pro.php">
				<input type=hidden name='talk_type' value='qna'>
				<input type="hidden" name="_mode" value="add"/>
				<input type="hidden" name="pcode" value="<?=$pcode?>"/>
					<table>
						<colgroup>
							<col width="84"><col width="*">
						</colgroup>
						<tbody>
							<?php /*********
							<tr>
								<th><span class="opt">작성자</span></th>
								<td>
									<div class="value">
										<input type="text" name="" class="input_design" placeholder="이름" style="width:130px;">
										<span class="opt">비밀번호(4자이상)</span>
										<input type="text" name="" class="input_design" placeholder="비밀번호" style="width:160px;">
										<label><input type="checkbox" name="">비밀글로 등록</label>
									</div>
								</td>
							</tr>
							**************/?>
							<tr>
								<th><span class="opt">제목</span></th>
								<td>
									<?php if(is_login()){ ?>
										<input type="text" name="_title" id="qna_title" class="input_design" placeholder="제목을 입력해주세요.">
									<?php }else{ ?>
										<input type="text" name="" onclick="login_alert('<?php echo urlencode($_rurl); ?>'); return false;" class="input_design" value="로그인 후 입력가능 합니다." placeholder="로그인 후 입력가능 합니다." readonly>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<th><span class="opt">내용</span></th>
								<td>
									<?php if(is_login()){ ?>
										<textarea name="_content" id="qna_content" cols="" rows="" class="textarea_design" placeholder="내용을 입력해주세요."></textarea>
									<?php }else{ ?>
										<textarea name="" onclick="login_alert('<?php echo urlencode($_rurl); ?>'); return false;"  cols="" rows="" class="textarea_design" placeholder="로그인 후 입력가능합니다." readonly>로그인 후 입력가능합니다.</textarea>
									<?php } ?>
								</td>
							</tr>
						</tbody> 
					</table>
				
					<div class="c_btnbox">
						<ul>
							<li><a href="#none" onclick="qna_add(); return false;" class="c_btn h40 black bold">등록하기</a></li>
							<!-- 등록취소 버튼 클릭시 등록폼 닫힘 -->
							<li><a href="#none" onclick="qna_write_form_view(); return false;" class="c_btn h40 black line">등록취소</a></li>
						</ul>
					</div>
				</form>
			</div>


			<!-- 리스트 -->
			<div class="c_view_list" id="ID_qna_list">
				<?php
					// 내용추출
					//$pcode= $pcode;
					$_mode = 'view';
					$talk_type = 'qna';
					$listpg = 1;
					include OD_PROGRAM_ROOT.'/product.qna.pro.php';
				?>
			</div>

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
	$('#ID_qna_form').toggle();
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
	var _visible = $("#"+id).hasClass('if_open');


	$(".qna_box_area").removeClass("if_open");
	// 열려있는걸 다시 클릭했을때는 닫기만 처리한다.
	if(old_qna_id == id) {this.old_qna_id = 0;return;}
	$("#"+id).addClass("if_open");
	old_qna_id = id;


	// hit cnt 증가
	if(_visible === false) {
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
}

</script>