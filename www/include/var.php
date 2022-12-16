<?php
$SubAdminMode = true;

// 바로빌 연동 CERTKEY
$tax_barobill_certkery_service = "93E19D7C-47E6-421F-B68E-B8BB0AAC46F9"; // 서비스 certkey
$tax_barobill_certkery_test = "8FE6E4DB-E408-4899-8A5E-57DB781245FA"; // 테스트 certkey

// - 현금영수증 결제수단 코드 ---
$ocs_type = array(
	'SC0010' => 'card', // LG U+
	'SC0030' => 'iche', // LG U+
	'SC0040' => 'virtual', // LG U+
	'SC0100' => 'online' // LG U+
);

// - 결제타입 --- 'payco'=>'PAYCO 간편결제' "P"=>"PAYCO 간편결제" // /* LCY : 2021-06-15 : 페이코 제거 */
$arr_payment_type = array('card'=>'카드결제' , 'iche'=>'계좌이체' , 'virtual'=>'가상계좌', 'point'=>'전액적립금결제', 'online'=>'무통장입금','hpp'=>'휴대폰결제');
$arr_paymethod_name = array("C" => "카드결제", "L" => "계좌이체", "B" => "무통장입금", "G" => "전액적립금결제", "V" => "가상계좌","H"=>"휴대폰결제");
// - 결제타입 ---

// LCY : 2021-07-04 : 신용카드 간편결제 추가 --  {
$arr_payment_type['easypay_kakaopay'] = '카카오페이';
$arr_paymethod_name['EK'] = '카카오페이';
$arr_payment_type['easypay_naverpay'] = '네이버페이 간편결제';
$arr_paymethod_name['EN'] = '네이버페이';
$arr_available_easypay_pg_list = array('easypay_kakaopay'=>$arr_payment_type['easypay_kakaopay'],'easypay_naverpay'=>$arr_payment_type['easypay_naverpay']); // 기본 제공
$arr_available_easypay_pg = array( 
    'inicis'=> $arr_available_easypay_pg_list
);
// LCY : 2021-07-04 : 신용카드 간편결제 추가 --  }

// {{{PG취소가능 결제수단}}} // SSJ : 주문/결제 통합 패치 : 2021-02-24
$arr_cancel_payment_type = array("card" , "iche" , "hpp" , "payco"); //  PG사와 연동하여 취소 가능한 결제수단 정의
$arr_cancel_part_payment_type = array("card" , "iche" , "payco"); //  PG사와 연동하여 취소 가능한 결제수단 정의 -- 부분취소(가상계좌제외)
$arr_refund_payment_type = array("online" , "virtual"); //  PG사와 연동하지 않고 환불계좌 받는 결제수단
$arr_cash_payment_type = array("online" , "virtual" , "iche"); // 현금영수증 발행가능 결제수단
$arr_order_payment_type = array("card" , "iche" , "hpp" , "point" , "payco"); // 주문목록 노출 시 접수완료시에만 노출되는 결제수단
// {{{PG취소가능 결제수단}}} // SSJ : 주문/결제 통합 패치 : 2021-02-24

// JJC : 간편결제 - 페이플 : 2021-06-05 -- 페이플 결제수단 추가 -- 
$arr_payment_type['payple'] = '페이플 간편결제';
$arr_paymethod_name['PP'] = '페이플 간편결제';
// JJC : 간편결제 - 페이플 : 2021-06-05 -- 페이플 결제수단 추가 -- 


// JJC : 간편결제 - 페이플 : 2021-06-05 -- 페이플 결제수단 추가 -- 
// {{{PG취소가능 결제수단}}} // SSJ : 주문/결제 통합 패치 : 2021-02-24
$arr_cancel_payment_type = array("card" , "iche" , "hpp" , "payco" , "payple"); //  PG사와 연동하여 취소 가능한 결제수단 정의
$arr_cancel_part_payment_type = array("card" , "iche" , "payco" , "payple"); //  PG사와 연동하여 취소 가능한 결제수단 정의 -- 부분취소(가상계좌제외)
$arr_refund_payment_type = array("online" , "virtual"); //  PG사와 연동하지 않고 환불계좌 받는 결제수단
$arr_cash_payment_type = array("online" , "virtual" , "iche"); // 현금영수증 발행가능 결제수단
$arr_order_payment_type = array("card" , "iche" , "hpp" , "point" , "payco" , "payple"); // 주문목록 노출 시 접수완료시에만 노출되는 결제수단
// {{{PG취소가능 결제수단}}} // SSJ : 주문/결제 통합 패치 : 2021-02-24
// JJC : 간편결제 - 페이플 : 2021-06-05 -- 페이플 결제수단 추가 -- 

// - 주문상태 ---
//$arr_order_status = array('결제대기' , '결제완료' , '배송준비' , '배송중' , '배송완료' , '주문취소');
$arr_order_status = array('접수대기' , '접수완료 ' , '구매발주' , '배송준비', '배송중' , '배송완료' , '주문취소');
// - 주문상태 ---

// - 진행중인 주문 상태 ---
//$arr_order_status_ordering = array('결제대기' , '접수완료' , '배송대기' , '배송준비' , '배송중' );
$arr_order_status_ordering = array('접수대기' , '접수완료' , '구매발주' , '배송준비' , '배송중' );
// - 진행중인 주문 상태 ---

// - 주문 컴플레인 상태 ---
$arr_order_complain = array('교환/반품신청' , '교환/반품완료' , '완료/부분취소요청(PG연동)' , '완료/부분취소요청(적립금 환불)' );
$arr_order_moneyback = array('환불요청' , '환불완료' );
// - 주문 컴플레인 상태 ---

// - 무통장결제 진행상태 ---
$arr_payonline_status = array('대기','확인','취소');
// - 무통장결제 진행상태 ---

// - 정산을 위한 주문상품 배송상태 ---
$arr_order_product_sendstatus = array('구매발주','배송준비','배송중','배송완료');// 상품배송상태
$arr_order_product_settlestatus = array('자격미달','신청가능','신청완료','지급완료');// 상품정산상태
$arr_order_settlement_status = array("none"=>"정산무관","ready"=>"정산대기","complete"=>"정산완료");
// - 정산을 위한 주문상품 배송상태 ---

// 사용자의 편의를 위해 사용자에게 보여지는 문구를 달리 표현한다.
$arr_massage_conv = array(
"접수대기"				=> "입금대기",
"접수완료"				=> "배송준비중",
"배송중"				=> "상품배송중",
"구매발주"				=> "배송준비중",
"배송준비"				=> "배송준비중",
"배송완료"				=> "배송완료",
"주문취소"				=> "주문취소",
"교환/반품신청"			=> "교환/반품중",
"교환/반품완료"			=> "교환/반품완료",
"완료/부분취소요청(PG연동)"	=> "교환/반품완료",
"완료/부분취소요청(적립금 환불)"	=> "교환/반품완료"
);

