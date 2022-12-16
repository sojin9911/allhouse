<?php
$app_current_link = basename($_SERVER['PHP_SELF']).'?cont='.$_GET['cont'];
include_once('wrap.header.php');

?>
<div class="wrap_iframe" style="width:100%;overflow:auto;">
	<iframe src="<?php echo (is_https()?'https://www.onedaynet.co.kr':'//www.onedaynet.co.kr'); ?>/_hy30_content/?cont=<?php echo urlencode($cont); ?>&cross_url=<?php echo urlencode($system['url']); ?>&enc=<?php echo $SiteInfoEnc; ?>" class="js_auto_height" scrolling="no" frameborder="0" style="width:100%; height:1200px; min-width:1200px;"></iframe>
</div>



<script type="text/javascript">
var ifrTarget = '.js_auto_height'; // 자동 크기조절될 아이프레임의 아이디나 클래스를 설정하세요.(아이디 #으로 시작, 클래스 .으로 시작)
if($(ifrTarget).length > 0) {

	// 프레임에서 정보 수신
	function IfrRevSize(e){
		var ifrHeight = e.data;
		$(ifrTarget).css('height', ifrHeight+'px');
	}
	if('addEventListener' in window) window.addEventListener('message', IfrRevSize, false);
	else if('attachEvent' in window) window.attachEvent('onmessage', IfrRevSize); // IE

	// 첫페이지 요청
	$(window).load(function() {
		var iframe = document.querySelector(ifrTarget);
		var src = $(ifrTarget).prop('src');
		//var ParseUrl = new URL(src);
		var ParseUrl = parseUrl(src);
		iframe.contentWindow.postMessage(getParameterByName('cross_url', ParseUrl['href']), ParseUrl['origin']);
	});

	// 리사이즈가 되면 프레임에 리사이즈 요청
	$(window).resize(function() {
		var iframe = document.querySelector(ifrTarget);
		var src = $(ifrTarget).prop('src');
		//var ParseUrl = new URL(src);
		var ParseUrl = parseUrl(src);
		iframe.contentWindow.postMessage(getParameterByName('cross_url', ParseUrl['href']), ParseUrl['origin']);
	});
}
</script>
<?php include_once('wrap.footer.php'); ?>