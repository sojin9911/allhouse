<?php
// -- 해당 메인 상품의 설정을 가져온다.
$itemMain = _MQ("select *from `smart_display_main_set` where `dms_type` = 'main' and `dms_depth` = '2' and `dms_view` = 'Y' and `dms_uid` = '".$dmsuid."'
and `dms_list_product_mobile_view` = 'Y'");

// -- 검색된 값이 없거나 상품이 노출이 아닐 시
if(count($res) <= 0 || count($itemMain) < 1 ) {
	echo '
		<div class="item_list">
			<div class="c_none"><div class="gtxt">등록된 상품이 없습니다.</div></div>
		</div>
	';
	return;
}

// 몇개 단위로 more를 할것인지 지정
$dms_add_more = 4;


// 임시보기 출력개수 보정처리
if($_COOKIE['temp_skin']) {
	$SkinInfoColArr = explode(',', $SkinInfo['category']['mo_list_depth']);
	if(!in_array($itemMain['dms_list_product_mobile_display'], $SkinInfoColArr)) $itemMain['dms_list_product_mobile_display'] = $SkinInfo['category']['mo_list_depth_default'];
}

$item_list_class = '';
if(in_array($itemMain['dms_list_product_mobile_display'], array(1))) $item_list_class = 'if_col'.$itemMain['dms_list_product_mobile_display'];
?>
<!-- ◆ 상품리스트 : 기본 2단 / 1단 if_col1  -->
<div class="item_list<?php echo (isset($item_list_class) && $item_list_class != ''?' '.$item_list_class:null); ?>">
	<ul class="js_dms_<?php echo $dmsuid; ?>_item js_dms_<?php echo $dmsuid; ?>_item1">
		<?php
		$dms_add_num = 1;
		foreach($res as $k=>$v) {
			if($k > 0 && $k%$dms_add_more <= 0) {
				$dms_add_num++;
				echo '</ul><ul class="js_dms_'.$dmsuid.'_item js_dms_'.$dmsuid.'_item'.$dms_add_num.'" style="display:none;">';
			}
		?>
			<li>
				<?php 
					$incType =''; // 타입은 기본 type1, 있을 경우 별도 설정
					$locationFile = basename(__FILE__); // 파일설정
					include OD_PROGRAM_ROOT."/product.list.inc_type.php"; // 아이템박스 공통화
				?>
			</li>
		<?php } ?>
	</ul>
</div>
<!-- / ◆ 상품리스트 -->

<?php if($dms_add_num > 1) { ?>
	<div class="btn_box"><a href="#none" class="btn_more js_dms_<?php echo $dmsuid; ?>_add_more">더 보기</a></div>
	<script type="text/javascript">
		function dms_<?php echo $dmsuid; ?>_add_more() {
			var max_count = $('.js_dms_<?php echo $dmsuid; ?>_item').length; // 최대개수 조회
			var visible_num = $('.js_dms_<?php echo $dmsuid; ?>_item:not([style*="display:none"])').length; // 보이는 개수 산출
			visible_num++; // 보이는 개수+1
			if(max_count < visible_num) {
				$('.js_dms_<?php echo $dmsuid; ?>_add_more').remove(); // 최대 개수에 도달 하면 more버튼 삭제
				return false; // 최대개수 보다 카운트가 올라가면 기능 정지
			}
			if(max_count == visible_num) $('.js_dms_<?php echo $dmsuid; ?>_add_more').remove(); // 최대 개수에 도달 하면 more버튼 삭제
			$('.js_dms_<?php echo $dmsuid; ?>_item'+visible_num).show(); // 다음 상품 노출 처리
			scrolltoClass('.js_dms_<?php echo $dmsuid; ?>_item'+visible_num); // 보이게 되는 위치로 스크롤 이동
		}
		$(document).on('click', '.js_dms_<?php echo $dmsuid; ?>_add_more', function(e) {
			e.preventDefault();
			dms_<?php echo $dmsuid; ?>_add_more();
		});
	</script>
<?php } ?>