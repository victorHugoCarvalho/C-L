<?php

session_start();

include("funcoes_genericas.php");
include("httprequest.inc");

//chkUser("index.php");        // Cenario: controle de acesso


//Cenário  -  Remover Projeto 

//Objetivo:	   Permitir ao Administrador do projeto remover um projeto
//Contexto:	   Um Administrador de projeto deseja remover um determinado projeto da base de dados
//                 Pré-Condição: Login, Ser administrador do projeto selecionado.  
//Atores:	   Administrador
//Recursos:	   Sistema, dados do projeto, base de dados
//Episódios:   O Administrador clica na opção “remover projeto” encontrada no menu superior.
//             O sistema disponibiliza uma tela para o administrador ter certeza de que esta removendo o projeto correto.
//             O Administrador clica no link de remoção.
//             O sistema chama a página que removerá o projeto do banco de dados.
?>
<html>
    <head>
        <title>Remover Projeto</title>
    </head>
<?php
      
        $id_projeto = $_SESSION['id_projeto_corrente'];
        $id_usuario = $_SESSION['id_usuario_corrente'];
      
        $r = bd_connect() or die("Erro ao conectar ao SGBD");  
        $qv = "SELECT * FROM projeto WHERE id_projeto = '$id_projeto' "; 
        $qvr = mysql_query($qv) or die("Erro ao enviar a query de select no projeto");        
        $resultArrayProjeto = mysql_fetch_array($qvr);
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
<br><br>
<center><b>Cuidado!O projeto será apagado para todos seus usuários!</b></center>
<p><br>
  <center><a href="remove_projeto_base.php">Apagar o projeto</a></center> 
</p>
<p>
  <i><a href="showSource.php?file=remove_projeto.php">Veja o código fonte!</a></i> 
</p>
</body>
</html>

