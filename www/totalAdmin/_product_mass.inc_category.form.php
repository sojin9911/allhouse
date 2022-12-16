<?PHP
	
	$_tmpcode = "TMP01234"; // 임시 상품번호
	$_code = $_tmpcode;

	// 임시 상품 - 카테고리 삭제
	_MQ_noreturn(" delete from smart_product_category where pct_pcode='". $_code ."' ");



	// -- 1차 카테고리 배열 적용 ---
	$arr_parent01 = array();
	$cres = _MQ_assoc("select c_uid , c_name from smart_category where c_view='Y' and c_depth='1' order by c_idx asc ");
	foreach( $cres as $k=>$v ){
		$arr_cate01[$v[c_uid]] = $v[c_name];
	}
	// -- 1차 카테고리 배열 적용 ---

?>

<span class='fr_tx'>1차 분류</span> <?=_InputSelect( "pass_cate01" , array_keys($arr_cate01) , "" , " id='pass_cate01' onchange='category_select2(1);' " , array_values($arr_cate01) , "-선택-") ?>&nbsp;&nbsp;&nbsp;
<span class='fr_tx'>2차 분류</span> <?=_InputSelect( "pass_cate02" , array() , $app_depth2 , " id='pass_cate02' onchange='category_select2(2);' " , array() , "-선택-") ?>&nbsp;&nbsp;&nbsp;
<span class='fr_tx'>3차 분류</span> <?=_InputSelect( "pass_cate03" , array() , "", " " , array() , "-선택-") ?>

<a href="#none" onclick="category_add();" class="c_btn h27 gray">선택 카테고리추가</a>


<div ID="_product_cateogry_list">
<!-- 상품카테고리 목록 노출 -->
<?PHP
	$_mode = "list";
	include_once("_product_mass.inc_category.pro.php");
?>
</div>



<SCRIPT LANGUAGE="JavaScript">
	// - 카테고리 목록 ---
	function category_list() {
		$.ajax({
			url: "_product_mass.inc_category.pro.php",
			type: "POST",
			data: "_mode=list&_code=<?=$_code?>",
			success: function(data){
				$("#_product_cateogry_list").html(data);
			}
		});
	}
	// - 카테고리 목록 ---
	// - 카테고리 삭제 ---
	function category_delete(_cuid) {
		if( _cuid ){
			$.ajax({
				url: "_product_mass.inc_category.pro.php",
				type: "POST",
				data: "_mode=delete&_code=<?=$_code?>&_cuid=" + _cuid ,
				success: function(data){
					category_list();
				}
			});
		}
	}
	// - 카테고리 삭제 ---
	// - 카테고리 추가 ---
	function category_add() {
		$.ajax({
			url: "_product_mass.inc_category.pro.php",
			type: "POST",
			data: "_mode=add&_code=<?=$_code?>&pass_parent01=" + $("select[name=pass_cate01]").val() + "&pass_parent02=" + $("select[name=pass_cate02]").val() + "&pass_parent03=" + $("select[name=pass_cate03]").val() ,
			success: function(data){
				category_list();
			}
		});
	}
	// - 카테고리 추가 ---
	// - 카테고리 선택 ---
	function category_select2(_idx) {
        $.ajax({
            url: "/program/categorysearch.pro.php",
			cache: false,
			dataType: "json",
			type: "POST",
            data: "pass_parent03_no_required=<?=$pass_cate03_no_required?>&pass_idx=" + _idx + "&pass_parent01=" + $("select[name=pass_cate01]").val() + "&pass_parent02=" + $("select[name=pass_cate02]").val() + "&pass_parent03=" + $("select[name=pass_cate03]").val() ,
            success: function(data){
                if(_idx == 2) {
					$("select[name=pass_cate03]").find("option").remove().end().append('<option value="">-선택-</option>');
					var option_str = '';
					for (var i = 0; i < data.length; i++) {option_str += '<option value="' + data[i].optionValue + '" >' + data[i].optionDisplay + '</option>';}
					$("select[name=pass_cate03]").append(option_str);
				}
				else if(_idx == 1){
					$("select[name=pass_cate02]").find("option").remove().end().append('<option value="">-선택-</option>');
					var option_str = '';
					for (var i = 0; i < data.length; i++) {option_str += '<option value="' + data[i].optionValue + '" >' + data[i].optionDisplay + '</option>';}
					$("select[name=pass_cate02]").append(option_str);
					$("select[name=pass_cate03]").find("option").remove().end().append('<option value="">-선택-</option>');
				}
            }
		});
	}
	// - 카테고리 선택 ---
</SCRIPT>