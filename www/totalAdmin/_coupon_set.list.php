<?php 

	// -- LCY -- 기세판 목록
	include_once('wrap.header.php');

	// 추가파라메터
	if(!$arr_param) $arr_param = array();

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


	//   --- 검색폼 불러오기
	//			반드시 - s_query가 적용되어야 함.
	$s_query = "  from smart_individual_coupon_set  where 1  ";


	// -- 검색시작 -- {{{
	if( $searchMode == 'true') { 

		// -- 검색어
		if($pass_input_type == 'name'){
			$s_query .= " and ocs_name like '%".$pass_input."%' ";
		}else if( $pass_input_type == 'desc'){
			$s_query .= " and ocs_desc like '%".$pass_input."%' ";
		}else if( $pass_input_type == 'all'){
			$s_query .= " and ( ocs_name like '%".$pass_input."%' or ocs_desc like '%".$pass_input."%') ";
		}

		if( $pass_type!= '') {  $s_query .= " and ocs_type = '".$pass_type."'  "; }// -- 쿠폰유형 
		if( $pass_issued_type!= '') {  $s_query .= " and ocs_issued_type = '".$pass_issued_type."'  "; }// -- 발급방법 

		// 등록일 검색
		if( $pass_sdate != '' && $pass_edate != ''){
			$s_query .= " and  ( left(ocs_rdate,10) BETWEEN '".$pass_sdate."' and '".$pass_edate."' ) ";
		}else{
			if($pass_sdate != ''){
				$s_query .= " and left(ocs_rdate,10) >= '".$pass_sdate."' ";
			}

			if($pass_edate != ''){
				$s_query .= " and left(ocs_rdate,10) <= '".$pass_edate."' ";
			}
		}	
	}
	// -- 검색종료 -- }}} 


	// -- 노출구분이 있을 시 :: 순위변경 가능할 시
	if(!$st) $st = 'ocs_uid';
	if(!$so) $so = 'desc';

	if(!$listmaxcount) $listmaxcount = 50;
	if(!$listpg) $listpg = 1;
	$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스

	$res = _MQ(" select count(*) as cnt  $s_query ");
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * , (select count(*) as cnt from smart_individual_coupon where coup_ocs_uid = ocs_uid ) as issued_cnt $s_query order by {$st} {$so} limit $count , $listmaxcount ");

	$arrSearchPass['type'] = array(''=>'전체') +  $arrCouponSet['ocs_type'];  
	$arrSearchPass['issued_type'] = array(''=>'전체') +  $arrCouponSet['ocs_issued_type'];  
