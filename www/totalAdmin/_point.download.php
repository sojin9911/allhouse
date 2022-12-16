<?php
// 엑셀등 처리값의 숫자 줄임 설정을 변경한다.(1234567890E+12 -> 123456789012) - 2015-03-19
@ini_set("precision", "20");
@ini_set('memory_limit', '1000M'); // 메모리 가용폭을 1기가 까지 올림

include_once('inc.php');
$fileName = 'point';
$toDay = date('Y-m-d', time());

# header 설정
if(!$c) {
	header( "Content-type: application/vnd.ms-excel; charset=utf-8" );
	header( "Content-Disposition: attachment; filename=$fileName-$toDay.xls" );
	print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");
}
$pr = array();
// 검색엑셀 다운로드
if($_mode == 'search'){
	$s_query = enc('d', $_search);
	$pr = _MQ_assoc(" select *, indr.in_name from smart_point_log as pl left join smart_individual as indr on (pl.pl_inid = indr.in_id) where (1) " . $s_query . $orderby);

// 선택엑셀다운로드
}else if($_mode == 'select'){
	$arr_uid = array();
	foreach($chk_uids as $k=>$v){
		$arr_uid[] = $v;
	}
	if(sizeof($arr_uid)){
		$pr = _MQ_assoc(" select *, indr.in_name from smart_point_log as pl left join smart_individual as indr on (pl.pl_inid = indr.in_id) where pl_uid in ('". implode("','" , $arr_uid) ."') " . $orderby);
	}

// 전체상품
}else{
	$pr = _MQ_assoc(" select *, indr.in_name from smart_point_log as pl left join smart_individual as indr on (pl.pl_inid = indr.in_id) " . $orderby);
}

// th 생성
function add_table_th($title, $style='') {

	return '<th'.(trim($style) != ''?' style="'.$style.'"':null).'>'.strip_tags($title).'</th>';
}

// 2020-01-14 SSJ :: 엑셀 다운로드 항목 설정
$th = array(
	'회원ID'=>array(
		'key'=>'pl_inid',
		'required'=>'Y',
		'width'=>'210'
	),
	'회원명'=>array(
		'key'=>'in_name',
		'required'=>'Y',
		'width'=>'210'
	),
	'제목'=>array(
		'key'=>'pl_title',
		'required'=>'Y',
		'width'=>'195'
	),
	'지급전 적립금'=>array(
		'key'=>'pl_point_before',
		'required'=>'Y',
		'width'=>'195'
	),
	'지급 적립금'=>array(
		'key'=>'pl_point',
		'required'=>'Y',
		'width'=>'320',
	),
	'지급후 적립금'=>array(
		'key'=>'pl_point_after',
		'required'=>'Y',
		'width'=>'0',
		'hide'=>'Y' // 업로드 시 타이틀 비노출
	),
	'지급상태'=>array(
		'key'=>'pl_status',
		'required'=>'Y',
		'width'=>'0',
		'hide'=>'Y' // 업로드 시 타이틀 비노출
	),
	'지급예정일'=>array(
		'key'=>'pl_appdate',
		'required'=>'Y',
		'width'=>'90',
	),
	'등록일'=>array(
		'key'=>'pl_rdate',
		'required'=>'Y',
		'width'=>'90',
	),
	'비고'=>array(
		'key'=>'pl_memo',
		'required'=>'Y',
		'width'=>'90',
	)
);

?>
<table border="1">
	<thead>
		<tr>
			<?php
			foreach($th as $k=>$v) {
				echo add_table_th($k, ($v['required']=='Y'?'background-color:#F79646; color:#fff':null));
			}
			?>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach($pr as $k=>$v) {

			// 비고사항
			$v['pl_memo'] = "";

			// 상태에 따른 처리
			if($v['pl_status'] <> 'Y'){
				$v['pl_point_before'] = '';
				$v['pl_point_after'] = '';
			}else{
				if($v['pl_point'] <> $v['pl_point_apply']){
					// 비고사항
					$v['pl_memo'] = "보정 : ".($v['pl_point_apply']-$v['pl_point']);
				}
			}

			// 타이틀
			$v['pl_title'] = strip_tags($v['pl_title']);

			// 상태값
			if($v['pl_status']=='Y') $v['pl_status'] = '적립완료';
			else if($v['pl_status']=='C') $v['pl_status'] = '적립취소';
			else $v['pl_status'] = '적립예정';

			// 날짜 형식
			$v['pl_appdate'] = date('Y.m.d', strtotime($v['pl_appdate']));
			$v['pl_rdate'] = date('Y.m.d', strtotime($v['pl_rdate']));

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