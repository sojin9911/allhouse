<?php 
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

	// -- 게시판 정보를 불러온다. {{{
	$getBoardList = get_board_list_array(false,true);
	// -- 게시판 정보를 불러온다. }}}

	// -- 게시판 필수 선택으로 지정
	$select_menu = in_array($select_menu,array_keys($getBoardList)) == false ? array_shift(array_keys($getBoardList)):$select_menu;  

	$arr_param['pass_menu'] = $select_menu;

	// 검색 체크
	$s_query = "
		from smart_bbs_faq
		where 1 
	";

	// -- 검색시작 -- {{{
	if( $searchMode == 'true') { 
		// -- 검색어
		if( $pass_input_type == 'content'){ // 내용
			$s_query .= " and bf_content like '%".$pass_input."%' ";
		}else if($pass_input_type == 'title'){ // 제목
			$s_query .= " and bf_title like '%".$pass_input."%' ";
		}else if( $pass_input_type == 'all'){ // 전체검색
			$s_query .= " and ( bf_content like '%".$pass_input."%'  or bf_title like '%".$pass_input."%'  ) ";
		}

		if( $pass_type!= '') {  $s_query .= " and bf_type = '".$pass_type."'  "; }// -- 분류

		if( $pass_best == 'Y'){  $s_query .= " and bf_best = 'Y'  "; } // 노출구분 :: 베스트
		else if( $pass_best == 'N'){$s_query .= " and bf_best != 'Y'  ";  }  // 노출구분 :: 일반

	}
	// -- 검색종료 -- }}} 

	$st = 'bf_rdate';
	$so = 'desc';

	if(!$listmaxcount) $listmaxcount = 50;
	if(!$listpg) $listpg = 1;
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스

	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * ".$s_query." ORDER BY ".$st." ".$so." limit ".$count." , ".$listmaxcount);

?>
	
	<!-- 단락타이틀 -->
	<div class="group_title">
		<strong>FAQ 검색</strong><!-- 메뉴얼로 링크 --><?=openMenualLink('FAQ검색')?>
		<!-- 해당페이지의 등록/업로드 버튼 있을 경우 -->
		<div class="btn_box"><a href="_bbs.post_faq.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">글등록</a></div>		
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
						<th>분류</th>
						<td colspan="3">		
							<?php echo _InputSelect( 'pass_type' , array_keys($arrFaqBoardConfig['faqType']), ($pass_type) , '' , array_values($arrFaqBoardConfig['faqType']) , '-분류-'); ?>				
						</td>				
					</tr>

					<tr>
						<th>검색어</th>
						<td>		
							<select name="pass_input_type">
								<option value="all" <?=$pass_input_type == 'all' ? 'selected' : ''?>>-전체검색-</option>
								<option value="title" <?=$pass_input_type == 'title' ? 'selected' : ''?>>제목</option>
								<option value="content" <?=$pass_input_type == 'writer' ? 'selected' : ''?>>답변</option>
							</select>						
							<input type="text" name="pass_input" class="design" value="<?=$pass_input?>" />
						</td>
						<th>노출구분</th>
						<td>
							<?php echo _InputRadio( 'pass_best' , array('','N','Y'), ($pass_best) , '' , array('전체','일반','베스트') , '-노출구분선택-'); ?>
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
	<form name="frmBbsFaq" id="frmBbsFaq" method="post" action="_bbs.post_faq.pro.php">
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
					<a href="#none" onclick="return false;" class="c_btn h27 gray selet-delete-item">선택삭제</a>
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
				<col width="40"><col width="70"/><col width="110"/><col width="*"/><col width="80"><col width="80"><col width="160"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">번호</th>
					<th scope="col">분류</th>
					<th scope="col">제목</th>
					<th scope="col">노출구분</th>
					<th scope="col">작성일</th>
					<th scope="col">관리 </th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach($res as $k=>$v) { 
					$_num = $TotalCount - $count - $k ;
					$_num = number_format($_num);


					$printBtn  = '<div class="lineup-center">';
					$printBtn .= '	<a href="'.$system['url'].'/?pn=faq.list&_uid='.$v['bf_uid'].'" target="_blank" class="c_btn h22">바로가기</a>';
					$printBtn .= '	<a href="_bbs.post_faq.form.php?_mode=modify&_uid='.$v['bf_uid'].'&_PVSC='.$_PVSC.'" class="c_btn h22">수정</a>';
					$printBtn .= '	<a href="#none" onclick="return false;" class="c_btn h22 gray delete-item" data-uid="'.$v['bf_uid'].'" data-apply = "true">삭제</a>';
					$printBtn .= '</div>';
					// KAY :: 2021-06-10 :: 에디터이미지관리 버튼생성
					$printBtn .= '<div class="lineup-center"><a href="#none" onclick="edit_img_pop('.$v['bf_uid'].')" class="c_btn h22 green">이미지 관리</a></div>';
					
					// -- FAQ 제목 
					$arrTitleIcon = array();
					$printTitle = strip_tags(stripslashes($v['bf_title']));
					$printTitle = '<a href="_bbs.post_faq.form.php?_mode=modify&_uid='.$v['bf_uid'].'" class=""  title="'.$printTitle.'">'.$printTitle.'</a>';

					$printBest = $v['bf_best'] == 'Y' ? '베스트': '일반';

					// -- 출력
					echo '<tr>';
					echo '<td><label class="design"><input type="checkbox" name="chkVar[]" class="js_ck" value="'.$v['bf_uid'].'"></label></td>';
					echo '	<td>'.$_num.'</td>';
					echo '	<td>'.$arrFaqBoardConfig['faqType'][$v['bf_type']].'</td>';
					echo '	<td class="t_left">'.$printTitle.'</td>';
					echo '	<td>'.$printBest.'</td>';
					echo '  <td>'.date('Y.m.d H:i:s', strtotime($v['bf_rdate'])).'</td>';
					echo '  <td>'.$printBtn.'</td>';
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
	
	<style>.post_replay .fr_bullet:before{ display:none; }</style>

	<!-- ●●● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount," ?&${_PVS}&listpg=" , "Y")?>
	</div>



<script>
	$(document).on('click','.selet-delete-item',function(){
		var chkLen = $('[name="chkVar[]"]:checked').length;
		if( chkLen  < 1){ alert('한개 이상 선택해 주세요.'); return false; }
		if( confirm("선택하신 FAQ 게시글을 삭제하시겠습니까? ") == false){ return false; }
		$('form#frmBbsFaq [name="_uid"]').val('');
		$('form#frmBbsFaq [name="_mode"]').val('selectDelete');
		$('form#frmBbsFaq').submit();
	});

	$(document).on('click','.delete-item',function(){
		if( confirm("해당 FAQ 게시글을 삭제하시겠습니까? ") == false){ return false; }
		var _uid = $(this).attr('data-uid');  // 고유번호 
		if( _uid == '' || _uid == undefined){ alert('잘못된 접근입니다.'); return false; }
		$('form#frmBbsFaq [name="_uid"]').val(_uid);
		$('form#frmBbsFaq [name="_mode"]').val('delete');
		$('form#frmBbsFaq').submit();
	});

	// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기
	function edit_img_pop(_uid, table='board_faq'){
		window.open('_config.editor_img.pop.php?_uid='+_uid+'&tn='+table+'','editimg','width=1120,height=600,scrollbars=yes');
	}

</script>
<?php 
	include_once('wrap.footer.php'); 
?>
