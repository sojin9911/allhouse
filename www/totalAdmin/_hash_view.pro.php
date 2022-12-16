<?php
include_once('inc.php');


// 상품리스트 해시태그 노출 설정
$_product_list_hashtag_cnt = rm_str($_product_list_hashtag_cnt);
$_product_list_hashtag_view = ($_product_list_hashtag_view?$_product_list_hashtag_view:'Y');
_MQ_noreturn("
	update smart_setup set 
		s_product_list_hashtag_cnt = '{$_product_list_hashtag_cnt}',
		s_product_list_hashtag_shuffle = '{$_product_list_hashtag_shuffle}',
		s_product_list_hashtag_view = '{$_product_list_hashtag_view}'
	where s_uid = 1
");
error_loc('_hash_view.php');