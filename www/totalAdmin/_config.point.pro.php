<?php
include_once('inc.php');

$que = "update smart_setup set
			s_pointusevalue			= '".rm_str($_pointusevalue)."',
			s_pointuselimit			= '".rm_str($_pointuselimit)."',
			s_joinpoint				= '".rm_str($_joinpoint)."',
			s_joinpointprodate		= '".rm_str($_joinpointprodate)."',
			s_orderpointprodate		= '".rm_str($_orderpointprodate)."',
			s_orderpointprodate		= '".rm_str($_orderpointprodate)."',
			s_productevalpoint		= '".rm_str($_productevalpoint)."',
			s_productevalprodate	= '".rm_str($_productevalprodate)."',
			s_producteval_limit		= '".($_producteval_limit?$_producteval_limit:'N')."'
			where s_uid				= 1 ";

_MQ_noreturn($que);

error_loc("_config.point.form.php");