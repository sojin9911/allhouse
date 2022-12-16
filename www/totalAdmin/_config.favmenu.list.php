<?php
	// SSJ : 2017-11-11 자주쓰는메뉴 , 2차 구조 적용 및 관리자별 별도 설정 추가
	include_once('wrap.header.php');
?>

		<form name="formFavMenuData">
			<input type="hidden" name="locUid1" value="<?=$locUid1?>"> <!-- 선택된 1차카테고리 -->
			<input type="hidden" name="locUid2" value="<?=$locUid2?>"> <!-- 선택된 2차카테고리 -->
		</form>
		<!-- ● 카테고리설정 -->
		<div class="category">
			<ul class="table">
				<li class="td">
					<div class="depth_tt"><span class="lineup"><strong>메뉴분류</strong><a href="#none" onclick="viewFavMenuForm('add','1',''); return false;" class="c_btn h23 darkgray scrollto" data-scrollto="view-form">추가</a></span></div>
					<div class="view-favmenu-list inner_box" data-depth="1">
					<?php
						$viewDepth = 1;
						include dirname(__FILE__)."/_config.favmenu.ajax_list.php";
					?>
					</div>
				</li>
				<li class="td">
					<div class="depth_tt"><span class="lineup"><strong>세부메뉴</strong><a href="#none" onclick="viewFavMenuForm('add','2',''); return false;" style="<?=$locUid1 == '' ? 'display:none;':''?>" class="c_btn h23 darkgray scrollto add-favmenu" data-scrollto="view-form" data-depth="2">추가</a></span></div>
					<div class="view-favmenu-list inner_box" data-depth="2">
					<?php
					if( $locUid2 != '' || $locUid1 != ''){
						$viewDepth = 2;
						include dirname(__FILE__)."/_config.favmenu.ajax_list.php";
					 }else{
					?>

						<div class="category_before">메뉴분류를 먼저 선택해주세요.</div>
					<?php } ?>
					</div>
				</li>
			</ul>
		</div>


		<?php // -- 메뉴 클릭 시 폼 ajax -- ?>
		<div class="view-favmenu-form" data-name="view-form"></div>
		<?php // -- 메뉴 클릭 시 폼 ajax -- ?>


<script>

// -- 메뉴순서변경
function idxFavMenu(_type,_uid,_depth)
{

	var locUid1 = $('form[name="formFavMenuData"] [name="locUid1"]').val();
	var locUid2 = $('form[name="formFavMenuData"] [name="locUid2"]').val();
	var url = '_config.favmenu.ajax_pro.php';

	_depth = _depth*1;
	_uid = _uid * 1;

  $.ajax({
      url: url, cache: false,dataType : 'json', type: "post", data: {_mode:'idx', _type : _type, _uid : _uid , locUid1 : locUid1, locUid2 : locUid2, _depth : _depth }, async:false,
      success: function(data){
		if(data.rst == 'success'){
			viewFavMenuListReload(_uid,_depth);
			return false;
		}else{
			alert(data.msg);
			return false;
		}

      },error:function(request,status,error){ console.log(error);}
  });
}

// -- 단독으로 메뉴 새로고침 -
function viewFavMenuListReload(_uid, _depth)
{

	var locUid1 = $('form[name="formFavMenuData"] [name="locUid1"]').val();

	_depth = _depth*1;
	_uid = _uid * 1;

	if(_depth == 1){
		$('.add-favmenu').hide();
		$('form[name="formFavMenuData"] [name="locUid1"]').val(_uid);
		$('form[name="formFavMenuData"] [name="locUid2"]').val('');
		$('.add-favmenu[data-depth="2"]').show();

	}else if( _depth == 2){
		$('.add-favmenu').hide();
		$('.add-favmenu[data-depth="2"]').show();
		$('form[name="formFavMenuData"] [name="locUid2"]').val(_uid);
	}

	var locUid2 = $('form[name="formFavMenuData"] [name="locUid2"]').val();
	var url = '_config.favmenu.ajax_list.php';

	$.ajax({
		url: url, cache: false,dataType : 'html', type: "get", data: {_mode:'reload',_depth : _depth, _uid : _uid, viewUid : locUid1 , locUid1 : locUid1, locUid2 : locUid2}, async:false,
		success: function(html){
			$('.view-favmenu-list[data-depth="'+_depth+'"]').html(html);
			// 1차메뉴 변경시에만
			if(_depth == 1 && locUid1 != $('form[name="formFavMenuData"] [name="locUid1"]').val()) viewFavMenuList(_depth,_uid);
			else viewFavMenuList(_depth,_uid);
		},error:function(request,status,error){ console.log(error);}
	});

}

