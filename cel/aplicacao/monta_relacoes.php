<?php
include_once("monta_relacoes.php");
include_once("coloca_links.php");
### MONTA AS RELACOES USADAS NO MENU LATERAL###



function monta_relacoes($id_projeto)
{
	// Apaga todas as relações existentes das tabelas centocen, centolex e lextolex
	
	$DB = new PGDB () ;
    $sql1 = new QUERY ($DB) ;
    $sql2 = new QUERY ($DB) ;
    $sql3 = new QUERY ($DB) ;
    
    //$sql1->execute ("DELETE FROM centocen");
    //$sql2->execute ("DELETE FROM centolex") ;
    //$sql3->execute ("DELETE FROM lextolex") ;

	// Refaz as relações das tabelas centocen, centolex e lextolex

	//seleciona todos os cenarios
	
	$q = "SELECT *
	          FROM cenario
	          WHERE id_projeto = $id_projeto
	          ORDER BY CHAR_LENGTH(titulo) DESC";
	$qrr = mysql_query($q) or die("Erro ao enviar a query");   
	
	while ($result = mysql_fetch_array($qrr)) // Para todos os cenarios 
	{    
		$id_cenario_atual = $result['id_cenario'];
		
		// Monta vetor com titulo dos cenarios
		
		$vetor_cenarios = carrega_vetor_cenario( $id_projeto, $id_cenario_atual );
		
		// Monta vetor com nome e sinonimos de todos os lexicos
		
		$vetor_lexicos = carrega_vetor_todos ( $id_projeto );
		
		// Ordena o vetor de lexico pela quantidade de palavaras do nome ou sinonimo
		
		quicksort( $vetor_lexicos, 0, count($vetor_lexicos)-1,'lexico' );
		
		// Ordena o vetor de cenarios pela quantidade de palavras do titulo
		
		quicksort( $vetor_cenarios, 0, count($vetor_cenarios)-1,'cenario' );
		
		## Titulo
		
		$titulo = $result['titulo'];
		$tempTitulo = cenario_para_lexico( $id_cenario_atual, $titulo, $vetor_lexicos );
		adiciona_relacionamento($id_cenario_atual,'cenario', $tempTitulo);
		
		## Objetivo
		
		$objetivo = $result['objetivo'];
		$tempObjetivo = cenario_para_lexico( $id_cenario_atual, $objetivo, $vetor_lexicos );
		adiciona_relacionamento($id_cenario_atual, 'cenario', $tempObjetivo);
		
		## Contexto
		
		$contexto = $result['contexto'];
		$tempContexto = cenario_para_lexico_cenario_para_cenario( $id_cenario_atual, $contexto, $vetor_lexicos, $vetor_cenarios );
		adiciona_relacionamento($id_cenario_atual, 'cenario', $tempContexto);
		
		## Atores 
		
		$atores = $result['atores'];
		$tempAtores = cenario_para_lexico( $id_cenario_atual, $atores, $vetor_lexicos );
		adiciona_relacionamento($id_cenario_atual, 'cenario', $tempAtores);
		
		## Recursos 
		
		$recursos = $result['recursos'];
		$tempRecursos = cenario_para_lexico( $id_cenario_atual, $recursos, $vetor_lexicos );
		adiciona_relacionamento($id_cenario_atual, 'cenario', $tempRecursos);
		
		## Excecao
		
		$excecao = $result['excecao'];
		$tempExcecao = cenario_para_lexico( $id_cenario_atual, $excecao, $vetor_lexicos);
		adiciona_relacionamento($id_cenario_atual, 'cenario', $tempExcecao);
		
		## Episodios
		
		$episodios = $result['episodios'];
		$tempEpisodios = cenario_para_lexico_cenario_para_cenario( $id_cenario_atual, $episodios, $vetor_lexicos, $vetor_cenarios );
		adiciona_relacionamento($id_cenario_atual, 'cenario', $tempEpisodios);
	}
	
	// Seleciona todos os léxicos
	
	$q = "SELECT *
	          FROM lexico
	          WHERE id_projeto = $id_projeto
	          ORDER BY CHAR_LENGTH(nome) DESC";
	$qrr = mysql_query($q) or die("Erro ao enviar a query");   
	
	while ($result = mysql_fetch_array($qrr)) // Para todos os lexicos
	{   
		$id_lexico_atual = $result['id_lexico'];
		
		// Monta vetor com nomes e sinonimos de todos os lexicos menos o lexico atual
		
		$vetor_lexicos = carrega_vetor( $id_projeto, $id_lexico_atual );
		
		// Ordena o vetor de lexicos pela quantidade de palavaras do nome ou sinonimo
		quicksort( $vetor_lexicos, 0, count($vetor_lexicos)-1,'lexico' );
		
		## Nocao
		
		$nocao = $result['nocao'];
		$tempNocao = lexico_para_lexico($id_lexico, $nocao, $vetor_lexicos);
		adiciona_relacionamento($id_lexico_atual, 'lexico', $tempNocao);
		
		## Impacto	
	
		$impacto = $result['impacto'];
		$tempImpacto = lexico_para_lexico($id_lexico, $impacto, $vetor_lexicos);
		adiciona_relacionamento($id_lexico_atual, 'lexico', $tempImpacto);
	} 
}

