<?php

session_start(); 

include("funcoes_genericas.php");
include("httprequest.inc");

// Cenário controle de acesso
chkUser("index.php");        

// Este script eh chamado quando ocorre uma solicitacao de inclusao
// de novo projeto, ou quando um novo usuario se cadastra no sistema


//Cenário  -  Cadastrar Novo Projeto 
//Objetivo:	   Permitir ao usuário cadastrar um novo projeto
//Contexto:	   Usuário deseja incluir um novo projeto na base de dados
//              Pré-Condição: Login  
//Atores:	   Usuário
//Recursos:	   Sistema, dados do projeto, base de dados
//Episódios:   O Usuário clica na opção “adicionar projeto” encontrada no menu superior.
//             O sistema disponibiliza uma tela para o usuário especificar os dados do novo projeto,
//              como o nome do projeto e sua descrição.
//             O usuário clica no botão inserir.
//             O sistema grava o novo projeto na base de dados e automaticamente constrói a Navegação
//              para este novo projeto.
//Exceção:	   Se for especificado um nome de projeto já existente e que pertença ou tenha a participação
//                 deste usuário, o sistema exibe uma mensagem de erro.

// Chamado atraves do botao de submit
if (isset($submit)) {    

	$id_projeto_incluido = inclui_projeto($nome, $descricao);
    
	// Inserir na tabela participa
    
	if ($id_projeto_incluido != -1 )
    {
	    $r = bd_connect() or die("Erro ao conectar ao SGBD");
	    $gerente = 1;
	    $id_usuario_corrente = $_SESSION['id_usuario_corrente'];    
	    $q = "INSERT INTO participa (id_usuario, id_projeto, gerente) VALUES ($id_usuario_corrente, $id_projeto_incluido, $gerente  )";
	    mysql_query($q) or die("Erro ao inserir na tabela participa");
    }
    else
    {
 ?>
 <html>
 <title>Erro</title>
 <body>
    <p style="color: red; font-weight: bold; text-align: center">Nome de projeto já existente!</p>
    <center><a href="JavaScript:window.history.go(-1)">Voltar</a></center>
 </body>
 </html>   
 <?php  	
    	return;
    }	    
?>

<script language="javascript1.3">

self.close();

</script>

<?php
// Chamado normalmente
} else {        
?>

<html>
    <head>
        <title>Adicionar Projeto</title>
        <script language="javascript1.3">

        function chkFrmVals() {
            if (document.forms[0].nome.value == "") {
                alert('Preencha o campo "Nome"');
				document.forms[0].nome.focus();
                return false;
            }else{
				padrao = /[\\\/\?"<>:|]/;
				nOK = padrao.exec(document.forms[0].nome.value);
				if (nOK)
				{
					window.alert ("O nome do projeto não pode conter nenhum dos seguintes caracteres:   / \\ : ? \" < > |");
					document.forms[0].nome.focus();
					return false;
				}	 
			}
            return true;
        }

        </script>
    </head>
    <body>
        <h4>Adicionar Projeto:</h4>
        <br>
        <form action="" method="post" onSubmit="return chkFrmVals();">
        <table>
            <tr>
                <td>Nome:</td>
                <td><input maxlength="128" name="nome" size="48" type="text"></td>
            </tr>
            <tr>
                <td>Descrição:</td>
                <td><textarea cols="48" name="descricao" rows="4"></textarea></td>
            <tr>
                <td align="center" colspan="2" height="60"><input name="submit" type="submit" value="Adicionar Projeto"></td>
            </tr>
        </table>
        </form>
        <br><i><a href="showSource.php?file=add_projeto.php">Veja o código fonte!</a></i>
    </body>
</html>

<?php
}
?>
