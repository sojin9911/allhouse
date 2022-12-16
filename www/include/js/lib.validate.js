////////////////////////////////////////////////////////////////////////
/*********************************************
* 파일명: lib.validate.js
* 기능: 유연한 자동 폼 검사기
* 만든이: 거친마루 <comfuture@maniacamp.com>
* 날짜: 2002-10-01
* == change log ==
* 2003-10-02 여러칸으로 나눠진 항목에 대한 검사기능 추가
* 2003-10-02 패스워드등 두개 항목에 대한 비교 기능 추가
**********************************************/

var FldDefaultColor;
var FildBackColor;
/// 에러메시지 포멧 정의 ///
var NO_BLANK = "{name+은는} 필수항목입니다";
var NOT_VALID = "{name+이가} 올바르지 않습니다";
// var TOO_LONG = "{name}의 길이가 초과되었습니다 (최대 {maxbyte}바이트)";

/// 스트링 객체에 메소드 추가 ///
String.prototype.trim = function(str) { 
    str = this != window ? this : str; 
    return str.replace(/^\s+/g,'').replace(/\s+$/g,''); 
}

String.prototype.hasFinalConsonant = function(str) {
    str = this != window ? this : str; 
    var strTemp = str.substr(str.length-1);
    return ((strTemp.charCodeAt(0)-16)%28!=0);
}

String.prototype.bytes = function(str) {
    str = this != window ? this : str;
    for(j=0; j<str.length; j++) {
        var chr = str.charAt(j);
        len += (chr.charCodeAt() > 128) ? 2 : 1
    }
    return len;
}



function validate() {
    for (i = 0; i < this.elements.length; i++ ) {
        var el = this.elements[i];
        if (el.tagName == "FIELDSET") continue;
        el.value = el.value.trim();

        var minbyte = el.getAttribute("MINBYTE");
        var maxbyte = el.getAttribute("MAXBYTE");
        var option = el.getAttribute("OPTION");
        var match = el.getAttribute("MATCH");
        var glue = el.getAttribute('GLUE');
        var PATTERN = el.getAttribute('PATTERN');

        if (el.getAttribute("REQUIRED") != null) {
            if (el.type.toLowerCase() == "radio" || el.type.toLowerCase() == "checkbox")
            {
                if(!chkRadio(this,el)) return doError(el,NO_BLANK);
            }
            if (el.value == null || el.value == "") {
                return doError(el,NO_BLANK);
            }
        }

        if (PATTERN != null && el.value != "") {
            if (!PATTERN(el,pattern)) return false;
        }

        if (minbyte != null) {
            if (el.value.bytes() < parseInt(minbyte)) {
                return doError(el,"{name+은는} 최소 "+minbyte+"바이트 이상 입력해야 합니다.");
            }
        }

        if (maxbyte != null && el.value != "") {
            var len = 0;
            if (el.value.bytes() > parseInt(maxbyte)) {
                return doError(el,"{name}의 길이가 초과되었습니다 (최대 "+maxbyte+"바이트)");
            }
        }

        if (match && (el.value != form.elements[match].value)) return doError(el,"{name+이가} 일치하지 않습니다");

        if (funcs[option] && option != null && el.value != "") {
            if (el.getAttribute('SPAN') != null) {
                var _value = new Array();
                for (span=0; span<el.getAttribute('SPAN');span++ ) {
                    _value[span] = this.elements[i+span].value;
                }
                var value = _value.join(glue == null ? '' : glue);
                if (!funcs[option](el,value)) return false;
            } else {
                if (!funcs[option](el)) return false;
            }
        }


    }
    return true;
}

function josa(str,tail) {
    return (str.hasFinalConsonant()) ? tail.substring(0,1) : tail.substring(1,2);
}

function doError(el,type,action) {
    var pattern = /{([a-zA-Z0-9_]+)\+?([가-힝]{2})?}/;
    var name = (hname = el.getAttribute("HNAME")) ? hname : el.getAttribute("NAME");
    pattern.exec(type);
    var tail = (RegExp.$2) ? josa(eval(RegExp.$1),RegExp.$2) : "";

    var error = (herror = el.getAttribute("HERROR")) ? herror : type.replace(pattern,eval(RegExp.$1) + tail);

    alert(error);

    if(el.getAttribute("SELECT") != null) el.select();
    if(el.getAttribute("DELETE") != null) el.value = "";
    if(el.getAttribute("NOFOCUS") == null) el.focus();
    return false;
}    
function chkRadio(str){
    for (j=0;j<str.length;j++) {
        if (str[j].checked) return true
    }
    return false;    
} 
/// 특수 패턴 검사 함수 매핑 ///
var funcs = new Array();
funcs['email'] = isValidEmail;
funcs['phone'] = isValidPhone;
funcs['userid'] = isValidUserid;
funcs['hangul'] = hasHangul;
funcs['number'] = isNumeric;
funcs['engonly'] = alphaOnly;
funcs['jumin'] = isValidJumin;
funcs['bizno'] = isValidBizNo;

