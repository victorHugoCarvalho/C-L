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
<title>Alterar dados de Usu&aacute;rio</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?php

// Scenery - Changing registration

// Purpose: Allow the user to perform changes in your registration data
// Context: Open System, User have accessed the system and logged
//         User want to change your registration
// Precondition: User has accessed the system	
// Actors: User, System.
// Features: Interface
// Episodes: The user changes the desired data
//          User clicks on the refresh button

$senha_script = md5($senha);
$query = "UPDATE usuario SET  nome ='$nome' , login = '$login' , email = '$email' , senha = '$senha_script' WHERE  id_usuario='$id_usuario'";

mysql_query($query) or die("<p style='color: red; font-weight: bold; text-align: center'>Erro!Login j&aacute; existente!</p><br><br><center><a href='JavaScript:window.history.go(-1)'>Voltar</a></center>");

?>
<center>
  <b>Cadastro atualizado com sucesso!</b>
</center>
<center>
  <button onClick="javascript:window.close();">Fechar</button>
</center>
</body>
</html>