<?php include_once('inc.header.php'); ?>
<body>
<div class="wrap">


	<!-- ● 메인 -->
	<div class="main_section">

		<div class="myinfo">

			<!-- 상단영역 -->
			<div class="myinfo">
				<!-- <span class="imgicon">
					<span class="icon1"></span>
					<span class="icon2"></span>
				</span> -->
				<div class="title_en">TOTAL ADMIN</div>
				<dl>
					<dt><?=$siteInfo['s_adshop']?></dt>
					<dd><a href="http://<?=$_SERVER['HTTP_HOST']?>" target="_blank" class="link"><?=$_SERVER['HTTP_HOST']?></a></dd>
				</dl>
			</div>

			<div class="btn_go_box">
				<ul>
					<li><a href="/" target="_blank" class="btn ic_home">내홈페이지</a></li>
					<li><a href="/totalAdmin/?_pcmode=chk&<?=str_replace('_mobilemode=chk','',$_SERVER['QUERY_STRING'])?>" class="btn ic_pc">PC버전보기</a></li>
					<li><a href="logout.php" class="btn ic_logout">로그아웃</a></li>
				</ul>
			</div>

		</div>

		<!-- 메인에서 메뉴바로가기 1차메뉴 -->
		<div class="admin_menu">
			<ul>
				<li><a href="_product.list.php" class="menu"><img src="<?=PATH_MOBILE_TOTALADMIN?>/images/mainmenu1.png" alt="대메뉴아이콘" /><span class="txt">상품관리</span></a></li>
				<li><a href="_order.list.php" class="menu"><img src="<?=PATH_MOBILE_TOTALADMIN?>/images/mainmenu2.png" alt="대메뉴아이콘" /><span class="txt">주문관리</a></span></li>
				<li><a href="_request.list.php?pass_menu=inquiry" class="menu"><img src="<?=PATH_MOBILE_TOTALADMIN?>/images/mainmenu3.png" alt="대메뉴아이콘" /><span class="txt">1:1문의</a></span></li>

			</ul>
		</div>
		<!-- / 메인에서 메뉴바로가기 -->


		<!-- 메인간략통계 : 사용안함 -->
		<div class="admin_state">
			<ul>


				<?php
					// --- 최근 30일 총 매출액 ---
					$TotalSalesData = _MQ("
						select
							sum(( (op_price*op_cnt) )) as total
						from
							smart_order as o left join
							smart_order_product as op on(o.o_ordernum = op.op_oordernum)
						where
							o_paystatus='Y'  and o_canceled='N' and
							DATE_ADD( DATE(o_rdate) , INTERVAL + 1 month) >= CURDATE()
					");
				?>
				<li>
					<div class="title_box"><span class="txt">최근 한달 총매출액</span></div>
					<span class="value"><span class="unit">￦</span><?=number_format($TotalSalesData['total'])?></span>
				</li>

				<?php
                    // --- 최근 일주일 1:1문의 ---
                    $TotalRequestCnt = get_request_cnt("inquiry"," and DATE_ADD( DATE(r_rdate) , INTERVAL + 7 day) >= CURDATE() "); // 전체
                    $TotalRequestReadyCnt = get_request_cnt("inquiry"," and r_status ='답변대기' and DATE_ADD( DATE(r_rdate) , INTERVAL + 7 day) >= CURDATE() "); // 답변대기
				?>
				<li>
					<div class="title_box"><span class="txt">최근 일주일 1:1문의</span></div>
					<span class="value"><?=number_format($TotalRequestCnt)?> <a href="_request.list.php?pass_menu=inquiry" class="btn_ready">답변대기 <?=number_format($TotalRequestReadyCnt)?></a></span>
				</li>

				<li>
					<div class="title_box"><span class="txt">등록된 총 상품개수</span></div>
					<span class="value"><?=number_format(DivisionProduct())?></span>
				</li>


			</ul>
		</div>

		<div class="user_guide_box">
			관리자페이지 모바일 버전은 중요한 기능을 빠르게 관리하기 위한 제한적인 서비스를 제공하고 있습니다. 모든 관리를 위해서는 PC버전을 이용해주세요.
		</div>

	</div>
	<!-- / 메인 -->



	<!-- 푸터 -->
	<div class="footer">
		<div class="copyright">&copy; <?php echo $siteInfo['s_adshop']; ?>. ALL RIGHTS RESERVED.</div>
	</div>
	<!-- /푸터 -->
</div>
</body>
<?php include_once('inc.footer.php'); ?>