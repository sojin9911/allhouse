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
          <!--보낸 쪽지함 start-->
<div class="content">
<link type="text/css" rel="stylesheet" href="/home/allhouse/www/skin/site/basic2/css/c_design.css">
<div class="">
	<div class="board_zone_tit">
		<h2>
				보낸 쪽지함
		</h2>
	</div>
    </div>
    <div class="board_zone_cont">
        <div class="board_zone_view">

            <div class="board_view_tit">
                <h3>
                    전체테스트
                </h3>
            </div>
            <div class="board_view_info">
                <span class="view_info_idip">
                    <strong></strong>
                </span>
                <span class="view_info_day">
                    <em></em>
                </span>
            </div>
            <!-- //board_view_info -->



            <div class="board_view_content">


                <div class="seem_cont">
                    <div style="margin:10px 0 10px 0">
                        <p>전체테스트&nbsp;</p>
                    </div>
                </div>
                <!-- //seem_cont -->


            </div>
            <!-- //board_view_content -->


            <!-- //board_view_comment -->

        </div>
        <!-- //board_zone_view -->

        <div class="btn_right_box">
			<a href="write.php?beforeSno=50256" class="btn_board_list" style="display: inline-block; box-sizing: border-box; vertical-align: top;"><strong>답장</strong></a>
            <button type="button" class="btn_board_list" onclick="history.go(-1);"><strong>목록</strong></button>
        </div>

    </div>
	<!-- //board_zone_cont -->

</div>
<!--보낸 쪽지함 end-->



            </div>
      </div>
				<!-- /상품평가/상품문의 리스트 -->

				<!-- 페이지네이트 (상품목록 형) -->
				<div class="c_pagi">
					<?php echo pagelisting($listpg, $Page, $listmaxcount, "?${_PVS}&listpg="); ?>
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
			var _smode = ($('.js_detail_view[data-uid='+_uid+']').attr('data-hit') == 'false'?'update':'nocount');
			if(_smode == 'update') {
				$.ajax({
					data: {
						_mode: 'eval_hit',
						_smode: _smode,
						_uid: _uid
					},
					type: 'POST',
					cache: false,
					url: '<?php echo OD_PROGRAM_URL; ?>/_pro.php',
					success: function(data) {
						// hit수 증가
						var _num = su.closest('tr').find('.js_eval_hit').text();
						_num.replace(/[^0-9]/g, '')*1;
						_num = _num*1;
						su.closest('tr').find('.js_eval_hit').text(number_format(_num+1));

						// 중복 hit차단
						$('.js_detail_view[data-uid='+_uid+']').attr('data-hit', 'true');
					}
				});
			}
		}
	});


	// 리뷰 삭제
	function eval_del(uid) {

		if(confirm("정말 삭제하시겠습니까?")) {
			$.ajax({
				url: "<?php echo OD_PROGRAM_URL; ?>/product.eval.pro.php",
				cache: false,
				type: "POST",
				data: "_mode=delete&uid=" + uid ,
				success: function(data){
					if( data == "no data" ) {
						alert('등록하신 글이 아닙니다.');
					}
					else if( data == "is reply" ) {
						alert('댓글이 있으므로 삭제가 불가합니다.');
					}
					else {
						alert('정상적으로 삭제하였습니다.');
						location.reload();
					}
				}
			});
		}
	}
</script>
