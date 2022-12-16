<?php
include_once('wrap.header.php');
$r = _MQ(" select s_paycancel_method from smart_setup where s_uid = 1 ");
?>
<form action="_config.paycancel.pro.php" method="post">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>할인 금액 환불 방식</th>
					<td colspan="3">
						<?php echo _InputRadio('s_paycancel_method', array('B', 'D'), ($r['s_paycancel_method']?$r['s_paycancel_method']:'B'), '', array('최종 환불 방식', '분배 환불 방식'), ''); ?>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<div class="tip_box">
							<?php 
								echo _DescStr('부분 취소 환불 방식은 상품 구매시 적용이 됩니다.', 'black'); 
								echo _DescStr('최종 환불 방식은 상품 주문에서 마지막 상품을 취소하여 전체 취소가 되었을 때 사용한 적립금과 쿠폰을 반환하고 할인 금액을 뺀 나머지 금액을 환불해 드리는 방식입니다.', 'black'); 
								echo _DescStr('예) 연필 500원 + 지우개 300원 + 볼펜 1,000원 - 할인 금액 400원 = 상품 구매액 1,400원'); 
								echo _DescStr('- 연필 500원 - 할인금액 0원 = 환불 금액 500원'); 
								echo _DescStr('- 지우개 300원 - 할인금액 0원 = 환불 금액 300원'); 
								echo _DescStr('- 볼펜 1,000원 - 할인 금액 400원 = 환불 금액 600원'); 
								echo _DescStr('- 총 상품 금액 1,800원 - 총 할인 금액 400원 = 총 환불 금액 1,400원'); 
								echo _DescStr('if) 마지막 상품 금액이 할인 금액 보다 작다면 할인 금액과 상품 금액의 차액만큼 전에 취소한 상품 금액에서 할인됩니다.'); 
								echo _DescStr('- 연필 500원 - 할인금액 0원 = 환불 금액 500원'); 
								echo _DescStr('- 볼펜 1,000원 - 할인 금액 100원 = 환불 금액  900원');  
								echo _DescStr('- 지우개 300원 - 할인 금액 300원 = 환불 금액 0원 ');  
								echo _DescStr('- 총 상품 금액 1,800원 - 총 할인 금액 400원 = 총 환불 금액 1,400원'); 
								echo '--------------------------------------------------------------------------------------------------------------------------------';
								echo _DescStr('분배 환불 방식은 상품을 취소할 때마다 할인된 금액이 상품 금액의 비율로 분배되어 환불 해드리는 방식입니다.', 'black'); 
								echo _DescStr("예) 노트 3,000원 + 샤프 2,000원 + 볼펜 1,000원 - 할인 금액 1,000원 = 상품 구매액 5,000원");
								echo _DescStr("- 상품 금액 X 할인 금액 / 총 상품 금액 = 상품별 할인 금액");
								echo _DescStr("- 노트 3,000원 - 상품별 할인 금액 500원 = 환불 금액 2,500원");
								echo _DescStr("- 샤프 2,000원 - 상품별 할인 금액 330원 = 환불 금액 1,670원");
								echo _DescStr("- 볼펜 1,000원 - 상품별 할인 금액 170원 = 환불 금액 830원");
								echo _DescStr('- 총 상품 금액 6,000원 - 총 할인 금액 1,000원 = 총 환불 금액 5,000원'); 
							?>
						</div>	
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