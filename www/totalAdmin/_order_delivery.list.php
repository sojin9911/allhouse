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
$s_query = " and o.o_canceled = 'N' and o.o_paystatus = 'Y' and npay_order = 'N' "; // 기본조건(취소 되지 않고 결제 상태인것) / 네이버페이 제외
if($pass_ordernum) $s_query .= " and o.o_ordernum like '%{$pass_ordernum}%' ";
if($pass_mid) $s_query .= " and o.o_mid like '%{$pass_mid}%' ";
if($pass_oname) $s_query .= " and o.o_oname like '%{$pass_oname}%' ";
if($pass_rname) $s_query .= " and o_rname like '%{$pass_rname}%' ";
if($pass_deposit) $s_query .= " and o_deposit like '%{$pass_deposit}%' ";
if($pass_status) $s_query .= " and o.o_status = '{$pass_status}' ";
if($pass_sendcompany) $s_query .= " and o.o_sendcompany = '{$pass_sendcompany}' ";
if($pass_sendnum) $s_query .= " and o.o_sendnum like '%{$pass_sendnum}%' ";
if($pass_sendstatus) $s_query .= " and o.o_sendstatus = '{$pass_sendstatus}' ";
if($pass_memtype) $s_query .= " and o.o_memtype = '{$pass_memtype}' ";
if($pass_settlement) $s_query .= " and o.o_paystatus3 = '{$pass_settlement}' ";


	// ----- JJC : 입점관리 : 2020-09-17 -----
	if($pass_com) {
		$s_query .= "
			and (
				SELECT 
					count(*)
				FROM smart_order_product as op
				WHERE 
					op.op_oordernum = o.o_ordernum AND
					op.op_partnerCode = '". addslashes($pass_com) ."'
			) > 0
		";
	}
	// ----- JJC : 입점관리 : 2020-09-17 -----


