<?php

// $id_lexico_atual = id do lexico atual, para que ele n�o crie um link para si mesmo
// funcao que carrega o vetor com todos os titulos dos lexicos e seus sinonimos menos o titulo do
// l�xico passado na variavel $id_lexico_atual e seus sinonimos

// Fun��o que carrega vetor com todos os titulos e sinonimos de lexicos menos o de id id_lexico_atual

function carrega_vetor_lexicos( $id_projeto, $id_lexico_atual, $semAtual )
{
	$vetorDeLexicos = array();
	if( $semAtual )
	{
		$queryLexicos   = "SELECT id_lexico, nome    
							FROM lexico    
							WHERE id_projeto = '$id_projeto' AND id_lexico <> '$id_lexico_atual' 
							ORDER BY nome DESC"; 
		
		$querySinonimos = "SELECT id_lexico, nome 
							FROM sinonimo
							WHERE id_projeto = '$id_projeto' AND id_lexico <> '$id_lexico_atual' 
							ORDER BY nome DESC";  
							
	}
	else 
	{
		
		$queryLexicos   = "SELECT id_lexico, nome    
							FROM lexico    
							WHERE id_projeto = '$id_projeto' 
							ORDER BY nome DESC"; 
		
		$querySinonimos = "SELECT id_lexico, nome    
							FROM sinonimo
							WHERE id_projeto = '$id_projeto' ORDER BY nome DESC";    
	
	}	
    
	$resultadoQueryLexicos = mysql_query( $queryLexicos ) or die("Erro ao enviar a query de sele&ccedil;&atilde;o na tabela l&eacute;xicos !". mysql_error());
    
	$i=0;
	while( $linhaLexico = mysql_fetch_object( $resultadoQueryLexicos ) ) 
    {
        $vetorDeLexicos[$i] = $linhaLexico;
        $i++;
    }
	
    $resultadoQuerySinonimos = mysql_query( $querySinonimos ) or die("Erro ao enviar a query de sele&ccedil;&atilde;o na tabela sin&ocirc;nimos !". mysql_error());
  	while( $linhaSinonimo = mysql_fetch_object( $resultadoQuerySinonimos ) ) 
    {
        $vetorDeLexicos[$i] = $linhaSinonimo;
        $i++;
    }
	return $vetorDeLexicos;
}
 
// $id_cenario_atual = id do cenario atual, para que ele n�o crie um link para si mesmo
// funcao que carrega o vetor com todos os titulos dos cenarios menos o titulo do cenario
// passado na variavel $id_cenario_atual 

function carrega_vetor_cenario( $id_projeto, $id_cenario_atual, $semAtual )
{
    if (!isset($vetorDeCenarios))
    { 
    	$vetorDeCenarios = array();
    }
    else
    {
    	//Nothing to do.
    }
	if( $semAtual)
	{
		$queryCenarios = "SELECT id_cenario, titulo    
							FROM cenario    
							WHERE id_projeto = '$id_projeto' AND id_cenario <> '$id_cenario_atual' 
							ORDER BY titulo DESC";    
			
	}
	else
	{
		$queryCenarios = "SELECT id_cenario, titulo    
							FROM cenario    
							WHERE id_projeto = '$id_projeto' 
							ORDER BY titulo DESC";
	}
	
	$resultadoQueryCenarios = mysql_query($queryCenarios) or die("Erro ao enviar a query de sele&ccedil;&atilde;o !!". mysql_error());
	
    $i=0;
    while( $linhaCenario = mysql_fetch_object( $resultadoQueryCenarios ) ) 
	{
        $vetorDeCenarios[$i] = $linhaCenario;
        $i++;
    }

    return $vetorDeCenarios;
}

// Divide o array em dois

