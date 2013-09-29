<?php
include_once("bd.inc");
include_once("bd_class.php");
include_once("seguranca.php");


/*
if (!(class_exists("PGDB"))) {
    include("bd_class.php");
}
*/

/* chkUser(): checa se o usu�rio acessando foi autenticado (presen�a da vari�vel de sess�o
$id_usuario_corrente). Caso ele j� tenha sido autenticado, continua-se com a execu��o do
script. Caso contr�rio, abre-se uma janela de logon. */
if (!(function_exists("chkUser"))) 
{
    function chkUser($url)
    {
        if(!(isset($_SESSION['id_usuario_corrente'])))    
        {
            ?>
            <script language="javascript1.3">
                    open('login.php?url=<?=$url?>', 'login', 'dependent,height=430,width=490,resizable,scrollbars,titlebar');
            </script>
            <?php
            exit();
        }
    }
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
    function inclui_cenario($id_projeto, $title, $objective, $context, $actors, $resources, $exception, $episodes)
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
###################################################################
# Insere um lexico no banco de dados.
# Recebe o id_projeto, nome, no��o, impacto e os sinonimos. (1.1)
# Insere os valores do lexico na tabela LEXICO. (1.2)
# Insere todos os sinonimos na tabela SINONIMO. (1.3)
# Devolve o id_lexico. (1.4)
#
###################################################################
if (!(function_exists("inclui_lexico"))) 
{
    function inclui_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao)
    {
        $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $data = date("Y-m-d");
     
                
        $query = "INSERT INTO lexico (id_projeto, data, nome, nocao, impacto, tipo)
              VALUES ($id_projeto, '$data', '" .prepara_dado(strtolower($nome)). "',
			  '".prepara_dado($nocao)."', '".prepara_dado($impacto)."', '$classificacao')";
				
		mysql_query($query) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        //sinonimo
        $newLexId = mysql_insert_id($result);
        
        
        if( ! is_array($sinonimos) )
        $sinonimos = array();
        
        foreach($sinonimos as $novoSin)
        {
       		$query = "INSERT INTO sinonimo (id_lexico, nome, id_projeto)
                VALUES ($newLexId, '" . prepara_dado(strtolower($novoSin)) . "', $id_projeto)";
            mysql_query($query, $result) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        }
        
        $query = "SELECT max(id_lexico) FROM lexico";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $result = mysql_fetch_row($queryResult);
        return $result[0];
    }
}
###################################################################
# Insere um projeto no banco de dados.
# Recebe o nome e descricao. (1.1)
# Verifica se este usuario ja possui um projeto com esse nome. (1.2)
# Caso nao possua, insere os valores na tabela PROJETO. (1.3)
# Devolve o id_cprojeto. (1.4)
#
###################################################################
if (!(function_exists("inclui_projeto"))) 
{
    function inclui_projeto($nome, $descricao)
    {
        $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        //verifica se usuario ja existe
        $queryVerification = "SELECT * FROM projeto WHERE nome = '$nome'";
        $queryVerificationResult = mysql_query($queryVerification) or die("Erro ao enviar a query de select<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        //$result = mysql_fetch_row($queryVerificationResult);
        $resultArray = mysql_fetch_array($queryVerificationResult);
        
        
        if ( $resultArray != false )
        {
            //verifica se o nome existente corresponde a um projeto que este usuario participa
            $id_projeto_repetido = $resultArray['id_projeto'];
            
            $id_usuario_corrente = $_SESSION['id_usuario_corrente'];
            
            $qvu = "SELECT * FROM participa WHERE id_projeto = '$id_projeto_repetido' AND id_usuario = '$id_usuario_corrente' ";
            
            $qvuv = mysql_query($qvu) or die("Erro ao enviar a query de SELECT no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            
            $resultArray = mysql_fetch_row($qvuv);
            
            if ($resultArray[0] != null )
            {
                return -1;
            }
            
        }
        
        $query = "SELECT MAX(id_projeto) FROM projeto";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de MAX ID<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $result = mysql_fetch_row($queryResult);
        
        if ( $result[0] == false )
        {
            $result[0] = 1;
        }
        else
        {
            $result[0]++;
        }
        $data = date("Y-m-d");
        
        $queryResult = "INSERT INTO projeto (id_projeto, nome, data_criacao, descricao)
                  VALUES ($result[0],'".prepara_dado($nome)."','$data' , '".prepara_dado($descricao)."')";
        
        mysql_query($queryResult) or die("Erro ao enviar a query INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        return $result[0];
    }
}


if (!(function_exists("recarrega"))) 
{
    function recarrega($url) 
    {
		?>
<script language="javascript1.3">
		
		location.replace('<?=$url?>');
		
		</script>
<?php
    }
}

if (!(function_exists("breakpoint"))) 
{
    function breakpoint($number) 
    {
		?>
<script language="javascript1.3">
		
		alert('<?=$number?>');
		
		</script>
<?php
    }
}

if (!(function_exists("simple_query")))
{
    funcTion simple_query($field, $table, $where)
    {
        $result = bd_connect() or die("Erro ao conectar ao SGBD");
        $query = "SELECT $field FROM $table WHERE $where";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query");
        $result = mysql_fetch_row($queryResult);
        return $result[0];        
    }
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
        // Conecta ao SGBD
        $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        // Inclui o cenario na base de dados (sem transformar os campos, sem criar os relacionamentos)
        $id_incluido = inclui_cenario($id_projeto, $title, $objective, $context, $actors, $resources, $exception, $episodes);
        
        $query = "SELECT id_cenario, titulo, contexto, episodios
              FROM cenario
              WHERE id_projeto = $id_projeto
              AND id_cenario != $id_incluido
              ORDER BY CHAR_LENGTH(titulo) DESC";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de SELECT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        ### PREENCHIMENTO DAS TABELAS LEXTOLEX E CENTOCEN PARA MONTAGEM DO MENU LATERAL
        
        // Verifica ocorr�ncias do titulo do cenario incluido no contexto 
        // e nos episodios de todos os outros cenarios e adiciona os relacionamentos,
        // caso possua, na tabela centocen
        
        while ($result = mysql_fetch_array($queryResult)) 
        {    // Para todos os cenarios
        
        	$tituloEscapado = escapa_metacaracteres( $title );
			$regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i"; 
	                
	        if((preg_match($regex, $result['contexto']) != 0) ||
	           (preg_match($regex, $result['episodios']) != 0) ) 
	        {   // (2.2)
	         
		        $query = "INSERT INTO centocen (id_cenario_from, id_cenario_to)
		                      VALUES (" . $result['id_cenario'] . ", $id_incluido)"; // (2.2.1)
		        mysql_query($query) or die("Erro ao enviar a query de INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  
	        }

			$tituloEscapado = escapa_metacaracteres( $result['titulo'] );
        	$regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i";        
      
        	if((preg_match($regex, $context) != 0) ||
         		(preg_match($regex, $episodes) != 0) ) 
         	{   // (2.3)        
        
        		$query = "INSERT INTO centocen (id_cenario_from, id_cenario_to) VALUES ($id_incluido, " . $result['id_cenario'] . ")"; //(2.4.1)
        
        		mysql_query($query) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
        	}   // if
      
        }   // while
        
        // Verifica a ocorrencia do nome de todos os lexicos nos campos titulo, objetivo,
        // contexto, atores, recursos, episodios e excecao do cenario incluido 
      
        $query = "SELECT id_lexico, nome FROM lexico WHERE id_projeto = $id_projeto";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de SELECT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        while ($result2 = mysql_fetch_array($queryResult)) 
        {    // (3)
        
        $nomeEscapado = escapa_metacaracteres( $result2['nome']);
		$regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";
         
	        if((preg_match($regex, $title) != 0) ||
	            (preg_match($regex, $objective) != 0) ||
	            (preg_match($regex, $context) != 0) ||
	            (preg_match($regex, $actors) != 0) ||
	            (preg_match($regex, $resources) != 0) ||
	            (preg_match($regex, $episodes) != 0) ||
	            (preg_match($regex, $exception) != 0) ) 
	        {   // (3.2)
	                
		        $queryCenario = "SELECT * FROM centolex WHERE id_cenario = $id_incluido AND id_lexico = " . $result2['id_lexico'];
		        $queryResultCenario = mysql_query($queryCenario) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
		        $resultArrayCen = mysql_fetch_array($queryResultCenario);
	        
		        if ($resultArrayCen == false)
		        {
		            $query = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_incluido, " . $result2['id_lexico'] . ")";
		            mysql_query($query) or die("Erro ao enviar a query de INSERT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
		        }
	        }   // if
      
        }   // while
        
        // Verifica a ocorrencia dos sinonimos de todos os lexicos nos campos titulo, objetivo,
        // contexto, atores, recursos, episodios e excecao do cenario incluido
      	//Sinonimos
                
        $querySinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = $id_projeto AND id_pedidolex = 0";
        
        $queryResultSinonimos = mysql_query($querySinonimos) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        $nomesSinonimos = array();
        
        $id_lexicoSinonimo = array();
        
        while($rowSinonimo = mysql_fetch_array($queryResultSinonimos))
        {
            
            $nomesSinonimos[]     = $rowSinonimo["nome"];
            $id_lexicoSinonimo[]  = $rowSinonimo["id_lexico"];
            
        }
      
        $qlc = "SELECT id_cenario, titulo, contexto, episodios, objetivo, atores, recursos, excecao
              FROM cenario
              WHERE id_projeto = $id_projeto
              AND id_cenario = $id_incluido";
        $count = count($nomesSinonimos);
        for ($i = 0; $i < $count; $i++)
        {
            
            $queryResult = mysql_query($qlc) or die("Erro ao enviar a query de busca<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            // verifica sinonimos dos outros lexicos no cenario inclu�do
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
		            $resultArrayCen = mysql_fetch_array($queryResultCenario);
		            
		            if ($resultArrayCen == false)
		            {
		                $query = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_incluido, $id_lexicoSinonimo[$i])";
		                mysql_query($query) or die("Erro ao enviar a query de insert no centolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
		            }
	            
	            }   // if
            }   // while
            
        } //for
        
    }
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
        $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        $id_incluido = inclui_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao); // (1)
        
        $queryResult = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios
              FROM cenario
              WHERE id_projeto = $id_projeto";
        
        $executeQuery = mysql_query($queryResult) or die("Erro ao enviar a query de SELECT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        while ($result = mysql_fetch_array($executeQuery)) 
        {    // 2  - Para todos os cenarios
        
           $nomeEscapado = escapa_metacaracteres( $nome );
		   $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";
         
            if( (preg_match($regex, $result['objetivo']) != 0) ||
                (preg_match($regex, $result['contexto']) != 0) ||
                (preg_match($regex, $result['atores']) != 0)   ||
                (preg_match($regex, $result['recursos']) != 0) ||
                (preg_match($regex, $result['excecao']) != 0)  ||
                (preg_match($regex, $result['episodios']) != 0) )
            { //2.2
        
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
                
                if( (preg_match($regex, $result2['objetivo']) != 0) ||
                    (preg_match($regex, $result2['contexto']) != 0) ||
                    (preg_match($regex, $result2['atores']) != 0)   ||
                    (preg_match($regex, $result2['recursos']) != 0) ||
                    (preg_match($regex, $result2['excecao']) != 0)  ||
                    (preg_match($regex, $result2['episodios']) != 0) )
                { 
                            
                    $qLex = "SELECT * FROM centolex WHERE id_cenario = " . $result2['id_cenario'] . " AND id_lexico = $id_incluido ";
                    $qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArraylex = mysql_fetch_array($qrLex);
                
                    if ( $resultArraylex == false )
                    {
                    
                        $query = "INSERT INTO centolex (id_cenario, id_lexico)
                             VALUES (" . $result2['id_cenario'] . ", $id_incluido)";                   
                    
                        mysql_query($query) or die("Erro ao enviar a query de INSERT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    } //if
                }//if                
            }//while            
        } //for
        
        
        $qlo = "SELECT id_lexico, nome, nocao, impacto, tipo
               FROM lexico
               WHERE id_projeto = $id_projeto
               AND id_lexico != $id_incluido";
                     
        //pega todos os outros lexicos
        $queryResult = mysql_query($qlo) or die("Erro ao enviar a query de SELECT no LEXICO<br>" . mysql_error() .
        								"<br>" . __FILE__ . __LINE__);
        
        while ($result = mysql_fetch_array($queryResult)) 
        {    // (3)
        
            $nomeEscapado = escapa_metacaracteres( $nome );
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";
            
            if ( (preg_match($regex, $result['nocao']) != 0 ) || (preg_match($regex, $result['impacto'])!= 0) )
            {
                
                $qLex = "SELECT * FROM lextolex WHERE id_lexico_from = " . $result['id_lexico'] . " AND id_lexico_to = $id_incluido";
                $qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                $resultArraylex = mysql_fetch_array($qrLex);
      
                if ( $resultArraylex == false )
                {
                    $query = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                          VALUES (" . $result['id_lexico'] . ", $id_incluido)";
                    
                    mysql_query($query) or die("Erro ao enviar a query de INSERT no lextolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                }
            }
         
			$nomeEscapado = escapa_metacaracteres( $result['nome'] );
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";
         
            if((preg_match($regex, $nocao) != 0) || (preg_match($regex, $impacto) != 0) )
            {   // (3.3)        
        
                $query = "INSERT INTO lextolex (id_lexico_from, id_lexico_to) VALUES ($id_incluido, " . $result['id_lexico'] . ")"; 
        
                mysql_query($query) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
            }
       
        }   // while
        
        
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
            while ($resultl = mysql_fetch_array($queryResult)) {
                               
				$nomeSinonimoEscapado = escapa_metacaracteres( $sinonimos[$i] );
			   $regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";
                
                if ((preg_match($regex, $resultl['nocao']) != 0)  || (preg_match($regex, $resultl['impacto']) != 0))
                {
                                    
                    $qLex = "SELECT * FROM lextolex WHERE id_lexico_from = " . $resultl['id_lexico'] . " AND id_lexico_to = $id_incluido";
                    $qrLex = mysql_query($qLex) or die("Erro ao enviar a query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    $resultArraylex = mysql_fetch_array($qrLex);
                    
                    if ( $resultArraylex == false )
                    {                        
                        $query = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                        VALUES (" . $resultl['id_lexico'] . ", $id_incluido)";            
                        mysql_query($query) or die("Erro ao enviar a query de insert no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
                    }//if
                }    //if
            }//while
        }//for
        
        //sinonimos ja existentes
        
        $querySinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = $id_projeto AND id_lexico != $id_incluido AND id_pedidolex = 0";
        
        $queryResultSinonimos = mysql_query($querySinonimos) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        $nomesSinonimos = array();
        
        $id_lexicoSinonimo = array();
        
        while($rowSinonimo = mysql_fetch_array($queryResultSinonimos))
        {
            $nomesSinonimos[]     = $rowSinonimo["nome"];
            $id_lexicoSinonimo[]  = $rowSinonimo["id_lexico"];
            
        }
      
    }
}


###################################################################
# Essa funcao recebe um id de cenario e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("removeCenario")))
{
    function removeCenario($id_projeto,$id_cenario)
    {
        $DB = new PGDB () ;
        $sql1 = new QUERY ($DB) ;
        $sql2 = new QUERY ($DB) ;
        $sql3 = new QUERY ($DB) ;
        $sql4 = new QUERY ($DB) ;
               
        # Remove o relacionamento entre o cenario a ser removido
        # e outros cenarios que o referenciam
        $sql1->execute ("DELETE FROM centocen WHERE id_cenario_from = $id_cenario") ;
        $sql2->execute ("DELETE FROM centocen WHERE id_cenario_to = $id_cenario") ;
        # Remove o relacionamento entre o cenario a ser removido
        # e o seu lexico
        $sql3->execute ("DELETE FROM centolex WHERE id_cenario = $id_cenario") ;
        # Remove o cenario escolhido
        $sql4->execute ("DELETE FROM cenario WHERE id_cenario = $id_cenario") ;
        
    }
}


###################################################################
# Essa funcao recebe um id de cenario e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("alteraCenario"))) 
{
    function alteraCenario($id_projeto, $id_cenario, $title, $objective, $context, $actors, $resources, $exception, $episodes)
    {
        $DB = new PGDB () ;
        $sql1 = new QUERY ($DB) ;
        $sql2 = new QUERY ($DB) ;
        $sql3 = new QUERY ($DB) ;
        $sql4 = new QUERY ($DB) ;
               
        # Remove o relacionamento entre o cenario a ser alterado
        # e outros cenarios que o referenciam
        $sql1->execute ("DELETE FROM centocen WHERE id_cenario_from = $id_cenario") ;
        $sql2->execute ("DELETE FROM centocen WHERE id_cenario_to = $id_cenario") ;
        # Remove o relacionamento entre o cenario a ser alterado
        # e o seu lexico
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
        
        while ($result = mysql_fetch_array($queryResult)) 
        {    // Para todos os cenarios
			$tituloEscapado = escapa_metacaracteres( $title );
	       	$regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i"; 
	                
	       	if((preg_match($regex, $result['contexto']) != 0) || (preg_match($regex, $result['episodios']) != 0) ) 
           	{   // (2.2)
	         
	        	$query = "INSERT INTO centocen (id_cenario_from, id_cenario_to)
	                      VALUES (" . $result['id_cenario'] . ", $id_cenario)"; // (2.2.1)
	        	mysql_query($query) or die("Erro ao enviar a query de INSERT<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  
	        }
			$tituloEscapado = escapa_metacaracteres( $result['titulo'] );
        	$regex = "/(\s|\b)(" . $tituloEscapado . ")(\s|\b)/i";        
      
	        if((preg_match($regex, $context) != 0) || (preg_match($regex, $episodes) != 0)) 
         	{   // (2.3)        
        		$query = "INSERT INTO centocen (id_cenario_from, id_cenario_to) VALUES ($id_cenario, " . $result['id_cenario'] . ")"; //(2.4.1)
        		mysql_query($query) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
        	}   // if
      
        }   // while
        
      
        $query = "SELECT id_lexico, nome FROM lexico WHERE id_projeto = $id_projeto";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de SELECT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        while ($result2 = mysql_fetch_array($queryResult)) 
        {    // (3)

			$nomeEscapado = escapa_metacaracteres( $result2['nome'] );
        	$regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";
         
         	if((preg_match($regex, $title) != 0) ||
            	(preg_match($regex, $objective) != 0) ||
            	(preg_match($regex, $context) != 0) ||
            	(preg_match($regex, $actors) != 0) ||
            	(preg_match($regex, $resources) != 0) ||
            	(preg_match($regex, $episodes) != 0) ||
            	(preg_match($regex, $exception) != 0) ) 
        	{   // (3.2)
                
        	$queryCenario = "SELECT * FROM centolex WHERE id_cenario = $id_cenario AND id_lexico = " . $result2['id_lexico'];
        	$queryResultCenario = mysql_query($queryCenario) or die("Erro ao enviar a query de select no centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        	$resultArrayCen = mysql_fetch_array($queryResultCenario);
        
	        	if ($resultArrayCen == false)
	        	{
	            	$query = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_cenario, " . $result2['id_lexico'] . ")";
	            	mysql_query($query) or die("Erro ao enviar a query de INSERT 3<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
	        	}
        	}   // if
      
        }   // while
        
        
      	//Sinonimos
                
        $querySinonimos = "SELECT nome, id_lexico FROM sinonimo WHERE id_projeto = $id_projeto AND id_pedidolex = 0";
        
        $queryResultSinonimos = mysql_query($querySinonimos) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        $nomesSinonimos = array();
        
        $id_lexicoSinonimo = array();
        
        while($rowSinonimo = mysql_fetch_array($queryResultSinonimos))
        {
            
            $nomesSinonimos[]     = $rowSinonimo["nome"];
            $id_lexicoSinonimo[]  = $rowSinonimo["id_lexico"];
            
        }
      
        $qlc = "SELECT id_cenario, titulo, contexto, episodios, objetivo, atores, recursos, excecao
              FROM cenario
              WHERE id_projeto = $id_projeto
              AND id_cenario = $id_cenario";
        $count = count($nomesSinonimos);
        
        for ($i = 0; $i < $count; $i++)
        {
            
            $queryResult = mysql_query($qlc) or die("Erro ao enviar a query de busca<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($result = mysql_fetch_array($queryResult)) 
            {    // verifica sinonimos dos lexicos no cenario inclu�do
            
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
		            $resultArrayCen = mysql_fetch_array($queryResultCenario);
		            
		            if ($resultArrayCen == false)
		            {
		                $query = "INSERT INTO centolex (id_cenario, id_lexico) VALUES ($id_cenario, $id_lexicoSinonimo[$i])";
		                mysql_query($query) or die("Erro ao enviar a query de insert no centolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);  // (3.3.1)
		            }
		            
		        }   // if
    		}   // while
        } //for
    }    
}


###################################################################
# Essa funcao recebe um id de lexico e remove todos os seus
# links e relacionamentos existentes em todas as tabelas do banco.
###################################################################
if (!(function_exists("removeLexico")))
{
    function removeLexico($id_projeto,$id_lexico)
    {
        $DB = new PGDB();
        $delete = new QUERY($DB);        
        
        # Remove o relacionamento entre o lexico a ser removido
        # e outros lexicos que o referenciam
        $delete->execute ("DELETE FROM lextolex WHERE id_lexico_from = $id_lexico") ;
        $delete->execute ("DELETE FROM lextolex WHERE id_lexico_to = $id_lexico") ;
        $delete->execute ("DELETE FROM centolex WHERE id_lexico = $id_lexico") ;
        
        # Remove o lexico escolhido
        $delete->execute ("DELETE FROM sinonimo WHERE id_lexico = $id_lexico") ;
        $delete->execute ("DELETE FROM lexico WHERE id_lexico = $id_lexico") ;
    }
}



###################################################################
# Essa funcao recebe um id de lexico e remove todos os seus
# links e relacionamentos existentes em todas as tabelas do banco.
###################################################################

if (!(function_exists("alteraLexico")))
{
    function alteraLexico($id_projeto, $id_lexico, $nome, $nocao, $impacto, $sinonimos, $classificacao)
    {
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
 	    
 	    ########
 	    
 	    # Verifica se h� alguma ocorrencia do titulo do lexico nos cenarios existentes no banco
       
        $queryResult = "SELECT id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios
              FROM cenario
              WHERE id_projeto = $id_projeto";
        
        $queryResult = mysql_query($queryResult) or die("Erro ao enviar a query de SELECT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        while ($result = mysql_fetch_array($queryResult)) 
        {    // 2  - Para todos os cenarios
            $nomeEscapado = escapa_metacaracteres( $nome );
			$regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";
         
            if( (preg_match($regex, $result['objetivo']) != 0) ||
                (preg_match($regex, $result['contexto']) != 0) ||
                (preg_match($regex, $result['atores']) != 0)   ||
                (preg_match($regex, $result['recursos']) != 0) ||
                (preg_match($regex, $result['excecao']) != 0)  ||
                (preg_match($regex, $result['episodios']) != 0) )
            { //2.2
        
                $query = "INSERT INTO centolex (id_cenario, id_lexico)
                     VALUES (" . $result['id_cenario'] . ", $id_lexico)"; //2.2.1
        
                mysql_query($query) or die("Erro ao enviar a query de INSERT 1<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
          
            }//if
        }//while

		# Fim da verificacao
        
        ########
 	    
 	    # Verifica se h� alguma ocorrencia de algum dos sinonimos do lexico nos cenarios existentes no banco
       
        //&sininonimos = sinonimos do novo lexico
        $count = count($sinonimos);
        for ($i = 0; $i < $count; $i++)//Para cada sinonimo
        {
            $queryResult = mysql_query($queryResult) or die("Erro ao enviar a query de SELECT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
            while ($result2 = mysql_fetch_array($queryResult))// para cada cenario
            {
                
                $nomeSinonimoEscapado = escapa_metacaracteres ( $sinonimos[$i] );
				$regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";
                
                if( (preg_match($regex, $result2['objetivo']) != 0) ||
                    (preg_match($regex, $result2['contexto']) != 0) ||
                    (preg_match($regex, $result2['atores']) != 0)   ||
                    (preg_match($regex, $result2['recursos']) != 0) ||
                    (preg_match($regex, $result2['excecao']) != 0)  ||
                    (preg_match($regex, $result2['episodios']) != 0) )
                { 
                   // $query = "INSERT INTO centolex (id_cenario, id_lexico)
                   //      VALUES (" . $result2['id_cenario'] . ", $id_lexico)";                   
                
                  //  mysql_query($query) or die("Erro ao enviar a query de INSERT 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    
                }//if                
            }//while            
        } //for
        
        # Fim da verificacao
        
        ########
        
        ### VERIFICACAO DE OCORRENCIA EM LEXICOS
        
        ########
 	    
 	    # Verifica a ocorrencia do titulo do lexico alterado no texto dos outros lexicos
        # Verifica a ocorrencia do titulo dos outros lexicos no lexico alterado
        
        //select para pegar todos os outros lexicos
        $qlo = "SELECT id_lexico, nome, nocao, impacto, tipo
               FROM lexico
               WHERE id_projeto = $id_projeto
               AND id_lexico <> $id_lexico";
                     
        $queryResult = mysql_query($qlo) or die("Erro ao enviar a query de SELECT no LEXICO<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        while ($result = mysql_fetch_array($queryResult)) // para cada lexico exceto o que esta sendo alterado
        {    // (3)
        	# Verifica a ocorrencia do titulo do lexico alterado no texto dos outros lexicos
        	        
            $nomeEscapado = escapa_metacaracteres( $nome );
			$regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";
            
            if ( (preg_match($regex, $result['nocao']) != 0 ) ||
                 (preg_match($regex, $result['impacto'])!= 0) )
            {
                $query = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
                      	VALUES (" . $result['id_lexico'] . ", $id_lexico)";
                
                mysql_query($query) or die("Erro ao enviar a query de INSERT no lextolex 2<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
	        }
         
            # Verifica a ocorrencia do titulo dos outros lexicos no texto do lexico alterado
            
			$nomeEscapado = escapa_metacaracteres( $result['nome'] );
            $regex = "/(\s|\b)(" . $nomeEscapado . ")(\s|\b)/i";
         
            if((preg_match($regex, $nocao) != 0) ||
               (preg_match($regex, $impacto) != 0) )
            {   // (3.3)        
        
                $query = "INSERT INTO lextolex (id_lexico_from, id_lexico_to) 
                		VALUES ($id_lexico, " . $result['id_lexico'] . ")"; 
        
                mysql_query($query) or die("Erro ao enviar a query de insert no centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__); 
            }
       
        }// while
        
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
            while ($resultl = mysql_fetch_array($queryResult)) 
            {// para cada lexico exceto o alterado
                $nomeSinonimoEscapado = escapa_metacaracteres( $sinonimos[$i] );
				$regex = "/(\s|\b)(" . $nomeSinonimoEscapado . ")(\s|\b)/i";
                
                // verifica sinonimo[i] do lexico alterado no texto de cada lexico
             
                if ( (preg_match($regex, $resultl['nocao']) != 0)  ||
                     (preg_match($regex, $resultl['impacto']) != 0))
                {
					
					 // Verifica  se a relacao encontrada ja se encontra no banco de dados. Se tiver nao faz nada, senao cadastra uma nopva relacao
					$queryVerification = "SELECT * FROM lextolex where id_lexico_from=" . $resultl['id_lexico'] . " and id_lexico_to=$id_lexico";
					echo("Query: ".$queryVerification."<br>");
					$resultado = mysql_query($queryVerification) or die("Erro ao enviar query de select no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);	
					if(!resultado)
					{
						$query = "INSERT INTO lextolex (id_lexico_from, id_lexico_to)
	                     VALUES (" . $resultl['id_lexico'] . ", $id_lexico)";            
						mysql_query($query) or die("Erro ao enviar a query de insert(sinonimo2) no lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);	
				  }
                    
                }//if
            }//while
        }//for
    	
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
		   }
        }    
    
        # Cadastra os sinonimos novamente
        
        if (!is_array($sinonimos) )
        	$sinonimos = array();
        
        foreach ($sinonimos as $novoSin)
        {
         	$query = "INSERT INTO sinonimo (id_lexico, nome, id_projeto)
                 VALUES ($id_lexico, '" . prepara_dado(strtolower($novoSin)) . "', $id_projeto)";
            
            mysql_query($query, $result) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        }
     
		# Fim - cadastro de sinonimos        
        
                
    }
}



###################################################################
# Essa funcao recebe um id de conceito e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("removeConceito"))) 
{
    function removeConceito($id_projeto, $id_conceito)
    {
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
###################################################################
# Essa funcao recebe um id de relacao e remove todos os seus
# links e relacionamentos existentes.
###################################################################
if (!(function_exists("removeRelacao"))) 
{
    function removeRelacao($id_projeto, $id_relacao)
    {
        $DB = new PGDB();
        $sql6 = new QUERY($DB);
        
        # Remove o conceito escolhido
        $sql6->execute ("DELETE FROM relacao WHERE id_relacao = $id_relacao");
        $sql6->execute ("DELETE FROM relacao_conceito WHERE id_relacao = $id_relacao");
    }
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
function checarLexicoExistente($projeto, $nome)
{
    $naoexiste = false;
    
    $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $query = "SELECT * FROM lexico WHERE id_projeto = $projeto AND nome = '$nome' ";
    $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($queryResult);
    
    $query = "SELECT * FROM sinonimo WHERE id_projeto = $projeto AND nome = '$nome' ";
    $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($queryResult);
    
    if ($resultArray == false)
    {
        $naoexiste = true;
    }
    else
    {
        $naoexiste = false;
    }
    
    return $naoexiste;
}


###################################################################
# Recebe o id do projeto e a lista de sinonimos (1.0)
# Funcao faz um select na tabela sinonimo.
# Para verificar se ja existe um sinonimo igual no BD.
# Faz um SELECT na tabela lexico para verificar se ja existe
# um lexico com o mesmo nome do sinonimo.(1.1)
# retorna true caso nao exista ou false caso exista (1.2)
###################################################################
function checarSinonimo($projeto, $listSinonimo)
{
    $naoexiste = true;
    
    $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    
    foreach ($listSinonimo as $sinonimo){
        
        $query = "SELECT * FROM sinonimo WHERE id_projeto = $projeto AND nome = '$sinonimo' ";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($queryResult);
        if ( $resultArray != false )
        {
            $naoexiste = false;
        }
        
        $query = "SELECT * FROM lexico WHERE id_projeto = $projeto AND nome = '$sinonimo' ";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($queryResult);
        if ( $resultArray == true )
        {
            $naoexiste = false;
        }
    }
    
    return $naoexiste;
    
    
}



###################################################################
# Funcao faz um select na tabela cenario.
# Para inserir um novo cenario, deve ser verificado se ele ja existe.
# Recebe o id do projeto e o titulo do cenario (1.0)
# Faz um SELECT na tabela cenario procurando por um nome semelhante
# no projeto (1.2)
# retorna true caso nao exista ou false caso exista (1.3)
###################################################################
function checkScenarioExists($project, $title)
{
    $scenarioExists = true;
    
    $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $query = "SELECT * FROM cenario WHERE id_projeto = $project AND titulo = '$title' ";
    $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($queryResult);
    if ( $resultArray == false )
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
# Funcao faz um insert na tabela de pedido.
# Para inserir um novo cenario ela deve receber os campos do novo
# cenario.
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este cenario caso o criador n�o seja o gerente.
# Arquivos que utilizam essa funcao:
# add_cenario.php
###################################################################
if (!(function_exists("inserirPedidoAdicionarCenario"))) {
    function addInsertRequestScenario($id_projeto, $title, $objective, $context, $actors, $resources, $exception, $episodes, $id_usuario)
    {
        $DB = new PGDB();
        $insert  = new QUERY($DB);
        $select  = new QUERY($DB);
        $select2 = new QUERY($DB);
        
        $query = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($queryResult);
        
        
        if ( $resultArray == false ) // the current user is not a manager
        {
            $insert->execute("INSERT INTO pedidocen (id_projeto, titulo, objetivo, contexto, atores, recursos, excecao, episodios, id_usuario, tipo_pedido, aprovado) VALUES ($id_projeto, '$title', '$objective', '$context', '$actors', '$resources', '$exception', '$episodes', $id_usuario, 'inserir', 0)");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $id_projeto");
            $record = $select->gofirst();
            $nome = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while($record2 != 'LAST_RECORD_REACHED')
            {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Inclus�o Cen�rio", "O usuario do sistema $nome\nPede para inserir o cenario $title \nObrigado!","From: $nome\result\n"."Reply-To: $email\result\n");
                $record2 = $select2->gonext();
            }
        }
        else // the current user is a manager
        { 
        	adicionar_cenario($id_projeto, $title, $objective, $context, $actors, $resources, $exception, $episodes);
        }
    }
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para alterar um cenario ela deve receber os campos do cenario
# jah modificados.(1.1)
# Ao final ela manda um e-mail para o gerentes do projeto
# referente a este cenario caso o criador n�o seja o gerente.(2.1)
# Arquivos que utilizam essa funcao:
# alt_cenario.php
###################################################################
if (!(function_exists("inserirPedidoAlterarCenario"))) {
    function inserirPedidoAlterarCenario($id_projeto, $id_cenario, $title, $objective, $context, $actors, $resources,$exception, $episodes, $justificativa, $id_usuario)
    {
        $DB = new PGDB();
        $insert = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        
        $query = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($queryResult);
        
        
        if ( $resultArray == false ) //nao e gerente
        {
            $insert->execute("INSERT INTO pedidocen (id_projeto, id_cenario, titulo, objetivo, contexto, atores, recursos, excecao, episodios, id_usuario, tipo_pedido, aprovado, justificativa) VALUES ($id_projeto, $id_cenario, '$title', '$objective', '$context', '$actors', '$resources', '$exception', '$episodes', $id_usuario, 'alterar', 0, '$justificativa')");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $id_projeto");
            $record = $select->gofirst();
            $nome = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while($record2 != 'LAST_RECORD_REACHED')
            {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Altera��o Cen�rio", "O usuario do sistema $nome\nPede para alterar o cenario $title \nObrigado!","From: $nome\result\n"."Reply-To: $email\result\n");
                $record2 = $select2->gonext();
            }
        }
        else //Eh gerente
        { 
        	alteraCenario($id_projeto, $id_cenario, $title, $objective, $context, $actors, $resources, $exception, $episodes);
        }
    }
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover um cenario ela deve receber
# o id do cenario e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_cenario.php
###################################################################
if (!(function_exists("inserirPedidoRemoverCenario")))
{
    function inserirPedidoRemoverCenario($id_projeto, $id_cenario, $id_usuario)
    {
        $DB = new PGDB();
        $insert = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
    
        $query = ("SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto");
        $queryResult = mysql_query($query) or die ("Erro ao enviar a query de select no participa<br>".mysql_error()."<br>".__FILE__.__LINE__);
        $resultArray = mysql_fetch_array($queryResult);

        if ($resultArray == false) //Nao e gerente
        {
            $select->execute("SELECT * FROM cenario WHERE id_cenario = $id_cenario");
            $cenario = $select->gofirst();
            $title = $cenario['titulo'];
            $insert->execute("INSERT INTO pedidocen (id_projeto, id_cenario, titulo, id_usuario, tipo_pedido, aprovado) VALUES ($id_projeto, $id_cenario, '$title', $id_usuario, 'remover', 0)");
            $select->execute("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 AND id_projeto = $id_projeto");
            $record = $select->gofirst();
            $nome = $record['nome'];
            $email = $record['email'];
            $record2 = $select2->gofirst();
            while($record2 != 'LAST_RECORD_REACHED') 
            {
                $id = $record2['id_usuario'];
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                $record = $select->gofirst();
                $mailGerente = $record['email'];
                mail("$mailGerente", "Pedido de Remover Cen�rio", "O usuario do sistema $nome\nPede para remover o cenario $id_cenario \nObrigado!", "From: $nome\result\n" . "Reply-To: $email\result\n");
                $record2 = $select2->gonext();
            }
        }
        else
        {
                removeCenario($id_projeto,$id_cenario);
        }
    }
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para inserir um novo lexico ela deve receber os campos do novo
# lexicos.
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico caso o criador n�o seja o gerente.
# Arquivos que utilizam essa funcao:
# add_lexico.php
###################################################################
if (!(function_exists("inserirPedidoAdicionarLexico"))) 
{
    function inserirPedidoAdicionarLexico($id_projeto,$nome,$nocao,$impacto,$id_usuario,$sinonimos, $classificacao)
    {
        $DB = new PGDB() ;
        $insert = new QUERY($DB) ;
        $select = new QUERY($DB) ;
        $select2 = new QUERY($DB) ;
        
        $query = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($queryResult);
        
        
        if ( $resultArray == false ) //nao e gerente
        {
            $insert->execute("INSERT INTO pedidolex (id_projeto,nome,nocao,impacto,tipo,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,'$nome','$nocao','$impacto','$classificacao',$id_usuario,'inserir',0)");
            $newId = $insert->getLastId();
            $select->execute("SELECT * FROM usuario WHERE id_usuario = '$id_usuario'");
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto");
            
            //insert sinonimos
            
            foreach($sinonimos as $sin)
            {
                $insert->execute("INSERT INTO sinonimo (id_pedidolex, nome, id_projeto) 
                VALUES ($newId, '".prepara_dado(strtolower($sin))."', $id_projeto)");
            }
            //fim da insercao dos sinonimos
            
            if ($select->getntuples() == 0 &&$select2->getntuples() == 0)
            {
                echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail." ;
            }
            else
            {
                $record = $select->gofirst ();
                $nome2 = $record['nome'] ;
                $email = $record['email'] ;
                $record2 = $select2->gofirst ();
                while($record2 != 'LAST_RECORD_REACHED')
                {
                    $id = $record2['id_usuario'] ;
                    $select->execute("SELECT * FROM usuario WHERE id_usuario = $id") ;
                    $record = $select->gofirst ();
                    $mailGerente = $record['email'] ;
                    mail("$mailGerente", "Pedido de Inclus�o de L�xico", "O usuario do sistema $nome2\nPede para inserir o lexico $nome \nObrigado!","From: $nome2\result\n"."Reply-To: $email\result\n");
                    $record2 = $select2->gonext();
                }
            }
            
        }
        else //Eh gerente
        { 
            adicionar_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao);
        }
    }
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para alterar um lexico ela deve receber os campos do lexicos
# jah modificados.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico caso o criador n�o seja o gerente.(2.1)
# Arquivos que utilizam essa funcao:
# alt_lexico.php
###################################################################
if (!(function_exists("inserirPedidoAlterarLexico"))) 
{
    function inserirPedidoAlterarLexico($id_projeto,$id_lexico,$nome,$nocao,$impacto,$justificativa,$id_usuario, $sinonimos, $classificacao)
    {                                    
        $DB = new PGDB();
        $insert = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        
        $query = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $resultArray = mysql_fetch_array($queryResult);
                
        if ( $resultArray == false) //nao e gerente
        {
            $insert->execute("INSERT INTO pedidolex (id_projeto,id_lexico,nome,nocao,impacto,id_usuario,tipo_pedido,aprovado,justificativa, tipo) VALUES ($id_projeto,$id_lexico,'$nome','$nocao','$impacto',$id_usuario,'alterar',0,'$justificativa', '$classificacao')");
            $newPedidoId = $insert->getLastId();
            
            //sinonimos
            foreach($sinonimos as $sin)
            {
            	$insert->execute("INSERT INTO sinonimo (id_pedidolex,nome,id_projeto) 
				VALUES ($newPedidoId,'".prepara_dado(strtolower($sin))."', $id_projeto)") ;
            }
            
            
            $select->execute("SELECT * FROM usuario WHERE id_usuario = '$id_usuario'") ;
            $select2->execute("SELECT * FROM participa WHERE gerente = 1 and id_projeto = $id_projeto") ;
            
            if ($select->getntuples() == 0 && $select2->getntuples() == 0)
            {
                echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail." ;
            }
            else
            {
                $record = $select->gofirst ();
                $nome2 = $record['nome'] ;
                $email = $record['email'] ;
                $record2 = $select2->gofirst ();
                while($record2 != 'LAST_RECORD_REACHED')
                {
                    $id = $record2['id_usuario'] ;
                    $select->execute("SELECT * FROM usuario WHERE id_usuario = $id");
                    $record = $select->gofirst ();
                    $mailGerente = $record['email'] ;
                    mail("$mailGerente", "Pedido de Alterar L�xico", "O usuario do sistema $nome2\nPede para alterar o lexico $nome \nObrigado!","From: $nome2\result\n"."Reply-To: $email\result\n");
                    $record2 = $select2->gonext();
                }
            }
        }
        else //Eh gerente
        {
            alteraLexico($id_projeto,$id_lexico, $nome, $nocao, $impacto, $sinonimos, $classificacao) ;
        }
        
    }
}
###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover um lexico ela deve receber
# o id do lexico e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este lexico.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_lexico.php
###################################################################
if (!(function_exists("inserirPedidoRemoverLexico"))) 
{
    function inserirPedidoRemoverLexico($id_projeto,$id_lexico,$id_usuario)
    {
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

            if ($select->getntuples() == 0&&$select2->getntuples() == 0){
                echo "<BR> [ERRO]Pedido nao foi comunicado por e-mail." ;
            }
            else
            {
                $record = $select->gofirst ();
                $nome = $record['nome'] ;
                $email = $record['email'] ;
                $record2 = $select2->gofirst ();
                while($record2 != 'LAST_RECORD_REACHED')
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
        else // e gerente
        {
            removeLexico($id_projeto,$id_lexico);
        }
    }
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para alterar um conceito ela deve receber os campos do conceito
# jah modificados.(1.1)
# Ao final ela manda um e-mail para o gerentes do projeto
# referente a este cenario caso o criador n�o seja o gerente.(2.1)
# Arquivos que utilizam essa funcao:
# alt_cenario.php
###################################################################
if (!(function_exists("inserirPedidoAlterarCenario"))) 
{
    function inserirPedidoAlterarConceito($id_projeto, $id_conceito, $nome, $descricao, $namespace, $justificativa, $id_usuario)
    {
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
                mail("$mailGerente", "Pedido de Altera��o Conceito", "O usuario do sistema $nomeUsuario\nPede para alterar o conceito $nome \nObrigado!","From: $nomeUsuario\result\n"."Reply-To: $email\result\n");
                $record2 = $select2->gonext();
            }
        }
        else //Eh gerente
        {
            removeConceito($id_projeto,$id_conceito) ;
            adicionar_conceito($id_projeto, $nome, $descricao, $namespace);
        }
    }
}

###################################################################
# Funcao faz um insert na tabela de pedido.
# Para remover um conceito ela deve receber
# o id do conceito e id projeto.(1.1)
# Ao final ela manda um e-mail para o gerente do projeto
# referente a este conceito.(2.1)
# Arquivos que utilizam essa funcao:
# rmv_conceito.php
###################################################################
if (!(function_exists("inserirPedidoRemoverConceito")))
{
    function inserirPedidoRemoverConceito($id_projeto,$id_conceito,$id_usuario)
    {
        $DB = new PGDB();
        $insert = new QUERY($DB);
        $select = new QUERY($DB);
        $select2 = new QUERY($DB);
        $select->execute("SELECT * FROM conceito WHERE id_conceito = $id_conceito") ;
        $conceito = $select->gofirst ();
        $nome = $conceito['nome'] ;
        
        $insert->execute("INSERT INTO pedidocon (id_projeto,id_conceito,nome,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,$id_conceito,'$nome',$id_usuario,'remover',0)") ;
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
            while($record2 != 'LAST_RECORD_REACHED')
            {
                $id = $record2['id_usuario'] ;
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id") ;
                $record = $select->gofirst ();
                $mailGerente = $record['email'] ;
                mail("$mailGerente", "Pedido de Remover Conceito", "O usuario do sistema $nome2\nPede para remover o conceito $id_conceito \nObrigado!","From: $nome\result\n"."Reply-To: $email\result\n");
                $record2 = $select2->gonext();
            }
        }
    }
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
        $DB = new PGDB () ;
        $insert = new QUERY ($DB) ;
        $select = new QUERY ($DB) ;
        $select2 = new QUERY ($DB) ;
        $select->execute("SELECT * FROM relacao WHERE id_relacao = $id_relacao") ;
        $relacao = $select->gofirst ();
        $nome = $relacao['nome'] ;
        
        $insert->execute("INSERT INTO pedidorel (id_projeto,id_relacao,nome,id_usuario,tipo_pedido,aprovado) VALUES ($id_projeto,$id_relacao,'$nome',$id_usuario,'remover',0)") ;
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
            while($record2 != 'LAST_RECORD_REACHED')
            {
                $id = $record2['id_usuario'] ;
                $select->execute("SELECT * FROM usuario WHERE id_usuario = $id") ;
                $record = $select->gofirst ();
                $mailGerente = $record['email'] ;
                mail("$mailGerente", "Pedido de Remover Conceito", "O usuario do sistema $nome2\nPede para remover o conceito $id_relacao \nObrigado!","From: $nome\result\n"."Reply-To: $email\result\n");
                $record2 = $select2->gonext();
            }
        }
    }
}

###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o cenario e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoCenario"))) 
{
    function tratarPedidoCenario($id_pedido)
    {
        $DB = new PGDB () ;
        $select = new QUERY ($DB) ;
        $delete = new QUERY ($DB) ;
        //print("<BR>SELECT * FROM pedidocen WHERE id_pedido = $id_pedido");
        $select->execute("SELECT * FROM pedidocen WHERE id_pedido = $id_pedido") ;
        if ($select->getntuples() == 0)
        {
            echo "<BR> [ERRO]Pedido invalido." ;
        }
        else
        {
            $record = $select->gofirst () ;
            $tipoPedido = $record['tipo_pedido'] ;
            if (!strcasecmp($tipoPedido,'remover'))
            {
                $id_cenario = $record['id_cenario'] ;
                $id_projeto = $record['id_projeto'] ;
                removeCenario($id_projeto,$id_cenario) ;
                //$delete->execute ("DELETE FROM pedidocen WHERE id_cenario = $id_cenario") ;
            }
            else
            {
                
                $id_projeto = $record['id_projeto'] ;
                $title = $record['titulo'] ;
                $objective = $record['objetivo'] ;
                $context = $record['contexto'] ;
                $actors = $record['atores'] ;
                $resources = $record['recursos'] ;
                $exception = $record['excecao'] ;
                $episodes = $record['episodios'] ;
                if (!strcasecmp($tipoPedido,'alterar'))
                {
                    $id_cenario = $record['id_cenario'] ;
                    removeCenario($id_projeto,$id_cenario) ;
                    //$delete->execute ("DELETE FROM pedidocen WHERE id_cenario = $id_cenario") ;
                }
                adicionar_cenario($id_projeto, $title, $objective, $context, $actors, $resources, $exception, $episodes) ;
            }
            //$delete->execute ("DELETE FROM pedidocen WHERE id_pedido = $id_pedido") ;
        }
    }
}
###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o lexico e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoLexico")))
{
    function tratarPedidoLexico($id_pedido)
    {
        $DB = new PGDB () ;
        $select = new QUERY ($DB) ;
        $delete = new QUERY ($DB);
        $selectSin = new QUERY ($DB);
        $select->execute("SELECT * FROM pedidolex WHERE id_pedido = $id_pedido") ;
        if ($select->getntuples() == 0)
        {
            echo "<BR> [ERRO]Pedido invalido." ;
        }
        else
        {
            $record = $select->gofirst () ;
            $tipoPedido = $record['tipo_pedido'] ;
            if (!strcasecmp($tipoPedido,'remover'))
            {
                $id_lexico = $record['id_lexico'] ;
                $id_projeto = $record['id_projeto'] ;
                removeLexico($id_projeto,$id_lexico) ;
            }
            else
            {
                $id_projeto = $record['id_projeto'] ;
                $nome = $record['nome'] ;
                $nocao = $record['nocao'] ;
                $impacto = $record['impacto'] ;
                $classificacao = $record['tipo'];
                
                //sinonimos
                
                $sinonimos = array();
                $selectSin->execute("SELECT nome FROM sinonimo WHERE id_pedidolex = $id_pedido");
                $sinonimo = $selectSin->gofirst();
                if ($selectSin->getntuples() != 0)
                {
               	    while($sinonimo != 'LAST_RECORD_REACHED')
               	    {
                        $sinonimos[] = $sinonimo["nome"];
                        $sinonimo = $selectSin->gonext();
                    }
                }
                
                if (!strcasecmp($tipoPedido,'alterar'))
                {
                    $id_lexico = $record['id_lexico'] ;
                    alteraLexico($id_projeto, $id_lexico, $nome, $nocao, $impacto, $sinonimos, $classificacao);
                }
                else if(($idLexicoConflitante = adicionar_lexico($id_projeto, $nome, $nocao, $impacto, $sinonimos, $classificacao)) <= 0)
                {
                    $idLexicoConflitante = -1 * $idLexicoConflitante;
                    $selectLexConflitante->execute("SELECT nome FROM lexico WHERE id_lexico = " . $idLexicoConflitante);
                    $row = $selectLexConflitante->gofirst();
                    
                    return $row["nome"];
                }
            }
            
            return null;
        }
    }
}
###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o cenario e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoConceito")))
{
    function tratarPedidoConceito($id_pedido)
    {
        $DB = new PGDB();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        $select->execute("SELECT * FROM pedidocon WHERE id_pedido = $id_pedido");
        if ($select->getntuples() == 0)
        {
            echo "<BR> [ERRO]Pedido invalido.";
        }
        else
        {
            $record = $select->gofirst ();
            $tipoPedido = $record['tipo_pedido'];
            if (!strcasecmp($tipoPedido,'remover'))
            {
                $id_conceito = $record['id_conceito'];
                $id_projeto = $record['id_projeto'];
                removeConceito($id_projeto,$id_conceito);
            }
            else
            {
                $id_projeto = $record['id_projeto'] ;
                $nome = $record['nome'] ;
                $descricao = $record['descricao'] ;
                $namespace = $record['namespace'] ;
                
                if (!strcasecmp($tipoPedido,'alterar'))
                {
                    $id_cenario = $record['id_conceito'] ;
                    removeConceito($id_projeto,$id_conceito) ;
                }
                adicionar_conceito($id_projeto, $nome, $descricao, $namespace) ;
            }
        }
    }
}

###################################################################
# Processa um pedido identificado pelo seu id.
# Recebe o id do pedido.(1.1)
# Faz um select para pegar o pedido usando o id recebido.(1.2)
# Pega o campo tipo_pedido.(1.3)
# Se for para remover: Chamamos a funcao remove();(1.4)
# Se for para alterar: Devemos (re)mover o cenario e inserir o novo.
# Se for para inserir: chamamos a funcao insert();
###################################################################
if (!(function_exists("tratarPedidoRelacao")))
{
    function tratarPedidoRelacao($id_pedido)
    {
        $DB = new PGDB();
        $select = new QUERY($DB);
        $delete = new QUERY($DB);
        $select->execute("SELECT * FROM pedidorel WHERE id_pedido = $id_pedido");
        
        if ($select->getntuples() == 0)
        {
            echo "<BR> [ERRO]Pedido invalido." ;
        }
        else
        {
            $record = $select->gofirst () ;
            $tipoPedido = $record['tipo_pedido'] ;
            if (!strcasecmp($tipoPedido,'remover'))
            {
                $id_relacao = $record['id_relacao'] ;
                $id_projeto = $record['id_projeto'] ;
                removeRelacao($id_projeto,$id_relacao) ;
            }
            else
            {
                $id_projeto = $record['id_projeto'] ;
                $nome         = $record['nome'] ;
                                
                if (!strcasecmp($tipoPedido,'alterar'))
                {
                    $id_relacao = $record['id_relacao'] ;
                    removeRelacao($id_projeto,$id_relacao) ;
                }
                adicionar_relacao($id_projeto, $nome) ;
            }
        }
    }
}
#############################################
#Deprecated by the author:
#Essa funcao deveria receber um id_projeto
#de forma a verificar se o gerente pertence
#a esse projeto.Ela so verifica atualmente
#se a pessoa e um gerente.
#############################################
if (!(function_exists("verificaGerente")))
{
    function verificaGerente($id_usuario)
    {
        $DB = new PGDB () ;
        $select = new QUERY ($DB) ;
        $select->execute("SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario");
        
        if ($select->getntuples() == 0)
        {
            return 0 ;
        }
        else
        {
            return 1 ;
        }
    }
}

#############################################
# Formata Data
# Recebe YYY-DD-MM
# Retorna DD-MM-YYYY
#############################################
if (!(function_exists("formataData"))) 
{
    function formataData($data)
    {
        $novaData = substr( $data, 8, 9 ) .
        substr( $data, 4, 4 ) .
        substr( $data, 0, 4 );
        return $novaData ;
    }
}

// Retorna TRUE ssse $id_usuario eh admin de $id_projeto
if (!(function_exists("is_admin")))
{
    function is_admin($id_usuario, $id_projeto)
    {
        $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $query = "SELECT *
              FROM participa
              WHERE id_usuario = $id_usuario
              AND id_projeto = $id_projeto
              AND gerente = 1";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        return (1 == mysql_num_rows($queryResult));
    }
}

// Retorna TRUE ssse $id_usuario tem permissao sobre $id_projeto
if (!(function_exists("check_proj_perm")))
{
    function check_proj_perm($id_usuario, $id_projeto)
    {
        $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        $query = "SELECT *
              FROM participa
              WHERE id_usuario = $id_usuario
              AND id_projeto = $id_projeto";
        $queryResult = mysql_query($query) or die("Erro ao enviar a query<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        return (1 == mysql_num_rows($queryResult));
    }
}
###################################################################
# Verifica se um determinado usuario e gerente de um determinado
# projeto
# Recebe o id do projeto. (1.1)
# Faz um select para pegar o resultArray da tabela Participa.(1.2)
# Se o resultArray for nao nulo: devolvemos TRUE(1);(1.3)
# Se o resultArray for nulo: devolvemos False(0);(1.4)
###################################################################
function verificaGerente($id_usuario, $id_projeto)
{
    $ret = 0;
    $query = "SELECT * FROM participa WHERE gerente = 1 AND id_usuario = $id_usuario AND id_projeto = $id_projeto";
    $queryResult = mysql_query($query) or die("Erro ao enviar a query de select no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArray = mysql_fetch_array($queryResult);
    
    if ( $resultArray != false )
	{
        
        $ret = 1;
    }
    
    return $ret;
}

###################################################################
# Remove um determinado projeto da base de dados
# Recebe o id do projeto. (1.1)
# Apaga os valores da tabela pedidocen que possuam o id do projeto enviado (1.2)
# Apaga os valores da tabela pedidolex que possuam o id do projeto enviado (1.3)
# Faz um SELECT para saber quais l�xico pertencem ao projeto de id_projeto (1.4)
# Apaga os valores da tabela lextolex que possuam possuam lexico do projeto (1.5)
# Apaga os valores da tabela centolex que possuam possuam lexico do projeto (1.6)
# Apaga os valores da tabela sinonimo que possuam possuam o id do projeto (1.7)
# Apaga os valores da tabela lexico que possuam o id do projeto enviado (1.8)
# Faz um SELECT para saber quais cenario pertencem ao projeto de id_projeto (1.9)
# Apaga os valores da tabela centocen que possuam possuam cenarios do projeto (2.0)
# Apaga os valores da tabela centolex que possuam possuam cenarios do projeto (2.1)
# Apaga os valores da tabela cenario que possuam o id do projeto enviado (2.2)
# Apaga os valores da tabela participa que possuam o id do projeto enviado (2.3)
# Apaga os valores da tabela publicacao que possuam o id do projeto enviado (2.4)
# Apaga os valores da tabela projeto que possuam o id do projeto enviado (2.5)
#
###################################################################
function removeProjeto($id_projeto)
{
    $result = bd_connect() or die("Erro ao conectar ao SGBD<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    
    //Remove os pedidos de cenario
    $queryVerification = "Delete FROM pedidocen WHERE id_projeto = '$id_projeto' ";
    $deletaPedidoCenario = mysql_query($queryVerification) or die("Erro ao apagar pedidos de cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    
    //Remove os pedidos de lexico
    $queryVerification = "Delete FROM pedidolex WHERE id_projeto = '$id_projeto' ";
    $deletaPedidoLexico = mysql_query($queryVerification) or die("Erro ao apagar pedidos do lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    
    //Remove os lexicos //verificar lextolex!!!
    $queryVerification = "SELECT * FROM lexico WHERE id_projeto = '$id_projeto' ";
    $queryVerificationResult = mysql_query($queryVerification) or die("Erro ao enviar a query de select no lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    
    while ($result = mysql_fetch_array($queryVerificationResult))
    {
        $id_lexico = $result['id_lexico']; //seleciona um lexico
        
        $queryVerification = "Delete FROM lextolex WHERE id_lexico_from = '$id_lexico' OR id_lexico_to = '$id_lexico' ";
        $deletaLextoLe = mysql_query($queryVerification) or die("Erro ao apagar pedidos do lextolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        $queryVerification = "Delete FROM centolex WHERE id_lexico = '$id_lexico'";
        $deletacentolex = mysql_query($queryVerification) or die("Erro ao apagar pedidos do centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        //$queryVerification = "Delete FROM sinonimo WHERE id_lexico = '$id_lexico'";
        //$deletacentolex = mysql_query($queryVerification) or die("Erro ao apagar sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        $queryVerification = "Delete FROM sinonimo WHERE id_projeto = '$id_projeto'";
        $deletacentolex = mysql_query($queryVerification) or die("Erro ao apagar sinonimo<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
    }
    
    $queryVerification = "Delete FROM lexico WHERE id_projeto = '$id_projeto' ";
    $deletaLexico = mysql_query($queryVerification) or die("Erro ao apagar pedidos do lexico<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    
    //remove os cenarios
    $queryVerification = "SELECT * FROM cenario WHERE id_projeto = '$id_projeto' ";
    $queryVerificationResult = mysql_query($queryVerification) or die("Erro ao enviar a query de select no cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    $resultArrayCenario = mysql_fetch_array($queryVerificationResult);
    
    while ($result = mysql_fetch_array($queryVerificationResult))
    {
        $id_lexico = $result['id_cenario']; //seleciona um lexico
        
        $queryVerification = "Delete FROM centocen WHERE id_cenario_from = '$id_cenario' OR id_cenario_to = '$id_cenario' ";
        $deletaCentoCen = mysql_query($queryVerification) or die("Erro ao apagar pedidos do centocen<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        $queryVerification = "Delete FROM centolex WHERE id_cenario = '$id_cenario'";
        $deletaLextoLe = mysql_query($queryVerification) or die("Erro ao apagar pedidos do centolex<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
        
        
    }
    
    $queryVerification = "Delete FROM cenario WHERE id_projeto = '$id_projeto' ";
    $deletaLexico = mysql_query($queryVerification) or die("Erro ao apagar pedidos do cenario<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    
    //remover participantes
    $queryVerification = "Delete FROM participa WHERE id_projeto = '$id_projeto' ";
    $deletaParticipantes = mysql_query($queryVerification) or die("Erro ao apagar no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    
    //remover publicacao
    $queryVerification = "Delete FROM publicacao WHERE id_projeto = '$id_projeto' ";
    $deletaPublicacao = mysql_query($queryVerification) or die("Erro ao apagar no publicacao<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    
    //remover projeto
    $queryVerification = "Delete FROM projeto WHERE id_projeto = '$id_projeto' ";
    $deletaProjeto= mysql_query($queryVerification) or die("Erro ao apagar no participa<br>" . mysql_error() . "<br>" . __FILE__ . __LINE__);
    
}
?>
