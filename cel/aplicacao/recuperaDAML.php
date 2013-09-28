<HTML>
<HEAD>
<LINK rel="stylesheet" type="text/css" href="style.css">
<TITLE>Recupera&ccedil;&atilde;o de Arquivos DAML</TITLE>
</HEAD>

<BODY>
<H2>Hist&oacute;rico de Arquivos DAML</H2>
<?PHP 

    include_once( "CELConfig/CELConfig.inc" ) ;
    /* 
        Arquivo   : recuperaDAML.php 
        Vers�o       : 1.0 
        Coment�rio: Este programa lista todos os arquivos DAML    gerados    em $_SESSION['diretorio'] 
    */ 
     
    function extrair_data( $nome_arquivo ) 
    { 
        list($projeto, $resto) = split("__", $nome_arquivo);
        list($dia, $mes, $ano, $hora, $minuto, $segundo, $extensao) = split('[_-.]', $resto); 
         
        if( !is_numeric($dia) || !is_numeric($mes) ||
        	!is_numeric($ano) || !is_numeric($hora) ||
        	!is_numeric($minuto) || !is_numeric($segundo))
        {
            return "-";
        }
         
        $mes_por_extenso = "-"; 
        
        switch( $mes ) 
        { 
            case 1: $mes_por_extenso = "janeiro"; break; 
            case 2: $mes_por_extenso = "fevereiro"; break; 
            case 3: $mes_por_extenso = "mar�o"; break; 
            case 4: $mes_por_extenso = "abril"; break; 
            case 5: $mes_por_extenso = "maio"; break; 
            case 6: $mes_por_extenso = "junho"; break; 
            case 7: $mes_por_extenso = "julho"; break; 
            case 8: $mes_por_extenso = "agosto"; break; 
            case 9: $mes_por_extenso = "setembro"; break; 
            case 10: $mes_por_extenso = "outubro"; break; 
            case 11: $mes_por_extenso = "novembro"; break; 
            case 12: $mes_por_extenso = "dezembro"; break; 
        }         
         
        return $dia . " de " . $mes_por_extenso . " de " . $ano . " &agrave;s " . $hora . ":" . $minuto . "." . $segundo . "\n"; 
    } 
     
    function extrair_projeto( $nome_arquivo ) 
    { 
        list($projeto) = split("__", $nome_arquivo); 
        return $projeto; 
    }     

    $diretorio = $_SESSION['diretorio']; 
    $site = $_SESSION['site']; 
     
    if ( $diretorio == "" )
    {
    //    $diretorio = "teste"; 
          $diretorio = CELConfig_ReadVar("DAML_dir_relativo_ao_CEL") ;
    }

    if ( $site == "" ) 
    {
    //    $site = "http://139.82.24.189/cel_vf/aplicacao/teste/";
          $site = "http://" . CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . 
         		   CELConfig_ReadVar("DAML_dir_relativo_ao_CEL") ;
          
          if ( $site == "http:///" )
          {
             print( "Aten&ccedil;&atilde;o: O arquivo de configura&ccedil;&atilde;o do CELConfig (padr&atilde;o: config2.conf) precisa ser configurado
             		corretamente.<BR>\n * N&atilde;o foram preenchidas as vari&aacute;veis 'HTTPD_ip','CEL_dir_relativo' 
             		e 'DAML_dir_relativo_ao_CEL'.<BR>\nPor favor, verifique o arquivo e tente novamente.<BR>\n" );
          }
    }
     
    /* Monta a tabela    de arquivos    DAML */ 
    print( "<CENTER><TABLE WIDTH=\"80%\">\n") ; 
    print( "<TR>\n\t<Th><STRONG>Projeto</STRONG></Th>\n\t<Th><STRONG>Gerado em</STRONG></Th>\n</TR>\n" ); 
    if ($dir_handle = @opendir( $diretorio )) 
    { 
        while (($arquivo = readdir($dir_handle)))
        { 
            if ( is_file( $diretorio . "/" . $arquivo ) && $arquivo != "." && $arquivo != ".." ) 
            { 
                print( "<TR>\n" ); 
                print( "\t<TD WIDTH=\"25%\" CLASS=\"Estilo\"><B>" . extrair_projeto( $arquivo ) . "</B></TD>\n" ); 
                print( "\t<TD WIDTH=\"55%\" CLASS=\"Estilo\">" . extrair_data( $arquivo ) . "</TD>\n" ); 
                print( "\t<TD WIDTH=\"10%\" >[<A HREF=\"" . $site . $arquivo . "\">Abrir</A>]</TD>\n" ); 
                print( "</TR>\n" ); 
            } 
        } 
        closedir( $dir_handle ); 
    } 
    print("</TABLE></CENTER>\n") ; 
?>
</BODY>
</HTML>
