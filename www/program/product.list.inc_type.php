<?php 
/*
	** ITEM BOX 공통화 처리 프로세서
	** 이곳에서 표준 상품 리스트내 모든 것들을 처리 
	** 기본 $v 로 받으며 변경될 경우 $v = $? 로 변경바람
	** 추가되는 변수의 경우 $v로 하되 겹치지 않게 prefix 로 하이픈을 첨부 
	** 사용법 
		<?php 
			$incType =''; // 타입은 기본 type1, 있을 경우 별도 설정
			$locationFile = basename(__FILE__); // 파일설정
			include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
		?>
*/

$_img = get_img_src($v['p_img_list_square'], IMG_DIR_PRODUCT);

// 바로구매/장바구니 버튼 (pcode,_type,is_option)
$is_option = is_option($v['p_code']); // 옵션상품인지 체크
$order_link = "javascript:app_submit_from_list('{$v['p_code']}', 'order', {$is_option});";
$cart_link = "javascript:app_submit_from_list('{$v['p_code']}', 'cart', {$is_option});";
$wish_link = "wish_tran('{$v['p_code']}')";

// 배송정보
$delivery_info = get_delivery_info($v['p_code']);

// 아이콘 설정
$icon = '';
$p_icon_array = explode(',', $v['p_icon']);
// 무료배송 아이콘
if($delivery_info['status'] == '1' && $freedelivery_icon_src) {
	$free_delivery_icon = '<img src="'.$freedelivery_icon_src .'" alt="'. $freedelivery_icon['pi_title'] .'">';
	$icon .= $free_delivery_icon;
}
// 상품쿠폰 아이콘
$ex_coupon = explode('|' , $v['p_coupon']);
if($ex_coupon[0] && $ex_coupon[1] && $coupon_icon_src) $icon .= '<img src="'.$coupon_icon_src .'" alt="'. $coupon_icon['pi_title'] .'">';
if(count($product_icon) > 0) {
	foreach($product_icon as $k0 => $v0) {
		if(array_search($v0['pi_uid'],$p_icon_array) !== false){
			$_tmp_arr = array('pc'=>get_img_src($v0['pi_img'],IMG_DIR_ICON), 'mo'=>get_img_src($v0['pi_img_m'],IMG_DIR_ICON));
			$_tmp_src = is_mobile() ? ($_tmp_arr['mo'] ? $_tmp_arr['mo'] : $_tmp_arr['pc']) : ($_tmp_arr['pc'] ? $_tmp_arr['pc'] : $_tmp_arr['mo']);
			if($_tmp_src) $icon .= '<img src="'.$_tmp_src.'" alt="'.$v0['pi_title'].'">';
		}
	}
}
// 기획전 아이콘
if($arr_promotion_pcode[$v['p_code']] > 0  && $promotion_icon_src) {
	$app_promotion_icon = '<img src="'.$promotion_icon_src .'" alt="'. $promotion_icon['pi_title'] .'">';
	$icon .= $app_promotion_icon;
}
$pro_icon = ($icon?'<div class="upper_icon">'.$icon.'</div>':null);


// 특정 페이지에서만 로드되도록, product.view. 는 제외 
if( in_array($locationFile, array('product.view.php')) < 1 ) { 
	// 기타 정보
	$eval_cnt = number_format( 1 * get_talk_total($v['p_code'], 'eval', 'normal')); // 상품평 갯수
	$qna_cnt = number_format( 1 * get_talk_total($v['p_code'], 'qna', 'normal')); // 상품문의 갯수
	$star_persent = get_eval_average($v['p_code']); // 상품평점
	$pro_point = number_format( 1 * $v['p_price']*$v['p_point_per']/100); // 적립율
}

// 배송비 금액 출력
$pro_delivery = '';
switch($v['p_shoppingPay_use']){
	case 'Y': $pro_delivery = '개별배송 '.number_format( 1 * $delivery_info['price']).'원'; break;
	case 'N': $pro_delivery = '배송비 '.number_format( 1 * $delivery_info['price']).'원'; break;
	case 'F': $pro_delivery = $free_delivery_icon; break; //무료배송
}

// 순위 추출
$_rank = $k + 1 + $listmaxcount * ($listpg-1);

// 최종 뷰 파일 include
$incType = $incType ? $incType : 'type1';
$productIncTypeFilename = '';
switch ($incType) {

	// 리스트형
	case 'typelist': 
		$productIncTypeFilename = "product.list.inc_typelist.php"; break; // PC:[ajax.main_best_category]

	// 기본형처리
	case 'type1': 
	default:
		$productIncTypeFilename = "product.list.inc_type1.php"; break; // 기본형

	
}

if($NotInclude !== true) {
	include($SkinData['skin_root'].'/'.$productIncTypeFilename); // 스킨폴더에서 해당 파일 호출
	actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
}
