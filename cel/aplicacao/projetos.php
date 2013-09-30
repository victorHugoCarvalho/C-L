<?php

include("funcoes_genericas.php");


?>
<html>
<head>
<p style="color: red; font-weight: bold; text-align: center"><img src="Images/Logo_CEL.jpg" width="180" height="100"><br/>
  <br/>
  Projetos Publicados</p>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head><body>
<?php

$bd_recupera = bd_connect() or die("Erro ao conectar ao SGBD");

//Cen�rio - Escolher Projeto

//Objetivo:   Permitir ao Administrador/Usu�rio escolher um projeto.
//Contexto:   O Administrador/Usu�rio deseja escolher um projeto.
//            Pr�-Condi��es: Login, Ser Administrador
//Atores:     Administrador, Usu�rio
//Recursos:   Usu�rios cadastrados
//Epis�dios:  Caso o Usuario selecione da lista de projetos um projeto da qual ele seja administrador,
//            ver ADMINISTRADOR ESCOLHE PROJETO.
//            Caso contr�rio, ver USU�RIO ESCOLHE PROJETO.

$query = "SELECT * FROM publicacao";
$ExecuteQuery = mysql_query($query) or die("Erro ao enviar a query de busca");

?>
<?php
while ( $result = mysql_fetch_row($ExecuteQuery) )
{
   $id_projeto = $result[0];
   $data       = $result[1];
   $versao     = $result[2];
   $XML        = $result[3];

   $qProcuraNomeProjeto = "SELECT * FROM projeto WHERE id_projeto = '$id_projeto'";
   $qrrProcura = mysql_query($qProcuraNomeProjeto) or die("Erro ao enviar a query de busca de projeto");
   $resultNome = mysql_fetch_row($qrrProcura);
   $nome_projeto = $resultNome[1];


	?>
	<table border='0'>
	  <tr>
	    <th height="29" width="140"><a href="mostrarProjeto.php?id_projeto=<?=$id_projeto?>&versao=<?=$versao?>">
	      <?=$nome_projeto?>
	      </a></th>
	    <th height="29" width="140">Data:
	      <?=$data?></th>
	    <th height="29" width="100">Vers&atilde;o:
	      <?=$versao?></th>
	  </tr>
	</table>
	<?php
}

?>
</body>
</html>