/// 패턴 검사 함수들 ///
function PATTERN(el,pattern) {
    pattern = eval("/"+pattern+"$/")
    return (pattern.test(el.value)) ? true : doError(el,"{name+은는} 형식에 맞지 않습니다.");
}

function isValidEmail(el,value) {
    var value = value ? value : el.value;
    var pattern = /^[_a-zA-Z0-9-\.]+@[\.a-zA-Z0-9-]+\.[a-zA-Z]+$/;
    return (pattern.test(value)) ? true : doError(el,NOT_VALID);
}

function isValidUserid(el) {
    var pattern = /^[a-zA-Z]{1}[a-zA-Z0-9_]{5,12}$/;
    return (pattern.test(el.value)) ? true : doError(el,"{name+은는} 6자이상 12자 이하이어야 하며,\n\n영문,숫자, _ 문자만 사용할 수 있습니다.\n\n첫글자는 반드시 영문이어야 합니다");
}

function hasHangul(el) {
    var pattern = /[가-힝]/;
    return (pattern.test(el.value)) ? true : doError(el,"{name+은는} 반드시 한글을 포함해야 합니다");
}

function alphaOnly(el) {
    var pattern = /^[a-zA-Z]+$/;
    return (pattern.test(el.value)) ? true : doError(el,NOT_VALID);
}

function isNumeric(el) {
    var pattern = /^[0-9]+$/;
    return (pattern.test(el.value)) ? true : doError(el,"{name+은는} 반드시 숫자로만 입력해야 합니다");
}

function isValidJumin(el,value) {
var pattern = /^([0-9]{6})-?([0-9]{7})$/; 
    var num = value ? value : el.value;
if (!pattern.test(num)) return doError(el,NOT_VALID); 
num = RegExp.$1 + RegExp.$2;

    var sum = 0;
    var last = num.charCodeAt(12) - 0x30;
    var bases = "234567892345";
    for (var i=0; i<12; i++) {
        if (isNaN(num.substring(i,i+1))) return doError(el,NOT_VALID);
        sum += (num.charCodeAt(i) - 0x30) * (bases.charCodeAt(i) - 0x30);
    }
    var mod = sum % 11;
    return ((11 - mod) % 10 == last) ? true : doError(el,NOT_VALID);
}

function isValidBizNo(el, value) { 
var pattern = /([0-9]{3})-?([0-9]{2})-?([0-9]{5})/; 
    var num = value ? value : el.value;
if (!pattern.test(num)) return doError(el,NOT_VALID); 
num = RegExp.$1 + RegExp.$2 + RegExp.$3;
var cVal = 0; 
for (var i=0; i<8; i++) { 
var cKeyNum = parseInt(((_tmp = i % 3) == 0) ? 1 : ( _tmp == 1 ) ? 3 : 7); 
cVal += (parseFloat(num.substring(i,i+1)) * cKeyNum) % 10; 
} 
var li_temp = parseFloat(num.substring(i,i+1)) * 5 + '0'; 
cVal += parseFloat(li_temp.substring(0,1)) + parseFloat(li_temp.substring(1,2)); 
return (parseInt(num.substring(9,10)) == 10-(cVal % 10)%10) ? true : doError(el,NOT_VALID); 
}

function isValidPhone(el,value) {
    var pattern = /^([0]{1}[0-9]{1,2})-?([1-9]{1}[0-9]{2,3})-?([0-9]{4})$/;
    var num = value ? value : el.value;
    if (pattern.exec(num)) {
        if(RegExp.$1 == "011" || RegExp.$1 == "016" || RegExp.$1 == "017" || RegExp.$1 == "018" || RegExp.$1 == "019") {
            if (!el.getAttribute('SPAN')) el.value = RegExp.$1 + "-" + RegExp.$2 + "-" + RegExp.$3;
        }
        return true;
    } else {
        return doError(el,NOT_VALID);
    }
}

var init_true;

function Initialized()
{
    init_true = true;

    for (var i = 0; i < document.forms.length; i++) {
// onsubmit 이벤트가 있다면 저장해 놓는다.
if (document.forms[i].onsubmit) document.forms[i].oldsubmit = document.forms[i].onsubmit;
            document.forms[i].onsubmit = validate;
for (var j = 0; j < document.forms[i].elements.length; j++) {
// 필수 입력일 경우는 * 배경이미지를 준다.
//            document.forms[i].elements[j].style.backgroundColor = FldDefaultColor ? FldDefaultColor : 'white';

if (document.forms[i].elements[j].getAttribute("REQUIRED") != null) {
                if (document.forms[i].elements[j].getAttribute("NOCOLOR") == null) {
                    document.forms[i].elements[j].style.backgroundColor = FildBackColor	//? FildBackColor : '#FCE0EF';
//                    document.forms[i].elements[j].className = "required";
//                    document.forms[i].elements[j].style.backgroundPosition = "top right";
//                    document.forms[i].elements[j].style.backgroundRepeat = "no-repeat";
                }
}
}
}
}



window.onload = Initialized;

///////////////////// 스크립트 끝 /////////////////////////////////