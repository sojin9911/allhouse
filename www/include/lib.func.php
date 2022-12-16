<?PHP
// json 을 호출하지 않으면 php 5.2이하 버전의 경우 lgpay모듈에서 에러가 발생될수 있다. 중복호출을 막기 위해. lgpay에서 호출되는 json 을 주석처리 했기 때문..
require_once(dirname(__FILE__)."/JSON.php");

if(!function_exists('error_msg')) {
	function error_msg($msg) {
		echo "<script language='javascript'>alert('$msg');history.back();</script>";
		exit;
	}
}
if(!function_exists('error_alt')) {
	function error_alt($msg,$key=false) {
		if( $key !== false){ $focusPrint = "  parent.document.getElementsByName('".$key."')[0].focus();  "; }
		echo "<script language='javascript'>alert('$msg');".$focusPrint."</script>";
		exit;
	}
}
if(!function_exists('error_msgPopup')) {
	function error_msgPopup($msg) {
		echo "<script language='javascript'>alert('$msg');opener.location.reload();window.close();</script>";
		exit;
	}
}
if(!function_exists('error_loc_msgPopup')) {
	function error_loc_msgPopup($loc,$msg) {
		echo "<script language='javascript'>alert('$msg');opener.location.href=('${loc}');window.close();</script>";
		exit;
	}
}
if(!function_exists('error_msgPopup_s')) {
	function error_msgPopup_s($msg) {
		echo "<script language='javascript'>alert('$msg');window.close();</script>";
		exit;
	}
}
if(!function_exists('error_loc_msg')) {
	function error_loc_msg($loc,$msg,$target=null) {
		if($target) { echo "<script language='javascript'>alert('$msg');".$target.".location.href=('${loc}');</script>"; }
		else { echo "<script language='javascript'>alert('$msg');location.href=('${loc}');</script>"; }
		exit;
	}
}
if(!function_exists('error_loc')) {
	function error_loc($loc,$target=null) {
		if($target) { echo "<script language='javascript'>".$target.".location.href=('${loc}');</script>"; }
		else { echo "<meta http-equiv='Refresh' content='0;url=${loc}'>"; }
		exit;
	}
}
if(!function_exists('error_frame_loc')) {
	function error_frame_loc($loc) {
		echo "<script language='javascript'>parent.location.href=('${loc}');</script>";
		exit;
	}
}
if(!function_exists('error_frame_loc_msg')) {
	function error_frame_loc_msg($loc,$msg) {
		echo "<script language='javascript'>alert('$msg');parent.location.href=('${loc}');</script>";
		exit;
	}
}
if(!function_exists('error_frame_reload')) {
	function error_frame_reload($msg) {
		echo "<script language='javascript'>alert('$msg');parent.location.reload();</script>";
		exit;
	}
}
if(!function_exists('error_frame_reload_nomsg')) {
	function error_frame_reload_nomsg() {
		echo "<script language='javascript'>parent.location.reload();</script>";
		exit;
	}
}
if(!function_exists('error_light_msg')) {
	function error_light_msg($class , $msg) {
		echo "<SCRIPT>alert('".$msg."');$('.".$class."').trigger('close');</SCRIPT>";
		exit;
	}
}
// === 비회원 구매설정을 위한 추가 kms 2019-06-25 ====
if(!function_exists('error_confirm_msg')) {
	function error_confirm_msg($loc,$msg, $loc2=null) {
		echo "<script language='javascript'> if (confirm('$msg') ) { parent.location.href=('${loc}');}";
		if ($loc2 != null) { echo "else { parent.location.href=('${loc2}'); }</script>";}
		else { echo "</script>"; }

		exit;
	}
}

function rm_str($num){		return preg_replace("/[^[:digit:]]/i","",$num);	}
function rm_numstar($num){		return preg_replace("/[[:digit:]]/i","*",$num);	}
function rm_comma($number) {		return preg_replace("/,/i","",$number);	}
function rm_renter($num){		return preg_replace("/\r/i","",$num);	}
function rm_nenter($num){		return preg_replace("/\n/i","",$num);	}
function rm_enter($num) {		return preg_replace("/\r\n/i" , "" , $num);	}
function rm_tab($num){		return preg_replace("/\t/i","",$num);	}
function rm_space($num){		return preg_replace("/ /i","",$num);	}
function rm_nbsp($num){		return preg_replace("/&nbsp;/i","",$num);	}


// 일반적으로 회원 아이디 출력할때 아이콘을 붙일때 사용한다.
function printUserID($id) {
	global $_COOKIE;

	$user_info = _individual_info($id);

	return $id;


}

// 공백 제거, 줄바꿈도 제거.
function trim2($text) {
	$text = str_replace("\n","",$text);
	$text = str_replace("\r","",$text);
	$text = str_replace(" ","",$text);
	$text = trim($text);
	return $text;
}

## 문자 자르기 - 배열로 리턴합니다.
function mb_cut_str(&$contents,$cut_len=0,$cut_num=1) {

	 /// 문자열 길이
	 $contents = iconv("UTF-8","EUC-KR" , $contents);
	 $cont_len = mb_strlen($contents );

	 /// setting default values
	 if($cut_len <= 0) $cut_len = $cont_len;
	 else              $cut_len = intval($cut_len);
	 if($cut_num <= 0)    $cut_num = 1;
	 elseif($cut_num > 1) $cut_num = intval($cut_num);

	 /// 문자열을 자르기 위한 시작위치
	 $start_pos = 0;

	 /// 자를 갯수만큼 loop
	 for($cnt=1; $cnt <= $cut_num; $cnt++) {
		  /// 다음번에 자를 문자열이 남아 있을때

		  if($cont_len > $start_pos ) {
				$s_flag = false;

				$chk_laststr1 = ord(mb_substr($contents,$cut_len-1,1));
				if( $chk_laststr1 > 127 ) {
					$cut_len --;
				}

				$tmp_str = mb_substr($contents,$start_pos,$cut_len);
				$tmp_pos = mb_strrpos($tmp_str,' ');

				if(!$tmp_pos) $tmp_pos = 0;

				$arr_cont[$cnt] = iconv("EUC-KR" , "UTF-8",$tmp_str);
				$start_pos += $cut_len;


				 /// 문자열을 $cut_num 갯수까지 자른후, 나머지를 array의 마지막에 넣음
				 if($cnt == $cut_num) {
						$arr_cont[$cnt+1] = iconv("EUC-KR" , "UTF-8",mb_substr($contents,$start_pos));
				 }
		  }
		  /// 다음번에 더이상 자를 문자열이 없으므로 for loop 빠져나감
		  else {
				 $arr_cont[$cnt] = iconv("EUC-KR" , "UTF-8",mb_substr($contents,$start_pos));
				 break;
		  }
	 }

	 /// array첫번째에 실제로 문자열을 자른 갯수를 넣는다
	 $arr_cont[0] = $cnt;

	 return $arr_cont;

}


// 메일발송함수
// mailer( 받을메일주소 , 메일제목 , 메일내용 )
function mailer($_email, $_title, $_content, $cc=array()) {
	global $siteInfo;
	$headers = '';
	$_title = '=?UTF-8?B?'.base64_encode($_title).'?=';
	//if(!preg_match("/@daum.net|@hamail.net/i" , $_email)) $headers .= "From: =?UTF-8?B?".base64_encode($siteInfo['s_sms_sitename'])."?= <{$siteInfo['s_ademail']}>\r\n";
	$headers .= "From: ". (!preg_match("/@daum.net|@hamail.net/i" , $_email) ? "=?UTF-8?B?".base64_encode($siteInfo['s_sms_sitename'])."?=" : $siteInfo['s_sms_sitename']) ." <{$siteInfo['s_ademail']}>\r\n";
	$headers .= "Content-Type: text/html; charset=utf-8\r\n";
	$headers .= "Return-Path: {$siteInfo['s_ademail']}\r\n";
	if(count($cc) > 0) $headers .= "Cc: <". implode(">,<" , $cc) .">\r\n";
	return @mail($_email, $_title, $_content, $headers, "-f {$siteInfo['s_ademail']}");
}


// 샵용 메일 컨텐츠를 추출한다.
// 인자 : 컨텐츠 - !!컨텐츠에는 타이틀을 포함한 html을 입력 바랍니다.!!
// 리턴 : 메일내용
function get_mail_content($mailling_content) {
	global $_SERVER, $siteInfo, $system, $_skin, $deny_content;

	// 메일링 로고 추출
	$mailing_url = $system['url']; // SSJ : 메일링 도메인 변경 : 2021-02-24
	$banner_info = info_banner('common,mailing,not_set_view,not_set_term,not_set_link_target', 1, 'data'); // 공통 메일링 상단 로고
	if(!$banner_info[0]['b_img']) $banner_info = info_banner($_skin.',site_top_logo', 1, 'data'); // 로고가 없다면 사이트 로고 호출
	if(!$banner_info[0]['b_img']) $banner_info[0]['b_img'] = '../../images/mailing/logo.jpg'; // 모두 없다면 솔루션 기본 로고 출력
	$mailling_logo = $mailing_url.IMG_DIR_BANNER.$banner_info[0]['b_img'];

	$mail_body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
		<head>
		<title>'.htmlspecialchars($siteInfo['s_glbtlt']).'</title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		</head>
		<body>
		<div style="overflow:hidden; padding:5px;">
			<div style="max-width:700px; margin:0 auto; background:#fff; border:1px solid #ddd; box-sizing:border-box;border-collapse: inherit;">

				<!-- ● Common Box / 헤더 -->
				<table style="width:100%;border-spacing:0; font-size:12px; font-family:\'돋움\',Dotum; line-height:17px">
					<tbody>
						<tr>
							<!-- [PC]공통 : 메일링 상단 로고 (가로 280 이하 * 세로 70 이하) -->
							<!-- 메일링 로고 따로 등록 / 없으면 헤더 기본로고 노출 -->
							<td style="background:#fff; padding:23px 30px; border-bottom:1px solid #ddd">
								<a href="'.$mailing_url.'" style="max-width:280px; display:inline-block" target="_blank">
									<img src="'.$mailling_logo.'" alt="'.addslashes(htmlspecialchars($siteInfo['s_glbtlt'])).'" style="max-width:100%; max-height:70px; border:0 !important;"/>
								</a>
							</td>
						</tr>
					</tbody>
				</table>
				<!-- / Common Box -->



				<!-- 컨텐츠박스 -->
				'.$mailling_content.'
				<!-- / 컨텐츠박스 -->



				<!-- ● Common Box / 푸터 -->
				<table style="width:100%;border-spacing:0; font-size:12px; font-family:\'돋움\',Dotum; line-height:17px">
					<tbody>
						<tr>
							<td style="text-align:center">
								<!-- 홈페이지 바로가기 버튼 -->
								<a href="'.$mailing_url.'" style="background:#505258; font-size:13px; font-weight:600; color:#fff; padding:13px 28px;text-decoration:none;display:inline-block;margin:20px 0 50px" target="_blank">홈페이지 바로가기</a>
							</td>
						</tr>
						<tr>
							<!-- 하단 쇼핑몰 정보 입력 -->
							<td style="background:#f5f5f5; padding:30px; color:#666; line-height:15px; letter-spacing:0px">
								본 메일은 발신전용으로 회신 할 경우 답변되지 않습니다. 문의사항은 <a href="'.$mailing_url.'/?pn=service.main" style="color:#666" target="_blank">고객센터</a>('.$siteInfo['s_glbtel'].')로 연락을 주십시오.<br/>
								<!-- 이메일 수신거부 문구 -->
								'.$deny_content.'
								<br/>
								<!-- 쇼핑몰 정보 -->
								소재지 : '.$siteInfo['s_company_addr'].' 대표 : '.$siteInfo['s_ceo_name'].'<br/>
								사업자 등록번호 : '.$siteInfo['s_company_num'].' 통신판매업신고 : '.$siteInfo['s_company_snum'].'<br/>
								고객센터 : '.$siteInfo['s_glbtel'].' '.($siteInfo['s_fax']?'팩스 : '.$siteInfo['s_fax'].' ':null).'개인정보관리책임 : '.$siteInfo['s_privacy_name'].'<br/><br/>
								<!-- Copyright -->
								<strong style="letter-spacing:0px; font-weight:400; font-size:12px">Copyright ⓒ '.htmlspecialchars($siteInfo['s_adshop']).' All rights reserved</strong>
							</td>
						</tr>
					</tbody>
				</table>
				<!-- / Common Box -->
			</div>
		</div>
		</body>
		</html>
	';

	return $mail_body;
}

// - UTF-8 한글 자르기 최종 함수 ::: 2013-01-08 정준철---
function cutstr_old($msg,$cut_size,$tail="...") {
	$cut_size = ($cut_size<=0 ? 100 : $cut_size) ;
	$han = $eng = $tmp_i =0; // 한글 , 영숫어 , 임시 i 갯수
	for($i=0;$i<$cut_size;$i++) {
		if(@ord($msg[$tmp_i])>127) {
			$han++;
			$tmp_i += 3;
		}
		else {
			$eng++;
			$tmp_i ++;
		}
	}
	$cut_size = ceil($han * 2/ 3) * 3 + $eng ;
	$snowtmp = "";//return string
	for($i=0;$i<$cut_size;$i++) {
		if(ord($msg[$i]) <= 127){
			$snowtmp.=$msg[$i];
		}
		else {
			$snowtmp .= $msg[$i].$msg[($i+1)].$msg[($i+2)];
			$i+=2;
		}
	}
	return $snowtmp . ( $msg != $snowtmp ? $tail : "");
}
// - UTF-8 한글 자르기 최종 함수 ::: 2013-01-08 정준철---



// - UTF-8 한글 자르기 최종 함수 ::: 2013-05-10 정준철---
 function cutstr_new_old($msg,$cut_size,$tail="...") {
	$han = $eng = $tmp_i =0; // 한글 , 영숫어 , 임시 i 갯수
	for($i=0;$i<$cut_size;$i++) {
	 if(@ord($msg[$tmp_i])>127) {
		$han++;
		$tmp_i += 3;
	 }
	 else {
		$eng++;
		$tmp_i ++;
	 }
	}
	$cut_size = $han * 3 + $eng ;
	$snowtmp = "";//return string
	for($i=0;$i<$cut_size;$i++) {
	 if(ord($msg[$i]) <= 127){
		$snowtmp.=$msg[$i];
	 }
	 else {
		$snowtmp .= $msg[$i].$msg[($i+1)].$msg[($i+2)];
		$i+=2;
	 }
	}
	return $snowtmp . ( $msg != $snowtmp ? $tail : "");
 }

 function utf8_length($str) {
   $len = strlen($str);
   for ($i = $length = 0; $i < $len; $length++) {
	$high = ord($str{$i});
	if ($high < 0x80)//0<= code <128 범위의 문자(ASCII 문자)는 인덱스 1칸이동
	 $i += 1;
	else if ($high < 0xE0)//128 <= code < 224 범위의 문자(확장 ASCII 문자)는 인덱스 2칸이동
	 $i += 2;
	else if ($high < 0xF0)//224 <= code < 240 범위의 문자(유니코드 확장문자)는 인덱스 3칸이동
	 $i += 3;
	else//그외 4칸이동 (미래에 나올문자)
	 $i += 4;
   }
   return $length;
 }
 function cutstr($str, $chars, $tail = '...') {
   if (utf8_length($str) <= $chars)//전체 길이를 불러올 수 있으면 tail을 제거한다.
	$tail = '';
   else
	$chars -= utf8_length($tail);//글자가 잘리게 생겼다면 tail 문자열의 길이만큼 본문을 빼준다.
   $len = strlen($str);
   for ($i = $adapted = 0; $i < $len; $adapted = $i) {
	$high = ord($str{$i});
	if ($high < 0x80)
	 $i += 1;
	else if ($high < 0xE0)
	 $i += 2;
	else if ($high < 0xF0)
	 $i += 3;
	else
	 $i += 4;
	if (--$chars < 0)
	 break;
   }
   return trim(substr($str, 0, $adapted)) . $tail;
 }

 function cutstr_new($str, $chars, $tail = '...') {
   if (utf8_length($str) <= $chars)//전체 길이를 불러올 수 있으면 tail을 제거한다.
	$tail = '';
   else
	$chars -= utf8_length($tail);//글자가 잘리게 생겼다면 tail 문자열의 길이만큼 본문을 빼준다.
   $len = strlen($str);
   for ($i = $adapted = 0; $i < $len; $adapted = $i) {
	$high = ord($str{$i});
	if ($high < 0x80)
	 $i += 1;
	else if ($high < 0xE0)
	 $i += 2;
	else if ($high < 0xF0)
	 $i += 3;
	else
	 $i += 4;
	if (--$chars < 0)
	 break;
   }
   return trim(substr($str, 0, $adapted)) . $tail;
 }
 // - UTF-8 한글 자르기 최종 함수 ::: 2013-01-08 정준철---


