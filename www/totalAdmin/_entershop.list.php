<?php
	// -- LCY -- 입점업체리스트
	include_once('wrap.header.php');

	if( $SubAdminMode !== true){  error_msg("이용할 수 없는 메뉴입니다."); }

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
?>

		<!-- 단락타이틀 -->
		<div class="group_title">
			<strong>입점업체검색</strong><!-- 메뉴얼로 링크 --><?=openMenualLink('입점업체검색')?>
			<!-- 해당페이지의 등록/업로드 버튼 있을 경우 -->
			<div class="btn_box"><a href="_entershop.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">입점업체등록</a></div>
		</div>

<?
	// 입점업체 관리 --- 검색폼 불러오기
	//			반드시 - s_query가 적용되어야 함.
	$s_query = " where 1 ";

	include_once("_entershop.inc_search.php");
	//	==> s_query 리턴됨.

	if(!$listmaxcount) $listmaxcount = 50;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'cp_rdate';
	if(!$so) $so = 'desc';
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스


	$res = _MQ(" select count(*) as cnt  from smart_company $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select *, (select count(*) from smart_product where p_cpid = cp_id ) as pcnt from smart_company  $s_query order by {$st} {$so} limit $count , $listmaxcount ");
?>

		<!-- ● 데이터 리스트 -->
		<div class="data_list">
		<form name="frm" id="frm" method="post">
			<input type="hidden" name="_mode" value="">
			<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
			<input type="hidden" name="searchQue" value="<?=enc('e',$s_query)?>">
			<input type="hidden" name="searchCnt" value="<?=$TotalCount?>">
			<input type="hidden" name="ctrlMode" value="">
			<input type=hidden name="_PVSC" value="<?=$_PVSC?>">



			<!-- ●리스트 컨트롤영역 -->
			<div class="list_ctrl">
				<div class="right_box">
					<a href="#none" onclick="ctrlExcelDownload('select'); return false;"  class="c_btn icon icon_excel">선택 엑셀다운로드</a>
					<a href="#none" onclick="ctrlExcelDownload('search'); return false;"  class="c_btn icon icon_excel">검색 엑셀다운로드(<?=$TotalCount?>)</a>

					<select class="h27" onchange="location.href=this.value;">
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'cp_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'cp_rdate' && $so == 'asc'?' selected':null); ?>>등록일 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'cp_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'cp_rdate' && $so == 'desc'?' selected':null); ?>>등록일 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'cp_name', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'cp_name' && $so == 'asc'?' selected':null); ?>>업체명순 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'cp_name', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'cp_name' && $so == 'desc'?' selected':null); ?>>업체명순 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'cp_id', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'cp_id' && $so == 'asc'?' selected':null); ?>>아이디 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'cp_id', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'cp_id' && $so == 'desc'?' selected':null); ?>>아이디 ▼</option>
					</select>

					<select class="h27" onchange="location.href=this.value;">
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>20), array('listpg')); ?>"<?php echo ($listmaxcount == 20?' selected':null); ?>>20개씩</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>50), array('listpg')); ?>"<?php echo ($listmaxcount == 50?' selected':null); ?>>50개씩</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('listmaxcount'=>100), array('listpg')); ?>"<?php echo ($listmaxcount == 100?' selected':null); ?>>100개씩</option>
					</select>

				</div>
			</div>

			<table class="table_list">
				<colgroup>
					<col width="35"/><col width="65"/><col width="*"/><col width="150"/><col width="150"/><col width="150"/><col width="*"/><col width="*"/><col width="90"/><col width="180"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK"></label></th>
						<th scope="col">번호</th>
						<th scope="col">아이디</th>
						<th scope="col">업체명</th>
						<th scope="col">대표자</th>
						<th scope="col">전화</th>
						<th scope="col">담당자 이메일</th>
						<th scope="col">담당자 휴대폰</th>
						<th scope="col">등록일</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
				<?php
					foreach($res as $k=>$v) {
						$_num = $TotalCount - $count - $k ;
						$_num = number_format($_num);
						$printEmail = $v['cp_email'] != '' ? trim($v['cp_email']):''; // 담당자 이메일
						$printTel = rm_str($v['cp_tel']) == '' ? '-' : tel_format($v['cp_tel']);  // 담당자 전화
						$printTel2 = rm_str($v['cp_tel2']) == '' ? '-' : tel_format($v['cp_tel2']);  // 담당자 휴대폰
						$printRdate = rm_str($v['cp_rdate']) > 0 ?  date('Y-m-d',strtotime($v['cp_rdate'])) : '-'; // 등록일
						$arrDisplayDelClass = array();

						if( $v['cp_id'] == 'hyssence' )  $arrDisplayDelClass[] = 'disabled-admin';
						if( $v['pcnt'] > 0 )  $arrDisplayDelClass[] = 'disabled-pcnt';

						$printBtn = '
							<div class="lineup-center">
								<a href="'.OD_ADMIN_DIR.'/?_mode=autologin&_id='.urlencode($v['cp_id']).'&userType=com" target="_blank" class="c_btn h22">로그인</a>
								<a href="_entershop.form.php?_mode=modify&_id='.urlencode($v['cp_id']).'&_PVSC='.$_PVSC.'" class="c_btn h22">수정</a>
								<a href="#none" onclick="return false;" class="c_btn h22 gray on-get-secession '.(count($arrDisplayDelClass) > 0 ? implode(" ",$arrDisplayDelClass) : null).'" data-id="'.$v['cp_id'].'" data-apply = "true">삭제</a>
							</div>
						'; // 관리버튼

						// -- 출력
						echo '<tr>';
						echo '	<td><label class="design"><input type="checkbox" class="js_ck cp-id" name="arrID[]" value="'.$v['cp_id'].'"></label></td>';
						echo '	<td>'.$_num.'</td>';
						echo '	<td>'.$v['cp_id'].'</td>';
						echo '	<td>'.$v['cp_name'].'</td>';
						echo '	<td>'.$v['cp_ceoname'].'</td>';
						echo '	<td>'.$printTel.'</td>';
						echo '	<td>'.$printEmail.'</td>';
						echo '	<td>'.$printTel2.'</td>';
						echo '	<td>'.$printRdate.'</td>';
						echo '	<td>'.$printBtn.'</td>';
						echo '</tr>';
					}
				?>
				</tbody>

			</table>

				<?php if(count($res) <  1) {  ?>
								<!-- 내용없을경우 -->
								<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>

				<?php } ?>

		</form>
		</div>


		<!-- ●●● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
		<div class="paginate">
			<?php echo pagelisting($listpg, $Page, $listmaxcount," ?&${_PVS}&listpg=" , "Y")?>
		</div>



