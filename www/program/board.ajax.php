<?php
# 게시글 처리 프로세스
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

switch( $ajaxMode ){

	// 등록
	case "execAddfile":
		if( $idx < 1){ echo json_encode(array('rst'=>'fail')); exit;  }
		$nextIdx = ($idx+1);
		$html = '	<div class="form_file list-files" data-mode="add">';
		$html .= '	<div class="input_file_box">';
		$html .= '		<input type="text" id="fakeFileTxt'.$nextIdx.'" class="fakeFileTxt" readonly="readonly" disabled="" placeholder="'.implode(",",$arrUpfileConfig['ext']['file']).'파일만 등록 가능합니다. 용량이 많을때에는 파일만 대용량이메일로 보내주시기 바랍니다.">';
		$html .= '		<div class="fileDiv">';
		$html .= '			<input type="button" class="buttonImg" value="파일찾기">';
		$html .= '			<input type="file" class="realFile" name="addFile[]" onchange="javascript:document.getElementById(\'fakeFileTxt'.$nextIdx.'\').value = this.value">';
		$html .= '		</div>';
		$html .= '	</div>';
		$html .= '	<span class="add_btn_box"><a href="#none" onclick="return false;" class="c_btn h30 dark line exec-delfile">- 삭제</a></span>';
		$html .= '	</div>';

		echo json_encode(array('rst'=>'success','idx'=>$idx,'nextIdx'=>$nextIdx,'html'=>$html)); exit;
	break;

	// -- 금지어 체크
	case "chkForbidden":

		// -- 관리자의 경우 금지어에 해당되지 않는다.
		if( is_admin() == true){
			echo json_encode(array('rst'=>'success')); exit;
		}

		// -- 금지어를 가져 온다 :: serialize
		if( $siteInfo['s_bbs_forbidden_word'] != ''){
			$forbiddenData = unserialize(stripslashes($siteInfo['s_bbs_forbidden_word']));
			$arrFw['writer'] = 	$forbiddenData['writer'] != '' ? explode(",",$forbiddenData['writer']) : array() ; 
			$arrFw['title'] = 		$forbiddenData['title'] != '' ? explode(",",$forbiddenData['title']) : array() ; 
			$arrFw['content'] = 	$forbiddenData['content'] != '' ? explode(",",$forbiddenData['content']) : array() ; 
		}

		$arrForbidden = array();
		foreach($arrFw['writer'] as $k=>$v){$arrForbidden['writer'][$v] = '';}
		foreach($arrFw['title'] as $k=>$v){$arrForbidden['title'][$v] = '';}
		foreach($arrFw['content'] as $k=>$v){$arrForbidden['content'][$v] = '';}

		// -- 글쓴이 금지어 체크 :: 있을 경우에만 체크한다.
		if( count($arrFw['writer']) > 0){ 
			$chkWriter = str_replace(array_keys($arrForbidden['writer']), array_values($arrForbidden['writer']), $_writer); 
			if($chkWriter != $_writer){ echo json_encode(array('rst'=>'fail','msg'=>"게시물 작성자에  금지어가 포함되어 있습니다.",'key'=>'_writer')); exit;  }
		}

		// -- 글제목 체크 :: 있을 경우에만 체크한다.
		if( count($arrFw['title']) > 0){ 
			$chkTitle = str_replace(array_keys($arrForbidden['title']), array_values($arrForbidden['title']), $_title); 
			if($chkTitle != $_title){ echo json_encode(array('rst'=>'fail','msg'=>"게시물 제목에 금지어가 포함되어 있습니다.",'key'=>'_title')); exit;  }
		}

		// -- 글내용 금지어 체크 :: 있을 경우에만 체크한다.
		if( count($arrFw['content']) > 0){ 
			$chkContent = str_replace(array_keys($arrForbidden['content']), array_values($arrForbidden['content']), $_content); 
			if($chkContent != $_content){ echo json_encode(array('rst'=>'fail','msg'=>"게시물 내용에 금지어가 포함되어 있습니다.",'key'=>'_content')); exit;  }
		}

		echo json_encode(array('rst'=>'success')); exit;

	break;
}