<?php include_once('wrap.header.php');?>

		<form name="formAdminMenuData">
			<input type="hidden" name="locUid1" value="<?=$locUid1?>"> <!-- 선택된 1차카테고리 -->
			<input type="hidden" name="locUid2" value="<?=$locUid2?>"> <!-- 선택된 2차카테고리 -->
		</form>



		<!-- ● 카테고리설정 -->
		<div class="category if_brand">
			<ul class="table">
				<li class="td ">
					<div class="depth_tt"><span class="lineup"><strong>브랜드</strong></span></div>
					<div class="view-admin-menu-list inner_box" data-depth="1">
					<?php
						$viewDepth = 1;
						include dirname(__FILE__)."/_brand.ajax_list.php";
					?>
					</div>
				</li>
			</ul>
		</div>


		<?php // -- 브랜드 클릭 시 폼 ajax -- ?>
		<div class="view-admin-menu-form" data-name="view-form"></div>
		<?php // -- 브랜드 클릭 시 폼 ajax -- ?>

<script>

// -- 브랜드순서변경
function idxAdminMenu(_type,_uid,_depth)
{

	var locUid1 = $('form[name="formAdminMenuData"] [name="locUid1"]').val();
	var locUid2 = $('form[name="formAdminMenuData"] [name="locUid2"]').val();
	var url = '_brand.ajax_pro.php';

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

// -- 단독으로 브랜드 새로고침 -
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

		$('.view-admin-menu-list[data-depth="3"]').html('<div class="category_before">상위 브랜드를 먼저 선택해주세요.</div>');

	}else if( _depth == 2){
		$('.add-admin-menu').hide();
		$('.add-admin-menu[data-depth="2"]').show();
		$('.add-admin-menu[data-depth="3"]').show();
		$('form[name="formAdminMenuData"] [name="locUid2"]').val(_uid);
	}

	var url = '_brand.ajax_list.php';

  $.ajax({
      url: url, cache: false,dataType : 'html', type: "get", data: {_mode:'reload',_depth : _depth, _uid : _uid , locUid1 : locUid1, locUid2 : locUid2},
      success: function(html){
      	$('.view-admin-menu-list[data-depth="'+_depth+'"]').html(html);
      },error:function(request,status,error){ console.log(error);}
  });

}

// -- 브랜드를 클릭 시 :: 3차브랜드는 제외
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

		$('.view-admin-menu-list[data-depth="3"]').html('<div class="category_before">상위 브랜드를 먼저 선택해주세요.</div>');

	}else if( _depth == 2){
		$('.add-admin-menu[data-depth="2"]').show();
		$('.add-admin-menu[data-depth="3"]').show();
		$('form[name="formAdminMenuData"] [name="locUid2"]').val(_uid);
	}

	var viewDepth = _depth + 1;
	var viewUid = _uid* 1;
	var url = '_brand.ajax_list.php';

	$('.admin-menu-list-tr[data-depth="'+_depth+'"]').removeClass('hit');
	$('.admin-menu-list-tr[data-depth="'+_depth+'"][data-uid="'+viewUid+'"]').addClass('hit');

  $.ajax({
      url: url, cache: false,dataType : 'html', type: "get", data: {viewDepth : viewDepth, viewUid : viewUid },
      success: function(html){
      	$('.view-admin-menu-list[data-depth="'+viewDepth+'"]').html(html);
      },error:function(request,status,error){ console.log(error);}
  });
}


// -- 브랜드 추가/수정
function saveAdminMenu(uid)
{
	// 수정
	if(uid > 0 ) {
		var mode = 'modify';
		var _name = $("input[name='_name["+ uid +"]']").val();
		var _view = $("input[name='_view["+ uid +"]']").filter(function() {if (this.checked) return this;}).val();

		if(_name == '' || _name == undefined ){ alert('브랜드명을 입력해 주세요.'); $("input[name='_name["+ uid +"]']").focus(); return false; }
		if(_view == '' || _view == undefined){ alert('노출여부를 선택해 주세요.'); $("input[name='_view["+ uid +"]']").focus(); return false; }

	}
	// 추가
	else {
		var mode = 'add';
		var _name = $("input[name='ADD_name']").val();
		var _view = $("input[name='ADD_view']").filter(function() {if (this.checked) return this;}).val();
		uid = 0;

		if(_name == '' || _name == undefined ){ alert('브랜드명을 입력해 주세요.'); $("input[name='ADD_name']").focus(); return false; }
		if(_view == '' || _view == undefined){ alert('노출여부를 선택해 주세요.'); $("input[name='ADD_view']").focus(); return false; }
	}


	var formData = '_mode='+ mode +'&';
	formData += '_uid=' + uid + '&';
	formData += '_name=' + _name + '&';
	formData += '_view=' + _view;


	var url = '_brand.ajax_pro.php';
	$.ajax({
      url: url, cache: false,dataType : 'json', type: "post", data: formData,
      success: function(data){

      	if( data.rst == 'error'){
      		alert(data.msg); window.location.reload();
      	}else if(data.rst == 'blank'){
      		alert(data.msg); $('input[name="'+data.key+'"]').focus();
      	}else if(data.rst == 'fail-modify' ){
      		alert(data.msg); return false;
      	}else if( data.rst == 'success'){
      		alert(data.msg);
			window.location.href="_brand.list.php";
      	}else{
      		window.location.reload();
      	}

      },error:function(request,status,error){ console.log(error);}
  });
}

// -- 브랜드삭제
function deleteAdminMenu(uid)
{

	var _uid = uid;
	if( _uid == '' || _uid == undefined){ alert("삭제할 수 없습니다."); return false; }

	if( confirm("해당 브랜드를 삭제하시겠습니까?") == false ){ return false; }

	var url = '_brand.ajax_pro.php';
	$.ajax({
      url: url, cache: false,dataType : 'json', type: "post", data: {_mode : 'delete' , _uid : _uid},
      success: function(data){

      	if( data.rst == 'error'){
      		alert(data.msg); window.location.reload();
      	}
		else if( data.rst == 'success'){
      		alert(data.msg);
			window.location.href="_brand.list.php";
      	}

      },error:function(request,status,error){ console.log(error);}
  });

}


	 // -------------- 일괄노출변경 / 일괄숨김변경 --------------
	 function changeView(_type) {
		 $('._view').each(function(){

			// 노출 적용
			if( _type == 'show'){
				if($(this).val() == 'Y') {$(this).prop('checked', true) ;}
				else if($(this).val() == 'N') {$(this).prop('checked', false) ;}
			}
			// 숨김 적용
			else if( _type == 'hide'){
				if($(this).val() == 'Y') {$(this).prop('checked', false) ;}
				else if($(this).val() == 'N') {$(this).prop('checked', true) ;}
			}

		 });
	 }
	 // -------------- 선택상품 일괄지정 --------------

</script>





<?php include_once('wrap.footer.php'); ?>