/*
	# Swiper.js를 이용한 슬라이드 메뉴 :: 2017-11-16 LDD
	구조
		<div class="js_test_menu">
			<ul>
				<li>1</li>
				<li class="hit">2</li>
				<li>3</li>
			</ul>
		</div>
	ele: .js_test_menu (아이디로도 사용 가능)
	active: {
		class: '.active', // 히트(액티브)요소 클래스 또는 아이디(.active or #active)
		speed: 500, // 히트(액티브)요소까지 이동 속도(0이면 효과 없음)
		position: 'left' // left일 경우 무조건 왼쪽이 히트 요소가 나오고 center면 정가운데에 히트 요소가 나옴
	}
	예제:
		var SwiperJSMenu = new SwiperJSMenu('.js_topmenu', {class:'.hit', speed:1000, position:'center'});
		SwiperJSMenu.action();

	active 변경에 따라 슬라이드 위치 변경:
		var SwiperJSMenu = new SwiperJSMenu('.js_topmenu', {class:'.hit', speed:1000, position:'center'});
		SwiperJSMenu.action();
		$('.js_topmenu').find('.hit').removeClass('hit');
		$('.js_topmenu').find('li:eq(3)').addClass('hit');
		SwiperJSMenu.moveUpdate();

	2차가공(슬라이드 위치 변경):
		var SwiperJSMenu = new SwiperJSMenu('.js_topmenu', {class:'.hit', speed:1000, position:'center'});
		SwiperJSMenu.action();
		SwiperJSMenu.swiper().setWrapperTransition(1000);
		SwiperJSMenu.swiper().setWrapperTranslate(-350);
		이런식으로 swiper.js의 옵션으로 2차 가공 가능
*/
(function($) {
	var SwiperJSMenu = function(ele, active) {
		if(typeof(Swiper) != 'function') return {'error':'swiper.js를 먼저 호출 하세요.'};
		this._swiper;
		this.ele = ele;
		this.rand = Math.floor(Math.random()*100000)+1; // 난수 발생
		this.defaultClass = 'js_swiper_menu_'; // 기본 클래스 접두사
		this.itemClass = this.defaultClass+'item_'+this.rand; // 슬라이드 아이템 클래스 추가
		this.wrapClass = this.defaultClass+'wrap_'+this.rand; // ul의 클래스
		this.prevClass = this.defaultClass+'prev_'+this.rand; // prev class
		this.nextClass = this.defaultClass+'next_'+this.rand; // next class
		this.activeClass = this.defaultClass+'active_'+this.rand; // active class
		this._width = 0; // 전체 크기 초기값
		this.active = active;
		if(this.active['hit_class'] == undefined) this.active['hit_class'] = '.active';
		if(this.active['speed'] == undefined) this.active['speed'] = 300;
		if(this.active['position'] == undefined) this.active['position'] = 'center';
		if(this.active['margin_right'] == undefined) this.active['margin_right'] = 1;
		if(this.active['auto_height'] == undefined) this.active['auto_height'] = false;
		return this._swiper;
	}
	SwiperJSMenu.prototype = {
		action: function() {
			var ua = window.navigator.userAgent.toLowerCase();
            var safari = (ua.indexOf('safari') >= 0 && ua.indexOf('chrome') < 0 && ua.indexOf('android') < 0);
			var s = this;
			var _ele = s.ele;
			$(_ele).find('li').addClass(s.itemClass); // ele 내부의 li에 슬라이드를 위한 클래스 추가
			$(_ele).find('ul').addClass(s.wrapClass);
			$.each($(_ele+' li'), function(k, v) {
				s._width += (safari?Math.ceil($(_ele+' li').eq(k).width()*1)+10:Math.ceil($(_ele+' li').eq(k).outerWidth(true)*1));
			}); // 전체 크기값 계산
			$(_ele).find('ul').css({
				'width': s._width+this.active['margin_right']
			}); // 전체 크기를 ul에 적용
			$(_ele).css({
				'overflow': 'hidden'
			}); // ele에 기본 css적용
			if(s._width <= $(_ele).width()) {
				if(s._swiper) {
					s.destroy();
					return;
				}
				else {
					s.destroy();
					return;
				}
			}
			s._swiper = new Swiper(_ele, {
				freeMode: true,
				slidesPerView: 'auto',
				spaceBetween: 0,
				autoHeight: this.active['auto_height'],
				speed: (s.active['speed'] == undefined?100:s.active['speed']),
				wrapperClass: s.wrapClass,
				slideClass: s.itemClass,
				slidePrevClass: s.prevClass,
				slideNextClass: s.nextClass,
				slideActiveClass: s.activeClass
			});
			s._swiper.updateSlidesSize(s._width+this.active['margin_right']);
			s.moveUpdate();
		},
		destroy: function() {
			var s = this;
			var _ele = s.ele;
			var ele = $(_ele);

			// 요소의 기본 클래스 제거
			ele.removeClass('swiper-container-horizontal').removeClass('swiper-container-free-mode').removeClass('swiper-container-android').removeAttr('style');

			// 스와이퍼를 위한 클래스 제거
			if(ele.find('.'+s.wrapClass)) ele.find('.'+s.wrapClass).removeClass(s.wrapClass).removeAttr('style');
			if(ele.find('.'+s.itemClass)) ele.find('.'+s.itemClass).removeClass(s.itemClass);
			if(ele.find('.'+s.wrapClass)) ele.find('.'+s.wrapClass).removeClass(s.wrapClass);
			if(ele.find('.'+s.prevClass)) ele.find('.'+s.prevClass).removeClass(s.prevClass);
			if(ele.find('.'+s.nextClass)) ele.find('.'+s.nextClass).removeClass(s.nextClass);
			if(ele.find('.'+s.activeClass)) ele.find('.'+s.activeClass).removeClass(s.activeClass);

			if(s._swiper) {
				s._swiper.destroy();
				s._swiper = null;
			}
		},
		moveUpdate: function() {
			var s = this;
			var _ele = s.ele;

			// active된 요소로 이동
			if($(_ele).find(s.active['hit_class']).length > 0) {
				var scrollOffset = ($(_ele).find(s.active['hit_class'])[0].offsetLeft *-1)+($(window).width()/2)-($(_ele).find(s.active['hit_class']).width()/2);
				if(scrollOffset > 0) scrollOffset = 0;
				var residual = 0; // active기준으로 잔여 width(active의 절반 크기 포함)
				$.each($(_ele+' li'), function(k, v) { // active기준으로 잔여 width(active의 절반 크기 포함)
					if(k == $(_ele).find(s.active['hit_class']).index()) residual += ($(this).width()/2);
					else if(k > $(_ele).find(s.active['hit_class']).index()) residual += $(this).width();
				});
				if(($(_ele).width()/2) > residual) scrollOffset = ($(_ele+' ul').width()-$(_ele).width())*-1; // active 요소가 스크롤 가능 영역 이상으로 스크롤 되지 않도록
				if(s.active['position'] == 'left') {
					//s._swiper.slideTo($(_ele).find(s.active['hit_class']).index(), s.active['speed']);
					s._swiper.slideTo($(_ele).find(s.active['hit_class']).index(), 0); // 2020-07-20 SSJ :: 아이폰에서 스크롤 위아래로 움직이면 스크롤이 왔다갔다하는 현상 수정
					scrollOffset = $(_ele).find(s.active['hit_class'])[0].offsetLeft*-1;
				}
				else { // active요소를 정중앙정렬 한다.
					if(typeof s._swiper == 'object') {
						//s._swiper.setWrapperTransition(s.active['speed']);
						s._swiper.setWrapperTransition(0); // 2020-07-20 SSJ :: 아이폰에서 스크롤 위아래로 움직이면 스크롤이 왔다갔다하는 현상 수정
						s._swiper.setWrapperTranslate(scrollOffset);
					}
				}
				this.residual_value = residual;
				this.scrollOffset_value = scrollOffset;
			}
			else { return; }
		},
		swiper: function() {
			return this._swiper;
		}
	}
	window.SwiperJSMenu = SwiperJSMenu;
})(jQuery);

/*===========================
Swiper AMD Export
===========================*/
if (typeof(module) !== 'undefined')
{
    module.exports = window.SwiperJSMenu;
}
else if (typeof define === 'function' && define.amd) {
    define([], function () {
        'use strict';
        return window.SwiperJSMenu;
    });
}