?>
	
	<!-- 단락타이틀 -->
	<div class="group_title">
		<strong>쿠폰 목록</strong><!-- 메뉴얼로 링크 --><?=openMenualLink('쿠폰목록')?>
		<!-- 해당페이지의 등록/업로드 버튼 있을 경우 -->
		<div class="btn_box"><a href="_coupon_set.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">쿠폰등록</a></div>		
	</div>

	<!-- ● 폼 영역 (검색/폼 공통으로 사용) : 검색으로 사용할 시 if_search -->
	<form name="searchfrm" id="searchfrm" method=get action='<?=$_SERVER["PHP_SELF"]?>'>
	<input type=hidden name="searchMode" value="true">	
	<input type="hidden" name="st" value="<?php echo $st; ?>">
	<input type="hidden" name="so" value="<?php echo $so; ?>">
	<input type="hidden" name="listmaxcount" value="<?php echo $listmaxcount; ?>">
	<?php if(sizeof($arr_param)>0){ foreach($arr_param as $__k=>$__v){ ?>
	<input type="hidden" name="<?php echo $__k; ?>" value="<?php echo $__v; ?>">
	<?php }} ?>

		<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form if_search">
			<table class="table_form">	
				<colgroup>
					<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th>검색어</th>
						<td>		
							<select name="pass_input_type">
								<option value="all" <?=$pass_input_type == 'all' ? 'selected' : ''?>>-전체검색-</option>
								<option value="name" <?=$pass_input_type == 'name' ? 'selected' : ''?>>쿠폰명</option>
								<option value="desc" <?=$pass_input_type == 'desc' ? 'selected' : ''?>>쿠폰설명</option>
							</select>						
							<input type="text" name="pass_input" class="design" style="" value="<?=$pass_input?>" />
						</td>
						<th>등록일 검색</th>
						<td>
							<input type="text" name="pass_sdate" value="<?php echo rm_str($pass_sdate) < 1 ? '': $pass_sdate ?>" class="design js_pic_day" readonly style="width:85px">
							<span class="fr_tx">-</span>
							<input type="text" name="pass_edate" value="<?php echo rm_str($pass_edate) < 1?  '': $pass_edate ?>" class="design js_pic_day" readonly style="width:85px">
						</td>
					</tr>
					
					<tr>
						<th>쿠폰유형</th>
						<td>
							<?php echo _InputRadio( 'pass_type' , array_keys($arrSearchPass['type']), ($pass_type) , '' , array_values($arrSearchPass['type']) , ''); ?>
						</td>
						<th>발급방법</th>
						<td>
							<?php echo _InputRadio( 'pass_issued_type' , array_keys($arrSearchPass['issued_type']), ($pass_issued_type) , '' , array_values($arrSearchPass['issued_type']) , ''); ?>
						</td>								
					</tr>			

					<tr>
						<td colspan="4">
							<div class="tip_box">
								<?php 
									echo _DescStr("발급방법 수동발급일경우 <em>수동발급</em> 버튼을 클릭하여 발급이 가능합니다.");
									echo _DescStr("한개라도 발급된 쿠폰이 있을경우 쿠폰 수정이 불가능합니다.");
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
						if($searchMode == 'true'){ 
							$arr_param = array_filter(array_merge(array('st'=>$st, 'so'=>$so, 'listmaxcount'=>$listmaxcount, 'menuUid'=>$menuUid),$arr_param));
					?>
						<li><a href="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?', $arr_param); ?>" class="c_btn h34 black line normal">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>


		</div>
	</form>




	<!-- ● 데이터 리스트 -->
	<div class="data_list">
	<form name="frm" id="frm" method="post" action="_bbs.board.pro.php">
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
		<input type="hidden" name="searchQue" value="<?=enc('e',$s_query)?>">
		<input type="hidden" name="searchCnt" value="<?=$TotalCount?>">
		<input type="hidden" name="ctrlMode" value="">
		<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
		<input type=hidden name="_uid" value=""> <?php // 개별실헹 :: 고유번호 저장 필드?>
		<input type=hidden name="_sort" value=""> <?php // 개별실행 ::  정렬방식 up,down,first,last?>

		<table class="table_list">
			<colgroup>
				<col width="70"/>
				<col width="*"/>
				<col width="70"/>
				<col width="100"/>
				<col width="*"/>
				<col width="220"/>
				<col width="160"/>
				<col width="100"/>
				<col width="80"/>
				<col width="120"/>

			</colgroup>
			<thead>
				<tr>
					<th scope="col">번호</th>
					<th scope="col">쿠폰명</th>
					<th scope="col">발급여부</th>
					<th scope="col">쿠폰유형</th>
					<th scope="col">쿠폰혜택</th>
					<th scope="col">사용기간</th>
					<th scope="col">발급방식</th>
					<th scope="col">발급내역</th>
					<th scope="col">등록일</th>
					<th scope="col">관리 </th>
				</tr>
			</thead> 
			<tbody>
			<?php
				foreach($res as $k=>$v) { 
					$_num = $TotalCount - $count - $k ;
					$_num = number_format($_num);

					// 사용기간
					if($v['ocs_use_date_type'] == 'date'){ // 사용기간 
						$printDate = $v['ocs_sdate'].'~'.$v['ocs_edate'];
					}else{
						$printDate = '발급일로부터 '.$v['ocs_expire'].'일까지 사용가능';
					}

					// 발급내역 버튼
					$printIussuedBtn = '
					<div class="lineup-vertical">
						<a href="_coupon.list.php?_uid='.$v['ocs_uid'].'&_PVSC='.$_PVSC.'"  class="c_btn h22">발급내역('.number_format($v['issued_cnt']).')</a>
					</div>
					'; 

					$printBtn = '
					<div class="lineup-vertical">
						<a href="_coupon_set.form.php?_mode=modify&_uid='.$v['ocs_uid'].'&_PVSC='.$_PVSC.'" class="c_btn h22">수정</a>
						<a href="#none" onclick="return false;" class="c_btn h22 gray delete-coupon" data-uid="'.$v['ocs_uid'].'" data-apply = "true">삭제</a>
					</div>
					'; // 관리버튼	

					// 발급여부
					if( $v['ocs_view'] == 'Y'){
						$printView = '<div class="lineup-vertical"><span class="c_tag h18 blue">발급중</span><a href="#none" onclick="CouponControllerView(\'N\',\''.$v['ocs_uid'].'\')" class="c_btn h22">발급중지</a></div>';
					}else{
						$printView = '<div class="lineup-vertical"><span class="c_tag h18 gray">발급중지</span><a href="#none" onclick="CouponControllerView(\'Y\',\''.$v['ocs_uid'].'\')" class="c_btn h22">발급시작</a></div>';
					}


					// 쿠폰명
					$printName = stripslashes($v['ocs_name']);

					// 쿠폰유형 
					$printType = $arrCouponSet['ocs_type'][$v['ocs_type']];

					// 할인혜택
					$printCouponSetBoon = printCouponSetBoon($v);

					// 발급방식 
					$printIssuedType = $arrCouponSet['ocs_issued_type'][$v['ocs_issued_type']];
					if( $v['ocs_issued_type'] == 'manual' ){



						if($v['ocs_view'] == 'N' || ( $v['ocs_use_date_type'] == 'date' &&  $v['ocs_edate'] < date('Y-m-d')) ){ // 발급중지 쿠폰은 발급안되도록 처리
							$printIssuedType = '
							<div class="lineup-vertical">
								<a href="#none" onclick="alert(\'발급중지된 쿠폰 및 사용기간이 지난 쿠폰은 발급이 불가능합니다.\'); return false;"  class="c_btn h22">'.$arrCouponSet['ocs_issued_type'][$v['ocs_issued_type']].'</a>
							</div>
							';
						}else{
							$printIssuedType = '
							<div class="lineup-vertical">
								<a href="_coupon.form.php?_uid='.$v['ocs_uid'].'&_PVSC='.$_PVSC.'"  class="c_btn h22">'.$arrCouponSet['ocs_issued_type'][$v['ocs_issued_type']].'</a>
							</div>
							';
						}

					}else if( $v['ocs_issued_type'] == 'auto') { 
						$printIssuedType .="<br/>(".$arrCouponSet['ocs_issued_type_auto'][$v['ocs_issued_type_auto']].")";
					}
					// -- 출력
					echo '<tr>';
					echo '	<td>'.$_num.'</td>';
					echo '	<td>'.$printName.'</td>';
					echo '	<td>'.$printView.'</td>';
					echo '	<td>'.$printType.'</td>';
					echo '	<td>'.$printCouponSetBoon.'</td>';
					echo '	<td>'.$printDate.'</td>';
					echo '	<td>'.$printIssuedType.'</td>';
					echo '	<td>'.$printIussuedBtn.'</td>';
					echo '	<td>'.printDateInfo($v['ocs_rdate']).'</td>';
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

	// 발급 
	function CouponControllerView(ctrlMode,_uid)
	{
		if( ctrlMode == 'Y'){
			if( confirm("해당 쿠폰에 대한 발급을 시작하시겠습니까?") == false){return false;}
		}else{
			if( confirm("해당 쿠폰에 대한 발급을 중지하시겠습니까?") == false){return false;}
		}

		location.href = "_coupon_set.pro.php?_mode=modifyView&ctrlMode="+ctrlMode+"&_uid="+_uid+"&_PVSC=<?=$_PVSC?>";
	}


	$(document).on('click','.delete-coupon',function(){
		if( confirm("해당 게시판을 삭제하시겠습니까?\n등록된 쿠폰이 있는경우 삭제가 불가능합니다.") == false){ return false; }

		var _uid = $(this).attr('data-uid');  // 고유번호 
		if( _uid == '' || _uid == undefined){ alert('잘못된 접근입니다.'); return false; }
		location.href = "_coupon_set.pro.php?_mode=delete&_uid="+_uid+"&_PVSC=<?=$_PVSC?>";
	});



</script>
<?php 
	 // viewarr($getBoardSkinInfo);
	include_once('wrap.footer.php'); 
?>
