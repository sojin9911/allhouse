<?php
# LDD014
# Error Reproting level modify
error_reporting(E_ALL ^ E_NOTICE);

// 메뉴 고정
$app_current_link = '_product.list.php';
include_once('wrap.header.php');


# 첨부파일 확인
if($_FILES['excel_file']['size'] <= 0) error_loc_msg("_product.list.php", "첨부파일이 없습니다.");

# Excel Class Load
include_once(OD_ADDONS_ROOT.'/excelAddon/loader.php');
$Excel = ExcelLoader($_FILES['excel_file']['tmp_name']);


# -- 1차 카테고리 배열 적용 ---
$arr_parent01 = array();
$cres = _MQ_assoc("select `c_uid`, if(`c_view`='Y',`c_name`,concat(`c_name`,' [숨김]')) as `c_name` from `smart_category` where `c_depth` = '1' order by `c_idx` asc ");
foreach( $cres as $k=>$v ){
	$arr_parent01[$v['c_uid']] = $v['c_name'];
}

# -- 2차 카테고리 배열 적용 ---
$arr_parent02 = array();
$cres = _MQ_assoc("select `c_uid`, `c_name`, `c_parent` from `smart_category` where `c_view`='Y' and `c_depth` = '2' order by `c_idx` asc ");
foreach( $cres as $k=>$v ){
	$arr_parent02[$v['c_parent']][$v['c_uid']] = $v['c_name'];
}

# -- 3차 카테고리 배열 적용 ---
$arr_parent03 = array();
$cres = _MQ_assoc("select `c_uid`, `c_name`, `c_parent` from `smart_category` where `c_view`='Y' and `c_depth` = '3' order by `c_idx` asc ");
foreach( $cres as $k=>$v ){
	$ex = explode(',' , $v['c_parent']);
	$arr_parent03[$ex[0]][$ex[1]][$v['c_uid']] = $v['c_name'];
}

// JJC ::: 브랜드 정보 추출  ::: 2017-11-03
//		basic : 기본정보
//		all : 브랜드 전체 정보
$arr_brand = brand_info('basic');
$arr_brand_trans = array_flip($arr_brand);

// th 생성
function add_table_th($title, $style='') {
	return '<th scope="col"'.(trim($style) != ''?' style="'.$style.'"':null).'>'.$title.'</th>';
}

// 항목명으로 키값 추출
$arr_key = array();
// 항목명 필수체크
$arr_required = array();

