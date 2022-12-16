<?php
include_once('wrap.header.php');
/*
	s_coupon_use : 쿠폰사용여부 (Y사용,N)
	s_coupon_view : 쿠폰노출 설정 (all:전체,member:회원) -- 미사용
	s_coupon_ordercancel_return :  주문취소에 따른 복원 사용여부 (Y:사용,N:미사용)
*/
$r = _MQ(" select * from smart_setup where s_uid = 1 ");
?>

<form action="_coupon_config.pro.php" method="post" name="frm">
	<div class="group_title"><strong>쿠폰 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>  
					<th class="ess">쿠폰사용여부</th>
					<td>
						<?php echo _InputRadio('s_coupon_use', array('Y','N'), ($r['s_coupon_use']?$r['s_coupon_use']:'N'), '', array('사용','미사용')); ?>
						<div class="tip_box">
							<?php echo _DescStr("해당설정은 등록된 모든 쿠폰에 대한 사용여부를 설정할 수 있습니다."); ?>
							<?php echo _DescStr('<em>미사용</em>으로 설정 시 등록된 쿠폰의 <em>사용</em> 여부와 상관없이 쿠폰을 사용할 수 없습니다. '); ?>
						</div>
					</td>
				</tr>				
				<tr>
					<th class="ess">주문취소에 따른 쿠폰복원 사용여부</th>
					<td>
						<?php echo _InputRadio('s_coupon_ordercancel_return', array('Y', 'N'), ($r['s_coupon_ordercancel_return']?$r['s_coupon_ordercancel_return']:'N'), '', array('사용', '미사용'), ''); ?>
						<div class="tip_box">
							<?php echo _DescStr('<em>사용</em>으로 설정할 경우 주문취소를 한 주문건에 대해 사용된 쿠폰을 복원합니다. '); ?>
							<?php echo _DescStr('<em>미사용</em>으로 설정할 경우 주문취소를 하더라도 사용된 쿠폰은 복원되지 않습니다. '); ?>
							<?php echo _DescStr('결제이전에 주문취소를 할경우 설정과 상관없이 쿠폰은 복원됩니다.'); ?>
						</div>
					</td>
				</tr>

			</tbody>
		</table>
	</div>


		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h46 red"><input type="submit" name="" value="확인"></span></li>
			</ul>
		</div>

</form>

<?php include_once('wrap.footer.php'); ?>