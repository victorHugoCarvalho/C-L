<?php
include 'estruturas.php';
include_once 'auxiliar_algoritmo.php';
session_start();
?>

<html>
  <head>
    <title>Algoritmo de Gera&ccedil;&atilde;o de Ontologias</title>
    <style>

    </style>
  </head>
  <body>
		<?php


function verifica_consistencia()
{
	return TRUE;
}


function compara_arrays($array1, $array2)
{

	if( count($array1) != count($array2) )
	{
		return FALSE;
	}

	foreach( $array1 as $key=>$elem )
	{
		if( $elem->verbo != $array2[$key]->verbo )
		{
			return FALSE;
		}
	}
	return TRUE;
}


/*
Cenario:	Montar hierarquia.
Objetivo:	Montar hierarquia de conceitos.
Contexto:	Organizacao da ontologia em andamento.
Atores:
Recursos:	Sistema, conceito, lista de subconceitos, lista de conceitos.
Episodios:
- Para cada subconceito
* Procurar sua chave na lista de conceitos.
* Adicionar a chave como um subconceito do conceito.
*/
function montar_hierarquia($conc, $nova_lista, $list)
{
	foreach( $nova_lista as $subcon )
	{
		$key = existe_conceito($subcon, $list);
		$conc->subconceitos[] = $subcon;
	}
}

