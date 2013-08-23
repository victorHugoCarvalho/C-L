<?php

include 'daml.php';
//include 'auxiliar_daml.php';
include 'auxiliar_bd.php';
include_once("bd.inc");
include_once("CELConfig/CELConfig.inc");

$link = bd_connect();

$site = "http://" . CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . CELConfig_ReadVar("DAML_dir_relativo_ao_CEL") ;
$dir =  CELConfig_ReadVar( "DAML_dir_relativo_ao_CEL" ) ;
$arquivo = nome_arquivo_daml();

$i = array ("title" => "Ontologia de teste" ,     
                 "creator" => "Pedro" ,     
                 "description" => "teste de tradução de léxico para ontologia" ,     
                 "subject" => "" ,       
                 "versionInfo" => "1.1" ) ;     

$lista_conceitos = get_lista_de_conceitos();
$lista_relacoes  = get_lista_de_relacoes();
$lista_axiomas   = get_lista_de_axiomas();


$daml = salva_daml( $site, $dir, $arquivo, $i, $lista_conceitos , $lista_relacoes , $lista_axiomas );    

if (!$daml)     
{     
    print 'Erro ao exportar ontologia para DAML!';     
}     
else     
{     
    print 'Ontologia exportada para DAML com sucesso! <br>';     
    print 'Arquivo criado: ';     
    print "<a href=\"$site$daml\">$daml</a>";     
}     


mysql_close($link);

?>