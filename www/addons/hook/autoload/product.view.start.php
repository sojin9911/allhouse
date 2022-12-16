<?php
// 후킹 선언에 대한 예시입니다.
if(!function_exists('product_view_php_start')) { // 함수가 없을 경우 처리하도록 조건
	function product_view_php_start() { // 함수명은 임의로 수정 가능합니다.
		// 내용
		//echo '> hook :: product_view_php_start()';
	}
}
//addHook('후킹 액션명', '함수명');
addHook('product.view.php.start', 'product_view_php_start'); // 후킹 등록