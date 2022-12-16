

// *** 익스플로서에서 this.type='password' 적용안되는 오류 처리 js  ***


// 참조 : http://www.dynamicsitesolutions.com/javascript/change-input-type-dynamically/
// Note: I recommend you use a more comprehensive event management script for
// production (aka "live") pages.
// Dean Edwards' event manipulation functions is a good example. You can find
// them here: http://dean.edwards.name/weblog/2005/10/add-event2/
var LoadHandler = {
  handlers:[],
  add:function(fn){
    if(window.onload!=LoadHandler.theHandler) LoadHandler._push(window.onload);
    LoadHandler._push(fn);
    window.onload=LoadHandler.theHandler;
  },
  _push:function(fn){
    if(typeof(fn)!='function') return;
    LoadHandler.handlers[LoadHandler.handlers.length]=fn;
  },
  theHandler:function(){
    var handlers=LoadHandler.handlers,i=-1,fn;
    while(fn=handlers[++i]) fn();
  }
}

// this is to prevent the form from submitting in this demo
// LoadHandler.add(function(){  document.frm_login_page.onsubmit=function(){return false;}});

function changeInputType(
  oldElm, // a reference to the input element
  iType, // value of the type property: 'text' or 'password'
  iValue, // the default value, set to 'password' in the demo
  blankValue, // true if the value should be empty, false otherwise
  noFocus) {  // set to true if the element should not be given focus
  if(!oldElm || !oldElm.parentNode || (iType.length<4) || 
    !document.getElementById || !document.createElement) return;
  var isMSIE=/*@cc_on!@*/false; //http://dean.edwards.name/weblog/2007/03/sniff/
  if(!isMSIE){
    var newElm=document.createElement('input');
    newElm.type=iType;
  } else {
    var newElm=document.createElement('span');
    newElm.innerHTML='<input type="'+iType+'" name="'+oldElm.name+'">';
    newElm=newElm.firstChild;
  }
  var props=['name','id','className','size','tabIndex','accessKey'];
  for(var i=0,l=props.length;i<l;i++){
    if(oldElm[props[i]]) newElm[props[i]]=oldElm[props[i]];
  }
  newElm.onfocus=function(){return function(){
    if(this.hasFocus) return;
    var newElm=changeInputType(this,'password',iValue,
      (this.value.toLowerCase()==iValue.toLowerCase())?true:false);
    if(newElm) newElm.hasFocus=true;
  }}();
  newElm.onblur=function(){return function(){
    if(this.hasFocus)
    if(this.value=='' || (this.value.toLowerCase()==iValue.toLowerCase())) {
      changeInputType(this,'text',iValue,false,true);
    }
  }}();
 // hasFocus is to prevent a loop where onfocus is triggered over and over again
  newElm.hasFocus=false;
  // some browsers need the value set before the element is added to the page
  // while others need it set after
  if(!blankValue) newElm.value=iValue;
  oldElm.parentNode.replaceChild(newElm,oldElm);
  if(!isMSIE && !blankValue) newElm.value=iValue;
  if(!noFocus || typeof(noFocus)=='undefined') {
    window.tempElm=newElm;
    setTimeout("tempElm.hasFocus=true;tempElm.focus();",1);
  }
  return newElm;
}

LoadHandler.add(function(){
  // Normally I use object detection, however, in this case since I need to 
  // detect Konqueror and Safari which don't have unique objects,
  // I will use the user agent string to detect them. Only use this type of 
  // detection as a last resort.
  // I'm doing this because example 2 crashes Konqueror 3.4 and Safari 1.0

  var ua=navigator.userAgent.toLowerCase();
  if(!((ua.indexOf('konqueror')!=-1) && /khtml\/3\.[0-4]/.test(ua)) && 
    !(((ua.indexOf('safari')!=-1) && !window.print))) {

      // Set the third value to the text you want to appear in the field.
      changeInputType(document.frm._pw,'text','PASSWORD',false,true);
  }
});