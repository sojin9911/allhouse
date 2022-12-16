<?php

	include_once("./inc.php");

	if(count($_uid) > 0){
		foreach ($_uid as $k=>$v) {

			$r = array();
			$r = _MQ(" select `s_uid` from `smart_order_settle_complete` where `s_uid` = '{$v}' ");
			$r = array_merge($r , _text_info_extraction( "smart_order_settle_complete" , $r['s_uid'] ));
			$opuid = explode(',', $r['s_opuid']);
			foreach($opuid as $kk=>$vv) {
				_MQ_noreturn(" update `smart_order_product` set `op_settlementstatus` = 'ready', `op_settlement_complete` = '0000-00-00 00:00:00' where `op_uid` = '{$vv}' ");
			}

			order_settlement_status_opuid(array_values($opuid));//2015-08-19 추가 - 정준철

			_MQ_noreturn("delete from `smart_order_settle_complete` where `s_uid` = '{$v}' ");

		}

		error_loc_msg('_order4.list.php?'.enc('d' , $_PVSC), '정상적으로 처리되었습니다.');
	}else{
		error_loc_msg('_order4.list.php?'.enc('d' , $_PVSC), '잘못된 접근입니다.');
	}

?>