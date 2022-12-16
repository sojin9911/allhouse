<?php
include_once('wrap.header.php');


// 주문 번호별 구분 색상
$DivisionColor = 'F5F5F5';

# 재귀 && 입점업체 조건을 위한 어드민 구분 판별
$AdminPathData = parse_url($_SERVER['REQUEST_URI']);
$AdminPathData = explode('/', $AdminPathData['path']);
$AdminPath = $AdminPathData[1]; unset($AdminPathData); // 'totalAdmin' or 'subAdmin'

# 쿼리 조건
$s_query = " where (1) and `o`.`npay_order` = 'Y' ".($AdminPath == 'subAdmin'?" and `op`.`op_partnerCode` = '{$_COOKIE["AuthCompany"]}' ":null);
if($pass_ordernum) $s_query .= " and `o`.`o_ordernum` like '%{$pass_ordernum}%' ";
if($pass_o_oname) $s_query .= " and `o`.`o_oname` like '%{$pass_o_oname}%' ";
if($pass_orderhtel) $s_query .= " and (replace(`o_ohp`, '-', '') like '%{$pass_orderhtel}%' or replace(`o_otel`, '-', '') like '%{$pass_orderhtel}%') ";
if($pass_sdate && $pass_edate) $s_query .= " and date(`o`.`o_rdate`) between '{$pass_sdate}' and '{$pass_edate}' ";
else if($pass_sdate) $s_query .= " and date(`o`.`o_rdate`) >= '{$pass_sdate}' ";
else if($pass_edate) $s_query .= " and date(`o`.`o_rdate`) <= '{$pass_edate}' ";
if($pass_mobile_order) $s_query .= " and `o`.`mobile` = '{$pass_mobile_order}' ";
if($pass_status) $s_query .= " and `op`.`npay_status` = '{$pass_status}' ";
if($pass_company) $s_query .= " and `op`.`op_partnerCode` = '{$pass_company}' ";
if($pass_paymethod) $s_query .= " and `o`.`o_paymethod` = '{$pass_paymethod}' ";


# 쿼리
$listmaxcount = 20;
if(!$listpg) $listpg = 1;
$count = $listpg * $listmaxcount - $listmaxcount;
$que = " select count(*) as `cnt` from `smart_order_product` as `op` left join `smart_order` as `o` on(`o`.`o_ordernum` = `op`.`op_oordernum`) {$s_query} ";
$res = _MQ($que);
$TotalCount = $res[cnt];
$Page = ceil($TotalCount / $listmaxcount);
$que = "
	select
		`op`.*,
		`o`.*,
		`op`.`npay_status` as `npay_status`,
		`o_rtel` as `ordertel`,
		`o_rhp` as `orderhtel`,
		`p_name`
	from
		`smart_order_product` as `op` left join
		`smart_order` as `o` on(`o`.`o_ordernum` = `op`.`op_oordernum`) left join
		`smart_product` as `p` on(`op`.`op_pcode` = `p`.`p_code`)
		{$s_query}
	order by `o_rdate` desc, `op_oordernum` asc, `op_uid` asc limit {$count}, {$listmaxcount}
";
$res = _MQ_assoc($que);
if(count($res) <= 0) $res = array();


