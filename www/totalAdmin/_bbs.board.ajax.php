<?php
	include_once './inc.php';

	switch ($ajaxMode) {

		case 'chkUid': //고유번호 체크

			if( $chkUid == ''){ echo json_encode(array('rst'=>'fail','msg'=>'게시판 아이디를 입력해 주세요.','key'=>'_uid')); exit; } // 비었을 경우

			if( preg_match("/^[a-zA-Z0-9_]*$/",$chkUid) == false ){ echo json_encode(array('rst'=>'fail','msg'=>'게시판 아이디는 영문(대문자, 소문자), 숫자, 언더바(_)만 사용 가능합니다.','key'=>'_uid')); exit; }

			$rowChk = _MQ("select count(*) as cnt from smart_bbs_info where bi_uid = '".$chkUid."' ");
			if( $rowChk['cnt'] < 1){ echo json_encode(array('rst'=>'success')); exit; } // 중복이 아닐경우
			else{ echo json_encode(array('rst'=>'fail','msg'=>'이미 사용중인 게시판 아이디 입니다. 다른아이디를 입력해 주세요.','key'=>'_uid')); exit; }    // 중복일경우
		break;

		case 'selectSkin': // 스킨선택 시

			$htmlSkin =  $htmlSkinMobile = "";


			$skinInfo = getBoardSkinInfo($_skinName,$agent);

			// 	<div class="dash_line"><!-- 점선라인 --></div>
			if( count($skinInfo) > 0 && $_skinName != '' ){
				$htmlSkin .='<div class="order_item">';

				$htmlSkin .='	<div class="bold">';
				$skinOption = $skinInfo[$_skinName]['skin']; // 변수를 짧게 줄인다

				$htmlSkin .=$skinOption['title'];
				$htmlSkin .='	</div> <!-- end bold -->';

				$htmlSkin .=' <div class="dash_line"><!-- 점선라인 --></div>';

				$htmlSkin .='	<table class="table_form ">';
				$htmlSkin .='		<colgroup><col width="180"><col width="*"></colgroup>';
				$htmlSkin .='		<tbody>';
				if($skinOption['board_thumb'] != '' ){
					$htmlSkin .='			<tr>';
					$htmlSkin .='				<th>미리보기</th>';
					$htmlSkin .='
															<td>
																<div class="preview_thumb">
																    <img src="'.$skinOption['board_thumb'].'" class="js_thumb_img" data-img="'.$skinOption['board_thumb'].'" alt=""><!-- 클릭하면 이미지 새창 -->
																    <a href="#none" class="c_btn h27 js_thumb_popup" data-img="'.$skinOption['board_thumb'].'">이미지 보기</a>
																</div>
															</td>
					';
					$htmlSkin .='			</tr>';
				}

				$htmlSkin .='			<tr>';
				$htmlSkin .='				<th>게시판 설명</th>';
				$htmlSkin .='				<td>'.$skinOption['info'].'</td>';
				$htmlSkin .='			</tr>';

				$htmlSkin .='			<tr>';
				$htmlSkin .='				<th>파일첨부</th>';
				$htmlSkin .='				<td>'.($skinOption['file'] == 'true' ? '<span class="c_tag blue h22">가능</span>':'<span class="c_tag gray h22">불가능</span>').'</td>';
				$htmlSkin .='			</tr>';

				$htmlSkin .='			<tr>';
				$htmlSkin .='				<th>이미지 첨부</th>';
				$htmlSkin .='				<td>'.($skinOption['images'] == 'true' ? '<span class="c_tag blue h22">가능</span>':'<span class="c_tag gray h22">불가능</span>').'</td>';
				$htmlSkin .='			</tr>';

				// -- 이미지첨부가 가능일 시 권장크기를 노출
				if($skinOption['images'] == 'true') {

					$temp_images_view = array('list'=>'게시물 리스트','view'=>'게시물 본문');
					if( $skinOption['images_view'] != ''){
						$htmlSkin .='			<tr>';
						$htmlSkin .='				<th>이미지 노출위치</th>';
						$htmlSkin .='				<td>'.($temp_images_view[$skinOption['images_view']]).'</td>';
						$htmlSkin .='			</tr>';
					}

					if( $skinOption['images_width'] != ''){
						$htmlSkin .='			<tr>';
						$htmlSkin .='				<th>이미지 권장크기(가로x세로)</th>';
						$htmlSkin .='				<td>'.$skinOption['images_width'].' x '.$skinOption['images_height'].' (pixel)</td>';
						$htmlSkin .='			</tr>';
					}
				}


				// -- 기간이벤트 사용여부
				if($skinOption['date'] != '') {
					$htmlSkin .='			<tr>';
					$htmlSkin .='				<th>기간 옵션</th>';
					$htmlSkin .='				<td>'.($skinOption['date'] == 'true' ? '<span class="c_tag blue h22">사용</span>':'<span class="c_tag gray h22">미사용</span>').'</td>';
					$htmlSkin .='			</tr>';
				}

				$htmlSkin .='		</tbody>';
				$htmlSkin .='	</table>';

				$htmlSkin .='</div> <!-- end order_item -->';
			}else{
				$htmlSkin .= '<div class="common_none"><div class="no_icon"></div><div class="gtxt">스킨 정보가 없습니다.</div></div>';
			}



			// -- 스킨특성 기능 사용여부 추가
			$arrSkinOption = array();
			$skinInfo = getBoardSkinInfo($_skinName,'pc');
			$skinOptionDefault = $skinInfo[$_skinName]['skin']; // PC 기준으로 가져온다.
			if( $skinOptionDefault['file'] == 'true'){ $arrSkinOption[] = 'upload-file'; }
			if( $skinOptionDefault['images'] == 'true'){ $arrSkinOption[] = 'upload-images'; }
			if( $skinOptionDefault['date'] == 'true'){ $arrSkinOption[] = 'option-date'; }
			if ( in_array($skinOptionDefault['type'],array('qna')) == false){ $arrSkinOption[] = 'option-comment'; }

			// -- 스킨타입
			$skinType =$skinOptionDefault['type'];

			echo json_encode(array('rst'=>'success','htmlSkin'=>$htmlSkin,'skinOption'=>$arrSkinOption,'skinType'=>$skinType)); exit;



		break;

	}

?>