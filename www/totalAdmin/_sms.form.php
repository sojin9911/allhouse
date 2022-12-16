<?PHP
	include_once("wrap.header.php");



	// ------------ SMS 스타일 시트 불러오기 ------------------
	echo '<link href="' . OD_ADMIN_URL . '/css/sms_style.css" rel="stylesheet" type="text/css" />';




	/**
	 *
	 * [일회성] 조건충족을 하지 못하면 페이지 block
	 * @return html
	 *
	 */
	function sms_result_msg() {
	    global $SMSUser;

	    if($SMSUser['code'] == 'U01') { // 아이디 또는 비밀번호가 누락되었습니다.
	        $ErrorMSG = $SMSUser['data'];
	        $btn_msg = '수정하기';
	        $btn_url = './_config.sms.form.php';
	        $btn_target = '_self';
	        $_trigger = 'on';
	    }
	    else if($SMSUser['code'] == 'U02') { // 잘못된 계정정보입니다.
	        $ErrorMSG = $SMSUser['data'];
	        $btn_msg = '수정하기';
	        $btn_url = './_config.sms.form.php';
	        $btn_target = '_self';
	        $_trigger = 'on';
	    }
	    else if($SMSUser['code'] == 'U03') { // 등록되지 않은 아이피 입니다.
	        $ErrorMSG = $SMSUser['data'];
	        $btn_msg = '수정하기';
	        $btn_url = './_config.sms.form.php';
	        $btn_target = '_self';
	        $_trigger = 'on';
	    }
	    else if($SMSUser['code'] == 'U04') { // 유효하지 않은 발신번호 입니다.
	        $ErrorMSG = $SMSUser['data'];
	        $btn_msg = '수정하기';
	        $btn_url = './_config.default.form.php#sms_send_tel';
	        $btn_target = '_self';
	        $_trigger = 'on';
	    }
	    else if($SMSUser['code'] == 'U05') { // 발신번호 등록 후 이용가능 합니다.
	        $ErrorMSG = $SMSUser['data'];
	        $btn_msg = '수정하기';
	        $btn_url = 'http://mobitalk.gobeyond.co.kr/pages/customer_modify.form.php';
	        $btn_target = '_blank';
	        $_trigger = 'on';
	    }
	    else if($SMSUser['code'] == 'U06') { // 발신번호 상태가 (대기/반려/만료) 입니다.
	        $ErrorMSG = $SMSUser['data'];
	        $btn_msg = $btn_url = $btn_target = '';
	        $_trigger = 'on';
	    }
	    else if($SMSUser['code'] == 'U00' && $SMSUser['data'] <= 0) { // 잔액부족
	        $ErrorMSG = '충전금액이 부족합니다.';
	        $btn_msg = '충전하기';
	        $btn_url = 'http://mobitalk.gobeyond.co.kr/';
	        $btn_target = '_blank';
	        $_trigger = 'on';
	    }
	    if($_trigger == 'on') {
	        $Opacity = 8;
	        $Uniq = uniqid();
	        echo '<script>$(document).ready(function () {setInterval("$(\'.blink_text_'.$Uniq.'\').fadeOut().fadeIn();",1000);});</script>';
	        echo '
	            <div class="sms_notyet">
	                <div class="inner">
						<div class="btn">
	                    '.($btn_msg?'<a class="c_btn h34 red bold" href="'.$btn_url.'" target="'.$btn_target.'">'.$btn_msg.'</a>':null).'
	                    <div class="btn_txt blink_text_'.$Uniq.'">✘ '.$ErrorMSG.'
	                    '.($btn_msg?'</div>':null).'
						</div>
	                </div>
	            </div>
	        ';
	    }
	}
	$SMSUser = onedaynet_sms_user();
	sms_result_msg();

?>










