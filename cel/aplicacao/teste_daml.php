<?php

include 'daml.php';
include 'auxiliar_bd.php';
include_once("bd.inc");
include_once("CELConfig/CELConfig.inc");

$connected_SGBD = bd_connect()or die("Erro ao conectar ao SGBD");

$url = "http://" . CELConfig_ReadVar("HTTPD_ip") . "/" . CELConfig_ReadVar("CEL_dir_relativo") . CELConfig_ReadVar("DAML_dir_relativo_ao_CEL") ;
$dir =  CELConfig_ReadVar( "DAML_dir_relativo_ao_CEL" ) ;
$file = nome_arquivo_daml();

$array_info = array ("title" => "Ontologia de teste" ,     
            "creator" => "Pedro" ,     
            "description" => "teste de tradu&ccedil;&atilde;o de l&eacute;xico para ontologia" ,     
            "subject" => "" ,       
            "versionInfo" => "1.1" ) ;     

$concepts_list = get_lista_de_conceitos();
$relationships_list  = get_lista_de_relacoes();
$axioms_list   = get_lista_de_axiomas();


$daml = salva_daml( $url, $dir, $file , $array_info , $concepts_list , $relationships_list , $axioms_list );    

if (!$daml)     
{     
    print 'Erro ao exportar ontologia para DAML!';     
}     
else     
{     
    print 'Ontologia exportada para DAML com sucesso! <br>';     
    print 'Arquivo criado: ';     
    print "<a href=\"$url$daml\">$daml</a>";     
}     


mysql_close($connected_SGBD);

?>