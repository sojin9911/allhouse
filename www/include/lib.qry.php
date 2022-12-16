<?php

// 로그인 체크
function loginchk_insert($_id , $_type){
	global $_SERVER;
	$que = "insert smart_loginchk set lc_mid='". $_id ."' , lc_type='".$_type."' , lc_ip='".$_SERVER["REMOTE_ADDR"]."' , lc_rdate=now() ";
	_MQ_noreturn($que);
}

// 배송비 정보를 추출한다. (상품코드로 부터...)
// 상품지정값인지, 입점업체지정값인지, 쇼핑몰기본값인지 체크하여 각각 처리한다. (우선순위 : 상품>입점업체>쇼핑몰기본값)
// 리턴값 : freePrice : 무료배송비,
//					price : 기본배송비,
//					from : 배송비정보출처(global : 쇼핑몰기본정보, company : 입점업체정보, product : 상품정보) ,
//					status : 배송결과값(1:무료배송/2:조건부무료배송/3:무조건배송비부과)
function get_delivery_info($pcode){

	// 상품정보를 추출한다.
	$p_r = _MQ("select p_shoppingPay_use,p_shoppingPay,p_shoppingPayFree,p_cpid, p_shoppingPayPdPrice, p_shoppingPayPfPrice from smart_product where p_code = '".$pcode."'");
	// 업체정보 추출
	$cp_delinfo = get_delivery_info_from_company($p_r['p_cpid']);

	if($p_r['p_shoppingPay_use'] == "Y") {		// 상품 배송비 정책을 사용한다.
		$dinfo['price'] = $p_r['p_shoppingPay'];
		$dinfo['freePrice'] = $p_r['p_shoppingPayFree'];
		$dinfo['from']	= "product";	// 상품
		$dinfo['status'] = $dinfo['price'] == 0 ? "1" : ($dinfo['freePrice'] == 0 ? "3" : "2");
		$dinfo['del_company'] = $cp_delinfo['del_company'];	// 배송업체
	}
	else if($p_r['p_shoppingPay_use'] == "F") {//무료배송비
		$dinfo['price'] = $p_r['p_shoppingPay'];
		$dinfo['freePrice'] = $p_r['p_shoppingPayFree'];
		$dinfo['from']	= "product";	// 상품
		$dinfo['status'] = 1;
		$dinfo['del_company'] = $cp_delinfo['del_company'];	// 배송업체
	}
	else if($p_r['p_shoppingPay_use'] == "P" ) {//개별배송비
		$dinfo['price'] = $p_r['p_shoppingPayPdPrice'];
		$dinfo['freePrice'] = $p_r['p_shoppingPayPfPrice'];
		$dinfo['from']	= "product";	// 상품
		$dinfo['status'] =$dinfo['price'] == 0 ? "1" : ($dinfo['freePrice'] == 0 ? "3" : "2");
		$dinfo['del_company'] = $cp_delinfo['del_company'];	// 배송업체
	}
	else {
		$dinfo = $cp_delinfo;
	}

	return $dinfo;
}


// 배송비 정보를 추출한다. (입점업체로 부터...)
// 상품지정값인지, 입점업체지정값인지, 쇼핑몰기본값인지 체크하여 각각 처리한다. (우선순위 : 상품>입점업체>쇼핑몰기본값)
// 무료배송비, 기본배송비, 배송비정보출처(global : 쇼핑몰기본정보, company : 입점업체정보, product : 상품정보) , 배송결과값(1:무료배송/2:조건부무료배송/3:무조건배송비부과)
function get_delivery_info_from_company($cpid){

	global $siteInfo , $SubAdminMode;

	// 업체정보를 추출
	$cp_r = _MQ("select * from smart_company where cp_id = '".$cpid."'");
	if($cp_r['cp_delivery_use'] == "Y" && $SubAdminMode ) {								// 입점업체 배송비 정책을 사용한다.
		$dinfo['price'] = $cp_r['cp_delivery_price'];
		$dinfo['freePrice'] = $cp_r['cp_delivery_freeprice'];
		$dinfo['from'] = "company";	// 입점업체
		$dinfo['del_company'] = $cp_r['cp_delivery_company'];	// 배송업체

	}	else {															// 쇼핑몰  배송비 정책을 사용한다.
		$dinfo['price'] = $siteInfo['s_delprice'];
		$dinfo['freePrice'] = $siteInfo['s_delprice_free'];
		$dinfo['from'] = "global";	// 쇼핑몰
		$dinfo['del_company'] = $siteInfo['s_del_company'];	// 배송업체

	}

	$dinfo['status'] = $dinfo['price'] == 0 ? "1" : ($dinfo['freePrice'] == 0 ? "3" : "2");

	return $dinfo;
}


// 개인회원정보 불러오기
function _individual_info( $id ){
	$r = _MQ(" select * from smart_individual where in_id='{$id}' ");
	if( $r['in_out'] == "Y" ) {
		$r['in_name'] = "guest";
	}
	return $r;
}



// 기업회원정보 불러오기
function _company_info( $id ){
	$r = _MQ(" select cp.* , ind.in_name from smart_company as cp left join smart_individual as ind on (ind.in_id=cp.cp_id) where cp.cp_id='{$id}' ");
	return $r;
}


// *** 상품정보 추출 - ***
//		- limit : 상품추출갯수 - 1개이상
//		- condition : 추출조건 (MIX) 배열형태 적용 예) array('p_newview'=>'Y')
function list_product($limit , $condition){
	global $_COOKIE;
	$sque = "";
	if( sizeof($condition) > 0) {
		foreach( $condition as $k=>$v ){
			$sque .= " and " . $k . "='" . $v . "' ";
		}
	}
	$r = _MQ_assoc("
		select
			p.* , cp.cp_name , ind.in_name
			".(is_login() ? " , (select count(*) from smart_product_wish as pw where pw.pw_pcode=p.p_code and pw.pw_inid='".get_userid()."' ) as cnt_pw" : "")."
		from smart_product as p
		inner join smart_company as cp on (cp.cp_id=p.p_cpid)
		inner join smart_individual as ind on (cp.cp_id=ind.in_id)
		where p_view='Y' and p_option_valid_chk = 'Y' " . $sque ."
		order by p_idx asc , p_rdate desc limit 0, " . $limit
	);
	return $r;
}
// *** 상품정보 추출 - ***




// *** 카테고리정보 추출 - ***
function info_category($uid){
	$r = _MQ(" select * from smart_category where c_view='Y' and c_uid='".$uid."'");

	if($r['c_depth']==3) {
		$r['depth3_cuid'] = $r['c_uid'];
		$r['depth3_cname'] = $r['c_name'];

		list($r['depth1_cuid'],$r['depth2_cuid']) = explode(",",_MQ_result("select c_parent from smart_category where c_uid = '".$r['c_uid']."' and c_depth=3"));
		$r['depth2_cname'] = _MQ_result("select c_name from smart_category where c_uid = '".$r['depth2_cuid']."'");
		$r['depth1_cname'] = _MQ_result("select c_name from smart_category where c_uid = '".$r['depth1_cuid']."'");
	}else if($r['c_depth']==2) {
		$r['depth2_cuid'] = $r['c_uid'];
		$r['depth2_cname'] = $r['c_name'];
		$r['depth1_cuid'] = _MQ_result("select c_parent from smart_category where c_uid = '".$r['c_uid']."' and c_depth=2");
		$r['depth1_cname'] = _MQ_result("select c_name from smart_category where c_uid = '".$r['depth1_cuid']."'");
	}else {
		$r['depth1_cuid'] = $r['c_uid'];
		$r['depth1_cname'] = $r['c_name'];
	}
	return $r;
}
// *** 배너정보 추출 - ***


// *** 상점정보 추출 - ***
//		- limit : 상품추출갯수 - 1개이상
//		- condition : 추출조건 (MIX) 배열형태 적용 예) array('p_newview'=>'Y')
function list_company($limit , $arr = NULL){
	global $_COOKIE;
	$sque = "";
	if( sizeof($arr) > 0) {
		foreach( $arr as $k=>$v ){
			$sque .= " and " . $k . "='" . $v . "' ";
		}
	}
	$r = _MQ_assoc("
		select
			cp.*
		from smart_company as cp
		where 1 " . $sque ."
		order by cp.cp_rdate desc limit 0, " . $limit
	);
	return $r;
}
// *** 상품정보 추출 - ***




// *** 배너정보 추출 - ***
//		- loc : 배너위치 - /include/var.php 참조
//		- limit : 배너추출갯수 - 1개이상
//		- date_type : 리턴 데이터값 형식 (html : 소스 , data : 일반데이터 )
//		- <a href=링크 target=타겟>이미지</a> 형태의 배열로 return

function info_banner($loc , $limit=1, $return_type = 'html'){
	$arr = array();
	$r = _MQ_assoc("select * from smart_banner where b_loc='" . $loc . "' and b_view='Y' and ( (b_sdate <= CURDATE() and b_edate >= CURDATE() ) or b_none_limit = 'Y' ) order by b_idx asc , b_uid asc limit 0, " . $limit );
	foreach($r as $k=>$v){
		if($return_type == "data" ) {
			$arr[$k] = $v;
		} else {
			$arr[$k] = "<img src='".IMG_DIR_BANNER.$v['b_img']."' alt='".stripslashes($v['b_title'])."' />";
			if($v['b_target'] != '_none' && isset($v['b_link'])) $arr[$k] = "<A HREF='".($v['b_link'] ? $v['b_link'] : "#")."' target='".$v['b_target']."'>".$arr[$k]."</A>";
		}

	}
	return $arr;
}
// *** 배너정보 추출 - ***



