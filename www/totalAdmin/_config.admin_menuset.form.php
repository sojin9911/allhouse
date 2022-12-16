<?php // -- LCY :: 2017-09-20 -- 운영자별 메뉴관리

	include_once('wrap.header.php');

	$resAdmin = _MQ_assoc("select *from smart_admin where a_type != 'master' order by a_uid desc ");
	if(count($resAdmin) < 1){ error_loc_msg('_config.admin.list.php', '등록된 운영자가 없습니다.\n운영자를 등록 후 이용할 수 있습니다.'); }

	$arrAdminList = array();
	foreach( $resAdmin as $k=>$v){
		$arrAdminList[$v['a_uid']] = $v['a_name'].'('.$v['a_id'].')';
	}

	$resAdminMenuSet['depth1'] = _MQ_assoc("select *from smart_admin_menu where am_view = 'Y' and am_depth='1' order by am_depth asc, am_idx asc");
?>
	<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
	<div class="data_form">
		<table class="table_form">
			<colgroup>
				<col width="180"/><col width="*"/>
			</colgroup>
			<tbody>
				<tr>
					<th>운영자 선택</th>
					<td>
					<?php echo _InputSelect( "selectAdmin" , array_keys($arrAdminList) , '' , " class='select-admin' " , array_values($arrAdminList), '' ); ?>
					</td>
				</tr>

				<tr>
					<th>Admin 메뉴</th>
					<td>
						<span class="fr_tx">1차 메뉴</span>
						<select name="adminMenuDepth1" class="select-admin-menu-depth" data-depth="1">
							<option value="">-선택-</option>
							<option value="all">전체</option>
							<?php foreach($resAdminMenuSet['depth1'] as $k=>$v) { ?>
							<option value="<?=$v['am_uid']?>"><?=$v['am_name']?></option>
							<?php } ?>
						</select>
						<?php echo _DescStr('1차 메뉴를 선택하시면 해당카테고리 및 하위 카테고리에 대한 설정이 가능합니다.', ''); ?>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="tip_box">
							<div class="c_tip">상위메뉴가 숨김으로 설정될 시 하위 메뉴도 자동 숨김처리 됩니다. </div>
							<div class="c_tip">메뉴를 선택하여 선택노출, 선택숨김 버튼을 클릭하시면 선택된 메뉴를 노출/숨김 처리할 수 있습니다.</div>
							<div class="c_tip black">메뉴가 노출되기 위해선 반드시 3차 메뉴가 노출로 설정되어야 합니다.</div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>



	</div>


	<div class="admin-menu-list-wrap">

	<div class="c_btnbox"></div>

	<!-- ● 데이터 리스트 -->
	<div class="data_list">

		<!-- ●리스트 컨트롤영역 -->
		<div class="list_ctrl">
			<div class="left_box">
				<a href="#none" onclick="selectAll('Y'); return false;" class="c_btn h27">전체선택</a>
				<a href="#none" onclick="selectAll('N'); return false;" class="c_btn h27">선택해제</a>
				<a href="#none" onclick="selectAdminMenuSet('Y'); return false;" class="c_btn h27 gray">선택노출</a>
				<a href="#none" onclick="selectAdminMenuSet('N'); return false;" class="c_btn h27 gray">선택숨김</a>
			</div>
		</div>

		<form name="formAdminMenuSet" method="post">
		<input type="hidden" name="selectAdminUid" value=""> <?php // -- 선택된 관리자 고유번호 --  ?>
		<table class="table_list">
			<colgroup>
				<col width="40"/><col width="80"/><col width="*"/><col width="280"/>
			</colgroup>
			<thead>
				<tr>

					<th scope="col"><label class="design"><input type="checkbox" class="js_AllCK"></label></th>
					<th scope="col">노출여부</th>
					<th scope="col">Admin 메뉴명</th>
					<th scope="col">관리</th>
				</tr>
			</thead>
			<tbody class="print-admin-menu-list">
			</tbody>
		</table>
		</form>
		<!-- 내용없을경우 -->
		<div class="common_none print-admin-menu-none event_admin_menu_none"><div class="no_icon"></div><div class="gtxt">운영자를 먼저 선택해주세요.</div></div>
	</div>



	<div class="c_btnbox admin-menu-btn-box" style="display: none;">
		<ul>
			<li><a href="#none" onclick="submitAdminMenuSet(); return false;" class="c_btn h46 red">적용</a></li>
		</ul>
	</div>

	</div>

