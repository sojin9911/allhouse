<?php
include_once('inc.php');
$_login_trigger = 'N'; // 로그인 필요없는 페이지 표시
include_once('inc.header.php');
$MoveLocation = '_main.php';
if(AdminLoginCheck('value') === false) $MoveLocation = 'logout.php';
?>
<body class="login_bg">
	<!-- 2초후 인덱스 자동이동 -->
	<meta http-equiv="refresh" content="1;url=<?php echo $MoveLocation; ?>">
	<div class="member_login_wrap">
		<!-- 로딩이미지 -->
		<div class="intro_loading_box">
			<div class="loading"></div>
			<div class="iconimg"></div>
		</div>
	</div>
</body>
<?php include_once('inc.footer.php'); ?>