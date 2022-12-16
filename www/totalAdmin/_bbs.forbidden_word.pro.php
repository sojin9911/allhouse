<?php 
	include_once("inc.php");

	// -- 데이터를 배열화
	$arrFwData = array('writer'=>$fwWriter, 'title'=>$fwTitle,'content'=>$fwContent);
	$fwData = addslashes(serialize($arrFwData));
	_MQ_noreturn("update smart_setup set s_bbs_forbidden_word = '".$fwData."' where s_uid = 1  ");
	error_loc_msg("_bbs.forbidden_word.form.php","저장되었습니다.");

?>