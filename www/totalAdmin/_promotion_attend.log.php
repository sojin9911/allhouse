<?php
	$app_current_link = '_promotion_attend.list.php';
	include_once('wrap.header.php');







	// 필수 변수 체크
	if(!$_uid) error_msg('잘못된 접근 입니다.');
	$arr_param = array('_uid'=>$_uid);

	// 넘길 변수 설정하기
	$_PVS2 = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {
		if(is_array($val)) {
			foreach($val as $sk=>$sv) {
				$_PVS2 .= "&" . $key ."[" . $sk . "]=$sv";
			}
		}
		else {
			if($key <> '_PVSC'){
				$_PVS2 .= "&$key=$val";
			}
		}
	}
	$_PVSC2 = enc('e' , $_PVS2);
	// 넘길 변수 설정하기

	######## 검색 체크
	$s_query = " from smart_promotion_attend_log as atl left join smart_individual as indr on (atl.atl_member = indr.in_id) where atl_event = '". $_uid ."' ";

	// 검색 쿼리 준비
	if( $pass_mid !="" ) { $s_query .= " and atl_member like '%${pass_mid}%' "; }
	if( $pass_name !="" ) { $s_query .= " and in_name like '%${pass_name}%' "; }
	if( $pass_sdate !="" ) { $s_query .= " and atl_date >= '${pass_sdate}' "; }
	if( $pass_edate !="" ) { $s_query .= " and atl_date <= '${pass_edate}' "; }
	if( $pass_status !="" ) { $s_query .= " and atl_success='${pass_status}' "; }
	if( $pass_setday !="" ) { $s_query .= " and atl_addinfo_days='${pass_setday}' "; }

	if(!$pass_limit) {$pass_limit = 20;}
	$listmaxcount = $pass_limit ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스


	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select atl.* , indr.in_name $s_query order by atl.atl_uid desc limit $count , $listmaxcount ");


	// 출석체크 이벤트 정보 추출
	$r = _MQ(" select * from smart_promotion_attend_config where atc_uid = '". $_uid ."' ");

	// 출석체크 이벤트 출석현황 추출
	$arr_attend_log = array();
	$arr_attend_member = array();
	// 총 참여수 ,  참여회원수 추출
	$_temp = _MQ_assoc(" select * from smart_promotion_attend_log where atl_event = '". $_uid ."' ");
	if(sizeof($_temp) > 0){
		foreach($_temp as $k=>$v){
			//ViewArr($v);
			// 총참여수
			$arr_attend_log['total']++;
			// 총 참여회원
			$arr_attend_member[$v['atl_member']] = 1;
			// 총 달성수 / 총 쿠폰지급수 / 총 적립금 지급액 추출
			if($v['atl_success'] == 'Y'){
				// 총 달성수
				$arr_attend_log['success'][$v['atl_addinfo_days']]++;
				// 총 쿠폰지급수
				if($v['atl_coupon'] > 0){
					$arr_attend_log['coupon'][$v['atl_coupon']]['cnt']++;
					$arr_attend_log['coupon'][$v['atl_coupon']]['name'] = $v['atl_coupon_name'];
				}
				// 총 적립금 지급액
				if($v['atl_point'] > 0){
					$arr_attend_log['point'] += $v['atl_point'];
				}
			}
		}
		// 총 참여회원 수
		$arr_attend_log['member'] = sizeof($arr_attend_member);
	}

	// 출석체크 이벤트 달성조건 정보 추출
	$addinfo = _MQ_assoc(" select * from smart_promotion_attend_addinfo where ata_event = '". $_uid ."' order by ata_days asc ");
	$arr_set_day = array();
	if(count($addinfo) > 0){
		foreach($addinfo as $k=>$v){
			$arr_set_day[$v['ata_days']] = ($r['atc_type'] == 'T' ? '누적참여 ' : '연속참여 ') . $v['ata_days'] . '일';
		}
	}


