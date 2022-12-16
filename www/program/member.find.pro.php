<?php
# ------------------------------> DEV 변경 하세요. <---------------------- 내부 html은 스킨 폴더에 파일 하나 만들어 처리 하는 형태로 변경
# 아이디 비밀번호 찾기
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행

// - 모드별 처리 ---
switch($_mode) {

	// 아이디 찾기
	case 'find_id':

		// 필수 입력 체크
		if(empty($_name) || $_name == '') die(json_encode(array('msg'=>'이름을 입력해주세요'))); // 실패
		if(empty($_tel) || $_tel == '') die(json_encode(array('msg'=>'휴대폰 번호를 입력해주세요'))); // 실패

		// 데이터 조회
		$_tel = str_replace('-', '', tel_format($_tel)); // 휴대전화 포맷을 맞춰주고 인젝션을 방지한다.
		$r = _MQ(" select in_id from `smart_individual` where in_name = '{$_name}' and replace(in_tel2, '-', '') = '{$_tel}' ");

		// 결과 반환
		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		if(empty($r['in_id'])) die(json_encode(array('result'=>'error', 'msg'=>'정보를 찾을 수 없습니다. 입력 정보를 확인해 주시기 바랍니다.'))); // 실패
		else die(json_encode(array('result'=>'success', 'id'=>LastCut($r['in_id'], 3, '*')))); // 성공
	break;


	// 비밀번호 찾기
	case 'find_pw':

		// 필수 입력 체크
		$n_type = $_type;
		if(empty($_id) || $_id == '') die(json_encode(array('msg'=>'아이디를 입력해주세요'))); // 실패
		if($_type != 'email') { // 휴대폰으로 찾기

			if(empty($_tel) || $_tel == '') die(json_encode(array('msg'=>'휴대폰 번호를 입력해주세요'))); // 실패
			$_tel = str_replace('-', '', tel_format($_tel)); // 휴대전화 포맷을 맞춰주고 인젝션을 방지한다.

			// SMS설정이 정상적이고 발송가능한 금액이 있다면 인증수단 추가
			$SMSUser = onedaynet_sms_user();
			if($SMSUser['code'] != 'U00' || $SMSUser['data'] <= 10) {
				$smsInfo = _MQ(" select * from smart_sms_set where ss_uid = 'temp_password' limit 1 ");
				if($smsInfo['ss_status'] != 'Y') $n_type = 'email'; // SMS 발송이 불가능한 상태에서 발송 구분을 이메일로 변경
			}
		}
		else { // 이메일로 찾기

			if(empty($_email) || $_email == '') die(json_encode(array('msg'=>'이메일 주소를 입력해주세요'))); // 실패
		}


		// 데이터 조회
		$r = _MQ(" select in_id, in_name, in_email, in_tel2 from `smart_individual` where in_id = '{$_id}' ".($_type != 'email'?" and replace(in_tel2, '-', '') = '{$_tel}' ":" and in_email = '{$_email}' "));
		if(empty($r['in_id'])) die(json_encode(array('result'=>'error', 'msg'=>'정보를 찾을 수 없습니다. 입력 정보를 확인해 주시기 바랍니다.'))); // 실패


		// 반환 데이터 설정
		$send = $re_email = $re_hp = '';
		$re_email_arr = $re_hp_arr = array();
		if(isset($r['in_email']) && $r['in_email'] != '') {
			$re_email_arr = explode('@', $r['in_email']);
			$re_email = LastCut($re_email_arr[0], 3, '*').'@'.$re_email_arr[1];
		}
		if(isset($r['in_tel2']) && $r['in_tel2'] != '') {
			$re_hp_arr = explode('-', tel_format($r['in_tel2']));
			$re_hp = $re_hp_arr[0].'-****-'.$re_hp_arr[2];
		}
		$send = ($_type != 'email'?$re_hp:$re_email);
		if($_type != $n_type) $send = $re_email; // SMS 발송이 불가능한 상태에서 발송 구분을 이메일로 변경

		// 임시 비밀번호 발급 및 수정
		$tmp_pw = '';
		for($i=0; $i<6; $i++ ){
			if(rand(1,2) == 1)$tmp_pw .= rand(0,9); // 숫자
			else $tmp_pw .= chr(rand(97,122)); // 영문
		}
		_MQ_noreturn(" update smart_individual set in_pw = password('{$tmp_pw}') where in_id = '{$r['in_id']}'");


		// 임시비밀번호 발송
		$_name = $r['in_name']; // 회원이름
		// $tmp_pw // 임시비밀번호
		if($n_type != 'email') { // 임시비밀번호 SMS발송

			$arr_send = array();

			$arr_replace = array(
				"{사이트명}" => $siteInfo['s_adshop'] ,
				"{회원명}" => $_name ,
				"{임시비밀번호}" => $tmp_pw ,
			);

			// 사용자 발송
			$UserSMSInfo = _MQ("select * from smart_sms_set where ss_uid = 'temp_password' limit 1");
			if($UserSMSInfo['ss_status'] == 'Y') {

				$UserTxet = str_replace(array_keys($arr_replace),array_values($arr_replace), $UserSMSInfo['ss_text']);
				//$UserTxet = $UserSMSInfo['ss_text'];
				//$UserTxet = str_replace('{사이트명}', $siteInfo['s_adshop'], $UserTxet);
				//$UserTxet = str_replace('{회원명}', $_name, $UserTxet);
				//$UserTxet = str_replace('{임시비밀번호}', $tmp_pw, $UserTxet);

				// 문자/알림톡 통합 발송
				//$arr_send[] = array('receive_num'=>$r['in_tel2'], 'send_num'=>$siteInfo['s_glbtel'], 'msg'=>$UserTxet, 'title'=>$UserSMSInfo['ss_title'], 'image'=>$UserSMSInfo['ss_file'], 'reserve_time'=>'');
				$arr_send[] = array_merge(array('receive_num'=>$r['in_tel2'], 'send_num'=>$siteInfo['s_glbtel'], 'msg'=>$UserTxet, 'title'=>$UserSMSInfo['ss_title'], 'image'=>$UserSMSInfo['ss_file'], 'reserve_time'=>'') , smsinfo_array($UserSMSInfo , $arr_replace));
			}

			// 관리자 발송
			$AdminSMSInfo = _MQ("select * from smart_sms_set where ss_uid = 'admin_temp_password' limit 1");
			if($AdminSMSInfo['ss_status'] == 'Y') {
				$AdminTxet = str_replace(array_keys($arr_replace),array_values($arr_replace), $AdminSMSInfo['ss_text']);
				//$AdminTxet = $AdminSMSInfo['ss_text'];
				//$AdminTxet = str_replace('{사이트명}', $siteInfo['s_adshop'], $AdminTxet);
				//$AdminTxet = str_replace('{회원명}', $_name, $AdminTxet);
				//$AdminTxet = str_replace('{임시비밀번호}', $tmp_pw, $AdminTxet);

				// 문자/알림톡 통합 발송
				//$arr_send[] = array('receive_num'=>$siteInfo['s_glbmanagerhp'], 'send_num'=>$siteInfo['s_glbtel'], 'msg'=>$AdminTxet, 'title'=>$AdminSMSInfo['ss_title'], 'image'=>$AdminSMSInfo['ss_file'], 'reserve_time'=>'' );
				$arr_send[] = array_merge(array('receive_num'=>$siteInfo['s_glbmanagerhp'], 'send_num'=>$siteInfo['s_glbtel'], 'msg'=>$AdminTxet, 'title'=>$AdminSMSInfo['ss_title'], 'image'=>$AdminSMSInfo['ss_file'], 'reserve_time'=>'' ) , smsinfo_array($AdminSMSInfo , $arr_replace));

			}

			// 문자발송
			//onedaynet_sms_multisend($arr_send);
			// 문자/알림톡 통합 발송
			onedaynet_alimtalk_multisend($arr_send);
		}
		else { // 임시비밀번호 메일발송

			$_title = "[{$siteInfo['s_adshop']}] 비밀번호 찾기";
			include_once(OD_PROGRAM_ROOT.'/mail/member.temp_password.php'); // 임시비밀번호 발급 메일 양식 (반환: $mailling_content)
			$_content = get_mail_content($mailling_content);
			mailer($r['in_email'], $_title, $_content);
		}

		// 결과 반환
		actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행
		die(json_encode(array('result'=>'success', 'id'=>LastCut($r['in_id'], 3, '*'), 'send'=>$send, 'alert'=>($_type != $n_type?'문자 메시지를 발송할 수 없어 이메일로 임시 비밀번호가 발급되었습니다.':'')))); // 성공
	break;
}
// - 모드별 처리 ---