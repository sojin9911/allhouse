		// - jquery validator 경고창 띄우기 (jquery validate 공통) ---
		jQuery.validator.setDefaults({
			onkeyup:false,
			onclick:false,
			onfocusout:false,
			showErrors:function(errorMap, errorList){
				if(errorList.length < 1) return;
				var caption = $(errorList[0].element).attr('name');
				alert(errorList[0].message);
			}
		});
		// - jquery validator 경고창 띄우기 (jquery validate 공통) ---

		$(function() {
			// 이미지 미리보기
			$(document).on('click', '.js_thumb_popup', function(e) {
				e.preventDefault();
				var img = $(this).data('img');
				$('.ui-dialog-content').dialog('close');
				$('.js_dialog').dialog({
					title: '이미지 보기',
					open: function(e, ui) {
						$(e.target).html('<div style="display:table; width:100%; min-height:101px;"><div style="display:table-cell; text-align:center; vertical-align:middle;"><img src="'+img+'" alt="" style="max-width:100%;" /></div></div>');
					},
					close: function(e, ui) {
						$(e.target).html('');
					}
				});
			});

			// Esc키 입력시 미리보기창 닫기
			$(document).on('keyup', 'body', function(e) {
				if(e.keyCode == 27) $('.close').trigger('click');
			});


		  $('img.js_thumb_img').tooltip({
				show: null, hide: null,
				items: 'img.js_thumb_img[data-img]',
				content: function(e) {
					if(!$(this).data('img')) return;
					return '<img src="'+$(this).data('img')+'" alt="" />';
				}
			});



			// 툴팁
			$('.js_tooltip').tooltip({
				show: null, hide: null,
				items: '.js_tooltip',
				content: function(e) {
					if(!$(this).data('content')) return ($(this).attr('alt')?$(this).attr('alt'):$(this).attr('title'));
					return $(this).data('content');
				}
			});

			// 클릭 복사
			$('.js_copy').on('click',function(){ $(this).prop('contentEditable',true).css({'cursor':'text'}); document.execCommand('selectAll',false,null); });
			$('.js_copy').on('blur',function(){ $(this).prop('contentEditable',false).css({'cursor':'pointer'}); $(this).text('<?=$_SERVER[SERVER_ADDR]?>'); });


			// 태그에디터
			$('.js_tag').tagEditor({forceLowercase:false}); // 2019-12-04 SSJ :: 아파벳 대문자 입력 가능하도록 수정

		});

		// 포멧별 데이터 피커 (air-datepicker)
		// 시간만 나오는 피커
		$('.js_pic_onlytime').datepicker({
			timepicker: true,
			onlyTimepicker: true,
			classes: 'only-timepicker'
		});

		// 날짜+시간 피커
		$('.js_pic_time').datepicker({
			timepicker: true,
			autoClose: true,
			language: 'ko',
			dateFormat: 'yyyy-mm-dd',
			timeFormat: 'hh:ii'
		});
		$('.js_pic_time_max_today').datepicker({ // 현재시간까지
			timepicker: true,
			autoClose: true,
			language: 'ko',
			dateFormat: 'yyyy-mm-dd',
			timeFormat: 'hh:ii',
			maxDate: new Date()
		});

		// 일 피커
		$('.js_pic_day, .js_datepic').datepicker({
			timepicker: false,
			autoClose: true,
			language: 'ko',
			dateFormat: 'yyyy-mm-dd'
		});
		$('.js_pic_day_max_today, .js_datepic_max_today').datepicker({ // 오늘까지
			timepicker: false,
			autoClose: true,
			language: 'ko',
			dateFormat: 'yyyy-mm-dd',
			maxDate: new Date()
		});

		// 월 피커
		$('.js_pic_month').datepicker({
			timepicker: false,
			autoClose: true,
			language: 'ko',
			minView: 'months',
			view: 'months',
			dateFormat: 'yyyy-mm'
		});
		$('.js_pic_month_max_today').datepicker({ // 이번달 까지
			timepicker: false,
			autoClose: true,
			language: 'ko',
			minView: 'months',
			view: 'months',
			dateFormat: 'yyyy-mm',
			maxDate: new Date()
		});

		// 년도피커
		$('.js_pic_year').datepicker({
			timepicker: false,
			autoClose: true,
			language: 'ko',
			minView: 'years',
			view: 'years',
			dateFormat: 'yyyy'
		});
		$('.js_pic_year_max_today').datepicker({ // 금년까지
			timepicker: false,
			autoClose: true,
			language: 'ko',
			minView: 'years',
			view: 'years',
			dateFormat: 'yyyy',
			maxDate: new Date()
		});

		// 색상 피커
		$('.js_colorpic').colorpicker({
			strings: '테마별 색상,기본색상,사용자지정,테마별 색상,돌아가기,히스토리,저장된 히스토리가 없습니다.'
		});