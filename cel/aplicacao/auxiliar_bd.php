<?php

include_once 'estruturas.php';
include_once 'auxiliar_algoritmo.php';
include_once 'bd.inc';
session_start();


function get_lista_de_sujeito()
{
	$id_projeto = $_SESSION['id_projeto'];
	$aux = array();
	
	$query = "select * from lexico where tipo = 'sujeito' AND id_projeto='$id_projeto';";
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	
	while ($line = mysql_fetch_array($result, MYSQL_BOTH))
	{
		$aux[] = obter_termo_do_lexico($line);
	}
	
	sort($aux);
	
	return $aux;
}

function get_lista_de_objeto()
{
	$id_projeto = $_SESSION['id_projeto'];
	$auxiliar = array();
	
	$query 	= "select * from lexico where tipo = 'objeto' AND id_projeto='$id_projeto';";
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	
	while ($line = mysql_fetch_array($result, MYSQL_BOTH))
	{
		$auxiliar[] = obter_termo_do_lexico($line);
	}
	
	sort($auxiliar);
	
	return $auxiliar;
}

function get_lista_de_verbo()
{
	$id_projeto = $_SESSION['id_projeto'];
	$auxiliar = array();
	
	$query = "select * from lexico where tipo = 'verbo' AND id_projeto='$id_projeto';";
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	
	while ($line = mysql_fetch_array($result, MYSQL_BOTH))
	{
		$auxiliar[] = obter_termo_do_lexico($line);
	}
	
	sort($auxiliar);
	
	return $auxiliar;
}

function get_lista_de_estado()
{
	$id_projeto = $_SESSION['id_projeto'];
	$auxiliar = array();
	
	$query = "select * from lexico where tipo = 'estado' AND id_projeto='$id_projeto';";
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	
	while ($line = mysql_fetch_array($result, MYSQL_BOTH))
	{
		$auxiliar[] = obter_termo_do_lexico($line);
	}
	
	sort($auxiliar);
	
	return $auxiliar;
}

function verifica_tipo()
{
	$id_projeto = $_SESSION['id_projeto'];
	//Esta fun��o verifica se todos os membros da tabela de l�xicos tem um tipo definido
	//Caso haja registros na tabela sem tipo defino, a fun��o retorna estes registros
	//Caso contr�rio retorna true
	
	$query = "select * from lexico where tipo is null AND id_projeto='$id_projeto' order by id_lexico;";
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	$result2 = mysql_num_rows($result);
	
	$column_value = $result2;
	
	if ($column_value>0)
	{
		/* Caso haja lexicos sem tipo definido, seus id's ser�o retornados atrav�s de um array */
		
		$auxiliar = array();
		
		while ($line2 = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$auxiliar[] = $line2['id_lexico'];
		}
		
		mysql_free_result($result);
                
		return($auxiliar);
	}
	else
	{
		mysql_free_result($result);
		return(TRUE);
	}
	
}

function atualiza_tipo($id_lexico, $type)
{
	$id_projeto = $_SESSION['id_projeto'];
	// esta fun��o atualiza o tipo do lexico $id_lexico (inteiro) para $tipo (string)
	// esta fun��o s� aceita os tipos: sujeito, objeto, verbo, estado e NULL
	
	if(!(($type != "sujeito")||($type != "objeto")||($type != "verbo")||($type != "estado")||($type != "null")))
	{
		return (FALSE);
	}
	else
	{
		//Nothing to do.
	}
	
	if($type == "null")
	{
		$query = "update lexico set tipo = $type where id_lexico = '$id_lexico';";
	}
	else
	{
		$query = "update lexico set tipo = '$type' where id_lexico = '$id_lexico';";
	}
	
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	return(TRUE);
}

function obter_lexico($id_lexico)
{
	$id_projeto = $_SESSION['id_projeto'];
	//retorna todos os campos do lexico; cada campo � uma posi��o do
	//array que pode ser indexada pelo nome do campo, ou por um indice
	//inteiro.
	$query  = "select * from lexico where id_lexico = '$id_lexico' AND id_projeto='$id_projeto';";
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	$line  = mysql_fetch_array($result, MYSQL_BOTH);
	return($line);
}

