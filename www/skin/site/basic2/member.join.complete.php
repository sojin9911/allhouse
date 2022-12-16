
<!-- ◆공통페이지 섹션 -->
<div class="c_section c_member">
	<div class="layout_fix">
		<!-- ◆공통페이지 타이틀 -->
		<div class="c_page_tit">
			<div class="title">회원가입</div>
			<div class="c_process">
				<ul>
                    <!-- 해당 페이지일 경우 li hit클래스 추가 / li 없을때 num 숫자 순서대로 넘버링 -->
                    <?php if($siteInfo['s_join_auth_use'] == 'Y' && false) { // 본인인증 사용시 ?>
                        <li><span class="num">01</span><span class="tit">약관동의</span></li>
                        <li><span class="num">02</span><span class="tit">본인인증</span></li>
                        <li><span class="num">03</span><span class="tit">정보입력</span></li>
                        <li class="hit"><span class="num">04</span><span class="tit">가입완료</span></li>
                    <?php } else { ?>
                        <li><span class="num">01</span><span class="tit">약관동의</span></li>
                        <li><span class="num">02</span><span class="tit">정보입력</span></li>
                        <li class="hit"><span class="num">03</span><span class="tit">가입완료</span></li>
                    <?php } ?>
                </ul>
			</div>
		</div>
		<!-- /공통페이지 타이틀 -->


		<!-- ◆회원가입완료 -->
		<div class="c_complete">
			<div class="complete_box">
				<div class="sub_txt">회원가입이 완료되었습니다.<br/>저희 사이트의 모든 서비스를 이용하실 수 있습니다.</div>
				<div class="tit"><strong><?php echo $mem_info['in_name']; ?></strong>님의 회원가입을 진심으로 환영합니다.</div>
			</div>
			<div class="c_btnbox ">
				<ul>
					<li><a href="/" class="c_btn h55 black ">홈으로</a></li>
					<li><a href="/?pn=mypage.main" class="c_btn h55 black line">마이페이지</a></li>
				</ul>
			</div>
		</div>
		<!-- /회원가입완료 -->
	</div>
</div>
<!-- /공통페이지 섹션 -->