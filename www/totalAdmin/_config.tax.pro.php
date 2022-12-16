<?php

	include "inc.php";

	if($email != '' && checkInputValue($email, 'email') !== true) error_msg('이메일 형식이 올바르지 않습니다.');
	if($htel != '' && checkInputValue($htel, 'htel') !== true) error_msg('휴대폰 형식이 올바르지 않습니다.');

	// 서비스 , 테스트에 따라 certkey 변경
	$TAX_CERTKEY = ($TAX_MODE == "service" ? $tax_barobill_certkery_service : $tax_barobill_certkery_test);

	// -- smart_setup 적용 ---
	$que = "
		update smart_setup set
			TAX_BAROBILL_ID		= '".$TAX_BAROBILL_ID."',
			TAX_BAROBILL_PW		= '".$TAX_BAROBILL_PW."',
			TAX_BAROBILL_NAME	= '".$TAX_BAROBILL_NAME."',
			TAX_CHK				= '".$TAX_CHK."',
			TAX_MODE			= '".$TAX_MODE."',
			TAX_CERTKEY			= '".$TAX_CERTKEY."',

			s_company_name			= '".$name."',
			s_ceo_name			= '".$ceoname."',
			s_company_num		= '".$number1."',
			s_company_addr		= '".$taxaddress."',
			s_glbmanagerhp		= '".$htel."',
			s_ademail			= '".$email."',
			s_glbtel			= '".$tel."',
			s_item1				= '".$taxstatus."',
			s_item2				= '".$taxitem."'


			/* SSJ : 현금영수증 필수발행 패치 : 2021-02-01 */
			,s_force_cashbill_use = '".($force_cashbill_use?$force_cashbill_use:'N')."'
			,s_force_cashbill_price = '".rm_str($force_cashbill_price)."'

		where s_uid				= 1
	";
	_MQ_noreturn($que);

	error_frame_reload("수정되었습니다");

