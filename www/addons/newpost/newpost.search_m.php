<!-- 디자인추가 2019-09-17 ARA -->
<style type="text/css">
    .post_popup_section .post_close_btn {position:fixed; left:0; bottom:0; width:100%; text-align:center;  box-sizing:border-box; padding:10px; z-index:50; background:#fff; box-shadow:0 -3px 3px rgba(0,0,0,0.1);}
    .post_popup_section .post_close_btn strong {display:block; color:#fff; height:45px; line-height:45px; font-size:14px; background:#333; }
    .post_popup_section iframe {margin-bottom:65px !important;}
</style>
<div id="find_postcode" class="post_popup_section" style="display:none;border:0;width:100%;">
<a href= "#none" id="btnFoldWrap" onclick="foldDaumPostcode(); return false;" class="post_close_btn" style="" ><strong>닫기</strong></a>
</div>
<?php if($_SERVER['HTTPS']) { ?>
<script src="//spi.maps.daum.net/imap/map_js_init/postcode.v2.js"></script>
<?php } else { ?>
<script src="//dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<?php } ?>
<script>
// 우편번호 찾기 찾기 화면을 넣을 element
var element_wrap = document.getElementById('find_postcode');

function foldDaumPostcode() {
    // iframe을 넣은 element를 안보이게 한다.
    element_wrap.style.display = 'none';
    $(".post_hide_section").show();
}

// 도로명주소 우편번호 열기
function post_popup_show() {
    // 현재 scroll 위치를 저장해놓는다.
    var currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);

    element_wrap.style.height = getCurrentInnerHeight();

    // iframe을 넣은 element를 보이게 한다.
    document.body.appendChild(element_wrap);
    element_wrap.style.display = 'block';

    $(".post_hide_section").hide();
    //document.getElementById("region_name").focus();
    document.body.scrollTop = 0;




    new daum.Postcode({
        oncomplete: function(data) {
            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

            // 도로명 주소의 노출 규칙에 따라 주소를 조합한다.
            // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
            var fullRoadAddr = data.roadAddress; // 도로명 주소 변수
            var extraRoadAddr = ''; // 도로명 조합형 주소 변수

            // 법정동명이 있을 경우 추가한다.
            if(data.bname !== ''){ extraRoadAddr += data.bname; }
            // 건물명이 있을 경우 추가한다.
            if(data.buildingName !== ''){ extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName); }
            // 도로명, 지번 조합형 주소가 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
            if(extraRoadAddr !== ''){ extraRoadAddr = ' (' + extraRoadAddr + ')'; }
            // 도로명, 지번 주소의 유무에 따라 해당 조합형 주소를 추가한다.
            if(fullRoadAddr !== ''){ fullRoadAddr += extraRoadAddr; }
            // fullRoadAddr가 없는 경우 예상 도로 주소를 마킹한다.
            if(fullRoadAddr == ''){ fullRoadAddr = data.autoRoadAddress; }

            // 지번주소가 없을 경우 도로명주소로 대체한다.
            if( data.jibunAddress == '' && fullRoadAddr != '' ) {
                if(data.autoJibunAddress) data.jibunAddress = data.autoJibunAddress;
                else data.jibunAddress = fullRoadAddr;
            }

            // 우편번호와 주소 및 영문주소 정보를 해당 필드에 넣는다.
            document.getElementById("_zonecode").value = data.zonecode;
            document.getElementById("_post1").value = data.postcode1;
            document.getElementById("_post2").value = data.postcode2;
            document.getElementById("_addr_doro").value = fullRoadAddr;
            document.getElementById("_addr1").value = data.jibunAddress;

            // iframe을 넣은 element를 안보이게 한다.
            foldDaumPostcode();
            document.getElementById("_addr2").focus();

            // 우편번호 찾기 화면이 보이기 이전으로 scroll 위치를 되돌린다.
            document.body.scrollTop = currentScroll;

        },

        // 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분. iframe을 넣은 element의 높이값을 조정한다.
        onresize : function(size) {

            element_wrap.style.height = getCurrentInnerHeight();
        },
        width : '100%',
        height : '100%'
    }).embed(element_wrap);


    // 아이폰이 아닐경우 스크롤이 두개여서 하나를 지우기 위함
    if( navigator.userAgent.indexOf("iPhone") < 1 ){
        var parent_el = document.getElementById("find_postcode");
        parent_el.lastElementChild.style.overflowY = "hidden";
    }



}

window.addEventListener('resize', function() {
  element_wrap.style.height = getCurrentInnerHeight();
}, true);

// 현재 높이값 구하기.
function getCurrentInnerHeight(){
    var close_btn_el = document.getElementById("btnFoldWrap");

    var cur_height = 0;
    // 아이폰이 아닐경우 닫기 버튼 빼기
    if( navigator.userAgent.indexOf("iPhone") < 1 ){
        cur_height = window.innerHeight - close_btn_el.scrollHeight;
    }else{
        cur_height = window.innerHeight ;
    }

    return cur_height + "px";
}

function new_post_view() {
    post_popup_show();
}
</script>