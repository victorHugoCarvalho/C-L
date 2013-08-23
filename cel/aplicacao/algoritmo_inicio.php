<?php
	//@session_destroy();
	//session_unset();
	session_start();

	include 'auxiliar_bd.php';
	include_once 'bd.inc';
	include_once "script_bd2.php" ;

	//mysql_close($link);
        $link = bd_connect();

	$list = verifica_tipo();

	if( is_array($list) )
	{
		foreach( $list as $id )
		{
			$lex = obter_lexico($id);
			$aux[] = $lex["nome"];
		}

		$_SESSION["lista"] = $aux;
		$_SESSION["job"] = "type";
		$_SESSION["nome1"] = 1;
		?>
		<script language="javascript">
			window.location = "auxiliar_interface.php";
		</script>
		<?php

		exit();
	}

	$_SESSION["lista_de_sujeito"] = get_lista_de_sujeito();
	$_SESSION["lista_de_objeto" ] = get_lista_de_objeto();
	$_SESSION["lista_de_verbo"  ] = get_lista_de_verbo();
	$_SESSION["lista_de_estado" ] = get_lista_de_estado();

	/*print_r($_SESSION["lista_de_sujeito"]);
	print_r($_SESSION["lista_de_objeto"]);
	print_r($_SESSION["lista_de_verbo"]);
	print_r($_SESSION["lista_de_estado"]);*/

	$_SESSION["salvar"] = "FALSE";


	if( $_POST["load"] == "FALSE" )
	{
		converte_impactos();
		$_SESSION["lista_de_conceitos"] = array();
		$_SESSION["lista_de_relacoes" ] = array();
		$_SESSION["lista_de_axiomas"  ] = array();

		$_SESSION["funcao"] = "sujeito_objeto";

		$_SESSION["index1"] = 0;
		$_SESSION["index2"] = 0;
		$_SESSION["index3"] = 0;
		$_SESSION["index4"] = 0;
		$_SESSION["index5"] = 0;
		$_SESSION["index6"] = 0;
		$_SESSION["index7"] = 0;

	}
	else
	{
		$_SESSION["lista_de_relacoes"]  = get_lista_de_relacoes();
		$_SESSION["lista_de_conceitos"] = get_lista_de_conceitos();
		$_SESSION["lista_de_axiomas"]   = get_lista_de_axiomas();

		$_SESSION["funcao"] = get_funcao();



		$indices = get_indices();
		if(count($indices) == 5)
		{
		$_SESSION["index1"] = $indices['index1']; //Sujeito
		$_SESSION["index3"] = $indices['index3']; //Verbo
		$_SESSION["index4"] = $indices['index4']; //Estado
		$_SESSION["index5"] = $indices['index5']; //Organizacao
		}
		else
		{
			$_SESSION["index1"] = 0; //Sujeito
			$_SESSION["index3"] = 0; //Verbo
			$_SESSION["index4"] = 0; //Estado
			$_SESSION["index5"] = 0; //Organizacao
		}
		$_SESSION["index2"] = 0;
		$_SESSION["index6"] = 0;
		$_SESSION["index7"] = 0;

	}

	mysql_close($link);


?>

<html>
  <head>
    <title>Algoritmo de Gera&ccedil;&atilde;o de Ontologias</title>
    <style>

    </style>
  </head>
<body>

	<script language="javascript">
			window.location = "algoritmo.php";
	</script>

</body>
</html>