<?php

	// -- LCY -- 게시글목록 + 댓글
	include_once('wrap.header.php');

	// 추가파라메터
	if(!$arr_param) $arr_param = array();

	// 넘길 변수 설정하기
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {
		if(is_array($val)) {
			foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }
		}
		else {
			$_PVS .= "&$key=$val";
		}
	}
	$_PVSC = enc('e' , $_PVS);
	// 넘길 변수 설정하기

	// -- 2019-04-10 SSJ :: 이미지를 사용하는 게시판 추출 ----
	$boardSkinInfo = getBoardSkinInfo(); // 게시판정보를 불러온다
	$boardImagesUse = array();
	if(count($boardSkinInfo) > 0){
		foreach($boardSkinInfo as $k=>$v){
			if($v['skin']['images'] == 'true'){
				$boardImagesUse[] = $k;
			}
		}
	}
	// -- 2019-04-10 SSJ :: 이미지를 사용하는 게시판 추출 ----

	// -- 게시판 정보를 불러온다.
	$getBoardList = get_board_list_array(false,true);

	// -- 게시판 필수 선택으로 지정
	$select_menu = in_array($select_menu,array_keys($getBoardList)) == false ? array_shift(array_keys($getBoardList)):$select_menu;
	if( $select_menu == '') $arr_param['select_menu'] = $select_menu;

	// 검색 체크
	$s_query = "
		from smart_bbs as b
		inner join smart_bbs_info as bi on (bi.bi_uid = b.b_menu)
		left join smart_individual as ind on (ind.in_id=b.b_inid)
		where 1 and b_menu = '".$select_menu."'
	";



	// -- 검색시작 -- {{{
	if( $searchMode == 'true') {
		// -- 검색어
		if($pass_input_type == 'id'){ // 등록자 아이디
			$s_query .= " and b_writer_type = 'member' and b_inid = '".$pass_input."' ";
		}else if( $pass_input_type == 'writer'){ // 등록자명
			$s_query .= " and b_writer like '%".$pass_input."%' ";
		}else if($pass_input_type == 'title'){ // 게시물 제목
			$s_query .= " and b_title like '%".$pass_input."%' ";
		}else if( $pass_input_type == 'all'){ // 전체검색
			$s_query .= " and ( (b_writer_type = 'member' and b_inid = '".$pass_input."') or b_writer like '%".$pass_input."%' or b_title like '%".$pass_input."%'  ) ";
		}
		if( $pass_view_type!= '') {  $s_query .= " and bi_view_type= '".$pass_view_type."'  "; }// -- 노출구분

		// 등록일 검색
		if( $pass_sdate != '' && $pass_edate != ''){
			$s_query .= " and  ( left(b_rdate,10) BETWEEN '".$pass_sdate."' and '".$pass_edate."' ) ";
		}else{
			if($pass_sdate != ''){
				$s_query .= " and left(b_rdate,10) >= '".$pass_sdate."' ";
			}

			if($pass_edate != ''){
				$s_query .= " and left(b_rdate,10) <= '".$pass_edate."' ";
			}
		}

		//카테고리 검색
		if( $pass_category!= '') {  $s_query .= " and b_category= '".$pass_category."'  "; }

	}
	// -- 검색종료 -- }}}

	$boardInfo = get_board_info($select_menu); // 게시판정보 추출
	$replyMode = in_array($boardInfo['bi_list_type'],array('qna')); // reply 모드판별

	if( $replyMode === true){ // 게시판의 형태가 qna 와 같이 답글이 필요없을경우
		$s_query .= " and b_depth = '1' ";

	}else{

	}

	if(!$listmaxcount) $listmaxcount = 50;
	// /$listmaxcount = 3;
	if(!$listpg) $listpg = 1;
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스

	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);
	$que = " select b.* ,bi.*, ind.in_name, CASE b_depth WHEN 1 THEN b_uid ELSE b_relation END as b_orderuid ".$s_query." ORDER BY b_notice='Y' desc, b_orderuid desc , b_depth asc limit ".$count." , ".$listmaxcount;
	$res = _MQ_assoc($que);

