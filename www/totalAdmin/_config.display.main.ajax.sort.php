<?PHP
	include "./inc.php";

	// where문과 정렬조건
	$query = "from smart_display_main_product where 1 and dmp_dmsuid ='".$_uid."' ";

	// 현재 상품 정보 추출
	$now = _MQ("select dmp_pcode, dmp_sort_group, dmp_sort_idx,dmp_dmsuid, dmp_idx from smart_display_main_product where dmp_pcode = '". $pcode ."' and dmp_dmsuid ='".$_uid."' ");

	switch($_mode) {
		case "top" :
			// top 상품 정보 추출
			$top = _MQ("select dmp_pcode, dmp_sort_group, dmp_sort_idx, dmp_idx ".$query." and dmp_idx < '". $now['dmp_idx'] ."'  order by dmp_idx asc limit 0, 1");

			if($top[dmp_pcode] == $pcode || count($top) < 1 ){echo json_encode(array('rst'=>'fail','msg'=>'맨 처음 상품입니다.')); exit;}

			// 현재 상품을 top상품의 위치 위로 이동
			_MQ_noreturn("update smart_display_main_product set dmp_sort_group = '". $top['dmp_sort_group'] ."', dmp_sort_idx = '". ($top['dmp_sort_idx']-0.5) ."', dmp_idx = '". $top['dmp_idx'] ."' where dmp_pcode = '".$now['dmp_pcode']."' and dmp_dmsuid ='".$now['dmp_dmsuid']."' ");

		break;
		case "bottom" :
			// bottom 상품 정보 추출
			$bottom = _MQ("select dmp_pcode, dmp_sort_group, dmp_sort_idx, dmp_idx ".$query." and dmp_idx > '". $now['dmp_idx'] ."'  order by dmp_idx desc limit 0, 1");

			if($bottom[dmp_pcode] == $pcode || count($bottom) < 1 ) {echo json_encode(array('rst'=>'fail','msg'=>'맨 마지막 상품입니다.')); exit;}

			// 현재 상품을 bottom상품의 위치 위로 이동
			_MQ_noreturn("update smart_display_main_product set dmp_sort_group = '". $bottom['dmp_sort_group'] ."', dmp_sort_idx = '". ($bottom['dmp_sort_idx']+0.5) ."', dmp_idx = '". $bottom['dmp_idx'] ."' where dmp_pcode = '".$now['dmp_pcode']."' and dmp_dmsuid ='".$now['dmp_dmsuid']."'  ");

		break;
		case "up" :
			// up 상품 정보 추출
			$up = _MQ("select dmp_pcode, dmp_sort_group, dmp_sort_idx, dmp_idx ".$query." and dmp_idx < '". $now['dmp_idx'] ."'   order by dmp_idx desc limit 0, 1");

			if(!$up[dmp_pcode]) {echo json_encode(array('rst'=>'fail','msg'=>'맨 처음 상품입니다.')); exit;}

			// 현재 상품을 up상품의 위치 위로 이동
			_MQ_noreturn("update smart_display_main_product set dmp_sort_group = '". $up['dmp_sort_group'] ."', dmp_sort_idx = '". ($up['dmp_sort_idx']-0.5) ."', dmp_idx = '". $up['dmp_idx'] ."' where dmp_pcode = '".$now['dmp_pcode']."' and dmp_dmsuid ='".$now['dmp_dmsuid']."' ");

		break;
		case "down" :
			// down 상품 정보 추출
			$down = _MQ("select dmp_pcode, dmp_sort_group, dmp_sort_idx, dmp_idx ".$query." and dmp_idx > '". $now['dmp_idx'] ."' order by dmp_idx asc limit 0, 1");

			if(!$down[dmp_pcode]){echo json_encode(array('rst'=>'fail','msg'=>'맨 마지막 상품입니다.')); exit;}

			// 현재 상품을 down상품의 위치 아래로 이동
			_MQ_noreturn("update smart_display_main_product set dmp_sort_group = '". $down['dmp_sort_group'] ."', dmp_sort_idx = '". ($down['dmp_sort_idx']+0.5) ."', dmp_idx = '". $down['dmp_idx'] ."' where dmp_pcode = '".$now['dmp_pcode']."'and dmp_dmsuid ='".$now['dmp_dmsuid']."'  ");
			break;

		case "modify_group" :
			// 변경할 상품그룹 정보 추출
			$group = _MQ(" select max(dmp_sort_idx) max from smart_display_main_product where dmp_sort_group = '". $_group ."' and dmp_pcode='".$pcode."' and dmp_dmsuid ='".$_uid."'  ");

			// 상위그룹으로 변경시
			if($now['dmp_sort_group']>$_group){
				_MQ_noreturn("update smart_display_main_product set dmp_sort_group = '". $_group ."', dmp_sort_idx = '". ($group['max']+0.5) ."' where dmp_pcode = '".$now['dmp_pcode']."'and dmp_dmsuid ='".$now['dmp_dmsuid']."'  ");
			}
			// 하위그룹으로 변경시
			else if($now['dmp_sort_group']<$_group){
				_MQ_noreturn("update smart_display_main_product set dmp_sort_group = '". $_group ."', dmp_sort_idx = '0.5' where dmp_pcode = '".$now['dmp_pcode']."'and dmp_dmsuid ='".$now['dmp_dmsuid']."'  ");
			}
			else{
				//  변경사항없음
			}
			break;

		default :
			error_alt("잘못된 접근입니다.");
			break;

	}

	// KAY : 2021-11-12 dmp_idx 재정렬
	main_product_resort($_uid);
	switch($_mode) {
		case "modify_group" :
			echo json_encode(array('rst'=>'success','msg'=>'수정이 완료되었습니다.'));
			break;
		default :
			echo json_encode(array('rst'=>'success'));
			break;
	}

?>