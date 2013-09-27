<?php

// Escapes metacharacters of PHP

function escapa_metacaracteres ( $string )
{
	$string = ereg_replace("[][{}()*+?.\\^$|]", "\\\\0", $string);
	return $string;
}

function prepara_dado( $string ) 
{
    //Removes whitespace from the beginning and end of string
    //$string = trim( $string );
	
	//Replace & with amp; (lest problems when generating the XML)
	
	$string = ereg_replace("&", "&amp;", $string);
	
	//Removes html tags and php string
	$string = strip_tags($string);
	
	//Checks whether the policy get_magic_quotes_gpc () is enabled, 
	//if the function is used in the string stripslashes.
	
	$string = get_magic_quotes_gpc() ? stripslashes($string) : $string;
	$string = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($string) : mysql_escape_string($string);
	return $string;
}

?>
