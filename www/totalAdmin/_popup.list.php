<?php
/*
	accesskey {
		a: 팝업추가
		s: 검색
		l: 전체리스트(검색결과 페이지에서 작동)
	}
*/
include_once('wrap.header.php');


# 기본변수
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_GET , $_POST))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
$_targetArr = array('_blank'=>'새창', '_self'=>'같은창');


// 검색 조건
$s_query = '';
if($pass_view) $s_query .= " and `p_view` = '{$pass_view}' ";
if($pass_title) $s_query .= " and `p_title` like '%{$pass_title}%' ";
if($pass_mode) $s_query .= " and `p_mode` = '{$pass_mode}' ";


// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = 'p_rdate';
if(!$so) $so = 'desc';
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_popup where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$r = _MQ_assoc(" select * from smart_popup where (1) {$s_query} order by {$st} {$so} limit $count , $listmaxcount ");

?>
<div class="group_title">
	<strong>팝업검색</strong>
	<div class="btn_box">
		<a href="_popup.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">팝업등록</a>
	</div>
</div>


<!-- 검색 -->
<div class="data_form if_search">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
		<input type="hidden" name="_mode" value="search">
		<input type="hidden" name="st" value="<?php echo $st; ?>">
		<input type="hidden" name="so" value="<?php echo $so; ?>">
		<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
		<table class="table_form">
			<colgroup>
				<col width="180">
				<col width="*">
				<col width="180">
				<col width="*">
			</colgroup>
			<thead>
				<tr>
					<th>노출여부</th>
					<td>
						<?php echo _InputRadio('pass_view', array('', 'Y', 'N'), $pass_view, '', array('전체', '노출', '비노출'), ''); ?>
					</td>
					<th>팝업타입</th>
					<td>
						<?php echo _InputRadio('pass_mode', array('', 'I', 'E'), $pass_mode, '', array('전체', '이미지', '에디터'), ''); ?>
					</td>
				</tr>
				<tr>
					<th>팝업이름</th>
					<td colspan="3">
						<input type="text" name="pass_title" class="design" value="<?php echo $pass_title; ?>" style="width:500px">
					</td>
				</tr>
			</thead>
		</table>
		<div class="c_btnbox">
			<ul>
				<li>
					<span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"></span>
				</li>
				<?php if($_mode == 'search') { ?>
					<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
				<?php } ?>
			</ul>
		</div>
	</form>
</div>
<!-- // 검색 -->


<!-- 리스트 -->
<div class="data_list">
	<div class="list_ctrl">
		<div class="right_box">
			<select class="h27" onchange="location.href=this.value;">
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_rdate' && $so == 'asc'?' selected':null); ?>>등록일 ▲</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_rdate' && $so == 'desc'?' selected':null); ?>>등록일 ▼</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_idx', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_idx' && $so == 'asc'?' selected':null); ?>>순위순 ▲</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_idx', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_idx' && $so == 'desc'?' selected':null); ?>>순위순 ▼</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_sdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_sdate' && $so == 'asc'?' selected':null); ?>>시작일 ▲</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_sdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_sdate' && $so == 'desc'?' selected':null); ?>>시작일 ▼</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_edate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'p_edate' && $so == 'asc'?' selected':null); ?>>종료일 ▲</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'p_edate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'p_edate' && $so == 'desc'?' selected':null); ?>>종료일 ▼</option>
			</select>
			<select class="h27" onchange="location.href=this.value;">
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
			</select>
		</div>
	</div>

	<table class="table_list">
		<colgroup>
			<col width="65"><col width="80"><col width="80"><col width="80"><col width="100"><col width="*">
			<col width="180"><col width="200"><col width="100"><col width="100">
		</colgroup>
		<thead>
			<tr>
				<th>NO</th>
				<th>노출</th>
				<th>노출위치</th>
				<th>순위</th>
				<th>팝업</th>
				<th>타이틀</th>
				<th>개재일</th>
				<th>링크</th>
				<th>링크타켓</th>
				<th>관리</th>
			</tr>
		</thead>
		<tbody>
			<?php if(count($r) > 0) { ?>
				<?php
				foreach($r as $k=>$v) {
					$_num = $TotalCount-$count-$k; // NO 표시
					$_title = strip_tags($v['p_title']);
					$_img = IMG_DIR_POPUP.$v['p_img'];
					if(file_exists($_SERVER['DOCUMENT_ROOT'].$_img)) $_img = '<img src="'.$_img.'" class="js_thumb_img" data-img="'.$_img.'" alt="'.addslashes($_title).'">';
					else $_img = '';
				?>
					<tr>
						<td><?php echo number_format($_num); ?></td>
						<td>
							<div class="lineup-vertical">
								<?php if($v['p_view'] == 'Y') { ?>
									<span class="c_tag blue h18 blue line">노출</span>
								<?php } else { ?>
									<span class="c_tag h18 gray">숨김</span>
								<?php } ?>
							</div>
						</td>
						<td>
							<?php
								$arr_type = array('A'=>'전체', 'P'=>'PC', 'M'=>'Mobile');
								echo $arr_type[($v['p_type']?$v['p_type']:'A')];
							?>
						</td>
						<td>
							<?php echo number_format($v['p_idx']); ?>
						</td>
						<td class="img80">
							<?php echo ($v['p_mode'] == 'I'?$_img:'[에디터형]'); ?>
						</td>
						<td class="t_left t_black">
							<?php echo $v['p_title']; ?>
						</td>
						<td>
							<?php echo ($v['p_none_limit'] == 'Y'?'무기한':$v['p_sdate'].' ~ '.$v['p_edate']); ?>
						</td>
						<td class="t_left">
							<?php if($v['p_mode'] == 'I' && $v['p_link'] != '') { ?>
								<a href="<?php echo $v['p_link']; ?>" target="_blank"><?php echo $v['p_link']; ?></a>
							<?php } else { ?>
								링크없음
							<?php } ?>
						</td>
						<td>
							<?php echo ($v['p_mode'] == 'I' && $v['p_link'] != '' ? $_targetArr[$v['p_target']] : '링크없음'); ?>
						</td>
						<td>
							<div class="lineup-vertical">
								<a href="_popup.form.php?_mode=modify&_uid=<?php echo $v['p_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>" class="c_btn h22 ">수정</a>
								<a href="#none" onclick="del('_popup.pro.php?_mode=delete&_uid=<?php echo $v['p_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>'); return false;" class="c_btn h22 gray">삭제</a>
							</div>
							<!-- KAY :: 2021-06-10 :: 에디터이미지관리 버튼생성-->
							<div class="lineup-vertical"><a href="#none" onclick="edit_img_pop('<?php echo $v['p_uid'] ?>')" class="c_btn h22 green">이미지 관리</a></div>
						</td>
					</tr>
				<?php } ?>
			<?php } ?>
		</tbody>
	</table>
	<?php if(count($r) <= 0) { ?>
		<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
	<?php } ?>

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
	</div>
</div>
<!-- // 리스트 -->

<script>
	// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기
	function edit_img_pop(_uid, table='popup'){
		window.open('_config.editor_img.pop.php?_uid='+_uid+'&tn='+table+'','editimg','width=1120,height=600,scrollbars=yes');
	}
	// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기
</script>
<?php include_once('wrap.footer.php'); ?>