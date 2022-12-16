<?php // -- 자주쓰는옵션 리스트 페이지
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
?>

	<!-- 단락타이틀 -->
	<div class="group_title">
		<strong>자주쓰는 옵션 관리</strong><!-- 메뉴얼로 링크 --><?=openMenualLink('자주쓰는 옵션 관리')?>
		<!-- 해당페이지의 등록/업로드 버튼 있을 경우 -->
		<div class="btn_box"><a href="_product.common_option_set.form.php<?php echo URI_Rebuild('?', array('_mode'=>'add', '_PVSC'=>$_PVSC)); ?>" class="c_btn h46 red" accesskey="a">자주쓰는 옵션 등록</a></div>
	</div>


	<?php
		// -- 자주쓰는 옵션 공통처리
		include_once dirname(__FILE__)."/_product.common_option_set.in_search.php";
	?>


	<!-- ● 데이터 리스트 -->
	<div class="data_list">
	<form name="frm" id="frm" method="post" action="_product.common_option_set.pro.php">
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
		<input type="hidden" name="searchQue" value="<?=enc('e',$s_query)?>">
		<input type="hidden" name="searchCnt" value="<?=$TotalCount?>">
		<input type="hidden" name="ctrlMode" value="">
		<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
		<input type=hidden name="chkVar" value=""> <?php // 자주쓰는옵션에서 개별 삭제일 시 저장될 고유번호 ?>


			<!-- ●리스트 컨트롤영역 -->
			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="ctrlSelect('copy'); return false;" class="c_btn h27">선택 복사</a>
					<a href="#none" onclick="ctrlSelect('delete'); return false;" class="c_btn h27">선택 삭제</a>
				</div>
				<div class="right_box">
					<select class="h27" onchange="location.href=this.value;">
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'cos_uid', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'cos_uid' && $so == 'asc'?' selected':null); ?>> 등록순 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'cos_uid', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'cos_uid' && $so == 'desc'?' selected':null); ?>>등록순 ▼</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'cos_depth', 'so'=>'asc'), array('listpg')); ?>"<?php echo ($st == 'cos_depth' && $so == 'asc'?' selected':null); ?>> 옵션차수순 ▲</option>
						<option value="<?php echo $_SERVER['PHP_SELF'].URI_Rebuild('?'.$_PVS, array('st'=>'cos_depth', 'so'=>'desc'), array('listpg')); ?>"<?php echo ($st == 'cos_depth' && $so == 'desc'?' selected':null); ?>>옵션차수순 ▼</option>

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
				<col width="35"/><col width="65"/><col width="*"/><col width="100"/><col width="80"/><col width="100"/><col width="100"/>
			</colgroup>
			<thead>
				<tr>
					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK"></label></th>
					<th scope="col">번호</th>
					<th scope="col">공통옵션관리명</th>
					<th scope="col">옵션노출방식</th>
					<th scope="col">옵션차수</th>
					<th scope="col">등록일</th>
					<th scope="col">관리 </th>
				</tr>
			</thead>
			<tbody>
			<?php
				// 옵션별 유형 추가 표기
				$arrOptionType = array('normal'=>'일반','color'=>'컬러','size'=>'사이즈');
				foreach($res as $k=>$v) {
					$_num = $TotalCount - $count - $k ;
					$_num = number_format($_num);

					$printName =$v['cos_name'];
					// -- 옵션노출방식
					if($v['cos_type'] == 'option'){
						$printOptionType = '일반옵션';
						// 옵션별 유형 추가 표기
						$arrName = array();
						if( $v['cos_depth'] > 0){ $arrName[] = "1차:".$arrOptionType[$v['cos_option1_type']]; }
						if( $v['cos_depth'] > 1){ $arrName[] = "2차:".$arrOptionType[$v['cos_option2_type']]; }
						if( $v['cos_depth'] > 2) { $arrName[] ="3차:".$arrOptionType[$v['cos_option3_type']];}
						if( count($arrName) > 0 ){ $printName .= " (".implode(" / ",$arrName).")"; }

					}else if($v['cos_type'] == 'addoption'){
						$printOptionType = '추가옵션';
					}

					$printBtn = '
						<div class="lineup-center">
							<a href="_product.common_option_set.form.php?_mode=modify&_uid='.$v['cos_uid'].'" class="c_btn h22">수정</a>
							<a href="#none" onclick="return false;" class="c_btn h22 gray get-delete"  data-uid="'.$v['cos_uid'].'" data-apply = "true">삭제</a>
						</div>
					'; // 관리버튼


					$printDepth = $v['cos_depth']."차옵션"; // -- 옵션차수
					$printRdate = date("Y-m-d",strtotime($v['cos_rdate'])) ;


					// -- 출력
					echo '<tr>';
					echo '	<td><label class="design"><input type="checkbox" class="js_ck cos-uid" name="arrUid[]" value="'.$v['cos_uid'].'"></label></td>';
					echo '	<td>'.$_num.'</td>';
					echo '	<td>'.$printName.'</td>';
					echo '	<td>'.$printOptionType.'</td>';
					echo '	<td>'.$printDepth.'</td>';
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

	function ctrlSelect(ctrlMode)
	{
		var chkLen = $('.cos-uid:checked').length;
		if( chkLen < 1){ alert("한개 이상 선택해 주세요."); return false; }
		if(ctrlMode == 'copy'){ // 선택복사
			if(confirm("선택하신 공통옵션을 복사하시겠습니까?\n생성된 공통옵션은 공통옵션관리명 앞에 [복사]가 붙습니다.") == false){ return false; }
		}else if(ctrlMode == 'delete'){ // 선택삭제
			if(confirm("선택하신 공통옵션을 삭제하시겠습니까?") == false){ return false; }
		}

		$('#frm [name="_mode"]').val('selectCtrl');
		$('#frm [name="ctrlMode"]').val(ctrlMode);
		$('#frm').submit();
	}


	$(document).on('click','.get-delete',function(){
		if( confirm("해당 공통옵션을 삭제하시겠습니까?") == false){ return false; }
		var _uid = $(this).attr('data-uid');
		if( _uid == '' || _uid == undefined){	alert('삭제할 데이터가 존재 하지 않습니다.');}

		$('[name="chkVar"]').val(_uid);
		$('#frm [name="_mode"]').val('delete');
		$('#frm').submit();
	})
</script>

<?php

	include_once('wrap.footer.php');

?>