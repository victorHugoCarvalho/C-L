<?php

include("funcoes_genericas.php");
include("httprequest.inc");

//  Setting - Choosing Project

//  Purpose: Allows the Administrator / User choose a design.
//  Context: The Administrator / User want to choose a design.
//  Preconditions: Login Become Administrator
//  Actors: Administrator, User
//  Features: Registered Users
//  Episodes: If you select the User from the list of projects a project of which he is an administrator,
//  SINGLE PROJECT ADMINISTRATOR see.
//  Otherwise, see USER CHOOSES DESIGN.
   
$bd_recupera = bd_connect() or die("Erro ao conectar ao SGBD");
   
$query = "select * from publicacao where id_projeto = $id_projeto AND versao = $versao";
$ExecuteQuery = mysql_query($query) or die("Erro ao enviar a query");
$row = mysql_fetch_row($ExecuteQuery);
$xml_banco = $row[3];

echo $xml_banco;
	
?>
