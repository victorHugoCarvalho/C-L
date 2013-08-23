<?php
/***************************************************************
 * Módulo de Configuração do CEL e seus Plugins.               *
 * Arquivos: CELConfig.inc , config2.conf                      *
 * Autor:    Alex Marques Campos                               *
 * Data:     setembro de 2003                                  *
 * Versão:   0.1                                               *
 ***************************************************************/

	
/* Esta função retorna o valor contido na variavel $var, registrada no arquivo $config_file */
function CELConfig_ReadVar( $var )
{
	$config_file = "CELConfig/config2.conf" ;

	/* Parse do arquivo de configurações, sem as seções */
	$array_configuracoes = parse_ini_file( $config_file ) ;
        /* print_r($array_configuracoes); */

	/* Parse do arquivo de configurações, com as seções */
	/*
		$array_configuracoes_por_secoes = parse_ini_file( $config_file , TRUE ) ;
		print_r($array_configuracoes_por_secoes) ;
	*/

	/* Se a variavel não estiver incluida no arquivo de configurações... */
	if ( !array_key_exists( $var , $array_configuracoes ) )
	{
	    /* ... exiba uma mensagem e termine o script atual */
	    print( "A variavel \"<STRONG>$var</STRONG>\" n&atilde;o foi registrada no ambiente.<BR>\nPor favor verifique o arquivo \"<STRONG>$config_file</STRONG>\" para mais detalhes." );
	    exit() ;
	}

	/* Se passou, a variavel existe! */

	// print( "$var = $array_configuracoes[$var] <BR>\n" ) ; 	/* DEBUG */

	return $array_configuracoes[$var] ;   /* retorna o conteúdo da variável */
}

function CELConfig_DisplayVars()
{
	$config_file = "config2.conf" ;
	$array_configuracoes = parse_ini_file( $config_file ) ;
	print_r( array_keys($array_configuracoes) ); 
}

?>
