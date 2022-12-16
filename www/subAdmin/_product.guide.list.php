<?php
include_once('wrap.header.php');


# 기본변수
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_GET , $_POST))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);


// 검색 조건
$s_query = "";
if($pass_type) $s_query .= " and g_type = '{$pass_type}' ";
if($pass_title) $s_query .= " and g_title like '%{$pass_title}%' ";
if($pass_com) $s_query .= " and g_user = '{$pass_com}' ";


// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = 'g_uid';
if(!$so) $so = 'desc';
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_product_guide where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$r = _MQ_assoc(" select * from smart_product_guide where (1) {$s_query} order by {$st} {$so} limit $count , $listmaxcount ");
?>
<div class="group_title">
	<strong>이용안내 검색</strong>
	<div class="btn_box">
		<a href="_product.guide.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">이용안내 등록</a>
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
					<th>타이틀</th>
					<td>
						<input type="text" name="pass_title" class="design" value="<?php echo $pass_title; ?>" style="width:185px">
					</td>
					<th>등록구분</th>
					<td>
						<?php echo _InputSelect('pass_type', array_keys($arrProGuideType), $pass_type, '', array_values($arrProGuideType), ''); ?>
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
			<col width="70">
			<col width="250">
			<col width="90">
			<col width="*">
			<col width="90">
			<col width="90">
			<col width="100">
		</colgroup>
		<thead>
			<tr>
				<th>NO</th>
				<th>등록구분</th>
				<th>기본노출</th>
				<th>타이틀</th>
				<th>최종수정일</th>
				<th>등록일</th>
				<th>관리</th>
			</tr>
		</thead>
		<tbody>
			<?php if(count($r) > 0) { ?>
				<?php
				foreach($r as $k=>$v) {

					$_num = $TotalCount-$count-$k; // NO 표시

				?>
					<tr>
						<td><?php echo number_format($_num); ?></td>
						<td><?php echo $arrProGuideType[$v['g_type']]; ?></td>
						<td>
							<div class="lineup-vertical">
								<?php if($v['g_default'] == 'Y') { ?>
									<span class="c_tag h18 blue line">기본</span>
								<?php }else{ ?>
									<span class="c_tag h18 gray line">선택</span>
								<?php } ?>
							</div>
						</td>
						<td class="t_left t_black">
							<?php echo strip_tags($v['g_title']); ?>
						</td>
						<td>
							<?php echo date('Y.m.d', strtotime($v['g_mdate'])); ?>
						</td>
						<td>
							<?php echo date('Y.m.d', strtotime($v['g_rdate'])); ?>
						</td>
						<td>
							<div class="lineup-vertical">
								<a href="_product.guide.form.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', '_uid'=>$v['g_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 ">수정</a>
								<a href="#none" onclick="del('_product.guide.pro.php<?php echo URI_Rebuild('?', array('_mode'=>'delete', '_uid'=>$v['g_uid'], '_PVSC'=>$_PVSC)); ?>'); return false;" class="c_btn h22 gray">삭제</a>
							</div>
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

<?php include_once('wrap.footer.php'); ?>