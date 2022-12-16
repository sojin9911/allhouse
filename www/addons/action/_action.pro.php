<?PHP

// 080 수신거부 설정 적용
include_once("inc.php");

$chk_info = array();

/*
	데이터 추가
*/

$chk_data['2year_opt'] = _MQ("select ss_uid from smart_sms_set where ss_uid = '2year_opt'  ");

if($chk_data['2year_opt']['ss_uid'] <> '2year_opt'){



	 _MQ_noreturn(" ALTER TABLE  `smart_sms_set` CHANGE  `ss_text`  `ss_text` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '문자내용' ");	

	 _MQ_noreturn(" INSERT INTO smart_sms_set ( ss_uid, ss_status , ss_text, ss_title, ss_file) VALUES
	    ( '2year_opt', 'y', '[{사이트명}] {회원명}님. \r\n수신동의 후 2년이 경과하였습니다.\r\n\r\n정보통신망법 제50조제8항 및 동법 시행령 제62조의3은 최초 동의한 날로부터 매2년마다 하도록 규정하고 있습니다. \r\n\r\n이에 따라 수신동의 받은 날부터 매 2년 마다 수신동의 여부를 재확인 해야 합니다.\r\n\r\n사이트에 접속하시어 로그인 하신 후 마이페이지 > 정보수정을 통해 이메일 및 SMS에 대한 수신여부를 확인해주시기 바랍니다.\r\n\r\n본 문자는 수신동의하신지 2년이 지난 회원중 SMS 수신에 동의 하신 회원에게만 발송이 됩니다.\r\n\r\n감사합니다.', '', ''); ");	

	 $chk_info[]['data'] = 'smart_sms_set 테이블, 데이터 추가 ';

}

/*
	칼럼추가
*/

# 오늘의 날짜를 확인하는 smart_setup 테이블 :: s_daily 칼럼
$is_column = is_column('smart_setup','s_daily');
if($is_column == false){
	_MQ_noreturn("ALTER TABLE  `smart_setup` ADD  `s_daily` DATE NOT NULL COMMENT  '오늘의 날짜'");
	$chk_info[]['column'] = 'smart_setup 테이블, s_daily 추가 ';

}


# 이메일수신 안내문구 추가 smart_setup 테이블 :: s_set_email_txt 칼럼
$is_column = is_column('smart_setup','s_set_email_txt');
if($is_column == false){
	_MQ_noreturn("ALTER TABLE  `smart_setup` ADD  `s_set_email_txt` TEXT NOT NULL COMMENT  '이메일 수신동의 및 수신거미에 대한 문구'");

	$text = "본 메일은 [__date__]일 상기 메일에 대한 메일을 수신동의 하셨습니다.\n본메일은 발신 전용 메일입니다. 메일수신을 원치 않으시면 [__deny__]를 눌러주십시오.\nif you do not want this of email_information, please click the [__deny__]";

	_MQ_noreturn("UPDATE smart_setup SET s_set_email_txt = '".$text."' WHERE s_uid = '1'  ");

	$chk_info[]['column'] = 'smart_setup 테이블, s_set_email_txt 추가 ';

}


# 080 수신거부 항목 추가 smart_setup 테이블 :: s_deny_tel 칼럼
$is_column = is_column('smart_setup','s_deny_tel');
if($is_column == false){
	_MQ_noreturn("ALTER TABLE  smart_setup ADD  s_deny_tel VARCHAR( 20 ) NULL DEFAULT NULL COMMENT  '080 수신거부 신청전화번호'");
	$chk_info[]['column'] = 'smart_setup 테이블, s_deny_tel 추가 ';
}
# 080 수신거부 항목 추가 smart_setup 테이블 :: s_deny_use 칼럼
$is_column = is_column('smart_setup','s_deny_use');
if($is_column == false){
	_MQ_noreturn("ALTER TABLE  smart_setup ADD  s_deny_use ENUM(  'Y',  'N' ) NOT NULL DEFAULT  'N' COMMENT  '080 수신거부 사용여부'");
	$chk_info[]['column'] = 'smart_setup 테이블, s_deny_use 추가 ';
}


# 매 2년마다 수신동의 설정 추가 smart_setup 테이블 :: s_2year_opt_use 칼럼
$is_column = is_column('smart_setup','s_2year_opt_use');
if($is_column == false){
_MQ_noreturn("ALTER TABLE  smart_setup ADD  s_2year_opt_use ENUM(  'Y',  'N' ) NOT NULL DEFAULT  'Y' COMMENT  '매 2년마다 수신동의 설정'");
	$chk_info[]['column'] = 'smart_setup 테이블, s_2year_opt_use 추가 ';
}
# 매 2년마다 수신동의 설정 추가 smart_setup 테이블 :: s_2year_opt_content_top 칼럼
$is_column = is_column('smart_setup','s_2year_opt_content_top');
if($is_column == false){
	_MQ_noreturn("ALTER TABLE  smart_setup ADD  s_2year_opt_content_top TEXT NULL COMMENT  '매 2년마다 수신동의 메일 - 상단내용'");
	$chk_info[]['column'] = 'smart_setup 테이블, s_2year_opt_content_top 추가 ';
}
# 매 2년마다 수신동의 설정 추가 smart_setup 테이블 :: s_2year_opt_title 칼럼
$is_column = is_column('smart_setup','s_2year_opt_title');
if($is_column == false){
	_MQ_noreturn("ALTER TABLE  smart_setup ADD  s_2year_opt_title VARCHAR( 200 ) NULL COMMENT  '매 2년마다 수신동의 메일 - 타이틀'");
	$chk_info[]['column'] = 'smart_setup 테이블, s_2year_opt_title 추가 ';
}


# 회원 수신동의/거부일자 smart_individual 테이블 :: m_opt_date 칼럼
$is_column = is_column('smart_individual','m_opt_date');
if($is_column == false){
	_MQ_noreturn("ALTER TABLE  smart_individual ADD  m_opt_date DATETIME NOT NULL COMMENT  '수신동의/거부일자'");
	$chk_info[]['column'] = 'smart_individual 테이블, m_opt_date 추가 ';
}
# 회원 수신동의/거부일자 smart_individual_sleep 테이블 :: m_opt_date 칼럼
$is_column = is_column('smart_individual_sleep','m_opt_date');
if($is_column == false){
	_MQ_noreturn("ALTER TABLE  smart_individual_sleep ADD  m_opt_date DATETIME NOT NULL COMMENT  '수신동의/거부일자'");
	$chk_info[]['column'] = 'smart_individual_sleep 테이블, m_opt_date 추가 ';
}


# 메일링 데이터의 광고성,이벤트성 체크 추가 => smart_mailing_data 테이블 :: md_adchk 칼럼
$is_column = is_column('smart_mailing_data','md_adchk');
if($is_column == false){
	_MQ_noreturn("ALTER TABLE  `smart_mailing_data` ADD  `md_adchk` ENUM(  'Y',  'N' ) NOT NULL COMMENT  '광고성, 이벤트성 메일링 체크 유무'");
	$chk_info[]['column'] = 'smart_mailing_data 테이블, md_adchk 추가 ';
}








/*
	# 테이블 추가
*/

# 080 수신거부 기록 테이블
$is_table = is_table('smart_member_080_deny');
if($is_table == false){
	_MQ_noreturn("
		CREATE TABLE IF NOT EXISTS smart_member_080_deny (
			md_uid int(10) NOT NULL auto_increment,
			md_refusal_num varchar(100) default NULL COMMENT '080 수신거부 번호',
			md_refusal_time varchar(50) default NULL COMMENT '수신거부 요청 시간',
			md_hp varchar(50) default NULL COMMENT '수신거부 요청 전화번호',
			md_status enum('OK' , 'MULTI' , 'NO' , 'FALSE' ) default 'OK' COMMENT '처리상태 - OK : 정상거부처리 , MULTI : 다수검색으로 인한 미처리 , NO : 미검색으로 인한 미처리 , FALSE : 080 수신거부 관리자 미설정 오류',
			md_rdate datetime default NULL COMMENT '저장일시',
			PRIMARY KEY  (md_uid),
			KEY md_status (md_status)
		) ENGINE=MyISAM COMMENT='080 수신거부 기록'
		");
	$chk_info[]['table'] = 'smart_member_080_deny 테이블 추가 ';
}

# 매2년 수신동의 재확인 - 메일/문자 발송 기록 테이블
$is_table = is_table('smart_2year_opt_log');
if($is_table == false){
	_MQ_noreturn("
		CREATE TABLE IF NOT EXISTS smart_2year_opt_log (
			ol_uid int(11) unsigned NOT NULL auto_increment,
			ol_mid varchar(50) NOT NULL default '' COMMENT '저장회원ID',
			ol_type enum('email' , 'sms' , 'both') NOT NULL default 'email' COMMENT '발송형태',
			ol_status	enum('Y', 'N') NOT NULL default 'N' COMMENT '발송상태',
			ol_rdate datetime default NULL COMMENT '저장일',
			ol_sdate datetime default NULL COMMENT '발송일',
			PRIMARY KEY  (ol_uid),
			KEY ol_mid (ol_mid),
			KEY ol_type (ol_type),
			KEY ol_status (ol_status)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='매2년 수신동의 재확인 - 메일/문자 발송 기록' 
		");
	$chk_info[]['table'] = 'smart_2year_opt_log 테이블 추가 ';
}



# 실행된 갯수에 대한 정보를 출력해준다.
$msg_list = '실행된 갯수 : '.count($chk_info) .'\n';
if(count($chk_info) > 0) { 

	
	foreach($chk_info as $k=>$v){
		$msg_list .= $v['column'].'\n';
		$msg_list .= $v['table'].'\n';
		$msg_list .= $v['data'].'\n';
	}

}

error_loc_msg("/totalAdmin/_attach/_addons.php?pass_menu=action/_action.form",$msg_list);


# 테이블 검사함수
function is_table($Table) {

	$sql = " desc " . $Table;
	$result = @mysql_query($sql);

	if(@mysql_num_rows($result)) return true;
	else return false;
}	

// 칼럼 검사 합수 ($Table => 테이블명, $Field=>칼럼명 )
function is_column($Table, $Field) {

	$sql = ' show columns from ' . $Table . ' like \''.$Field.'\' ';
	$result = @mysql_query($sql);

	if(@mysql_num_rows($result)) return true;
	else return false;
}	

?>