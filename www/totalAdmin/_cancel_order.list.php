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


// 검색 체크
$s_query = " and o_canceled != 'N' and o_moneyback_status != 'none' ";
if($pass_ordernum) $s_query .= " AND o_ordernum = '{$pass_ordernum}' "; // 주문번호
if($pass_orderid) $s_query .= " AND o_mid like '%{$pass_orderid}%' "; // 주문자ID
if($pass_ordername) $s_query .= " AND o_oname like '%{$pass_ordername}%' "; // 주문자이름
if($pass_moneyback_status) $s_query .= " and o_moneyback_status = '{$pass_moneyback_status}' "; // 환불상태
if($pass_moneyback_comment) $s_query .= " AND o_moneyback_comment like '%{$pass_moneyback_comment}%' "; // 환불계좌정보
if($pass_ordetel) $s_query .= " and (replace(`o_ohp`, '-', '') like '%".str_replace('-', '', $pass_ordetel)."%' or replace(`o_otel`, '-', '') like '%".str_replace('-', '', $pass_ordetel)."%') "; // 연락처 검색
if($pass_paymethod) $s_query .= " and o_paymethod = '{$pass_paymethod}' "; // 결제수단 검색

$date_type = 'o_moneyback_date';
if($pass_date_type == 'rdate') $date_type = 'o_moneyback_comdate'; // 환불처리일
else if($pass_date_type == 'cdate') $date_type = 'o_moneyback_date'; // 환불요청일
if($pass_sdate && $pass_edate)  $s_query .= " AND ({$date_type} between '{$pass_sdate}' and '". date('Y-m-d', strtotime('+1day', strtotime($pass_edate))) ."') ";// - 검색기간
else if($pass_sdate) $s_query .= " AND {$date_type} >= '{$pass_sdate}' ";
else if($pass_edate) $s_query .= " AND {$date_type} < '". date('Y-m-d', strtotime('+1day', strtotime($pass_edate))) ."' ";


	// ----- JJC : 입점관리 : 2020-09-17 -----
	if($pass_com) {
		$s_query .= "
			and (
				SELECT
					count(*)
				FROM smart_order_product as op
				WHERE
					op_oordernum = o_ordernum AND
					op_partnerCode = '". addslashes($pass_com) ."'
			) > 0
		";
	}
	// ----- JJC : 입점관리 : 2020-09-17 -----


// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = $date_type;
if(!$so) $so = 'desc';
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_order where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
if(!$st) $st = 'o_moneyback_date';
if(!$so) $so = 'desc';
$que = "
	select
		o.*,
		(select count(*) from smart_order_product as op where op.op_oordernum = o.o_ordernum) as op_cnt,
		(
			select
				concat(op.op_pname, '|' , op.op_partnerCode ) /* JJC : 입점관리 : 2020-09-17 */
			from
				smart_order_product as op
			where
				op.op_oordernum = o.o_ordernum
			order by op.op_uid asc limit 1
		) as p_info
	from
		smart_order as o
	where (1)
		{$s_query}
	order by {$st} {$so} limit {$count}, {$listmaxcount}
";
$res = _MQ_assoc($que);
?>
<div class="group_title"><strong>주문검색</strong></div>

<!-- ●폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
<form action="<?php ECHO $_SERVER['PHP_SELF']; ?>" method="get">
	<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
	<input type="hidden" name="mode" value="search">
	<div class="data_form if_search">
		<table class="table_form">
			<colgroup>
				<col width="140"/><col width="*"/><col width="140"/><col width="*"/><col width="140"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>주문번호</th>
					<td><input type="text" name="pass_ordernum" class="design" value="<?php echo $pass_ordernum; ?>" /></td>
					<th>주문자명</th>
					<td><input type="text" name="pass_ordername" class="design" style="width:100px;" value="<?php echo $pass_ordername; ?>" /></td>
					<th>주문자 아이디</th>
					<td><input type="text" name="pass_orderid" class="design" value="<?php echo $pass_orderid; ?>" /></td>
				</tr>
				<tr>
					<th>주문자 연락처</th>
					<td><input type="text" name="pass_ordetel" class="design" value="<?php echo $pass_ordetel; ?>" /></td>
					<th>결제수단</th>
					<td>
						<?php echo _InputSelect('pass_paymethod', array_keys($arr_payment_type), $pass_paymethod, '', array_values($arr_payment_type), '전체'); ?>
					</td>
					<th>환불계좌정보</th>
					<td><input type="text" name="pass_moneyback_comment" class="design" value="<?php echo $pass_moneyback_comment; ?>" /></td>
				</tr>
				<tr>
					<th>환불상태</th>
					<td>
						<?php echo _InputRadio('pass_moneyback_status', array('', 'request', 'complete'), $pass_moneyback_status, '', array('전체', '환불요청', '환불완료')); ?>
					</td>
					<th>환불요청일</th>
					<td colspan="3">
						<select name="pass_date_type">
							<option value="rdate"<?php echo ($pass_date_type == 'rdate'?' selected':null); ?>>환불요청일</option>
							<option value="cdate"<?php echo ($pass_date_type == 'cdate'?' selected':null); ?>>환불처리일</option>
						</select>
						<input type="text" name="pass_sdate" class="design js_pic_day_max_today" style="width:85px;" value="<?php echo $pass_sdate; ?>" readonly />
						<span class="fr_tx">-</span>
						<input type="text" name="pass_edate" class="design js_pic_day_max_today" style="width:85px;" value="<?php echo $pass_edate; ?>" readonly />
					</td>
				</tr>


				<?php
					// ----- JJC : 입점관리 : 2020-09-17 -----
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
				<?php } // ----- JJC : 입점관리 : 2020-09-17 -----?>


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
<!-- /폼 영역 -->



