<?PHP
	include "./inc.php";

	// - 입력수정 사전처리 ---
	if( $_mode <> "delete"  ) {

		// --사전 체크 ---
		$p_idx = nullchk($p_idx , "순위를 선택해주시기 바랍니다.");
		if($p_none_limit=='N'){
			$p_sdate = nullchk($p_sdate , "시작일을 입력해주시기 바랍니다.");
			$p_edate = nullchk($p_edate , "종료일을 입력해주시기 바랍니다.");
		}
		//$p_link = str_replace("http://" , "" , $p_link); // http:// 제거 // 2019-11-07 SSJ :: 외부링크 허용
		$p_mode = ($p_mode?$p_mode:'I');
		$p_bgcolor = strtoupper('#'.str_replace('#', '', ($p_bgcolor?$p_bgcolor:'ffffff'))); // 배경색 :: 기본 #FFFFFF
		$p_width = rm_comma($p_width);
		$p_width = ((int)$p_width >= 350?$p_width:350); // 팝업 가로크기 :: 최소 350
		$p_height = rm_comma($p_height);
		$p_height = ((int)$p_height >= 250?$p_height:250); // 팝업 세로크기 :: 최소 250
		// --사전 체크 ---

		// --이미지 처리 ---
		$_imgname = _PhotoPro( "..".IMG_DIR_POPUP , "p_img" ) ; // 이미지
		// --이미지 처리 ---


		// --query 사전 준비 ---
		$sque = "
			 p_img = '{$_imgname}'
			,p_link='{$p_link}'
			,p_target='{$p_target}'
			,p_view='{$p_view}'
			,p_title='{$p_title}'
			,p_idx = '".$p_idx."'
			,p_left = '".rm_comma($p_left)."'
			,p_top = '".rm_comma($p_top)."'
			,p_mtop = '".rm_comma($p_mtop)."'
			,p_sdate = '".$p_sdate."'
			,p_edate = '".$p_edate."'
			,p_none_limit = '".$p_none_limit."'
			,p_type = '".$p_type."'

			, p_mode = '{$p_mode}'
			, p_bgcolor = '{$p_bgcolor}'
			, p_content = '{$p_content}'
			, p_width = '{$p_width}'
			, p_height = '{$p_height}'
		";
		// --query 사전 준비 ---

	}
	// - 입력수정 사전처리 ---



	// - 모드별 처리 ---
	switch( $_mode ){

		case "add":
			_MQ_noreturn("insert smart_popup set $sque , p_rdate=now()");
			$_uid = mysql_insert_id();

			// KAY :: 에디터 이미지관리 :: 2021-06-02 -------------
			editor_img_ex($p_content , "popup" , $_uid);

			error_loc("_popup.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
			break;

		case "modify":
			_MQ_noreturn(" update smart_popup set  $sque where p_uid='${_uid}' ");

			// KAY :: 에디터 이미지관리 :: 2021-06-02 -------------
			editor_img_ex($p_content , "popup" , $_uid);

			error_loc("_popup.form.php?_mode=modify&_uid=${_uid}&_PVSC=${_PVSC}");
			break;

		case "delete":

			// -- 이미지 삭제 ---
			$r = _MQ("select p_img  from smart_popup where p_uid='${_uid}' ");
			if( $r['p_img']) {
			_PhotoDel( "../../upfiles/popup" , $r['p_img'] );
			}
			// -- 이미지 삭제 ---

			// KAY :: 에디터 이미지 관리 :: 에디터 이미지 사용관리 DB삭제, 파일관리 사용개수 업데이트 :: 2021-07-07
			editor_img_del($_uid,'popup');

			_MQ_noreturn("delete from smart_popup where p_uid='$_uid' ");
			error_loc( "_popup.list.php?" . enc('d' , $_PVSC) );
			break;
	}
	// - 모드별 처리 ---

?>