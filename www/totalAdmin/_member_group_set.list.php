<?php
	// -- LCY -- 회원등급관리
	include_once('wrap.header.php');

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
	$res = _MQ_assoc(" select mgs.*, (select count(*) as cnt from smart_individual where in_mgsuid = mgs_uid ) as cnt from smart_member_group_set as mgs  where 1 order by mgs_idx desc, mgs_rank desc");

?>

		<!-- 단락타이틀 -->
		<div class="group_title">
			<strong>등급안내사항</strong><!-- 메뉴얼로 링크 -->
			<!-- 해당페이지의 등록/업로드 버튼 있을 경우 -->
			<div class="btn_box"><a href="_member_group_set.form.php?_mode=add" class="c_btn h46 red">회원 등급등록</a></div>
		</div>

		<table class="table_form">
			<colgroup>
				<col width="*">
			</colgroup>
			<tbody>
				<tr>
					<td>
						<div class="tip_box">
							<div class="c_tip">회원등급 삭제 시 등록된 회원이 존재할 경우 삭제가 불가능합니다.</div>
							<div class="c_tip">등급순서의 경우 낮을 수록 제일 먼저 노출되며 기본등급의 경우 순서변경이 불가능합니다.</div>
							<div class="c_tip">등급평가 시 등급순서가 가장 높은 등급이 최종 적용되므로 중복되지 않는 숫자를 입력해 주셔야 합니다.</div>
							<div class="c_tip">등록된 회원 수의 경우 각 등급별 등록된 회원의 수를 의미합니다.</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>


		<!-- 가운데정렬버튼 -->
		<div class="c_btnbox">

		</div>


		<!-- ● 데이터 리스트 -->
		<div class="data_list">
		<form name="frm" id="frm" method="post" target="common_frame" action="_member_group_set.pro.php">
			<input type="hidden" name="_mode" value="">
			<input type="hidden" name="_uid" value="">

			<?php if(count($res) >  0) {  ?>

			<!-- ●리스트 컨트롤영역 -->
			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick=" return false;" class="c_btn h27 on-select-delete">선택등급 삭제</a>
					<a href="#none" onclick=" return false;" class="c_btn h27 on-select-idx">등급순서 일괄적용</a>
				</div>
			</div>

			<table class="table_list">
				<colgroup>
					<col width="35"/><col width="*"/><col width="*"/><col width="*"/><col width="*"/><col width="*"/><col width="120"/><col width="180"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK"></label></th>
						<th scope="col">등급순서</th>
						<th scope="col">등급명</th>
						<th scope="col">등록된 회원 수</th>
						<th scope="col">등급조건</th>
						<th scope="col">등급혜택</th>
						<th scope="col">등록일</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>
				<?php
					foreach($res as $k=>$v) {

						$printRank = $v['mgs_rank'];
						$printName = $v['mgs_name'];

						// -- 등록된 회원수를 가져온다.
						//$rowRcnt = _MQ_result("select count(*) as cnt from smart_individual where  in_mgsuid = '".$v['mgs_uid']."' ");
						$rowRcnt = $v['cnt'];
						$printRcnt = number_format($rowRcnt)."명"; //  등록된 회원 수

						// -- 등급조건을 가져온다.
						$arrCondition = array(); $printCondition = '';
						if($v['mgs_condition_totprice'] > 0){ $arrCondition[] = number_format($v['mgs_condition_totprice']).'원 이상 구매'; }
						if($v['mgs_condition_totcnt'] > 0){ $arrCondition[] = number_format($v['mgs_condition_totcnt']).'회 이상 구매'; }
						if(count($arrCondition) > 0){ $printCondition = implode(", ",$arrCondition); }
						else{ $printCondition = '제한없음'; }

						// -- 등급혜택을 가져온다.
						$arrBoon = array(); $printBoon = '';
						if($v['mgs_give_point_per'] > 0){ $arrBoon[] = number_format($v['mgs_give_point_per'],1).'% 적립'; }
						if($v['mgs_sale_price_per'] > 0){ $arrBoon[] = number_format($v['mgs_sale_price_per'],1).'% 할인'; }
						if(count($arrBoon) > 0){ $printBoon = implode(", ",$arrBoon); }
						else{ $printBoon = '없음'; }

						// -- 등록일
						$printRdate = date('Y-m-d',strtotime($v['mgs_rdate']));

						// {{{회원등급추가}}}
						$disabledDeleteClass = $readonlyAttr = '';
						if($v['mgs_rank'] == 1 ){ $disabledDeleteClass = 'disabled'; $readonlyAttr = "readonly";   }
						else{
							$readonlyAttr .=" tabindex='".($k+1)."' ";
						}


						$printBtn = '
							<div class="lineup-center">
								<a href="_member_group_set.form.php?_mode=modify&_uid='.$v['mgs_uid'].'&_PVSC='.$_PVSC.'" class="c_btn h22">수정</a>
								<a href="#none" onclick="return false;" class="c_btn h22 gray on-get-delete '.$readonlyClass.'" data-uid="'.$v['mgs_uid'].'">삭제</a>
							</div>
						'; // 관리버튼



						$printIdx = '
							<div class="lineup-center" style="margin-bottom:5px;">
								<input type="text" name="_idx['.$v['mgs_uid'].']" value="'.$v['mgs_idx'].'" data-value="'.$v['mgs_idx'].'" class="design js_input_idx number_style" '.$readonlyAttr.' placeholder="" style="width:45px;margin-right:0;">
							</div>';

						// {{{회원등급추가}}}


						// -- 출력
						echo '<tr>';
						echo '	<td><label class="design"><input type="checkbox" class="js_ck mgs-uid '.$disabledDeleteClass.'" name="arrUid[]" value="'.$v['mgs_uid'].'" '.$disabledDeleteClass.'></label></td>';
						echo '	<td>'.$printIdx.'</td>';
						echo '	<td>'.$printName.'</td>';
						echo '	<td>'.$printRcnt.'</td>';
						echo '	<td>'.$printCondition.'</td>';
						echo '	<td>'.$printBoon.'</td>';
						echo '	<td>'.$printRdate.'</td>';
						echo '	<td>'.$printBtn.'</td>';
						echo '</tr>';
					}
				?>
				</tbody>
			</table>
		</form>
		</div>


		<!-- ●●● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
		<div class="paginate">
			<?php echo pagelisting($listpg, $Page, $listmaxcount," ?&${_PVS}&listpg=" , "Y")?>
		</div>


		<?php }else {  ?>
						<!-- 내용없을경우 -->
						<div class="common_none"><div class="no_icon"></div><div class="gtxt">등록된 등급이 없습니다.</div></div>

		<?php } ?>


