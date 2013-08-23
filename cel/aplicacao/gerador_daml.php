<?php   


include("daml.php");   
include("auxiliar_bd.php");   
include_once("bd.inc");

session_start();   


$link = bd_connect();  

if ( $_POST['user'] == "")  
{  
// Recupera nome do usuário 
   $sql_user = "select nome from usuario where id_usuario='". $_SESSION['id_usuario_corrente'] . "';";  
   $query_user = mysql_query($sql_user) or die("Erro ao verificar usuário!". mysql_error());    
   $result = mysql_fetch_array($query_user);   
   $usuario = $result[0];  
}  
else  
{  
   $usuario =  $_POST['user'];  
}  

// Recupera nome do projeto 
   $sql_project = "select nome from projeto where id_projeto='". $_SESSION['id_projeto_corrente'] . "';";  
   $query_project = mysql_query($sql_project) or die("Erro ao verificar usuário!". mysql_error());    
   $result = mysql_fetch_array($query_project);   
   $project = $result[0];  

$site = $_SESSION['site'];  
$dir = $_SESSION['diretorio'];   
$arquivo = strtr($project, "ãäåöõÕ", "aaaooo") . "__" . date("j-m-Y_H-i-s") . ".daml";  
      
$i = array ("title" => $_POST['title'] ,       
                 "creator" => $usuario ,       
                 "description" => $_POST['description'] ,  
                 "subject" => $_POST['subject'] ,         
                 "versionInfo" => $_POST['versionInfo'] ) ;       

$_SESSION['id_projeto'] = $_SESSION['id_projeto_corrente'] ;
$lista_conceitos = get_lista_de_conceitos();   
$lista_relacoes = get_lista_de_relacoes();   
$lista_axiomas = get_lista_de_axiomas();  

$daml = salva_daml( $site, $dir, $arquivo, $i, $lista_conceitos , $lista_relacoes , $lista_axiomas );      

mysql_close($link);  

?>   

<html> 
<head><title>Gerar DAML</title></head> 
<body bgcolor="#FFFFFF"> 

<?php  
if ( !$daml )  
{  
    print 'Erro ao exportar ontologia para DAML!';       

} else {  

    print 'Ontologia exportada para DAML com sucesso! <br>';       
    print 'Arquivo criado: ';       
    print "<a href=\"$site$daml\">$daml</a>";       


}  
?>  

</body> 
</html> 