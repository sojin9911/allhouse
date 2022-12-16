<?php
include_once('inc.php');

// 기본처리
if($_mode == 'insert') { // 계좌 추가

	$SD = _MQ(" select max(bs_idx)+1 as max from smart_bank_set ");
	_MQ_noreturn(" insert into smart_bank_set set bs_bank_name = '{$_bank_name}', bs_user_name = '{$_user_name}', bs_bank_num = '{$_bank_num}', bs_idx = '".($SD['max']?$SD['max']:1)."' ");

	// 순서 다시 맞춤
	_MQ_noreturn(" select @bs_idx:=0; ");
	_MQ_noreturn(" update smart_bank_set set bs_idx=@bs_idx:=@bs_idx+1 order by bs_idx ");
}
else if($_mode == 'modify') { // 선택수정

	if(count($_uid) <= 0) error_msg('처리할 데이터를 1개 이상 선택 바랍니다.');
	foreach($_uid as $k=>$v) {
		_MQ_noreturn(" update smart_bank_set set bs_bank_name = '{$_bank_name[$k]}', bs_user_name = '{$_user_name[$k]}', bs_bank_num = '{$_bank_num[$k]}' where bs_uid = '{$k}' ");
	}
}
else if($_mode == 'delete') { // 선택삭제

	if(count($_uid) <= 0) error_msg('처리할 데이터를 1개 이상 선택 바랍니다.');
	foreach($_uid as $k=>$v) {
		_MQ_noreturn(" delete from smart_bank_set where bs_uid = '{$k}' ");
	}

	// 순서 다시 맞춤
	_MQ_noreturn(" select @bs_idx:=0; ");
	_MQ_noreturn(" update smart_bank_set set bs_idx=@bs_idx:=@bs_idx+1 order by bs_idx ");
}
else if($_mode == 'ind_modify') { // 개별수정

	_MQ_noreturn(" update smart_bank_set set bs_bank_name = '{$_bank_name}', bs_user_name = '{$_user_name}', bs_bank_num = '{$_bank_num}' where bs_uid = '{$_uid}' ");
}
else if($_mode == 'ind_delete') { // 개별삭제

	_MQ_noreturn(" delete from smart_bank_set where bs_uid = '{$_uid}' ");

	// 순서 다시 맞춤
	_MQ_noreturn(" select @bs_idx:=0; ");
	_MQ_noreturn(" update smart_bank_set set bs_idx=@bs_idx:=@bs_idx+1 order by bs_idx ");
}

// 정렬처리
else if(in_array($_mode, array('sort_up', 'sort_down'))) { // 위로 & 아래로

	// 현재 idx기준 이전값과 이후 값을 얻는다.
	$SD = array(
		'index'=>array($_uid, $_idx)
	);
	$SortData = _MQ_assoc("
		select
			s1.bs_idx as idx,
			s1.bs_uid as uid,
			s2.type
		from
			smart_bank_set as s1 inner join (
				select 'prev' as type, max(bs_idx) as bs_idx from smart_bank_set where bs_idx < {$_idx}
				union all
				select 'next' as type, min(bs_idx) as bs_idx from smart_bank_set where bs_idx > {$_idx}
			) s2 on s1.bs_idx = s2.bs_idx;
	");
	foreach($SortData as $k=>$v) {
		$SD[$v['type']] = array($v['uid'], $v['idx']);
	}

	// 데이터 처리
	if($_mode == 'sort_up') { // 위로

		if(!$SD['prev']) die('첫번째 데이터 입니다.');
		_MQ_noreturn(" update smart_bank_set set bs_idx = bs_idx-1 where bs_uid = '{$SD['index'][0]}' ");
		_MQ_noreturn(" update smart_bank_set set bs_idx = bs_idx+1 where bs_uid = '{$SD['prev'][0]}' ");
	}
	else { // 아래로

		if(!$SD['next']) die('마지막 데이터 입니다.');
		_MQ_noreturn(" update smart_bank_set set bs_idx = bs_idx+1 where bs_uid = '{$SD['index'][0]}' ");
		_MQ_noreturn(" update smart_bank_set set bs_idx = bs_idx-1 where bs_uid = '{$SD['next'][0]}' ");

	}
	die('success');
}
else if(in_array($_mode, array('sort_top', 'sort_bottom'))) { // 맨위로 & 맨아래로

	// 현재 idx기준 이전값과 이후 값을 얻는다.
	$SD = array(
		'index'=>array($_uid, $_idx)
	);
	$SortData = _MQ_assoc("
		select
			s1.bs_idx as idx,
			s1.bs_uid as uid,
			s2.type
		from
			smart_bank_set as s1 inner join (
				select 'prev' as type, max(bs_idx) as bs_idx from smart_bank_set where bs_idx < {$_idx}
				union all
				select 'next' as type, min(bs_idx) as bs_idx from smart_bank_set where bs_idx > {$_idx}
			) s2 on s1.bs_idx = s2.bs_idx;
	");
	foreach($SortData as $k=>$v) {
		$SD[$v['type']] = array($v['uid'], $v['idx']);
	}

	// 순위조정
	if($_mode == 'sort_top') { // 맨위로

		if(!$SD['prev']) die('첫번째 데이터 입니다.');
		_MQ_noreturn(" update smart_bank_set set bs_idx = 0 where bs_idx = {$_idx} ");
		//_MQ_noreturn(" update smart_bank_set set bs_idx = bs_idx+1 where bs_idx < {$_idx} ");
	}
	else { // 맨아래로

		if(!$SD['next']) die('마지막 데이터 입니다.');
		_MQ_noreturn(" update smart_bank_set set bs_idx = 9999999999 where bs_idx = {$_idx} ");
		//_MQ_noreturn(" update smart_bank_set set bs_idx = bs_idx-1 where bs_idx > {$_idx} ");
	}

	// 순서 다시 맞춤
	_MQ_noreturn(" select @bs_idx:=0; ");
	_MQ_noreturn(" update smart_bank_set set bs_idx=@bs_idx:=@bs_idx+1 order by bs_idx ");

	die('success');
}

// 설정페이지 이동
error_loc('_config.none_bank.php');