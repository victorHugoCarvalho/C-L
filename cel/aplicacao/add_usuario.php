<?php
session_start();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<?php

include("funcoes_genericas.php");
include_once("bd.inc");

define('TRUE', 'true');

$firstTime = TRUE;

include("httprequest.inc");

if (isset($submit)) // Called by the submit button
{
    $firstTime = "false";
    // ** Scenario "Adding independet user" **
    // The system checks if all the fields are filled. If any field is not filled, 
    //   the system warns the user that all the field should be filled.  
    if ($name == "" || $email == "" || $login == "" || $password == "" || $confimationPassword == "")
	{
        $pageStyle = "color: red; font-weight: bold";
        $pageText = "Por favor, preencha todos os campos.";
        reload("?pageStyle=$pageStyle&pageText=$pageText&name=$name&email=$email&login=$login&password=$password&confimationPassword=$confimationPassword&new=$new");
    }
    else
    {
    	// Check if the passwords typed by the user are equal
        if ($password != $confimationPassword)
        {
            $pageStyle = "color: red; font-weight: bold";
            $pageText = "Senhas diferentes. Favor preencher novamente as senhas.";
            reload("?pageStyle=$pageStyle&pageText=$pageText&nome=$name&email=$email&login=$login&new=$new");
        }
        else
        {

            // Scenario - Add user independent
			// Objective: Allow a user who is not registered as an administrator, register
			//   with the administrator profile.
			// Context: User want to register as an administrator.
			// User onscreen registration user.
			// Pre-conditions: User has accessed the system.
			// Actors: User, System
			// Resources: Interface, Database
			// Episodes: The system returns to the user interface with fields for entering
			//   a Name, email, login, password, and password confirmation.
			//   The user fills in the fields and click on submit.
			//   The system then checks to see if all fields are filled.
			//     -If any field is no filled, the system warns you that all
			//        fields must be filled.
			//     -If all fields are completed, the system checks in the data bank to see if this login exists 
			//     -If the typed login already exists, the system returns the same page warning to the user that he must choose another login.

            $connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");
            $query = "SELECT id_usuario FROM usuario WHERE login = '$login'";
            $SendQuery = mysql_query($query) or die("Erro ao enviar a query");
            if (mysql_num_rows($SendQuery)) // If there is any user with this login
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
                    alert ("Login j\u00e1 existente no sistema. Favor escolher outro login.")
                </script>
				<?php
                reload("?new=$new");
            }
            else // Passed through all the tests. Can be added to the DB
			{
				/* Replace all occurences of ">" and "<" for " " */
				$name  = str_replace( ">" , " " , str_replace ( "<" , " " , $name  ) ) ;
				$login = str_replace( ">" , " " , str_replace ( "<" , " " , $login ) ) ;
				$email = str_replace( ">" , " " , str_replace ( "<" , " " , $email ) ) ;
				
				// Encrypting the password
				$password = md5($password);
                $query = "INSERT INTO usuario (nome, login, email, senha) VALUES ('$name', '$login', '$email', '$password')";
                mysql_query($query) or die("Erro ao cadastrar o usu\u00e1rio");
                reload("?&registered=$registered&new=$new&login=$login");
            }
        }   // else
    }   // else
} else if (isset($registered))
{
    // Registration completed. Depending where the user came from,
    // whe should send him to a different place

    if ($new == TRUE) // If the user came from the login screen
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

        $idCurrentUser = simple_query("id_usuario", "usuario", "login = '$login'");
        $_SESSION['id_usuario_corrente'] = $idCurrentUser;
		?>
		<script language="javascript1.3">

			// Redirect the user to the registration project page
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
	    // $login is the user login, passed trough the URL
	    $idRegisteredUser = simple_query("id_usuario", "usuario", "login = '$login'");
	    $query = "INSERT INTO participa (id_usuario, id_projeto)
	          VALUES ($idRegisteredUser, " . $_SESSION['id_projeto_corrente'] . ")";
	    mysql_query($query) or die("Erro ao inserir na tabela participa");
	
	    $userName = simple_query("nome", "usuario", "id_usuario = $idRegisteredUser");
	    $projectName = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);
		?>
		<script language="javascript1.3">
		
			document.writeln('<p style="color: blue; font-weight: bold; text-align: center">Usu&aacute;rio <b><?=$userName?></b> cadastrado e inclu&iacute;do no projeto <b><?=$projectName?></b></p>');
			document.writeln('<p align="center"><a href="javascript:self.close();">Fechar</a></p>');
		
		</script>
		<?php
    }
}
else // Script called normally
{    
    if (empty($pageStyle))
	{
        $pageStyle = "color: green; font-weight: bold";
        $pageText = "Favor preencher os dados abaixo:";
    }
    else
    {
		// nothing to do    	
    }

    if ($firstTime)
    {
         $email ="";
	     $login ="";
	     $name  ="";
	     $password = "";
         $confimationPassword = "";

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
		<p style="<?=$pageStyle?>">
			<?=$pageText?>
		</p>
		<form action="?new=<?=$new?>" method="post">
			<table>
	    		<tr>
	      			<td>Nome:</td>
	      			<td colspan="3"><input name="name" maxlength="255" size="48" type="text" value="<?=$name?>"></td>
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
	      			<td><input name="password" maxlength="32" size="16" type="password" value="<?=$password?>"></td>
	      			<td>Senha (confirma&ccedil;&atilde;o):</td>
	      			<td><input name="confimationPassword" maxlength="32" size="16" type="password" value=""></td>
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