// 문자발송 멘트를 설정하기 위한 유형
$arr_sms_text_type = array(
	"join"			=>"회원가입시",
	"temp_password"	=>"임시비밀번호 발급",
	"order_online"	=>"무통장주문",
	"order_virtual"	=>"가상계좌주문",
	"order_pay"		=>"접수완료시",
	"online_pay"	=>"입금확인시",
	"delivery"		=>"상품배송시",
	"order_cancel"  =>"주문취소시",
	"cancel_part_request" =>"부분취소요청시", //KAY :: 2021-09-09 부분취소요청 추가
	"order_cancel_part" =>"부분취소완료시", //KAY :: 2021-09-09 부분취소-> 부분취소완료시로 수정
	"request"		=>"문의접수시",
    "product_review"        =>"상품후기등록시",
    "product_talk"      =>"상품문의접수시",
    "2year_opt"     =>"매2년수신동의",
);

// 결제 pg사
$arr_pg_type = array(
	"inicis"		=>"KG 이니시스",
	"kcp"			=>"NHN KCP",
	//"allthegate"	=>"올더게이트",
	"lgpay"			=>"토스페이먼츠",
	"billgate"		=>"빌게이트",
	"daupay"		=>"페이조아"
);

// 결제 pg사
$arr_pg_tax_name = array(
	"inicis"		=>"이니시스",
	"kcp"			=>"KCP",
	//"allthegate"	=>"올더게이트",
	"lgpay"			=>"토스페이먼츠",
	"billgate"		=>"빌게이트",
	"daupay"		=>"페이조아"
);

// - 배너위치 ---
$arr_banner_loc_common = $arr_banner_loc = array(
	'common,mailing,not_set_view,not_set_term,not_set_link_target' =>'[공통] 메일링 상단 로고 (가로 280 이하 x 세로 70 이하, 1개)',
);
// - 배너위치 ---

$arr_request_type = array(
	'inquiry' => '1:1문의',
	'partner' => '제휴문의',
	);


// - 정산유형 ---
$arr_commisstion_type = array(
	'supplyprice'=>'공급가',
	'persent'=>'수수료',
);
// - 상품분류 ---

// - 상품관련 문의 분류 ---
$arr_p_talk_type = array(
	'qna'=>'상품문의',
	'eval'=>'상품평가',
);
// - 상품분류 ---

//// - 쿠폰유형 분류 --- //=> $arrCouponSet 으로 변경
//$arr_coupon_type = array(
//"event"			=>"이벤트쿠폰",
//"express"		=>"무료배송쿠폰",
//"b_day"			=>"[자동발급] 생일쿠폰",
//"first_buy"		=>"[자동발급] 첫구매할인쿠폰",
//"new_member"	=>"[자동발급] 회원가입쿠폰"
//);
//// - 상품분류 ---

// ksnet 은행코드
$ksnet_bank = array (
	"01" => "한국은행", "02" => "산업은행", "03" => "기업은행", "04" => "국민은행", "05" => "외환은행", "06" => "주택은행", "07" => "수협은행", "08" => "수출입", "09" => "장기신용", "10" => "신농협중앙", "11" => "농협중앙", "12~15" => "농협회원", "16" => "축협중앙", "20" => "우리은행", "21" => "조흥은행", "22" => "상업은행", "23" => "제일은행", "24" => "한일은행", "25" => "서울은행", "26" => "신한은행", "27" => "한미은행", "28" => "동화은행", "29" => "동남은행", "30" => "대동은행", "31" => "대구은행", "32" => "부산은행", "33" => "충청은행", "34" => "광주은행", "35" => "제주은행", "36" => "경기은행", "37" => "전북은행", "38" => "강원은행", "39" => "경남은행", "40" => "충북은행", "53" => "씨티은행", "71" => "우체국", "76" => "신용보증", "81" => "하나은행", "82" => "보람은행", "83" => "평화은행", "93" => "새마을금고");

// - 택배사정보 ---
$arr_delivery_company = array(
	 "CJ대한통운택배"=>"https://www.doortodoor.co.kr/parcel/doortodoor.do?fsp_action=PARC_ACT_002&fsp_cmd=retrieveInvNoACT&invc_no="
	,"CJ GLS(HTH통합)"=>"https://www.doortodoor.co.kr/parcel/doortodoor.do?fsp_action=PARC_ACT_002&fsp_cmd=retrieveInvNoACT&invc_no="
	,"드림택배"=>"http://www.idreamlogis.com/delivery/delivery_result.jsp?item_no="
	,"우체국EMS"=>"http://service.epost.go.kr/trace.RetrieveEmsTrace.postal?ems_gubun=E&POST_CODE="
	,"우체국등기"=>"http://service.epost.go.kr/trace.RetrieveRegiPrclDeliv.postal?sid1="
	,"우체국택배"=>"https://service.epost.go.kr/trace.RetrieveDomRigiTraceList.comm?sid1="
	,"한진택배"=>"https://www.hanjin.co.kr/kor/CMS/DeliveryMgr/WaybillResult.do?mCode=MN038&schLang=KR&wblnumText2="
	,"롯데택배"=>"https://www.lotteglogis.com/home/reservation/tracking/linkView?InvNo="
	//,"로젠택배"=>"http://d2d.ilogen.com/d2d/delivery/invoice_tracesearch_quick.jsp?slipno="
	,"로젠택배"=>"https://www.ilogen.com/web/personal/trace/"
	,"KG로지스"=>"http://www.kglogis.co.kr/contents/waybill.jsp?item_no=" // 드림택배로 통합
	,"CVSnet"=>"http://www.doortodoor.co.kr/jsp/cmn/TrackingCVS.jsp?pTdNo="
	,"CU 편의점택배"=>"https://www.cupost.co.kr/postbox/delivery/localResult.cupost?invoice_no="
	,"KGB택배"=>"#"
	,"경동택배"=>"http://kdexp.com/basicNewDelivery.kd?barcode="
	,"대신택배"=>"https://www.ds3211.co.kr/freight/internalFreightSearch.ht?billno="
	,"일양택배"=>"http://www.ilyanglogis.com/functionality/tracking_result.asp?hawb_no="
	,"합동택배"=>"http://www.hdexp.co.kr/basic_delivery.hd?barcode="
	,"GTX로지스"=>"http://www.gtxlogis.co.kr/tracking/default.asp?awblno="
	,"건영택배"=>"http://www.kunyoung.com/goods/goods_01.php?mulno="
	,"천일택배"=>"http://www.chunil.co.kr/HTrace/HTrace.jsp?transNo="
	,"한의사랑택배"=>"http://www.hanips.com/html/sub03_03_1.html?logicnum="
	,"한덱스"=>"http://www.hanjin.co.kr/Logistics_html"
	,"DHL"=>"http://www.dhl.co.kr/content/kr/ko/express/tracking.shtml?brand=DHL&AWB="
	,"TNT Express"=>"http://www.tnt.com/webtracker/tracking.do?respCountry=kr&respLang=ko&searchType=CON&cons="
	,"UPS"=>"https://wwwapps.ups.com/WebTracking/track?track=yes&loc=ko_kr&trackNums="
	,"Fedex"=>"http://www.fedex.com/Tracking?ascend_header=1&clienttype=dotcomreg&cntry_code=kr&language=korean&tracknumbers="
	,"USPS"=>"https://tools.usps.com/go/TrackConfirmAction?tLabels="
	,"i-Parcel"=>"https://tracking.i-parcel.com/Home/Index?trackingnumber="
	,"DHL Global Mail"=>"http://webtrack.dhlglobalmail.com/?trackingnumber="
	,"범한판토스"=>"http://totprd.pantos.com/jsp/gsi/vm/popup/notLoginTrackingListExpressPoPup.jsp?quickType=HBL_NO&quickNo="
	,"AirBoyExpress"=>"http://www.airboyexpress.com/tracking/tracking.asp?shipping_number="
	,"GSMNtoN"=>"http://www.gsmnton.com/gsm/handler/Tracking-OrderList?searchType=TrackNo&trackNo="
	,"APEX(ECMS Express)"=>"http://www.apexglobe.com"
	,"KGL네트웍스"=>"http://www.hydex.net/ehydex/jsp/home/distribution/tracking/tracingView.jsp?InvNo="
	,"굿투럭"=>"http://www.goodstoluck.co.kr/#modal"
	,"호남택배"=>"http://honamlogis.co.kr"
	,"GSI Express"=>"http://www.gsiexpress.com/track_pop.php?track_type=ship_num&query_num="
	,"SLX로지스"=>"http://slx.co.kr/delivery/delivery_number.php?param1="
	,"ACI Express"=>"http://www.acieshop.com/pod.html?OrderNo="
	,"CGM 국제택배"=>"http://idn.inlos.com/CST/CST2/CST2044.aspx?Hawb="
	,"WIZWA"=>"http://www.wizwa.co.kr/tracking_exec.php?invoice_no="
	,"고려택배"=>"http://www.klogis.com/main.asp#"
	,"스피디익스프레스"=>"http://www.speedyexpress.net/tracking_view.php#"
	,"[자체배송]"=>"#"
	,"방문수령"=>"#"
	,"퀵서비스"=>"#"
);
// - 택배사정보 ---

