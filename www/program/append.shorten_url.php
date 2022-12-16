<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// url 축소하기 적용--------------------------------------
$org_url = "http://".$_SERVER[HTTP_HOST]."/?pn=product.view&pcode=".$_GET['pcode'];
$app_shorten_url = get_shortURL_2($org_url);
$app_shorten_url = $app_shorten_url?$app_shorten_url:$org_url;

$que = "insert into smart_sns_log set
				sl_pcode			=	'".$_GET[pcode]."',
				sl_type				=	'".$_GET[type]."',
				sl_ip				=	'".$_SERVER[REMOTE_ADDR]."',
				sl_rdate			=	now()";

$res = mysql_query($que);

switch($_GET['type']){

	// 트위터
	case "twitter":
		echo "<script>location.href=('http://twitter.com/intent/tweet?text=" . $_GET['text'] . "' + ' ' + encodeURIComponent('${app_shorten_url}'));</script>";
	break;

	// 미투데이
	case "me2day":
	echo "<script>location.href=('http://me2day.net/posts/new?new_post[body]=" . $_GET['_body'] . " ' + encodeURIComponent('${app_shorten_url}') + '&new_post[tags]=" . $_GET['_tags'] . "');</script>";
	break;

	// 페이스북
	case "facebook":
		echo "<script>location.href=('http://www.facebook.com/sharer.php?u=${app_shorten_url}&t=".$_GET['t']."');</script>";
	break;
		
}

actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행