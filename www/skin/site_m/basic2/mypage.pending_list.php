<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지

$page_title = "나의 보관함";
include_once($SkinData['skin_root'].'/member.header.php'); // 상단 헤더 출력

?>

<div class="c_section c_mypage">
  <div class="my_pl_wrap">
    <div id="Accordion_wrap">


      <div class="que">
        <h3><strong>입고</strong> - 현재 보관함에 가지고 있는 물건내역입니다.</h3>    
      </div>
      <div class="anw">
        <table>
          <tr>
            <th><input type="checkbox" name="" id=""></th>
          </tr>
          <tr>
            <th>브랜드명</th>
            <td>베리베리(22겨울1차)</td>
          </tr>
          <tr>
            <th>제품명</th>
            <td>벌룬곰세트</td>
          </tr>
          <tr>
            <th>사진</th>
            <td></td>
          </tr>
          <tr>
            <th>컬러</th>
            <td>백메란지</td>
          </tr>
          <tr>
            <th>단가</th>
            <td>32,000</td>
          </tr>
          <tr><th rowspan=3>발송수량</th></tr>
          <tr>
            <th class="double_th">단</th>
            <td>단1</td>
          </tr>
          <tr>
            <th class="double_th">[고미]치수(수량)</th>
            <td>7(1)</td>
          </tr>
          <tr>
            <th>소계</th>
            <td>32,000</td>
          </tr>
          <tr>
            <th>구분</th>
            <td>입고</td>
          </tr>
          <tr>
            <th>메모</th>
            <td></td>
          </tr>
          <tr>
            <th>위탁발송현황</th>
            <td></td>
          </tr>
        </table>
        <span class="popup_btn">위탁배송하기</span>
        <p class="my_for_clr"><strong>위탁배송</strong>은 <span>체크박스</span> 클릭 후 <span>위탁배송버튼</span>을 클릭하여 주세요.</p>
      </div>



      <div class="que">
        <h3><strong>입출고 기타</strong> - 입출고 기타 및 DC내역 등.</h3>
      </div>
      <div class="anw">
        <ul>
          <li><p class="my_for_clr">총 출고 금액 : <span>32,000</span></p></li>
          <li><p>출고 금액 : 32,000</p></li>
          <li><p>입출고 기타 : 0</p></li>
        </ul>
        <p class="my_for_clr que_info">
          총 출고 금액이 30만원 미만일 경우 택배비 일괄 <strong>2500원</strong> 책정되며 <strong>30만원 이상</strong>일 경우 <span class="red_clr">무료</span>로 배송이 됩니다.
        </p>
        <p class="my_for_clr que_info">발송요청은 보관함의 <span class="red_clr">모든 상품</span>을 회원정보의 주소로 <span class="red_clr">일괄배송</span> 합니다.</p>
        <div class="table_re_btn">
          <button>발송요청(일괄발송)</button>
          <button>보관요청</button>
        </div>
        <table>
          <colgroup>
            <col width="30%">
          </colgroup>
          <tr>
            <th>품번</th>
            <td>품번</td>
          </tr>
          <tr>
            <th>브랜드</th>
            <td>브랜드</td>
          </tr>
          <tr>
            <th>제품명</th>
            <td>제품명</td>
          </tr>
          <tr>
            <th>단가</th>
            <td>단가</td>
          </tr>
          <tr>
            <th>수량</th>
            <td>수량</td>
          </tr>
          <tr>
            <th>사진</th>
            <td>사진</td>
          </tr>
          <tr>
            <th>합계</th>
            <td>합계</td>
          </tr>
          <tr>
            <th>비고</th>
            <td>비고</td>
          </tr>
          <tr>
            <th>구분</th>
            <td>구분</td>
          </tr>
        </table>
      </div>



      <div class="que">
        <h3><strong>입고예정</strong> - 주문진행중 또는 예약중인 물건내역입니다.</h3>
      </div>
      <div class="anw">
        <p class="my_for_clr">
          <span class="red_clr">[!중요]</span> - 입고예정 상품은 <span>주문취소 및 변경이 불가</span>하니 꼭 참고하시기 바랍니다.
        </p>
        <table>
          <tr>
            <th>브랜드명</th>
            <td>베리베리(22겨울1차)</td>
          </tr>
          <tr>
            <th>제품명</th>
            <td>벌룬곰세트</td>
          </tr>
          <tr>
            <th>사진</th>
            <td></td>
          </tr>
          <tr>
            <th>컬러</th>
            <td>백메란지</td>
          </tr>
          <tr>
            <th>단가</th>
            <td>32,000</td>
          </tr>
          <tr><th rowspan=3>발송수량</th></tr>
          <tr>
            <th class="double_th">단</th>
            <td>단1</td>
          </tr>
          <tr>
            <th class="double_th">[고미]치수(수량)</th>
            <td>7(1)</td>
          </tr>
          <tr>
            <th>소계</th>
            <td>32,000</td>
          </tr>
          <tr>
            <th>구분</th>
            <td>입고</td>
          </tr>
          <tr>
            <th>메모</th>
            <td></td>
          </tr>
        </table>
      </div>



      <div class="que">
        <h3>
          <strong>미입고/품절</strong>
          <p class="my_for_clr">최신날짜의 출고서정보가 노출, 이전내역은 <span>[출고서]</span>에서 확인 가능</p>
        </h3>
      </div>
      <div class="anw">
        <p class="my_for_clr">
          <span class="red_clr">[!중요]</span> - 미입고 / 리오더 상품은 <span>재주문을 하지않으면 자동취소</span>로 진행되니 필요하신 상품이면 꼭 다시 <span class="red_clr">재주문</span>하시기 바랍니다.
        </p>
        <table>
          <tr>
            <th>브랜드명</th>
            <td>베리베리(22겨울1차)</td>
          </tr>
          <tr>
            <th>제품명</th>
            <td>벌룬곰세트</td>
          </tr>
          <tr>
            <th>사진</th>
            <td></td>
          </tr>
          <tr>
            <th>컬러</th>
            <td>백메란지</td>
          </tr>
          <tr>
            <th>단가</th>
            <td>32,000</td>
          </tr>
          <tr><th rowspan=3>발송수량</th></tr>
          <tr>
            <th class="double_th">단</th>
            <td>단1</td>
          </tr>
          <tr>
            <th class="double_th">[고미]치수(수량)</th>
            <td>7(1)</td>
          </tr>
          <tr>
            <th>소계</th>
            <td>32,000</td>
          </tr>
          <tr>
            <th>구분</th>
            <td>입고</td>
          </tr>
          <tr>
            <th>메모</th>
            <td></td>
          </tr>
        </table>
        <div class="table_re_btn">
          <button>미입고 상품 다시 주문</button>
          <p>[미입고 상품 다시주문] 버튼을 누르시면 장바구니에 저장됩니다.</p>
        </div>
      </div>
    </div>
    <div class="popup_bg"></div>
    <div class="popup_area">
      <h3>위탁배송 진행 내역 <span>X</span></h3>
      <table>
        <tr>
          <th>거래처명[아이디]</th>
          <th>위탁배송발송희망날짜</th>
          <th>전화번호</th>
        </tr>
        <tr>
          <td>거래처명[아이디]</td>
          <td>2022.11.30</td>
          <td>010-1111-1234</td>
        </tr>
        <tr>
          <th>핸드폰</th>
          <th>등록일</th>
          <th>통계</th>
        </tr>
        <tr>
          <td>핸드폰</td>
          <td>등록일</td>
          <td>통계</td>
        </tr>
      </table>

      <table>
        <tr>
          <th>브랜드</th>
          <th>상품명</th>
        </tr>
        <tr>
          <td><input type="text"></td><!--브랜드-->
          <td><input type="text"></td><!--상품명-->
        </tr>
        <tr>
          <th>컬러</th>
          <th>사이즈</th>
        </tr>
        <tr>
          <td><input type="text"></td><!--컬러-->
          <td><input type="text"></td><!--사이즈-->
        </tr>
        <tr>
          <th>수량</th>
          <th>요청사항</th>
        </tr>
        <tr>
          <td><input type="text"></td><!--수량-->
          <td><input type="text"></td><!--요청사항-->
        </tr>
        <tr>
          <th>받는사람 주소</th>
          <th>받는사람 이름</th>
        </tr>
        <tr>
          <td>
            <div>
              <input type="text"> <button>찾기</button>
            </div>
            <input type="text">
          </td><!--받는사람 주소-->
          <td>
            <input type="text">
          </td><!--받는사람 이름-->
        </tr>
        <tr>
          <th>받는사람 핸드폰</th>
          <th>받는사람 전화번호</th>
        </tr>
        <tr>
          <td class="for_phone">
            <input type="text"> - <input type="text"> - <input type="text">
          </td><!--받는사람 핸드폰-->
          <td class="for_phone">
            <input type="text"> - <input type="text"> - <input type="text">
          </td><!--받는사람 전화번호-->
        </tr>
      </table>
      <button class="bg_btn">저장</button>

      <div class="popup_deliv_search">
        <ul class="clearfix">
          <li class="like_th">발송구분</li>
          <li class="like_td">
            <select name="" id="">
              <option value="">::전체::</option>
            </select>
          </li>
        </ul>
        <ul class="clearfix">
          <li class="like_th">검색구분</li>
          <li class="like_td">
            <select name="" id="">
              <option value="">내용</option>
            </select>
          </li>
          <li class="margin_for_left"><input type="text"></li>
          <li><button class="bg_btn">검색</button></li>
        </ul>
      </div>

      <div class="deliv_wrap">
        <p>총 배송대상자 : <span class="cnt_clr">0</span>건</p>
        <table>
          <tr>
            <th>접수신청</th>
            <th>검수확인중</th>
            <th>발송완료</th>
            <th>발송불가</th>
            <th>운송장입력상태</th>
          </tr>
          <tr>
            <td>0 건</td>
            <td>0 건</td>
            <td>0 건</td>
            <td>0 건</td>
            <td class="deliv_wait">대기중</td>
          </tr>
        </table>
      </div>
      <div class="deliv_wrap">
        <p class="my_for_clr">택배 업체명 : <span class="red_clr">cj택배</span></p>
        <p class="my_for_clr">총 배송요청금액 : <span class="red_clr">0</span>원</p>
        <p class="my_for_clr">총 배송완료금액 : <span class="red_clr">0</span>원</p>
      </div>
      <div class="deliv_btn_wrap">
        <button>삭제</button>
        <button>목록</button>
      </div>
      <table>
        <colgroup>
          <col width="30%">
        </colgroup>
        <tr>
          <th><input type="checkbox" name="" id=""></th>
          <td></td>
        </tr>
        <tr>
          <th>상품정보</th>
          <td>상품정보</td>
        </tr>
        <tr>
          <th>받는사람 주소</th>
          <td>받는사람 주소</td>
        </tr>
        <tr>
          <th>받는사람 이름</th>
          <td>받는사람 이름</td>
        </tr>
        <tr>
          <th>받는사람 핸드폰</th>
          <td>받는사람 핸드폰</td>
        </tr>
        <tr>
          <th>요청사항</th>
          <td>요청사항</td>
        </tr>
        <tr>
          <th>진행상태</th>
          <td>진행상태</td>
        </tr>
        <tr>
          <th>등록일</th>
          <td>등록일</td>
        </tr>
        <tr>
          <th>송장번호</th>
          <td>송장번호</td>
        </tr>
        <tr>
          <th>비고</th>
          <td>비고</td>
        </tr>
      </table>
    </div><!--/popup_area-->
  </div>
</div>
<script>
  $(".que").click(function() {
    $(this).next(".anw").stop().slideToggle(300);
    $(this).toggleClass('on').siblings().removeClass('on');
    $(this).next(".anw").siblings(".anw").slideUp(300); // 1개씩 펼치기
  });

  $( '.popup_btn' ).click( function() {
    $( '.popup_area' ).addClass( 'on' );
    $( '.popup_bg' ).addClass( 'on' );
  } );
  $( '.popup_bg' ).click( function() {
    $( '.popup_area' ).removeClass( 'on' );
    $( '.popup_bg' ).removeClass( 'on' );
  } );
  $( '.popup_area h3 span' ).click( function() {
    $( '.popup_area' ).removeClass( 'on' );
    $( '.popup_bg' ).removeClass( 'on' );
  } );

</script>