?>



	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<!-- 단락타이틀 -->
	<div class="group_title">
		<strong>게시글 검색</strong><!-- 메뉴얼로 링크 --><?=openMenualLink('게시글검색')?>
		<!-- 해당페이지의 등록/업로드 버튼 있을 경우 -->
		<div class="btn_box"><a href="_bbs.post_mng.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC,'select_menu'=>$select_menu)); ?>" class="c_btn h46 red" accesskey="a">글등록</a></div>
	</div>

	<form name="searchfrm" id="searchfrm" method=get action='<?=$_SERVER["PHP_SELF"]?>'>
	<input type=hidden name="searchMode" value="true">
	<input type="hidden" name="st" value="<?php echo $st; ?>">
	<input type="hidden" name="so" value="<?php echo $so; ?>">
	<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
	<?php if(sizeof($arr_param)>0){ foreach($arr_param as $__k=>$__v){ ?>
	<input type="hidden" name="<?php echo $__k; ?>" value="<?php echo $__v; ?>">
	<?php }} ?>

		<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form if_search">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
				</colgroup>
				<tbody>

					<tr>
						<th>게시판선택</th>
						<td>
							<?php echo _InputSelect( "select_menu" , array_keys($getBoardList) , $select_menu, "" , array_values($getBoardList) , "-게시판선택-"); ?>
							<?php
								// KAY :: 게시판 카테고리설정
								$_categoryload = array_filter(explode(",",$boardInfo['bi_category']));
								if($boardInfo['bi_category_use']=='Y' && $_categoryload){
									echo _InputSelect("pass_category", array_values($_categoryload) ,$pass_category,"", array_values($_categoryload) ,"-카테고리선택-") ;
								 }
							 ?>
						</td>
						<th>검색일자</th>
						<td>
							<input type="text" name="pass_sdate" value="<?php echo rm_str($pass_sdate) < 1 ? '': $pass_sdate ?>" class="design js_pic_day" readonly style="width:85px">
							<span class="fr_tx">-</span>
							<input type="text" name="pass_edate" value="<?php echo rm_str($pass_edate) < 1?  '': $pass_edate ?>" class="design js_pic_day" readonly style="width:85px">
						</td>
					</tr>

					<tr>
						<th>검색어</th>
						<td colspan="3">
							<select name="pass_input_type">
								<option value="all" <?=$pass_input_type == 'all' ? 'selected' : ''?>>-전체검색-</option>
								<option value="title" <?=$pass_input_type == 'title' ? 'selected' : ''?>>게시물 제목</option>
								<option value="id" <?=$pass_input_type == 'id' ? 'selected' : ''?>>등록자 아이디</option>
								<option value="writer" <?=$pass_input_type == 'writer' ? 'selected' : ''?>>등록자 이름</option>
							</select>
							<input type="text" name="pass_input" class="design"  value="<?=$pass_input?>" />
						</td>
					</tr>

				</tbody>
			</table>
			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
					<li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"/></span><!-- <a href="" class="c_btn h34 black ">검색</a> --></li>
					<?php
						if($searchMode == 'true'){
							$arr_param = array_filter(array_merge(array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount, 'menuUid'=>$menuUid),$arr_param));
					?>
						<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>


		</div>
	</form>



	<!-- ● 데이터 리스트 -->
	<div class="data_list">
	<form name="frmBbsInfo" id="frmBbsInfo" method="post" action="_bbs.post_mng.pro.php">
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
		<input type="hidden" name="searchQue" value="<?=enc('e',$s_query)?>">
		<input type="hidden" name="searchCnt" value="<?=$TotalCount?>">
		<input type="hidden" name="ctrlMode" value="">
		<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
		<input type=hidden name="_uid" value=""> <?php // 개별실헹 :: 고유번호 저장 필드?>
		<input type=hidden name="_sort" value=""> <?php // 개별실행 ::  정렬방식 up,down,first,last?>


			<!-- ●리스트 컨트롤영역 -->
			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
					<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
					<a href="#none" onclick="return false;" class="c_btn h27 gray select-delete-item">선택삭제</a>
				</div>
				<div class="right_box">
					<select class="h27" onchange="location.href=this.value;">
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
					</select>

				</div>
			</div>
			<!-- / 리스트 컨트롤영역 -->


		<table class="table_list">
			<colgroup>
				<col width="40"><col width="70"/><col width="80"/><col width="120"/>
				<?php if($boardInfo['bi_category_use']=='Y' && $_categoryload) { ?>
				<!-- KAY :: 게시판 카테고리설정 -->
				<col width="120"/>
				<?php }?>
				<col width="*"/><col width="150"/><col width="100"><col width="80"/>
				<?php if( $replyMode === true){ // 게시판의 형태가 qna 와 같이 답글이 필요없을경우 ?>
				<col width="80"/>
				<?php } ?>
				<col width="160"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">번호</th>
					<th scope="col">노출구분</th>
					<th scope="col">게시판 이름</th>
					<?php if($boardInfo['bi_category_use']=='Y' && $_categoryload) { ?>
					<!-- KAY :: 게시판 카테고리설정 -->
					<th scope="col">카테고리</th>
					<?php } ?>
					<th scope="col">제목</th>
					<th scope="col">작성자</th>
					<th scope="col">작성일</th>
					<th scope="col">조회수</th>
					<?php if( $replyMode === true){ // 게시판의 형태가 qna 와 같이 답글이 필요없을경우 ?>
					<th scope="col">답변상태</th>
					<?php } ?>
					<th scope="col">관리 </th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach($res as $k=>$v) {
					$_num = $TotalCount - $count - $k ;
					$_num = number_format($_num);

					$printBtn  = '<div class="lineup-vertical">';
					$printBtn .= '	<a href="'.$system['url'].'/?pn=board.view&_menu='.$v['b_menu'].'&_uid='.$v['b_uid'].'" target="_blank" class="c_btn h22">바로가기</a>';
					$printBtn .= '	<a href="_bbs.post_mng.form.php?_mode=modify&_uid='.$v['b_uid'].'&_PVSC='.$_PVSC.'" class="c_btn h22">수정</a>';
					if($v['b_depth'] < 2 && $v['b_relation'] < 1 ){
						//$printBtn .= '	<a href="_bbs.post_mng.form.php?_mode=reply&_uid='.$v['b_uid'].'&_PVSC='.$_PVSC.'" class="c_btn h22">답글</a>';
					}
					$printBtn .= '	<a href="#none" onclick="return false;" class="c_btn h22 gray delete-item" data-uid="'.$v['b_uid'].'" data-apply = "true">삭제</a>';
					$printBtn .= '</div>';
					// KAY :: 2021-06-10 :: 에디터이미지관리 버튼생성
					$printBtn .= '<div class="lineup-vertical"><a href="#none" onclick="edit_img_pop('.$v['b_uid'].')" class="c_btn h22 green">이미지 관리</a></div>';



					// -- 댓글개수표시
					$printCommentCnt = '';
					// $commentCnt = _MQ_result("select count(*) from smart_bbs_comment where bt_buid = '".$v['b_uid']."' ");
					$commentCnt = $v['b_talkcnt'];
					if( $commentCnt > 0){
							$printCommentCnt = '<span class="t_reply">['.$commentCnt.']</span>';
					}

					// -- 게시물 제목
					$arrTitleIcon = array();
					$printTitle = strip_tags(stripslashes($v['b_title'])); // fr_url h22
					$printTitle = '<a href="_bbs.post_mng.view.php?_uid='.$v['b_uid'].'&_PVSC='.$_PVSC.'" class=""  title="'.$printTitle.'">'.$printTitle.$printCommentCnt.'</a>';
					$tdReply =  '';
					if($v['b_depth'] > 1 && $v['b_relation'] > 0){ $tdReply = 'if_reply'; }
					if( $v['b_secret'] == 'Y'){ $arrTitleIcon[] = '<span class="c_tag h18 gray">비밀글</span>';}
					if( count($arrTitleIcon) > 0){ $printTitle = implode($arrTitleIcon).''.$printTitle; }
					else{ $printTitle = $printTitle; }

					// -- 작성자 정보
					$printWriterInfo = in_array($v['b_writer_type'], array('member','admin')) == true ? showUserInfo($v['b_inid'],$v['b_writer'],$v) : showUserInfo(false,$v['b_writer']);

					$printReplyStatus = '';
					if( $replyMode === true){ // 게시판의 형태가 qna 와 같이 답글이 필요없을경우
						$replyCnt = _MQ_result("select count(*) as cnt from smart_bbs where b_relation = '".$v['b_uid']."' and b_depth = '2' ");
						$printReplyStatus = '<td>'.($replyCnt > 0 ? '<span class="c_tag blue h22">답변완료</span>' :  '<span class="c_tag gray h22">답변대기</span>').'</td>';
					}

					if( $v['b_notice'] == 'Y'){ $_num = '<div class="lineup-center">'. $arr_adm_button['공지'] .'</div>'; }

					// KAY :: 게시판 카테고리설정 -- 카테고리 정보
					$printCategory='';
					if($boardInfo['bi_category_use']=='Y' && $_categoryload){
						$printCategory='<td>'.$v['b_category'].'</td>';
					}

					// -- 2019-04-10 SSJ :: 이미지가 포함된 게시판일 경우 이미지 노출 ----
					$_image = '';
					if(in_array($v['bi_skin'], $boardImagesUse)){
						// 이미지 체크
						$_img = get_img_src($v['b_img1'], IMG_DIR_BOARD);
						if($_img <> '') $_image = '<span class="preview_thumb"><img src="'.$_img.'" class="js_thumb_img" data-img="'.$_img.'" alt=""></span>';
					}
					// -- 2019-04-10 SSJ :: 이미지가 포함된 게시판일 경우 이미지 노출 ----

					// -- 출력
					echo '<tr>';
					echo '<td><label class="design"><input type="checkbox" name="chkVar[]" class="js_ck chk-buid" value="'.$v['b_uid'].'"></label></td>';
					echo '	<td>'.$_num.'</td>';
					echo '	<td>'.$arrBoardViewType[$v['bi_view_type']].'</td>';
					echo '	<td>'.$v['bi_name'].'</td>';
					echo $printCategory; // KAY :: 게시판 카테고리설정 -- 카테고리 정보
					echo '	<td class="t_left '.$tdReply.'">'.$_image.$printTitle.'</td>';
					echo '	<td>'.$printWriterInfo.'</td>';
					echo '	<td>'.printDateInfo($v['b_rdate']).'</td>';
					echo '	<td>'.number_format($v['b_hit']).'</td>';
					echo $printReplyStatus;
					echo '  <td>'.$printBtn.'</td>';
					echo '</tr>';
				}
			?>
			</tbody>

		</table>

			<?php if(	(count($res)+count($resNotice)) <  1) {  ?>
							<!-- 내용없을경우 -->
							<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>

			<?php } ?>

	</form>
	</div>

	<style>.post_replay .fr_bullet:before{ display:none; }</style>

	<!-- ●●● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount," ?&${_PVS}&listpg=" , "Y")?>
	</div>



