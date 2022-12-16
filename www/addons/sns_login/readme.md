# 소셜로그인 개발가이드
- 기본 애드온 경로: `/addons/sns_login`

## 소셜로그인 기본 제공 콜백주소
- 페이스북: 도메인/addons/sns_login/facebook/callback.php
- 카카오: 도메인/addons/sns_login/kakao/callback.php
- 네이버: 도메인/addons/sns_login/naver/callback.php

## 신규 로그인 추가
1 `/addons/sns_login/sns_login.hook.php`를 열고 `$SNSField`에 기존 항목을 참조하여 새로 추가 합니다.
```php
$SNSField = array(
    ...
    '로그인 폴더'=>array(
        'name'=>'로그인 명칭',
        'config_use'=>'sns 사용여부',
        'config_key'=>'환경 설정 테이블의 앱 아이디 DB필드 명',
        'config_secret'=>'환경 설정 테이블의 앱 시크릿 DB필드 명(없는경우 nope으로 처리 하고 콜백에서 사용여부 수정)',
        'join'=>'가입여부 DB필드 명',
        'id'=>'가입 아이디 DB필드 명',
        'short'=>'아이디 접두사'
    ), // SNS 관련 데이터 필드
);
```

2 `/addons/sns_login/` 폴더에 `$SNSField`에 지정한 `로그인 폴더`명의 폴더를 생성합니다.

3 `/addons/sns_login/kakao`의 폴더 내용을 기준으로 파일들을 생성 수정 합니다.
```
파일 구조
┌ /addons/sns_login/로그인 폴더
├ index.php: 공백파일
├ inc.php: 기본라이브러리 호출 파일(변경금지)
├ callback.php: oauth통신을 하여 정보를 취득파일(수정팔요)
└ login.pro.php: 로그인, 연동, 가입 처리 프로세스 파일
```

4 `callback.php` 파일 수정  
 - 각각 SNS환경에 맞게 수정합니다.
