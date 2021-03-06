<?php
include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");

include("dataBase/DatabaseRemoveCenario.php");


/*
if (!(class_exists("PGDB"))) {
    include("bd_class.php");
}
*/

// chkUser(): checa se o usu�rio acessando foi autenticado checks whether the user has been authenticated  
// presence of the session variable $ id_current_user). If it has
// already been authenticated, continues with the execution of the script.
// Otherwise, it opens a logon window. 

if (!(function_exists("chkUser"))) 
{
    function checkUser($url)
    {
        assert($url != null, "url must not be null");
                
        if(!(isset($_SESSION['id_usuario_corrente'])))    
        {
            ?>
            <script language="javascript1.3">
                    open('login.php?url=<?=$url?>', 'login', 'dependent,height=430,width=490,resizable,scrollbars,titlebar');
            </script>
            <?php
            exit();
        }
        else
        {
        	//Nothing to do.
        }
    }
}
else
{
	//Nothing to do.
}
###################################################################
# Insere um lexico no banco de dados.
# Recebe o id_projeto, nome, noção, impacto e os sinonimos. (1.1)
# Insere os valores do lexico na tabela LEXICO. (1.2)
# Insere todos os sinonimos na tabela SINONIMO. (1.3)
# Devolve o id_lexico. (1.4)
#
###################################################################
if (!(function_exists("inclui_lexico"))) 
{
    function inclui_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao)
    {
        assert($id_projeto != null, "id_projeto must not be null");
        assert($nome != null, "nome must not be null");
        assert($nocao != null, "nocao must not be null");
        assert($classificacao != null, "classificacao must not be null");
        
        $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $data = date("Y-m-d");
     
                
        $query = "INSERT INTO lexico (id_projeto, data, nome, nocao, impacto, tipo)
                    VALUES ($id_projeto, '$data', '" .prepara_dado(strtolower($nome)). "',
                    '".prepara_dado($nocao)."', '".prepara_dado($impacto)."', '$classificacao')";
				
        mysql_query($query) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        //sinonimo
        $newLexicoId = mysql_insert_id($result);
        
        if (!is_array($sinonimos))
        {
            $sinonimos = array();
        }
        else
        {
            //Nothing to do.
        }
        
        foreach($sinonimos as $novoSinonimo)
        {
            $query = "INSERT INTO sinonimo (id_lexico, nome, id_projeto)
                        VALUES ($newLexicoId, '" . prepara_dado(strtolower($novoSinonimo)) . "', $id_projeto)";
            mysql_query($query, $result) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        }
        
        $query = "SELECT max(id_lexico) FROM lexico";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $result = mysql_fetch_row($queryResult);
        
        return $result[0];
    }
}
else
{
	//Nothing to do.
}

if (!(function_exists("reload"))) 
{
    function reload($url) 
    {
    	assert($url != null , "Url must not be null!!");
    	
        ?>
        <script language="javascript1.3">
            location.replace('<?=$url?>');
        </script>
        <?php
    }
}
else
{
	//Nothing to do.
}

if (!(function_exists("breakpoint"))) 
{
    function breakpoint($number) 
    {
        assert($number != null, "number must not be null");
        
        ?>
        <script language="javascript1.3">	
            alert('<?=$number?>');
        </script>
        <?php
    }
}
else
{
	//Nothing to do.
}

if (!(function_exists("simple_query")))
{
    function simple_query($field, $table, $where)
    {
    	assert($field != null, "field must not be null");
    	assert($table != null, "table must not be null");
    	assert($where != null, "where must not be null");
    	
        $result = bd_connect() or die("Erro ao conectar ao SGBD");
        $query = "SELECT $field FROM $table WHERE $where";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query");
        $result = mysql_fetch_row($queryResult);
        return $result[0];        
    }
}
else
{
	//Nothing to do.
}


// Para a correta inclusao de um cenario, uma serie de procedimentos
// precisam ser tomados (relativos ao requisito 'navegacao circular'):
//
// 1. Incluir o novo cenario na base de dados;
// 2. Para todos os cenarios daquele projeto, exceto o rec�m inserido:
//      2.1. Procurar em contexto e episodios
//           por ocorrencias do titulo do cenario incluido;
//      2.2. Para os campos em que forem encontradas ocorrencias:
//          2.2.1. Incluir entrada na tabela 'centocen';
//      2.3. Procurar em contexto e episodios do cenario incluido
//           por ocorrencias de titulos de outros cenarios do mesmo projeto;
//      2.4. Se achar alguma ocorrencia:
//          2.4.1. Incluir entrada na tabela 'centocen';
// 3. Para todos os nomes de termos do lexico daquele projeto:
//      3.1. Procurar ocorrencias desses nomes no titulo, objetivo, contexto,
//           recursos, atores, episodios, do cenario incluido;
//      3.2. Para os campos em que forem encontradas ocorrencias:
//          3.2.1. Incluir entrada na tabela 'centolex';

