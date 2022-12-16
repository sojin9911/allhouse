
	// --- 검색항목 열기 ---
	function view_search() {
		patt = /block/g;
		if(patt.test($(".cm_search_form").css("display"))) {
			$(".cm_search_form").slideUp(300);
			$('.page_top_area').addClass('if_closed');
			$("input[name='search_type']").val("close");//검색형태(open , close)
		}
		else {
			$(".cm_search_form").slideDown(300);
			$('.page_top_area').removeClass('if_closed');
			$("input[name='search_type']").val("open");//검색형태(open , close)
		}
	}

	// --- 삭제 확인 ---
	function del($href) {
		if(confirm("정말 삭제하시겠습니까?")) {
			document.location.href = $href;
		}
	}