<style>
.c_tag {float:initial !important; margin-bottom:2px !important;}
.c_tag.cart {color:#666 !important; background:#F4FFF4; border:1px solid #008000; width:75px !important; padding:0px !important; }
.c_tag.manual {color:#666 !important; background:#FFE8EF; border:1px solid #FF4080; width:75px !important; padding:0px !important; }
</style>
<?php
    if(!isset($_REQUEST['view'])) $_REQUEST['view'] = '';
    // 입금대기 주문 목록 지정
    if($_REQUEST['view'] == 'online') {
        $app_current_link = '_order.list.php?view=online';
        // 추가 파라메터 설정
        $arr_param = array('view'=>'online');
    }

    include_once('wrap.header.php');

    // 넘길 변수 설정하기
    $_PVS = ""; // 링크 넘김 변수
    foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {
        if(is_array($val)) foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }
        else $_PVS .= "&$key=$val";
    }
    $_PVSC = enc('e' , $_PVS);
    // 넘길 변수 설정하기



    // 추가파라메터
    if(!$arr_param) $arr_param = array();

    // 결제상태 추가 -- 결제실패
    $arr_order_status[] = "환불요청";
    $arr_order_status[] = "결제실패";

    // 기본결제상태 지정
    if($pass_paystatus == '') $pass_paystatus = 'A';

    // 검색 체크
    $s_query = " from smart_order as o left join smart_individual as indr on (indr.in_id=o.o_mid) where o_canceled!='Y' and `npay_order` = 'N' ";
    if( $pass_ordernum !="" ) { $s_query .= " and o_ordernum like '%${pass_ordernum}%' "; }
    if( $pass_pname !="" ) {
        $s_query .= "
            and (
                    select count(*)
                    from smart_order_product as op
                    where op.op_oordernum = o.o_ordernum
                        and concat(op.op_pcode,ifnull(op.op_pname,''),ifnull(op.op_option1,''),ifnull(op.op_option2,''),ifnull(op.op_option3,'')) like '%${pass_pname}%'
            ) > 0
        ";
    }
    if( $pass_mid !="" ) { $s_query .= " and o_mid like '%${pass_mid}%' "; }
    if( $pass_oname !="" ) { $s_query .= " and o_oname like '%${pass_oname}%' "; }
    if( $pass_rname !="" ) { $s_query .= " and o_rname like '%${pass_rname}%' "; }
    if( $pass_deposit !="" ) { $s_query .= " and o_deposit like '%${pass_deposit}%' "; }
    if( $pass_memtype !="" ) { $s_query .= " and o_memtype='${pass_memtype}' "; }
    if( $pass_paymethod !="" ) { 
        // LCY : 2021-07-04 : 신용카드 간편결제 추가
        $s_query .= " and ( o_paymethod='${pass_paymethod}' or o_easypay_paymethod_type = '${pass_paymethod}' )  "; 
    }
    if( $pass_paystatus !="A" ) { $s_query .= " and o_paystatus='${pass_paystatus}' "; }
        if( $pass_status !="" ) {
        if($pass_status=='접수완료'){
            $s_query .= " and o_status in ('접수완료','구매발주') ";
        }else{
            $s_query .= " and o_status='${pass_status}' ";
        }
    }
    if( $pass_sdate !="" ) { $s_query .= " and o_rdate>='${pass_sdate}' "; }
    if( $pass_edate !="" ) { $s_query .= " and o_rdate<'". date('Y-m-d', strtotime('+1day', strtotime($pass_edate))) ."' "; }
    //if( $pass_get_tax =="Y" ) { $s_query .= " and o_get_tax='Y' and ifnull((select ocs_method from smart_order_cashlog where ocs_ordernum=o.o_ordernum order by ocs_uid desc limit 1),'') = 'AUTH' "; }
    //else if( $pass_get_tax =="N" ) { $s_query .= " and o_get_tax='Y' and ifnull((select ocs_method from smart_order_cashlog where ocs_ordernum=o.o_ordernum order by ocs_uid desc limit 1),'') != 'AUTH' "; }
    if( $pass_get_tax =="Y" ) {
        $s_query .= "
            and o_get_tax='Y'
            and (
                ifnull((select ocs_method from smart_order_cashlog where ocs_ordernum=o.o_ordernum order by ocs_uid desc limit 1),'') = 'AUTH'
                or
                (select count(*) from smart_baro_cashbill where bc_ordernum=o.o_ordernum and bc_iscancel = 'N' and bc_isdelete = 'N' and BarobillState in ('2000','3000')) > 0
            )
        ";
    }
    else if( $pass_get_tax =="N" ) {
        $s_query .= "
            and o_get_tax='Y'
            and (
                ifnull((select ocs_method from smart_order_cashlog where ocs_ordernum=o.o_ordernum order by ocs_uid desc limit 1),'') != 'AUTH'
                and
                (select count(*) from smart_baro_cashbill where bc_ordernum=o.o_ordernum and bc_iscancel = 'N' and bc_isdelete = 'N' and BarobillState in ('2000','3000')) = 0
            )
        ";
    }


    // ----- JJC : 입점관리 : 2020-09-17 -----
    if($pass_com) {
        $s_query .= "
            and (
                SELECT 
                    count(*)
                FROM smart_order_product as op
                WHERE 
                    op.op_oordernum = o.o_ordernum AND
                    op.op_partnerCode = '". addslashes($pass_com) ."'
            ) > 0
        ";
    }
    // ----- JJC : 입점관리 : 2020-09-17 -----


    // 입금대기 주문 목록 검색 지정
    if($_REQUEST['view'] == 'online') {
        // 결제방식 - 무통장, 가상계좌
        $s_query .= " and o_paymethod in ('online', 'virtual') ";
        // 결제상태 - 접수대기
        $s_query .= " and o_paystatus='N' ";
        // 주문상태 - 접수대기
        $s_query .= " and o_status='접수대기' ";

        // 가상계좌 주문 체크 - 입금계좌 정보가 있는 주문만
        $s_query .= " and if(o_paymethod='virtual', (select count(*) as cnt from smart_order_onlinelog as ool where ool.ool_ordernum=o.o_ordernum), 1) > 0 ";
    }

    if(!$listmaxcount) $listmaxcount = 20;
    if(!$listpg) $listpg = 1;
    if(!$st) $st = "o_rdate"; // 접수완료일 우선 정렬
    $st = stripslashes($st);
    if(!$so) $so = 'desc';
    $count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스

    $res = _MQ(" select count(*) as cnt {$s_query} ");
    $TotalCount = $res['cnt'];
    $Page = ceil($TotalCount / $listmaxcount);

    $que = "
        select
            o.* , indr.in_id, indr.in_name,
            (select ocs_method from smart_order_cashlog where ocs_ordernum=o.o_ordernum order by ocs_uid desc limit 1) as ocs_cash,
            (select count(*) from smart_baro_cashbill where bc_ordernum=o.o_ordernum and bc_iscancel = 'N' and bc_isdelete = 'N' and BarobillState in ('2000','3000')) as bc_cnt
        {$s_query}
        order by {$st} {$so} limit $count , $listmaxcount
    ";
    $res = _MQ_assoc($que);

?>
<div class="group_title"><strong>주문검색</strong></div>

<!-- ●폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
<div class="data_form if_search">

    <form name="searchfrm" method="get" action="<?php echo $_SERVER["PHP_SELF"]?>">
    <?php if(sizeof($arr_param)>0){ foreach($arr_param as $__k=>$__v){ ?>
    <input type="hidden" name="<?php echo $__k; ?>" value="<?php echo $__v; ?>">
    <?php }} ?>
    <input type="hidden" name="mode" value="search">
    <input type="hidden" name="st" value="<?php echo $st; ?>">
    <input type="hidden" name="so" value="<?php echo $so; ?>">
    <input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
    <input type="hidden" name="_cpid" value="<?php echo $_cpid; ?>">

    <!-- 폼테이블 3단 -->
    <table class="table_form">
        <colgroup>
            <col width="140"><col width="*"><col width="140"><col width="*"><col width="140"><col width="*">
        </colgroup>
        <tbody>
            <tr>
                <th>주문번호</th>
                <td><input type="text" name="pass_ordernum" class="design" style="" value="<?php echo $pass_ordernum; ?>"></td>
                <th>주문자명</th>
                <td><input type="text" name="pass_oname" class="design" style="width:100px;" value="<?php echo $pass_oname; ?>"></td>
                <th>주문자 아이디</th>
                <td><input type="text" name="pass_mid" class="design" style="" value="<?php echo $pass_mid; ?>"></td>
            </tr>
            <tr>
                <th>입금자명</th>
                <td><input type="text" name="pass_deposit" class="design" style="width:100px;" value="<?php echo $pass_deposit; ?>"></td>
                <th>수령자명</th>
                <td><input type="text" name="pass_rname" class="design" style="width:100px;" value="<?php echo $pass_rname; ?>"></td>
                <th>회원타입</th>
                <td>
                    <?php echo _InputRadio( "pass_memtype" , array('','Y','N'), $pass_memtype , "" , array('전체','회원','비회원')); ?>
                </td>
            </tr>
            <?php
                // 입금대기 주문 목록 지정
                if($_REQUEST['view'] <> 'online') {
            ?>
            <tr>
                <th>결제수단</th>
                <td>
                    <?php echo _InputSelect( "pass_paymethod" , array_keys($arr_payment_type), $pass_paymethod , "" , array_values($arr_payment_type) , '전체'); ?>
                </td>
                <th>주문상태</th>
                <td>
                    <?php echo _InputSelect( "pass_status" , $arr_order_status , $pass_status , "" , "" , '전체'); ?>
                </td>
                <th>결제상태</th>
                <td>
                    <?php echo _InputRadio( "pass_paystatus" , array('A','Y','N'), $pass_paystatus , "" , array('전체','접수완료','접수대기') , ''); ?>
                </td>
            </tr>
            <?php }else{ ?>
            <tr>
                <th>결제수단</th>
                <td>
                    <?php echo _InputRadio( "pass_paymethod" , array('','online','virtual'), $pass_paymethod , "" , array('전체','무통장입금','가상계좌') , ''); ?>
                </td>
                <th>주문상태</th>
                <td>
                    <span class="c_tag gray h22 t4">접수대기</span>
                </td>
                <th>결제상태</th>
                <td>
                    <span class="c_tag gray h22 t4">접수대기</span>
                </td>
            </tr>
            <?php } ?>
            <tr>
                <th>주문상품</th>
                <td>
                    <input type="text" name="pass_pname" class="design" style="" value="<?php echo $pass_pname; ?>">
                </td>
                <th>주문일</th>
                <td>
                    <input type="text" name="pass_sdate" value="<?php echo $pass_sdate; ?>" class="design js_pic_day" style="width:85px">
                    <span class="fr_tx">-</span>
                    <input type="text" name="pass_edate" value="<?php echo $pass_edate; ?>" class="design js_pic_day" style="width:85px">
                </td>
                <th>현금영수증</th>
                <td>
                    <?php echo _InputRadio( "pass_get_tax" , array('','N','Y'), $pass_get_tax , "" , array('전체','발행대기','발행완료')); ?>
                </td>
            </tr>


            <?php
                // ----- JJC : 입점관리 : 2020-09-17 -----
                if($SubAdminMode === true && $AdminPath == 'totalAdmin') { // 입점업체 검색기능 2016-05-26 LDD
                    $arr_customer = arr_company();
                    $arr_customer2 = arr_company2();
            ?>
            <tr>
                <th>입점업체</th>
                <td colspan="5">
                    <!-- 20개 이상일때만 select2적용 -->
                    <?php if(sizeof($arr_customer) > 20){ ?>
                    <link href="/include/js/select2/css/select2.css" type="text/css" rel="stylesheet">
                    <script src="/include/js/select2/js/select2.min.js"></script>
                    <script>$(document).ready(function() { $('.select2').select2(); });</script>
                    <?php } ?>
                    <?php echo _InputSelect( 'pass_com' , array_keys($arr_customer) , $pass_com , ' class="select2" ' , array_values($arr_customer) , '-입점업체-'); ?>
                </td>
            </tr>
            <?php } // ----- JJC : 입점관리 : 2020-09-17 -----?>


        </tbody>
    </table>
    <!-- 폼테이블 3단 -->



    <!-- 가운데정렬버튼 -->
    <div class="c_btnbox">
        <ul>
            <li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"/></span></li>
            <?php
                if($mode == 'search'){
                    $arr_param = array_filter(array_merge($arr_param,array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)));
            ?>
                <li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal">전체목록</a></li>
            <?php } ?>
        </ul>
    </div>

    </form>

