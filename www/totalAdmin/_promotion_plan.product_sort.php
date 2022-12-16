<?PHP
	include "./inc.php";

	// where문과 정렬조건
	$query = "from smart_promotion_plan_product_setup where 1 and ppps_ppuid ='".$_uid."' ";

	// 현재 상품 정보 추출
	$now = _MQ("select ppps_pcode, ppps_sort_group, ppps_sort_idx,ppps_ppuid, ppps_idx from smart_promotion_plan_product_setup where ppps_pcode = '". $pcode ."' and ppps_ppuid ='".$_uid."' ");

	switch($_mode) {
		case "top" :
			// top 상품 정보 추출
			$top = _MQ("select ppps_pcode, ppps_sort_group, ppps_sort_idx, ppps_idx ".$query." and ppps_idx < '". $now['ppps_idx'] ."'  order by ppps_idx asc limit 0, 1");

			if($top['ppps_pcode'] == $pcode || count($top) < 1 ){echo json_encode(array('rst'=>'fail','msg'=>'맨 처음 상품입니다.')); exit;}

			// 현재 상품을 top상품의 위치 위로 이동
			_MQ_noreturn("update smart_promotion_plan_product_setup set ppps_sort_group = '". $top['ppps_sort_group'] ."', ppps_sort_idx = '". ($top['ppps_sort_idx']-0.5) ."', ppps_idx = '". $top['ppps_idx'] ."' where ppps_pcode = '".$now['ppps_pcode']."' and ppps_ppuid ='".$now['ppps_ppuid']."' ");

		break;
		case "bottom" :
			// bottom 상품 정보 추출
			$bottom = _MQ("select ppps_pcode, ppps_sort_group, ppps_sort_idx, ppps_idx ".$query." and ppps_idx > '". $now['ppps_idx'] ."'  order by ppps_idx desc limit 0, 1");

			if($bottom['ppps_pcode'] == $pcode || count($bottom) < 1 ) {echo json_encode(array('rst'=>'fail','msg'=>'맨 마지막 상품입니다.')); exit;}

			// 현재 상품을 bottom상품의 위치 위로 이동
			_MQ_noreturn("update smart_promotion_plan_product_setup set ppps_sort_group = '". $bottom['ppps_sort_group'] ."', ppps_sort_idx = '". ($bottom['ppps_sort_idx']+0.5) ."', ppps_idx = '". $bottom['ppps_idx'] ."' where ppps_pcode = '".$now['ppps_pcode']."' and ppps_ppuid ='".$now['ppps_ppuid']."'  ");

		break;
		case "up" :
			// up 상품 정보 추출
			$up = _MQ("select ppps_pcode, ppps_sort_group, ppps_sort_idx, ppps_idx ".$query." and ppps_idx < '". $now['ppps_idx'] ."'   order by ppps_idx desc limit 0, 1");

			if(!$up['ppps_pcode']) {echo json_encode(array('rst'=>'fail','msg'=>'맨 처음 상품입니다.')); exit;}

			// 현재 상품을 up상품의 위치 위로 이동
			_MQ_noreturn("update smart_promotion_plan_product_setup set ppps_sort_group = '". $up['ppps_sort_group'] ."', ppps_sort_idx = '". ($up['ppps_sort_idx']-0.5) ."', ppps_idx = '". $up['ppps_idx'] ."' where ppps_pcode = '".$now['ppps_pcode']."' and ppps_ppuid ='".$now['ppps_ppuid']."' ");

		break;
		case "down" :
			// down 상품 정보 추출
			$down = _MQ("select ppps_pcode, ppps_sort_group, ppps_sort_idx, ppps_idx ".$query." and ppps_idx > '". $now['ppps_idx'] ."' order by ppps_idx asc limit 0, 1");

			if(!$down['ppps_pcode']){echo json_encode(array('rst'=>'fail','msg'=>'맨 마지막 상품입니다.')); exit;}

			// 현재 상품을 down상품의 위치 아래로 이동
			_MQ_noreturn("update smart_promotion_plan_product_setup set ppps_sort_group = '". $down['ppps_sort_group'] ."', ppps_sort_idx = '". ($down['ppps_sort_idx']+0.5) ."', ppps_idx = '". $down['ppps_idx'] ."' where ppps_pcode = '".$now['ppps_pcode']."'and ppps_ppuid ='".$now['ppps_ppuid']."'  ");
			break;

		case "modify_group" :
			// 변경할 상품그룹 정보 추출
			$group = _MQ(" select max(ppps_sort_idx) max from smart_promotion_plan_product_setup where ppps_sort_group = '". $_group ."' and ppps_pcode='".$pcode."' and ppps_ppuid ='".$_uid."'  ");

			// 상위그룹으로 변경시
			if($now['ppps_sort_group']>$_group){
				_MQ_noreturn("update smart_promotion_plan_product_setup set ppps_sort_group = '". $_group ."', ppps_sort_idx = '". ($group['max']+0.5) ."' where ppps_pcode = '".$now['ppps_pcode']."'and ppps_ppuid ='".$now['ppps_ppuid']."'  ");
			}
			// 하위그룹으로 변경시
			else if($now['ppps_sort_group']<$_group){
				_MQ_noreturn("update smart_promotion_plan_product_setup set ppps_sort_group = '". $_group ."', ppps_sort_idx = '0.5' where ppps_pcode = '".$now['ppps_pcode']."'and ppps_ppuid ='".$now['ppps_ppuid']."'  ");
			}
			else{
				//  변경사항없음
			}
			break;

		default :
			error_alt("잘못된 접근입니다.");
			break;

	}
	// KAY : 2021-11-12 ppps_idx 재정렬
	promotion_product_resort($_uid);
	switch($_mode) {
		case "modify_group" :
			echo json_encode(array('rst'=>'success','msg'=>'수정이 완료되었습니다.'));
			break;
		default :
			echo json_encode(array('rst'=>'success'));
			break;
	}

?>