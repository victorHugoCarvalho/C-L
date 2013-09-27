<?php

session_start(); 

include("funcoes_genericas.php");
include("httprequest.inc");


chkUser("index.php");        // checks whether the user has been authenticated

//This script is called when a solicitation of inclusion
//new project, or when a New User register on the system


// Scenery - Register New Project
// Purpose: Allow user to register a new project
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
	$id_projeto_incluido = inclui_projeto($nome, $descricao);
    
	// inserts in the table participa
    
	if ($id_projeto_incluido != -1 )
    {
	    $connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");
	    $gerente = 1;
	    $id_usuario_corrente = $_SESSION['id_usuario_corrente'];    
	    $query = "INSERT INTO participa (id_usuario, id_projeto, gerente) VALUES ($id_usuario_corrente, $id_projeto_incluido, $gerente)";
	    mysql_query($query) or die("Erro ao inserir na tabela participa");
    }
    else
    {
		?>
<html>
<title>Erro</title>
<body>
<p style="color: red; font-weight: bold; text-align: center">Nome de projeto j� existente!</p>
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
	// Chamado normalmente
}
else
{
	?>
<html>
        <head>
        <title>Adicionar Projeto</title>
        <script language="javascript1.3">

	function chkFrmVals()
	{
		if (document.forms[0].nome.value == "")
		{
			alert('Preencha o campo "Nome"');
			document.forms[0].nome.focus();
			return false;
		}
		else
		{
			pattern = /[\\\/\?"<>:|]/;
			outOfPattern = pattern.exec(document.forms[0].nome.value);
			if (outOfPattern)
			{
				window.alert ("O nome do projeto n�o pode conter nenhum dos seguintes caracteres:   / \\ : ? \" < > |");
				document.forms[0].nome.focus();
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
<form action="" method="post" onSubmit="return chkFrmVals();">
          <table>
    <tr>
              <td>Nome:</td>
              <td><input maxlength="128" name="nome" size="48" type="text"></td>
            </tr>
    <tr>
              <td>Descri&ccedil;&atilde;o:</td>
              <td><textarea cols="48" name="descricao" rows="4"></textarea></td>
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
