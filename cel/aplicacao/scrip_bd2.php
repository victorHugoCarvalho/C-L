<html>
<head>
<title></title>
</head>

<body>
<?php 

include 'auxiliar_bd.php';
include_once("bd.inc");
include_once("CELConfig/CELConfig.inc");

$link = bd_connect() or die("Erro na conexão à BD : " . mysql_error() . __LINE__);

if ($link && mysql_select_db(CELConfig_ReadVar("BD_database")))
{
    echo "SUCESSO NA CONEXÃO À BD <br>";
} 
else 
{
	echo "ERRO NA CONEXÃO À BD <br>"; 
}



//$filename = "teste.txt"; 

     /* 
if (!$handle = fopen($filename, 'w')) 
{ 
      print "Nao foi possível abrir o arquivo !!!($filename)"; 
      exit; 
} 


while($line = mysql_fetch_array($result, MYSQL_ASSOC)) 
{ 
//    $id_impacto = $line['id_impacto']; 
    $id_lexico = $line['id_lexico']; 
    $impacto = $line['impacto']; 

    if (!fwrite($handle, "@\r\n$id_lexico\r\n")) 
    { 
        print "Cannot write to file ($filename)"; 
        exit; 
    } 

    if (!fwrite($handle, "$impacto\r\n")) 
    { 
        print "Cannot write to file ($filename)"; 
        exit; 
    } 

} 

fclose($handle);*/ 

/*
mysql_query("delete from impacto;"); 

$lines = file ("teste.txt"); 

$pegar_id = "FALSE"; 
$id_lexico = 0; 

foreach ($lines as $line_num => $line) 
{ 

    if($line[0] == '@') 
    { 
        $pegar_id = 1; 
        continue; 
    } 
    if($pegar_id) 
    { 
        $id = sscanf($line,"%d"); 
        $id_lexico = $id[0]; 
        $pegar_id = 0; 
        continue; 
    } 

    //$aux = sscanf($line,"%s"); 
    //$impacto = $aux[0]; 
    $query  = "insert into impacto (id_lexico, impacto) values ('$id_lexico', '$line');"; 
    $result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error());; 
} 

$query  = "select * from impacto;"; 
$result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error()); 

$result2 = mysql_num_rows($result); 


$query  = "select * from impacto order by id_lexico;"; 
$result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error()); 
$result2 = mysql_num_rows($result); 
print "<br>TOTAL DE IMPACTOS: "; 
print $result2; 
print "<br><br>"; 

echo "LISTA DE IMPACTOS<br>"; 

while($line = mysql_fetch_array($result, MYSQL_ASSOC)) 
{ 
    $id_impacto = $line['id_impacto']; 
    $id_lexico = $line['id_lexico']; 
    $impacto = $line['impacto']; 
    print "$id_impacto  $id_lexico  $impacto <br>"; 
    //print "$impacto<br><br>"; 
} 

*/
/*
$query = "alter table conceito drop pai;";
$result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);

$query = "create table hierarquia (id_hierarquia int(11) not null AUTO_INCREMENT,
                                        id_projeto int(11) not null ,
										id_conceito int(11) not null ,
                                        id_subconceito int(11) not null ,
                                        primary key(id_hierarquia, id_projeto, id_conceito, id_subconceito)
                                        );";
$result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);
 */
 /*
$query = "alter table algoritmo add id_projeto int default 30";
$result = mysql_query($query) or die("A criação de id_projeto falhou : " . mysql_error() . __LINE__); 

$query = "alter table algoritmo add constraint fk_id_projeto foreign key (id_projeto) references projeto(id_projeto);" ;
$result = mysql_query($query) or die("A criação de id_projeto falhou : " . mysql_error() . __LINE__); 
*/

$query  = "alter table conceito add namespace varchar(250) NULL after descricao;";
$result = mysql_query($query) or die("A consulta à BD falhou : " . mysql_error() . __LINE__);


echo "<br>FIM !!!"; 


mysql_close($link); 

?>
</body>
</html>
