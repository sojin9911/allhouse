<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
clean_cart(); // 장바구니 판매불가 상품 삭제

// ajax호출시 pn을 shop.cart.list 로지정하기위해
$_tmp_pn = $pn;
$pn = 'shop.cart.list';
//  장바구니 공통 프로세스
include_once(dirname(__FILE__).'/shop.cart.inc.php');
$pn = $_tmp_pn;


include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end');