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



// 검색 조건
$s_query = '';
if( $pass_title !="" ) { $s_query .= " and md_title like '%${pass_title}%' "; }


// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = 'md_rdate';
if(!$so) $so = 'desc';
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_mailing_data where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$r = _MQ_assoc(" select * from smart_mailing_data where (1) {$s_query} order by {$st} {$so} limit $count , $listmaxcount ");

?>
<div class="group_title">
	<strong>메일링 검색</strong>
	<div class="btn_box">
		<a href="_mailing_data.form.php<?php echo URI_Rebuild('?', array('?_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">메일링 등록</a>
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
			</colgroup>
			<thead>
				<tr>
					<th>메일링 제목</th>
					<td>
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

	<table class="table_list">
		<colgroup>
			<col width="70"><col width="*"><col width="180"><col width="100"><col width="200">
		</colgroup>
		<thead>
			<tr>
				<th>NO</th>
				<th>메일링 제목</th>
				<th>등록일</th>
				<th>발송횟수</th>
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
						<td class="t_left t_black">
							<?php echo $v['md_title']; ?>
						</td>
						<td>
							<?php echo date('Y.m.d H:i:s', strtotime($v['md_rdate'])); ?>
						</td>
						<td>
							<?php echo number_format($v['mp_cnt']); ?>
						</td>
						<td>
							<div class="lineup-vertical">
								<a href="_mailing_profile.form.php<?php echo URI_Rebuild('?', array('_mode'=>'send', '_mduid'=>$v['md_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 blue">메일수신자등록</a>
								<a href="_mailing_data.form.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', '_uid'=>$v['md_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 ">수정</a>
								<a href="#none" onclick="del('_mailing_data.pro.php<?php echo URI_Rebuild('?', array('_mode'=>'delete', '_uid'=>$v['md_uid'], '_PVSC'=>$_PVSC)); ?>'); return false;" class="c_btn h22 gray">삭제</a>
							</div>
							<!-- KAY :: 2021-06-10 :: 에디터이미지관리 버튼생성-->
							<div class="lineup-vertical"><a href="#none" onclick="edit_img_pop('<?php echo $v['md_uid'] ?>')" class="c_btn h22 green">이미지 관리</a></div>
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
	function edit_img_pop(_uid, table='mailing'){
		window.open('_config.editor_img.pop.php?_uid='+_uid+'&tn='+table+'','editimg','width=1120,height=600,scrollbars=yes');
	}
	// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기
</script>
<?php include_once('wrap.footer.php'); ?>