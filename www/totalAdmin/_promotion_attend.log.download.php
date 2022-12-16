<?php
// 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19
@ini_set("precision", "20");
@ini_set('memory_limit', '1000M'); // 메모리 가용폭을 1기가 까지 올림

include_once('inc.php');
$fileName = 'attendLog';
$toDay = date('Y-m-d', time());


# header 설정
if(!$c) {
	header( "Content-type: application/vnd.ms-excel; charset=utf-8" );
	header( "Content-Disposition: attachment; filename=$fileName-$toDay.xls" );
	print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");
}



// 출석체크 이벤트 정보 추출
$r = _MQ(" select * from smart_promotion_attend_config where atc_uid = '". $_uid ."' ");


$pr = array();
// 검색엑셀 다운로드
$orderby = " order by atl_uid desc ";
if($_mode == 'search'){
	$s_query = enc('d', $_search);
	$pr = _MQ_assoc(" select *  " . $s_query . $orderby);

// 선택엑셀다운로드
}else if($_mode == 'select'){
	$arr_uid = array();
	foreach($chk_uid as $k=>$v){
		if($v == 'Y') $arr_uid[] = $k;
	}
	if(sizeof($arr_uid)){
		$pr = _MQ_assoc(" select * from smart_promotion_attend_log as atl left join smart_individual as indr on (atl.atl_member = indr.in_id) where atl_uid in ('". implode("','" , $arr_uid) ."') " . $orderby);
	}

// 전체상품
}else{
	$pr = _MQ_assoc(" select * from smart_promotion_attend_log " . $orderby);
}


// th 생성
function add_table_th($title) {

	return '<th>'.strip_tags($title).'</th>';
}

// 엑셀 다운로드 항목 설정
$th = array(
	'회원아이디'=>array(
		'key'=>'atl_member',
	),
	'회원명'=>array(
		'key'=>'in_name',
	),
	'출석체크 진행 현황'=>array(
		'key'=>'atc_type',
	),
	'지급쿠폰'=>array(
		'key'=>'atl_coupon_name',
	),
	'지급적립금'=>array(
		'key'=>'atl_point',
	),
	'참여일시'=>array(
		'key'=>'atl_rdate',
	),
);

?>
<table border="1">
	<thead>
		<tr>
			<?php
			foreach($th as $k=>$v) {
				echo '<th>'.strip_tags($k).'</th>';
			}
			?>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($pr as $k=>$v) {
			// 달성조건
			$_status = ($r['atc_type'] == 'T' ? '누적참여' : '연속참여 ') . $v['atl_addinfo_days'] . '일';
			// 출석체크 진행 현황 만족
			if($v['atl_success'] == 'Y') $_status .= ' (보상지급)';
			else if($v['atl_addinfo_days_count'] > 0) $_status .= ' ('.$v['atl_addinfo_days_count'].'일/'.$v['atl_addinfo_days'].'일)';
			$v['atc_type'] = $_status;

			// 지급한 쿠폰이 있을때 발급대기인 쿠폰이 있는지 체크
			$_ready_coup = '';
			if($v['atl_coupon']>0){
				$_ready = _MQ(" select acr_idate from smart_promotion_attend_coupon_ready where acr_atluid = '". $v['atl_uid'] ."' and acr_status = 'N' ");
				if($_ready['acr_idate']) $_ready_coup = ' (발급예정일: ' . $_ready['acr_idate'] . ')';
			}
			$v['atl_coupon_name'] = ($v['atl_coupon_name'] ? stripslashes($v['atl_coupon_name']) : '').$_ready_coup;
		?>
		<tr>
			<?php
			foreach($th as $kk=>$vv) {
				echo '<td>'.$v[$vv['key']].'</td>';
			}
			?>
		</tr>
		<?php } ?>
	</tbody>
</table>