// -- 메뉴를 클릭 시 :: 1차메뉴만
function viewFavMenuList(_depth,_uid,_clear)
{
	if(_clear != 'N'){
		$('.view-favmenu-form').html('');
	}

	if(_depth == '' || _depth == undefined){ return false; }
	if(_uid == '' || _uid == undefined){ _uid = '0'; }

	_depth = _depth*1;
	_uid = _uid * 1;

	var viewDepth = _depth + 1;
	var viewUid = _uid* 1;
	var url = '_config.favmenu.ajax_list.php';

	$('.add-favmenu').hide();
	$('.favmenu-list-tr[data-depth="'+_depth+'"]').removeClass('hit');
	$('.favmenu-list-tr[data-depth="'+_depth+'"][data-uid="'+viewUid+'"]').addClass('hit');

	if(_depth == 1){
		$('form[name="formFavMenuData"] [name="locUid1"]').val(_uid);
		$('form[name="formFavMenuData"] [name="locUid2"]').val('');
		$('.add-favmenu[data-depth="2"]').show();

		$.ajax({
			url: url, cache: false,dataType : 'html', type: "get", data: {viewDepth : viewDepth, viewUid : viewUid }, async:false,
			success: function(html){
				$('.view-favmenu-list[data-depth="'+viewDepth+'"]').html(html);
				viewFavMenuForm('click',_depth,_uid);
			},error:function(request,status,error){ console.log(error);}
		});
	}else{
		$('.add-favmenu[data-depth="2"]').show();
		viewFavMenuForm('click',_depth,_uid);
	}
}

// -- 수정 또는 추가를 클릭할 시
function viewFavMenuForm(_mode,_depth,_uid)
{
	if( _uid == '' || _uid == undefined){ _uid = 0; }

	if(_mode == 'click'){
		_mode = 'modify';
	}else{
		viewFavMenuList(_depth,_uid,'N');
	}

	// -- 현재 위치를 반환
	var locUid1 = $('form[name="formFavMenuData"] [name="locUid1"]').val();
	var locUid2 = $('form[name="formFavMenuData"] [name="locUid2"]').val();

	_depth = _depth *1;
	_uid = _uid *1;

	// -- 메뉴추가일 시 상위 카테고리 확인
	if(_mode == 'add'){
		if(_depth == 2){
			if( locUid1 == '' || locUid1 == undefined){ alert("메뉴분류를 선택해 주세요."); return false; }
		}
	}

	var url = '_config.favmenu.ajax_form.php';
	$.ajax({
		url: url, cache: false,dataType : 'html', type: "get", data: {_mode:_mode,  _uid : _uid , locUid1 : locUid1, locUid2 : locUid2, _depth : _depth }, async:false,
		success: function(html){
			$('.view-favmenu-form').html(html);
		},error:function(request,status,error){ console.log(error);}
	});
}

// -- 메뉴 추가/수정
function saveFavMenu()
{
	var formData = $('form[name="formFavMenu"]').serialize();
	if(formData == '' || formData == undefined){ return false; }

	var chkName = $('form[name="formFavMenu"] [name="_name"]').val();
	var chkView = $('form[name="formFavMenu"] [name="_view"]:checked').val();
	if(chkName == '' || chkName == undefined ){ alert('메뉴명을 입력해 주세요.'); $('form[name="formFavMenu"] [name="_name"]').focus(); return false; }
	if(chkView == '' || chkView == undefined){ alert('노출여부를 선택해 주세요.'); $('form[name="formFavMenu"] [name="_view"]').focus(); return false; }

	var url = '_config.favmenu.ajax_pro.php';
  $.ajax({
      url: url, cache: false,dataType : 'json', type: "post", data: formData, async:false,
      success: function(data){

      	if( data.rst == 'error'){
      		alert(data.msg); window.location.reload();
      	}else if(data.rst == 'blank'){
      		alert(data.msg); $('form[name="formFavMenu"] [name="'+data.key+'"]').focus();
      	}else if( data.rst == 'fail-link'){
      		alert(data.msg); $('form[name="formFavMenu"] [name="'+data.key+'"]').focus();
      	}else if(data.rst == 'fail-modify' ){
      		alert(data.msg); return false;
      	}else if( data.rst == 'success'){
			var _depth = $('form[name="formFavMenu"] input[name=_depth]').val();
			var _uid = $('form[name="formFavMenu"] input[name=_uid]').val();
			var locUid1 = $('form[name="formFavMenu"] input[name=locUid1]').val();
			if(_depth == '1'){
				viewFavMenuListReload(data._uid,data._depth);
			}else{
				viewFavMenuList(1, locUid1);
				$('.favmenu-list-tr[data-depth="2"][data-uid="'+_uid+'"]').addClass('hit');
			}
			viewFavMenuForm('modify',data._depth, data._uid);
      		alert(data.msg);
			return true;
      	}else{
      		window.location.reload();
      	}

      },error:function(request,status,error){ console.log(error);}
  });
}

