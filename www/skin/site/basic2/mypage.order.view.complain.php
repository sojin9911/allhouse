<!-- 팝업 / 팝업 사이즈는 컨텐츠 마다 별도 -->
<div class="c_pop order_complain_page" style="display:none;width:auto;height:auto;">

		<form ID="frm_complain_page" name="frm_complain_page" action="<?php echo OD_PROGRAM_URL; ?>/mypage.order.pro.php" target="common_frame" method="post" onsubmit="return complain_func(this)">
		<input type="hidden" name="opuid" id="opuid" value="" />
		<input type="hidden" name="_mode" value="complain" />	

			<!-- 크기에 따라 자동으로 가운데 정렬되도록 -->
			<div class="pop_wrap" style="width:600px; margin-left:-300px; margin-top:-400px">
				<!-- 팝업창 기본타이틀 -->
				<div class="pop_title">
					교환/반품 신청
					<a href="#none" onclick="return false;" class="btn_close close" title="닫기"></a>
				</div>

				
				<!-- 팝업창 내용 -->
				<div class="conts_box">
					<div class="inner_box">
						<!-- 여기에 필요한 내용 들어감 -->

						<div class="c_group_tit"><span class="tit">교환/반품 신청내용</span></div>
						<div class="c_form">
							<table>
								<colgroup>
									<col width="150"><col width="*"><col width="150"><col width="*">
								</colgroup>
								<tbody>
									<tr>
										<th class="ess"><span class="tit ">신청상품</span></th>
										<td colspan="3">
											<input type="text" name="complan_pname"  id="complan_pname" class="input_design" value="" readonly  />
										</td>
									</tr>
									<tr>
										<th class="ess"><span class="tit ">신청내용</span></th>
										<td colspan="3">
											<div class="textarea_box"><textarea name="complain_content" rows="3" style="" class="textarea_design" placeholder="관리자에게 전달하실 내용이 있다면 입력해주세요."></textarea></div>
											<div class="tip_txt">위 정보를 다시한번 정확하게 확인 후 신청해주시면, 관리자 확인 후 처리됩니다.</div>
										</td>
									</tr>
								</tbody> 
							</table>
						</div>
					</div>
				</div>


				<!-- 버튼 컬러 변경 불가 -->
				<div class="c_btnbox">
					<ul>
						<li><span class="c_btn h30 black"><input type="submit" value="교환/반품 신청"></span></li>
						<li><a href="#none" onclick="return false;" class="c_btn h30 light line close">닫기</a></li>
					</ul>
				</div>
			</div>

		</form>

	<!-- 팝업창 배경 -->
	<div class="bg"></div>
</div>






<script>
	function complain_func(frm) {

        <?php 
            // ![LCY] 2020-07-13 -- 네이버페이 사용자 주문취소 비활성 패치  --
            if( $row['npay_order'] == 'Y'){ 
                echo 'alert("네이버페이의 경우 교환/반품 신청은 고객센터에 문의해 주세요."); return false;';
            }
        ?>

		if(!frm.opuid.value) {alert('오류가 발생하였습니다. 새로고침 후 다시 시도해주세요.');return false;}
		if(!confirm('교환/반품 신청을 하시겠습니까?')) return false;

		return true;
	}

	$(document).ready(function(){
		// - 아이디 찾기 폼체크
		$("#frm_complain_page").validate({
			rules: {
				complain_content: { required: true }
			},
			messages: {
				complain_content: { required: "내용을 입력해 주세요." }
			}
		});
	});
		
	// - 교환반품 박스 open ---
	function complain_view(pname,opuid){
		$("#opuid").val(opuid);
		$("#complan_pname").val(pname);

		$('.order_complain_page').lightbox_me({
			centered: true, 
			closeEsc: true,
			onLoad: function() { 
			}
		});
	}
</script>
