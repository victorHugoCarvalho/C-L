<?php
session_start();

include_once("bd.inc");

$connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");

//  Scenery - Change register

//  Purpose: Allow user to make changes in your registration data
//  Context: Open System, User have accessed the system and logged
//  User want to change your registration
//  Precondition: User has accessed the system
//  Actors: User, System.
//  Features: Interface
//  Episodes: The system provides the user a screen with the following text fields,
//  pilled with user data to be changed:
//  name, email, login, password and confirm password, and a button to update
//  the information provided

$id_usuario = $_SESSION['id_usuario_corrente'];


$query = "SELECT * FROM usuario WHERE id_usuario='$id_usuario'";

$ExecuteQuery = mysql_query($query) or die("Erro ao executar a query");

  $row = mysql_fetch_row($ExecuteQuery);
  $nome  = $row[1];
  $email = $row[2];
  $login = $row[3];
  $senha = $row[4];


?>
<html>
	<head>
	<title>Alterar dados de Usu&aacute;rio</title>
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
			else
			{
			    //Nothing to do.
			}
			
			if (email == "")
			{
				alert ( "Por favor, digite o seu e-mail.")
                                form.email.focus();
                                return false;
		   	}
			else
			{
			    //Nothing to do.
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
                        else
                        {
                            //Nothing to do.
                        }
                        
		   	if (senha != senha_conf)
		   	{
                            alert ( "A senha e a confirmacao nao sao as mesmas!")
                            form.senha.focus();
                            return false;
		   	}
		   	else
		   	{
		   	    //Nothing to do.
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
                            else
                            {
                                //Nothing to do.
                            }
		     	
                            alert("Aten��o: o E-mail digitado n�o � v�lido.")
                            email.focus();
                            email.select();
                            return (false)
		  	}
		  	else
		  	{
		  	    //Nothing to do.
		  	}
		}
		
		
		//-->
	</SCRIPT>
	
	<body>
		<h3 style="text-align: center">Por favor, preencha os dados abaixo:</h3>
		<form action="updUser.php" method="post">
		  <table>
		    <tr>
		      <td>Nome:</td>
		      <td colspan="3"><input name="nome" maxlength="255" size="48" type="text" value="<?=$nome?>"></td>
		    </tr>
		    <tr>
		      <td>E-mail:</td>
		      <td colspan="3"><input name="email" maxlength="64" size="48" type="text" value="<?=$email?>" OnBlur="checkEmail(this)"></td>
		    </tr>
		    <tr>
		      <td>Login:</td>
		      <td><input name="login" maxlength="32" size="24" type="text" value="<?=$login?>"></td>
		    </tr>
		    <tr>
		      <td>Senha:</td>
		      <td><input name="senha" maxlength="32" size="16" type="password" value=""></td>
		    </tr>
		    <tr>
		      <td>Senha (confirma&ccedil;&atilde;o):</td>
		      <td><input name="senha_conf" maxlength="32" size="16" type="password" value=""></td>
		    </tr>
		    <tr>
		      <td align="center" colspan="4" height="40" valign="bottom"><input name="submit" onClick="return TestarBranco(this.form);" type="submit" value="Atualizar"></td>
		    </tr>
		  </table>
		</form>
		<br>
		<i><a href="showSource.php?file=Call_UpdUser.php">Veja o c&oacute;digo fonte!</a></i>
	</body>
</html>