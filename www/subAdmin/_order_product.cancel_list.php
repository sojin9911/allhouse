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
$s_query = " and (o.o_canceled = 'Y' or op.op_cancel = 'Y') and o.o_paystatus = 'Y' and o.npay_order = 'N' and op.op_partnerCode = '{$com_id}' "; // 기본조건(취소 되지 않고 결제 상태인것) / 네이버페이 제외
if($pass_ordernum) $s_query .= " and op.op_oordernum = '{$pass_ordernum}' ";
if($pass_mid) $s_query .= " and = '{$pass_mid}' ";
if($pass_memtype) $s_query .= " and o.o_mid like '%{$pass_memtype}%' ";
if($pass_oname) $s_query .= " and o.o_oname like '%{$pass_oname}%' ";
if($pass_rname) $s_query .= " and o.o_rname like '%{$pass_rname}%' ";
if($pass_pname) $s_query .= " and op.op_pname like '%{$pass_pname}%' ";
if($pass_option) $s_query .= " and (op.op_option1 like '%{$pass_option}%' or op.op_option2 like '%{$pass_option}%' or op.op_option3 like '%{$pass_option}%') ";


// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = 'o_rdate';
if(!$so) $so = 'desc';
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ("
	select
		count(*) as cnt
	from
		smart_order_product as op left join
		smart_order as o on (o.o_ordernum=op.op_oordernum)
	where (1)
		{$s_query}
");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$res = _MQ_assoc("
	select
		o.*, op.*
	from
		smart_order_product as op left join
		smart_order as o on (o.o_ordernum=op.op_oordernum)
	where (1)
		{$s_query}
	order by {$st} {$so} limit {$count}, {$listmaxcount}
");
?>
<div class="group_title"><strong>주문검색</strong></div>

<!-- ●폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
<div class="data_form if_search">
	<form action="<?php ECHO $_SERVER['PHP_SELF']; ?>" method="get">
		<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
		<input type="hidden" name="mode" value="search">
		<!-- 폼테이블 3단 -->
		<table class="table_form">
			<colgroup>
				<col width="140"/><col width="*"/><col width="140"/><col width="*"/><col width="140"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>주문번호</th>
					<td><input type="text" name="pass_ordernum" value="<?php echo $pass_ordernum; ?>" class="design" value=""></td>
					<th>주문자 아이디</th>
					<td><input type="text" name="pass_mid" value="<?php echo $pass_mid; ?>" class="design" value=""></td>
					<th>회원타입</th>
					<td>
						<?php echo _InputRadio('pass_memtype', array('', 'Y', 'N'), $pass_memtype, '', array('전체', '회원', '비회원')); ?>
					</td>
				</tr>
				<tr>
					<th>주문자명</th>
					<td><input type="text" name="pass_oname" value="<?php echo $pass_oname; ?>" class="design" value="" style="width:100px;"></td>
					<th>수령자명</th>
					<td colspan="3"><input type="text" name="pass_rname" value="<?php echo $pass_rname; ?>" class="design" value="" style="width:100px;"></td>
				</tr>
				<tr>
					<th>주문상품명</th>
					<td><input type="text" name="pass_pname" value="<?php echo $pass_pname; ?>" class="design" value=""></td>
					<th>옵션명</th>
					<td colspan="3"><input type="text" name="pass_option" value="<?php echo $pass_option; ?>" class="design" value=""></td>
				</tr>
			</tbody>
		</table>
		<!-- 폼테이블 3단 -->


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
	</form>
</div>



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
			<col width="70">
			<col width="*">
			<col width="100">
			<col width="70">
			<col width="90">
		</colgroup>
		<thead>
			<tr>
				<th scope="col">NO</th>
				<th scope="col">주문일</th>
				<th scope="col">주문번호<br>주문자명</th>
				<th scope="col">수령자명</th>
				<th scope="col">상품정보</th>
				<th scope="col">상품가격</th>
				<th scope="col">구매수량</th>
				<th scope="col">취소형태</th>
			</tr>
		</thead>
		<?php if(count($res) > 0) { ?>
			<tbody>
				<?php
					foreach($res as $k=>$v) {
						$_num = $TotalCount-$coun -$k;
				?>
					<tr>
						<td><?php echo number_format($_num); ?></td>
						<td>
							<?php echo ($v['op_rdate'] == '0000-00-00 00:00:00'?'-':date('Y-m-d', strtotime($v['op_rdate']))); ?>
							<div class="t_light"><?php echo date('H:i', strtotime($v['op_rdate'])); ?></div>
						</td>
						<td>
							<span class="block"><?php echo $v['op_oordernum']; ?></span>
							<?php echo showUserInfo($v['o_mid'], $v['o_oname']); ?>
						</td>
						<td><?php echo $v['o_rname']; ?></td>
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
						<td class="t_black bold"><?php echo number_format($v['op_price']); ?>원</td>
						<td class="t_black bold"><?php echo number_format($v['op_cnt']); ?>개</td>
						<td>
							<div class="lineup-vertical">
								<?php echo ($v['op_cancel'] == 'Y'?'<div class="c_btn h22 gray line t4">부분취소</div>':null); ?>
								<?php echo ($v['o_canceled'] == 'Y'?'<div class="c_tag black h22 t4">주문취소</div>':null); ?>
							</div>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		<?php } ?>
	</table>


	<?php if(count($res) <= 0) { ?>
		<!-- 내용없을경우 -->
		<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
	<?php } ?>

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
	</div>
</div>
<?php include_once('wrap.footer.php'); ?>