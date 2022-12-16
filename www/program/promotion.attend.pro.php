<?php
//// 테스트코드
//$_POST[mode] = 'checkin';
//$_POST[today_date] = '2018-03-17';
//$_POST[uid] = '1';
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


// 비회원 접근 제한
if(is_login() == false) error_msg('로그인이 필요한 서비스 입니다.');


$__code = "OK"; $__msg = "";
function print_json() {
	GLOBAL $__code, $__msg;
	echo json_encode(array("code"=>$__code,"msg"=>$__msg));
	exit;
}

// 출석체크 이벤트 설정 추출
$event_info = get_promotion_attend_event();
$uid = $uid ? $uid : $event_info['atc_uid'];

// 넘어온 uid가 가용 이벤트 uid와 다를 경우 처리
if( $uid <> $event_info['atc_uid'] ) { $__code = "FAIL"; $__msg = "잘못된 정보입니다."; print_json(); }

// 이벤트 자체가 종료되었을 경우 처리
if( $event_info['atc_use'] == 'N' || strtotime($event_info['atc_edate']) < strtotime(date('Y-m-d')) || sizeof($event_info['info']) == 0) { $__code = "FAIL"; $__msg = "출석체크 이벤트가 종료되었습니다.."; print_json(); }

// 연속출석 체크할 ata_uid구하기
$next_uid = _MQ_result(" select atl_addinfo_uid from smart_promotion_attend_log where atl_member = '".get_userid()."' and atl_event = '".$event_info['atc_uid']."' order by atl_date desc limit 1 ");
$next_uid = $next_uid ? $next_uid  : $event_info['info'][0]['ata_uid'];

