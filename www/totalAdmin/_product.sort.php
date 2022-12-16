<?PHP
	include "./inc.php";

	// where문과 정렬조건
	$query = enc("d",$query);

	// 현재 상품 정보 추출
	$now = _MQ("select p_code, p_sort_group, p_sort_idx, p_idx from smart_product where p_code = '". $pcode ."' ");

	switch($_mode) {
		case "top" :
			// top 상품 정보 추출
			$top = _MQ("select p_code, p_sort_group, p_sort_idx, p_idx ".$query." and p_idx < '". $now['p_idx'] ."' order by p_idx asc limit 0, 1");

			if($top[p_code] == $pcode || count($top) < 1  ) error_alt("맨 처음 상품입니다.");

			// 현재 상품을 top상품의 위치 위로 이동
			_MQ_noreturn("update smart_product set p_sort_group = '". $top['p_sort_group'] ."', p_sort_idx = '". ($top['p_sort_idx']-0.5) ."', p_idx = '". $top['p_idx'] ."' where p_code = '".$now['p_code']."' ");

		break;
		case "bottom" :
			// bottom 상품 정보 추출
			$bottom = _MQ("select p_code, p_sort_group, p_sort_idx, p_idx ".$query." and p_idx > '". $now['p_idx'] ."' order by p_idx desc limit 0, 1");

			if($bottom[p_code] == $pcode || count($bottom) < 1  ) error_alt("맨 마지막 상품입니다.");

			// 현재 상품을 bottom상품의 위치 위로 이동
			_MQ_noreturn("update smart_product set p_sort_group = '". $bottom['p_sort_group'] ."', p_sort_idx = '". ($bottom['p_sort_idx']+0.5) ."', p_idx = '". $bottom['p_idx'] ."' where p_code = '".$now['p_code']."' ");

		break;
		case "up" :
			// up 상품 정보 추출
			$up = _MQ("select p_code, p_sort_group, p_sort_idx, p_idx ".$query." and p_idx < '". $now['p_idx'] ."' order by p_idx desc limit 0, 1");

			if(!$up[p_code]) error_alt("맨 처음 상품입니다.");

			// 현재 상품을 up상품의 위치 위로 이동
			_MQ_noreturn("update smart_product set p_sort_group = '". $up['p_sort_group'] ."', p_sort_idx = '". ($up['p_sort_idx']-0.5) ."', p_idx = '". $up['p_idx'] ."' where p_code = '".$now['p_code']."' ");

		break;
		case "down" :
			// down 상품 정보 추출
			$down = _MQ("select p_code, p_sort_group, p_sort_idx, p_idx ".$query." and p_idx > '". $now['p_idx'] ."' order by p_idx asc limit 0, 1");

			if(!$down[p_code]) error_alt("맨 마지막 상품입니다.");

			// 현재 상품을 down상품의 위치 아래로 이동
			_MQ_noreturn("update smart_product set p_sort_group = '". $down['p_sort_group'] ."', p_sort_idx = '". ($down['p_sort_idx']+0.5) ."', p_idx = '". $down['p_idx'] ."' where p_code = '".$now['p_code']."' ");
			break;
		case "modify_group" :
			// 변경할 상품그룹 정보 추출
			$group = _MQ(" select max(p_sort_idx) max from smart_product where p_sort_group = '". $_group ."' ");

			// 상위그룹으로 변경시
			if($now['p_sort_group']>$_group){
				_MQ_noreturn("update smart_product set p_sort_group = '". $_group ."', p_sort_idx = '". ($group['max']+0.5) ."' where p_code = '".$now['p_code']."' ");
			}
			// 하위그룹으로 변경시
			else if($now['p_sort_group']<$_group){
				_MQ_noreturn("update smart_product set p_sort_group = '". $_group ."', p_sort_idx = '0.5' where p_code = '".$now['p_code']."' ");
			}
			else{
				//  변경사항없음
			}
			break;
		case "mass_sort" :
			if(sizeof($chk_pcode) > 0){
				foreach($chk_pcode as $pcode=>$val){
					if($val == 'Y'){

						// 현재 상품 정보 추출
						$now = _MQ("select p_code, p_sort_group, p_sort_idx, p_idx from smart_product where p_code = '". $pcode ."' ");

						// 변경할 상품그룹 정보 추출
						$group = _MQ(" select max(p_sort_idx) max from smart_product where p_sort_group = '". $sort_group[$pcode] ."' ");

						// 상위그룹으로 변경시
						if($now['p_sort_group']>$sort_group[$pcode]){
							_MQ_noreturn("update smart_product set p_sort_group = '". $sort_group[$pcode] ."', p_sort_idx = '". ($group['max']+0.5) ."' where p_code = '".$now['p_code']."' ");
						}
						// 하위그룹으로 변경시
						else if($now['p_sort_group']<$sort_group[$pcode]){
							_MQ_noreturn("update smart_product set p_sort_group = '". $sort_group[$pcode] ."', p_sort_idx = '0.5' where p_code = '".$now['p_code']."' ");
						}

					}
				}
			}
			break;
		default :
			error_alt("잘못된 접근입니다.");
			break;

	}


	// SSJ : 2017-09-18 p_idx 재정렬
	product_resort();

	switch($_mode) {
		case "modify_group" :
		case "mass_sort" :
			error_frame_reload('정상적으로 수정되었습니다.');
			break;
		default :
			// 새로고침.
			error_frame_reload_nomsg();
			break;
	}

?>