// 페이지네이트
function pagelisting($cur_page, $total_page, $n, $url , $depth=null) {
	$start_page = ( ( (int)( ($cur_page - 1 ) / 10 ) ) * 10 ) + 1;
	$end_page = $start_page + 9;

	//// LDD: 2017-11-22 listpg가 계속 주소 뒤에 붙는 현상 수정
	//$url_rebuild = $url;
	//parse_str($url_rebuild, $url_rebuild);
	//$url_rebuild = http_build_query($url_rebuild);
	//$url = str_replace('?=&', '?', urldecode($url_rebuild));
	// SSJ: 2020-02-12 URL 중간에 다른 변수가 올때 listpg= 사라지는 현상 수정
	$uri_prefix = '';
	if(substr($url_rebuild, 0, 1) == '/') { // 첫글자가 /로 시작하면 접두사에 저장하고 제거한다.
		$url_rebuild = substr($url_rebuild, 1);
		$uri_prefix .= '/';
	}
	if(substr($url_rebuild, 0, 1) == '?') { // 첫글자가 ?로 시작하면 접두사에 저장하고 제거한다.
		$url_rebuild = substr($url_rebuild, 1);
		$uri_prefix .= '?';
	}
	$arr = parse_url($url);
	$ex = array_values(array_filter(explode("&" , $arr['query'])));
	$arr_re = array();
	if(count($ex) > 0){
		foreach($ex as $k=>$v){
			$eex = array_filter(explode("=" , $v));
			unset($arr_re[$eex[0]]);
			$arr_re[$eex[0]] = $eex[1];
		}
	}
	$url_rebuild = "";
	if(count($arr_re) > 0){
		foreach($arr_re as $k=>$v){
			if($url_rebuild <> "") $url_rebuild .= "&";
			$url_rebuild .= $k."=".$v;
		}
	}
	$url = $uri_prefix.'?'.$url_rebuild;

	if($end_page >= $total_page) $end_page = $total_page;
	if(!$end_page) $end_page=1;
	$retValue = "	<span class='lineup'>";
	if($cur_page > 1) {
		$retValue .= "<span class='nextprev'>";
		$retValue .= "<span class='btn click'><span class='no'><span class='icon ic_first'></span></span><a href='" .$url . "1' class='ok' title='처음' ><span class='icon ic_first'></span></a></span>";
		$retValue .= "<span class='btn click'><span class='no'><span class='icon ic_prev'></span></span><a href='" . $url . ($cur_page-1) . "' class='ok' title='이전' ><span class='icon ic_prev'></span></a></span>";
		$retValue .= "</span>";
	} else {
		$retValue .= "<span class='nextprev'>";
		$retValue .= "<span class='btn'><span class='no'><span class='icon ic_first'></span></span><a href='" .$url . "1' class='ok' title='처음' ><span class='icon ic_first'></span></a></span>";
		$retValue .= "<span class='btn'><span class='no'><span class='icon ic_prev'></span></span><a href='" . $url . ($cur_page-1) . "' class='ok' title='이전' ><span class='icon ic_prev'></span></a></span>";
		$retValue .= "</span>";
	}

	$retValue .= "<span class='number'>";
	for($k=$start_page;$k<=$end_page;$k++)
	if($cur_page != $k) $retValue .= "<a href='" . $url . $k . "'>${k}</a>";
	else $retValue .= "<a href='#none' onclick='return false;' class='hit'>${k}</a>";
	$retValue .= "</span>";

	if($cur_page < $total_page) {
		$retValue .= "<span class='nextprev'>";
		$retValue .= "<span class='btn click'><span class='no'><span class='icon ic_next'></span></span><a href='" . $url . ($cur_page+1) . "' class='ok' title='다음' ><span class='icon ic_next'></span></a></span>";
		$retValue .= "<span class='btn click'><span class='no'><span class='icon ic_last'></span></span><a href='" . $url . $total_page . "' class='ok' title='끝' ><span class='icon ic_last'></span></a></span>";
		$retValue .= "</span>";
	} else {
		$retValue .= "<span class='nextprev'>";
		$retValue .= "<span class='btn'><span class='no'><span class='icon ic_next'></span></span><a href='" . $url . ($cur_page+1) . "' class='ok' title='다음' ><span class='icon ic_next'></span></a></span>";
		$retValue .= "<span class='btn'><span class='no'><span class='icon ic_last'></span></span><a href='" . $url . $total_page . "' class='ok' title='끝' ><span class='icon ic_last'></span></a></span>";
		$retValue .= "</span>";
	}
	$retValue .= "</span>";
	return $retValue;
}
function pagelisting_mobile($cur_page, $total_page, $n, $url , $depth=null) {
	$start_page = ( ( (int)( ($cur_page - 1 ) / 5 ) ) * 5 ) + 1;
	$end_page = $start_page + 4;

	if($end_page >= $total_page) $end_page = $total_page;
	if(!$end_page) $end_page=1;

	//// LDD: 2017-11-22 listpg가 계속 주소 뒤에 붙는 현상 수정
	//$url_rebuild = $url;
	//parse_str($url_rebuild, $url_rebuild);
	//$url_rebuild = http_build_query($url_rebuild);
	//$url = str_replace('?=&', '?', urldecode($url_rebuild));
	// SSJ: 2020-02-12 URL 중간에 다른 변수가 올때 listpg= 사라지는 현상 수정
	$uri_prefix = '';
	if(substr($url_rebuild, 0, 1) == '/') { // 첫글자가 /로 시작하면 접두사에 저장하고 제거한다.
		$url_rebuild = substr($url_rebuild, 1);
		$uri_prefix .= '/';
	}
	if(substr($url_rebuild, 0, 1) == '?') { // 첫글자가 ?로 시작하면 접두사에 저장하고 제거한다.
		$url_rebuild = substr($url_rebuild, 1);
		$uri_prefix .= '?';
	}
	$arr = parse_url($url);
	$ex = array_values(array_filter(explode("&" , $arr['query'])));
	$arr_re = array();
	if(count($ex) > 0){
		foreach($ex as $k=>$v){
			$eex = array_filter(explode("=" , $v));
			unset($arr_re[$eex[0]]);
			$arr_re[$eex[0]] = $eex[1];
		}
	}
	$url_rebuild = "";
	if(count($arr_re) > 0){
		foreach($arr_re as $k=>$v){
			if($url_rebuild <> "") $url_rebuild .= "&";
			$url_rebuild .= $k."=".$v;
		}
	}
	$url = $uri_prefix.'?'.$url_rebuild;

	$retValue = "<span class='inner'>";
	if($cur_page > 1) {
		$retValue .= "<span class='nextprev'>";
		$retValue .= '
			<span class="btn click ic_first">
				<span title="처음페이지" class="no"><span class="icon "></span></span>
				<a href="'.$url.'1" title="처음페이지" class="ok "><span class="icon "></span></a>
			</span>
		';
		$retValue .= '
			<span class="btn click ic_prev">
				<span title="이전페이지" class="no"><span class="icon "></span></span>
				<a href="'.$url.($cur_page-1).'" title="이전페이지" class="ok "><span class="icon "></span></a>
			</span>
		';
		$retValue .= "</span>";
	} else {
		$retValue .= "<span class='nextprev'>";
		$retValue .= '
			<span class="btn ic_first">
				<span title="처음페이지" class="no"><span class="icon "></span></span>
				<a href="'.$url.'1" title="처음페이지" class="ok "><span class="icon "></span></a>
			</span>
		';
		$retValue .= '
			<span class="btn ic_prev">
				<span title="이전페이지" class="no"><span class="icon "></span></span>
				<a href="'.$url.($cur_page-1).'" title="이전페이지" class="ok "><span class="icon "></span></a>
			</span>
		';
		$retValue .= "</span>";
	}

	$retValue .= "<span class='number'>";
	for($k=$start_page;$k<=$end_page;$k++)
	if($cur_page != $k) $retValue .= "<a href='$url$k'>${k}</a>";
	else $retValue .= "<a href='#none' onclick='return false;' class='hit'>${k}</a>";
	$retValue .= "</span>";


	if($cur_page < $total_page) {
		$retValue .= "<span class='nextprev'>";
		$retValue .= '
			<span class="btn click ic_next">
				<span title="다음페이지" class="no"><span class="icon "></span></span>
				<a href="'.$url.($cur_page+1).'" title="다음페이지" class="ok "><span class="icon"></span></a>
			</span>
		';
		$retValue .= '
			<span class="btn click ic_last">
				<span title="마지막페이지" class="no"><span class="icon "></span></span>
				<a href="'.$url.$total_page.'" title="마지막페이지" class="ok "><span class="icon "></span></a>
			</span>
		';
		$retValue .= "</span>";
	} else {
		$retValue .= "<span class='nextprev'>";
		$retValue .= '
			<span class="btn ic_next">
				<span title="다음페이지" class="no"><span class="icon "></span></span>
				<a href="'.$url.($cur_page+1).'" title="다음페이지" class="ok "><span class="icon"></span></a>
			</span>
		';
		$retValue .= '
			<span class="btn ic_last">
				<span title="마지막페이지" class="no"><span class="icon "></span></span>
				<a href="'.$url.$total_page.'" title="마지막페이지" class="ok "><span class="icon "></span></a>
			</span>
		';
		$retValue .= "</span>";
	}

	$retValue .= "</span>";

	return $retValue;
}

function jumin_check( $ser_no1, $ser_no2) {
	if((strlen($ser_no1) != 6) || (strlen($ser_no2) != 7)) { return (1); }
	$ser = $ser_no1. "0$ser_no2";
	for($i=0; $i <14; $i++) { $a[$i] = intval($ser[$i]); }
	// 자릿수 합산
	$j = $a[0]*2+$a[1]*3+$a[2]*4+$a[3]*5+$a[4]*6+$a[5]*7+$a[7]*8+$a[8]*9+$a[9]*2+$a[10]*3+$a[11]*4+$a[12]*5;
	$j = $j % 11;
	$k = 11 - $j;
	if($k > 9) $k = $k % 10;
	$j = $a[13];

	if($j == $k) {
		// 성별확인 및 Y2K...
		if($a[7] == 1) { $sex = "M"; $Y = 1900; }
		elseif($a[7] == 2) { $sex = "F"; $Y = 1900; }
		elseif($a[7] == 3) { $sex = "M"; $Y = 2000; }
		elseif($a[7] == 4) { $sex = "F"; $Y = 2000; }
		else return(1);
		// 생년월일 확인 및 체크
		$Y = $Y + $a[0]*10 + $a[1];
		$M = $a[2]*10 + $a[3];
		if(($M == 0) || ($M >12)) return(1);
		$D = $a[4]*10 + $a[5];
		if(($D == 0) || ($D >31)) return(1);
		return(0);
	}
	else  return(1);
}

## 숫자의 글자화
function NumComp( $num ){

	$price_unit0=array("","1","2","3","4","5","6","7","8","9");
	$price_unit1=array("","십","백","천");
	$price_unit2=array("","만 ","억 ","조 ","경 ","해 ","시 ","양 ","구 ","간 ","정 ");
	$won = array();

	for( $i = strlen($num)-1; $i >= 0; $i-- ){
		$won[$i] = $price_unit0[substr($num , strlen($num)-1-$i , 1 )];
		if( $i > 0 && $won[$i] != "" ) {	 $won[$i].= $price_unit1[$i%4]; }
		if( $i % 4 == 0 ) {	$won[$i].= $price_unit2[($i/4)]; }
	}
	for( $i = strlen($num)-1; $i >= 0; $i-- ){
		if( strlen($won[$i]) == 2) { $won[$i-$i%4].="-"; }
		if( strlen($won[$i]) == 1 && $i>0) { $won[$i]=""; }
		if( $i%4 != 0 ) { $won[$i] = str_replace("일","",$won[$i]); }
	}

	$ex_won = implode("", $won);

	if( !(rm_str(substr($num , -8, 4)) > 0 ) ) {
		$ex_won = str_replace("만","",$ex_won);
	}

	return $ex_won ;
}




function myquote($string) {
	if (get_magic_quotes_gpc()==1) {
		return $string;
	} else {
		return $string;
	}
}

################################
######## 등록 가능한 파일 검사
################################
######## 등록 파일 체크 ##########################################
######## 첨부가능 파일 : doc, hwp, ppt, xls, zip, pds ##
## 타입 체크
## zip : Noname3.zip .. 1438 .. application/x-zip-compressed
## xls : 041203-은빈정보통신(x335).xls .. 97792 .. application/vnd.ms-excel
## ppt : 20041009사업계획서.ppt .. 254976 .. application/vnd.ms-powerpoint
## doc : CP검수요청서_CD등급용.doc .. 74240 .. application/msword
## pdf : file.pdf .. 381589 .. application/pdf
## hwp : 97용-계약서.hwp .. 22351 .. application/octet-stream
##############################################################
function File_Exam( $file_type , $file_name ){
	$ex_type = explode("/",$file_type);
	if( preg_match("/zip/i",$ex_type[1]) ) { $trigger = "yes"; }
	elseif( preg_match("/ms-excel/i",$ex_type[1]) ) { $trigger = "yes"; }
	elseif( preg_match("/ms-powerpoint/i",$ex_type[1]) ) { $trigger = "yes"; }
	elseif( preg_match("/msword/i",$ex_type[1]) ) { $trigger = "yes"; }
	elseif( preg_match("/pdf/i",$ex_type[1]) ) { $trigger = "yes"; }
	elseif( $ex_type[1] == "octet-stream" && strtolower(substr($file_name,-3))=="hwp" ) { $trigger = "yes"; }
	else { $trigger = "no"; }
	if( preg_match("/image/i",$ex_type[0]) ) { $trigger = "yes"; }
	return $trigger ;
}





#### 전송버튼 ####
// var 은 get방식으로 "var1=변수1&var2=변수2" 형식으로 입력하여야 함
// 추가버튼 예시) <li><a href="" class="c_btn h34 black line">복사하기</a></li>
function _submitBTN($str , $var=null , $add_btn='' , $float_btn=true, $only_list_btn=false) {
	global $pass_variable_string_url , $_PVSC;

	if(strpos($str,'?')===false) $prefix = '?';
	else $prefix = '&';

	$button_str = "";
	if($_PVSC) {
		$app_pvsc = URI_Rebuild(enc('d' , $_PVSC));
	}
	else {
		$app_pvsc = URI_Rebuild(enc('d' , $pass_variable_string_url));
	}
	// 등록폼 내부 버튼
	$button_str .= '
			<div class="c_btnbox">
				<ul>
					'.($only_list_btn === false?'<li><span class="c_btn h46 red"><input type="submit" name="" value="확인" accesskey="s" /></span></li>':null).'
					<li><a href="'. $str . ($app_pvsc||$var?$prefix:null) . $app_pvsc . ($var?"&":null) . $var .'" class="c_btn h46 black line" accesskey="l">목록</a></li>'.$add_btn.'
				</ul>
			</div>
	';
	// 등록폼 따라다니는 버튼
	if($float_btn){
		$button_str .= '
			<!-- ● 스크롤 고정 등록버튼 (등록폼에서만 스크롤 내리면 고정됨/최하단 버튼 영역에 가면 사라짐) : 등록페이지 공통 -->
			<div class="fixed_save js_fixed_save" style="display:none;">
				<div class="wrapping">
					<!-- 가운데정렬버튼 -->
					<div class="c_btnbox">
						<ul>
							'.($only_list_btn === false?'<li><span class="c_btn h34 red"><input type="submit" name="" value="확인" accesskey="s" /></span></li>':null).'
							<li><a href="'. $str . ($app_pvsc||$var?$prefix:null) . $app_pvsc . ($var?"&":null) . $var .'" class="c_btn h34 black line" accesskey="l">목록</a></li>'. str_replace(array('h46'), 'h34', $add_btn) .'
						</ul>
					</div>
				</div>
			</div>
		';
	}

	return $button_str;
}



function _submitBTNsub($float_btn=true) {
	$button_str = '';
	$button_str .= '
		<!-- 저장 -->
		<div class="c_btnbox">
			<ul>
				<li><span class="c_btn h46 red"><input type="submit" value="확인" accesskey="s" /></span></li>
			</ul>
		</div>
		<!-- 저장 -->
	';
	// 등록폼 따라다니는 버튼
	if($float_btn){
		$button_str .= '
			<!-- ● 스크롤 고정 등록버튼 (등록폼에서만 스크롤 내리면 고정됨/최하단 버튼 영역에 가면 사라짐) : 등록페이지 공통 -->
			<div class="fixed_save js_fixed_save" style="display:none;">
				<div class="wrapping">
					<!-- 가운데정렬버튼 -->
					<div class="c_btnbox">
						<ul>
							<li><span class="c_btn h34 red"><input type="submit" name="" value="확인" accesskey="s" /></span></li>
						</ul>
					</div>
				</div>
			</div>
		';
	}

	return $button_str;
}




#### 서브 타이틀 ####
function _subTITLE($str) { return "<tr><td colspan=2 bgcolor='#E3D2C8' height=30><B>${str}</B></td></tr>";}





#### select 형 input 처리 ####
## _InputSelect( 이름 , 배열 , 정해진 값 , 이벤트 , 정해진(지정)배열 , 초기값)
function _InputSelect( $_name , $_arr , $_chk , $_event , $_arr2=null , $initval =null) {
	if( !$initval ) { $initval = "-선택-";}
	$_str = "<select name='${_name}' ${_event}><option value=''>${initval}</option>";
	foreach( $_arr  as $key=>$val ) {
		if( $_arr2 !="" ){ $_appname = $_arr2[$key]; }
		else { $_appname = $val ; }
		$_str .= "<option value='${val}' ";
		if( $val == $_chk ) { $_str .= "selected"; }
		$_str .=">${_appname}</option>";
	}
	$_str .= "</select>";
	return $_str ;
}




#### select 형 input 처리 - 숫자연속형 ####
## _InputSelectNum( 이름 , 시작값 , 종료값 , 정해진 값 , 이벤트 , 초기값)
function _InputSelectNum( $_name , $_num1 , $_num2 , $_chk , $_event , $initval =null) {
	if( !$initval ) { $initval = "-";}
	$_str = "<select name='${_name}' ${_event} ><option value=''>${initval}</option>";
	for( $i=$_num1; $i<=$_num2; $i++ ){
		$_str .= "<option value='$i' ";
		if( $i == $_chk ) { $_str .= "selected"; }
		$_str .=" >" . $i .'</option>';
	}
	$_str .= "</select>";
	return $_str ;
}



// 1~12월 선택
function _InputSelectMonth( $_name , $_chk , $_event , $initval =null) {
	if( !$initval ) { $initval = "-";}
	$_str = "<select name='${_name}' ${_event}><option value=''>${initval}</option>";
	for( $i=1 ; $i<=12 ; $i++  ){
		$_str .= "<option value='" . sprintf("%02d" , $i) . "' ";
		if( $i == $_chk ) { $_str .= "selected"; }
		$_str .=">${i}월</option>";
	}
	$_str .= "</select>";
	return $_str ;
}

// 1~31일 선택
function _InputSelectDay( $_name , $_chk , $_event , $initval =null) {
	if( !$initval ) { $initval = "-";}
	$_str = "<select name='${_name}' ${_event}><option value=''>${initval}</option>";
	for( $i=1 ; $i<=31 ; $i++  ){
		$_str .= "<option value='" . sprintf("%02d" , $i) . "' ";
		if( $i == $_chk ) { $_str .= "selected"; }
		$_str .=">${i}일</option>";
	}
	$_str .= "</select>";
	return $_str ;
}




#### radio 형 input 처리####
## _InputRadio( 이름 , 배열 , 정해진 값 , 이벤트 , 정해진(지정)배열 , )
function _InputRadio( $_name , $_arr , $_chk , $_event , $_arr2 ) {
	$_str = '';
	if( sizeof($_arr2) >0 ) {
		$arr_appname = $_arr2;
	}
	else {
		$arr_appname = $_arr;
	}
	foreach( $_arr as $k=>$v ){
		$_str .= "<label class='design'><input type=radio id='${_name}{$v}' name='${_name}' value='{$v}' ".$_event;
		if( $_chk == $v ) {
			$_str .= " checked";
		}
		$_str .=" >" . $arr_appname[$k] ."</label>" ;
	}
	return $_str;
}





#### checkbox 형 input 처리####
## _InputCheckbox( 이름 , 배열 , 정해진 값(반드시 배열형태) , 이벤트 , 정해진(지정)배열 , )
function _InputCheckbox($_name, $_arr, $_chk, $_event, $_arr2) {
	if(sizeof($_arr2) >0) $arr_appname = $_arr2;
	else $arr_appname = $_arr;
	foreach($_arr as $k=>$v){
		//if($k!=0 && $k%4==0) $_str .= '</label><label class="design">';

		// 배열값이 1개일경우 따로 분류하여 처리한다.
		if(sizeof($_arr) > 1) {
			$_str .= '<label class="design"><input type="checkbox" id="'.$_name.$v.'" name="'.$_name.'[]" value="'.$v.'" '.$_event;
			if(@in_array($v , $_chk)) $_str .= ' checked';
		} else {
			$_str .= '<label class="design"><input type="checkbox" id="'.$_name.$v.'" name="'.$_name.'" value="'.$v.'" '.$_event;
			if($v == $_chk) $_str .= ' checked';
		}
		$_str .= '>'.$arr_appname[$k].'</label>';
	}
	return $_str ;
}

//  - 원데이넷 문자발송 함수 ---
function onedaynet_sms_send($tran_phone, $tran_callback, $tran_msg) {
    global $siteInfo, $_SERVER;

    //sms_send( 아이디 , 비번 , 받을 전번 , 보낸 전번 , 메시지 , 예약시간(형태 : 2015-12-10 13:21:25) , 서버아이피 )
    if( $tran_phone && $tran_callback && $tran_msg ) {
        $SMSDec = enc_array('d', $siteInfo['s_smspw']);
        include_once($_SERVER['DOCUMENT_ROOT'].'/include/soapLib/nusoap.php');
        $client = new soapclientW('http://mobitalk.gobeyond.co.kr/nusoap/sms.send.php');
        $result = $client->call('sms_send',array('id' => $siteInfo['s_smsid'], 'pw'=>$SMSDec['s_smspw'], 'receive_num'=>$tran_phone, 'send_num'=>$tran_callback, 'msg'=>$tran_msg, 'reserve_time'=>'', 'ip'=>'auto'));
        $result_json = json_decode($result, true);
        if(in_array(gettype($result_json), array('integer', 'NULL'))) $result_array = array($result_json); // 이전 string 반환 교정
        else $result_array = $result_json;

        insert_sms_send_log($result_array);
        return $result_array;
    }
}
//  - 원데이넷 문자발송 함수 ---


