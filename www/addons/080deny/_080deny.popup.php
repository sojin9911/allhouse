<?php
$app_mode = 'popup';
include_once('inc.header.php');

// 기본처리
$popup_title = $_mode == 'config' ? '부가설정 매뉴얼':'가상계좌 입금내역 통보 URL 신청 안내메일';

?>
<div class="popup">
	<div class="pop_title"><strong><?php echo $popup_title; ?></strong></div>

	<?php if( $_mode == 'config'){ ?> 
	<div class="data_list">
		<table class="table_form">
			<colgroup>
				<col width="140">
				<col width="*-">
			</colgroup>
			<tbody>
				<tr>
					<th>결제 로그기록 위치설정</th>
					<td class="edit_td">
						<?php echo _DescStr("활성화 모드 설정에 따라 아래와 같이 FTP 접속 후 해당 파일 내 내용을 수정해 주셔야 합니다."); ?>
						<div class="js_html_viewer" contenteditable="false" style="min-height:100px">
							<strong>/pg/pc/billgate/config/config.ini 77째줄</strong>
							<div class="dash_line"></div>
							[변경전]<br/>
							log_file = <br/>
							[변경후]<br/>
							log_file = <?=dirname($_SERVER[DOCUMENT_ROOT])?>/pg/pc/billgate/log
						</div>
					</td>
				</tr>				
				<tr>
					<th>암호화 키 설정</th>
					<td class="edit_td">
						<?php echo _DescStr("활성화 모드 설정에 따라 아래와 같이 FTP 접속 후 해당 파일 내 내용을 수정해 주셔야 합니다."); ?>
						<div class="js_html_viewer" contenteditable="false" style="min-height:100px">
							<strong>/pg/pc/billgate/config/config.ini 84~85째줄</strong>
							<div class="dash_line"></div>
							[변경전]<br/>
							key = QkZJRlBDRTI4T0c1OUtBMw==<br/>
							iv = PRJ59Q2GHPT844TQ<br/>
							[변경후]<br/>
							key = <span style="font-style: italic; color: #999;">빌게이트에서 발급받은 암호화 키</span><br/>
							iv = <span style="font-style: italic; color: #999;">빌게이트에서 발급받은 암호화 Initialize Vector</span>
						</div>
					</td>
				</tr>				
				<tr>
					<th>활성화 모드에 따른 설정</th>
					<td class="edit_td">
						<?php echo _DescStr("활성화 모드 설정에 따라 아래와 같이 FTP 접속 후 해당 파일 내 내용을 수정해 주셔야 합니다."); ?>
						<div class="js_html_viewer" contenteditable="false" style="min-height:100px">
						<strong>/pg/pc/billgate/config/config.ini 68째줄</strong>
						<div class="dash_line"></div>
						[테스트 모드]<br/>
						mode = 0<br/>
						[실결제 모드]<br/>
						mode = 1
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php }else if($_mode == 'mail'){ // 빌게이트 전용  ?>
	<div class="data_list">
		<table class="table_form">
			<colgroup>
				<col width="140">
				<col width="*-">
			</colgroup>
			<tbody>
				<tr>
					<td colspan="2">
						<?php echo _DescStr("가상계좌 결제 사용을 위해서는 반드시 아래 정보를 <em>tech@billgate.net</em>으로 보내야 합니다.","black"); ?>
					</td>
				</tr>
				<tr>
					<th>수신자</th>
					<td>
						<div class="js_html_viewer" contenteditable="true" oncut="return false;" onpaste="return false;">tech@billgate.net</div>
					</td>
				</tr>
				<tr>
					<th>메일제목</th>
					<td>
						<div class="js_html_viewer" contenteditable="true" oncut="return false;" onpaste="return false;"><?php echo "가상계좌 DB처리페이지 추가 요청"; ?></div>
					</td>
				</tr>
				<tr>
					<th>발송내용</th>
					<td class="edit_td">
						<div class="js_html_viewer" contenteditable="true" style="min-height:150px">
							* 가맹점 아이디 (빌게이트에서 발급된 아이디) : <?=$siteInfo['s_pg_code']?><br/>
							* DB처리할 페이지의 경로 (http:// 로 시작하는 url) : http://<?=$system['host'].OD_PROGRAM_DIR?>/shop.order.result_billgate_vacctinput.php<br/>
							* DB처리할 웹 서버의 IP 주소 : <?=$_SERVER[SERVER_ADDR]?><br/>
							* DB처리할 웹 서버의 포트번호 (default : 80) : 80
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<?php } ?>

	<div class="c_btnbox">
		<ul>
			<!-- <li><a href="" class="c_btn h34 black">확인</a></li> -->
			<li><a href="#none" onclick="window.close(); return false;" class="c_btn h34 black line normal">닫기</a></li>
		</ul>
	</div>
</div>




<script type="text/javascript">
	$(document).ready(function(){
		$('.js_html_viewer').on('keypress cut paste click', function(e) {
			e.preventDefault();
			if(e.type == 'click') document.execCommand('selectAll',false,null);
		});
	});
</script>
<style type="text/css">
	.js_html_viewer {
		float: left;
		background: #fff;
		box-sizing: border-box;
		border: 1px solid #d9dee3;
		padding: 0 5px;
		margin-right: 5px;
		overflow: hidden;
		padding: 4px 10px 5px 9px;
		width:100%;
	}
	strong { font-weight:600; }
	span { display: inline; }
</style>
<?php
include_once('inc.footer.php');
?>