// console >= IE8
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
		if(!console[method]) console[method] = noop;
	}
})();

// localstorage >= IE8
(function() {
	// Union of Chrome, Firefox, IE, Opera, and Safari localStorage methods
	var localStorage_methods = ['setItem', 'getItem', 'removeItem', 'clear'];
	var localStorage_length = localStorage_methods.length;
	var localStorage = (typeof window.localStorage == 'object'?window.localStorage:{});
	var localStorage_method;
	var localStorage_noop = function() {};
	while (localStorage_length--) {
		localStorage_method = localStorage_methods[localStorage_length];
		if(!localStorage[localStorage_method]) localStorage[localStorage_method] = localStorage_noop;
	}
})();;

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function MM_showHideLayers() { //v6.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
    obj.visibility=v; }
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}

function styleFlip(selObj,strClassName){
	selObj.className = strClassName;
}

function dspChange(){
  var j,pj,vj,objSpan,argsj=dspChange.arguments;
  for (j=0; j<(argsj.length-2); j+=3) if ((objSpan=MM_findObj(argsj[j]))!=null) { v=argsj[j+2];
    if (objSpan.style) { objSpan=objSpan.style; v=(v=='show')?'':(v=='hide')?'none':v; }
    objSpan.display=v; }
}

function DisplayDirect(index) {
	for (i=1; i<3; i++){
		if ( i==index ) {
			document.images['img'+index].src="/images/notice_tit_"+index+"_on.gif";
			document.getElementById('direct' + index).style.display='';
			document.getElementById('direct' + index + '_more').style.display='';
		}
		else {
			document.images['img'+i].src="/images/notice_tit_"+i+".gif";
			document.getElementById('direct' + i).style.display='none';
			document.getElementById('direct' + i + '_more').style.display='none';
		}
	}
}


function DisplayDirect2(index) {
	for (i=1; i<4; i++){
		if ( i==index ) {
			document.getElementById('direct' + index).style.display='';
		}
		else {
			document.getElementById('direct' + i).style.display='none';
			eval("document.find_id_form.groups"+i+".checked=false");
		}
	}
}



// ????????? ?????? ??????
function onlyNum() {
	if(((event.keyCode<48)||(event.keyCode>57))&&(event.keyCode!=13)) {
		event.returnValue=false;
	}

	len = event.srcElement.value.length;
	if(len > 1) {
		var ch = event.srcElement.value;
		var isnum = /^\d+$/.test(event.srcElement.value);

		if (isnum == false) {
			alert(ch + " ????????? ?????? ???????????????.");
			event.srcElement.value = "";
            event.returnValue=false;
        }
    }
}

function goto_content_in_submain(contentUrl, leftUrl)
{
	contentUrl = escape(contentUrl);
	var url = "/jumpmain.htm?&left=" + leftUrl + "&content=" + contentUrl;
	try
	{
		top.iflg_body.iflg_main.location.href = url;
	} catch(e) { alert(e);}
}

function Isnum_c(ch) {
    return ((ch > 0));
}

function setLeftPage() {
	var leftPath = getLeftPath();
	if( arguments.length >= 1 ) leftPath = arguments[0];
	var iflg_left = parent.iflg_left;
	try
	{
		if ( iflg_left.location.pathname != leftPath ) {
			iflg_left.location.replace("http://"+location.hostname+leftPath);
		}
	}
	catch(exception)
	{	// left??? ??????????????? ?????? ?????? ??????.
		iflg_left.location.replace("http://"+location.hostname+leftPath);
	}
}

function open_window(name, url, left, top, width, height, toolbar, menubar, statusbar, scrollbar, resizable) {
	toolbar_str = toolbar ? 'yes' : 'no';
	menubar_str = menubar ? 'yes' : 'no';
	statusbar_str = statusbar ? 'yes' : 'no';
	scrollbar_str = scrollbar ? 'yes' : 'no';
	resizable_str = resizable ? 'yes' : 'no';
	window.open(url, name, 'left='+left+',top='+top+',width='+width+',height='+height+',toolbar='+toolbar_str+',menubar='+menubar_str+',status='+statusbar_str+',scrollbars='+scrollbar_str+',resizable='+resizable_str);
}


