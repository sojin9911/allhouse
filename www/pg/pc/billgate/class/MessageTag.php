<?php 
class MessageTag
{
	var $USER_ID                      = "0001" ;  // 고객 ID
	var $USER_NAME                    = "0002" ;  // 고객 이름
	var $ITEM_CODE                    = "0003" ;  // 상품 코드
	var $ITEM_NAME                    = "0004" ;  // 상품 이름
	var $USER_IP                      = "0005" ;  // 고객 IP
	var $USER_EMAIL                   = "0006" ;  // 고객 Email
	var $MOBILE_NUMBER                = "0007" ;  // 휴대폰 번호
	var $PIN_NUMBER                   = "0008" ;  // 핀 번호
	var $PASSWORD                     = "0009" ;  // 패스워드
	var $BUSINESS_NUMBER              = "0010" ;  // 사업자 등록 번호
	var $DEAL_DATE                    = "0011" ;  // 거래 일시
	var $DEAL_AMOUNT                  = "0012" ;  // 거래 금액(공급가액)
	var $VAT                          = "0013" ;  // 부가세
	var $SERVICE_CHARGE               = "0014" ;  // 봉사료
	var $USING_TYPE                   = "0015" ;  // 거래자 구분
	var $DEAL_TYPE                    = "0016" ;  // 거래 구분
	var $IDENTIFIER                   = "0017" ;  // 신분 확인
	var $CASH_ID                      = "0018" ;  // 캐쉬 ID
	var $CASH_PASSWORD                = "0019" ;  // 캐쉬 패스워드
	var $PIN_PASSWORD                 = "0020" ;  // 핀 패스워드
	var $MALL_USER_ID                 = "0021" ;  // 가맹점 고객 ID
	var $AFFILIATER_REGISTER_ID       = "0022" ;  // 제휴사 등록 아이디
	var $ITEM_KIND                    = "0023" ;  // 상품 종류
	var $NOTIFY_TYPE                  = "0024" ;  // 통지 형태
	var $ORDER_ID                     = "0025" ;  // 승인 요청시 주문 번호
	var $TERMINAL_ID                  = "0026" ;  // 터미널 ID
	var $CURRENCY                     = "0030" ;  // 통화 구분	          
	var $QUOTA                        = "0031" ;  // 할부 개월 수 	    
	var $EXPIRE_DATE                  = "0032" ;  // 유효년일	        
	var $CVC2                         = "0033" ;  // CVC2	            
	var $CARD_COMPANY_CODE            = "0034" ;  // 카드사 코드 	    
	var $CERT_TYPE                    = "0035" ;  // ISP/MIP 구분	    
	var $INTEREST_TYPE                = "0036" ;  // 무이자 할부 구분	
	var $MIX_TYPE                     = "0037" ;  // 복합결제 여부	    
	var $RECEIVER_NAME                = "0038" ;  // 수령자 성명	    
	var $RECEIVER_ADDRESS             = "0039" ;  // 수령자 배송지      
	var $MPI_CAVV                     = "0040" ;  // MPI CAVV           
	var $MPI_XID                      = "0041" ;  // MPI X-ID           
	var $MPI_ECI                      = "0042" ;  // MPI EC-I           
	var $SESSION_KEY                  = "0043" ;  // SessionKey         
	var $ENCRYPT_DATA                 = "0044" ;  // Encrypted Data     
	var $IC_DATA_TYPE                 = "0045" ;  // IC DATA 형태       
	var $IC_DATA                      = "0046" ;  // IC DATA            
	var $SIGN_TYPE                    = "0047" ;  // 서명유무           
	var $SIGN_DATA                    = "0048" ;  // Sign DATA          
	var $ANI                          = "0049" ;  // ANI
	var $DNIS                         = "0050" ;  // DNIS
	var $WIRE_NUMBER                  = "0051" ;  // 유선번호
	var $AGREE_MONTHS                 = "0052" ;  // 동의기간
	var $SEARCH_START_DATE            = "0053" ;  // 검색 시작일자
	var $SEARCH_END_DATE              = "0054" ;  // 검색 끝일자
	var $FILE_NAME                    = "0055" ;  // 파일명
	var $FILE_SIZE                    = "0056" ;  // 파일 크기
	var $FILE_DATA                    = "0057" ;  // 파일 데이타
	var $FILE_SEQ                     = "0058" ;  // 파일 Sequence

