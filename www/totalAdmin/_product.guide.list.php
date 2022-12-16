<?php
/*
	accesskey {
		a: 팝업추가
		s: 검색
		l: 전체리스트(검색결과 페이지에서 작동)
	}
*/
include_once('wrap.header.php');


/*
	
CREATE TABLE  hy30_db.smart_product_guide (
g_uid INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT  '고유번호',
g_user VARCHAR( 50 ) NOT NULL COMMENT  '적용입점업체아이디',
g_type TINYINT NOT NULL COMMENT  '안내구분',
g_default ENUM(  'Y',  'N' ) NOT NULL DEFAULT  'N' COMMENT  '기본적용여부',
g_title VARCHAR( 100 ) NOT NULL COMMENT  '타이틀',
g_content TEXT NOT NULL COMMENT  '이용안내내용',
g_rdate DATETIME NOT NULL COMMENT  '등록일',
INDEX (  g_user ,  g_type ,  g_default )
) ENGINE = MYISAM


*/



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
				<?php if($SubAdminMode === true) { ?>
				<col width="180">
				<col width="*">
				<?php } ?>
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
					<?php
						if($SubAdminMode === true) { // 입점업체 검색기능 2016-05-26 LDD
							$arr_customer = arr_company();
							$arr_customer = array_merge(array('_MASTER_'=>'[통합관리자]'), $arr_customer);
							$arr_customer2 = arr_company2();
							$arr_customer2 = array_merge(array('_MASTER_'=>'[통합관리자]'), $arr_customer2);
					?>
						<th>입점업체</th>
						<td>
							<!-- 20개 이상일때만 select2적용 -->
							<?php if(sizeof($arr_customer) > 20){ ?>
							<link href="/include/js/select2/css/select2.css" type="text/css" rel="stylesheet">
							<script src="/include/js/select2/js/select2.min.js"></script>
							<script>$(document).ready(function() { $('.select2').select2(); });</script>
							<?php } ?>
							<?php echo _InputSelect( 'pass_com' , array_keys($arr_customer) , $pass_com , ' class="select2" ' , array_values($arr_customer) , '-입점업체-'); ?>
						</td>
					<?php } ?>
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
			<?php if($SubAdminMode === true) { ?>
			<col width="180">
			<?php } ?>
			<col width="250"><col width="90"><col width="*"><col width="90"><col width="90"><col width="100">
		</colgroup>
		<thead>
			<tr>
				<th>NO</th>
				<?php if($SubAdminMode === true) { ?>
				<th>입점업체</th>
				<?php } ?>
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
						<?php if($SubAdminMode === true) { ?>
						<td><?php echo ($arr_customer2[$v['g_user']]?$arr_customer2[$v['g_user']]:'확인불가'); ?></td>
						<?php } ?>
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
							<!-- KAY :: 2021-06-10 :: 에디터이미지관리 버튼생성-->
							<div class="lineup-vertical">
								<a href="#none" onclick="edit_img_pop('<?php echo $v['g_uid'] ?>')" class="c_btn h22 green">이미지 관리</a>
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

<script>
	// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기
	function edit_img_pop(_uid, table='setting'){
		window.open('_config.editor_img.pop.php?_uid='+_uid+'&tn='+table+'','editimg','width=1120,height=600,scrollbars=yes');
	}
	// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기
</script>
<?php include_once('wrap.footer.php'); ?>