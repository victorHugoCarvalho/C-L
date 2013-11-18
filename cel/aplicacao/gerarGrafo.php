<?php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

checkUser("index.php");        // checks whether the user has been authenticated  

$XML = "";

?>
<html>
<body>
<head>
<title>Gerar Grafo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<?php

//     Scenario: Generate Graph 
//     Purpose: Allow the administrator to generate the graph of a project
//     Context: Manager to generate a graph for one of the versions of XML
//     Actors: Administrator
//     Resources: System, XML, data registered design, database.
//     Episodes: Restriction: Owning a generated XML project

$bd_recupera = bd_connect() or die("Erro ao conectar ao SGBD");
$query = "SELECT * FROM publicacao WHERE id_projeto = '$id_projeto'";
$ExecuteQuery = mysql_query($query) or die("Erro ao enviar a query");
?>
<h2>Gerar Grafo</h2>
<br>
<?php
while ( $result = mysql_fetch_row($ExecuteQuery) )
{
   $data   = $result[1];
   $versao = $result[2];
   $XML    = $result[3];	
	?>
<table>
  <tr>
    <th>Vers&atilde;o:</th>
    <td><?=$versao?></td>
    <th>Data:</th>
    <td><?=$data?></td>
    <th><a href="mostraXML.php?id_projeto=<?=$id_projeto?>&versao=<?=$versao?>">XML</a></th>
    <th><a href="grafo\mostraGrafo.php?versao=<?=$versao?>&id_projeto=<?=$id_projeto?>">Gerar Grafo</a></th>
  </tr>
</table>
<?php
}
?>
<br>
<i><a href="showSource.php?file=recuperarXML.php">Veja o c&oacute;digo fonte!</a></i>
</body>
</html>
