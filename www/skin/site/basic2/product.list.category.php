<?php
if(count($dp2cate[$ActiveCate['cuid'][0]]) <= 0) return; // 2차 카테고리가 없는 경우 실행 안함

// 해당 카테고리의 2차카테고리 리스트
$Category2Depth = $dp2cate[$ActiveCate['cuid'][0]];

// 해당 카테고리의 3차 카테고리 리스트
$Category3Depth = array();
if(isset($ActiveCate['cuid'][1])) {
	$Category3Depth = $dp3cate[$ActiveCate['cuid'][1]];
}
?>
<!-- ◆ 서브 : 카테고리 -->
<div class="sub_category">
	<?php foreach($Category2Depth as $k=>$v) { ?>
		<div class="depth_box<?php echo ($ActiveCate['cuid'][1] == $v['c_uid']?' if_open':null); ?>"><!-- 2차 선택하면 if_open -->
			<a href="/?pn=<?php echo $pn; ?>&cuid=<?php echo $v['c_uid']; ?>" class="ctg2"><?php echo $v['c_name']; ?></a><!-- 2차 -->
			<?php if(count($dp3cate[$v['c_uid']]) > 0) { ?>
				<ul>
					<?php foreach($dp3cate[$v['c_uid']] as $kk=>$vv) { ?>
						<li<?php echo ($ActiveCate['cuid'][2] == $vv['c_uid']?' class="hit"':null); ?>><a href="/?pn=<?php echo $pn; ?>&cuid=<?php echo $vv['c_uid']; ?>" class="ctg3"><?php echo $vv['c_name']; ?></a></li>
					<?php } ?>
				</ul>
			<?php } ?>
		</div>
	<?php } ?>
</div>
<!-- / 서브 : 카테고리 -->