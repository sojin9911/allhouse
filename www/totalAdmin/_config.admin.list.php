<?php  // -- LCY :: 2017-09-20 -- 운영자관리 리스트 페이지
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

	// -- 검색체크
	$s_query = " where a_type != 'master' ";
	$pass_input_type = $pass_input_type == '' ? 'all':$pass_input_type;
	if( $searchMode == "true" ) {

		if(  $pass_input != ''){
			if( $pass_input_type =="id" ) { $s_query .= " and a_id like '%".$pass_input."%' "; } // 아이디
			if( $pass_input_type == "name" ) { $s_query .= " and a_name like '%".$pass_input."%' "; } // 성명
			if( $pass_input_type == "email") { $s_query .= " and a_email like '%".$pass_input."%' "; } // 이메일
			if( $pass_input_type == "htel" ) { $s_query .= " and REPLACE(a_htel, '-','') like '%".rm_str($pass_input)."%' "; } // 핸드폰

			// -- 전체검색
			if( $pass_input_type == "all"){
				$s_query .= " and ( a_id like '%".$pass_input."%' or a_name like '%".$pass_input."%' or  a_email like '%".$pass_input."%' or REPLACE(a_htel, '-','') like '%".($pass_input)."%' ) ";
			}

		}


		if( $pass_use !="") { $s_query .= " and a_use = '".$pass_use."' "; } // 승인여부
	}

	$listmaxcount = 20 ;
	if( !$listpg ) {$listpg = 1 ;}
	$count = $listpg * $listmaxcount - $listmaxcount;

	$res = _MQ(" select count(*) as cnt from smart_admin ".$s_query);
	$TotalCount = $res['cnt'];
	$Page = ceil($TotalCount / $listmaxcount);

	$res = _MQ_assoc(" select * from smart_admin ".$s_query." ORDER BY a_rdate desc limit ".$count." , ".$listmaxcount);
?>

		<!-- 단락타이틀 -->
		<div class="group_title">
			<strong>운영자 검색</strong>
			<!-- 해당페이지의 등록/업로드 버튼 있을 경우 -->
			<div class="btn_box"><a href="_config.admin.form.php?_mode=add&_PVSC=<?=$_PVSC?>" class="c_btn h46 red">운영자등록</a></div>
		</div>


		<!-- ●폼 영역 (검색/폼 공통으로 사용) -->
		<div class="data_form if_search">
			<form name="searchfrm" id="searchfrm" method=get action='<?=$_SERVER["PHP_SELF"]?>'>
			<input type=hidden name="searchMode" value="true">
			<table class="table_form">
				<colgroup>
					<col width="180"/><col width="*"/><col width="180"/><col width="*"/>
				</colgroup>
				<tbody>
					<tr>
						<th>검색어</th>
						<td>
							<?php echo _InputSelect( "pass_input_type" , array('all','id','name','email','htel') , $pass_input_type , "" , array('전체검색','아이디','성명','이메일','핸드폰'), '' ); ?>
							<input type="text" name="pass_input" class="design" style="width:150px" value="<?=$pass_input?>" />
						</td>
						<th>승인여부</th>
						<td>
							<?php echo _InputRadio( "pass_use" , array('', 'Y','N'), $pass_use , "" , array('전체', '승인','미승인') ); ?>
						</td>
					</tr>
				</tbody>
			</table>

			<!-- 가운데정렬버튼 -->
			<div class="c_btnbox">
				<ul>
					<li><a href="#none" onclick="submitAdminSearch(); return false;"  class="c_btn h34 black">검색</a></li>
					<?if ($searchMode == 'true') { // 검색했을 시 에만?>
					<li><a href="_config.admin.list.php" class="c_btn h34 black line normal">전체목록</a></li>
					<?php } ?>
				</ul>
			</div>
			</form>
		</div>



		<!-- ● 데이터 리스트 -->
		<div class="data_list">


			<!-- ●리스트 컨트롤영역 -->
			<div class="list_ctrl">
				<div class="left_box">
					<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
					<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
					<a href="#none" onclick="selectDeleteAdmin(); return false;" class="c_btn h27 gray">선택삭제</a>
				</div>
			</div>

			<table class="table_list">
				<colgroup>
					<col width="65"/><col width="*"/><col width="150"/><col width="150"/><col width="150"/><col width="*"/><col width="90"/><col width="100"/>
				</colgroup>
				<thead>
					<tr>
						<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK"></label></th>
						<th scope="col">번호</th>
						<th scope="col">아이디</th>
						<th scope="col">성명</th>
						<th scope="col">핸드폰</th>
						<th scope="col">이메일</th>
						<th scope="col">승인상태</th>
						<th scope="col">등록일</th>
						<th scope="col">관리</th>
					</tr>
				</thead>
				<tbody>

				<?php
					foreach($res as $k=>$v){
						$_num = $TotalCount - $count - $k; // NO 표시
						$_num = number_format($_num);
						$printHtel = rm_str($v['a_htel']) != '' ? tel_format($v['a_htel']) : '-'; // 핸드폰
						$printEmail = $v['a_email']; // 핸드폰
						$printAdminUse = '<div class="lineup-vertical">'.($v['a_use'] == 'Y' ? '<span class="c_tag blue line h18 t3">승인</span>':'<span class="c_tag gray h18 t3">미승인</span>').'</div>'; // 승인상태
						$printAdminRdate = rm_str($v['a_rdate']) > 0 ? date("Y-m-d",strtotime($v['a_rdate'])) : '-'; // 등록일
						$printBtn = '
							<div class="lineup-center">
								<a href="_config.admin.form.php?_uid='.$v['a_uid'].'&_mode=modify&_PVSC='.$_PVSC.'" class="c_btn h22">수정</a>
								'.($v['a_type'] == 'admin' ? '<a href="#none" onclick="return false;" class="c_btn h22 gray on-admin-delete" data-admin-id="'.$v['a_id'].'" data-admin-uid="'.$v['a_uid'].'" >삭제</a>' : '').'
							</div>
						'; // 관리버튼

						// -- 출력
						echo '<tr>';
						echo '	<td><label class="design"><input type="checkbox" name="adminUid[]" value="'.$v['a_uid'].'" class="js_ck"></label></td>';
						echo '	<td>'.$_num.'</td>';
						echo '	<td>'.$v['a_id'].'</td>';
						echo '	<td>'.$v['a_name'].'</td>';
						echo '	<td>'.$printHtel.'</td>';
						echo '	<td>'.$printEmail.'</td>';
						echo '	<td>'.$printAdminUse.'</td>';
						echo '	<td>'.$printAdminRdate.'</td>';
						echo '	<td>'.$printBtn.'</td>';
						echo '</tr>';

					}
				?>
				</tbody>

			</table>
		<?php if( count($res) < 1){ ?>
			<!-- 내용없을경우 -->
			<div class="common_none"><div class="no_icon"></div><div class="gtxt"><?=$searchMode == 'true' ? '검색된 운영자가 없습니다.':'등록된 운영자가 없습니다.';?></div></div>
		<?php } ?>
		</div>


		<!-- ●●● 페이지네이트(공통사용) : 디자인을 위해 nextprev버튼 4개를 모두 노출시키고 클릭가능 여부로 구분 -->
		<div class="paginate">
			<?php echo pagelisting($listpg, $Page, $listmaxcount," ?&${_PVS}&listpg=" , "Y")?>
		</div>



