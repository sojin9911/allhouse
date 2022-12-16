<div class="c_section c_shop">
	<div class="layout_fix">
		<!-- ◆공통타이틀 -->
		<div class="c_page_tit">
			<div class="title">주문완료</div>
			<!-- 단계별 페이지 -->
			<div class="c_process">
				<ul>
					<!-- 해당 페이지 hit -->
					<li><span class="num">01</span><span class="tit">장바구니</span></li>
					<li><span class="num">02</span><span class="tit">주문/결제</span></li>
					<li class="hit"><span class="num">03</span><span class="tit">주문완료</span></li>
				</ul>
			</div>
		</div>
		<!-- /공통타이틀 -->


		<!-- ◆ 주문완료 -->
		<div class="c_complete">
			<div class="complete_box">
				<span class="order_number">주문번호 : <strong><?php echo $row['o_ordernum']; ?></strong></span>
				<div class="tit">
					<?php if($row['o_paystatus'] == 'Y') { ?>
						<strong><?php echo $row['o_oname']; ?></strong>님의 주문 및 결제가 안전하게 완료되었습니다.
					<?php } else { ?>
						<strong><?php echo $row['o_oname']; ?></strong>님의 주문이 안전하게 완료되었습니다.<br />지정된 계좌로 입금해주시면 결제확인 후 배송이 진행됩니다.
					<?php } ?>
				</div>
				<div class="sub_txt">
					본 화면에서는 새로고침(F5) 또는 뒤로가기 버튼을 클릭하지 않는 것이 좋습니다.<br>
					위와 같은 동작으로 인하여 중복 결제가 발생할 수 있습니다.
				</div>
				<div class="sub_txt">회원의 경우 마이페이지에서 주문 진행 상황을 확인할 수 있습니다.</div>


			</div>
			<div class="c_btnbox none_float">
				<ul>
					<li><a href="/" class="c_btn h55 black ">홈으로</a></li>
					<?php if(is_login()) { ?>
						<!-- 로그인후 -->
						<li><a href="/?pn=mypage.main" class="c_btn h55 black line">마이페이지</a></li>
					<?php } else { ?>
						<!-- 로그인전 -->
						<li><a href="/?pn=service.guest.order.list" class="c_btn h55 black line">비회원 주문조회</a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<!-- / 주문완료 -->


	</div>
</div>