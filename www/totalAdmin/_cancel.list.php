<?php
include_once('wrap.header.php');

// 검색 체크
$s_query = " and o.npay_order = 'N' ";
$date_type = 'op_cancel_rdate';
if($pass_date_type == 'rdate') $date_type = 'op_cancel_rdate'; // 요청일
else if($pass_date_type == 'cdate') $date_type = 'op_cancel_cdate'; // 취소일
if($pass_sdate && $pass_edate) { $s_query .= " AND date(op_cancel_rdate) between '{$pass_sdate}' and '{$pass_edate}' "; }// - 검색기간
else if($pass_sdate) $s_query .= " AND date(op_cancel_rdate) >= '{$pass_sdate}' ";
else if($pass_edate) $s_query .= " AND date(op_cancel_rdate) <= '{$pass_edate}' ";
if($pass_ordernum) $s_query .= " AND op.op_oordernum = '{$pass_ordernum}' "; //주문번호
if($pass_orderid) $s_query .= " AND o.o_mid like '%{$pass_orderid}%' "; //주문자ID
if($pass_ordername) $s_query .= " AND o.o_oname like '%{$pass_ordername}%' "; //주문자이름
if($pass_orderhtel) $s_query .= " AND (replace(o.o_otel, '-', '') like '%". rm_str($pass_orderhtel) ."%' or replace(o.o_ohp, '-', '') like '%". rm_str($pass_orderhtel) ."%') "; //주문자연락처
if($pass_cancel) $s_query .= " AND op.op_cancel = '{$pass_cancel}' ";
else $s_query .= " AND (op.op_cancel = 'Y' OR op.op_cancel = 'R')";
if($pass_com) $s_query .= " and op.op_partnerCode = '{$pass_com}' "; // 입점업체


// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = 'op.op_cancel_rdate';
if(!$so) $so = 'desc';
$count = $listpg*$listmaxcount-$listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_order_product as op left join smart_order as o on (o.o_ordernum = op.op_oordernum) where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$que = "
	select
		* ,
		o.o_otel as ordertel,
		o.o_ohp as orderhtel
	from
		smart_order_product as op left join
		smart_order as o on (o.o_ordernum = op.op_oordernum)
	where (1)
		{$s_query}
	order by {$st} {$so}
	limit {$count}, {$listmaxcount}
