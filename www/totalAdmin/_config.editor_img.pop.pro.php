<?PHP
	// --- KAY :: 에디터 이미지 관리 :: 2021-06-10 ---
	//		에디터 이미지 개별관리 처리 파일

	ini_set('memory_limit','512M'); // 2020-02-20 SSJ :: 파일용량이 클경우 썸네일 생성 시 메모리 오류 방지
	include "./inc.php";

	 // 에디터 이미지 경로
	$app_path_editimg =  $_SERVER['DOCUMENT_ROOT'].IMG_DIR_SMARTEDITOR;

	// 사용되는 에디터 이미지 정보 추출
	$r =_MQ("
		SELECT		
			eiu.eiu_eifuid, eif.eif_use_cnt, eif.eif_img
		FROM	smart_editor_images_use as eiu
		LEFT JOIN smart_editor_images_files as eif on (eiu.eiu_eifuid = eif.eif_uid )
		WHERE	
			eiu.eiu_uid = '{$_edit_uid}'	
	");

	switch($_mode){

		// --- 에디터 이미지 파일수정 ---
		case "modify":

			//	이미지명, 고유번호 변수
			$img_var = "_img_edit_" . $_edit_uid;
			
			// 에디터 이미지 명
			$edit_img = $r['eif_img'];

			// 에디터 이미지 등록 함수
			$res_img = _PhotoProEditorImg( $app_path_editimg , $img_var , $edit_img);

			// 수정 시 eif_rdate 현재로 업데이트
			_MQ_noreturn("UPDATE smart_editor_images_files SET eif_rdate = now() WHERE eif_uid = '{$_uid}' "); 

			// $res_img (이미지 등록 함수 리턴값)
			if($res_img['db_pro']=='update'){
				$_imgname = $res_img['name'];// 이미지명
				// 파일관리 DB 업데이트 ( 사용할때만 노출되기때문에 개별관리 수정 시 개수 업데이트 x )
				_MQ_noreturn("UPDATE smart_editor_images_files SET eif_img = '{$_imgname}' , eif_rdate = now() WHERE  eif_uid='{$r['eiu_eifuid']}' ");
			}

			error_frame_loc("_config.editor_img.pop.php?_uid=" . $_uid ."&tn=".$_tn);
			break;
		// --- 에디터 이미지 파일수정 ---


		// --- 에디터 이미지 개별삭제 ---
		// 파일 개수 줄이기, 에디터 이미지 사용관리 DB삭제
		case "delete":

			// 파일관리에서 사용 개수만 줄임
			$use_cnt = $r['eif_use_cnt'];
			$use_cnt--;

			if($use_cnt >=0){
				// 에디터 이미지 사용관리 DB 삭제 , 파일관리 DB 사용개수 업데이트
				_MQ_noreturn("UPDATE smart_editor_images_files SET eif_use_cnt = '{$use_cnt}' WHERE  eif_uid='{$r['eiu_eifuid']}' "); // 파일관리 DB 업데이트
				_MQ_noreturn("DELETE FROM smart_editor_images_use WHERE eiu_uid='{$_edit_uid}' ");		// 사용관리 DB 삭제
			}

			error_frame_loc("_config.editor_img.pop.php?_uid=". $_uid."&tn=".$_tn);
			break;
		// --- 에디터 이미지 개별삭제 ---

	}