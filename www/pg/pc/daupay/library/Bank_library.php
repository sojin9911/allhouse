<?
/* ========================================================================== */
/* =   프로그램 명		:	Culture_library.php                   = */
/* =   프로그램 설명		:	Library 파일 		            = */
/* =   작성일			:	2009-04      		            = */
/* =   저작권			:	(주)다우기술                        = */
/* ========================================================================== */
require dirname(__FILE__)."/../config/keycode.cfg";	
define( "PAYTYPE"	,"B" );
define( "DAOU_SOCK_ERROR"	,"-1" );
define( "VERSION"	,"1.0" );
define( "REQUEST"	,"01" );
define( "RESPONSE"	,"02" );
define( "ACK"	,"03" );

?>
<?
/* ========================================================================== */
/* =   함수 명			:	CultureCert                           = */
/* =   함수 설명		:	Culture 인증		            = */
/* =   작성일			:	2009-04      		            = */
/* =   저작권			:	(주)다우기술      	            = */
/* ========================================================================== */
function BankCancel(  $server_ip, $port, $cpid, $enckey, $timeout ) {
	
	global $opcode;
	global $DAOUTRX;
	global $AMOUNT;
	global $CANCELMEMO;
	

	
	global $res_resultcode;
	global $res_errormessage;
	global $res_daoutrx;
	global $res_amount;
	global $res_canceldate;

	
	$sendBody = 
    		_MaxLength($DAOUTRX,20).'|'.
    		_MaxLength($AMOUNT,10).'|'.
    		_MaxLength($CANCELMEMO,50);


		//opcode 추가
		$opcode = PAYTYPE."CANCEL_";
		
	  	$recvArray	= array();
		
	  	$recvArray	= explode( "|", bin_do(  $server_ip, $port, $opcode, $cpid, $timeout, $enckey, $sendBody));
	  	
	  	$res_resultcode    	= $recvArray[0];
	  	$res_errormessage  	= $recvArray[1];
	  	$res_daoutrx       	= $recvArray[2];
	  	$res_amount        	= $recvArray[3];
	  	$res_canceldate      = $recvArray[4];
		
}


/* ========================================================================== */
/* =   함수 명			:	bin_do		   	  	    = */
/* =   함수 설명		:	bin 실행 			    = */
/* =   작성일			:	2009-02      	                    = */
/* =   저작권			:	(주)다우기술                        = */
/* ========================================================================== */
function bin_do(  $argv1="", $argv2="", $argv3="", $argv4="", $argv5="", $argv6="", $argv7="" )
{
	
		//body 부분 encrypt 암호화
		$daou_packet_body = _packet_encrypt( $argv7, $argv6);

		//소캣 통신
		$result = _requestImpayUnit($daou_packet_body, $argv3, $argv4) ;

		//result 부분 decrypt
		$rt = _packet_decrypt( $result, $argv6);
	
		return $rt;
}



function _encutment($fbi) {         
	$fbi = substr($fbi,46,2).substr($fbi,12,1).substr($fbi,268,2).substr($fbi,7,1).substr($fbi,169,2).substr($fbi,46,1).substr($fbi,305,1).substr($fbi,188,1).substr($fbi,103,1).substr($fbi,402,1).substr($fbi,246,2).substr($fbi,193,1);
	return $fbi;
}

function _decutment($swx) {         
	$swx = substr($swx,46,2).substr($swx,140,1).substr($swx,268,2).substr($swx,110,1).substr($swx,169,2).substr($swx,212,1).substr($swx,305,1).substr($swx,312,1).substr($swx,318,1).substr($swx,232,1).substr($swx,246,2).substr($swx,193,1).substr($swx,12,1).substr($swx,103,1).substr($swx,269,2).substr($swx,46,1).substr($swx,200,1).substr($swx,46,1).substr($swx,509,2).substr($swx,79,2).substr($swx,136,1).substr($swx,80,1).substr($swx,178,1).substr($swx,80,1).substr($swx,300,1);
	return $swx;
} 

if ( !function_exists( 'hex2bin' ) ) {
function hex2bin($hexdata) {                
	$bindata="";                
	for ($i=0;$i<strlen($hexdata);$i+=2) {                        
		$bindata.=chr(hexdec(substr($hexdata,$i,2)));                
	}                
	return $bindata;        
}                
}

