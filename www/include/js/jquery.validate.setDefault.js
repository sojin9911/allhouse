/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: KO (Korean; 한국어)
 */
$.extend( $.validator.messages, {
	required: $.validator.format("{name} 필수 항목입니다."),
	remote: "항목을 수정하세요.",
	email: "{name+은는} 유효하지 않은 E-Mail주소입니다.",
	url: "유효하지 않은 URL입니다.",
	date: "올바른 날짜를 입력하세요.",
	dateISO: "올바른 날짜(ISO)를 입력하세요.",
	number: "유효한 숫자가 아닙니다.",
	digits: "숫자만 입력 가능합니다.",
	creditcard: "신용카드 번호가 바르지 않습니다.",
	equalTo: "같은 값을 다시 입력하세요.",
	extension: "올바른 확장자가 아닙니다.",
	maxlength: $.validator.format( "{0}자를 넘을 수 없습니다. " ),
	minlength: $.validator.format( "{0}자 이상 입력하세요." ),
	rangelength: $.validator.format( "문자 길이가 {0} 에서 {1} 사이의 값을 입력하세요." ),
	range: $.validator.format( "{0} 에서 {1} 사이의 값을 입력하세요." ),
	max: $.validator.format( "{0} 이하의 값을 입력하세요." ),
	min: $.validator.format( "{0} 이상의 값을 입력하세요." )
} );

/* ---------- jquery validator 경고창 띄우기 (jquery validate 공통) ---------- */
/*
jQuery.validator.setDefaults({
	onkeyup:false,
	onclick:false,
	onfocusout:false,
	showErrors:function(errorMap, errorList){
		if(errorList != "") {	// 에러가 있을때만 alert 호출
			alert(errorList[0].message);
			$("input[name='"+$(errorList[0].element).attr('name')+"']").focus();
		}
	}
});
*/
jQuery.validator.setDefaults({
	onkeyup:false,
	onclick:false,
	onfocusout:false,
	showErrors:function(errorMap, errorList){
		if(errorList != "") {	// 에러가 있을때만 alert 호출

			var msg = errorList[0].message;
			var atName = $(errorList[0].element).attr('name');
			if(msg == 'This field is required.') msg = '필수 항목값이 누락 되었습니다.';

			alert(msg);
			try { $("input[name='"+atName+"']").focus(); }
			catch (e) { /* 네임이 배열형태인 경우 특정 기기에서 에러가 발생 방지 */ }
		}
	}
});
/* ---------- // jquery validator 경고창 띄우기 (jquery validate 공통) ---------- */


// 커스텀 룰 추가 -------------------------------------------------------------------------
// - 한글만 입력
jQuery.validator.addMethod("hangul", function(value, element) {
	var pattern = /[a-z0-9]|[ \[\]{}()<>?|`~!@#$%^&*-_+=,.;:\"'\\]/ig;
	var checked = pattern.test(value);
	var new_value = (checked === true?value.replace(pattern, ''):value);
	return this.optional(element) || (new_value == value?true:false);
}, "한글만 입력 가능합니다.");

// - 휴대폰 검증
jQuery.validator.addMethod("htel", function(value, element) {
	var pattern = /(01[016789]{1}|02|0[3-9]{1}[0-9]{1})-?[0-9]{3,4}-?[0-9]{4}/ig;
	return this.optional(element) || pattern.test(value);
}, "휴대폰 번호 양식이 올바르지 않습니다. 입력 가능합니다.");