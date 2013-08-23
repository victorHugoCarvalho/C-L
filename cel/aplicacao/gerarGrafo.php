<?php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

chkUser("index.php");        // Checa se o usuario foi autenticado

$XML = "";

?>
<html>
<body>
    <head>
        <title>Gerar Grafo</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">        
    </head>

<?php

//Cenário -  Gerar Grafo 

//Objetivo:   Permitir ao administrador gerar o grafo de um projeto
//Contexto:   Gerente deseja gerar um grafo para uma das versões de XML
//Atores:     Administrador
//Recursos:   Sistema, XML, dados cadastrados do projeto, banco de dados.
//Episódios:  Restrição: Possuir um XML gerado do projeto

$bd_recupera = bd_connect() or die("Erro ao conectar ao SGBD");
$q = "SELECT * FROM publicacao WHERE id_projeto = '$id_projeto'";
$qrr = mysql_query($q) or die("Erro ao enviar a query");
?>
<h2>Gerar Grafo</h2><br>
<?php
while ( $result = mysql_fetch_row($qrr) )
{
   $data   = $result[1];
   $versao = $result[2];
   $XML    = $result[3];	
	?>
	<table>
	   <tr>
			<th>Versão:</th><td><?=$versao?></td>
			<th>Data:</th><td><?=$data?></td>
			<th><a href="mostraXML.php?id_projeto=<?=$id_projeto?>&versao=<?=$versao?>">XML</a></th>
			<th><a href="grafo\mostraGrafo.php?versao=<?=$versao?>&id_projeto=<?=$id_projeto?>">Gerar Grafo</a></th>
	                
	   </tr>
	</table>

	<?php
}
?>

<br><i><a href="showSource.php?file=recuperarXML.php">Veja o código fonte!</a></i>
    
</body>

</html>
