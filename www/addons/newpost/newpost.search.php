<?php if($_SERVER['HTTPS']) { ?>
<script src="//spi.maps.daum.net/imap/map_js_init/postcode.v2.js"></script>
<?php } else { ?>
<script src="//dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<?php } ?>
<script>
// 도로명주소 우편번호 열기
function new_post_view(){
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
			// fullRoadAddr가 없 는경우 예상 도로 주소를 마킹한다.
			if(fullRoadAddr == ''){ fullRoadAddr = data.autoRoadAddress; }

			// 지번주소가 없을 경우 도로명주소로 대체한다.
            if( data.jibunAddress == '' && fullRoadAddr != '' ) {
                if(data.autoJibunAddress) data.jibunAddress = data.autoJibunAddress;
                else data.jibunAddress = fullRoadAddr;
            }

		
			// 우편번호와 주소 정보를 해당 필드에 넣는다.
			document.getElementById("_zonecode").value = data.zonecode;
			document.getElementById("_post1").value = data.postcode1;
			document.getElementById("_post2").value = data.postcode2;
			document.getElementById("_addr_doro").value = fullRoadAddr;
			document.getElementById("_addr1").value = data.jibunAddress;
			document.getElementById("_addr2").focus();

		}
	}).open();
}


</script>