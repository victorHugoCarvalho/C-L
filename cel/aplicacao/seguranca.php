<?php

// Escapa os metacaracteres do PHP

function escapa_metacaracteres ( $string )
{
	$string = ereg_replace("[][{}()*+?.\\^$|]", "\\\\0", $string);
	return $string;
}

function prepara_dado( $string ) 
{
	//Remove espaços em branco do inicio e do fim da string
	//$string = trim( $string );
	
	// Substitui o & por amp; (para que não de problemas ao gerar o XML)
	
	$string = ereg_replace("&", "&amp;", $string);
	
	// Retira tags html e php da string
	
	$string = strip_tags($string);
	
	// Verifica se a diretiva get_magic_quotes_gpc() esta ativada, se estiver a função stripslashes é utilizada na string
	
	$string = get_magic_quotes_gpc() ? stripslashes($string) : $string;
	$string = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($string) : mysql_escape_string($string);
	return $string;
}

?>
