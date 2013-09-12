	<?php
session_start();

include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

$id_usuario = $_SESSION['id_usuario_corrente'];

$connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");


?>
<html>
<head>
<title>Alterar dados de Usuário</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?php

// Cenário - Alterar cadastro
//
//Objetivo:  Permitir ao usuário realizar alteração nos seus dados cadastrais	
//Contexto:  Sistema aberto, Usuário ter acessado ao sistema e logado 
//           Usuário deseja alterar seus dados cadastrais 
//           Pré-Condição: Usuário ter acessado ao sistema	
//Atores:    Usuário, Sistema.	
//Recursos:  Interface	
//Episódios: O usuário altera os dados desejados
// 	     Usuário clica no botão de atualizar

$senha_script = md5($senha);
$query = "UPDATE usuario SET  nome ='$nome' , login = '$login' , email = '$email' , senha = '$senha_script' WHERE  id_usuario='$id_usuario'";

mysql_query($query) or die("<p style='color: red; font-weight: bold; text-align: center'>Erro!Login ja existente!</p><br><br><center><a href='JavaScript:window.history.go(-1)'>Voltar</a></center>");

?>
<center>
  <b>Cadastro atualizado com sucesso!</b>
</center>
<center>
  <button onClick="javascript:window.close();">Fechar</button>
</center>
</body>
</html>