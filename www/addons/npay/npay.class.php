<?php
// LDD Npay
/**
* 원데이넷 Npay
**/
# 에러 레벨 낮춤
error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING );

# 네이버페이 연동 클래스
class OnedayNpay {

	public $NpayID, $NpayKey, $Mode, $Charset, $HomeUrl, $ImgBaseUrl, $NpayUrl, $Redirect, $User, $WishUrl, $RedirectWish; // 최초생성
	public $Pinfo, $Type; // 지정된 상품 정보
	protected $Parameter = ''; // 통신 파라미터
	protected $Xml = ''; // 상품정보 제공 xml


	# 생성자
	public function __construct($NpayID, $NpayKey, $Mode='real', $Charset='utf8') {

		// Npay 주문등록 URL
		$BaseUrl = array(
						'utf8'=>array(
							'real'=>'https://pay.naver.com/customer/api/order.nhn',
							'test'=>'https://test-pay.naver.com/customer/api/order.nhn'
						),
						'euckr'=>array(
							'real'=>'https://pay.naver.com/customer/api/CP949/order.nhn',
							'test'=>'https://test-pay.naver.com/customer/api/CP949/order.nhn'
						)
					);

		// Npay Wish 등록 URL
		$WishUrl = array(
						'utf8'=>array(
							'real'=>'https://pay.naver.com/customer/api/wishlist.nhn',
							'test'=>'https://test-pay.naver.com/customer/api/wishlist.nhn'
						),
						'euckr'=>array(
							'real'=>'https://pay.naver.com/customer/api/CP949/wishlist.nhn',
							'test'=>'https://test-pay.naver.com/customer/api/CP949/wishlist.nhn'
						)
					);


		if(is_mobile() === true) { // 모바일용

			// NPay 주문페이지
			$RedirectUrl = array(
							'real'=>'https://m.pay.naver.com/mobile/customer/order.nhn',
							'test'=>'https://test-m.pay.naver.com/mobile/customer/order.nhn'
						);

			// NPay 찜하기 페이지
			$RedirectWish = array(
							'utf8'=>array(
								'real'=>'https://m.pay.naver.com/mobile/customer/wishList.nhn',
								'test'=>'https://test-m.pay.naver.com/mobile/customer/wishList.nhn'
							),
							'euckr'=>array(
								'real'=>'https://m.pay.naver.com/mobile/customer/CP949/wishList.nhn',
								'test'=>'https://test-m.pay.naver.com/mobile/customer/CP949/wishList.nhn'
							)
						);
		}
		else { // PC용

			// NPay 주문페이지
			$RedirectUrl = array(
							'real'=>'https://pay.naver.com/customer/order.nhn',
							'test'=>'https://test-pay.naver.com/customer/order.nhn'
						);

			// NPay 찜하기 페이지
			$RedirectWish = array(
							'utf8'=>array(
								'real'=>'https://pay.naver.com/customer/wishlistPopup.nhn',
								'test'=>'https://test-pay.naver.com/customer/wishlistPopup.nhn'
							),
							'euckr'=>array(
								'real'=>'https://pay.naver.com/customer/CP949/wishlistPopup.nhn',
								'test'=>'https://test-pay.naver.com/customer/CP949/wishlistPopup.nhn'
							)
						);
		}

		// 환경변수 설정
		$this->NpayID		= $NpayID; // 상점아이디
		$this->NpayKey		= $NpayKey; // 가맹점 인증키
		$this->Mode			= $Mode; // 사용모드 설정(real: 실서비스용, test: 테스트용)
		$this->Charset		= $Charset; // 언어셋 설정(utf8, euckr)
		$this->NpayUrl		= $BaseUrl[$Charset][$Mode]; // NPay 통신 URL 지정
		$this->WishUrl		= $WishUrl[$Charset][$Mode]; // NPay wish URL 지정
		$this->Redirect		= $RedirectUrl[$Mode];
		$this->RedirectWish = $RedirectWish[$Charset][$Mode];
		$this->User			= (is_login()?get_userid():$_COOKIE["AuthShopCOOKIEID"]); // 사용자 고유

		// LCY 2019-07-31 -- (is_https() === true?'https://':'http://') https 보안서버 일경우 적용 --
		$this->HomeUrl		= (is_https() === true?'https://':'http://').$_SERVER['HTTP_HOST'];

		//$this->ImgBaseUrl	= $this->HomeUrl.'/upfiles/product/';
		$this->ImgBaseUrl   = 'http://'.$_SERVER['SERVER_NAME'].'/upfiles/product/'; // 2020-06-15 SSJ :: 이미지 URL은 http://로 보냄
	}


	# 소멸자(사용안함)
	public function __destruct() {}


	# 설정값 확인
	public function Debug() {

		$Debug = array();

		// 설정 출력
		$Debug['config'] = array();
		$Debug['config']['Mode'] = $this->Mode;
		$Debug['config']['NpayID'] = $this->NpayID;
		$Debug['config']['NpayKey'] = $this->NpayKey;
		$Debug['config']['Charset'] = $this->Charset;
		$Debug['config']['NpayUrl'] = $this->NpayUrl;
		$Debug['config']['Redirect'] = $this->Redirect;

		// 기타 기본설정
		$Debug['config']['HomeUrl'] = $this->HomeUrl;
		$Debug['config']['ImgBaseUrl'] = $this->ImgBaseUrl;
		$Debug['config']['User'] = $this->User;

		// 상품 설정
		$Debug['config']['Type'] = $this->Type;
		$Debug['config']['Pinfo'] = $this->Pinfo;

		// 기타
		$Debug['config']['Product'] = $this->ViewProduct();
		$Debug['config']['Option'] = $this->ViewOption();
		//$Debug['config']['Parameter'] = $this->MakePram();

		return $Debug;
	}


	# 상품설정
	public function set($Pinfo=array(), $Type='view') {

		$this->Pinfo = $Pinfo; // 상품정보
		$this->Type = $Type; // 액션위치(view:뷰페이지, cart:장바구니)
	}


	# 상품정보호출
	protected function ViewProduct() {

		$Re = array();
		if(count($this->Pinfo) > 0) {
			foreach($this->Pinfo as $k=>$v) {
				$Re[$v[0]] = _MQ(" select * from `smart_product` where `p_code` = '{$v[0]}' ");
			}
		}
		return $Re;
	}


