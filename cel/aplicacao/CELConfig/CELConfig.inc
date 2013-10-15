<?php
/***************************************************************
 * M�dulo de Configura��o do CEL e seus Plugins.               *
 * Arquivos: CELConfig.inc , config2.conf                      *
 * Autor:    Alex Marques Campos                               *
 * Data:     setembro de 2003                                  *
 * Vers�o:   0.1                                               *
 ***************************************************************/

	
/* Esta fun��o retorna o valor contido na variavel $var, registrada no arquivo $config_file */
function CELConfig_ReadVar( $var )
{
	$config_file = "config2.conf" ;

	/* Parse do arquivo de configura��es, sem as se��es */
	$array_configuracoes = parse_ini_file( $config_file ) ;
        /* print_r($array_configuracoes); */

	/* Parse do arquivo de configura��es, com as se��es */
	/*
		$array_configuracoes_por_secoes = parse_ini_file( $config_file , TRUE ) ;
		print_r($array_configuracoes_por_secoes) ;
	*/

	/* Se a variavel n�o estiver incluida no arquivo de configura��es... */
	if ( !array_key_exists( $var , $array_configuracoes ) )
	{
	    /* ... exiba uma mensagem e termine o script atual */
	    print( "A variavel \"<STRONG>$var</STRONG>\" n&atilde;o foi registrada no ambiente.<BR>\nPor favor verifique o arquivo \"<STRONG>$config_file</STRONG>\" para mais detalhes." );
	    exit() ;
	}

	/* Se passou, a variavel existe! */

	// print( "$var = $array_configuracoes[$var] <BR>\n" ) ; 	/* DEBUG */

	return $array_configuracoes[$var] ;   /* retorna o conte�do da vari�vel */
}

function CELConfig_DisplayVars()
{
	$config_file = "config2.conf" ;
	$array_configuracoes = parse_ini_file( $config_file ) ;
	print_r( array_keys($array_configuracoes) ); 
}

?>
