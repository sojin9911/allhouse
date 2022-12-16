/*
	-- totalAdmin Common Script -- 
*/
(function() {
  // Union of Chrome, Firefox, IE, Opera, and Safari console methods
  var methods = ["assert", "cd", "clear", "count", "countReset",
    "debug", "dir", "dirxml", "error", "exception", "group", "groupCollapsed",
    "groupEnd", "info", "log", "markTimeline", "profile", "profileEnd",
    "select", "table", "time", "timeEnd", "timeStamp", "timeline",
    "timelineEnd", "trace", "warn"];
  var length = methods.length;
  var console = (window.console = window.console || {});
  var method;
  var noop = function() {};
  while (length--) {
    method = methods[length];
    // define undefined methods as noops to prevent errors
    if (!console[method])
      console[method] = noop;
  }
})();

// -- 객체선언 
var common = {}

$(document).ready(function(){
	// -- 통합관리자 메뉴 열고/닫기 이벤트 --
	$(document).on('click','.js-menu-ctl',function(){
		var chk = $('.js-menu-container').hasClass('if_hide'); // 닫혀있는지 체크 
		if( chk == true){ // 닫혀있다면 연다.
			$(this).attr('title','메뉴닫기');
			$('.js-menu-container').removeClass('if_hide');
		}else{ // 열려있다면 닫는다.
			$(this).attr('title','메뉴열기');
			$('.js-menu-container').addClass('if_hide');
		}
	});
	// -- 통합관리자 메뉴 열고/닫기 이벤트 --

	// -- 통합관리자 서브 메뉴 열고/닫기 이벤트 --
	$(document).on('click','.js-sub-menu-ctl',function(){
		var uid = $(this).attr('data-amuid'); //  고유번호
		var chk = $('.js-sub-menu-container[data-amuid="'+uid+'"]').hasClass('if_open'); // 열려있는지 체크
		if( chk == true){ // 열려있다면 닫는다.
			$('.js-sub-menu-container[data-amuid="'+uid+'"]').removeClass('if_open');
		}else{ // 닫혀있다면 연다
			$('.js-sub-menu-container').removeClass('if_open');
			$('.js-sub-menu-container[data-amuid="'+uid+'"]').addClass('if_open');
		}
	});	
	// -- 통합관리자 서브 메뉴 열고/닫기 이벤트 --

});

// 전체 선택 or 해제
$(document).on('click','.js_AllCK', function() {
		var checked = $(this).is(':checked');
		//if(checked === true) $('input[type=checkbox].js_ck:not(.disabled)').prop('checked',true);
		//else $('input[type=checkbox].js_ck').removeAttr('checked');
		if(checked === true) $('input[type=checkbox].js_ck:not(.disabled)').prop('checked', false).trigger('click');
		else $('input[type=checkbox].js_ck:not(.disabled)').prop('checked', true).trigger('click');
});

// 하위 항목 체크박스
$(document).on('click','input[type=checkbox].js_ck',  function() {
		var Leng = $('input[type=checkbox].js_ck').length;
		var checked = 0;
		$.each($('input[type=checkbox].js_ck'), function() {
				var undo_check = $(this).is(':checked');
				if(undo_check === true) checked++;
		});

		if(Leng == checked) $('.js_AllCK').attr('checked', 'checked');
		else $('.js_AllCK').removeAttr('checked');
});

// 전체 선택 or 해제 -- 리스트 상단 버튼에서함수로 제어
function selectAll(_type){
	if(_type == 'Y'){
		$('.js_AllCK, .js_option_AllCK').prop('checked', false).trigger('click');
	}else if(_type == 'N'){
		$('.js_AllCK, .js_option_AllCK').prop('checked', true).trigger('click');
	}
}



// JJC : 전체 선택 or 해제 --- 상품옵션일괄관리에 대한 처리 : 2017-11-21
$(document).on('click','.js_option_AllCK', function() {
		var checked = $(this).is(':checked');
		var pcode = $(this).data('pcode');
		//if(checked === true) $('input[type=checkbox].js_ck_'+ pcode +':not(.disabled)').prop('checked',true);
		//else $('input[type=checkbox].js_ck_'+ pcode ).removeAttr('checked');
		if(checked === true) $('input[type=checkbox].js_ck_'+ pcode +':not(.disabled)').prop('checked', false).trigger('click');
		else $('input[type=checkbox].js_ck_'+ pcode ).prop('checked', true).trigger('click');
});
// 함수명중복으로 통합 SSJ : 2017-11-24
//// JJC : 전체 선택 or 해제 -- 리스트 상단 버튼에서함수로 제어 --- 상품옵션일괄관리에 대한 처리 : 2017-11-21
//function selectAll(_type){
//	if(_type == 'Y'){
//		$('.js_option_AllCK').prop('checked',true);
//		$('input[type=checkbox].js_ck:not(.disabled)').prop('checked',true); // 각 옵션 데이터 체크 적용
//	}else if(_type == 'N'){
//		$('.js_option_AllCK').removeAttr('checked');
//		$('input[type=checkbox].js_ck').removeAttr('checked'); // 각 옵션 데이터 체크 풀기
//	}
//}




