<?php

	// 브랜드별 상품목록  --> 브랜드 메뉴박스
	include_once(dirname(__FILE__).'/inc.php');
	actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



	// 넘겨온 변수 체크
	$s_que = " where c_view = 'Y' ";
	$brand = $brand ? $brand : 'all';
	switch($brand){
		case "all": break; // 전체일 경우
		case "etc":
			$arr_prefix = array_merge($arr_prefix_kor , $arr_prefix_eng);
			$s_que .= " and c_prefix_str NOT IN ('". implode("' , '" , $arr_prefix) ."') ";
			break;
		default :
			if($brand) {
				$s_que .= " and c_prefix_str = '". addslashes($brand) ."' ";
			}
			break;
	}


	// 브랜드가 uid 형태로 넘어온 경우
	if($uid) {
		// 해당 브랜드의 초성정보 추출
		$res = _MQ("select * from smart_brand where c_uid = '". addslashes($uid) ."' ");
		if($res['c_prefix_str']) {
			$s_que .= " and c_prefix_str = '". $res['c_prefix_str'] ."' ";
		}
	}


	// 전체 브랜드 초성별 분류
	$arr_brand_prefix = array();
	$res = _MQ_assoc("select * from smart_brand ". $s_que ." order by c_name asc ");
	foreach( $res as $k=>$v ){
		// 한글과 영문이 아닌 경우 - 무조건 기타로 보냄
		$str_asi = @ord($v['c_prefix_str']);
		$v['c_prefix_str'] =  ($str_asi == '227' || ($str_asi >= 65 &&$str_asi <= 90)) ? $v['c_prefix_str'] : '기타';
		$arr_brand_prefix[$v['c_prefix_str']][$v['c_uid']] = $v['c_name'];
	}



	include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 스킨 호출
	actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행