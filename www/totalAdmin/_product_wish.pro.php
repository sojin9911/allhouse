<?PHP
	include "./inc.php";


	// - 모드별 처리 ---
	switch( $_mode ){

		case "delete":

			$pw_uid = nullchk($pw_uid , '찜 코드를 입력해주시기 바랍니다.');

			// 상품정보 삭제
			_MQ_noreturn("delete from smart_product_wish where pw_uid='{$pw_uid}' ");

			error_loc_msg("_product_wish.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null) , '정상적으로 삭제되었습니다.');
			break;



		// 일괄삭제
		case "mass_delete":

			if(sizeof($chk_uid) > 0){
				$s_query = " where pw_uid in ('".implode("','" , $chk_uid)."') ";

				// 상품삭제
				_MQ_noreturn("delete from smart_product_wish {$s_query} ");

				error_loc_msg('_product_wish.list.php'.($_PVSC ? '?'.enc('d' , $_PVSC) : null) , '정상적으로 삭제되었습니다.');
			}else{
				error_msg('잘못된 접근입니다.');
			}
			break;


	}
	// - 모드별 처리 ---

	exit;
?>