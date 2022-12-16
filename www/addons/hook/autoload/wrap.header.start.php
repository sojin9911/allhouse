<?php
// 후킹 선언에 대한 예시입니다.
if(!function_exists('wrap_header_php_start')) { // 함수가 없을 경우 처리하도록 조건
	function wrap_header_php_start() { // 함수명은 임의로 수정 가능합니다.
		// 내용
	}
}
//addHook('후킹 액션명', '함수명');
addHook('wrap.header.php.start', 'wrap_header_php_start'); // 후킹 등록