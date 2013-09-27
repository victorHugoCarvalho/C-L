<?php

include_once "CELConfig/CELConfig.inc" ;

function bd_connect()
{
    $ipValor    = CELConfig_ReadVar("BD_ip") ;
	$BDuser     = CELConfig_ReadVar("BD_user") ;
	$BDpassword = CELConfig_ReadVar("BD_password") ;
	$BDdatabase = CELConfig_ReadVar("BD_database") ;

	$connected_SGBD = mysql_pconnect( $ipValor , $BDuser , $BDpassword ) or die( "Erro na conex&atilde;o ao BD : " . mysql_error() ) ;
	
	if ($connected_SGBD && mysql_select_db($BDdatabase))	
	{
		return ($connected_SGBD);
	}
	else 
	{
		return (FALSE);
	}
}

?>