// ????????????/??????
function chkBox(bool) {
	var obj = document.getElementsByName("chk[]");
	for (var i=0; i<obj.length; i++) {
		obj[i].checked = bool;
	}
}


function __sleep($href) {
  if(confirm("??????????????? ???????????? ????????????????????")) {
    document.location.href = $href;
  }
}

function del($href) {
  if(confirm("?????? ?????????????????????????")) {
    document.location.href = $href;
  }
}
function cancel($href) {
  if(confirm("?????? ?????????????????????????")) {
    document.location.href = $href;
  }
}


function service_send(str) {
	document.frm.action = str + "form.php";
	document.frm.submit();
}

function mainflash(Str1, Str2, Str3){
	document.write('<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'+Str2+'" height="'+Str3+'" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" id=ShockwaveFlash1>'
	+'<param name="movie" value="'+Str1+'">'
	+'<param name="quality" value="high">'
	+'<param name="wmode" value="transparent">'
	+'<embed src="'+Str1+'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" width="'+Str2+'" height="'+Str3+'" type="application/x-shockwave-flash"></embed>'
	+'</object>');
}



function setHyphen(string)
{
	var chk_str = eval("document.frm."+string);
	var str = checkDigit(chk_str.value);
	var retValue = "";
	var len = str.length;

	if (len == 8 || len == 9 || len == 10 || len == 11)
	{
		if (len == 8) {
			retValue = retValue + str.substring(0, 4) + "-" + str.substring(4, 8);
		}
		else if (len == 9) {
			retValue = retValue + str.substring(0, 2) + "-" + str.substring(2, 5) + "-" + str.substring(5, 9);
		}
		else if (len == 10) {
			if( str.substring(0, 2) == "02" ) {
				retValue = retValue + str.substring(0, 2) + "-" + str.substring(2, 6) + "-" + str.substring(6,10);
			}
			else {
				retValue = retValue + str.substring(0, 3) + "-" + str.substring(3, 6) + "-" + str.substring(6, 10);
			}
		}
		else {
			retValue = retValue + str.substring(0, 3) + "-" + str.substring(3, 7) + "-" + str.substring(7, 11);
		}
	}
	else {
		// alert("?????? ?????? ????????? ?????????");
		retValue = str;
	}
	chk_str.value = retValue;
}



function setHyphen_frm(frm, string) {
	var chk_str = eval("document."+frm+"."+string);
	var str = checkDigit(chk_str.value);
	var retValue = "";
	var len = str.length;

	if (len == 8 || len == 9 || len == 10 || len == 11)
	{
		if (len == 8) {
			retValue = retValue + str.substring(0, 4) + "-" + str.substring(4, 8);
		}
		else if (len == 9) {
			retValue = retValue + str.substring(0, 2) + "-" + str.substring(2, 5) + "-" + str.substring(5, 9);
		}
		else if (len == 10) {
			if( str.substring(0, 2) == "02" ) {
				retValue = retValue + str.substring(0, 2) + "-" + str.substring(2, 6) + "-" + str.substring(6,10);
			}
			else {
				retValue = retValue + str.substring(0, 3) + "-" + str.substring(3, 6) + "-" + str.substring(6, 10);
			}
		}
		else {
			retValue = retValue + str.substring(0, 3) + "-" + str.substring(3, 7) + "-" + str.substring(7, 11);
		}
	}
	else {
		// alert("?????? ?????? ????????? ?????????");
		retValue = str;
	}
	chk_str.value = retValue;
}


// ??????????????? ?????? ??? ?????? ????????? ??????????????? ?????????.
function checkDigit(num) {
	var Digit = "1234567890";
	var string = num;
	var len = string.length
	var retVal = "";
	for (i = 0; i < len; i++)
	{
		if (Digit.indexOf(string.substring(i, i+1)) >= 0)
		{
			retVal = retVal + string.substring(i, i+1);
		}
	}
	return retVal;
}


