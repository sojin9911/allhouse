<?php

	// -- LCY -- 회원리스트
	include_once('wrap.header.php');
	// papersj
	// member_sleep_backup('wyoule');
?>

		<!-- 단락타이틀 -->
		<div class="group_title">
			<strong>휴면회원검색</strong><!-- 메뉴얼로 링크 --><?=openMenualLink('휴면회원검색')?>
			<!-- 해당페이지의 등록/업로드 버튼 있을 경우 -->
		</div>



<?php

	// 넘길 변수 설정하기
	$_PVS = ""; // 링크 넘김 변수
	foreach(array_filter(array_merge($_POST,$_GET)) as $key => $val) {
		if(is_array($val)) {
			foreach($val as $sk=>$sv) { $_PVS .= "&" . $key ."[" . $sk . "]=$sv";  }
		}
		else {
			$_PVS .= "&$key=$val";
		}
	}
	$_PVSC = enc('e' , $_PVS);
	// 넘길 변수 설정하기

	// 회원타입을 선택하지 않으면 전체선택
	if( count($pass_type) < 1){ $pass_type = array('D','F','K','N'); }

	// 회원 관리 --- 검색폼 불러오기
	//			반드시 - s_query가 적용되어야 함.
	$s_query = " from smart_individual_sleep as indr where 1 and in_sleep_type = 'Y' AND in_out = 'N' ";
	include_once("_individual.inc_search.php");
	//	==> s_query 리턴됨.

	if(!$listmaxcount) $listmaxcount = 50;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'ins_rdate';
	if(!$so) $so = 'desc';
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스

	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * $s_query order by {$st} {$so} limit $count , $listmaxcount ");



?>


		<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
		<!-- ● 데이터 리스트 -->
		<div class="data_list">
		<form name="frm" id="frm" method="post">
			<input type="hidden" name="_mode" value="">
			<input type="hidden" name="searchQue" value="<?=enc('e',$s_query)?>">
			<input type="hidden" name="searchCnt" value="<?=count($res)?>">
			<input type="hidden" name="ctrlMode" value="">
			<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
			<input type="hidden" name="apply" value="true">
			<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">


			<!-- ●리스트 컨트롤영역 -->
			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="ctrlOutMail('select'); return false;" class="c_btn h27">선택회원 휴면메일발송</a>
					<a href="#none" onclick="ctrlOutMail('search'); return false;" class="c_btn h27">검색회원 휴면메일발송(<?=number_format($TotalCount)?>)</a>
					<a href="#none" onclick="ctrlReturn('select'); return false;" class="c_btn h27">선택회원 휴면해제</a>
					<a href="#none" onclick="ctrlReturn('search'); return false;" class="c_btn h27">검색회원 휴면해제(<?=number_format($TotalCount)?>)</a>
				</div>
				<div class="right_box">
					<a href="#none" onclick="ctrlExcelDownload('select'); return false;"  class="c_btn icon icon_excel">선택회원 엑셀다운로드</a>
					<a href="#none" onclick="ctrlExcelDownload('search'); return false;"  class="c_btn icon icon_excel">검색회원 엑셀다운로드(<?=number_format($TotalCount)?>)</a>

					<select class="h27" onchange="location.href=this.value;">
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
					</select>

				</div>
			</div>

			<table class="table_list">
				<colgroup>
					<col width="35"/><col width="65"/><col width="65"/><col width="120"/><col width="*"/><col width="150"/><col width="*"/><col width="*"/><col width="*"/><col width="110"/><col width="110"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK"></label></th>
						<th scope="col">번호</th>
						<th scope="col">승인</th>
						<th scope="col">휴면메일발송여부</th>
						<th scope="col">아이디</th>
						<th scope="col">성명</th>
						<th scope="col">이메일</th>
						<th scope="col">전화</th>
						<th scope="col">휴대폰</th>
						<th scope="col">휴면전환일</th>
						<th scope="col">최근접속일</th>
					</tr>
				</thead>
				<tbody>
				<?php
					foreach($res as $k=>$v) {
						$_num = $TotalCount - $count - $k ;
						$_num = number_format($_num);
						$printEmail = $v['in_email'] != '' ? trim($v['in_email']):'';
						$printTel = rm_str($v['in_tel']) == '' ? '-' : tel_format($v['in_tel']);  // 전화
						$printTel2 = rm_str($v['in_tel2']) == '' ? '-' : tel_format($v['in_tel2']);  // 휴대폰

						$printSendEmail = $v['ins_mailing'] == 'Y' ? '<span class="c_tag h18 blue">발송</span>':'<span class="c_tag h18 gray">미발송</span>'; // 휴면메일발송여부
						$printSleepdate = rm_str($v['ins_rdate']) > 0 ?  date('Y-m-d',strtotime($v['ins_rdate'])) : '-'; // 휴면전환일
						$printLdate = rm_str($v['in_ldate']) > 0 ?  date('Y-m-d',strtotime($v['in_ldate'])) : '-'; // 최근접속일

						$printBtn = '
							<div class="lineup-center">
								<a href="#none" onclick="return false;" class="c_btn h22 gray get-send-email" data-id="'.$v['in_id'].'">휴면메일발송</a>
								<a href="#none" onclick="return false;" class="c_btn h22 gray get-return" data-id="'.$v['in_id'].'">휴면해제</a>
							</div>
						'; // 관리버튼


/*
<span class="c_tag gray h18 gray">숨김</span>
<span class="c_tag blue h18 blue line">노출</span>
*/
						// -- 승인여부
						if($v['in_auth']  != 'Y' ){
							$printAuth = '<span class="c_tag gray h18 gray">미승인</span>';
						}else{
							$printAuth = '<span class="c_tag gray h18 blue line">승인</span>';
						}

						// -- 출력
						echo '<tr>';
						echo '	<td><label class="design"><input type="checkbox" class="js_ck in-id" name="arrID[]" value="'.$v['in_id'].'"></label></td>';
						echo '	<td>'.$_num.'</td>';
						echo '	<td><div class="lineup-vertical">'.$printAuth.'</div></td>';
						echo '	<td><div class="lineup-center">'.$printSendEmail.'</div></td>';
						echo '	<td>'.$v['in_id'].'</td>';
						echo '	<td>'.$v['in_name'].'</td>';
						echo '	<td>'.$printEmail.'</td>';
						echo '	<td>'.$printTel.'</td>';
						echo '	<td>'.$printTel2.'</td>';
						echo '	<td>'.$printSleepdate.'</td>';
						echo '	<td>'.$printLdate.'</td>';
						echo '</tr>';
					}
				?>
				</tbody>

			</table>

				<?php if(count($res) <  1) {  ?>
								<!-- 내용없을경우 -->
								<div class="common_none"><div class="no_icon"></div><div class="gtxt">휴면회원이 없습니다.</div></div>

				<?php } ?>

		</form>
		</div>


		<!-- ●●● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
		<div class="paginate">
			<?php echo pagelisting($listpg, $Page, $listmaxcount," ?&${_PVS}&listpg=" , "Y")?>
		</div>



