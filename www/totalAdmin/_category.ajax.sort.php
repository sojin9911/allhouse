<?PHP
	include "./inc.php";

	// where문과 정렬조건
	$query = "from smart_product_category_best where 1 and pctb_cuid ='".$cuid."' ";

	// 현재 상품 정보 추출
	$now = _MQ("select pctb_pcode, pctb_sort_group, pctb_sort_idx,pctb_cuid, pctb_idx from smart_product_category_best where pctb_pcode = '". $pcode ."' and pctb_cuid ='".$cuid."' ");

	switch($_mode) {
		case "top" :
			// top 상품 정보 추출
			$top = _MQ("select pctb_pcode, pctb_sort_group, pctb_sort_idx, pctb_idx ".$query." and pctb_idx < '". $now['pctb_idx'] ."'  order by pctb_idx asc limit 0, 1");

			if($top[pctb_pcode] == $pcode || count($top) < 1 ) {echo json_encode(array('rst'=>'fail','msg'=>'맨 처음 상품입니다.')); exit;}

			// 현재 상품을 top상품의 위치 위로 이동
			_MQ_noreturn("update smart_product_category_best set pctb_sort_group = '". $top['pctb_sort_group'] ."', pctb_sort_idx = '". ($top['pctb_sort_idx']-0.5) ."', pctb_idx = '". $top['pctb_idx'] ."' where pctb_pcode = '".$now['pctb_pcode']."' and pctb_cuid ='".$now['pctb_cuid']."' ");

		break;
		case "bottom" :
			// bottom 상품 정보 추출
			$bottom = _MQ("select pctb_pcode, pctb_sort_group, pctb_sort_idx, pctb_idx ".$query." and pctb_idx > '". $now['pctb_idx'] ."'  order by pctb_idx desc limit 0, 1");

			if($bottom[pctb_pcode] == $pcode || count($bottom) < 1 ) {echo json_encode(array('rst'=>'fail','msg'=>'맨 마지막 상품입니다.')); exit;}

			// 현재 상품을 bottom상품의 위치 위로 이동
			_MQ_noreturn("update smart_product_category_best set pctb_sort_group = '". $bottom['pctb_sort_group'] ."', pctb_sort_idx = '". ($bottom['pctb_sort_idx']+0.5) ."', pctb_idx = '". $bottom['pctb_idx'] ."' where pctb_pcode = '".$now['pctb_pcode']."' and pctb_cuid ='".$now['pctb_cuid']."'  ");

		break;
		case "up" :
			// up 상품 정보 추출
			$up = _MQ("select pctb_pcode, pctb_sort_group, pctb_sort_idx, pctb_idx ".$query." and pctb_idx < '". $now['pctb_idx'] ."'   order by pctb_idx desc limit 0, 1");

			if(!$up[pctb_pcode]) {echo json_encode(array('rst'=>'fail','msg'=>'맨 처음 상품입니다.')); exit;}

			// 현재 상품을 up상품의 위치 위로 이동
			_MQ_noreturn("update smart_product_category_best set pctb_sort_group = '". $up['pctb_sort_group'] ."', pctb_sort_idx = '". ($up['pctb_sort_idx']-0.5) ."', pctb_idx = '". $up['pctb_idx'] ."' where pctb_pcode = '".$now['pctb_pcode']."' and pctb_cuid ='".$now['pctb_cuid']."' ");

		break;
		case "down" :
			// down 상품 정보 추출
			$down = _MQ("select pctb_pcode, pctb_sort_group, pctb_sort_idx, pctb_idx ".$query." and pctb_idx > '". $now['pctb_idx'] ."' order by pctb_idx asc limit 0, 1");

			if(!$down[pctb_pcode]){echo json_encode(array('rst'=>'fail','msg'=>'맨 마지막 상품입니다.')); exit;}

			// 현재 상품을 down상품의 위치 아래로 이동
			_MQ_noreturn("update smart_product_category_best set pctb_sort_group = '". $down['pctb_sort_group'] ."', pctb_sort_idx = '". ($down['pctb_sort_idx']+0.5) ."', pctb_idx = '". $down['pctb_idx'] ."' where pctb_pcode = '".$now['pctb_pcode']."'and pctb_cuid ='".$now['pctb_cuid']."'  ");
			break;

		case "modify_group" :
			// 변경할 상품그룹 정보 추출
			$group = _MQ(" select max(pctb_sort_idx) max from smart_product_category_best where pctb_sort_group = '". $_group ."' and pctb_pcode='".$pcode."' and pctb_cuid ='".$cuid."'  ");

			// 상위그룹으로 변경시
			if($now['pctb_sort_group']>$_group){
				_MQ_noreturn("update smart_product_category_best set pctb_sort_group = '". $_group ."', pctb_sort_idx = '". ($group['max']+0.5) ."' where pctb_pcode = '".$now['pctb_pcode']."'and pctb_cuid ='".$now['pctb_cuid']."'  ");
			}
			// 하위그룹으로 변경시
			else if($now['pctb_sort_group']<$_group){
				_MQ_noreturn("update smart_product_category_best set pctb_sort_group = '". $_group ."', pctb_sort_idx = '0.5' where pctb_pcode = '".$now['pctb_pcode']."'and pctb_cuid ='".$now['pctb_cuid']."'  ");
			}
			else{
				//  변경사항없음
			}
			break;

		default :
			error_alt("잘못된 접근입니다.");
			break;

	}
	// KAY : 2021-11-12 pctb_idx 재정렬
	cate_product_resort($cuid);
	switch($_mode) {
		case "modify_group" :
			echo json_encode(array('rst'=>'success','msg'=>'수정이 완료되었습니다.'));
			break;
		default :
			echo json_encode(array('rst'=>'success'));
			break;
	}

?>