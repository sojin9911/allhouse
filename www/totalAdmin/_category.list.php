<?php include_once('wrap.header.php');?>

		<form name="formCategoryData">
			<input type="hidden" name="locUid1" value="<?=$locUid1?>"> <!-- 선택된 1차카테고리 -->
			<input type="hidden" name="locUid2" value="<?=$locUid2?>"> <!-- 선택된 2차카테고리 -->
		</form>
		<!-- ● 카테고리설정 -->
		<div class="category">
			<ul class="table">
				<li class="td">
					<div class="depth_tt"><span class="lineup"><strong>1차 메뉴</strong><a href="#none" onclick="viewCategoryForm('add','1',''); return false;" class="c_btn h23 darkgray scrollto" data-scrollto="view-form">추가</a></span></div>
					<div class="view-category-list inner_box" data-depth="1">
					<?php
						$viewDepth = 1;
						include dirname(__FILE__)."/_category.ajax_list.php";
					?>
					</div>
				</li>
				<li class="td">
					<div class="depth_tt"><span class="lineup"><strong>2차 메뉴</strong><a href="#none" onclick="viewCategoryForm('add','2',''); return false;" style="<?=$locUid1 == '' ? 'display:none;':''?>" class="c_btn h23 darkgray scrollto add-category" data-scrollto="view-form" data-depth="2">추가</a></span></div>
					<div class="view-category-list inner_box" data-depth="2">
					<?php
					if( $locUid2 != '' || $locUid1 != ''){
						$viewDepth = 2;
						include dirname(__FILE__)."/_category.ajax_list.php";
					 }else{
					?>

						<div class="category_before">상위 메뉴를 먼저 선택해주세요.</div>
					<?php } ?>
					</div>
				</li>
				<li class="td">
					<div class="depth_tt"><span class="lineup"><strong>3차 메뉴</strong><a href="#none" onclick="viewCategoryForm('add','3',''); return false;" style="<?=$locUid2 == '' ? 'display:none;':''?>" class="c_btn h23 darkgray scrollto add-category" data-scrollto="view-form" data-depth="3">추가</a></span></div>
					<div class="view-category-list inner_box" data-depth="3">
					<?php
					if( $locUid2 != ''){
						$viewDepth = 3;
						include dirname(__FILE__)."/_category.ajax_list.php";
					 }else{
					?>
						<div class="category_before">상위 메뉴를 먼저 선택해주세요.</div>
					<?php } ?>
					</div>
				</li>
			</ul>
		</div>


		<?php // -- 메뉴 클릭 시 폼 ajax -- ?>
		<div class="view-category-form" data-name="view-form"></div>
		<?php // -- 메뉴 클릭 시 폼 ajax -- ?>


<script>



// -- 메뉴순서변경
function idxCategory(_type,_uid,_depth)
{
	var locUid1 = $('form[name="formCategoryData"] [name="locUid1"]').val();
	var locUid2 = $('form[name="formCategoryData"] [name="locUid2"]').val();
	var url = '_category.ajax_pro.php';

	_depth = _depth*1;
	_uid = _uid * 1;

  $.ajax({
      url: url, cache: false,dataType : 'json', type: "post", data: {_mode:'idx', _type : _type, _uid : _uid , locUid1 : locUid1, locUid2 : locUid2, _depth : _depth },
      success: function(data){
      	if(data.rst == 'success'){
      		viewCategoryListReload(_uid,_depth);
      		return false;
      	}else{
      		alert(data.msg);
      		return false;
      	}

      },error:function(request,status,error){ console.log(request.responseText);}
  });
}

