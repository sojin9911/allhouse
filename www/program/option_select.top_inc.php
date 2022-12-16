<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// - 상품추출 ---
$que = "select * from smart_product where p_code = '".$code."' ";
$r = _MQ($que);

$arr_option_data = array();
if($r[p_option_type_chk] == "3depth") {
	$poque = "
		SELECT 
			po3.* , 
			po2.po_uid as po2_uid, po2.po_poptionname as po2_poptionname,
			po1.po_uid as po1_uid, po1.po_poptionname as po1_poptionname
		FROM smart_product_option as po3 
		inner join smart_product_option as po2 on ( po2.po_uid = SUBSTRING_INDEX(po3.po_parent,',',-1) and po2.po_depth=2)
		inner join smart_product_option as po1 on ( po1.po_uid = SUBSTRING_INDEX(po3.po_parent,',',1) and po1.po_depth=1)
		WHERE po3.po_view='Y' and po3.po_pcode='" . $code . "' and po3.po_depth=3 ORDER BY po3.po_sort , po3.po_uid ASC
	";
	$pores = _MQ_assoc($poque);
	foreach( $pores as $k=>$por ){
		$arr_option_data[$por[po_uid]]['option_name1'] = $por[po1_poptionname];
		$arr_option_data[$por[po_uid]]['option_name2'] = $por[po2_poptionname];
		$arr_option_data[$por[po_uid]]['option_name3'] = $por[po_poptionname];
		$arr_option_data[$por[po_uid]]['option_supplyprice'] = $por[po_poption_supplyprice];
		$arr_option_data[$por[po_uid]]['option_price'] = $por[po_poptionprice];
		$arr_option_data[$por[po_uid]]['option_cnt'] = $por[po_cnt];
	}
}
else if($r[p_option_type_chk] == "2depth") {
	$poque = "
		SELECT 
			po2.*,
			po1.po_uid as po1_uid, po1.po_poptionname as po1_poptionname
		FROM smart_product_option as po2 
		inner join smart_product_option as po1 on ( po1.po_uid = SUBSTRING_INDEX(po2.po_parent,',',1) and po1.po_depth=1)
		WHERE po2.po_view='Y' and po2.po_pcode='" . $code . "' and po2.po_depth=2 ORDER BY po2.po_sort , po2.po_uid ASC
	";
	$pores = _MQ_assoc($poque);
	foreach( $pores as $k=>$por ){
		$arr_option_data[$por[po_uid]]['option_name1'] = $por[po1_poptionname];
		$arr_option_data[$por[po_uid]]['option_name2'] = $por[po_poptionname];
		$arr_option_data[$por[po_uid]]['option_supplyprice'] = $por[po_poption_supplyprice];
		$arr_option_data[$por[po_uid]]['option_price'] = $por[po_poptionprice];
		$arr_option_data[$por[po_uid]]['option_cnt'] = $por[po_cnt];
	}
}
else if($r[p_option_type_chk] == "1depth") {
	$poque = " SELECT * FROM smart_product_option WHERE po_view='Y' and po_pcode='" . $code . "' and po_depth=1 ORDER BY po_sort , po_uid ASC ";
	$pores = _MQ_assoc($poque);
	foreach( $pores as $k=>$por ){
		$arr_option_data[$por[po_uid]]['option_name1'] = $por[po_poptionname];
		$arr_option_data[$por[po_uid]]['option_supplyprice'] = $por[po_poption_supplyprice];
		$arr_option_data[$por[po_uid]]['option_price'] = $por[po_poptionprice];
		$arr_option_data[$por[po_uid]]['option_cnt'] = $por[po_cnt];
	}
}

actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행