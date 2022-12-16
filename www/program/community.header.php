<?php
exit; // 사용하는지 여부 체크
# 스킨의 파일을 바로 부를 경우 사용
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// -- 커뮤니티로 적용된 게시판을 가져온다.
$bbsList = _MQ_assoc("select bi_uid, bi_name, bi_skin, bi_list_type from smart_bbs_info where bi_view_type = 'community' and bi_view = 'Y' order by bi_view_idx asc ");

// 탑네비 메뉴 리스트
$TopNavArr = $normalMenu =  $boardMenu  = array();

// -- 게시판메뉴
foreach($bbsList as $k=>$v) {
	$chkHit = preg_match("/(board.)/",$pn) == true && $_menu == $v['bi_uid'] ? true : false;
	$skinNameView = $skinNameViewVal === true ? '('.$v['bi_skin'].')' : null;
	$boardMenu[$v['bi_name'].$skinNameView] = array(
		'link'=>'/?pn=board.list&_menu='.$v['bi_uid'],
		'hit'=> $chkHit
	);
}

// -- 일반메뉴 첫번째
$normalMenu[0] = array(
	'상품 평가'=>array(
		'link'=>'/?pn=faq.list',
		'hit'=>($pn == 'faq.list'?true:false)
	),
	'상품 문의'=>array(
		'link'=>'/?pn=service.qna.list',
		'hit'=>($pn == 'service.qna.list'?true:false)
	)
);

// -- 일반메뉴 두번째
$normalMenu[1] = array(
	'제휴문의'=>array(
		'link'=>'/?pn=service.partner.form',
		'hit'=>($pn == 'service.partner.form'?true:false)
	),
);

// -- 순서조절 하여 추가
$TopNavArr = array_merge($normalMenu[0] , $boardMenu , $normalMenu[1] );

include_once($SkinData['skin_root'].'/community.header.php'); // 스킨폴더에서 해당 스킨 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행

?>