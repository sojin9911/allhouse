if(!window.datepicker_ymd || window.datepicker_ymd === undefined) window.datepicker_ymd = 'yyyy-mm-dd';

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
	dateFormat: window.datepicker_ymd,
	timeFormat: 'hh:ii'
});
$('.js_pic_time_max_today').datepicker({ // 현재시간까지
	timepicker: true,
	autoClose: true,
	language: 'ko',
	dateFormat: window.datepicker_ymd,
	timeFormat: 'hh:ii',
	maxDate: new Date()
});

// 일 피커
$('.js_pic_day, .js_datepic').datepicker({
	timepicker: false,
	autoClose: true,
	language: 'ko',
	dateFormat: window.datepicker_ymd
});
$('.js_pic_day_min_today, .js_datepic_min_today').datepicker({ // 오늘전까지
	timepicker: false,
	autoClose: true,
	language: 'ko',
	dateFormat: window.datepicker_ymd,
	minDate: new Date()
});
$('.js_pic_day_max_today, .js_datepic_max_today').datepicker({ // 오늘까지
	timepicker: false,
	autoClose: true,
	language: 'ko',
	dateFormat: window.datepicker_ymd,
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