$arr_o_status = array(
"접수대기" => "<span class='c_tag gray h22 t4'>접수대기</span>",
"접수완료" => "<span class='c_tag blue h22 t4'>접수완료</span>",
"배송중" => "<span class='c_tag green h22 t4'>배송중</span>",
"배송완료" => "<span class='c_tag darkgreen h22 t4'>배송완료</span>",
"주문취소" => "<span class='c_tag h22 black t4'>주문취소</span>",
"결제실패" => "<span class='c_tag h22 red t4'>결제실패</span>",
);

$arr_o_status_new = array(
	"접수대기" => "<span class='icon_state state_ready'>접수대기</span>",
	"접수완료" => "<span class='icon_state state_pay'>접수완료</span>",
	"배송중" => "<span class='icon_state state_deliver'>배송중</span>",
	"배송완료" => "<span class='icon_state state_ok'>배송완료</span>",
	"주문취소" => "<span class='icon_state state_cancel'>주문취소</span>",
	"결제실패" => "<span class='icon_state '>결제실패</span>",
);

$arr_o_status_mobile = array(
"접수대기" => "<span class='state state_ready'>접수대기</span>",
"결제확인" => "<span class='state state_pay'>결제확인</span>",
"접수완료" => "<span class='state state_pay'>접수완료</span>",
"배송중" => "<span class='state state_delivery'>배송중</span>",
"배송완료" => "<span class='state state_get'>배송완료</span>",
"주문취소" => "<span class='state state_cancel'>주문취소</span>",
"결제실패" => "<span class='state state_cancel'>결제실패</span>",
"발급완료" => "<span class='state state_get'>발급완료</span>",
);

$arr_adm_button = array(
"카드결제" => "<span class='c_tag violet h22 t5'>카드결제</span>",
"가상계좌" => "<span class='c_tag ygreen h22 t5'>가상계좌</span>",
"무통장입금" => "<span class='c_tag sky h22 t5'>무통장입금</span>",
"계좌이체" => "<span class='c_tag brown h22 t5'>계좌이체</span>",
"전액적립금결제" => "<span class='c_tag purple h22 t5'>적립금결제</span>",
"휴대폰결제" => "<span class='c_tag cyan h22 t5'>휴대폰결제</span>",
"페이코" => "<span class='c_tag red h22 t5'>페이코</span>",
"PAYCO 간편결제" => "<span class='c_tag red h22 t5'>페이코</span>", // SSJ : 주문/결제 통합 패치 : 2021-02-24 : 페이코 결제 아이콘 추가

"접수대기" => "<span class='c_tag gray h22 t4'>접수대기</span>",
"접수완료" => "<span class='c_tag blue h22 t4'>접수완료</span>",
"결제확인" => "<span class='c_tag violet h22 t4'>결제확인</span>",
"환불요청" => "<span class='c_tag aqua h22 t4'>환불요청</span>",

"입고대기" => "<span class='c_tag violet h22 t4'>입고대기</span>",

"현금영수증 요청" => "<span class='c_tag gray h22 t5'>현금영수증</span>",
"현금영수증 발행" => "<span class='c_tag blue h22 t5'>현금영수증</span>",

"구매발주" => "<span class='c_tag light h22 t4'>구매발주</span>",
"배송준비" => "<span class='c_tag ygreen h22 t4'>배송준비</span>",
"배송중" => "<span class='c_tag green h22 t4'>배송중</span>",
"배송완료" => "<span class='c_tag darkgreen h22 t4'>배송완료</span>",
"주문취소" => "<span class='c_tag black h22 t4'>주문취소</span>",
"결제실패" => "<span class='c_tag red h22 t4'>결제실패</span>",


"노출" => "<span class='c_tag h18 blue line'>노출</span>",
"숨김" => "<span class='c_tag h18 gray'>숨김</span>",
"비노출" => "<span class='c_tag h18 gray'>비노출</span>", // KAY :: 에디터 이미지 : 2021-07-20 :: 에디터 이미지 비노출 아이콘 추가

"수신가능" => "<span class='shop_state_pack'><span class='orange'>수신가능</span></span>",
"수신거부" => "<span class='shop_state_pack'><span class='gray'>수신거부</span></span>",


"적립완료" => "<span class='c_tag h18 blue t4'>적립완료</span>",
"적립예정" => "<span class='c_tag h18 gray t4'>적립예정</span>",
"적립취소" => "<span class='c_tag h18 black t4'>적립취소</span>",

"사용" => "<span class='c_tag h18 blue line t3'>사용</span>",
"중지" => "<span class='c_tag h18 gray t3'>중지</span>",
"미사용" => "<span class='c_tag h18 gray t3'>미사용</span>",
"사용대기" => "<span class='shop_state_pack'><span class='sky'>사용대기</span></span>",

"공지" => "<span class='c_tag h18 yellow'>공지</span>",

);