<?php
//SetCookie("testDev", 'true' , 0 , "/" , "." . str_replace("www." , "" , $system['host']));
?>

<script>

	// -- 운영자 선택 시
	$(document).on('change','.select-admin',selectAdmin);

	// -- Admin 메뉴 선택 시
	$(document).on('change','.select-admin-menu-depth',AdminMenuView);


	// --  메뉴별 노출/숨김 셀렉트
	$(document).on('change','.select-admin-view-single',function(e){

		var sval = $(this).val(); // 선택된 값
		var adminMenuUid = $(this).attr('data-uid'); // 해당 메뉴의 고유번호
		var depth = $(this).attr('data-depth')*1; // 해당 메뉴의 차수

		if( sval == '' || sval == undefined){    return false; }
		if( adminMenuUid == '' || adminMenuUid == undefined){ return false; }

		// -- ajax
		var url = '_config.admin_menuset.ajax.php';
	  $.ajax({
	      url: url, cache: false,dataType : 'json', type: "post", data: {ajaxMode : 'adminMenuSelectSingle' ,  sval : sval , adminMenuUid : adminMenuUid, depth : depth}, success: function(data){
	     		if( data != undefined){
	     			if(data.rst == 'success'){

							if( data.data == undefined){ return false; }
							for(var i=0; i < data['data'].length; i++){
								var chkUid = data['data'][i]['am_uid'];
								if( chkUid == '' || chkUid == undefined){ continue; }
								if( sval == 'N'){ // 숨김처리 일 시
									$('.select-admin-view-single[data-uid="'+chkUid+'"]').val('N');
									$('.select-admin-view-single[data-uid="'+chkUid+'"]').attr('disabled',true); // 하위메뉴 disabled
									//$('.btn-admin-view-single[data-uid="'+chkUid+'"]').attr('disabled',true); // 하위메뉴 disabled
								}else{
									var nextDepth = depth+1; // 다음차수
									//$('.select-admin-view-single[data-depth="'+nextDepth+'"][data-uid="'+chkUid+'"]').removeAttr('disabled'); // 바로 다음차수만 해체
									//$('.btn-admin-view-single[data-depth="'+nextDepth+'"][data-uid="'+chkUid+'"]').removeAttr('disabled'); // 바로 다음차수만 해체
								}
							}
	     			}else{

	     			}
	     		}else{ alert('잘못된 접근입니다.'); window.location.reload(); }
	      },error:function(request,status,error){ console.log(request.responseText);}
	  });

		$('.select-admin-menu-view').val(sval);
	});


	// -- 바로적용
	$(document).on('click','.btn-admin-view-single',function(){
		var adminMenuUid = $(this).attr('data-uid');
		var chkValue = $('.select-admin-view-single[data-uid="'+adminMenuUid+'"]').val();
		var chkValueName = chkValue == 'Y' ? '노출':'숨김';
		var adminUid = $('.select-admin').val();

		if( adminUid == '' || adminUid == undefined) { initAdminMenu();alert("운영자를 선택해 주세요."); return false; }
		var selectVar = 'adminMenu[]='+adminMenuUid;

		// -- 적용하려는 값을 판별

		var url = '_config.admin_menuset.ajax.php';
	  $.ajax({
	      url: url, cache: false,dataType : 'json', type: "post", data: {ajaxMode : 'selectAdminMenuSet', adminUid : adminUid, selectVar : selectVar,  chkValue : chkValue , chkValueName : chkValueName}, success: function(data){
	      	if( data == undefined){ return false; }
	      	if( data.rst == 'success'){
	      		$('.js_AllCK').removeAttr('checked');
	      		AdminMenuView();
	      	}else{
	      		alert(data.msg);
	      		return false;
	      	}
	      },error:function(request,status,error){ console.log(request.responseText);}
	  });

	});


	// -- 적용버튼을 클릭시
	function submitAdminMenuSet()
	{
		var selectVar = $('.select-admin-view-single').serialize();
		var adminMenuUid = $(this).attr('data-uid');
		var adminUid = $('.select-admin').val();
		if( adminUid == '' || adminUid == undefined) { initAdminMenu();alert("운영자를 선택해 주세요."); return false; }
		var url = '_config.admin_menuset.ajax.php';
	  $.ajax({
	      url: url, cache: false,dataType : 'json', type: "post", data: {ajaxMode : 'submitAdminMenuSet', adminUid : adminUid, selectVar : selectVar}, success: function(data){
	      	if( data == undefined){ return false; }
	      	if( data.rst == 'success'){
	      		$('.js_AllCK').removeAttr('checked');
	      		AdminMenuView();
	      	}else{
	      		alert(data.msg);
	      		return false;
	      	}
	      },error:function(request,status,error){ console.log(request.responseText);}
	  });



	}


	// -- 관리자 메뉴 초기화
	function initAdminMenu()
	{
	  // $('.admin-menu-list-wrap').hide(); // 관리자 메뉴를 감싸는 레이아웃 노출
	  $('[name="selectAdminUid"]').val(''); // 선택된 관리자 초기화
	  $('.select-admin-menu-depth').val('') // 관리자 1차 메뉴 선택 초기화
	  $('.print-admin-menu-list').html('');       // 관리자 메뉴리스트 노출
	  $('.admin-menu-btn-box').hide();
	  $('.event_admin_menu_none').show();
	}


	// -- 관리자 메뉴 선택 시 메뉴를 노출시켜준다.
	function AdminMenuView()
	{
		var sval = $('.select-admin-menu-depth').val();
		var depth = $('.select-admin-menu-depth').attr('data-depth');
		var adminUid = $('.select-admin').val();
		if( adminUid == '' || adminUid == undefined) { initAdminMenu();alert("운영자를 선택해 주세요."); return false; }


		if( sval == ''){ initAdminMenu(); return false; }
		if( depth == ''){ initAdminMenu(); return false;}
		if( (depth*1) > 1){ initAdminMenu(); return false; }

		// -- ajax
		var url = '_config.admin_menuset.ajax.php';
	  $.ajax({
	      url: url, cache: false,dataType : 'html', type: "get", data: {ajaxMode : 'adminMenuList' ,  sval : sval , depth : depth, adminUid : adminUid}, success: function(data){

  	    	$('.admin-menu-list-wrap').show(); // 관리자 메뉴를 감싸는 레이아웃 노출
    		$('.print-admin-menu-list').html(data);       // 관리자 메뉴리스트 노출
    		$('.select-admin-view-single').removeAttr('disabled');

		  $('.admin-menu-btn-box').show();
		  $('.event_admin_menu_none').hide();

	      },error:function(request,status,error){ console.log(request.responseText);}
	  });
	}


	// -- 운영자 선택시 처리함수
	function selectAdmin()
	{
		var adminUid = $('.select-admin').val();
		if( adminUid == '' || adminUid == undefined){
			initAdminMenu();
			return false;
		}
		// -- ajax
		var url = '_config.admin_menuset.ajax.php';
	  $.ajax({
	      url: url, cache: false,dataType : 'json', type: "POST", data: {ajaxMode : 'adminMenuSetList' , adminUid : adminUid }, success: function(data){
	      	if( data != undefined){
	      		if(data.rst == 'success'){
	      				$('.select-admin-menu-depth').val('all').trigger('change');
	      				$('[name="selectAdminUid"]').val(adminUid);
	      				return false;
	      		}else{
	      			initAdminMenu();
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

	// -- 선택 노출/숨김 처리
	function selectAdminMenuSet(chkValue)
	{
		var chkValueName = chkValue == 'Y' ? '노출':'숨김';
		var chkLen = $('.js_ck:checked').length * 1;
		if( chkLen < 1) { alert(chkValueName + " 처리하실 메뉴를 선택해 주세요."); return false; }

		var adminUid = $('.select-admin').val();
		if( adminUid == '' || adminUid == undefined) { initAdminMenu();alert("운영자를 선택해 주세요."); return false; }

		var selectVar = $('.js_ck:checked').serialize();
		var url = '_config.admin_menuset.ajax.php';
	  $.ajax({
	      url: url, cache: false,dataType : 'json', type: "post", data: {ajaxMode : 'selectAdminMenuSet', selectVar : selectVar, adminUid : adminUid, chkValue : chkValue , chkValueName : chkValueName}, success: function(data){
	      	if( data == undefined){ return false; }
	      	if( data.rst == 'success'){
	      		$('.js_AllCK').removeAttr('checked');
	      		AdminMenuView();
	      	}else{ alert(data.msg); return false; }
	      },error:function(request,status,error){ console.log(request.responseText);}
	  });

	}

</script>


<?php include_once('wrap.footer.php');  ?>