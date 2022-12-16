<?php
$page_title = $boardInfo['bi_name']; // 게시판명
include_once($SkinData['skin_root'].'/'.$boardInfo['bi_view_type'].'.header.php'); // 상단 헤더 출력
?>

<form action="<?php echo OD_PROGRAM_URL.'/board.pro.php'; ?>" name="bbsPost" method="post" enctype="multipart/form-data" autocomplete="off" target="common_frame">
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="_menu" value="<?php echo $_menu; ?>">
	<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">

	<!-- ◆공통페이지 섹션 -->
	<div class="c_section c_board">

		<?php
			echo $BoardSkinData; // -- 스킨데이터 호출 :: program/board.view.php 에서 호출 --
		?>

		<?php
			if( is_login() !== true && is_admin() !== true){
				$policy = arr_policy('Y','guest_board');
		?>
		<!-- 개인정보처리방침 -->
		<div class="c_agree">
			<div class="agree_form">
				<div class="c_group_tit">
					<span class="tit">비회원 글쓰기에 대한 개인정보 수집 및 이용 동의</span>
					<!-- 개인정보처리방침페이지로 이동 -->
					<a href="/?pn=pages.view&type=agree&data=privacy" class="btn" target="_blank" style="display: none;">전체보기</a>
				</div>
				<div class="form">
					<div class="text_box">
						<textarea cols="" rows="12" class="textarea_design" readonly="readonly"><?php echo stripslashes(strip_tags($policy['guest_board']['po_content']))?></textarea>
					</div>
					<div class="agree_check"><label><input type="checkbox" name="_agree"/>위의 내용을 읽고 이에 동의합니다.</label></div>
				</div>
			</div>
		</div>
		<?php } ?>


		<div class="c_btnbox">
			<ul>
				<li><a href="<?php echo $_GET['_PVSC']?"/?".enc('d',$_PVSC):"/?pn=board.list&_menu=".$_menu ?>" class="c_btn h55 line">취소</a></li>
				<li><a href="#none" onclick="return false;" class="c_btn h55 black on-submit">작성완료</a></li>
			</ul>
		</div>



	</div>
	<!-- /공통페이지 섹션 -->
</form>

