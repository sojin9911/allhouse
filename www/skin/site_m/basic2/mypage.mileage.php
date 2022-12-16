<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

$page_title = "마일리지";
include_once($SkinData['skin_root'].'/member.header.php'); // 상단 헤더 출력
?>

<div id="container">
    <div id="contents_wrap">
        <div class="deposit">
          <div class="my_money">
              <p class="tit">사용 가능한 마일리지</p>
              <p class="money">0원</p>
          </div>
          <p class="my_money_p">마일리지 내역</p>
          <div class="money_search">
              <form method="get" name="frmDateSearch" id="frmDateSearch" autocomplete="off">
                  <input type="hidden" name="mode" value="">
                  <div class="select">
                      <select name="" id="">
                          <option value="">오늘</option>
                          <option value="" selected="">최근 7일</option>
                          <option value="">최근 15일</option>
                          <option value="">최근 1개월</option>
                          <option value="">최근 3개월</option>
                          <option value="">최근 6개월</option>
                          <option value="">최근 1년</option>
                      </select>
                  </div>
              </form>
            </div>
          <div class="money_history">
            <table width="100%" cellspacing="0" cellpadding="0" class="table_style1">
                <colgroup>
                    <col style="width:105px">
                    <col>
                    <col style="width:110px">
                </colgroup>
                <thead>
                <tr>
                    <th scope="col">일자</th>
                    <th scope="col">내용</th>
                    <th scope="col">마일리지</th>
                </tr>
                </thead>
                <tbody>
                <tr data-order-no="2204071321402828" data-order-handlesno="0">
                    <td>2022-04-07</td>
                    <td class="content">
                        상품구매 <br>
                    </td>
                    <td class="minus">-30,000원</td>
                </tr>
                <tr data-order-no="2204071258129049" data-order-handlesno="0">
                    <td>2022-04-07</td>
                    <td class="content">
                        상품구매 <br>
                    </td>
                    <td class="minus">-3,000원</td>
                </tr>
                <tr data-order-no="" data-order-handlesno="0">
                    <td>2022-04-07</td>
                    <td class="content">
                        환불 시 사용 예치금 환원 <br>
                    </td>
                    <td class="">+500,000원</td>
                </tr>
                </tbody>
            </table>
            <button type="button" class="deposit_more_btn btn_tbl" data-page="2">내역 더보기</button>
          </div>

        </div>
        <div id="lyReason" class="layer_wrap dn" data-remote="../mypage/layer_deposit_reason.php"></div>

        <script type="text/javascript">
            // <!--
            $(document).ready(function(){
                $('.deposit_more_btn.btn_tbl').on('click', function(){
                    gd_get_list($(this).data('page'));
                });

                // 검색기간 선택
                if ($('.check_option_inner').length) {
                    $('.check_option_inner').change(function (e) {
                        $('#frmDateSearch').submit();
                    });
                }
            });

            function gd_get_list(page) {
                var searchPeriod = parseInt($('.check_option_inner').val());
                $.get('./deposit.php', {'page' : page, 'searchPeriod' : searchPeriod}, function (data) {
                    console.log(data);
                    var addDepositList = $(data).find('.money_history tbody tr');

                    if (addDepositList.length) {
                        $('.money_history tbody').append(addDepositList);

                        $('.deposit_more_btn.btn_tbl').data('page', page + 1);
                    } else {
                        alert("더이상 마일리지 내역이 없습니다.");
                    }
                });
            }

            $(document).on("click",".btn_open_layer",function(){

                var obj = $(this);
                var target = obj.attr('href');
                var url = $(target).data('remote');
                var params = {
                    orderNo: obj.closest('tr').data('order-no'),
                    handleSno: obj.closest('tr').data('order-handlesno')
                };

                $.post(url, params, function (data) {
                    if (!_.isUndefined(data.code) && data.code == 0) {
                        alert(data.message);
                        return false;
                    }
                    $(target).removeClass('dn').empty().html(data);

                });

            });

            //-->
        </script>
    </div>
    <!-- //contents_wrap -->
</div>