# 공금업체 리스트 추출
$arr_customer = arr_company();
?>
<div class="group_title"><strong>주문검색</strong></div>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="_cpid" value="<?php echo $_cpid; ?>">
	<?php if($c) { ?><input type="hidden" name="test" value="<?php echo $c; ?>"><?php } echo PHP_EOL; ?>
	<!-- ●폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
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
					<td><input type="text" name="pass_o_oname" class="design" style="width:100px;" value="<?php echo $pass_o_oname; ?>" /></td>
					<th>주문자 연락처</th>
					<td><input type="text" name="pass_orderhtel" class="design" value="<?php echo $pass_orderhtel; ?>" /></td>
				</tr>
				<tr>
					<th>주문일자</th>
					<td>
						<input type="text" name="pass_sdate" value="<?php echo $pass_sdate; ?>" class="design js_pic_day_max_today" style="width:85px">
						<span class="fr_tx">-</span>
						<input type="text" name="pass_edate" value="<?php echo $pass_edate; ?>" class="design js_pic_day_max_today" style="width:85px">
					</td>
					<th>주문기기</th>
					<td>
						<label class="design"><input type="checkbox" name="pass_mobile_order" value=""<?php echo (empty($pass_mobile_order) || $pass_mobile_order == ''?' checked':null); ?>>전체</label>
						<label class="design"><input type="checkbox" name="pass_mobile_order" value="Y"<?php echo ($pass_mobile_order == 'Y'?' checked':null); ?>>PC주문</label>
						<label class="design"><input type="checkbox" name="pass_mobile_order" value="N"<?php echo ($pass_mobile_order == 'N'?' checked':null); ?>>모바일주문</label>
					</td>
					<th>진행상태</th>
					<td>
						<?php echo _InputSelect('pass_status', array('PAYED', 'PLACE', 'DISPATCHED', 'CANCELED'), $pass_status, '', array('결제완료', '발주처리', '배송처리', '취소'), '-상태-'); ?>
					</td>
				</tr>
				<?php if($AdminPath == 'totalAdmin' && $SubAdminMode === true) { ?>
				<tr>
					<th>공급업체</th>
					<td>
						<?php if(sizeof($arr_customer) > 20){ ?>
							<link href="/include/js/select2/css/select2.css" type="text/css" rel="stylesheet">
							<script src="/include/js/select2/js/select2.min.js"></script>
							<script>$(document).ready(function() { $('.select2').select2(); });</script>
						<?php } ?>
						<?php echo _InputSelect('pass_company', array_keys($arr_customer), $pass_company, ' class="select2"', array_values($arr_customer), '-공급업체-'); ?>
					</td>
					<th>결제수단</th>
					<td>
						<?php
						$arr_paymethod = array(
							"신용카드" => "card",
							"계좌이체" => "iche",
							"무통장입금" => "online",
							"포인트결제" => "point",
							"가상계좌" => "virtual",
							"휴대폰" => "phone"
						);
						echo _InputSelect('pass_paymethod', array_values($arr_paymethod), $pass_paymethod, '', array_keys($arr_paymethod), '전체');
						?>
					</td>
					<th></th>
					<td></td>
				</tr>
				<?php } ?>
				<tr>
					<td colspan="6">
						<!-- 여러줄 도움말 -->
						<div class="tip_box">
							<?php echo _DescStr('발주처리, 배송처리, 취소처리는 상세보기를 통해서만 가능 합니다. (네이버페이 제약사항)'); ?>
							<?php echo _DescStr('네이버페이 주문정보는 정산처리가 불가능 합니다. 네이버주문은 네이버페이 센터를 이용바랍니다.'); ?>
							<?php echo _DescStr('리스트에 출력되는 <span class="npay_tag"><strong>NPAY CODE</strong></span>는 네이버페이 주문코드 입니다.', 'black'); ?>
							<?php echo _DescStr('엑셀다운로드에 표기 되는 <em>N 포인트 사용</em>과 <em>N 적립금 사용</em>는 동일 주문번호의 전체 주문을 기준으로합니다.', 'black'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>


		<!-- 가운데정렬버튼 -->
		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"></span></li>
				<?php if($mode == 'search') { ?>
					<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="c_btn h34 black line normal">전체목록</a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<!-- /폼 영역 -->
</form>



<form action="_npay_order.pro.php" method="POST" class="form_list"<?php echo ($c?null:' target="common_frame"'); ?>>
	<input type="hidden" name="_mode" value="get_search_excel">
	<input type="hidden" name="_seachcnt" value="<?php echo $TotalCount; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="_search_que" value="<?php echo enc('e', $s_query); ?>">
	<?php if($c) { ?><input type="hidden" name="test" value="<?php echo $c; ?>"><?php } echo PHP_EOL; ?>
	<!-- ● 데이터 리스트 -->
	<div class="data_list">
		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="right_box">
				<a href="#none" onclick="select_excel_send(); return false;" class="c_btn icon icon_excel">선택 엑셀다운로드</a>
				<a href="#none" onclick="search_excel_send(); return false;" class="c_btn icon icon_excel">검색 엑셀다운로드<?php echo ($TotalCount > 0?'('.number_format($TotalCount).')':null); ?></a>
			</div>
		</div>
		<!-- / 리스트 컨트롤영역 -->


		<table class="table_list">
			<colgroup>
				<col width="45"/><col width="70"/>
				<col width="80"/><col width="150"/>
				<col width="105"/><col width="*"/>
				<?php if($AdminPath == 'totalAdmin' && $SubAdminMode === true) { ?><col width="100"/><?php } echo PHP_EOL; ?>
				<col width="100"/>
				<col width="90"/><col width="80"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="allchk" value="Y"></label></th>
					<th scope="col">NO</th>
					<th scope="col">주문일</th>
					<th scope="col">주문번호<br/>주문자명</th>
					<th scope="col">연락처</th>
					<th scope="col">상품정보</th>
					<?php if($AdminPath == 'totalAdmin' && $SubAdminMode === true) { ?><th scope="col">공급업체</th><?php } echo PHP_EOL; ?>
					<th scope="col">결제수단<br/>결제금액</th>
					<th scope="col">진행상태</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$tmpOrder = ''; // 주문 번호별 구분 색상 - 주문코드
				$tmpNum = 0; // 주문 번호별 구분 색상 - 주문번호별 넘버
				foreach($res as $k=>$v) {

					// // 주문 번호별 구분 색상 - 주문별 배경색 채우기
					if($tmpOrder != $v['op_oordernum']) $tmpNum++;
					if($tmpNum%2 === 0) $TDcolor = ' style="background-color:#'.$DivisionColor.'"';
					else $TDcolor = null;
					$tmpOrder = $v['op_oordernum'];


					// 순서
					$_num = $TotalCount - $count - $k;

					// 상태아이콘
					$StatusIcon = '';
					if($v['npay_status'] == 'PAYED') $StatusIcon = '<span class="c_tag blue h22 t4">결제완료</span>';
					if($v['npay_status'] == 'PLACE') $StatusIcon = '<span class="c_tag purple h22 t4">발주처리</span>';
					if($v['npay_status'] == 'DISPATCHED') $StatusIcon = '<span class="c_tag green h22 t4">배송처리</span>';
					if($v['npay_status'] == 'CANCELED') $StatusIcon = '<span class="c_tag light h22 t4">주문취소</span>';
				?>
					<tr>
						<td<?php echo $TDcolor; ?>>
							<label class="design"><input type="checkbox" name="op_uid[]" class="chk_box" value="<?php echo $v['op_uid']; ?>"></label>
						</td>
						<td<?php echo $TDcolor; ?>><?php echo number_format($_num); ?></td>
						<td<?php echo $TDcolor; ?>>
							<?php echo date('Y.m.d', strtotime($v['o_rdate'])); ?>
							<div class="t_light"><?php echo date('H:i', strtotime($v['o_rdate'])); ?></div>
						</td>
						<td<?php echo $TDcolor; ?>>
							<span class="block"><?php echo $v['op_oordernum']; ?></span>
							<?php echo showUserInfo($v['o_mid'], $v['o_oname']); ?>
						</td>
						<td<?php echo $TDcolor; ?>>
							<?php
							echo ($v['ordertel']?$v['ordertel'].'<br>':null);
							echo ($v['orderhtel']?$v['orderhtel']:null);
							?>
						</td>
						<td<?php echo $TDcolor; ?>>
							<?php if($v['mobile'] == 'Y') { ?>
								<span class="c_tag h18 mo">MO주문</span>
							<?php } else { ?>
								<span class="c_tag h18 t3 pc">PC주문</span>
							<?php } ?>
							<!-- 네이버페이코드 -->
							<span class="npay_tag"><strong>NPAY CODE</strong><em><?php echo $v['npay_order_code']; ?></em></span>
							<!-- 상품정보 -->
							<div class="order_item">
								<!-- 상품명 -->
								<div class="title">
									<?php echo htmlspecialchars_decode($v['op_pname']); ?>
									<span class="t_light normal"> x <span class="t_black normal"><?php echo number_format($v['op_cnt']); ?>개</span></span>
								</div>
								<!-- 옵션명, div반복 -->
								<?php echo ($v['op_option1']?'<div class="option bullet">'.($v['op_is_addoption'] == 'N'?'선택 : ':'추가 : ').htmlspecialchars_decode($v['op_option1']).'</div>':null); ?>
								<?php echo ($v['op_option2']?'<div class="option bullet">'.($v['op_is_addoption'] == 'N'?'선택 : ':'추가 : ').htmlspecialchars_decode($v['op_option2']).'</div>':null); ?>
								<?php echo ($v['op_option3']?'<div class="option bullet">'.($v['op_is_addoption'] == 'N'?'선택 : ':'추가 : ').htmlspecialchars_decode($v['op_option3']).'</div>':null); ?>
							</div>
						</td>
						<?php if($AdminPath == 'totalAdmin' && $SubAdminMode === true) { ?>
							<td<?php echo $TDcolor; ?>>
								<?php echo showCompanyInfo($v['op_partnerCode']); ?>
							</td>
						<?php } ?>
						<td<?php echo $TDcolor; ?>>
							<?php echo $arr_payment_type[$v['o_paymethod']]; ?>
							<div class="bold t_black"><?php echo number_format($v['op_price'] * $v['op_cnt']); ?>원</div>
						</td>
						<td<?php echo $TDcolor; ?>>
							<div class="lineup-vertical">
								<?php echo $StatusIcon; ?>
							</div>
						</td>
						<td<?php echo $TDcolor; ?>>
							<div class="lineup-center"><a href="_npay_order.form.php?_mode=modify&_uid=<?php echo $v['op_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>" class="c_btn h22">상세보기</a></div>
						</td>
					</tr>
				<?php } ?>
			</tbody>
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
</form>


<script type="text/javascript">
// 선택 엑셀 다운로드
function select_excel_send() {

	var cnt = $('.chk_box:checked').length;
	if(cnt <= 0) return alert('엑셀변환하실 주문을 1건 이상 선택 바랍니다.');

	$('.form_list').find('input[name=_mode]').val('get_excel');
	$('.form_list').submit();
}

// 검색 엑셀 다운로드
function search_excel_send() {

	$('.form_list').find('input[name=_mode]').val('get_search_excel');
	$('.form_list').submit();
}

$(function() {

	// 전체선택 or 해제
	$('body').delegate('.allchk', 'click', function() {

		var chk = $(this).is(':checked');
		if(chk === true) $('input:checkbox.chk_box').removeAttr('checked').attr('checked', true);
		else $('input:checkbox.chk_box').removeAttr('checked');

		$('.allchk').attr('checked', chk);
	});
});
</script>
<?php include_once('wrap.footer.php'); ?>