<?php
if($_REQUEST['_mode'] == 'modify') {
    $app_current_name = '상품 수정';
    $app_current_link = '_product.list.php';
}
	include_once("inc.header.php");

// 수기주문 - 상품등록
$app_mode = "";
$o_ordernum = $_GET["o_ordernum"];
$op_pouid = $_GET["op_pouid"];
if ($o_ordernum && $op_pouid) {
    $app_mode = "popup";
    $sque = "select * from smart_order_product where op_oordernum='$o_ordernum' AND op_pouid='$op_pouid' ";
    $op = _MQ($sque);
    $row['p_name'] = $op['op_pname'];
    $row['p_option_type_chk'] = "2depth";
    $row['p_use_content'] = "Y";
}

if ($app_mode == "popup") {
	include_once("inc.header.php");
} else {
    include_once('wrap.header.php');
}

// - 수정 ---
if( $_mode == "modify" ) {
    $que = " select * from smart_product where p_code='${_code}'  ";
    $row = _MQ($que);

    $_str = "수정";
    $app_cpname = $row['p_cpid'] . ":" . $row['cp_name'];

    // - 텍스트 정보 추출 ---
    $_text_info_extraction = _text_info_extraction( "smart_product" , $row['p_code'] );
    $_text_info_extraction = is_array($_text_info_extraction) ? $_text_info_extraction : array();
    if($row) $row = array_merge($row , $_text_info_extraction);
}
// - 수정 ---

// - 등록 ---
else {
    $_mode = "add";
    $_str = "등록";
    $app_cpname = "";
    $row['c_parent'] = $pass_parent01;
    $_code = shop_productcode_create();// 예 : A1234-B1234-C1234

    // 등록상품이 가장위에 노출되도록
    $row['p_sort_group'] = 100;
    $row['p_sort_idx'] = 0.5;
    $row['p_idx'] = 0.5;
}
// - 등록 ---


// 아이콘 정보 배열로 추출
$product_icon = get_product_icon_info("product_name_small_icon");

// - 입점업체 ---
$arr_customer = arr_company();
$arr_customer2 = arr_company2();

// 입점업체 아이디 추출
// $_cpid = _MQ("select cp_id from smart_company limit 1");
// $_cpid = $_cpid[cp_id];

// 스킨정보 추출
$SkinInfo = SkinInfo();
?>



<form name="frm" method="post" ENCTYPE="multipart/form-data" action="_product.pro.php" >
<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
<input type="hidden" name="_code" value="<?php echo $_code; ?>">

