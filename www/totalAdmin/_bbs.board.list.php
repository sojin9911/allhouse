<?php

	// -- LCY -- 기세판 목록
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


	// 회원 관리 --- 검색폼 불러오기
	//			반드시 - s_query가 적용되어야 함.
	$s_query = " from smart_bbs_info  where 1  ";


	// -- 검색시작 -- {{{
	if( $searchMode == 'true') {

		// -- 검색어
		if($pass_input_type == 'uid'){
			$s_query .= " and bi_uid = '".$pass_input."' ";
		}else if( $pass_input_type == 'name'){
			$s_query .= " and bi_name like '%".$pass_input."%' ";
		}else if( $pass_input_type == 'all'){
			$s_query .= " and ( bi_uid = '".$pass_input."' or bi_name like '%".$pass_input."%') ";
		}

		if( $pass_skin!= '') {  $s_query .= " and bi_skin= '".$pass_skin."'  "; }// -- 게시판스킨
		if( $pass_view!= '') {  $s_query .= " and bi_view= '".$pass_view."'  "; }// -- 노출여부
		if( $pass_view_type!= '') {  $s_query .= " and bi_view_type= '".$pass_view_type."'  "; }// -- 노출구분

	}
	// -- 검색종료 -- }}}

	// -- 게시판 정보를 불러온다. {{{
	$getBoardSkinInfo = getBoardSkinInfo(); // 게시판 스킨 정보 배열로 호출
	$arrBoardKink = array();
	foreach($getBoardSkinInfo as $k=>$v){
		$arrBoardKink[$k] = $v['skin']['title'];
	}
	// -- 게시판 정보를 불러온다. }}}

	// -- 노출구분이 있을 시 :: 순위변경 가능할 시
	if( $pass_view_type != '' && in_array($pass_view_type,array_keys($arrBoardViewType)) == true) {
		$st = 'bi_view_idx';
		$so = 'asc';
	}else{
		$st = 'bi_rdate';
		$so = 'desc';
	}


	if(!$listmaxcount) $listmaxcount = 50;
	if(!$listpg) $listpg = 1;
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스

	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * $s_query order by {$st} {$so} limit $count , $listmaxcount ");