	# 상품 옵션정보호출
	protected function ViewOption() {

		$Re = array();
		if($this->Type == 'view') { // 상품상세
			if(count($this->Pinfo) > 0) {
				foreach($this->Pinfo as $k=>$v) {
					$Re[$v[0]] = _MQ_assoc(" select * from `smart_product_tmpoption` where `pto_mid` = '{$this->User}' and `pto_pcode` = '{$v[0]}' ");
				}
			}
		}
		else { // 장바구니
			if(count($this->Pinfo) > 0) {
				foreach($this->Pinfo as $k=>$v) {
					$Re[$v[0]] = _MQ_assoc(" select * from `smart_cart` where `c_cookie` = '{$this->User}' and `c_pcode` = '{$v[0]}' ");
				}
			}
		}
		return $Re;
	}


	# 파라미터 추가
	protected function AddPram($key='', $val='') {

		if(trim($key) != '') $this->Parameter .= '&'.$key.'='.$val;
		return $this->Parameter;
	}
	protected function ResetPram() { $this->Parameter = ''; }


	# Mysql에 테이블이 있는지 확인 (테이블이 있다면 true 반환)
	protected function IsTable($Table) {

		$sql = " desc " . $Table;
		$result = mysql_query($sql);

		if(mysql_num_rows($result)) return true;
		else return false;
	}


	# NPay 구매등록 키 생성기
	protected function NpayKey() {

		$Key  = $_SERVER['REMOTE_ADDR']; // 접속자IP
		$Key .= '^'.$this->User; // 고유쿠키값
		$Key .= '^'.date('YmdHis'); // 날짜+시간+분+초
		$Key .= '^'.substr(microtime(),2, 6); // 마이크로 타임 6자리
		$Key .= '^'.rand(10000,99999); // 랜덤 5자리
		return md5($Key);
	}


