<?php 
// -- 쿠폰발급
$app_current_link = '_coupon_set.list.php';
include_once('wrap.header.php');

$row = _MQ("select *, (select count(*) as cnt from smart_individual_coupon where coup_ocs_uid = ocs_uid ) as issued_cnt from  smart_individual_coupon_set where ocs_uid = '".$_uid."' ");
if( count($row) < 1){ error_loc_msg("_coupon_set.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "잘못된 접근입니다.");  }

if( $row['ocs_view'] != 'Y'){ error_loc_msg("_coupon_set.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "발급이 중지된 쿠폰입니다.");  }
if( $row['ocs_issued_type'] != 'manual'){ error_loc_msg("_coupon_set.list.php?". ($_PVSC?enc('d' , $_PVSC):enc('d' , $pass_variable_string_url)), "쿠폰발급유형이 수동발급인 쿠폰만 발급가능합니다."); }

$rowChk = _MQ("select count(*) as cnt from smart_individual_coupon where coup_ocs_uid = '".$_uid."'  ");


?>


<!-- ●단락타이틀 -->
<div class="group_title"><strong>쿠폰정보</strong><!-- 메뉴얼로 링크 --> </div>


<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
<div class="data_form">
	<table class="table_form">	
		<colgroup>
			<col width="180"/><col width="*"/>
		</colgroup>
		<tbody>
			<tr> 
				<th>발급상태</th>
				<td>
					<?php 

						// 발급여부
						if( $row['ocs_view'] == 'Y'){
							echo '<div class="lineup-center"><span class="c_tag h18 blue">발급중</span></div>';
						}else{
							echo '<div class="lineup-center"><span class="c_tag h18 gray">발급중지</span></div>';
						}

					?>
				</td>
			</tr>												

			<tr> 
				<th>쿠폰유형</th>
				<td>
					<?php echo $arrCouponSet['ocs_type'][$row['ocs_type']]; ?>
				</td>
			</tr>												

			<tr> 
				<th>발급방법</th>
				<td>
					<?php echo $arrCouponSet['ocs_issued_type'][$row['ocs_issued_type']].($row['ocs_issued_type'] == 'auto' ? ' ('.$arrCouponSet['ocs_issued_type_auto'][$row['ocs_issued_type_auto']].')':null); ?>
				</td>
			</tr>	

			<tr> 
				<th>쿠폰명</th>
				<td>
					<?php echo stripslashes($row['ocs_name']); ?>
				</td>
			</tr>	
			
			<?php if( trim($row['ocs_desc']) != ''){ ?> 
			<tr> 
				<th>쿠폰설명</th>
				<td>
					<?php echo stripslashes($row['ocs_desc']); ?>
				</td>
			</tr>	
			<?php } ?>

			<tr> 
				<th>사용기간</th>
				<td>
					<?php 
						// 사용기간 
						if($row['ocs_use_date_type'] == 'date'){ // 사용기간 
							echo $row['ocs_sdate'].'~'.$row['ocs_edate'];
						}else{
							echo  '발급일로부터 '.$row['ocs_expire'].'일까지 사용가능';
						}
					?>
				</td>
			</tr>

			<tr> 
				<th>쿠폰혜택</th>
				<td>
					<?php 
						// 할인혜택
						echo printCouponSetBoon($row);							
					?>
				</td>
			</tr>

			<tr> 
				<th>발급내역</th>
				<td>
					<?php 
						// 사용기간 
						// 발급내역 버튼
						echo '
						<div class="lineup-center">
							<a href="_coupon.list.php?_uid='.$_uid.'"  class="c_btn h22">발급내역('.number_format($row['issued_cnt']).')</a>
						</div>
						'; 						
					?>
				</td>
			</tr>

		</tbody> 
	</table>
</div>


<!-- 단락타이틀 -->
<div class="group_title">
	<strong>발급회원 검색</strong><!-- 메뉴얼로 링크 --><?=openMenualLink('발급회원 검색')?>	
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

	// 추가파라메터
	if(!$arr_param) $arr_param = array();
	$arr_param = array('_uid'=>$_uid);

	// 회원타입을 선택하지 않으면 전체선택
	if( count($pass_type) < 1){ $pass_type = array('D','F','K','N'); }

	// 회원 관리 --- 검색폼 불러오기
	//			반드시 - s_query가 적용되어야 함.

	$s_query = " from smart_individual as indr where 1 and in_sleep_type = 'N' AND in_out = 'N' ";

/*	if( $row['ocs_issued_type'] != 'manual'){
		$s_query .= " and (select count(*) from smart_individual_coupon where coup_ocs_uid = '".$_uid."' and coup_inid = indr.in_id ) < 1  ";
	}
*/
	include_once("_individual.inc_search.php");
	//	==> s_query 리턴됨.
	
	if(!$listmaxcount) $listmaxcount = 50;
	if(!$listpg) $listpg = 1;
	if(!$st) $st = 'in_rdate';
	if(!$so) $so = 'desc';
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스


	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * , ( select count(*) from smart_individual_coupon where coup_ocs_uid = '".$_uid."' and coup_inid = indr.in_id ) as issuedMemCnt $s_query order by {$st} {$so} limit $count , $listmaxcount ");


?>

<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->

<!-- ● 데이터 리스트 -->
<div class="data_list">
<form name="frm" id="frm" method="post" action="_coupon.pro.php">
	<input type="hidden" name="_PVSC" value="<?=$_PVSC?>"> <?php // -- 기본모드 --- 미사용 모든건 ajax 에서 체크 ?>
	<input type="hidden" name="_uid" value="<?php echo $_uid; ?>">
	<input type="hidden" name="issuedCnt" value="<?php echo $rowChk['cnt'] < 1 ? 0 : $rowChk['cnt']; ?>">	
	<input type="hidden" name="_mode" value="issued">
	<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
	<input type="hidden" name="searchQue" value="<?=enc('e',$s_query)?>">
	<input type="hidden" name="searchCnt" value="<?=$TotalCount?>">
	<input type="hidden" name="ctrlMode" value="">
	<input type=hidden name="_PVSC" value="<?=$_PVSC?>">



	<!-- ●리스트 컨트롤영역 -->
	<div class="list_ctrl">
		<div class="left_box">
			<a href="#none" onclick="ctrlIssued('select'); return false;" class="c_btn h27">선택회원 쿠폰발급</a>
			<a href="#none" onclick="ctrlIssued('search'); return false;" class="c_btn h27">검색회원 쿠폰발급(<?=number_format($TotalCount)?>)</a>
		</div>
		<div class="right_box">


			<select class="h27" onchange="location.href=this.value;">
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_rdate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'in_rdate' && $so == 'asc'?' selected':null); ?>>가입일 ▲</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_rdate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'in_rdate' && $so == 'desc'?' selected':null); ?>>가입일 ▼</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_ldate', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'in_ldate' && $so == 'asc'?' selected':null); ?>>최근접속일 ▲</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_ldate', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'in_ldate' && $so == 'desc'?' selected':null); ?>>최근접속일 ▼</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_name', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'in_name' && $so == 'asc'?' selected':null); ?>>이름순 ▲</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_name', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'in_name' && $so == 'desc'?' selected':null); ?>>이름순 ▼</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_id', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'in_id' && $so == 'asc'?' selected':null); ?>>아이디 ▲</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_id', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'in_id' && $so == 'desc'?' selected':null); ?>>아이디 ▼</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_point', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'in_point' && $so == 'asc'?' selected':null); ?>>적립금 ▲</option>
				<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'in_point', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'in_point' && $so == 'desc'?' selected':null); ?>>적립금 ▼</option>
		
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
			<col width="35"/>
			<col width="65"/>
			<col width="65"/>
			<col width="*"/>
			<col width="150"/>
			<col width="*"/>
			<col width="*"/>
			<col width="*"/>
			<col width="100"/>
			<col width="100"/>
			<col width="75"/>
		</colgroup>
		<thead>
			<tr>
				<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK"></label></th>
				<th scope="col">번호</th>
				<th scope="col">승인</th>
				<th scope="col">아이디</th>
				<th scope="col">성명</th>
				<th scope="col">이메일</th>
				<th scope="col">전화</th>
				<th scope="col">휴대폰</th>
				<th scope="col">가입일</th>
				<th scope="col">최근접속일</th>
				<th scope="col">발급개수</th>
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
				$printRdate = rm_str($v['in_rdate']) > 0 ?  date('Y-m-d',strtotime($v['in_rdate'])) : '-'; // 가입일 
				$printLdate = rm_str($v['in_ldate']) > 0 ?  date('Y-m-d',strtotime($v['in_ldate'])) : '-'; // 최근접속일 

				// -- 승인여부 
				if($v['in_auth']  != 'Y' ){ 
					$printAuth = '<span class="c_tag gray h18 gray t3">미승인</span>';
				}else{
					$printAuth = '<span class="c_tag gray h18 blue line t3">승인</span>';
				}

				// -- 출력
				echo '<tr>';
				echo '	<td><label class="design"><input type="checkbox" class="js_ck in-id" name="arrID[]" value="'.$v['in_id'].'"></label></td>';
				echo '	<td>'.$_num.'</td>';
				echo '	<td><div class="lineup-vertical">'.$printAuth.'</div></td>';
				echo '	<td>'.$v['in_id'].'</td>';
				echo '	<td>'.$v['in_name'].'</td>';
				echo '	<td>'.$printEmail.'</td>';
				echo '	<td>'.$printTel.'</td>';
				echo '	<td>'.$printTel2.'</td>';
				echo '	<td>'.$printRdate.'</td>';
				echo '	<td>'.$printLdate.'</td>';
				echo '	<td>'.number_format($v['issuedMemCnt']).'</td>';
				echo '</tr>';
			}
		?>
		</tbody>

	</table>

		<?php if(count($res) <  1) {  ?> 
						<!-- 내용없을경우 -->
						<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 내용이 없습니다.</div></div>

		<?php } ?>

</div>

</form>

<!-- ●●● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
<div class="paginate">
	<?php echo pagelisting($listpg, $Page, $listmaxcount," ?&${_PVS}&listpg=" , "Y")?>
</div>



<script>
 function ctrlIssued(ctrlMode)
 {
 	if( ctrlMode == 'select'){
 		if( confirm("선택하신 회원에 대해 쿠폰발급을 하시겠습니까?") == false){ return false; }
 		var chkLen = $('.in-id:checked').length *1;
 		if( chkLen < 1){ alert("회원을 한명이상 선택해 주세요."); return false; }
 	}else if( ctrlMode == 'search'){
 		if( confirm("검색하신 회원에 대해 쿠폰발급을 하시겠습니까?") == false){ return false; }
 		var chkCnt = $('form#frm input[name="searchCnt"]').val()*1;
 		if( chkCnt < 1){ alert("쿠폰발급을 할 수 있는 회원이 없습니다.");  }
 	}else{ alert("잘못된 접근입니다."); return false; }

	$('form#frm input[name="ctrlMode"]').val(ctrlMode);
	$('form#frm').submit();
	$('form#frm input[name="ctrlMode"]').val('');

 }
</script>


<?php 
	include_once('wrap.footer.php');
?>