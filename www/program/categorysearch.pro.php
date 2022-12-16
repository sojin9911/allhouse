<?PHP

	include "inc.php";

	// 넘겨온 변수
    //$pass_parent01
    //$pass_parent02
	//$pass_parent03_no_required
    //$_idx


	



    // - 2단 분류 선택시 ---
    if(  $pass_parent02 && $pass_idx == 2 ){
        $que = "select c_uid , c_name from smart_category where c_view='Y' and c_depth='3' and find_in_set('${pass_parent02}' , c_parent) > 0 order by c_idx asc";
    }
    // - 2단 분류 선택시 ---

    //  - 1단분류 ---
    else if( $pass_parent01 && $pass_idx == 1 ) {
        $que = "select c_uid , c_name from smart_category where c_view='Y' and c_depth='2' and find_in_set('${pass_parent01}' , c_parent) > 0 order by c_idx asc";
	}
    //  - 1단분류 ---

    $res = mysql_query($que);
	$str = "";
	for( $i=0; $v = mysql_fetch_assoc($res); $i++){

		if($i <> 0) {
			$str .= ' , ';
		}
		$str .= '{"optionValue" : "' . $v[c_uid] . '" , "optionDisplay" : "' . $v[c_name] . '"}';
		$cnt ++;
	}
	echo '[' . $str . ']';


    exit;
?>