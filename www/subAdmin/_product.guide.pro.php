<?PHP
include_once('inc.php');

// - 입력수정 사전처리 ---
if($_mode <> "delete") {
	$g_user = $com_id;

	// --사전 체크 ---
	$g_type = nullchk($g_type , "등록구분을 선택해주시기 바랍니다.");
	$g_title = nullchk($g_title , "타이틀을 입력해주시기 바랍니다.");
	$g_content = nullchk($g_content , "상세내용을 입력해주시기 바랍니다.");
	// --사전 체크 ---


	// --query 사전 준비 ---
	$sque = " 
		 g_user = '". $g_user ."'
		,g_type = '". $g_type ."'
		,g_default = '". ($g_default=='Y'?'Y':'N') ."'
		,g_title = '". addslashes($g_title) ."'
		,g_content = '". addslashes($g_content) ."'
		,g_mdate = now()
	";
	// --query 사전 준비 ---

}
// - 입력수정 사전처리 ---

if($g_default == 'Y'){
	// 동일한 업체 동일한 분류의 기본노출 설정은 해지한다
	_MQ_noreturn(" update smart_product_guide set g_default = 'N' where g_user = '". $g_user ."' and g_type = '". $g_type ."' and g_default = 'Y' " . ($_uid ? " and g_uid != '". $_uid ."' " : null));
}



// - 모드별 처리 ---
switch( $_mode ){

	case "add":
		_MQ_noreturn("insert smart_product_guide set ${sque}, g_rdate=now()");
		$_uid = mysql_insert_id();
		error_loc("_product.guide.form.php" . URI_Rebuild('?', array('_mode'=>'modify', '_uid'=>$_uid, '_PVSC'=>$_PVSC)));
		break;


	case "modify":
		_MQ_noreturn(" update smart_product_guide set ${sque} where g_uid='${_uid}' ");
		error_loc("_product.guide.form.php" . URI_Rebuild('?', array('_mode'=>'modify', '_uid'=>$_uid, '_PVSC'=>$_PVSC)));
		break;


	case "delete":
		_MQ_noreturn("delete from smart_product_guide where g_uid='$_uid' ");
		error_loc( "_product.guide.list.php" . ($_PVSC ? '?' : '') .  enc('d' , $_PVSC) );
		break;
}
// - 모드별 처리 ---