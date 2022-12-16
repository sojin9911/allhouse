<?php

$page_title = "1:1 온라인 문의";
include_once($SkinData['skin_root'].'/member.header.php'); // 상단 헤더 출력
?>

<!-- ◆공통페이지 섹션 -->
<div class="c_section c_board page-inquiry">


	<form name="frm_request" id="frm_request" method=post action="<?php echo OD_PROGRAM_URL.'/mypage.inquiry.pro.php'; ?>" enctype="multipart/form-data" target="common_frame"  >
		<input type="hidden" name="_menu" value="inquiry">

		<!-- ◆게시판 쓰기 (공통) -->
		<div class="c_form c_board_form">
			<table>
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
							<div class="textarea_box"><textarea name="_content" rows="10" style="" class="textarea_design" placeholder=""></textarea></div>
							<div class="tip_txt black">글 등록 시 주민번호, 계좌번호와 같은 개인정보 입력은 삼가해 주시기 바랍니다.</div>
						</td>
					</tr>
					<tr>
						<th><span class="tit ">첨부파일</span></th>
						<td>
							<div class="tip_txt">첨부파일은 PC에서 등록 가능합니다.</div>
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
			<?php  // 내부패치 68번줄 kms 2019-11-05 ?>
			<li><a href="<?php echo $_GET['_PVSC']?"/?".enc('d',$_PVSC):"/?pn=mypage.inquiry.list" ?>" class="c_btn h55 line">취소</a></li>
			<li><a href="#none" onclick="return false;" class="c_btn h55 black js_inquiry_submit" data-switch="on">문의하기</a></li>
		</ul>
	</div>

</div>
<!-- /공통페이지 섹션 -->


<script type="text/javascript">

	// 리캡챠의 크기를 재조정 한다.
	$(document).ready(function() { // 리캽챠의 크기를 고정한다.(늘아났다가 줄어드는 현상 방지)
		recaptcha_resize();
		$('.g-recaptcha').css({
			'width': $('input[name="_title"]').outerWidth()+'px'
		});
	});
	$(window).load(recaptcha_resize); // 변경된 크기의 스케일 사이즈를 구하고 스케일을 리캽챠에 적용 하여 크기를 줄인다
	$(window).resize(recaptcha_resize); // 변경된 크기의 스케일 사이즈를 구하고 스케일을 리캽챠에 적용 하여 크기를 줄인다
	$(window).on('orientationchange', recaptcha_resize); // 변경된 크기의 스케일 사이즈를 구하고 스케일을 리캽챠에 적용 하여 크기를 줄인다
	function recaptcha_resize() {
		var i_width = $('input[name="_title"]').outerWidth();
		var rscale = i_width/$('.g-recaptcha iframe').width();
		if(rscale > 1) return; // 스케일이 1보다 크지 않도록 조정
		$('.g-recaptcha').css({
			'width': i_width+'px',
			'transform': 'scale('+rscale+')',
			'-webkit-transform': 'scale('+rscale+')',
			'transform-origin': '0 0',
			'-webkit-transform-origin': '0 0'
		});
	}

	$(document).on('click','.js_inquiry_submit',function(){
		$('#frm_request').submit();;
	});

	$(function(){
		document.frm_request.reset(); // form 초기화
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

</script>