	# npay테이블에 주문등록
	protected function npayWrite() {

		// 필요정보 호출
		$pr = $this->ViewProduct(); // 상품정보
		$po = $this->ViewOption(); // 옵션정보
		$Uniq = $this->NpayKey(); // NPay 구매등록 고유번호 생성(주문번호 마다 다름)
		_MQ_noreturn(" delete from smart_npay where c_cookie = '".$this->User."' and date(c_rdate) < (CURDATE()-INTERVAL 2 DAY) "); // 네이버 페이 카트 기록 전 기존내역 초기화(2일 이전)

		if($this->Type == 'view') { // 상품 뷰페이지에서 동작
			foreach($pr as $k=>$v) {

				if(count($po[$k]) > 0) { // 옵션이 있는 상품

					$tmpVar = _MQ("select p_point_per from smart_product where p_code = '".$k."'");
					foreach($po[$k] as $kk=>$vv) {

						_MQ_noreturn("
							insert smart_npay set
								  c_pcode 				= '". $k ."'
								, c_option1				= '". mysql_real_escape_string($vv['pto_poptionname1'])."'
								, c_option2				= '". mysql_real_escape_string($vv['pto_poptionname2'])."'
								, c_option3				= '". mysql_real_escape_string($vv['pto_poptionname3'])."'
								, c_cnt					= '".$vv['pto_cnt']."'
								, c_pouid				= '".$vv['pto_pouid']."'
								, c_cookie				= '".$this->User."'
								, c_rdate				= now()
								, c_supply_price		= '". $vv['pto_poption_supplyprice']."'
								, c_price				= '". $vv['pto_poptionprice']."'
								, c_point				= '". (($vv['pto_poptionprice']*$vv['pto_cnt'])*($tmpVar['p_point_per']/100))."'
								, c_is_addoption		= '". $vv['pto_is_addoption']."'
								, c_addoption_parent	= '". $vv['pto_addoption_parent']."'
								, c_uniq				= '". $Uniq ."'
						");
					}
				}
				else { // 옵션이 없는 상품

					_MQ_noreturn("
						insert smart_npay set
							  c_pcode 			= '". $k ."'
							, c_cnt				= '".$this->Pinfo[0][1]."'
							, c_pouid			= '0'
							, c_cookie			= '".$this->User."'
							, c_rdate			= now()
							, c_supply_price	= (select p_sPrice from smart_product where p_code='".$k."')
							, c_price			= '". $v['p_price']."'
							, c_point			= ((select p_price*(p_point_per/100) from smart_product where p_code='".$k."')*".$this->Pinfo[0][1].")
							, c_uniq			= '". $Uniq ."'
					");
				}
			}
		}
		else { // 상품 카트에서 동작
			foreach($po as $k=>$v) {
				foreach($v as $kk=>$vv) {

					_MQ_noreturn("
						insert smart_npay set
							  c_pcode 				= '". $vv['c_pcode'] ."'
							, c_option1				= '". mysql_real_escape_string($vv['c_option1'])."'
							, c_option2				= '". mysql_real_escape_string($vv['c_option2'])."'
							, c_option3				= '". mysql_real_escape_string($vv['c_option3'])."'
							, c_cnt					= '".$vv['c_cnt']."'
							, c_pouid				= '".$vv['c_pouid']."'
							, c_cookie				= '".$this->User."'
							, c_rdate				= now()
							, c_supply_price		= '". $vv['c_supply_price']."'
							, c_price				= '". $vv['c_price']."'
							, c_point				= '". (($vv['c_poptionprice']*$vv['c_cnt'])*($tmpVar['p_point_per']/100))."'
							, c_is_addoption		= '". $vv['c_is_addoption']."'
							, c_addoption_parent	= '". $vv['c_addoption_parent']."'
							, c_uniq				= '". $Uniq ."'
					");
				}
			}
		}

		return $Uniq;
	}


	# 카트의 배송비를 구한다. -> jjc -> ldd
	protected function CartDPay() {
		global $SubAdminMode, $siteInfo;

		$prCode = array();
		$pr = $this->ViewProduct();
		if(count($pr) <= 0) $pr = array();
		foreach($pr as $k=>$v) {
			$prCode[] = $k;
		}

		// --- 장바구니 정보 추출 ---
		$arr_cart = $arr_customer = $arr_delivery = $arr_product_info = array(); $tot = 0;

		// ![LCY] 2020-06-15 네이버페이 입점업체 배송비 적용 오류 패치 m.cp_delivery_use, 추가
		$que = "
			select
				c.* , p.*, po.*,
				m.cp_name, m.cp_id , m.cp_delivery_price , m.cp_delivery_freeprice , m.cp_delivery_company , m.cp_delivery_use , 
				c_pouid as app_pouid
			from smart_cart as c
			inner join smart_product as p on (p.p_code=c.c_pcode)
			inner join smart_company as m on (m.cp_id=p.p_cpid)
			left join smart_product_option as po on (po.po_uid = c.c_pouid)
			where
				c.c_cookie = '{$this->User}' and
				c.c_pcode in ('".implode("', '", $prCode)."')
			order by c_uid asc
		";
		$r = _MQ_assoc($que);
		// ----- JJC : 상품별 배송비 : 2018-08-16 -----
			$arr_product_per_apply = array();
			$arr_per_product = array();
			foreach( $r as $k=>$v ){
				$arr_per_product[$v['c_pcode']]['sum'] += $v['c_cnt'] * ($v['c_price'] + $v['c_optionprice']);
			}
		// ----- JJC : 상품별 배송비 : 2018-08-16 -----
		foreach( $r as $k=>$v ){

			// 쇼핑몰  배송비 정책을 사용한다.
			if($v['cp_delivery_use'] == "N" || $SubAdminMode === false ) {

				$v['cp_delivery_price'] = $siteInfo['s_delprice'];
				$v['cp_delivery_freeprice'] = $siteInfo['s_delprice_free'];
			}
			$arr_customer[$v['p_cpid']] = array('com_delprice'=>$v['cp_delivery_price'] , 'com_delprice_free'=>$v['cp_delivery_freeprice']);

			// 배송비용 계산을 위한 입점업체별 주문금액합산 - 개별배송 , 무료배송일 경우 가격 포함 하지 않음.
			if( $v['p_shoppingPay_use']=="N" ){

				$arr_delivery[$v['p_cpid']] += $v['c_cnt'] * $v['c_price'];
			}
			else if($v['p_shoppingPay_use']=="Y" ){

				$arr_customer[$v['p_cpid']]['app_delivery_price'] += $v['p_shoppingPay'] * $v['c_cnt'] ;
				$tot  += $v['p_shoppingPay'] * $v['c_cnt'] ;
			}
			// ----- JJC : 상품별 배송비 : 2018-08-16 -----
			else if($v['p_shoppingPay_use'] == 'P') {
				// ![LCY] 상품별 배송비 오류 패치 p_shoppingPayPfPrice 는 0보다 커야함
				$tot  += ($arr_product_per_apply[$v['c_pcode']] > 0 ? 0 : ($v['p_shoppingPayPfPrice'] >  $arr_per_product[$v['c_pcode']]['sum'] || $v['p_shoppingPayPfPrice'] == 0 ? $v['p_shoppingPayPdPrice'] : 0 ));
				$arr_product_per_apply[$v['c_pcode']] ++;
			}
			// ----- JJC : 상품별 배송비 : 2018-08-16 -----
		}
		// --- 업체별 배송비 정보 계산 ---
		// --- 업체별 배송비 처리 ---
		if(sizeof(array_filter($arr_delivery)) > 0 ) {
			foreach( array_filter($arr_delivery) as $k=>$v ){
				if($arr_customer[$k]['com_delprice_free'] > 0) {
					$arr_customer[$k]['app_delivery_price'] += ($arr_customer[$k]['com_delprice_free'] > $v ? $arr_customer[$k]['com_delprice'] : 0 ); // 배송비적용
					$tot += ($arr_customer[$k]['com_delprice_free'] > $v ? $arr_customer[$k]['com_delprice'] : 0 ); // 배송비적용
				}
				else {
					$arr_customer[$k]['app_delivery_price'] += $arr_customer[$k]['com_delprice'];//배송비적용
					$tot  += $arr_customer[$k]['com_delprice'];//배송비적용
				}
			}
		}
		// --- 업체별 배송비 처리 ---
		return $tot;
	}



	# 전달할 쿼리스트링 만듦
	protected function MakePram() {

		global $siteInfo, $SubAdminMode;

		// 필요정보 호출
		$pr = $this->ViewProduct(); // 상품정보
		$po = $this->ViewOption(); // 옵션정보
		$this->ResetPram(); // 파라미터를 초기화
		$NPayWriteData = $this->npayWrite(); // 상품을 NPayWriteData에 등록 하고 고유키를 받는다.

		// ---------- 공통 파라미터 조합 ---------- //
			// (필수: Y)
			// 상점 ID. 네이버페이에 가입 승인될 때 정해진다.
			$this->AddPram('SHOP_ID', urlencode($this->NpayID));
			// (필수: Y)
			// 인증키. 네이버페이에 가입 승인될 때 정해진다.
			$this->AddPram('CERTI_KEY', urlencode($this->NpayKey));

			// (필수: N)
			// 경로별 매출 코드. 영문자 기준 최대 300자. 매출 코드가 필요한 경우에 입력한다.
			$this->AddPram('SALES_CODE', '');

			// (필수: N)
			// 몰 내부 관리 코드. 영문자 기준 최대 300자. 내부 관리 코드가 필요한 경우에 입력한다.
			$this->AddPram('MALL_MANAGE_CODE', $NPayWriteData); // NPayWriteData의 c_uniq값을 전달

			// (필수: N)
			// 지식쇼핑 CPA 코드. 지식쇼핑 가맹점 중 파라미터 방식을 이용한 CPA 과금을 원하는 가맹점은 이 값을 입력한다.
			$this->AddPram('CPA_INFLOW_CODE', urlencode($_COOKIE["CPAValidator"]));

			// (필수: N)
			// SA CLICK ID. 네이버 검색광고 이용 가맹점 중 광고주 센터의 광고 효과 보고서를 통해 네이버페이 전환 데이터를 확인하길 원하는 가맹점은 SA로부터 받은 추적 URL 파라미터 중 NVADID를 입력한다.
			$this->AddPram('SA_CLICK_ID', $_COOKIE["NVADID"]);

			// (필수: N)
			// 네이버페이 포인트 유입 경로 코드
//			$this->AddPram('NMILEAGE_INFLOW_CODE', '');

			// (필수: N)
			// 네이버 서비스 유입 경로 코드(네이버 서비스를 통해서 유입된 경우에 서비스를 구분하기 위해 사용되는 코드)를 입력한다.
			$this->AddPram('NAVER_INFLOW_CODE', urlencode($_COOKIE["NA_CO"]));
		// ---------- 공통 파라미터 조합 ---------- //


		// ---------- 카트인경우 입점업체별배송비 처리 ---------- //


		// ---------- 품별 파라미터 조합 ---------- //
			$TOTAL_COUNT = 0; // 합계수량
			$TOTAL_PRICE = 0; // 합계금액
			$TOTAL_SHIPPING_PRICE = ($this->Type == 'cart'?$this->CartDPay():0); // 카트의 경우 전체 배송비를 계산하여 초기값으로 지정
			$arr_customer_apply = array();

			if(count($this->Pinfo) > 0) {
				foreach($this->Pinfo as $k=>$v) {

					// 해당상품정보
					$Vpr = $pr[$v[0]]; // 상품정보
					$Vpo = $po[$v[0]]; // 옵션정보 or 카트정보
					$VOprice = $Vpr['p_price']; // 상품기본가격
					$Vprice = 0; // 총금액(상품금액+옵션)
					$Vcount = 0; // 총구매수량(옵션+)
					$SHIPPING_PRICE = 0; // 상품별 배송비

					$ITEM_OPTION = ''; // 옵션명 ("색상:노랑/사이즈:XL")
					$ITEM_OPTION_CODE = ''; // 옵션코드(1/2/3/5/)

					// 네이버 페이 지원 불가 상품은 건너 뜀 (테스트 모드에서는 무시)
					if($Vpr['npay_use'] == 'N' && $this->Mode == 'real') continue;

					$ITEM_OPTION_NEW = array(); // 2016-03-25 네이버요청에 따라 조정
					$ITEM_OPTION_CODE_NEW = array(); // 2016-03-25 네이버요청에 따라 조정
					$ITEM_OPTION_PRICE = array(); // 2016-03-25 네이버요청에 따라 조정
					$ITEM_OPTION_COUNT = array(); // 2016-03-25 네이버요청에 따라 조정


					if($this->Type == 'view') { // 상품뷰에서 실행

						if($Vpr['p_option_type_chk'] == 'nooption') { // 무옵션 상품

							$Vcount += $v[1]; // 수량은 초기 넘어온 값으로 설정
							$Vprice += $Vpr['p_price'] * $v[1];
						}
						else { // 옵션상품

							foreach($Vpo as $kk=>$vv) {

								$OptionName_Tmp = array(
									$vv['pto_poptionname1'],
									$vv['pto_poptionname2'],
									$vv['pto_poptionname3']
								);
								$OptionName = array();
								foreach($OptionName_Tmp as $kkk=>$vvv) {

									if(trim($vvv) == '') continue;
									$OptionName[] = $vvv;
								}
								$Vcount += $vv['pto_cnt'];
								$Vprice += (($vv['pto_pprice']+$vv['pto_poptionprice'])*$vv['pto_cnt']);

								if($vv['pto_pouid'] <= 0) continue; // 옵션이 없다면 제외
								$ITEM_OPTION .= implode('/', $OptionName).'/'.$vv['pto_cnt'].'개/';
								$ITEM_OPTION_CODE .= ($vv['pto_pouid']?$vv['pto_pouid']:0).'/';

								$ITEM_OPTION_NEW[] = implode('/', $OptionName).'/'.$vv['pto_cnt'].'개/'; // 2016-03-25 네이버요청에 따라 조정
								$ITEM_OPTION_CODE_NEW[] = ($vv['pto_pouid']?$vv['pto_pouid']:0); // 2016-03-25 네이버요청에 따라 조정
								$ITEM_OPTION_PRICE[] = (($vv['pto_pprice']+$vv['pto_poptionprice'])*$vv['pto_cnt']); // 2016-03-25 네이버요청에 따라 조정
								$ITEM_OPTION_COUNT[] = $vv['pto_cnt']; // 2016-03-25 네이버요청에 따라 조정
							}
						}
					}
					else { // 상품 카트에서 실행

						foreach($Vpo as $kk=>$vv) {

							$OptionName_Tmp = array(
									$vv['c_option1'],
									$vv['c_option2'],
									$vv['c_option3']
							);
							$OptionName = array();
							foreach($OptionName_Tmp as $kkk=>$vvv) {

								if(trim($vvv) == '') continue;
								$OptionName[] = $vvv;
							}
							$Vcount += $vv['c_cnt']; // 전체 수량
							$Vprice += ($vv['c_cnt']*($vv['c_price']+$vv['c_optionprice'])); // 전체 가격

							if($vv['c_pouid'] <= 0) continue; // 옵션이 없다면 제외
							if($vv['c_pouid'] > 0) {
								$ITEM_OPTION .= implode('/', $OptionName);
								$ITEM_OPTION_CODE .= ($vv['c_pouid']?$vv['c_pouid']:0).'/';

								$ITEM_OPTION_NEW[] = implode('/', $OptionName); // 2016-03-25 네이버요청에 따라 조정
								$ITEM_OPTION_CODE_NEW[] = ($vv['c_pouid']?$vv['c_pouid']:0); // 2016-03-25 네이버요청에 따라 조정
							}
							$ITEM_OPTION_PRICE[] = ($vv['c_cnt']*($vv['c_price']+$vv['c_optionprice'])); // 2016-03-25 네이버요청에 따라 조정
							$ITEM_OPTION_COUNT[] = $vv['c_cnt']; // 2016-03-25 네이버요청에 따라 조정
						}
					}


					// View에서 요청시
					if($this->Type == 'view') {

						// 배송료 처리
						if($Vpr['p_shoppingPay_use'] == 'N') { // 배송비 정책이 입점형

							$arr_customer = _MQ("select * from `smart_company` where `cp_id` = '{$Vpr['p_cpid']}' ");
							if($arr_customer['cp_delivery_use'] == "N" || $SubAdminMode === false) {

								$Vpr['p_shoppingPay'] = $siteInfo['s_delprice'];
								$Vpr['p_shoppingPayFree'] = $siteInfo['s_delprice_free'];
							}
							else if($arr_customer['cp_delivery_use'] == "Y" && $SubAdminMode === true) {

								$Vpr['p_shoppingPay'] = $arr_customer['cp_delivery_price'];
								$Vpr['p_shoppingPayFree'] = $arr_customer['cp_delivery_freeprice'];
							}

							if(($Vprice < $Vpr['p_shoppingPayFree'] || $Vpr['p_shoppingPayFree'] == 0) && !$arr_customer_apply[$Vpr['p_cpid']]) {

								$TOTAL_SHIPPING_PRICE += $Vpr['p_shoppingPay'];
								$arr_customer_apply[$Vpr['p_cpid']] = $Vpr['p_cpid'];
							}
							else {

								$TOTAL_SHIPPING_PRICE += 0;
							}
						}
						else if($Vpr['p_shoppingPay_use'] == 'Y') { // 개별상품

							$TOTAL_SHIPPING_PRICE += $Vpr['p_shoppingPay'] * $Vcount;
						}
						// ----- JJC : 상품별 배송비 : 2018-08-16 -----
						else if($Vpr['p_shoppingPay_use'] == 'P') {
							if($Vprice > $Vpr['p_shoppingPayPfPrice'] && $Vpr['p_shoppingPayPfPrice'] > 0 ) $TOTAL_SHIPPING_PRICE = 0;  // ![LCY] 상품별 배송비 오류 패치 p_shoppingPayPfPrice 는 0보다 커야함
							else $TOTAL_SHIPPING_PRICE = $Vpr['p_shoppingPayPdPrice'];
						}
						// ----- JJC : 상품별 배송비 : 2018-08-16 -----
						else { // 배송상품 아님 -> 무료배송

							$TOTAL_SHIPPING_PRICE += 0;
						}
					}


					//echo '실행위치: '.$this->Type.'<br>';
					//echo '상품수량: '.$Vcount.'<br>';
					//echo '상품합산금액: '.$Vprice.'<br>';
					//echo '배송비: '.$TOTAL_SHIPPING_PRICE.'<br>';

					//ViewArr($Vpr);
					//ViewArr($Vpo);


					$TOTAL_COUNT += $Vcount;
					$TOTAL_PRICE += $Vprice;

					/*
					// (필수: Y)
					// 상품 ID
					$this->AddPram('ITEM_ID', $Vpr['p_code']);

					// (필수: N)
					// 지식쇼핑 EP의 ma_pid. 지식쇼핑 가맹점이면 지식쇼핑 EP의 ma_pid 와 동일한 값을 입력해야 한다.
					$this->AddPram('EC_MALL_PID', $Vpr['p_code']);

					// (필수: Y)
					// 상품 주문 개수
					$this->AddPram('ITEM_COUNT', $Vcount);

					// (필수: Y)
					// 개별 상품 단가. 0보다 커야 한다.
					$this->AddPram('ITEM_UPRICE', $VOprice);

					// (필수: Y)
					// 상품 이름
					$this->AddPram('ITEM_NAME', urlencode($Vpr['p_name']));


					// (필수: Y)
					// 해당 상품 총 가격. 상품 할인 행사가 있으면, ITEM_COUNT와 ITEM_UPRICE를 곱한 값보다 작은 값을 가질 수 있다.
					//$this->AddPram('ITEM_TPRICE', ($Vprice+$SHIPPING_PRICE) );
					$this->AddPram('ITEM_TPRICE', $Vprice);

					// (필수: Y)
					// 선택한 옵션 사항
					// ITEM_OPTION의 값은 실제 주문서 페이지에 그대로 표시된다. 각 쇼핑몰은 이용자가 선택한 옵션 사항을 텍스트로 표시할 수 있다. HTML 태그는 사용할 수 없다. ITEM_OPTION이 여러 종류일 경우 슬래시(/)로 구분하는 것을 권장한다. 예를 들어, 색상은 노랑이고 사이즈는 XL이면 ITEM_OPTION은 "노랑/XL" 또는 "색상:노랑/사이즈:XL"로 표기하는 것을 권장한다.
					$this->AddPram('ITEM_OPTION', $ITEM_OPTION);

					// (필수: N)
					// 주문 등록 시 각 상품의 옵션별 추가 정보를 저장하기 위한 상품 단위 관리 코드
					// ITEM_OPTION_CODE는 각 가맹점이 주문 등록 시 각 상품의 옵션별 추가 정보를 저장하기 위한 상품 단위 관리 코드이다. 옵션별 상품 코드가 별도로 존재하거나 옵션별 상품 재고 관리가 필요하여 기존의 MALL_MANAGE_CODE만으로는 해당 상품 단위 관리가 어려울 때 이 값을 사용할 수 있다.
					$this->AddPram('ITEM_OPTION_CODE', $ITEM_OPTION_CODE);
					*/



					// 2016-03-25 네이버요청에 따라 조정
					if(count($ITEM_OPTION_NEW) > 0) {
						foreach($ITEM_OPTION_NEW as $kkkk=>$vvvv) {

							// (필수: Y)
							// 상품 ID
							$this->AddPram('ITEM_ID', $Vpr['p_code']);

							// (필수: N)
							// 지식쇼핑 EP의 ma_pid. 지식쇼핑 가맹점이면 지식쇼핑 EP의 ma_pid 와 동일한 값을 입력해야 한다.
							$this->AddPram('EC_MALL_PID', $Vpr['p_code']); // 지식쇼핑 EP의 ma_pid. 지식쇼핑 가맹점이면 지식쇼핑 EP의 ma_pid 와 동일한 값을 입력해야 한다.

							// (필수: Y)
							// 상품 주문 개수
							$this->AddPram('ITEM_COUNT', $ITEM_OPTION_COUNT[$kkkk]);

							// (필수: Y)
							// 개별 상품 단가. 0보다 커야 한다.
							$this->AddPram('ITEM_UPRICE', $VOprice);

							// (필수: Y)
							// 상품 이름
							$this->AddPram('ITEM_NAME', urlencode($Vpr['p_name'])); //


							// (필수: Y)
							// 해당 상품 총 가격. 상품 할인 행사가 있으면, ITEM_COUNT와 ITEM_UPRICE를 곱한 값보다 작은 값을 가질 수 있다.
							$this->AddPram('ITEM_TPRICE', $ITEM_OPTION_PRICE[$kkkk]);

							// (필수: Y)
							// 선택한 옵션 사항
							// ITEM_OPTION의 값은 실제 주문서 페이지에 그대로 표시된다. 각 쇼핑몰은 이용자가 선택한 옵션 사항을 텍스트로 표시할 수 있다. HTML 태그는 사용할 수 없다. ITEM_OPTION이 여러 종류일 경우 슬래시(/)로 구분하는 것을 권장한다. 예를 들어, 색상은 노랑이고 사이즈는 XL이면 ITEM_OPTION은 "노랑/XL" 또는 "색상:노랑/사이즈:XL"로 표기하는 것을 권장한다.
							$this->AddPram('ITEM_OPTION', urlencode($ITEM_OPTION_NEW[$kkkk]));

							// (필수: N)
							// 주문 등록 시 각 상품의 옵션별 추가 정보를 저장하기 위한 상품 단위 관리 코드
							// ITEM_OPTION_CODE는 각 가맹점이 주문 등록 시 각 상품의 옵션별 추가 정보를 저장하기 위한 상품 단위 관리 코드이다. 옵션별 상품 코드가 별도로 존재하거나 옵션별 상품 재고 관리가 필요하여 기존의 MALL_MANAGE_CODE만으로는 해당 상품 단위 관리가 어려울 때 이 값을 사용할 수 있다.
							$this->AddPram('ITEM_OPTION_CODE', $ITEM_OPTION_CODE_NEW[$kkkk]);
						}
					}
					else {
						// (필수: Y)
						// 상품 ID
						$this->AddPram('ITEM_ID', $Vpr['p_code']);

						// (필수: N)
						// 지식쇼핑 EP의 ma_pid. 지식쇼핑 가맹점이면 지식쇼핑 EP의 ma_pid 와 동일한 값을 입력해야 한다.
						$this->AddPram('EC_MALL_PID', $Vpr['p_code']);

						// (필수: Y)
						// 상품 주문 개수
						$this->AddPram('ITEM_COUNT', $Vcount);

						// (필수: Y)
						// 개별 상품 단가. 0보다 커야 한다.
						$this->AddPram('ITEM_UPRICE', $VOprice);

						// (필수: Y)
						// 상품 이름
						$this->AddPram('ITEM_NAME', urlencode($Vpr['p_name']));


						// (필수: Y)
						// 해당 상품 총 가격. 상품 할인 행사가 있으면, ITEM_COUNT와 ITEM_UPRICE를 곱한 값보다 작은 값을 가질 수 있다.
						//$this->AddPram('ITEM_TPRICE', ($Vprice+$SHIPPING_PRICE) );
						$this->AddPram('ITEM_TPRICE', $Vprice);

						// (필수: Y)
						// 선택한 옵션 사항
						// ITEM_OPTION의 값은 실제 주문서 페이지에 그대로 표시된다. 각 쇼핑몰은 이용자가 선택한 옵션 사항을 텍스트로 표시할 수 있다. HTML 태그는 사용할 수 없다. ITEM_OPTION이 여러 종류일 경우 슬래시(/)로 구분하는 것을 권장한다. 예를 들어, 색상은 노랑이고 사이즈는 XL이면 ITEM_OPTION은 "노랑/XL" 또는 "색상:노랑/사이즈:XL"로 표기하는 것을 권장한다.
						$this->AddPram('ITEM_OPTION', $ITEM_OPTION);

						// (필수: N)
						// 주문 등록 시 각 상품의 옵션별 추가 정보를 저장하기 위한 상품 단위 관리 코드
						// ITEM_OPTION_CODE는 각 가맹점이 주문 등록 시 각 상품의 옵션별 추가 정보를 저장하기 위한 상품 단위 관리 코드이다. 옵션별 상품 코드가 별도로 존재하거나 옵션별 상품 재고 관리가 필요하여 기존의 MALL_MANAGE_CODE만으로는 해당 상품 단위 관리가 어려울 때 이 값을 사용할 수 있다.
						$this->AddPram('ITEM_OPTION_CODE', $ITEM_OPTION_CODE);
					}
				}
			}
		// ---------- 품별 파라미터 조합 ---------- //


		// ---------- 합산 파라미터 조합 ---------- //
			// (필수: Y)
			// 네이버페이 주문서 페이지에서 [이전페이지]를 클릭했을 때 이동하는 페이지 URL. 상점 메인 화면으로 돌아가거나, 주문을 따로 저장했다면 주문서 페이지로 돌아가게 할 수 있다.
			if($this->Type == 'cart') $this->AddPram('BACK_URL', urlencode($this->HomeUrl.'/?pn=shop.cart.list'));
			else $this->AddPram('BACK_URL', urlencode($this->HomeUrl.'/?pn=product.view&pcode='.$this->Pinfo[0][0]));

			// (필수: Y)
			// 배송료. 무료이면 0, 선불 또는 착불이면 배송료(0보다 커야 함). 착불이면서 배송료를 특정할 수 없는 경우에는 0
			$this->AddPram('SHIPPING_PRICE', $TOTAL_SHIPPING_PRICE);

			// (필수: Y)
			// 배송료 지불 방법. 무료이면 "FREE", 선불이면 "PAYED", 착불이면 "ONDELIVERY"
			$this->AddPram('SHIPPING_TYPE', ($TOTAL_SHIPPING_PRICE>0?'PAYED':'FREE'));

			// (필수: N)
			// 지역별 추가 배송료에 대한 안내. 글자 수 기준 최대 50자(예: 제주도 3,000원 추가, 제주도 외 도서·산간 지역 5,000원 추가).
			$this->AddPram('SHIPPING_ADDITIONAL_PRICE', '');

			// (필수: Y)
			// 총 주문 금액. ITEM_TPRICE의 합과 선불 배송료를 더한 값과 같아야 한다.
			$this->AddPram('TOTAL_PRICE', ($TOTAL_PRICE+$TOTAL_SHIPPING_PRICE));
		// ---------- 합산 파라미터 조합 ---------- //


		// 파라미터 반환
		$Param['param'] = ($TOTAL_COUNT > 0?$this->AddPram():'error'); // 조합한 파라미터를 변수화
		$Param['TOTAL_PRICE'] = ($TOTAL_PRICE+$TOTAL_SHIPPING_PRICE);

		$this->ResetPram(); // 파라미터를 초기화
		return $Param; // 파라미터 반환
	}


	# 주문서 등록 및 주문페이지 이동
	public function run() {

		$MakePram		= $this->MakePram();
		$TOTAL_PRICE	= $MakePram['TOTAL_PRICE'];
		$queryString	= $MakePram['param'];
		if($queryString == 'error') return error_msg('네이버페이로 주문할 수 있는 상품이 없습니다.');
		$NpayUrl		= parse_url($this->NpayUrl);
		$req_addr		= 'ssl://'.$NpayUrl['host'];
		$req_url		= 'POST '.$NpayUrl['path'].' HTTP/1.1';
		$req_host		= $NpayUrl['host'];
		$req_port		= 443;
		$nc_sock = @fsockopen($req_addr, $req_port, $errno, $errstr);
		if($nc_sock) {

			fwrite($nc_sock, $req_url."\r\n" );
			fwrite($nc_sock, "Host: ".$req_host.":".$req_port."\r\n" );
			if($Charset == 'euckr') fwrite($nc_sock, "Content-type: application/x-www-form-urlencoded; charset=CP949\r\n");
			else fwrite($nc_sock, "Content-type: application/x-www-form-urlencoded; charset=utf-8\r\n");
			fwrite($nc_sock, "Content-length: ".strlen($queryString)."\r\n");
			fwrite($nc_sock, "Accept: */*\r\n");
			fwrite($nc_sock, "\r\n");
			fwrite($nc_sock, $queryString."\r\n");
			fwrite($nc_sock, "\r\n");

			// get header
			while(!feof($nc_sock)){

				$header = fgets($nc_sock, 4096);
				if($header == "\r\n") break;
				else $headers .= $header;
			}

			// get body
			while(!feof($nc_sock)){ $bodys .= fgets($nc_sock, 4096); }

			fclose($nc_sock);
			$resultCode = substr($headers, 9, 3);

			if($resultCode == 200) $orderId = $bodys; // success
			else die($bodys); // fail
		}
		else {
			return "{$errstr} ({$errno})\n";
			exit;
		}

		if($resultCode == 200) {

			// 주문서 URL 재전송(redirect)
			$redirectUrl = $this->Redirect.'?ORDER_ID='.$orderId.'&SHOP_ID='.$this->NpayID.'&TOTAL_PRICE='.$TOTAL_PRICE;
			@header("Location:".$redirectUrl);
		}
	}


	# 상품 제공 정보 출력
	protected function AddXml($k='', $v='', $type='multi') {

		if(trim($k) != '') {

			if($type == 'alone') {

				$this->Xml .= $k.PHP_EOL;
			}
			else {

				$k_exp = explode(' ', $k);
				if(trim($v) != '') $this->Xml .= "<{$k}><![CDATA[{$v}]]></{$k_exp[0]}>".PHP_EOL;
				else $this->Xml .= "<{$k}>{$v}</{$k_exp[0]}>".PHP_EOL;
			}
		}

		return $this->Xml;
	}
	protected function ResetXml() { $this->Xml = '<?xml version="1.0" encoding="utf-8"?>'; }
	public function ProductView($Mode='real') {

		// 필요한 함수 준비
		if(!function_exists('get_category_info')) {

			// 카테고리 정보 추출한다.
			// 인자 : 카테고리 코드
			// 리턴 : 카테고리 정보(배열)
			function get_category_info($catecode) {
				return _MQ("select * from `smart_category` where `c_uid` = '{$catecode}'");
			}
		}
		if(!function_exists('get_total_category_info')) {
			// 1,2,3차 카테고리 정보를 모두 추출한다.
			// 인자 : 카테고리 코드
			// 리턴 : 1,2,3 카테고리 정보(배열)
			function get_total_category_info($catecode) {
				// 카테고리 정보
				$category_info = get_category_info($catecode);

				switch($category_info[c_depth]) {
					case 3;
						$total_info[depth3_catecode] = $category_info[c_uid];
						$total_info[depth3_catename] = $category_info[c_name];

						$parent_catecode = end(explode(",",$category_info[c_parent]));
						$category_info = get_category_info($parent_catecode);
					case 2;
						$total_info[depth2_catecode] = $category_info[c_uid];
						$total_info[depth2_catename] = $category_info[c_name];

						$parent_catecode = end(explode(",",$category_info[c_parent]));
						$category_info = get_category_info($parent_catecode);
					case 1;
						$total_info[depth1_catecode]			= $category_info[c_uid];
						$total_info[depth1_catename]			= $category_info[c_name];
						$total_info[depth1_display]				= $category_info[c_catetype];
				}

				return $total_info;
			}
		}

		// 해더 설정
		header('Content-Type: application/xml; charset=utf-8');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');

		// 상품코드 추출
		$QueryStrings = $_SERVER['QUERY_STRING'];
		$pcode = array();
		foreach(explode('&', $QueryStrings) as $k=>$v) {

			list($kk, $vv) = explode('=', $v);
			$pcode[] = $vv;
		}

		// 공통
		$this->AddXml('<?xml version="1.0" encoding="utf-8"?>', '', 'alone');
		$this->AddXml('<response>', '', 'alone');

		// 표시 가능한 상품이 없는경우
		if(trim($pcode[0]) == '') {

			$this->AddXml('error', '표시할 수 있는 상품 정보가 없습니다. (ITEM_ID)');
			$this->AddXml('</response>', '', 'alone');
			$xml = $this->AddXml();
			$this->ResetXml();
			return $xml;
		}

		// 상품코드별 생성
		foreach($pcode as $k=>$v) {

			$Pr = _MQ(" select * from `smart_product` where `p_code` = '{$v}' ");
			$Pc = _MQ(" select * from `smart_product_category` where `pct_pcode` = '{$v}' ");
			if(!$Pr['p_code']) continue;
			$category = get_total_category_info($Pc['pct_cuid']);
			$this->AddXml('<item id="'.$v.'">', '', 'alone');

				$MainImage = (strpos($Pr['p_img_b1'], '//') !== false?$Pr['p_img_b1']:$this->ImgBaseUrl.$Pr['p_img_b1']);
				$ThumbImage = ($Pr['p_img_list']?(strpos($Pr['p_img_list'], '//') !== false?$Pr['p_img_list']:$this->ImgBaseUrl.$Pr['p_img_list']):$MainImage);
				$this->AddXml('mall_pid', ''); // 지식쇼핑 EP의 ma_pid(필수 X)
				$this->AddXml('name', $Pr['p_name']); // 상품 이름
				$this->AddXml('url', $this->HomeUrl.'/?pn=product.view&pcode='.$v); // 상품 정보 URL
				$this->AddXml('description', $Pr['p_subname']); // 상품 설명
				$this->AddXml('image', $MainImage); // 상품 사진 URL
				$this->AddXml('thumb', $ThumbImage); // 상품 썸네일 URL
				$this->AddXml('price', $Pr['p_price']); // 상품의 정상 가격
				$this->AddXml('quantity', $Pr['p_stock']); // 상품의 재고량

				// 카테고리가 있는경우 카테고리 출력
				if($Pr['p_option_type_chk'] != 'nooption') {

					$this->AddXml('<options>', '', 'alone');

						$op1 = _MQ_assoc(" select * from `smart_product_option` where `po_pcode` = '{$v}' and `po_depth` = '1' "); // 1차옵션
						if($Pr['p_option_type_chk'] == '1depth') { // 1차옵션

							$this->AddXml('<option name="상품옵션">', '', 'alone');
							foreach($op1 as $kk=>$vv) {
								$this->AddXml('select', $vv['po_poptionname']);
							}
							$this->AddXml('</option>', '', 'alone');
						} else { // 2~3 차옵션(2차옵션 까지만 표기됨)

							foreach($op1 as $kk=>$vv) {

								$this->AddXml('<option name="'.$vv['po_poptionname'].'">', '', 'alone');

								$op2 = _MQ_assoc(" select * from `smart_product_option` where `po_pcode` = '{$v}' and `po_depth` = '2' and `po_parent` = '{$vv['po_uid']}' "); // 2차옵션
								foreach($op2 as $kkk=>$vvv) {

									$this->AddXml('select', $vvv['po_poptionname']);
								}
								$this->AddXml('</option>', '', 'alone');
							}
						}
					$this->AddXml('</options>', '', 'alone');

				}

				// 가맹점 사이트에서 상품의 카테고리 (최대 4차->솔루션에 맞게 3차까지만)
				if($category['depth1_catename'] && $category['depth2_catename'] && $category['depth3_catename']) $this->AddXml('<category>', '', 'alone');
					if($category['depth1_catename']) $this->AddXml('first', $category['depth1_catename']);
					if($category['depth2_catename']) $this->AddXml('second', $category['depth2_catename']);
					if($category['depth3_catename']) $this->AddXml('third', $category['depth3_catename']);
				if($category['depth1_catename'] && $category['depth2_catename'] && $category['depth3_catename']) $this->AddXml('</category>', '', 'alone');


				// 상품별 반품 주소 (필수 X)
				/*
				$xml = $this->AddXml('<returnInfo>', '', 'alone'); // <!-- 상품별 반품 주소 -->
				$xml = $this->AddXml('zipcode', '우편번호'); // 우편번호
				$xml = $this->AddXml('address1', '기본 주소. 동(읍/면/리)까지 입력 (광주 광역시 쌍촌동)'); // 기본 주소. 동(읍/면/리)까지 입력 (광주 광역시 쌍촌동)
				$xml = $this->AddXml('address2', '상세 주소. 번지 및 아파트 동호수까지 입력 (905-52 2층 상상너머(원데이넷))'); // 상세 주소. 번지 및 아파트 동호수까지 입력 (905-52 2층 상상너머(원데이넷))
				$xml = $this->AddXml('sellername', '수령인 이름 (상상너머 판매자)'); // 수령인 이름 (상상너머 판매자)
				$xml = $this->AddXml('contact1', '연락처1 (1544-6937)'); // 연락처1 (1544-6937)
				$xml = $this->AddXml('contact2', '연락처2. 이 값은 생략할 수 있다.'); // 연락처2. 이 값은 생략할 수 있다.
				$xml = $this->AddXml('</returnInfo>', '', 'alone');
				*/

			$this->AddXml('</item>', '', 'alone');
		}


		// 공통
		$xml = $this->AddXml('</response>', '', 'alone');
		$xml = $this->AddXml();
		$this->ResetXml();
		return $xml;
	}


	# 찜하기
	public function WishAdd() {

		$Pinfo = array();
		foreach($this->ViewProduct() as $k=>$v) { $Pinfo = $v; }
		$this->ResetPram(); // 파라미터를 초기화

		// (필수: Y)
		// 상점 ID. 네이버페이에 가입 승인될 때 정해진다.
		$this->AddPram('SHOP_ID', urlencode($this->NpayID));

		// (필수: Y)
		// 인증키. 네이버페이에 가입 승인될 때 정해진다.
		$this->AddPram('CERTI_KEY', urlencode($this->NpayKey));

		// (필수: Y)
		// 상품 ID
		$this->AddPram('ITEM_ID', $Pinfo['p_code']);

		// (필수: N)
		// 지식쇼핑 EP의 ma_pid. 지식쇼핑 가맹점이면 지식쇼핑 EP의 ma_pid 와 동일한 값을 입력해야 한다.
		$this->AddPram('EC_MALL_PID', $Vpr['p_code']);

		// (필수: Y)
		// 상품 이름
		$this->AddPram('ITEM_NAME', $Pinfo['p_name']);

		// (필수: Y)
		// 상품 설명
		$this->AddPram('ITEM_DESC', $Pinfo['p_subname']);

		// (필수: Y)
		// 개별 상품 단가. 0보다 커야 한다.
		$this->AddPram('ITEM_UPRICE', $Pinfo['p_price']);


		// 외부이미지 호환 URL
		$MainImage = (strpos($Pinfo['p_img_b1'], '//') !== false?$Pinfo['p_img_b1']:$this->ImgBaseUrl.$Pinfo['p_img_b1']);
		$ThumbImage = ($Pinfo['p_img_list']?(strpos($Pinfo['p_img_list'], '//') !== false?$Pinfo['p_img_list']:$this->ImgBaseUrl.$Pinfo['p_img_list']):$MainImage);


		// (필수: Y)
		// 상품 사진 URL.
		$this->AddPram('ITEM_IMAGE', $MainImage);

		// (필수: Y)
		// 상품 썸네일 URL.
		$this->AddPram('ITEM_THUMB', $ThumbImage);

		// (필수: Y)
		// 상품 정보 URL.
		$this->AddPram('ITEM_URL', urlencode($this->HomeUrl.'/?pn=product.view&pcode='.$Pinfo['p_code']));


		// 위시 정보 등록
		$MakePram		= $this->AddPram();
		$this->ResetPram(); // 파라미터를 초기화
		$queryString 	= $MakePram;
		$NpayUrl		= parse_url($this->WishUrl);
		$req_addr		= 'ssl://'.$NpayUrl['host'];
		$req_url		= 'POST '.$NpayUrl['path'].' HTTP/1.1';
		$req_host		= $NpayUrl['host'];
		$req_port		= 443;
		$nc_sock = @fsockopen($req_addr, $req_port, $errno, $errstr);
		if($nc_sock) {

			fwrite($nc_sock, $req_url."\r\n" );
			fwrite($nc_sock, "Host: ".$req_host.":".$req_port."\r\n" );
			if($Charset == 'euckr') fwrite($nc_sock, "Content-type: application/x-www-form-urlencoded; charset=CP949\r\n");
			else fwrite($nc_sock, "Content-type: application/x-www-form-urlencoded; charset=utf-8\r\n");
			fwrite($nc_sock, "Content-length: ".strlen($queryString)."\r\n");
			fwrite($nc_sock, "Accept: */*\r\n");
			fwrite($nc_sock, "\r\n");
			fwrite($nc_sock, $queryString."\r\n");
			fwrite($nc_sock, "\r\n");

			// get header
			while(!feof($nc_sock)){

				$header = fgets($nc_sock, 4096);
				if($header == "\r\n") break;
				else $headers .= $header;
			}

			// get body
			while(!feof($nc_sock)){ $bodys .= fgets($nc_sock, 4096); }

			fclose($nc_sock);
			$resultCode = substr($headers, 9, 3);

			if($resultCode == 200) $orderId = $bodys; // success
			else die($bodys); // fail
		}
		else {
			echo "{$errstr} ({$errno})\n";
			exit;
		}
		//echo $orderId;

		if($resultCode == 200) {

			// 주문서 URL 재전송(redirect)
			$redirectUrl = $this->RedirectWish.'?SHOP_ID='.$this->NpayID.'&ITEM_ID='.$orderId;
			@header("Location:".$redirectUrl);
		}
	}
}
$Npay = new OnedayNpay($siteInfo['npay_id'], $siteInfo['npay_key'], $siteInfo['npay_mode']);