// LCY : 2021-07-04 : 신용카드 간편결제 추가
$arr_adm_button["E카카오페이"] = "<span class='c_tag h22 yellow line'>".$arr_payment_type['easypay_kakaopay']."</span>";
$arr_adm_button["E네이버페이"] = "<span class='c_tag h22 green line'>".$arr_payment_type['easypay_naverpay']."</span>";
// LCY : 2021-07-04 : 신용카드 간편결제 추가

// - 상품정보 기본항목 8가지  ---
$arr_reqinfo_keys = array("제품소개","색상","사이즈","제조사","A/S 책임자와 전화번호","제조국","취급시주의사항","품질보증기준");


# 네이버페이 배송조회 URL
$NPayCourier = array(
	'CJGLS'=>'https://www.doortodoor.co.kr/parcel/doortodoor.do?fsp_action=PARC_ACT_002&fsp_cmd=retrieveInvNoACT&invc_no=', // CJ대한통운
	'YELLOW'=>'http://www.kglogis.co.kr/contents/waybill.jsp?item_no=', // 옐로우캡
	//'KGB'=>'http://www.ilogen.com/iLOGEN.Web.New/TRACE/TraceView.aspx?gubun=slipno&slipno=', // 로젠택배
	'KGB'=>'https://www.ilogen.com/web/personal/trace/', // 로젠택배
	'DONGBU'=>'http://www.kglogis.co.kr/contents/waybill.jsp?item_no=', // 동부익스프레스택배
	'EPOST'=>'https://service.epost.go.kr/trace.RetrieveDomRigiTraceList.comm?sid1=', // 우체국택배
	'REGISTPOST'=>'http://service.epost.go.kr/trace.RetrieveRegiPrclDeliv.postal?sid1=', // 우편등기
	'HANJIN'=>'https://www.hanjin.co.kr/kor/CMS/DeliveryMgr/WaybillResult.do?mCode=MN038&schLang=KR&wblnumText2=', // 한진택배
	'HYUNDAI'=>'http://www.hydex.net/ehydex/jsp/home/distribution/tracking/tracingView.jsp?InvNo=', // 롯데(현대)택배
	'KGBLS'=>'http://www.kgbls.co.kr/tracing.asp?number=', // KGB택배
	'INNOGIS'=>'http://www.gtxlogis.co.kr/tracking/default.asp?awblno=', // GTX로지스
	'DAESIN'=>'https://www.ds3211.co.kr/freight/internalFreightSearch.ht?billno=', // 대신택배
	'ILYANG'=>'http://www.ilyanglogis.com/functionality/tracking_result.asp?hawb_no=', // 일양로지스
	'KDEXP'=>'http://www.kdexp.com/rerere.asp?p_item=', // 경동택배
	'CHUNIL'=>'http://www.cyber1001.co.kr/kor/taekbae/HTrace.jsp?transNo=', // 천일택배
	'CH1'=>'#', // 기타택배
	'HDEXP'=>'http://www.hdexp.co.kr/parcel/order_result_t.asp?stype=1&p_item=', // 합동택배
	'CVSNET'=>'http://www.cvsnet.co.kr/postbox/m_delivery/local/local.jsp?invoice_no=', // 편의점택배
	'DHL'=>'http://www.dhl.co.kr/ko/express/tracking.html?brand=DHL&AWB=', // DHL
	'FEDEX'=>'https://www.fedex.com/apps/fedextrack/?action=track&cntry_code=kr&trackingnumber=', // FEDEX
	'GSMNTON'=>'http://www.gsmnton.com/gsm/handler/Tracking-OrderList?searchType=TrackNo&trackNo=', // GSMNTON
	'WARPEX'=>'http://packing.warpex.com/api/tsTrack?wbl=', // WarpEx
	'WIZWA'=>'http://www.wizwa.co.kr/tracking_exec.php?invoice_no=', // WIZWA
	'EMS'=>'http://service.epost.go.kr/trace.RetrieveEmsTraceTibco.postal?ems_gubun=E&POST_CODE=', // EMS
	'DHLDE'=>'https://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=en&extendedSearch=true&idc=', // DHL(독일)
	'ACIEXPRESS'=>'http://www.acieshop.com/pod.html?OrderNo=', // ACI
	'EZUSA'=>'#', // EZUSA
	'PANTOS'=>'http://totprd.pantos.com/jsp/gsi/vm/popup/notLoginTrackingListExpressPoPup.jsp?quickType=HBL_NO&quickNo=', // 범한판토스
	'UPS'=>'https://wwwapps.ups.com/WebTracking/track?loc=ko_KR&tbifl=1&hiddenText=&track.x=조회&trackSelectedOption=&tracknum=', // UPS
	'KOREXG'=>'http://ex.korex.co.kr:7004/fis20/KIL_HttpCallExpTrackingInfo_Ctr.do?rqs_IO_CCD=O&vSNM=w&rqs_HAWB_NO=', // CJ대한통운(국제택배)
	'TNT'=>'https://www.tnt.com/express/ko_kr/site/home/applications/tracking.html?searchType=CON&cons=', // TNT
	'SWGEXP'=>'http://system.swgexp.com/common/tracking.asp?shipping_no=', // 성원글로벌
	'DAEWOON'=>'http://www.daewoonsys.com/common/tracking.asp?shipping_no=', // 대운글로벌
);



// 2017-07-12 ::: 보안서버 :::: 관리자 - 개인정보 이용 페이지 - 보안서버 ::: JJC
$arr_ssl_admin_page = array(
	// 회원관리
	'_individual.list.php' => '회원관리 - 목록',
	'_individual.form.php' => '회원관리 - 등록/수정',
	'_individual_sleep.list.php' => '휴면회원관리  - 목록',
	'_entershop.list.php' => '입점업체관리  - 목록',
	'_entershop.form.php' => '입점업체관리  - 등록/수정',
	// 주문관리
	'_order.list.php' => '주문관리  - 목록',
	'_order.form.php' => '주문관리  - 상세',
	'_npay_order.list.php' => '네이버페이 주문관리  - 목록',
	'_npay_order.form.php' => '네이버페이 주문관리  - 상세',
	'_order.cancel_list.php' => '주문취소관리  - 목록',
	'_order_delivery.list.php' => '배송주문관리  - 목록',
	'_order_product.list.php' => '배송주문상품관리  - 목록',
	'_order3.list.php' => '정산대기관리',
	'_order4.list.php' => '정산완료관리 - 목록',
	'_order4.view.php' => '정산완료관리 - 상세',
	'_cancel.list.php' => '부분취소요청관리',
	'_order_complain.list.php' => '교환/반품관리',
	'_cancel_order.list.php' => '환불요청관리',
	// 게시판관리
	'_bbs.post_mng.list.php' => '게시물 통합관리 - 목록',
	'_bbs.post_mng.form.php' => '게시물 통합관리 - 등록/수정',
	'_bbs.comment_mng.list.php' => '댓글 통합관리 - 목록',
	'_bbs.comment_mng.form.php' => '댓글 통합관리 - 등록/수정',
	// 일반관리
	'_request.list.php' => '문의관리 - 목록',
	'_request.form.php' => '문의관리 - 등록/수정',
);