// 쇼핑몰 포인트 로그 입력
// 인자 : 회원아이디(이메일) ,
//				타이틀 ,
//				적용포인트 ,		- 0이나 다른숫자가 들어오면 그냥 리턴
//				적용상태(Y,N),
//				처리일(0~ 숫자) - 0은 즉시 처리
function shop_pointlog_insert( $_id , $_title , $_point , $_status , $_pro_date){

	$_point = rm_comma($_point); // 콤마제거
	if(!is_numeric($_point) || $_point==0) return;	 // 포인트가 0원이면 처리 하지 않는다.

	if(!is_numeric($_pro_date)) {	// 잘못입력되었거나, 숫자가 아닌경우 30일후 적립처리한다.
		$_appdate = date("Y-m-d" , strtotime("+30day"));
	} else {
		$_appdate = date("Y-m-d" , strtotime("+".$_pro_date."day"));
	}
	_MQ_noreturn("
		insert smart_point_log set
			  pl_inid='".$_id."'
			, pl_title = '".addslashes($_title)."'
			, pl_point = ".$_point."
			, pl_status='".$_status."'
			, pl_appdate = '".$_appdate."'
			, pl_rdate = now()
	");
	$uid = mysql_insert_id();

	if($_appdate == DATE('Y-m-d')) {

		$apply_id = $_id;
		$row_point = _MQ(" select * from smart_individual where in_id = '".$apply_id."' ");

		// 회원아이디가 있을때만 실행
		if($row_point['in_id']){
			$apply_point = $_point;
			$calc_point = $row_point['in_point'] + $apply_point;
			if($calc_point < 0){
				$apply_point = $row_point['in_point']*(-1);
				$calc_point = 0;
			}
			// 회원 포인트 UPDATE
			_MQ_noreturn("update smart_individual set in_point = ". $calc_point ." where  in_id = '".$apply_id."' ");
			// 포인트 UPDATE
			_MQ_noreturn("update smart_point_log set pl_point_before = '". $row_point['in_point'] ."', pl_point_apply = '". $apply_point ."', pl_point_after = '". $calc_point ."', pl_status = 'Y', pl_adate = now() where  pl_uid = '". $uid ."'");
		}
		else{
			_MQ_noreturn("update smart_point_log set pl_title = '[취소] ". addslashes($_title) ." (회원아이디 불일치)', pl_status = 'C' where  pl_uid = '". $uid ."'");
		}
	}
	// 포인트 적립 클론 실행(즉시 적립을 위해)
	//point_update();
}


// 쇼핑몰 포인트 로그 삭제 | shop_pointlog_delete( 회원아이디(이메일) , 타이틀, 주문번호, 주문 상품 고유 번호 )
// - 기본적으로 포인트 로그 삭제는 30일 이내에 이루어져야 함 ---
// - 주문의 경우 타이틀에 주문번호 명시되어 있으므로 삭제시 적용가능 ---
// - 상품 구매 시 적립된 포인트, 구매시 적립된 쿠폰 포인트 반환 추가 (부분취소 kms 2019-03-13)
function shop_pointlog_delete( $_id , $_title,$_ordernum='',$_opuid='' ){
	// 2014-12-09 포인트취소 패치 :: 이미포인트가 사용되었다면 회원포인트차감. 단, 음수이면 0으로
	if($_id <> '' && $_title <> ''){
		$res = _MQ(" select * from smart_point_log where pl_inid='".$_id."' and pl_title = '".$_title."' ");
		// 부분 취소 포인트 수정 (부분취소 kms 2019-03-13)
		if($_ordernum <> '' && $_opuid <> ''){
			$sop_res = _MQ("select op_point from smart_order_product where op_oordernum = '".$_ordernum."' and op_uid= '".$_opuid."' "); // 주문 상품 정보
			$so_res = _MQ("select o_coupon_individual_uid from smart_order where o_ordernum = '".$_ordernum."' "); // 쿠폰 정보를 부르기 위함
		}
	}
	else if($_id <> '' && $_title == '')  $res = _MQ(" select * from smart_point_log where pl_uid='".$_id."' ");

	if($res['pl_status']=='Y'){
		// 지급후에는 포인트 차감로그 추가 - 지급완료된 로그는 이후 변경하지 않는다.
		// 상품 구매시 적립된 포인트, 구매시 적립 쿠폰 반환 (부분취소 kms 2019-03-14)
		if(count($sop_res)>0){
			// 부분 취소가 마지막 이고, 쿠폰이 포인트 적립이라면 포인트 금액 취소. (부분취소 kms 2019-03-14)
			$sop_last_chk = _MQ("select count(*) as cnt from smart_order_product where op_oordernum = '".$_ordernum."' and op_uid != '".$_opuid."' and op_cancel != 'Y' ");
			if($sop_last_chk['cnt'] == 0 ){
				$coupon_uid = explode(",",$so_res['o_coupon_individual_uid']); // 쿠폰 사용 체크
				if(count($coupon_uid) > 1){ // 쿠폰 중복 사용시
					foreach ($coupon_uid as $k => $v) {
						$coupon = _MQ("select coup_type, coup_price from smart_individual_coupon where coup_uid = '".$v."'"); // 쿠폰 정보를 불러옴
						if($coupon['coup_type'] == 'save'){ // 구매시 포인트 적립 쿠폰이라면 적립된 금액 취소.
							shop_pointlog_insert( $res['pl_inid'] , '[취소]' . $res['pl_title'] , $coupon['coup_price']*(-1) , 'N' , 0);
						}
					}
				}else{ // 쿠폰 단일 사용시
					$coupon = _MQ("select coup_type, coup_price from smart_individual_coupon where coup_uid = '".$so_res['o_coupon_individual_uid']."'");
					if($coupon['coup_type'] == 'save') // 구매시 포인트 적립 쿠폰이라면 적립된 금액 취소.
						shop_pointlog_insert( $res['pl_inid'] , '[취소]' . $res['pl_title'] , $coupon['coup_price']*(-1) , 'N' , 0);
				}

			}
			shop_pointlog_insert( $res['pl_inid'] , '[취소]' . $res['pl_title'] , $sop_res['op_point']*(-1) , 'N' , 0); 	// 구매한 상품 적립 포인트 취소
		}
		else { shop_pointlog_insert( $res['pl_inid'] , '[취소]' . $res['pl_title'] , $res['pl_point']*(-1) , 'N' , 0); }
	}else if($res['pl_status']=='N'){
		// 지급대기일경우 상태만 취소로 변경 - 지급대기상태만 취소상태로 변경
		_MQ_noreturn(" update smart_point_log set pl_status = 'C' where pl_uid='".$res['pl_uid']."' ");
	}
}


// 주문상태를 업데이트 한다.
// 설명 : order_product 의 각 배송상태를 확인하여, order 의 주문상태를 업데이트 하여 변경한다.
//				모든 상품의 주문이 배송완료		=> 주문상태 : 배송완료
//				상품이 하나라도 배송중이면		=> 주문상태 : 배송중
//				하나도 배송완료나,진행이없으면=> 주문상태 : 접수완료 or 접수대기
// 인자 : 주문번호
function order_status_update($ordernum) {

	if(!$ordernum) return;
	$order_info = get_order_info($ordernum);
	$insert_add = "";

	// 취소된 주문은 패스..
	if($order_info['o_canceled'] == "Y") $order_status ="주문취소";
	else if($order_info['o_canceled'] == "R") $order_status ="환불요청";
	else if($order_info['o_paystatus'] == "N") $order_status ="접수대기";
	else {
		//$order_product_info_array = _MQ_assoc("select * from smart_order_product where op_oordernum = '".$ordernum."'");
		/* ----  주문상품의 배송상태 확인 :: 부분취소된 상품 제외 2017-03-06 SSJ ---- */
		$order_product_info_array = _MQ_assoc("select * from smart_order_product where op_oordernum = '".$ordernum."' and op_cancel != 'Y' ");
		/* ----  주문상품의 배송상태 확인 :: 부분취소된 상품 제외 2017-03-06 SSJ ---- */

		foreach($order_product_info_array as $k => $v) {
			$count['전체갯수']++;
			$count[$v['op_sendstatus']]++;
		}

//		if($count['배송완료'] == $count['전체갯수']) {
//			$order_status ="배송완료";
//		}
//		else if($count['배송중'] == $count['전체갯수']) {
//			$order_status ="배송중";
//		}
//		else if($count['구매발주'] == $count['전체갯수']) {
//			$order_status ="구매발주";
//		}
//		else if($count['배송준비'] == $count['전체갯수']) {
//			$order_status ="배송준비";
//		}
//		else {
//			//$order_status ="접수완료";
//			$order_status = $order_info['o_status'];
//		}

		// 배송상태 우선순위에 의해서 상태값 배정
		// 취소불가능 우선순위 : 배송준비 > 배송중 > 배송완료
		// 취소가능 우선순위 : 접수완료 > 구매발주
		if($count['배송준비'] > 0) {
			$order_status ="배송준비";
		}
		else if($count['배송중'] > 0) {
			$order_status ="배송중";
		}
		else if($count['배송완료'] > 0) {
			$order_status ="배송완료";
		}
		else if($count['구매발주'] > 0) {
			$order_status ="구매발주";
		}
		else {
			$order_status ="접수완료";
		}


	}

	$arr_insert_add = array();
	if($order_status <> $order_info['o_status'] ){
		$arr_insert_add[] = " o_status = '".$order_status."' ";
	}
	if(in_array($order_status, array('구매발주', '배송준비', '배송중', '배송완료'))) {
		$arr_insert_add[] = " o_sendstatus = '".$order_status."'"; // LDD 2016-05-24
	}
	if( sizeof($arr_insert_add) > 0 ){
		_MQ_noreturn("update smart_order set ". implode(',', $arr_insert_add) ." where o_ordernum = '".$ordernum."'");
	}

	order_delivery_date_update($ordernum,$order_status);// 배송시 상태에 따른 배송진행일, 배송완료일 체크

	return;

}


/*
	// 배송시 상태에 따른 배송진행일, 배송완료일 체크
	// o_senddate -> 배송중 체크시 기록이되며 최초 한번만 기록
	// o_completedate -> 배송완료일에 기록이 되며 주문된 모든 주문상품이 배송완료 되었을 시 처리
	// 아무위치나 실행해선안된다. 배송진행상태 변경 처리 이후에 실행시켜야 한다.
*/
function order_delivery_date_update($ordernum,$order_status)
{
	if($order_status == '' || $ordernum == '') { return ;}
	if( $order_status == '구매발주' || $order_status == '배송준비'){
		_MQ_noreturn(" update smart_order set o_senddate = '0000-00-00', o_completedate = '0000-00-00' where o_ordernum = '".$ordernum."'  ");
	}else if( $order_status == '배송중'){
		$o_senddate = _MQ_result("select op_senddate from smart_order_product where op_oordernum = '".$ordernum."' and op_sendstatus = '배송중' order by op_senddate asc  ");
		_MQ_noreturn(" update smart_order set o_senddate = '".$o_senddate."', o_completedate = '0000-00-00' where o_ordernum = '".$ordernum."'  ");
	}else if($order_status == '배송완료'){
		$o_completedate = _MQ_result("select op_completedate from smart_order_product where op_oordernum = '".$ordernum."' and op_sendstatus = '배송완료' order by op_completedate desc  ");
		_MQ_noreturn(" update smart_order set  o_completedate = '".$o_completedate."' where o_ordernum = '".$ordernum."'  ");
	}
	return ;
}


// 1뎁스에 속한 3뎁스 cuid 를 추출한다.
function get_3depth_cuid($cuid) {

	$row = _MQ_assoc("select c_uid from smart_category where find_in_set('".$cuid."',c_parent ) and c_depth=3");
	foreach($row as $k => $v) {
		$cuid_arr[] = $v['c_uid'];
	}

	return $cuid_arr;

}


//LCY::COUPON -- 기존함수 수정
// 유저에게 쿠폰을 지급한다.
// 인자 : 유져아이디
//			: 타이틀
//			: 쿠폰유형 (product_sale : 상품할인쿠폰, free_delivery : 무료배송쿠폰)
//			: 쿠폰 가격
function give_coupon($coup_inid,$couponSetData) {
	global $arrCouponSet;
	if(count($couponSetData) < 1){ return ; }

	// 사용기간
	if($couponSetData['ocs_use_date_type'] == 'date'){ // 사용기간
		if( $couponSetData['ocs_edate'] < date('Y-m-d')){ return;  } // 사용기간이 오늘보다 작다면
		$coup_expdate = $couponSetData['ocs_edate'];
	}else{
		if( $couponSetData['ocs_expire'] < 1){ return ; } // 사용일이 없다면
		$coup_expdate = date('Y-m-d',strtotime("+ ".$couponSetData['ocs_expire']." days"));
	}

	// 쿠폰셋 정보 시리얼화
	$coup_ocsinfo = serialize($couponSetData);

	$coup_uid = shop_couponnum_create(); // 쿠폰 새로 생성
	$arrSque = array(); // 넘길 쿼리문 초기화

	$arrSque[] = " coup_uid = '".$coup_uid."' ";
	$arrSque[] = " coup_inid  = '".$coup_inid."' ";
	$arrSque[] = " coup_use  = 'N' ";
	$arrSque[] = " coup_type   = '".$couponSetData['ocs_boon_type']."' ";
	$arrSque[] = " coup_ocs_uid = '".$couponSetData['ocs_uid']."' ";
	$arrSque[] = " coup_expdate = '".$coup_expdate."' ";
	$arrSque[] = " coup_ocsinfo = '".addslashes($coup_ocsinfo)."' ";
	$arrSque[] = " coup_rdate = now() ";

	$sque = implode(",",$arrSque);
	$que = "insert into smart_individual_coupon set ".$sque;
	_MQ_noreturn($que);
}
// 만료일이 지난 회원쿠폰 만료처리
function coupon_update() {
	 _MQ_noreturn(" update smart_individual_coupon set coup_use ='E'  where coup_expdate  < CURDATE() and coup_use='N' ");
}

// 주문건수 추출
function get_order_cnt($in_id,$remaining_month=99) {

	if($remaining_month>0) $add_que = " and o_rdate >= '".date('Y-m-d',strtotime("-".$remaining_month." month"))."' ";

	$r = _MQ("select count(*) as cnt from smart_order as o where o_mid='".$in_id."' and o_paystatus='Y' and o_canceled!='Y' ".$add_que);
	return $r['cnt'];

}

// 주문 총액 추출
function get_order_price($in_id,$remaining_month=99) {

	if($remaining_month>0) $add_que = " and o_rdate >= '".date('Y-m-d',strtotime("-".$remaining_month." month"))."' ";

	$r = _MQ("select sum(o_price_real) as price  from smart_order as o where o_mid='".$in_id."' and o_paystatus='Y' and o_canceled!='Y' ".$add_que);
	return $r['price'];

}

// 주문서 정보 추출
function get_order_info($ordernum) {

	$r = _MQ("select *  from smart_order where o_ordernum='".$ordernum."'");

	return $r;

}
// 주문서 상품 정보 추출
function get_order_product_info($ordernum) {

	$r = _MQ_assoc("select *  from smart_order_product where op_oordernum='".$ordernum."'");

	return $r;

}


// 카테고리 상품 갯수 추출
function get_cate_cnt($cuid) {

	$cuid_arr = explode("_",$cuid);

	$r = _MQ("select count(*) as cnt from smart_product where p_cuid in ('".implode("','",$cuid_arr)."') and p_view ='Y' and p_option_valid_chk = 'Y' ");

	return $r['cnt'];
}


// 1dep 카테고리명 추출 (리턴은 한글로 보냄)
function get_pro_1depth($cuid,$mode = 'name') {
	if(!$cuid) return;
	$r = _MQ("select c_uid,c_name from smart_category where c_uid = (select SUBSTRING_INDEX(c_parent,',',1) from smart_category where c_uid = ".$cuid.")");

	if($mode == "uid")
		return $r['c_uid'];
	else
		return $r['c_name'];
}

// 상품 카테고리 정보를 가져온다.
// cuid 1순위, 없을 경우 상품코드 2순위
function get_pro_depth_info($cuid , $pcode) {
	// -- 상품 카테고리 정보 추출 ---
	$arr_product_cate = $arr_init = array();
	/* SSJ : 2018-03-29 상품카테고리에 1차, 2차 카테고리가 지정될 경우 오류 수정
	$que = "
		select
			pct.pct_cuid as c3_uid,
			substring_index(c.c_parent , ',' ,-1) as c2_uid ,
			substring_index(c.c_parent , ',' ,1) as c1_uid,
			c.c_name as c3_name ,
			c2.c_name as c2_name ,
			c1.c_name as c1_name
		from smart_product_category as pct
		left join smart_product as p on (p.p_code = pct.pct_pcode)
		left join smart_category as c on (c.c_uid = pct.pct_cuid and c.c_depth=3)
		left join smart_category as c2 on (c2.c_uid = substring_index(c.c_parent , ',' ,-1) and c2.c_depth=2)
		left join smart_category as c1 on (c1.c_uid = substring_index(c.c_parent , ',' ,1) and c1.c_depth=1)
		where  pct.pct_pcode = '".$pcode."'
		order by pct.pct_uid asc
	";
	*/
	$que = "
		select
			c.c_uid as c3_uid,
			c2.c_uid as c2_uid,
			c1.c_uid as c1_uid,
			c.c_name as c3_name ,
			c2.c_name as c2_name ,
			c1.c_name as c1_name
		from smart_product_category as pct
		left join smart_product as p on (p.p_code = pct.pct_pcode)
		left join smart_category as c on (c.c_uid = pct.pct_cuid and c.c_depth=3)
		left join smart_category as c2 on (c2.c_uid = substring_index( substring_index( (select concat(c_parent,',',c_uid) from smart_category where c_uid = pct.pct_cuid)  , ',' ,2)  , ',' ,-1) and c2.c_depth=2)
		left join smart_category as c1 on (c1.c_uid = substring_index( substring_index( (select if(c_parent>0, concat(c_parent,',',c_uid), c_uid) from smart_category where c_uid = pct.pct_cuid)  , ',' ,1)  , ',' ,-1) and c1.c_depth=1)
		where  pct.pct_pcode = '".$pcode."' and c1.c_name is not null
		order by pct.pct_uid asc
	";

	$r = _MQ_assoc($que);
	foreach($r as $k=>$v){
		if($k == 0 ) {$arr_init[1]=$v['c1_uid'];	$arr_init[2]=$v['c2_uid'];	$arr_init[3]=$v['c3_uid'];}
		if(!is_array($arr_product_cate[$v['c1_uid']])) { $arr_product_cate[$v['c1_uid']] = array($v['c1_uid'], $v['c2_uid'], $v['c3_uid']); }
		if(!is_array($arr_product_cate[$v['c2_uid']])) { $arr_product_cate[$v['c2_uid']] = array($v['c1_uid'], $v['c2_uid'], $v['c3_uid']); }
		if(!is_array($arr_product_cate[$v['c3_uid']])) { $arr_product_cate[$v['c3_uid']] = array($v['c1_uid'], $v['c2_uid'], $v['c3_uid']); }

		if(!is_array($arr_product_cate_name[$v['c1_uid']])) { $arr_product_cate_name[$v['c1_uid']] = array($v['c1_name'], $v['c2_name'], $v['c3_name']); }
		if(!is_array($arr_product_cate_name[$v['c2_uid']])) { $arr_product_cate_name[$v['c2_uid']] = array($v['c1_name'], $v['c2_name'], $v['c3_name']); }
		if(!is_array($arr_product_cate_name[$v['c3_uid']])) { $arr_product_cate_name[$v['c3_uid']] = array($v['c1_name'], $v['c2_name'], $v['c3_name']); }
	}

	if($cuid && $arr_product_cate[$cuid][0]) {
		// 카테고리 정보를 통해 추출
		$data[1] = $arr_product_cate[$cuid][0];
		$data[2] = $arr_product_cate[$cuid][1];
		$data[3] = $arr_product_cate[$cuid][2];
		$name[1] = $arr_product_cate_name[$cuid][0];
		$name[2] = $arr_product_cate_name[$cuid][1];
		$name[3] = $arr_product_cate_name[$cuid][2];
	}
	else {
		// 기본 상품정보를 통해 추출
		$data[1] = $r[0]['c1_uid'];
		$data[2] = $r[0]['c2_uid'];
		$data[3] = $r[0]['c3_uid'];
		$name[1] = $r[0]['c1_name'];
		$name[2] = $r[0]['c2_name'];
		$name[3] = $r[0]['c3_name'];
	}
	return array($data,$name);
}

// 카테고리별 상품 갯수를 업데이트 한다.
function update_catagory_product_count() {

	return; // 사용을 위해서는 이줄을 제거 하세요 2015-11-20 LDD(jjc 지시)
	// -- 카테고리 상품 갯수 초기화 ---
	_MQ_noreturn("update smart_category set c_pro_cnt=0");
	// -- 카테고리 당 상품 갯수 적용 ---
	$arr = array();
	$r = _MQ_assoc("
		select
			pct.pct_cuid,
			pct.pct_pcode,
			substring_index(c.c_parent , ',' ,-1) as c2_uid ,
			substring_index(c.c_parent , ',' ,1) as c1_uid
		from smart_product_category as pct
		left join smart_product as p on (p.p_code = pct.pct_pcode)
		left join smart_category as c on (c.c_uid = pct.pct_cuid and c.c_depth=3)
		where
			p.p_view='Y'
	");
	//	and p.p_stock > 0
	//	group by pct.pct_cuid
	foreach($r as $k => $v) {
		$arr[$v['pct_cuid']][$v['pct_pcode']] ++;
		$arr[$v['c2_uid']][$v['pct_pcode']] ++;
		$arr[$v['c1_uid']][$v['pct_pcode']] ++;
	}
	foreach($arr as $k => $v) {
		$sque = " update smart_category set c_pro_cnt = ".sizeof($v)." where c_uid = '".$k."'";
		_MQ_noreturn($sque);
		//echo $sque . "<hr>";
	}
}
/*	// 카테고리별 상품 갯수를 업데이트 한다.
function update_catagory_product_count() {

	// 카테고리 상품 갯수 초기화
	_MQ_noreturn("update smart_category set c_pro_cnt=0");
	$r = _MQ_assoc("select p_cuid,count(*) as cnt from smart_product where p_view='Y' and p_stock > 0 group by p_cuid");
	foreach($r as $k => $v) {
		$cnt = $v[cnt];

		// 자신 카테고리에 상품 갯수 추가
		$r2 = _MQ("select * from smart_category where c_uid = '".$v[p_cuid]."'");
		_MQ_noreturn("update smart_category set c_pro_cnt = c_pro_cnt + ".$cnt." where c_uid = '".$v[p_cuid]."'");

		// 부모 카테고리에도 상품 갯수 추가
		if($r2[c_depth] > 1) {
			$parent_array = explode(",",$r2[c_parent]);
			for($i=0;$i<sizeof($parent_array);$i++) {
				_MQ_noreturn("update smart_category set c_pro_cnt = c_pro_cnt + ".$cnt." where c_uid = '".$parent_array[$i]."'");
			}
		}

	}
}*/

// 포인트 업데이트
function point_update() {

	$r = _MQ_assoc("select *, indr.in_point, indr.in_id from smart_point_log as pl left join smart_individual as indr on (pl.pl_inid = indr.in_id) where pl.pl_appdate <= '".date('Y-m-d')."' and pl.pl_status='N' and pl.pl_delete='N' ");
	foreach( $r as $k=>$v ){
		$apply_id = $v['in_id'];
		if($apply_id <> ''){ // 회원아이디가 있을때만 실행
			$apply_point = $v['pl_point'];
			$calc_point = $v['in_point'] + $apply_point;
			if($calc_point < 0){
				$apply_point = $v['in_point']*(-1);
				$calc_point = 0;
			}
			_MQ_noreturn("update smart_individual set in_point = ". $calc_point ." where  in_id = '".$apply_id."' ");
			// 포인트 업데이트후 회원포인트 추출
			$point_after = _MQ_result(" select in_point from smart_individual where in_id = '".$apply_id."' ");
			_MQ_noreturn("update smart_point_log set pl_point_before = '". $v['in_point'] ."', pl_point_apply = '". $apply_point ."', pl_point_after = '". $point_after ."', pl_status = 'Y', pl_adate = now() where  pl_uid = '".$v['pl_uid']."'");
		}else{
			_MQ_noreturn("update smart_point_log set pl_title = '[취소] ". $v['pl_title'] ." (회원아이디 불일치)', pl_status = 'C' where  pl_uid = '".$v['pl_uid']."'");
		}
	}

	return;

}

// -- 운영자관리 :: 계정정보 --
function get_site_admin($AdminUID = '')
{
	global $_COOKIE;
	if($AdminUID == '') $AdminUID = $_COOKIE['AuthAdmin'];
	$row = _MQ("select *from smart_admin where a_uid = '{$AdminUID}'  ");
	return $row;
}


// LDD: 2018-03-23 -- 운영자별 메뉴 권한 체크
function AdminMenuCheck() {
	global $CURR_FILENAME, $app_current_link, $app_mode; // 현재 페이지
	$AdminInfo = get_site_admin($_COOKIE['AuthAdmin']);
	if($AdminInfo['a_type'] == 'master') return; // 관리자 타입이 master(삭제불가 관리자)인경우 pass
	if($CURR_FILENAME == 'index.php') return; // 관리자 index.php 는 제외

	// 승인여부 판단
	if($AdminInfo['a_use'] != 'Y') {
		@samesiteCookie("AuthAdmin","",time() - 100000,"/");
		@samesiteCookie("AuthCompany","",time() - 100000,"/");
		AdminLogout();
		error_loc_msg('index.php', '승인되지 않은 운영자 계정입니다.');
	}
	if($CURR_FILENAME == '_main.php') return; // 관리자 _main.php 는 제외(승인 완료 상태에서)

	$app_current_link = $app_current_link ? $app_current_link : $CURR_FILENAME;

	// 메뉴권한확인
	if($app_mode <> "popup"){ // 2018-07-27 SSJ :: 팝업창은 관리자 메뉴에 등록되어 있지 않아서 권한체크 제외 , 필수변수 :: $app_mode
		$_menu = _MQ(" select am_uid from smart_admin_menu where am_link = '{$app_current_link}' ");
		if(!$_menu['am_uid']) error_loc_msg('_main.php', '페이지가 존재하지 않거나 권한이 없는 페이지입니다.');
		$_amenu = _MQ(" select ams_uid from smart_admin_menu_set where ams_amuid = '{$_menu['am_uid']}' and ams_auid = '{$AdminInfo['a_uid']}' ");
		if(!$_amenu['ams_uid']) error_loc_msg('_main.php', '권한이 없는 페이지입니다.');
	}
}

// 사이트 기본 정보를 호출한다.
// 인자 : 없음
// 리턴 : 사이트 기본 정보
function get_site_info() {
	$r = _MQ("select * from smart_setup where s_uid=1");
	return $r;
}

// 카테고리 목록 정보를 얻는다.
// 인자 : 뎁스 ( 생략가능 )
// 리턴 : 카테고리 정보
function get_category_list($depth=1,$parent_cuid) {
	if($parent_cuid) $where = " and find_in_set(".$parent_cuid.",c_parent) ";
	$r = _MQ_assoc("select * from smart_category where c_view='Y' and c_depth=".$depth." ".$where." order by c_idx asc");
	return $r;
}

// 카테고리 이름을 얻는다.
// 인자 : 카테고리 코드
// 리턴 : 해당 카테고리 이름
function get_category_name($cuid) {
	$r = _MQ("select c_name from smart_category where c_uid='".$cuid."'");
	return $r['c_name'];
}

// 상품평의 평가점수의 평균을 추출한다.
// 인자 : 상품코드, 상품평 구분(eval : 상품평가)
// 리턴 : 100점 만점을 기준한 평균치
function get_eval_average($pcode,$pt_type = 'eval') {
	global $arr_p_talk_type;

	$row = _MQ("select (sum(pt_eval_point)/count(*)) as av from smart_product_talk where pt_type = '".$arr_p_talk_type[$pt_type]."' and pt_eval_point != 0  and pt_pcode = '".$pcode."' and pt_intype = 'normal'");

	return intval($row['av']);

}

// 상품 상세페이지 링크 추출
// 인자 : 상품코드
// 리턴 : 상품 상세페이지 주소 link
function get_pro_view_link($pcode) {
	return "/?pn=product.view&pcode=".$pcode;
}

// 유효한 옵션이 있는지 확인한다.
// 인자 : 상품코드
// 리턴 : boolean
function is_option($pcode) {
	$p = _MQ(" select p_option_type_chk as opt from smart_product where p_code = '". $pcode ."' ");
	$depth = rm_str($p['opt']);
	if($depth > 0){
		$r = _MQ("select if(count(*),true,false) as cnt from smart_product_option where po_view = 'Y' and po_depth = '". $depth ."' and po_pcode='".$pcode."' and po_cnt > 0");
		return $r['cnt'];
	}else{
		return 0;
	}
}


// 후손 카테고리 cuid 를 추출한다.
// 인자 : 카테고리 코드
// 리턴 : cuid 배열값
function get_descendant_cate($cuid) {

	$cuid_arr[] = $cuid;

	$row = _MQ_assoc("select c_uid from smart_category where find_in_set('".$cuid."',c_parent ) and c_view ='Y' ");
	foreach($row as $k => $v) {
		$cuid_arr[] = $v['c_uid'];
	}

	return $cuid_arr;

}

// 자식 카테고리 cuid 를 추출한다.
// 인자 : 카테고리 코드
// 리턴 : cuid 배열값
function get_child_cate($cuid) {

	$cate_info = info_category($cuid);
	$c_depth = $cate_info['c_depth']+1;

	$row = _MQ_assoc("select c_uid from smart_category where find_in_set('".$cuid."',c_parent ) and c_view ='Y' and c_depth = '".$c_depth."' ");
	foreach($row as $k => $v) {
		$cuid_arr[] = $v['c_uid'];
	}

	return $cuid_arr;

}

// 장바구니 담긴 상품갯수를 추출한다.
// 인자 : 없음
// 리턴 : 상품갯수(int)
function get_cart_cnt() {
	global $_COOKIE;

	$r = _MQ("select count(*) as cnt from smart_cart where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and c_cookie != '' group by c_pcode ");
    if (!$r['cnt']) $r['cnt'] = 0;

	return $r['cnt'];
}

// 수기주문 장바구니 담긴 상품갯수를 추출한다.
// 인자 : 없음
// 리턴 : 상품갯수(int)
function get_cart_manual_cnt() {
	global $_COOKIE;

	$r = _MQ("select count(*) as cnt from smart_cart_manual where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and c_cookie != '' group by c_pno");
    if (!$r['cnt']) $r['cnt'] = 0;

	return $r['cnt'];
}

// 게시판 목록을 추출한다.
// 인자 : 없음
// 리턴 : 게시판 이름 배열값 ( uid, name, name2, post_cnt )
function get_board_list_array($is_list= false,$is_option=false) {

	$r = _MQ_assoc("select bi_uid,bi_name,bi_list_type from smart_bbs_info where 1 ");
	foreach($r as $k => $v) {

		if( $is_option === true){ $option = ' ('.$v['bi_uid'].')'; } // 게시판 고유번호와 함께 뿌려준다.
		$res[$v['bi_uid']] = $v['bi_name'].$option;
	}

	if($is_list == true){ // 리스트를 뿌려준다.
		foreach($r as $k => $v) {

			$res[$v['bi_uid']] = $v['bi_skin'];
		}
	}

	return $res;
}


// 게시판 정보를 추출한다.
// 인자 : 게시판 코드
// 리턴 : 게시판 정보
function get_board_info($b_menu) {

	$r = _MQ("select * from smart_bbs_info where bi_uid='".$b_menu."'");

	return $r;

}

// 게시물 정보를 추출한다.
// 인자 : 게시물 코드
// 리턴 : 게시물 정보
function get_post_info($b_uid) {

	$r = _MQ("select * from smart_bbs where b_uid='".$b_uid."'");

	return $r;

}

// 게시물을 최근 목록 추출한다.
// 인자 : 게시판코드, 추출갯수
// 리턴 : 게시물 목록
function get_board_list($b_menu,$limit=10,$secret='Y') {

	if($secret != 'Y'){ $sque = " and b_secret != 'Y' "; }
	//$r = _MQ_assoc("select * from smart_bbs where b_menu='".$b_menu."' ".$sque." ORDER BY b_uid desc limit ".$limit);
	$b_menu_sql = " b_menu = '{$b_menu}' ";
	if(is_array($b_menu)) $b_menu_sql = " b_menu in('".implode("', '", $b_menu)."') ";
	$r = _MQ_assoc("select * from smart_bbs where {$b_menu_sql} {$sque} ORDER BY b_uid desc limit ".$limit);

	return $r;

}

// 게시물을 추출한다.
// 인자 : 게시판코드, 조건문
// 리턴 : 게시물 갯수
function get_board_cnt($b_menu,$where='') {

	$r = _MQ("select count(*) as cnt from smart_bbs where b_menu='".$b_menu."' ".$where);

	return $r['cnt'];

}

// 게시물 갯수를 업데이트 한다.
// 인자 : 게시판코드
// 리턴 : 없음.
function update_board_post_cnt($bi_uid='') {

	if($bi_uid) $where = " where bi_uid='".$bi_uid."'";
	_MQ_noreturn("update smart_bbs_info set bi_post_cnt = (select count(*) from smart_bbs where b_menu = bi_uid) ".$where);
	return;

}

// 게시글의 댓글 개수를 업데이트한다.
function update_board_comment_cnt($_buid)
{
	$cnt = _MQ_result("select count(*) as cnt from smart_bbs_comment where bt_buid = '".$_buid."' ");
	_MQ_noreturn("update smart_bbs set b_talkcnt = '".$cnt."' where b_uid = '".$_buid."'  ");
	return true;
}

// 댓글을 추출한다.
// 인자 : 게시판코드, 조건문
// 리턴 : 댓글 갯수
function get_board_talk_cnt($b_menu,$where='') {
	$r = _MQ("select sum(b_talkcnt) as talkcnt from smart_bbs where b_menu='".$b_menu."' ".$where);
	return $r['talkcnt'];
}

// 해당 상품에 달린 토크 총 갯수를 구한다.
// 인자 : 상품코드(코드, all: 전체상품),
//				토크유형(talk:토크, eval:평가, qna:문의, all: 전체글),
//				작성자유형(all:전체, normal:회원, admin:운영자, company:입점업체)
//				조건문
// 리턴 : 갯수
function get_talk_total($pcode,$ttype='talk',$mtype='all', $where='') {
	global $arr_p_talk_type;

	if($mtype != "all") $where .= " and pt_intype = '".$mtype."' ";
	if($pcode != "all") $where .= " and pt_pcode = '".$pcode."' ";
	if($ttype != "all") $where .= " and pt_type = '".$arr_p_talk_type[$ttype]."' ";

	$row = _MQ("select count(*) as cnt from smart_product_talk where 1 ".$where);

	return $row['cnt'];

}

// 문의(1:1문의,제휴문의) 총 갯수를 구한다.
// 인자 : 문의유형(type값 , all: 전체상품),
//				조건문
// 리턴 : 갯수
function get_request_cnt($menu, $where='') {

	if($menu != "all") $where .= " and r_menu = '".$menu."' ";

	$row = _MQ("select count(*) as cnt from smart_request where 1 ".$where);

	return $row['cnt'];

}

// 상품정보를 추출한다.
// 인자 : 상품코드
// 리턴 : 상품정보
function get_product_info($pcode) {

	$row = _MQ("select * from smart_product where p_code = '".$pcode."'");

	return $row;

}

// 최근 본상품 정보를 추출한다.
// 인자 : 없음
// 리턴 : 최근본 상품 코드 목록
function get_latest_list() {
	global $_COOKIE, $siteInfo;
	$r = _MQ_assoc("select * from smart_product_latest as pl inner join smart_product as p on (p.p_code = pl.pl_pcode) where pl_uniqkey='" . $_COOKIE["AuthProductLatest"] . "' and p_view ='Y' and p_option_valid_chk = 'Y' order by pl_rdate desc limit 0, ".($siteInfo['s_today_view_max'] > 0?$siteInfo['s_today_view_max']:12)." ");
	return $r;
}

// 장바구니 담긴 상품인지 체크
// 인자 : 상품코드
// 리턴 : boolean
function is_wish($pcode) {

	$r = _MQ("select count(*) as cnt from smart_product_wish where pw_pcode = '".$pcode."' and pw_inid='".get_userid()."'");
	if($r['cnt'] > 0) return true;
	else return false;

}

// pg 결제 취소에 대한 로그를 쌓는다
function card_cancle_log_write($tno,$res_msg, $ordernum=null) {

    global $_ordernum;
    $ordernum = $ordernum ? $ordernum : $_ordernum;
	_MQ_noreturn("update smart_order_cardlog set oc_cancle_content = '".$res_msg."' where oc_tid = '".$tno."' ".($ordernum ? "  and oc_oordernum = '". $ordernum ."' " : ""));
}

// 상품 아이콘 정보를 가져온다.
// 인자 : 상품 아이콘 유형 (생략시 모든 아이콘 정보 추출)
// 리턴 : 상품 아이콘 정보 배열값
function get_product_icon_info($_type = "all") {
	$data = array();
	$_where = $_type != "all" ? " and pi_type ='".$_type."' " : "";
	$r = _MQ_assoc("select * from smart_product_icon where 1 ".$_where." order by pi_idx");
	foreach($r as $k => $v) {
		$data[] = $v;
	}
	return $data;
}

// 무통장 계좌 정보를 가져온다.
// 인자 : 없음
// 리턴 : 무통장 계좌 정보 배열값
function get_online_bank_info() {

	$r = _MQ_assoc("select * from smart_bank_set order by bs_idx");

	return $r;
}

// 진행중인 주문건수 추출
// 인자 없음
// 리턴 : 진행중인 주문건수
function get_order_ing_cnt($order_status=array()) {
	global $arr_order_status_ordering,$_COOKIE;
	$arr_order_status_ordering = (count($order_status) > 0?$order_status:$arr_order_status_ordering);

	$add_sql = "";
	//if(!in_array('접수대기', $order_status)) $add_sql = " and o_paystatus='Y'";
	//$r = _MQ("select count(*) as cnt from smart_order as o where o_mid='".get_userid()."' {$add_sql} and o_status in ('".implode("','",$arr_order_status_ordering)."') ");
	if(!in_array('접수대기', $arr_order_status_ordering)){
		$add_sql = " and o_paystatus='Y'";
	}else{
		unset($arr_order_status_ordering[array_search('접수대기', $arr_order_status_ordering)]);
		$add_sql = " or (o_status='접수대기' and o_paymethod='online') ";
	}
	$r = _MQ("select count(*) as cnt from smart_order as o where o_mid='".get_userid()."' and (o_status in ('".implode("','",$arr_order_status_ordering)."') {$add_sql} ) ");
	return $r['cnt'];
}

// 쿠폰 갯수 추출
// 인자 없음
// 리턴 : 쿠폰 갯수
function get_coupon_enable_cnt() {
	global $_COOKIE;

	$r = _MQ("select count(*) as cnt from smart_individual_coupon where coup_inid='".get_userid()."' and coup_use='N'");
	return $r['cnt'];

}

// 찜한상품 갯수 추출
function get_wish_cnt() {
	global $_COOKIE;

	$r = _MQ("select count(*) as cnt from smart_product_wish where pw_inid = '".get_userid()."'");

	return $r['cnt'];
}


// 주문중인 상품의 재고를 확인한다.
function order_product_stock_check($c_cookie) {

	// 재고 확인시 별문제가 없다면 ok가 리턴될것이다.
	$return_value = "ok";

	$r = _MQ_assoc("select * from smart_cart where c_cookie = '".$c_cookie."' and c_cnt > 0");
	foreach($r as $k => $v) {
		$is_option = $v['c_pouid'] ? true : false; // 옵션상품인지 일반상품인지 체크

		// 상품 수량
		list($cnt) = _MQ("select p_stock as `0` from smart_product where p_code = '".$v['c_pcode']."'");

		// 상품 옵션 수량
		if($is_option) {
			$is_addoption = $v[c_is_addoption]=="Y" ? true : false; // 일반옵션인지 추가옵션인지 체크
			if($is_addoption){
				list($option_cnt) = _MQ("select pao_cnt as `0` from smart_product_addoption where pao_pcode = '".$v['c_pcode']."' and pao_uid = '".$v['c_pouid']."'");
				// 추가 옵션수량을 재고로 잡는다.
				$cnt = $option_cnt;
			}else{
				list($option_cnt) = _MQ("select po_cnt as `0` from smart_product_option where po_pcode = '".$v['c_pcode']."' and po_uid = '".$v['c_pouid']."'");
				// 상품 본 수량과 옵션수량중 작은 수량을 재고로 잡는다.
				$cnt = min($option_cnt,$cnt);
			}
		}


		if($cnt < 1) {			// 이미 품절된 상품이면..
			// 장바구니에 담긴 수량을 0으로 수정
			_MQ_noreturn("update smart_cart set c_cnt=0 where c_uid = '".$v['c_uid']."'");
			$return_value = "soldout";
		} else if($v['c_cnt'] > $cnt) { // 재고량 보다 장바구니에 담긴 수량이 더 많으면...
			_MQ_noreturn("update smart_cart set c_cnt=".$cnt." where c_uid = '".$v['c_uid']."'");
			if($return_value != "soldout") $return_value = "notenough";	// 이미 soldout된 상품이 있다면 리턴값을 수정할필요 없다.
		}
	}

	return $return_value;
}

// 주문중인 상품 옵션의 재고를 확인한다.
function order_product_option_stock($o_ordernum, $op_pcode, $op_pouid, $op_is_addoption) {

    $is_option = $op_pouid ? true : false; // 옵션상품인지 일반상품인지 체크

    // 상품 수량
    list($cnt) = _MQ("select p_stock as `0` from smart_product where p_code = '".$op_pcode."'");

    // 상품 옵션 수량
    if($is_option) {
        $is_addoption = $op_is_addoption=="Y" ? true : false; // 일반옵션인지 추가옵션인지 체크
        if($is_addoption){
            list($option_cnt) = _MQ("select pao_cnt as `0` from smart_product_addoption where pao_pcode = '".$op_pcode."' and pao_uid = '".$op_pouid."'");
            // 추가 옵션수량을 재고로 잡는다.
            $cnt = $option_cnt;
        }else{
            list($option_cnt) = _MQ("select po_cnt as `0` from smart_product_option where po_pcode = '".$op_pcode."' and po_uid = '".$op_pouid."'");
            // 상품 본 수량과 옵션수량중 작은 수량을 재고로 잡는다.
            $cnt = min($option_cnt,$cnt);
        }
    }
    
    // 입고수량
    if ($o_ordernum) {
        $sql_search = " and op_oordernum='".$o_ordernum."' ";
    }
    list($op_instock_cnt) = _MQ("select sum(op_instock_cnt) as `0` from smart_order_product where (1) $sql_search and op_pcode = '".$op_pcode."' and op_pouid = '".$op_pouid."'");
    $cnt = $cnt + $op_instock_cnt;

	return $cnt;
}


// 2020-04-07 SSJ :: 문자 발송을 위한 sms정보를 추출하여 배열화 한다.
// 인자 : 상대방 전화번호, 문자 발송 유형 , 주문번호를 2차배열 형태로 받음
//      $arr = array(
//         array('to'=>'01012341234','type'=>'join','ordernum'=>'tester')
//         ,array('to'=>'01012341234','type'=>'order_online','ordernum'=>'75729-22740-55984')
//         ,array('to'=>'01012341234','type'=>'order_pay','ordernum'=>'52702-33705-63265')
//      );
// 리턴 : $arr_send, 2차배열 형태
function get_send_info($arr){
    global $siteInfo;

    $arr_send = array();
    $stringsAdd = array(); // 치환자 추가 SSJ : 2017-10-21

    if(count($arr) > 0){
        $arr_type = array();
        $arr_order = array();
        foreach($arr as $k=>$v){
            // 타입 일괄 처리
            $arr_type[$v['type']]['cnt']++;
            // 주문번호 일괄처리
            $_onum = '';
            if(!is_array($v['ordernum'])){
                $_onum = $v['ordernum'];
            }
            if($_onum <> ''){
                $arr_order[$_onum]['cnt']++;
                // 부분취소시 주문상품명
                if(  in_array($v['type'], array( 'order_cancel_part' )  ) ){
                    $arr_order[$_onum]['pname_cnt']++;
                }
            }
        }

        // 타입 일괄 처리
        foreach($arr_type as $k=>$v){
            if($v['cnt'] > 0){
                $arr_type[$k]['info']= _MQ("select * from smart_sms_set where ss_uid = '".$k."' limit 1");
                $arr_type[$k]['infoAdmin']= _MQ("select * from smart_sms_set where ss_uid = 'admin_".$k."' limit 1");
            }
        }

        // 주문번호 일괄처리
        foreach($arr_order as $k=>$v){
            if($v['cnt'] > 0){
                $tmp = _MQ("select count(*) as cnt from smart_order where o_ordernum = '".$k."'");
                if($tmp['cnt']>0) {
                    $r = _MQ("select o_paymethod, o_bank, o_oname, o_rdate, o_price_real, o_bank, o_sendcompany, o_sendnum, o_senddate from smart_order where o_ordernum = '".$k."'");
                    $arr_order[$k]['name'] = $r['o_oname'];
                    $arr_order[$k]['order_date'] = date('Y-m-d', strtotime($r['o_rdate']));
                    $arr_order[$k]['order_pay'] = number_format($r['o_price_real']);
                    $arr_order[$k]['none_bank'] = $r['o_bank'];
                    if($r['o_paymethod'] == 'virtual' && $r['o_bank'] == '') {
                        $OrderOnlineLog = _MQ(" select * from smart_order_onlinelog where ool_ordernum = '{$k}' ");
                        $arr_order[$k]['none_bank'] = "[{$OrderOnlineLog['ool_bank_name']}] {$OrderOnlineLog['ool_account_num']}, {$OrderOnlineLog['ool_bank_owner']}";
                    }
                    $arr_order[$k]['send_com'] = $r['o_sendcompany'];
                    $arr_order[$k]['send_num'] = $r['o_sendnum'];
                    $arr_order[$k]['send_date'] = date('Y-m-d', strtotime($r['o_senddate']));
                }
                else {
                    $tmp = _MQ("select count(*) as cnt from smart_individual where in_id = '".$k."'");
                    if($tmp['cnt']>0) {
                        $r = _MQ("select in_id, in_name from smart_individual where in_id = '".$k."'");
                        $arr_order[$k]['name'] = $r['in_name'];
                        $arr_order[$k]['mem_id'] = $r['in_id'];
                    }
                    else { $arr_order[$k]['name'] = $k; }
                }
            }
            if($v['pname_cnt'] > 0){
                $op_res = _MQ( " select op_pname from smart_order_product where op_oordernum = '".$ordernum."' and op_cancel = 'Y' order by op_cancel_cdate desc " );
                $arr_order[$k]['pname']= $op_res['op_pname'];
            }
        }

        foreach($arr as $k=>$v){
            // 변수설정
            $to = $v['to'];
            $type = $v['type'];
            $ordernum = $v['ordernum'];

            // ordernum 이 배열이면 추가치환자 적용
            if(is_array($ordernum)) {
                $stringsAdd = $ordernum;
                // ordernum 추출
                $ordernum = $stringsAdd['{주문번호}'];
                $stringsAdd['{주문번호}'] = '';
                $stringsAdd = array_filter($stringsAdd);
            }

            // 초기화
            $name = $arr_order[$ordernum]['name'];
            $mem_id = $arr_order[$ordernum]['mem_id'];
            $order_date = $arr_order[$ordernum]['order_date'];
            $order_pay = $arr_order[$ordernum]['order_pay'];
            $none_bank = $arr_order[$ordernum]['none_bank'];
            $send_com = $arr_order[$ordernum]['send_com'];
            $send_num = $arr_order[$ordernum]['send_num'];
            $send_date = $arr_order[$ordernum]['send_date'];
            $op_name = $arr_order[$ordernum]['op_name'];

            // 치환자 정리
            $arr_replace = array(
                "{주문번호}" => $ordernum ,
                "{사이트명}" => $siteInfo['s_adshop'] ,
                "{주문자명}" => $name ,
                "{회원명}" => $name ,
                "{회원아이디}" => $mem_id ,
                "{주문일}" => $order_date ,
                "{결제금액}" => $order_pay ,
                "{입금계좌번호}" => $none_bank ,
                "{택배사}" => $send_com ,
                "{운송장번호}" => $send_num ,
                "{배송일}" => $send_date ,
                "{주문상품명}" => $op_name ,
            );

            // 치환자 통합
            if(sizeof($stringsAdd)>0) {
                $arr_replace = array_merge($arr_replace , $stringsAdd);
            }

            // 사용자 발송
            // $smsInfo = _MQ("select * from smart_sms_set where ss_uid = '".$type."' limit 1");
            $smsInfo = $arr_type[$type]['info'];
            if($smsInfo['ss_status'] == "Y") {
                $text = str_replace(array_keys($arr_replace),array_values($arr_replace), $smsInfo['ss_text']);
                // 문자/알림톡 통합 발송
                $arr_send[] = array_merge(array('receive_num'=> $to, 'send_num'=> $siteInfo['s_glbtel'], 'msg'=> $text, 'title'=>$smsInfo['ss_title'], 'image'=>$smsInfo['ss_file'], 'reserve_time'=>'' ) , smsinfo_array($smsInfo , $arr_replace));
            }

            // 관리자 발송
            $smsInfo = $arr_type[$type]['infoAdmin'];
            if($smsInfo['ss_status'] == "Y") {
                $text = str_replace(array_keys($arr_replace),array_values($arr_replace), $smsInfo['ss_text']);
                // 문자/알림톡 통합 발송
                $arr_send[] = array_merge(array('receive_num'=> $siteInfo['s_glbmanagerhp'], 'send_num'=> $siteInfo['s_glbtel'], 'msg'=> $text, 'title'=>$smsInfo['ss_title'], 'image'=>$smsInfo['ss_file'], 'reserve_time'=>'' ) , smsinfo_array($smsInfo , $arr_replace));
            }
        }
    }

    return $arr_send;
}

// 문자를 발송한다.
// 인자 : 상대방 전화번호, 문자 발송 유형 , 주문번호
//				join					:회원가입,
//				order_online	:주문완료_무통장
//				order_pay			:주문완료_접수완료
//				online_pay		:무통장입금확인
//				delivery			:상품발송
//				request				:문의
// 리턴 : 없음 (자체 발송 처리)
function shop_send_sms($to,$type,$ordernum="") {

    $arr[] = array('to'=>$to,'type'=>$type,'ordernum'=>$ordernum);
    $arr_send = get_send_info($arr); // 2020-04-07 SSJ :: 문자 발송을 위한 sms정보 추출 함수 적용
    // 문자/알림톡 통합 발송
	//onedaynet_sms_multisend($arr_send);
	onedaynet_alimtalk_multisend($arr_send);

    return;
}

// 2020-04-07 SSJ :: 문자 일괄 발송 함수
// shop_send_sms() 과 동일하나 인자를 배열로 받아 일괄 발송한다.
function shop_send_sms_multi($arr) {

    $arr_send = get_send_info($arr);
    // 문자/알림톡 통합 발송
	//onedaynet_sms_multisend($arr_send);
	onedaynet_alimtalk_multisend($arr_send);

    return;
}


function get_multicategory_location($pcode,$cuid) { // 다중카테고리 정보 출력 // 2014-05-16
	$temp = array();
	$product_category = _MQ_assoc("select pct_cuid from smart_product_category where pct_pcode='$pcode'");
	foreach($product_category as $v) { if($cuid==$v['pct_cuid']) { $temp[0] = $cuid; break; } else { $temp[] = $v['pct_cuid']; }} $temp = implode("','",$temp);
	$category = _MQ_assoc("select c_parent,c_depth,c_uid from smart_category where c_uid in ('$temp')"); // ------ 1차카테고리보정: c_depth, c_uid 추가 ------ 2019-03-18 LCY
	foreach($category as $k=>$v) {

		// ------ 1차카테고리보정: c_depth, c_uid 추가 ------ 2019-03-18 LCY
		if( $v['c_depth'] == 1){
			$depth[0]['depth1']['cuid'] = $v['c_uid']; $depth[0]['depth1']['name'] = get_category_name($v['c_uid']);
			break;
		}
		// ------ 1차카테고리보정: c_depth, c_uid 추가 ------ 2019-03-18 LCY

		$cuid_temp = explode(',',$v['c_parent']);
		if($cuid_temp[0] == $cuid) {
			$depth[0]['depth1']['cuid'] = $cuid_temp[0]; $depth[0]['depth1']['name'] = get_category_name($cuid_temp[0]);
			$depth[0]['depth2']['cuid'] = $cuid_temp[1]; $depth[0]['depth2']['name'] = get_category_name($cuid_temp[1]);
			break;
		}
		else if($cuid_temp[1] == $cuid) {
			$depth[0]['depth1']['cuid'] = $cuid_temp[0]; $depth[0]['depth1']['name'] = get_category_name($cuid_temp[0]);
			$depth[0]['depth2']['cuid'] = $cuid_temp[1]; $depth[0]['depth2']['name'] = get_category_name($cuid_temp[1]);
			break;
		}
		else {
			$depth[$k]['depth1']['cuid'] = $cuid_temp[0];
			$depth[$k]['depth1']['name'] = get_category_name($cuid_temp[0]);
			$depth[$k]['depth2']['cuid'] = $cuid_temp[1];
			$depth[$k]['depth2']['name'] = get_category_name($cuid_temp[1]);
		}
	}
	return $depth;
}


// 상품목록을 불러온다.
function get_product_list($query,$order,$limit,$img_size,$name_len='100',$subname_len='100',$img_type='p_img_list_square') {
	global $_SERVER;
	// 모바일 체크
	$is_mobile = is_mobile();

	// 상품 아이콘정보.
	$product_icon = get_product_icon_info('product_name_small_icon');
	// 자동적용아이콘 - 상품쿠폰
	$coupon_icon = get_product_icon_info('product_coupon_small_icon');
	$_tmp_arr = array('pc'=>get_img_src($coupon_icon[0]['pi_img'],IMG_DIR_ICON), 'mo'=>get_img_src($coupon_icon[0]['pi_img_m'],IMG_DIR_ICON));
	$coupon_icon_src = $is_mobile ? ($_tmp_arr['mo'] ? $_tmp_arr['mo'] : $_tmp_arr['pc']) : ($_tmp_arr['pc'] ? $_tmp_arr['pc'] : $_tmp_arr['mo']);
	// 자동적용아이콘 - 무료쿠폰
	$freedelivery_icon = get_product_icon_info('product_freedelivery_small_icon');
	$_tmp_arr = array('pc'=>get_img_src($freedelivery_icon[0]['pi_img'],IMG_DIR_ICON), 'mo'=>get_img_src($freedelivery_icon[0]['pi_img_m'],IMG_DIR_ICON));
	$freedelivery_icon_src = $is_mobile ? ($_tmp_arr['mo'] ? $_tmp_arr['mo'] : $_tmp_arr['pc']) : ($_tmp_arr['pc'] ? $_tmp_arr['pc'] : $_tmp_arr['mo']);

	$assoc = _MQ_assoc("select
						*,
						(select count(*) as cnt from smart_product_talk where pt_pcode=p_code and pt_type='상품평가' and pt_intype='normal') as review_cnt
						from smart_product where p_view = 'Y' and p_option_valid_chk = 'Y' ".$query. $order . $limit);
	foreach($assoc as $k => $v) {

		// 변수 초기화
		unset($tmpicon);

		// 배송정보 추출
		$delivery_info = get_delivery_info($v['p_code']);

		// 쿠폰상품
		$ex_coupon = explode('|' , $p_info['p_coupon']);
		//if($ex_coupon[0] && $ex_coupon[1] && $coupon_icon_src) $tmpicon .= '<img src="'.$coupon_icon_src .'" alt="'. $coupon_icon['pi_title'] .'">'; // 쿠폰상품 아이콘

		// 무료배송
		if($delivery_info['status'] == '1' && $freedelivery_icon_src) $tmpicon .= '<img src="'.$freedelivery_icon_src .'" alt="'. $freedelivery_icon['pi_title'] .'">';	// 무료배송 아이콘

		// 아이콘 설정
		$p_icon_array = explode(",",$v['p_icon']);
		if(count($product_icon) > 0) {
			foreach($product_icon as $k0 => $v0) {
				if(array_search($v0['pi_uid'],$p_icon_array) !== false){
					$_tmp_arr = array('pc'=>get_img_src($v0['pi_img'],IMG_DIR_ICON), 'mo'=>get_img_src($v0['pi_img_m'],IMG_DIR_ICON));
					$_tmp_src = $is_mobile ? ($_tmp_arr['mo'] ? $_tmp_arr['mo'] : $_tmp_arr['pc']) : ($_tmp_arr['pc'] ? $_tmp_arr['pc'] : $_tmp_arr['mo']);
					if($_tmp_src) $tmpicon .= '<img src="'.$_tmp_src.'" alt="'.$v0['pi_title'].'">';
				}
			}
		}
		$v['icon'] = $tmpicon ? "<span class='upper_icon'>".$tmpicon."</span>" : "" ;

		// 옵션상품인지 체크
		$v['is_option'] = is_option($v['p_code']);

		// 바로구매/장바구니 버튼 (pcode,_type,is_option) {
		$v['order_link'] = " javascript:app_submit_from_list('".$v['p_code']."','order',".$is_option.") ";
		$v['cart_link'] = " javascript:app_submit_from_list('".$v['p_code']."','cart',".$is_option.") ";

		// 기타정보
		$thumb = $v[$img_type]?$v[$img_type]:$v['p_img_list2'];
		$img = IMG_DIR_PRODUCT.$thumb;
		$v['img'] = $img;
	//	$v['proname'] = cutstr_new($v['p_name'],$name_len);
		$v['proname'] = cutstr_new($v['p_name'],$name_len);
		$v['prosubname'] = cutstr_new($v['p_subname'],$subname_len);
		$v['link'] = "/?pn=product.view&pcode=".$v['p_code'];
		$v['catename'] = get_category_name($v['p_cuid']);
		$v['star_persent'] = get_eval_average($v['p_code']);	// 상품평 %
		$v['eval_cnt'] = number_format(get_talk_total($v['p_code'],"eval","normal"));			// 상품평 갯수
		$v['qna_cnt'] = number_format(get_talk_total($v['p_code'],"qna","normal"));			// 상품문의 갯수
		$v['price'] = number_format($v['p_price']);
		$v['screenPrice'] = number_format($v['p_screenPrice']);

		// 장바구니 추가 버튼
		$AddCartLink = '';
		if($v['p_option_type_chk'] == 'nooption') $AddCartLink = OD_PROGRAM_DIR."/shop.cart.pro.php?mode=add&pcode=".$v['p_code']."&pass_mode=dir&pass_type=cart&option_select_type=".$v['p_option_type_chk'];
		else $AddCartLink = "#none\" onclick=\"if(confirm('옵션이 있는 상품입니다. 상품페이지로 이동하시겠습니까?')) location.href='/?pn=product.view&pcode=".$v['p_code']."';";

		// 이미지 html
		if(file_exists($_SERVER['DOCUMENT_ROOT'].$img) && $thumb)
			$v['img_html'] = "<div class='item_thumb'>
								<img src='".$img."' alt='".$proname."' style='".$img_size."'/>".(!is_mobile()?"
								<!-- ◆ 상품리스트간단보기(상품박스 최상단,모두공통,위치는변경) -->
								<div class='item_quick_btn'>
									<ul>
										<li><a href='#none' class='quick_btn quick_view' data-pcode='".$v['p_code']."' title='간단보기'></a></li>
										<li><a href='/?pn=product.view&pcode=".$v['p_code']."' class='quick_btn quick_blank' title='새창보기' target='_blank'></a></li>
										<!-- 옵션있는 상품의 경우 경고창 -->
										<li>
											<a href=\"".$AddCartLink."\" class='quick_btn quick_cart' title='장바구니 담기'></a>
										</li>
										<!-- 찜하기하면 wish_hit 클래스추가 -->
										<li><a href='#none' class='quick_btn quick_wish ajax_wish".(GetWished($v['p_code']) === true?' wish_hit if_wish':null)."' data-code='".$v['p_code']."' title='찜하기'></a></li>
									</ul>
								</div>
								<!-- / ◆ 상품리스트간단보기 -->":null)."
							</div>";
		else
			$v['img_html'] = "<div class='item_thumb'></div>";

		$data[$k] = $v;
	}

	return $data;

}

// - 입점업체 배열화 ---
function arr_company(){
	$arr_customer = array();
	$res = _MQ_assoc("SELECT cp_id , cp_name FROM smart_company ORDER BY cp_name asc ");
	foreach( $res as $k=>$v ){
		$arr_customer[$v['cp_id']] = $v['cp_name'] . "(아이디 : ". $v['cp_id'] .")";
	}
	return $arr_customer;
}
function arr_company2(){
	$arr_customer = array();
	$res = _MQ_assoc("SELECT cp_id , cp_name FROM smart_company ORDER BY cp_name asc ");
	foreach( $res as $k=>$v ){
		$arr_customer[$v['cp_id']] = $v['cp_name'];
	}
	return $arr_customer;
}
// - 입점업체 배열화 ---




// LCY::COUPON :: 첫구매
function couponIssuedAutoType1($_ordernum)
{

	$issuedAutoType = '1';

	// 자동발급 쿠폰에 지정된 쿠폰이 있는지 체크
	$resCouponSet = _MQ_assoc("select *from smart_individual_coupon_set where ocs_issued_type = 'auto' and ocs_view = 'Y' and ocs_issued_type_auto = '".$issuedAutoType."'     ");
	if( count($resCouponSet) < 1){ return false; }

	// 주문번호를 통해 아이디를 가져온다.
	$getMemberID = _MQ_result("select o_mid  from smart_order where o_ordernum = '".$_ordernum."' and o_memtype = 'Y' ");
	$mem_info = _individual_info($getMemberID);
	if( count($mem_info) < 1){ return false; }

	$rowChk = _MQ("select count(*) as cnt from smart_order where o_paystatus = 'Y' and o_canceled = 'N' and o_mid = '".$mem_info['in_id']."' and o_memtype = 'Y'    ");
	if( $rowChk['cnt'] != 1){ return ; }
	foreach($resCouponSet as $csk=>$csv){
		// 쿠폰 기본체크
		$isChk = couponSetIssuedChk($csv, $mem_info);
		if( $isChk !== true){ continue; }

		give_coupon($mem_info['in_id'], $csv); // 쿠폰발급
	}
}

// LCY::COUPON :: 구매/접수완료
function couponIssuedAutoType2($_ordernum)
{

	$issuedAutoType = '2';

	// 자동발급 쿠폰에 지정된 쿠폰이 있는지 체크
	$resCouponSet = _MQ_assoc("select *from smart_individual_coupon_set where ocs_issued_type = 'auto' and ocs_view = 'Y' and ocs_issued_type_auto = '".$issuedAutoType."'     ");
	if( count($resCouponSet) < 1){ return false; }

	// 주문번호를 통해 아이디를 가져온다.
	$getMemberID = _MQ_result("select o_mid  from smart_order where o_ordernum = '".$_ordernum."' and o_memtype = 'Y' ");
	$mem_info = _individual_info($getMemberID);
	if( count($mem_info) < 1){ return false; }

	// 주문번호와 함께 체킹
	$rowChk = _MQ("select count(*) as cnt from smart_order where o_paystatus = 'Y' and o_canceled = 'N' and o_mid = '".$mem_info['in_id']."' and o_memtype = 'Y' and o_ordernum = '".$_ordernum."'     ");
	if( $rowChk['cnt'] < 1){ return ; }
	foreach($resCouponSet as $csk=>$csv){
		// 쿠폰 기본체크
		$isChk = couponSetIssuedChk($csv, $mem_info);
		if( $isChk !== true){ continue; }

		give_coupon($mem_info['in_id'], $csv); // 쿠폰발급
	}
}

// LCY::COUPON :: 생일축하
function couponIssuedAutoType3()
{

	$issuedAutoType = '3';

	// 자동발급 쿠폰에 지정된 쿠폰이 있는지 체크
	$resCouponSet = _MQ_assoc("select *from smart_individual_coupon_set where ocs_issued_type = 'auto' and ocs_view = 'Y' and ocs_issued_type_auto = '".$issuedAutoType."'     ");
	if( count($resCouponSet) < 1){ return false; }

	$resIndr = _MQ_assoc("select * from smart_individual where right(in_birth,5) = '".date('m-d')."' and 	in_out = 'N' and in_sleep_type = 'N' and in_userlevel = '2'  ");
	if( count($resIndr) < 1){ return false; }

	foreach($resIndr as $mem_key=>$mem_info){
		foreach($resCouponSet as $csk=>$csv){
			$isChk = couponSetIssuedChk($csv, $mem_info);
			if( $isChk !== true){ continue; }
			give_coupon($mem_info['in_id'], $csv); // 쿠폰발급
		}
	}
}

//LCY::COUPON 새 회원 쿠폰 발급 -- 반드시 회원가입 프로세서 이후에서만 실행해야한다.
function couponIssuedAutoType4($id){

	// 자동발급 쿠폰에 지정된 쿠폰이 있는지 체크
	$issuedAutoType = '4';
	$mem_info = _individual_info($id);
	// if( date('Y-m-d',strtotime($mem_info['in_rdate'])) != date('Y-m-d')){ return false;  } // 오늘이 아니라면

	$resCouponSet = _MQ_assoc("select *from smart_individual_coupon_set where ocs_issued_type = 'auto' and ocs_view = 'Y' and ocs_issued_type_auto = '".$issuedAutoType."'     ");
	foreach($resCouponSet as $csk=>$csv){
		// 쿠폰 기본체크
		$isChk = couponSetIssuedChk($csv, $mem_info);
		if( $isChk !== true){ continue; }
		give_coupon($mem_info['in_id'], $csv); // 쿠폰발급
	}
}

//SSJ::COUPON 출석체크 지연발급 쿠폰 자동발급
function couponIssuedAutoType5(){
	// 발급대기 쿠폰 추출
	$res = _MQ_assoc(" select * from smart_promotion_attend_coupon_ready where acr_status = 'N' and acr_idate <= '". date('Y-m-d') ."'  ");
	if(count($res) > 0){
		$arr = array();
		foreach($res as $k=>$v){
			$arr[$v['acr_ocsuid']][] = $v['acr_inid'];
		}
		foreach($arr as $k=>$v){
			// 자동발급 쿠폰에 지정된 쿠폰이 있는지 체크
			$issuedAutoType = '5';
			$couponSetData = _MQ(" select * from smart_individual_coupon_set where ocs_view = 'Y' and ocs_issued_type = 'auto' and ocs_issued_type_auto = '".$issuedAutoType."' and ocs_uid = '". $k ."' ");
			// 발급가능한 쿠폰일경우에만 쿠폰발급
			foreach($v as $sk=>$sv){
				$mem_info = _individual_info($sv);
				$couponSetIssuedChk = couponSetIssuedChk($couponSetData,$mem_info);
				if($couponSetIssuedChk == true){
					give_coupon($sv, $couponSetData);
				}
			}
		}
		_MQ_noreturn(" update smart_promotion_attend_coupon_ready set acr_status = 'Y' where acr_status = 'N' and acr_idate <= '". date('Y-m-d') ."' ");
	}
}


// 휴면계정 별도 저장처리 -- smart_individual -> smart_individual_sleep 복사 후 수정
// 아이디가 있다면 임시 적으로 휴면 계정 전환
function member_sleep_backup($_id=false){
		global $siteInfo;

    // @ -- 2017-06-01 LCY -- 휴면계정개선패치 :: 필드업데이트
    UpdateMemberTable();

	if($_id == false){
		$mr = _MQ_assoc("select * from smart_individual where  in_userlevel != '9' AND in_name not in ('휴면전환' , '탈퇴한회원') and in_sleep_type != 'Y'  and in_ldate < '". date('Y-m-d H:i:s',strtotime("- ". $siteInfo['member_sleep_period'] ." month"))."' ");
	}else{
		$mr = _MQ_assoc("select *from smart_individual where in_id = '".$_id."' ");
	}

	foreach( $mr as $k=>$v ){

		// --- 복사 ---
		$_field1 = $_field2 = array();
		foreach( $v as $sk=>$sv ){

			$_field1[] = $sk . " = '". addslashes(stripslashes($sv)) ."' " ;

			if( !in_array( $sk , array("in_id" , "in_name",'in_sleep_type', 'in_sleep_request' )) ){$_field2[] = $sk . " = '' " ; }
		}

		// --- smart_individual_sleep 정보 추가 ---
		$sque = " insert smart_individual_sleep set ins_rdate = now(),  ". implode(" , " , array_filter($_field1)) ." ";
		_MQ_noreturn($sque);
		// -- 휴면전환 여부를 동일 하게 Y 로
		_MQ_noreturn(" update smart_individual_sleep  set  in_sleep_type = 'Y' where in_id='". $v['in_id'] ."' ");

		// --- smart_individual 정보 변경 ---
		_MQ_noreturn(" update smart_individual set in_name='휴면전환' , in_sleep_type = 'Y', ". implode(" , " , array_filter($_field2)) ." where in_id='". $v['in_id'] ."' ");

	}
}


// 휴면계정 복귀처리 -- smart_individual_sleep --> smart_individual 수정 후 삭제
function member_sleep_return( $_id ){

	global $siteInfo;
    // @ -- 2017-06-01 LCY -- 휴면계정개선패치 :: 필드업데이트
    UpdateMemberTable();

	$mr = _MQ("select * from smart_individual_sleep where in_id='". $_id ."' ");
	if(sizeof($mr) > 0 ) {
		// --- 복사 ---
		$_field = array();
		foreach( $mr as $sk=>$sv ){if( !in_array( $sk , array("ins_rdate" , "ins_mailing" , "in_id", 'in_sleep_type' , 'in_ldate' , 'in_sleep_request' )) ){

				// LCY 2017-12-09 -- 회원등급정책 => 휴면회원해제 시 등급 초기화 일 시 --
				if($siteInfo['member_return_groupinit'] == 'Y' && $sk == 'in_mgsuid' ) {
					// -- 등급기본순위를 가져온다.
					$defaultMgsUid = _MQ_result("select mgs_uid from smart_member_group_set  where 1 and mgs_rank  = 1 ");
					if( $defaultMgsUid != ''){ $sv = $defaultMgsUid; }
				}
				$_field[] = $sk . " = '". addslashes(stripslashes($sv)) ."' " ;
			}
		}
		$sque = " update smart_individual 	set		in_ldate =  now() , in_sleep_type = 'N' ,". implode(" , " , array_filter($_field)) ." where in_id ='". $mr['in_id'] ."' ";
		_MQ_noreturn($sque);

		// --- 삭제 ---
		_MQ_noreturn(" delete from smart_individual_sleep where in_id='". $mr['in_id'] ."' ");
	}
	return (sizeof($mr) > 0 ? "Y" : "N");
}


// 주문의 정산상태 체크 -- 주문상품 고유번호 --> 배열형태 . 예) array(111, 222);
function order_settlement_status_opuid($arr_opuid) {

	// 주문번호정보 추출
	$arr_ordernum = array();
	$opr = _MQ_assoc("select op_oordernum from smart_order_product where op_cancel='N' and op_uid in ('". implode("' , '" , array_values($arr_opuid) ) ."') group by op_oordernum ");
	foreach($opr as $k=>$v){
		$arr_ordernum[$v['op_oordernum']]++;
	}

	// 주문상품 정산상태 추출
	$arr_op = array();
	$opr = _MQ_assoc("select op_oordernum , op_settlementstatus , count(*) as cnt from smart_order_product where op_cancel='N' and op_oordernum in ('". implode("' , '" , array_keys($arr_ordernum) ) ."') group by op_oordernum , op_settlementstatus ");
	foreach($opr as $k=>$v){
		$arr_op[$v['op_oordernum']][$v['op_settlementstatus']] = $v['cnt'];
	}

	if( sizeof($arr_op) > 0 ) {
		foreach($arr_op as $k=>$v){
			$_status = "";
			if($v["none"] > 0 ) {$_status = "none";}
			else if($v["ready"] > 0 ) {$_status = "ready";}
			else if($v["complete"] > 0 ) {$_status = "complete";}
			if( $_status  ){
				_MQ_noreturn(" update smart_order set o_paystatus3='". $_status ."' where o_ordernum='". $k ."' ");// 주문 정산상태 변경
			}
		}
	}

	return $arr_op ;

}


// 세금계산서 연동 - 바로빌 로그 저장
function tax_log_insert($btuid , $mode , $code , $msg){
	$que = "
		insert smart_baro_tax_log set
			tl_btuid = '". $btuid ."',
			tl_mode ='". $mode ."',
			tl_code = '". $code ."',
			tl_remark ='". $msg ."',
			tl_rdate = now()
	";
	_MQ_noreturn($que);
}

// 텍스트 연동 정보 입력하기 _text_info_insert( 테이블명 , 연동 부모데이터 고유번호 , 키워드타입(상세요강, 자기소개서등), text형태 값)
function _text_info_insert( $tablename , $datauid , $keytype , $value , $trigger=null ){
	// 데이터 확인
	$r = _MQ("select count(*) as cnt from smart_table_text where ttt_tablename = '{$tablename}' and ttt_datauid = '{$datauid}' and ttt_keyword = '{$keytype}'");

	if(trim($value) || $trigger == "ignore") {
		if($r[cnt] > 0) {
			// 데이터 수정
			_MQ_noreturn(" update smart_table_text set ttt_value = '" . mysql_real_escape_string($value) . "' where ttt_tablename = '{$tablename}' and ttt_datauid = '{$datauid}' and ttt_keyword = '{$keytype}' ");
		}
		else {
			// 데이터 입력
			_MQ_noreturn(" insert smart_table_text set ttt_tablename = '{$tablename}' , ttt_datauid = '{$datauid}' , ttt_keyword = '{$keytype}' , ttt_value = '" . mysql_real_escape_string($value) . "'");
		}
	}

	// 기존 값이 있고 넘어온 값이 없다면 기존 값 삭제 kms 2019-05-14
	if( $r[cnt] > 0 && trim($value) == "" ){
		_text_info_delete( $tablename , $datauid , $keytype );
	}

}

// 일반 연동 정보 입력하기 _tail_info_delete( 테이블명 , 연동 부모데이터 고유번호 , 키워드타입(상세요강, 자기소개서등))
function _text_info_delete( $tablename , $datauid , $keytype ){
	// 이전 데이터 삭제
	_MQ_noreturn("delete from smart_table_text where ttt_tablename = '{$tablename}' and ttt_datauid = '{$datauid}' and ttt_keyword = '{$keytype}'");
}

// 일반 연동 정보 입력하기 _text_info_extraction( 테이블명 , 연동 부모데이터 고유번호 , 키워드타입(상세요강, 자기소개서등))
function _text_info_extraction( $tablename , $datauid ){
	// 데이터 추출
	$arr = array();
	$r = _MQ_assoc("select ttt_keyword , ttt_value from smart_table_text where ttt_tablename = '{$tablename}' and ttt_datauid = '{$datauid}' order by ttt_uid asc ");
	foreach($r as $k=>$v){
		$arr[$v['ttt_keyword']] = $v['ttt_value'];
	}
	return $arr;
}

// 위시 여부 확인
function GetWished($pcode) {

	$ir = _MQ("select count(*) as cnt from smart_product_wish where pw_inid='" . get_userid() . "' and pw_pcode='". $pcode ."'");
	if($ir['cnt'] > 0) return true;
	else return false;
}

/* --------------------------------------------------------------------------- */
// Mysql에 테이블에 필드가 있는지 확인 (필드가 있다면 1반환)
if(!function_exists('IsField')) {
    function IsField($Table, $Field) {

        $sql = ' show columns from ' . $Table . ' like \''.$Field.'\' ';
        $result = mysql_query($sql);

        if(@mysql_num_rows($result)) return true;
        else return false;
    }
}

/* --------------------------------------------------------------------------- */
// Mysql 테이블의 정보 출력 (인덱스, 컬럼 리스트, 컬럼 데이터 반환)
if(!function_exists('IsTableData')) {
    function IsTableData($Table) {

        // 초기값
        $ColnumNum = 0;
        $IndexNum = 0;

        // 테이블 인덱스 정보
        $IndexResult = mysql_query(' show index from ' . $Table);
        while($IndexData = mysql_fetch_assoc($IndexResult)){

            $Index[$IndexNum] = $IndexData;
            $IndexNum++;
        }


        // 테이블 컬럼 상세 정보
        $ColumnResult = mysql_query(' show columns from ' . $Table);
        while($ColumnData = mysql_fetch_assoc($ColumnResult)){

            $Column['list'][$ColnumNum] = $ColumnData['Field'];
            $Column['data'][$ColumnData['Field']] = $ColumnData;
            $Column['data'][$ColumnData['Field']]['number'] = $ColnumNum;

            $ColnumNum++;
        }


        // 정보를 모두 변수에 담음
        $list['index'] = $Index; // 인덱스 정보
        $list['columns'] = $Column; // 컬럼 정보


        return $list;
    }
}

// @ -- 테이블 확인
if(!function_exists('IsTable')) {
    function IsTable($table)
    {
        $row_chk = _MQ("SHOW TABLES LIKE '".$table."'");
        if(count($row_chk) > 0){ return true; }
        else{ return false; }
    }
}

// @ -- 컬럼 추가 ::
if(!function_exists('AddFeidlUpdate')) {
    function AddFeidlUpdate($table,$column_data = array())
    {

        if( count($column_data) < 1){ return false; }
        $field = $column_data['Field']; // 필드
        $type = $column_data['Type']; // 타입
        $default = $column_data['Default']; // 기본값
        $extra = $column_data['Extra']; // 기본함수
        $add_type = '';
        if( $column_data['Null'] == 'NO'){
            $add_type .= $default == '' ? " not null "  : " not null default '".$default."'  "  ;
        }else{
            $add_type .= $default == '' ? ' default null ' : " default '".$default."'  " ;
        }
        _MQ_noreturn("alter table ".$table." add ".$field."  ".$type." ".$add_type." ".$extra);
    }
}


// @ -- $member_table_name 와  $membersleep_table_name 테이블을 비교하여 동기화 시켜준다.
if(!function_exists('UpdateMemberTable')) {
    function UpdateMemberTable()
    {
        // @ -- 고정 테이블 및 컬럼 셋팅
        $member_table_name = "smart_individual";
        $membersleep_table_name = "smart_individual_sleep";
        $arr_except_columns = array('ins_rdate','ins_mailing'); // 휴면계정 컬럼 삭제 시 제외할 컬럼


        // @ -- 테이블이 있는지 검사
        if( IsTable($member_table_name) == false || IsTable($membersleep_table_name) == false){ return false; }

        // @ -- 회원,휴면회원 테이블 검사   // 1 차배열 정보 => [index], [columns][list] : 칼럼명, [columns][data] : 칼럼속성
        $is_table_member = IsTableData($member_table_name);
        $is_table_member_sleep = IsTableData($membersleep_table_name);
        $arr_update_data = array(); //  업데이트 컬럼과 삭제될 컬럼 배열 초기화


        if( count($is_table_member) < 1 || count($is_table_member_sleep) < 1 ) { return false; }
        // @ -- 회원테이블 컬럼정보에서 회원휴면 테이블 칼럼정보와 비교한다.
        foreach($is_table_member['columns']['list'] as $k=>$v){
            if( IsField($membersleep_table_name,$v) == true) { continue; }
            AddFeidlUpdate($membersleep_table_name,$is_table_member['columns']['data'][$v]); // $membersleep_table_name 테이블에 칼럼 추가
            $arr_update_data['add'][$v] = $is_table_member['columns']['data'][$v]; // 단순기록
        }

        // @ -- 휴면테이블 컬럼정보에서 회원테이블에 없는 컬럼은 삭제처리 한다.
         foreach($is_table_member_sleep['columns']['list'] as $k=>$v){
            if( IsField($member_table_name,$v) == true || in_array($v,$arr_except_columns) == true) { continue; }
            _MQ_noreturn(" ALTER TABLE  ".$membersleep_table_name." DROP  `".$v."` "); // 컬럼 삭제
            $arr_update_data['drop'][$v] = $is_table_member_sleep['columns']['data'][$v]; // 단순기록
         }

        return $arr_update_data ;
    }
}




########## 보안서버 상태정보 추출 - 보안서버 ::: JJC ##########
function ssl_condition_info(){

	global $siteInfo;

	$arr = array();


	// --- 전체설정 ---
		// 진행여부
		$arr['ssl_status'] = 'N';// 보안서버 진행여부  - 기본 미진행
		$arr['desc']['ssl_status'] = '보안서버 진행여부 - Y:진행, N:미진행';

		//			1) 보안서버 사용여부 체킹
		//			2) 보안서버 진행상태 체킹
		//			3) 보안서버 사용기간 체킹
		if(
			$siteInfo['s_ssl_check'] == 'Y' &&
			$siteInfo['s_ssl_status'] == '진행' &&
			$siteInfo['s_ssl_sdate'] <= DATE('Y-m-d') &&
			$siteInfo['s_ssl_edate'] >= DATE('Y-m-d')
		){
			$arr['ssl_status'] = 'Y';// 진행
		}

		$arr['ssl_domain'] = "https://" . trim($siteInfo['s_ssl_domain']) . ($siteInfo['s_ssl_port'] ? ":" . trim($siteInfo['s_ssl_port']) : "");
		$arr['desc']['ssl_domain'] = '보안서버 도메인';
	// --- 전체설정 ---


	// --- 관리자설정 ---
		$arr['ssl_admin_status'] = $siteInfo['s_ssl_admin_loc'];// 관리자 보안서버 적용페이지 - N:미사용, A:전체페이지 ,  P:개인정보 이용 페이지
		$arr['desc']['ssl_admin_status'] = '관리자 보안서버 적용페이지 - N:미사용, A:전체페이지 ,  P:개인정보 이용 페이지';
		$page_ex = explode("§" , $siteInfo['s_ssl_admin_page']);
		if(sizeof($page_ex) > 0 ) {
			foreach( $page_ex as $k=>$v ){
				$page_ex2 = explode("|" , $v);
				$arr['ssl_admin_page'][] = trim((isset($page_ex2[1])?$page_ex2[1]:null));
			}
		}
		$arr['desc']['s_ssl_admin_page'] = '관리자 보안서버 추가 적용페이지';
	// --- 관리자설정 ---


	// --- 모바일 사용자 설정 ---
		$arr['ssl_m_status'] = $siteInfo['s_ssl_m_loc'];// 모바일 보안서버 적용페이지 - N:미사용, A:전체페이지 ,  P:개인정보 이용 페이지
		$arr['desc']['ssl_m_status'] = '모바일 보안서버 적용페이지 - N:미사용, A:전체페이지 ,  P:개인정보 이용 페이지';
		$page_ex = explode("§" , $siteInfo['s_ssl_m_page']);
		if(sizeof($page_ex) > 0 ) {
			foreach( $page_ex as $k=>$v ){
				$page_ex2 = explode("|" , $v);
				$arr['ssl_m_page'][] = trim((isset($page_ex2[1])?$page_ex2[1]:null));
			}
		}
		$arr['desc']['s_ssl_admin_page'] = '모바일 보안서버 추가 적용페이지';
	// --- 모바일 사용자 설정 ---


	// --- PC 사용자 설정 ---
		$arr['ssl_pc_status'] = $siteInfo['s_ssl_pc_loc'];// PC 보안서버 적용페이지 - N:미사용, A:전체페이지 ,  P:개인정보 이용 페이지
		$arr['desc']['ssl_pc_status'] = 'PC 보안서버 적용페이지 - N:미사용, A:전체페이지 ,  P:개인정보 이용 페이지';
		$page_ex = explode("§" , $siteInfo['s_ssl_pc_page']);
		if(sizeof($page_ex) > 0 ) {
			foreach( $page_ex as $k=>$v ){
				$page_ex2 = explode("|" , $v);
				$arr['ssl_pc_page'][] = trim((isset($page_ex2[1])?$page_ex2[1]:null));
			}
		}
		$arr['desc']['ssl_pc_page'] = 'PC 보안서버 추가 적용페이지';

		// PC 사용 이미지
		switch($siteInfo['s_ssl_pc_img']){
			//미사용
			case "N":
				$arr['ssl_pc_img'] = '';
			break;

			//UCERT SSL
			case "U":
				$arr['ssl_pc_img'] = '<!--UCERT Certificate Mark--><img src="https://www.ucert.co.kr/images/maincenterContent/trustlogo/ucert_black.gif" width="92" height="103" align="absmiddle" border="0" style="cursor:pointer" Onclick=javascript:window.open("https://www.ucert.co.kr/trustlogo/UCERT_TRUSTLOGO.html?sealnum='.$siteInfo['s_ssl_pc_sealnum'].'","mark","scrollbars=no,resizable=no,width=530,height=468");<!--UCERT Certificate Mark-->';
			break;

			//KISA SSL
			case "K":
				$arr['ssl_pc_img'] = '<!--KISA Certificate Mark--><img src="https://www.ucert.co.kr/image/trustlogo/s_kisa.gif" width="65" height="63" align="absmiddle" border="0" style="cursor:pointer" Onclick=javascript:window.open("https://www.ucert.co.kr/trustlogo/sseal_cert.html?sealnum='.$siteInfo['s_ssl_pc_sealnum'].'&sealid='.$siteInfo['s_ssl_pc_sealid'].'","mark","scrollbars=no,resizable=no,width=565,height=780");><!--KISA Certificate Mark-->';
			break;

			//Alpha SSL
			case "A":
				$arr['ssl_pc_img'] = '<!-----Alpha SEAL Start----------><img src="https://www.ucert.co.kr/image/trustlogo/alphassl_seal.gif" width="115" height="55" align="absmiddle" border="0" style="cursor:pointer" Onclick=javascript:window.open("https://www.ucert.co.kr/trustlogo/sseal_alphassl.html?sealnum='.$siteInfo['s_ssl_pc_sealnum'].'","mark","scrollbars=no,resizable=no,width=420,height=670");><!-----Alpha SEAL End---------->';
			break;

			//Comodo SSL
			case "C":
				$arr['ssl_pc_img'] = '<!-----Comodo SEAL Start----------><img src="https://www.ucert.co.kr/images/maincenterContent/trustlogo/PositiveSSL_tl_trans.gif" style="cursor:pointer" Onclick=javascript:window.open("https://www.ucert.co.kr/trustlogo/sseal_comodo.html?sealnum='.$siteInfo['s_ssl_pc_sealnum'].'","mark","scrollbars=no,resizable=no,width=420,height=500");><!-----Comodo SEAL End---------->';
			break;

			//기타
			case "E":
				$arr['ssl_pc_img'] = $siteInfo['s_ssl_pc_img_etc'];
			break;

		}// PC 사용 이미지
		$arr['desc']['ssl_pc_img'] = 'PC 보안서버 인증이미지';

	// --- PC 사용자 설정 ---


	return $arr;

}
########## 보안서버 상태정보 추출 - 보안서버 ::: JJC ##########



// 상품현황
/*
* $Type
* all: 전체상품
* view: 노출상품
* hide: 숨김상품
* best: 베스트상품
* new: 신규상품
* stock: 재고량 10개 이하 상품
* 기타: 쿼리문을 직접 작성하는 경우
*/
function DivisionProduct($Type='all') {
	$Where = '';
	if($Type == 'all') $Where = "";
	else if($Type == 'view') $Where = " and `p_view` = 'Y' ";
	else if($Type == 'hide') $Where = " and `p_view` = 'N' ";
	else if($Type == 'best') $Where = " and `p_bestview` = 'Y' ";
	else if($Type == 'new') $Where = " and `p_newview` = 'Y' ";
	else if($Type == 'stock') $Where = " and `p_stock` <= 10 ";
	else $Where = $Type;

	$Data = _MQ(" select count(*) as cnt from `smart_product` where (1) {$Where} ");
	return ($Data['cnt']?$Data['cnt']:0);
}

// 회원현황
/*
* $Type
* all: 전체회원
* use: 정상회원
* sleep: 휴면회원
* leave: 탈퇴회원
* 기타: 쿼리문을 직접 작성하는 경우
*/
function DivisionMember($Type='all') {
	$Where = '';
	if($Type == 'all') $Where = ""; // 전체
	else if($Type == 'use') $Where = " and `in_sleep_type` = 'N' and `in_out` = 'N' "; // 정상
	else if($Type == 'sleep') $Where = " and `in_out` = 'N' and `in_sleep_type` = 'Y' "; // 휴면
	else if($Type == 'leave') $Where = " and `in_out` = 'Y' "; // 탈퇴
	else $Where = $Type;

	$Data = _MQ(" select count(*) as `cnt` from `smart_individual` where (1) {$Where} "); // 전체 회원 수
	return ($Data['cnt']?$Data['cnt']:0);
}





	// JJC ::: 브랜드 정보 추출  ::: 2017-11-03
	//		basic : 기본정보
	//		all : 브랜드 전체 정보
	function brand_info( $_type = "basic" ){
		$arr_brand = array();
		$brand_que = " select * from smart_brand where c_depth=1 and c_view = 'Y'order by c_idx asc";
		$brand_res = _MQ_assoc($brand_que);
		foreach($brand_res as $k=>$v){
			if($_type == "all") {
				$arr_brand[$v['c_uid']] = $v;
			}
			else {
				$arr_brand[$v['c_uid']] = $v['c_name'];
			}
		}
		return $arr_brand;
	}
	// JJC ::: 브랜드 정보 추출  ::: 2017-11-03


/**
	* -- LCY :: 2017-11-07 관리자 메뉴중 접근가능한 메뉴가 최소 1개라도 있는지 체크
**/
function adminMenuChk($app_current_link, $menuUid)
{
	global $arrPublicAdminPage;
	$siteAdmin = get_site_admin(); // 관리자 정보

	if( in_array($app_current_link, $arrPublicAdminPage) == true){ return true; }

	// -- 운영자 정보가 없을경우
	if( $siteAdmin['a_uid'] == ''){ return false; }

	// -- 최고관리자 처리
	if( $siteAdmin['a_type'] =='master'){ return true; }

	// -- 1뎁스중 권한이 있는지 체크 없다면 false
	$chkDepth1 = _MQ("select count(*) as cnt from smart_admin_menu as am inner join smart_admin_menu_set as ams on(ams.ams_amuid = am.am_uid) where am_view = 'Y' and am_depth = '1' and  ams.ams_uid is not null and ams.ams_auid = '".$siteAdmin['a_uid']."'   order by am_idx asc ");
	if($chkDepth1['cnt'] < 1){ return false; }

		// -- 2뎁스중 권한이 있는지 체크 없다면 false
	$chkDepth2 = _MQ("select count(*) as cnt from smart_admin_menu as am inner join smart_admin_menu_set as ams on(ams.ams_amuid = am.am_uid) where am_view = 'Y' and am_depth = '2' and  ams.ams_uid is not null and ams.ams_auid = '".$siteAdmin['a_uid']."'   order by am_idx asc ");
	if($chkDepth2['cnt'] < 1){ return false; }

		// -- 3뎁스중 권한이 있는지 체크 없다면 false
	$chkDepth3 = _MQ("select count(*) as cnt from smart_admin_menu as am inner join smart_admin_menu_set as ams on(ams.ams_amuid = am.am_uid) where am_view = 'Y' and am_depth = '3' and  ams.ams_uid is not null and ams.ams_auid = '".$siteAdmin['a_uid']."'   order by am_idx asc ");
	if($chkDepth3['cnt'] < 1){ return false; }

	// -- 파일명에 속한 권한이 있는지 체크
	$chkCurrent = _MQ("select count(*) as cnt from smart_admin_menu as am inner join smart_admin_menu_set as ams on(ams.ams_amuid = am.am_uid) where am_view = 'Y' and
		( am.am_link = '".$app_current_link."' or am.am_uid = '".$menuUid."'  )and  ams.ams_uid is not null and ams.ams_auid = '".$siteAdmin['a_uid']."'   order by am_idx asc ");
	if($chkCurrent['cnt'] < 1){ return false; }


	return true;

}
// --  LCY -- 관리자 메뉴를 가져온다.
function adminMenuSet()
{
	$siteAdmin = get_site_admin(); // 관리자 정보

	$arrAdminMenuSet = array();
	if( $siteAdmin['a_type'] == 'master'){
		$allAdminMenu = _MQ_assoc("select am_uid from smart_admin_menu where am_view = 'Y' order by am_idx asc ");
		foreach($allAdminMenu as $k=>$v){ $arrAdminMenuSet[$v['am_uid']] = true; }
	}


	$resAdminMenuSet = _MQ_assoc("select ams_amuid from smart_admin_menu_set where ams_auid = '".$siteAdmin['a_uid']."' ");
	foreach($resAdminMenuSet as $k=>$v){ $arrAdminMenuSet[$v['ams_amuid']] = true; }
	return $arrAdminMenuSet;
}

// -- LCY -- 회원탈퇴처리
function memberGetOut($inID, $type='admin')
{

	if( $inID == ''){ return false;  }
	/*
		- 하이센스의 경우 in_out 이라는 칼럼이 있기때문에 in_name			= '탈퇴회원', ,in_pw			= '탈퇴회원' 처리 불필요하여 삭제
	*/
		// --query 사전 준비 ---
		$sque = "
			in_email		= ''
			, in_name			= '탈퇴회원'
			,in_emailsend	= 'N'
			,in_smssend		= 'N'
			,in_tel			= ''

			,in_tel2 = ''
			,in_zip1 = ''
			,in_zip2 = ''
			,in_address1 = ''
			,in_address2 = ''
			,in_address_doro = ''
			,in_zonecode= ''
			,in_sex= ''
			,in_birth=''
			,in_cancel_bank=''
			,in_cancel_bank_account= ''
			,in_cancel_bank_name=''
			,in_auth= 'N'
			,in_mgsuid= '0'

			,in_odate		= now()
			,in_out			= 'Y'
			,in_point		= '0'
			, sns_join = 'N'
			, fb_join = 'N'
			, fb_encid = ''
			, ko_join = 'N'
			, ko_encid = ''
			, nv_join = 'N'
			, nv_encid = ''
		";
		// --query 사전 준비 ---
		$que = " update smart_individual set ".$sque." where in_id='". $inID ."' and in_userlevel != '9' and in_out != 'Y'  ";
		_MQ_noreturn($que);
		return true;
}


//// SSJ ::: 상품 정렬 순서 유효성검사 && 재정렬 ::: 2017-11-13
//function product_resort(){
//	// 정렬그룹의 유효성 검사
//	$arr_pinfo = array();
//	$arr_sort_group = array();
//	$res = _MQ_assoc(" select p_code, p_sort_group, p_sort_idx, p_idx from smart_product where 1 order by p_sort_group asc , p_sort_idx asc , p_idx asc ");
//	$pidx = 1;
//	foreach($res as $k=>$v){
//		$trigger_idx = true; // false 이면 업데이트
//
//		$arr_sort_group[$v['p_sort_group']] = $arr_sort_group[$v['p_sort_group']]+1;
//
//		if($pidx <> $v['p_idx']) $trigger_idx = false;
//		if( ($arr_sort_group[$v['p_sort_group']]) <> $v['p_sort_idx']) $trigger_idx = false;
//
//		if(!$trigger_idx){
//			$arr_pinfo[$v['p_code']] = array('p_idx'=>$pidx, 'p_sort_group'=>$v['p_sort_group'], 'p_sort_idx'=>$arr_sort_group[$v['p_sort_group']]);
//		}
//
//		$pidx++;
//	}
//
//	// 잘못된 데이터가 있으면 재정렬
//	if(sizeof($arr_pinfo)>0){
//		foreach($arr_pinfo as $k=>$v){
//			_MQ_noreturn(" update smart_product set p_sort_group = '". $v['p_sort_group'] ."', p_sort_idx = '". $v['p_sort_idx'] ."', p_idx = '". $v['p_idx'] ."' where p_code = '". $k ."' ");
//		}
//	}
//}
//// SSJ ::: 상품 정렬 순서 유효성검사 && 재정렬 ::: 2018-09-03 수정본
function product_resort(){
	// 정렬그룹의 유효성 검사
	$arr_pinfo = array();
	$arr_idx = array();
	$arr_gidx = array();
	$arr_sort_group = array();
	$res = _MQ_assoc(" select p_code, p_sort_group, p_sort_idx, p_idx from smart_product where 1 order by p_sort_group asc , p_sort_idx asc , p_idx asc ");
	$pidx = 1;
	foreach($res as $k=>$v){
		$trigger_idx = true; // false 이면 업데이트

		$arr_sort_group[$v['p_sort_group']] = $arr_sort_group[$v['p_sort_group']]+1;

		if($pidx <> $v['p_idx']){
			$arr_idx[$pidx-$v['p_idx']][] = $v['p_code'];
		}
		if( ($arr_sort_group[$v['p_sort_group']]) <> $v['p_sort_idx']){
			$arr_gidx[$arr_sort_group[$v['p_sort_group']]-$v['p_sort_idx']][] = $v['p_code'];
		}

		$pidx++;
	}

	if(sizeof($arr_idx)>0){
		foreach($arr_idx as $k=>$v){
			_MQ_noreturn(" update smart_product set p_idx = (p_idx + '". $k ."') where p_code in ('". implode("','" , $v) ."') ");
		}
	}
	if(sizeof($arr_gidx)>0){
		foreach($arr_gidx as $k=>$v){
			_MQ_noreturn(" update smart_product set p_sort_idx = (p_sort_idx + '". $k ."') where p_code in ('". implode("','" , $v) ."') ");
		}
	}
}
// KAY :: 메인 선택 상품 정렬순서 :: 2021-11-11 
function main_product_resort($_uid){
	// 정렬그룹의 유효성 검사
	$arr_idx = array();
	$arr_gidx = array();
	$arr_sort_group = array();
	$res = _MQ_assoc(" select dmp_pcode, dmp_sort_group, dmp_sort_idx, dmp_idx from smart_display_main_product where dmp_dmsuid= '".$_uid."' order by dmp_sort_group asc , dmp_sort_idx asc , dmp_idx asc ");
	$dmpidx = 1;
	foreach($res as $k=>$v){
		$trigger_idx = true; // false 이면 업데이트

		$arr_sort_group[$v['dmp_sort_group']] = $arr_sort_group[$v['dmp_sort_group']]+1;

		if($dmpidx <> $v['dmp_idx']){
			$arr_idx[$dmpidx-$v['dmp_idx']][] = $v['dmp_pcode'];
		}
		if( ($arr_sort_group[$v['dmp_sort_group']]) <> $v['dmp_sort_idx']){
			$arr_gidx[$arr_sort_group[$v['dmp_sort_group']]-$v['dmp_sort_idx']][] = $v['dmp_pcode'];
		}

		$dmpidx++;
	}

	if(sizeof($arr_idx)>0){
		foreach($arr_idx as $k=>$v){
			_MQ_noreturn(" update smart_display_main_product set dmp_idx = (dmp_idx + '". $k ."') where dmp_pcode in ('". implode("','" , $v) ."') and dmp_dmsuid='".$_uid."' ");
		}
	}
	if(sizeof($arr_gidx)>0){
		foreach($arr_gidx as $k=>$v){
			_MQ_noreturn(" update smart_display_main_product set dmp_sort_idx = (dmp_sort_idx + '". $k ."') where dmp_pcode in ('". implode("','" , $v) ."') and dmp_dmsuid='".$_uid."' ");
		}
	}
}

// KAY :: 타입별 선택 상품 정렬순서 :: 2021-11-11 
function type_product_resort($_uid){
	// 정렬그룹의 유효성 검사
	$arr_idx = array();
	$arr_gidx = array();
	$arr_sort_group = array();
	$res = _MQ_assoc(" select dtp_pcode, dtp_sort_group, dtp_sort_idx, dtp_idx from smart_display_type_product where dtp_dtsuid= '".$_uid."' order by dtp_sort_group asc , dtp_sort_idx asc , dtp_idx asc ");
	$dtpidx = 1;
	foreach($res as $k=>$v){
		$trigger_idx = true; // false 이면 업데이트

		$arr_sort_group[$v['dtp_sort_group']] = $arr_sort_group[$v['dtp_sort_group']]+1;

		if($dtpidx <> $v['dtp_idx']){
			$arr_idx[$dtpidx-$v['dtp_idx']][] = $v['dtp_pcode'];
		}
		if( ($arr_sort_group[$v['dtp_sort_group']]) <> $v['dtp_sort_idx']){
			$arr_gidx[$arr_sort_group[$v['dtp_sort_group']]-$v['dtp_sort_idx']][] = $v['dtp_pcode'];
		}

		$dtpidx++;
	}

	if(sizeof($arr_idx)>0){
		foreach($arr_idx as $k=>$v){
			_MQ_noreturn(" update smart_display_type_product set dtp_idx = (dtp_idx + '". $k ."') where dtp_pcode in ('". implode("','" , $v) ."') and dtp_dtsuid='".$_uid."' ");
		}
	}
	if(sizeof($arr_gidx)>0){
		foreach($arr_gidx as $k=>$v){
			_MQ_noreturn(" update smart_display_type_product set dtp_sort_idx = (dtp_sort_idx + '". $k ."') where dtp_pcode in ('". implode("','" , $v) ."') and dtp_dtsuid='".$_uid."' ");
		}
	}
}
// KAY :: 카테고리 베스트상품 정렬순서 :: 2021-11-12
function cate_product_resort($_uid){
	// 정렬그룹의 유효성 검사
	$arr_idx = array();
	$arr_gidx = array();
	$arr_sort_group = array();
	$res = _MQ_assoc(" select pctb_pcode, pctb_sort_group, pctb_sort_idx, pctb_idx from smart_product_category_best where pctb_cuid= '".$_uid."' order by pctb_sort_group asc , pctb_sort_idx asc , pctb_idx asc ");
	$pctbidx = 1;
	foreach($res as $k=>$v){
		$trigger_idx = true; // false 이면 업데이트

		$arr_sort_group[$v['pctb_sort_group']] = $arr_sort_group[$v['pctb_sort_group']]+1;

		if($pctbidx <> $v['pctb_idx']){
			$arr_idx[$pctbidx-$v['pctb_idx']][] = $v['pctb_pcode'];
		}
		if( ($arr_sort_group[$v['pctb_sort_group']]) <> $v['pctb_sort_idx']){
			$arr_gidx[$arr_sort_group[$v['pctb_sort_group']]-$v['pctb_sort_idx']][] = $v['pctb_pcode'];
		}

		$pctbidx++;
	}

	if(sizeof($arr_idx)>0){
		foreach($arr_idx as $k=>$v){
			_MQ_noreturn(" update smart_product_category_best set pctb_idx = (pctb_idx + '". $k ."') where pctb_pcode in ('". implode("','" , $v) ."') and pctb_cuid='".$_uid."' ");
		}
	}
	if(sizeof($arr_gidx)>0){
		foreach($arr_gidx as $k=>$v){
			_MQ_noreturn(" update smart_product_category_best set pctb_sort_idx = (pctb_sort_idx + '". $k ."') where pctb_pcode in ('". implode("','" , $v) ."') and pctb_cuid='".$_uid."' ");
		}
	}
}

// KAY :: 기획전 상품 정렬순서 :: 2021-11-12
function promotion_product_resort($_uid){
	// 정렬그룹의 유효성 검사
	$arr_idx = array();
	$arr_gidx = array();
	$arr_sort_group = array();
	$res = _MQ_assoc(" select ppps_pcode, ppps_sort_group, ppps_sort_idx, ppps_idx from smart_promotion_plan_product_setup where ppps_ppuid= '".$_uid."' order by ppps_sort_group asc , ppps_sort_idx asc , ppps_idx asc ");
	$pppsidx = 1;
	foreach($res as $k=>$v){
		$trigger_idx = true; // false 이면 업데이트

		$arr_sort_group[$v['ppps_sort_group']] = $arr_sort_group[$v['ppps_sort_group']]+1;

		if($pppsidx <> $v['ppps_idx']){
			$arr_idx[$pppsidx-$v['ppps_idx']][] = $v['ppps_pcode'];
		}
		if( ($arr_sort_group[$v['ppps_sort_group']]) <> $v['ppps_sort_idx']){
			$arr_gidx[$arr_sort_group[$v['ppps_sort_group']]-$v['ppps_sort_idx']][] = $v['ppps_pcode'];
		}

		$pppsidx++;
	}

	if(sizeof($arr_idx)>0){
		foreach($arr_idx as $k=>$v){
			_MQ_noreturn(" update smart_promotion_plan_product_setup set ppps_idx = (ppps_idx + '". $k ."') where ppps_pcode in ('". implode("','" , $v) ."') and ppps_ppuid='".$_uid."' ");
		}
	}
	if(sizeof($arr_gidx)>0){
		foreach($arr_gidx as $k=>$v){
			_MQ_noreturn(" update smart_promotion_plan_product_setup set ppps_sort_idx = (ppps_sort_idx + '". $k ."') where ppps_pcode in ('". implode("','" , $v) ."') and ppps_ppuid='".$_uid."' ");
		}
	}
}


// SSJ : 2017-11-15 이용약관등 정책정보 추출 -- 기본은 사용으로 설정된 항목만 보여줌
function arr_policy($_use='Y',$_name = ''){
	global $AdminPath;

	$arr_policy = array();

	// 정책정보 구분 -- 기본셋팅
	$arrType = array(
		'agree' => array('name'=>'이용약관(텍스트)', 'type'=>'S')
		,'agree_html' => array('name'=>'이용약관(PC)', 'type'=>'S')
		,'agree_html_m' => array('name'=>'이용약관(MOBILE)', 'type'=>'S')
		,'privacy' => array('name'=>'개인정보처리방침', 'type'=>'S')
		,'privacy_html' => array('name'=>'개인정보처리방침(PC)', 'type'=>'S')
		,'privacy_html_m' => array('name'=>'개인정보처리방침(MOBILE)', 'type'=>'S')
		,'join_privacy' => array('name'=>'[필수] 개인정보수집 및 이용 동의(회원가입)', 'type'=>'S')
		,'join_optional' => array('name'=>'[선택] 개인정보수집 및 이용 동의(회원가입)', 'type'=>'M')
		,'join_csinfo' => array('name'=>'[선택] 개인정보 처리ㆍ위탁 동의(회원가입)', 'type'=>'M')
		,'join_thirdinfo' => array('name'=>'[선택] 개인정보 제3자 제공 동의(회원가입)', 'type'=>'M')
		,'guest_order' => array('name'=>'[필수] 개인정보수집 및 이용 동의(비회원 주문)', 'type'=>'S')
		,'guest_board' => array('name'=>'[필수] 개인정보수집 및 이용 동의(비회원 글쓰기)', 'type'=>'S')
		,'partner_agree' => array('name'=>'[필수] 개인정보수집 및 이용 동의(광고/제휴문의)', 'type'=>'S')
		,'sendmail_agree' => array('name'=>'[필수] 개인정보수집 및 이용 동의(상품메일)', 'type'=>'S')
		,'subscription_agree' => array('name'=>'[필수] 개인정보수집 및 이용 동의(구독하기)', 'type'=>'S')

		// [LCY] 2020-03-04 무단수집
		,'deny_html' => array('name'=>'이메일무단수집거부(PC)', 'type'=>'S')
		,'deny_html_m' => array('name'=>'이메일무단수집거부(MOBILE)', 'type'=>'S')

	);

	$sque = "";
	if($_use == 'Y'){
		// 특정항목만 추출
		$sque = " and po_use = 'Y' ";
	}

	if($_name != ''){
		// 특정항목만 추출
		$sque .= " and po_name = '".$_name."' ";
	}

	$res = _MQ_assoc("select * from smart_policy where 1 {$sque} order by po_name , po_uid asc ");
	foreach($res as $k=>$v){
		if($AdminPath == '') $v['po_content'] = ConfigReplace($v['po_content']); // 2019-07-24 SSJ :: 사용자 페이지에서 호출 시 치환자 적용
		if(empty($arr_policy[$v['po_name']])){
			$arr_policy[$v['po_name']]['name'] = $arrType[$v['po_name']]['name'];
			$arr_policy[$v['po_name']]['type'] = $arrType[$v['po_name']]['type'];
			$arr_policy[$v['po_name']]['po_use'] = $v['po_use']?$v['po_use']:'Y';
			if($arrType[$v['po_name']]['type']=='S'){
				$arr_policy[$v['po_name']]['po_uid'] = $v['po_uid'];
				$arr_policy[$v['po_name']]['po_title'] = $v['po_title']?$v['po_title']:$arrType[$v['po_name']]['name'];
				$arr_policy[$v['po_name']]['po_content'] = $v['po_content'];
			}
		}
		if($arrType[$v['po_name']]['type']=='M'){
			$arr_policy[$v['po_name']]['data'][] = array('po_uid'=>$v['po_uid'], 'po_title'=>$v['po_title'], 'po_content'=>$v['po_content']);
		}
	}
	return $arr_policy;
}

/**
	-- LCY :: 2017-11-17 -- 계급별 정보 배열화
	-- @info => 계급별 정보를 배열로 불러온다.
	-- @info => $_uid 가 있을경우 해당 정보만 가져온다.
**/
function getGroupInfo($_uid=false)
{

	if($_uid != false){ $squery = " and mgs_uid = '".$_uid."'  "; }
	$res = _MQ_assoc("select *from smart_member_group_set where 1 ".$squery."  order by mgs_idx asc, mgs_rank asc ");
	$arrGroupInfo = array();
	if(count($res) > 0 ){
		foreach($res as $k=>$v){
			$arrGroupInfo[$v['mgs_uid']]['uid'] = $v['mgs_uid'];
			$arrGroupInfo[$v['mgs_uid']]['rank'] = $v['mgs_rank'];
			$arrGroupInfo[$v['mgs_uid']]['name'] = $v['mgs_name'];

			// {{{회원등급추가}}}
			$arrGroupInfo[$v['mgs_uid']]['idx'] = $v['mgs_idx'];
			$arrGroupInfo[$v['mgs_uid']]['icon'] = $v['mgs_icon'];
			$arrGroupInfo[$v['mgs_uid']]['mobile_icon'] = $v['mgs_mobile_icon'];
			// {{{회원등급추가}}}

			$arrGroupInfo[$v['mgs_uid']]['condition_totprice'] = $v['mgs_condition_totprice'];
			$arrGroupInfo[$v['mgs_uid']]['condition_totcnt'] = $v['mgs_condition_totcnt'];
			$arrGroupInfo[$v['mgs_uid']]['give_point_per'] = $v['mgs_give_point_per'];
			$arrGroupInfo[$v['mgs_uid']]['sale_price_per'] = $v['mgs_sale_price_per'];
		}
	}

	return $arrGroupInfo;
}



	// SSL 사용안함 강제처리
	//			- SSL 사용상태에서 기간만료등으로 사용할 수 없을 경우에 대한 강제처리
	function ssl_forced_reset(){
		$que = " update smart_setup set s_ssl_check = 'N', s_ssl_admin_loc = 'N', s_ssl_pc_loc = 'N', s_ssl_m_loc = 'N', s_ssl_status = '대기' where s_uid = '1' ";
		_MQ_noreturn($que);
	}



# "관련상품 지정"에 지정된 옵션에 따라 상품리스트를 추출한다 2017-12-20 SSJ
function ProductRelation($pcode, $limit=10) {
    global $DeveSaleType;

    if(is_array($pcode)){
        $p_info = $pcode;
    }else{
        $p_info = _MQ(" select * from smart_product where (1) and p_code = '{$pcode}' ");
    }
    $type = $p_info['p_relation_type'];
    $p_list = array();

    // 동일카테고리
    if($type == 'category') {

        // 상품의 카테고리 추출
        $Pcate = _MQ_assoc(" select pc.pct_cuid, c.c_depth from smart_product_category as pc inner join smart_category as c on (pc.pct_cuid=c.c_uid) where pc.pct_pcode = '".$p_info['p_code']."' and c.c_view = 'Y' ");
        $arr_pcate = array();
        foreach($Pcate as $k=>$v){
            $arr_pcate[] = $v['pct_cuid'];

            // 해당 카테고리의 하위 카테고리 포함
            if($v['c_depth']<3){
                $sub_cate = _MQ_assoc(" select c_uid from smart_category as c where c.c_view = 'Y' and find_in_set('". $v['pct_cuid'] ."', c.c_parent) ");
                if(count($sub_cate) > 0){
                    foreach($sub_cate as $sk=>$sv){ $arr_pcate[] = $sv['c_uid']; }
                }
            }
        }

        // 해당카테고리가 등록된 상품 추출
        /* SSJ : 상품 품절 체크 - p_soldout_chk 정보 업데이트 : 2019-02-11 */
        $que = "
            select
                p.*, pc.pct_cuid as cuid
            from smart_product_category as pc
            left join smart_product as p on (pc.pct_pcode=p.p_code)
            where 1
                and p.p_view = 'Y'
                and p.p_option_valid_chk = 'Y'
                and p.p_stock > 0
                and p_soldout_chk = 'N'
                and p.p_code != '".$p_info['p_code'] ."'
                and pc.pct_cuid in ('". implode("','", $arr_pcate) ."')
            group by p.p_code
            order by p.p_idx asc
        ";
        $p_list = _MQ_assoc($que);

    }
    // 수동지정
    else if($type == 'manual'){

        $relation = str_replace("|",",",$p_info['p_relation']);
        /* SSJ : 상품 품절 체크 - p_soldout_chk 정보 업데이트 : 2019-02-11 */
        $que = "
            select
                *,(select pct_cuid from smart_product_category where pct_pcode = p_code order by pct_uid asc limit 1) as cuid
            from smart_product as p
            where 1
                and p.p_view = 'Y'
                and p.p_option_valid_chk = 'Y'
                and p.p_stock > 0
                and p_soldout_chk = 'N'
                and p.p_code != '".$p_info['p_code'] ."'
                and find_in_set(p_code,'".$relation."') > 0
            order by p.p_idx asc
        ";
        $limit = 0;
        $p_list = _MQ_assoc($que);
    }

    // 미사용
    else{
        $p_list = array();
    }

    if(count($p_list) > 0) {
        //shuffle($p_list); // 배열셔플
        if($limit > 0){
            array_splice($p_list, $limit); // limit값만큼만 추출
        }
    }

    return $p_list;
}



# 장바구니 판매불가 상품 삭제 2018-01-04 LDD
function clean_cart() {
	// 변경된 상품 정보 조회(상품삭제, 노출여부, 상품재고, 상품가격, 상품공급가, 옵션재고, 옵션가격, 옵션공급가, 추가옵션재고, 추가옵션가격, 추가옵션공급가)
	$qur = "
		select
			c.c_pcode,
			c.c_pouid,
			c.c_is_addoption
		from
			smart_cart as c left join
			smart_product as p on(c.c_pcode = p.p_code)
		where (1) and
			c.c_cookie = '{$_COOKIE['AuthShopCOOKIEID']}' and
			(
				p.p_code is null or
				p.p_view != 'Y' or
				p.p_option_valid_chk != 'Y' or

				if(
					c.c_pouid = 0,
					(if(c.c_old_price = p.p_price and c.c_supply_price = if(p_commission_type = '공급가', p.p_sPrice, (p.p_price-round(p.p_price*p.p_sPersent/100))) and c.c_cnt <= p.p_stock, 'Y', 'N')),
					(
						if(
							c.c_is_addoption != 'Y',

							if(
								(select concat(po_pcode, po_uid, po_poptionprice) from smart_product_option where po_uid = c.c_pouid and po_cnt >= c.c_cnt and po_view = 'Y' and po_pcode = c.c_pcode) =
								concat(c.c_pcode, c.c_pouid, c.c_old_price),

								'Y', 'N'
							),
							if(
								(select concat(pao_pcode, pao_uid, pao_poptionprice) from smart_product_addoption where pao_uid = c.c_pouid and pao_cnt >= c.c_cnt and pao_view = 'Y' and pao_pcode = c.c_pcode ) =
								concat(c.c_pcode, c.c_pouid, c.c_old_price),

								'Y', 'N'
							)
						)
					)
				) = 'N'
			)
	";
	$cr = _MQ_assoc($qur);

	// 변경된 상품 삭제 및 경고창 출력
	if(count($cr) > 0) {
		foreach($cr as $k=>$v) {
			//_MQ_noreturn(" delete from smart_cart where c_pcode = '{$v['c_pcode']}' and if(c_is_addoption = 'N', c_pouid, c_addoption_parent) = '{$v['c_pouid']}' and c_cookie = '{$_COOKIE['AuthShopCOOKIEID']}' ");
			if($v['c_is_addoption'] == 'Y') _MQ_noreturn(" delete from smart_cart where c_pcode = '{$v['c_pcode']}' and c_pouid = '{$v['c_pouid']}' and c_cookie = '{$_COOKIE['AuthShopCOOKIEID']}' ");
			else _MQ_noreturn(" delete from smart_cart where c_pcode = '{$v['c_pcode']}' and (c_pouid = '{$v['c_pouid']}' or c_addoption_parent = '{$v['c_pouid']}') and c_cookie = '{$_COOKIE['AuthShopCOOKIEID']}' ");
		}
		error_loc_msg('/?pn=shop.cart.list', '판매정보가 변경된 상품이 포함되어 삭제하였습니다.');
	}
}



/*
	LCY :: 2018-01-18 -- 게시판 권한체크 통합함수 --
	@ $_uid : 게시판 아이디
	@ $_type : 권한타입, list, view, write,reply,comment,editor
	@ return : true or false
*/
function boardAuthChk($_uid,$_type)
{
	global $mem_info;
	$return = false;
	if( in_array($_type,array('list', 'view', 'write','reply','comment','editor')) == false){ return array('code'=>'9999','msg'=>'접근권한이 없습니다.'); }
	$row = _MQ("select *from smart_bbs_info where bi_uid = '".$_uid."' ");
	if( count($row) < 1){  return array('code'=>'9998','msg'=>'게시판 정보가 없습니다.'); }

	// -- 사용유무가 있는 권한은 먼저 처리
	if(in_array($_type , array('reply','comment'))){
		if( $row['bi_'.$_type.'_use'] != 'Y'){ return array('code'=>'9997','msg'=>'본 게시판에서 해당 기능은 사용할 수 없습니다.');}
	}

	// -- 사용유무가 없을 시 관리자는 무조건 권한획득
	if( is_admin() === true){ return true; }

	$auth_value = $row['bi_auth_'.$_type] == '' ? 0 : $row['bi_auth_'.$_type];
	switch($auth_value){
		case "9": // 관리자라면, 사용자,통합관리자 의 권한을 체크
			if( is_admin() !== true){ return array('code'=>'9996','msg'=>'관리자 이용 가능한 기능입니다.'); }
			else {return true; }
			break;
		case "2": // 회원이라면 그룹정보를 확인
			if( is_login() !== true){ return array('code'=>'9995','msg'=>'회원만 이용가능합니다. 로그인 후 이용해 주세요.'); }
			if( rm_str(trim($row['bi_auth_'.$_type.'_group'])) == ''){ return array('code'=>'9994','msg'=>'본 권한이 없습니다.'); }
			if( in_array($mem_info['in_mgsuid'], explode(',',$row['bi_auth_'.$_type.'_group']) ) == false){ return array('code'=>'9993','msg'=>'권한이 없습니다.'); }
			else return true;
			break;
		default : return true;
		break;
	}

	return $return;
}

// -- 게시판의 전체권한을 배열로 RETURN
function boardAuthChkAll($_uid)
{
	$arrAuthType = array('list', 'view', 'write','reply','comment','editor');
	$arrAuth = array();
	foreach($arrAuthType as $v){
		$arrAuth[$v] = boardAuthChk($_uid,$v);
	}
	return $arrAuth;
}

/*
	// -- LCY 2018-02-01 :: 비밀글에 대한 체크  --
*/
function postSecretChk($_uid)
{
	global $siteInfo;
	$r = _MQ("select * from smart_bbs where b_uid = '".$_uid."' ");
	if( count($r) < 1){ return false; }
	if( $r['b_secret'] != 'Y'){ return true; } // 비밀글이 아닐경우
	if( is_admin() === true){ return true; } // 관리자는 무조건 권한이 있다

	if( $r['b_writer_type'] == 'member'){
		if( $r['b_inid'] == get_userid() ) { return true; } // 본인글이라면 통과
		else{
			// -- 2뎁스의 비밀글일경우 1뎁스를 판별한다.:: 부모글을 남겼을 경우 답글이 비밀글이라 하더라도 볼수 있어야한다.
			if( $r['b_depth'] > 1 && $r['b_relation'] > 0 ) { return postSecretChk($r['b_relation']); }
		}
	}else if($r['b_writer_type'] == 'guest'){
		$authCode = onedaynet_encode($siteInfo['s_license'].$_uid);
		if( $_SESSION['authPostItem'][$_uid] == $authCode ){ return true; }
		else{
			// -- 2뎁스의 비밀글일경우 1뎁스를 판별한다.:: 부모글을 남겼을 경우 답글이 비밀글이라 하더라도 볼수 있어야한다.
			if( $r['b_depth'] > 1 && $r['b_relation'] > 0 ) { return postSecretChk($r['b_relation']); }
		}
	}
	return false;
}


// LCY -- 특정 파일의 고유값의 파일정보를  return --
function getFilesRow($_uid){
	$rowFile = _MQ_assoc("select *from smart_files where f_uid = '".$_uid."' ");
	return $rowFile;
}

// LCY -- 특정 테이블 고유값에 속한 모든 파일정보를 return --
function getFilesRes($table,$tableUid)
{
	$resFile = _MQ_assoc("select *from smart_files where f_table = '".$table."' and f_table_uid = '".$tableUid."' ");
	return $resFile;
}

// LCY -- 특정 테이블 고유값에 속한 파일 개수를 호출 :: 파일개수만 판별할 시 사용 --
function getFilesCount($table,$tableUid)
{
	$cnt = _MQ_result("select count(*) as cnt from smart_files where f_table = '".$table."' and f_table_uid = '".$tableUid."' ");
	return $cnt;
}

/*
	-- LCY 2018-01-25 :: 파일사제함수
	- $filename : 삭제할 파일명
	- $_uid : 고유번호가 있을경우 db 도 삭제
	- 기본 파일 경로는 고정
*/
function deleteFiles($filename,$_uid=false)
{
	$appDir =  $_SERVER['DOCUMENT_ROOT'].IMG_DIR_FILE.$filename;
	if( is_file($appDir) == true){ @unlink($appDir); }

	// -- $_uid 가 있다면 db도 함께 삭제
	if( $_uid != false){ _MQ_noreturn("delete from smart_files where f_uid = '".$_uid."' "); }

	return true;
}

// -- 쇼핑몰 운영자 계정정보 --
function shopAdminInfo()
{
	$row = _MQ("select *from smart_individual where in_userlevel = '9' ");
	return $row;
}



// 사용자 정보를 보여준다
// 관리자 전용 함수이며, 회원정보 수정 페이지로 넘겨 처리한다.
// 인지	: 회원아이디
//			: 링크연결
// 회원정보가 없으면 $str 만 출력한다. (기업회원이나, 가상회원, 삭제된 회원등)
//			- $mem_arr => 회원정보
function showUserInfo($id = false , $str='', $ind_arr = array()) {

	// $str 값이 없으면 아이디를 대신 출력한다.
	if(!$str) $str = $id;
	if(!$str) $str = '알수없음';
	if( $id == false){ return $str.' <span class="t_orange">(비회원)</span>'; } // 아이디가 없다면 무조건 비회원처리

	// 아이디는 있는데 회원정보가 없으면 아이디를 조회
	if(sizeof($ind_arr) <= 0) {
		$r = _MQ("select in_name from smart_individual where in_id = '".$id."'");
		if(sizeof($r) < 1 ) return $str.' <span class="t_orange">(비회원)</span>';
		else $ind_arr = $r;
	}

	// 2018-10-11 SSJ :: 회원명 추가
	if(sizeof($ind_arr) > 0 && $str == $id && $ind_arr['in_name'] <> '') $str = $ind_arr['in_name'];

	// -- 회원정보가 존재한다면
	return	"<a href='_individual.form.php?_mode=modify&_id=".$id."' target='_blank'>".$str." <span class='t_light'>(".$id.")</span></a>";
}


// 입점업체 정보를 보여준다
// 관리자 전용 함수이며, 입점업체 수정 페이지로 넘겨 처리한다.
// 인지	: 입점업체아이디
//			: 링크연결
// 입점업체정보가 없으면 $str 만 출력한다. (기업회원이나, 가상회원, 삭제된 회원등)
//			- $mem_arr => 입접업체정보
function showCompanyInfo($id = false , $str='', $ind_arr = array()) {

	// $str 값이 없으면 아이디를 대신 출력한다.
	if(!$str) $str = $id;
	if(!$str) $str = '알수없음';

	// 아이디는 있는데 회원정보가 없으면 아이디를 조회
	if(count($ind_arr) <= 0) {
		$r = _MQ("select cp_name from smart_company where cp_id = '{$id}'");
		if(!$r['cp_name']) return $str.' <span class="t_orange">(삭제됨)</span>';
		else return '<a href="_entershop.form.php?_mode=modify&_id='.$id.'" target="_blank">'.$r['cp_name'].' <span class="t_light block">('.$id.')</span></a>';
	}

	// -- 입점업체정보가 존재한다면
	return	"<a href='_entershop.form.php?_mode=modify&_id=".$id."' target='_blank'>".$str." <span class='t_light'>(".$id.")</span></a>";
}


// - 쇼핑몰 주문번호 생성 ---
function shop_ordernum_create($type=null){
	// --> 주문번호 - 숫자조합으로 15글자 적용, 예)
	// --> 생성원리 1. 5개씩 3단락
	//	--> 생성예. 12345-23456-34567

	$ex = explode(' ', microtime());
	$tmp1 = sprintf("%05d" , rand(0,99999));
	$tmp2 = sprintf("%u" , crc32( microtime(). rand(1,99999) ));
	$tmp2 = str_pad( $tmp2 , 10 , '0', STR_PAD_RIGHT);
	$order_a = sprintf("%05d" , substr($tmp2 , 0 , 5));
	$order_b = substr($tmp2 , -5);
	$_code = $tmp1 ."-" . $order_a ."-" . $order_b;

	// - 과거 같은 주문 번호 여부 확인 ---
	$orderchk = _MQ("select count(*) as cnt from smart_order where 	o_ordernum = '".  strtoupper($_code) ."'");
	if( $orderchk[cnt] > 0 ){
		$_code = shop_ordernum_create();
	}

	return $_code ;
}
// - 상품코드 생성 ---

// 각 PG사별 전표를 출력한다
function link_credit_receipt($ordernum, $text='[전표보기]') {

	GLOBAL $_SERVER, $siteInfo;

	$tmp = _MQ("select * from smart_order_cardlog where oc_oordernum='".$ordernum."' order by oc_uid desc limit 1");
	$arr_occontent = array(); $ex = explode("§§" , $tmp[oc_content]);
	foreach( $ex as $sk=>$sv ){ $ex2 = explode("||" , $sv); $arr_occontent[$ex2[0]] = $ex2[1]; }
	$ordr = _MQ("select * from smart_order where o_ordernum='".$ordernum."'");

	switch($siteInfo[s_pg_type]) {

		case "billgate":
			return "<a href='#none' onclick=\"window.open('https://cpadmin.billgate.net/billgate/common/authCardReceipt.jsp?mid=".$siteInfo[s_pg_code]."&transNm=".$tmp[oc_tid]."&currTp=0000','C_receipt','width=400, height=750'); return false;\"><b>".$text."</b></a>";
		break;

		case "kcp":
			return "
			<a href='#none' onclick=\"showReceipt( '".$tmp[oc_tid]."', '".$ordernum."', '".$ordr[o_price_real]."' );return false;\"><b>".$text."</b></a>
			<script>
				function showReceipt(tid,ordernum,amount) {
					popupWin =  window.open( 'https://admin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=card_bill&tno='+tid+'&order_no='+ordernum+'&trade_mony='+amount, 'popWinName','menubar=1,toolbar=0,width=470,height=815,resize=1,left=10,top=10' );
				}
			</script>";
		break;

		case "inicis":
			return "
			<a href='#none' onclick=\"showReceipt('".$tmp[oc_tid]."');return false;\"><b>".$text."</b></a>
			<script>
				function showReceipt(noTid) {
					popupWin =  window.open( 'https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/mCmReceipt_head.jsp?noTid=' + noTid + '&noMethod=1', 'popWinName','menubar=1,toolbar=0,width=450,height=667,resize=1,left=252,top=116' );
				}
			</script>";
		break;

		case "lgpay":
			//$CST_PLATFORM = $siteInfo[s_pg_mode];
			//$CST_MID = $siteInfo[s_pg_code];
			//$LGD_MID = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
			//$LGD_MERTKEY = $siteInfo[s_pg_key];
			//$LGD_TID = $tmp[oc_tid];
			//$authdata = md5($LGD_MID.$LGD_TID.$LGD_MERTKEY);
			//return "<script language='JavaScript' src='//pgweb.uplus.co.kr".($CST_PLATFORM=='test'?':7085':'')."/WEB_SERVER/js/receipt_link.js'></script>
			//<a onclick=\"showReceiptByTID('".$LGD_MID."', '".$LGD_TID."', '".$authdata."');return false;\" href='#none'><b>".$text."</b></a>";
			// SSJ : 토스페이먼츠 PG 모듈 교체 : 2021-02-22 : 카드결제 영수증과 현금영수증만 출력 가능
			$receiptUrl = '';
			if($ordr['o_paymethod'] == 'card'){
				$ex = explode("§§", $tmp['oc_content']);
				foreach($ex as $ek=>$ev){
					$eex = explode("||", $ev);
					if($eex[0] == 'receiptUrl' && $eex[1] <> ''){ $receiptUrl = $eex[1]; }
				}
			}else if($ordr['o_paymethod'] == 'virtual' && $ordr['o_paystatus'] == 'Y'){
				$ex = explode("§§", $tmp['oc_content']);
				foreach($ex as $ek=>$ev){
					$eex = explode("||", $ev);
					if($eex[0] == 'cashReceipt' && $eex[1] <> ''){ $receiptUrl = $eex[1]; }
					$text = str_replace('[영수증출력]', '[현금영수증출력]', $text);
				}
			}
			if($receiptUrl <> ''){
				return "
				<a href='#none' onclick=\"showReceipt('".$receiptUrl."');return false;\"><b>".$text."</b></a>
				<script>
					function showReceipt(noTid) {
						popupWin =  window.open( '".$receiptUrl."', 'popWinName','menubar=1,toolbar=0,width=450,height=667,resize=1,left=252,top=116' );
					}
				</script>";
			}
		break;

		case "allthegate":
			return "<a href='#none' onclick=\"receipt('".$tmp[oc_tid]."','".$siteInfo[s_pg_code]."','".substr($arr_occontent[rApprTm],0,8)."','".$arr_occontent[rDealNo]."');return false;\"><b>".$text."</b></a>
			<script>
				function receipt(adm_no, service_id, send_dt, send_no){
					url='http://www.allthegate.com/receipt/receipt.jsp'
					url=url+'?adm_no='+adm_no;
					url=url+'&service_id='+service_id;
					url=url+'&send_dt='+send_dt;
					url=url+'&send_no='+send_no;
					url=url+'&path=home';
					window.open(url, 'window','toolbar=no,location=no,directories=no,status=,menubar=no,scrollbars=no,resizable=no,width=423,height=668,top=0,left=150');
				}
			</script>";
		break;

		case "daupay":
			return "<a href='#none' onclick=\"popup_receipt(); return false;\"><b>".$text."</b></a>
						<form id='frm_receipt' name='frm_receipt' action='https://agent.daoupay.com/common/PayInfoPrintDirectCard.jsp' method='post' target='C_receipt'>
						<input type='hidden' name='DAOUTRX' value='".$tmp[oc_tid]."'>
						</form>
						<script>
						function popup_receipt(){
							frm = document.getElementById('frm_receipt');
							window.open('','C_receipt','width=400, height=750');
							frm.submit();
						}
						</script>";
		break;

	}

 }



// 2017-08-21 mysql passwrod형으로 변경
if(!function_exists('db_password')) {
	function db_password($value='') {
		$row = _MQ(" select password('$value') as pass ");
		return $row['pass'];
	}
}


/*
	-- LCY 2018-01-25 :: 파일업로드 함수 --
	- 멀티체크도 하기때문에 $_filesDel, $_filesOld 변수명 규칙을 사용하여 처리해야한다.
	-	filesDel, filesOld 의 경우 이미 파일이 존재하는경우만 사용하므로 key 값과 매칭하여 처리
*/
function odtFileUpload($key,$table,$tableUid)
{
	$fileOLD = $key . "_OLD" ; // OLD 파일명
	$fileDEL = $key . "_DEL" ; // 파일 삭제 여부
	global $_FILES , $$fileOLD ,  $$fileDEL , $arrUpfileConfig ;
	$arrFilename = $_files = $_filesOld = $_filesDel = $errorMsg = array();

	$uploadDir = $_SERVER['DOCUMENT_ROOT'].IMG_DIR_FILE;
	if( is_dir($uploadDir) !== true){ return false; } // 디렉토리가 존재하지 않을 시

	if( count($_FILES[$key]) < 1){ return false; }
	if( in_array('',array($uploadDir,$table,$tableUid)) == true){ return false; }

	// -- 업로드 형태가 멀티인지,싱글인지 구분
	if( is_array($_FILES[$key]['name']) == false && count($_FILES[$key]['name']) < 1){ // 싱글이라면 배열강제로 생성
		$_files['name'][] = $_FILES[$key]['name'];
		$_files['type'][] = $_FILES[$key]['type'];
		$_files['tmp_name'][] = $_FILES[$key]['tmp_name'];
		$_files['error'][] = $_FILES[$key]['error'];
		$_files['size'][] = $_FILES[$key]['size'];

		if( isset($$fileOLD) ) { $_filesOld[] = $$fileOLD;} // -- 이전파일이 있다면
		if( isset($$fileDEL) ) { $_filesDel[] = $$fileDEL;} // -- 삭제된 파일이 있다면
	}else{
		$_files = $_FILES[$key];
		$_filesOld = $$fileOLD;
		$_filesDel = $$fileDEL;
		if( is_array($$fileOLD) ) { $_filesOld = $$fileOLD;} // -- 이전파일이 있다면
		if( is_array($$fileDEL) ) { $_filesDel = $$fileDEL;} // -- 삭제된 파일이 있다면
	}

	// -- 파일삭제가 있을경우
	if( count($_filesDel) > 0){
		foreach($_filesDel as $fuid){
			$rowChk = _MQ("select *from smart_files where f_uid = '".$fuid."' ");
			if( count($rowChk) > 0){
				$fileLoc = $uploadDir.$rowChk['f_realname'];
				if( is_file($fileLoc) == true){ @unlink( $fileLoc ); }
				_MQ_noreturn("delete from smart_files where f_uid = '".$fuid."' ");
			}
		}
	}
	if( count($_files) > 0){
		foreach($_files['name'] as $k=>$v){

			// -- 에러처리
			if($_files['error'][$k] > 0 && $_files['tmp_name'][$k] != ''){

				switch($_files["error"][$k]){
					case "1":$errorMsg[] = '서버내 업로드 설정값 제한'; break;//업로드한 파일 크기가 PHP 'upload_max_filesize' 설정값보다 클 경우
					case "2":$errorMsg[] = '업로드 용량초과'; break;// UPLOAD_ERR_FORM_SIZE, 업로드한 파일 크기가 HTML 폼에 명시한 MAX_FILE_SIZE 값보다 클 경우
					case "3":$errorMsg[] = '파일정보 누락'; break;//파일중 일부만 전송된 경우
				}
				
				// LCY : 2022-08-30 : 파일업로드 보완 패치 -- continue;
				continue;
			}
			// -- var.php 에 설정된 용량 체크 // LCY : 2022-08-30 : 파일업로드 보완 패치 -- continue;
			if( $_files["size"][$k] >  $arrUpfileConfig['size'] ){  $errorMsg[] = '전역설정 용량 초과'; continue;  }  
			if($_files["size"][$k] > 0){
				$app_ext = strtolower(end(explode('.',$v))); // 확장자  // LCY : 2022-08-30 : 파일업로드 보완 패치 -- array_pop => end 로 변경

				// LCY : 2022-08-30 : 파일업로드 보완 패치 -- continue;
				if( !preg_match("/".implode("|",$arrUpfileConfig['ext']['file'])."/i" , $app_ext) ) { $errorMsg[] = '등록불가 확장자'; continue; }
				$file_name = sprintf("%u" , crc32( $v . $k . time())) .".". $app_ext; // JJC : 파일명 중복 방지 : 2021-12-01
				$result['upload'] = copy( $_files["tmp_name"][$k] ,$uploadDir . $file_name);

				$arrFilename[$k]['real'] = $file_name; // 실제 파일이름
				$arrFilename[$k]['old'] = $v; // 노출될 파일이름
				$arrFilename[$k]['size'] = $_files["size"][$k]; // 파일사이즈		// 바이트 ?

				// -- 이전파일이 있을경우 삭제후 처리
				if( is_array($_filesOld) == true && in_array($k, array_values($_filesOld) ) == true){
					$rowChk = _MQ("select *from smart_files where f_uid = '".$k."' ");
					if( count($rowChk) > 0){
						$fileLoc = $uploadDir.$rowChk['f_realname'];
						if( is_file($fileLoc) == true){ @unlink( $fileLoc ); } // 기존파일 삭제
						_MQ_noreturn("update smart_files set  f_oldname = '".$arrFilename[$k]['old']."', f_realname = '".$arrFilename[$k]['real']."', f_size = '".$arrFilename[$k]['size']."'  ,f_mdate = now() where f_uid = '".$k."'     ");
					}
				}else{   // $key,$uploadDir,$table,$tableUid
					_MQ_noreturn("insert smart_files set f_table = '".$table."', f_table_uid = '".$tableUid."', f_oldname = '".$arrFilename[$k]['old']."', f_realname = '".$arrFilename[$k]['real']."', f_size = '".$arrFilename[$k]['size']."' ,f_mdate = now() ,  f_rdate= now()  ");
				}
			} // 업로드할 파일이 있을경우

		} // -- end foreach
	}
	if( count($errorMsg) < 1 ){ return true; }
	else{ return $errorMsg; }
}


/*
	-- LCY 2018-02-12
	-- 스킨변경에 따른 기본값 업데이트
	-- 함수 실행시점 => 스킨변경 시
*/
function updateSkinPro()
{
	$updateSkinInfo = SkinInfo(); // 스킨정보를 호출

	// -- 카테고리에 상품 진열 :: 기본값으로 초기화 5,4,2,1 * 3, 2
	_MQ_noreturn("update smart_category set c_list_product_display = '".$updateSkinInfo['category']['pc_list_depth_default']."', c_list_product_mobile_display = '".$updateSkinInfo['category']['mo_list_depth_default']."' ,  c_best_product_display = '".$updateSkinInfo['category']['pc_best_depth_default']."', c_best_product_mobile_display = '".$updateSkinInfo['category']['mo_best_depth_default']."' ");

	// -- 검색상품 진열 :: 기본값으로 초기화 5,4,2,1 * 3, 2
	_MQ_noreturn("update smart_setup set s_search_display = '".$updateSkinInfo['category']['pc_list_depth_default']."' , s_search_mobile_display = '".$updateSkinInfo['category']['mo_list_depth_default']."' where s_uid = '1' ");

	// -- 타입별 진열 :: 기본값을 초기화 :: 5,4,2,1 * 3, 2
	_MQ_noreturn("update smart_display_type_set set dts_list_product_display = '".$updateSkinInfo['category']['pc_list_depth_default']."', dts_list_product_mobile_display = '".$updateSkinInfo['category']['mo_list_depth_default']."'  ");

	// -- 메인상품 진열 :: 기본값을 초기화 :: 5,4,3 * 3, 2
	_MQ_noreturn("update smart_display_main_set set dms_list_product_display = '".$updateSkinInfo['category']['pc_best_depth_default']."', dms_list_product_mobile_display = '".$updateSkinInfo['category']['mo_best_depth_default']."'  ");


	return true;
}



// 환경 설정값으로 치환 함수
/*
	입력된 문자열에서 환경설정 값의 치환자를 변경한다.
*/
function ConfigReplace($content='', $custom=array()) {
	global $siteInfo, $arr_pg_type;
	/*
		[적림급 설정::사용 최소금액]
		[적림급 설정::사용한도]
		[적림급 설정::회원가입 지급금액]
		[적림급 설정::회원가입 지급일]
		[적림급 설정::구매 적립일]
		[적림급 설정::포토후기 지급금액]
		[적림급 설정::포토후기 지급일]
		[상품/배송::택배사]
		[통합 전자결제(PG) 관리::PG사]
		[쇼핑몰 기본정보::고객센터 대표번호]
		[쇼핑몰 기본정보::대표번호]
		[쇼핑몰 기본정보::대표 이메일]
		[쇼핑몰 기본정보::이메일]
		[쇼핑몰 기본정보::사이트명]
		[쇼핑몰 기본정보::회사명]
		[쇼핑몰 기본정보::대표자명]
		[쇼핑몰 기본정보::통신판매신고번호]
		[쇼핑몰 기본정보::업태]
		[쇼핑몰 기본정보::종목]
		[쇼핑몰 기본정보::개인정보관리책임자]
		[쇼핑몰 기본정보::로그인 시도횟수]
	*/
	$content = str_replace('[적림급 설정::사용 최소금액]', number_format($siteInfo['s_pointusevalue']), $content);
	$content = str_replace('[적림급 설정::사용한도]', number_format($siteInfo['s_pointuselimit']), $content);
	$content = str_replace('[적림급 설정::회원가입 지급금액]', number_format($siteInfo['s_joinpoint']), $content);
	$content = str_replace('[적림급 설정::회원가입 지급일]', $siteInfo['s_joinpointprodate'], $content);
	$content = str_replace('[적림급 설정::구매 적립일]', $siteInfo['s_orderpointprodate'], $content);
	$content = str_replace('[적림급 설정::포토후기 지급금액]', number_format($siteInfo['s_productevalpoint']), $content);
	$content = str_replace('[적림급 설정::포토후기 지급일]', $siteInfo['s_productevalprodate'], $content);
	$content = str_replace('[상품/배송::택배사]', $siteInfo['s_del_company'], $content);
	$content = str_replace('[통합 전자결제(PG) 관리::PG사]', $arr_pg_type[$siteInfo['s_pg_type']], $content);
	$content = str_replace('[쇼핑몰 기본정보::고객센터 대표번호]', $siteInfo['s_glbtel'], $content);
	$content = str_replace('[쇼핑몰 기본정보::대표번호]', $siteInfo['s_glbtel'], $content);
	$content = str_replace('[쇼핑몰 기본정보::대표 이메일]', $siteInfo['s_ademail'], $content);
	$content = str_replace('[쇼핑몰 기본정보::이메일]', $siteInfo['s_ademail'], $content);
	$content = str_replace('[쇼핑몰 기본정보::사이트명]', $siteInfo['s_adshop'], $content);
	$content = str_replace('[쇼핑몰 기본정보::회사명]', $siteInfo['s_company_name'], $content);
	$content = str_replace('[쇼핑몰 기본정보::대표자명]', $siteInfo['s_ceo_name'], $content);
	$content = str_replace('[쇼핑몰 기본정보::통신판매신고번호]', $siteInfo['s_company_snum'], $content);
	$content = str_replace('[쇼핑몰 기본정보::업태]', $siteInfo['s_item1'], $content);
	$content = str_replace('[쇼핑몰 기본정보::종목]', $siteInfo['s_item2'], $content);
	$content = str_replace('[쇼핑몰 기본정보::개인정보관리책임자]', $siteInfo['s_privacy_name'], $content);
	$content = str_replace('[쇼핑몰 기본정보::로그인 시도횟수]', number_format($siteInfo['member_login_cnt']), $content);

	// [LCY] 2020-03-04 -- 이메일 무단 수집 금지 -- {
	$set = date('Y-m-d H:i:s', filemtime($_SERVER['DOCUMENT_ROOT'].'/include/config_database.php'));
	$content = str_replace('[게시일::이메일무단수집거부]', $set , $content);
	// [LCY] 2020-03-04 -- 이메일 무단 수집 금지 -- }

	// 커스텀 치환자 $custom = array('{{치환자}}'=>'replace', '{{주소}}'=>'http://www.onedaynet.co.kr');
	if(count($custom) > 0) {
		foreach($custom as $k=>$v) {
			if(!$k || !$v) continue;
			$content = str_replace($k, $v, $content);
		}
	}

	return $content;
}

// SSJ : 2018-03-09 등록된 이용안내 추출
function getProductGuideInfo($cpid){

	$arr_guide_info = array();
	$arr_customer2 = arr_company2();

	$subque = $cpid ? " or g_user='". $cpid ."' " : "";
	$_guide_info = _MQ_assoc(" select * from smart_product_guide where g_user='_MASTER_' {$subque} order by (g_user='_MASTER_') desc , g_uid desc ");
	//ViewArr($_guide_info);
	if(sizeof($_guide_info) > 0){
		foreach($_guide_info as $k=>$v){
			// 기본선택 여부
			if($v['g_default']=='Y' && $v['g_user']=='_MASTER_'){
				$arr_guide_info[$v['g_type']]['default'] = $v['g_uid'];
			}
			// 최초등록시 기본설정을따른다
			if($_mode == 'add' && $v['g_default']=='Y' && $v['g_user']=='_MASTER_'){
				$row['p_guide_type_'.$v['g_type']] = 'list';
				$row['p_guide_uid_'.$v['g_type']] = $v['g_uid'];
			}

			// 타이틀/내용 저장
			$arr_guide_info[$v['g_type']]['title'][$v['g_uid']] = ($v['g_user']<>'_MASTER_'?'['.($arr_customer2[$v['g_user']]?$arr_customer2[$v['g_user']]:'확인불가').'] ':null) . $v['g_title'];
			$arr_guide_info[$v['g_type']]['content'][$v['g_uid']] = $v['g_content'];
		}
	}
	return $arr_guide_info;
}

/*
	- 회원등급에 따른 혜택 정보를 가져온다.
*/
function getGroupSetInfo($_id = false)
{
	global $mem_info;

	if( is_login() !== true){ return array(); }
	$get_memberInfo = $mem_info;
	if($_id !== false){ $get_memberInfo = _individual_info($_id); } // 회원아이디가 지정되어있다면 해당 회원아이디로 가져온다.
	if( count($get_memberInfo) < 1){ return array(); }
	$row = _MQ("select * from smart_member_group_set where mgs_uid = '".$get_memberInfo['in_mgsuid']."'  ");
	return $row;
}

/*
	- 회원등급에 따른 혜택을 계산해준다.
*/
function getGroupSetPer($price,$_type,$pcode=false)
{
	$arrResult = array();

	if( $price == ''){ return false; }
	$chkCnt = _MQ_result("select count(*) as cnt from smart_product where p_code = '".$pcode."' and p_groupset_use = 'Y' ");
	if($chkCnt < 1){ return false; }
	$groupSetInfo = getGroupSetInfo();
	if( count($groupSetInfo) < 1){ return 0;}

	if( $_type == 'point') {  // 포인트라면
		if( $groupSetInfo['mgs_give_point_per'] > 0){
			return  round($price* $groupSetInfo['mgs_give_point_per']/100);
		}else{
			return  0;
		}
	}else if($_type == 'price' ){  // 금액이라면
		if( $groupSetInfo['mgs_sale_price_per'] > 0){
			return round($price* $groupSetInfo['mgs_sale_price_per']/100);
		}else{
			return 0;
		}
	}
	return  0;
}



	// JJC : 옵션 삭제 전 옵션 이미지 삭제 :2018-03-19
	//			pouid : 삭제할 옵션 고유번호
	//						옵션 차수에 상관없이 삭제함.
	function option_img_del($pouid){
		$app_path = $_SERVER['DOCUMENT_ROOT'] . "/upfiles/option";
		$res = _MQ_assoc("select * from smart_product_option where po_color_type = 'img' and ( po_uid='". addslashes($pouid) ."' or find_in_set('". addslashes($pouid) ."',po_parent) >0 ) ");
		foreach( $res as $k=>$v ){
			if( @file_exists($app_path .'/'. $v['po_color_name']) ){
				@unlink($app_path .'/'. $v['po_color_name']);
			}
		}
	}


	// JJC : 자주 옵션 삭제 전 자주 옵션 이미지 삭제 :2018-03-19
	//			pouid : 삭제할 자주 옵션 고유번호
	//						자주 옵션 차수에 상관없이 삭제함.
	function common_option_img_del($pouid){
		$app_path = $_SERVER['DOCUMENT_ROOT'] . "/upfiles/option";
		$res = _MQ_assoc("select * from smart_common_option where co_color_type = 'img' and ( co_uid='". addslashes($pouid) ."' or find_in_set('". addslashes($pouid) ."',co_parent) >0 ) ");
		foreach( $res as $k=>$v ){
			if( @file_exists($app_path .'/'. $v['co_color_name']) ){
				@unlink($app_path .'/'. $v['co_color_name']);
			}
		}
	}


	 // 무료배송 이벤트 체크  :: $_mode order 로 고정, 추가개발 조건이 있을 수 있으나 현재는 order 만 사용 ::
	function PromotionEventDeliveryChk($_mode='order')
	{
		global $mem_info;

		// 특수 조건이 추가안되었다면 shop.order.form 에서 실행되도록 하세요. ---------------------------------------------------
		$row = getPromotionEventDelivery();
		if($row['use'] == 'N'){ return false; }
		if($row['sdate'] > date('Y-m-d') || $row['edate'] < date('Y-m-d')  ){ return false;} // 이벤트 기간이 아닐경우
		if( $row['setMember'] == 'group'){ // 회원 그룹일경우
			if(  $row['setGroupUid'] == '') { return false; } // 설정된 그룹이 없을 경우 처리
			$arrSetGroupUid = explode(",",$row['setGroupUid']);  // 설정된 그룹을 ,(콤마) 단위로 분류
			if( in_array( $mem_info['in_mgsuid'] , $arrSetGroupUid ) == false){ return false; } // 회원그룹이 속해있지 않다면
		}
		// 위에까지 무료배송 이벤트 기본조건 ---------------------------------------------------

		if( $_mode == 'view'){ return true; } // 상품상세에서는 노출조건만 따진다.

		// -- 주문시 체크한다 :: 장바구니에서 c_direct 가 Y 인것 중에서 c_price 의 합산금액을 뽑는다.
		$rowCartChk = _MQ("select sum(c_price*c_cnt) as totalSum from smart_cart where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and c_direct = 'Y'  ");
		if($row['minPrice'] > $rowCartChk['totalSum']){ return false; } // 주문금액이 무료배송 조건인 금액보다 작을 경우

		// 여기까지 무료배송 이벤트 기본조건 종료 true 값을 최종 return ---------------------------------------------------

		return true;
	}


	// 가능한 출석체크 이벤트 정보 추출
	function get_promotion_attend_event(){
		$arr = array();

		$r = _MQ(" select * from smart_promotion_attend_config where atc_use = 'Y' and (atc_limit = 'N' or atc_sdate <= CURDATE() and atc_edate >= CURDATE()) order by atc_uid desc limit 1 ");

		// 정보저장
		$arr['atc_uid'] = $r['atc_uid'];
		$arr['atc_use'] = $r['atc_use'];
		$arr['atc_title'] = $r['atc_title'];
		$arr['atc_type'] = $r['atc_type'];
		$arr['atc_duplicate'] = $r['atc_duplicate'];
		$arr['atc_limit'] = $r['atc_limit'];
		$arr['atc_img_pc'] = $r['atc_img_pc'];
		$arr['atc_img_mo'] = $r['atc_img_mo'];
		if($r['atc_limit'] == 'Y'){
			$arr['atc_sdate'] = $r['atc_sdate'];
			$arr['atc_edate'] = $r['atc_edate'];
		}else{ // 기간제한없을때는 +- 1년까지 보여준다.
			$arr['atc_sdate'] = date('Y-m-d', strtotime('-1year'));
			$arr['atc_edate'] = date('Y-m-d', strtotime('+1year'));
		}

		// 2017-01-06 연속출석조건추출 SSJ
		$e_info = _MQ_assoc(" select * from smart_promotion_attend_addinfo where ata_event = '". $r['atc_uid'] ."' order by ata_days asc ");
		if(sizeof($e_info)>0){ foreach($e_info as $key => $info){
			//$e_info_print .= ' + 연속출석 <strong>'. number_format($info[mca_days]).'</strong>일시 <strong>'. number_format($info[mca_point]) .'</strong>원을 <strong>'. number_format($info[mca_delay]) .'</strong>일 후에 적립합니다.<br>';
			$arr['info'][$key]['ata_uid'] = $info['ata_uid'];
			$arr['info'][$key]['ata_days'] = $info['ata_days'];
			$arr['info'][$key]['ata_coupon'] = $info['ata_coupon'];
			$arr['info'][$key]['ata_coupon_delay'] = $info['ata_coupon_delay'];
			$arr['info'][$key]['ata_point'] = $info['ata_point'];
			$arr['info'][$key]['ata_point_delay'] = $info['ata_point_delay'];
		} }

		return $arr;

	}

	/*LCY::COUPON
		// 쿠폰설정 아이템 발급중지 함수
		// 사용기간으로 지정된 쿠폰중 발급이 진행중이고, 사용기간이 지난쿠폰을 찾아 발급중지를 시킨다.
		// 오차피 발급시에 한번 체킹하지만 하루업데이트를 통해 처리하고자 추가하였음
	*/
	function coupon_set_update()
	{
		_MQ_noreturn(" update smart_individual_coupon_set set ocs_view = 'N' where ocs_view = 'Y' and ocs_use_date_type = 'date' and ocs_edate < CURDATE()  ");
		return true;
	}


	/*LCY::COUPON -- 쿠폰발급체크
		- return true 또는 false, 체크시 === true 일경우 또는 !== true 아닐경우
		- 다운로드형 쿠폰일경우 반드시 상품코드가 있어야한다.
		- 회원등급, 중복발급 체킹은 반드시 $mem_info 가 있어야한다.
	*/
	function couponSetIssuedChk($couponSetData,$mem_info = array(), $pcode=false)
	{

		// 발급여부 체크
		if( $couponSetData['ocs_view'] == 'N'){ return false;} // 발급중이 아니라면

		// 사용일 체크
		if($couponSetData['ocs_use_date_type'] == 'date'){ // 사용기간
			if( $couponSetData['ocs_edate'] < date('Y-m-d')){  return false;  } // 사용기간이 오늘보다 작다면
		}else{ // 사용일
			if( $couponSetData['ocs_expire'] < 1){ return false ; } // 사용일이 없다면
		}

		// 쿠폰헤택 금액체크
		if( $couponSetData['ocs_per']+$couponSetData['ocs_price'] == 0){ return false ; } // 혜택이 없다면

		// 발급수량 체킹
		if( in_array($couponSetData['ocs_issued_type'], array('auto')) == true && $couponSetData['ocs_issued_cnt_type'] == 'cnt'){
			$issuedTotalCnt = _MQ_result("select count(*) as cnt from smart_individual_coupon where coup_ocs_uid = '".$couponSetData['ocs_uid']."'  ");
			if( ($issuedTotalCnt+1) > $couponSetData['ocs_issued_cnt']){  return false; }
		}

		// 발급가능한 회원등급 체크 :: 쿠폰발급유형이 자동,다운로드 일 시 에만 (회원정보필요)
		if( in_array($couponSetData['ocs_issued_type'], array('auto')) == true) {
			$authGroup= $couponSetData['ocs_issued_group'] != '' ? explode(',',$couponSetData['ocs_issued_group']):array();
			if( count($authGroup) < 1){ return false; }
			if( count($mem_info) < 1){ return false; } // 회원정보 없을 시
			if( in_array($mem_info['in_mgsuid'],$authGroup) == false){return false;  }
		}


		// 쿠폰 중복발급 체크 (회원정보필요) - 자동발급
		if( in_array($couponSetData['ocs_issued_type'], array('auto')) == true && $couponSetData['ocs_issued_due_type'] == 'N' && in_array($couponSetData['ocs_issued_type_auto'] , array('1','3','4') ) == false ){
			if( count($mem_info) < 1){ return false; } // 회원정보 없을 시
			$coupTotalCnt  = _MQ_result("select count(*) as cnt from smart_individual_coupon where coup_inid = '".$mem_info['in_id']."' and coup_ocs_uid = '".$couponSetData['ocs_uid']."'
			 ");
			if( $coupTotalCnt > 0){ return false; }
		}

		// 쿠폰 중복 발급 체크 -- 생일쿠폰 체크 (회원정보 필요) :: 1년에 한번씩만 발급가능하기 때문에 해당 부분을 체크
		if( in_array($couponSetData['ocs_issued_type'], array('auto')) == true  && $couponSetData['ocs_issued_type_auto'] == '3'  ){
			if( count($mem_info) < 1){ return false; } // 회원정보 없을 시
			$coupTotalCnt  = _MQ_result("select count(*) as cnt from smart_individual_coupon where coup_inid = '".$mem_info['in_id']."' and coup_ocs_uid = '".$couponSetData['ocs_uid']."'
			  and left(coup_rdate,4) = '".date('Y')."' ");
			if( $coupTotalCnt > 0){ return false; }
		}

		// 쿠폰 중복 발급 체크 -- 첫 구매/결제시 ,회원가입 체크 :: 쿠폰 발급전이므로 같은 쿠폰이 한개도 없어야한다. (회원정보 필요)
		if( in_array($couponSetData['ocs_issued_type'], array('auto')) == true  && in_array($couponSetData['ocs_issued_type_auto'] , array('1','4') ) == true  ){
			if( count($mem_info) < 1){ return false; } // 회원정보 없을 시
			$coupTotalCnt  = _MQ_result("select count(*) as cnt from smart_individual_coupon where coup_inid = '".$mem_info['in_id']."' and coup_ocs_uid = '".$couponSetData['ocs_uid']."' ");
			if( $coupTotalCnt > 0){return false; }
		}

		return true;
	}

/*회원쿠폰
	- 주문 시 쿠폰이벤트 시 쿠폰적용 할 경우 추가
*/
function couponFormInsert($coup_uid)
{
	global $_COOKIE;
	if(is_login() == false){ return false; } // 회원이 아닐경우 제외
	if( $coup_uid == '') { return false; }
	_MQ_noreturn("insert smart_individual_coupon_form set ocf_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' , ocf_coupuid = '".$coup_uid."', ocf_rdate = now()  ");
}

/*회원쿠폰
	- 주문 시 쿠폰이벤트 시 쿠폰적용 취소할 경우 삭제처리
*/
function couponFormDelete($coup_uid)
{
	global $_COOKIE;
	if(is_login() == false){ return false; } // 회원이 아닐경우 제외
	if( $coup_uid == '') { return false; }
	_MQ_noreturn("delete from smart_individual_coupon_form where ocf_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and ocf_coupuid = '".$coup_uid."'    ");
}


/*회원쿠폰
	- 주문 시 쿠폰이벤트 초기화 (가장 먼저 실행)
*/
function couponFormInit()
{
	global $_COOKIE;
	if(is_login() == false){ return false; } // 회원이 아닐경우 제외

	_MQ_noreturn("delete from smart_individual_coupon_form where ocf_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'   ");
}



// ----- 상품 옵션 적합성 체크 -----
//			1. 상품 당 옵션정리 - 먼저 삭제
//			2. 적합성 체크
function product_option_validate_check($pcode){

	// 상품 당 옵션정리 - 먼저 삭제
	$arr_pouid = $arr_pouid_del = array();
	$res = _MQ_assoc(" SELECT po_depth , po_uid , po_parent FROM smart_product_option WHERE po_pcode = '". $pcode ."' ORDER BY po_depth ASC ");
	foreach( $res as $k=>$v ){
		$arr_pouid[$v['po_depth']][$v['po_uid']] ++;
		if($v['po_depth'] == 2 && !( $arr_pouid[1][$v['po_parent']] > 0 )) {
			$arr_pouid_del[$v['po_uid']] ++;//삭제할 옵션 UID
			_MQ_noreturn(" DELETE FROM smart_product_option WHERE po_uid = '". $v['po_uid'] ."' ");
		}
		else if($v['po_depth'] == 3) {
			$ex = explode(',' , $v['po_parent']);
			if(!( $arr_pouid[1][$ex[0]] > 0  && $arr_pouid[2][$ex[1]] > 0 )) {
				_MQ_noreturn(" DELETE FROM smart_product_option WHERE po_uid = '". $v['po_uid'] ."' ");
			}
		}
	}

	// 적합성 체크
	$res = _MQ("
		SELECT
			IFNULL(MAX(po.po_depth),0) as max_depth , IFNULL(REPLACE( IF( p.p_option_type_chk = 'nooption' , 0 , p.p_option_type_chk) , 'depth' , '' ),0) as p_chk , p.p_option_valid_chk
		FROM smart_product as p
		LEFT JOIN smart_product_option as po ON ( p.p_code = po.po_pcode )
		WHERE p.p_code='". $pcode ."'
	");
	if( $res['max_depth'] >= $res['p_chk'] && $res['p_option_valid_chk'] <> 'Y'){
		_MQ_noreturn(" UPDATE smart_product SET p_option_valid_chk = 'Y' WHERE p_code='". $pcode ."'  ");
	}
	if( $res['max_depth'] < $res['p_chk'] && $res['p_option_valid_chk'] <> 'N'){
		_MQ_noreturn(" UPDATE smart_product SET p_option_valid_chk = 'N' WHERE p_code='". $pcode ."'  ");
	}
}
// ----- 상품 옵션 적합성 체크 -----

// 2019-02-11 SSJ :: 상품 품절체크
// --- 상품 재고 변경 시 = 상품정보 수정 시, 옵션정보 수정 시, 주문 시, 주문 취소 시
function product_soldout_check($pcode){
    // 필수변수 체크
    if(!$pcode) return;

    // 상품정보 추출
    $pinfo = _MQ("select p_option_type_chk,p_stock,p_soldout_chk from smart_product where p_code = '". $pcode ."' ");
    $depth = rm_str($pinfo['p_option_type_chk']) * 1;
    $option = array();
    switch($depth){
        case 3: // 3차 옵션의 재고 추출
            $option = _MQ("
                select
                    sum(po3.po_cnt) as cnt
                from smart_product_option as po1
                left join smart_product_option as po2 on (po1.po_uid = po2.po_parent)
                left join smart_product_option as po3 on (find_in_set(po2.po_uid,po3.po_parent) > 0)
                where 1
                    and po1.po_pcode = '". $pcode ."'
                    and po1.po_depth = '1'
                    and po1.po_view = 'Y'
                    and po2.po_depth = '2'
                    and po2.po_view = 'Y'
                    and po3.po_depth = '3'
                    and po3.po_view = 'Y'
            ");
            break;
        case 2: // 2차 옵션의 재고 추출
            $option = _MQ("
                select
                    sum(po2.po_cnt) as cnt
                from smart_product_option as po1
                left join smart_product_option as po2 on (po1.po_uid = po2.po_parent)
                where 1
                    and po1.po_pcode = '". $pcode ."'
                    and po1.po_depth = '1'
                    and po1.po_view = 'Y'
                    and po2.po_depth = '2'
                    and po2.po_view = 'Y'
            ");
            break;
        case 1: // 1차 옵션의 재고 추출
            $option = _MQ("
                select
                    sum(po1.po_cnt) as cnt
                from smart_product_option as po1
                where 1
                    and po1.po_pcode = '". $pcode ."'
                    and po1.po_depth = '1'
                    and po1.po_view = 'Y'
            ");
            break;
        default: // 옵션없음
            $option['cnt'] = $pinfo['p_stock'];
            break;
    }
    $o_stock = $option['cnt'];

    // 상품재고가 0보다크면 옵션재고 체크
    if($pinfo['p_stock'] > 0){
        // 옵션재고가 0보다크고 솔드아웃 이면 솔드아웃 해제
        if($o_stock > 0 && $pinfo['p_soldout_chk'] == 'Y') _MQ_noreturn(" update smart_product set p_soldout_chk = 'N' where p_code = '". $pcode ."' ");
        // 옵션재고가 0이고 솔드아웃이 아니면 솔드아웃 처리
        else if($o_stock <= 0 && $pinfo['p_soldout_chk'] == 'N') _MQ_noreturn(" update smart_product set p_soldout_chk = 'Y' where p_code = '". $pcode ."' ");
    }

    // 상품재고가 0이고 솔드아웃이 아니면 솔드아웃 처리
    else if($pinfo['p_soldout_chk'] == 'N'){
        _MQ_noreturn(" update smart_product set p_soldout_chk = 'Y' where p_code = '". $pcode ."' ");
    }

    return;
}

// === 본인인증 휴대폰 번호 중복 체크 추가 통합 kms 2019-06-21 ====
function memberDuplicateTelChk( $_tel ){
	if ($_tel == "") { return true; }

	$tel_cnt_res = _MQ("select count(*) as cnt from smart_individual where in_tel2 = '{$_tel}' ");

	if ($tel_cnt_res['cnt'] > 0 ) {
		return true;
	}else{
		return false;
	}
}
// === 본인인증 휴대폰 번호 중복 체크 추가 통합 kms 2019-06-21 ====

// SSJ : 자동 배송완료 패치 : 2021-02-01
// --- 취소되지 않은 주문 중 배송상태가 배송중이고
// --- 상태 변경일로부터 설정일이 지나고
// --- 모든 주문상품이 위 조건에 부합한 주문
function auto_delivery_complete(){
	global $siteInfo;

	if($siteInfo['s_delivery_auto'] > 0){
		// 배송완료 조건에 맞는 주문 상품 검색
		$res = _MQ_assoc("
			select
				op.op_uid, op.op_oordernum
			from smart_order as o
			left join smart_order_product as op on (o.o_ordernum = op.op_oordernum)
			where 1
				and o.o_paystatus = 'Y'
				and o.o_canceled = 'N'
				and o.npay_order = 'N'
				and op.op_cancel = 'N'
				and op.op_sendstatus = '배송중'
				and op.op_senddate <= '". date('Y-m-d', strtotime($siteInfo['s_delivery_auto']*(-1). ' days')) ."'
		");
		$arr_ordernum = array();
		if(count($res) > 0){
			foreach($res as $k=>$v){
				$arr_ordernum[] = $v['op_oordernum'];
				_MQ_noreturn(" update smart_order_product set op_sendstatus = '배송완료' , op_completedate = now() where op_uid = '{$v['op_uid']}' ");
			}

			$arr_ordernum = array_unique($arr_ordernum);
			foreach($arr_ordernum as $k=>$v){
				// 주문상태 업데이트
				order_status_update($v);
			}
		}

	}
}


//KAY :: 에디터 이미지 관리 :: 2021-06-02
//    설명 : 에디터 태그를 통해 이미지 파일명 추출
//    Parameter
//        $content :: 에디터내용 불러오기
//        $table :: 에디터이미지 사용하는 테이블 
//                게시판(게시글관리) - board , 게시판(게시글양식) - board_template, 게시판(faq) - board_faq
//                디자인(일반페이지) - normal
//                디자인(팝업) - popup
//                프로모션 - promotion
//                상품 - product
//                환경설정 : 상품상세 이용안내 - setting
//                메일링  - mailing
//        $unique_num :: 각 테이블의 고유번호
//        $del_type :: 이미지 삭제시 적용.

// 한글명일 경우 확장자를 기준으로 짤림
// ex 기본이미지.png 이면 .png 로만 출력 한글명추출을 위한 함수
function getbasename($path) {
	$pattern = (strncasecmp(PHP_OS, 'WIN', 3) ? '/([^\/]+)[\/]*$/' : '/([^\/\\\\]+)[\/\\\\]*$/');
	if (preg_match($pattern, $path, $matches))
	return $matches[1];
	return '';
}

function editor_img_ex($content, $table , $unique_num) {

	// 하나라도 없을 경우 실행 X
	if( !$content || !$table || !$unique_num ) return;

	// content 내용으로 이미지태그 정규식 변환
	preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", stripslashes(stripslashes($content)), $arr_content_img);

	// 에디터 이미지 명 배열
	$arrInsertImages = array();


	// 에디터 이미지가 없는 경우 실행 X
	if(sizeof($arr_content_img[1]) > 0 ) {

		// --- 이미지명 배열화 LOOP ---
		foreach($arr_content_img[1] as $ek => $ev){

		// 이미지 도메인 추출 ( 외부이미지 판단 )
		preg_match_all("/^(?:(?:https?):\/\/)?([^\/]+)/i", $ev, $arr_outimg);

		// 외부 이미지일 경우 실행 X
		if( sizeof($arr_outimg) > 0 && $arr_outimg[1] && $arr_outimg[1] != $_SERVER['HTTP_HOST']){ continue ; }

		// 경로(url, 디렉토리경로 등)를 제외한 이미지 파일명   ( getbasename = 경로를 제외할수 있게 해주는 함수 )
		$app_img = getbasename($ev);

		// 한글파일 읽기를 위한 변환
		$app_img = iconv("UTF-8","EUC-KR",$app_img);  //  한글명 파일을 읽기위한 변환
		$app_img = str_replace('%2F','/',urldecode($app_img)); // 한글명 파일을 읽기 위한 변환시 디코딩

		// 에디터 이미지 절대경로
		$app_img_path = $_SERVER['DOCUMENT_ROOT'] . IMG_DIR_SMARTEDITOR . $app_img;

		// 에디터 이미지 태그만 있고 파일은 없는 경우 DB에 저장하지 말아야 함
		if( @file_exists($app_img_path) ) {

			$app_img = str_replace('%2F','/',urlencode($app_img)); // 한글명 변환 인코딩

			// --- 에디터 이미지 파일관리 DB 저장 ---
				// 에디터 이미지 파일 최초등록시 uid를 뽑아올수 없기때문에 LAST_INSERT_ID 사용
				_MQ_noreturn("
					INSERT INTO smart_editor_images_files
						(eif_img,eif_rdate, eif_use_cnt)
					VALUES
						('".trim($app_img)."', now(), '1')
					ON DUPLICATE KEY UPDATE
						eif_rdate = now(),
						eif_uid = LAST_INSERT_ID( eif_uid )
				");
				$arrInsertImages[] = trim($app_img);
				// --- 에디터 이미지 파일관리 DB 저장 --
				$eifuid = mysql_insert_id();

				// 에디터 이미지가 사용될때 
				// --- 사용관리 DB에 저장 ---
				_MQ_noreturn("
					INSERT INTO smart_editor_images_use
						(eiu_eifuid,eiu_datauid, eiu_tablename )
					VALUES
						('".$eifuid."','". addslashes($unique_num) ."', '". addslashes($table) ."' )
					ON DUPLICATE KEY UPDATE
						eiu_dummy = 0
				");
				// --- 사용관리 DB에 저장 ---
			}
		}// --- 이미지명 배열화 LOOP ---
	}

	// 파일, 사용 여부 정보 추출
	$res = _MQ_assoc("
		SELECT 
			eif.eif_img, eiu.eiu_eifuid,eiu.eiu_datauid
		FROM smart_editor_images_files as eif
			LEFT JOIN smart_editor_images_use as eiu on (eiu.eiu_eifuid = eif.eif_uid) 
		WHERE 
			eiu.eiu_datauid = '{$unique_num}' and eiu.eiu_tablename='{$table}'
	");

	foreach($res as $k=>$v){
		// 사용관리 DB 삭제
		if(in_array($v['eif_img'],$arrInsertImages) < 1){ _MQ_noreturn("DELETE FROM smart_editor_images_use WHERE eiu_eifuid='{$v['eiu_eifuid']}' ");   }

		_MQ_noreturn("
			UPDATE smart_editor_images_files SET 
			  eif_use_cnt = (SELECT COUNT(*) AS cnt FROM smart_editor_images_use WHERE eiu_eifuid = eif_uid),
			  eif_rdate = now()
			WHERE
			  eif_uid = '". $v['eiu_eifuid'] ."'
		");
	}
}


// 상품, 게시글, 게시글양식 등을 삭제했을때 이미지 사용관리 DB 삭제
function editor_img_del($unique_num , $table) {

	// 선택 삭제, 삭제 :: 들어오는 값 형태가 다름
	if(!is_array($unique_num)){ 
	$unique_num = array($unique_num); 
	}
	$arr_num ="'".implode("','",$unique_num)."'";

	// 파일, 사용 여부 정보 추출 - 반드시 삭제 전 정보 취합하여야 함.
	$res = _MQ_assoc(" SELECT eiu_eifuid FROM smart_editor_images_use WHERE  eiu_datauid in ($arr_num) AND eiu_tablename = '{$table}' ");

	// 사용 삭제
	_MQ_noreturn("DELETE FROM smart_editor_images_use WHERE eiu_tablename='{$table}' AND eiu_datauid in ($arr_num)  ");

	// 반드시 삭제 후 개수 갱신하여야 함.
	foreach($res as $k=>$v){
		_MQ_noreturn("
			UPDATE smart_editor_images_files SET
				eif_use_cnt = (SELECT COUNT(*) AS cnt FROM smart_editor_images_use WHERE eiu_eifuid ='{$v['eiu_eifuid']}'),eif_rdate = now()
			WHERE
				eif_uid = '". $v['eiu_eifuid'] ."'
		");
	}
}