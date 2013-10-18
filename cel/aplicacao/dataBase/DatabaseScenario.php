<?php

include_once ("../aplicacao/bd.inc");
include_once ("../aplicacao/bd_class.php");
include_once ("../aplicacao/seguranca.php");


// Function does a select on the table scenario.
// To insert a new scenario, it should be checked if it already exists.
// Gets the id of the project and the title of the scenario (1.0)
// Makes a SELECT on the table looking for a scenario similar name
// in the project (1.2)
// returns true if there is or false if not exists (1.3)


function checkScenarioExists($project, $title)
{
	$scenarioExists = true;

	$result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	$query = "SELECT * FROM cenario WHERE id_projeto = $project AND titulo = '$title' ";
	$queryResult = mysql_query($query) or die("Erro ao enviar a query de select no cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	$resultArray = mysql_fetch_array($queryResult);
	if ($resultArray == false)
	{
		$scenarioExists = false;
	}
	else
	{
		// nothing to do
	}

	return $scenarioExists;
}

?>