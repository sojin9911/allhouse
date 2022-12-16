<?php

	// 경로정의
	define("PATH_MOBILE_TOTALADMIN",	"/addons/m.totalAdmin");	// 모바일 관리자 기본 PATH


	// 접속 메뉴 체크 - 상품목록 , 주문목록, 1:1문의
	$arr_menu_link = array( "/totalAdmin/_product.list.php" , "/totalAdmin/_order.list.php" , "/totalAdmin/_request.list.php?pass_menu=inquiry");


	## JJC004 - 모바일 관리자페이지
	//include_once( dirname(__FILE__)."/../../inc.php");
	if(!$_SERVER['DOCUMENT_ROOT']) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../'); // dirname(__FILE__) 다음 경로 주의
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');






	################# 모바일 관리자 페이지 전용 함수 #################

	// 모바일 관리자 페이지리스팅 함수
	function pagelisting_mobile_totaladmin($cur_page, $total_page, $n, $url , $depth=null) {
		$page_unit_limit = "5"; //노출페이지 개수
		$start_page = ( ( (int)( ($cur_page - 1 ) / $page_unit_limit ) ) * $page_unit_limit ) + 1;
		$end_page = $start_page + ($page_unit_limit - 1);
		if($end_page >= $total_page) $end_page = $total_page;
		if(!$end_page) $end_page=1;
		if($total_page > 1){
			$retValue = "
				<div class='cm_paginate'>
					<span class='inner'>
						<a href='". ($cur_page > 1 ? $url . ($cur_page-1) : "#none") ."' class='prevnext' title='이전'><span class='arrow'></span></a>
			";
			for($k=$start_page;$k<=$end_page;$k++){
				$retValue .= "<a href='".($cur_page != $k ? trim($url . $k) : "#none")."' class='number ".($cur_page != $k ? "" : "hit")."'>" . $k . "</a>";
			}
			$retValue .= "
						<a href='". ($cur_page < $total_page ? $url . ( $cur_page + 1 ) : "#none") ."' class='prevnext' title='다음'><span class='arrow'></span></a>
					</span>
				</div>
			";
		}
		return $retValue;
	}


	## _InputRadio_totaladmin( 이름 , 배열 , 정해진 값 , 이벤트 , 정해진(지정)배열 , )
	function _InputRadio_totaladmin( $_name , $_arr , $_chk , $_event , $_arr2 ) {
		if( sizeof($_arr2) >0 ) {$arr_appname = $_arr2;}
		else {$arr_appname = $_arr;}
		foreach( $_arr as $k=>$v ){
			$_str .= "<label for='${_name}{$v}'><input type='radio' name='" . $_name . "' value='" . $v . "' ". $_event ." ". ( $_chk == $v ? "checked" : "") ."/>" . $arr_appname[$k] ."</label>" ;
		}
		return $_str;
	}

	
	## _InputCheckbox_totaladmin( 이름 , 배열 , 정해진 값(반드시 배열형태) , 이벤트 , 정해진(지정)배열 , )
	function _InputCheckbox_totaladmin( $_name , $_arr , $_chk , $_event , $_arr2 ) {
		if( sizeof($_arr2) >0 ) { $arr_appname = $_arr2; }
		else { $arr_appname = $_arr; }
		foreach( $_arr as $k=>$v ){
			// 배열값이 1개일경우 따로 분류하여 처리한다.
			if(sizeof($_arr)>1) {
				$_str .= "<label for='${_name}{$v}'><input type='checkbox' name='${_name}[]' value='{$v}' ". ( @in_array($v , $_chk ) ? "checked" : "") ." />" . $arr_appname[$k] ."</label>";
			} 
			else {
				$_str .= "<label for='${_name}{$v}'><input type='checkbox' name='${_name}' value='{$v}' ". ( $v == $_chk ? "checked" : "") ." />" . $arr_appname[$k] ."</label>";
			}
		}
		return $_str ;
	}

	// 모바일 관리자 페이지 항목 설명
	// app_tag => blue / orange (두가지 타입) -- 도움말 공간 dt는 주황색 dd는 파란색
	function _DescStr_mobile_totaladmin($str , $app_tag="blue"){
		$_tag = ( $app_tag == "blue" ? "dd" : "dt");
		return "<div class='guide_box'><dl><". $_tag .">" . $str . "</". $_tag ."></dl></div>";
	}
	
	################# 모바일 관리자 페이지 전용 함수 #################
?>