<script>
!function(e){var t,o={className:"autosizejs",id:"autosizejs",append:"\n",callback:!1,resizeDelay:10,placeholder:!0},i='<textarea tabindex="-1" style="position:absolute; top:-999px; left:0; right:auto; bottom:auto; border:0; padding: 0; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; box-sizing:content-box; word-wrap:break-word; height:0 !important; min-height:0 !important; overflow:hidden; transition:none; -webkit-transition:none; -moz-transition:none;"/>',a=["fontFamily","fontSize","fontWeight","fontStyle","letterSpacing","textTransform","wordSpacing","textIndent","whiteSpace"],n=e(i).data("autosize",!0)[0];n.style.lineHeight="99px","99px"===e(n).css("lineHeight")&&a.push("lineHeight"),n.style.lineHeight="",e.fn.autosize=function(i){return this.length?(i=e.extend({},o,i||{}),n.parentNode!==document.body&&e(document.body).append(n),this.each(function(){function o(){var t,o=window.getComputedStyle?window.getComputedStyle(u,null):null;o?(t=parseFloat(o.width),("border-box"===o.boxSizing||"border-box"===o.webkitBoxSizing||"border-box"===o.mozBoxSizing)&&e.each(["paddingLeft","paddingRight","borderLeftWidth","borderRightWidth"],function(e,i){t-=parseFloat(o[i])})):t=p.width(),n.style.width=Math.max(t,0)+"px"}function s(){var s={};if(t=u,n.className=i.className,n.id=i.id,d=parseFloat(p.css("maxHeight")),e.each(a,function(e,t){s[t]=p.css(t)}),e(n).css(s).attr("wrap",p.attr("wrap")),o(),window.chrome){var r=u.style.width;u.style.width="0px";{u.offsetWidth}u.style.width=r}}function r(){var e,a;t!==u?s():o(),n.value=!u.value&&i.placeholder?p.attr("placeholder")||"":u.value,n.value+=i.append||"",n.style.overflowY=u.style.overflowY,a=parseFloat(u.style.height)||0,n.scrollTop=0,n.scrollTop=9e4,e=n.scrollTop,d&&e>d?(u.style.overflowY="scroll",e=d):(u.style.overflowY="hidden",c>e&&(e=c)),e+=z,Math.abs(a-e)>.01&&(u.style.height=e+"px",n.className=n.className,w&&i.callback.call(u,u),p.trigger("autosize.resized"))}function l(){clearTimeout(h),h=setTimeout(function(){var e=p.width();e!==b&&(b=e,r())},parseInt(i.resizeDelay,10))}var d,c,h,u=this,p=e(u),z=0,w=e.isFunction(i.callback),f={height:u.style.height,overflow:u.style.overflow,overflowY:u.style.overflowY,wordWrap:u.style.wordWrap,resize:u.style.resize},b=p.width(),g=p.css("resize");p.data("autosize")||(p.data("autosize",!0),("border-box"===p.css("box-sizing")||"border-box"===p.css("-moz-box-sizing")||"border-box"===p.css("-webkit-box-sizing"))&&(z=p.outerHeight()-p.height()),c=Math.max(parseFloat(p.css("minHeight"))-z||0,p.height()),p.css({overflow:"hidden",overflowY:"hidden",wordWrap:"break-word"}),"vertical"===g?p.css("resize","none"):"both"===g&&p.css("resize","horizontal"),"onpropertychange"in u?"oninput"in u?p.on("input.autosize keyup.autosize",r):p.on("propertychange.autosize",function(){"value"===event.propertyName&&r()}):p.on("input.autosize",r),i.resizeDelay!==!1&&e(window).on("resize.autosize",l),p.on("autosize.resize",r),p.on("autosize.resizeIncludeStyle",function(){t=null,r()}),p.on("autosize.destroy",function(){t=null,clearTimeout(h),e(window).off("resize",l),p.off("autosize").off(".autosize").css(f).removeData("autosize")}),r())})):this}}(jQuery||$);
</script>