function _packet_encrypt($param_input_string, $enenkey)
	{
		
		$desend =  "";
		$belief = array();
		$belief[0] = "0f1e2d34cb5a6978";
		$belief[1] = "57930f21a38ec6b4";
		$belief[2] = "1d72eb0f39ac8465";
		$belief[3] = "c3ab69720514df8e";
		$belief[4] = "a35c97b261d04fe8";
		$belief[5] = "905373c2f41a8e6b";
		$belief[6] = "1d72eb09fc38a465";
		$belief[7] = "5793201f3aec86b4";
		$belief[8] = "c5ab36917024dfe8";
		$belief[9] = "395730c8f142abe6";
		$belief[10] = "d1e7209bcf38a645";
		$belief[11] = "903537cf2a481e6b";
		$belief[12] = "c5a3b97261d40f8e";
		$belief[13] = "1d2e7fb03c9a8465";
		$belief[14] = "a53c7b926d014e8f";
		$belief[15] = "10efd234cab56987";
		$hope = array();
		$hope[0] = "sjhwvbsb";
		$hope[1] = "hwcsghea";
		$hope[2] = "gncabknd";
		$hope[3] = "nitenksw";
		$hope[4] = "tegnlsdd";
		$hope[5] = "mdmehddp";
		$hope[6] = "leokfsdk";
		$hope[7] = "qawsxawy";
		$hope[8] = "unversty";
		$hope[9] = "bananaqq";
		$hope[10] = "internet";
		$hope[11] = "menglong";
		$hope[12] = "poiukjhg";
		$hope[13] = "qwsxashj";
		$hope[14] = "wordwide";
		$hope[15] = "tgbyhnrf";
		$webdate = array();
		$webdate[0] = "20120321232312aa";
		$webdate[1] = "20120223223312bb";
		$webdate[2] = "20120509223312aa";
		$webdate[3] = "20120302223312bb";
		$webdate[4] = "20110418223312aa";
		$webdate[5] = "20120426223312bb";
		$webdate[6] = "20120312223312aa";
		$webdate[7] = "20100723223312bb";
		$webdate[8] = "20091211223312aa";
		$webdate[9] = "20080625223312bb";
		$webdate[10] = "20120508223312aa";
		$webdate[11] = "20120104223312bb";
		$webdate[12] = "20111103223312aa";
		$webdate[13] = "20110209223312bb";
		$webdate[14] = "20120323223312aa";
		$webdate[15] = "20090217223312bb";

		for($i = 0 ; $i < 16 ; $i++){
			if(i % 4 == 0){
				$desend.= $belief[$i].$webdate[$i].$hope[$i] % 16;
			}else if(i % 3 == 0){
				$desend.= CODEKEY1.CODEKEY4.CODEKEY7;
			}else if(i % 2 == 0){
				$desend.= CODEKEY2.CODEKEY5.CODEKEY8;
			}else{
				$desend.= CODEKEY3.CODEKEY6;
			}
		}

		$suq = base64_encode(_encutment($desend));
		$xst = base64_encode(_decutment($desend));


		$key2 = bin2hex($enenkey);  
	   	$padding_string = _toPkcs7($param_input_string) ;
	    $cipher_txt = @mcrypt_encrypt(MCRYPT_RIJNDAEL_128
	    								,hex2bin(_hash($suq).$key2)
	    								, $padding_string
	    								, MCRYPT_MODE_CBC
										,_unhash($xst));			
	   $encrypttext =  bin2hex($cipher_txt);
			
		return $encrypttext;	
	}


	
function _packet_decrypt($param_input_string, $enenkey)
	{  

		$desend =  "";
		$belief = array();
		$belief[0] = "0f1e2d34cb5a6978";
		$belief[1] = "57930f21a38ec6b4";
		$belief[2] = "1d72eb0f39ac8465";
		$belief[3] = "c3ab69720514df8e";
		$belief[4] = "a35c97b261d04fe8";
		$belief[5] = "905373c2f41a8e6b";
		$belief[6] = "1d72eb09fc38a465";
		$belief[7] = "5793201f3aec86b4";
		$belief[8] = "c5ab36917024dfe8";
		$belief[9] = "395730c8f142abe6";
		$belief[10] = "d1e7209bcf38a645";
		$belief[11] = "903537cf2a481e6b";
		$belief[12] = "c5a3b97261d40f8e";
		$belief[13] = "1d2e7fb03c9a8465";
		$belief[14] = "a53c7b926d014e8f";
		$belief[15] = "10efd234cab56987";
		$hope = array();
		$hope[0] = "sjhwvbsb";
		$hope[1] = "hwcsghea";
		$hope[2] = "gncabknd";
		$hope[3] = "nitenksw";
		$hope[4] = "tegnlsdd";
		$hope[5] = "mdmehddp";
		$hope[6] = "leokfsdk";
		$hope[7] = "qawsxawy";
		$hope[8] = "unversty";
		$hope[9] = "bananaqq";
		$hope[10] = "internet";
		$hope[11] = "menglong";
		$hope[12] = "poiukjhg";
		$hope[13] = "qwsxashj";
		$hope[14] = "wordwide";
		$hope[15] = "tgbyhnrf";
		$webdate = array();
		$webdate[0] = "20120321232312aa";
		$webdate[1] = "20120223223312bb";
		$webdate[2] = "20120509223312aa";
		$webdate[3] = "20120302223312bb";
		$webdate[4] = "20110418223312aa";
		$webdate[5] = "20120426223312bb";
		$webdate[6] = "20120312223312aa";
		$webdate[7] = "20100723223312bb";
		$webdate[8] = "20091211223312aa";
		$webdate[9] = "20080625223312bb";
		$webdate[10] = "20120508223312aa";
		$webdate[11] = "20120104223312bb";
		$webdate[12] = "20111103223312aa";
		$webdate[13] = "20110209223312bb";
		$webdate[14] = "20120323223312aa";
		$webdate[15] = "20090217223312bb";

		for($i = 0 ; $i < 16 ; $i++){
			if(i % 4 == 0){
				$desend.= $belief[$i].$webdate[$i].$hope[$i] % 16;
			}else if(i % 3 == 0){
				$desend.= CODEKEY1.CODEKEY4.CODEKEY7;
			}else if(i % 2 == 0){
				$desend.= CODEKEY2.CODEKEY5.CODEKEY8;
			}else{
				$desend.= CODEKEY3.CODEKEY6;
			}
		}

		$qus =  base64_encode(_encutment($desend));
		$edf = base64_encode(_decutment($desend));

		$key2 = bin2hex($enenkey); 
	    $decrypttext = @mcrypt_decrypt(MCRYPT_RIJNDAEL_128
	    						, hex2bin(_hash($qus).$key2)
	    						, hex2bin($param_input_string)
	    						, MCRYPT_MODE_CBC
								,_unhash($edf));
		return 	_fromPkcs7($decrypttext);
	}