// -- 단독으로 메뉴 새로고침 -
function viewCategoryListReload(_uid, _depth)
{
	_depth = _depth*1;
	_uid = _uid * 1;


	if(_depth == 1){
		$('.add-category').hide();
		$('form[name="formCategoryData"] [name="locUid1"]').val(_uid);
		$('form[name="formCategoryData"] [name="locUid2"]').val('');
		$('.add-category[data-depth="2"]').show();

		$('.view-category-list[data-depth="3"]').html('<div class="category_before">상위 메뉴를 먼저 선택해주세요.</div>');

	}else if( _depth == 2){
		$('.add-category').hide();
		$('.add-category[data-depth="2"]').show();
		$('.add-category[data-depth="3"]').show();
		$('form[name="formCategoryData"] [name="locUid2"]').val(_uid);
	}
	var locUid1 = $('form[name="formCategoryData"] [name="locUid1"]').val();
	var locUid2 = $('form[name="formCategoryData"] [name="locUid2"]').val();
	var url = '_category.ajax_list.php';

  $.ajax({
      url: url, cache: false,dataType : 'html', type: "get", data: {_mode:'reload',_depth : _depth, _uid : _uid , locUid1 : locUid1, locUid2 : locUid2},
      success: function(html){
      	$('.view-category-list[data-depth="'+_depth+'"]').html(html);

      },error:function(request,status,error){ console.log(request.responseText);}
  });

}

// -- 메뉴를 클릭 시 :: 3차메뉴는 제외
function viewCategoryList(_depth,_uid)
{
	$('.view-category-form').html('');

	if(_depth == '' || _depth == undefined){ return false; }
	if(_uid == '' || _uid == undefined){ _uid = '0'; }

	_depth = _depth*1;
	_uid = _uid * 1;

	$('.add-category').hide();
	if(_depth == 1){
		$('form[name="formCategoryData"] [name="locUid1"]').val(_uid);
		$('form[name="formCategoryData"] [name="locUid2"]').val('');
		$('.add-category[data-depth="2"]').show();

		$('.view-category-list[data-depth="3"]').html('<div class="category_before">상위 메뉴를 먼저 선택해주세요.</div>');

	}else if( _depth == 2){
		$('.add-category[data-depth="2"]').show();
		$('.add-category[data-depth="3"]').show();
		$('form[name="formCategoryData"] [name="locUid2"]').val(_uid);
	}

	var viewDepth = _depth + 1;
	var viewUid = _uid* 1;
	var url = '_category.ajax_list.php';

	$('.category-list-tr[data-depth="'+_depth+'"]').removeClass('hit');
	$('.category-list-tr[data-depth="'+_depth+'"][data-uid="'+viewUid+'"]').addClass('hit');

  $.ajax({
      url: url, cache: false,dataType : 'html', type: "get", data: {viewDepth : viewDepth, viewUid : viewUid },
      success: function(html){
      	$('.view-category-list[data-depth="'+viewDepth+'"]').html(html);
      },error:function(request,status,error){ console.log(request.responseText);}
  });
}

// -- 수정 또는 추가를 클릭할 시
function viewCategoryForm(_mode,_depth,_uid)
{
	if( _uid == '' || _uid == undefined){ _uid = 0; }

	// -- 현재 위치를 반환
	var locUid1 = $('form[name="formCategoryData"] [name="locUid1"]').val();
	var locUid2 = $('form[name="formCategoryData"] [name="locUid2"]').val();

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

	var url = '_category.ajax_form.php';
  $.ajax({
      url: url, cache: false,dataType : 'html', type: "get", data: {_mode:_mode,  _uid : _uid , locUid1 : locUid1, locUid2 : locUid2, _depth : _depth },
      success: function(html){
      	if(_mode !='add'){
      		viewCategoryListReload(_uid,_depth);
      	}

      	$('.view-category-form').html(html);
      	selectBestProductList(); // 선택된 카테고리 베스트 상품 가져오기
      },error:function(request,status,error){ console.log(request.responseText);}
  });

}

