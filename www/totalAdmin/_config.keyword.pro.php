<?php
include_once('inc.php');
// -- LCY :: 상품키워드 추가 
$sql_que .=" , s_faq_keyword = '".$s_faq_keyword."'  ";
// 설정값 업데이트
$que = "
	update smart_setup set
		  s_recommend_keyword = '{$s_recommend_keyword}'
		, s_recommend_hashtag = '{$s_recommend_hashtag}'
		, s_faq_keyword = '{$s_faq_keyword}' 
	where s_uid = '1'
";
_MQ_noreturn($que);

// 설정페이지 이동
error_loc('_config.keyword.php');