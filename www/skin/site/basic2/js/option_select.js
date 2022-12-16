    // - 옵션 선택 체크 ---
    function option_select(depth,code,k_idx,poname) {

		//// 추가옵션 패치 2014-03-24
		//if( depth == "1" ) {
		//	var tmp_addoption_cnt = $("input[name=addoption_cnt]").val(); // 추가옵션 수량
		//	var tmp_cnt = 0; // 체크된 추가옵션 수량
		//	$(".add_option_chk").each(function(index){
		//		if( $(this).val() ) {
		//			tmp_cnt ++;
		//		}
		//	});
		//	if( tmp_addoption_cnt != tmp_cnt ){ // 추가옵션수량 만큼 선택
		//		alert("앞선 옵션을 선택해주세요.");
		//		return false;
		//	}
		//}
		//// 추가옵션 패치 끝

        var depth_next = depth * 1 + 1;
        var select_var = $("#option_select"+depth+"_id").val();
        var app_var = "";
        if( $("input, select").is("#option_select1_id") == true ){
            app_var = "&uid1=" + $("#option_select1_id").val() ;
        }
        if(select_var) {
            $.ajax({
                url: "/program/option_select.php",
                cache: false,
                type: "POST",
                data: "code="+code+"&depth="+depth+"&poname="+poname+"&uid=" + select_var + app_var ,
                success: function(data){
//                    $("#span_option" + depth_next ).html(data).removeClass('before');
                    $(".span_option" + depth_next  +"_"+k_idx).html(data).removeClass('before');
                    //select_box_reload();    // hover를 동작하게 하기 위해 다시한번 호출
                }
            });
        }
    }
    // - 옵션 선택 체크 ---


    // - 옵션 추가 ---
    function option_select_add(code) {
        var _trigger = true;
        if( $("input, select").is("#option_select1_id") == true && !($("#option_select1_id").val() != "") ){
            _trigger = false;
        }
        if( $("input, select").is("#option_select2_id") == true && !($("#option_select2_id").val() != "") ){
            _trigger = false;
        }
        if( $("input, select").is("#option_select3_id") == true && !($("#option_select3_id").val() != "") ){
            _trigger = false;
        }
        if( _trigger ) {
            $.ajax({
                url: "/program/option_select_add.php",
                cache: false,
                type: "POST",
                data: "code="+code+"&uid1=" + $("#option_select1_id").val() + "&uid2=" + $("#option_select2_id").val() + "&uid3=" + $("#option_select3_id").val() + "&add_uid1="+$("#add_option_select_1_id").val() +"&add_uid2="+$("#add_option_select_2_id").val()  +"&add_uid3="+$("#add_option_select_3_id").val()  +"&add_uid4="+$("#add_option_select_4_id").val() +"&add_uid5="+$("#add_option_select_5_id").val() +"&add_uid6="+$("#add_option_select_6_id").val() +"&add_uid7="+$("#add_option_select_7_id").val() +"&add_uid8="+$("#add_option_select_8_id").val() +"&add_uid9="+$("#add_option_select_9_id").val() +"&add_uid10="+$("#add_option_select_10_id").val(),
                success: function(data){
                    if(data == "error1") {
                        alert('잘못된 접근입니다.');
                    }
                    else if(data == "error2") {
                        alert('이미 선택한 옵션입니다.');
                    }
                    else if(data == "error3") {
                        alert('선택 옵션의 재고량이 부족합니다.');
                    }
                    else if(data == "error4") {
                        alert('재고량이 부족합니다.');
                    }
                    else {
                        $("#span_seleced_list").html(data);

						// 합계가격을 뿌려줌 (view 부분은 ajax 소스 밖에 위치하므로 이와같이 추가 처리함...)
						$("#option_select_expricesum_display").html($("#option_select_expricesum").val().comma());
                    }
                }
            });
        }
        else {
            alert('옵션을 선택해 주세요');
        }
    }
    // - 옵션 추가 ---

    // - 옵션 추가 - 옵션 없을 경우 ---
    function nooption_select_add(code) {
        $.ajax({
            url: "/program/option_select_add.php",
            cache: false,
            type: "POST",
            data: "code="+code+"&option_type_chk=none&add_uid1="+$("#add_option_select_1_id").attr('data-uid') +"&add_uid2="+$("#add_option_select_2_id").attr('data-uid') +"&add_uid3="+$("#add_option_select_3_id").attr('data-uid')  +"&add_uid4="+$("#add_option_select_4_id").attr('data-uid') +"&add_uid5="+$("#add_option_select_5_id").attr('data-uid') +"&add_uid6="+$("#add_option_select_6_id").attr('data-uid') +"&add_uid7="+$("#add_option_select_7_id").attr('data-uid') +"&add_uid8="+$("#add_option_select_8_id").attr('data-uid') +"&add_uid9="+$("#add_option_select_9_id").attr('data-uid') +"&add_uid10="+$("#add_option_select_10_id").attr('data-uid'),
            success: function(data){
                if(data == "error1") {
                    alert('잘못된 접근입니다.');
                }
                else if(data == "error2") {
                    alert('이미 선택한 옵션입니다.');
                }
                else if(data == "error4") {
                    alert('재고량이 부족합니다.');
                }
                else {
                    $("#span_seleced_list").html(data);

					// 합계가격을 뿌려줌 (view 부분은 ajax 소스 밖에 위치하므로 이와같이 추가 처리함...)
					$("#option_select_expricesum_display").html($("#option_select_expricesum").val().comma());
                }
            }
        });
    }
    // - 옵션 추가 - 옵션 없을 경우 ---

    // - 옵션 삭제 ---
    function option_select_del(uid,code) {
        $.ajax({
            url: "/program/option_select_del.php",
            cache: false,
            type: "POST",
            data: "code="+code+"&uid=" + uid ,
            success: function(data){
                if(data == "error1") {
                    alert('잘못된 접근입니다.');
                }
                else if(data == "error4") {
                    alert('재고량이 부족합니다.');
                }
                else {
                    $("#span_seleced_list").html(data);

					// 합계가격을 뿌려줌 (view 부분은 ajax 소스 밖에 위치하므로 이와같이 추가 처리함...)
					$("#option_select_expricesum_display").html($("#option_select_expricesum").val().comma());
                }
            }
        });
    }
    // - 옵션 삭제 ---

    // - 옵션 수량수정 ::: _type : up/down ---
    function option_select_update(_type , uid,code) {
        $.ajax({
            url: "/program/option_select_update.php",
            cache: false,
            type: "POST",
            data: "_type="+ _type +"&code="+code+"&uid=" + uid + "&cnt=" + $("#input_cnt_" + uid).val(),
            success: function(data){
                if(data == "error1") {
                    alert('잘못된 접근입니다.');
                }
                else if(data == "error3") {
                    alert('선택 옵션의 재고량이 부족합니다.');
                }
                else if(data == "error4") {
                    alert('재고량이 부족합니다.');
                }
                else {
                    $("#span_seleced_list").html(data);

					// 합계가격을 뿌려줌 (view 부분은 ajax 소스 밖에 위치하므로 이와같이 추가 처리함...)
					$("#option_select_expricesum_display").html($("#option_select_expricesum").val().comma());
                }
            }
        });
    }
    // - 옵션 수량수정 ---




	// - 추가옵션 추가 ---
	function add_option_select_add(code , pao_uid, name, uid) {
		if( pao_uid ) {
			$.ajax({
				url: "/program/add_option_select_add.php",
				cache: false,
				type: "POST",
				data: {"code" : code , "uid" : pao_uid },
				success: function(data){
					if(data == "error1") {
						alert('잘못된 접근입니다.');
					}
					else if(data == "error2") {
						alert('이미 선택한 옵션입니다.');
					}
					else if(data == "error3") {
						alert('선택 옵션의 재고량이 부족합니다.');
					}
					else if(data == "error4") {
						alert('재고량이 부족합니다.');
					}
					else if(data == "error6") {
						alert('필수옵션을 먼저 선택해 주시기 바랍니다.');
					}
					else {
						$("#span_seleced_list").html(data);

						// 합계가격을 뿌려줌 (view 부분은 ajax 소스 밖에 위치하므로 이와같이 추가 처리함...)
						$("#option_select_expricesum_display").html($("#option_select_expricesum").val().comma());

						// 기본형태 맞춤
						if(uid) $("#add_option_select_"+uid+"_id").val(pao_uid);
						if(name && uid) $("#add_option_pao_name_"+uid).html(name);
						if(uid) $("#add_option_select_"+uid+"_box").hide();
						setTimeout(function(){ $('#add_option_select_'+uid+'_box').attr({'style':''}); }, 100);
					}
				}
			});
		}
	}
	// - 추가옵션 추가 ---

	// - 추가옵션 수량수정 ::: _type : up/down ---
	function add_option_select_update(_type , uid, code) {
		$.ajax({
			url: "/program/add_option_select_update.php",
			cache: false,
			type: "POST",
			data: { "_type" : _type , "code" : code, "uid" : uid , "cnt" : $("#input_cnt_add_" + uid).val() },
			success: function(data){
				if(data == "error1") {
					alert('잘못된 접근입니다.');
				}
				else if(data == "error3") {
					alert('선택 옵션의 재고량이 부족합니다.');
				}
				else if(data == "error4") {
					alert('재고량이 부족합니다.');
				}
				else if(data == "error5") {
					alert('구매제한 개수가 초과 되었습니다.');
				}
				else {
					$("#span_seleced_list").html(data);

					// 합계가격을 뿌려줌 (view 부분은 ajax 소스 밖에 위치하므로 이와같이 추가 처리함...)
					$("#option_select_expricesum_display").html($("#option_select_expricesum").val().comma());
				}
			}
		});
	}
	// - 추가옵션 수량수정 ---