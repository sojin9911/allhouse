<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
clean_cart(); // 장바구니 판매불가 상품 삭제

//  장바구니 공통 프로세스
include_once(dirname(__FILE__).'/shop.cart.inc.php');

// === 비회원 구매 설정 추가 통합 kms 2019-06-20 ==== 
// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---
// === 비회원 구매 설정 추가 통합 kms 2019-06-20 ==== 

// 회원의 찜하기 상품 추출
$whish_max_cnt = is_mobile() ? 6 : 8;
$get_pro_wish = array();
if(is_login() && count($arr_cart)>0) {
	$get_pro_wish = _MQ_assoc(" select * from smart_product_wish as pw inner join smart_product  as p on(p.p_code = pw.pw_pcode) where pw.pw_inid = '".get_userid()."' order by pw.pw_rdate desc limit 0,{$whish_max_cnt} ");
}


// 다른 고객이 많이 찾은 상품
$pro_max_cnt = is_mobile() ? 6 : 8;
$get_pro_push = array();
if(count($get_pro_wish) < 1){ // 찜한상품이 없으면 노출
	$str_pkv = '';
	foreach($arr_push_relation as $ak=>$av){
		if($av <> ''){
			$str_pkv .= implode(',',explode('|',$av));
		}
	}
	/* SSJ : 상품 품절 체크 - p_soldout_chk 정보 업데이트 : 2019-02-11 */
    $get_pro_push = _MQ_assoc("select * from smart_product
        where find_in_set(p_code,'".$str_pkv."') and not find_in_set(p_code,'".implode(',',$arr_push_code)."') and p_stock > 0 and p_soldout_chk = 'N' and p_view = 'Y' order by p_salecnt  desc limit 0,30   ");

    /* SSJ : 상품 품절 체크 - p_soldout_chk 정보 업데이트 : 2019-02-11 */
    if(count($get_pro_push) <= 0 ) { // 관련상품이 없을 시 판매수량으로 뽑는다.
        $get_pro_push = _MQ_assoc("select * from smart_product where p_stock > 0 and p_soldout_chk = 'N' and p_view = 'Y' order by p_salecnt desc limit 0,30");
    }
	shuffle($get_pro_push);
	$get_pro_push = array_slice($get_pro_push, 0, $pro_max_cnt);
}

include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end');