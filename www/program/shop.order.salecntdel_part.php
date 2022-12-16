<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


// 2018-11-19 SSJ :: 단일 상품 재고 증가 및 판매량 차감 :: $_ordernum , $_uid
// *** 결제확인 시 --> 상품 수량 증가와 판매량 차감 ***

// - 주문정보 추출 ---
$opsr = _MQ("
	SELECT * FROM smart_order_product as op
	inner join smart_order as o on (o.o_ordernum = op.op_oordernum)
	WHERE o.o_ordernum='" . $_ordernum . "' and op_uid = '".$_uid."'
");

if($opsr['op_uid'] <> ''){

    if($opsr['op_pouid']){
        // 일반옵션인지 추가옵션인지 구분하여 처리
        if($opsr['op_is_addoption'] != 'Y'){
            // 추가옵션이 포함된 옵션인지 체크
            $add_res = _MQ_assoc(" select * from smart_order_product where op_is_addoption = 'Y' and op_addoption_parent = '".$opsr['op_pouid']."' and op_oordernum = '".$opsr['op_oordernum']."' ");
            if( count($add_res) > 0 ) {
                foreach($add_res as $adk=>$adv) {
                    // 판매된 옵션 수량 차감 및 판매량 증가
                    _MQ_noreturn("update smart_product_addoption set pao_salecnt = pao_salecnt - '".$adv['op_cnt']."' , pao_cnt = pao_cnt + '".$adv['op_cnt']."' where pao_uid='".$adv['op_pouid']."'");
                }
            }
            // 판매된 옵션 수량 차감 및 판매량 증가
            _MQ_noreturn("update smart_product_option set po_salecnt = po_salecnt - '".$opsr['op_cnt']."' , po_cnt = po_cnt + '".$opsr['op_cnt']."' where po_uid='".$opsr['op_pouid']."'");
            // 판매된 상품 수량 차감 및 판매량 증가
            _MQ_noreturn("update smart_product set p_salecnt = p_salecnt - '".$opsr['op_cnt']."' ,p_stock = p_stock + '".$opsr['op_cnt']."' where p_code = '".$opsr['op_pcode']."'");
        }
    }else{
        // 판매된 상품 수량 차감 및 판매량 증가
        _MQ_noreturn("update smart_product set p_salecnt = p_salecnt - '".$opsr['op_cnt']."' ,p_stock = p_stock + '".$opsr['op_cnt']."' where p_code = '".$opsr['op_pcode']."'");
    }

    // SSJ : 상품 품절 체크 - p_soldout_chk 정보 업데이트 : 2019-02-11
    product_soldout_check($opsr['op_pcode']);
}




actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행