// -- 메뉴 추가/수정
function saveCategory()
{
//	var formData = $('form[name="formCategory"]').serialize();
	var formData = new FormData($('form[name="formCategory"]')[0]);
	formData.append('test', 'test'); // 2018-11-26 SSJ :: 406에러 참조
	if(formData == '' || formData == undefined){ return false; }

	var chkName = $('form[name="formCategory"] [name="_name"]').val();
	var chkView = $('form[name="formCategory"] [name="_view"]:checked').val();
	if(chkName == '' || chkName == undefined ){ alert('카테고리명을 입력해 주세요.'); $('form[name="formCategory"] [name="_name"]').focus(); return false; }
	if(chkView == '' || chkView == undefined){ alert('노출여부를 선택해 주세요.'); $('form[name="formCategory"] [name="_view"]').focus(); return false; }

	var url = '_category.ajax_pro.php';
  $.ajax({
          url:url,
          async:false,
          type:'POST',
          data: formData,
          dataType:'json',
          cache: false,
          contentType: false,
          processData: false,
          success: function(data){
		      	if( data.rst == 'error'){
		      		alert(data.msg); window.location.reload();
		      	}else if(data.rst == 'blank'){
		      		alert(data.msg); $('form[name="formCategory"] [name="'+data.key+'"]').focus();
		      	}else if( data.rst == 'fail-link'){
		      		alert(data.msg); $('form[name="formCategory"] [name="'+data.key+'"]').focus();
		      	}else if(data.rst == 'fail-modify' ){
		      		alert(data.msg); return false;
		      	}else if( data.rst == 'success'){
		      		alert(data.msg);


					var _mode =  $('form[name="formCategory"] input[name="_mode"]').val();
					var locUid1 = $('form[name="formCategory"] input[name="locUid1"]').val();
					var locUid2 = $('form[name="formCategory"] input[name="locUid2"]').val();

		      		switch(data._depth){
		      			case "1":
							if( _mode != 'add'){
								window.location.href="_category.list.php?viewUid="+locUid1;
								return true;
							}else{
								window.location.href="_category.list.php?viewUid="+locUid1;
							}

		      			break;
		      			case "2":
		      			case "3":
							if( _mode != 'add'){
								window.location.href="_category.list.php?locUid1="+locUid1+"&locUid2="+locUid2;
								return true;
							}else{
								window.location.href="_category.list.php?locUid1="+locUid1+"&locUid2="+locUid2;
							}

		      			break;
		      		}


		      	}else{
		      		window.location.reload();
		      	}

          },
          error: function(request, status, error){ console.log(request.responseText); }
    }); //  ajax 이벤트


}

// -- 메뉴삭제
function deleteCategory()
{

	var _uid = $('form[name="formCategory"] [name="_uid"]').val();
	var _depth = $('form[name="formCategory"] [name="_depth"]').val();
	var _mode = $('form[name="formCategory"] [name="_mode"]').val();

	if( _uid == '' || _uid == undefined){ alert("삭제할 수 없습니다."); return false; }

	if( confirm("하위 카테고리가 있을경우 삭제가 불가능합니다.\n해당 카테고리를 삭제하시겠습니까?") == false ){ return false; }

	var url = '_category.ajax_pro.php';
  $.ajax({
      url: url, cache: false,dataType : 'json', type: "post", data: {_mode : 'delete' , _uid : _uid, _depth : _depth},
      success: function(data){

      	if( data.rst == 'error'){
      		alert(data.msg); window.location.reload();
      	}else if(data.rst == 'blank'){
      		alert(data.msg); $('form[name="formCategory"] [name="'+_data.key+'"]').focus();
      	}else if( data.rst == 'fail-link'){
      		alert(data.msg); $('form[name="formCategory"] [name="'+_data.key+'"]').focus();
      	}else if( data.rst == 'fail-delete'){
      		alert(data.msg); $('form[name="formCategory"] [name="'+_data.key+'"]').focus();
      	}else if( data.rst == 'success'){
      		alert(data.msg);

					var locUid1 = $('form[name="formCategoryData"] [name="locUid1"]').val();
					var locUid2 = $('form[name="formCategoryData"] [name="locUid2"]').val();

					if(locUid1 == _uid ){ locUid1 = ''; }
					if(locUid2 == _uid ){ locUid2 = ''; }

      		switch(data._depth){
      			case "1":
      				window.location.href="_category.list.php?viewUid="+locUid1;
      			break;
      			case "2":
      			case "3":
      				window.location.href="_category.list.php?locUid1="+locUid1+"&locUid2="+locUid2;
      			break;
      		}
      	}

      },error:function(request,status,error){ console.log(request.responseText);}
  });

}

$(document).on('click','.chk-alert',function(){
	var chk = $(this).prop('checked');
	if( chk == true){
		alert('체크 시 하위카테고리가 모두 동일 적용됩니다.');
	}
});


