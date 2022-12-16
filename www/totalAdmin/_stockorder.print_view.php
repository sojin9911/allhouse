<?php
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// - 넘길 변수 설정하기 ---
$_PVS = ""; // 링크 넘김 변수
foreach(array_filter(array_unique(array_merge($_POST,$_GET))) as $key => $val) { $_PVS .= "&$key=$val"; }
$_PVSC = enc('e' , $_PVS);
// - 넘길 변수 설정하기 ---

$ordernum_list = implode("','",array_keys($chk_ordernum)); // _mode == mass_print

// 추가파라메터
if(!$arr_param) $arr_param = array();

$s_query = " from smart_order_product as op where op_oordernum in ('{$ordernum_list}') ";

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
    order by {$st} {$so}
";
//echo $que;
$res = _MQ_assoc($que);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>주문 발주 인쇄하기</title>
<!-- 문서모드고정 (문서모드7이되서 깨지는경우가있음) -->
<link rel="apple-touch-icon-precomposed" href="images/homeicon.png" />
<meta name="format-detection" content="telephone=no" />
<SCRIPT src="/include/js/jquery-1.11.2.min.js"></SCRIPT>
<link href="//ssip.co.kr/totalAdmin/css/totalAdmin.css" rel="stylesheet" type="text/css">
<style type="text/css">
    html {width:100%;}
    body {margin:0; padding:20px; background:#fff;}
    body,p,pre,form,span,div,table,td,ul,ol,li,dl,dt,dd,input,textarea,label,button {color:#888; word-wrap:break-word; word-break:keep-all; font-family:"맑은고딕", sans-serif ; font-size:13px; font-weight:400; color:#333;}
    b,strong {word-wrap:break-word; word-break:break-all; font-family:inherit; font-size:inherit; font-weight:600; letter-spacing:0px;}

    p,form,span,h1,h2,h3,h4,h5,h6 {margin:0; padding:0; font-weight:normal}
    div,table {margin:0; padding:0; border-spacing:0; border-collapse:collapse; border:0px none; }
    ul,ol,li,td,dl,dt,dd {margin:0; padding:0; list-style:none;}
    em,i {font-style:normal}
    a,span {display:inline-block;}
    img {border:0;}
    span,div,a,b,strong,label {color:inherit; font-size:inherit; font-weight:inherit}
    table caption {width:0px; height:0px; font-size:0; visibility:hidden; }
    table {width:100%;}

    .wrap {max-width:1000px; margin:0 auto}

    .print_title {text-align:center; font-size:27px; color:#666; border:1px solid #ccc; padding:10px 0 15px 0}
    .print_title strong {color:#000;}

    /* SSJ : 부분취소 표시 : 2020-09-04 */
    tr.cancel td {position:relative; color:#999;}
    tr.cancel td:before {content: "";position: absolute;left: 0;top: 48%;width: 100%;height: 1px;border-top: 1px solid #333;}

    .c_tag {float:initial !important; margin-bottom:2px !important;}
    .c_tag.cart {color:#666 !important; background:#F4FFF4; border:1px solid #008000; width:75px !important; padding:0px !important; }
    .c_tag.manual {color:#666 !important; background:#FFE8EF; border:1px solid #FF4080; width:75px !important; padding:0px !important; }
    .order_item .option {width:100%;}
    .table_option {}
    .table_list td {padding:3px 10px !important;}
    .table_list td.tt {width:150px; background:#FFFFEA;}
    .table_list td.th {width:50px; background:#f5f5f5;}
    .table_list td.td {width:60px; text-align:left;}
    .order_item .option {padding: 2px 0 5px !important;}
    .order_item .option:before {background:initial !important;}
    .order_item .title {padding:6px 0 10px; font-size:14px;}

   input {border:0px}
</style>
</head>
<body>
<div class="wrap">


<!-- ◆ 프린트 타이틀 -->
<div class="print_title"><strong><?php echo $siteInfo['s_adshop']; ?></strong> 발주 내역서</div>
<!-- / 프린트 타이틀 -->

    <table class="table_list">
        <colgroup>
            <col width="45"><col width="100"><col width="*">
        </colgroup>
        <thead>
            <tr>
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
                                                <td class="th">재고</td>
                                                <td class="td">
                                                   <span class="t_black"><?php echo number_format($sv['stock_cnt']); ?>개</span>
                                                </td>
                                                <td class="th">주문</td>
                                                <td class="td">
                                                   <span class="t_black"><?php echo number_format($sv['cnt']); ?>개</span>
                                                </td>
                                                <td class="th">발주</td>
                                                <td class="td">
                                                    <span class="t_black"><?php echo $sv['orderstock_cnt']; ?>개</span>
                                                </td>
                                                <td class="th">입고</td>
                                                <td class="td">
                                                    <span class="t_black"><?php echo $sv['instock_cnt']; ?>개</span>
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
                                                <td class="th">재고</td>
                                                <td class="td">
                                                   <span class="t_black"><?php echo number_format($pinfo['stock_cnt']); ?>개</span>
                                                </td>
                                                <td class="th">주문</td>
                                                <td class="td">
                                                    <span class="t_black"><?php echo number_format($pinfo['cnt']); ?>개</span>
                                                </td>
                                                <td class="th">발주</td>
                                                <td class="td">
                                                    <span class="t_black"><?php echo $sv['orderstock_cnt']; ?>개</span>
                                                </td>
                                                <td class="th">입고</td>
                                                <td class="td">
                                                    <span class="t_black"><?php echo $sv['instock_cnt']; ?>개</span>
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
                                                    <td class="th">재고</td>
                                                    <td class="td">
                                                       <span class="t_black"><?php echo number_format($sv['stock_cnt']); ?>개</span>
                                                    </td>
                                                    <td class="th">주문</td>
                                                    <td class="td">
                                                        <span class="t_black"><?php echo number_format($sv['cnt']); ?>개</span>
                                                    </td>
                                                    <td class="th">발주</td>
                                                    <td class="td">
                                                        <span class="t_black"><?php echo $sv['orderstock_cnt']; ?>개</span>
                                                    </td>
                                                    <td class="th">입고</td>
                                                    <td class="td">
                                                        <span class="t_black"><?php echo $sv['instock_cnt']; ?>개</span>
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
                                                    <td class="th">재고</td>
                                                    <td class="td">
                                                       <span class="t_black"><?php echo number_format($pinfo['stock_cnt']); ?>개</span>
                                                    </td>
                                                    <td class="th">주문</td>
                                                    <td class="td">
                                                        <span class="t_black"><?php echo number_format($pinfo['cnt']); ?>개</span>
                                                    </td>
                                                    <td class="th">발주</td>
                                                    <td class="td">
                                                        <span class="t_black"><?php echo $sv['orderstock_cnt']; ?>개</span>
                                                    </td>
                                                    <td class="th">입고</td>
                                                    <td class="td">
                                                        <span class="t_black"><?php echo $sv['instock_cnt']; ?>개</span>
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


</div>

<SCRIPT LANGUAGE="JavaScript">
    $(document).ready(function() {
        print();
    });
</SCRIPT>

</body>
</html>