<script>
	// -- 입점업체 삭제
	$(document).on('click','.on-get-secession',function(){
		var cpID = $(this).attr('data-id'); // 입점업체아이디
		var adminChk = $(this).hasClass('disabled-admin'); // 기본 입점업체 체크
		var pcntChk = $(this).hasClass('disabled-pcnt'); // 입점업체로 등록되어있는 상품체크
		if( cpID == '' || cpID == ''){ return false;}
		if( adminChk == true){ alert('기본 입점업체 계정은 삭제 할 수 없습니다.'); return false; } // 기본 입점계정은 삭제처리 불가능
		if( pcntChk == true){
			if( confirm("본 입점업체로 등록된 상품이 존재합니다.\n그래도 삭제하시겠습니까?") == false){ return false; }
		}else{
			if( confirm("선택하신 입점업체을 삭제 처리 하시겠습니까?") == false){ return false; }
		}
		var apply = $(this).attr('data-apply');
		if( apply != 'true'){ alert("잠시만 기다려주세요.\n현재 선택입점업체에 대한 삭제처리를 진행중입니다."); return false;  }
		var url = '_entershop.ajax.php';
		  $.ajax({
		      url: url, cache: false,dataType : 'json', type: "post", data: {ajaxMode : 'delete' , cpID : cpID }, success: function(data){
		      	$(this).attr('data-apply','true');
		      	if( data == undefined){ return false; }
		      	if( data.rst == 'success'){
		      		alert(data.msg);
		      		window.location.reload();
		      		return false;
		      	}else{ alert(data.msg); return false; }
		      },error:function(request,status,error){ console.log(request.responseText);}
		  });
	})


	// -- 선택/검색 엑셀 다운로드
	function ctrlExcelDownload(ctrlMode)
	{
		if( ctrlMode == 'select'){ // 선택
			var chkLen = $('.js_ck:checked').length; // 선택된 것의 길이
			if( chkLen < 1){ alert("한개이상 선택해 주세요."); return false; }
		}else if(ctrlMode == 'search'){
			var chkCnt = $('form#frm [name="searchCnt"]').val()*1;
			if( chkCnt < 1){ alert("검색된 입점업체이 없습니다."); return false; }
		}

		$('form#frm [name="_mode"]').val('getExcelDownload');
		$('form#frm [name="ctrlMode"]').val(ctrlMode);
		$('form#frm').attr('target','common_frame');
		$('form#frm').attr('action','_entershop.pro.php');

		frm.submit();
	}


</script>
<?php
	include_once('wrap.footer.php');

	# 수신거부 고객을 포함하여 재발송 확인
	include_once OD_ADDONS_ROOT."/080deny/_inc.reconfirm.php";
?>
