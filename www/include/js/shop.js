// 목록에서 구매/장바구니 담기
function app_submit_from_list(pcode,_type,is_option) {
	if(is_option) {
		alert("옵션이 있는 상품입니다. 옵션을 선택해주시기 바랍니다.");
		location.href = '/?pn=product.view&pcode='+pcode;
	}
	else {
		location.href = '/program/shop.cart.pro.php?mode=add&pcode='+pcode+'&pass_type=' + _type + '&option_select_type=nooption';
	}
}

// - 상세페이지에서 구매/장바구니 담기
function app_submit(pcode,_type) {
	if( !( $("#option_select_cnt").val() * 1 > 0 ) ) {
		alert("옵션을 하나 이상 선택해주시기 바랍니다.")
	}
	else if( !( $("#option_select_expricesum").val() * 1 > 0 ) ) {
		alert("총 주문금액은 1,000원 이상이어야 합니다.")
	}
	else {
		if($('#add_option_select_1_id').val()) { var add_option_select_1 = $('#add_option_select_1_id').val(); } else { var add_option_select_1 = ''; }
		if($('#add_option_select_2_id').val()) { var add_option_select_2 = $('#add_option_select_2_id').val(); } else { var add_option_select_2 = ''; }
		if($('#add_option_select_3_id').val()) { var add_option_select_3 = $('#add_option_select_3_id').val(); } else { var add_option_select_3 = ''; }
		if($('#add_option_select_4_id').val()) { var add_option_select_4 = $('#add_option_select_4_id').val(); } else { var add_option_select_4 = ''; }
		if($('#add_option_select_5_id').val()) { var add_option_select_5 = $('#add_option_select_5_id').val(); } else { var add_option_select_5 = ''; }
		if($('#add_option_select_6_id').val()) { var add_option_select_6 = $('#add_option_select_6_id').val(); } else { var add_option_select_6 = ''; }
		if($('#add_option_select_7_id').val()) { var add_option_select_7 = $('#add_option_select_7_id').val(); } else { var add_option_select_7 = ''; }
		if($('#add_option_select_8_id').val()) { var add_option_select_8 = $('#add_option_select_8_id').val(); } else { var add_option_select_8 = ''; }
		if($('#add_option_select_9_id').val()) { var add_option_select_9 = $('#add_option_select_9_id').val(); } else { var add_option_select_9 = ''; }
		if($('#add_option_select_10_id').val()) { var add_option_select_10 = $('#add_option_select_10_id').val(); } else { var add_option_select_10 = ''; }
		// JJC : 뒤로가기 문제 해결을 위해 임시 프레임 생성 : 2019-02-18
		if($('iframe[name=common_frame_tmp]').length == 0) $('body').append('<iframe name="common_frame_tmp" width="150" height="150" frameborder="0" style="display:none;"></iframe>');
		common_frame_tmp.location.href = ('/program/shop.cart.pro.php?mode=add&pcode='+pcode+'&pass_type=' + _type  + '&add_option_select_1=' + add_option_select_1 + '&add_option_select_2=' + add_option_select_2 + '&add_option_select_3=' + add_option_select_3 + '&add_option_select_4=' + add_option_select_4 + '&add_option_select_5=' + add_option_select_5 + '&add_option_select_6=' + add_option_select_6 + '&add_option_select_7=' + add_option_select_7 + '&add_option_select_8=' + add_option_select_8 + '&add_option_select_9=' + add_option_select_9 + '&add_option_select_10=' + add_option_select_10 + '&option_select_type=' + $("#option_select_type").val() + '&option_select_cnt=' + $("#option_select_cnt").val()); // cart 처리페이지 이동
	}
}




function app_soldout() {
	alert("품절된 상품입니다.");
}

// 검색 확인 버튼
function search_submit() {
	frm = document.search_frm;
	if(frm.search_word.value == "" || frm.search_word.value == "상품을 검색하세요") {
		alert('검색할 상품명을 입력하세요');
		return;
	}
	frm.submit();
}

// 바로검색 기능.
function direct_search(search_word) {
	frm = document.search_frm;
	frm.search_word.value = search_word;
	frm.submit();
}

// 카트에 담긴 상품 갯수를 페이지 전체적으로 적용한다.
function glb_cart_cnt_update(cnt, view_mode) {
	if(view_mode != "show") {
		if(cnt*1 > 0) $(".glb_cart_cnt, .js_cart_cnt").show();
		else $(".glb_cart_cnt, .js_cart_cnt").hide();
	}

	$(".glb_cart_cnt, .js_cart_cnt").text(cnt);
}
