<?php include_once('wrap.header.php'); ?>
<form action="_config.orderbank.pro.php" method="post">
<input type="hidden" name="_mode" value="modify">

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">	
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>사용여부</th>
					<td>
						<?php echo _InputRadio( '_bank_autocheck_use' , array('Y','N') , $siteInfo['s_bank_autocheck_use'] , '' , array('사용','미사용') , ''); ?>
					</td>
				</tr>
				<tr>
					<th>APIBOX 아이디</th>
					<td>
						<input type="text" name="_apibox_id" class="design" style="width:250px;" value="<?php echo $siteInfo["s_apibox_id"]; ?>"/>
						<div class="tip_box">
							<?php echo _DescStr("무통장 계좌번호는 무통장 입금 계좌 에 등록하신 계좌번호와 반드시 일치하여야 합니다.",'orange'); ?>
							<?php echo _DescStr("<b onclick=\"window.open('http://apibox.kr')\" style='cursor:pointer'>http://apibox.kr</b> 를 통해 회원가입한 아이디를 입력하세요."); ?>
							<?php echo _DescStr("가입 후 <em>[마이페이지 > 무통장입금자동통보 > 계좌번호관리]</em>에서 계좌번호를 등록하여 사용하세요."); ?>
							<?php echo _DescStr("연동절차 : <em><A HREF='https://www.apibox.kr/bank/manual.pdf' target='_blank'>https://www.apibox.kr/bank/manual.pdf</A></em>"); ?>
							<?php echo _DescStr("콜백주소 : <em>".$app_HTTP_URL."/addons/apibox/_api.bank.auto.check.php</em>"); ?>
							<?php echo _DescStr("설치상태는 <A HREF='".$app_HTTP_URL."/addons/apibox/setup/setup.php' target='_blank'><em>".$app_HTTP_URL."/addons/apibox/setup/setup.php</em></A> 에서 확인 가능합니다."); ?>
						</div>
					</td>
				</tr>
			</tbody> 
		</table>
	</div>
 
	<?php echo _submitBTNsub(); ?>

</form>

<?php include_once('wrap.footer.php'); ?>