<?php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

//chkUser("index.php");       


//  Scenery - Remove Project

//  Purpose: Allow Project Manager to remove a project
//  Context: A Project Manager you want to remove a particular project database
//  Precondition: Login Become administrator selected project.
//  Actors: Administrator
//  Resources: System, design data, database
//  Episodes: The Administrator clicks the option? Remove project? found in the top menu.
//  The system provides a screen for the administrator to make sure that removing the correct project.
//  The Administrator clicks on the link removal.
//  The system calls the page that will remove the project from the database.
  
?>
<html>
	<head>
		<title>Remover Projeto</title>
	</head>
<?php
      
        $id_projeto = $_SESSION['id_projeto_corrente'];
        $id_usuario = $_SESSION['id_usuario_corrente'];
      
        $connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");  
        $query = "SELECT * FROM projeto WHERE id_projeto = '$id_projeto' "; 
        $ExecuteQuery = mysql_query($query) or die("Erro ao enviar a query de select no projeto");        
        $resultArrayProjeto = mysql_fetch_array($ExecuteQuery);
        $nome_Projeto       = $resultArrayProjeto[1];
        $data_Projeto       = $resultArrayProjeto[2];
        $descricao_Projeto  = $resultArrayProjeto[3];  
  
        
        
?>
	<body>
		<h4>Remover Projeto:</h4>
		<p><br>
		</p>
		<table width="100%" border="0">
		  <tr>
		    <td width="29%"><b>Nome do Projeto:</b></td>
		    <td width="29%"><b>Data de cria&ccedil;&atilde;o</b></td>
		    <td width="42%"><b>Descri&ccedil;&atilde;o</b></td>
		  </tr>
		  <tr>
		    <td width="29%"><?php echo $nome_Projeto; ?></td>
		    <td width="29%"><?php echo $data_Projeto; ?></td>
		    <td width="42%"><?php echo $descricao_Projeto; ?></td>
		  </tr>
		</table>
		<br>
		<br>
		<center>
		  <b>Cuidado!O projeto ser&aacute; apagado para todos seus usu&aacute;rios!</b>
		</center>
		<p><br>
		  <center>
		    <a href="remove_projeto_base.php">Apagar o projeto</a>
		  </center>
		</p>
		<p> <i><a href="showSource.php?file=remove_projeto.php">Veja o c&oacute;digo fonte!</a></i> </p>
	</body>
</html>