<script>


	$(function(){recaptcha_resize();}); // 변경된 크기의 스케일 사이즈를 구하고 스케일을 리캽챠에 적용 하여 크기를 줄인다
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
		var upfileCnt = <?php echo $boardFormData['addFileCnt']; ?>;

		if( idx >= upfileCnt){ alert("파일첨부는 최대 <?php echo $arrUpfileConfig['cnt']; ?>개 까지 첨부가능합니다.\n등록된 파일이 있으실경우 삭제 하신 후 추가해 주세요."); return false; }
		var url = '<?php echo OD_PROGRAM_URL.'/board.ajax.php'; ?>';
	  $.ajax({
	      url: url, cache: false,dataType : 'json', type: "get", data: {ajaxMode:'execAddfile',idx : idx , buid : buid  }, success: function(data){
	      	if( data.rst == 'success') {
		      	$('.list-files:last-child').after(data.html);
		      	return true;
		      }else{
		      	return false;
		      }
	      },error:function(request,status,error){ console.log(request.responseText);}
	  });
	});

	//// -- 취소 클릭 시
	//$(document).on('click','.on-cancel',function(){
	//	if( confirm("작성중인 정보는 저장되지 않습니다.\n취소하시겠습니까?") == false){ return false; }
	//	return true;
	//});

	// -- submit 시
	$(document).on('click','.on-submit',function(){
		$('form[name="bbsPost"]').submit();
	});

	$(document).ready(function(){

		recaptcha_resize();

		$('.g-recaptcha').css({
			'width': $('input[name="_title"]').outerWidth()+'px'
		});

		// -  validate ---
		$('form[name=bbsPost]').validate({
			ignore: '.ignore',
			rules: {
				_menu : {required : true  } ,
				_title : {required : true  },
				_writer : {required : true  },

				<?php if($boardInfo['bi_option_date_use'] === true){  ?>
				_sdate : {required : true  },
				_edate : {required : true  },
				<?php } ?>

				<?php if( $boardFormData['passwdUse'] === true ) {   ?>
				_passwd : {required : true , minlength : 4  },
				<?php } ?>

				<?php if( $boardFormData['imagesUploadUse']  === true && $postInfo['b_img1'] == '' ) { ?>
				_img1 : {required : true  },
				<?php } ?>

				_content : {required : true  } ,

				_agree : { required : true}
			},
			messages: {
				_menu : {required : '게시판이 선택되지 않았습니다.'  } ,
				_title : {required : '제목을 입력해 주세요.'  } ,
				_writer : {required : '작성자명을 입력해 주세요.'  },

				<?php if($boardFormData['optionDateUse'] === true ){  ?>
				_sdate : {required : '기간(시작일)을 입력해 주세요.'  },
				_edate : {required : '기간(종료일)을 입력해 주세요.'  },
				<?php } ?>

				<?php if( $boardFormData['passwdUse'] === true ) {   ?>
				_passwd : {required : '비밀번호를 입력해 주세요.' , minlength  : '비밀번호는 최소 4자 이상 입력해 주세요.'  },
				<?php } ?>


				<?php if( $boardFormData['imagesUploadUse']  === true && $postInfo['b_img1'] == '' ) { ?>
				_img1 : {required : '목록 이미지를 등록해 주세요.'  },
				<?php } ?>

				_content : {required : '내용을 입력해 주세요.' },

				_agree : { required : '개인정보처리방침에 동의해 주세요.'}
			},
			submitHandler : function(form) {

				var url = '<?php echo OD_PROGRAM_URL.'/board.ajax.php'; ?>';

				var _writer = $('input[name="_writer"]').val();
				var _title = $('input[name="_title"]').val();
				var _content = $('textarea[name="_content"]').val();
				$.ajax({
					url: url, cache: false,dataType : 'json', type: "post", data: {ajaxMode:'chkForbidden', _writer : _writer, _title : _title, _content : _content,    }, success: function(data){
						if( data.rst == 'success') {
							form.submit();
						}else{
							alert(data.msg);
							$('[name="'+data.key+'"]').focus();
							return false;
						}
				      },error:function(request,status,error){ console.log(request.responseText);}
				});


			}

		});
		// - validate ---
	});


	$.fn.setPreview = function(opt){
		"use strict"
		var defaultOpt = {
			inputFile: $(this),
			img: null,
			w: 100,
			//h: 200
		};
		$.extend(defaultOpt, opt);

		var previewImage = function(){
			if (!defaultOpt.inputFile || !defaultOpt.img) {
			return;
			};

			var inputFile = defaultOpt.inputFile.get(0);
			var img       = defaultOpt.img.get(0);

			// FileReader
			if (window.FileReader) {
				// image 파일만

				if( inputFile.files[0] == undefined){

					$('#img_preview').hide();
					return ;
				}

				if (!inputFile.files[0].type.match(/image\//)){

					$('#img_preview').hide();
						return ;
				};

				// preview
				try {
					var reader = new FileReader();
					reader.onload = function(e){
						img.src = e.target.result;
						img.style.width  = defaultOpt.w+'%';
						//img.style.height = defaultOpt.h+'px';
						img.style.display = '';
					}
					reader.readAsDataURL(inputFile.files[0]);
				} catch (e) {
				}
			// img.filters (MSIE)
			} else if (img.filters) {

				inputFile.select();
				inputFile.blur();
				var imgSrc = document.selection.createRange().text;

				img.style.width  = defaultOpt.w+'%';
				//img.style.height = defaultOpt.h+'px';
				img.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(enable='true',sizingMethod='scale',src=\""+imgSrc+"\")";
				img.style.display = '';
			// no support
			} else {
				$('#img_preview').hide();
				return ;
			}
		};

		// onchange
		$(this).change(function(){
			if( $(this).val() == ''){
				$('#img_preview').hide();
				previewImage();
			}else{
				previewImage();
			}


		});
	};


	$(document).ready(function(){
		var opt = {
			img: $('#img_preview'),
			w: 100,
			//h: 150
		};

		$('#thumb_file').setPreview(opt);
	});

	// 2019-10-23 SSJ :: 취소 클릭 알림창 변경document.ready 이후에 실행시키기 위해 window.load로 적용
	$(window).load(function(){
		// -- 취소 클릭 시
		var form_org = $('form[name=bbsPost]').serialize();
		$(document).on('click','.on-cancel',function(){
			var form_cur = $('form[name=bbsPost]').serialize();
			if(form_org != form_cur){
				if( confirm("작성중인 정보는 저장되지 않습니다.\n취소하시겠습니까?") == false){ return false; }
			}
			return true;
		});
	});
</script>