function _hash($qaz) {  
	return codefunction($qaz);        
} 
function _unhash($aws) {  
	return aesrtfunction($aws);
} 

function _toPkcs7 ($value)      
    {                
    	if ( is_null ($value) )	$value = "" ;                
    	$padSize = 16 - (strlen ($value) % 16) ;                
    	return $value . str_repeat (chr($padSize), $padSize) ;        
    }                
    
function _fromPkcs7 ($value)        
    {                
    	$valueLen = strlen ($value) ;
    	if ( $valueLen % 16 > 0 )
    		$value = "";
    	$padSize = ord ($value{$valueLen - 1}) ;
    	if ( ($padSize < 1) or ($padSize > 16) )
    		$value = "";                // Check padding.                
    	for ($i = 0; $i < $padSize; $i++)                
    	{                        
    		if ( ord ($value{$valueLen - $i - 1}) != $padSize )
    			$value = "";                
    	}               
    	return substr ($value, 0, $valueLen - $padSize) ;        
    }

function codefunction($aes) {              
	return base64_decode($aes);        
} 

function aesrtfunction($edc) {              
	return hex2bin(base64_decode($edc));        
} 

/*private*/ function _requestImpayUnit($param_packet_body, $opcode, $cpid ) 
	{
		$impay_fd = fsockopen(SERVER_IP,
                          BANK_PORT);
        if (!$impay_fd)
        {
	   		$rtn = DAOU_SOCK_ERROR;
			error_log("socket cenenct fail.... ");
        }
		else
		{

			$daou_packet_lenght = strlen($param_packet_body) + 1 ;

			//요청헤더 생성
			$daou_packet_head = _makeHead($opcode,$cpid, REQUEST, $daou_packet_lenght );
			

			//요청헤더 + body + tail
			$daou_packet =  $daou_packet_head.$param_packet_body.chr(10);

			//daou_packet 보냄
			$rtn = fwrite($impay_fd, $daou_packet);
			error_log("packet send... ");

			if($rtn<=0){
				$rtn = DAOU_SOCK_ERROR;
				error_log("packet send fail... ");
			}
			
			//daou_packet 받음
			$cipher_read = fread($impay_fd, 1024);
			error_log("packet read... ");

			//ack헤더 생성	
			$daou_packet_ack =  _makeHead($opcode,$cpid, ACK, "1");

			//ack헤더 + tail
			$daou_packet_ack = $daou_packet_ack.chr(10);

			
			$rtn = fwrite($impay_fd, $daou_packet_ack);
			error_log("ACK packet send... ");

			if($rtn<=0){
				$rtn = DAOU_SOCK_ERROR;
				error_log("ACK packet send fail... ");
			}

			//소켓 닫기
			fclose($impay_fd);
			
	
			//head 와 tail 제거
			$rtn = substr($cipher_read,37,strlen($cipher_read)-38);
			
			
		}

		return $rtn;
	}

/*private*/ function _makeHead($opcode, $cpid, $opcodeState, $daou_packet_lenght ) {
		$daou_head = 
			str_pad($opcode.$opcodeState,10,STR_PAD_RIGHT).
			str_pad(VERSION,3,STR_PAD_RIGHT).
			str_pad($cpid,20," ",STR_PAD_RIGHT).
			str_pad($daou_packet_lenght,4,"0",STR_PAD_LEFT);
			
			return $daou_head;
}

function _MaxLength($strValue, $strValueLen){
		
		if(strlen($strValue) > $strValueLen){
			$strValue = substr($strValue,0,strValueLen);
		}
		return$strValue;
}

	    /* public */ function printline($paramString)
    {
           printf("[DEBUG]%s\n", $paramString);
    }
	

?>
