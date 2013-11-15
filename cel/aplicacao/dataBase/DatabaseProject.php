<?php

include_once ("../aplicacao/bd.inc");
include_once ("../aplicacao/bd_class.php");
include_once ("../aplicacao/seguranca.php");


//Insere um projeto no banco de dados.
//Recebe o nome e descricao. (1.1)
//Verifica se este usuario ja possui um projeto com esse nome. (1.2)
//Caso nao possua, insere os valores na tabela PROJETO. (1.3)
//Devolve o id_cprojeto. (1.4)

if (!(function_exists("includeProject")))
{
	function includeProject($name, $description)
	{
		$result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		// cheks if the project already exists
		$queryVerification = "SELECT * FROM projeto WHERE nome = '$name'";
		$queryVerificationResult = mysql_query($queryVerification) or die("Erro ao enviar a query de select<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

		//$result = mysql_fetch_row($queryVerificationResult);
		$resultArray = mysql_fetch_array($queryVerificationResult);


		if ( $resultArray != false )
		{
			// checks if the project name corresponds to a project that the user already paticipates
			$idRepeatedProject = $resultArray['id_projeto'];

			$idCurrentUser = $_SESSION['id_usuario_corrente'];

			$query = "SELECT * FROM participa WHERE id_projeto = '$idRepeatedProject' AND id_usuario = '$idCurrentUser' ";

			$queryResult = mysql_query($query) or die("Erro ao enviar a query de SELECT no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

			$resultArray = mysql_fetch_row($queryResult);

			if ($resultArray[0] != null )
			{
				return -1;
			}
			else
			{
				//Nothing to do.
			}

		}
		else
		{
			//Nothing to do.
		}

		$query = "SELECT MAX(id_projeto) FROM projeto";
		$queryResult = mysql_query($query) or die("Erro ao enviar a query de MAX ID<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		$result = mysql_fetch_row($queryResult);

		if ($result[0] == false)
		{
			$result[0] = 1;
		}
		else
		{
			$result[0]++;
		}
		$date = date("Y-m-d");

		$queryResult = "INSERT INTO projeto (id_projeto, nome, data_criacao, descricao)
		VALUES ($result[0],'".prepara_dado($name)."','$date' , '".prepara_dado($description)."')";

		mysql_query($queryResult) or die("Erro ao enviar a query INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

		return $result[0];
	}
}
else
{
	//Nothing to do.
}


// Remove um determinado projeto da base de dados
// Recebe o id do projeto. (1.1)
// Apaga os valores da tabela pedidocen que possuam o id do projeto enviado (1.2)
// Apaga os valores da tabela pedidolex que possuam o id do projeto enviado (1.3)
// Faz um SELECT para saber quais l�xico pertencem ao projeto de id_projeto (1.4)
// Apaga os valores da tabela lextolex que possuam possuam lexico do projeto (1.5)
// Apaga os valores da tabela centolex que possuam possuam lexico do projeto (1.6)
// Apaga os valores da tabela sinonimo que possuam possuam o id do projeto (1.7)
// Apaga os valores da tabela lexico que possuam o id do projeto enviado (1.8)
// Faz um SELECT para saber quais cenario pertencem ao projeto de id_projeto (1.9)
// Apaga os valores da tabela centocen que possuam possuam cenarios do projeto (2.0)
// Apaga os valores da tabela centolex que possuam possuam cenarios do projeto (2.1)
// Apaga os valores da tabela cenario que possuam o id do projeto enviado (2.2)
// Apaga os valores da tabela participa que possuam o id do projeto enviado (2.3)
// Apaga os valores da tabela publicacao que possuam o id do projeto enviado (2.4)
// Apaga os valores da tabela projeto que possuam o id do projeto enviado (2.5)



function removeProject($idProject)
{
	assert($idProject != null , "idProject must not be null!!");
	
	$result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

	// Remove scenario requests
	$queryVerification = "Delete FROM pedidocen WHERE id_projeto = '$idProject' ";
	$removeScenarioRequest = mysql_query($queryVerification) or die("Erro ao apagar pedidos de cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

	// Remove lexicon requests
	$queryVerification = "Delete FROM pedidolex WHERE id_projeto = '$idProject' ";
	$removeLexiconRequest = mysql_query($queryVerification) or die("Erro ao apagar pedidos do lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

	// Remove lexicons
	$queryVerification = "SELECT * FROM lexico WHERE id_projeto = '$idProject' ";
	$queryVerificationResult = mysql_query($queryVerification) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

	while ($result = mysql_fetch_array($queryVerificationResult))
	{
		// select a lexicon
		$idLexicon = $result['id_lexico'];

		$queryVerification = "Delete FROM lextolex WHERE id_lexico_from = '$idLexicon' OR id_lexico_to = '$idLexicon' ";
		$removeLextoLe = mysql_query($queryVerification) or die("Erro ao apagar pedidos do lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

		$queryVerification = "Delete FROM centolex WHERE id_lexico = '$idLexicon'";
		$removecentolex = mysql_query($queryVerification) or die("Erro ao apagar pedidos do centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

		//$queryVerification = "Delete FROM sinonimo WHERE id_lexico = '$idLexicon'";
		//$removecentolex = mysql_query($queryVerification) or die("Erro ao apagar sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

		$queryVerification = "Delete FROM sinonimo WHERE id_projeto = '$idProject'";
		$removecentolex = mysql_query($queryVerification) or die("Erro ao apagar sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	}

	$queryVerification = "Delete FROM lexico WHERE id_projeto = '$idProject' ";
	$removeLexicon = mysql_query($queryVerification) or die("Erro ao apagar pedidos do lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

	// Remove scenarios
	$queryVerification = "SELECT * FROM cenario WHERE id_projeto = '$idProject' ";
	$queryVerificationResult = mysql_query($queryVerification) or die("Erro ao enviar a query de select no cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	$resultArrayScenario = mysql_fetch_array($queryVerificationResult);

	while ($result = mysql_fetch_array($queryVerificationResult))
	{
		// Select a scenario
		$idScenario = $result['id_cenario'];

		$queryVerification = "Delete FROM centocen WHERE id_cenario_from = '$idScenario' OR id_cenario_to = '$idScenario' ";
		$removeCentoCen = mysql_query($queryVerification) or die("Erro ao apagar pedidos do centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

		$queryVerification = "Delete FROM centolex WHERE id_cenario = '$idScenario'";
		$removeLextoLe = mysql_query($queryVerification) or die("Erro ao apagar pedidos do centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);


	}

	$queryVerification = "Delete FROM cenario WHERE id_projeto = '$idProject' ";
	$removeLexicon = mysql_query($queryVerification) or die("Erro ao apagar pedidos do cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

	// Remove participants
	$queryVerification = "Delete FROM participa WHERE id_projeto = '$idProject' ";
	$removeParticipants = mysql_query($queryVerification) or die("Erro ao apagar no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

	// Remove publication remover publicacao
	$queryVerification = "Delete FROM publicacao WHERE id_projeto = '$idProject' ";
	$removePublication = mysql_query($queryVerification) or die("Erro ao apagar no publicacao<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

	// Remove project
	$queryVerification = "Delete FROM projeto WHERE id_projeto = '$idProject' ";
	$removeProject = mysql_query($queryVerification) or die("Erro ao apagar no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
}
?>