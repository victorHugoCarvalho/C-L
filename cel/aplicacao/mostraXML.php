<?php

session_start();
include("funcoes_genericas.php");
include("httprequest.inc");

chkUser("index.php");        // Checa se o usuario foi autenticado
   
$bd_recupera = bd_connect() or die("Erro ao conectar ao SGBD");

//Cenário -  Gerar Relatórios XML 

//Objetivo:	Permitir ao administrador gerar relatórios em formato XML de um projeto,
//              identificados por data.
//Contexto:   Gerente deseja gerar um relatório para um dos projetos da qual é administrador.
//              Pré-Condição: Login, projeto cadastrado.
//Atores:	  Administrador
//Recursos:	  Sistema, dados do relatório, dados cadastrados do projeto, banco de dados.
//Episódios:  Gerando com sucesso o relatório a partir dos dados cadastrados do projeto,
//             o sistema fornece ao administrador a tela de visualização do relatório
//             XML criado.
   
$qq = "select * from publicacao where id_projeto = $id_projeto AND versao = $versao";
$qrr = mysql_query($qq) or die("Erro ao enviar a query");
$row = mysql_fetch_row($qrr);
$xml_banco = $row[3];

echo $xml_banco;
	
?>