?>

	<!-- 단락타이틀 -->
	<div class="group_title">
		<strong>게시판 검색</strong><!-- 메뉴얼로 링크 --><?=openMenualLink('게시판검색')?>
		<!-- 해당페이지의 등록/업로드 버튼 있을 경우 -->
		<div class="btn_box"><a href="_bbs.board.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">게시판등록</a></div>
	</div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
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
						<th>검색어</th>
						<td>
							<select name="pass_input_type">
								<option value="all" <?=$pass_input_type == 'all' ? 'selected' : ''?>>-전체검색-</option>
								<option value="uid" <?=$pass_input_type == 'uid' ? 'selected' : ''?>>게시판 아이디</option>
								<option value="name" <?=$pass_input_type == 'name' ? 'selected' : ''?>>게시판 이름</option>
							</select>
							<input type="text" name="pass_input" class="design" style="" value="<?=$pass_input?>" />
						</td>
						<th>게시판스킨</th>
						<td>
							<?=_InputSelect( "pass_skin" , array_keys($arrBoardKink) , $pass_skin, "" , array_values($arrBoardKink) , "-스킨선택-")?>
						</td>
					</tr>

					<tr>
						<th>노출여부</th>
						<td>
							<?php echo _InputRadio( 'pass_view' , array('', 'Y', 'N'), ($pass_view) , '' , array('전체', '노출', '숨김') , ''); ?>
						</td>
						<th>노출구분</th>
						<td>
							<?php echo _InputRadio( 'pass_view_type' , array_merge(array(''), array_keys($arrBoardViewType)), ($pass_view_type) , '' , array_merge(array('전체'), array_values($arrBoardViewType)) , ''); ?>
						</td>
					</tr>

					<tr>
						<td colspan="4">
							<div class="tip_box">
								<div class="c_tip black">순위변경의 경우 노출구분 검색시에만 변경 가능합니다.</div>
							</div>
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
	<form name="frmBbsInfo" id="frmBbsInfo" method="post" action="_bbs.board.pro.php">
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
		<input type="hidden" name="searchQue" value="<?=enc('e',$s_query)?>">
		<input type="hidden" name="searchCnt" value="<?=$TotalCount?>">
		<input type="hidden" name="ctrlMode" value="">
		<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
		<input type=hidden name="_uid" value=""> <?php // 개별실헹 :: 고유번호 저장 필드?>
		<input type=hidden name="_sort" value=""> <?php // 개별실행 ::  정렬방식 up,down,first,last?>

		<table class="table_list">
			<colgroup>
				<col width="70"/><?php if( $pass_view_type != '' && in_array($pass_view_type,array_keys($arrBoardViewType)) == true){  ?> <col width="120"/> <?php } ?><col width="70"/><col width="80"/><col width="*"/><col width="*"/><col width="150"/><col width="80"/><col width="80"><col width="160"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col">번호</th>
					<?php if( $pass_view_type != '' && in_array($pass_view_type,array_keys($arrBoardViewType)) == true){  ?>
						<th scope="col">순위</th>
					<?php } ?>
					<th scope="col">노출여부</th>
					<th scope="col">노출구분</th>
					<th scope="col">게시판 아이디</th>
					<th scope="col">게시판 이름</th>
					<th scope="col">게시판 스킨</th>
					<th scope="col">게시글 수</th>
					<th scope="col">등록일</th>
					<th scope="col">관리 </th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach($res as $k=>$v) {
					$_num = $TotalCount - $count - $k ;
					$_num = number_format($_num);

					$printBtn = '
					<div class="lineup-vertical">
						<a href="'.$system['url'].'/?pn=board.list&_menu='.$v['bi_uid'].'" target="_blank" class="c_btn h22">바로가기</a>
						<a href="_bbs.board.form.php?_mode=modify&_uid='.$v['bi_uid'].'&_PVSC='.$_PVSC.'" class="c_btn h22">수정</a>
						<a href="#none" onclick="return false;" class="c_btn h22 gray delete-board" data-uid="'.$v['bi_uid'].'" data-apply = "true">삭제</a>
					</div>
					'; // 관리버튼

					// 순위변경 :: 노출구분이 있을 시에만가능
					$printIdx = '
						<div class="lineup-center">
							<a href="#none"  class="c_btn h22 icon_up evt-sort" data-uid="'.$v['bi_uid'].'" data-sort="up"  title="위로"></a>
							<a href="#none"  class="c_btn h22 icon_down evt-sort" data-uid="'.$v['bi_uid'].'" data-sort="down"  title="아래로"></a>
							<a href="#none"  class="c_btn h22 icon_top evt-sort" data-uid="'.$v['bi_uid'].'" data-sort="first"  title="맨위로"></a>
							<a href="#none"  class="c_btn h22 icon_bottom evt-sort" data-uid="'.$v['bi_uid'].'" data-sort="last"  title="맨아래로"></a>
						</div>
					';

					$printView =  $arr_adm_button[($v['bi_view'] == 'Y' ? '노출' : '숨김')]; // -- 노출여부
					$printSkinName = $arrBoardKink[$v['bi_skin']]; // // -- 스킨명
					$printPostCnt =  number_format($v['bi_post_cnt']); // -- 게시글 수



					// -- 출력
					echo '<tr>';
					echo '	<td>'.$_num.'</td>';
					if( $pass_view_type != '' && in_array($pass_view_type,array_keys($arrBoardViewType)) == true){
						echo '	<td>'.$printIdx.'</td>';
					}
					echo '	<td><div class="lineup-center">'.$printView.'</div></td>';
					echo '	<td>'.$arrBoardViewType[$v['bi_view_type']].'</td>';
					echo '	<td>'.$v['bi_uid'].'</td>';
					echo '	<td>'.$v['bi_name'].'</td>';
					echo '	<td>'.$printSkinName.'</td>';
					echo '	<td>'.$printPostCnt.'</td>';
					echo '	<td>'.printDateInfo($v['bi_rdate']).'</td>';
					echo '	<td>'.$printBtn.'</td>';
					echo '</tr>';
				}
			?>
			</tbody>

		</table>

			<?php if(count($res) <  1) {  ?>
							<!-- 내용없을경우 -->
							<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>

			<?php } ?>

	</form>
	</div>


	<!-- ●●● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount," ?&${_PVS}&listpg=" , "Y")?>
	</div>



<script>

	$(document).on('click','.delete-board',function(){
		if( confirm("해당 게시판을 삭제하시겠습니까?\n등록된 게시물이 있는경우 삭제가 불가능합니다.") == false){ return false; }

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

</script>
<?php
	 // viewarr($getBoardSkinInfo);
	include_once('wrap.footer.php');
?>
