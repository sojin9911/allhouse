<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// --> 옵션/장바구니/비회원 구매를 위한 쿠키 적용여부 파악
cookie_chk();



switch($mode){

    // 프로모션 코드 LMH005
    case "promotion_code":
        $do = !$do ? 'add' : $do;
        $__result_text = ""; $__result_code = "OK"; $__result_array = array();

        function promo_output($txt,$code,$result='') { echo json_encode(array('text'=>$txt,'code'=>$code,'result'=>$result)); exit; }

        if($promotion_code=='') { $__result_text = "프로모션코드를 입력하세요."; $__result_code = "FAIL"; promo_output($__result_text,$__result_code); }
        else {
            // 존재하는 코드 체크
            $chk = _MQ_result(" select count(*) from smart_promotion_code where pr_code = '".$promotion_code."' and pr_use = 'Y' ");
            if($chk==0) { $__result_text = "잘못된 코드입니다."; $__result_code = "FAIL"; promo_output($__result_text,$__result_code); }

            // 만료여부 체크
            $chk = _MQ_result(" select count(*) from smart_promotion_code where pr_code = '".$promotion_code."' and pr_expire_date >= CURDATE() and pr_use = 'Y' ");
            if($chk==0) { $__result_text = "만료된 코드입니다."; $__result_code = "FAIL"; promo_output($__result_text,$__result_code); }

            // 정보 반환
            $p = _MQ(" select * from smart_promotion_code where pr_code = '".$promotion_code."' and pr_use = 'Y' ");
            $__result_array = array("code"=>$promotion_code,"type"=>$p['pr_type'],"amount"=>$p['pr_amount']);
            $__result_text = "프로모션코드가 적용되었습니다."; promo_output($__result_text,$__result_code,$__result_array);
        }

    break;

    // 선택삭제
    case "select_onlydelete":
        // 상품코드 추출
        $_product = _MQ(" select c_pcode from smart_cart where c_uid = '".$cuid."' ");
        $code = $_product['c_pcode'];

        $que = "delete from smart_cart where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and c_uid = '".$cuid."' ";
        _MQ_noreturn($que);

        // 삭제후 남은 옵션중 필수 옵션이 없으면 모든 추가옵션 삭제
        $no_addoption_cnt = _MQ(" select count(*) as cnt from smart_cart where c_cookie ='". $_COOKIE["AuthShopCOOKIEID"] ."' and c_pcode = '".$code."' and c_is_addoption = 'N' ");
        if($no_addoption_cnt['cnt']==0) {
            _MQ_noreturn(" delete from smart_cart where c_cookie ='". $_COOKIE["AuthShopCOOKIEID"] ."' and c_pcode = '".$code."' ");
        }

        echo 'ok'; exit;
        break;




    // 선택수량변경 - for ajax
    case "select_modify":
        if(!$app_cnt) $app_cnt = $_ccnt[$cuid];
        if( $app_cnt <= 0 ) {
            echo 'error1'; exit; // 수정하실 수량은 0보다 커야 합니다.
        }


        $tmpVar = _MQ("select  c.* , p_point_per , p_groupset_use , p_code from smart_product as p inner join smart_cart c on (c.c_pcode = p.p_code) where c.c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and  c.c_uid = '".$cuid."'");


            // {{{회원등급혜택}}}
            if(is_login() == true && $tmpVar['p_groupset_use'] == 'Y'  ){ // 로그인 중이고 등급할인혜택 적용이 Y 라면
                $c_old_price = $tmpVar['c_old_price'];
                $c_old_point = ( ($c_old_price*$app_cnt)*($tmpVar[p_point_per]/100) );
                $c_price = $c_old_price-getGroupSetPer( $c_old_price,'price',$tmpVar['p_code']);
                $c_point = $c_old_point + getGroupSetPer( ($c_old_price*$app_cnt),'point',$tmpVar['p_code']);
                $c_groupset_price_per = $groupSetInfo['mgs_sale_price_per'] > 0 ? $groupSetInfo['mgs_sale_price_per'] : 0;
                $c_groupset_point_per = $groupSetInfo['mgs_give_point_per'] > 0 ? $groupSetInfo['mgs_give_point_per'] : 0;
            }else{
                $c_old_price = $tmpVar['c_old_price'];
                $c_old_point = ( ($c_old_price*$app_cnt)*($tmpVar[p_point_per]/100) );
                $c_price = $c_old_price;
                $c_point = $c_old_point;
                $c_groupset_price_per = 0;
                $c_groupset_point_per = 0;
            }
            $updateQue = "
                , c_price = '".$c_price."'
                , c_point = '".floor($c_point)."'
                , c_old_price = '".$c_old_price."'
                , c_old_point = '".floor($c_old_point)."'
                , c_groupset_price_per = '".$c_groupset_price_per."'
                , c_groupset_point_per = '".$c_groupset_point_per."'
            ";
            // {{{회원등급혜택}}}


        // 옵션에 따른 적립금 계산 적용값을 넣어준다.
        $que = "update smart_cart set c_cnt='".$app_cnt."' ".$updateQue." where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and c_uid = '".$cuid."' ";


        _MQ_noreturn($que);

        /* ------------ 주문 상품 재고 체크 ------------------*/
        // 주문을 위한 상품 재고 체크
        // 카트에 담긴 상품 수량을 현재 재고와 확인하여. 만약 보유 수량보다 주문량이 더 많을시,
        // 카트에 담긴 상품 수량을 강제 조정한다.
        // 함수 리턴값 (품절 : soldout , 수량이 부족 : notenough , 그외 ok)
        // 그후 엑션은 페이지에 따라서 처리한다.
        echo order_product_stock_check($_COOKIE["AuthShopCOOKIEID"]); exit;
        // case "soldout" : 장바구니 담긴 상품중 품절 된 상품이 있습니다.
        // case "notenough" : 해당 상품의 재고량이 부족합니다.
        // case "ok" : 성공
        /* ------------ // 주문 상품 재고 체크 ------------------*/
        break;


    // 선택 구매 (장바구니 구매)
    case "select_buy":

        if( count($_code) > 0 ) {
        }else{
//            error_frame_loc_msg("/?pn=shop.cart.list",'선택된 상품이 없습니다.');
//            exit;
        }

        $buy_type = $_POST["buy_type"];

        // 상품 금액 추출
        include(OD_PROGRAM_ROOT."/shop.cart.inc.php");

        // 장바구니 구매
        if ($buy_type == "cart") {
            $arr_product_sum = $arr_product = array();
            foreach($arr_cart as $crk=>$crv) {
                // -- 변수 초기화
                unset($del_chk_customer, $is_vat_free, $_num); // 2017-06-16 ::: 부가세율설정 ::: JJC
                $arr_product = array(); // 업체별 상품 합계
                $arr_per_product = array(); // 상품별 합계 // ----- JJC : 상품별 배송비 : 2018-08-16 -----

                // {{{LCY무료배송이벤트}}}
                $temp_delivery_sum = 0; // 무료배송일경우 임시로 저장하기 위한 배열

                foreach($crv as $k=>$v) { // 업체별 상품 반복 구간

                    // No. 설정
                    $_num++;
                    /* 상품 정보 */
                    $pr = $arr_product_info[$k]; // 업체 상품의 정보를 담는다.
                    $pro_name	= strip_tags($pr['p_name']);	// 상품명
                    /* 상품 정보 끝 */

                    // {{{회원등급혜택}}}
                    unset($groupSetUse);
                    if( $pr['p_groupset_use'] == 'Y' && is_login() == true ){
                        if($groupSetInfo['mgs_sale_price_per'] > 0 || $groupSetInfo['mgs_give_point_per'] > 0){
                            $groupSetUse = true;
                        }
                    }
                    // {{{회원등급혜택}}}

                    // -- 변수 초기화
                    unset($option_html , $sum_price , $sum_product_cnt, $sum_point);
                    foreach($v as $sk => $sv) {

                        $sv['p_vat'] = $siteInfo['s_vat_product'] == 'C' ? $sv['p_vat'] : $siteInfo['s_vat_product']; // SSJ : 2018-02-10 전체설정이 복합과세일때 상품의 과세설정을 그외는 전체설정을 따른다

                        $option_tmp_name = !$sv['c_option1'] ? '옵션없음' : trim($sv['c_option1'].' '.$sv['c_option2'].' '.$sv['c_option3']);
                        $option_tmp_price		= $sv['c_price'] + $sv['c_optionprice'];
                        $option_tmp_cnt			= $sv['c_cnt'];
                        $option_tmp_sum_price	= $sv['c_cnt'] * ($sv['c_price'] + $sv['c_optionprice']);
                        $app_point				= $sv['c_point'];

                        // 상품 수량 select 값
                        $c_option_color = "블랙";
                        $buy_limit_array = array();
                        $buy_max = 200; // 최고 구매갯수 설정
                        $buy_limit = $sv['buy_limit'] ? min($sv['c_option1'] ? $sv['oto_cnt'] : $sv['stock'] ,$sv['buy_limit']) : min($sv['c_option1'] ? $sv['oto_cnt'] : $sv['stock'] ,$buy_max); // 구매제한이 없으면 재고만큼만 선택할수 있게 하되 max는 200
                        for($i=1;$i<=$buy_limit;$i++) { $buy_limit_array[] = $i; }

                        if ($sv['c_option1'] == "색상") {
                            $c_option_color = $sv['c_option2'];
                            continue;
                        }

                        //상품수 , 포인트 , 상품금액
                        $arr_product["cnt"] += $option_tmp_cnt;//상품수
                        // ----- SSJ : 추가옵션은 개별배송비 미적용 : 2020-02-04 -----
                        if($sv['c_is_addoption']<>'Y') $sum_product_cnt += $option_tmp_cnt ;// |개별배송패치| - 상품갯수를 가져온다 : 해당 코드가 없을 시 추가
                        $arr_product["point"] += $app_point ;//포인트
                        $arr_product["sum"] += $option_tmp_sum_price;//상품금액
                        $arr_per_product[$k]['sum'] += $option_tmp_sum_price;//상품금액// ----- JJC : 상품별 배송비 : 2018-08-16 -----
                        $sum_price += $option_tmp_sum_price;//상품금액
                        $sum_point += $app_point;//상품당 포인트 합계 // 2016-12-13 ::: 포인트 적용 수정 - JJC

                    } // end foreach => $v

                    // 	KAY ::: 상품쿠폰 할인율 적용  ::: 2021-03-22
                    $ex_coupon = explode("|", $pr['p_coupon']);
                    $coupon_html = '';
                    if($ex_coupon[0] && $ex_coupon[1]){
                        $ex_coupon['name'] = stripslashes($ex_coupon[0]);
                        $ex_coupon['price'] = rm_comma($ex_coupon[2]);
                        $ex_coupon['per'] = floor(rm_comma($ex_coupon[3])*10)/10;
                        $ex_coupon['max'] = rm_comma($ex_coupon[4]); //쿠폰 최댓값 콤마 제거
                        $ex_coupon['perprice'] = floor($sv['c_price']*$sv['c_cnt']*$ex_coupon['per']/100); //퍼센트 계산

                        //per일때 최대값 비교, per이 아닌경우 원 출력
                        $ex_coupon_perprice = 0;
                        if($ex_coupon[1] == 'per'){
                            if($ex_coupon['max'] > 0 && $ex_coupon['max'] < $ex_coupon['perprice']){
                                $ex_coupon_perprice = $ex_coupon['max'];
                            }else{
                                $ex_coupon_perprice= $ex_coupon['perprice'];
                            }
                        }else{
                            $ex_coupon_perprice= $ex_coupon[2];
                        }

                        $ex_coupon_p = ($ex_coupon[1] == 'per' ? "<strong>" . $ex_coupon['per'] ."</strong>%" : "<strong>" . number_format($ex_coupon['price']) ."</strong>원"); //per일 경우 per price일경우 price
                        $ex_coupon_max = ($ex_coupon[1] == 'per' && $ex_coupon['max'] > 0 ? "</strong> ( 최대 <strong>". number_format($ex_coupon['max']) . "</strong>원 할인 )." : null); //max

                        if( $ex_coupon_perprice > $option_tmp_sum_price) { $coupon_html = ""; }
                    }

                    /* 추가배송비개선 - 2017-05-19::SSJ  */
                    // 배송설정별 추가배송비 적용을위한 클래스지정
                    $class_delivery_addprice = "";
                    $class_delivery_addprice_print = "";

                    // 배송비 추출
                    $app_delivery = "무료배송" ; $delivery_price = 0;
                    switch($pr['p_shoppingPay_use']){
                        case "Y":
                            $delivery_price = $pr['p_shoppingPay'] * $sum_product_cnt;// 선택 구매 2015-12-04 LDD // |개별배송패치|
                            $arr_product["delivery"]+= $pr['p_shoppingPay'] * $sum_product_cnt;

                            // {{{LCY무료배송이벤트}}}
                            $temp_delivery_sum  += $pr['p_shoppingPay'] * $sum_product_cnt;

                            $app_delivery = $delivery_price > 0 ? "<strong>" . number_format($delivery_price) . "</strong>원":"무료배송";
                            if($pr['p_shoppingPay'] > 0){
                                    $app_delivery .= "<br>(개별배송)";
                            }

                           // 입점업체의 설정체크
                            if($siteInfo['s_del_addprice_use']=="Y" && $arr_customer[$crk]['cp_del_addprice_use']=="Y" && $arr_customer[$crk]['cp_del_addprice_use_unit']=="Y"){
                                // 배송설정별 추가배송비 적용을위한 클래스지정
                                $class_delivery_addprice = "js_delevery_addprice js_delevery_addprice_unit";
                                $class_delivery_addprice_print = "js_delevery_addprice_print js_delevery_addprice_unit_print";
                            }
                            break;
                        case "F":
                            $app_delivery = "무료배송";
                            $delivery_price = 0;

                            // --- JJC : 무료배송 시 추가배송비 1회 적용 : 2020-04-28 ---
                            // 입점업체의 설정체크 / // 배송설정별 추가배송비 적용을위한 클래스지정
                            if($del_chk_customer <> $crk) {
                                //$del_chk_customer = $crk;
                                if($siteInfo['s_del_addprice_use']=="Y" && $arr_customer[$crk]['cp_del_addprice_use']=="Y" && $arr_customer[$crk]['cp_del_addprice_use_free']=="Y"){
                                    // 배송설정별 추가배송비 적용을위한 클래스지정
                                    $class_delivery_addprice = "js_delevery_addprice";
                                    $class_delivery_addprice_print = "js_delevery_addprice_print";
                                }
                            }
                            // --- JJC : 무료배송 시 추가배송비 1회 적용 : 2020-04-28 ---

                            break;
                        case "N":
                            $app_delivery = "무료배송";
                            $delivery_price = 0;
                            if($del_chk_customer <> $crk) {
                                $app_delivery = ($arr_customer[$crk]['app_delivery_price'] <> 0 ? "<strong>" . number_format($arr_customer[$crk]['app_delivery_price']) . "</strong>원" : "무료배송") ;
                                $arr_product["delivery"]+=$arr_customer[$crk]['app_delivery_price'];

                                // {{{LCY무료배송이벤트}}}
                                $temp_delivery_sum  += $arr_customer[$crk]['app_delivery_price'];

                                $del_chk_customer = $crk;
                                $delivery_price = $arr_customer[$crk]['app_delivery_price'];// 선택 구매 2015-12-04 LDD

                                // 일반배송상품중 무료배송조건충족시
                                if($arr_customer[$crk]['app_delivery_price']==0){
                                    // 입점업체의 설정체크
                                    if($siteInfo['s_del_addprice_use']=="Y" && $arr_customer[$crk]['cp_del_addprice_use']=="Y" && $arr_customer[$crk]['cp_del_addprice_use_normal']=="Y"){
                                        // 배송설정별 추가배송비 적용을위한 클래스지정
                                        $class_delivery_addprice = "js_delevery_addprice";
                                        $class_delivery_addprice_print = "js_delevery_addprice_print";
                                    }

                                // 일반배송상품
                                }else{
                                    // 입점업체의 설정체크
                                    if($siteInfo['s_del_addprice_use']=="Y" && $arr_customer[$crk]['cp_del_addprice_use']=="Y"){
                                        // 배송설정별 추가배송비 적용을위한 클래스지정
                                        $class_delivery_addprice = "js_delevery_addprice";
                                        $class_delivery_addprice_print = "js_delevery_addprice_print";
                                    }
                                }
                            }
                            break;
                        // ----- JJC : 상품별 배송비 : 2018-08-16 -----
                        case "P":
                            $cart_delivery_price = ($pr['p_shoppingPayPfPrice'] == 0 || $pr['p_shoppingPayPfPrice'] >  $arr_per_product[$k]['sum'] ? $pr['p_shoppingPayPdPrice'] : 0 ); // 2020-03-19 SSJ :: 상품별 무료배송 무료배송비 노출 오류 수정
                            $arr_product["delivery"]+= $cart_delivery_price;
                            $app_delivery = ($cart_delivery_price > 0 ? "<strong>" . number_format($cart_delivery_price) . "</strong>원" : "무료배송");
                            if($cart_delivery_price > 0){
                                $app_delivery .= "<div class=''>상품별배송".($pr['p_shoppingPayPfPrice'] > 0 ? "<br>(".number_format($pr['p_shoppingPayPfPrice'])."원 이상 무료배송)" : null)."</div>"; // 2020-03-19 SSJ :: 상품별 무료배송 무료배송비 노출 오류 수정
                            }

                            // {{{LCY무료배송이벤트}}}
                            $temp_delivery_sum  += $cart_delivery_price;
                            $delivery_price = $cart_delivery_price;// 선택 구매 2015-12-04 LDD

                            // 무료일 경우 --> 추가배송비 설정 사용함 + 상품별배송 상품을 무료배송비이상 구매하여 무료배송이 되었을때 추가배송비 적용
                            if($siteInfo['s_del_addprice_use']=="Y" && $cart_delivery_price == 0 && $siteInfo['s_del_addprice_use_product']=="Y"){
                                // 배송설정별 추가배송비 적용을위한 클래스지정
                                $class_delivery_addprice = "js_delevery_addprice";
                                $class_delivery_addprice_print = "js_delevery_addprice_print";
                            }
                            // 무료가 아닌 경우 --> 추가배송비 설정이 사용함으로 되어 있으면 진행
                            else if($siteInfo['s_del_addprice_use']=="Y" && $cart_delivery_price > 0 ){
                                // 배송설정별 추가배송비 적용을위한 클래스지정
                                $class_delivery_addprice = "js_delevery_addprice";
                                $class_delivery_addprice_print = "js_delevery_addprice_print";
                            }
                            break;
                        // ----- JJC : 상품별 배송비 : 2018-08-16 -----
                    }
                    /* 추가배송비개선 - 2017-05-19::SSJ  */

                    // {{{LCY무료배송이벤트}}}
                    if( $freeEventChk === true &&  $pr['p_free_delivery_event_use'] == 'Y' ){
                            if( $arr_product["delivery"] >= $delivery_price) $arr_product["delivery"] -= $delivery_price;
                            $app_delivery = "무료배송(이벤트)";
                            $delivery_price = 0;
                    }
                }
                // 전체 총계를 $arr_prouct_sum 배열에 담는다 $ak 는 키값으로 총계의 구분 키값이다.
                foreach($arr_product as $ak=>$av){ $arr_product_sum[$ak] += $av; }
            }
                
            // 사용가능한 적립금 추출
            $able_point = $mem_info['in_point']; // 회원 적립금
            $psers = array();
            if(is_login()){ // 2019-10-28 SSJ :: 회원일경우에만 적립금 체크
                $psers = _MQ("select sum(o_price_usepoint) as use_point_sum from smart_order where o_apply_point = 'N' and o_price_usepoint > 0 and  o_canceled = 'N' and o_paystatus = 'N' and  o_mid = '".get_userid()."' and o_paymethod in ('online','virtual') ");
            }
            if($able_point > 0){
                $use_point_sum = $psers['use_point_sum'] > 0 ? $psers['use_point_sum'] : 0;
                $able_point = $mem_info[in_point] - $use_point_sum;
                $able_point = $able_point <= 0 ? 0: $able_point;
            }

            $price_sum = $arr_product_sum['sum'];
            $price_total = ($arr_product_sum['sum'] + $arr_product_sum['delivery']+$arr_product_sum['add_delivery']);
            $price_delivery = ($arr_product_sum['delivery']+$arr_product_sum['add_delivery']);
            $price_add_delivery = $arr_product_sum['add_delivery'];
            $app_point = ceil($arr_product_sum['point']);

            $_ordernum					= shop_ordernum_create();//주문번호 생성 예) 12345-23456-34567
            $_memtype					= (is_login() ? "Y" : "N");//회원타입, Y:회원, N:비회원
            $_mid						= ( is_login() ? get_userid() : $_COOKIE["AuthShopCOOKIEID"] );//회원아이디, 비회원일 경우 쿠키정보 입력
            $_price_real				= $price_total;// 실제결제해야할 금액
            $_price_total				= $price_sum;// 구매총액 (상품 금액)
            $_price_delivery			= $price_delivery; //배송비
            $cpointres					= _MQ(" select ifnull(sum(c_point ),0) as sum_point from smart_cart where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' ");
            $_price_supplypoint			= floor($cpointres[sum_point]);//제공해야할 포인트

            // 포인트 있을경우 포인트 구매.
            if ($able_point >= $price_total) {
                $_price_usepoint			= $price_total;
                $_paystatus					= "Y";//결제상태
                $_status					= "접수완료";//주문상태
            } else {
                $_price_usepoint			= 0;
                $_paystatus					= "N";//결제상태
                $_status					= "접수대기";//주문상태
            }

        // 수기 주문
        } else if ($buy_type == "manual") {
            $price_sum = 0;
            $price_total = 0;
            $price_delivery = 0;
            $price_add_delivery = 0;
            $app_point = 0;
            $able_point = 0;

            $_ordernum					= shop_ordernum_create();//주문번호 생성 예) 12345-23456-34567
            $_memtype					= (is_login() ? "Y" : "N");//회원타입, Y:회원, N:비회원
            $_mid						= ( is_login() ? get_userid() : $_COOKIE["AuthShopCOOKIEID"] );//회원아이디, 비회원일 경우 쿠키정보 입력
            $_price_real				= $price_total;// 실제결제해야할 금액
            $_price_total				= $price_sum;// 구매총액 (상품 금액)
            $_price_delivery			= $price_delivery; //배송비
            $_price_supplypoint			= 0;//제공해야할 포인트

            $_price_usepoint			= 0;
            $_paystatus					= "N";//결제상태
            $_status					= "접수대기";//주문상태
        }


        $_price_coupon_individual	= $use_coupon_price_member;//보너스쿠폰사용액
        $_price_coupon_product		= $use_coupon_price_product;//상품쿠폰사용액

        // {{{회원쿠폰}}}
        $_save_price_coupon_individual	= $use_coupon_save_price_member;//보너스 쿠폰 적립액
        // {{{회원쿠폰}}}

        $_price_promotion			= $use_promotion_price;//프로모션코드 할인금액 LMH005
        $_paymethod = "point";//결제방식
        $_canceled					= "N";//결제취소상태

        $_oname  = $mem_info['in_name'];
        $_otel	= $mem_info['in_tel1'];
        $_ohp	= $mem_info['in_tel2'];
        $_oemail	= $mem_info['in_email'];
        $_rname	= $mem_info['in_name'];
        $_rhp	= $mem_info['in_tel2'];
        $_rtel	= $mem_info['in_tel1'];

        // -- smart_order 입력 ---
        $sque = "
            insert smart_order set
                o_ordernum							= '". $_ordernum ."'
                , o_buy_type				= '". $buy_type ."'
                , o_memtype							= '". $_memtype ."'
                , o_mid									= '". $_mid ."'
                , o_oname							= '". $_oname ."'
                , o_otel									= '". $_otel ."'
                , o_ohp									= '". $_ohp ."'
                , o_oemail							= '". $_oemail ."'
                , o_rname								= '". $_rname ."'
                , o_rtel									= '". $_rtel ."'
                , o_rhp									= '". $_rhp ."'
                , o_rpost								= '". $_post ."'
                , o_raddr1							= '". $_addr1 ."'
                , o_raddr2							= '". $_addr2 ."'
                , o_raddr_doro						= '". $_addr_doro ."'
                , o_content							= '". $_content ."'
                , o_price_real						= '". $_price_real ."'
                , o_price_total						= '". $_price_total ."'
                , o_price_delivery					= '". $_price_delivery ."'
                , o_price_supplypoint			= '". $_price_supplypoint ."'
                , o_price_usepoint				= '". $_price_usepoint ."'
                , o_price_coupon_product	= '". $_price_coupon_product ."'
                , o_paymethod						= '". $_paymethod ."'
                , o_paystatus						= '". $_paystatus ."'
                , o_canceled						= '". $_canceled ."'
                , o_status								= '". $_status ."'
                , o_bank								= '". $_bank ."'
                , o_get_tax							= '". $_get_tax ."'
                , o_deposit							= '". $_deposit ."'
                , o_rdate								= now()
                , o_apply_point						= 'N'
                , mobile								= '".$_is_mobile."'
                , o_rzonecode						= '".$_zonecode."'
                , o_area								= '". $app_area ."'
        ";
        // 프로모션코드 사용했다면 저장 LMH005
        if($_price_promotion > 0) {
            $sque .= " , o_promotion_code = '".$promotion_code."', o_promotion_price = '".$_price_promotion."' ";
        }

        // 현금 영수증 신청정보 저장
        if($_get_tax == "Y"){
            $sque .= "
                , o_tax_TradeUsage				= '". $_tax_TradeUsage ."'
                , o_tax_TradeMethod			= '". $_tax_TradeMethod ."'
                , o_tax_IdentityNum				= '". onedaynet_encode(rm_str($_tax_IdentityNum)) ."'
            ";
        }

        // {{{주문기기정보}}}
        $sque .= "
            , device_info = '".$GetDeviceInfo."'
        ";
        // {{{주문기기정보}}}

        //{{{회원쿠폰}}}
        if( count($use_coupon_member) > 0 ){
            $sque .= " , o_coupon_individual_uid = '".implode(",",$use_coupon_member)."'  ";
        }
        $sque .="
            , o_price_coupon_individual	= '". $_price_coupon_individual ."'
            , o_save_price_coupon_individual = '".$_save_price_coupon_individual."'
        ";
        //{{{회원쿠폰}}}

        // LCY : 2021-07-04 : 신용카드 간편결제 추가 -- 간편결제일경우 처리 --
        if( $_easypay_paymethod_type != '' ){
            $sque .= " , o_easypay_paymethod_type = '".$_easypay_paymethod_type."'  ";  
        }


        // JJC : 부분취소 개선 : 2021-02-10
        //	// 포인트 환불 방식을 가져와서 주문할때 입력 (부분취소 kms 2019-03-15)
        //	$site_info = get_site_info();
        //	$sque .= ",o_paycancel_method =  '".$site_info['s_paycancel_method']."'";
        //	// 포인트 환불 방식을 가져와서 주문할때 입력 (부분취소 kms 2019-03-15)
        // 포인트 환불 방식을 가져와서 주문할때 입력
        $sque .= ",o_paycancel_method =  '".$siteInfo['s_paycancel_method']."'";
        // JJC : 부분취소 개선 : 2021-02-10


        _MQ_noreturn($sque);


        // -- smart_order_product 입력 ---
        // -- 카트정보 체크 --
        include(OD_PROGRAM_ROOT."/shop.cart.inc.php");
        //return $arr_customer ;
        //Array
        //(
        //    [p_cpid] => Array
        //        (
        //            [app_delivery_price] => 5000 // 실제 배송비
        //        )
        //)
        $arr_customer_apply = array();
        $arr_product_apply = array();


        // 장바구니 주문
        if ($buy_type == "cart") {
            $sres = _MQ_assoc(" select c.*,p.p_name,p.p_cpid, p.p_commission_type, p.p_sPersent , p.p_shoppingPay_use , p.p_shoppingPay , p.p_vat
            , p_free_delivery_event_use , p_groupset_use

            /* JJC : 상품별 배송비 : 2018-08-16 */
            , p.p_shoppingPayPfPrice , p.p_shoppingPayPdPrice

            from smart_cart as c left join smart_product as p on (p.p_code = c.c_pcode) where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' order by p.p_cpid asc, c.c_is_addoption desc, c.c_uid asc ");// 선택 구매 2015-12-04 LDD

            foreach( $sres as $k=>$v ){

                // --- 배송비 타입 설정 ---
                $_delivery_type = "입점";
                switch($v['p_shoppingPay_use']){
                    case "Y": $_delivery_type ="개별"; $product_delivery_price = $v['p_shoppingPay'] * $v[c_cnt]; break;
                    case "N":
                        $_delivery_type ="입점";
                        $product_delivery_price = ($arr_customer_apply[$v['p_cpid']] > 0 ? 0 : $arr_customer[$v['p_cpid']]['app_delivery_price']); // 입점 기본배송정책 따름.
                        $arr_customer_apply[$v['p_cpid']] ++;// 배송비는 1회 적용
                    break; // 일괄 추가
                    case "F": $_delivery_type ="무료"; $product_delivery_price = 0; break;
                    // ----- JJC : 상품별 배송비 : 2018-08-16 -----
                    case "P":
                        $_delivery_type ="상품별";
                        $product_delivery_price = ($arr_product_per_apply[$v['c_pcode']] > 0 ? 0 : ($v['p_shoppingPayPfPrice'] == 0 || $v['p_shoppingPayPfPrice'] >  $arr_per_product[$v['c_pcode']]['sum'] ? $v['p_shoppingPayPdPrice'] : 0 )); // 상품별 배송비 설정 따름. // 2020-03-19 SSJ :: 상품별 무료배송 무료배송비 노출 오류 수정
                        $arr_product_per_apply[$v['c_pcode']] ++;// 배송비는 1회 적용
                        break;
                    // ----- JJC : 상품별 배송비 : 2018-08-16 -----
                }
                // 추가배송비
                $product_add_delivery_price = ($arr_product_apply[$v['c_pcode']] > 0 ? 0 : $op_add_delivery_price[$v['c_pcode']]);
                $arr_product_apply[$v['c_pcode']] ++;

                // ----- SSJ : 추가옵션은 개별배송비 미적용 : 2020-02-04 -----
                if($v['c_is_addoption'] == 'Y') $product_delivery_price = 0;



                $ssque = "
                    insert smart_order_product set
                        op_oordernum			= '". $_ordernum ."'
                        , op_pcode				= '". $v[c_pcode] ."'
                        , op_pouid				= '". $v[c_pouid]."'
                        , op_option1			= '". mysql_real_escape_string($v[c_option1]) ."'
                        , op_option2			= '". mysql_real_escape_string($v[c_option2]) ."'
                        , op_option3			= '". mysql_real_escape_string($v[c_option3]) ."'
                        , op_add_delivery_price = '".$product_add_delivery_price."'
                        , op_supply_price		= '". $v[c_supply_price] ."'
                        , op_price				= '". $v[c_price] ."'
                        , op_point				= '". $v[c_point]."'
                        , op_cnt				= '". $v[c_cnt] ."'
                        , op_sendstatus			= '구매발주'
                        , op_rdate				= now()
                        , op_partnerCode		= '".$v[p_cpid]."'
                        , op_comSaleType		= '".$v[p_commission_type]."'
                        , op_commission			= '".$v[p_sPersent]."'
                        , op_pname				= '".mysql_real_escape_string($v[p_name])."'
                        , op_delivery_price		= '". $product_delivery_price ."'
                        , op_is_addoption		= '". $v['c_is_addoption'] ."'
                        , op_addoption_parent	= '". $v['c_addoption_parent'] ."'
                        , op_delivery_type = '". $_delivery_type ."'
                ";


                // 2017-06-16 ::: 부가세율설정 ::: JJC
                $v['p_vat'] = $siteInfo['s_vat_product'] == 'C' ? $v['p_vat'] : $siteInfo['s_vat_product']; // SSJ : 2018-02-10 전체설정이 복합과세일때 상품의 과세설정을 그외는 전체설정을 따른다
                $ssque .= ", op_vat = '". $v['p_vat'] ."'";
                // 2017-06-16 ::: 부가세율설정 ::: JJC


                // {{{LCY무료배송이벤트}}} -- select 시 p_free_delivery_event_use 추가 , 상품당 설정이 있기때문에 해당 값을 op에 저장
                $v['p_free_delivery_event_use'] = $freeEventChk === true && $v['p_free_delivery_event_use'] == 'Y' ? 'Y':'N';
                $ssque .= "  , op_free_delivery_event_use = '".$v['p_free_delivery_event_use']."'   ";
                // {{{LCY무료배송이벤트}}}


                // {{{회원등급혜택}}} -- c_old_price , c_old_point , c_groupset_price_per, c_groupset_point_per 참고
                unset($groupSetUse);
                if( $v['p_groupset_use'] == 'Y' && is_login() == true ){
                    if($groupSetInfo['mgs_sale_price_per'] > 0 || $groupSetInfo['mgs_give_point_per'] > 0){
                        $groupSetUse = true;
                    }
                }

                // 변수 재정의
                $v['p_groupset_use'] = $groupSetUse === true && $v['p_groupset_use'] == 'Y' ? 'Y':'N';

                $ssque .= "  , op_groupset_use = '".$v['p_groupset_use']."'   ";
                $ssque .= "  , op_old_price = '".$v['c_old_price']."'   ";
                $ssque .= "  , op_old_point = '".$v['c_old_point']."'   ";
                $ssque .= "  , op_groupset_price_per = '".$v['c_groupset_price_per']."'   ";
                $ssque .= "  , op_groupset_point_per = '".$v['c_groupset_point_per']."'   ";
                // {{{회원등급혜택}}}

                // SSJ : 정산 할인금액 패치 : 2021-05-14 -- 상품쿠폰 사용액 저장
                if(count($product_coupon) > 0 && count($tmp_product_coupon) < 1){ $tmp_product_coupon = $product_coupon; }
                if($tmp_product_coupon[$v['c_pcode']] > 0){
                    $ssque .= "  , op_use_product_coupon = '".$tmp_product_coupon[$v['c_pcode']]."'   ";
                    $tmp_product_coupon[$v['c_pcode']] = 0; // 적용 후 초기화
                }

                _MQ_noreturn($ssque);
            }
            // -- smart_order_product 입력 ---


        // 수기 주문
        } else if ($buy_type == "manual") {
            $sres = _MQ_assoc(" select * from smart_cart_manual as c where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' order by c.c_uid asc ");

            foreach( $sres as $k=>$v ){
                if (!$v[c_item_name]) $v[c_item_name] = $tmp_c_item_name;
                if (!$v[c_brand]) $v[c_brand] = $tmp_c_brand;
                $ssque = "
                    insert smart_order_product set
                        op_oordernum			= '". $_ordernum ."'
                        , op_pcode				= ''
                        , op_pouid				= '". mysql_real_escape_string($v[c_pno]) ."'
                        , op_option1			= '". mysql_real_escape_string($v[c_color]) ."'
                        , op_option2			= '". mysql_real_escape_string($v[c_size]) ."'
                        , op_option3			= ''
                        , op_add_delivery_price = ''
                        , op_supply_price		= ''
                        , op_price				= '0'
                        , op_point				= '0'
                        , op_cnt				= '". $v[c_cnt] ."'
                        , op_sendstatus			= '구매발주'
                        , op_rdate				= now()
                        , op_partnerCode		= ''
                        , op_comSaleType		= ''
                        , op_commission			= ''
                        , op_pname				= '".mysql_real_escape_string($v[c_item_name])."'
                        , op_pbrand				= '".mysql_real_escape_string($v[c_brand])."'
                        , op_delivery_price		= ''
                        , op_is_addoption		= 'N'
                        , op_addoption_parent	= '0'
                        , op_delivery_type = '개별'
                ";
                $tmp_c_item_name = $v[c_item_name];
                $tmp_c_brand = $v[c_brand];


                // 2017-06-16 ::: 부가세율설정 ::: JJC
                $v['p_vat'] = $siteInfo['s_vat_product'] == 'C' ? $v['p_vat'] : $siteInfo['s_vat_product']; // SSJ : 2018-02-10 전체설정이 복합과세일때 상품의 과세설정을 그외는 전체설정을 따른다
                $ssque .= ", op_vat = '". $v['p_vat'] ."'";
                // 2017-06-16 ::: 부가세율설정 ::: JJC


                // {{{LCY무료배송이벤트}}} -- select 시 p_free_delivery_event_use 추가 , 상품당 설정이 있기때문에 해당 값을 op에 저장
                $v['p_free_delivery_event_use'] = $freeEventChk === true && $v['p_free_delivery_event_use'] == 'Y' ? 'Y':'N';
                $ssque .= "  , op_free_delivery_event_use = '".$v['p_free_delivery_event_use']."'   ";
                // {{{LCY무료배송이벤트}}}


                // {{{회원등급혜택}}} -- c_old_price , c_old_point , c_groupset_price_per, c_groupset_point_per 참고
                unset($groupSetUse);
                if( $v['p_groupset_use'] == 'Y' && is_login() == true ){
                    if($groupSetInfo['mgs_sale_price_per'] > 0 || $groupSetInfo['mgs_give_point_per'] > 0){
                        $groupSetUse = true;
                    }
                }

                // 변수 재정의
                $v['p_groupset_use'] = $groupSetUse === true && $v['p_groupset_use'] == 'Y' ? 'Y':'N';

                $ssque .= "  , op_groupset_use = '".$v['p_groupset_use']."'   ";
                $ssque .= "  , op_old_price = '".$v['c_old_price']."'   ";
                $ssque .= "  , op_old_point = '".$v['c_old_point']."'   ";
                $ssque .= "  , op_groupset_price_per = '".$v['c_groupset_price_per']."'   ";
                $ssque .= "  , op_groupset_point_per = '".$v['c_groupset_point_per']."'   ";
                // {{{회원등급혜택}}}

                // SSJ : 정산 할인금액 패치 : 2021-05-14 -- 상품쿠폰 사용액 저장
                if(count($product_coupon) > 0 && count($tmp_product_coupon) < 1){ $tmp_product_coupon = $product_coupon; }
                if($tmp_product_coupon[$v['c_pcode']] > 0){
                    $ssque .= "  , op_use_product_coupon = '".$tmp_product_coupon[$v['c_pcode']]."'   ";
                    $tmp_product_coupon[$v['c_pcode']] = 0; // 적용 후 초기화
                }

                _MQ_noreturn($ssque);
            }
            // -- smart_order_product 입력 ---
        } // 수기 주문 끝

        // ----- JJC : 상품별 배송비 : 2018-08-16 -----
        $arr_product_per_apply = array();
        $arr_per_product = array();
        foreach( $sres as $k=>$v ){
            $arr_per_product[$v['c_pcode']]['sum'] += $v['c_cnt'] * ($v['c_price'] + $v['c_optionprice']);
        }
        // ----- JJC : 상품별 배송비 : 2018-08-16 -----



        include_once OD_PROGRAM_ROOT."/shop.order.usepoint.php";


        // -- 주문서 저장 후 세션파괴 ::: 재등록 막기 ---
        $_SESSION["order_start"] = "";
        session_destroy();
        session_start();

        if(substr(phpversion(),0,3) < 5.4) { session_register("session_ordernum"); }
        $_SESSION["session_ordernum"] = $_ordernum;//주문번호


        // 결제 상태 업데이트
        if($_paystatus <> "N") {
            $ordernum = $_ordernum;

            $__sque = "update smart_order set o_paystatus='Y' , o_status='접수완료', o_paydate=now() where o_ordernum='". $ordernum ."' ";
            _MQ_noreturn($__sque);

            $__sque = "update smart_order_product set op_paydate=now() where op_oordernum='". $ordernum ."' ";
            _MQ_noreturn($__sque);


            // 상품 재고 차감 및 판매량 증가
            $_ordernum = $ordernum;
            include(OD_PROGRAM_ROOT."/shop.order.salecntadd_pro.php");


            // 결제가 확인되었을 경우 - 포인트 쿠폰 - 적용
            // 제공변수 : $_ordernum
            $_ordernum = $ordernum;
            include(OD_PROGRAM_ROOT."/shop.order.pointadd_pro.php");


            // 주문정보 보정
            if(!$r) $r = $order_info;
            if(!$r) $r = get_order_info($_ordernum);


            // 문자 발송
            $sms_to = $r['o_ohp'] ? $r['o_ohp'] : $r['o_otel'];
            shop_send_sms($sms_to,"order_pay",$_ordernum);


            // - 메일발송 ---
            $_oemail = $r['o_oemail'];
            if( mailCheck($_oemail) ){
                $_ordernum = $ordernum;
                $_type = "card"; // 결제확인처리
                include_once(OD_PROGRAM_ROOT."/shop.order.mail.php"); // 메일 내용 불러오기 ($mailing_content)
                $_title = "[".$siteInfo['s_adshop']."]주문하신 상품의 결제가 성공적으로 완료되었습니다!";
                $_content = $mailing_app_content;
                $_content = get_mail_content($_content);
                mailer( $_oemail , $_title , $_content );
            }
            // - 메일발송 ---


            // 2018-12-12 SSJ :: 주문상태 업데이트 추가
            order_status_update($_ordernum);
        }

        // 장바구니 정보 삭제 - 무통장결제는 shop.order.pro.php에서처리 SSJ : 2018-03-22
        if($_COOKIE["AuthShopCOOKIEID"]) {
            // 장바구니 주문
            if ($buy_type == "cart") {
                _MQ_noreturn(" delete from smart_cart where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' ");
            // 수기 주문
            } else if ($buy_type == "manual") {
                _MQ_noreturn(" delete from smart_cart_manual where c_cookie='".$_COOKIE["AuthShopCOOKIEID"]."' ");
            }
        }

        error_loc("/?pn=shop.order.complete");


    break;




    // 다수선택삭제
    case "select_delete":

        if( sizeof($_code) == 0 ) {
            echo 'error1'; exit;  // 1개이상 선택해주시기 바랍니다.
        }

        // 값이 key 에 있는지 val 에 있는지 체크하여 처리한다.
        if($_code[0]) $_code_array = implode("','" , $_code);
        else $_code_array = implode("','" , array_keys($_code));

        $que = "delete from smart_cart where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and c_pcode  in ('".$_code_array."') ";
        _MQ_noreturn($que);

        echo 'ok'; exit;
        break;



    // - 다수 선택 추가  ---
    case "select_add":

        if( sizeof($pcode_array) == 0 ) {
            error_msg("1개이상 선택해주시기 바랍니다.");
        }

        for($i=0;$i<count($pcode_array);$i++) {
            $pcode = $pcode_array[$i];

            if( !$pass_type ) {
                $pass_type = "cart";
            }

            // 이미 담긴 상품인지 체크
            $cnt_tmp = _MQ("select count(*) as cnt from smart_cart  where c_pcode = '". $pcode ."' and c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and  c_pouid = '0'");
            if($cnt_tmp[cnt] > 0) continue;

            // 상품공급가를 구한다 - 정산형태가 수수료일경우에는 수수료로 공급가를 계산해서 넣는다.
            $pinfo = get_product_info($pcode);
            $c_supply_price = $pinfo[p_commission_type] == "공급가" ? $pinfo[p_sPrice] : $pinfo[p_price] - round($pinfo[p_price] * $pinfo[p_sPersent] / 100);

            // {{{회원등급혜택}}}
            if(is_login() == true && $pinfo['p_groupset_use'] == 'Y'  ){ // 로그인 중이고 등급할인혜택 적용이 Y 라면
                $c_old_price = $pinfo['p_price'];
                $c_old_point = ( ($c_old_price*$c_cnt)*($pinfo[p_point_per]/100) );
                $c_price = $c_old_price-getGroupSetPer( $c_old_price,'price',$pinfo['p_code']);
                $c_point = $c_old_point + getGroupSetPer( ($c_old_price*$c_cnt),'point',$pinfo['p_code']);
                $c_groupset_price_per = $groupSetInfo['mgs_sale_price_per'] > 0 ? $groupSetInfo['mgs_sale_price_per'] : 0;
                $c_groupset_point_per = $groupSetInfo['mgs_give_point_per'] > 0 ? $groupSetInfo['mgs_give_point_per'] : 0;
            }else{
                $c_old_price = $pinfo['p_price'];
                $c_old_point = ( ($c_old_price*$c_cnt)*($pinfo[p_point_per]/100) );
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
                    c_pcode = '". $pcode ."'
                , c_cnt = '1'
                , c_pouid = '0'
                ".$sque_tmp."
                , c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'
                , c_rdate = now()
                , c_supply_price = '".$c_supply_price."'
                , c_price = ".$c_price."
                , c_point = ".floor($c_point)."
                , c_direct			= '".($pass_type=='order'?'Y':'N')."'

                ".$add_que."
            ";

            _MQ_noreturn($sque);

        }	// end for



        if( $pass_type == "order" ) {
            // 2020-03-25 SSJ :: 비회원 주문 시 로그인 페이지로 이동
            if ( $siteInfo['s_none_member_buy'] == "Y" && !is_login() && $siteInfo['s_none_member_login_skip'] <> 'Y' ) {
                error_loc("/?pn=member.login.form&_rurl=".enc('e' , 'pn=shop.order.form'));
            }else{
                error_loc("/?pn=shop.order.form");
            }
        }
        else {
            error_loc("/?pn=shop.cart.list");
        }
        break;




    // - 추가 (상세페이지로부터 넘겨져옴) ---
    case "add":
        // 넘겨져온 변수
        //pcode=$code&pass_type=type(order:주문하기/cart:장바구니)
        $pcode = nullchk($pcode , "상품을 선택해주시기 바랍니다.");
        if( !$pass_type ) {
            $pass_type = "cart";
        }

        _MQ_noreturn(" update smart_cart set c_direct = 'N' where c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' ");// 선택 구매 2015-12-04 LDD


        // 옵션 없는 경우
        if( $option_select_type == "nooption" ) {
            // 장바구니 넣기

            // 이미 담긴 상품인지 체크
            $cnt_tmp = _MQ("select count(*) as cnt from smart_cart  where c_pcode = '". $pcode ."' and c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."' and  c_pouid = '0'");
            if($cnt_tmp[cnt] > 0) error_frame_loc_msg("/?pn=shop.cart.list","이미 장바구니에 담긴 상품입니다.");

            $c_cnt = $option_select_cnt > 1 ? $option_select_cnt : 1;

            // 상품공급가를 구한다 - 정산형태가 수수료일경우에는 수수료로 공급가를 계산해서 넣는다.
            $pinfo = get_product_info($pcode);
            $c_supply_price = $pinfo[p_commission_type] == "공급가" ? $pinfo[p_sPrice] : $pinfo[p_price] - round($pinfo[p_price] * $pinfo[p_sPersent] / 100);

            // {{{회원등급혜택}}}
            if(is_login() == true && $pinfo['p_groupset_use'] == 'Y'  ){ // 로그인 중이고 등급할인혜택 적용이 Y 라면
                $c_old_price = $pinfo['p_price'];
                $c_old_point = ( ($c_old_price*$c_cnt)*($pinfo[p_point_per]/100) );
                $c_price = $c_old_price-getGroupSetPer( $c_old_price,'price',$pinfo['p_code']);
                $c_point = $c_old_point + getGroupSetPer( ($c_old_price*$c_cnt),'point',$pinfo['p_code']);
                $c_groupset_price_per = $groupSetInfo['mgs_sale_price_per'] > 0 ? $groupSetInfo['mgs_sale_price_per'] : 0;
                $c_groupset_point_per = $groupSetInfo['mgs_give_point_per'] > 0 ? $groupSetInfo['mgs_give_point_per'] : 0;
            }else{
                $c_old_price = $pinfo['p_price'];
                $c_old_point = ( ($c_old_price*$c_cnt)*($pinfo[p_point_per]/100) );
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
                  c_pcode = '". $pcode ."'
                , c_cnt = '".$c_cnt."'
                , c_pouid = '0'
                ".$sque_tmp."
                , c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'
                , c_rdate = now()
                , c_supply_price = '".$c_supply_price."'
                , c_price = ".$c_price."
                , c_point = ".floor($c_point)."
                , c_direct				= '".($pass_type=='order'?'Y':'N')."'

                ".$add_que."
            ";


            _MQ_noreturn($sque);
        }
        else {
            // 선택옵션 정보 추출
            $que = "select * from smart_product_tmpoption where pto_mid='".$_COOKIE["AuthShopCOOKIEID"]."' order by pto_uid asc ";
            $res = _MQ_assoc($que);
            foreach( $res as $k=>$v ){

                // 같은 상품은 삭제한다
                _MQ_noreturn("delete from smart_cart where c_pcode = '". $pcode ."' and c_pouid = '".$v[pto_pouid]."' and c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'");

                // 상품공급가를 구한다 - 정산형태가 수수료일경우에는 수수료로 공급가를 계산해서 넣는다.
                $pinfo = get_product_info($pcode);
                $c_supply_price = $pinfo[p_commission_type] == "공급가" ? $v[pto_poption_supplyprice] : $v[pto_poptionprice] - round($v[pto_poptionprice] * $pinfo[p_sPersent] / 100);



                // {{{회원등급혜택}}}
                if(is_login() == true && $pinfo['p_groupset_use'] == 'Y'  ){ // 로그인 중이고 등급할인혜택 적용이 Y 라면
                    $c_old_price = $v[pto_poptionprice];
                    $c_old_point = ( ($c_old_price*$v[pto_cnt])*($pinfo[p_point_per]/100) );
                    $c_price = $c_old_price - getGroupSetPer( $c_old_price,'price',$v['pto_pcode']);
                    $c_point = $c_old_point + getGroupSetPer( ($c_old_price*$v[pto_cnt]),'point',$v['pto_pcode']);
                    $c_groupset_price_per = $groupSetInfo['mgs_sale_price_per'] > 0 ? $groupSetInfo['mgs_sale_price_per'] : 0;
                    $c_groupset_point_per = $groupSetInfo['mgs_give_point_per'] > 0 ? $groupSetInfo['mgs_give_point_per'] : 0;
                }else{
                    $c_old_price = $v[pto_poptionprice]; // 기존금액
                    $c_old_point = ( ($c_old_price*$v[pto_cnt])*($pinfo[p_point_per]/100) );  // 기존금액
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


                // 장바구니 넣기
                $sque = "
                    insert smart_cart set
                        c_pcode = '". $pcode ."'
                        , c_option1 = '". mysql_real_escape_string($v[pto_poptionname1])."'
                        , c_option2 = '". mysql_real_escape_string($v[pto_poptionname2])."'
                        , c_option3 = '". mysql_real_escape_string($v[pto_poptionname3])."'
                        , c_cnt = '".$v[pto_cnt]."'
                        , c_pouid = '".$v[pto_pouid]."'
                        ".$sque_tmp."
                        , c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'
                        , c_rdate = now()
                        , c_supply_price = '". $c_supply_price."'
                        , c_price = '". $c_price."'
                        , c_point = '".  floor($c_point) ."'
                        , c_direct = '".($pass_type=='order'?'Y':'N')."'
                        , c_is_addoption = '". $v['pto_is_addoption']."'
                        , c_addoption_parent = '". $v['pto_addoption_parent']."'

                        ".$add_que."


                ";
                _MQ_noreturn($sque);
            }
            _MQ_noreturn("delete from smart_product_tmpoption where pto_mid ='". $_COOKIE["AuthShopCOOKIEID"] ."' ");
        }

        if( $pass_type == "order" ) {
            // === 비회원구매 설정 kms 2019-06-25 ====
            if ( $none_member_buy === true ) {
                error_confirm_msg("/?pn=shop.order.form", '구매하기는 로그인 후 이용하실 수 있습니다.\n\n로그인 페이지로 이동하시겠습니까?', '/?pn=shop.cart.list' );
            }else{
                // 2020-03-25 SSJ :: 비회원 주문 시 로그인 페이지로 이동
                if ( $siteInfo['s_none_member_buy'] == "Y" && !is_login() && $siteInfo['s_none_member_login_skip'] <> 'Y' ) {
                    error_frame_loc("/?pn=member.login.form&_rurl=".enc('e' , 'pn=shop.order.form'));
                }else{
                    error_frame_loc("/?pn=shop.order.form");
                }
            }
            // === 비회원구매 설정 kms 2019-06-25 ====

        }
        else {
            // 2016-05-23 장바구니 담은 후 레이어팝업으로 물어보기 - 추가
            if( preg_match("/product.view/i" , $_SERVER["HTTP_REFERER"]) && $pass_mode == ''  ) {
                $cart_cnt = get_cart_cnt();
                echo '
                    <script src="/include/js/jquery-1.11.2.min.js"></script>
                    <script >
                        $(document).ready(function(){
                            $(".view_cart_ask" , parent.document).addClass("if_cart_save");
                            $(".glb_cart_cnt" , parent.document).text('. $cart_cnt .');
                            $("iframe[name=common_frame]" , parent.document).attr("src" , "about:blank");
                        });
                    </script>
                ';
            }
            // 목록에서 바로 담을 경우 처리
            else {
                error_frame_loc('/?pn=shop.cart.list');
            }
        }
        break;


    // - 수기주문 추가 ---
    case "manual_add":
        // 이미 담긴 상품인지 체크
        $cnt_tmp = _MQ("select max(c_pno) as max_c_pno from smart_cart_manual ");
        if($cnt_tmp[max_c_pno] == 0) $c_pno++; else $c_pno = $cnt_tmp[max_c_pno]+1;

        $cm_brand = $_POST["cm_brand"];
        $cm_item_name = $_POST["cm_item_name"];
        $cm_color = $_POST["cm_color"];
        $cm_size = $_POST["cm_size"];
        $cm_cnt = $_POST["cm_cnt"];

        for ($i =0 ; $i < count($cm_size); $i++) {
            $c_brand = $cm_brand[$i];
            $c_item_name = $cm_item_name[$i];
            $c_color = $cm_color[$i];
            $c_size = strtoupper($cm_size[$i]);
            $c_cnt = $cm_cnt[$i];
            if ($c_size && $c_cnt) {
                $sque = "
                    insert smart_cart_manual set
                      c_cookie = '".$_COOKIE["AuthShopCOOKIEID"]."'
                    , c_pno = '". $c_pno ."'
                    , c_brand = '".$c_brand."'
                    , c_item_name = '".$c_item_name."'
                    , c_color = '".$c_color."'
                    , c_size = '".$c_size."'
                    , c_cnt = '".$c_cnt."'
                    , c_rdate = now() 
                ";
                _MQ_noreturn($sque);
            }
        }
        echo 'ok'; exit;

        break;

}


actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행