function obter_termo_do_lexico($lexico)
{
	$id_projeto = $_SESSION['id_projeto'];
	$impactos   = array();
	$id_lexico  = $lexico['id_lexico'];
	$query	    = "select impacto from impacto where id_lexico = '$id_lexico'";
	$result     = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	
	while($line = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$impactos[] = strtolower($line['impacto']);
	}
	
	$lexico_terms = new termo_do_lexico(strtolower($lexico['nome']), strtolower($lexico['nocao']), $impactos);
	return $lexico_terms;
}

/*
function zera_tipos()
{
$query = "update lexico set tipo =  NULL;";
$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
}
*/

function cadastra_impacto($id_lexico, $impacto)
{
	$id_projeto = $_SESSION['id_projeto'];
	$query  = "insert into impacto (id_lexico, impacto) values ('$id_lexico', '$impacto');";
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	
	$query  = "select * from impacto where impacto = '$impacto' and id_lexico = $id_lexico;";
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	
	$line = mysql_fetch_array($result, MYSQL_ASSOC);
	$id_impacto = $line['id_impacto'];
	
	return $id_impacto;
}

//criar tabela para conceitos (class conceito)
function get_lista_de_conceitos()
{
	$id_projeto = $_SESSION['id_projeto'];
	$auxiliar = array();
	
	$query = "select * from conceito where id_projeto='$id_projeto';";
	$result1 = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	
	while ($line = mysql_fetch_array($result1, MYSQL_BOTH))
	{
		$conceito = new conceito($line['nome'], $line['descricao'] );
		$conceito->namespace = $line['namespace'];
		
		$id = $line['id_conceito'];
		$query = "select * from relacao_conceito where id_conceito = '$id' AND id_projeto='$id_projeto';";
		$result2 = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
		
		while ($line2 = mysql_fetch_array($result2, MYSQL_BOTH))
		{
			$id_relacao = $line2['id_relacao'];
			$query = "select * from relacao where id_relacao = '$id_relacao' AND id_projeto='$id_projeto';";
			$result3 = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
			$line3 = mysql_fetch_array($result3, MYSQL_BOTH);
			$relacao = $line3['nome'];
			$predicado = $line2['predicado'];
			$indice = existe_relacao($relacao, $conceito->relacoes);
			
			if ($indice != -1 )
			{
				$conceito->relacoes[$indice]->predicados[] = $predicado;
			}
			else
			{
				$conceito->relacoes[] = new relacao_entre_conceitos($predicado, $relacao);
			}
		}
		$auxiliar[] = $conceito;
	}
	sort($auxiliar);
	
	$query = "select * from hierarquia where id_projeto='$id_projeto';";
	$result1 = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	
	while ($line = mysql_fetch_array($result1, MYSQL_BOTH))
	{
		$id_conceito = $line['id_conceito'];
		$query = "select * from conceito where id_conceito = '$id_conceito' AND id_projeto='$id_projeto';";
		$result2 = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
		$line2 = mysql_fetch_array($result2, MYSQL_BOTH);
		$conceito_nome = $line2['nome'];
		
		$id_subconceito = $line['id_subconceito'];
		$query = "select * from conceito where id_conceito = '$id_subconceito' AND id_projeto='$id_projeto';";
		$result2 = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
		$line2 = mysql_fetch_array($result2, MYSQL_BOTH);
		$subconceito_nome = $line2['nome'];
		
		foreach ($auxiliar as $key=>$conc1)
		{
			if($conc1->nome == $conceito_nome)
			{
				$auxiliar[$key]->subconceitos[] = $subconceito_nome;
			}
			else
			{
				//Nothing to do.
			}
		}
	}
	
	return $auxiliar;
}

//criar tabela para conceitos (class relacao_entre_conceitos)
function get_lista_de_relacoes()
{
	$id_projeto = $_SESSION['id_projeto'];
	$auxiliar = array();
	
	$query = "select nome from relacao where id_projeto='$id_projeto';";
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	
	while ($line = mysql_fetch_array($result, MYSQL_BOTH))
	{
		$auxiliar[] = $line['nome'];
	}
	
	sort($auxiliar);
	
	return $auxiliar;
}

