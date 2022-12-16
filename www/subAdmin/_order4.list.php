<?php
include_once('wrap.header.php');


// 넘길 변수 설정하기
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {
	if(is_array($val)) foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }
	else $_PVS .= "&$key=$val";
}
$_PVSC = enc('e' , $_PVS);
// 넘길 변수 설정하기



// 기본 날짜 지정 (7일)
if(!$pass_sdate) $pass_sdate = date('Y-m-d', strtotime('-7 day'));
$pass_edate = ($pass_edate?$pass_edate:date('Y-m-d'));


// 검색 조건
$s_query = " and s_partnerCode = '{$com_id}' ";
if($pass_sdate && $pass_edate) $s_query .= " and date(s_date) between '{$pass_sdate}' and '{$pass_edate}' "; // - 검색기간
else if($pass_sdate) $s_query .= " and date(s_date) >= '{$pass_sdate}' ";
else if($pass_edate) $s_query .= " and date(s_date) <= '{$pass_edate}' ";


// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = 's_uid';
if(!$so) $so = 'desc';
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_order_settle_complete where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$res = _MQ_assoc("
	select
		*
	from
		smart_order_settle_complete
	where (1)
		{$s_query}
	order by {$st} {$so}
	limit {$count}, {$listmaxcount}
");


// 전체 입점업체 정보 호출
$arr_customer = arr_company();
$arr_customer2 = arr_company2();
?>
<form action="<?php ECHO $_SERVER['PHP_SELF']; ?>" method="get">
	<input type="hidden" name="mode" value="search">
	<!-- ●폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<div class="group_title"><strong>정산완료 검색</strong></div>
	<div class="data_form if_search if_nobottom">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>검색기간</th>
					<td>
						<input type="text" name="pass_sdate" class="design js_pic_day_max_today" style="width:85px;" value="<?php echo $pass_sdate; ?>" readonly />
						<span class="fr_tx">-</span>
						<input type="text" name="pass_edate" class="design js_pic_day_max_today" style="width:85px;" value="<?php echo $pass_edate; ?>" readonly />
					</td>
				</tr>
			</tbody>
		</table>

		<!-- 가운데정렬버튼 -->
		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"/></span></li>
				<?php
				if($mode == 'search') {
				?>
					<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal">전체목록</a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</form>


<form class="form_list" method="post" target="">
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="_seachcnt" value="<?php echo $TotalCount; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="_search_que" value="<?php echo enc('e', $s_query); ?>">
	<input type="hidden" name="st" value="<?php echo $st; ?>">
	<input type="hidden" name="so" value="<?php echo $so; ?>">
	<?php if($c) { ?><input type="hidden" name="test" value="<?php echo $c; ?>"><?php } echo PHP_EOL; ?>

	<div class="data_list">
		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="right_box">
				<a href="#none" onclick="select_excel_send(); return false;" class="c_btn icon icon_excel">선택 엑셀다운로드</a>
				<a href="#none" onclick="search_excel_send(); return false;" class="c_btn icon icon_excel">검색 엑셀다운로드<?php echo ($TotalCount > 0?'('.number_format($TotalCount).')':null); ?></a>
				<select class="h27" onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
				</select>
			</div>
		</div>
		<!-- / 리스트 컨트롤영역 -->

		<table class="table_list">
			<cplgroup>
				<col width="45">
				<col width="90">
				<col width="150">
				<col width="110">
				<col width="110">
				<col width="*">
				<col width="110">
				<col width="110">
				<?php if($siteInfo['TAX_CHK'] == 'Y') { ?>
					<col width="130">
				<?php } ?>
				<col width="80">
			</cplgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">정산일</th>
					<th scope="col">총금액</th>
					<th scope="col">정산수량</th>
					<th scope="col">배송비</th>
					<th scope="col">정산금액</th>
					<th scope="col">할인액</th>
					<th scope="col">수수료</th>
					<?php if($siteInfo['TAX_CHK'] == 'Y') { ?>
						<th scope="col">세금계산서<br>발행상태</th>
					<?php } ?>
					<th scope="col">상세보기</th>
				</tr>
			</thead>
			<?php if(count($res) > 0) { ?>
				<tbody>
					<?php foreach($res as $k=>$v) { ?>
						<tr>
							<td>
								<label class="design"><input type="checkbox" name="_uid[]" class="js_ck" value="<?php echo $v['s_uid']; ?>"></label>
							</td>
							<td>
								<?php echo date('Y-m-d', strtotime($v['s_date'])); ?>
							</td>
							<td>
								<span class="lineup"><strong><?php echo number_format($v['s_price']+$v['s_price_vat_n']); ?></strong><em>원</em></span>
							</td>
							<td>
								<span class="lineup"><strong class="t_green"><?php echo number_format($v['s_count']+$v['s_count_vat_n']); ?></strong><em>건</em></span>
							</td>
							<td>
								<span class="lineup"><strong><?php echo number_format($v['s_delivery_price']+$v['s_delivery_price_vat_n']); ?></strong><em>원</em></span>
							</td>
							<td>
								<span class="lineup"><strong class="t_red"><?php echo number_format($v['s_com_price']+$v['s_com_price_vat_n']); ?></strong><em>원</em></span>
							</td>
							<td>
								<span class="lineup"><strong><?php echo number_format($v['s_usepoint']+$v['s_usepoint_vat_n']); ?></strong><em>원</em></span>
							</td>
							<td>
								<span class="lineup"><strong class="t_sky"><?php echo number_format($v['s_discount']+$v['s_discount_vat_n']); ?></strong><em>원</em></span>
							</td>
							<?php if($siteInfo['TAX_CHK'] == 'Y') { ?>
								<td>
									<div class="lineup-vertical">
										<?php
											switch($v['s_tax_status']){
												case 1000 :echo '<span class="c_tag h22 black">임시저장</span>'; break;
												case 2010 : case 2011 :echo '<span class="c_tag h22 cyan">세금계산서발행중</span>'; break;
												case 4012 :echo '<span class="c_tag h22 yellow">발행거부</span>'; break;
												case 3014 : case 3011 : echo '<span class="c_tag h22 blue">발행완료</span>'; break;
												case 5013 : case 5031 : echo '<span class="c_tag h22 red">발행취소</span>'; break;
												default : echo '<span class="c_tag h22 gray">미발행</span>'; break;
											}
										?>
									</div>
								</td>
							<?php } ?>
							<td>
								<div class="lineup-vertical">
									<a href="_order4.view.php<?php echo URI_Rebuild('?', array('suid'=>$v['s_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22">상세보기</a>
								</div>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			<?php } ?>
		</table>


		<?php if(count($res) <= 0) { ?>
			<!-- 내용없을경우 -->
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">정산내역이 없습니다.</div></div>
		<?php } ?>

		<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
		<div class="paginate">
			<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
		</div>
	</div>
</form>

<script type="text/javascript">
	function select_excel_send() {
		var cnt = $('.js_ck:checked').length;
		if(cnt <= 0) return alert('엑셀변환하실 항목을 1건 이상 선택 바랍니다.');
		$('.form_list').prop('action', '_order4.excel.php');
		$('.form_list').find('input[name=_mode]').val('get_excel');
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
		$('.form_list').prop('action', ''); // action 초기화
	}
	function search_excel_send() {
		$('.form_list').prop('action', '_order4.excel.php');
		$('.form_list').find('input[name=_mode]').val('get_search_excel');
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
		$('.form_list').prop('action', ''); // action 초기화
	}
</script>
<?php include_once('wrap.footer.php'); ?>