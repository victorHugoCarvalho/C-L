<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

// alt_cenario.php: This script registers a new scenario of the project. 
//                  A variable $id_project is sent through the URL that 
//                  indicates where in project should be inserted the new scenery.
// File that calls: main.php
session_start();
include("funcoes_genericas.php");
include("httprequest.inc");
include_once("bd.inc");

// checks whether the user has been authenticated
chkUser("index.php");

// Connect to the SGBD
$connected_SGBD = bd_connect() or die("Erro ao conectar ao SGBD");

// The variables will be initialized by form html.
if (isset($submit)) 
{
    inserirPedidoAlterarCenario($_SESSION['id_projeto_corrente'],
                                $id_cenario,
                                $title,
                                $objective,
                                $context,
                                $actors,
                                $resources,
                                $exception,
                                $episodes,
                                $justification,
                                $_SESSION['id_usuario_corrente']);
	?>
	<script language="javascript1.3">
	
		opener.parent.frames['code'].location.reload();
		opener.parent.frames['text'].location.replace('main.php?id_projeto=<?=$_SESSION['id_projeto_corrente']?>');
	
	</script>
	<h4>Opera&ccedil;&atilde;o efetuada com sucesso!</h4>
	<script language="javascript1.3">
	
		self.close();
	
	</script>
	<?php
}
else  // Script called through the top menu
{

    $project_name = simple_query("nome", "projeto", "id_projeto = " . $_SESSION['id_projeto_corrente']);

    $query = "SELECT * FROM cenario WHERE id_cenario = $id_cenario";
    $executeQuery = mysql_query($query) or die("Erro ao executar a query");
    $result = mysql_fetch_array($executeQuery);

	//  Scenario - Include Scenario
		  
	//  Purpose: Allow user to include a new scenario
	//  Context: User want to include a new scenario.
	//  Precondition: Login, backdrop not registered
	//  Actors: User, System
	//  Resources: Data to be registered
	//  Episodes: The system provides the user a screen with the following text fields:
	//    - Name Scenario
	//    - Objective.  Restriction: Text box with at least 5 writing visible lines
	//    - Context.  Restriction: Text box with at least 5 writing visible lines
	//    - Actors.    Restriction: Text box with at least 5 writing visible lines
	//    - Resources.  Restriction: Text box with at least 5 writing visible lines
	//    - Exception.   Restriction: Text box with at least 5 writing visible lines
	//    - Episodes. Restriction: Text box with at least 16 writing visible lines
	//    - Button to confirm the inclusion of the new scenario
	//        Restrictions: After clicking the confirmation button,
	//          the system checks whether all fields have been filled.
	//        Exception: If all fields are empty, returns to the user a warning message
	//          that all fields must be completed and a button to return to the previous page.
	
	?>
	<html>
		<head>
			<title>Alterar Cen&aacute;rio</title>
		</head>
		<body>
			<h4>Alterar Cen&aacute;rio</h4>
			<br>
			<form action="?id_projeto=<?=$id_projeto?>" method="post">
  				<table>
    				<tr>
      					<td>Projeto:</td>
      					<td><input disabled size="48" type="text" value="<?=$project_name?>"></td>
    				</tr>
    				<input type="hidden" name="id_cenario" value="<?=$result['id_cenario']?>">
    
      				<td>T&iacute;tulo:</td>
      				<?php $result['titulo'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['titulo']); ?>
					<input type="hidden" name="title" value="<?=$result['titulo']?>">
					<td><input disabled maxlength="128" name="titulo2" size="48" type="text" value="<?=$result['titulo']?>"></td>
    				<tr>
      					<td>Objetivo:</td>
      					<? $result['objetivo'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['objetivo']); ?>
      					<td><textarea name="objective" cols="48" rows="3"><?=$result['objetivo']?></textarea></td>
    				</tr>
    				<tr>
      					<td>Contexto:</td>
      					<? $result['contexto'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['contexto']); ?>
      					<td><textarea name="context" cols="48" rows="3"><?=$result['contexto']?></textarea></td>
    				</tr>
    				<tr>
      					<td>Atores:</td>
      					<? $result['atores'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['atores']); ?>
      					<td><textarea name="actors" cols="48" rows="3"><?=$result['atores']?></textarea></td>
    				</tr>
    				<tr>
      					<td>Recursos:</td>
      					<? $result['recursos'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['recursos']); ?>
      					<td><textarea name="resources" cols="48" rows="3"><?=$result['recursos']?></textarea></td>
					</tr>
					<tr>
						<td>Exce&ccedil;&atilde;o:</td>
					    <? $result['excecao'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['excecao']); ?>
					    <td><textarea name="exception" cols="48" rows="3"><?=$result['excecao']?></textarea></td>
					</tr>
					<tr>
					    <td>Epis&oacute;dios:</td>
					    <? $result['episodios'] = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $result['episodios']); ?>
					    <td><textarea  cols="48" name="episodes" rows="5"><?=$result['episodios']?></textarea></td>
					</tr>
					<tr>
						<td>Justificativa para a altera&ccedil;&atilde;o:</td>
						<td><textarea name="justification" cols="48" rows="2"></textarea></td>
					</tr>
					<tr>
      					<td colspan="2"><b><small>Essa justificativa &eacute; necess&aacute;ria apenas para aqueles usu&aacute;rios que n&atilde;o s&atilde;o administradores.</small></b></td>
    				</tr>
    				<tr>
      					<td align="center" colspan="2" height="60"><input name="submit" type="submit" value="Alterar Cen&aacute;rio" onClick="updateOpener()"></td>
    				</tr>
  				</table>
			</form>
			<center>
				<a href="javascript:self.close();">Fechar</a>
			</center>
			<br>
			<i><a href="showSource.php?file=alt_cenario.php">Veja o c&oacute;digo fonte!</a></i>
		</body>
	</html>
	<?php
}
?>
