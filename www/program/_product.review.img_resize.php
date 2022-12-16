<?php
/*
* SSJ : 리뷰이미지 일괄 리사이즈  : 2021-05-24
* https://{도메인}/program/_product.review.img_resize.php
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
$app_path = dirname(__FILE__)."/..".IMG_DIR_PRODUCT;


$talk_type = 'eval'; // 상품후기
$que = " select * from smart_product_talk where 1 and pt_type = '".$arr_p_talk_type[$talk_type]."' and pt_depth = 1 and pt_img != '' ";
$res = _MQ_assoc($que);
foreach($res as $k=>$v){
	$_img_name = $v['pt_img'];
	curl_async('http://'.$system['host'].OD_PROGRAM_DIR.'/app.resize_img.php?_img='.$_img_name.'&_path='.$app_path);
}