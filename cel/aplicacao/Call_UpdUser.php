<?php
session_start();

include_once("bd.inc");

$r = bd_connect() or die("Erro ao conectar ao SGBD");

// Cenário - Alterar cadastro
//
//Objetivo:	 Permitir ao usuário realizar alteração nos seus dados cadastrais	
//Contexto:	 Sistema aberto, Usuário ter acessado ao sistema e logado 
//           Usuário deseja alterar seus dados cadastrais 
//           Pré-Condição: Usuário ter acessado ao sistema	
//Atores:	 Usuário, Sistema.	
//Recursos:	 Interface	
//Episódios: O sistema fornecerá para o usuário uma tela com os seguintes campos de texto,
//           preenchidos com os dados do usuário,  para serem alterados:
//           nome, email, login, senha e confirmação da senha; e um botão de atualizar
//           as informações fornecidas

$id_usuario = $_SESSION['id_usuario_corrente'];


$q = "SELECT * FROM usuario WHERE id_usuario='$id_usuario'";

$qrr = mysql_query($q) or die("Erro ao executar a query");

  $row = mysql_fetch_row($qrr);
  $nome  = $row[1];
  $email = $row[2];
  $login = $row[3];
  $senha = $row[4];


?>
<html>
    <head>
        <title>Alterar dados de Usuário</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>

    <script language="JavaScript">
<!--
function TestarBranco(form)
{
	login      = form.login.value;
	senha      = form.senha.value;
	senha_conf = form.senha_conf.value;
	nome       = form.nome.value;
	email      = form.email.value;

	if (login == "")
	{ 
		alert ("Por favor, digite o seu Login.")
		form.login.focus()
      	return false;
    }
	if (email == "")
	{
		alert ( "Por favor, digite o seu e-mail.")
      	form.email.focus();
      	return false;
   	}
  	if (senha == "")
    { 
  	    alert ("Por favor, digite a sua senha.")
      	form.senha.focus()
      	return false;
    }
    if (nome == "")
    { 
        alert ("Por favor, digite o seu nome.")
      	form.nome.focus()
      	return false;
    }
   	if (senha != senha_conf)
   	{
      	alert ( "A senha e a confirmacao nao sao as mesmas!")
      	form.senha.focus();
      	return false;
   	}
}


function checkEmail(email)
{
  	if(email.value.length > 0)
  	{
     	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value))
     	{
        	return (true)
     	}
     	
		alert("Atenção: o E-mail digitado não é válido.")
		email.focus();
		email.select();
		return (false)
  	}
}


//-->
</SCRIPT>
    <body>
    <h3 style="text-align: center">Por favor, preencha os dados abaixo:</h3>
        <form action="updUser.php" method="post">
        <table>
            <tr>
                <td>Nome:</td><td colspan="3"><input name="nome" maxlength="255" size="48" type="text" value="<?=$nome?>"></td>
            </tr>
            <tr>
                <td>E-mail:</td><td colspan="3"><input name="email" maxlength="64" size="48" type="text" value="<?=$email?>" OnBlur="checkEmail(this)"></td>
            </tr>
            <tr>
                <td>Login:</td><td><input name="login" maxlength="32" size="24" type="text" value="<?=$login?>"></td>
            </tr>
            <tr>
                <td>Senha:</td><td><input name="senha" maxlength="32" size="16" type="password" value=""></td>
			</tr>
			<tr>
				<td>Senha (confirmação):</td><td><input name="senha_conf" maxlength="32" size="16" type="password" value=""></td>
            </tr>
            <tr>
                <td align="center" colspan="4" height="40" valign="bottom"><input name="submit" onClick="return TestarBranco(this.form);" type="submit" value="Atualizar"></td>
            </tr>
        </table>
        </form>
        <br><i><a href="showSource.php?file=Call_UpdUser.php">Veja o código fonte!</a></i>
     </body>
</html>