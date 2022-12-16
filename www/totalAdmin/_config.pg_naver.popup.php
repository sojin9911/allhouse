<?php
$app_mode = 'popup';
include_once('inc.header.php');

// 기본처리
$popup_title = $popup_subject = $popup_content = '안내사항';
if($siteInfo['npay_use'] != 'Y') error_msgPopup_s('"네이버페이 사용여부"가 "미사용" 상태입니다.');

// 모드별 처리
if($_mode == 'btn_notice') { // 버튼 연동심사 발송내용
	if(!$siteInfo['npay_id'] || !$siteInfo['npay_all_key'] || !$siteInfo['npay_key'] || !$siteInfo['npay_bt_key']) error_msgPopup_s('네이버 버튼연동 진행이 불가능한 상태입니다.');
	if($siteInfo['npay_mode'] != 'test') error_msgPopup_s('버튼연동 "활성화 모드"가 "실적용 모드" 상태입니다.');
	$popup_title = '버튼연동심사 발송내용';
	$popup_subject = '['.$siteInfo['s_adshop'].'] 네이버페이 버튼 연동심사를 요청드립니다.';
	$popup_content = '
		안녕하세요. <br>
		'.$siteInfo['s_adshop'].' (<a href="http://'.$system['host'].'" target="_blank">http://'.$system['host'].'</a>) 입니다. <br>
		네이버페이 연동심사를 다음과 같이 요청 드립니다. <br><br>

		<strong>1. 상품정보제공 XML</strong><br>
		- http://'.$system['host'].'/addons/npay/<br>
		- 예시: http://'.$system['host'].'/addons/npay/?ITEM_ID=I9663-I3989-M1758&amp;ITEM_ID=W0580-P7297-W9903&amp;ITEM_ID=L0069-T3087-H8994<br>
		- 특이1: ITEM_ID가 누락된 경우 error을 반환<br>
		- 특이2: ITEM_ID가 정상적이지 못한경우 제외 하고 나머지 항목을 출력<br><br>

		<strong>2. 상품페이지에서 요청 (<small style="color:#0072CA">&amp;nt=test 파라미터 추가</small>)</strong><br>
		- 예시1: http://'.$system['host'].'/?pn=product.view&amp;pcode=I9663-I3989-M1758&amp;nt=test<br>
		- 예시2: http://'.$system['host'].'/?pn=product.view&amp;pcode=W0580-P7297-W9903&amp;nt=test<br>
		- 예시3: http://'.$system['host'].'/?pn=product.view&amp;pcode=L0069-T3087-H8994&amp;nt=test<br>
		<br>
		
		<strong>3. 장바구니에서 요청 (<small style="color:#0072CA">&amp;nt=test 파라미터 추가</small>)</strong><br>
		- http://'.$system['host'].'/?pn=shop.cart.list&amp;nt=test <br>
		<br>
		
		<strong>더불어, 주문연동(<u>네이버페이 API 4.1연동</u>)을 위하여 연동 SANDBOX키를 요청 드립니다.</strong><br>
		- 네이버페이 가맹점ID: <strong>'.($siteInfo['npay_id']?$siteInfo['npay_id']:'네이버페이 ID를 저장 후 이용가능').'</strong><br>
		- 이메일: <strong>'.$siteInfo['s_ademail'].'</strong><br>
		- 휴대폰: <strong>'.$siteInfo['s_glbmanagerhp'].'</strong><br>
		- 담당자: <strong>'.$siteInfo['s_ceo_name'].'</strong><br>
		- 서버IP: <strong>'.$_SERVER['SERVER_ADDR'].'</strong><br>
		- 개발언어: <strong>PHP '.phpversion().'</strong><br>
		<br>

		<strong>* 위 내용으로 네이버페이 연동 검수요청을 드립니다.</strong><br>
		<strong style="color:#ff0000">* 네이버페이 버튼 노출은 주문연동 후 처리 됩니다.</strong>
	';
}
else if($_mode == 'sync_notice') { // 주문 연동심사 발송내용
	if(!$siteInfo['npay_id'] || !$siteInfo['npay_all_key'] || !$siteInfo['npay_key'] || !$siteInfo['npay_bt_key'] || !$siteInfo['npay_lisense'] || !$siteInfo['npay_secret']) error_msgPopup_s('네이버 주문연동 진행이 불가능한 상태입니다.');
	if($siteInfo['npay_sync_mode'] != 'test') error_msgPopup_s('주문연동 "주문연동 모드"가 "실적용 모드" 상태입니다.');
	$popup_title = '주문 연동심사 발송내용';
	$popup_subject = '['.$siteInfo['s_adshop'].'] 네이버페이 주문 연동심사를 요청드립니다.';
	$popup_content = '
		안녕하세요. <br>
		'.$siteInfo['s_adshop'].' (<a href="http://'.$system['host'].'" target="_blank">http://'.$system['host'].'</a>) 입니다. <br>
		네이버페이 주문연동(<strong>네이버페이 API 4.1연동</strong>)심사를 다음과 같이 요청 드립니다. <br><br>

		- <strong>네이버페이 가맹점ID:</strong> '.($siteInfo['npay_id']?$siteInfo['npay_id']:'네이버페이 ID를 저장 후 이용가능').'<br>
		- <strong>CallbackURL:</strong> <span style="color:#0072CA">http://'.$system['host'].'/addons/npay/callback.php</span><br>
		- <strong>체크아웃 리뉴얼 API 적용 현황(C1)<span style="color:#0072CA"> - 적용항목만 노출</span></strong><br />
		<table width="1024px" border="1" style="border: 1px solid #ccc">
			<thead>
				<tr>
					<th rowspan="2">항목</th>
					<th rowspan="2">API명</th>
					<th colspan="2" style="background-color:#F2F2F2; width:120px">Access Type</th>
					<th rowspan="2" style="background-color:#F2F2F2; width:200px">적용 위치 또는 메뉴</th>
					<th rowspan="2" style="background-color:#F2F2F2; width:200px">동작 방식</th>
					<th rowspan="2" style="background-color:#F2F2F2">기타 및 비고</th>
				</tr>
				<tr>
					<th style="background-color:#F2F2F2">read</th>
					<th style="background-color:#F2F2F2">write</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="center">
						상품주문 조회 ><br>
						상품주문 조회 ><br>
						상품주문 내역 상세 조회
					</td>
					<td style="color:#FF7F27; font-weight:bold; text-align:center">GetProductOrderInfoList</td>
					<td align="center">O</td>
					<td align="center">-</td>
					<td align="center">콜백 위치에서 상태 변경 시 사용</td>
					<td style="padding:10px">
						출력없음 <br>
						- 콜백 주소로 타입 전달시 변경 상품주문 조회를 거쳐 주문번호를 확인 후 해당 주문번호의 주문상세내역을 가져오며 가장 처음 데이터를 조회하여 타입별 시스템 정산을 수행한다.
					</td>
					<td></td>
				</tr>
				<tr>
					<td align="center">
						상품주문조회 ><br>
						상품주문조회 ><br>
						변경 상품주문 조회
					</td>
					<td style="color:#FF7F27; font-weight:bold; text-align:center">GetChangedProductOrderList</td>
					<td align="center">O</td>
					<td align="center">-</td>
					<td align="center">콜백 위치에서 상태 변경 시 사용</td>
					<td style="padding:10px">
						출력없음 <br>
						- 콜백 주소로 타입 전달시 가장 처음 데이터를 조회 하여 주문번호를 추출한다.
					</td>
					<td></td>
				</tr>
				<tr>
					<td align="center">
						판매관리 ><br>
						발주처리 ><br>
						판매 취소
					</td>
					<td style="color:#FF7F27; font-weight:bold; text-align:center">CancelSale</td>
					<td align="center">-</td>
					<td align="center">O</td>
					<td style="padding:10px">
						http://'.$system['host'].'/addons/npay/npay.order.pro.php?n_num=운송장번호&n_name=우체국택배&npay_code=결제완료주문상품코드
					</td>
					<td align="center">
						내부프로세스 처리
					</td>
					<td align="center">실서비스시<br>접근제한</td>
				</tr>
                <tr>
                    <td align="center">
                        판매관리 ><br>
                        발주처리 ><br>
                        발주 처리
                    </td>
                    <td style="color:#FF7F27; font-weight:bold; text-align:center">PlaceProductOrder</td>
                    <td align="center">-</td>
                    <td align="center">O</td>
                    <td style="padding:10px">
                        내부프로세스 처리
                    </td>
                    <td align="center">
                        내부프로세스 처리
                    </td>
                    <td align="center">실서비스시<br>접근제한</td>
                </tr>
				<tr>
					<td align="center">
						판매관리 ><br>
						상품발송 ><br>
						발송 처리
					</td>
					<td style="color:#FF7F27; font-weight:bold; text-align:center">ShipProductOrder</td>
					<td align="center">-</td>
					<td align="center">O</td>
					<td style="padding:10px">
						http://'.$system['host'].'/addons/npay/npay.order.pro.php?n_num=운송장번호&n_name=우체국택배&npay_code=결제완료주문상품코드
					</td>
					<td align="center">
						내부프로세스 처리
					</td>
					<td align="center">실서비스시<br>접근제한</td>
				</tr>
			</tbody>
		</table><br>
		- <strong>연동규격</strong><br>
		1. 내부 프로세스 연동으로 콜백과 프로세스 처리는 1:1로 강제 한다.<br>
		2. 콜백 발생 시점의 주문변경조회는 콜백이 발생한 시점 (YYYY-mm-dd H:i:s)으로 부터 InquiryTimeTo 미지정 까지로 한다.<br>
		3. <span style="color:#FF7F27; font-weight:bold;">테스트의 경우 내부 프로세스를 실행하지 않으며 주문변경조회 기간을 2012-01-01T00:00:00+09:00 ~ 2012-01-02T00:00:00+09:00 으로 한다.</span><br>
		4. 실서비스 시 모든 콜백의 오류 사유는 평문으로 반환 된다.(에러의 경우 헤더코드 200이외 코드)<br>
		5. 실서비스의 모든 콜백 프로세스는 주문상품정보의 MallManageCode가 있을 경우 구동된다.(없을경우 에러 반환)<br><br>

		- <strong>연동테스트</strong><br>
		* 주소: <a href="http://'.$system['host'].'/addons/npay/test_tools/" target="_blank" style="color:#0072CA;">http://'.$system['host'].'/addons/npay/test_tools/</a><br>
		* 요청에 따라 동작이 처리 되는 테스트 툴입니다.<br>
		* 필요에 따라 직접 요청 하시어 테스트 가능 합니다.<br>
		* 실서비스 모드로 전환시 해당 페이지는 자동 차단됩니다.<br>
		<br>

		<strong>* 위 내용으로 주문연동(네이버페이 API연동) Production환경 라이센스 발급 및 콜백 등록 요청 드립니다.</strong>
	';
}
else if($_mode == 'last_notice') { // 최종 연동완료 발송내용
	if(!$siteInfo['npay_id'] || !$siteInfo['npay_all_key'] || !$siteInfo['npay_key'] || !$siteInfo['npay_bt_key'] || !$siteInfo['npay_lisense'] || !$siteInfo['npay_secret']) error_msgPopup_s('네이버페이 버튼연동 또는 주문연동이 정상적으로 마무리 되지 않은 상태입니다.'); 
	if($siteInfo['npay_mode'] == 'test' || $siteInfo['npay_sync_mode'] == 'test') error_msgPopup_s('네이버페이 버튼연동 또는 주문연동 모드가 테스트 모드 상태입니다.');
	$popup_title = '최종 연동완료 발송내용';
	$popup_subject = '['.$siteInfo['s_adshop'].'] 네이버페이 연동완료/오픈처리를 요청드립니다.';
	$popup_content = '
		안녕하세요. <br>
		'.$siteInfo['s_adshop'].' (<a href="http://'.$system['host'].'" target="_blank">http://'.$system['host'].'</a>) 입니다. <br>
		네이버페이 버튼연동/주문연동 모두 실 서비스로 전환하였습니다.<br><br>

		- <strong>네이버페이 가맹점ID:</strong> '.($siteInfo['npay_id']?$siteInfo['npay_id']:'네이버페이 ID를 저장 후 이용가능').'<br><br>

		<strong style="color:#ff0000">* 최종 확인 후 정상 오픈 처리 부탁드리겠습니다.</strong>
	';
}
?>
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
<div class="popup">
	<div class="pop_title"><strong><?php echo $popup_title; ?></strong></div>
	<div class="data_list">
		<table class="table_form">
			<colgroup>
				<col width="140">
				<col width="*-">
			</colgroup>
			<tbody>
				<tr>
					<th>수신자</th>
					<td>
						<div class="js_html_viewer js_html_to js-clipboard" data-clipboard-target=".js_html_to" contenteditable="true" oncut="return false;" onpaste="return false;">dl_Techsupport@navercorp.com</div>
					</td>
				</tr>
				<tr>
					<th>메일제목</th>
					<td>
						<div class="js_html_viewer js_html_subject js-clipboard" data-clipboard-target=".js_html_subject" contenteditable="true" oncut="return false;" onpaste="return false;"><?php echo $popup_subject; ?></div>
					</td>
				</tr>
				<tr>
					<th>발송내용</th>
					<td class="edit_td">
						<div class="js_html_viewer js_html_content js-clipboard" data-clipboard-target=".js_html_content" contenteditable="true" style="min-height:150px">
							<?php echo $popup_content; ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

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
<?php
include_once('inc.footer.php');
?>