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
<title>Recuperar XML</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<?php

//Cen�rio -  Gerar Relat�rios XML 

//Objetivo:   Permitir ao administrador gerar relat�rios em formato XML de um projeto,
//             identificados por data.
//Contexto:   Gerente deseja gerar um relat�rio para um dos projetos da qual � administrador.
//              Pr�-Condi��o: Login, projeto cadastrado.
//Atores:     Administrador
//Recursos:   Sistema, dados do relat�rio, dados cadastrados do projeto, banco de dados.
//Epis�dios:  Restri��o: Recuperar os dados em XML do Banco de dados e os transformar
//                       por uma XSL para a exibi��o.

$bd_recupera = bd_connect() or die("Erro ao conectar ao SGBD");
if (isset($apaga))
{
	if ( $apaga )
	{
		$qApaga = "DELETE FROM publicacao WHERE id_projeto = '$id_projeto' AND versao = '$versao' ";
		$qrrApaga = mysql_query($qApaga);	
	}
}
$q = "SELECT * FROM publicacao WHERE id_projeto = '$id_projeto'";
$qrr = mysql_query($q) or die("Erro ao enviar a query");
?>
<h2>Recupera XML/XSL</h2>
<br>
<?php
while ( $result = mysql_fetch_row($qrr) )
{
   $data   = $result[1];
   $versao = $result[2];
   $XML    = $result[3];	
	?>
	<table>
	  <tr>
	    <th>Vers�o:</th>
	    <td><?=$versao?></td>
	    <th>Data:</th>
	    <td><?=$data?></td>
	    <th><a href="mostraXML.php?id_projeto=<?=$id_projeto?>&versao=<?=$versao?>">XML</a></th>
	    <th><a href="recuperarXML.php?id_projeto=<?=$id_projeto?>&versao=<?=$versao?>&apaga=true">Apaga XML</a></th>
	  </tr>
	</table>
	<?php
}

?>
<br>
<i><a href="showSource.php?file=recuperarXML.php">Veja o c&oacute;digo fonte!</a></i>
</body>
</html>
