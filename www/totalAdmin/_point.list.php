<?PHP
/*
	accesskey {
		a: 팝업추가
		s: 검색
		l: 전체리스트(검색결과 페이지에서 작동)
	}
*/
include_once('wrap.header.php');

//shop_pointlog_insert( 'papersj' , '포인트삭제' , '-5000' , 'N' , '0');
//exit;


# 기본변수
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_GET , $_POST))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);


// 검색 체크
$s_query = " and pl_delete = 'N' ";
if( $pass_inid !="" ) $s_query .= " and pl_inid like '%${pass_inid}%' ";
if( $pass_title !="" ) $s_query .= " and pl_title like '%${pass_title}%' ";
if( $pass_status !="" ) $s_query .= " and pl_status = '${pass_status}' ";

// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = 'pl_uid';
if(!$so) $so = 'desc';
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_point_log as pl where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$r = _MQ_assoc(" select *, indr.in_name from smart_point_log as pl left join smart_individual as indr on (pl.pl_inid = indr.in_id) where (1) {$s_query} order by {$st} {$so} limit $count , $listmaxcount ");

?>

<div class="group_title">
	<strong>적립금 검색</strong>
	<div class="btn_box">
		<a href="_point.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">적립금 등록</a>
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
					<col width="140"><col width="*">
					<col width="140"><col width="*">
					<col width="140"><col width="*">
				</colgroup>
			<thead>
				<tr>
					<th>처리상태</th>
					<td>
						<?php echo _InputSelect('pass_status', array('Y', 'N', 'C'), $pass_status, '', array('적립완료', '적립예정', '적립취소'), ''); ?>
					</td>
					<th>아이디</th>
					<td>
						<input type="text" name="pass_inid" class="design" value="<?php echo $pass_inid; ?>" style="">
					</td>
					<th>제목</th>
					<td>
						<input type="text" name="pass_title" class="design" value="<?php echo $pass_title; ?>" style="">
					</td>
				</tr>

				<tr>
					<td colspan="6">
						<div class="tip_box">
							<?php echo _DescStr('적립이 완료된 적립금 내역은 수정할 수 없습니다. '); ?>
							<?php echo _DescStr('적립이 완료된 적립금 내역 취소 시 적립된 적립금 만큼을 차감 하는 취소내역이 추가됩니다. '); ?>
							<?php echo _DescStr('적립 이전의 적립금 내역 취소 시 <em>적립취소</em>상태가 되며 적립예정일이 되어도 적립금이 적립되지 않습니다. '); ?>
							<?php echo _DescStr('적립금 내역의 삭제 시 회원 포인트는 변동되지 않습니다.  '); ?>
							<?php echo _DescStr('적립금 내역의 삭제 시 적립금 내역의 합계와 회원 적립금이 다를 수 있습니다. ', 'black'); ?>
							<?php echo _DescStr('삭제된 내역과 <em>적립취소</em>상태의 내역은 마이페이지에 노출되지 않습니다. '); ?>
						</div>
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
	<form name="frm" method="post" action="" >
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<!-- 2020-01-14 SSJ :: 엑셀다운로드 추가 -->
	<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
	<input type="hidden" name="_search" value="<?php echo enc('e', $s_query); ?>">

		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
				<a href="#none" onclick="selectDelete(); return false;" class="c_btn h27 gray">선택삭제</a>
			</div>
			<div class="right_box">
				<!-- 2020-01-14 SSJ :: 엑셀다운로드 추가 -->
				<a href="#none" onclick="downloadExcel('select'); return false;" class="c_btn icon icon_excel">선택 엑셀다운로드</a>
				<a href="#none" onclick="downloadExcel('search'); return false;" class="c_btn icon icon_excel">검색 엑셀다운로드(<?php echo number_format($TotalCount); ?>)</a>

				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pl_uid', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'pl_uid' && $so == 'asc'?' selected':null); ?>>등록일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pl_uid', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'pl_uid' && $so == 'desc'?' selected':null); ?>>등록일 ▼</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pl_adate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'pl_adate' && $so == 'asc'?' selected':null); ?>>지급일 ▲</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'pl_adate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'pl_adate' && $so == 'desc'?' selected':null); ?>>지급일 ▼</option>
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
				<col width="40">
				<col width="80">
				<col width="150">
				<col width="*">
				<col width="100">
				<col width="100">
				<col width="100">
				<col width="80">
				<col width="90">
				<col width="90">
				<col width="160">
			</colgroup>
			<thead>
				<tr>
					<th><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th>NO</th>
					<th>회원ID</th>
					<th>제목</th>
					<th>지급전 적립금</th>
					<th>지급 적립금</th>
					<th>지급후 적립금</th>
					<th>지급상태</th>
					<th>지급예정일</th>
					<th>등록일</th>
					<th>관리</th>
				</tr>
			</thead>
			<tbody>
				<?php if(count($r) > 0) { ?>
					<?php
					foreach($r as $k=>$v) {
						$_num = $TotalCount-$count-$k; // NO 표시
						$_title = strip_tags($v['pl_title']);

						// 적립상태
						$status_icon = $arr_adm_button['적립예정'];
						if($v['pl_status']=='Y') $status_icon = $arr_adm_button['적립완료'];
						else if($v['pl_status']=='C') $status_icon = $arr_adm_button['적립취소'];

					?>
						<tr>
							<td>
								<label class="design"><input type="checkbox" name="chk_uids[]" class="js_ck" value="<?php echo $v['pl_uid']; ?>"></label>
							</td>
							<td><?php echo number_format($_num); ?></td>
							<td>
								<?php echo showUserInfo($v['pl_inid'],$v['in_name']); ?>
							</td>
							<td class="t_left t_black">
								<?php echo $_title; ?>
							</td>
							<td class="t_light">
								<?php echo ($v['pl_status']=='Y'?number_format($v['pl_point_before']):'-'); ?>
							</td>
							<td class="t_black">
								<?php echo number_format($v['pl_point']); ?>
							</td>
							<td class="t_light">
								<?php echo ($v['pl_status']=='Y'?number_format($v['pl_point_after']):'-'); ?>
								<?php echo ($v['pl_status']=='Y' && $v['pl_point'] <> $v['pl_point_apply'] ? '<br><span class="t_red">(보정 : '.number_format($v['pl_point_apply']-$v['pl_point']).')</span>' : ''); ?>
							</td>
							<td>
								<div class="lineup-vertical"><?php echo $status_icon; ?></div>
							</td>
							<td>
								<?php echo date('Y.m.d', strtotime($v['pl_appdate'])); ?>
							</td>
							<td>
								<?php echo date('Y.m.d', strtotime($v['pl_rdate'])); ?>
							</td>
							<td>
								<div class="lineup-vertical">
									<a href="_point.form.php?_mode=modify&_uid=<?php echo $v['pl_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>" class="c_btn h22 ">수정</a>
									<a href="#none" onclick="cancel('_point.pro.php?_mode=cancel&_uid=<?php echo $v['pl_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>'); return false;" class="c_btn h22 gray">취소</a>
									<!-- <a href="#none" onclick="delete_log('_point.pro.php?_mode=delete&_uid=<?php echo $v['pl_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>'); return false;" class="c_btn h22 gray">삭제</a> -->
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

	</form>
</div>
<!-- // 리스트 -->


<script>
	// 포인트 로그 삭제  -- db상에서는 삭제하지 않고 노출되지 않도록만 처리
	function delete_log($href){
		if(confirm('적립금 내역을 삭제하여도 회원 적립금은 차감(가감)되지 않습니다.\n\n적립금 내역 삭제 시 적립금 내역의 합계와 회원의 보유 적립금이 \n\n다를 수 있습니다. 정말 삭제하시겠습니까?')){
			document.location.href = $href;
		}
	}
	 // 선택삭제
	 function selectDelete() {
		 if($('.js_ck').is(':checked')){
			 if(confirm('적립금 내역을 삭제하여도 회원 적립금은 차감(가감)되지 않습니다.\n\n적립금 내역 삭제 시 적립금 내역의 합계와 회원의 보유 적립금이 \n\n다를 수 있습니다. 정말 삭제하시겠습니까?')){
				$('form[name=frm]').children('input[name=_mode]').val('mass_delete');
				$('form[name=frm]').attr('action' , '_point.pro.php');
				document.frm.submit();
			 }
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }

	// 2020-01-14 SSJ :: 엑셀다운로드 추가
	function downloadExcel(_mode){
		if(_mode == 'select' && $('.js_ck').is(":checked") === false){
			alert('1개 이상 선택해 주시기 바랍니다.');
			return false;
		}

		$("form[name=frm]").children("input[name=_mode]").val(_mode);
		$("form[name=frm]").attr("action" , "_point.download.php");
		$("form[name=frm]").attr("target" , "_self");
		document.frm.submit();
		return true;
	}
	// 검색엑셀 다운로드
</script>

<?php include_once('wrap.footer.php'); ?>