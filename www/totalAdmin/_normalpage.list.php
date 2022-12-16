<?php
	include_once("wrap.header.php");

	// 2019-11-29 SSJ :: 노출 메뉴 추가
	if($is_colmn_menu == 'N'){
		// 한번더 체크
		$trigger_colmn_menu = false;
		$chk = _MQ_assoc(" desc smart_normal_page ");
		if(count($chk) > 0){
			foreach($chk as $k=>$v){
				if($v['Field'] == 'np_menu'){
					$trigger_colmn_menu = true;
					break;
				}
			}
		}
		// db 추가
		if($trigger_colmn_menu === false){
			_MQ_noreturn(" alter table smart_normal_page add column `np_menu` varchar(30) not null default 'default' comment '노출메뉴' ");
			_MQ_noreturn(" alter table smart_normal_page add index(`np_menu`) ");
		}
	}

	// 검색 체크
	$s_query = " where 1 ";
	if( $pass_view !="" ) { $s_query .= " and np_view='${pass_view}' "; }
	if( $pass_title !="" ) { $s_query .= " and np_title like '%${pass_title}%' "; }

	// 2019-11-29 SSJ :: 노출 메뉴 검색 추가
	if( $pass_menu !="" ) { $s_query .= " and np_menu='${pass_menu}' "; }

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from smart_normal_page $s_query ");
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * from smart_normal_page {$s_query} ORDER BY np_idx asc , np_uid asc limit $count , $listmaxcount ");
?>

	<!-- ● 단락타이틀 -->
	<div class="group_title">
		<strong>일반페이지 검색</strong>
		<div class="btn_box">
			<a href="_normalpage.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">일반페이지 등록</a>
		</div>
	</div>

	<form name="searchfrm" method="get" action="<?=$PHP_SELF?>" autocomplete="off">
	<input type="hidden" name="mode" value="search">
		<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
		<div class="data_form if_search">

			<!-- 폼테이블 2단 -->
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*"><col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>노출여부</th>
						<td>
							<?php echo _InputRadio('pass_view' , array('','Y','N') , $pass_view , '' , array('전체','노출','숨김'));?>
						</td>
						<th>노출메뉴</th>
						<td>
							<?php echo _InputRadio('pass_menu' , array('','default','agree','only') , $pass_menu , '' , array('전체','회사소개','이용안내','단독메뉴'));?>

							<!-- np_menu 칼럼 DB 추가여부 :: N이면 pro파일에서 DB 추가후 저장한다 -->
							<input type="hidden" name="is_colmn_menu" value="<?php echo ($res[0]['np_menu']=="" ? "N" : "Y"); ?>">
						</td>
					</tr>
					<tr>
						<th>페이지명</th>
						<td colspan="3"><input type="text" name="pass_title" class="design" style="width:250px;" value="<?php echo $pass_title; ?>"></td>
					</tr>
				</tbody>
			</table>
			<!-- 폼테이블 2단 -->


			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
					<li><span class="c_btn h34 black"><input type="submit" name="" value="검색" /></span><!-- <a href="" class="c_btn h34 black ">검색</a> --></li>
					<?php if($mode == 'search'){ ?>
						<li><a href="<?php echo $_SERVER["PHP_SELF"]?>" class="c_btn h34 black line normal">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>

		</div>
	</form>




	<!-- ● 데이터 리스트 -->
	<div class="data_list">

		<?php if(sizeof($res) > 0){ ?>
			<table class="table_list">
				<colgroup>
					<col width="70"><col width="70"><col width="120"><col width="120"><col width="*"><col width="90"><col width="160">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">NO</th>
						<th scope="col">노출순위</th>
						<th scope="col">노출여부</th>
						<th scope="col">노출메뉴</th>
						<th scope="col">페이지명</th>
						<th scope="col">등록일</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
				<?php
					foreach($res as $k=>$v) {
						$app_link = "/?pn=pages.view&type=pages&data=" . $v['np_id'];
						$_link_out = "<a href='" . $app_link . "' class='c_btn h22 ' target='_blank'>바로가기</a>";
						$_mod = "<a href='#none' class='c_btn h22 ' onclick='location.href=(\"_normalpage.form.php?_mode=modify&_uid=$v[np_uid]&_PVSC=${_PVSC}\");'>수정</a>";
						$_del = "<a href='#none' class='c_btn h22 gray'  onclick='del(\"_normalpage.pro.php?_mode=delete&_uid=$v[np_uid]&_PVSC=${_PVSC}\");'>삭제</a>";

						$_num = $TotalCount - $count - $k ;

						// 2019-11-29 SSJ :: 노출메뉴 추가
						$_menu = "회사소개";
						if($v['np_menu'] == "agree") $_menu = "이용안내";
						else if($v['np_menu'] == "only") $_menu = "단독메뉴";
				?>
					<tr>
						<td><?php echo number_format($_num); ?></td>
						<td><?php echo number_format($v['np_idx']); ?></td>
						<td><div class="lineup-center"><?php echo $arr_adm_button[($v['np_view'] == 'Y' ? '노출' : '숨김')]; ?></div></td>
						<td><?php echo $_menu; ?></td>
						<td class="t_left"><?php echo stripslashes($v['np_title']); ?></td>
						<td><?php echo date('Y.m.d', strtotime($v['np_rdate'])); ?></td>
						<td>
							<div class="lineup-vertical">
								<?php echo $_mod; ?>
								<?php echo $_del; ?>
								<?php echo $_link_out; ?>
							</div>
							<!-- KAY :: 2021-06-10 :: 에디터이미지관리 버튼생성-->
							<div class="lineup-vertical"><a href='#none' onclick="edit_img_pop('<?php echo $v['np_uid'] ?>')" class='c_btn h22 green'>이미지 관리</a></div>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>

			<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
			<div class="paginate">
				<?php echo pagelisting($listpg, $Page, $listmaxcount," ?${_PVS}&listpg=" , "Y")?>
			</div>

		<?php }else{ ?>
			<!-- 내용없을경우 -->
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<?php } ?>

	</div>

  <script>
    
	// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기
	function edit_img_pop(_uid, table='normal'){
		window.open('_config.editor_img.pop.php?_uid='+_uid+'&tn='+table+'','editimg','width=1120,height=600,scrollbars=yes');
	}
	// KAY :: 에디터 이미지 관리 :: 개별관리 팝업창 띄우기
 
  </script>




<?PHP
	include_once("wrap.footer.php");  //o_price_real
?>