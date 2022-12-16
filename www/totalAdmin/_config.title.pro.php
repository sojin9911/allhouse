<?php
	include 'inc.php';

	if($_mode == 'modify'){
		// 모든 정보 삭제
		_MQ_noreturn(" delete from smart_site_title where 1 ");
		if(count($_uid)){
			$arrQue = array();
			foreach($_uid as $k=>$v){
				// 새로 추가
				if(trim($_page[$k]) <> '') $arrQue[] = "('". addslashes(trim($_name[$k])) ."', '§§". addslashes(trim($_page[$k])) ."§§', '". addslashes(trim($_title[$k])) ."')";
			}
			$que = " insert into smart_site_title (sst_name, sst_page, sst_title) values " . implode(",", $arrQue);
			_MQ_noreturn($que);
		}
		 error_loc_msg('_config.title.form.php?menuUid='.$menuUid , '정상적으로 저장되었습니다.');
	}else if($_mode == 'create'){
		// DB 생성 여부 체크
		$row_chk = _MQ(" SHOW TABLES LIKE 'smart_site_title' ");
		if(count($row_chk) < 1){

			// 테이블 생성
			$que = "
				CREATE TABLE  `smart_site_title` (
				`sst_uid` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT  '고유번호',
				`sst_name` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '페이지명',
				`sst_page` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '페이지URL',
				`sst_title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  '페이지 타이틀',
				PRIMARY KEY (  `sst_uid` ) ,
				INDEX (  `sst_name` )
				) ENGINE = MYISAM COMMENT =  '사이트 타이틀 설정';
			";
			_MQ_noreturn($que);

			// 기본 데이터 등록
			$que = "
				INSERT INTO `smart_site_title` (`sst_uid`, `sst_name`, `sst_page`, `sst_title`) VALUES
				(1, '기본페이지 - 메인페이지', '§§/§§', '{공통타이틀}'),
				(2, '기본페이지 - 상품목록', '§§/?pn=product.list§§', '{카테고리명} - {사이트명}'),
				(3, '기본페이지 - 쇼핑몰 기획전 홈', '§§/?pn=product.promotion_list§§', '쇼핑몰 기획전 - {사이트명}'),
				(4, '기본페이지 - 쇼핑몰 기획전 상세', '§§/?pn=product.promotion_view§§', '{기획전명} - {사이트명}'),
				(5, '기본페이지 - 브랜드상품', '§§/?pn=product.brand_list§§', '브랜드 상품 - {사이트명}'),
				(6, '기본페이지 - 브랜드상품(브랜드 선택 시)', '§§/?pn=product.brand_list&uid=§§', '{브랜드명} - {사이트명}'),
				(7, '기본페이지 - 상품상세보기', '§§/?pn=product.view§§', '{상품명} - {사이트명}'),
				(8, '기본페이지 - 상품검색', '§§/?pn=product.search.list§§', '{검색어} 검색결과 - {사이트명}'),
				(9, '기본페이지 - 장바구니', '§§/?pn=shop.cart.list§§', '장바구니 - {사이트명}'),
				(10, '기본페이지 - 주문/결제', '§§/?pn=shop.order.form§§/?pn=shop.order.result§§', '주문/결제 - {사이트명}'),
				(11, '기본페이지 - 주문완료', '§§/?pn=shop.order.complete§§', '주문완료 - {사이트명}'),
				(12, '멤버쉽 - 로그인', '§§/?pn=member.login.form§§', '로그인 - {사이트명}'),
				(13, '멤버쉽 - 회원가입', '§§/?pn=member.join.agree§§/?pn=member.join.form§§', '회원가입 - {사이트명}'),
				(14, '멤버쉽 - 가입완료', '§§/?pn=member.join.complete§§', '가입완료 - {사이트명}'),
				(15, '멤버쉽 - 아이디/비밀번호 찾기', '§§/?pn=member.find.form§§', '아이디/비밀번호 찾기 - {사이트명}'),
				(16, '마이페이지 - 메인', '§§/?pn=mypage.main§§', '마이페이지 - {사이트명}'),
				(17, '마이페이지 - 주문내역', '§§/?pn=mypage.order.list§§/?pn=mypage.order.view§§', '주문내역 - {사이트명}'),
				(18, '마이페이지 - 적립금', '§§/?pn=mypage.point.list§§', '적립금 - {사이트명}'),
				(19, '마이페이지 - 쿠폰', '§§/?pn=mypage.coupon.list§§', '쿠폰 - {사이트명}'),
				(20, '마이페이지 - 1:1 온라인 문의', '§§/?pn=mypage.inquiry.form§§', '1:1 온라인 문의 - {사이트명}'),
				(21, '마이페이지 - 찜한상품', '§§/?pn=mypage.wish.list§§', '찜한상품 - {사이트명}'),
				(22, '마이페이지 - 문의내역', '§§/?pn=mypage.inquiry.list§§', '문의내역 - {사이트명}'),
				(23, '마이페이지 - 상품후기', '§§/?pn=mypage.eval.list§§', '상품후기 - {사이트명}'),
				(24, '마이페이지 - 상품문의', '§§/?pn=mypage.qna.list§§', '상품문의 - {사이트명}'),
				(25, '마이페이지 - 정보수정', '§§/?pn=mypage.modify.form§§', '정보수정 - {사이트명}'),
				(26, '마이페이지 - 로그인기록', '§§/?pn=mypage.login.log§§', '로그인기록 - {사이트명}'),
				(27, '마이페이지 - 회원탈퇴', '§§/?pn=mypage.leave.form§§', '회원탈퇴 - {사이트명}'),
				(28, '게시판 - 게시판 리스트', '§§/?pn=board.list§§', '{게시판명} - {사이트명}'),
				(29, '게시판 - 게시판 상세보기', '§§/?pn=board.view§§', '{게시물제목} - {사이트명}'),
				(30, '게시판 - 게시판 글쓰기', '§§/?pn=board.form§§', '{게시판명} - {사이트명}'),
				(31, '고객센터 - 메인', '§§/?pn=service.main§§', '고객센터 - {사이트명}'),
				(32, '고객센터 - 자주 묻는 질문', '§§/?pn=faq.list§§', '자주 묻는 질문 - {사이트명}'),
				(33, '고객센터 - 미확인 입금자', '§§/?pn=service.deposit.list§§', '미확인 입금자 - {사이트명}'),
				(34, '커뮤니티 - 상품후기', '§§/?pn=service.eval.list§§', '상품후기 - {사이트명}'),
				(35, '커뮤니티 - 상품문의', '§§/?pn=service.qna.list§§', '상품문의 - {사이트명}'),
				(36, '커뮤니티 - 제휴문의', '§§/?pn=service.partner.form§§', '제휴문의 - {사이트명}'),
				(37, '커뮤니티 - 출석체크', '§§/?pn=promotion.attend§§', '출석체크 - {사이트명}'),
				(38, '일반페이지 - 회사소개', '§§/?pn=pages.view&type=agree&data=company§§', '회사소개 - {사이트명}'),
				(39, '일반페이지 - 이용안내', '§§/?pn=pages.view&type=agree&data=guide§§', '이용안내 - {사이트명}'),
				(40, '일반페이지 - 이용약관', '§§/?pn=pages.view&type=agree&data=agree§§', '이용약관 - {사이트명}'),
				(41, '일반페이지 - 개인정보처리방침', '§§/?pn=pages.view&type=agree&data=privacy§§', '개인정보처리방침 - {사이트명}'),
				(42, '일반페이지 - 이메일무단수집거부', '§§/?pn=pages.view&type=agree&data=deny§§', '이메일무단수집거부 - {사이트명}');
			";
			_MQ_noreturn($que);

			error_loc_msg('_config.title.form.php', 'DB가 추가 되었습니다. ');
		}else{
			error_loc_msg('_config.title.form.php', '이미 실행된 작업입니다. ');
		}
	}else{
		error_msg('잘못된 접근입니다.');
	}