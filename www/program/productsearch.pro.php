<?PHP
include "inc.php";

// �Ѱܿ� ����
//$pass_parent 1�� ī�װ�cuid
//�ش�ī�װ��� ���ԵǴ� �����ǰ ���� ����


//3��ī�װ����� ����
if($pass_parent){
	//$depth3_cuid = get_3depth_cuid($pass_parent);
	//foreach($depth3_cuid as $k => $v){
	//	if($k<>0){
	//		$s_query .= " or ";
	//	}
		$s_query = " pct_cuid  = '" . $pass_parent . "' "; 
	//}

	$que = "
		select 
			p.p_code, p.p_name
		from smart_product as p
		left join smart_product_category as pct on (p.p_code=pct.pct_pcode)
		where 
			". $s_query . "
		order by p_idx asc , p_rdate desc
	";
	$p_res = _MQ_assoc($que);
	$str = "";
	foreach($p_res as $k => $v){
		if($k<>0){
			$str .= ' , ';
		}
		$str .= '{"optionValue" : "' . $v[p_code] . '" , "optionDisplay" : "' . $v[p_name] . '"}';

	}
	echo '[' . $str . ']';
}

exit;