<?php
	/****************************************************
	* Conversor de um arquivo XML gerado pelo CEL para  *
	* um arquivo XML utilizável pelo sistema de grafo   *
	*****************************************************/


	/*********************************
	*        VARIÁVEIS GLOBAIS       *
	**********************************/

	$debug = 0;

	// O arquivo XML a converter
	$filein = $_GET['file'];
	
	// O arquivo XML de saída
	$fileout = "Graph.xml";

	$i = 1;
	// If the file exists and isn't older than 5 minutes, then create another one
	while ((file_exists($fileout)) && ( time() - filemtime($fileout) < 300 )) {
		$fileout = "Graph_".$i.".xml";
		$i++;
	}
	
	// Para o debug, permite indentar o código:
	// Grau de profundidade dos tags
	$depth = 0;
	// Indica se o cursor de leitura está lendo texto entre um par de tag.
	$openedtag = 0;
	
	// Indica se o tag TEXTO foi aberto
	$openText = 0;

	// Indica se a função de leitura de caracteres deve ler o texto ou não
	$readText = 0;

	// Texto lido	
	$textTotal = "";

	// Expressão destacada para fazer uma referência
	$edgeWord = "";

	// Indica se o tag NOME_SIMBOLO foi aberto
	$openNomeSimbolo = 0;
	

	/********************************************************
	*                      START ELEMENT                    *
	*                           -                           *
	* Função chamada quando o parser encontra um tag aberto *
	*********************************************************/

	function startElement($parser, $name, $attribs)
	{
		global $depth;
		global $openedtag;
		global $out;
		global $openText;
		global $readText;
		global $edgeWord;
		global $textTotal;
		global $openNomeSimbolo;
		global $edgeDestID;
		global $edges;
		global $debug;
		
		if ($name == "NOME_SIMBOLO") {

			// Indica que o cursor está dentro do tag NOME_SIMBOLO
			$openNomeSimbolo = 1;

		}

		if ($name == "TEXTO") {

			// Indica que o programa vai ler o texto a seguir
			$readText = 1;

			// Se tiver um atributo, é uma referência. 
			foreach( $attribs as $key => $value ) {
				// Guardar o identificador do nó de destinação em memória enquanto não conhecemos a palavra fonte do elo.
				$edgeDestID = $value;
			}
			
			// Se é a primeira vez que o tag TEXTO for encontrado (que não seja dentro de um tag NOME_SIMBOLO)...
			if ((!$openText) && (!$openNomeSimbolo)) {

				// Indicar que o cursor está dentro do tag TEXTO
				$openText = 1;

				// Reinicializar o texto lido
				$textTotal = "";

				// Para a indentação (debug)
				newLine();

				// Escrever o tag TEXTO (só a primeira vez!)
				output( "<TEXTO>" );

			}
		} 
		else if ($name == "PT") {
			// Fazer nada. Para se livrar deste tag.
		} 
		else {

			// Para a indentação (debug)
			newLine();

			// Escreve o nome do tag...
			output( "<$name" );

			// ... e seus atributos
			foreach( $attribs as $key => $value ) 
			{
				output( " $key=\"$value\"" );
			}
			output( ">" );

			// Incrementar a indentação
		    	$depth++;

			// Indicar que um tag foi aberto (útil para a indentação só)
			$openedtag = 1;

		}

	}


	/*********************************************************
	*                      END ELEMENT                       *
	*                           -                            *
	* Função chamada quando o parser encontra um tag fechado *
	*********************************************************/
	
	function endElement($parser, $name)
	{
		global $depth;
		global $openedtag;
		global $out;
		global $openText;
		global $readText;
		global $edgeWord;
		global $textTotal;
		global $openNomeSimbolo;
		global $edges;
		global $edgeDestID;
		global $debug;

		if ($name == "NOME_SIMBOLO") {
			
			// Indica que o cursor não está mais dentro do tag NOME_SIMBOLO
			$openNomeSimbolo = 0;

		}

		// Se o tag fechado é o tag SENTENCA e que texto foi lido (ou seja, encontrou pelo menos um tag TEXTO)...
		if (($name == "SENTENCA") && ($openText == 1)) {

			// Indica que o cursor não está mais dentro de um tag TEXTO
			$openText = 0;

			// Escrever o texto inteiro que foi lido (a acumulação de todos os TEXTO dentro do SENTENCA)
			output( $textTotal );

			// Fechar o tag TEXTO
			output( "</TEXTO>" );

			// Se referências foram encontradas, escrevê-las
			if ($edges) {
				foreach( $edges as $word => $id ) {
					newLine();
					output( "<REFERENCIA TO=\"$id\">$word</REFERENCIA>" );
				}

				// Reinicia a tabela de referências
				unset($GLOBALS['edges']);
			}
		}
		
		// Se o tag fechado é o tag TEXTO...
		if ($name == "TEXTO") {
			
			// Indica que não precisa ler os caracteres fora dos tags
			$readText = 0;

			// Se o tag TEXTO tinha uma referência, adicioná-la à lista das referências do nó
			if ($edgeDestID) {
				$edges[$edgeWord] = $edgeDestID;
				unset($GLOBALS['edgeDestID']);
			}
		}  
		else if ($name == "PT") {
			// Fazer nada. Para se livrar deste tag.
		} 
		else {
		    	$depth--;
			newLine();
			output( "</$name>" );
			$openedtag = 0;
		}
	}


	/************************************************************
	*                        CHARACTER DATA                     *
	*                               -                           *
	* Função chamada quando texto fora dos tags está sendo lido *
	*************************************************************/

	function characterData($parser, $data)
	{
		global $out;
		global $openText;
		global $edgeWord;
		global $textTotal;
		global $readText;
		global $debug;

		// Se o tag TEXTO foi aberto e que precisa ler o texto...
		if ($openText && $readText) {

			// Guardar em memória a palavra (caso seja uma referência)
			$edgeWord = $data;

			// Adicionar ao texto total o texto lido
			$textTotal .= $data;

		} 
		else {
			// No caso geral, simplesmente reescrever o que foi lido
			output( $data );
		}
	}

	function newLine() {
		global $debug;
		if ($debug) {
			global $out;
			global $depth;
			output( "\n" );
			output( str_repeat("  ",$depth) );
		}
	}

	function output( $text ) {
		global $debug;
		global $out;
		if ($debug) {
			echo $text;
		} else {
			fwrite( $out , $text );
		}
	}

	/************************************************************
	*                      EXECUCAO DO PARSER                   *
	*************************************************************/
	
	// Cria o parser
	$xml_parser = xml_parser_create();

	// Define as funções a chamar quando um tag abre e um tag fecha
	xml_set_element_handler($xml_parser, "startElement", "endElement");

	// Define a função a chamar quando texto fora dos tags está encontrado
	xml_set_character_data_handler($xml_parser, "characterData");

	// Abrir o arquivo de entrada
	if (!($in = fopen($filein, "r"))) {
	    	die("Unable to open the XML input file");
	}

	// Se o arquivo de destinação já existe, apagá-lo
	if (file_exists($fileout)) {
		unlink($fileout) or die("Unable to delete the old XML result file");
	}

	// Criar o arquivo de destinação
	if (!($out = fopen($fileout, "w"))) {
	    	die("Unable to open the XML result file");
	}

	// Escrever a versão e o charset no início do arquivo
	output( "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>" );
	
	// Percorre o arquivo XML 4kB por 4kB.
	while ($data = fread($in, 4096)) {
	    	if (!xml_parse($xml_parser, $data, feof($in))) {
	        	die(sprintf("error XML : %s on line %d",
	            	xml_error_string(xml_get_error_code($xml_parser)),
	                  xml_get_current_line_number($xml_parser)));
	    	}
	}

	// Liberta o objeto parser
	xml_parser_free($xml_parser);

	// Redireciona a página para o arquivo contendo o Applet Java.
	if (!$debug) {
		header('Location: grafo.php?xmlSource='.$fileout);
	}
?> 