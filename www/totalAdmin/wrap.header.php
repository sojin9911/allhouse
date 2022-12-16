<?php include_once('inc.header.php'); ?>
<!-- ●●●헤더(공통)-->
<div class="header">
	<!-- 사이트명 / 관리자메인 -->
	<div class="site_name">
		<a href="<?php echo OD_ADMIN_URL; ?>" class="btn">
			<!-- 하이센스 <strong>3.0</strong> -->
			<?php echo $siteInfo['s_adshop']; ?>
		</a>
	</div>
	<div class="right_btn">
		<ul>
			<!-- 관리자접속정보 -->
			<!-- li반복 -->
			<li class="li">
				<?php if( $siteAdmin['a_type'] == 'master') {  // -- 전체관리자일경우 -- ?>
					<!-- 운영자관리 페이지로 이동 -->
					<a href="_config.admin.list.php" class="btn"><strong>전체관리자</strong> <span class="id"><?=$siteAdmin['a_id']?></span></a>
				<?php }else{ // -- 일반관리자일 경우 -- ?>
					<a href="#none" onclick="return false;" class="btn" style="cursor:default"><strong>일반관리자</strong> <span class="id"><?=$siteAdmin['a_id']?></span></a>
				<?php } ?>
			</li>

			<?php
			// SSJ : 2017-11-12 자주쓰는 메뉴
			$favMenu = _MQ_assoc(" select fm_uid, fm_menuName from smart_favmenu where fm_appId = 'totaladmin' and fm_admin = '". $siteAdmin['a_uid'] ."' and fm_depth = '1' and fm_view = 'Y' order by fm_menuIdx asc ");
			if(sizeof($favMenu) > 0){
				foreach($favMenu as $_favk=>$_favv){
					// 세부 메뉴 추출
					$_favq = "
						select fm.fm_menuName, am.am_link , am.am_uid
						from smart_favmenu as fm
						left join smart_admin_menu as am on (fm.fm_menu = am.am_uid)
						left join smart_admin_menu_set as ams on (am.am_uid = ams_amuid ". ($siteAdmin['a_uid']<>1 ? " and ams.ams_auid = '". $siteAdmin['a_uid'] ."' "  : null) .")
						where 1
							and fm.fm_appId = 'totaladmin'
							and fm.fm_admin = '". $siteAdmin['a_uid'] ."'
							and fm.fm_depth = '2'
							and fm.fm_parent = '".$_favv['fm_uid']."'
							and am.am_uid > '0'
							and am.am_view = 'Y'
							and am.am_link != ''
							". ($siteAdmin['a_uid']<>1 ? " and ams.ams_uid > '0' "  : null) ."
						group by fm.fm_uid
						order by fm.fm_menuIdx asc
					";
					$_favr = _MQ_assoc($_favq);
			?>
					<li class="li">
						<a href="#none" class="btn menu"><?php echo stripslashes($_favv['fm_menuName']); ?><span class="shape"></span></a>
						<div class="btn_box">
							<div class="shape"></div>
							<div class="inner">
								<?php
									if(sizeof($_favr)>0){
										echo '<ul>';
										foreach($_favr as $_favk2=>$_favv2){
											echo '<li><a href="'. OD_ADMIN_DIR . '/' . $_favv2['am_link'] .(strpos($_favv2['am_link'],'?')!==false?'&':'?').'menuUid='. $_favv2['am_uid'] .'" class="link_btn">'. $_favv2['fm_menuName'] .'</a></li>';
										}
										echo '</ul>';
									}else{
										// <!-- 자주쓰는 메뉴 없을경우 ul없어지고 나오면 됩니다. -->
										echo '<div class="none_menu">자주쓰는 메뉴를<br/>설정해주세요.</div>';
									}
								?>

							</div>
							<!-- 설정버튼 -->
							<div class="set_box">
								<span class="txt">메뉴관리</span>
								<a href="<?php echo OD_ADMIN_DIR; ?>/_config.favmenu.list.php" class="set_btn">설정</a>
							</div>
						</div>
					</li>
			<?php
				}
			}
			// SSJ : 2017-11-12 자주쓰는 메뉴 - end
			?>
			<li class="li"><a href="/" class="btn" target="_blank">내홈페이지</a></li>
			<li class="li"><a href="logout.php" class="btn">로그아웃</a></li>
		</ul>
	</div>
