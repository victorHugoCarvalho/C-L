<?php

session_start();

include_once ("dataBase/dataBaseProject.php");
include("funcoes_genericas.php");
include_once("CELConfig/CELConfig.inc");



//  Scenery - Remove Project base
//  Purpose: Perform removal of a design database
//  Context: A Project Manager you want to remove a particular project database
//  Precondition: Login Become administrator selected project, the project has selected for removal in remove_project.php.
//  Actors: Administrator
//  Resources: System, design data, database
//  Episodes: The system deletes all data on the particular design of your database.


	$id_projeto = $_SESSION['id_projeto_corrente'];
        
    removeProject($id_projeto);    
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
