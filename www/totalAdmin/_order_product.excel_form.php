<?php
$app_current_link = '_order_product.list.php';
include_once('wrap.header.php');
include_once(OD_ADDONS_ROOT.'/excelAddon/loader.php');
$Excel = ExcelLoader($_FILES['excel_file']['tmp_name']);
$ExcelCnt = count($Excel)-2;


// 엑셀 간략 검증
$OPCheck = _MQ(" select op_uid from smart_order_product where op_uid = '{$Excel[2][0]}' ");
if(!$OPCheck['op_uid']) error_msg('엑셀 파일이 잘못되었습니다.\\n\\n배송주문상품관리에서 받은 엑셀 파일이 맞는지 확인바랍니다.');
?>
<form action="_order_product.pro.php" method="post" onsubmit="return wFun();">
	<input type="hidden" name="_mode" value="ins_excel">
	<div class="group_title">
		<strong>배송주문상품관리 일괄업로드</strong>
		<!-- 해당페이지의 등록/업로드 버튼 있을 경우 -->
		<div class="btn_box js_submit_box" style="display: none;">
			<span class="c_btn h46 red"><input type="submit" value="등록처리 (<?php echo number_format($ExcelCnt); ?>)" /></span>
			<a href="<?php echo $app_current_link; ?>" class="c_btn h46 black line">돌아가기</a>
		</div>
	</div>

	<div class="data_form">
		<table class="table_form">
			<tbody>
				<tr>
					<td>
						<div class="tip_box">
							<?php echo _DescStr('처리 수에 따라 다소시간이 걸릴 수 있습니다.'); ?>
							<?php echo _DescStr('해당 페이지에서 <em>등록처리</em>버튼을 눌러 저장 하지 않으면 등록되지 않습니다.'); ?>
							<?php echo _DescStr('해당 페이지에서 <em>새로고침</em>을 할 경우 문제가 생길 수 있습니다.'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<div class="data_list_excel js_data_table">
		<table class="table_list">
			<colgroup>
				<col width="70">
				<col width="70">
				<col width="100">
				<col width="165">
				<col width="165">
				<col width="100">
				<col width="135">
				<col width="90">
				<col width="80">
				<col width="145">
				<col width="145">
				<col width="80">
				<col width="145">
				<col width="145">
				<col width="120">
				<col width="*">
				<col width="*">
				<col width="155">
				<col width="*">
				<col width="*">
				<col width="*">
				<col width="*">
				<col width="100">
				<col width="70">
				<col width="100">
				<col width="200">
				<col width="200">
			</colgroup>
			<thead>
				<tr>
					<th scope="col">NO</th>
					<th scope="col">처리</th>
					<th scope="col">배송상태</th>
					<th scope="col">택배사</th>
					<th scope="col">송장번호</th>
					<th scope="col">고유번호</th>
					<th scope="col">주문번호</th>
					<th scope="col">주문일자</th>
					<th scope="col">주문자</th>
					<th scope="col">주문자전화</th>
					<th scope="col">주문자휴대폰</th>
					<th scope="col">수령인</th>
					<th scope="col">수령인전화</th>
					<th scope="col">수령인휴대폰</th>
					<th scope="col">배송지우편번호</th>
					<th scope="col">배송지주소-지번</th>
					<th scope="col">배송지주소-도로명</th>
					<th scope="col">상품코드</th>
					<th scope="col">대표상품명</th>
					<th scope="col">옵션1</th>
					<th scope="col">옵션2</th>
					<th scope="col">옵션3</th>
					<th scope="col">판매단가</th>
					<th scope="col">수량</th>
					<th scope="col">금액</th>
					<th scope="col">배송시문구</th>
					<th scope="col">관리자메모</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$num = 0;
				foreach($Excel as $k=>$v) {
					if($k < 2) continue; // 파일정보와 헤더는 제외
					$num++;

					$_opuid = $v[0]; // 주문번호
				?>
					<tr>
						<td><?php echo number_format($num); ?></td>
						<td>
							<?php echo _InputSelect('w_check['.$_opuid.']', array('Y', 'N'), 'Y', '', array('등록', '제외'), ''); ?>
						</td>
						<td>
							<?php
							$DClass = ''; // 배송상태 셀렉트박스 클래스
							if($v[20] == '배송완료') $DClass = 'diliver_ok';
							else if($v[20] == '배송중') $DClass = 'diliver_ing';
							else if($v[20] == '배송준비') $DClass = 'diliver_ready';
							else if($v[20] == '구매발주') $DClass = 'pay_ready';
							else $DClass = 'diliver_ready';
							echo _InputSelect('_sendstatus['.$_opuid.']', $arr_order_product_sendstatus, ($v[20]?$v[20]:'배송중'), ' class="js_sendstatus '.($DClass?$DClass:null).'"', '', '');
							?>
						</td>
						<td>
							<?php echo _InputSelect('_sendcompany['.$_opuid.']', array_keys($arr_delivery_company), $v[21], '', '', ''); ?>
						</td>
						<td>
							<input type="text" name="_sendnum[<?php echo $_opuid; ?>]" class="design" placeholder="송장번호" value="<?php echo $v[22]; ?>">
						</td>
						<td><?php echo $_opuid; ?></td>
						<td><?php echo $v[1]; ?></td>
						<td><?php echo $v[2]; ?></td>
						<td><?php echo $v[3]; ?></td>
						<td><?php echo $v[4]; ?></td>
						<td><?php echo $v[5]; ?></td>
						<td><?php echo $v[6]; ?></td>
						<td><?php echo $v[7]; ?></td>
						<td><?php echo $v[8]; ?></td>
						<td><?php echo $v[9]; ?></td>
						<td class="t_left"><?php echo $v[10]; ?></td>
						<td class="t_left"><?php echo $v[11]; ?></td>
						<td><?php echo $v[12]; ?></td>
						<td class="t_left"><?php echo $v[13]; ?></td>
						<td class="t_left"><?php echo $v[14]; ?></td>
						<td class="t_left"><?php echo $v[15]; ?></td>
						<td class="t_left"><?php echo $v[16]; ?></td>
						<td><?php echo number_format((int)$v[17]); ?></td>
						<td><?php echo number_format((int)$v[18]); ?></td>
						<td><?php echo number_format((int)$v[19]); ?></td>
						<td><?php echo htmlspecialchars($v[23]); ?></td>
						<td><?php echo htmlspecialchars($v[24]); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</form>


<script type="text/javascript">
	//페이지가 준비 되면 노출 :: 페이지 준비전 submit가 발생시 데이터 잘림 현상 발생
	$(document).ready(function() {
		$('.js_submit_box').show();
	});

	function wFun() {
		if(!confirm("입력하시겠습니까?")) return false;
		return true;
	}


	$('.js_sendstatus').on('change', function(e) {
		var _status = $(this).val();
		var _class = new Array();
		_class['배송완료'] = 'diliver_ok';
		_class['배송중'] = 'diliver_ing';
		_class['배송준비'] = 'diliver_ready';
		_class['구매발주'] = 'pay_ready';

		$(this).removeClass('diliver_ok');
		$(this).removeClass('diliver_ing');
		$(this).removeClass('diliver_ready');
		$(this).removeClass('pay_ready');
		if(_class[_status]) $(this).addClass(_class[_status]);
		else $(this).addClass(_class['구매발주']);
	});

	// 휠 스크롤을 가로에서 세로로 변경
	$('.js_data_table').bind('mousewheel', function(e) {
		e.preventDefault();
		var wheelDelta = e.originalEvent.wheelDelta;
		if(wheelDelta > 0) $(this).scrollLeft(-wheelDelta + $(this).scrollLeft());
		else $(this).scrollLeft(-wheelDelta + $(this).scrollLeft());
	});
</script>
<?php include_once('wrap.footer.php'); ?>