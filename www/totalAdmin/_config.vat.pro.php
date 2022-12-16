<?PHP

	include "./inc.php";


	$que = "
		update smart_setup set
			s_vat_product = '". $s_vat_product ."',
			s_vat_delivery = '". $s_vat_delivery ."',
			s_vat_discount = '". $s_vat_discount ."'			
		where 
			s_uid = 1
	";
	_MQ_noreturn($que);


	error_loc("_config.vat.form.php");

