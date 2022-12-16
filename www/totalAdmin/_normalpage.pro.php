<?PHP
include_once("inc.php");


// 2019-11-29 SSJ :: 노출 메뉴 추가
if($is_colmn_menu == 'N'){
	// 한번더 체크
	$trigger_colmn_menu = false;
	$chk = _MQ_assoc(" desc smart_normal_page ");
	if(count($chk) > 0){
		foreach($chk as $k=>$v){
			if($v['Field'] == 'np_menu'){
				$trigger_colmn_menu = true;
				break;
			}
		}
	}
	// db 추가
	if($trigger_colmn_menu === false){
		_MQ_noreturn(" alter table smart_normal_page add column `np_menu` varchar(30) not null default 'default' comment '노출메뉴' ");
		_MQ_noreturn(" alter table smart_normal_page add index(`np_menu`) ");
	}
}


// - 입력수정 사전처리 ---
if(in_array($_mode , array("add" , "modify"))) {

	// --사전 체크 ---
	$_id = nullchk($_id, "페이지아이디를 입력하시기 바랍니다.");
	$_view = nullchk($_view, "노출여부을 선택하시기 바랍니다.");
	$_idx = nullchk($_idx, "노출순위를 입력하시기 바랍니다.");
	$_title = nullchk($_title, "페이지명을 입력하시기 바랍니다.");
	$_content = nullchk($_content, "페이지 내용을 입력하시기 바랍니다.");
	// --사전 체크 ---

	// --이미지 처리 ---
	$_header_img_name = _PhotoPro( "..".IMG_DIR_NORMAL , "_header_img" ) ; // 상단이미지
	$_footer_img_name = _PhotoPro( "..".IMG_DIR_NORMAL , "_footer_img" ) ; // 하단이미지
	// --이미지 처리 ---

	// --query 사전 준비 ---
	$sque = "
		np_view = '" . $_view . "',
		np_id = '" . $_id . "',
		np_idx = '" . $_idx . "',
		np_title = '" . $_title . "',
		np_content = '" . $_content . "',
		np_content_m = '" . $_content_m . "',
		np_header_img = '{$_header_img_name}',
		np_footer_img = '{$_footer_img_name}',
		np_use_content = '". ($_use_content?$_use_content:'N') ."'
	"; // LDD005 수정
	// --query 사전 준비 ---

	// 2019-11-29 SSJ :: 노출 메뉴 저장
	if($_menu <> '') $sque .= " ,np_menu = '". $_menu ."' ";

}
// - 입력수정 사전처리 ---



// - 모드별 처리 ---
switch( $_mode ){

	case "add":
		_MQ_noreturn("insert smart_normal_page set {$sque} , np_rdate=now()");
		$_uid = mysql_insert_id();

		// KAY :: 에디터 이미지 관리 :: 에디터 이미지 등록함수 :: 2021-06-02 ----------
		editor_img_ex($_content.$_content_m , 'normal' , $_uid);

		error_loc("_normalpage.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
		break;

	case "modify":
		_MQ_noreturn(" update smart_normal_page set {$sque} where np_uid='${_uid}' ");

		// KAY :: 에디터 이미지관리 :: 에디터 이미지 등록함수 :: 2021-06-02 ----------
		editor_img_ex($_content.$_content_m , 'normal' , $_uid);

		error_loc("_normalpage.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
		break;

	case "delete":
		// -- 이미지 삭제 ---
		$r = _MQ("select np_header_img, np_footer_img from smart_normal_page where np_uid='${_uid}' ");

		if( $r['np_header_img']) _PhotoDel( "..".IMG_DIR_NORMAL , $r[np_header_img] );
		if( $r['np_footer_img']) _PhotoDel( "..".IMG_DIR_NORMAL , $r[np_footer_img] );
		// -- 이미지 삭제 ---

		// KAY :: 에디터 이미지 관리 :: 에디터 이미지 사용관리 DB삭제, 파일관리 사용개수 업데이트 :: 2021-07-07
		editor_img_del($_uid,'normal');

		_MQ_noreturn("delete from smart_normal_page where np_uid='{$_uid}' ");
		error_loc("_normalpage.list.php?".enc('d' , $_PVSC));
		break;


	// - 페이지 아이디 체크 ---
	case "idchk":
		if(preg_match("/^[a-zA-Z0-9]*$/",$_id) == false ){ die('en'); }

		$r = _MQ("select count(*) as cnt from smart_normal_page where np_id='". $_id ."' ");
		if($r['cnt'] > 0 ) {

			echo "no";//중복 아이디 있음 - 사용불가
		}
		else {

			echo "yes";//중복 아이디 없음 - 사용가능
		}
		exit;
		break;
	// - 페이지 아이디 체크 ---

}
// - 모드별 처리 ---