// - Comma 적용 ---
String.prototype.comma=function() { 
	var l_text=this.replace(/,/g,''); 

	if(l_text == "0") return "0";

	var l_pattern=/^(-?\d+)(\d{3})($|\..*$)/; 

	if(l_pattern.test(l_text)){ 
		l_text=l_text.replace(l_pattern,function(str,p1,p2,p3) 
		{ 
		  return p1.comma() + ("," + p2 + p3); 
		}); 
	} 
	return l_text; 
}

// script number_format
function number_format(number, decimals, decPoint, thousandsSep) {

	number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
	var n = !isFinite(+number) ? 0 : +number
	var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
	var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
	var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
	var s = ''

	var toFixedFix = function (n, prec) {
		var k = Math.pow(10, prec)
		return '' + (Math.round(n * k) / k).toFixed(prec)
	}

	// @todo: for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || ''
		s[1] += new Array(prec - s[1].length + 1).join('0')
	}

	return s.join(dec)
}


/* ----- 숫자만 입력받고 천단위 콤마를 삽입한다. ---------- */
$(document).on('focusin', '.number_style', function(){
	$(this).val($(this).val().replace(/,/g,""));
	if($(this).val()*1===0) $(this).val('');
});
$(document).on('focusout', '.number_style', function(){
	var decimals = 0;
	if($(this).data('decimals') != undefined && $(this).data('decimals')*1 > 0) decimals = $(this).data('decimals');
	else if($(this).data('type') == 'float') decimals = $(this).val().toString().split('.').length > 1 ? $(this).val().toString().split('.')[1].length : 0;
	$(this).val(number_format($(this).val(), decimals));
});
$(document).on('keypress', '.number_style', function(){
	// 숫자만 입력
	if( (event.keyCode<48 || event.keyCode>57) && event.keyCode!=45 && event.keyCode!=13 && event.keyCode!=46) {
		event.returnValue=false;
	}
});
$(document).ready(function(){ $('.number_style').trigger('focusout'); });

// 폼전송시 시 number_style에 콤마 제거 // 2017-11-10 SSJ
$(document).on('submit', 'form', remove_comma); 
$('form').submit(remove_comma);
function remove_comma(){
	$('.number_style').trigger('focusin');
}


// 선택회원을 휴면처리
function __sleep($href) {
  if(confirm("선택회원을 휴면처리 하시겠습니까??")) {
    document.location.href = $href;
  }
}
// 삭제
function del($href) {
  if(confirm("정말 삭제하시겠습니까?")) {
    document.location.href = $href;
  }
}
// 취소
function cancel($href) {
  if(confirm("정말 취소하시겠습니까?")) {
    document.location.href = $href;
  }
}


// 포커스 위치에 글자 추가 -> $('.textarea').insertAtCaret('문구문구문구');
jQuery.fn.extend({
	insertAtCaret: function(myValue){
		return this.each(function(i) {
			if (document.selection) {
				//For browsers like Internet Explorer
				this.focus();
				sel = document.selection.createRange();
				sel.text = myValue;
				this.focus();
			}
			else if (this.selectionStart || this.selectionStart == '0') {
				//For browsers like Firefox and Webkit based
				var startPos = this.selectionStart;
				var endPos = this.selectionEnd;
				var scrollTop = this.scrollTop;
				this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
				this.focus();
				this.selectionStart = startPos + myValue.length;
				this.selectionEnd = startPos + myValue.length;
				this.scrollTop = scrollTop;
			}
			else {
				this.value += myValue;
				this.focus();
			}
		})
	}
});


// 등록페이지 플로트 버튼 show/hide
function toggle_fixed_save(){
	var winH = $(window).height();
	var docH = $(document).height();
	var ps = $(window).scrollTop();

	// 플로트 버튼이 사라질 위치 체크 
	var maxScrollTop = docH - winH - 290;

	if(maxScrollTop < ps){
		$('.js_fixed_save').stop().hide();
	}else{
		$('.js_fixed_save').stop().show();
	}
}
$(document).ready(toggle_fixed_save);
$(document).scroll(toggle_fixed_save);

// SSJ : 2017-11-24 쿠키생성
function setCookie(c_name,value,exdays){
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
}
// SSJ : 2017-11-24 쿠키가져오기
function getCookie(c_name){
	var i,x,y,ARRcookies=document.cookie.split(";");
	for (i=0;i<ARRcookies.length;i++){
		x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
		y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
		x=x.replace(/^\s+|\s+$/g,"");
		if (x==c_name){
			return unescape(y);
		}
	}
}