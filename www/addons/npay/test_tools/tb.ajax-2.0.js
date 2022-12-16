/*
# * Terrorboy ⓒ
# * http://www.foxrain.me/
# * http://www.terrorboy.net/
 */

/* ----------------------------------------------------------------- */
// 2.X ver
/* ----------------------------------------------------------------- */
(function($) {

	$.TBAjax = function(Url, SendData, Logview) {

		Logview = (Logview)?Logview:'';

		var data = $.ajax({   
				type: "GET",
				url: Url+encodeURI(SendData),
				async: false,
				error:function(request, status, error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
		}).responseText;

		if(Logview == 'true') console.log({'Url':Url, 'SendData':SendData, 'Logview':Logview});

		return data;
	}

	$(document).ready(function() {

		$('[tb-ajax-action]').each(function() {

			var Action = $(this).attr('tb-ajax-action');
			var URL = $(this).attr('tb-ajax-url');
			var Target = $(this).attr('tb-ajax-target');
			var SendData = $(this).attr('tb-ajax-data');
			var LogView = $(this).attr('tb-ajax-log');
			
			if(Action == 'click') {

				$(this).on('click', function(e) {
					e.preventDefault();

					var data = $.ajax({   
							type: "GET",
							url: URL+encodeURI(SendData),
							async: false,
							error:function(request, status, error){
								console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
							}
					}).responseText;

					if($(Target).is('textarea') || $(Target).is('input')) $(Target).val(data);
					else $(Target).html(data);

					if(LogView == 'true') console.log({"TarGet":Target, "URL":URL, "Data":SendData});
				});
			}
			if(Action == 'change') {

				$(this).on('change', function(e) {
					e.preventDefault();

					var data = $.ajax({   
							type: "GET",
							url: URL+encodeURI(SendData),
							async: false,
							error:function(request, status, error){
								console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
							}
					}).responseText;

					if($(Target).is('textarea') || $(Target).is('input')) $(Target).val(data);
					else $(Target).html(data);

					if(LogView == 'true') console.log({"TarGet":Target, "URL":URL, "Data":SendData});
				});
			}
			else if(Action == 'submit') {

				$(this).on(Action, function(e) {
					e.preventDefault();

					var Method = ($(this).attr('method'))?$(this).attr('method'):'';
					URL = $(this).attr('action');

					if(Method == 'get' || Method == 'GET' || Method == '') {

						SendData = $(this).serialize();
						var data = $.ajax({
								type: 'GET',
								url: URL + '?' + encodeURI(SendData),
								async: false,
								error:function(request,status,error){
									console.log('code:'+request.status+'\n'+'message:'+request.responseText+'\n'+'error:'+error);
								}
						}).responseText;
					}
					else {

						SendData = new FormData($(this)[0]);
						var data = $.ajax({
							
								type: "POST",
								url: URL,
								data : SendData,
								cache: false,
								contentType: false,
								processData: false,
								async: false,
								error:function(request,status,error){
									console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
								}
						}).responseText;
					}

					if($(Target).is('textarea') || $(Target).is('input')) $(Target).val(data);
					else $(Target).html(data);

					if(LogView == 'true') console.log({"TarGet":Target, "URL":URL, "Data":SendData});
				});
			}
			else {

				$( document ).ready(function() {

					var data = $.ajax({
							type: "GET",
							url: URL+encodeURI(SendData),
							async: false,
							error:function(request, status, error){
								console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
							}
					}).responseText;

					if($(Target).is('textarea') || $(Target).is('input')) $(Target).val(data);
					else $(Target).html(data);

					if(LogView == 'true') console.log({"TarGet":Target, "URL":URL, "Data":SendData});
				});
			}
		});
	});

})(jQuery);





/* ----------------------------------------------------------------- */
// 1.X Old ver
/* ----------------------------------------------------------------- */
/*
 * TBAjax
 */
// Ajax 통신 인수
function AjaxTB(Target, UserURL, Search){
	this.Target = Target;
	this.UserURL = UserURL;
	this.Search = Search;
	// 같은 타겟 내부에서 공용으로 사용 하는 변수 생성
	window.Target = new Array();
}

// Ajax 전송 함수
AjaxTB.prototype = {
	Sender: function(Logs){
		// 공용 함수에 객체 고유값이 없다면 고유값을 담음
		if(!window.Target[this.Target]){
			window.Target[this.Target] = this.Search;
		}

		var data = $.ajax({   
				type: "POST",
				url: this.UserURL+encodeURI(this.Search),
				async: false,
				error:function(request,status,error){
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
		}).responseText;

		// 콘솔 출력 모드일 경우 출력
		if(Logs == true) {
		
			// 콘솔로그 출력
			console.log({"TarGet":this.Target, "URL":this.UserURL+this.Search, "Data":window.Target[this.Target], "ConsoleLogPut":Logs});
		}

		// 타겟에 결과 값 전송
		$(this.Target).html(data);
	}
}






/*
 * TBAjaxForm
 */
// AjaxForm 전송 함수
function AjaxFormTB(Element) {

	this.Target = $(Element).attr('target');
	this.UserURL = $(Element).attr('action');
	this.Method = $(Element).attr('method');
	this.Search = $(Element).serialize();

	// 같은 타겟 내부에서 공용으로 사용 하는 변수 생성
	window.Target = new Array();
}

// AjaxForm 전송 함수
AjaxFormTB.prototype = {

	Sender: function(Logs){

		if(!window.Target[this.Target]) { // 공용 함수에 객체 고유값이 없다면 고유값을 담음

			window.Target[this.Target] = this.Search;
		}
		

		if(this.Method == "get" || this.Method == "GET") { // Get 전송
			
			var data = $.ajax({   
					type: "GET",
					url: this.UserURL + '?' + encodeURI(this.Search),
					async: false,
					error:function(request,status,error){
						console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					}
			}).responseText;

			// 콘솔 출력 모드일 경우 출력
			if(Logs == true) {
			
				// 콘솔로그 출력
				console.log({"TarGet":this.Target, "URL":this.UserURL+"?"+this.Search, "Data":window.Target[this.Target], "ConsoleLogPut":Logs});
			}
		}
		else { // POST 전송

			var data = $.ajax({
				
					type: "POST",
					url: this.UserURL,
					data : this.Search,
					contentType: "application/x-www-form-urlencoded; charset=UTF-8",
					async: false,
					error:function(request,status,error){
						console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					}
			}).responseText;

			// 콘솔 출력 모드일 경우 출력
			if(Logs == true) {
			
				// 콘솔로그 출력
				console.log({"TarGet":this.Target, "URL":this.UserURL, "Data":window.Target[this.Target], "ConsoleLogPut":Logs});
			}
		}

		// 타겟에 결과 값 전송
		$(this.Target).html(data);

		return false;
	}
}

// 실제 동작 검출
$(function(){

	$("#TBAjaxForm").on("submit", function(){

		var TBAjaxForm = new AjaxFormTB(this);
		TBAjaxForm.Sender();
		return false;
	});
});