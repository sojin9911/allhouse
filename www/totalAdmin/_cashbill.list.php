<?PHP

	$app_current_link = '_cashbill.list.php';
	include_once('wrap.header.php');

	include_once(OD_ADDONS_ROOT . '/barobill/include/var.php');

	// 넘길 변수 설정하기
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) { $_PVS .= "&$key=$val"; }
	$_PVSC = enc('e' , $_PVS);
	// 넘길 변수 설정하기



	// 검색 체크
	$s_query = " where 1 and bc_type != 'pg' and bc_isdelete = 'N' ";

	// 텝메뉴검색
	if( $_state == "autofail" ) { $s_query .= " and bc_iscancel = 'N' and BarobillState = '9999'  "; }
	else if( $_state == "temp" ) { $s_query .= " and bc_iscancel = 'N' and BarobillState = '1000'  "; }
	else if( $_state == "issue" ) { $s_query .= " and bc_iscancel = 'N' and BarobillState in ('2000','3000') "; }
	else if( $_state == "cancel" ) { $s_query .= " and bc_iscancel = 'Y' "; }
	else if( $_state == "fail" ) { $s_query .= " and BarobillState = '4000' "; }

	if( $pass_sdate && $pass_edate ) { $s_query .= " AND TradeDate between '". $pass_sdate ."' and '". $pass_edate ."' "; }// - 검색기간
	else if( $pass_sdate ) { $s_query .= " AND TradeDate >= '". $pass_sdate ."' "; }
	else if( $pass_edate ) { $s_query .= " AND TradeDate <= '". $pass_edate ."' "; }
	if( $pass_state ) { $s_query .= " and BarobillState = '". $pass_state ."' "; }
	if( $pass_number ) { $s_query .= " and IdentityNum like '%". $pass_number ."%' "; }
	if( $pass_name ) { $s_query .= " and ItemName like '%". $pass_name ."%' "; }
	if( $pass_usage ) { $s_query .= " and TradeUsage = '". $pass_usage ."' "; }
	if( $pass_type ) { $s_query .= " and TradeType = '". $pass_type ."' "; }
	if( $pass_bctype ) { $s_query .= " and bc_type = '". $pass_bctype ."' "; }
	if( $pass_ordernum ) { $s_query .= " and bc_ordernum like '%". $pass_ordernum ."%' "; }
	if( $pass_oname ) { $s_query .= " and (select o.o_oname from smart_order as o where bc_ordernum = o.o_ordernum) like '%". $pass_oname ."%' "; }



	// 페이지설정
	$listmaxcount = $listmaxcount ? $listmaxcount : 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$que = " select count(*) as cnt from smart_baro_cashbill $s_query ";
	$res = _MQ($que);
	$TotalCount = $res[cnt];
	$Page = ceil($TotalCount / $listmaxcount);


	// 현금영수증 리스트 불러오기
	$que = "
		select
			* ,
			(select o_canceled from smart_order as o where bc_ordernum = o.o_ordernum ) as o_canceled ,
			(select o.o_oname from smart_order as o where bc_ordernum = o.o_ordernum) as o_oname
		from smart_baro_cashbill as bc
		" . $s_query . "
		ORDER BY bc_uid desc limit $count , $listmaxcount
	";

	$res = _MQ_assoc($que);

	// 발행실패 총 수량 추출
	//$app_autofail_cnt = _MQ_result(" select count(*) as cnt from smart_baro_cashbill where 1 and bc_isdelete = 'N' and bc_iscancel = 'N' and BarobillState = '9999' ");
	// 전송실패함 총 수량 추출
	//$app_fail_cnt = _MQ_result(" select count(*) as cnt from smart_baro_cashbill where 1 and bc_isdelete = 'N' and BarobillState = '4000' ");