// marca as relações de léxicos para léxicos

function lexico_para_lexico($id_lexico, $texto, $vetor_lexicos)
{
	$i=0;
    while( $i < count( $vetor_lexicos ) )
    {
        $regex = "/(\s|\b)(" . $vetor_lexicos[$i]->nome . ")(\s|\b)/i";
        $texto = preg_replace( $regex, "$1{l".$vetor_lexicos[$i]->id_lexico."**$2"."}$3", $texto );
        $i++;
    	// insere o relacionamento na tabela centolex
        //$q = "INSERT 
        //		INTO lextolex (id_lexico_from, id_lexico_to)
        //		VALUES ($id_lexico, " . $vetor_lexicos[$i]->id_lexico . ")";
        //mysql_query($q) or die("Erro ao enviar a query de INSERT na lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
    }
    return $texto;
}

// Marca as relações de cenários para léxicos

function cenario_para_lexico($id_cenario, $texto, $vetor_lexicos )
{
    $i=0;
    while( $i < count( $vetor_lexicos ) )
    {
     	$regex = "/(\s|\b)(" . $vetor_lexicos[$i]->nome . ")(\s|\b)/i";
        $texto = preg_replace( $regex, "$1{l".$vetor_lexicos[$j]->id_lexico."**$2"."}$3", $texto );
        $i++;
       	// insere o relacionamento na tabela centolex
        //$q = "INSERT 
        //		INTO centolex (id_cenario, id_lexico)
        //		VALUES ($id_cenario, " . $vetor_lexicos[$i]->id_lexico . ")";
        //mysql_query($q) or die("Erro ao enviar a query de INSERT na centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
    }
    return $texto;
}


// Marca as relações de cenários para cenários
	
function cenario_para_cenario($id_cenario, $texto, $vetor_cenarios )
{
    $i=0;
    while( $i < count( $vetor_cenarios ) )
    {
     	$regex = "/(\s|\b)(" . $vetor_cenarios[$i]->titulo . ")(\s|\b)/i";
        $texto = preg_replace( $regex, "$1{c".$vetor_cenarios[$j]->id_cenario."**$2"."}$3", $texto );
        $i++;
       	// insere o relacionamento na tabela centolex
        //$q = "INSERT 
        //		INTO centolex (id_cenario, id_lexico)
        //		VALUES ($id_cenario, " . $vetor_lexicos[$i]->id_lexico . ")";
        //mysql_query($q) or die("Erro ao enviar a query de INSERT na centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
    }
    return $texto;
}

// Marca as relaçoes de cenário para cenário e cenário para léxico no mesmo texto