// 2017-07-12 ::: 보안서버 :::: 관리자 - 보안서버 제외 페이지 ::: JJC
$arr_ssl_deny_admin_page = array(
	//'_config.sms.out_list.php' => 'SMS충전관리',
	//'_config.sms.out_send_list.php' => 'SMS발송내역',
	'_mailing_premium.view.php' => '프리미엄메일링',
);

// 2017-07-12 ::: 보안서버 :::: PC - 개인정보 이용 페이지 - 보안서버 ::: JJC
$arr_ssl_pc_page = array(
	'member.login.form' => '로그인',
	'member.find.form' => '아이디/비번찾기',
	'member.join.agree' => '회원가입 - 약관동의',
	'member.join.form' => '회원가입',
	'mypage.modify.form' => '정보수정',
	'board.form' => '게시판 작성폼',
	'board.view' => '게시판 상세페이지',
	'shop.order.form' => '주문서 작성품',
	'shop.order.result' => '주문서 확인페이지',
	'shop.order.complete' => '주문서 완료페이지',
	'shop.order.complete' => '주문서 완료페이지',
	'mypage.order.view' => '마이페이지 주문서 상세페이지',
	'service.guest.order.view' => '비회원 주문 조회 상세페이지',
);


// 2017-07-12 ::: 보안서버 :::: PC - 보안서버 제외 페이지 ::: JJC
$arr_ssl_deny_pc_page = array(
	//'main' => '메인'
);

// 2017-07-12 ::: 보안서버 :::: 모바일 - 개인정보 이용 페이지 - 보안서버 ::: JJC
$arr_ssl_m_page = array(
	'member.login.form' => '로그인',
	'member.find.form' => '아이디/비번찾기',
	'member.join.agree' => '회원가입 - 약관동의',
	'member.join.form' => '회원가입',
	'mypage.modify.form' => '정보수정',
	'board.form' => '게시판 작성폼',
	'board.view' => '게시판 상세페이지',
	'shop.order.form' => '주문서 작성품',
	'shop.order.result' => '주문서 확인페이지',
	'shop.order.complete' => '주문서 완료페이지',
	'shop.order.complete' => '주문서 완료페이지',
	'mypage.order.view' => '마이페이지 주문서 상세페이지',
	'service.guest.order.view' => '비회원 주문 조회 상세페이지',
);

// 2017-07-12 ::: 보안서버 :::: 모바일 - 보안서버 제외 페이지 ::: JJC
$arr_ssl_deny_m_page = array(
);

// -- 관리자 페이지중 접근이 공개인 페이지
$arrPublicAdminPage = array('_main.php','index.php');

// -- 관리자메뉴 숨김/파일명 수정 불가능한 파일명
$arrAdminMenuNoneModify = array('_config.admin_menu.list.php');

// -- 관리자메뉴 삭제 불가능한 파일명
$arrAdminMenuNoneDelete = array('_config.admin_menu.list.php','_config.admin.list.php');

// -- 매뉴얼 링크 키값 :: openMenualLink($key) 함수 참고 -- 2017-09-13 LCY
$arrMenualLink = array(
	'선택메뉴설정'=>array('use'=>'Y','link'=>'http://onedaynet.co.kr/','target'=>'_blank', 'title'=>'선택 메뉴 설정 메뉴얼' ),
	'운영자검색'=>array('use'=>'Y','link'=>'http://onedaynet.co.kr/','target'=>'_blank', 'title'=>'운영자검색 메뉴얼' ),
	'사업자정보'=>array('use'=>'Y','link'=>'http://onedaynet.co.kr/','target'=>'_blank', 'title'=>'사업자 정보 메뉴얼' ),
);

// -- LCY :: 휴대폰 선택 목록 배열 -- 사용자페이지
$arrSelectHtelType = array('010','011','016','017','018','019');

// SSJ : 2017-12-02 상품상세이용안내관리 구분
$arrProGuideType = array('10'=>'구매/배송안내','20'=>'교환/반품/환불이 가능한 경우','30'=>'교환/반품/환불이 불가능한 경우');

// LCY :: 2017-12-06 회원등급 특정기간 배열선언
$arrGroupsetCheckTerm = array(
	'print'=>array(
		'monthlast'=>'지난달 ('.date("Y.m.01" , strtotime(date("Y-m-01"))-1).' ~ '.date("Y.m.d" , strtotime(date("Y-m-01"))-1).')',
		'month1'=>'최근1개월 ('.date( "Y.m.d" , strtotime("-1 month") ).' ~ '.date("Y.m.d").')',
		'month2'=>'최근2개월 ('.date( "Y.m.d" , strtotime("-2 month") ).' ~ '.date("Y.m.d").')',
		'month3'=>'최근3개월 ('.date( "Y.m.d" , strtotime("-3 month") ).' ~ '.date("Y.m.d").')',
		'month4'=>'최근4개월 ('.date( "Y.m.d" , strtotime("-4 month") ).' ~ '.date("Y.m.d").')',
		'month5'=>'최근5개월 ('.date( "Y.m.d" , strtotime("-5 month") ).' ~ '.date("Y.m.d").')',
		'month6'=>'최근6개월 ('.date( "Y.m.d" , strtotime("-6 month") ).' ~ '.date("Y.m.d").')'
	),
	'value'=>array(
		'monthlast'=>array('s'=>date("Y-m-01" , strtotime(date("Y-m-01"))-1), 'e'=>date("Y-m-d" , strtotime(date("Y-m-01"))-1)),
		'month1'=>array('s'=>date( "Y-m-d" , strtotime("-1 month") ),'e'=>date("Y-m-d")),
		'month2'=>array('s'=>date( "Y-m-d" , strtotime("-2 month") ),'e'=>date("Y-m-d")),
		'month3'=>array('s'=>date( "Y-m-d" , strtotime("-3 month") ),'e'=>date("Y-m-d")),
		'month4'=>array('s'=>date( "Y-m-d" , strtotime("-4 month") ),'e'=>date("Y-m-d")),
		'month5'=>array('s'=>date( "Y-m-d" , strtotime("-5 month") ),'e'=>date("Y-m-d")),
		'month6'=>array('s'=>date( "Y-m-d" , strtotime("-6 month") ),'e'=>date("Y-m-d")),
	)
);



// SSJ : 2017-12-20 상품 상세페이지 노출 항목
$arrDisplayPinfo = array(
	'screenPrice' => '정상가(소비자가)'
	,'price' => '판매가'
	,'point' => '적립금'
	,'maker/orgin' => '제조사/원산지'
	,'brand' => '브랜드'
	,'deliveryInfo' => '배송정보'
	,'deliveryPrice' => '배송비'
	,'coupon' => '상품쿠폰(할인혜택)'
	// ,'groupSet' => '회원혜택'
);
// SSJ : 2017-12-20 상품 상세페이지 추가 노출 항목
$arrDisplayPinfoAdd = array(
	 'subname' => '부가 상품명'
	 ,'optionStock' => '옵션재고'
);