// ??????????????? ???????????? ?????? - ????????? jquery ????????? ????????? ???
function innerHTMLJS(obj,content) {
	// if(typeof(obj) != 'object' && typeof(content) != 'string') return;
	obj = $("#" + obj);

	// avoid IE innerHTML bug
	content = '<body>' + content.replace(/<\/?head>/gi, '')
				.replace(/<\/?html>/gi, '')
				.replace(/<body/gi, '<div')
				.replace(/<\/body/gi, '</div') + '</body>';

	obj.append(content);

	var scripts = obj.attr('script');

	if(scripts == false) return true; // no node script == no problem !

	for(var i=0; i<scripts.length; i++) {
		var scriptclone = document.createElement('script');
		if(scripts[i].attributes.length > 0) { /* boucle de copie des attributs du script dans le nouveau node */
			for(var j in scripts[i].attributes) {
				if(typeof(scripts[i].attributes[j]) != 'undefined'
					&& typeof(scripts[i].attributes[j].nodeName) != 'undefined' /* IE needs it */
					&& scripts[i].attributes[j].nodeValue != null
					&& scripts[i].attributes[j].nodeValue != '' /* IE needs it ou il copie des nodes vides */)
				{
					scriptclone.setAttribute(scripts[i].attributes[j].nodeName, scripts[i].attributes[j].nodeValue);
				}
			}
		}
		scriptclone.text = scripts[i].text; // on copie le corp du script
		/*
			la j'ai pas compris, si je ne return pas sous opera ici : le javascript s'execute 2 fois -
			mais la : le script s'execute mais n'est pas a ce moment la place entre les balises scripts !
			et si je return juste apres le innerHTML, le script n'est pas execute... ---o(<
		*/

		if (navigator.userAgent.indexOf("Opera")>0) { return; }
		/* on force le remplacement du node par dom, qui a pour effet de forcer le parsing du javascript */
		scripts[i].parentNode.replaceChild(scriptclone, scripts[i]);
	}
	return true;
}


// ?????????
function twt_share(title,url) {
	window.open("http://twitter.com/intent/tweet?text=" + encodeURIComponent(title) + " " + encodeURIComponent(url), 'twt_share', '');
}
// ????????????
function me2_share(title,url,tag) {
	window.open("http://me2day.net/posts/new?new_post[body]=" + encodeURIComponent(title) + " " + encodeURIComponent(url) + "&new_post[tags]=" + encodeURIComponent(tag), 'me2_share', '');
}


// - ???????????? ?????? ---
function postToFeedCom(_link , _pic , _name  , _description) {
	// meta tag ??????
	$("link[rel=image_src]").attr("href" , _pic);
	$("meta[name=description]").attr("content" , _description);
	// facebook meta tag ??????
	$('meta[property^=og]').each(function(){
		var app_fbstr = $(this).attr("property");
		if(app_fbstr == "og:title"){$(this).attr("content" , _name);}
		else if(app_fbstr == "og:url"){$(this).attr("content" , _link);}
		else if(app_fbstr == "og:image"){$(this).attr("content" , _pic);}
		else if(app_fbstr == "og:site_name"){$(this).attr("content" , _name);}
		else if(app_fbstr == "og:description"){$(this).attr("content" , _description);}
	});

	// calling the API ...
	var obj = {
		method: 'feed',
		link: _link,
		picture: _pic,
		name: _name,
		description: _description
	};
	function callbackCom(response) {
		document.getElementById('msg').innerHTML = "Post ID: " + response['post_id'];
	}
	FB.ui(obj, callbackCom);
}
// - ???????????? ?????? ---


// - Comma ?????? ---
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
// - Comma ?????? ---



// - input ??? url ?????? ?????? ---
function click_link(field) {
	if($("input[name="+field+"]").val().length == 0 ) {
		alert("url??? ????????? ?????????");
	}
	else {
		window.open('http://' + $("input[name="+field+"]").val().replace("http://", ""), 'click_link', '');
	}
}
// - input ??? url ?????? ?????? ---