<script>
$(document).on('click','.on-get-delete',function(){
	if( confirm("해당 등급을 삭제 하시겠습니까?") == false){ return false; }
	var chk = $(this).hasClass('disabled');
	var _uid = $(this).attr('data-uid');
	if( _uid == undefined || _uid == ''){ alert("등급 정보가 없습니다."); return false; }
	if( chk == true){ alert("기본등급은 삭제가 불가능합니다."); return false; }

	$('form#frm [name="_mode"]').val('delete');
	$('form#frm [name="_uid"]').val(_uid);
	$('form#frm').submit();

	$('form#frm [name="_mode"]').val('');
	$('form#frm [name="_uid"]').val('');
	return true;
})

$(document).on('click','.on-select-delete',function(){

	if( confirm("선택하신 등급을 삭제 하시겠습니까?") == false){ return false; }
	var chkLen = $('.js_ck:checked').length *1;
	if( chkLen < 1){ alert("한개 이상 선택해 주세요."); return false; }

	$('form#frm [name="_mode"]').val('selectDelete');
	$('form#frm').submit();
	$('form#frm [name="_mode"]').val('');
	return true;
})


// {{{회원등급추가}}}
$(document).on('focusout','.js_input_idx',function(){
	var dval = $(this).attr('data-value')*1

	if( $(this).val()*1 == 0){
		$(this).val( (dval == 0 ? 1 : dval) )
	}

	$(this).attr('data-value', $(this).val() );
})

$(document).on('click','.on-select-idx',function(){

	if( confirm("입력된 등급순서를 일괄적용 하시겠습니까?") == false){ return false; }

	var rstChkCnt = 0;

	$('.js_input_idx').each(function(i,v){
		if( $(v).val()*1 == 0 ){ rstChkCnt ++; }
	})

	if( rstChkCnt > 0) { alert("등급순서는 1이상 입력해 주세요."); return false; }


	$('form#frm [name="_mode"]').val('execIdx');
	$('form#frm').submit();
	$('form#frm [name="_mode"]').val('');
	return true;
})
// {{{회원등급추가}}}

</script>
<?php
	include_once('wrap.footer.php');
?>
