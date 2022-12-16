<?php
/*
mhash0.9.9.9
libmcrypt2.5.8
mcrypt2.6.8
php >= 4.4
php option --with-mhash --with-mcrypt --with-dom --with-zlib-dir
*/
if(!$_SERVER['DOCUMENT_ROOT']) $_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__).'/../../'); // dirname(__FILE__) 다음 경로 주의
define('LIB_PATH',  $_SERVER['DOCUMENT_ROOT'].'/addons/npay/');
$_path_str = $_SERVER['DOCUMENT_ROOT'];
include_once($_SERVER['DOCUMENT_ROOT'].'/include/inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/addons/npay/npay.class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/addons/npay/nhnapi-simplecryptlib.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/addons/npay/HTTP/Request.php');


// 네이버페이 주문연동 클래스 (OnedayNpay의 확장 클래스)
class Sync extends OnedayNpay {

	public $LicenseKey, $SecretKey, $SyncMode, $SyncUrl;
	public $scl; // 네이버 플랫폼 클래스 변수

	# extends를 위하여 똑같이 값을 부모 __construct에 넣는다.
	public function __construct($Npay, $LicenseKey, $SecretKey, $SyncMode='test') {

		// 부모값 승계를 위한 처리
		/*
		* $Npay 변수는 OnedayNpay 클래스 선언 변수
		*/
		$NpayID = $Npay->NpayID;
		$NpayKey = $Npay->NpayKey;
		$Mode = $Npay->Mode;
		$Charset = $Npay->Charset;
		parent::__construct($NpayID, $NpayKey, $Mode, $Charset);

		$this->scl = new NHNAPISCL(); // 네이버플랫폼 서비스 클래스 변수 생성
		$this->LicenseKey = $LicenseKey;
		$this->SecretKey = $SecretKey;
		$this->SyncMode = $SyncMode;
		if($SyncMode == 'test') $this->SyncUrl = 'http://sandbox.api.naver.com/Checkout/';
		else $this->SyncUrl = 'http://ec.api.naver.com/Checkout/';
	}

	# 설정값 확인
	public function Debug() {

		$Debug = array();
		$Debug['config'] = array();
		$Debug['config']['SyncMode'] = $this->SyncMode;
		$Debug['config']['LicenseKey'] = $this->LicenseKey;
		$Debug['config']['SecretKey'] = $this->SecretKey;
		$Debug['config']['SyncUrl'] = $this->SyncUrl;

		$Debug['NpayDebug'] = parent::Debug();
		return $Debug;
	}

	# ~~~~~~~~~~~~~~~~~~~~~~~~ 기반함수 ~~~~~~~~~~~~~~~~~~~~~~~~ #
	# 결과별 메시지 (한글 메시지 반환)
	public function StatusText($val) {

		$status = array(
					'PAY_WAITING'=>'입금 대기(주문변경)',
					'PAYMENT_WAITING'=>'입금 대기(주문상태)',
					'PAYED'=>'결제 완료(주문변경, 주문상태)',
					'DISPATCHED'=>'발송 처리(주문변경)',
					'CANCEL_REQUESTED'=>'취소 요청(주문변경)',
					'RETURN_REQUESTED'=>'반품 요청(주문변경)',
					'EXCHANGE_REQUESTED'=>'교환 요청(주문변경)',
					'EXCHANGE_REDELIVERY_READY'=>'교환 재배송 준비(주문변경)',
					'HOLDBACK_REQUESTED'=>'구매 확정 보류 요청(주문변경)',
					'CANCELED'=>'취소(주문변경, 주문상태)',
					'RETURNED'=>'반품(주문변경, 주문상태)',
					'EXCHANGED'=>'교환(주문변경, 주문상태)',
					'PURCHASE_DECIDED'=>'구매 확정(주문변경, 주문상태)',
					'CANCELED_BY_NOPAYMENT'=>'미입금 취소(주문상태)',
					'CANCEL'=>'취소(클레임)',
					'RETURN'=>'교환(클레임)',
					'EXCHANGE'=>'반품(클레임)',
					'PURCHASE_DECISION_HOLDBACK'=>'구매 확정 보류(클레임)',
					'ADMIN_CANCEL'=>'직권 취소(클레임)',
					'CANCEL_REQUEST'=>'취소 요청(클레임 처리)',
					'CANCELING'=>'취소 처리 중(클레임 처리)',
					'CANCEL_DONE'=>'취소 처리 완료(클레임 처리)',
					'CANCEL_REJECT'=>'취소 철회(클레임 처리)',
					'RETURN_REQUEST'=>'반품 요청(클레임 처리)',
					'COLLECTING'=>'수거 처리 중(클레임 처리)',
					'COLLECT_DONE'=>'수거 완료(클레임 처리)',
					'RETURN_DONE'=>'반품 완료(클레임 처리)',
					'RETURN_REJECT'=>'반품 철회(클레임 처리)',
					'EXCHANGE_REQUEST'=>'교환 요청(클레임 처리)',
					'EXCHANGE_REDELIVERING'=>'교환 재배송 중(클레임 처리)',
					'EXCHANGE_DONE'=>'교환 완료(클레임 처리)',
					'EXCHANGE_REJECT'=>'교환 거부(클레임 처리)',
					'PURCHASE_DECISION_HOLDBACK'=>'구매 확정 보류(클레임 처리)',
					'PURCHASE_DECISION_HOLDBACK_REDELIVERING'=>'구매 확정 보류 재배송 중(클레임 처리)',
					'PURCHASE_DECISION_REQUEST'=>'구매 확정 요청(클레임 처리)',
					'PURCHASE_DECISION_HOLDBACK_RELEASE'=>'구매 확정 보류 해제(클레임 처리)',
					'ADMIN_CANCELING'=>'구매 확정 보류 해제(클레임 처리)',
					'ADMIN_CANCEL_DONE'=>'직권 취소 완료(클레임 처리)',
					'NOT_YET'=>'미보류 or 발주 미확인(보류, 발주상태코드)',
					'HOLDBACK'=>'보류 중(보류)',
					'RELEASED'=>'보류 해제(보류)',
					'SELLER_CONFIRM_NEED'=>'판매자 확인 필요(보류사유)',
					'PURCHASER_CONFIRM_NEED'=>'구매자 확인 필요(보류사유)',
					'SELLER_REMIT'=>'판매자 직접 송금(보류사유)',
					'PRODUCT_PREPARE'=>'상품 준비 중(발송지연사유)',
					'CUSTOMER_REQUEST'=>'고객 요청(발송지연사유)',
					'CUSTOM_BUILD'=>'주문 제작(발송지연사유)',
					'RESERVED_DISPATCH'=>'예약 발송(발송지연사유)',
					'ETC'=>'기타(발송지연사유, 교환보류사유)',
					'DELIVERY'=>'택배, 등기, 소포(배송방법)',
					'GDFW_ISSUE_SVC'=>'굿스플로 송장 출력(배송방법)',
					'VISIT_RECEIPT'=>'방문 수령(배송방법)',
					'DIRECT_DELIVERY'=>'직접 전달(배송방법)',
					'QUICK_SVC'=>'퀵서비스(배송방법)',
					'NOTHING'=>'배송 없음(배송방법)',
					'RETURN_DESIGNATED'=>'지정 반품 택배(배송방법)',
					'RETURN_DELIVERY'=>'일반 반품 택배(배송방법)',
					'RETURN_INDIVIDUAL'=>'직접 반송(배송방법)',
					'PRODUCT_UNSATISFIED'=>'서비스 및 상품 불만족(클레임요청사유)',
					'DELAYED_DELIVERY'=>'배송 지연(클레임요청사유)',
					'SOLD_OUT'=>'상품 품절(클레임요청사유)',
					'INTENT_CHANGED'=>'구매 의사 취소(클레임요청사유)',
					'WRONG_ORDER'=>'다른 상품 잘못 주문(클레임요청사유)',
					'DROPPED_DELIVERY'=>'배송 누락(클레임요청사유)',
					'BROKEN'=>'상품 파손(클레임요청사유)',
					'INCORRECT_INFO'=>'상품 정보 상이(클레임요청사유)',
					'WRONG_DELIVERY'=>'오배송(클레임요청사유)',
					'WRONG_OPTION'=>'색상 등이 다른 상품을 잘못 배송(클레임요청사유)',
					'NOT_YET_DISCUSSION'=>'상호 협의가 완료되지 않은 주문 건(클레임요청사유)',
					'OUT_OF_STOCK'=>'재고 부족으로 인한 판매 불가(클레임요청사유)',
					'SALE_INTENT_CHANGED'=>'판매 의사 변심으로 인한 거부(클레임요청사유)',
					'NOT_YET_PAYMENT'=>'구매자의 미결제로 인한 거부(클레임요청사유)',
					'OK'=>'발주 확인(발주 상태)',
					'CANCEL'=>'발주 확인 해제(발주 상태)',
					'DOMESTIC'=>'국내(주소타입)',
					'FOREIGN'=>'국외(주소타입)',
					'EXCHANGE_DELIVERYFEE'=>'교환 배송비 청구(교환보류사유)',
					'EXCHANGE_EXTRAFEE'=>'기타 교환 비용 청구(교환보류사유)',
					'EXCHANGE_PRODUCT_READY'=>'교환 상품 미입고(교환보류사유)',
					'EXCHANGE_HOLDBACK'=>'교환 구매 확정 보류(교환보류사유)',
					'GENERAL'=>'구매평(구매평 유형)',
					'PREMIUM'=>'프리미엄 구매평(구매평 유형)',
		);
		return $status[$val];
	}

