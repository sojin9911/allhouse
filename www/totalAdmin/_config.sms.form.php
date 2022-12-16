<?PHP

	include_once("wrap.header.php");



	// ------------ SMS 스타일 시트 불러오기 ------------------
	echo '<link href="' . OD_ADMIN_URL . '/css/sms_style.css" rel="stylesheet" type="text/css" />';




	$r = _MQ_assoc("select * from smart_sms_set");
	foreach($r as $k => $v) {
		$uid = $v[ss_uid];
		${$uid."_status"} = $v[ss_status];
		${$uid."_text"} = $v[ss_text];
	}

	function php_sms_byte_calc($str){

		$pattern =  '/[가-힣]+/u'; // 한글 (2byte 계산)
		preg_match_all($pattern, $str, $match);
		$comment_mb_string = implode('', $match[0]);

		$pattern = '/[^가-힣]+/u'; // 특수문자 (1byte 계산)
		preg_match_all($pattern, $str , $match);
		$comment_special_string = implode('', $match[0]);

		$real_length = strlen($str) - strlen($comment_mb_string) - strlen($comment_special_string) + mb_strlen($comment_mb_string, 'utf-8')  * 2 + mb_strlen($comment_special_string, 'utf-8');

		return $real_length;
	}

?>
<form name="form_sms_info">
	<input type="hidden" name="mode" value="sms_info"/>
	<!-- ●단락타이틀 -->
	<div class="group_title"><strong>SMS계정설정</strong></div>
	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">아이디</th>
					<td><input type="text" name="_smsid" class="design" style="" value="<?=$siteInfo['s_smsid']?>" /></td>
					<th class="ess">비밀번호</th>
					<td><input type="text" name="_smspw" placeholder="변경 시 입력하세요" class="design" value=""  autocomplete="off"></td>
				</tr>
				<tr>
					<td colspan="4" style="text-align:center;">
						<div class="lineup-center">
							<span class="c_btn h34 red bold "><input type="submit" name="" value="계정 설정 저장" /></span>
							<a class="c_btn h34 black " href="_config.sms.out_list.php?type=charge" >충전하기</a>
							<?php
								$SMSUser = onedaynet_sms_user();
								if($SMSUser['code'] == 'U04') {
									echo '
										<a class="c_btn h34 black line gray bold " href="_config.default.form.php#sms_send_tel" >발신번호수정</a>
									';
								}
								if($SMSUser['code'] == 'U00') {
									echo '
										<a class="c_btn h34 black line gray bold " href="_config.sms.out_list.php" >충전내역 (잔여 '.number_format($SMSUser['data'],1).'건)</a>
									';
								}
							?>
							<a class="c_btn h34 line gray bold"  href="http://mobitalk.gobeyond.co.kr/" target="_blank">모비톡 바로가기</a>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<div class="tip_box">
							<?=_DescStr("SMS 관리시 반드시 적용할 서버의 아이피를 등록하시기 바랍니다. 아이피가 등록되지 않은 서버에서는 문자가 발송되지 않습니다.")?>
							<?=_DescStr("현재 아이피 : <strong style='cursor: pointer; outline: none;' class='_copy'>" . $_SERVER['SERVER_ADDR'] . "</strong>")?>
							<?php
								if($SMSUser['code'] != 'U00') {
									$Uniq = uniqid();
									echo _DescStr("<span class='blink_text_" . $Uniq . "'><strong style='font-size:13px; color:#ff0000'>" . $SMSUser['data'] . "</strong></span>");
									echo '<script>$(document).ready(function () {setInterval("$(\'.blink_text_'.$Uniq.'\').fadeOut().fadeIn();",1000);});</script>';
								}
							?>
						</div>
						<script>
							$(document).ready(function(){
								$('form[name=form_sms_info]').on('submit', function(e){ e.preventDefault();
									//if($(this).valid()) {
										if($('input[name=_smspw]').val() == '') { alert('비밀번호를 입력바랍니다.'); $('input[name=_smspw]').focus(); return; }
										var data = $(this).serialize();
										$.ajax({
											data: data,
											type: 'POST',
											cache: false,
											url: './_config.sms.ajax.php',
											success: function(data) {
												if($.trim(data)=='OK') { alert('성공적으로 저장되었습니다.');  location.reload(); } else { alert(data); }
											},
											error:function(request,status,error){
												alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
											}
										});
									//}
								});
							});
						</script>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</form>





