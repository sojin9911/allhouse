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


// 검색 조건
$s_query = " and o.o_canceled != 'Y' and o.o_paystatus = 'Y' and o.npay_order = 'N' and op.op_partnerCode = '{$com_id}' ";
if($pass_pname) $s_query .= " and p.p_name like '%{$pass_pname}%' ";
if($pass_ordernum) $s_query .= " and op.op_oordernum like '%{$pass_ordernum}%' ";
if($pass_mid) $s_query .= " and o.o_mid like '%{$pass_mid}%' ";
if($pass_oname) $s_query .= " and o.o_oname like '%{$pass_oname}%' ";
if($pass_paystatus) $s_query .= " and o.o_paystatus='{$pass_paystatus}' ";
if($pass_status) $s_query .= " and o.o_status='{$pass_status}' ";
if($pass_sendcompany) $s_query .= " and op.op_sendcompany='{$pass_sendcompany}' ";
if($pass_sendnum) $s_query .= " and op.op_sendnum like '%{$pass_sendnum}%' ";
if($pass_sendstatus) $s_query .= " and op.op_sendstatus='{$pass_sendstatus}' ";
if($pass_settlement) $s_query .= " and op.op_settlementstatus='{$pass_settlement}' ";
if($pass_pcode) $s_query .= " and op.op_pcode = '{$pass_pcode}' ";
if($pass_complain) $s_query .= " and op.op_complain = '${pass_complain}' ";
else $s_query .= " and op.op_complain != '' ";


$date_type = 'op_complain_date';
if($pass_date_type == 'cdate') $date_type = 'op_complain_date'; // 신청일
else if($pass_date_type == 'odate') $date_type = 'op_rdate'; // 주문일
else if($pass_date_type == 'pdate') $date_type = 'op_paydate'; // 결제일
else if($pass_date_type == 'ddate') $date_type = 'op_senddate'; // 배송완료일
if($pass_sdate && $pass_edate)  $s_query .= " AND ({$date_type} between '{$pass_sdate}' and '{$pass_edate}') ";// - 검색기간
else if($pass_sdate) $s_query .= " AND {$date_type} >= '{$pass_sdate}' ";
else if($pass_edate) $s_query .= " AND {$date_type} <= '{$pass_edate}' ";
$DateTypeArr = array(
	'op_complain_date'=>'신청일',
	'op_rdate'=>'주문일',
	'op_paydate'=>'결제일',
	'op_senddate'=>'배송완료일'
);

if($pass_company) $s_query .= " AND op.op_partnerCode = '{$pass_company}' "; // 공급업체
if($pass_pname) $s_query .= " AND op.op_pname like '%{$pass_pname}%' "; //상품명
if($pass_paymethod) $s_query .= " and o.o_paymethod = '{$pass_paymethod}' ";  // 결제수단


// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = $date_type;
if(!$so) $so = 'desc';
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ("
	select
		count(*) as cnt
	from
		smart_order_product as op inner join
		smart_order as o on (o.o_ordernum=op.op_oordernum) left join
		smart_product as p on (p.p_code=op.op_pcode)
	where (1)
		{$s_query}
");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$res = _MQ_assoc("
	select
		op.*, o.*, p.p_name
	from
		smart_order_product as op inner join
		smart_order as o on (o.o_ordernum=op.op_oordernum) left join
		smart_product as p on (p.p_code=op.op_pcode)
	where (1)
		{$s_query}
	order by {$st} {$so} limit {$count}, {$listmaxcount}
");
?>
<div class="group_title"><strong>주문검색</strong></div>

<form action="<?php ECHO $_SERVER['PHP_SELF']; ?>" method="get">
	<input type="hidden" name="mode" value="search">
	<!-- ●폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<div class="data_form if_search if_nobottom">
		<table class="table_form">
			<colgroup>
				<col width="140"/><col width="*"/><col width="140"/><col width="*"/><col width="140"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>주문번호</th>
					<td><input type="text" name="pass_ordernum" value="<?php echo $pass_ordernum; ?>" class="design"></td>
					<th>주문상품명</th>
					<td><input type="text" name="pass_pname" value="<?php echo $pass_pname; ?>" class="design"></td>
					<th>배송상태</th>
					<td>
						<?php echo _InputRadio('pass_sendstatus', array('', '배송대기', '배송중', '배송완료'), $pass_sendstatus, '', array('전체', '배송대기', '배송중', '배송완료'), ''); ?>
					</td>
				</tr>
				<tr>
					<th>주문자 아이디</th>
					<td>
						<input type="text" name="pass_mid" value="<?php echo $pass_mid; ?>" class="design">
					</td>
					<th>주문자명</th>
					<td>
						<input type="text" name="pass_oname" value="<?php echo $pass_oname; ?>" class="design" style="width:100px;">
					</td>
					<th>교환/반품상태</th>
					<td>
						<?php echo _InputSelect('pass_complain', $arr_order_complain, $pass_complain, '', '', ''); ?>
					</td>
				</tr>
				<tr>
					<th>검색기간</th>
					<td colspan="5">
						<select name="pass_date_type">
							<option value="cdate"<?php echo ($pass_date_type == 'cdate'?' selected':null); ?>>신청일</option>
							<option value="odate"<?php echo ($pass_date_type == 'odate'?' selected':null); ?>>주문일</option>
							<option value="pdate"<?php echo ($pass_date_type == 'pdate'?' selected':null); ?>>결제일</option>
							<option value="ddate"<?php echo ($pass_date_type == 'ddate'?' selected':null); ?>>배송완료일</option>
						</select>
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



<!-- ● 데이터 리스트 -->
<div class="data_list">
	<!-- ●리스트 컨트롤영역 -->
	<div class="list_ctrl">
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
			<col width="70">
			<col width="90">
			<col width="130">
			<col width="*">
			<col width="300">
			<col width="300">
			<col width="73">
			<col width="80">
		</colgroup>
		<thead>
			<tr>
				<th scope="col">NO</th>
				<th scope="col"><?php echo str_replace(' ', '<br>', $DateTypeArr[$date_type]); ?></th>
				<th scope="col">주문번호<br>주문자명</th>
				<th scope="col">상품정보</th>
				<th scope="col">문의내용</th>
				<th scope="col">교환/반품상태</th>
				<th scope="col">배송상태</th>
				<th scope="col">상세보기</th>
			</tr>
		</thead>
		<?php if(count($res) > 0) { ?>
			<tbody>
				<?php
				foreach($res as $k=>$v) {
					$_num = $TotalCount-$coun -$k;
				?>
					<tr data-uid="<?php echo $v['op_uid']; ?>">
						<td><?php echo number_format($_num); ?></td>
						<td>
							<?php
							if($date_type == 'op_senddate') $v[$date_type] = $v[$date_type].' 00:00:00'; // 배송완료일은 date포맷이 다르기 때문에 맞춰준다
							?>
							<?php echo ($v[$date_type] == '0000-00-00 00:00:00'?'-':date('Y-m-d', strtotime($v[$date_type]))); ?>
							<?php if($v[$date_type] != '0000-00-00 00:00:00') { ?>
								<div class="t_light"><?php echo date('H:i', strtotime($v[$date_type])); ?></div>
							<?php } ?>
						</td>
						<td>
							<span class="block"><?php echo $v['op_oordernum']; ?></span>
							<?php echo showUserInfo($v['o_mid'], $v['o_oname']); ?>
						</td>
						<td class="t_left">
							<div class="order_item">
								<!-- 상품명 -->
								<div class="title bold"><?php echo $v['op_pname']; ?></div>
								<?php if($v['op_option1'] || $v['op_option2'] || $v['op_option3']) { ?>
									<div class="option bullet">
										<?php echo ($v['op_is_addoption']=="Y" ? "추가옵션 : " : "옵션" )." : ".trim($v['op_option1']." ".$v['op_option2']." ".$v['op_option3']); ?>
										<span class="t_black"><?php echo number_format($v['op_cnt']); ?>개</span>
									</div>
								<?php } else { ?>
									<div class="option bullet">
										<span class="t_black"><?php echo number_format($v['op_cnt']); ?>개</span>
									</div>
								<?php } ?>
							</div>
						</td>
						<td class="t_left">
							<?php echo nl2br(htmlspecialchars($v['op_complain_comment'])); ?>
						</td>
						<td>
							<div class="lineup-center">
								<?php if($v['op_cancel'] === 'Y') { ?>
									<div class="lineup-vertical">
										<div class="block t_black"><?php echo ($v['op_cancel_type'] == 'pg'?'PG연동':'적립금'); ?></div>
										<span class="c_tag h22 black t4">취소완료</span>
									</div>
								<?php } else if($v['op_cancel'] === 'R') { ?>
									<div class="lineup-vertical">
										<div class="block t_black"><?php echo ($v['op_cancel_type'] == 'pg'?'PG연동':'적립금'); ?></div>
										<span class="c_tag h22 gray t4">취소요청</span>
									</div>
								<?php } else { ?>
									<?php
									$arr_order_complain_sub = array('교환/반품신청', '교환/반품완료');
									if(in_array($v['op_complain'], array('완료/부분취소요청(PG연동)', '완료/부분취소요청(적립금 환불)'))) echo $v['op_complain'];
									else echo _InputSelect( "op_complain" , $arr_order_complain_sub , $v['op_complain'] , "" , "" , "");

									if(in_array($v['op_complain'], $arr_order_complain_sub)) {
									?>
										<a href="#none" onclick="return false;" class="c_btn h28 black js_submit">상태적용</a>
									<?php } ?>
								<?php } ?>
							</div>
						</td>
						<td><?php echo $arr_adm_button[$v['op_sendstatus']]; ?></td>
						<td>
							<div class="lineup-vertical">
								<a href="_order.form.php<?php echo URI_Rebuild('?', array('view'=>'order_complain', '_mode'=>'modify', '_ordernum'=>$v['op_oordernum'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 line">상세보기</a>
							</div>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		<?php } ?>
	</table>

	<?php if(count($res) <= 0) { ?>
		<!-- 내용없을경우 -->
		<div class="common_none"><div class="no_icon"></div><div class="gtxt">접수된 내용이 없습니다.</div></div>
	<?php } ?>

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
	</div>
</div>

<script type="text/javascript">
	// 교환/반품상태 변경
	$(document).on('click', '.js_submit', function(e) {
		e.preventDefault();
		var su = $(this).closest('tr');
		var _uid = su.data('uid');
		var op_complain = su.find('select[name^=op_complain] option:selected').val();
		var _url = '_order_complain.pro.php';
		if(!op_complain) {
			alert('교환/반품상태를 선택하세요.');
			su.find('select[name^=op_complain]').focus();
			return false;
		}
		_url = _url+'?&uid='+_uid+'&op_complain='+op_complain;
		common_frame.location.href = _url;
	});
</script>
<?php include_once('wrap.footer.php'); ?>