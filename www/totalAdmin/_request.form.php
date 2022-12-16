<?PHP
	if(!$_GET['pass_menu']) $_GET['pass_menu'] = 'inquiry';
	$app_current_link = '_request.list.php?pass_menu='.$_GET['pass_menu'];
	include_once('wrap.header.php');

	if( $_mode == "modify" ) {
		$row = _MQ(" select * from smart_request where r_uid='{$_uid}' ");

		// -- 게시물 첨부파일을 불러온다.
		$getBoardFile = getFilesRes('smart_request',$_uid);
		$getBoardFileUser = getFilesRes('smart_request', $_uid.'_user'); // LDD: 2018-08-03 사용자
	}

	if( !$pass_menu ) {
		error_msg("메뉴를 선택해주시기 바랍니다.");
	}


?>

<script language='javascript' src='../../include/js/lib.validate.js'></script>


<form name="frm" method="post" ENCTYPE="multipart/form-data" action="_request.pro.php">
<input type=hidden name="_mode" value="<?php echo $_mode; ?>">
<input type=hidden name="_PVSC" value="<?php echo $_PVSC; ?>">
<input type=hidden name="_uid" value="<?php echo $_uid; ?>">
<input type=hidden name="pass_menu" value="<?php echo $pass_menu; ?>">
<input type=hidden name="_menu" value="<?php echo $pass_menu; ?>">



	<div class="data_form">

		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<?php if( in_array($row[r_menu] , array("inquiry")) ){?>
				<tr>
					<th>회원 아이디</th>
					<td><?php echo $row['r_inid']; ?></td>
				</tr>
				<?php } ?>
				<?php if( in_array($row[r_menu] , array("partner")) ) {?>
				<tr>
					<th class="ess">이름/상호명</th>
					<td>
						<input type="text" name="_comname" value="<?php echo stripslashes($row['r_comname']); ?>" class="design" required>
					</td>
				</tr>
				<!-- <tr>
					<th>문의자 연락처</th>
					<td>
					<input type="text" name="_tel" value="<?php echo stripslashes($row['r_tel']); ?>" class="design">
					</td>
				</tr> -->
				<tr>
					<th class="ess">연락처</th>
					<td>
					<input type="text" name="_hp" value="<?php echo stripslashes($row['r_hp']); ?>" class="design">
					</td>
				</tr>
				<tr>
					<th class="ess">이메일</th>
					<td>
					<input type="text" name="_email" value="<?php echo stripslashes($row['r_email']); ?>" class="design"></td>
				</tr>
				<?php } ?>
				<tr>
					<th class="ess">문의제목</th>
					<td><input type="text" name="_title" class="design" style="width:400px" value="<?php echo stripslashes(strip_tags($row['r_title'])); ?>" /></td>
				</tr>

				<tr>
					<th class="ess">문의내용</th>
					<td>
					<textarea name="_content" class="design"  style="width:90%;height:180px;"><?php echo stripslashes($row['r_content']); ?></textarea>
					</td>
				</tr>

				<?php
				// LDD: 2018-08-03 사용자 첨부가 있다면 노출
				if(count($getBoardFileUser) > 0) {
				?>
					<tr>
						<th>사용자 첨부파일</th>
						<td>
							<!-- ● 데이터 리스트 -->
							<table class="table_form">
								<colgroup>
									<col width="140"/><col width="*"/>
								</colgroup>
								<tbody>
									<?php foreach($getBoardFileUser as $k=>$v) { $file_num = $k+1; ?>
										<tr>
											<th>첨부파일 <span class="files_idx"><?php echo $file_num?></span></th>
											<td>
												<?php
												// LDD: 2018-08-03 첨부파일이 이미지인경우 미리보기 및 다운로드 버튼 제공
												if(is_image_file($v['f_oldname']) == true) {
												?>
													<div class="preview_thumb">
														<img src="<?php echo IMG_DIR_FILE.$v['f_realname']; ?>" class="js_thumb_img js_thumb_popup" data-img="<?php echo IMG_DIR_FILE.$v['f_realname']; ?>" alt="">
														<a href="<?php echo OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$v['f_uid']; ?>" class="c_btn h27" >다운로드</a>
													</div>
												<?php } else { ?>
													<div class="preview_thumb"><a href="<?php echo OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$v['f_uid']; ?>" class="btn_file" title="<?php echo addslashes($v['f_oldname']); ?>"><?php echo $v['f_oldname']; ?></a></div>
												<?php } ?>
											</td>
										</tr>
									<?php } ?>
								</tbody>
							</table>
						</td>
					</tr>
				<?php } ?>

				<tr>
					<th>파일첨부</th>
					<td>
						<!-- ● 데이터 리스트 -->
						<table class="table_form">
							<colgroup>
								<col width="140"/><col width="*"/>
							</colgroup>
							<tbody>
							<?php if( count($getBoardFile) > 0) { ?>
								<?php foreach( $getBoardFile as $k=>$v){  $idx = ($k+1);?>
								<tr class="tr-files" data-idx="<?php echo $idx ?>" data-mode="modify">
									<th>첨부파일 <span class="files_idx" data-idx="<?php echo $idx ?>"><?php echo $idx?></span></th>
									<td>
										<div class="input_file" style="width:250px">
											<input type="text" id="fakeFileTxt<?php echo $idx;?>" class="fakeFileTxt" readonly="readonly" value="<?php echo $v['f_oldname']; ?>" disabled>
											<div class="fileDiv">
												<input type="button" class="buttonImg" value="파일찾기" />
												<input type="file" name="modifyFile[<?php echo $v['f_uid'] ?>]" value="<?php echo $v['f_oldname']; ?>" class="realFile" onchange="javascript:document.getElementById('fakeFileTxt<?php echo $idx;?>').value = this.value" />
											</div>
										</div>
										<div class="preview_thumb"><a href="<?php echo ''.OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$v['f_uid'].''; ?>" class="btn_file" title="<?php echo addslashes($v['f_oldname']); ?>"><?php echo $v['f_oldname']; ?></a></div>
										<label class="design"><input type="checkbox" name="modifyFile_DEL[]" value="<?php echo $v['f_uid']; ?>" >삭제</label>
										<input type="hidden" name="modifyFile_OLD[]" value="<?php echo $v['f_uid'] ?>">
										<?php if($k == 0){ ?>
										<a href="#none" onclick="return false;" data-idx="<?php echo $idx; ?>" class="c_btn h27 icon icon_plus_b exec-addfile">추가</a>
										<div class="c_tip"><?php echo implode(",",$arrUpfileConfig['ext']['file']) ?> 파일만 등록 가능합니다.(최대 <em><?php echo number_format($arrUpfileConfig['cnt']).'</em>개 까지 추가가능' ?>)</div>
										<?php } ?>
									</td>
								</tr>
								<?php }?>
							<?php }else{ ?>
								<tr class="tr-files" data-mode="add">
									<th>첨부파일 <span class="files-idx">1</span></th>
									<td>
										<div class="input_file" style="width:250px">
											<input type="text" id="fakeFileTxt<?php echo $idx;?>" class="fakeFileTxt" readonly="readonly" disabled>
											<div class="fileDiv">
												<input type="button" class="buttonImg" value="파일찾기" />
												<input type="file" name="addFile[]" class="realFile" onchange="javascript:document.getElementById('fakeFileTxt<?php echo $idx;?>').value = this.value" />
											</div>
										</div>
										<a href="#none" onclick="return false;" class="c_btn h27 icon icon_plus_b exec-addfile">추가</a>
										<div class="c_tip"><?php echo implode(",",$arrUpfileConfig['ext']['file']) ?> 파일만 등록 가능합니다.(최대 <em><?php echo number_format($arrUpfileConfig['cnt']).'</em>개 까지 추가가능' ?>)</div>
									</td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</td>
				</tr>

				<tr>
					<th>답변상태</th>
					<td>
						<?php echo _InputRadio( "_status" , array('답변대기','답변완료') , ($row['r_status'] ? $row['r_status'] : "답변대기") , "" , array('답변대기','답변완료') , ""); ?>
					</td>
				</tr>

				<tr>
					<th>관리자답변(메모)</th>
					<td>
					<textarea name="_admcontent" class="design" style="width:90%;height:180px;" ><?php echo stripslashes($row['r_admcontent']); ?></textarea></td>
				</tr>
				<?php if( in_array($row['r_menu'] , array("partner")) ) {?>
				<tr>
					<th>이메일발송</th>
					<td>
						<label class="design"><input type="checkbox" name="_sendmail" value="Y" <?php echo ($row['r_status']=='답변대기'?'checked':''); ?>/> 답변내용 메일 발송하기</label>
						<div class="tip_box">
						<?php echo _DescStr("답변내용을 이메일로 발송하려면 체크된 상태로 저장하세요."); ?>
						<?php echo _DescStr("메일발송을 하지 않을경우 비회원 문의자는 답변을 확인 할 수 없습니다."); ?>
						<?php echo _DescStr("답변상태를 \"답변완료\"로 체크 하고 \"답변내용 메일 발송하기\"를 체크하였을 경우 답변내용이 메일로 발송됩니다."); ?>
						<?php echo _DescStr("답변상태가 \"답변완료\"일때 \"답변내용 메일 발송하기\"를 체크하였을 경우 답변내용이 메일로 재발송됩니다."); ?>
						</div>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<th>참고사항</th>
					<td>문의등록시간 : <?php echo $row['r_rdate']; ?></td>
				</tr>
			</tbody>
		</table>

	</div>


<?php echo _submitBTN("_request.list.php" , "pass_menu={$pass_menu}")?>
</form>


<script>
	// -- 파일 추가 --
	var addfile_auth=true;
	$(document).on('click','.exec-addfile',function(){
		var idx = $('.tr-files').length*1;
		var buid = $('#frmBbs [name="_uid"]').val();
		var upfileCnt = <?php echo $arrUpfileConfig['cnt']; ?>;

		if(addfile_auth !=true){return false}
		addfile_auth = false;

		if( idx >= upfileCnt){ alert('파일첨부는 최대 '+number_format(upfileCnt)+'개 까지 첨부가능합니다.'); return false; }

		var url = '_bbs.post_mng.ajax.php';
	  $.ajax({
	      url: url, cache: false,dataType : 'json', type: "get", data: {ajaxMode:'execAddfile',idx : idx , buid : buid  }, success: function(data){
	      	if( data.rst == 'success') {
				addfile_auth = true;
		      	$('tr.tr-files:last-child').after(data.html);
		      	return true;
		      }else{
		      	return false;
		      }
	      },error:function(request,status,error){ console.log(request.responseText);}
	  });
	});

	// -- 파일 삭제
	$(document).on('click','.exec-delfile',function(){
		$(this).closest('tr.tr-files[data-mode="add"]').remove();
		$('tr.tr-files').each(function(i,v){
			$(v).find('.files-idx').text(i+1);
		});
	});


	// 폼 유효성 검사
	$(document).ready(function(){
		$("form[name=frm]").validate({
				ignore: ".ignore",
				rules: {
						<?php if( in_array($row[r_menu] , array('partner')) ) {?>
						_comname : { required: true },
						_hp: { required: true },
						_email: { required: true },
						<?php } ?>
						_title: { required: true }
						,_content: { required: true }
				},
				invalidHandler: function(event, validator) {
					// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.

				},
				messages: {
						<?php if( in_array($row[r_menu] , array('partner')) ) {?>
						_comname : { required: '이름/상호명을 입력해주시기 바랍니다.' },
						_hp: { required: '연락처를 입력해주시기 바랍니다.' },
						_email: { required: '이메일을 입력해주시기 바랍니다.' },
						<?php } ?>
						_title: { required: '문의제목을 입력해주시기 바랍니다.' }
						,_content: { required: '문의내용을 입력해주시기 바랍니다.' }
				},
				submitHandler : function(form) {
					// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
					form.submit();
				}

		});
	});


</script>


<?PHP
	include_once('wrap.footer.php');
?>