//criar tabela para axiomas (string)
function get_lista_de_axiomas()
{
	$id_projeto = $_SESSION['id_projeto'];
	$auxiliar = array();
	
	$query = "select axioma from axioma where id_projeto='$id_projeto';";
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	
	while ($line = mysql_fetch_array($result, MYSQL_BOTH))
	{
		$auxiliar[] = $line['axioma'];
	}
	
	sort($auxiliar);
	
	return $auxiliar;
}

//variavel funcao (string)
function get_funcao()
{
	$id_projeto = $_SESSION['id_projeto'];
	
	$query = "select valor from algoritmo where nome = 'funcao' AND id_projeto='$id_projeto';";
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	$line = mysql_fetch_array($result, MYSQL_BOTH);
	return $line['valor'];
}

//variaveis de indice (int)
function get_indices()
{
	$id_projeto = $_SESSION['id_projeto'];
	
	$query = "select * from algoritmo where id_projeto='$id_projeto';";
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	$indice = array();
	
	while ($line = mysql_fetch_array($result, MYSQL_BOTH))
	{
		$indice[$line['nome']] = $line['valor'];
	}
	
	return $indice;
}

function salvar_algoritmo()
{
	$id_projeto = $_SESSION['id_projeto'];
	$link = bd_connect();
	
	foreach ($_SESSION["lista_de_conceitos"] as $conceito)
	{
		print($conceito->nome);
		foreach ($conceito->relacoes as $relacao)
		{
			print("<br>----$relacao->verbo");
			foreach ($relacao->predicados as $predicado)
			{
				print("<br>--------$predicado");
			}
		}
	}
	
	
	$query = "delete from relacao where id_projeto='$id_projeto';";
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	$query = "delete from conceito where id_projeto='$id_projeto';";
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	$query = "delete from relacao_conceito where id_projeto='$id_projeto';";
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	$query = "delete from axioma where id_projeto='$id_projeto';";
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	$query = "delete from algoritmo where id_projeto='$id_projeto';";
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	$query = "delete from hierarquia where id_projeto='$id_projeto';";
	$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	
	if (isset($_SESSION["lista_de_relacoes"]) )
	{
		foreach ($_SESSION["lista_de_relacoes"] as $relacao)
		{
			$query  = "insert into relacao (nome, id_projeto) values ('$relacao', '$id_projeto');";
			$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
		}
	}
	else if (isset($_SESSION["lista_de_conceitos"]))
	{
		foreach ($_SESSION["lista_de_conceitos"] as $conceito)
		{                                      
			$query  = "select id_conceito from conceito where nome = '$conceito->nome' and id_projeto='$id_projeto';";
			$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
                        
			$id_conceito = 0;
			if (mysql_num_rows($result) > 0 )
			{ 
				$line = mysql_fetch_array($result, MYSQL_BOTH);
				$id_conceito = $line['id_conceito'];
			}
			else
			{			
				$query  = "insert into conceito (nome,descricao,namespace, id_projeto) values ('$conceito->nome', '$conceito->descricao','$conceito->namespace' ,'$id_projeto');";
				$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
				
				$query  = "select id_conceito from conceito where nome = '$conceito->nome' and id_projeto='$id_projeto';";
				$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
				$line = mysql_fetch_array($result, MYSQL_BOTH);
				$id_conceito = $line['id_conceito'];
			}
			
			
			foreach ($conceito->relacoes as $relacao)
			{
				$verbo = $relacao->verbo;
				$query  = "select id_relacao from relacao where nome = '$verbo' and id_projeto='$id_projeto';";
				$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
				$line = mysql_fetch_array($result, MYSQL_BOTH);
				$id_relacao = $line['id_relacao'];
				$predicados = $relacao->predicados;
				
				foreach ($predicados as $predicado)
				{
					$query  = "insert into relacao_conceito (id_conceito,id_relacao,predicado,id_projeto) values ('$id_conceito', '$id_relacao', '$predicado', '$id_projeto');";
					$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
				}
			}
		}
		
		foreach ($_SESSION["lista_de_conceitos"] as $conceito)
		{
			foreach ($conceito->subconceitos as $subconceito)
			{
				if ($subconceito != -1 )
				{
					$query  = "select id_conceito from conceito where nome = '$subconceito' and id_projeto='$id_projeto';";
					$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
					$line = mysql_fetch_array($result, MYSQL_BOTH);
					$id_subconceito = $line['id_conceito'];
					
					$nome = $conceito->nome;
					$query  = "select id_conceito from conceito where nome = '$nome' and id_projeto='$id_projeto';";
					$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
					$line = mysql_fetch_array($result, MYSQL_BOTH);
					$id_conceito = $line['id_conceito'];
					
					$query  = "insert into hierarquia (id_conceito,id_subconceito,id_projeto) values ('$id_conceito', '$id_subconceito','$id_projeto');";
					$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
				}
				else
				{
					//Nothing to do.
				}
			}
		}
	}
	else if( isset($_SESSION["lista_de_axiomas"]) )
	{
		foreach ($_SESSION["lista_de_axiomas"] as $axioma)
		{
			$query  = "insert into axioma (axioma,id_projeto) values ( '$axioma','$id_projeto' );";
			$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
		}
	}
	else if (isset($_SESSION["funcao"]) )
	{
		$funcao = $_SESSION['funcao'];
		$query  = "insert into algoritmo (nome, valor, id_projeto) values ('funcao'," ;
		$query = $query . "'" . $funcao . "', '$id_projeto' );";
		$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	}
	else if (isset($_SESSION["index1"]) )
	{
		$query  = "insert into algoritmo (nome, valor,id_projeto) values ('index1',";
		$query = $query . "'" . $_SESSION['index1'] . "', '$id_projeto');";
		$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	}
	else if (isset($_SESSION["index3"]) )
	{
		$query  = "insert into algoritmo (nome, valor, id_projeto) values ('index3',";
		$query = $query . "'" . $_SESSION['index3'] . "', '$id_projeto');";
		$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	}
	else if (isset($_SESSION["index4"]) )
	{
		$query  = "insert into algoritmo (nome, valor, id_projeto) values ('index4',";
		$query = $query . "'" . $_SESSION['index4'] . "', '$id_projeto');";
		$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	}
	else if (isset($_SESSION["index5"]) )
	{
		$query  = "insert into algoritmo (nome, valor, id_projeto) values ('index5',";
		$query = $query . "'" . $_SESSION['index5'] . "', '$id_projeto');";
		$result = mysql_query($query) or die("A consulta � BD falhou : " . mysql_error() . __LINE__);
	}
	else
	{
		//Nothing to do.
	}
	mysql_close($link);
	
	if ($_SESSION["funcao"] != 'fim')
	{
		?>
                <script>
			document.location = "auxiliar_interface.php";
		</script>
                <?php
	}
	else
	{
		?>
                <script>
			document.location = "algoritmo.php";
		</script>
                <?php
	}
}

if (isset($_SESSION["tipos"]))
{
	session_unregister("tipos");
	
	include_once 'bd.inc';
	
	$link = bd_connect();
	
	$list = verifica_tipo();
	
	foreach ($list as $key=>$termo)
	{
		$auxiliar = $_POST["type" . $key];
		echo ("$termo, $auxiliar <br>");
		
		if (!atualiza_tipo($termo, $auxiliar))
		{
			echo "ERRO <br>";
		}
		else
		{
			//Nothing to do.
		}
	}
	
	mysql_close($link);
	?>
        <script>
		document.location = "algoritmo_inicio.php";
	</script>
        <?php
}
else
{
	//Nothing to do.
}

if (array_key_exists("save", $_POST ))
{
	salvar_algoritmo();
}
else
{
	//Nothing to do.
}


?>
<html>
    <head>
    <title>Auxiliar BD</title>
    <style></style>
    </head>
    <body>
</body>
</html>