/**
 *
 * # 원데이넷 문자발송 함수 - 일괄발송
 *
 * @param array($send_array)
 * @detail ->
 *          $send_array = array(
 *                          array(
 *                              'receive_num'=>'010-0000-0000'
 *                              , 'send_num'=>'1544-6937'
 *                              , 'msg'=>'SMS 다중발송 테스트 010-0000-0000'
 *                              , 'reserve_time'=>''
 *                              , 'title'=>'테스트입니다'
 *                              , 'image'=>'123124521.jpg'
 *                              , 'image_del'=>'Y'
 *                          )
 *                      );
 * @detail -->
 *  ==> array()
 *      ==> [발송차순][receive_num] : 받을 전번
 *      ==> [발송차순][send_num] : 보낸 전번
 *      ==> [발송차순][msg] : 메시지
 *      ==> [발송차순][reserve_time] : 예약시간(형태 : 2011-04-05 13:21:25)
 *      ==> [발송차순][title] : 제목 (LMS/MMS 전송시 표시됨)
 *      ==> [발송차순][image] : 첨부이미지 (/upfiles/ 에 저장)
 *      ==> [발송차순][image_del] : 첨부이미지 삭제여부(Y 일 경우 전송완료 후 로컬 이미지 삭제)
 *
 * @return array(array('result_code', 'result_msg', 'send_num', 'receive_num'))
 * @detail ->
 *          $return = array(
 *              [0] => array(
 *                      [code] => S00
 *                      [data] => 정상적으로 발송되었습니다.
 *                      [send_num] => 1544-6937
 *                      [receive_num] => 010-0000-0000
 *                  ),
 *              [1] => array(
 *                      [code] => Y
 *                      [data] => 성공!
 *                      [send_num] => 1544-6937
 *                      [receive_num] => 010-0000-0000
 *                  )
 *          );
 * @detail -->
 *  ==> array()
 *      ==> [전송차순][result_code] = 결과코드
 *      ==> [전송차순][result_msg] = 결과 메시지
 *      ==> [전송차순][send_num] = 발신번호
 *      ==> [전송차순][receive_num] = 수신번호
**/
# 원데이넷 문자발송 함수 - 일괄발송
function onedaynet_sms_multisend($arr_send = array()) {
    global $_SERVER, $siteInfo;

    // 처리 데이터가 없는 경우
    if(count($arr_send) <= 0) return;

    // 초기값 설정
    $SMSDec = enc_array('d', $siteInfo['s_smspw']);
    $tran_id = $siteInfo['s_smsid']; $tran_pw = $SMSDec['s_smspw']; $arr_send_string = array(); $arr_send_image = array(); $result_array = array();

    // 이미지처리
    foreach($arr_send as $k=>$v) {
        if(trim($v['image']) <> '') array_push($arr_send_image, $arr_send[$k]); // 이미지가 있다면 MMS
        else array_push($arr_send_string, $arr_send[$k]); // 이미지가 없으면 SMS/LMS
    }

    // nusoap include
    include_once($_SERVER['DOCUMENT_ROOT'].'/include/soapLib/nusoap.php');

    // SMS/LMS 발송
    if(count($arr_send_string) > 0) {

        $client = new soapclientW('http://mobitalk.gobeyond.co.kr/nusoap/mms.send_server_multi.php');
        // sms_send( 아이디 , 비번 , 메세지 배열 , 서버아이피)
        $result = $client->call('sms_send', array('id'=>$tran_id, 'pw'=>$tran_pw, 'arr_send'=>$arr_send_string, 'ip'=>'auto'));
        $result_json = json_decode($result, true);
        if(in_array(gettype($result_json), array('integer', 'NULL'))) $result_array = array($result_json); // 이전 string 반환 교정
        else $result_array = $result_json;
    }

    // MMS 발송
    if(count($arr_send_image) > 0) {

        include_once($_SERVER['DOCUMENT_ROOT'].'/include/soapLib/nusoapmime.php');
        foreach($arr_send_image as $k=>$v) {

            $tran_phone = $v['receive_num']; $tran_callback = $v['send_num']; $tran_msg = $v['msg'];
            $tran_reservetime = $v['reserve_time']; $tran_title = $v['title']; $tran_img = $v['image']; $tran_img_del = $v['image_del'];
            $app_dir = $_SERVER['DOCUMENT_ROOT'].'/upfiles';
            $client = new soapclientmime('http://mobitalk.gobeyond.co.kr/nusoap/mms.send_server_one.php?wsdl', true);
            $client->setHTTPEncoding('deflate, gzip');

            if($tran_img){
                $file = '';
                $fp = @fopen( $app_dir.'/'.$tran_img, 'rb');
                if($fp) { while(!feof($fp)){ $file .= fgets($fp); } }
                @fclose($fp);
				if($tran_img_del == 'Y' && count($arr_send_image) <= ($k+1)) { @unlink($app_dir.'/'.$tran_img); } // 이미지 삭제 추가
                $cid = $client->addAttachment($file, $tran_img);
            }

            // mobitalk_mms_send(아이디, 비번, 받을번호, 보낸번호, 메시지, 제목, 이미지, 예약시간, 서버아이피)
            $result = $client->call(
                'mobitalk_mms_send',
                array(
                    'tran_id'=>$tran_id, 'tran_pw'=>$tran_pw, 'tran_phone'=>$tran_phone, 'tran_callback'=>$tran_callback, 'tran_msg'=>str_replace("&" , "&amp;" , $tran_msg),
                    'tran_title'=>$tran_title, 'tran_img'=>$tran_img, 'tran_reservetime'=>$tran_reservetime, 'tran_ip'=>'auto'
                )
            );
            $result_json = json_decode($result, true);
            if(in_array(gettype($result_json), array('integer', 'NULL'))) $result_json = array($result); // 이전 string 반환 교정
            $result_array = array_merge($result_array, $result_json);
        }
    }

    insert_sms_send_log($result_array);
    return $result_array;
}



	# 원데이넷 문자/알림톡발송 함수 - 개별발송
	// ( onedaynet_sms_send 대체함수 )
	//						tran_phone : 수신전화, tran_callback : 발신전화, tran_msg : 메시지
	function onedaynet_alimtalk_send($tran_phone, $tran_callback, $tran_msg , $smsInfo = array()) {
		global $siteInfo, $_SERVER;

		//sms_send( 아이디 , 비번 , 받을 전번 , 보낸 전번 , 메시지 , 예약시간(형태 : 2015-12-10 13:21:25) , 서버아이피 )
		if( $tran_phone && $tran_callback && $tran_msg ) {
			$SMSDec = enc_array('d', $siteInfo['s_smspw']);
			include_once($_SERVER['DOCUMENT_ROOT'].'/include/soapLib/nusoap.php');

			$client = new soapclientW('http://mobitalk.gobeyond.co.kr/nusoap/tot.send.php');

			// alimtalkYN : 알림톡 적용여부( Y / N ), alimtalk_uid : 템플릿 고유번호
			// alimtalk_add1 : 적용변수1 , alimtalk_add2 : 적용변수2 , alimtalk_add3 : 적용변수3 , alimtalk_add4 : 적용변수4 , alimtalk_add5 : 적용변수5 , alimtalk_add6 : 적용변수6 , alimtalk_add7 : 적용변수7 , alimtalk_add8 : 적용변수8
			$alimtalkYN = ($smsInfo['kakao_status'] ? $smsInfo['kakao_status'] : 'N');
			$alimtalk_uid = $smsInfo['kakao_templet_num'] ;
			$alimtalk_add1 = $smsInfo['kakao_add1'] ;
			$alimtalk_add2 = $smsInfo['kakao_add2'] ;
			$alimtalk_add3 = $smsInfo['kakao_add3'] ;
			$alimtalk_add4 = $smsInfo['kakao_add4'] ;
			$alimtalk_add5 = $smsInfo['kakao_add5'] ;
			$alimtalk_add6 = $smsInfo['kakao_add6'] ;
			$alimtalk_add7 = $smsInfo['kakao_add7'] ;
			$alimtalk_add8 = $smsInfo['kakao_add8'] ;

			$result = $client->call('sms_send',array('id' => $siteInfo['s_smsid'], 'pw'=>$SMSDec['s_smspw'], 'receive_num'=>$tran_phone, 'send_num'=>$tran_callback, 'msg'=>$tran_msg, 'reserve_time'=>'', 'ip'=>'auto' , 'alimtalkYN' => $alimtalkYN , 'alimtalk_uid' => $alimtalk_uid, 'alimtalk_add1' => $alimtalk_add1 , 'alimtalk_add2' => $alimtalk_add2 , 'alimtalk_add3' => $alimtalk_add3 , 'alimtalk_add4' => $alimtalk_add4 , 'alimtalk_add5' => $alimtalk_add5 , 'alimtalk_add6' => $alimtalk_add6 , 'alimtalk_add7' => $alimtalk_add7 , 'alimtalk_add8' => $alimtalk_add8 ));
			$result_json = json_decode($result, true);
			if(in_array(gettype($result_json), array('integer', 'NULL'))) $result_array = array($result_json); // 이전 string 반환 교정
			else $result_array = $result_json;

			insert_sms_send_log($result_array);
			return $result_array;
		}
	}
	# 원데이넷 문자/알림톡발송 함수 - 개별발송



# 원데이넷 문자/알림톡발송 함수 - 일괄발송
// (onedaynet_sms_multisend 대체문자)
//		* 알림톡일 경우 MMS을 발송할 수는 없음.
//				$arr_send : 문자발송 배열
//						receive_num : 수신번호 , send_num : 발신번호  , msg : 메시지 , image : 첨부이미지, reserve_time : 예약시간
//						alimtalkYN : 알림톡 적용여부( Y / N ), alimtalk_uid : 템플릿 고유번호
//						alimtalk_add1 : 적용변수1 , alimtalk_add2 : 적용변수2 , alimtalk_add3 : 적용변수3 , alimtalk_add4 : 적용변수4 , alimtalk_add5 : 적용변수5 , alimtalk_add6 : 적용변수6 , alimtalk_add7 : 적용변수7 , alimtalk_add8 : 적용변수8
function onedaynet_alimtalk_multisend($arr_send = array()) {
	global $_SERVER, $siteInfo;

	// 처리 데이터가 없는 경우
	if(count($arr_send) <= 0) return;

	// 초기값 설정
	$SMSDec = enc_array('d', $siteInfo['s_smspw']);
	$tran_id = $siteInfo['s_smsid']; $tran_pw = $SMSDec['s_smspw']; $arr_send_string = array(); $arr_send_image = array(); $result_array = array();

	// 이미지처리
	foreach($arr_send as $k=>$v) {
		if(trim($v['image']) <> '' ) {
			array_push($arr_send_image, $arr_send[$k]); // 이미지가 있다면 MMS
		}
		else {
			array_push($arr_send_string, $arr_send[$k]); // 이미지가 없으면 SMS/LMS
		}
	}

	// nusoap include
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/soapLib/nusoap.php');


	// SMS/LMS 발송
	if(count($arr_send_string) > 0) {

		$client = new soapclientW('http://mobitalk.gobeyond.co.kr/nusoap/tot.send_server_multi.php');

		// sms_send( 아이디 , 비번 , 메세지 배열 , 서버아이피)
		$result = $client->call('sms_send', array('id'=>$tran_id, 'pw'=>$tran_pw, 'arr_send'=>$arr_send_string, 'ip'=>'auto'));
		$result_json = json_decode($result, true);
		if(in_array(gettype($result_json), array('integer', 'NULL'))) $result_array = array($result_json); // 이전 string 반환 교정
		else $result_array = $result_json;
	}

	// MMS 발송
	if(count($arr_send_image) > 0) {

		include_once($_SERVER['DOCUMENT_ROOT'].'/include/soapLib/nusoapmime.php');
		foreach($arr_send_image as $k=>$v) {

			$tran_phone = $v['receive_num']; $tran_callback = $v['send_num']; $tran_msg = $v['msg'];
			$tran_reservetime = $v['reserve_time']; $tran_title = $v['title']; $tran_img = $v['image']; $tran_img_del = $v['image_del'];
			$app_dir = $_SERVER['DOCUMENT_ROOT'].'/upfiles';
			$client = new soapclientmime('http://mobitalk.gobeyond.co.kr/nusoap/tot.send_server_one?wsdl', true);
			$client->setHTTPEncoding('deflate, gzip');

			if($tran_img){
				$file = '';
				$fp = @fopen( $app_dir.'/'.$tran_img, 'rb');
				if($fp) { while(!feof($fp)){ $file .= fgets($fp); } }
				@fclose($fp);
				$cid = $client->addAttachment($file, $tran_img);
			}

			// mobitalk_mms_send(아이디, 비번, 받을번호, 보낸번호, 메시지, 제목, 이미지, 예약시간, 서버아이피)
			$result = $client->call(
				'mobitalk_mms_send',
				array(
					'tran_id'=>$tran_id, 'tran_pw'=>$tran_pw, 'tran_phone'=>$tran_phone, 'tran_callback'=>$tran_callback, 'tran_msg'=>$tran_msg,
					'tran_title'=>$tran_title, 'tran_img'=>$tran_img, 'tran_reservetime'=>$tran_reservetime, 'tran_ip'=>'auto',
					'alimtalkYN' => ($alimtalkYN ? $alimtalkYN : 'N') , 'alimtalk_uid' => $v['alimtalk_uid'] , 'alimtalk_add1' => $v['alimtalk_add1'], 'alimtalk_add2' => $v['alimtalk_add2'], 'alimtalk_add3' => $v['alimtalk_add3'], 'alimtalk_add4' => $v['alimtalk_add4'], 'alimtalk_add5' => $v['alimtalk_add5'], 'alimtalk_add6' => $v['alimtalk_add6'], 'alimtalk_add7' => $v['alimtalk_add7'], 'alimtalk_add8' => $v['alimtalk_add8']
				)
			);
			$result_json = json_decode($result, true);
			if(in_array(gettype($result_json), array('integer', 'NULL'))) $result_json = array($result); // 이전 string 반환 교정
			$result_array = array_merge($result_array, $result_json);
		}
	}

	insert_sms_send_log($result_array);
	return $result_array;
}
# 원데이넷 문자/알림톡발송 함수 - 일괄발송


# 원데이넷 문자/알림톡발송을 위한 - 문자설정 배열 설정
//		smsInfo : 지정한 문자의 배열정보
//		arr_replace : 치환자 배열
function smsinfo_array($smsInfo , $arr_replace=array()){
	return array(
		'alimtalkYN' => ($smsInfo['kakao_status'] ? $smsInfo['kakao_status'] : 'N') ,
		'alimtalk_uid' => $smsInfo['kakao_templet_num'] ,
		'alimtalk_add1' => str_replace(array_keys($arr_replace),array_values($arr_replace), $smsInfo['kakao_add1']) ,
		'alimtalk_add2' => str_replace(array_keys($arr_replace),array_values($arr_replace), $smsInfo['kakao_add2']) ,
		'alimtalk_add3' => str_replace(array_keys($arr_replace),array_values($arr_replace), $smsInfo['kakao_add3']) ,
		'alimtalk_add4' => str_replace(array_keys($arr_replace),array_values($arr_replace), $smsInfo['kakao_add4']) ,
		'alimtalk_add5' => str_replace(array_keys($arr_replace),array_values($arr_replace), $smsInfo['kakao_add5']) ,
		'alimtalk_add6' => str_replace(array_keys($arr_replace),array_values($arr_replace), $smsInfo['kakao_add6']) ,
		'alimtalk_add7' => str_replace(array_keys($arr_replace),array_values($arr_replace), $smsInfo['kakao_add7']) ,
		'alimtalk_add8' => str_replace(array_keys($arr_replace),array_values($arr_replace), $smsInfo['kakao_add8'])
	);
}
# 원데이넷 문자/알림톡발송을 위한 - 문자설정 배열 설정



// - 모비톡 계정확인 함수 --
function onedaynet_sms_user() {

    global $siteInfo;
    if($siteInfo['s_smsid'] && $siteInfo['s_smspw'] && $siteInfo['s_glbtel']) {
        $SMSDec = enc_array('d', $siteInfo['s_smspw']);
        include_once($_SERVER['DOCUMENT_ROOT'].'/include/soapLib/nusoap.php');
        $client = new soapclientW('http://mobitalk.gobeyond.co.kr/nusoap/user.info.php');
        $result = $client->call('user_info', array('id'=>$siteInfo['s_smsid'], 'pw'=>$SMSDec['s_smspw'], 'tel'=>$siteInfo['s_glbtel']));
        return json_decode($result, true);
    }
}
// - 모비톡 계정확인 함수 --


