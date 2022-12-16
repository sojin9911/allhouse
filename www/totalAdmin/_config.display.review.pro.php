<?php
include_once('inc.php');

_MQ_noreturn(" update smart_setup set s_main_review = '".(isset($s_main_review)?$s_main_review:'Y')."', s_main_review_porder = '".(isset($s_main_review_porder)?$s_main_review_porder:'R')."', s_main_review_score = '".(isset($s_main_review_score)?$s_main_review_score:1)."', s_main_review_view = '".(isset($s_main_review_view)?$s_main_review_view:'A')."', s_main_review_limit = '".($s_main_review_limit > 0?$s_main_review_limit:3)."' where s_uid = '1' ");
error_loc('_config.display.review.php');