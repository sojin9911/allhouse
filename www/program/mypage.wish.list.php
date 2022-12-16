<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행
if( get_userid() == false){ error_loc_msg("/?pn=member.login.form&_rurl=".urlencode("/?".$_SERVER['QUERY_STRING']),"로그인이 필요한 서비스 입니다."); }

// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_POST,$_GET))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---


# 기본처리
member_chk();
if(is_login()) $indr = $mem_info; // 개인정보 추출


# 데이터 조회
$listmaxcount = 10 ; // 페이지당 갯수
if($pass_limit) $listmaxcount = $pass_limit;
if(!$listpg) $listpg = 1;
$count = $listpg * $listmaxcount - $listmaxcount;
$s_query = "
	from smart_product_wish as pw
	inner join smart_product as p on ( p.p_code=pw.pw_pcode )
	where pw.pw_inid='". get_userid() ."'
";
$res = _MQ(" select count(*)  as cnt $s_query ");
$TotalCount = $res[cnt];
$Page = ceil($TotalCount / $listmaxcount);
$s_que = "
    select
        pw.*, p.* ,
        (select count(*) from smart_product_wish as pw2 where pw2.pw_pcode=pw.pw_pcode) as cnt_product_wish
        ,if(p_soldout_chk='N',p_stock,0) as p_stock
    $s_query
    order by pw_uid desc limit $count , $listmaxcount
";
$row = _MQ_assoc($s_que);



include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행