<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>

		<!-- ◆게시판 쓰기 (공통) -->
		<div class="c_form c_board_form">
			<!-- 리스트 제어 -->
			<div class="c_list_ctrl">
				<div class="tit_box">
					<!-- 게시판명 -->
					<span class="tit"><?php echo $boardInfo['bi_name'] ?></span>
				</div>
			</div>
			<table>
				<colgroup>
					<col width="150"/><col width="*"/><col width="150"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th class="ess"><span class="tit ">글제목</span></th>
						<td colspan="3">
							<input type="text" name="_title" value="<?php echo $boardFormData['title'] ?>" class="input_design" placeholder="게시글 제목" />
						</td>
					</tr>
					<tr>
						<th class="ess"><span class="tit ">작성자</span></th>
						<td colspan="3">
							<input type="text" name="_writer" value="<?php echo $boardFormData['writer'];?>" class="input_design" placeholder="작성자 명" style="width:170px" />
						</td>
					</tr>

					<?php if($boardFormData['categoryUse']==true && $boardFormData['category']){?>
					<!-- KAY :: 게시판 카테고리설정 -- 사용여부에 따른 카테고리 설정-->
					<tr>
						<th class=""><span class="tit ">카테고리</span></th>
						<td colspan="3">
							<?php echo _InputSelect( "_category" , array_values($boardFormData['category']) ,$postInfo['b_category'], '', array_values($boardFormData['category']) , ''); ?>
						</td>
					</tr>
					<?php }?>

					<?php if( $boardFormData['passwdUse'] === true) {   ?>
					<tr>
						<th class="ess"><span class="tit ">비밀번호</span></th>
						<td colspan="3">
							<div class="input_box">
								<input type="password" name="_passwd" class="input_design" placeholder="숫자 혹은 영문 4글자 이상" autocomplete="new-password" style="width:170px" />
								<?php if( $boardFormData['secretUse'] === true ) { ?>
								<label class="if_beside"><input type="checkbox" name="_secret" value="Y" <?php echo $boardFormData['secretChk'] === true ? 'checked':'' ; ?>/>비밀글로 등록합니다.</label>
								<?php } ?>
							</div>
							<div class="tip_txt"><?php echo $boardFormData['pwTxt']; ?></div>
						</td>
					</tr>
					<?php
						}else{
							if( $boardFormData['secretUse'] === true || $boardFormData['noticeUse'] === true){
					?>
					<tr>
						<th class=""><span class="tit ">선택항목</span></th>
						<td colspan="3">
							<?php if( $boardFormData['secretUse'] === true ) { ?>
							<label class=""><input type="checkbox" name="_secret" value="Y" <?php echo $boardFormData['secretChk'] === true ? 'checked':'' ; ?>/>비밀글로 등록합니다.</label>
							<?php } ?>

							<?php if( $boardFormData['noticeUse'] === true ) {  ?>
							<label class=""><input type="checkbox" name="_notice" value="Y" <?php echo $boardFormData['noticeChk'] === true ? 'checked':'' ; ?>/>공지사항으로 등록합니다.</label>
							<?php } ?>
						</td>

					</tr>
					<?php
							} //  비밀글 또는 공지사항 사용가능일 시 :: 공지사항은 관리자만 사용가능
						}
					?>

					<?php if( $boardFormData['optionDateUse'] === true) { ?>
					<tr>
						<th class="ess"><span class="tit ">기간</span></th>
						<td colspan="3">
							<div class="date">
								<div class="input_box">
									<input type="text" name="_sdate"  class="input_design js_datepic_min_today" value="<?php echo $boardFormData['sdate']; ?>" readonly placeholder="시작일" style="width:120px">
									<span class="dash">~</span>
									<input type="text" name="_edate" class="input_design js_datepic_min_today" value="<?php echo $boardFormData['edate'];?>" readonly placeholder="종료일" style="width:120px">
								</div>
							</div>
						</td>
					</tr>
					<?php } ?>

					<?php if( $boardFormData['imagesUploadUse'] === true) {  ?>
					<tr>
						<th class="ess"><span class="tit ">목록 이미지</span></th>
						<td colspan="3">
							<?php if( $boardFormData['listImage'] != '') { ?>
							<div class="duplicate_file">
								<div class="table">
									<div class="title">등록된 이미지</div>
									<ul>
										<li class="upload-file">
											<a href="<?php echo $boardFormData['listImage']; ?>" target="_blank" onclick="" class="txt"><?php echo $postInfo['b_img1']; ?></a>
											<label class="del_btn"><input type="checkbox" name="_img1_DEL" value="Y">삭제</label>
											<input type="hidden" name="_img1_OLD" value="<?php echo $postInfo['b_img1']; ?>">
										</li>
									</ul>
								</div>
							</div>
							<?php } ?>
							<!-- 사진첨부 -->
							<div class="form_file">
								<div class="input_file_box">
									<input type="text" id="fakeFileTxt" class="fakeFileTxt" readonly="readonly" disabled="" placeholder="사진 첨부는 <?php echo implode(",",$arrUpfileConfig['ext']['images']); ?>만 등록가능합니다.">
									<div class="fileDiv">
										<input type="button" class="buttonImg" value="파일찾기">
										<input type="file" class="realFile" name="_img1" onchange="javascript:document.getElementById('fakeFileTxt').value = this.value">
									</div>
								</div>
							</div>

						</td>
					</tr>
					<?php } ?>

					<?php if( $boardFormData['fileUploadUse'] === true) {  ?>
					<tr>
						<th class=""><span class="tit ">첨부파일</span></th>
						<td colspan="3">

							<?php
								// -- 등록된 파일이 있을경우 처리
								if(count($boardFormData['resFile']) > 0) {
							?>
							<!-- 등록된 파일 -->
							<div class="duplicate_file">
								<div class="table">
									<div class="title">등록된 파일</div>
									<ul>
									<?php foreach($boardFormData['resFile'] as $k=>$v){ ?>
										<li class="upload-file">
											<a href="<?php echo ''.OD_PROGRAM_URL.'/filedown.pro.php?_uid='.$v['f_uid'].''; ?>" class="txt"><?php echo $v['f_oldname']; ?></a>
											<label class="del_btn"><input type="checkbox" name="modifyFile_DEL[]" value="<?php echo $v['f_uid']; ?>">삭제</label>
											<input type="hidden" name="modifyFile_OLD[]" value="<?php echo $v['f_uid'] ?>">
											<input type="file" name="modifyFile[<?php echo $v['f_uid'] ?>]" style="display: none;">
										</li>
									<?php } ?>
									</ul>
								</div>
							</div>
							<?php } ?>

							<?php if( $boardFormData['addFileUse'] === true ) {  ?>
							<!-- 사진첨부 -->
							<!-- 파일첨부 추가 삭제시 if_add 클래스 추가 / form_file 반복 -->
							<div class="duplicate if_add">
								<div class="form_file list-files" data-mode="add">
									<div class="input_file_box">
										<input type="text" id="fakeFileTxt1" class="fakeFileTxt" readonly="readonly" disabled="" placeholder="<?php echo implode(",",$arrUpfileConfig['ext']['file']); ?>파일만 등록 가능합니다. 용량이 많을때에는 파일만 대용량이메일로 보내주시기 바랍니다.">
										<div class="fileDiv">
											<input type="button" class="buttonImg" value="파일찾기">
											<input type="file" class="realFile" name="addFile[]" onchange="javascript:document.getElementById('fakeFileTxt1').value = this.value">
										</div>
									</div>
									<!-- 추가 -->
									<span class="add_btn_box"><a href="#none" onclick="return false;" class="c_btn h30 black exec-addfile">+ 추가</a></span>
								</div>
							</div>
							<?php } ?>

						</td>
					</tr>

					<?php } ?>
					<tr>
						<th class="ess"><span class="tit ">내용</span></th>
						<td colspan="3">
							<!-- 에디터들어감 -->
							<div class="textarea_box"><textarea name="_content" rows="5" style="" class="textarea_design SEditor" <?php echo $boardFormData['editorUse'] === true ? '':'data-text-mode="true"' ?> placeholder=""><?php echo $boardFormData['content']; ?></textarea></div>
							<div class="tip_txt black">글 등록 시 주민번호, 계좌번호와 같은 개인정보 입력은 삼가해 주시기 바랍니다.</div>
						</td>
					</tr>

					<?php if( $boardFormData['recaptchaUse'] === true) { ?>
					<tr class="tr-recaptcha">
						<th class="ess"><span class="tit ">스팸방지</span></th>
						<td colspan="3">
							<script src='https://www.google.com/recaptcha/api.js'></script>
							<div class="g-recaptcha"  data-sitekey="<?php echo $boardFormData['recaptchaApi']; ?>"></div>
							<div class="tip_txt black">스팸방지에 문제가 있을 시 <a href="#none" onclick="grecaptcha.reset(); return false;" >이곳</a> 을 클릭해 주세요.</div>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>