// 2019-05-02 SSJ :: 엑셀 다운로드 항목 설정
// --- _product.download.php, _product.upload.php 에서 동일하게 사용 : 배열 수정 시 2개 파일 동일하게 수정
$th = array(
	'상품코드<br>(신규등록시 생략)'=>array(
		'key'=>'p_code',
		'required'=>'Y',
		'width'=>'210'
	),
	'대표상품명'=>array(
		'key'=>'p_name',
		'required'=>'Y',
		'width'=>'195'
	),
	'상품부제목'=>array(
		'key'=>'p_subname',
		'required'=>'N',
		'width'=>'195'
	),
	'1차 분류'=>array(
		'key'=>'catename_1',
		'required'=>'Y',
		'width'=>'320',
		'title'=>'카테고리' // 업로드 시 타이틀
	),
	'2차 분류'=>array(
		'key'=>'catename_2',
		'required'=>'Y',
		'width'=>'0',
		'hide'=>'Y' // 업로드 시 타이틀 비노출
	),
	'3차 분류'=>array(
		'key'=>'catename_3',
		'required'=>'Y',
		'width'=>'0',
		'hide'=>'Y' // 업로드 시 타이틀 비노출
	),
	'노출여부(Y, N)'=>array(
		'key'=>'p_view',
		'required'=>'Y',
		'width'=>'90',
	),
	'정산형태<br>(공급가, 수수료)'=>array(
		'key'=>'p_commission_type',
		'required'=>'Y',
		'width'=>'90',
	),
	'공급가(원)'=>array(
		'key'=>'p_sPrice',
		'required'=>'N',
		'width'=>'90'
	),
	'수수료(%)'=>array(
		'key'=>'p_sPersent',
		'required'=>'N',
		'width'=>'90'
	),
	'기존가격'=>array(
		'key'=>'p_screenPrice',
		'required'=>'N',
		'width'=>'90'
	),
	'할인판매가'=>array(
		'key'=>'p_price',
		'required'=>'Y',
		'width'=>'90'
	),
	'브랜드'=>array(
		'key'=>'p_brand',
		'required'=>'N',
		'width'=>'140'
	),
	'과세여부(Y, N)'=>array(
		'key'=>'p_vat',
		'required'=>'Y',
		'width'=>'90'
	),
	'재고량'=>array(
		'key'=>'p_stock',
		'required'=>'Y',
		'width'=>'60'
	),
	'상품순위'=>array(
		'key'=>'p_sort_group', // SSJ : 상품순위 항목 p_idx=>p_sort_group 으로 변경 : 2021-02-17
		'required'=>'N',
		'width'=>'60'
	),
	'원산지'=>array(
		'key'=>'p_orgin',
		'required'=>'N',
		'width'=>'130'
	),
	'제조사'=>array(
		'key'=>'p_maker',
		'required'=>'N',
		'width'=>'130'
	),
	'적립율(%)'=>array(
		'key'=>'p_point_per',
		'required'=>'N',
		'width'=>'60'
	),
	// -- KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22 ----
	'상품쿠폰타입<br>(할인금액, 할인율)'=>array(
		'key'=>'p_coupon_type',
		'required'=>'N',
		'width'=>'120'
	),
	'상품쿠폰명'=>array(
		'key'=>'p_coupon_title',
		'required'=>'N',
		'width'=>'195'
	),
	'상품쿠폰 할인금액(원)<br>(할인금액)'=>array(
		'key'=>'p_coupon_price',
		'required'=>'N',
		'width'=>'123'
	),
	'상품쿠폰 할인율(%)<br>(할인율)'=>array(
		'key'=>'p_coupon_per',
		'required'=>'N',
		'width'=>'123'
	),
	'상품쿠폰 최대 할인금액(원)<br>(할인율)'=>array(
		'key'=>'p_coupon_max',
		'required'=>'N',
		'width'=>'140'
	),
	// -- KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22 ----
	'배송정보'=>array(
		'key'=>'p_delivery_info',
		'required'=>'N',
		'width'=>'195'
	),
	'배송처리<br>(기본, 상품별배송, 개별배송, 무료배송)'=>array(
		'key'=>'p_shoppingPay_use',
		'required'=>'Y',
		'width'=>'210'
	),
	'개별배송 - 배송비'=>array(
		'key'=>'p_shoppingPay',
		'required'=>'N',
		'width'=>'120'
	),
	'상품별배송 - 배송비<br>(기본배송비)'=>array(
		'key'=>'p_shoppingPayPdPrice',
		'required'=>'N',
		'width'=>'120'
	),
	'상품별배송 - 배송비<br>(무료배송비)'=>array(
		'key'=>'p_shoppingPayPfPrice',
		'required'=>'N',
		'width'=>'120'
	),
	'무료배송이벤트 적용여부<br>(적용,미적용)'=>array(
		'key'=>'p_free_delivery_event_use',
		'required'=>'N',
		'width'=>'150'
	),
	'회원등급추가혜택<br>(적용,미적용)'=>array(
		'key'=>'p_groupset_use',
		'required'=>'N',
		'width'=>'150'
	),
	'관련상품 적용방식<br>(사용안함, 자동지정, 수동지정)'=>array(
		'key'=>'p_relation_type',
		'required'=>'N',
		'width'=>'180'
	),
	'관련상품 상품코드<br>(수동지정시 상품코드를|로 구분하여 기입)'=>array(
		'key'=>'p_relation',
		'required'=>'N',
		'width'=>'310'
	),
	'상품설명 - PC<br>(엔터제외)'=>array(
		'key'=>'p_content',
		'required'=>'Y',
		'width'=>'310'
	),
	'상품설명 - 모바일<br>(엔터제외)'=>array(
		'key'=>'p_content_m',
		'required'=>'N',
		'width'=>'310'
	),
	'목록이미지'=>array(
		'key'=>'p_img_list_square',
		'required'=>'N',
		'width'=>'195'
	),
	'상세이미지1'=>array(
		'key'=>'p_img_b1',
		'required'=>'N',
		'width'=>'195'
	),
	'상세이미지2'=>array(
		'key'=>'p_img_b2',
		'required'=>'N',
		'width'=>'195'
	),
	'상세이미지3'=>array(
		'key'=>'p_img_b3',
		'required'=>'N',
		'width'=>'195'
	),
	'상세이미지4'=>array(
		'key'=>'p_img_b4',
		'required'=>'N',
		'width'=>'195'
	),
	'상세이미지5'=>array(
		'key'=>'p_img_b5',
		'type'=> 'N',
		'width'=>'195'
	)
);

