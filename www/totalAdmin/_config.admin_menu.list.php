<?php include_once('wrap.header.php');?>
		
		<form name="formAdminMenuData">
			<input type="hidden" name="locUid1" value="<?=$locUid1?>"> <!-- 선택된 1차카테고리 -->
			<input type="hidden" name="locUid2" value="<?=$locUid2?>"> <!-- 선택된 2차카테고리 -->
		</form>
		<!-- ● 카테고리설정 -->
		<div class="category">
			<ul class="table">
				<li class="td">
					<div class="depth_tt"><span class="lineup"><strong>1차 메뉴</strong><a href="#none" onclick="viewAdminMenuForm('add','1',''); return false;" class="c_btn h23 darkgray scrollto" data-scrollto="view-form">추가</a></span></div>
					<div class="view-admin-menu-list inner_box" data-depth="1">
					<?php 
						$viewDepth = 1;
						include dirname(__FILE__)."/_config.admin_menu.ajax_list.php"; 
					?>
					</div>
				</li>
				<li class="td">
					<div class="depth_tt"><span class="lineup"><strong>2차 메뉴</strong><a href="#none" onclick="viewAdminMenuForm('add','2',''); return false;" style="<?=$locUid1 == '' ? 'display:none;':''?>" class="c_btn h23 darkgray scrollto add-admin-menu" data-scrollto="view-form" data-depth="2">추가</a></span></div>
					<div class="view-admin-menu-list inner_box" data-depth="2">
					<?php 
					if( $locUid2 != '' || $locUid1 != ''){ 
						$viewDepth = 2;
						include dirname(__FILE__)."/_config.admin_menu.ajax_list.php"; 
					 }else{ 						
					?>					
				
						<div class="category_before">상위 메뉴를 먼저 선택해주세요.</div>
					<?php } ?>
					</div>
				</li>
				<li class="td">
					<div class="depth_tt"><span class="lineup"><strong>3차 메뉴</strong><a href="#none" onclick="viewAdminMenuForm('add','3',''); return false;" style="<?=$locUid2 == '' ? 'display:none;':''?>" class="c_btn h23 darkgray scrollto add-admin-menu" data-scrollto="view-form" data-depth="3">추가</a></span></div>
					<div class="view-admin-menu-list inner_box" data-depth="3">
					<?php 
					if( $locUid2 != ''){ 
						$viewDepth = 3;
						include dirname(__FILE__)."/_config.admin_menu.ajax_list.php"; 
					 }else{ 						
					?>						
						<div class="category_before">상위 메뉴를 먼저 선택해주세요.</div>
					<?php } ?>
					</div>
				</li>
			</ul>
		</div>
		

		<?php // -- 메뉴 클릭 시 폼 ajax -- ?> 
		<div class="view-admin-menu-form" data-name="view-form"></div>
		<?php // -- 메뉴 클릭 시 폼 ajax -- ?> 


<script>

// -- 메뉴순서변경 
function idxAdminMenu(_type,_uid,_depth)
{

	var locUid1 = $('form[name="formAdminMenuData"] [name="locUid1"]').val();
	var locUid2 = $('form[name="formAdminMenuData"] [name="locUid2"]').val();
	var url = '_config.admin_menu.ajax_pro.php';

	_depth = _depth*1;
	_uid = _uid * 1;

  $.ajax({
      url: url, cache: false,dataType : 'json', type: "post", data: {_mode:'idx', _type : _type, _uid : _uid , locUid1 : locUid1, locUid2 : locUid2, _depth : _depth }, 
      success: function(data){
      	if(data.rst == 'success'){
      		viewAdminMenuListReload(_uid,_depth);
      		return false;  
      	}else{
      		alert(data.msg);
      		return false;
      	}
      	   
      },error:function(request,status,error){ console.log(error);}
  });	 	
}

