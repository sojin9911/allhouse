<?php defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지 ?>
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_mypage">
	<div class="layout_fix">
		<!-- ◆공통페이지 타이틀 -->
		<div class="c_page_tit hide">
			<!-- 마이페이지 메인으로 이동 -->
			<div class="title"><a href="/?pn=mypage.main" class="tit">마이페이지</a></div>
			<!-- 로케이션 -->
			<div class="c_location hide">
				<ul>
					<li>홈</li>
					<li>마이페이지</li>
					<?php // 내부패치 68번줄 kms 2019-11-05 ?>
					<li>배송지 관리</li>
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



<!-- 1:1 문의 내역 복사해서 만든 파일입니다 -->
			<div class="right_sec">
				<div class="right_sec_wrap">
					<?php // c_mypage_list -> c_board_list kms 2019-11-05 ?>
					<!-- ◆마이페이지 리스트 공통 -->
					<div class="c_board_list">
						<!-- 리스트 제어 -->
						<div class="c_list_ctrl">
							<div class="tit_box">
								<?php // 내부패치 68번줄 kms 2019-11-05 ?>
								<span class="tit">배송지 관리</span>
								<div class="total">배송지 관리 내역 총 <strong><?php echo number_format($TotalCount); ?></strong> 건</div>
							</div>
							<form action="/" method="get">
								<input type="hidden" name="pn" value="<?php echo $pn; ?>">
								<div class="ctrl_right">
									<select name="search_type">
										<option value="search_title,search_content"<?php echo ($search_type == 'search_title,search_content'?' selected':null); ?>>제목 + 내용</option>
										<option value="search_title"<?php echo ($search_type == 'search_title'?' selected':null); ?>>제목</option>
										<option value="search_content"<?php echo ($search_type == 'search_content'?' selected':null); ?>>내용</option>
									</select>
									<div class="search">
										<input type="text" name="search_word" value="<?php echo $search_word; ?>" class="input_search" placeholder="검색어를 입력해주세요."/>
										<input type="submit" value="" class="btn_search" title="검색"/>
									</div>
									<?php if(isset($search_word) && $search_word != '') { ?>
										<!-- 검색한 후 노출 / 검색 전 숨김 -->
										<a href="/?pn=<?php echo $pn; ?>" class="all_btn">전체목록</a>
									<?php } ?>
									<?php // 내부패치 68번줄 kms 2019-11-05 ?>
									<!-- <a href="/?pn=mypage.shipping.form&_PVSC=<?php echo $_PVSC; ?>" class="write_btn">+ 새 배송지 추가</a> -->
									<a href="#" class="write_btn">+ 새 배송지 추가</a>
								</div>
							</form>
						</div>

						<?php if(count($row) >= 1) { ?>
							<div class="c_none"><span class="gtxt">등록된 내용이 없습니다.</span></div>
						<?php } else { ?>
							<table>
								<colgroup>
									<col width="12%"/><col width="12%"/><col><col width="20%"/><col width="12%"/>
								</colgroup>
								<thead>
									<tr>
										<th scope="col">배송지이름</th>
										<th scope="col">받으실 분</th>
										<th scope="col">주소</th>
										<th scope="col">연락처</th>
										<th scope="col">수정/삭제</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td scope="col" class="center_for_font">이름</td>
										<td scope="col" class="center_for_font">받으실 분</td>
										<td scope="col">
											<P>(13434)</P>
											<P>111, 경기 성남시 분당구 판교역로</P>
										</td>
										<td scope="col">
											<P>전화번호:</P>
											<P>휴대폰 : 010-1234-1234</P>
										</td>
										<td scope="col" class="center_for_font">
											<div class="ship_btn">
												<a href="">수정</a>
												<a href="">삭제</a>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						<?php } ?>
					</div>
				</div>

			<!-- 페이지네이트 (상품목록 형) -->
				<div class="c_pagi">
					<?php echo pagelisting($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
				</div>
			</div>
		</div>
	</div>

		

  <div class="add_ship_bg"></div>
  <div class="add_ship_info">
    <h3>나의 배송지 관리 <span class="add_info_close">x</span></h3>
    <p>배송지 등록</p>
    <table>
      <tr>
        <th class="require">배송지 이름</th>
        <td><input type="text"></td>
      </tr>
      <tr>
        <th class="require">받으실 분</th>
        <td><input type="text"></td>
      </tr>
      <tr>
        <th class="require">받으실 곳</th>
        <td>
          <div>
            <input type="text"> <button type="button">우편번호 검색</button>
          </div>
          <div class="detail_add">
            <input type="text"> <input type="text">
          </div>
        </td>
      </tr>
      <tr>
        <th>전화번호</th>
        <td><input type="text"></td>
      </tr>
      <tr>
        <th class="require">휴대폰번호</th>
        <td><input type="text"></td>
      </tr>
    </table>
    <div class="add_si_default">
      <input type="checkbox" name="" id="defaultF1">
      <label for="defaultF1" class="check_s">기본 배송지로 설정합니다.</label>
    </div>
    <div class="add_si_btn">
      <button type="button" class="cancle">취소</button>
      <button type="button" class="save_info">저장</button>
    </div>
  </div>
</div>
<!-- /공통페이지 섹션 -->


<script type="text/javascript">
	$(document).on('click', '.js_detail_btn', function(e) {
		e.preventDefault();
		var su = $(this);
		var _uid = su.closest('tr').data('uid');
		var _visible = $('.js_detail_view[data-uid='+_uid+']').is(':visible');
		$('.js_detail_view').hide();
		$('.js_view').removeClass('if_open');
		$('.js_detail_btn.arrow_btn').attr('title', '열기');
		if(_visible === false) {
			$('.js_detail_view[data-uid='+_uid+']').show();
			su.closest('tr').find('.js_detail_btn.arrow_btn').attr('title', '닫기');
			$('.js_view[data-uid='+_uid+']').addClass('if_open');
		}
	});


	// 문의삭제
	function inquiry_del(uid) {
		if(confirm("정말 삭제하시겠습니까?")) {
			$.ajax({
				url: "<?php echo OD_PROGRAM_URL; ?>/mypage.inquiry.pro.php",
				cache: false,
				type: "POST",
				data: "_mode=delete&uid=" + uid ,
				success: function(data){
					if( data == "no data" ) {
						alert('등록하신 글이 아닙니다.');
					}
					else {
						alert('정상적으로 삭제하였습니다.');
						location.reload();
					}
				}
			});
		}
	}



  $(".write_btn").click(function () {
    $('html, body').css({'overflow': 'hidden', 'height': '100%'});
    console.log('sssss');
    if ($(".add_ship_bg").hasClass("on")) {
      $(".add_ship_bg").removeClass("on");
      $(".add_ship_info").removeClass("on");
    } else {
      $(".add_ship_bg").addClass("on");
      $(".add_ship_info").addClass("on");
    }
  });
 
  $(".add_info_close").click(function () {
		$('html, body').css({'overflow': 'inherit', 'height': 'inherit'});
    if ($(".add_ship_bg").hasClass("on")) {
      $(".add_ship_bg").removeClass("on");
      $(".add_ship_info").removeClass("on");
    } 
  })
	$(".add_si_btn .cancle").click(function () {
		$('html, body').css({'overflow': 'inherit', 'height': 'inherit'});
    if ($(".add_ship_bg").hasClass("on")) {
      $(".add_ship_bg").removeClass("on");
      $(".add_ship_info").removeClass("on");
    } 
  })
</script>