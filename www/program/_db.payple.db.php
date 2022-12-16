<?php
/*
* LCY : 2021-07-04 : 신용카드 간편결제 추가
* -- http://{도메인}/program/_db.payple.db.php
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');

// smart_setup 필드추가 추가
if(!IsField('smart_setup', 's_payple_use')){
	_MQ_noreturn("
			ALTER TABLE smart_setup
				ADD s_payple_use ENUM( 'N', 'Y' ) NOT NULL DEFAULT 'N' COMMENT '간편결제(페이플) 사용여부',
				ADD s_payple_mode ENUM( 'test', 'service' ) NOT NULL DEFAULT 'test' COMMENT '간편결제(페이플) 활성화 모드',
				ADD s_payple_cst_id VARCHAR( 50 ) NOT NULL COMMENT '간편결제(페이플) 가맹점 ID(cst_id)',
				ADD s_payple_custKey VARCHAR( 100 ) NOT NULL COMMENT '간편결제(페이플) 가맹점 운영 Key(custKey)',
				ADD s_payple_cancelKey VARCHAR( 100 ) NOT NULL COMMENT '간편결제(페이플) 취소(환불) key',
				ADD s_product_auto_PP INT( 3 ) NOT NULL DEFAULT '0' COMMENT '페이플 간편결제 자동정산 처리 일수 추가' AFTER s_product_auto_on ;
	");
	echo '<hr> smart_setup에 필드 추가완료</hr>';
}else{
	echo '<hr> smart_setup에 필드 이미추가완료</hr>';
}


// smart_order 필드 수정
_MQ_noreturn("
	ALTER TABLE smart_order 
		CHANGE o_paymethod o_paymethod ENUM( 'card', 'iche', 'online', 'point', 'virtual', 'hpp', 'payco', 'payple' ) NULL DEFAULT 'card' COMMENT '결제방식, payple 추가';
");
echo '<hr> smart_order에 필드 수정완료</hr>';


// smart_individual_payple_info 테이블 추가 
if( !IsTable('smart_individual_payple_info') ){
	_MQ_noreturn("
		CREATE TABLE IF NOT EXISTS smart_individual_payple_info (
			ipi_inid varchar(30) NOT NULL COMMENT '회원아이디',
			ipi_payer_id varchar(100) default NULL COMMENT '빌링키',
			ipi_rdate datetime NOT NULL COMMENT '등록일',
			UNIQUE KEY ipi_inid (ipi_inid)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='페이플 간편결제 연동정보';
	");
	echo '<hr> smart_individual_payple_info 테이블 추가완료</hr>';
}else{ echo '<hr> smart_individual_payple_info 테이블 이미 추가됨</hr>';  }


$chk = _MQ("select count(*) as cnt from smart_admin_menu where am_link = '_config.pg_easypay.form.php' ");
if( $chk['cnt'] < 1){
	_MQ_noreturn("insert smart_admin_menu set am_view = 'Y' , am_name ='간편결제 설정', am_depth = '3', am_parent = '1,17', am_idx = '6'   , am_link = '_config.pg_easypay.form.php' ");
	echo '<hr>관리자 간편결제 설정 메뉴 추가완료</hr>';
}else{
	echo '<hr>관리자 간편결제 설정 메뉴 이미 추가됨 </hr>';
}