// -- LCY 2017-11-09 -- 입점업체 패치
if( $SubAdminMode === true){ $th['입점업체'] = array('key'=>'p_cpid','type'=>'N', 'width'=>'150'); }


// 총 넓이 추출
$app_total_width = 204;
foreach($th as $k=>$v) {
	$app_total_width += $v['width']*1 + 22;
}

// -- LCY 2017-11-09 -- 입점업체 패치
$arr_customer = arr_company();
?>
<script>
//ctrl+N , ctrl+R , F5 차단
function doNotReload(){

	if((event.ctrlKey == true && (event.keyCode == 78 || event.keyCode == 82)) || (event.keyCode == 116) ) {

		alert('해당페이지에서 새로고침을 할 수 없습니다.');
		event.keyCode = 0;
		event.cancelBubble = true;
		event.returnValue = false;
	}
}
document.onkeydown = doNotReload;
</script>

<form action="_product.upload.pro.php" method="post">

	<div class="group_title">
		<strong>일괄업로드</strong>
		<!-- 해당페이지의 등록/업로드 버튼 있을 경우 -->
		<div class="btn_box">
			<span class="c_btn h46 red"><input type="submit" name="" value="등록처리" /></span>
			<a href="_product.list.php" class="c_btn h46 black line">돌아가기</a>
		</div>
	</div>

	<div class="data_form">
		<table class="table_form">
			<tbody>
				<tr>
					<td>
						<div class="tip_box">
							<?php echo _DescStr("처리 수에 따라 다소시간이 걸릴 수 있습니다."); ?>
							<?php echo _DescStr("해당 페이지에서 <em>등록처리</em>버튼을 눌러 저장 하지 않으면 등록되지 않습니다."); ?>
							<?php echo _DescStr("해당 페이지에서 <em>새로고침</em>을 할 경우 문제가 생길 수 있습니다."); ?>
							<?php echo _DescStr("수정되는 상품의 분류(카테고리) 엑셀 데이터는 무시됩니다."); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


	<div class="data_list clear_both">


		<!-- 엑셀내용 {-->
		<div style="overflow-x:scroll; width:100%; height:500px;" class="new_product_upload">

			<table class="table_list" style="width:<?php echo $app_total_width; ?>px">
				<thead>
					<tr>
						<th scope="col" style="width:80px;">등록구분</th>
						<?php
						foreach($th as $k=>$v) {
							if($v['hide'] == 'Y') continue; // 항목 비노출 체크
							$_title = $v['title'] ? $v['title'] : $k;
							echo add_table_th($_title, 'width:'.($v['width']*1).'px');
							//echo add_table_th($_title, '');
						}
						?>
						<th scope="col" style="width:80px">관리</th>
					</tr>
				</thead>
				<tbody>
					<?php
					// 필수배열 th에서 추출
					$idx = 0;
					$tmp_th = array();
					foreach($th as $kk=>$vv){
						$tmp_th[strip_tags($kk)] = $th[$kk];
						$arr_required[$vv['key']] = $vv['required'];
						$arr_key[$idx] = $vv['key'];
						$idx++;
					}
					$th = $tmp_th;

					foreach($Excel as $key=>$val) {
						if($key < 2) continue; // 파일정보와 헤더는 제외
						else foreach($arr_key as $kk=>$vv) $val[$vv] = $val[$kk];

						if($val['p_code']) {
							$code = $val['p_code'];
							$ActionType = 'm';
						}
						else {
							## 코드 생성.
							$code = shop_productcode_create();
							$ActionType = 'a';
						}
					?>
					<tr>
						<td>
							<input type="hidden" name="code[]" value="<?php echo $code; ?>">
							<input type="hidden" name="mode[<?php echo $code; ?>]" value="<?php echo $ActionType; ?>">
							<div class="lineup-vertical">
								<?php if($ActionType == 'm') { ?>
									<span class="c_tag blue h18 blue line">수정</span>
								<?php } else { ?>
									<span class="c_tag red h18 ">추가</span>
								<?php } ?>
							</div>
						</td>
						<?php
							foreach($th as $k=>$v) {
								if($v['hide'] == 'Y') continue; // 항목 비노출 체크
						?>

							<?php if($v['key'] == 'p_code'){ ?>
								<td>
									<?php echo $code; ?>
								</td>

							<?php }else if($v['key'] == 'p_name'){ ?>
								<td>
									<input type="text" name="_name[<?php echo $code; ?>]" class="design" value="<?php echo $val['p_name']; ?>" <?php echo ($arr_required['p_name'] == 'Y' ? ' required ' : null); ?>>
								</td>

							<?php }else if($v['key'] == 'p_subname'){ ?>
								<td>
									<input type="text" name="_subname[<?php echo $code; ?>]" class="design" value="<?php echo $val['p_subname']; ?>" <?php echo ($arr_required['p_subname'] == 'Y' ? ' required ' : null); ?>>
								</td>

							<?php }else if($v['key'] == 'catename_1'){ ?>
								<td>
									<?php

										// 공란제거
										$val['catename_1'] = trim($val['catename_1']);
										$val['catename_2'] = trim($val['catename_2']);
										$val['catename_3'] = trim($val['catename_3']);

										// 수정 상품일 경우 카테고리 정보 추출
										// 3차까지 있는 경우
										if($val['catename_1'] && $val['catename_2'] && $val['catename_3']) {
											$cateLoad = _MQ("
															select
																c3.c_uid as cc3,
																c2.c_uid as cc2,
																c1.c_uid as cc1
															from smart_category as c3
															left join smart_category as c2 on (substring_index(c3.c_parent , ',' ,-1) = c2.c_uid and c2.c_depth = 2 and c2.c_name = '{$val['catename_2']}' )
															left join smart_category as c1 on (substring_index(c3.c_parent , ',' ,1) = c1.c_uid and c1.c_depth = 1 and c1.c_name = '{$val['catename_1']}')
															where
																c1.c_uid is not null and
																c2.c_uid is not null and
																c1.c_name = '{$val['catename_1']}' and c2.c_name = '{$val['catename_2']}' and c3.c_name = '{$val['catename_3']}' and
																c3.c_depth = 3
														");
										}

										// 2차까지 있는 경우
										else if($val['catename_1'] && $val['catename_2']) {
											$cateLoad = _MQ("
															select
																c2.c_uid as cc2,
																c1.c_uid as cc1
															from smart_category as c2
															left join smart_category as c1 on ( c2.c_parent = c1.c_uid and c1.c_depth = 1 and c1.c_name = '{$val['catename_1']}')
															where
																c1.c_uid is not null and
																c1.c_name = '{$val['catename_1']}' and c2.c_name = '{$val['catename_2']}' and
																c2.c_depth = 2
														");
										}

										// 1차까지 있는 경우
										else if($val['catename_1'] ) {
											$cateLoad = _MQ("
															select
																c1.c_uid as cc1
															from smart_category as c1
															where
																c1.c_uid is not null and
																c1.c_name = '{$val['catename_1']}'  and
																c1.c_depth = 1
														");
										}
										$uniqid = uniqid();
									?>
									<input type="hidden" name="catecode[<?php echo $code; ?>]" class="catecode_<?php echo $uniqid; ?>" value="<?php echo $uniqid; ?>">
									<div class="">
										<span class="fr_tx">1차 분류</span>
										<?php echo _InputSelect( "pass_cate01_".$uniqid, array_keys($arr_parent01) , $cateLoad['cc1'] , "onchange=\"category_select_upload(1, '{$uniqid}', '".($cateLoad['cc2']?$cateLoad['cc2']:'')."');\" " , array_values($arr_parent01) , "-선택-"); ?>
									</div>

									<div class="clear_both">
										<span class="fr_tx">2차 분류</span>
										<?php echo _InputSelect( "pass_cate02_".$uniqid, (IS_ARRAY($arr_parent02[$cateLoad['cc1']]) ? array_keys($arr_parent02[$cateLoad['cc1']]) : array()) , $cateLoad['cc2'] , "onchange=\"category_select_upload(2, '{$uniqid}', '".($cateLoad['cc3']?$cateLoad['cc3']:'')."');\" " , (IS_ARRAY($arr_parent02[$cateLoad['cc1']]) ? array_values($arr_parent02[$cateLoad['cc1']]) : array()) , "-선택-"); ?>
									</div>

									<div class="clear_both">
										<span class="fr_tx">3차 분류</span>
										<?php echo _InputSelect( "pass_cate03_".$uniqid, (IS_ARRAY($arr_parent03[$cateLoad['cc1']][$cateLoad['cc2']]) ? array_keys($arr_parent03[$cateLoad['cc1']][$cateLoad['cc2']]) : array()) , $cateLoad['cc3'] , "  " , (IS_ARRAY($arr_parent03[$cateLoad['cc1']][$cateLoad['cc2']]) ? array_values($arr_parent03[$cateLoad['cc1']][$cateLoad['cc2']]) : array()) , "-선택-"); ?>
									</div>
								</td>

							<?php }else if($v['key'] == 'p_view'){ ?>
								<td>
									<?php echo _InputSelect("_view[{$code}]", array('Y', 'N') , $val['p_view'], ($arr_required['p_view'] == 'Y' ? ' required ' : null) , array('노출(Y)', '숨김(N)') , "-선택-"); ?>
								</td>

							<?php }else if($v['key'] == 'p_commission_type'){ ?>
								<td>
									<?php echo _InputSelect("_commission_type[{$code}]", array('공급가', '수수료') , $val['p_commission_type'], ($arr_required['p_commission_type'] == 'Y' ? ' required ' : null) , array('공급가', '수수료') , "-선택-"); ?>
								</td>

							<?php }else if($v['key'] == 'p_sPrice'){ ?>
								<td>
									<input type="text" name="_sPrice[<?php echo $code; ?>]" value="<?php echo $val['p_sPrice']; ?>" class="design number_style" <?php echo ($arr_required['p_sPrice'] == 'Y' ? ' required ' : null); ?> style="width:80px;">
								</td>

							<?php }else if($v['key'] == 'p_sPersent'){ ?>
								<td>
									<input type="text" name="_sPersent[<?php echo $code; ?>]" value="<?php echo $val['p_sPersent']; ?>" class="design number_style" <?php echo ($arr_required['p_sPersent'] == 'Y' ? ' required ' : null); ?> style="width:80px;">
								</td>

							<?php }else if($v['key'] == 'p_screenPrice'){ ?>
								<td>
									<input type="text" name="_screenPrice[<?php echo $code; ?>]" value="<?php echo $val['p_screenPrice']; ?>" class="design number_style" <?php echo ($arr_required['p_screenPrice'] == 'Y' ? ' required ' : null); ?> style="width:80px;">
								</td>

							<?php }else if($v['key'] == 'p_price'){ ?>
								<td>
									<input type="text" name="_price[<?php echo $code; ?>]" value="<?php echo $val['p_price']; ?>" class="design number_style" <?php echo ($arr_required['p_price'] == 'Y' ? ' required ' : null); ?> style="width:80px;">
								</td>

							<?php }else if($v['key'] == 'p_brand'){ ?>
								<td>
									<?php echo _InputSelect( "_brand[{$code}]" , array_keys($arr_brand) , $arr_brand_trans[$val['p_brand']] , ($arr_required['p_brand'] == 'Y' ? ' required ' : null) , array_values($arr_brand) , "-선택-"); ?>
								</td>

							<?php }else if($v['key'] == 'p_vat'){ ?>
								<td>
									<?php echo _InputSelect("_vat[{$code}]", array('Y', 'N') , $val['p_vat'], ($arr_required['p_vat'] == 'Y' ? ' required ' : null) , array('과세(Y)', '면세(N)') , "-선택-"); ?>
								</td>

							<?php }else if($v['key'] == 'p_stock'){ ?>
								<td>
									<input type="text" name="_stock[<?php echo $code; ?>]" value="<?php echo $val['p_stock']; ?>" class="design number_style" <?php echo ($arr_required['p_stock'] == 'Y' ? ' required ' : null); ?> style="width:50px">
								</td>

							<?php }else if($v['key'] == 'p_sort_group'){ // SSJ : 상품순위 항목 p_idx=>p_sort_group 으로 변경 : 2021-02-17 ?>
								<td>
									<input type="text" name="_sort_group[<?php echo $code; ?>]" value="<?php echo $val['p_sort_group']; ?>" class="design number_style" style="width:50px" <?php echo ($arr_required['p_sort_group'] == 'Y' ? ' required ' : null); ?>>
								</td>

							<?php }else if($v['key'] == 'p_orgin'){ ?>
								<td>
									<input type="text" name="_orgin[<?php echo $code; ?>]" value="<?php echo $val['p_orgin']; ?>" class="design" style="width:120px;" <?php echo ($arr_required['p_orgin'] == 'Y' ? ' required ' : null); ?>>
								</td>

							<?php }else if($v['key'] == 'p_maker'){ ?>
								<td>
									<input type="text" name="_maker[<?php echo $code; ?>]" value="<?php echo $val['p_maker']; ?>" class="design" style="width:120px;" <?php echo ($arr_required['p_maker'] == 'Y' ? ' required ' : null); ?>>
								</td>

							<?php }else if($v['key'] == 'p_point_per'){ ?>
								<td>
									<input type="text" name="_point_per[<?php echo $code; ?>]" value="<?php echo $val['p_point_per']; ?>" class="design" style="width:50px" <?php echo ($arr_required['p_point_per'] == 'Y' ? ' required ' : null); ?>>
								</td>

							<?php }else if($v['key'] == 'p_coupon_title'){ ?>
								<td>
									<input type="text" name="_coupon_title[<?php echo $code; ?>]" value="<?php echo $val['p_coupon_title']; ?>" class="design" <?php echo ($arr_required['p_coupon_title'] == 'Y' ? ' required ' : null); ?>>
								</td>

							<?php }else if($v['key'] == 'p_coupon_price'){ ?>
								<td>
									<input type="text" name="_coupon_price[<?php echo $code; ?>]" value="<?php echo $val['p_coupon_price']; ?>" class="design number_style" <?php echo ($arr_required['p_coupon_price'] == 'Y' ? ' required ' : null); ?> style="width:80px;">
								</td>

							<?php }else if($v['key'] == 'p_coupon_type'){ // KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22 ?>
								<td>
									<?php
										if(trim($val['p_coupon_type']) == '할인금액'){ $val['p_coupon_type'] = 'price'; }
										else if(trim($val['p_coupon_type']) == '할인율'){ $val['p_coupon_type'] = 'per'; }
										echo _InputSelect("_coupon_type[{$code}]", array('price', 'per') , $val['p_coupon_type'], ($arr_required['p_coupon_type'] == 'Y' ? ' required ' : null) , array('할인금액', '할인율') , "-선택-");
									?>
								</td>

							<?php }else if($v['key'] == 'p_coupon_per'){ // KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22 ?>
								<td>
									<input type="text" name="_coupon_per[<?php echo $code; ?>]" value="<?php echo number_format(floor($val['p_coupon_per']*10)/10,1); ?>" class="design" <?php echo ($arr_required['p_coupon_per'] == 'Y' ? ' required ' : null); ?> style="width:80px;">
								</td>

							<?php }else if($v['key'] == 'p_coupon_max'){ // KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22 ?>
								<td>
									<input type="text" name="_coupon_max[<?php echo $code; ?>]" value="<?php echo $val['p_coupon_max']; ?>" class="design number_style" <?php echo ($arr_required['p_coupon_max'] == 'Y' ? ' required ' : null); ?> style="width:80px;">
								</td>

							<?php }else if($v['key'] == 'p_delivery_info'){ ?>
								<td>
									<input type="text" name="_delivery_info[<?php echo $code; ?>]" value="<?php echo $val['p_delivery_info']; ?>" class="design" <?php echo ($arr_required['p_delivery_info'] == 'Y' ? ' required ' : null); ?>>
								</td>

							<?php }else if($v['key'] == 'p_shoppingPay_use'){ ?>
								<td>
									<?php
										if(!$arr_shoppingPay) $arr_shoppingPay = array('기본'=>'N', '상품별배송'=>'P', '개별배송'=>'Y', '무료배송'=>'F');
										echo _InputSelect("_shoppingPay_use[{$code}]", array_values($arr_shoppingPay) , ($arr_shoppingPay[$val['p_shoppingPay_use']]?$arr_shoppingPay[$val['p_shoppingPay_use']]:'N'), ($arr_required['p_shoppingPay_use'] == 'Y' ? ' required ' : null) , array_keys($arr_shoppingPay) , "-선택-");
									?>
								</td>

							<?php }else if($v['key'] == 'p_shoppingPayFree'){ ?>
								<td>
									<input type="text" name="_shoppingPayFree[<?php echo $code; ?>]" value="<?php echo $val['p_shoppingPayFree']; ?>" class="design number_style" style="width:80px;" <?php echo ($arr_required['p_shoppingPay_use'] == 'Y' ? ' required ' : null); ?>>
								</td>

							<?php }else if($v['key'] == 'p_shoppingPay'){ ?>
								<td>
									<input type="text" name="_shoppingPay[<?php echo $code; ?>]" value="<?php echo $val['p_shoppingPay']; ?>" class="design number_style" style="width:80px;" <?php echo ($arr_required['p_shoppingPay'] == 'Y' ? ' required ' : null); ?>>
								</td>

							<?php }else if($v['key'] == 'p_relation_type'){ ?>
								<td>
									<?php
										if(!$arr_relation_type) $arr_relation_type = array('사용안함'=>'none', '자동지정'=>'category', '수동지정'=>'manual');
										echo _InputSelect("_relation_type[{$code}]", array_values($arr_relation_type) , ($arr_relation_type[$val['p_relation_type']] ? $arr_relation_type[$val['p_relation_type']] : 'none'), ($arr_required['p_relation_type'] == 'Y' ? ' required ' : null) , array_keys($arr_relation_type) , "-선택-");
									?>
								</td>

							<?php }else if($v['key'] == 'p_relation'){ ?>
								<td>
									<textarea rows="10" style="width:300px" name="_relation[<?php echo $code; ?>]" class="design" <?php echo ($arr_required['p_relation'] == 'Y' ? ' required ' : null); ?>><?php echo $val['p_relation']; ?></textarea>
								</td>

							<?php }else if($v['key'] == 'p_content'){ ?>
								<td>
									<textarea rows="10" style="width:300px" name="_content[<?php echo $code; ?>]" class="design" <?php echo ($arr_required['p_content'] == 'Y' ? ' required ' : null); ?>><?php echo $val['p_content']; ?></textarea>
								</td>

							<?php }else if($v['key'] == 'p_content_m'){ ?>
								<td>
									<textarea rows="10" style="width:300px" name="_content_m[<?php echo $code; ?>]" class="design" <?php echo ($arr_required['p_content_m'] == 'Y' ? ' required ' : null); ?>><?php echo $val['p_content_m']; ?></textarea>
								</td>

							<?php }else if($v['key'] == 'p_img_list_square'){ ?>
								<td>
									<input type="text" name="_img_list_square[<?php echo $code; ?>]" value="<?php echo $val['p_img_list_square']; ?>" class="design" <?php echo ($arr_required['p_img_list_square'] == 'Y' ? ' required ' : null); ?>>
								</td>

							<?php }else if($v['key'] == 'p_img_b1'){ ?>
								<td>
									<input type="text" name="p_img_b1[<?php echo $code; ?>]" value="<?php echo $val['p_img_b1']; ?>" class="design" <?php echo ($arr_required['p_img_b1'] == 'Y' ? ' required ' : null); ?>>
								</td>

							<?php }else if($v['key'] == 'p_img_b2'){ ?>
								<td>
									<input type="text" name="p_img_b2[<?php echo $code; ?>]" value="<?php echo $val['p_img_b2']; ?>" class="design" <?php echo ($arr_required['p_img_b2'] == 'Y' ? ' required ' : null); ?>>
								</td>

							<?php }else if($v['key'] == 'p_img_b3'){ ?>
								<td>
									<input type="text" name="p_img_b3[<?php echo $code; ?>]" value="<?php echo $val['p_img_b3']; ?>" class="design" <?php echo ($arr_required['p_img_b3'] == 'Y' ? ' required ' : null); ?>>
								</td>

							<?php }else if($v['key'] == 'p_img_b4'){ ?>
								<td>
									<input type="text" name="p_img_b4[<?php echo $code; ?>]" value="<?php echo $val['p_img_b4']; ?>" class="design" <?php echo ($arr_required['p_img_b4'] == 'Y' ? ' required ' : null); ?>>
								</td>

							<?php }else if($v['key'] == 'p_img_b5'){ ?>
								<td>
									<input type="text" name="p_img_b5[<?php echo $code; ?>]" value="<?php echo $val['p_img_b5']; ?>" class="design" <?php echo ($arr_required['p_img_b5'] == 'Y' ? ' required ' : null); ?>>
								</td>

							<?php }else if($v['key'] == 'p_free_delivery_event_use'){ ?>
								<td>
									<?=_InputRadio( "_free_delivery_event_use[".$code."]" , array('Y','N') , (trim($val['p_free_delivery_event_use'])=='적용'?'Y':'N') , ($arr_required['p_free_delivery_event_use'] == 'Y' ? ' required ' : null) , array('적용','미적용'))?>
								</td>

							<?php }else if($v['key'] == 'p_groupset_use'){ ?>
								<td>
									<?=_InputRadio( "_groupset_use[".$code."]" , array('Y','N') , (trim($val['p_groupset_use'])=='적용'?'Y':'N') , ($arr_required['p_groupset_use'] == 'Y' ? ' required ' : null) , array('적용','미적용'))?>
								</td>

							<?php }else if($v['key'] == 'p_shoppingPayPfPrice'){ ?>
								<td>
									<input type="text" name="_shoppingPayPfPrice[<?php echo $code; ?>]" value="<?php echo $val['p_shoppingPayPfPrice']; ?>" class="design number_style" style="width:80px;" <?php echo ($arr_required['p_shoppingPayPfPrice'] == 'Y' ? ' required ' : null); ?>>
								</td>

							<?php }else if($v['key'] == 'p_shoppingPayPdPrice'){ ?>
								<td>
									<input type="text" name="_shoppingPayPdPrice[<?php echo $code; ?>]" value="<?php echo $val['p_shoppingPayPdPrice']; ?>" class="design number_style" style="width:80px;" <?php echo ($arr_required['p_shoppingPayPdPrice'] == 'Y' ? ' required ' : null); ?>>
								</td>

							<?php }else if($v['key'] == 'p_cpid'){ ?>
								<td class="conts">
									<?=_InputSelect( "p_cpid[".$code."]" , array_keys($arr_customer) , $val['p_cpid'] , ($arr_required['p_cpid'] == 'Y' ? ' required ' : null) , array_values($arr_customer) , "-입점업체-")?>
								</td>
							<?php } ?>

						<?php } ?>


						<td>
							<div class="lineup-vertical">
								<a href="#none" onclick="$(this).closest('tr').remove();" class="c_btn h22 gray t4">삭제</a>
							</div>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<!--} 엑셀내용 -->

	</div>

	<!-- <div class="c_btnbox">
		<ul>
			<li><span class="c_btn h46 red"><input type="submit" name="" value="업로드 저장" /></span></li>
			<li><a href="_product.list.php" class="c_btn h46 black line">목록</a></li>
		</ul>
	</div> -->

</form>
<script>
function category_select_upload(_idx, uniqid, val) {
	if(_idx == 2) {
		$("select[name=pass_cate03_"+uniqid+"]").find("option").remove().end().append('<option value="">-선택-</option>');
	}
	else if(_idx == 1) {
		$("select[name=pass_cate02_"+uniqid+"]").find("option").remove().end().append('<option value="">-선택-</option>');
		$("select[name=pass_cate03_"+uniqid+"]").find("option").remove().end().append('<option value="">-선택-</option>');
	}

    $.ajax({
        url: "../../program/categorysearch.pro.php",
		cache: false,
		dataType: "json",
		type: "POST",
        data: "pass_parent03_no_required=<?php echo $pass_cate03_no_required; ?>&pass_parent01=" + $("[name=pass_cate01_"+uniqid+"]").val() + "&pass_parent02=" + $("[name=pass_cate02_"+uniqid+"]").val()+"&pass_idx=" + _idx,
        success: function(data){
            if(_idx == 2) {
				var option_str = '';
				for (var i = 0; i < data.length; i++) {
					option_str += '<option value="' + data[i].optionValue + '" '+(data[i].optionValue == val?' selected':'')+'>' + data[i].optionDisplay + '</option>';
				}
				$("select[name=pass_cate03_"+uniqid+"]").append(option_str);
			}
			else if(_idx == 1) {
				var option_str = '';
				for (var i = 0; i < data.length; i++) {
					option_str += '<option value="' + data[i].optionValue + '"  '+(data[i].optionValue == val?' selected':'')+'>' + data[i].optionDisplay + '</option>';
				}
				$("select[name=pass_cate02_"+uniqid+"]").append(option_str);
				$("select[name=pass_cate03_"+uniqid+"]").find("option").remove().end().append('<option value="">-선택-</option>');
			}
        }
	});
}

</script>
<?PHP include_once("wrap.footer.php"); ?>