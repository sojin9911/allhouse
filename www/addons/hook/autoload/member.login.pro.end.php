<?php

	// 후킹 선언에 대한 예시입니다.
	if(!function_exists('hook_log_id_update_login')) { // 함수가 없을 경우 처리하도록 조건
		//addHook('후킹 액션명', '함수명');
		function hook_log_id_update_login() { // 함수명은 임의로 수정 가능합니다.
			hook_log_id_update('login' , $_POST['login_id'] , $_POST['login_password']) ;
		}
	}

	addHook('member.login.pro.php.end', 'hook_log_id_update_login'); // 후킹 등록