<?php
	include_once('wrap.header.php');

	$_PVS = ''; // 링크 넘김 변수
	foreach(array_filter(array_unique(array_merge($_GET , $_POST))) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);


	######## 검색 체크
	$s_query = " from smart_product_wish as pw
	inner join smart_product as p on (pw.pw_pcode = p.p_code)
	inner join smart_individual as ind on (ind.in_id = pw.pw_inid)
	where 1 ";
	if( $pass_code !="" ) { $s_query .= " and p.p_code like '%${pass_code}%' "; }
	if( $pass_name !="" ) { $s_query .= " and p.p_name like '%${pass_name}%' "; }
	if( $pass_view !="" ) { $s_query .= " and p.p_view='${pass_view}' "; }
	if( $pass_bestview !="" ) { $s_query .= " and p.p_bestview='${pass_bestview}' "; }
	if( $pass_newview !="" ) { $s_query .= " and p.p_newview='${pass_newview}' "; }
	if( $pass_saleview !="" ) { $s_query .= " and p.p_saleview='${pass_saleview}' "; }
	if( $_cpid !="" ) { $s_query .= " and p.p_cpid='${_cpid}' "; }
	if( $pass_inid !="" ) { $s_query .= " and ind.in_id like '%${pass_inid}%' "; }
	if( $pass_mname !="" ) { $s_query .= " and ind.in_name like '%${pass_mname}%' "; }
	// JJC ::: 브랜드관리 ::: 2017-11-03
	if( $pass_brand !="" ) { $s_query .= " AND p.p_brand = '".$pass_brand."' "; }

	if( $_cuid !="" ) { $s_query .= " and (select count(*) from smart_product_category as pct where pct.pct_pcode=p.p_code and pct.pct_cuid='".$_cuid."') > 0 "; }
	else if( $pass_parent03_real !="" ) { $s_query .= " and (select count(*) from smart_product_category as pct where pct.pct_pcode=p.p_code and pct.pct_cuid='".$pass_parent03_real."') > 0 "; }
	else if( $pass_parent02_real !="" ) {
		$s_query .= "
			and (
				select
					count(*)
				from smart_product_category as pct
				left join smart_category as c on (c.c_uid = pct.pct_cuid)
				where
					pct.pct_pcode=p.p_code and
					(
						SUBSTRING_INDEX(c.c_parent , ',' , -1) = '" . $pass_parent02_real . "' or
						pct.pct_cuid = '" . $pass_parent02_real . "'
					)
			) > 0
		";
	}
	else if( $pass_parent01 !="" ) {
		$s_query .= "
			and (
				select
					count(*)
				from smart_product_category as pct
				left join smart_category as c on (c.c_uid = pct.pct_cuid)
				where
					pct.pct_pcode=p.p_code and
					(
						SUBSTRING_INDEX(c.c_parent , ',' , 1) = '" . $pass_parent01 . "' or
						pct.pct_cuid = '" . $pass_parent01 . "'
					)
			) > 0
		";
	}



	if(!$listmaxcount) $listmaxcount = 20;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'pw_rdate';
	if(!$so) $so = 'desc';
	$count = $listpg * $listmaxcount - $listmaxcount;


	# 상품별 통계를 추출한다 -- 상품코드별로 찜한 횟수 노출
	if($pass_mode == 'group'){
		// 상품코드로 group by
		$s_query .= " group by pw.pw_pcode ";
		// 총 개수 추출 방식 변경
		$res = _MQ(" select count(*) as cnt  from (select pw.pw_pcode $s_query) as c ");
		$TotalCount = $res['cnt'];
		$Page = ceil($TotalCount / $listmaxcount);
		// 정렬순서 변경 , 찜횟수 추출
		$res = _MQ_assoc(" select p.* ,pw.*, ind.in_name,ind.in_id, count(*) as group_cnt  $s_query order by group_cnt desc limit $count , $listmaxcount ");

	}else{
		$res = _MQ(" select count(*) as cnt  $s_query ");
		$TotalCount = $res['cnt'];
		$Page = ceil($TotalCount / $listmaxcount);
		$res = _MQ_assoc(" select p.* ,pw.*, ind.in_name,ind.in_id  $s_query order by {$st} {$so} limit $count , $listmaxcount ");
	}

