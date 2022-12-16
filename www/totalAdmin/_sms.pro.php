<?PHP

	include_once("inc.php");

	if(!strcmp($form,"sendform")) {

		$tran_callback			= $send_from_num ; //$send_from_num1 ."-". $send_from_num2 ."-". $send_from_num3;
		$tran_msg				= $message;
		$tran_title				= $send_title;

		// JJC : 핸드폰번호 중복제거 : 2020-10-23
		$arr_tmp1 = array_filter(explode("/",$send_list_serial));
		$arr_tmp2 = array();
		if(sizeof($arr_tmp1) > 0 ) {foreach($arr_tmp1 as $k=>$v){$arr_tmp2[trim($v)]++;}}
		$arr = array_keys($arr_tmp2);
		// JJC : 핸드폰번호 중복제거 : 2020-10-23

		$arr_send = array();

		if(sizeof($arr) > 300) {
			error_alt("한꺼번에 300개를 초과하여 발송할 수 없습니다.");
		}

		if($_reserv_chk == "Y") {	// 예약발송
			$app_sque = "${_reserv_y}-${_reserv_m}-${_reserv_d} ${_reserv_h}:${_reserv_i}:00";
		} else {
			$app_sque = "";
		}

		// MMS 이미지 업로드
		$app_src = $_SERVER[DOCUMENT_ROOT].'/upfiles';
		if($_FILES['a_file'][error] > 0 && $_FILES['a_file'][name] ){
			switch($_FILES['a_file'][error]){
				case "1":error_alt("업로드한 파일 크기가 2Mb 이상입니다."); break;//업로드한 파일 크기가 PHP 'upload_max_filesize' 설정값보다 클 경우
				case "2":error_alt("업로드한 파일 크기가 너무 큽니다.\\n60KB 이하로 등록하세요."); break;// UPLOAD_ERR_FORM_SIZE, 업로드한 파일 크기가 HTML 폼에 명시한 MAX_FILE_SIZE 값보다 클 경우
				case "3":error_alt("파일 전송에 오류가 있습니다."); break;//파일중 일부만 전송된 경우
				case "4":error_alt("파일이 전송되지 않습니다."); break;//파일도 전송되지 않았을 경우
			}
			exit;
		}

		if($_FILES['a_file'][size]> 0){
			if($_FILES['a_file'][size] > 60*1024) { error_alt("업로드한 파일 크기가 너무 큽니다.\\n60KB 이하로 등록하세요."); exit; }
			else {
				$ex_image_name = explode(".",$_FILES['a_file'][name]);
				$app_ext = strtolower($ex_image_name[(sizeof($ex_image_name)-1)]); // 확장자
				if( !preg_match("/gif|jpg|bmp|png/i" , $app_ext) ) { error_msg("등록가능한 이미지가 아닙니다."); }
				$img_name = sprintf("%u" , crc32($_FILES['a_file'][name] . time() . rand())) . "." . $app_ext ;
				@copy($_FILES['a_file'][tmp_name] , $app_src . "/" . $img_name);
			}
		}
		// MMS 이미지 업로드 끝

		foreach($arr as $k=>$v){
			$arr_send[] = array('receive_num'=> $v , 'send_num'=> $tran_callback , 'msg'=> $tran_msg , 'reserve_time'=>$app_sque, 'title'=> $tran_title, 'image'=>$img_name, 'image_del'=>'Y' );
		}

		$result = onedaynet_sms_multisend($arr_send);
		# 결과 반환 설정&출력
		$suc = 0;
		$fal = 0;
		foreach($result as $k=>$v) {

		    if($v['code'] == 'S00') $suc++;
		    else $fal++;
		}
		if(count($result) == $suc) error_frame_loc_msg('_sms.form.php', number_format(count($result)).'개의 메시지가 발송되었습니다.'); // 전부성공
		else if(count($result) == $fal) error_frame_loc_msg('_sms.log.php', number_format(count($result)).'개의 메시지 발송이 실패 하였습니다.'); // 전부실패
		else error_frame_loc_msg('_sms.form.php', number_format(count($result)).'개의 메시지 중 '.number_format($suc).'개의 발송 완료되었습니다. [성공: '.number_format($suc).', 실패: '.number_format($fal).']'); // 부분실패


	}


?>