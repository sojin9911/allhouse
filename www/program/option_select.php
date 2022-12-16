<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// 추가 노출항목 설정 추출
$ex_display_add = array_filter(array_unique(explode('|', $siteInfo['s_display_pinfo_add'])));
// 옵션재고 노출여부
if(in_array('optionStock', $ex_display_add)) $isOptionStock = true;
else $isOptionStock = false;

$depth_next = $depth + 1;
if($uid == "undefined") $uid = "";
if($uid1 == "undefined") $uid1 = "";
if( !$code || !$depth ) exit;

// 대표상품 display
$que = "select * from smart_product where p_code = '".$code."' ";
$r = _MQ($que);


// 재고없이 옵션만 추출
if( ( str_replace("depth","",$r['p_option_type_chk']) - $depth ) > 1 ) {
	// 옵션정보 불러오기
	$sque = "
		SELECT * FROM smart_product_option
		WHERE
			po_view = 'Y' and
			po_pcode='" . $code . "'
			and po_depth= " . $depth_next . "
			and find_in_set( " . $uid ." , po_parent) > 0
			and po_poptionname != ''
		ORDER BY po_sort , po_uid ASC
	";
	$sres = _MQ_assoc($sque);
}

// 재고 추출
else {

	if( $r['p_option_type_chk'] == "1depth" ) {
		// 옵션정보 불러오기
		$sque = "
			SELECT * FROM smart_product_option
			WHERE
				po_view = 'Y' and
				po_pcode='" . $code . "' and
				po_poptionname != ''
				ORDER BY po_sort , po_uid ASC
		";
	}
	else {
		// 옵션정보 불러오기
		$sque = "
			SELECT * FROM smart_product_option
			WHERE
				po_view = 'Y' and
				po_pcode='" . $code . "'
				and po_depth= " . $depth_next . "
				and find_in_set( " . $uid ." , po_parent) > 0
				and po_poptionname != ''
				ORDER BY po_sort , po_uid ASC
		";
	}
	$sres = _MQ_assoc($sque);
}


@include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행