	// jquery 브라우저 사이즈 변경시점
	app_width();
	$(document).ready(function() {
		$(window).resize(function() { 
			app_width();// width 값에 따른 상품레이어 처리 - 변경값
		}); 
	});


	// width 값에 따른 상품레이어 처리
	function app_width(){
		var app_width_num = $(window).width();
		if( app_width_num >= 640 ) {
			app_global_width = 640;
			app_global_height = 410;
		}
		else {
			app_global_width = app_width_num;
			app_global_height = app_global_width*(410/640);
			app_global_height2 = app_global_height/1.8;
		}

		$('#app_img_width').html(app_global_width);// 넓이값 지정
		$('#app_img_height').html(app_global_height);// 높이값 지정

		$('.galleryTouch').css({'width':app_global_width+'px' , 'height':app_global_height+'px'});// galleryTouch 지정

		$('#imgs_head').css({'width':(app_global_width * $("#app_img_cnt_head").text())+'px'});// imgs_head 지정
		$('#imgs_head > img').css({'width':app_global_width+'px' , 'height':app_global_height+'px'});// imgs_head 지정

		$('.galleryTouch_grip1').css({'width':app_global_width+'px' , 'height':app_global_height2+'px'});// galleryTouch 지정

		$('#imgs_grip1').css({'width':(app_global_width * $("#app_img_cnt_grip1").text())+'px'});// imgs_head 지정
		$('#imgs_grip1 .pd_list').css({'width':app_global_width+'px' , 'height':app_global_height2+'px'});// imgs_head 지정
		$('#imgs_grip1 .pd_list li').css({'width':app_global_width/3+'px'});// imgs_head 지정


		$('.galleryTouch_grip2').css({'width':app_global_width+'px' , 'height':app_global_height2+'px'});// galleryTouch 지정
		$('#imgs_grip2').css({'width':(app_global_width * $("#app_img_cnt_grip2").text())+'px'});// imgs_head 지정
		$('#imgs_grip2 .pd_list').css({'width':app_global_width+'px' , 'height':app_global_height2+'px'});// imgs_head 지정
		$('#imgs_grip2 .pd_list li').css({'width':app_global_width/3+'px'});// imgs_head 지정


		$('.galleryTouch_grip3').css({'width':app_global_width+'px' , 'height':app_global_height2+'px'});// galleryTouch 지정
		$('#imgs_grip3').css({'width':(app_global_width * $("#app_img_cnt_grip3").text())+'px'});// imgs_head 지정
		$('#imgs_grip3 .pd_list').css({'width':app_global_width+'px' , 'height':app_global_height2+'px'});// imgs_head 지정
		$('#imgs_grip3 .pd_list li').css({'width':app_global_width/3+'px'});// imgs_head 지정


	}