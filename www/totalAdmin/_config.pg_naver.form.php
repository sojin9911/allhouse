<?php
include_once('wrap.header.php');
$r = _MQ(" select * from smart_setup where s_uid = 1 ");
?>
<form action="_config.pg_naver.pro.php" method="post">
	<div class="group_title"><strong>네이버페이 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>네이버페이 사용여부</th>
					<td>
						<?php echo _InputRadio('npay_use', array('Y', 'N'), ($r['npay_use']?$r['npay_use']:'N'), '', array('사용', '미사용'), ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr('네이버페이를 신청 후 서비스를 이용해주시기 바랍니다.'); ?>
						<a href="https://admin.pay.naver.com" class="c_btn h22 if_with_tip" target="_blank">네이버센터 바로가기</a>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="tip_box">
							<?php echo _DescStr('심사 과정 : 1.계약 → 2.네이버 가입심사 → 3.버튼시스템 연동&심사 → 4.버튼 연동승인 → 5.주문시스템&심사 → 6.주문시스템연동승인 → 7.서비스', 'black'); ?>
							<?php echo _DescStr('네이버페이는 실 도메인을 연결하여 적용한 후 이용 가능합니다.', 'black'); ?>
							<?php echo _DescStr('주문연동 시 반품 및 교환은 연동되지 않습니다.', 'black'); ?>
							<?php echo _DescStr('네이버페이 주문은 정산에 포함되지 않습니다.', 'black'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="group_title"><strong>네이버페이 버튼연동 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>활성화 모드</th>
					<td>
						<?php echo _InputRadio('npay_mode', array('real', 'test'), ($r['npay_mode']?$r['npay_mode']:'test'), '', array('실적용 모드', '테스트 모드'), ''); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<?php echo _DescStr('연동 확인 심사 승인 전 까지 테스트 모드로 사용하며 승인 이후 실적용 모드로 변경하시기 바랍니다.'); ?>
					</td>
				</tr>
				<tr>
					<th>네이버페이 ID</th>
					<td>
						<input type="text" name="npay_id" class="design" value="<?php echo $r['npay_id']; ?>">
						<?php echo _DescStr('네이버페이(NPay, 네이버체크아웃) 가입(계약)시 사용한 아이디를 입력하세요.'); ?>
					</td>
				</tr>
				<tr>
					<th>네이버 공통 인증키</th>
					<td>
						<input type="text" name="npay_all_key" class="design" value="<?php echo $r['npay_all_key']; ?>">
					</td>
				</tr>
				<tr>
					<th>가맹점 인증키</th>
					<td>
						<input type="text" name="npay_key" class="design" value="<?php echo $r['npay_key']; ?>" style="width:280px">
					</td>
				</tr>
				<tr>
					<th>버튼 인증키</th>
					<td>
						<input type="text" name="npay_bt_key" class="design" value="<?php echo $r['npay_bt_key']; ?>" style="width:280px">
					</td>
				</tr>
				<tr>
					<td colspan="2">						
						<?php echo _DescStr('네이버페이 테스트 연동 요청을 먼저 하신 후 진행하여 주시기 바랍니다.'); ?>
						<div class="clear_both"></div>
						<?php echo _DescStr('네이버페이 계약 시 메일에 표시된 담당자에게 "<u class="js_npay_popup" data-mode="btn_notice">버튼 연동심사 발송내용</u>" 을 복사하여 보내주십시오.'); ?>
						<a href="#none" class="c_btn h22 js_npay_popup if_with_tip" data-mode="btn_notice">버튼 연동심사 발송내용 보기</a>
						<div class="clear_both"></div>
						<?php echo _DescStr('네이버페이 연동 관련 오류 문의'); ?>
						<a href="https://www.onedaynet.co.kr:446/p/service.inqury_list.html" class="c_btn h22 if_with_tip" target="_blank">원데이넷 고객센터</a>					
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="group_title"><strong>네이버페이 주문연동 설정</strong></div>
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>주문연동 모드</th>
					<td>
						<?php echo _InputRadio('npay_sync_mode', array('real', 'test'), ($r['npay_sync_mode']?$r['npay_sync_mode']:'test'), '', array('실적용 모드', '테스트 모드'), ''); ?>
					</td>
				</tr>
				<tr>
					<th>Access License</th>
					<td>
						<input type="text" name="npay_lisense" class="design" value="<?php echo $r['npay_lisense']; ?>" style="width:510px">
					</td>
				</tr>
				<tr>
					<th>Secret Key</th>
					<td>
						<input type="text" name="npay_secret" class="design" value="<?php echo $r['npay_secret']; ?>" style="width:510px">
					</td>
				</tr>
				<tr>
					<td colspan="2">						
						<?php echo _DescStr('"<u class="js_npay_popup" data-mode="sync_notice">주문 연동심사 발송내용</u>" 을 복사하여 보내주십시오.'); ?>
						<a href="#none" class="c_btn h22 js_npay_popup if_with_tip" data-mode="sync_notice">주문 연동심사 발송내용 보기</a>
						<div class="clear_both"></div>
						<?php echo _DescStr('모든 연동을 완료 후 "<u class="js_npay_popup" data-mode="last_notice">최종 연동완료 발송내용</u>" 을 복사하여 보내주십시오.'); ?>
						<a href="#none" class="c_btn h22 js_npay_popup if_with_tip" data-mode="last_notice">최종 연동완료 발송내용 보기</a>				
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<?php echo _submitBTNsub(); ?>
</form>

<script type="text/javascript">
	$(document).on('click', '.js_npay_popup', function(e) {
		e.preventDefault();
		var _mode = $(this).data('mode');
		window.open('_config.pg_naver.popup.php?_mode='+_mode, 'npay_notice', 'width=1120,height=800,top=100,scrollbars=yes');
	});
</script>
<?php include_once('wrap.footer.php'); ?>