	# 택배회사 이름 조회
	public function Courier($val, $type='oneday') {

		$Courier = array(
			'CJGLS'=>array('npay'=>'CJ대한통운', 'oneday'=>'CJ GLS(HTH통합)'),
			'KGB'=>array('npay'=>'로젠택배', 'oneday'=>'로젠택배'),
			'DONGBU'=>array('npay'=>'KG로지스', 'oneday'=>'KG로지스'),
			'EPOST'=>array('npay'=>'우체국택배', 'oneday'=>'우체국택배'),
			'REGISTPOST'=>array('npay'=>'우편등기', 'oneday'=>'우체국등기'),
			'HANJIN'=>array('npay'=>'한진택배', 'oneday'=>'한진택배'),
			'HYUNDAI'=>array('npay'=>'롯데택배', 'oneday'=>'롯데택배'),
			'INNOGIS'=>array('npay'=>'GTX로지스', 'oneday'=>'GTX로지스'),
			'DAESIN'=>array('npay'=>'대신택배', 'oneday'=>'대신택배'),
			'ILYANG'=>array('npay'=>'일양로지스', 'oneday'=>'일양택배'),
			'KDEXP'=>array('npay'=>'경동택배', 'oneday'=>'경동택배'),
			'CHUNIL'=>array('npay'=>'천일택배', 'oneday'=>'천일택배'),
			'CH1'=>array('npay'=>'기타 택배', 'oneday'=>'기타택배'),
			'HDEXP'=>array('npay'=>'합동택배', 'oneday'=>'합동택배'),
			'CVSNET'=>array('npay'=>'편의점택배', 'oneday'=>'편의점택배'),
			'DHL'=>array('npay'=>'DHL', 'oneday'=>'DHL'),
			'FEDEX'=>array('npay'=>'FEDEX', 'oneday'=>'FEDEX'),
			'GSMNTON'=>array('npay'=>'GSMNTON', 'oneday'=>'GSMNTON'),
			'WARPEX'=>array('npay'=>'WarpEx', 'oneday'=>'WarpEx'),
			'WIZWA'=>array('npay'=>'WIZWA', 'oneday'=>'WIZWA'),
			'EMS'=>array('npay'=>'EMS', 'oneday'=>'우체국EMS'),
			'DHLDE'=>array('npay'=>'DHL(독일)', 'oneday'=>'DHL(독일)'),
			'ACIEXPRESS'=>array('npay'=>'ACI', 'oneday'=>'ACI Express'),
			'EZUSA'=>array('npay'=>'EZUSA', 'oneday'=>'EZUSA'),
			'PANTOS'=>array('npay'=>'범한판토스', 'oneday'=>'범한판토스'),
			'UPS'=>array('npay'=>'UPS', 'oneday'=>''),
			'KOREXG'=>array('npay'=>'CJ대한통운(국제택배)', 'oneday'=>'CJ대한통운(국제택배)'),
			'TNT'=>array('npay'=>'TNT', 'oneday'=>'TNT'),
			'SWGEXP'=>array('npay'=>'성원글로벌', 'oneday'=>'성원글로벌'),
			'DAEWOON'=>array('npay'=>'대운글로벌', 'oneday'=>'대운글로벌'),
			'USPS'=>array('npay'=>'USPS', 'oneday'=>'USPS'),
			'IPARCEL'=>array('npay'=>'i-parcel', 'oneday'=>'i-parcel'),
			'KUNYOUNG'=>array('npay'=>'건영택배', 'oneday'=>'건영택배'),
			'HPL'=>array('npay'=>'한의사랑택배', 'oneday'=>'한의사랑택배'),
			'DADREAM'=>array('npay'=>'다드림', 'oneday'=>'다드림'),
			'SLX'=>array('npay'=>'SLX택배', 'oneday'=>'SLX택배'),
			'HONAM'=>array('npay'=>'호남택배', 'oneday'=>'호남택배'),
			'GSIEXPRESS'=>array('npay'=>'GSI익스프레스', 'oneday'=>'GSI익스프레스'),
		);
		return ($Courier[$val][$type]?$Courier[$val][$type]:$val);
	}

	# 네이버 saop의 반환 respon -> xml2array 키값에서 필요없는 값을 지운다.
	protected function NDelArray($value) {

		if(is_array($value)) {

			$RVal = array();
			foreach($value as $k=>$v) {

				$rk = str_replace(array('soapenv:', 'n:', 'n1:'), '', $k);
				$RVal[$rk] = $v;
			}
			return array_map(array($this, 'NDelArray'), $RVal);
		}
		else {

			return $value;
		}
	}

	# xml데이터를 array로 바꾼다.
	protected function xml2array($contents, $get_attributes=1, $priority = 'tag') {

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

		return $this->NDelArray($xml_array);
	}

	# SOAP 로 자료 요청
	protected function Soap($operation, $request_body, $service = 'MallService41') {

		# 통신
		$targetUrl = $this->SyncUrl.$service;
		$rq = new HTTP_Request($targetUrl);
		$rq->addHeader("Content-Type", "text/xml; charset=UTF-8");
		$rq->addHeader("SOAPAction", $service . "#" . $operation);
		$rq->setBody($request_body);
		$result = $rq->sendRequest();
		$rcode = $rq->getResponseCode();
		if(PEAR::isError($result)) return 'error: '.$result->toString();
		if($rcode!='200') return 'error: http response code='.$rcode;
		$response = $rq->getResponseBody();

		# 반환
		return $response;
	}

	# dsxl 형태 만듦
	/**
	* $MallAddContent 는 오퍼레이션별 DSXL 값을 지정
	* output: array('key'=>복호화키, 'xml'=>전송dsxl)
	**/
	protected function GetDsxl($operation, $MallAddContent, $service = 'MallService41') {

		# 준비
		$scl = $this->scl;
		$timestamp = $scl->getTimestamp();
		$detailLevel = 'Full';
		$version = '4.1';
		$accessLicense = $this->LicenseKey;
		$key = $this->SecretKey;
		$signature = $scl->generateSign($timestamp . $service . $operation, $key);

		$xml = '
			<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:mall="http://mall.checkout.platform.nhncorp.com/" xmlns:base="http://base.checkout.platform.nhncorp.com/">
				<soapenv:Header/>
				<soapenv:Body>
					<mall:'.$operation.'Request>
						<base:AccessCredentials>
							<base:AccessLicense>'.$accessLicense.'</base:AccessLicense>
							<base:Timestamp>'.$timestamp.'</base:Timestamp>
							<base:Signature>'.$signature.'</base:Signature>
						</base:AccessCredentials>
						<base:RequestID></base:RequestID>
						<base:DetailLevel>'.$detailLevel.'</base:DetailLevel>
						<base:Version>'.$version.'</base:Version>
						'.$MallAddContent.'
					</mall:'.$operation.'Request>
				</soapenv:Body>
			</soapenv:Envelope>
		';
		$Return['xml'] = $xml; // 전송될 XML반환
		$Return['key'] = $scl->generateKey($timestamp, $key); // 암문을 복호화 하기 위한 키를 생성
		return $Return;
	}