function divide_array( &$vet, $ini, $fim, $tipo )
{
    $i = $ini;
    $j = $fim;
    $dir = 1;
    
    while( $i < $j )
    {
    	if(strcasecmp($tipo,'cenario') == 0)
    	{
    		if( strlen( $vet[$i]->titulo ) < strlen( $vet[$j]->titulo ) )
	        {
	            $str_temp = $vet[$i];
	            $vet[$i] = $vet[$j];
	            $vet[$j] = $str_temp;
	            $dir--;
	        }
	        else
	        {
	        	//Nothing to do.
	        }	
    	}
    	else
    	{
	        if( strlen( $vet[$i]->nome ) < strlen( $vet[$j]->nome ) )
	        {
	            $str_temp = $vet[$i];
	            $vet[$i] = $vet[$j];
	            $vet[$j] = $str_temp;
	            $dir--;
	        }
	        else
	        {
	        	//Nothing to do.
	        }
    	}
        if( $dir == 1 )
        {
            $j--;
        }
        else
        {
            $i++;
        }
    }
    
    return $i;
}

// Ordena o vetor

function quicksort( &$vet, $ini, $fim, $tipo )
{
    if( $ini < $fim )
    {
        $k = divide_array( $vet, $ini, $fim, $tipo );
        quicksort( $vet, $ini, $k-1, $tipo );
        quicksort( $vet, $k+1, $fim, $tipo );
    }
    else
    {
    	//Nothing to do.
    }
}

// Funcao que constroi os links de acordo com o texto, passado atrav�s do par�metro $texto, lexicos, passados
// atrav�s do par�metro $vetorDeLexicos, e cenarios, passados atraves do parametro $vetorDeCenarios   

