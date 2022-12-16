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
include_once($_SERVER['DOCUMENT_ROOT'].'/addons/npay/Nsync.class.php');


# 테스트 모드, 키 값 검증
if($siteInfo['npay_sync_mode'] != 'test') die('실서비스 모드 입니다. 테스트 모드를 진행 하실 필요가 없습니다.');
if(trim($siteInfo['npay_lisense']) == '') die('AccessLicense가 없습니다. 환경설정 - PG설정 - 주문연동 - AccessLicense 를 확인하세요.');
if(trim($siteInfo['npay_secret']) == '') die('SecretKey가 없습니다. 환경설정 - PG설정 - 주문연동 - SecretKey 를 확인하세요.');

# 필수 라이브러리 검증
if(!extension_loaded('mcrypt')) { echo '서버관리자에게 mcrypt 2.6.8, libmcrypt 2.5.8 설치를 요청 하세요.'; phpinfo(); exit; }
if(!extension_loaded('mhash')) { echo '서버관리자에게 mhash 0.9.9.9 설치를 요청 하세요.'; phpinfo(); exit; }
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>주문연동(네이버페이 API연동) 테스트 툴</title>
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


			// 테스트 모드 실행
			function TestMode(Type) {

				var Target = Type.toLowerCase();
				var result = $.TBAjax('tools.pro.php', '?mode='+Type);
				$('.'+Target+'_result').html(result);
				scrolltoClass('.'+Target+'_result');
				ActiveAni('.'+Target+'_result',0);
			}

			// 그리드 비움
			function TestRest(Type) {

				var Target = Type.toLowerCase();
				$('.'+Target+'_result').html(Type+' \'테스트\'버튼을 눌러 실행하세요.');
			}
		</script>
	</head>
	<body>
		<article class="markdown-body">
			<!-- 테스트툴 액션 -->
			<h1>주문연동(네이버페이 API연동) 테스트 툴</h1>
			<table>
				<thead>
					<tr>
						<th>요청API</th>
						<th>설명</th>
						<th>액션</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>GetChangedProductOrderList</td>
						<td>변경 상품주문 조회</td>
						<td><input type="button" value="테스트" onclick="TestMode('GetChangedProductOrderList');"></td>
					</tr>
					<tr>
						<td>GetProductOrderInfoList</td>
						<td>상품주문 내역 상세 조회</td>
						<td>
							<input type="button" value="테스트" onclick="TestMode('GetProductOrderInfoList');">
						</td>
					</tr>
					<tr>
						<td>CancelSale</td>
						<td>판매 취소</td>
						<td><input type="button" value="테스트" onclick="TestMode('CancelSale');"></td>
					</tr>
					<tr>
						<td>ShipProductOrder</td>
						<td>발송 처리</td>
						<td><input type="button" value="테스트" onclick="TestMode('ShipProductOrder');"></td>
					</tr>
					<tr>
						<td>phpinfo</td>
						<td>phpinfo 확인</td>
						<td><input type="button" value="테스트" onclick="TestMode('phpinfo');"></td>
					</tr>
				</tbody>
			</table>
			<!-- / 테스트툴 액션 -->



			<!-- GetChangedProductOrderList -->
			<h3>
				<code>GetChangedProductOrderList</code> 결과
				<input type="button" value="테스트" onclick="TestMode('GetChangedProductOrderList');">
				<small><a href="javascript:TestRest('GetChangedProductOrderList');">리셋</a></small>
			</h3>
			<blockquote class="getchangedproductorderlist_result">GetChangedProductOrderList '테스트'버튼을 눌러 실행하세요.</blockquote>
			<!-- / GetChangedProductOrderList -->



			<!-- GetProductOrderInfoList -->
			<h3>
				<a name="GetProductOrderInfoList"></a>
				<code>GetProductOrderInfoList</code> 결과 <small>
				<input type="button" value="테스트" onclick="TestMode('GetProductOrderInfoList');">
				<a href="javascript:TestRest('GetProductOrderInfoList');">리셋</a></small>
			</h3>
			<blockquote class="getproductorderinfolist_result">GetProductOrderInfoList '테스트'버튼을 눌러 실행하세요.</blockquote>
			<!-- / GetProductOrderInfoList -->



			<!-- CancelSale -->
			<h3>
				<code>CancelSale</code> 결과
				<input type="button" value="테스트" onclick="TestMode('CancelSale');">
				<small><a href="javascript:TestRest('CancelSale');">리셋</a></small>
			</h3>
			<blockquote class="cancelsale_result">CancelSale '테스트'버튼을 눌러 실행하세요.</blockquote>
			<!-- / CancelSale -->



			<!-- ShipProductOrder -->
			<h3>
				<code>ShipProductOrder</code> 결과
				<input type="button" value="테스트" onclick="TestMode('ShipProductOrder');">
				<small><a href="javascript:TestRest('ShipProductOrder');">리셋</a></small>
			</h3>
			<blockquote class="shipproductorder_result">ShipProductOrder '테스트'버튼을 눌러 실행하세요.</blockquote>
			<!-- / ShipProductOrder -->



			<!-- phpinfo -->
			<h3>
				<code>phpinfo</code> 결과
				<input type="button" value="테스트" onclick="TestMode('phpinfo');">
				<small><a href="javascript:TestRest('phpinfo');">리셋</a></small>
			</h3>
			<blockquote class="phpinfo_result">phpinfo '테스트'버튼을 눌러 실행하세요.</blockquote>
			<!-- / phpinfo -->
		</article>
	</body>
</html>