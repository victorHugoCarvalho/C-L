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
	assert($project != null , "project nao deve ser nulo!!");
	assert($title != null , " title project nao deve ser nulo!!");
	
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

###################################################################
# Insere um cenario no banco de dados.
# Recebe o id_projeto, titulo, objetivo, contexto, atores, recursos, excecao e episodios. (1.1)
# Insere os valores do lexico na tabela CENARIO. (1.2)
# Devolve o id_cenario. (1.4)
#
###################################################################
if (!(function_exists("inclui_cenario")))
{
	function includeScenario($id_projeto, $title, $objective, $context, $actors, $resources, $exception, $episodes)
	{
		//global $result;      // Conexao com a base de dados
		$result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$data = date("Y-m-d");

		$query = "INSERT INTO cenario (id_projeto,data, titulo, objetivo, contexto, atores, recursos, excecao, episodios)
		VALUES ($id_projeto,'$data', '".prepara_dado(strtolower($title))."', '".prepara_dado($objective)."',
		'".prepara_dado($context)."', '".prepara_dado($actors)."', '".prepara_dado($resources)."',
		'".prepara_dado($exception)."', '".prepara_dado($episodes)."')";
			
		mysql_query($query) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$query = "SELECT max(id_cenario) FROM cenario";
		$queryResult = mysql_query($query) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$result = mysql_fetch_row($queryResult);
		return $result[0];
	}
}
else
{
	//Nothing to do.
}

?>