</div>
<!-- /●●●헤더(공통)-->



<!-- ●●●네비(공통)-->
<div class="nav">
	<div class="layout_fix">
		<div class="nav_box">
			<ul>
				<!-- 활성화시 hit클래스 추가 -->
				<?php
				$res_depth1 = _MQ_assoc("select *
											from smart_admin_menu as depth1 where am_view = 'Y' and am_depth = '1' order by am_idx asc");

				foreach($res_depth1 as  $depth1_key=>$depth1_value) {

					// -- 권한체크
				//	if($siteAdminMenuSet[$depth1_value['am_uid']] !== true ){ continue; }


					// -- 1뎁스에서 볼수 있는 페이지를 가져온다.
					$resChkDepth2 = _MQ_assoc("select am_uid from smart_admin_menu where find_in_set(am_uid,'".implode(",",array_keys($siteAdminMenuSet))."') > 0 and am_view = 'Y' and am_depth = '2' and am_parent = '".$depth1_value['am_uid']."' order by am_idx asc");
				//	if( count($resChkDepth2) < 1){ continue; }
					foreach($resChkDepth2 as $k2=>$v2){
						$rowChkDepth3 = _MQ("select am_uid, am_link from smart_admin_menu where find_in_set(am_uid,'".implode(",",array_keys($siteAdminMenuSet))."') > 0 and find_in_set('".$v2['am_uid']."',am_parent) > 0 and am_view = 'Y' and am_depth = '3'   order by am_idx asc ");
						//if( count($rowChkDepth3) < 1){ continue; }
						//else{ $depth1_value['am3_link'] = $rowChkDepth3['am_link']; $depth1_value['am3_uid'] = $rowChkDepth3['am_uid']; break; }
						$depth1_value['am3_link'] = $rowChkDepth3['am_link']; $depth1_value['am3_uid'] = $rowChkDepth3['am_uid']; break;
					}

					$depth_hit = false;
					if( $depth1_value['am_uid'] == $current_page_info['am1_uid']  ){	$depth_hit = true;}

					if( $depth1_value['am3_link'] != '') $chkLink = explode("?",$depth1_value['am3_link']);
					// -- 파일체크
					if( $depth1_value['am3_link'] == '' || is_file(OD_ADMIN_ROOT.'/'.$chkLink[0]) == false){ $depth1_value['am3_link'] = '_blank.php';  }
				?>
					<li class="<?=$depth_hit === true ? 'hit':''?>"><a href="<?php echo OD_ADMIN_URL.'/'.$depth1_value['am3_link'].(strpos($depth1_value['am3_link'],'?')!==false?'&':'?').'menuUid='.$depth1_value['am3_uid']; ?>" class="btn"><?php echo $depth1_value['am_name']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>
<!-- ●●●네비(공통)-->


<?php if( $CURR_FILENAME != '_main.php' && count($current_page_info) > 0  ) {  ?>
<!-- ●●●컨텐츠영역 / close_btn 클릭시 if_hide 클래스 추가 -->
<div class="container js-menu-container">
	<!-- 왼쪽메뉴 -->
	<div class="aside">
		<!-- 1차메뉴명 -->
		<div class="title_box"><?=$current_page_info['am1_name']?></div>

		<!-- 카테고리메뉴 -->
		<div class="ctg_box">
			<ul class="ul">
			<?php
				$res_depth2 = _MQ_assoc("select *from smart_admin_menu where am_view = 'Y'  and am_depth = '2' and am_parent = '".$current_page_info['am1_uid']."' order by am_idx asc  ");

				foreach($res_depth2 as $depth2_key=>$depth2_value) {

					if($siteAdminMenuSet[$depth2_value['am_uid']] !== true ){ continue; }

					$res_depth3 = _MQ_assoc("select *from smart_admin_menu where am_view = 'Y'  and am_depth = '3' and find_in_set('".$depth2_value['am_uid']."',am_parent) > 0 order by am_idx asc  ");

					$curr_depth2_chk = true;
					$curr_depth2_chk = count($res_depth3) > 0 && $depth2_value['am_uid'] ==  $current_page_info['am2_uid'] ? true : null; // 다 열지 않을 시 주석해제
			?>
				<!-- li반복 -->
				<!-- 클릭시 if_open 클래스 추가 -->
				<li class="li js-sub-menu-container <?echo $curr_depth2_chk === true ? 'if_open':null ?> <?php echo count($res_depth3) < 1 ? 'no-depth3': null ?>" data-amuid="<?php echo $depth2_value['am_uid'] ?>">
					<!-- 2차카테고리 -->
					<div class="depth2_box"><a href="#none" onclick="return false;" class="tt js-sub-menu-ctl" data-amuid="<?php echo $depth2_value['am_uid']?>"><?php echo $depth2_value['am_name']?></a></div>
					<?php if( count($res_depth3)  > 0 ) { ?>
					<!-- 3차카테고리 -->
					<div class="depth3_box">
						<ul>
						<?php
							foreach($res_depth3 as $depth3_key => $depth3_value) {
								unset($chkLink);
								if($siteAdminMenuSet[$depth3_value['am_uid']] !== true ){ continue; }
								$curr_depth3_chk = $depth3_value['am_uid'] ==  $current_page_info['am3_uid'] ? true : null;
								$chk_nonepage = false;

								if( $depth3_value['am_link'] != '') $chkLink = explode("?",$depth3_value['am_link']);

								if( $depth3_value['am_link'] == '' || is_file(OD_ADMIN_ROOT.'/'.$chkLink[0]) == false){ $depth3_value['am_link'] = '_blank.php'; $chk_nonepage = true; }
						?>
							<!-- 활성화시 hit클래스 추가 -->
							<li class="<?php echo $curr_depth3_chk === true ? 'hit' : '' ?>"><a href="<?php echo OD_ADMIN_URL.'/'.$depth3_value['am_link'].(strpos($depth3_value['am_link'],'?')!==false?'&':'?').'menuUid='.$depth3_value['am_uid']; ?>" class="btn"><?php echo $depth3_value['am_name'] ?><?php echo $chk_nonepage === true  ? '<font color="red">(X)</font>':null?></a></li>
						<?php } ?>
						</ul>
					</div>
					<?php } ?>
				</li>
			<?php } ?>
			</ul>
		</div>
	</div>
	<!-- 왼쪽메뉴 -->

	<!-- ●오른쪽컨텐츠 -->
	<div class="section">


		<div class="page_top">
			<!-- 클릭후 title 메뉴열기로 텍스트 변경 -->
			<a href="#none" onclick="return false;" class="close_btn js-menu-ctl" title="메뉴닫기"></a>
			<div class="tit">
				<strong><?php echo $app_current_name != '' ? $app_current_name : $current_page_info['am3_name']; ?></strong>
				<?php if($ManualLink[$MLink]) { ?>
					<a href="<?php echo $ManualLink[$MLink]; ?>" class="m_btn" title="매뉴얼 보기" target="_blank"></a>
				<?php } ?>
			</div>
			<span class="location"><?php echo implode(" > ", array($current_page_info['am1_name'], $current_page_info['am2_name'], $current_page_info['am3_name']) ) ?></span>
		</div>

		<!-- 하단 컨텐츠 -->
<?php } ?>