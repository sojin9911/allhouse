<!-- 슬라이드 사이트맵 -->
<div class="sitemap_wrap"><a href="#none" onclick="sitemap_ctl();" class="btn_close" title="슬라이드닫기"><span class="shape"></span></a></div>
<div class="slide_area sitemap" style="display:none;">
	
	<div class="slide_box">
		<div class="title">통합관리자 전체서비스</div>
		
		<!-- 중요버튼 -->
		<div class="btn_go_box">
			<ul>
				<li><a href="/" target="_blank" class="btn ic_home">내홈페이지</a></li>
				<li><a href="/totalAdmin/?_pcmode=chk&<?=str_replace('_mobilemode=chk','',$_SERVER['QUERY_STRING'])?>" class="btn ic_pc">PC버전보기</a></li>
				<li><a href="logout.php" class="btn ic_logout">로그아웃</a></li>
			</ul>
		</div>

		<!-- 전체메뉴 -->
		<div class="menu_box">
			<ul>

				<!-- 2뎁스 메뉴없는경우 only -->
				<li class="only">
					<a href="_product.list.php" class="depth1">상품관리</a>
					<a href="_product.list.php" class="btn_icon" title="메뉴열기"><span class="shape"></span></a>
				</li>

				<li class="open">
					<a href="_order.list.php" class="depth1" >주문관리</a>
					<a href="#none" onclick="return false;" class="toggleSlideDepth2 btn_icon" title="메뉴열기"><span class="shape"></span></a>
					<div class="depth2_box" >
						<dl>
							<dd><a href="_order.list.php" class="depth2">주문관리</a></dd>
							<dd><a href="_order.list.php?style=b" class="depth2">무통장주문관리</a></dd>
						</dl>
					</div>
				</li>

				<!-- 2뎁스 메뉴없는경우 only -->
				<li class="only">
					<a href="_request.list.php?pass_menu=inquiry" class="depth1">1:1문의</a>
					<a href="_request.list.php?pass_menu=inquiry" class="btn_icon" title="메뉴열기"><span class="shape"></span></a>
				</li>

			</ul>
		</div>

		<!-- 현재시간표기 -->
		<div class="time">
			<dl>
				<dt ID="idCurrentTime"><?=date("H:i:s")?></dt>
				<dd ID="idCurrentDay"><?=date("Y.m.d")?></dd>
			</dl>
		</div>

		<div style="height:60px;"></div>

	</div>

</div>
<!-- / 슬라이드 사이트맵 -->

<SCRIPT LANGUAGE="JavaScript">
	// --- 슬라이드 메뉴 컨트롤 ---
	function sitemap_ctl(){
		var type;
		var display = $(".sitemap").css("display");

		if(display=="block")	{ type = "none"; }
		if(display=="none")		{ type = "block"; }

		if(type=="block"){
			// 사이트맵 슬라이드
			$(".sitemap").show();
			$(".sitemap").animate({"left":"0"},500);
			// 백그라운드
			$(".sitemap_wrap").fadeIn('fast');
			$(".sitemap_wrap .btn_close").fadeIn('fast');
			$(".wrap").height($(window).height()).css("overflow","hidden");
		}
		else{
			// 사이트맵 슬라이드
			$(".sitemap").animate({"left":"-100%"},500,function(){$(".sitemap").hide();});
			// 백그라운드
			$(".sitemap_wrap").fadeOut('fast');
			$(".sitemap_wrap .btn_close").fadeOut('fast');
			$(".wrap").height("auto").css("overflow","visible");
		}
	}
	$(window).bind( 'orientationchange', function(e){ setTimeout(resize_slide(),50); });
	function resize_slide(){
		$('.sitemap, .sitemap_wrap').height( window.innerHeight );
		if( $('.sitemap').is(':visible') ) { $(".wrap").height($(window).height()).css("overflow","hidden"); }
	}
	// --- 슬라이드 메뉴 컨트롤 ---

	// --- 시간 컨트롤 ---
	function fnCurrentTime() {
		var today = new Date();

		var y = today.getFullYear();
		var mn = today.getMonth();
		var d = today.getDate();

		var h = today.getHours();
		var m = today.getMinutes();
		var s = today.getSeconds();

		mn = fnCheckTime(mn*1+1);
		m = fnCheckTime(m);
		s = fnCheckTime(s);

		$("#idCurrentDay").html(y+ "." + mn + "." + d);
		$("#idCurrentTime").html(h+ ":" + m + ":" + s);

		setTimeout(function(){fnCurrentTime()}, 1000);
	}
	function fnCheckTime(i) {
		if (i < 10) { i= "0" + i; }
		return i;
	}
	$(document).ready(function(){ 
		setInterval(function(){ fnCurrentTime() }, 1000); 
		$('.sitemap').on('click',function(e){ e.stopPropagation(); }); 
		$('.sitemap_wrap').on('click',function(){ sitemap_ctl(); });
		// 메뉴 열고 닫기
		$('.toggleSlideDepth2').on('click',function(){
			$(this).parent().toggleClass('open');
		});
	});
	// --- 시간 컨트롤 ---
</SCRIPT>