// - 문자 발송 오류 로그 기록 ---
function insert_sms_send_log($Result=array()) {

    if(count($Result) <= 0) return;

    // 자동 인스톨 처리
    $InstallCK = mysql_query(' desc smart_sms_log ');
    if(!@mysql_num_rows($InstallCK)) {

        _MQ_noreturn("
            CREATE TABLE  `smart_sms_log` (
                `idx` INT( 11 ) NOT NULL AUTO_INCREMENT COMMENT  '고유키',
                `code` VARCHAR( 5 ) NOT NULL COMMENT  '에러코드',
                `msg` VARCHAR( 255 ) NOT NULL COMMENT  '에러메시지',
                `send_num` VARCHAR( 20 ) NOT NULL COMMENT  '보내는 번호',
                `receive_num` VARCHAR( 20 ) NOT NULL COMMENT  '받는번호',
                `rdate` DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00' COMMENT  '기록일',
                PRIMARY KEY (  `idx` )
            ) ENGINE = MYISAM COMMENT =  'SMS 발송 에러로그'
        ");
    }

    // 로그 기록
    foreach($Result as $k=>$v) {

        if($v['code'] == 'S00' || !$v['code']) continue;
        _MQ_noreturn(" insert into `smart_sms_log` set `code` = '{$v['code']}', `msg` = '{$v['data']}', `send_num` = '{$v['send_num']}', `receive_num` = '{$v['receive_num']}', `rdate` = now() ");
    }
}
// - 문자 발송 오류 로그 기록 ---


function mailCheck($email) {
	if(preg_match("/^[^@]+@[^@]+\.[^@\.]+$/i",$email)) { return true; }
	else { return false; }
}




####null , 널 , 빈값체크 함수
function nullchk($val , $str , $loc=null , $popup=null) {
	if ( preg_replace("/[[:space:]]/i","",$val) == "" ) {
		if( $popup == "Y" ) { error_msgPopup_s( $str ); }
		elseif( $popup == "ALT" ) { error_alt( $str ); }
		elseif( $loc != "" ) { error_loc_msg($loc , $str ); }
		else { error_msg( $str ); }
	}
	else {
		return $val ;
	}
}





## 배열정보 보기 ##
function ViewArr($arr) {
	echo "<xmp>". print_r($arr , true) ."</xmp>";
}
function ViewPost() {
	global $_POST;
	echo "<xmp>". print_r($_POST , true) ."</xmp>";
}
function ViewReq() {
	global $_REQUEST;
	echo "<xmp>". print_r($_REQUEST , true) ."</xmp>";
}






## base64_encode의 경우 한글깨짐 발생(예 : 룸싸롱/텐프로/쩜오 ) ..
## 이를 대체하기 위한 인코딩 및 디코딩 함수
function enc( $mode ,  $str ) {
	$a = array( "+"=>"§" , "?"=>"※" , "#"=>"☆" , "&"=>"★" , "/"=>"○" );
	if($mode=="e") { // encoding
		$str=base64_encode($str);
		foreach( $a as $k=>$v ) { $str=str_replace( $k , $v , $str ); }
	}
	if($mode=="d") { // decoding
		foreach( $a as $k=>$v ) { $str=str_replace( $v , $k , $str ); }
		$str=base64_decode($str);
	}
	return $str;
}




// 다중배열 검색
function recursive_array_search($search_value,$target_array) {
	if(sizeof($target_array) > 0 ) {
		foreach($target_array as $key=>$value) {
			$current_key=$key;
			if($search_value===$value OR (is_array($value) && recursive_array_search($search_value,$value))) {
				return $current_key;
			}
		}
	}
	return false;
}
function recursive_array_search_value($search_value,$target_array) {
	if(sizeof($target_array) > 0 ) {
		foreach($target_array as $key=>$value) {
			$current_key=$key;
			if($search_value===$value OR (is_array($value) && recursive_array_search_value($search_value,$value))) {
				return $value;
			}
		}
	}
	return false;
}
function recursiveFind(array $array, $needle) {
	$iterator  = new RecursiveArrayIterator($array);
	$recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
	$aHitList = array();
	foreach ($recursive as $key => $value) {
		if( is_array($value) && recursive_array_search_value($needle , $value)) {
			$aHitList[] = $value ;
		}
	}
	return $aHitList;
}





// zip파일만 등록가능함
//$fileLOC-- 파일 등록 디렉토리
//$fileVAR -- 파일 변수
//$fileOLD -- OLD 파일명
function _FileForm( $fileLOC , $fileVAR ,$fileOLD) {
	$_img_reg = "";
	if($fileOLD) {
		$fileOLD_src = $fileLOC . "/" . $fileOLD ;
		$_img_reg .= "<A HREF='{$fileOLD_src}' target=_blank>{$fileOLD}</A><input type=hidden name='{$fileVAR}_OLD' value='{$fileOLD}'>";
		$_img_reg .= "<input type=checkbox name='{$fileVAR}_DEL' value='Y'>파일삭제<br>";
	}
	$_img_reg .= "<input type=file name='{$fileVAR}' size=20 class=input_text>";
	return $_img_reg ;
}



//$fileLOC-- 파일 등록 디렉토리
//$fileVAR -- 파일 변수
function _FilePro( $fileLOC , $fileVAR, $fileEtx='zip') {
	 $fileOLD = $fileVAR . "_OLD" ; // OLD 파일명
	 $fileDEL = $fileVAR . "_DEL" ; // 파일 삭제 여부
	global $_FILES , $$fileOLD ,  $$fileDEL ;
	$fileEtx = $fileEtx ? $fileEtx : 'zip';

	if($_FILES[$fileVAR][error] > 0 && $_FILES[$fileVAR][tmp_name] ){
		switch($_FILES[$fileVAR][error]){
			case "1":error_msg("업로드한 파일 크기가 설정용량 이상입니다."); break;//업로드한 파일 크기가 PHP 'upload_max_filesize' 설정값보다 클 경우
			case "2":error_msg("업로드한 파일 크기가 설정용량 이상입니다."); break;// UPLOAD_ERR_FORM_SIZE, 업로드한 파일 크기가 HTML 폼에 명시한 MAX_FILE_SIZE 값보다 클 경우
			case "3":error_msg("파일 전송에 오류가 있습니다."); break;//파일중 일부만 전송된 경우
			//case "4":error_msg("파일이 전송되지 않습니다."); break;//파일도 전송되지 않았을 경우
		}
	}

	if($_FILES[$fileVAR][size]> 0){
		$ex_image_type = explode(".",$_FILES[$fileVAR][name]);
		if( !preg_match("/".$fileEtx."/i" , $ex_image_type[(sizeof($ex_image_type)-1)])) {
			error_msg("등록가능한 파일이 아닙니다.");
		}
		if( $$fileOLD ) {
			@unlink( $fileLOC . "/" . $$fileOLD );
		}
		$file_name = sprintf("%u" , crc32($_FILES[$fileVAR][name] . time() . rand())) . strtolower(substr($_FILES[$fileVAR][name],-4));
		@copy($_FILES[$fileVAR][tmp_name] , $fileLOC . "/" . $file_name);
	}
	elseif( $$fileDEL == 'Y' ) {
		if( $$fileOLD ) {
			@unlink( $fileLOC . "/" . $$fileOLD );
		}
		$file_name = "";
	}
	else{
		$file_name = $$fileOLD ;
	}
	return $file_name ;
}



//$fileLOC-- 파일 등록 디렉토리
//$fileNAME -- 파일명
function _FileDel( $fileLOC , $fileNAME) {
	$fileFILE = $fileLOC . "/" . $fileNAME ;
	if( @file_exists($fileFILE) ) {
		@unlink( $fileLOC . "/" . $fileNAME );
	}
}





//$photoLOC-- 이미지파일 등록 디렉토리
//$photoVAR -- 이미지 변수
//$photoOLD -- OLD 이미지 명
function _PhotoForm($photoLOC, $photoVAR, $photoOLD, $attr='', $addClass=array()) {

	$_img_reg = '
		<div class="input_file"'.($attr?' '.$attr:null).'>
			<input type="text" class="fakeFileTxt" readonly="readonly" disabled="">
			<div class="fileDiv">
				<input type="button" class="buttonImg" value="파일찾기">
				<input type="file" name="'.$photoVAR.'" class="realFile'.($addClass['realFile']?' '.$addClass['realFile']:null).'" onchange="$(this).closest(\'.input_file\').find(\'.fakeFileTxt\').val(this.value);">
			</div>
		</div>
	';
	if($photoOLD) {
		$photoOLD_src = get_img_src($photoOLD, $photoLOC.'/');
		if($photoOLD_src == '') $photoOLD_src = 'images/admin_no_thumb.gif';//삭제된이미지
		$_img_reg .= '
			<div class="preview_thumb">
				<img src="'.$photoOLD_src.'" class="js_thumb_img'.($addClass['js_thumb_img']?' '.$addClass['js_thumb_img']:null).'" data-img="'.$photoOLD_src.'" alt=""><!-- 클릭하면 이미지 새창 -->
				<a href="#none" class="c_btn h27 js_thumb_popup'.($addClass['js_thumb_popup']?' '.$addClass['js_thumb_popup']:null).'" data-img="'.$photoOLD_src.'">이미지 보기</a>
			</div>
			<label class="design"><input type="hidden" name="'.$photoVAR.'_OLD" class="oldFile" value="'.$photoOLD.'"><input type="checkbox" class="js_del'.($addClass['js_del']?' '.$addClass['js_del']:null).'" name="'.$photoVAR.'_DEL" value="Y">삭제</label>
		';
	}

	return $_img_reg;
}
function _PhotoFormOld( $photoLOC , $photoVAR ,$photoOLD) {
	$_img_reg = "";
	if($photoOLD) {

		$photoOLD_src =get_img_src($photoOLD,$photoLOC . "/");
		$_size = @getimagesize( $photoOLD_src);
		if( $_size[0] > 300 ) { $_size[0]= 300; }
		$_img_reg .= "<img src='{$photoOLD_src}' width='$_size[0]'><br>";
		$_img_reg .= "<input type=hidden name='{$photoVAR}_OLD' value='{$photoOLD}'>";
		$_img_reg .= "<span class='multi'><input type=checkbox name='{$photoVAR}_DEL' value='Y'>이미지 삭제</span><br>";
	}
	$_img_reg .= "<input type=file name='{$photoVAR}' size=20 class=input_text>";
	return $_img_reg ;
}



//$photoLOC-- 이미지파일 등록 디렉토리
//$photoVAR -- 이미지 변수
function _PhotoPro( $photoLOC , $photoVAR , $popup=null) {
	$photoOLD = $photoVAR . "_OLD" ; // OLD 이미지 명
	$photoDEL = $photoVAR . "_DEL" ; // 이미지 삭제 여부
	global $_FILES , $$photoOLD ,  $$photoDEL ;

	if($_FILES[$photoVAR][error] > 0 && $_FILES[$photoVAR][tmp_name] ){
		if(strtolower($popup)=='alt'){
			switch($_FILES[$photoVAR][error]){
				case "1":error_alt("업로드한 파일 크기가 2Mb 이상입니다."); break;//업로드한 파일 크기가 PHP 'upload_max_filesize' 설정값보다 클 경우
				case "2":error_alt("업로드한 파일 크기가 2Mb 이상입니다."); break;// UPLOAD_ERR_FORM_SIZE, 업로드한 파일 크기가 HTML 폼에 명시한 MAX_FILE_SIZE 값보다 클 경우
				case "3":error_alt("파일 전송에 오류가 있습니다."); break;//파일중 일부만 전송된 경우
				case "4":error_alt("파일이 전송되지 않습니다."); break;//파일도 전송되지 않았을 경우
			}
		}else{
			switch($_FILES[$photoVAR][error]){
				case "1":error_msg("업로드한 파일 크기가 2Mb 이상입니다."); break;//업로드한 파일 크기가 PHP 'upload_max_filesize' 설정값보다 클 경우
				case "2":error_msg("업로드한 파일 크기가 2Mb 이상입니다."); break;// UPLOAD_ERR_FORM_SIZE, 업로드한 파일 크기가 HTML 폼에 명시한 MAX_FILE_SIZE 값보다 클 경우
				case "3":error_msg("파일 전송에 오류가 있습니다."); break;//파일중 일부만 전송된 경우
				case "4":error_msg("파일이 전송되지 않습니다."); break;//파일도 전송되지 않았을 경우
			}
		}
	}

	// LCY : 2021-08-11 : 1차 이미지 파일 보안 처리 { 
	$s = file_get_contents($_FILES[$photoVAR][tmp_name]);
	if( preg_match("/(\<\?php)/", $s) > 0){ error_msg("등록가능한 이미지가 아닙니다.");  }
	// LCY : 2021-08-11 : 1차 이미지 파일 보안 처리 }


	if($_FILES[$photoVAR][size]> 0){
		$ex_image_name = explode(".",$_FILES[$photoVAR][name]);
		$app_ext = strtolower($ex_image_name[(sizeof($ex_image_name)-1)]); // 확장자
		if( !preg_match("/gif|jpg|jpeg|bmp|png/i" , $app_ext) ) {
			if(strtolower($popup)=='alt') error_alt("등록가능한 이미지가 아닙니다.");
			else error_msg("등록가능한 이미지가 아닙니다.");
		}
		if( $$photoOLD ) {
							@unlink( $photoLOC . "/" . $$photoOLD );
					}
		$img_name = sprintf("%u" , crc32($_FILES[$photoVAR][name] . time() . rand())) . "." . $app_ext ;
		@copy($_FILES[$photoVAR][tmp_name] , $photoLOC . "/" . $img_name);
	}
			elseif( $$photoDEL == 'Y' ) {
		if( $$photoOLD ) {
							@unlink( $photoLOC . "/" . $$photoOLD );
		}
		$img_name = "";
	} else{
		$img_name = $$photoOLD ;
	}

	return $img_name ;
}



//$photoLOC-- 이미지파일 등록 디렉토리
//$photoNAME -- 이미지명
function _PhotoDel( $photoLOC , $photoNAME) {

		$photoFILE = $photoLOC . "/" . $photoNAME ;
		if( @file_exists($photoFILE) ) {
			@unlink( $photoLOC . "/" . $photoNAME );
		}

}



// 관리자 페이지 항목 설명
function _DescStr($str,$color=''){
	return "<div class='c_tip ".$color."'>".$str."</div>";
}


// goo.gl 을 이용한 shorten url 적용
function get_shortURL($longURL) {
	//구글 api 키 발급 : https://code.google.com/apis/console/?pli=1 > 동의 > services > URL Shortener API 를  on으로 체크
	$api_key = "AIzaSyB-71m5RSgTZjXM1uJOHrSHJSLCRVfu69E"; // onedaynet google API key
	$curlopt_url = "https://www.googleapis.com/urlshortener/v1/url?key=".$api_key;

	$ch = curl_init();
	//$timeout = 10;

	curl_setopt($ch, CURLOPT_URL, $curlopt_url);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$jsonArray = array('longUrl' => $longURL);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jsonArray));
	$shortURL = curl_exec($ch);
	curl_close($ch);
	$result_array = json_decode($shortURL, true);
	if($result_array['shortUrl']) return $result_array['shortUrl'];// durl.me
	else if($result_array['id']) return $result_array['id'];    // goo.gl
	else return false;

	$shortURL = curl_exec($ch);
	curl_close($ch);

	return $shortURL;
}

// goo.gl 을 이용한 shorten url 적용
function get_shortURL_2($longURL){

	//구글 api 키 발급 : https://code.google.com/apis/console/?pli=1 > 동의 > services > URL Shortener API 를  on으로 체크
	$api_key = "AIzaSyB-71m5RSgTZjXM1uJOHrSHJSLCRVfu69E"; // onedaynet google API key
	$curlopt_url = "https://www.googleapis.com/urlshortener/v1/url?key=".$api_key;

	$ch = curl_init();
	//$timeout = 10;

	curl_setopt($ch, CURLOPT_URL, $curlopt_url);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$jsonArray = array('longUrl' => $longURL);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jsonArray));
	$shortURL = curl_exec($ch);
	curl_close($ch);
	$result_array = json_decode($shortURL, true);
	if($result_array['shortUrl']) return $result_array['shortUrl'];// durl.me
	else if($result_array['id']) return $result_array['id'];    // goo.gl
	else return false;

	$shortURL = curl_exec($ch);
	curl_close($ch);

	return $shortURL;

}


// 개인 회원 체크
// frame ::: 프레임인지 확인
function member_chk($frame=null) {
	global $_COOKIE, $mem_info;
	if( !is_login() || !$mem_info['in_id']) {
		if($frame == "Y"){
			error_loc_msg_confirm("/?pn=member.login.form&_rurl=".urlencode("/?".$_SERVER[QUERY_STRING]), "로그인 후 이용하실 수 있습니다.\\n로그인페이지로 이동 하시겠습니까?", "parent");
		}
		else {
			error_loc_msg_confirm("/?pn=member.login.form&_rurl=".urlencode("/?".$_SERVER[QUERY_STRING]), "로그인 후 이용하실 수 있습니다.\\n로그인페이지로 이동 하시겠습니까?");
		}
	}
}