?>

	<!-- ● 단락타이틀 -->
	<div class="group_title">
		<strong><?php echo htmlspecialchars(stripslashes($r['atc_title']))?></strong>
		<em style="margin-top: 3px;">
			<?php
				if($r['atc_limit'] == 'N'){
					echo '&nbsp;(기간제한 없음)';
				}else{
					echo '&nbsp;('. implode(' ~ ' , array($r['atc_sdate'], $r['atc_edate'])) .')';
				}
			?>
		</em>
		<!-- 해당페이지의 등록/업로드 버튼 있을 경우 -->
		<div class="btn_box"><a href="_promotion_attend.list.php<?php echo ($_PVSC ? '?' . enc('d' , $_PVSC) : null); ?>" class="c_btn h46">목록으로 돌아가기</a></div>

		<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
		<div class="open_excel">

			<!-- ● 데이터 리스트 -->
			<table class="table_list">
				<colgroup>
					<col width="17%"><col width="16.6%"><col width="*"><col width="*"><col width="17%">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">총 참여수</th>
						<th scope="col">참여 회원수</th>
						<th scope="col">보상 지급 횟수</th>
						<th scope="col">총 쿠폰 지급 횟수</th>
						<th scope="col">총 적립금 지급액</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php echo number_format($arr_attend_log['total']); ?>회</td>
						<td><?php echo number_format($arr_attend_log['member']); ?>명</td>
						<td class="t_left">
							<?php
								if(sizeof($arr_attend_log['success']) > 0){
									foreach($arr_attend_log['success'] as $k=>$v){
										echo ($r['atc_type'] == 'T' ? '+ 누적참여' : '+ 연속참여 ') . $k . '일 : ' . $v . '회<br>';
									}
								}else{
									echo '<div class="lineup-vertical">0회</div>';
								}
							?>
						</td>
						<td class="t_left">
							<?php
								if(sizeof($arr_attend_log['coupon']) > 0){
									foreach($arr_attend_log['coupon'] as $k=>$v){
										$_ex = explode('-', $v['name']);
										echo '+ ' . trim($_ex[0]) . ' : ' . $v['cnt'] . '회<br>';
									}
								}else{
									echo '<div class="lineup-vertical">0회</div>';
								}
							?>
						</td>
						<td><?php echo number_format($arr_attend_log['point']); ?>원</td>
					</tr>
				</tbody>
			</table>

		</div>
	</div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<div class="data_form if_search">

		<form name="searchfrm" method="get" action="<?php echo $_SERVER["PHP_SELF"]?>">
		<input type="hidden" name="mode" value="search">
		<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">

			<!-- 폼테이블 2단 -->
			<table class="table_form">
				<colgroup>
					<col width="180"><col width="*"><col width="180"><col width="*">
				</colgroup>
				<tbody>
					<tr>
						<th>회원아이디</th>
						<td><input type="text" name="pass_mid" class="design" style="" value="<?php echo $pass_mid; ?>"></td>
						<th>회원명</th>
						<td><input type="text" name="pass_name" class="design" style="" value="<?php echo $pass_name; ?>"></td>
					</tr>
					<tr>
						<th>참여일시</th>
						<td>
							<input type="text" name="pass_sdate" value="<?php echo $pass_sdate; ?>" class="design js_pic_day js_passdate" style="width:85px">
							<span class="fr_tx">-</span>
							<input type="text" name="pass_edate" value="<?php echo $pass_edate; ?>" class="design js_pic_day js_passdate" style="width:85px">
						</td>
						<th>보상지급</th>
						<td>
							<?php echo _InputRadio( "pass_status" , array('', 'N', 'Y'), $pass_status , "" , array('전체', '보상미지급', '보상지급') ); ?>
							<span class="bar" style="margin-right:20px"></span>
							<?php echo _InputSelect( "pass_setday" , array_keys($arr_set_day), $pass_setday , "" , array_values($arr_set_day) , "지급조건 선택"); ?>
						</td>
					</tr>
				</tbody>
			</table>
			<!-- 폼테이블 2단 -->


			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
					<li><span class="c_btn h34 black"><input type="submit" name="" value="검색" /></span></li>
					<?php
						if($mode == 'search'){
							$arr_param = array_filter(array_merge(array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount),$arr_param));
					?>
						<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>

		</form>

	</div>

	<!-- ● 데이터 리스트 -->
	<div class="data_list">

		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
				<a href="#none" onclick="selectDelete(); return false;" class="c_btn h27 gray">선택삭제</a>
			</div>
			<div class="right_box">
				<a href="#none" onclick="downloadExcel('select'); return false;" class="c_btn icon icon_excel">선택 엑셀다운로드</a>
				<a href="#none" onclick="downloadExcel('search'); return false;" class="c_btn icon icon_excel">검색 엑셀다운로드(<?php echo number_format($TotalCount); ?>)</a>
			</div>
		</div>
		<!-- / 리스트 컨트롤영역 -->

		<form name="frm" method="post" action="_promotion_attend.pro.php" >
		<input type="hidden" name="_mode" value="mass_delete_log">
		<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
		<input type="hidden" name="_PVSC2" value="<?php echo $_PVSC2; ?>">
		<input type="hidden" name="_search" value="<?php echo enc('e', $s_query); ?>">

			<table class="table_list">
				<colgroup>
					<col width="40"><col width="80"><col width="120"><col width="120"><col width="*"><col width="*"><col width="*"><col width="150"><col width="80">
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
						<th scope="col">NO</th>
						<th scope="col">회원아이디</th>
						<th scope="col">회원명</th>
						<th scope="col">출석체크 진행 현황</th>
						<th scope="col">지급쿠폰</th>
						<th scope="col">지급적립금</th>
						<th scope="col">참여일시</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if(sizeof($res) > 0){
						foreach($res as $k=>$v){

							$_del = "<a href='#none' onclick='delete_log(\"_promotion_attend.pro.php?_mode=delete_log&_uid=" . $v['atl_uid'] . "&_PVSC=" . $_PVSC . "&_PVSC2=" . $_PVSC2 . "\");' class='c_btn h22 gray'>삭제</a>";

							$_num = $TotalCount - $count - $k ;

							// 출석체크 진행 현황 추출
							$_status = ($r['atc_type'] == 'T' ? '누적참여' : '연속참여 ') . $v['atl_addinfo_days'] . '일';
							// 출석체크 진행 현황 만족
							if($v['atl_success'] == 'Y') $_status .= ' <span class="t_sky">(보상지급)</span>';
							else $_status .= ' <span class="t_light">('.$v['atl_addinfo_days_count'].'일/'.$v['atl_addinfo_days'].'일)</span>'; // <!-- 하이센스 3.0 출석체크 패치 -->

							// 지급한 쿠폰이 있을때 발급대기인 쿠폰이 있는지 체크
							$_ready_coup = '';
							if($v['atl_coupon']>0){
								$_ready = _MQ(" select acr_idate from smart_promotion_attend_coupon_ready where acr_atluid = '". $v['atl_uid'] ."' and acr_status = 'N' ");
								if($_ready['acr_idate']) $_ready_coup = ' (발급예정일: ' . $_ready['acr_idate'] . ')';
							}
					?>
							<tr>
								<td>
									<label class="design"><input type="checkbox" name="chk_uid[<?php echo $v['atl_uid']; ?>]" class="js_ck" value="Y"></label>
								</td>
								<td><?php echo $_num; ?></td>
								<td>
									<?php echo $v['atl_member']; ?>
								</td>
								<td>
									<?php echo stripslashes($v['in_name']); ?>
								</td>
								<td>
									<?php echo $_status; ?>
								</td>
								<td class="t_left">
									<?php echo ($v['atl_coupon_name'] ? stripslashes($v['atl_coupon_name']) : ''); ?>
									<?php echo $_ready_coup; ?>
								</td>
								<td class="t_right">
									<?php echo number_format($v['atl_point']); ?>
								</td>
								<td>
									<?php echo date('Y.m.d H:i:s' , strtotime($v['atl_rdate'])); ?>
								</td>
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

		</form>

		<?php if(sizeof($res) < 1){ ?>
			<!-- 내용없을경우 -->
			<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
		<?php } ?>

	</div>
	<!-- / 데이터 리스트 -->

	<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
	<div class="paginate">
		<?php echo pagelisting($listpg, $Page, $listmaxcount," ?_uid={$_uid}&${_PVS}&listpg=" , "Y")?><!-- 하이센스 3.0 출석체크 패치 -->
	</div>


	<script type="text/javascript">
	$(function() {

		// 이벤트 기간설정에 따른 시작일/종료일 설정 여부 체크
		$('.js_use_limit').on('click', function(){
			<?php echo ($isEditable === false ? ' return false; // 출석체크 진행 이후에는 수정불가 ' : null); ?>
			var _limit = $(this).val();
			// 기간제한없을때 기간수정불가
			if(_limit == 'N'){
				$('.js_limit_date').attr('disabled',true);
			}else{
				$('.js_limit_date').removeAttr('disabled');
			}
		});

	});

	 // 선택삭제
	 function selectDelete() {
		 if($('.js_ck').is(":checked")){
			 if(confirm("내역을 삭제하여도 지급된 적립금, 쿠폰은 취소되지 않습니다.\n\n정말 삭제하시겠습니까?")){
				$("form[name=frm]").children("input[name=_mode]").val("mass_delete_log");
				$("form[name=frm]").attr("action" , "_promotion_attend.pro.php");
				document.frm.submit();
			 }
		 }
		 else {
			 alert('1개 이상 선택해 주시기 바랍니다.');
		 }
	 }
	// 삭제
	function delete_log($href) {
		if(confirm("내역을 삭제하여도 지급된 적립금, 쿠폰은 취소되지 않습니다.\n\n정말 삭제하시겠습니까?")) {
			document.location.href = $href;
		}
	}

	// 선택엑셀 다운로드
	function downloadExcel(_mode){
		if(_mode == 'select' && $('.js_ck').is(":checked") === false){
			alert('1개 이상 선택해 주시기 바랍니다.');
			return false;
		}

		$("form[name=frm]").children("input[name=_mode]").val(_mode);
		$("form[name=frm]").attr("action" , "_promotion_attend.log.download.php");
		$("form[name=frm]").attr("target" , "_self");
		document.frm.submit();
		return true;
	}
	// 검색엑셀 다운로드

	</script>

<?php include_once('wrap.footer.php'); ?>