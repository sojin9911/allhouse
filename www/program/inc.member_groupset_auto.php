<?php # LCY :: 2017-12-06 -- 회원등급평가 실행프로그램
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
/*
	`smart_setup` 칼럼
	groupset_autouse : 자동평가 사용여부 ( Y, N ) 상단에 서 판별
	groupset_apply_rdate : 최근갱신시간
	groupset_auto_daily : 자동판별 기간 (day,week,month) 당일,일주일,한달
	groupset_check_term : 특정기간 주문 판별 시 지난달(monthlast), 최근1~6개월(month1~month6),
*/
	// -- 실행가능 변수
	$applyValue = false;
	$updateResult = false;

	// -- 테스트시 주석해제 후 실행하면 된다.
	// define('MEMBER_GROUPSET_APPLY',true);

	if( defined('MEMBER_GROUPSET_APPLY') === false){
		if($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']) return; // 동일 서버안에서만 동작 하도록 => curl_async을 통해서만 실행됨

		$opDate = date("Y-m-d",strtotime($siteInfo['groupset_apply_rdate'])); // 최근갱신일을 날짜로 변경.

		// -- 판별기간에 따른 날짜를 가져온다.
		switch( $siteInfo['groupset_auto_daily'] ){
			case "day":$appDate = date("Y-m-d"); break; // 당일 날짜를 가져온다.
			case "week": $appDate = date("Y-m-d",strtotime("-1 week")); break; // 한주전의 날짜를 가져온다.
			case "month": $appDate = date("Y-m-d",strtotime("-1 month")); break; // 한달전의 날짜를 가져온다.
		}

		if( $opDate <= $appDate){ // 최근실행시간이 자동평가 기간보다 작거나 같다면 실행
			$applyValue = true;
		}

	}else{
		$applyValue = true;
	}

	// -- 등급평가 시작
	if($applyValue === true){
		// -- 부화를 줄이기 위해 주문을 한 회원만 join 하여 가져온다.
		/*
			@ 조건 ::주문건에 대한 처리
		*/

		// -- 등급별 정보를 배열로 담는다.  {1보다 큰값을가져온다.}
		$arrGroupInfo = array();
		$resMgs = _MQ_assoc("select *from smart_member_group_set  where 1 and mgs_rank  > 1 order by mgs_idx desc, mgs_rank desc  "); // 정렬추가

		// -- 등급기본순위를 가져온다.
		$defaultMgsUid = _MQ_result("select mgs_uid from smart_member_group_set  where 1 and mgs_rank  = 1 ");

		if(rm_str($defaultMgsUid)== '' ) return;


		$chkTermS = $arrGroupsetCheckTerm['value'][$siteInfo['groupset_check_term']]['s']; // 시작일
		$chkTermE = $arrGroupsetCheckTerm['value'][$siteInfo['groupset_check_term']]['e']; // 종료일
		$arrUpdateMember = $arrUpdateMemberInfo  = array();  // 업데이트에 해당되는 회원을 담을 배열
		$resStep1 = _MQ_assoc("select count(*) as cnt, sum(o_price_real) as totPrice, o_mid , in_mgsuid
		 from smart_order as o inner join smart_individual as m on(m.in_id = o_mid) where
			o_memtype = 'Y' and o_paystatus = 'Y'
			and o_canceled = 'N' and o_status in('결제완료','배송중','배송완료')
			and ( left(o_rdate,10) BETWEEN '".$chkTermS."' and '".$chkTermE."' )
			and in_sleep_type = 'N' and in_out = 'N' and in_auth = 'Y'
			group by o_mid
		");

		// -- 주문에 속한 회원 기준으로 먼저 등급어데이트를 한다.
		foreach($resStep1 as $k=>$v){

			$updateMgsUid = '';

			// -- 등급별로 판별하여 처리
			foreach($resMgs as $sk=>$sv){

				if( $arrUpdateMember[$v['o_mid']] == $v['o_mid'] ){ continue; } // 추가,,

				if( $sv['mgs_condition_totprice'] <= $v['totPrice'] && $sv['mgs_condition_totcnt'] <= $v['cnt']){ // 회원의 총 주문금액이 등급별 주문금액 조건보다 크고, 주문횟수가 클경우
					$arrUpdateMember[$v['o_mid']] = $v['o_mid']; // 주문기록이 없는 회원 일괄업데이트시 제외될 회원 배열로 담는다.
					$arrUpdateMemberInfo[$sv['mgs_uid']][] = $v['o_mid'];
				}
			}
		}

		// -- 공통쿼리문 실행 => 휴면이아니고, 탈퇴가 아니고, 승인이 Y인것
		$commonSque = " and in_sleep_type = 'N' and in_out = 'N' and in_auth = 'Y' ";

		// -- 등급별로 처리 => 이전 등급을 기록한다.  --
		foreach($arrUpdateMemberInfo as $k=>$v){
			_MQ_noreturn("update smart_individual set in_mgsuid_old = in_mgsuid , in_mgsuid = '".$k."' ,  in_mgsdate = now()
				where in_id IN ('".implode("' , '",array_values($v))."' )
				".$commonSque."
			");
		}

		// -- 나머지 등급은 모두 기본등급으로 => 이전 등급을 기록한다. --
		_MQ_noreturn("update smart_individual set in_mgsuid_old = in_mgsuid , in_mgsuid = '".$defaultMgsUid."' ,  in_mgsdate = now()
			where in_id NOT IN ('".implode("' , '",array_values($arrUpdateMember))."')
			".$commonSque."
		");

		// -- 등급 최근 갱신일 업데이트
		_MQ_noreturn("update smart_setup set groupset_apply_rdate = now() where s_uid = 1;");
		$updateResult = true; // 업데이트 최종 성공 return; => 수동 처리 시 해당 값을 통해 처리
	}


?>