<?php

session_start();

include("funcoes_genericas.php");
include_once("coloca_links.php");
include("httprequest.inc");
include_once("bd.inc");
include_once("seguranca.php");

chkUser("index.php");        // Checa se o usuario foi autenticado
      

if( isset( $_POST['flag']))
{
    $flag = "ON";
}
else
{
    $flag = "OFF";
}


?>

<?php

// gerador_xml.php

// Dada a base e o id do projeto, gera-se o xml

// dos cenários e léxicos.

//Cenário - Gerar Relatórios XML 

//Objetivo:    Permitir ao administrador gerar relatórios em formato XML de um projeto, identificados por data.     
//Contexto:    Gerente deseja gerar um relatório para um dos projetos da qual é administrador.
//          Pré-Condição: Login, projeto cadastrado.
//Atores:    Administrador     
//Recursos:    Sistema, dados do relatório, dados cadastrados do projeto, banco de dados.     
//Episódios:O sistema fornece para o administrador uma tela onde deverá fornecer os dados
//          do relatório para sua posterior identificação, como data e versão. 
//          Para efetivar a geração do relatório, basta clicar em Gerar. 
//          Restrição: O sistema executará duas validações: 
//                      - Se a data é válida.
//                      - Se existem cenários e léxicos em datas iguais ou anteriores.
//          Gerando com sucesso o relatório a partir dos dados cadastrados do projeto,
//          o sistema fornece ao administrador a tela de visualização do relatório XML criado. 
//          Restrição: Recuperar os dados em XML do Banco de dados e os transformar por uma XSL para a exibição.      

