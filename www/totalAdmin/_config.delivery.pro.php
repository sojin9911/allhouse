<?PHP
	include "./inc.php";

	// 사전체크
	$_delprice		= delComma($_delprice);
	$_delprice_free	= delComma($_delprice_free);

	$_complain_ok	= htmlspecialchars($_complain_ok);
	$_complain_fail	= htmlspecialchars($_complain_fail);

	// 자동정산 설정
	if(!$_product_auto_on) $_product_auto_on = 'N';
	$s_que = "";
	$s_que .= ", `s_product_auto_C` = '".$_product_auto_C."' ";
	$s_que .= ", `s_product_auto_L` = '".$_product_auto_L."' ";
	$s_que .= ", `s_product_auto_B` = '".$_product_auto_B."' ";
	$s_que .= ", `s_product_auto_G` = '".$_product_auto_G."' ";
	$s_que .= ", `s_product_auto_V` = '".$_product_auto_V."' ";
	$s_que .= ", `s_product_auto_H` = '".$_product_auto_H."' ";
	$s_que .= ", `s_product_auto_P` = '".$_product_auto_P."' ";
	$s_que .= ", `s_product_auto_on` = '".$_product_auto_on."' ";

	# 추가배송비 설정 추가 2017-05-18 :: SSJ {
	$s_que .= ", s_del_addprice_use = '".$_del_addprice_use."' ";
	$s_que .= ", s_del_addprice_use_normal = '".$_del_addprice_use_normal."' ";
	$s_que .= ", s_del_addprice_use_unit = '".$_del_addprice_use_unit."' ";
	$s_que .= ", s_del_addprice_use_free = '".$_del_addprice_use_free."' ";
	# 추가배송비 설정 추가 2017-05-18 :: SSJ }

	// ----- JJC : 상품별 배송비 : 2018-08-16 -----
	$s_que .= ", s_del_addprice_use_product = '" . $_del_addprice_use_product . "' ";
	// ----- JJC : 상품별 배송비 : 2018-08-16 -----

	// SSJ : 자동 배송완료 패치 : 2021-02-01
	$s_que .= " , s_delivery_auto = '".$_delivery_auto."' ";
	// SSJ : 자동 배송완료 패치 : 2021-02-01

	$que = "update smart_setup set
				s_delprice						= '".$_delprice."',
				s_delprice_free					= '".$_delprice_free."',
				s_del_company					= '".$_del_company."',
				s_del_date						= '".$_del_date."',
				s_del_complain_price			= '".$_del_complain_price."',
				s_del_return_addr				= '".$_del_return_addr."',
				s_complain_ok					= '".$_complain_ok."',
				s_complain_fail					= '".$_complain_fail."',
				s_account_commission			= '".$_account_commission."'
				{$s_que}
				where s_uid						= 1";

	_MQ_noreturn($que);

	error_loc("_config.delivery.form.php");
	exit;
?>