// JJC : 주문 - 할인항목 지정 : 2018-01-04
//			- 주문시 할인된 항목별 내용 표시에 적용 가능
//			- 통계시 주문할인액에 일괄합산하여 적용 가능
$arr_order_discount_field = array(
	'o_price_coupon_individual' => '보너스쿠폰',
	'o_price_coupon_product' => '상품쿠폰',
	'o_price_usepoint' => '적립금 사용',
	'o_promotion_price' => '프로모션코드',
	 //'' => '회원등급할인',
);

// JJC : 주문 - 취소항목 지정 : 2018-01-04
//			- 통계시 취소액에 대한 합산에 적용 가능
//			- 현금영수증, 세금계산서에서 취소액에 대한 합산에 적용 가능
$arr_order_cancel_field = array(
	'o_price_refund' => '부분취소시 환불/취소한 금액',
	'o_price_usepoint_refund' => '부분취소시 환불한 적립금',
);


// JJC : 주문 - 지역 : 2018-01-07
$arr_order_area = array(
	'강원' => '강원', '경기' => '경기', '경남' => '경남', '경북' => '경북', '광주' => '광주', '대구' => '대구', '대전' => '대전', '부산' => '부산', '서울' => '서울', '세종' => '세종', '울산' => '울산', '인천' => '인천', '전남' => '전남', '전북' => '전북', '제주' => '제주', '충남' => '충남', '충북' => '충북',
	'강원도' => '강원', '경기도' => '경기', '경상남도' => '경남', '경상북도' => '경북', '광주광역시' => '광주', '대구광역시' => '대구', '대전광역시' => '대전', '부산광역시' => '부산', '서울특별시' => '서울', '세종특별자치시' => '세종', '울산광역시' => '울산', '인천광역시' => '인천', '전라남도' => '전남', '전라북도' => '전북', '제주특별자치도' => '제주', '충청남도' => '충남', '충청북도' => '충북',
);
$arr_order_area_basic = array('강원', '경기', '경남' , '경북' , '광주' , '대구' , '대전' , '부산' , '서울' , '세종' , '울산' , '인천' , '전남' , '전북' , '제주' , '충남' , '충북');


// JJC : 통계 - 나이대 : 2018-01-12
$arr_order_age = array(10 => '10대', 20 => '20대', 30 => '30대', 40 => '40대', 50 => '50대', 60 => '60대', 70 => '70대', 'etc' => '기타');


// JJC : 통계 - 성별 : 2018-01-12
$arr_order_sex = array('M' => '남성', 'F' => '여성', 'etc' => '미선택');


// -- LCY ::: 검색조건항목
$arrSearchOption = array('category'=>'카테고리','brand'=>'브랜드','hashtag'=>'해시태그','price'=>'가격대','boon'=>'혜택구분');

// -- LCY :: 게시판 노출 유형
$arrBoardViewType = array('service'=>'고객센터','community'=>'커뮤니티');

// -- LCY :: 게시판 권한 변수
$arrBoardAuthValue = array(0=>'제한없음',2=>'회원',9=>'관리자');

// -- LCY :: 댓글 권한 변수
$arrBoardCommentAuthValue = array(2=>'회원',9=>'관리자');

$varCommentWriteLen = 500;

// -- LCY :: 파일,이미지 첨부 확장자 지정
$arrUpfileConfig['ext'] = array('file'=>array('zip','xls','xlsx' , 'pdf' , 'ppt', 'pptx' , 'doc' , 'docx', 'hwp'), 'images'=>array('png','jpg','jpeg','gif') );
$arrUpfileConfig['size'] = preg_replace("/[M]/i","",ini_get('upload_max_filesize')) > 0 ? preg_replace("/[M]/i","",ini_get('upload_max_filesize'))*1048576 : 0;
$arrUpfileConfig['cnt'] = 5; // 게시판 파일업로드 시 추가할 수 있는 개수

// -- LCY :: 자주묻는 질문 분류
$arrFaqType = array("1"=>"이용안내","2"=>"회원관련","3"=>"주문/결제/배송","4"=>"교환/환불/반품","5"=>"적립금관련","6"=>"기타");

// -- LCY :: 자주묻는질문 게시판 설정 :: 별도의 설정은 없다
$arrFaqBoardConfig['bestCnt'] = 5; // 고객센터 베스트 개수
$arrFaqBoardConfig['newIcon'] = 5; // new 아이콘 노출일
$arrFaqBoardConfig['faqType'] = array("1"=>"이용안내","2"=>"회원관련","3"=>"주문/결제/배송","4"=>"교환/환불/반품","5"=>"적립금관련","6"=>"기타");//자주묻는 질문 분류


/*
	LCY 2018-02-11 -- PG 사별 카드 코드
*/
$arr_pg_card_code = array(
'inicis' => array('01'=>'하나(외환)','03'=>'롯데','04'=>'현대','06'=>'국민','11'=>'BC','12'=>'삼성','14'=>'신한','21'=>'해외 VISA','22'=>'해외마스터','23'=>'해외 JCB','26'=>'중국은련','32'=>'광주','33'=>'전북','34'=>'하나','35'=>'산업카드','41'=>'NH','43'=>'씨티','44'=>'우리','48'=>'신협체크','51'=>'수협','52'=>'제주','54'=>'MG새마을금고체크','71'=>'우체국체크','95'=>'저축은행체크'),
'kcp' => array('CCKM'=>'KB국민카드' ,'CCNH'=>'NH채움카드' ,'CCSG'=>'신세계한미' ,'CCCT'=>'씨티카드' ,'CCHM'=>'한미카드' ,'CVSF'=>'해외비자' ,'CCAM'=>'국내아멕스','CCLO'=>'롯데카드','CAMF'=>'해외아멕스' ,'CCBC'=>'BC카드' ,'CCPH'=>'우리카드' ,'CCHN'=>'하나SK카드' ,'CCSS'=>'삼성카드' ,'CCKJ'=>'광주카드','CCSU'=>'수협카드','CCCU'=>'신협카드','CCSH'=>'신한카드','CCJB'=>'전북카드','CCCJ'=>'제주카드','CCLG'=>'신한카드','CMCF'=>'해외마스터','CJCF'=>'해외JCB','CCKE'=>'외환카드','CCDI'=>'현대카드','CCSB'=>'저축카드','CCKD'=>'산은카드','CCUF'=>'은련카드'),
'lgpay' => array('11' => '국민','42' => '제주','21' => '하나(외환)','46' => '광주','30' => 'KDB산업체크	','51' => '삼성','31' => '비씨','61' => '현대','32' => '하나','62' => '신협체크','33' => '우리(구.평화VISA)','71' => '롯데','34' => '수협','91' => 'NH	','35' => '전북','36' => '씨티','37' => '우체국체크','38' => 'MG새마을금고체크	','39' => '저축은행체크	','41' => '신한(구.LG카드 포함)'),
'billgate' => array('0052'=>'비씨카드','0050'=>'국민카드','0073'=>'현대카드','0054'=>'삼성카드','0053'=>'신한(LG)카드','0055'=>'롯데카드','0089'=>'저축은행','0051'=>'외환카드','0076'=>'하나','0079'=>'제주','0080'=>'광주','0073'=>'신협(현대)','0075'=>'수협','0081'=>'전북','0078'=>'농협','0084'=>'씨티'),
'daupay' => array('CCLG'=>'신한카드','CCBC'=>'BC카드','CCKM'=>'국민카드','CCSS'=>'삼성카드','CCDI'=>'현대카드','CCLO'=>'롯데카드','CCHN'=>'하나SK카드','CCKE'=>'외한카드','CCNH'=>'NH농협카드','CCCT'=>'시티카드','CCPH'=>'우리카드')
);

