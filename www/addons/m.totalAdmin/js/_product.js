
	// 카테고리 선택
	function category_select(_idx) {
        $.ajax({
            url: "/program/categorysearch.pro.php",
			cache: false,
			dataType: "json",
			type: "POST",
            data: "pass_parent03_no_required=<?=$pass_parent03_no_required?>&pass_parent01=" + $("[name=pass_parent01]").val() + "&pass_parent02=" + $("[name=pass_parent02]").val()+"&pass_idx=" + _idx ,
            success: function(data){
                if(_idx == 2) {
					//$("select[name=pass_parent02]").val(app_cuid); // 현재정보 적용
					$("select[name=_cuid]").find("option").remove().end().append('<option value="">-선택-</option>');
					var option_str = '';
					for (var i = 0; i < data.length; i++) {
						option_str += '<option value="' + data[i].optionValue + '" >' + data[i].optionDisplay + '</option>';
					}
					$("select[name=_cuid]").append(option_str);
				}
				else if(_idx == 1){
					$("select[name=pass_parent02]").find("option").remove().end().append('<option value="">-선택-</option>');
					var option_str = '';
					for (var i = 0; i < data.length; i++) {
						option_str += '<option value="' + data[i].optionValue + '" >' + data[i].optionDisplay + '</option>';
					}
					$("select[name=pass_parent02]").append(option_str);
					$("select[name=_cuid]").find("option").remove().end().append('<option value="">-선택-</option>');
				}
            }
		});
		category_chk();
	}

	//2 , 3차 카테고리 체크 - IE이외 카테고리 오류 보정
	function category_chk(){
		$("input[name=pass_parent02_real]").val($("select[name=pass_parent02] option:selected").val()); // 2차 카테고리
		$("input[name=pass_parent03_real]").val($("select[name=_cuid] option:selected").val()); // 3차 카테고리
	}

	var delivery_setting = function() {
	
		if($("._shoppingPay_use:checked").val() == "Y") {
			$("#_shoppingPay_use_Y").show();
			$("#_shoppingPay_use_N").hide();
		} else {
			$("#_shoppingPay_use_Y").hide();
			$("#_shoppingPay_use_N").show();
		}

	}

	$("._shoppingPay_use").click(delivery_setting);

   $(document).ready(function() {
		delivery_setting();
   });


	// 정산 형태 선택
	var comm_type_check = function() {
		if($("input[name=_commission_type]:checked").val() == "공급가") {
			$("#comSaleTypeTr1").show();
			$("#comSaleTypeTr2").hide();
		} else {
			$("#comSaleTypeTr1").hide();
			$("#comSaleTypeTr2").show();
		}
	}
	$("input[name=_commission_type]").click(comm_type_check);
	$(document).ready(comm_type_check);

