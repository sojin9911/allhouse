<?php

/*
* SSJ : 리뷰이미지 일괄 리사이즈  : 2021-05-24
* -- curl_async를 통하여 이미지 리사이즈
*/

ini_set('memory_limit' , '-1');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
include_once(dirname(__file__)."/../include/img_support.php");
if($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']) return; // 동일 서버안에서만 동작 하도록 => curl_async을 통해서만 실행됨

$app_path = $_path ? $_path : dirname(__FILE__)."/..".IMG_DIR_PRODUCT;
$_img_name = $_img ? $_img : null;
if(!$_img_name){

	return false;

}else{

	 //-- SSJ : 상품후기 이미지 리사이즈 및 회전방지 : 2021-05-24 ----
	 $max_width = 1000;
	ImgRotate($app_path.$_img_name); // 이미지 회전값 보정
	$_size = getimagesize($app_path.$_img_name);
	if($_size[0] > $max_width){
		$_thumb_w = $max_width;
		$_thumb_h  = $_size[1] * $_thumb_w / $_size[0];
		ImgThumb($app_path, $_img_name, $_thumb_w, $_thumb_h, 'resize');
	}
	 //-- SSJ : 상품후기 이미지 리사이즈 및 회전방지 : 2021-05-24 ----

}