<html>

    <head>
        <title></title>
    </head>

    <body>
    
<?php


include_once("bd.inc") ;
include_once('auxiliar_bd.php');
session_start();

function converte_impactos()
{
	$link = bd_connect() or die("Erro na conex&atilde;o ao BD : " . mysql_error() . __LINE__);
	
	$filename = "teste.txt";
	
	$query  = "select * from lexico;";
	$result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
	
	if (!$handle = fopen($filename, 'w'))
	{
		print "Nao foi poss&iacute;vel abrir o arquivo !!!($filename)";
		exit;
	}
	
	// � importante escrever para o arquivo teste.txt para separar
	// impactos que est�o num mesmo impacto
	
	while($line = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$id_lexico = $line['id_lexico'];
		$impacto = $line['impacto'];
		
		if (!fwrite($handle, "@\r\n$id_lexico\r\n"))
		{
			print "Cannot write to file ($filename)";
			exit;
		}
		
		else if (!fwrite($handle, "$impacto\r\n"))
		{
			print "Cannot write to file ($filename)";
			exit;
		}
		else
		{
			//Nothing to do.
		}
	}
	
	fclose($handle);
	
	mysql_query("delete from impacto;");
	
	$lines = file ($filename);
	
	$pegar_id = "FALSE";
	$id_lexico = 0;
	
	foreach ($lines as $line_num => $line)
	{
		if($line[0] == '@')
		{
			$pegar_id = 1;
			continue;
		}
		else
		{
			//Nothing to do.
		}
		
		if($pegar_id)
		{
			$id = sscanf($line,"%d");
			$id_lexico = $id[0];
			$pegar_id = 0;
			continue;
		}
		else
		{
			//Nothing to do.
		}
            
		print ($line . "<br>\n" );
		if( strcmp(trim($line),"") != 0 )
		{		
			$query  = "insert into impacto (id_lexico, impacto) values ('$id_lexico', '$line');";
			$result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . " " . $line ." ". $id_lexico . " " . __LINE__);
		}
		else
		{
			//Nothing to do.
		}
	}
	
	$query  = "select * from impacto order by id_lexico;";
	$result = mysql_query($query) or die("A consulta ao BD falhou : " . mysql_error() . __LINE__);
	$result2 = mysql_num_rows($result);
	
	mysql_close($link);
}
?>
</body>
</html>