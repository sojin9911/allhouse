<?PHP
	// -- 1차 카테고리 배열 적용 ---
	$arr_parent01 = array();
	$cres = _MQ_assoc("select c_uid , c_name from smart_category where c_view='Y' and c_depth='1' order by c_idx asc ");
	foreach( $cres as $k=>$v ){
		$arr_cate01[$v['c_uid']] = $v['c_name'];
	}
	// -- 1차 카테고리 배열 적용 ---
?>


<span class="fr_tx">1차 분류</span>
<?php echo _InputSelect( 'pass_cate01' , array_keys($arr_cate01) , '' , 'id="pass_cate01" onchange="category_select2(1);" ' , array_values($arr_cate01) , '-선택-'); ?>
<span class="fr_tx">2차 분류</span>
<?php echo _InputSelect( 'pass_cate02' , array() , $app_depth2 , 'id="pass_cate02" onchange="category_select2(2);" ' , array() , '-선택-'); ?>
<span class="fr_tx">3차 분류</span>
<?php echo _InputSelect( 'pass_cate03' , array() , '', ' ' , array() , '-선택-'); ?>
<a href="#none" onclick="category_add();" class="c_btn h27 blue icon icon_plus">선택 카테고리 추가</a>


<SCRIPT LANGUAGE="JavaScript">
	// - 카테고리 목록 ---
	function category_list() {
		$.ajax({
			url: "_product.inc_category_pro.php",
			type: "POST",
			data: "_cmode=list&_code=<?php echo $_code; ?>",
			success: function(data){
				$("#_product_cateogry_list").html(data);
			}
		});
	}
	// - 카테고리 목록 ---
	// - 카테고리 삭제 ---
	function category_delete(_cuid) {
		if( confirm('정말 삭제하시겠습니까?') ){
			if( _cuid ){
				$.ajax({
					url: "_product.inc_category_pro.php",
					type: "POST",
					data: "_cmode=delete&_code=<?php echo $_code; ?>&_cuid=" + _cuid ,
					success: function(data){
						category_list();
					}
				});
			}
			else {
				alert("3차 카테고리를 선택해주시기 바랍니다.");
			}
		}
	}
	// - 카테고리 삭제 ---
	// - 카테고리 추가 ---
	function category_add() {
		$.ajax({
			url: "_product.inc_category_pro.php",
			type: "POST",
			data: "_cmode=add&_code=<?=$_code?>&pass_parent01=" + $("select[name=pass_cate01]").val() + "&pass_parent02=" + $("select[name=pass_cate02]").val() + "&pass_parent03=" + $("select[name=pass_cate03]").val() ,
			success: function(data){
				/*
				console.log("_mode=add&_code=<?=$_code?>&pass_parent01=" + $("select[name=pass_cate01]").val() + "&pass_parent02=" + $("select[name=pass_cate02]").val() + "&pass_parent03=" + $("select[name=pass_cate03]").val());
				console.log(data);
				*/
				category_list();
			}
		});
	}
	// - 카테고리 추가 ---
	// - 카테고리 선택 ---
	function category_select2(_idx) {
        $.ajax({
            url: "<?php echo OD_PROGRAM_DIR; ?>/categorysearch.pro.php",
			cache: false,
			dataType: "json",
			type: "POST",
            data: "pass_parent03_no_required=<?php echo $pass_cate03_no_required; ?>&pass_parent01=" + $("[name=pass_cate01]").val() + "&pass_parent02=" + $("[name=pass_cate02]").val()+"&pass_idx=" + _idx ,
            success: function(data){
                if(_idx == 2) {
					//$("select[name=pass_cate02]").val(apppass_cate03); // 현재정보 적용
					$("select[name=pass_cate03]").find("option").remove().end().append('<option value="">-선택-</option>');
					var option_str = '';
					for (var i = 0; i < data.length; i++) {
						option_str += '<option value="' + data[i].optionValue + '" >' + data[i].optionDisplay + '</option>';
					}
					$("select[name=pass_cate03]").append(option_str);
				}
				else if(_idx == 1){
					$("select[name=pass_cate02]").find("option").remove().end().append('<option value="">-선택-</option>');
					var option_str = '';
					for (var i = 0; i < data.length; i++) {
						option_str += '<option value="' + data[i].optionValue + '" >' + data[i].optionDisplay + '</option>';
					}
					$("select[name=pass_cate02]").append(option_str);
					$("select[name=pass_cate03]").find("option").remove().end().append('<option value="">-선택-</option>');
				}
            }
		});
	}
	// - 카테고리 선택 ---
</SCRIPT>