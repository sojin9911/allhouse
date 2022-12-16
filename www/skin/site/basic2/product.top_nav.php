<?php
# 스킨의 파일을 바로 부를 경우 사용
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
/*
	$ActiveCate -> 현재카테고리의 정보를 반환<1~3차> => (/program/wrap.header.php에서 지정)
	$Category2Depth -> 해당카테고리의 2차 카테고리 리스트를 반환 => (/program/product.top_nav.php에서 지정)
	$Category3Depth -> 해당카테고리의 2차 카테고리 리스트를 반환 => (/program/product.top_nav.php에서 지정)
*/
$app_navpn = $pn;
# 상품상세는 목록 페이지로 이동
if($pn=='product.view') $app_navpn = 'product.list';

$LeftCategoryHit = 1;
if(count($Category2Depth) > 0) $LeftCategoryHit++;
if(count($Category3Depth) > 0) $LeftCategoryHit++;
?>
<!-- ◆ 서브 : 카테고리 위치 / 상품상세에서는 if_view -->
<div class="sub_location<?php echo ($pn == 'product.view'?' if_view':null); ?>">
	<div class="layout_fix">
		<div class="ctg_box">
			<ul>
				<li><a href="/" class="btn">home</a><span class=""></span></li>
				<li<?php echo ($LeftCategoryHit === 1?' class="hit"':null); ?>><span class="shape">&gt;</span><a href="/?pn=<?php echo $app_navpn; ?>&cuid=<?php echo $ActiveCate['cuid'][0]; ?>" class="btn"><?php echo $ActiveCate['cname'][0]; ?></a></li>
				<?php if(count($Category2Depth) > 0) { // 2차 카테고리 리스트 출력 ?>
					<li<?php echo ($LeftCategoryHit === 2?' class="hit"':null); ?>><span class="shape">&gt;</span><a href="/?pn=<?php echo $app_navpn; ?>&cuid=<?php echo (isset($ActiveCate['cuid'][1])?$ActiveCate['cuid'][1]:$ActiveCate['cuid'][0]); ?>" class="btn"><?php echo (isset($ActiveCate['cname'][1])?$ActiveCate['cname'][1]:'전체'); ?></a></li>
				<?php } ?>
				<?php if(count($Category3Depth) > 0) { // 3차 카테고리 리스트 출력 ?>
					<li<?php echo ($LeftCategoryHit === 3?' class="hit"':null); ?>><span class="shape">&gt;</span><a href="/?pn=<?php echo $app_navpn; ?>&cuid=<?php echo (isset($ActiveCate['cuid'][2])?$ActiveCate['cuid'][2]:$ActiveCate['cuid'][1]); ?>" class="btn"><?php echo (isset($ActiveCate['cname'][2])?$ActiveCate['cname'][2]:'전체'); ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>
<!-- /서브 : 카테고리 위치 -->