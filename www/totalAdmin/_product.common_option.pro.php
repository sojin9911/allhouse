<?PHP
# LDD010
// 카테고리 정보에 대한 3단 select 배열을 위한 ajax
$app_mode = "popup";
include_once("inc.php");


############### 옵션 SORTing ###############
// pass_type - U : up , D :down
// pass_depth - 1, 2, 3
// pass_uid  -옵션 고유번호
if( in_array($pass_type , array("insert")) && $pass_depth && $pass_uid ) {


    // 타겟 데이터 정보 추출
    $target_r = _MQ(" select * from smart_common_option where co_uid='" . $pass_uid ."' ");

    //error_msg($pass_depth);

    // 타켓 하위 순위 1 순위씩 밀림
    _MQ_noreturn(" update smart_common_option set co_sort = co_sort + 1 where co_suid='" . $pass_common_uid . "' and co_depth='". $pass_depth ."' ". ($target_r['co_parent'] ? " and co_parent = '". $target_r['co_parent'] ."' " : "") . " and co_sort > '". $target_r['co_sort'] ."' ");


    switch ($pass_depth) {
        case '1':

            $max_sort = ($target_r['co_sort'] + 1);

            // 항목추가 - 1차
            _MQ_noreturn("
                insert smart_common_option set
                    co_suid='{$pass_common_uid}',
                    co_poptionname='',
                    co_depth='1',
                    co_sort='". $max_sort ."'
            ");
            $uid_1depth = mysql_insert_id();

			if(in_array($pass_common_mode, array('2', '3'))){
				// 순번추출 - 2차
				$r2 = _MQ(" select ifnull(max(co_sort),0) as max_sort from smart_common_option where co_suid='" . $pass_common_uid . "' and co_depth='2' and co_parent='" . $uid_1depth . "' ");
				$max_sort2 = $r2['max_sort'] + 1;

				// 항목추가 - 2차
				_MQ_noreturn("
					insert smart_common_option set
						co_suid='{$pass_common_uid}',
						co_poptionname='',
						co_depth='2',
						co_parent='{$uid_1depth}',
						co_sort='". $max_sort2 ."'
				");
				$uid_2depth = mysql_insert_id();

				if(in_array($pass_common_mode, array('3'))){
					// 순번추출 - 3차
					$r3 = _MQ(" select ifnull(max(co_sort),0) as max_sort from smart_common_option where co_suid='" . $pass_common_uid . "' and co_depth='3' and find_in_set('" . $uid_2depth . "' , co_parent) > 0 ");
					$max_sort3 = $r3['max_sort'] + 1;

					// 항목추가 - 3차
					_MQ_noreturn("
						insert smart_common_option set
							co_suid='{$pass_common_uid}',
							co_poptionname='',
							co_depth='3',
							co_parent='{$uid_1depth},{$uid_2depth}',
							co_sort='". $max_sort3 ."'
					");
				}
			}
        break;
        case '2':

            $max_sort = ($target_r['co_sort'] + 1);

            // 항목추가 - 2차
            mysql_query("
                insert smart_common_option set
                    co_suid='{$pass_common_uid}',
                    co_poptionname='',
                    co_depth='2',
                    co_parent='{$target_r['co_parent']}',
                    co_sort='". $max_sort ."'
            ");
            $uid_2depth = mysql_insert_id($connect);

            if(in_array($pass_common_mode, array('3'))){
				// 순번추출 - 3차
				$r3 = _MQ(" select ifnull(max(co_sort),0) as max_sort from smart_common_option where co_suid='" . $pass_common_uid . "' and co_depth='3' and find_in_set('" . $uid_2depth . "' , co_parent) > 0 ");
				$max_sort3 = $r3['max_sort'] + 1;

				// 항목추가 - 3차
				mysql_query("
					insert smart_common_option set
						co_suid='{$pass_common_uid}',
						co_poptionname='',
						co_depth='3',
						co_parent='{$target_r['co_parent']},{$uid_2depth}',
						co_sort='". $max_sort3 ."'
				");
			}
        break;
        case '3':

            $max_sort = ($target_r['co_sort'] + 1);

            // 항목추가 - 3차
            mysql_query("
                insert smart_common_option set
                    co_suid='{$pass_common_uid}',
                    co_poptionname='',
                    co_depth='3',
                    co_parent='{$target_r['co_parent']}',
                    co_sort='". $max_sort ."'
            ");
        break;
    }

    // 항목추가 - 2차
    //_MQ_noreturn(" insert smart_common_option set co_suid='" . $pass_common_uid . "', co_poptionname='', co_depth='". $pass_depth ."', co_parent='" . $target_r['co_parent'] . "', co_sort='". ($target_r['co_sort'] + 1) ."' ");
}




############### 옵션 SORTing ###############
// pass_type - U : up , D :down
// pass_depth - 1, 2, 3
// pass_uid  -옵션 고유번호
else if( in_array($pass_type , array("U" , "D")) && $pass_depth && $pass_uid ) {

    // 타겟 데이터 정보 추출
    $target_r = _MQ(" select * from smart_common_option where co_uid='" . $pass_uid ."' ");

    // max sort 추출
    $maxr = _MQ(" select ifnull(max(co_sort),0) as max_sort from smart_common_option where co_suid='" . $pass_common_uid . "' and co_depth='". $pass_depth ."' ". ($target_r['co_parent'] ? " and co_parent = '". $target_r['co_parent'] ."' " : "") );

    // 0보다 크고 max 보다 적어야 함.
    if( $target_r[co_sort] > 0 && $target_r['co_sort'] <= $maxr['max_sort'] ) {

        // 타켓 데이터 정보 변경
        _MQ_noreturn(" update smart_common_option set ". ( $pass_type == "U" ? "co_sort=co_sort-1" : "co_sort=co_sort+1") ." where co_uid='" . $pass_uid ."' ");


        // 순위 바꿀 데이터 정보 추출 및 변경
        if( $pass_type == "U" ) {
            $change_r = _MQ(" select * from smart_common_option where co_suid='" . $pass_common_uid . "' and co_depth='". $pass_depth ."' ". ($target_r['co_parent'] ? " and co_parent = '". $target_r['co_parent'] ."' " : "") . " and co_uid != '" . $pass_uid ."' and co_sort < '". $target_r['co_sort'] ."' order by co_sort desc , co_uid desc limit 1 ");
            _MQ_noreturn(" update smart_common_option set co_sort = co_sort + 1 where co_uid='" . $change_r[co_uid] ."' ");
        }
        else {
            $change_r = _MQ(" select * from smart_common_option where co_suid='" . $pass_common_uid . "' and co_depth='". $pass_depth ."' ". ($target_r['co_parent'] ? " and co_parent = '". $target_r['co_parent'] ."' " : "") . " and co_uid != '" . $pass_uid ."' and co_sort > '". $target_r['co_sort'] ."' order by co_sort asc , co_uid asc limit 1 ");
            _MQ_noreturn(" update smart_common_option set co_sort = co_sort - 1 where co_uid='" . $change_r[co_uid] ."' ");
        }
    }

}



# 2015-11-18 실시간 저장을 위하여 정보를 한번씩 더 저장 한다.
if( sizeof($co_info) > 0 ) {

	// 옵션정보 추출
	$arr_co = array();
	$res = _MQ_assoc("select * from smart_common_option where co_uid IN ('". implode("' , '" , array_keys($co_info)) ."') ");
	foreach( $res as $k=>$v ){
		foreach( $v as $sk=>$sv ){
			$arr_co[$v['co_uid']][$sk] = $sv;
		}
	}

	// --이미지 경로 ---
	$trigger_img = 0; // 이미지 저장시 부모창 재 갱신
	$app_path = $_SERVER['DOCUMENT_ROOT'] . "/upfiles/option";

    foreach( $co_info as $k=>$v ){

		// 옵션유형에 따른 적용
		if($v['co_color_type'] == 'color') {
			$app_color_name = mysql_real_escape_string(trim($v['co_color_name_c']));
			// 이미지일 경우 삭제
			if( @file_exists($app_path .'/'. $arr_co[$k]['co_color_name']) ){
				@unlink($app_path .'/'. $arr_co[$k]['co_color_name']);
			}
		}
		else if($v['co_color_type'] == 'img') {
			// 이미지 등록
			$app_color_name = _PhotoPro( $app_path , "co_info_img_" . $k ) ;
			// 이미지 업로드시 썸네일 & 재시작
			if($_FILES["co_info_img_" . $k]['size'] > 0 ) {
				app_img_thumbnail($app_path , $app_color_name , ${"co_info_img_" . $k . "_OLD"} , 35 , 35 );
				$trigger_img ++;
			}
			// 삭제시 재시작
			if(${"co_info_img_" . $k . "_DEL"} == 'Y'){
				$trigger_img ++;
			}
		}

        $que = "
            update smart_common_option set
                co_poptionname     ='".mysql_real_escape_string(trim($v['co_poptionname']))."',
                co_poptionprice    ='".rm_str($v['co_poptionprice'])."',
                co_poption_supplyprice ='".rm_str($v['co_poption_supplyprice'])."',
                co_cnt             ='".rm_str($v['co_cnt'])."',
                co_view            = '".$v['co_view']."',

				co_color_type ='". $v['co_color_type'] ."',
				co_color_name ='". $app_color_name ."'

            where co_uid='{$k}'
        ";
        _MQ_noreturn($que);
    }
}

if( $trigger_img > 0 ) {
	//echo "<script>alert('저장하였습니다.');parent.category_apply();</script>";
	//echo "<script>parent.category_apply();</script>";
}

exit;