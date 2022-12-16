<?PHP
	include "./inc.php";

	// where문과 정렬조건
	$query = "from smart_display_type_product where 1 and dtp_dtsuid ='".$_uid."' ";

	// 현재 상품 정보 추출
	$now = _MQ("select dtp_pcode, dtp_sort_group, dtp_sort_idx,dtp_dtsuid, dtp_idx from smart_display_type_product where dtp_pcode = '". $pcode ."' and dtp_dtsuid ='".$_uid."' ");

	switch($_mode) {
		case "top" :
			// top 상품 정보 추출
			$top = _MQ("select dtp_pcode, dtp_sort_group, dtp_sort_idx, dtp_idx ".$query." and dtp_idx < '". $now['dtp_idx'] ."'  order by dtp_idx asc limit 0, 1");

			if($top[dtp_pcode] == $pcode || count($top) < 1 ) {echo json_encode(array('rst'=>'fail','msg'=>'맨 처음 상품입니다.')); exit;}

			// 현재 상품을 top상품의 위치 위로 이동
			_MQ_noreturn("update smart_display_type_product set dtp_sort_group = '". $top['dtp_sort_group'] ."', dtp_sort_idx = '". ($top['dtp_sort_idx']-0.5) ."', dtp_idx = '". $top['dtp_idx'] ."' where dtp_pcode = '".$now['dtp_pcode']."' and dtp_dtsuid ='".$now['dtp_dtsuid']."' ");

		break;
		case "bottom" :
			// bottom 상품 정보 추출
			$bottom = _MQ("select dtp_pcode, dtp_sort_group, dtp_sort_idx, dtp_idx ".$query." and dtp_idx > '". $now['dtp_idx'] ."'  order by dtp_idx desc limit 0, 1");

			if($bottom[dtp_pcode] == $pcode || count($bottom) < 1 ) {echo json_encode(array('rst'=>'fail','msg'=>'맨 마지막 상품입니다.')); exit;}

			// 현재 상품을 bottom상품의 위치 위로 이동
			_MQ_noreturn("update smart_display_type_product set dtp_sort_group = '". $bottom['dtp_sort_group'] ."', dtp_sort_idx = '". ($bottom['dtp_sort_idx']+0.5) ."', dtp_idx = '". $bottom['dtp_idx'] ."' where dtp_pcode = '".$now['dtp_pcode']."' and dtp_dtsuid ='".$now['dtp_dtsuid']."'  ");

		break;
		case "up" :
			// up 상품 정보 추출
			$up = _MQ("select dtp_pcode, dtp_sort_group, dtp_sort_idx, dtp_idx ".$query." and dtp_idx < '". $now['dtp_idx'] ."'   order by dtp_idx desc limit 0, 1");

			if(!$up[dtp_pcode]) {echo json_encode(array('rst'=>'fail','msg'=>'맨 처음 상품입니다.')); exit;}

			// 현재 상품을 up상품의 위치 위로 이동
			_MQ_noreturn("update smart_display_type_product set dtp_sort_group = '". $up['dtp_sort_group'] ."', dtp_sort_idx = '". ($up['dtp_sort_idx']-0.5) ."', dtp_idx = '". $up['dtp_idx'] ."' where dtp_pcode = '".$now['dtp_pcode']."' and dtp_dtsuid ='".$now['dtp_dtsuid']."' ");

		break;
		case "down" :
			// down 상품 정보 추출
			$down = _MQ("select dtp_pcode, dtp_sort_group, dtp_sort_idx, dtp_idx ".$query." and dtp_idx > '". $now['dtp_idx'] ."' order by dtp_idx asc limit 0, 1");

			if(!$down[dtp_pcode]) {echo json_encode(array('rst'=>'fail','msg'=>'맨 마지막 상품입니다.')); exit;}

			// 현재 상품을 down상품의 위치 아래로 이동
			_MQ_noreturn("update smart_display_type_product set dtp_sort_group = '". $down['dtp_sort_group'] ."', dtp_sort_idx = '". ($down['dtp_sort_idx']+0.5) ."', dtp_idx = '". $down['dtp_idx'] ."' where dtp_pcode = '".$now['dtp_pcode']."'and dtp_dtsuid ='".$now['dtp_dtsuid']."'  ");
			break;

		case "modify_group" :
			// 변경할 상품그룹 정보 추출
			$group = _MQ(" select max(dtp_sort_idx) max from smart_display_type_product where dtp_sort_group = '". $_group ."' and dtp_pcode='".$pcode."' and dtp_dtsuid ='".$_uid."'  ");

			// 상위그룹으로 변경시
			if($now['dtp_sort_group']>$_group){
				_MQ_noreturn("update smart_display_type_product set dtp_sort_group = '". $_group ."', dtp_sort_idx = '". ($group['max']+0.5) ."' where dtp_pcode = '".$now['dtp_pcode']."'and dtp_dtsuid ='".$now['dtp_dtsuid']."'  ");
			}
			// 하위그룹으로 변경시
			else if($now['dtp_sort_group']<$_group){
				_MQ_noreturn("update smart_display_type_product set dtp_sort_group = '". $_group ."', dtp_sort_idx = '0.5' where dtp_pcode = '".$now['dtp_pcode']."'and dtp_dtsuid ='".$now['dtp_dtsuid']."'  ");
			}
			else{
				//  변경사항없음
			}
			break;

		default :
			error_alt("잘못된 접근입니다.");
			break;

	}
	// KAY : 2021-11-12 dtp_idx 재정렬
	type_product_resort($_uid);

	switch($_mode) {
		case "modify_group" :
			echo json_encode(array('rst'=>'success','msg'=>'수정이 완료되었습니다.'));
			break;
		default :
			echo json_encode(array('rst'=>'success'));
			break;
	}

?>