// -- 단독으로 메뉴 새로고침 -
function viewAdminMenuListReload(_uid, _depth)
{

	var locUid1 = $('form[name="formAdminMenuData"] [name="locUid1"]').val();
	var locUid2 = $('form[name="formAdminMenuData"] [name="locUid2"]').val();

	_depth = _depth*1;
	_uid = _uid * 1;

	
	if(_depth == 1){
		$('.add-admin-menu').hide();
		$('form[name="formAdminMenuData"] [name="locUid1"]').val(_uid);
		$('form[name="formAdminMenuData"] [name="locUid2"]').val('');
		$('.add-admin-menu[data-depth="2"]').show();

		$('.view-admin-menu-list[data-depth="3"]').html('<div class="category_before">상위 메뉴를 먼저 선택해주세요.</div>');

	}else if( _depth == 2){
		$('.add-admin-menu').hide();
		$('.add-admin-menu[data-depth="2"]').show();
		$('.add-admin-menu[data-depth="3"]').show();
		$('form[name="formAdminMenuData"] [name="locUid2"]').val(_uid);
	}

	var locUid1 = $('form[name="formAdminMenuData"] [name="locUid1"]').val();
	var locUid2 = $('form[name="formAdminMenuData"] [name="locUid2"]').val();

	var url = '_config.admin_menu.ajax_list.php';

  $.ajax({
      url: url, cache: false,dataType : 'html', type: "get", data: {_mode:'reload',_depth : _depth, _uid : _uid , locUid1 : locUid1, locUid2 : locUid2}, 
      success: function(html){
      	$('.view-admin-menu-list[data-depth="'+_depth+'"]').html(html);
      },error:function(request,status,error){ console.log(error);}
  });	 

}

// -- 메뉴를 클릭 시 :: 3차메뉴는 제외
function viewAdminMenuList(_depth,_uid)
{
	$('.view-admin-menu-form').html('');

	if(_depth == '' || _depth == undefined){ return false; }
	if(_uid == '' || _uid == undefined){ _uid = '0'; }

	_depth = _depth*1;
	_uid = _uid * 1;

	$('.add-admin-menu').hide();
	if(_depth == 1){
		$('form[name="formAdminMenuData"] [name="locUid1"]').val(_uid);
		$('form[name="formAdminMenuData"] [name="locUid2"]').val('');
		$('.add-admin-menu[data-depth="2"]').show();

		$('.view-admin-menu-list[data-depth="3"]').html('<div class="category_before">상위 메뉴를 먼저 선택해주세요.</div>');

	}else if( _depth == 2){
		$('.add-admin-menu[data-depth="2"]').show();
		$('.add-admin-menu[data-depth="3"]').show();
		$('form[name="formAdminMenuData"] [name="locUid2"]').val(_uid);
	}

	var viewDepth = _depth + 1;
	var viewUid = _uid* 1;
	var url = '_config.admin_menu.ajax_list.php';

	$('.admin-menu-list-tr[data-depth="'+_depth+'"]').removeClass('hit');
	$('.admin-menu-list-tr[data-depth="'+_depth+'"][data-uid="'+viewUid+'"]').addClass('hit');

  $.ajax({
      url: url, cache: false,dataType : 'html', type: "get", data: {viewDepth : viewDepth, viewUid : viewUid }, 
      success: function(html){
      	$('.view-admin-menu-list[data-depth="'+viewDepth+'"]').html(html);
      },error:function(request,status,error){ console.log(error);}
  });	 
}

// -- 수정 또는 추가를 클릭할 시
function viewAdminMenuForm(_mode,_depth,_uid)
{
	if( _uid == '' || _uid == undefined){ _uid = 0; }

	// -- 현재 위치를 반환
	var locUid1 = $('form[name="formAdminMenuData"] [name="locUid1"]').val();
	var locUid2 = $('form[name="formAdminMenuData"] [name="locUid2"]').val();

	_depth = _depth *1;
	_uid = _uid *1;

	// -- 메뉴추가일 시 상위 카테고리 확인
	if(_mode == 'add'){
		if(_depth == 2){
			if( locUid1 == '' || locUid1 == undefined){ alert("상위카테고리를 선택해 주세요."); return false; }
		}else if(_depth == 3){
			if( locUid2 == '' || locUid2 == undefined){ alert("상위카테고리를 선택해 주세요."); return false; }
		}
	}

	var url = '_config.admin_menu.ajax_form.php';
  $.ajax({
      url: url, cache: false,dataType : 'html', type: "get", data: {_mode:_mode,  _uid : _uid , locUid1 : locUid1, locUid2 : locUid2, _depth : _depth }, 
      success: function(html){
      	$('.view-admin-menu-form').html(html);
      	if(_mode !='add'){ 
      		viewAdminMenuListReload(_uid,_depth);
      	}      	
      },error:function(request,status,error){ console.log(error);}
  });	 	
}