// 데이터 조회
if(!$listmaxcount) $listmaxcount = 20;
if(!$listpg) $listpg = 1;
if(!$st) $st = 'o_rdate';
if(!$so) $so = 'desc';
$count = $listpg * $listmaxcount - $listmaxcount;
$res = _MQ(" select count(*) as cnt from smart_order as o where (1) {$s_query} ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount/$listmaxcount);
$res = _MQ_assoc("
	select
		o.*,
		(
			select
				concat(op.op_pname, '|', if(op.op_option1 != '', op.op_option1, ''), if(op.op_option2 != '', concat(' ', op.op_option2), ''), if(op.op_option3 != '', concat(' ', op.op_option3), ''), ' ', op.op_cnt, '개', '|', op.op_partnerCode ) /* JJC : 입점관리 : 2020-09-17 */
			from
				smart_order_product as op left join
				smart_product as p on (p.p_code=op.op_pcode)
			where
				op.op_oordernum = o.o_ordernum
			order by op.op_uid asc
			limit 0 , 1
		) as app_pname,
		(select count(*) from smart_order_product as op2 where op2.op_oordernum = o.o_ordernum) as app_pcnt,
		(select count(*) from smart_order_product as op3 where op3.op_oordernum = o.o_ordernum and op3.op_cancel != 'N') as app_cancel_pcnt
	from
		smart_order as o
	where (1)
		{$s_query} and
		(select count(*) from smart_order_product as op2 where op2.op_oordernum = o.o_ordernum)-(select count(*) from smart_order_product as op3 where op3.op_oordernum = o.o_ordernum and op3.op_cancel != 'N') > 0
	order by {$st} {$so} limit {$count}, {$listmaxcount}
");
?>
<form action="_order_delivery.excel_form.php" method="post" enctype="multipart/form-data">
	<!-- ● 단락타이틀 -->
	<div class="group_title">
		<strong>주문검색</strong>
		<!-- 해당페이지의 등록/업로드 버튼 있을 경우 -->
		<div class="btn_box"><a href="#none" class="c_btn h46 red line js_open_excel_box">일괄업로드</a></div>

		<!-- 엑셀일괄등록 열림 -->
		<div class="open_excel js_excel_box" style="display: none;">
			<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>일괄업로드</th>
						<td>
							<div class="input_file" style="width:300px">
								<input type="text" id="fakeFileTxt" class="fakeFileTxt" readonly="readonly" disabled="">
								<div class="fileDiv">
									<input type="button" class="buttonImg" value="파일찾기">
									<input type="file" name="excel_file" class="realFile" onchange="javascript:document.getElementById('fakeFileTxt').value = this.value; return false;">
								</div>
							</div>
							<span class="c_btn h27 black"><input type="submit" value="업로드" /></span>
							<div class="dash_line"><!-- 점선라인 --></div>
							<div class="tip_box">
								<?php echo _DescStr('<u>선택 엑셀다운로드</u> 또는 <u>검색 엑셀다운로드</u>를 통하여 받은 데이터를 수정 후 업로드 바랍니다.'); ?>
								<?php echo _DescStr('배송주문관리에서 받은 엑셀 파일만 사용 가능합니다.', 'black'); ?>
								<?php echo _DescStr('데이터를 수정 시 <u>배송상태</u>, <u>택배사</u>, <u>송장번호</u>만 수정 반영 됩니다.'); ?>
								<?php echo _DescStr('파일은 최대 '.$MaxUploadSize.'까지 업로드 가능 하며, 용량에 따라 다소시간이 걸릴 수 있습니다.'); ?>
								<?php echo _DescStr('일괄업로드는 "파일업로드" - "업로드 수정/확인" - "등록처리" 단계를 거쳐 처리됩니다.'); ?>
								<?php echo _DescStr('엑셀97~2003 버전 파일만 업로드가 가능하므로, 엑셀 2007이상 버전은(xlsx) 다른 이름저장을 통해 97~2003버전으로 저장하여 등록하시기 바랍니다.');?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</form>



<form action="<?php ECHO $_SERVER['PHP_SELF']; ?>" method="get">
	<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
	<!-- ●폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<div class="data_form if_search">
		<!-- 폼테이블 3단 -->
		<input type="hidden" name="mode" value="search">
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
					<td><input type="text" name="pass_rname" value="<?php echo $pass_rname; ?>" class="design" value="" style="width:100px;"></td>
					<th>배송상태</th>
					<td>
						<?php echo _InputRadio('pass_sendstatus', array('', '구매발주', '배송준비', '배송중', '배송완료'), $pass_sendstatus, '', array('전체', '구매발주', '배송준비', '배송중', '배송완료'), ''); ?>
					</td>
				</tr>
				<tr>
					<th>택배사</th>
					<td>
						<?php echo _InputSelect('pass_sendcompany', array_keys($arr_delivery_company), $pass_sendcompany, '', '', ''); ?>
					</td>
					<th>송장번호</th>
					<td><input type="text" name="pass_sendnum" value="<?php echo $pass_sendnum; ?>" class="design" value=""></td>
					<th>정산상태</th>
					<td>
						<?php echo _InputRadio('pass_settlement', array('', 'none', 'ready', 'complete'), $pass_settlement, '', array('전체', '정산무관', '정산대기', '정산완료'), ''); ?>
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


				<tr>
					<td colspan="6">
						<?php echo _DescStr('배송상태를 <em>배송준비</em>상태로 변경하면 사용자의 직접 주문취소가 제한됩니다.'); ?>
					</td>
				</tr>
			</tbody>
		</table>
		<!-- 폼테이블 3단 -->


		<!-- 가운데정렬버튼 -->
		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"/></span></li>
				<?php if($mode == 'search') { ?>
					<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal">전체목록</a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</form>



<!-- ● 데이터 리스트 -->
<div class="data_list">
	<form action="_order_delivery.pro.php" method="post" class="form_list"<?php echo ($c?null:' target="common_frame"'); ?>>
		<input type="hidden" name="_mode" value="get_search_excel">
		<input type="hidden" name="_seachcnt" value="<?php echo $TotalCount; ?>">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
		<input type="hidden" name="_search_que" value="<?php echo enc('e', $s_query); ?>">
		<input type="hidden" name="st" value="<?php echo $st; ?>">
		<input type="hidden" name="so" value="<?php echo $so; ?>">
		<?php if($c) { ?><input type="hidden" name="test" value="<?php echo $c; ?>"><?php } echo PHP_EOL; ?>
		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
				<?php echo _InputSelect('select_sendstatus', $arr_order_product_sendstatus, ' class="js_select_sendstatus"', '', '', ''); ?>
				<a href="#none" onclick="selectSendstatus(); return false;" class="c_btn h27 gray">일괄배송상태변경</a>
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
				<col width="40"/><col width="70"/><col width="90"/><col width="135"/><col width="70"/><col width="*"/><col width="70"/><col width="165"/><col width="155"/><col width="70"/><col width="80"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
					<th scope="col">NO</th>
					<th scope="col">주문일</th>
					<th scope="col">주문번호<br/>주문자명</th>
					<th scope="col">수령자명</th>
					<th scope="col">상품정보</th>
					<th scope="col">정산상태</th>
					<th scope="col">택배사<br/>송장번호</th>
					<th scope="col">배송상태</th>
					<th scope="col">배송조회</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<?php if(count($res) > 0) { ?>
				<tbody>
					<?php
					foreach($res as $k=>$v) {
						$_num = $TotalCount-$count -$k;
						$v['app_pcnt'] = $v['app_pcnt']-$v['app_cancel_pcnt']; // 수량표기
						$app_pname = explode('|', $v['app_pname']);

						// 정산상태 출력
						$print_settlement_status = '';
						switch($v['o_paystatus3']) {
							case 'none': $print_settlement_status = '<span class="c_tag red h22 t4">정산무관</span>'; break;
							case 'ready': $print_settlement_status = '<span class="c_tag gray h22 t4">정산대기</span>'; break;
							case 'complete': $print_settlement_status = '<span class="c_tag blue h22 t4">정산완료</span>'; break;
						}
					?>
						<tr data-uid="<?php echo $v['o_ordernum']; ?>">
							<td><label class="design"><input type="checkbox" name="_uid[]" class="js_ck" value="<?php echo $v['o_ordernum']; ?>"></label></td>
							<td><?php echo number_format($_num); ?></td>
							<td>
								<?php echo date('Y.m.d', strtotime($v['o_rdate'])); ?>
								<div class="t_light"><?php echo date('H:i', strtotime($v['o_rdate'])); ?></div>
							</td>
							<td>
								<span class="block"><?php echo $v['o_ordernum']; ?></span>
								<?php echo showUserInfo($v['o_mid'], $v['o_oname']); ?>
							</td>
							<td><?php echo $v['o_rname']; ?></td>
							<td class="t_left">
								<!-- 상품정보 -->
								<div class="order_item">
									<!-- 상품명 -->
									<div class="title bold">
										<?php echo ($SubAdminMode === true && $AdminPath == 'totalAdmin' && $app_pname[2] ? "<span style='font-weight:normal; color:#999;'>(".$arr_customer2[$app_pname[2]] . ")</span>" : "") ; // JJC : 입점관리 : 2020-09-17?>
										<?php echo $app_pname[0]; ?>
									</div>
									<?php if($app_pname[1]) { ?>
										<div class="option bullet">
											<?php echo $app_pname[1]; ?>
											<?php if($v['app_pcnt'] > 1) { ?>
												<span class="t_light normal"><span class="t_black normal">외</span> <span class="t_black normal"><?php echo number_format(($v['app_pcnt']-1)); ?>개</span></span>
											<?php } ?>
										</div>
									<?php } ?>
								</div>
							</td>
							<td>
								<div class="lineup-vertical">
									<?php echo $print_settlement_status; ?>
								</div>
							</td>
							<td>

								<div class="lineup-full">
									<?php echo _InputSelect('_sendcompany['.$v['o_ordernum'].']', array_keys($arr_delivery_company), $v['o_sendcompany'], '', '', ''); ?>
									<input type="text" name="_sendnum[<?php echo $v['o_ordernum']; ?>]" class="design" placeholder="송장번호" value="<?php echo $v['o_sendnum']; ?>">
								</div>
							</td>
							<td>

								<div class="lineup-center">
									<?php
									$DClass = ''; // 배송상태 셀렉트박스 클래스
									if($v['o_sendstatus'] == '구매발주') $DClass = 'pay_ready';
									else if($v['o_sendstatus'] == '배송준비') $DClass = 'diliver_ready';
									else if($v['o_sendstatus'] == '배송중') $DClass = 'diliver_ing';
									else if($v['o_sendstatus'] == '배송완료') $DClass = 'diliver_ok';
									echo _InputSelect('_sendstatus['.$v['o_ordernum'].']', $arr_order_product_sendstatus, $v['o_sendstatus'], ($DClass?' class="'.$DClass.'"':null), '', '');
									?>
									<a href="#none" class="c_btn h28 black js_submit">적용</a>
								</div>
							</td>
							<td>
								<?php
								if($v['o_sendstatus'] != '구매발주' && $v['o_sendcompany']) {
									$DLink = '';
									$DLinkJS = null;
									if($v['o_sendcompany'] == '[자체배송]') {
										$DLink = '#none';
										$DLinkJS = "alert('자체배송은 배송조회가 불가능합니다.'); return false;";
									}
									else if($arr_delivery_company[$v['o_sendcompany']] && $v['o_sendnum']) {
										$DLink = $arr_delivery_company[$v['o_sendcompany']].rm_str($v['o_sendnum']);
										$DLinkJS = null;
									}
									else {
										$DLink = '#none';
										$DLinkJS = "alert('배송사 정보를 확인 할 수 없습니다..'); return false;";
									}
								?>
									<div class="lineup-vertical">
										<a href="<?php echo $DLink; ?>"<?php echo ($DLinkJS != null?' onclick="'.$DLinkJS.'"':null); ?><?php echo ($DLink != '#none'?' target="_blank"':null); ?> class="c_btn h22 green line t4">배송조회</a>
									</div>
								<?php } ?>
							</td>
							<td>
								<div class="lineup-vertical">
									<a href="#none" onclick="window.open('<?php echo OD_PROGRAM_URL; ?>/mypage.order.mass.print_view.php<?php echo URI_Rebuild('?', array('_mode'=>'print', 'ordernum'=>$v['o_ordernum'], '_PVSC'=>$_PVSC)); ?>' ,'print','width=860,height=820,scrollbars=yes'); return false;" class="c_btn h22">주문인쇄</a>
									<a href="_order.form.php<?php echo URI_Rebuild('?', array('view'=>$view, '_mode'=>'modify', '_ordernum'=>$v['o_ordernum'], 'view'=>'order_delivery', '_PVSC'=>$_PVSC)); ?>" class="c_btn h22">상세보기</a>
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
	</form>
</div>


<script type="text/javascript">
	// 주문개별처리
	$(document).on('click', '.js_submit', function(e) {
		e.preventDefault();
		var su = $(this).closest('tr');
		var _ordernum = su.data('uid');
		var _uid = su.data('uid');
		var _sendcompany = su.find('select[name^=_sendcompany] option:selected').val();
		var _sendnum = su.find('input[name^=_sendnum]').val();
		var _sendstatus = su.find('select[name^=_sendstatus] option:selected').val();
		var _url = '_order_delivery.pro.php';
		// JJC : 2021-01-15 : 배송준비 수정 가능하게 함
		if(_sendstatus == '배송중' || _sendstatus == '배송완료') {
			if(!_sendcompany) {
				alert('배송사를 선택하세요.');
				su.find('select[name^=_sendcompany]').focus();
				return false;
			}
			if(!_sendnum) {
				alert('송장번호를 입력하세요.');
				su.find('input[name^=_sendnum]').focus();
				return false;
			}
		}
		// JJC : 2021-01-15 : 배송준비 수정 가능하게 함
		if(!_sendstatus) {
			alert('배송상태를 선택하세요.');
			su.find('select[name^=_sendstatus]').focus();
			return false;
		}
		_url = _url+'?_mode=modify_sendstatus&_uid[]='+_ordernum+'&_sendcompany['+_ordernum+']='+_sendcompany+'&_sendnum['+_ordernum+']='+_sendnum+'&select_sendstatus='+_sendstatus;

		common_frame.location.href = _url;
	});

	// 일괄배송상태변경
	function selectSendstatus() {
		var sendstatus = $('select[name=select_sendstatus]').val();
		var trigger = true;

		if(!sendstatus) {
			alert('배송상태를 선택하세요.');
			$("select[name=select_sendstatus]").focus();
			return false;
		}

		if($('.js_ck:checked').length <= 0) {
			alert('처리할 주문을 1건 이상 선택 바랍니다.');
			return false;
		}

		$.each($('.js_ck:checked'), function(k, v) {
			if(!$(this).closest('tr').find('select[name^=_sendcompany] option:selected').val()) {
				trigger = false;
				return false;
			}
			if(!$(this).closest('tr').find('input[name^=_sendnum]').val()) {
				trigger = false;
				return false;
			}
		});
		if(trigger === false) {
			if(!confirm('입력되지 않은 택배사 또는 송장번호가 있습니다.\n\n제외 또는 무시하고 계속 하시겠습니까?')) return false;
		}

		if(!confirm('선택하신 '+$('.js_ck:checked').length+'건의 배송상태를 일괄 수정하시겠습니까?')) return false;
		$('.form_list').find('input[name=_mode]').val('modify_sendstatus');
		$('.form_list').submit();
	}


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

	// 상품 일괄업로드 폼 열기/닫기
	$(document).delegate('.js_open_excel_box', 'click', function(){
		$('.js_excel_box').toggle(); return false;
	});
</script>
<?php include_once('wrap.footer.php'); ?>