/*
	// -- 페이코 테스트 정보 추가  -- test 키값의 대문자 유의 *****
	sellerKey	//(필수) 가맹점 코드 - 파트너센터에서 알려주는 값으로, 초기 연동 시 PAYCO에서 쇼핑몰에 값을 전달한다.
	cpId	//(필수)상점ID, 30자 이내
	productId	//(필수) 상품ID, 50자 이내
	deliveryId	//(필수) 배송비상품ID, 50자 이내 :: 미사용
	deliveryReferenceKey	//(필수) 가맹점에서 관리하는 배송비상품 연동 키, 100자 이내, 고정 :: 미사용
*/
$arrPaycoInfo = array(
	'test'=>array('sellerKey'=>'S0FSJE','cpId'=>'PARTNERTEST','productId'=>'PROD_EASY','deliveryId'=>'DELIVERY_PROD','deliveryReferenceKey'=>'DV0001'),
	'paymethod'=>array('01'=>'신용카드(일반)','02'=>'무통장입금(가상계좌)','04'=>'계좌이체','31'=>'신용카드','35'=>'바로이체','98'=>'PAYCO 포인트','75'=>'페이코 쿠폰','77'=>'가맹점 쿠폰')
);


/*
	-- 문자/이메일 수신관련설정 변수
*/
$arrAddonsService = array(
	'tabMenu_080'=>array(
		"080deny/_receipt.form"=>"080 수신거부 설정",
		"080deny/_member_080deny.list"=>"080 수신거부 기록관리",
	),
	'tabMenu_smsEmail'=>array(
		"2yearOpt/_2year_opt.form"=>"수신동의 발송관리 (매2년)",
		"emailCnf/_email_config.form"=>"이메일수신거부 문구설정"
	),
	'080denyStatus'=>array(
		'OK' => '정상 수신거부 처리' ,
		'MULTI' => '다수검색 미처리' ,
		'NO' => '미검색 미처리' ,
		'FALSE' => '080 수신거부 설정 오류'
	)
);





# 비공개 작업을 위한 개발자 모드 추가 2016-07-18 LDD
$DeveloperIP = array('112.219.125.10');
$DeveMode = (in_array($_SERVER['REMOTE_ADDR'], $DeveloperIP)?true:false); // 개발사라면 데브모드 on
$DeveModeCounter = 0; // 현재페이지에서 데브모드를 사용한 횟수
$DeveModeComment = array();
define('DEVE_MODE', $DeveMode);
function DeveMode($comment=null) { // 개발자 모드 여부(true or false) // 메시지 응용 -> DeveMode(str_replace($_SERVER['DOCUMENT_ROOT'], '', __FILE__).':'.__LINE__);
	global $DeveModeCounter, $DeveloperIP, $DeveModeComment;
	$DeveModeCounter++;
	if($comment != null && $comment != '') $DeveModeComment[] = $comment;
	return (in_array($_SERVER['REMOTE_ADDR'], $DeveloperIP)?true:false);
}
function DeveModeFooter($bg_color='#4F4F58', $ft_color='#ffffff', $add_style='') { // </body> 위에 추가 하세요.
	global $DeveModeCounter, $DeveloperIP, $DeveModeComment;
	$DeveMode = (in_array($_SERVER['REMOTE_ADDR'], $DeveloperIP)?true:false);
	if($DeveMode !== true || $DeveModeCounter <= 0) return;
	$DeveModeCommentBox = '';
	if(count($DeveModeComment) > 0) {
		$DeveModeCommentBox .= '<ul>';
		foreach($DeveModeComment as $ck=>$cv) {
			if($cv) $DeveModeCommentBox .= '<li>&middot; '.$cv.'</li>';
		}
		$DeveModeCommentBox .= '</ul>';
	}
	echo '
		<div style="position: fixed; top:51px; left:0; height:'.($DeveModeCommentBox?'120':'30').'px; line-height:30px; background-color:'.$bg_color.'; color:'.$ft_color.'; text-align:center; cursor:move; z-index:2147483647; '.$add_style.'" class="js_deve_mode_tip">
			<div style="position: absolute; right: 10px;">
				<a href="#none" onclick="document.querySelector(\'.js_deve_mode_tip\').remove();">X</a>
			</div>
			<div style="padding:0 50px;">개발 작업 진행중('.number_format($DeveModeCounter).')</div>
			'.(count($DeveModeComment)?'<div style="background-color:#fff;  margin:10px 10px 10px 10px; text-align:left;"><div style="overflow-y:scroll; padding: 7px 10px; height:50px; line-height:17px;">'.$DeveModeCommentBox.'</div></div>':null).'
		</div>
		<script>
				var offset = [0,0];
				var divOverlay = document.querySelector(".js_deve_mode_tip");
				var isDown = false;
				divOverlay.addEventListener("mousedown", function(e) {
					isDown = true;
					offset = [
						divOverlay.offsetLeft - e.clientX,
						divOverlay.offsetTop - e.clientY
					];
				}, true);
				document.addEventListener("mouseup", function() {
					isDown = false;
				}, true);

				document.addEventListener("mousemove", function(e) {
					if(e.target.className == "js_deve_mode_tip") e.preventDefault(); // js_deve_mode_tip 만 이동 가능하도록 처리
					if(isDown) {
						divOverlay.style.left = ((e.clientX + offset[0]>0?e.clientX + offset[0]:0)) + "px";
						divOverlay.style.top  = ((e.clientY + offset[1]>0?e.clientY + offset[1]:0)) + "px";
					}
				}, true);
		</script>
	';
}
function DeveModeFooterDetail($title='개발 작업 진행중') { // </body> 위에 추가 하세요.
	global $DeveModeCounter, $DeveloperIP, $DeveModeComment;
	$DeveMode = (in_array($_SERVER['REMOTE_ADDR'], $DeveloperIP)?true:false);
	if($DeveMode !== true || $DeveModeCounter <= 0) return;
	$DeveModeCommentBox = '';
	$DeveModeCommentBoxBtn = '';
	if(count($DeveModeComment) > 0) {
		foreach($DeveModeComment as $ck=>$cv) {
			if($cv) $DeveModeCommentBox .= '<li style="list-style: square; margin-top:5px; border-bottom: 1px dashed #ccc; padding:5px 0;">'.$cv.'</li>';
		}
		$DeveModeCommentBoxBtn = '
			<a href="javascript:;" onclick="document.querySelector(\'.js_bottom\').style.height = \'300px\';" style="display: inline-block; float:right; margin-right:10px; background-color:#ffffff; border:1px solid #222222; color: #222222; text-align:center; width:30px; height:30px; line-height: 30px;" title="크게보기">▲</a>
			<a href="javascript:;" onclick="document.querySelector(\'.js_bottom\').style.height = \'50px\';" style="display: inline-block; float:right; margin-right:5px; background-color:#ffffff; border:1px solid #222222; color: #222222; text-align:center; width:30px; height:30px; line-height: 30px;" title="작게보기">▼</a>
		';
	}
	echo '
		<div class="js_debug_wrap" style="position:fixed; z-index: 9999999999999999999; bottom: 0; width:100%;">
			<div style="display: block;">
				<div class="js_debug_close" style="text-align: right; height:31px;">
					<a href="javascript:;" onclick="document.querySelector(\'.js_debug_wrap\').remove();" style="display: inline-block; float:right; background-color:#444444; border:1px solid #444444; color: #fff; text-align:center; width:30px; height:30px; line-height: 30px; font-weight: 700;">X</a>
					'.$DeveModeCommentBoxBtn.'
				</div>
				<div class="js_bottom" style="background-color: #444444; height: 50px">
					<div style="height:50px; line-height: 50px; color:#fff;">
						<div style="float:left; background-color:#4D815C; color:#fff; padding:0 10px; font-weight:800; font-size:13px; min-width:30px; text-align:center;">'.number_format($DeveModeCounter).'</div>
						<div style="float:left; color:#fff; margin-left:10px;">'.$title.'</div>
					</div>
					<div style="height:250px; background-color: #fff; overflow-y: scroll;">
						<ul style="margin: 5px 20px;">
							'.$DeveModeCommentBox.'
						</ul>
					</div>
				</div>
			</div>
		</div>
	';
}


