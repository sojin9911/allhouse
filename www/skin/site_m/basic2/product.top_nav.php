<?php
# 스킨의 파일을 바로 부를 경우 사용
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
/*
	$dp1cate -> 1차 카테고리 전체=> (/program/wrap.header.php에서 지정)
	$ActiveCate -> 현재카테고리의 정보를 반환<1~3차> => (/program/wrap.header.php에서 지정)
	$SameCategory => 현재 카테고리의 동일 위치 카테고리 => array(array('cuid'=>'cname')) => (/program/product.top_nav.php에서 지정)
	$DownCategory => 현재 카태고리의 하위 카테고리 => array(array('cuid'=>'cname')) => (/program/product.top_nav.php에서 지정)
	$Category2Depth -> 해당카테고리의 2차 카테고리 리스트를 반환 => (/program/product.top_nav.php에서 지정)
	$Category3Depth -> 해당카테고리의 2차 카테고리 리스트를 반환 => (/program/product.top_nav.php에서 지정)
*/
?>
<?php
if($_event) { // 이벤트 페이지에서 노출(검색 네비와 같은 구조)
	$EventProductList = _MQ_assoc(" select * from `smart_display_type_set` where (1) and dts_view = 'Y' and dts_list_product_mobile_view = 'Y' order by dts_idx asc ");
?>
	<!-- ******************************************
	     서브 상단(공통)
	  -- ****************************************** -->
	<div class="sub_top">
		<div class="sub_nav js_top_nav_same if_open"><!-- 클릭하면 if_open -->
			<div class="top_box">
				<a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로"></a>
				<div class="btn">
					<?php echo $ActiveCate['cname'][0]; ?>
					<?php if(count($EventProductList) > 0) { ?>
						<a href="#none" class="btn_ctrl js_tn_sub_open" title="동일카테고리 열기"></a>
					<?php } ?>
				</div>
			</div>

			<?php if(count($EventProductList) > 0) { ?>
				<script type="text/javascript">
					$(document).on('click', '.js_tn_sub_open', function(e) {
						e.preventDefault();
						$(this).closest('.js_top_nav_same').toggleClass('if_open');
					});
				</script>
				<!-- 동일 카테고리 열림(하위아님) -->
				<div class="same_ctg">
					<ul>
						<?php foreach($EventProductList as $epk=>$epv) { ?>
							<li<?php echo ($epv['dts_uid'] == $typeuid?' class="hit"':null); ?>><a href="/?pn=product.list&_event=type&typeuid=<?php echo $epv['dts_uid']; ?>" class="ctg2"><?php echo $epv['dts_name']; ?></a></li>
						<?php } ?>
					</ul>
				</div>
			<?php } ?>
		</div>
	</div>
	<!-- / 서브 상단(공통) -->
<?php } else { ?>
	<!-- ******************************************
	     서브 상단(공통)
	  -- ****************************************** -->
	<div class="sub_top">
		<div class="sub_nav js_top_nav_same"><!-- 클릭하면 if_open -->
			<div class="top_box">
				<a href="#none" onclick="history.go(-1); return false;" class="btn_back" title="뒤로"></a>
				<div class="btn">
					<?php echo ($ActiveCate['cname'][2]?$ActiveCate['cname'][1]:($ActiveCate['cname'][1]?$ActiveCate['cname'][1]:$ActiveCate['cname'][0])); ?>
					<a href="#none" class="btn_ctrl js_tn_sub_open" title="동일카테고리 열기"></a>
				</div>
			</div>
			<?php
			if(count($SameCategory) > 0) {
			?>
				<!-- 동일 카테고리 열림(하위아님) -->
				<div class="same_ctg">
					<ul>
						<?php
						// 상위 카테고리 '전체' 표시 - 1차에서는 나오지 않는다
						if(count($ActiveCate['cuid']) > 1) {
						?>
							<li><a href="/?pn=<?php echo ($pn == 'product.view'?'product.list':$pn); ?>&cuid=<?php echo $ActiveCate['cuid'][0]; ?>" class="ctg2">전체</a></li>
						<?php } ?>
						<?php
						foreach($SameCategory as $tn_k=>$tn_v) {
							$tn_hit = false;
							if($tn_k == $cuid) $tn_hit = true;
							else if(count($ActiveCate['cuid']) >= 2 && $ActiveCate['cuid'][1] == $tn_k) $tn_hit = true;
						?>
							<li<?php echo ($tn_hit === true?' class="hit"':null); ?>><a href="/?pn=<?php echo ($pn == 'product.view'?'product.list':$pn); ?>&cuid=<?php echo $tn_k; ?>" class="ctg2"><?php echo $tn_v; ?></a></li>
						<?php } ?>
					</ul>
				</div>
			<?php } ?>
		</div>
		<script type="text/javascript">
			$(document).on('click', '.js_tn_sub_open', function(e) {
				e.preventDefault();
				$(this).closest('.js_top_nav_same').toggleClass('if_open');
			});
		</script>

		<?php if(count($DownCategory) > 0) { ?>
			<!-- 하위 카테고리(2차 혹은 3차가 됨) -->
			<div class="ctg3_box">
				<div class="inner">
				<!-- li 3개 채워서 ul반복 -->
					<ul>
						<?php
						$TNNum = 0;
						$TNum = 0;
						// 상위 카테고리 '전체' 표시 - 1차에서는 나오지 않는다
						if(count($ActiveCate['cuid']) > 1) {
							$TNNum = 1;
							$TNum = 1;
							$ActiveTopCategory = $ActiveCate['cuid'][0];
							if(count($ActiveCate['cuid']) >= 2) $ActiveTopCategory = $ActiveCate['cuid'][1];
						?>
						<li<?php echo ($ActiveTopCategory == $cuid?' class="hit"':null); ?>><a href="/?pn=<?php echo ($pn == 'product.view'?'product.list':$pn); ?>&cuid=<?php echo $ActiveTopCategory; ?>" class="ctg3">전체</a></li>
						<?php } ?>
						<?php
						foreach($DownCategory as $tn_k=>$tn_v) {
							$TNNum++;
							if($TNum > 0 && $TNum%3 == 0) {
								echo '</ul><ul>';
								$TNNum = 1;
							}
							$TNum++;
						?>
							<li<?php echo ($tn_k == $cuid?' class="hit"':null); ?>><a href="/?pn=<?php echo ($pn == 'product.view'?'product.list':$pn); ?>&cuid=<?php echo $tn_k; ?>" class="ctg3"><?php echo $tn_v; ?></a></li>
						<?php } ?>
						<?php
						// 잔여개수 채우기
						if($TNNum < 3) {
							for($i=$TNNum; $i<3; $i++) {
						?>
							<li></li>
						<?php }} ?>
					</ul>
				</div>
			</div>
		<?php } ?>
	</div>
	<!-- / 서브 상단(공통) -->
<?php } ?>