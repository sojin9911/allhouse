<?

	include "inc.php";

	switch($mode){

		case "sms_info":

			// SMS 설정 정보 저장
			_MQ_noreturn("update smart_setup set s_smsid = '".$_smsid."', s_smspw = '".enc_array('e', array('s_smsid'=>$_smsid, 's_smspw'=>$_smspw))."'");
			echo "OK";

		break;

		case "rollback":

			$uid = $ma=='a'?'admin_'.$uid:$uid;

			$rollback = array(
				// 회원가입시
				"join"					=> "[{사이트명}] 회원가입을 환영합니다. 즐거운 쇼핑이 되도록 노력하겠습니다.",
				"admin_join"			=> "[{사이트명}] {회원명}님이 회원가입을 하였습니다.",

				'temp_password'			=> '[{사이트명}] {회원명}님 임시 비밀번호를 안내드립니다. 임시 비밀번호: {임시비밀번호}',
				'admin_temp_password'	=> '[{사이트명}] {회원명}님께서 임시비밀번호를 발급받으셨습니다.',

				// 무통장주문시
				"order_online"			=> "[{사이트명}] 고객님 주문이 완료되었습니다. 입금이 확인되면 상품이 배송됩니다. 감사합니다. [주문번호: {주문번호}]",
				"admin_order_online"	=> "[{사이트명}] 무통장주문이 확인되었습니다. 관리자페이지에서 확인해주세요. [주문번호: {주문번호}]",

				// 가상계좌주문시
				"order_virtual"			=> "[{사이트명}] 고객님 주문이 완료되었습니다. 입금이 확인되면 상품이 배송됩니다. 감사합니다. [주문번호: {주문번호}]",
				"admin_order_virtual"	=> "[{사이트명}] 가상계좌주문이 확인되었습니다. 관리자페이지에서 확인해주세요. [주문번호: {주문번호}]",

				// 결제완료시
				"order_pay"				=> "[{사이트명}] 고객님의 주문이 완료되었습니다. [주문번호: {주문번호}] 주중 배송기준에 따라 상품이 배송됩니다. 감사합니다.",
				"admin_order_pay"		=> "[{사이트명}] 주문결제가 완료되었습니다. 관리자페이지에서 확인해 주십시오. [주문번호: {주문번호}]",

				// 입금확인시
				"online_pay"			=> "[{사이트명}] 고객님의 입금이 확인되었습니다. [주문번호: {주문번호}] 주중 배송기준에 따라 상품이 배송됩니다. 감사합니다.",
				"admin_online_pay"		=> "[{사이트명}] 주문의 입금이 확인되었습니다. 관리자페이지에서 확인해 주십시오. [주문번호: {주문번호}]",

				// 상품배송시
				"delivery"				=> "[{사이트명}] 고객님이 주문하신 상품이 발송되었습니다. 마이페이지에서 배송상황을 확인하실 수 있습니다. 감사합니다. [주문번호: {주문번호}]",
				"admin_delivery"		=> "[{사이트명}] 상품이 발송되었습니다. [주문번호: {주문번호}]",

				// 주문취소시
				"order_cancel"			=> "[{사이트명}] 주문이 정상적으로 취소되었습니다. [주문번호: {주문번호}]",
				"admin_order_cancel"	=> "[{사이트명}] 주문이 정상적으로 취소되었습니다. [주문번호: {주문번호}]",

				// 부분취소시
				"order_cancel_part"         => "[{사이트명}] 주문하신 상품 중 일부 상품의 주문이 취소되었습니다. [주문번호: {주문번호}] [취소된상품: {주문상품명}]",
				"admin_order_cancel_part"   => "[{사이트명}] 주문하신 상품 중 일부 상품의 주문이 취소되었습니다. [주문번호: {주문번호}] [취소된상품: {주문상품명}]",


				// 문의접수시
				"request"				=> "[{사이트명}] 고객님 온라인문의가 접수되었습니다. 빠른 시간내에 문의에 대한 답변을 드리겠습니다. 감사합니다.",
				"admin_request"			=> "[{사이트명}] {회원명} 님께서 온라인문의를 접수하셨습니다. 관리자페이지에서 답변을 등록해 주십시오."

                // 2019-04-09 SSJ :: 상품후기 접수 시
                ,"product_review"               => "[{사이트명}] 고객님 상품후기가 접수되었습니다. [상품: {후기(문의)상품명}]",
                "admin_product_review"          => "[{사이트명}] {회원명} 님께서 상품후기를 등록하셨습니다. [상품: {후기(문의)상품명}]"

                // 2019-04-09 SSJ :: 상품문의 접수 시
                ,"product_talk"             => "[{사이트명}] 고객님 상품문의가 접수되었습니다. 빠른 시간내에 문의에 대한 답변을 드리겠습니다. 감사합니다. [상품: {후기(문의)상품명}]",
                "admin_product_talk"            => "[{사이트명}] {회원명} 님께서 상품문의를 접수하셨습니다. 관리자페이지에서 답변을 등록해 주십시오. [상품: {후기(문의)상품명}]"

				// 매2년마다 수신동의
				, "2year_opt"			=> "[{사이트명}] {회원명}님.
수신동의 후 2년이 경과하였습니다.

정보통신망법 제50조제8항 및 동법 시행령 제62조의3은 최초 동의한 날로부터 매2년마다 하도록 규정하고 있습니다.

이에 따라 수신동의 받은 날부터 매 2년 마다 수신동의 여부를 재확인 해야 합니다.

사이트에 접속하시어 로그인 하신 후 마이페이지 > 정보수정을 통해 이메일 및 SMS에 대한 수신여부를 확인해주시기 바랍니다.

본 문자는 수신동의하신지 2년이 지난 회원중 SMS 수신에 동의 하신 회원에게만 발송이 됩니다.

감사합니다."
				, "admin_2year_opt" => ""

			);

			echo $rollback[$uid];

		break;

		case "load":

			// 회원에게 발송 정보
			$r = _MQ_assoc("select * from smart_sms_set where ss_uid = '".$type."'");
			foreach($r as $k => $v) {
				$m_name = $arr_sms_text_type[$type];
				$m_uid = $v[ss_uid];
				$m_status = $v[ss_status];
				$m_text = $v[ss_text];
				$m_title = $v[ss_title];
				$m_file = $v[ss_file];

				$mk_name = $arr_sms_text_type[$type];
				$mk_uid = $v['ss_uid'];
				$mk_status = $v['kakao_status'];
				$mk_knum = $v['kakao_templet_num'];
				$mk_kadd1 = $v['kakao_add1'];
				$mk_kadd2 = $v['kakao_add2'];
				$mk_kadd3 = $v['kakao_add3'];
				$mk_kadd4 = $v['kakao_add4'];
				$mk_kadd5 = $v['kakao_add5'];
				$mk_kadd6 = $v['kakao_add6'];
				$mk_kadd7 = $v['kakao_add7'];
				$mk_kadd8 = $v['kakao_add8'];
			}

			// 관리자에게 발송 정보
			$r = _MQ_assoc("select * from smart_sms_set where ss_uid = 'admin_".$type."'");
			foreach($r as $k => $v) {
				$a_name = $arr_sms_text_type[$type];
				$a_uid = $v[ss_uid];
				$a_status = $v[ss_status];
				$a_text = $v[ss_text];
				$a_title = $v[ss_title];
				$a_file = $v[ss_file];

				$ak_name = $arr_sms_text_type[$type];
				$ak_uid = $v['ss_uid'];
				$ak_status = $v['kakao_status'];
				$ak_knum = $v['kakao_templet_num'];
				$ak_kadd1 = $v['kakao_add1'];
				$ak_kadd2 = $v['kakao_add2'];
				$ak_kadd3 = $v['kakao_add3'];
				$ak_kadd4 = $v['kakao_add4'];
				$ak_kadd5 = $v['kakao_add5'];
				$ak_kadd6 = $v['kakao_add6'];
				$ak_kadd7 = $v['kakao_add7'];
				$ak_kadd8 = $v['kakao_add8'];
			}


			$array = array(
				"member" =>
					array(
						"_name"		=>	$m_name,
						"_status"	=>	$m_status,
						"_uid"		=>	$m_uid,
						"_text"		=>	$m_text,
						"_title"	=>	$m_title,
						"_file"		=>	$m_file
				),
				"admin" =>
					array(
						"_name"		=>	$a_name,
						"_status"	=>	$a_status,
						"_uid"		=>	$a_uid,
						"_text"		=>	$a_text,
						"_title"	=>	$a_title,
						"_file"		=>	$a_file
				),

				'kakao_member' => array(
					  '_name' => $mk_name
					, '_status' => $mk_status
					, '_uid' => $mk_uid
					, '_knum' => $mk_knum
					, '_klink' => $mk_klink
					, '_kadd1' => $mk_kadd1
					, '_kadd2' => $mk_kadd2
					, '_kadd3' => $mk_kadd3
					, '_kadd4' => $mk_kadd4
					, '_kadd5' => $mk_kadd5
					, '_kadd6' => $mk_kadd6
					, '_kadd7' => $mk_kadd7
					, '_kadd8' => $mk_kadd8
				),
				'kakao_admin' => array(
					  '_name' => $ak_name
					, '_status' => $ak_status
					, '_uid' => $ak_uid
					, '_knum' => $ak_knum
					, '_klink' => $ak_klink
					, '_kadd1' => $ak_kadd1
					, '_kadd2' => $ak_kadd2
					, '_kadd3' => $ak_kadd3
					, '_kadd4' => $ak_kadd4
					, '_kadd5' => $ak_kadd5
					, '_kadd6' => $ak_kadd6
					, '_kadd7' => $ak_kadd7
					, '_kadd8' => $ak_kadd8
				)
			);

			echo json_encode($array);

		break;


	}

?>