";
$res = _MQ_assoc($que);
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
					<th>상태</th>
					<td>
						<select name="pass_cancel">
							<option value="">- 선택 -</option>
							<option value="Y"<?php echo ($pass_cancel=='Y'?' selected':null); ?>>취소완료</option>
							<option value="R"<?php echo ($pass_cancel=='R'?' selected':null); ?>>취소요청중</option>
						</select>
					</td>
					<th>검색기간</th>
					<td>
						<select name="pass_date_type">
							<option value="rdate"<?php echo ($pass_date_type == 'rdate'?' selected':null); ?>>요청일</option>
							<option value="cdate"<?php echo ($pass_date_type == 'cdate'?' selected':null); ?>>취소일</option>
						</select>
						<input type="text" name="pass_sdate" class="design js_pic_day_max_today" style="width:85px;" value="<?php echo $pass_sdate; ?>" readonly />
						<span class="fr_tx">-</span>
						<input type="text" name="pass_edate" class="design js_pic_day_max_today" style="width:85px;" value="<?php echo $pass_edate; ?>" readonly />
					</td>
				</tr>
				<tr>
					<th>주문자 아이디</th>
					<td><input type="text" name="pass_orderid" value="<?php echo $pass_orderid; ?>" class="design"></td>
					<th>주문자명</th>
					<td><input type="text" name="pass_ordername" value="<?php echo $pass_ordername; ?>" class="design"></td>
					<th>주문자 연락처</th>
					<td><input type="text" name="pass_orderhtel" value="<?php echo $pass_orderhtel; ?>" class="design"></td>
				</tr>
				<?php
					if($SubAdminMode === true && $AdminPath == 'totalAdmin') { // 입점업체 검색기능 2016-05-26 LDD
						$arr_customer = arr_company();
						$arr_customer2 = arr_company2();
				?>
				<tr>
					<th>입점업체</th>
					<td colspan="5">
						<!-- 20개 이상일때만 select2적용 -->
						<?php if(sizeof($arr_customer) > 20){ ?>
						<link href="/include/js/select2/css/select2.css" type="text/css" rel="stylesheet">
						<script src="/include/js/select2/js/select2.min.js"></script>
						<script>$(document).ready(function() { $('.select2').select2(); });</script>
						<?php } ?>
						<?php echo _InputSelect( 'pass_com' , array_keys($arr_customer) , $pass_com , ' class="select2" ' , array_values($arr_customer) , '-입점업체-'); ?>
					</td>
				</tr>
				<?php } ?>
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
	<form action="_cancel.pro.php" method="post" class="form_list">
		<input type="hidden" name="_mode" value="get_search_excel">
		<input type="hidden" name="_seachcnt" value="<?php echo $TotalCount; ?>">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
		<input type="hidden" name="_search_que" value="<?php echo enc('e', $s_query); ?>">
		<input type="hidden" name="st" value="<?php echo $st; ?>">
		<input type="hidden" name="so" value="<?php echo $so; ?>">

		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
				<a href="#none" onclick="mass_cancel(); return false;" class="c_btn h27 gray">선택 주문취소</a>
			</div>
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
			<colgroup>
				<col width="45">
				<col width="70">
				<col width="80">
				<col width="150">
				<col width="105">
				<col width="*">
				<col width="90">
				<col width="90">
				<col width="90">
				<col width="90">
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">NO</th>
					<th scope="col">요청일<br>취소일</th>
					<th scope="col">주문번호<br>주문자명</th>
					<th scope="col">연락처</th>
					<th scope="col">상품정보</th>
					<th scope="col">결제방법<br>환불금액</th>
					<th scope="col">진행상태</th>
					<th scope="col">환불수단<br>취소상황</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<?php if(count($res) > 0) { ?>
				<tbody>
					<?php
					foreach($res as $k=>$v) {
						$_num = $TotalCount-$count -$k;
						$cancel_price = $v['op_price'] * $v['op_cnt'] + $v['op_delivery_price'] + $v['op_add_delivery_price'] - $v['op_cancel_discount_price'] ;// 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC
					?>
						<tr>
							<td>
								<label class="design"><input type="checkbox" name="OpUid[]" class="js_ck" value="<?php echo $v['op_uid']; ?>"></label>
							</td>
							<td><?php echo number_format($_num); ?></td>
							<td>
								<?php echo date('Y.m.d', strtotime($v['op_cancel_rdate'])); ?>
								<?php echo ($v['op_cancel_cdate'] != '0000-00-00 00:00:00'?'<br>'.date('Y.m.d', strtotime($v['op_cancel_cdate'])):'<br>-'); ?>
							</td>
							<td>
								<span class="block"><?php echo $v['op_oordernum']; ?></span>
								<?php echo showUserInfo($v['o_mid'], $v['o_oname']); ?>
							</td>
							<td>
								<?php echo ($v['orderhtel']?$v['orderhtel']:'-'); ?><br>
								<?php echo ($v['ordertel']?$v['ordertel']:'-'); ?>
							</td>
							<td class="t_left">
								<div class="order_item">
									<!-- 상품명 -->
									<div class="title bold">
										<?php echo ($SubAdminMode === true && $AdminPath == 'totalAdmin' && $v['op_partnerCode'] ? "<span style='font-weight:normal; color:#999;'>(".$arr_customer2[$v['op_partnerCode']] . ")</span>" : "") ; // JJC : 입점관리 : 2020-09-17?>
										<?php echo $v['op_pname']; ?>
									</div>
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

								<?php if($v['op_cancel_bank_account'] <> '' || $v['op_cancel_bank_name'] <> '' || $v['op_cancel_msg'] <> ''){ ?>
									<div class="clear_both"></div>
									<table class="table_list" style="margin-top:3px;">
										<tbody>
											<?php if($v['op_cancel_type'] == 'pg' && in_array($v['o_paymethod'], array('online', 'virtual'))){ ?>
											<tr>
												<td class="t_left" colspan="2">
													<strong class="bold">환불계좌</strong> : <?php echo $ksnet_bank[$v['op_cancel_bank']]; ?> <?php echo $v['op_cancel_bank_account']; ?> <?php echo $v['op_cancel_bank_name']; ?><br>
												</td>
											</tr>
											<?php } ?>
											<?php if($v['op_cancel_msg'] <> ''){ ?>
											<tr>
												<td class="t_left">
													<strong class="bold">요청내용</strong> :
													<div class="dash_line"></div>
													<?php echo nl2br(htmlspecialchars($v['op_cancel_msg'])); ?>
												</td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
								<?php } ?>
							</td>
							<td>
								<div class="lineup-vertical">
                                    <?php 
                                        // LCY : 2021-07-04 : 신용카드 간편결제 추가
                                        if( $v['o_easypay_paymethod_type'] != ''){ 
                                            echo $arr_adm_button["E".$arr_available_easypay_pg_list[$v['o_easypay_paymethod_type']]];
                                        }else{
                                            echo $arr_adm_button[$arr_payment_type[$v['o_paymethod']]];
                                        }
                                    ?>
									<?php if($cancel_price > 0) { ?>
										<div class="block bold t_black"><?php echo number_format($cancel_price); ?>원</div>
									<?php } else { ?>
										<div class="c_tag h22 yellow t5">전액적립금</div>
									<?php } ?>
								</div>
							</td>
							<td><div class="lineup-vertical"><?php echo $arr_adm_button[$v['op_sendstatus']]; ?></div></td>
							<td>
								<div class="lineup-vertical">
									<div class="block t_black"><?php echo ($v['op_cancel_type'] == 'pg'?'PG연동':'완료/부분취소요청'); ?></div>
									<?php echo ($v['op_cancel'] == 'R'?'<span class="c_tag h22 gray t4">취소요청</span>':'<span class="c_tag h22 black t4">취소완료</span>'); ?>
								</div>
							</td>
							<td>
								<div class="lineup-vertical">
									<a href="_cancel.form.php<?php echo URI_Rebuild('?', array('_mode'=>'modify', '_ordernum'=>$v['op_oordernum'], 'uid'=>$v['op_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 line">상세보기</a>
									<?php if($v['op_cancel'] != 'Y'){ ?>
									<a href="_cancel.pro.php<?php echo URI_Rebuild('?', array('_mode'=>'cancel', 'ordernum'=>$v['op_oordernum'], 'op_uid'=>$v['op_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 gray">취소처리</a>
									<a href="_cancel.pro.php<?php echo URI_Rebuild('?', array('_mode'=>'req_cancel', 'ordernum'=>$v['op_oordernum'], 'op_uid'=>$v['op_uid'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 red">요청삭제</a>
									<?php } ?>
								</div>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			<?php } ?>
		</table>
	</form>

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
	// 선택 엑셀 다운로드
	function select_excel_send() {
		var cnt = $('.js_ck:checked').length;
		if(cnt <= 0) return alert('엑셀변환하실 주문을 1건 이상 선택 바랍니다.');
		$('.form_list').find('input[name=_mode]').val('get_excel');
		$('.form_list').submit();
	}

	// 검색 엑셀 다운로드
	function search_excel_send() {
		$('.form_list').find('input[name=_mode]').val('get_search_excel');
		$('.form_list').submit();
	}

	// 선택 주문 취소
	function mass_cancel() {
		$('.form_list').find('input[name=_mode]').val('mass');
		$('.form_list').submit();
	}
</script>
<?php include_once('wrap.footer.php'); ?>