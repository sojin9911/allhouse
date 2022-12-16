<?php
include 'inc.php';


if($_mode == 'update') {
	_MQ_noreturn(" update smart_setup set s_skin = '{$set_skin}', s_skin_m = '{$set_skin}' where s_uid='1' ");

	// -- 기존 스킨과 정보가 다르다면 
	if( $siteInfo['s_skin'] != $set_skin){
		$siteInfo = get_site_info(); // 스킨정보가 업데이트 됨에 따라 다시 불러온다.
		updateSkinPro(); 
	}


}

error_loc("_skin.php");