/*
Cenario:	Traduzir os termos do lexico classificados como sujeito e objeto.
Objetivo:	Traduzir os termos do lexico classificados como sujeito e objeto.
Contexto:	Algoritmo de tradução iniciado.
Atores:		Usuario.
Recursos:	Sistema, lista de sujeito e objetos, lista de conceitos, lista de relacoes.
Episodios:
- Para cada elemento da lista de sujeito e objetos
* Criar novo conceito com o mesmo nome e a descricao igual a nocao do elemento.
* Para cada impacto do elemento
. Verificar com o usuario a existencia do impacto na lista de relacoes.
. Caso não exista, incluir este impacto na lista de relacoes.
. Incluir esta relacao na lista de relacoes do conceito.
. Descobrir
* Incluir o conceito na lista de conceitos.
* Verificar consistencia.
*/
function traduz_sujeito_objeto($lista_de_sujeito_e_objeto, $conceitos, $relacoes, $axiomas)
{

	for( ; $_SESSION["index1"] < count($lista_de_sujeito_e_objeto); ++$_SESSION["index1"] )
	{

		$suj = $lista_de_sujeito_e_objeto[$_SESSION["index1"]];

		if( !isset( $_SESSION["conceito"] ))
		{
			$_SESSION["salvar"] = "TRUE";
			$_SESSION["conceito"] = new conceito($suj->nome, $suj->nocao);
			$_SESSION["conceito"]->namespace = "proprio";
		}
		else
		{
			$_SESSION["salvar"] = "FALSE";
		}


		for( ; $_SESSION["index2"] < count($suj->impacto); ++$_SESSION["index2"] )
		{

			$imp = $suj->impacto[$_SESSION["index2"]];

			if( trim($imp) == "" )
			continue;

			if( !isset($_SESSION["verbos_selecionados"]) )
				$_SESSION["verbos_selecionados"] = array();

			if( !isset($_SESSION["impact"]) )
			{
				$_SESSION["impact"] = array();
				$_SESSION["finish_insert"] = FALSE;
			}
			while( !$_SESSION["finish_insert"] )
			{
				if( !isset( $_SESSION["exist"] ) )
				{
					asort($relacoes);
					$_SESSION["lista"] = $relacoes;
					$_SESSION["nome1"] = $imp;
					$_SESSION["nome2"] = $suj;
					$_SESSION["job"] = "exist";

							?>
							<SCRIPT language='javascript'>
								document.location = "auxiliar_interface.php";
							</SCRIPT>


							
							<?php

							exit();
				}



				if( $_POST["existe"] == "FALSE")
				{           
					
					$nome = strtolower($_POST["nome"]);
					session_unregister("exist");
					if( (count($_SESSION["verbos_selecionados"]) != 0) && (array_search( $nome, $_SESSION["verbos_selecionados"] ) !== null))
					{
						continue;
					}
					$_SESSION["verbos_selecionados"][] = $nome;
					$i = array_search( $nome, $relacoes );
					if( $i === false )
					{
						$_SESSION["impact"][] = (array_push($relacoes, $nome) - 1);
					}
					else
					{
						$_SESSION["impact"][] = $i;
					}



				}
				else if ( $_POST["indice"] != -1 )
				{
					session_unregister("exist");
					if( (count($_SESSION["verbos_selecionados"]) != 0) && array_search( $relacoes[$_POST["indice"]], $_SESSION["verbos_selecionados"] ) !== false )
					{
						continue;
					}
					$_SESSION["verbos_selecionados"][] = $relacoes[$_POST["indice"]];
					$_SESSION["impact"][] = $_POST["indice"];

				}
				else
				{
					$_SESSION["finish_insert"] = TRUE;
				}

			}

			if( !isset($_SESSION["ind"]))
			{
				$_SESSION["ind"] = 0;
			}

			$_SESSION["verbos_selecionados"] = array();

			for( ; $_SESSION["ind"] < count($_SESSION["impact"]); ++$_SESSION["ind"] )
			{

				if( !isset($_SESSION["predicados_selecionados"]) )
					$_SESSION["predicados_selecionados"] = array();

				$indice = $_SESSION["impact"][$_SESSION["ind"]];
				$_SESSION["finish_relation"] = FALSE;
				while( !$_SESSION["finish_relation"] )
				{
					if( !isset($_SESSION["insert_relation"]) )
					{
						asort($conceitos);
						$_SESSION["lista"] = $conceitos;
						$_SESSION["nome1"] = $relacoes[$indice];
						$_SESSION["nome2"] = $suj->nome;
						$_SESSION["nome3"] = $imp;
						$_SESSION["job"] = "insert_relation";
								?>
								<SCRIPT language='javascript'>
									document.location = "auxiliar_interface.php";
								</SCRIPT>
								<?php

								exit();
					}
					else if( isset($_SESSION["nome2"]) )
					{

						session_unregister("nome2");
						session_unregister("nome3");
						session_unregister("insert_relation");


						if( $_POST["existe"] == "FALSE" )
						{
							$conc = strtolower($_POST["nome"]);

							if( (count($_SESSION["predicados_selecionados"]) != 0) && (array_search( $conc, $_SESSION["predicados_selecionados"] ) !== null))
							{
								continue;
							}
							$_SESSION["predicados_selecionados"][] = $conc;

							if( existe_conceito($conc, $_SESSION['lista_de_conceitos']) == -1)
							{
								if( existe_conceito($conc, $lista_de_sujeito_e_objeto) == -1)
								{
									$nconc = new conceito($conc,"");
									$nconc->namespace = $_POST['namespace'];
									$_SESSION['lista_de_conceitos'][] = $nconc;
								}
							}

							$ind_rel = existe_relacao( $_SESSION['nome1'], $_SESSION['conceito']->relacoes);
							if( $ind_rel != -1 )
							{
								if( array_search($conc,$_SESSION["conceito"]->relacoes[$ind_rel]->predicados) === false )
									$_SESSION["conceito"]->relacoes[$ind_rel]->predicados[] = $conc;
							}
							else
							{
								$_SESSION["conceito"]->relacoes[] = new relacao_entre_conceitos( $conc , $_SESSION["nome1"]);
							}
						}
						else if( $_POST["indice"] != "-1" )
						{
							$conc = $conceitos[$_POST["indice"]]->nome;
							if( (count($_SESSION["predicados_selecionados"]) != 0) && (array_search( $conc, $_SESSION["predicados_selecionados"] ) !== null))
							{
								continue;
							}

							$_SESSION["predicados_selecionados"][] = $conc;

							$ind_rel = existe_relacao( $_SESSION['nome1'], $_SESSION['conceito']->relacoes);
							if( $ind_rel != -1 )
							{
								if( array_search($conc,$_SESSION["conceito"]->relacoes[$ind_rel]->predicados) === false )
									$_SESSION["conceito"]->relacoes[$ind_rel]->predicados[] = $conc;
							}
							else
							{
								$_SESSION["conceito"]->relacoes[] = new relacao_entre_conceitos( $conc , $_SESSION["nome1"]);
							}
						}
						else
						{
							$_SESSION["finish_relation"] = TRUE;
						}
					}
				}
				$_SESSION["predicados_selecionados"] = array();
			}


/*Unregister a global variable from the current session*/
			session_unregister("exist");
			session_unregister("impact");
			session_unregister("ind");
			session_unregister("insert_relation");
			session_unregister("insert");
			session_unregister("verbos_selecionados");
			session_unregister("predicados_selecionados");

		}

		$finish_disjoint = FALSE;
		while( !$finish_disjoint )
		{
			if( !isset($_SESSION["axiomas_selecionados"]) )
				$_SESSION["axiomas_selecionados"] = array();

			if( !isset( $_SESSION["disjoint"] ) )
			{
				$_SESSION["lista"] = $conceitos;
				$_SESSION["nome1"] = $_SESSION["conceito"]->nome;
				$_SESSION["job"] = "disjoint";
						?>
						<SCRIPT language='javascript'>
							document.location = "auxiliar_interface.php";
						</SCRIPT>
						<?php

						exit();
			}
			if( $_POST["existe"] == "TRUE")
			{
				$axioma = $_SESSION["conceito"]->nome . " disjoint " . strtolower($_POST["nome"]);
				if( array_search($axioma, $axiomas) === false )
				{
					$axiomas[] = $axioma;
					$_SESSION["axiomas_selecionados"][] = $axioma;
				}
				session_unregister("disjoint");
			}
			else
			{
				$finish_disjoint = TRUE;
			}
		}
		$_SESSION["axiomas_selecionados"] = array();

		$conceitos[] = $_SESSION["conceito"];
		asort($conceitos);

		if( !verifica_consistencia() )
		{
			exit();
		}

		session_unregister("insert");
		session_unregister("disjoint");
		session_unregister("exist");
		session_unregister("insert_relation");
		session_unregister("conceito");
		$_SESSION["index2"] = 0;

	}
	$_SESSION["index1"] = 0;
	session_unregister("finish_insert");
	session_unregister("finish_relation");
}