<form name="form_sms" method="post" action="_sms.pro.php" target="common_frame" enctype="multipart/form-data">
<input type="hidden" name="form" id="form" value="sendform">
<input type="hidden" name="send_list_serial" id="send_list_serial">

	<!-- 문자내용 세부설정 -->
	<div class="total_sms if_sendpage">



		<!-- 휴대폰한번감싸기 -->
		<div class="sms_phone_wrap">

			<ul class="send_layout">
				<li class="left">

					<!-- 문자항목들 -->
					<div class="sms_send">



						<!-- 받는 사람 입력부분 -->
						<div class="send_to">
							<dl>
								<dt>
									<div class="title">휴대폰 번호 입력</div>
									<textarea name="send_to_num" tabindex="1" id="send_to_num" type="text" class="input_phone" placeholder="번호를 입력해주세요." ></textarea>
									<!-- <input type="button" name="" tabindex="4" class="btn_add" value="+ 받는사람 추가" onclick="send_list_add(form_sms)"/>  -->
									<div class="this_btn">
										<span class="c_btn h27"><input type="button" name="" tabindex="4" value="+ 받는사람 추가" onclick="send_list_add(form_sms)" /></span>
									</div>
									<span class="edge"></span>
								</dt>
								<dd>
									<div class="title">받는 사람 총 <strong id="slt_phonecnt">0</strong> 명</div>
									<div class="result"><select name="send_list" id="send_list" multiple class="" ></select></div>
									<div class="this_btn">
										<!-- <a href="#none" onclick="send_list_delete(form_sms);return false;" class="btn_delete">선택삭제</a> -->
										<a class="c_btn h27" href="#none" onclick="send_list_delete(form_sms);return false;">- 선택 삭제하기</a>
									</div>
								</dd>
							</dl>
							<div class="tip_box">
								<?=_DescStr("휴대폰 번호를 Enter 단위로 입력하세요.")?>
								<?=_DescStr("복사한 번호를 붙여넣어서 편리하게 추가 할 수 있습니다.")?>
							</div>
						</div>



						<!-- 예약전송 입력부분 -->
						<div class="send_reserve if_open"><!-- 체크하면 클래스값 -->
							<dl>
								<dt><label><input type="checkbox" name="_reserv_chk" id="_reserv_chk" value="Y" />예약한 시간에 전송하기</label></dt>
								<?PHP
									$arr_12 = array(); $arr_30 = array(); $arr_24 = array(); $arr_60 = array();
									for( $i=1;$i<=12;$i++ ){ $arr_12[] = sprintf("%02d",$i); }
									for( $i=0;$i<=31;$i++ ){ $arr_30[] = sprintf("%02d",$i); }
									for( $i=0;$i<=23;$i++ ){ $arr_24[] = sprintf("%02d",$i); }
									for( $i=0;$i<=59;$i++ ){ $arr_60[] = sprintf("%02d",$i); }
								?>
								<dd>
									<div class="box">
										<strong>날짜</strong>
										<span class="time"><?=_InputSelect( "_reserv_y" , array(date(Y),date(Y,strtotime("+1 year"))) , date(Y) , "" , "" , "-")?><span class="unit">년</span></span>
										<span class="time"><?=_InputSelect( "_reserv_m" , $arr_12 , date(m) , "" , "" , "-")?><span class="unit">월</span></span>
										<span class="time"><?=_InputSelect( "_reserv_d" , $arr_30 , date(d) , "" , "" , "-")?><span class="unit">일</span></span>
									</div>
									<div class="box">
										<strong>시간</strong>
										<span class="time"><?=_InputSelect( "_reserv_h" , $arr_24 , date(H) , "" , "" , "-")?><span class="unit">시</span></span>
										<span class="time"><?=_InputSelect( "_reserv_i" , $arr_60 , date(i) , "" , "" , "-")?><span class="unit">분</span></span>
									</div>
								</dd>
							</dl>
						</div>


						<!-- 보내는 사람 입력부분 (기본정보에서 기본입력) -->
						<div class="send_from">
							<dl>
								<dt>
									<strong>발신번호</strong> ☎ <?=$siteInfo[s_glbtel]?>
									<input name="send_from_num" id="send_from_num" class="input_design" type="hidden" value="<?=$siteInfo[s_glbtel]?>" readonly>
								</dt>
								<dd>
									<span class="c_btn h46 red"><input type="button" name="" onclick="send_ok(form_sms); return false;" value="문자 전송하기" /></span>
								</dd>
							</dl>
						</div>
					</div>


				</li>
				<li class="right">

					<!-- 휴대폰폼 -->
					<div class="sms_phone">
						<div class="body">
							<div class="inner">

								<!-- 제목 lms, mms : placeholder ie하위버전 체크바랍니다 -->
								<div class="title_box"><input type="text" class="input_design " style="outline:0;" name="send_title" placeholder="문자메세지 제목 입력" /></div>

								<!-- 이 상자가 스크롤이 생기는 부분입니다 -->
								<div class="fix_box a_box textarea_wrap" style="cursor:text;">
									<!-- 메세지내용 -->
									<div class="message_box">
										<!-- 이미지첨부 들어갈 위치 -->
										<div class="textarea" style="border:0;cursor: text;">
											<textarea name="message" id="message" tabindex="1" rows="4" data-ma="a" style="display:block;resize:none;width:100%;outline:0;" class="textarea_content chk_length" placeholder="문자 내용을 입력해주세요."></textarea>
										</div>
									</div>
								</div>

								<!-- byte검사 문자구분 -->
								<div class="total_box">
									<dl>
										<dt><span class="byte"><span style="color:inherit;" id="message_len_id" class="a_len">0</span> byte <strong id="sms_type" class="a_type">SMS</strong></span></dt>
										<dd>
											<!-- 이미지첨부 -->
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
									</dl>
								</div>

							</div>
						</div>
					</div>

				</li>
			</ul>
		</div>



		<!-- 휴대폰 아래쪽 공간 -->
		<div class="sms_bottom">

			<div class="tip_box">
				<div class="c_tip">문자메세지의 제목은 <strong>LMS, MMS</strong>의 경우에만 발송됩니다.</div>
				<div class="c_tip">이미지를 등록한 경우 자동으로 MMS 형태로 변경되며 <u>JPG만 업로드 가능</u>합니다.</div>
				<div class="c_tip">발신번호는 <u>환경설정 &gt; 기본설정</u>에서 수정 가능하며, 발신번호 인증이 완료된 번호만 사용가능합니다.</div>
			</div>
		</div>


		<!-- 가운데정렬버튼 -->
		<div class="c_btnbox">
			<ul>
				<li><a class="c_btn h46 black " href="_config.sms.out_list.php?type=charge" >SMS 충전하기</a></li>
				<li>
					<?php
						if($SMSUser['code'] == 'U04') {
							echo '
								<a class="c_btn h46 black line gray  " href="_config.default.form.php#sms_send_tel" >발신번호수정</a>
							';
						}
						if($SMSUser['code'] == 'U00') {
							echo '
								<a class="c_btn h46 black line gray  " href="_config.sms.out_list.php" >SMS 충전내역 (잔여 '.number_format($SMSUser['data'],1).'건)</a>
							';
						}
					?>
				</li>
				<li><a class="c_btn h46 line gray "  href="http://mobitalk.gobeyond.co.kr/" target="_blank">모비톡 바로가기</a></li>
			</ul>
		</div>


		<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
		<!-- <div class="data_form">
			<table class="table_form">
				<tbody>

					<tr>
						<td style="text-align:center;">

							<div class="lineup-center">

								<a class="c_btn h34 black " href="_config.sms.out_list.php?type=charge" >SMS 충전하기</a>



								<a class="c_btn h34 line gray bold"  href="http://mobitalk.gobeyond.co.kr/" target="_blank">모비톡 방문하기</a>

							</div>

						</td>
					</tr>

				</tbody>
			</table>

		</div> -->




	</div>
