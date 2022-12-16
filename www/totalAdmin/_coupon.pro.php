<?php 
	include "./inc.php";

	switch ($_mode) {
		case 'issued': // 발급일 시

			// -- 회원 키
			$arrKey = array('in_id'=>'아이디');

			if($ctrlMode == 'select'){
				$ctrlModeName = '선택';
				if( count($arrID) < 1 ){ error_msg("회원을 1명이상 선택해 주세요."); }
				$sque = " from smart_individual where 1 and in_sleep_type = 'N' AND in_out = 'N' and find_in_set(in_id, '".implode(",",$arrID)."' ) > 0   ";
			}else if( $ctrlMode == 'search'){
				$sque = enc('d', $searchQue);
			}else{
				$ctrlModeName = '검색';
				error_msg('실행이 올바르지 않습니다.');
			}



			if( $orderby == ''){ $orderby = ' order by in_rdate desc '; }
			$res = _MQ_assoc("select ".implode(",",array_keys($arrKey))."   ".$sque. '  '.$orderby);
			if( count($res) < 1 ){ error_msg('잘못된 접근입니다.'); }

			$couponSetData  = _MQ("select *from smart_individual_coupon_set where ocs_uid = '".$_uid."'   ");
			if( count($couponSetData) < 1){ error_msg('발급할 쿠폰이 존재하지 않습니다.');  }
			if( $couponSetData['ocs_use_date_type'] == 'date' && $couponSetData['ocs_edate'] < date('Y-m-d')) { error_msg('발급이 불가능한 쿠폰입니다.');   }

			foreach($res as $k=>$v){
				give_coupon($v['in_id'],$couponSetData);
			}

			error_loc_msg("_coupon.form.php".($_PVSC ? '?'.enc('d' , $_PVSC) : '?_uid='.$_uid),$ctrlModeName.' 회원에게 쿠폰이 발급되었습니다.');			
				
		break;

		case 'delete': // 삭제 시

			// -- 회원 키
			$arrKey = array('coup_uid'=>'쿠폰번호');

			if($ctrlMode == 'select'){
				$ctrlModeName = '선택';
				if( count($arr_coup_uid) < 1 ){ error_msg("삭제하실 회원의 쿠폰을 1개이상 선택해 주세요."); }
				$sque = " from smart_individual_coupon as coup left join smart_individual as indr on(indr.in_id = coup.coup_inid) where 1 and coup_ocs_uid = '".$_uid."' 
				and find_in_set(coup_uid, '".implode(",",$arr_coup_uid)."' ) > 0
				 ";

			}else if( $ctrlMode == 'search'){
				$sque = enc('d', $searchQue);
			}else{
				$ctrlModeName = '검색';
				error_msg('실행이 올바르지 않습니다.');
			}


			$res = _MQ_assoc("select ".implode(",",array_keys($arrKey))."   ".$sque. '  '.$orderby);
			if( count($res) < 1 ){ error_msg('잘못된 접근입니다.'); }

			$couponSetData  = _MQ("select *from smart_individual_coupon_set where ocs_uid = '".$_uid."'   ");
			if( count($couponSetData) < 1){ error_msg('삭제할 회원의 쿠폰이 존재하지 않습니다.');  }

			$arrCoupUid = array();
			foreach($res as $k=>$v){
				$arrCoupUid[] = $v['coup_uid'];
			}


			
			_MQ_noreturn("delete from smart_individual_coupon where find_in_set(coup_uid , '".implode(",",$arrCoupUid)."' ) > 0 ");

	
			error_loc("_coupon.list.php".($_PVSC ? '?'.enc('d' , $_PVSC) : '?_uid='.$_uid));				
				
		break;
		
		default:
			exit;
			break;
	}

?>