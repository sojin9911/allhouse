<?php

if( !( $siteInfo['s_deny_use'] == "Y" && $siteInfo['s_deny_tel'] ) )  { 
	error_loc_msg("_addons.php?pass_menu=080deny/_receipt.form","080수신거부설정을 확인해주시기 바랍니다.");
}


// 회원 관리 --- 검색폼 불러오기
//			반드시 - s_query가 적용되어야 함.
$s_query = " from smart_member_080_deny where 1 ";


// -- 검색시작 -- {{{
if( $searchMode == 'true') { 
	if( $pass_hp !="" ) { $s_query .= " and md_hp like '%${pass_hp}%' "; }
	if( $pass_status !="" ) { $s_query .= " and md_status = '${pass_status}' "; }
}
// -- 검색종료 -- }}} 


//	==> s_query 리턴됨.

if(!$listmaxcount) $listmaxcount = 30;
if(!$listpg) $listpg = 1;
if(!$st) $st = 'md_uid';
if(!$so) $so = 'desc';
$count = $listpg * $listmaxcount - $listmaxcount;	// 상상너머 하이센스


$res = _MQ(" select count(*) as cnt  $s_query ");
$TotalCount = $res['cnt'];
$Page = ceil($TotalCount / $listmaxcount);

$res = _MQ_assoc(" select *  $s_query $s_orderby limit $count , $listmaxcount ");

$arr_param = array('pass_menu'=>$pass_menu);
?>

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
					<th>수신거부 요청번호</th>
					<td>		
						<input type="text" name="pass_hp" class="design" style="width:190px" value="<?=$pass_hp?>" />
					</td>
					<th>적용상태</th>
					<td>
						<?php echo _InputSelect( 'pass_status' , array_keys($arrAddonsService['080denyStatus']), ($pass_status) , '' , array_values($arrAddonsService['080denyStatus']) , ''); ?>
					</td>
				</tr>		
			<tr>
				<td colspan="4">
					<div class="tip_box">
						<?=_DescStr("<em>".$arrAddonsService['080denyStatus']['OK']."</em> : 수신거부요청번호가 검색되어 해당 회원을 <strong>수신거부로 처리</strong>한 상태입니다.")?>
						<?=_DescStr("<em>".$arrAddonsService['080denyStatus']['MULTI']."</em> :  수신거부요청번호가 <strong>다수 검색</strong>되어 해당 회원을 수신거부로 처리하지 못한 상태입니다. 운영자께서 확인한 후 수동으로 처리해주시기 바랍니다.")?>
						<?=_DescStr("<em>".$arrAddonsService['080denyStatus']['NO']." </em> : 수신거부요청번호가 검색되지 않아 수신거부를 처리하지 못한 상태입니다.")?>
						<?=_DescStr("<em>".$arrAddonsService['080denyStatus']['FALSE']."</em> : 환경설정 &gt; 080수신거부설정이 정상적으로 등록되지 않은 상태입니다. 해당 서비스를 이용하기 위해서는 설정을 등록해주시기 바랍니다.")?>
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
	<form name="frm080deny" id="frm080deny" method="post" action="/addons/080deny/_member_080deny.pro.php">
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
		<input type="hidden" name="searchQue" value="<?=enc('e',$s_query)?>">
		<input type="hidden" name="searchCnt" value="<?=$TotalCount?>">
		<input type="hidden" name="ctrlMode" value="">
		<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
		<input type=hidden name="chkVar" value=""> <?php //  개별 삭제일 시 저장될 아이디 ?>


			<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="ctrlDelete('select'); return false;" class="c_btn h27">선택삭제</a>
				<a href="#none" onclick="ctrlDelete('search'); return false;" class="c_btn h27">검색삭제(<?=$TotalCount?>)</a>
			</div>
		</div>

		<table class="table_list">
			<colgroup>
				<col width="35"/><col width="65"/><col width="*"/><col width="150"/><col width="150"/><col width="150"/><col width="150"/><col width="70"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK"></label></th>
					<th scope="col">NO</th>
					<th scope="col">수신거부번호</th>
					<th scope="col">수신거부요청번호</th>
					<th scope="col">적용상태</th>
					<th scope="col">거부요청시간</th>
					<th scope="col">기록시간</th>
					<th scope="col">관리</th>					
				</tr>
			</thead> 
			<tbody>
			<?php
				foreach($res as $k=>$v) { 
					$_num = $TotalCount - $count - $k ;
					$printBtn = '
						<div class="lineup-center">
							<a href="#none" onclick="return false;" class="c_btn h22 gray get-delete"  data-uid="'.$v['md_uid'].'" data-apply = "true">삭제</a>
						</div>
					'; // 관리버튼							

					// -- 출력
					echo '<tr>';
					echo "	<td><label class='design'><input type='checkbox' class='js_ck md-uid' name='arrUid[]' value='".$v['md_uid']."'></label></td>";
					echo "	<td>" . $_num ."</td>";
					echo "	<td>".$v['md_refusal_num']."</td>";
					echo "	<td>".$v['md_hp']."</td>";
					echo "	<td>".$arrAddonsService['080denyStatus'][$v['md_status']]."</td>";
					echo "	<td>".( $v['md_refusal_time'] > 0 ? date("Y-m-d H:i:s" , $v['md_refusal_time']) : '-')."</td>";
					echo "	<td>".$v['md_rdate']."</td>";
					echo "	<td>".$printBtn."</td>";
					echo "</tr>";
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

	// -- 개별삭제 적용
	$(document).on('click','.get-delete',function(){
		var chkVar  = $(this).attr('data-uid');
		ctrlDelete('single', chkVar);
	});

	// -- 선택회원 완전삭제 chkVar == 
	function ctrlDelete(ctrlMode, chkVar) {

		// -- 선택/검색 처리 시에는 chkVar 가 없기때문에 초기화 처리
		if(chkVar == undefined) { chkVar = ''; }

		if(ctrlMode == 'select') { // 선택
			var chkLen = $('.js_ck:checked').length; // 선택된 것의 길이
			if( chkLen < 1){ alert("한개이상 선택해 주세요."); return false; }
		}
		else if( ctrlMode == 'search'){
			var chkCnt = $('form#frm080deny [name="searchCnt"]').val()*1;
			if( chkCnt < 1) { alert("검색된 기록이 없습니다."); return false; }			
		}
		else if( ctrlMode == 'single') { // 관리에서 개별삭제 처리 시
			if(chkVar == '') { alert("삭제 처리가 불가능합니다."); return false; }
			$('form#frm080deny input[name="chkVar"]').val(chkVar);
		}

		if(confirm("삭제 처리를 하시겠습니까?") == false){ return false; }

		$('form#frm080deny [name="_mode"]').val('delete');
		$('form#frm080deny [name="ctrlMode"]').val(ctrlMode);

		frm080deny.submit();
	}
</script>