switch( $mode ) {

	case "checkin":

		// 오늘 출첵 여부
		$today_status = false;
		$today_done = _MQ_result(" select count(*) from smart_promotion_attend_log where atl_member = '".get_userid()."' and atl_date = '".$today_date."' and atl_event = '".$event_info['atc_uid']."' ");
		if( $today_done > 0 ) { $today_status = true; }

		if( $today_status === true ) {
			// 오늘 출첵 했을 경우
			$__code = "FAIL"; $__msg = "이미 출석체크 하셨습니다."; print_json();
		} else {

			// 연속 출첵 확인
			$chk_series = true;
			$chk_date = _MQ_result(" select atl_date from smart_promotion_attend_log where atl_member = '".get_userid()."' and atl_status = 'Y' and atl_addinfo_uid = '". $next_uid ."' and atl_event = '".$event_info['atc_uid']."' order by atl_date desc limit 1 ");
			if( $chk_date ) {
				$eventDiff = abs(strtotime($today_date) - strtotime($chk_date));
				$eventYearDiff = floor($eventDiff / (365*60*60*24));
				$eventMonthDiff = floor(($eventDiff - $eventYearDiff * 365*60*60*24) / (30*60*60*24));
				$eventDayDiff = floor(($eventDiff - $eventYearDiff * 365*60*60*24 - $eventMonthDiff*30*60*60*24)/ (60*60*24));
				if( $eventDayDiff > 1 ) { $chk_series = false; }
			}


			$__code = "OK"; $__msg = "성공적으로 출석체크 되었습니다.";
			// 연속 출첵이 아닐 경우 기존 내용 삭제
			if( $chk_series === false && $event_info['atc_type']=='C') {
				_MQ_noreturn(" update smart_promotion_attend_log set atl_status = 'N' where atl_member = '".get_userid()."'  and atl_event = '".$event_info['atc_uid']."' ");
				$__code = "OK"; $__msg = "성공적으로 출석체크 되었습니다.\n연속으로 출석하지 않아 연속출석 내역은 초기화 됩니다.";

				$next_uid = $event_info['info'][0]['ata_uid'];
			}


			// 출첵 저장
			_MQ_noreturn(" insert into smart_promotion_attend_log set atl_member = '".get_userid()."', atl_date = '".$today_date."', atl_status = 'Y', atl_event = '".$event_info['atc_uid']."', atl_addinfo_uid = '". $next_uid ."', atl_addinfo_days = (select ata_days from smart_promotion_attend_addinfo where ata_uid = '". $next_uid ."'), atl_rdate = now() ");
			$inserted_log = mysql_insert_id();

			// 출첵 횟수 충족시 포인트 지급
			foreach($event_info['info'] as $key=>$info){

				if($next_uid <> $info['ata_uid']) continue;

				// 누적출석 체크
				if($event_info['atc_type']=='T'){
					$chk_date_query = " and atl_member = '".get_userid()."' and atl_status = 'Y' and atl_addinfo_uid = '". $info['ata_uid'] ."' and atl_event = '".$event_info['atc_uid']."' ";
				// 연속출석 체크
				}else{
					$chk_date_query = " and atl_member = '".get_userid()."' and atl_date between '".date('Y-m-d',strtotime(" - ".$info['ata_days']." days ", strtotime($today_date)))."' and '".$today_date."' and atl_status = 'Y' and atl_addinfo_uid = '". $info['ata_uid'] ."' and atl_event = '".$event_info['atc_uid']."' "; // <!-- 하이센스 3.0 출석체크 패치 -->
				}

				$chk_date_cnt = _MQ_result(" select count(*) from smart_promotion_attend_log where 1 ".$chk_date_query);
				_MQ_noreturn(" update smart_promotion_attend_log set atl_addinfo_days_count = '". $chk_date_cnt ."' where atl_uid = '". $inserted_log ."' "); // <!-- 하이센스 3.0 출석체크 패치 -->

				if( $chk_date_cnt == $info['ata_days'] ) {
					// 연속출석조건의 마지막이면 첫번째 출석체크로 변경
					if($event_info['info'][($key+1)]['ata_uid']){
						// 연속 출첵이 확인되었을 경우 해당 로그 상태 변화 (atl_addinfo_uid 다음체크할 출석체크로 uid변경)
						$next_event = $event_info['info'][($key+1)]['ata_uid']; // 다음
						$is_first = false; // 첫번째 이벤트로 돌아가는지 여부
					}else{
						// 연속 출첵이 확인되었을 경우 해당 로그 상태 변화 (atl_addinfo_uid 다음체크할 출석체크로 uid변경)
						$next_event = $event_info['info'][0]['ata_uid']; // 다음

						// 혜택 중복 여부 체크
						if($event_info['atc_duplicate']=='Y'){
							$is_first = true; // 첫번째 이벤트로 돌아감
						}else{
							$is_first = false; // 첫번째 이벤트로 돌아가는지 여부
						}
					}

					_MQ_noreturn(" update smart_promotion_attend_log set atl_addinfo_uid = '". $next_event ."' ". ($is_first ? " ,atl_status = 'N' " : null) ." where 1 ".$chk_date_query);
					$_app_msg = '성공적으로 출석체크 되었습니다.';


					// 2019-11-08 SSJ :: 연속참여 - 한번만 혜택 획득일 경우 이미 획득한 보상이 있는지 체크
					$app_log_chk = true;
					if($event_info['atc_type']=='C' && $event_info['atc_duplicate']=='N'){
						$res_log_chk = _MQ(" select count(*) as cnt from smart_promotion_attend_log where atl_event = '".$event_info['atc_uid']."' and atl_member = '".get_userid()."' and atl_success = 'Y' and atl_addinfo_days = '". $info['ata_days'] ."' ");
						if($res_log_chk['cnt'] > 0) $app_log_chk = false;
					}

					if($app_log_chk){
						_MQ_noreturn(" update smart_promotion_attend_log set atl_success = 'Y' where atl_uid = '". $inserted_log ."' "); // 달성완료 표시

						// 적립금 지급
						if( $info['ata_point'] > 0 ) {
							if($event_info['atc_type']=='T'){
								$_app_msg .= "\n".number_format($info['ata_days']).'회 출석하여 적립금 '.number_format($info['ata_point']).' 포인트가 ' . ($info['ata_point_delay']>0?number_format($info['ata_point_delay']).'일 후 지급됩니다.':'지급되었습니다.');
								$_app_point_title = '출석체크 이벤트 적립금 적용('. $event_info['atc_title'] .' / 누적출석 '. $info['ata_days'] .'회)';
							// 연속출석 체크
							}else{
								$_app_msg .= "\n" . '연속으로 '.number_format($info['ata_days']).'회 출석하여 적립금 '.number_format($info['ata_point']).' 포인트가 ' . ($info['ata_point_delay']>0?number_format($info['ata_point_delay']).'일 후 지급됩니다.':'지급되었습니다.');
								$_app_point_title = '출석체크 이벤트 적립금 적용('. $event_info['atc_title'] .' / 연속출석 '. $info['ata_days'] .'회)';
							}

							shop_pointlog_insert(get_userid(), $_app_point_title, $info['ata_point'], 'N', $info['ata_point_delay']);
							_MQ_noreturn(" update smart_promotion_attend_log set atl_point = '". $info['ata_point'] ."' where atl_uid = '". $inserted_log ."' ");
						}
						// 쿠폰 지급
						if( $info['ata_coupon'] > 0 ) {
							// 쿠폰지급 프로세스 추가
							$couponSetData = _MQ(" select * from smart_individual_coupon_set where ocs_view = 'Y' and ocs_issued_type = 'auto' and ocs_issued_type_auto = '5' and ocs_uid = '". $info['ata_coupon'] ."' ");
							// 발급가능한 쿠폰일경우에만 쿠폰발급
							$couponSetIssuedChk = couponSetIssuedChk($couponSetData,$mem_info);
							if($couponSetData['ocs_uid'] == $info['ata_coupon'] && $couponSetIssuedChk == true){
								if($info['ata_coupon_delay']>0){
									// 발급대기 목록에 추가
									_MQ_noreturn(" insert into smart_promotion_attend_coupon_ready set acr_status='N', acr_inid='".get_userid()."', acr_ocsuid='". $info['ata_coupon'] ."', acr_atluid='". $inserted_log ."', acr_idate='". date('Y-m-d', strtotime('+'. $info['ata_coupon_delay'] .'days' , strtotime($today_date))) ."', acr_rdate=now() ");
								}else{
									give_coupon(get_userid(), $couponSetData);
								}
								$coupon_title = '['. $arrCouponSet['ocs_type'][$couponSetData['ocs_type']] .'] ' . trim(stripslashes($couponSetData['ocs_name'])) . ' - ' . printCouponSetBoon($couponSetData);
								_MQ_noreturn(" update smart_promotion_attend_log set atl_coupon = '". $info['ata_coupon'] ."', atl_coupon_name = '". $coupon_title ."' where atl_uid = '". $inserted_log ."' ");

								// 연속출석 체크
								if($event_info['atc_type']=='T'){
									$_app_msg .= "\n".number_format($info['ata_days']).'회 출석하여 \"할인쿠폰('. $couponSetData['ocs_name'] .')\"이 ' . ($info['ata_coupon_delay']>0?number_format($info['ata_coupon_delay']).'일 후 지급됩니다.':'지급되었습니다.');
								}else{
									$_app_msg .= "\n".'연속으로 '.number_format($info['ata_days']).'회 출석하여 \"할인쿠폰('. $couponSetData['ocs_name'] .')\"이 ' . ($info['ata_coupon_delay']>0?number_format($info['ata_coupon_delay']).'일 후 지급됩니다.':'지급되었습니다.');
								}
							}
						}
					}
					$__code = "OK"; $__msg = $_app_msg;
				}


				break;
			}

		}

	break;

}

print_json();


actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행