// -- 메뉴삭제
function deleteFavMenu()
{

	var _uid = $('form[name="formFavMenu"] [name="_uid"]').val();
	var _depth = $('form[name="formFavMenu"] [name="_depth"]').val();
	var _mode = $('form[name="formFavMenu"] [name="_mode"]').val();

	if( _uid == '' || _uid == undefined){ alert("삭제할 수 없습니다."); return false; }

	var msg = '';
	if(_depth == 1){
		msg = "메뉴분류를 삭제할 경우 하위 세부메뉴가 모두 삭제 됩니다.\n해당 분류를 삭제하시겠습니까?";
	}else{
		msg = "해당 세부메뉴를 삭제하시겠습니까?";
	}

	if( confirm(msg) == false ){ return false; }

	var url = '_config.favmenu.ajax_pro.php';
  $.ajax({
      url: url, cache: false,dataType : 'json', type: "post", data: {_mode : 'delete' , _uid : _uid, _depth : _depth}, async:false,
      success: function(data){

      	if( data.rst == 'error'){
      		alert(data.msg); window.location.reload();
      	}else if(data.rst == 'blank'){
      		alert(data.msg); $('form[name="formFavMenu"] [name="'+_data.key+'"]').focus();
      	}else if( data.rst == 'fail-link'){
      		alert(data.msg); $('form[name="formFavMenu"] [name="'+_data.key+'"]').focus();
      	}else if( data.rst == 'fail-delete'){
      		alert(data.msg); $('form[name="formFavMenu"] [name="'+_data.key+'"]').focus();
      	}else if( data.rst == 'success'){
      		alert(data.msg);

					var locUid1 = $('form[name="formFavMenuData"] [name="locUid1"]').val();
					var locUid2 = $('form[name="formFavMenuData"] [name="locUid2"]').val();

					if(locUid1 == _uid ){ locUid1 = ''; }
					if(locUid2 == _uid ){ locUid2 = ''; }

      		switch(data._depth){
      			case "1":
      				window.location.href="_config.favmenu.list.php?viewUid="+locUid1;
      			break;
      			case "2":
      			case "3":
      				window.location.href="_config.favmenu.list.php?locUid1="+locUid1+"&locUid2="+locUid2;
      			break;
      		}
      	}

      },error:function(request,status,error){ console.log(error);}
  });

}

// 세부메뉴 수정 시 관리자 메뉴 선택
function menu_select(_idx) {
	$.ajax({
		url: "<?php echo OD_ADMIN_DIR; ?>/_config.favmenu.ajax_pro.php",
		cache: false,
		dataType: "json",
		type: "POST",
		data: "_mode=select_admin_menu&pass_menu01=" + $("[name=pass_menu01]").val() + "&pass_menu02=" + $("[name=pass_menu02]").val()+"&pass_idx=" + _idx ,
		success: function(data){
			if(_idx == 2) {
				//$("select[name=pass_menu02]").val(apppass_menu03); // 현재정보 적용
				$("select[name=pass_menu03]").find("option").remove().end().append('<option value="">-선택-</option>');
				var option_str = '';
				for (var i = 0; i < data.length; i++) {
					option_str += '<option value="' + data[i].optionValue + '" >' + data[i].optionDisplay + '</option>';
				}
				$("select[name=pass_menu03]").append(option_str);
			}
			else if(_idx == 1){
				$("select[name=pass_menu02]").find("option").remove().end().append('<option value="">-선택-</option>');
				var option_str = '';
				for (var i = 0; i < data.length; i++) {
					option_str += '<option value="' + data[i].optionValue + '" >' + data[i].optionDisplay + '</option>';
				}
				$("select[name=pass_menu02]").append(option_str);
				$("select[name=pass_menu03]").find("option").remove().end().append('<option value="">-선택-</option>');
			}
		}
	});
}
// 세부메뉴 수정 시 관리자 메뉴 선택
</script>





<?php include_once('wrap.footer.php'); ?>