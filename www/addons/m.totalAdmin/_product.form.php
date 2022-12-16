<?php
// 페이지 표시
$app_current_page_name = "상품관리";
$app_dir = "../../upfiles/product";



include_once('wrap.header.php');

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
	$row['p_sort_group'] = _MQ_result(" select min(p_sort_group) as min from smart_product ");
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

// JJC ::: 브랜드 정보 추출  ::: 2017-11-03
//		basic : 기본정보
//		all : 브랜드 전체 정보
$arr_brand = brand_info('basic');
?>
<form name="frm" action="_product.pro.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
	<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
	<input type="hidden" name="_code" value="<?php echo $_code; ?>">
	<div class="container">
		<div class="data_form">
			<div class="like_table">
				<ul>
					<li class="opt">상품 노출</li>
					<li class="value">
						<?=_InputRadio_totaladmin("_view", array('N', "Y"), ($row['p_view']?$row['p_view']:"Y"), "", array('숨김', "노출") )?>
					</li>
				</ul>
				<ul>
					<li class="opt">상품 아이콘</li>
					<li class="value">
						<?php
							$r2 = $product_icon;
							$pi_uid_array = explode(",",$row[p_icon]);
							foreach($r2 as $k2 => $v2) {
								$checked = @array_search($v2[pi_uid],$pi_uid_array) === false ? NULL : " checked ";
								echo "<label><input type='checkbox' name='_icon[]' value='".$v2[pi_uid]."' ".$checked."><img src='".IMG_DIR_ICON.$v2[pi_img]."' title = '".$v2[pi_title]."'></label>";
							}
						?>
					</li>
				</ul>
				<?php include_once("_product.inc_category_form.php"); ?>
				<ul>
					<li class="opt ess">상품코드</li>
					<li class="value">
						<input type="text" name="" class="input_design" value="<?=$_code?>" readonly />
					</li>
				</ul>
				<ul>
					<li class="opt ess">입점업체</li>
					<li class="value">
						<div class="select">
							<span class="shape"></span>
							<?php echo _InputSelect( "_cpid" , array_keys($arr_customer) , $row['p_cpid'] , "" , array_values($arr_customer) , "-입점업체-"); ?>
						</div>
					</li>
				</ul>
				<ul>
					<li class="opt">브랜드</li>
					<li class="value">
						<div class="select">
							<span class="shape"></span>
							<?php echo _InputSelect( "_brand" , array_keys($arr_brand) , $row['p_brand'] , "" , array_values($arr_brand) , "-브랜드-"); ?>
						</div>
					</li>
				</ul>
				<?php // 2017-06-16 ::: 부가세율설정 ::: JJC ?>
				<?php if($siteInfo['s_vat_product'] == 'C'){ ?>
					<ul>
						<li class="opt ess">과세여부</li>
						<li class="value">
							<label><input type="radio" name="p_vat" value="Y" <?php echo ($row['p_vat'] == "Y" || !$row['p_vat'] ? "checked" : NULL); ?>>과세</label>
							<label><input type="radio" name="p_vat" value="N" <?php echo ($row['p_vat'] == "N" ? "checked" : NULL); ?>>면세</label>
							<?php echo _DescStr_mobile_totaladmin('과세 선택 시 판매가격에 부가세 포함되어 있습니다. 세금계산서와 현금영수증 발행 시 부가세가 포함 됩니다.'); ?>
							<?php echo _DescStr_mobile_totaladmin('면세 선택 시 판매가격에 부가세 포함되어 있지 않습니다. 세금계산서와 현금영수증은 발행되지 않습니다.'); ?>
							<?php echo _DescStr_mobile_totaladmin('카드 결제 시 세금계산서와 현금영수증은 발행되지 않습니다.'); ?>
						</li>
					</ul>
				<?php } ?>
				<?php // 2017-06-16 ::: 부가세율설정 ::: JJC ?>
				<ul>
					<li class="opt ess">정산형태</li>
					<li class="value"><?=_InputRadio_totaladmin("_commission_type", array('공급가', "수수료"), ($row['p_commission_type']?$row['p_commission_type']:"공급가"), " onclick='saleType(this.form)' ", array() )?></li>
				</ul>

				<ul ID="comSaleTypeTr1" style='display:<?=( ($row['p_commission_type'] == "공급가" || !$row['p_commission_type'] ) ?"":"none")?>'>
					<li class="opt ess">공급가격</li>
					<li class="value"><input type="text" name="_sPrice" class="input_design " value="<?=$row['p_sPrice']?>" style="width:200px;" /><span class="txt_back">원</span></li>
				</ul>

				<ul ID="comSaleTypeTr2" style='display:<?=( ($row['p_commission_type'] == "수수료" ) ?"":"none")?>'>
					<li class="opt ess">수수료</li>
					<li class="value"><input type="text" name="_sPersent" class="input_design" placeholder="수수료" style="width:50px;" value="<?=$row['p_sPersent'] ? $row['p_sPersent'] : $siteInfo['s_account_commission'];?>"/><span class="txt_back">%</span></li>
				</ul>
				<ul>
					<li class="opt ess">대표상품명</li>
					<li class="value">
						<input type="text" name="_name" class="input_design" placeholder="대표상품명을 입력하세요." value="<?=$row['p_name']?>" />
					</li>
				</ul>

				<ul>
					<li class="opt ess">상품부제목</li>
					<li class="value">
						<input type="text" name="_subname" class="input_design" placeholder="상품부제목을 입력하세요." value="<?=$row['p_subname']?>" />
					</li>
				</ul>
				<ul>
					<li class="opt">정상가</li>
					<li class="value"><input type="text" name="_screenPrice" class="input_design" value="<?=$row['p_screenPrice']?>" style="width:200px;" /><span class="txt_back">원</span></li>
				</ul>

				<ul>
					<li class="opt ess">판매가</li>
					<li class="value"><input type="text" name="_price" class="input_design" value="<?=$row['p_price']?>" style="width:200px;" /><span class="txt_back">원</span></li>
				</ul>
				<?PHP
				$ex_coupon = explode("|" , $row['p_coupon']);
				?>
				<ul>
					<li class="opt">상품쿠폰</li>
					<li class="value">
						<input type="text" name="_coupon_title" class="input_design" placeholder="쿠폰명" value="<?=$ex_coupon[0]?>" />
						<input type="text" name="_coupon_price" class="input_design" placeholder="쿠폰금액" value="<?=$ex_coupon[1]?>" />
						<?=_DescStr_mobile_totaladmin("쿠폰명 / 쿠폰금액 모두 입력하여야만 적용되며 <u>쿠폰상품 아이콘</u>이 자동으로 붙습니다.")?>
						<?=_DescStr_mobile_totaladmin('쿠폰상품 아이콘은 [PC 통합관리자 &gt; 상품 &gt; 상품관리 &gt; 상품 아이콘 관리]에서 등록/수정하실 수 있습니다.', 'orange'); ?>
					</li>
				</ul>
				<ul>
					<li class="opt">무료배송이벤트 적용여부</li>
					<li class="value">
						<?php echo _InputRadio_totaladmin( "_free_delivery_event_use" , array('Y','N'), ($row['p_free_delivery_event_use'] ? $row['p_free_delivery_event_use'] : "N") , " class='_free_delivery_event_use' " , array('적용','미적용') , ''); ?>
						<?php echo _DescStr_mobile_totaladmin('무료배송이벤트 사용에 따른 상품별 적용여부를 설정할 수 있습니다.'); ?>
					</li>
				</ul>
				<ul>
					<li class="opt">회원등급추가혜택</li>
					<li class="value">
						<?php echo _InputRadio_totaladmin( "_groupset_use" , array('Y','N'), ($row['p_groupset_use'] ? $row['p_groupset_use'] : "N") , " class='_groupset_use' " , array('적용','미적용') , ''); ?>
						<?php echo _DescStr_mobile_totaladmin('회원등급에 따른 추가 혜택에 대한 적용여부를 설정할 수 있습니다.'); ?>
					</li>
				</ul>
				<ul>
					<li class="opt">재고량</li>
					<li class="value"><input type="text" name="_stock" class="input_design" value="<?=isset($row['p_stock']) ? $row['p_stock'] : "10000";?>" style="width:200px;" /><span class="txt_back">개</span></li>
				</ul>
				<ul>
					<li class="opt">상품순위</li>
					<li class="value">
						<input type="text" name="_sort_group" class="input_design" value="<?php echo $row['p_sort_group']; ?>" placeholder="" style="width:50px">
						<input type="hidden" name="_sort_idx" class="design" value="<?php echo $row['p_sort_idx']; ?>" >
						<input type="hidden" name="_idx" class="design" value="<?php echo $row['p_idx']; ?>" >
					</li>
				</ul>
				<ul>
					<li class="opt">판매량</li>
					<li class="value"><input type="text" name="_salecnt" class="input_design" value="<?=isset($row['p_salecnt']) ? $row['p_salecnt'] : "0";?>" style="width:200px;" /><span class="txt_back">개</span></li>
				</ul>
				<ul>
					<li class="opt">적립율</li>
					<li class="value">
						<input type="text" name="_point_per" class="input_design" value="<?=isset($row['p_point_per']) ? $row['p_point_per'] : "0";?>" style="width:50px;" /><span class="txt_back">%</span>
						<?=_DescStr_mobile_totaladmin("상품구매 시 상품가격 당 %로 적용됩니다. 예) 1%적용 시 10,000원 구매하여 결제하면 적립금 100포인트가 적립됩니다.")?>
					</li>
				</ul>
				<ul>
					<li class="opt ess">제조사</li>
					<li class="value">
						<input type="text" name="_maker" class="input_design" placeholder="제조사를 입력하세요." value="<?=$row['p_maker']?>" />
					</li>
				</ul>
				<ul>
					<li class="opt ess">원산지</li>
					<li class="value">
						<input type="text" name="_orgin" class="input_design" placeholder="원산지을 입력하세요." value="<?=$row['p_orgin']?>" />
					</li>
				</ul>
				<ul>
					<li class="opt">배송정보</li>
					<li class="value">
						<input type="text" name="_delivery_info" class="input_design" placeholder="배송정보" value="<?=$row[p_delivery_info]?>" />
						<?=_DescStr_mobile_totaladmin("예 : 로젠택배 (2~3일 소요) ")?>
					</li>
				</ul>
				<ul>
					<li class="opt">배송비 설정</li>
					<li class="value">
						<label><input type="radio" name="_shoppingPay_use" class="_shoppingPay_use" value="N" <?php echo ($row['p_shoppingPay_use'] == 'N' || $row['p_shoppingPay_use'] == '' ? ' checked ' : NULL); ?>><?php echo ($SubAdminMode ? '입점업체' : '쇼핑몰'); ?> 배송비 정책 적용</label>
						<label><input type="radio" name="_shoppingPay_use" class="_shoppingPay_use" value="Y" <?php echo ($row['p_shoppingPay_use'] == 'Y' ? ' checked ' : NULL); ?>>개별 배송비 적용</label>
						<!-- 개별배송비 일때 -->
						<input type="text" name="_shoppingPay" class="input_design" placeholder="" value="<?php echo $row['p_shoppingPay']; ?>" style="width:100px" <?php echo ($row['p_shoppingPay_use'] <> 'Y' ? ' disabled ' : NULL); ?>>
						<span class="txt_back">원</span>
						<label><input type="radio" name="_shoppingPay_use" class="_shoppingPay_use" value="F" <?php echo ($row['p_shoppingPay_use'] == 'F' ? ' checked ' : NULL); ?>>무료 배송 적용</label>
						<!-- 무료배송비 일때 -->
						<?php echo _DescStr_mobile_totaladmin('무료 배송 적용 시 <u>무료배송 아이콘</u>이 자동으로 붙습니다.'); ?>
					</li>
				</ul>
				<? //  EP : DB URL - LCY  ?>
				<ul>
					<li class="opt">네이버 EP</li>
					<li class="value">
						<?=_InputRadio_totaladmin( "p_naver_switch" , array('Y','N') , $row[p_naver_switch] ? $row[p_naver_switch] : "N" , "" , array('적용','미적용') , "")?>
						<?=_DescStr_mobile_totaladmin("상품에 대한 지식 쇼핑 노출여부를 설정할 수 있습니다.")?>
						<?=_DescStr_mobile_totaladmin("네이버 지식 쇼핑 노출은 전체설정(환경설정 > 기본설정 > 네이버 EP), 상품개별설정에서 모두 적용되어야 노출됩니다.")?>
						<?=_DescStr_mobile_totaladmin("전체상품업데이트는 매일 21시 ~ 24시 사이 갱신됩니다. - 전체상품 EP URL : <B>http://". $_SERVER[HTTP_HOST] ."/addons/ep/naver/all.txt</B>")?>
						<?=_DescStr_mobile_totaladmin("요약상품업데이트는 매일 08:30 ~ 20:30 사이, 한시간마다 갱신됩니다. - 요약상품 EP URL : <B>http://". $_SERVER[HTTP_HOST] ."/addons/ep/naver/brief.txt</B>")?>
						<?=_DescStr_mobile_totaladmin("전체EP는 요약EP 가 끝나는 시점에서 생성 되도록  지식쇼핑 관리자 내에서 시간설정을 맞춰 주어야만  정상적으로 상품 노출이 가능합니다.",'orange')?>
					</li>
				</ul>
				<ul>
					<li class="opt">다음 EP</li>
					<li class="value">
						<?=_InputRadio_totaladmin( "p_daum_switch" , array('Y','N') , $row[p_daum_switch] ? $row[p_daum_switch] : "N" , "" , array('적용','미적용') , "")?>
						<?=_DescStr_mobile_totaladmin("상품에 대한 다음 EP 노출여부를 설정할 수 있습니다.")?>
						<?=_DescStr_mobile_totaladmin("다음 하우 쇼핑 노출은 전체설정(환경설정 > 기본설정 > 다음 EP), 상품개별설정에서 모두 적용되어야 노출됩니다.")?>
						<?=_DescStr_mobile_totaladmin("전체상품업데이트는 매일 21시 ~ 24시 사이 갱신됩니다. - 전체상품 EP URL : <B>http://". $_SERVER[HTTP_HOST] ."/addons/ep/daum/all.txt</B>")?>
						<?=_DescStr_mobile_totaladmin("요약상품업데이트는 매일 08:30 ~ 20:30 사이, 한시간마다 갱신됩니다. - 요약상품 EP URL : <B>http://". $_SERVER[HTTP_HOST] ."/addons/ep/daum/brief.txt</B>")?>
						<?=_DescStr_mobile_totaladmin("전체EP는 요약EP 가 끝나는 시점에서 생성 되도록  하우쇼핑 관리자 내에서 시간설정을 맞춰 주어야만  정상적으로 상품 노출이 가능합니다.",'orange')?>
					</li>
				</ul>
				<? //  EP : DB URL - LCY  ?>





				<ul>
					<li class="opt">상품옵션</li>
					<li class="value"><?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?></li>
				</ul>

				<ul>
					<li class="opt">추가옵션</li>
					<li class="value"><?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?></li>
				</ul>

				<ul>
					<li class="opt">정보제공고시</li>
					<li class="value"><?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?></li>
				</ul>

				<ul>
					<li class="opt">관련상품 지정</li>
					<li class="value"><?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?></li>
				</ul>

				<ul>
					<li class="opt">해시태그 지정</li>
					<li class="value"><?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?></li>
				</ul>

				<ul>
					<li class="opt">상품설명</li>
					<li class="value"><?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?></li>
				</ul>
				<ul>
					<li class="opt">상품 이미지</li>
					<li class="value"><?=_DescStr_mobile_totaladmin("PC버전에서 변경가능하십니다." , "orange")?></li>
				</ul>
				<ul>
					<li class="opt">등록시간</li>
					<li class="value"><?php echo date('Y-m-d H:i:s', strtotime($row['p_rdate'])); ?></li>
				</ul>
			</div>
		</div>
	</div>


	<!-- ●●●●●●●●●● 가운데정렬버튼 -->
	<div class="cm_bottom_button">
		<ul>
			<li><span class="button_pack"><input type="submit" class="btn_lg_red" value="수정"></span></li>
			<li><span class="button_pack"><a href="_product.list.php?<?=enc('d' , $_PVSC)?>" class="btn_lg_white">목록으로</a></span></li>
		</ul>
	</div>
	<!-- / 가운데정렬버튼 -->
</form>


<script language="JavaScript" src="./js/_product.js"></script>
<link rel="stylesheet" href="/include/js/jquery/jqueryui/jquery-ui.min.css" type="text/css">
<script>
	$(document).ready(function(){

		// -  validate ---
		$("form[name=frm]").validate({
			ignore: "input[type=text]:hidden",
			rules: {
				_code: { required: true},//상품코드
				_name: { required: true},//대표상품명
				_screenPrice: { required: true},//기존가격
				_price: { required: true}//할인판매가
			},
			messages: {
				_code: { required: "상품코드를 입력하시기 바랍니다."},//상품코드
				_name: { required: "대표상품명을 입력하시기 바랍니다."},//대표상품명
				_screenPrice: { required: "기존가격을 입력하시기 바랍니다."},//기존가격
				_price: { required: "할인판매가을 입력하시기 바랍니다."}//할인판매가
			},
			submitHandler : function(form) {
				form.submit();
			}
		});
		// - validate ---
	});

</script>

<?php include_once('wrap.footer.php'); ?>