<?php
include_once('wrap.header.php');
$_type = ($_type?$_type:'day'); // 출력모드
?>

<!-- 탭메뉴 -->
<div class="c_tab">
	<ul>
		<li<?php echo ($_type == 'day'?' class="hit"':null); ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?_type=day" class="btn"><strong>일자별</strong></a></li>
		<li<?php echo ($_type == 'month'?' class="hit"':null); ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?_type=month" class="btn"><strong>월별</strong></a></li>
		<li<?php echo ($_type == 'hour'?' class="hit"':null); ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?_type=hour" class="btn"><strong>시간별</strong></a></li>		
		<li<?php echo ($_type == 'week'?' class="hit"':null); ?>><a href="<?php echo $_SERVER['PHP_SELF']; ?>?_type=week" class="btn"><strong>요일별</strong></a></li>
	</ul>
</div>
<!-- / 탭메뉴 -->


<?php
include_once('_static_sale.method.'.$_type.'.php');
include_once('wrap.footer.php');
?>