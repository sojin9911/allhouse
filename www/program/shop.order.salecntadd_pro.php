<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



// *** 결제확인 시 --> 상품 수량 차감과 판매량 증가 ***

// - 주문정보 추출 ---
$opsr = get_order_product_info($_ordernum);

foreach($opsr as $k => $v) {

    if($v['op_pouid']){
        // 일반옵션인지 추가옵션인지 구분하여 처리
        if($v['op_is_addoption'] == 'Y'){
            // 판매된 옵션 수량 차감 및 판매량 증가
            _MQ_noreturn("update smart_product_addoption set pao_salecnt = pao_salecnt + '".$v['op_cnt']."' , pao_cnt = pao_cnt - '".$v['op_cnt']."' where pao_uid='".$v['op_pouid']."'");
        }else{
            // 판매된 옵션 수량 차감 및 판매량 증가
            _MQ_noreturn("update smart_product_option set po_salecnt = po_salecnt + '".$v['op_cnt']."' , po_cnt = po_cnt - '".$v['op_cnt']."' where po_uid='".$v['op_pouid']."'");
            // 판매된 상품 수량 차감 및 판매량 증가
            _MQ_noreturn("update smart_product set p_salecnt = p_salecnt + '".$v['op_cnt']."' ,p_stock = p_stock - '".$v['op_cnt']."' where p_code = '".$v['op_pcode']."'");
        }
    }else{
        // 판매된 상품 수량 차감 및 판매량 증가
        _MQ_noreturn("update smart_product set p_salecnt = p_salecnt + '".$v['op_cnt']."' ,p_stock = p_stock - '".$v['op_cnt']."' where p_code = '".$v['op_pcode']."'");
    }

    // SSJ : 상품 품절 체크 - p_soldout_chk 정보 업데이트 : 2019-02-11
    product_soldout_check($v['op_pcode']);
}




actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행