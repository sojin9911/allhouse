<?php
include_once('wrap.header.php');
$siteInfo = _MQ(" select * from smart_setup where s_uid = 1 ");

/*
	-- 휴대폰 결제
	s_pg_mobile_use	= 휴대폰 결제 사용여부
	s_pg_mobile_type	= 휴대폰 결제 모듈 (pg,other)  기본-pg, 별도의 외부 모듈 - other
*/

?>
<form name="frmPayco" id = "frmPayco" action="_config.pg_mobile.pro.php" method="post">
	<input type="hidden" name="s_pg_mobile_type" value="<?php echo $siteInfo['s_pg_mobile_type'] ?>">
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>

					
				<tr>
					<th>휴대폰 결제 사용여부</th>
					<td>
						<?php echo _InputRadio('s_pg_mobile_use', array('Y', 'N'), ($siteInfo['s_pg_mobile_use']?$siteInfo['s_pg_mobile_use']:'N'), '', array('사용', '미사용'), ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr('이용중인 통합  전자결제(PG) 서비스 에 따라 휴대폰 결제 서비스가 적용 됩니다.'); ?>
						<a href="_config.pg.form.php" class="c_btn h22 if_with_tip" target="_blank">통합 전자결제(PG) 관리 바로가기</a>				
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="tip_box">
							<?php 
								echo _DescStr('휴대폰 결제 서비스의 경우 <em>통합관리자 > 환경설정 > 결제 관련 설정 > 결제 수단 설정</em>과는 별개로 사용여부 설정을 할 수 있습니다.','black'); 
								echo _DescStr('휴대폰 결제 서비스 사용여부를 설정하시기전  <em>통합관리자 > 환경설정 > 결제 관련 설정 > 통합 전자결제(PG) 관리</em>에서 이용중인 통합  전자결제(PG) 서비스를 확인 후 해당 PG사에서 휴대폰 결제 서비스를 신청해 주셔야 합니다.','black'); 
								echo _DescStr("이용중인 통합  전자결제(PG) 서비스가 아닌 별도의 휴대폰 결제 서비스를 신청하실경우 사용이 불가능 합니다.","black");
								echo _DescStr("휴대폰 결제 취소의 경우 결제 월 말 일까지만 취소처리 가능하며 결제 월 이후에는 주문취소 후 고객에게 직접 환불 해야 합니다.","black");
								echo _DescStr("복합과세의 경우 반드시 PG와 먼저 복합과세 계약을 신청하셔야합니다.","black");
							?>
						</div>	
					</td>
				</tr>																		
			</tbody>
		</table>
	</div>


	<?php  echo _submitBTNsub(); ?>
</form>

<?php include_once('wrap.footer.php'); ?>