// 묻고 이동하기
function error_loc_msg_confirm($loc, $msg, $target=null) {

	die("<script>
	var conf = confirm('{$msg}');
	if(!conf) history.back();
	else ".($target?$target.'.':null)."location.href = '{$loc}';
	</script>");
}


// 쿠키 체크 - 쇼핑몰의 비회원구매을 위한 조치
function cookie_chk() {
	global $_COOKIE;
	if( !$_COOKIE["AuthShopCOOKIEID"] ) {
		error_loc_msg("/" , "잘못된 접근입니다.");
	}
}




// Post 방식의 curl 발송 위한 함수
function CurlPostExec( $url , $data, $RequestTime=100) {
	$cu = curl_init();
	curl_setopt($cu, CURLOPT_URL,  $url ); // 데이타를 보낼 URL 설정
	curl_setopt($cu, CURLOPT_POST,1); // 데이타를 get/post 로 보낼지 설정
	curl_setopt($cu, CURLOPT_RETURNTRANSFER,1); // REQUEST 에 대한 결과값을 받을건지 체크 #Resource ID 형태로 넘어옴 :: 내장 함수 curl_errno 로 체크
	$arr_url = parse_url($url);
	if( $arr_url[scheme] == "https") {curl_setopt($cu, CURLOPT_SSL_VERIFYPEER, 0);}
	curl_setopt($cu, CURLOPT_TIMEOUT, $RequestTime); // REQUEST 에 대한 결과값을 받는 시간타임 설정
	curl_setopt($cu, CURLOPT_POSTFIELDS, $data);
	$str = curl_exec($cu); // 실행
	curl_close($cu);
	return $str;
}

// get 방식의 curl 읽기 위한 함수
function CurlExec($url, $RequestTime=100) {
	$cu = curl_init();
	curl_setopt($cu, CURLOPT_URL,  $url ); // 데이타를 보낼 URL 설정
	//curl_setopt($cu, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; InfoPath.1)"); // 해당 데이타를 보낼 http head 정의 : 삭제해도 되긴함
	curl_setopt($cu, CURLOPT_POST,0); // 데이타를 get/post 로 보낼지 설정
	//curl_setopt($cu, CURLOPT_POSTFIELDS,"arg=$arg1"); // 보낼 데이타를 설정 형식은 GET 방식으로 설정 ex) $vars = "arg=$arg1&arg2=$arg2&arg3=$arg3";
	curl_setopt($cu, CURLOPT_RETURNTRANSFER,1); // REQUEST 에 대한 결과값을 받을건지 체크 #Resource ID 형태로 넘어옴 :: 내장 함수 curl_errno 로 체크
	$arr_url = parse_url($url);
	if( $arr_url[scheme] == "https") {curl_setopt($cu, CURLOPT_SSL_VERIFYPEER, 0);}
	curl_setopt($cu, CURLOPT_TIMEOUT, $RequestTime); // REQUEST 에 대한 결과값을 받는 시간타임 설정
	$str = curl_exec($cu); // 실행
	curl_close($cu);
	return $str;
}



// get 방식의 curl 읽기 위한 함수 :: 헤더정보 추출 - 보안서버 ::: JJC
//		200 정상이며, 나머지 비정상 호출
function CurlExecHeader($url, $RequestTime=100) {
	$cu = curl_init();
	curl_setopt($cu, CURLOPT_URL,  $url ); // 데이타를 보낼 URL 설정
	//curl_setopt($cu, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; InfoPath.1)"); // 해당 데이타를 보낼 http head 정의 : 삭제해도 되긴함
	curl_setopt($cu, CURLOPT_POST,0); // 데이타를 get/post 로 보낼지 설정
	//curl_setopt($cu, CURLOPT_POSTFIELDS,"arg=$arg1"); // 보낼 데이타를 설정 형식은 GET 방식으로 설정 ex) $vars = "arg=$arg1&arg2=$arg2&arg3=$arg3";
	curl_setopt($cu, CURLOPT_RETURNTRANSFER,1); // REQUEST 에 대한 결과값을 받을건지 체크 #Resource ID 형태로 넘어옴 :: 내장 함수 curl_errno 로 체크
	$arr_url = parse_url($url);
	if( $arr_url['scheme'] == "https") {curl_setopt($cu, CURLOPT_SSL_VERIFYPEER, 0);}
	curl_setopt($cu, CURLOPT_TIMEOUT, $RequestTime); // REQUEST 에 대한 결과값을 받는 시간타임 설정
	$str = curl_exec($cu); // 실행
	$http_code = curl_getinfo($cu, CURLINFO_HTTP_CODE);
	curl_close($cu);
	return $http_code;
}



// POST방식으로 fsockopen 통신 :: 리턴값은 없고 보내고 바로 커넥션을 끊는다
/*
	예제
		curl_async('http://example.com/test.php');
		curl_async('https://example.com/test.php');
		curl_async('/test.php');
*/
function curl_async($url) {
	$parts = parse_url($url);
	if(empty($parts['host'])) {
		$parts['scheme'] = '';
		$parts['host'] = reset(explode(':', $_SERVER['HTTP_HOST']));
		$parts['port'] = (count(explode(':', $_SERVER['HTTP_HOST'])) > 1?end(explode(':', $_SERVER['HTTP_HOST'])):80);
		if($parts['port'] != '80') $parts['scheme'] = 'https';
	}

	// -- https(보안서버) 의 경우 작동이 안되던 오류 패치 2019-05-10
	if($parts['scheme'] <> 'https' && preg_match("/https/i" , $url)) {$parts['scheme'] = 'https';}
	if($parts['scheme'] == 'https' && ($parts['port'] == '80' || !$parts['port'])) {$parts['port'] = 443;}

	if(is_array($parts) && isset($parts['query'])) $post_string = $parts['query'];
	else $post_string = '';
	$fp = @fsockopen(($parts['scheme'] == 'https'?'ssl://':null).$parts['host'], isset($parts['port'])?$parts['port']:80,  $errno, $errstr, 10);
	$out = "POST ".$parts['path']." HTTP/1.1\r\n";
	$out.= "Host: ".$parts['host']."\r\n";
	$out.= "Content-Type: application/x-www-form-urlencoded\r\n";
	$out.= "Content-Length: ".strlen($post_string)."\r\n";
	$out.= "Connection: Close\r\n\r\n";
	if(isset($post_string)) $out.= $post_string;
	@fwrite($fp, $out);
	@fclose($fp);
}




// - 쇼핑몰 상품코드 생성 ---
function shop_productcode_create($type=null){
	// --> 상품코드 - 영숫자조합으로 15글자 적용, 예)
	// --> 생성원리 1. 첫 글자는  영문대문자로 한다
	// --> 생성원리 2. 5개씩 3단락
	//	--> 생성예. A1234-B1234-C1234
	// --> chr(65) : A ~ chr(90) : Z
	$_code = "";
	for( $i=0;$i<3; $i++ ){
		if( $i <> 0 ) {
			$_code .= "-";
		}
		for( $j=0; $j<5 ; $j++ ){
			if( $j<>0 ) { // 숫자
				$_code .= rand(0,9);
			}
			else { // 영문
				$_code .= chr(rand(65,90));
			}
		}
	}
	return $_code ;
}
// - 상품코드 생성 ---


// - 쇼핑몰 쿠폰번호 생성 ---
function shop_couponnum_create($type=null){
	// --> 상품코드 - 영숫자조합으로 15글자 적용, 예)
	// --> 생성원리 1. 다섯번째 글자는  영문대문자로 한다
	// --> 생성원리 2. 5개씩 3단락
	//	--> 생성예. A1234-B1234-C1234
	// --> chr(65) : A ~ chr(90) : Z
	$_code = "";
	for( $i=0;$i<3; $i++ ){
		if( $i <> 0 ) {
			$_code .= "-";
		}
		for( $j=0; $j<5 ; $j++ ){
			if( $j<>4 ) { // 숫자
				$_code .= rand(0,9);
			}
			else { // 영문
				$_code .= chr(rand(65,90));
			}
		}
	}
	return $_code ;
}
// - 상품코드 생성 ---



// - 배열을 insert / update - query문으로 준비하기 위한 함수
function array_to_query($arr){
	$que = "";
	$cnt = 0;
	if( sizeof($arr) > 0 ) {
		foreach($arr as $k=>$v){
			if( $cnt <> 0 ) {
				$que .= " , ";
			}
			if( $k ){
				$que .= " $k = '" . strip_tags($v) . "' ";
				$cnt ++;
			}
		}
	}
	return $que ;
}
// - 배열을 insert / update - query문으로 준비하기 위한 함수


// 추가 / 수정항목체크 - check Add or Modify (원본 , 비교데이터) - return (stay , add , modify)
// 광고수정/등록시 추가/수정확인 함수
function chk_AoM( $ori , $data ){
	$trigger = "stay";
	if( $ori ) {
		if($ori <> $data) {
			$trigger = "modify";
		}
	}
	else {
		if($data) {
			$trigger = "add";
		}
	}
	return $trigger;
}

/*
* javascript escape 대응함수
*/
function unescape($text) {
	return urldecode(preg_replace_callback('/%u([[:alnum:]]{4})/', create_function(
						'$word',
						'return iconv("UTF-16LE", "UHC", chr(hexdec(substr($word[1], 2, 2))).chr(hexdec(substr($word[1], 0, 2))));'
						), $text));
}


// - 상품등록시 썸네일 적용(자동등록) ---
//		::: app_product_auto_thumbnail(path , 이미지명 )
function app_product_auto_thumbnail($app_path, $_img_name){
	$SkinInfo = SkinInfo();//스킨정보 추출
	include_once "wideimage/lib/WideImage.php";
	$image = WideImage::load($app_path.$_img_name);

	$dimentions = array(
		'auto_main_' => array($SkinInfo['product']['detail_image_width'], $SkinInfo['product']['detail_image_height']), // 메인이미지
		'auto_s_' =>  array($SkinInfo['product']['list_image_width'], $SkinInfo['product']['list_image_height']), // 정사각형
		'thumbs_s_auto_main_' => array($SkinInfo['product']['thumb_image_width'], $SkinInfo['product']['thumb_image_height']), // 메인 썸네일
		'thumbs_s_auto_s_' => array($SkinInfo['product']['thumb_image_width'], $SkinInfo['product']['thumb_image_height']) // 정사각형 썸네일
	);

	foreach($dimentions as $k => $v) {
		$image->resize($v[0],$v[1],'outside')->crop('center','center',$v[0],$v[1])->saveToFile($app_path.$k.$_img_name);
	}

	@unlink($app_path . $_img_name);		// 원본 이미지는 삭제한다.
}

function app_product_thumbnail($app_path, $_img_name, $_img_OLD, $mode="s"){
	$SkinInfo = SkinInfo();//스킨정보 추출
	include_once "wideimage/lib/WideImage.php";
	$image = WideImage::load($app_path.$_img_name);

	$dimentions = array(
		's' => array(
			'thumbs_s_' => array($SkinInfo['product']['thumb_image_width'], $SkinInfo['product']['thumb_image_height'])
		),
		'main' => array(
			'b' => array($SkinInfo['product']['detail_image_width'], $SkinInfo['product']['detail_image_height']),
			'thumbs_s_' => array($SkinInfo['product']['thumb_image_width'], $SkinInfo['product']['thumb_image_height'])
		)
	);

	if($_img_OLD){
		@unlink($app_path .'thumbs_b_'. $_img_OLD);
		@unlink($app_path .'thumbs_s_'. $_img_OLD);
	}

	foreach($dimentions as $k => $v) {
		if($k == $mode) {
			foreach($v as $kk => $vv) {
				if($kk=='b') {
					$image->resize($vv[0],$vv[1],'outside')->crop('center','center',$vv[0],$vv[1])->saveToFile($app_path.$_img_name);
				} else {
					$image->resize($vv[0],$vv[1],'outside')->crop('center','center',$vv[0],$vv[1])->saveToFile($app_path.$kk.$_img_name);
				}
			}
		}
	}
}
// - 상품등록시 썸네일 적용 ---


// - 이미지 등록시 썸네일 적용 ---
//			app_path - path
//			_img_name - 이미지 항목
//			_width - 썸네일 width
//			_height - 썸네일 height
function app_img_thumbnail($app_path, $_img_name , $_img_OLD , $_width , $_height){
	if( $_width && $_height ) {
		include_once dirname(__FILE__) . "/wideimage/lib/WideImage.php";
		$image = WideImage::load($app_path .'/'. $_img_name);
		if($_img_OLD){
			@unlink($app_path .'/'. $_img_OLD);
		}
		$image->resize($_width,$_height,'outside')->crop('center','center',$_width,$_height)->saveToFile($app_path .'/'. $_img_name) ;
	}
}



// --- 가격 숫자를 텍스트화 FUNCTION ---
function number_to_text( $num ){
	$arr_num[0] = "zero";	$arr_num[1] = "one";	$arr_num[2] = "two";	$arr_num[3] = "three";	$arr_num[4] = "four";
	$arr_num[5] = "five";	$arr_num[6] = "six";	$arr_num[7] = "seven";	$arr_num[8] = "eight";	$arr_num[9] = "nine";
	$arr_num[","] = "dot";

	$app_num = number_format($num); // 콤마적용
	$app_span_str ="";
	for( $i=0; $i<strlen($app_num) ; $i++ ){
		$app_span_str .= "<span class='". $arr_num[$app_num[$i]] ."'></span>";
	}
	return $app_span_str ;
}
// --- 가격 숫자를 텍스트화 FUNCTION ---

// 이미지 경로 처리
function get_img_src($img,$dir='') {
	global $_SERVER;

	//$img = str_replace('&', '&', str_replace('&amp', '&', str_replace('&', '&', $img))); // & 처리
	$img = str_replace('&amp', '&', str_replace('＆', '&', $img)); // & 처리

	// 외부 이미지체크
	if( strpos($img, '//') !== false ){
		if(strpos($img,'thumbs_s_') !== false) $img = str_replace('thumbs_s_', '' , $img);
		$dir = '';// 경로없이 그대로 노출
	}else{
		if(!$dir) $dir = IMG_DIR_PRODUCT;

		// 넘어온 dir 값이 절대경로인지 상대경로인지 확인하여 파일이 존재하는지 확인한다.
		if(strpos($dir,$_SERVER['DOCUMENT_ROOT']) !== false){
			$server_dir = $dir;
			$dir = str_replace($_SERVER['DOCUMENT_ROOT'], '', $dir);
		}else if(substr($dir,0,1) == "/"){
			$server_dir = $_SERVER['DOCUMENT_ROOT'].$dir;
		}else{
			$server_dir = $dir;
			// 절대경로로 변경
			$ex = array_filter(explode('/', dirname($_SERVER['PHP_SELF'])));
			$_exstr = '';
			for($i=0;$i<count($ex);$i++){ $_exstr = '../' . $_exstr; }
			$dir = str_replace('./', '/', str_replace($_exstr, '/', $dir));
		}

		// 풀 URL로 변경
		$dir = ($_SERVER['HTTPS'] == 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $dir;

		if($img == '' || !file_exists($server_dir . $img)){
			if(strpos($img,'thumbs_s_') !== false){ // 썸네일이 없으면 원본 이미지 추출
				$img = str_replace('thumbs_s_', '' , $img);
				if($img == '' || !file_exists($server_dir . $img)){
					$dir = $img = '';
				}
			}else{
				$dir = $img = '';
			}
		}
	}
	return $dir.$img;
}

// 회원아이디를 리턴한다.
// 인자 : 없음
// 리턴값 : 회원-아이디 , 비회원-공백
function get_userid() {
	global $_COOKIE;
	if($_COOKIE["AuthIndividualMember"])
		return $_COOKIE["AuthIndividualMember"];
	else
		return "";
}

// 콤마를 제거한다.
function delComma($val) {
	return str_replace(",","",$val);

}

// 로그인여부확인
function is_login() {
	global $_COOKIE;
	if($_COOKIE["AuthIndividualMember"]) return true;
	else return false;
}

// 최고 관리자인지 확인 (사용자 모드 상에서...)
function is_admin() {
	global $mem_info;

	if($mem_info[in_userlevel] == 9 || is_master() == true)  return true;
	else return false;

}

// 모바일 접속인지 체크한다.
function is_mobile($_mode='cookie') {
	global $_COOKIE, $_SERVER;

	// PC모드를 선택한 상태라면 flase
	if(isset($_COOKIE['AuthNoMobile']) && $_COOKIE['AuthNoMobile'] == "chk" && $_mode == 'cookie') return false;

	include_once($_SERVER['DOCUMENT_ROOT'].'/include/Mobile_Detect/Mobile_Detect.php');
	$detect = new Mobile_Detect;
	if( $detect->isMobile()) return true;
	else return false;
}


// 전화번호 하이픈 넣기
function tel_format($telNo) {
	 $telNo = preg_replace('/[^\d\n]+/', '', $telNo);
	 if(substr($telNo,0,1)!="0" && strlen($telNo)>8) $telNo = "0".$telNo;
	 $Pn3 = substr($telNo,-4);
	 if(substr($telNo,0,2)=="01") $Pn1 =  substr($telNo,0,3);
	 elseif(substr($telNo,0,2)=="02") $Pn1 =  substr($telNo,0,2);
	 elseif(substr($telNo,0,3)=="050") $Pn1 =  substr($telNo,0,4);
	 elseif(substr($telNo,0,1)=="0") $Pn1 =  substr($telNo,0,3);
	 $Pn2 = substr($telNo,strlen($Pn1),-4);
	 return implode("-",array_filter(array($Pn1,$Pn2,$Pn3)));
}




// - 문구 중 url 주소 자동링크 ---
 function string_auto_link($str) {

	 // http
	 $str = preg_replace("/http:\/\/([0-9a-z-.\/@~?&=_]+)/i", "<a href=\"http://\\1\" target='_blank'>http://\\1</a>", $str);

	 // ftp
	 $str = preg_replace("/ftp:\/\/([0-9a-z-.\/@~?&=_]+)/i", "<a href=\"ftp://\\1\" target='_blank'>ftp://\\1</a>", $str);

	 // email
	 $str = preg_replace("/([_0-9a-z-]+(\.[_0-9a-z-]+)*)@([0-9a-z-]+(\.[0-9a-z-]+)*)/i", "<a href=\"mailto:\\1@\\3\">\\1@\\3</a>", $str);

	 return $str;

 }


 function error_loc_nomsgPopup($loc) {
	 echo "<script language='javascript'>opener.location.href=('${loc}');window.close();</script>";
	 exit;
 }


 /*
 	// LCY - 로그인 틀린 횟수 쿠키 기록 함수
	$type => get : 틀린횟수를 가져온다,  del : 기록 삭제
 */
 function access_deny_cnt($type)
 {
 	global $system;
 	if($type == 'get'){

 		$ad_cnt  = $_COOKIE['AccessDenyCnt'];

 		if(!$ad_cnt){
 				$ad_cnt = 1;
 		}else{
 			$ad_cnt ++;

 		}

 		samesiteCookie("AccessDenyCnt", $ad_cnt , 0 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));

 		return $ad_cnt;

 	}else{ // del

 			samesiteCookie("AccessDenyCnt", "" , time() -3600 , "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
 	}

 	return true;
 }


// --- 암호화 ---
function onedaynet_encode( $str ){
	global $DB_id;
	$key = pack('H*', md5($DB_id));
	$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
	$pad = $size - (strlen($str) % $size);
	$str = $str . str_repeat(chr($pad), $pad);
	$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
	$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	mcrypt_generic_init($td, $key, $iv);
	$data = mcrypt_generic($td, $str);
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	return urlencode(enc('e' , $data)); // -- 익스버그 해결 -- 2019-10-01 LCY
}


// --- 복호화 ---
function onedaynet_decode( $encode_str ){
	global $DB_id;
	$encode_str = urldecode($encode_str); // -- 익스버그 해결 -- 2019-10-01 LCY
	$key = pack('H*', md5($DB_id));
	$decrypted= mcrypt_decrypt(MCRYPT_RIJNDAEL_128,$key, enc('d',$encode_str), MCRYPT_MODE_ECB);
	$dec_s = strlen($decrypted);
	$padding = ord($decrypted[$dec_s-1]);
	$decrypted = substr($decrypted, 0, -$padding);
	return $decrypted;
}


// 세션생성
function mk_sess($Name, $Val) {
    if(PHP_VERSION < '5.3.0') session_register($Name);
    $$Name = $_SESSION[$Name] = $Val;
}
// 세션 view
function view_sess($Name) { return (isset($_SESSION[$Name])?$_SESSION[$Name]:''); }
// 쿠키생성
function mk_cookie($Name, $Val) {

    $CName = md5($Name);
    samesiteCookie(md5($Name), base64_encode($Val), 0, "/");
    $$CName = $_COOKIE[$CName] = base64_encode($Val);
}
// 쿠키 view
function view_cookie($Name) {
    $cookie = md5($Name);
    if (array_key_exists($cookie, $_COOKIE)) return base64_decode($_COOKIE[$cookie]);
    else return "";
}


// 관리자 세션로그인 ADD -- $_uid 추가 :: 관리자 고유번호 -- 2017-07-24 LCY
function AdminLogin($_uid) {
    global $_SESSION, $_SERVER;
    if($_uid == ''){ return false; }
    $siteInfo = get_site_info();

    mk_sess('AuthAdminDiff', md5($siteInfo['s_license'].$_SERVER['REMOTE_ADDR'].$_uid));
    mk_cookie('AuthAdminDiff', $_SESSION['AuthAdminDiff']);
}
// 관리자 세션로그아웃
function AdminLogout() {
	global $system;
    @session_unset(); // 모든 세션변수를 언레지스터 시켜줌
    @samesiteCookie("AuthAdmin","",time() - 100000,"/");
    @samesiteCookie("AuthCompany","",time() - 100000,"/");
    @samesiteCookie("AuthAdmin", "" , 0, "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
    mk_cookie('AuthAdminDiff', '', time() - 100000);
}
// 관리자 로그인체크
function AdminLoginCheck($_mode='move') {
    global $_SESSION, $_SERVER, $_COOKIE;

    if(basename($_SERVER['SCRIPT_FILENAME']) == 'index.php') { return; }
    $siteInfo = get_site_info();
    $AuthDiffCookie = view_cookie('AuthAdminDiff');
    $AuthDiff[] = md5($siteInfo['s_license'].$_SERVER['REMOTE_ADDR'].$_COOKIE['AuthAdmin']);
    $AuthDiff[] = (isset($_SESSION['AuthAdminDiff'])?$_SESSION['AuthAdminDiff']:null);
    $AuthDiff[] = ($AuthDiffCookie?$AuthDiffCookie:'0');
    $AuthDiff = @array_flip(array_filter($AuthDiff));

    if( count($AuthDiff) === 1 && $AuthDiffCookie) { return true; }
    else { if($_mode == 'move') { AdminLogout(); die("<script>top.location.href=('".OD_ADMIN_URL."/logout.php')</script>"); } return false; }
}
// 최고관리자 체크 == 통합관리자
function is_master()
{
	if(empty($_COOKIE["AuthAdmin"]) == false){
		return true;
	}else{
		return false;
	}
}


// 입점 관리자 세션로그인 ADD -- $_uid 추가 :: 관리자 고유번호 -- 2017-07-24 LCY
function SubAdminLogin($_uid) {
    global $_SESSION, $_SERVER;
    if($_uid == ''){ return false; }
    $siteInfo = get_site_info();
    if(is_mobile() === true) mk_sess('AuthSubAdminDiff', md5($siteInfo['s_license'].$_SERVER['HTTP_USER_AGENT'].$_uid)); // 모바일에서는 아이피가 수시로 바뀌기 때문에 아이피 항목 제외
    else mk_sess('AuthSubAdminDiff', md5($siteInfo['s_license'].$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$_uid));
    mk_cookie('AuthSubAdminDiff', $_SESSION['AuthSubAdminDiff']);
}
// 입점 관리자 세션로그아웃
function SubAdminLogout() {
	global $system;
    @session_unset(); // 모든 세션변수를 언레지스터 시켜줌
    @samesiteCookie("AuthCompany","",time() - 100000,"/");
    @samesiteCookie("AuthCompany", "" , 0, "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
    mk_cookie('AuthSubAdminDiff', '', time() - 100000);
}
// 입점 관리자 로그인체크
function SubAdminLoginCheck($_mode='move') {
    global $_SESSION, $_SERVER, $_COOKIE;

    if(basename($_SERVER['SCRIPT_FILENAME']) == 'index.php') { return; }
    $siteInfo = get_site_info();
    $AuthDiffCookie = view_cookie('AuthSubAdminDiff');
    if(is_mobile() === true) $AuthDiff[] = md5($siteInfo['s_license'].$_SERVER['HTTP_USER_AGENT'].$_COOKIE['AuthCompany']); // 모바일에서는 아이피가 수시로 바뀌기 때문에 아이피 항목 제외
    else $AuthDiff[] = md5($siteInfo['s_license'].$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$_COOKIE['AuthCompany']);
    $AuthDiff[] = (isset($_SESSION['AuthSubAdminDiff'])?$_SESSION['AuthSubAdminDiff']:null);
    $AuthDiff[] = ($AuthDiffCookie?$AuthDiffCookie:'0');
    $AuthDiff = @array_flip(array_filter($AuthDiff));

    if( count($AuthDiff) === 1 && $AuthDiffCookie) { return true; }
    else { if($_mode == 'move') { AdminLogout(); die("<script>top.location.href=('".OD_SUB_ADMIN_URL."/logout.php')</script>"); } return false; }
}


// 사용자 세션로그인 ADD
function UserLogin($_uid) {
	global $_SESSION, $_SERVER;
	if($_uid == ''){ return false; }
	$siteInfo = get_site_info();
	if(is_mobile() === true) mk_sess('AuthUserDiff', md5($siteInfo['s_license'].$_SERVER['HTTP_USER_AGENT'].$_uid.$_uid)); // 모바일에서는 아이피가 수시로 바뀌기 때문에 아이피 항목 제외
	else mk_sess('AuthUserDiff', md5($siteInfo['s_license'].$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$_uid.$_uid));
	mk_cookie('AuthUserDiff', $_SESSION['AuthUserDiff']);
}
// 사용자 세션로그아웃
function UserLogout() {
	global $system;
	@session_unset(); // 모든 세션변수를 언레지스터 시켜줌
	@samesiteCookie("AuthIndividualMember","",time() - 100000,"/");
	@samesiteCookie("AuthIndividualMember", "" , 0, "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
	@samesiteCookie("AuthShopCOOKIEID","",time() - 100000,"/");
	@samesiteCookie("AuthShopCOOKIEID", "" , 0, "/" , "." . str_replace("www." , "" , reset(explode(':', $system['host']))));
	samesiteCookie('AuthShopCOOKIEID', md5(serialize($_SERVER) . mt_rand(0,9999999)) , 0 , '/', '.'.str_replace('www.', '', reset(explode(':', $system['host']))));
	mk_cookie('AuthUserDiff', '', time() - 100000);
}
// 사용자 로그인체크
function UserLoginCheck($_mode='move') {
	global $_SESSION, $_SERVER, $_COOKIE;

	$siteInfo = get_site_info();
	$AuthDiffCookie = view_cookie('AuthUserDiff');
	if(is_mobile('auto') === true) $AuthDiff[] = md5($siteInfo['s_license'].$_SERVER['HTTP_USER_AGENT'].$_COOKIE['AuthIndividualMember'].$_COOKIE['AuthShopCOOKIEID']); // 모바일에서는 아이피가 수시로 바뀌기 때문에 아이피 항목 제외 // 2019-07-25 SSJ :: 모바일에서 PC버전보기 시 로그아웃되는 현상 수정 - if(is_mobile() === true) => if(is_mobile('auto') === true)
	else $AuthDiff[] = md5($siteInfo['s_license'].$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$_COOKIE['AuthIndividualMember'].$_COOKIE['AuthShopCOOKIEID']);
	$AuthDiff[] = (isset($_SESSION['AuthUserDiff'])?$_SESSION['AuthUserDiff']:null);
	$AuthDiff[] = ($AuthDiffCookie?$AuthDiffCookie:'0');
	$AuthDiff = @array_flip(array_filter($AuthDiff));

	if(count($AuthDiff) === 1 && $AuthDiffCookie) { return true; }
	else { if($_mode == 'move' && !preg_match('/member.login.pro.php/i', $_SERVER['REQUEST_URI'])) { UserLogout(); die("<script>alert('로그인 세션이 만료되었습니다.'); top.location.href=('".OD_PROGRAM_URL."/member.login.pro.php?_mode=logout')</script>"); } return false; }
}



// 콘솔모드에 데이터를 출력한다. (https://github.com/adamschwartz/log)
/*
console('BOX', 'box');
console('CODE', 'code');
console('RED', 'red');
console('BLUE', 'blue');
console('text');
console('[c="color:#d200ff; font-weight:bold"]user style[c]');
console('user style2', 'color:#d200ff; font-weight:bold; font-size:20px;');

console('user style333', 'color:#ff0000; font-weight:bold; font-size:20px;');
*/
function console($Data, $style='') {
	global $system;
	static $tb_console2 = true;

	$uniqid = uniqid();

	if($tb_console2) {

		echo "<script>\r\n//<![CDATA[\r\nif(!console){var console={log:function(){}}}\r\n</script>".PHP_EOL;
		echo "<script src='".$system['__url']."/include/js/log.min.js'></script>".PHP_EOL;
		echo "<script>
			var ConsoleLogBox = 'font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; color: #fff; font-size: 20px; padding: 15px 20px; background: #444; border-radius: 4px; line-height: 100px; text-shadow: 0 1px #000';
			var ConsoleLogCode = 'background: rgb(255, 255, 219); padding: 1px 5px; border: 1px solid rgba(0, 0, 0, 0.1)';
			var ConsoleLogFontRed = 'color:#ff0000';
			var ConsoleLogFontBlue = 'color:#2400ff';
			</script>".PHP_EOL;
	}

	if($style == 'box') { $StyleCode = 'ConsoleLogBox'; }
	else if($style == 'code') { $StyleCode = 'ConsoleLogCode'; }
	else if($style == 'red') { $StyleCode = 'ConsoleLogFontRed'; }
	else if($style == 'blue') { $StyleCode = 'ConsoleLogFontBlue'; }
	else if($style) { $StyleCode = 'ConsoleLogUserStyle_'.$uniqid; }

	echo "<script>";
	if($StyleCode == 'ConsoleLogUserStyle_'.$uniqid) echo 'var ConsoleLogUserStyle_'.$uniqid.' = "'.$style.'"; '.PHP_EOL;
	if($style) {

		$LogHeader = "%c";
		$LogTail = ", $StyleCode";
	}

	$output = explode("\r\n", $Data);
	if(is_array($Data)) {

		$lines = json_encode($Data);
		echo "console.table({$lines});".PHP_EOL;
	}
	else if(trim($output)) {

		foreach($output as $line) {
			$line = addslashes($line);
			echo "log('{$LogHeader}{$line}'{$LogTail}); ".PHP_EOL;
		}
	}
	echo "</script>".PHP_EOL;

	$tb_console2 = false;
}





// 디바이스정보 LDD 2016-12-02
//			추가 수정 JJC 2018-03-06 -- output , agent 함수 추가
function Get_device_info($output=NULL , $agent=NULL) {

	$Info = array();
	$agent = $agent ? $agent : $_SERVER['HTTP_USER_AGENT'];

	$Info['os'] = 'Unknown';
	$Info['browser'] = array();
	$Info['browser']['base_name'] = 'Unknown';
	$Info['browser']['name'] = 'Unknown';

	// 플랫 폼 확인
	if(preg_match('/Android|Apache-HttpClient\/UNAVAILABLE \(java/i', $agent)) $Info['os'] = 'android';

	else if(preg_match('/iPod|iPhone|iso/i', $agent)) $Info['os'] = 'ios';
	else if(preg_match('/android/i', $agent)) $Info['os'] = 'Android';
	else if(preg_match('/BlackBerry/i', $agent)) $Info['os'] = 'blackberry';
	else if(preg_match('/SymbianOS/i', $agent)) $Info['os'] = 'symbianos';
	else if(preg_match('/Windows CE/i', $agent)) $Info['os'] = 'windows ce';
	else if(preg_match('/webOS/i', $agent)) $Info['os'] = 'webos';
	else if(preg_match('/PalmOS/i', $agent)) $Info['os'] = 'palmos';

	else if(preg_match('/macintosh|mac os x/i', $agent)) $Info['os'] = 'mac';
	else if(preg_match('/linux/i', $agent)) $Info['os'] = 'linux';
	else if(preg_match("/windows 98/i", $agent))             $Info['os'] = "98";
	else if(preg_match("/windows 95/i", $agent))             $Info['os'] = "95";
	else if(preg_match("/windows nt 4\.[0-9]*/i", $agent))   $Info['os'] = "NT";
	else if(preg_match("/windows nt 5\.0/i", $agent))        $Info['os'] = "2000";
	else if(preg_match("/windows nt 5\.1/i", $agent))        $Info['os'] = "XP";
	else if(preg_match("/windows nt 5\.2/i", $agent))        $Info['os'] = "2003";
	else if(preg_match("/windows nt 6\.0/i", $agent))        $Info['os'] = "Vista";
	else if(preg_match("/windows nt 6\.1/i", $agent))        $Info['os'] = "Windows7";
	else if(preg_match("/windows nt 6\.2/i", $agent))        $Info['os'] = "Windows8";
	else if(preg_match("/windows nt 10\.0/i", $agent))        $Info['os'] = "Windows10";// JJC : 브라우저 추가 : 2018-10-04
	else if(preg_match("/windows 9x/i", $agent))             $Info['os'] = "ME";
	else if(preg_match("/windows ce/i", $agent))             $Info['os'] = "CE";
	else if(preg_match("/mac/i", $agent))                    $Info['os'] = "MAC";
	else if(preg_match("/linux/i", $agent))                  $Info['os'] = "Linux";
	else if(preg_match("/sunos/i", $agent))                  $Info['os'] = "sunOS";
	else if(preg_match("/irix/i", $agent))                   $Info['os'] = "IRIX";
	else if(preg_match("/phone/i", $agent))                  $Info['os'] = "Phone";
	else if(preg_match("/bot|slurp/i", $agent))              $Info['os'] = "Robot";
	else if(preg_match("/internet explorer/i", $agent))      $Info['os'] = "IE";
	else if(preg_match("/mozilla/i", $agent))                $Info['os'] = "Mozilla";

	// 브라우져 확인
	if(preg_match('/MSIE/i',$agent) && !preg_match('/Opera/i',$agent)) {
		$Info['browser']['base_name'] = 'Internet Explorer';
		$Info['browser']['name'] = "MSIE";
	}
	else if(preg_match('/Firefox/i',$agent)) {
		$Info['browser']['base_name'] = 'Mozilla Firefox';
		$Info['browser']['name'] = "Firefox";
	}
	else if(preg_match('/Chrome/i',$agent)) {
		$Info['browser']['base_name'] = 'Google Chrome';
		$Info['browser']['name'] = "Chrome";
	}
	else if(preg_match('/Safari/i',$agent)) {
		$Info['browser']['base_name'] = 'Apple Safari';
		$Info['browser']['name'] = "Safari";
	}
	else if(preg_match('/Opera/i',$agent)) {
		$Info['browser']['base_name'] = 'Opera';
		$Info['browser']['name'] = "Opera";
	}
	else if(preg_match('/Netscape/i',$agent)) {
		$Info['browser']['base_name'] = 'Netscape';
		$Info['browser']['name'] = "Netscape";
	}
	// JJC : 브라우저 추가 : 2018-10-04
	else if(preg_match('/windows nt/i',$agent) && preg_match('/rv:/i',$agent)) {
		$Info['browser']['base_name'] = 'Internet Explorer';
		$Info['browser']['name'] = "MSIE";
	}
	else if(preg_match('/NAVER/i',$agent)) {
		$Info['browser']['base_name'] = 'NAVER';
		$Info['browser']['name'] = "NAVER";
	}
	else if(preg_match('/DaumApps/i',$agent)) {
		$Info['browser']['base_name'] = 'DaumApps';
		$Info['browser']['name'] = "DaumApps";
	}
	else if(preg_match('/KAKAOTALK/i',$agent)) {
		$Info['browser']['base_name'] = 'KAKAOTALK';
		$Info['browser']['name'] = "KAKAOTALK";
	}
	else if(preg_match('/Instagram/i',$agent)) {
		$Info['browser']['base_name'] = 'Instagram';
		$Info['browser']['name'] = "Instagram";
	}
	else if(preg_match('/FBAN\/FBIOS|facebook/i',$agent)) {
		$Info['browser']['base_name'] = 'Facebook';
		$Info['browser']['name'] = "Facebook";
	}
	else if(preg_match('/NateOn/i',$agent)) {
		$Info['browser']['base_name'] = 'NateOn';
		$Info['browser']['name'] = "NateOn";
	}
	// JJC : 브라우저 추가 : 2018-10-04

	if($output == 'array') {
		$Return = array();
		$Return['os'] = addslashes("{$Info['os']}");
		$Return['browser'] = addslashes("{$Info['browser']['name']}({$Info['browser']['base_name']})");
		$Return['ip'] = addslashes("{$_SERVER['REMOTE_ADDR']}");
		$Return['agent'] = addslashes("{$_SERVER['HTTP_USER_AGENT']}");
		return $Return;
	}
	else {
		$Return = '';
		$Return = "[os] {$Info['os']}".PHP_EOL;
		$Return .= "[browser] {$Info['browser']['name']}({$Info['browser']['base_name']})".PHP_EOL;
		$Return .= "[IP] {$_SERVER['REMOTE_ADDR']}".PHP_EOL;
		$Return .= "[Agent] {$_SERVER['HTTP_USER_AGENT']}";
		return addslashes($Return);
	}
}


# 현재 접속이 https인지 확인 2017-06-21 LDD
function is_https() {
	if(isset($_SERVER['HTTPS'])) {
		if(strtolower($_SERVER['HTTPS']) == 'on') return true;
		else if($_SERVER['HTTPS'] == '1') return true;
	}
	else {
		return false;
	}
}



// XML 데이터를 PHP Array로 반환 2017-06-26 LDD
function xml2array($contents, $get_attributes=1, $priority = 'tag') {
	if(!$contents) return array();
	if(!function_exists('xml_parser_create')) return array();
	$parser = xml_parser_create('');
	xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, trim($contents), $xml_values);
	xml_parser_free($parser);
	if(!$xml_values) return;
	$xml_array = array();
	$parents = array();
	$opened_tags = array();
	$arr = array();
	$current = &$xml_array;
	$repeated_tag_index = array();
	foreach($xml_values as $data) {
		unset($attributes,$value);
		extract($data);
		$result = array();
		$attributes_data = array();
		if(isset($value)) {
			if($priority == 'tag') $result = $value;
			else $result['value'] = $value;
		}
		if(isset($attributes) and $get_attributes) {
			foreach($attributes as $attr => $val) {
				if($priority == 'tag') $attributes_data[$attr] = $val;
				else $result['attr'][$attr] = $val;
			}
		}
		if($type == "open") {
			$parent[$level-1] = &$current;
			if(!is_array($current) or (!in_array($tag, array_keys($current)))) {
				$current[$tag] = $result;
				if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
				$repeated_tag_index[$tag.'_'.$level] = 1;
				$current = &$current[$tag];
			}
			else {
				if(isset($current[$tag][0])) {
					$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
					$repeated_tag_index[$tag.'_'.$level]++;
				}
				else {
					$current[$tag] = array($current[$tag],$result);
					$repeated_tag_index[$tag.'_'.$level] = 2;
					if(isset($current[$tag.'_attr'])) {
						$current[$tag]['0_attr'] = $current[$tag.'_attr'];
						unset($current[$tag.'_attr']);
					}
				}
				$last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
				$current = &$current[$tag][$last_item_index];
			}
		}
		else if($type == "complete") {
			if(!isset($current[$tag])) {
				$current[$tag] = $result;
				$repeated_tag_index[$tag.'_'.$level] = 1;
				if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;
			}
			else {
				if(isset($current[$tag][0]) and is_array($current[$tag])) {
					$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
					if($priority == 'tag' and $get_attributes and $attributes_data) {
						$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
					}
					$repeated_tag_index[$tag.'_'.$level]++;
				}
				else {
					$current[$tag] = array($current[$tag],$result);
					$repeated_tag_index[$tag.'_'.$level] = 1;

					if($priority == 'tag' and $get_attributes) {

						if(isset($current[$tag.'_attr'])) {

							$current[$tag]['0_attr'] = $current[$tag.'_attr'];
							unset($current[$tag.'_attr']);
						}

						if($attributes_data) {

							$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
						}
					}
					$repeated_tag_index[$tag.'_'.$level]++;
				}
			}
		}
		elseif($type == 'close') {
			$current = &$parent[$level-1];
		}
	}
	return($xml_array);
}

// 배열을 shuffle 한다.
/*
	$arr = Array([독립필수품] => 1, [안전용품] => 1, [마이홈] => 1, [행복한덕질] => 1, [나만알고싶어] => 1, [너무예뻐] => 1, [사랑] => 1, [원데이넷] => 1, [상상너머] => 2, [패션] => 2, [애플] => 1, [빵] => 1, [빠리바게뜨앗] => 1, [브래드] => 1, [1234] => 1)
	$arr = shuffle_assoc($arr);

	return Array(
		[상상너머] => 2
		[빠리바게뜨앗] => 1
		[원데이넷] => 1
		[빵] => 1
		[너무예뻐] => 1
		[패션] => 2
		[사랑] => 1
		[1234] => 1
		[행복한덕질] => 1
		[브래드] => 1
		[마이홈] => 1
		[나만알고싶어] => 1
		[안전용품] => 1
		[애플] => 1
		[독립필수품] => 1
	)

*/
function shuffle_assoc($list) {
	if(!is_array($list)) return $list;
	$keys = array_keys($list);
	shuffle($keys);
	$random = array();
	foreach ($keys as $key) $random[$key] = $list[$key];
	return $random;
}




// 2017-07-12 ::: SSL 페이지 변별 후 자동이동 - 보안서버 ::: JJC
//		JJC - 2018-11-23 수정
//		- type
//				admin : 관리자
//				pc : PC 사용자
//				m : 모바일 사용자
//		SSJ - 2019-11-25 수정
//				- type삭제 : 보안서버 적용 여부만 체크하며 모든 페이지에 적용
function AutoHTTPSMove() {
	global $_SERVER , $pn , $CURR_FILENAME , $siteInfo;

	//보안서버 상태정보 추출
	$arr = ssl_condition_info();

	// 전체 - 사용여부
	$HTTP_STATUS = ($arr['ssl_status'] == 'N' ? 'N' : 'A'); // 사용시 전체페이지 적용

	// ssl 도메인
	$HTTP_SSL_DOMAIN = $arr['ssl_domain'];

	// 일반 도메인
	$HTTP_NORMAL_DOMAIN = "http://".(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']?($_SERVER['SSL_TLS_SNI']?$_SERVER['SSL_TLS_SNI']:reset(explode(':', $_SERVER['HTTP_HOST']))):$_SERVER['HTTP_HOST']);


	// 사용여부에 따른 처리
	switch($HTTP_STATUS){

		// 미사용시
		case 'N':
			if($_SERVER['HTTPS']) {
				@header("Location:" . $HTTP_NORMAL_DOMAIN . $_SERVER['REQUEST_URI']);
				exit;
			}
			break;

		// 전체페이지
		case 'A':
			// 보안서버 제외페이지
			 if( !$_SERVER['HTTPS'] ) {
				if(HTTPS_Check()){
					@header("Location:" . $HTTP_SSL_DOMAIN .$_SERVER['REQUEST_URI']);
					exit;
				}
				else {
					ssl_forced_reset();// SSL 사용안함 강제처리
				}
			}
			break;

	}

}// 2017-07-12 ::: SSL 페이지 변별 후 자동이동 - 보안서버 ::: JJC


// -- LCY :: 매뉴얼 이동 함수 ;; $key값 필요
function openMenualLink($key)
{
	global $arrMenualLink;

	if( count($arrMenualLink) < 1){ return false; } // 기본적으로 키값이 없을 시 에러
	if( $arrMenualLink[$key]['link'] == ''){ return false; } // 링크가 없을 시 에러

	$link = $arrMenualLink[$key]['link'];
	$target = $arrMenualLink[$key]['target'] != '' ? $arrMenualLink[$key]['target'] : '_blank';
	$use = $arrMenualLink[$key]['use'] != '' ? $arrMenualLink[$key]['use'] : 'N';
	$title = $arrMenualLink[$key]['title'] != '' ? $arrMenualLink[$key]['title'] : '메뉴얼';
	$aTag = "<a href='".$link."' class='m_btn' target = '".$target."' title='".$title."' ></a>";

	return $aTag;


}


// LDD: 2017-10-11 현사이트가 SSL사용이 가능한 상태인지 확인 ==> (HTTPS_Check() === true?'ssl 사용가능':'ssl 사용불가')
function HTTPS_Check($_mode='fsockopen') {
	global $system;
	if($system['ssl_use'] === false) return false;
	if(!$system['ssl_port']) $system['ssl_port'] = 443;
	if(!$system['ssl_domain']) $system['ssl_domain'] = $system['host'];
	if($_mode == 'fsockopen') {
		return PingTest($system['ssl_domain'], $system['ssl_port'], 3);
	}
	else {
		$sslUrl = 'https://'.$system['ssl_domain'].':'.$system['ssl_port'];
		return (CurlExecHeader($sslUrl) == 200?true:false);
	}
}

# ping test 2016-08-27 LDD
function PingTest($host,$port=80,$timeout=6) {
	$fsock = @fsockopen($host, $port, $errno, $errstr, $timeout);
	if(!$fsock) { return false; }
	else {
		fclose($fsock);
		return TRUE;
	}
}


/**
 * LDD: 특정폴더의 용량 판별
 *
 * @param      string  $path  DOCUMENT_ROOT로부터의 경로
 * @return     array  array('DiskSize'=>용량, 'DiskFile'=>내부 파일 개수)
 */
function CheckDirSize($path='/') {
	static $DiskSize, $DiskFile;

	$dir = $_SERVER['DOCUMENT_ROOT'].str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
	if(is_dir($dir)) {
		$fp = opendir($dir);
		while(false !== ($entry = readdir($fp))) {
			if($entry != "." && $entry != "..") {
				if(is_dir($dir.'/'.$entry)) {
					clearstatcache();
					CheckDirSize($dir.'/'.$entry);
				}
				else if(is_file($dir.'/'.$entry)){
					$DiskSize += filesize($dir.'/'.$entry);
					clearstatcache();
					$DiskFile++;
				}
			}
		}
		closedir($fp);
	}
	return array('DiskSize'=>$DiskSize, 'DiskFile'=>$DiskFile);
}


/**
 * LDD: 용량을 T, G, M, K, byte등으록 표기
 *
 * @param      integer  용량
 * @return     integer  용량(T, G, M, K, byte)
 */
function SizeText($Size=0) {
	if(is_numeric($Size)) { // $Size 가 숫자일경우
		if($Size >= 1099511627776) $Size = number_format($Size/1099511627776, 1) . "T";
		else if($Size >= 1073741824) $Size = number_format($Size/1073741824, 1) . "G";
		else if($Size >= 1048576) $Size = number_format($Size/1048576, 1) . "M";
		else if($Size >= 1024) $Size = number_format($Size/1024, 1) . "K";
		else $Size = number_format($Size, 0) . "byte";
	}
	else {
		$Size = 0;
	}

	return $Size;
}


/**
	* -- LCY :: 2017-11-03 -- 입력값 체크
	* @param $input => 입력값, $option => 검색옵션 ('ko'=>'한글' , 'htel'=>'휴대폰번호')
	* @return true or false;
	* @detail 한글 이름검색 시 사용
	* @comment 한글문자 체크 (자음 모음은 검색 X)
	* @comment 휴대폰번호 검색은 전체번호를 넘겨주어야합니다.
**/
function checkInputValue($input,$option='')
{
	global $siteInfo;

	if( is_array($siteInfo) || count($siteInfo) < 1){ $siteInfo = get_site_info(); }
	$chk = false;

	switch($option){
		// -- 한글검색
		case "ko":
			// -- 완성된 한글문자만 체크()
			$chk = preg_replace('/[\x{1100}-\x{11FF}\x{AC00}-\x{D7AF}]+/u', '', $input);
			if($chk == ''){ return true; }
			else { return false; }
		break;

		// -- 영문검색
		case "en":
			// -- 영문만 체크
			$chk = preg_match("/^[a-zA-Z]*$/",$input);
			if($chk > 0 ){ return true; }
			else { return false; }
		break;

		// -- 영문+숫자검색
		case "enum":
			// -- 영문+숫자 체크
			$chk = preg_match("/^[a-zA-Z0-9]*$/",$input);
			if($chk > 0 ){ return true; }
			else { return false; }
		break;

		// -- 휴대폰번호 검색
		case "htel":
			$chk = preg_match("/^(01[016789]{1}|02|0[3-9]{1}[0-9]{1})-?[0-9]{3,4}-?[0-9]{4}$/", $input);
			if($chk > 0){ return true; }
			else{ return false; }
		break;

		// -- 이메일검사
		case "email":
			$chk = filter_var($input, FILTER_VALIDATE_EMAIL);
			if( $chk == false){ return false; }
			else { return true; }
		break;

		default: return false; break;
	}

}


// 검색엔진 정보 호출
function GetSearchEngin($url='') {
	global $system;

	// 기본값
	$engin = 'unknown';
	$detail = 'unknown';
	$keyword = 'unknown';

	// 키워드 추출
	if(isset($url)) {
		$UrlInfo = parse_url($url, PHP_URL_QUERY);
		parse_str($UrlInfo, $UrlInfo);
	}
	else {
		$UrlInfo = array();
	}

	// 검색엔진별 판단
	if(preg_match("/google./i", $url)) { // 구글 처리
		$engin = '구글';
		$detail = 'unknown';
		$keyword = ($UrlInfo['q']?$UrlInfo['q']:($UrlInfo['q']?$UrlInfo['oq']:'unknown'));
	}
	else if(preg_match("/naver.com/i", $url)) { // 네이버 처리
		/*
			통합검색 http://search.naver.com/search.naver?where=nexearch
			사이트 http://web.search.naver.com/search.naver?where=site
			블로그 http://cafeblog.search.naver.com/search.naver?where=post
			카페 http://cafeblog.search.naver.com/search.naver?where=article
			지식인 http://kin.search.naver.com/search.naver?where=kin
			뉴스 http://news.search.naver.com/search.naver?where=news
		*/
		$engin = '네이버';
		$detail = '통합검색';
		$keyword = ($UrlInfo['query']?$UrlInfo['query']:'unknown');
		if(preg_match("/search.naver.com/i", $url)) $detail = '통합검색';
		else if(preg_match("/web.search.naver.com/i", $url)) $detail = '사이트';
		else if(preg_match("/cafeblog.search.naver.com/i", $url) && $UrlInfo['where'] == 'post') $detail = '블로그';
		else if(preg_match("/cafeblog.search.naver.com/i", $url) && $UrlInfo['where'] == 'article') $detail = '카페';
		else if(preg_match("/kin.search.naver.com/i", $url)) $detail = '지식인';
		else if(preg_match("/news.search.naver.com/i", $url)) $detail = '뉴스';
		else if(preg_match("/shopping.naver.com/i", $url)) $detail = '지식쇼핑';

		if(preg_match("/blog.naver.com/i", $url)) {
			$engin = '사이트링크';
			$detail = '블로그';
		}
		else if(preg_match("/cafe.naver.com/i", $url)) {
			$engin = '사이트링크';
			$detail = '카페';
		}
	}
	else if(preg_match("/daum.net\/search/i", $url) || preg_match("/shopping.daum.net/i", $url)) { // 다음 처리
		/*
			쇼핑 http://shopping.daum.net/#!search=
			통합검색 http://search.daum.net/search?&w=tot
			사이트 http://search.daum.net/search?w=site
			블로그 http://search.daum.net/search?w=blog
			카페 http://search.daum.net/search?w=cafe
			뉴스 http://search.daum.net/search?w=news
		*/
		$engin = '다음';
		$detail = '통합검색';
		if(preg_match("/shopping.daum.net/i", $url)) $keyword = ($UrlInfo['search']?$UrlInfo['search']:'unknown');
		else $keyword = ($UrlInfo['q']?$UrlInfo['q']:'unknown');

		if(preg_match("/shopping.daum.net/i", $url)) $detail = '쇼핑';
		else if(preg_match("/search.daum.net\/search/i", $url) && $UrlInfo['w'] == 'tot') $detail = '통합검색';
		else if(preg_match("/search.daum.net\/search/i", $url) && $UrlInfo['w'] == 'site') $detail = '사이트';
		else if(preg_match("/search.daum.net\/search/i", $url) && $UrlInfo['w'] == 'blog') $detail = '블로그';
		else if(preg_match("/search.daum.net\/search/i", $url) && $UrlInfo['w'] == 'cafe') $detail = '카페';
		else if(preg_match("/search.daum.net\/search/i", $url) && $UrlInfo['w'] == 'news') $detail = '뉴스';
	}
	else if(preg_match("/daum.net\/nate/i", $url)) { // 네이트 처리
		$engin = '네이트';
		$detail = 'unknown';
		$keyword = ($UrlInfo['q']?$UrlInfo['q']:'unknown');
	}
	else if(preg_match("/search.zum.com/i", $url)) { // 줌 처리
		$engin = '줌';
		$detail = 'unknown';
		$keyword = ($UrlInfo['query']?$UrlInfo['query']:'unknown');
	}
	else if(preg_match("/search.dreamwiz.com/i", $url)) { // 드림위즈 처리
		$engin = '드림위즈';
		$detail = 'unknown';
		$keyword = ($UrlInfo['sword']?$UrlInfo['sword']:'unknown');
	}
	else if(preg_match("/powersearch.korea.com/i", $url)) { // 코리아닷컴 처리
		$engin = '코리아닷컴';
		$detail = 'unknown';
		$keyword = ($UrlInfo['query']?$UrlInfo['query']:'unknown');
	}
	else if(preg_match("/bing.com/i", $url)) { // 빙 처리
		$engin = 'Bing';
		$detail = 'unknown';
		$keyword = ($UrlInfo['q']?$UrlInfo['q']:'unknown');
	}
	else if(preg_match("/duckduckgo.com/i", $url)) { // DuckDuckGo 처리
		$engin = 'DuckDuckGo';
		$detail = 'unknown';
		$keyword = ($UrlInfo['q']?$UrlInfo['q']:'unknown');
	}


	// 직접접속 또는 링크 접속 판단
	if(preg_match("/{$system['host']}/i", $url) || !$url) {
		$engin = '직접접속';
		$detail = 'unknown';
	}
	else if($keyword == 'unknown' && $url && !in_array($detail, array('지식쇼핑', '쇼핑', '블로그', '카페'))) {

		$engin = '링크';
		$detail = 'unknown';
	}

	return array('engin'=>$engin, 'detail'=>$detail, 'keyword'=>$keyword, 'url'=>$url);
}


/*
	URI_Rebuild(uri, 변경 또는 추가할 URI 요소 배열, 삭제할 URI 요소 배열)
	URI에 동일 배열 추가시 주소 뒤에 계속 추가되는 현상 보정
	예시>
		URI_Rebuild('?test=1&test=2') => ?test=2
		URI_Rebuild('?test=1&test=2', array('test'=>3, 'add'=>1)) => ?test=3&add=1
		URI_Rebuild('?test=1&test=2&re=removed', array('test'=>3, 'add'=>1), array('re')) => ?test=3&add=1
*/
function URI_Rebuild($uri = '', $AddUri=array(), $RemoveUri=array()) {
	if(!$uri) return;
	$uri_prefix = '';
	$url_rebuild = $uri;

	// 첫글자가 ?로 시작하면 접두사에 저장하고 제거한다.
	if(substr($url_rebuild, 0, 1) == '?') {
		$url_rebuild = substr($url_rebuild, 1);
		$uri_prefix = '?';
	}
	parse_str($url_rebuild, $url_rebuild); // URI를 배열화
	if(count($AddUri) > 0) $url_rebuild = array_filter(array_merge($url_rebuild, $AddUri)); // 추가 URI가 있다면 추가
	if(count($RemoveUri) > 0) {
		foreach($RemoveUri as $k=>$v) {
			unset($url_rebuild[$v]);
		}
	}
	$url_rebuild = http_build_query($url_rebuild); // 다시 URI 평문으로 빌드
	$uri = str_replace('?=&', '?', $url_rebuild); // 잔여 삭제// GET 변경 - sql injection 막기
	if($uri == '?') $uri = '';
	if($uri == '') $uri_prefix = '';
	return $uri_prefix.$uri;
}

/**
	-- LCY :: Serialize 화된 무료배송이벤트 아이템을 가져온다. --
		@@ type -- array();
		@@ item -- use 사용여부 Y,N
		@@ item -- sdate 시작일
		@@ item -- edate 종료일
		@@ item -- minPrice = 최소결제금액
		@@ item -- setMember = 대상 ('all','group') 등급
		@@ item -- setGroupUid = 등급지정 시 해당등급의 고유번호
		@@ item -- mdate = 수정일
		@@ ------- addslashes, stripslashes  반드시 사용
**/
function getPromotionEventDelivery()
{
	global $siteInfo;
	if( $siteInfo['promotion_event_delivery_config'] != ''){
		$arrItem = unserialize(stripslashes($siteInfo['promotion_event_delivery_config']));
		if( count($arrItem) < 1 ){ return array(); }
		return $arrItem;
	}else{
		return array();
	}
}



// 숫자에 따른 요일 지정 함수
//		0~6까지 숫자에 따른 요일명 지정
//		str 의 경우 통상 "요일"을 입력함.
function week_name( $w , $str=NULL){
	switch( $w ){
		case 0 : $_wm = "일"; break;
		case 1 : $_wm = "월"; break;
		case 2 : $_wm = "화"; break;
		case 3 : $_wm = "수"; break;
		case 4 : $_wm = "목"; break;
		case 5 : $_wm = "금"; break;
		case 6 : $_wm = "토"; break;
	}
	return $_wm . $str ;
}

	// 날짜 따른 요일 지정 함수
	//		rdate - YYYY-mm-dd 형식
	//		str 의 경우 통상 "요일"을 입력함.
	function DateToWeekName( $rdate , $str=NULL){
		$w = $rdate ? date("w" , strtotime($rdate)) : date("w");
		switch( $w ){
			case 0 : $_wm = "일"; break;
			case 1 : $_wm = "월"; break;
			case 2 : $_wm = "화"; break;
			case 3 : $_wm = "수"; break;
			case 4 : $_wm = "목"; break;
			case 5 : $_wm = "금"; break;
			case 6 : $_wm = "토"; break;
		}
		return $_wm . $str ;
	}


# 지정된 수의 앞글자만 남기고 뒷글자 모두 가림
/*
echo LastCut('홍길동', 2, '○'); => 홍○○
echo LastCut('상상너머', 2, '※'); => 상상※※
*/
function LastCut($str='', $leng=3, $suffix='*') {

	$Ostr = $str;
	$str_leng = mb_strlen($str, 'UTF-8');
	$str = mb_substr($str, 0, $leng,'UTF-8');

	for($i=0;$i<($str_leng-$leng); $i++) { $str .= $suffix; }
	return $str;
}

// 뒤에 $leng 만큼 $suffix 고정으로 글자를 가림
function LastCut2($str='', $leng=3, $suffix='*') {

	$Ostr = $str;
	$str_leng = mb_strlen($str, 'UTF-8');
	if( $str_leng <= $leng){  $leng = $leng-1; }
	$str = mb_substr($str, 0, ($str_leng-$leng),'UTF-8');

	//for($i=0;$i<($str_leng-$leng); $i++) { $str .= $suffix; }
	for($i=0;$i<$leng; $i++) { $str .= $suffix; } // LDD: 2018-07-21
	return $str;
}



/*
	LCY :: 2018-01-13 -- 게시판 스킨을 배열로 가져올 수 있도록 추가 --
	@ 스킨명에 해당되는 스킨정보 호출
	@ $skinName : 스킨명의 경우 skin/board/[스킨명]
	@ $skinName : pc, 또는 mobile
	@ return array : $skinInfo['스킨명']
*/
function getBoardSkinInfo($boardSkinName=false,$ua=false)
{
	$ua = $ua == false ? 'pc' : strtolower($ua);
	$boardSkinInfo = array(); // 게시판 스킨 정보를 담을 배열

	if( $ua == 'pc'){
		$rootDirBoardSkin = 'board';
	}else{
		$rootDirBoardSkin = 'board_m';
	}

	// -- 스킨리스트를 가져온다.
	$boardSkinList = array();

	// -- 스킨명이 없다면 전체 스킨을 호출 :: PC
	if($boardSkinName === false) {
		$boardSkinTmp = dir(OD_SKIN_ROOT.'/'.$rootDirBoardSkin.'/');
		while($entry = $boardSkinTmp->read()) {
		    if(in_array($entry, array('..', '.'))) continue;
		    $boardSkinList[] = $entry;
		}
	}else{
		$boardSkinList[] = $boardSkinName; // 지정된 스킨명을담는다
	}

	sort($boardSkinList); // 폴더명 순으로 정렬

	if( count($boardSkinList)  > 0 ) {
		foreach($boardSkinList as $k=>$v) {
			// -- 스킨정보 :: PC
			//$boardSkinTmpPc = dir(OD_SKIN_ROOT.'/'.$rootDirBoardSkin.'/');
			if(file_exists(OD_SKIN_ROOT.'/'.$rootDirBoardSkin.'/'.$v.'/skin.xml')) {
				$boardSkinInfo[$v] = xml2array(file_get_contents(OD_SKIN_ROOT.'/'.$rootDirBoardSkin.'/'.$v.'/skin.xml'));
				// -- 썸네일이 있다면
				if(file_exists(OD_SKIN_ROOT.'/'.$rootDirBoardSkin.'/'.$v.'/thumb.png')) $boardSkinInfo[$v]['skin']['board_thumb'] = OD_SKIN_URL.'/'.$rootDirBoardSkin.'/'.$v.'/thumb.png';
				else $boardSkinInfo[$v]['skin']['board_thumb'] = '';

			}
		}
	}


	return $boardSkinInfo;
}


// @ 2017-03-20 LCY :: 익스에서 한글파일 깨짐현상
function utf2euc($str) { return iconv("UTF-8","cp949//IGNORE", $str); }
function getChkIE() {
	if(!isset($_SERVER['HTTP_USER_AGENT'])) return false;
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) return true; // IE8
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Windows NT 6.1') !== false) return true; // IE11
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko') !== false) return true; // IE11 - 업데이트 버전
	return false;
}




// --- JJC : 날짜 차이 함수 - 기획전에서 사용 : 2018-01-22 ---
//			$edate -> 뒤 날짜
//			$sdate -> 앞 날짜
if (!function_exists('fn_date_diff')) {
	function fn_date_diff($edate,$sdate){
		$r = strtotime($edate) - strtotime($sdate) ;
		return ceil($r / (60*60 *24   )) ;
	}
}
// --- 날짜 차이 함수 ---

/*
	// -- date 출력함수
	// -- $_type = false :: 년.월.일 <div class="t_light">시:분:초</span>
	// -- $_type = date :: 년-월-일
*/
function printDateInfo($date,$_type = false)
{
	if($_type == false){
		$d = date('Y.m.d',strtotime($date));
		$hm = date('H:i:s',strtotime($date));
		return $d.'<div class="t_light">'.$hm.'</div>';
	}else{
		return date($_type,strtotime($date));
	}
}


// -- 함수수정
// - 배열 넘김시 사용되는 변수 encode / decode ---
function enc_array($mode, $str) {
    if(!function_exists('onedaynet_encode') || !function_exists('onedaynet_decode')) return '필수 함수가 없습니다.';
    if($mode == 'd') return unserialize(onedaynet_decode($str));
    else if($mode == 'e') return onedaynet_encode(serialize($str));
    else return 'error';
}
//예) 인코딩 => enc_array( 'e' ,  array('테스트입니다.'));
//예) 디코딩 => enc_array( 'd' ,  $인코딩);
// - 배열 넘김시 사용되는 변수 encode / decode ---



// 상품 리스트의 정렬 링크를 만들어 준다.
/*
	ProductOrderLinkBuild(array('_order'=>'sale', 'listpg'=>1))
	└> /?pn=product.list&cuid=147&listpg=1&_order=sale
*/
function ProductOrderLinkBuild($option=array()) {
	global $_PVSC;

	// 기본1
	$url = array();
	$url['pn'] = $_GET['pn']; // pn
	$url['cuid'] = $_GET['cuid']; // 카테고리 코드
	$url['_order'] = $_GET['_order']; // 지정된 정렬
	$url['order_field'] = $_GET['order_field']; // 직접 정렬필드
	$url['order_sort'] = $_GET['order_sort']; // 직접 정렬 방식
	$url['list_type'] = $_GET['list_type']; // 리스트 출력 방식
	$url['listmaxcount'] = $_GET['listmaxcount']; // 출력개수

	// 검색관련
	$url['search_hashtag'] = $_GET['search_hashtag']; // 해시태그
	$url['search_word'] = str_replace('#', '%23', $_GET['search_word']); // 검색어
	$url['detail_search'] = $_GET['detail_search']; // 상세검색 여부
	$url['search_word_detail'] = $_GET['search_word_detail']; // 상세 검색어
	$url['search_price'] = $_GET['search_price']; // 가격대
	$url['search_brand'] = $_GET['search_brand']; // 브랜드
	$url['search_boon'] = $_GET['search_boon']; // 혜택구분


	// 기본2
	$url['listpg'] = $_GET['listpg']; // 페이지

	// 사용자지정
	if(count($option) > 0) {
		foreach($option as $k=>$v) {
			$url[$k] = $v;
		}
	}

	// 완성형 URL 반환
	$re_url = URI_Rebuild(http_build_query($url));
	return '/?'.$re_url;
}

// 해당스킨의 정보를 불러온다
/*
	SkinInfo(출력 배열중 특정 배열을 지정하여 호출한다.(전체는 all), 스킨폴더 내부의 어떤폴더에서 불러올지 지정, 스킨을 직접지정(자동은 auto))
*/
function SkinInfo($view='all', $type='site', $skin='auto') {
	global $siteInfo;
	if($type == 'auto') {
		//$type = (is_mobile()?'site_m':'site'); // 디바이스별 스킨 호출
		$type = 'site';
	}

	if($_COOKIE['temp_skin'] != '' && $skin == 'cookie') {
		$skin = $_COOKIE['temp_skin'];
	}
	else {
		//if($type == 'site') $skin = $siteInfo['s_skin'];
		//else if($type == 'site_m') $skin = $siteInfo['s_skin_m'];
		$skin = $siteInfo['s_skin'];
	}

	if(!file_exists(OD_SKIN_ROOT.'/'.$type.'/'.$skin.'/skin.xml')) return array('정보파일이 없습니다.');
	$SkinInfo = array();
	$SkinInfo = xml2array(file_get_contents(OD_SKIN_ROOT.'/'.$type.'/'.$skin.'/skin.xml'));
	if(!$SkinInfo['skin']['title']) $SkinInfo['skin']['title'] = $skin;
	if(isset($SkinInfo['skin']['info'])) {
		$Info = explode(PHP_EOL, trim($SkinInfo['skin']['info']));
		$SkinInfo['skin']['info'] = array();
		if(count($Info) <= 0) $Info = array();
		foreach($Info as $kk=>$vv) {
			$vv = trim($vv);
			if(!$vv) continue;
			$SkinInfo['skin']['info'][] = trim($vv);
		}
	}
	if($view != 'all') return (count($SkinInfo['skin'][$view] > 0)?$SkinInfo['skin'][$view]:array('지정배열이 없습니다.'));
	return (count($SkinInfo['skin'] > 0)?$SkinInfo['skin']:array());
}




// ----- 브랜드 - 초성 추출을 위한 함수 -----
//			사용법 :  cutstr_new(linear_hangul('안녕하세요'),1,'') ==> ㅇ 추출
function linear_utf8_strlen($str) { return mb_strlen($str, 'UTF-8'); }
function linear_utf8_charAt($str, $num) { return mb_substr($str, $num, 1, 'UTF-8'); }
function linear_utf8_ord($ch) {
  $len = strlen($ch);
  if($len <= 0) return false;
  $h = ord($ch{0});
  if ($h <= 0x7F) return $h;
  if ($h < 0xC2) return false;
  if ($h <= 0xDF && $len>1) return ($h & 0x1F) <<  6 | (ord($ch{1}) & 0x3F);
  if ($h <= 0xEF && $len>2) return ($h & 0x0F) << 12 | (ord($ch{1}) & 0x3F) << 6 | (ord($ch{2}) & 0x3F);
  if ($h <= 0xF4 && $len>3) return ($h & 0x0F) << 18 | (ord($ch{1}) & 0x3F) << 12 | (ord($ch{2}) & 0x3F) << 6 | (ord($ch{3}) & 0x3F);
  return false;
}
function linear_hangul($str) {
  $cho = array("ㄱ","ㄲ","ㄴ","ㄷ","ㄸ","ㄹ","ㅁ","ㅂ","ㅃ","ㅅ","ㅆ","ㅇ","ㅈ","ㅉ","ㅊ","ㅋ","ㅌ","ㅍ","ㅎ");
  $jung = array("ㅏ","ㅐ","ㅑ","ㅒ","ㅓ","ㅔ","ㅕ","ㅖ","ㅗ","ㅘ","ㅙ","ㅚ","ㅛ","ㅜ","ㅝ","ㅞ","ㅟ","ㅠ","ㅡ","ㅢ","ㅣ");
  $jong = array("","ㄱ","ㄲ","ㄳ","ㄴ","ㄵ","ㄶ","ㄷ","ㄹ","ㄺ","ㄻ","ㄼ","ㄽ","ㄾ","ㄿ","ㅀ","ㅁ","ㅂ","ㅄ","ㅅ","ㅆ","ㅇ","ㅈ","ㅊ","ㅋ"," ㅌ","ㅍ","ㅎ");
  $result = "";
  for ($i=0; $i<linear_utf8_strlen($str); $i++) {
	$code = linear_utf8_ord(linear_utf8_charAt($str, $i)) - 44032;
	if ($code > -1 && $code < 11172) {
	  $cho_idx = $code / 588;
	  $jung_idx = $code % 588 / 28;
	  $jong_idx = $code % 28;
	  $result .= $cho[$cho_idx].$jung[$jung_idx].$jong[$jong_idx];
	} else {
	   $result .= linear_utf8_charAt($str, $i);
	}
  }
  return $result;
}
// ----- 브랜드 - 초성 추출을 위한 함수 -----

// -- bin2hex 함수로 변경된 hex 코드를 다시 디코드하는 함수
// -- 기본적으로 php5.4? 이상에서는 지원이되나 낮은버전은 미지원으로 추가
if ( !function_exists( 'hex2bin' ) ) {
    function hex2bin( $str ) {
        $sbin = "";
        $len = strlen( $str );
        for ( $i = 0; $i < $len; $i += 2 ) {
            $sbin .= pack( "H*", substr( $str, $i, 2 ) );
        }

        return $sbin;
    }
}



	// 옵션 컬러형일 경우 함수 - _product_option1~3.ajax.php에서 사용
	//			pouid - 옵션고유번호
	//			color_type - 옵션 컬러 타입 : color , img
	//			color_name - color일 경우 color picker 값 , img 일 경우 이미지명
	function fn_option_color($pouid , $color_type , $color_name){
		echo '

			<div style="float:right" class="option_color">' . _InputRadio('po_info[' . $pouid . '][po_color_type]', array('color', 'img'), ( $color_type ? $color_type :'color'), ' class="color_type" data-pouid="' . $pouid . '" ', array('색상', '이미지')) . '</div>

			<div class="dash_line"><!-- 점선라인 --></div>

			<!-- // 1) 컬러 선택 -->
			<div class="right_box color" data-pouid="' . $pouid . '">
				<input type="text" name="po_info[' . $pouid . '][po_color_name_c]" class="design js_colorpic" value="'. ( $color_type == 'color' ? $color_name : '#000000') .'" style="width:70px" />
			</div>

			<!-- // 2) 이미지 선택 -->
			<div class="right_box img" data-pouid="' . $pouid . '">
				' . _PhotoForm( '../upfiles/option', 'po_info_img_' . $pouid , ($color_type == 'img' ? $color_name : '') , ' style="width:150px" ') . '
			</div>

		';
	}




	// 자주쓰는 옵션 컬러형일 경우 함수 - _product_option1~3.ajax.php에서 사용
	//			couid - 자주옵션고유번호
	//			color_type - 옵션 컬러 타입 : color , img
	//			color_name - color일 경우 color picker 값 , img 일 경우 이미지명
	function fn_common_option_color($couid , $color_type , $color_name){
		echo '

			<div style="float:right">' . _InputRadio('co_info[' . $couid . '][co_color_type]', array('color', 'img'), ( $color_type ? $color_type :'color'), ' class="color_type" data-couid="' . $couid . '" ', array('색상', '이미지')) . '</div>

			<div class="dash_line"><!-- 점선라인 --></div>

			<!-- // 1) 컬러 선택 -->
			<div class="right_box color" data-couid="' . $couid . '">
				<input type="text" name="co_info[' . $couid . '][co_color_name_c]" class="design js_colorpic" value="'. ( $color_type == 'color' ? $color_name : '') .'" style="width:70px" />
			</div>

			<!-- // 2) 이미지 선택 -->
			<div class="right_box img" data-couid="' . $couid . '">
				' . _PhotoForm( '../upfiles/option', 'co_info_img_' . $couid , ($color_type == 'img' ? $color_name : '') , ' style="width:150px" ') . '
			</div>

		';
	}


	/*LCY::COUPON
		// 쿠폰할인혜택에 대한 정보를 보기 좋게 출력해준다.
		// 회원에게 발급된 쿠폰정보는 smart_individual_coupon 테이블에 coup_ocsinfo 에 serialize 화된것을 unserialize 화 해서 넘겨주면된다.
		// 쿠폰테이블에서 뽑을경우 smart_individual_coupon 정보를 뽑아서 해당 데이터 배열을 넘겨주면된다. $res 에서는 $value에 해당되는 배열값을 넘겨준다.
	*/
	function printCouponSetBoon($couponSetData)
	{
		if( is_array($couponSetData) == false || count($couponSetData) < 1){ return '-'; }

		// 할인혜택
		if( $couponSetData['ocs_boon_type'] == 'discount'){ // 할인
			if( $couponSetData['ocs_dtype'] == 'per'){ // 할인율일경우
				$printBoonType = "구매시 ".$couponSetData['ocs_per']."% 할인";
				if($couponSetData['ocs_price_max_use'] == 'Y') $printBoonType .=", 최대 ".number_format($couponSetData['ocs_price_max']).'원 까지 할인';
			}else{
				$printBoonType = number_format($couponSetData['ocs_price'])."원 할인";
			}
		}else if( $couponSetData['ocs_boon_type'] == 'save'){ // 적립

			if( $couponSetData['ocs_dtype'] == 'per'){ // 할인율일경우
				$printBoonType = "구매시 ".$couponSetData['ocs_per']."% 적립";
				if($couponSetData['ocs_price_max_use'] == 'Y') $printBoonType .=", 최대 ".number_format($couponSetData['ocs_price_max']).'원 까지 적립';
			}else{
				$printBoonType = number_format($couponSetData['ocs_price'])."원 적립";
			}
		}else if( $couponSetData['ocs_boon_type'] == 'delivery'){ // 배송비 할인
			$printBoonType = "배송비 ".number_format($couponSetData['ocs_price'])."원 할인";
		}

		return $printBoonType;
	}

// number_format 에 대한 추가처리, 소수점에 대한 추가처리
function odt_number_format($value,$option=0)
{
	if($option == 0){ return number_format($value); }
	$prefix = ".";
	for($i=0; $i < $option; $i++){
		$prefix .="0";
	}
	$result = str_replace($prefix, "", (string)number_format($value, $option));
	return $result;
}

// LDD: 2018-08-03 파일이 이미지파일인지 확인 한다.
function is_image_file($file_name='') {
	global $arrUpfileConfig;
	if(!$file_name) return false;
	$image_ext = $arrUpfileConfig['ext']['images']; // $arrUpfileConfig -> /inclue/var.php
	if(count($image_ext) <= 0) $image_ext = array('png','jpg','jpeg','gif');
	return preg_match('`'.implode('|', $image_ext).'`i', $file_name);
}

// LDD: 2018-11-29 할인률 계산 <-- 필요해서 뷰콜에서 가져와서 추가 LCY 2018-12-27
function DCPer($old=0, $new=0) {
	if($old <= 0) return 0;
	return round(($old-$new)*100/$old);
}

// 2019-03-05 SSJ :: 네이버 에디터 동영상 사이즈 제어를 위해 iframe 태그가 있으면 div.iframe_wrap 으로 감싸기
function wrap_tag_iframe($str){
	// addslashes 체크
	$is_slashes = strpos($str , "\\\"") !== false || strpos($str , "\\\'") !== false;
	if($is_slashes){
		$str = stripslashes($str);
	}

	// 엔터키 입력 시
	$str = str_replace(array("<div class=\"iframe_wrap\"><br></div>"), array("<p><br></p>"), $str);

	// .iframe_wrap으로 감싸져있는 iframe 추출하여 따로 저장
	$match = array();
	preg_match_all("/<div class=\"iframe_wrap\">(.*?)<\/div>/is",$str, $match);
	$except = array();
	if(count($match[0]) > 0){
		$arr = array_filter(array_unique($match[0]));
		foreach($arr as $k=>$v){
			$except['{iframe'.$k.'}'] = $v;
		}
		$str = str_replace(array_values($except), array_keys($except), $str);
	}

	// .iframe_wrap으로 감싸져 있지 않는 iframe 추출
	$match = array();
	preg_match_all("/<iframe(.*?)<\/iframe>/is",$str, $match);
	if(count($match[0]) > 0){
		$arr = array_filter(array_unique($match[0]));
		foreach($arr as $k=>$v){
			$str = str_replace(array($v), array('<div class="iframe_wrap">'.$v.'</div>'), $str);
		}
		//ViewArr($arr);
	}

	// .iframe_wrap으로 감싸져있던 iframe 복구
	if(count($except) > 0){
		$str = str_replace(array_keys($except), array_values($except), $str);
	}

	if($is_slashes){
		$str = addslashes($str);
	}
	return $str;
}

// 평점 -> 별로 변환 추가 kms 2019-09-11
function eval_point_change_star( $eval_point ) {
	// 평점 -> 별로 변환
	$tmp_pt_str = "<strong>★</strong>";
	$eval_str ="";
	if ( $eval_point > 0  ) {
		$tmp_pt_cnt = $eval_point / 20;
		for ( $i=1; $i <= 5; $i++ ) {
			if ( $i > $tmp_pt_cnt ) {
				$eval_str .= "<em>★</em>";
			}else{
				$eval_str .= $tmp_pt_str;
			}
		}
	}
	return $eval_str;
}

// JJC : 2019-09-03 : 실제 아이피 판별
if( function_exists('getRealIpAddr') == false){
	function getRealIpAddr(){
		if(!empty($_SERVER['HTTP_CLIENT_IP']) && getenv('HTTP_CLIENT_IP')){
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && getenv('HTTP_X_FORWARDED_FOR')){
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		elseif(!empty($_SERVER['REMOTE_HOST']) && getenv('REMOTE_HOST')){
			return $_SERVER['REMOTE_HOST'];
		}
		elseif(!empty($_SERVER['REMOTE_ADDR']) && getenv('REMOTE_ADDR')){
			return $_SERVER['REMOTE_ADDR'];
		}
		return false;
	}
}

// 2019-12-04 SSJ :: 이미지 alt 속성 넣기
// -- alt속성이 없으면 상품명을 alt에 넣기
function set_img_alter($str, $alt=""){
	// addslashes 체크
	$is_slashes = strpos($str , "\\\"") !== false || strpos($str , "\\\'") !== false;
	if($is_slashes) $str = stripslashes($str);

	// 치환자
	$replace = array();

	// img테그 추출
	$img = array();
	$match = array();
	preg_match_all('/<img[^>]+>/i',$str, $match);
	if(count($match[0]) > 0){
		foreach( $match[0] as $k=>$v) preg_match_all('/(src|title|alt)=("[^"]*")/i',$v, $img[$v]);

		foreach($img as $k=>$v){
			$key = array_search('alt', $v[1]);
			$val = str_replace("\"","",$v[2][$key]);

			// alt속성이 빈값이면 alt속성 제거하여 추가되도록 한다
			if($key != false && $val == ""){
				unset($v[0][$key], $v[1][$key], $v[2][$key]);
				$v = array_filter($v);
				$key = false;
			}

			// alt 속성이 없으면
			if($key === false){
				// 이미지 테그 작성
				$img_tag = "<img";
				if(count($v[0]) > 0) foreach($v[0] as $sk=>$sv) $img_tag .= " ".$sv;
				// alt추가
				$img_tag .= " alt=\"". htmlspecialchars($alt) ."\"";
				$img_tag .= ">";
				// 치환자 저장
				$replace[$k] = $img_tag;
			}
		}
	}

	// 이미지 테그 교체
	if(count($replace) > 0) $str = str_replace(array_keys($replace), array_values($replace), $str);
	// addslashes
	if($is_slashes) $str = addslashes($str);
	return $str;
}

// -- 2020-07-13 SSJ :: 웹 취약점 보완 패치 XSS filter 함수 ----
if( function_exists('RemoveXSS') == false){
	function RemoveXSS($val) {

		// remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
		// this prevents some character re-spacing such as <java\0script>
		// note that you have to handle splits with \n, \r, and \t later since they *are*
		// allowed in some inputs
		$val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);

		// straight replacements, the user should never need these since they're normal characters
		// this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&
		// #X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>

		$search = 'abcdefghijklmnopqrstuvwxyz';
		$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$search .= '1234567890!@#$%^&*()';
		$search .= '~`";:?+/={}[]-_|\'\\';
		for ($i = 0; $i < strlen($search); $i++) {
			// ;? matches the ;, which is optional
			// 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

			// &#x0040 @ search for the hex values
			$val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val);
			// with a ;

			// &#00064 @ 0{0,7} matches '0' zero to seven times
			$val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
		}

		// now the only remaining whitespace attacks are \t, \n, and \r
		$ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'base');
		$ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
		$ra = array_merge($ra1, $ra2);

		$found = true; // keep replacing as long as the previous round replaced something
		while ($found == true) {
			$val_before = $val;
			for ($i = 0; $i < sizeof($ra); $i++) {
				$pattern = '/';
				for ($j = 0; $j < strlen($ra[$i]); $j++) {
					if ($j > 0) {
						$pattern .= '(';
						$pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
						$pattern .= '|(&#0{0,8}([9][10][13]);?)?';
						$pattern .= ')?';
					}
					$pattern .= $ra[$i][$j];
				 }
				 $pattern .= '/i';
				$replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
				$val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
				if ($val_before == $val) {
					// no replacements were made, so exit the loop
					$found = false;
				}
			}
		}
		return $val;
	}
}
// -- 2020-07-13 SSJ :: 웹 취약점 보완 패치 XSS filter 함수 ----


// KAY :: 에디터 이미지 관리 :: 2021-06-07 
//    파라미터
//      $ei_tablename : 에디터 이미지 대상 테이블명 
//      $ei_uid : 에디터 이미지 대상 고유번호
//    리턴
//      name : 이미지명
//      link : 에디터 이미지 수정 시 바로가기를 위한 링크 ( 바로가기를 위해 사용 )
//      table : 에디터 이미지가 사용되어있는 DB테이블

function editor_img_info($ei_tablename , $ei_uid){
  // 전체 관리 list 에서 name은 테이블명을 영어->한글 ,link는 바로가기 링크  
  switch($ei_tablename){
    // 상품
    case "product": 
      $ei_info = array('name' => '상품', 'link' => ($ei_uid ? '_product.form.php?_mode=modify&_code=' . $ei_uid : ''));
      break;

    // 게시판 - 게시글 관리
    case "board":
      $ei_info = array('name' => '게시글', 'link' => ($ei_uid ? '_bbs.post_mng.form.php?_mode=modify&_uid=' . $ei_uid : '') );
      break;

    // 게시판 - 게시글 양식 관리
    case "board_template":  
      $ei_info = array('name' => '게시글양식', 'link' => ($ei_uid ? '_bbs.post_template.form.php?_mode=modify&_uid=' . $ei_uid : ''));
      break;

    // 게시판 - FAQ 관리
    case "board_faq": 
      $ei_info = array('name' => 'FAQ', 'link' => ($ei_uid ? '_bbs.post_faq.form.php?_mode=modify&_uid=' . $ei_uid : ''));
      break;

    // 디자인 - 팝업 관리
    case "popup": 
      $ei_info = array('name' => '팝업', 'link' => ($ei_uid ? '_popup.form.php?_mode=modify&_uid=' . $ei_uid : ''));
      break;

    // 디자인 - 일반페이지 관리 
    case "normal":  
      $ei_info = array('name' => '일반페이지', 'link' => ($ei_uid ? '_normalpage.form.php?_mode=modify&_uid=' . $ei_uid : ''));
      break;
    
    // 프로모션- 기획전 관리
    case "promotion": 
      $ei_info = array('name' => '프로모션', 'link' => ($ei_uid ? '_promotion_plan.form.php?_mode=modify&uid=' . $ei_uid : ''));
      break;
    
    // 회원관리 - 메일관리 - 메일링
    case "mailing": 
      $ei_info = array('name' => '메일링', 'link' => ($ei_uid ? '_mailing_data.form.php?_mode=modify&_uid=' . $ei_uid : ''));
      break;

    // 환경설정 - 상품상세이용안내
    case "setting": 
      $ei_info = array('name' => '환경설정', 'link' => ($ei_uid ? '_product.guide.form.php?_mode=modify&_uid=' . $ei_uid : ''));
      break;
  }
  return $ei_info;
}

// KAY :: 에디터 이미지 관리 :: 2021-06-16
// 에디터 이미지 처리 전용 함수
//    파라미터
//      $photoLOC : 이미지파일 등록 디렉토리
//      $photoVAR : 이미지 변수(변경이미지)
//      $photoOLD : 기존 이미지명
//    리턴
//      name : 이미지명
//      db_pro : DB 처리 형태 - none:미작동, update:수정
function _PhotoProEditorImg( $photoLOC , $photoVAR , $photoOLD ) {

  global $_FILES ;
  $_db_pro = 'none'; // DB 처리 형태 - none:미작동, update:수정

  if($_FILES[$photoVAR]['error'] > 0 && $_FILES[$photoVAR]['tmp_name'] ){
    if(strtolower($popup)=='alt'){
      switch($_FILES[$photoVAR]['error']){
        case "1":error_alt("업로드한 파일 크기가 2Mb 이상입니다."); break;//업로드한 파일 크기가 PHP 'upload_max_filesize' 설정값보다 클 경우
        case "2":error_alt("업로드한 파일 크기가 2Mb 이상입니다."); break;// UPLOAD_ERR_FORM_SIZE, 업로드한 파일 크기가 HTML 폼에 명시한 MAX_FILE_SIZE 값보다 클 경우
        case "3":error_alt("파일 전송에 오류가 있습니다."); break;//파일중 일부만 전송된 경우
        case "4":error_alt("파일이 전송되지 않습니다."); break;//파일도 전송되지 않았을 경우
      }
    }else{
      switch($_FILES[$photoVAR]['error']){
        case "1":error_alt("업로드한 파일 크기가 2Mb 이상입니다."); break;//업로드한 파일 크기가 PHP 'upload_max_filesize' 설정값보다 클 경우
        case "2":error_alt("업로드한 파일 크기가 2Mb 이상입니다."); break;// UPLOAD_ERR_FORM_SIZE, 업로드한 파일 크기가 HTML 폼에 명시한 MAX_FILE_SIZE 값보다 클 경우
        case "3":error_alt("파일 전송에 오류가 있습니다."); break;//파일중 일부만 전송된 경우
        case "4":error_alt("파일이 전송되지 않습니다."); break;//파일도 전송되지 않았을 경우
      }
    }
  }

  // 파일이 있을 경우 실행
  if( $_FILES[$photoVAR]['size']> 0 ){

    // 파일 명을 explode로 구분하여 확장자, 확장자가 없는 이미지명으로 분리
    $ex_image_name = explode(".",$_FILES[$photoVAR]['name']);

    // 확장자
    $app_ext = strtolower($ex_image_name[(sizeof($ex_image_name)-1)]); 

    // 정해놓은 확장자가 아닐 경우 실행 x
    if( !preg_match("/gif|jpg|jpeg|bmp|png/i" , $app_ext) ) {
      if(strtolower($popup)=='alt') error_alt("등록가능한 이미지가 아닙니다.");
      else error_alt("등록가능한 이미지가 아닙니다.");
    }

    // 기존이미지 O , 변경이미지 O --> 기존이미지 파일 삭제 한 뒤 변경이미지로 변경 후 파일명 그대로 return
    if($photoOLD ){

      // 확장자가 다를 경우 오류처리
      $ex_photoOLD = explode(".",$photoOLD);
      $photoOLD_ext = strtolower($ex_photoOLD[(sizeof($ex_photoOLD)-1)]); // 확장자
      if($photoOLD_ext != $app_ext)  {error_alt("등록하신 이미지의 확장자가 달라 수정 할 수 없습니다.");}

      $photoOLD = iconv("UTF-8","EUC-KR",$photoOLD);  //  한글명 파일을 읽기위한 변환

      @unlink( $photoLOC . $photoOLD );// 기존이미지 삭제

      // 신규이미지 카피 (기존이미지명 그대로)
      $img_name = $photoOLD;
    }

    // 기존이미지 X , 변경이미지 O --> 변경이미지 추가 후 return
    else if( !$photoOLD ){

      // 신규이미지 카피 (신규이미지명 적용)
      $img_name = sprintf("%u" , crc32($_FILES[$photoVAR]['name'] . time() . rand())) . "." . $app_ext ;
      $_db_pro = 'update'; // DB 처리 형태 - none:미작동, update:수정
    }
    @copy($_FILES[$photoVAR]['tmp_name'] , $photoLOC . $img_name);
  }

  // 기존이미지 O , 변경이미지 X --> 기존이미지 파일명 그대로 return
  else if($photoOLD && $_FILES[$photoVAR]['size'] == 0 ){
    // 기존이미지명 그대로
    $img_name = $photoOLD;
  }

  return array( 'name' => $img_name , 'db_pro' => $_db_pro) ;
}

/*
	apache_request_headers 서포트 
	@ comment
		Authorization 사용하기 위해선 .htaccess 에 추가 인증선언 필요
		ex) SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1
*/
if( !function_exists('apache_request_headers')){

	function apache_request_headers(){
		$headers = array();
		foreach ($_SERVER as $k => $v)
		{
			$keyPrefix = str_replace('REDIRECT_','',$k);
			if (substr($keyPrefix, 0, 5) == "HTTP_" || substr($keyPrefix, 0, 5) == "HTTP_")
			{
				$keyPrefix = str_replace('_', ' ', substr($keyPrefix, 5));
				$keyPrefix = str_replace(' ', '-', ucwords(strtolower($keyPrefix)));
				$headers[$keyPrefix] = $v;
			}
		}
		return $headers;
	}
}

// LCY : 2022-01-10 : SNS 고유 식별자 패치 -- 숫자로 구성된 고유 아이디 생성 함수
 if( !function_exists('creat_num_uniqueID') ) {
    // - 접두사_{숫자} : 생성해준다.
    function creat_num_uniqueID($prefix="",$length = 12){

        $arrNums = array();
        for($i=0;$i<$length;$i++){ $arrNums[] = mt_rand(0,9); }
        $randNum = implode("",$arrNums);
        $id = $prefix.$randNum;

        // 중복체크
        $idChk = _MQ("select count(*) as cnt from smart_individual where in_id = '".$id."' ");
        if( $idChk['cnt'] > 0 ){
            $id = creat_num_uniqueID();
        }
        return $id ;
    }
}

if( !function_exists('get_selected') ) {
    function get_selected($field, $value)
    {
        if( is_int($value) ){
            return ((int) $field===$value) ? ' selected="selected"' : '';
        }

        return ($field===$value) ? ' selected="selected"' : '';
    }
}

// 변수 또는 배열의 이름과 값을 얻어냄. print_r() 함수의 변형
if( !function_exists('print_r2') ) {
    function print_r2($var)
    {
        ob_start();
        print_r($var);
        $str = ob_get_contents();
        ob_end_clean();
        $str = preg_replace("/ /", "&nbsp;", $str);
        echo nl2br("<span style='font-family:Tahoma, 굴림; font-size:9pt;'>$str</span>");
    }
}
