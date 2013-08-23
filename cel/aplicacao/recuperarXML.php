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

//Cenário -  Gerar Relatórios XML 

//Objetivo:   Permitir ao administrador gerar relatórios em formato XML de um projeto,
//             identificados por data.
//Contexto:   Gerente deseja gerar um relatório para um dos projetos da qual é administrador.
//              Pré-Condição: Login, projeto cadastrado.
//Atores:     Administrador
//Recursos:   Sistema, dados do relatório, dados cadastrados do projeto, banco de dados.
//Episódios:  Restrição: Recuperar os dados em XML do Banco de dados e os transformar
//                       por uma XSL para a exibição.

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
<h2>Recupera XML/XSL</h2><br>
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
                <th><a href="recuperarXML.php?id_projeto=<?=$id_projeto?>&versao=<?=$versao?>&apaga=true">Apaga XML</a></th>
                
   </tr>


</table>

<?php
}

?>

<br><i><a href="showSource.php?file=recuperarXML.php">Veja o código fonte!</a></i>
    
    </body>

</html>
