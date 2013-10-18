<?php

session_start(); 

include_once ("dataBase/DatabaseProject.php");
include("funcoes_genericas.php");
include("httprequest.inc");


chkUser("index.php");        // checks whether the user has been authenticated

//This script is called when a solicitation of inclusion
//new project, or when a New User register on the system


// Scenario - Register New Project

// Objective: Allow user to register a new project
// Context: User want to include a new project in the database
// Precondition: Login
// Actors: User
// Resources: System, design data, database
// Episodes:  The User clicks the option to add design found in the top menu.
//             as the project name and description.
//           The user clicks the insert button.
//                The system saves the new project in the database and automatically builds the navigation
//                for this new project.
// Exception: If it is specified a project name already exists and belongs or has participation
//            this user, the system displays an error message.o.

// Called by the button submit
if (isset($submit))
{    
	$idAddedProject = includeProject($name, $description);
    
	// inserts in the table participa
    
	if ($idAddedProject != -1 )
    {
	    $connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");
	    $manager = 1;
	    $idCurrentUser = $_SESSION['id_usuario_corrente'];    
	    $query = "INSERT INTO participa (id_usuario, id_projeto, gerente) VALUES ($idCurrentUser, $idAddedProject, $manager)";
	    mysql_query($query) or die("Erro ao inserir na tabela participa");
    }
    else
    {
		?>
		<html>
		<title>Erro</title>
		<body>
		<p style="color: red; font-weight: bold; text-align: center">Nome de projeto j&aacute; existente!</p>
		<center>
		  <a href="JavaScript:window.history.go(-1)">Voltar</a>
		</center>
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
	// regular call
}
else
{
	?>
	<html>
        <head>
        	<title>Adicionar Projeto</title>
        	<script language="javascript1.3">

				function checkFormValues()
				{
					if (document.forms[0].name.value == "")
					{
						alert('Preencha o campo "Nome"');
						document.forms[0].name.focus();
						return false;
					}
					else
					{
						pattern = /[\\\/\?"<>:|]/;
						outOfPattern = pattern.exec(document.forms[0].name.value);
						if (outOfPattern)
						{
							window.alert ("O nome do projeto n\u00e3o pode conter nenhum dos seguintes caracteres:   / \\ : ? \" < > |");
							document.forms[0].name.focus();
							return false;
						}
						else
						{
						    //Nothing to do.
						}	 
					}
			
			    return true;
				}

        	</script>
		</head>
		<body>
			<h4>Adicionar Projeto:</h4>
			<br>
			<form action="" method="post" onSubmit="return checkFormValues();">
				<table>
			    	<tr>
			        	<td>Nome:</td>
			           	<td><input maxlength="128" name="name" size="48" type="text"></td>
			        </tr>
			    	<tr>
			        	<td>Descri&ccedil;&atilde;o:</td>
			            <td><textarea cols="48" name="description" rows="4"></textarea></td>
			        	<tr>
			            	<td align="center" colspan="2" height="60"><input name="submit" type="submit" value="Adicionar Projeto"></td>
			            </tr>
				</table>
			</form>
			<br>
			<i><a href="showSource.php?file=add_projeto.php">Veja o c&oacute;digo fonte!</a></i>
		</body>
	</html>
<?php
}
?>