// ????????? ??????
function login_alert(pn) {
	if(confirm("????????? ??? ???????????? ????????????.\n\n????????? ???????????? ?????????????????????????")){
		// ????????? ?????????
		if(top != undefined){
			top.location.href='/?pn=member.login.form&_rurl='+pn;
		}else{
			location.href='/?pn=member.login.form&_rurl='+pn;
		}
	}
	//login_view();
}

function Only_Numeric() {
	if((event.keyCode<48 || event.keyCode>57 || event.keyCode==45) && event.keyCode!=13) event.returnValue=false;
}

// ???????????? ?????? ????????? limit ?????? ?????? alert??? ????????? ?????????.
function length_limit(obj,limit) {
   var p, len=0;  // ??????????????? ????????? ??????
   for(p=0; p< obj.value.length; p++) {
	(obj.value.charCodeAt(p)  > 255) ? len+=2 : len++;  // ????????????

	if(len>limit) {
		alert("??????/?????? ?????? " +limit+" ?????? ?????? ????????? ??? ????????????."+getObjectLength(obj));
		obj.value = obj.value.substr(0,p);
		return;
	}
   }

}

// ???????????? ?????? ????????? ?????????.
function getObjectLength(obj) {
   var p, len=0;  // ??????????????? ????????? ??????
   for(p=0; p< obj.value.length; p++)
   {
	(obj.value.charCodeAt(p)  > 255) ? len+=2 : len++;  // ????????????
   }
	return len;
 }

// ??????????????? ??????
function set_start_page(url)
 {
	document.body.style.behavior='url(#default#homepage)';
	document.body.setHomePage('http://'+url);
 }

//$(document).ready(function() {
//
//
//
//	/* ----- ????????? ???????????? ????????? ????????? ????????????. ---------- */
//	$( ".number_style" ).bind( "keypress keyup", function() {
//
//		// ????????? ??????
//		if( (event.keyCode<48 || event.keyCode>57) && event.keyCode!=45 && event.keyCode!=13) {
//			event.returnValue=false;
//		}
//
//		// ????????? ??????
//		this.value = this.value.comma();
//
//	});
//
//
//	obj = $(".number_style");
//
//	if(obj.length > 0)
//		for(var i in obj)
//			if(obj[i].value != undefined)
//				obj[i].value = obj[i].value.comma();
//});


/* ----- ????????? ???????????? ????????? ????????? ????????????. ---------- */
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
	// ????????? ??????
	if( (event.keyCode<48 || event.keyCode>57) && event.keyCode!=45 && event.keyCode!=13 && event.keyCode!=46) {
		event.returnValue=false;
	}
});
$(document).ready(function(){ $('.number_style').trigger('focusout'); });

// ???????????? ??? number_style??? ?????? ?????? // 2017-11-10 SSJ
$(document).on('submit', 'form', remove_comma); 
$('form').submit(remove_comma);
function remove_comma(){
	$('.number_style').trigger('focusin');
}
function number_style_comma_delete(){
	remove_comma();
}


function print_r(obj) {
	var a = "";
	var tmp = 1;
	for(key in obj) {
		if(tmp++ % 10 == 0) {alert(a);a="";}
		a+="`"+key+"` = "+obj[key]+"\n";
	}
	alert(a);
}

// defaultValue ??? ??????
function input_dv_insert() {

	obj = $("input[type=text], textarea");
	for(i=0;i<obj.length;i++) {
		dv = $(obj).eq(i).attr("defaultValue");
		!$(obj).eq(i).val() && dv ? $(obj).eq(i).val(dv) : null;
	}

}

// ????????? defaultValue ??? ??????
function input_dv_delete() {

	obj = $("input[type=text], textarea");
	for(i=0;i<obj.length;i++) {
		dv = $(obj).eq(i).attr("defaultValue");
		$(obj).eq(i).val() == dv ? $(obj).eq(i).val("") : null;
	}

}

//// ????????? ?????? ??????
//function number_style_comma_delete() {
//
//	obj = $(".number_style");
//
//	if(obj.length > 0)
//		for(var i in obj)
//			if(obj[i].value != undefined)
//				obj[i].value = obj[i].value.replace(/,/g,"");
//}

// ??? ???????????? ?????? ?????????
function formSubmitSet() {

	input_dv_delete();
	number_style_comma_delete();

}

