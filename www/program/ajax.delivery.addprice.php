<?php
# 게시글 처리 프로세스
include_once(dirname(__FILE__).'/inc.php');
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


// 추가배송비를 적용한다면 
// 넘어온주소와 매칭되는 도서산간지역 검색
// 배송비설정에따른 추가배송비 적용여부 - json - 형식으로 전송

$arr_addr = array(); // 지번주소 배열화
$arr_addr_doro = array(); // 도로명주소 배열화

// 지번주소 추출
unset($ex);
if($app_addr){
	$ex = explode(" " , $app_addr);
	$arr_addr = array_filter(array_merge($ex, array($app_addr2)));
}

// 도로명주소 추출
unset($ex);
if($app_addr_doro){
	$ex = explode(" " , $app_addr_doro);
	$arr_addr_doro = array_filter(array_merge($ex, array($app_addr2)));
}
// 매칭여부체크
$trigger = false;
$addprice = 0; // 추가배송비

// 도로명주소 검색
if(sizeof($arr_addr_doro)>0){
	$max = sizeof($arr_addr_doro) -1;
	for($i = $max; $i >= 0; $i--){

		if($trigger) break; // 매칭된 결과가 있으면 종료

		$_tmp_addr = "";
		for($j = 0; $j <= $i; $j++ ){
			$_tmp_addr = implode(" ", array_filter(array($_tmp_addr, $arr_addr_doro[$j])));
		}
		
		$r = _MQ(" select da_price from smart_delivery_addprice where da_addr like '". $_tmp_addr ."' ");
		if($r['da_price']>0){
			$trigger = true;
			$addprice = $r['da_price'];
		}
	}
}

// 지번주소 검색
if(sizeof($arr_addr)>0 && !$trigger){
	$max = sizeof($arr_addr) -1;
	for($i = $max; $i >= 0; $i--){

		if($trigger) break; // 매칭된 결과가 있으면 종료

		$_tmp_addr = "";
		for($j = 0; $j <= $i; $j++ ){
			$_tmp_addr = implode(" ", array_filter(array($_tmp_addr, $arr_addr[$j])));
		}
		
		$r = _MQ(" select da_price from smart_delivery_addprice where da_addr like '". $_tmp_addr ."' ");
		if($r['da_price']>0){
			$trigger = true;
			$addprice = $r['da_price'];
		}
	}
}


echo $addprice;
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행