function cenario_para_lexico_cenario_para_cenario( $id_cenario,$texto, $vetor_lexicos, $vetor_cenarios )
{
    $i=0;
    $j=0;
    $k=0;
    $total = count( $vetor_lexicos) + count( $vetor_cenarios);
    while( $k < $total )
    {
        if( strlen( $vetor_cenarios[$j]->titulo ) < strlen( $vetor_lexicos[$i]->nome ) )
    	{
    		$regex = "/(\s|\b)(" . $vetor_lexicos[$i]->nome . ")(\s|\b)/i";
			$texto = preg_replace( $regex, "$1{l".$vetor_lexicos[$i]->id_lexico."**$2"."}$3", $texto );
       		$i++;
       		
       		// insere o relacionamento na tabela centolex
        	//$q = "INSERT 
        	//		INTO centolex (id_cenario, id_lexico)
        	//		VALUES ($id_cenario, " . $vetor_lexicos[$i]->id_lexico . ")";
        	//mysql_query($q) or die("Erro ao enviar a query de INSERT na centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
        	
    	}else{
        	$regex = "/(\s|\b)(" . $vetor_cenarios[$j]->titulo . ")(\s|\b)/i";
           	$texto = preg_replace( $regex, "$1{c".$vetor_cenarios[$j]->id_cenario."**$2"."}$3", $texto );
    	    $j++;
    	}
        $k++;
    }   
    return $texto;
}
// Função que adiciona os relacionamentos nas tabelas centocen, centolex e lextolex
// Atraves da analise das marcas

// id_from id do léxico ou cenário que referencia outro cenário ou léxico
// $tipo_from tipo de quem esta referenciando ( se é léxico ou cenário)

function adiciona_relacionamento( $id_from, $tipo_from, $texto )
{
    $i = 0; // indice do texto com marcadores
    $parser = 0; // verifica quando devem ser adicionadas as tags
    
    $novo_texto = "";
    while( $i < strlen( &$texto ) )
    {    
        if( $texto[$i] == "{" )
        {
            $parser++;
            if( $parser == 1 ) //adiciona link ao texto - abrindo
            {
                 $id_to = "";
                 $i++;
                 $tipo= $texto[$i];
                 $i++;
                 while( $texto[$i] != "*" )
                 {
                    $id_to .= $texto[$i];
                 	$i++;	
                 }
                 if($tipo=="l")// Destino é um léxico (id_lexico_to)
                 {
                 	 if(strcasecmp($tipo_from,'lexico') == 0 )// Origem é um léxico (id_lexico_from -> id_lexico_to)
                 	 {
                 	 	echo '<script language="javascript">confirm(" '.$id_from.' - '.$id_to.'léxico para léxico")</script>';
                 	 	//adiciona relação de lexico para léxico	
                 	 }else if(strcasecmp($tipo_from,'cenario') == 0)// Origem é um cenário (id_cenario -> id_lexico)
                 	 {
                 	 	echo '<script language="javascript">confirm(" '.$id_from.' - '.$id_to.'cenário para léxico")</script>';
                 	 	//adiciona relação de cenário para léxico
                 	 }
                 }
                 if($tipo=="c")// Destino é um cenário (id_cenario_to)
                 {
                     if(strcasecmp($tipo_from,'cenario') == 0)// Origem é um cenario (id_cenario_from -> id_cenario_to)
                     {
                 	 	echo '<script language="javascript">confirm(" '.$id_from.' - '.$id_to.'cenário para cenário")</script>';
                 	 	// Relacionamentos do tipo cenário para cenário
                 	 	// Adiciona relacao de cenario para cenario na tabela centocen
                 	 	//$q = "INSERT 
				      	//		INTO centocen (id_cenario_from, id_cenario_to)
				       	//		VALUES ($id_from, " . $vetor_cenarios[$j]->id_cenario . ")";
				       	//mysql_query($q) or die("Erro ao enviar a query de INSERT na centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                 	 }
                 }
                 $i+1;
            }
        }elseif( $texto[$i] == "}" )
        {
            $parser--;
         
        }
        $i++;
    }
}   
 

?>