function bookmark(title, url) {

	// ????????? ?????? ????????? ?????? ????????? ?????? ??????
	if(!title) { title = document.title; }
	if(!url) { url = location.href; }

	// ?????? ????????????(??????, ?????????)??? ????????????????????? ????????? ???????????? ?????? ???????????? ????????? ??????
	$.browser.chrome = $.browser.webkit && !!window.chrome;
	$.browser.safari = $.browser.webkit && !window.chrome;

	if($.browser.chrome || $.browser.safari) {
		alert('?????? ???????????? ??????????????? ?????? ???????????? ?????? ????????? ???????????? ????????????. \n\n' + (navigator.userAgent.toLowerCase().indexOf('mac') != - 1 ? 'Command/Cmd' : 'CTRL') + ' + D ??? ?????? ?????? ??????????????? ??????????????????.');
	} else {
	    if(window.external) { // ie
	        window.external.AddFavorite(url, title);
	    }
	    else if(window.sidebar) { // firefox
	        window.sidebar.addPanel(title, url, "");
	    }
	    else if(window.opera && window.print) { // opera
	        var elem = document.createElement('a');
	        elem.setAttribute('href',url);
	        elem.setAttribute('title',title);
	        elem.setAttribute('rel','sidebar');
	        elem.click(); // this.title=document.title;
	    }
	}
}

// ?????? ?????? ???????????? ????????????. 2017.1.30 ?????????
// => ???????????? ????????? ????????? ?????? ?????? ????????? ?????? delegate??? ?????? 2017-06-27 :: SSJ
$(document).delegate('.lang_tab_menu' , 'click' , function() {
	var input_name = $(this).data("input");
	var lang_name = $(this).data("lang");

	// ??????????????????
	$(".line_"+input_name).hide();
	$(".line_"+input_name+"_"+lang_name).show();
	$(".line_"+input_name+"_"+lang_name+ " [name="+input_name+"_"+lang_name+"]").focus();
	// ?????????
	$(".tab_"+input_name).removeClass("tab_hit");
	$(".tab_"+input_name+"_"+lang_name).addClass("tab_hit");
});

// ?????? ?????? ???????????? ????????????. 2017.1.30 ?????????
// => ???????????? ????????? ????????? ?????? ?????? ????????? ?????? delegate??? ?????? 2017-06-27 :: SSJ
$(document).delegate('.lang_tab_menu_top' , 'click' , function() {
	var lang_name = $(this).data("lang");

	// ??????????????????
	$(".line_tab").hide();
	$(".line_tab_"+lang_name).show();
	// ?????????
	$(".lang_tab_menu").removeClass("tab_hit");
	$(".lang_tab_menu[data-lang="+lang_name+"]").addClass("tab_hit");
	// ?????????
	$(".lang_tab_menu_top").removeClass("tab_hit");
	$(".lang_tab_menu_top[data-lang="+lang_name+"]").addClass("tab_hit");
});


// jquery UI ?????? ??????
jQuery.curCSS = jQuery.css;

// fix sammy in IE
if($.browser.msie) {
	$("a[href='javascript:;']").on("click", function() { return false; });
}

// class -> array
;!(function ($) {
    $.fn.classes = function (callback) {
        var classes = [];
        $.each(this, function (i, v) {
            var splitClassName = v.className.split(/\s+/);
            for (var j in splitClassName) {
                var className = splitClassName[j];
                if (-1 === classes.indexOf(className)) {
                    classes.push(className);
                }
            }
        });
        if ('function' === typeof callback) {
            for (var i in classes) {
                callback(classes[i]);
            }
        }
        return classes;
    };
})(jQuery);

// ??????????????? ????????? ????????? ??????
function scrolltoClass(Target, top, _root) {

	if(!_root || _root == undefined) _root = $('html, body');
	if($(Target).offset() === undefined) return; // ?????? ???????????? ?????? ??????
	if(!top) top = 10;
	_root.animate({
		scrollTop: $(Target)[0].offsetTop - top
	}, 500, 'easeInOutCubic');
}