	# 리턴메시지 출력 (무조건 성공 표시를 하지만 로그를 남김)
	protected function MSG($msg, $http_code='200') {

		@header('HTTP/1.1 200');
		@header('body: RESULT=TRUE');
		if($http_code != 200) $this->CallbackActionLog('[ERROR] '.$msg);
		die('RESULT=TRUE'); exit;
	}



	# ~~~~~~~~~~~~~~~~~~~~~~~~ 조회계열 ~~~~~~~~~~~~~~~~~~~~~~~~ #
	# 특정 상품주문에 대한 상세 내역을 조회한다. ($OrderID = productorderid)
	public function GetProductOrderInfoList($OrderID = '', $service = 'MallService41') {

		if(trim($OrderID) == '') return;
		$operation = 'GetProductOrderInfoList';
		$Dsxl = $this->GetDsxl($operation, '<mall:ProductOrderIDList>'.$OrderID.'</mall:ProductOrderIDList>');
		$request_body = $Dsxl['xml'];
		$response = $this->Soap($operation, $request_body);
		$response = $this->xml2array($response);
		$response = $response['Envelope']['Body']['GetProductOrderInfoListResponse']['ProductOrderInfoList'];

		$scl = $this->scl;

		// 암호화된 데이터를 풀어 준다.
		if($response[0]) {
			foreach($response as $k=>$v) {

				$response[$k]['Order']['OrdererID'] = $scl->decrypt($Dsxl['key'], $v['Order']['OrdererID']);
				$response[$k]['Order']['OrdererName'] = $scl->decrypt($Dsxl['key'], $v['Order']['OrdererName']);
				$response[$k]['Order']['OrdererTel1'] = $scl->decrypt($Dsxl['key'], $v['Order']['OrdererTel1']);
				$response[$k]['ProductOrder']['ShippingAddress']['BaseAddress'] = $scl->decrypt($Dsxl['key'], $v['ProductOrder']['ShippingAddress']['BaseAddress']);
				$response[$k]['ProductOrder']['ShippingAddress']['Name'] = $scl->decrypt($Dsxl['key'], $v['ProductOrder']['ShippingAddress']['Name']);
				$response[$k]['ProductOrder']['ShippingAddress']['Tel1'] = $scl->decrypt($Dsxl['key'], $v['ProductOrder']['ShippingAddress']['Tel1']);

				if($response[$k]['ProductOrder']['MallMemberID'])
					$response[$k]['ProductOrder']['MallMemberID'] = $scl->decrypt($Dsxl['key'], $v['ProductOrder']['MallMemberID']);

				if($response[$k]['ProductOrder']['ShippingAddress']['DetailedAddress'])
					$response[$k]['ProductOrder']['ShippingAddress']['DetailedAddress'] = $scl->decrypt($Dsxl['key'], $v['ProductOrder']['ShippingAddress']['DetailedAddress']);
				if($response[$k]['ProductOrder']['ShippingAddress']['Tel2'])
					$response[$k]['ProductOrder']['ShippingAddress']['Tel2'] = $scl->decrypt($Dsxl['key'], $v['ProductOrder']['ShippingAddress']['Tel2']);
			}

			$response = $response[0];
		}
		else {
			$response['Order']['OrdererID'] = $scl->decrypt($Dsxl['key'], $response['Order']['OrdererID']);
			$response['Order']['OrdererName'] = $scl->decrypt($Dsxl['key'], $response['Order']['OrdererName']);
			$response['Order']['OrdererTel1'] = $scl->decrypt($Dsxl['key'], $response['Order']['OrdererTel1']);
			$response['ProductOrder']['ShippingAddress']['BaseAddress'] = $scl->decrypt($Dsxl['key'], $response['ProductOrder']['ShippingAddress']['BaseAddress']);
			$response['ProductOrder']['ShippingAddress']['Name'] = $scl->decrypt($Dsxl['key'], $response['ProductOrder']['ShippingAddress']['Name']);
			$response['ProductOrder']['ShippingAddress']['Tel1'] = $scl->decrypt($Dsxl['key'], $response['ProductOrder']['ShippingAddress']['Tel1']);

			if($response['ProductOrder']['MallMemberID'])
				$response['ProductOrder']['MallMemberID'] = $scl->decrypt($Dsxl['key'], $response['ProductOrder']['MallMemberID']);
			if($response['ProductOrder']['ShippingAddress']['DetailedAddress'])
				$response['ProductOrder']['ShippingAddress']['DetailedAddress'] = $scl->decrypt($Dsxl['key'], $response['ProductOrder']['ShippingAddress']['DetailedAddress']);
			if($response['ProductOrder']['ShippingAddress']['Tel2'])
				$response['ProductOrder']['ShippingAddress']['Tel2'] = $scl->decrypt($Dsxl['key'], $response['ProductOrder']['ShippingAddress']['Tel2']);
		}

		return $response; // 일단 주문 3건 검색결과 배열내부 같은 자료 반복
	}

	# 변경 상품주문 조회
	public function GetChangedProductOrderList($TimeForm='', $TimeTo='', $LastChangedStatusCode = '', $service = 'MallService41') {

		$AddXml = '';
		$operation = 'GetChangedProductOrderList';
		/*
		$LastChangedStatusCode =
			PAY_WAITING / 입금 대기
			PAYED / 결제 완료
			DISPATCHED / 발송 처리
			CANCEL_REQUESTED / 취소 요청
			RETURN_REQUESTED / 반품 요청
			EXCHANGE_REQUESTED / 교환 요청
			EXCHANGE_REDELIVERY_READY / 교환 재배송 준비
			HOLDBACK_REQUESTED / 구매 확정 보류 요청
			CANCELED / 취소
			RETURNED / 반품
			EXCHANGED / 교환
			PURCHASE_DECIDED / 구매 확정
		 */

		if($this->SyncMode != 'test') { // 테스트 모드가 아닐경우

			// 시작일이 없다면 오늘
			if(trim($TimeForm) == '') $AddXml .= '<base:InquiryTimeFrom>'.date('Y-m-d', time()).'T00:00:00+09:00</base:InquiryTimeFrom>';
			else $AddXml .= '<base:InquiryTimeFrom>'.date('Y-m-d', strtotime($TimeForm)).'T00:00:00+09:00</base:InquiryTimeFrom>';

			// 종료일이 없다면 오늘
			if(trim($TimeTo) == '') $AddXml .= '';
			else $AddXml .= '<base:InquiryTimeTo>'.date('Y-m-d', strtotime($TimeTo)).'T23:59:59+09:00</base:InquiryTimeTo>';
		}
		else { // 테스트 모드일 경우 시작일을 2012-01-01로 설정

			$AddXml .= '<base:InquiryTimeFrom>2012-01-01T00:00:00+09:00</base:InquiryTimeFrom>';
			$AddXml .= '<base:InquiryTimeTo>2012-01-02T00:00:00+09:00</base:InquiryTimeTo>';
		}

		// 상태값으로 조회
		if(trim($LastChangedStatusCode) != '') $AddXml .= '<mall:LastChangedStatusCode>'.$LastChangedStatusCode.'</mall:LastChangedStatusCode>';
		else $AddXml .= '<mall:LastChangedStatusCode/>';

		$Dsxl = $this->GetDsxl($operation, $AddXml.'<mall:MallID/>');
		$request_body = $Dsxl['xml'];
		$response = $this->Soap($operation, $request_body);
		$response = $this->xml2array($response);
		$response = ($response['Envelope']['Body']['GetChangedProductOrderListResponse']['ChangedProductOrderInfoList']?$response['Envelope']['Body']['GetChangedProductOrderListResponse']['ChangedProductOrderInfoList']:$response['Envelope']['Body']['GetChangedProductOrderListResponse']);
		if(!$response[0]) $response = array($response);
		@rsort($response); // 최신자료가 먼저 나오도록 정렬 반전
		return $response;
	}


