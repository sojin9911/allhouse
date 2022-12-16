<?PHP
	include "./inc.php";

	// - 입력수정 사전처리 ---
	if( in_array($_mode, array('add', 'modify')) ) {

		// --사전 체크 ---
		$_type = nullchk($_type , '아이콘 유형을 선택해주시기 바랍니다.');
		$_title = nullchk($_title , '아이콘 타이틀을 입력해주시기 바랍니다.');
		$_idx = nullchk($_idx , '순위를 입력해주시기 바랍니다.');
		// --사전 체크 ---

		// --이미지 처리 ---
		$_imgname = _PhotoPro( '..'.IMG_DIR_ICON , '_img' ) ; // 아이콘이미지
		$_imgname_m = _PhotoPro( '..'.IMG_DIR_ICON , '_img_m' ) ; // 아이콘이미지
		// --이미지 처리 ---


		// --query 사전 준비 ---
		$sque = " 
			 pi_type='{$_type}'
			,pi_img = '{$_imgname}'
			,pi_img_m = '{$_imgname_m}'
			,pi_title='". addslashes($_title) ."'
			,pi_idx = '".rm_str($_idx)."'
		";
		// --query 사전 준비 ---

	}
	// - 입력수정 사전처리 ---

	// - 모드별 처리 ---
	switch( $_mode ){

		case "add":
			_MQ_noreturn("insert smart_product_icon set $sque , pi_rdate=now()");
			$_uid = mysql_insert_id();
			error_loc("_product_icon.form.php" . URI_Rebuild('?', array('_mode'=>'modify', '_uid'=>$_uid, '_PVSC'=>$_PVSC)));
			break;


		case "modify":
			_MQ_noreturn(" update smart_product_icon set  $sque where pi_uid='${_uid}' ");
			error_loc("_product_icon.form.php" . URI_Rebuild('?', array('_mode'=>'modify', '_uid'=>$_uid, '_PVSC'=>$_PVSC)));
			break;


		case "delete":
			// -- 이미지 삭제 ---
			$r = _MQ("select pi_img, pi_img_m from smart_product_icon where pi_uid='${_uid}' ");
			if( $r['pi_img']) {
				_PhotoDel( '..'.IMG_DIR_ICON , $r['pi_img'] );
				_PhotoDel( '..'.IMG_DIR_ICON , $r['pi_img_m'] );
			}
			// -- 이미지 삭제 ---

			_MQ_noreturn("delete from smart_product_icon where pi_uid='$_uid' ");
			error_loc_msg( "_product_icon.list.php" . URI_Rebuild('?'.enc('d' , $_PVSC)) , '정상적으로 삭제되었습니다.');
			break;


		case "mass_delete":
			$chk_uid = array_filter($chk_uid);
			if(sizeof($chk_uid) > 0){
				$res = _MQ_assoc(" select pi_uid, pi_img, pi_img_m from smart_product_icon where pi_uid in ('". implode("','", $chk_uid) ."') ");
				foreach($res as $r){
					// -- 이미지 삭제 ---
					if( $r['pi_img']) {
						_PhotoDel( '..'.IMG_DIR_ICON , $r['pi_img'] );
						_PhotoDel( '..'.IMG_DIR_ICON , $r['pi_img_m'] );
					}
					// -- 이미지 삭제 ---
					_MQ_noreturn("delete from smart_product_icon where pi_uid='". $r['pi_uid'] ."' ");
				}
				error_loc_msg( "_product_icon.list.php" . ($_PVSC ? '?'.enc('d' , $_PVSC) : null) , '정상적으로 삭제되었습니다.');
			}else{
				error_msg('잘못된 접근입니다.');
			}
			break;


		case "sort":
			if($_uid && $_idx){
				_MQ_noreturn("update smart_product_icon set pi_idx = '". rm_str($_idx) ."' where pi_uid='". $_uid ."' ");
				error_loc( "_product_icon.list.php" . ($_PVSC ? '?'.enc('d' , $_PVSC) : null));
			}else{
				error_msg('잘못된 접근입니다.');
			}
			break;
	}
	// - 모드별 처리 ---

?>