<?PHP
	
	# KAY :: 에디터 이미지 관리 :: 파일 생성
	// 에디터 이미지 처리 파일
	ini_set('memory_limit','512M'); // 2020-02-20 SSJ :: 파일용량이 클경우 썸네일 생성 시 메모리 오류 방지
	include "./inc.php";


	// 에디터 이미지 경로
	$app_path_editimg =  $_SERVER['DOCUMENT_ROOT'].IMG_DIR_SMARTEDITOR;

	// 에디터 이미지 정보 추출
	$r =_MQ("
		SELECT		
			eif.eif_img, eif.eif_use_cnt, eif.eif_uid, eiu.eiu_uid
		FROM	smart_editor_images_files as eif
		LEFT JOIN smart_editor_images_use as eiu on (eiu.eiu_eifuid = eif.eif_uid)
		WHERE		
			eif.eif_uid  = '{$_uid}'
	");

	switch($_mode){

		// --- 에디터 이미지 파일수정 ---
		case "modify":

			// 에디터 이미지명 받아오는 변수
			$img_var = "_img_edit_" . $_uid;
			$edit_img= $r['eif_img'];	// 에디터 이미지 명

			// 이미지 수정 시, 이미지 등록 함수
			$res_img = _PhotoProEditorImg( $app_path_editimg , $img_var , $edit_img);

			// 파일관리 DB 파일 수정일 업데이트
			_MQ_noreturn("UPDATE smart_editor_images_files SET eif_rdate = now() WHERE eif_uid = '{$_uid}' "); 

			// $res_img (이미지 등록함수 리턴값)
			// 에디터 이미지를 사용시에만 수정가능
			if(count($r)>0 && $res_img['db_pro']=='update'){
			
				$_img_name = $res_img['name'];	// 수정 이미지명
				$edit_cnt = $r['eif_use_cnt']++;		// 에디터 이미지 사용개수

				// 이미지 업데이트
				_MQ_noreturn("UPDATE smart_editor_images_files SET eif_img = '{$_img_name}', eif_use_cnt='{$edit_cnt}', eif_rdate = now() WHERE eif_uid = '{$_uid}' "); 
			}

			error_frame_loc("_config.editor_img.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null));
			break;
		// --- 에디터 이미지 파일수정 ---



		// --- 전체관리 선택삭제 --- 
		// 파일삭제, 사용관리 DB 삭제, 파일관리 DB 삭제
		case "mass_delete":
			
			// 에디터 이미지 정보추출
			//$editimg_chk = 체크박스로 선택되는 에디터 이미지 ei_uid 개수 체크
			$res = _MQ_assoc("
				SELECT	
					eiu.eiu_uid, eif.eif_img, eif.eif_uid
				FROM	smart_editor_images_files as eif
				LEFT JOIN smart_editor_images_use as eiu on (eiu.eiu_eifuid = eif.eif_uid )
				WHERE
					eif.eif_uid in ('". implode("','" , array_keys($editimg_chk)) ."')
			");

			if(count($res)>0){
				foreach($res as $k =>$r){
					
					// 에디터 이미지 명
					$edit_img = $r['eif_img'];

					$edit_img = iconv("UTF-8", "cp949", $edit_img); // 한글깨짐현상 올릴때와 동일하게 마춰준다.

					_PhotoDel($app_path_editimg, $edit_img);	// 파일삭제
					_MQ_noreturn("DELETE FROM smart_editor_images_use WHERE eiu_uid='{$r['eiu_uid']}' ");	// 사용관리 DB 삭제
					_MQ_noreturn("DELETE FROM smart_editor_images_files WHERE eif_uid='{$r['eif_uid']}' ");	// 파일관리 DB 삭제
				}
			}

			error_frame_loc("_config.editor_img.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null));
			break;
		// --- --- 전체관리 선택삭제 --- 



		// --- 전체관리 개별삭제 ---
		// 파일삭제, 사용관리 DB 삭제, 파일관리 DB 삭제
		case "delete":

			// 에디터 이미지 명
			$edit_img= $r['eif_img'];

			$edit_img = iconv("UTF-8", "cp949", $edit_img); // 한글깨짐현상 올릴때와 동일하게 마춰준다.

			if(count($r)>0){
				_PhotoDel($app_path_editimg, $edit_img);	// 파일삭제
				_MQ_noreturn("DELETE FROM smart_editor_images_use WHERE eiu_uid='{$r['eiu_uid']}' ");	// 사용관리 DB 삭제
				_MQ_noreturn("DELETE FROM smart_editor_images_files WHERE eif_uid='{$r['eif_uid']}' ");	// 파일관리 DB 삭제
			}

			error_frame_loc("_config.editor_img.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : null));
			break;
		// --- 전체관리 개별삭제 ---
	}