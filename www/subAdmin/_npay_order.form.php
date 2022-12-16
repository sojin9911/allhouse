<?php
$app_current_link = '_npay_order.list.php'; // 목록페이지 지정
include_once('wrap.header.php');

# 재귀 && 입점업체 조건을 위한 어드민 구분 판별
$AdminPathData = parse_url($_SERVER['REQUEST_URI']);
$AdminPathData = explode('/', $AdminPathData['path']);
$AdminPath = $AdminPathData[1]; unset($AdminPathData);

# 정보조회
$r = _MQ("
	select
		`op`.*,
		`o`.*,
		`op`.`npay_status` as `npay_status`,
		`p`.`p_img_list`
	from
		`smart_order_product` as `op` left join
		`smart_product` as `p` on (`p`.`p_code` = `op`.`op_pcode`) left join
		`smart_order` as `o` on(`o`.`o_ordernum` = `op`.`op_oordernum`)
	where
		`op`.`op_uid` = '{$_uid}'
		".($AdminPath == 'subAdmin'?" and `op`.`op_partnerCode` = '{$_COOKIE["AuthCompany"]}' ":null)."
");
$sv = $r;
if($r['npay_order'] != 'Y') error_msg('네이버페이 주문만 조회가능합니다.');

# 주문상품정보 변수 할당
// -- 이미지 ---
$img_src = get_img_src($sv['p_img_list']);
$img_src = (@file_exists($_SERVER['DOCUMENT_ROOT'].'/upfiles/product/'.$img_src) && $img_src?$img_src:$sv['p_img_list']);


// -- 배송상품정보 ::: 택배, 송장, 발송일 표기 ---
$delivery_html = '';
if($sv['op_sendcompany']) {
	$delivery_html = "
		<div class='option_box'>
			<div class='pro_option'>
				<span  style='display:block'><span class='coupon_num'>택배사 : ". $sv['op_sendcompany'] ."</span></span>
				<span  style='display:block'><span class='coupon_num'>송장번호 : ". $sv['op_sendnum'] ."</span></span>
				<span  style='display:block'><span class='coupon_num'>발송일 : ". substr($sv['op_senddate'],0, 10) ."</span></span>
			</div>
		</div>
	";
}
// -- 배송상품정보 ---

// 상태아이콘
$StatusIcon = '';
if($r['npay_status'] == 'PAYED') $StatusIcon = '<span class="c_tag blue h22 t4">결제완료</span>';
if($r['npay_status'] == 'PLACE') $StatusIcon = '<span class="c_tag purple h22 t4">발주처리</span>';
if($r['npay_status'] == 'DISPATCHED') $StatusIcon = '<span class="c_tag green h22 t4">배송처리</span>';
if($r['npay_status'] == 'CANCELED') $StatusIcon = '<span class="c_tag light h22 t4">주문취소</span>';

// -- 배송비 ---
$delivery_print = ($sv['op_delivery_price'] > 0 && $delivery_print != "무료배송") ? number_format($sv['op_delivery_price'])."원" : "-"; // 배송정보.
$add_delivery_print = ($sv['op_add_delivery_price'] ? "<br>추가배송비 : +".number_format($sv['op_add_delivery_price'])."원" : "") ;// 추가배송비 여부
// -- 배송비 ---


$OrderDate = date("Y년 m월 d일 H시 i분",strtotime($r['o_rdate']));


// 함께 주문한 전체 상품
$OtherOrder = _MQ_assoc(" select * from `smart_order_product` where (1) and `op_oordernum` = '{$sv['op_oordernum']}' ".($AdminPath == 'subAdmin'?" and `op_partnerCode` = '{$_COOKIE['AuthCompany']}' ":null)." order by `op_uid` asc ");
?>
<?php if($AdminPath == 'totalAdmin') { // 통합관리자는 수정 가능 ?>
	<form action="_npay_order.pro.php" method="post">
		<input type="hidden" name="_mode" value="modify">
		<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
		<input type="hidden" name="ordernum" value="<?php echo $r['op_oordernum']; ?>">
<?php } echo PHP_EOL; ?>

	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>주문상품정보</strong></div>
	<!-- ● 데이터 리스트 -->
	<div class="data_list">
		<table class="table_list">
			<colgroup>
				<col width="100"/><col width="*"/><col width="120"/><col width="100"/><col width="120"/><col width="100"/><col width="100"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col">이미지</th>
					<th scope="col">상품정보</th>
					<th scope="col">상품가격</th>
					<th scope="col">수량</th>
					<th scope="col">주문금액</th>
					<th scope="col">배송비</th>
					<th scope="col">진행상태</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<!-- 상세페이지 이미지 if_img80 클래스 추가 -->
					<td class="if_img80">
						<?php echo ($img_src ? "<img src='" . '/upfiles/product/'.$img_src . "' style='width:100px;'>" : "-"); ?>
					</td>
					<td>
						<?php if($sv['mobile'] == 'Y') { ?>
							<span class="c_tag h18 t3 mo">MO주문</span>
						<?php } else { ?>
							<span class="c_tag h18 t3 pc">PC주문</span>
						<?php } ?>
						<!-- 상품정보 -->
						<div class="order_item">
							<!-- 상품명 -->
							<div class="title"><?php echo htmlspecialchars_decode($sv['op_pname']); ?></div>
							<!-- 옵션명, div반복 -->
							<?php echo ($sv['op_option1']?'<div class="option bullet">'.($sv['op_is_addoption'] == 'N'?'선택 : ':'추가 : ').htmlspecialchars_decode($sv['op_option1']).'</div>':null); ?>
							<?php echo ($sv['op_option2']?'<div class="option bullet">'.($sv['op_is_addoption'] == 'N'?'선택 : ':'추가 : ').htmlspecialchars_decode($sv['op_option2']).'</div>':null); ?>
							<?php echo ($sv['op_option3']?'<div class="option bullet">'.($sv['op_is_addoption'] == 'N'?'선택 : ':'추가 : ').htmlspecialchars_decode($sv['op_option3']).'</div>':null); ?>
						</div>
					</td>
					<td class="t_black"><?php echo number_format($sv['op_price']); ?>원</td>
					<td class="t_black"><?php echo number_format($sv['op_cnt']); ?>개</td>
					<td class="t_black bold"><?php echo number_format($sv['op_price'] * $sv['op_cnt']); ?>원</td>
					<td class="t_black bold"><?php echo $delivery_print.$add_delivery_print; ?></td>
					<td>
						<div class="lineup-vertical">
							<?php echo $StatusIcon; ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>



	<?php if(count($OtherOrder) > 0) { ?>
		<!-- ● 단락타이틀 -->
		<div class="group_title"><strong>함께 주문한 전체 상품</strong></div>
		<!-- ● 데이터 리스트 -->
		<div class="data_list">
			<table class="table_list">
				<colgroup>
					<col width="65"/><col width="*"/><col width="180"/><col width="100"/><col width="100"/><col width="100"/><col width="100"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col">번호</th>
						<th scope="col">상품정보</th>
						<th scope="col">NPAY CODE</th>
						<th scope="col">상태</th>
						<th scope="col">상세보기</th>
						<th scope="col">포인트 사용</th>
						<th scope="col">적립금 사용</th>
					</tr>
				</thead> 
				<tbody>
					<?php
					// 주문 번호별 구분 색상
					$ActiveorderColor = 'F5F5F5';
					foreach($OtherOrder as $ook=>$oov) {
						// 상태아이콘
						$StatusIconSub = '';
						if($oov['npay_status'] == 'PAYED') $StatusIconSub = '<span class="c_tag blue h22 t4">결제완료</span>';
						if($oov['npay_status'] == 'PLACE') $StatusIconSub = '<span class="c_tag purple h22 t4">발주처리</span>';
						if($oov['npay_status'] == 'DISPATCHED') $StatusIconSub = '<span class="c_tag green h22 t4">배송처리</span>';
						if($oov['npay_status'] == 'CANCELED') $StatusIconSub = '<span class="c_tag light h22 t4">주문취소</span>';

						// 현재상품 BG색
						$TDcolor = '';
						if($oov['op_uid'] == $_uid) $TDcolor = ' style="background-color:#'.$ActiveorderColor.'"';
					?>
						<tr>
							<td<?php echo $TDcolor; ?>><?php echo $ook+1; ?></td>
							<td<?php echo $TDcolor; ?>>
								<?php if($oov['mobile'] == 'Y') { ?>
									<span class="c_tag h18 mo">MO주문</span>
								<?php } else { ?>
									<span class="c_tag h18 t3 pc">PC주문</span>
								<?php } ?>
								<!-- 상품정보 -->
								<div class="order_item">
									<!-- 상품명 -->
									<div class="title">
										<?php echo htmlspecialchars_decode($oov['op_pname']); ?>
										<span class="t_light normal"> x <span class="t_black normal"><?php echo number_format($oov['op_cnt']); ?>개</span></span>
									</div>
									<!-- 옵션명, div반복 -->
									<?php echo ($oov['op_option1']?'<div class="option bullet">'.($oov['op_is_addoption'] == 'N'?'선택 : ':'추가 : ').htmlspecialchars_decode($oov['op_option1']).'</div>':null); ?>
									<?php echo ($oov['op_option2']?'<div class="option bullet">'.($oov['op_is_addoption'] == 'N'?'선택 : ':'추가 : ').htmlspecialchars_decode($oov['op_option2']).'</div>':null); ?>
									<?php echo ($oov['op_option3']?'<div class="option bullet">'.($oov['op_is_addoption'] == 'N'?'선택 : ':'추가 : ').htmlspecialchars_decode($oov['op_option3']).'</div>':null); ?>
								</div>
							</td>
							<td<?php echo $TDcolor; ?>><?php echo $oov['npay_order_code']; ?></td>
							<td<?php echo $TDcolor; ?>>
								<div class="lineup-vertical">
									<?php echo $StatusIconSub; ?>
								</div>
							</td>
							<?php if($oov['op_uid'] == $_uid) { ?>
								<td class="t_red"<?php echo $TDcolor; ?>>현재상품</td>
							<?php } else { ?>
								<td<?php echo $TDcolor; ?>>
									<div class="lineup-vertical">
										<a href="_npay_order.form.php?_mode=modify&_uid=<?php echo $oov['op_uid']; ?>" class="c_btn h22">상세보기</a>
									</div>
								</td>
							<?php } ?>
							<?php if($ook <= 0) { ?>
								<td class="t_black bold" rowspan="<?php echo count($OtherOrder); ?>"><?php echo number_format($r['npay_point']); ?>원</td>
								<td class="t_black bold" rowspan="<?php echo count($OtherOrder); ?>"><?php echo number_format($r['npay_point2']); ?>원</td>
							<?php } ?>
						</tr>
					<?php } ?>
				</tbody> 
			</table>
		</div>
	<?php } ?>



	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>네이버페이 처리</strong></div>
	<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<!-- 폼테이블 2단 -->
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>상품코드</th>
					<td>
						<span class="npay_tag"><strong>NPAY CODE</strong><em><?php echo $r['npay_order_code']; ?></em></span>
						<div class="clear_both"><span class="bold">MallManageCode :</span> <?php echo $r['npay_uniq']; ?></div>
					</td>
					<th>진행상태</th>
					<td>
						<?php echo $StatusIcon; ?>
					</td>
				</tr>
				<?php if(in_array($r['npay_status'], array('PAYED', 'PLACE')) || $r['npay_status'] != 'CANCELED') { ?>
					<tr>
						<?php if(in_array($r['npay_status'], array('PAYED', 'PLACE'))) { // 발주처리 or 발주처리 완료 표시 ?>
						<th>발주처리</th>
						<td>
							<a href="" class="c_btn h27 purple line bold">발주처리 하기</a><span class="c_tag h27 gray bold">발주처리 됨</span>
							<div class="tip_box">
								<div class="c_tip">발주처리 후 복구할 수 없습니다.</div>
								<div class="c_tip">고객이 네이버측에서 취소 및 주소지 변경이 불가하게 됩니다.</div>
							</div>
						</td>
						<?php } // 발주처리 or 발주처리 완료 표시 End ?>
						<?php
						$_expressname = $r['op_sendcompany'];
						$_expressnum = $r['op_sendnum'];
						if($r['npay_status'] != 'CANCELED') { // 취소시 배송처리 항목 보이지 않게 처리
						?>
						<th>배송처리</th>
						<td<?php echo (!in_array($r['npay_status'], array('PAYED', 'PLACE'))?' colspan="3"':null); ?>>
							<div class="lineup-resposive">
								<?php if(!$_expressname) { ?>
									<?php
										$NpayDeliveryCode = array(
											'CJ대한통운'=>'CJGLS',
											'로젠택배'=>'KGB',
											'KG로지스'=>'DONGBU',
											'우체국택배'=>'EPOST',
											'우편등기'=>'REGISTPOST',
											'한진택배'=>'HANJIN',
											'롯데택배'=>'HYUNDAI',
											'GTX로지스'=>'INNOGIS',
											'대신택배'=>'DAESIN',
											'일양로지스'=>'ILYANG',
											'경동택배'=>'KDEXP',
											'천일택배'=>'CHUNIL',
											'기타 택배'=>'CH1',
											'합동택배'=>'HDEXP',
											'편의점택배'=>'CVSNET',
											'DHL'=>'DHL',
											'FEDEX'=>'FEDEX',
											'GSMNTON'=>'GSMNTON',
											'WarpEx'=>'WARPEX',
											'WIZWA'=>'WIZWA',
											'EMS'=>'EMS',
											'DHL(독일)'=>'DHLDE',
											'ACI'=>'ACIEXPRESS',
											'EZUSA'=>'EZUSA',
											'범한판토스'=>'PANTOS',
											'UPS'=>'UPS',
											'CJ대한통운(국제택배)'=>'KOREXG',
											'TNT'=>'TNT',
											'성원글로벌'=>'SWGEXP',
											'대운글로벌'=>'DAEWOON',
											'USPS'=>'USPS',
											'i-parcel'=>'IPARCEL',
											'건영택배'=>'KUNYOUNG',
											'한의사랑택배'=>'HPL',
											'다드림'=>'DADREAM',
											'SLX택배'=>'SLX',
											'호남택배'=>'HONAM',
											'GSI익스프레스'=>'GSIEXPRESS',
											'직접배송'=>'DIRECT_DELIVERY',
											'방문수령'=>'VISIT_RECEIPT'
										);
										echo _InputSelect( "expressname" , array_keys($NpayDeliveryCode) , $_expressname , "" , "" , "-택배사 선택-");
									?>
									<input type="text" name="expressnum" class="design" style="width:145px" value="<?php echo $_expressnum; ?>" placeholder="송장번호를 입력하세요.">
									<a href="#none" onclick="NPaySendExpress(); return false;" class="c_btn h27 green line bold">발송처리 요청</a>
									<script type="text/javascript">
										function NPaySendExpress() {
											var expressname = $('select[name^=expressname] option:selected').val();
											var expressnum = $('input[name^=expressnum]').val();
											if(expressname == '') {
												alert('택배사를 선택해주세요.');
												return false;
											}
											if(!confirm('네이버페이주문연동 상품입니다.\n발송처리 후 수정할 수 없습니다. \n\n계속하시겠습니까?')) {
												alert('발송이 취소되었습니다..');
												return false;
											}
											location.href = '/addons/npay/npay.order.pro.php?path=<?php echo $AdminPath; ?>&op_uid=<?php echo $r['op_uid']; ?>&expressname='+expressname+'&expressnum='+expressnum+'&_PVSC=<?php echo $_PVSC; ?>';
										}
									</script>
								<?php } else { ?>
									<span class="bold"><?php echo $_expressname; ?></span>
									<span class="block"><?php echo $_expressnum; ?></span>
									<a href="<?php echo $NPayCourier[$r[op_sendcompany]].rm_str($r['op_sendnum']); ?>" class="c_btn h22 green line h22 t4" target="_blank">배송조회</a>
								<?php } ?>
							</div>
							<?php if(!$_expressname) { ?>
							<div class="tip_box">
								<?php echo _DescStr('네이버페이 정책상 발송처리 후 수정이 불가능 하오니 신중히 발송처리 바랍니다.'); ?>
								<?php echo _DescStr('송장번호 오류시 네이버페이로 문의 하시여 변경 요청 바랍니다.'); ?>
							</div>
							<?php } ?>
						</td>
						<?php } ?>
					</tr>
				<?php } ?>
				<?php if($AdminPath == 'totalAdmin' && (in_array($r['npay_status'], array('PAYED', 'PLACE'))) || $r['npay_status'] != 'CANCELED') { // 통합관리자 && 결제완료 혹은 발추처리 ?>
					<tr>
						<?php if($AdminPath == 'totalAdmin' && in_array($r['npay_status'], array('PAYED', 'PLACE'))) { // 통합관리자 && 결제완료 혹은 발추처리 ?>
							<th>네이버페이 주문취소</th>
							<td>
								<a href="#none" onclick="NPaySendCancel(); return false;" class="c_btn h27 line bold">주문취소 요청</a>
								<?php echo _DescStr('주문취소는 네이버페이 정책상 <em>결제완료 상태</em>에서만 가능 합니다.'); ?>
								<script type="text/javascript">
									function NPaySendCancel() {
										if(!confirm('취소요청을 하실경우 복구 할 수 없습니다.\n\n계속하시겠습니까?')) return false;
										location.href = '/addons/npay/npay.order.pro.php?path=<?php echo $AdminPath; ?>&_uid=<?php echo $r['op_uid']; ?>&_PVSC=<?php echo $_PVSC; ?>';
									}
								</script>
							</td>
						<?php } ?>
						<?php if($AdminPath == 'totalAdmin' && $r['npay_status'] != 'CANCELED') { // 강제취소 ?>
							<th>강제취소</th>
							<td<?php echo (!in_array($r['npay_status'], array('PAYED', 'PLACE'))?' colspan="3"':null); ?>>
								<a href="#none" onclick="if(confirm('네이버페이 관리자를 통하여 반품 및 환불을 하였을 경우 사용 바랍니다.\n\n계속하시겠습니까?')) document.location.href = '_npay_order.pro.php?_mode=force_cancel&npay_code=<?php echo $r['npay_order_code']; ?>&_PVSC=<?php echo $_PVSC; ?>'; return false;" class="c_btn h27 black line dark bold">강제취소</a>
								<?php echo _DescStr('네이버페이 관리자를 통하여 반품 및 환불을 하였을 경우 사용 바랍니다.'); ?>
							</td>
						<?php } ?>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<!-- / 폼테이블 2단 -->
	</div>



	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>관리자 메모</strong></div>
	<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<!-- 폼테이블 2단 -->
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>메모 내용</th>
					<td colspan="3">
						<textarea name="o_admcontent" rows="4" cols="" class="design"><?php echo stripslashes($r['o_admcontent']); ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
		<!-- /폼테이블 2단 -->
	</div>


	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>결제 정보</strong></div>
	<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<!-- 폼테이블 2단 -->
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>결제금액</th>
					<!-- td에 텍스트만 있을경우 only_text 클래스 추가 -->
					<td class="only_text bold"><?php echo number_format($sv['op_price'] * $sv['op_cnt']); ?>원</td>
					<th>결제수단</th>
					<td class="only_text bold"><?php echo $arr_payment_type[$sv['o_paymethod']]; ?></td>
				</tr>
				<tr>
					<th>결제일</th>
					<td<?php echo ($AdminPath == 'totalAdmin' && $SubAdminMode === true?null:' colspan="3"'); ?>>
						<?php echo date('Y-m-d', strtotime($r['o_rdate'])); ?>
						<span class="t_light"><?php echo date('H:i:s', strtotime($r['o_rdate'])); ?></span>
					</td>
					<?php
					if($AdminPath == 'totalAdmin' && $SubAdminMode === true) {
						$CompanyInfo = _MQ("select cp_name from smart_company where cp_id = '{$sv['op_partnerCode']}'");
					?>
						<th>공급업체</th>
						<td class="only_text">
							<?php if($CompanyInfo['cp_name']) { ?><a href="_entershop.form.php?_mode=modify&_id=<?php echo $sv['op_partnerCode']; ?>" target="_blank"><?php } ?>
								<span class="bold"><?php echo ($CompanyInfo['cp_name']?$CompanyInfo['cp_name']:'<span class="t_orange">삭제됨</span>'); ?></span>
								(아이디 : <?php echo $sv['op_partnerCode']; ?>)
							<?php if($CompanyInfo['cp_name']) { ?></a><?php } ?>
						</td>
					<?php } ?>
				</tr>
			</tbody>
		</table>
		<!-- /폼테이블 2단 -->
	</div>



	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>주문자 정보</strong></div>
	<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<!-- 폼테이블 2단 -->
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>주문번호</th>
					<td class="only_text"><span class="bold"><?php echo $r['op_oordernum']; ?></span></td>
					<th>주문일시</th>
					<td class="only_text"><span class="bold"><?php echo $OrderDate; ?></span></td>
				</tr>
				<tr>
					<th>주문자명</th>
					<td class="only_text">
						<?php echo showUserInfo($r['o_mid'], $r['o_oname']); ?>
					</td>
					<th>휴대폰번호</th>
					<td><input type="text" name="o_ohp" class="design" value="<?php echo $r['o_ohp']; ?>" style="width: 110px;"></td>
				</tr>
				<tr>
					<th>이메일 주소</th>
					<td colspan="3"><input type="text" name="o_oemail" class="design" style="width:185px;" value="<?php echo $r['o_oemail']; ?>"></td>
				</tr>
			</tbody>
		</table>
		<!-- /폼테이블 2단 -->
	</div>



	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>받는 분 정보</strong></div>
	<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<!-- 폼테이블 2단 -->
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>받는 분 이름</th>
					<td><input type="text" name="o_rname" value="<?php echo $r['o_rname']; ?>" class="design" style="width:100px"></td>
					<th>휴대폰번호</th>
					<td><input type="text" name="o_rhp" class="design" value="<?php echo $r['o_rhp']; ?>" style="width: 110px;"></td>
				</tr>
				<tr>
					<th>배송지 주소</th>
					<td>
						<?php $Zip = explode('-', $r['o_rpost']); ?>
						<input type="text" name="_rzip1" id="_post1" value="<?php echo $Zip[0]; ?>" class="design t_center" style="width:50px">
						<span class="fr_tx">-</span>
						<input type="text" name="_rzip2" id="_post2" value="<?php echo $Zip[1]; ?>" class="design t_center" style="width:50px">
						<a href="#none" onclick="new_post_view(); return false;" class="c_btn h27 black">우편번호 찾기</a>
						<div class="lineup-full">
							<input type="text" name="_raddress" id="_addr1" class="design" value="<?php echo $r['o_raddr1']; ?>">
							<input type="text" name="_raddress1" id="_addr2" class="design" value="<?php echo $r['o_raddr2']; ?>">
						</div>
					</td>
					<th>도로명 주소</th>
					<td>
						<input type="text" name="_rzonecode" id="_zonecode" value="<?php echo $r['o_rzonecode']; ?>" class="design t_center" style="width:70px">
						<div class="lineup-full">
							<input type="text" name="_raddress_doro" id="_addr_doro" value="<?php echo $r['o_raddr_doro']; ?>" class="design">
						</div>
					</td>
				</tr>
				<tr>
					<th>배송시 유의사항</th>
					<td colspan="3">
						<textarea name="comment" rows="4" cols="" class="design"><?php echo htmlspecialchars(stripslashes($r['o_content'])); ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
		<!-- /폼테이블 2단 -->
	</div>


<!-- 상세페이지 버튼 -->
<?php if($AdminPath == 'totalAdmin') { ?>
		<?php echo _submitBTN($app_current_link); ?>
	</form>
<?PHP
} else { // 입점업체의 경우 확인 버튼 제거
	if(strpos($str,'?')===false) $prefix = '?';
	else $prefix = '&';
	$app_pvsc = URI_Rebuild(enc('d' , $_PVSC));
	$app_current_link = $app_current_link.($app_pvsc?$prefix:null).$app_pvsc;
?>
	<div class="c_btnbox">
		<ul>
			<li><a href="<?php echo $app_current_link; ?>" class="c_btn h46 black line" accesskey="l">목록</a></li>
		</ul>
	</div>
	<div class="fixed_save js_fixed_save" style="display: none;">
		<div class="wrapping">
			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
					<li><a href="<?php echo $app_current_link; ?>" class="c_btn h34 black line" accesskey="l">목록</a></li>
				</ul>
			</div>
		</div>
	</div>
<?php } ?>
<?php
include_once(OD_ADDONS_ROOT.'/newpost/newpost.search.php');
include_once('wrap.footer.php');
?>