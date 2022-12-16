<?PHP
	include_once("./inc.php");

	// - 모드별 처리 ---
	switch( $_mode ){

        // 입고처리
        case "modify_stock" :
            $_uid = $_GET["_uid"];
            $_op_stockorder_cnt = $_GET["_op_stockorder_cnt"];
            $_op_stock_cnt = $_GET["_op_stock_cnt"];

            if($_uid) {
                if (!$_op_stockorder_cnt) $_op_stockorder_cnt = 0;
                if (!$_op_stock_cnt) $_op_stock_cnt = 0;
                _MQ_noreturn(" update smart_order_product set op_orderstock_cnt = '{$_op_stockorder_cnt}' , op_instock_cnt = '{$_op_stock_cnt}' where op_uid='{$_uid}' ");
            }

            error_frame_reload('변경하였습니다.');

        // 품절처리
        case "modify_soldout" :
            $_ordernum = $_GET["_ordernum"];
            $_uid = $_GET["_uid"];

            if($_ordernum && $_uid) {
                _MQ_noreturn(" update smart_order_product set op_sendstatus = '품절' where op_oordernum='{$_ordernum}' and op_uid='{$_uid}' ");
            }

            error_frame_reload('변경하였습니다.');
            
    }
	// - 모드별 처리 ---

	exit;
?>