if (!(function_exists("gerar_xml"))) {
    function gerar_xml( $bd, $id_projeto, $data_pesquisa, $flag_formatado)
    {
        $xml_resultante = "";
		$vetorVazio = array();
       
        if ($flag_formatado == "ON")
        {
			$xml_resultante = "";
			$xml_resultante = $xml_resultante . "<?xml-stylesheet type='text/xsl' href='projeto.xsl'?>\n" ;
        }
        $xml_resultante = $xml_resultante . "<projeto>\n" ;

        // Seleciona o nome do projeto

	    $qry_nome = "SELECT nome
                     FROM projeto
                     WHERE id_projeto = " . $id_projeto ;
	    $tb_nome = mysql_query ( $qry_nome ) or die ( "Erro ao enviar a query de selecao." ) ;

        // Adiciona o nome do projeto no xml		
		$xml_resultante = $xml_resultante . "<nome>" . mysql_result ( $tb_nome, 0 ) . "</nome>\n"; 

        ## CENÁRIOS ##
        
        // Seleciona os cenários de um projeto.

        $qry_cenario = "SELECT id_cenario ,
                               titulo ,
                               objetivo ,
                               contexto ,
                               atores ,
                               recursos ,
                               episodios ,
                               excecao
                        FROM   cenario
                        WHERE  (id_projeto = " . $id_projeto
                        . ") AND (data <=" . " '" . $data_pesquisa . "'". ")
                        ORDER BY id_cenario,data DESC";

        $tb_cenario = mysql_query( $qry_cenario ) or die( "Erro ao enviar a query de selecao." ) ;

        $primeiro = true;

        $id_temp = "";
		
		$vetor_todos_lexicos = carrega_vetor_lexicos($id_projeto, 0, false);

		// Para cada cenário
		
  	    while ( $row = mysql_fetch_row( $tb_cenario ) ) 
        {
            $id_cenario = "<ID>" . $row[ 0 ] . "</ID>" ;
            $id_cenario_atual = $row[ 0 ];
            $vetor_cenarios = carrega_vetor_cenario( $id_projeto, $id_cenario_atual,true );
            
            // Porque usa $id_temp != $id_cenario ? e a variavel primeiro
            
            if (($id_temp != $id_cenario) or (primeiro))
            {
                $titulo = '<titulo id="' . strtr(strip_tags ( $row[ 1 ] ),"áâãàóõôéêç","aaaaoooeec") . '">' . ucwords(strip_tags ( $row[ 1 ] )) . '</titulo>' ;

                $objetivo = "<objetivo>" . "<sentenca>" . gera_xml_links ( monta_links ( $row[ 2 ], $vetor_todos_lexicos, $vetorVazio ) ) . "</sentenca>" . "<PT/>" . "</objetivo>" ;
																		   			
                $contexto = "<contexto>" . "<sentenca>" . gera_xml_links ( monta_links ( $row[ 3 ], $vetor_todos_lexicos, $vetor_cenarios ) ) . "</sentenca>" . "<PT/>" . "</contexto>" ;

                $atores = "<atores>" . "<sentenca>" . gera_xml_links ( monta_links ( $row[ 4 ], $vetor_todos_lexicos, $vetorVazio ) ) . "</sentenca>" . "<PT/>" . "</atores>" ;

                $recursos = "<recursos>" . "<sentenca>" . gera_xml_links ( monta_links ( $row[ 5 ], $vetor_todos_lexicos, $vetorVazio ) ) . "</sentenca>" . "<PT/>" . "</recursos>" ;

                $excecao = "<excecao>" . "<sentenca>" . gera_xml_links ( monta_links ( $row[ 7 ], $vetor_todos_lexicos, $vetorVazio ) ) . "</sentenca>" . "<PT/>" . "</excecao>" ;

                $episodios = "<episodios>" . "<sentenca>" . gera_xml_links ( monta_links ( $row[ 6 ], $vetor_todos_lexicos, $vetor_cenarios ) ) . "</sentenca>" . "<PT/>" . "</episodios>" ;
																			 
                $xml_resultante = $xml_resultante . "<cenario>\n" ;

                $xml_resultante = $xml_resultante . "$titulo\n" ;

                $xml_resultante = $xml_resultante . "$objetivo\n" ;

                $xml_resultante = $xml_resultante . "$contexto\n" ;

                $xml_resultante = $xml_resultante . "$atores\n" ;

                $xml_resultante = $xml_resultante . "$recursos\n" ;

                $xml_resultante = $xml_resultante . "$episodios\n" ;
                
                $xml_resultante = $xml_resultante . "$excecao\n" ;

                $xml_resultante = $xml_resultante . "</cenario>\n" ;

                $primeiro = false;

                //??$id_temp = id_cenario;
           }
        
        } // while dos cenários
        
        // Seleciona os lexicos de um projeto.
        
        $qry_lexico = "SELECT id_lexico ,
                               nome ,
                               nocao ,
                               impacto
                        FROM   lexico
                        WHERE  (id_projeto = " . $id_projeto .

                ") AND (data <=" . " '" . $data_pesquisa . "'". ")

                ORDER BY id_lexico,data DESC";

        $tb_lexico = mysql_query( $qry_lexico ) or die( "Erro ao enviar a query de selecao." ) ;

        $primeiro = true;

        $id_temp = "";
	
		// Para cada simbolo do lexico
	
        while ( $row = mysql_fetch_row( $tb_lexico ) ) 
        {
			$vetor_lexicos = carrega_vetor_lexicos( $id_projeto, $row[ 0 ],true );
			quicksort( $vetor_lexicos, 0, count($vetor_lexicos)-1,'lexico' );
        	$id_lexico = "<ID>" . $row[ 0 ] . "</ID>" ;
            if (($id_temp != $id_lexico) or (primeiro))
            {

				$nome = '<nome_simbolo id="' . strtr(strip_tags ( $row[ 1 ] ),"áâãàóõôéêç","aaaaoooeec") . '">' . '<texto>' . ucwords(strip_tags ( $row[ 1 ] )) . '</texto>' . '</nome_simbolo>' ;

				
				// Consulta os sinonimos do simbolo
				$querySinonimo = "SELECT nome 
									FROM sinonimo
									WHERE (id_projeto = " . $id_projeto . ") 
									AND (id_lexico = " . $row[0] ." )";	
				
				$resultSinonimos = mysql_query( $querySinonimo ) or die( "Erro ao enviar a query de selecao de sinonimos." ) ;
				
				//Para cada sinonimo do simbolo
				$sinonimo = "<sinonimos>";
				
				while ( $rowSin = mysql_fetch_row( $resultSinonimos ) ) 
				{
					$sinonimo .= "<sinonimo>" . $rowSin[0] . "</sinonimo>";
				}
				$sinonimo .= "</sinonimos>";  
				
				$nocao = "<nocao>" . "<sentenca>" . gera_xml_links ( monta_links( $row[ 2 ], $vetor_lexicos, $vetorVazio ) ) . "<PT/>" . "</sentenca>" . "</nocao>" ;

				$impacto = "<impacto>" . "<sentenca>" . gera_xml_links ( monta_links( $row[ 3 ], $vetor_lexicos, $vetorVazio )) . "<PT/>" . "</sentenca>" . "</impacto>" ;

                $xml_resultante = $xml_resultante . "<lexico>\n" ;

                $xml_resultante = $xml_resultante . "$nome\n" ;

                $xml_resultante = $xml_resultante . "$sinonimo\n";
				
				$xml_resultante = $xml_resultante . "$nocao\n" ;

                $xml_resultante = $xml_resultante . "$impacto\n" ;

                $xml_resultante = $xml_resultante . "</lexico>\n" ;

                $primeiro = false;

                //$id_temp = id_lexico;
            }

        } // while

        $xml_resultante = $xml_resultante . "</projeto>\n" ;

        return $xml_resultante ;

    } // gerar_xml
}

///////////////////////////////////////////////////////////////////////////////////////////////////
//
//Cenário - Gerar links nos Relatórios XML criados
//
//Objetivo:    Permitir que os relatórios gerados em formato XML possuam termos com links 
//          para os seus respectivos léxicos
//
//Contexto:    Gerente deseja gerar um relatório em XML para um dos projetos da qual é administrador.
//          Pré-Condição: Login, projeto cadastrado, acesso ao banco de dados.
//
//Atores:    Sistema    
//
//Recursos:    Sistema, sentenças a serem linkadas, dados cadastrados do projeto, banco de dados. 
//    
//Episódios:O sistema recebe a sentença com os tags próprios do C&L e retorna o código do link HTML
//            equivalente para os léxicos cadatrados no sistema. 
//     
///////////////////////////////////////////////////////////////////////////////////////////////////
//
//Léxicos:
//
//     Função:            gera_xml_links
//     Descrição:         Analisa uma sentença recebida afim de identificar as tags utilizadas no C&L
//                        para linkar os léxicos e transformar em links XML.
//     Sinônimos:         -
//     Exemplo: 
//        ENTRADA: <!--CL:tam:2--><a title="Lexico" href="main.php?t=l&id=228">software livre</a>
//                 <!--/CL-->
//        SAÍDA:  <a title="Lexico" href="main.php?t=l&id=228"><texto referencia_lexico=software 
//                livre>software livre</texto></a>
//
//     Variável:            $sentenca
//     Descrição:         Armazena a expressão passada por argumento a ser tranformada em link.
//     Sinônimos:         -
//     Exemplo:             <!--CL:tam:2--><a title="Lexico" href="main.php?t=l&id=228">software livre
//                        </a><!--/CL-->
//
//     Variável:            $regex
//     Descrição:            Armazena o pattern a ser utilizado ao se separar a sentença.
//     Sinônimos:            -
//     Exemplo:            "/(<!--CL:tam:\d+-->(<a[^>]*?\>)([^<]*?)<\/a><!--\/CL-->)/mi"
//
//     Variável:            $vetor_texto
//     Descrição:         Array que armazena palavra por palavra a senteça a ser linkada, sem o tag.
//     Sinônimos:         -
//     Exemplo:             $vetor_texto[0] => software
//                        $vetor_texto[1] => livre
//
//     Variável:            $inside_tag
//     Descrição:         Determina se a análise está sendo feita dentro ou fora do tag
//     Sinônimos:         -
//     Exemplo:             false
//
//     Variável:            $tamanho_vetor_texto
//     Descrição:         Armazena a número de palavras que se encontram no array $vetor_texto. 
//     Sinônimos:         -
//     Exemplo:             2
//
//     Variável:            $i
//     Descrição:         Variável utilizada como um contador para uso genérico.
//     Sinônimos:         -
//     Exemplo:             -
//
//     Variável:            $match
//     Descrição:         Armazena o valor 1 caso a string "/href="main.php\?t=(.)&id=(\d+?)"/mi"
//                        seja encontrada na no array $vetor_texto. Caso contrário, armazena 0.
//     Sinônimos:         -
//     Exemplo:             0
//
//     Variável:            $id_projeto
//     Descrição:         Armazena o número identificador do projeto corrente.
//     Sinônimos:         -
//     Exemplo:             1
//
//     Variável:            $atributo
//     Descrição:         Armazena um tag que indica a referência para um léxico
//     Sinônimos:         -
//     Exemplo:             referencia_lexico
//
//     Variável:            $query
//     Descrição:         Armazena a consulta a ser feita no banco de dados
//     Sinônimos:         -
//     Exemplo:             SELECT nome FROM lexico WHERE id_projeto = $id_projeto
//
//     Variável:            $result
//     Descrição:         Armazena o resultado da consulta feita ao banco de dados
//     Sinônimos:         -
//     Exemplo:             -
//
//     Variável:            $row
//     Descrição:         Array que armazena tupla a tupla o resultado da consulta realizada
//     Sinônimos:         -
//     Exemplo:             -
//
//     Variável:            $valor
//     Descrição:         Armazena uma tupla, substituindo os caracteres acentuados pelos seus 
//                        equivalentes sem acentuação.
//     Sinônimos:         -
//     Exemplo:             acentuacao
//
///////////////////////////////////////////////////////////////////////////////////////////////////


if (!(function_exists("gera_xml_links"))) {
    function gera_xml_links($sentenca)
    {
        
        if (trim($sentenca)!="")
        {
        
        	$regex = "/(<a[^>]*?>)(.*?)<\/a>/";
	
            $vetor_texto = preg_split($regex, $sentenca, -1, PREG_SPLIT_DELIM_CAPTURE);
            $tamanho_vetor_texto = count($vetor_texto);
            $i = 0;
            
                 
            while ($i < $tamanho_vetor_texto )
            {
           		preg_match('/href="main.php\?t=(.)&id=(\d+?)"/mi', $vetor_texto[$i], $match);
                if($match)
                {
                    $id_projeto = $_SESSION['id_projeto_corrente'];
                        
                    // Verifica se é léxico 
                    if($match[1]=='l')
                    {
                        // Retira o link do texto
                       $vetor_texto[$i]="";
                        
                        //link para léxico
                        $atributo = "referencia_lexico";                        
                            
                        $query = "SELECT nome FROM lexico WHERE id_projeto = $id_projeto AND id_lexico = $match[2] ";
                        $result = mysql_query($query) or die("Erro ao enviar a query lexico");
                        $row = mysql_fetch_row($result);
                    	// Pega o nome do léxico
                        $valor = strtr( $row[ 0 ] ,"áâãàóõôéêç","aaaaoooeec");
                            
                        $vetor_texto[$i+1] = '<texto '.$atributo.'="'.$valor.'">'.$vetor_texto[$i+1].'</texto>';
                    } else if($match[1]=='c')
                    {
                        // Retira o link do texto
                        $vetor_texto[$i]="";
                        
                        //link para cenário
                        $atributo = "referencia_cenario";                        
                            
                        $query = "SELECT titulo FROM cenario WHERE id_projeto = $id_projeto AND id_cenario = $match[2] ";
                        $result = mysql_query($query) or die("Erro ao enviar a query cenario");
                        $row = mysql_fetch_row($result);
                        // Pega o titulo do cenario
                        $valor = strtr( $row[ 0 ] ,"áâãàóõôéêç","aaaaoooeec");
                            
                        $vetor_texto[$i+1] = '<texto '.$atributo.'="'.$valor.'">'.strip_tags($vetor_texto[$i+1]).'</texto>';
                    }
                    
                 	$i = $i+2;   
                }
                else
                {
                    if (trim($vetor_texto[$i])!="")
                    {
                        $vetor_texto[$i] = "<texto>".$vetor_texto[$i]."</texto>";
                    }
                    
                    $i = $i+1;
                }
            }
            // Junta os elementos do array vetor_texto em uma string
            return implode("", $vetor_texto);
        }
        return $sentenca;
    }
}
?>

<?php

    $id_projeto = $_SESSION['id_projeto_corrente'];
    $data_pesquisa = $data_ano . "-" . $data_mes . "-" . $data_dia;
    $flag_formatado = $flag;

    // Abre base de dados.
      $bd_trabalho = bd_connect() or die("Erro ao conectar ao SGBD");
      
      $qVerifica = "SELECT * FROM publicacao WHERE id_projeto = '$id_projeto' AND versao = '$versao' ";
      $qrrVerifica = mysql_query($qVerifica);

		// Se não existir nenhum XML com o id passado ele cria
        if ( !mysql_num_rows($qrrVerifica) )
        {

			$str_xml = gerar_xml( $bd_trabalho , $id_projeto,  $data_pesquisa, $flag_formatado ) ;
           
			$xml_resultante = "<?xml version='1.0' encoding='ISO-8859-1' ?>\n".$str_xml ;
			
            $q = "INSERT INTO publicacao ( id_projeto, data_publicacao, versao, XML)
                 VALUES ( '$id_projeto', '$data_pesquisa', '$versao', '".mysql_real_escape_string($xml_resultante)."')";
              
			mysql_query($q) or die("Erro ao enviar a query INSERT do XML no banco de dados! ");
			recarrega("http://pes.inf.puc-rio.br/cel/aplicacao/mostraXML.php?id_projeto=".$id_projeto."&versao=".$versao);
		}
    else
    {
    ?>
    <html><head><title>Projeto</title></head><body bgcolor="#FFFFFF">
    <p style="color: red; font-weight: bold; text-align: center">Essa versão já existe!</p>
    <br>
    <br>
        <center><a href="JavaScript:window.history.go(-1)">Voltar</a></center>
    </body></html>
    
    <?php
    }   
?> 