    # LDD: 2019-01-16 주문정보 조회(상품주문번호 조회용) - 검수 요청 필요 없는 API
    public function GetProductOrderIDList($OrderID = '', $service = 'MallService41') {
        global $DeveMode;
        $operation = 'GetProductOrderIDList';
        $AddXml = '';
        $AddXml .= '<mall:OrderID>'.$OrderID.'</mall:OrderID>';
        $Dsxl = $this->GetDsxl($operation, $AddXml.'<mall:MallID/>');
        $request_body = $Dsxl['xml'];
        $response = $this->Soap($operation, $request_body);
        $response = $this->xml2array($response);
        $response = $response['Envelope']['Body']['GetProductOrderIDListResponse'];
        if($response['ProductOrderIDList']) $response = (is_array($response['ProductOrderIDList']) === true?$response['ProductOrderIDList']:array($response['ProductOrderIDList']));
        else $response = array();
        return $response;
    }

	# ~~~~~~~~~~~~~~~~~~~~~~~~ 처리계열 ~~~~~~~~~~~~~~~~~~~~~~~~ #
	# 상태별 동장 처리 (콜백URL -> TYPE -> 해당 함수 실행) 2016-05-27 LDD
	public function CallbackAction($type) {

		/*
		# 전달 가능한 $type 값
		PAY_WAITING => 입금 대기
		PAYED => 결제 완료 ★
		DISPATCHED => 발송 처리 ★
		CANCEL_REQUESTED => 취소 요청
		RETURN_REQUESTED => 반품 요청
		EXCHANGE_REQUESTED => 교환 요청
		EXCHANGE_REDELIVERY_READY => 교환 재배송 준비
		HOLDBACK_REQUESTED => 구매 확정 보류 요청
		CANCELED => 취소 ★
		RETURNED => 반품
		EXCHANGED => 교환
		PURCHASE_DECIDED => 구매 확정
		*/
		if(trim($type) == '') $this->MSG('ERROR: TYPE값이 넘어오지 않음', '405'); // 405(허용되지 않는 방법)
		$ChangeOrder = $this->GetChangedProductOrderList('','', $type); // TYPE 기준 주문변경 이력조회


		# 접속기록 남김 (FILE: ../npay/log/{Y-m-d}.log) -> 에러시만 남기도록 주석처리
		//$this->CallbackActionLog();

		# 테스트 모드는 무조건 성공 출력 (내부처리 막음)
		if($this->SyncMode == 'test') $this->MSG('RESULT=TRUE', '200');


		# 동작별 액션을 취한다. (클래스 함수가 없는경우 미지원 기능으로 반환)
		if(method_exists($this, 'Run_'.$type) === true) $this->{'Run_'.$type}($ChangeOrder);
		else $this->MSG('미지원 TYPE (none exists class Function {'.$type.'})', '406'); // 406(허용되지 않음)
	}
	protected function CallbackActionLog($msg = '') {

		$LogPath = $_SERVER['DOCUMENT_ROOT'].'/addons/npay/log/';
		$LogFile = date('Y-m-d').'.log';
		$LogMsg = '';
		$LogMsg .= "[{$_SERVER['REMOTE_ADDR']}] ".date('Y-m-d H:i:s', time()).PHP_EOL;
		$LogMsg .= print_r($_REQUEST, true);
		if(trim($msg) != '') $LogMsg .= $msg.PHP_EOL;
		$LogMsg .= "------------------------------------".PHP_EOL.PHP_EOL;

		if(!is_dir($LogPath)) @mkdir($LogPath, 0777, true);
		$Write = fopen($LogPath.$LogFile, 'a');
		fwrite($Write, $LogMsg);
		fclose($Write);
	}

