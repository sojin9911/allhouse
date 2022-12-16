<?PHP

	/* SSJ: 2017-09-19 include시 변수명이 겹치지 않도록 함수화 */
	if(!function_exists('showCategory')){
		function showCategory($app_depth1='', $app_depth2='', $app_depth3='', $pass_parent03_no_required='Y'){
			global $MobileAdmin;

			// 넘어올 변수
			// 1차 카테고리 - $app_depth1 = $ex[0];
			// 2차 카테고리 - $app_depth2 = $ex[1];


			// 폴더 위치 보정
			if( !$_path_str ) {
				if( @file_exists("../../include/config_database.php") ) {
					$_path_str = "../..";
				}
				else {
					$_path_str = "..";
				}
			}


			// -- 1차 카테고리 배열 적용 ---
			$arr_parent01 = array();
			$res = _MQ_assoc("select c_uid , c_name from smart_category where c_view='Y' and c_depth='1' order by c_idx asc ");
			foreach( $res as $k=>$v ){
				if($select_mode == 'readonly') { if($v['c_uid'] == $app_depth1) $arr_parent01[$v['c_uid']] = $v['c_name'];
				} else $arr_parent01[$v['c_uid']] = $v['c_name'];
		  }
			// -- 1차 카테고리 배열 적용 ---

			// -- 2차 카테고리 배열 적용 ---
			$arr_parent02 = array();
			if( $app_depth1 ) {
				$res = _MQ_assoc("select c_uid , c_name from smart_category where c_view='Y' and c_depth='2' and find_in_set('${app_depth1}' , c_parent) > 0 order by c_idx asc");
				foreach( $res as $k=>$v ){
					$arr_parent02[$v['c_uid']] = $v['c_name'];
				}
			}
			// -- 2차 카테고리 배열 적용 ---

			// -- 3차 카테고리 배열 적용 ---
			$arr_parent03 = array();
			if( $app_depth2 ) {
				$res = _MQ_assoc("select c_uid , c_name from smart_category where c_view='Y' and c_depth='3' and find_in_set('${app_depth2}' , c_parent) > 0 order by c_idx asc");
				foreach( $res as $k=>$v ){
					$arr_parent03[$v['c_uid']] = $v['c_name'];
				}
			}
			// -- 3차 카테고리 배열 적용 ---
			if($MobileAdmin === true) {
				$str = "
					<!-- IE이외 카테고리 오류 보정 -->
					<input type=hidden name='pass_parent02_real' value='". $app_depth2 ."'>
					<input type=hidden name='pass_parent03_real' value='". $app_depth3 ."'>
					<!-- IE이외 카테고리 오류 보정 -->
					<div class=\"select\">
						<span class=\"shape\"></span>
						". _InputSelect( 'pass_parent01' , array_keys($arr_parent01) , $app_depth1 , 'id="pass_parent01" onchange="category_select(1);" ' , array_values($arr_parent01) , '-선택-')  ."
					</div>
					<div class=\"select\">
						<span class=\"shape\"></span>
						". _InputSelect( 'pass_parent02' , array_keys($arr_parent02) , $app_depth2 , 'id="pass_parent02" onchange="category_select(2);" ' , array_values($arr_parent02) , '-선택-')  ."
					</div>
					<div class=\"select\">
						<span class=\"shape\"></span>
						". _InputSelect( '_cuid' , array_keys($arr_parent03) , $app_depth3, ' onchange="category_chk();" hname="상품분류" '.($pass_parent03_no_required=='Y' ? '':'required').'  ' , array_values($arr_parent03) , '-선택-')  ."
					</div>
				";
			}
			else {
				$str = "
					<!-- IE이외 카테고리 오류 보정 -->
					<input type=hidden name='pass_parent02_real' value='". $app_depth2 ."'>
					<input type=hidden name='pass_parent03_real' value='". $app_depth3 ."'>
					<!-- IE이외 카테고리 오류 보정 -->
					<span class='fr_tx'>1차 분류</span> ". _InputSelect( 'pass_parent01' , array_keys($arr_parent01) , $app_depth1 , 'id="pass_parent01" onchange="category_select(1);" ' , array_values($arr_parent01) , '-선택-')  ."&nbsp;&nbsp;&nbsp;
					<span ID='span_category'>
						<span class='fr_tx'>2차 분류</span> ". _InputSelect( 'pass_parent02' , array_keys($arr_parent02) , $app_depth2 , 'id="pass_parent02" onchange="category_select(2);" ' , array_values($arr_parent02) , '-선택-')  ."&nbsp;&nbsp;&nbsp;
						<span class='fr_tx'>3차 분류</span> ". _InputSelect( '_cuid' , array_keys($arr_parent03) , $app_depth3, ' onchange="category_chk();" hname="상품분류" '.($pass_parent03_no_required=='Y' ? '':'required').'  ' , array_values($arr_parent03) , '-선택-')  ."
					</span>
				";
			}

			return $str;
		}

	}
	$app_depth3 = $app_depth3 ? $app_depth3 : $row['p_cuid'];
	echo showCategory($app_depth1, $app_depth2, $app_depth3, $pass_parent03_no_required);
?>
<SCRIPT LANGUAGE="JavaScript">
	// 카테고리 선택
	if(typeof category_select === 'undefined'){
		function category_select(_idx) {
			$.ajax({
				url: "<?php echo OD_PROGRAM_DIR; ?>/categorysearch.pro.php",
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
	}
	//2 , 3차 카테고리 체크 - IE이외 카테고리 오류 보정
	if(typeof category_chk === 'undefined'){
		function category_chk(){
			$("input[name=pass_parent02_real]").val($("select[name=pass_parent02] option:selected").val()); // 2차 카테고리
			$("input[name=pass_parent03_real]").val($("select[name=_cuid] option:selected").val()); // 3차 카테고리
		}
	}
</SCRIPT>