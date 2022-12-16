
<!-- ● 단락타이틀 -->
<div class="group_title"><strong>이용안내</strong></div>

<!-- ● 폼 영역 (검색/폼 공통으로 사용) -->
<div class="data_form">

	<!-- ● 내부탭 -->
	<div class="c_tab tab_guide">
		<ul>
			<?php 
				$is_hit = true;
				foreach($arrProGuideType as $k=>$v){ 
			?>
				<li class="<?php echo ($is_hit?'hit':null); ?>"><a href="#none" class="btn tab_menu" data-idx="<?php echo $k; ?>"><strong><?php echo $v; ?></strong></a></li>
				<?php if($is_hit){ ?><input type="hidden" name="guide_tabidx_save" value="<?php echo $k; ?>"><?php } ?>
			<?php 
				$is_hit=false; } 
			?>
		</ul>
	</div>

	

	<table class="table_form">	
		<tbody>
			<tr>
				<td>
					<div id="ajax_guide_info_area">
						<?php include_once(OD_ADMIN_ROOT.'/_product.inc_guide.ajax.php'); ?>
					</div>
					<div class="dash_line"><!-- 점선라인 --></div>
					<div style="position:relative;overflow:hidden;">
						<?php if($first_type=='none'){ ?>
							<div class="guide_layout" style="position: absolute;width: 100%;height: 100%;background: rgba(98, 98, 98, 0.38); z-index:5; bottom:17px;"></div>
						<?php }else if($first_type=='list'){ ?>
							<div class="guide_layout" style="position: absolute;width: 100%;height: 100%;background: rgba(239, 239, 239, 0.38); bottom:17px;"></div>
						<?php }else{ ?>
							<div class="guide_layout" style="position: absolute;width: 100%;height: 100%; display:none; bottom:17px;"></div>
						<?php } ?>
						<textarea name="" class="design SEditor guide_viewer" style="width:100%;height:300px;"><?php echo stripslashes($first_content); ?></textarea>
					</div>
				</td>
			</tr>
		</tbody>
	</table>

	<script>
		// 입점업체 변경시
		$(document).on('change', '[name=_cpid]', function(){
			var cpid = $(this).val();
			$.ajax({
				url:'<?php echo OD_ADMIN_DIR; ?>/_product.inc_guide.ajax.php',
				data:'_code=<?php echo $_code; ?>&_cpid=' + cpid,
				type:'get',
				dataType:'html',
				success:function(data){
					$('#ajax_guide_info_area').html(data);
					$('.tab_guide').find('li.hit').find('.tab_menu').trigger('click');
				}
			});
		});
		// 이용내역 적용타입 선택
		$(document).on('change', '.js_chg_guide_type', function(){
			var idx = $(this).data('idx');

			// 변경전 값 저장
			var sv = $('input[name=p_guide_type_'+idx+'_save]').val();
			$('input[name=p_guide_type_'+idx+'_save]').val($(this).val());

			var type = $('input[name=p_guide_type_'+ idx +']:checked').val();
			$('select[name=p_guide_uid_'+ idx +']').hide();

			// 변경전 값이 직접입력이라면 직접입력 값 저장
			if(sv=='manual' && type!=sv){
				// 불러온내용 에디터에 적용
				var id = $('.guide_viewer').attr('id');
				var _bak = '';
				if(oEditors.length > 0){
					_bak = oEditors.getById[id].getContents();
				}

				$('[name=p_guide_'+ idx +']').val(_bak);
			}

			if(type=='none'){
				 
				// 사용안함/글쓰기방지 레이아웃 노출
				$('.guide_layout').css({'background-color':'rgba(98, 98, 98, 0.38)'}).show();

				// 저장된 내용 불러오기
				var _text = $('[name=p_guide_'+ idx +']').val()
				// 불러온내용 에디터에 적용
				var id = $('.guide_viewer').attr('id');
				if(oEditors.length > 0){
					oEditors.getById[id].exec("SET_IR", [_text]);
				}

			}else if(type=='manual'){

				// 사용안함/글쓰기방지 레이아웃 노출
				$('.guide_layout').hide();

				// 저장된 내용 불러오기
				var _text = $('[name=p_guide_'+ idx +']').val()
				// 불러온내용 에디터에 적용
				var id = $('.guide_viewer').attr('id');
				if(oEditors.length > 0){
					oEditors.getById[id].exec("SET_IR", [_text]);
				}

			}else if(type=='list'){

				// 사용안함/글쓰기방지 레이아웃 노출
				$('.guide_layout').css({'background-color':'rgba(239, 239, 239, 0.38)'}).show();

				$('select[name=p_guide_uid_'+ idx +']').show();
				set_guide_info(idx);

			}
		});

		$(document).on('click', '.tab_menu', function(){

			var idx = $(this).data('idx');

			// 변경전 값 저장
			var sv = $('input[name=guide_tabidx_save]').val();
			$('input[name=guide_tabidx_save]').val(idx);

			var type = $('input[name=p_guide_type_'+ sv +']:checked').val();

			// 변경전 값이 직접입력이라면 직접입력 값 저장
			if(type=='manual'){
				// 불러온내용 에디터에 적용
				var id = $('.guide_viewer').attr('id');
				var _bak = '';
				if(oEditors.length > 0){
					_bak = oEditors.getById[id].getContents();
				}

				$('[name=p_guide_'+ sv +']').val(_bak);
			}

			$('.js_chg_guide_type[data-idx='+idx+']:checked').trigger('change');
		});

		// 이용내역 본문에 삽입하기
		function set_guide_info(idx){
			if(!idx){ alert('잘못된 접근입니다. '); return false; }

			var _val = $('select[name=p_guide_uid_'+ idx +']').val();
			var _text = $('#guide_content_' + _val).val();
			
			if(!_val){ _text = ''; }

			// 불러온내용 에디터에 적용
			var id = $('.guide_viewer').attr('id');
			if(oEditors.length > 0){
				oEditors.getById[id].exec("SET_IR", [_text]);
			}

		}

		// submit할때 마지막 변경내용 적용
		$(document).ready(function(){
			$('.guide_viewer').each(function(){
				$(this).closest('form').on('submit', function(){
					$('.tab_guide').find('li.hit').find('.tab_menu').trigger('click');
				});
			});
		});

	</script>

</div>