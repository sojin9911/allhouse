<?
    ##RESULT ,ORDER 페이지에서 우선 선언되어야할 내용을 포함합니다. 
    ##미리선언하고픈 데이터를 입력하시기 바랍니다.

    $view_method_card = "Y";    //결제방법중 카드를 보여준다. (Y:보임 N:숨김)
    $view_method_rbank = "N";    //결제방법중 실시간계좌이체를 보여준다. (올더게이트는 실시간을 지원하지않으므로 설정해도 사용할수 없음)
    $run_function = "PayStart();";  //결제창오픈 함수명
?>