<?php
include_once('inc.php');
if(!$com_id) error_msg('잘못된접근입니다.');

// 사전체크
$_delivery_price = delComma($_delivery_price);
$_delivery_freeprice = delComma($_delivery_freeprice);

// 2017-06-16 ::: 부가세율설정 ::: JJC
$_vat_delivery = ($siteInfo['s_vat_delivery'] == 'C'?$_vat_delivery:$siteInfo['s_vat_delivery']); // 복합과세가 아닐 경우 전체설정에 적용됨

_MQ_noreturn("
	update smart_company set 
		  cp_delivery_price = '$_delivery_price'
		, cp_delivery_freeprice = '$_delivery_freeprice'
		, cp_delivery_use = '$_delivery_use'
		, cp_delivery_company = '$_delivery_company'
		, cp_delivery_date = '$_delivery_date'
		, cp_delivery_complain_price = '$_delivery_complain_price'
		, cp_delivery_return_addr = '$_delivery_return_addr'
		, cp_del_addprice_use ='{$_del_addprice_use}'
		, cp_del_addprice_use_normal ='{$_del_addprice_use_normal}'
		, cp_del_addprice_use_unit ='{$_del_addprice_use_unit}'
		, cp_del_addprice_use_free ='{$_del_addprice_use_free}'
		, cp_vat_delivery = '{$_vat_delivery}'
	where 
		cp_id = '{$com_id}'
");
error_loc("_config.delivery.form.php");