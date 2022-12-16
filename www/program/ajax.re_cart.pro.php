<?php
# 게시글 처리 프로세스
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행



// --> 옵션/장바구니/비회원 구매를 위한 쿠키 적용여부 파악
cookie_chk();

/*
	-- LCY
	### 장바구니 다시 담기
	- 넘겨저온 변수 : ordernum, opcode, if_stats
*/
$res = _MQ("
	select op.*,
	p.p_stock
	from smart_order_product as op
	inner join smart_product as p on ( p.p_code=op.op_pcode )
	inner join smart_order as o on (o.o_ordernum = op.op_oordernum)
	where op_oordernum='{$ordernum}'
	and op_pcode = '{$opcode}'
");
$snum = sizeof($res);
if($snum <= 0){
	echo json_encode(array('result'=>'fail'));
	exit;
}


$pque = "select p_option_type_chk from smart_product where p_code='". $opcode ."' ";
$pr = _MQ($pque);
$p_option_type_chk = $pr[p_option_type_chk];

if($p_option_type_chk == 'nooption'){ // 옵션이 없을 경우

	// 이미 담긴 상품인지 체크
	$cnt_res = _MQ("select count(*) as cnt from smart_cart  where c_pcode = '". $opcode ."' and c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and  c_pouid = '0'");
	$cnt_tmp = $cnt_res['cnt'];
	if($cnt_tmp > 0){
		echo json_encode(array('result'=>'fail'));
		exit;
	}

    if($res['p_stock'] <= 0){  // 재고량이 0 일경우
		echo json_encode(array('result'=>'fail'));
		exit;
    }

    $op_cnt_res = $res['p_stock'] >= $res['op_cnt'] ? $res['op_cnt'] : $res['p_stock'];  // 재고량이 있을경우 다시구매 갯수로, 그렇지 않을 경우 남은 재고량으로




	// 상품공급가를 구한다 - 정산형태가 수수료일경우에는 수수료로 공급가를 계산해서 넣는다.
	$pinfo = get_product_info($opcode);
	$c_supply_price = $pinfo[p_commission_type] == "공급가" ? $pinfo[p_sPrice] : $pinfo[p_price] - round($pinfo[p_price] * $pinfo[p_sPersent] / 100);

	for($unset_i=0;$unset_i<=10;$unset_i++){ // 옵션이 없는 상품은 추가 옵션을 사용해서는 안될것같음
		$unset_name ='add_option_select_'.$unset_i;
		unset($$unset_name);
	}

	// {{{회원등급혜택}}}
	if(is_login() == true && $pinfo['p_groupset_use'] == 'Y'  ){ // 로그인 중이고 등급할인혜택 적용이 Y 라면
		$c_old_price = $pinfo['p_price'];
		$c_old_point = ( ($c_old_price*$c_cnt)*($pinfo[p_point_per]/100) );
		//$c_price = $c_old_price-getGroupSetPer( $c_old_price,'price',$pinfo['p_code']);
		//$c_point = $c_old_point + getGroupSetPer( ($c_old_price*$c_cnt),'point',$pinfo['p_code']);
		$c_groupset_price_per = $groupSetInfo['mgs_sale_price_per'] > 0 ? $groupSetInfo['mgs_sale_price_per'] : 0;
		$c_groupset_point_per = $groupSetInfo['mgs_give_point_per'] > 0 ? $groupSetInfo['mgs_give_point_per'] : 0;
	}else{
		$c_old_price = $pinfo['p_price'];
		$c_old_point = ( ($c_old_price*$c_cnt)*($pinfo[p_point_per]/100) );
		//$c_price = $c_old_price;
		//$c_point = $c_old_point;
		$c_groupset_price_per = 0;
		$c_groupset_point_per = 0;
	}

	$add_que = "
		,c_old_price = '".$c_old_price."'
		,c_old_point = '".floor($c_old_point)."'
		,c_groupset_price_per = '".$c_groupset_price_per."'
		,c_groupset_point_per = '".$c_groupset_point_per."'
	";
	// {{{회원등급혜택}}}

	$sque = "
		insert smart_cart set
		  c_pcode = '". $opcode ."'
		, c_cnt = '".$op_cnt_res."'
		, c_pouid = '0'
		, c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'
		, c_rdate = now()
		, c_supply_price = '".$c_supply_price."'
		, c_price = (select p_price from smart_product where p_code='".$opcode."')
		, c_point = (select p_price*(p_point_per/100)*".$op_cnt_res." from smart_product where p_code='".$opcode."')
		, c_direct				= 'N'
		, c_is_addoption = 'N'
		, c_addoption_parent = '0'

		".$add_que."
	";
	_MQ_noreturn($sque);


	echo json_encode(array('result'=>'success','console'=>$pinfo['p_price']));
	exit;


}

// 옵션이 있을 경우 처리
$res_que = _MQ_assoc("
	select *
	from smart_order_product
	where op_oordernum='{$ordernum}'
	and op_pcode = '{$opcode}'
");

if(count($res_que) <= 0  ){
	echo json_encode(array('result'=>'fail'));
	exit;
}

$code = $opcode;
$app_cnt = 0;
foreach($res_que as $k=>$v){

	if($v['op_is_addoption'] == 'Y'){ // 추가 옵션일 경우 추가옵션정보를 가져온다

    // 상품정보, 옵션정보 추출
    include_once(OD_PROGRAM_ROOT."/add_option_select.top_inc.php");

	}else{ // 일반 옵션이라면
    // 상품정보, 옵션정보 추출

    include_once(OD_PROGRAM_ROOT."/option_select.top_inc.php");
	}


	if(count($pores ) > 0){

		 if($arr_option_data[$v['op_pouid']]['option_cnt']  <= 0){ // 옵션 수량이 0보다 작다면
		 	continue;
		 }
	}else{
		continue;
	}

	 $op_cnt_res = $arr_option_data[$v['op_pouid']]['option_cnt'] >= $res['op_cnt'] ? $res['op_cnt'] : $arr_option_data[$v['op_pouid']]['option_cnt'];

	// 상품공급가를 구한다 - 정산형태가 수수료일경우에는 수수료로 공급가를 계산해서 넣는다.
	$pinfo = get_product_info($code);

	$c_supply_price = $pinfo[p_commission_type] == "공급가" ? $arr_option_data[$v['op_pouid']]['option_supplyprice'] : $arr_option_data[$v['op_pouid']]['option_price'] - round($arr_option_data[$v['op_pouid']]['option_price'] * $pinfo[p_sPersent] / 100);
	// 장바구니 넣기

	// {{{회원등급혜택}}}
	if(is_login() == true && $pinfo['p_groupset_use'] == 'Y'  ){ // 로그인 중이고 등급할인혜택 적용이 Y 라면
		$c_old_price = $arr_option_data[$v[op_pouid]]['option_price'];
		$c_old_point = ( ($c_old_price*$v[op_cnt])*($pinfo[p_point_per]/100) );
		$c_price = $c_old_price - getGroupSetPer( $c_old_price,'price',$code);
		$c_point = $c_old_point + getGroupSetPer( ($c_old_price*$v[op_cnt]),'point',$code);
		$c_groupset_price_per = $groupSetInfo['mgs_sale_price_per'] > 0 ? $groupSetInfo['mgs_sale_price_per'] : 0;
		$c_groupset_point_per = $groupSetInfo['mgs_give_point_per'] > 0 ? $groupSetInfo['mgs_give_point_per'] : 0;
	}else{
		$c_old_price = $arr_option_data[$v[op_pouid]]['option_price']; // 기존금액
		$c_old_point = ( ($c_old_price*$v[op_cnt])*($pinfo[p_point_per]/100) );  // 기존금액
		$c_price = $c_old_price;
		$c_point = $c_old_point;
		$c_groupset_price_per = 0;
		$c_groupset_point_per = 0;
	}
	$add_que = "
		,c_old_price = '".$c_old_price."'
		,c_old_point = '".floor($c_old_point)."'
		,c_groupset_price_per = '".$c_groupset_price_per."'
		,c_groupset_point_per = '".$c_groupset_point_per."'
	";
	// {{{회원등급혜택}}}

	$sque = "
		insert smart_cart set
			c_pcode          = '". $code ."'
			, c_option1      = '". mysql_real_escape_string($arr_option_data[$v['op_pouid']]['option_name1'])."'
			, c_option2      = '". mysql_real_escape_string($arr_option_data[$v['op_pouid']]['option_name2'])."'
			, c_option3      = '". mysql_real_escape_string($arr_option_data[$v['op_pouid']]['option_name3'])."'
			, c_cnt          = '".$op_cnt_res."'
			, c_pouid        = '".$v['op_pouid']."'
			, c_cookie       = '".$_COOKIE["AuthShopCOOKIEID"]."'
			, c_rdate        = now()
			, c_supply_price = '". $c_supply_price."'
			, c_price        = '". $arr_option_data[$v['op_pouid']]['option_price']."'
			, c_point        = '". (($arr_option_data[$v['op_pouid']]['option_price']*$op_cnt_res)*($pinfo[p_point_per]/100))."'
			, c_direct				= 'N'
			, c_is_addoption = '". $v['op_is_addoption']."'
			, c_addoption_parent = '". $v['op_addoption_parent']."'

			".$add_que."
	";
	_MQ_noreturn($sque);
	$app_cnt ++;
}


if($app_cnt > 0) {
	echo json_encode(array('result'=>'success'));
}else{
	echo json_encode(array('result'=>'fail'));
}
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행