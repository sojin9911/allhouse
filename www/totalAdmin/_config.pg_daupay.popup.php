<?php
$app_mode = 'popup';
include_once('inc.header.php');

// 기본처리
$popup_title = $_mode == 'config' ? '부가설정 매뉴얼':'가상계좌 입금내역 통보 URL 신청 안내메일';

// -- 안내메일 양식
$arrMailContent[] = "1. DB처리페이지 URL : http://".$system['host'].OD_PROGRAM_DIR."/shop.order.result_daupay.pro.php";
$arrMailContent[] = "2. 가상계좌발행 DB처리페이지 URL : http://".$system['host'].OD_PROGRAM_DIR."/shop.order.result_daupay.pro.php";
$arrMailContent[] = "3. 상점 IP : ".$_SERVER['SERVER_ADDR'];
$arrMailContent[] = "4. 암호화 키 : ";

?>

<div class="popup">
	<div class="pop_title"><strong><?php echo $popup_title; ?></strong></div>
	<?php if($_mode == 'mail'){ // 빌게이트 전용  ?>
	<div class="data_list">
		<table class="table_form">
			<colgroup>
				<col width="140">
				<col width="*-">
			</colgroup>
			<tbody>
				<tr>
					<td colspan="2">
						<?php echo _DescStr("정상 결제서비스를 위해 아래의 정보를 support@daoupay.com 으로 보내야합니다.","black"); ?>
					</td>
				</tr>
				<tr>
					<th>수신자</th>
					<td>
						<div class="js_html_viewer" contenteditable="true" oncut="return false;" onpaste="return false;">support@daoupay.com</div>
					</td>
				</tr>
				<tr>
					<th>메일제목</th>
					<td>
						<div class="js_html_viewer" contenteditable="true" oncut="return false;" onpaste="return false;"><?php echo "결제 서비스를 위해 설정정보를 보내드립니다."; ?></div>
					</td>
				</tr>
				<tr>
					<th>발송내용</th>
					<td class="edit_td">
						<div class="js_html_viewer" contenteditable="true" style="min-height:150px"><?php echo implode("<br/>",$arrMailContent); ?></div>
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