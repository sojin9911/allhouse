<?PHP

	include_once("wrap.header.php");



	// 모비톡에 넘길 변수
	//			id : 모비톡 아이디
	//			pw : 모비톡 비번
	//			onedaynet_id : 복호화할 솔루션의 onedaynet_id
	$SMSDec = enc_array('d', $siteInfo['s_smspw']);
	$pass_str = "id=" . $siteInfo['s_smsid'] . "&pw=" . $SMSDec['s_smspw'] . "&ip=" . $_SERVER['SERVER_ADDR'];
	$pass_var = "pass_var=" . enc('e' , onedaynet_encode( $pass_str )) .  "&eoi=" . enc('e' , $DB_id);

	$url = "//mobitalk.gobeyond.co.kr/pages/out_send_result.list.php?" . $pass_var ;

?>
<div style="width:100%;overflow:auto;" class="wrap_iframe"><iframe name="pass_mobitalk" src="<?=$url?>&cross_url=<?php echo urlencode($system['url']); ?>" style="width:100%; min-width:1200px; height:1000px; " class="js_auto_height" scrolling="no" frameborder=0 ></iframe></div>

<script type="text/javascript">
var ifrTarget = '.js_auto_height'; // 자동 크기조절될 아이프레임의 아이디나 클래스를 설정하세요.(아이디 #으로 시작, 클래스 .으로 시작)
if($(ifrTarget).length > 0) {

	// 프레임에서 정보 수신
	function IfrRevSize(e){
		var ifrHeight = e.data;
		//alert(e.data);
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
<?PHP
	include_once("wrap.footer.php");
?>