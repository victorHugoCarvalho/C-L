<?php
session_start();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<?php

include("funcoes_genericas.php");
include_once("bd.inc");

$firstTime = "true";

include("httprequest.inc");

if (isset($submit)) // Called by the submit button
{
    $firstTime = "false";
    // ** Scenario "Adding independet user" **
    // The system checks if all the fields are filled. If any field is not filled, 
    //   the system warns the user that all the field should be filled.  
    if ($nome == "" || $email == "" || $login == "" || $senha == "" || $senha_conf == "")
	{
        $p_style = "color: red; font-weight: bold";
        $p_text = "Por favor, preencha todos os campos.";
        recarrega("?p_style=$p_style&p_text=$p_text&nome=$nome&email=$email&login=$login&senha=$senha&senha_conf=$senha_conf&novo=$novo");
    }
    else
    {
    	// Check if the passwords typed by the user are equal
        if ($senha != $senha_conf)
        {
            $p_style = "color: red; font-weight: bold";
            $p_text = "Senhas diferentes. Favor preencher novamente as senhas.";
            recarrega("?p_style=$p_style&p_text=$p_text&nome=$nome&email=$email&login=$login&novo=$novo");
        }
        else
        {

            // ** Cenario "Inclusao de Usuario Independente" **
            // Todos os campos estao preenchidos. O sistema deve agora verificar
            // se ja nao existe alguem cadastrado com o mesmo login informado pelo usuario.

			// Cen�rio - Incluir usu�rio independente 
			
			// Objetivo:  Permitir um usu�rio, que n�o esteja cadastrado como administrador, se cadastrar 
			//            com o perfil de administrador	
			// Contexto:  Sistema aberto Usu�rio deseja cadastrar-se ao sistema como administrador. 
			//            Usu�rio na tela de cadastro de usu�rio 
			// Pr�-Condi��es: Usu�rio ter acessado ao sistema	
			// Atores:    Usu�rio, Sistema	
			// Recursos:  Interface, Banco de Dados	
			// Epis�dios: O sistema retorna para o usu�rio uma interface com campos para entrada de
			//            um Nome, email, login, uma senha e a confirma��o da senha.
			//            O usu�rio preenche os campos e clica em cadastrar 
			//            O sistema ent�o checa para ver se todos os campos est�o preenchidos.
			//              Caso algum campo deixar de ser preenchido, o sistema avisa que todos
			//               os campos devem ser preenchidos.
			//              Caso todos os campos estiverem preenchidos, o sistema checa no banco
			//               de dados para ver se esse login j� existe..
			//              Caso aquele login digitado j� exista, o sistema retorna a mesma p�gina
			//               para o usu�rio avisando que o usu�rio deve escolher outro login,.

            $connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");
            $query = "SELECT id_usuario FROM usuario WHERE login = '$login'";
            $SendQuery = mysql_query($query) or die("Erro ao enviar a query");
            if (mysql_num_rows($SendQuery)) // Se ja existe algum usuario com este login
            {        
				// Scenaro - Add User
				
				// Objective:  Allow the manager to create new users
				// Context:  The manager wants to add new users (not registered),
				//     creating new users to the selected project
				// Pre-Conditions:  Login
				// Actors:  Manager
				// Resources:  Users data
				// Episodes:  The manager clicks on the link Add User (not registered) on this project,
				//     typing the new users information: name, email, login and password.
				//     If the login already exists, show an error message on the screen
				//       informing that this login already exists

                ?>
				<script language="JavaScript">
                    alert ("Login j&aacute; existente no sistema. Favor escolher outro login.")
                </script>
				<?php
                recarrega("?novo=$novo");
            }
            else // Passed through all the test. Can be added to the DB
			{
				/* Replace all occurences of ">" and "<" for " " */
				$nome  = str_replace( ">" , " " , str_replace ( "<" , " " , $nome  ) ) ;
				$login = str_replace( ">" , " " , str_replace ( "<" , " " , $login ) ) ;
				$email = str_replace( ">" , " " , str_replace ( "<" , " " , $email ) ) ;
				
				// Encrypting the password
				$senha = md5($senha);
                $query = "INSERT INTO usuario (nome, login, email, senha) VALUES ('$nome', '$login', '$email', '$senha')";
                mysql_query($query) or die("Erro ao cadastrar o usuario");
                recarrega("?cadastrado=&novo=$novo&login=$login");
            }
        }   // else
    }   // else
} else if (isset($cadastrado))
{
    // Registration completed. Dependendo de onde o usuario veio,
    // devemos manda-lo para um lugar diferente.

    if ($novo == "true") // If the user came from the login screen
    {

        // ** Scenario "Adding a new independent user" **
        // The user just registered in the system, we should redirect him 
        // to the adding projects part

        // Register that the user is logged with the newly registered login
		
		// Scenario - Add independent user
		
		// Objective:  Allow a not registered user, to register himself with the manager
		//     profile
		// Context:  Openen system. User wants to register himself in the system as a manager
		//     User on the user registration screen       
		// Pre-Conditions:  User must have accessed the system
		// Actors:  User, System	
		// Resources:  Interface, Database
		// Episodes:  If the typed login does not exist, the system register this user
		//     in the database as a manager, enabling:
		//       - Redirect him to the ADD NEW PROJECT screen

        $id_usuario_corrente = simple_query("id_usuario", "usuario", "login = '$login'");
        $_SESSION['id_usuario_corrente'] = $id_usuario_corrente;
		?>
		<script language="javascript1.3">

			// Redireciona o usuario para a parte de inclusao de projetos
			opener.location.replace('index.php');
			open('add_projeto.php', '', 'dependent,height=300,width=550,resizable,scrollbars,titlebar');
			self.close();

		</script>
		<?php
    }
    else
	{
	    // ** Scenario "Editing user" **
	    // The project manager just added the user
	    // Now we should add the added user to the manager project 
	
	    // Database connection
	    $connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");
	    // $login eh o login do usuario incluido, passado na URL
	    $id_usuario_incluido = simple_query("id_usuario", "usuario", "login = '$login'");
	    $query = "INSERT INTO participa (id_usuario, id_projeto)
	          VALUES ($id_usuario_incluido, " . $_SESSION['id_projeto_corrente'] . ")";
	    mysql_query($query) or die("Erro ao inserir na tabela participa");
	
	    $nome_usuario = simple_query("nome", "usuario", "id_usuario = $id_usuario_incluido");
	    $nome_projeto = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
		?>
		<script language="javascript1.3">
		
			document.writeln('<p style="color: blue; font-weight: bold; text-align: center">Usu&aacute;rio <b><?=$nome_usuario?></b> cadastrado e inclu&iacute;do no projeto <b><?=$nome_projeto?></b></p>');
			document.writeln('<p align="center"><a href="javascript:self.close();">Fechar</a></p>');
		
		</script>
		<?php
    }
}
else // Script called normally
{    
    if (empty($p_style))
	{
        $p_style = "color: green; font-weight: bold";
        $p_text = "Favor preencher os dados abaixo:";
    }
    else
    {
		// nothing to do    	
    }

    if ($firstTime)
    {
         $email ="";
	     $login ="";
	     $nome  ="";
	     $senha = "";
         $senha_conf = "";

    }
    else
    {
    	// nothing to do
    }
	?>
	<html>
		<head>
			<title>Cadastro de Usu&aacute;rio</title>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		</head>
		<body>
		<script language="JavaScript">
			<!--
		    function verifyEmail(form)
		    {
		    	email = form.email.value;
		        // checking if the email contais a @
		        i = email.indexOf("@");
		        if (i == -1)
		        {
		        	alert('Aten\u00e7\u00e3o: o E-mail digitado n\u00e3o \u00e9 v\u00e1lido.');
		        	return false;
		        }
		        else
		        {
		        	// nothing to do
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
						// nothing to do
					}
					    
					alert("Aten\u00e7\u00e3o: o E-mail digitado n\u00e3o \u00e9 v\u00e1lido.")
					email.focus();
					email.select();
					return (false)
				}
				else
				{
					// nothing to do
				}
			}
		    //-->
	
		</SCRIPT>
		<p style="<?=$p_style?>">
			<?=$p_text?>
		</p>
		<form action="?novo=<?=$novo?>" method="post">
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
	      			<td><input name="senha" maxlength="32" size="16" type="password" value="<?=$senha?>"></td>
	      			<td>Senha (confirma&ccedil;&atilde;o):</td>
	      			<td><input name="senha_conf" maxlength="32" size="16" type="password" value=""></td>
	    		</tr>
	    		<tr>
		      		<?php
		
					// Scenario - Add user
					
					// Objective:  Allows the manager to create new users
					// Context:  The manager wants to add new users (not registered) creating news
					//  user to the selected project
					// Pre-Conditions:  Login
					// Actors:  Manager
					// Resources:  Users data
					// Episodes:  Clicking on the registration button to confirm the addition
					//   of the new user to the selected project
					//			  The new created user will receive via email his login and password
		
					?>
	    			<td align="center" colspan="4" height="40" valign="bottom"><input name="submit" onClick="return verifyEmail(this.form);" type="submit" value="Cadastrar"></td>
	    		</tr>
	  		</table>
		</form>
		<br>
		<i><a href="showSource.php?file=add_usuario.php">Veja o c&oacute;digo fonte!</a></i>
		</body>
	</html>
<?php
}
?>
