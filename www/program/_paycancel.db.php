<?php
/*
* [하이센스3.0 결제취소파일 일원화 패치]  DB 수정
* http://{도메인}/program/_paycancel.db.php
*/
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');



// "결제대기 주문 자동취소 설정" 메뉴 추가
$chk = _MQ(" select count(*) as cnt from smart_admin_menu where am_depth = 3 and am_link = '_config.auto_cancel.php' "); // 등록여부 체크
if($chk['cnt'] == 0){
    $r = _MQ(" select * from smart_admin_menu where am_depth = 2 and replace(am_name, ' ' , '') = '결제관련설정' "); // 결제관련설정 메뉴 추출
    $r2 = _MQ(" select max(am_idx) as idx from smart_admin_menu where am_depth = 3 and am_parent = '". $r['am_parent'] .",". $r['am_uid'] ."' "); // 결제관련설정 메뉴 추출
    $que = "
        insert into smart_admin_menu set
        am_idx = '". ($r2['idx']+1) ."'
        ,am_depth = 3
        ,am_view = 'Y'
        ,am_parent = '". $r['am_parent'] .",". $r['am_uid'] ."'
        ,am_name = '결제대기 주문 자동취소 설정'
        ,am_link = '_config.auto_cancel.php'
    ";
    _MQ_noreturn($que);

    ViewArr('[결제대기 주문 자동취소 설정] 관리자 메뉴가 추가되었습니다.');

}else{
    ViewArr('[결제대기 주문 자동취소 설정] 이미 추가된 관리자 메뉴입니다.');
}




// db항목체크
$table = 'smart_setup';
$column = 's_order_auto_cancel_term';
$column_data = array('Field'=>$column , 'Type'=>'INT(10)' , 'Null'=>'NO' , 'Default'=>'1' , 'Extra'=>'COMMENT \'주문 자동취소 일자 설정\'');
$isField = IsField($table, $column);
if($isField === false){

	AddFeidlUpdate($table, $column_data);

	ViewArr('['.$table.'->'.$column.'] 항목이 추가되었습니다.');
}else{
	ViewArr('['.$table.'->'.$column.'] 이미 추가된 항목입니다.');
}


echo '<a href="/" style="">[홈으로]</a>';

