<style>
.c_tag {float:initial !important; margin-bottom:2px !important;}
.c_tag.cart {color:#666 !important; background:#F4FFF4; border:1px solid #008000; width:75px !important; padding:0px !important; }
.c_tag.manual {color:#666 !important; background:#FFE8EF; border:1px solid #FF4080; width:75px !important; padding:0px !important; }
.order_item .option {width:100%;}
.table_option {}
.table_list td {padding:3px 10px !important;}
.table_list td.tt {width:200px; background:#FFFFEA;}
.table_list td.th {width:70px; background:#f5f5f5;}
.table_list td.td {width:100px; text-align:left;}
.table_list td.td_btn {width:220px; text-align:left;}
.order_item .option {padding: 2px 0 5px !important;}
.order_item .option:before {background:initial !important;}
.order_item .title {padding:6px 0 10px; font-size:14px;}
</style>
<?php
    if(!isset($_REQUEST['view'])) $_REQUEST['view'] = '';

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

    $s_query = " from smart_order_product as op where (1) ";

    if($pass_paystatus == '') $pass_paystatus = '구매발주';
    $s_query .= " and op_pcode!='' AND op_sendstatus='${pass_paystatus}' ";

    // 상품코드
    if ($pass_pcode) {
        $s_query .= " and instr(op_pcode,'$pass_pcode')";
    }

    if(!$listmaxcount) $listmaxcount = 20;
    if(!$listpg) $listpg = 1;
    if(!$st) $st = "op_rdate"; // 접수완료일 우선 정렬
    $st = stripslashes($st);
    if(!$so) $so = 'asc';
    $count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스
 
    $res = _MQ("select count(c.op_pcode) as cnt from ( select op_pcode {$s_query} group by op.op_pcode ) as c ");
    $TotalCount = $res['cnt'];
    $Page = ceil($TotalCount / $listmaxcount);

    $que = "
        select
         op.*
        {$s_query}
        group by op.op_pcode
        order by {$st} {$so} limit $count , $listmaxcount
    ";
    //echo $que;
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
                <th>상품코드</th>
                <td><input type="text" name="pass_pcode" class="design" style="" value="<?php echo $pass_pcode; ?>"></td>
                <td>
                    <span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"/></span>
                </td>
            </tr>
        </tbody>
    </table>
    <!-- 폼테이블 3단 -->
    </form>

</div>
<!-- /폼 영역 -->





<!-- ● 데이터 리스트 -->
<div class="data_list" style="margin-top:40px">

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
            </div>
            <div class="right_box">
                <a href="#none" onclick="selectPrint(); return false;" class="c_btn icon icon_print">선택일괄인쇄</a>
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
                <col width="45"><col width="100"><col width="*">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
                    <th scope="col">NO</th>
                    <th scope="col" colspan="2">상품정보</th>
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
                                op.*, sum(op.op_cnt) as op_cnt, sum(op.op_orderstock_cnt) as op_orderstock_cnt, sum(op.op_instock_cnt) as op_instock_cnt, 
                                p.p_img_list_square
                            from smart_order_product as op
                            left join smart_product as p on (p.p_code=op.op_pcode)
                            where op.op_pcode = '". $v['op_pcode'] ."' 
                            group by op_pouid order by op.op_uid
                        ";
                        //echo $sque."<br>";
                        $sres = _MQ_assoc($sque);
                        foreach($sres as $sk=>$sv){
                            $op_pcode = $sv['op_pcode'];

                            // 상품코드
                            $arr_pinfo[$op_pcode]['code'] = $op_pcode;
                            // 상품명
                            $arr_pinfo[$op_pcode]['name'] = stripslashes($sv['op_pname']);
                            // 이미지 체크
                            $_p_img = get_img_src('thumbs_s_'.$sv['p_img_list_square']);
                            if($_p_img == '') $_p_img = 'images/thumb_no.jpg';
                            $arr_pinfo[$op_pcode]['img'] = $_p_img;

                            // JJC : 입점관리 : 2020-09-17
                            $arr_pinfo[$op_pcode]['cpid'] = $sv['op_partnerCode'];

                            if($sv['op_pouid']){ // 옵션있음
                                $stock_cnt = order_product_option_stock($v['o_ordernum'], $op_pcode, $sv['op_pouid'], $sv['op_is_addoption']);
                                $arr_pinfo[$op_pcode]['has_option'] = 'Y';
                                if (!$sv['op_option1']) $sv['op_option1'] = $tmp_option1;
                                $arr_pinfo[$op_pcode]['option'][] = array(
                                                                                            'op_uid'=>$sv['op_uid']
                                                                                            ,'name'=>implode(' ', array_filter(array($sv['op_option1'],$sv['op_option2'],$sv['op_option3'])))
                                                                                            ,'brand'=>$sv['op_pbrand']
                                                                                            ,'cnt'=>$sv['op_cnt']
                                                                                            ,'orderstock_cnt'=>$sv['op_orderstock_cnt']
                                                                                            ,'instock_cnt'=>$sv['op_instock_cnt']
                                                                                            ,'stock_cnt'=>$stock_cnt
                                                                                            ,'is_addoption'=>$sv['op_is_addoption']
                                                                                            ,'cancel_refund'=>$sv['op_cancel'] // KAY :: 2021-09-09 :: 옵션 부분취소
                                                                                        );
                                $tmp_option1 = $sv['op_option1'];
                            }else{ // 옵션없음
                                $stock_cnt = order_product_option_stock($op_pcode, $sv['op_pouid'], $sv['op_is_addoption']);
                                $arr_pinfo[$op_pcode]['has_option'] = 'N';
                                $arr_pinfo[$op_pcode]['stock_cnt'] = $stock_cnt;
                            }
                            $arr_pinfo[$op_pcode]['cnt'] += $sv['op_cnt'];
                            $arr_pinfo[$op_pcode]['point'] += $sv['op_point'];
                            $arr_pinfo[$op_pcode]['delivery_type'] = $sv['op_delivery_type'];
                            $arr_pinfo[$op_pcode]['delivery_price'] += $sv['op_delivery_price'];
                            $arr_pinfo[$op_pcode]['add_delivery_price'] += $sv['op_add_delivery_price'];
                        }

                        // 주문상품 수 체크 - 최소:1
                        $app_rowspan	= max(1, count($arr_pinfo));

                        // 첫번째 주문상품 별도처리
                        $pinfo = array_shift($arr_pinfo);
                ?>
                        <!-- 상품 2개 이상시 배송이 각각 따로 진행될 경우 tr에 if_more2 클래스를 추가하고 상품정보와 진행상태 td에 각각 this_order클래스 추가 -->
                        <tr class="<?php echo ($app_rowspan > 1 ? 'if_more2' : null); ?>" >
                            <td rowspan="<?php echo $app_rowspan; ?>">
                                <label class="design"><input type="checkbox" name="chk_ordernum[<?php echo $v['op_oordernum']; ?>]" class="js_ck" value="Y"></label>
                            </td>
                            <td rowspan="<?php echo $app_rowspan; ?>"><?php echo number_format($_num); ?></td>

                            <!-- 주문 상품별 옵션정보:반복 -->
                            <td class="if_img<?php echo ($app_rowspan > 1 ? ' this_order' : null); ?>">
                                <a href="_product.form.php?_mode=modify&_code=<?php echo $pinfo['code']; ?>" title="<?php echo addslashes($pinfo['name']); ?>" target="_blank">
                                    <img src="<?php echo $pinfo['img']; ?>" alt="<?php echo addslashes($pinfo['name']); ?>">
                                </a>
                            </td>
                            <td class="<?php echo ($app_rowspan > 1 ? ' this_order' : null); ?>">
                                <!-- 상품정보 -->
                                <div class="order_item">
                                    <!-- 상품명 -->
                                    <div class="title bold">
                                        <?php echo stripslashes($pinfo['name']); ?>
                                    </div>
                                    <!-- 옵션명, div반복 -->
                                    <?php
                                        if($pinfo['has_option']=='Y' && count($pinfo['option']) > 0){
                                            foreach($pinfo['option'] as $sk=>$sv){
                                    ?>
                                            <div class="option">
                                                <table class="table_option">
                                                <tr>
                                                    <td class="tt"><?php echo stripslashes($sv['name']); ?></td>
                                                    <td class="th">재고수량</td>
                                                    <td class="td">
                                                       <span class="t_black"><?php echo number_format($sv['stock_cnt']); ?>개</span>
                                                    </td>
                                                    <td class="th">주문수량</td>
                                                    <td class="td">
                                                       <span class="t_black"><?php echo number_format($sv['cnt']); ?>개</span>
                                                    </td>
                                                    <td class="th">발주수량</td>
                                                    <td class="td">
                                                        <span class="t_black"><input type="text" name="_op_stockorder_cnt" class="design" style="width:50px;" value="<?php echo $sv['orderstock_cnt']; ?>">개</span>
                                                    </td>
                                                    <td class="th">입고수량</td>
                                                    <td class="td">
                                                        <span class="t_black"><input type="text" name="_op_stock_cnt" class="design" style="width:50px;" value="<?php echo $sv['instock_cnt']; ?>">개</span>
                                                    </td>
                                                    <td class="td_btn">
                                                        <a href="javascript:void(0);" class="c_tag line green h18 t6 js_stock_submit" style="margin:5px 5px;" data-uid="<?php echo $sv['op_uid']; ?>" >발주/입고저장</a>
                                                        <a href="javascript:void(0);" class="c_tag line red h18 t6 js_soldout_submit" style="margin:5px 5px;" data-uid="<?php echo $sv['op_uid']; ?>">품절처리</a>
                                                    </td>
                                                </tr>
                                                </table>
                                            </div>
                                    <?php
                                            }
                                        }else{
                                    ?>
                                            <div class="option">
                                                <table class="table_option">
                                                <tr>
                                                    <td class="tt"><?php echo stripslashes($pinfo['name']); ?></td>
                                                    <td class="th">재고수량</td>
                                                    <td class="td">
                                                       <span class="t_black"><?php echo number_format($pinfo['stock_cnt']); ?>개</span>
                                                    </td>
                                                    <td class="th">주문수량</td>
                                                    <td class="td">
                                                        <span class="t_black"><?php echo number_format($pinfo['cnt']); ?>개</span>
                                                    </td>
                                                    <td class="th">발주수량</td>
                                                    <td class="td">
                                                        <span class="t_black"><input type="text" name="_op_stockorder_cnt" class="design" style="width:50px;" value="<?php echo $sv['orderstock_cnt']; ?>">개</span>
                                                    </td>
                                                    <td class="th">입고수량</td>
                                                    <td class="td">
                                                        <span class="t_black"><input type="text" name="_op_stock_cnt" class="design" style="width:50px;" value="<?php echo $sv['instock_cnt']; ?>">개</span>
                                                    </td>
                                                    <td class="td_btn">
                                                        <a href="javascript:void(0);" class="c_tag line green h18 t6 js_stock_submit" style="margin:5px 5px;" data-uid="<?php echo $sv['op_uid']; ?>" >발주/입고저장</a>
                                                        <a href="javascript:void(0);" class="c_tag line red h18 t6 js_soldout_submit" style="margin:5px 5px;" data-uid="<?php echo $sv['op_uid']; ?>">품절처리</a>
                                                    </td>
                                                </tr>
                                                </table>
                                            </div>
                                    <?php } ?>
                                </div>
                            </td>
                            <!-- //주문 상품별 옵션정보:반복 -->
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
                                    <!-- 상품정보 -->
                                    <div class="order_item">
                                        <!-- 상품명 -->
                                        <div class="title bold">
                                            <?php echo stripslashes($pinfo['name']); ?>
                                        </div>
                                        <!-- 옵션명, div반복 -->
                                        <?php
                                            if($pinfo['has_option']=='Y' && count($pinfo['option']) > 0){
                                                foreach($pinfo['option'] as $sk=>$sv){
                                        ?>
                                                <div class="option">
                                                    <table class="table_option">
                                                    <tr>
                                                        <td class="tt"><?php echo stripslashes($sv['name']); ?></td>
                                                        <td class="th">재고수량</td>
                                                        <td class="td">
                                                           <span class="t_black"><?php echo number_format($sv['stock_cnt']); ?>개</span>
                                                        </td>
                                                        <td class="th">주문수량</td>
                                                        <td class="td">
                                                            <span class="t_black"><?php echo number_format($sv['cnt']); ?>개</span>
                                                        </td>
                                                        <td class="th">발주수량</td>
                                                        <td class="td">
                                                            <span class="t_black"><input type="text" name="_op_stockorder_cnt" class="design" style="width:50px;" value="<?php echo $sv['orderstock_cnt']; ?>">개</span>
                                                        </td>
                                                        <td class="th">입고수량</td>
                                                        <td class="td">
                                                            <span class="t_black"><input type="text" name="_op_stock_cnt" class="design" style="width:50px;" value="<?php echo $sv['instock_cnt']; ?>">개</span>
                                                        </td>
                                                    <td class="td_btn">
                                                        <a href="javascript:void(0);" class="c_tag line green h18 t6 js_stock_submit" style="margin:5px 5px;" data-uid="<?php echo $sv['op_uid']; ?>" >발주/입고저장</a>
                                                        <a href="javascript:void(0);" class="c_tag line red h18 t6 js_soldout_submit" style="margin:5px 5px;" data-uid="<?php echo $sv['op_uid']; ?>">품절처리</a>
                                                    </td>
                                                    </tr>
                                                    </table>
                                                </div>
                                        <?php
                                                }
                                            }else{
                                        ?>
                                                <div class="option">
                                                    <table class="table_option">
                                                    <tr>
                                                        <td class="tt"><?php echo stripslashes($pinfo['name']); ?></td>
                                                        <td class="th">재고수량</td>
                                                        <td class="td">
                                                           <span class="t_black"><?php echo number_format($pinfo['stock_cnt']); ?>개</span>
                                                        </td>
                                                        <td class="th">주문수량</td>
                                                        <td class="td">
                                                            <span class="t_black"><?php echo number_format($pinfo['cnt']); ?>개</span>
                                                        </td>
                                                        <td class="th">발주수량</td>
                                                        <td class="td">
                                                            <span class="t_black"><input type="text" name="_op_stockorder_cnt" class="design" style="width:50px;" value="<?php echo $sv['orderstock_cnt']; ?>">개</span>
                                                        </td>
                                                        <td class="th">입고수량</td>
                                                        <td class="td">
                                                            <span class="t_black"><input type="text" name="_op_stock_cnt" class="design" style="width:50px;" value="<?php echo $sv['instock_cnt']; ?>">개</span>
                                                        </td>
                                                        <td class="td_btn">
                                                            <a href="javascript:void(0);" class="c_tag line green h18 t6 js_stock_submit" style="margin:5px 5px;" data-uid="<?php echo $sv['op_uid']; ?>" >발주/입고저장</a>
                                                            <a href="javascript:void(0);" class="c_tag line red h18 t6 js_soldout_submit" style="margin:5px 5px;" data-uid="<?php echo $sv['op_uid']; ?>">품절처리</a>
                                                        </td>
                                                    </tr>
                                                    </table>
                                                </div>
                                        <?php } ?>
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
    // 입고 저장
    $(document).on('click', '.js_stock_submit', function(e) {
        e.preventDefault();
        var su = $(this).closest('tr');
        var _ordernum = $(this).data('num');
        var _uid = $(this).data('uid');
        var _op_stockorder_cnt = su.find('input[name^=_op_stockorder_cnt]').val();
        var _op_stock_cnt = su.find('input[name^=_op_stock_cnt]').val();
        var _url = '_stockorder.pro.php';
        _url = _url+"?_mode=modify_stock&_ordernum="+_ordernum+"&_uid="+_uid+"&_op_stockorder_cnt="+_op_stockorder_cnt+"&_op_stock_cnt="+_op_stock_cnt;

        common_frame.location.href = _url;
    });

    // 품절 처리
    $(document).on('click', '.js_soldout_submit', function(e) {
        e.preventDefault();
        if (confirm("품절 처리 하시겠습니까?")){
            var su = $(this).closest('tr');
            var _ordernum = $(this).data('num');
            var _uid = $(this).data('uid');
            var _url = '_stock.pro.php';
            _url = _url+"?_mode=modify_soldout&_ordernum="+_ordernum+"&_uid="+_uid;

            common_frame.location.href = _url;
        }    
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
            $('form[name=frm]').attr('target' , 'stockorder_print');
            $('form[name=frm]').attr('action' , '_stockorder.print_view.php');
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

</SCRIPT>





<?php include_once('wrap.footer.php'); ?>