function monta_links ( $texto , $vetorDeLexicos , $vetorDeCenarios ) 
{
  	$copiaTexto = $texto;
	if (!isset($vetorAuxLexicos))
	{ 
		$vetorAuxLexicos = array();
	}
	if (!isset($vetorAuxCenarios))
	{ 
		$vetorAuxCenarios = array();
	}
	if (!isset($vetorDeCenarios))
	{ 
		$vetorDeCenarios = array();
	}
	if (!isset($vetorDeLexicos))
	{ 
		$vetorDeLexicos = array();
	}

	// Se o vetor de cen�rios estiver vazio ele s� ira procurar por refer�ncias a lexicos
	
		
	if ( count( $vetorDeCenarios )== 0 )
	{
		
		$i=0;
		$a=0;
		while( $i < count( $vetorDeLexicos ) )
        {
           	$nomeLexico = escapa_metacaracteres( $vetorDeLexicos[$i]->nome );
			$regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
			if( preg_match( $regex, $copiaTexto ) != 0 )
			{
				$copiaTexto = preg_replace( $regex," ", $copiaTexto );
				$vetorAuxLexicos[$a] = $vetorDeLexicos[$i];
				$a++;
			}
			else
			{
				//Nothing to do.
			}
			$i++;
        }
	}
	else
	{
	
		// Se o vetor de cen�rios n�o estiver vazio ele ir� procurar por l�xicos e cen�rios
	
		$tamLexicos = count( $vetorDeLexicos);
		$tamCenarios = count( $vetorDeCenarios );
		$tamanhoTotal = $tamLexicos + $tamCenarios ;
	    $i = 0;
		$j = 0;
		$a = 0;
		$b = 0;
		$contador = 0;
		while ( $contador < $tamanhoTotal)
	    {
	    	if ( ($i < $tamLexicos ) && ($j < $tamCenarios) )
			{
				if( strlen( $vetorDeCenarios[$j]->titulo ) < strlen( $vetorDeLexicos[$i]->nome ) )
		    	{
		    		$nomeLexico = escapa_metacaracteres( $vetorDeLexicos[$i]->nome );
					$regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
					if(preg_match( $regex, $copiaTexto ) != 0 )
					{
						$copiaTexto = preg_replace( $regex, " ", $copiaTexto );
						$vetorAuxLexicos[$a] = $vetorDeLexicos[$i];
						$a++;
					}
					else
					{
						//Nothing to do.
					}
		            $i++;

				}
				else
				{
		
					$tituloCenario = escapa_metacaracteres( $vetorDeCenarios[$j]->titulo );
					$regex = "/(\s|\b)(" . $tituloCenario . ")(\s|\b)/i";
		            if(preg_match( $regex, $copiaTexto ) != 0 )
					{
						$copiaTexto = preg_replace( $regex," ", $copiaTexto );
						$vetorAuxCenarios[$b] = $vetorDeCenarios[$j];
						$b++;
					}
					$j++;
		        }
			}
			else if ($tamLexicos == $i) 
			{
			
				$tituloCenario = escapa_metacaracteres( $vetorDeCenarios[$j]->titulo );
				$regex = "/(\s|\b)(" . $tituloCenario . ")(\s|\b)/i";
		        if(preg_match( $regex, $copiaTexto ) != 0 )
				{
					$copiaTexto = preg_replace( $regex," ", $copiaTexto );
					$vetorAuxCenarios[$b] = $vetorDeCenarios[$j];
					$b++;
				}
				else
				{
					//Nothing to do.
				}
				$j++;
				
			}
			else if ($tamCenarios == $j)
			{
			
				$nomeLexico = escapa_metacaracteres( $vetorDeLexicos[$i]->nome );
				$regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
				if(preg_match( $regex, $copiaTexto ) != 0 )
				{
					$copiaTexto = preg_replace( $regex, " ", $copiaTexto );
					$vetorAuxLexicos[$a] = $vetorDeLexicos[$i];
					$a++;
				}
				else
				{
					//Nothing to do.
				}
		        $i++;
				
			}
			else
			{
				//Nothing to do.
			}
			$contador++;
		}
	}
	//print_r( $vetorAuxLexicos );
	// Adiciona os links para lexicos no texto 
	
	$indice=0;
	$vetorAux = array();
	while($indice < count( $vetorAuxLexicos ) )
	{
		$nomeLexico = escapa_metacaracteres( $vetorAuxLexicos[$indice]->nome );
		$regex = "/(\s|\b)(" . $nomeLexico . ")(\s|\b)/i";
		$link = "<a title=\"L�xico\" href=\"main.php?t=l&id=".$vetorAuxLexicos[$indice]->id_lexico."\">".$vetorAuxLexicos[$indice]->nome."</a>";
		$vetorAux[$indice] = $link;
		$texto = preg_replace( $regex,"$1wzzxkkxy".$indice."$3", $texto );
		$indice++;
	}
	$indice2=0;
	
	while($indice2 < count( $vetorAux ) )
	{
		$linkLexico = ( $vetorAux[$indice2] );
		$regex = "/(\s|\b)(wzzxkkxy".$indice2 . ")(\s|\b)/i";
		$texto = preg_replace( $regex, "$1".$linkLexico."$3", $texto );
		$indice2++;
	}
	
	
	// Adiciona os links para cen�rios no texto 
	
	$indice=0;
	$vetorAuxCen = array();
	while($indice < count( $vetorAuxCenarios) )
	{
		$tituloCenario = escapa_metacaracteres($vetorAuxCenarios[$indice]->titulo);
		$regex = "/(\s|\b)(" . $tituloCenario . ")(\s|\b)/i";
		$link = "$1<a title=\"Cen�rio\" href=\"main.php?t=c&id=".$vetorAuxCenarios[$indice]->id_cenario."\"><span style=\"font-variant: small-caps\">".$vetorAuxCenarios[$indice]->titulo."</span></a>$3";
		$vetorAuxCen[$indice] = $link;
		$texto = preg_replace( $regex,"$1wzzxkkxyy".$indice."$3", $texto );
		$indice++;
	}
	
	
	$indice2 = 0;
	while($indice2 < count( $vetorAuxCen) )
	{
		$linkCenario = ( $vetorAuxCen[$indice2] );
		$regex = "/(\s|\b)(wzzxkkxyy".$indice2 . ")(\s|\b)/i";
		$texto = preg_replace( $regex, "$1".$linkCenario."$3", $texto );
		$indice2++;
	}
	
	return $texto;
	
} 

?>
