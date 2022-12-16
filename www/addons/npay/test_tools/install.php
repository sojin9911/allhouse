<?php
/*
mhash0.9.9.9
libmcrypt2.5.8
mcrypt2.6.8
php >= 4.4
php option --with-mhash --with-mcrypt --with-dom --with-zlib-dir
*/
# NPay
if(!$_SERVER['DOCUMENT_ROOT']) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../../'); // dirname(__FILE__) 다음 경로 주의
$_path_str = $_SERVER['DOCUMENT_ROOT'];
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

# 필수 라이브러리 검증
if(!extension_loaded('mcrypt')) { echo '서버관리자에게 mcrypt 2.6.8, libmcrypt 2.5.8 설치를 요청 하세요.'; phpinfo(); exit; }
//if(!extension_loaded('mhash')) { echo '서버관리자에게 mhash 0.9.9.9 설치를 요청 하세요.'; phpinfo(); exit; }
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>주문연동(네이버페이 API연동) DB Install 툴</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<style type="text/css">
		input[type=button] { background-color:#353535; color:#fff; border:0; cursor:pointer }
		</style>
		<script src="/include/js/jquery-1.11.2.min.js"></script>
		<script src="/include/js/jquery/jquery.easing.1.3.js"></script>
		<script src="tb.ajax-2.0.js"></script>
		<script type="text/javascript">
			// 활성화 애니
			function ActiveAni(Target, num) {

				if(!num) num = 0;
				setTimeout(function(){

					$({alpha:0}).animate({alpha:1}, {
						duration: 500,
						step: function() {

							$(Target).css('border-color','rgba(255,0,0,'+this.alpha+')');
						},
						complete: function() {

							$(Target).css('border-color','rgba(221,221,221,'+this.alpha+')');

							num++;
							if(num <= 2) ActiveAni(Target, num);
						}
					});
				}, 100);
			}


			// 해쉬태그로 페이지 스크롤 함수
			function scrolltoClass(Target) {

				var $root = $('html, body');
				if($(Target).offset() === undefined) return; // 없는 객체라면 실행 차단
				$root.animate({
					scrollTop: $(Target).offset().top - 10
				}, 500, 'easeInOutCubic');
			}


			// Install 모드 실행
			function IntallMode(Type) {

				var Target = Type.toLowerCase();
				var result = $.TBAjax('install.pro.php', '?mode='+Type);
				$('.'+Target+'_result').html(result);
				scrolltoClass('.'+Target+'_result');
				ActiveAni('.'+Target+'_result',0);
			}

			// 그리드 비움
			function GridReset(Type) {

				var Target = Type.toLowerCase();
				$('.'+Target+'_result').html(Type+' \'Install\'버튼을 눌러 실행하세요.');
			}
		</script>
	</head>
	<body>
		<article class="markdown-body">
			<!-- Install툴 액션 -->
			<h1>주문연동(네이버페이 API연동) Install 툴</h1>
			<table>
				<thead>
					<tr>
						<th>요청API</th>
						<th>설명</th>
						<th>설치</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>smart_setup</td>
						<td>환경설정 네이버페이 관련 필드 생성</td>
						<td><input type="button" value="Install" onclick="IntallMode('smart_setup');"></td>
					</tr>
					<tr>
						<td>smart_product</td>
						<td>상품설정 네이버페이 관련 필드 생성</td>
						<td>
							<input type="button" value="Install" onclick="IntallMode('smart_product');">
						</td>
					</tr>
					<tr>
						<td>smart_order</td>
						<td>주문 네이버페이 관련 필드 생성</td>
						<td><input type="button" value="Install" onclick="IntallMode('smart_order');"></td>
					</tr>
					<tr>
						<td>smart_order_product</td>
						<td>주문상품 네이버페이 관련 필드 생성</td>
						<td><input type="button" value="Install" onclick="IntallMode('smart_order_product');"></td>
					</tr>
					<tr>
						<td>smart_npay</td>
						<td>임시 네이버페이 테이블 생성</td>
						<td><input type="button" value="Install" onclick="IntallMode('smart_npay');"></td>
					</tr>
				</tbody>
			</table>
			<!-- / Install툴 액션 -->



			<!-- smart_setup -->
			<h3>
				<code>smart_setup</code> 결과
				<input type="button" value="Install" onclick="IntallMode('smart_setup');">
				<small><a href="javascript:GridReset('smart_setup');">리셋</a></small>
			</h3>
			<blockquote class="smart_setup_result">smart_setup 'Install' 버튼을 눌러 실행하세요.</blockquote>
			<!-- / smart_setup -->



			<!-- smart_product -->
			<h3>
				<code>smart_product</code> 결과
				<input type="button" value="Install" onclick="IntallMode('smart_product');">
				<small><a href="javascript:GridReset('smart_product');">리셋</a></small>
			</h3>
			<blockquote class="smart_product_result">smart_product 'Install' 버튼을 눌러 실행하세요.</blockquote>
			<!-- / smart_product -->



			<!-- smart_order -->
			<h3>
				<code>smart_order</code> 결과
				<input type="button" value="Install" onclick="IntallMode('smart_order');">
				<small><a href="javascript:GridReset('smart_order');">리셋</a></small>
			</h3>
			<blockquote class="smart_order_result">smart_order 'Install' 버튼을 눌러 실행하세요.</blockquote>
			<!-- / smart_order -->



			<!-- smart_order_product -->
			<h3>
				<code>smart_order_product</code> 결과
				<input type="button" value="Install" onclick="IntallMode('smart_order_product');">
				<small><a href="javascript:GridReset('smart_order_product');">리셋</a></small>
			</h3>
			<blockquote class="smart_order_product_result">smart_order_product 'Install' 버튼을 눌러 실행하세요.</blockquote>
			<!-- / smart_order_product -->



			<!-- smart_npay -->
			<h3>
				<code>smart_npay</code> 결과
				<input type="button" value="Install" onclick="IntallMode('smart_npay');">
				<small><a href="javascript:GridReset('smart_npay');">리셋</a></small>
			</h3>
			<blockquote class="smart_npay_result">smart_npay 'Install' 버튼을 눌러 실행하세요.</blockquote>
			<!-- / smart_npay -->
		</article>
	</body>
</html>