<?
include_once("inc.php");

	# 잘못된 접근 차단
	if($_GET['_mode'] == '' || $_GET['_email'] == '') {  error_alt("잘못된 접근입니다.","back");   }


	$_email = enc('d',$_GET['_email']); // 복호화

	switch($_mode) {

			// 비밀번호 변경 안내페이지에서 비밀번호 변경했을 경우 lcy  
			case 'email_deny' :

				$chk_member = _MQ_assoc("SELECT in_id FROM smart_individual WHERE in_email = '".stripslashes($_email)."' AND in_emailsend = 'Y' ");

				if(count($chk_member) > 0){

					$result_memeber=array();
					foreach($chk_member as $k=>$v){
						$result_member[] = $v['in_id'];
					}

					$id = $chk_member[0]['in_id'];
					$_mailling = 'N';
					$_deny_msg = ' <dd style="font-family:\'나눔고딕\',\'돋움\'; font-size:13px; padding:15px 25px; font-weight:600; color:#555; margin:0; border:1px solid #ddd; border-top:0"> ※ 해당이메일로 등록된 계정의 모든 수신동의 상태가 변경됨을 알려드립니다. </dd> ';

					// 정보수정 시 광고성 정보 수신동의 상태 - 정보 추가 - changeAlert 
					// 자체로 메일발송함.
					// $id 변수를 이용하여 회원정보 추출
					// $_mailling / $_sms 정보 있어야 함.
					$_change_alert_file_name = $_SERVER["DOCUMENT_ROOT"] . "/addons/changeAlert/changeAlert.mail.contents.modify.php";
					if(@file_exists($_change_alert_file_name)) { include_once($_change_alert_file_name); }								

					_MQ_noreturn("UPDATE smart_individual SET m_opt_date = '".date('Y-m-d H:i:s')."', in_emailsend = 'N' WHERE FIND_IN_SET(in_id,'".implode(',',$result_member)."') > 0  ");

					error_loc_msg('/','수신거부가 정상적으로 등록되었습니다.\n앞으로 등록된 '.$_email.' 로는 더이상 광고성, 이벤트성 이메일이 발송되지 않습니다.');

				}else{
					error_loc_msg('/?pn=member.login.form','수신거부될 이메일이 존재하지 않습니다. 로그인후 수신거부를 해주세요.');
				}


			break;

	// - 모드별 처리 ---
	}
?>