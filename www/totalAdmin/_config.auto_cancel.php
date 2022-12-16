<?php
// ----- [하이센스3.0 결제취소파일 일원화 패치] : 자동주문취소 -----
include_once('wrap.header.php');
$r = _MQ(" select * from smart_setup where s_uid = 1 ");
?>
<form action="_config.auto_cancel.pro.php" method="post">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="250><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>결제대기 주문 자동취소 설정</th>
					<td colspan="3">
						<input type="text" name="_order_auto_cancel_term" class="design" style="width:50px;" value="<?=$r['s_order_auto_cancel_term']?>" /><span class="fr_tx">일</span>
						<br /><br /><?php echo _DescStr('설정값이 0일 경우 자동취소가 작동하지 않습니다.'); ?>
						<br /><?php echo _DescStr('결제대기 상태로 설정한 일수는 넘은 주문은 자동취소됩니다.'); ?>
						<br /><?php echo _DescStr('결제대기는 결제수단이 무통장입금 및 가상계좌인 주문뿐 아니라 신용카드, 계좌이체, 핸드폰 결제 진행대기중인 주문도 포함됩니다.'); ?>
						<br /><?php echo _DescStr('자동취소는 1일1회 작동되며, 주문일로부터 지정한 일수를 초단위로 판단하여 초과될 경우 취소합니다.'); ?>
						<br /><?php echo _DescStr('예) 자동취소 1일 설정. 2020-06-10 20:08:11 주문된 건의 경우 - 2020-06-11 20:08:11에 만 하루가 되므로 2020-06-12까지 결제대기일 경우 취소됩니다.'); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<!-- 저장 -->
	<div class="c_btnbox">
		<ul>
			<li><span class="c_btn h46 red"><input type="submit" value="확인" /></span></li>
		</ul>
	</div>
	<!-- 저장 -->
</form>
<?php include_once('wrap.footer.php'); ?>