	var $MOBILE_COMPANY_CODE          = "0059" ;  // 이동통신사 코드
	var $RESPONSE_RETURN_URL          = "0060" ;  // 정상응답 리턴 URL
	var $RESPONSE_FAIL_URL            = "0061" ;  // 실패응답 리턴URL

	var $BANK_ID                      = "0062" ;  // 은행아이디
	var $ACCOUNT_NAME                 = "0063" ;  // 계좌명
	var $COMPANY_NAME                 = "0064" ;  // 이용기관명
	var $REFUND_FLAG                  = "0065" ;  // 환불 구분값
	var $TRANSFER_FLAG                = "0066" ;  // 거래 구분값
	var $FEE                          = "0067" ;  // 수수료
	var $CPCODE                       = "0068" ;  // CPCODE
	var $SOCIAL_NUMBER                = "0069" ;  // 주민번호
	var $OPCODE                       = "0070" ;  // OPCODE
	var $CRYPTO_SOCIAL_NUMBER         = "0071" ;  // 암호화값
	var $CRYPTO_ANI                   = "0072" ;  // 암호화값
	var $CRYPTO_CASH_ID               = "0073" ;  // 암호화값
	var $AFFILIATER_CODE              = "0074" ;  // 제휴사 코드
	var $EMAIL_TEMPLATE_CODE          = "0075" ;  // 메일 템플릿 코드
	var $CALL_CENTER_NEMBER			  = "0076" ;  // 가맹점 콜센터 전화번호 

	var $BANK_CUSTOMER_CODE			  = "0077" ;  // 가맹점 은행 기관코드
	var $STATUS_CODE				  = "0078" ;  // 상태코드
	var $MULTI_BILL_ACCOUNT_CODE	  = "0079" ;  // 가상계좌 다계좌 정산코드
	var $ACCOUNT_NUMBER				  = "0080" ;  // 가상계좌 번호
	var $ACCOUNT_ID					  = "0081" ;  // 가상계좌 고유번호
	var $REQUIRE_TYPE				  = "0082" ;  // 수납필수여부 

	var $ADSL_ID					  = "0083" ;  // 메가패스 ID
	var $ADSL_BALANCE				  = "0084" ;  // ADSL 잔액
	var $ARS_BALANCE				  = "0085" ;  // ARS 잔액
	var $ARS_AUTH_AMOUNT			  = "0086" ;  // ARS 결제 금액
	var $ADSL_AUTH_AMOUNT			  = "0087" ;  // ADSL 결제 금액
	var $MAX_DATE					  = "0088" ;  // MAX DATE
	var $NOW_DATE					  = "0089" ;  // NOW DATE
	var $RANDOM_CERT_NUMBER           = "0090" ;  // 랜덤 인증 번호
	var $SERVICE_TYPE                 = "0091" ;  // 서비스타입
	var $SERVICE_TYPE_DETAIL          = "0092" ;  // 서비스타입 상세

	var $BANK_CODE                    = "0093" ;  // 은행코드
	var $PROCESS_DATE                 = "0094" ;  // 처리일자
	var $CPCODE_PASSWORD              = "0095" ;  // CPCODE 패스워드
	var $PROTOCOL_NUMBER              = "0096" ;  // 전문번호
	var $PRE_PROTOCOL_NUMBER          = "0097" ;  // 이전 전문번호
	var $TRANSFER_DATE                = "0098" ;  // 전송일자
	var $TRANSFER_TIME                = "0099" ;  // 전송시간
	var $SERVICE_KIND_CODE            = "0100" ;  // 서비스 구분 코드
	var $SERVICE_TRANSACTION_ID       = "0101" ;  // 서비스 TRANSACTION ID
	var $IDENTIFIER_TYPE              = "0102" ;  // 신분확인 값 구분 코드 (01: 주민번호, 02:휴대폰, 03: 현금영수증카드번호, 04:사업자번호)
	var $PRE_TRANSFER_DATE            = "0103" ;  // 이전 전송일자
	var $AGENT_NAME                   = "0104" ;  // 가맹점 명