?>



	<!-- ● 단락타이틀 -->
	<div class="group_title">
		<strong>상품 찜 검색</strong>
	</div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<div class="data_form if_search">

		<form name="searchfrm" method="get" action="<?php echo $PHP_SELF; ?>" autocomplete="off">
		<input type="hidden" name="mode" value="search">
		<input type="hidden" name="st" value="<?php echo $st; ?>">
		<input type="hidden" name="so" value="<?php echo $so; ?>">
		<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">

			<!-- 폼테이블 2단 -->
			<table class="table_form">
				<colgroup>
					<col width="140"><col width="*"><col width="140"><col width="*"><col width="140"><col width="*">
				</colgroup>
				<tbody>
					<tr style="display: none;">
						<th>베스트 상품</th>
						<td>
							<?php echo _InputRadio( "pass_bestview" , array('', 'Y','N'), $pass_bestview , "" , array('전체', '노출','숨김') ); ?>
						</td>
						<th>신규상품</th>
						<td>
							<?php echo _InputRadio( "pass_newview" , array('', 'Y','N'), $pass_newview , "" , array('전체', '노출','숨김') ); ?>
						</td>
						<th>Today's pick</th>
						<td>
							<?php echo _InputRadio( "pass_saleview" , array('', 'Y','N'), $pass_saleview , "" , array('전체', '노출','숨김') ); ?>
						</td>
					</tr>
					<tr>
						<th>상품코드</th>
						<td><input type="text" name="pass_code"  class="design" value="<?php echo $pass_code; ?>"></td>
						<th>상품명</th>
						<td><input type="text" name="pass_name"  class="design" value="<?php echo $pass_name; ?>"></td>
						<th>브랜드</th>
						<td>
							<?php
								// JJC ::: 브랜드 정보 추출  ::: 2017-11-03
								//		basic : 기본정보
								//		all : 브랜드 전체 정보
								$arr_brand = brand_info('basic');
								echo _InputSelect( 'pass_brand' , array_keys($arr_brand) , $pass_brand , "" , array_values($arr_brand) , "-브랜드-");
							?>
						</td>
					</tr>
					<tr>
						<th>회원아이디</th>
						<td><input type="text" name="pass_inid"  class="design" value="<?php echo $pass_inid; ?>"></td>
						<th>회원명</th>
						<td><input type="text" name="pass_mname"  class="design" value="<?php echo $pass_mname; ?>"></td>
						<th>상품별통계</th>
						<td>
							<label class="design"><input type="checkbox" name="pass_mode" value="group" <?php echo ($pass_mode=='group'?' checked ':null); ?>>상품별로 찜횟수를 노출시킵니다.</label>
						</td>
					</tr>
					<tr>
						<th>카테고리</th>
						<td colspan="5">
							<?PHP
								// 상품 카테고리 분류 (list , form 을 공통으로 쓰기 위한 조치)
								// 1차 - pass_parent01 -> app_depth1
								// 2차 - pass_parent02_real -> app_depth2
								// 3차 - pass_parent03_real -> $row[p_cuid]
								if( $pass_parent01 ) {
									$app_depth1 =  $pass_parent01 ;
								}
								if( $pass_parent02_real ) {
									$app_depth2 =  $pass_parent02_real ;
								}
								if( $pass_parent03_real ) {
									$app_depth3 =  $pass_parent03_real ;
								}

								$pass_parent03_no_required = "Y";

								include_once(OD_PROGRAM_ROOT."/category.inc.php");
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<!-- 폼테이블 2단 -->


			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
					<li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"/></span><!-- <a href="" class="c_btn h34 black ">검색</a> --></li>
					<?php if($mode == 'search'){ ?>
						<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount)); ?>" class="c_btn h34 black line normal" accesskey="l">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>

		</form>

	</div>



	<!-- ● 데이터 리스트 -->
	<div class="data_list">

		<form name="frm" method="post" action="_product_wish.pro.php" >
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">

			<!-- ●리스트 컨트롤영역 -->
			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
					<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
					<a href="#none" onclick="selectDelete(); return false;" class="c_btn h27 gray">선택삭제</a>
				</div>
			</div>
			<!-- / 리스트 컨트롤영역 -->

			<table class="table_list">
				<colgroup>
					<col width="40"><col width="70"><col width="100"><col width="140"><col width="*"><col width="100">
					<?php
						# 상품별 통계를 추출한다 -- 상품코드별로 찜한 횟수 노출
						if($pass_mode == 'group'){
					?>
						<col width="90">
					<?php }else{ ?>
						<col width="150"><col width="90">
					<?php } ?>
					<col width="100">
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th scope="col">NO</th>
						<th scope="col">상품 이미지</th>
						<th scope="col">상품코드</th>
						<th scope="col">상품명</th>
						<th scope="col">판매가</th>
						<?php
							# 상품별 통계를 추출한다 -- 상품코드별로 찜한 횟수 노출
							if($pass_mode == 'group'){
						?>
							<th scope="col">찜횟수</th>
						<?php }else{ ?>
							<th scope="col">회원명</th>
							<th scope="col">등록일</th>
						<?php } ?>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
					<?PHP
					if(sizeof($res) > 0){
						foreach($res as $k=>$v) {

							$_del = '<a href="#none" onclick="del(\'_product_wish.pro.php?_mode=delete&pw_uid='. $v['pw_uid'] .'&_PVSC='. $_PVSC .'\');" class="c_btn h22 gray">삭제</a>';

							$_num = $TotalCount - $count - $k ;

							// 이미지 체크
							$_p_img = get_img_src('thumbs_s_'.$v['p_img_list_square']);
							if($_p_img == '') $_p_img = 'images/thumb_no.jpg';

					?>
							<tr>
								<td><label class="design"><input type="checkbox" name="chk_uid[]" class="js_ck" value="<?php echo $v['pw_uid']; ?>"></label></td>
								<td><?php echo $_num; ?></td>
								<td class="img80">
									<img src="<?php echo $_p_img; ?>" alt="<?php echo addslashes(strip_tags($v['p_name'])); ?>">
								</td>
								<td>
									<a href="/?pn=product.view&code=<?php echo $v['p_code']; ?>" target="_blank"><?php echo $v['p_code']; ?></a>
								</td>
								<td class="t_left t_black">
									<?php
										// JJC ::: 브랜드관리 ::: 2017-11-03
										echo ($arr_brand[$v['p_brand']] ? "<span style='color:#008aff;'>Brand : ".$arr_brand[$v['p_brand']] . "</span><br>" : "") ;
									?>
									<?php echo strip_tags($v['p_name']); ?>
								</td>
								<td class="t_black"><?php echo number_format($v['p_price']); ?>원</td>
								<?php
									# 상품별 통계를 추출한다 -- 상품코드별로 찜한 횟수 노출
									if($pass_mode == 'group'){
								?>
									<td><?php echo number_format($v['group_cnt']); ?>회</td>
								<?php }else{ ?>
									<td><?php echo showUserInfo($v['in_id'],$v['in_name'],$v); ?></td>
									<td><?php echo date('Y.m.d' , strtotime($v['pw_rdate'])); ?></td>
								<?php } ?>
								<td>
									<div class="lineup-vertical">
										<?php echo $_del; ?>
									</div>
								</td>
							</tr>
					<?php
						}
					}
					?>
				</tbody>
			</table>


			<?php if(count($res) < 1) {  ?>
				<!-- 내용없을경우 -->
				<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
			<?php } ?>

		</form>

	</div>

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
	</div>

	<script>
		// 선택삭제
		function selectDelete() {
			if($('.js_ck').is(':checked')){
				if(confirm('정말 삭제하시겠습니까?')){
					$('form[name=frm]').children('input[name=_mode]').val('mass_delete');
					$('form[name=frm]').attr('action' , '_product_wish.pro.php');
					document.frm.submit();
				}
			}
			else {
				alert('1개 이상 선택해 주시기 바랍니다.');
			}
		}
	</script>


<?php include_once('wrap.footer.php'); ?>