<?php
defined('_OD_DIRECT_') OR exit('개별 실행이 불가능한 파일 입니다.'); // 개별실행 방지
actionHook(basename(__FILE__).'.start'); // 해당 파일 시작에 대한 후킹액션 실행


// 벤치마크(속도측정)결과를 스크립트 변수로 생성 - 후킹액션이 실행 되는 것들만 측정됨(임의 후킹 액션을 통해 특정 가능)
function BenchMarkScript() {
	$Bench = HookTimeView(); // 브라우저 콘솔창에 console.table(bench); 를 실행
	if(count($Bench) > 0) {
		echo PHP_EOL.'<script>'.PHP_EOL.'var bench = new Array();';
		foreach($Bench as $k=>$v) {
			echo PHP_EOL."bench['{$k}'] = '{$v}초'; ";
		}
		echo PHP_EOL."bench['소모 메모리'] = '".SizeText(memory_get_usage())."';";
		echo PHP_EOL.'</script>'.PHP_EOL;
	}
}
addHook('footer_insert', 'BenchMarkScript');

include_once($SkinData['skin_root'].'/'.basename(__FILE__)); // 스킨폴더에서 해당 파일 호출
actionHook(basename(__FILE__).'.end'); // 해당 파일 종료에 대한 후킹액션 실행