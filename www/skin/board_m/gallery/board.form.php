<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>

	<!-- ◆게시판 쓰기 (공통) -->
	<div class="c_form c_board_form">
		<table>
			<tbody>
				<tr>
					<th class="ess"><span class="tit ">제목</span></th>
					<td>
						<input type="text" name="_title" class="input_design" placeholder="게시글 제목" value="<?php echo $boardFormData['title'] ?>" />
					</td>
				</tr>
				<tr>
					<th class="ess"><span class="tit ">작성자</span></th>
					<td >
						<input type="text" name="_writer" class="input_design" value="<?php echo $boardFormData['writer'];?>" placeholder="작성자 명"/>
					</td>
				</tr>

				<?php if($boardFormData['categoryUse']==true && $boardFormData['category']){?>
				<!-- KAY :: 게시판 카테고리설정 -- 사용여부에 따른 카테고리 설정-->
				<tr>
					<th class=""><span class="tit ">카테고리</span></th>
					<td >
						<div class="select">
							<?php echo _InputSelect( "_category" , array_values($boardFormData['category']) ,$postInfo['b_category'], '', array_values($boardFormData['category']) , ''); ?>
						</div>
					</td>
				</tr>
				<?php }?>

				<?php if( $boardFormData['passwdUse'] === true) {   ?>
				<tr>
					<th class="ess"><span class="tit ">비밀글</span></th>
					<td>
						<div class="input_box">
							<input type="password" name="_passwd" class="input_design" placeholder="숫자 혹은 영문 4글자 이상" autocomplete="new-password" />
							<?php if( $boardFormData['secretUse'] === true ) { // 비밀글 상요 시 ?>
							<label class="label_design"><input type="checkbox" name="_secret"  value="Y" <?php echo $boardFormData['secretChk'] === true ? 'checked':'' ; ?>   />
								<span class="icon"></span><span class="txt">비밀글로 등록</span>
							</label>
							<?php } ?>
						</div>
						<div class="tip_txt"><?php echo $boardFormData['pwTxt']; ?></div>
					</td>
				</tr>
				<?php }else{ ?>

				<?php 	if( $boardFormData['secretUse'] === true ) { // 비밀글 사용 시 ?>
				<tr>
					<th class=""><span class="tit ">비밀글</span></th>
					<td>
						<div class="input_box">
							<label class="label_design"><input type="checkbox" name="_secret" value="Y" <?php echo $boardFormData['secretChk'] === true ? 'checked':'' ; ?> />
								<span class="icon"></span><span class="txt">비밀글로 등록</span>
							</label>
						</div>
					</td>
				</tr>
				<?php 	} ?>
				<?php } ?>

				<?php if( $boardFormData['noticeUse'] === true ) { //  공지글 사용시 :: 관리자 일경우  ?>
				<tr>
					<th class=""><span class="tit ">공지글</span></th>
					<td>
						<div class="input_box">
							<label class="label_design"><input type="checkbox" name="_notice" value="Y" <?php echo $boardFormData['noticeChk'] === true ? 'checked':'' ; ?> />
								<span class="icon"></span><span class="txt">공지사항으로 등록</span>
							</label>
						</div>
					</td>
				</tr>
				<?php } ?>

				<?php if( $boardFormData['optionDateUse'] === true) { ?>

				<tr>
					<th class="ess"><span class="tit ">기간</span></th>
					<td>
						<div class="date">
							<div class="input_box">
								<input type="text" name="_sdate"  class="input_design js_datepic_min_today" value="<?php echo $boardFormData['sdate']; ?>" readonly placeholder="시작일" style="width:110px">
								<span class="dash">~</span>
								<input type="text" name="_edate" class="input_design js_datepic_min_today" value="<?php echo $boardFormData['edate'];?>" readonly placeholder="종료일" style="width:110px">
							</div>
						</div>
					</td>
				</tr>

				<?php } ?>

				<?php if( $boardFormData['imagesUploadUse'] === true) {  ?>
				<tr>
					<th class="ess"><span class="tit ">목록 이미지</span></th>
					<td>

						<!-- 사진첨부 -->
						<div class="form_file">
							<div class="input_file_box">
								<input type="text" id="fakeFileTxt" class="fakeFileTxt" readonly="readonly" disabled=""  placeholder="<?php echo implode(",",$arrUpfileConfig['ext']['images']); ?> 만 등록가능">
								<div class="fileDiv">
									<input type="button" class="buttonImg" value="파일찾기">
									<input type="file" class="realFile" accept="<?php echo 'image/'.implode(", image/",$arrUpfileConfig['ext']['images']); ?>" name="_img1" id="thumb_file" onchange="javascript:document.getElementById('fakeFileTxt').value = this.value">
								</div>
							</div>
						</div>

						<div class="input_box">
							<div class="photo_box js_photo_view">
								<div class="photo_inner"><img src="<?php echo  $boardFormData['listImage'] ; ?>" style="margin-top:10px;width:100%;<?php echo $boardFormData['listImage'] == '' ? 'display: none' : null; ?> " id="img_preview" class="img_preview"></div>
							</div>
							<?php if( $boardFormData['listImage'] != '') { ?>
							<label class="label_design"><input type="checkbox" name="_img1_DEL" value="Y" />첨부 이미지삭제</label>
							<input type="hidden" name="_img1_OLD" value="<?php echo $postInfo['b_img1']; ?>">
							<?php } ?>
						</div>


					</td>
				</tr>
				<?php } ?>


				<tr>
					<!-- <th class="ess"><span class="tit ">내용</span></th> -->
					<td colspan="2">
						<!-- 에디터들어감 -->
						<div class="textarea_box"><textarea name="_content" rows="5" style="" class="textarea_design SEditor" placeholder="" <?php echo $boardFormData['editorUse'] === true ? '':'data-text-mode="true"' ?>><?php echo $boardFormData['content']; ?></textarea></div>
						<div class="tip_txt black">글 등록 시 주민번호, 계좌번호와 같은 개인정보 입력은 삼가해 주시기 바랍니다.</div>
					</td>
				</tr>
				<?php if( $boardFormData['fileUploadUse'] === true) {  ?>
				<tr>
					<th class=""><span class="tit ">첨부파일</span></th>
					<td>
						<div class="tip_txt">첨부파일은 PC에서 등록 가능합니다.</div>
					</td>
				</tr>
				<?php } ?>
				<?php if( $boardFormData['recaptchaUse'] === true) { ?>
				<tr class="tr-recaptcha">
					<th class="ess"><span class="tit ">스팸방지</span></th>
					<td>
						<!-- 스팸방지 들어감 -->
						<script src='https://www.google.com/recaptcha/api.js'></script>
						<div class="g-recaptcha"  data-sitekey="<?php echo $boardFormData['recaptchaApi']; ?>"></div>
						<div class="tip_txt black">스팸방지에 문제가 있을 시 <a href="#none" onclick="grecaptcha.reset(); return false;" >이곳</a> 을 클릭해 주세요.</div>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>