<script>
	$(document).on('click','.on-admin-delete',function(){
		var adminId = $(this).attr('data-admin-id');
		var adminUid = $(this).attr('data-admin-uid');
		if(confirm("운영자 아이디 `"+adminId+"` 를 삭제하시겠습니까?") == false){ return false; }

		// -- ajax post 처리 ==> get 처리는 보안상 취약
		var url = '_config.admin.ajax.php';
	  $.ajax({
	      url: url, cache: false,dataType : 'json', type: "POST", data: {ajaxMode : 'DeleteAdmin', adminId : adminId, adminUid :adminUid}, success: function(data){
	      	if( data != undefined){
	      		if(data.rst == 'success'){
	      			alert(data.msg);
	      			window.location.reload();
	      			return false;
	      		}else{
	      			alert(data.msg);
	      			return false;
	      		}
	      	}else{
	      		alert('잘못된 접근입니다.');
	      		window.location.reload();
	      		return false;
	      	}
	      },error:function(request,status,error){ console.log(request.responseText);}
	  });
	});

	// -- 선택 운영자 삭제
	function selectDeleteAdmin()
	{
		if( confirm('선택된 운영자를 삭제하시겠습니까?') == false){ return false;  }

		var chkLen = $('.js_ck').length;
		if( chkLen < 1){ alert('삭하실 운영자를 선택해 주세요.'); return false; }

		var data = $('.js_ck').serialize()+'&ajaxMode=selectDeleteAdmin';

		// -- ajax post 처리 ==> get 처리는 보안상 취약
		var url = '_config.admin.ajax.php';
	  $.ajax({
	      url: url, cache: false,dataType : 'json', type: "POST", data: data, success: function(data){
	      	if( data != undefined){
	      		if(data.rst == 'success'){
	      			alert(data.msg);
	      			window.location.reload();
	      			return false;
	      		}else{
	      			alert(data.msg);
	      			return false;
	      		}
	      	}else{
	      		alert('잘못된 접근입니다.');
	      		window.location.reload();
	      		return false;
	      	}
	      },error:function(request,status,error){ console.log(request.responseText);}
	  });

	}


	// -- 검색체크
	function submitAdminSearch()
	{
		$('form#searchfrm').submit();;
	}

</script>


<?php include_once('wrap.footer.php'); ?>