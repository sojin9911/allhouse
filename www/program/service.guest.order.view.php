<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



# 데이터 조회
$que = " select o.* , oc.oc_tid , oc.oc_content
			from smart_order as o
			left join smart_order_cardlog as oc on (oc.oc_oordernum=o.o_ordernum)
			where replace(o.o_ordernum,'-','')='".str_replace('-','',$_onum)."' and o.o_ordernum!='' and o_oname = '".$_oname."'
";
$row = _MQ($que);
$ordernum = $row['o_ordernum'];
if($row['o_ordernum']) {
	$sres = _MQ_assoc("
		select op.*,o.*, p.p_name,p.p_cpid, p.p_img_list , p.p_img_list_square , p.p_code, p.p_coupon,p.p_stock, p.p_shoppingPay, p_shoppingPay_use
		from smart_order as o
		left join smart_order_product as op on (op.op_oordernum = o.o_ordernum )
		left join smart_product as p on ( p.p_code=op.op_pcode )
		where op_oordernum='{$ordernum}'
		group by op_pcode
		order by op_uid
	");
}



include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행