/*
Cenario:	Traduzir os termos do lexico classificados como verbo.
Objetivo:	Traduzir os termos do lexico classificados como verbo.
Contexto:	Algoritmo de tradução iniciado.
Atores:		Usuario.
Recursos:	Sistema, lista de verbo, lista de relacoes.
Episodios:
- Para cada elemento da lista de verbo
* Verificar com o usuario a existencia do verbo na lista de relacoes.
* Caso não exista, incluir este verbo na lista de relacoes.
* Verificar consistencia.
*/
function traduz_verbos($verbos, $relacoes)
{
	for( ; $_SESSION["index3"] < count($verbos); ++$_SESSION["index3"] )
	{

		$verbo = $verbos[$_SESSION["index3"]];


		if( !isset( $_SESSION["exist"] ) )
		{
			$_SESSION["salvar"] = "TRUE";
			asort($relacoes);
			$_SESSION["lista"] = $relacoes;
			$_SESSION["nome1"] = $verbo->nome;
			$_SESSION["nome2"] = $verbo;
			$_SESSION["job"] = "exist";
				?>
				<SCRIPT language='javascript'>
				document.location = "auxiliar_interface.php";
				</SCRIPT>
				<?php

				exit();
		}

		if( $_POST["existe"] == "FALSE")
		{
			$nome = strtolower($_POST["nome"]);
			if( array_search($nome, $relacoes) === false )
				array_push($relacoes, $nome);
		}


		//	$lista_de_relacoes = $_SESSION["lista"];

		if( !verifica_consistencia() )
		{
			exit();
		}

		session_unregister("exist");
		session_unregister("insert");
	}
	$_SESSION["index3"] = 0;
}



/*
Cenario:	Traduzir os termos do lexico classificados como estado.
Objetivo:	Traduzir os termos do lexico classificados como estado.
Contexto:	Algoritmo de traducao iniciado.
Atores:		Usuario.
Recursos:	Sistema, lista de estado, lista de conceitos, lista de relacoes, lista de axiomas.
Episodios:
- Para cada elemento da lista de estado
* Para cada impacto do elemento
. Descobrir
* Verificar se o elemento possui importancia central na ontologia.
* Caso tenha, traduza como se fosse um sujeito/objeto.
* Caso contrario, traduza como se fosse um verbo.
* Verificar consistencia.
*/
function traduz_estados($estados, $conceitos, $relacoes, $axiomas)
{
	for( ; $_SESSION["index4"] < count($estados ); ++$_SESSION["index4"] )
	{

		$estado = $estados[$_SESSION["index4"]];


		$aux = array($estado);

		if( !isset( $_SESSION["main_subject"] ) )
		{

			$_SESSION["nome1"] = $estado->nome;
			$_SESSION["nome2"] = $estado;
			$_SESSION["job"] = "main_subject";
					?>
					<p>
						<SCRIPT language='javascript'>
							document.location = "auxiliar_interface.php";
						</SCRIPT>
					<?php

					exit();

					//$rel = exist($verbo->nome, $lista_de_relacoes);
		}


		if( !isset( $_SESSION["translate"] ) )
		{
			if( $_POST["main_subject"] == "TRUE" )
			{
				$_SESSION["translate"] = 1;
				traduz_sujeito_objeto($aux, &$conceitos, &$relacoes, &$axiomas);
			}
			else
			{
				$_SESSION["translate"] = 2;
				traduz_verbos($aux, &$relacoes);
			}
		}
		else if ( $_SESSION["translate"] == 1 )
		{
			traduz_sujeito_objeto($aux, &$conceitos, &$relacoes);
		}
		else if ( $_SESSION["translate"] == 2 )
		{
			traduz_verbos($aux, &$relacoes);
		}



		if( !verifica_consistencia() )
		{
			exit();
		}

		session_unregister("main_subject");
		session_unregister("translate");

	}
	$_SESSION["index4"] = 0;
}