	# 결제완료처리 2016-05-27 LDD
	protected function Run_PAYED($ChangeOrder) {
		global $SubAdminMode, $siteInfo;

		# 기본변수 준비
		$MoveOrder = array();
		$MoveOrdernum = array();

		// 하이센스용
		$arr_paymethod = array(
			"신용카드" => "card",
			"신용카드 간편결제" => "card",
			"실시간계좌이체" => "iche",
			"계좌 간편결제" => "iche",
			"무통장입금" => "online",
			"포인트결제" => "point",
			"가상계좌" => "virtual",
			"휴대폰" => "hpp",
			"휴대폰 간편결제" => "hpp",
		);

		$arr_tmp_delivery_price = array(); // 배송비 처리 배열
		$product_delivery_price = array();

		# 변경 정보 만큼 처리 진행
		foreach($ChangeOrder as $nk=>$nv) {

			# 필요정보 변수화
			$Npoid = $nv['ProductOrderID']; // [네이버] PONO900000000008
			$OrderDetail = $this->GetProductOrderInfoList($Npoid); // 데이터 호출

			# 임시주문 복사
			if(!$MoveOrder[$OrderDetail['ProductOrder']['MallManageCode']]) { // 중복처리 방지 영역

				# 중복처리 방지를 위한 배열추가
				$MoveOrder[$OrderDetail['ProductOrder']['MallManageCode']] = $OrderDetail['ProductOrder']['MallManageCode'];
				$_ordernum = shop_ordernum_create(); // 주문번호 생성 예) 12345-23456-34567
				$MoveOrdernum[$OrderDetail['ProductOrder']['MallManageCode']] = $_ordernum;


				# 상품+임시주문정보에서 데이터를 가져온다.
				$MallManageCode = $OrderDetail['ProductOrder']['MallManageCode'];
				$OData = _MQ_assoc("
					select
						*
					from
						`smart_npay` as `n` left join
						`smart_product` as `p` on(`n`.`c_pcode` = `p`.`p_code`)
					where
						`n`.`c_uniq` = '".$MallManageCode."'
				");
				if(count($OData) <= 0) continue;

				# 배송비처리
				$arr_cart = $arr_customer = $arr_delivery = $arr_product_info = array();
				$que = "
					select
						`c`.*, `p`.*, `po`.*, `pao`.*,
						`m`.`cp_name`, `m`.`cp_id`, `m`.`cp_delivery_price`, `m`.`cp_delivery_freeprice`, `m`.`cp_delivery_company`,
						case `c_is_addoption` WHEN 'Y' THEN `c_addoption_parent` else `c_pouid` end as `app_pouid`
					from
						`smart_npay` as `c`
						left join `smart_product` as `p` on (`p`.`p_code` = `c`.`c_pcode`)
						left join `smart_product_option` as `po` on (`po`.`po_uid` = `c`.`c_pouid`)
						left join `smart_product_addoption` as `pao` on (`pao`.`pao_uid` = `c`.`c_pouid`)
						left join `smart_company` as `m` on (`m`.`cp_id` = `p`.`p_cpid`)
					where
						`c`.`c_uniq` = '{$MallManageCode}'
					order by `c_rdate` asc, `c_is_addoption` desc
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

					# 장바구니 정보 저장
					foreach( $v as $sk=>$sv ){
						$arr_cart[$v['p_cpid']][$v['c_pcode']][$v['c_pouid']][$sk] = $sv;
						$arr_product_info[$v['c_pcode']][$sk] = $sv;
					}

					// 쇼핑몰  배송비 정책을 사용한다.
					if($v['cp_delivery_use'] == "N" || $SubAdminMode === false ) {

						$v['cp_delivery_price'] = $siteInfo['s_delprice'];
						$v['cp_delivery_freeprice'] = $siteInfo['s_delprice_free'];
					}
					$arr_customer[$v['p_cpid']] = array('com_delprice'=>$v['cp_delivery_price'] , 'com_delprice_free'=>$v['cp_delivery_freeprice']);

					// 배송비용 계산을 위한 입점업체별 주문금액합산 - 개별배송 , 무료배송일 경우 가격 포함 하지 않음.
					if( $v['p_shoppingPay_use']=="N" ){

						$arr_delivery[$v['p_cpid']] += $v['c_cnt'] * ($v['c_price'] + $v['c_optionprice']);
					}
					else if($v['p_shoppingPay_use']=="Y" ){

						$arr_customer[$v['p_cpid']]['app_delivery_price'] += $v['p_shoppingPay'] * $v['c_cnt'] ;
					}
					// ----- JJC : 상품별 배송비 : 2018-08-16 -----
					else if($v['p_shoppingPay_use'] == 'P') {
						$arr_customer[$v['p_cpid']]['app_delivery_price']  = ($v['p_shoppingPayPfPrice']> $arr_per_product[$v['c_pcode']]['sum']?$v['p_shoppingPayPdPrice']:0); // 상품별 배송비 설정 따름.
					}
					// ----- JJC : 상품별 배송비 : 2018-08-16 -----

					# 상품 형태 - 둘다 Y 인경우 both
					$order_type_product = $order_type_coupon = 'N';
					$order_type_product = "Y";
				}

				// --- 업체별 배송비 처리 ---
				if(sizeof(array_filter($arr_delivery)) > 0 ) {
					foreach( array_filter($arr_delivery) as $k=>$v ){
						if($arr_customer[$k]['com_delprice_free'] > 0) {
							$arr_customer[$k]['app_delivery_price'] += ($arr_customer[$k]['com_delprice_free'] > $v ? $arr_customer[$k]['com_delprice'] : 0 ); // 배송비적용
						}
						else {
							$arr_customer[$k]['app_delivery_price'] += $arr_customer[$k]['com_delprice'];//배송비적용
						}
					}
				}
				// --- 업체별 배송비 처리 ---


				$order_type = "product";
				$arr_product_sum = $arr_product = array();
				foreach($arr_cart as $crk=>$crv) {
					unset($del_chk_customer);
					foreach($crv as $k=>$v) {
						unset($option_html , $sum_price);
						foreach($v as $sk => $sv) {
							$option_tmp_name		= !$sv['c_option1'] ? "옵션없음" : $sv['c_option1']." ".$sv['c_option2']." ".$sv['c_option3'];
							$option_tmp_price		= $sv['c_price'] + $sv['c_optionprice'];
							$option_tmp_cnt			= $sv['c_cnt'];
							$option_tmp_sum_price	= $sv['c_cnt'] * ($sv['c_price'] + $sv['c_optionprice']);
							$app_point				= $sv['c_point'];

							# 상품수 , 포인트 , 상품금액
							$arr_product["cnt"] += $option_tmp_cnt;//상품수
							$sum_product_cnt += $option_tmp_cnt ;// |개별배송패치| - 상품갯수를 가져온다 : 해당 코드가 없을 시 추가
							$arr_product["point"] += $app_point ;//포인트
							$arr_product["sum"] += $option_tmp_sum_price;//상품금액
							$sum_price += $option_tmp_sum_price;//상품금액

							$delivery_price = 0;
							/*
							switch($sv['p_shoppingPay_use']){
								case "N":
									$arr_product["delivery"]+=$sv['p_shoppingPay']* $option_tmp_cnt;
									$delivery_price = $sv['p_shoppingPay'] * $option_tmp_cnt;
								break;
								case "Y":
									if($del_chk_customer <> $crk) {
										$arr_product["delivery"]+=$arr_customer[$crk]['app_delivery_price'];
										$delivery_price = $arr_customer[$crk]['app_delivery_price'];
										$del_chk_customer = $crk;
									}
								break;
							}
							*/
							if($del_chk_customer <> $crk) {
								$arr_product["delivery"]+=$arr_customer[$crk]['app_delivery_price'];
								$delivery_price = $arr_customer[$crk]['app_delivery_price'];
								$del_chk_customer = $crk;
							}

							$c_cookie = $sv['c_cookie'];
							$npay_uniq = $sv['c_uniq'];

							$product_delivery_price[$OrderDetail['ProductOrder']['MallManageCode']][$sv['c_pcode']] = $delivery_price;
							$product_add_delivery_price[$OrderDetail['ProductOrder']['MallManageCode']][$sv['c_pcode']] = 0;
						}
					}
				}
				$arr_product_sum = $arr_product;
				$price_total = $arr_product_sum['sum'] + $arr_product_sum['delivery']+$arr_product_sum['add_delivery']; // 실제결제해야할 금액
				$price_delivery = $arr_product_sum['delivery']+$arr_product_sum['add_delivery'];

				# 사용자정보
				$_oname = $_uname = $_rname = $OrderDetail['Order']['OrdererName'];
				$_oemail = '';
				$_ohtel = $OrderDetail['Order']['OrdererTel1'];
				$_paymethod = $arr_paymethod[$OrderDetail['Order']['PaymentMeans']];
				$_ohp = tel_format($_ohtel); $_ohp = explode('-',$_ohp);
				$_ohtel1 = $_uhtel1 = $_rhtel1 = $_ohp[0];
				$_ohtel2 = $_uhtel2 = $_rhtel2 = $_ohp[1];
				$_ohtel3 = $_uhtel3 = $_rhtel3 = $_ohp[2];
				$paydate = substr($OrderDetail['ProductOrder']['ShippingDueDate'],0,10);
				if(trim($OrderDetail['ProductOrder']['ShippingAddress']['BaseAddress']) != '') {

					$_rname = $OrderDetail['ProductOrder']['ShippingAddress']['Name'];
					if(trim($OrderDetail['ProductOrder']['ShippingAddress']['Tel1']) != '') {
						$_rhtel = tel_format($OrderDetail['ProductOrder']['ShippingAddress']['Tel1']); $_rhtel = explode('-',$_rhtel);
						$_rhtel1 = $_rhtel[0];
						$_rhtel2 = $_rhtel[1];
						$_rhtel3 = $_rhtel[2];
					}
					$exp_zip = explode('-', $OrderDetail['ProductOrder']['ShippingAddress']['ZipCode']);
					$_rzip1 = $exp_zip[0];
					$_rzip2 = $exp_zip[1];
					$_rzonecode = $OrderDetail['ProductOrder']['ShippingAddress']['ZipCode'];
					$_raddress = $OrderDetail['ProductOrder']['ShippingAddress']['BaseAddress'];
					$_raddress1 = $OrderDetail['ProductOrder']['ShippingAddress']['DetailedAddress'];
					$_raddress_doro = $OrderDetail['ProductOrder']['ShippingAddress']['BaseAddress'].$OrderDetail['ProductOrder']['ShippingAddress']['DetailedAddress'];
				}


				# 주문정보
				$_mid					= $OData[0]['c_cookie']; // 주문자
				$_price_real			= $OrderDetail['ProductOrder']['TotalPaymentAmount']+(is_numeric($OrderDetail['ProductOrder']['DeliveryFeeAmount'])?$OrderDetail['ProductOrder']['DeliveryFeeAmount']:0);// 실제결제해야할 금액
				$_price_total			= $OrderDetail['ProductOrder']['TotalPaymentAmount']+(is_numeric($OrderDetail['ProductOrder']['DeliveryFeeAmount'])?$OrderDetail['ProductOrder']['DeliveryFeeAmount']:0);// 구매총액?
				if($OrderDetail['Order']['GeneralPaymentAmount']) {
					$_price_real = $_price_total = $OrderDetail['Order']['GeneralPaymentAmount'];
				}
				$_price_delivery		= $price_delivery; //배송비
				$_price_supplypoint		= 0;//제공해야할 포인트
				$_price_usepoint		= 0;//포인트사용액
				$_price_coupon_member	= 0;//보너스쿠폰사용액
				$_price_coupon_product	= 0;//상품쿠폰사용액
				$_price_promotion		= 0;//프로모션코드 할인금액 LMH005
				$_price_sale_total		= 0;
				$_price_sale_total		= 0;//프로모션코드 할인금액 추가 LMH005
				$_paymethod				= $_paymethod;//결제방식
				$_paystatus				= "Y";//결제상태
				$_canceled				= "N";//결제취소상태
				$_status				= "결제확인";//주문상태 -> 결제확인 부터 시작
				$_get_tax				= 'N';	// 현금영수증
				$_paydate				= explode("-",$paydate); // 입금예정일
				$_paybankname			= $_bank; // 입금은행정보
				$_order_type			= $order_type;
				$mobile_order			= ($OrderDetail['Order']['PayLocationType']=='MOBILE'?'Y':'N');
				$row_member = _MQ("SELECT * FROM smart_individual WHERE in_id = '".$c_cookie."' ");
				$_content =  $OrderDetail['ProductOrder']['ShippingMemo'];
				$npay_order = 'Y'; // 네이버페이로 구매

				# 수취인 휴대폰 정보 패치 2016-07-15 LDD
				$_rhtel = $OrderDetail['ProductOrder']['ShippingAddress']['Tel2'];
				$_rhtel = tel_format($_rhtel); $_rhtel = explode('-',$_rhtel);
				$_rhp1 = $_rhtel[0];
				$_rhp2 = $_rhtel[1];
				$_rhp3 = $_rhtel[2];

				# order 입력
				$FindOrderData = _MQ(" select * from smart_order where npay_order = '{$npay_order}' and npay_uniq = '{$npay_uniq}' ");
				$sque = "
					insert `smart_order` set
						`o_ordernum`					= '". $_ordernum ."',
						`o_memtype`					= '". ($row_member['in_id'] ? "Y" : "N") ."',
						`o_mid`						= '". $_mid ."',
						`o_oname`						= '". $_oname ."',
						`o_otel`						= '". tel_format($_uhtel1.'-'.$_uhtel2.'-'.$_uhtel3) ."',
						`o_ohp`						= '". tel_format($_ohtel1.'-'.$_ohtel2.'-'.$_ohtel3) ."',
						`o_oemail`					= '". $_oemail ."',
						`o_rname`						= '". $_rname ."',
						`o_rtel`						= '". tel_format($_rhtel1.'-'.$_rhtel2.'-'.$_rhtel3) ."',
						`o_rhp`						= '". tel_format($_rhp1.'-'.$_rhp2.'-'.$_rhp3) ."',
						`o_rpost`						= '". $_rzip1.'-'.$_rzip2 ."',
						`o_rzonecode`					= '". $_rzonecode ."',
						`o_raddr1`					= '". $_raddress ."',
						`o_raddr2`					= '". $_raddress1 ."',
						`o_raddr_doro`				= '". $_raddress_doro ."',
						`o_content`					= '". $_content ."',
						`o_admcontent`				= '',
						`o_price_real`				= '". ($_price_real-$_price_sale_total) ."',
						`o_price_total`				= '". ($_price_real-$_price_delivery) ."',
						`o_price_delivery`			= '". $_price_delivery ."',
						`o_price_supplypoint`			= '0',
						`o_price_usepoint`			= '". $_price_usepoint."',
						`o_apply_point`				= 'N',
						`o_price_coupon_individual`	= '0',
						`o_coupon_individual_uid`		= '',
						`o_price_coupon_product`		= '0',
						`o_paymethod`					= '". $_paymethod ."',
						`o_paystatus`					= 'Y',
						`o_canceled`					= 'N',
						`o_status`					= '결제완료',
						`o_bank`						= ". $_paybankname."'',
						`o_get_tax`					= '',
						`o_deposit`					= '',
						`o_rdate`						= now(),
						`o_sendstatus`				= '배송대기',
						`o_web_mode`					= '".$mobile_order."',
						`mobile`							= '".$mobile_order."',

						`npay_order`					= '".$npay_order."',
						`npay_uniq`					= '".$npay_uniq."'
				";
				if(!$FindOrderData['o_ordernum']) _MQ_noreturn($sque);
			}

			# 주문상품정보 추가
			$v = _MQ("
				select
					`c`.*,
					`p`.`p_name`,
					`p`.`p_cpid`,
					`p`.`p_commission_type`,
					`p`.`p_sPrice`,
					`p`.`p_sPersent`,
					`p`.`p_shoppingPay_use`
				from
					`smart_npay` as `c` left join
					`smart_product` as `p` on (`p`.`p_code` = `c`.`c_pcode`)
				where
					`c_uniq` = '{$OrderDetail['ProductOrder']['MallManageCode']}' and
					`c_pcode` = '{$OrderDetail['ProductOrder']['ProductID']}' and
					`c_pouid` = '{$OrderDetail['ProductOrder']['OptionCode']}'
				order by `c_rdate` asc, `c_is_addoption` desc
			");// 선택 구매 2015-12-04 LDD

			// --- 배송비 타입 설정 ---
			$_delivery_type = "입점";
			switch($v['p_shoppingPay_use']){
				case "Y": $_delivery_type ="개별"; break;
				case "N": $_delivery_type ="입점"; break; // 일괄 추가
				case "F": $_delivery_type ="무료"; break;
			}

			$FindOrderData = _MQ(" select * from smart_order_product where npay_order_code = '{$nv['ProductOrderID']}' and npay_uniq = '{$OrderDetail['ProductOrder']['MallManageCode']}' ");
			$ssque = "
				insert `smart_order_product` set
					  `op_oordernum`			= '". $MoveOrdernum[$OrderDetail['ProductOrder']['MallManageCode']] ."'
					, `op_pcode`				= '". $v['c_pcode'] ."'
					, `op_pouid`				= '". $v['c_pouid']."'
					, `op_option1`				= '". mysql_real_escape_string($v['c_option1']) ."'
					, `op_option2`				= '". mysql_real_escape_string($v['c_option2']) ."'
					, `op_option3`				= '". mysql_real_escape_string($v['c_option3']) ."'
					, `op_add_delivery_price`	= '". (
							$arr_tmp_delivery_price[$OrderDetail['ProductOrder']['MallManageCode']]["add_dp_".$v['c_pcode']] > 0 || $v['c_is_addoption']=='Y' ?
							0 :
							$product_add_delivery_price[$OrderDetail['ProductOrder']['MallManageCode']][$v['c_pcode']]
						)."'
					, `op_delivery_price`		= '". (
							$arr_tmp_delivery_price[$OrderDetail['ProductOrder']['MallManageCode']]["dp_".$v['c_pcode']] > 0 ?
							0 :
							$product_delivery_price[$OrderDetail['ProductOrder']['MallManageCode']][$v['c_pcode']]
						) ."'
					, `op_supply_price`			= '". $v['c_supply_price'] ."'
					, `op_price`				= '". $v['c_price'] ."'
					, `op_point`				= '". $v['c_point'] ."'
					, `op_cnt`					= '". $v['c_cnt'] ."'
					, `op_sendstatus`			= '배송대기'
					, `op_rdate`				= now()
					, `op_partnerCode`			= '". $v['p_cpid']."'
					, `op_comSaleType`			= '". $v['p_commission_type'] ."'
					, `op_commission`			= '". $v['p_sPersent']."'
					, `op_pname`				= '".mysql_real_escape_string($v['p_name'])."'
					, `op_is_addoption`			= '". $v['c_is_addoption'] ."'
					, `op_addoption_parent`		= '". $v['c_addoption_parent'] ."'
					, `op_delivery_type` 		= '". $_delivery_type ."'
					, `npay_order_code`			= '{$nv['ProductOrderID']}'
					, `npay_uniq`				= '{$OrderDetail['ProductOrder']['MallManageCode']}'
					, `npay_status`				= 'PAYED'
					, `npay_point`				= '{$OrderDetail['Order']['NaverMileagePaymentAmount']}'
					, `npay_point2`				= '{$OrderDetail['Order']['CheckoutAccumulationPaymentAmount']}'
			";
			if(!$FindOrderData['op_oordernum']) _MQ_noreturn($ssque);

			# 배송비 상품당 1회 적용
			if($product_delivery_price[$OrderDetail['ProductOrder']['MallManageCode']][$v['c_pcode']] > 0 )
				$arr_tmp_delivery_price[$OrderDetail['ProductOrder']['MallManageCode']]["dp_".$v['c_pcode']] ++;
			if($product_add_delivery_price[$OrderDetail['ProductOrder']['MallManageCode']][$v['c_pcode']] > 0 )
				$arr_tmp_delivery_price[$OrderDetail['ProductOrder']['MallManageCode']]["add_dp_".$v['c_pcode']] ++;

			# 임시주문정보 삭제
			_MQ_noreturn(" delete from smart_npay where c_uniq = '{$OrderDetail['ProductOrder']['MallManageCode']}' and `c_pcode` = '{$OrderDetail['ProductOrder']['ProductID']}' and `c_pouid` = '{$OrderDetail['ProductOrder']['OptionCode']}' ");

			# 쿠폰상품은 티켓을 발행한다.
			$_ordernum = $MoveOrdernum[$OrderDetail['ProductOrder']['MallManageCode']];
			include_once(OD_PROGRAM_ROOT."/shop.order.couponadd_pro.php");

			# 주문상태 업데이트
			order_status_update($_ordernum);
		}

