<?PHP

	// 메모리 무제한 풀기
	ini_set('memory_limit','-1');

	include_once("inc.php");

	switch( $_mode ){


		// 매2년마다 수신동의 발송설정
		case "setup" :
			$sque = " update smart_setup set  s_2year_opt_use = '".$_2year_opt_use."' where s_uid = 1";
			_MQ_noreturn($sque);
			error_loc("/totalAdmin/_addons.php?_menuType=smsEmail&pass_menu=2yearOpt/_2year_opt.form");
			break;


		// 메일설정
		case "mailsetup" :
			$sque = " update smart_setup set  s_2year_opt_title = '". addslashes($_2year_opt_title) ."' , s_2year_opt_content_top = '". addslashes($_2year_opt_content_top) ."' where s_uid = 1";
			_MQ_noreturn($sque);
			error_loc("/totalAdmin/_addons.popup.php?_mode=2yearMail");
			break;

		// 메일발송 - iframe 이미로 넘길 필요 없음.
		case "send" :
			$_type = $_type ? $_type : "email"; // 타입없을 경우 기본 이메일 발송

			if( $siteInfo['s_2year_opt_use'] == "Y" ) {

				// JJC : 수정 : 2021-05-17
				//	$mr_row = _MQ_assoc("
				//		select
				//			ol.ol_uid ,
				//			m.in_tel ,m.in_tel2 , m.in_id , m.in_name , m.in_email
				//		from smart_2year_opt_log as ol
				//		inner join smart_individual as m on (m.in_id = ol.ol_mid and m.in_userlevel != '9')
				//		where
				//			ol.ol_status='N'
				//		order by ol.ol_uid asc
				//		limit 0, 10
				//	");
				$mr_row = _MQ_assoc("
					select
						ol.ol_uid ,
						m.in_tel ,m.in_tel2 , m.in_id , m.in_name , m.in_email
					from smart_2year_opt_log as ol
					inner join smart_individual as m on (in_id = ol.ol_mid and in_sleep_type = 'N' AND in_out = 'N'  AND in_userlevel != '9')
					where
						ol.ol_status='N'
					order by ol.ol_uid asc
					limit 0, 10
				");
				// JJC : 수정 : 2021-05-17
				foreach($mr_row as $k=>$v){

					// - 문자발송 ---
					if( in_array($_type , array("sms" , "both"))  ) {
						unset($arr_send);
						$smskbn = "2year_opt";	// 문자 발송 유형

						$row_sms = _MQ("select * from smart_sms_set where ss_uid = '".$type."' limit 1");

						$arr_replace = array(
							"{사이트명}" => $siteInfo['s_adshop'] ,
							"{회원명}" => $sms_name
						);

						if($row_sms['ss_status'] == "Y") {

							$sms_to		= $v['in_tel2'] ? $v['in_tel2'] : $v['in_tel'] ;
							$sms_name = $v['in_name'];// 회원명

							$sms_text = str_replace(array_keys($arr_replace),array_values($arr_replace), $row_sms['ss_text']);
							//$sms_text	= $row_sms['ss_text'];
							//$sms_text = str_replace("{사이트명}",$siteInfo[s_adshop],$sms_text);
							//$sms_text = str_replace("{회원명}",$sms_name,$sms_text);
							$sms_title	= $row_sms['ss_title'];
							$sms_file	= $row_sms['ss_file'];

							 // 문자/알림톡 통합 발송
							 //$arr_send[] = array('receive_num'=> $siteInfo[s_glbmanagerhp], 'send_num'=> $siteInfo[s_glbtel], 'msg'=> $sms_text, 'title'=>$row_sms[ss_title], 'image'=>$row_sms[ss_file], 'reserve_time'=>'' );
							 $arr_send[] = array_merge(array('receive_num'=> $siteInfo[s_glbmanagerhp], 'send_num'=> $siteInfo[s_glbtel], 'msg'=> $sms_text, 'title'=>$row_sms[ss_title], 'image'=>$row_sms[ss_file], 'reserve_time'=>'' ) , smsinfo_array($row_sms , $arr_replace));
						}

						// 문자/알림톡 통합 발송
						//onedaynet_sms_multisend($arr_send);
						onedaynet_alimtalk_multisend($arr_send);

					}
					// - 문자발송 ---

					// - 메일발송 ---
					if( in_array($_type , array("email" , "both"))  ) {
						$_id = $v['in_id'];// 아이디 설정
						$_name = $v['in_name'];// 이름 설정
						$_email = $v['in_email'];// 이메일 설정

						if( mailCheck($_email) ){
							// ==> 메일 내용 불러오기 $mailling_content
							include(OD_MAIL_ROOT."/mail.contents.2yearOpt.php"); // 메일 내용 불러오기 ($mailing_content)
							$_content = get_mail_content($mailling_content);

							mailer( $_email , $_title , $_content );
						}
					}
					// - 메일발송 ---

					//발송후 업데이트
					_MQ_noreturn(" update smart_2year_opt_log set ol_sdate=now() , ol_status='Y' where ol_uid='". $v['ol_uid'] ."' "); // 로그 발송처리

				}

				// JJC : 수정 : 2021-05-17
				//$mr_cnt = _MQ(" select count(*) as cnt from smart_2year_opt_log where ol_status='N'  "); // 수신동의 2년 지난 -  회원 갯수 체크
				$mr_cnt = _MQ(" select count(*) as cnt from smart_2year_opt_log INNER JOIN smart_individual on (in_id = ol_mid and in_sleep_type = 'N' AND in_out = 'N' and in_userlevel != '9') where ol_status='N'  "); // 수신동의 2년 지난 -  회원 갯수 체크
				echo $mr_cnt['cnt'];

			}
			break;

		}

?>