<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_board page-inquiry">
	<div class="layout_fix">
		<!-- ◆공통페이지 타이틀 -->
		<div class="c_page_tit hide">
			<div class="title"><a href="/?pn=mypage.main" class="tit">마이페이지</a></div>
			<!-- 로케이션 -->
			<div class="c_location hide">
				<ul>
					<li>홈</li>
					<li>마이페이지</li>
					<li>1:1 온라인 문의</li>
				</ul>
			</div>
		</div>
		<!-- / 공통페이지 타이틀 -->



		<div class="mypage_section">
			<div class="left_sec">

				<?php include_once($SkinData['skin_root'].'/member.header.php'); // -- 공통해더 --  ?>
			</div>	


			<div class="right_sec">
				<form name="frm_request" id="frm_request" method=post action="<?php echo OD_PROGRAM_URL.'/mypage.inquiry.pro.php'; ?>" enctype="multipart/form-data" target="common_frame"  >
					<input type="hidden" name="_menu" value="inquiry">
					<!-- ◆게시판 쓰기 (공통) -->
					<div class="c_form c_board_form">
						<!-- 리스트 제어 -->
						<div class="c_list_ctrl">
							<div class="tit_box">
								<!-- 게시판명 -->
								<span class="tit">1:1 온라인 문의</span>
							</div>
						</div>
						<table>
							<colgroup>
								<col width="150"/><col width="*"/>
							</colgroup>
							<tbody>
								<tr>
									<th class="ess"><span class="tit ">문의제목</span></th>
									<td>
										<input type="text" name="_title" class="input_design" placeholder="문의제목을 입력해 주세요." />
									</td>
								</tr>
								<tr>
									<th class="ess"><span class="tit ">내용</span></th>
									<td>
										<!-- 에디터들어감 -->
										<div class="textarea_box"><textarea name="_content" rows="15" style="" class="textarea_design" placeholder=""></textarea></div>
										<div class="tip_txt black">글 등록 시 주민번호, 계좌번호와 같은 개인정보 입력은 삼가해 주시기 바랍니다.</div>
									</td>
								</tr>
								<tr>
									<th><span class="tit ">첨부파일</span></th>
									<td>
										<!-- 사진첨부 -->
										<!-- 파일첨부 추가 삭제시 if_add 클래스 추가 / form_file 반복 -->
										<div class="duplicate if_add">
											<div class="form_file list-files" data-mode="add">
												<div class="input_file_box">
													<input type="text" id="fakeFileTxt1" class="fakeFileTxt" readonly="readonly" disabled="" placeholder="용량이 많을때에는 파일만 대용량이메일로 보내주시기 바랍니다.">
													<div class="fileDiv">
														<input type="button" class="buttonImg" value="파일찾기">
														<input type="file" class="realFile" name="addFile[]" onchange="javascript:document.getElementById('fakeFileTxt1').value = this.value">
													</div>
												</div>
												<!-- 추가 -->
												<span class="add_btn_box"><a href="#none" onclick="return false;" class="c_btn h30 black exec-addfile">+ 추가</a></span>
											</div>
										</div>
									</td>
								</tr>
								<?php if( $inquiryData['recaptchaUse'] === true){ ?>
								<tr class="tr-recaptcha">
									<th class="ess"><span class="tit ">스팸방지</span></th>
									<td>
										<!-- 스팸방지 들어감 -->
										<script src='https://www.google.com/recaptcha/api.js'></script>
										<div class="g-recaptcha"  data-sitekey="<?php echo $siteInfo['recaptcha_api']; ?>"></div>
										<div class="tip_txt black">스팸방지에 문제가 있을 시 <a href="#none" onclick="grecaptcha.reset(); return false;" >이곳</a> 을 클릭해 주세요.</div>
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</form>


				<div class="c_btnbox">
					<ul>
						<?php // 내부패치 68번줄 kms 2019-11-05  ?>
						<li><a href="<?php echo $_GET['_PVSC']?"/?".enc('d',$_PVSC):"/?pn=board.list&_menu=".$_menu ?>" class="c_btn h55 dark on-cancel">취소</a></li>
						<li><a href="#none" onclick="return false;" class="c_btn h55 color js_inquiry_submit" data-switch="on">문의하기</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /공통페이지 섹션 -->


<script type="text/javascript">

	$(document).on('click','.js_inquiry_submit',function(){
		$('#frm_request').submit();;
	});

	$(function(){
		document.frm_request.reset();
		$("#frm_request").validate({
			ignore: "input[type=text]:hidden",
		    rules: {
			 _title: { required: true, minlength: 2 }
			, _content: { required: true, minlength: 2 }
		    },
		    messages: {
				 _title: { required: "문의제목을 입력하세요", minlength: "2글자 이상 등록하셔야 합니다." }
				, _content: { required: "문의내용을 입력하세요", minlength: "2글자 이상 등록하셔야 합니다." }
		    },
			submitHandler: function(form) {
				// -- 서브밋 연속 클릭 방지
				var chk = $('.js_inquiry_submit').attr('data-switch');
				if( chk == 'on'){
					$('.js_inquiry_submit').attr('data-switch','off');
					form.submit();
					setTimeout(function(){$('.js_inquiry_submit').attr('data-switch','on'); },3000)
				}else{
					alert("잠시만 기다려 주세요.");
					return false;
				}
			}
		});
	})

	// -- 파일 삭제
	$(document).on('click','.exec-delfile',function(){
		$(this).closest('.list-files[data-mode="add"]').remove();
		$('.list-files[data-mode="add"]').each(function(i,v){
			$(v).find('.files-idx').text(i+1);
		});
	});

	// -- 파일 추가 --
	$(document).on('click','.exec-addfile',function(){
		var idx = $('.list-files').length*1;
		var buid = $('#frmBbs [name="_uid"]').val();
		var upfileCnt = <?php echo $arrUpfileConfig['cnt']; ?>;

		if( idx >= upfileCnt){ alert("파일첨부는 최대 <?php echo $arrUpfileConfig['cnt']; ?>개 까지 첨부가능합니다.\n등록된 파일이 있으실경우 삭제 하신 후 추가해 주세요."); return false; }
		var url = '<?php echo OD_PROGRAM_URL.'/_pro.php'; ?>';
		$.ajax({
		      url: url, cache: false,dataType : 'json', type: "get", data: {_mode:'request_add_files',idx : idx , buid : buid  }, success: function(data){
		      	if( data.rst == 'success') {
			      	$('.list-files:last-child').after(data.html);
			      	return true;
			      }else{
			      	return false;
			      }
		      },error:function(request,status,error){ console.log(request.responseText);}
		});
	});
</script>