/*
	// --- 선택된 베스트 상품 이벤트
*/

	// --- 페이지 네이트 ---
	$(document).on('click','.view-paginate .lineup a',function(){
		var ahref = $(this).attr('href');
		var hasHit = $(this).hasClass('hit');
		$('.ajax-data-box').attr('data-ahref',ahref);
		if(hasHit == true){ return false; }
		else{
			selectBestProductList();
		}
		var $root = $('html, body');
		$root.animate({
			scrollTop: $('[data-name="view-best"]').offset().top - 10
		}, 500, 'easeInOutCubic');
		return false;
	});

	// --- 베스트 아이템 삭제
	$(document).on('click','.select-best-product-delete',function(){
		var cuid = $('[name="formCategory"] [name="_uid"]').val();
		var chkLen = $('.best-pcode:checked').length;
		if( chkLen < 1){ alert("한개 이상 선택해 주세요."); return false; }
		var selectVar = $('.best-pcode:checked').serialize();
		var url = '_category.ajax_pro.php';
    $.ajax({
        url: url, cache: false,dataType : 'json', type: "POST",
        data: {_mode : 'selectBestProductDelete' , selectVar : selectVar , cuid : cuid },
        success: function(data){
        	selectBestProductList();

					var $root = $('html, body');
					$root.animate({
						scrollTop: $('[data-name="view-best"]').offset().top - 10
					}, 500, 'easeInOutCubic');
					return false;

        },error:function(request,status,error){ console.log(request.responseText); }
    });
	});

	// 베스트 상품을 가져온다.
	function selectBestProductList()
	{
		var cuid = $('[name="formCategory"] [name="_uid"]').val();
		var _mode = 'selectBestProductList';
		var ahref = $('.ajax-data-box').attr('data-ahref');

    var result = $.parseJSON($.ajax({
        url: "_category.ajax_pro.php",
        type: "get",
        dataType : "json",
        data: {_mode : _mode , ahref : ahref, cuid : cuid},
        async: false
    }).responseText);

    if(result == undefined){ return false; }
    if( (result.cnt*1) > 0) {
    	$('.js_AllCK').prop('checked',false);
    	$('.select-best-product-none').hide();
    	$('.select-best-product-list').html(result.printList);
  	}else{
  		$('.select-best-product-list').html('');
  		$('.select-best-product-none').show();
  	}

  	// -- 페이지네이트
  	$('.view-paginate').html(result.printPaginate);
	}

	// -- 베스트 상품선택
	function selectBestProductAddpop()
	{
		var cuid = $('[name="formCategory"] [name="_uid"]').val();
		if( cuid == undefined || cuid == '' || cuid == '0' || cuid == 0){ alert('카테고리 추가 후 선택 가능합니다.'); return false; }

		window.open('_category.best_product.pop.php?pass_cuid='+cuid,'selectBestProductAddpop', 'width=1120, height=800, scrollbars=yes');
	}


	// 베스트상품 순위조정 up-down-top-bottom
	function sort_up(pcode,mode,cuid) {
		<?php if(pcode && mode){ ?>

			$.ajax({
				url: "_category.ajax.sort.php", 
				cache: false,dataType : 'json', type: "POST", 
				data: {_mode:mode,cuid:cuid,pcode:pcode }, 
				success: function(data){
					if(data.rst == 'fail'){
						alert(data.msg);
						return false;
					}
					selectBestProductList();
				},error:function(request,status,error){ console.log(request.responseText); }
			});
		<?php }else{ ?>
			alert('순위조정은 정렬상태가 "노출순위 ▲"인 상태에서만 조정할 수 있습니다,');
		<?php } ?>
	}
	// 베스트상품 순위그룹 수정
	function sort_group(pcode,cuid){
		var group = $('.sort_group_'+ pcode).val()*1;
		if(group <= 0){
			alert('상품 순위를 입력해 주시기 바랍니다.');
			$('.sort_group_'+ pcode).focus();
			return false;
		}

		$.ajax({
			url: "_category.ajax.sort.php", 
			cache: false,dataType : 'json', type: "POST", 
			data: {_mode : 'modify_group',_group:group,cuid:cuid,pcode:pcode }, 
			success: function(data){
				if(data.rst == 'fail'){
					alert(data.msg);
					return false;
				}
				selectBestProductList();
				if(data.msg !=''){
					alert(data.msg);
				}
			},error:function(request,status,error){ console.log(request.responseText); }
		});
	}

</script>





<?php include_once('wrap.footer.php'); ?>