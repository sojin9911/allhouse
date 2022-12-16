<?php 
	include_once "./inc.php";

	switch ($ajaxMode) {

		case 'execAddfile': // 폼에서 파일추가를 클릭 시 
			if( $idx < 1){ echo json_encode(array('rst'=>'fail')); exit;  }
			$nextIdx = ($idx+1);
			$html = '	<tr class="tr-files" data-mode="add">';
			$html .= '	<th>첨부파일 <span class="files-idx">'.$nextIdx.'</span></th>';
			$html .= '	<td>';
			$html .= '		<div class="input_file" style="width:250px">';
			$html .= '			<input type="text" id="fakeFileTxt'.$nextIdx.'" class="fakeFileTxt" readonly="readonly" disabled>';
			$html .= '			<div class="fileDiv">';
			$html .= '				<input type="button" class="buttonImg" value="파일찾기" />';
			$html .= '				<input type="file" name="addFile[]" class="realFile" onchange="javascript:document.getElementById(\'fakeFileTxt'.$nextIdx.'\').value = this.value" /> ';
			$html .= '			</div>';
			$html .= '		</div>';
			$html .= '		<a href="#none" onclick="return false;" class="c_btn h27 icon icon_minus_b exec-delfile">삭제</a>';
			$html .= '	</td>';
			$html .= '</tr>';
			echo json_encode(array('rst'=>'success','idx'=>$idx,'nextIdx'=>$nextIdx,'html'=>$html)); exit;
			
		break;

		case 'setAdminTemplate': // 관리자 템플릿
			// -- 쇼핑몰 게시물 양식  정보를 가져온다. 
			$rowShopTemplate = _MQ("select *from smart_bbs_template where bt_type = 'admin' and bt_uid = '".$btuid."'  ");
			if( count($rowShopTemplate) < 1){ echo json_encode(array('rst'=>'선택하신 관리자 게시글 양식이 존재하지 않습니다.')); exit; }
			$_title = stripslashes($rowShopTemplate['bt_title']); // 게시글양식이 있을경우 :: 추가일시에만 적용
			$_content = stripslashes($rowShopTemplate['bt_content']); // 게시글양식이 있을경우 :: 추가일시에만 적용
			echo json_encode(array('rst'=>'success', '_title'=>$_title,'_content'=>$_content)); exit;
		break;

		// -- 댓글등록
		case 'addComment': 
			// -- 게시물 조회 
			$rowChk = _MQ("select count(*) as cnt from smart_bbs where b_uid = '".$_buid."' ");
			if( $rowChk['cnt'] < 1){ echo json_encode(array('rst'=>'fail','msg'=>'게시글이 존재하지 않습니다.')); exit;  }
			$shopAdminInfo = shopAdminInfo(); // 쇼핑몰 관리자 계정정보 호출 :: smart_individual and level is 9
			$_depth = rm_str($_depth) < 1 ? 1 : $_depth;
			$_relation = rm_str($_relation) < 1 ? 0 : $_relation;  

			if( mb_strlen($_content) > $varCommentWriteLen){ echo json_encode(array('rst'=>'fail','msg'=>'댓글내용은 '.$varCommentWriteLen.'자 이하로 입력해 주세요.')); exit;}

			$que = "
				bt_buid = '".$_buid."'
				, bt_writer = '".$shopAdminInfo['in_name']."'
				, bt_inid = '".$shopAdminInfo['in_id']."'
				, bt_content = '".addslashes( htmlspecialchars($_content) )."'
				, bt_depth = '".$_depth."'
				, bt_relation= '".$_relation."'
				, bt_reginfo_ip= '".$_SERVER['REMOTE_ADDR']."'
				, bt_rdate = now()
			";
			_MQ_noreturn("insert smart_bbs_comment set ".$que);

			// -- 댓글개수 업데이트 
			update_board_comment_cnt($_buid);

			echo json_encode(array('rst'=>'success','msg'=>'댓글이 등록되었습니다.')); exit;
		break;

		// -- 댓글수정
		case "modifyComment":

			$rowChk = _MQ("select count(*) as cnt from smart_bbs_comment where bt_uid= '".$_uid."'");
			if( $rowChk['cnt'] < 1){ echo json_encode(array('rst'=>'fail','msg'=>'수정하실 댓글이 존재하지 않습니다.')); exit; }

			if( mb_strlen($_content) > $varCommentWriteLen){ echo json_encode(array('rst'=>'fail','msg'=>'댓글내용은 '.$varCommentWriteLen.'자 이하로 입력해 주세요.')); exit;}

			$que = "
				bt_content = '".addslashes( htmlspecialchars($_content) )."'
			";
			_MQ_noreturn("update smart_bbs_comment set ".$que." where bt_uid = '".$_uid."'  ");
			echo json_encode(array('rst'=>'success','msg'=>'댓글이 수정되었습니다.'));
			exit;
		break;

		// -- 댓글삭제
		case "deleteComment":

			$rowChk = _MQ("select count(*) as cnt from smart_bbs_comment where bt_uid= '".$_uid."'");
			if( $rowChk['cnt'] < 1){ echo json_encode(array('rst'=>'fail','msg'=>'수정하실 댓글이 존재하지 않습니다.')); exit; }
			_MQ_noreturn("delete from smart_bbs_comment where bt_uid = '".$_uid."' ");

			// -- 댓글개수 업데이트 
			update_board_comment_cnt($_buid);

			echo json_encode(array('rst'=>'success','msg'=>'댓글이 삭제되었습니다.'));
			exit;		
		break;


		// -- 댓글보기
		case 'listComment':

			// 데이터 조회
			$listpg = rm_str($ahref);
			if(!rm_str($listpg)) $listpg = 1;

			if(!$listmaxcount) $listmaxcount = 20;
			if(!$listpg) $listpg = 1;
			$count = $listpg * $listmaxcount - $listmaxcount;
			$row = _MQ("select count(*) as cnt from smart_bbs_comment where bt_buid=  '".$_buid."' ");
			$TotalCount = $row['cnt'];
			$Page = ceil($TotalCount/$listmaxcount);
			$res = _MQ_assoc("select *from smart_bbs_comment where bt_buid=  '".$_buid."' order by bt_rdate desc limit ".$count." , ".$listmaxcount."  ");

			if( count($res) < 1){ echo json_encode(array('rst'=>'noneData')); exit; }

			$printList = '<div class="dash_line"></div>';
			$printList .= '<table class="table_list">';
			$printList .= '	<colgroup>';
			$printList .= '	<col width="70"/><col width="150"/><col width="*"/><col width="80"/><col width="120"/>';
			$printList .= '	</colgroup>';
			$printList .= '	<thead>';
			$printList .= '		<tr>
									<th scope="col">NO</th>
									<th scope="col">작성자</th>
									<th scope="col">내용</th>
									<th scope="col">작성일</th>
									<th scope="col">관리</th>
								</tr>';
			$printList .= '	</thead>';
			$printList .= '	<tbody>';

			foreach($res as $k=>$v){
				$_num = $TotalCount - $count - $k ;
				$_num = number_format($_num);
				$printList .= '		<tr class="comment-list-item">';
				$printList .= '			<td class="">'.$_num.'</td>';
				$printList .= '			<td class="">'.showUserInfo($v['bt_inid'],$v['bt_writer'],$v).'</td>';
				$printList .= '			<td class="t_left">
											<div class="comment-content" data-uid="'.$v['bt_uid'].'">'.nl2br(stripslashes($v['bt_content'])).'</div>
											<table class="table_list modify-comment-form" style="display:none;" data-uid="'.$v['bt_uid'].'">
												<colgroup>
													<col width="*"/><col width="120"/>
												</colgroup>
												<tbody>
													<tr>
														<td class="t_left">
															<textarea id="modify-comment-content" data-uid="'.$v['bt_uid'].'" rows="3" cols="" class="design" style="resize:none;" >'.stripslashes($v['bt_content']).'</textarea>				
														</td>
														<td>
															<div class="lineup-vertical">
																<a href="#none" class="c_btn h34 modify-comment-submit" data-uid="'.$v['bt_uid'].'">등록</a>
																<a href="#none" class="c_btn h34 gray cancel-comment-item" data-uid="'.$v['bt_uid'].'">취소</a>
															</div>
														</td>
													</tr>
												</tbody> 
											</table>												
										</td>';
				$printList .= '			<td class="">'.printDateInfo($v['bt_rdate']).'</td>';
				$printList .= '			<td>';
				$printList .= '				<div class="lineup-vertical">';
				$printList .= '					<a href="#none" onclick="return false;" class="c_btn h22 white modify-comment-item" data-uid = "'.$v['bt_uid'].'">수정</a>';
				$printList .= '					<a href="#none" onclick="return false;" class="c_btn h22 gray delete-comment-item" data-uid = "'.$v['bt_uid'].'">삭제</a>';
				$printList .= '				</div>';
				$printList .= '			</td>';
				$printList .= '		</tr>';
			}

			$printList .= '	</tbody>';
			$printList .= '</table>';			

		 $printList .= '
				<div class="paginate">'.pagelisting($listpg, $Page, $listmaxcount, "?{$_PVS}&listpg=", 'Y').'</div>
		 ';

		echo json_encode(array('rst'=>'success','html'=>$printList,'paginate'=>$printPaginate));
		break;

	}

?>