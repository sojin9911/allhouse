<?php # 자주쓰는 옵션 팝업 폼
	$app_mode = 'popup';
	include_once('inc.header.php');

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



<?// 팝업을 위한 css 추가 --- window.open시 1120px로 띄움 ?>
<style>
	body {min-width:1100px;}
	.wrap {padding-bottom:0px;}
</style>



<div class="popup" style="border:0;">

	<!-- 단락타이틀 -->
	<div class="group_title">
		<strong>자주쓰는 옵션</strong><!-- 메뉴얼로 링크 --><?=openMenualLink('자주쓰는옵션')?>
	</div>

	<?php 

		// -- 자주쓰는 옵션 공통처리 
		$arr_param = array('pass_mode'=>$pass_mode,'pass_code'=>$pass_code);
		include_once dirname(__FILE__)."/_product.common_option_set.in_search.php";
	?>

	<!-- ● 데이터 리스트 -->
	<div class="data_list">
	<form name="frm" id="frm" method="post" action="_product_common_option.pro.php">
		<input type="hidden" name="_mode" value="">
		<input type="hidden" name="pass_mode" value="<?=$pass_mode?>">
		<input type="hidden" name="pass_code" value="<?=$pass_code?>">
		<input type="hidden" name="orderby" value="<?php echo "order by {$st} {$so}"; ?>">
		<input type="hidden" name="searchQue" value="<?=enc('e',$s_query)?>">
		<input type="hidden" name="searchCnt" value="<?=$TotalCount?>">
		<input type="hidden" name="ctrlMode" value="">
		<input type=hidden name="_PVSC" value="<?=$_PVSC?>">
		<input type=hidden name="chkVar" value=""> <?php // 자주쓰는옵션에서 개별 삭제일 시 저장될 고유번호 ?>

		<div class="data-box" data-pass-mode="<?php echo $pass_mode; ?>" data-pass-code="<?php echo $pass_code; ?>"></div> <?php // -- 다른 변수와 겹칠 수 있으니 별도 레이아웃으로 처리 ?>


			<!-- ●리스트 컨트롤영역 -->
			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="selectApply(); return false;" class="c_btn h27">선택 적용</a>
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
					<th scope="col">공통옵션관리명 (옵션별유형)</th>
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
							<a href="#none"  class="c_btn h22 get-apply" data-option-type="'.$v['cos_type'].'" data-option-depth="'.$v['cos_depth'].'" data-uid="'.$v['cos_uid'].'" data-option1-type="'.$v['cos_option1_type'].'" data-option2-type = "'.$v['cos_option2_type'].'" data-option3-type ="'.$v['cos_option3_type'].'">적용</a>
						</div>
					'; // 관리버튼				

					
					$printDepth = $v['cos_depth']."차옵션"; // -- 옵션차수 			
					$printRdate = date("Y-m-d",strtotime($v['cos_rdate'])) ;

					// -- 출력
					echo '<tr>';
					echo '	<td><label class="design"><input type="checkbox" class="js_ck cos-uid" name="arrUid[]" value="'.$v['cos_uid'].'" data-option-type="'.$v['cos_type'].'" data-option-depth="'.$v['cos_depth'].'" data-option1-type="'.$v['cos_option1_type'].'" data-option2-type = "'.$v['cos_option2_type'].'" data-option3-type ="'.$v['cos_option3_type'].'" ></label></td>';
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




	<!-- 가운데정렬버튼 -->
	<div class="c_btnbox">
		<ul>
			<li><a href="#none" onclick="window.close();" class="c_btn h34 black line normal">닫기</a></li>
		</ul>
	</div>

</div>

