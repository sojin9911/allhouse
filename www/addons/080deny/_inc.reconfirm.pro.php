<?php 

	include "./inc.php";

	// - 모드별 처리 ---
	switch( $_mode ){

		// 수신거부 고객을 포함하여 발송시 재확인 - select(선택) 형 처리
		// 결과 값이 1 이상이면 수신거부를 포함하고 있음
		case "sms_chk_again_select":

			if( count($chk_cellular) < 1){ $chk_cellular = $arrID; } 

			if($_action == 'check'){ // 체크라면

				$que = " select count(*) as cnt from smart_individual where in_userlevel != '9' and in_smssend = 'N' and in_id in ('". implode("','" , array_values($chk_cellular)) ."') ";
				$chkr = _MQ($que);
				echo json_encode(array('rst'=>'success','deny_cnt'=>$chkr['cnt']));
				exit;

			}else if($_action == 'send'){ // 발송이라면
				if($_type == 'deny'){ // 제외발송 => 제외된 항목의 id 를 보내준다.
					$deny_arr = array();
					$que = " select in_id from smart_individual where in_userlevel != '9' and in_smssend = 'N' and in_id in ('". implode("','" , array_values($chk_cellular)) ."') ";
					$chkr = _MQ_assoc($que);
					foreach($chkr as $k=>$v){
						$deny_arr[] = $v['in_id']; 
					}

					if(count($deny_arr) < 1 ) {  // 제외된 항목이 없다면
						echo json_encode(array('rst'=>'fail','msg'=>'제외할 회원이 존재하지않습니다.')); // 실패처리
						exit;                       
					} 

					echo json_encode(array('rst'=>'success','deny_arr'=>$deny_arr)); // 제외하고 보낼 회원의 아이디
					exit;

				}
			}

			break;

		// 수신거부 고객을 포함하여 발송시 재확인 - search (검색) 형 처리
		// 결과 값이 1 이상이면 수신거부를 포함하고 있음
		case "sms_chk_again_search":
			if($_search_que == ''){ $_search_que = $searchQue; }
			if($_action == 'check'){ // 체크라면
				$que = " select count(*) as cnt  " . enc('d' ,  $_search_que ) . " and in_smssend = 'N' and in_userlevel != '9'  ";
				$chkr = _MQ($que);
				echo json_encode(array('rst'=>'success','deny_cnt'=>$chkr['cnt']));
				exit;
			}else{

				if($_type == 'deny'){ // 제외발송 => 제외된 항목의 id 를 보내준다.
					$squery = enc('d' ,  $_search_que); // 수신이 y 인것

					if(preg_match("/(and in_smssend = \'Y\')/",$squery) == true){
						echo json_encode(array('rst'=>'fail','msg'=>'잘못된 정보입니다.')); // 실패처리
						exit;           
					}

					$squery .= " and in_smssend = 'Y' and in_userlevel != '9'  ";

					$que = " select count(*) AS cnt " .$squery  ." ";// JJC : 검색 시 `from smart_individual` 중복 오류수정 : 2020-09-24


					$chkr = _MQ($que);

					if($chkr['cnt'] < 1 ) {  // SMS 보낼회원이 없을 시
						echo json_encode(array('rst'=>'fail','msg'=>'SMS를 보낼회원이 존재하지 않습니다.')); // 실패처리
						exit;                       
					} 

					echo json_encode(array('rst'=>'success','_search_que'=>enc('e',$squery),'_search_dque'=>$squery)); // 제외하고 보낼 회원의 아이디
					exit;

				}

			}
			break;

		}

?>