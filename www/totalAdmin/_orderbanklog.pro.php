<?
	include_once("inc.php");


	if( (!$_uid || !$_mode) && $_mode <> 'create_table'){
		error_msg("잘못된 접근입니다.");
	}

	switch($_mode){
		// 메모수정
		case "memo":
			// ajax로 저장
			if($_uid){
				_MQ_noreturn(" update smart_orderbank_log set ob_memo = '" . addslashes(urldecode($_memo)) . "' where ob_uid = '". $_uid ."' ");
				echo 'success';
			}else{
				echo 'fail';
			}
			exit;
			break;

		// 입금내역삭제
		case "delete":
			// 주문연동된 내역은 삭제 불가
			$r = _MQ(" select count(*) as cnt from smart_orderbank_log as ob left join smart_order as o on (ob.ob_ordernum = o.o_ordernum) where ob_uid ='". $_uid ."'  and ob_ordernum != '' and ob_status = 'Y' and ob_status_type in ('order', 'adminO') and o.o_canceled = 'N' ");
			if($r['cnt']>0){
				error_msg("주문과 연동된 입금내역은 삭제할 수 없습니다.\\n\\n연동취소후 다시 시도해 주시기 바랍니다.");
			}else{
				_MQ_noreturn(" update smart_orderbank_log set ob_deleted = 'Y', ob_content = concat(ob_content,'\n[". date("Y-m-d H:i:s") ."] 입금내역삭제') where ob_uid ='". $_uid ."' ");
				error_loc_msg('_orderbanklog.list.php?pass_status=' . $pass_status . "&" . enc('d', $_PVSC) , "정상적으로 삭제되었습니다.");
			}
			break;

		// 연동취소
		case "cancel":
			// 주문내역추출
			$r = _MQ(" select ob_ordernum , count(*) as cnt from smart_orderbank_log where ob_uid ='". $_uid ."'  and ob_ordernum != '' and ob_status = 'Y' and ob_status_type in ('order', 'adminO') ");
			if($r['cnt']>0){
				// 필수변수 : $_ordernum;
				$_ordernum = $r['ob_ordernum'];
				include('inc.order_online.paycancel.php');
				
				_MQ_noreturn(" update smart_orderbank_log set ob_ordernum = '', ob_status = 'N', ob_status_type = 'ready', ob_content = concat(ob_content,'\n[". date("Y-m-d H:i:s") ."] 주문연동 취소') where ob_uid ='". $_uid ."' ");
				error_loc_msg('_orderbanklog.list.php?pass_status=' . $pass_status . "&" . enc('d', $_PVSC) , "정상적으로 취소되었습니다.");
			}else{
				error_msg("주문과 연동된 입금내역이 아닙니다.\\n\\n확인후 다시 시도해 주시기 바랍니다.");
			}
			
			error_loc_msg('_orderbanklog.list.php?pass_status=' . $pass_status . "&" . enc('d', $_PVSC) , "정상적으로 취소되었습니다.");
			break;

		// DB생성
		case "create_table":
			$que = "
				CREATE TABLE IF NOT EXISTS `smart_orderbank_log` (
				  `ob_uid` int(11) NOT NULL auto_increment,
				  `ob_ordernum` varchar(50) NOT NULL COMMENT '주문번호',
				  `ob_tid` varchar(100) NOT NULL,
				  `ob_content` text NOT NULL,
				  `ob_status` enum('Y','N') NOT NULL default 'N' COMMENT 'Y : 입금확인, N : 입금미확인',
				  `ob_status_type` enum('order','adminO','adminC','ready') NOT NULL default 'ready' COMMENT '처리상태 order: 정상주문연동, adminO: 관리자주문연동, adminC: 관리자확인, ready: 처리대기',
				  `ob_paydate` datetime NOT NULL COMMENT '실제 입금이 확인된 날짜',
				  `ob_date` datetime NOT NULL,
				  `ob_ordername` varchar(50) NOT NULL COMMENT '로그 저장 이름',
				  `ob_orderprice` int(11) NOT NULL default '0' COMMENT '로그 주문금액',
				  `ob_account` varchar(50) NOT NULL COMMENT '입금계좌번호',
				  `ob_memo` text NOT NULL COMMENT '관리자메모사항',
				  `ob_deleted` enum('Y','N') NOT NULL default 'N' COMMENT '삭제여부',
				  PRIMARY KEY  (`ob_uid`),
				  KEY `ob_ordernum` (`ob_ordernum`),
				  KEY `ob_deleted` (`ob_deleted`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;		
			";
			_MQ_noreturn($que);
			
			error_loc_msg('_orderbanklog.list.php?pass_status=' . $pass_status , "DB생성이 완료 되었습니다.");
			break;
	}
	exit;		
?>