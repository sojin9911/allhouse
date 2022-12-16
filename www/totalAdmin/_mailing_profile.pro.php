<?PHP
	include "./inc.php";



	// - 모드별 처리 ---
	switch( $_mode ){

		// -- 선택회원 전체 삭제 ---
		case "profile_delete":
			$fp = fopen("..".IMG_DIR_NORMAL."/mailing.profile.php", "w");
			fputs($fp,"<?PHP\n\t\$app_profile = '';\n?>");
			fclose($fp);
			error_loc_msg("_mailing_profile.form.php" . URI_Rebuild('?', array('_mode'=>'send', '_mduid'=>$_mduid, '_PVSC'=>$_PVSC)) , "정상적으로 삭제하였습니다.");
			break;
		// -- 선택회원 전체 삭제 ---



		// -- 선택회원 전체 삭제 ---
		case "delete":
			if($_mpuid){
				_MQ_noreturn(" delete from smart_mailing_profile where mp_uid = '". $_mpuid ."' ");
			}else{
				error_msg('잘못된 접근입니다.');
			}

			error_loc_msg("_mailing_profile.form.php" . URI_Rebuild('?', array('_mode'=>'send', '_mduid'=>$_mduid, '_PVSC'=>$_PVSC)) , "정상적으로 삭제하였습니다.");
			break;
		// -- 선택회원 전체 삭제 ---





		// -- 메일 발송을 위한 데이터 저장 ---
		case "send":

			// --- 사전 체크 ---
			$_mduid = nullchk($_mduid , "잘못된 접근입니다.");
			$_cnt = nullchk($_cnt , "메일링 회원을 선택해 주시기 바랍니다.");
			if( $_cnt == 0 ) {
				error_msg("메일링 회원을 선택해 주시기 바랍니다.");
			}
			// --- 사전 체크 ---

			// --- 저장한 정보 불러오기 --> $app_profile 로 저장됨
			include_once("..".IMG_DIR_NORMAL."/mailing.profile.php");
			$ex_app_profile = array_filter(array_unique(explode("," , $app_profile)));
			$cnt_app_profile = sizeof($ex_app_profile);
			// --- 저장한 정보 불러오기 --> $app_profile 로 저장됨


			// --- 200명 단위로 끊어서 arr_profile 저장 ---
			// 오늘 등록한 해당 메일은은 같은 _brother_key가 되도록 함
			// _brother_key는 md5처리함
			$_brother_key = md5( $_mduid . $cnt_app_profile . date("YmdH"));
			$arr_profile = array();// 메일링 회원 이메일 200개 단위 저장 배열
			if( $cnt_app_profile > 0 ){
				$chk_cnt = 0;
				foreach($ex_app_profile as $k=>$v){
					// 무조건 내림처리
					$app_key = floor ( $chk_cnt / 200 );
					$app_2key = $chk_cnt % 200 ;
					$arr_profile[$app_key][$app_2key] = $v; // 배열에 메일 저장 (_brother_key에 의해 200개씩 끊어지게 됨)
					$chk_cnt ++;
				}
			}
			// --- 200명 단위로 끊어서 arr_profile 저장 ---


			// --- arr_profile 배열 정보를 smart_mailing_profile에 저장 ---
			foreach($arr_profile as $k=>$v){
				$que = "
					insert smart_mailing_profile set
						 mp_mduid = '{$_mduid}'
						,mp_brother = '{$_brother_key}'
						,mp_email = '". implode("," , $v) ."'
						,mp_status = 'N'
						,mp_rdate = now()
				";
				_MQ_noreturn($que);
			}
			// --- arr_profile 배열 정보를 smart_mailing_profile에 저장 ---


			// --- 선택회원 삭제 ---
			$fp = fopen("..".IMG_DIR_NORMAL."/mailing.profile.php", "w");
			fputs($fp,"<?PHP\n\t\$app_profile = '';\n?>");
			fclose($fp);
			// --- 선택회원 삭제 ---

			error_loc_msg("_mailing_profile.form.php" . URI_Rebuild('?', array('_mode'=>'send', '_mduid'=>$_mduid, '_PVSC'=>$_PVSC)) , "메일 발송 데이터를 정상적으로 저장하였습니다.\\n\\n메일발송 버튼을 눌러 최종 발송 처리 하시기 바랍니다.");
			break;
		// -- 메일 발송을 위한 데이터 저장 ---

		// 발송 처리
		case "sendpro":

			if(!$_uid) error_alt("잘못된 접근입니다.");

			// - 데이터 추출 ---
			$r = _MQ_assoc("
				select mp.* , md.* from smart_mailing_profile as mp
					inner join smart_mailing_data as md on (md.md_uid = mp.mp_mduid)
				where mp_status='N' and mp_uid = '".$_uid."'
				");
			foreach( $r as $k=>$v ){

				// -- 메일 발송전 데이터를 발송으로 처리함... 메일 발송 에러시 무한 루프 돌 수 있기 때문 ---
				$sque = " update smart_mailing_profile set mp_status='Y' , mp_sdate=now() where mp_uid='$v[mp_uid]' ";
				_MQ_noreturn($sque);


				// -- 변수 준비
				$_title = stripslashes($v[md_title]);
				$_content = stripslashes($v[md_content]);
				$_type = stripslashes($v[md_type]);
						$_header = "MIME-Version: 1.0\r\n" ;
				$_header .= "Content-Type: text/html; charset=utf-8\r\n";
				$_header .= "Reply-To:".$siteInfo[s_ademail]."\r\n";

				$_rdate = str_replace("-" , "." , substr($v[mp_rdate],0,10));


				// -- 이메일 데이터 분리 ---
				$ex_app_profile = array_filter(array_unique(explode("," , $v[mp_email])));

				foreach($ex_app_profile as $sk=>$sv){

					$_email = $sv; // -> 이메일

					if( $_type == "all" ){
						$app_content = $_content ; // -> 메일내용
					}
					else {
						// -- 준비할 변수 1. $_title : 제목
						// -- 준비할 변수 2. $_content : 내용
						// -- 준비할 변수 3. $_rdate : 보내는 날짜 (YYYY.MM.DD)
						// -- 준비할 변수 4. $_email : 보내는 메일
						$app_content = get_mail_content($_content);
					}

					if($v['md_adchk'] == 'Y'){
						// 이메일 암호화 전송
						$_temp_content = preg_replace('/\[__date__\]/',$chk_email[$sv],nl2br($siteInfo['s_set_email_txt'])); // SSJ : 메일링 수신동의 항목 누락 수정 : 2021-02-24
						$_temp_content = preg_replace('/\[__deny__\]/','<a target="_blank" href="http://'.$_SERVER['SERVER_NAME'].'/addons/action/pro.php?_mode=email_deny&_email='.enc('e',$sv).'">[수신거부]</a>',$_temp_content);
					}

					// SSJ : 메일링 수신동의 항목 디자인 변경 : 2021-02-24
					$deny_content = "
						<div style='overflow:hidden; padding:5px; margin-top:-5px;'>
							<div style='max-width:700px; margin:0 auto; padding:20px 0; text-align:center; background-color:#f5f5f5; font-size: 12px; font-family: 돋움,Dotum; line-height: 17px; border: 1px solid #ddd; box-sizing: border-box; color: #666;'>
								".$_temp_content."
							</div>
						</div>
					";

					$app_content .= $deny_content;

                    // 네이버 깨짐현상으로 인해
                    if( preg_match("/@naver.com/i" , $_email) ){
                        $app_header = "From: \"".$siteInfo[s_adshop]."\" <".$siteInfo[s_ademail]."> \r\n" . $_header;
                    }
                    else {
                        $app_header = "From: \"" . "=?UTF-8?B?".base64_encode($siteInfo[s_adshop])."?="  . "\" <".$siteInfo[s_ademail]."> \r\n" . $_header;
                    }

					// -- 메일 발송
					@mail($_email, '=?UTF-8?B?'.base64_encode($_title).'?=', $app_content, $app_header ,  "-f".$siteInfo[s_ademail]);
				}

			}

			error_frame_loc_msg("_mailing_profile.form.php". URI_Rebuild('?', array('_mode'=>'send', '_mduid'=>$_mduid, '_PVSC'=>$_PVSC)) , "발송되었습니다.");

			break;

	}
	// - 모드별 처리 ---

?>