<script>

	// -- 선택 엑셀 다운로드
	function ctrlExcelDownload(ctrlMode)
	{
		if( ctrlMode == 'select'){ // 선택
			var chkLen = $('.js_ck:checked').length; // 선택된 것의 길이
			if( chkLen < 1){ alert("한명이상 선택해 주세요."); return false; }
		}else if(ctrlMode == 'search'){
			var chkCnt = $('form#frm [name="searchCnt"]').val()*1;
			if( chkCnt < 1){ alert("검색된 회원이 없습니다."); return false; }
		}

		$('form#frm [name="_mode"]').val('getExcelDownload');
		$('form#frm [name="ctrlMode"]').val(ctrlMode);
		$('form#frm').attr('action','_individual_sleep.pro.php');

		frm.submit();
	}

	// -- 휴면해제 처리
	function ctrlReturn(ctrlMode)
	{


		if(ctrlMode == 'select'){
				if(confirm("선택된 회원을 휴면해제 하시겠습니까?") == false){ return false; }
			var chkLen = $('.js_ck:checked').length; // 선택된 것의 길이
			if( chkLen < 1){ alert("한명이상 선택해 주세요."); return false; }
		}else if(ctrlMode == 'search'){
				if(confirm("검색된 회원을 휴면해제 하시겠습니까?") == false){ return false; }
			var chkCnt = $('form#frm [name="searchCnt"]').val()*1;
			if( chkCnt < 1){ alert("검색된 회원이 없습니다."); return false; }
		}else{ return false; }

		$('form#frm [name="_mode"]').val('getSleepReturn');
		$('form#frm [name="ctrlMode"]').val(ctrlMode);
		$('form#frm').attr('action','_individual_sleep.pro.php');
		$('form#frm').submit();
	}

		// -- 휴면해제 처리
	function ctrlOutMail(ctrlMode)
	{

		if(ctrlMode == 'select'){
			if(confirm("선택된 회원에게 휴면메일을 발송하시겠습니까?") == false){ return false; }
			var chkLen = $('.js_ck:checked').length; // 선택된 것의 길이
			if( chkLen < 1){ alert("한명이상 선택해 주세요."); return false; }
		}else if(ctrlMode == 'search'){
			if(confirm("검색된 회원에게 휴면메일을 발송하시겠습니까?") == false){ return false; }
			var chkCnt = $('form#frm [name="searchCnt"]').val()*1;
			if( chkCnt < 1){ alert("검색된 회원이 없습니다."); return false; }
		}else{ return false; }

		$('form#frm [name="_mode"]').val('getSleepReturnMail');
		$('form#frm [name="ctrlMode"]').val(ctrlMode);
		$('form#frm').attr('action','_individual_sleep.pro.php');
		$('form#frm').submit();
	}


</script>
<?php
	include_once('wrap.footer.php');

	# 수신거부 고객을 포함하여 재발송 확인
	include_once OD_ADDONS_ROOT."/080deny/_inc.reconfirm.php";
?>