// ???????????? ??????
function FindHash() {

	var UrlHash = $(location).attr('hash');
	UrlHash = UrlHash.replace('#none', ''); // ????????? #none ??????
	UrlHash = UrlHash.replace('#', ''); // ????????? # ??????

	return UrlHash;
}

// IE Data ?????? ?????? FIX (??????????????? ?????? yyyy-mm-dd??? ????????????.)
function newDateFix(val) {

	if($.browser.msie) {

		var NewVal = val.split('-');
		return new Date(parseInt(NewVal[0]), parseInt(NewVal[1])-1, parseInt(NewVal[2]));
	}
	else {
		return new Date(val);
	}
}

// php: str_pad(str, pad_length, STR_PAD_LEFT)
function pad(str, max) {

	str = str.toString();
	return str.length < max ? pad("0" + str, max) : str;
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


// ????????????
function favorite() {
	var bookmarkURL = window.location.href;
	var bookmarkTitle = document.title;
	var triggerDefault = false;

	if (window.sidebar && window.sidebar.addPanel) {

		// Firefox version < 23
		window.sidebar.addPanel(bookmarkTitle, bookmarkURL, '');
	} else if ((window.sidebar && (navigator.userAgent.toLowerCase().indexOf('firefox') > -1)) || (window.opera && window.print)) {

		// Firefox version >= 23 and Opera Hotlist
		var $this = $(this);
		$this.attr('href', bookmarkURL);
		$this.attr('title', bookmarkTitle);
		$this.attr('rel', 'sidebar');
		$this.off(e);
		triggerDefault = true;
	} else if (window.external && ('AddFavorite' in window.external)) {

		// IE Favorite
		window.external.AddFavorite(bookmarkURL, bookmarkTitle);
	} else {

		// WebKit - Safari/Chrome
		alert((navigator.userAgent.toLowerCase().indexOf('mac') != -1 ? 'Cmd' : 'Ctrl') + '+D ?????? ?????? ??????????????? ???????????? ??? ????????????.');
	}
	return triggerDefault;
}

// ie ??????
function is_ie() {
	if(navigator.userAgent.toLowerCase().indexOf("chrome") != -1) return false;
	if(navigator.userAgent.toLowerCase().indexOf("msie") != -1) return true;
	if(navigator.userAgent.toLowerCase().indexOf("windows nt") != -1) return true;
	return false;
}

// ???????????? ??????
/*
fun_new: ?????? ????????? ?????? ????????? ????????? ??????
fun_old: ??????????????? ????????? ??? ????????? ??????
parent: ????????? ??????
Tip: fun_new ???????????? return false;??? ?????? ?????? fun_old??? ?????? ?????? ????????????.
*/
function scriptHook(fun_new, fun_old, parent) {

    if(typeof parent == 'undefined') parent = window;
    for(var i in parent) {
        if(parent[i] === fun_old) {

            parent[i] = function() {

                var Return = fun_new();
                if(Return === false) return;
                return fun_old.apply(this, arguments);
            }
            break;
        }
    }
}



// ??????&????????? css?????? ??????
/*
	$(ele).cssBefore('width', 200); // ?????? ??????????????? ????????? ????????? ??????
	$(ele).cssAfter('width', 200); // ?????? ??????????????? ???????????? ????????? ??????
*/
(function() {
  $.pseudoElements = {
    length: 0
  };

  var setPseudoElement = function(parameters) {
    if (typeof parameters.argument === 'object' || (parameters.argument !== undefined && parameters.property !== undefined)) {
      for (var element in parameters.elements.get()) {
        if (!element.pseudoElements) element.pseudoElements = {
          styleSheet: null,
          before: {
            index: null,
            properties: null
          },
          after: {
            index: null,
            properties: null
          },
          id: null
        };

        var selector = (function() {
          if (element.pseudoElements.id !== null) {
            if (Number(element.getAttribute('data-pe--id')) !== element.pseudoElements.id) element.setAttribute('data-pe--id', element.pseudoElements.id);
            return '[data-pe--id="' + element.pseudoElements.id + '"]::' + parameters.pseudoElement;
          } else {
            var id = $.pseudoElements.length;
            $.pseudoElements.length++

              element.pseudoElements.id = id;
            element.setAttribute('data-pe--id', id);

            return '[data-pe--id="' + id + '"]::' + parameters.pseudoElement;
          };
        })();

        if (!element.pseudoElements.styleSheet) {
          if (document.styleSheets[0]) {
            element.pseudoElements.styleSheet = document.styleSheets[0];
          } else {
            var styleSheet = document.createElement('style');

            document.head.appendChild(styleSheet);
            element.pseudoElements.styleSheet = styleSheet.sheet;
          };
        };

        if (element.pseudoElements[parameters.pseudoElement].properties && element.pseudoElements[parameters.pseudoElement].index) {
          element.pseudoElements.styleSheet.deleteRule(element.pseudoElements[parameters.pseudoElement].index);
        };

        if (typeof parameters.argument === 'object') {
          parameters.argument = $.extend({}, parameters.argument);

          if (!element.pseudoElements[parameters.pseudoElement].properties && !element.pseudoElements[parameters.pseudoElement].index) {
            var newIndex = element.pseudoElements.styleSheet.rules.length || element.pseudoElements.styleSheet.cssRules.length || element.pseudoElements.styleSheet.length;

            element.pseudoElements[parameters.pseudoElement].index = newIndex;
            element.pseudoElements[parameters.pseudoElement].properties = parameters.argument;
          };

          var properties = '';

          for (var property in parameters.argument) {
            if (typeof parameters.argument[property] === 'function')
              element.pseudoElements[parameters.pseudoElement].properties[property] = parameters.argument[property]();
            else
              element.pseudoElements[parameters.pseudoElement].properties[property] = parameters.argument[property];
          };

          for (var property in element.pseudoElements[parameters.pseudoElement].properties) {
            properties += property + ': ' + element.pseudoElements[parameters.pseudoElement].properties[property] + ' !important; ';
          };

          element.pseudoElements.styleSheet.addRule(selector, properties, element.pseudoElements[parameters.pseudoElement].index);
        } else if (parameters.argument !== undefined && parameters.property !== undefined) {
          if (!element.pseudoElements[parameters.pseudoElement].properties && !element.pseudoElements[parameters.pseudoElement].index) {
            var newIndex = element.pseudoElements.styleSheet.rules.length || element.pseudoElements.styleSheet.cssRules.length || element.pseudoElements.styleSheet.length;

            element.pseudoElements[parameters.pseudoElement].index = newIndex;
            element.pseudoElements[parameters.pseudoElement].properties = {};
          };

          if (typeof parameters.property === 'function')
            element.pseudoElements[parameters.pseudoElement].properties[parameters.argument] = parameters.property();
          else
            element.pseudoElements[parameters.pseudoElement].properties[parameters.argument] = parameters.property;

          var properties = '';

          for (var property in element.pseudoElements[parameters.pseudoElement].properties) {
            properties += property + ': ' + element.pseudoElements[parameters.pseudoElement].properties[property] + ' !important; ';
          };

          element.pseudoElements.styleSheet.addRule(selector, properties, element.pseudoElements[parameters.pseudoElement].index);
        };
      };

      return $(parameters.elements);
    } else if (parameters.argument !== undefined && parameters.property === undefined) {
      var element = $(parameters.elements).get(0);

      var windowStyle = window.getComputedStyle(
        element, '::' + parameters.pseudoElement
      ).getPropertyValue(parameters.argument);

      if (element.pseudoElements) {
        return $(parameters.elements).get(0).pseudoElements[parameters.pseudoElement].properties[parameters.argument] || windowStyle;
      } else {
        return windowStyle || null;
      };
    } else {
      console.error('Invalid values!');
      return false;
    };
  };

  $.fn.cssBefore = function(argument, property) {
    return setPseudoElement({
      elements: this,
      pseudoElement: 'before',
      argument: argument,
      property: property
    });
  };
  $.fn.cssAfter = function(argument, property) {
    return setPseudoElement({
      elements: this,
      pseudoElement: 'after',
      argument: argument,
      property: property
    });
  };
})();