<!-- ● 데이터 리스트 -->
<form name="frm" class="form_list" action="_cancel_order.pro.php" method="post">
	<input type="hidden" name="_mode" value="">
	<input type="hidden" name="_seachcnt" value="<?php echo $TotalCount; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="_search_que" value="<?php echo enc('e', $s_query); ?>">
	<input type="hidden" name="_submode" value="_cancel_order">
	<div class="data_list">

		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
				<a href="#none" onclick="mass_complete(); return false;" class="c_btn h27 gray">선택 환불완료</a>
				<a href="#none" onclick="selectCancel(); return false;" class="c_btn h27 gray">선택주문취소</a>
			</div>
			<div class="right_box">
				<a href="#none" onclick="select_excel_send(); return false" class="c_btn icon icon_excel">선택 엑셀다운로드</a>
				<a href="#none" onclick="search_excel_send(); return false;" class="c_btn icon icon_excel">검색 엑셀다운로드<?php echo ($TotalCount > 0?'('.number_format($TotalCount).')':null); ?></a>
				<select onchange="location.href=this.value;">
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
					<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
				</select>
			</div>
		</div>
		<!-- / 리스트 컨트롤영역 -->


		<table class="table_list">
			<colgroup>
				<col width="45"/><col width="70"/><col width="90"/><col width="90"/><col width="90"/><col width="140"/><col width="*"/>
				<col width="180"/><!-- SSJ : 주문/결제 통합 패치 : 2021-02-24 -->
				<col width="100"/><col width="80"/><col width="90"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">NO</th>
					<th scope="col">환불요청일</th>
					<th scope="col">환불처리일</th>
					<th scope="col">환불상태</th>
					<th scope="col">주문번호<br/>주문자명</th>
					<th scope="col">상품정보</th>
					<th scope="col">연락처<br/>환불계좌</th>
					<th scope="col">결제수단<br/>환불금액</th>
					<th scope="col">주문상태</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<?php if(count($res) > 0) { ?>
				<tbody>
					<?php
					foreach($res as $k=>$v) {
						$_num = $TotalCount-$count-$k;
					?>
						<tr>
							<td>
								<label class="design">
									<input type="checkbox" name="chk_ordernum[<?php echo $v['o_ordernum']; ?>]" class="class_ordernum js_ck" value="Y">
								</label>
							</td>
							<td>
								<?php echo number_format($_num); ?>
							</td>
							<td>
								<?php echo ($v['o_moneyback_date'] == '0000-00-00 00:00:00'?'-':date('Y-m-d', strtotime($v['o_moneyback_date']))); ?>
								<?php if($v['o_moneyback_date'] != '0000-00-00 00:00:00') { ?>
									<div class="t_light"><?php echo date('H:i', strtotime($v['o_moneyback_date'])); ?></div>
								<?php } ?>
							</td>
							<td>
								<?php echo ($v['o_moneyback_comdate'] == '0000-00-00 00:00:00'?'-':date('Y-m-d', strtotime($v['o_moneyback_comdate']))); ?>
								<?php if($v['o_moneyback_comdate'] != '0000-00-00 00:00:00') { ?>
									<div class="t_light"><?php echo date('H:i', strtotime($v['o_moneyback_comdate'])); ?></div>
								<?php } ?>
							</td>
							<td>
								<div class="lineup-center">
									<?php if($v['o_moneyback_status'] == 'request') { ?>
										<span class="c_tag h22 aqua t5">환불요청중</span>
									<?php } else { ?>
										<?php if($v['o_moneyback_status'] == 'complete') { ?>
											<span class="c_tag h22 blue t5">환불완료</span>
										<?php } ?>
									<?php } ?>
								</div>
							</td>
							<td>
								<span class="block"><?php echo $v['o_ordernum']; ?></span>
								<?php echo showUserInfo($v['o_mid'], $v['o_oname']); ?>
							</td>
							<td>
								<!-- 상품정보 -->
								<div class="order_item">
									<!-- 상품명 -->
									<div class="title bold">
										<?php
											// --- JJC : 입점관리 : 2020-09-17 ---
											$app_pname = explode('|', $v['p_info']);
											echo ($SubAdminMode === true && $AdminPath == 'totalAdmin' && $app_pname[1] ? "<span style='font-weight:normal; color:#999;'>(".$arr_customer2[$app_pname[1]] . ")</span> " : "") ;
											echo $app_pname[0];
											// --- JJC : 입점관리 : 2020-09-17 ---
										?>
									</div>
									<?php if($v['op_cnt'] > 1) { ?>
										<div class="option bullet">
											<span class="t_light normal"><span class="t_black normal">외</span> <span class="t_black normal"><?php echo number_format(($v['op_cnt']-1)); ?>개</span></span>
										</div>
									<?php } ?>
								</div>
							</td>
							<td>
								<?php if($v['o_otel'] || $v['o_ohp']) { ?><div class="block bold t_black"><?php echo implode(" , " , array_filter(array(trim($v['o_otel']) , trim($v['o_ohp'])))); ?></div><?php } ?>
								<?php echo str_replace('환불계좌: ', '', $v['o_moneyback_comment']); ?>
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
									<?php if($v['o_price_real'] > 0) { ?>
										<div class="block bold t_black"><?php echo number_format($v['o_price_real']); ?>원</div>
									<?php } else { ?>
										<span class="c_tag h22 yellow t5">전액적립금</span>
									<?php } ?>
								</div>
							</td>
							<td>
								<div class="lineup-center">
									<?php echo $arr_adm_button[$v['o_status']]; ?>
								</div>
							</td>
							<td>
								<div class="lineup-vertical">
									<?php if($v['o_moneyback_status'] == 'request') { ?>
										<!-- SSJ : 주문/결제 통합 패치 : 2021-02-24 -->
										<a href="_order.form.php?_mode=modify&_ordernum=<?php echo $v['o_ordernum']; ?>&view=cancel_order&_PVSC=<?php echo $_PVSC; ?>" class="c_btn h22 white t6">상세보기</a>
										<a href="_cancel_order.pro.php?_mode=complete&ordernum=<?php echo $v['o_ordernum']; ?>&_PVSC=<?php echo $_PVSC; ?>" onclick="if(!confirm('정말 실행하시겠습니까?')) return false;" class="c_btn h22 gray t6">환불완료처리</a>
										<a href="_cancel_order.pro.php?_mode=reset&ordernum=<?php echo $v['o_ordernum']; ?>&_PVSC=<?php echo $_PVSC; ?>" onclick="if(!confirm('정말 실행하시겠습니까?')) return false;" class="c_btn h22 gray t6">환불요청취소</a>
									<?php } else if($v['o_canceled']=='R'){ ?>
										<a href="_cancel_order.pro.php?_mode=request&ordernum=<?php echo $v['o_ordernum']; ?>&_PVSC=<?php echo $_PVSC; ?>" onclick="if(!confirm('정말 실행하시겠습니까?')) return false;" class="c_btn h22 t6">환불요청전환</a>
										<a href="#none" onclick="cancel('_order.pro.php<?php echo URI_Rebuild('?', array('_mode'=>'cancel', '_ordernum'=>$v['o_ordernum'], '_submode'=>'_cancel_order', '_PVSC'=>$_PVSC)); ?>'); return false;" class="c_btn h22 t6 gray">주문취소</a>
									<?php }else{ ?>
										<?php echo $arr_adm_button['취소완료']; ?>
										<span class="c_tag gray h22 t4">취소완료</span>
									<?php } ?>
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
</form>


<script type="text/javascript">
	// 선택 엑셀 다운로드
	function select_excel_send() {
		var cnt = $('.js_ck:checked').length;
		if(cnt <= 0) return alert('엑셀변환하실 주문을 1건 이상 선택 바랍니다.');
		$('.form_list').find('input[name=_mode]').val('select_excel');
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
	}

	// 검색 엑셀 다운로드
	function search_excel_send() {
		$('.form_list').find('input[name=_mode]').val('search_excel');
		$('.form_list').submit();
		$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
	}

	// 선택 환불완료 처리
	function mass_complete() {
		var cnt = $('.js_ck:checked').length;
		if(cnt <= 0) return alert('1건 이상 선택 바랍니다.');
		if(confirm('정말 선택하신 주문의 환불요청을 완료처리하겠습니까?')) {
			$('.form_list').find('input[name=_mode]').val('mass');
			$('.form_list').submit();
			$('.form_list').find('input[name=_mode]').val(''); // _mode 초기화
		}
	}


	 function selectCancel() {
		 if($('.js_ck').is(':checked')){
			 if(confirm('정말 취소하시겠습니까?')){
				$('form[name=frm]').children('input[name=_mode]').val('mass_cancel');
				$('form[name=frm]').attr('action' , '_order.pro.php');
				document.frm.submit();
			 }
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }

</script>
<?php include_once('wrap.footer.php'); ?>