	var $INPUT_BANK_CODE              = "0105" ;  // 입금 은행코드 
	var $INPUT_ACCOUNT_NUMBER         = "0106" ;  // 입금 계좌번호
	var $INPUT_ACCOUNT_NAME           = "0107" ;  // 입금 계좌 예금주명
	var $INPUT_ACCOUNT_PASSWORD       = "0108" ;  // 입금 계좌 비밀번호
	var $OUTPUT_BANK_CODE             = "0109" ;  // 출금 은행코드 
	var $OUTPUT_ACCOUNT_NUMBER        = "0110" ;  // 출금 계좌번호
	var $OUTPUT_ACCOUNT_NAME          = "0111" ;  // 출금 계좌 예금주명
	var $OUTPUT_ACCOUNT_PASSWORD      = "0112" ;  // 출금 계좌 비밀번호
	var $OUTPUT_ACCOUNT_SOCIAL_NUMBER = "0113" ;  // 출금 계좌 계좌실명번호
	var $INPUT_ACCOUNT_PRINT          = "0114" ;  // 입금 계좌 인자
	var $OUTPUT_ACCOUNT_PRINT         = "0115" ;  // 출금 계좌 인자

	var $TRANSACTION_ID               = "1001" ;  // 거래 번호
	var $RESPONSE_CODE                = "1002" ;  // 응답 코드
	var $RESPONSE_MESSAGE             = "1003" ;  // 응답 메시지
	var $AUTH_NUMBER                  = "1004" ;  // 승인 번호
	var $AUTH_DATE                    = "1005" ;  // 승인 일시
	var $BALANCE                      = "1006" ;  // 잔액
	var $AUTH_AMOUNT                  = "1007" ;  // 결제 금액
	var $CANCEL_DATE                  = "1008" ;  // 취소 일시
	var $DETAIL_RESPONSE_CODE         = "1009" ;  // 상세 응답 코드
	var $DETAIL_RESPONSE_MESSAGE      = "1010" ;  // 상세 응답 메시지
	var $REQUEST_DATE                 = "1011" ;  // 거래 요청 일자
	var $PRE_TRANSACTION_ID           = "1012" ;  // 이전 거래 번호
	var $AFFILIATER_RESPONSE_CODE     = "1013" ;  // 제휴사 에러코드 
	var $EXPIRATION_DATE              = "1014" ;  // 유효 기간
	var $CARD_TYPE                    = "1015" ;  // 카드 타입(도토리 상품권 : 충전형, 고정형, slip 형, online)
	var $ISSUE_DATE                   = "1016" ;  // 발행 일자
	var $CERT_NUMBER                  = "1017" ;  // 인증 번호
	var $USER_KEY                     = "1018" ;  // 사용자 키
	var $AFFILIATER_TRANSACTION_ID    = "1019" ;  // 제휴사 거래 번호
	var $AGENT_NUMBER                 = "1020" ;  // 가맹점 번호
	var $ISSUE_COMPANY_CODE           = "1021" ;  // 카드발급사코드
	var $BUY_COMPANY_CODE             = "1022" ;  // 카드매입사코드

	var $RESULT                       = "1023" ;  // result
	var $ERROR_CODE                   = "1024" ;  // errorCode
	var $CALLER_ID                    = "1025" ;  // caller_id
	var $SERVICE_ID                   = "1026" ;  // service_id
	var $ID_BALANCE                   = "1027" ;  // 아이디 잔액

	var $TRANSFER_COUNT               = "1028" ;  // 이체건수
	var $TRANSFER_AMOUNT              = "1029" ;  // 이체금액
	var $REFUND_COUNT                 = "1030" ;  // 지급이체건수
	var $REFUND_AMOUNT                = "1031" ;  // 지급이체금액

	var $CANCEL_AMOUNT                = "1033" ;  // 취소금액

	var $RESERVED01                   = "9001" ;  // 임시 필드
	var $RESERVED02                   = "9002" ;  // 임시 필드
	var $RESERVED03                   = "9003" ;  // 임시 필드	
}
?>