/*
Cenario:	Organizar ontologia.
Objetivo:	Organizar ontologia.
Contexto:	Listas de conceitos, relacoes e axiomas prontas.
Atores:		Usuario.
Recursos:	Sistema, lista de conceitos, lista de relacoes, lista de axiomas.
Episodios:
- Faz-se uma copia da lista de conceitos.
- Para cada elemento x da lista de conceitos
* Cria-se uma nova lista contendo o elemento x.
* Para cada elemento subsequente y
. Compara as relacoes dos elementos x e y.
. Caso possuam as mesmas relacoes, adiciona-se o elemento y a nova lista que ja contem x.
. Retira-se y da lista de conceitos.
* Retira-se x da lista de conceitos.
* Caso a nova lista tenha mais de dois elementos, ou seja, caso x compartilhe as mesmas
relacoes com outro termo
. Procura por um elemento na lista de conceitos que faca referencia a todos os elementos
da nova lista.
. Caso exista tal elemento, montar hierarquia.
. Caso nao exista, descobrir.
* Verificar consistencia.
- Restaurar lista de conceitos.
*/
function organizar_ontologia($conceitos, $relacoes, $axiomas)
{
	$_SESSION["salvar"] = "TRUE";
	/*for( ; $_SESSION["index5"] < count($conceitos); ++$_SESSION["index5"] )
	{
		$_SESSION["salvar"] = "TRUE";

		$conc = $conceitos[$_SESSION["index5"]];

		if( count($conc->subconceitos) > 0 )
		{
			if( $conc->subconceitos[0] == -1 )
			{
				array_splice($conc->subconceitos, 0, 1);
				continue;
			}
		}

		$conc->subconceitos[0] = -1;
		$key = $_SESSION["index5"];

		$nova_lista_de_conceitos = array($conc);

		for( $i = $key+1; $i < count($conceitos); ++$i )
		{
			if (compara_arrays($conc->relacoes, $conceitos[$i]->relacoes))
			{
				$conceitos[$i]->subconceitos[0] = -1;
				$nova_lista_de_conceitos[] = $conceitos[$i];
			}
		}
		*/
		//if( count($nova_lista_de_conceitos) >= 2 )

		$finish_relation = FALSE;
		while( !$finish_relation )
		{
			$indice = 0;

			if( !isset( $_SESSION["reference"] ) )
			{

				$_SESSION["lista"] = $conceitos;//array($conc1, $nconc);
				//$_SESSION['nome1'] = $nova_lista_de_conceitos;//
				$_SESSION["job"] = "reference";
								?>
						<a href="auxiliar_interface.php">auxiliar_interface</a>
						<SCRIPT language='javascript'>
							document.location = "auxiliar_interface.php";
						</SCRIPT>
						<?php

								exit();

								//$rel = exist($verbo->nome, $lista_de_relacoes);
			}

			session_unregister("reference");

			$achou = FALSE;

			if(isset($_POST['pai']))
			{
				$pai_nome = $_POST['pai'];
				$key2 = existe_conceito($pai_nome, $conceitos);
				$filhos = array();
				foreach($conceitos as $key3=>$filho)
				{
					$filho_nome = trim($filho->nome);
					if( isset($_POST[$key3]) )
					{
						$filhos[] = $filho_nome;
					}
				}
				if( count($filhos) > 0)
				{
					montar_hierarquia(&$conceitos[$key2], $filhos, $conceitos );
					$achou = true;
				}
			}
			else
			{
				$finish_relation = true;
			}


			if(!$achou)
			{
				//tentar montar hierarquia pelo vocabulario minimo.
			}
		}

		if( !verifica_consistencia() )
		{
			exit();
		}
		//array_splice($conc->subconceitos, 0, 1);
	//}
	//$_SESSION["index5"] = 0;
}


