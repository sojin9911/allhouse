<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
	<head>
		<title><?php echo $siteInfo['s_glbtlt']; ?></title>


		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /><!-- 문서모드고정 (문서모드7이되서 깨지는경우가있음) -->
		<?php /*
		// 캐싱을 사용하지 않을경우 사용 (/include/var.php 의 $cache_ver 값을 마이크로 타임으로 변경 필요)
		<meta http-equiv="Cache-Control" content="no-cache">
		<meta http-equiv="Expires" content="0">
		<meta http-equiv="Pragma" content="no-cache">
		*/?>
		<meta name="viewport" content="width=device-width" /><!-- 모바일에서 PC접근 크기 최적화 -->
		<meta name="format-detection" content="telephone=no" /><!-- 자동으로 전화링크되는것 방지 -->
		<meta name="keywords" content="<?php echo str_replace(array('/', '\\', '"', "'"), '', $siteInfo['s_glbkwd']); ?>">
		<meta name="description" content="<?php echo str_replace(array('/', '\\', '"', "'"), '', $siteInfo['s_glbdsc']); ?>">
		<?php if($og_type) { ?><meta property="og:type" content="<?php echo $og_type; ?>" /><!-- Open Graph --><?php echo PHP_EOL; } ?>
		<?php if($og_title) { ?><meta property="og:title" content="<?php echo $og_title; ?>" /><!-- Open Graph --><?php echo PHP_EOL; } ?>
		<?php if($og_description) { ?><meta property="og:description" content="<?php echo $og_description; ?>" /><!-- Open Graph --><?php echo PHP_EOL; } ?>
		<?php if($og_url) { ?><meta property="og:url" content="<?php echo $og_url; ?>" /><!-- Open Graph --><?php echo PHP_EOL; } ?>
		<?php if($og_site_name) { ?><meta property="og:site_name" content="<?php echo $og_site_name; ?>" /><!-- Open Graph --><?php echo PHP_EOL; } ?>
		<?php if($og_image) { ?><meta property="og:image" content="<?php echo $og_image; ?>" /><!-- Open Graph --><?php echo PHP_EOL; } ?>
		<?php if($og_app_id) { ?><meta property="fb:app_id" content="<?php echo $og_app_id; ?>" /><!-- Open Graph --><?php echo PHP_EOL; } ?>
		<!-- 2019-12-03 SSJ :: 트위터 카드 추가 -->
		<?php if($og_type2) { ?><meta property="twitter:card" content="<?php echo $og_type2; ?>" /><!-- twitter:card --><?php echo PHP_EOL; } ?>
		<?php if($og_title) { ?><meta property="twitter:title" content="<?php echo $og_title; ?>" /><!-- twitter:card --><?php echo PHP_EOL; } ?>
		<?php if($og_description) { ?><meta property="twitter:description" content="<?php echo $og_description; ?>" /><!-- twitter:card --><?php echo PHP_EOL; } ?>
		<?php if($og_url) { ?><meta property="twitter:url" content="<?php echo $og_url; ?>" /><!-- twitter:card --><?php echo PHP_EOL; } ?>
		<?php if($og_image) { ?><meta property="twitter:image" content="<?php echo $og_image; ?>" /><!-- twitter:card --><?php echo PHP_EOL; } ?>
		<?php echo (str_replace(array('.onedaynet.co.kr', '.gobeyond.co.kr'), '', $_SERVER['HTTP_HOST']) != $_SERVER['HTTP_HOST']?'<meta name="robots" content="noindex">'.PHP_EOL:null); // 원데이넷/상상너머 2차 도메인으로 네이버 검색 노출 차단 ?>
		<meta name="NaverBot" content="ALL">
		<meta name="NaverBot" content="index,follow">


		<!-- 디자인css 순서지킬것 -->
		<link rel="canonical" href="<?php echo $canonical_url; ?>">
		<?php if($og_image) { ?><link href="<?php echo $og_image; ?>" rel="image_src" /><!-- Open Graph --><?php echo PHP_EOL; } // 페이스북 공유하기 썸네일 ?>
		<link href="<?php echo $SkinData['skin_url']; ?>/css/c_design.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" type="text/css" /><!-- 공통 CSS -->
		<link href="<?php echo $SkinData['skin_url']; ?>/css/c_board.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" type="text/css" /><!-- 공통 CSS -->
		<link href="<?php echo $SkinData['skin_url']; ?>/css/c_item.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" type="text/css" /><!-- 공통 CSS -->
		<link href="<?php echo $SkinData['skin_url']; ?>/css/c_member.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" type="text/css" /><!-- 공통 CSS -->
		<link href="<?php echo $SkinData['skin_url']; ?>/css/c_mypage.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" type="text/css" /><!-- 공통 CSS -->
		<link href="<?php echo $SkinData['skin_url']; ?>/css/c_shop.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" type="text/css" /><!-- 공통 CSS -->
		<link href="<?php echo $SkinData['skin_url']; ?>/css/setting.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" type="text/css" /><!-- 스킨별 CSS -->
		<link href="<?php echo $SkinData['skin_url']; ?>/css/hyssence.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" type="text/css" /><!-- 스킨별 CSS -->
		<link href="<?php echo $SkinData['skin_url']; ?>/css/hyssence_sub.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" type="text/css" /><!-- 스킨별 CSS -->
		<link href="<?php echo $SkinData['skin_url']; ?>/css/customize_p.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" type="text/css" /><!-- 개발팀전용 -->
		<link href="<?php echo $SkinData['skin_url']; ?>/css/customize_d.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" type="text/css" /><!-- 디자인팀전용 -->
		<link href="<?php echo $system['__url']; ?>/include/js/bxslider/jquery.bxslider.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" type="text/css" /><!-- bxslider -->
		<link href="<?php echo $system['__url']; ?>/include/js/air-datepicker/css/datepicker.min.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" type="text/css"><!-- 데이트피커 추가 -->
		<link href="<?php echo $SkinData['skin_url']; ?>/css/editor.css" rel="stylesheet" type="text/css" /><!-- 에디터 css -->
		<?php if($Favicon) { echo PHP_EOL; ?>
		<!-- 파비콘 -->
		<link href="<?php echo $system['__url'].IMG_DIR_BANNER.$Favicon; ?>" rel="apple-touch-icon-precomposed" />
		<link href="<?php echo $system['__url'].IMG_DIR_BANNER.$Favicon; ?>" rel="shortcut icon" type="image/x-icon"/>
		<?php echo PHP_EOL; } ?>


		<script src="<?php echo $system['__url']; ?>/include/js/jquery-1.11.2.min.js?ver=1"></script><!-- jquery -->
		<script src="<?php echo $system['__url']; ?>/include/js/jquery-migrate-1.2.1.min.js?ver=1"></script>
		<script src="<?php echo $system['__url']; ?>/include/js/jquery/jquery.easing.1.3.js?ver=1"></script>
		<script src="<?php echo $system['__url']; ?>/include/js/jquery.placeholder.js?ver=1"></script>
		<script src="<?php echo $system['__url']; ?>/include/js/jquery/jquery.lightbox_me.js?ver=1"></script><!-- lightbox -->
		<script src="<?php echo $system['__url']; ?>/include/js/bxslider/jquery.bxslider.js?ver=1"></script><!-- bxslider -->
		<script src="<?php echo $system['__url']; ?>/include/js/jquery/jquery.validate.js?ver=1"></script><!-- validate -->
		<script src="<?php echo $system['__url']; ?>/include/js/jquery.dotdotdot.js?ver=1"></script><!-- dotdotdot -->
		<script src="<?php echo $system['__url']; ?>/include/js/default.js?ver=<?php echo $cache_ver; ?>"></script><!-- 기본 js -->
		<script src="<?php echo $system['__url']; ?>/include/js/shop.js?ver=<?php echo $cache_ver; ?>"></script><!-- 쇼핑몰 공통 js -->

		<!--  2017-07-12 ::: 네이버 스마트에디터2 추가 ::: SSJ { -->
		<script type="text/javascript" src="<?php echo $system['__url']; ?>/include/smarteditor2/js/service/HuskyEZCreator.js?ver=<?php echo $cache_ver; ?>" charset="utf-8"></script>
		<script type="text/javascript" src="<?php echo $system['__url']; ?>/include/smarteditor2/smarteditor2.js?ver=<?php echo $cache_ver; ?>" charset="utf-8"></script>
		<!-- } 2017-07-12 ::: 네이버 스마트에디터2 추가 ::: SSJ -->

		<!-- 데이트피커 추가 -->
			<!--[if lte ie 9]>
				<link href="<?php echo $system['__url']; ?>/include/js/jquery/jqueryui/jquery-ui.min.css?ver=<?php echo $cache_ver; ?>" rel="stylesheet" type="text/css" />
				<script src="<?php echo $system['__url']; ?>/include/js/jquery/jqueryui/jquery-ui.min.js?ver=<?php echo $cache_ver; ?>"></script>
				<script src="<?php echo $system['__url']; ?>/include/js/jquery/jquery.ui.datepicker-ko.js?ver=<?php echo $cache_ver; ?>"></script>
				<script type="text/javascript">
					window.datepicker_ymd = 'yy-mm-dd';
				</script>
			<![endif]-->
			<!--[if (gt ie 9)]>
				<script src="<?php echo $system['__url']; ?>/include/js/air-datepicker/js/datepicker.js?ver=<?php echo $cache_ver; ?>"></script>
				<script src="<?php echo $system['__url']; ?>/include/js/air-datepicker/js/i18n/datepicker.ko.js?ver=<?php echo $cache_ver; ?>"></script>
				<script type="text/javascript">
					window.datepicker_ymd = 'yyyy-mm-dd';
				</script>
			<![endif]-->
			<?php if (!preg_match('/MSIE\s(?P<v>\d+)/i', @$_SERVER['HTTP_USER_AGENT'], $B)) { ?>
				<script src="<?php echo $system['__url']; ?>/include/js/air-datepicker/js/datepicker.js?ver=<?php echo $cache_ver; ?>"></script>
				<script src="<?php echo $system['__url']; ?>/include/js/air-datepicker/js/i18n/datepicker.ko.js?ver=<?php echo $cache_ver; ?>"></script>
				<script type="text/javascript">
					window.datepicker_ymd = 'yyyy-mm-dd';
				</script>
			<?php } ?>
		<!-- 데이트피커 추가 -->

		<script type="text/javascript">
			var od_url = '<?php echo $og_url; ?>';
			var og_type = '<?php echo ($og_type?$og_type:null); ?>';
			var og_title = '<?php echo ($og_title?$og_title:null); ?>';
			var og_description = '<?php echo ($og_description?rm_enter($og_description):null); ?>';
			var og_url = '<?php echo ($og_url?$og_url:null); ?>';
			var og_site_name = '<?php echo ($og_site_name?$og_site_name:null); ?>';
			var og_image = '<?php echo ($og_image?$og_image:null); ?>';
			var og_app_id = '<?php echo ($og_app_id?$og_app_id:null); ?>';

			$(function() { $('.ellipsis').dotdotdot(); });

			<?php if(isset($siteInfo['s_facebook_key'])) { echo PHP_EOL; // 페이스북 앱 처리 ?>
			window.fbAsyncInit = function() { FB.init({ version: 'v2.12', appId: '<?php echo $siteInfo['s_facebook_key']; ?>', xfbml: true, status: true, cookie: true }); };
			$(window).bind('load', function() { (function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if(d.getElementById(id)) {return;} js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/ko_KR/all.js"; fjs.parentNode.insertBefore(js, fjs); }(document, 'script', 'facebook-jssdk')); });
			function postToFeed(title, desc, url, image){ FB.ui({method: 'feed',link: url, picture: image, name: title,description: desc}, function() {}); }
			function postToFeedLayer(title, desc, url, image){ FB.ui({method: 'feed', display: 'popup', link: url, picture: image, name: title,description: desc}, function() {}); }
			<?php echo PHP_EOL; } // 페이스북 앱 처리 ?>
		</script>

		<?php if($siteInfo['npay_use'] == 'Y' && trim($siteInfo['npay_all_key']) != '') { echo PHP_EOL; ?>
		<!-- 네이버 유입경로 -->
		<script type="text/javascript" src="//wcs.naver.net/wcslog.js"></script>
		<script type="text/javascript">
			if(!wcs_add) var wcs_add = {};
			wcs_add["wa"] = "<?php echo $siteInfo['npay_all_key']; ?>";

			// 체크아웃 whitelist가 있을 경우
			// 유입 추적 함수 호출
			wcs.inflow("<?php echo str_replace('www.', '', reset(explode(':', $_SERVER["HTTP_HOST"]))); ?>");
			wcs_do();
		</script>
		<!-- 네이버 유입경로 -->
		<?php } ?>

		<?php if(count($NWChanel) > 0) { echo PHP_EOL; ?>
		<!-- 네이버 검색 연관채널 -->
		<script type="application/ld+json">
			{
				"@context": "http://schema.org",
				"@type": "Person",
				"name": "<?php echo $og_title; ?>",
				"url": "<?php echo $og_url; ?>",
				"sameAs": [
					<?php foreach($NWChanel as $nwck=>$nwcv) { ?>
					<?php echo ($nwck>0?',':''); ?>"<?php echo $nwcv; ?>"
					<?php } echo PHP_EOL; ?>
				]
			}
		</script>
		<?php } ?>

		<?php echo $siteInfo['s_gmeta'].PHP_EOL; // 메타태그 출력 - 사용자 오류에 영향을 덜 받도록 이위치 고수 ?>
		<?php actionHook('header_insert'); // <head>~</head> 사이 후킹을 이용하여 스크립트, css, 메타등을 추가 해야 하는경우 사용 ?>

	</head>
<body>
<div class="wrap js_header_position">