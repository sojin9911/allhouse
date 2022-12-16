<?php
    if(!isset($_REQUEST['view'])) $_REQUEST['view'] = '';
    if($_REQUEST['view'] == 'online') {
        $app_current_link = '_order.list.php?view=online';// 메뉴지정
        $app_current_link_list = '_order.list.php';// 목록페이지 지정
    }else if($_REQUEST['view'] == 'cancel') {
        $app_current_link = '_order.cancel_list.php';// 메뉴지정
        $app_current_link_list = '_order.cancel_list.php';// 목록페이지 지정
    }else if($_REQUEST['view'] == 'order_delivery') {
        $app_current_link = '_order_delivery.list.php';// 메뉴지정
        $app_current_link_list = '_order_delivery.list.php';// 목록페이지 지정
    }else if($_REQUEST['view'] == 'order_product') {
        $app_current_link = '_order_product.list.php';// 메뉴지정
        $app_current_link_list = '_order_product.list.php';// 목록페이지 지정
    }else if($_REQUEST['view'] == 'cancel_order') { // SSJ : 주문/결제 통합 패치 : 2021-02-24
        $app_current_link = '_cancel_order.list.php';// 메뉴지정
        $app_current_link_list = '_cancel_order.list.php';// 목록페이지 지정
    }else{
        $app_current_link = '_order.list.php';// 메뉴지정
        $app_current_link_list = '_order.list.php'; // 목록페이지 지정
    }


    include_once('wrap.header.php');


    if( $_mode == "modify" ) {
        // 주문정보 추출
        $que = " select * from smart_order where o_ordernum='{$_ordernum}' ";
        $row = _MQ($que);

        $_member = _MQ(" select * from smart_individual where in_id = '".$row['o_mid']."' ");

        // 주문상품 정보 추출
        $arr_product = array();
        $sres = _MQ_assoc("
            select op.* , p.p_name, p.p_img_list_square, p.p_code
            from smart_order_product as op
            left join smart_product as p on (p.p_code=op.op_pcode)
            where op_oordernum='{$_ordernum}'
            order by op.op_uid
        ");
        # 주문상품 추출
        $arr_pinfo = array(); // 주문상품, 옵션 정보
        $arr_status = array(); // 주문상품 진행상태 체크
        $arr_sendnum = array(); // 배송정보 체크
        foreach($sres as $sk=>$sv){
            // 장바구니 주문
            if ($row["o_buy_type"] == "cart") {
                $op_pcode = $sv['op_pcode'];
             // 수기 주문
            } else if ($row["o_buy_type"] == "manual") {
                $op_pcode = $sv['op_pouid'];
            }

            // 상품코드
            $arr_pinfo[$op_pcode]['code'] = $op_pcode;
            // 상품명
            $arr_pinfo[$op_pcode]['name'] = stripslashes($sv['op_pname']);
            // 이미지 체크
            $_p_img = get_img_src('thumbs_s_'.$sv['p_img_list_square']);
            if($_p_img == '') $_p_img = 'images/thumb_no.jpg';
            $arr_pinfo[$op_pcode]['img'] = $_p_img;

            // 부분취소 상태 체크 -- 결제전에는 상태없음
            $app_cancel_btn = '';
            if($row['o_canceled']=='N' && $row['o_paystatus']=='Y' && $sv['op_is_addoption'] == 'N'){
                if($sv['op_cancel'] == 'Y'){
                    $app_cancel_btn = '<span class="option_btn"><span class="c_btn h22 white line t4">취소완료</span></span>';
                }else if($sv['op_cancel'] == 'R'){
                    $app_cancel_btn = '<span class="option_btn"><span class="c_btn h22 gray line t5">취소요청중</span></span>';
                }else if($sv['op_cancel'] == 'N'){
                    if(!$sv['op_complain']){
                        if(in_array($row['o_paymethod'], $arr_cancel_part_payment_type) || in_array($row['o_paymethod'], $arr_refund_payment_type)){ // SSJ : 주문/결제 통합 패치 : 2021-02-24
                            $app_cancel_btn = '<span class="option_btn"><a href="#none" onclick="return false;" class="c_btn h22 black line dark t4 product_cancel" data-ordernum="'. $row['o_ordernum'] .'" data-opuid="'. $sv['op_uid'] .'">부분취소</a></span>';
                        }

                        // JJC : 간편결제 - 페이플 : 2021-06-05 - 부분취소불가
                        if($row['o_paymethod'] == "payple") {$app_cancel_btn = "";}


                    }else{
                        $app_cancel_btn = '<span class="option_btn"><span class="c_btn h22 gray line t6">'.$arr_massage_conv[$sv['op_complain']].'</span></span>';
                    }
                }
            }


            // 2017-06-20 ::: 부가세율설정 ::: JJC
            $app_vat_str = ( ($siteInfo['s_vat_product'] == 'C' && $sv['op_vat'] == 'N') ? ' <span class="t_blue">(면세)</span>' : '');
            // 2017-06-20 ::: 부가세율설정 ::: JJC

            if($sv['op_pouid']){ // 옵션있음
                $arr_pinfo[$op_pcode]['has_option'] = 'Y';
                $arr_pinfo[$op_pcode]['option'][$sk] = array(
                                                                            'op_uid'=>$sv['op_uid']
                                                                            ,'name'=>implode(' ', array_filter(array($sv['op_option1'],$sv['op_option2'],$sv['op_option3'])))
                                                                            ,'price'=>$sv['op_price']
                                                                            ,'cnt'=>$sv['op_cnt']
                                                                            ,'is_addoption'=>$sv['op_is_addoption']
                                                                            ,'app_cancel_btn'=>$app_cancel_btn
                                                                            ,'app_vat_str'=>$app_vat_str
                                                                        );
            }else{ // 옵션없음
                $arr_pinfo[$op_pcode]['op_uid'] = $sv['op_uid'];
                $arr_pinfo[$op_pcode]['has_option'] = 'N';
                $arr_pinfo[$op_pcode]['price'] = $sv['op_price'];
                $arr_pinfo[$op_pcode]['app_cancel_btn'] = $app_cancel_btn;
                $arr_pinfo[$op_pcode]['app_vat_str'] = $app_vat_str;
            }

            $arr_pinfo[$op_pcode]['cnt'] += $sv['op_cnt'];
            $arr_pinfo[$op_pcode]['tprice'] += ($sv['op_cnt'] * $sv['op_price']);
            $arr_pinfo[$op_pcode]['point'] += $sv['op_point'];
            $arr_pinfo[$op_pcode]['delivery_type'] = $sv['op_delivery_type'];
            $arr_pinfo[$op_pcode]['delivery_price'] += $sv['op_delivery_price'];
            $arr_pinfo[$op_pcode]['add_delivery_price'] += $sv['op_add_delivery_price'];


            // 주문상품의 진행상태
            $arr_status[$op_pcode]['total']++;
            if($row['o_canceled'] == 'Y' || $sv['op_cancel'] == 'Y'){ // 주문자체가 취소이거나, 부분취소가 있다면
                $arr_status[$op_pcode]['cancel']++;
            }else if($sv['o_canceled'] == 'R'){ // 환불요청
                $arr_status[$op_pcode]['refund']++;
            }else if($row['o_status'] == '결제실패'){ // 결제실패일경우
                $arr_status[$op_pcode]['fail']++;
            }else{
                if($row['o_paystatus'] =='Y'){ // 주문결제를 했다면,
                    if($sv['op_sendstatus'] == '구매발주') {
                        $arr_status[$op_pcode]['pay']++;
                    }else if($sv['op_sendstatus'] == '배송준비'){
                        $arr_status[$op_pcode]['del_ready']++;
                    }else if($sv['op_sendstatus'] == '배송중'){
                        $arr_status[$op_pcode]['delivery']++;
                    }else if($sv['op_sendstatus'] == '배송완료'){
                        $arr_status[$op_pcode]['complete']++;
                    }else{
                        $arr_status[$op_pcode]['cancel']++;
                    }
                }else{ // 주문결제를 하지 않았다면
                    $arr_status[$op_pcode]['ready']++;
                }
            }

            # 배송조회
            if(in_array($sv['op_sendstatus'], array('배송중','배송완료')) && $sv['op_sendcompany'] && $sv['op_sendnum']){
                //if($arr_sendnum[$sv['op_sendnum']] > 0) continue; // 중복제거
                if($arr_sendnum[$sv['op_sendnum']] == 0){
                    $arr_sendnum[$sv['op_sendnum']]++;
                    $arr_pinfo[$op_pcode]['delivery_print'][] = '
                        <div class="lineup-vertical">
                            <span class="bold">'. $sv['op_sendcompany'] .'</span>
                            <span class="block">'. $sv['op_sendnum'] .'</span>
                            <a href="'. ($row['npay_order'] == 'Y' ? ($NPayCourier[$sv[op_sendcompany]]?$NPayCourier[$sv[op_sendcompany]]:$arr_delivery_company[$sv[op_sendcompany]]) : $arr_delivery_company[$sv['op_sendcompany']]) . rm_str($sv['op_sendnum']) .'" class="c_btn h22 green line h22 t4" target="_blank">배송조회</a>
                        </div>
                    ';
                }
            }

            //{{{혜택표기}}} -- 혜택에 대한 처리
            $arrBoonInfo =  array();
            // 혜택에 대한 처리 :: list1 -  회원 할인/추가적립
            if( $sv['op_groupset_use'] == 'Y' && $sv['op_groupset_price_per'] > 0 ){ $arrBoonInfo[] = '회원할인 '.odt_number_format($sv['op_groupset_price_per'],1).'%'; }
            if( $sv['op_groupset_use'] == 'Y' && $sv['op_groupset_point_per'] > 0 ){ $arrBoonInfo[] = '회원추가적립'.odt_number_format($sv['op_groupset_price_per'],1).'%'; }

            // 혜택에 대한 처리 :: list2 -  쿠폰적용여부
            $rowClChk = _MQ("select count(*) as cnt from smart_order_coupon_log where cl_oordernum = '".$sv['op_oordernum']."' and cl_pcode = '".$op_pcode."'   ");
            if( $rowClChk['cnt'] > 0){ $arrBoonInfo[] = '상품쿠폰사용 '.number_format($rowClChk['cnt']).'개';  }

            // 혜택에 대한 처리 :: list3 -  무료배송
            if( $sv['op_free_delivery_event_use'] == 'Y'){ $arrBoonInfo[] = '무료배송 이벤트'; }

            $arr_pinfo[$op_pcode]['boonInfo'] = count($arrBoonInfo) > 0 ? implode("<br>",$arrBoonInfo) : '';
            //{{{혜택표기}}}

        } //

        // 주문상품 진행상태 체크
        foreach($arr_status as $sk=>$sv){
            # 진행상태
            $op_status_icon = '';
            if($row['o_canceled'] == 'Y'){ // 주문자체가 취소 되었으면 주문취소 :: [접수대기, 접수완료, 배송중, 배송완료, 주문취소, 결제실패]
                $arr_pinfo[$sk]['status'] = '주문취소';
            }
            else if($sv['fail']>0){ // 결제실패가 하나라도 있으면 결제실패상태 :: [접수대기, 접수완료, 배송중, 배송완료, 주문취소, 결제실패] - [결제실패]
                $arr_pinfo[$sk]['status'] = '결제실패';
            }
            else if($sv['ready']>0){ // 접수대기가 하나라도 있으면 접수대기상태 :: [접수대기, 접수완료, 배송중, 배송완료, 주문취소] - [접수대기]
                $arr_pinfo[$sk]['status'] = '접수대기';
            }
            else if($sv['delivery']>0){ // 배송중이 하나라도 있으면 배송중상태 :: [접수완료, 배송중, 배송완료, 주문취소] - [배송중]
                $arr_pinfo[$sk]['status'] = '배송중';
            }
            else if($sv['del_ready']>0){ // 접수완료가 하나라도 있으면 접수완료상태 :: [접수완료, 배송완료, 주문취소] - [접수완료]
                $arr_pinfo[$sk]['status'] = '배송준비';
            }
            else if($sv['pay']>0){ // 접수완료가 하나라도 있으면 접수완료상태 :: [접수완료, 배송완료, 주문취소] - [접수완료]
                //$arr_pinfo[$sk]['status'] = '접수완료';
                $arr_pinfo[$sk]['status'] = '구매발주'; //=> 상세페이지에서는 구매발주로 표현
            }
            else if($sv['complete']>0){ // 배송완료가 하나라도 있으면 배송완료상태 :: [배송완료, 주문취소] - [배송완료]
                $arr_pinfo[$sk]['status'] = '배송완료';
            }else{ // 나머지는 주문취소  :: [주문취소] - [주문취소]
                $arr_pinfo[$sk]['status'] = '주문취소';
            }
        }

        # 상품쿠폰정보 추춘
        $pcoupon = _MQ_assoc(" select * from smart_order_coupon_log where cl_oordernum = '". $row['o_ordernum'] ."' and cl_type = 'product' ");
        if(count($pcoupon)>0){
            foreach($pcoupon as $k=>$v){
                $arr_pinfo[$v['cl_pcode']]['product_coupon'] = $v['cl_title'].'<br>'.number_format($v['cl_price']).'원';
            }
        }

    }else{ error_msg('잘못된 접근입니다.'); }


?>



<form name="frm" method="post" action="_order.pro.php" >
<input type="hidden" name="_ordernum" value='<?php echo $_ordernum; ?>'>
<input type="hidden" name="_mode" value="<?php echo $_mode; ?>">
<input type="hidden" name="view" value="<?php echo $view; ?>">
<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
<input type="hidden" name="_paymethod" value="<?php echo $row['o_paymethod']; ?>">

    <!-- ● 단락타이틀 -->
    <div class="group_title"><strong>상품정보</strong></div>



    <!-- ● 데이터 리스트 -->
    <div class="data_list">
        <table class="table_list">
            <colgroup>
                <col width="100"><col width="*"><col width="60"><col width="90"><col width="100"><col width="100"><col width="<?=count($arrBoonInfo) > 0 ? '140':'100'?>"><col width="60"><col width="120">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">이미지</th>
                    <th scope="col">상품정보</th>
                    <th scope="col">수량</th>
                    <th scope="col">적립금</th>
                    <th scope="col">주문금액</th>
                    <th scope="col">배송비</th>
                    <th scope="col">할인혜택</th>
                    <th scope="col">배송상태</th>
                    <th scope="col">배송정보</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $arr_pinfo as $k=>$v ){ ?>
                        <tr>
                            <td class="img80"><img src="<?php echo $v['img']; ?>" alt="<?php echo addslashes($v['name']); ?>"></td>
                            <td>
                                <!-- 상품정보 -->
                                <div class="order_item if_view">
                                    <!-- 상품명 -->
                                    <div class="title bold"><?php echo $v['name']; ?></div>
                                    <?php if($v['has_option']=='Y' && count($v['option'])>0){ ?>
                                        <!-- 옵션명, div반복 -->
                                        <?php foreach($v['option'] as $sk=>$sv){ ?>
                                            <div class="option bullet">
                                                <span class="option_name"><?php echo ($sv['is_addoption']=='Y'?'추가 : ':'선택 : '); ?><?php echo $sv['name']; ?></span>
                                                <span class="option_price"><?php echo number_format($sv['price']); ?>원 x <?php echo number_format($sv['cnt']); ?>개</span>
                                                <?php echo $sv['app_cancel_btn']; ?>
                                            </div>
                                        <?php } ?>
                                    <?php }else{ ?>
                                        <div class="option bullet">
                                            <span class="option_price no_option"><?php echo number_format($v['price']); ?>원 x <?php echo number_format($v['cnt']); ?>개</span>
                                            <?php echo $v['app_cancel_btn']; ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </td>
                            <td class="t_black"><?php echo number_format($v['cnt']); ?></td>
                            <td class="t_black"><?php echo number_format($v['point']); ?></td>
                            <td class="t_black bold"><?php echo number_format($v['tprice']); ?>원</td>
                            <td class="t_black bold">
                                <?php echo number_format($v['delivery_price']); ?>원
                                <?php if($v['delivery_type']<>'입점'){ ?>
                                    <br>(<?php echo $v['delivery_type']; ?>배송)
                                <?php } ?>

                                <?php if($v['add_delivery_price']>0){ ?>
                                    <div class="normal" style="margin-top:5px;">+<?php echo number_format($v['add_delivery_price']); ?>원<br>(추가배송비)</div>
                                <?php } ?>
                            </td>
                            <td class="t_orange bold">
                                <?php echo $v['boonInfo']; ?>
                            </td>
                            <td>
                                <div class="lineup-vertical">
                                    <?php echo $arr_adm_button[$v['status']]; ?>
                                </div>
                            </td>
                            <td>
                                <?php
                                    if(count($v['delivery_print'])>0) echo implode('<br>', $v['delivery_print']);
                                ?>
                            </td>
                        </tr>

            <?php
                        //상품수 , 포인트 , 상품금액
                        $arr_product['cnt'] += $v['cnt'];//상품수
                        $arr_product['point'] += $v['point'];//포인트
                        $arr_product['sum'] += $v['tprice'];//상품금액
                        $arr_product['add_delivery'] += $v['delivery_price'] + $v['add_delivery_price'];//개별배송비 포함
                    }
                ?>
            </tbody>
        </table>

        <!-- 결제금액정보 -->
        <div class="total_price">
            <div>
                <ul>
                    <li>주문상품 수 : <strong><?php echo number_format($arr_product['cnt']); ?></strong><em>개</em></li>
                    <li>적립금 : <strong><?php echo number_format($arr_product['point']); ?></strong><em>포인트</em></li>
                    <li>배송비 : <strong><?php echo number_format($arr_product['add_delivery']); ?></strong><em>원</em></li>
                    <li>주문총액 : <strong><?php echo number_format($arr_product['sum']); ?></strong><em>원</em></li>
                    <li>결제예정금액 : <strong><?php echo number_format($arr_product['sum'] + $arr_product['add_delivery']); ?></strong><em>원</em></li>
                </ul>
            </div>
        </div>
    </div>




    <!-- ● 단락타이틀 -->
    <div class="group_title"><strong>관리자 메모</strong></div>

    <!-- ●폼 영역 (검색/폼 공통으로 사용) -->
    <div class="data_form">
        <table class="table_form">
            <colgroup>
                <col width="180"><col width="*">
            </colgroup>
            <tbody>
                <tr>
                    <th>메모 내용</th>
                    <td>
                        <textarea name="_admcontent" rows="4" cols="" class="design"><?php echo $row['o_admcontent']; ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>




    <!-- ● 단락타이틀 -->
    <div class="group_title"><strong>주문 및 결제 정보</strong></div>

    <!-- ●폼 영역 (검색/폼 공통으로 사용) -->
    <div class="data_form">
        <table class="table_form">
            <colgroup>
                <col width="180"><col width="*"><col width="180"><col width="*">
            </colgroup>
            <tbody>
                <tr>
                    <th>주문번호</th>
                    <td class="only_text"><?php echo $row['o_ordernum']; ?></td>
                    <th>주문일시</th>
                    <td class="only_text"><?php echo date('Y-m-d', strtotime($row['o_rdate'])); ?> <span class="t_light"><?php echo date('H:i:s', strtotime($row['o_rdate'])); ?></span></td>
                </tr>
                <tr>
                    <th>주문정보</th>
                    <td colspan="3">
                        <table>
                            <colgroup>
                                <col width="130"><col width="*"><col width="130"><col width="*">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <th>주문금액</th>
                                    <td colspan="3" class="only_text">
                                        <span class="t_black bold"><?php echo number_format($row['o_price_total']); ?>원</span>(상품금액)
                                        + <span class="t_black bold"><?php echo number_format($row['o_price_delivery']); ?>원</span>(배송비)
                                        = <span class="t_black bold"><?php echo number_format($row['o_price_total']+$row['o_price_delivery']); ?>원</span>(합계금액)
                                    </td>
                                </tr>
                                <tr>
                                    <th>할인정보</th>
                                    <td colspan="3" class="only_text">
                                        <?php
                                            $order_discount_cnt = $order_discount_sum = 0;
                                            foreach($arr_order_discount_field as $cfk=>$cfv){

                                                echo ($order_discount_cnt == 0 ? NULL : ' + ');
                                                echo '<span class="t_black bold">' . number_format($row[$cfk]) . '원</span>('. $cfv .')';

                                                echo ( $cfk == 'o_price_coupon_individual' && $row['o_coupon_individual_uid'] ? '['.$row['o_coupon_individual_uid'].']' : NULL); // 보너스쿠폰사용액일 경우 추가
                                                echo ( $cfk == 'o_promotion_price' && $row['o_promotion_code'] ? '['.$row['o_promotion_code'].']' : NULL); // 프로모션코드할인금액일 경우 추가

                                                $order_discount_cnt ++;//순번
                                                $order_discount_sum += $row[$cfk];//합계
                                            }
                                        ?>
                                        = <span class="t_black bold"><?php echo number_format($order_discount_sum); ?>원</span>(합계금액)
                                    </td>
                                </tr>
                                <tr>
                                    <th>부분취소/환불액</th>
                                    <td colspan="3" class="only_text">
                                        <?php
                                            $order_cancel_cnt = $order_cancel_sum = 0;
                                            foreach($arr_order_cancel_field as $cfk=>$cfv){
                                                echo ($order_cancel_cnt == 0 ? NULL : ' + ');
                                                echo '<span class="t_black bold">' . number_format($row[$cfk]) . '원</span>('. $cfv .')';
                                                $order_cancel_cnt ++;//순번
                                                $order_cancel_sum += $row[$cfk];//합계
                                            }
                                        ?>
                                        = <span class="t_black bold"><?php echo number_format($order_cancel_sum); ?>원</span>(합계금액)<br>
                                        <div class="tip_box">
                                            <?php echo _DescStr('부분 취소 요청 및 완료된 주문상품의 환불 금액이 표시됩니다.'); // JJC : 부분취소 개선 : 2021-02-10 ?>
                                            <?php echo _DescStr('전체 취소시 취소 비용은 포함되지 않습니다.'); ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>계산금액</th>
                                    <td colspan="3" class="only_text">
                                        <span class="t_black bold"><?=number_format($row['o_price_total']+$row['o_price_delivery'] - $row['o_price_coupon_individual'] - $row['o_price_coupon_product'] - $row['o_price_usepoint'] - $row['o_promotion_price']); ?>원</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>실결제가</th>
                                    <td class="only_text">
                                        <span class="t_black bold"><?=number_format($row['o_price_real'])?>원</span>
                                    </td>
                                    <th>적립금</th>
                                    <td class="only_text"><span class="t_black bold"><span class="t_black bold"><?php echo number_format($row['o_price_supplypoint']); ?>포인트</span></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th>결제정보</th>
                    <td colspan="3">
                        <!-- 내부테이블 -->
                        <table>
                            <colgroup>
                                <col width="130"><col width="*"><col width="130"><col width="*">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <th>결제수단</th>
                                    <td colspan="3">
                                        <?php 
                                            // LCY : 2021-07-04 : 신용카드 간편결제 추가
                                            if( $row['o_easypay_paymethod_type'] != ''){ 
                                                echo $arr_adm_button["E".$arr_available_easypay_pg_list[$row['o_easypay_paymethod_type']]];
                                            }else{
                                                echo $arr_adm_button[$arr_payment_type[$row['o_paymethod']]]; 
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>주문상태</th>
                                    <td>
                                        <?php echo str_replace(array('h22','t4'), array('h27', ''), ($row['o_status']?$arr_adm_button[$row['o_status']]:$arr_adm_button['결제실패'])); ?>

                                        <?php if($row['o_status'] == '접수대기' && $row['o_canceled']=='N' && $row['o_paystatus']=='N'){ ?>
                                            <a href="#none" onclick="if(confirm('입금확인 처리하시겠습니까?')){ document.location.href = '_order.pro.php<?php echo URI_Rebuild('?', array('view'=>$view, '_mode'=>'payconfirm', '_ordernum'=>$_ordernum, '_PVSC'=>$_PVSC)); ?>'; } return false;" class="c_btn h27 line bold">입금확인</a>
                                            <div class="tip_box">
                                                <?php echo _DescStr('접수완료가 되지 않음에 따라 적립금 사용/지급, 쿠폰사용 등이 적용되지 않은 상태입니다.'); ?>
                                                <?php echo _DescStr('입금확인 시 주문상태가 <em>접수완료</em>상태로 변경됩니다.'); ?>
                                                <?php echo _DescStr('입금취소는 결제수단이 <span class="preview_icon"><em>무통장입금</em><span class="ov" style="width:250px"><strong>※ 입금확인은 PG사와 연동되지 않습니다.</strong><br> ex) 카드결제일 경우 <em>입금확인</em>하더라도<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;카드결제가 이루어지지는 않습니다.</span></span>일 경우 사용하시기 바랍니다.'); ?>
                                                <?php echo _DescStr('배송상태의 변경은 <a href="_order_delivery.list.php" target="_blank"><em>배송주문관리</em></a>메뉴를 이용해 주시기 바랍니다.'); ?>
                                            </div>
                                        <?php }else if(in_array($row['o_status'], array('접수완료', '구매발주')) && $row['o_canceled']=='N' && $row['o_paystatus']=='Y'){ ?>
                                            <a href="#none" onclick="if(confirm('입금취소 처리하시겠습니까?')){ document.location.href = '_order.pro.php<?php echo URI_Rebuild('?', array('view'=>$view, '_mode'=>'paycancel', '_ordernum'=>$_ordernum, '_PVSC'=>$_PVSC)); ?>'; } return false;" class="c_btn h27 line bold">입금취소</a>
                                            <div class="tip_box">
                                                <?php echo _DescStr('접수완료에 따라 적립금 사용/지급, 쿠폰사용 등이 적용된 상태입니다.'); ?>
                                                <?php echo _DescStr('입금취소 시 주문상태가 <em>접수대기</em>상태로 변경됩니다.'); ?>
                                                <?php echo _DescStr('입금취소는 결제수단이 <span class="preview_icon"><em>무통장입금</em><span class="ov" style="width:250px"><strong>※ 입금취소는 PG사와 연동되지 않습니다.</strong><br> ex) 카드결제일 경우 <em>입금취소</em>하더라도<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;카드결제 내역이 취소되지는 않습니다.</span></span>일 경우 사용하시기 바랍니다.'); ?>
                                                <?php echo _DescStr('배송상태의 변경은 <a href="_order_delivery.list.php" target="_blank"><em>배송주문관리</em></a>메뉴를 이용해 주시기 바랍니다.'); ?>
                                            </div>
                                        <?php }else if($row['o_canceled']=='N' && $row['o_paystatus']=='Y'){ ?>
                                            <a href="#none" onclick="alert('배송이 진행된 주문은 입금취소할 수 없습니다.'); return false;" class="c_btn h27 dark bold">입금취소</a>
                                            <div class="tip_box">
                                                <?php echo _DescStr('접수완료에 따라 적립금 사용/지급, 쿠폰사용 등이 적용된 상태입니다.'); ?>
                                                <?php echo _DescStr('배송이 진행된 주문은 입금취소할 수 없습니다.'); ?>
                                                <?php echo _DescStr('배송상태의 변경은 <a href="_order_delivery.list.php" target="_blank"><em>배송주문관리</em></a>메뉴를 이용해 주시기 바랍니다.'); ?>
                                            </div>
                                        <?php } ?>
                                    </td>
                                    <th>강제취소</th>
                                    <td>
                                        <?php if($row['o_canceled']<>'Y'){ ?>
                                            <a href="#none" class="c_btn h27 black line dark bold" onclick="if(confirm('PG관리자에서 직접 결제를 취소하였거나\n\n일부 오류로 강제 취소를 하실경우 사용 바랍니다.\n\n계속하시겠습니까?')){ document.location.href = '_order.pro.php<?php echo URI_Rebuild('?', array('view'=>$view, '_mode'=>'cancel', 'force_cancel'=>'1', '_ordernum'=>$_ordernum, '_PVSC'=>$_PVSC)); ?>'; }">강제취소</a>
                                            <div class="tip_box">
                                                <?php echo _DescStr('주문취소는 주문목록의 <em>주문취소</em>버튼을 사용하시기 바랍니다'); ?>
                                                <?php echo _DescStr('강제취소 하였을 경우 PG사의 결제내역은 직접 취소 하여야 합니다.'); ?>
                                                <?php echo _DescStr('PG관리자에서 직접 결제를 취소하였을 경우 사용하시기 바랍니다.', 'black'); ?>
                                                <?php echo _DescStr('일부 오류로 강제 취소를 하실경우 사용하시기 바랍니다.', 'black'); ?>
                                            </div>
                                        <?php }else{ ?>
                                            <a href="#none" class="c_btn h27 black dark bold" onclick="alert('이미 취소된 주문입니다.'); return false;">강제취소</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                                    if( in_array($row['o_paymethod'], $arr_cash_payment_type) ) { // SSJ : 주문/결제 통합 패치 : 2021-02-24
                                        // 가상계좌 발급 내역
                                        if($row['o_paymethod'] == 'virtual'){
                                            $virtual_log = _MQ("select ool_account_num, ool_bank_name, ool_deposit_name, ool_bank_owner from smart_order_onlinelog where ool_ordernum='$_ordernum' and ool_type='R' order by ool_uid desc limit 1");
                                            $row['o_bank'] = '['.$virtual_log['ool_bank_name'].'] ' . $virtual_log['ool_account_num'] . ($virtual_log['ool_bank_owner']?', '.$virtual_log['ool_bank_owner']:null);
                                            $row['o_deposit'] = $virtual_log['ool_deposit_name'];
                                        }

                                        // 현금영수증 발행 정보 추출
                                        $cashbill = _MQ("select * from smart_baro_cashbill where bc_ordernum = '{$_ordernum}' and bc_iscancel = 'N' and bc_isdelete = 'N' and BarobillState in (1000,2000,3000) order by bc_uid desc limit 1");
                                ?>
                                    <tr>
                                        <?php if($row['o_paymethod'] <> 'iche'){ ?>
                                        <th><?php echo ($row['o_paymethod'] == 'online' ? '무통장' : '가상계좌'); ?>입금정보</th>
                                        <td>
                                            <div class="lineup-resposive">
                                                <input type="text" name="_bank" class="design" style="width:285px" value="<?php echo $row['o_bank']; ?>">
                                                <div class="fr_box">
                                                    <span class="fr_bullet">입금자명</span>
                                                    <input type="text" name="_deposit" class="design" style="width:80px" value="<?php echo $row['o_deposit']; ?>">
                                                </div>
                                            </div>
                                        </td>
                                        <?php } ?>
                                        <th>현금영수증 신청</th>
                                        <td<?php echo ($row['o_paymethod'] == 'iche' ? ' colspan="3"' : null); ?>>
                                            <div class="lineup-resposive">
                                                <label class="design"><input type="checkbox" name="_get_tax" id="js_get_tax" value="Y" <?php echo ($row['o_get_tax'] == 'Y' ? 'checked' : null);?> >발행신청</label>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr class="js_get_tax_form" style="<?php echo ($row['o_get_tax']=='Y' && $row['o_paymethod']=='online' ? null : 'display:none;'); ?>">
                                        <th>현금영수증 신청정보</th>
                                        <td colspan="3">

                                                <span class="fr_bullet">거래용도 : </span>
                                                <label class="design"><input type="radio" id="_tax_TradeUsage1" name="_tax_TradeUsage" value="1" <?php echo ($row['o_tax_TradeUsage']<>'2' ? ' checked' : null); ?>>소득공제(주민번호/휴대폰/카드번호)</label>
                                                <label class="design"><input type="radio" id="_tax_TradeUsage2" name="_tax_TradeUsage" value="2" <?php echo ($row['o_tax_TradeUsage']=='2' ? ' checked' : null); ?>>지출증빙(사업자번호)</label>

                                                <div class="dash_line"><!-- 점선라인 --></div>
                                                <span class="fr_bullet">신분확인번호 구분 : </span>
                                                <label class="design">
                                                    <input type="radio" id="js_tradeMethod1" name="_tax_TradeMethod" value="1"
                                                        <?php echo ($row['o_tax_TradeMethod']=='1' ? ' checked' : null); ?>
                                                        <?php echo ($row['o_tax_TradeUsage']=='2' ? ' disabled' : null); ?>
                                                        >카드번호(국세청에 등록된 카드번호만 가능)
                                                </label>
                                                <!-- <label class="design">
                                                    <input type="radio" id="js_tradeMethod3" name="_tax_TradeMethod" value="3"
                                                        <?php echo ($row['o_tax_TradeMethod']=='3' ? ' checked' : null); ?>
                                                        <?php echo ($row['o_tax_TradeUsage']=='2' ? ' disabled' : null); ?>
                                                        >주민등록번호
                                                </label> -->
                                                <label class="design">
                                                    <input type="radio" id="js_tradeMethod5" name="_tax_TradeMethod" value="5"
                                                        <?php echo ($row['o_tax_TradeMethod']=='5' || $row['o_tax_TradeUsage']=='' ? ' checked' : null); ?>
                                                        <?php echo ($row['o_tax_TradeUsage']=='2' ? ' disabled' : null); ?>
                                                        >휴대폰번호
                                                </label>
                                                <label class="design">
                                                    <input type="radio" id="js_tradeMethod4" name="_tax_TradeMethod" value="4"
                                                        <?php echo ($row['o_tax_TradeMethod']=='4' ? ' checked' : null); ?>
                                                        <?php echo ($row['o_tax_TradeUsage']<>'2' ? ' disabled' : null); ?>
                                                        >사업자번호
                                                </label>

                                                <div class="dash_line"><!-- 점선라인 --></div>
                                                <span class="fr_bullet">신분확인번호 : </span>
                                                <input type="text" name="_tax_IdentityNum" class="design js_number_valid" style="width:120px" value="<?php echo onedaynet_decode($row['o_tax_IdentityNum']); ?>">
                                                <input type="hidden" name="_identitynum_valid" value="" /><!-- 신분확인번호 유효성체크 -->

                                        </td>
                                    </tr>

                                    <?php if(in_array($row['o_paymethod'], $arr_cash_payment_type) && $row['o_get_tax']=='Y'){ // SSJ : 주문/결제 통합 패치 : 2021-02-24 ?>
                                    <tr>
                                        <th>현금영수증 발행정보</th>
                                        <td colspan="3">
                                            <?php
                                                // 현금영수증 발행 내역이 있다면
                                                if(sizeof($cashbill) > 0){
                                            ?>
                                                <span class="fr_bullet normal">발행상태 : <?php echo ($cashbill['BarobillState']=='1000' ? '<span class="t_sky">임시저장</span>' : '<span class="t_green">발행완료</span>'); ?></span>
                                                <?php if($cashbill['BarobillState']=='1000'){ ?>
                                                    <span class="fr_bullet normal">접수일 : <?php echo date('Y-m-d h:i',strtotime($cashbill['RegistDT'])); ?></span>
                                                <?php }else{ ?>
                                                    <span class="fr_bullet normal">발행일 : <?php echo date('Y-m-d h:i',strtotime($cashbill['IssueDT'])); ?></span>
                                                <?php } ?>
                                                <?php if($cashbill['NTSConfirmNum']){ ?><span class="fr_bullet normal">승인번호 : <?php echo $cashbill['NTSConfirmNum']; ?></span><?php } ?>
                                                <?php if($cashbill['IdentityNum']){ ?><span class="fr_bullet normal">신분확인번호 : <?php echo $cashbill['IdentityNum']; ?></span><?php } ?>
                                                <span class="fr_bullet normal">발행금액 : <?php echo number_format($cashbill['Amount']); ?></span>
                                            <?php
                                                }else{
                                                    // 현금영수증 대상 금액 추출
                                                    //ViewArr($row);
                                                    //ViewArr($sres);

                                            ?>
                                                <a href="/totalAdmin/_cashbill.form.php?_state=issue&_ordernum=<?php echo $row['o_ordernum']; ?>" class="c_btn h27 line bold" target="_blank">현금영수증 발행</a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php } ?>

                                <?php } ?>

                            </tbody>
                        </table>
                        <!-- / 내부테이블 -->
                    </td>
                </tr>

                <?php
                    // 환불요청이 있을 경우
                    if( $row['o_moneyback_status'] <> 'none' ){
                ?>
                <tr>
                    <th>환불요청정보</th>
                    <td colspan="3">

                        <table>
                            <colgroup>
                                <col width="180"><col width="*"><col width="180"><col width="*">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <th>환불처리상태</th>
                                    <td colspan="3" class="only_text">
                                        <?php echo $row['o_moneyback_status'] == "complete" ? "환불완료" : "환불신청중"; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>환불계좌</th>
                                    <td colspan="3" class="only_text">
                                        <!-- SSJ : 주문/결제 통합 패치 : 2021-02-24 -->
                                        <input type="text" name="_moneyback_comment" value="<?php echo trim(stripslashes(str_replace("환불계좌:","",$row['o_moneyback_comment']))); ?>" class="design" style="width:285px">
                                    </td>
                                </tr>
                                <tr>
                                    <th>환불요청시간</th>
                                    <td colspan="3" class="only_text">
                                        <?php echo $row['o_moneyback_date']; ?>
                                    </td>
                                </tr>
                                <?php
                                    // 환불요청이 완료된 경우
                                    if( $row['o_moneyback_status'] == 'complete'){
                                ?>
                                <tr>
                                    <th>환불처리시간</th>
                                    <td colspan="3" class="only_text">
                                        <?php echo $row['o_moneyback_comdate']; ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                    </td>
                </tr>
                <?php
                    }
                ?>

                <?php
                // SSJ 2018-05-16 :: 주문취소정보 노출 ---{
                if($row['o_canceled'] == 'Y'){
                    // 환불정보 - 부분취소정보
                    $arrCinfo = array();
                    foreach($sres as $sk=>$sv){
                        // 환불정보
                        if($sv['op_complain'] <> ''){
                            $arrCinfo[strtotime($sv['op_complain_date'])] = '[' . $sv['op_complain_date'] . '] 교환/반품신청('. $sv['op_pname'] .') - ' . $sv['op_complain'];
                        }

                        // 부분취소정보
                        if($sv['op_cancel'] <> 'N'){
                            $arrCinfo[strtotime($sv['op_cancel_rdate'])] = '[' . $sv['op_cancel_rdate'] . '] 부분취소 요청('. $sv['op_pname'] .') - ' . ($sv['op_cancel_mem_type']<>'admin'?'회원취소':'운영자취소');
                            $arrCinfo[strtotime($sv['op_cancel_cdate'])] = '[' . $sv['op_cancel_cdate'] . '] 부분취소 완료('. $sv['op_pname'] .') - 운영자취소';
                        }

                    }
                    ksort($arrCinfo);
                ?>
                <tr>
                    <th>취소정보</th>
                    <td colspan="3">
                        <?php if(count($arrCinfo) > 0){ ?>
                            <?php echo implode('<br>', $arrCinfo); ?><br>
                        <?php } ?>
                        [<?php echo $row['o_canceldate']; ?>] 주문취소 - <?php echo ($row['o_cancel_mem_type']<>'admin'?'회원취소':'운영자취소'); ?>
                    </td>
                </tr>
                <?php
                }
                //--- SSJ 2018-05-16 :: 주문취소정보 노출
                ?>
            </tbody>
        </table>
    </div>



    <!-- ● 단락타이틀 -->
    <div class="group_title"><strong>주문자 정보</strong></div>



    <!-- ●폼 영역 (검색/폼 공통으로 사용) -->
    <div class="data_form">
        <table class="table_form">
            <colgroup>
                <col width="180"><col width="*"><col width="180"><col width="*">
            </colgroup>
            <tbody>
                <tr>
                    <th>회원타입</th>
                    <td>
                        <?=_InputRadio( "_memtype" , array('Y','N'), $row['o_memtype'] , '' , array('회원','비회원') , '') ?>
                    </td>
                    <th>주문자 아이디</th>
                    <td>
                        <input type="text" name="_mid" value="<?=$row['o_mid']?>" class="design" style="width:185px">
                    </td>
                </tr>
                <tr>
                    <th>주문자명</th>
                    <td>
                        <input type="text" name="_oname" value="<?=$row['o_oname']?>" class="design" style="width:100px">
                    </td>
                    <th>휴대폰번호</th>
                    <td>
                        <input type="text" name="_ohp" value="<?php echo tel_format($row['o_ohp']); ?>" class="design t_center" style="width:110px">
                    </td>
                </tr>
                <tr>
                    <th>주문자 이메일 주소</th>
                    <td colspan="3">
                        <input type="text" name="_oemail" value="<?php echo $row['o_oemail']; ?>" class="design" style="width:185px">
                    </td>
                </tr>

                <tr>
                    <th>주문자 기기정보</th>
                    <td colspan="3">
                        <textarea cols="30" rows="4" class="design" readonly><?php echo $row['device_info']; ?>
                        </textarea>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>


    <!-- ● 단락타이틀 -->
    <div class="group_title"><strong>받는 분 정보</strong></div>




    <!-- ●폼 영역 (검색/폼 공통으로 사용) -->
    <div class="data_form">
        <table class="table_form">
            <colgroup>
                <col width="180"><col width="*"><col width="180"><col width="*">
            </colgroup>
            <tbody>
                <tr>
                    <th>받는 분 이름</th>
                    <td><input type="text" name="_rname" value="<?php echo $row['o_rname']; ?>" class="design" style="width:100px"></td>
                    <th>휴대폰번호</th>
                    <td>
                        <input type="text" name="_rhp" value="<?php echo tel_format($row['o_rhp']); ?>" class="design t_center" style="width:110px">
                    </td>
                </tr>
                <?php // ----- SSJ : 관리자 지번주소 내부패치 : 2020-04-27 -----?>
                <tr>
                    <th>배송지 주소</th>
                    <td>
                        <input type="text" name="_rzonecode" id="_zonecode" value="<?php echo $row['o_rzonecode']; ?>" class="design t_center" style="width:70px" readonly="readonly">

                        <a href="#none" onclick="new_post_view(); return false;" class="c_btn h28 black">우편번호 찾기</a>
                        <div class="lineup-full">
                            <input type="text" name="_raddr_doro" id="_addr_doro" value="<?php echo $row['o_raddr_doro']; ?>" class="design" readonly="readonly">
                            <input type="text" name="_raddr2" id="_addr2" class="design" style="" value="<?php echo $row['o_raddr2']; ?>">
                        </div>
                    </td>
                    <th>지번 주소</th>
                    <td>
                        <?php
                            // 배송지 우편번호
                            $arr_post = explode('-', $row['o_rpost']);
                        ?>
                        <input type="hidden" name="_rpost1" id="_post1" value="<?php echo $arr_post[0]; ?>" class="design t_center" style="width:50px" readonly="readonly">
                        <input type="hidden" name="_rpost2" id="_post2" value="<?php echo $arr_post[1]; ?>" class="design t_center" style="width:50px" readonly="readonly">
                        <div class="lineup-full">
                            <input type="text" name="_raddr1" id="_addr1" class="design" style="" value="<?php echo $row['o_raddr1']; ?>" readonly="readonly">
                        </div>
                    </td>
                </tr>
                <?php // ----- SSJ : 관리자 지번주소 내부패치 : 2020-04-27 -----?>
                <tr>
                    <th>배송시 유의사항</th>
                    <td colspan="3">
                        <textarea name="_content" rows="4" cols="" class="design"><?php echo $row['o_content']; ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>






    <?php
        $clque = "select * from smart_order_cardlog where oc_oordernum= '".$_ordernum."' and oc_tid !='' ";
        $clr = _MQ($clque);
        if(sizeof($clr) > 0 ) {
            $ex = explode("§§" , $clr['oc_content']);
    ?>
    <!-- ● 단락타이틀 -->
    <div class="group_title"><strong>결제기록(카드 / 계좌이체 기록)</strong></div>

    <!-- ●폼 영역 (검색/폼 공통으로 사용) -->
    <div class="data_form">
        <table class="table_form">
            <colgroup>
                <col width="180"><col width="*">
            </colgroup>
            <tbody>
                <tr>
                    <th>결제인증번호</th>
                    <td><?php echo $clr['oc_tid']; ?></td>
                </tr>
                <?php
                    foreach($ex as $k=>$v){
                        $ex2 = explode("||" , $v);
                        if(!$ex2[1]) continue;
                ?>
                        <tr>
                            <th><?php echo $ex2[0]; ?></th>
                            <td><?php echo $ex2[1]; ?></td>
                        </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>

    <!-- 상세페이지 버튼 -->
    <?php echo _submitBTN($app_current_link_list); ?>

</form>





<!-- ●●●●●●●●●● 부분취소신청 (티플형) LMH001 -->
<div class="popup" id="product_cancel_pop" style="display:none;width:640px;background:#fff;">

    <!--  레이어팝업 공통타이틀 영역 -->
    <div class="pop_title">부분취소/환불 신청<a href="#none" onclick="return false;" class="close btn_close" title="닫기"></a></div>

    <!-- 설명글 -->
    <div class="tip_box">
        <div class="c_tip">부분 취소할 상품을 꼭 다시한번 확인하시고, 다음 정보를 입력해주시면 관리자의 확인 후 처리됩니다.</div>
        <?php if( in_array($row['o_paymethod'],$arr_refund_payment_type) ) { // SSJ : 주문/결제 통합 패치 : 2021-02-24 ?>
            <!-- 가상계좌 / 직접환불 안내문구 -->
            <?php if( in_array($row['o_paymethod'],array('virtual')) ) { ?>
            <div class="c_tip"><em>가상계좌</em>의 부분취소는 지원하지 않습니다.</div>
            <?php } ?>
            <div class="c_tip"><em>직접환불</em>은 PG사와 연동되지 않습니다. 고객님의 계좌로 직접 환불처리 후 취소처리 해주시기 바랍니다.</div>
        <?php } ?>
        <?php if($row['o_paycancel_method'] =='D' ){ // 환불 방식이 분배이면 설명 (부분취소 kms 2019-03-20)
                echo _DescStr("상품 금액에서 할인금액이 상품 금액 비율로 분배되어 환불됩니다. ");
                echo _DescStr("할인금액이 있으면 상품 금액에서 제외되고 환불됩니다.");
                echo _DescStr("적립금 사용 내역은 마이페이지에 적립금 탭에서 확인하실 수 있습니다.");
            }else{
                echo _DescStr("마지막 상품을 취소할 때 상품 금액에서 할인금액이 제외되고 환불됩니다.");
                echo _DescStr("할인금액이 있으면 상품 금액에서 제외되고 환불됩니다.");
                echo _DescStr("적립금 사용 내역은 마이페이지에 적립금 탭에서 확인하실 수 있습니다.");
            }
        ?>
    </div>
    <div class="dash_line"><!-- 점선라인 --></div>

    <div class="group_title"><strong>상품정보</strong></div>

    <!-- 하얀색박스공간 -->
    <div class="data_list">

        <table class="table_list">
            <colgroup>
                <col width="115"><col width="*"><col width="120"><!-- <col width="85"><col width="80"> -->
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">이미지</th>
                    <th scope="col">상품 및 옵션 정보</th>
                    <th scope="col">금액 / 배송비</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="img80"><a href="#none" onclick="return false;"><img class="product_thumb" src="" alt="" /></a></td>
                    <td>
                        <!-- 상품정보 -->
                        <div class="order_item">
                            <!-- 상품명 -->
                            <div class="title bold product_name"></div>
                            <div class="option bullet">
                                <span class="option_name product_option"></span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="t_black bold product_price">0</span><br>
                        <div class="dash_line"><!-- 점선라인 --></div>
                        <span class="t_black normal delivery_price">0</span><br>

                        <div style="display:none;">
                            <span class="t_black normal discount_price">0</span><br>
                            <span class="t_black normal return_price">0</span><br>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>

    <br>


    <div class="group_title"><strong>환불정보</strong></div>

    <!-- 하얀색박스공간 -->
    <div class="data_form">

        <form name="product_cancel">
        <input type="hidden" name="mode" value="cancel"/><input type="hidden" name="ordernum" value=""/><input type="hidden" name="op_uid" value=""/><input type="hidden" name="cancel_mem_type" value="admin"/>

            <table class="table_form">
                <colgroup>
                    <col width="115"><col width="*">
                </colgroup>
                <tbody>
                    <tr>
                        <th class="ess"><span class="tit ">환불수단</span></th>
                        <td>
                            <?php if(in_array($siteInfo['s_pg_type'],array_keys($arr_pg_type))) { // SSJ : 주문/결제 통합 패치 : 2021-02-24 ?>
                                <?php if( in_array($row['o_paymethod'],$arr_refund_payment_type) ) { // SSJ : 주문/결제 통합 패치 : 2021-02-24 ?>
                                    <label class="design"><input type="radio" name="cancel_type" class="cancel_type_pg" checked value="pg"/>직접 환불</label>
                                <?php }else{ ?>
                                    <label class="design"><input type="radio" name="cancel_type" class="cancel_type_pg" checked value="pg"/>PG사 직접 취소</label>
                                <?php } ?>
                            <?php } ?>
                            <?php if($row['o_memtype'] == 'Y'){ // SSJ : 비회원 주문 취소 요청 시 적립금 환불 막기 : 2021-06-04 ?>
                                <label class="design"><input type="radio" name="cancel_type" class="cancel_type_point" value="point"/>적립금 환불</label>
                            <?php } ?>
                        </td>
                    </tr>

                    <?php // --- JJC : 부분취소 개선 : 2021-02-10  --- ?>
                    <tr>
                        <th class=""><span class="tit ">환불가능액</span></th>
                        <td>
                            <span class="t_black normal">직접 환불 가능액 : <span class="cancel_return_price">0</span>원(배송비 포함)</span><br>
                            <div class="dash_line"><!-- 점선라인 --></div>
                            <span class="t_black normal">적립금 환불 가능액 : <span class="cancel_return_point">0</span>원</span><br>
                            <div class="dash_line"><!-- 점선라인 --></div>
                             <span class="t_black normal">할인액 : <span class="cancel_return_discount">0</span>원(환불불가)</span><br>
                        </td>
                    </tr>
                    <?php // --- JJC : 부분취소 개선 : 2021-02-10  --- ?>

                    <?php if( in_array($row['o_paymethod'],$arr_refund_payment_type) ) { // SSJ : 주문/결제 통합 패치 : 2021-02-24 ?>
                    <tr class="view_pg">
                        <th class="ess"><span class="tit ">환불계좌</span></th>
                        <td>
                            <input type="text" name="cancel_bank_name" class="design icon_name" value="<?=$_member['in_cancel_bank_name']?>" placeholder="예금주" style="width:140px;"/>
                            <select name="cancel_bank" class="design" style="width:170px;">
                                <?php foreach($ksnet_bank as $kk=>$vv) { ?>
                                <option value="<?php echo $kk; ?>" <?php echo ($_member['in_cancel_bank']==$kk?'selected':''); ?>><?php echo $vv; ?></option>
                                <?php } ?>
                            </select>
                            <div class="clear_both"><!-- 점선라인 --></div>
                            <input type="text" name="cancel_bank_account" class="design icon_bank" value="<?=$_member['in_cancel_bank_account']?>" placeholder="계좌번호" style="width:315px;"/>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th class=""><span class="tit ">전달내용</span></th>
                        <td >
                            <textarea name="cancel_msg" rows="3" style="" class="design" placeholder="관리자에게 전달하실 내용이 있다면 입력해주세요."></textarea>
                            <div class="c_tip">위 정보를 다시한번 정확하게 확인 후 신청해주시면, 관리자 확인 후 처리됩니다.</div>
                        </td>
                    </tr>
                </tbody>
            </table>


            <!-- 레이어팝업 버튼공간 -->
            <div class="c_btnbox">
                <ul>
                    <li><span class="c_btn h34 black"><input type="submit" name="" value="취소신청" ></span></li>
                    <li><a href="#none" onclick="return false;" class="c_btn h34 black line close" >닫기</a></li>
                </ul>
            </div>
            <!-- / 레이어팝업 버튼공간 -->

        </form>

    </div>
    <!-- / 하얀색박스공간 -->

</div>


<?php
    // 우편번호 찾기
    include_once(OD_ADDONS_ROOT.'/newpost/newpost.search.php')
?>

<script>
$(document).ready(function(){

    // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC  -----------
    $('input[name=cancel_type]').on('change',function(){
        var type = $(this).val();
        if( type=='pg' ) { $('.view_pg').show(); } else { $('.view_pg').hide(); }
    });
    // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC  -----------

    $('.product_cancel').on('click',function(){
        var ordernum = $(this).data('ordernum'), op_uid = $(this).data('opuid'), $product_pop = $('#product_cancel_pop'), $product_form = $('form[name=product_cancel]');
        $.ajax({
            data: {'ordernum': ordernum, 'op_uid': op_uid, 'mode': 'product'},
            type: 'POST', dataType: 'JSON', cache: false,
            url: '<?php echo OD_PROGRAM_URL; ?>/mypage.order.view.ajax.php',
            success: function(data) {
                if(data['result']=='OK'){

                    $product_pop.find('.product_thumb').attr('src',data['data']['image']);
                    $product_pop.find('.product_name').text(data['data']['name']);

                    // --- JJC : 부분취소 개선 : 2021-02-10  ---
                    // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC
                    $product_pop.find('.product_price').text(data['data']['price']);//상품금액
                    $product_pop.find('.delivery_price').text(data['data']['delivery']);//배송비용
                    $product_pop.find('.discount_price').text(data['data']['discount']);//할인비용
                    // 2016-11-30 ::: 부분취소 - 할인비용 항목 추가 ::: JJC

                    $product_pop.find('.return_price').text(data['data']['return_price']);//환불금액
                    $product_pop.find('.cancel_return_price').text(data['data']['return_price']);//직접 환불 가능액
                    $product_pop.find('.cancel_return_point').text(data['data']['return_point']);//적립금 환불 가능액
                    $product_pop.find('.cancel_return_discount').text(data['data']['return_discount']);//할인금액
                    // --- JJC : 부분취소 개선 : 2021-02-10  ---

                    if(data['data']['option']) {
                        $product_pop.find('.product_option').text('옵션: ' + data['data']['option']);
                        if(data['data']['addoption']) {
                            $product_pop.find('.product_option').append('<br/>추가옵션: '+data['data']['addoption']);
                        }
                    } else { $product_pop.find('.product_option').text(''); }
                    $product_form.find('input[name=ordernum]').val(ordernum);
                    $product_form.find('input[name=op_uid]').val(op_uid);
                    if(data['data']['pg_check']=='N') {
                        $('input[name=cancel_type].cancel_type_pg').parent().hide();
                        $('input[name=cancel_type].cancel_type_pg').prop('disabled',true);
                        $('input[name=cancel_type].cancel_type_point').prop('checked',true).trigger('change');
                    }
                    $('#product_cancel_pop').lightbox_me({
                        centered: true, closeEsc: false,
                        onLoad: function() { },
                        onClose: function(){
                            $product_form.find('input[name=ordernum]').val('');
                            $product_form.find('input[name=op_uid]').val('');
                        }
                    });
                }
                else {alert(data['result_text']);}
            },
            error:function(request,status,error){alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);}
        });
    });
    // --- JJC : 부분취소 개선 : 2021-02-10  ---
    $('form[name=product_cancel]').on('submit',function(e){ e.preventDefault();
        if(confirm("정말 주문을 취소하시겠습니까?")===true) {
            var data = $(this).serialize();
            $.ajax({
                data: data, type: 'POST', dataType: 'JSON', cache: false,
                url: '<?php echo OD_PROGRAM_URL; ?>/mypage.order.view.ajax.php',
                success: function(data) {
                    if(data['result']=='OK'){alert('성공적으로 취소요청 되었습니다.'); location.reload(); return false;}
                    else {alert(data['result_text']);}
                },
                error:function(request,status,error){
                    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
                }
            });
        }
    });
    // --- JJC : 부분취소 개선 : 2021-02-10  ---
});




// - 현금영수증 발행신청시 신청항목 입력폼 노출 ----
$('#js_get_tax, input[name=_paymethod]').on('click',function(){
        var _trigger = ($('#js_get_tax').prop('checked') && <?php echo ($row['o_paymethod']=='online' ? 'true' : 'false'); ?> ? true : false); // 현금영수증 신청체크 && 무통장체크 모두 만족할때
        if(_trigger){
            $('.js_get_tax_form').show();// 현금영수증 신청폼 보임
        }else{
            $('.js_get_tax_form').hide();// 현금영수증 신청폼 숨김
        }
});
// - 현금영수증 지출증빙일때는 사업자번호만 선택가능 ---
$('input[name=_tax_TradeUsage]').on('change', function(){
    var _val = $(this).val();

    // 소득공제일때
    if(_val == '1'){
        $('input[name=_tax_TradeMethod]').prop('disabled', false);

        $('#js_tradeMethod5').prop('checked', true); // 기본선택 휴대폰번호
        $('#js_tradeMethod4').prop('disabled', true); // 사업자번호 선택불가
    }
    // 지출증빙일때
    else if(_val=='2'){
        $('input[name=_tax_TradeMethod]').prop('disabled', true);

        $('#js_tradeMethod4').prop('disabled', false); // 사업자번호 선택가능
        $('#js_tradeMethod4').prop('checked', true); // 기본선택 사압자번호
    }
    $('.js_number_valid').trigger('change');
});

$('input[name=_tax_TradeMethod]').on('change', function(){
    $('input[name=_tax_IdentityNum]').val('');
    $('input[name=_identitynum_valid]').val('');
});

// 신분확인번호 유효성체크----
$(document).delegate('.js_number_valid', 'change', function(){
    var _type = $('input[name=_tax_TradeMethod]:checked').val() + '';
    var _val = $(this).val();
    //alert(_type);
    if(_type != undefined && _val.replace(' ','') != ''){
        var result = validate_number(_type,_val);
        if(result === false){
            var msg = '';
            if(_type == '1'){
                //카드 번호가 유효한지 검사
                msg = '잘못된 카드번호 입니다. 확인 후 다시 입력해주시기 바랍니다.';
            }
            else if(_type == '3'){
                //주민등록 번호가 유효한지 검사
                msg = '잘못된 주민등록번호 입니다. 확인 후 다시 입력해주시기 바랍니다.';
            }
            else if(_type == '4'){
                //사업자등록 번호가 유효한지 검사
                msg = '잘못된 사업자번호 입니다. 확인 후 다시 입력해주시기 바랍니다.';
            }
            else if(_type == '5'){
                //휴대폰 번호가 유효한지 검사
                msg = '잘못된 휴대폰번호 입니다. 확인 후 다시 입력해주시기 바랍니다.';
            }
            $('input[name=_identitynum_valid]').val('');
            //alert(msg);
        }else{
            $('input[name=_identitynum_valid]').val('1');
        }
    }else{
        $('input[name=_identitynum_valid]').val('');
    }
});
$('.js_number_valid').trigger('change');// 최초실행시 한번실행시킨다


function validate_number(_type, number) {

    //빈칸과 대시 제거
    number = number.replace(/[ -]/g,'');

    var match;
    if(_type == "1"){
        //카드 번호가 유효한지 검사
        match = /^(?:(94[0-9]{14})|(4[0-9]{12}(?:[0-9]{3})?)|(5[1-5][0-9]{14})|(6(?:011|5[0-9]{2})[0-9]{12})|(3[47][0-9]{13})|(3(?:0[0-5]|[68][0-9])[0-9]{11})|((?:2131|1800|35[0-9]{3})[0-9]{11}))$/.exec(number);
    }
    else if(_type == "3"){
        //주민등록 번호가 유효한지 검사
        match = /^(?:[0-9]{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[1,2][0-9]|3[0,1]))[1-4][0-9]{6}$/.exec(number);
    }
    else if(_type == "4"){
        //사업자등록 번호가 유효한지 검사
        match = checkBizID(number);
    }
    else if(_type == "5"){
        //휴대폰 번호가 유효한지 검사
        match = /^01([0|1|6|7|8|9]?)-?([0-9]{3,4})-?([0-9]{4})$/.exec(number);
    }

    if(match) {
        return true;
    } else {
        return false;
    }
}

function checkBizID(bizID)  //사업자등록번호 체크
{
    // bizID는 숫자만 10자리로 해서 문자열로 넘긴다.
    var checkID = new Array(1, 3, 7, 1, 3, 7, 1, 3, 5, 1);
    var tmpBizID, i, chkSum=0, c2, remander;
     bizID = bizID.replace(/-/gi,'');

     for (i=0; i<=7; i++) chkSum += checkID[i] * bizID.charAt(i);
     c2 = "0" + (checkID[8] * bizID.charAt(8));
     c2 = c2.substring(c2.length - 2, c2.length);
     chkSum += Math.floor(c2.charAt(0)) + Math.floor(c2.charAt(1));
     remander = (10 - (chkSum % 10)) % 10 ;

    if (Math.floor(bizID.charAt(9)) == remander) return true ; // OK!
      return false;
}



// 폼 유효성 검사
$(document).ready(function(){
    $('form[name=frm]').validate({
            ignore: '.ignore',
            rules: {
                    _memtype: { required: true }
                    ,_mid: { required: true }
                    ,_oname: { required: true }
                    ,_ohp: { required: true }
                    ,_oemail: { required: true , email: true }
                    ,_rname: { required: true }
                    ,_rhp: { required: true }
                    ,_rzonecode: { required: true }
                    ,_raddr_doro: { required: true }
                    ,_raddr2: { required: true }
                    ,_tax_IdentityNum:{ required: function() { return ($("input[name=_get_tax]:checked").val() == "Y" && <?php echo ($row['o_paymethod']=='online' ? 'true' : 'false'); ?> ? true : false); } }
                    ,_identitynum_valid:{ required: function() { return ($("input[name=_get_tax]:checked").val() == "Y" && <?php echo ($row['o_paymethod']=='online' ? 'true' : 'false'); ?> ? true : false); } }
            },
            invalidHandler: function(event, validator) {
                // 입력값이 잘못된 상태에서 submit 할때 자체처리하기전 사용자에게 핸들을 넘겨준다.

            },
            messages: {
                    _memtype: { required: '회원타입을 선택해주시기 바랍니다.' }
                    ,_mid: { required: '주문자 아이디를 입력해주시기 바랍니다.' }
                    ,_oname: { required: '주문자명을 입력해주시기 바랍니다.' }
                    ,_ohp: { required: '주문자 휴대폰번호를 입력해주시기 바랍니다.' }
                    ,_oemail: { required: '주문자 이메일 주소를 입력해주시기 바랍니다.' , email: '이메일 형식이 올바르지 않습니다.' }
                    ,_rname: { required: '받는 분 이름을 입력해주시기 바랍니다.' }
                    ,_rhp: { required: '받는 분 휴대폰번호를 입력해주시기 바랍니다.' }
                    ,_rzonecode: { required: '우편번호 찾기 버튼을 눌러 배송지 주소(우편번호)를 입력해주시기 바랍니다.' }
                    ,_raddr_doro: { required: '우편번호 찾기 버튼을 눌러 배송지 주소를 입력해주시기 바랍니다.' }
                    ,_raddr2: { required: '배송지 주소를 입력해주시기 바랍니다.' }
                    ,_tax_IdentityNum:{ required: "신분확인번호를 입력해주시기 바랍니다." }
                    ,_identitynum_valid:{ required: "잘못된 신분확인번호 입니다." }
            },
            submitHandler : function(form) {
                // 폼이 submit 될때 마지막으로 뭔가 할 수 있도록 핸들을 넘겨준다.
                form.submit();
            }

    });
});
</script>
<!-- / 부분취소신청 -->


<?php include_once('wrap.footer.php'); ?>