<?php

include("funcoes_genericas.php");
include("httprequest.inc");

//Cenário  -  Escolher Projeto

//Objetivo:     Permitir ao Administrador/Usuário escolher um projeto.
//Contexto:     O Administrador/Usuário deseja escolher um projeto.
//              Pré-Condições: Login, Ser Administrador
//Atores:       Administrador, Usuário
//Recursos:     Usuários cadastrados
//Episódios:    Caso o Usuario selecione da lista de projetos um projeto da qual ele seja administrador,
//              ver ADMINISTRADOR ESCOLHE PROJETO.
//              Caso contrário, ver USUÁRIO ESCOLHE PROJETO.
   
$bd_recupera = bd_connect() or die("Erro ao conectar ao SGBD");
   
$qq = "select * from publicacao where id_projeto = $id_projeto AND versao = $versao";
$qrr = mysql_query($qq) or die("Erro ao enviar a query");
$row = mysql_fetch_row($qrr);
$xml_banco = $row[3];

echo $xml_banco;
	
?>
