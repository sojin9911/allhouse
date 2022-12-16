<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
if($access_token) {
	$Profile = json_decode(CurlExec('https://api.instagram.com/v1/users/self/?access_token='.$access_token, 10), true);
	$insta_id = $Profile['data']['username'];
}
?>
<html>
<head>
	<title>하이센스 3.0 인스타그램 API 토큰연동</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /><!-- 문서모드고정 (문서모드7이되서 깨지는경우가있음) -->

	<script src="<?php echo $system['__url']; ?>/include/js/jquery-1.11.2.min.js?ver=1"></script><!-- jquery -->
	<script type="text/javascript">
		$(document).ready(function() {
			<?php if(!$access_token) { ?>

				var UrlHash = $(location).attr('hash');
				var Token = UrlHash.split('#access_token=');
				Token = Token[1];
				if(Token == '') {
					alert('인스타그램과 통신이 실패하였습니다.');
					window.close();
				}
				location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?access_token='+Token;

			<?php } else { ?>

				var tokenEle = $(opener.document).find('.js_insta_token');
				var idEle = $(opener.document).find('.js_insta_id');
				var insta_token = '<?php echo $access_token; ?>';
				var insta_id = '<?php echo $insta_id; ?>';

				if(insta_token == '' || insta_id == '') {
					alert('프로필 정보 획득에 실패했습니다.\n\n잠시 후 다시 시도해주세요.');
					window.close();
				}

				if(tokenEle.length <= 0 || idEle.length <= 0) {
					alert('솔루션과 통신할 수 없는 상태입니다.');
					window.close();
				}
				tokenEle.val(insta_token);
				idEle.val(insta_id);

				alert('성공적으로 토큰키가 마킹되었습니다.\n\n확인을 눌러 저장해주세요.');
				window.close();

			<?php } ?>
		});
	</script>
</head>
<body><!-- 문제가 있다면 창을 닫고 잠시 후 다시 시도해주세요. --></body>
</html>