?>

	<div class="group_title">
		<strong>현금영수증검색</strong>
		<!-- 해당페이지의 등록/업로드 버튼 있을 경우 -->
		<div class="btn_box">
			<a href="_cashbill.form.php<?php echo URI_Rebuild('?', array('_state'=>$_state, '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red">현금영수증 개별발급</a>
			<!-- <a href="#none" onclick="cashbill_sync();return false;" class="c_btn h46 red line">정보갱신</a> -->
		</div>
	</div>





	<!-- 검색영역 -->
	<form name="searchfrm" method="get" action="<?php echo $_SERVER["PHP_SELF"]?>">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="st" value="<?php echo $st; ?>">
	<input type="hidden" name="so" value="<?php echo $so; ?>">
	<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
	<input type=hidden name="_state" value="<?=$_state?>">

		<div class="data_form if_search">
			<table class="table_form" summary="검색항목">
					<colgroup>
						<col width="180"><col width="*"><col width="180"><col width="*"><col width="180"><col width="*">
					</colgroup>
				</colgroup>
				<tbody>
					<tr>
						<th>발행상태</th>
						<td><?=_InputSelect("pass_state", array_keys($arr_cashbill_state), $pass_state, "", array_values($arr_cashbill_state), "")?></td>
						<th>거래용도</th>
						<td><?=_InputSelect("pass_usage", array_keys($arr_tradeUsage), $pass_usage, "", array_values($arr_tradeUsage), "")?></td>
						<th>거래구분</th>
						<td><?=_InputSelect("pass_type", array_keys($arr_tradeType), $pass_type, "", array_values($arr_tradeType), "")?></td>
					</tr>
					<tr>
						<th>발행구분</th>
						<td><?=_InputSelect("pass_bctype", array("barobill","pg"), $pass_bctype, "", array("바로빌 발행", "PG사 발행"), "")?></td>
						<th>품목명</th>
						<td><input type=text name="pass_name" class="design" value="<?php echo $pass_name; ?>"></td>
						<th>주문번호</th>
						<td><input type=text name="pass_ordernum" class="design" value="<?php echo $pass_ordernum; ?>"></td>
					</tr>
					<tr>
					</tr>
					<tr>
						<th>신분확인번호</th>
						<td><input type=text name="pass_number" class="design" value="<?php echo $pass_number; ?>"></td>
						<th>거래일자</th>
						<td>
							<input type=text name="pass_sdate" class="design js_pic_day" value="<?php echo $pass_sdate; ?>" style="width:85px;">
							<span class="fr_tx">-</span>
							<input type=text name="pass_edate" class="design js_pic_day" value="<?php echo $pass_edate; ?>" style="width:85px;">
						</td>
						<th>주문자명</th>
						<td><input type=text name="pass_oname" class="design" value="<?php echo $pass_oname; ?>"></td>
					</tr>
					<tr>
					<td colspan="6">
						<!-- 여러줄 도움말 -->
						<div class="tip_box">
							<?=_DescStr("발행된 현금영수증은 매일 오후3시에 국세청으로 전송됩니다.")?>
							<?=_DescStr("삭제된 현금영수증은 <a href='//www.barobill.co.kr' target='_blank'><em>바로빌사이트</em></a> [현금영수증 > 보관함 > 삭제 보관함] 에서 조회할 수 있습니다.")?>
							<?=_DescStr("현금영수증 발행 시 별도의 비용이 발생하지 않습니다.")?>
							<?=_DescStr("<em>PG사 발급</em>현금영수증은 PG사 가맹점 페이지에서 관리가 가능합니다. ")?>
							<?PHP
								// 2018-08-27 SSJ :: 바로빌-현금영수증 문서키 중복 체크를 통해 아이디 유효성 체크
								if($siteInfo['TAX_BAROBILL_ID'] && $siteInfo['TAX_CERTKEY']) {
									echo '<script>
												(function(){
													// check_key 를 이용하여 계정정보 유효성 체크 - 오류 발생시에만 메세지 추가
													$.get("/totalAdmin/ajax.simple.php?_mode=check_key", function( data ) {
														console.log(data);
														if(data != ""){
															$(".js_return_error").html("<font style=\"color:red;font-size:12px;\">※ " + data + "</font> <a href=\"/totalAdmin/_config.tax.form.php\" target=\"_blank\" class=\"\">[바로빌 설정 바로가기]</a>").show();
														}
													}, "text");
												})();
											</script>';
									echo '<span class="fr_tx js_return_error" style="display:none;"></span>';
								}
							?>
						</div>
					</td>
				</tr>
				</tbody>
			</table>


			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
					<li><span class="c_btn h34 black"><input type="submit" name="" value="검색" accesskey="s"/></span><!-- <a href="" class="c_btn h34 black ">검색</a> --></li>
					<?php
						if($mode == 'search'){
					?>
						<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="c_btn h34 black line normal">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>



		</div>
	</form>
	<!-- // 검색영역 -->



<form name="frm" method="post" action="_cashbill.pro.php" target="common_frame">
<input type="hidden" name="_mode" value="">
<input type="hidden" name="_seachcnt" value="<?php echo $TotalCount; ?>">
<input type="hidden" name="_PVSC" value="<?php echo $_PVSC; ?>">
<input type="hidden" name="_search_que" value="<?php echo enc('e',$s_query); ?>">

				<!-- 리스트영역 -->
				<div class="data_list">

					<!-- ●리스트 컨트롤영역 -->
					<div class="list_ctrl">
						<div class="left_box">
							<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
							<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
							<a href="#none" onclick="mass_issue_cashbill(); return false;" class="c_btn h27 gray">선택발행</a>
							<a href="#none" onclick="mass_print_cashbill(); return false;" class="c_btn h27 gray">선택인쇄</a>
							<a href="#none" onclick="mass_delete_cashbill(); return false;" class="c_btn h27 gray">선택삭제</a>
						</div>

					</div>
					<!-- / 리스트 컨트롤영역 -->


					<table class="table_list" summary="리스트기본">
						<colgroup>
							<col width="40"><col width="70"><col width="100"><col width="100"><col width="100"><col width="120"><col width="*"><col width="100"><col width="160"><col width="100"><col width="140">
						</colgroup>
						<thead>
							<tr>
								<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK" value="Y"></label></th>
								<th scope="col">NO</th>
								<th scope="col">발행상태</th>
								<th scope="col">거래구분</th>
								<th scope="col">거래용도</th>
								<th scope="col">신분확인번호</th>
								<th scope="col">품목명</th>
								<th scope="col">공급가액</th>
								<th scope="col">주문번호</th>
								<th scope="col">거래일자</th>
								<th scope="col">관리</th>
							</tr>
						</thead>
						<tbody>
						<?PHP

							foreach($res as $k=>$v) {

								if($v['BarobillState']=='1000'){
									$_mode = '<a href="_cashbill.form.php'. URI_Rebuild('?', array('_mode'=>'modify', '_uid'=>$v['bc_uid'],  '_state'=>$_state, '_PVSC'=>$_PVSC)) .'" class="c_btn h22">수정</a>';
								}else{
									$_mode = '';
								}

								if($v['BarobillState']=='1000'){
									$_issue = '<a href="_cashbill.pro.php'. URI_Rebuild('?', array('_mode'=>'issue_one', '_key'=>$v['MgtKey'], '_PVSC'=>$_PVSC)) .'" class="c_btn h22">발행</a>';
								}else{
									$_issue = '';
								}

								if(in_array($v['BarobillState'], array('9999','1000','4000','6000','7000'))){// 임시저장상태만 삭제기능
									$_del = '<a href="_cashbill.pro.php'. URI_Rebuild('?', array('_mode'=>'delete', '_key'=>$v['MgtKey'], '_PVSC'=>$_PVSC)) .'" class="c_btn h22 gray">삭제</a>';
								}else if($v['bc_iscancel']=='N' && $v['bc_isdelete']=='N'){
									if(in_array($v['BarobillState'], array('3000'))){
										$_del = '<a href="_cashbill.pro.php'. URI_Rebuild('?', array('_mode'=>'cancel', '_key'=>$v['MgtKey'], '_PVSC'=>$_PVSC)) .'" class="c_btn h22 gray">취소</a>';
									}else{
										$_del = '<a href="_cashbill.pro.php'. URI_Rebuild('?', array('_mode'=>'cancelbeforesend', '_key'=>$v['MgtKey'], '_PVSC'=>$_PVSC)) .'" class="c_btn h22 gray">취소</a>';
									}
								}
								if(!in_array($v['BarobillState'], array('0000','1000'))){
									$_info = '<a href="#none" onclick="info_cashbill(\'' . $v['MgtKey'] . '\');" class="c_btn h22">조회</a>';
									$_print = '<a href="#none" onclick="print_cashbill(\'' . $v['MgtKey'] . '\');" class="c_btn h22">인쇄</a>';
								}else{
									$_info = '';
									$_print = '';
								}


								// PG사 발행건은 정보확인버튼외에 감추기
								if($v['bc_type']=='pg'){
									$_mode = $_issue = $_del = $_info = $_print = '';
									$_pgbtn = 'PG사 발급';
								}else{
									$_pgbtn = '';
								}

								$_num = $TotalCount - $count - $k ;

						?>
								<tr>
									<td>
										<input type="checkbox" name="_mgtnum[]" value="<?php echo $v['MgtKey'] ?>" class="<?php echo ($v['bc_type']<>'pg' ? ' js_ck' : ''); ?>" <?php echo ($v['bc_type']=='pg' ? ' disabled' : ''); ?>>
									</td>
									<td><?php echo $_num; ?></td>
									<td><div class="lineup-vertical"><?php echo $arr_barobill_button[$arr_cashbill_state[$v['BarobillState']]]; ?></div></td>
									<td><?php echo $arr_tradeType[$v['TradeType']]; ?></td>
									<td><?php echo $arr_tradeUsage[$v['TradeUsage']]; ?></td>
									<td><?php echo ($v['bc_type']=='pg' ? 'PG사발급' : $v['IdentityNum']); ?></td>
									<td class="t_left"><?php echo stripslashes($v['ItemName']); ?></td>
									<td><?php echo number_format($v['Amount']); ?>원</td>
									<td><?php echo  ($v['bc_ordernum'] ? $v['bc_ordernum'] . '<br>(' . $v['o_oname'] . ')' : '개별발급'); ?></td>
									<td><?php echo date('Y.m.d',strtotime($v['TradeDate'])); ?></td>
									<td>
										<div class="lineup-vertical">
											<?php echo $_mode; ?>
											<?php echo $_issue; ?>
											<?php echo $_info; ?>
											<?php echo $_print; ?>
											<?php echo $_del; ?>
											<?php echo $_pgbtn; ?>
										</div>
									</td>
								</tr>
						<?php
							}
						?>
						</tbody>
					</table>

					<?php if(sizeof($res) < 1){ ?>
						<!-- 내용없을경우 -->
						<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>
					<?php } ?>

			</div>
</form>


<!-- ● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
<div class="paginate">
	<?php echo pagelisting($listpg, $Page, $listmaxcount, URI_Rebuild('?'.$_PVS.'&listpg='), 'Y')?>
</div>


<!-- 문서이력 조회 창 -->
<div class="popup cashbill_info close" style="display:none;width:500px;background:#fff;"></div>


<script>

	// 출력하기
	function print_cashbill(_key){
		var url = "_cashbill.print.php?app_tax_mgtnum="+_key;
		window.open(url,"cashbill_print","width=800,height=650,scrollbars=no");
	}

	// 선택출력하기
	function mass_print_cashbill(){
		document.frm.action= "_cashbill.print.php";
		document.frm.target= "cashbill_print";
		document.frm._mode.value= "mass_print";
		window.open("","cashbill_print","width=800,height=650,scrollbars=no");

		document.frm.submit();

		document.frm.action= "_cashbill.pro.php";
		document.frm.target= "";
		document.frm._mode.value= "";
	}

	// 선택발행
	function mass_issue_cashbill(){
		document.frm.action= "_cashbill.pro.php";
		document.frm.target= "";
		document.frm._mode.value= "mass_issue";
		document.frm.submit();
	}

	// 선택삭제
	function mass_delete_cashbill(){
		document.frm.action= "_cashbill.pro.php";
		document.frm.target= "";
		document.frm._mode.value= "mass_delete";
		document.frm.submit();
	}

	//  문서조회
	function info_cashbill(_key){
		var url = "_cashbill.info.php?app_tax_mgtnum="+_key;
		window.open(url,"cashbill_info","width=800,height=900,scrollbars=no");

	}


	// -- DB의 현금영수증 정보와 바로빌의 실제데이터와 다를 수 있음 --
	// 1. 현금영수증발행후 바로빌측에서 국세청으로 정보를 전송(하루한번 오후3시경)
	// 2. 바로빌에서 직접 수정한경우 사이트와 정보가 달라짐
	function cashbill_sync(){
		var url = "_cashbill.sync.php";
		window.open(url,"cashbill_sync","width=400,height=450,scrollbars=no");
	}

</script>




<?PHP
	include_once('wrap.footer.php');
?>