<script>


	// -- 게시판 유형별로 보여지는게 달라 필수 선택으로 변경
	$(document).on('change','[name="select_menu"]',function(){
		if( $(this).val() == '' || $(this).val() == undefined){ return false; }
		location.href='_bbs.post_mng.list.php?select_menu='+$(this).val();;
	});

	// -- 선택삭제
	$(document).on('click','.select-delete-item',function(){
		if( confirm("선택하신 게시판을 삭제하시겠습니까?\n게시물의 모든 데이터 및 댓글이 삭제가 됩니다.") == false){ return false; }
		var chkLen = $('.chk-buid:checked').length;
		if( chkLen < 1 ){ alert('한개이상 선택해 주세요.'); return false; }
		$('form#frmBbsInfo [name="_mode"]').val('selectDelete');
		$('form#frmBbsInfo').submit();
	})

	// -- 한개삭제
	$(document).on('click','.delete-item',function(){
		if( confirm("해당 게시물을 삭제하시겠습니까?\n게시물의 모든 데이터 및 댓글이 삭제가 됩니다.") == false){ return false; }

		var _uid = $(this).attr('data-uid');  // 고유번호
		if( _uid == '' || _uid == undefined){ alert('잘못된 접근입니다.'); return false; }

		$('form#frmBbsInfo [name="_uid"]').val(_uid);
		$('form#frmBbsInfo [name="_mode"]').val('delete');
		$('form#frmBbsInfo').submit();
	});

	$(document).on('click','.evt-sort',function(){
		var _sort = $(this).attr('data-sort'); // 정렬방식, 위로,아래롤,맨위로,맨아래로
		var _uid = $(this).attr('data-uid'); // 고유번호
		if( _uid == '' || _uid == undefined || _sort == '' || _sort == undefined){ alert("잘못된 접근입니다."); return false; }
		$('form#frmBbsInfo [name="_uid"]').val(_uid);
		$('form#frmBbsInfo [name="_sort"]').val(_sort);
		$('form#frmBbsInfo [name="_mode"]').val('sort');
		$('form#frmBbsInfo').submit();

	});

	// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기
	function edit_img_pop(_uid, table='board'){
		window.open('_config.editor_img.pop.php?_uid='+_uid+'&tn='+table+'','editimg','width=1120,height=600,scrollbars=yes');
	}

</script>
<?php
	include_once('wrap.footer.php');
?>
