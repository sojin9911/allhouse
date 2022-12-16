 <?php
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
if(!$siteInfo['kakao_api']) die('통합관리자 - 환경설정 - 운영 관리 설정 - SNS 로그인/API 설정의 카카오톡 API 항목을 확인 하세요.');
$default_address = '광주광역시 서구 금호운천길 80-3';
$default_lat = '35.142302099986466';
$default_lng = '126.85627984861613';
?>
<html>
	<head>
		<title>다음지도 좌표 검색</title>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /><!-- 문서모드고정 (문서모드7이되서 깨지는경우가있음) -->
		<meta name="viewport" content="width=device-width" /><!-- 모바일에서 PC접근 크기 최적화 -->
		<meta name="format-detection" content="telephone=no" /><!-- 자동으로 전화링크되는것 방지 -->
		<!-- <meta name="robots" content="noindex"> -->

		<link href="style.css" rel="stylesheet" type="text/css" />

		<script src="<?php echo $system['__url']; ?>/include/js/jquery-1.11.2.min.js?ver=1"></script><!-- jquery -->
		<script src="<?php echo $system['__url']; ?>/include/js/jquery-migrate-1.2.1.min.js?ver=1"></script>
		<script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=<?php echo $siteInfo['kakao_api']; ?>&libraries=services"></script>
	</head>
	<body>
		<div id="js_map" style="width:100%; height:400px;"></div>
		<article class="markdown-body">
			<table>
				<colgroup>
					<col width="10%">
					<col width="35%">
					<col width="10%">
					<col width="35%">
				</colgroup>
				<tbody>
					<tr>
						<td>위도</td>
						<td><input class="js_map_tmp_lat" value="<?php echo $default_lat; ?>" style="width:100%"></td>
						<td>경도</td>
						<td><input class="js_map_tmp_lng" value="<?php echo $default_lng; ?>" style="width:100%"></td>
					</tr>
					<tr>
						<td colspan="4" style="text-align:center; font-weight:800;">주소검색</td>
					</tr>
					<tr>
						<td colspan="3">
							<input type="text" class="input_text js_find_addr" value="<?php echo trim(preg_replace('`\(.*?\)`', '', $default_address)); ?>" style="width:100%">
						</td>
						<td>
							<a href="#none" class="small white js_find_addr_submit">좌표검색</a>
						</td>
					</tr>
				</tbody>
			</table>
		</article>

		<script type="text/javascript">
			$(document).ready(function() {
				var geocoder = new daum.maps.services.Geocoder();
				var map_ele = document.getElementById('js_map');
				var options, map;
				var lat = $('.js_map_tmp_lat').val();
				var lng = $('.js_map_tmp_lng').val();

				// 지도 최초 실행
				if(lat === undefined) {
					map_ele.style.display = 'none';
				}
				else {
					options = { center: new daum.maps.LatLng(lat, lng), level: 3 }
					map = new daum.maps.Map(map_ele, options);
					SetMarker();
				}


				// 지도검색
				$(document).on('click', '.js_find_addr_submit', function(e) {
					e.preventDefault();
					FindAddr();
				});


				// 주소 -> 좌표 -> 지도표기
				function FindAddr() {
					var geo_addr = $('.js_find_addr').val();
					geocoder.addressSearch(geo_addr,  function(result, status) {
						if(status === daum.maps.services.Status.OK) {
							$('.js_map_tmp_lat').val(result[0]['y']);
							$('.js_map_tmp_lng').val(result[0]['x']);
							map_ele.style.display = 'block';
							options = {
								center: new daum.maps.LatLng(result[0]['y'], result[0]['x']),
								level: 3
							}
							map = new daum.maps.Map(map_ele, options);
							SetMarker();
						}
						else {
							alert('주소검색에 실패 하였습니다.\n주소를 다시 확인 후 시도바랍니다.');
						}
					});
				}

				// 마커표시
				function SetMarker() {
					if(typeof map == 'object') {
						// 마커 추가
						var marker = new daum.maps.Marker({ position: map.getCenter() });
						marker.setMap(map);

						// 지도타입 컨트롤러 추가
						var mapTypeControl = new daum.maps.MapTypeControl();
						map.addControl(mapTypeControl, daum.maps.ControlPosition.TOPRIGHT);

						// 줌컨트롤러 추가
						var zoomControl = new daum.maps.ZoomControl();
						map.addControl(zoomControl, daum.maps.ControlPosition.RIGHT);

						// 클릭하여 좌표 변경
						new daum.maps.event.addListener(map, 'click', function(mouseEvent) {

							var latlng = mouseEvent.latLng;
							marker.setPosition(latlng);
							map.panTo(latlng);

							$('.js_map_tmp_lat').val(latlng.getLat());
							$('.js_map_tmp_lng').val(latlng.getLng());
						});
					}
				}
			});
		</script>
	</body>
</html>