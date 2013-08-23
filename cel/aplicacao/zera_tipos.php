<?php

        include 'bd.inc';

	$link = bd_connect();

	
	$query = "update lexico set tipo =  NULL;";
	$result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error());
	
	mysql_close($link);

?>