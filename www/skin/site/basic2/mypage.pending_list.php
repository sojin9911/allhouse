<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>

<!-- ◆공통페이지 섹션 -->
<div class="c_section c_board">
	<div class="layout_fix">
		<!-- ◆공통페이지 타이틀 -->
		<div class="c_page_tit hide">
			<div class="title"><a href="/?pn=mypage.main" class="tit">마이페이지</a></div>
			<!-- 로케이션 -->
			<div class="c_location hide">
				<ul>
					<li>홈</li>
					<li>마이페이지</li>
					<li>상품문의</li>
				</ul>
			</div>
		</div>
		<!-- / 공통페이지 타이틀 -->

		<div class="mypage_section">
			<div class="left_sec">
				<!-- ◆공통탭메뉴 -->
				<?php include_once($SkinData['skin_root'].'/member.header.php'); // -- 공통해더 --  ?>
				<!-- / 공통탭메뉴 -->
			</div>

		
			<div class="right_sec">	
				<div class="right_sec_wrap">			
					<div class="board_zone_tit">
						<h2>나의 보관함</h2>
					</div>
					<div class="my_locker">
						<div class="table_receive">
							<h3>
								<strong>입고</strong> - 현재 보관함에 가지고 있는 물건내역입니다. 
								<span class="popup_btn go_consign">위탁배송하기</span>
							</h3>
							<table>
								<tr>
									<th rowspan=2></th>
									<th rowspan=2>브랜드명</th>
									<th rowspan=2>제품명</th>
									<th rowspan=2>사진</th>
									<th rowspan=2>컬러</th>
									<th rowspan=2>단가</th>
									<th colspan=2>발송수량</th>
									<th rowspan=2>소계</th>
									<th rowspan=2>구분</th>
									<th rowspan=2>메모</th>
									<th rowspan=2>위탁발송현황</th>
								</tr>
								<tr>
									<th>단</th>
									<th>[고미]치수(수량)</th>
								</tr>
								<tr>
									<td><input type="checkbox" name="" id=""></td>
									<td>베리베리(22겨울1차)</td>
									<td>벌룬곰세트</td>
									<td></td>
									<td>백메란지</td>
									<td>32,000</td>
									<td></td>
									<td>7 (1)</td>
									<td>32,000</td>
									<td>입고</td>
									<td></td>
									<td></td>
								</tr>
							</table>
							<p><strong>위탁배송</strong>은 <span>체크박스</span> 클릭 후 우측 <span>위탁배송버튼</span>을 클릭하여 주세요.</p>
						</div>

						<div class="table_receive_etc">
							<h3><strong>입출고 기타</strong> - 입출고 기타 및 DC내역 등. 
							<span class="popup_btn">위탁배송진행내역</span></h3>
							<table>
								<tr>
									<th>품번</th>
									<th>브랜드</th>
									<th>제품명</th>
									<th>단가</th>
									<th>수량</th>
									<th>사진</th>
									<th>합계</th>
									<th>비고</th>
									<th>구분</th>
								</tr>
								<tr>
									<td>품번</td>
									<td>브랜드</td>
									<td>제품명</td>
									<td>단가</td>
									<td>수량</td>
									<td>사진</td>
									<td>합계</td>
									<td>비고</td>
									<td>구분</td>
								</tr>
							</table>
							<ul>
								<li>총 출고 금액 : <span>32,000</span> 원</li>
								<li>출고 금액 : <strong>32,000</strong> 원</li>
								<li>입출고 기타 : <strong>0</strong> 원</li>
							</ul>
							<p>총 출고 금액이 30만원 미만일 경우 택배비 일괄 <strong>2500원</strong> 책정되며 <strong>30만원 이상</strong>일경우 <span class="red_clr">무료</span>로 배송이 됩니다.</p>
							<p>발송요청은 보관함의 <span class="red_clr">모든 상품</span>을 회원정보의 주소로 <span class="red_clr">일괄배송</span>합니다.</p>

							<div class="table_re_btn">
								<button class="sj_btn">발송요청(일괄발송)</button>
								<button class="sj_btn">보관요청</button>
							</div>
						</div>

						<div class="table_receive_expect">
							<h3>
								<strong>입고예정</strong> - 주문진행중 또는 예약중인 물건내역입니다.
								<p><span class="red_clr">[!중요]</span> - 입고예정 상품은 <span>주문취소 및 변경이 불가</span>하니 꼭 참고하시기 바랍니다.</p>
							</h3>
							<table>
								<tr>
									<th rowspan=2>브랜드명</th>
									<th rowspan=2>제품명</th>
									<th rowspan=2>사진</th>
									<th rowspan=2>컬러</th>
									<th rowspan=2>단가</th>
									<th colspan=2>발송수량</th>
									<th rowspan=2>소계</th>
									<th rowspan=2>구분</th>
									<th rowspan=2>메모</th>
								</tr>
								<tr>
									<th>단</th>
									<th>[고미]치수(수량)</th>
								</tr>
								<tr>
									<td>베리베리(22겨울1차)</td>
									<td>벌룬곰세트</td>
									<td></td>
									<td>백메란지</td>
									<td>32,000</td>
									<td></td>
									<td>7(1)</td>
									<td>32,000</td>
									<td>입고</td>
									<td></td>
								</tr>
							</table>
						</div>



						<div class="table_sold_out">
							<h3>
								<strong>미입고/품절</strong> - 최신날짜의 출고서정보가 노출, 이전내역은 <span>[출고서]</span>에서 확인가능
								<p><span class="red_clr">[!중요]</span> - 미입고 /리오더 상품은 <span>재주문을 하지않으면 자동취소</span>로 진행되니 필요하신 상품이면 꼭 다시 <span class="red_clr">재주문</span>하시기 바랍니다.</p>
							</h3>
							<table>
								<tr>
									<th rowspan=2>브랜드명</th>
									<th rowspan=2>제품명</th>
									<th rowspan=2>사진</th>
									<th rowspan=2>컬러</th>
									<th rowspan=2>단가</th>
									<th colspan=2>발송수량</th>
									<th rowspan=2>소계</th>
									<th rowspan=2>구분</th>
									<th rowspan=2>메모</th>
								</tr>
								<tr>
									<th>단</th>
									<th>[고미]치수(수량)</th>
								</tr>
								<tr>
									<td>베리베리(22겨울1차)</td>
									<td>벌룬곰세트</td>
									<td></td>
									<td>백메란지</td>
									<td>32,000</td>
									<td></td>
									<td>7(1)</td>
									<td>32,000</td>
									<td>입고</td>
									<td></td>
								</tr>
							</table>
							<div class="table_re_btn">
								<button class="sj_btn">미입고 상품 다시 주문</button>
								<p>[미입고 상품 다시주문] 버튼을 누르시면 장바구니에 저장됩니다.</p>
							</div>
						</div>
					</div>


					<div class="popup_bg"></div>
					<div class="consign_area">
						<h2>위탁배송 진행 내역 <span>X</span></h2>
						<table>
							<tr>
								<th>거래처명[아이디]</th>
								<th>위탁배송발송희망일자</th>
								<th>전화번호</th>
								<th>핸드폰</th>
								<th>등록일</th>
								<th>통계</th>
							</tr>
							<tr>
								<td>[]</td>
								<td>[접수중]</td>
								<td></td>
								<td></td>
								<td>등록일</td>
								<td></td>
							</tr>
						</table>

						<div class="for_write_tb">
							<table>
								<tr>
									<th>브랜드</th>
									<th>상품명</th>
									<th>컬러</th>
									<th>사이즈</th>
									<th>수량</th>
								</tr>
								<tr>
									<td><input type="text"></td>
									<td><input type="text"></td>
									<td><input type="text"></td>
									<td><span class="red_clr">입고수량:</span><input type="text"></td>
									<td><input type="text"></td>
								</tr>
							</table>
							<table>
								<tr>
									<th>받는사람 주소</th>
									<th>받는사람 이름</th>
									<th>받는사람 핸드폰</th>
									<th>받는사람 전화번호</th>
									<th>요청사항</th>
								</tr>
								<tr>
									<td class="write_address">
										<div>
											<input type="text"> <button>찾기</button>
										</div>
										<input type="text">
									</td>
									<td><input type="text"></td>
									<td class="write_phone">
										<input type="text"> - <input type="text"> - <input type="text">
									</td>
									<td class="write_phone">
										<input type="text"> - <input type="text"> - <input type="text">
									</td>
									<td><input type="text"></td>
								</tr>
							</table>
						</div>
				
						<div class="table_re_btn"><button class="sj_btn">저장</button></div>

						<div class="deliv_area">
							<div class="deliv_search">
								<ul class="clearfix">
									<li class="like_th">발송구분</li>
									<li class="like_td">
										<select name="" id="">
											<option value="all">::전체::</option>
										</select>
									</li>
								</ul>
								<ul class="clearfix">
									<li class="like_th">검색구분</li>
									<li class="like_td">
										<select name="" id="">
											<option value="">내용</option>
										</select>
										<input type="text">
										<button>검색</button>
									</li>
								</ul>
							</div>

							<ul class="deliv_info">
								<li>
									<p>총 배송대상자  : <span class="cnt_clr"></span>건 (접수신청: 건, 검수확인중: 건, 발송완료 : 건, 발송불가 : 건, 운송장입력상태 : <span class="deliv_wait">대기중</span> )</p>
								</li>
								<li>
									<p>택배업체명 : <span class="red_clr">cj택배</span></p>
									<p>총 배송요청금액 : <span class="red_clr">0</span> 원, 총 배송완료금액 : <span class="red_clr">0</span> 원</p>
									<div>
										<button class="sj_btn">삭제</button>
										<button class="sj_btn">목록</button>
									</div>
								</li>
							</ul>

							<table>
								<colgroup>
									<col width="2%">
								</colgroup>
								<tr>
									<th rowspan=2></th>
									<th rowspan=2>상품정보<br>(+ 일괄배송추가, -삭제)</th>
									<th colspan=3>받는사람</th>
									<th rowspan=2>요청사항</th>
									<th rowspan=2>진행상태</th>
									<th rowspan=2>등록일</th>
									<th>송장번호</th>
								</tr>
								<tr>
									<th>주소</th>
									<th>이름</th>
									<th>핸드폰(전화번호)</th>
									<th>비고</th>
								</tr>
								<tr>
									<td rowspan=2><input type="checkbox" name="" id=""></td>
									<td rowspan=2>2</td>
									<td rowspan=2>3</td>
									<td rowspan=2>4</td>
									<td rowspan=2>5</td>
									<td rowspan=2>6</td>
									<td rowspan=2>7</td>
									<td rowspan=2>8</td>
									<td>9</td>
								</tr>
								<tr>
									<td>10</td>
								</tr>
							</table>
						</div>
					</div>
							
							 
				</div><!--right_sec_wrap-->
			</div>
		
		</div>
				

			
	</div>
		<!-- /공통페이지 섹션 -->
</div>

<script>
      $( document ).ready( function() {
        $( '.go_consign' ).click( function() {
          $( '.consign_area' ).addClass( 'on' );
          $( '.popup_bg' ).addClass( 'on' );
        } );
				$( '.popup_bg' ).click( function() {
          $( '.consign_area' ).removeClass( 'on' );
          $( '.popup_bg' ).removeClass( 'on' );
        } );
				$( '.consign_area h2 span' ).click( function() {
          $( '.consign_area' ).removeClass( 'on' );
          $( '.popup_bg' ).removeClass( 'on' );
        } );
      } );
    </script>