/*
Cenario:  	Traduzir Léxico para Ontologia.
Objetivo: 	Traduzir Léxico para Ontologia.
Contexto: 	Existem listas de elementos do léxico organizadas por tipo, e estes elementos
são consistentes.
Atores:   	Usuário.
Recursos: 	Sistema, listas de elementos do léxico organizadas por tipo, listas de elementos
da ontologia.
Episódios:
- Criar lista de conceitos vazia.
- Criar lista de relacoes vazia.
- Criar lista de axiomas vazia.
- Traduzir os termos do lexico classificados como sujeito e objeto.
- Traduzir os termos do lexico classificados como verbo.
- Traduzir os termos do lexico classificados como estado.
- Organizar a ontologia.

*/
function traduz()
{
	//Verifica se as listas foram iniciadas.
	if( isset( $_SESSION["lista_de_sujeito"]   ) && isset( $_SESSION["lista_de_objeto"]   ) &&
            isset( $_SESSION["lista_de_verbo"]     ) && isset( $_SESSION["lista_de_estado"]   ) &&
    	    isset( $_SESSION["lista_de_conceitos"] ) && isset( $_SESSION["lista_de_relacoes"] ) &&
    	    isset( $_SESSION["lista_de_axiomas"]   ) )
	{
		$sujeitos = $_SESSION["lista_de_sujeito"];
		$objetos  = $_SESSION["lista_de_objeto"];
		$verbos   = $_SESSION["lista_de_verbo"];
		$estados  = $_SESSION["lista_de_estado"];
	}
	else
	{
		echo "ERRO! <br>";
		exit();
	}

	$lista_de_sujeito_e_objeto = array_merge($sujeitos, $objetos);
	sort($lista_de_sujeito_e_objeto);
	$_SESSION['lista_de_sujeito_e_objeto'] = $lista_de_sujeito_e_objeto;


	if( $_SESSION["funcao"] == "sujeito_objeto" )
	{
		traduz_sujeito_objeto($lista_de_sujeito_e_objeto, &$_SESSION["lista_de_conceitos"], &$_SESSION["lista_de_relacoes"], &$_SESSION["lista_de_axiomas"]);
		$_SESSION["funcao"] = "verbo";
	}

	if( $_SESSION["funcao"] == "verbo" )
	{
		traduz_verbos($verbos, &$_SESSION["lista_de_relacoes"]);
		$_SESSION["funcao"] = "estado";
	}

	if( $_SESSION["funcao"] == "estado" )
	{
		traduz_estados($estados, &$_SESSION["lista_de_conceitos"], &$_SESSION["lista_de_relacoes"], &$_SESSION["lista_de_axiomas"]);
		$_SESSION["funcao"] = "organiza";
	}

	if( $_SESSION["funcao"] == "organiza" )
	{
		organizar_ontologia(&$_SESSION["lista_de_conceitos"], &$_SESSION["lista_de_relacoes"], &$_SESSION["lista_de_axiomas"]);
		$_SESSION["funcao"] = "fim";
	}


	//Imprime Resultados
	/*
	print("CONCEITOS: <br>");
	foreach( $_SESSION["lista_de_conceitos"] as $con)
	{
		echo "$con->nome --> $con->descricao ";
		foreach($con->relacoes as $rel)
		{

		}
		echo "<br>";
	}

	print("RELACOES: <br>");
	print_r($_SESSION["lista_de_relacoes"]);
	echo "<br>";

	print("AXIOMAS: <br>");
	print_r($_SESSION["lista_de_axiomas"]);
	echo "<br>";
	*/
	echo 'O processo de geração de Ontologias foi concluído com sucesso!<br>
	Não esqueça de clicar em Salvar.';
	?>
            <p>
            <form method="POST" action="auxiliar_bd.php">
                <input type="hidden" value="TRUE" name="save" size="20" >
                <input type="submit" value="SALVAR">
                  </form>
            </p>
     <?php
}


traduz();






   ?>


 </body>
</html>