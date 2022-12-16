
		<!-- 등록하기 버튼 클릭시 if_open 클래스 추가 -->
		<div class="c_view_board if_open">

			<!-- 후기/문의 공통 -->
			<div class="board_title">
				<span class="guide_txt">게시판 성격과 다른내용의 글을 등록하실 경우 임의로 삭제처리 될 수 있습니다.</span>
				<div class="c_btnbox">
					<ul>
						<li><a href="#none" onclick="eval_write_form_view(); return false;" class="c_btn h30 black">등록하기</a></li>
						<li><a href="/?pn=service.eval.list" class="c_btn h30 dark line">전체보기</a></li>
					</ul>
				</div>
			</div>

			<!-- 등록폼 / 등록하기 버튼 클릭시 노출 -->
			<div class="c_view_form" id="ID_eval_form" style="display:none;">
				<form method="post" name="eval_frm" id="eval_frm" enctype="multipart/form-data" target="common_frame_eval" action="<?php echo OD_PROGRAM_URL; ?>/product.eval.pro.php">
				<input type=hidden name='talk_type' value='eval'>
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
                                        <?php if($trigger_eval_msg <> ''){ // SSJ : 상품후기 작성제한 기능강화 : 2020-08-13 : 상품후기 작성전 관리자 설정에 따른 안내문구 노출 ?>
                                            <input type="text" name="" onclick="alert('<?php echo $trigger_eval_msg; ?>'); return false;" class="input_design" value="<?php echo $trigger_eval_msg; ?>" placeholder="<?php echo $trigger_eval_msg; ?>" readonly>
                                        <?php }else{ ?>
                                            <input type="text" name="_title" id="eval_title" class="input_design" placeholder="제목을 입력해주세요.">
                                        <?php } ?>
									<?php }else{ ?>
										<input type="text" name="" onclick="login_alert('<?php echo urlencode($_rurl); ?>'); return false;" class="input_design" value="로그인 후 입력가능 합니다." placeholder="로그인 후 입력가능 합니다." readonly>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<th><span class="opt">평가</span></th>
								<td>
									<div class="mark_box">
										<label><input type="radio" name="_eval_point" value="100" checked="checked"><span class="mark"><span class="star" style="width:100%"></span></span></label>
										<label><input type="radio" name="_eval_point" value="80"><span class="mark"><span class="star" style="width:80%"></span></span></label>
										<label><input type="radio" name="_eval_point" value="60"><span class="mark"><span class="star" style="width:60%"></span></span></label>
										<label><input type="radio" name="_eval_point" value="40"><span class="mark"><span class="star" style="width:40%"></span></span></label>
										<label><input type="radio" name="_eval_point" value="20"><span class="mark"><span class="star" style="width:20%"></span></span></label>
									</div>
								</td>
							</tr>
							<tr>
								<th><span class="opt">내용</span></th>
								<td>
									<?php if(is_login()){ ?>
                                        <?php if($trigger_eval_msg <> ''){ // SSJ : 상품후기 작성제한 기능강화 : 2020-08-13 : 상품후기 작성전 관리자 설정에 따른 안내문구 노출 ?>
                                            <textarea name="" onclick="alert('<?php echo $trigger_eval_msg; ?>'); return false;"  cols="" rows="" class="textarea_design" placeholder="<?php echo $trigger_eval_msg; ?>" readonly><?php echo $trigger_eval_msg; ?></textarea>
                                        <?php }else{ ?>
                                            <textarea name="_content" id="eval_content" cols="" rows="" class="textarea_design" placeholder="내용을 입력해주세요."></textarea>
                                        <?php } ?>
									<?php }else{ ?>
										<textarea name="" onclick="login_alert('<?php echo urlencode($_rurl); ?>'); return false;"  cols="" rows="" class="textarea_design" placeholder="로그인 후 입력가능합니다." readonly>로그인 후 입력가능합니다.</textarea>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<th><span class="opt">사진첨부</span></th>
								<td>
									<!-- 사진첨부 -->
									<div class="form_file">
										<div class="input_file_box">
											<input type="text" id="fakeFileTxt" class="fakeFileTxt" readonly="readonly" disabled="" placeholder="사진 첨부는 600kb이하의 이미지 JPG,JPEG,GIF,PNG만 등록가능합니다.">
											<div class="fileDiv">
												<?php if(is_login()){ ?>
                                                    <?php if($trigger_eval_msg <> ''){ // SSJ : 상품후기 작성제한 기능강화 : 2020-08-13 : 상품후기 작성전 관리자 설정에 따른 안내문구 노출 ?>
                                                        <input type="button" class="buttonImg" value="파일찾기">
													    <input type="text" class="realFile" name="_img" onclick="alert('<?php echo $trigger_eval_msg; ?>'); return false;">
                                                    <?php }else{ ?>
                                                        <input type="button" class="buttonImg" value="파일찾기">
													    <input type="file" class="realFile" name="_img" onchange="javascript:document.getElementById('fakeFileTxt').value = this.value">
                                                    <?php } ?>
												<?php }else{ ?>
													<input type="button" class="buttonImg" value="파일찾기">
													<input type="text" class="realFile" name="_img" onclick="login_alert('<?php echo urlencode($_rurl); ?>'); return false;">
												<?php } ?>
											</div>
										</div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>

					<div class="c_btnbox">
						<ul>
							<li><a href="#none" onclick="eval_add(); return false;" class="c_btn h40 black bold">등록하기</a></li>
							<!-- 등록취소 버튼 클릭시 등록폼 닫힘 -->
							<li><a href="#none" onclick="eval_write_form_view(); return false;" class="c_btn h40 black line">등록취소</a></li>
						</ul>
					</div>
				</form>
			</div>


			<!-- 리스트 -->
			<div class="c_view_list" id="ID_eval_list">
				<?php
					// 내용추출
					//$pcode= $pcode;
					$_mode = 'view';
					$talk_type = 'eval';
					$listpg = 1;
					include OD_PROGRAM_ROOT.'/product.eval.pro.php';
				?>
			</div>

		</div>


<script>
function iframe_init(_type)
{
	if(_type == true){
		$('body').after('<iframe name="common_frame_eval" id="common_frame_eval" width="150" height="150" frameborder="0" style="display:none;"></iframe>')
	}else{
		$('#common_frame_eval').remove();
	}
}

// 상품평 쓰기 폼 노출
function eval_write_form_view() {
	$('#ID_eval_form').toggle();
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
	var _visible = $("#"+id).hasClass('if_open');


	$(".eval_box_area").removeClass("if_open");
	// 열려있는걸 다시 클릭했을때는 닫기만 처리한다.
	if(old_eval_id == id) {this.old_eval_id = 0;return;}
	$("#"+id).addClass("if_open");
	old_eval_id = id;


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