</form>






<script>

// detect IE version ( returns false for non-IE browsers )
var ie = function(){for(var e=3,n=document.createElement("div"),r=n.all||[];n.innerHTML="<!--[if gt IE "+ ++e+"]><br><![endif]-->",r[0];);return e>4?e:!e}();
if(ie!==false && ie<10) { $('.input_file_sms').addClass('old-ie'); } else { $('.input_file_sms').removeClass('old-ie'); }

// lastIndexOf function
Array.prototype.lastIndexOf||(Array.prototype.lastIndexOf=function(r){"use strict";if(null==this)throw new TypeError;var t=Object(this),e=t.length>>>0;if(0===e)return-1;var a=e;arguments.length>1&&(a=Number(arguments[1]),a!=a?a=0:0!=a&&a!=1/0&&a!=-(1/0)&&(a=(a>0||-1)*Math.floor(Math.abs(a))));for(var n=a>=0?Math.min(a,e-1):e-Math.abs(a);n>=0;n--)if(n in t&&t[n]===r)return n;return-1});

// 글자 바이트수로 자르기
function cutByte(r,t){var e=r,n=0,c=r.length;for(i=0;c>i;i++){if(n+=chr_byte(r.charAt(i)),n==t-1){e=2==chr_byte(r.charAt(i+1))?r.substring(0,i+1):r.substring(0,i+2);break}if(n==t){e=r.substring(0,i+1);break}}return e}function chr_byte(r){return escape(r).length>4?2:1}

