<?php

session_start();

include("funcoes_genericas.php");
include_once("CELConfig/CELConfig.inc");



//Cen�rio  -  Remover Projeto da base

//Objetivo:	   Efetuar a remo��o de um projeto da base de dados
//Contexto:	   Um Administrador de projeto deseja remover um determinado projeto da base de dados
//                 Pr�-Condi��o: Login, Ser administrador do projeto selecionado, ter selecionado o projeto para remo��o em remove_projeto.php.  
//Atores:	   Administrador
//Recursos:	   Sistema, dados do projeto, base de dados
//Epis�dios:       O sistema apaga todos os dados referentes ao determinado projeto da sua base de dados.


	$id_projeto = $_SESSION['id_projeto_corrente'];
        
    removeProjeto($id_projeto);    
?>
<html>
<script language="javascript1.3">

function logoff()
{		
   location.href = "http://<?php print( CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") ); ?>index.php";
}

</script>
<head>
	<title>Remover Projeto</title>
</head>

	<body>
		<center>
		  <b>Projeto apagado com sucesso.</b>
		</center>
		<p> <a href="javascript:logoff();">Clique aqui para Sair</a> </p>
		<p> <i><a href="showSource.php?file=remove_projeto_base.php">Veja o c&oacute;digo fonte!</a></i> </p>
	</body>
</html>