<script>
	var app_option1_type = app_option2_type = app_option3_type = '';
	function selectApply()
	{
		var chkLen = $('.cos-uid:checked').length;
		if( chkLen < 1){ alert("한개 이상 선택해 주세요."); return false; }
		var passMode = $('.data-box').attr('data-pass-mode').replace("depth","")*1;
		// -- 옵션의 차수가 맞는지 체크
		var chkCnt = 0; 
		var chkTypeCnt = 0;
		$('.cos-uid:checked').each(function(i,v){
			var optionType = $(v).attr('data-option-type'); // 옵션타입 (일반,추가)
			var optionDepth = $(v).attr('data-option-depth')*1; // 옵션차수
	
			// -- 옵션 유형별 정의
			var option1Type = $(v).attr('data-option1-type'); // 1차옵션 유형
			var option2Type = $(v).attr('data-option2-type'); // 2차옵션 유형
			var option3Type = $(v).attr('data-option3-type'); // 3차옵션 유형			

			// --  일반옵션일 경우에만  상품의 옵션과 맞는지 체크
			if( optionType == 'option'){
				if( passMode != optionDepth){ chkCnt ++;  }

				// --  일반옵션일 경우에만  상품의 옵션과 맞는지 체크
				if( passMode != optionDepth){  alert("일반옵션은 상품에 설정된 옵션차수만 적용가능합니다."); return false;  }

				// 옵션 유형애 따른 처리 추가
				if( optionDepth > 0){
					if( app_option1_type != option1Type){ chkTypeCnt++; }
				}
				if( optionDepth > 1){
					if( app_option2_type != option2Type){ chkTypeCnt++; }
				}
				if( optionDepth > 2){
					if( app_option3_type != option3Type){ chkTypeCnt++; }
				}
				

			}
		});

		if( chkCnt > 0){ alert("일반옵션은 상품에 설정된 옵션차수만 적용가능합니다."); return false; }
		if( chkTypeCnt > 0){ alert("일반옵션은 상품에 설정된 옵션유형이 같을 경우에만 적용가능합니다."); return false;  } 




		$('#frm [name="_mode"]').val('selectApply');
		$('#frm').submit();		
	}

	$(document).on('click','.get-apply',function(){
		if( confirm("선택하신 자주쓰는 옵션을 적용하시겠습니까??") == false){ return false; }
		var _uid = $(this).attr('data-uid');
		var passMode = $('.data-box').attr('data-pass-mode').replace("depth","")*1;
		var optionType = $(this).attr('data-option-type'); // 옵션타입 (일반,추가)
		var optionDepth = $(this).attr('data-option-depth')*1; // 옵션차수		

		// -- 옵션 유형별 정의
		var option1Type = $(this).attr('data-option1-type'); // 1차옵션 유형
		var option2Type = $(this).attr('data-option2-type'); // 2차옵션 유형
		var option3Type = $(this).attr('data-option3-type'); // 3차옵션 유형

		// --  일반옵션일 경우에만  상품의 옵션과 맞는지 체크
		if( optionType == 'option'){
			if( passMode != optionDepth){  alert("일반옵션은 상품에 설정된 옵션차수만 적용가능합니다."); return false;  }

			// 옵션 유형애 따른 처리 추가
			var chkTypeCnt = 0;

			if( optionDepth > 0){
				if( app_option1_type != option1Type){ chkTypeCnt++; }
			}
			if( optionDepth > 1){
				if( app_option2_type != option2Type){ chkTypeCnt++; }
			}
			if( optionDepth > 2){
				if( app_option3_type != option3Type){ chkTypeCnt++; }
			}

			if( chkTypeCnt > 0){ alert("일반옵션은 상품에 설정된 옵션유형이 같을 경우에만 적용가능합니다."); return false;  } 
		}		
		if( _uid == '' || _uid == undefined){	alert('적용할 공통옵션이 존재하지 않습니다.');}

		$('[name="chkVar"]').val(_uid);
		$('#frm [name="_mode"]').val('apply');
		$('#frm').submit();
	});

	$(document).ready(function(){
		
		<?php 
			// 차수에 따른 옵션 유형 - 부모창에서 추출
			if(IN_ARRAY($pass_mode , array("1depth" , "2depth" , "3depth"))) {
				echo "app_option1_type = $(\"input[name='p_option1_type']:checked\", opener.document ).val();";
			}
			if(IN_ARRAY($pass_mode , array("2depth" , "3depth"))) {
				echo "app_option2_type = $(\"input[name='p_option2_type']:checked\", opener.document ).val();";
			}
			if(IN_ARRAY($pass_mode , array("3depth"))) {
				echo "app_option3_type = $(\"input[name='p_option3_type']:checked\", opener.document ).val();";
			}	
		?>	
	});
</script>


<?PHP
		include_once("inc.footer.php");
?>