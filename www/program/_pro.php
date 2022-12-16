<?php
# 기타 프로세스(팝업, 히트등등)
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

if($_mode == 'eval_hit') { // 상품평점 hit 카운트
	if($_smode == 'update') {
		$_uid = preg_replace("/[^0-9]*/s", "", $_uid);
		$_uid = (int)$_uid;
		_MQ_noreturn(" update `smart_product_talk` set `pt_hit` = pt_hit+1 where `pt_uid` = '{$_uid}' ");
	}
}
else if($_mode == 'popup_close') { // 팝업닫기
	if(!$uid) die('error');
	$uid = preg_replace("/[^0-9]*/s", "", $uid);
	$uid = (int)$uid;
	samesiteCookie("AuthPopupClose_".$uid, 'Y', (time()+3600*24), '/', '.'.str_replace("www." , "" , reset(explode(':', $system['host']))));
	die('success');
}
else if($_mode == 'intro_skip') { // 인트로 스킵
	samesiteCookie('intro_skip', 'Y', (time()+3600*24), '/', '.'.str_replace("www." , "" , reset(explode(':', $system['host']))));
	die('success');
}
else if($_mode == 'request_add_files') { // 1:1 문의 파일

	if($idx < 1) die(json_encode(array('rst'=>'fail')));
	$nextIdx = ($idx+1);
	$html = '	<div class="form_file list-files" data-mode="add">';
	$html .= '	<div class="input_file_box">';
	$html .= '		<input type="text" id="fakeFileTxt'.$nextIdx.'" class="fakeFileTxt" readonly="readonly" disabled="" placeholder="용량이 많을때에는 파일만 대용량이메일로 보내주시기 바랍니다.">';
	$html .= '		<div class="fileDiv">';
	$html .= '			<input type="button" class="buttonImg" value="파일찾기">';
	$html .= '			<input type="file" class="realFile" name="addFile[]" onchange="javascript:document.getElementById(\'fakeFileTxt'.$nextIdx.'\').value = this.value">';
	$html .= '		</div>';
	$html .= '	</div>';
	$html .= '	<span class="add_btn_box"><a href="#none" onclick="return false;" class="c_btn h30 dark line exec-delfile">- 삭제</a></span>';
	$html .= '	</div>';

	die(json_encode(array('rst'=>'success','idx'=>$idx,'nextIdx'=>$nextIdx,'html'=>$html)));
}
else if($_mode == 'get_pstock') {
    // 2019-07-24 SSJ :: 현재 상품 재고를 추출한다
    // -- 옵션타입에 상관없이 p_stock을 반환한다
    // -- 옵션을 사용하지 않는 상품만 호출
    $r = _MQ(" select p_stock as cnt from smart_product where p_code = '". $pcode ."' ");
    $stock = (string) ($r['cnt']*1);
    die($stock);
}