// 캐싱을 위한 캐시버전 생성
$cache_ver = date('Ymd', time()).(isset($_none_cache)?str_replace(array('1.', ' '), array('.', ''), microtime()):1);

// 브랜드별 상품 적용을 위한 한글, 영문 초성 배열화
$arr_prefix_kor = array('ㄱ','ㄴ','ㄷ','ㄹ','ㅁ','ㅂ','ㅅ','ㅇ','ㅈ','ㅊ','ㅋ','ㅌ','ㅍ','ㅎ');
$arr_prefix_eng = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

// MD'S PICK 추가/수정/삭제 권한여부
$varMdAuth = false;

// 쿠폰등록시 사용되는 변수 배열화 :: 항목이 몇개 없는거라도 값을 공통화 하기위해 사용
$arrCouponSet = array(
	"ocs_view"=>array("Y"=>"발급","N"=>"발급안함"),
	"ocs_type"=>array("order"=>"주문쿠폰","delivery"=>"배송쿠폰"), // 쿠폰유형 => product: 상품적용, order : 주문적용, delivery :: 배송할인
	"ocs_issued_type"=>array("manual"=>"수동발급","auto"=>"자동발급"), // 발급방식 => manual : 수동발급, auto : 자동발급, download : 다운로드
	"ocs_issued_type_auto"=>array("1"=>"첫 구매/접수완료","2"=>"구매/접수완료","3"=>"생일축하","4"=>"회원가입","5"=>"출석체크"),  // 자동발급 설정
	"ocs_use_date_type"=>array("date"=>"사용기간 지정","expire"=>"사용가능일 지정"),  // 사용기간
	"ocs_boon_type"=>array("discount"=>"구매시 할인","save"=>"구매시 적립","delivery"=>"배송비 할인"), // 쿠폰 혜택
	"ocs_dtype"=>array("price"=>"원","per"=>"%"),  // 쿠폰 혜택 금액
	"ocs_issued_cnt_type"=>array("limit"=>"제한없음","cnt"=>"수량제한"), // 발급 수량
	"ocs_due_use"=>array("Y"=>"중복사용 가능","N"=>"중복사용 불가"), // 같은 유형의 쿠폰과 중복사용 여부
	"ocs_issued_due_type"=>array("N"=>"중복발급 안함","Y"=>"중복발급함"), // 같은 유형의 쿠폰과 중복사용 여부
);
$arr_coupon_type = array("order"=>"주문쿠폰","delivery"=>"배송쿠폰");
$arrProductDisplyImage = array(
	'pc'=>array(6=>'type_6x1.gif',5=>'type_5x1.gif', 4=> 'type_4x1.gif', 3=> 'type_3x1.gif', 2=> 'type_list2x.gif', 1=> 'type_list1x.gif'),
	'mobile'=>array(3=> 'type_3x1.gif', 2=> 'type_2x1.gif', 1=> 'type_m1x1s2.gif')
);

// KAY :: 에디터 이미지 관리 :: 2021-06-24
// 에디터 이미지 사용하는 (list가 있는 9개) 곳 변수
// product - 상품
// board - 게시글 관리
// board_template - 게시글 양식 관리
//  board_faq - 게시글 faq 관리
// popup - 팝업
// promotion - 프로모션 기획전
// normal - 디자인 일반페이지
// setting - 환경설정 상품/배송 설정 - 상품 상세 이용안내 관리
$ei_group = array(
	"product"=>array("table"=>"smart_product","content"=>"p_content","content_m"=>"p_content_m","use_table"=>"product","uid"=>"p_code","date"=>"p_rdate"),
	"board"=>array("table"=>"smart_bbs","content"=>"b_content","use_table"=>"board","uid"=>"b_uid","date"=>"b_rdate"),
	"board_template"=>array("table"=>"smart_bbs_template","content"=>"bt_content","use_table"=>"board_template","uid"=>"bt_uid","date"=>"bt_rdate"),
	"board_faq"=>array("table"=>"smart_bbs_faq","content"=>"bf_content","use_table"=>"board_faq","uid"=>"bf_uid","date"=>"bf_rdate"),
	"popup"=>array("table"=>"smart_popup","content"=>"p_content","use_table"=>"popup","uid"=>"p_uid","date"=>"p_rdate"),
	"promotion"=>array("table"=>"smart_table_text","content"=>"ttt_value","use_table"=>"promotion","uid"=>"ttt_datauid"),
	"mailing"=>array("table"=>"smart_mailing_data","content"=>"md_content","use_table"=>"mailing","uid"=>"md_uid","date"=>"md_rdate"),
	"normal"=>array("table"=>"smart_normal_page","content"=>"np_content","content_m"=>"np_content_m","use_table"=>"normal","uid"=>"np_uid","date"=>"np_rdate"),
	"setting"=>array("table"=>"smart_product_guide","content"=>"g_content","use_table"=>"setting","uid"=>"g_uid","date"=>"g_rdate")
);
