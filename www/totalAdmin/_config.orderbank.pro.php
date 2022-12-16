<?php
include_once('inc.php');

if($_mode == 'modify'){
	// 설정값 업데이트
	$que = "
		update smart_setup set
			 s_bank_autocheck_use		= '".$_bank_autocheck_use."'
			,s_apibox_id							= '".$_apibox_id."'
		where s_uid = '1'
	";
	_MQ_noreturn($que);

	error_loc_msg('_config.orderbank.form.php', '정상적으로 수정되었습니다.');
}
else{
	error_msg('잘못된 접근입니다.');
}
exit;