if (!(function_exists("adicionar_cenario")))
{
        function adicionar_cenario($id_projeto, $title, $objective, $context, $actors, $resources, $exception, $episodes)
        {
            // $exception can be null
            assert($id_projeto != null, "id_projeto must not be null");
            assert($title != null, "title must not be null");
            assert($objective != null, "objective must not be null");
            assert($context != null, "context must not be null");

            // Conecta ao SGBD
            $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            // Inclui o cenario na base de dados (sem transformar os campos, sem criar os relacionamentos)
            $id_incluido = includeScenario($id_projeto, $title, $objective, $context, $actors, $resources, $exception, $episodes);

            $query = "SELECT id_cenario, titulo, contexto, episodios
                  FROM cenario
                  WHERE id_projeto = $id_projeto
                  AND id_cenario != $id_incluido
                  ORDER BY CHAR_LENGTH(titulo) DESC";
            $queryResult = mysql_query($query) or die("Erro ao enviar a query de SELECT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

            ### PREENCHIMENTO DAS TABELAS LEXTOLEX E CENTOCEN PARA MONTAGEM DO MENU LATERAL

            // Verifica ocorrências do titulo do cenario incluido no contexto 
            // e nos episodios de todos os outros cenarios e adiciona os relacionamentos,
            // caso possua, na tabela centocen

            while ($result = mysql_fetch_array($queryResult)) // Para todos os cenarios
            {
                    $tituloEscapado = escapa_metacaracteres( $title );
                    $regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i"; 

                    if ((preg_match($regex, $result['contexto']) != 0) ||
                        (preg_match($regex, $result['episodios']) != 0)) 
                    {
                            $query = "INSERT INTO centocen (id_cenario_from, id_cenario_to)
                                        VALUES (" . $result['id_cenario'] . ", $id_incluido)";
                            mysql_query($query) or die("Erro ao enviar a query de INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  
                    }

                    $tituloEscapado = escapa_metacaracteres( $result['titulo'] );
                    $regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i";        

                    if ((preg_match($regex, $context) != 0) ||
                        (preg_match($regex, $episodes) != 0))
                    {
                            $query = "INSERT INTO centocen (id_cenario_from, id_cenario_to) VALUES ($id_incluido, " . $result['id_cenario'] . ")"; //(2.4.1)
                            mysql_query($query) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
                    }

            }

            // Verifica a ocorrencia do nome de todos os lexicos nos campos titulo, objetivo,
            // contexto, atores, recursos, episodios e excecao do cenario incluido 

            $query = "SELECT id_lexico, nome FROM lexico WHERE id_projeto = $id_projeto";
            $queryResult = mysql_query($query) or die("Erro ao enviar a query de SELECT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

            while ($result2 = mysql_fetch_array($queryResult)) 
            {
                    $nomeEscapado = escapa_metacaracteres( $result2['nome']);
                    $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";

                    if ((preg_match($regex, $title) != 0) ||
                        (preg_match($regex, $objective) != 0) ||
                        (preg_match($regex, $context) != 0) ||
                        (preg_match($regex, $actors) != 0) ||
                        (preg_match($regex, $resources) != 0) ||
                        (preg_match($regex, $episodes) != 0) ||
                        (preg_match($regex, $exception) != 0) ) 
                    {

                            $queryCenario = "SELECT * FROM centolex WHERE id_cenario = $id_incluido AND id_lexico = " . $result2['id_lexico'];
                            $queryResultCenario = mysql_query($queryCenario) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                            $resultArrayCenario = mysql_fetch_array($queryResultCenario);

                            if ($resultArrayCenario == false)
                            {
                                $query = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_incluido, " . $result2['id_lexico'] . ")";
                                mysql_query($query) or die("Erro ao enviar a query de INSERT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
                            }
                    }
            }

            // Verifica a ocorrencia dos sinonimos de todos os lexicos nos campos titulo, objetivo,
            // contexto, atores, recursos, episodios e excecao do cenario incluido
            //Sinonimos

            $querySinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = $id_projeto AND id_pedidolex = 0";
            $queryResultSinonimos = mysql_query($querySinonimos) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            $nomesSinonimos = array();
            $id_lexicoSinonimo = array();

            while ($rowSinonimo = mysql_fetch_array($queryResultSinonimos))
            {
                $nomesSinonimos[] = $rowSinonimo["nome"];
                $id_lexicoSinonimo[] = $rowSinonimo["id_lexico"];
            }

            $qlc = "SELECT id_cenario, titulo, contexto, episodios, objetivo, atores, recursos, excecao
                    FROM cenario
                    WHERE id_projeto = $id_projeto
                    AND id_cenario = $id_incluido";
            $count = count($nomesSinonimos);

            for ($i = 0; $i < $count; $i++)
            {
                    $queryResult = mysql_query($qlc) or die("Erro ao enviar a query de busca<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

                    // verifica sinonimos dos outros lexicos no cenario incluído
                    while ($result = mysql_fetch_array($queryResult)) 
                    {    
                            $nomeSinonimoEscapado = escapa_metacaracteres( $nomesSinonimos[$i] );
                            $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                            if ((preg_match($regex, $objective) != 0) ||
                                (preg_match($regex, $context) != 0) ||
                                (preg_match($regex, $actors) != 0) ||
                                (preg_match($regex, $resources) != 0) ||
                                (preg_match($regex, $episodes) != 0) ||
                                (preg_match($regex, $exception) != 0) ) 
                            {

                                    $queryCenario = "SELECT * FROM centolex WHERE id_cenario = $id_incluido AND id_lexico = $id_lexicoSinonimo[$i] ";
                                    $queryResultCenario = mysql_query($queryCenario) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                                    $resultArrayCenario = mysql_fetch_array($queryResultCenario);

                                    if ($resultArrayCenario == false)
                                    {
                                        $query = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_incluido, $id_lexicoSinonimo[$i])";
                                        mysql_query($query) or die("Erro ao enviar a query de insert no centolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
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
                    }
            } 
        
    }
}
else
{
	//Nothing to do.
}

//
// Para a correta inclusao de um termo no lexico, uma serie de procedimentos
// precisam ser tomados (relativos ao requisito 'navegacao circular'):
//
// 1. Incluir o novo termo na base de dados;
// 2. Para todos os cenarios daquele projeto:
//      2.1. Procurar em titulo, objetivo, contexto, recursos, atores, episodios
//           por ocorrencias do termo incluido ou de seus sinonimos;
//      2.2. Para os campos em que forem encontradas ocorrencias:
//              2.2.1. Incluir entrada na tabela 'centolex';
// 3. Para todos termos do lexico daquele projeto (menos o recem-inserido):
//      3.1. Procurar em nocao, impacto por ocorrencias do termo inserido ou de seus sinonimos;
//      3.2. Para os campos em que forem encontradas ocorrencias:
//              3.2.1. Incluir entrada na tabela 'lextolex';
//      3.3. Procurar em nocao, impacto do termo inserido por
//           ocorrencias de termos do lexico do mesmo projeto;
//      3.4. Se achar alguma ocorrencia:
//              3.4.1. Incluir entrada na table 'lextolex';

if (!(function_exists("adicionar_lexico"))) 
{
    function adicionar_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao)
    {
        // $exception can be null
        assert($id_projeto != null, "id_projeto must not be null");
        assert($nome != null, "nome must not be null");
        assert($nocao != null, "nocao must not be null");
        assert($classificacao != null, "classificacao must not be null");
        
        $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        $id_incluido = inclui_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao); // (1)
        
        assert($id_incluido != null, "id_incluido must not be null");
        
        $queryResult = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios
              FROM cenario
              WHERE id_projeto = $id_projeto";
        
        $executeQuery = mysql_query($queryResult) or die("Erro ao enviar a query de SELECT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        while ($result = mysql_fetch_array($executeQuery)) // Para todos os cenarios
        {
        
           $nomeEscapado = escapa_metacaracteres( $nome );
           $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";
         
            if ((preg_match($regex, $result['objetivo']) != 0) ||
                (preg_match($regex, $result['contexto']) != 0) ||
                (preg_match($regex, $result['atores']) != 0)   ||
                (preg_match($regex, $result['recursos']) != 0) ||
                (preg_match($regex, $result['excecao']) != 0)  ||
                (preg_match($regex, $result['episodios']) != 0) )
            {
        
                $query = "INSERT INTO centolex (id_cenario, id_lexico)
                            VALUES (" . $result['id_cenario'] . ", $id_incluido)"; //2.2.1
        
                mysql_query($query) or die("Erro ao enviar a query de INSERT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
        }

        
        //sinonimos do novo lexico
        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++)
        {
            $executeQuery = mysql_query($queryResult) or die("Erro ao enviar a query de SELECT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($result2 = mysql_fetch_array($executeQuery))
            {       
                $nomeSinonimoEscapado = escapa_metacaracteres( $sinonimos[$i] );
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";
                
                if ((preg_match($regex, $result2['objetivo']) != 0) ||
                    (preg_match($regex, $result2['contexto']) != 0) ||
                    (preg_match($regex, $result2['atores']) != 0)   ||
                    (preg_match($regex, $result2['recursos']) != 0) ||
                    (preg_match($regex, $result2['excecao']) != 0)  ||
                    (preg_match($regex, $result2['episodios']) != 0))
                { 
                            
                    $queryLexico = "SELECT * FROM centolex WHERE id_cenario = " . $result2['id_cenario'] . " AND id_lexico = $id_incluido ";
                    $queryResultLexico = mysql_query($queryLexico) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArrayLexico = mysql_fetch_array($queryResultLexico);
                
                    if ($resultArrayLexico == false)
                    {
                    
                        $query = "INSERT INTO centolex (id_cenario, id_lexico)
                             VALUES (" . $result2['id_cenario'] . ", $id_incluido)";                   
                    
                        mysql_query($query) or die("Erro ao enviar a query de INSERT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    }
                    else
                    {
                        // Nothing to 
                    }
                }                
            }
        }
        
        
        $qlo = "SELECT id_lexico, nome, nocao, impacto, tipo
               FROM lexico
               WHERE id_projeto = $id_projeto
               AND id_lexico != $id_incluido";
                     
        //pega todos os outros lexicos
        $queryResult = mysql_query($qlo) or die("Erro ao enviar a query de SELECT no LEXICO<br>" . mysql_error() .
        								"<br>" . __FILE__ . __LINE__);
        
        while ($result = mysql_fetch_array($queryResult)) 
        {
            $nomeEscapado = escapa_metacaracteres( $nome );
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";
            
            if ((preg_match($regex, $result['nocao']) != 0 ) || (preg_match($regex, $result['impacto'])!= 0))
            {
                
                $queryLexico = "SELECT * FROM lextolex WHERE id_lexico_from = " . $result['id_lexico'] . " AND id_lexico_to = $id_incluido";
                $queryResultLexico = mysql_query($queryLexico) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                $resultArrayLexico = mysql_fetch_array($queryResultLexico);
      
                if ($resultArrayLexico == false)
                {
                    $query = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                          VALUES (" . $result['id_lexico'] . ", $id_incluido)";
                    
                    mysql_query($query) or die("Erro ao enviar a query de INSERT no lextolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                }
            }
         
			$nomeEscapado = escapa_metacaracteres( $result['nome'] );
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";
         
            if ((preg_match($regex, $nocao) != 0) || (preg_match($regex, $impacto) != 0))
            {
                $query = "INSERT INTO lextolex (id_lexico_from, id_lexico_to) VALUES ($id_incluido, " . $result['id_lexico'] . ")"; 
                mysql_query($query) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
            }
       
        }
        
        
        //lexico para lexico
        
        $queryLexico = "SELECT id_lexico, nome, nocao, impacto
              FROM lexico
              WHERE id_projeto = $id_projeto
              AND id_lexico != $id_incluido";                                                                     
        
        //sinonimos dos outros lexicos no texto do inserido
        
        $queryResult = mysql_query($queryLexico) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++)
        {
            while ($resultl = mysql_fetch_array($queryResult))
            {
                               
                $nomeSinonimoEscapado = escapa_metacaracteres( $sinonimos[$i] );
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";
                
                if ((preg_match($regex, $resultl['nocao']) != 0)  || (preg_match($regex, $resultl['impacto']) != 0))
                {
                                    
                    $queryLexico = "SELECT * FROM lextolex WHERE id_lexico_from = " . $resultl['id_lexico'] . " AND id_lexico_to = $id_incluido";
                    $queryResultLexico = mysql_query($queryLexico) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArrayLexico = mysql_fetch_array($queryResultLexico);
                    
                    if ( $resultArrayLexico == false )
                    {                        
                        $query = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                        VALUES (" . $resultl['id_lexico'] . ", $id_incluido)";            
                        mysql_query($query) or die("Erro ao enviar a query de insert no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    }
                }
            }
        }
        
        //sinonimos ja existentes
        $querySinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = $id_projeto AND id_lexico != $id_incluido AND id_pedidolex = 0";
        $queryResultSinonimos = mysql_query($querySinonimos) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $nomesSinonimos = array();
        $id_lexicoSinonimo = array();
        
        while($rowSinonimo = mysql_fetch_array($queryResultSinonimos))
        {
            $nomesSinonimos[] = $rowSinonimo["nome"];
            $id_lexicoSinonimo[] = $rowSinonimo["id_lexico"];
        }
    }
}
else
{
	//Nothing to do.
}


###################################################################
# Essa funcao recebe um id de cenario e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("alteraCenario"))) 
{
    function alteraCenario($id_projeto, $id_cenario, $title, $objective, $context, $actors, $resources, $exception, $episodes)
    {
        // $exception can be null
        assert($id_projeto != null, "id_projeto must not be null");
        assert($title != null, "title must not be null");
        assert($objective != null, "objective must not be null");
        assert($context != null, "context must not be null");
        
        $DB = new PGDB () ;
        $sql1 = new QUERY ($DB) ;
        $sql2 = new QUERY ($DB) ;
        $sql3 = new QUERY ($DB) ;
        $sql4 = new QUERY ($DB) ;
               
        # Remove o relacionamento entre o cenario a ser alterado e outros cenarios que o referenciam
        $sql1->execute ("DELETE FROM centocen WHERE id_cenario_from = $id_cenario") ;
        $sql2->execute ("DELETE FROM centocen WHERE id_cenario_to = $id_cenario") ;
        # Remove o relacionamento entre o cenario a ser alterado e o seu lexico
        $sql3->execute ("DELETE FROM centolex WHERE id_cenario = $id_cenario") ;
        
        # atualiza o cenario
        $sql4->execute ("update cenario set 
		objetivo = '".prepara_dado($objective)."', 
		contexto = '".prepara_dado($context)."', 
		atores = '".prepara_dado($actors)."', 
		recursos = '".prepara_dado($resources)."', 
		episodios = '".prepara_dado($episodes)."', 
		excecao = '".prepara_dado($exception)."' 
		where id_cenario = $id_cenario ");
        
        // monta_relacoes($id_projeto);
        
        // Conecta ao SGBD
        $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        $query = "SELECT id_cenario, titulo, contexto, episodios
              FROM cenario
              WHERE id_projeto = $id_projeto
              AND id_cenario != $id_cenario
              ORDER BY CHAR_LENGTH(titulo) DESC";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de SELECT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        while ($result = mysql_fetch_array($queryResult)) // Para todos os cenarios
        {
		$tituloEscapado = escapa_metacaracteres( $title );
	       	$regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i"; 
	                
	       	if((preg_match($regex, $result['contexto']) != 0) || (preg_match($regex, $result['episodios']) != 0) ) 
           	{
	         
	        	$query = "INSERT INTO centocen (id_cenario_from, id_cenario_to)
                                 VALUES (" . $result['id_cenario'] . ", $id_cenario)"; // (2.2.1)
	        	mysql_query($query) or die("Erro ao enviar a query de INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  
	        }
	        else
	        {
	        	//Nothing to do.
	        }
			$tituloEscapado = escapa_metacaracteres( $result['titulo'] );
        	$regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i";        
      
	        if((preg_match($regex, $context) != 0) || (preg_match($regex, $episodes) != 0)) 
         	{
        		$query = "INSERT INTO centocen (id_cenario_from, id_cenario_to) VALUES ($id_cenario, " . $result['id_cenario'] . ")"; //(2.4.1)
        		mysql_query($query) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
        	}   
        	else
        	{
        		//Nothing to do.
        	}
        }
        
      
        $query = "SELECT id_lexico, nome FROM lexico WHERE id_projeto = $id_projeto";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de SELECT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        while ($result2 = mysql_fetch_array($queryResult)) 
        {

		$nomeEscapado = escapa_metacaracteres( $result2['nome'] );
        	$regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";
         
         	if ((preg_match($regex, $title) != 0) ||
                    (preg_match($regex, $objective) != 0) ||
                    (preg_match($regex, $context) != 0) ||
                    (preg_match($regex, $actors) != 0) ||
                    (preg_match($regex, $resources) != 0) ||
                    (preg_match($regex, $episodes) != 0) ||
                    (preg_match($regex, $exception) != 0) ) 
        	{
                
                        $queryCenario = "SELECT * FROM centolex WHERE id_cenario = $id_cenario AND id_lexico = " . $result2['id_lexico'];
                        $queryResultCenario = mysql_query($queryCenario) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                        $resultArrayCenario = mysql_fetch_array($queryResultCenario);
        
	        	if ($resultArrayCenario == false)
	        	{
                            $query = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_cenario, " . $result2['id_lexico'] . ")";
                            mysql_query($query) or die("Erro ao enviar a query de INSERT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
	        	}
	        	else
	        	{
	        		//Nothing to do.
	        	}
        	}   
      
        }   
        
        
      	//Sinonimos
        $querySinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = $id_projeto AND id_pedidolex = 0";
        $queryResultSinonimos = mysql_query($querySinonimos) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $nomesSinonimos = array();
        $id_lexicoSinonimo = array();
        
        while($rowSinonimo = mysql_fetch_array($queryResultSinonimos))
        {
            $nomesSinonimos[] = $rowSinonimo["nome"];
            $id_lexicoSinonimo[] = $rowSinonimo["id_lexico"];
        }
      
        $qlc = "SELECT id_cenario, titulo, contexto, episodios, objetivo, atores, recursos, excecao
              FROM cenario
              WHERE id_projeto = $id_projeto
              AND id_cenario = $id_cenario";
        $count = count($nomesSinonimos);
        
        for ($i = 0; $i < $count; $i++)
        {
            $queryResult = mysql_query($qlc) or die("Erro ao enviar a query de busca<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($result = mysql_fetch_array($queryResult)) // verifica sinonimos dos lexicos no cenario incluído
            {
                    $nomeSinonimoEscapado = escapa_metacaracteres( $nomesSinonimos[$i] );
	            $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";
	            
		        if ((preg_match($regex, $objective) != 0) ||
		             (preg_match($regex, $context) != 0) ||
		             (preg_match($regex, $actors) != 0) ||
		             (preg_match($regex, $resources) != 0) ||
		             (preg_match($regex, $episodes) != 0) ||
		             (preg_match($regex, $exception) != 0) ) 
		        {
		            
		            $queryCenario = "SELECT * FROM centolex WHERE id_cenario = $id_cenario AND id_lexico = $id_lexicoSinonimo[$i] ";
		            $queryResultCenario = mysql_query($queryCenario) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		            $resultArrayCenario = mysql_fetch_array($queryResultCenario);
		            
		            if ($resultArrayCenario == false)
		            {
		                $query = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_cenario, $id_lexicoSinonimo[$i])";
		                mysql_query($query) or die("Erro ao enviar a query de insert no centolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
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
    		}  
        } 
    }    
}
else
{
	//Nothing to do.
}


###################################################################
# Essa funcao recebe um id de lexico e remove todos os seus
# links e relacionamentos existentes em todas as tabelas do banco.
###################################################################
if (!(function_exists("removeLexico")))
{
    function removeLexico($id_projeto,$id_lexico)
    {
    	assert($id_projeto != null, "id_projeto must not be null");
    	assert($id_lexico != null, "id_lexico must not be null");
    	
        $DB = new PGDB();
        $delete = new QUERY($DB);        
        
        # Remove o relacionamento entre o lexico a ser removido e outros lexicos que o referenciam
        $delete->execute ("DELETE FROM lextolex WHERE id_lexico_from = $id_lexico") ;
        $delete->execute ("DELETE FROM lextolex WHERE id_lexico_to = $id_lexico") ;
        $delete->execute ("DELETE FROM centolex WHERE id_lexico = $id_lexico") ;
        
        # Remove o lexico escolhido
        $delete->execute ("DELETE FROM sinonimo WHERE id_lexico = $id_lexico") ;
        $delete->execute ("DELETE FROM lexico WHERE id_lexico = $id_lexico") ;
    }
}
else
{
	//Nothing to do.
}



###################################################################
# Essa funcao recebe um id de lexico e remove todos os seus
# links e relacionamentos existentes em todas as tabelas do banco.
###################################################################

if (!(function_exists("alteraLexico")))
{
    function alteraLexico($id_projeto, $id_lexico, $nome, $nocao, $impacto, $sinonimos, $classificacao)
    {
        assert($id_projeto != null, "id_projeto must not be null");
        assert($id_lexico != null, "id_lexico must not be null");
        assert($nome != null, "nome must not be null");
        assert($nocao != null, "nocao must not be null");
        assert($classificacao != null, "classificacao must not be null");
        
        $DB = new PGDB();
        $delete = new QUERY($DB);        
        
        # Remove os relacionamento existentes anteriormente
        $delete->execute ("DELETE FROM lextolex WHERE id_lexico_from = $id_lexico") ;
        $delete->execute ("DELETE FROM lextolex WHERE id_lexico_to = $id_lexico") ;
        $delete->execute ("DELETE FROM centolex WHERE id_lexico = $id_lexico") ;
        
        # Remove todos os sinonimos cadastrados anteriormente
        $delete->execute ("DELETE FROM sinonimo WHERE id_lexico = $id_lexico") ;
        
        # Altera o lexico escolhido
        $delete->execute ("UPDATE lexico SET 
		nocao = '".prepara_dado($nocao)."', 
		impacto = '".prepara_dado($impacto)."', 
		tipo = '$classificacao' 
		where id_lexico = $id_lexico");
        
        $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        # Fim altera lexico escolhido
        ### VERIFICACAO DE OCORRENCIA EM CENARIOS ###
        
        # Verifica se há alguma ocorrencia do titulo do lexico nos cenarios existentes no banco
       
        $queryResult = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios
                        FROM cenario
                        WHERE id_projeto = $id_projeto";
        
        $queryResult = mysql_query($queryResult) or die("Erro ao enviar a query de SELECT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        while ($result = mysql_fetch_array($queryResult)) //Para todos os cenarios
        {
            $nomeEscapado = escapa_metacaracteres( $nome );
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";
         
            if ((preg_match($regex, $result['objetivo']) != 0) ||
                (preg_match($regex, $result['contexto']) != 0) ||
                (preg_match($regex, $result['atores']) != 0)   ||
                (preg_match($regex, $result['recursos']) != 0) ||
                (preg_match($regex, $result['excecao']) != 0)  ||
                (preg_match($regex, $result['episodios']) != 0))
            {
        
                $query = "INSERT INTO centolex (id_cenario, id_lexico)
                            VALUES (" . $result['id_cenario'] . ", $id_lexico)"; //2.2.1
        
                mysql_query($query) or die("Erro ao enviar a query de INSERT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
          
            }
            else
            {
            	//Nothing to do.
            }
        }

        # Fim da verificacao
        
        ########
 	    
        # Verifica se há alguma ocorrencia de algum dos sinonimos do lexico nos cenarios existentes no banco
       
        //&sininonimos = sinonimos do novo lexico
        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++) //Para cada sinonimo
        {
            $queryResult = mysql_query($queryResult) or die("Erro ao enviar a query de SELECT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($result2 = mysql_fetch_array($queryResult))// para cada cenario
            {
                $nomeSinonimoEscapado = escapa_metacaracteres ( $sinonimos[$i] );
                $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";
                
                if ((preg_match($regex, $result2['objetivo']) != 0) ||
                    (preg_match($regex, $result2['contexto']) != 0) ||
                    (preg_match($regex, $result2['atores']) != 0)   ||
                    (preg_match($regex, $result2['recursos']) != 0) ||
                    (preg_match($regex, $result2['excecao']) != 0)  ||
                    (preg_match($regex, $result2['episodios']) != 0))
                { 
                   // $query = "INSERT INTO centolex (id_cenario, id_lexico)
                   //      VALUES (" . $result2['id_cenario'] . ", $id_lexico)";                   
                
                  //  mysql_query($query) or die("Erro ao enviar a query de INSERT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    
                }
                else
                {
                	//Nothing to do.
                }                
            }            
        }
        
        # Fim da verificacao
        
        ########
        
        ### VERIFICACAO DE OCORRENCIA EM LEXICOS
        
        ########
 	    
 	    # Verifica a ocorrencia do titulo do lexico alterado no texto dos outros lexicos
        # Verifica a ocorrencia do titulo dos outros lexicos no lexico alterado
        
        //select para pegar todos os outros lexicos
        $queryIdlexico = "SELECT id_lexico, nome, nocao, impacto, tipo
               FROM lexico
               WHERE id_projeto = $id_projeto
               AND id_lexico <> $id_lexico";
                     
        $queryResult = mysql_query($queryIdlexico) or die("Erro ao enviar a query de SELECT no LEXICO<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        while ($result = mysql_fetch_array($queryResult)) // para cada lexico exceto o que esta sendo alterado
        {
            # Verifica a ocorrencia do titulo do lexico alterado no texto dos outros lexicos
        	        
            $nomeEscapado = escapa_metacaracteres( $nome );
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";
            
            if ((preg_match($regex, $result['nocao']) != 0 ) ||
                (preg_match($regex, $result['impacto'])!= 0))
            {
                    $query = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                                VALUES (" . $result['id_lexico'] . ", $id_lexico)";
                
                    mysql_query($query) or die("Erro ao enviar a query de INSERT no lextolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            }
            else
            {
                    //Nothing to do.
            }
         
            # Verifica a ocorrencia do titulo dos outros lexicos no texto do lexico alterado
            
            $nomeEscapado = escapa_metacaracteres( $result['nome'] );
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";
         
            if ((preg_match($regex, $nocao) != 0) ||
               (preg_match($regex, $impacto) != 0) )
            {
                    $query = "INSERT INTO lextolex (id_lexico_from, id_lexico_to) 
                                VALUES ($id_lexico, " . $result['id_lexico'] . ")"; 
                    mysql_query($query) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
            }
            else
            {
            	//Nothing to do.
            }
       
        }
        
        # Fim da verificao por titulo
        $queryLexico = "SELECT id_lexico, nome, nocao, impacto
                        FROM lexico
                        WHERE id_projeto = $id_projeto
                        AND id_lexico <> $id_lexico";                                                                     
        
        # Verifica a ocorrencia dos sinonimos do lexico alterado nos outros lexicos
        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++)// para cada sinonimo do lexico alterado
        {
         	
                $queryResult = mysql_query($queryLexico) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);        
                while ($resultl = mysql_fetch_array($queryResult)) // para cada lexico exceto o alterado
                {
                    $nomeSinonimoEscapado = escapa_metacaracteres( $sinonimos[$i] );
                    $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";

                    // verifica sinonimo[i] do lexico alterado no texto de cada lexico

                    if ((preg_match($regex, $resultl['nocao']) != 0) ||
                        (preg_match($regex, $resultl['impacto']) != 0))
                    {
                             // Verifica  se a relacao encontrada ja se encontra no banco de dados. Se tiver nao faz nada, senao cadastra uma nopva relacao
                            $queryVerification = "SELECT * FROM lextolex where id_lexico_from=" . $resultl['id_lexico'] . " and id_lexico_to=$id_lexico";
                            echo("Query: ".$queryVerification."<br>");
                            $resultado = mysql_query($queryVerification) or die("Erro ao enviar query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                            
                            if (!resultado)
                            {
                                    $query = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                                            VALUES (" . $resultl['id_lexico'] . ", $id_lexico)";            
                                    mysql_query($query) or die("Erro ao enviar a query de insert(sinonimo2) no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);	
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
            }
        }
    	
    	# Verifica a ocorrencia dos sinonimos dos outros lexicos no lexico alterado
        $querySinonimos = "SELECT nome, id_lexico 
        		FROM sinonimo 
        		WHERE id_projeto = $id_projeto 
        		AND id_lexico <> $id_lexico 
        		AND id_pedidolex = 0";
        
        $queryResultSinonimos = mysql_query($querySinonimos) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        $nomesSinonimos = array();
        $id_lexicoSinonimo = array();
        
        while ($rowSinonimo = mysql_fetch_array($queryResultSinonimos))
        {
        	$nomeSinonimoEscapado = escapa_metacaracteres( $rowSinonimo["nome"] );
			$regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";
        
        	if ((preg_match($regex, $nocao) != 0) ||
                    (preg_match($regex, $impacto) != 0))
        	{
                            // Verifica  se a relacao encontrada ja se encontra no banco de dados. Se tiver nao faz nada, senao cadastra uma nopva relacao
                            $queryVerification = "SELECT * FROM lextolex where id_lexico_from=$id_lexico and id_lexico_to=".$rowSinonimo['id_lexico'];
                            $resultado = mysql_query($queryVerification) or die("Erro ao enviar query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);	
                            if (!resultado)
                            {		
				   $query = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                                        VALUES ($id_lexico, " . $rowSinonimo['id_lexico'] . ")";            
	                    
                                    mysql_query($query) or die("Erro ao enviar a query de insert(sinonimo) no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);	
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
        }    
    
        # Cadastra os sinonimos novamente
        
        if (!is_array($sinonimos))
        {
        	$sinonimos = array();
        }
        else
        {
        	//Nothing to do.
        }
        
        foreach ($sinonimos as $novoSinonimo)
        {
         	$query = "INSERT INTO sinonimo (id_lexico, nome, id_projeto)
                            VALUES ($id_lexico, '" . prepara_dado(strtolower($novoSinonimo)) . "', $id_projeto)";
            
                mysql_query($query, $result) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        }
     
		# Fim - cadastro de sinonimos
    }
}
else
{
	//Nothing to do.
}



###################################################################
# Essa funcao recebe um id de conceito e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("removeConceito"))) 
{
    function removeConceito($id_projeto, $id_conceito)
    {
    	assert($id_projeto != null, "id_projeto must not be null");
    	assert($id_conceito != null, "id_conceito must not be null");
    	
        $DB = new PGDB () ;
        $sql = new QUERY ($DB) ;
        $sql2 = new QUERY ($DB) ;
        $sql3 = new QUERY ($DB) ;
        $sql4 = new QUERY ($DB) ;
        $sql5 = new QUERY ($DB) ;
        $sql6 = new QUERY ($DB) ;
        $sql7 = new QUERY ($DB) ;
        # Este select procura o cenario a ser removido
        # dentro do projeto
        
        $sql2->execute ("SELECT * FROM conceito WHERE id_projeto = $id_projeto and id_conceito = $id_conceito") ;
        if ($sql2->getntuples() == 0)
        {
            //echo "<BR> Cenario nao existe para esse projeto." ;
        }
        else
        {
            $record = $sql2->gofirst ();
            $nomeConceito = $record['nome'] ;
            # tituloCenario = Nome do cenario com id = $id_cenario
        }
        
        # [ATENCAO] Essa query pode ser melhorada com um join
        //print("<br>SELECT * FROM cenario WHERE id_projeto = $id_projeto");
        /*  $sql->execute ("SELECT * FROM cenario WHERE id_projeto = $id_projeto AND id_cenario != $tituloCenario");
        if ($sql->getntuples() == 0){
        echo "<BR> Projeto n�o possui cenarios." ;
        }else{*/
        $queryResult = "SELECT * FROM conceito WHERE id_projeto = $id_projeto AND id_conceito != $id_conceito";
        //echo($queryResult)."          ";
        $queryResult = mysql_query($queryResult) or die("Erro ao enviar a query de SELECT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        while ($result = mysql_fetch_array($queryResult))
        {
            # Percorre todos os cenarios tirando as tag do conceito
            # a ser removido
            //$record = $sql->gofirst ();
            //while($record !='LAST_RECORD_REACHED'){
            $idConceitoRef = $result['id_conceito'] ;
            $nomeAnterior = $result['nome'] ;
            $descricaoAnterior = $result['descricao'] ;
            $namespaceAnterior = $result['namespace'] ;
            #echo        "/<a title=\"Cen�rio\" href=\"main.php?t='c'&id=$id_cenario>($tituloCenario)<\/a>/mi"  ;
            #$episodiosAnterior = "<a title=\"Cen�rio\" href=\"main.php?t=c&id=38\">robin</a>" ;
            /*"'<a title=\"Cen�rio\" href=\"main.php?t=c&id=38\">robin<\/a>'si" ; */
            $tiratag = "'<[\/\!]*?[^<>]*?>'si" ;
            //$tiratagreplace = "";
            //$tituloCenario = preg_replace($tiratag,$tiratagreplace,$tituloCenario);
            $regexp = "/<a[^>]*?>($nomeConceito)<\/a>/mi" ;//rever
            $replace = "$1";
            //echo($episodiosAnterior)."   ";
            //$tituloAtual = $tituloAnterior ;
            //*$tituloAtual = preg_replace($regexp,$replace,$tituloAnterior);*/
            $descricaoAtual = preg_replace($regexp,$replace,$descricaoAnterior);
            $namespaceAtual = preg_replace($regexp,$replace,$namespaceAnterior);
            /*echo "ant:".$episodiosAtual ;
            echo "<br>" ;
            echo "dep:".$episodiosAnterior ;*/
            // echo($tituloCenario)."   ";
            // echo($episodiosAtual)."  ";
            //print ("<br>update cenario set objetivo = '$objetivoAtual',contexto = '$contextoAtual',atores = '$atoresAtual',recursos = '$recursosAtual',episodios = '$episodiosAtual' where id_cenario = $idCenarioRef ");
            $sql7->execute ("update conceito set descricao = '$descricaoAtual', namespace = '$namespaceAtual' where id_conceito = $idConceitoRef ");
            
            //$record = $sql->gonext() ;
            // }
        }
        
        # Remove o conceito escolhido
        $sql6->execute ("DELETE FROM conceito WHERE id_conceito = $id_conceito");
        $sql6->execute ("DELETE FROM relacao_conceito WHERE id_conceito = $id_conceito");
    }
}
else
{
	//Nothing to do.
}

###################################################################
# Essa funcao recebe um id de relacao e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("removeRelacao"))) 
{
    function removeRelacao($id_projeto, $id_relacao)
    {
    	assert($id_projeto != null, "id_projeto must not be null");
    	assert($id_relacao != null, "id_relacao must not be null");
    	
        $DB = new PGDB();
        $sql6 = new QUERY($DB);
        
        # Remove o conceito escolhido
        $sql6->execute ("DELETE FROM relacao WHERE id_relacao = $id_relacao");
        $sql6->execute ("DELETE FROM relacao_conceito WHERE id_relacao = $id_relacao");
    }
}
else
{
	//Nothing to do.
}

###################################################################
# Funcao faz um select na tabela lexico.
# Para inserir um novo lexico, deve ser verificado se ele ja existe,
# ou se existe um sinonimo com o mesmo nome.
# Recebe o id do projeto e o nome do lexico (1.0)
# Faz um SELECT na tabela lexico procurando por um nome semelhante
# no projeto (1.1)
# Faz um SELECT na tabela sinonimo procurando por um nome semelhante
# no projeto (1.2)
# retorna true caso nao exista ou false caso exista (1.3)
###################################################################
function checkLexiconExists($project, $name)
{
	assert($project != null, "project must not be null");
	assert($name != null, "name must not be null");
	
        $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $query = "SELECT * FROM lexico WHERE id_projeto = $project AND nome = '$name' ";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArrayLexico = mysql_fetch_array($queryResult);
        
        if ($resultArray == false)
        {
            $exists = false;
        }
        else
        {
            // Nothing to do
        }

        $query = "SELECT * FROM sinonimo WHERE id_projeto = $project AND nome = '$name' ";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArraySinonimo = mysql_fetch_array($queryResult);
        
        $resultArray = $resultArrayLexico || $resultArraySinonimo;

        if ($resultArray == true)
        {
            $exists = true;
        }
        else
        {
            $exists = false;
        }

        return $exists;
}


###################################################################
# Recebe o id do projeto e a lista de sinonimos (1.0)
# Funcao faz um select na tabela sinonimo.
# Para verificar se ja existe um sinonimo igual no BD.
# Faz um SELECT na tabela lexico para verificar se ja existe
# um lexico com o mesmo nome do sinonimo.(1.1)
# retorna true caso nao exista ou false caso exista (1.2)
###################################################################
function checkSynonymExists($project, $listSynonym)
{
        assert($project != null, "project must not be null");
        assert($listSynonym != null, "listSynonym must not be null");

        $exists = false;

        $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);

        foreach ($listSynonym as $synonym)
        {    
            $query = "SELECT * FROM sinonimo WHERE id_projeto = $project AND nome = '$synonym' ";
            $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            $resultArray = mysql_fetch_array($queryResult);
            
            if ($resultArray == false)
            {
                $exists = false;
            }
            else
            {
                // Nothing to do
            }

            $query = "SELECT * FROM lexico WHERE id_projeto = $project AND nome = '$synonym' ";
            $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            $resultArray = mysql_fetch_array($queryResult);
            
            if ($resultArray == true)
            {
                $exists = true;
            }
            else
            {
                $exists = false;
            }
        }

        return $exists;
}

###################################################################
# Function is an insert in the table request.
# To insert a new scenario she should get the fields of the new scenario.
# At the end she sends an email to the project manager regarding this
# scenario if the creator is not the manager.
# Files that use this function:
# add_cenario.php
###################################################################
if (!(function_exists("addScenarioInsertRequest"))) 
{
    function addScenarioInsertRequest($id_projeto, $title, $objective, $context, $actors,
                                      $resources, $exception, $episodes, $id_usuario)
    {
            assert($id_projeto != null, "id_projeto must not be null");
            assert($title != null, "title must not be null");
            assert($objective != null, "objective must not be null");
            assert($context != null, "context must not be null");
            
            
            $DB = new PGDB();
            $insert  = new QUERY($DB);
            $selectUser  = new QUERY($DB);
            $selectParticipa = new QUERY($DB);

            $query = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
            $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            $resultArray = mysql_fetch_array($queryResult);


            if ($resultArray == false) // the current user is not a manager
            {
                    $insert->execute("INSERT INTO pedidocen (id_projeto, titulo, objetivo, contexto, atores, recursos, excecao, episodios, id_usuario, tipo_pedido, aprovado) VALUES ($id_projeto, '$title', '$objective', '$context', '$actors', '$resources', '$exception', '$episodes', $id_usuario, 'inserir', 0)");
                    $selectUser->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
                    $selectParticipa->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $id_projeto");
                    $recordUser = $selectUser->gofirst();
                    $nome = $recordUser['nome'];
                    $email = $recordUser['email'];
                    $recordParticipa = $selectParticipa->gofirst();
                    
                    while($recordParticipa != 'LAST_RECORD_REACHED')
                    {
                        $id = $recordParticipa['id_usuario'];
                        $selectUser->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                        $recordUser = $selectUser->gofirst();
                        $mailGerente = $recordUser['email'];
                        mail("$mailGerente", "Pedido de Inclus�o Cen�rio", "O usuario do sistema $nome\nPede para inserir o cenario $title \nObrigado!","From: $nome\result\n"."Reply-To: $email\result\n");
                        $recordParticipa = $selectParticipa->gonext();
                    }
            }
            else // the current user is a manager
            { 
                    adicionar_cenario($id_projeto, $title, $objective, $context, $actors, $resources, $exception, $episodes);
            }
    }
}
else
{
	//Nothing to do.
}

###################################################################
# Function is an insert in the table request.
# To change a scenario she should get the fields of the scenario 
# already modified. (1.1)
# At the end she sends an e-mail project managers concerning this
# scenario if the creator is not the manager. (2.1)
# Files that use this function:
# alt_cenario.php
###################################################################
if (!(function_exists("inserirPedidoAlterarCenario"))) 
{
    function inserirPedidoAlterarCenario($id_projeto, $id_cenario, $title, $objective, $context, $actors,
                                         $resources,$exception, $episodes, $justificativa, $id_usuario)
    {
        assert($id_projeto != null, "id_projeto must not be null");
        assert($id_cenario != null, "id_cenario must not be null");
        assert($title != null, "title must not be null");
        assert($objective != null, "objective must not be null");
        assert($context != null, "context must not be null");
        assert($id_usuario != null, "id_usuario must not be null");
        
        $DB = new PGDB();
        $insert = new QUERY($DB);
        $selectUser = new QUERY($DB);
        $selectParticipa = new QUERY($DB);
        
        $query = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($queryResult);
        
        if ($resultArray == false) // user is not a manager
        {
            $insert->execute("INSERT INTO pedidocen (id_projeto, id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios, id_usuario, tipo_pedido, aprovado, justificativa) VALUES ($id_projeto, $id_cenario, '$title', '$objective', '$context', '$actors', '$resources', '$exception', '$episodes', $id_usuario, 'alterar', 0, '$justificativa')");
            $selectUser->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $selectParticipa->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $id_projeto");
            $recordUser = $selectUser->gofirst();
            $nome = $recordUser['nome'];
            $email = $recordUser['email'];
            $recordParticipa = $selectParticipa->gofirst();
            while($recordParticipa != 'LAST_RECORD_REACHED')
            {
                $id = $recordParticipa['id_usuario'];
                $selectUser->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $recordUser = $selectUser->gofirst();
                $mailGerente = $recordUser['email'];
                mail("$mailGerente", "Pedido de Alteração Cenário", "O usuario do sistema $nome\nPede para alterar o cenario $title \nObrigado!","From: $nome\result\n"."Reply-To: $email\result\n");
                $recordParticipa = $selectParticipa->gonext();
            }
        }
        else // user is a manager
        { 
            alteraCenario($id_projeto, $id_cenario, $title, $objective, $context, $actors, $resources, $exception, $episodes);
        }
    }
}
else
{
	//Nothing to do.
}

###################################################################
# Function is an insert in the table request.
# To remove a scenario she should receive
# the scenario id and project id. (1.1)
# At the end she sends an email to the project manager
# Referring to this lexicon. (2.1)
# Files that use this function:
# rmv_cenario.php
###################################################################
if (!(function_exists("inserirPedidoRemoverCenario")))
{
    function inserirPedidoRemoverCenario($id_projeto, $id_cenario, $id_usuario)
    {
    	assert($id_projeto != null, "id_projeto must not be null");
    	assert($id_cenario != null, "id_cenario must not be null");
    	assert($id_usuario != null, "id_usuario must not be null");
    	
        $DB = new PGDB();
        $insert = new QUERY($DB);
        $selectUser = new QUERY($DB);
        $selectParticipa = new QUERY($DB);
    
        $query = ("SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto");
        $queryResult = mysql_query($query) or die ("Erro ao enviar a query de select no participa<br>".mysql_error()."<br>".__FILE__.__LINE__);
        $resultArray = mysql_fetch_array($queryResult);

        if ($resultArray == false) //Nao e gerente
        {
            $selectUser->execute("SELECT * FROM cenario WHERE id_cenario = $id_cenario");
            $cenario = $selectUser->gofirst();
            $title = $cenario['titulo'];
            $insert->execute("INSERT INTO pedidocen (id_projeto, id_cenario, titulo, id_usuario, tipo_pedido, aprovado) VALUES ($id_projeto, $id_cenario, '$title', $id_usuario, 'remover', 0)");
            $selectUser->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $selectParticipa->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $id_projeto");
            $recordUser = $selectUser->gofirst();
            $nome = $recordUser['nome'];
            $email = $recordUser['email'];
            $recordParticipa = $selectParticipa->gofirst();
            
            while($recordParticipa != 'LAST_RECORD_REACHED') 
            {
                $id = $recordParticipa['id_usuario'];
                $selectUser->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $recordUser = $selectUser->gofirst();
                $mailGerente = $recordUser['email'];
                mail("$mailGerente", "Pedido de Remover Cen�rio", "O usuario do sistema $nome\nPede para remover o cenario $id_cenario \nObrigado!", "From: $nome\result\n" . "Reply-To: $email\result\n");
                $recordParticipa = $selectParticipa->gonext();
            }
        }
        else
        {
            removeCenario($id_projeto,$id_cenario);
        }
    }
}
else
{
	//Nothing to do.
}

###################################################################
# Function is an insert in the table request.
# To insert a new lexicon she should receive the new lexical fields.
# At the end she sends an email to the project manager regarding
# this lexicon if the creator is not the manager.
# Files that use this function:
# add_lexico.php
###################################################################
if (!(function_exists("addLexiconInsertRequest"))) 
{
    function addLexiconInsertRequest($id_project, $name, $notion, $impact, $id_user, $synonyms, $classification)
    {
        assert($id_project != null, "id_project must not be null");
        assert($name != null, "name must not be null");
        assert($notion != null, "notion must not be null");
        assert($id_user != null, "id user must not be null");
        assert($classification != null, "classification must not be null");
        
        $DB = new PGDB() ;
        $insert = new QUERY($DB) ;
        $selectUser = new QUERY($DB) ;
        $selectPaticipa = new QUERY($DB) ;
        
        $query = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_user AND id_projeto = $id_project";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($queryResult);
        
        if ($resultArray == false) // user is not a manager
        {
            $insert->execute("INSERT INTO pedidolex (id_projeto,nome,nocao,impacto,tipo,id_usuario,tipo_pedido,aprovado) VALUES ($id_project,'$name','$notion','$impact','$classification',$id_user,'inserir',0)");
            $newId = $insert->getLastId();
            $selectUser->execute("SELECT * FROM usuario WHERE id_usuario = '$id_user'");
            $selectPaticipa->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_project");
            
            //insert synonyms
            
            foreach($synonyms as $synonym)
            {
                $insert->execute("INSERT INTO sinonimo (id_pedidolex, nome, id_projeto) 
                VALUES ($newId, '".prepara_dado(strtolower($synonym))."', $id_project)");
            }
            // end of the insertions of synonyms
            
            if ($selectUser->getntuples() == 0 && $selectPaticipa->getntuples() == 0)
            {
                echo "<BR> [ERRO]Pedido n&atilde;o foi comunicado por e-mail." ;
            }
            else
            {
                $resultUser = $selectUser->gofirst ();
                $userName = $resultUser['nome'] ;
                $email = $resultUser['email'] ;
                $resultParticipa = $selectPaticipa->gofirst ();
                while($resultParticipa != 'LAST_RECORD_REACHED')
                {
                    $idCurrentUser = $resultParticipa['id_usuario'] ;
                    $selectUser->execute("SELECT * FROM usuario WHERE id_usuario = $idCurrentUser") ;
                    $resultUser = $selectUser->gofirst ();
                    $managerMail = $resultUser['email'] ;
                    mail("$managerMail", "Pedido de Inclus&atilde;o de L&eacute;xico", "O usu&aacute;rio do sistema $userName\nPede para inserir o l&eacute;xico $name \nObrigado!","From: $userName\result\n"."Reply-To: $email\result\n");
                    $resultParticipa = $selectPaticipa->gonext();
                }
            }
        }
        else // user is a manager
        { 
            adicionar_lexico($id_project, $name, $notion, $impact, $synonyms, $classification);
        }
    }
}
else
{
	//Nothing to do.
}
###################################################################
# Function is an insert in the table request.
# To change a lexical she should receive the lexical fields already
# modified. (1.1)
# At the end she sends an email to the project manager regarding
# this lexicon if the creator is not the manager. (2.1)
# Files that use this function:
# alt_lexico.php
###################################################################
if (!(function_exists("inserirPedidoAlterarLexico"))) 
{
    function inserirPedidoAlterarLexico($id_projeto, $id_lexico, $nome, $nocao, $impacto, $justificativa,
                                        $id_usuario, $sinonimos, $classificacao)
    {
        assert($id_projeto != null, "id_projeto must not be null");
        assert($nome != null, "nome must not be null");
        assert($nocao != null, "nocao must not be null");
        assert($id_usuario != null, "id usuario must not be null");
        assert($classificacao != null, "classificacao must not be null");

        $DB = new PGDB();
        $insert = new QUERY($DB);
        $selectUser = new QUERY($DB);
        $selectParticipa = new QUERY($DB);
        
        $query = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($queryResult);
                
        if ($resultArray == false) //user is not a manager
        {
            $insert->execute("INSERT INTO pedidolex (id_projeto,id_lexico,nome,nocao,impacto,id_usuario,tipo_pedido,aprovado,justificativa, tipo) VALUES ($id_projeto,$id_lexico,'$nome','$nocao','$impacto',$id_usuario,'alterar',0,'$justificativa', '$classificacao')");
            $newPedidoId = $insert->getLastId();
            
            //sinonimos
            foreach($sinonimos as $sin)
            {
            	$insert->execute("INSERT INTO sinonimo (id_pedidolex,nome,id_projeto) 
                                  VALUES ($newPedidoId,'".prepara_dado(strtolower($sin))."', $id_projeto)") ;
            }
            
            
            $selectUser->execute("SELECT * FROM usuario WHERE id_usuario = '$id_usuario'") ;
            $selectParticipa->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto") ;
            
            if ($selectUser->getntuples() == 0 && $selectParticipa->getntuples() == 0)
            {
                echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail." ;
            }
            else
            {
                $recordUser = $selectUser->gofirst ();
                $nameUser = $recordUser['nome'] ;
                $emailUser = $recordUser['email'] ;
                $recordParticipa = $selectParticipa->gofirst ();
                
                while($recordParticipa != 'LAST_RECORD_REACHED')
                {
                    $id = $recordParticipa['id_usuario'] ;
                    $selectUser->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                    $recordUser = $selectUser->gofirst ();
                    $mailGerente = $recordUser['email'] ;
                    mail("$mailGerente", "Pedido de Alterar Léxico", "O usuario do sistema $nameUser\nPede para alterar o lexico $nome \nObrigado!","From: $nameUser\result\n"."Reply-To: $emailUser\result\n");
                    $recordParticipa = $selectParticipa->gonext();
                }
            }
        }
        else // user is a manager
        {
            alteraLexico($id_projeto,$id_lexico, $nome, $nocao, $impacto, $sinonimos, $classificacao) ;
        }
    }
}
else
{
	// Nothing to do.
}

##########################################################inserirPedidoRemoverCenario#########
# Function is an insert in the table request.
# To remove a lexicon it must receive the id and id lexicon
# project. (1.1)
# At the end she sends an email to the project manager regarding
# this lexicon. (2.1)
# Files that use this function:
# rmv_lexico.php
###################################################################
if (!(function_exists("inserirPedidoRemoverLexico"))) 
{
    function inserirPedidoRemoverLexico($id_projeto,$id_lexico,$id_usuario)
    {
    	assert($id_projeto != null, "id_projeto must not be null");
    	assert($id_lexico != null, "id_lexico must not be null");
    	assert($id_usuario != null, "id_usuario must not be null");
    	
        $DB = new PGDB () ;
        $insert = new QUERY ($DB) ;
        $select = new QUERY ($DB) ;
        $select2 = new QUERY ($DB) ;
        
        $query = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($queryResult);
        
        if ( $resultArray == false ) //nao e gerente
        {
            $select->execute("SELECT * FROM lexico WHERE id_lexico = $id_lexico") ;
            $lexico = $select->gofirst ();
            $nome = $lexico['nome'] ;

            $insert->execute("INSERT INTO pedidolex (id_projeto,id_lexico,nome,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,$id_lexico,'$nome',$id_usuario,'remover',0)") ;
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario") ;
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto") ;

            if ($select->getntuples() == 0&&$select2->getntuples() == 0)
            {
                echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail." ;
            }
            else
            {
                $record = $select->gofirst ();
                $nome = $record['nome'] ;
                $email = $record['email'] ;
                $record2 = $select2->gofirst ();
                
                while ($record2 != 'LAST_RECORD_REACHED')
                {
                    $id = $record2['id_usuario'] ;
                    $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                    $record = $select->gofirst ();
                    $mailGerente = $record['email'] ;
                    mail("$mailGerente", "Pedido de Remover L�xico", "O usuario do sistema $nome2\nPede para remover o lexico $id_lexico \nObrigado!","From: $nome\result\n"."Reply-To: $email\result\n");
                    $record2 = $select2->gonext();
                }
            }
        }
        else // user is a manager
        {
            removeLexico($id_projeto,$id_lexico);
        }
    }
}
else
{
	//Nothing to do.
}

###################################################################
# Function is an insert in the table request.
# To change a concept she should get the fields of the concept has
# changed. (1.1)
# At the end she sends an e-mail project managers concerning this
# scenario if the creator is not the manager. (2.1)
# Files that use this function:
# alt_cenario.php
###################################################################
if (!(function_exists("inserirPedidoAlterarCenario"))) 
{
    function inserirPedidoAlterarConceito($id_projeto, $id_conceito, $nome, $descricao, $namespace,$justificativa, $id_usuario)
    {
        assert($id_projeto != null, "id_projeto must not be null");
        assert($nome != null, "nome must not be null");
        assert($descricao != null, "descricao must not be null");
        assert($namespace != null, "namespace must not be null");

        $DB = new PGDB();
        $insert = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        
        $query = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($queryResult);
        
        if ($resultArray == false) //nao e gerente
        {
            $insert->execute("INSERT INTO pedidocon (id_projeto, id_conceito, nome, descricao, namespace, id_usuario, tipo_pedido, aprovado, justificativa) VALUES ($id_projeto, $id_conceito, '$nome', '$descricao', '$namespace', $id_usuario, 'alterar', 0, '$justificativa')");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $id_projeto");
            $record = $select->gofirst();
            $nomeUsuario = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            
            while($record2 != 'LAST_RECORD_REACHED')
            {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Alteração Conceito", "O usuario do sistema $nomeUsuario\nPede para alterar o conceito $nome \nObrigado!","From: $nomeUsuario\result\n"."Reply-To: $email\result\n");
                $record2 = $select2->gonext();
            }
        }
        else //user is a manager
        {
            removeConceito($id_projeto,$id_conceito) ;
            adicionar_conceito($id_projeto, $nome, $descricao, $namespace);
        }
    }
}
else
{
	//Nothing to do.
}

###################################################################
# Function is an insert in the table request.
# To remove a concept it must receive the concept id and project
# id. (1.1)
# At the end she sends an email to the project manager regarding
# this concept. (2.1)
# Files that use this function:
# rmv_conceito.php
###################################################################
if (!(function_exists("inserirPedidoRemoverConceito")))
{
    function inserirPedidoRemoverConceito($id_projeto,$id_conceito,$id_usuario)
    {
    	assert($id_projeto != null, "id_projeto must not be null");
    	assert($id_conceito != null, "id_conceito must not be null");
    	assert($id_usuario != null, "id_usuario must not be null");
    	
        $DB = new PGDB();
        $insert = new QUERY($DB);
        $selectUser = new QUERY($DB);
        $selectParticipa = new QUERY($DB);
        $selectUser->execute("SELECT * FROM conceito WHERE id_conceito = $id_conceito") ;
        $conceito = $selectUser->gofirst ();
        $nome = $conceito['nome'] ;
        
        $insert->execute("INSERT INTO pedidocon (id_projeto,id_conceito,nome,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,$id_conceito,'$nome',$id_usuario,'remover',0)") ;
        $selectUser->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario") ;
        $selectParticipa->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto") ;
        
        if ($selectUser->getntuples() == 0&&$selectParticipa->getntuples() == 0)
        {
            echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail." ;
        }
        else
        {
            $recordUser = $selectUser->gofirst ();
            $nome = $recordUser['nome'] ;
            $email = $recordUser['email'] ;
            $recordParticipa = $selectParticipa->gofirst ();
            while($recordParticipa != 'LAST_RECORD_REACHED')
            {
                $id = $recordParticipa['id_usuario'] ;
                $selectUser->execute("SELECT * FROM usuario WHERE id_usuario = $id") ;
                $recordUser = $selectUser->gofirst ();
                $mailGerente = $recordUser['email'] ;
                mail("$mailGerente", "Pedido de Remover Conceito", "O usuario do sistema $nome2\nPede para remover o conceito $id_conceito \nObrigado!","From: $nome\result\n"."Reply-To: $email\result\n");
                $recordParticipa = $selectParticipa->gonext();
            }
        }
    }
}
else
{
	//Nothing to do.
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover uma relacao ela deve receber
# o id da relacao e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este relacao.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_relacao.php
###################################################################
if (!(function_exists("inserirPedidoRemoverRelacao")))
{
    function inserirPedidoRemoverRelacao($id_projeto,$id_relacao,$id_usuario)
    {
    	assert($id_projeto != null, "id_projeto must not be null");
    	assert($id_relacao != null, "id_relacao must not be null");
    	assert($id_usuario != null, "id_usuario must not be null");
    	
        $DB = new PGDB () ;
        $insert = new QUERY ($DB) ;
        $selectUser = new QUERY ($DB) ;
        $selectParticipa = new QUERY ($DB) ;
        $selectUser->execute("SELECT * FROM relacao WHERE id_relacao = $id_relacao") ;
        $relacao = $selectUser->gofirst ();
        $nome = $relacao['nome'] ;
        
        $insert->execute("INSERT INTO pedidorel (id_projeto,id_relacao,nome,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,$id_relacao,'$nome',$id_usuario,'remover',0)") ;
        $selectUser->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario") ;
        $selectParticipa->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto") ;
        
        if ($selectUser->getntuples() == 0 && $selectParticipa->getntuples() == 0)
        {
            echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail." ;
        }
        else
        {
            $recordUser = $selectUser->gofirst ();
            $nome = $recordUser['nome'] ;
            $email = $recordUser['email'] ;
            $recordParticipa = $selectParticipa->gofirst ();
            while($recordParticipa != 'LAST_RECORD_REACHED')
            {
                $id = $recordParticipa['id_usuario'] ;
                $selectUser->execute("SELECT * FROM usuario WHERE id_usuario = $id") ;
                $recordUser = $selectUser->gofirst ();
                $mailGerente = $recordUser['email'] ;
                mail("$mailGerente", "Pedido de Remover Conceito", "O usuario do sistema $nome2\nPede para remover o conceito $id_relacao \nObrigado!","From: $nome\result\n"."Reply-To: $email\result\n");
                $recordParticipa = $selectParticipa->gonext();
            }
        }
    }
}
else
{
	//Nothing to do.
}

#############################
# Formata Data
# Recebe YYY-DD-MM
# Retorna DD-MM-YYYY
#############################################
if (!(function_exists("formataData"))) 
{
    function formataData($data)
    {
    	assert($data != null, "data must not be null");
    	
        $novaData = substr( $data, 8, 9 ) .
        substr( $data, 4, 4 ) .
        substr( $data, 0, 4 );
        return $novaData ;
    }
}
else
{
	//Nothing to do.
}

?>