// 숫자에 콤마 추가
String.prototype.comma=function(){var r=this.replace(/,/g,"");if("0"==r)return"0";var t=/^(-?\d+)(\d{3})($|\..*$)/;return t.test(r)&&(r=r.replace(t,function(r,t,e,n){return t.comma()+(","+e+n)})),r};


	function send_list_add(form){

		var send_list_count = document.getElementById("send_list").options.length;
		var send_to_num = document.getElementById("send_to_num").value.trim();

		if(!send_to_num) { alert("받는사람 번호를 입력하세요."); $('#send_to_num').focus(); return false; }

		if(send_to_num) {

			// JJC : 핸드폰번호 중복제거 : 2020-10-23 : 배열 초기화 및 받는핸드폰 번호 가져오기
			var final_data = [];
			$("#send_list option").each(function(){
				final_data.push($(this).text());
			});
			// JJC : 핸드폰번호 중복제거 : 2020-10-23 : 배열 초기화 및 받는핸드폰 번호 가져오기

			var ex = send_to_num.split("\n");
			for ( var i in ex ) {
				if(ex[i].trim() !='' ){
					var send_to_new = "";
					var send_to_new_tmp = ex[i].replace(/-/gi, "");
					if( send_to_new_tmp.length == 11 ) {
						send_to_new = send_to_new_tmp.substring(0, 3) + "-" + send_to_new_tmp.substring(3, 7) + "-" + send_to_new_tmp.substring(7);
					}
					else if( send_to_new_tmp.length == 10 ) {
						send_to_new = send_to_new_tmp.substring(0, 3) + "-" + send_to_new_tmp.substring(3, 6) + "-" + send_to_new_tmp.substring(6);
					}

					// JJC : 핸드폰번호 중복제거 : 2020-10-23 : 중복요소 확인
					if(send_to_new != '' && final_data.indexOf(send_to_new) == -1 ) {
						document.getElementById("send_list").options[send_list_count] = new Option(send_to_new,send_to_new);
						send_list_count ++;
						final_data.push(send_to_new);
					}
					// JJC : 핸드폰번호 중복제거 : 2020-10-23 : 중복요소 확인

				}
			}
			if(send_list_count > 300) {
				$("#send_list option").remove();
				alert("한꺼번에 300개를 초과하여 발송할 수 없습니다.");
			}
			document.getElementById("send_to_num").value="";
			document.getElementById("slt_phonecnt").innerHTML=document.getElementById("send_list").options.length;
			document.getElementById("send_to_num").focus();

		}
	}

	function send_list_delete(form){
		var send_list_count = document.getElementById("send_list").options.length;

		for(i=0;i<send_list_count;i++){
			if(document.getElementById("send_list").options[i].selected == true){
				document.getElementById("send_list").options[i] = null;
				send_list_count--;
				i--;
			}
		}
		document.getElementById("slt_phonecnt").innerHTML=document.getElementById("send_list").options.length;
	}


	function send_ok(form){

		if(document.getElementById("message").value=="메세지를 입력해주세요" || document.getElementById("message").value==""){
			alert("메시지를 입력해 주세요.");
			document.getElementById("message").value="";
			document.getElementById("message_len_id").innerHTML="0";
			//document.getElementById("message").focus();
			$('.textarea_content').focus();
		}
		else{
			var send_list_count = document.getElementById("send_list").options.length;
			var send_list_value = "";

			for(i=0;i<send_list_count;i++){
				if(i==0) send_list_value += document.getElementById("send_list").options[i].value;
				else send_list_value += "/" + document.getElementById("send_list").options[i].value;
			}

			if(send_list_value == ""){
				alert("메시지를 받을 전화번호를 추가해주세요");
				document.getElementById("send_to_num1").focus();
			}
			else{
				if(confirm('메시지 발송 개수에 따라 수초에서 수분이상 걸릴 수 있습니다.\n발송완료 메시지를 받을때까지\n추가로 문자전송하기 버튼을 클릭하지마세요.\n전송하시겠습니까?')){
					document.getElementById("send_list_serial").value = send_list_value;
					document.form_sms.submit();
				}
			}
		}
	}

	function str_length(form) {

		if ( navigator.appCodeName != 'Mozilla' ) {
			return document.getElementById("message").value.length;
		}

		var len = 0;

		for (var i=0; i<document.getElementById("message").value.length; i++) {

			if ( document.getElementById("message").value.substr(i, 1) > '~' ) {
				len+=2;
			}
			else {
				len++;
			}
		}

		return len;
	}

	function str_prev() {
		if ( navigator.appCodeName != 'Mozilla' ) {
			return document.SEND.h_content.value.length;
		}
		var len = 0;

		for (var i=0; i<document.SEND.h_content.value.length; i++) {
			if ( document.SEND.h_content.value.substr(i, 1) > '~' ) {
				len+=2;
			}
			else {
				len++;
			}

			if (len > 200) {
				return i
			}
		}

		return len;
	}

	$(document).ready(function(){

		// 문자입력 폼
		$('.textarea_content').autosize();
		$('.textarea_wrap').on('click',function(){ $(this).find('.textarea_content').focus(); });

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
		$('.chk_length').on('keyup',function() { check_length(); });

	});



	// 문자 타입 체크 (sms / lms / mms)
	function check_length(onoff) {
		$('.chk_length').each(function(){
			var len = 0, ma = $(this).data('ma'), height = $('.'+ma+'_box').height(), val = $(this).val();
			var current_type = $('.'+ma+'_type').text(), do_not_alert = onoff===true?true:false;

			// 글자수 계산
			if("Mozilla"!=navigator.appCodeName)len=$(this).val().length;else for(var i=0;i<$(this).val().length;i++)$(this).val().substr(i,1)>"~"?len+=2:len++;

			if(len > 2000) {
				alert('최대 2,000 바이트까지 보내실 수 있습니다.'); val = cutByte(val,1990); $(this).val(val); len = 0;
				// 글자수 재계산
				if("Mozilla"!=navigator.appCodeName)len=$(this).val().length;else for(var i=0;i<$(this).val().length;i++)$(this).val().substr(i,1)>"~"?len+=2:len++;
			}

			$('.'+ma+'_len').text(String(len).comma());
			if($.trim($('.'+ma+'_file').val()).length == 0 && $.trim($('.'+ma+'_file_OLD').val()).length == 0)  {
				if(len > 90) {
					// LMS
					if(current_type=='SMS' && do_not_alert===false) { alert('LMS로 전환되며 추가요금이 발생합니다.'); }
					$('.'+ma+'_type').text('LMS');
				} else {
					// SMS
					$('.'+ma+'_type').text('SMS');
				}
			} else {
				// MMS
				if(current_type!='MMS' && do_not_alert===false) { alert('MMS로 전환되며 추가요금이 발생합니다.'); }
				$('.'+ma+'_type').text('MMS');
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
				check_length();
			} else { $('.'+ma+'_img').remove(); check_length(); }
		}
	}

</script>














<?
    // - 회원선택으로부터 넘어왔을 경우 체크 ---
    if($ctrlMode) {


        switch($ctrlMode){
            // --- 선택회원 ---
            case "select":
            if( count($chk_cellular) < 1){ $chk_cellular = $arrID; }
            // --- 선택회원 ---
            $sres = _MQ_assoc("select in_tel2 from smart_individual where find_in_set(in_id,'".implode(',',$chk_cellular)."') > 0 ");


            foreach($sres as $sk=>$sv){
                $chk_cellular[$sk]=rm_str($sv[in_tel2]);
            }

            break; // 적용되어 넘어온 상태

            // --- 검색회원 ---
            case "search":

            if($_search_que == ''){ $_search_que = $searchQue; }

                $chk_cellular = array();
                $sres = _MQ_assoc(" select in_tel2 " . enc('d' ,  $_search_que ) . " ORDER BY in_rdate desc ");// JJC : 검색 시 `from smart_individual` 중복 오류수정 : 2020-09-24
                foreach($sres as $sk=>$sv){
                    $chk_cellular[$sk]=rm_str($sv[in_tel2]);
                }
                break;
            // --- 검색회원 ---
        }
?>
    <SCRIPT>
        $(document).ready(function(){
            $("#send_list").find("option").remove();
            var option_str = "";
            <? foreach(array_filter($chk_cellular) as $sk=>$sv){ echo "option_str += \"<option value='". $sv ."'>". $sv ."</option>\";\n"; } ?>
            $("#send_list").append(option_str);
            $("#slt_phonecnt").html("<?=sizeof($chk_cellular)?>");
        });
    </SCRIPT>
<?
    }
    // - 회원선택으로부터 넘어왔을 경우 체크 ---

    // 하단
    include_once("wrap.footer.php");
?>