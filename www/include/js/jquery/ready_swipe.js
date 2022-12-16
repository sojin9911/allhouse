
	$(function() {
		var speed=500,

		// - 상단배너 초기값 설정 ---
		currentImg_head=0,
		imgs_head = $("#imgs_head");
		maxImages_head=$("#app_img_cnt_head").text();
		// - 하단배너 초기값 설정 ---
		currentImg_grip1=0,
		imgs_grip1 = $("#imgs_grip1");
		maxImages_grip1=$("#app_img_cnt_grip1").text();
		// - 하단배너 초기값 설정 ---
		currentImg_grip2=0,
		imgs_grip2 = $("#imgs_grip2");
		maxImages_grip2=$("#app_img_cnt_grip2").text();
		// - 하단배너 초기값 설정 ---
		currentImg_grip3=0,
		imgs_grip3 = $("#imgs_grip3");
		maxImages_grip3=$("#app_img_cnt_grip3").text();

// -- 헤더용품 처리 -------------------------------------------
		//Init touch swipe
		imgs_head.swipe( {
			triggerOnTouchEnd : true,
			swipeStatus : swipeStatus_head,
			allowPageScroll:"vertical",
	        click:function(event, target) {

				// 터치한 타켓의 onclick 함수를 실행한다.
				target.onclick();
	        }
		});

		/**
		* Catch each phase of the swipe.
		* move : we drag the div.
		* cancel : we animate back to where we were
		* end : we animate to the next image
		*/
		function swipeStatus_head(event, phase, direction, distance, fingers){
			//If we are moving before swipe, and we are going L or R, then manually drag the images
			if( phase=="move" && (direction=="left" || direction=="right") ){
				var duration=0;

				if (direction == "left")
					scrollImages_head(($("#app_img_width").text() * currentImg_head) + distance, duration);

				else if (direction == "right")
					scrollImages_head(($("#app_img_width").text() * currentImg_head) - distance, duration);
			}

			//Else, cancel means snap back to the begining
			else if ( phase == "cancel"){
				scrollImages_head($("#app_img_width").text() * currentImg_head, speed);
			}

			//Else end means the swipe was completed, so move to the next image
			else if ( phase =="end" ){
				if (direction == "right")
					previousImage_head()
				else if (direction == "left")
					nextImage_head()
			}
		}

		function previousImage_head(){
			currentImg_head = Math.max(currentImg_head-1, 0);
			scrollImages_head( $("#app_img_width").text() * currentImg_head, speed);
		}

		function nextImage_head(){
			currentImg_head = Math.min(currentImg_head+1, maxImages_head-1);
			scrollImages_head( $("#app_img_width").text() * currentImg_head, speed);
		}

		/**
		 * Manually update the position of the imgs on drag
		 */
		function scrollImages_head(distance, duration){
			head_change_pageing_dot(currentImg_head);
			imgs_head.css("-webkit-transition-duration", (duration/1000).toFixed(1) + "s");
			//inverse the number we set in the css
			var value = (distance<0 ? "" : "-") + Math.abs(distance).toString();
			imgs_head.css("-webkit-transform", "translate3d("+value +"px,0px,0px)");
			$("#test_head").text(imgs_head.css("-webkit-transform"));

		}
// -- 헤더용품 처리 -------------------------------------------
// --하단배너 처리 -------------------------------------------
		//Init touch swipe
		imgs_grip1.swipe( {
			triggerOnTouchEnd : true,
			swipeStatus : swipeStatus_grip1,
			allowPageScroll:"vertical",
	        click:function(event, target) {
				target.onclick();
	        }
		});

		/**
		* Catch each phase of the swipe.
		* move : we drag the div.
		* cancel : we animate back to where we were
		* end : we animate to the next image
		*/
		function swipeStatus_grip1(event, phase, direction, distance, fingers){
			//If we are moving before swipe, and we are going L or R, then manually drag the images
			if( phase=="move" && (direction=="left" || direction=="right") ){
				var duration=0;

				if (direction == "left")
					scrollImages_grip1(($("#app_img_width").text() * currentImg_grip1) + distance, duration);

				else if (direction == "right")
					scrollImages_grip1(($("#app_img_width").text() * currentImg_grip1) - distance, duration);
			}

			//Else, cancel means snap back to the begining
			else if ( phase == "cancel"){
				scrollImages_grip1($("#app_img_width").text() * currentImg_grip1, speed);
			}

			//Else end means the swipe was completed, so move to the next image
			else if ( phase =="end" ){
				if (direction == "right")
					previousImage_grip1()
				else if (direction == "left")
					nextImage_grip1()
			}
		}

		function previousImage_grip1(){
			currentImg_grip1 = Math.max(currentImg_grip1-1, 0);
			scrollImages_grip1( $("#app_img_width").text() * currentImg_grip1, speed);
		}

		function nextImage_grip1(){
			currentImg_grip1 = Math.min(currentImg_grip1+1, maxImages_grip1-1);
			scrollImages_grip1( $("#app_img_width").text() * currentImg_grip1, speed);
		}

		/**
		 * Manually update the position of the imgs on drag
		 */
		function scrollImages_grip1(distance, duration){
			grip1_change_pageing_dot(currentImg_grip1);
			imgs_grip1.css("-webkit-transition-duration", (duration/1000).toFixed(1) + "s");
			//inverse the number we set in the css
			var value = (distance<0 ? "" : "-") + Math.abs(distance).toString();
			imgs_grip1.css("-webkit-transform", "translate3d("+value +"px,0px,0px)");
		}
// --하단배너 처리 -------------------------------------------
// --하단배너 처리 -------------------------------------------
		//Init touch swipe
		imgs_grip2.swipe( {
			triggerOnTouchEnd : true,
			swipeStatus : swipeStatus_grip2,
			allowPageScroll:"vertical",
	        click:function(event, target) {
				target.onclick();
	        }
		});

		/**
		* Catch each phase of the swipe.
		* move : we drag the div.
		* cancel : we animate back to where we were
		* end : we animate to the next image
		*/
		function swipeStatus_grip2(event, phase, direction, distance, fingers){
			//If we are moving before swipe, and we are going L or R, then manually drag the images
			if( phase=="move" && (direction=="left" || direction=="right") ){
				var duration=0;

				if (direction == "left")
					scrollImages_grip2(($("#app_img_width").text() * currentImg_grip2) + distance, duration);

				else if (direction == "right")
					scrollImages_grip2(($("#app_img_width").text() * currentImg_grip2) - distance, duration);
			}

			//Else, cancel means snap back to the begining
			else if ( phase == "cancel"){
				scrollImages_grip2($("#app_img_width").text() * currentImg_grip2, speed);
			}

			//Else end means the swipe was completed, so move to the next image
			else if ( phase =="end" ){
				if (direction == "right")
					previousImage_grip2()
				else if (direction == "left")
					nextImage_grip2()
			}
		}

		function previousImage_grip2(){
			currentImg_grip2 = Math.max(currentImg_grip2-1, 0);
			scrollImages_grip2( $("#app_img_width").text() * currentImg_grip2, speed);
		}

		function nextImage_grip2(){
			currentImg_grip2 = Math.min(currentImg_grip2+1, maxImages_grip2-1);
			scrollImages_grip2( $("#app_img_width").text() * currentImg_grip2, speed);
		}

		/**
		 * Manually update the position of the imgs on drag
		 */
		function scrollImages_grip2(distance, duration){
			grip2_change_pageing_dot(currentImg_grip2);
			imgs_grip2.css("-webkit-transition-duration", (duration/1000).toFixed(1) + "s");
			//inverse the number we set in the css
			var value = (distance<0 ? "" : "-") + Math.abs(distance).toString();
			imgs_grip2.css("-webkit-transform", "translate3d("+value +"px,0px,0px)");
		}
// --하단배너 처리 -------------------------------------------
// --하단배너 처리 -------------------------------------------
		//Init touch swipe
		imgs_grip3.swipe( {
			triggerOnTouchEnd : true,
			swipeStatus : swipeStatus_grip3,
			allowPageScroll:"vertical",
	        click:function(event, target) {
				target.onclick();
	        }
		});

		/**
		* Catch each phase of the swipe.
		* move : we drag the div.
		* cancel : we animate back to where we were
		* end : we animate to the next image
		*/
		function swipeStatus_grip3(event, phase, direction, distance, fingers){
			//If we are moving before swipe, and we are going L or R, then manually drag the images
			if( phase=="move" && (direction=="left" || direction=="right") ){
				var duration=0;

				if (direction == "left")
					scrollImages_grip3(($("#app_img_width").text() * currentImg_grip3) + distance, duration);

				else if (direction == "right")
					scrollImages_grip3(($("#app_img_width").text() * currentImg_grip3) - distance, duration);
			}

			//Else, cancel means snap back to the begining
			else if ( phase == "cancel"){
				scrollImages_grip3($("#app_img_width").text() * currentImg_grip3, speed);
			}

			//Else end means the swipe was completed, so move to the next image
			else if ( phase =="end" ){
				if (direction == "right")
					previousImage_grip3()
				else if (direction == "left")
					nextImage_grip3()
			}
		}

		function previousImage_grip3(){
			currentImg_grip3 = Math.max(currentImg_grip3-1, 0);
			scrollImages_grip3( $("#app_img_width").text() * currentImg_grip3, speed);
		}

		function nextImage_grip3(){
			currentImg_grip3 = Math.min(currentImg_grip3+1, maxImages_grip3-1);
			scrollImages_grip3( $("#app_img_width").text() * currentImg_grip3, speed);
		}

		/**
		 * Manually update the position of the imgs on drag
		 */
		function scrollImages_grip3(distance, duration){
			grip3_change_pageing_dot(currentImg_grip3);
			imgs_grip3.css("-webkit-transition-duration", (duration/1000).toFixed(1) + "s");
			//inverse the number we set in the css
			var value = (distance<0 ? "" : "-") + Math.abs(distance).toString();
			imgs_grip3.css("-webkit-transform", "translate3d("+value +"px,0px,0px)");
		}
// --하단배너 처리 -------------------------------------------

	});