// -- 메뉴 추가/수정
function saveAdminMenu()
{
	var formData = $('form[name="formAdminMenu"]').serialize();
	if(formData == '' || formData == undefined){ return false; }

	var chkName = $('form[name="formAdminMenu"] [name="_name"]').val();
	var chkView = $('form[name="formAdminMenu"] [name="_view"]:checked').val();
	if(chkName == '' || chkName == undefined ){ alert('메뉴명을 입력해 주세요.'); $('form[name="formAdminMenu"] [name="_name"]').focus(); return false; }
	if(chkView == '' || chkView == undefined){ alert('노출여부를 선택해 주세요.'); $('form[name="formAdminMenu"] [name="_view"]').focus(); return false; }

	var url = '_config.admin_menu.ajax_pro.php';
  $.ajax({
      url: url, cache: false,dataType : 'json', type: "post", data: formData, 
      success: function(data){

      	if( data.rst == 'error'){
      		alert(data.msg); window.location.reload();
      	}else if(data.rst == 'blank'){
      		alert(data.msg); $('form[name="formAdminMenu"] [name="'+data.key+'"]').focus();
      	}else if( data.rst == 'fail-link'){
      		alert(data.msg); $('form[name="formAdminMenu"] [name="'+data.key+'"]').focus();
      	}else if(data.rst == 'fail-modify' ){
      		alert(data.msg); return false;
      	}else if( data.rst == 'success'){
      		alert(data.msg); 

					var locUid1 = $('form[name="formAdminMenuData"] [name="locUid1"]').val();
					var locUid2 = $('form[name="formAdminMenuData"] [name="locUid2"]').val();
			

      		switch(data._depth){
      			case "1":
      				window.location.href="_config.admin_menu.list.php?viewUid="+locUid1;
      			break;
      			case "2":
      			case "3":
      				window.location.href="_config.admin_menu.list.php?locUid1="+locUid1+"&locUid2="+locUid2;
      			break;
      		}
      	}else{
      		window.location.reload();
      	}

      },error:function(request,status,error){ console.log(error);}
  });	 		
}

// -- 메뉴삭제
function deleteAdminMenu()
{

	var _uid = $('form[name="formAdminMenu"] [name="_uid"]').val();
	var _depth = $('form[name="formAdminMenu"] [name="_depth"]').val();
	var _mode = $('form[name="formAdminMenu"] [name="_mode"]').val();

	if( _uid == '' || _uid == undefined){ alert("삭제할 수 없습니다."); return false; }

	if( confirm("상위메뉴일경우 하위메뉴가 모두 삭제됩니다.\n해당 메뉴를 삭제하시겠습니까?") == false ){ return false; }

	var url = '_config.admin_menu.ajax_pro.php';
  $.ajax({
      url: url, cache: false,dataType : 'json', type: "post", data: {_mode : 'delete' , _uid : _uid, _depth : _depth}, 
      success: function(data){

      	if( data.rst == 'error'){
      		alert(data.msg); window.location.reload();
      	}else if(data.rst == 'blank'){
      		alert(data.msg); $('form[name="formAdminMenu"] [name="'+_data.key+'"]').focus();
      	}else if( data.rst == 'fail-link'){
      		alert(data.msg); $('form[name="formAdminMenu"] [name="'+_data.key+'"]').focus();
      	}else if( data.rst == 'fail-delete'){
      		alert(data.msg); $('form[name="formAdminMenu"] [name="'+_data.key+'"]').focus();
      	}else if( data.rst == 'success'){
      		alert(data.msg); 

					var locUid1 = $('form[name="formAdminMenuData"] [name="locUid1"]').val();
					var locUid2 = $('form[name="formAdminMenuData"] [name="locUid2"]').val();

					if(locUid1 == _uid ){ locUid1 = ''; }
					if(locUid2 == _uid ){ locUid2 = ''; }

      		switch(data._depth){
      			case "1":
      				window.location.href="_config.admin_menu.list.php?viewUid="+locUid1;
      			break;
      			case "2":
      			case "3":
      				window.location.href="_config.admin_menu.list.php?locUid1="+locUid1+"&locUid2="+locUid2;
      			break;
      		}      		
      	}

      },error:function(request,status,error){ console.log(error);}
  });	 	

}


</script>


	


<?php include_once('wrap.footer.php'); ?>