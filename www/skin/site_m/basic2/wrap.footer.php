<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
?>
<!-- ******************************************
     FOOTER (공통)
  -- ****************************************** -->
<div class="footer">
	<div class="top_menu clearfix">
		<div class="foot_tm_cs">
			<div class="title_box"><span class="tit">CS CENTER</span></div>
			<div class="cs_info">
				<div class="tel"><a href="tel:<?php echo $siteInfo['s_glbtel']; ?>"><?php echo $siteInfo['s_glbtel']; ?></a></div>
			</div>
			<div class="cs_time">
				<?php echo nl2br($siteInfo['s_cs_info']); ?>
			</div>
		</div>
		<div class="foot_tm_bank">
			<div class="title_box"><span class="tit">BANK INFO</span></div>
			<?php
				$NoneBank = _MQ_assoc(" select * from smart_bank_set where (1) order by bs_idx asc ");
				if(count($NoneBank) <= 0) $NoneBank = array();
				foreach($NoneBank as $k=>$v) {
			?>
			<ul>
				<li class="ftb_onner">
					<div class="name">예금주 : <strong><?php echo $v['bs_user_name']; ?></strong></div>
				</li>
				<li class="ftb_bank_info">
					<span class="name"><strong><?php echo $v['bs_bank_name']; ?></strong></span>
					<span class="number"><?php echo $v['bs_bank_num']; ?></span>
				</li>
			</ul>
			<?php } ?>
		</div>
	</div>
	<div class="top_menu1 clearfix">
		<div class="foot_tm_cs1">
			<div class="top_menu_btn">
				<a href="/?_pcmode=chk&<?php echo str_replace('_mobilemode=chk', '', $_SERVER['QUERY_STRING']); ?>" class="btn">고객센터</a>
			</div>
		</div>
		<div class="foot_tm_cs1">
			<div class="top_menu_btn">
				<a href="/?_pcmode=chk&<?php echo str_replace('_mobilemode=chk', '', $_SERVER['QUERY_STRING']); ?>" class="btn">PC화면</a>
			</div>
		</div>
	</div>
	<!-- 푸터메뉴 -->
	<div class="bottom_menu">
		<ul class="clearfix">
			<?php if($normalpage_view['company'] == 1) { // JJC : 2020-12-16 : 일반페이지 노출여부 확인?>
				<li><a href="/?pn=pages.view&type=agree&data=company" class="btn">회사소개</a></li>
			<?php } ?>
			<li><a href="/?pn=pages.view&type=agree&data=agree" class="btn">이용약관</a></li>
			<li class="foot_bm_privacy"><a href="/?pn=pages.view&type=agree&data=privacy" class="btn">개인정보처리방침</a></li>
			<li><a href="/?pn=pages.view&type=agree&data=privacy" class="btn">이용안내</a></li>
			<li class="hide"><a href="/?_pcmode=chk&<?php echo str_replace('_mobilemode=chk', '', $_SERVER['QUERY_STRING']); ?>" class="btn">PC버전</a></li>
		</ul>
	</div>

	<!-- 회사/사이트 정보 -->
	<div class="info_box">
		<ul>
			<?php if($siteInfo['sns_link_instagram'].$siteInfo['sns_link_facebook'].$siteInfo['sns_link_twitter'].$siteInfo['sns_link_blog'].$siteInfo['sns_link_cafe'].$siteInfo['sns_link_youtube'].$siteInfo['sns_link_kkp'].$siteInfo['sns_link_kks'] != '') { ?>
				<li class="hide">
					<div class="sns_box">
						<ul>
							<?php if($siteInfo['sns_link_instagram']) { ?>
								<li><a href="<?php echo $siteInfo['sns_link_instagram']; ?>" class="sns" title="인스타그램" target="_blank"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/b_insta.png" alt="인스타그램" /></a></li>
							<?php } ?>
							<?php if($siteInfo['sns_link_facebook']) { ?>
								<li><a href="<?php echo $siteInfo['sns_link_facebook']; ?>" class="sns" title="페이스북" target="_blank"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/b_face.png" alt="페이스북" /></a></li>
							<?php } ?>
							<?php if($siteInfo['sns_link_twitter']) { ?>
								<li><a href="<?php echo $siteInfo['sns_link_twitter']; ?>" class="sns" title="트위터" target="_blank"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/b_twitt.png" alt="트위터" /></a></li>
							<?php } ?>
							<?php if($siteInfo['sns_link_blog']) { ?>
								<li><a href="<?php echo $siteInfo['sns_link_blog']; ?>" class="sns" title="블로그" target="_blank"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/b_blog.png" alt="블로그" /></a></li>
							<?php } ?>
							<?php if($siteInfo['sns_link_cafe']) { ?>
								<li><a href="<?php echo $siteInfo['sns_link_cafe']; ?>" class="sns" title="카페" target="_blank"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/b_cafe.png" alt="블로그" /></a></li>
							<?php } ?>
							<?php if($siteInfo['sns_link_youtube']) { ?>
								<li><a href="<?php echo $siteInfo['sns_link_youtube']; ?>" class="sns" title="유튜브" target="_blank"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/b_youtube.png" alt="유튜브" /></a></li>
							<?php } ?>
							<?php if($siteInfo['sns_link_kkp']) { ?>
								<li><a href="<?php echo $siteInfo['sns_link_kkp']; ?>" class="sns" title="카카오 채널" target="_blank"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/b_kplus.png" alt="카카오 채널" /></a></li>
							<?php } ?>
							<?php if($siteInfo['sns_link_kks']) { ?>
								<li><a href="<?php echo $siteInfo['sns_link_kks']; ?>" class="sns" title="카카오 스토리" target="_blank"><img src="<?php echo $SkinData['skin_url']; ?>/images/skin/b_kstory.png" alt="카카오 스토리" /></a></li>
							<?php } ?>
						</ul>
					</div>
				</li>
			<?php } ?>
			<li>
				<div class="shop_name hide"><?php echo $siteInfo['s_adshop']; ?></div>
				<span class="txt">상호 : <?php echo $siteInfo['s_company_name']; ?></span>
				<span class="txt">대표 : <?php echo $siteInfo['s_ceo_name']; ?></span>
			</li>
			<li>
				<span class="txt">주소 : <?php echo $siteInfo['s_company_addr']; ?></span>
			</li>
			<?php if($siteInfo['s_view_network_company_info'] == 'Y') { ?>
				<li>
					<span class="txt">사업자번호 : <?php echo $siteInfo['s_company_num']; ?></span>
					<span class="txt">통신판매업신고 : <?php echo $siteInfo['s_company_snum']; ?></span>
				</li>
			<?php } ?>
			<li>
				<span class="txt">대표번호: <a href="tel:<?php echo $siteInfo['s_glbtel']; ?>"><?php echo $siteInfo['s_glbtel']; ?></a></span>
				<span class="txt">이메일 : <?php echo $siteInfo['s_ademail']; ?></span>
			</li>
			<li class="copy">ⓒ <span><?php echo $system['host']; ?></span>. All Rights Reserved.</li>
		</ul>
	</div>
</div>

<script type="text/javascript">
    // 연관되지 않는 요소 열고닫기 (스크립트는 이것만 추가)
    $(document).on('click','.js_onoff_event',function(e){
        var data = $(this).data();
        var targetClass = data.target;
        var addClass = data.add;
        var chk = $(targetClass).hasClass(addClass);
        if( chk == true){ // 이미 있다면
            $(targetClass).removeClass(addClass);
        }else{
            $(targetClass).addClass(addClass);
        }
    });
</script>

<?php include_once(OD_PROGRAM_ROOT.'/inc.footer.php'); // 스킨 내부파일로 직접 include 하지 마세요. ?>