		# 모든 처리가 에러없이 처리된경우(Run_{TYPE} 공통)
		$this->MSG('RESULT=TRUE', '200');
	}

	# 발송처리 2016-05-27 LDD
	protected function Run_DISPATCHED($ChangeOrder) {

		foreach($ChangeOrder as $nk=>$nv) {

			# 필요정보 변수화
			$Npoid = $nv['ProductOrderID']; // [네이버] PONO900000000008
			$OrderDetail = $this->GetProductOrderInfoList($Npoid); // 데이터 호출
			# 주문정보를 가져온다.
			$OData = _MQ("
				select
					*
				from
					`smart_order_product` as `op` left join
					`smart_order` as `o` on(`op`.`op_oordernum` = `o`.`o_ordernum`)
				where
					`o`.`npay_order` = 'Y' and
					`op`.`npay_order_code` = '{$Npoid}'
			");
			if(count($OData) <= 0) continue; // 스팩상 그냥 승인 처리
			$_ordernum = $OData['op_oordernum']; // o_ordernum

			# 택배사 보정 작업
			if($OrderDetail['Delivery']['DeliveryMethod'] == 'VISIT_RECEIPT') {
				$OrderDetail['Delivery']['DeliveryCompany'] = '방문수령';
				$OrderDetail['Delivery']['TrackingNumber'] = '방문수령';
			}
			if($OrderDetail['Delivery']['DeliveryMethod'] == 'DIRECT_DELIVERY') {
				$OrderDetail['Delivery']['DeliveryCompany'] = '직접배송';
				$OrderDetail['Delivery']['TrackingNumber'] = '직접배송';
			}
			if($OrderDetail['Delivery']['DeliveryMethod'] == 'QUICK_SVC') {
				$OrderDetail['Delivery']['DeliveryCompany'] = '퀵서비스';
				$OrderDetail['Delivery']['TrackingNumber'] = '퀵서비스';
			}

			# 택배명->솔루션 텍배
			//$OrderDetail['Delivery']['DeliveryCompany'] = 'EPOST'; // 테스트
			$DeliveryCompany = $this->Courier($OrderDetail['Delivery']['DeliveryCompany']);

			# 운송장번호
			//$OrderDetail['Delivery']['TrackingNumber'] = '123456789'; // 테스트
			$TrackingNumber = $OrderDetail['Delivery']['TrackingNumber'];

			# 발송을 위한 정보가 모두 있는지 확인
			if(trim($DeliveryCompany) == '') continue;
			if(trim($TrackingNumber) == '') continue;

			# 배송처리
			_MQ_noreturn("
				update
					`smart_order_product`
				set
					`op_sendcompany` = '{$DeliveryCompany}',
					`op_sendnum` = '{$TrackingNumber}',
					`op_senddate` = now(),
					`op_sendstatus` = '배송완료',
					`npay_status` = 'DISPATCHED'
				where
					`op_uid` = '".$OData['op_uid']."'
			");

			# 주문상태 업데이트
			order_status_update($_ordernum);
		}

		# 모든 처리가 에러없이 처리된경우(Run_{TYPE} 공통)
		$this->MSG('RESULT=TRUE', '200');
	}

	# 주문취소처리 2016-05-27 LDD (부분취소 로직을 따름)
	protected function Run_CANCELED($ChangeOrder) {

		$_result_msg = '네이버페이 취소';
		foreach($ChangeOrder as $nk=>$nv) {

			# 필요정보 변수화
			$Npoid = $nv['ProductOrderID']; // [네이버] PONO900000000008
			$OrderDetail = $this->GetProductOrderInfoList($Npoid); // 데이터 호출

			# 주문정보를 가져온다.
			$ordr = _MQ("
				select
					*
				from
					`smart_order_product` as `op` left join
					`smart_order` as `o` on(`op`.`op_oordernum` = `o`.`o_ordernum`)
				where
					`o`.`npay_order` = 'Y' and
					`op`.`npay_order_code` = '{$Npoid}' and
					`o`.`o_canceled` = 'N'
			");
			if(count($ordr) <= 0) continue; // 스팩상 그냥 승인 처리
			$_ordernum = $ordr['op_oordernum']; // op_oordernum
			$_uid = $ordr['op_uid'];


			// 2018-11-19 SSJ :: 단일 상품 재고 증가 및 판매량 차감 :: $_ordernum , $_uid
			include(OD_PROGRAM_ROOT.'/shop.order.salecntdel_part.php');
			_MQ_noreturn(" update `smart_order_product` set
				`op_cancel` = 'Y',
				`op_cancel_returnmsg` = '{$_result_msg}',
				`op_cancel_tid` = '',
				`op_cancel_cdate` = now(),
				`npay_status` = 'CANCELED'
				where `op_oordernum` = '{$_ordernum}' and `op_uid` = '{$_uid}'
			");

			# 추가옵션 취소처리
			$add_res = _MQ_assoc(" select * from `smart_order_product` where `op_is_addoption` = 'Y' and `op_addoption_parent` = '{$ordr['op_pouid']}' and `op_oordernum` = '{$ordr['op_oordernum']}' ");
			if(count($add_res) > 0) {
				foreach($add_res as $adk=>$adv) {

					_MQ_noreturn(" update `smart_order_product` set
						`op_cancel` = 'Y',
						`op_cancel_returnmsg` = '{$_result_msg}',
						`op_cancel_tid` = '',
						`op_cancel_cdate` = now(),
						`npay_status` = 'CANCELED'
						where `op_oordernum` = '{$adv['op_oordernum']}' and `op_uid` = '{$adv['op_uid']}'
					");
				}
			}

			# 마지막 부분취소일 경우 주문 전체 취소
			$tmp = _MQ(" select count(*) as `cnt` from `smart_order_product` where `op_cancel` != 'Y' and `op_oordernum` = '{$_ordernum}' ");
			if($tmp['cnt'] == 0) {

				include(OD_PROGRAM_ROOT.'/shop.order.pointdel_pro.php');
				_MQ_noreturn(" update `smart_order` set `o_canceled` = 'Y' where `o_ordernum` = '{$_ordernum}' ");
			}

			# 주문발송 상태 변경
			order_status_update($_ordernum);
		}

		# 모든 처리가 에러없이 처리된경우(Run_{TYPE} 공통)
		$this->MSG('RESULT=TRUE', '200');
	}



	# ~~~~~~~~~~~~~~~~~~~~~~~~ 조작계열 ~~~~~~~~~~~~~~~~~~~~~~~~ #
	# 상태별 동작처리(관리자 -> 취소 or 발송) 2016-05-27 LDD
	public function OrderInfoChange($type='express', $Data=array()) {

		# 실모드라면 외부 접근 차단.
		if($this->SyncMode != 'test' && !$_COOKIE["AuthAdmin"] && !$_COOKIE["AuthCompany"] ) $this->MSG('외부접근이 허용되지 않는 페이지 입니다.', 401);

		# 실제 함수를 위하여 타입을 변경
		switch($type) {
			case 'cancel':
				$type = 'Cancel';
			break;
			case 'express':
				$type = 'Express';
			break;
		}

		# 처리 데이터 항목이 넘어오지 않았다면
		if(!is_array($Data)) $this->MSG('처리 항목 데이터 없음', 406);

		# 동작별 액션을 취한다. (클래스 함수가 없는경우 미지원 기능으로 반환)
		if(method_exists($this, 'Order_'.$type) === true) return $this->{'Order_'.$type}($Data);
		else $this->MSG('미지원 TYPE (none exists class Function {'.$type.'})', '406'); // 406(허용되지 않음)
	}

	# 발송처리 ---------------------------------------> 작업필요 (처리분기 변경)
	protected function Order_Express($Data) {

		# 넘어온 배열을 일반 변수처림 사용하기 위한설정
		@extract($Data);

		# 기본변수 만듦
		if(trim($npay_code) == '') {

			$npay_codes = array();
			foreach($op_uid as $k=>$v) {

				$odp = _MQ(" select `npay_order_code` from `smart_order_product` where `op_uid` = '{$v}' ");
				if(trim($odp['npay_order_code']) != '') {

					foreach(explode(',', $odp['npay_order_code']) as $kk=>$vv) {
						$npay_codes[$vv]['code'] = $vv;
						$npay_codes[$vv]['expressname'] = $expressname[0];
						$npay_codes[$vv]['expressnum'] = $expressnum[0];
					}
				}
			}
		}
		else {

			$npay_codes = array();
			foreach(explode(',', $npay_code) as $kk=>$vv) {
				$npay_codes[$vv]['code'] = $vv;
				$npay_codes[$vv]['expressname'] = $n_name;
				$npay_codes[$vv]['expressnum'] = $n_num;
			}
		}

		# NPay로 발송처리 동작 값보냄
		foreach($npay_codes as $k=>$v) {

			$Result = $this->ShipProductOrder($v['code'], $v['expressname'], $v['expressnum']);
		}

		return $Result;
	}
	# 네이버페이로 발송처리
	public function ShipProductOrder($code, $expressname, $expressnum, $service = 'MallService41') {

		# 네이버페이에서 요청 하는 배송사 코드
		$NpayDeliveryCode = array(
				'CJ대한통운'=>'CJGLS',
				'로젠택배'=>'KGB',
				'KG로지스'=>'DONGBU',
				'우체국택배'=>'EPOST',
				'우편등기'=>'REGISTPOST',
				'한진택배'=>'HANJIN',
				'롯데택배'=>'HYUNDAI',
				'GTX로지스'=>'INNOGIS',
				'대신택배'=>'DAESIN',
				'일양로지스'=>'ILYANG',
				'경동택배'=>'KDEXP',
				'천일택배'=>'CHUNIL',
				'기타 택배'=>'CH1',
				'합동택배'=>'HDEXP',
				'편의점택배'=>'CVSNET',
				'DHL'=>'DHL',
				'FEDEX'=>'FEDEX',
				'GSMNTON'=>'GSMNTON',
				'WarpEx'=>'WARPEX',
				'WIZWA'=>'WIZWA',
				'EMS'=>'EMS',
				'DHL(독일)'=>'DHLDE',
				'ACI'=>'ACIEXPRESS',
				'EZUSA'=>'EZUSA',
				'범한판토스'=>'PANTOS',
				'UPS'=>'UPS',
				'CJ대한통운(국제택배)'=>'KOREXG',
				'TNT'=>'TNT',
				'성원글로벌'=>'SWGEXP',
				'대운글로벌'=>'DAEWOON',
				'USPS'=>'USPS',
				'i-parcel'=>'IPARCEL',
				'건영택배'=>'KUNYOUNG',
				'한의사랑택배'=>'HPL',
				'다드림'=>'DADREAM',
				'SLX택배'=>'SLX',
				'호남택배'=>'HONAM',
				'GSI익스프레스'=>'GSIEXPRESS',
				'직접배송'=>'DIRECT_DELIVERY',
				'방문수령'=>'VISIT_RECEIPT'
		);
		if($NpayDeliveryCode[$expressname] == 'DIRECT_DELIVERY') { // 자체배송

			$NpayDeliveryCode[$expressname] = '';
			$expressnum = '';
			$DeliveryMethodCode = 'DIRECT_DELIVERY'; // 직접배송
		}
		else if($NpayDeliveryCode[$expressname] == 'VISIT_RECEIPT') { // 방문수령

			$NpayDeliveryCode[$expressname] = '';
			$expressnum = '';
			$DeliveryMethodCode = 'VISIT_RECEIPT'; // 직접배송
		}
		else {
			$DeliveryMethodCode = 'DELIVERY'; // 원배송, 재배송
		}

		# 처리 타임 스탬프 만듦
		$timestamp = date("Y-m-d\TH:i:s",strtotime("-9hour"));
		$microtime = substr(microtime(),2,2);
		$timestamp = $timestamp.'.'.$microtime.'Z';

		$xml = '<mall:ProductOrderID>'.$code.'</mall:ProductOrderID>'.PHP_EOL; // 주문번호
		$xml .= '<mall:DeliveryMethodCode>'.$DeliveryMethodCode.'</mall:DeliveryMethodCode>'.PHP_EOL; // 배송방법코드
		if(trim($NpayDeliveryCode[$expressname]) != '') $xml .= '<mall:DeliveryCompanyCode>'.$NpayDeliveryCode[$expressname].'</mall:DeliveryCompanyCode>'.PHP_EOL; // 택배사코드
		else $xml .= '<mall:DeliveryCompanyCode/>'.PHP_EOL;
		if(trim($NpayDeliveryCode[$expressname]) != '') $xml .= '<mall:TrackingNumber>'.$expressnum.'</mall:TrackingNumber>'.PHP_EOL; // 운송장번호
		else $xml .= '<mall:TrackingNumber/>'.PHP_EOL;
		$xml .= '<mall:DispatchDate>'.$timestamp.'</mall:DispatchDate>'.PHP_EOL;

		$operation = 'ShipProductOrder';
		$Dsxl = $this->GetDsxl($operation, $xml);
		$request_body = $Dsxl['xml'];
		$response = $this->Soap($operation, $request_body);
		$response = $this->xml2array($response);
		return $response; // 전송결과 출력

		# NPay 상품정보 호출
		//$Norder = $this->GetProductOrderInfoList($code);
		//ViewArr($Norder);
	}

	# 취소처리 ---------------------------------------> 작업필요 (처리분기 변경)
	protected function Order_Cancel($Data) {

		# 넘어온 배열을 일반 변수처림 사용하기 위한설정
		@extract($Data);
		if(trim($ordr['npay_order_code']) == '') return false;

		foreach(explode(',', $ordr['npay_order_code']) as $k=>$v) {
			$this->CancelSale($v);
		}
		//exit;
		//return $this->CancelSale($ordr['npay_order_code']);
	}
	# 네이버페이로 취소요청
	public function CancelSale($code, $service = 'MallService41') {

		# 초기 설정
		$issue = 'INTENT_CHANGED'; // 취소사유는 "구매 의사 취소"로 고정

		# 요청 등록
		$xml = '<mall:ProductOrderID>'.$code.'</mall:ProductOrderID>'.PHP_EOL; // 주문번호
		$xml .= '<mall:CancelReasonCode>'.$issue.'</mall:CancelReasonCode>';

		$operation = 'CancelSale';
		$Dsxl = $this->GetDsxl($operation, $xml);
		$request_body = $Dsxl['xml'];
		$response = $this->Soap($operation, $request_body);
		$response = $this->xml2array($response);
		//ViewArr($response); // 전송결과 출력

		# NPay 상품정보 호출
		//$Norder = $this->GetProductOrderInfoList($code);
		//ViewArr($Norder);
	}

	# 발주처리
	protected function Order_Place($Data) {

		# 넘어온 배열을 일반 변수처림 사용하기 위한설정
		@extract($Data);
		foreach(explode(',', $code) as $k=>$v) {

			_MQ_noreturn(" update `smart_order_product` set `npay_status` = 'PLACE' where `npay_order_code` = '{$v}' "); // 상태를 발주처리로 변경
			$this->PlaceProductOrder($v); // 코드별 발주처리
		}
	}
	public function PlaceProductOrder($code, $service = 'MallService41') {

		# 요청 등록
		$xml = '<mall:ProductOrderID>'.$code.'</mall:ProductOrderID>'.PHP_EOL; // 주문번호

		$operation = 'PlaceProductOrder';
		$Dsxl = $this->GetDsxl($operation, $xml);
		$request_body = $Dsxl['xml'];
		$response = $this->Soap($operation, $request_body);
		$response = $this->xml2array($response);
	}
}

# 클래스변수는 생성 해서 나가도록 처리
$NSync = new Sync($Npay, $siteInfo['npay_lisense'], $siteInfo['npay_secret'], $siteInfo['npay_sync_mode']);