<form name="frm" method="POST" action="_config.sms.pro.php" enctype="multipart/form-data">
	<input type="hidden" name="editing" value="N"/>
	<input type="hidden" name="uid" value=""/>
	<input type="hidden" name="menu_idx" value="<?=$menu_idx?>"/>

	<!-- 문자내용 세부설정 -->
	<div class="total_sms">


		<!-- 문자항목들 -->
		<div class="sms_tab">
			<ul>
				<?php
					foreach($arr_sms_text_type as $uid => $value) {
						$_me_check = ${$uid."_status"}=='Y'?true:false;
						$_ad_check = ${"admin_".$uid."_status"}=='Y'?true:false;
						$null_check = ($_me_check === true || $_ad_check === true?true:false); // 2016-12-07 LDD
				?>
					<li class="sms_types" id="sms_item_<?=$uid?>" data-type="<?=$uid?>">
						<!-- hit할 경우 나오는 아이콘 -->
						<span class="hit_icon"><img src="./images/new_sms/opt_ic.png" alt="" /></span>
						<?php if($null_check === true) { // 2016-12-07 LDD ?>
							<!-- 문자내용미리보기 -->
							<div class="quick_preview">
								<span class="edge"><img src="./images/new_sms/prev_arrow.gif" alt="" /></span>
								<dl>
									<? if($_me_check) { ?>
									<dt>회원에게 : <?=${$uid."_text"}?></dt>
									<? } ?>
									<? if($_ad_check) { ?>
									<dd>관리자에게 : <?=${"admin_".$uid."_text"}?></dd>
									<? } ?>
								</dl>
							</div>
						<?php } // 2016-12-07 LDD ?>
						<a href="#none" onclick="return false;" class="link">
							<?=$value?>
							<!-- 전송하는지, 전송안하는지 체크하는 부분 me,checked_me는 회원용;;  ad,checked_ad는 관리자용 클래스 -->
							<span class="send_check">
								<span class="me <?=$_me_check?'checked_me':''?>"><span class="icon"></span><strong>사용자</strong></span>
								<span class="ad <?=$_ad_check?'checked_ad':''?>"><span class="icon"></span><strong>관리자</strong></span>
							</span>
						</a>
					</li>
				<? } ?>
			</ul>
		</div>


		<!-- 휴대폰 전체박스 -->
		<div class="sms_phone_wrap">
			<div class="inner_box">

				<div class="if_user">
					<div class="set_tit"><strong>사용자 메시지 설정</strong></div>
					<!-- 사용자 발송내용 -->
					<div class="sms_phone">
						<div class="body">
							<div class="inner">
								<!-- 전송여부체크 -->
								<div class="check me"><label><input type="checkbox" name="m_status" class="m_status" value="Y" checked /><span class="tx">사용자에게 문자 전송<span class="icon"></span></span></label></div>
								<!-- 제목 lms, mms : placeholder ie하위버전 체크바랍니다 -->
								<div class="title_box"><input type="text" class="input_design m_title" name="m_title" placeholder="문자메세지 제목 입력" style="outline:0;" /></div>
								<!-- 이 상자가 스크롤이 생기는 부분입니다 -->
								<div class="fix_box m_box textarea_wrap" style="cursor: text;">
									<!-- 메세지내용 -->
									<div class="message_box">
										<textarea name="m_text" rows="" cols="" class="m_text chk_length textarea_content" tabindex="1" data-ma="m" placeholder="" style="outline:0;resize:none;"></textarea>
									</div>
								</div>


								<!-- byte검사 문자구분 -->
								<div class="total_box">
									<dl>
										<dt><span class="byte"><span class="m_len" style="color:inherit;">0</span> byte <strong class="m_type">SMS</strong></span></li>
										<dd>
											<div class="file_box">
												<div class="input_file_sms">
													<a href="#none" onclick="return false;" class="buttonImg_delete realFile_delete" data-ma="m" data-delete="Y" title="이미지삭제">&nbsp;</a>
													<input type="text" id="m_fakeFileTxt" class="fakeFileTxt" readonly="readonly" disabled>
													<div class="fileDiv" title="이미지첨부">
														<input type="button" class="buttonImg" value="이미지첨부" />
														<input type="file" accept="image/jpeg" name="m_file" class="realFile m_file" data-ma="m" onchange="javascript:document.getElementById('m_fakeFileTxt').value = this.value.match(/[^\/\\]+$/)" />
														<input type="hidden" name="m_file_OLD" class="realFile_old m_file_OLD" data-ma="m" value=""/>
													</div>
												</div>
											</div>
										</dd>
										<dd><a href="#none" class="btn_rollback" onclick="return false;" title="메시지를 초기 설정으로 되돌립니다." data-ma="m">기본문구</a></dd>
									</dl>
								</div>

							</div>
						</div>
					</div>

					<!-- 사용자 알림톡 발송내용 -->
					<div class="sms_phone kakao_phone">
						<div class="body">
							<div class="inner">
								<!-- 전송여부체크 -->
								<div class="check me"><label><input type="checkbox" name="mk_status" class="mk_status" value="Y" checked /><span class="tx">사용자에게 알림톡 전송<span class="icon"></span></span></label></div>
								<div class="title_box tit"><input type="text" class="input_design mk_kakao_templet_num" name="mk_kakao_templet_num" placeholder="알림톡 템플릿 고유번호 입력" style="outline:0; "></div>
								<div class="title_box code" style="display: none;"><input type="text" class="input_design mk_kakao_btn_link" name="mk_kakao_btn_link" placeholder="알림톡 버튼 사용 시 링크 입력" style="outline:0; " /></div>
								<?php for($i=1; $i<=8; $i++) { ?>
									<div class="title_box code"><input type="text" class="input_design mk_kakao_add<?php echo $i; ?> js_drop_me" name="mk_kakao_add<?php echo $i; ?>" placeholder="알림톡 치환용 추가정보<?php echo $i; ?> 입력" style="outline:0; "></div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>


				<div class="if_admin">
					<div class="set_tit"><strong>관리자 메시지 설정</strong></div>

					<!-- 관리자 발송내용 -->
					<div class="sms_phone">
						<div class="body">
							<div class="inner">
								<!-- 전송여부체크 -->
								<div class="check ad"><label><input type="checkbox" name="a_status" class="a_status" value="Y" checked /><span class="tx">관리자에게 문자 전송<span class="icon"></span></span></label></div>
								<!-- 제목 lms, mms : placeholder ie하위버전 체크바랍니다 -->
								<div class="title_box"><input type="text" class="input_design a_title" name="a_title" placeholder="문자메세지 제목 입력" style="outline:0;" /></div>
								<!-- 이 상자가 스크롤이 생기는 부분입니다 -->
								<div class="fix_box a_box textarea_wrap" style="cursor: text;">
									<!-- 메세지내용 -->
									<div class="message_box">
										<textarea name="a_text" rows="" cols="" class="a_text chk_length textarea_content" tabindex="2" data-ma="a" placeholder="" style="outline:0;resize:none;"></textarea>
									</div>
								</div>

								<!-- byte검사 문자구분 -->
								<div class="total_box">
									<dl>
										<dt><span class="byte"><span class="a_len" style="color:inherit;">0</span> byte <strong class="a_type">SMS</strong></span></dt>
										<dd>
											<div class="file_box">
												<div class="input_file_sms">
													<a href="#none" onclick="return false;" class="buttonImg_delete realFile_delete" data-ma="a" data-delete="Y" title="이미지삭제">&nbsp;</a>
													<input type="text" id="a_fakeFileTxt" class="fakeFileTxt" readonly="readonly" disabled>
													<div class="fileDiv" title="이미지첨부">
														<input type="button" class="buttonImg" value="이미지첨부" />
														<input type="file" accept="image/jpeg" name="a_file" class="realFile a_file" data-ma="a" onchange="javascript:document.getElementById('a_fakeFileTxt').value = this.value.match(/[^\/\\]+$/)" />
														<input type="hidden" name="a_file_OLD" class="realFile_old a_file_OLD" data-ma="a" value=""/>
													</div>
												</div>
											</div>
										</dd>
										<dd><a href="#none" class="btn_rollback" onclick="return false;" title="메시지를 초기 설정으로 되돌립니다." data-ma="a">기본문구</a></dd>
									</dl>
								</div>


							</div>
						</div>
					</div>

					<!-- 관리자 알림톡 발송내용 -->
					<div class="sms_phone kakao_phone">
						<div class="body">
							<div class="inner">
								<!-- 전송여부체크 -->
								<div class="check ad"><label><input type="checkbox" name="ak_status" class="ak_status" value="Y" checked /><span class="tx">관리자에게 알림톡 전송<span class="icon"></span></span></label></div>
								<div class="title_box tit"><input type="text" class="input_design ak_kakao_templet_num" name="ak_kakao_templet_num" placeholder="알림톡 템플릿 고유번호 입력" style="outline:0; "></div>
								<div class="title_box code" style="display: none;"><input type="text" class="input_design ak_kakao_btn_link" name="ak_kakao_btn_link" placeholder="알림톡 버튼 사용 시 링크 입력" style="outline:0; " /></div>
								<?php for($i=1; $i<=8; $i++) { ?>
									<div class="title_box code"><input type="text" class="input_design ak_kakao_add<?php echo $i; ?>  js_drop_me" name="ak_kakao_add<?php echo $i; ?>" placeholder="알림톡 치환용 추가정보<?php echo $i; ?>  입력" style="outline:0; "></div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>


		<!-- 치환자 -->
		<div class="sms_code">
			<div class="inner_box">
				<div class="code_tit">사용 가능한 치환자</div>
				<ul class="replace_item">
					<li data-text="{사이트명}"><strong>{사이트명}</strong> : 사이트명</li>
					<li data-text="{회원아이디}"><strong>{회원아이디}</strong> : 회원아이디</li>
					<li data-text="{회원명}"><strong>{회원명}</strong> : 회원명</li>
					<li data-text="{임시비밀번호}"><strong>{임시비밀번호}</strong> : 임시비밀번호</li>

					<li data-text="{주문번호}"><strong>{주문번호}</strong> : 주문번호</li>
					<li data-text="{주문자명}"><strong>{주문자명}</strong> : 주문자명</li>
					<li data-text="{주문일}"><strong>{주문일}</strong> : 주문일</li>
					<li data-text="{결제금액}"><strong>{결제금액}</strong> : 결제금액</li>
					<li data-text="{입금계좌번호}"><strong>{입금계좌번호}</strong> : 입금계좌번호</li>

					<li data-text="{주문상품명}"><strong>{주문상품명}</strong> : 주문상품명(부분취소요청/부분취소완료전용)</li>
					<li data-text="{택배사}"><strong>{택배사}</strong> : 택배사</li>
					<li data-text="{운송장번호}"><strong>{운송장번호}</strong> : 운송장번호</li>
					<li data-text="{배송일}"><strong>{배송일}</strong> : 배송일</li>

					<li data-text="{후기(문의)상품명}"><strong>{후기(문의)상품명}</strong> : 후기(문의) 상품명(상품후기/상품문의전용)</li>
                    <li data-text="{후기(문의)타이틀}"><strong>{후기(문의)타이틀}</strong> : 후기(문의) 타이틀(상품후기/상품문의전용)</li>
				</ul>
			</div>
		</div>



		<!-- 휴대폰 아래쪽 공간 -->
		<div class="sms_bottom">
			<div class="tip_box">
				<div class="c_tip">위에서 제공된 <u>치환자를 마우스로 드래그하여</u> 입력폼에 놓으면 추가되며, 실제 발송되는 글자수와는 차이가 있을 수 있습니다.</div>
				<div class="c_tip">문자메세지의 제목은 <strong>LMS, MMS</strong>의 경우에만 전송되며 MMS 전송시 이미지는 <u>60kb 이하의 JPG</u>만 등록가능합니다.</div>
				<div class="c_tip">수정을 원하는 항목을 선택하신 후 문자내용이나 전송설정을 변경하신 후 문자내용 저장하기를 꼭 눌러주세요.</div>
				<div class="c_tip">알림톡은 단독발송이 불가능 하며 <strong>사용자 또는 관리자 문자 발송을 사용하는 상태에서만 발송 가능</strong>합니다.</div>
				<div class="c_tip">템플릿을 이용한 알림톡 발송이 실패할 경우 일반 문자메시지로 <strong>대체 발송</strong>되는데, 이 경우 <strong>알림톡 요금이 아닌 문자메시지 요금이 적용</strong>됩니다.</div>
				<div class="c_tip red">대체 발송 시 내용의 길이가 <strong>90byte 이하일 경우 SMS, 이상일 경우 LMS 요금이 과금</strong>됩니다.</div>
			</div>
		</div>



		<!-- 가운데정렬버튼 -->
		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h46 red"><input type="submit" name="" value="모든 내용 저장" /></span></li>
			</ul>
		</div>



		<!-- 확인 후 삭제바람

		<div class="button_box" style="margin: 0 0 30px 40px;">
			<span class="shop_btn_pack btn_input_red"><input style="width:622px;" type="submit" name="" class="input_large" value="위 문자내용 모두 저장하기" /></span>
		</div> -->

	</div>
</form>




<script type="text/javascript">
// detect IE version ( returns false for non-IE browsers )
var ie = function(){for(var e=3,n=document.createElement("div"),r=n.all||[];n.innerHTML="<!--[if gt IE "+ ++e+"]><br><![endif]-->",r[0];);return e>4?e:!e}();
if(ie!==false && ie<10) { $('.input_file_sms').addClass('old-ie'); } else { $('.input_file_sms').removeClass('old-ie'); }


// lastIndexOf function
Array.prototype.lastIndexOf||(Array.prototype.lastIndexOf=function(r){"use strict";if(null==this)throw new TypeError;var t=Object(this),e=t.length>>>0;if(0===e)return-1;var a=e;arguments.length>1&&(a=Number(arguments[1]),a!=a?a=0:0!=a&&a!=1/0&&a!=-(1/0)&&(a=(a>0||-1)*Math.floor(Math.abs(a))));for(var n=a>=0?Math.min(a,e-1):e-Math.abs(a);n>=0;n--)if(n in t&&t[n]===r)return n;return-1});


// 글자 바이트수로 자르기
function cutByte(r,t){var e=r,n=0,c=r.length;for(i=0;c>i;i++){if(n+=chr_byte(r.charAt(i)),n==t-1){e=2==chr_byte(r.charAt(i+1))?r.substring(0,i+1):r.substring(0,i+2);break}if(n==t){e=r.substring(0,i+1);break}}return e}function chr_byte(r){return escape(r).length>4?2:1}


// 치환될 바이트수 정의
var replace_byte = <?php
	$_byte = array(
		"{사이트명}" => array( php_sms_byte_calc('{사이트명}') , php_sms_byte_calc($siteInfo[s_adshop],"EUC-KR") ),
		"{주문번호}" => array( php_sms_byte_calc('{주문번호}') , 17 ),
		"{주문자명}" => array( php_sms_byte_calc('{주문자명}') , 8 )
	);
	echo json_encode($_byte);
?>;


$(document).ready(function(){

	// 치환자 끌어놓기
	$('.replace_item li').disableSelection();
	$(".replace_item li").draggable({helper: 'clone',
		 start: function(e, ui)
		 {
			var _w = ($(this).width()+1); // SSJ: 2017-09-28 넓이에 소수점이 포함될경우 클론의 텍스트가 두줄되는것 방지
			$(ui.helper).css({'width': _w + 'px'});
		 }
	});
	$(".textarea_wrap").droppable({ accept: ".replace_item li", drop: function(ev, ui) {
		$(this).find('.textarea_content').insertAtCaret(ui.draggable.data('text')); check_length();
	}});
	$(".js_drop_me").droppable({ accept: ".replace_item li", drop: function(ev, ui) {
		$(this).insertAtCaret(ui.draggable.data('text')); check_length();
	}});

	// 문자입력 폼
	//$('.textarea_content').autosize();
	// LCY : 2021-12-02 : textarea 초기화 리사이징 수정 
	var autosizeEl = $('.textarea_content').autosize();
	$('.textarea_wrap').on('click',function(){ $(this).find('.textarea_content').focus(); });

	$editing = $('input[name=editing]');
	$uid = $('input[name=uid]');

	$('.m_title, .a_title').hide();

	// SMS 구분 선택하면 입력 페이지 로드
	$('.sms_types').on('click',function(){

		if($editing.val()=='Y') {
			if(!confirm("현재 수정중인 메세지가 있습니다. 저장하지 않고 계속할까요?")) { return false; }
			else { $editing.val('N'); }
		}

		$('.sms_types').removeClass('hit'); $(this).addClass('hit');
		var _type = $(this).data('type');
		$.ajax({
			data: { mode: 'load', type: _type },
			type: 'POST',
			cache: false,
			dataType: 'JSON',
			url: './_config.sms.ajax.php',
			success: function(data) {
				// 현재 SMS 구분
				$uid.val(data.member._uid);

				// 문구 출력
				$('.m_text').val(data.member._text);
				$('.a_text').val(data.admin._text);

				// 제목 출력
				$('.m_title').val(data.member._title);
				$('.a_title').val(data.admin._title);

				// 문구 placeholder 출력
				$('.m_text').attr({ 'placeholder' : data.member._name + ' - 회원에게 전송할 내용을 입력하세요.' });
				$('.a_text').attr({ 'placeholder' : data.admin._name + ' - 관리자에게 전송할 내용을 입력하세요' });

				// 발송여부 선택
				if(data.member._status=='Y') { $('.m_status').prop('checked',true); } else { $('.m_status').prop('checked',false); }
				if(data.admin._status=='Y') { $('.a_status').prop('checked',true); } else { $('.a_status').prop('checked',false); }

				// 첨부파일 있으면 출력
				$('.m_img, .a_img').remove(); $('.m_file, .m_file_OLD, .a_file, .a_file_OLD').val('');
				if(data.member._file) {
					$('.m_box .message_box').prepend('<div class="img_box m_img"><a href="#none" onclick="return false;" data-ma="m" data-delete="Y" class="realFile_delete btn_delete" title="이미지삭제"><img src="../images/new_sms/btn_img_delete.png" alt="" /></a><img src="/upfiles/'+data.member._file+'" alt="" /></div>');
					$('.m_file_OLD').val(data.member._file);
				}
				if(data.admin._file) {
					$('.a_box .message_box').prepend('<div class="img_box a_img"><a href="#none" onclick="return false;" data-ma="a" data-delete="Y" class="realFile_delete btn_delete" title="이미지삭제"><img src="../images/new_sms/btn_img_delete.png" alt="" /></a><img src="/upfiles/'+data.admin._file+'" alt="" /></div>');
					$('.a_file_OLD').val(data.admin._file);
				}



				// 알림톡 데이터 적용
				if(data.kakao_member._status == 'Y') { $('.mk_status').prop('checked',true); } else { $('.mk_status').prop('checked',false); }
				$('.mk_kakao_templet_num').val(data.kakao_member._knum);
				$('.mk_kakao_btn_link').val(data.kakao_member._klink);
				<?php for($i=1; $i<=8; $i++) { ?>
					$('.mk_kakao_add<?php echo $i; ?>').val(data.kakao_member._kadd<?php echo $i; ?>);
				<?php } ?>


				if(data.kakao_admin._status == 'Y') { $('.ak_status').prop('checked',true); } else { $('.ak_status').prop('checked',false); }
				$('.ak_kakao_templet_num').val(data.kakao_admin._knum);
				$('.ak_kakao_btn_link').val(data.kakao_admin._klink);
				<?php for($i=1; $i<=8; $i++) { ?>
					$('.ak_kakao_add<?php echo $i; ?>').val(data.kakao_admin._kadd<?php echo $i; ?>);
				<?php } ?>


				// 콘솔에 출력
				//console.log(data);

				// 문자 타입
				check_length(true);

				// LCY : 2021-12-02 : textarea 초기화 리사이징 수정 
				autosizeEl.trigger('autosize.destroy');
				autosizeEl = $('.textarea_content').css('padding','15px 20px 40px 20px').autosize();

			},
			error:function(request,status,error){
				alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});

	});

	// 최초 페이지 로드시 첫 구분 클릭
	<? if($_GET[_uid]) { ?>$('.sms_types#sms_item_<?=$_GET[_uid]?>').trigger('click');<? } else { ?>$('.sms_types:first').trigger('click');<? } ?>

	// 문구, 제목 수정시 editing 상태 변경
	$('.m_text, .a_text, .m_title, .a_title').on('focus',function(){ $editing.val('Y'); });


	// 파일업로드 처리
	$(".realFile").change(function(){
		var ma = $(this).data('ma');
		if($(this).val().length > 0) {
			// 사이즈 체크
			if(this.files && this.files[0].size > 60*1024) { alert("업로드한 파일 크기가 너무 큽니다.\n60KB 이하로 등록하세요."); $(this).val(''); return false; }
			// 확장자 체크
			var validExtensions = ['jpg','jpeg'];
			var fileName = (ie!==false&&ie<10)?$(this).val().match(/[^\/\\]+$/):this.files[0].name;
			var fileNameExt = (/[.]/.exec(fileName)) ? /[^.]+$/.exec(fileName) : undefined; fileNameExt = $.trim(fileNameExt);
			if($.inArray(fileNameExt, validExtensions) == -1){ alert('JPG 파일만 등록할 수 있습니다.'); $(this).val(''); return false; }
		}
		readURL(ma,this); $('.'+ma+'_box textarea').focus();
	});

	// 업로드한 파일 취소
	$('.textarea_wrap').on('click','.realFile_delete',function(){
		var ma = $(this).data('ma'), del = $(this).data('delete');
		if(confirm("이미지를 삭제하시겠습니까?")) {
			if(del == 'Y') { $('.'+ma+'_file_OLD').val('').trigger('change'); }
			$('.'+ma+'_file').val('').trigger('change'); $('#'+ma+'_fakeFileTxt').val('');
		} else { return false; }
	});

	// 업로드한 파일 취소 (ie8)
	$('.input_file_sms').on('click','.realFile_delete',function(){
		var ma = $(this).data('ma'), del = $(this).data('delete');
		if($('#'+ma+'_fakeFileTxt').val().length == 0) {
			alert('삭제할 이미지가 없습니다.'); return false;
		} else {
			if(confirm("이미지를 삭제하시겠습니까?")) {
				if(del == 'Y') { $('.'+ma+'_file_OLD').val('').trigger('reset').trigger('change'); }
				$('.'+ma+'_file').val('').trigger('reset').trigger('change'); $('#'+ma+'_fakeFileTxt').val('');
			} else { return false; }
		}
	});


	// 문구 작성할때 길이 체크
	$('.chk_length').on('keyup change',function() { check_length(); });


	// IP 복사
	$('._copy').on('click',function(){ $(this).prop('contentEditable',true).css({'cursor':'text'}); document.execCommand('selectAll',false,null); });
	$('._copy').on('blur',function(){ $(this).prop('contentEditable',false).css({'cursor':'pointer'}); $(this).text('<?=$_SERVER[SERVER_ADDR]?>'); });


	// 초기문구로 되돌리기
	$('.btn_rollback').on('click',function(){
		var ma = $(this).data('ma'), uid = $('input[name=uid]').val();
		var confirm_txt = ma=='a'?'관리자':'회원';
		if(confirm(confirm_txt + ' 문구를 초기 세팅상태로 되돌리겠습니까?')) {
			$.ajax({
				data: {'mode':'rollback','uid':uid,'ma':ma},
				type: 'POST',
				cache: false,
				url: './_config.sms.ajax.php',
				success: function(data) {
					$('.'+ma+'_img').remove();
					$('.'+ma+'_text').val(data);
					$('.'+ma+'_file').remove(); $('.'+ma+'_file_OLD').remove();
					$('.check_length').trigger('change');
				},
				error:function(request,status,error){
					alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});
		}
	});
});


// 문자 타입 체크 (sms / lms / mms)
function check_length(onoff) {
	$('.chk_length').each(function(){
		var len = 0, ma = $(this).data('ma'), height = $('.'+ma+'_box').height(), val = $(this).val();
		var current_type = $('.'+ma+'_type').text(), do_not_alert = onoff===true?true:false;

		// 글자수 계산
		if("Mozilla"!=navigator.appCodeName)len=$(this).val().length;else for(var i=0;i<$(this).val().length;i++)$(this).val().substr(i,1)>"~"?len+=2:len++;

		// 치환자 체크
		$.each(replace_byte,function(e,n){-1!=val.indexOf(e)&&(len=len-n[0]+n[1])});

		if(len > 2000) {
			alert('최대 2,000 바이트까지 보내실 수 있습니다.'); val = cutByte(val,1990); $(this).val(val); len = 0;
			// 글자수 및 치환자 재계산
			if("Mozilla"!=navigator.appCodeName)len=$(this).val().length;else for(var i=0;i<$(this).val().length;i++)$(this).val().substr(i,1)>"~"?len+=2:len++;
			$.each(replace_byte,function(e,n){-1!=val.indexOf(e)&&(len=len-n[0]+n[1])});
		}

		$('.'+ma+'_len').text(String(len).comma());
		if($.trim($('.'+ma+'_file').val()).length == 0 && $.trim($('.'+ma+'_file_OLD').val()).length == 0)  {
			if(len > 90) {
				// LMS
				if(current_type=='SMS' && do_not_alert===false) { alert('LMS로 전환되며 추가요금이 발생합니다.'); }
				$('.'+ma+'_type').text('LMS');
				if($('.'+ma+'_title').is(':visible')) { }
				else { $('.'+ma+'_title').show(); $('.'+ma+'_box').height(height - 41); }
			} else {
				// SMS
				$('.'+ma+'_type').text('SMS');
				if($('.'+ma+'_title').is(':visible')) { $('.'+ma+'_title').hide(); $('.'+ma+'_box').height(height + 41); }
			}
		} else {
			// MMS
			if(current_type!='MMS' && do_not_alert===false) { alert('MMS로 전환되며 추가요금이 발생합니다.'); }
			$('.'+ma+'_type').text('MMS');
			if($('.'+ma+'_title').is(':visible')) { }
			else { $('.'+ma+'_title').show(); $('.'+ma+'_box').height(height - 41); }
		}
	});
}


// 파일업로드 처리
function readURL(ma,input) {
	if(ie!==false&&ie<10) {
		//alert($('.'+ma+'_file').val());
		if($('.'+ma+'_file').val().length > 0) {
			$('.'+ma+'_img').remove();
			$('.'+ma+'_text').focus();
			if($('.'+ma+'_title').is(':visible')) { }
			else { $('.'+ma+'_title').show(); $('.'+ma+'_box').height($('.'+ma+'_box').height() - 41); }
			check_length();
		} else { $('.'+ma+'_img').remove(); check_length(); }
	} else {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				$('.'+ma+'_img').remove();
				$('.'+ma+'_box .message_box').prepend('<div class="img_box '+ma+'_img"><a href="#none" onclick="return false;" data-ma="'+ma+'" class="realFile_delete btn_delete" title="이미지삭제"><img src="../images/new_sms/btn_img_delete.png" alt="" /></a><img src="'+e.target.result+'" alt="" /></div>');
				$('.'+ma+'_text').focus();
			}
			reader.readAsDataURL(input.files[0]);
			if($('.'+ma+'_title').is(':visible')) { }
			else { $('.'+ma+'_title').show(); $('.'+ma+'_box').height($('.'+ma+'_box').height() - 41); }
			check_length();
		} else { $('.'+ma+'_img').remove(); check_length(); }
	}
}


// 사용자 문자 발송이 미사용이면 사용자 알림톡 사용도 off
$('.m_status').on('click', function(e) {
	var _ck = $(this).is(':checked');
	if(_ck === false) $('.mk_status').prop('checked', false);
});
$('.mk_status').on('click', function(e) {
	var _ck = $('.m_status').is(':checked');
	if(_ck === false) {
		alert('알림톡은 단독발송이 불가능합니다.');
		e.preventDefault();
	}
});

// 관리자 문자 발송이 미사용이면 관리자 알림톡 사용도 off
$('.a_status').on('click', function(e) {
	var _ck = $(this).is(':checked');
	if(_ck === false) $('.ak_status').prop('checked', false);
});
$('.ak_status').on('click', function(e) {
	var _ck = $('.a_status').is(':checked');
	if(_ck === false) {
		alert('알림톡은 단독발송이 불가능합니다.');
		e.preventDefault();
	}
});



// textarea Auto Height
!function(e){var t,o={className:"autosizejs",id:"autosizejs",append:"\n",callback:!1,resizeDelay:10,placeholder:!0},i='<textarea tabindex="-1" style="position:absolute; top:-999px; left:0; right:auto; bottom:auto; border:0; padding: 0; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; box-sizing:content-box; word-wrap:break-word; height:0 !important; min-height:0 !important; overflow:hidden; transition:none; -webkit-transition:none; -moz-transition:none;"/>',a=["fontFamily","fontSize","fontWeight","fontStyle","letterSpacing","textTransform","wordSpacing","textIndent","whiteSpace"],n=e(i).data("autosize",!0)[0];n.style.lineHeight="99px","99px"===e(n).css("lineHeight")&&a.push("lineHeight"),n.style.lineHeight="",e.fn.autosize=function(i){return this.length?(i=e.extend({},o,i||{}),n.parentNode!==document.body&&e(document.body).append(n),this.each(function(){function o(){var t,o=window.getComputedStyle?window.getComputedStyle(u,null):null;o?(t=parseFloat(o.width),("border-box"===o.boxSizing||"border-box"===o.webkitBoxSizing||"border-box"===o.mozBoxSizing)&&e.each(["paddingLeft","paddingRight","borderLeftWidth","borderRightWidth"],function(e,i){t-=parseFloat(o[i])})):t=p.width(),n.style.width=Math.max(t,0)+"px"}function s(){var s={};if(t=u,n.className=i.className,n.id=i.id,d=parseFloat(p.css("maxHeight")),e.each(a,function(e,t){s[t]=p.css(t)}),e(n).css(s).attr("wrap",p.attr("wrap")),o(),window.chrome){var r=u.style.width;u.style.width="0px";{u.offsetWidth}u.style.width=r}}function r(){var e,a;t!==u?s():o(),n.value=!u.value&&i.placeholder?p.attr("placeholder")||"":u.value,n.value+=i.append||"",n.style.overflowY=u.style.overflowY,a=parseFloat(u.style.height)||0,n.scrollTop=0,n.scrollTop=9e4,e=n.scrollTop,d&&e>d?(u.style.overflowY="scroll",e=d):(u.style.overflowY="hidden",c>e&&(e=c)),e+=z,Math.abs(a-e)>.01&&(u.style.height=e+"px",n.className=n.className,w&&i.callback.call(u,u),p.trigger("autosize.resized"))}function l(){clearTimeout(h),h=setTimeout(function(){var e=p.width();e!==b&&(b=e,r())},parseInt(i.resizeDelay,10))}var d,c,h,u=this,p=e(u),z=0,w=e.isFunction(i.callback),f={height:u.style.height,overflow:u.style.overflow,overflowY:u.style.overflowY,wordWrap:u.style.wordWrap,resize:u.style.resize},b=p.width(),g=p.css("resize");p.data("autosize")||(p.data("autosize",!0),("border-box"===p.css("box-sizing")||"border-box"===p.css("-moz-box-sizing")||"border-box"===p.css("-webkit-box-sizing"))&&(z=p.outerHeight()-p.height()),c=Math.max(parseFloat(p.css("minHeight"))-z||0,p.height()),p.css({overflow:"hidden",overflowY:"hidden",wordWrap:"break-word"}),"vertical"===g?p.css("resize","none"):"both"===g&&p.css("resize","horizontal"),"onpropertychange"in u?"oninput"in u?p.on("input.autosize keyup.autosize",r):p.on("propertychange.autosize",function(){"value"===event.propertyName&&r()}):p.on("input.autosize",r),i.resizeDelay!==!1&&e(window).on("resize.autosize",l),p.on("autosize.resize",r),p.on("autosize.resizeIncludeStyle",function(){t=null,r()}),p.on("autosize.destroy",function(){t=null,clearTimeout(h),e(window).off("resize",l),p.off("autosize").off(".autosize").css(f).removeData("autosize")}),r())})):this}}(jQuery||$);

// paste text at cursor position
$.fn.insertAtCaret=function(t){return this.each(function(){if(document.selection)this.focus(),sel=document.selection.createRange(),sel.text=t,this.focus();else if(this.selectionStart||"0"==this.selectionStart){var s=this.selectionStart,e=this.selectionEnd,i=this.scrollTop;this.value=this.value.substring(0,s)+t+this.value.substring(e,this.value.length),this.focus(),this.selectionStart=s+t.length,this.selectionEnd=s+t.length,this.scrollTop=i}else this.value+=t,this.focus()})};
</script>

<?php include_once('wrap.footer.php'); ?>