<input type="hidden" name="o_ordernum" value="<?php echo $o_ordernum; ?>">
<input type="hidden" name="op_pouid" value="<?php echo $op_pouid; ?>">
<!-- <input type=hidden name="_cpid" value="<?php echo $_cpid; ?>"> -->

	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>상품노출 설정</strong></div>


	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>상품 노출</th>
					<td colspan="3">
						<?php echo _InputRadio( '_view' , array('Y','N'), ($row['p_view'] ? $row['p_view'] : 'N') , '' , array('노출','숨김') , ''); ?>
					</td>
				</tr>
				<tr>
					<th>상품 아이콘</th>
					<td colspan="3">
						<?php
							$r2 = $product_icon;
							$pi_uid_array = explode(",",$row[p_icon]);
							if(sizeof($r2) > 0){
								foreach($r2 as $k2 => $v2) {
									$checked = @array_search($v2[pi_uid],$pi_uid_array) === false ? NULL : " checked ";
									echo "<label class='design'><input type='checkbox' name='_icon[]' value='". $v2['pi_uid'] ."' ".$checked."><img src='". IMG_DIR_ICON . $v2['pi_img'] ."' title='".$v2['pi_title']."'></label>";
								}
							}else{
								echo _DescStr('등록된 상품 아이콘이 없습니다.');
							}
						?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>




	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>상품 카테고리 설정</strong></div>




	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>상품 카테고리</th>
					<td>
						<?php include_once("_product.inc_category_form.php"); ?>
					</td>
				</tr>
				<tr>
					<th>선택된 카테고리</th>
					<td>

						<span id="_product_cateogry_list">
							<!-- 상품카테고리 목록 노출 -->
							<?PHP
								$_cmode = "list";
								//$_code = $_code;
								include_once("_product.inc_category_pro.php");
							?>
						</span>

					</td>
				</tr>
			</tbody>
		</table>
	</div>




	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>상품 기본 정보</strong></div>




	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th class="ess">상품코드</th>
					<td>
						<input type="text" name="" class="design" value="<?php echo $_code; ?>" placeholder="" style="width:200px" readonly="readonly">
						<?php if($_mode != 'add') { ?>
							<a href="#none" onclick="window.open('/?pn=product.view&pcode=<?php echo $_code; ?>'); return false;" class="c_btn h27">미리보기</a>
						<?php } ?>
					</td>
				</tr>
				<tr<?php echo ($SubAdminMode === false?' style="display:none"':null); ?> class="ess">
					<th class="ess">입점업체</th>
					<td>
						<?php
							// 입점기능 off시 상품 추가 기본 입점업체 적용 2016-05-25 LDD
							if($SubAdminMode === false && !$row['p_cpid']) {

								foreach($arr_customer as $k=>$v) {

									if($row['p_cpid']) continue;
									$row['p_cpid'] = $k;
								}
							}
							// 입점기능 off시 상품 추가 기본 입점업체 적용 2016-05-25 LDD
						?>
						<!-- 20개 이상일때만 select2적용 -->
						<?php if(sizeof($arr_customer) > 20){ ?>
						<link href="/include/js/select2/css/select2.css" type="text/css" rel="stylesheet">
						<script src="/include/js/select2/js/select2.min.js"></script>
						<script>$(document).ready(function() { $('.select2').select2(); });</script>
						<?php } ?>
						<?php echo _InputSelect( '_cpid' , array_keys($arr_customer) , $row['p_cpid'] , ' class="select2" ' , array_values($arr_customer) , '-입점업체-'); ?>
					</td>
				</tr>


				<?php
					// JJC ::: 브랜드 정보 추출  ::: 2017-11-03
					//		basic : 기본정보
					//		all : 브랜드 전체 정보
					$arr_brand = brand_info('basic');
				?>
				<tr>
					<th>브랜드</th>
					<td>
						<?php echo _InputSelect( "_brand" , array_keys($arr_brand) , $row['p_brand'] , "" , array_values($arr_brand) , "-브랜드-"); ?>
					</td>
				</tr>
				<? // JJC ::: 브랜드 정보 추출  ::: 2017-11-03?>




				<?php // 2017-06-16 ::: 부가세율설정 ::: JJC ?>
				<?php if($siteInfo['s_vat_product'] == 'C'){ ?>
				<tr>
					<th>과세여부</th>
					<td>
						<label class="design"><input type="radio" name="p_vat" value="Y" <?php echo ($row['p_vat'] == "Y" || !$row['p_vat'] ? "checked" : NULL); ?>>과세</label>
						<label class="design"><input type="radio" name="p_vat" value="N" <?php echo ($row['p_vat'] == "N" ? "checked" : NULL); ?>>면세</label>
						<div class="tip_box">
							<?php echo _DescStr('과세 선택 시 판매가격에 부가세 포함되어 있습니다. 세금계산서와 현금영수증 발행 시 부가세가 포함 됩니다.'); ?>
							<?php echo _DescStr('면세 선택 시 판매가격에 부가세 포함되어 있지 않습니다. 세금계산서와 현금영수증은 발행되지 않습니다.'); ?>
							<?php echo _DescStr('카드 결제 시 세금계산서와 현금영수증은 발행되지 않습니다.'); ?>
						</div>
                    </td>
				</tr>
				<?php } ?>
				<?php // 2017-06-16 ::: 부가세율설정 ::: JJC ?>

				<tr>
					<th>업체정산형태</th>
					<td>
						<!-- 숨기지 말고, 인풋 disabled 로 구분 -->
						<label class="design"><input type="radio" name="_commission_type" value="공급가" <?php echo ($row['p_commission_type'] == '공급가' ? ' checked' : null); ?>>공급가(매입가)</label>
						<input type="text" name="_sPrice" id="comSaleTypeTr1" class="design number_style" value="<?php echo $row['p_sPrice']; ?>" placeholder="" style="width:100px" <?php echo ($row['p_commission_type'] <> '공급가' ? ' disabled' : null); ?>>
						<span class="fr_tx">원</span>
						<label class="design"><input type="radio" name="_commission_type" value="수수료" <?php echo ($row['p_commission_type'] <> '공급가' ? ' checked' : null); ?>>수수료</label>
						<input type="text" name="_sPersent" id="comSaleTypeTr2" class="design number_style" value="<?php echo ($row['p_sPersent'] ? $row['p_sPersent'] : $siteInfo['s_account_commission']); ?>" placeholder="" style="width:50px" <?php echo ($row['p_commission_type'] == '공급가' ? ' disabled' : null); ?>>
						<span class="fr_tx">%</span>
					</td>
				</tr>
				<tr>
					<th class="ess">상품명</th>
					<td>
						<span class="fr_bullet">대표 상품명 : </span><input type="text" name="_name" class="design" value="<?php echo $row['p_name']; ?>" placeholder="" style="width:500px">
						<div class="dash_line"><!-- 점선라인 --></div>
						<span class="fr_bullet">부가 상품명 : </span><input type="text" name="_subname" class="design" value="<?php echo $row['p_subname']; ?>" placeholder="" style="width:500px">
					</td>
				</tr>
				<tr>
					<th class="ess">상품가격</th>
					<td>
						<span class="fr_tx">정상가</span>
						<input type="text" name="_screenPrice" class="design number_style" value="<?php echo $row["p_screenPrice"]; ?>" placeholder="" style="width:100px">
						<span class="fr_tx">원</span>
						<div class="bar"></div>
						<span class="fr_tx">판매가</span>
						<input type="text" name="_price" class="design number_style" value="<?php echo $row["p_price"]; ?>" placeholder="" style="width:100px">
						<span class="fr_tx">원</span>
						<?php // 정상가 문구 추가 kms 2019-09-19 ?>
						<div class="tip_box">
							<?php echo _DescStr('정상가가 없을 경우 0으로 입력해 주시면 됩니다.'); ?>
							<?php echo _DescStr('정상가가 판매 가와 같을 경우 적용되지 않습니다.'); ?>
						</div>
					</td>
				</tr>
				<!-- KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22 -->
				<tr>
					<th>상품쿠폰</th>
					<td>
						<?php
							$ex_coupon = explode("|" , $row['p_coupon']);
							if(count($ex_coupon) < 4){  $_tmp = $ex_coupon; $ex_coupon[0] = $_tmp[0]; $ex_coupon[2] = $_tmp[1]; $ex_coupon[1] = ''; }// 이전 데이터 예외 처리
						?>
						<?php echo _InputRadio( "_coupon_type" , array('', 'price' ,'per') , $ex_coupon[1] , "  " , array('미사용', '할인금액(원)' ,'할인율(%)'));?>
						<div class="dash_line"><!-- 점선라인 --></div>

						<div class="cls_coupon_type price per">
							<span class="fr_tx">쿠폰명</span>
							<input type="text" name="_coupon_title" class="design " value="<?php echo $ex_coupon[0]; ?>" placeholder="" style="width:150px">
							<div class="bar"></div>
						</div>

						<div class="cls_coupon_type price">
							<span class="fr_tx">쿠폰 할인금액 : </span>
							<input type="text" name="_coupon_price" class="design number_style" value="<?php echo $ex_coupon[2]; ?>" placeholder="" style="width:100px">
							<span class="fr_tx">원</span>
						</div>

						<div class="cls_coupon_type per">
							<span class="fr_tx">쿠폰 할인율 : </span>
							<input type="text" name="_coupon_per" class="design number_style" data-decimals="1" value="<?php echo number_format(floor($ex_coupon[3]*10)/10,1); ?>" placeholder="" style="width:100px">
							<span class="fr_tx">%</span>
							<div class="bar"></div>
							<span class="fr_tx">최대 할인금액 : </span>
							<input type="text" name="_coupon_max" class="design number_style" value="<?php echo $ex_coupon[4]; ?>" placeholder="" style="width:100px">
							<span class="fr_tx">원</span>
							<div class="tip_box">
								<?php echo _DescStr('쿠폰 할인율은 소수 첫째 자리까지 허용합니다.'); ?>
								<?php echo _DescStr('쿠폰 할인율 반영 후 실제 할인금액 적용 시 소수 이하는 버림처리합니다.'); ?>
								<?php echo _DescStr('최대 할인금액에 공백 또는 0원 설정 시 할인금액의 제한이 없습니다.'); ?>
							</div>
						</div>
						<span class="tip_box">
							<?php
								// SSJ : 2017-12-14 자동적용 상품쿠폰 아이콘 추출
								$coupon_icon = get_product_icon_info('product_coupon_small_icon'); $coupon_icon = $coupon_icon[0];
								$_icon_src = get_img_src($coupon_icon['pi_img'], IMG_DIR_ICON);
							?>​
							<?php echo _DescStr('쿠폰명 / 쿠폰 할인금액 혹은 할인율 선택 후 입력하여야만 적용되며 <em>쿠폰상품 아이콘</em> <span class="preview_icon"><img src="images/log_guide.gif" alt="">'. ($_icon_src ? '<span class="ov"><img src="'. $_icon_src .'" alt="'. $coupon_icon['pi_title'] .'"></span>' : null) .'</span>이 자동으로 붙습니다.'); ?>
							<?php echo _DescStr('쿠폰상품 아이콘은 [상품 &gt; 상품관리 &gt; 상품 아이콘 관리]에서 등록/수정하실 수 있습니다.'); ?>
							<?php echo _DescStr('쿠폰할인금액은 상품금액을 초과할 수 없습니다.'); ?>
						</span>
					</td>
				</tr>
				<!-- KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22 -->
				<tr>
					<th>무료배송이벤트 적용여부</th>
					<td >
						<?php
							// 무료배송 이벤트 전체설정이 되어있는지 체크
							$freeEventChk = PromotionEventDeliveryChk('view'); // 무료배송 이벤트 기본체크
							echo _InputRadio( "_free_delivery_event_use" , array('Y','N'), ($row['p_free_delivery_event_use'] ? $row['p_free_delivery_event_use'] : "N") , " class='_free_delivery_event_use' " , array('적용','미적용') , '');

							if( $freeEventChk === true) {
								echo _DescStr('무료배송이벤트 사용에 따른 상품별 적용여부를 설정할 수 있습니다.');
							}else{
								echo _DescStr('무료배송이벤트를 적용할 수 없습니다.<em>통합관리자 > 프로모션 > 이벤트 > 무료배송이벤트</em> 에서 설정을 확인해 주세요.','black');
							}
						?>
					</td>
				</tr>
				<tr>
					<th>회원등급추가혜택</th>
					<td >
						<?php
							echo _InputRadio( "_groupset_use" , array('Y','N'), ($row['p_groupset_use'] ? $row['p_groupset_use'] : "N") , " class='_groupset_use' " , array('적용','미적용') , '');
							echo _DescStr('회원등급에 따른 추가 혜택에 대한 적용여부를 설정할 수 있습니다.');
						?>
					</td>
				</tr>

				<?php // LCY : 네이버페이 사용유무 추가 : 2020-10-20 ?>
				<tr>
					<th>네이버페이 사용유무</th>
					<td >
						<?php
							echo _InputRadio( "npay_use" , array('Y','N'), ($row['npay_use'] ? $row['npay_use'] : "N") , "" , array('사용','미사용') , '');

							echo '<div class="tip_box">';
							echo _DescStr('네이버페이 사용 시 상품별 설정이 가능하며 미사용 설정 시 네이버정책 위반이 될 수 있으므로 주의하시기 바랍니다.');
							echo _DescStr('빈드시 네이버페이 결제 또는 재고 문제가 있을 시 해당 설정을 통해 네이버페이를 일시적으로 미사용 처리 할 수 있습니다.');
							echo '</div>';
						?>
					</td>
				</tr>
				<?php // LCY : 네이버페이 사용유무 추가 : 2020-10-20 ?>


			</tbody>
		</table>
	</div>





	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>상품 부가정보 및 옵션 설정</strong></div>




	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*"><col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>재고량</th>
					<td>
						<input type="text" name="_stock" class="design number_style" value="<?php echo $row['p_stock']; ?>" placeholder="" style="width:100px">
					</td>
					<th>상품순위</th>
					<td>
						<input type="text" name="_sort_group" class="design number_style" value="<?php echo $row['p_sort_group']; ?>" placeholder="" style="width:50px">
						<input type="hidden" name="_sort_idx" class="design" value="<?php echo $row['p_sort_idx']; ?>" >
						<input type="hidden" name="_idx" class="design" value="<?php echo $row['p_idx']; ?>" >
					</td>
				</tr>
				<tr>
					<th>판매량</th>
					<td>
						<input type="text" name="_salecnt" class="design number_style" value="<?php echo $row['p_salecnt']; ?>" placeholder="" style="width:100px">
					</td>
					<th>적립율</th>
					<td>
						<input type="text" name="_point_per" class="design number_style" data-decimals="1" value="<?php echo $row['p_point_per']; ?>" placeholder="" style="width:50px">
						<span class="fr_tx">%</span>
						<?php echo _DescStr('상품구매 시 상품가격 당 소수점 1자리까지 %로 적용되며, 적립 시 소수점 이하는 버림처리됩니다. 예) 0.1%적용 시 10,000원 구매하여 결제하면 적립금 10포인트가 적립됩니다.'); ?>
					</td>
				</tr>
				<tr>
					<th>제조사</th>
					<td>
						<input type="text" name="_maker" class="design" value="<?php echo $row['p_maker']; ?>" placeholder="" style="">
					</td>
					<th>원산지</th>
					<td>
						<input type="text" name="_orgin" class="design" value="<?php echo $row['p_orgin']; ?>" placeholder="" style="">
					</td>
				</tr>


				<tr>
					<th>옵션설정</th>
					<td colspan="3">

						<div style="clear:both;">
							<?php echo _InputRadio( "_option_type_chk" , array('nooption','1depth','2depth','3depth'), ($row['p_option_type_chk'] ? $row['p_option_type_chk'] : "nooption") , " class='_option_type_chk' " , array('옵션사용안함','1차옵션','2차옵션','3차옵션') , '');?>
                            <input type="hidden" name="p_option1_type" class="design" value="normal" placeholder="" style="">
                            <input type="hidden" name="p_option2_type" class="design" value="normal" placeholder="" style="">
                            <input type="hidden" name="p_option3_type" class="design" value="normal" placeholder="" style="">
						</div>

					</td>
				</tr>

				<tr>
					<th>상품옵션</th>
					<td>
						<a href="#none" onclick="option_popup('<?php echo $_code; ?>'); return false;" class="c_btn h27 black">옵션창 열기</a>
						<a href="#none" onclick="common_option_popup('<?php echo $_code; ?>'); return false;" class="c_btn h27">자주쓰는 옵션</a>
						<?php echo _DescStr('주문 내역이 있는 상품의 옵션은 변경하지 마시기 바랍니다.'); ?>
					</td>
					<th>&nbsp;</th>
					<td>
					</td>
				</tr>
				<tr>
					<th>정보제공고시</th>
					<td colspan="3">
						<a href="#none" onclick="reqinfo_popup();" class="c_btn h27 black">정보제공고시 관리창 열기</a>
						<?php echo _DescStr('상품에 필요한 정보제공고시의 항목과 내용으로 등록하며, 등록된 내용은 상품 상세페이지에 노출됩니다.'); ?>
					</td>
				</tr>

				<!-- 연관상품 -->
				<?php include '_product.relation.php'; ?>
				<!-- 연관상품 -->

				<!-- 해시태그 -->
				<?php include '_product.hashtag.php'; ?>
				<!-- 해시태그 끝 -->

			</tbody>
		</table>
	</div>



	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>배송정보 설정</strong></div>





	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>배송정보</th>
					<td>
						<input type="text" name="_delivery_info" class="design" placeholder="" value="<?php echo $row['p_delivery_info']?>" style="">
						<div class="c_tip">예 : 로젠택배 (2~3일 소요) </div>
					</td>
				</tr>
				<tr>
					<th>배송비 설정</th>
					<td>
						<label class="design"><input type="radio" name="_shoppingPay_use" class="_shoppingPay_use" value="N" <?php echo ($row['p_shoppingPay_use'] == 'N' || $row['p_shoppingPay_use'] == '' ? ' checked ' : NULL); ?>><?php echo ($SubAdminMode ? '입점업체' : '쇼핑몰'); ?> 배송비 정책 적용</label>

						<?php
							// ----- JJC : 상품별 배송비 : 2018-08-16 -----
						?>
						<label class="design"><input type="radio" name="_shoppingPay_use" class="_shoppingPay_use" value="P" <?php echo ($row['p_shoppingPay_use'] == 'P' ? ' checked ' : NULL); ?>>상품별 배송비</label>
						<?php
							// ----- JJC : 상품별 배송비 : 2018-08-16 -----
						?>
						<label class="design"><input type="radio" name="_shoppingPay_use" class="_shoppingPay_use" value="Y" <?php echo ($row['p_shoppingPay_use'] == 'Y' ? ' checked ' : NULL); ?>>개별 배송비 적용</label>
						<!-- 개별배송비 일때 -->
						<input type="text" name="_shoppingPay" class="design number_style" id="_shoppingPay_use_Y" placeholder="" value="<?php echo $row['p_shoppingPay']; ?>" style="width:100px" <?php echo ($row['p_shoppingPay_use'] <> 'Y' ? ' disabled ' : NULL); ?>>
						<span class="fr_tx">원</span>
						<label class="design"><input type="radio" name="_shoppingPay_use" class="_shoppingPay_use" value="F" <?php echo ($row['p_shoppingPay_use'] == 'F' ? ' checked ' : NULL); ?>>무료 배송 적용</label>
						<!-- 무료배송비 일때 -->
						<?php
							// SSJ : 2017-12-14 자동적용 무료배송 아이콘 추출
							$freedelivery_icon = get_product_icon_info('product_freedelivery_small_icon'); $freedelivery_icon = $freedelivery_icon[0];
							$_icon_src = get_img_src($freedelivery_icon['pi_img'], IMG_DIR_ICON);
						?>
						<?php echo _DescStr('무료 배송 적용 시 <em>무료배송 아이콘</em> <span class="preview_icon"><img src="images/log_guide.gif" alt="">'. ($_icon_src ? '<span class="ov"><img src="'. $_icon_src .'" alt="'. $freedelivery_icon['pi_title'] .'"></span>' : null) .'</span>이 자동으로 붙습니다.'); ?>
						<!-- 쇼핑몰 배송비 정책 적용일때 -->
						<div>
							<div class="dash_line"><!-- 점선라인 --></div>
							<a href="#none" onclick="entershop_view(); return false;" class="c_btn h27 black">쇼핑몰 배송비 정책 확인</a>
							<div class="c_tip">장바구니에 담긴 전체금액을 기준으로 무료배송비가 적용됩니다.</div>
						</div>

						<?php
							// ----- JJC : 상품별 배송비 : 2018-08-16 -----
						?>
						<div class="shoppingPayP">
							<div class="dash_line"><!-- 점선라인 --></div>
							<span class="fr_tx">상품별 기본배송비</span>
							<input type="text" name="_shoppingPayPdPrice" class="design number_style" placeholder="" value="<?php echo $row['p_shoppingPayPdPrice']; ?>" style="width:100px" <?php echo ($row['p_shoppingPay_use'] <> 'P' ? ' disabled ' : NULL); ?>>
							<span class="fr_tx">원. 무료배송비</span>
							<input type="text" name="_shoppingPayPfPrice" class="design number_style" placeholder="" value="<?php echo $row['p_shoppingPayPfPrice']; ?>" style="width:100px" <?php echo ($row['p_shoppingPay_use'] <> 'P' ? ' disabled ' : NULL); ?>>
							<span class="fr_tx">원</span>
							<div class="c_tip">장바구니에 담긴 상품당 금액을 기준으로 무료배송비가 적용됩니다.</div>
						</div>
						<?php
							// ----- JJC : 상품별 배송비 : 2018-08-16 -----
						?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>




	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>상품 상세 설명</strong></div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">

		<!-- ● 내부탭 -->
		<div class="c_tab">
			<ul>
				<li class="hit"><a href="#none" class="btn tab_menu" data-idx="pc" data-trigger="N"><strong>상품상세설명(PC)</strong></a></li>
				<li><a href="#none" class="btn tab_menu" data-idx="mobile" data-trigger="Y"><strong>상품상세설명(MOBILE)</strong></a></li>
			</ul>
			<label class="design"><input type="checkbox" name="_use_content" value="Y" <?php echo ($row['p_use_content'] == 'Y' ? ' checked ' : null); ?>>PC/MOBILE 상세설명 함께 사용</label>
		</div>

		<table class="table_form">
			<tbody>
				<tr>
					<td>
						<div class="tab_conts" data-idx="pc">
							<textarea name="_content" class="input_text SEditor" style="width:100%;height:300px;"><?php echo stripslashes($row['p_content']); ?></textarea>
						</div>
						<div class="tab_conts" data-idx="mobile" style="display:none">
							<textarea name="_content_m" class="input_text SEditor" style="width:100%;height:300px;"><?php echo stripslashes($row['p_content_m']); ?></textarea>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>



	<!-- 이용안내 -->
	<?php include_once(dirname(__file__)."/_product.inc_guide.php"); ?>


	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>상품 이미지 등록</strong></div>


	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>이미지등록 설정</th>
					<td>
						<label class="design"><input type="radio" name="_img_auto_resize_use" <?php echo ($_mode<>'modify'?' checked ':''); ?> class="img_auto_resize_use" value="auto">자동등록</label>
						<label class="design"><input type="radio" name="_img_auto_resize_use" <?php echo ($_mode=='modify'?' checked ':''); ?> class="img_auto_resize_use" value="direct">직접등록</label>
					</td>
				</tr>
				<tr class="auto_area">
					<th>대표 이미지</th>
					<td>
						<?php echo _PhotoForm( '../upfiles/product', '_img_main_tmp', '', 'style="width:300px"')?>
						<?php echo _DescStr("권장사이즈 : {$SkinInfo['product']['main_image_width']} x {$SkinInfo['product']['main_image_height']} (pixel)"); ?>
					</td>
				</tr>
				<tr>
					<th>개별 이미지</th>
					<td>
						<!-- ● 데이터 리스트 -->
						<table class="table_form direct_area">
							<colgroup>
								<col width="140"><col width="*">
							</colgroup>
							<tbody>
								<tr>
									<th>목록 이미지</th>
									<td>
										<?php
											// 파일삭제 체크를위한 기존이미지 저장
											echo '<input type="hidden" name="backup_img_org" value="'. implode('||', array_filter(array($row['p_img_list_square'],$row['p_img_b1'],$row['p_img_b2'],$row['p_img_b3'],$row['p_img_b4'],$row['p_img_b5']))) .'">';
										?>

										<!-- SSJ : 2017-12-12 외부이미지 사용 추가 -->
										<div class="input_file hyperLinkDiv" style="display:none;width:250px">
											<div class="fileDiv">
												<input type="text" name="_img_list_square" class="realFile hyperLink" value="<?php echo (strpos($row['p_img_list_square'], '//') !== false?$row['p_img_list_square']:null); ?>" placeholder="http(s)://를 포함하여 입력해주세요." disabled>
											</div>
										</div>
										<?php echo _PhotoForm( '../upfiles/product', '_img_list_square', $row['p_img_list_square'], 'style="width:250px"')?>
										<label class="design">
											<input type="checkbox" class="js_use_hyperlink" name="_use_hyperlink[]" value="_img_list_square">외부 이미지 사용
										</label>
										<?php if(strpos($row['p_img_list_square'], '//') !== false){ ?>
										<script>
											// 외부 이미지 사용시 처리
											(function(){
												$parent = $('.js_use_hyperlink[value=_img_list_square]').attr({'checked':'checked'}).closest('td');
												$parent.find('.input_file:not(.hyperLinkDiv)').hide().find('input').attr({'disabled':'disabled'});
												$parent.find('.input_file.hyperLinkDiv').show().find('input').removeAttr('disabled');
											})();
										</script>
										<?php } ?>
										<?php echo _DescStr("권장사이즈 : {$SkinInfo['product']['list_image_width']} x {$SkinInfo['product']['list_image_height']} (pixel)"); ?>
									</td>
								</tr>
								<?php
									$arr_imgname = array('_img_b1', '_img_b2', '_img_b3', '_img_b4', '_img_b5');
									$_img_idx = 0;
									foreach($arr_imgname as $k=>$v){
										if(!$row['p'.$v] && $k>0) continue;
										$_img_idx++;
								?>
									<tr>
										<th>상세 이미지 <font class="js_img_idx"><?php echo $_img_idx;?></font></th>
										<td>
											<!-- SSJ : 2017-12-12 외부이미지 사용 추가 -->
											<div class="input_file hyperLinkDiv" style="display:none;width:250px">
												<div class="fileDiv">
													<input type="text" name="_img_b<?php echo $_img_idx; ?>" class="realFile hyperLink" value="<?php echo (strpos($row['p'.$v], '//') !== false?$row['p'.$v]:null); ?>" placeholder="http(s)://를 포함하여 입력해주세요." disabled>
												</div>
											</div>
											<?php echo _PhotoForm( '../upfiles/product', '_img_b'.$_img_idx, $row['p'.$v], 'style="width:250px"')?>
											<label class="design">
												<input type="checkbox" class="js_use_hyperlink" name="_use_hyperlink[]" value="_img_b<?php echo $_img_idx; ?>">외부 이미지 사용
											</label>
											<?php if(strpos($row['p'.$v], '//') !== false){ ?>
											<script>
												// 외부 이미지 사용시 처리
												(function(){
													$parent = $('.js_use_hyperlink[value=_img_b<?php echo $_img_idx; ?>]').attr({'checked':'checked'}).closest('td');
													$parent.find('.input_file:not(.hyperLinkDiv)').hide().find('input').attr({'disabled':'disabled'});
													$parent.find('.input_file.hyperLinkDiv').show().find('input').removeAttr('disabled');
												})();
											</script>
											<?php } ?>

											<?php if($k==0){ ?>
											<a href="#none" class="c_btn h27 icon icon_plus_b js_addimg_btn">추가</a>
											<?php }else{ ?>
											<a href="#none" class="c_btn h27 icon icon_minus_b js_delimg_btn">삭제</a>
											<?php } ?>
											<?php echo _DescStr("권장사이즈 : {$SkinInfo['product']['detail_image_width']} x {$SkinInfo['product']['detail_image_height']} (pixel)"); ?>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
						<div class="c_tip auto_area">자동등록 이용 시 개별 이미지는 대표 이미지를 사용하여 자동 리사이즈 됩니다. (썸네일, 리스트 이미지)</div>
					</td>
				</tr>
				<?php if($_mode == 'modify'){ ?>
				<tr>
					<th>상품등록 시간</th>
					<td><?php echo date('Y-m-d', strtotime($row['p_rdate'])); ?> <span class="t_light"><?php echo date('H:i:s', strtotime($row['p_rdate'])); ?></span></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>




	<!-- ● 단락타이틀 -->
	<div class="group_title"><strong>다음, 네이버 EP연동</strong></div>



	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"><col width="*">
			</colgroup>
			<tbody>
				<tr>
					<th>네이버 EP</th>
					<td>
						<?php echo _InputRadio( 'p_naver_switch' , array('Y','N') , $row['p_naver_switch'] ? $row['p_naver_switch'] : "N" , "" , array('적용','미적용') , ""); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="tip_box">
							<?php echo _DescStr("상품에 대한 <em>네이버 EP 노출여부를 설정</em>할 수 있습니다.")?>
							<?php echo _DescStr("네이버 EP 노출은 전체설정(환경설정 > 기본설정 > 쇼핑몰 기본정보 > 네이버 EP), 상품개별설정에서 모두 적용되어야 노출됩니다.")?>
							<?php echo _DescStr("전체상품 DB URL : <em>http://". $system['host'] ."/addons/ep/naver/allep.php</em>")?>
							<?php echo _DescStr("요약EP는 3.0 버전에서는 더이상 사용하지 않습니다.")?>
						</div>
					</td>
				</tr>
				<tr>
					<th>다음 EP</th>
					<td>
						<?php echo _InputRadio( 'p_daum_switch' , array('Y','N') , $row['p_daum_switch'] ? $row['p_daum_switch'] : "N" , "" , array('적용','미적용') , ""); ?>
						<div class="dash_line"><!-- 점선라인 --></div>
						<div class="tip_box">
							<?php echo _DescStr('상품에 대한 <em>다음 하우쇼핑 노출여부를 설정</em>할 수 있습니다.'); ?>
							<?php echo _DescStr("다음 EP 노출은 전체설정(환경설정 > 기본설정 > 쇼핑몰 기본정보 > 다음 EP), 상품개별설정에서 모두 적용되어야 노출됩니다.")?>
							<?php echo _DescStr('전체상품 DB URL : <em>http://'.$system['host'].'/addons/ep/daum/allep.php</em>'); ?>
							<?php echo _DescStr('요약상품 DB URL : <em>http://'.$system['host'].'/addons/ep/daum/briefep.php</em>'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<?php echo _submitBTN('_product.list.php', '', ($_mode == 'modify' ? '<li><a href="#none" class="c_btn h46 black line" id="product_copy">복사하기</a></li>' : null)); ?>

</form>






<script language="javascript">

	// 상품복사
	//$('#product_copy').on('click',function(e){
	$(document).on('click', '#product_copy', function(e){
		e.preventDefault();
		var c = confirm('상품을 복사하시겠습니까?');
		if(c){ location.href='_product.copy.php?pcode=<?=$_code?>&_PVSC=<?php echo $_PVSC; ?>'; }
	});

	// 정산 형태 선택
	var comm_type_check = function() {
		if($('input[name=_commission_type]:checked').val() == '공급가') {
			$('#comSaleTypeTr1').prop('disabled', false);
			$('#comSaleTypeTr2').prop('disabled', true);
		} else {
			$('#comSaleTypeTr1').prop('disabled', true);
			$('#comSaleTypeTr2').prop('disabled', false);
		}
	}
	$(document).on('click', 'input[name=_commission_type]', comm_type_check);
	$(document).ready(comm_type_check);


	// 배송정보 설정
	var delivery_setting = function() {
		if($('._shoppingPay_use:checked').val() == 'Y') {
			$('#_shoppingPay_use_Y').removeAttr('disabled');
		} else {
			$('#_shoppingPay_use_Y').attr('disabled','disabled');
		}
		// ----- JJC : 상품별 배송비 : 2018-08-16 -----
		if($('._shoppingPay_use:checked').val() == 'P') {
			$("input[name='_shoppingPayPdPrice']").removeAttr('disabled');
			$("input[name='_shoppingPayPfPrice']").removeAttr('disabled');
			$(".shoppingPayP").show();
		} else {
			$("input[name='_shoppingPayPdPrice']").attr('disabled','disabled');
			$("input[name='_shoppingPayPfPrice']").attr('disabled','disabled');
			$(".shoppingPayP").hide();
		}
		// ----- JJC : 상품별 배송비 : 2018-08-16 -----
	}
	$(document).on('click', '._shoppingPay_use', delivery_setting);
	$(document).ready(delivery_setting);


	// 쇼핑몰/입점업체 배송비 확인하기
	function entershop_view() {
		var cpid = $('select[name=_cpid]').val();
		if(!cpid) {
			alert('입점업체를 먼저 선택하세요');
		} else {
			<?php
				if($SubAdminMode)	{
					echo "window.open('_entershop.form.php?_mode=modify&menu_idx=16&_id='+cpid);";
				}
				else {
					echo "window.open('_config.delivery.form.php?menu_idx=5');";
				}
			?>
		}
	}

	// 텝메뉴
	$(document).on('click', '.tab_menu', function() {
		$parent = $(this).closest('.data_form');
		var idx = $(this).data('idx');
		// 탭변경
		$parent.find('.tab_menu').closest('li').removeClass('hit');
		$parent.find('.tab_menu[data-idx='+ idx +']').closest('li').addClass('hit');
		// 입력항목변경
		$parent.find('.tab_conts').hide();
		$parent.find('.tab_conts[data-idx='+ idx +']').show();

		// 부모창이 display:none; 일때 높이 오류 수정
		var trigger_cont_editor = $(this).data('trigger')=='Y' ? true : false;
		if(trigger_cont_editor){
			$('.tab_conts[data-idx='+ idx +'] .SEditor').each(function(){
				var id = $(this).attr('id');
				if(oEditors.length > 0){
					oEditors.getById[id].exec('RESIZE_EDITING_AREA_BY',[true]);
				}
			});
			$(this).data('trigger','N');
		}
	});


	// 옵션설정에 따른 노출
	function onoff_option() {
		// 옵션사용하지 않을 경우 옵션유형 모두 닫기
		if($('._option_type_chk:checked').val() == 'nooption') {
			$(".option_type").hide();
		}
		else {
//			$(".option_type").show(); // 옵션유형 div 열기
			$(".init_depth_type").hide(); // 옵션유형 항목 일단 모두 닫기
			if($('._option_type_chk:checked').val() == '1depth') {
				$(".init_depth1_type").show(); // 1차만 열기
			}
			else if($('._option_type_chk:checked').val() == '2depth') {
				$(".init_depth1_type").show(); $(".init_depth2_type").show(); // 1차,2차 열기
			}
			else if($('._option_type_chk:checked').val() == '3depth') {
				$(".init_depth_type").show(); // 모두 열기
			}
		}
	}
	$(document).ready(onoff_option);
	$(document).on('click', '._option_type_chk', onoff_option);


	// 이미지 자동등록/직접등록
	function onoff() {
		if($('.img_auto_resize_use:checked').val() == 'auto') {
			$('.auto_area').show();
			$('.direct_area').hide();
		} else {
			$('.auto_area').hide();
			$('.direct_area').show();
		}
	}
	$(document).ready(onoff);
	$(document).on('click', '.img_auto_resize_use', onoff);


	// KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22
	function switch_coupon(){
		var cp_type = $("[name='_coupon_type']:checked").val();
		var cp_per = $("input[name='_coupon_per']").val();
		var cp_price = $("input[name='_coupon_price']").val();

		$(".cls_coupon_type").hide();
		if(cp_type == "price"){ $(".cls_coupon_type.price").show();}
		if(cp_type == "per"){ $(".cls_coupon_type.per").show(); }
	}
	$(document).ready(switch_coupon);
	$(document).on("change" , "[name='_coupon_type']", switch_coupon );


	// 상세이미지 등록폼 추가 버튼
	$(document).on('click', '.js_addimg_btn', function(){
		if($('.js_img_idx').length>=5){
			alert('상세 이미지는 최대 5개까지만 등록가능합니다.');
			return false;
		}
		var html = '';
			html += '<tr>';
			html += '	<th>상세 이미지 <font class="js_img_idx">0</font></th>';
			html += '	<td>';
			html += '		<div class="input_file hyperLinkDiv" style="display:none;width:250px"><div class="fileDiv">';
			html += '			<input type="text" name="" class="realFile hyperLink" value="" placeholder="http(s)://를 포함하여 입력해주세요." disabled>';
			html += '		</div></div>';
			html += '		<?php echo str_replace(array("\n","\r"), "", addslashes(_PhotoForm( "../upfiles/product", "", "", "style=\"width:250px\""))); ?>';
			html += '		<a href="#none" class="c_btn h27 icon icon_minus_b js_delimg_btn">삭제</a>';
			html += '		<label class="design"><input type="checkbox" class="js_use_hyperlink" name="_use_hyperlink[]" value="">외부 이미지 사용</label>';
			html += '		<?php echo addslashes(_DescStr("권장사이즈 : {$SkinInfo['product']['detail_image_width']} x {$SkinInfo['product']['detail_image_height']} (pixel)")); ?>';
			html += '	</td>';
			html += '</tr>';

		$(this).closest('tbody').append(html);

		rename_img();

	});
	// 상세이미지 등록폼 삭제 버튼
	$(document).on('click', '.js_delimg_btn', function(){
		$(this).closest('tr').remove();

		rename_img();

	});
	//
	function rename_img(){
		var _img_idx = 0;
		$('.js_img_idx').each(function(){
			_img_idx++;
			// 항목명 변경
			$(this).text(_img_idx);
			// input[name]변경 -- realFile
			$(this).closest('tr').find('input[type=file].realFile').attr('name','_img_b'+_img_idx);
			// input[name]변경 -- oldFile
			$(this).closest('tr').find('input[type=hidden].oldFile').attr('name','_img_b'+_img_idx+'_OLD');
			// input[name]변경 -- js_del
			$(this).closest('tr').find('input[type=hidden].js_del').attr('name','_img_b'+_img_idx+'_DEL');
			// value변경 -- js_use_hyperlink
			$(this).closest('tr').find('input[type=checkbox].js_use_hyperlink').val('_img_b' + _img_idx);
			// input[name]변경 -- hyperLink
			$(this).closest('tr').find('input[type=text].hyperLink').attr('name','_img_b'+_img_idx);
		});
	}


	// 옵션창 열기
	function option_popup(pass_code) {
		pass_mode = $('._option_type_chk').filter(function() {if (this.checked) return this;}).val();
		if(pass_mode == 'nooption' || pass_mode == undefined) {
			alert('1차~3차 옵션을 선택하세요');
			return false;
		}
		window.open('_product_option.form.php?pass_mode='+pass_mode+'&pass_code=' + pass_code ,'option','width=1120,height=638,scrollbars=yes');
	}

	// -- 자주쓰는 옵션 열기
	function common_option_popup(pass_code)
	{
		pass_mode = $('._option_type_chk').filter(function() {if (this.checked) return this;}).val();
		if(pass_mode == 'nooption' || pass_mode == undefined) {
			alert('1차~3차 옵션을 선택하세요');
			return false;
		}
		window.open('_product_common_option.pop.php?pass_mode='+pass_mode+'&pass_code=' + pass_code ,'option','width=1120,height=638,scrollbars=yes');
	}

	// 추가옵션창 열기
	function addoption_popup(code) {
		pass_mode = $('._option_type_chk').filter(function() {if (this.checked) return this;}).val();
		if(pass_mode == 'nooption' || pass_mode == undefined) {
			alert('1차~3차 옵션을 선택하세요');
			return false;
		}
		window.open('_product_addoption.popup.php?pass_code=' + code,'addoption','width=1120,height=638,scrollbars=yes');
	}

	// 정보제공고시창 열기
	function reqinfo_popup() {
		window.open('_product_reqinfo.popup.php?pass_code=<?php echo $_code; ?>','reqinfo','width=1120,height=600,scrollbars=yes');
	}

	// 외부 이미지 사용 체크 이벤트
	$(document).on('click', '.js_use_hyperlink', function(){
		$parent = $(this).closest('td');
		var trigger = $(this).is(':checked');
		if(trigger){
			$parent.find('.input_file:not(.hyperLinkDiv)').hide().find('input').attr({'disabled':'disabled'});
			$parent.find('.input_file.hyperLinkDiv').show().find('input').removeAttr('disabled');
		}else{
			$parent.find('.input_file:not(.hyperLinkDiv)').show().find('input').removeAttr('disabled');
			$parent.find('.input_file.hyperLinkDiv').hide().find('input').attr({'disabled':'disabled'});
		}
	});


	// KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22 - Max 검증 시 콤마제거
	jQuery.validator.addMethod("max", function( value, element, param ) {
		value = value.replace(/,/,'')
		return this.optional( element ) || value <= param;
	}, "Please enter a value less than or equal to {0}.");

	// 폼 유효성 검사
	$(document).ready(function(){
		$("form[name=frm]").validate({
				ignore: ".ignore",
				rules: {
						_code: { required: true }
						,_cpid: { required: true }
						,_name: { required: true }
						,_type: { required: true }
						,_price: { required: true , min: { param: 1, depends: function(element) { return ($('input[name=_price]').val().replace(/,/g,'')*1 == 0 ? 1 : 0); } } }
						,_content: { required: true }
						,_content_m: { required: function(){ return ($('input[name=_use_content]').is(':checked') ? false : true);} }
						// -- KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22 ----
						,_coupon_title: { required: function(){ return ($('[name=_coupon_type]:checked').val() != '' ? true : false);} }
						,_coupon_price: {
							required: function(){ return ($('[name=_coupon_type]:checked').val() == 'price' ? true : false); }
							,min:function(){ return ($('[name=_coupon_type]:checked').val() == 'price' ? 1 : false); }
							,max:function(){ return ($('[name=_coupon_type]:checked').val() == 'price' ? ($('input[name="_price"]').val().replace(/,/g,'')*1 - 1) : false); }
						}
						,_coupon_per: {
							required: function(){ return ($('[name=_coupon_type]:checked').val() == 'per' ? true : false); }
							,min:function(){ return ($('[name=_coupon_type]:checked').val() == 'per' ? 0.1 : false); }
							,max:function(){ return ($('[name=_coupon_type]:checked').val() == 'per' ? 99.9 : false); }
						}
						// -- KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22 ----
				},
				invalidHandler: function(event, validator) {
					// 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.
				},
				messages: {
						_code : { required: '상품코드를 입력해주시기 바랍니다.' }
						,_cpid : { required: '입점업체를 선택 해주시기 바랍니다.' }
						,_name : { required: '대표상품명을 입력해주시기 바랍니다.' }
						,_price : { required: '판매가를 입력해주시기 바랍니다.' , min: '판매가를 입력해주시기 바랍니다.'}
						,_content : { required: '상품상세설명(PC)을 입력해주시기 바랍니다.' }
						,_content_m : { required: '상품상세설명(MOBILE)을 입력해주시기 바랍니다.' }
						// -- KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22 ----
						,_coupon_title: { required: '쿠폰명을 입력해주시기 바랍니다.' }
						,_coupon_price: {
							required: '쿠폰 할인금액을 입력해주시기 바랍니다.'
							,min:'쿠폰 할인금액은 0보다 큰 금액으로 입력해주시기 바랍니다.'
							,max: '쿠폰 할인금액은 판매가보다 적은 금액으로 입력해주시기 바랍니다.'
						}
						,_coupon_per: {
							required: '쿠폰 할인율을 입력해주시기 바랍니다.'
							,min:'쿠폰 할인율은 0보다 큰 수를 입력해주시기 바랍니다.'
							,max: '쿠폰 할인율은 100보다 작은 수를 입력해주시기 바랍니다.'
						}
						// -- KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22 ----
				},
				submitHandler : function(form) {
					// 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
					form.submit();
				}

		});
	});

</script>

<?php 
if ($app_mode == "popup") {
    include_once("inc.footer.php");
} else {
    include_once('wrap.footer.php');
}
?>