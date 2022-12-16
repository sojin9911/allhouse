<?PHP
/*
※ 대상자리스트를 리턴해 주는 url
 - URL
   ex) http://"+domain+"/pages/append.amail.php?mode=groupftp&user_id="+user_id+"group_code="+groupCode+"&post_id="+post_id

 - 웹 페이지 방식(아이디 값은 키 값 이여야 합니다.)
   위와 같이 URL Call을 하게 되면 대상자 리스트를 저희가 정한 포맷으로 리스팅 해주시면 될 것 같습니다.
   <tr><td>아이디</td><td>이름</td><td>이메일</td></tr>
   <tr><td>yongsub</td><td>변용섭</td><td>yongsub@amail.co.kr</td></tr>
   <tr><td>yongsub1</td><td>변용섭</td><td>yongsub@amail.co.kr</td></tr>
   <tr><td>yongsub2</td><td>변용섭</td><td>yongsub@amail.co.kr</td></tr>
   <tr><td>yongsub3</td><td>변용섭</td><td>yongsub@amail.co.kr</td></tr>

    접근 아이피 제한해야 함...
    개발서버 : 175.209.46.91
    실 서버 : 119.207.76.26
*/


	header("Content-Type:text/html;charset=utf-8");

	include_once dirname(__FILE__)."/../include/inc.php";

	## 에러메시지(멈춤)
	function error_stop($msg) {
		echo "<script>alert(\"$msg\");</script>";
		exit;
	}


    // 변수 설정
    foreach( $_POST as $k=>$v ){
        $k = $v;
    }


    // http://amailtest.onedaynet.co.kr/pages/append.amail.php?mode=groupftp&user_id=onedaynet&group_code=A&post_id=master
    // mode=groupftp&user_id="+user_id+"group_code="+groupCode+"&post_id="+post_id
    // mode : groupftp로 고정
    // user_id : 사이트의 아이디
    // group_code : 테스트(T - test) , 전체회원(A - all) , 메일링회원(M - mailling) , 구독회원(S - subscribe)
    // post_id : 에이메일에서 보내주는 키 값

    $setup_user_id = 'new_'.$siteInfo[s_mailid];

    // 사전설정
    if( !in_array($mode , array("groupftp","grouplist" , "groupchoice"))) {
        error_stop("잘못된 접근입니다.1");
    }

    if( $user_id <> $setup_user_id ) {
        error_stop("잘못된 접근입니다.2 $user_id - $setup_user_id");
    }


    if( !in_Array($_SERVER["REMOTE_ADDR"] , array("175.209.46.91" , "119.207.76.26" , "175.209.46.100", "182.210.7.13")) ) {//지정 아이피 확인
        error_stop("지정한 아이피가 아닙니다. $ip");
    }


//    if( !($group_code && $mode) ) {
//        error_stop("요청 parameter가 부족합니다.");
//    }


/*
    // 사용권한이 있는 post_id 인지 확인
	$location = "/odprogram/odmanager/odmembers/od_mail.php";
    $que  = " SELECT * FROM m_adm_menu WHERE m2_vkbn = 'y' AND m2_link ='${location}' ";
    $res = mysql_query($que);
    if( mysql_num_rows($res) > 0 ) {
        $r = mysql_fetch_assoc($res);
        $sque  = " SELECT m15_vkbn FROM m_menu_set WHERE m15_id = '". $post_id ."' AND m15_code1 = '".$r[m2_code1]."' AND m15_code2 = '".$r[m2_code2]."'   ";
        $sres = mysql_query($sque);
        if( mysql_result($sres,0,0) <> "Y" ) {
            error_stop("권한이 없습니다.");
        }
    }
*/


    switch( $group_code ){
        case "T": // 테스트
            $que = "SELECT '".$siteInfo[s_adid]."' as id, '".$siteInfo[s_ademail]."' as email, '관리자' as name";
            $que2 = "SELECT 1 as cnt ";
            break;
        case "all": // 전체회원
            $que = "SELECT in_id as id, in_email as email, in_name as name FROM smart_individual WHERE   in_out='N' and in_email !=''";
            $que2 = "SELECT count(*) as cnt FROM smart_individual WHERE  in_out='N' and in_email !=''";
            break;
        case "M": // 메일링회원
            $que = "SELECT in_id as id, in_email as email, in_name as name FROM smart_individual WHERE   in_out='N' and in_email !='' AND in_emailsend = 'Y'";
            $que2 = "SELECT count(*) as cnt FROM smart_individual WHERE  in_out='N' and in_email !='' AND in_emailsend = 'Y'";
            break;
        case "S": // 구독회원
            $que = "SELECT f_email as id , f_email as email , f_email as name FROM smart_feed WHERE f_email !='' and f_emailsend = 'Y'";
            $que2 = "SELECT count(*) as cnt FROM smart_feed WHERE f_email !='' and f_emailsend = 'Y'";
            break;
    }


    $str = "";
    switch( $mode ){
        case "groupftp": // 그룹FTP 모드
            $res = mysql_query($que);
            while($r = mysql_fetch_assoc($res)) {
                if(!$r[id] || !$r[name] || !$r[email]) continue;
                $str .= "<tr><td>$r[id]</td><td>$r[name]</td><td>$r[email]</td></tr>\n";
            }
            break;
        case "grouplist": // 그룹LIST 모드
            $str .= "<tr><td>T</td><td>테스트</td></tr>\n";
            $str .= "<tr><td>M</td><td>메일링회원</td></tr>\n";
            $str .= "<tr><td>S</td><td>구독회원</td></tr>";
            break;
        case "groupchoice": // 그룹CHOICE 모드
            if( $group_code ) {
                $res = mysql_query($que2);
                $str .= mysql_result($res,0,0);
            }
            else {
                $str .= 0;
            }
            break;
    }


    echo $str;

?>