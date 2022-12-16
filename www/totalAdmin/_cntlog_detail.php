<?php

	include_once('wrap.header.php');


	// - 넘길 변수 설정하기 ---
	$_PVS = ""; $ARR_PVS = array(); // 링크 넘김 변수
	foreach(array_filter($_GET) as $key => $val) { $ARR_PVS[$key] = $val; } // GET먼저 중복걸러내기
	foreach(array_filter($_POST) as $key => $val) { $ARR_PVS[$key] = $val; } // POST나중 중복걸러내기
	foreach( $ARR_PVS as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// - 넘길 변수 설정하기 ---



	$listmaxcount = 50 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;


	// 일자계산
	$pass_sdate = $pass_sdate ? $pass_sdate : date('Y-m-d' , strtotime("-1 week"));
	$pass_edate = $pass_edate ? $pass_edate : date('Y-m-d');

	$s_query = "
		FROM smart_cntlog_list as sc
		INNER JOIN smart_cntlog_detail as scd ON (sc.sc_uid = scd.sc_uid)
		where 1
	";
	$s_query .= " and DATE(sc.sc_date) between '". $pass_sdate ."' and '". $pass_edate ."' ";

	if($pass_mobile)	$s_query .= " and sc.sc_mobile = '". $pass_mobile ."' ";
	if($pass_keyword)	$s_query .= " and scd.sc_keyword like '%". $pass_keyword ."%' ";
	if($pass_browser)	$s_query .= " and scd.sc_browser like '%". $pass_browser ."%' ";
	if($pass_ip)	$s_query .= " and scd.sc_ip like '%". $pass_ip ."%' ";
	if($pass_referer)	$s_query .= " and scd.sc_referer like '%". $pass_referer ."%' ";


	// ------- 순위별 목록 -------
	$que = "
		SELECT
			count(*) as cnt
			" . $s_query . "
	";
	$res = _MQ($que);

	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);


	//------------------ sort 기능 ------------------
	IF($order_field) {
		$order_sort = $order_sort ? $order_sort : '1';
		$que_order = $order_field .' '. ($order_sort == '2' ? ' DESC' : ' ASC') . ($order_field <> 'sc.sc_uid' ? ' , sc.sc_uid DESC ' : '');
	}
	else {
		$que_order = ' sc.sc_uid DESC ';
	}
	//------------------ sort 기능 ------------------


	// ------- 순위별 목록 -------
	$que = "
		SELECT
			sc.*, scd.*
			" . $s_query . "
		ORDER BY ". $que_order ."
		LIMIT " . $count . " , " . $listmaxcount . "
	";
	$res = _MQ_assoc($que);

?>


			<!-- 기간검색 -->
			<form name='searchfrm' method='post' action='<?=$PHP_SELF?>'>
			<input type="hidden" name="pass_menu" value="<?php echo $pass_menu; ?>">
			<input type=hidden name='mode' value='search'>
			<input type=hidden name='_mode' value=''><!-- 엑셀다운로드용 -->

				<div class="data_form if_search">
					<table class="table_form">
						<colgroup>
							<col width="130"><col width="350"><col width="180"><col width="*">
						</colgroup>
						<tbody>
							<tr>
								<th>기간선택</th>
								<td colspan="3">
									<span style="float:left;">
										<input type="text" name="pass_sdate" class='design js_pic_day_max_today' value="<?=$pass_sdate?>" readonly style="width:90px;">
										<span class="fr_tx">~</span>
										<input type="text" name="pass_edate" class='design js_pic_day_max_today' value="<?=$pass_edate?>" readonly style="width:90px;">
									</span>
								</td>
							</tr>
							<tr>
								<th>DEVICE</th>
								<td class="conts"><?=_InputSelect( "pass_mobile" , array('N','Y') , $pass_mobile , "" , array('PC','MOBILE') , "-선택-")?></td>
								<th>KEYWORD</th>
								<td class="conts"><input type=text name='pass_keyword' class='design' value="<?=$pass_keyword?>"></td>
							</tr>
							<tr>
								<th>Browser</th>
								<td class="conts"><input type=text name='pass_browser' class='design' value="<?=$pass_browser?>"></td>
								<th>IP</th>
								<td class="conts"><input type=text name='pass_ip' class='design' value="<?=$pass_ip?>"></td>
							</tr>
							<tr>
								<th>유입경로</th>
								<td class="conts" colspan="3"><input type=text name='pass_referer' class='design' value="<?=$pass_referer?>" style="width:300px;"></td>
							</tr>
						</tbody>
					</table>
					<div class="c_btnbox">
						<ul>
							<li><span class="c_btn h34 black"><input type="submit" value="검색" accesskey="s"></span></li>
							<?php if($mode == 'search'){ ?>
								<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?pass_menu=<?=$pass_menu?>" class="c_btn h34 black line normal" accesskey="l">초기화</a></li>
							<?php } ?>
						</ul>
					</div>
				</div>

			</form>
			<!-- / 기간검색 -->



			<!-- 리스트영역 -->
			<div class="group_title"><!-- 공백을 위한 추가 --></div>
			<div class="data_list">
				<div class="list_ctrl">
					<div class="right_box">
						<a href="#none" onclick="searchExcel(); return false;" class="c_btn icon icon_excel">엑셀다운로드</a>
					</div>
				</div>

				<table class="table_list if_counter_table">
					<colgroup>
						<col width="60"/><col width="140"/><col width="110"/><col width="70"/><col width="140"/>
						<col width="*"/>
						<col width="160"/>
					</colgroup>
					<thead>
						<tr>
							<th scope="col" class="colorset" >NO.</th>
							<th scope="col" class="colorset" >유입일</th>
							<th scope="col" class="colorset" >IP</th>
							<th scope="col" class="colorset" >DEVICE</th>
							<th scope="col" class="colorset" >검색어</th>
							<th scope="col" class="colorset" >유입경로</th>
							<th scope="col" class="colorset" >BROWSER</th>
						</tr>
					</thead>
					<tbody>
						<?php

							foreach( $res as $datek => $datev ){

								$_num = $TotalCount - $count - $datek ;

								// 접속기기
								$_device = ($datev['sc_mobile'] == 'Y' ? '<span class="c_tag h18 mo">MO</span>' : '<span class="c_tag h18 t3 pc">PC</span>');

								echo '
									<tr>
										<td>'. $_num .'</td>
										<td>'. $datev['sc_date'] .'</td>
										<td>'. $datev['sc_ip'] .'</td>
										<td><span class="shop_state_pack">'. $_device .'</span></td>
										<td>'. $datev['sc_keyword'] .'</td>
										<td class="t_left">' . ( $datev['sc_referer'] ? '<a href="'.$datev['sc_referer'].'" target="_blank">'.$datev['sc_referer'].'</a>' : '' ) . '</td>
										<td>'. $datev['sc_browser'] .'</td>
									</tr>
								';
							}
							// ------- 순위별 목록 -------
						?>

					</tbody>
				</table>

				<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
				<div class="paginate">
					<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
				</div>

			</div>



<script>
	// --- 검색 엑셀 ---
	function searchExcel() {
		$('form[name="searchfrm"]').children('input[name="_mode"]').val('cntlog_detail_search');
		$('form[name="searchfrm"]').attr('action', '_cntlog.pro.php');
		$('form[name="searchfrm"]')[0].submit();
		$('form[name="searchfrm"]').attr('action', '<?=$PHP_SELF?>');
	}
	// --- 검색 엑셀 ---
</script>



<?php

	include_once('wrap.footer.php');

?>