<?php

	$pass_menu = $_REQUEST[pass_menu] ? $_REQUEST[pass_menu] : "inquiry";

	// 페이지 표시
	$app_current_page_name = "1:1문의관리";
	include dirname(__FILE__)."/wrap.header.php";


	// 검색 체크
	$s_query = " from smart_request where r_menu='{$pass_menu}' ";

	// 제목 체크
	if($search_type == "open") {$pass_title_tmp = $pass_title ? $pass_title : $pass_title_tmp;}
	else {$pass_title = $pass_title_tmp ? $pass_title_tmp : $pass_title;}


	// 검색 체크
	if( $mode == "search" ) {
		if( $pass_title !="" ) { $s_query .= " and r_title like '%{$pass_title}%' "; }
		if( $pass_status !="" ) { $s_query .= " and r_status='{$pass_status}' "; }
		if( $pass_id !="" ) { $s_query .= " and r_inid like '%{$pass_id}%' "; }
	}


	$listmaxcount = 5 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;


	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * {$s_query} ORDER BY r_rdate desc limit $count , $listmaxcount ");
?>



<form role="search" name="searchfrm" method="post" action="<?=$_SERVER["PHP_SELF"]?>">
<input type=hidden name=mode value=search>
<input type="hidden" name="search_type" value="close">
<input type=hidden name=pass_menu value=<?=$pass_menu?>>
	<div class="page_top_area if_closed">

		<div class="title_box"><span class="txt">SEARCH</span>
			<div class="before_search">
				<button type="submit" class="btn_search"></button>
				<input type="search" name="pass_title_tmp" value="<?=$pass_title_tmp?>" class="input_design" placeholder="제목 검색">
			</div>
			<a href="#none" onclick="view_search(); return false;" class="btn_ctrl btn_close" title="검색닫기">상세검색닫기<span class="shape"></span></a>
			<a href="#none" onclick="view_search(); return false;" class="btn_ctrl btn_open" title="검색열기">상세검색열기<span class="shape"></span></a>
		</div>

		<!-- ●●●●● 검색폼 -->
		<div class="cm_search_form">
			<ul>
				<li class="ess double">
					<span class="opt">제목</span>
					<div class="value"><input type="text" name="pass_title" value="<?=$pass_title?>" class="input_design" placeholder="제목을 입력하세요." /></div>
				</li>
				<li class="ess double">
					<span class="opt">회원아이디</span>
					<div class="value"><input type="text" name="pass_id" value="<?=$pass_id?>" class="input_design" placeholder="회원아이디를 입력하세요." /></div>
				</li>
				<li class="ess">
					<span class="opt">답변상태</span>
					<div class="value">
						<div class="select">
							<span class="shape"></span>
							<?=_InputSelect( "pass_status" , array('답변대기','답변완료'), $pass_status , "  " ,  "" , '-선택-') ?>
						</div>
					</div>
				</li>
			</ul>

			<!-- ●●●●● 가운데정렬버튼 -->
			<div class="cm_bottom_button">
				<ul>
					<li><span class="button_pack"><input type="submit" class="btn_md_blue" value="검색하기"></span></li>
					<?if($mode == "search") :?><li><span class="button_pack"><a href="_request.list.php?pass_menu=<?=$pass_menu?>" class="btn_md_black">전체목록</a></span></li><?endif;?>
				</ul>
			</div>
			<!-- / 가운데정렬버튼 -->

		</div>

	</div>
	<!-- / 상단에 들어가는 검색등 공간 -->
</form>






<?
	if(sizeof($res) == 0 ) :
		echo "<div class='cm_no_conts'><div class='no_icon'></div><div class='gtxt'>등록된 내용이 없습니다.</div></div>"; 
	endif;
?>




	<!--  ●●●●● 내용들어가는 공간 -->
	<div class="container">

		<!-- ●●●●● 데이터리스트 주문리스트 추가 if_order -->
		<div class="data_list if_order">

<?php

	foreach($res as $k=>$row){

		$_num = $TotalCount - $count - $k ;

		$_mod = "<span class='button_pack'><a href='_request.form.php?pass_menu=" . $pass_menu . "&_mode=modify&_uid=". $row[r_uid] ."&_PVSC=" . $_PVSC . "' class='btn_sm_white'>관리</a></span>";
		$_del = "<span class='button_pack'><a href='#none' onclick=\"del('_request.pro.php?pass_menu=" . $pass_menu . "&_mode=delete&_uid=". $row[r_uid] ."&_PVSC=" . $_PVSC . "');\" class='btn_sm_black'>삭제</a></span>";

		$_status = (
			$row[r_status] == "답변대기" ? 
				"<span class='texticon_pack checkicon'><span class='light'>".$row[r_status]."</span></span>" : 
				"<span class='texticon_pack checkicon'><span class='dark'>".$row[r_status]."</span></span>"
		);

		echo "
			<dl>
				<dd>
					<div class='first_box'>
						<span class='number'>no.". $_num ."</span>
						<span class='date'>문의일 : ".date("y.m.d" , strtotime($row[r_rdate]))."</span>
					</div>
					<div class='request_info'>
						". $_status ."
						<div class='name'>회원ID : <span class='txt'>". $row[r_inid] ."</span></div>
						<div class='title'>".stripslashes(htmlspecialchars($row[r_title]))."</div>						
					</div>
				</dd>
				<dt>					
					<div class='btn_box'>
						<ul>							
							<li>". $_mod ."</li>
							<li>". $_del ."</li>
						</ul>	
					</div>
				</dt>
			</dl>
		";
	}
?>
		</div>
		<!-- / 데이터리스트 -->

	</div>
	<!-- / 내용들어가는 공간 -->

	<?=pagelisting_mobile_totaladmin($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>



<?php

	include dirname(__FILE__)."/wrap.footer.php";

?>