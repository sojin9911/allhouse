<?
	include_once("inc.php");


	if(!$_uid || !$_type){
		error_msgPopup_s("잘못된 접근입니다.");
	}

	switch($_type){
		// 주문외입금 처리
		case "adminC":
			
			$que = " update smart_orderbank_log set ob_status = 'Y' , ob_status_type = 'adminC' , ob_ordernum = '' , ob_content = concat(ob_content,'\n[". date("Y-m-d H:i:s") ."] 주문외입금처리')  where ob_uid = '{$_uid}' ";
			_MQ_noreturn($que);
			
			break;


		// 관리자주문연동 처리
		case "adminO":

			if(!$_ordernum){
				error_msgPopup_s("잘못된 접근입니다.");
			}else{
				$r = _MQ("SELECT o_ordernum FROM smart_order WHERE o_ordernum='" . $_ordernum . "'");
				$ordernum = $_ordernum = $r['o_ordernum'];
			}
			
			$que = " update smart_orderbank_log set ob_status = 'Y' , ob_status_type = 'adminO', ob_ordernum = '{$_ordernum}' , ob_content = concat(ob_content,'\n[". date("Y-m-d H:i:s") ."] 관리자 주문연동(". $_ordernum .")')  where ob_uid = '{$_uid}' ";
			_MQ_noreturn($que);

			// -- 입금확인처리 --------
			// 필수변수 : $_ordernum;
			include(dirname(__file__).'/inc.order_online.payconfirm.php');
			
			break;
	}
		
?>
<script type="text/javascript">
	opener.location.reload();
	window.close();
</script>