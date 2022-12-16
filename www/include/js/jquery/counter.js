			// endDttm - YmdHis 형태 적용
			function ts_remainDate(endDttm , app_id, startDttm){

				var startDttm = startDttm;
				var endDate = new Date(endDttm.substring(0,4) , endDttm.substring(4,6)-1 , endDttm.substring(6,8) , endDttm.substring(8,10) , endDttm.substring(10,12) , endDttm.substring(12,14));
				var startDate = new Date(startDttm.substring(0,4) , startDttm.substring(4,6)-1 , startDttm.substring(6,8) , startDttm.substring(8,10) , startDttm.substring(10,12) , startDttm.substring(12,14));
				periodDate = (endDate - startDate)/1000;
				if(endDate > startDate){
					ts_remainTime(periodDate , app_id);
				}
				else {
					$('.' + app_id).html("<span class='box_red'>0</span><span class='box_red'>0</span><span class='tx'>일</span><span class='box_black'>0</span><span class='box_black'>0</span><span class='tx'>:</span><span class='box_black'>0</span><span class='box_black'>0</span><span class='tx'>:</span><span class='box_black'>0</span><span class='box_black'>0</span>");
				}
			}

			// endDttm - YmdHis 형태 적용
			function ts_remainTime(periodDate , app_id){
				var strtonum = new Array();
				strtonum[0] = "0";
				strtonum[1] = "1";
				strtonum[2] = "2";
				strtonum[3] = "3";
				strtonum[4] = "4";
				strtonum[5] = "5";
				strtonum[6] = "6";
				strtonum[7] = "7";
				strtonum[8] = "8";
				strtonum[9] = "9";

				var day  = Math.floor(periodDate / 86400);
				var day_10 = Math.floor(day/10);
				var day_1 = day - day_10 * 10;
				var hour = Math.floor((periodDate - day * 86400 )/3600);
		//		var hour = Math.floor(periodDate / 3600);
				var hour_10 = Math.floor(hour/10);
				var hour_1 = hour - hour_10 * 10;
				var min  = Math.floor((periodDate - day * 86400 - hour * 3600)/60);
		//		var min  = Math.floor((periodDate - hour * 3600)/60);
				var min_10 = Math.floor(min/10);
				var min_1 = min - min_10 * 10;
				var sec  = Math.floor(periodDate - day * 86400 - hour * 3600 - min * 60);
		//		var sec  = Math.floor(periodDate - hour * 3600 - min * 60);
				var sec_10 = Math.floor(sec/10);
				var sec_1 = sec - sec_10 * 10;
				var str_time = "";
				if(day > 0 ) {
					str_time += "<span class='box_red'>"+strtonum[day_10]+"</span><span class='box_red'>"+strtonum[day_1]+"</span>";
				}
				else {
					str_time += "<span class='box_red'>0</span><span class='box_red'>0</span>";
				}
				str_time += "<span class='tx'>일</span>";

		//		if(day > 0 || (day == 0 && hour > 0)) {
				if( hour > 0 ) {
					str_time += "<span class='box_black'>"+strtonum[hour_10]+"</span><span class='box_black'>"+strtonum[hour_1]+"</span>";
				}
				else {
					str_time += "<span class='box_black'>0</span><span class='box_black'>0</span>";
				}
				str_time += "<span class='tx'>:</span>";

		//		if(day > 0 || (day == 0 && hour > 0) || (day == 0 && hour == 0 && min > 0)) {
				if( hour > 0 || (hour == 0 && min > 0)) {

					str_time += "<span class='box_black'>"+strtonum[min_10]+"</span><span class='box_black'>"+strtonum[min_1]+"</span>";
				}
				else {
					str_time += "<span class='box_black'>0</span><span class='box_black'>0</span>";
				}
				str_time += "<span class='tx'>:</span>";

		//		if(day > 0 || (day == 0 && hour > 0) || (day == 0 && hour == 0 && min > 0) || (day == 0 && hour == 0 && min == 0 && sec > 0)) {
				if( hour > 0 || ( hour == 0 && min > 0) || (hour == 0 && min == 0 && sec > 0)) {
					str_time += "<span class='box_black'>"+strtonum[sec_10]+"</span><span class='box_black'>"+strtonum[sec_1]+"</span>";
				}
				else {
					str_time += "<span class='box_black'>0</span><span class='box_black'>0</span>";
				}
				str_time += "";

				$('.' + app_id).html(str_time);
				periodDate = periodDate -1;
				setTimeout(function(){ts_remainTime(periodDate , app_id);}, 1000);
				return;
			}