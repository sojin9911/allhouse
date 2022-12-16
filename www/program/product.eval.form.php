<?php
// defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// SSJ : 상품후기 작성제한 기능강화 : 2020-08-13 : 상품후기 작성전 관리자 설정에 따른 안내문구 노출
$trigger_eval_msg = ''; // 상품후기 작성제한 시 문구
// 포토후기 작성횟수 추출
$talk_type = 'eval'; // 상품후기
$que = "
    select count(*) as cnt
    from smart_product_talk
    where 1
        and pt_type = '".$arr_p_talk_type[$talk_type]."'
        and pt_pcode = '". $pcode ."'
        and pt_inid = '".get_userid()."'
        and pt_depth = 1
";
$er = _MQ($que);

if($siteInfo['s_producteval_limit']<>'N'){
    // 구매내역 추출
    $que = "
        select count(*) as cnt
        from smart_order as o
        left join smart_order_product as op on (o.o_ordernum = op.op_oordernum)
        where 1
            and o.o_memtype = 'Y'
            and o.o_mid = '". get_userid() ."'
            and o.o_paystatus = 'Y'
            and o.o_canceled = 'N'
            and op.op_pcode = '". $pcode ."'
            and op.op_cancel = 'N'
    ";
    $or = _MQ($que);

    if($or['cnt'] < 1){
        $trigger_eval_msg = '상품후기는 상품을 구매한 회원만 작성 가능합니다.';
    }
    else if($siteInfo['s_producteval_limit']=='B'){
        if($or['cnt'] <= $er['cnt']){
            $trigger_eval_msg = '상품후기는 상품을 구매한 횟수 만큼만 등록할 수 있습니다.';
        }
    }
}
// SSJ : 상품후기 작성제한 기능강화 : 2020-08-13 : 상품후기 작성전 관리자 설정에 따른 안내문구 노출


include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행