</div>
<!-- /폼 영역 -->





<!-- ● 데이터 리스트 -->
<div class="data_list">

    <form name="frm" method="post" action="" >
    <input type="hidden" name="_mode" value="">
    <input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
    <input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
    <input type="hidden" name="_search" value="<?php echo enc('e', $s_query); ?>">

        <!-- ●리스트 컨트롤영역 -->
        <div class="list_ctrl">
            <div class="left_box">
                <a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
                <a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
                <a href="#none" onclick="selectCancel(); return false;" class="c_btn h27 gray">선택주문취소</a>
                <?php if($view == 'online'){ ?>
                    <a href="#none" onclick="select_paystatus_send(); return false;" class="c_btn h27 gray">선택입금확인</a>
                <?php } ?>
            </div>
            <div class="right_box">
                <a href="#none" onclick="selectExcel(); return false;" class="c_btn icon icon_excel">선택 엑셀다운로드</a>
                <?php // LCY : 2022-02-15 : 검색엑셀다운로드 기능추가 ?>
                <a href="#none" onclick="search_excel_send(); return false;" class="c_btn icon icon_excel">검색 엑셀다운로드<?php echo ($TotalCount > 0?'('.number_format($TotalCount).')':null); ?></a>
                <!--<a href="#none" onclick="selectPrint(); return false;" class="c_btn icon icon_print">선택일괄인쇄</a>-->
                <select class="h27" onchange="location.href=this.value;">
                    <option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
                    <option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
                    <option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
                </select>
            </div>
        </div>
        <!-- / 리스트 컨트롤영역 -->


        <table class="table_list">
            <colgroup>
                <col width="45"><col width="70"><col width="90"><col width="135"><col width="60"><col width="*"><col width="90"><col width="110"><col width="90"><col width="90">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
                    <th scope="col">NO</th>
                    <th scope="col">주문일</th>
                    <th scope="col">주문번호<br>주문자명</th>
                    <th scope="col" colspan="2">상품정보</th>
                    <th scope="col">진행상태</th>
                    <th scope="col">결제금액</th>
                    <th scope="col">결제수단</th>
                    <th scope="col">관리</th>
                </tr>
            </thead>
            <tbody>
                <?php
                 if(sizeof($res) > 0){
                    foreach($res as $k=>$v){

                        $_num = $TotalCount - $count - $k ;

                        // 현금영수증 발행여부 확인
                        if($v['o_get_tax']=='Y') {
                            if($v['ocs_cash']=='AUTH') { $cash_status = '현금영수증 발행'; }
                            else if($v['bc_cnt']>0) { $cash_status = '현금영수증 발행'; } // 바로빌 현금영수증 확인
                            else { $cash_status = '현금영수증 요청'; }
                        } else { $cash_status = ''; }

                        # 모바일 구매
                        if($v['mobile'] == 'Y') $device_icon = '<span class="c_tag h18 mo">MO주문</span>';
                        else $device_icon = '<span class="c_tag h18 t3 pc">PC주문</span>';

                        # 수기 주문
                        if($v['o_buy_type'] == 'manual') $buy_type_icon = '<span class="c_tag h18 manual">수기 주문</span>';
                        # 장바구니 주문
                        else $buy_type_icon = '<span class="c_tag h18 t3 cart">장바구니 주문</span>';

                        # 주문상품 추출
                        $arr_pinfo = array(); // 주문상품, 옵션 정보
                        $arr_status = array(); // 주문상품 진행상태 체크

                        $sque = "
                            select
                                op.op_uid, op.op_pouid, op.op_pcode, op.op_pname, op.op_option1, op.op_option2, op.op_option3,  op.op_pbrand, op.op_cnt, op.op_is_addoption, op.op_cancel, op_sendstatus , op.op_partnerCode,  /* JJC : 입점관리 : 2020-09-17 */
                                p.p_img_list_square
                            from smart_order_product as op
                            left join smart_product as p on (p.p_code=op.op_pcode)
                            where op.op_oordernum = '". $v['o_ordernum'] ."' order by op.op_uid
                        ";
                        $sres = _MQ_assoc($sque);
                        foreach($sres as $sk=>$sv){
                            // 장바구니 주문
                            if ($v["o_buy_type"] == "cart") {
                                $op_pcode = $sv['op_pcode'];
                             // 수기 주문
                            } else if ($v["o_buy_type"] == "manual") {
                                if (!$sv['op_pcode']) {
                                    $op_pcode = $sv['op_pouid'];
                                } else {
                                    $op_pcode = $sv['op_pcode'];
                                }
                            }

                            // 상품코드
                            $arr_pinfo[$op_pcode]['code'] = $op_pcode;
                            $arr_pinfo[$op_pcode]['code_chk'] = $sv['op_pcode'];
                            // 상품명
                            $arr_pinfo[$op_pcode]['name'] = stripslashes($sv['op_pname']);
                            // 이미지 체크
                            $_p_img = get_img_src('thumbs_s_'.$sv['p_img_list_square']);
                            if($_p_img == '') $_p_img = 'images/thumb_no.jpg';
                            $arr_pinfo[$op_pcode]['img'] = $_p_img;

                            // JJC : 입점관리 : 2020-09-17
                            $arr_pinfo[$op_pcode]['cpid'] = $sv['op_partnerCode'];

                            if($sv['op_pouid']){ // 옵션있음
                                $arr_pinfo[$op_pcode]['has_option'] = 'Y';
                                $arr_pinfo[$op_pcode]['option'][] = array(
                                                                                            'op_uid'=>$sv['op_uid']
                                                                                            ,'name'=>implode(' ', array_filter(array($sv['op_option1'],$sv['op_option2'],$sv['op_option3'])))
                                                                                            ,'brand'=>$sv['op_pbrand']
                                                                                            ,'cnt'=>$sv['op_cnt']
                                                                                            ,'is_addoption'=>$sv['op_is_addoption']
                                                                                            ,'cancel_refund'=>$sv['op_cancel'] // KAY :: 2021-09-09 :: 옵션 부분취소
                                                                                        );
                            }else{ // 옵션없음
                                $arr_pinfo[$op_pcode]['has_option'] = 'N';
                            }
                            $arr_pinfo[$op_pcode]['cnt'] += $sv['op_cnt'];
                            $arr_pinfo[$op_pcode]['point'] += $sv['op_point'];
                            $arr_pinfo[$op_pcode]['delivery_type'] = $sv['op_delivery_type'];
                            $arr_pinfo[$op_pcode]['delivery_price'] += $sv['op_delivery_price'];
                            $arr_pinfo[$op_pcode]['add_delivery_price'] += $sv['op_add_delivery_price'];

                            // 주문상품의 진행상태
                            $arr_status[$op_pcode]['total']++;
                            if($v['o_canceled'] == 'Y' || $sv['op_cancel'] == 'Y'){ // 주문자체가 취소이거나, 부분취소가 있다면
                                $arr_status[$op_pcode]['cancel']++;
                            }else if($v['o_canceled'] == 'R'){ // 환불요청
                                $arr_status[$op_pcode]['refund']++;
                            }else if($sv['op_cancel'] == 'R'){ // KAY :: 2021-09-06 :: 부분취소요청
                                $arr_status[$op_pcode]['cancel_refund']++;
                            }else if($v['o_status'] == '결제실패'){ // 결제실패일경우
                                $arr_status[$op_pcode]['fail']++;
                            }else{
                                if($v['o_paystatus'] =='Y'){ // 주문결제를 했다면,
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
                        }

                        // 주문상품 진행상태 체크
                        foreach($arr_status as $sk=>$sv){
                            # 진행상태
                            $op_status_icon = '';
                            if($v['o_canceled'] == 'Y'){ // 주문자체가 취소 되었으면 주문취소 :: [접수대기, 접수완료, 배송중, 배송완료, 주문취소, 결제실패]
                                $arr_pinfo[$sk]['status'] = '주문취소';
                            }
                            else if($sv['fail']>0){ // 결제실패가 하나라도 있으면 결제실패상태 :: [접수대기, 접수완료, 배송중, 배송완료, 주문취소, 결제실패] - [결제실패]
                                $arr_pinfo[$sk]['status'] = '결제실패';
                            }
                            else if($sv['refund']>0){ // 환불요청이 하나라도 있으면 환불요청상태 :: [접수대기, 접수완료, 배송중, 배송완료, 주문취소, 결제실패] - [결제실패]
                                $arr_pinfo[$sk]['status'] = '환불요청';
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
                                $arr_pinfo[$sk]['status'] = '접수완료';
                            }
                            else if($sv['complete']>0){ // 배송완료가 하나라도 있으면 배송완료상태 :: [배송완료, 주문취소] - [배송완료]
                                $arr_pinfo[$sk]['status'] = '배송완료';
                            }else{ // 나머지는 주문취소  :: [주문취소] - [주문취소]
                                $arr_pinfo[$sk]['status'] = '주문취소';
                            }

                            // KAY :: 2021-09-09  :: 부분취소
                            if($sv['cancel_refund']>0){
                                $arr_pinfo[$sk]['cancel_refund']='부분취소요청';
                            }
                        }

                        // 주문상품 수 체크 - 최소:1
                        $app_rowspan	= max(1, count($arr_pinfo));

                        // 첫번째 주문상품 별도처리
                        $pinfo = array_shift($arr_pinfo);
                ?>
                        <!-- 상품 2개 이상시 배송이 각각 따로 진행될 경우 tr에 if_more2 클래스를 추가하고 상품정보와 진행상태 td에 각각 this_order클래스 추가 -->
                        <tr class="<?php echo ($app_rowspan > 1 ? 'if_more2' : null); ?>" >
                            <td rowspan="<?php echo $app_rowspan; ?>">
                                <label class="design"><input type="checkbox" name="chk_ordernum[<?php echo $v['o_ordernum']; ?>]" class="js_ck" value="Y"></label>
                            </td>
                            <td rowspan="<?php echo $app_rowspan; ?>"><?php echo number_format($_num); ?></td>
                            <td rowspan="<?php echo $app_rowspan; ?>">
                                <?php
                                    // 접수완료일이 있으면 노출 없으면 주문일 노출
                                    $app_rdate = ($v['o_paydate']<> '0000-00-00 00:00:00' ? $v['o_paydate'] : $v['o_rdate']);
                                ?>
                                <?php echo date('Y.m.d', strtotime($app_rdate)); ?><div class="t_light"><?php echo date('H:i', strtotime($app_rdate)); ?></div>
                            </td>
                            <td rowspan="<?php echo $app_rowspan; ?>" align=center>
                                <?php echo $buy_type_icon; ?>
                                <?php echo $device_icon; ?>
                                <span class="block" style="clear:both"><?php echo $v['o_ordernum']; ?></span>
                                <?php echo showUserInfo($v['o_mid'], $v['o_oname']); ?>
                            </td>

                            <!-- 주문 상품별 옵션정보:반복 -->
                            <td class="if_img<?php echo ($app_rowspan > 1 ? ' this_order' : null); ?>">
                                <a href="_product.form.php?_mode=modify&_code=<?php echo $pinfo['code']; ?>" title="<?php echo addslashes($pinfo['name']); ?>" target="_blank">
                                    <img src="<?php echo $pinfo['img']; ?>" alt="<?php echo addslashes($pinfo['name']); ?>">
                                </a>
                            </td>
                            <td class="<?php echo ($app_rowspan > 1 ? ' this_order' : null); ?>">
                                <?
                                // 장바구니 주문
                                if ($v["o_buy_type"] == "cart") {
                                ?>
                                    <!-- 상품정보 -->
                                    <div class="order_item">
                                        <!-- 상품명 -->
                                        <div class="title bold">
                                            <?php echo ($SubAdminMode === true && $AdminPath == 'totalAdmin' && $pinfo['cpid'] ? "<span style='font-weight:normal; color:#999;'>(".$arr_customer2[$pinfo['cpid']] . ")</span>" : "") ; // JJC : 입점관리 : 2020-09-17?>
                                            <?php echo stripslashes($pinfo['name']); ?>
                                        </div>
                                        <!-- 옵션명, div반복 -->
                                        <?php
                                            if($pinfo['has_option']=='Y' && count($pinfo['option']) > 0){
                                                foreach($pinfo['option'] as $sk=>$sv){
                                        ?>
                                                    <div class="option bullet"> <?php echo stripslashes($sv['name']); ?> × <span class="t_black"><?php echo number_format($sv['cnt']); ?>개</span></div>
                                                    <?php if($sv['cancel_refund']=='R'){?>
                                                        <div><span class="c_tag line red h18 t6" style="margin:5px 5px;">취소요청</span></div>
                                                    <?php }else if($sv['cancel_refund']=='Y'){?>
                                                        <div><span class="c_tag red h18 t6" style="margin:5px 5px;">취소완료</span></div>
                                                    <?php }?>
                                        <?php
                                                }
                                            }else{
                                        ?>
                                                <div class="option bullet"><span class="t_black"><?php echo number_format($pinfo['cnt']); ?>개</span></div>
                                                <?php if($pinfo['cancel_refund']=='부분취소요청'){?>
                                                  <div><span class="c_tag line red h18 t6" style="margin:5px 5px;">취소요청</span></div>
                                                <?php }else if($pinfo['status']=='주문취소'){?>
                                                  <div><span class="c_tag red h18 t6" style="margin:5px 5px;">취소완료</span></div>
                                                <?php }?>
                                        <?php } ?>
                                    </div>

                                <?
                                 // 수기 주문
                                } else if ($v["o_buy_type"] == "manual") {
                                ?>
                                    <!-- 상품정보 -->
                                    <div class="order_item">
                                        <!-- 상품명 -->
                                        <div class="title bold">
                                            <?php echo ($SubAdminMode === true && $AdminPath == 'totalAdmin' && $pinfo['cpid'] ? "<span style='font-weight:normal; color:#999;'>(".$arr_customer2[$pinfo['cpid']] . ")</span>" : "") ; // JJC : 입점관리 : 2020-09-17?>
                                            <?php echo stripslashes($pinfo['name']); ?>
                                            <?php if (!$pinfo['code_chk']) { ?>
                                            <a href="javascript:product_add_popup('<?php echo $v['o_ordernum']; ?>','<?php echo $pinfo['code']; ?>');" class="c_tag red h22 t5" style="margin:5px 5px;">상품 등록</a>
                                            <? } ?>
                                        </div>
                                        <!-- 옵션명, div반복 -->
                                        <?php
                                            if($pinfo['has_option']=='Y' && count($pinfo['option']) > 0){
                                                foreach($pinfo['option'] as $sk=>$sv){
                                        ?>
                                                    <div class="option bullet"><?php echo stripslashes($sv['name']); ?> × <span class="t_black"><input type="text" name="_op_cnt" class="design" style="width:50px;" value="<?php echo $sv['cnt']; ?>">개</span></div>
                                                    <?php if($sv['cancel_refund']=='R'){?>
                                                        <div><span class="c_tag line red h18 t6" style="margin:5px 5px;">취소요청</span></div>
                                                    <?php }else if($sv['cancel_refund']=='Y'){?>
                                                        <div><span class="c_tag red h18 t6" style="margin:5px 5px;">취소완료</span></div>
                                                    <?php }?>
                                                    <a href="#none" class="c_btn h18 t4 black js_submit" data-num="<?php echo $v['o_ordernum']; ?>" data-uid="<?php echo $sv['op_uid']; ?>" style="margin:7px 0 0 5px;">변경</a>
                                        <?php
                                                }
                                            }else{
                                        ?>
                                                <div class="option bullet"><span class="t_black"><input type="text" name="op_cnt[<?=$v['o_sendstatus']?>]" class="design" style="width:50px;" value="<?php echo $pinfo['cnt']; ?>">개</span></div>
                                                <?php if($pinfo['cancel_refund']=='부분취소요청'){?>
                                                  <div><span class="c_tag line red h18 t6" style="margin:5px 5px;">취소요청</span></div>
                                                <?php }else if($pinfo['status']=='주문취소'){?>
                                                  <div><span class="c_tag red h18 t6" style="margin:5px 5px;">취소완료</span></div>
                                                <?php }?>
                                                    <a href="#none" class="c_btn h18 t4 black js_submit" data-num="<?php echo $v['o_ordernum']; ?>" data-uid="<?php echo $sv['op_uid']; ?>" style="margin:7px 0 0 5px;">변경</a>
                                        <?php } ?>
                                    </div>
                                <?
                                }
                                ?>
                            </td>
                            <td class="<?php echo ($app_rowspan > 1 ? ' this_order' : null); ?>">
                                <div class="lineup-vertical">
                                    <?php echo ($pinfo['status']?$arr_adm_button[$pinfo['status']]:$arr_adm_button['결제실패']); ?>
                                </div>
                            </td>
                            <!-- //주문 상품별 옵션정보:반복 -->

                            <td class="t_black bold" rowspan="<?php echo $app_rowspan; ?>"><?php echo number_format($v['o_price_real']); ?>원</td>
                            <td rowspan="<?php echo $app_rowspan; ?>">
                                <div class="lineup-vertical">
                                    <?php 
                                        // LCY : 2021-07-04 : 신용카드 간편결제 추가
                                        if( $v['o_easypay_paymethod_type'] != ''){ 
                                            echo $arr_adm_button["E".$arr_available_easypay_pg_list[$v['o_easypay_paymethod_type']]];
                                        }else{
                                            echo $arr_adm_button[$arr_payment_type[$v['o_paymethod']]];
                                        }
                                    ?>
                                    <?php echo $arr_adm_button[$cash_status]; ?>
                                </div>
                            </td>
                            <td rowspan="<?php echo $app_rowspan; ?>">
                                <div class="lineup-vertical">
                                    <a href="#none" onclick="window.open('<?php echo OD_PROGRAM_URL; ?>/mypage.order.mass.print_view.php<?php echo URI_Rebuild('?', array('_mode'=>'print', 'ordernum'=>$v['o_ordernum'], '_PVSC'=>$_PVSC)); ?>' ,'print','width=860,height=820,scrollbars=yes'); return false;" class="c_btn h22 ">주문인쇄</a>
                                    <a href="_order.form.php<?php echo URI_Rebuild('?', array('view'=>$view, '_mode'=>'modify', '_ordernum'=>$v['o_ordernum'], '_PVSC'=>$_PVSC)); ?>" class="c_btn h22 ">상세보기</a>
                                    <a href="#none" onclick="cancel('_order.pro.php<?php echo URI_Rebuild('?', array('_mode'=>'cancel', '_ordernum'=>$v['o_ordernum'], '_PVSC'=>$_PVSC)); ?>'); return false;" class="c_btn h22 gray">주문취소</a>
                                </div>
                            </td>
                        </tr>

                        <?php
                            // 나머지 주문상품별 옵션 노출
                            if(count($arr_pinfo)>0){
                                foreach($arr_pinfo as $pinfo){
                        ?>
                                    <tr class="<?php echo ($app_rowspan > 1 ? 'if_more2' : null); ?>">
                                        <!-- 주문 상품별 옵션정보:반복 -->
                                        <td class="if_img<?php echo ($app_rowspan > 1 ? ' this_order' : null); ?>">
                                            <a href="_product.form.php?_mode=modify&_code=<?php echo $pinfo['code']; ?>" title="<?php echo addslashes($pinfo['name']); ?>" target="_blank">
                                                <img src="<?php echo $pinfo['img']; ?>" alt="<?php echo addslashes($pinfo['name']); ?>">
                                            </a>
                                        </td>
                                        <td class="<?php echo ($app_rowspan > 1 ? ' this_order' : null); ?>">
                                        <?
                                        // 장바구니 주문
                                        if ($v["o_buy_type"] == "cart") {
                                        ?>
                                            <!-- 상품정보 -->
                                            <div class="order_item">
                                                <!-- 상품명 -->
                                                <div class="title bold">
                                                    <?php echo ($SubAdminMode === true && $AdminPath == 'totalAdmin' && $pinfo['cpid'] ? "<span style='font-weight:normal; color:#999;'>(".$arr_customer2[$pinfo['cpid']] . ")</span>" : "") ; // JJC : 입점관리 : 2020-09-17?>
                                                    <?php echo stripslashes($pinfo['name']); ?>
                                                </div>
                                                <!-- 옵션명, div반복 -->
                                                <?php
                                                    if($pinfo['has_option']=='Y' && count($pinfo['option']) > 0){
                                                        foreach($pinfo['option'] as $sk=>$sv){
                                                ?>
                                                            <div class="option bullet"><?php echo stripslashes($sv['name']); ?> × <span class="t_black"><?php echo $sv['cnt']; ?>개</span></div>
                                                            <?php if($sv['cancel_refund']=='R'){?>
                                                              <div><span class="c_tag line red h18 t6" style="margin:5px 5px;">취소요청</span></div>
                                                            <?php }else if($sv['cancel_refund']=='Y'){?>
                                                              <div><span class="c_tag red h18 t6" style="margin:5px 5px;">취소완료</span></div>
                                                            <?php }?>
                                                <?php
                                                        }
                                                    }else{
                                                ?>
                                                        <div class="option bullet"><span class="t_black"><?php echo $pinfo['cnt']; ?>개</span></div>
                                                        <?php if($pinfo['cancel_refund']=='부분취소요청'){?>
                                                            <div><span class="c_tag line red h18 t6" style="margin:5px 5px;">취소요청</span></div>
                                                        <?php }else if($pinfo['status']=='주문취소'){?>
                                                            <div><span class="c_tag red h18 t6" style="margin:5px 5px;">취소완료</span></div>
                                                        <?php }?>
                                                <?php } ?>
                                            </div>
                                        <?
                                         // 수기 주문
                                        } else if ($v["o_buy_type"] == "manual") {
                                        ?>
                                            <!-- 상품정보 -->
                                            <div class="order_item">
                                                <!-- 상품명 -->
                                                <div class="title bold">
                                                    <?php echo ($SubAdminMode === true && $AdminPath == 'totalAdmin' && $pinfo['cpid'] ? "<span style='font-weight:normal; color:#999;'>(".$arr_customer2[$pinfo['cpid']] . ")</span>" : "") ; // JJC : 입점관리 : 2020-09-17?>
                                                    <?php echo stripslashes($pinfo['name']); ?>
                                                    <?php if (!$pinfo['code_chk']) { ?>
                                                    <a href="javascript:product_add_popup('<?php echo $v['o_ordernum']; ?>','<?php echo $pinfo['code']; ?>');" class="c_tag red h22 t5" style="margin:5px 5px;">상품 등록</a>
                                                    <? } ?>
                                                </div>
                                                <!-- 옵션명, div반복 -->
                                                <?php
                                                    if($pinfo['has_option']=='Y' && count($pinfo['option']) > 0){
                                                        foreach($pinfo['option'] as $sk=>$sv){
                                                ?>
                                                            <div class="option bullet"><?php echo stripslashes($sv['name']); ?> × <span class="t_black"><input type="text" name="_op_cnt" class="design" style="width:50px;" value="<?php echo $sv['cnt']; ?>">개</span></div>
                                                            <?php if($sv['cancel_refund']=='R'){?>
                                                              <div><span class="c_tag line red h18 t6" style="margin:5px 5px;">취소요청</span></div>
                                                            <?php }else if($sv['cancel_refund']=='Y'){?>
                                                              <div><span class="c_tag red h18 t6" style="margin:5px 5px;">취소완료</span></div>
                                                            <?php }?>
                                                            <a href="#none" class="c_btn h18 t4 black js_submit" data-num="<?php echo $v['o_ordernum']; ?>" data-uid="<?php echo $sv['op_uid']; ?>" style="margin:7px 0 0 5px;">변경</a>
                                                <?php
                                                        }
                                                    }else{
                                                ?>
                                                        <div class="option bullet"><span class="t_black"><input type="text" name="_op_cnt" class="design" style="width:50px;" value="<?php echo $pinfo['cnt']; ?>">개</span></div>
                                                        <?php if($pinfo['cancel_refund']=='부분취소요청'){?>
                                                            <div><span class="c_tag line red h18 t6" style="margin:5px 5px;">취소요청</span></div>
                                                        <?php }else if($pinfo['status']=='주문취소'){?>
                                                            <div><span class="c_tag red h18 t6" style="margin:5px 5px;">취소완료</span></div>
                                                        <?php }?>
                                                        <a href="#none" class="c_btn h18 t4 black js_submit" data-num="<?php echo $v['o_ordernum']; ?>" data-uid="<?php echo $sv['op_uid']; ?>" style="margin:7px 0 0 5px;">변경</a>
                                                <?php } ?>
                                            </div>
                                        <?
                                        }
                                        ?>
                                        </td>
                                        <td class="<?php echo ($app_rowspan > 1 ? ' this_order' : null); ?>">
                                            <div class="lineup-vertical">
                                                <?php echo $arr_adm_button[$pinfo['status']]; ?>
                                            </div>
                                        </td>
                                        <!-- //주문 상품별 옵션정보:반복 -->
                                    </tr>
                        <?php
                                }
                            }
                        ?>

                <?php
                    }
                }
                ?>
            </tbody>
        </table>



        <?php if(sizeof($res) < 1){ ?>
            <!-- 내용없을경우 -->
            <div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
        <?php } ?>


        <!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
        <div class="paginate">
            <?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
        </div>

        </form>

</div>
<!-- / 데이터 리스트 -->



<script>
    // 수기 주문 - 수량 변경
    $(document).on('click', '.js_submit', function(e) {
        e.preventDefault();
        var su = $(this).closest('tr');
        var _ordernum = $(this).data('num');
        var _uid = $(this).data('uid');
        var _op_cnt = su.find('input[name^=_op_cnt]').val();
        var _url = '_order.pro.php';
        _url = _url+"?_mode=modify_op_cnt&_ordernum="+_ordernum+"&_uid="+_uid+"&_op_cnt="+_op_cnt;

        common_frame.location.href = _url;
    });

    <?php if($view == 'online'){ ?>
    // # -- 2016-11-28 LCY :: 무통장다수개처리
    function select_paystatus_send()
    {
        if(confirm('선택된 항목을 입금확인 처리 하시겠습니까?(주문연동이 자동적용됩니다.)') == false){
            return false;
        }

        // -- 체크항목
         if($('.js_ck').is(':checked')){

            $('form[name=frm]').children('input[name=_mode]').val('select_paystatus');
            $('form[name=frm]').attr('action' , '_order.pro.php');
            document.frm.submit();
         }
         else { // 체크 안되었을 시
             alert('1건 이상 선택시 입금확인이 가능합니다.');
         }

    }
    // # -- 2016-11-28 LCY :: 무통장다수개처리
    <?php } ?>

     function selectModify() {
         if($('.js_ck').is(":checked")){
             document.frm.submit();
         }
         else {
             alert('1개 이상 선택해 주시기 바랍니다.');
         }
     }



     function selectCancel() {
         if($('.js_ck').is(':checked')){
             if(confirm('정말 취소하시겠습니까?')){
                $('form[name=frm]').children('input[name=_mode]').val('mass_cancel');
                $('form[name=frm]').attr('action' , '_order.pro.php');
                document.frm.submit();
             }
         }
         else {
             alert('1개 이상 선택해 주시기 바랍니다.');
         }
     }

     function selectExcel() {
         if($('.js_ck').is(':checked')){
            $('form[name=frm]').children('input[name=_mode]').val('get_excel');
            $('form[name=frm]').attr('action' , '_order.pro.php');
            document.frm.submit();
         }
         else {
             alert('1개 이상 선택해 주시기 바랍니다.');
         }
     }
     function selectPrint() {
         if($('.js_ck').is(':checked')){
            $('form[name=frm]').children('input[name=_mode]').val('mass_print');
            $('form[name=frm]').attr('target' , 'mass_print');
            $('form[name=frm]').attr('action' , '<?php echo OD_PROGRAM_URL; ?>/mypage.order.mass.print_view.php');
            document.frm.submit();
         }
         else {
             alert('1개 이상 선택해 주시기 바랍니다.');
         }
     }

    <?php // LCY : 2022-02-15 : 검색엑셀다운로드 기능추가 ?>
    function search_excel_send() {
        $('form[name=frm]').children('input[name=_mode]').val('get_search_excel');
        $('form[name=frm]').attr('action' , '_order.pro.php');
        document.frm.submit();
    }

    // 수기주문 - 상품등록
    function product_add_popup(o_ordernum, op_pouid) {
		window.open('_product.form.php?_mode=add&o_ordernum='+o_ordernum+'&op_pouid='+op_pouid ,'product','width=1520,height=700,scrollbars=yes');
    }
</SCRIPT>


<?php include_once('wrap.footer.php'); ?>