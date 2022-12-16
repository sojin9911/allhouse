<?PHP
	include "inc.php";

	// - 입력수정 사전처리 ---
	if( $_mode == "delete"  ) {

	}
	// - 입력수정 사전처리 ---
	
	// - 모드별 처리 ---
	switch( $_mode ){

		case "ajax_form":
			//ViewArr($_POST);

			// 주소저장 배열
			$arr = array("jibun"=>array(),"road"=>array());
			
			// 지번 상세주소 추출
			$jibun_detail = trim(preg_replace("/${sido}|${sigungu}|${bname1}|${bname2}/i", "", $jibunAddress));
			// 도로명 상세주소 추출
			$road_detail = trim(preg_replace("/${sido}|${sigungu}|${bname1}|${roadname}/i", "", $roadAddress));

			// 지번 주소저장
			$arr['jibun'][] = $sido .' '. $sigungu;
			$arr['jibun'][] = $bname1;
			$arr['jibun'][] = $bname2;
			$arr['jibun'][] = $jibun_detail;
			$arr['jibun'] = array_filter($arr['jibun']);
			// 도로명 주소저장
			$arr['road'][] = $sido .' '. $sigungu;
			$arr['road'][] = $bname1;
			$arr['road'][] = $roadname;
			$arr['road'][] = $road_detail;
			$arr['road'] = array_filter($arr['road']);

			// 폼생성
			$idx = 0;
			$form_str = '
				<table class="table_form">
					<colgroup><col width="100"><col width="*"></colgroup>
					<tbody>
						<tr>
							<th>추가배송비</th>
							<td><input type="text" name="_addprice" id="_addprice" value="'. number_format($addprice) .'" class="design number_style" style="width:80px"><span class="fr_tx">원</span></td>
						</tr>';

			foreach($arr as $k=>$v){
				$_addr = ''; // 주소 이어붇이기
				$_keyval = ''; // 시도+구군 :: 키값으로 사용
				if($k=='jibun') $form_str .= '<tr><th>지번주소</th><td>';
				if($k=='road') $form_str .= '<tr><th>도로명주소</th><td>';
				foreach($v as $k2=>$v2){
					$idx++;
					$_addr = trim(implode(' ', array($_addr,$v2)));

					// 마지막데이터는 따로 출력
					if(preg_replace('/ /i', '', $_addr) == preg_replace('/ /i', '', (implode(' ', $arr[$k])))){ continue; }
					$form_str .= '
						<div class="clear_both">
							<input type="text" name="" value="'. $_addr .'" class="design" disabled style="width:300px">
							<input type="hidden" name="" id="addr_'. $idx .'" value="'. $_addr .'" />
							<a href="#none" onclick="insert_addprice('.$idx.')" class="c_btn h27">선택추가</a>
						</div>
					';
				}
				$idx++;
				$form_str .= '
					<div class="clear_both">
						<input type="text" name="" id="addr_'. $idx .'" value="'. $_addr .'" class="design" style="width:300px">
						<a href="#none" onclick="insert_addprice('.$idx.')" class="c_btn h27 gray">수정추가</a>
					</div>
				';
				$form_str .= '</td></tr>';
			}

			$form_str .= '</tbody></table>';
			echo $form_str;

			break;
		
		
		
		
		// 도서산간지역 추가
		case "add":
			
			// 필수정보 체크
			$addr = nullchk($addr , "주소가 선택되지 않았습니다.");
			//$addprice = nullchk($addprice , "추가배송비가 입력되지 않았습니다.");
			$addprice = $addprice ? $addprice : 0;


			// 이미 적용된 주소인지 체크
			$chk_cnt = _MQ_result(" select count(*) as cnt from smart_delivery_addprice where da_addr = '". trim($addr) ."' ");
			if($chk_cnt>0) error_msg("이미 추가배송비가 적용된 주소입니다.");


			// 도서산간지역 추가
			$que = "
				insert into smart_delivery_addprice set
					da_addr = '". addslashes(trim($addr)) ."'
					,da_price = '". rm_str($addprice) ."' 
					,da_rdate = now()
			";
			_MQ_noreturn($que);
			error_loc_msg("_config.delivery_addprice.list.php?addprice=".$addprice,"정상적으로 추가 되었습니다");

			break;

		
		// 도서산간지역 수정
		case "modify":
			
			$_uid = nullchk($_uid , "주소가 선택되지 않았습니다.");
			$addprice = $addprice ? $addprice : 0;


			// 도서산간지역 추가
			$que = "
				update smart_delivery_addprice set
					da_addr = '". addslashes(trim($addr)) ."'
					,da_price = '". rm_str($addprice) ."' 
				where da_uid = '". $_uid ."'
			";
			_MQ_noreturn($que);
			echo "<script>window.opener.location.reload();</script>";
			error_msgPopup_s("정상적으로 수정되었습니다.");

			break;

		
		// 추가배송비 삭제
		case "delete":
			_MQ_noreturn("delete from smart_delivery_addprice where da_uid='$_uid' ");
			error_loc_msg( "_config.delivery_addprice.list.php?" . enc('d' , $_PVSC) ,"정상적으로 삭제되었습니다");
			break;

		
		// 추가배송비 선택 삭제
		case "mass_delete":
			if(sizeof($chk_uid) == 0 ) {
				error_msg("잘못된 접근입니다.");
			}
			foreach($chk_uid as $k=>$v) {
				_MQ_noreturn("delete from smart_delivery_addprice where da_uid='". $v ."' ");
			}

			error_loc_msg( "_config.delivery_addprice.list.php?" . enc('d' , $_PVSC) ,"정상적으로 삭제되었습니다");
			break;

		
		// 추가배송비 선택 삭제
		case "mass_modify":
			if(sizeof($chk_uid) == 0) {
				error_msg("잘못된 접근입니다.");
			}
			$modify_addprice  = rm_str($modify_addprice) ? rm_str($modify_addprice) : 0;
			_MQ_noreturn("update smart_delivery_addprice set da_price = '". $modify_addprice ."' where da_uid in ('". implode("','",$chk_uid) ."') ");
			error_loc_msg( "_config.delivery_addprice.list.php?" . enc('d' , $_PVSC) ,"추가배송비가 변경되었습니다.");
			break;
		

		// 추가배송비 엑셀 업로드
		case "ins_excel":

			require_once dirname(__file__)."/../include/reader.php";
			$data = new Spreadsheet_Excel_Reader();
			$data->setOutputEncoding('utf-8');

			$data->read($_FILES['excel_file']['tmp_name']);

			error_reporting(E_ALL ^ E_NOTICE);
			
			$result = array("update"=>0, "insert"=>0); // 추가/업데이트 카운트
			for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {

				$r = $data->sheets[0]['cells'][$i];

				// 이미등록된 주소인지 체크
				$chk = _MQ(" select da_uid from smart_delivery_addprice where da_addr = '". addslashes(trim($r[1])) ."'");

				if($chk['da_uid']){
					// 도서산간지역 추가배송비 업데이트
					$que = "
						update smart_delivery_addprice set
							da_price = '". rm_str($r[2]) ."' 
						where da_uid = '". $chk['da_uid'] ."'
					";
					_MQ_noreturn($que);
					$result['update']++;
				}else{
					// 도서산간지역 추가
					$que = "
						insert into smart_delivery_addprice set
							da_addr = '". addslashes(trim($r[1])) ."'
							,da_price = '". rm_str($r[2]) ."' 
							,da_rdate = now()
					";
					_MQ_noreturn($que);
					$result['insert']++;
				}

			}

			$msg = "";
			if($result['insert'] > 0){
				$msg .= number_format($result['insert']) . "개의 도서산간지역이 추가되었습니다. ";
			}
			if($result['update'] > 0){
				if($msg) $msg .= "\\n\\n";
				$msg .= number_format($result['update']) . "개의 도서산간지역의 추가배송비가 업데이트 되었습니다. ";
			}

			error_loc_msg("_config.delivery_addprice.list.php" , $msg);
			break;


		// 엑셀 다운로드
		case "select_excel": // 선택
		case "search_excel": // 검색


			$toDay = date("YmdHis");
			$fileName = "delivery_addprice";

			// -- Exel 파일로 변환 ---
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=".$fileName."_".$toDay.".xls");


			if($_mode == "select_excel") {
				if(sizeof($chk_uid) == 0) {
					error_msg("잘못된 접근입니다.");
				}
				$app_uids = implode("','" , $chk_uid);
				$s_query = " where da_uid in ('" . $app_uids . "') ";
			}
			else if($_mode == "search_excel") {
				$s_query = enc('d',$_search_que);


			}


			echo "
					<table border=1>
						<tr>
							<td>도서산간 지역</td><td>추가배송비</td>
						</tr>
			";
			$que = " select * from smart_delivery_addprice {$s_query} ORDER BY da_addr asc ";
			$res = _MQ_assoc($que);
			foreach($res as $k=>$v) {

				echo "
					<tr>
						<td>". $v['da_addr'] ."</td><td>". $v['da_price'] ."</td>
					</tr>
				";
			}
			echo "</table>";
			
			break;



		// DB 생성
		case "create_table": 


				// 추가배송비 테이블 추가 2017-03-02 SSJ
				$r = _MQ_assoc(" SHOW TABLES LIKE 'smart_delivery_addprice'; ");

				if(count($r)>0){
					// 이미추가된 테이블
					//error_loc_msg("_config.delivery_addprice.list.php" , "이미 추가되었습니다.");
				}else{
					// 테이블생성
					$que = "
							CREATE TABLE IF NOT EXISTS `smart_delivery_addprice` (
							  `da_uid` int(8) NOT NULL auto_increment,
							  `da_addr` varchar(100) NOT NULL COMMENT '지정주소',
							  `da_price` int(10) default NULL COMMENT '추가배송비',
							  `da_rdate` datetime NOT NULL COMMENT '저장일',
							  PRIMARY KEY  (`da_uid`),
							  KEY `da_addr` (`da_addr`)
							)
					";
					_MQ_noreturn($que);
					// 기본데이터 추가
					$que = "
						INSERT INTO `smart_delivery_addprice` (`da_uid`, `da_addr`, `da_price`, `da_rdate`) VALUES
							(1, '인천 강화군 교동면', 3000, now()),
							(2, '인천 강화군 삼산면', 3000, now()),
							(3, '인천 강화군 서도면', 3000, now()),
							(4, '인천 옹진군 대청면', 3000, now()),
							(5, '인천 옹진군 덕적면', 3000, now()),
							(6, '인천 옹진군 백령면', 3000, now()),
							(7, '인천 옹진군 북도면', 3000, now()),
							(8, '인천 옹진군 연평면', 3000, now()),
							(9, '인천 옹진군 자월면', 3000, now()),
							(10, '인천 중구 무의동', 3000, now()),
							(11, '전북 군산시 옥도면', 3000, now()),
							(12, '전북 부안군 위도면', 3000, now()),
							(13, '부산 강서구 눌차동', 3000, now()),
							(14, '부산 강서구 대항동', 3000, now()),
							(15, '부산 강서구 동선동', 3000, now()),
							(16, '부산 강서구 성북동', 3000, now()),
							(17, '부산 강서구 천성동', 3000, now()),
							(18, '경남 거제시 장목면 시방리', 3000, now()),
							(19, '경남 거제시 둔덕면 술역리', 3000, now()),
							(20, '경남 사천시 마도동', 3000, now()),
							(21, '경남 사천시 신수동', 3000, now()),
							(22, '경남 통영시 사량면', 3000, now()),
							(23, '경남 통영시 욕지면', 3000, now()),
							(24, '경남 통영시 용남면 어의리', 3000, now()),
							(25, '경남 통영시 용남면 지도리', 3000, now()),
							(26, '경남 통영시 한산면', 3000, now()),
							(27, '경남 통영시 산양읍 저림리', 3000, now()),
							(28, '경남 통영시 산양읍 추도리', 3000, now()),
							(29, '경남 통영시 산양읍 연곡리', 3000, now()),
							(30, '경남 통영시 산양읍 곤리', 3000, now()),
							(31, '제주특별자치도 제주시', 3000, now()),
							(32, '제주특별자치도 제주시 우도면', 3000, now()),
							(33, '제주특별자치도 제주시 추자면', 3000, now()),
							(34, '제주특별자치도 서귀포시', 3000, now()),
							(35, '경북 울릉군 북면', 3000, now()),
							(36, '경북 울릉군 서면', 3000, now()),
							(37, '경북 울릉군 울릉읍', 3000, now()),
							(38, '충남 당진시 석문면 난지도리', 3000, now()),
							(39, '충남 당진시 신평면 매산리', 3000, now()),
							(40, '충남 보령시 오천면 고대도리', 3000, now()),
							(41, '충남 보령시 오천면 녹도리', 3000, now()),
							(42, '충남 보령시 오천면 삽시도리', 3000, now()),
							(43, '충남 보령시 오천면 외연도리', 3000, now()),
							(44, '충남 보령시 오천면 원산도리', 3000, now()),
							(45, '충남 보령시 오천면 장고도리', 3000, now()),
							(46, '충남 보령시 오천면 호도리', 3000, now()),
							(47, '충남 보령시 오천면 효자도리', 3000, now()),
							(48, '충남 서산시 지곡면 중왕리', 3000, now()),
							(49, '충남 태안군 근흥면 가의도리', 3000, now()),
							(50, '전남 고흥군 봉래면 사양리', 3000, now()),
							(51, '전남 고흥군 도양읍 시산리', 3000, now()),
							(52, '전남 고흥군 도양읍 봉암리', 3000, now()),
							(53, '전남 고흥군 도양읍 득량리', 3000, now()),
							(54, '전남 고흥군 도화면 지죽리', 3000, now()),
							(55, '전남 목포시 달동', 3000, now()),
							(56, '전남 목포시 율도동', 3000, now()),
							(57, '전남 신안군 도초면', 3000, now()),
							(58, '전남 신안군 비금면', 3000, now()),
							(59, '전남 신안군 신의면', 3000, now()),
							(60, '전남 신안군 안좌면', 3000, now()),
							(61, '전남 신안군 암태면', 3000, now()),
							(62, '전남 신안군 압해읍 가란리', 3000, now()),
							(63, '전남 신안군 압해읍 고이리', 3000, now()),
							(64, '전남 신안군 압해읍 매화리', 3000, now()),
							(65, '전남 신안군 임자면', 3000, now()),
							(66, '전남 신안군 자은면', 3000, now()),
							(67, '전남 신안군 지도읍 어의리', 3000, now()),
							(68, '전남 신안군 지도읍 선도리', 3000, now()),
							(69, '전남 신안군 장산면', 3000, now()),
							(70, '전남 신안군 증도면 병풍리', 3000, now()),
							(71, '전남 신안군 팔금면', 3000, now()),
							(72, '전남 신안군 하의면', 3000, now()),
							(73, '전남 신안군 흑산면', 3000, now()),
							(74, '전남 여수시 경호동', 3000, now()),
							(75, '전남 여수시 남면', 3000, now()),
							(76, '전남 여수시 묘도동', 3000, now()),
							(77, '전남 여수시 삼산면', 3000, now()),
							(78, '전남 여수시 화정면 개도리', 3000, now()),
							(79, '전남 여수시 화정면 낭도리', 3000, now()),
							(80, '전남 여수시 화정면 상화리', 3000, now()),
							(81, '전남 여수시 화정면 여자리', 3000, now()),
							(82, '전남 여수시 화정면 월호리', 3000, now()),
							(83, '전남 여수시 화정면 적금리', 3000, now()),
							(84, '전남 여수시 화정면 제도리', 3000, now()),
							(85, '전남 여수시 화정면 조발리', 3000, now()),
							(86, '전남 여수시 화정면 하화리', 3000, now()),
							(87, '전남 영광군 낙월면', 3000, now()),
							(88, '전남 완도군 군외면 당인리', 3000, now()),
							(89, '전남 완도군 군외면 불목리', 3000, now()),
							(90, '전남 완도군 군외면 영풍리', 3000, now()),
							(91, '전남 완도군 군외면 황진리', 3000, now()),
							(92, '전남 완도군 금당면', 3000, now()),
							(93, '전남 완도군 금일읍', 3000, now()),
							(94, '전남 완도군 약산면', 3000, now()),
							(95, '전남 완도군 고금면', 3000, now()),
							(96, '전남 완도군 노화읍', 3000, now()),
							(97, '전남 완도군 보길면', 3000, now()),
							(98, '전남 완도군 생일면', 3000, now()),
							(99, '전남 완도군 소안면', 3000, now()),
							(100, '전남 완도군 청산면', 3000, now()),
							(101, '전남 진도군 조도면', 3000, now()),
							(102, '전남 진도군 의신면 모도리', 3000, now()),
							(103, '전남 보성군 벌교읍 장도리', 3000, now());
					";
					_MQ_noreturn($que);

				}

				// -- 설정 전체 설정 DB 추가 -----
				$chk = _MQ_assoc(" desc smart_setup ");
				//ViewArr($chk);
				$arr = array();
				foreach($chk as $k=>$v){
					$arr[$v['Field']] = "Y";
				}
				//ViewArr($arr);
				$add = "s_del_addprice_use";
				if($arr[$add]<>"Y"){
					_MQ_noreturn(" ALTER TABLE  `smart_setup` ADD  `s_del_addprice_use` ENUM(  'Y',  'N' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'N' COMMENT  '추가배송비적용여부' ");
				}
				$add = "s_del_addprice_use_normal";
				if($arr[$add]<>"Y"){
					_MQ_noreturn(" ALTER TABLE  `smart_setup` ADD  `s_del_addprice_use_normal` ENUM(  'Y',  'N' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'N' COMMENT  '일반배송상품을 무료배송비 이상 구매하여 무료배송이 적용된경우 추가배송비 적용여부' ");
				}
				$add = "s_del_addprice_use_unit";
				if($arr[$add]<>"Y"){
					_MQ_noreturn(" ALTER TABLE  `smart_setup` ADD  `s_del_addprice_use_unit` ENUM(  'Y',  'N' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'N' COMMENT  '개별배송상품의 추가배송비 적용여부' ");
				}
				$add = "s_del_addprice_use_free";
				if($arr[$add]<>"Y"){
					_MQ_noreturn(" ALTER TABLE  `smart_setup` ADD  `s_del_addprice_use_free` ENUM(  'Y',  'N' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'N' COMMENT  '무료배송상품의 추가배송비 적용여부' ");
				}

				// -- 입점업체 설정 DB 추가 -----
				$chk = _MQ_assoc(" desc smart_company ");
				//ViewArr($chk);
				$arr = array();
				foreach($chk as $k=>$v){
					$arr[$v['Field']] = "Y";
				}
				//ViewArr($arr);
				$add = "cp_del_addprice_use";
				if($arr[$add]<>"Y"){
					_MQ_noreturn(" ALTER TABLE  `smart_company` ADD  `cp_del_addprice_use` ENUM(  'Y',  'N' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'N' COMMENT  '추가배송비적용여부' ");
				}
				$add = "cp_del_addprice_use_normal";
				if($arr[$add]<>"Y"){
					_MQ_noreturn(" ALTER TABLE  `smart_company` ADD  `cp_del_addprice_use_normal` ENUM(  'Y',  'N' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'N' COMMENT  '일반배송상품을 무료배송비 이상 구매하여 무료배송이 적용된경우 추가배송비 적용여부' ");
				}
				$add = "cp_del_addprice_use_unit";
				if($arr[$add]<>"Y"){
					_MQ_noreturn(" ALTER TABLE  `smart_company` ADD  `cp_del_addprice_use_unit` ENUM(  'Y',  'N' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'N' COMMENT  '개별배송상품의 추가배송비 적용여부' ");
				}
				$add = "cp_del_addprice_use_free";
				if($arr[$add]<>"Y"){
					_MQ_noreturn(" ALTER TABLE  `smart_company` ADD  `cp_del_addprice_use_free` ENUM(  'Y',  'N' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'N' COMMENT  '무료배송상품의 추가배송비 적용여부' ");
				}

				// -- 주문상품 추가배송비 DB 추가 -----
				$chk = _MQ_assoc(" desc smart_order_product ");
				//ViewArr($chk);
				$arr = array();
				foreach($chk as $k=>$v){
					$arr[$v['Field']] = "Y";
				}
				//ViewArr($arr);
				$add = "op_add_delivery_price";
				if($arr[$add]<>"Y"){
					_MQ_noreturn(" ALTER TABLE  `smart_order_product` ADD  `op_add_delivery_price` int(11)  NOT NULL DEFAULT  '0' COMMENT  '주문상품별 추가배송비");
				}

			
				error_loc_msg("_config.delivery_addprice.list.php" , "추가배송비 DB가 생성되었습니다.");
			break;

	}
	// - 모드별 처리 ---

?>