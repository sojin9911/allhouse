<?PHP
	include "./inc.php";

	$relation_prop_code = $_COOKIE['relation_prop_code_' . $_code];

	if($relation_prop_code){
		$ex = explode("|" , $relation_prop_code );
		foreach($ex as $v) {
			$relation_prop_code_Division[] = $v;
		}
	}


	if(is_array($relation_prop_code_Division)){
		// 관련상품 실데이터 추출
		$app_str = implode("|" , $relation_prop_code_Division);

		// 관련상품 노출용데이터 추출
		$arr_str_view = array();
		foreach($relation_prop_code_Division as $k=>$v){
			$_tmp = _MQ(" select p_code,p_name from smart_product where p_code = '". $v ."' ");
			if($_tmp['p_code'] <> ''){ // 2019-07-31 SSJ :: 삭제된상품 제외
				$arr_str_view[] = stripslashes($_tmp['p_name']) . '('. $v .')';
			}
		}
		$app_str_view = implode(" | " , $arr_str_view);
	}
	else{
		// 관련상품 실데이터 추출
		$app_str = '';

		// 관련상품 노출용데이터 추출
		$app_str_view = '';
	}

?>
<SCRIPT src="/include/js/jquery-1.7.1.min.js"></SCRIPT>
<SCRIPT LANGUAGE="JavaScript">
	$(document).ready(function() {
		opener.$('input[name=_relation]').val('<?php echo $app_str; ?>').trigger('change');
		opener.$('.js_relation_view').val('<?php echo addslashes($app_str_view); ?>').trigger('change');
		close();
	});
</SCRIPT>